<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\PaperRepository;
use App\Repositories\PurchaseRepository;
use App\Services\PaymentService;

final class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $payments = new PaymentService(),
        private readonly PurchaseRepository $purchases = new PurchaseRepository(),
        private readonly PaperRepository $papers = new PaperRepository()
    ) {
    }

    public function checkout(string $id): void
    {
        $this->validateCsrf();
        $user = $this->requireAuth();

        if ($user['role'] !== 'student' && $user['role'] !== 'admin') {
            flash('error', 'Only students can purchase papers.');
            $this->redirect('/papers');
        }

        $result = $this->payments->initializePurchase((int) $user['id'], (int) $id, $user['email']);
        if (!$result['success']) {
            flash('error', $result['message']);
            if (!empty($result['already_owned'])) {
                $paper = $this->papers->findById((int) $id);
                $this->redirect($paper ? '/papers/' . $paper['slug'] : '/library');
            }
            $this->back('/papers');
        }

        redirect($result['authorization_url']);
    }

    public function callback(): void
    {
        $user = $this->requireAuth();
        $reference = (string) input('reference', input('trxref', ''));

        if ($reference === '') {
            flash('error', 'Missing payment reference.');
            $this->redirect('/dashboard');
        }

        $result = $this->payments->verifyByReference($reference);
        if (!$result['success']) {
            flash('error', $result['message']);
            $this->redirect('/dashboard');
        }

        flash('success', 'Payment successful! You can now download your paper.');
        $this->redirect('/dashboard');
    }

    public function library(): void
    {
        $user = $this->requireAuth();
        $items = $this->purchases->forUser((int) $user['id']);

        $this->view('student/library', [
            'title' => 'My Library',
            'purchases' => $items,
        ]);
    }
}
