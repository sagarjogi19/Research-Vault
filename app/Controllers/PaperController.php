<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PaperService;
use App\Services\PaymentService;

final class PaperController extends Controller
{
    public function __construct(
        private readonly PaperService $papers = new PaperService(),
        private readonly PaymentService $payments = new PaymentService()
    ) {
    }

    public function index(): void
    {
        $filters = [
            'q' => trim((string) input('q', '')),
            'category_id' => input('category_id', ''),
            'is_free' => input('is_free', ''),
            'year' => input('year', ''),
        ];

        $page = max(1, (int) input('page', 1));
        $result = $this->papers->listPublished($filters, $page);

        $this->view('papers/index', [
            'title' => 'Browse Research Papers',
            'papers' => $result['items'],
            'pagination' => $result,
            'filters' => $filters,
            'categories' => $this->papers->categories(),
            'years' => $this->papers->years(),
        ]);
    }

    public function show(string $slug): void
    {
        $paper = $this->papers->findPublishedBySlug($slug);
        if ($paper === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Paper Not Found']);
            return;
        }

        $user = auth_user();
        $canAccess = false;
        if ($user) {
            $canAccess = is_admin() || $this->payments->userCanAccess((int) $user['id'], $paper);
        } elseif ((int) $paper['is_free'] === 1) {
            $canAccess = false; // still need login to download
        }

        $this->view('papers/show', [
            'title' => $paper['title'],
            'paper' => $paper,
            'canAccess' => $canAccess,
            'isOwned' => $user ? $this->payments->userCanAccess((int) $user['id'], $paper) : false,
        ]);
    }
}
