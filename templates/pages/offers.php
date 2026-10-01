<?php
$pageTitle = 'Nos offres immobilières';
$metaDescription = 'Vente de villas haut et moyen standing, gestion locative, accompagnement sur mesure, expertise immobilière et sécurisation des documents : les offres de GELPAZ IMMO.';
partial('breadcrumb', ['title' => 'Nos offres immobilières', 'crumbs' => [['À propos', '/a-propos'], ['Nos offres immobilières', null]], 'image' => 'assets/images/hero/hero-3.jpg']);
$offers = [
    ['key', 'Vente de villas haut et moyen standing', 'Des villas F3, F4 et F5 duplex dans des cités modernes et viabilisées.', '/nos-activites/vente-de-villas'],
    ['clipboard-check', 'Gestion locative', 'Nous louons, suivons et entretenons votre bien pour sécuriser vos revenus.', '/nos-activites/gestion-locative'],
    ['pencil-ruler', 'Accompagnement sur mesure', 'Apportez votre plan ou laissez-nous concevoir un plan personnalisé.', '/nos-activites/projets-personnalises'],
    ['scale', 'Expertise immobilière', 'Évaluation de biens et conseil pour des décisions éclairées.', '/nos-activites/expertise-immobiliere'],
    ['file-check', 'Vérification et sécurisation des documents', 'Pour acheter en toute sérénité et en toute sécurité juridique.', '/nos-activites/securisation-et-conseil'],
    ['lightbulb', 'Étude et conseil', 'Avant toute acquisition ou construction, nous étudions votre projet avec vous.', '/nos-activites/securisation-et-conseil'],
];
$specs = [
    'f3-moyen-standing' => [['area', 'Parcelles de 300 m²'], ['bed', '2 chambres'], ['bath', '2 salles de bain']],
    'f4-moyen-standing' => [['area', 'Parcelles de 400 m²'], ['bed', '3 chambres'], ['sofa', 'Salon et salle à manger']],
    'f5-duplex-haut-standing' => [['area', 'Plus de 555 m²'], ['bed', '4 chambres + bureau'], ['waves', 'Option piscine privative']],
];
?>
<section class="section">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Nos solutions</span>
            <h2 class="sec-title" data-split>Des offres pour chaque projet de vie</h2>
            <p class="sec-text">Particuliers, entreprises, investisseurs ou membres de la diaspora : GELPAZ IMMO vous propose des solutions complètes et un accompagnement de A à Z.</p>
        </div>
        <div class="offer-grid">
            <?php foreach ($offers as $i => [$ic, $t, $d, $u]): ?>
            <a class="offer-card" href="<?= url($u) ?>" data-reveal="up" data-delay="<?= ($i % 3) * 100 ?>">
                <span class="feature-icon"><?= icon($ic) ?></span>
                <h3><?= e($t) ?></h3>
                <p><?= e($d) ?></p>
                <span class="link-arrow"><span>En savoir plus</span><?= icon('arrow-right') ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section bg-smoke">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Nos gammes de villas</span>
                <h2 class="sec-title" data-split>Trois gammes, un même niveau d’exigence</h2>
            </div>
            <a class="btn btn-primary" href="<?= url('/souscription-logement') ?>"><span>Souscrire</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="range-grid">
            <?php foreach ($categories as $i => $c):
                $cover = App\Repo::properties(['categorie' => $c['slug']], 1); $cover = $cover[0] ?? null; ?>
            <article class="range-card" data-reveal="up" data-delay="<?= $i * 100 ?>">
                <div class="range-media">
                    <?= img($cover ? property_cover($cover) : url('assets/images/hero/hero-1.jpg'), $c['name']) ?>
                    <span class="badge"><?= plural((int) $c['total'], 'logement') ?></span>
                </div>
                <div class="range-body">
                    <h3><?= e($c['name']) ?></h3>
                    <p><?= e($c['description']) ?></p>
                    <ul class="range-specs">
                        <?php foreach ($specs[$c['slug']] ?? [] as [$ic, $label]): ?><li><?= icon($ic) ?><?= e($label) ?></li><?php endforeach; ?>
                    </ul>
                    <div class="range-price">
                        <span>Prix sur demande</span>
                        <a class="link-arrow" href="<?= url('/logements?categorie=' . $c['slug']) ?>"><span>Voir les villas</span><?= icon('arrow-right') ?></a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Financement</span>
            <h2 class="sec-title" data-split>Des modalités adaptées à votre situation</h2>
        </div>
        <div class="pay-grid">
            <div class="pay-card" data-reveal="up">
                <span class="feature-icon"><?= icon('banknote') ?></span>
                <h3>Paiement au comptant</h3>
                <p>Réglez votre villa en une fois et bénéficiez d’une procédure d’acquisition simplifiée et rapide.</p>
            </div>
            <div class="pay-card" data-reveal="up" data-delay="100">
                <span class="feature-icon"><?= icon('landmark') ?></span>
                <h3>Financement bancaire</h3>
                <p>Nous vous orientons et vous accompagnons dans la constitution de votre dossier de financement.</p>
            </div>
            <div class="pay-card pay-card-accent" data-reveal="up" data-delay="200">
                <span class="feature-icon"><?= icon('pencil-ruler') ?></span>
                <h3>Projet personnel</h3>
                <p>Apportez votre plan ou faites appel à nous pour un plan personnalisé : nous réalisons la villa qui vous ressemble.</p>
                <a class="btn btn-gold btn-sm" href="<?= url('/nos-activites/projets-personnalises') ?>" style="margin-top:10px"><span>En savoir plus</span><?= icon('arrow-up-right') ?></a>
            </div>
        </div>
    </div>
</section>

<?php if ($featured): ?>
<section class="section bg-smoke">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>À la une</span>
                <h2 class="sec-title" data-split>Nos villas en vedette</h2>
            </div>
            <a class="btn btn-outline" href="<?= url('/logements') ?>"><span>Tous les logements</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="property-grid">
            <?php foreach ($featured as $p): partial('property-card', ['p' => $p]); endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
