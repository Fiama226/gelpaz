<div class="adm-toolbar"><span class="adm-muted"><?= plural(count($rows), 'abonné') ?></span><a class="adm-btn adm-btn-primary" href="<?= url('/admin/newsletter?export=csv') ?>"><?= icon('download') ?><span>Exporter (CSV)</span></a></div>
<div class="adm-card adm-card-flush">
    <?php if ($rows): ?>
    <div class="adm-table-wrap"><table class="adm-table"><thead><tr><th>E-mail</th><th>Inscription</th><th class="adm-col-actions">Action</th></tr></thead><tbody>
    <?php foreach ($rows as $r): ?>
    <tr><td data-label="E-mail"><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></td><td data-label="Inscription"><?= e(date_fr($r['created_at'], 'datetime')) ?></td>
    <td class="adm-col-actions"><form method="post" data-confirm="Supprimer cet abonné ?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="adm-icon-btn is-danger" type="submit" title="Supprimer"><?= icon('trash') ?></button></form></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php else: ?><div class="adm-empty-state"><?= icon('mail') ?><p>Aucun abonné pour le moment.</p></div><?php endif; ?>
</div>
