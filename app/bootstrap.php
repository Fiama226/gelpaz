<?php
/**
 * GELPAZ IMMO — Amorçage de l'application.
 * Chargé par index.php (web) et par les scripts du dossier tools/ (CLI).
 */
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('APP_VERSION', '2.0.0');
define('IS_CLI', PHP_SAPI === 'cli');

mb_internal_encoding('UTF-8');
date_default_timezone_set('Africa/Ouagadougou');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

/* -------------------------------------------------------------------------
 * Configuration
 * ---------------------------------------------------------------------- */
$defaults = [
    'app' => ['url' => '', 'debug' => false, 'key' => '', 'force_https' => false],
    'db' => [
        'driver' => 'sqlite', 'host' => 'localhost', 'port' => 3306, 'database' => 'gelpaz',
        'username' => 'root', 'password' => '', 'charset' => 'utf8mb4',
        'path' => ROOT . '/storage/database.sqlite',
    ],
    'mail' => [
        'driver' => 'log', 'host' => '', 'port' => 587, 'encryption' => 'tls', 'username' => '',
        'password' => '', 'from_email' => 'no-reply@gelpaz.com', 'from_name' => 'GELPAZ IMMO',
    ],
    'security' => ['frame_options' => '', 'hsts' => false, 'cookie_samesite' => 'Lax', 'cookie_secure' => null],
];
$configFile = ROOT . '/config/config.php';
$userConfig = is_file($configFile) ? (array) require $configFile : [];
$GLOBALS['__config'] = array_replace_recursive($defaults, $userConfig);
$GLOBALS['__config']['installed'] = $userConfig !== [];

if (($GLOBALS['__config']['app']['key'] ?? '') === '') {
    // Clé de secours propre à l'installation (fichier non versionné)
    $keyFile = ROOT . '/storage/app.key';
    if (!is_file($keyFile)) {
        @file_put_contents($keyFile, bin2hex(random_bytes(32)));
    }
    $GLOBALS['__config']['app']['key'] = is_file($keyFile) ? trim((string) file_get_contents($keyFile)) : 'gelpaz-fallback-key';
}

/* -------------------------------------------------------------------------
 * Erreurs
 * ---------------------------------------------------------------------- */
$debug = (bool) $GLOBALS['__config']['app']['debug'];
error_reporting(E_ALL);
ini_set('display_errors', $debug || IS_CLI ? '1' : '0');
ini_set('log_errors', '1');
if (is_dir(ROOT . '/storage/logs') && is_writable(ROOT . '/storage/logs')) {
    ini_set('error_log', ROOT . '/storage/logs/php-error.log');
}

/* -------------------------------------------------------------------------
 * Autoload (namespace App\ => app/)
 * ---------------------------------------------------------------------- */
spl_autoload_register(static function (string $class): void {
    if (strncmp($class, 'App\\', 4) !== 0) {
        return;
    }
    $file = ROOT . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require ROOT . '/app/helpers.php';

set_exception_handler(static function (Throwable $e): void {
    error_log('[GELPAZ] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (IS_CLI) {
        fwrite(STDERR, $e . PHP_EOL);
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(500);
    }
    if (config('app.debug')) {
        echo '<pre style="padding:20px;font:13px/1.5 monospace;white-space:pre-wrap">' . htmlspecialchars((string) $e) . '</pre>';
        return;
    }
    $file = ROOT . '/templates/pages/500.php';
    if (is_file($file)) {
        include $file;
    } else {
        echo 'Une erreur est survenue. Merci de réessayer plus tard.';
    }
});

/* -------------------------------------------------------------------------
 * Requête web : HTTPS, en-têtes de sécurité, session
 * ---------------------------------------------------------------------- */
if (!IS_CLI) {
    if (config('app.force_https') && !is_https()) {
        header('Location: https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
        exit;
    }
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if ($fo = (string) config('security.frame_options')) {
        header('X-Frame-Options: ' . $fo);
    }
    if (config('security.hsts') && is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    header_remove('X-Powered-By');

    // Cookie de session : SameSite=Lax par défaut (recommandé). « None » (+ Secure) n'est utile
    // que si le site doit fonctionner dans un iframe d'un autre domaine (prévisualisation).
    $sameSite = ucfirst(strtolower((string) config('security.cookie_samesite', 'Lax')));
    $secureCfg = config('security.cookie_secure');
    $secure = $secureCfg === null ? is_https() : (bool) $secureCfg;
    if ($sameSite === 'None' && !$secure) {
        $sameSite = 'Lax';
    }
    session_name('gelpaz_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => in_array($sameSite, ['Lax', 'Strict', 'None'], true) ? $sameSite : 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    if (is_dir(ROOT . '/storage/sessions') && is_writable(ROOT . '/storage/sessions')) {
        session_save_path(ROOT . '/storage/sessions');
    }
    session_start();
}
