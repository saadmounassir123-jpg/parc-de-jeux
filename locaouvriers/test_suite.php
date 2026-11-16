<?php
/**
 * Test Suite Automatisée - LocaOuvriers
 * Vérification des exigences fonctionnelles, de validation et de sécurité
 */
require_once __DIR__ . '/includes/fonctions.php';

echo "========================================================\n";
echo "      SUITE DE TESTS DE VALIDATION & SÉCURITÉ           \n";
echo "========================================================\n\n";

$succes = 0;
$echecs = 0;

function tester($nom_test, $condition, $details = '') {
    global $succes, $echecs;
    if ($condition) {
        echo "  [PASS] $nom_test\n";
        $succes++;
    } else {
        echo "  [FAIL] $nom_test - $details\n";
        $echecs++;
    }
}

// --------------------------------------------------------------------------
// 1. TESTS DE VALIDATION DES NOMS
// --------------------------------------------------------------------------
echo "1. Validation des Noms et Prénoms :\n";
tester("Nom valide standard", valider_nom("Mohammed Alami") === true);
tester("Nom avec tiret et apostrophe", valider_nom("Jean-Luc d'Artois") === true);
tester("Nom avec accents", valider_nom("Éléonore Céline") === true);
tester("Rejet des chiffres dans le nom", valider_nom("Mohammed123") !== true);
tester("Rejet nom trop court (1 lettre)", valider_nom("A") !== true);
tester("Rejet nom vide ou espaces", valider_nom("   ") !== true);
tester("Rejet caractères spéciaux", valider_nom("Ali@Enterprise#") !== true);

// --------------------------------------------------------------------------
// 2. TESTS DE VALIDATION DU TÉLÉPHONE (10 chiffres stricts)
// --------------------------------------------------------------------------
echo "\n2. Validation du Numéro de Téléphone (10 chiffres) :\n";
tester("Téléphone marocain 10 chiffres valide (06)", valider_telephone("0612345678") === true);
tester("Téléphone 10 chiffres valide (07)", valider_telephone("0799887766") === true);
tester("Rejet si moins de 10 chiffres (9 chiffres)", valider_telephone("061234567") !== true);
tester("Rejet si plus de 10 chiffres (11 chiffres)", valider_telephone("06123456789") !== true);
tester("Rejet si lettres présentes", valider_telephone("061234567a") !== true);
tester("Rejet si caractères spéciaux/espaces", valider_telephone("06 12 34 56") !== true);
tester("Rejet si vide", valider_telephone("") !== true);

// --------------------------------------------------------------------------
// 3. TESTS DE VALIDATION DE L'EMAIL
// --------------------------------------------------------------------------
echo "\n3. Validation de l'Adresse Email :\n";
tester("Email valide standard", valider_email("client@site.com") === true);
tester("Email valide complexe", valider_email("user.nom+test@domaine.co.ma") === true);
tester("Rejet email sans @", valider_email("clientsite.com") !== true);
tester("Rejet email sans domaine", valider_email("client@") !== true);
tester("Rejet email avec espaces", valider_email("client @site.com") !== true);
tester("Rejet email vide", valider_email("") !== true);

// --------------------------------------------------------------------------
// 4. TESTS DE VALIDATION DES DATES DE RÉSERVATION
// --------------------------------------------------------------------------
echo "\n4. Validation des Dates de Réservation :\n";
$auj = date('Y-m-d');
$demain = date('Y-m-d', strtotime('+1 day'));
$dans_5j = date('Y-m-d', strtotime('+5 days'));
$hier = date('Y-m-d', strtotime('-1 day'));
$dans_40j = date('Y-m-d', strtotime('+40 days'));

tester("Dates valides (demain -> +5 jours)", valider_dates_reservation($demain, $dans_5j) === true);
tester("Dates valides 1 seule journée (aujourd'hui)", valider_dates_reservation($auj, $auj) === true);
tester("Rejet date début dans le passé", valider_dates_reservation($hier, $demain) !== true);
tester("Rejet date fin antérieure à date début", valider_dates_reservation($dans_5j, $demain) !== true);
tester("Rejet durée supérieure à 31 jours", valider_dates_reservation($auj, $dans_40j) !== true);

// --------------------------------------------------------------------------
// 5. TESTS DE VALIDATION DES ENTIERS (Nombre d'ouvriers)
// --------------------------------------------------------------------------
echo "\n5. Validation du Nombre d'Ouvriers :\n";
tester("Entier valide (1 ouvrier)", valider_entier(1, 1, 20) === true);
tester("Entier valide (10 ouvriers)", valider_entier(10, 1, 20) === true);
tester("Rejet nombre < 1 (0)", valider_entier(0, 1, 20) !== true);
tester("Rejet nombre > 20 (25)", valider_entier(25, 1, 20) !== true);
tester("Rejet nombre négatif (-3)", valider_entier(-3, 1, 20) !== true);

// --------------------------------------------------------------------------
// 6. TESTS DE LA BASE DE DONNÉES & UTILISATEURS DÉMO
// --------------------------------------------------------------------------
echo "\n6. Intégrité Base de Données & Authentification :\n";
$users = $pdo->query("SELECT email, role, mot_de_passe FROM users")->fetchAll();
tester("Table users contient des comptes", count($users) >= 3);

$admin = $pdo->query("SELECT * FROM users WHERE email = 'admin@site.com'")->fetch();
tester("Compte Admin présent", $admin && $admin['role'] === 'admin');
tester("Mot de passe Admin vérifié par password_verify", $admin && password_verify('admin123', $admin['mot_de_passe']));

$client = $pdo->query("SELECT * FROM users WHERE email = 'client@site.com'")->fetch();
tester("Compte Client présent", $client && $client['role'] === 'client');
tester("Mot de passe Client vérifié par password_verify", $client && password_verify('admin123', $client['mot_de_passe']));

$ouvrier = $pdo->query("SELECT * FROM users WHERE email = 'ouvrier@site.com'")->fetch();
tester("Compte Ouvrier présent", $ouvrier && $ouvrier['role'] === 'ouvrier');
tester("Mot de passe Ouvrier vérifié par password_verify", $ouvrier && password_verify('admin123', $ouvrier['mot_de_passe']));

$trades = $pdo->query("SELECT COUNT(*) FROM trades")->fetchColumn();
tester("Corps de métier disponibles", (int)$trades >= 5);

$workers = $pdo->query("SELECT COUNT(*) FROM workers")->fetchColumn();
tester("Ouvriers actifs enregistrés", (int)$workers >= 4);

echo "\n========================================================\n";
echo "RÉSULTAT GLOBAL : $succes test(s) réussi(s), $echecs échec(s).\n";
echo "========================================================\n";
