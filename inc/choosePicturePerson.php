<?php
/**
 * Choisir l'image principale d'une personne
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
if (isset($_POST["image"])) {
    if (archiPersonne::setImage($_GET["id"], $_POST["image"])) {
        header(
            "Location: ".$config->creerUrl(
                "", "evenementListe", array("selection"=>"personne", "id"=>869)
            )
        );
    }
}
$person= new archiPersonne();
$infos=$person->getInfosPersonne($_GET["id"]);
echo "<h2 class='h1'><a href='".$config->creerUrl(
    '', '', array(
        'archiAffichage'=>'evenementListe',
        'selection'=>"personne", 'id'=>$_GET["id"]
    )
)."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>";

$images = archiPersonne::getImages($_GET["id"]);
echo "<form method='POST' action='".
$config->creerUrl("", "choosePicturePerson", array("id"=>869))."'>";
foreach ($images as $image) {
    echo "<button type='submit' name='image' value='".$image->idImage."'>
    <img src='".$config->getUrlImage("moyen").
    $image->dateUpload."/".$image->idHistoriqueImage.".jpg' alt='' />
    </button>";
}
echo "</form>";
?>
