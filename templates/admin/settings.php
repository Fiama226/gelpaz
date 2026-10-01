<form method="post" class="adm-form"><?= csrf_field() ?>
    <?php foreach ($groups as $group => $fields): ?>
    <div class="adm-card">
        <h2 class="adm-section-title" style="margin-top:0"><?= e($group) ?></h2>
        <div class="adm-fields">
            <?php foreach ($fields as [$key, $label, $type]): $v = $values[$key] ?? ''; ?>
            <div class="adm-field adm-col-<?= $type === 'textarea' ? 'full' : 'half' ?>">
                <?php if ($type === 'checkbox'): ?>
                <label class="adm-check"><input type="checkbox" name="<?= e($key) ?>" value="1" <?= $v === '1' ? 'checked' : '' ?>><span class="adm-check-box"></span><span><?= e($label) ?></span></label>
                <?php elseif ($type === 'textarea'): ?>
                <label for="s-<?= e($key) ?>"><?= e($label) ?></label><textarea id="s-<?= e($key) ?>" name="<?= e($key) ?>" rows="3"><?= e($v) ?></textarea>
                <?php else: ?>
                <label for="s-<?= e($key) ?>"><?= e($label) ?></label><input id="s-<?= e($key) ?>" type="<?= in_array($type, ['email', 'url', 'number'], true) ? $type : 'text' ?>" name="<?= e($key) ?>" value="<?= e($v) ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <p class="adm-hint"><?= icon('info') ?>Les paramètres techniques d’envoi des e-mails (serveur SMTP) se trouvent dans le fichier <code>config/config.php</code>. Testez l’envoi depuis la page Outils.</p>
    <div class="adm-form-actions"><button class="adm-btn adm-btn-primary adm-btn-lg" type="submit"><?= icon('save') ?><span>Enregistrer les réglages</span></button></div>
</form>
