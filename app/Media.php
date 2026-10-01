<?php
declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Gestion des images : téléversement sécurisé, redimensionnement (GD), miniatures,
 * et importation des images de l'ancien site.
 */
final class Media
{
    public const MAX_SIZE = 10 * 1024 * 1024; // 10 Mo
    private const MIMES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    private const FOLDERS = ['properties', 'plans', 'posts', 'sites', 'partners', 'services', 'slides', 'testimonials', 'misc'];

    /** Normalise $_FILES['x'] (simple ou multiple) en liste de fichiers. */
    public static function files(?array $input): array
    {
        if (!$input || !isset($input['name'])) {
            return [];
        }
        if (!is_array($input['name'])) {
            return ($input['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE ? [] : [$input];
        }
        $out = [];
        foreach ($input['name'] as $i => $name) {
            if (($input['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name' => $name, 'type' => $input['type'][$i] ?? '', 'tmp_name' => $input['tmp_name'][$i] ?? '',
                'error' => $input['error'][$i] ?? 0, 'size' => $input['size'][$i] ?? 0,
            ];
        }
        return $out;
    }

    /** Téléverse une image et retourne son chemin relatif (ex. uploads/properties/2026/10/abc.jpg). */
    public static function upload(array $file, string $folder, int $maxWidth = 1920, int $thumbWidth = 800): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(self::uploadError((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
        }
        if (($file['size'] ?? 0) > self::MAX_SIZE) {
            throw new RuntimeException('Image trop lourde (10 Mo maximum).');
        }
        if (!is_uploaded_file($file['tmp_name']) && !IS_CLI) {
            throw new RuntimeException('Téléversement invalide.');
        }
        return self::store((string) file_get_contents($file['tmp_name']), $folder, (string) ($file['name'] ?? 'image'), $maxWidth, $thumbWidth);
    }

    /** Enregistre des données binaires d'image après validation. */
    public static function store(string $data, string $folder, string $nameHint, int $maxWidth = 1920, int $thumbWidth = 800): string
    {
        if (!in_array($folder, self::FOLDERS, true)) {
            $folder = 'misc';
        }
        if ($data === '' || strlen($data) > self::MAX_SIZE * 2) {
            throw new RuntimeException('Fichier image vide ou trop volumineux.');
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->buffer($data);
        if (!isset(self::MIMES[$mime]) || @getimagesizefromstring($data) === false) {
            throw new RuntimeException('Format non pris en charge (JPG, PNG, WebP ou GIF uniquement).');
        }
        $ext = self::MIMES[$mime];
        $base = substr(slugify(pathinfo($nameHint, PATHINFO_FILENAME)), 0, 40) ?: 'image';
        $relDir = 'uploads/' . $folder . '/' . date('Y/m');
        $absDir = ROOT . '/' . $relDir;
        if (!is_dir($absDir . '/thumbs') && !@mkdir($absDir . '/thumbs', 0775, true) && !is_dir($absDir . '/thumbs')) {
            throw new RuntimeException('Impossible de créer le dossier ' . $relDir);
        }
        $name = $base . '-' . bin2hex(random_bytes(4));
        $finalExt = ($ext === 'gif') ? 'gif' : $ext;
        $rel = $relDir . '/' . $name . '.' . $finalExt;
        $abs = ROOT . '/' . $rel;

        if (extension_loaded('gd') && $ext !== 'gif') {
            $img = @imagecreatefromstring($data);
            if (!$img) {
                throw new RuntimeException('Image illisible ou corrompue.');
            }
            $img = self::orient($img, $data, $mime);
            self::save(self::resize($img, $maxWidth), $abs, $ext);
            self::save(self::resize($img, $thumbWidth), $absDir . '/thumbs/' . $name . '.' . $finalExt, $ext);
        } else {
            file_put_contents($abs, $data);
            copy($abs, $absDir . '/thumbs/' . $name . '.' . $finalExt);
        }
        return $rel;
    }

    private static function orient(\GdImage $img, string $data, string $mime): \GdImage
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data('data://image/jpeg;base64,' . base64_encode($data));
        $o = (int) ($exif['Orientation'] ?? 1);
        $rotated = match ($o) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => null,
        };
        return $rotated ?: $img;
    }

    private static function resize(\GdImage $img, int $maxWidth): \GdImage
    {
        $w = imagesx($img);
        $h = imagesy($img);
        if ($w <= $maxWidth) {
            return $img;
        }
        $nw = $maxWidth;
        $nh = (int) round($h * $nw / $w);
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $dst;
    }

    private static function save(\GdImage $img, string $path, string $ext): void
    {
        match ($ext) {
            'png' => imagepng($img, $path, 7),
            'webp' => imagewebp($img, $path, 82),
            default => imagejpeg($img, $path, 82),
        };
    }

    /** Supprime une image téléversée (et sa miniature). Ne touche qu'au dossier uploads/. */
    public static function delete(?string $path): void
    {
        $path = ltrim((string) $path, '/');
        if ($path === '' || !str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            return;
        }
        @unlink(ROOT . '/' . $path);
        @unlink(ROOT . '/' . preg_replace('#/([^/]+)$#', '/thumbs/$1', $path));
    }

    private static function uploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image trop lourde pour le serveur.',
            UPLOAD_ERR_PARTIAL => 'Téléversement incomplet, merci de réessayer.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier envoyé.',
            default => 'Erreur lors du téléversement (code ' . $code . ').',
        };
    }

    /* ======================= Import des images distantes ======================= */

    /** Télécharge une URL distante (cURL ou flux PHP). */
    public static function download(string $url): string
    {
        if (!preg_match('#^https?://#i', $url)) {
            throw new RuntimeException('URL invalide.');
        }
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_USERAGENT => 'GELPAZ-IMMO-Importer/2.0',
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            ]);
            $data = curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            if ($data === false || $code >= 400) {
                throw new RuntimeException('Téléchargement impossible (' . ($err ?: 'HTTP ' . $code) . ').');
            }
            return (string) $data;
        }
        $ctx = stream_context_create(['http' => ['timeout' => 30, 'user_agent' => 'GELPAZ-IMMO-Importer/2.0']]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data === false) {
            throw new RuntimeException('Téléchargement impossible (allow_url_fopen désactivé ?).');
        }
        return $data;
    }

    /** Liste des images distantes restant à importer. */
    public static function pending(): array
    {
        $items = [];
        $isLocal = static fn(?string $p) => $p && is_file(ROOT . '/' . ltrim($p, '/'));
        foreach (Database::all("SELECT id, image, image_remote FROM properties WHERE image_remote <> ''") as $r) {
            if (!$isLocal($r['image'])) {
                $items[] = ['key' => 'properties:' . $r['id'], 'table' => 'properties', 'id' => (int) $r['id'], 'col' => 'image', 'url' => $r['image_remote'], 'folder' => 'properties'];
            }
        }
        foreach (Database::all("SELECT id, path, remote_url FROM property_images WHERE remote_url <> ''") as $r) {
            if (!$isLocal($r['path'])) {
                $items[] = ['key' => 'property_images:' . $r['id'], 'table' => 'property_images', 'id' => (int) $r['id'], 'col' => 'path', 'url' => $r['remote_url'], 'folder' => 'properties'];
            }
        }
        foreach (Database::all("SELECT id, image, image_remote FROM property_plans WHERE image_remote <> ''") as $r) {
            if (!$isLocal($r['image'])) {
                $items[] = ['key' => 'property_plans:' . $r['id'], 'table' => 'property_plans', 'id' => (int) $r['id'], 'col' => 'image', 'url' => $r['image_remote'], 'folder' => 'plans'];
            }
        }
        foreach (Database::all("SELECT id, image, image_remote, gallery FROM posts") as $r) {
            if ($r['image_remote'] && !$isLocal($r['image'])) {
                $items[] = ['key' => 'posts:' . $r['id'], 'table' => 'posts', 'id' => (int) $r['id'], 'col' => 'image', 'url' => $r['image_remote'], 'folder' => 'posts'];
            }
            foreach ((array) json_decode((string) $r['gallery'], true) as $i => $g) {
                if (!empty($g['remote']) && !$isLocal($g['path'] ?? '')) {
                    $items[] = ['key' => 'gallery:' . $r['id'] . ':' . $i, 'table' => 'posts_gallery', 'id' => (int) $r['id'], 'index' => $i, 'url' => $g['remote'], 'folder' => 'posts'];
                }
            }
        }
        foreach (Database::all("SELECT id, image, image_remote FROM sites WHERE image_remote <> ''") as $r) {
            if (!$isLocal($r['image'])) {
                $items[] = ['key' => 'sites:' . $r['id'], 'table' => 'sites', 'id' => (int) $r['id'], 'col' => 'image', 'url' => $r['image_remote'], 'folder' => 'sites'];
            }
        }
        foreach (Database::all("SELECT id, logo, logo_remote FROM partners WHERE logo_remote <> ''") as $r) {
            if (!$isLocal($r['logo'])) {
                $items[] = ['key' => 'partners:' . $r['id'], 'table' => 'partners', 'id' => (int) $r['id'], 'col' => 'logo', 'url' => $r['logo_remote'], 'folder' => 'partners'];
            }
        }
        return $items;
    }

    /** Importe jusqu'à $limit images. Retourne un bilan. */
    public static function importBatch(int $limit = 4, array $skip = []): array
    {
        $done = 0;
        $errors = [];
        $pending = array_values(array_filter(self::pending(), static fn($i) => !in_array($i['key'], $skip, true)));
        foreach (array_slice($pending, 0, $limit) as $item) {
            try {
                $data = self::download($item['url']);
                $width = $item['folder'] === 'partners' ? 600 : 1920;
                $path = self::store($data, $item['folder'], basename((string) parse_url($item['url'], PHP_URL_PATH)), $width, $item['folder'] === 'partners' ? 400 : 800);
                if ($item['table'] === 'posts_gallery') {
                    $gallery = (array) json_decode((string) Database::value('SELECT gallery FROM posts WHERE id = ?', [$item['id']]), true);
                    $gallery[$item['index']]['path'] = $path;
                    Database::update('posts', ['gallery' => json_encode($gallery, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)], 'id = ?', [$item['id']]);
                } else {
                    Database::update($item['table'], [$item['col'] => $path], 'id = ?', [$item['id']]);
                }
                $done++;
            } catch (\Throwable $e) {
                $errors[] = ['key' => $item['key'], 'url' => $item['url'], 'error' => $e->getMessage()];
            }
        }
        $remaining = count($pending) - $done - count($errors);
        return ['done' => $done, 'errors' => $errors, 'remaining' => max(0, $remaining), 'total_pending' => count($pending)];
    }
}
