<?php

declare(strict_types=1);

namespace App\Services;

final class FileUploadService
{
    private int $maxSize;
    /** @var list<string> */
    private array $allowedExtensions;
    /** @var array<string, string> */
    private array $allowedMimes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public function __construct()
    {
        $this->maxSize = (int) env('UPLOAD_MAX_SIZE', 10485760);
        $types = (string) env('ALLOWED_FILE_TYPES', 'pdf,doc,docx');
        $this->allowedExtensions = array_filter(array_map('trim', explode(',', strtolower($types))));
    }

    public function uploadPaper(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'errors' => ['file' => $this->uploadErrorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE))]];
        }

        if (($file['size'] ?? 0) > $this->maxSize) {
            return ['success' => false, 'errors' => ['file' => 'File exceeds the maximum allowed size of ' . format_bytes($this->maxSize) . '.']];
        }

        $original = (string) ($file['name'] ?? 'document');
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowedExtensions, true)) {
            return ['success' => false, 'errors' => ['file' => 'Only PDF, DOC, and DOCX files are allowed.']];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        $expected = $this->allowedMimes[$extension] ?? null;

        // Allow common mime variants for Office docs
        $validMimes = array_filter([
            $expected,
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip', // some docx reported as zip
            'application/octet-stream',
        ]);

        if (!in_array($mime, $validMimes, true)) {
            return ['success' => false, 'errors' => ['file' => 'Invalid file type detected.']];
        }

        $directory = storage_path('papers');
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return ['success' => false, 'errors' => ['file' => 'Unable to prepare upload directory.']];
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $directory . DIRECTORY_SEPARATOR . $storedName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'errors' => ['file' => 'Failed to store uploaded file.']];
        }

        return [
            'success' => true,
            'path' => 'papers/' . $storedName,
            'original_name' => $original,
            'size' => (int) $file['size'],
            'mime' => $mime,
        ];
    }

    public function absolutePath(string $relativePath): string
    {
        return storage_path(ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR));
    }

    public function deleteIfExists(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $path = $this->absolutePath($relativePath);
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded file is too large.',
            UPLOAD_ERR_PARTIAL => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'Please select a document to upload.',
            default => 'File upload failed. Please try again.',
        };
    }
}
