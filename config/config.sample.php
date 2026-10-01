<?php
/**
 * GELPAZ IMMO — Fichier de configuration.
 *
 * Copiez ce fichier en « config/config.php » puis renseignez vos paramètres,
 * OU laissez l'assistant d'installation (https://votre-domaine/install) le créer pour vous.
 * Ne versionnez jamais config/config.php (il contient vos mots de passe).
 */
return [
    'app' => [
        // URL publique du site, sans « / » final (laisser vide pour une détection automatique).
        'url'         => 'https://gelpaz.com',
        // Afficher les erreurs PHP (à désactiver en production).
        'debug'       => false,
        // Clé secrète (64 caractères aléatoires) : sert à signer les formulaires.
        'key'         => 'remplacez-moi-par-une-longue-chaine-aleatoire',
        // Rediriger automatiquement http:// vers https://
        'force_https' => false,
    ],

    // Base de données : 'mysql' (recommandé en production) ou 'sqlite'.
    'db' => [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'port'     => 3306,
        'database' => 'gelpaz',
        'username' => 'gelpaz_user',
        'password' => '',
        'charset'  => 'utf8mb4',
        // Utilisé uniquement si driver = 'sqlite'
        'path'     => __DIR__ . '/../storage/database.sqlite',
    ],

    // Envoi des e-mails des formulaires.
    // driver : 'mail' (fonction mail() de PHP), 'smtp' (recommandé) ou 'log' (tests : écrit dans storage/logs/mail.log)
    'mail' => [
        'driver'     => 'smtp',
        'host'       => 'mail.gelpaz.com',
        'port'       => 587,
        'encryption' => 'tls',          // 'tls', 'ssl' ou ''
        'username'   => 'infos@gelpaz.com',
        'password'   => '',
        'from_email' => 'infos@gelpaz.com',
        'from_name'  => 'GELPAZ IMMO',
    ],

    'security' => [
        // En-tête X-Frame-Options (protection contre le clickjacking). Mettre '' pour désactiver.
        'frame_options' => 'SAMEORIGIN',
        // Activer HSTS (uniquement si le site est entièrement servi en HTTPS).
        'hsts'          => false,
        // Cookie de session : 'Lax' (recommandé). 'None' uniquement si le site est affiché dans un iframe externe.
        'cookie_samesite' => 'Lax',
        // Forcer le drapeau Secure du cookie (null = automatique selon HTTPS).
        'cookie_secure'   => null,
    ],
];
