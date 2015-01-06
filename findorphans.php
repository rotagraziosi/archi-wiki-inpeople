<?php
/** @file
* Script qui détecte les éléments orphelins
* (peut produire des faux positifs)
*
* PHP Version 5.3.3
*
* @category General
* @package ArchiWiki
* @author Pierre Rudloff <contact@rudloff.pro>
* @license GNU GPL v3 https://www.gnu.org/licenses/gpl.html
* @link https://archi-strasbourg.org/
*
* */
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8" />
<title>Archi-Strasbourg - Éléments orphelins</title>
</head>
<body>
<ul>
<?php
require_once "includes/framework/config.class.php";
require_once "modules/archi/includes/archiEvenement.class.php";
$config=new Config();
$req = "
SELECT idEvenement, titre
FROM historiqueEvenement
GROUP BY idEvenement;";
$res = $config->connexionBdd->requete($req);
$e = new archiEvenement();
while ($event=mysql_fetch_object($res)) {
if (!$e->getIdEvenementGroupeAdresseFromIdEvenement($event->idEvenement)) {
if (!empty($event->titre)) {
$titre = $event->titre;
} else {
$titre = '(sans titre)';
}
echo '<li><a href="index.php?archiAffichage=adresseDetail'.
'&archiIdEvenementGroupeAdresse='.
$event->idEvenement.'&modeAffichage=simple">'.$titre.'</a></li>';
}
}
?>
</ul>
</body>
</html>