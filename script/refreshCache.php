<?php
// recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
//include('/home/pia/archiv2/includes/framework/config.class.php');
include('/home/vhosts/fabien/archi-strasbourg-v2/includes/framework/config.class.php');

//include_once('/home/pia/archiv2/modules/archi/includes/archiAccueil.class.php');
//include_once('/home/pia/archiv2/modules/archi/includes/archiAdresse.class.php');
//include_once('/home/pia/archiv2/modules/archi/includes/archiImage.class.php');
include_once('/home/vhosts/fabien/archi-strasbourg-v2/modules/archi/includes/archiAccueil.class.php');
include_once('/home/vhosts/fabien/archi-strasbourg-v2/modules/archi/includes/archiAdresse.class.php');
include_once('/home/vhosts/fabien/archi-strasbourg-v2/modules/archi/includes/archiImage.class.php');

$config = new config();

$cache = new cacheObject();
$cache->refreshCachedPages();

?>
