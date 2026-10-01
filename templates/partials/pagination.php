<?php
/** @var array $pager */
if (($pager['total_pages'] ?? 1) <= 1) {
    return;
}
$cur = $pager['current'];
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($cur > 1): ?>
    <a class="page-link page-arrow" href="<?= e(query_url(['page' => $cur - 1 > 1 ? $cur - 1 : null])) ?>" aria-label="Page précédente"><?= icon('arrow-left') ?></a>
    <?php endif; ?>
    <?php foreach ($pager['pages'] as $p): ?>
        <?php if ($p === '…'): ?>
        <span class="page-link page-dots">…</span>
        <?php elseif ($p === $cur): ?>
        <span class="page-link is-active" aria-current="page"><?= str_pad((string) $p, 2, '0', STR_PAD_LEFT) ?></span>
        <?php else: ?>
        <a class="page-link" href="<?= e(query_url(['page' => $p > 1 ? $p : null])) ?>"><?= str_pad((string) $p, 2, '0', STR_PAD_LEFT) ?></a>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($cur < $pager['total_pages']): ?>
    <a class="page-link page-arrow" href="<?= e(query_url(['page' => $cur + 1])) ?>" aria-label="Page suivante"><?= icon('arrow-right') ?></a>
    <?php endif; ?>
</nav>
