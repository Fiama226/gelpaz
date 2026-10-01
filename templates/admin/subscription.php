<div class="adm-toolbar"><a class="adm-btn adm-btn-ghost" href="<?= url('/admin/souscriptions') ?>"><?= icon('arrow-left') ?><span>Retour aux souscriptions</span></a></div>
<div class="adm-grid-2 adm-grid-wide">
    <section class="adm-card">
        <div class="adm-card-head"><h2><?= e($sub['villa_type']) ?></h2><span class="adm-pill adm-pill-<?= e($sub['status']) ?>"><?= e(subscription_status_label($sub['status'])) ?></span></div>
        <dl class="adm-dl adm-dl-2">
            <dt>Nom complet</dt><dd><?= e($sub['full_name']) ?></dd>
            <dt>Téléphone</dt><dd><a href="<?= e(phone_href($sub['phone'])) ?>"><?= e($sub['phone']) ?></a></dd>
            <dt>E-mail</dt><dd><?= $sub['email'] ? '<a href="mailto:' . e($sub['email']) . '">' . e($sub['email']) . '</a>' : '—' ?></dd>
            <dt>Résidence</dt><dd><?= e(trim($sub['city'] . ', ' . $sub['country'], ', ') ?: '—') ?></dd>
            <dt>Site souhaité</dt><dd><?= e($sub['site'] ?: '—') ?></dd>
            <dt>Mode de paiement</dt><dd><?= e($sub['payment_mode']) ?></dd>
            <dt>Reçue le</dt><dd><?= e(date_fr($sub['created_at'], 'datetime')) ?></dd>
        </dl>
        <?php if ($sub['message']): ?><h3>Message du client</h3><div class="adm-message-body"><?= nl2br(e($sub['message'])) ?></div><?php endif; ?>
        <div class="adm-form-actions">
            <a class="adm-btn adm-btn-primary" href="https://wa.me/<?= e(preg_replace('/\D/', '', $sub['phone'])) ?>?text=<?= rawurlencode('Bonjour ' . $sub['full_name'] . ', GELPAZ IMMO vous contacte au sujet de votre demande de souscription (' . $sub['villa_type'] . ').') ?>" target="_blank" rel="noopener"><?= icon('brand-whatsapp') ?><span>Contacter sur WhatsApp</span></a>
            <?php if ($sub['email']): ?><a class="adm-btn adm-btn-ghost" href="mailto:<?= e($sub['email']) ?>?subject=<?= rawurlencode('Votre demande de souscription — GELPAZ IMMO') ?>"><?= icon('mail') ?><span>Répondre par e-mail</span></a><?php endif; ?>
        </div>
    </section>
    <aside class="adm-card">
        <h2>Suivi de la demande</h2>
        <form method="post"><?= csrf_field() ?>
            <div class="adm-field"><label for="st">Statut</label><select id="st" name="status"><?php foreach (['nouveau' => 'Nouvelle', 'en_cours' => 'En cours', 'traite' => 'Traitée', 'annule' => 'Annulée'] as $k => $l): ?><option value="<?= $k ?>" <?= $sub['status'] === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></div>
            <div class="adm-field"><label for="notes">Notes internes</label><textarea id="notes" name="notes" rows="6" placeholder="Appel du…, visite prévue le…"><?= e($sub['notes']) ?></textarea></div>
            <button class="adm-btn adm-btn-primary adm-btn-block" type="submit"><?= icon('save') ?><span>Enregistrer</span></button>
        </form>
        <form method="post" style="margin-top:14px"><?= csrf_field() ?><button class="adm-btn adm-btn-danger adm-btn-sm adm-btn-block" name="action" value="delete" type="submit" data-confirm-click="Supprimer définitivement cette demande ?"><?= icon('trash') ?><span>Supprimer la demande</span></button></form>
    </aside>
</div>
