<?php
$baseUrl = '/admin/' . $m['key'];
$sortable = !empty($m['sortable']) && $q === '';
?>
<div class="adm-toolbar">
    <form class="adm-search" method="get" action="<?= url($baseUrl) ?>" role="search">
        <?= icon('search') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher…" aria-label="Rechercher">
    </form>
    <span class="adm-muted"><?= plural($pager['total'], 'élément') ?></span>
    <a class="adm-btn adm-btn-primary" href="<?= url($baseUrl . '/nouveau') ?>"><?= icon('plus') ?><span>Ajouter</span></a>
</div>
<div class="adm-card adm-card-flush">
    <?php if ($rows): ?>
    <div class="adm-table-wrap">
    <table class="adm-table" <?= $sortable ? 'data-sortable="' . e(url($baseUrl . '/ordre')) . '" data-offset="' . (int) $pager['offset'] . '"' : '' ?>>
        <thead><tr>
            <?php if ($sortable): ?><th class="adm-col-handle" aria-label="Ordre"></th><?php endif; ?>
            <?php foreach ($m['columns'] as [$col, $label]): ?><th><?= e($label) ?></th><?php endforeach; ?>
            <th class="adm-col-actions">Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr data-id="<?= (int) $r['id'] ?>">
                <?php if ($sortable): ?><td class="adm-col-handle"><span class="adm-handle" draggable="true" title="Glisser pour réordonner"><?= icon('grip') ?></span></td><?php endif; ?>
                <?php foreach ($m['columns'] as [$col, $label, $type]): $v = $r[$col] ?? ''; ?>
                <td data-label="<?= e($label) ?>">
                    <?php if ($type === 'image'):
                        $remoteCol = $col === 'logo' ? 'logo_remote' : 'image_remote';
                        $src = media_thumb((string) $v, (string) ($r['thumb_remote'] ?? ''), (string) ($r[$remoteCol] ?? ''), null); ?>
                        <?php if ($src): ?><img class="adm-thumb" src="<?= e($src) ?>" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.src='<?= e(url('assets/images/placeholder.svg')) ?>'"><?php else: ?><span class="adm-thumb adm-thumb-empty"><?= icon('image') ?></span><?php endif; ?>
                    <?php elseif ($type === 'title'): ?>
                        <a class="adm-row-title" href="<?= url($baseUrl . '/' . $r['id']) ?>"><?= e(str_limit((string) $v, 80)) ?></a>
                    <?php elseif ($type === 'toggle'): ?>
                        <button type="button" class="adm-switch<?= (int) $v ? ' is-on' : '' ?>" data-toggle-url="<?= e(url($baseUrl . '/' . $r['id'] . '/basculer')) ?>" data-field="<?= e($col) ?>" aria-pressed="<?= (int) $v ? 'true' : 'false' ?>" aria-label="<?= e($label) ?>"><span></span></button>
                    <?php elseif ($type === 'map'): ?>
                        <span class="adm-pill"><?= e($m['maps'][$col][$v] ?? $v) ?></span>
                    <?php elseif (str_starts_with($type, 'relation:')): ?>
                        <?= e($relations[$col][(string) $v] ?? '—') ?>
                    <?php elseif ($type === 'date'): ?>
                        <?= $v ? e(date_fr((string) $v, 'short')) : '—' ?>
                    <?php else: ?>
                        <?= e(str_limit((string) $v, 60)) ?>
                    <?php endif; ?>
                </td>
                <?php endforeach; ?>
                <td class="adm-col-actions">
                    <?php if (!empty($m['view']) && !empty($r['slug'])): ?><a class="adm-icon-btn" href="<?= url(str_replace('{slug}', (string) $r['slug'], $m['view'])) ?>" target="_blank" rel="noopener" title="Voir sur le site"><?= icon('eye') ?></a><?php endif; ?>
                    <a class="adm-icon-btn" href="<?= url($baseUrl . '/' . $r['id']) ?>" title="Modifier"><?= icon('edit') ?></a>
                    <form method="post" action="<?= url($baseUrl . '/' . $r['id'] . '/supprimer') ?>" data-confirm="Supprimer définitivement cet élément ?"><?= csrf_field() ?><button class="adm-icon-btn is-danger" type="submit" title="Supprimer"><?= icon('trash') ?></button></form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
    <div class="adm-empty-state"><?= icon($m['icon']) ?><p><?= $q !== '' ? 'Aucun résultat pour « ' . e($q) . ' ».' : 'Aucun élément pour le moment.' ?></p><a class="adm-btn adm-btn-primary" href="<?= url($baseUrl . '/nouveau') ?>"><?= icon('plus') ?><span>Ajouter</span></a></div>
    <?php endif; ?>
</div>
<?php if ($pager['total_pages'] > 1): ?>
<nav class="adm-pagination"><?php foreach ($pager['pages'] as $p): ?><?php if ($p === '…'): ?><span>…</span><?php else: ?><a class="<?= $p === $pager['current'] ? 'is-active' : '' ?>" href="<?= e(query_url(['page' => $p])) ?>"><?= $p ?></a><?php endif; ?><?php endforeach; ?></nav>
<?php endif; ?>
<?php if ($sortable): ?><p class="adm-hint"><?= icon('info') ?>Glissez-déposez les lignes à l’aide de la poignée pour modifier l’ordre d’affichage sur le site.</p><?php endif; ?>
