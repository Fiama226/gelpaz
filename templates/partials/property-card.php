<?php
/** @var array $p */
$gallery = array_slice(property_gallery($p), 0, 4);
$url = url('/logements/' . $p['slug']);
$area = $p['land_area'] ? format_area($p['land_area']) : format_area($p['built_area']);
$areaLabel = $p['land_area'] ? 'Parcelle' : 'Bâti';
$layout = $layout ?? 'grid';
?>
<article class="property-card<?= $layout === 'list' ? ' property-card-list' : '' ?>" data-cat="<?= e($p['category_slug'] ?? '') ?>" data-type="<?= e($p['transaction']) ?>">
    <div class="property-media">
        <?php if (count($gallery) > 1): ?>
        <div class="swiper card-slider">
            <div class="swiper-wrapper">
                <?php foreach ($gallery as $i => $g): ?>
                <a class="swiper-slide" href="<?= $url ?>" tabindex="<?= $i ? '-1' : '0' ?>"><?= img($g['thumb'], $p['title'] . ($i ? ' — photo ' . ($i + 1) : ''), 'property-img') ?></a>
                <?php endforeach; ?>
            </div>
            <button class="card-nav card-prev" type="button" aria-label="Photo précédente"><?= icon('chevron-left') ?></button>
            <button class="card-nav card-next" type="button" aria-label="Photo suivante"><?= icon('chevron-right') ?></button>
            <div class="card-dots"></div>
        </div>
        <?php else: ?>
        <a href="<?= $url ?>"><?= img($gallery[0]['thumb'], $p['title'], 'property-img') ?></a>
        <?php endif; ?>
        <div class="property-badges">
            <span class="badge badge-<?= $p['transaction'] === 'location' ? 'gold' : 'primary' ?>"><?= e(transaction_label($p['transaction'])) ?></span>
            <?php if (!empty($p['is_featured'])): ?><span class="badge badge-dark"><?= icon('star') ?>En vedette</span><?php endif; ?>
        </div>
        <button class="fav-btn" type="button" data-fav="<?= e($p['slug']) ?>" aria-label="Ajouter aux favoris" aria-pressed="false"><?= icon('heart') ?></button>
        <span class="property-photos"><?= icon('images') ?><?= (int) ($p['images_total'] ?? count($gallery)) ?></span>
    </div>
    <div class="property-body">
        <div class="property-top">
            <?php if (!empty($p['category_name'])): ?><a class="property-cat" href="<?= url('/logements?categorie=' . $p['category_slug']) ?>"><?= e($p['category_name']) ?></a><?php endif; ?>
            <span class="property-status status-<?= e($p['status']) ?>"><?= e(property_status_label($p['status'])) ?></span>
        </div>
        <h3 class="property-title"><a href="<?= $url ?>"><?= e($p['title']) ?></a></h3>
        <p class="property-location"><?= icon('map-pin') ?><span><?= e($p['site_name'] ?: ($p['address'] ?: $p['city'])) ?></span></p>
        <?php if ($layout === 'list' && !empty($p['excerpt'])): ?><p class="property-excerpt"><?= e(str_limit((string) $p['excerpt'], 150)) ?></p><?php endif; ?>
        <ul class="property-specs">
            <?php if ($p['bedrooms']): ?><li title="Chambres"><?= icon('bed') ?><span><?= (int) $p['bedrooms'] ?> <small>ch.</small></span></li><?php endif; ?>
            <?php if ($p['bathrooms']): ?><li title="Salles d’eau"><?= icon('bath') ?><span><?= (int) $p['bathrooms'] ?> <small>sdb</small></span></li><?php endif; ?>
            <?php if ($area): ?><li title="<?= e($areaLabel) ?>"><?= icon('area') ?><span><?= e($area) ?></span></li><?php endif; ?>
        </ul>
        <div class="property-foot">
            <div class="property-price"><small>Prix</small><strong>Sur demande</strong></div>
            <a class="btn-circle" href="<?= $url ?>" aria-label="Voir le logement <?= e($p['title']) ?>"><?= icon('arrow-up-right') ?></a>
        </div>
    </div>
</article>
