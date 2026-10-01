<?php
declare(strict_types=1);

namespace App;

/**
 * Requêtes de lecture pour le site public.
 */
final class Repo
{
    private const PROPERTY_SELECT = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug, s.name AS site_name, s.slug AS site_slug
        FROM properties p
        LEFT JOIN property_categories c ON c.id = p.category_id
        LEFT JOIN sites s ON s.id = p.site_id';

    /* ------------------------------------------------------------ Logements */

    public static function properties(array $f = [], int $limit = 9, int $offset = 0, ?int &$total = null): array
    {
        $where = ['p.is_published = 1'];
        $params = [];
        if (!empty($f['categorie'])) {
            $where[] = 'c.slug = ?';
            $params[] = $f['categorie'];
        }
        if (!empty($f['type']) && in_array($f['type'], ['vente', 'location'], true)) {
            $where[] = 'p.`transaction` = ?';
            $params[] = $f['type'];
        }
        if (!empty($f['site'])) {
            $where[] = 's.slug = ?';
            $params[] = $f['site'];
        }
        if (!empty($f['chambres'])) {
            $where[] = 'p.bedrooms >= ?';
            $params[] = (int) $f['chambres'];
        }
        if (!empty($f['q'])) {
            $where[] = '(p.title LIKE ? OR p.excerpt LIKE ? OR p.city LIKE ? OR p.reference LIKE ? OR s.name LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like, $like, $like);
        }
        if (!empty($f['featured'])) {
            $where[] = 'p.is_featured = 1';
        }
        $order = match ($f['tri'] ?? '') {
            'recent' => 'p.created_at DESC, p.id DESC',
            'surface' => 'COALESCE(p.land_area, p.built_area, 0) DESC',
            'chambres' => 'p.bedrooms DESC',
            'populaire' => 'p.views DESC',
            default => 'p.is_featured DESC, p.sort ASC, p.id DESC',
        };
        $sqlWhere = ' WHERE ' . implode(' AND ', $where);
        $total = (int) Database::value('SELECT COUNT(*) FROM properties p LEFT JOIN property_categories c ON c.id = p.category_id LEFT JOIN sites s ON s.id = p.site_id' . $sqlWhere, $params);
        $rows = Database::all(self::PROPERTY_SELECT . $sqlWhere . ' ORDER BY ' . $order . ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset, $params);
        return self::withImages($rows);
    }

    public static function property(string $slug): ?array
    {
        $p = Database::one(self::PROPERTY_SELECT . ' WHERE p.slug = ? AND p.is_published = 1', [$slug]);
        if (!$p) {
            return null;
        }
        $p['images'] = Database::all('SELECT * FROM property_images WHERE property_id = ? ORDER BY sort, id', [$p['id']]);
        $p['plans'] = Database::all('SELECT * FROM property_plans WHERE property_id = ? ORDER BY sort, id', [$p['id']]);
        return $p;
    }

    public static function similar(array $p, int $limit = 6): array
    {
        $rows = Database::all(
            self::PROPERTY_SELECT . ' WHERE p.is_published = 1 AND p.id <> ? ORDER BY (p.category_id = ?) DESC, p.is_featured DESC, p.sort ASC LIMIT ' . $limit,
            [$p['id'], (int) $p['category_id']]
        );
        return self::withImages($rows);
    }

    /** Ajoute les 4 premières images de galerie à chaque logement (pour les mini-diaporamas des cartes). */
    private static function withImages(array $rows): array
    {
        if (!$rows) {
            return [];
        }
        $ids = array_map(static fn($r) => (int) $r['id'], $rows);
        $images = Database::all('SELECT * FROM property_images WHERE property_id IN (' . implode(',', $ids) . ') ORDER BY sort, id');
        $byProp = [];
        foreach ($images as $img) {
            $byProp[$img['property_id']][] = $img;
        }
        foreach ($rows as &$r) {
            $r['images_total'] = count($byProp[$r['id']] ?? []) + 1;
            $r['images'] = array_slice($byProp[$r['id']] ?? [], 0, 3);
        }
        return $rows;
    }

    public static function categories(): array
    {
        return Database::all('SELECT c.*, (SELECT COUNT(*) FROM properties p WHERE p.category_id = c.id AND p.is_published = 1) AS total
            FROM property_categories c ORDER BY c.sort, c.name');
    }

    public static function countProperties(?string $transaction = null): int
    {
        if ($transaction) {
            return (int) Database::value('SELECT COUNT(*) FROM properties WHERE is_published = 1 AND `transaction` = ?', [$transaction]);
        }
        return (int) Database::value('SELECT COUNT(*) FROM properties WHERE is_published = 1');
    }

    public static function incrementViews(string $table, int $id): void
    {
        $key = 'viewed_' . $table . '_' . $id;
        if (empty($_SESSION[$key])) {
            $_SESSION[$key] = 1;
            Database::run("UPDATE `$table` SET views = COALESCE(views, 0) + 1 WHERE id = ?", [$id]);
        }
    }

    /* ---------------------------------------------------------------- Sites */

    public static function sites(?bool $featured = null): array
    {
        $sql = 'SELECT s.*, (SELECT COUNT(*) FROM properties p WHERE p.site_id = s.id AND p.is_published = 1) AS total FROM sites s';
        if ($featured !== null) {
            $sql .= ' WHERE s.is_featured = ' . ($featured ? 1 : 0);
        }
        return Database::all($sql . ' ORDER BY s.sort, s.name');
    }

    /* ---------------------------------------------------------- Actualités */

    public static function posts(array $f = [], int $limit = 9, int $offset = 0, ?int &$total = null): array
    {
        $where = ['is_published = 1', 'published_at <= ?'];
        $params = [now()];
        if (!empty($f['categorie'])) {
            $where[] = 'category = ?';
            $params[] = $f['categorie'];
        }
        if (!empty($f['tag'])) {
            $where[] = 'tags LIKE ?';
            $params[] = '%' . $f['tag'] . '%';
        }
        if (!empty($f['q'])) {
            $where[] = '(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)';
            $like = '%' . $f['q'] . '%';
            array_push($params, $like, $like, $like);
        }
        if (!empty($f['exclude'])) {
            $where[] = 'id <> ?';
            $params[] = (int) $f['exclude'];
        }
        $sqlWhere = ' WHERE ' . implode(' AND ', $where);
        $total = (int) Database::value('SELECT COUNT(*) FROM posts' . $sqlWhere, $params);
        return Database::all('SELECT *, (SELECT COUNT(*) FROM comments cm WHERE cm.post_id = posts.id AND cm.status = \'approved\') AS comments_count FROM posts'
            . $sqlWhere . ' ORDER BY published_at DESC, id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset, $params);
    }

    public static function post(string $slug): ?array
    {
        return Database::one('SELECT * FROM posts WHERE (slug = ? OR legacy_slug = ?) AND is_published = 1 AND published_at <= ?', [$slug, $slug, now()]);
    }

    public static function adjacentPosts(array $post): array
    {
        $prev = Database::one('SELECT title, slug, image, image_remote, thumb_remote FROM posts WHERE is_published = 1 AND (published_at < ? OR (published_at = ? AND id < ?)) ORDER BY published_at DESC, id DESC LIMIT 1', [$post['published_at'], $post['published_at'], $post['id']]);
        $next = Database::one('SELECT title, slug, image, image_remote, thumb_remote FROM posts WHERE is_published = 1 AND published_at <= ? AND (published_at > ? OR (published_at = ? AND id > ?)) ORDER BY published_at ASC, id ASC LIMIT 1', [now(), $post['published_at'], $post['published_at'], $post['id']]);
        return [$prev, $next];
    }

    public static function postCategories(): array
    {
        return Database::all("SELECT category, COUNT(*) AS total FROM posts WHERE is_published = 1 AND category <> '' GROUP BY category ORDER BY total DESC, category");
    }

    public static function tags(int $limit = 16): array
    {
        $counts = [];
        foreach (Database::all("SELECT tags FROM posts WHERE is_published = 1 AND tags <> ''") as $r) {
            foreach (array_filter(array_map('trim', explode(',', (string) $r['tags']))) as $t) {
                $counts[$t] = ($counts[$t] ?? 0) + 1;
            }
        }
        arsort($counts);
        return array_slice(array_keys($counts), 0, $limit);
    }

    public static function comments(int $postId): array
    {
        $rows = Database::all("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at ASC", [$postId]);
        $tree = [];
        $children = [];
        foreach ($rows as $r) {
            if ($r['parent_id']) {
                $children[$r['parent_id']][] = $r;
            } else {
                $tree[] = $r;
            }
        }
        foreach ($tree as &$t) {
            $t['replies'] = $children[$t['id']] ?? [];
        }
        return $tree;
    }

    /* -------------------------------------------------------------- Divers */

    public static function services(bool $featuredOnly = false): array
    {
        return Database::all('SELECT * FROM services WHERE is_published = 1' . ($featuredOnly ? ' AND is_featured = 1' : '') . ' ORDER BY sort, id');
    }

    public static function service(string $slug): ?array
    {
        return Database::one('SELECT * FROM services WHERE slug = ? AND is_published = 1', [$slug]);
    }

    public static function faqs(): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_published = 1 ORDER BY sort, id');
    }

    public static function testimonials(): array
    {
        return Database::all('SELECT * FROM testimonials WHERE is_published = 1 ORDER BY sort, id');
    }

    public static function partners(): array
    {
        return Database::all('SELECT * FROM partners WHERE is_published = 1 ORDER BY sort, id');
    }

    public static function slides(): array
    {
        return Database::all('SELECT * FROM slides WHERE is_published = 1 ORDER BY sort, id');
    }
}
