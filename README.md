# 🔥 Croisade Que Ton Règne Vienne — Site Web

> Jésus au centre de l'adoration

## Structure du projet

```
croisade/
├── index.html          ← Page principale (ouvrir dans navigateur)
├── css/
│   └── style.css       ← Tous les styles (design system complet)
├── js/
│   └── main.js         ← Animations GSAP + logique
├── php/
│   ├── config.php      ← Configuration base de données
│   └── register.php    ← Backend traitement formulaire
└── images/
    ├── logo.svg        ← Logo (remplacer par logo.png officiel)
    ├── hero.png        ← Image hero
    ├── vision.png      ← Section vision
    ├── cameroon.png    ← Paysage Cameroun
    ├── crowd.png       ← Foule en adoration
    ├── louange.png     ← Carte louange
    └── adoration.png   ← Carte adoration
```

---

## Déploiement avec PHP (formulaire)

### Option 1 : Hébergement web classique (cPanel, Infomaniak, OVH...)

1. **Uploader** tous les fichiers sur votre serveur via FTP
2. **Créer la base de données MySQL** via phpMyAdmin
3. **Configurer** `php/config.php` :
   ```php
   define('DB_HOST',     'localhost');
   define('DB_USER',     'votre_user');
   define('DB_PASSWORD', 'votre_mot_de_passe');
   define('DB_NAME',     'croisade_db');
   ```
4. La table `inscriptions` sera créée **automatiquement** au premier envoi

### Option 2 : XAMPP (test en local)

1. Installer XAMPP : https://www.apachefriends.org/fr/index.html
2. Copier le dossier `croisade/` dans `C:/xampp/htdocs/`
3. Démarrer Apache + MySQL dans XAMPP Control Panel
4. Ouvrir : `http://localhost/croisade/`
5. Créer la base de données `croisade_db` dans phpMyAdmin

---

## Structure de la table MySQL

```sql
CREATE TABLE `inscriptions` (
    `id`            INT           AUTO_INCREMENT PRIMARY KEY,
    `nom`           VARCHAR(100)  NOT NULL,
    `prenom`        VARCHAR(100)  NOT NULL,
    `email`         VARCHAR(191)  NOT NULL,
    `telephone`     VARCHAR(30)   DEFAULT NULL,
    `ville`         VARCHAR(100)  DEFAULT NULL,
    `eglise`        VARCHAR(200)  DEFAULT NULL,
    `participation` VARCHAR(80)   DEFAULT NULL,
    `message`       TEXT          DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

---

## Voir les inscriptions

Dans **phpMyAdmin**, exécutez :
```sql
SELECT * FROM inscriptions ORDER BY created_at DESC;
```

---

## Remplacer le logo

Remplacez `images/logo.svg` par votre **logo officiel PNG** :
- Renommez votre fichier logo en `logo.png`
- Dans `index.html`, remplacez `logo.svg` par `logo.png`

---

## Personnaliser le contenu

| Élément | Fichier | Section |
|---|---|---|
| Date/lieu de la croisade | `index.html` | Hero (`.hero-info-card`) |
| Texte vision | `index.html` | Section `#vision` |
| Chiffres compteurs | `index.html` | Section `#compteurs` |
| Email de contact | `index.html` | Footer |
| Réseaux sociaux | `index.html` | Footer `.footer-social` |
| Couleurs | `css/style.css` | Variables CSS `:root` |

---

## Technologies utilisées

- **HTML5** sémantique + SEO
- **CSS3** avec variables CSS, glassmorphism, animations
- **JavaScript** vanilla + GSAP 3 + ScrollTrigger
- **PHP 7.4+** avec PDO (sécurisé contre SQL injection)
- **MySQL** pour les inscriptions
