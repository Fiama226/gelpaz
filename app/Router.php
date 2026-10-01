<?php
declare(strict_types=1);

namespace App;

/**
 * Routeur HTTP minimaliste : motifs du type /logements/{slug}.
 */
final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): self
    {
        return $this->add(['GET', 'HEAD'], $pattern, $handler);
    }

    public function post(string $pattern, callable|array $handler): self
    {
        return $this->add(['POST'], $pattern, $handler);
    }

    public function any(string $pattern, callable|array $handler): self
    {
        return $this->add(['GET', 'HEAD', 'POST'], $pattern, $handler);
    }

    public function add(array $methods, string $pattern, callable|array $handler): self
    {
        $regex = preg_replace_callback('#\{([a-z_]+)(?::([^}]+))?\}#', static function ($m) {
            return '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')';
        }, rtrim($pattern, '/') ?: '/');
        $this->routes[] = [$methods, '#^' . $regex . '$#u', $handler];
        return $this;
    }

    /** Retourne true si une route a été trouvée et exécutée. */
    public function dispatch(string $method, string $path): bool
    {
        $path = rtrim($path, '/') ?: '/';
        $allowed = [];
        foreach ($this->routes as [$methods, $regex, $handler]) {
            if (!preg_match($regex, $path, $m)) {
                continue;
            }
            if (!in_array($method, $methods, true)) {
                $allowed = array_merge($allowed, $methods);
                continue;
            }
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            if (is_array($handler) && is_string($handler[0])) {
                $handler = [new $handler[0](), $handler[1]];
            }
            $result = $handler(...array_values($params));
            if (is_string($result)) {
                echo $result;
            }
            return true;
        }
        if ($allowed) {
            http_response_code(405);
            header('Allow: ' . implode(', ', array_unique($allowed)));
            echo 'Méthode non autorisée';
            return true;
        }
        return false;
    }
}
