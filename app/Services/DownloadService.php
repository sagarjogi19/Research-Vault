<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DownloadRepository;
use App\Repositories\PaperRepository;

final class DownloadService
{
    public function __construct(
        private readonly PaperRepository $papers = new PaperRepository(),
        private readonly DownloadRepository $downloads = new DownloadRepository(),
        private readonly PaymentService $payments = new PaymentService(),
        private readonly FileUploadService $files = new FileUploadService()
    ) {
    }

    public function authorizeAndServe(int $userId, int $paperId, ?string $ip = null, bool $isAdmin = false): array
    {
        $paper = $this->papers->findById($paperId);
        if ($paper === null || $paper['status'] !== 'published') {
            return ['success' => false, 'message' => 'Paper not found.', 'status' => 404];
        }

        if (!$isAdmin && !$this->payments->userCanAccess($userId, $paper)) {
            return ['success' => false, 'message' => 'Purchase this paper to unlock the download.', 'status' => 403];
        }

        $absolute = $this->files->absolutePath($paper['file_path']);
        if (!is_file($absolute)) {
            return ['success' => false, 'message' => 'The document file is missing.', 'status' => 404];
        }

        $this->downloads->create([
            'user_id' => $userId,
            'paper_id' => $paperId,
            'ip_address' => $ip,
        ]);
        $this->papers->incrementDownloads($paperId);

        return [
            'success' => true,
            'path' => $absolute,
            'name' => $paper['file_name'],
            'mime' => $paper['mime_type'] ?: 'application/octet-stream',
        ];
    }

    public function historyForUser(int $userId): array
    {
        return $this->downloads->forUser($userId);
    }
}
