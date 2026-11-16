# 👷 LocaOuvriers – Application 100% React (SPA)

Application moderne de location d'ouvriers et artisans qualifiés à la journée au Maroc, refaite **exclusivement en React** (Vite + React Router + Tailwind CSS).

---

## 🚀 Démarrage Rapide

### 1. Prérequis
- [Node.js](https://nodejs.org/) (v18 ou supérieur)
- npm ou yarn

### 2. Lancement du serveur de développement React
Dans le dossier du projet (`c:\xampp\htdocs\locaouvriers`) :

```bash
# Lancer le serveur Vite React avec rechargement à chaud (HMR)
npm run dev
```

L'application s'ouvre directement sur : **http://localhost:3000** (ou le port indiqué par Vite).

### 3. Compilation pour la production
```bash
npm run build
```
Les fichiers statiques optimisés sont générés dans le dossier `dist/`.
Si vous utilisez **XAMPP Apache**, le projet peut aussi être consulté directement sur `http://localhost/locaouvriers/` grâce au routeur de secours dans `index.php`.

---

## 🔑 Mode Démo & Comptes de Test (1-Clic)

Un sélecteur **« Mode Démo »** est disponible directement dans la barre de navigation (Navbar) ainsi que sur la page de connexion pour basculer instantanément :

| Rôle | Email | Mot de passe | Description |
|---|---|---|---|
| 👮 **Administrateur** | `admin@site.com` | `admin123` | Dashboard KPIs, validation & affectation des ouvriers, gestion des effectifs |
| 👤 **Client** | `client@site.com` | `admin123` | Réservation d'ouvriers, estimation de devis, paiement et suivi des chantiers |
| 👷 **Ouvrier** | `ouvrier@site.com` | `admin123` | Planning des missions quotidiennes, pointage, statut en direct |

---

## 🛠️ Fonctionnalités Implémentées

### 1. Vitrine & Landing Page (`/`)
- **Hero interactif** avec indicateurs de satisfaction, garantie de remplacement et temps de réponse.
- **Simulateur de Devis en direct** : calcul immédiat (`Tarif journalier × Nombre de jours × Nombre d'ouvriers`) avec bouton de réservation pré-remplie.
- **Catalogue des métiers** avec moteur de recherche en temps réel et filtres par compétence.
- **Artisans vérifiés du mois** avec avis, notes et tags.
- **Galerie de réalisations** (Casablanca, Rabat, Marrakech, Tanger...).
- **FAQ interactive** sous forme d'accordéons.

### 2. Authentification & Profils
- Formulaire de connexion avec mémorisation de session.
- Inscription séparée pour **Clients** et **Artisans** (avec sélection du métier, spécialités et salaire souhaité).
- Validation complète en direct (téléphone à 10 chiffres, email RFC, mot de passe fort).
- Page d'édition de profil et mise à jour du mot de passe.

### 3. Espace Client (`/client/*`)
- **Réservation détaillée** : choix du métier, effectif (1 à 20), calendrier de dates avec calcul dynamique, adresse d'intervention et consignes.
- **Mes Réservations** : liste complète, statuts (*En attente, Acceptée, Affectée, En cours, Terminée, Annulée*), suivi des artisans affectés (avec nom et téléphone), annulation et simulateur de paiement en ligne.

### 4. Espace Ouvrier (`/ouvrier/planning`)
- **Mon Planning** : affichage des missions du jour et futures.
- Coordonnées directes du client (téléphone, adresse du chantier).
- Boutons d'avancement de mission (*Démarrer la mission* ➔ *Marquer comme terminée*).
- Modal de signalement d'absence ou d'imprévu.

### 5. Espace Administration (`/admin/*`)
- **Tableau de bord KPIs** : chiffre d'affaires total, demandes urgentes à traiter, effectifs sur le terrain, ouvriers disponibles.
- **Gestion des réservations** : filtre par statut, recherche, validation/refus, affectation d'ouvriers qualifiés via menu déroulant dédié.
- **Gestion des ouvriers** : liste complète, ajout de nouvel artisan avec validation, activation/désactivation de compte.

### 6. Persistance des données
- Les données sont initialisées à partir du jeu d'essai standard et **sauvegardées automatiquement dans le `localStorage`** du navigateur.
- Vos créations de réservations, modifications de statuts et ajouts d'ouvriers sont conservés même après rechargement de la page.
- Bouton de réinitialisation rapide aux données d'origine inclus dans le menu démo.

---

## 📁 Architecture du Code React

```text
locaouvriers/
├── index.html                   # Entrée HTML principale
├── vite.config.js               # Configuration Vite + React + Alias @
├── tailwind.config.js           # Configuration Tailwind CSS
├── package.json                 # Dépendances React, React Router, Lucide
├── public/                      # Images et médias statiques
│   └── images/                  # Photos des métiers, réalisations et artisans
└── src/
    ├── main.jsx                 # Montage ReactDOM
    ├── App.jsx                  # Configuration des routes SPA et Layout
    ├── index.css                # Styles Tailwind et variables graphiques
    ├── context/
    │   └── AppContext.jsx       # State global & synchronisation localStorage
    ├── data/
    │   └── mockData.js          # Données de démonstration initiales
    ├── utils/
    │   └── helpers.js           # Utilitaires (prix, badges, validation)
    ├── components/
    │   ├── Navbar.jsx           # Barre de navigation responsive + sélecteur démo
    │   ├── Footer.jsx           # Pied de page
    │   ├── FlashMessage.jsx     # Notifications toast
    │   └── ProtectedRoute.jsx   # Protection des accès par rôle
    └── pages/
        ├── Welcome.jsx          # Page d'accueil publique
        ├── auth/
        │   ├── Login.jsx        # Connexion & accès rapide
        │   └── Register.jsx     # Inscription client / artisan
        ├── client/
        │   ├── Reserver.jsx     # Formulaire de réservation
        │   └── Reservations.jsx # Suivi des réservations client
        ├── ouvrier/
        │   └── Planning.jsx     # Planning des missions ouvrier
        ├── admin/
        │   ├── Dashboard.jsx    # Tableau de bord d'administration
        │   ├── Reservations.jsx # Gestion et affectation des demandes
        │   └── Workers.jsx      # Recrutement et gestion des ouvriers
        └── profile/
            └── Edit.jsx         # Gestion du compte utilisateur
```
