<?php
$nav = [
    ['label' => 'Accueil', 'url' => '/'],
    ['label' => 'À propos', 'url' => '/a-propos', 'children' => [
        ['label' => 'Qui sommes-nous ?', 'url' => '/a-propos', 'icon' => 'building-2'],
        ['label' => 'Missions – Visions – Valeurs', 'url' => '/missions-visions-valeurs', 'icon' => 'target'],
        ['label' => 'Nos offres immobilières', 'url' => '/nos-offres-immobilieres', 'icon' => 'key'],
        ['label' => 'Questions fréquentes', 'url' => '/faq', 'icon' => 'help-circle'],
    ]],
    ['label' => 'Nos offres', 'url' => '/logements', 'children' => [
        ['label' => 'Nos logements', 'url' => '/logements', 'icon' => 'home'],
        ['label' => 'Nos sites', 'url' => '/nos-sites', 'icon' => 'map-pin'],
    ]],
    ['label' => 'Souscription', 'url' => '/souscription-logement'],
    ['label' => 'Nos activités', 'url' => '/nos-activites'],
    ['label' => 'Actualités', 'url' => '/actualites'],
    ['label' => 'Contact', 'url' => '/contact'],
];
$isActive = static function (array $item): bool {
    if (is_active($item['url'])) {
        return true;
    }
    foreach ($item['children'] ?? [] as $c) {
        if (is_active($c['url'])) {
            return true;
        }
    }
    return false;
};
$socials = array_filter([
    'facebook' => setting('facebook_url'), 'youtube' => setting('youtube_url'), 'instagram' => setting('instagram_url'),
    'linkedin' => setting('linkedin_url'), 'tiktok' => setting('tiktok_url'),
]);
$GLOBALS['__nav'] = $nav;
$GLOBALS['__socials'] = $socials;
$announcement = setting('announcement');
?>
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-info">
            <a href="mailto:<?= e(setting('email')) ?>"><?= icon('mail') ?><span><?= e(setting('email')) ?></span></a>
            <span class="topbar-hide-sm"><?= icon('map-pin') ?><span><?= e(str_replace(', Burkina Faso', '', (string) setting('address'))) ?></span></span>
            <span class="topbar-hide-md"><?= icon('clock') ?><span><?= e(setting('opening_hours')) ?></span></span>
        </div>
        <?php if ($announcement): ?>
        <a class="topbar-announce" href="<?= url('/souscription-logement') ?>"><span class="pulse-dot"></span><span><?= e($announcement) ?></span></a>
        <?php endif; ?>
        <div class="topbar-right">
            <div class="social-links">
                <?php foreach ($socials as $k => $link): ?>
                <a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($k)) ?>"><?= icon('brand-' . $k) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="lang-switch notranslate" translate="no" role="group" aria-label="Langue du site">
                <?= icon('globe') ?>
                <button type="button" data-lang="fr" class="is-active" aria-label="Français">FR</button>
                <button type="button" data-lang="en" aria-label="English">EN</button>
                <button type="button" data-lang="it" aria-label="Italiano">IT</button>
            </div>
        </div>
    </div>
</div>

<header class="site-header<?= !empty($headerTransparent) ? ' is-transparent' : '' ?>" id="siteHeader">
    <div class="container header-inner">
        <a class="logo" href="<?= url('/') ?>" aria-label="GELPAZ IMMO — Accueil">
            <img class="logo-color" src="<?= url('assets/images/brand/logo-gelpaz.svg') ?>" alt="GELPAZ IMMO-SA" width="150" height="94">
            <img class="logo-white" src="<?= url('assets/images/brand/logo-gelpaz-white.svg') ?>" alt="" width="150" height="94" aria-hidden="true">
        </a>
        <nav class="main-nav" aria-label="Navigation principale">
            <ul>
                <?php foreach ($nav as $item): ?>
                <li class="<?= !empty($item['children']) ? 'has-sub' : '' ?><?= $isActive($item) ? ' is-active' : '' ?>">
                    <a href="<?= url($item['url']) ?>"><?= e($item['label']) ?><?= !empty($item['children']) ? icon('chevron-down', 'nav-caret') : '' ?></a>
                    <?php if (!empty($item['children'])): ?>
                    <ul class="sub-menu">
                        <?php foreach ($item['children'] as $c): ?>
                        <li><a href="<?= url($c['url']) ?>"><?= icon($c['icon']) ?><span><?= e($c['label']) ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="header-actions">
            <a class="header-call" href="<?= e(phone_href((string) setting('phone'))) ?>">
                <span class="header-call-icon"><?= icon('phone-call') ?></span>
                <span class="header-call-text"><small>Appelez-nous</small><strong class="notranslate" translate="no"><?= e(setting('phone')) ?></strong></span>
            </a>
            <a class="btn btn-primary btn-sm header-cta" href="<?= url('/souscription-logement') ?>"><span>Souscrire</span><?= icon('arrow-up-right') ?></a>
            <button class="menu-toggle" type="button" aria-label="Ouvrir le menu" aria-controls="offcanvas" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

<div class="offcanvas" id="offcanvas" aria-hidden="true">
    <div class="offcanvas-backdrop" data-close-menu></div>
    <aside class="offcanvas-panel" aria-label="Menu mobile">
        <div class="offcanvas-head">
            <a href="<?= url('/') ?>"><img src="<?= url('assets/images/brand/logo-gelpaz.svg') ?>" alt="GELPAZ IMMO-SA" width="120" height="75"></a>
            <button class="offcanvas-close" type="button" data-close-menu aria-label="Fermer le menu"><?= icon('x') ?></button>
        </div>
        <nav class="mobile-nav" aria-label="Navigation mobile">
            <ul>
                <?php foreach ($nav as $item): ?>
                <li class="<?= !empty($item['children']) ? 'has-sub' : '' ?><?= $isActive($item) ? ' is-active' : '' ?>">
                    <?php if (!empty($item['children'])): ?>
                    <button type="button" class="mobile-sub-toggle" aria-expanded="false"><?= e($item['label']) ?><?= icon('plus') ?></button>
                    <ul class="mobile-sub">
                        <?php foreach ($item['children'] as $c): ?>
                        <li><a href="<?= url($c['url']) ?>"><?= e($c['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <a href="<?= url($item['url']) ?>"><?= e($item['label']) ?></a>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <div class="offcanvas-contact">
            <a href="<?= e(phone_href((string) setting('phone'))) ?>"><?= icon('phone') ?><span class="notranslate" translate="no"><?= e(setting('phone')) ?></span></a>
            <a href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span class="notranslate" translate="no"><?= e(setting('phone_mobile')) ?></span></a>
            <a href="mailto:<?= e(setting('email')) ?>"><?= icon('mail') ?><span><?= e(setting('email')) ?></span></a>
            <span><?= icon('map-pin') ?><span><?= e(setting('address')) ?></span></span>
        </div>
        <a class="btn btn-primary btn-block" href="<?= url('/souscription-logement') ?>"><span>Souscrire à un logement</span><?= icon('arrow-up-right') ?></a>
        <div class="offcanvas-foot">
            <div class="social-links">
                <?php foreach ($socials as $k => $link): ?>
                <a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($k)) ?>"><?= icon('brand-' . $k) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="lang-switch notranslate" translate="no">
                <button type="button" data-lang="fr" class="is-active">FR</button>
                <button type="button" data-lang="en">EN</button>
                <button type="button" data-lang="it">IT</button>
            </div>
        </div>
    </aside>
</div>
