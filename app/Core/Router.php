<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<string, array{handler: callable|array, middleware: array}>> */
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, callable|array $handler, array $middleware): self
    {
        $this->routes[$method][$this->normalize($path)] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        $basePath = request_base_path();
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        $path = $this->normalize($path);
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $route => $config) {
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $params = array_filter(
                $matches,
                static fn($key): bool => !is_int($key),
                ARRAY_FILTER_USE_KEY
            );

            foreach ($config['middleware'] as $middlewareClass) {
                $middleware = new $middlewareClass();
                $middleware->handle();
            }

            $handler = $config['handler'];
            if (is_array($handler)) {
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->{$action}(...array_values($params));
                return;
            }

            $handler(...array_values($params));
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Page Not Found']);
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
