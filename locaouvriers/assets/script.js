/**
 * ============================================================================
 * LocaOuvriers - Script JavaScript Frontend
 * Code clair, simple et facile à comprendre
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {

  // --------------------------------------------------------------------------
  // 1. GESTION DU MENU MOBILE (BURGER)
  // --------------------------------------------------------------------------
  const boutonBurger = document.querySelector('.burger');
  const menuNav      = document.getElementById('menu');

  if (boutonBurger && menuNav) {
    boutonBurger.addEventListener('click', function () {
      // Alterne l'affichage du menu sur mobile
      menuNav.classList.toggle('ouvert');
    });
  }


  // --------------------------------------------------------------------------
  // 2. SIMULATEUR DE TARIF EN DIRECT
  // Calcul : Total = Tarif Journalier × Nombre de Jours × Nombre d'Ouvriers
  // --------------------------------------------------------------------------
  const selectMetier  = document.getElementById('sim-metier');
  const sliderJours   = document.getElementById('sim-jours');
  const sliderOuvriers= document.getElementById('sim-ouvriers');
  const labelJours    = document.getElementById('val-jours');
  const labelOuvriers = document.getElementById('val-ouvriers');
  const affichageTotal= document.getElementById('sim-total');
  const affichageTarif= document.getElementById('sim-tarif-unitaire');
  const affichageDesc = document.getElementById('sim-formule');
  const lienReserver  = document.getElementById('btn-reserver-sim');

  function calculerTarif() {
    if (!selectMetier || !sliderJours || !sliderOuvriers) return;

    // Récupérer les valeurs sélectionnées par le client
    const optionChoisie = selectMetier.options[selectMetier.selectedIndex];
    const tarifParJour  = parseFloat(optionChoisie.value) || 0;
    const metierId      = optionChoisie.dataset.id || '';
    const nbJours       = parseInt(sliderJours.value) || 1;
    const nbOuvriers    = parseInt(sliderOuvriers.value) || 1;

    // Mettre à jour les labels des curseurs
    if (labelJours) labelJours.textContent = nbJours;
    if (labelOuvriers) labelOuvriers.textContent = nbOuvriers;

    // Formule mathématique simple
    const montantTotal = tarifParJour * nbJours * nbOuvriers;

    // Affichage avec 2 décimales et monnaie en DH
    if (affichageTarif) {
      affichageTarif.textContent = tarifParJour.toFixed(2).replace('.', ',') + ' DH/jour';
    }
    if (affichageDesc) {
      affichageDesc.textContent = nbOuvriers + (nbOuvriers > 1 ? ' ouvriers × ' : ' ouvrier × ') + nbJours + (nbJours > 1 ? ' jours' : ' jour');
    }
    if (affichageTotal) {
      affichageTotal.textContent = montantTotal.toFixed(2).replace('.', ',') + ' DH';
    }

    // Mise à jour de l'URL du bouton pour préremplir le formulaire de réservation
    if (lienReserver && metierId) {
      lienReserver.href = (window.BASE_URL || '/locaouvriers') + '/client/reserver.php?metier=' + metierId + '&nb=' + nbOuvriers;
    }
  }

  // Écouter les changements sur chaque contrôle du simulateur
  if (selectMetier && sliderJours && sliderOuvriers) {
    selectMetier.addEventListener('change', calculerTarif);
    sliderJours.addEventListener('input', calculerTarif);
    sliderOuvriers.addEventListener('input', calculerTarif);

    // Premier calcul au chargement de la page
    calculerTarif();
  }


  // --------------------------------------------------------------------------
  // 3. RECHERCHE ET FILTRE EN DIRECT DES MÉTIERS
  // --------------------------------------------------------------------------
  const champRecherche = document.getElementById('recherche-metier');
  const cartesMetiers  = document.querySelectorAll('.carte-metier-photo');

  if (champRecherche && cartesMetiers.length > 0) {
    champRecherche.addEventListener('input', function () {
      const motCle = champRecherche.value.toLowerCase().trim();

      cartesMetiers.forEach(function (carte) {
        const nomMetier = carte.querySelector('.nom-metier')?.textContent.toLowerCase() || '';
        const descMetier= carte.querySelector('.desc-metier-photo')?.textContent.toLowerCase() || '';

        // Si le nom ou la description contient le mot recherché, on affiche la carte
        if (nomMetier.includes(motCle) || descMetier.includes(motCle)) {
          carte.style.display = 'flex';
        } else {
          carte.style.display = 'none';
        }
      });
    });
  }


  // --------------------------------------------------------------------------
  // 4. BOUTON FLOTTANT "REMONTER EN HAUT"
  // --------------------------------------------------------------------------
  const boutonRemonter = document.getElementById('btnRemonter');

  if (boutonRemonter) {
    // Afficher le bouton uniquement après 350px de défilement
    window.addEventListener('scroll', function () {
      if (window.scrollY > 350) {
        boutonRemonter.classList.add('visible');
      } else {
        boutonRemonter.classList.remove('visible');
      }
    });

    // Remonter en douceur au clic
    boutonRemonter.addEventListener('click', function () {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

});
