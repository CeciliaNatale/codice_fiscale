<?php

  error_reporting(E_ALL &~ E_NOTICE);
  require_once 'codicefiscale.class.php';

  $cognome = "";
  $nome    = "";
  $data    = "";
  $sesso   = "";
  $comune  = "";

  echo "================================\n";
  echo "dati inseriti\n";
  echo "================================\n";

  echo $cognome . "\n";
  echo $nome . "\n";
  echo $data . "\n";
  echo $sesso . "\n";
  echo $comune . "\n";

  echo "================================\n";

  $cf = new codicefiscale();
  $cf->calcola($cognome, $nome, $data, $sesso, $comune);
?>
