<?php
$pageTitle = 'Qui sommes-nous ?';
$metaDescription = 'GELPAZ IMMO SA, promoteur immobilier au Burkina Faso : héritière de l’agence GELPAZ SARL fondée en 1987 par feu M. Z. Alain ZOUNGRANA, premier expert immobilier diplômé d’État du pays.';
partial('breadcrumb', ['title' => 'Qui sommes-nous ?', 'crumbs' => [['À propos', null]], 'image' => 'assets/images/about/about-2.jpg']);
$helpIcons = ['building-2', 'key', 'clipboard-check', 'pencil-ruler', 'hard-hat', 'landmark', 'scale', 'shield-check'];
?>
<section class="section about-collage-section">
    <div class="container">
        <div class="about-collage-grid">
            <div class="collage" data-reveal="left">
                <div class="collage-item collage-1"><?= img(url('assets/images/about/about-1.jpg'), 'Famille propriétaire d’une villa GELPAZ') ?></div>
                <div class="collage-item collage-2"><?= img(url('assets/images/hero/hero-1.jpg'), 'Villa moderne à Ouagadougou') ?></div>
                <div class="collage-item collage-3"><?= img(url('assets/images/about/about-2.jpg'), 'Conseillère GELPAZ IMMO avec des clients') ?></div>
                <div class="collage-item collage-4"><?= img(url('assets/images/services/construction.jpg'), 'Chantier de construction de villas') ?></div>
                <div class="collage-item collage-5"><?= img(url('assets/images/about/about-3.jpg'), 'Intérieur d’une villa') ?></div>
                <div class="about-rotating" aria-hidden="true">
                    <svg viewBox="0 0 200 200"><defs><path id="circlePathAbout" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs><text><textPath href="#circlePathAbout">GELPAZ IMMO • LA DIFFÉRENCE • DEPUIS 1987 •</textPath></text></svg>
                    <span class="about-rotating-center"><?= bird() ?></span>
                </div>
            </div>
            <div data-reveal="right">
                <span class="sub-title"><?= bird() ?>Notre histoire</span>
                <h2 class="sec-title" data-split>GELPAZ IMMO, la référence immobilière depuis 1987</h2>
                <p class="sec-text">Nous accompagnons particuliers, entreprises et institutions dans la <strong>vente</strong>, la <strong>location</strong> et la <strong>gestion</strong> de biens immobiliers, avec une expertise reconnue dans le domaine des bâtiments et travaux publics, la gestion de patrimoines et les biens haut de gamme contemporains.</p>
                <p>Notre agence immobilière <strong>GELPAZ SARL</strong> a été créée en 1987. En juin 2009 naît <strong>GELPAZ IMMO SA</strong>, société de promotion et de vente immobilière, pour répondre à un besoin croissant de logements décents au Burkina Faso.</p>
                <div class="feature-list">
                    <div class="feature-item"><span class="feature-icon"><?= icon('hard-hat') ?></span><div><h3>Expertise BTP</h3><p>Des constructions durables et des ouvrages réalisés dans les règles de l’art.</p></div></div>
                    <div class="feature-item"><span class="feature-icon"><?= icon('landmark') ?></span><div><h3>Gestion de patrimoine</h3><p>Valorisation et gestion de biens pour particuliers et institutions.</p></div></div>
                    <div class="feature-item"><span class="feature-icon"><?= icon('gem') ?></span><div><h3>Biens haut de gamme</h3><p>Des villas contemporaines, du moyen au haut standing.</p></div></div>
                    <div class="feature-item"><span class="feature-icon"><?= icon('handshake') ?></span><div><h3>Relation directe</h3><p>Nous travaillons directement avec nos clients, en toute transparence.</p></div></div>
                </div>
                <div class="btn-group">
                    <a class="btn btn-primary" href="<?= url('/logements') ?>"><span>Voir nos logements</span><?= icon('arrow-up-right') ?></a>
                    <a class="call-box" href="<?= e(phone_href((string) setting('phone'))) ?>">
                        <span class="call-box-icon"><?= icon('phone-call') ?></span>
                        <span><small>Une question ?</small><strong class="notranslate" translate="no"><?= e(setting('phone')) ?></strong></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-smoke">
    <div class="container">
        <div class="founder" data-reveal="up">
            <div class="founder-mark notranslate" translate="no">AZ</div>
            <div>
                <span class="sub-title sub-title-light"><?= bird() ?>Notre fondateur</span>
                <h2>Feu M. Z. Alain ZOUNGRANA</h2>
                <span class="founder-role">Fondateur et Président d’honneur de GELPAZ IMMO</span>
                <p>Premier expert immobilier diplômé d’État du Burkina Faso, il a fondé l’agence GELPAZ en 1987 avec une conviction : chaque famille mérite un logement décent, construit avec sérieux et vendu en toute transparence.</p>
                <p>Son héritage – exigence, intégrité et sens du service – guide chaque jour nos équipes, de la conception des cités jusqu’à la remise des clés.</p>
            </div>
        </div>

        <div class="section-head text-center" style="margin-top:100px">
            <span class="sub-title"><?= bird() ?>Nos grandes étapes</span>
            <h2 class="sec-title" data-split>Plus de trente ans d’engagement</h2>
        </div>
        <div class="timeline">
            <?php
            $steps = [
                ['1987', 'Création de GELPAZ SARL', 'L’agence immobilière voit le jour à Ouagadougou.'],
                ['2009', 'Naissance de GELPAZ IMMO SA', 'En juin, la société de promotion et de vente immobilière est créée.'],
                ['2021', 'Premiers résidents', 'Les premières familles s’installent à la Cité de l’Intégration.'],
                ['2023', 'Pont de Kuul-Roumdé', 'Mise en service d’un pont réalisé par GELPAZ IMMO à Saaba.'],
                ['2025', 'Entreprise primée', 'Meilleure entreprise de promotion immobilière du Burkina Faso.'],
                ['2025', 'Engagement RSE', 'Opération de reboisement sur le site de l’Intégration.'],
                ['2025', 'Remise de logements à Pô', 'Remise partielle de villas F3 et F4 aux bénéficiaires.'],
                ['2026', 'Coopération Sud-Sud', 'Validation de projets urbains avec la SOPROFIM à N’Djaména.'],
            ];
            foreach ($steps as $i => [$y, $t, $d]): ?>
            <div class="timeline-item" data-reveal="up" data-delay="<?= ($i % 4) * 90 ?>">
                <span class="timeline-year"><?= $y ?></span>
                <h3><?= e($t) ?></h3>
                <p><?= e($d) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head section-head-split">
            <div>
                <span class="sub-title"><?= bird() ?>Ce que nous faisons</span>
                <h2 class="sec-title" data-split>Comment GELPAZ IMMO vous accompagne</h2>
            </div>
            <a class="btn btn-outline" href="<?= url('/nos-activites') ?>"><span>Toutes nos activités</span><?= icon('arrow-up-right') ?></a>
        </div>
        <div class="help-grid">
            <?php foreach ($services as $i => $s): ?>
            <a class="help-card" href="<?= url('/nos-activites/' . $s['slug']) ?>" data-reveal="up" data-delay="<?= ($i % 4) * 90 ?>">
                <span class="help-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <span class="help-icon"><?= icon($s['icon'] ?: ($helpIcons[$i] ?? 'building')) ?></span>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e(str_limit((string) $s['excerpt'], 110)) ?></p>
                <span class="link-arrow"><span>En savoir plus</span><?= icon('arrow-right') ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-sm counters">
    <div class="container">
        <div class="counters-grid">
            <div class="counter-item"><strong><span data-count="1987">1987</span></strong><span>Création de l’agence</span></div>
            <div class="counter-item"><strong><span data-count="<?= (int) setting('stat_years', 30) ?>">0</span><em>+</em></strong><span>Années d’expérience</span></div>
            <div class="counter-item"><strong><span data-count="<?= (int) setting('stat_sites', 14) ?>">0</span></strong><span>Sites et cités</span></div>
            <div class="counter-item"><strong><span data-count="<?= (int) setting('stat_regions', 17) ?>">0</span></strong><span>Régions ciblées</span></div>
        </div>
    </div>
</section>

<section class="section-sm">
    <div class="container">
        <div class="award-band" data-reveal="up">
            <span class="award-icon"><?= icon('trophy') ?></span>
            <div>
                <h3>Meilleure entreprise de promotion immobilière du Burkina Faso</h3>
                <p>Distinction reçue lors du gala des Top 20 des Entrepreneurs du BTP (2025) : elle célèbre la confiance de nos clients et le travail de nos équipes.</p>
            </div>
            <a class="btn btn-dark" href="<?= url('/actualites/une-nouvelle-victoire-pour-gelpaz-immo') ?>"><span>Lire l’article</span><?= icon('arrow-up-right') ?></a>
        </div>
    </div>
</section>

<section class="section-sm">
    <div class="container">
        <div class="partners-head">
            <span class="sub-title"><?= bird() ?>Nos partenaires</span>
            <p>Professionnels de l’immobilier, architectes, banques et institutions : ils nous accompagnent dans nos projets.</p>
        </div>
    </div>
    <?php partial('partners', ['partners' => $partners]); ?>
</section>

<?php partial('testimonials', ['testimonials' => $testimonials]); ?>
<?php partial('contact-section', ['title' => 'Rencontrons-nous pour parler de votre projet']); ?>
<?php partial('marquee', ['variant' => 'gold']); ?>
