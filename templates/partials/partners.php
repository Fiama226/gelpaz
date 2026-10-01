<?php
/** @var array $partners */
if (!$partners) {
    return;
}
?>
<div class="partners-marquee" aria-label="Nos partenaires">
    <div class="partners-track">
        <?php for ($r = 0; $r < 2; $r++): ?>
        <div class="partners-group" <?= $r ? 'aria-hidden="true"' : '' ?>>
            <?php foreach (array_merge($partners, $partners) as $pt): ?>
            <?php $logo = media($pt['logo'], $pt['logo_remote'], null); if (!$logo) { continue; } ?>
            <<?= $pt['url'] ? 'a href="' . e($pt['url']) . '" target="_blank" rel="noopener"' : 'div' ?> class="partner-logo" title="<?= e($pt['name']) ?>">
                <?= img($logo, $pt['name'], '', ['tabindex' => $r ? '-1' : null]) ?>
            </<?= $pt['url'] ? 'a' : 'div' ?>>
            <?php endforeach; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>
