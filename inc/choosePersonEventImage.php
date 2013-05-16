<?php
require_once __DIR__ .'/../modules/archi/includes/archiPersonne.class.php';
require_once __DIR__ .'/../modules/archi/includes/archiEvenement.class.php';
require_once __DIR__ .'/../modules/archi/includes/archiImage.class.php';
$person = new ArchiPersonne($_GET['idPerson']);

$event=new ArchiEvenement($_GET['idEvent']);

echo "<h2>Sélection de l'image principale pour ".$person->prenom.' '.$person->nom." sur l'événement ".$event->getTitle().'</h2>';


$dummyImage = new ArchiImage($row['idImage']);
$reqImages = $dummyImage->getImagesFromEvenement(array('select'=>'hi1.idImage', 'idEvenement'=>$_GET['idEvent']));

$res = $dummyImage->connexionBdd->requete($reqImages);
echo '<ul>';
while($row = mysql_fetch_assoc($res)) {
    $image = new ArchiImage($row['idImage']);
    echo '<li><img src="getPhotoSquare.php?id='.$image->getIdHistoriqueImage().'" /></li>';
}
echo '</ul>';
