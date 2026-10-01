<?php
$pageTitle = 'Actualités';
$metaDescription = 'Actualités de GELPAZ IMMO : projets, réalisations, événements, législation foncière et immobilière au Burkina Faso.';
$needsSwiper = false;
partial('breadcrumb', ['title' => 'Actualités', 'crumbs' => [['Actualités', null]], 'image' => 'assets/images/hero/hero-2.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="listing-toolbar">
            <div class="chips" role="navigation" aria-label="Catégories">
                <a class="chip<?= $filters['categorie'] === '' ? ' is-active' : '' ?>" href="<?= url('/actualites') ?>">Toutes</a>
                <?php foreach ($categories as $c): ?>
                <a class="chip<?= $filters['categorie'] === $c['category'] ? ' is-active' : '' ?>" href="<?= url('/actualites?categorie=' . rawurlencode($c['category'])) ?>"><?= e($c['category']) ?></a>
                <?php endforeach; ?>
            </div>
            <form class="widget-search" action="<?= url('/actualites') ?>" method="get" role="search" style="min-width:300px">
                <label class="sr-only" for="blog-q">Rechercher une actualité</label>
                <input id="blog-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Rechercher une actualité…" style="background:var(--smoke)">
                <button type="submit" aria-label="Rechercher"><?= icon('search') ?></button>
            </form>
        </div>
        <?php if ($filters['q'] || $filters['tag']): ?>
        <div class="active-filters">
            <?php if ($filters['q']): ?><a class="chip" href="<?= e(query_url(['q' => null, 'page' => null])) ?>">« <?= e($filters['q']) ?> »<?= icon('x') ?></a><?php endif; ?>
            <?php if ($filters['tag']): ?><a class="chip" href="<?= e(query_url(['tag' => null, 'page' => null])) ?>">#<?= e($filters['tag']) ?><?= icon('x') ?></a><?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($posts): ?>
        <div class="post-grid">
            <?php foreach ($posts as $i => $post): ?><div data-reveal="up" data-delay="<?= ($i % 3) * 90 ?>"><?php partial('post-card', ['post' => $post, 'showExcerpt' => true]); ?></div><?php endforeach; ?>
        </div>
        <?php partial('pagination', ['pager' => $pager]); ?>
        <?php else: ?>
        <div class="empty-state">
            <span class="icon-wrap"><?= icon('newspaper') ?></span>
            <h2>Aucune actualité trouvée</h2>
            <p>Essayez une autre recherche ou parcourez toutes nos actualités.</p>
            <a class="btn btn-primary" href="<?= url('/actualites') ?>"><span>Toutes les actualités</span><?= icon('arrow-right') ?></a>
        </div>
        <?php endif; ?>
    </div>
</section>
