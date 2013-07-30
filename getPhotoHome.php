<?php
/**
 * Redimenssionne une photo en 640x340 px pour la page d'accueil de la version mobile
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
require_once "includes/framework/config.class.php";
$config = new Config();
$req = "
        SELECT dateUpload
        FROM  historiqueImage 
        WHERE idHistoriqueImage = ".mysql_real_escape_string($_GET["id"]);
$res =$config->connexionBdd->requete($req);
$image=mysql_fetch_object($res);
$path="images/grand/".$image->dateUpload."/".$_GET["id"].".jpg";
$infos=getimagesize($path);
$input = imagecreatefromjpeg($path);
header("Content-Type: image/jpeg");
$width=640;
$height=($infos[1]*640)/$infos[0];
$output = imagecreatetruecolor(640, 340);
//$output = imagecreatetruecolor($width, $height);
if ($infos[1] > $infos[0]) {
    $x=-170;
} else {
    $x=0;
}
imagecopyresampled(
    $output, $input, 0, $x, 0, 0, $width, $height, $infos[0], $infos[1]
);
imagejpeg($output);
?>
