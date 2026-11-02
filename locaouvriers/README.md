# LocaOuvriers – Location d'ouvriers à la journée (PHP simple)

## Installation (XAMPP / WAMP)
1. Copier le dossier `locaouvriers` dans `htdocs`.
2. Dans phpMyAdmin : Importer `database.sql`.
3. Ouvrir http://localhost/locaouvriers/
(Si le dossier porte un autre nom, modifier `BASE` dans `includes/config.php`.)

## Comptes de test (mot de passe : admin123)
- Administrateur : admin@site.com
- Client : client@site.com
- Ouvriers : ouvrier@site.com, karim@site.com, plombier@site.com, electricien@site.com

## Structure
- `index.php, login.php, register.php, logout.php` : pages publiques
- `client/` : réserver, voir et annuler ses réservations
- `ouvrier/` : planning, démarrer / terminer une mission
- `admin/` : tableau de bord, réservations (accepter, affecter, payer), ouvriers
- `includes/` : config, fonctions communes, en-tête, pied de page
- `assets/style.css` : toute la mise en forme
- `logo.php` : le logo (SVG généré en PHP) – `logo.php?full=1` pour la version avec le nom

## Règles du projet
- Prix = tarif journalier × nombre de jours × nombre d'ouvriers (paiement hors site).
- Un ouvrier ne peut faire qu'une mission par jour.
- Parcours : client réserve → admin accepte → admin affecte un ouvrier libre → l'ouvrier démarre et termine → la réservation passe "terminée".
