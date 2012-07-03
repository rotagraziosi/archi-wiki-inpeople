<?php
include("../includes/framework/config.class.php");

?>
<html>
<head>
<meta http-quiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php

$config = new config();

echo "Modification des adresses liees en groupes d'adresses lies<br>";

$req = "SELECT * FROM _evenementAdresseLiee WHERE idEvenementGroupeAdresse='0' AND idAdresse<>'0'";
$res = $config->connexionBdd->requete($req);

while($fetch = mysql_fetch_assoc($res))
{
	$reqGA = "SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$fetch['idAdresse']."'";
	echo $reqGA."<br>";
	$resGA = $config->connexionBdd->requete($reqGA);
	
	if(mysql_num_rows($resGA)==1)
	{
		$fetchGA = mysql_fetch_assoc($resGA);
		$idEvenementGroupeAdresse = $fetchGA['idEvenement'];
		
		$reqUpdate = "UPDATE _evenementAdresseLiee SET idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."' WHERE idAdresse='".$fetch['idAdresse']."'";
		echo $reqUpdate."<br>";
		$resUpdate = $config->connexionBdd->requete($reqUpdate);
		
	}
	else
	{
		echo "=> ".$fetch['idAdresse']."  (".mysql_num_rows($resGA).")<br>";
	}

}

?>
</body>
</html>
