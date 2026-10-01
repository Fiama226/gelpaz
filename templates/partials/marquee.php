<?php
$words = array_filter(array_map('trim', explode('|', (string) setting('marquee', 'Bienvenue'))));
$variant = $variant ?? 'gold';
?>
<div class="marquee marquee-<?= e($variant) ?>" aria-label="Bienvenue dans les langues du Burkina Faso">
    <div class="marquee-track notranslate" translate="no">
        <?php for ($r = 0; $r < 2; $r++): ?>
        <div class="marquee-group" <?= $r ? 'aria-hidden="true"' : '' ?>>
            <?php foreach ($words as $w): ?><span class="marquee-item"><?= e($w) ?></span><span class="marquee-sep"><?= bird() ?></span><?php endforeach; ?>
            <?php foreach ($words as $w): ?><span class="marquee-item"><?= e($w) ?></span><span class="marquee-sep"><?= bird() ?></span><?php endforeach; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>
