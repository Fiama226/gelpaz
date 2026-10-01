<div class="adm-stats">
    <a class="adm-stat" href="<?= url('/admin/logements') ?>"><span class="adm-stat-icon"><?= icon('home') ?></span><div><strong><?= $stats['published'] ?><small>/<?= $stats['properties'] ?></small></strong><span>Logements en ligne</span></div></a>
    <a class="adm-stat" href="<?= url('/admin/messages?statut=non-lus') ?>"><span class="adm-stat-icon is-gold"><?= icon('inbox') ?></span><div><strong><?= $stats['unread'] ?></strong><span>Messages non lus</span></div></a>
    <a class="adm-stat" href="<?= url('/admin/souscriptions?statut=nouveau') ?>"><span class="adm-stat-icon is-green"><?= icon('clipboard-list') ?></span><div><strong><?= $stats['new_subscriptions'] ?></strong><span>Nouvelles souscriptions</span></div></a>
    <a class="adm-stat" href="<?= url('/admin/commentaires') ?>"><span class="adm-stat-icon is-navy"><?= icon('message-square') ?></span><div><strong><?= $stats['pending_comments'] ?></strong><span>Commentaires à modérer</span></div></a>
</div>
<?php if ($pendingMedia > 0): ?>
<div class="adm-alert adm-alert-info"><?= icon('download') ?><span><strong><?= $pendingMedia ?> images</strong> sont encore chargées depuis l’ancien site gelpaz.com. <a href="<?= url('/admin/outils') ?>">Importez-les sur votre hébergement</a> en un clic pour ne plus en dépendre.</span></div>
<?php endif; ?>
<div class="adm-grid-2">
    <section class="adm-card">
        <div class="adm-card-head"><h2>Derniers messages</h2><a class="adm-link" href="<?= url('/admin/messages') ?>">Tout voir</a></div>
        <?php if ($messages): ?>
        <ul class="adm-list">
            <?php foreach ($messages as $msg): ?>
            <li class="<?= (int) $msg['is_read'] ? '' : 'is-unread' ?>"><a href="<?= url('/admin/messages/' . $msg['id']) ?>">
                <span class="adm-avatar adm-avatar-sm"><?= e(initials($msg['name'])) ?></span>
                <span class="adm-list-body"><strong><?= e($msg['name']) ?></strong><small><?= e($msg['subject'] ?: 'Message') ?><?= $msg['property_title'] ? ' — ' . e($msg['property_title']) : '' ?></small></span>
                <span class="adm-pill adm-pill-<?= e($msg['type']) ?>"><?= e(ucfirst($msg['type'])) ?></span>
                <time><?= e(time_ago($msg['created_at'])) ?></time>
            </a></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?><p class="adm-empty">Aucun message pour le moment.</p><?php endif; ?>
    </section>
    <section class="adm-card">
        <div class="adm-card-head"><h2>Dernières souscriptions</h2><a class="adm-link" href="<?= url('/admin/souscriptions') ?>">Tout voir</a></div>
        <?php if ($subscriptions): ?>
        <ul class="adm-list">
            <?php foreach ($subscriptions as $s): ?>
            <li><a href="<?= url('/admin/souscriptions/' . $s['id']) ?>">
                <span class="adm-avatar adm-avatar-sm is-gold"><?= e(initials($s['full_name'])) ?></span>
                <span class="adm-list-body"><strong><?= e($s['full_name']) ?></strong><small><?= e($s['villa_type']) ?> · <?= e($s['payment_mode']) ?></small></span>
                <span class="adm-pill adm-pill-<?= e($s['status']) ?>"><?= e(subscription_status_label($s['status'])) ?></span>
                <time><?= e(time_ago($s['created_at'])) ?></time>
            </a></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?><p class="adm-empty">Aucune demande de souscription pour le moment.</p><?php endif; ?>
    </section>
</div>
<div class="adm-grid-2">
    <section class="adm-card">
        <div class="adm-card-head"><h2>Logements les plus consultés</h2><span class="adm-muted"><?= number_format($stats['views'], 0, ',', ' ') ?> vues au total</span></div>
        <ul class="adm-list">
            <?php foreach ($topProperties as $p): ?>
            <li><a href="<?= url('/admin/logements/' . $p['id']) ?>"><span class="adm-list-body"><strong><?= e($p['title']) ?></strong></span><span class="adm-pill"><?= (int) $p['views'] ?> vues</span></a></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="adm-card">
        <div class="adm-card-head"><h2>Actions rapides</h2></div>
        <div class="adm-quick">
            <a href="<?= url('/admin/logements/nouveau') ?>"><?= icon('house-plus') ?><span>Ajouter un logement</span></a>
            <a href="<?= url('/admin/actualites/nouveau') ?>"><?= icon('newspaper') ?><span>Publier une actualité</span></a>
            <a href="<?= url('/admin/partenaires/nouveau') ?>"><?= icon('handshake') ?><span>Ajouter un partenaire</span></a>
            <a href="<?= url('/admin/reglages') ?>"><?= icon('settings') ?><span>Modifier les coordonnées</span></a>
            <a href="<?= url('/admin/newsletter') ?>"><?= icon('mail') ?><span><?= $stats['newsletter'] ?> abonnés newsletter</span></a>
            <a href="<?= url('/') ?>" target="_blank" rel="noopener"><?= icon('external-link') ?><span>Voir le site</span></a>
        </div>
    </section>
</div>
