<div class="adm-toolbar"><a class="adm-btn adm-btn-ghost" href="<?= url('/admin/messages') ?>"><?= icon('arrow-left') ?><span>Retour aux messages</span></a></div>
<div class="adm-grid-2 adm-grid-wide">
    <section class="adm-card">
        <div class="adm-card-head"><h2><?= e($msg['subject'] ?: 'Message') ?></h2><span class="adm-pill adm-pill-<?= e($msg['type']) ?>"><?= e(ucfirst($msg['type'])) ?></span></div>
        <div class="adm-message-body"><?= nl2br(e($msg['message'] ?: '(aucun message)')) ?></div>
        <div class="adm-form-actions">
            <?php if ($msg['email']): ?><a class="adm-btn adm-btn-primary" href="mailto:<?= e($msg['email']) ?>?subject=<?= rawurlencode('Re : ' . ($msg['subject'] ?: 'Votre demande')) ?>"><?= icon('reply') ?><span>Répondre par e-mail</span></a><?php endif; ?>
            <?php if ($msg['phone']): ?><a class="adm-btn adm-btn-ghost" href="<?= e(phone_href($msg['phone'])) ?>"><?= icon('phone') ?><span>Appeler</span></a>
            <a class="adm-btn adm-btn-ghost" href="https://wa.me/<?= e(preg_replace('/\D/', '', $msg['phone'])) ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>WhatsApp</span></a><?php endif; ?>
        </div>
    </section>
    <aside class="adm-card">
        <h2>Coordonnées</h2>
        <dl class="adm-dl">
            <dt>Nom</dt><dd><?= e($msg['name']) ?></dd>
            <dt>E-mail</dt><dd><?= $msg['email'] ? '<a href="mailto:' . e($msg['email']) . '">' . e($msg['email']) . '</a>' : '—' ?></dd>
            <dt>Téléphone</dt><dd><?= e($msg['phone'] ?: '—') ?></dd>
            <?php if ($msg['property_title']): ?><dt>Logement</dt><dd><a href="<?= url('/logements/' . $msg['property_slug']) ?>" target="_blank" rel="noopener"><?= e($msg['property_title']) ?></a></dd><?php endif; ?>
            <?php if ($msg['visit_date']): ?><dt>Visite souhaitée</dt><dd><?= e(date_fr($msg['visit_date'])) ?></dd><?php endif; ?>
            <dt>Reçu le</dt><dd><?= e(date_fr($msg['created_at'], 'datetime')) ?></dd>
            <dt>Adresse IP</dt><dd><?= e($msg['ip']) ?></dd>
        </dl>
        <form method="post" class="adm-inline-actions"><?= csrf_field() ?>
            <button class="adm-btn adm-btn-ghost adm-btn-sm" name="action" value="unread" type="submit"><?= icon('mail') ?><span>Marquer non lu</span></button>
            <button class="adm-btn adm-btn-danger adm-btn-sm" name="action" value="delete" type="submit" data-confirm-click="Supprimer ce message ?"><?= icon('trash') ?><span>Supprimer</span></button>
        </form>
    </aside>
</div>
