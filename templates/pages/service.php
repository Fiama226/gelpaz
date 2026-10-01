<?php
$pageTitle = $service['title'];
$metaDescription = $service['excerpt'];
$ogImage = media($service['image']);
$features = array_filter(array_map('trim', explode("\n", (string) $service['features'])));
partial('breadcrumb', ['title' => $service['title'], 'crumbs' => [['Nos activités', '/nos-activites'], [$service['title'], null]], 'image' => $service['image'] ?: 'assets/images/services/construction.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="sidebar-layout">
            <article>
                <div class="service-detail-media" data-reveal="up"><?= img(media($service['image']), $service['title'], '', ['loading' => 'eager']) ?></div>
                <span class="sub-title"><?= bird() ?>Nos activités</span>
                <h2 class="sec-title"><?= e($service['title']) ?></h2>
                <p class="sec-text" style="max-width:none"><strong><?= e($service['excerpt']) ?></strong></p>
                <div class="prose"><?= sanitize_html($service['content']) ?></div>
                <?php if ($features): ?>
                <div class="service-features">
                    <?php foreach ($features as $f): ?><div class="service-feature" data-reveal="up"><?= icon('check') ?><span><?= e($f) ?></span></div><?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="award-band" style="margin:40px 0">
                    <span class="award-icon"><?= icon('phone-call') ?></span>
                    <div><h3>Un projet ? Parlons-en</h3><p>Nos conseillers vous répondent du lundi au vendredi, <?= e(setting('opening_hours')) ?>.</p></div>
                    <a class="btn btn-primary" href="<?= url('/contact') ?>"><span>Nous contacter</span><?= icon('arrow-up-right') ?></a>
                </div>
                <?php if ($faqs): ?>
                <h2 style="font-size:28px;margin:50px 0 24px">Questions fréquentes</h2>
                <div class="accordion" data-single>
                    <?php foreach ($faqs as $i => $f): ?>
                    <div class="accordion-item<?= $i === 0 ? ' is-open' : '' ?>">
                        <button class="accordion-btn" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>"><span class="accordion-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>.</span><span class="q"><?= e($f['question']) ?></span><span class="accordion-icon"><?= icon('plus') ?></span></button>
                        <div class="accordion-panel"><div class="accordion-content"><p><?= nl2br(e($f['answer'])) ?></p></div></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </article>
            <?php partial('sidebar-services', ['services' => $services, 'recentPosts' => $recentPosts, 'current' => $service['slug']]); ?>
        </div>
    </div>
</section>
