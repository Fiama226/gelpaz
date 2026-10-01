<?php
/**
 * Routeur pour le serveur PHP intégré (développement local uniquement) :
 *   php -S localhost:8000 server.php
 * En production, utilisez Apache (.htaccess fourni) ou Nginx (nginx.conf.example).
 */
$path = rawurldecode((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'));
if (preg_match('#^/(app|config|database|storage|templates|tools)(/|$)#', $path) || preg_match('#/\.(?!well-known)#', $path)) {
    http_response_code(403);
    echo 'Accès interdit';
    return true;
}
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    if (preg_match('#\.(php\d?|phtml|phar|sqlite|log)$#i', $path)) {
        http_response_code(403);
        echo 'Accès interdit';
        return true;
    }
    return false; // fichier statique servi directement
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
