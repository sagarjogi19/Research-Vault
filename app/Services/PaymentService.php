<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Repositories\PaperRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PurchaseRepository;

final class PaymentService
{
    private Database $db;

    public function __construct(
        private readonly PaperRepository $papers = new PaperRepository(),
        private readonly PurchaseRepository $purchases = new PurchaseRepository(),
        private readonly PaymentRepository $payments = new PaymentRepository(),
        ?Database $db = null
    ) {
        $this->db = $db ?? Database::getInstance();
    }

    public function userCanAccess(int $userId, array $paper): bool
    {
        if ((int) $paper['is_free'] === 1) {
            return true;
        }

        return $this->purchases->hasCompletedPurchase($userId, (int) $paper['id']);
    }

    public function initializePurchase(int $userId, int $paperId, string $email): array
    {
        $paper = $this->papers->findById($paperId);
        if ($paper === null || $paper['status'] !== 'published') {
            return ['success' => false, 'message' => 'Paper not found.'];
        }

        if ((int) $paper['is_free'] === 1) {
            return ['success' => false, 'message' => 'This paper is free and does not require payment.'];
        }

        if ($this->purchases->hasCompletedPurchase($userId, $paperId)) {
            return ['success' => false, 'message' => 'You already own this paper.', 'already_owned' => true];
        }

        $secret = (string) config('paystack.secret_key');
        if ($secret === '' || str_contains($secret, 'xxxxxxxx')) {
            return ['success' => false, 'message' => 'Paystack is not configured. Add your API keys to the .env file.'];
        }

        $amount = (float) $paper['price'];
        $currency = (string) config('paystack.currency', 'NGN');
        $reference = 'RV_' . time() . '_' . bin2hex(random_bytes(4));

        try {
            $this->db->beginTransaction();

            $existing = $this->purchases->findByUserAndPaper($userId, $paperId);
            if ($existing && $existing['status'] === 'pending') {
                $purchaseId = (int) $existing['id'];
                $this->purchases->update($purchaseId, [
                    'amount' => $amount,
                    'currency' => $currency,
                ]);
            } elseif ($existing && $existing['status'] === 'completed') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'You already own this paper.', 'already_owned' => true];
            } else {
                $purchaseId = $this->purchases->create([
                    'user_id' => $userId,
                    'paper_id' => $paperId,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'pending',
                ]);
            }

            $payload = [
                'email' => $email,
                'amount' => (int) round($amount * 100),
                'currency' => $currency,
                'reference' => $reference,
                'callback_url' => config('paystack.callback_url'),
                'metadata' => [
                    'paper_id' => $paperId,
                    'user_id' => $userId,
                    'purchase_id' => $purchaseId,
                    'paper_title' => $paper['title'],
                ],
            ];

            $response = $this->paystackRequest('/transaction/initialize', $payload);
            if (!($response['status'] ?? false)) {
                $this->db->rollBack();
                return ['success' => false, 'message' => $response['message'] ?? 'Unable to initialize payment.'];
            }

            $this->payments->create([
                'purchase_id' => $purchaseId,
                'user_id' => $userId,
                'reference' => $reference,
                'access_code' => $response['data']['access_code'] ?? null,
                'amount' => $amount,
                'currency' => $currency,
                'gateway' => 'paystack',
                'gateway_response' => json_encode($response),
                'status' => 'initialized',
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'authorization_url' => $response['data']['authorization_url'],
                'reference' => $reference,
                'public_key' => config('paystack.public_key'),
                'amount' => $amount,
            ];
        } catch (\Throwable $e) {
            if ($this->db->pdo()->inTransaction()) {
                $this->db->rollBack();
            }

            return ['success' => false, 'message' => 'Payment initialization failed. Please try again.'];
        }
    }

    public function verifyByReference(string $reference): array
    {
        $payment = $this->payments->findByReference($reference);
        if ($payment === null) {
            return ['success' => false, 'message' => 'Payment reference not found.'];
        }

        if ($payment['status'] === 'success') {
            return ['success' => true, 'message' => 'Payment already verified.', 'payment' => $payment];
        }

        $response = $this->paystackRequest('/transaction/verify/' . rawurlencode($reference), null, 'GET');
        if (!($response['status'] ?? false)) {
            $this->payments->update((int) $payment['id'], [
                'status' => 'failed',
                'gateway_response' => json_encode($response),
            ]);

            return ['success' => false, 'message' => $response['message'] ?? 'Payment verification failed.'];
        }

        $data = $response['data'] ?? [];
        if (($data['status'] ?? '') !== 'success') {
            $this->payments->update((int) $payment['id'], [
                'status' => 'failed',
                'gateway_response' => json_encode($response),
            ]);

            return ['success' => false, 'message' => 'Payment was not successful.'];
        }

        $paidAmount = ((int) ($data['amount'] ?? 0)) / 100;
        if (abs($paidAmount - (float) $payment['amount']) > 0.01) {
            return ['success' => false, 'message' => 'Paid amount does not match expected amount.'];
        }

        try {
            $this->db->beginTransaction();

            $this->payments->update((int) $payment['id'], [
                'status' => 'success',
                'paid_at' => date('Y-m-d H:i:s'),
                'gateway_response' => json_encode($response),
            ]);

            $this->purchases->update((int) $payment['purchase_id'], [
                'status' => 'completed',
                'purchased_at' => date('Y-m-d H:i:s'),
            ]);

            $this->db->commit();

            $payment = $this->payments->findByReference($reference);

            return ['success' => true, 'message' => 'Payment verified successfully.', 'payment' => $payment];
        } catch (\Throwable $e) {
            if ($this->db->pdo()->inTransaction()) {
                $this->db->rollBack();
            }

            return ['success' => false, 'message' => 'Could not finalize payment.'];
        }
    }

    private function paystackRequest(string $endpoint, ?array $payload, string $method = 'POST'): array
    {
        $secret = (string) config('paystack.secret_key');
        $url = 'https://api.paystack.co' . $endpoint;

        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $secret,
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload ?? []));
        }

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            return ['status' => false, 'message' => 'Paystack connection error: ' . $error];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return ['status' => false, 'message' => 'Invalid response from Paystack.', 'http_code' => $code];
        }

        return $decoded;
    }
}
