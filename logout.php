<?php
require 'includes/fonctions.php';
session_unset();
session_destroy();
session_start();
message('Vous êtes déconnecté.');
aller_a('login.php');
