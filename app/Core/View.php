<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $viewFile = view_path(str_replace('.', '/', $view) . '.php');
        if (!is_file($viewFile)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = view_path(str_replace('.', '/', $layout) . '.php');
        if (!is_file($layoutFile)) {
            throw new \RuntimeException("Layout [{$layout}] not found.");
        }

        require $layoutFile;
    }

    public static function partial(string $partial, array $data = []): void
    {
        $file = view_path(str_replace('.', '/', $partial) . '.php');
        if (!is_file($file)) {
            return;
        }

        extract($data, EXTR_SKIP);
        require $file;
    }
}
