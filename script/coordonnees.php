<?php
include("../includes/framework/config.class.php");
include("../modules/archi/includes/archiAdresse.class.php");
?>
<html>
<head>
<meta http-quiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php

$config = new config();

$typeTable = "ville";

switch($typeTable)
{
	case "arrets":
		// RI67
		$identifiant = "idArret";
		$champLibelleAdresse = "adresseArret";
		$tableAMettreAJour = "arretsTransportsEnCommun";
	break;
	case "offresDescriptions":
		// RI67
		$identifiant = "idDescription";
		$champLibelleAdresse = "adresseOffre2";
		$tableAMettreAJour = "offresBiensDescriptions";
	break;
	case "historiqueAdresse":
		// archiV2
		$identifiant = "idHistoriqueAdresse";
		$champLibelleAdresse = "intituleAdresse";
		$tableAMettreAJour = "historiqueAdresse";
	break;
	case "ville":
		$identifiant = "idVille";
		$champLibelleAdresse = "libelleAdresse";
		$tableAMettreAJour = "ville";
	break;
	default:
	break;
}



if(isset($_POST["idATraiter"]))
{
	$id = $_POST["idATraiter"];
	$paramReq = "WHERE $identifiant>=$id AND $identifiant<".($id+10);
}
else
{
	$req = "select min($identifiant) as premierId from $tableAMettreAJour";
	$res = $config->connexionBdd->requete($req);
	$fetch = mysql_fetch_assoc($res);
	$id = $fetch['premierId'];
	$paramReq = "WHERE $identifiant>=$id AND $identifiant<".($id+10);
}

if(isset($_GET['ajout']))
{
	foreach($_POST['latitude_arret'] as $idTable => $valueLatitude )
	{
		$reqInsert = "UPDATE $tableAMettreAJour SET latitude='".$valueLatitude."' ,longitude='".$_POST['longitude_arret'][$idTable]."' WHERE $identifiant='".$idTable."'";
		$resInsert = $config->connexionBdd->requete($reqInsert);	
	}
	$idSuivant = $idTable+1; // on recupere la valeur suivant a traiter
}
else
{
	$idSuivant = $id+10;
}






switch($typeTable)
{
	case "arrets":
		$req = "
			SELECT idArret, idAdresse, concat(IF(atc.numeroAdresse!='0',atc.numeroAdresse,''), ' ', a.complementAdresse, ' ', a.nomAdresse,' ',v.nomVille) as adresseArret
			FROM  arretsTransportsEnCommun atc
			LEFT JOIN adresse a ON a.idAdresse = atc.idAdresseArret
			LEFT JOIN ville v ON v.idVille = a.idVille
			$paramReq
			ORDER BY idArret
			LIMIT 10
		";
	break;
	case "offresDescriptions":
		 $req = "
			SELECT obd.idDescription, concat(obd.adresseOffre,' ',v.nomVille) as adresseOffre2
			FROM offresBiensDescriptions obd
			LEFT JOIN ville v ON v.idVille = obd.idVille
			$paramReq
			ORDER BY obd.idDescription
			LIMIT 10		 
		 ";
	break;
	case "historiqueAdresse":
		$req = "
			SELECT ha.idHistoriqueAdresse,ha.idAdresse
			FROM historiqueAdresse ha
			$paramReq
			ORDER BY ha.idHistoriqueAdresse
			LIMIT 10
		";
		
	break;
	case "ville":
		$req = "
			SELECT v.idVille as idVille,v.nom as nomVille,p.nom as nomPays, concat(v.nom,' ',p.nom) as libelleAdresse
			FROM ville v
			LEFT JOIN pays p ON p.idPays = v.idPays
			$paramReq
			AND v.nom!='autre'
			ORDER BY v.idVille
			LIMIT 10		
		";
	break;
	default:
	break;
}



echo $req;


echo "<h1>Recuperation des coordonnées de latitudes et longitudes</h1>";  
  
$paramsGoogleMap = array('googleMapKey'=>$config->googleMapKey);

$googleMap = new googleMap($paramsGoogleMap);

echo $googleMap->getJsFunctions();



// recuperation des adresses des arrets
$res = $config->connexionBdd->requete($req);

$str = new stringObject();
echo "
		<script language='javascript'>
		geocoder = new GClientGeocoder();
		</script>
";
echo "
<div id='debug' style='background-color:blue;color:white;'>
</div>
<form action='?ajout=1' name='formAdresses' id=formAdresses method='POST' enctype='multipart/form-data'>
<input type='text' value='$idSuivant' name='idATraiter'>
";


$a = new archiAdresse();

$i=0;

while($fetch = mysql_fetch_assoc($res))
{
	if($typeTable!='ville')
	{
		$adresse=$a->getIntituleAdresseFrom($fetch['idAdresse'],'idAdresse',array("noQuartier"=>true,"noSousQuartier"=>true));
		if(!$str->isUTF8($adresse))
			$adresse = utf8_encode($adresse);
	}
	else
	{
		$adresse = $fetch['libelleAdresse'];
	}
	
	
	echo "
	$adresse
	<input type='text' name='latitude_arret[".$fetch[$identifiant]."]' id='latitude_arret_".$fetch[$identifiant]."' value=''>
	<input type='text' name='longitude_arret[".$fetch[$identifiant]."]' id='longitude_arret_".$fetch[$identifiant]."' value=''><br>
	";
}



$res = $config->connexionBdd->requete($req);
while($fetch = mysql_fetch_assoc($res))
{

	if($typeTable!='ville')
	{
		$adresse=$a->getIntituleAdresseFrom($fetch['idAdresse'],'idAdresse',array("noQuartier"=>true,"noSousQuartier"=>true));
		
		if(!$str->isUTF8($adresse))
			$adresse = utf8_encode($adresse);
	}
	else
	{
		$adresse = $fetch['libelleAdresse'];
	}
	
	echo "
	<script language='javascript'>

		var point1_".$fetch[$identifiant].";

		function getPoint1_".$fetch[$identifiant]."(response)
		{
			if (response.Status.code != 200) 
			{
				document.getElementById('debug').innerHTML+=\"erreur adresse = $adresse <br>\";
		   	} 
			else 
			{
			        place = response.Placemark[0];
			        document.getElementById('latitude_arret_".$fetch[$identifiant]."').value = place.Point.coordinates[1];
					document.getElementById('longitude_arret_".$fetch[$identifiant]."').value = place.Point.coordinates[0];
					
			}
		}
		
		geocoder.getLocations(\"".$adresse."\", getPoint1_".$fetch[$identifiant].");
	</script>
	<br>
	";
	$lastId = $fetch[$identifiant];

	$i++;
	sleep(1);
}
echo "</form>";
	echo "
	<script language='javascript'>
		document.getElementById('debug').innerHTML+='OK';
	</script>
	";



if($i>0)
{
	echo "
	<script language='javascript'>
		function lasuite()
		{
			document.getElementById('formAdresses').submit();
		}
		
		setTimeout('lasuite()',4000);
	</script>
	";
}
?>
</body>
</html>
