<?php
/** @var array $testimonials */
if (!$testimonials) {
    return;
}
?>
<section class="section testimonials-section">
    <div class="testimonials-bg-bird" aria-hidden="true"><?= bird() ?></div>
    <div class="container">
        <div class="testimonials-grid">
            <div class="testimonials-intro" data-reveal="left">
                <span class="sub-title"><?= bird() ?>Témoignages</span>
                <h2 class="sec-title" data-split>Ce que disent nos résidents</h2>
                <p>Découvrez les témoignages de nos clients installés à la Cité de l’Intégration. Rejoignez-les et trouvez la maison de vos rêves.</p>
                <div class="trust-card">
                    <div class="trust-avatars">
                        <?php foreach (array_slice($testimonials, 0, 3) as $t): ?><span class="avatar"><?= e(initials($t['name'])) ?></span><?php endforeach; ?>
                        <span class="avatar avatar-plus"><?= icon('plus') ?></span>
                    </div>
                    <div>
                        <strong>Des familles comblées</strong>
                        <span>installées depuis 2021 à la Cité de l’Intégration</span>
                    </div>
                </div>
                <div class="slider-arrows">
                    <button class="slider-arrow testi-prev" type="button" aria-label="Témoignage précédent"><?= icon('arrow-left') ?></button>
                    <button class="slider-arrow testi-next" type="button" aria-label="Témoignage suivant"><?= icon('arrow-right') ?></button>
                </div>
            </div>
            <div class="testimonials-slider-wrap" data-reveal="right">
                <div class="swiper testimonials-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ($testimonials as $t): ?>
                        <div class="swiper-slide">
                            <figure class="testimonial-card">
                                <span class="testimonial-quote"><?= icon('quote') ?></span>
                                <blockquote>« <?= e($t['content']) ?> »</blockquote>
                                <figcaption>
                                    <span class="avatar avatar-lg"><?= e(initials($t['name'])) ?></span>
                                    <span><strong><?= e($t['name']) ?></strong><small><?= e($t['role']) ?></small></span>
                                </figcaption>
                            </figure>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="testi-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</section>
