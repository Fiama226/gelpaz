<?php
/** @var array $p */
$pageTitle = $p['title'];
$metaDescription = $p['excerpt'] ?: excerpt($p['description'], 160);
$gallery = property_gallery($p);
$ogImage = $gallery[0]['full'];
$ogType = 'article';
$url = absolute_url('/logements/' . $p['slug']);
$area = $p['land_area'] ? format_area($p['land_area']) : '';
$built = $p['built_area'] ? format_area($p['built_area']) : '';
$features = array_filter(array_map('trim', explode("\n", (string) $p['features'])));
$waText = 'Bonjour GELPAZ IMMO, je suis intéressé(e) par le logement « ' . $p['title'] . ' » (réf. ' . $p['reference'] . ') : ' . $url;
$mapQuery = $p['map_query'] ?: ($site['map_query'] ?? '') ?: ($p['address'] ?: $p['city'] . ', Burkina Faso');
$extraSchema = [
    '@context' => 'https://schema.org', '@type' => 'Residence', 'name' => $p['title'], 'description' => $metaDescription,
    'image' => array_slice(array_column($gallery, 'full'), 0, 5), 'url' => $url,
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => $p['city'], 'addressCountry' => 'BF'],
];
$overview = array_filter([
    ['home', 'Type', preg_replace('/^Villa\s+/i', '', (string) ($p['category_name'] ?? '—'))],
    $p['rooms'] ? ['layers', 'Pièces', $p['rooms']] : null,
    $p['bedrooms'] ? ['bed', 'Chambres', $p['bedrooms']] : null,
    $p['bathrooms'] ? ['bath', 'Salles d’eau', $p['bathrooms']] : null,
    $built ? ['ruler', 'Surface bâtie', $built] : null,
    $area ? ['area', 'Parcelle', $area] : null,
    $p['terraces'] ? ['sun', 'Terrasses', $p['terraces']] : null,
    $p['floors'] ? ['stairs', 'Niveaux', $p['floors']] : null,
    ['badge-check', 'Statut', property_status_label($p['status'])],
]);
$specRows = array_filter([
    'Référence' => $p['reference'], 'Catégorie' => $p['category_name'] ?? '', 'Transaction' => transaction_label($p['transaction']),
    'Statut' => property_status_label($p['status']), 'Surface bâtie' => $built, 'Superficie de la parcelle' => $area,
    'Pièces' => $p['rooms'], 'Chambres' => $p['bedrooms'], 'Salles d’eau' => $p['bathrooms'], 'Salons / séjours' => $p['living_rooms'],
    'Cuisines' => $p['kitchens'], 'Terrasses' => $p['terraces'], 'Garages' => $p['garages'], 'Niveaux' => $p['floors'],
    'Année de construction' => $p['year_built'], 'Site' => $p['site_name'] ?? '', 'Ville' => $p['city'],
], static fn($v) => $v !== null && $v !== '' && $v !== 0);
$specChunks = array_chunk($specRows, (int) ceil(count($specRows) / 2), true);
partial('breadcrumb', ['title' => 'Détails du logement', 'tag' => 'div', 'crumbs' => [['Nos logements', '/logements'], [$p['title'], null]], 'image' => 'assets/images/hero/hero-3.jpg']);
?>
<section class="section" style="padding-top:70px">
    <div class="container">
        <div class="property-header">
            <div>
                <div class="property-header-badges">
                    <span class="badge badge-<?= $p['transaction'] === 'location' ? 'gold' : 'primary' ?>"><?= e(transaction_label($p['transaction'])) ?></span>
                    <?php if (!empty($p['category_name'])): ?><span class="badge badge-light"><?= e($p['category_name']) ?></span><?php endif; ?>
                    <span class="badge badge-success"><?= e(property_status_label($p['status'])) ?></span>
                    <?php if ($p['reference']): ?><span class="badge badge-light notranslate" translate="no">Réf. <?= e($p['reference']) ?></span><?php endif; ?>
                </div>
                <h1><?= e($p['title']) ?></h1>
                <p class="property-header-loc"><?= icon('map-pin') ?><?= e($p['address'] ?: $p['city']) ?></p>
            </div>
            <div class="property-header-side">
                <small>Prix</small>
                <strong>Sur demande</strong>
                <div class="property-actions">
                    <button class="icon-btn" type="button" data-fav="<?= e($p['slug']) ?>" aria-label="Ajouter aux favoris"><?= icon('heart') ?></button>
                    <button class="icon-btn" type="button" data-copy="<?= e($url) ?>" aria-label="Copier le lien"><?= icon('link') ?></button>
                    <button class="icon-btn" type="button" data-native-share aria-label="Partager"><?= icon('share') ?></button>
                    <button class="icon-btn" type="button" data-print aria-label="Imprimer la fiche"><?= icon('printer') ?></button>
                </div>
            </div>
        </div>

        <div class="gallery-main">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($gallery as $i => $g): ?>
                    <div class="swiper-slide">
                        <a href="<?= e($g['full']) ?>" data-lightbox="property" data-caption="<?= e($g['caption']) ?>" aria-label="Agrandir la photo <?= $i + 1 ?>">
                            <?= img($g['full'], $g['caption'] . ' — photo ' . ($i + 1), '', ['loading' => $i === 0 ? 'eager' : 'lazy', 'fetchpriority' => $i === 0 ? 'high' : null]) ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (count($gallery) > 1): ?>
            <button class="gallery-arrow gallery-prev" type="button" aria-label="Photo précédente"><?= icon('arrow-left') ?></button>
            <button class="gallery-arrow gallery-next" type="button" aria-label="Photo suivante"><?= icon('arrow-right') ?></button>
            <?php endif; ?>
            <span class="gallery-count"><?= icon('images') ?><span class="gallery-current">1</span> / <?= count($gallery) ?></span>
        </div>
        <?php if (count($gallery) > 1): ?>
        <div class="swiper gallery-thumbs">
            <div class="swiper-wrapper">
                <?php foreach ($gallery as $i => $g): ?><div class="swiper-slide"><?= img($g['thumb'], 'Miniature ' . ($i + 1)) ?></div><?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="details-layout">
            <div class="details-main">
                <div class="details-block" data-reveal="up">
                    <h2>Aperçu</h2>
                    <div class="overview-grid">
                        <?php foreach ($overview as [$ic, $label, $val]): ?>
                        <div class="overview-item"><span class="icon-box"><?= icon($ic) ?></span><div><small><?= e($label) ?></small><strong><?= e((string) $val) ?></strong></div></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="details-block" data-reveal="up">
                    <h2>Description</h2>
                    <div class="prose"><?= sanitize_html($p['description']) ?: '<p>' . e($p['excerpt']) . '</p>' ?></div>
                </div>

                <div class="details-block" data-reveal="up">
                    <h2>Caractéristiques</h2>
                    <div class="specs-cols">
                        <?php foreach ($specChunks as $chunk): ?>
                        <table class="specs-table"><tbody>
                            <?php foreach ($chunk as $k => $v): ?><tr><th scope="row"><?= e($k) ?></th><td><?= e((string) $v) ?></td></tr><?php endforeach; ?>
                        </tbody></table>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($features): ?>
                <div class="details-block" data-reveal="up">
                    <h2>Équipements et prestations</h2>
                    <ul class="amenities">
                        <?php foreach ($features as $f): ?><li><?= icon('check') ?><?= e($f) ?></li><?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (count($gallery) > 1): ?>
                <div class="details-block" data-reveal="up">
                    <h2>Galerie photos</h2>
                    <div class="gallery-grid">
                        <?php foreach (array_slice($gallery, 0, 6) as $i => $g): ?>
                        <a href="<?= e($g['full']) ?>" data-lightbox="property" data-caption="<?= e($g['caption']) ?>">
                            <?= img($g['thumb'], $g['caption'] . ' — photo ' . ($i + 1)) ?>
                            <?php if ($i === 5 && count($gallery) > 6): ?><span class="gallery-more">+<?= count($gallery) - 6 ?></span><?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($p['plans'])): ?>
                <div class="details-block" data-reveal="up">
                    <h2>Plans</h2>
                    <div class="plan-tabs">
                        <?php foreach ($p['plans'] as $i => $pl): ?><button class="plan-tab<?= $i === 0 ? ' is-active' : '' ?>" type="button"><?= e($pl['title']) ?></button><?php endforeach; ?>
                    </div>
                    <?php foreach ($p['plans'] as $i => $pl): $src = media($pl['image'], $pl['image_remote'], null); ?>
                    <div class="plan-pane<?= $i === 0 ? ' is-active' : '' ?>">
                        <?php if ($src): ?><a href="<?= e($src) ?>" data-lightbox="plans" data-caption="<?= e($pl['title']) ?>"><?= img($src, 'Plan — ' . $pl['title']) ?></a><?php endif; ?>
                        <?php if ($pl['description']): ?><p><?= nl2br(e($pl['description'])) ?></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($p['video_url']): ?>
                <div class="details-block" data-reveal="up">
                    <h2>Vidéo</h2>
                    <div class="why-media" style="margin:0">
                        <?= img(property_cover($p, false), $p['title']) ?>
                        <button class="play-btn" type="button" data-video="<?= e($p['video_url']) ?>" aria-label="Lire la vidéo"><span class="play-ring"></span><?= icon('play') ?></button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="details-block" data-reveal="up">
                    <h2>Localisation</h2>
                    <div class="map-frame">
                        <iframe title="Carte : <?= e($mapQuery) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=<?= rawurlencode($mapQuery) ?>&z=14&output=embed"></iframe>
                    </div>
                    <p class="map-address"><?= icon('map-pin') ?><?= e($p['address'] ?: $p['city']) ?><?php if ($site): ?> — <a href="<?= url('/nos-sites#' . $site['slug']) ?>">Voir le site</a><?php endif; ?></p>
                </div>
            </div>

            <aside class="sticky-side" aria-label="Contacter GELPAZ IMMO au sujet de ce logement">
                <div class="side-card price-card">
                    <small>Prix de vente</small>
                    <strong>Sur demande</strong>
                    <p>Recevez une offre personnalisée selon le modèle, les options et votre mode de paiement (comptant ou financement bancaire).</p>
                    <a class="btn btn-gold" href="<?= e(phone_href((string) setting('phone_mobile'))) ?>"><?= icon('phone') ?><span class="notranslate" translate="no"><?= e(setting('phone_mobile')) ?></span></a>
                    <a class="btn btn-whatsapp" href="<?= e(whatsapp_url($waText)) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>Écrire sur WhatsApp</span></a>
                </div>

                <div class="side-card" id="demande-visite">
                    <h3>Demander une visite</h3>
                    <form class="form-grid" action="<?= url('/formulaire/contact') ?>" method="post" data-ajax-form novalidate>
                        <?= csrf_field() ?><?= antispam_fields() ?>
                        <input type="hidden" name="_back" value="/logements/<?= e($p['slug']) ?>#demande-visite">
                        <input type="hidden" name="type" value="visite">
                        <input type="hidden" name="property_id" value="<?= (int) $p['id'] ?>">
                        <input type="hidden" name="subject" value="Demande de visite — <?= e($p['title']) ?>">
                        <div class="field"><label for="v-name">Nom complet <span aria-hidden="true">*</span></label><input id="v-name" name="name" type="text" required autocomplete="name"><?= field_error('name') ?></div>
                        <div class="field"><label for="v-phone">Téléphone</label><input id="v-phone" name="phone" type="tel" autocomplete="tel" placeholder="+226 ..."><?= field_error('phone') ?></div>
                        <div class="field"><label for="v-email">E-mail</label><input id="v-email" name="email" type="email" autocomplete="email"><?= field_error('email') ?></div>
                        <div class="field"><label for="v-date">Date souhaitée</label><input id="v-date" name="visit_date" type="date" min="<?= date('Y-m-d') ?>"></div>
                        <div class="field"><label for="v-msg">Message</label><textarea id="v-msg" name="message" rows="3">Bonjour, je souhaite visiter le logement « <?= e($p['title']) ?> ».</textarea><?= field_error('message') ?></div>
                        <div class="field field-check"><label class="checkbox"><input type="checkbox" name="consent" value="1" required><span>J’accepte d’être recontacté(e) par GELPAZ IMMO.</span></label><?= field_error('consent') ?></div>
                        <button class="btn btn-primary btn-block" type="submit"><span>Envoyer ma demande</span><?= icon('send') ?></button>
                        <div class="form-message" role="status" aria-live="polite"></div>
                    </form>
                </div>

                <div class="side-card agency-card">
                    <img src="<?= url('assets/images/brand/logo-gelpaz.svg') ?>" alt="GELPAZ IMMO-SA" width="120" height="75" loading="lazy">
                    <p>Promoteur immobilier depuis 1987</p>
                    <ul>
                        <li><?= icon('map-pin') ?><span><?= e(setting('address')) ?></span></li>
                        <li><?= icon('phone') ?><a class="notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li>
                        <li><?= icon('mail') ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                        <li><?= icon('clock') ?><span><?= e(setting('opening_hours')) ?></span></li>
                    </ul>
                </div>

                <div class="side-card">
                    <h3>Partager ce logement</h3>
                    <div class="share-links">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($url) ?>" target="_blank" rel="noopener" aria-label="Partager sur Facebook"><?= icon('brand-facebook') ?></a>
                        <a href="https://wa.me/?text=<?= rawurlencode($p['title'] . ' — ' . $url) ?>" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp"><?= icon('brand-whatsapp') ?></a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($url) ?>" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn"><?= icon('brand-linkedin') ?></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($url) ?>&text=<?= rawurlencode($p['title']) ?>" target="_blank" rel="noopener" aria-label="Partager sur X"><?= icon('brand-x') ?></a>
                        <a href="mailto:?subject=<?= rawurlencode($p['title']) ?>&body=<?= rawurlencode($url) ?>" aria-label="Partager par e-mail"><?= icon('mail') ?></a>
                        <button type="button" data-copy="<?= e($url) ?>" aria-label="Copier le lien"><?= icon('copy') ?></button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php if ($similar): ?>
<section class="section bg-smoke similar-section">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>À découvrir aussi</span>
                <h2 class="sec-title" data-split>Logements similaires</h2>
            </div>
            <div class="slider-arrows">
                <button class="slider-arrow similar-prev" type="button" aria-label="Précédent"><?= icon('arrow-left') ?></button>
                <button class="slider-arrow similar-next" type="button" aria-label="Suivant"><?= icon('arrow-right') ?></button>
            </div>
        </div>
        <div class="swiper similar-slider">
            <div class="swiper-wrapper">
                <?php foreach ($similar as $sp): ?><div class="swiper-slide"><?php partial('property-card', ['p' => $sp]); ?></div><?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
