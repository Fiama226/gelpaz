<?php
/** @var array $post */
$url = url('/actualites/' . $post['slug']);
?>
<article class="post-card">
    <a class="post-media" href="<?= $url ?>">
        <?= img(post_cover($post), $post['title'], 'post-img') ?>
        <span class="post-date"><strong><?= date_fr($post['published_at'], 'day') ?></strong><small><?= date_fr($post['published_at'], 'month_year') ?></small></span>
    </a>
    <div class="post-body">
        <ul class="post-meta">
            <li><?= icon('user') ?><?= e($post['author'] ?: 'GELPAZ IMMO') ?></li>
            <?php if ($post['category']): ?><li><?= icon('tag') ?><a href="<?= url('/actualites?categorie=' . rawurlencode($post['category'])) ?>"><?= e($post['category']) ?></a></li><?php endif; ?>
        </ul>
        <h3 class="post-title"><a href="<?= $url ?>"><?= e($post['title']) ?></a></h3>
        <?php if (!empty($showExcerpt)): ?><p class="post-excerpt"><?= e(str_limit((string) $post['excerpt'], 130)) ?></p><?php endif; ?>
        <a class="link-arrow" href="<?= $url ?>"><span>Lire la suite</span><?= icon('arrow-right') ?></a>
    </div>
</article>
