<?php
/**
 * GELPAZ IMMO — Contrôleur frontal.
 * Toutes les requêtes (hors fichiers statiques) passent par ce fichier.
 */
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

use App\Admin\AdminController as A;
use App\Admin\CrudController as Crud;
use App\Controllers\FormController as F;
use App\Controllers\InstallController;
use App\Controllers\SiteController as S;
use App\Router;

$path = current_path();
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

/* ----------------------------------------------------- Installation */
if (str_starts_with($path, '/install') || !is_installed()) {
    if (!str_starts_with($path, '/install')) {
        redirect('/install');
    }
    (new InstallController())->handle();
    exit;
}

$router = new Router();

/* ---------------------------------------------------- Site public */
$router->get('/', [S::class, 'home']);
$router->get('/a-propos', [S::class, 'about']);
$router->get('/missions-visions-valeurs', [S::class, 'mission']);
$router->get('/nos-offres-immobilieres', [S::class, 'offers']);
$router->get('/logements', [S::class, 'properties']);
$router->get('/logements/{slug}', [S::class, 'property']);
$router->get('/nos-sites', [S::class, 'sites']);
$router->get('/souscription-logement', [S::class, 'subscription']);
$router->get('/nos-activites', [S::class, 'services']);
$router->get('/nos-activites/{slug}', [S::class, 'service']);
$router->get('/faq', [S::class, 'faq']);
$router->get('/actualites', [S::class, 'blog']);
$router->get('/actualites/{slug}', [S::class, 'post']);
$router->get('/contact', [S::class, 'contact']);
$router->get('/mentions-legales', [S::class, 'legal']);
$router->get('/sitemap.xml', [S::class, 'sitemap']);
$router->get('/robots.txt', [S::class, 'robots']);
$router->get('/site.webmanifest', [S::class, 'manifest']);

/* ------------------------------------------------------ Formulaires */
$router->post('/formulaire/contact', [F::class, 'contact']);
$router->post('/formulaire/souscription', [F::class, 'subscription']);
$router->post('/formulaire/newsletter', [F::class, 'newsletter']);
$router->post('/actualites/{slug}/commentaire', [F::class, 'comment']);

/* ------------------------------------------------------ Back-office */
$router->any('/admin/connexion', [A::class, 'login']);
$router->post('/admin/deconnexion', [A::class, 'logout']);
$router->get('/admin', [A::class, 'dashboard']);
$router->any('/admin/messages', [A::class, 'messages']);
$router->any('/admin/messages/{id:\d+}', [A::class, 'message']);
$router->any('/admin/souscriptions', [A::class, 'subscriptions']);
$router->any('/admin/souscriptions/{id:\d+}', [A::class, 'subscription']);
$router->any('/admin/commentaires', [A::class, 'comments']);
$router->any('/admin/newsletter', [A::class, 'newsletter']);
$router->any('/admin/reglages', [A::class, 'settings']);
$router->any('/admin/compte', [A::class, 'account']);
$router->any('/admin/outils', [A::class, 'tools']);
$router->post('/admin/outils/import', [A::class, 'importBatch']);
$router->post('/admin/upload-editeur', [A::class, 'editorUpload']);
$router->get('/admin/{module}', [Crud::class, 'index']);
$router->any('/admin/{module}/nouveau', [Crud::class, 'create']);
$router->any('/admin/{module}/{id:\d+}', [Crud::class, 'edit']);
$router->post('/admin/{module}/{id:\d+}/supprimer', [Crud::class, 'delete']);
$router->post('/admin/{module}/{id:\d+}/basculer', [Crud::class, 'toggle']);
$router->post('/admin/{module}/ordre', [Crud::class, 'reorder']);

if ($router->dispatch($method, $path)) {
    exit;
}

/* ------------------------- Redirections depuis l'ancien site WordPress */
$legacy = [
    '#^/nous-connaitre$#' => '/a-propos',
    '#^/mission-vision-valeur$#' => '/missions-visions-valeurs',
    '#^/properties-list-2(/page/\d+)?$#' => '/logements',
    '#^/nos-realisations$#' => '/logements',
    '#^/blog-list-no-sidebar-2(/page/\d+)?$#' => '/actualites',
    '#^/category/.+$#' => '/actualites',
    '#^/contact-us$#' => '/contact',
    '#^/estate_developer/.+$#' => '/contact',
    '#^/property_category/f3-economiques-finis$#' => '/logements?categorie=f3-moyen-standing',
    '#^/property_category/f4-moyen-standing$#' => '/logements?categorie=f4-moyen-standing',
    '#^/property_category/f5-duplex$#' => '/logements?categorie=f5-duplex-haut-standing',
    '#^/property_action_category/vente$#' => '/logements?type=vente',
    '#^/property_action_category/location$#' => '/logements?type=location',
    '#^/property_(city|area|category|action_category)/.+$#' => '/logements',
];
foreach ($legacy as $regex => $target) {
    if (preg_match($regex, $path)) {
        redirect($target, 301);
    }
}
if (preg_match('#^/estate_property/([a-z0-9\-]+)$#', $path, $m)) {
    $map = ['f5-haut-standing' => 'f5-duplex-haut-standing', 'modele-f4a-2' => 'modele-f4a'];
    redirect('/logements/' . ($map[$m[1]] ?? $m[1]), 301);
}
// Anciens articles : gelpaz.com/{slug-article}
if (preg_match('#^/([a-z0-9\-]+)$#', $path, $m)) {
    $post = App\Database::one('SELECT slug FROM posts WHERE (slug = ? OR legacy_slug = ?) AND is_published = 1', [$m[1], $m[1]]);
    if ($post) {
        redirect('/actualites/' . $post['slug'], 301);
    }
}

abort(404);
