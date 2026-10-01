<?php
$pageTitle = 'Nos activités';
$metaDescription = 'Promotion immobilière, vente de villas, gestion locative, projets personnalisés, BTP, gestion de patrimoine, expertise et sécurisation : les activités de GELPAZ IMMO.';
partial('breadcrumb', ['title' => 'Nos activités', 'crumbs' => [['Nos activités', null]], 'image' => 'assets/images/services/construction.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="sidebar-layout">
            <div>
                <div class="section-head" style="margin-bottom:40px">
                    <span class="sub-title"><?= bird() ?>Ce que nous faisons</span>
                    <h2 class="sec-title" data-split>Des services immobiliers complets</h2>
                    <p class="sec-text">De la conception de cités à la gestion de votre patrimoine, nos équipes mettent plus de trente ans d’expérience à votre service.</p>
                </div>
                <div class="service-grid">
                    <?php foreach ($services as $i => $s): ?>
                    <article class="service-card" data-reveal="up" data-delay="<?= ($i % 2) * 100 ?>">
                        <a class="service-card-media" href="<?= url('/nos-activites/' . $s['slug']) ?>" tabindex="-1">
                            <?= img(media($s['image']), $s['title']) ?>
                            <span class="service-card-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            <span class="service-card-icon"><?= icon($s['icon'] ?: 'building') ?></span>
                        </a>
                        <div class="service-card-body">
                            <h3><a href="<?= url('/nos-activites/' . $s['slug']) ?>"><?= e($s['title']) ?></a></h3>
                            <p><?= e($s['excerpt']) ?></p>
                            <a class="link-arrow" href="<?= url('/nos-activites/' . $s['slug']) ?>"><span>En savoir plus</span><?= icon('arrow-right') ?></a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php partial('sidebar-services', ['services' => $services, 'recentPosts' => $recentPosts, 'current' => null]); ?>
        </div>
    </div>
</section>

<?php if ($activities): ?>
<section class="section bg-smoke">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Responsabilité sociétale</span>
                <h2 class="sec-title" data-split>Nos actions pour la communauté</h2>
                <p class="sec-text">Reboisement de nos cités, soutien au sport et aux initiatives locales, journées portes ouvertes : nous associons développement immobilier et engagement citoyen.</p>
            </div>
            <a class="btn btn-outline" href="<?= url('/actualites?categorie=RSE') ?>"><span>Toutes nos actions</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="post-grid" style="grid-template-columns:repeat(2,1fr)">
            <?php foreach ($activities as $post): partial('post-card', ['post' => $post, 'showExcerpt' => true]); endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
