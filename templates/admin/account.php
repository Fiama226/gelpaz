<form method="post" class="adm-form" style="max-width:760px"><?= csrf_field() ?>
    <div class="adm-card">
        <h2 class="adm-section-title" style="margin-top:0">Mes informations</h2>
        <div class="adm-fields">
            <div class="adm-field adm-col-half<?= isset($errors['name']) ? ' has-error' : '' ?>"><label for="a-name">Nom</label><input id="a-name" name="name" value="<?= e($user['name']) ?>" required><?php if (isset($errors['name'])): ?><small class="adm-error"><?= e($errors['name']) ?></small><?php endif; ?></div>
            <div class="adm-field adm-col-half<?= isset($errors['email']) ? ' has-error' : '' ?>"><label for="a-email">E-mail (identifiant)</label><input id="a-email" type="email" name="email" value="<?= e($user['email']) ?>" required><?php if (isset($errors['email'])): ?><small class="adm-error"><?= e($errors['email']) ?></small><?php endif; ?></div>
        </div>
        <h2 class="adm-section-title">Changer de mot de passe</h2>
        <div class="adm-fields">
            <div class="adm-field adm-col-half<?= isset($errors['new_password']) ? ' has-error' : '' ?>"><label for="a-new">Nouveau mot de passe</label><input id="a-new" type="password" name="new_password" autocomplete="new-password"><small class="adm-help">Laisser vide pour conserver l’actuel. 8 caractères minimum.</small><?php if (isset($errors['new_password'])): ?><small class="adm-error"><?= e($errors['new_password']) ?></small><?php endif; ?></div>
            <div class="adm-field adm-col-half<?= isset($errors['new_password_confirm']) ? ' has-error' : '' ?>"><label for="a-new2">Confirmation</label><input id="a-new2" type="password" name="new_password_confirm" autocomplete="new-password"><?php if (isset($errors['new_password_confirm'])): ?><small class="adm-error"><?= e($errors['new_password_confirm']) ?></small><?php endif; ?></div>
            <div class="adm-field adm-col-full<?= isset($errors['current_password']) ? ' has-error' : '' ?>"><label for="a-cur">Mot de passe actuel <em>*</em></label><input id="a-cur" type="password" name="current_password" required autocomplete="current-password"><small class="adm-help">Requis pour valider toute modification.</small><?php if (isset($errors['current_password'])): ?><small class="adm-error"><?= e($errors['current_password']) ?></small><?php endif; ?></div>
        </div>
    </div>
    <div class="adm-form-actions"><button class="adm-btn adm-btn-primary adm-btn-lg" type="submit"><?= icon('save') ?><span>Enregistrer</span></button></div>
</form>
