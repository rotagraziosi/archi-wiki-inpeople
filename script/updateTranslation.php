<?php
/**
 * Ajoute les nouvelles chaines au fichier PO
 * 
 * PHP Version 5.3.3
 * 
 * @category Script
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
print_r(exec("cd ".dirname(__DIR__)."/locale/; sh ./add.sh 2>&1"));
header("Location: ".$_SERVER["HTTP_REFERER"]);
?>
