<?php
/**
 * Contenu initial du site (repris de gelpaz.com) : réglages, logements, sites,
 * services, FAQ, témoignages, partenaires et diaporama d'accueil.
 * Les images distantes pointent vers l'ancien site ; l'outil « Importer les médias »
 * du back-office les rapatrie sur votre hébergement.
 */
$u = 'https://gelpaz.com/wp-content/uploads/';
// Image WordPress : [originale, miniature éventuelle]
$wp = static fn(string $path, ?string $size = '835x467') => [
    $u . $path,
    $size ? $u . preg_replace('/(\.[a-z]+)$/i', '-' . $size . '$1', $path) : '',
];

$f4Features = "Surface bâtie de 145 m²\n3 chambres\n3 salles d’eau\n2 terrasses\nSalon\nSalle à manger\nCuisine\nCité viabilisée aux voies larges";
$f3Features = "Surface bâtie de 130 m²\n2 chambres\n2 salles d’eau\nSalon\nCuisine\nTerrasse\nCité viabilisée aux voies larges";

return [
    /* ------------------------------------------------------------ Réglages */
    'settings' => [
        'site_name' => 'GELPAZ IMMO',
        'company_name' => 'GELPAZ IMMO SA',
        'tagline' => 'La différence !',
        'phone' => '+226 25 37 10 55',
        'phone_mobile' => '+226 67 30 81 85',
        'whatsapp' => '22667308185',
        'email' => 'infos@gelpaz.com',
        'contact_recipient' => 'infos@gelpaz.com',
        'address' => 'Dagnoën, rue 29.128, Ouagadougou, Burkina Faso',
        'opening_hours' => '8h – 13h · 14h – 17h',
        'map_query' => 'Dagnoën, Ouagadougou, Burkina Faso',
        'facebook_url' => 'https://www.facebook.com/share/p/14JZtytkCf1/',
        'youtube_url' => 'https://youtu.be/IX-ySy8Mbi0',
        'instagram_url' => '',
        'linkedin_url' => '',
        'tiktok_url' => '',
        'announcement' => 'Souscription ouverte : villas F3, F4 et F5 duplex à la Cité de l’Intégration',
        'stat_years' => '30',
        'stat_sites' => '14',
        'stat_ranges' => '3',
        'stat_regions' => '17',
        'autoreply' => '1',
        'seo_title' => 'GELPAZ IMMO — Promoteur immobilier au Burkina Faso depuis 1987',
        'seo_description' => 'GELPAZ IMMO SA, promoteur immobilier à Ouagadougou : villas F3, F4 et F5 duplex, souscription logement, gestion locative, expertise et accompagnement sur mesure au Burkina Faso.',
        'marquee' => 'Ney waoongo|Adanse|Fôfô|Biali-biala|Yansi-yansi|Bienvenue',
    ],

    /* ------------------------------------------- Catégories de logements */
    'categories' => [
        ['name' => 'Villa F3 Moyen Standing', 'slug' => 'f3-moyen-standing', 'icon' => 'home', 'description' => 'Villas de 2 chambres, idéales pour les jeunes couples et les petites familles.', 'sort' => 1],
        ['name' => 'Villa F4 Moyen Standing', 'slug' => 'f4-moyen-standing', 'icon' => 'building', 'description' => 'Villas de 3 chambres, spacieuses et lumineuses, pour toute la famille.', 'sort' => 2],
        ['name' => 'F5 Duplex Haut Standing', 'slug' => 'f5-duplex-haut-standing', 'icon' => 'building-2', 'description' => 'Duplex de 4 chambres sur plus de 555 m², bureau, séjour privé et option piscine.', 'sort' => 3],
    ],

    /* ------------------------------------------------------------- Sites */
    'sites' => [
        ['name' => 'Cité de l’Intégration', 'slug' => 'cite-de-l-integration', 'city' => 'Ouagadougou', 'area' => 'Extension Sud de Ouaga 2000', 'status' => 'commercialisation', 'is_featured' => 1, 'image_remote' => $u . '2025/09/image-17.png', 'map_query' => 'Cité de l\'Intégration, Ouaga 2000, Ouagadougou', 'description' => 'Notre site phare dans l’extension sud de Ouaga 2000 : des villas F3, F4 et F5 duplex au cœur d’une cité viabilisée, aux voies principales larges et aux accès dégagés. Les premiers résidents s’y sont installés dès 2021.'],
        ['name' => 'Cité de l’Intégration — Extension Nord', 'slug' => 'cite-de-l-integration-extension-nord', 'city' => 'Ouagadougou', 'area' => 'Ouaga 2000', 'status' => '', 'is_featured' => 1, 'map_query' => 'Ouaga 2000, Ouagadougou', 'description' => 'Le prolongement nord de la Cité de l’Intégration, pensé pour accueillir de nouvelles familles dans un cadre de vie moderne.'],
        ['name' => 'Cité de l’Intégration — Extension Sud', 'slug' => 'cite-de-l-integration-extension-sud', 'city' => 'Ouagadougou', 'area' => 'Ouaga 2000', 'status' => '', 'is_featured' => 1, 'map_query' => 'Ouaga 2000, Ouagadougou', 'description' => 'L’extension sud de la cité, dans la continuité de l’aménagement de la Cité de l’Intégration.'],
        ['name' => 'Cité de l’Espoir', 'slug' => 'cite-de-l-espoir', 'city' => '', 'area' => 'Burkina Faso', 'status' => '', 'is_featured' => 1, 'map_query' => '', 'description' => 'Une cité GELPAZ IMMO conçue pour rendre accessible un logement décent et durable.'],
        ['name' => 'Site de Sabtoana 1', 'slug' => 'site-de-sabtoana-1', 'city' => 'Sabtoana', 'area' => 'Burkina Faso', 'status' => '', 'is_featured' => 1, 'map_query' => 'Sabtoana, Burkina Faso', 'description' => 'Premier site de Sabtoana, un programme résidentiel GELPAZ IMMO.'],
        ['name' => 'Site de Sabtoana 2', 'slug' => 'site-de-sabtoana-2', 'city' => 'Sabtoana', 'area' => 'Burkina Faso', 'status' => '', 'is_featured' => 1, 'map_query' => 'Sabtoana, Burkina Faso', 'description' => 'Second site de Sabtoana, dans la continuité du premier programme.'],
        ['name' => 'Cité de Pô', 'slug' => 'cite-de-po', 'city' => 'Pô', 'area' => 'Région du Nahouri', 'status' => 'livre', 'is_featured' => 1, 'map_query' => 'Pô, Burkina Faso', 'description' => 'Remise partielle de logements F3 et F4 aux bénéficiaires : une étape importante dans la concrétisation de notre programme immobilier à Pô.'],
        ['name' => 'Ouédraogo Yaar', 'slug' => 'ouedraogo-yaar', 'city' => '', 'area' => '', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'Ponsomtenga 957', 'slug' => 'ponsomtenga-957', 'city' => 'Ponsomtenga', 'area' => '', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'Garghin', 'slug' => 'garghin', 'city' => 'Garghin', 'area' => 'Ouagadougou', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'Saaba', 'slug' => 'saaba', 'city' => 'Saaba', 'area' => '', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'Léo Wan', 'slug' => 'leo-wan', 'city' => 'Léo', 'area' => '', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'Kaya', 'slug' => 'kaya', 'city' => 'Kaya', 'area' => '', 'status' => '', 'is_featured' => 0, 'map_query' => '', 'description' => ''],
        ['name' => 'N’Djaména — Gredia, Gaoui et Guilmey', 'slug' => 'ndjamena-tchad', 'city' => 'N’Djaména (Tchad)', 'area' => 'Gredia 20,13 ha · Gaoui 1,81 ha · Guilmey 11 ha', 'status' => 'international', 'is_featured' => 0, 'map_query' => 'N\'Djamena, Tchad', 'description' => 'Projets de développement urbain validés en septembre 2026 en partenariat avec la SOPROFIM : 300 à 400 logements prévus sur trois sites de la capitale tchadienne.'],
    ],

    /* --------------------------------------------------------- Logements */
    'properties' => [
        [
            'title' => 'F5 Duplex haut standing', 'slug' => 'f5-duplex-haut-standing', 'legacy' => 'f5-haut-standing',
            'reference' => 'GZ-F5-01', 'category' => 'f5-duplex-haut-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou',
            'address' => 'Cité de l’Intégration — Extension Sud de Ouaga 2000',
            'land_area' => 555, 'rooms' => 5, 'bedrooms' => 4, 'bathrooms' => 3, 'living_rooms' => 2, 'kitchens' => 1, 'terraces' => 2, 'floors' => 2,
            'is_featured' => 1,
            'excerpt' => 'Un somptueux duplex de plus de 555 m² : 4 chambres, 3 salles de bain, séjour privé, bureau, cuisine entièrement aménagée, balcon et terrasses. Piscine privative en option.',
            'description' => '<p>Entrez dans l’univers du <strong>luxe et du raffinement</strong> avec ce somptueux F5 haut standing de plus de <strong>555 m²</strong>.</p><p>Il se compose de <strong>4 chambres</strong>, <strong>3 salles de bain</strong>, un <strong>séjour privé</strong>, un <strong>bureau</strong>, une <strong>cuisine entièrement aménagée</strong> et de nombreux espaces de détente : <strong>balcon, terrasses, salon</strong>.</p><p>Vous avez également la possibilité d’ajouter une <strong>piscine privative</strong> pour plus de confort.</p><p>Une résidence prestigieuse pour ceux qui souhaitent <strong>allier élégance, modernité et confort absolu</strong>.</p>',
            'features' => "Duplex sur deux niveaux\nParcelle de plus de 555 m²\n4 chambres\n3 salles de bain\nSalon et séjour privé\nBureau\nCuisine entièrement aménagée\nBalcon\nTerrasses\nOption piscine privative",
            'cover' => [$u . '2025/09/Image20.jpg', ''],
            'images' => array_map(static fn($n) => [$u . '2025/09/Image' . $n . '.jpg', ''], [25, 17, 18, 19, 21, 22, 24, 23]),
        ],
        [
            'title' => 'F4 moyen standing — 400 m²', 'slug' => 'f4-moyen-standing', 'legacy' => 'f4-moyen-standing',
            'reference' => 'GZ-F4-400', 'category' => 'f4-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou',
            'address' => 'Cité de l’Intégration — Extension Sud de Ouaga 2000',
            'land_area' => 400, 'rooms' => 4, 'bedrooms' => 3, 'bathrooms' => 2, 'living_rooms' => 1, 'kitchens' => 1,
            'is_featured' => 1,
            'excerpt' => 'Profitez de l’espace et du confort avec ce F4 spacieux de 400 m² : un salon lumineux, 3 chambres confortables, une salle à manger et 2 salles de bain.',
            'description' => '<p>Profitez de l’espace et du confort avec ce <strong>F4 spacieux de 400 m²</strong>.</p><p>Il comprend un <strong>salon lumineux</strong>, <strong>3 chambres confortables</strong>, une <strong>salle à manger</strong> et <strong>2 salles de bain</strong>, pour une vie de famille agréable au sein de la Cité de l’Intégration.</p><p>Paiement au comptant ou avec financement bancaire : nos conseillers vous accompagnent à chaque étape.</p>',
            'features' => "Parcelle de 400 m²\n3 chambres\n2 salles de bain\nSalon lumineux\nSalle à manger\nCuisine\nCité viabilisée aux voies larges",
            'cover' => [$u . '2025/09/IMG-20250912-WA0019.jpg', $u . '2025/09/IMG-20250912-WA0019-525x328.jpg'],
            'images' => array_merge(
                [$wp('2025/09/IMG-20250912-WA0024.jpg', '810x467'), $wp('2025/09/IMG-20250912-WA0026.jpg'), $wp('2025/09/IMG-20250912-WA0021.jpg')],
                array_map(static fn($n) => [$u . '2025/09/Image' . $n . '.jpg', ''], [7, 8, 9, 10, 11, 12, 13, 14, 15, 16]),
                [$wp('2025/09/IMG-20250912-WA0023.jpg'), $wp('2025/09/IMG-20250912-WA0009.jpg'), $wp('2025/09/IMG-20250912-WA0010.jpg'), $wp('2025/09/IMG-20250912-WA0016.jpg'), $wp('2025/09/IMG-20250912-WA0027.jpg')]
            ),
        ],
        [
            'title' => 'F3 moyen standing — 300 m²', 'slug' => 'f3-moyen-standing', 'legacy' => 'f3-moyen-standing',
            'reference' => 'GZ-F3-300', 'category' => 'f3-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou',
            'address' => 'Cité de l’Intégration — Extension Sud de Ouaga 2000',
            'land_area' => 300, 'rooms' => 3, 'bedrooms' => 2, 'bathrooms' => 2, 'living_rooms' => 1, 'kitchens' => 1,
            'is_featured' => 1,
            'excerpt' => 'Offrez-vous un cadre de vie confortable et moderne avec ce F3 idéal pour les familles et les jeunes couples : 2 chambres et 2 salles de bain sur une parcelle de 300 m².',
            'description' => '<p>Offrez-vous un cadre de vie <strong>confortable et moderne</strong> avec ce F3 idéal pour les familles et les jeunes couples.</p><p>Composé de <strong>2 chambres</strong> et <strong>2 salles de bain</strong>, il s’élève sur une parcelle de <strong>300 m²</strong> au sein de la Cité de l’Intégration, dans l’extension sud de Ouaga 2000.</p><p>Paiement au comptant ou avec financement bancaire.</p>',
            'features' => "Parcelle de 300 m²\n2 chambres\n2 salles de bain\nSalon\nCuisine\nCité viabilisée aux voies larges",
            'cover' => [$u . '2025/09/Image6-1.jpg', ''],
            'images' => array_merge(
                [[$u . '2025/09/Villa-F3-scaled.jpg', '']],
                array_map(static fn($n) => [$u . '2025/09/Image' . $n . '.jpg', ''], ['1-1', '2-1', '3-1', '4-1', '5-1', '1', '2', '3', '4', '5', '6'])
            ),
        ],
        [
            'title' => 'Villa F4 — Modèle F4C', 'slug' => 'modele-f4c', 'legacy' => 'modele-f4c',
            'reference' => 'F4C', 'category' => 'f4-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou', 'address' => 'Ouagadougou — Centre',
            'built_area' => 145, 'rooms' => 4, 'bedrooms' => 3, 'bathrooms' => 3, 'living_rooms' => 1, 'kitchens' => 1, 'terraces' => 2,
            'is_featured' => 1,
            'excerpt' => 'Villa F4 moyen standing de 145 m² bâtis : 3 chambres, 3 salles d’eau, 2 terrasses, salle à manger et cuisine.',
            'description' => '<p>Le <strong>modèle F4C</strong> est une villa F4 moyen standing de <strong>145 m² de surface bâtie</strong>.</p><ul><li>Chambres : 03</li><li>Salles d’eau : 03</li><li>Terrasses : 02</li><li>Salle à manger : 01</li><li>Cuisine : 01</li></ul><p>Un plan fonctionnel et lumineux, pensé pour le confort de toute la famille.</p>',
            'features' => $f4Features,
            'cover' => [$u . '2026/08/IMG-20250813-WA0087-1.jpg', $u . '2026/08/IMG-20250813-WA0087-1-525x328.jpg'],
            'images' => array_map(static fn($f) => $wp('2026/08/IMG-20250813-' . $f . '.jpg'), ['WA0096-1', 'WA0092-1', 'WA0093-1', 'WA0094-1', 'WA0095-1', 'WA0088-1', 'WA0089-1', 'WA0090-1', 'WA0091-1', 'WA0087-2']),
        ],
        [
            'title' => 'Villa F4 — Modèle F4B', 'slug' => 'modele-f4b', 'legacy' => 'modele-f4b',
            'reference' => 'F4B', 'category' => 'f4-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou', 'address' => 'Ouagadougou — Centre',
            'built_area' => 145, 'rooms' => 4, 'bedrooms' => 3, 'bathrooms' => 3, 'living_rooms' => 1, 'kitchens' => 1, 'terraces' => 2,
            'excerpt' => 'Villa F4 moyen standing de 145 m² bâtis : 3 chambres, 3 salles d’eau, 2 terrasses, salle à manger et cuisine.',
            'description' => '<p>Le <strong>modèle F4B</strong> est une villa F4 moyen standing de <strong>145 m² de surface bâtie</strong>.</p><ul><li>Chambres : 03</li><li>Salles d’eau : 03</li><li>Terrasses : 02</li><li>Salle à manger : 01</li><li>Cuisine : 01</li></ul>',
            'features' => $f4Features,
            'cover' => [$u . '2026/08/IMG-20250813-WA0079.jpg', $u . '2026/08/IMG-20250813-WA0079-525x328.jpg'],
            'images' => [],
        ],
        [
            'title' => 'Villa F4 — Modèle F4A', 'slug' => 'modele-f4a', 'legacy' => 'modele-f4a-2',
            'reference' => 'F4A', 'category' => 'f4-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou', 'address' => 'Ouagadougou',
            'built_area' => 145, 'rooms' => 4, 'bedrooms' => 3, 'bathrooms' => 3, 'living_rooms' => 1, 'kitchens' => 1, 'terraces' => 2,
            'excerpt' => 'Villa F4 moyen standing de 145 m² bâtis : 3 chambres, 3 salles d’eau, 2 terrasses, salle à manger et cuisine.',
            'description' => '<p>Le <strong>modèle F4A</strong> est une villa F4 moyen standing de <strong>145 m² de surface bâtie</strong>.</p><ul><li>Chambres : 03</li><li>Salles d’eau : 03</li><li>Terrasses : 02</li><li>Salle à manger : 01</li><li>Cuisine : 01</li></ul>',
            'features' => $f4Features,
            'cover' => [$u . '2026/08/IMG-20250813-WA0068-1.jpg', $u . '2026/08/IMG-20250813-WA0068-1-525x328.jpg'],
            'images' => [],
        ],
        [
            'title' => 'Villa F3 — Modèle F3A', 'slug' => 'modele-f3a', 'legacy' => 'modele-f3a',
            'reference' => 'F3A', 'category' => 'f3-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou', 'address' => 'Ouagadougou — Centre',
            'built_area' => 130, 'rooms' => 3, 'bedrooms' => 2, 'bathrooms' => 2, 'living_rooms' => 1, 'kitchens' => 1,
            'excerpt' => 'Villa F3 moyen standing de 130 m² bâtis : 2 chambres, 2 salles d’eau, salon et cuisine.',
            'description' => '<p>Le <strong>modèle F3A</strong> est une villa F3 moyen standing d’environ <strong>130 m² de surface bâtie</strong>, avec <strong>2 chambres</strong> et <strong>2 salles d’eau</strong>.</p><p>Une villa compacte et fonctionnelle, idéale pour un premier achat.</p>',
            'features' => $f3Features,
            'cover' => [$u . '2026/08/IMG-20250813-WA0052.jpg', $u . '2026/08/IMG-20250813-WA0052-525x328.jpg'],
            'images' => [],
        ],
        [
            'title' => 'Villa F3 — Modèle F3B', 'slug' => 'modele-f3b', 'legacy' => 'modele-f3b',
            'reference' => 'F3B', 'category' => 'f3-moyen-standing', 'site' => 'cite-de-l-integration',
            'transaction' => 'vente', 'status' => 'disponible', 'city' => 'Ouagadougou', 'address' => 'Ouagadougou — Centre',
            'built_area' => 130, 'rooms' => 3, 'bedrooms' => 2, 'bathrooms' => 2, 'living_rooms' => 1, 'kitchens' => 1,
            'excerpt' => 'Villa F3 moyen standing de 130 m² bâtis : 2 chambres, 2 salles d’eau, salon et cuisine.',
            'description' => '<p>Le <strong>modèle F3B</strong> est une villa F3 moyen standing d’environ <strong>130 m² de surface bâtie</strong>, avec <strong>2 chambres</strong> et <strong>2 salles d’eau</strong>.</p>',
            'features' => $f3Features,
            'cover' => [$u . '2026/08/IMG-20250813-WA0059-2.jpg', $u . '2026/08/IMG-20250813-WA0059-2-525x328.jpg'],
            'images' => [],
        ],
        [
            'title' => 'Villa F3 à Bassinko', 'slug' => 'villa-de-bassinko-type-f3', 'legacy' => 'villa-de-bassinko-type-f3',
            'reference' => 'GZ-BSK-F3', 'category' => 'f3-moyen-standing', 'site' => null,
            'transaction' => 'location', 'status' => 'disponible', 'city' => 'Bassinko', 'address' => 'Bassinko — Centre',
            'land_area' => 240, 'rooms' => 3, 'bedrooms' => 2, 'bathrooms' => 1, 'living_rooms' => 1, 'kitchens' => 1,
            'excerpt' => 'Immédiatement disponible à la location : une charmante villa F3 de 240 m² à Bassinko, confortable et fonctionnelle.',
            'description' => '<p><strong>Immédiatement disponible.</strong></p><p>Dans cette charmante villa de <strong>240 m²</strong>, de type F3, située à Bassinko, vous trouverez un espace de vie confortable et fonctionnel, idéal pour une famille.</p>',
            'features' => "Parcelle de 240 m²\n2 chambres\nSalle d’eau\nSalon\nCuisine\nImmédiatement disponible",
            'cover' => [$u . '2023/06/17265438_606003786271724_8644683356165898240_a.jpg', $u . '2023/06/17265438_606003786271724_8644683356165898240_a-525x328.jpg'],
            'images' => [],
        ],
    ],

    /* ---------------------------------------------------------- Services */
    'services' => [
        ['title' => 'Promotion immobilière', 'slug' => 'promotion-immobiliere', 'icon' => 'building-2', 'image' => 'assets/images/hero/hero-2.jpg', 'is_featured' => 1,
         'excerpt' => 'Nous concevons et réalisons des cités modernes et des villas F3, F4 et F5 duplex sur des sites viabilisés, pensés pour durer.',
         'features' => "Sites intelligents et viabilisés\nVillas F3, F4 et F5 duplex\nVoies larges et accès dégagés\nProgrammes à travers le Burkina Faso",
         'content' => '<p>Promoteur immobilier depuis plus de trente ans, GELPAZ IMMO conçoit et réalise des <strong>cités résidentielles modernes</strong> : de l’aménagement du site à la remise des clés.</p><p>Notre mission : créer des <strong>sites intelligents</strong>, offrir un logement décent au plus grand nombre et être présents dans les <strong>17 régions du Burkina Faso</strong>, en accompagnant l’État dans sa politique de décentralisation.</p><h3>Nos gammes de villas</h3><ul><li><strong>F3 moyen standing</strong> : 2 chambres, parcelles de 300 m² ;</li><li><strong>F4 moyen standing</strong> : 3 chambres, parcelles de 400 m² ;</li><li><strong>F5 duplex haut standing</strong> : 4 chambres sur plus de 555 m².</li></ul>'],
        ['title' => 'Vente de villas', 'slug' => 'vente-de-villas', 'icon' => 'key', 'image' => 'assets/images/hero/hero-1.jpg', 'is_featured' => 1,
         'excerpt' => 'Des villas haut et moyen standing à acquérir au comptant ou avec un financement bancaire, avec un accompagnement de A à Z.',
         'features' => "Villas haut et moyen standing\nPaiement au comptant\nFinancement bancaire\nAccompagnement jusqu’à la remise des clés",
         'content' => '<p>Que vous recherchiez une maison familiale spacieuse ou un premier logement, GELPAZ IMMO vous propose des <strong>villas haut et moyen standing</strong> adaptées à vos besoins et à votre budget.</p><h3>Comment acheter avec GELPAZ IMMO ?</h3><ol><li><strong>Choix de la propriété</strong> : parcourez nos logements et sélectionnez la villa qui vous correspond.</li><li><strong>Réservation</strong> : contactez notre service commercial par e-mail ou par téléphone/WhatsApp.</li><li><strong>Finalisation</strong> : nous fixons ensemble un rendez-vous pour la procédure de validation de l’acquisition.</li></ol><p>Deux modes de paiement sont possibles : <strong>au comptant</strong> ou avec un <strong>financement bancaire</strong>.</p>'],
        ['title' => 'Gestion locative', 'slug' => 'gestion-locative', 'icon' => 'clipboard-check', 'image' => 'assets/images/services/gestion.jpg', 'is_featured' => 1,
         'excerpt' => 'Confiez-nous la location de votre bien : recherche de locataires, suivi, encaissements et entretien en toute sérénité.',
         'features' => "Recherche et sélection des locataires\nÉtats des lieux et suivi\nGestion des loyers\nEntretien du bien",
         'content' => '<p>Propriétaires, investisseurs ou membres de la diaspora : GELPAZ IMMO assure la <strong>gestion locative</strong> de vos biens pour vous faire gagner du temps et sécuriser vos revenus.</p><p>Fort de notre expérience d’agence immobilière depuis 1987, nous prenons en charge la mise en location, le suivi des locataires et l’entretien de votre patrimoine.</p>'],
        ['title' => 'Projets personnalisés', 'slug' => 'projets-personnalises', 'icon' => 'pencil-ruler', 'image' => 'assets/images/services/plans.jpg', 'is_featured' => 1,
         'excerpt' => 'Apportez votre plan ou laissez-nous concevoir un plan personnalisé : nous réalisons la villa qui vous ressemble.',
         'features' => "Votre plan ou un plan sur mesure\nConseil technique et architectural\nSuivi de chantier\nRespect des délais et du budget",
         'content' => '<p>Vous avez un projet personnel ? GELPAZ IMMO vous accompagne : <strong>apportez votre plan</strong>, ou faites appel à nos équipes pour concevoir un <strong>plan personnalisé</strong>.</p><p>Nous mettons à votre service notre expertise du bâtiment pour transformer votre projet en réalité, dans le respect de vos attentes, de votre budget et des normes en vigueur.</p>'],
        ['title' => 'Bâtiment et travaux publics', 'slug' => 'batiment-travaux-publics', 'icon' => 'hard-hat', 'image' => 'assets/images/services/construction.jpg', 'is_featured' => 1,
         'excerpt' => 'Une expertise reconnue en BTP : constructions, aménagements et ouvrages d’art, comme le pont de Kuul-Roumdé à Saaba.',
         'features' => "Construction de bâtiments\nAménagement et viabilisation\nOuvrages d’art\nContrôle qualité",
         'content' => '<p>Notre expertise s’étend au <strong>Bâtiment et aux Travaux Publics</strong>. Nous réalisons des constructions de qualité et des infrastructures utiles aux populations.</p><p>En 2023, GELPAZ IMMO a par exemple réalisé un <strong>pont à Kuul-Roumdé, dans la commune rurale de Saaba</strong>, ouvert à la circulation le 5 mai 2023 : une connexion essentielle pour le développement de la zone.</p>'],
        ['title' => 'Gestion de patrimoine', 'slug' => 'gestion-de-patrimoine', 'icon' => 'landmark', 'image' => 'assets/images/about/about-3.jpg', 'is_featured' => 0,
         'excerpt' => 'Valorisation et gestion de patrimoines immobiliers pour les particuliers, les entreprises et les institutions.',
         'features' => "Audit de patrimoine\nStratégie de valorisation\nSuivi administratif\nReporting régulier",
         'content' => '<p>GELPAZ IMMO met son expérience au service de la <strong>gestion et de la valorisation de patrimoines immobiliers</strong>, notamment de biens haut de gamme et contemporains.</p><p>Nous vous aidons à préserver et à faire fructifier votre patrimoine, avec discrétion et professionnalisme.</p>'],
        ['title' => 'Expertise immobilière', 'slug' => 'expertise-immobiliere', 'icon' => 'scale', 'image' => 'assets/images/about/about-2.jpg', 'is_featured' => 0,
         'excerpt' => 'Évaluation de biens et conseil, dans la tradition du premier expert immobilier diplômé d’État du Burkina Faso.',
         'features' => "Évaluation de biens\nRapports d’expertise\nConseil à l’investissement\nAccompagnement des institutions",
         'content' => '<p>L’<strong>expertise immobilière</strong> est dans l’ADN de GELPAZ : notre fondateur, feu <strong>M. Z. Alain ZOUNGRANA</strong>, fut le premier expert immobilier diplômé d’État du Burkina Faso.</p><p>Nous évaluons vos biens et vous conseillons pour des décisions éclairées, avant un achat, une vente ou un investissement.</p>'],
        ['title' => 'Sécurisation et conseil', 'slug' => 'securisation-et-conseil', 'icon' => 'shield-check', 'image' => 'assets/images/services/conseil.jpg', 'is_featured' => 0,
         'excerpt' => 'Vérification et sécurisation des documents, étude et conseil avant toute acquisition ou construction.',
         'features' => "Vérification des documents\nSécurisation juridique\nÉtude avant acquisition\nConseil avant construction",
         'content' => '<p>Acheter un terrain ou un logement est un engagement important. GELPAZ IMMO vous accompagne avec la <strong>vérification et la sécurisation de vos documents</strong>, ainsi qu’une <strong>étude et des conseils</strong> avant toute acquisition ou construction.</p><p>La <strong>confiance</strong> est notre première valeur : nous plaçons la sécurité juridique au cœur de chaque transaction.</p>'],
    ],

    /* --------------------------------------------------------------- FAQ */
    'faqs' => [
        ['category' => 'Général', 'question' => 'Qui est GELPAZ IMMO ?', 'answer' => 'GELPAZ IMMO SA est une société de promotion et de vente immobilière créée en juin 2009, qui fait suite à l’agence immobilière GELPAZ SARL, fondée en 1987. Son fondateur et président d’honneur, feu M. Z. Alain ZOUNGRANA, fut le premier expert immobilier diplômé d’État du Burkina Faso.'],
        ['category' => 'Général', 'question' => 'Quels services proposez-vous ?', 'answer' => 'Nous proposons la vente de villas haut et moyen standing, la gestion locative, l’accompagnement sur mesure de vos projets, l’expertise immobilière, la vérification et la sécurisation des documents, ainsi que l’étude et le conseil avant acquisition ou construction.'],
        ['category' => 'Logements', 'question' => 'Quels types de logements proposez-vous ?', 'answer' => 'Trois gammes de villas : la F3 moyen standing (2 chambres, parcelles de 300 m²), la F4 moyen standing (3 chambres, parcelles de 400 m²) et le F5 duplex haut standing (4 chambres, bureau et séjour privé sur plus de 555 m², piscine en option).'],
        ['category' => 'Logements', 'question' => 'Où se situent vos sites ?', 'answer' => 'Notre site phare est la Cité de l’Intégration, dans l’extension sud de Ouaga 2000, avec ses extensions Nord et Sud. Nous sommes également présents à la Cité de l’Espoir, à Sabtoana, à Pô, à Garghin, à Saaba, à Ponsomtenga, à Kaya et sur d’autres sites. Retrouvez la liste complète sur la page « Nos sites ».'],
        ['category' => 'Souscription', 'question' => 'Comment souscrire à un logement ?', 'answer' => 'Trois étapes simples : 1) choisissez la villa qui vous intéresse dans la rubrique « Nos logements » ; 2) réservez-la en contactant notre service commercial (infos@gelpaz.com, +226 67 30 81 85 par appel ou WhatsApp) ou via le formulaire de souscription ; 3) nous vous recontactons pour fixer un rendez-vous de validation de l’acquisition.'],
        ['category' => 'Souscription', 'question' => 'Quels sont les modes de paiement ?', 'answer' => 'Vous pouvez payer au comptant ou recourir à un financement bancaire. Nos conseillers vous orientent et vous accompagnent dans la constitution de votre dossier.'],
        ['category' => 'Souscription', 'question' => 'Pourquoi les prix ne sont-ils pas affichés sur le site ?', 'answer' => 'Le prix d’une villa dépend du modèle, du site, des options choisies et du mode de paiement. Pour vous garantir une information exacte et à jour, nous vous transmettons une offre personnalisée, rapidement et sans engagement : il suffit de nous contacter.'],
        ['category' => 'Souscription', 'question' => 'Je vis à l’étranger : puis-je acheter un logement ?', 'answer' => 'Oui. Accompagner la diaspora fait partie de nos missions, notamment pour accéder à un logement dans sa province d’origine. Les échanges peuvent se faire à distance par e-mail, téléphone ou WhatsApp, et nous vous tenons informé à chaque étape.'],
        ['category' => 'Logements', 'question' => 'Puis-je visiter une villa avant de m’engager ?', 'answer' => 'Bien sûr. Demandez une visite depuis la fiche du logement qui vous intéresse ou appelez-nous : nous organisons la visite sur rendez-vous. Nous organisons aussi régulièrement des journées portes ouvertes.'],
        ['category' => 'Services', 'question' => 'Puis-je faire construire selon mon propre plan ?', 'answer' => 'Oui, c’est l’objet de notre accompagnement « projets personnels » : apportez votre plan, ou laissez nos équipes concevoir un plan personnalisé adapté à vos besoins et à votre budget.'],
        ['category' => 'Services', 'question' => 'Assurez-vous la gestion locative de mon bien ?', 'answer' => 'Oui. Nous prenons en charge la mise en location de votre bien, le suivi des locataires et l’entretien, pour sécuriser vos revenus locatifs en toute sérénité.'],
        ['category' => 'Services', 'question' => 'Comment sécurisez-vous les transactions ?', 'answer' => 'La confiance est notre première valeur : nous vérifions et sécurisons les documents liés à chaque transaction et vous conseillons avant toute acquisition ou construction, pour vous garantir la sécurité juridique.'],
        ['category' => 'Logements', 'question' => 'Vos cités sont-elles viabilisées ?', 'answer' => 'Nos programmes sont pensés comme des sites intelligents. À la Cité de l’Intégration, nos résidents témoignent d’une viabilisation tenue, de voies principales très larges et d’accès dégagés jusqu’aux résidences.'],
        ['category' => 'Général', 'question' => 'Quels sont vos horaires d’ouverture ?', 'answer' => 'Nos bureaux, situés à Dagnoën (rue 29.128, Ouagadougou), vous accueillent de 8h à 13h et de 14h à 17h. Vous pouvez aussi nous écrire à tout moment via le formulaire de contact ou sur WhatsApp.'],
        ['category' => 'Général', 'question' => 'Comment vous contacter ?', 'answer' => 'Par téléphone au +226 25 37 10 55, par appel ou WhatsApp au +226 67 30 81 85, par e-mail à infos@gelpaz.com, ou via les formulaires du site. Nous vous répondons dans les meilleurs délais.'],
    ],

    /* ------------------------------------------------------- Témoignages */
    'testimonials' => [
        ['name' => 'Issa KINI', 'role' => 'Résident à la Cité de l’Intégration depuis 2021', 'content' => 'Je suis très satisfait des services qui m’ont été proposés chez GELPAZ IMMO. Le site est très agréable à vivre et je n’ai eu aucune difficulté majeure.'],
        ['name' => 'Ousseni YAMEOGO', 'role' => 'Résident à la Cité de l’Intégration', 'content' => 'Dans mon voisinage, j’ai été le tout premier résident. Je me suis installé juste 3 mois après l’acquisition de ma maison. Acquérir une maison, on le sait tous, n’est pas facile au Burkina. Mais avec GELPAZ IMMO, toutes ces étapes ont été facilitées. Je suis satisfait en tout cas.'],
        ['name' => 'Margerite KAMBOU', 'role' => 'Résidente à la Cité de l’Intégration depuis décembre 2022', 'content' => 'Je suis satisfaite à 100 %. La promesse de viabilisation a été tenue, les voies principales sont très larges et les chemins d’accès directs à nos résidences dégagés. Je recommande GELPAZ IMMO !'],
    ],

    /* -------------------------------------------------------- Partenaires */
    'partners' => [
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2023/08/telechargement-2.png'],
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2023/08/WhatsApp-Image-2023-08-08-at-15.30.25-3.jpeg'],
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2023/08/Capture.png'],
        ['name' => 'SKY Concept', 'logo_remote' => $u . '2025/09/telecharge-3.jpg'],
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2025/09/Nouveau-projet-6-2.jpg'],
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2023/08/11.png'],
        ['name' => 'Partenaire GELPAZ IMMO', 'logo_remote' => $u . '2023/08/telecharge-2.png'],
    ],

    /* ------------------------------------------------ Diaporama d’accueil */
    'slides' => [
        ['pre_title' => 'GELPAZ IMMO, la différence !', 'title' => 'Bâtissons ensemble votre avenir', 'text' => 'Depuis 1987, nous concevons des cités modernes et des villas de qualité pour rendre accessible un logement décent au Burkina Faso.', 'image' => 'assets/images/hero/hero-1.jpg', 'button_text' => 'Découvrir nos logements', 'button_url' => '/logements', 'button2_text' => 'Souscrire maintenant', 'button2_url' => '/souscription-logement'],
        ['pre_title' => 'Souscription ouverte', 'title' => 'La Cité de l’Intégration vous ouvre ses portes', 'text' => 'Villas F3, F4 et F5 duplex dans l’extension sud de Ouaga 2000, au comptant ou avec financement bancaire.', 'image' => 'assets/images/hero/hero-2.jpg', 'button_text' => 'Souscrire à une villa', 'button_url' => '/souscription-logement', 'button2_text' => 'Voir nos sites', 'button2_url' => '/nos-sites'],
        ['pre_title' => 'Projets sur mesure', 'title' => 'Votre villa, selon vos plans', 'text' => 'Apportez votre plan ou laissez nos experts concevoir un projet personnalisé, de la parcelle jusqu’aux finitions.', 'image' => 'assets/images/hero/hero-3.jpg', 'button_text' => 'Parler à un conseiller', 'button_url' => '/contact', 'button2_text' => 'Nos activités', 'button2_url' => '/nos-activites'],
    ],
];
