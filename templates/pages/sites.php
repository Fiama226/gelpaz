<?php
$pageTitle = 'Nos sites';
$metaDescription = 'Cité de l’Intégration, Cité de l’Espoir, Sabtoana, Pô, Garghin, Saaba, Kaya… Découvrez les sites et cités GELPAZ IMMO au Burkina Faso.';
partial('breadcrumb', ['title' => 'Nos sites', 'crumbs' => [['Nos offres', null], ['Nos sites', null]], 'image' => 'assets/images/hero/hero-2.jpg']);
$main = $featured[0] ?? null;
?>
<section class="section">
    <div class="container">
        <div class="split-grid">
            <div data-reveal="left">
                <span class="sub-title"><?= bird() ?>Nos implantations</span>
                <h2 class="sec-title" data-split>Des cités modernes à travers le Burkina Faso</h2>
                <p class="sec-text">Notre ambition : être présents dans les 17 régions du pays et permettre à chacun, y compris à la diaspora, d’accéder à un logement décent dans sa province d’origine.</p>
                <div class="about-stats">
                    <div class="stat"><strong><span data-count="<?= count($featured) + count($others) ?>">0</span></strong><span>sites au Burkina</span></div>
                    <div class="stat"><strong><span data-count="<?= count($categories) ?>">0</span></strong><span>gammes de villas</span></div>
                    <div class="stat"><strong><span data-count="17">0</span></strong><span>régions ciblées</span></div>
                </div>
                <a class="btn btn-primary" href="<?= url('/souscription-logement') ?>"><span>Souscrire à une villa</span><?= icon('arrow-up-right') ?></a>
            </div>
            <?php if ($main): ?>
            <div class="map-frame" style="aspect-ratio:4/3.4;border-radius:28px" data-reveal="right">
                <iframe title="Carte : <?= e($main['name']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=<?= rawurlencode($main['map_query'] ?: 'Ouaga 2000, Ouagadougou') ?>&z=13&output=embed"></iframe>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section bg-smoke">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Nos cités</span>
                <h2 class="sec-title" data-split>Nos principaux sites</h2>
            </div>
            <a class="btn btn-outline" href="<?= url('/logements') ?>"><span>Voir les logements</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="site-list-grid">
            <?php foreach ($featured as $i => $s): $simg = media($s['image'], $s['image_remote'], null); ?>
            <article class="site-item" id="<?= e($s['slug']) ?>" data-reveal="up" data-delay="<?= ($i % 2) * 100 ?>">
                <div class="site-item-media<?= $simg ? '' : ' site-card-pattern' ?>">
                    <?php if ($simg): ?><?= img($simg, $s['name']) ?><?php else: ?><span class="site-pattern-pin"><?= icon('map-pin') ?></span><?php endif; ?>
                </div>
                <div class="site-item-body">
                    <span class="site-badge"><?= e(site_status_label($s['status'])) ?></span>
                    <h3><?= e($s['name']) ?></h3>
                    <span class="site-item-loc"><?= icon('map-pin') ?><?= e(trim(($s['city'] ?: '') . ($s['area'] && $s['area'] !== $s['city'] ? ' · ' . $s['area'] : ''), ' ·') ?: 'Burkina Faso') ?></span>
                    <?php if ($s['description']): ?><p><?= e($s['description']) ?></p><?php endif; ?>
                    <div class="site-item-actions">
                        <?php if ((int) $s['total']): ?><a class="link-arrow" href="<?= url('/logements?site=' . $s['slug']) ?>"><span><?= plural((int) $s['total'], 'logement') ?></span><?= icon('arrow-right') ?></a><?php endif; ?>
                        <?php if ($s['map_query']): ?><a class="link-arrow" href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($s['map_query']) ?>" target="_blank" rel="noopener"><span>Itinéraire</span><?= icon('navigation') ?></a><?php endif; ?>
                        <a class="link-arrow" href="<?= url('/souscription-logement') ?>"><span>Souscrire</span><?= icon('arrow-up-right') ?></a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($others): ?>
<section class="section">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Et aussi</span>
            <h2 class="sec-title" data-split>Nos autres sites</h2>
        </div>
        <div class="other-sites">
            <?php foreach ($others as $i => $s): ?>
            <div class="other-site" id="<?= e($s['slug']) ?>" data-reveal="up" data-delay="<?= ($i % 3) * 80 ?>">
                <span class="feature-icon"><?= icon('map-pinned') ?></span>
                <div><strong><?= e($s['name']) ?></strong><span><?= e($s['city'] ?: site_status_label($s['status'])) ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php foreach ($international as $s): ?>
<section class="section-sm" style="padding-bottom:120px">
    <div class="container">
        <div class="intl-card" id="<?= e($s['slug']) ?>" data-reveal="up">
            <div>
                <span class="sub-title sub-title-light"><?= bird() ?><?= e(site_status_label($s['status'])) ?></span>
                <h2><?= e($s['name']) ?></h2>
                <p><?= e($s['description']) ?></p>
                <a class="btn btn-gold" href="<?= url('/actualites/signature-du-pv-de-validation-des-projets-de-developpement-urbain') ?>"><span>Lire l’actualité</span><?= icon('arrow-up-right') ?></a>
            </div>
            <div class="intl-sites">
                <div class="intl-site"><strong>Site de Gredia</strong><span>20,13 ha</span></div>
                <div class="intl-site"><strong>Site de Guilmey</strong><span>11 ha</span></div>
                <div class="intl-site"><strong>Site de Gaoui</strong><span>1,81 ha</span></div>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>
