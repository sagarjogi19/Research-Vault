<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\DashboardService;

final class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard = new DashboardService()
    ) {
    }

    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => $this->dashboard->adminStats(),
        ], 'layouts/admin');
    }
}
