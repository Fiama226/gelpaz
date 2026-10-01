<?php
$pageTitle = 'Nos logements';
$metaDescription = 'Villas F3, F4 et F5 duplex à vendre ou à louer à Ouagadougou et au Burkina Faso : découvrez tous les logements GELPAZ IMMO. Prix sur demande.';
partial('breadcrumb', ['title' => 'Nos logements', 'crumbs' => [['Nos offres', null], ['Nos logements', null]], 'image' => 'assets/images/hero/hero-1.jpg']);
$catNames = array_column($categories, 'name', 'slug');
$siteNames = array_column($sites, 'name', 'slug');
$active = [];
if ($filters['categorie']) { $active['categorie'] = $catNames[$filters['categorie']] ?? $filters['categorie']; }
if ($filters['type']) { $active['type'] = transaction_label($filters['type']); }
if ($filters['site']) { $active['site'] = $siteNames[$filters['site']] ?? $filters['site']; }
if ($filters['chambres']) { $active['chambres'] = $filters['chambres'] . ' chambres et +'; }
if ($filters['q']) { $active['q'] = '« ' . $filters['q'] . ' »'; }
?>
<section class="section" style="padding-top:0">
    <div class="container">
        <form class="filter-bar" method="get" action="<?= url('/logements') ?>" data-reveal="up">
            <div class="filter-search">
                <?= icon('search') ?>
                <label class="sr-only" for="f-q">Rechercher</label>
                <input class="input" id="f-q" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Rechercher un logement, un site, une référence…">
            </div>
            <label class="sr-only" for="f-cat">Catégorie</label>
            <select class="input" id="f-cat" name="categorie">
                <option value="">Toutes les gammes</option>
                <?php foreach ($categories as $c): ?><option value="<?= e($c['slug']) ?>" <?= $filters['categorie'] === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= (int) $c['total'] ?>)</option><?php endforeach; ?>
            </select>
            <label class="sr-only" for="f-type">Transaction</label>
            <select class="input" id="f-type" name="type">
                <option value="">Vente et location</option>
                <option value="vente" <?= $filters['type'] === 'vente' ? 'selected' : '' ?>>À vendre</option>
                <option value="location" <?= $filters['type'] === 'location' ? 'selected' : '' ?>>À louer</option>
            </select>
            <label class="sr-only" for="f-site">Site</label>
            <select class="input" id="f-site" name="site">
                <option value="">Tous les sites</option>
                <?php foreach ($sites as $s): if (!(int) $s['total']) { continue; } ?><option value="<?= e($s['slug']) ?>" <?= $filters['site'] === $s['slug'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?>
            </select>
            <label class="sr-only" for="f-tri">Trier par</label>
            <select class="input" id="f-tri" name="tri">
                <option value="">Pertinence</option>
                <option value="recent" <?= $filters['tri'] === 'recent' ? 'selected' : '' ?>>Plus récents</option>
                <option value="surface" <?= $filters['tri'] === 'surface' ? 'selected' : '' ?>>Plus grande surface</option>
                <option value="chambres" <?= $filters['tri'] === 'chambres' ? 'selected' : '' ?>>Plus de chambres</option>
                <option value="populaire" <?= $filters['tri'] === 'populaire' ? 'selected' : '' ?>>Les plus consultés</option>
            </select>
            <button class="btn btn-primary" type="submit"><?= icon('sliders') ?><span>Filtrer</span></button>
        </form>

        <div class="listing-toolbar">
            <p class="listing-count"><span><?= (int) $pager['total'] ?></span> <?= $pager['total'] > 1 ? 'logements trouvés' : 'logement trouvé' ?></p>
            <div class="view-toggle" role="group" aria-label="Affichage">
                <a href="<?= e(query_url(['vue' => null])) ?>" class="<?= $view === 'grid' ? 'is-active' : '' ?>" aria-label="Affichage en grille"><?= icon('grid') ?></a>
                <a href="<?= e(query_url(['vue' => 'liste'])) ?>" class="<?= $view === 'list' ? 'is-active' : '' ?>" aria-label="Affichage en liste"><?= icon('list') ?></a>
            </div>
        </div>

        <?php if ($active): ?>
        <div class="active-filters">
            <?php foreach ($active as $k => $label): ?>
            <a class="chip" href="<?= e(query_url([$k => null, 'page' => null])) ?>"><?= e($label) ?><?= icon('x') ?></a>
            <?php endforeach; ?>
            <a class="link-arrow" href="<?= url('/logements') ?>"><span>Effacer les filtres</span><?= icon('rotate-ccw') ?></a>
        </div>
        <?php endif; ?>

        <?php if ($items): ?>
        <div class="property-grid<?= $view === 'list' ? ' is-list' : '' ?>">
            <?php foreach ($items as $p): partial('property-card', ['p' => $p, 'layout' => $view]); endforeach; ?>
        </div>
        <?php partial('pagination', ['pager' => $pager]); ?>
        <?php else: ?>
        <div class="empty-state">
            <span class="icon-wrap"><?= icon('home') ?></span>
            <h2>Aucun logement ne correspond à votre recherche</h2>
            <p>Modifiez vos critères ou contactez-nous : nous avons peut-être la villa qu’il vous faut, ou pouvons la construire selon vos plans.</p>
            <div class="btn-group" style="justify-content:center">
                <a class="btn btn-primary" href="<?= url('/logements') ?>"><span>Voir tous les logements</span><?= icon('arrow-right') ?></a>
                <a class="btn btn-outline" href="<?= url('/contact') ?>"><span>Nous contacter</span><?= icon('mail') ?></a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section-sm bg-ivory">
    <div class="container">
        <div class="award-band" style="background:#fff" data-reveal="up">
            <span class="award-icon"><?= icon('pencil-ruler') ?></span>
            <div>
                <h3>Vous ne trouvez pas la villa idéale ?</h3>
                <p>Apportez votre plan ou laissez nos équipes concevoir un projet personnalisé, adapté à vos envies et à votre budget.</p>
            </div>
            <a class="btn btn-primary" href="<?= url('/nos-activites/projets-personnalises') ?>"><span>Projet sur mesure</span><?= icon('arrow-up-right') ?></a>
        </div>
    </div>
</section>
