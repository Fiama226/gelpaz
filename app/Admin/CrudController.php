<?php
declare(strict_types=1);

namespace App\Admin;

use App\Auth;
use App\Database;
use App\Media;
use Throwable;

/**
 * CRUD générique du back-office, piloté par app/Admin/modules.php.
 */
final class CrudController
{
    private array $modules;

    public function __construct()
    {
        Auth::require();
        $this->modules = require ROOT . '/app/Admin/modules.php';
    }

    private function module(string $key): array
    {
        $m = $this->modules[$key] ?? null;
        if (!$m) {
            abort(404);
        }
        if (!empty($m['admin_only']) && (Auth::user()['role'] ?? '') !== 'admin') {
            flash('error', 'Accès réservé aux administrateurs.');
            redirect('/admin');
        }
        $m['key'] = $key;
        return $m;
    }

    public function index(string $key): string
    {
        $m = $this->module($key);
        $q = trim((string) ($_GET['q'] ?? ''));
        $where = '1=1';
        $params = [];
        if ($q !== '' && !empty($m['search'])) {
            $where = '(' . implode(' OR ', array_map(static fn($c) => "`$c` LIKE ?", $m['search'])) . ')';
            $params = array_fill(0, count($m['search']), '%' . $q . '%');
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM `{$m['table']}` WHERE $where", $params);
        $pager = paginate($total, 25, max(1, (int) ($_GET['page'] ?? 1)));
        $rows = Database::all("SELECT * FROM `{$m['table']}` WHERE $where ORDER BY {$m['order']} LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);
        $relations = [];
        foreach ($m['columns'] as [$col, , $type]) {
            if (str_starts_with($type, 'relation:')) {
                $relations[$col] = $this->options('table:' . substr($type, 9));
            }
        }
        return view('admin/list', ['m' => $m, 'rows' => $rows, 'pager' => $pager, 'q' => $q, 'relations' => $relations, 'pageTitle' => $m['label']], 'admin/layout');
    }

    public function create(string $key): string
    {
        $m = $this->module($key);
        $row = [];
        foreach ($m['fields'] as $f) {
            if (isset($f[0], $f['default']) && $f[0] !== 'section') {
                $row[$f[0]] = $f['default'];
            }
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$id, $errors, $row] = $this->save($m, null);
            if ($id) {
                flash('success', ucfirst($m['singular']) . ' créé' . ($m['gender'] === 'f' ? 'e' : '') . ' avec succès.');
                redirect('/admin/' . $key . '/' . $id);
            }
        }
        return $this->form($m, $row, $errors, null);
    }

    public function edit(string $key, string $id): string
    {
        $m = $this->module($key);
        $row = Database::one("SELECT * FROM `{$m['table']}` WHERE id = ?", [(int) $id]);
        if (!$row) {
            abort(404);
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$saved, $errors, $posted] = $this->save($m, $row);
            if ($saved) {
                flash('success', 'Modifications enregistrées.');
                redirect('/admin/' . $key . '/' . $id . (isset($_POST['_continue']) ? '' : ''));
            }
            $row = array_merge($row, $posted);
        }
        return $this->form($m, $row, $errors, (int) $id);
    }

    private function form(array $m, array $row, array $errors, ?int $id): string
    {
        $options = [];
        foreach ($m['fields'] as $f) {
            if (($f[2] ?? '') === 'select') {
                $options[$f[0]] = $this->options($f['options']);
            }
        }
        $extra = [];
        if ($id && !empty($m['gallery'])) {
            $extra['images'] = Database::all('SELECT * FROM property_images WHERE property_id = ? ORDER BY sort, id', [$id]);
        }
        if ($id && !empty($m['plans'])) {
            $extra['plans'] = Database::all('SELECT * FROM property_plans WHERE property_id = ? ORDER BY sort, id', [$id]);
        }
        if (!empty($m['post_gallery'])) {
            $extra['post_gallery'] = (array) json_decode((string) ($row['gallery'] ?? '[]'), true);
        }
        $title = $id ? 'Modifier : ' . (string) ($row['title'] ?? $row['name'] ?? $row['question'] ?? ('#' . $id)) : 'Ajouter ' . ($m['gender'] === 'f' ? 'une ' : 'un ') . $m['singular'];
        return view('admin/form', ['m' => $m, 'row' => $row, 'errors' => $errors, 'id' => $id, 'options' => $options, 'extra' => $extra, 'pageTitle' => $title], 'admin/layout');
    }

    /** @return array{0: int|false, 1: array, 2: array} */
    private function save(array $m, ?array $existing): array
    {
        if (!csrf_verify()) {
            return [false, ['_form' => 'Session expirée, merci de réessayer.'], $_POST];
        }
        $data = [];
        $errors = [];
        $isUsers = $m['table'] === 'users';
        $newFiles = [];
        foreach ($m['fields'] as $f) {
            if (($f[0] ?? '') === 'section') {
                continue;
            }
            [$name, $label, $type] = [$f[0], $f[1], $f[2]];
            $raw = $_POST[$name] ?? null;
            switch ($type) {
                case 'checkbox':
                    $data[$name] = !empty($raw) ? 1 : 0;
                    break;
                case 'number':
                    $data[$name] = ($raw === '' || $raw === null) ? null : (int) $raw;
                    break;
                case 'select':
                    $val = is_string($raw) ? $raw : '';
                    $opts = $this->options($f['options']);
                    if ($val !== '' && !array_key_exists($val, $opts)) {
                        $errors[$name] = 'Valeur invalide.';
                    }
                    $data[$name] = ($val === '' && str_starts_with((string) (is_string($f['options']) ? $f['options'] : ''), 'table:')) ? null : $val;
                    break;
                case 'richtext':
                    $data[$name] = sanitize_html(is_string($raw) ? $raw : '');
                    break;
                case 'datetime':
                    $v = is_string($raw) ? trim($raw) : '';
                    $data[$name] = $v !== '' && strtotime($v) ? date('Y-m-d H:i:s', strtotime($v)) : now();
                    break;
                case 'email':
                    $v = strtolower(trim((string) $raw));
                    if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
                        $errors[$name] = 'Adresse e-mail invalide.';
                    }
                    $data[$name] = $v;
                    break;
                case 'url':
                    $v = trim((string) $raw);
                    if ($v !== '' && !preg_match('#^https?://#i', $v)) {
                        $errors[$name] = 'Le lien doit commencer par http:// ou https://';
                    }
                    $data[$name] = $v;
                    break;
                case 'password':
                    $v = (string) $raw;
                    if ($v !== '') {
                        if (strlen($v) < 8) {
                            $errors[$name] = 'Le mot de passe doit contenir au moins 8 caractères.';
                        } else {
                            $data[$name] = password_hash($v, PASSWORD_DEFAULT);
                        }
                    } elseif (!$existing) {
                        $errors[$name] = 'Mot de passe obligatoire.';
                    }
                    break;
                case 'image':
                    $files = Media::files($_FILES[$name] ?? null);
                    if ($files) {
                        try {
                            $newFiles[$name] = Media::upload($files[0], $f['folder'] ?? 'misc', ($f['folder'] ?? '') === 'partners' ? 600 : 1920, ($f['folder'] ?? '') === 'partners' ? 400 : 800);
                            $data[$name] = $newFiles[$name];
                        } catch (Throwable $e) {
                            $errors[$name] = $e->getMessage();
                        }
                    } elseif (!empty($_POST[$name . '_remove'])) {
                        $data[$name] = '';
                        if (!empty($f['remote'])) {
                            $data[$f['remote']] = '';
                        }
                    }
                    break;
                case 'slug':
                    $data[$name] = trim((string) $raw);
                    break;
                default:
                    $data[$name] = is_string($raw) ? trim(str_replace("\0", '', $raw)) : '';
            }
            if (!empty($f['required']) && $type !== 'password' && $type !== 'image' && ($data[$name] ?? '') === '' && !isset($errors[$name])) {
                $errors[$name] = 'Ce champ est obligatoire.';
            }
            if (!empty($f['unique']) && ($data[$name] ?? '') !== '') {
                $dupe = Database::value("SELECT id FROM `{$m['table']}` WHERE `$name` = ?" . ($existing ? ' AND id <> ?' : ''), $existing ? [$data[$name], $existing['id']] : [$data[$name]]);
                if ($dupe) {
                    $errors[$name] = 'Cette valeur est déjà utilisée.';
                }
            }
        }
        // Slugs uniques
        foreach ($m['fields'] as $f) {
            if (($f[2] ?? '') === 'slug') {
                $base = slugify($data[$f[0]] !== '' ? $data[$f[0]] : (string) ($data[$f['source']] ?? 'element'));
                $slug = $base;
                $n = 2;
                while (Database::value("SELECT id FROM `{$m['table']}` WHERE `{$f[0]}` = ?" . ($existing ? ' AND id <> ?' : ''), $existing ? [$slug, $existing['id']] : [$slug])) {
                    $slug = $base . '-' . $n++;
                }
                $data[$f[0]] = $slug;
            }
        }
        if ($isUsers && $existing && (int) $existing['id'] === (int) Auth::user()['id'] && ($data['role'] ?? 'admin') !== 'admin') {
            $errors['role'] = 'Vous ne pouvez pas retirer vos propres droits d’administrateur.';
        }
        if ($errors) {
            foreach ($newFiles as $p) {
                Media::delete($p);
            }
            unset($data['password']);
            return [false, $errors, $data];
        }
        $columns = array_keys(\App\Schema::tables()[$m['table']]['columns']);
        if (in_array('updated_at', $columns, true)) {
            $data['updated_at'] = now();
        }
        if (!$existing && in_array('created_at', $columns, true)) {
            $data['created_at'] = now();
        }
        if ($m['table'] === 'posts' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if ($m['table'] === 'posts' && !$existing) {
            $data['views'] = 0;
            $data['gallery'] = '[]';
        }
        if ($m['table'] === 'properties' && !$existing) {
            $data['views'] = 0;
        }
        $data = array_intersect_key($data, array_flip($columns));

        // Remplacement d'image : supprimer l'ancien fichier
        if ($existing) {
            foreach ($m['fields'] as $f) {
                if (($f[2] ?? '') === 'image' && array_key_exists($f[0], $data) && ($existing[$f[0]] ?? '') !== $data[$f[0]]) {
                    Media::delete($existing[$f[0]] ?? '');
                }
            }
            Database::update($m['table'], $data, 'id = ?', [$existing['id']]);
            $id = (int) $existing['id'];
        } else {
            $id = Database::insert($m['table'], $data);
        }

        if (!empty($m['gallery'])) {
            $this->saveGallery($id);
        }
        if (!empty($m['plans'])) {
            $this->savePlans($id);
        }
        if (!empty($m['post_gallery'])) {
            $this->savePostGallery($id);
        }
        return [$id, [], $data];
    }

    private function saveGallery(int $propertyId): void
    {
        $delete = array_map('intval', (array) ($_POST['gallery_delete'] ?? []));
        foreach (Database::all('SELECT * FROM property_images WHERE property_id = ?', [$propertyId]) as $img) {
            if (in_array((int) $img['id'], $delete, true)) {
                Media::delete($img['path']);
                Database::delete('property_images', 'id = ?', [$img['id']]);
                continue;
            }
            $caption = (string) ($_POST['gallery_caption'][$img['id']] ?? $img['caption']);
            Database::update('property_images', ['caption' => mb_substr(trim($caption), 0, 255)], 'id = ?', [$img['id']]);
        }
        $order = array_filter(array_map('intval', explode(',', (string) ($_POST['gallery_order'] ?? ''))));
        foreach (array_values($order) as $i => $imgId) {
            Database::update('property_images', ['sort' => $i + 1], 'id = ? AND property_id = ?', [$imgId, $propertyId]);
        }
        $max = (int) Database::value('SELECT COALESCE(MAX(sort), 0) FROM property_images WHERE property_id = ?', [$propertyId]);
        foreach (Media::files($_FILES['gallery_new'] ?? null) as $file) {
            try {
                $path = Media::upload($file, 'properties');
                Database::insert('property_images', ['property_id' => $propertyId, 'path' => $path, 'remote_url' => '', 'remote_thumb' => '', 'caption' => '', 'sort' => ++$max]);
            } catch (Throwable $e) {
                flash('error', 'Une image n’a pas pu être ajoutée : ' . $e->getMessage());
            }
        }
    }

    private function savePlans(int $propertyId): void
    {
        foreach ((array) ($_POST['plans'] ?? []) as $planId => $p) {
            $plan = Database::one('SELECT * FROM property_plans WHERE id = ? AND property_id = ?', [(int) $planId, $propertyId]);
            if (!$plan) {
                continue;
            }
            if (!empty($p['delete'])) {
                Media::delete($plan['image']);
                Database::delete('property_plans', 'id = ?', [$plan['id']]);
                continue;
            }
            $update = ['title' => mb_substr(trim((string) ($p['title'] ?? '')), 0, 190) ?: 'Plan', 'description' => trim((string) ($p['description'] ?? '')), 'sort' => (int) ($p['sort'] ?? 0)];
            $files = Media::files($_FILES['plan_image_' . $plan['id']] ?? null);
            if ($files) {
                try {
                    $update['image'] = Media::upload($files[0], 'plans');
                    Media::delete($plan['image']);
                } catch (Throwable $e) {
                    flash('error', 'Plan : ' . $e->getMessage());
                }
            }
            Database::update('property_plans', $update, 'id = ?', [$plan['id']]);
        }
        $newFiles = $_FILES['new_plan_image'] ?? null;
        foreach ((array) ($_POST['new_plans'] ?? []) as $i => $p) {
            $title = trim((string) ($p['title'] ?? ''));
            $file = null;
            if ($newFiles && isset($newFiles['name'][$i]) && ($newFiles['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                $file = ['name' => $newFiles['name'][$i], 'type' => $newFiles['type'][$i], 'tmp_name' => $newFiles['tmp_name'][$i], 'error' => $newFiles['error'][$i], 'size' => $newFiles['size'][$i]];
            }
            if ($title === '' && !$file) {
                continue;
            }
            $image = '';
            if ($file) {
                try {
                    $image = Media::upload($file, 'plans');
                } catch (Throwable $e) {
                    flash('error', 'Plan : ' . $e->getMessage());
                }
            }
            Database::insert('property_plans', ['property_id' => $propertyId, 'title' => $title ?: 'Plan', 'image' => $image, 'image_remote' => '', 'description' => trim((string) ($p['description'] ?? '')), 'sort' => (int) ($p['sort'] ?? 0)]);
        }
    }

    private function savePostGallery(int $postId): void
    {
        $gallery = (array) json_decode((string) Database::value('SELECT gallery FROM posts WHERE id = ?', [$postId]), true);
        $delete = array_map('intval', (array) ($_POST['post_gallery_delete'] ?? []));
        $kept = [];
        foreach ($gallery as $i => $g) {
            if (in_array($i, $delete, true)) {
                Media::delete($g['path'] ?? '');
                continue;
            }
            $kept[] = $g;
        }
        foreach (Media::files($_FILES['post_gallery_new'] ?? null) as $file) {
            try {
                $kept[] = ['path' => Media::upload($file, 'posts'), 'remote' => ''];
            } catch (Throwable $e) {
                flash('error', 'Galerie : ' . $e->getMessage());
            }
        }
        Database::update('posts', ['gallery' => json_encode($kept, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)], 'id = ?', [$postId]);
    }

    public function delete(string $key, string $id): never
    {
        $m = $this->module($key);
        if (!csrf_verify()) {
            flash('error', 'Session expirée, merci de réessayer.');
            redirect('/admin/' . $key);
        }
        $row = Database::one("SELECT * FROM `{$m['table']}` WHERE id = ?", [(int) $id]);
        if ($row) {
            if ($m['table'] === 'users') {
                if ((int) $row['id'] === (int) Auth::user()['id']) {
                    flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                    redirect('/admin/' . $key);
                }
                if ($row['role'] === 'admin' && (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'admin'") <= 1) {
                    flash('error', 'Impossible de supprimer le dernier administrateur.');
                    redirect('/admin/' . $key);
                }
            }
            foreach ($m['fields'] as $f) {
                if (($f[2] ?? '') === 'image') {
                    Media::delete($row[$f[0]] ?? '');
                }
            }
            if ($m['table'] === 'properties') {
                foreach (Database::all('SELECT path FROM property_images WHERE property_id = ?', [$row['id']]) as $img) {
                    Media::delete($img['path']);
                }
                foreach (Database::all('SELECT image FROM property_plans WHERE property_id = ?', [$row['id']]) as $pl) {
                    Media::delete($pl['image']);
                }
                Database::delete('property_images', 'property_id = ?', [$row['id']]);
                Database::delete('property_plans', 'property_id = ?', [$row['id']]);
            }
            if ($m['table'] === 'posts') {
                foreach ((array) json_decode((string) $row['gallery'], true) as $g) {
                    Media::delete($g['path'] ?? '');
                }
                Database::delete('comments', 'post_id = ?', [$row['id']]);
            }
            if ($m['table'] === 'property_categories') {
                Database::run('UPDATE properties SET category_id = NULL WHERE category_id = ?', [$row['id']]);
            }
            if ($m['table'] === 'sites') {
                Database::run('UPDATE properties SET site_id = NULL WHERE site_id = ?', [$row['id']]);
            }
            Database::delete($m['table'], 'id = ?', [$row['id']]);
            flash('success', ucfirst($m['singular']) . ' supprimé' . ($m['gender'] === 'f' ? 'e' : '') . '.');
        }
        redirect('/admin/' . $key);
    }

    public function toggle(string $key, string $id): never
    {
        $m = $this->module($key);
        $field = (string) ($_POST['field'] ?? '');
        $allowed = array_map(static fn($c) => $c[0], array_filter($m['columns'], static fn($c) => $c[2] === 'toggle'));
        if (!csrf_verify() || !in_array($field, $allowed, true)) {
            json_response(['ok' => false, 'message' => 'Action non autorisée.'], 403);
        }
        Database::run("UPDATE `{$m['table']}` SET `$field` = 1 - `$field` WHERE id = ?", [(int) $id]);
        $value = (int) Database::value("SELECT `$field` FROM `{$m['table']}` WHERE id = ?", [(int) $id]);
        json_response(['ok' => true, 'value' => $value]);
    }

    public function reorder(string $key): never
    {
        $m = $this->module($key);
        if (!csrf_verify() || empty($m['sortable'])) {
            json_response(['ok' => false], 403);
        }
        $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])));
        $offset = max(0, (int) ($_POST['offset'] ?? 0));
        foreach (array_values($ids) as $i => $rowId) {
            Database::update($m['table'], ['sort' => $offset + $i + 1], 'id = ?', [$rowId]);
        }
        json_response(['ok' => true]);
    }

    /** Options d'un champ select : tableau statique ou « table:nom ». */
    private function options(array|string $source): array
    {
        if (is_array($source)) {
            return $source;
        }
        static $cache = [];
        if (isset($cache[$source])) {
            return $cache[$source];
        }
        $table = substr($source, 6);
        $labelCol = match ($table) {
            'property_categories', 'sites' => 'name',
            default => 'title',
        };
        $out = [];
        foreach (Database::all("SELECT id, `$labelCol` AS label FROM `$table` ORDER BY " . ($table === 'sites' || $table === 'property_categories' ? 'sort, ' : '') . "`$labelCol`") as $r) {
            $out[(string) $r['id']] = $r['label'];
        }
        return $cache[$source] = $out;
    }
}
