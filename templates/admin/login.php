<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">
<title>Connexion — Administration GELPAZ IMMO</title>
<link rel="icon" href="<?= url('assets/images/brand/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="adm-login-page">
<div class="adm-login">
    <div class="adm-login-visual" style="background-image:url('<?= e(url('assets/images/hero/hero-3.jpg')) ?>')">
        <div><img src="<?= url('assets/images/brand/logo-gelpaz-white.svg') ?>" alt="GELPAZ IMMO" width="180" height="113"><p>Espace d’administration du site<br><strong>GELPAZ IMMO, « La différence ! »</strong></p></div>
    </div>
    <div class="adm-login-form">
        <form method="post" action="<?= url('/admin/connexion') ?>" class="adm-card">
            <h1>Connexion</h1>
            <p class="adm-muted">Accédez à la gestion des logements, actualités et demandes.</p>
            <?php if ($m = flash('success')): ?><div class="adm-alert adm-alert-success"><?= icon('check-circle') ?><span><?= e($m) ?></span></div><?php endif; ?>
            <?php if ($error): ?><div class="adm-alert adm-alert-error"><?= icon('alert-triangle') ?><span><?= e($error) ?></span></div><?php endif; ?>
            <?= csrf_field() ?>
            <label class="adm-field"><span>E-mail</span><input type="email" name="email" value="<?= e($email) ?>" required autocomplete="username" autofocus></label>
            <label class="adm-field"><span>Mot de passe</span><input type="password" name="password" required autocomplete="current-password"></label>
            <button class="adm-btn adm-btn-primary adm-btn-block" type="submit"><?= icon('log-in') ?><span>Se connecter</span></button>
            <a class="adm-back" href="<?= url('/') ?>"><?= icon('arrow-left') ?>Retour au site</a>
        </form>
    </div>
</div>
</body>
</html>
