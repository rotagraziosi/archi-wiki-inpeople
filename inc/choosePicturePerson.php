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
                "", "evenementListe", array("selection"=>"personne", "id"=>$_GET["id"])
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
echo "<p>"._("Cliquez sur une image pour la sélectionner.")."</p>";
//Penser à prendre les images de tous les événements de l'adresse
$images = archiPersonne::getImages($_GET["id"]);
echo "<form method='POST' action='".
$config->creerUrl("", "choosePicturePerson", array("id"=>$_GET["id"]))."'>";
if (is_array($images)) {
    foreach ($images as $image) {
        echo "<button type='submit' name='image' value='".$image->idImage."'>
        <img src='".$config->getUrlImage("moyen").
        $image->dateUpload."/".$image->idHistoriqueImage.".jpg' alt='' />
        </button>";
    }
} else {
    header(
        "Location: ".$config->creerUrl(
            "", "evenementListe", array("selection"=>"personne", "id"=>$_GET["id"])
        )
    );
}
echo "</form>";
?>
