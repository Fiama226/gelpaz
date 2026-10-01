<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex">
<title>Installation — GELPAZ IMMO</title>
<link rel="icon" href="<?= url('assets/images/brand/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<style>.inst{max-width:880px;margin:0 auto;padding:40px 18px 80px}.inst-head{display:flex;align-items:center;gap:18px;margin-bottom:26px}.inst-head img{width:120px}.req{list-style:none;padding:0;margin:0;display:grid;gap:8px}.req li{display:flex;align-items:center;gap:10px}.req .ok{color:#12A36A}.req .ko{color:#E0473E}.inst pre{white-space:pre-wrap;background:#0A1B33;color:#E6C77A;padding:18px;border-radius:12px;font-size:12.5px;overflow:auto}[data-db="sqlite"] .mysql-only{display:none}</style>
</head>
<body>
<div class="inst">
    <div class="inst-head"><img src="<?= url('assets/images/brand/logo-gelpaz.svg') ?>" alt="GELPAZ IMMO"><div><h1 style="margin:0">Installation du site</h1><p class="adm-muted" style="margin:0">Quelques informations et votre site GELPAZ IMMO est prêt.</p></div></div>
    <?php foreach ($errors as $err): ?><div class="adm-alert adm-alert-error"><?= icon('alert-triangle') ?><span><?= e($err) ?></span></div><?php endforeach; ?>
    <?php if ($generated): ?>
    <div class="adm-card"><h2>Dernière étape : créer le fichier de configuration</h2><p>La base est prête mais le dossier <code>config/</code> n’est pas accessible en écriture. Créez le fichier <code>config/config.php</code> avec le contenu ci-dessous, puis <a href="<?= url('/admin/connexion') ?>">connectez-vous</a>.</p><pre><?= e($generated) ?></pre></div>
    <?php else: ?>
    <div class="adm-card">
        <h2>1. Vérification du serveur</h2>
        <ul class="req"><?php foreach ($requirements as $label => $ok): ?><li><?= icon($ok ? 'check-circle' : 'x-circle', $ok ? 'ok' : 'ko') ?><span><?= e($label) ?></span></li><?php endforeach; ?></ul>
    </div>
    <form method="post" class="adm-form" id="inst-form" data-db="<?= e($input['driver']) ?>">
        <?= csrf_field() ?>
        <div class="adm-card">
            <h2>2. Base de données</h2>
            <div class="adm-fields">
                <div class="adm-field adm-col-full"><label>Type de base</label>
                    <select name="driver" onchange="document.getElementById('inst-form').dataset.db=this.value">
                        <option value="mysql" <?= $input['driver'] === 'mysql' ? 'selected' : '' ?>>MySQL / MariaDB (recommandé)</option>
                        <option value="sqlite" <?= $input['driver'] === 'sqlite' ? 'selected' : '' ?>>SQLite (fichier local, sans serveur)</option>
                    </select></div>
                <div class="adm-field adm-col-half mysql-only"><label>Serveur</label><input name="host" value="<?= e($input['host']) ?>"></div>
                <div class="adm-field adm-col-half mysql-only"><label>Port</label><input name="port" value="<?= e($input['port']) ?>"></div>
                <div class="adm-field adm-col-third mysql-only"><label>Nom de la base</label><input name="database" value="<?= e($input['database']) ?>"></div>
                <div class="adm-field adm-col-third mysql-only"><label>Utilisateur</label><input name="username" value="<?= e($input['username']) ?>" autocomplete="off"></div>
                <div class="adm-field adm-col-third mysql-only"><label>Mot de passe</label><input type="password" name="password" value="" autocomplete="new-password"></div>
            </div>
        </div>
        <div class="adm-card">
            <h2>3. Compte administrateur</h2>
            <div class="adm-fields">
                <div class="adm-field adm-col-half"><label>Nom</label><input name="admin_name" value="<?= e($input['admin_name']) ?>"></div>
                <div class="adm-field adm-col-half"><label>E-mail (identifiant)</label><input type="email" name="admin_email" value="<?= e($input['admin_email']) ?>" required></div>
                <div class="adm-field adm-col-half"><label>Mot de passe (8 caractères min.)</label><input type="password" name="admin_password" required minlength="8" autocomplete="new-password"></div>
                <div class="adm-field adm-col-half"><label>Confirmation</label><input type="password" name="admin_password_confirm" required minlength="8" autocomplete="new-password"></div>
            </div>
        </div>
        <div class="adm-card">
            <h2>4. Envoi des e-mails (formulaires → infos@gelpaz.com)</h2>
            <div class="adm-fields">
                <div class="adm-field adm-col-half"><label>Méthode</label><select name="mail_driver"><option value="mail">Fonction mail() de l’hébergeur</option><option value="smtp" <?= $input['mail_driver'] === 'smtp' ? 'selected' : '' ?>>Serveur SMTP (recommandé)</option></select></div>
                <div class="adm-field adm-col-half"><label>Adresse d’expédition</label><input type="email" name="from_email" value="<?= e($input['from_email']) ?>"></div>
                <div class="adm-field adm-col-third"><label>Serveur SMTP</label><input name="smtp_host" value="<?= e($input['smtp_host']) ?>" placeholder="mail.gelpaz.com"></div>
                <div class="adm-field adm-col-third"><label>Port</label><input name="smtp_port" value="<?= e($input['smtp_port']) ?>"></div>
                <div class="adm-field adm-col-third"><label>Chiffrement</label><select name="smtp_encryption"><option value="tls">TLS (587)</option><option value="ssl" <?= $input['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>SSL (465)</option><option value="" <?= $input['smtp_encryption'] === '' ? 'selected' : '' ?>>Aucun</option></select></div>
                <div class="adm-field adm-col-half"><label>Utilisateur SMTP</label><input name="smtp_username" value="<?= e($input['smtp_username']) ?>" autocomplete="off"></div>
                <div class="adm-field adm-col-half"><label>Mot de passe SMTP</label><input type="password" name="smtp_password" autocomplete="new-password"></div>
            </div>
            <p class="adm-hint"><?= icon('info') ?>Ces paramètres restent modifiables dans <code>config/config.php</code>.</p>
        </div>
        <div class="adm-form-actions"><button class="adm-btn adm-btn-primary adm-btn-lg" type="submit"><?= icon('check') ?><span>Installer le site</span></button><span class="adm-muted">Les tables seront créées et tout le contenu de GELPAZ IMMO importé automatiquement.</span></div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
