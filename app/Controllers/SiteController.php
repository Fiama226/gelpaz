<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\Repo;

/**
 * Pages publiques du site.
 */
final class SiteController
{
    public function home(): string
    {
        return view('pages/home', [
            'slides' => Repo::slides(),
            'categories' => Repo::categories(),
            'featured' => Repo::properties([], 6),
            'services' => Repo::services(true),
            'sites' => Repo::sites(true),
            'partners' => Repo::partners(),
            'testimonials' => Repo::testimonials(),
            'posts' => Repo::posts([], 6),
            'searchSites' => Repo::sites(),
            'rentCount' => Repo::countProperties('location'),
        ]);
    }

    public function about(): string
    {
        return view('pages/about', [
            'services' => Repo::services(),
            'partners' => Repo::partners(),
            'testimonials' => Repo::testimonials(),
            'sites' => Repo::sites(true),
        ]);
    }

    public function mission(): string
    {
        return view('pages/mission', ['testimonials' => Repo::testimonials()]);
    }

    public function offers(): string
    {
        return view('pages/offers', [
            'services' => Repo::services(),
            'categories' => Repo::categories(),
            'featured' => Repo::properties(['featured' => 1], 3),
        ]);
    }

    public function properties(): string
    {
        $filters = [
            'categorie' => trim((string) ($_GET['categorie'] ?? '')),
            'type' => trim((string) ($_GET['type'] ?? '')),
            'site' => trim((string) ($_GET['site'] ?? '')),
            'chambres' => (int) ($_GET['chambres'] ?? 0) ?: '',
            'q' => mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 80),
            'tri' => trim((string) ($_GET['tri'] ?? '')),
        ];
        $perPage = 9;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = 0;
        Repo::properties($filters, 1, 0, $total);
        $pager = paginate($total, $perPage, $page);
        $items = Repo::properties($filters, $perPage, $pager['offset'], $total);
        return view('pages/properties', [
            'items' => $items,
            'pager' => $pager,
            'filters' => $filters,
            'categories' => Repo::categories(),
            'sites' => Repo::sites(),
            'view' => ($_GET['vue'] ?? '') === 'liste' ? 'list' : 'grid',
        ]);
    }

    public function property(string $slug): string
    {
        $property = Repo::property($slug);
        if (!$property) {
            $legacy = ['f5-haut-standing' => 'f5-duplex-haut-standing', 'modele-f4a-2' => 'modele-f4a'];
            if (isset($legacy[$slug])) {
                redirect('/logements/' . $legacy[$slug], 301);
            }
            abort(404);
        }
        Repo::incrementViews('properties', (int) $property['id']);
        $site = $property['site_id'] ? Database::one('SELECT * FROM sites WHERE id = ?', [$property['site_id']]) : null;
        return view('pages/property', [
            'p' => $property,
            'site' => $site,
            'similar' => Repo::similar($property, 6),
        ]);
    }

    public function sites(): string
    {
        $all = Repo::sites();
        return view('pages/sites', [
            'featured' => array_values(array_filter($all, static fn($s) => (int) $s['is_featured'] === 1 && $s['status'] !== 'international')),
            'others' => array_values(array_filter($all, static fn($s) => (int) $s['is_featured'] === 0 && $s['status'] !== 'international')),
            'international' => array_values(array_filter($all, static fn($s) => $s['status'] === 'international')),
            'categories' => Repo::categories(),
        ]);
    }

    public function subscription(): string
    {
        return view('pages/subscription', [
            'categories' => Repo::categories(),
            'sites' => Repo::sites(true),
            'covers' => [
                'f3-moyen-standing' => Repo::property('f3-moyen-standing'),
                'f4-moyen-standing' => Repo::property('f4-moyen-standing'),
                'f5-duplex-haut-standing' => Repo::property('f5-duplex-haut-standing'),
            ],
        ]);
    }

    public function services(): string
    {
        return view('pages/services', [
            'services' => Repo::services(),
            'recentPosts' => Repo::posts([], 3),
            'activities' => Repo::posts(['categorie' => 'RSE'], 2),
        ]);
    }

    public function service(string $slug): string
    {
        $service = Repo::service($slug);
        if (!$service) {
            abort(404);
        }
        return view('pages/service', [
            'service' => $service,
            'services' => Repo::services(),
            'recentPosts' => Repo::posts([], 3),
            'faqs' => array_slice(Repo::faqs(), 0, 4),
        ]);
    }

    public function faq(): string
    {
        return view('pages/faq', ['faqs' => Repo::faqs()]);
    }

    public function blog(): string
    {
        $filters = [
            'categorie' => trim((string) ($_GET['categorie'] ?? '')),
            'tag' => trim((string) ($_GET['tag'] ?? '')),
            'q' => mb_substr(trim((string) ($_GET['q'] ?? '')), 0, 80),
        ];
        $perPage = 9;
        $total = 0;
        Repo::posts($filters, 1, 0, $total);
        $pager = paginate($total, $perPage, max(1, (int) ($_GET['page'] ?? 1)));
        return view('pages/blog', [
            'posts' => Repo::posts($filters, $perPage, $pager['offset'], $total),
            'pager' => $pager,
            'filters' => $filters,
            'categories' => Repo::postCategories(),
        ]);
    }

    public function post(string $slug): string
    {
        $post = Repo::post($slug);
        if (!$post) {
            abort(404);
        }
        if ($post['slug'] !== $slug) {
            redirect('/actualites/' . $post['slug'], 301);
        }
        Repo::incrementViews('posts', (int) $post['id']);
        [$prev, $next] = Repo::adjacentPosts($post);
        return view('pages/post', [
            'post' => $post,
            'comments' => Repo::comments((int) $post['id']),
            'commentsCount' => (int) Database::value("SELECT COUNT(*) FROM comments WHERE post_id = ? AND status = 'approved'", [$post['id']]),
            'prev' => $prev,
            'next' => $next,
            'related' => Repo::posts(['categorie' => $post['category'], 'exclude' => $post['id']], 3),
            'recentPosts' => Repo::posts(['exclude' => $post['id']], 4),
            'categories' => Repo::postCategories(),
            'tags' => Repo::tags(),
        ]);
    }

    public function contact(): string
    {
        return view('pages/contact', [
            'properties' => Database::all('SELECT id, title FROM properties WHERE is_published = 1 ORDER BY sort, id'),
        ]);
    }

    public function legal(): string
    {
        return view('pages/legal');
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $urls = ['/', '/a-propos', '/missions-visions-valeurs', '/nos-offres-immobilieres', '/logements', '/nos-sites',
            '/souscription-logement', '/nos-activites', '/faq', '/actualites', '/contact', '/mentions-legales'];
        $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $out .= '  <url><loc>' . e(absolute_url($u)) . '</loc><changefreq>weekly</changefreq></url>' . "\n";
        }
        foreach (Database::all('SELECT slug, updated_at FROM properties WHERE is_published = 1') as $p) {
            $out .= '  <url><loc>' . e(absolute_url('/logements/' . $p['slug'])) . '</loc><lastmod>' . date('Y-m-d', strtotime((string) $p['updated_at']) ?: time()) . '</lastmod></url>' . "\n";
        }
        foreach (Database::all('SELECT slug FROM services WHERE is_published = 1') as $s) {
            $out .= '  <url><loc>' . e(absolute_url('/nos-activites/' . $s['slug'])) . '</loc></url>' . "\n";
        }
        foreach (Database::all('SELECT slug, updated_at FROM posts WHERE is_published = 1 AND published_at <= ?', [now()]) as $p) {
            $out .= '  <url><loc>' . e(absolute_url('/actualites/' . $p['slug'])) . '</loc><lastmod>' . date('Y-m-d', strtotime((string) $p['updated_at']) ?: time()) . '</lastmod></url>' . "\n";
        }
        echo $out . '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nDisallow: /admin\nDisallow: /install\nDisallow: /formulaire\nAllow: /\n\nSitemap: " . absolute_url('/sitemap.xml') . "\n";
    }

    public function manifest(): void
    {
        header('Content-Type: application/manifest+json; charset=utf-8');
        echo json_encode([
            'name' => 'GELPAZ IMMO', 'short_name' => 'GELPAZ', 'lang' => 'fr', 'start_url' => url('/'),
            'display' => 'standalone', 'background_color' => '#ffffff', 'theme_color' => '#0A1B33',
            'icons' => [
                ['src' => url('assets/images/brand/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => url('assets/images/brand/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
