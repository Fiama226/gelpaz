<div class="adm-toolbar">
    <div class="adm-tabs">
        <a class="<?= $status === 'pending' ? 'is-active' : '' ?>" href="<?= url('/admin/commentaires') ?>">À modérer</a>
        <a class="<?= $status === 'approved' ? 'is-active' : '' ?>" href="<?= url('/admin/commentaires?statut=approved') ?>">Publiés</a>
        <a class="<?= $status === 'tous' ? 'is-active' : '' ?>" href="<?= url('/admin/commentaires?statut=tous') ?>">Tous</a>
    </div>
</div>
<?php if ($rows): ?>
<div class="adm-comments">
    <?php foreach ($rows as $c): ?>
    <article class="adm-card adm-comment">
        <div class="adm-comment-head"><span class="adm-avatar adm-avatar-sm"><?= e(initials($c['name'])) ?></span><div><strong><?= e($c['name']) ?></strong> <small><?= e($c['email']) ?> · <?= e(date_fr($c['created_at'], 'datetime')) ?></small><small class="adm-sub">sur « <a href="<?= url('/actualites/' . $c['post_slug']) ?>" target="_blank" rel="noopener"><?= e((string) $c['post_title']) ?></a> »<?= $c['parent_id'] ? ' (réponse)' : '' ?></small></div>
        <span class="adm-pill adm-pill-<?= $c['status'] === 'approved' ? 'traite' : 'nouveau' ?>"><?= $c['status'] === 'approved' ? 'Publié' : 'En attente' ?></span></div>
        <p><?= nl2br(e($c['content'])) ?></p>
        <form method="post" class="adm-inline-actions"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
            <?php if ($c['status'] !== 'approved'): ?><button class="adm-btn adm-btn-primary adm-btn-sm" name="action" value="approve"><?= icon('check') ?><span>Approuver</span></button><?php else: ?><button class="adm-btn adm-btn-ghost adm-btn-sm" name="action" value="pending"><?= icon('eye-off') ?><span>Dépublier</span></button><?php endif; ?>
            <button class="adm-btn adm-btn-danger adm-btn-sm" name="action" value="delete" data-confirm-click="Supprimer ce commentaire ?"><?= icon('trash') ?><span>Supprimer</span></button>
        </form>
    </article>
    <?php endforeach; ?>
</div>
<?php else: ?><div class="adm-card"><div class="adm-empty-state"><?= icon('message-square') ?><p>Aucun commentaire dans cette catégorie.</p></div></div><?php endif; ?>
