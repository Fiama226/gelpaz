<div class="adm-grid-2">
    <section class="adm-card">
        <div class="adm-card-head"><h2>Importer les images de l’ancien site</h2><?= $pending ? '<span class="adm-pill adm-pill-nouveau">' . $pending . ' en attente</span>' : '<span class="adm-pill adm-pill-traite">Terminé</span>' ?></div>
        <p>Les photos des logements, actualités, sites et partenaires reprises de <strong>gelpaz.com</strong> sont pour l’instant affichées depuis l’ancien site. Cet outil les télécharge, les redimensionne et les enregistre sur votre hébergement (dossier <code>uploads/</code>).</p>
        <div class="adm-progress" data-import-progress hidden><div class="adm-progress-bar"><span></span></div><p class="adm-muted" data-import-status></p></div>
        <button class="adm-btn adm-btn-primary" type="button" data-import-start="<?= e(url('/admin/outils/import')) ?>" data-total="<?= (int) $pending ?>" <?= $pending ? '' : 'disabled' ?>><?= icon('download') ?><span>Lancer l’importation</span></button>
        <ul class="adm-import-errors" data-import-errors></ul>
        <p class="adm-hint"><?= icon('info') ?>Vous pouvez aussi lancer l’import en ligne de commande : <code>php tools/import-media.php</code></p>
    </section>
    <section class="adm-card">
        <div class="adm-card-head"><h2>Tester l’envoi des e-mails</h2></div>
        <?php if ($mailResult): ?><div class="adm-alert adm-alert-<?= $mailResult['ok'] ? 'success' : 'error' ?>"><?= icon($mailResult['ok'] ? 'check-circle' : 'alert-triangle') ?><span><?= e($mailResult['message']) ?></span></div><?php endif; ?>
        <p>Les formulaires du site envoient une notification à <strong><?= e(setting('contact_recipient', setting('email'))) ?></strong>. Vérifiez que l’envoi fonctionne :</p>
        <form method="post" class="adm-inline-form"><?= csrf_field() ?><input type="hidden" name="action" value="test_mail">
            <input type="email" name="to" value="<?= e(setting('contact_recipient', setting('email'))) ?>" required aria-label="Destinataire">
            <button class="adm-btn adm-btn-primary" type="submit"><?= icon('send') ?><span>Envoyer un test</span></button>
        </form>
        <h3 style="margin-top:28px">Informations système</h3>
        <dl class="adm-dl"><?php foreach ($system as $k => $v): ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endforeach; ?></dl>
    </section>
</div>
