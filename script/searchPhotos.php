<?php

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/config.class.php');
include('../modules/archi/includes/archiImage.class.php');

class connex extends config
{
	public $connex;
	function __construct()
	{
		parent::__construct();	
		$this->connex = $this->connexionBdd;
	}
}

$connexionBdd = new connex();
//$cheminPhysiqueImagesOriginaux 		= '/home/pia/archiv2/images/originaux/';
$cheminPhysiqueImagesOriginaux 		= '/home/vhosts/fabien/archi-strasbourg-v2/images/originaux/';
echo "<b>Recherche des photos manquantes.</b><br>";

$req ="
		SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload, ei.idEvenement as idEvenement
		FROM historiqueImage hi2, historiqueImage hi1
		LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
		WHERE hi2.idImage = hi1.idImage
		GROUP BY hi1.idImage, hi1.idHistoriqueImage
		HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
		";
		
$res = $connexionBdd->connex->requete($req);
$nbImageManquante=0;
while($fetch = mysql_fetch_assoc($res))
{
	if(!file_exists($cheminPhysiqueImagesOriginaux.$fetch['dateUpload'].'/'.$fetch['idHistoriqueImage'].".jpg"))
	{
		echo $cheminPhysiqueImagesOriginaux.$fetch['dateUpload'].'/'.$fetch['idHistoriqueImage'].".jpg<br>";
		echo "Image manquant : idHistoriqueImage = ".$fetch['idHistoriqueImage']." idEvenement=".$fetch["idEvenement"]."<br>";
		$nbImageManquante++;
	}
}
echo "Nb images manquantes = ".$nbImageManquante."<br>";


?>