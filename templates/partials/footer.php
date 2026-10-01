<?php
$socials = $GLOBALS['__socials'] ?? [];
?>
<section class="footer-cta">
    <div class="container">
        <div class="footer-cta-inner" data-reveal="up">
            <div class="footer-cta-bird"><?= bird() ?></div>
            <div class="footer-cta-text">
                <span class="sub-title sub-title-light"><?= bird() ?>Prêt à devenir propriétaire ?</span>
                <h2>Votre future villa vous attend à la Cité de l’Intégration</h2>
            </div>
            <div class="footer-cta-actions">
                <a class="btn btn-gold" href="<?= url('/souscription-logement') ?>"><span>Souscrire maintenant</span><?= icon('arrow-up-right') ?></a>
                <a class="btn btn-outline-light" href="<?= e(whatsapp_url('Bonjour GELPAZ IMMO, je souhaite être recontacté(e) au sujet de vos villas.')) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>WhatsApp</span></a>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="footer-bg-bird" aria-hidden="true"><?= bird() ?></div>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-about">
                <a href="<?= url('/') ?>" class="footer-logo"><img src="<?= url('assets/images/brand/logo-gelpaz-white.svg') ?>" alt="GELPAZ IMMO-SA" width="170" height="106" loading="lazy"></a>
                <p>Promoteur immobilier depuis 1987, GELPAZ IMMO conçoit des cités modernes et des villas de qualité pour rendre accessible un logement décent au Burkina Faso.</p>
                <p class="footer-slogan"><strong>GELPAZ IMMO, « La différence ! »</strong></p>
                <div class="social-links social-links-lg">
                    <?php foreach ($socials as $k => $link): ?>
                    <a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($k)) ?>"><?= icon('brand-' . $k) ?></a>
                    <?php endforeach; ?>
                    <a href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?= icon('brand-whatsapp') ?></a>
                </div>
            </div>
            <div class="footer-col">
                <h3 class="footer-title">Liens utiles</h3>
                <ul class="footer-links">
                    <li><a href="<?= url('/a-propos') ?>">Qui sommes-nous ?</a></li>
                    <li><a href="<?= url('/missions-visions-valeurs') ?>">Missions – Visions – Valeurs</a></li>
                    <li><a href="<?= url('/nos-offres-immobilieres') ?>">Nos offres immobilières</a></li>
                    <li><a href="<?= url('/nos-activites') ?>">Nos activités</a></li>
                    <li><a href="<?= url('/actualites') ?>">Actualités</a></li>
                    <li><a href="<?= url('/faq') ?>">Questions fréquentes</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3 class="footer-title">Nos logements</h3>
                <ul class="footer-links">
                    <li><a href="<?= url('/logements?categorie=f3-moyen-standing') ?>">Villas F3 moyen standing</a></li>
                    <li><a href="<?= url('/logements?categorie=f4-moyen-standing') ?>">Villas F4 moyen standing</a></li>
                    <li><a href="<?= url('/logements?categorie=f5-duplex-haut-standing') ?>">F5 duplex haut standing</a></li>
                    <li><a href="<?= url('/logements?type=location') ?>">Logements à louer</a></li>
                    <li><a href="<?= url('/nos-sites') ?>">Nos sites</a></li>
                    <li><a href="<?= url('/souscription-logement') ?>">Souscription logement</a></li>
                </ul>
            </div>
            <div class="footer-col footer-contact">
                <h3 class="footer-title">Nous joindre</h3>
                <ul class="footer-contact-list">
                    <li><?= icon('map-pin') ?><span><?= e(setting('address')) ?></span></li>
                    <li><?= icon('phone') ?><span><a class="notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a><br><a class="notranslate" translate="no" href="<?= e(whatsapp_url()) ?>" target="_blank" rel="noopener"><?= e(setting('phone_mobile')) ?></a> (WhatsApp)</span></li>
                    <li><?= icon('mail') ?><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                    <li><?= icon('clock') ?><span><?= e(setting('opening_hours')) ?></span></li>
                </ul>
                <form class="newsletter-form" action="<?= url('/formulaire/newsletter') ?>" method="post" data-ajax-form novalidate>
                    <?= csrf_field() ?><?= antispam_fields() ?>
                    <input type="hidden" name="_back" value="<?= e(current_path()) ?>">
                    <label for="nl-email" class="footer-newsletter-label">Recevez nos actualités et nouvelles offres</label>
                    <div class="newsletter-field">
                        <input id="nl-email" type="email" name="email" placeholder="Votre adresse e-mail" required autocomplete="email">
                        <button type="submit" aria-label="S’inscrire"><?= icon('send') ?></button>
                    </div>
                    <div class="form-message" role="status" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>© <?= date('Y') ?> <?= e(setting('company_name', 'GELPAZ IMMO SA')) ?>. Tous droits réservés.</p>
            <ul>
                <li><a href="<?= url('/mentions-legales') ?>">Mentions légales</a></li>
                <li><a href="<?= url('/mentions-legales#confidentialite') ?>">Confidentialité</a></li>
                <li><a href="<?= url('/sitemap.xml') ?>">Plan du site</a></li>
                <li><a href="<?= url('/contact') ?>">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>
