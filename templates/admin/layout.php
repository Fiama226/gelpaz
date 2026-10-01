<?php
$user = App\Auth::user();
$isAdmin = ($user['role'] ?? '') === 'admin';
$badges = [
    'messages' => (int) App\Database::value('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
    'souscriptions' => (int) App\Database::value("SELECT COUNT(*) FROM subscriptions WHERE status = 'nouveau'"),
    'commentaires' => (int) App\Database::value("SELECT COUNT(*) FROM comments WHERE status = 'pending'"),
];
$nav = [
    'Pilotage' => [['/admin', 'Tableau de bord', 'dashboard', true]],
    'Contenus' => [
        ['/admin/logements', 'Logements', 'home'], ['/admin/gammes', 'Gammes', 'layers'], ['/admin/sites', 'Sites', 'map-pin'],
        ['/admin/actualites', 'Actualités', 'newspaper'], ['/admin/services', 'Activités', 'briefcase'], ['/admin/faq', 'FAQ', 'help-circle'],
        ['/admin/temoignages', 'Témoignages', 'quote'], ['/admin/partenaires', 'Partenaires', 'handshake'], ['/admin/diaporama', 'Diaporama', 'images'],
    ],
    'Demandes' => [
        ['/admin/messages', 'Messages', 'inbox', false, 'messages'], ['/admin/souscriptions', 'Souscriptions', 'clipboard-list', false, 'souscriptions'],
        ['/admin/commentaires', 'Commentaires', 'message-square', false, 'commentaires'], ['/admin/newsletter', 'Newsletter', 'mail'],
    ],
    'Configuration' => array_values(array_filter([
        $isAdmin ? ['/admin/reglages', 'Réglages', 'settings'] : null,
        $isAdmin ? ['/admin/utilisateurs', 'Utilisateurs', 'users'] : null,
        $isAdmin ? ['/admin/outils', 'Outils', 'wrench'] : null,
        ['/admin/compte', 'Mon compte', 'user'],
    ])),
];
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle ?? 'Administration') ?> — Administration GELPAZ IMMO</title>
<link rel="icon" href="<?= url('assets/images/brand/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin">
<aside class="adm-sidebar" id="admSidebar">
    <a class="adm-brand" href="<?= url('/admin') ?>"><img src="<?= url('assets/images/brand/logo-gelpaz-white.svg') ?>" alt="GELPAZ IMMO" width="110" height="69"><span>Administration</span></a>
    <nav class="adm-nav" aria-label="Menu d’administration">
        <?php foreach ($nav as $group => $items): ?>
        <p class="adm-nav-title"><?= e($group) ?></p>
        <ul>
            <?php foreach ($items as $it):
                $active = !empty($it[3]) ? current_path() === $it[0] : str_starts_with(current_path(), $it[0]); ?>
            <li><a class="<?= $active ? 'is-active' : '' ?>" href="<?= url($it[0]) ?>"><?= icon($it[2]) ?><span><?= e($it[1]) ?></span><?php if (!empty($it[4]) && $badges[$it[4]] > 0): ?><em class="adm-badge"><?= $badges[$it[4]] ?></em><?php endif; ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </nav>
    <div class="adm-sidebar-foot">
        <a href="<?= url('/') ?>" target="_blank" rel="noopener"><?= icon('external-link') ?><span>Voir le site</span></a>
    </div>
</aside>
<div class="adm-main">
    <header class="adm-topbar">
        <button class="adm-burger" type="button" aria-label="Menu" data-toggle-sidebar><?= icon('menu') ?></button>
        <h1 class="adm-title"><?= e($pageTitle ?? 'Administration') ?></h1>
        <div class="adm-user">
            <span class="adm-avatar"><?= e(initials((string) ($user['name'] ?? 'A'))) ?></span>
            <span class="adm-user-name"><strong><?= e($user['name'] ?? '') ?></strong><small><?= ($user['role'] ?? '') === 'admin' ? 'Administrateur' : 'Éditeur' ?></small></span>
            <form method="post" action="<?= url('/admin/deconnexion') ?>"><?= csrf_field() ?><button class="adm-icon-btn" type="submit" title="Déconnexion" aria-label="Déconnexion"><?= icon('log-out') ?></button></form>
        </div>
    </header>
    <div class="adm-content">
        <?php if ($m = flash('success')): ?><div class="adm-alert adm-alert-success"><?= icon('check-circle') ?><span><?= e($m) ?></span></div><?php endif; ?>
        <?php if ($m = flash('error')): ?><div class="adm-alert adm-alert-error"><?= icon('alert-triangle') ?><span><?= e($m) ?></span></div><?php endif; ?>
        <?= $content ?>
    </div>
</div>
<div class="adm-overlay" data-toggle-sidebar></div>
<script>window.ADMIN = {csrf: <?= json_encode(csrf_token()) ?>, base: <?= json_encode(base_path()) ?>};</script>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
