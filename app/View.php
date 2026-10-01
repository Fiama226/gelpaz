<?php
declare(strict_types=1);

namespace App;

use RuntimeException;
use Throwable;

/**
 * Moteur de gabarits minimaliste (PHP natif).
 * Les variables définies dans un gabarit de page (ex. $pageTitle) sont transmises à la mise en page.
 */
final class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        [$content, $vars] = self::capture($template, $data + self::$shared);
        if ($layout === null) {
            return $content;
        }
        $vars['content'] = $content;
        [$html] = self::capture($layout, $vars);
        return $html;
    }

    public static function partial(string $name, array $data = []): string
    {
        [$html] = self::capture('partials/' . $name, $data + self::$shared);
        return $html;
    }

    private static function capture(string $__template, array $__data): array
    {
        $__file = ROOT . '/templates/' . $__template . '.php';
        if (!is_file($__file)) {
            throw new RuntimeException('Gabarit introuvable : ' . $__template);
        }
        extract($__data, EXTR_SKIP);
        ob_start();
        try {
            include $__file;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        $__out = (string) ob_get_clean();
        $__vars = get_defined_vars();
        unset($__vars['__file'], $__vars['__template'], $__vars['__data'], $__vars['__out'], $__vars['e']);
        return [$__out, $__vars];
    }
}
