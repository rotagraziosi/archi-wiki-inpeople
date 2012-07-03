<?php
// recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");


//include('/home/pia/archiv2/includes/framework/config.class.php');


include("../includes/framework/config.class.php");


$config = new config();


echo "<h1>Moulinette recherche de groupe d'adresse sur image vueSur et prisDepuis</h1>";


$reqImages = "
		SELECT idImage,idAdresse,vueSur,prisDepuis 
		FROM _adresseImage
		WHERE idEvenementGroupeAdresse='0'
		AND (prisDepuis='1' OR vueSur='1')
";

$resImages = $config->connexionBdd->requete($reqImages);

$nbTrouves = 0;
while($fetchImages = mysql_fetch_assoc($resImages))
{
	// on va verifier s'il y a plusieurs groupe d'adresses lié a l'adresse , si non , on lie 
	$reqGAAdresse = "
		SELECT count(ae.idEvenement) as nbGA
		FROM _adresseEvenement ae 
		WHERE ae.idAdresse = '".$fetchImages['idAdresse']."'
		
	";
	$resGAAdresse = $config->connexionBdd->requete($reqGAAdresse);
	$fetchGAAdresse = mysql_fetch_assoc($resGAAdresse);
	
	if($fetchGAAdresse['nbGA']==1)
	{
		// recherche du groupe d'adresse
		$reqGA = "
			SELECT ae.idEvenement as idEvenementGroupeAdresse
			FROM _adresseEvenement ae
			WHERE ae.idAdresse = '".$fetchImages['idAdresse']."'
		";
		$resGA = $config->connexionBdd->requete($reqGA);
		if(mysql_num_rows($resGA)==1)
		{
			$fetchGA = mysql_fetch_assoc($resGA);
			// mise a jour
			$reqMaj = "UPDATE _adresseImage SET idEvenementGroupeAdresse='".$fetchGA['idEvenementGroupeAdresse']."' WHERE idImage='".$fetchImages['idImage']."' AND idAdresse='".$fetchImages['idAdresse']."'";
			$resMaj = $config->connexionBdd->requete($reqMaj);
			echo $reqMaj."<br>";
			$nbTrouves++;
		}
		else
		{
			echo "probleme idImage=".$fetchImages['idImage']." idAdresse=".$fetchImage['idAdresse']."<br>";
		}
		
	}
	else
	{
		echo "idAdresse =>".$fetchGAAdresse['nbGA']." groupes adresses<br>";
		echo "..............probleme idImage=".$fetchImages['idImage']." idAdresse=".$fetchImages['idAdresse']."<br>";
	}
}
echo "nombre de resolutions : $nbTrouves<br>";

?>