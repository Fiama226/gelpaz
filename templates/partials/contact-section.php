<?php
$properties = App\Database::all('SELECT id, title FROM properties WHERE is_published = 1 ORDER BY sort, id');
$title = $title ?? 'Parlons de votre projet immobilier';
?>
<section class="section contact-section" id="contact-rapide">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-visual" data-reveal="left">
                <div class="contact-map-card">
                    <?= img(url('assets/images/about/about-2.jpg'), 'Un conseiller GELPAZ IMMO présente des plans à un couple', 'contact-visual-img') ?>
                    <div class="contact-pin" aria-hidden="true"><span></span><?= icon('map-pin') ?></div>
                    <div class="contact-float-card">
                        <h3>Notre agence</h3>
                        <ul>
                            <li><?= icon('map-pin') ?><span><?= e(setting('address')) ?></span></li>
                            <li><?= icon('phone') ?><a class="notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li>
                            <li><?= icon('clock') ?><span><?= e(setting('opening_hours')) ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="contact-form-wrap" data-reveal="right">
                <span class="sub-title"><?= bird() ?>Prendre contact</span>
                <h2 class="sec-title" data-split><?= e($title) ?></h2>
                <p class="sec-text">Une question, une demande de visite ou de souscription ? Laissez-nous vos coordonnées : un conseiller vous répond rapidement.</p>
                <form class="form-grid ajax-form" action="<?= url('/formulaire/contact') ?>" method="post" data-ajax-form novalidate>
                    <?= csrf_field() ?><?= antispam_fields() ?>
                    <input type="hidden" name="_back" value="<?= e(current_path()) ?>#contact-rapide">
                    <input type="hidden" name="type" value="contact" data-type-field>
                    <div class="field">
                        <label for="cs-name">Nom complet <span aria-hidden="true">*</span></label>
                        <input id="cs-name" type="text" name="name" required autocomplete="name" placeholder="Votre nom">
                        <?= field_error('name') ?>
                    </div>
                    <div class="field">
                        <label for="cs-phone">Téléphone</label>
                        <input id="cs-phone" type="tel" name="phone" autocomplete="tel" placeholder="+226 ...">
                        <?= field_error('phone') ?>
                    </div>
                    <div class="field">
                        <label for="cs-email">E-mail</label>
                        <input id="cs-email" type="email" name="email" autocomplete="email" placeholder="vous@exemple.com">
                        <?= field_error('email') ?>
                    </div>
                    <div class="field">
                        <label for="cs-subject">Objet</label>
                        <select id="cs-subject" name="subject" data-subject-select>
                            <option value="Demande d’informations">Demande d’informations</option>
                            <option value="Demande de visite" data-type="visite">Demande de visite</option>
                            <option value="Souscription logement">Souscription logement</option>
                            <option value="Projet personnalisé">Projet personnalisé</option>
                            <option value="Gestion locative">Gestion locative</option>
                            <option value="Autre demande">Autre demande</option>
                        </select>
                    </div>
                    <div class="field field-full">
                        <label for="cs-property">Logement qui vous intéresse</label>
                        <select id="cs-property" name="property_id">
                            <option value="">— Aucun en particulier —</option>
                            <?php foreach ($properties as $pr): ?><option value="<?= (int) $pr['id'] ?>"><?= e($pr['title']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field-full">
                        <label for="cs-message">Votre message</label>
                        <textarea id="cs-message" name="message" rows="4" placeholder="Bonjour, je souhaiterais..."></textarea>
                        <?= field_error('message') ?>
                    </div>
                    <div class="field field-full field-check">
                        <label class="checkbox"><input type="checkbox" name="consent" value="1" required><span>J’accepte que GELPAZ IMMO utilise mes données pour répondre à ma demande.</span></label>
                        <?= field_error('consent') ?>
                    </div>
                    <div class="field field-full">
                        <button class="btn btn-primary" type="submit"><span>Envoyer ma demande</span><?= icon('send') ?></button>
                    </div>
                    <div class="form-message field-full" role="status" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </div>
</section>
