<?php
/**
 * GELPAZ IMMO — Fonctions utilitaires globales.
 */
declare(strict_types=1);

use App\Database;
use App\View;

/* ===================== Configuration & environnement ===================== */

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'] ?? [];
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function is_installed(): bool
{
    return (bool) config('installed');
}

function is_https(): bool
{
    if (IS_CLI) {
        return false;
    }
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 80) === 443
        || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/* ================================ URLs =================================== */

/** Chemin de base (gère une installation dans un sous-dossier). */
function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $configured = (string) config('app.url', '');
    if ($configured !== '') {
        $p = rtrim((string) parse_url($configured, PHP_URL_PATH), '/');
        return $base = $p;
    }
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    $dir = rtrim(dirname($script), '/.');
    // Le routeur du serveur de développement (server.php) est à la racine
    return $base = ($dir === '' || $dir === '/') ? '' : $dir;
}

function url(string $path = ''): string
{
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:') || str_starts_with($path, '#')) {
        return $path;
    }
    return base_path() . '/' . ltrim($path, '/');
}

function absolute_url(string $path = ''): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $configured = rtrim((string) config('app.url', ''), '/');
    if ($configured !== '') {
        $origin = preg_replace('#^(https?://[^/]+).*$#i', '$1', $configured);
    } else {
        $origin = (is_https() ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    }
    return $origin . url($path);
}

function asset(string $path): string
{
    $file = ROOT . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? substr((string) filemtime($file), -6) : APP_VERSION;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function current_path(): string
{
    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $base = base_path();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    $path = '/' . trim(rawurldecode($path), '/');
    return $path;
}

function is_active(string $path, bool $exact = false): bool
{
    $current = current_path();
    if ($path === '/') {
        return $current === '/';
    }
    return $exact ? $current === $path : ($current === $path || str_starts_with($current, rtrim($path, '/') . '/'));
}

/** Construit une URL en fusionnant des paramètres avec la requête courante. */
function query_url(array $params, ?string $path = null): string
{
    $query = array_merge($_GET, $params);
    $query = array_filter($query, static fn($v) => $v !== null && $v !== '' && $v !== []);
    unset($query['page_url']);
    $qs = http_build_query($query);
    return url($path ?? current_path()) . ($qs !== '' ? '?' . $qs : '');
}

function redirect(string $to, int $code = 302): never
{
    header('Location: ' . (preg_match('#^https?://#', $to) ? $to : url($to)), true, $code);
    exit;
}

/* ============================ Échappement / texte ========================= */

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function slugify(string $text): string
{
    $text = trim($text);
    if (function_exists('transliterator_transliterate')) {
        $t = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
        if (is_string($t)) {
            $text = $t;
        }
    } else {
        $map = ['à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a','å'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i','ñ'=>'n','ó'=>'o','ò'=>'o','ô'=>'o','ö'=>'o','õ'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ý'=>'y','ÿ'=>'y','œ'=>'oe','æ'=>'ae','’'=>'-',"'"=>'-'];
        $text = strtr(mb_strtolower($text), $map);
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', strtolower($text)) ?? '';
    return trim($text, '-') ?: 'element';
}

function str_limit(string $text, int $limit = 120, string $end = '…'): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    $cut = mb_substr($text, 0, $limit);
    $space = mb_strrpos($cut, ' ');
    return rtrim($space ? mb_substr($cut, 0, $space) : $cut, ' ,;:.') . $end;
}

function excerpt(?string $html, int $limit = 160): string
{
    $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return str_limit($text, $limit);
}

function plural(int $n, string $singular, ?string $pluralForm = null): string
{
    return $n . ' ' . ($n > 1 ? ($pluralForm ?? $singular . 's') : $singular);
}

const MONTHS_FR = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
const MONTHS_FR_SHORT = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];

/** Formate une date en français : long (9 septembre 2026), short (09/09/2026), day, month. */
function date_fr(?string $date, string $format = 'long'): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    $m = (int) date('n', $ts) - 1;
    return match ($format) {
        'short' => date('d/m/Y', $ts),
        'day' => date('d', $ts),
        'month' => MONTHS_FR_SHORT[$m],
        'month_year' => MONTHS_FR_SHORT[$m] . ' ' . date('Y', $ts),
        'datetime' => date('d/m/Y à H\hi', $ts),
        default => (int) date('j', $ts) . ' ' . MONTHS_FR[$m] . ' ' . date('Y', $ts),
    };
}

function time_ago(?string $date): string
{
    if (!$date) {
        return '';
    }
    $diff = time() - (int) strtotime($date);
    return match (true) {
        $diff < 60 => 'à l’instant',
        $diff < 3600 => 'il y a ' . plural((int) floor($diff / 60), 'minute'),
        $diff < 86400 => 'il y a ' . plural((int) floor($diff / 3600), 'heure'),
        $diff < 86400 * 30 => 'il y a ' . plural((int) floor($diff / 86400), 'jour'),
        default => 'le ' . date_fr($date, 'short'),
    };
}

function format_area(int|string|null $m2): string
{
    if ($m2 === null || $m2 === '' || (int) $m2 === 0) {
        return '';
    }
    return number_format((int) $m2, 0, ',', ' ') . ' m²';
}

function reading_time(?string $html): int
{
    $words = str_word_count(strip_tags((string) $html));
    return max(1, (int) ceil($words / 200));
}

/* ======================= Liens téléphone / WhatsApp ======================= */

function phone_href(string $phone): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

function whatsapp_url(string $message = ''): string
{
    $number = preg_replace('/\D/', '', (string) setting('whatsapp', '22667308185'));
    return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}

/* ================================ Médias ================================= */

/**
 * Retourne l'URL d'une image : fichier local s'il existe, sinon l'URL distante
 * (image originale de l'ancien site), sinon une image de repli.
 */
function media(?string $local, ?string $remote = null, ?string $fallback = 'assets/images/hero/hero-1.jpg'): string
{
    if ($local && is_file(ROOT . '/' . ltrim($local, '/'))) {
        return url($local);
    }
    if ($remote) {
        return $remote;
    }
    return $fallback ? url($fallback) : '';
}

/** Variante miniature (cartes, listes). */
function media_thumb(?string $local, ?string $remoteThumb = null, ?string $remote = null, ?string $fallback = 'assets/images/hero/hero-1.jpg'): string
{
    if ($local) {
        $thumb = preg_replace('#/([^/]+)$#', '/thumbs/$1', ltrim($local, '/'));
        if ($thumb && is_file(ROOT . '/' . $thumb)) {
            return url($thumb);
        }
        if (is_file(ROOT . '/' . ltrim($local, '/'))) {
            return url($local);
        }
    }
    return media(null, $remoteThumb ?: $remote, $fallback);
}

/* ================================ Icônes ================================= */

function icon(string $name, string $class = '', ?int $size = null): string
{
    static $icons = null;
    $icons ??= require ROOT . '/app/icons.php';
    $inner = $icons[$name] ?? $icons['circle'] ?? '';
    $sizeAttr = $size ? ' width="' . $size . '" height="' . $size . '"' : '';
    $cls = trim('icon icon-' . $name . ' ' . $class);
    if (str_starts_with($name, 'brand-')) {
        return '<svg class="' . e($cls) . '" viewBox="0 0 24 24"' . $sizeAttr . ' fill="currentColor" aria-hidden="true" focusable="false"><path d="' . $inner . '"/></svg>';
    }
    return '<svg class="' . e($cls) . '" viewBox="0 0 24 24"' . $sizeAttr . ' fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $inner . '</svg>';
}

/** Oiseau GELPAZ (élément graphique de la marque). */
function bird(string $class = ''): string
{
    return '<svg class="bird ' . e($class) . '" viewBox="474 220 998 175" aria-hidden="true" focusable="false"><path fill="currentColor" d="M484 265.5C520 250 590 233 650 231C760 229 900 285 984 320L1463 265.5L990 382C930 345 840 310 760 289C680 272 580 264 484 265.5Z"/></svg>';
}

/* ============================== Réglages ================================= */

function setting(string $key, mixed $default = ''): mixed
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        if (is_installed()) {
            try {
                foreach (Database::all('SELECT `name`, `value` FROM settings') as $row) {
                    $cache[$row['name']] = $row['value'];
                }
            } catch (Throwable) {
                $cache = [];
            }
        }
    }
    $value = $cache[$key] ?? null;
    return ($value === null || $value === '') ? $default : $value;
}

/* ====================== Session, flash, ancien input ====================== */

function flash(string $key, mixed $value = null): mixed
{
    if (func_num_args() === 2) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $v;
}

function old(string $key, string $default = ''): string
{
    return (string) ($_SESSION['_old'][$key] ?? $default);
}

function remember_input(array $input): void
{
    unset($input['_token'], $input['password'], $input['website'], $input['_ts']);
    $_SESSION['_old'] = $input;
}

function clear_old_input(): void
{
    unset($_SESSION['_old']);
}

/* ============================ Sécurité formulaires ======================== */

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $token = (string) ($_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return $token !== '' && hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token);
}

/** Champs anti-spam : pot de miel + horodatage signé. */
function antispam_fields(): string
{
    $ts = (string) time();
    $sig = hash_hmac('sha256', $ts, (string) config('app.key'));
    return '<div class="hp-field" aria-hidden="true"><label>Laissez ce champ vide<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>'
        . '<input type="hidden" name="_ts" value="' . $ts . '.' . $sig . '">';
}

/** Retourne null si OK, sinon un message d'erreur. */
function antispam_check(int $minSeconds = 3): ?string
{
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        return 'Votre message a été détecté comme indésirable.';
    }
    $raw = (string) ($_POST['_ts'] ?? '');
    [$ts, $sig] = array_pad(explode('.', $raw, 2), 2, '');
    if (!ctype_digit($ts) || !hash_equals(hash_hmac('sha256', $ts, (string) config('app.key')), $sig)) {
        return 'Le formulaire a expiré. Merci de recharger la page.';
    }
    $age = time() - (int) $ts;
    if ($age < $minSeconds) {
        return 'Envoi trop rapide : merci de vérifier votre saisie puis de réessayer.';
    }
    if ($age > 86400 * 2) {
        return 'Le formulaire a expiré. Merci de recharger la page.';
    }
    return null;
}

/** Limite de fréquence (true = autorisé). Enregistre la tentative. */
function rate_limit(string $key, int $max, int $seconds, bool $record = true): bool
{
    try {
        $now = time();
        Database::run('DELETE FROM rate_limits WHERE created_at < ?', [$now - 86400]);
        $count = (int) Database::value('SELECT COUNT(*) FROM rate_limits WHERE k = ? AND created_at > ?', [$key, $now - $seconds]);
        if ($count >= $max) {
            return false;
        }
        if ($record) {
            Database::insert('rate_limits', ['k' => $key, 'created_at' => $now]);
        }
    } catch (Throwable) {
        return true;
    }
    return true;
}

/* ============================== Réponses ================================= */

function is_ajax(): bool
{
    return strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest'
        || str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json');
}

function json_response(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function view(string $template, array $data = [], ?string $layout = 'layouts/main'): string
{
    return View::render($template, $data, $layout);
}

function partial(string $name, array $data = []): void
{
    echo View::partial($name, $data);
}

function abort(int $code = 404, string $message = ''): never
{
    http_response_code($code);
    if ($code === 404) {
        echo view('pages/404', ['pageTitle' => 'Page introuvable', 'message' => $message]);
    } else {
        echo e($message ?: 'Erreur ' . $code);
    }
    exit;
}

/* ============================ Nettoyage HTML ============================= */

/** Nettoie un contenu HTML riche (liste blanche de balises et d'attributs). */
function sanitize_html(?string $html): string
{
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }
    $allowed = [
        'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'h2' => [], 'h3' => [], 'h4' => [], 'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [],
        'a' => ['href', 'title', 'target', 'rel'], 'img' => ['src', 'alt', 'width', 'height', 'loading'],
        'figure' => [], 'figcaption' => [], 'hr' => [], 'span' => [], 'div' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => [], 'td' => [],
    ];
    if (!class_exists(DOMDocument::class)) {
        return strip_tags($html, '<' . implode('><', array_keys($allowed)) . '>');
    }
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $root = $doc->getElementById('__root');
    if (!$root) {
        return strip_tags($html);
    }
    $walk = static function (DOMNode $node) use (&$walk, $allowed, $doc): void {
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'meta', 'link'], true)) {
                    $node->removeChild($child);
                    continue;
                }
                $walk($child);
                if (!isset($allowed[$tag])) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attr) {
                    $name = strtolower($attr->name);
                    if (!in_array($name, $allowed[$tag], true)) {
                        $child->removeAttribute($attr->name);
                        continue;
                    }
                    if (in_array($name, ['href', 'src'], true)) {
                        $val = trim($attr->value);
                        if (!preg_match('#^(https?:|mailto:|tel:|/|\#|[a-z0-9_\-./]+$)#i', $val) || preg_match('#^\s*(javascript|data|vbscript):#i', $val)) {
                            $child->removeAttribute($attr->name);
                        }
                    }
                }
                if ($tag === 'a' && $child->getAttribute('target') === '_blank') {
                    $child->setAttribute('rel', 'noopener noreferrer');
                }
            } elseif ($child instanceof DOMComment) {
                $node->removeChild($child);
            }
        }
    };
    $walk($root);
    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    return trim($out);
}

/* ============================== Libellés ================================= */

function transaction_label(?string $t): string
{
    return match ($t) {
        'location' => 'À louer',
        default => 'À vendre',
    };
}

function property_status_label(?string $s): string
{
    return match ($s) {
        'reserve' => 'Réservé',
        'vendu' => 'Vendu',
        'loue' => 'Loué',
        'bientot' => 'Bientôt disponible',
        default => 'Disponible',
    };
}

function site_status_label(?string $s): string
{
    return match ($s) {
        'commercialisation' => 'En commercialisation',
        'livre' => 'Livré',
        'a_venir' => 'À venir',
        'international' => 'Projet international',
        'en_cours' => 'En cours de réalisation',
        default => 'Site GELPAZ IMMO',
    };
}

function subscription_status_label(?string $s): string
{
    return match ($s) {
        'en_cours' => 'En cours',
        'traite' => 'Traitée',
        'annule' => 'Annulée',
        default => 'Nouvelle',
    };
}

/* ============================= Pagination ================================ */

/** Calcule la pagination. Retourne [offset, pages[], current, total]. */
function paginate(int $total, int $perPage, int $page): array
{
    $totalPages = max(1, (int) ceil($total / max(1, $perPage)));
    $page = min(max(1, $page), $totalPages);
    $pages = [];
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i === 1 || $i === $totalPages || abs($i - $page) <= 1) {
            $pages[] = $i;
        } elseif (end($pages) !== '…') {
            $pages[] = '…';
        }
    }
    return [
        'offset' => ($page - 1) * $perPage,
        'limit' => $perPage,
        'current' => $page,
        'total_pages' => $totalPages,
        'total' => $total,
        'pages' => $pages,
    ];
}

/* ============================ Images (HTML) ============================== */

/** Balise <img> avec chargement différé et image de repli en cas d'erreur. */
function img(string $src, string $alt = '', string $class = '', array $attrs = []): string
{
    $a = ['src' => $src, 'alt' => $alt, 'loading' => 'lazy', 'decoding' => 'async'];
    if ($class !== '') {
        $a['class'] = $class;
    }
    if (preg_match('#^https?://#i', $src)) {
        $a['referrerpolicy'] = 'no-referrer';
    }
    $a['data-fallback'] = url('assets/images/placeholder.svg');
    $a = array_merge($a, $attrs);
    $html = '<img';
    foreach ($a as $k => $v) {
        if ($v === null || $v === false) {
            continue;
        }
        $html .= ' ' . $k . ($v === true ? '' : '="' . e($v) . '"');
    }
    return $html . '>';
}

function property_cover(array $p, bool $thumb = true): string
{
    return $thumb
        ? media_thumb($p['image'] ?? '', $p['thumb_remote'] ?? '', $p['image_remote'] ?? '')
        : media($p['image'] ?? '', $p['image_remote'] ?? '');
}

/** Liste des images d'un logement (couverture + galerie) : [full, thumb, caption]. */
function property_gallery(array $p): array
{
    $list = [['full' => property_cover($p, false), 'thumb' => property_cover($p, true), 'caption' => $p['title']]];
    foreach ($p['images'] ?? [] as $img) {
        $list[] = [
            'full' => media($img['path'] ?? '', $img['remote_url'] ?? ''),
            'thumb' => media_thumb($img['path'] ?? '', $img['remote_thumb'] ?? '', $img['remote_url'] ?? ''),
            'caption' => ($img['caption'] ?? '') ?: $p['title'],
        ];
    }
    return $list;
}

function post_cover(array $post, bool $thumb = true): string
{
    return $thumb
        ? media_thumb($post['image'] ?? '', $post['thumb_remote'] ?? '', $post['image_remote'] ?? '', 'assets/images/hero/hero-2.jpg')
        : media($post['image'] ?? '', $post['image_remote'] ?? '', 'assets/images/hero/hero-2.jpg');
}

/** Galerie d'un article : liste d'URL complètes. */
function post_gallery(array $post): array
{
    $out = [];
    foreach ((array) json_decode((string) ($post['gallery'] ?? ''), true) as $g) {
        $src = media($g['path'] ?? '', $g['remote'] ?? '', null);
        if ($src !== '') {
            $out[] = $src;
        }
    }
    return $out;
}

/** Initiales pour les avatars (ex. « Issa KINI » => « IK »). */
function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $out ?: 'G';
}

/** Zone d'erreur d'un champ de formulaire (remplie côté serveur ou en AJAX). */
function field_error(string $name): string
{
    $errors = $GLOBALS['__form_errors'] ?? [];
    $msg = isset($errors[$name]) ? e($errors[$name]) : '';
    return '<span class="field-error" data-error-for="' . e($name) . '">' . $msg . '</span>';
}
