<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\DownloadService;

final class DownloadController extends Controller
{
    public function __construct(
        private readonly DownloadService $downloads = new DownloadService()
    ) {
    }

    public function download(string $id): void
    {
        $user = $this->requireAuth();
        $result = $this->downloads->authorizeAndServe(
            (int) $user['id'],
            (int) $id,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $user['role'] === 'admin'
        );

        if (!$result['success']) {
            http_response_code($result['status'] ?? 403);
            flash('error', $result['message']);
            $this->back('/papers');
        }

        $path = $result['path'];
        $name = $result['name'];
        $mime = $result['mime'];

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . rawurlencode($name) . '"');
        header('Content-Length: ' . filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        readfile($path);
        exit;
    }
}
