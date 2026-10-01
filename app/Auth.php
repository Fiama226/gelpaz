<?php
declare(strict_types=1);

namespace App;

/**
 * Authentification du back-office (sessions + protection contre la force brute).
 */
final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW = 900; // 15 minutes
    private const IDLE_TIMEOUT = 7200; // 2 heures

    public static function attempt(string $email, string $password): bool|string
    {
        $key = 'login:' . client_ip();
        if (!rate_limit($key, self::MAX_ATTEMPTS, self::WINDOW, false)) {
            return 'Trop de tentatives de connexion. Merci de patienter 15 minutes.';
        }
        $user = Database::one('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
        if (!$user || !password_verify($password, (string) $user['password'])) {
            rate_limit($key, PHP_INT_MAX, self::WINDOW); // enregistre l'échec
            usleep(random_int(200000, 500000));
            return false;
        }
        if (password_needs_rehash((string) $user['password'], PASSWORD_DEFAULT)) {
            Database::update('users', ['password' => password_hash($password, PASSWORD_DEFAULT)], 'id = ?', [$user['id']]);
        }
        Database::delete('rate_limits', 'k = ?', [$key]);
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        $_SESSION['admin_seen'] = time();
        Database::update('users', ['last_login_at' => now()], 'id = ?', [$user['id']]);
        return true;
    }

    public static function user(): ?array
    {
        static $user = false;
        if ($user !== false) {
            return $user;
        }
        $id = (int) ($_SESSION['admin_id'] ?? 0);
        if ($id === 0) {
            return $user = null;
        }
        if (time() - (int) ($_SESSION['admin_seen'] ?? 0) > self::IDLE_TIMEOUT) {
            self::logout();
            return $user = null;
        }
        $_SESSION['admin_seen'] = time();
        return $user = Database::one('SELECT id, name, email, role, last_login_at FROM users WHERE id = ?', [$id]);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_seen']);
        session_regenerate_id(true);
    }

    public static function require(): void
    {
        if (!self::check()) {
            if (is_ajax()) {
                json_response(['ok' => false, 'message' => 'Session expirée. Merci de vous reconnecter.'], 401);
            }
            $_SESSION['admin_intended'] = current_path();
            redirect('/admin/connexion');
        }
    }
}
