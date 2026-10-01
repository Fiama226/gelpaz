# GELPAZ IMMO — site vitrine PHP

Refonte du site de GELPAZ IMMO, inspirée de la direction artistique Piller et adaptée à l'identité de la marque.

## Lancer le projet

Pré-requis : PHP 8+

```bash
php -S 0.0.0.0:8000
```

Puis ouvrir `http://localhost:8000`.

## Pages disponibles

- `/?page=home`
- `/?page=about`
- `/?page=property`
- `/?page=details`
- `/?page=services`
- `/?page=faq`
- `/?page=blog`
- `/?page=article`
- `/?page=contact`

## Structure

```text
index.php             # routage et vues PHP
assets/style.css      # design responsive
assets/images/        # dossier réservé aux médias locaux
```

Les données immobilières présentes dans `index.php` sont actuellement des données de démonstration. Elles peuvent être remplacées par une base MySQL, une API WordPress ou un back-office.

## Déploiement Apache

Le projet fonctionne sans framework. Il suffit de placer le dossier dans le répertoire public du serveur Apache/Nginx avec PHP activé.

## Personnalisation

Les couleurs principales sont définies au début de `assets/style.css` :

```css
--ink: #172c2b;
--yellow: #e3a928;
--cream: #f5f2eb;
```
