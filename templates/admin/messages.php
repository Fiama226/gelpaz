<div class="adm-toolbar">
    <div class="adm-tabs">
        <a class="<?= $type === '' && $status === '' ? 'is-active' : '' ?>" href="<?= url('/admin/messages') ?>">Tous</a>
        <a class="<?= $status === 'non-lus' ? 'is-active' : '' ?>" href="<?= url('/admin/messages?statut=non-lus') ?>">Non lus</a>
        <a class="<?= $type === 'contact' ? 'is-active' : '' ?>" href="<?= url('/admin/messages?type=contact') ?>">Contact</a>
        <a class="<?= $type === 'visite' ? 'is-active' : '' ?>" href="<?= url('/admin/messages?type=visite') ?>">Visites</a>
    </div>
    <form class="adm-search" method="get" action="<?= url('/admin/messages') ?>"><?= icon('search') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher…" aria-label="Rechercher"></form>
    <a class="adm-btn adm-btn-ghost" href="<?= e(query_url(['export' => 'csv'])) ?>"><?= icon('download') ?><span>Exporter (CSV)</span></a>
</div>
<form method="post" class="adm-card adm-card-flush" data-bulk>
    <?= csrf_field() ?>
    <?php if ($rows): ?>
    <div class="adm-bulk-bar"><label class="adm-check adm-check-sm"><input type="checkbox" data-check-all><span class="adm-check-box"></span><span>Tout sélectionner</span></label>
        <select name="action" aria-label="Action groupée"><option value="read">Marquer comme lu</option><option value="unread">Marquer comme non lu</option><option value="delete">Supprimer</option></select>
        <button class="adm-btn adm-btn-ghost adm-btn-sm" type="submit" data-confirm-bulk>Appliquer</button></div>
    <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th></th><th>Expéditeur</th><th>Objet</th><th>Type</th><th>Reçu</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
        <tr class="<?= (int) $r['is_read'] ? '' : 'is-unread' ?>">
            <td><label class="adm-check adm-check-sm"><input type="checkbox" name="ids[]" value="<?= (int) $r['id'] ?>"><span class="adm-check-box"></span></label></td>
            <td data-label="Expéditeur"><a class="adm-row-title" href="<?= url('/admin/messages/' . $r['id']) ?>"><?= e($r['name']) ?></a><small class="adm-sub"><?= e($r['email'] ?: $r['phone']) ?></small></td>
            <td data-label="Objet"><?= e(str_limit((string) $r['subject'], 50)) ?><?php if ($r['property_title']): ?><small class="adm-sub"><?= icon('home') ?><?= e($r['property_title']) ?></small><?php endif; ?></td>
            <td data-label="Type"><span class="adm-pill adm-pill-<?= e($r['type']) ?>"><?= e(ucfirst($r['type'])) ?></span></td>
            <td data-label="Reçu"><?= e(date_fr($r['created_at'], 'datetime')) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php else: ?><div class="adm-empty-state"><?= icon('inbox') ?><p>Aucun message.</p></div><?php endif; ?>
</form>
<?php if ($pager['total_pages'] > 1): ?><nav class="adm-pagination"><?php foreach ($pager['pages'] as $p): ?><?php if ($p === '…'): ?><span>…</span><?php else: ?><a class="<?= $p === $pager['current'] ? 'is-active' : '' ?>" href="<?= e(query_url(['page' => $p])) ?>"><?= $p ?></a><?php endif; ?><?php endforeach; ?></nav><?php endif; ?>
