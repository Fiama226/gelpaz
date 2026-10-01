<?php
/** @var string $content */
$siteName = setting('site_name', 'GELPAZ IMMO');
$pageTitle = $pageTitle ?? null;
$fullTitle = $pageTitle ? $pageTitle . ' — ' . $siteName : setting('seo_title', $siteName . ' — La différence !');
$metaDescription = $metaDescription ?? setting('seo_description');
$canonical = $canonical ?? absolute_url(current_path());
$ogImage = $ogImage ?? absolute_url('assets/images/brand/og-image.jpg');
if (!preg_match('#^https?://#', $ogImage)) {
    $ogImage = absolute_url($ogImage);
}
$bodyClass = $bodyClass ?? '';
$needsSwiper = $needsSwiper ?? true;
$headerTransparent = $headerTransparent ?? false;
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'RealEstateAgent',
    'name' => setting('company_name', 'GELPAZ IMMO SA'),
    'url' => absolute_url('/'),
    'logo' => absolute_url('assets/images/brand/logo-gelpaz.svg'),
    'image' => absolute_url('assets/images/brand/og-image.jpg'),
    'telephone' => setting('phone'),
    'email' => setting('email'),
    'foundingDate' => '1987',
    'slogan' => setting('tagline', 'La différence !'),
    'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Dagnoën, rue 29.128', 'addressLocality' => 'Ouagadougou', 'addressCountry' => 'BF'],
    'openingHours' => 'Mo-Fr 08:00-13:00, Mo-Fr 14:00-17:00',
    'sameAs' => array_values(array_filter([setting('facebook_url'), setting('youtube_url'), setting('instagram_url'), setting('linkedin_url')])),
];
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e(str_limit((string) $metaDescription, 160)) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#0A1B33">
<meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:locale" content="fr_FR">
<meta property="og:title" content="<?= e($pageTitle ?? $fullTitle) ?>">
<meta property="og:description" content="<?= e(str_limit((string) $metaDescription, 200)) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="<?= url('assets/images/brand/favicon.svg') ?>" type="image/svg+xml">
<link rel="icon" href="<?= url('assets/images/brand/favicon-32.png') ?>" sizes="32x32" type="image/png">
<link rel="apple-touch-icon" href="<?= url('assets/images/brand/apple-touch-icon.png') ?>">
<link rel="manifest" href="<?= url('site.webmanifest') ?>">
<link rel="preload" href="<?= url('assets/fonts/plus-jakarta-sans-latin-700-normal.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?= url('assets/fonts/inter-latin-400-normal.woff2') ?>" as="font" type="font/woff2" crossorigin>
<?php if ($needsSwiper): ?><link rel="stylesheet" href="<?= asset('vendor/swiper/swiper-bundle.min.css') ?>"><?php endif; ?>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<script>document.documentElement.className+=" js";document.addEventListener("error",function(e){var t=e.target;if(t&&t.tagName==="IMG"&&t.getAttribute("data-fallback")&&!t.getAttribute("data-failed")){t.setAttribute("data-failed","1");t.src=t.getAttribute("data-fallback");}},true);</script>
<script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if (!empty($extraSchema)): ?><script type="application/ld+json"><?= json_encode($extraSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script><?php endif; ?>
</head>
<body class="<?= e(trim($bodyClass . ($headerTransparent ? ' has-transparent-header' : ''))) ?>">
<a class="skip-link" href="#contenu">Aller au contenu</a>

<div class="preloader" id="preloader" aria-hidden="true">
    <div class="preloader-inner">
        <?= bird('preloader-bird') ?>
        <div class="preloader-word notranslate" translate="no"><span>G</span><span>E</span><span>L</span><span>P</span><span>A</span><span>Z</span></div>
        <div class="preloader-bar"><span></span></div>
    </div>
</div>

<?php partial('header', ['headerTransparent' => $headerTransparent]); ?>

<main id="contenu">
<?= $content ?>
</main>

<?php partial('footer'); ?>

<a class="whatsapp-float" href="<?= e(whatsapp_url('Bonjour GELPAZ IMMO, je souhaite avoir des informations sur vos logements.')) ?>" target="_blank" rel="noopener" aria-label="Discuter sur WhatsApp">
    <?= icon('brand-whatsapp') ?>
    <span class="whatsapp-float-label">Discutons sur WhatsApp</span>
</a>
<button class="back-to-top" id="backToTop" type="button" aria-label="Revenir en haut de la page">
    <svg class="progress-ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22"/></svg>
    <?= icon('arrow-up') ?>
</button>

<div class="lightbox" id="lightbox" hidden>
    <button class="lightbox-close" type="button" aria-label="Fermer"><?= icon('x') ?></button>
    <button class="lightbox-prev" type="button" aria-label="Image précédente"><?= icon('chevron-left') ?></button>
    <figure class="lightbox-figure"><img src="" alt=""><figcaption></figcaption></figure>
    <button class="lightbox-next" type="button" aria-label="Image suivante"><?= icon('chevron-right') ?></button>
    <div class="lightbox-counter"></div>
</div>

<div class="video-modal" id="videoModal" hidden>
    <div class="video-modal-inner">
        <button class="video-modal-close" type="button" aria-label="Fermer la vidéo"><?= icon('x') ?></button>
        <div class="video-frame"></div>
    </div>
</div>

<div id="google_translate_element" hidden></div>
<script>window.GELPAZ = {base: <?= json_encode(base_path()) ?>, placeholder: <?= json_encode(url('assets/images/placeholder.svg')) ?>};</script>
<?php if ($needsSwiper): ?><script src="<?= asset('vendor/swiper/swiper-bundle.min.js') ?>" defer></script><?php endif; ?>
<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
