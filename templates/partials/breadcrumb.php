<?php
/** @var string $title @var array $crumbs @var ?string $image @var ?string $tag */
$tag = $tag ?? 'h1';
$image = $image ?? 'assets/images/hero/hero-1.jpg';
?>
<section class="page-hero">
    <div class="page-hero-bg" style="background-image:url('<?= e(media($image)) ?>')"></div>
    <div class="page-hero-shape" aria-hidden="true"><?= bird() ?></div>
    <div class="container page-hero-inner">
        <<?= $tag ?> class="page-hero-title" data-split><?= e($title) ?></<?= $tag ?>>
        <nav class="breadcrumbs" aria-label="Fil d’Ariane">
            <ol itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?= url('/') ?>"><?= icon('home') ?><span itemprop="name">Accueil</span></a>
                    <meta itemprop="position" content="1">
                </li>
                <?php foreach ($crumbs ?? [] as $i => $c): ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if (!empty($c[1])): ?>
                    <a itemprop="item" href="<?= url($c[1]) ?>"><span itemprop="name"><?= e($c[0]) ?></span></a>
                    <?php else: ?>
                    <span itemprop="name" aria-current="page"><?= e(str_limit($c[0], 60)) ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?= $i + 2 ?>">
                </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</section>
