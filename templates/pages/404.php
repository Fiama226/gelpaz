<?php
$pageTitle = $pageTitle ?? 'Page introuvable';
$needsSwiper = false;
$metaDescription = 'La page demandée est introuvable.';
?>
<section class="error-page">
    <div class="container">
        <div class="error-visual" aria-hidden="true">
            <span class="error-code">404</span>
            <span class="error-bird"><?= bird() ?></span>
        </div>
        <h1>Oups ! Cette page s’est envolée</h1>
        <p><?= e(($message ?? '') ?: 'La page que vous recherchez n’existe pas ou a été déplacée. Pas d’inquiétude : votre future villa, elle, vous attend toujours !') ?></p>
        <form class="widget-search error-search" action="<?= url('/logements') ?>" method="get" role="search">
            <label class="sr-only" for="err-q">Rechercher un logement</label>
            <input id="err-q" type="search" name="q" placeholder="Rechercher un logement…" style="background:var(--smoke)">
            <button type="submit" aria-label="Rechercher"><?= icon('search') ?></button>
        </form>
        <div class="btn-group" style="justify-content:center">
            <a class="btn btn-primary btn-lg" href="<?= url('/') ?>"><?= icon('home') ?><span>Retour à l’accueil</span></a>
            <a class="btn btn-outline btn-lg" href="<?= url('/logements') ?>"><span>Voir nos logements</span><?= icon('arrow-right') ?></a>
        </div>
        <div class="error-links">
            <a class="chip" href="<?= url('/souscription-logement') ?>"><?= icon('key') ?>Souscription</a>
            <a class="chip" href="<?= url('/nos-sites') ?>"><?= icon('map-pin') ?>Nos sites</a>
            <a class="chip" href="<?= url('/actualites') ?>"><?= icon('newspaper') ?>Actualités</a>
            <a class="chip" href="<?= url('/contact') ?>"><?= icon('mail') ?>Contact</a>
        </div>
    </div>
</section>
