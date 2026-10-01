# GELPAZ IMMO — Site web officiel (v2)

Nouveau site de **GELPAZ IMMO SA**, promoteur immobilier au Burkina Faso depuis 1987.
Site en PHP natif (sans framework), avec back-office complet, base MySQL (ou SQLite) et design premium
aux couleurs du logo GELPAZ : bleu GELPAZ `#1C80F0`, bleu nuit `#0A1B33` et accents dorés `#C9A24A`.

> **État (octobre 2026) : prêt à être mis en ligne.**
> Quelques informations restent à fournir par GELPAZ : voir [À compléter par GELPAZ](#à-compléter-par-gelpaz).

---

## Sommaire
1. [Fonctionnalités](#fonctionnalités)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Mise en ligne](#mise-en-ligne)
5. [Configuration](#configuration)
6. [Le back-office](#le-back-office)
7. [Images de l’ancien site](#images-de-lancien-site)
8. [Langues FR / EN / IT](#langues-fr--en--it)
9. [À compléter par GELPAZ](#à-compléter-par-gelpaz)
10. [Personnalisation](#personnalisation)
11. [Sauvegardes et mises à jour](#sauvegardes-et-mises-à-jour)
12. [Dépannage](#dépannage)
13. [Structure du projet](#structure-du-projet)
14. [Sécurité](#sécurité)
15. [Développement local](#développement-local)
16. [Crédits et licences](#crédits-et-licences)
17. [Historique des versions](#historique-des-versions)

---

## Fonctionnalités

### Pages publiques
| Page | URL |
|---|---|
| Accueil (diaporama, recherche, gammes, logements, services, sites, partenaires, témoignages, actualités) | `/` |
| Qui sommes-nous ? | `/a-propos` |
| Missions – Visions – Valeurs | `/missions-visions-valeurs` |
| Nos offres immobilières | `/nos-offres-immobilieres` |
| Nos logements (filtres, tri, grille/liste, pagination) | `/logements` |
| Fiche logement (galerie, caractéristiques, plans, carte, demande de visite) | `/logements/{slug}` |
| Nos sites | `/nos-sites` |
| Souscription logement (formulaire complet) | `/souscription-logement` |
| Nos activités (+ fiche de chaque service) | `/nos-activites` |
| FAQ | `/faq` |
| Actualités (+ article avec galerie et commentaires) | `/actualites` |
| Contact | `/contact` |
| Mentions légales / confidentialité | `/mentions-legales` |
| Page 404 personnalisée | — |

- **Prix jamais affichés** : « Prix sur demande » partout, avec boutons d’appel et WhatsApp.
- **Formulaires réels** :
  - *contact, demande de visite et souscription* : enregistrés dans le back-office **et** envoyés par e-mail à `infos@gelpaz.com`, avec accusé de réception au visiteur (désactivable dans les réglages) ;
  - *commentaires des actualités* : publiés seulement après validation dans le back-office (notification par e-mail) ;
  - *newsletter* : inscriptions enregistrées dans le back-office (export CSV) ;
  - anti-spam sur tous les formulaires : jeton CSRF, champ piège, délai minimal de 3 secondes, 5 envois maximum par 10 minutes et par adresse IP.
- **Bouton WhatsApp flottant** (+226 67 30 81 85) et liens WhatsApp pré-remplis sur chaque logement.
- **Animations** : diaporama, carrousels (Swiper), titres animés lettre par lettre, apparitions au défilement, compteurs, bandeaux défilants, panneaux de services extensibles, effets de survol.
- **SEO** : balises meta et Open Graph, données structurées (RealEstateAgent, Residence, NewsArticle, FAQPage, fil d’Ariane), `sitemap.xml`, `robots.txt`, URL propres en français.
- **Redirections 301** des anciennes adresses WordPress (`/nous-connaitre`, `/estate_property/...`, `/blog-list-no-sidebar-2`, `/contact-us`, anciens articles…) : le référencement est conservé.
- **Responsive** (mobile, tablette, ordinateur), accessible (navigation clavier, contrastes, `prefers-reduced-motion`) et rapide (polices et bibliothèques hébergées localement, images différées).

### Back-office (`/admin`)
Logements, actualités, sites, partenaires, FAQ, demandes reçues, réglages… tout se gère sans toucher au code. Détail dans [Le back-office](#le-back-office).

---

## Prérequis
- **PHP 8.1 ou plus** (développé et testé avec PHP 8.5), avec les extensions :
  - obligatoires : `pdo_mysql` (ou `pdo_sqlite`), `mbstring`, `gd`, `fileinfo` ;
  - recommandées : `openssl` (envoi SMTP sécurisé) et `curl` (import des images de l’ancien site) ;
  - facultative : `intl` (adresses des pages plus propres pour les titres accentués).
- **MySQL 5.7+ / MariaDB 10.3+** (recommandé) — ou SQLite pour un petit hébergement.
- **Apache** avec `mod_rewrite` (fichier `.htaccess` fourni) **ou Nginx** (voir `nginx.conf.example`).

---

## Installation

1. **Copiez les fichiers** sur votre hébergement (à la racine du domaine, ou dans un sous-dossier).
2. **Créez une base de données MySQL** vide (ex. `gelpaz`) et un utilisateur avec tous les droits sur cette base.
3. Vérifiez que les dossiers `config/`, `storage/` et `uploads/` sont **accessibles en écriture** par PHP.
4. Ouvrez **`https://votre-domaine/install`** et suivez l’assistant. Il vérifie d’abord les prérequis, puis demande :
   - le type de base (MySQL ou SQLite) et ses identifiants ;
   - le compte administrateur ;
   - les paramètres d’envoi des e-mails (SMTP).

   L’assistant crée les tables, **importe tout le contenu de GELPAZ** (logements, actualités, sites, services, FAQ, témoignages, partenaires, diaporama), puis écrit `config/config.php`. Une fois le site installé, `/install` redirige vers le back-office.
5. Connectez-vous sur **`/admin`**, puis suivez la liste [Mise en ligne](#mise-en-ligne).

### Installation en ligne de commande
Copiez `config/config.sample.php` en `config/config.php` si besoin, puis lancez par exemple :
```bash
php tools/install.php --mysql-host=localhost --mysql-db=gelpaz --mysql-user=gelpaz_user --mysql-pass='...' \
  --admin-email=vous@gelpaz.com --admin-password='MotDePasseSolide!' --url=https://gelpaz.com --mail-driver=smtp
```

| Option | Rôle |
|---|---|
| `--mysql-host`, `--mysql-port`, `--mysql-db`, `--mysql-user`, `--mysql-pass` | connexion MySQL |
| `--sqlite` | utiliser SQLite (`storage/database.sqlite`) au lieu de MySQL |
| `--admin-email`, `--admin-password`, `--admin-name` | compte administrateur (créé, ou mot de passe réinitialisé s’il existe déjà) |
| `--url` | URL publique du site |
| `--mail-driver` | `smtp`, `mail` ou `log` ; complétez ensuite la section `mail` de `config/config.php` (serveur, identifiant, mot de passe) |
| `--debug` | afficher les erreurs (développement uniquement) |
| `--no-seed` | créer les tables sans importer le contenu GELPAZ |
| `--force` | relancer l’installation sur un site déjà installé (voir la mise en garde ci-dessous) |

> ⚠️ `--force` **réécrit `config/config.php`** : nouvelle clé secrète, paramètres SMTP à ressaisir. Le contenu existant est conservé : les tables ne sont jamais effacées et le contenu de départ n’est ajouté que dans les tables vides.

Le schéma SQL brut est aussi fourni dans `database/schema.mysql.sql` (import phpMyAdmin) ; dans ce cas, le contenu de départ s’importe avec la commande ci-dessus.

### Nginx
Utilisez `nginx.conf.example` comme modèle (contrôleur frontal, dossiers internes interdits, uploads non exécutables, cache).

### Sous-dossier (Apache)
Si le site est dans un sous-dossier (ex. `/gelpaz/`), décommentez `RewriteBase /gelpaz/` dans `.htaccess` et indiquez l’URL complète dans `config/config.php` (`'url' => 'https://exemple.com/gelpaz'`).

---

## Mise en ligne
Liste de contrôle avant d’ouvrir le site au public :

- [ ] Certificat HTTPS actif ; dans `config/config.php` : `app.force_https => true`, puis `security.hsts => true` une fois tout vérifié en HTTPS.
- [ ] `app.debug => false`.
- [ ] Paramètres SMTP renseignés, puis **Outils → Tester l’envoi des e-mails** réussi. L’adresse qui reçoit les formulaires se vérifie dans **Réglages → Coordonnées**.
- [ ] **Outils → Importer les images de l’ancien site**, **avant** la fermeture de l’ancien WordPress.
- [ ] Mot de passe administrateur solide ; un compte par personne (**Utilisateurs**).
- [ ] **Réglages** relus : téléphones, numéro WhatsApp, adresse, horaires, réseaux sociaux, chiffres clés.
- [ ] Éléments de la section [À compléter par GELPAZ](#à-compléter-par-gelpaz) renseignés.
- [ ] Une ancienne adresse testée (ex. `/nous-connaitre` doit ouvrir `/a-propos`), puis `https://gelpaz.com/sitemap.xml` déclaré dans Google Search Console.
- [ ] [Sauvegardes](#sauvegardes-et-mises-à-jour) programmées.

---

## Configuration
Tout se trouve dans **`config/config.php`** (créé par l’installation, jamais versionné ; modèle : `config/config.sample.php`) :

| Clé | Rôle | Valeur conseillée en production |
|---|---|---|
| `app.url` | URL publique, sans `/` final (vide = détection automatique) | `https://gelpaz.com` |
| `app.debug` | affiche les erreurs PHP | `false` |
| `app.key` | clé secrète qui signe les formulaires (générée à l’installation) | à garder secrète, ne pas modifier |
| `app.force_https` | redirige http → https | `true` dès que le certificat est actif |
| `db.*` | connexion MySQL (`driver`, `host`, `port`, `database`, `username`, `password`) ou SQLite (`path`) | MySQL |
| `mail.driver` | `smtp` (recommandé), `mail` (fonction PHP) ou `log` (tests : écrit dans `storage/logs/mail.log`, aucun envoi réel) | `smtp` |
| `mail.host`, `port`, `encryption`, `username`, `password` | serveur SMTP (ex. la messagerie de `infos@gelpaz.com`) | port `587` + `tls`, ou `465` + `ssl` |
| `mail.from_email`, `mail.from_name` | expéditeur des e-mails envoyés par le site | `infos@gelpaz.com`, `GELPAZ IMMO` |
| `security.frame_options` | en-tête X-Frame-Options (anti-clickjacking) | `SAMEORIGIN` |
| `security.hsts` | en-tête HSTS (le navigateur impose ensuite le HTTPS) | `true`, une fois le HTTPS vérifié |
| `security.cookie_samesite` | cookie de session : `Lax`, `Strict` ou `None` | `Lax` (`None` seulement si le site doit fonctionner dans un iframe externe ; n’est appliqué qu’en HTTPS) |
| `security.cookie_secure` | drapeau `Secure` du cookie (`null` = automatique selon le HTTPS) | `null` |

Derrière Cloudflare ou un proxy, le HTTPS est aussi détecté grâce à l’en-tête `X-Forwarded-Proto`.

Les coordonnées affichées sur le site, l’adresse qui **reçoit** les formulaires (par défaut `infos@gelpaz.com`) et l’accusé de réception se règlent dans le back-office (**Réglages**).

---

## Le back-office
- **Adresse** : `/admin` (lien non affiché sur le site public).
- **Rôles** : *Administrateur* (accès complet) et *Éditeur* (contenus et demandes, sans réglages, utilisateurs ni outils).
- **Sécurité** : mots de passe hachés (bcrypt), blocage pendant 15 minutes après 5 tentatives échouées, déconnexion après 2 h d’inactivité, jetons CSRF sur toutes les actions.
- **Ordre d’affichage** : glissez-déposez les lignes (logements, sites, FAQ, partenaires…) avec la poignée.
- **Photos** : glisser-déposer, redimensionnement automatique (1 920 px + miniature 800 px), formats JPG / PNG / WebP / GIF, 10 Mo maximum par fichier.

| Rubrique | Ce que l’on y fait |
|---|---|
| Tableau de bord | derniers messages et souscriptions, logements les plus consultés, actions rapides |
| Logements | photos, plans, caractéristiques, gamme, site, statut, mise en avant, publication |
| Gammes de logements | catégories de logements (nom, icône, description, ordre) |
| Sites | cités et projets : ville, statut, description, photo, carte |
| Actualités | articles avec éditeur riche, image de couverture et galerie |
| Activités / services | les fiches de la page « Nos activités » |
| FAQ · Témoignages · Partenaires · Diaporama d’accueil | questions fréquentes, avis clients, logos des partenaires, grandes images de la page d’accueil |
| Messages | formulaires de contact et demandes de visite (export CSV) |
| Souscriptions | demandes de souscription : statut et notes de suivi (export CSV) |
| Commentaires | approuver, remettre en attente ou supprimer les commentaires |
| Newsletter | liste des inscrits (export CSV) |
| Réglages ¹ | identité, coordonnées, réseaux sociaux, page d’accueil (bandeau d’annonce, mots défilants, chiffres clés), référencement Google, accusé de réception |
| Utilisateurs ¹ | comptes administrateur et éditeur |
| Outils ¹ | import des images de l’ancien site, test d’envoi des e-mails, informations système |
| Mon compte | nom, e-mail et mot de passe |

¹ Réservé aux administrateurs.

> ⚠️ Si vous avez utilisé un compte de démonstration, **changez immédiatement son mot de passe** (*Mon compte*).

**Mot de passe oublié ?** Un autre administrateur peut le réinitialiser dans *Utilisateurs*. Sinon, générez une empreinte avec
`php -r "echo password_hash('NouveauMotDePasse!', PASSWORD_DEFAULT), PHP_EOL;"`
puis collez-la dans la colonne `password` de la table `users` (phpMyAdmin).

---

## Images de l’ancien site
Le contenu repris de gelpaz.com (photos des villas, actualités, partenaires) pointe d’abord vers les **images originales en haute définition** de l’ancien site, pour que le nouveau site soit complet dès l’installation.

Pour ne plus dépendre de l’ancien site, ouvrez **Back-office → Outils → Importer les images de l’ancien site** (ou lancez `php tools/import-media.php`) : les images sont téléchargées par petits lots, optimisées et enregistrées dans `uploads/`. L’import peut être interrompu puis relancé ; il reprend là où il s’était arrêté, et les images introuvables sont signalées. À faire **avant** la fermeture de l’ancien site WordPress.

Visuels fournis dans `assets/images/` :
- `brand/` : **logo vectorisé** (SVG couleur, blanc, bleu nuit), favicons, image de partage réseaux sociaux. *Le logo est une recréation vectorielle fidèle du logo existant : remplacez les fichiers par vos originaux si vous les possédez (voir [Personnalisation](#personnalisation)).*
- `hero/`, `about/`, `services/` : visuels d’ambiance en haute définition **créés numériquement** (bannières et illustrations). Ils ne représentent pas des biens précis ; les fiches logements utilisent les vraies photos GELPAZ.

---

## Langues FR / EN / IT
Le site est rédigé en français. Les boutons **FR / EN / IT** (barre supérieure et menu mobile) activent la **traduction automatique Google**, chargée uniquement lorsque l’anglais ou l’italien est choisi. Les noms propres, téléphones et le logo sont exclus de la traduction.

---

## À compléter par GELPAZ
Informations encore manquantes au moment de la livraison :

| Élément | Où le renseigner |
|---|---|
| **Paramètres SMTP** de la messagerie `infos@gelpaz.com` (serveur, port, identifiant, mot de passe) | assistant `/install`, ou section `mail` de `config/config.php` |
| **Noms des partenaires** : seul « SKY Concept » est connu, les 6 autres logos s’affichent sous le nom « Partenaire GELPAZ IMMO » | Back-office → Partenaires |
| **Sites** : ville manquante pour *Cité de l’Espoir* et *Ouédraogo Yaar* ; statut (en commercialisation, livré…) à préciser pour 11 sites ; description à rédiger pour *Ouédraogo Yaar, Ponsomtenga 957, Garghin, Saaba, Léo Wan* et *Kaya* | Back-office → Sites |
| **Hébergeur** du site (nom, adresse, téléphone) pour les mentions légales | `templates/pages/legal.php`, section « Hébergement » |
| **Logo officiel** (fichiers d’origine), si disponible | `assets/images/brand/` (voir [Personnalisation](#personnalisation)) |
| **Import des images** de l’ancien site, juste après la mise en ligne | Back-office → Outils |

---

## Personnalisation
- **Couleurs** : variables CSS en tête de `assets/css/style.css` (`--primary`, `--primary-dark`, `--navy`, `--gold`…). Le back-office a sa propre feuille : `assets/css/admin.css`.
- **Polices** : Plus Jakarta Sans (titres) et Inter (texte), fichiers `woff2` dans `assets/fonts/` (variables `--font-title` et `--font-body`).
- **Textes** : tout ce qui est géré dans le back-office (logements, actualités, sites, FAQ, coordonnées…) se modifie sans code. Les textes de présentation (accueil, À propos, Missions – Visions – Valeurs, Nos offres, Mentions légales) se trouvent dans `templates/pages/`, et le menu dans `templates/partials/header.php` et `footer.php`.
- **Icônes** : le helper `icon('nom')` utilise les icônes Lucide intégrées dans `app/icons.php` (171 icônes + logos des réseaux sociaux).
- **Logo** : remplacez les fichiers de `assets/images/brand/` **en gardant les mêmes noms** :

  | Fichier | Utilisation |
  |---|---|
  | `logo-gelpaz.svg` | en-tête sur fond blanc, menu mobile, encart agence des fiches logements, données structurées, assistant d’installation |
  | `logo-gelpaz-white.svg` | en-tête transparent sur les bannières, pied de page, back-office |
  | `logo-gelpaz-navy.svg` | variante bleu nuit (non utilisée par défaut) |
  | `favicon.svg`, `favicon-32.png`, `apple-touch-icon.png`, `icon-192.png`, `icon-512.png` | icônes d’onglet, de favori et d’application mobile |
  | `og-image.jpg` (1200 × 630 px) | aperçu affiché lors d’un partage sur Facebook, WhatsApp… |

Les fichiers CSS et JavaScript sont versionnés automatiquement (`?v=…`) : après une modification, les visiteurs reçoivent la nouvelle version sans vider leur cache.

---

## Sauvegardes et mises à jour

### Sauvegardes
Au moins une fois par semaine, et toujours avant une mise à jour, sauvegardez :
1. **la base de données** : export phpMyAdmin, ou `mysqldump -u UTILISATEUR -p gelpaz > gelpaz-AAAA-MM-JJ.sql` (avec SQLite : copiez `storage/database.sqlite`) ;
2. **le dossier `uploads/`** : photos envoyées depuis le back-office et images importées ;
3. **le fichier `config/config.php`** : il contient les mots de passe, conservez-le en lieu sûr.

Pour restaurer : réimportez le fichier SQL, puis recopiez `uploads/` et `config/config.php`.

### Mettre à jour le code
- **Avec Git** : `git pull origin main` sur le serveur.
- **Par FTP** : envoyez les fichiers modifiés **sans écraser** `config/config.php`, `storage/` ni `uploads/`.

---

## Dépannage

| Problème | Solution |
|---|---|
| L’accueil s’affiche mais les autres pages renvoient une erreur 404 | Apache : activez `mod_rewrite` et autorisez le `.htaccess` (`AllowOverride All`). Nginx : reprenez le bloc `location /` de `nginx.conf.example`. Site dans un sous-dossier : voir [Installation](#sous-dossier-apache). |
| Page blanche ou « erreur 500 » | Consultez `storage/logs/php-error.log`, ou passez temporairement `app.debug` à `true`. Vérifiez la version de PHP (8.1 minimum) et les extensions. |
| Les e-mails n’arrivent pas | **Outils → Tester l’envoi des e-mails** ; vérifiez les identifiants SMTP, le couple port/chiffrement (587 + `tls` ou 465 + `ssl`) et le dossier des indésirables. Avec `mail.driver = log`, rien n’est envoyé : les e-mails sont écrits dans `storage/logs/mail.log`. |
| Les photos de l’ancien site ne s’affichent pas | Lancez l’import (**Outils**) ; vérifiez que `uploads/` est accessible en écriture et que l’extension GD est active. |
| Impossible d’envoyer une photo | Fichier de plus de 10 Mo ou format non pris en charge ; vérifiez aussi `upload_max_filesize` et `post_max_size` dans la configuration PHP. |
| « Votre session a expiré » sur un formulaire | La page est restée ouverte trop longtemps : rechargez-la puis renvoyez le formulaire. |
| Message « trop de tentatives » à la connexion | Blocage de sécurité après 5 échecs : patientez 15 minutes. |
| Retour à l’écran de connexion juste après s’être connecté | Le cookie de session est refusé : vérifiez `security.cookie_secure` et `security.cookie_samesite`, et que `storage/` est accessible en écriture. Derrière un proxy, l’en-tête `X-Forwarded-Proto` doit être transmis. |
| `/install` renvoie vers le back-office | C’est normal une fois le site installé. Pour réinstaller : `php tools/install.php --force` (voir la mise en garde dans [Installation](#installation-en-ligne-de-commande)). |

---

## Structure du projet
```
index.php              Contrôleur frontal (routes + redirections des anciennes URL)
server.php             Routeur pour le serveur PHP intégré (développement)
.htaccess              Configuration Apache        nginx.conf.example   Configuration Nginx
app/                   Code PHP (Database, Router, View, Mailer, Media, Auth, Installer, Repo…)
  Controllers/         Pages publiques, formulaires, installation
  Admin/               Back-office (modules.php = configuration des écrans de gestion)
templates/             Gabarits : layouts/, partials/, pages/, admin/
assets/                css/, js/, fonts/ (Plus Jakarta Sans, Inter), vendor/ (Swiper 11), images/
database/              seeds/ (contenu GELPAZ), schema.mysql.sql
config/                config.sample.php (modèle) — config.php (généré, non versionné)
storage/               base SQLite éventuelle, journaux, sessions (non public)
uploads/               fichiers envoyés depuis le back-office (exécution PHP désactivée)
tools/                 install.php, import-media.php (ligne de commande)
```

---

## Sécurité
- Requêtes SQL préparées (PDO) partout ; échappement systématique des sorties.
- Contenus riches nettoyés (liste blanche de balises, suppression des scripts et liens `javascript:`).
- Téléversements validés (type MIME réel, image décodable, nom aléatoire) ; scripts interdits dans `uploads/`.
- Dossiers `app/`, `config/`, `database/`, `storage/`, `templates/`, `tools/` inaccessibles depuis le web.
- Cookie de session `HttpOnly`, `SameSite` configurable (`Lax` par défaut), `Secure` en HTTPS.
- En-têtes de sécurité : `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `X-Frame-Options`, HSTS (optionnel).
- Formulaires : jeton CSRF, champ piège, délai minimal, 5 envois maximum par 10 minutes et par IP.
- Fichiers sensibles exclus de Git : `config/config.php`, base SQLite, clé, journaux, sessions et fichiers envoyés.

---

## Développement local
```bash
php tools/install.php --sqlite --admin-email=admin@exemple.com --admin-password="MotDePasse!" --debug --mail-driver=log
php -S localhost:8000 server.php
```
Puis ouvrez http://localhost:8000 (site) et http://localhost:8000/admin (back-office).
`server.php` reproduit les règles du `.htaccess` (URL propres, dossiers internes bloqués). Les e-mails sont écrits dans `storage/logs/mail.log`.

---

## Crédits et licences
- [Swiper](https://swiperjs.com) 11.2.10 — licence MIT.
- Icônes [Lucide](https://lucide.dev) — licence ISC ; logos des réseaux sociaux [Simple Icons](https://simpleicons.org) — CC0 1.0.
- Polices [Plus Jakarta Sans](https://fonts.google.com/specimen/Plus+Jakarta+Sans) et [Inter](https://rsms.me/inter/) — SIL Open Font License 1.1 (fichiers issus de Fontsource).
- Services tiers chargés par le navigateur des visiteurs : Google Traduction (seulement quand EN ou IT est choisi) et cartes Google Maps.
- Visuels `hero/`, `about/` et `services/` : images générées numériquement pour GELPAZ IMMO.
- Logo, textes, photos des logements et des actualités : © GELPAZ IMMO SA.

---

## Historique des versions
| Version | Date | Contenu |
|---|---|---|
| v2 | octobre 2026 | Refonte complète : contenu réel de gelpaz.com, base de données, back-office, formulaires avec envoi d’e-mails, SEO, redirections des anciennes adresses ([PR #2](https://github.com/Fiama226/gelpaz/pull/2)) |
| v1 | octobre 2026 | Première maquette PHP avec données de démonstration ([PR #1](https://github.com/Fiama226/gelpaz/pull/1)) |

---
© GELPAZ IMMO SA — « La différence ! »
