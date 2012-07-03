<?php
// moulinette pour nettoyer les descriptions sur les images

//ini_set ('max_execution_time', 0);

include('../includes/framework/config.class.php');





class moulinette extends config
{
	function __construct()
	{
		parent::__construct();
		echo "
		<form action='descImages.php' method='post' enctype='multipart/form-data'>
			<input type='submit' name='submit' value='go'>
			
		</form>";
		
		if(isset($this->variablesPost['submit']) || isset($this->variablesPost['Suivant']))
		{
			$this->doMoulinette();
		}
	}

	public function doMoulinette()
	{
		echo "Lancement de la moulinette...<br>";
		
		if(isset($this->variablesPost['idHistoriqueImage']) && $this->variablesPost['idHistoriqueImage']!='0' && $this->variablesPost['idHistoriqueImage']!='')
		{
			// maj
			$update = "update historiqueImage SET description ='".$this->variablesPost['description']."' where idHistoriqueImage = '".$this->variablesPost['idHistoriqueImage']."'";
			$resUpdate = $this->connexionBdd->requete($update);

			
			// affichage du suivant
			$idCourant = (intval($this->variablesPost['idHistoriqueImage'])+1);
			$req = 
			"SELECT * FROM historiqueImage WHERE description<>'' and idHistoriqueImage='".$idCourant."'
			";
			
			
			
			$res = $this->connexionBdd->requete($req);
			
			while(mysql_num_rows($res)==0 && $idCourant<100000)
			{
				$req = 
			"SELECT * FROM historiqueImage WHERE description<>'' and idHistoriqueImage='".$idCourant."' and description like '%rn%'
			";
				$res = $this->connexionBdd->requete($req);
				$idCourant++;
			}
			
			
			$fetch = mysql_fetch_assoc($res);

		}
		else
		{
		
			$req = 
			"SELECT * FROM historiqueImage WHERE description<>''
			";
			$res = $this->connexionBdd->requete($req);
			
			$fetch = mysql_fetch_assoc($res);
		}
		
		
		echo "<form name='edit' method=post enctype='multipart/form-data'>";
		
		
		echo "<textarea name='description' cols=50 rows=40>".stripslashes($fetch['description'])."</textarea>";
		echo "<input type='text' name='idHistoriqueImage' value='".$fetch['idHistoriqueImage']."'>";
		echo "<input type='submit' name='Suivant' value='suivant'>";
		echo "</form>";
		
		
		

	}
}
$m = new moulinette();
?>