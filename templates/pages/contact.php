<?php
$pageTitle = 'Contactez-nous';
$metaDescription = 'Contactez GELPAZ IMMO à Ouagadougou : ' . setting('phone') . ', ' . setting('phone_mobile') . ' (WhatsApp), ' . setting('email') . '. ' . setting('address') . '.';
$needsSwiper = false;
partial('breadcrumb', ['title' => 'Contactez-nous', 'crumbs' => [['Contact', null]], 'image' => 'assets/images/about/about-2.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="contact-cards">
            <div class="contact-card" data-reveal="up">
                <span class="contact-card-icon"><?= icon('map-pin') ?></span>
                <h3>Notre adresse</h3>
                <p><?= e(setting('address')) ?></p>
            </div>
            <div class="contact-card" data-reveal="up" data-delay="100">
                <span class="contact-card-icon"><?= icon('phone-call') ?></span>
                <h3>Téléphones</h3>
                <a class="notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a>
                <a class="notranslate" translate="no" href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener"><?= e(setting('phone_mobile')) ?> (WhatsApp)</a>
            </div>
            <div class="contact-card" data-reveal="up" data-delay="200">
                <span class="contact-card-icon"><?= icon('mail') ?></span>
                <h3>E-mail et horaires</h3>
                <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a>
                <p><?= e(setting('opening_hours')) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0" id="formulaire">
    <div class="container">
        <?php partial('alerts'); ?>
        <div class="contact-page-grid">
            <div class="form-card" data-reveal="left">
                <span class="sub-title"><?= bird() ?>Écrivez-nous</span>
                <h2 class="sec-title" data-split>Vous avez une question ?</h2>
                <p>Remplissez le formulaire ci-dessous : votre message est transmis directement à notre équipe, qui vous répond dans les meilleurs délais.</p>
                <form class="form-grid" action="<?= url('/formulaire/contact') ?>" method="post" data-ajax-form novalidate style="margin-top:26px">
                    <?= csrf_field() ?><?= antispam_fields() ?>
                    <input type="hidden" name="_back" value="/contact#formulaire">
                    <input type="hidden" name="type" value="contact" data-type-field>
                    <div class="field"><label for="ct-name">Nom complet <span aria-hidden="true">*</span></label><input id="ct-name" name="name" type="text" required autocomplete="name" value="<?= e(old('name')) ?>"><?= field_error('name') ?></div>
                    <div class="field"><label for="ct-email">E-mail</label><input id="ct-email" name="email" type="email" autocomplete="email" value="<?= e(old('email')) ?>"><?= field_error('email') ?></div>
                    <div class="field"><label for="ct-phone">Téléphone</label><input id="ct-phone" name="phone" type="tel" autocomplete="tel" placeholder="+226 ..." value="<?= e(old('phone')) ?>"><?= field_error('phone') ?></div>
                    <div class="field"><label for="ct-subject">Objet</label>
                        <select id="ct-subject" name="subject" data-subject-select>
                            <?php foreach (['Demande d’informations', 'Villa F3 moyen standing', 'Villa F4 moyen standing', 'F5 duplex haut standing', 'Souscription logement', 'Demande de visite', 'Gestion locative', 'Projet personnalisé', 'Partenariat', 'Autre demande'] as $o): ?>
                            <option value="<?= e($o) ?>" <?= $o === 'Demande de visite' ? 'data-type="visite"' : '' ?> <?= old('subject') === $o ? 'selected' : '' ?>><?= e($o) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field-full"><label for="ct-property">Logement concerné</label>
                        <select id="ct-property" name="property_id"><option value="">— Aucun en particulier —</option><?php foreach ($properties as $pr): ?><option value="<?= (int) $pr['id'] ?>"><?= e($pr['title']) ?></option><?php endforeach; ?></select>
                    </div>
                    <div class="field field-full"><label for="ct-message">Message <span aria-hidden="true">*</span></label><textarea id="ct-message" name="message" rows="6" required><?= e(old('message')) ?></textarea><?= field_error('message') ?></div>
                    <div class="field field-full field-check"><label class="checkbox"><input type="checkbox" name="consent" value="1" required><span>J’accepte que GELPAZ IMMO utilise mes données pour répondre à ma demande (voir la <a href="<?= url('/mentions-legales#confidentialite') ?>">politique de confidentialité</a>).</span></label><?= field_error('consent') ?></div>
                    <div class="field field-full"><button class="btn btn-primary btn-lg" type="submit"><span>Envoyer le message</span><?= icon('send') ?></button></div>
                    <div class="form-message field-full" role="status" aria-live="polite"></div>
                </form>
            </div>
            <div class="contact-page-visual" data-reveal="right">
                <?= img(url('assets/images/hero/hero-1.jpg'), 'Villa moderne GELPAZ') ?>
                <div class="contact-pin" aria-hidden="true"><span></span><?= icon('map-pin') ?></div>
                <div class="contact-float-card">
                    <h3>Passez nous voir</h3>
                    <ul>
                        <li><?= icon('map-pin') ?><span><?= e(setting('address')) ?></span></li>
                        <li><?= icon('clock') ?><span><?= e(setting('opening_hours')) ?></span></li>
                        <li><?= icon('brand-whatsapp') ?><a href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener">Discuter sur WhatsApp</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="map-section" aria-label="Plan d’accès">
    <iframe title="Plan d’accès à GELPAZ IMMO" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=<?= rawurlencode((string) setting('map_query', 'Dagnoën, Ouagadougou')) ?>&z=14&output=embed"></iframe>
</section>
