<?php
/**
 * Configuration des modules du back-office (CRUD générique).
 *
 * Types de champs : text, slug, textarea, richtext, number, select, checkbox, image, datetime, email, url,
 * password, lines, section. Options utiles : required, col (half|third|full), help, options, source (pour slug),
 * folder (pour image), remote (colonne d'URL distante à afficher), default.
 */
$statuses = ['disponible' => 'Disponible', 'reserve' => 'Réservé', 'bientot' => 'Bientôt disponible', 'vendu' => 'Vendu', 'loue' => 'Loué'];
$icons = ['home' => 'Maison', 'building' => 'Immeuble', 'building-2' => 'Bâtiment', 'key' => 'Clé', 'clipboard-check' => 'Gestion', 'pencil-ruler' => 'Plans', 'hard-hat' => 'Chantier', 'landmark' => 'Patrimoine', 'scale' => 'Expertise', 'shield-check' => 'Sécurité', 'handshake' => 'Partenariat', 'trees' => 'Environnement', 'wallet' => 'Finances', 'users' => 'Personnes', 'map-pin' => 'Localisation', 'award' => 'Distinction'];

return [
    'logements' => [
        'table' => 'properties', 'label' => 'Logements', 'singular' => 'logement', 'icon' => 'home', 'gender' => 'm',
        'order' => 'sort ASC, id DESC', 'search' => ['title', 'reference', 'city', 'address'], 'sortable' => true,
        'view' => '/logements/{slug}', 'gallery' => true, 'plans' => true,
        'columns' => [
            ['image', 'Photo', 'image'], ['title', 'Titre', 'title'], ['category_id', 'Gamme', 'relation:property_categories'],
            ['transaction', 'Transaction', 'map'], ['status', 'Statut', 'map'], ['is_featured', 'Vedette', 'toggle'], ['is_published', 'En ligne', 'toggle'],
        ],
        'maps' => ['transaction' => ['vente' => 'À vendre', 'location' => 'À louer'], 'status' => $statuses],
        'fields' => [
            ['section', 'Informations principales'],
            ['title', 'Titre du logement', 'text', 'required' => true, 'col' => 'full'],
            ['slug', 'Adresse de la page (slug)', 'slug', 'source' => 'title', 'col' => 'half', 'help' => 'Généré automatiquement à partir du titre.'],
            ['reference', 'Référence', 'text', 'col' => 'half'],
            ['category_id', 'Gamme', 'select', 'options' => 'table:property_categories', 'col' => 'third'],
            ['transaction', 'Transaction', 'select', 'options' => ['vente' => 'À vendre', 'location' => 'À louer'], 'col' => 'third', 'default' => 'vente'],
            ['status', 'Statut', 'select', 'options' => $statuses, 'col' => 'third', 'default' => 'disponible'],
            ['site_id', 'Site / cité', 'select', 'options' => 'table:sites', 'col' => 'half'],
            ['city', 'Ville', 'text', 'col' => 'half', 'default' => 'Ouagadougou'],
            ['address', 'Adresse / localisation affichée', 'text', 'col' => 'full'],
            ['section', 'Caractéristiques'],
            ['built_area', 'Surface bâtie (m²)', 'number', 'col' => 'third'],
            ['land_area', 'Superficie de la parcelle (m²)', 'number', 'col' => 'third'],
            ['rooms', 'Nombre de pièces', 'number', 'col' => 'third'],
            ['bedrooms', 'Chambres', 'number', 'col' => 'third'],
            ['bathrooms', 'Salles d’eau / bain', 'number', 'col' => 'third'],
            ['living_rooms', 'Salons / séjours', 'number', 'col' => 'third'],
            ['kitchens', 'Cuisines', 'number', 'col' => 'third'],
            ['terraces', 'Terrasses', 'number', 'col' => 'third'],
            ['garages', 'Garages / parkings', 'number', 'col' => 'third'],
            ['floors', 'Niveaux', 'number', 'col' => 'third'],
            ['year_built', 'Année de construction', 'number', 'col' => 'third'],
            ['section', 'Présentation'],
            ['excerpt', 'Résumé (cartes et référencement)', 'textarea', 'col' => 'full', 'rows' => 3],
            ['description', 'Description détaillée', 'richtext', 'col' => 'full'],
            ['features', 'Équipements et prestations', 'lines', 'col' => 'full', 'help' => 'Un élément par ligne (ex. : « Cuisine équipée »).'],
            ['section', 'Médias et options'],
            ['image', 'Photo principale', 'image', 'folder' => 'properties', 'remote' => 'image_remote', 'col' => 'half'],
            ['video_url', 'Vidéo (lien YouTube)', 'url', 'col' => 'half'],
            ['map_query', 'Localisation sur la carte', 'text', 'col' => 'half', 'help' => 'Ex. : « Cité de l’Intégration, Ouaga 2000 ». Laisser vide pour utiliser le site.'],
            ['sort', 'Ordre d’affichage', 'number', 'col' => 'half', 'default' => 0],
            ['is_featured', 'Mettre en vedette', 'checkbox', 'col' => 'half'],
            ['is_published', 'Publié sur le site', 'checkbox', 'col' => 'half', 'default' => 1],
        ],
    ],

    'gammes' => [
        'table' => 'property_categories', 'label' => 'Gammes de logements', 'singular' => 'gamme', 'icon' => 'layers', 'gender' => 'f',
        'order' => 'sort ASC, name ASC', 'search' => ['name'], 'sortable' => true,
        'columns' => [['name', 'Nom', 'title'], ['slug', 'Slug', 'text'], ['sort', 'Ordre', 'text']],
        'fields' => [
            ['name', 'Nom de la gamme', 'text', 'required' => true, 'col' => 'half'],
            ['slug', 'Slug', 'slug', 'source' => 'name', 'col' => 'half'],
            ['icon', 'Icône', 'select', 'options' => $icons, 'col' => 'half'],
            ['sort', 'Ordre', 'number', 'col' => 'half', 'default' => 0],
            ['description', 'Description courte', 'textarea', 'col' => 'full', 'rows' => 3],
        ],
    ],

    'sites' => [
        'table' => 'sites', 'label' => 'Sites', 'singular' => 'site', 'icon' => 'map-pin', 'gender' => 'm',
        'order' => 'sort ASC, name ASC', 'search' => ['name', 'city', 'area'], 'sortable' => true, 'view' => '/nos-sites#{slug}',
        'columns' => [['image', 'Photo', 'image'], ['name', 'Nom', 'title'], ['city', 'Ville', 'text'], ['status', 'Statut', 'map'], ['is_featured', 'Principal', 'toggle']],
        'maps' => ['status' => ['' => 'Site GELPAZ IMMO', 'commercialisation' => 'En commercialisation', 'en_cours' => 'En cours de réalisation', 'livre' => 'Livré', 'a_venir' => 'À venir', 'international' => 'Projet international']],
        'fields' => [
            ['name', 'Nom du site', 'text', 'required' => true, 'col' => 'half'],
            ['slug', 'Slug', 'slug', 'source' => 'name', 'col' => 'half'],
            ['city', 'Ville', 'text', 'col' => 'third'],
            ['area', 'Zone / précision', 'text', 'col' => 'third'],
            ['status', 'Statut', 'select', 'options' => ['' => 'Site GELPAZ IMMO', 'commercialisation' => 'En commercialisation', 'en_cours' => 'En cours de réalisation', 'livre' => 'Livré', 'a_venir' => 'À venir', 'international' => 'Projet international'], 'col' => 'third'],
            ['description', 'Description', 'textarea', 'col' => 'full', 'rows' => 4],
            ['map_query', 'Localisation (carte Google)', 'text', 'col' => 'half'],
            ['image', 'Photo', 'image', 'folder' => 'sites', 'remote' => 'image_remote', 'col' => 'half'],
            ['sort', 'Ordre', 'number', 'col' => 'half', 'default' => 0],
            ['is_featured', 'Site principal (mis en avant)', 'checkbox', 'col' => 'half', 'default' => 1],
        ],
    ],

    'actualites' => [
        'table' => 'posts', 'label' => 'Actualités', 'singular' => 'actualité', 'icon' => 'newspaper', 'gender' => 'f',
        'order' => 'published_at DESC, id DESC', 'search' => ['title', 'category', 'tags'], 'view' => '/actualites/{slug}', 'post_gallery' => true,
        'columns' => [['image', 'Image', 'image'], ['title', 'Titre', 'title'], ['category', 'Catégorie', 'text'], ['published_at', 'Date', 'date'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['title', 'Titre', 'text', 'required' => true, 'col' => 'full'],
            ['slug', 'Slug', 'slug', 'source' => 'title', 'col' => 'half'],
            ['category', 'Catégorie', 'text', 'col' => 'half', 'datalist' => ['Partenariats', 'Législation', 'Réalisations', 'Événements', 'RSE', 'Distinctions'], 'default' => 'Actualités'],
            ['excerpt', 'Chapeau / résumé', 'textarea', 'col' => 'full', 'rows' => 3],
            ['content', 'Contenu', 'richtext', 'col' => 'full', 'required' => true],
            ['image', 'Image principale', 'image', 'folder' => 'posts', 'remote' => 'image_remote', 'col' => 'half'],
            ['tags', 'Mots-clés (séparés par des virgules)', 'text', 'col' => 'half'],
            ['author', 'Auteur', 'text', 'col' => 'third', 'default' => 'GELPAZ IMMO'],
            ['published_at', 'Date de publication', 'datetime', 'col' => 'third'],
            ['is_published', 'Publié', 'checkbox', 'col' => 'third', 'default' => 1],
        ],
    ],

    'services' => [
        'table' => 'services', 'label' => 'Activités / services', 'singular' => 'service', 'icon' => 'briefcase', 'gender' => 'm',
        'order' => 'sort ASC, id ASC', 'search' => ['title'], 'sortable' => true, 'view' => '/nos-activites/{slug}',
        'columns' => [['image', 'Image', 'image'], ['title', 'Titre', 'title'], ['is_featured', 'Accueil', 'toggle'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['title', 'Titre', 'text', 'required' => true, 'col' => 'half'],
            ['slug', 'Slug', 'slug', 'source' => 'title', 'col' => 'half'],
            ['icon', 'Icône', 'select', 'options' => $icons, 'col' => 'half'],
            ['image', 'Image', 'image', 'folder' => 'services', 'col' => 'half'],
            ['excerpt', 'Résumé', 'textarea', 'col' => 'full', 'rows' => 3],
            ['content', 'Contenu détaillé', 'richtext', 'col' => 'full'],
            ['features', 'Points forts', 'lines', 'col' => 'full', 'help' => 'Un point par ligne.'],
            ['sort', 'Ordre', 'number', 'col' => 'third', 'default' => 0],
            ['is_featured', 'Afficher sur l’accueil', 'checkbox', 'col' => 'third'],
            ['is_published', 'Publié', 'checkbox', 'col' => 'third', 'default' => 1],
        ],
    ],

    'faq' => [
        'table' => 'faqs', 'label' => 'FAQ', 'singular' => 'question', 'icon' => 'help-circle', 'gender' => 'f',
        'order' => 'sort ASC, id ASC', 'search' => ['question', 'answer', 'category'], 'sortable' => true,
        'columns' => [['question', 'Question', 'title'], ['category', 'Thème', 'text'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['question', 'Question', 'text', 'required' => true, 'col' => 'full'],
            ['answer', 'Réponse', 'textarea', 'required' => true, 'col' => 'full', 'rows' => 6],
            ['category', 'Thème', 'text', 'col' => 'third', 'datalist' => ['Général', 'Logements', 'Souscription', 'Services']],
            ['sort', 'Ordre', 'number', 'col' => 'third', 'default' => 0],
            ['is_published', 'Publiée', 'checkbox', 'col' => 'third', 'default' => 1],
        ],
    ],

    'temoignages' => [
        'table' => 'testimonials', 'label' => 'Témoignages', 'singular' => 'témoignage', 'icon' => 'quote', 'gender' => 'm',
        'order' => 'sort ASC, id ASC', 'search' => ['name', 'content'], 'sortable' => true,
        'columns' => [['name', 'Nom', 'title'], ['role', 'Qualité', 'text'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['name', 'Nom', 'text', 'required' => true, 'col' => 'half'],
            ['role', 'Qualité (ex. : Résident depuis 2021)', 'text', 'col' => 'half'],
            ['content', 'Témoignage', 'textarea', 'required' => true, 'col' => 'full', 'rows' => 5],
            ['sort', 'Ordre', 'number', 'col' => 'half', 'default' => 0],
            ['is_published', 'Publié', 'checkbox', 'col' => 'half', 'default' => 1],
        ],
    ],

    'partenaires' => [
        'table' => 'partners', 'label' => 'Partenaires', 'singular' => 'partenaire', 'icon' => 'handshake', 'gender' => 'm',
        'order' => 'sort ASC, id ASC', 'search' => ['name'], 'sortable' => true,
        'columns' => [['logo', 'Logo', 'image'], ['name', 'Nom', 'title'], ['url', 'Site web', 'text'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['name', 'Nom du partenaire', 'text', 'required' => true, 'col' => 'half'],
            ['url', 'Site web (facultatif)', 'url', 'col' => 'half'],
            ['logo', 'Logo', 'image', 'folder' => 'partners', 'remote' => 'logo_remote', 'col' => 'half'],
            ['sort', 'Ordre', 'number', 'col' => 'third', 'default' => 0],
            ['is_published', 'Publié', 'checkbox', 'col' => 'third', 'default' => 1],
        ],
    ],

    'diaporama' => [
        'table' => 'slides', 'label' => 'Diaporama d’accueil', 'singular' => 'diapositive', 'icon' => 'images', 'gender' => 'f',
        'order' => 'sort ASC, id ASC', 'search' => ['title'], 'sortable' => true,
        'columns' => [['image', 'Image', 'image'], ['title', 'Titre', 'title'], ['is_published', 'En ligne', 'toggle']],
        'fields' => [
            ['pre_title', 'Sur-titre', 'text', 'col' => 'half'],
            ['title', 'Titre', 'text', 'required' => true, 'col' => 'half'],
            ['text', 'Texte', 'textarea', 'col' => 'full', 'rows' => 3],
            ['image', 'Image de fond (1920 px de large conseillé)', 'image', 'folder' => 'slides', 'col' => 'full'],
            ['button_text', 'Bouton 1 — texte', 'text', 'col' => 'half'],
            ['button_url', 'Bouton 1 — lien', 'text', 'col' => 'half'],
            ['button2_text', 'Bouton 2 — texte', 'text', 'col' => 'half'],
            ['button2_url', 'Bouton 2 — lien', 'text', 'col' => 'half'],
            ['sort', 'Ordre', 'number', 'col' => 'half', 'default' => 0],
            ['is_published', 'Publiée', 'checkbox', 'col' => 'half', 'default' => 1],
        ],
    ],

    'utilisateurs' => [
        'table' => 'users', 'label' => 'Utilisateurs', 'singular' => 'utilisateur', 'icon' => 'users', 'gender' => 'm',
        'order' => 'name ASC', 'search' => ['name', 'email'], 'admin_only' => true,
        'columns' => [['name', 'Nom', 'title'], ['email', 'E-mail', 'text'], ['role', 'Rôle', 'map'], ['last_login_at', 'Dernière connexion', 'date']],
        'maps' => ['role' => ['admin' => 'Administrateur', 'editor' => 'Éditeur']],
        'fields' => [
            ['name', 'Nom', 'text', 'required' => true, 'col' => 'half'],
            ['email', 'E-mail (identifiant)', 'email', 'required' => true, 'col' => 'half', 'unique' => true],
            ['role', 'Rôle', 'select', 'options' => ['admin' => 'Administrateur (accès complet)', 'editor' => 'Éditeur (contenus et demandes)'], 'col' => 'half', 'default' => 'editor'],
            ['password', 'Mot de passe', 'password', 'col' => 'half', 'help' => '8 caractères minimum. Laisser vide pour ne pas le modifier.'],
        ],
    ],
];
