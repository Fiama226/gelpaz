# GELPAZ IMMO — Site web officiel (v2)

Nouveau site de **GELPAZ IMMO SA**, promoteur immobilier au Burkina Faso depuis 1987.
Site en PHP natif (sans framework), avec back-office complet, base MySQL (ou SQLite) et design premium
aux couleurs du logo GELPAZ : bleu GELPAZ `#1C80F0`, bleu nuit `#0A1B33` et accents dorés `#C9A24A`.

---

## Sommaire
1. [Fonctionnalités](#fonctionnalités)
2. [Prérequis](#prérequis)
3. [Installation](#installation)
4. [Configuration (e-mails, HTTPS…)](#configuration)
5. [Le back-office](#le-back-office)
6. [Images de l’ancien site](#images-de-lancien-site)
7. [Langues FR / EN / IT](#langues-fr--en--it)
8. [Structure du projet](#structure-du-projet)
9. [Sécurité](#sécurité)
10. [Développement local](#développement-local)

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
- **Formulaires réels** (contact, demande de visite, souscription, newsletter, commentaires) : enregistrement en base **et** e-mail à `infos@gelpaz.com`, accusé de réception au visiteur, protection anti-spam (jeton CSRF, champ piège, délai minimal, limite de fréquence).
- **Bouton WhatsApp flottant** (+226 67 30 81 85) et liens WhatsApp pré-remplis sur chaque logement.
- **Animations** : diaporama, carrousels (Swiper), titres animés lettre par lettre, apparitions au défilement, compteurs, bandeaux défilants, panneaux de services extensibles, effets de survol.
- **SEO** : balises meta et Open Graph, données structurées (RealEstateAgent, Residence, NewsArticle, FAQ, fil d’Ariane), `sitemap.xml`, `robots.txt`, URL propres en français.
- **Redirections 301** des anciennes adresses WordPress (`/nous-connaitre`, `/estate_property/...`, `/blog-list-no-sidebar-2`, `/contact-us`, anciens articles…) : le référencement est conservé.
- **Responsive** (mobile, tablette, ordinateur), accessible (navigation clavier, contrastes, `prefers-reduced-motion`) et rapide (polices et bibliothèques hébergées localement, images différées).

### Back-office (`/admin`)
Tableau de bord, **logements** (photos multiples par glisser-déposer, caractéristiques, plans, vedette, publication), gammes, **sites**, **actualités** (éditeur riche, galerie photos), services, FAQ, témoignages, **partenaires** (logos), diaporama d’accueil, **messages reçus**, **demandes de souscription** (statut + notes de suivi), **modération des commentaires**, abonnés newsletter, **exports CSV**, **réglages** (téléphones, e-mails, adresse, horaires, réseaux sociaux, chiffres clés, SEO), utilisateurs (administrateur / éditeur), mon compte, outils (import des images, test d’envoi d’e-mail).

---

## Prérequis
- **PHP 8.1 ou plus** avec les extensions `pdo_mysql` (ou `pdo_sqlite`), `mbstring`, `gd`, `fileinfo`, `curl` (recommandée), `intl` (facultative).
- **MySQL 5.7+ / MariaDB 10.3+** (recommandé) — ou SQLite pour un petit hébergement.
- **Apache** avec `mod_rewrite` (fichier `.htaccess` fourni) **ou Nginx** (voir `nginx.conf.example`).

---

## Installation

1. **Copiez les fichiers** sur votre hébergement (à la racine du domaine, ou dans un sous-dossier).
2. **Créez une base de données MySQL** vide (ex. `gelpaz`) et un utilisateur avec tous les droits sur cette base.
3. Vérifiez que les dossiers `config/`, `storage/` et `uploads/` sont **accessibles en écriture** par PHP.
4. Ouvrez **`https://votre-domaine/install`** et suivez l’assistant :
   - choix de la base (MySQL ou SQLite) ;
   - création du compte administrateur ;
   - paramètres d’envoi des e-mails.

   L’assistant crée les tables, **importe tout le contenu de GELPAZ** (logements, actualités, sites, services, FAQ, témoignages, partenaires, diaporama), puis écrit `config/config.php`.
5. Connectez-vous sur **`/admin`**.

> **Sans assistant ?** Copiez `config/config.sample.php` en `config/config.php`, renseignez-le, puis lancez
> `php tools/install.php --mysql-host=localhost --mysql-db=gelpaz --mysql-user=... --mysql-pass=... --admin-email=... --admin-password=...`
> Le schéma SQL brut est aussi fourni dans `database/schema.mysql.sql` (import phpMyAdmin) ; dans ce cas, le contenu initial doit être importé avec la commande ci-dessus.

### Nginx
Utilisez `nginx.conf.example` comme modèle (contrôleur frontal, dossiers internes interdits, uploads non exécutables, cache).

### Sous-dossier (Apache)
Si le site est dans un sous-dossier (ex. `/gelpaz/`), décommentez `RewriteBase /gelpaz/` dans `.htaccess` et indiquez l’URL complète dans `config/config.php` (`'url' => 'https://exemple.com/gelpaz'`).

---

## Configuration
Tout se trouve dans **`config/config.php`** (jamais versionné) :

| Clé | Rôle |
|---|---|
| `app.url` | URL publique (vide = détection automatique) |
| `app.debug` | `false` en production |
| `app.force_https` | redirige http → https |
| `db.*` | connexion MySQL ou SQLite |
| `mail.driver` | `smtp` (recommandé), `mail` (fonction PHP) ou `log` (tests : `storage/logs/mail.log`) |
| `mail.host/port/encryption/username/password` | serveur SMTP (ex. la messagerie de `infos@gelpaz.com`) |
| `security.frame_options` | `SAMEORIGIN` (protection anti-clickjacking) |

L’adresse qui **reçoit** les formulaires se règle dans le back-office : *Réglages → E-mail de réception des formulaires* (par défaut `infos@gelpaz.com`).
Testez l’envoi depuis *Outils → Tester l’envoi des e-mails*.

---

## Le back-office
- **Adresse** : `/admin` (lien non affiché sur le site public).
- **Rôles** : *Administrateur* (accès complet) et *Éditeur* (contenus et demandes, sans réglages ni utilisateurs).
- **Sécurité** : mots de passe hachés (bcrypt/argon), blocage après 5 tentatives échouées pendant 15 minutes, déconnexion après 2 h d’inactivité, jetons CSRF sur toutes les actions.
- **Ordre d’affichage** : glissez-déposez les lignes (logements, sites, FAQ, partenaires…) avec la poignée.
- **Photos** : redimensionnées automatiquement (1 920 px + miniature 800 px), formats JPG / PNG / WebP / GIF, 10 Mo max.

> ⚠️ Si vous avez utilisé le compte de démonstration, **changez immédiatement son mot de passe** (*Mon compte*).

---

## Images de l’ancien site
Le contenu repris de gelpaz.com (photos des villas, actualités, partenaires) pointe d’abord vers les **images originales en haute définition** de l’ancien site, pour que le nouveau site soit complet dès l’installation.

Pour ne plus dépendre de l’ancien site, ouvrez **Back-office → Outils → Importer les images de l’ancien site** (ou lancez `php tools/import-media.php`) : toutes les images sont téléchargées, optimisées et enregistrées dans `uploads/`. À faire **avant** la fermeture de l’ancien site WordPress.

Visuels fournis dans `assets/images/` :
- `brand/` : **logo vectorisé** (SVG couleur, blanc, bleu nuit), favicons, image de partage réseaux sociaux. *Le logo est une recréation vectorielle fidèle du logo existant : remplacez les fichiers par vos originaux si vous les possédez.*
- `hero/`, `about/`, `services/` : visuels d’ambiance en haute définition **créés numériquement** (bannières et illustrations). Ils ne représentent pas des biens précis ; les fiches logements utilisent les vraies photos GELPAZ.

---

## Langues FR / EN / IT
Le site est rédigé en français. Les boutons **FR / EN / IT** (barre supérieure et menu mobile) activent la **traduction automatique Google**, chargée uniquement lorsque l’anglais ou l’italien est choisi. Les noms propres, téléphones et le logo sont exclus de la traduction.

---

## Structure du projet
```
index.php              Contrôleur frontal (routes + redirections des anciennes URL)
server.php             Routeur pour le serveur PHP intégré (développement)
.htaccess              Configuration Apache        nginx.conf.example   Configuration Nginx
app/                   Code PHP (Database, Router, View, Mailer, Media, Auth, Installer, Repo…)
  Controllers/         Pages publiques, formulaires, installation
  Admin/               Back-office (modules.php = configuration des écrans CRUD)
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
- Cookies de session `HttpOnly` + `SameSite=Lax` (+ `Secure` en HTTPS), en-têtes de sécurité.
- Formulaires : jeton CSRF, champ piège, délai minimal, limite de 5 envois / 10 min / IP.

---

## Développement local
```bash
php tools/install.php --sqlite --admin-email=admin@exemple.com --admin-password="MotDePasse!" --debug --mail-driver=log
php -S localhost:8000 server.php
```
Puis ouvrez http://localhost:8000 (site) et http://localhost:8000/admin (back-office).
Les e-mails sont écrits dans `storage/logs/mail.log`.

---
© GELPAZ IMMO SA — « La différence ! »
