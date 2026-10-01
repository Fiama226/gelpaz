<div class="adm-toolbar">
    <div class="adm-tabs">
        <a class="<?= $status === '' ? 'is-active' : '' ?>" href="<?= url('/admin/souscriptions') ?>">Toutes</a>
        <?php foreach (['nouveau' => 'Nouvelles', 'en_cours' => 'En cours', 'traite' => 'Traitées', 'annule' => 'Annulées'] as $k => $l): ?>
        <a class="<?= $status === $k ? 'is-active' : '' ?>" href="<?= url('/admin/souscriptions?statut=' . $k) ?>"><?= e($l) ?> <em><?= (int) ($counts[$k] ?? 0) ?></em></a>
        <?php endforeach; ?>
    </div>
    <form class="adm-search" method="get" action="<?= url('/admin/souscriptions') ?>"><?= icon('search') ?><input type="search" name="q" value="<?= e($q) ?>" placeholder="Rechercher…" aria-label="Rechercher"></form>
    <a class="adm-btn adm-btn-ghost" href="<?= e(query_url(['export' => 'csv'])) ?>"><?= icon('download') ?><span>Exporter (CSV)</span></a>
</div>
<div class="adm-card adm-card-flush">
    <?php if ($rows): ?>
    <div class="adm-table-wrap"><table class="adm-table">
        <thead><tr><th>Client</th><th>Logement</th><th>Paiement</th><th>Résidence</th><th>Statut</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td data-label="Client"><a class="adm-row-title" href="<?= url('/admin/souscriptions/' . $r['id']) ?>"><?= e($r['full_name']) ?></a><small class="adm-sub"><?= e($r['phone']) ?></small></td>
            <td data-label="Logement"><?= e($r['villa_type']) ?><small class="adm-sub"><?= e(str_limit((string) $r['site'], 40)) ?></small></td>
            <td data-label="Paiement"><?= e($r['payment_mode']) ?></td>
            <td data-label="Résidence"><?= e(trim($r['city'] . ', ' . $r['country'], ', ') ?: '—') ?></td>
            <td data-label="Statut"><span class="adm-pill adm-pill-<?= e($r['status']) ?>"><?= e(subscription_status_label($r['status'])) ?></span></td>
            <td data-label="Date"><?= e(date_fr($r['created_at'], 'short')) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php else: ?><div class="adm-empty-state"><?= icon('clipboard-list') ?><p>Aucune demande de souscription.</p></div><?php endif; ?>
</div>
<?php if ($pager['total_pages'] > 1): ?><nav class="adm-pagination"><?php foreach ($pager['pages'] as $p): ?><?php if ($p === '…'): ?><span>…</span><?php else: ?><a class="<?= $p === $pager['current'] ? 'is-active' : '' ?>" href="<?= e(query_url(['page' => $p])) ?>"><?= $p ?></a><?php endif; ?><?php endforeach; ?></nav><?php endif; ?>
