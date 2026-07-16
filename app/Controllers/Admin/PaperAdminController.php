<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\PaperService;

final class PaperAdminController extends Controller
{
    public function __construct(
        private readonly PaperService $papers = new PaperService()
    ) {
    }

    public function index(): void
    {
        $filters = [
            'q' => trim((string) input('q', '')),
            'category_id' => input('category_id', ''),
            'status' => input('status', ''),
            'is_free' => input('is_free', ''),
        ];
        $page = max(1, (int) input('page', 1));
        $result = $this->papers->listAll($filters, $page);

        $this->view('admin/papers/index', [
            'title' => 'Manage Papers',
            'papers' => $result['items'],
            'pagination' => $result,
            'filters' => $filters,
            'categories' => $this->papers->categories(),
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $this->view('admin/papers/form', [
            'title' => 'Upload Research Paper',
            'paper' => null,
            'categories' => $this->papers->categories(),
            'errors' => flash('errors') ?? [],
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = $this->requireAuth();
        $input = $this->paperInput();

        $result = $this->papers->create($input, $_FILES['file'] ?? [], (int) $user['id']);
        if (!$result['success']) {
            $this->withOld($input);
            flash('errors', $result['errors'] ?? ['file' => $result['message'] ?? 'Upload failed.']);
            flash('error', 'Could not save the paper. Please review the form.');
            $this->redirect('/admin/papers/create');
        }

        $this->clearOld();
        flash('success', 'Research paper uploaded successfully.');
        $this->redirect('/admin/papers');
    }

    public function edit(string $id): void
    {
        $paper = $this->papers->findById((int) $id);
        if ($paper === null) {
            flash('error', 'Paper not found.');
            $this->redirect('/admin/papers');
        }

        $this->view('admin/papers/form', [
            'title' => 'Edit Research Paper',
            'paper' => $paper,
            'categories' => $this->papers->categories(),
            'errors' => flash('errors') ?? [],
        ], 'layouts/admin');
    }

    public function update(string $id): void
    {
        $this->validateCsrf();
        $input = $this->paperInput();
        $file = $_FILES['file'] ?? null;

        $result = $this->papers->update((int) $id, $input, $file);
        if (!$result['success']) {
            $this->withOld($input);
            flash('errors', $result['errors'] ?? []);
            flash('error', 'Could not update the paper.');
            $this->redirect('/admin/papers/' . $id . '/edit');
        }

        $this->clearOld();
        flash('success', 'Paper updated successfully.');
        $this->redirect('/admin/papers');
    }

    public function destroy(string $id): void
    {
        $this->validateCsrf();
        $result = $this->papers->delete((int) $id);
        flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Paper deleted.' : ($result['message'] ?? 'Delete failed.'));
        $this->redirect('/admin/papers');
    }

    private function paperInput(): array
    {
        return [
            'title' => trim((string) input('title', '')),
            'abstract' => trim((string) input('abstract', '')),
            'authors' => trim((string) input('authors', '')),
            'category_id' => input('category_id', ''),
            'is_free' => input('is_free', '1'),
            'price' => input('price', '0'),
            'publication_year' => input('publication_year', ''),
            'keywords' => trim((string) input('keywords', '')),
            'status' => input('status', 'published'),
        ];
    }
}
