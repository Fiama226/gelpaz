<?php
/**
 * Installation en ligne de commande.
 * Exemples :
 *   php tools/install.php --sqlite --admin-email=admin@gelpaz.com --admin-password="MotDePasse!"
 *   php tools/install.php --mysql-host=localhost --mysql-db=gelpaz --mysql-user=gelpaz --mysql-pass=secret \
 *        --admin-email=admin@gelpaz.com --admin-password="MotDePasse!" --mail-driver=smtp
 * Options : --debug (affiche les erreurs), --mail-driver=mail|smtp|log, --url=https://gelpaz.com, --force
 */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    exit("CLI uniquement.\n");
}
require __DIR__ . '/../app/bootstrap.php';

$o = getopt('', ['sqlite', 'mysql-host:', 'mysql-port:', 'mysql-db:', 'mysql-user:', 'mysql-pass:', 'admin-email:', 'admin-password:', 'admin-name:', 'debug', 'mail-driver:', 'url:', 'force', 'no-seed']);
if (is_installed() && !isset($o['force'])) {
    exit("Le site est déjà installé (config/config.php existe). Utilisez --force pour réinstaller.\n");
}
if (empty($o['admin-email']) || empty($o['admin-password'])) {
    exit("Paramètres requis : --admin-email et --admin-password\n");
}
$db = isset($o['mysql-host'])
    ? ['driver' => 'mysql', 'host' => $o['mysql-host'], 'port' => (int) ($o['mysql-port'] ?? 3306), 'database' => $o['mysql-db'] ?? 'gelpaz',
        'username' => $o['mysql-user'] ?? 'root', 'password' => $o['mysql-pass'] ?? '', 'charset' => 'utf8mb4']
    : ['driver' => 'sqlite', 'path' => ROOT . '/storage/database.sqlite'];

App\Installer::install($db, ['name' => $o['admin-name'] ?? 'Administrateur', 'email' => $o['admin-email'], 'password' => $o['admin-password']], !isset($o['no-seed']));
[$ok] = App\Installer::writeConfig([
    'app' => ['url' => $o['url'] ?? '', 'debug' => isset($o['debug']), 'key' => bin2hex(random_bytes(32)), 'force_https' => false],
    'db' => $db,
    'mail' => ['driver' => $o['mail-driver'] ?? 'mail', 'host' => '', 'port' => 587, 'encryption' => 'tls', 'username' => '', 'password' => '',
        'from_email' => 'infos@gelpaz.com', 'from_name' => 'GELPAZ IMMO'],
    'security' => ['frame_options' => 'SAMEORIGIN', 'hsts' => false, 'cookie_samesite' => 'Lax', 'cookie_secure' => null],
]);
echo $ok ? "Installation terminée. Connectez-vous sur /admin/connexion\n" : "Base installée, mais config/config.php n'a pas pu être écrit.\n";
