<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Installer;
use App\View;
use Throwable;

/**
 * Assistant d'installation web (/install).
 */
final class InstallController
{
    public function handle(): void
    {
        if (is_installed()) {
            redirect('/admin');
        }
        $errors = [];
        $input = [
            'driver' => extension_loaded('pdo_mysql') ? 'mysql' : 'sqlite',
            'host' => 'localhost', 'port' => '3306', 'database' => 'gelpaz', 'username' => '', 'password' => '',
            'admin_name' => 'Administrateur', 'admin_email' => '', 'mail_driver' => 'mail',
            'smtp_host' => '', 'smtp_port' => '587', 'smtp_encryption' => 'tls', 'smtp_username' => '', 'smtp_password' => '',
            'from_email' => 'infos@gelpaz.com',
        ];
        $generated = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            foreach ($input as $k => $v) {
                $input[$k] = trim((string) ($_POST[$k] ?? $v));
            }
            $password = (string) ($_POST['admin_password'] ?? '');
            if (!csrf_verify()) {
                $errors[] = 'Session expirée : merci de recharger la page.';
            }
            if (!in_array($input['driver'], ['mysql', 'sqlite'], true)) {
                $errors[] = 'Type de base de données invalide.';
            }
            if (!filter_var($input['admin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse e-mail administrateur invalide.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Le mot de passe administrateur doit contenir au moins 8 caractères.';
            }
            if ($password !== (string) ($_POST['admin_password_confirm'] ?? '')) {
                $errors[] = 'Les deux mots de passe ne correspondent pas.';
            }
            if (!$errors) {
                $db = $input['driver'] === 'mysql'
                    ? ['driver' => 'mysql', 'host' => $input['host'], 'port' => (int) $input['port'], 'database' => $input['database'],
                        'username' => $input['username'], 'password' => $input['password'], 'charset' => 'utf8mb4']
                    : ['driver' => 'sqlite', 'path' => ROOT . '/storage/database.sqlite'];
                try {
                    Installer::install($db, ['name' => $input['admin_name'] ?: 'Administrateur', 'email' => $input['admin_email'], 'password' => $password]);
                    $config = [
                        'app' => ['url' => '', 'debug' => false, 'key' => bin2hex(random_bytes(32)), 'force_https' => is_https()],
                        'db' => $db,
                        'mail' => [
                            'driver' => $input['mail_driver'] === 'smtp' ? 'smtp' : 'mail',
                            'host' => $input['smtp_host'], 'port' => (int) $input['smtp_port'], 'encryption' => $input['smtp_encryption'],
                            'username' => $input['smtp_username'], 'password' => $input['smtp_password'],
                            'from_email' => $input['from_email'] ?: 'infos@gelpaz.com', 'from_name' => 'GELPAZ IMMO',
                        ],
                        'security' => ['frame_options' => 'SAMEORIGIN', 'hsts' => false, 'cookie_samesite' => 'Lax', 'cookie_secure' => null],
                    ];
                    if ($input['driver'] === 'sqlite') {
                        // Chemin relatif au fichier de configuration pour rester portable
                        $config['db']['path'] = ROOT . '/storage/database.sqlite';
                    }
                    [$ok, $php] = Installer::writeConfig($config);
                    if ($ok) {
                        flash('success', 'Installation terminée ! Connectez-vous avec votre compte administrateur.');
                        redirect('/admin/connexion');
                    }
                    $generated = $php;
                } catch (Throwable $e) {
                    $errors[] = 'Installation impossible : ' . $e->getMessage();
                }
            }
        }
        echo View::render('install', [
            'requirements' => Installer::requirements(),
            'errors' => $errors,
            'input' => $input,
            'generated' => $generated,
        ], null);
    }
}
