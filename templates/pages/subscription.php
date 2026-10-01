<?php
$pageTitle = 'Souscription logement';
$metaDescription = 'Souscrivez à une villa F3, F4 ou F5 duplex à la Cité de l’Intégration (extension sud de Ouaga 2000) : paiement au comptant ou financement bancaire.';
partial('breadcrumb', ['title' => 'Souscription logement', 'crumbs' => [['Souscription logement', null]], 'image' => 'assets/images/about/about-1.jpg']);
partial('alerts');
$ranges = [
    ['Villa F3 moyen standing', 'f3-moyen-standing', 'Parcelles de 300 m²', [['bed', '2 chambres'], ['bath', '2 salles de bain'], ['area', '300 m² de terrain']]],
    ['Villa F4 moyen standing', 'f4-moyen-standing', 'Parcelles de 400 m²', [['bed', '3 chambres'], ['sofa', 'Salon et salle à manger'], ['area', '400 m² de terrain']]],
    ['F5 duplex haut standing', 'f5-duplex-haut-standing', 'Plus de 555 m²', [['bed', '4 chambres + bureau'], ['waves', 'Option piscine'], ['area', '555 m² et plus']]],
];
?>
<section class="section">
    <div class="container">
        <div class="split-grid">
            <div data-reveal="left">
                <span class="sub-title"><?= bird() ?>Cité de l’Intégration — Extension Sud de Ouaga 2000</span>
                <h2 class="sec-title" data-split>Vous souhaitez réaliser votre projet de vie ?</h2>
                <ul class="sub-intro-list">
                    <li><?= icon('home') ?>Vous voulez devenir propriétaire d’une villa moderne dans une cité viabilisée.</li>
                    <li><?= icon('globe') ?>Vous vivez à l’étranger et souhaitez investir au pays en toute sécurité.</li>
                    <li><?= icon('wallet') ?>Vous cherchez des modalités de paiement adaptées : comptant ou financement bancaire.</li>
                    <li><?= icon('pencil-ruler') ?>Vous avez votre propre plan, ou souhaitez un plan personnalisé.</li>
                </ul>
                <p class="sec-text"><strong>GELPAZ IMMO vous accompagne</strong> à chaque étape, de la souscription à la remise des clés.</p>
                <a class="btn btn-primary" href="#formulaire-souscription"><span>Je souscris</span><?= icon('arrow-down') ?></a>
            </div>
            <div class="split-media" data-reveal="right">
                <?= img(url('assets/images/about/about-1.jpg'), 'Famille devant sa villa', '', ['style' => 'object-position:center 30%']) ?>
                <div class="split-media-badge"><?= icon('key') ?><div><strong>Installés en 3 mois</strong><span>témoignage d’un résident</span></div></div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-smoke">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>Nos villas disponibles</span>
            <h2 class="sec-title" data-split>Choisissez votre future villa</h2>
        </div>
        <div class="range-grid">
            <?php foreach ($ranges as $i => [$label, $slug, $land, $specs]): $cover = $covers[$slug] ?? null; ?>
            <article class="range-card" data-reveal="up" data-delay="<?= $i * 100 ?>">
                <div class="range-media">
                    <?= img($cover ? property_cover($cover, false) : url('assets/images/hero/hero-1.jpg'), $label) ?>
                    <span class="badge"><?= e($land) ?></span>
                </div>
                <div class="range-body">
                    <h3><?= e($label) ?></h3>
                    <ul class="range-specs">
                        <?php foreach ($specs as [$ic, $t]): ?><li><?= icon($ic) ?><?= e($t) ?></li><?php endforeach; ?>
                        <li><?= icon('wallet') ?>Comptant ou financement bancaire</li>
                    </ul>
                    <div class="range-price">
                        <span>Prix sur demande</span>
                        <div class="btn-group" style="gap:10px">
                            <?php if ($cover): ?><a class="btn-circle" href="<?= url('/logements/' . $cover['slug']) ?>" aria-label="Voir la fiche"><?= icon('eye') ?></a><?php endif; ?>
                            <a class="btn btn-primary btn-sm" href="#formulaire-souscription" data-select-villa="<?= e($label) ?>"><span>Souscrire</span><?= icon('arrow-right') ?></a>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section process-section bg-ivory">
    <div class="container">
        <div class="section-head text-center">
            <span class="sub-title"><?= bird() ?>La procédure</span>
            <h2 class="sec-title" data-split>Trois étapes vers votre nouvelle maison</h2>
        </div>
        <div class="process-grid">
            <div class="process-card" data-reveal="up"><span class="process-num">01</span><span class="process-icon"><?= icon('search') ?></span><h3>Choix de la propriété</h3><p>Sélectionnez la villa qui vous intéresse parmi nos offres.</p></div>
            <div class="process-card" data-reveal="up" data-delay="120"><span class="process-num">02</span><span class="process-icon"><?= icon('calendar-check') ?></span><h3>Réservation</h3><p>Envoyez le formulaire ci-dessous ou contactez-nous par e-mail, téléphone ou WhatsApp.</p></div>
            <div class="process-card" data-reveal="up" data-delay="240"><span class="process-num">03</span><span class="process-icon"><?= icon('key') ?></span><h3>Finalisation</h3><p>Nous fixons ensemble le rendez-vous de validation de votre acquisition.</p></div>
        </div>
    </div>
</section>

<section class="section" id="formulaire-souscription">
    <div class="container">
        <div class="subscribe-layout">
            <div class="form-card" data-reveal="left">
                <span class="sub-title"><?= bird() ?>Formulaire de souscription</span>
                <h2 class="sec-title" style="font-size:clamp(26px,2.6vw,38px)">Réservez votre villa en quelques minutes</h2>
                <form class="form-grid" action="<?= url('/formulaire/souscription') ?>" method="post" data-ajax-form novalidate>
                    <?= csrf_field() ?><?= antispam_fields() ?>
                    <input type="hidden" name="_back" value="/souscription-logement#formulaire-souscription">
                    <div class="field"><label for="s-name">Nom et prénom(s) <span aria-hidden="true">*</span></label><input id="s-name" name="full_name" type="text" required autocomplete="name" value="<?= e(old('full_name')) ?>"><?= field_error('full_name') ?></div>
                    <div class="field"><label for="s-phone">Téléphone / WhatsApp <span aria-hidden="true">*</span></label><input id="s-phone" name="phone" type="tel" required autocomplete="tel" placeholder="+226 ..." value="<?= e(old('phone')) ?>"><?= field_error('phone') ?></div>
                    <div class="field"><label for="s-email">E-mail</label><input id="s-email" name="email" type="email" autocomplete="email" value="<?= e(old('email')) ?>"><?= field_error('email') ?></div>
                    <div class="field"><label for="s-country">Pays de résidence</label><input id="s-country" name="country" type="text" autocomplete="country-name" placeholder="Burkina Faso, Côte d’Ivoire, France…" value="<?= e(old('country')) ?>"></div>
                    <div class="field"><label for="s-city">Ville</label><input id="s-city" name="city" type="text" autocomplete="address-level2" value="<?= e(old('city')) ?>"></div>
                    <div class="field"><label for="s-site">Site souhaité</label>
                        <select id="s-site" name="site">
                            <option value="Cité de l’Intégration — Extension Sud de Ouaga 2000">Cité de l’Intégration — Extension Sud de Ouaga 2000</option>
                            <?php foreach ($sites as $s): if ($s['slug'] === 'cite-de-l-integration') { continue; } ?><option value="<?= e($s['name']) ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
                            <option value="À définir">À définir avec un conseiller</option>
                        </select>
                    </div>
                    <div class="field field-full">
                        <label>Type de logement <span aria-hidden="true">*</span></label>
                        <div class="radio-cards">
                            <?php foreach (['Villa F3 moyen standing', 'Villa F4 moyen standing', 'F5 duplex haut standing', 'Projet personnalisé'] as $i => $v): ?>
                            <label class="radio-card"><input type="radio" name="villa_type" value="<?= e($v) ?>" <?= old('villa_type') === $v ? 'checked' : '' ?> required><span><?= icon($i === 3 ? 'pencil-ruler' : 'home') ?><?= e($v) ?></span></label>
                            <?php endforeach; ?>
                        </div>
                        <?= field_error('villa_type') ?>
                    </div>
                    <div class="field field-full">
                        <label>Mode de paiement <span aria-hidden="true">*</span></label>
                        <div class="radio-cards">
                            <?php foreach (['Au comptant' => 'banknote', 'Financement bancaire' => 'landmark', 'À définir avec un conseiller' => 'users'] as $v => $ic): ?>
                            <label class="radio-card"><input type="radio" name="payment_mode" value="<?= e($v) ?>" <?= old('payment_mode') === $v ? 'checked' : '' ?> required><span><?= icon($ic) ?><?= e($v) ?></span></label>
                            <?php endforeach; ?>
                        </div>
                        <?= field_error('payment_mode') ?>
                    </div>
                    <div class="field field-full"><label for="s-msg">Précisions sur votre projet</label><textarea id="s-msg" name="message" rows="4" placeholder="Nombre de personnes, délai souhaité, options (piscine…), questions…"><?= e(old('message')) ?></textarea></div>
                    <div class="field field-full field-check"><label class="checkbox"><input type="checkbox" name="consent" value="1" required><span>J’accepte d’être recontacté(e) par GELPAZ IMMO au sujet de ma demande de souscription.</span></label><?= field_error('consent') ?></div>
                    <div class="field field-full"><button class="btn btn-primary btn-lg" type="submit"><span>Envoyer ma demande de souscription</span><?= icon('send') ?></button></div>
                    <div class="form-message field-full" role="status" aria-live="polite"></div>
                </form>
            </div>
            <aside class="sticky-side" data-reveal="right">
                <div class="side-card price-card">
                    <small>Besoin d’aide ?</small>
                    <strong>Parlons-en</strong>
                    <p>Notre service commercial répond à toutes vos questions sur les villas, les sites et les modalités de paiement.</p>
                    <a class="btn btn-gold" href="<?= e(phone_href((string) setting('phone_mobile'))) ?>"><?= icon('phone') ?><span class="notranslate" translate="no"><?= e(setting('phone_mobile')) ?></span></a>
                    <a class="btn btn-whatsapp" href="<?= e(whatsapp_url('Bonjour GELPAZ IMMO, je souhaite souscrire à une villa.')) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>WhatsApp</span></a>
                </div>
                <div class="side-card">
                    <h3>Après votre demande</h3>
                    <ol class="subscribe-steps">
                        <li><div><strong>Nous vous appelons</strong><span>pour confirmer votre choix et répondre à vos questions.</span></div></li>
                        <li><div><strong>Visite et offre</strong><span>visite du site et offre personnalisée selon votre mode de paiement.</span></div></li>
                        <li><div><strong>Validation</strong><span>rendez-vous de validation de votre acquisition.</span></div></li>
                    </ol>
                </div>
            </aside>
        </div>
    </div>
</section>
