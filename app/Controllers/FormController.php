<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\Mailer;

/**
 * Traitement des formulaires publics : contact / demande de visite, souscription,
 * newsletter et commentaires. Protection : CSRF, pot de miel, délai minimal, limite de fréquence.
 */
final class FormController
{
    public function contact(): never
    {
        $back = $this->back('/contact');
        $this->guard('contact', $back);
        $in = $this->input(['type', 'name', 'email', 'phone', 'subject', 'message', 'property_id', 'visit_date', 'consent']);
        $type = in_array($in['type'], ['contact', 'visite', 'information'], true) ? $in['type'] : 'contact';
        $errors = [];
        if (mb_strlen($in['name']) < 2) {
            $errors['name'] = 'Merci d’indiquer votre nom.';
        }
        if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse e-mail invalide.';
        }
        if ($in['email'] === '' && $in['phone'] === '') {
            $errors['email'] = 'Indiquez au moins une adresse e-mail ou un numéro de téléphone.';
        }
        if ($in['phone'] !== '' && !preg_match('/^[0-9 +().\-]{6,25}$/', $in['phone'])) {
            $errors['phone'] = 'Numéro de téléphone invalide.';
        }
        if ($type === 'contact' && mb_strlen($in['message']) < 10) {
            $errors['message'] = 'Votre message est un peu court (10 caractères minimum).';
        }
        if (mb_strlen($in['message']) > 5000) {
            $errors['message'] = 'Votre message est trop long (5 000 caractères maximum).';
        }
        if ($in['consent'] === '') {
            $errors['consent'] = 'Merci d’accepter le traitement de vos données.';
        }
        if ($errors) {
            $this->respond(false, 'Merci de corriger les champs indiqués.', $errors, $back);
        }
        $property = null;
        if ((int) $in['property_id'] > 0) {
            $property = Database::one('SELECT id, title, slug, reference FROM properties WHERE id = ?', [(int) $in['property_id']]);
        }
        $subject = $in['subject'] !== '' ? $in['subject'] : match ($type) {
            'visite' => 'Demande de visite',
            'information' => 'Demande d’informations',
            default => 'Message depuis le site',
        };
        Database::insert('messages', [
            'type' => $type, 'name' => $in['name'], 'email' => $in['email'], 'phone' => $in['phone'],
            'subject' => mb_substr($subject, 0, 190), 'message' => $in['message'], 'property_id' => $property['id'] ?? null,
            'visit_date' => $in['visit_date'] !== '' ? mb_substr($in['visit_date'], 0, 30) : null, 'is_read' => 0,
            'ip' => client_ip(), 'user_agent' => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255), 'created_at' => now(),
        ]);
        $rows = [
            'Type de demande' => match ($type) { 'visite' => 'Demande de visite', 'information' => 'Demande d’informations', default => 'Contact' },
            'Nom' => $in['name'], 'E-mail' => $in['email'], 'Téléphone' => $in['phone'], 'Objet' => $subject,
            'Logement' => $property ? $property['title'] . ' (' . $property['reference'] . ') — ' . absolute_url('/logements/' . $property['slug']) : '',
            'Date de visite souhaitée' => $in['visit_date'] !== '' ? date_fr($in['visit_date']) : '',
        ];
        $this->notify('[Site GELPAZ] ' . $subject . ' — ' . $in['name'], 'Nouvelle demande reçue sur le site', $rows, $in['message'], $in['email'], $in['name']);
        $this->autoReply($in['email'], $in['name'], 'Nous avons bien reçu votre demande « ' . $subject . ' ». Un conseiller GELPAZ IMMO vous recontacte très rapidement.');
        $this->respond(true, $type === 'visite'
            ? 'Merci ! Votre demande de visite a bien été envoyée. Nous vous recontactons très vite pour fixer le rendez-vous.'
            : 'Merci ! Votre message a bien été envoyé. Notre équipe vous répond dans les meilleurs délais.', [], $back);
    }

    public function subscription(): never
    {
        $back = $this->back('/souscription-logement');
        $this->guard('souscription', $back);
        $in = $this->input(['full_name', 'email', 'phone', 'country', 'city', 'villa_type', 'site', 'payment_mode', 'message', 'consent']);
        $errors = [];
        if (mb_strlen($in['full_name']) < 3) {
            $errors['full_name'] = 'Merci d’indiquer vos nom et prénom.';
        }
        if ($in['phone'] === '' || !preg_match('/^[0-9 +().\-]{6,25}$/', $in['phone'])) {
            $errors['phone'] = 'Merci d’indiquer un numéro de téléphone valide.';
        }
        if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse e-mail invalide.';
        }
        $types = ['Villa F3 moyen standing', 'Villa F4 moyen standing', 'F5 duplex haut standing', 'Projet personnalisé'];
        if (!in_array($in['villa_type'], $types, true)) {
            $errors['villa_type'] = 'Merci de choisir un type de logement.';
        }
        $modes = ['Au comptant', 'Financement bancaire', 'À définir avec un conseiller'];
        if (!in_array($in['payment_mode'], $modes, true)) {
            $errors['payment_mode'] = 'Merci de choisir un mode de paiement.';
        }
        if ($in['consent'] === '') {
            $errors['consent'] = 'Merci d’accepter d’être recontacté(e).';
        }
        if ($errors) {
            $this->respond(false, 'Merci de corriger les champs indiqués.', $errors, $back);
        }
        Database::insert('subscriptions', [
            'full_name' => $in['full_name'], 'email' => $in['email'], 'phone' => $in['phone'], 'country' => $in['country'],
            'city' => $in['city'], 'villa_type' => $in['villa_type'], 'site' => mb_substr($in['site'], 0, 150),
            'payment_mode' => $in['payment_mode'], 'message' => mb_substr($in['message'], 0, 5000), 'status' => 'nouveau',
            'notes' => '', 'ip' => client_ip(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->notify('[Souscription] ' . $in['villa_type'] . ' — ' . $in['full_name'], 'Nouvelle demande de souscription', [
            'Nom complet' => $in['full_name'], 'Téléphone' => $in['phone'], 'E-mail' => $in['email'],
            'Pays de résidence' => $in['country'], 'Ville' => $in['city'], 'Type de logement' => $in['villa_type'],
            'Site souhaité' => $in['site'], 'Mode de paiement' => $in['payment_mode'],
        ], $in['message'], $in['email'], $in['full_name']);
        $this->autoReply($in['email'], $in['full_name'], 'Nous avons bien reçu votre demande de souscription (' . $in['villa_type'] . '). Un conseiller vous contacte rapidement pour la suite de la procédure.');
        $this->respond(true, 'Félicitations ! Votre demande de souscription a bien été enregistrée. Un conseiller GELPAZ IMMO vous contacte très rapidement.', [], $back);
    }

    public function newsletter(): never
    {
        $back = $this->back('/');
        $this->guard('newsletter', $back, 2);
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->respond(false, 'Merci d’indiquer une adresse e-mail valide.', ['email' => 'Adresse e-mail invalide.'], $back);
        }
        if (!Database::one('SELECT id FROM newsletter WHERE email = ?', [$email])) {
            Database::insert('newsletter', ['email' => $email, 'ip' => client_ip(), 'created_at' => now()]);
        }
        $this->respond(true, 'Merci ! Vous êtes inscrit(e) à notre lettre d’information.', [], $back);
    }

    public function comment(string $slug): never
    {
        $post = Database::one('SELECT id, title, slug FROM posts WHERE slug = ? AND is_published = 1', [$slug]);
        if (!$post) {
            abort(404);
        }
        $back = '/actualites/' . $post['slug'] . '#commentaires';
        $this->guard('commentaire', $back);
        $in = $this->input(['name', 'email', 'content', 'parent_id']);
        $errors = [];
        if (mb_strlen($in['name']) < 2) {
            $errors['name'] = 'Merci d’indiquer votre nom.';
        }
        if (!filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse e-mail invalide.';
        }
        if (mb_strlen($in['content']) < 5 || mb_strlen($in['content']) > 3000) {
            $errors['content'] = 'Votre commentaire doit contenir entre 5 et 3 000 caractères.';
        }
        if (preg_match_all('#https?://#i', $in['content']) > 2) {
            $errors['content'] = 'Votre commentaire contient trop de liens.';
        }
        if ($errors) {
            $this->respond(false, 'Merci de corriger les champs indiqués.', $errors, $back);
        }
        $parent = (int) $in['parent_id'];
        if ($parent && !Database::one('SELECT id FROM comments WHERE id = ? AND post_id = ?', [$parent, $post['id']])) {
            $parent = 0;
        }
        Database::insert('comments', [
            'post_id' => $post['id'], 'parent_id' => $parent ?: null, 'name' => $in['name'], 'email' => $in['email'],
            'content' => $in['content'], 'status' => 'pending', 'ip' => client_ip(), 'created_at' => now(),
        ]);
        $this->notify('[Commentaire à modérer] ' . $post['title'], 'Nouveau commentaire à modérer', [
            'Article' => $post['title'], 'Nom' => $in['name'], 'E-mail' => $in['email'],
            'Modération' => absolute_url('/admin/commentaires'),
        ], $in['content']);
        $this->respond(true, 'Merci ! Votre commentaire a bien été reçu : il sera publié après validation par notre équipe.', [], $back);
    }

    /* -------------------------------------------------------------- Outils */

    private function input(array $keys): array
    {
        $out = [];
        foreach ($keys as $k) {
            $v = $_POST[$k] ?? '';
            $out[$k] = is_string($v) ? trim(str_replace("\0", '', $v)) : '';
        }
        return $out;
    }

    private function back(string $default): string
    {
        $ref = (string) ($_POST['_back'] ?? '');
        return ($ref !== '' && str_starts_with($ref, '/') && !str_starts_with($ref, '//')) ? $ref : $default;
    }

    private function guard(string $scope, string $back, int $minSeconds = 3): void
    {
        if (!csrf_verify()) {
            $this->respond(false, 'Votre session a expiré. Merci de recharger la page puis de réessayer.', [], $back);
        }
        if ($err = antispam_check($minSeconds)) {
            $this->respond(false, $err, [], $back);
        }
        if (!rate_limit('form:' . $scope . ':' . client_ip(), 5, 600)) {
            $this->respond(false, 'Vous avez envoyé plusieurs demandes en peu de temps. Merci de réessayer dans quelques minutes ou de nous appeler.', [], $back);
        }
    }

    private function notify(string $subject, string $title, array $rows, string $message = '', string $replyTo = '', string $replyName = ''): void
    {
        $to = (string) setting('contact_recipient', setting('email', 'infos@gelpaz.com'));
        $html = Mailer::template($title, $rows, 'Envoyé depuis ' . e(absolute_url('/')) . ' le ' . date_fr(now(), 'datetime') . '.', $message);
        (new Mailer())->send($to, $subject, $html, $replyTo ?: null, $replyName ?: null);
    }

    private function autoReply(string $email, string $name, string $text): void
    {
        if ($email === '' || setting('autoreply', '1') !== '1') {
            return;
        }
        $html = Mailer::template(
            'Merci ' . $name . ' !',
            [],
            e($text) . '<br><br>Pour toute urgence, appelez-nous au <strong>' . e(setting('phone')) . '</strong> ou écrivez-nous sur WhatsApp au <strong>' . e(setting('phone_mobile')) . '</strong>.<br><br>À très bientôt,<br><strong>L’équipe GELPAZ IMMO</strong>'
        );
        (new Mailer())->send($email, 'GELPAZ IMMO — Nous avons bien reçu votre demande', $html, (string) setting('email'));
    }

    private function respond(bool $ok, string $message, array $errors, string $back): never
    {
        if (is_ajax()) {
            json_response(['ok' => $ok, 'message' => $message, 'errors' => $errors], $ok ? 200 : 422);
        }
        if ($ok) {
            clear_old_input();
        } else {
            remember_input($_POST);
            flash('errors', $errors);
        }
        flash($ok ? 'success' : 'error', $message);
        redirect($back);
    }
}
