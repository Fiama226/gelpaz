<?php
$pageTitle = $post['title'];
$metaDescription = $post['excerpt'] ?: excerpt($post['content']);
$ogImage = post_cover($post, false);
$ogType = 'article';
$url = absolute_url('/actualites/' . $post['slug']);
$gallery = post_gallery($post);
$tagsList = array_filter(array_map('trim', explode(',', (string) $post['tags'])));
$extraSchema = ['@context' => 'https://schema.org', '@type' => 'NewsArticle', 'headline' => $post['title'], 'image' => [$ogImage],
    'datePublished' => date('c', strtotime($post['published_at'])), 'author' => ['@type' => 'Organization', 'name' => 'GELPAZ IMMO'],
    'publisher' => ['@type' => 'Organization', 'name' => 'GELPAZ IMMO', 'logo' => ['@type' => 'ImageObject', 'url' => absolute_url('assets/images/brand/icon-512.png')]]];
partial('breadcrumb', ['title' => 'Actualités', 'tag' => 'div', 'crumbs' => [['Actualités', '/actualites'], [$post['title'], null]], 'image' => 'assets/images/hero/hero-2.jpg']);
partial('alerts');
?>
<section class="section">
    <div class="container">
        <div class="sidebar-layout">
            <article class="post-details">
                <div class="post-details-media"><?= img($ogImage, $post['title'], '', ['loading' => 'eager', 'fetchpriority' => 'high']) ?></div>
                <ul class="post-details-meta">
                    <li><?= icon('calendar') ?><time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><?= date_fr($post['published_at']) ?></time></li>
                    <li><?= icon('user') ?><?= e($post['author'] ?: 'GELPAZ IMMO') ?></li>
                    <?php if ($post['category']): ?><li><?= icon('tag') ?><a href="<?= url('/actualites?categorie=' . rawurlencode($post['category'])) ?>"><?= e($post['category']) ?></a></li><?php endif; ?>
                    <li><?= icon('clock') ?><?= reading_time($post['content']) ?> min de lecture</li>
                    <li><?= icon('message-square') ?><?= plural($commentsCount, 'commentaire') ?></li>
                </ul>
                <h1><?= e($post['title']) ?></h1>
                <div class="prose"><?= sanitize_html($post['content']) ?></div>

                <?php if ($gallery): ?>
                <div class="post-gallery">
                    <?php foreach ($gallery as $i => $g): ?>
                    <a href="<?= e($g) ?>" data-lightbox="post" data-caption="<?= e($post['title']) ?>"><?= img($g, $post['title'] . ' — photo ' . ($i + 1)) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="post-footer">
                    <div class="post-tags">
                        <?php if ($tagsList): ?><strong>Mots-clés :</strong><?php foreach ($tagsList as $t): ?><a href="<?= url('/actualites?tag=' . rawurlencode($t)) ?>"><?= e($t) ?></a><?php endforeach; ?><?php endif; ?>
                    </div>
                    <div class="share-links">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($url) ?>" target="_blank" rel="noopener" aria-label="Partager sur Facebook"><?= icon('brand-facebook') ?></a>
                        <a href="https://wa.me/?text=<?= rawurlencode($post['title'] . ' — ' . $url) ?>" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp"><?= icon('brand-whatsapp') ?></a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($url) ?>" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn"><?= icon('brand-linkedin') ?></a>
                        <a href="https://twitter.com/intent/tweet?url=<?= rawurlencode($url) ?>&text=<?= rawurlencode($post['title']) ?>" target="_blank" rel="noopener" aria-label="Partager sur X"><?= icon('brand-x') ?></a>
                        <button type="button" data-copy="<?= e($url) ?>" aria-label="Copier le lien"><?= icon('link') ?></button>
                    </div>
                </div>

                <?php if ($prev || $next): ?>
                <nav class="post-nav" aria-label="Articles précédent et suivant">
                    <?php if ($prev): ?><a class="prev" href="<?= url('/actualites/' . $prev['slug']) ?>"><?= img(post_cover($prev), '') ?><div><small><?= icon('arrow-left') ?>Article précédent</small><strong><?= e(str_limit($prev['title'], 70)) ?></strong></div></a><?php else: ?><span></span><?php endif; ?>
                    <?php if ($next): ?><a class="next" href="<?= url('/actualites/' . $next['slug']) ?>"><?= img(post_cover($next), '') ?><div><small>Article suivant<?= icon('arrow-right') ?></small><strong><?= e(str_limit($next['title'], 70)) ?></strong></div></a><?php endif; ?>
                </nav>
                <?php endif; ?>

                <section class="comments" id="commentaires">
                    <h2><?= $commentsCount ? plural($commentsCount, 'commentaire') : 'Commentaires' ?></h2>
                    <?php if ($comments): ?>
                    <ul class="comment-list">
                        <?php foreach ($comments as $c): ?>
                        <li>
                            <div class="comment">
                                <span class="avatar avatar-lg"><?= e(initials($c['name'])) ?></span>
                                <div style="flex:1">
                                    <div class="comment-head"><strong><?= e($c['name']) ?></strong><small><?= date_fr($c['created_at'], 'datetime') ?></small></div>
                                    <p><?= nl2br(e($c['content'])) ?></p>
                                    <button class="reply-btn" type="button" data-reply="<?= (int) $c['id'] ?>" data-name="<?= e($c['name']) ?>"><?= icon('reply') ?>Répondre</button>
                                </div>
                            </div>
                            <?php if ($c['replies']): ?>
                            <ul class="comment-replies">
                                <?php foreach ($c['replies'] as $r): ?>
                                <li class="comment">
                                    <span class="avatar"><?= e(initials($r['name'])) ?></span>
                                    <div style="flex:1"><div class="comment-head"><strong><?= e($r['name']) ?></strong><small><?= date_fr($r['created_at'], 'datetime') ?></small></div><p><?= nl2br(e($r['content'])) ?></p></div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p>Soyez le premier à réagir à cet article.</p>
                    <?php endif; ?>
                    <div class="comment-form-wrap">
                        <h3 style="font-size:24px">Laisser un commentaire</h3>
                        <p>Votre adresse e-mail ne sera pas publiée. Les commentaires sont publiés après modération.</p>
                        <form id="comment-form" class="form-grid" action="<?= url('/actualites/' . $post['slug'] . '/commentaire') ?>" method="post" data-ajax-form novalidate>
                            <?= csrf_field() ?><?= antispam_fields() ?>
                            <input type="hidden" name="parent_id" value="">
                            <div class="replying-to field-full"><span>Réponse à <strong class="replying-name"></strong></span><button type="button" class="reply-btn" data-cancel-reply><?= icon('x') ?>Annuler</button></div>
                            <div class="field"><label for="c-name">Nom <span aria-hidden="true">*</span></label><input id="c-name" name="name" type="text" required autocomplete="name"><?= field_error('name') ?></div>
                            <div class="field"><label for="c-email">E-mail <span aria-hidden="true">*</span></label><input id="c-email" name="email" type="email" required autocomplete="email"><?= field_error('email') ?></div>
                            <div class="field field-full"><label for="c-content">Commentaire <span aria-hidden="true">*</span></label><textarea id="c-content" name="content" rows="5" required></textarea><?= field_error('content') ?></div>
                            <div class="field field-full"><button class="btn btn-primary" type="submit"><span>Publier mon commentaire</span><?= icon('send') ?></button></div>
                            <div class="form-message field-full" role="status" aria-live="polite"></div>
                        </form>
                    </div>
                </section>
            </article>

            <aside class="sidebar" aria-label="Barre latérale">
                <div class="widget">
                    <h3 class="widget-title">Rechercher</h3>
                    <form class="widget-search" action="<?= url('/actualites') ?>" method="get" role="search">
                        <label class="sr-only" for="ps-q">Rechercher</label>
                        <input id="ps-q" type="search" name="q" placeholder="Rechercher une actualité…">
                        <button type="submit" aria-label="Rechercher"><?= icon('search') ?></button>
                    </form>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Catégories</h3>
                    <ul class="widget-links">
                        <?php foreach ($categories as $c): ?>
                        <li><a class="<?= $c['category'] === $post['category'] ? 'is-active' : '' ?>" href="<?= url('/actualites?categorie=' . rawurlencode($c['category'])) ?>"><span><?= e($c['category']) ?></span><span class="count"><?= (int) $c['total'] ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Articles récents</h3>
                    <ul class="recent-posts">
                        <?php foreach ($recentPosts as $rp): ?>
                        <li class="recent-post">
                            <a class="recent-post-img" href="<?= url('/actualites/' . $rp['slug']) ?>" tabindex="-1"><?= img(post_cover($rp), $rp['title']) ?></a>
                            <div><small><?= icon('calendar') ?><?= date_fr($rp['published_at']) ?></small><h4><a href="<?= url('/actualites/' . $rp['slug']) ?>"><?= e(str_limit($rp['title'], 70)) ?></a></h4></div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php if ($tags): ?>
                <div class="widget">
                    <h3 class="widget-title">Mots-clés</h3>
                    <div class="tag-cloud"><?php foreach ($tags as $t): ?><a href="<?= url('/actualites?tag=' . rawurlencode($t)) ?>"><?= e($t) ?></a><?php endforeach; ?></div>
                </div>
                <?php endif; ?>
                <div class="widget-cta">
                    <span class="widget-cta-bg" style="background-image:url('<?= e(url('assets/images/hero/hero-1.jpg')) ?>')"></span>
                    <span class="call-box-icon"><?= icon('home') ?></span>
                    <h3>Devenez propriétaire</h3>
                    <p>Villas F3, F4 et F5 duplex disponibles à la Cité de l’Intégration.</p>
                    <a class="btn btn-gold btn-sm" href="<?= url('/souscription-logement') ?>"><span>Souscrire</span><?= icon('arrow-up-right') ?></a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section bg-smoke">
    <div class="container">
        <div class="section-head">
            <span class="sub-title"><?= bird() ?>À lire aussi</span>
            <h2 class="sec-title">Dans la même catégorie</h2>
        </div>
        <div class="post-grid"><?php foreach ($related as $rp): partial('post-card', ['post' => $rp]); endforeach; ?></div>
    </div>
</section>
<?php endif; ?>
