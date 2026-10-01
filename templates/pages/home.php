<?php
$headerTransparent = true;
$bodyClass = 'page-home';
$metaDescription = setting('seo_description');
$years = (int) setting('stat_years', 30);
$catIcons = ['f3-moyen-standing' => 'home', 'f4-moyen-standing' => 'building', 'f5-duplex-haut-standing' => 'building-2'];
$benefits = [
    ['icon' => 'award', 'title' => 'Plus de 30 ans d’expérience', 'text' => 'Agence fondée en 1987, devenue GELPAZ IMMO SA en 2009.'],
    ['icon' => 'shield-check', 'title' => 'Sécurité juridique', 'text' => 'Documents vérifiés et sécurisés à chaque étape de votre acquisition.'],
    ['icon' => 'handshake', 'title' => 'Un partenariat de confiance', 'text' => 'Nous travaillons directement avec vous, sans intermédiaire.'],
    ['icon' => 'gem', 'title' => 'La qualité avant tout', 'text' => 'Des villas bien conçues, durables et agréables à vivre.'],
    ['icon' => 'lightbulb', 'title' => 'Innovation constante', 'text' => 'Des sites intelligents et une approche durable de l’habitat.'],
    ['icon' => 'wallet', 'title' => 'Paiement flexible', 'text' => 'Au comptant ou avec un financement bancaire.'],
    ['icon' => 'users', 'title' => 'Écoute active', 'text' => 'Un accompagnement personnalisé, à l’écoute de vos besoins.'],
    ['icon' => 'trophy', 'title' => 'Entreprise primée', 'text' => 'Meilleure entreprise de promotion immobilière du Burkina Faso (2025).'],
    ['icon' => 'globe', 'title' => 'Proche de la diaspora', 'text' => 'Achetez à distance, nous vous tenons informé de tout.'],
    ['icon' => 'trees', 'title' => 'Engagement RSE', 'text' => 'Reboisement des cités et soutien aux initiatives locales.'],
];
?>

<!-- ============================== HERO ============================== -->
<section class="hero" aria-label="Présentation">
    <div class="swiper hero-slider">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $i => $s): ?>
            <div class="swiper-slide hero-slide">
                <div class="hero-bg" style="background-image:url('<?= e(media($s['image'])) ?>')"></div>
                <div class="hero-overlay"></div>
                <div class="container hero-content">
                    <?php if ($s['pre_title']): ?><span class="hero-pre"><?= bird() ?><?= e($s['pre_title']) ?></span><?php endif; ?>
                    <<?= $i === 0 ? 'h1' : 'h2' ?> class="hero-title"><?= e($s['title']) ?></<?= $i === 0 ? 'h1' : 'h2' ?>>
                    <?php if ($s['text']): ?><p class="hero-text"><?= e($s['text']) ?></p><?php endif; ?>
                    <div class="hero-btns">
                        <?php if ($s['button_text']): ?><a class="btn btn-primary btn-lg" href="<?= e(url($s['button_url'])) ?>"><span><?= e($s['button_text']) ?></span><?= icon('arrow-up-right') ?></a><?php endif; ?>
                        <?php if ($s['button2_text']): ?><a class="btn btn-outline-light btn-lg" href="<?= e(url($s['button2_url'])) ?>"><span><?= e($s['button2_text']) ?></span><?= icon('arrow-right') ?></a><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="hero-controls">
        <div class="container hero-controls-inner">
            <div class="hero-counter"><span class="hero-current">01</span><span class="hero-sep"></span><span class="hero-total"><?= str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) ?></span></div>
            <div class="hero-pagination"></div>
            <div class="hero-arrows">
                <button class="hero-prev" type="button" aria-label="Diapositive précédente"><?= icon('arrow-left') ?></button>
                <button class="hero-next" type="button" aria-label="Diapositive suivante"><?= icon('arrow-right') ?></button>
            </div>
        </div>
    </div>
    <div class="hero-outline" aria-hidden="true">
        <div class="hero-outline-track notranslate" translate="no">
            <?php for ($r = 0; $r < 4; $r++): ?><span>GELPAZ IMMO</span><span class="dot"><?= bird() ?></span><span>La différence</span><span class="dot"><?= bird() ?></span><?php endfor; ?>
        </div>
    </div>
</section>

<!-- ========================= RECHERCHE RAPIDE ========================= -->
<section class="search-wrap" aria-label="Rechercher un logement">
    <div class="container">
        <form class="search-panel" action="<?= url('/logements') ?>" method="get" data-reveal="up">
            <div class="search-field">
                <label for="hs-cat"><?= icon('home') ?>Type de villa</label>
                <select id="hs-cat" name="categorie">
                    <option value="">Toutes les gammes</option>
                    <?php foreach ($categories as $c): ?><option value="<?= e($c['slug']) ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="search-field">
                <label for="hs-type"><?= icon('key') ?>Transaction</label>
                <select id="hs-type" name="type">
                    <option value="">Vente ou location</option>
                    <option value="vente">À vendre</option>
                    <option value="location">À louer</option>
                </select>
            </div>
            <div class="search-field">
                <label for="hs-site"><?= icon('map-pin') ?>Site</label>
                <select id="hs-site" name="site">
                    <option value="">Tous les sites</option>
                    <?php foreach ($searchSites as $s): if ((int) $s['total'] === 0) { continue; } ?><option value="<?= e($s['slug']) ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="search-field">
                <label for="hs-bed"><?= icon('bed') ?>Chambres</label>
                <select id="hs-bed" name="chambres">
                    <option value="">Indifférent</option>
                    <option value="2">2 et plus</option>
                    <option value="3">3 et plus</option>
                    <option value="4">4 et plus</option>
                </select>
            </div>
            <button class="btn btn-primary search-submit" type="submit"><?= icon('search') ?><span>Rechercher</span></button>
        </form>
    </div>
</section>

<!-- ============================ CATÉGORIES ============================ -->
<section class="section categories-section">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Nos gammes</span>
                <h2 class="sec-title" data-split>Explorez nos villas par catégorie</h2>
            </div>
            <div class="slider-arrows">
                <button class="slider-arrow cat-prev" type="button" aria-label="Précédent"><?= icon('arrow-left') ?></button>
                <button class="slider-arrow cat-next" type="button" aria-label="Suivant"><?= icon('arrow-right') ?></button>
            </div>
        </div>
        <div class="swiper categories-slider" data-reveal="up">
            <div class="swiper-wrapper">
                <?php foreach ($categories as $i => $c): ?>
                <div class="swiper-slide">
                    <a class="category-card" href="<?= url('/logements?categorie=' . $c['slug']) ?>">
                        <span class="category-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="category-icon"><?= icon($c['icon'] ?: ($catIcons[$c['slug']] ?? 'home')) ?></span>
                        <h3><?= e($c['name']) ?></h3>
                        <p><?= e($c['description']) ?></p>
                        <span class="category-count"><?= plural((int) $c['total'], 'logement') ?><?= icon('arrow-up-right') ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
                <div class="swiper-slide">
                    <a class="category-card" href="<?= url('/logements?type=location') ?>">
                        <span class="category-num"><?= str_pad((string) (count($categories) + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="category-icon"><?= icon('key') ?></span>
                        <h3>Logements à louer</h3>
                        <p>Des villas disponibles à la location, prêtes à vous accueillir.</p>
                        <span class="category-count"><?= plural($rentCount, 'logement') ?><?= icon('arrow-up-right') ?></span>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a class="category-card category-card-accent" href="<?= url('/nos-activites/projets-personnalises') ?>">
                        <span class="category-num"><?= str_pad((string) (count($categories) + 2), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="category-icon"><?= icon('pencil-ruler') ?></span>
                        <h3>Projet sur mesure</h3>
                        <p>Apportez votre plan ou laissez-nous concevoir le vôtre.</p>
                        <span class="category-count">Nous consulter<?= icon('arrow-up-right') ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================== À PROPOS ============================== -->
<section class="section about-home">
    <div class="container">
        <div class="about-home-grid">
            <div class="about-home-media" data-reveal="left">
                <div class="about-img about-img-main"><?= img(url('assets/images/about/about-1.jpg'), 'Une famille devant sa nouvelle villa GELPAZ') ?></div>
                <div class="about-img about-img-small"><?= img(url('assets/images/about/about-3.jpg'), 'Salon lumineux d’une villa moderne') ?></div>
                <div class="experience-badge">
                    <strong><span data-count="<?= $years ?>">0</span>+</strong>
                    <span>années d’expérience</span>
                </div>
                <div class="about-rotating" aria-hidden="true">
                    <svg viewBox="0 0 200 200"><defs><path id="circlePathHome" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs><text><textPath href="#circlePathHome" startOffset="0">GELPAZ IMMO • LA DIFFÉRENCE • DEPUIS 1987 •</textPath></text></svg>
                    <span class="about-rotating-center"><?= bird() ?></span>
                </div>
            </div>
            <div class="about-home-content" data-reveal="right">
                <span class="vertical-label" aria-hidden="true">À propos de nous</span>
                <span class="sub-title"><?= bird() ?>Qui sommes-nous ?</span>
                <h2 class="sec-title" data-split>Bâtir l’accès à un logement décent, depuis 1987</h2>
                <p class="sec-text">GELPAZ IMMO SA est une société de promotion et de vente immobilière créée en 2009, héritière de l’agence GELPAZ SARL fondée en 1987 par feu M. Z. Alain ZOUNGRANA, premier expert immobilier diplômé d’État du Burkina Faso.</p>
                <ul class="check-list">
                    <li><?= icon('check') ?>Vente et location de villas haut et moyen standing</li>
                    <li><?= icon('check') ?>Expertise reconnue en bâtiment et travaux publics</li>
                    <li><?= icon('check') ?>Gestion de patrimoines immobiliers</li>
                    <li><?= icon('check') ?>Accompagnement personnalisé, de la souscription aux clés</li>
                </ul>
                <div class="about-stats">
                    <div class="stat"><strong><span data-count="<?= (int) setting('stat_sites', 14) ?>">0</span></strong><span>sites et cités</span></div>
                    <div class="stat"><strong><span data-count="<?= (int) setting('stat_ranges', 3) ?>">0</span></strong><span>gammes de villas</span></div>
                    <div class="stat"><strong><span data-count="<?= (int) setting('stat_regions', 17) ?>">0</span></strong><span>régions ciblées</span></div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-dark" href="<?= url('/a-propos') ?>"><span>En savoir plus</span><?= icon('arrow-up-right') ?></a>
                    <a class="call-box" href="<?= e(phone_href((string) setting('phone_mobile'))) ?>">
                        <span class="call-box-icon"><?= icon('phone-call') ?></span>
                        <span><small>Conseil gratuit</small><strong class="notranslate" translate="no"><?= e(setting('phone_mobile')) ?></strong></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================ LOGEMENTS ============================ -->
<section class="section properties-home bg-smoke">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Nos logements</span>
                <h2 class="sec-title" data-split>Des villas pensées pour votre confort</h2>
            </div>
            <div class="filter-tabs" role="tablist" aria-label="Filtrer les logements">
                <button class="filter-tab is-active" type="button" data-filter="*" role="tab" aria-selected="true">Tous</button>
                <?php foreach ($categories as $c): if (!(int) $c['total']) { continue; } ?>
                <button class="filter-tab" type="button" data-filter="<?= e($c['slug']) ?>" role="tab" aria-selected="false"><?= e(preg_replace('/^Villa\s+/i', '', $c['name'])) ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="property-grid filterable" data-reveal="up">
            <?php foreach ($featured as $p): partial('property-card', ['p' => $p]); endforeach; ?>
        </div>
        <div class="section-foot">
            <a class="btn btn-primary" href="<?= url('/logements') ?>"><span>Voir tous nos logements</span><?= icon('arrow-up-right') ?></a>
        </div>
    </div>
</section>

<!-- ======================= POURQUOI NOUS CHOISIR ======================= -->
<section class="section why-section">
    <div class="why-bg-bird" aria-hidden="true"><?= bird() ?></div>
    <div class="container">
        <div class="why-grid">
            <div class="why-content" data-reveal="left">
                <span class="sub-title sub-title-light"><?= bird() ?>Pourquoi nous choisir ?</span>
                <h2 class="sec-title sec-title-light" data-split>Obtenez l’offre qui fait la différence</h2>
                <p class="sec-text">Notre différence : travailler directement avec nos clients pour bâtir une relation durable, fondée sur la confiance, la qualité et une perspective d’innovation constante.</p>
                <div class="why-media">
                    <?= img(url('assets/images/hero/hero-3.jpg'), 'Villa duplex avec piscine') ?>
                    <?php if (setting('youtube_url')): ?>
                    <button class="play-btn" type="button" data-video="<?= e(setting('youtube_url')) ?>" aria-label="Voir notre vidéo de présentation">
                        <span class="play-ring"></span><?= icon('play') ?>
                    </button>
                    <span class="why-media-label">Découvrez GELPAZ IMMO en vidéo</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="why-columns" data-reveal="right">
                <?php foreach ([array_slice($benefits, 0, 5), array_slice($benefits, 5)] as $col => $items): ?>
                <div class="why-col why-col-<?= $col ? 'down' : 'up' ?>">
                    <div class="why-track">
                        <?php for ($r = 0; $r < 2; $r++): foreach ($items as $b): ?>
                        <div class="benefit-card" <?= $r ? 'aria-hidden="true"' : '' ?>>
                            <span class="benefit-icon"><?= icon($b['icon']) ?></span>
                            <h3><?= e($b['title']) ?></h3>
                            <p><?= e($b['text']) ?></p>
                        </div>
                        <?php endforeach; endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================== SERVICES ============================== -->
<section class="section services-home">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Nos activités</span>
            <h2 class="sec-title" data-split>Des solutions immobilières complètes</h2>
            <p class="sec-text">Particuliers, entreprises, institutions et membres de la diaspora : nous vous accompagnons de la conception à la remise des clés.</p>
        </div>
        <div class="service-panels" data-reveal="up">
            <?php foreach (array_slice($services, 0, 5) as $i => $s): ?>
            <a class="service-panel<?= $i === 0 ? ' is-active' : '' ?>" href="<?= url('/nos-activites/' . $s['slug']) ?>">
                <span class="service-panel-bg" style="background-image:url('<?= e(media($s['image'])) ?>')"></span>
                <span class="service-panel-shade"></span>
                <span class="service-panel-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <span class="service-panel-body">
                    <span class="service-panel-icon"><?= icon($s['icon'] ?: 'building') ?></span>
                    <span class="service-panel-title"><?= e($s['title']) ?></span>
                    <span class="service-panel-text"><?= e($s['excerpt']) ?></span>
                    <span class="service-panel-link">Découvrir<?= icon('arrow-up-right') ?></span>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======================= COMMENT ACHETER (3 ÉTAPES) ======================= -->
<section class="section process-section bg-ivory">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Simple et transparent</span>
            <h2 class="sec-title" data-split>Comment acheter ou louer avec GELPAZ IMMO</h2>
        </div>
        <div class="process-grid">
            <div class="process-card" data-reveal="up">
                <span class="process-num">01</span>
                <span class="process-icon"><?= icon('search') ?></span>
                <h3>Choix de la propriété</h3>
                <p>Parcourez nos offres et trouvez la villa qui vous correspond, en ligne ou lors d’une visite.</p>
                <a class="link-arrow" href="<?= url('/logements') ?>"><span>Voir les logements</span><?= icon('arrow-right') ?></a>
            </div>
            <div class="process-card" data-reveal="up" data-delay="120">
                <span class="process-num">02</span>
                <span class="process-icon"><?= icon('calendar-check') ?></span>
                <h3>Réservation</h3>
                <p>Contactez notre service commercial par e-mail à <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a> ou au <span class="notranslate" translate="no"><?= e(setting('phone_mobile')) ?></span> (appel ou WhatsApp).</p>
                <a class="link-arrow" href="<?= url('/souscription-logement') ?>"><span>Réserver en ligne</span><?= icon('arrow-right') ?></a>
            </div>
            <div class="process-card" data-reveal="up" data-delay="240">
                <span class="process-num">03</span>
                <span class="process-icon"><?= icon('key') ?></span>
                <h3>Finalisation</h3>
                <p>Nous vous recontactons pour fixer le rendez-vous de validation de votre acquisition. Bienvenue chez vous !</p>
                <a class="link-arrow" href="<?= url('/contact') ?>"><span>Nous contacter</span><?= icon('arrow-right') ?></a>
            </div>
        </div>
    </div>
</section>

<!-- ======================= NOS SITES & PARTENAIRES ======================= -->
<section class="section sites-home">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Nos sites</span>
                <h2 class="sec-title" data-split>Présents à travers le Burkina Faso</h2>
            </div>
            <a class="btn btn-outline" href="<?= url('/nos-sites') ?>"><span>Tous nos sites</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="sites-grid" data-reveal="up">
            <?php foreach (array_slice($sites, 0, 5) as $i => $s): ?>
            <a class="site-card<?= $i === 0 ? ' site-card-lg' : '' ?>" href="<?= url('/nos-sites#' . $s['slug']) ?>">
                <?php $simg = media($s['image'], $s['image_remote'], null); ?>
                <span class="site-card-media<?= $simg ? '' : ' site-card-pattern' ?>">
                    <?php if ($simg): ?><?= img($simg, $s['name']) ?><?php else: ?><span class="site-pattern-pin"><?= icon('map-pin') ?></span><?php endif; ?>
                </span>
                <span class="site-card-body">
                    <span class="site-badge"><?= e(site_status_label($s['status'])) ?></span>
                    <span class="site-card-title"><?= e($s['name']) ?></span>
                    <span class="site-card-loc"><?= icon('map-pin') ?><?= e(trim(($s['city'] ?: '') . ($s['area'] && $s['area'] !== $s['city'] ? ' · ' . $s['area'] : ''), ' ·') ?: 'Burkina Faso') ?></span>
                    <?php if ((int) $s['total']): ?><span class="site-card-count"><?= plural((int) $s['total'], 'logement disponible', 'logements disponibles') ?></span><?php endif; ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="partners-block">
        <div class="container">
            <div class="partners-head">
                <span class="sub-title"><?= bird() ?>Nos partenaires</span>
                <p>Nous travaillons en étroite collaboration avec des professionnels de l’immobilier, des architectes, des banques et d’autres acteurs clés du secteur.</p>
            </div>
        </div>
        <?php partial('partners', ['partners' => $partners]); ?>
    </div>
</section>

<?php partial('testimonials', ['testimonials' => $testimonials]); ?>

<?php partial('contact-section'); ?>

<?php partial('marquee', ['variant' => 'navy']); ?>

<!-- ============================ ACTUALITÉS ============================ -->
<section class="section news-home">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Actualités</span>
                <h2 class="sec-title" data-split>Les dernières nouvelles de GELPAZ IMMO</h2>
            </div>
            <div class="section-head-actions">
                <div class="slider-arrows">
                    <button class="slider-arrow news-prev" type="button" aria-label="Précédent"><?= icon('arrow-left') ?></button>
                    <button class="slider-arrow news-next" type="button" aria-label="Suivant"><?= icon('arrow-right') ?></button>
                </div>
                <a class="btn btn-outline" href="<?= url('/actualites') ?>"><span>Toutes les actualités</span><?= icon('arrow-up-right') ?></a>
            </div>
        </div>
        <div class="swiper news-slider" data-reveal="up">
            <div class="swiper-wrapper">
                <?php foreach ($posts as $post): ?>
                <div class="swiper-slide"><?php partial('post-card', ['post' => $post]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
