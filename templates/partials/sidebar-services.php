<aside class="sidebar" aria-label="Barre latérale">
    <div class="widget">
        <h3 class="widget-title">Rechercher</h3>
        <form class="widget-search" action="<?= url('/logements') ?>" method="get" role="search">
            <label class="sr-only" for="side-q">Rechercher un logement</label>
            <input id="side-q" type="search" name="q" placeholder="Rechercher un logement…">
            <button type="submit" aria-label="Rechercher"><?= icon('search') ?></button>
        </form>
    </div>
    <div class="widget">
        <h3 class="widget-title">Nos activités</h3>
        <ul class="widget-links">
            <?php foreach ($services as $s): ?>
            <li><a class="<?= ($current ?? null) === $s['slug'] ? 'is-active' : '' ?>" href="<?= url('/nos-activites/' . $s['slug']) ?>"><span><?= e($s['title']) ?></span><?= icon('arrow-right') ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="widget-cta">
        <span class="widget-cta-bg" style="background-image:url('<?= e(url('assets/images/hero/hero-3.jpg')) ?>')"></span>
        <span class="call-box-icon"><?= icon('phone-call') ?></span>
        <h3>Besoin d’aide ?</h3>
        <p>Un conseiller GELPAZ IMMO vous répond et vous accompagne dans votre projet.</p>
        <a class="phone notranslate" translate="no" href="<?= e(phone_href((string) setting('phone'))) ?>"><?= e(setting('phone')) ?></a>
        <a class="btn btn-gold btn-sm" href="<?= url('/contact') ?>"><span>Nous écrire</span><?= icon('arrow-up-right') ?></a>
    </div>
    <?php if (!empty($recentPosts)): ?>
    <div class="widget">
        <h3 class="widget-title">Dernières actualités</h3>
        <ul class="recent-posts">
            <?php foreach ($recentPosts as $rp): ?>
            <li class="recent-post">
                <a class="recent-post-img" href="<?= url('/actualites/' . $rp['slug']) ?>" tabindex="-1"><?= img(post_cover($rp), $rp['title']) ?></a>
                <div><small><?= icon('calendar') ?><?= date_fr($rp['published_at']) ?></small><h4><a href="<?= url('/actualites/' . $rp['slug']) ?>"><?= e(str_limit($rp['title'], 70)) ?></a></h4></div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</aside>
