<?php
// Logo de LocaOuvriers dessiné en PHP (image SVG).
//   logo.php                          -> icône seule (carré)
//   logo.php?full=1                   -> icône + nom de la marque
//   logo.php?full=1&texte=%23ffffff   -> nom en blanc (pour fond sombre)
header('Content-Type: image/svg+xml; charset=utf-8');

$complet = isset($_GET['full']);
$texte   = $_GET['texte'] ?? '#0b2545';
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $texte)) {
    $texte = '#0b2545';
}
$largeur = $complet ? 430 : 120;
?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 <?= $largeur ?> 120" width="<?= $largeur ?>" height="120">
  <defs>
    <linearGradient id="fond" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#1d4e89"/>
      <stop offset="1" stop-color="#0b2545"/>
    </linearGradient>
    <linearGradient id="casque" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#ffd166"/>
      <stop offset="1" stop-color="#ffb703"/>
    </linearGradient>
  </defs>

  <!-- Fond arrondi -->
  <rect width="120" height="120" rx="28" fill="url(#fond)"/>
  <!-- Casque de chantier -->
  <path d="M24 72 A36 36 0 0 1 96 72 Z" fill="url(#casque)"/>
  <rect x="54" y="33" width="12" height="39" rx="5" fill="#fb8500"/>
  <rect x="14" y="70" width="92" height="12" rx="6" fill="#fb8500"/>
  <!-- Coche "réservé" -->
  <circle cx="60" cy="99" r="12" fill="#ffffff"/>
  <path d="M54 99 l5 5 l9 -11" fill="none" stroke="#0b2545" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>

<?php if ($complet): ?>
  <!-- Nom de la marque -->
  <text x="138" y="68" font-family="Segoe UI, Arial, sans-serif" font-size="38" font-weight="800" textLength="276" lengthAdjust="spacingAndGlyphs" fill="<?= $texte ?>">Loca<tspan fill="#fb8500">Ouvriers</tspan></text>
  <text x="140" y="94" font-family="Segoe UI, Arial, sans-serif" font-size="15" font-weight="600" textLength="276" lengthAdjust="spacing" fill="<?= $texte ?>" opacity="0.65">OUVRIERS À LA JOURNÉE</text>
<?php endif; ?>
</svg>
