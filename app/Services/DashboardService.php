<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DownloadRepository;
use App\Repositories\PaperRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PurchaseRepository;
use App\Repositories\UserRepository;

final class DashboardService
{
    public function __construct(
        private readonly UserRepository $users = new UserRepository(),
        private readonly PaperRepository $papers = new PaperRepository(),
        private readonly PurchaseRepository $purchases = new PurchaseRepository(),
        private readonly PaymentRepository $payments = new PaymentRepository(),
        private readonly DownloadRepository $downloads = new DownloadRepository()
    ) {
    }

    public function adminStats(): array
    {
        return [
            'students' => $this->users->countByRole('student'),
            'papers' => $this->papers->countAll(),
            'published_papers' => $this->papers->countAll('published'),
            'purchases' => $this->purchases->countCompleted(),
            'revenue' => $this->purchases->revenueTotal(),
            'downloads' => $this->downloads->countAll(),
            'recent_students' => $this->users->recentStudents(5),
            'recent_payments' => $this->payments->recent(8),
            'recent_papers' => $this->papers->recent(5),
        ];
    }

    public function studentStats(int $userId): array
    {
        return [
            'purchases' => $this->purchases->forUser($userId),
            'downloads' => $this->downloads->forUser($userId, 10),
            'download_count' => $this->downloads->countForUser($userId),
            'purchase_count' => count($this->purchases->forUser($userId)),
        ];
    }
}
