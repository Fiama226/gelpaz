<?php
declare(strict_types=1);

namespace App;

use PDO;
use RuntimeException;

/**
 * Installation : création des tables, contenu initial et compte administrateur.
 */
final class Installer
{
    /** Vérifie les prérequis serveur. Retourne [libellé => bool]. */
    public static function requirements(): array
    {
        return [
            'PHP 8.1 ou supérieur (actuel : ' . PHP_VERSION . ')' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'Extension PDO' => extension_loaded('pdo'),
            'Pilote PDO MySQL ou SQLite' => extension_loaded('pdo_mysql') || extension_loaded('pdo_sqlite'),
            'Extension mbstring' => extension_loaded('mbstring'),
            'Extension GD (redimensionnement des images)' => extension_loaded('gd'),
            'Dossier config/ accessible en écriture' => is_writable(ROOT . '/config'),
            'Dossier storage/ accessible en écriture' => is_writable(ROOT . '/storage'),
            'Dossier uploads/ accessible en écriture' => is_writable(ROOT . '/uploads'),
        ];
    }

    public static function install(array $db, array $admin, bool $seed = true): void
    {
        $pdo = Database::connect($db);
        Database::setConnection($pdo);
        $driver = (string) $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        foreach (Schema::statements($driver) as $sql) {
            $pdo->exec($sql);
        }
        if ($seed) {
            self::seed();
        }
        self::createAdmin($admin['name'] ?? 'Administrateur', (string) $admin['email'], (string) $admin['password']);
    }

    public static function createAdmin(string $name, string $email, string $password): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Adresse e-mail administrateur invalide.');
        }
        if (strlen($password) < 8) {
            throw new RuntimeException('Le mot de passe administrateur doit contenir au moins 8 caractères.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $existing = Database::one('SELECT id FROM users WHERE email = ?', [strtolower($email)]);
        if ($existing) {
            Database::update('users', ['password' => $hash, 'name' => $name], 'id = ?', [$existing['id']]);
            return;
        }
        Database::insert('users', [
            'name' => $name, 'email' => strtolower($email), 'password' => $hash,
            'role' => 'admin', 'created_at' => now(),
        ]);
    }

    /** Insère le contenu initial (uniquement dans les tables vides). */
    public static function seed(): void
    {
        $c = require ROOT . '/database/seeds/content.php';
        $posts = require ROOT . '/database/seeds/posts.php';
        $now = now();
        $empty = static fn(string $t) => (int) Database::value("SELECT COUNT(*) FROM `$t`") === 0;

        Database::transaction(static function () use ($c, $posts, $now, $empty) {
            if ($empty('settings')) {
                foreach ($c['settings'] as $k => $v) {
                    Database::insert('settings', ['name' => $k, 'value' => (string) $v]);
                }
            }

            $catIds = [];
            if ($empty('property_categories')) {
                foreach ($c['categories'] as $cat) {
                    $catIds[$cat['slug']] = Database::insert('property_categories', $cat);
                }
            } else {
                foreach (Database::all('SELECT id, slug FROM property_categories') as $r) {
                    $catIds[$r['slug']] = (int) $r['id'];
                }
            }

            $siteIds = [];
            if ($empty('sites')) {
                foreach ($c['sites'] as $i => $site) {
                    $siteIds[$site['slug']] = Database::insert('sites', [
                        'name' => $site['name'], 'slug' => $site['slug'], 'city' => $site['city'], 'area' => $site['area'],
                        'description' => $site['description'], 'status' => $site['status'], 'image' => $site['image'] ?? '',
                        'image_remote' => $site['image_remote'] ?? '', 'map_query' => $site['map_query'],
                        'is_featured' => (int) $site['is_featured'], 'sort' => $i + 1, 'created_at' => $now,
                    ]);
                }
            } else {
                foreach (Database::all('SELECT id, slug FROM sites') as $r) {
                    $siteIds[$r['slug']] = (int) $r['id'];
                }
            }

            if ($empty('properties')) {
                foreach ($c['properties'] as $i => $p) {
                    $id = Database::insert('properties', [
                        'title' => $p['title'], 'slug' => $p['slug'], 'reference' => $p['reference'],
                        'category_id' => $catIds[$p['category']] ?? null, 'site_id' => $p['site'] ? ($siteIds[$p['site']] ?? null) : null,
                        'transaction' => $p['transaction'], 'status' => $p['status'], 'city' => $p['city'], 'address' => $p['address'],
                        'built_area' => $p['built_area'] ?? null, 'land_area' => $p['land_area'] ?? null, 'rooms' => $p['rooms'] ?? null,
                        'bedrooms' => $p['bedrooms'] ?? null, 'bathrooms' => $p['bathrooms'] ?? null, 'living_rooms' => $p['living_rooms'] ?? null,
                        'kitchens' => $p['kitchens'] ?? null, 'terraces' => $p['terraces'] ?? null, 'garages' => $p['garages'] ?? null,
                        'floors' => $p['floors'] ?? null, 'year_built' => null, 'excerpt' => $p['excerpt'], 'description' => $p['description'],
                        'features' => $p['features'], 'image' => '', 'image_remote' => $p['cover'][0], 'thumb_remote' => $p['cover'][1],
                        'video_url' => '', 'map_query' => '', 'is_featured' => (int) ($p['is_featured'] ?? 0), 'is_published' => 1,
                        'views' => 0, 'sort' => $i + 1, 'created_at' => $now, 'updated_at' => $now,
                    ]);
                    foreach ($p['images'] as $j => [$remote, $thumb]) {
                        Database::insert('property_images', [
                            'property_id' => $id, 'path' => '', 'remote_url' => $remote, 'remote_thumb' => $thumb,
                            'caption' => '', 'sort' => $j + 1,
                        ]);
                    }
                }
            }

            if ($empty('posts')) {
                foreach ($posts as $p) {
                    $remote = (string) $p['image_remote'];
                    $thumb = '';
                    if ($remote !== '' && preg_match('#/(2026/0[89]|2025/02|2023/07/DSC)#', $remote)) {
                        $thumb = str_contains($remote, '-scaled.')
                            ? str_replace('-scaled.', '-525x328.', $remote)
                            : (string) preg_replace('/(\.jpe?g)$/i', '-525x328$1', $remote);
                    }
                    $gallery = array_map(static fn($g) => ['path' => '', 'remote' => $g], $p['gallery']);
                    Database::insert('posts', [
                        'title' => $p['title'], 'slug' => $p['slug'], 'legacy_slug' => $p['legacy_slug'] ?? null,
                        'category' => $p['category'], 'tags' => $p['tags'], 'author' => 'GELPAZ IMMO',
                        'excerpt' => $p['excerpt'], 'content' => $p['content'], 'image' => $p['image'],
                        'image_remote' => $remote, 'thumb_remote' => $thumb,
                        'gallery' => json_encode($gallery, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'is_published' => 1, 'is_featured' => 0, 'published_at' => $p['published_at'], 'views' => 0,
                        'created_at' => $p['published_at'], 'updated_at' => $p['published_at'],
                    ]);
                }
            }

            if ($empty('services')) {
                foreach ($c['services'] as $i => $s) {
                    Database::insert('services', $s + ['sort' => $i + 1, 'is_published' => 1]);
                }
            }
            if ($empty('faqs')) {
                foreach ($c['faqs'] as $i => $f) {
                    Database::insert('faqs', $f + ['sort' => $i + 1, 'is_published' => 1]);
                }
            }
            if ($empty('testimonials')) {
                foreach ($c['testimonials'] as $i => $t) {
                    Database::insert('testimonials', $t + ['rating' => null, 'photo' => '', 'sort' => $i + 1, 'is_published' => 1]);
                }
            }
            if ($empty('partners')) {
                foreach ($c['partners'] as $i => $p) {
                    Database::insert('partners', $p + ['logo' => '', 'url' => '', 'sort' => $i + 1, 'is_published' => 1]);
                }
            }
            if ($empty('slides')) {
                foreach ($c['slides'] as $i => $s) {
                    Database::insert('slides', $s + ['sort' => $i + 1, 'is_published' => 1]);
                }
            }
        });
    }

    /** Écrit config/config.php. Retourne le contenu (à copier manuellement si l'écriture échoue). */
    public static function writeConfig(array $config): array
    {
        $php = "<?php\n// Généré par l'assistant d'installation GELPAZ IMMO le " . date('d/m/Y H:i') . "\n// Ne versionnez pas ce fichier.\nreturn " . self::export($config) . ";\n";
        $ok = @file_put_contents(ROOT . '/config/config.php', $php, LOCK_EX) !== false;
        if ($ok) {
            @chmod(ROOT . '/config/config.php', 0640);
        }
        if ($ok && function_exists('opcache_invalidate')) {
            @opcache_invalidate(ROOT . '/config/config.php', true);
        }
        return [$ok, $php];
    }

    private static function export(mixed $value, int $level = 0): string
    {
        if (is_array($value)) {
            $pad = str_repeat('    ', $level + 1);
            $out = "[\n";
            foreach ($value as $k => $v) {
                $out .= $pad . var_export($k, true) . ' => ' . self::export($v, $level + 1) . ",\n";
            }
            return $out . str_repeat('    ', $level) . ']';
        }
        return var_export($value, true);
    }
}
