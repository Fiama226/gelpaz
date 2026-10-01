<?php
$baseUrl = '/admin/' . $m['key'];
$action = $id ? url($baseUrl . '/' . $id) : url($baseUrl . '/nouveau');
$viewUrl = ($id && !empty($m['view']) && !empty($row['slug'])) ? url(str_replace('{slug}', (string) $row['slug'], $m['view'])) : null;
$val = static fn(string $k) => $row[$k] ?? '';
?>
<div class="adm-toolbar">
    <a class="adm-btn adm-btn-ghost" href="<?= url($baseUrl) ?>"><?= icon('arrow-left') ?><span>Retour à la liste</span></a>
    <?php if ($viewUrl): ?><a class="adm-btn adm-btn-ghost" href="<?= e($viewUrl) ?>" target="_blank" rel="noopener"><?= icon('eye') ?><span>Voir sur le site</span></a><?php endif; ?>
</div>
<?php if (!empty($errors)): ?><div class="adm-alert adm-alert-error"><?= icon('alert-triangle') ?><span><?= e($errors['_form'] ?? 'Merci de corriger les champs signalés.') ?></span></div><?php endif; ?>
<form class="adm-form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-unsaved>
    <?= csrf_field() ?>
    <div class="adm-card">
        <div class="adm-fields">
        <?php foreach ($m['fields'] as $f):
            if ($f[0] === 'section'): ?>
            <h2 class="adm-section-title"><?= e($f[1]) ?></h2>
            <?php continue; endif;
            [$name, $label, $type] = [$f[0], $f[1], $f[2]];
            $col = $f['col'] ?? 'full';
            $err = $errors[$name] ?? null;
            $req = !empty($f['required']); ?>
            <div class="adm-field adm-col-<?= e($col) ?><?= $err ? ' has-error' : '' ?>">
                <?php if ($type === 'checkbox'): ?>
                    <label class="adm-check"><input type="checkbox" name="<?= e($name) ?>" value="1" <?= (int) $val($name) ? 'checked' : '' ?>><span class="adm-check-box"></span><span><?= e($label) ?></span></label>
                <?php else: ?>
                    <label for="f-<?= e($name) ?>"><?= e($label) ?><?= $req ? ' <em>*</em>' : '' ?></label>
                    <?php switch ($type):
                        case 'textarea': ?>
                            <textarea id="f-<?= e($name) ?>" name="<?= e($name) ?>" rows="<?= (int) ($f['rows'] ?? 4) ?>" <?= $req ? 'required' : '' ?>><?= e($val($name)) ?></textarea>
                        <?php break; case 'lines': ?>
                            <textarea id="f-<?= e($name) ?>" name="<?= e($name) ?>" rows="6"><?= e($val($name)) ?></textarea>
                        <?php break; case 'richtext': ?>
                            <div class="adm-editor" data-editor>
                                <div class="adm-editor-toolbar">
                                    <button type="button" data-cmd="formatBlock" data-value="h2" title="Titre">H2</button>
                                    <button type="button" data-cmd="formatBlock" data-value="h3" title="Sous-titre">H3</button>
                                    <button type="button" data-cmd="formatBlock" data-value="p" title="Paragraphe">¶</button>
                                    <span class="sep"></span>
                                    <button type="button" data-cmd="bold" title="Gras"><b>G</b></button>
                                    <button type="button" data-cmd="italic" title="Italique"><i>I</i></button>
                                    <button type="button" data-cmd="underline" title="Souligné"><u>S</u></button>
                                    <span class="sep"></span>
                                    <button type="button" data-cmd="insertUnorderedList" title="Liste à puces"><?= icon('list') ?></button>
                                    <button type="button" data-cmd="insertOrderedList" title="Liste numérotée">1.</button>
                                    <button type="button" data-cmd="formatBlock" data-value="blockquote" title="Citation"><?= icon('quote') ?></button>
                                    <span class="sep"></span>
                                    <button type="button" data-cmd="createLink" title="Lien"><?= icon('link') ?></button>
                                    <button type="button" data-cmd="unlink" title="Retirer le lien">⨯</button>
                                    <button type="button" data-cmd="insertImage" title="Image"><?= icon('image') ?></button>
                                    <button type="button" data-cmd="removeFormat" title="Effacer la mise en forme">Tx</button>
                                    <button type="button" data-cmd="source" title="Code HTML">&lt;/&gt;</button>
                                </div>
                                <div class="adm-editor-area prose-admin" contenteditable="true"><?= sanitize_html((string) $val($name)) ?></div>
                                <textarea name="<?= e($name) ?>" class="adm-editor-source" hidden><?= e(sanitize_html((string) $val($name))) ?></textarea>
                            </div>
                        <?php break; case 'select': ?>
                            <select id="f-<?= e($name) ?>" name="<?= e($name) ?>">
                                <?php if (is_string($f['options'])): ?><option value="">— Aucun —</option><?php endif; ?>
                                <?php foreach ($options[$name] as $k => $lbl): ?><option value="<?= e((string) $k) ?>" <?= (string) $val($name) === (string) $k ? 'selected' : '' ?>><?= e($lbl) ?></option><?php endforeach; ?>
                            </select>
                        <?php break; case 'image':
                            $remote = !empty($f['remote']) ? (string) ($row[$f['remote']] ?? '') : '';
                            $src = media((string) $val($name), $remote, null); ?>
                            <div class="adm-image-field">
                                <div class="adm-image-preview"><?php if ($src): ?><img src="<?= e($src) ?>" alt="" referrerpolicy="no-referrer" onerror="this.src='<?= e(url('assets/images/placeholder.svg')) ?>'"><?php else: ?><span><?= icon('image') ?></span><?php endif; ?></div>
                                <div>
                                    <input id="f-<?= e($name) ?>" type="file" name="<?= e($name) ?>" accept="image/jpeg,image/png,image/webp,image/gif" data-preview>
                                    <?php if ($src): ?><label class="adm-check adm-check-sm"><input type="checkbox" name="<?= e($name) ?>_remove" value="1"><span class="adm-check-box"></span><span>Retirer l’image</span></label><?php endif; ?>
                                    <?php if ($remote && !$val($name)): ?><small class="adm-help"><?= icon('info') ?>Image hébergée sur l’ancien site — importable depuis Outils.</small><?php endif; ?>
                                </div>
                            </div>
                        <?php break; case 'datetime': ?>
                            <input id="f-<?= e($name) ?>" type="datetime-local" name="<?= e($name) ?>" value="<?= $val($name) ? e(date('Y-m-d\TH:i', strtotime((string) $val($name)))) : e(date('Y-m-d\TH:i')) ?>">
                        <?php break; case 'password': ?>
                            <input id="f-<?= e($name) ?>" type="password" name="<?= e($name) ?>" autocomplete="new-password" <?= !$id ? 'required' : '' ?>>
                        <?php break; case 'number': ?>
                            <input id="f-<?= e($name) ?>" type="number" name="<?= e($name) ?>" value="<?= e((string) $val($name)) ?>" min="0" step="1">
                        <?php break; default: ?>
                            <input id="f-<?= e($name) ?>" type="<?= in_array($type, ['email', 'url'], true) ? $type : 'text' ?>" name="<?= e($name) ?>" value="<?= e((string) $val($name)) ?>" <?= $req ? 'required' : '' ?> <?= $type === 'slug' ? 'data-slug-from="' . e($f['source']) . '"' : '' ?> <?= !empty($f['datalist']) ? 'list="dl-' . e($name) . '"' : '' ?>>
                            <?php if (!empty($f['datalist'])): ?><datalist id="dl-<?= e($name) ?>"><?php foreach ($f['datalist'] as $o): ?><option value="<?= e($o) ?>"><?php endforeach; ?></datalist><?php endif; ?>
                    <?php endswitch; ?>
                <?php endif; ?>
                <?php if (!empty($f['help'])): ?><small class="adm-help"><?= e($f['help']) ?></small><?php endif; ?>
                <?php if ($err): ?><small class="adm-error"><?= e($err) ?></small><?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($m['gallery'])): ?>
    <div class="adm-card">
        <div class="adm-card-head"><h2>Galerie photos</h2><span class="adm-muted"><?= $id ? plural(count($extra['images'] ?? []), 'photo') : 'Disponible après le premier enregistrement' ?></span></div>
        <?php if ($id): ?>
        <div class="adm-gallery" data-gallery>
            <?php foreach ($extra['images'] ?? [] as $img): $src = media_thumb($img['path'], $img['remote_thumb'], $img['remote_url'], null); ?>
            <div class="adm-gallery-item" draggable="true" data-id="<?= (int) $img['id'] ?>">
                <img src="<?= e($src) ?>" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.src='<?= e(url('assets/images/placeholder.svg')) ?>'">
                <input type="text" name="gallery_caption[<?= (int) $img['id'] ?>]" value="<?= e($img['caption']) ?>" placeholder="Légende (facultatif)">
                <label class="adm-gallery-del" title="Supprimer"><input type="checkbox" name="gallery_delete[]" value="<?= (int) $img['id'] ?>"><?= icon('trash') ?></label>
                <span class="adm-gallery-grip" title="Glisser pour réordonner"><?= icon('grip') ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="gallery_order" value="<?= e(implode(',', array_column($extra['images'] ?? [], 'id'))) ?>" data-gallery-order>
        <?php endif; ?>
        <label class="adm-dropzone"><?= icon('upload') ?><span><strong>Ajouter des photos</strong> — JPG, PNG ou WebP, 10 Mo max. par image. Vous pouvez en sélectionner plusieurs.</span><input type="file" name="gallery_new[]" accept="image/jpeg,image/png,image/webp" multiple data-count-files></label>
        <p class="adm-hint"><?= icon('info') ?>La photo principale (champ ci-dessus) apparaît en premier ; glissez les vignettes pour modifier l’ordre, cochez la corbeille pour supprimer.</p>
    </div>
    <?php endif; ?>

    <?php if (!empty($m['plans'])): ?>
    <div class="adm-card">
        <div class="adm-card-head"><h2>Plans du logement</h2><button type="button" class="adm-btn adm-btn-ghost adm-btn-sm" data-add-plan><?= icon('plus') ?><span>Ajouter un plan</span></button></div>
        <div class="adm-plans" data-plans>
            <?php foreach ($extra['plans'] ?? [] as $pl): $src = media($pl['image'], $pl['image_remote'], null); ?>
            <div class="adm-plan">
                <div class="adm-image-preview adm-image-preview-sm"><?php if ($src): ?><img src="<?= e($src) ?>" alt=""><?php else: ?><span><?= icon('image') ?></span><?php endif; ?></div>
                <div class="adm-plan-fields">
                    <input type="text" name="plans[<?= (int) $pl['id'] ?>][title]" value="<?= e($pl['title']) ?>" placeholder="Titre (ex. : Rez-de-chaussée)">
                    <textarea name="plans[<?= (int) $pl['id'] ?>][description]" rows="2" placeholder="Description (facultatif)"><?= e($pl['description']) ?></textarea>
                    <div class="adm-plan-row"><input type="file" name="plan_image_<?= (int) $pl['id'] ?>" accept="image/*"><input type="number" name="plans[<?= (int) $pl['id'] ?>][sort]" value="<?= (int) $pl['sort'] ?>" title="Ordre" style="max-width:90px"><label class="adm-check adm-check-sm"><input type="checkbox" name="plans[<?= (int) $pl['id'] ?>][delete]" value="1"><span class="adm-check-box"></span><span>Supprimer</span></label></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <template data-plan-template>
            <div class="adm-plan">
                <div class="adm-image-preview adm-image-preview-sm"><span><?= icon('image') ?></span></div>
                <div class="adm-plan-fields">
                    <input type="text" name="new_plans[__i__][title]" placeholder="Titre (ex. : Rez-de-chaussée)">
                    <textarea name="new_plans[__i__][description]" rows="2" placeholder="Description (facultatif)"></textarea>
                    <div class="adm-plan-row"><input type="file" name="new_plan_image[__i__]" accept="image/*"><input type="number" name="new_plans[__i__][sort]" value="0" title="Ordre" style="max-width:90px"></div>
                </div>
            </div>
        </template>
        <?php if (empty($extra['plans'])): ?><p class="adm-hint"><?= icon('info') ?>Ajoutez les plans (rez-de-chaussée, étage…) : ils s’affichent sous forme d’onglets sur la fiche du logement.</p><?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($m['post_gallery'])): ?>
    <div class="adm-card">
        <div class="adm-card-head"><h2>Galerie de l’article</h2><span class="adm-muted"><?= plural(count($extra['post_gallery'] ?? []), 'photo') ?></span></div>
        <div class="adm-gallery">
            <?php foreach ($extra['post_gallery'] ?? [] as $i => $g): $src = media($g['path'] ?? '', $g['remote'] ?? '', null); ?>
            <div class="adm-gallery-item">
                <img src="<?= e($src) ?>" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.src='<?= e(url('assets/images/placeholder.svg')) ?>'">
                <label class="adm-gallery-del" title="Supprimer"><input type="checkbox" name="post_gallery_delete[]" value="<?= (int) $i ?>"><?= icon('trash') ?></label>
            </div>
            <?php endforeach; ?>
        </div>
        <label class="adm-dropzone"><?= icon('upload') ?><span><strong>Ajouter des photos à la galerie</strong> — plusieurs fichiers possibles.</span><input type="file" name="post_gallery_new[]" accept="image/jpeg,image/png,image/webp" multiple data-count-files></label>
    </div>
    <?php endif; ?>

    <div class="adm-form-actions">
        <button class="adm-btn adm-btn-primary adm-btn-lg" type="submit"><?= icon('save') ?><span><?= $id ? 'Enregistrer les modifications' : 'Créer' ?></span></button>
        <a class="adm-btn adm-btn-ghost" href="<?= url($baseUrl) ?>">Annuler</a>
    </div>
</form>
