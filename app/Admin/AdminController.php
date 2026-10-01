<?php
declare(strict_types=1);

namespace App\Admin;

use App\Auth;
use App\Database;
use App\Mailer;
use App\Media;
use Throwable;

/**
 * Back-office : connexion, tableau de bord, demandes reçues, réglages et outils.
 */
final class AdminController
{
    private function guard(bool $adminOnly = false): void
    {
        Auth::require();
        if ($adminOnly && (Auth::user()['role'] ?? '') !== 'admin') {
            flash('error', 'Accès réservé aux administrateurs.');
            redirect('/admin');
        }
    }

    private function checkPost(string $back): void
    {
        if (!csrf_verify()) {
            flash('error', 'Session expirée, merci de réessayer.');
            redirect($back);
        }
    }

    public function login(): string
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        $error = null;
        $email = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim((string) ($_POST['email'] ?? ''));
            if (!csrf_verify()) {
                $error = 'Session expirée. Merci de réessayer.';
            } else {
                $result = Auth::attempt($email, (string) ($_POST['password'] ?? ''));
                if ($result === true) {
                    $to = (string) ($_SESSION['admin_intended'] ?? '/admin');
                    unset($_SESSION['admin_intended']);
                    redirect(str_starts_with($to, '/admin') ? $to : '/admin');
                }
                $error = is_string($result) ? $result : 'Identifiants incorrects.';
            }
        }
        return view('admin/login', ['error' => $error, 'email' => $email], null);
    }

    public function logout(): never
    {
        if (csrf_verify()) {
            Auth::logout();
        }
        flash('success', 'Vous êtes déconnecté(e).');
        redirect('/admin/connexion');
    }

    public function dashboard(): string
    {
        $this->guard();
        $count = static fn(string $sql, array $p = []) => (int) Database::value($sql, $p);
        $stats = [
            'properties' => $count('SELECT COUNT(*) FROM properties'),
            'published' => $count('SELECT COUNT(*) FROM properties WHERE is_published = 1'),
            'posts' => $count('SELECT COUNT(*) FROM posts'),
            'messages' => $count('SELECT COUNT(*) FROM messages'),
            'unread' => $count('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
            'subscriptions' => $count('SELECT COUNT(*) FROM subscriptions'),
            'new_subscriptions' => $count("SELECT COUNT(*) FROM subscriptions WHERE status = 'nouveau'"),
            'pending_comments' => $count("SELECT COUNT(*) FROM comments WHERE status = 'pending'"),
            'newsletter' => $count('SELECT COUNT(*) FROM newsletter'),
            'views' => $count('SELECT COALESCE(SUM(views), 0) FROM properties'),
        ];
        return view('admin/dashboard', [
            'pageTitle' => 'Tableau de bord',
            'stats' => $stats,
            'messages' => Database::all('SELECT m.*, p.title AS property_title FROM messages m LEFT JOIN properties p ON p.id = m.property_id ORDER BY m.created_at DESC LIMIT 6'),
            'subscriptions' => Database::all('SELECT * FROM subscriptions ORDER BY created_at DESC LIMIT 5'),
            'topProperties' => Database::all('SELECT id, title, slug, views FROM properties ORDER BY views DESC, id ASC LIMIT 5'),
            'pendingMedia' => count(Media::pending()),
        ], 'admin/layout');
    }

    /* ----------------------------------------------------------- Messages */

    public function messages(): string
    {
        $this->guard();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/messages');
            $ids = array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])));
            $action = (string) ($_POST['action'] ?? '');
            if ($ids) {
                $in = implode(',', $ids);
                match ($action) {
                    'read' => Database::run("UPDATE messages SET is_read = 1 WHERE id IN ($in)"),
                    'unread' => Database::run("UPDATE messages SET is_read = 0 WHERE id IN ($in)"),
                    'delete' => Database::run("DELETE FROM messages WHERE id IN ($in)"),
                    default => null,
                };
                flash('success', 'Action effectuée sur ' . plural(count($ids), 'message') . '.');
            }
            redirect('/admin/messages' . (!empty($_GET) ? '?' . http_build_query($_GET) : ''));
        }
        $type = (string) ($_GET['type'] ?? '');
        $status = (string) ($_GET['statut'] ?? '');
        $q = trim((string) ($_GET['q'] ?? ''));
        $where = ['1=1'];
        $params = [];
        if (in_array($type, ['contact', 'visite', 'information'], true)) {
            $where[] = 'm.type = ?';
            $params[] = $type;
        }
        if ($status === 'non-lus') {
            $where[] = 'm.is_read = 0';
        }
        if ($q !== '') {
            $where[] = '(m.name LIKE ? OR m.email LIKE ? OR m.phone LIKE ? OR m.subject LIKE ? OR m.message LIKE ?)';
            array_push($params, ...array_fill(0, 5, '%' . $q . '%'));
        }
        $sqlWhere = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM messages m WHERE $sqlWhere", $params);
        $pager = paginate($total, 25, max(1, (int) ($_GET['page'] ?? 1)));
        $rows = Database::all("SELECT m.*, p.title AS property_title FROM messages m LEFT JOIN properties p ON p.id = m.property_id WHERE $sqlWhere ORDER BY m.created_at DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);
        if (($_GET['export'] ?? '') === 'csv') {
            $this->csv('messages', ['Date', 'Type', 'Nom', 'E-mail', 'Téléphone', 'Objet', 'Logement', 'Date de visite', 'Message'], array_map(static fn($r) => [
                $r['created_at'], $r['type'], $r['name'], $r['email'], $r['phone'], $r['subject'], $r['property_title'], $r['visit_date'], $r['message'],
            ], Database::all("SELECT m.*, p.title AS property_title FROM messages m LEFT JOIN properties p ON p.id = m.property_id WHERE $sqlWhere ORDER BY m.created_at DESC", $params)));
        }
        return view('admin/messages', ['pageTitle' => 'Messages reçus', 'rows' => $rows, 'pager' => $pager, 'type' => $type, 'status' => $status, 'q' => $q], 'admin/layout');
    }

    public function message(string $id): string
    {
        $this->guard();
        $msg = Database::one('SELECT m.*, p.title AS property_title, p.slug AS property_slug FROM messages m LEFT JOIN properties p ON p.id = m.property_id WHERE m.id = ?', [(int) $id]);
        if (!$msg) {
            abort(404);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/messages/' . $id);
            if (($_POST['action'] ?? '') === 'delete') {
                Database::delete('messages', 'id = ?', [$msg['id']]);
                flash('success', 'Message supprimé.');
                redirect('/admin/messages');
            }
            Database::update('messages', ['is_read' => 0], 'id = ?', [$msg['id']]);
            flash('success', 'Message marqué comme non lu.');
            redirect('/admin/messages');
        }
        if (!(int) $msg['is_read']) {
            Database::update('messages', ['is_read' => 1], 'id = ?', [$msg['id']]);
        }
        return view('admin/message', ['pageTitle' => 'Message de ' . $msg['name'], 'msg' => $msg], 'admin/layout');
    }

    /* ------------------------------------------------------ Souscriptions */

    public function subscriptions(): string
    {
        $this->guard();
        $status = (string) ($_GET['statut'] ?? '');
        $q = trim((string) ($_GET['q'] ?? ''));
        $where = ['1=1'];
        $params = [];
        if (in_array($status, ['nouveau', 'en_cours', 'traite', 'annule'], true)) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        if ($q !== '') {
            $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR country LIKE ?)';
            array_push($params, ...array_fill(0, 4, '%' . $q . '%'));
        }
        $sqlWhere = implode(' AND ', $where);
        if (($_GET['export'] ?? '') === 'csv') {
            $this->csv('souscriptions', ['Date', 'Statut', 'Nom', 'Téléphone', 'E-mail', 'Pays', 'Ville', 'Logement', 'Site', 'Paiement', 'Message', 'Notes'], array_map(static fn($r) => [
                $r['created_at'], subscription_status_label($r['status']), $r['full_name'], $r['phone'], $r['email'], $r['country'], $r['city'], $r['villa_type'], $r['site'], $r['payment_mode'], $r['message'], $r['notes'],
            ], Database::all("SELECT * FROM subscriptions WHERE $sqlWhere ORDER BY created_at DESC", $params)));
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM subscriptions WHERE $sqlWhere", $params);
        $pager = paginate($total, 25, max(1, (int) ($_GET['page'] ?? 1)));
        $rows = Database::all("SELECT * FROM subscriptions WHERE $sqlWhere ORDER BY created_at DESC LIMIT {$pager['limit']} OFFSET {$pager['offset']}", $params);
        $counts = [];
        foreach (Database::all('SELECT status, COUNT(*) AS n FROM subscriptions GROUP BY status') as $r) {
            $counts[$r['status']] = (int) $r['n'];
        }
        return view('admin/subscriptions', ['pageTitle' => 'Demandes de souscription', 'rows' => $rows, 'pager' => $pager, 'status' => $status, 'q' => $q, 'counts' => $counts], 'admin/layout');
    }

    public function subscription(string $id): string
    {
        $this->guard();
        $sub = Database::one('SELECT * FROM subscriptions WHERE id = ?', [(int) $id]);
        if (!$sub) {
            abort(404);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/souscriptions/' . $id);
            if (($_POST['action'] ?? '') === 'delete') {
                Database::delete('subscriptions', 'id = ?', [$sub['id']]);
                flash('success', 'Demande supprimée.');
                redirect('/admin/souscriptions');
            }
            $status = (string) ($_POST['status'] ?? 'nouveau');
            Database::update('subscriptions', [
                'status' => in_array($status, ['nouveau', 'en_cours', 'traite', 'annule'], true) ? $status : 'nouveau',
                'notes' => trim((string) ($_POST['notes'] ?? '')),
                'updated_at' => now(),
            ], 'id = ?', [$sub['id']]);
            flash('success', 'Demande mise à jour.');
            redirect('/admin/souscriptions/' . $id);
        }
        return view('admin/subscription', ['pageTitle' => 'Souscription de ' . $sub['full_name'], 'sub' => $sub], 'admin/layout');
    }

    /* -------------------------------------------------------- Commentaires */

    public function comments(): string
    {
        $this->guard();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/commentaires');
            $id = (int) ($_POST['id'] ?? 0);
            match ((string) ($_POST['action'] ?? '')) {
                'approve' => Database::update('comments', ['status' => 'approved'], 'id = ?', [$id]),
                'pending' => Database::update('comments', ['status' => 'pending'], 'id = ?', [$id]),
                'delete' => Database::delete('comments', 'id = ? OR parent_id = ?', [$id, $id]),
                default => null,
            };
            flash('success', 'Commentaire mis à jour.');
            redirect('/admin/commentaires' . (isset($_GET['statut']) ? '?statut=' . urlencode((string) $_GET['statut']) : ''));
        }
        $status = (string) ($_GET['statut'] ?? 'pending');
        $rows = Database::all('SELECT c.*, p.title AS post_title, p.slug AS post_slug FROM comments c LEFT JOIN posts p ON p.id = c.post_id'
            . ($status === 'tous' ? '' : ' WHERE c.status = ?') . ' ORDER BY c.created_at DESC LIMIT 200', $status === 'tous' ? [] : [$status === 'approved' ? 'approved' : 'pending']);
        return view('admin/comments', ['pageTitle' => 'Commentaires', 'rows' => $rows, 'status' => $status], 'admin/layout');
    }

    /* ---------------------------------------------------------- Newsletter */

    public function newsletter(): string
    {
        $this->guard();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/newsletter');
            Database::delete('newsletter', 'id = ?', [(int) ($_POST['id'] ?? 0)]);
            flash('success', 'Adresse supprimée.');
            redirect('/admin/newsletter');
        }
        if (($_GET['export'] ?? '') === 'csv') {
            $this->csv('newsletter', ['E-mail', 'Date d’inscription'], array_map(static fn($r) => [$r['email'], $r['created_at']], Database::all('SELECT * FROM newsletter ORDER BY created_at DESC')));
        }
        return view('admin/newsletter', ['pageTitle' => 'Abonnés à la newsletter', 'rows' => Database::all('SELECT * FROM newsletter ORDER BY created_at DESC')], 'admin/layout');
    }

    /* ------------------------------------------------------------ Réglages */

    public static function settingsFields(): array
    {
        return [
            'Identité' => [
                ['site_name', 'Nom du site', 'text'], ['company_name', 'Raison sociale', 'text'], ['tagline', 'Slogan', 'text'],
            ],
            'Coordonnées' => [
                ['phone', 'Téléphone principal', 'text'], ['phone_mobile', 'Téléphone mobile / WhatsApp (affiché)', 'text'],
                ['whatsapp', 'Numéro WhatsApp (format international sans +, ex. 22667308185)', 'text'],
                ['email', 'E-mail affiché', 'email'], ['contact_recipient', 'E-mail de réception des formulaires', 'email'],
                ['address', 'Adresse', 'text'], ['opening_hours', 'Horaires d’ouverture', 'text'], ['map_query', 'Localisation sur la carte (page Contact)', 'text'],
            ],
            'Réseaux sociaux' => [
                ['facebook_url', 'Facebook', 'url'], ['youtube_url', 'YouTube (vidéo de présentation)', 'url'], ['instagram_url', 'Instagram', 'url'],
                ['linkedin_url', 'LinkedIn', 'url'], ['tiktok_url', 'TikTok', 'url'],
            ],
            'Page d’accueil' => [
                ['announcement', 'Bandeau d’annonce (barre supérieure)', 'text'],
                ['marquee', 'Mots du bandeau défilant (séparés par |)', 'text'],
                ['stat_years', 'Années d’expérience', 'number'], ['stat_sites', 'Nombre de sites', 'number'],
                ['stat_ranges', 'Nombre de gammes', 'number'], ['stat_regions', 'Régions ciblées', 'number'],
            ],
            'Référencement et e-mails' => [
                ['seo_title', 'Titre de la page d’accueil (Google)', 'text'], ['seo_description', 'Description (Google)', 'textarea'],
                ['autoreply', 'Envoyer un accusé de réception aux visiteurs', 'checkbox'],
            ],
        ];
    }

    public function settings(): string
    {
        $this->guard(true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/reglages');
            foreach (self::settingsFields() as $fields) {
                foreach ($fields as [$key, , $type]) {
                    $value = $type === 'checkbox' ? (!empty($_POST[$key]) ? '1' : '0') : trim((string) ($_POST[$key] ?? ''));
                    if ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        flash('error', 'Adresse e-mail invalide : ' . $value);
                        redirect('/admin/reglages');
                    }
                    if (Database::one('SELECT name FROM settings WHERE name = ?', [$key])) {
                        Database::update('settings', ['value' => $value], 'name = ?', [$key]);
                    } else {
                        Database::insert('settings', ['name' => $key, 'value' => $value]);
                    }
                }
            }
            flash('success', 'Réglages enregistrés.');
            redirect('/admin/reglages');
        }
        $values = [];
        foreach (Database::all('SELECT name, value FROM settings') as $r) {
            $values[$r['name']] = $r['value'];
        }
        return view('admin/settings', ['pageTitle' => 'Réglages du site', 'groups' => self::settingsFields(), 'values' => $values], 'admin/layout');
    }

    /* ------------------------------------------------------------ Compte */

    public function account(): string
    {
        $this->guard();
        $user = Database::one('SELECT * FROM users WHERE id = ?', [Auth::user()['id']]);
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPost('/admin/compte');
            $name = trim((string) ($_POST['name'] ?? ''));
            $email = strtolower(trim((string) ($_POST['email'] ?? '')));
            $current = (string) ($_POST['current_password'] ?? '');
            $new = (string) ($_POST['new_password'] ?? '');
            if ($name === '') {
                $errors['name'] = 'Nom obligatoire.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'E-mail invalide.';
            } elseif (Database::value('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, $user['id']])) {
                $errors['email'] = 'Cet e-mail est déjà utilisé.';
            }
            if (!password_verify($current, (string) $user['password'])) {
                $errors['current_password'] = 'Mot de passe actuel incorrect.';
            }
            if ($new !== '' && strlen($new) < 8) {
                $errors['new_password'] = '8 caractères minimum.';
            }
            if ($new !== '' && $new !== (string) ($_POST['new_password_confirm'] ?? '')) {
                $errors['new_password_confirm'] = 'Les mots de passe ne correspondent pas.';
            }
            if (!$errors) {
                $data = ['name' => $name, 'email' => $email];
                if ($new !== '') {
                    $data['password'] = password_hash($new, PASSWORD_DEFAULT);
                }
                Database::update('users', $data, 'id = ?', [$user['id']]);
                session_regenerate_id(true);
                flash('success', 'Votre compte a été mis à jour.');
                redirect('/admin/compte');
            }
            $user = array_merge($user, ['name' => $name, 'email' => $email]);
        }
        return view('admin/account', ['pageTitle' => 'Mon compte', 'user' => $user, 'errors' => $errors], 'admin/layout');
    }

    /* ------------------------------------------------------------- Outils */

    public function tools(): string
    {
        $this->guard(true);
        $mailResult = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'test_mail') {
            $this->checkPost('/admin/outils');
            $to = trim((string) ($_POST['to'] ?? ''));
            $mailer = new Mailer();
            $ok = $mailer->send($to, 'GELPAZ IMMO — E-mail de test', Mailer::template('E-mail de test', ['Envoyé le' => date_fr(now(), 'datetime'), 'Méthode' => (string) config('mail.driver')], 'Si vous lisez ce message, l’envoi des e-mails fonctionne correctement.'));
            $mailResult = $ok ? ['ok' => true, 'message' => 'E-mail envoyé à ' . $to . ' (méthode : ' . config('mail.driver') . ').'] : ['ok' => false, 'message' => 'Échec de l’envoi : ' . $mailer->error()];
        }
        $pending = Media::pending();
        return view('admin/tools', [
            'pageTitle' => 'Outils',
            'pending' => count($pending),
            'mailResult' => $mailResult,
            'system' => [
                'Version de PHP' => PHP_VERSION,
                'Base de données' => strtoupper(Database::driver()),
                'Envoi des e-mails' => (string) config('mail.driver'),
                'Extension GD (images)' => extension_loaded('gd') ? 'Oui' : 'Non',
                'cURL (import des images)' => function_exists('curl_init') ? 'Oui' : (ini_get('allow_url_fopen') ? 'Non (allow_url_fopen)' : 'Non'),
                'Taille max. d’envoi' => ini_get('upload_max_filesize') . ' / ' . ini_get('post_max_size'),
                'Version du site' => APP_VERSION,
            ],
        ], 'admin/layout');
    }

    public function importBatch(): never
    {
        $this->guard(true);
        if (!csrf_verify()) {
            json_response(['ok' => false, 'message' => 'Session expirée.'], 403);
        }
        @set_time_limit(120);
        $skip = array_values(array_filter((array) ($_POST['skip'] ?? []), 'is_string'));
        try {
            $r = Media::importBatch(3, $skip);
            json_response(['ok' => true] + $r);
        } catch (Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function editorUpload(): never
    {
        $this->guard();
        if (!csrf_verify()) {
            json_response(['ok' => false, 'message' => 'Session expirée.'], 403);
        }
        $files = Media::files($_FILES['image'] ?? null);
        if (!$files) {
            json_response(['ok' => false, 'message' => 'Aucune image reçue.'], 422);
        }
        try {
            $path = Media::upload($files[0], 'posts', 1600);
            json_response(['ok' => true, 'url' => url($path)]);
        } catch (Throwable $e) {
            json_response(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** Export CSV (séparateur « ; » compatible Excel). */
    private function csv(string $name, array $header, array $rows): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="gelpaz-' . $name . '-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $header, ';', '"', '\\');
        foreach ($rows as $r) {
            fputcsv($out, array_map(static fn($v) => (string) $v, $r), ';', '"', '\\');
        }
        fclose($out);
        exit;
    }
}
