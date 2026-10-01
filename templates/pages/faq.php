<?php
$pageTitle = 'Questions fréquentes';
$metaDescription = 'Souscription, modes de paiement, visites, diaspora, gestion locative : toutes les réponses à vos questions sur GELPAZ IMMO.';
$needsSwiper = false;
$cats = array_values(array_unique(array_filter(array_column($faqs, 'category'))));
$extraSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(static fn($f) => [
    '@type' => 'Question', 'name' => $f['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
], $faqs)];
partial('breadcrumb', ['title' => 'Questions fréquentes', 'crumbs' => [['FAQ', null]], 'image' => 'assets/images/about/about-2.jpg']);
?>
<section class="section">
    <div class="container">
        <div class="faq-layout">
            <div>
                <span class="sub-title"><?= bird() ?>FAQ</span>
                <h2 class="sec-title" data-split>Vos questions, nos réponses</h2>
                <div class="faq-cats" role="group" aria-label="Filtrer par thème">
                    <button class="chip is-active" type="button" data-faq-filter="*">Toutes</button>
                    <?php foreach ($cats as $c): ?><button class="chip" type="button" data-faq-filter="<?= e($c) ?>"><?= e($c) ?></button><?php endforeach; ?>
                </div>
                <div class="accordion" data-single>
                    <?php foreach ($faqs as $i => $f): ?>
                    <div class="accordion-item<?= $i === 0 ? ' is-open' : '' ?>" data-cat="<?= e($f['category']) ?>">
                        <button class="accordion-btn" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" id="faq-<?= (int) $f['id'] ?>">
                            <span class="accordion-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>.</span>
                            <span class="q"><?= e($f['question']) ?></span>
                            <span class="accordion-icon"><?= icon('plus') ?></span>
                        </button>
                        <div class="accordion-panel" role="region" aria-labelledby="faq-<?= (int) $f['id'] ?>">
                            <div class="accordion-content"><p><?= nl2br(e($f['answer'])) ?></p></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <aside class="sticky-side">
                <div class="widget-cta">
                    <span class="widget-cta-bg" style="background-image:url('<?= e(url('assets/images/about/about-2.jpg')) ?>')"></span>
                    <span class="call-box-icon"><?= icon('help-circle') ?></span>
                    <h3>Vous ne trouvez pas votre réponse ?</h3>
                    <p>Notre équipe vous répond rapidement par téléphone, WhatsApp ou e-mail.</p>
                    <a class="phone notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a>
                    <div class="btn-group" style="justify-content:center;gap:10px">
                        <a class="btn btn-gold btn-sm" href="<?= url('/contact') ?>"><span>Nous écrire</span><?= icon('mail') ?></a>
                        <a class="btn btn-whatsapp btn-sm" href="<?= e(whatsapp_url('Bonjour GELPAZ IMMO, j’ai une question :')) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>WhatsApp</span></a>
                    </div>
                </div>
                <div class="side-card">
                    <h3>Liens utiles</h3>
                    <ul class="widget-links">
                        <li><a href="<?= url('/souscription-logement') ?>"><span>Souscrire à une villa</span><?= icon('arrow-right') ?></a></li>
                        <li><a href="<?= url('/logements') ?>"><span>Voir nos logements</span><?= icon('arrow-right') ?></a></li>
                        <li><a href="<?= url('/nos-sites') ?>"><span>Découvrir nos sites</span><?= icon('arrow-right') ?></a></li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
