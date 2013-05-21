#! /usr/bin/php
<?php
/**
 * Affiche les fichiers de logs de manière plus lisible
 * 
 * PHP version 5.4.4
 * 
 * @category Script
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * */
$labels = array('Date', 'Emails', 'Sujet', 'Contenu');
if ($logfile = fopen($argv[1], 'r')) {
    while (($line = fgets($logfile, 4096)) !== false) {
        print_r(array_combine($labels, json_decode($line, true)));
    }
    fclose($logfile);
}
