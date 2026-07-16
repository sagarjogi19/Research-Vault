<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PaperService;

final class HomeController extends Controller
{
    public function __construct(
        private readonly PaperService $papers = new PaperService()
    ) {
    }

    public function index(): void
    {
        $this->view('home', [
            'title' => 'Research Repository & Digital Library',
            'recentPapers' => $this->papers->recent(6),
            'popularPapers' => $this->papers->popular(4),
            'categories' => $this->papers->categoriesWithCounts(),
        ]);
    }
}
