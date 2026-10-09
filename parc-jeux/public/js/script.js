// JavaScript simple (sans bibliothèque)

// 1. Menu burger (navigation publique ou sidebar admin)
var burger = document.getElementById('burger');
var burgerPublic = document.getElementById('burger-public');

if (burger) {
    burger.addEventListener('click', function () {
        var sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.toggle('ouvert');
    });
}
if (burgerPublic) {
    burgerPublic.addEventListener('click', function () {
        var menu = document.getElementById('menu');
        var navCompte = document.getElementById('nav-compte');
        if (menu) menu.classList.toggle('ouvert');
        if (navCompte) navCompte.classList.toggle('ouvert');
    });
}

// 2. Confirmation avant une action (formulaires avec data-confirm)
var formsConfirm = document.querySelectorAll('form[data-confirm]');
formsConfirm.forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!confirm(form.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
});

// 3. Validation côté client : champs obligatoires et nombres
var formsValidation = document.querySelectorAll('form.validation');
formsValidation.forEach(function (form) {
    form.addEventListener('submit', function (event) {
        var valide = true;
        form.querySelectorAll('input, select, textarea').forEach(function (champ) {
            champ.classList.remove('invalide');
            var vide = champ.hasAttribute('required') && champ.value.trim() === '' && champ.offsetParent !== null;
            var tropPetit = champ.type === 'number' && champ.value !== '' && champ.min !== '' && parseFloat(champ.value) < parseFloat(champ.min);
            if (vide || tropPetit) {
                champ.classList.add('invalide');
                valide = false;
            }
        });
        if (!valide) {
            event.preventDefault();
            alert('Veuillez corriger les champs en rouge.');
        }
    });
});

// 4. Formatage d'un montant
function formaterMontant(valeur) {
    return valeur.toFixed(2).replace('.', ',') + ' DH';
}

// 5. Vente de billets : calcul du total en direct
var quantites = document.querySelectorAll('.quantite');
if (quantites.length > 0) {
    var calculerTotalAchat = function () {
        var total = 0;
        quantites.forEach(function (champ) {
            var sousTotal = (parseInt(champ.value) || 0) * parseFloat(champ.getAttribute('data-prix'));
            champ.closest('tr').querySelector('.sous-total').textContent = formaterMontant(sousTotal);
            total += sousTotal;
        });
        document.getElementById('total-achat').textContent = formaterMontant(total);
    };
    quantites.forEach(function (champ) { champ.addEventListener('input', calculerTotalAchat); });
    calculerTotalAchat();
}

// 6. Réservation : montant estimé = somme des tarifs des jeux x nombre de personnes
var jeuxChoisis = document.querySelectorAll('.jeu-choisi');
if (jeuxChoisis.length > 0) {
    var calculerTotalResa = function () {
        var nb = parseInt(document.getElementById('nb-personnes').value) || 0;
        var total = 0;
        jeuxChoisis.forEach(function (c) {
            if (c.checked) { total += parseFloat(c.getAttribute('data-prix')) * nb; }
        });
        document.getElementById('total-resa').textContent = formaterMontant(total);
    };
    jeuxChoisis.forEach(function (c) { c.addEventListener('change', calculerTotalResa); });
    document.getElementById('nb-personnes').addEventListener('input', calculerTotalResa);
    calculerTotalResa();
}

// 7. Dépense : afficher le choix du jeu seulement pour une dépense liée à un jeu
var typeDepense = document.getElementById('type-depense');
if (typeDepense) {
    var majBlocJeu = function () {
        document.getElementById('bloc-jeu').style.display = (typeDepense.value === 'jeu') ? 'block' : 'none';
    };
    typeDepense.addEventListener('change', majBlocJeu);
    majBlocJeu();
}

// 8. Compte utilisateur : afficher la fiche liée selon le rôle
var roleChoisi = document.getElementById('role');
if (roleChoisi) {
    var majBlocsRole = function () {
        document.getElementById('bloc-responsable').style.display = (roleChoisi.value === 'responsable') ? 'block' : 'none';
        document.getElementById('bloc-client').style.display = (roleChoisi.value === 'client') ? 'block' : 'none';
    };
    roleChoisi.addEventListener('change', majBlocsRole);
    majBlocsRole();
}

// 9. Graphique en barres (recettes / dépenses) dessiné avec des div, sans bibliothèque
var graphique = document.getElementById('graphique');
if (graphique) {
    var labels = JSON.parse(graphique.getAttribute('data-labels'));
    var recettes = JSON.parse(graphique.getAttribute('data-recettes'));
    var depenses = JSON.parse(graphique.getAttribute('data-depenses'));
    var maximum = Math.max.apply(null, recettes.concat(depenses).concat([1]));

    labels.forEach(function (mois, i) {
        var groupe = document.createElement('div');
        groupe.className = 'groupe-barres';
        var barres = document.createElement('div');
        barres.className = 'barres';
        [[recettes[i], 'vert'], [depenses[i], 'rouge']].forEach(function (donnee) {
            var barre = document.createElement('div');
            barre.className = 'barre ' + donnee[1];
            barre.style.height = (donnee[0] / maximum * 100) + '%';
            barre.title = formaterMontant(donnee[0]);
            barres.appendChild(barre);
        });
        var texte = document.createElement('div');
        texte.className = 'mois';
        texte.textContent = mois;
        groupe.appendChild(barres);
        groupe.appendChild(texte);
        graphique.appendChild(groupe);
    });
}
