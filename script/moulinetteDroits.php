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



$req = "select * from utilisateur";
$res = $config->connexionBdd->requete($req);


while($fetch = mysql_fetch_assoc($res))
{
	if($fetch['estAdmin']==1)
	{
		$reqUpdate = "update utilisateur set idProfil=4 where idUtilisateur = '".$fetch['idUtilisateur']."'";
		$resUpdate = $config->connexionBdd->requete($reqUpdate);
		
	}
}


?>
</body>
</html>
