<?php
$success = flash('success');
$error = flash('error');
if ($success): ?>
<div class="alert alert-success" role="status"><?= icon('check-circle') ?><span><?= e($success) ?></span></div>
<?php endif; if ($error): ?>
<div class="alert alert-error" role="alert"><?= icon('alert-triangle') ?><span><?= e($error) ?></span></div>
<?php endif;
$GLOBALS['__form_errors'] = flash('errors') ?: [];
