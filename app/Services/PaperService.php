<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Validator;
use App\Repositories\CategoryRepository;
use App\Repositories\PaperRepository;

final class PaperService
{
    public function __construct(
        private readonly PaperRepository $papers = new PaperRepository(),
        private readonly CategoryRepository $categories = new CategoryRepository(),
        private readonly FileUploadService $uploader = new FileUploadService()
    ) {
    }

    public function listPublished(array $filters, int $page = 1): array
    {
        $filters['status'] = 'published';
        return $this->papers->search($filters, $page);
    }

    public function listAll(array $filters, int $page = 1): array
    {
        return $this->papers->search($filters, $page);
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $paper = $this->papers->findBySlug($slug);
        if ($paper === null || $paper['status'] !== 'published') {
            return null;
        }

        return $paper;
    }

    public function findById(int $id): ?array
    {
        return $this->papers->findById($id);
    }

    public function create(array $input, array $file, int $uploaderId): array
    {
        $validation = $this->validatePaperInput($input);
        if (!$validation['success']) {
            return $validation;
        }

        $upload = $this->uploader->uploadPaper($file);
        if (!$upload['success']) {
            return $upload;
        }

        $slug = $this->uniqueSlug(slugify($input['title']));
        $isFree = (int) ($input['is_free'] ?? 1) === 1;

        $id = $this->papers->create([
            'title' => trim($input['title']),
            'slug' => $slug,
            'abstract' => trim($input['abstract']),
            'authors' => trim($input['authors']),
            'category_id' => (int) $input['category_id'],
            'uploaded_by' => $uploaderId,
            'file_path' => $upload['path'],
            'file_name' => $upload['original_name'],
            'file_size' => $upload['size'],
            'mime_type' => $upload['mime'],
            'preview_path' => null,
            'is_free' => $isFree ? 1 : 0,
            'price' => $isFree ? 0 : (float) $input['price'],
            'publication_year' => !empty($input['publication_year']) ? (int) $input['publication_year'] : null,
            'keywords' => trim((string) ($input['keywords'] ?? '')),
            'status' => $input['status'] ?? 'published',
        ]);

        return ['success' => true, 'id' => $id];
    }

    public function update(int $id, array $input, ?array $file = null): array
    {
        $paper = $this->papers->findById($id);
        if ($paper === null) {
            return ['success' => false, 'errors' => ['title' => 'Paper not found.']];
        }

        $validation = $this->validatePaperInput($input);
        if (!$validation['success']) {
            return $validation;
        }

        $isFree = (int) ($input['is_free'] ?? 1) === 1;
        $data = [
            'title' => trim($input['title']),
            'abstract' => trim($input['abstract']),
            'authors' => trim($input['authors']),
            'category_id' => (int) $input['category_id'],
            'is_free' => $isFree ? 1 : 0,
            'price' => $isFree ? 0 : (float) $input['price'],
            'publication_year' => !empty($input['publication_year']) ? (int) $input['publication_year'] : null,
            'keywords' => trim((string) ($input['keywords'] ?? '')),
            'status' => $input['status'] ?? $paper['status'],
        ];

        if ($paper['title'] !== $data['title']) {
            $data['slug'] = $this->uniqueSlug(slugify($data['title']), $id);
        }

        if ($file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->uploader->uploadPaper($file);
            if (!$upload['success']) {
                return $upload;
            }

            $this->uploader->deleteIfExists($paper['file_path']);
            $data['file_path'] = $upload['path'];
            $data['file_name'] = $upload['original_name'];
            $data['file_size'] = $upload['size'];
            $data['mime_type'] = $upload['mime'];
        }

        $this->papers->update($id, $data);
        return ['success' => true, 'id' => $id];
    }

    public function delete(int $id): array
    {
        $paper = $this->papers->findById($id);
        if ($paper === null) {
            return ['success' => false, 'message' => 'Paper not found.'];
        }

        $this->uploader->deleteIfExists($paper['file_path']);
        $this->papers->delete($id);

        return ['success' => true];
    }

    public function categories(): array
    {
        return $this->categories->all();
    }

    public function categoriesWithCounts(): array
    {
        return $this->categories->withPaperCounts();
    }

    public function years(): array
    {
        return $this->papers->years();
    }

    public function recent(int $limit = 6): array
    {
        return $this->papers->recent($limit);
    }

    public function popular(int $limit = 6): array
    {
        return $this->papers->popular($limit);
    }

    private function validatePaperInput(array $input): array
    {
        $validator = Validator::make($input)
            ->required('title')
            ->max('title', 255)
            ->required('abstract', 'Abstract')
            ->required('authors')
            ->max('authors', 255)
            ->required('category_id', 'Category')
            ->numeric('category_id', 'Category')
            ->required('is_free', 'Access type')
            ->in('is_free', ['0', '1', 0, 1], 'Access type')
            ->in('status', ['published', 'draft', 'archived'], 'Status');

        if ((string) ($input['is_free'] ?? '1') === '0') {
            $validator->required('price')->numeric('price');
            if (isset($input['price']) && (float) $input['price'] <= 0) {
                return ['success' => false, 'errors' => ['price' => 'Price must be greater than zero for premium papers.']];
            }
        }

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->firstErrors()];
        }

        if ($this->categories->findById((int) $input['category_id']) === null) {
            return ['success' => false, 'errors' => ['category_id' => 'Selected category is invalid.']];
        }

        return ['success' => true];
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = $base;
        $i = 1;
        while ($this->papers->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
