<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DashboardService;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard = new DashboardService()
    ) {
    }

    public function index(): void
    {
        $user = $this->requireAuth();
        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        }

        $stats = $this->dashboard->studentStats((int) $user['id']);

        $this->view('student/dashboard', [
            'title' => 'Student Dashboard',
            'stats' => $stats,
        ]);
    }
}
