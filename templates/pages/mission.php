<?php
$pageTitle = 'Missions – Visions – Valeurs';
$metaDescription = 'Les missions, la vision et les valeurs de GELPAZ IMMO : confiance, qualité, transparence et proximité pour un logement décent au Burkina Faso.';
partial('breadcrumb', ['title' => 'Missions – Visions – Valeurs', 'crumbs' => [['À propos', '/a-propos'], ['Missions – Visions – Valeurs', null]], 'image' => 'assets/images/hero/hero-2.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="split-grid">
            <div class="split-media" data-reveal="left">
                <?= img(url('assets/images/hero/hero-2.jpg'), 'Vue aérienne d’une cité résidentielle moderne') ?>
                <div class="split-media-badge"><?= icon('target') ?><div><strong>Un logement décent</strong><span>pour chaque famille burkinabè</span></div></div>
            </div>
            <div data-reveal="right">
                <span class="sub-title"><?= bird() ?>Notre raison d’être</span>
                <h2 class="sec-title" data-split>Bâtir des cités où il fait bon vivre</h2>
                <p class="sec-text">Depuis plus de trente ans, GELPAZ IMMO conçoit des programmes immobiliers durables et accessibles, au service des familles, de la diaspora et des collectivités.</p>
                <ul class="check-list">
                    <li><?= icon('check') ?>Des sites intelligents, viabilisés et bien desservis</li>
                    <li><?= icon('check') ?>Une présence ambitionnée dans les 17 régions du pays</li>
                    <li><?= icon('check') ?>Un partenariat étroit avec l’État et les collectivités</li>
                </ul>
                <a class="btn btn-primary" href="<?= url('/nos-sites') ?>"><span>Découvrir nos sites</span><?= icon('arrow-up-right') ?></a>
            </div>
        </div>
    </div>
</section>

<section class="section bg-smoke">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Ce qui nous anime</span>
            <h2 class="sec-title" data-split>Nos missions et notre vision</h2>
        </div>
        <div class="mvv-tabs">
            <div class="mvv-card" data-reveal="up">
                <span class="mvv-card-icon"><?= icon('flag') ?></span>
                <h3>Nos missions</h3>
                <ul>
                    <li><?= icon('check-circle') ?>Créer des sites intelligents</li>
                    <li><?= icon('check-circle') ?>Être présents dans les 17 régions du Burkina Faso</li>
                    <li><?= icon('check-circle') ?>Permettre à la diaspora d’accéder à un logement dans sa province d’origine</li>
                    <li><?= icon('check-circle') ?>Accompagner l’État dans sa politique de décentralisation</li>
                </ul>
            </div>
            <div class="mvv-card" data-reveal="up" data-delay="120">
                <span class="mvv-card-icon"><?= icon('eye') ?></span>
                <h3>Notre vision</h3>
                <ul>
                    <li><?= icon('check-circle') ?>Devenir la référence de la promotion immobilière au Burkina Faso et dans la sous-région</li>
                    <li><?= icon('check-circle') ?>Rendre accessible un logement décent au plus grand nombre</li>
                    <li><?= icon('check-circle') ?>Inscrire chaque projet dans le développement durable</li>
                    <li><?= icon('check-circle') ?>Construire avec l’État et les collectivités territoriales</li>
                </ul>
            </div>
            <div class="mvv-card" data-reveal="up" data-delay="240">
                <span class="mvv-card-icon"><?= icon('gem') ?></span>
                <h3>Nos engagements</h3>
                <ul>
                    <li><?= icon('check-circle') ?>Écoute active de chaque client</li>
                    <li><?= icon('check-circle') ?>Intégrité et loyauté en toutes circonstances</li>
                    <li><?= icon('check-circle') ?>Professionnalisme à chaque étape</li>
                    <li><?= icon('check-circle') ?>Discrétion dans le traitement de vos projets</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section why-section">
    <div class="why-bg-bird" aria-hidden="true"><?= bird() ?></div>
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title sub-title-light"><?= bird() ?>Nos valeurs</span>
            <h2 class="sec-title sec-title-light" data-split>Quatre valeurs, une seule différence</h2>
        </div>
        <div class="values-grid">
            <?php
            $values = [
                ['shield-check', 'Confiance', 'La sécurité juridique est au cœur de chaque transaction : documents vérifiés, engagements tenus.'],
                ['badge-check', 'Qualité', 'Des matériaux et des finitions choisis avec soin pour des villas durables et agréables à vivre.'],
                ['eye', 'Transparence', 'Une information claire à chaque étape, du choix de votre villa jusqu’à la remise des clés.'],
                ['heart-handshake', 'Proximité', 'Une équipe disponible, à votre écoute, à Ouagadougou comme à distance pour la diaspora.'],
            ];
            foreach ($values as $i => [$ic, $t, $d]): ?>
            <div class="value-card" data-reveal="up" data-delay="<?= $i * 100 ?>">
                <span class="value-icon"><?= icon($ic) ?></span>
                <h3><?= e($t) ?></h3>
                <p><?= e($d) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="chips chips-dark" style="justify-content:center;margin-top:50px">
            <?php foreach (['Écoute active', 'Intégrité', 'Loyauté', 'Professionnalisme', 'Discrétion', 'Innovation'] as $v): ?>
            <span class="chip"><?= icon('sparkles') ?><?= e($v) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('testimonials', ['testimonials' => $testimonials]); ?>
