<?php
// recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/framework/config.class.php');
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

//$cheminImagesArchiv1 = '/home/laurent/public_html/archilaurent/photos/originaux/';

$cheminImagesArchiv1 = '/home/pia/archi/photos/originaux/';

$i = new archiImage();
echo $cheminImagesArchiv1."=><br>";
echo "mini=".$i->cheminPhysiqueImagesMini."<br>";
echo "moyen=".$i->cheminPhysiqueImagesMoyen."<br>";
echo "grand=".$i->cheminPhysiqueImagesGrand."<br>";

$iddossier="";
if(isset($_POST["iddossier"]))
	$iddossier=$_POST["iddossier"];
?>

<form action="transfert.php" name="transfert" enctype="multipart/form-data" method="post">

iddossier = <input type="text" name="iddossier" value="<?php echo $iddossier; ?>"><input type="submit" value="Lister" name="lister"><br>
Liste des images du dossier : <br>
<?php
if(isset($_POST["iddossier"]) && isset($_POST["lister"]))
{
	$Directory = $cheminImagesArchiv1.$_POST["iddossier"]."/";

	if (is_dir($Directory) && is_readable($Directory)) 
	{
		if($MyDirectory = opendir($Directory)) 
		{
			while($Entry = readdir($MyDirectory)) 
			{
				if($Entry!='.' && $Entry!='..')
				{
					echo "<input type='radio' name='nomFichier' value='".$Entry."'>".$Entry."<br>";
				}
			}
		}
	}
}
?>
<br>
idImage = <input type="text" name="idImage" value=""><br>

<input type="submit" value="Valider" name="valider"><br>

</form>

<?php

$connex = new connex();

	if(isset($_POST["valider"]))
	{
		if(isset($_POST["idImage"]) && $_POST["idImage"]!="")
		{
			// recherche de la date
			$resDate = $connex->connex->requete("select idHistoriqueImage,dateUpload from historiqueImage where idImage='".$_POST["idImage"]."';");
			if(mysql_num_rows($resDate)==1)
			{
				$fetchDate = mysql_fetch_assoc($resDate);
				echo "date =".$fetchDate["dateUpload"]."<br>";
				if(isset($_POST["nomFichier"]) && $_POST["nomFichier"]!="")
				{
					echo "nomFichier a transferer : ".$_POST["nomFichier"]."<br>";
					
					$fichierSource = $cheminImagesArchiv1.$_POST['iddossier']."/".$_POST['nomFichier'];
					
					$dateUpload = $fetchDate["dateUpload"];
					$typeFichier = pia_substr(strtolower($fichierSource),-3);
					$idHistoriqueImage = $fetchDate['idHistoriqueImage'];
					
					
					echo "fichierSource = ".$fichierSource."<br>";
					echo "destination = ".$i->cheminPhysiqueImagesMini.$dateUpload.'/'.$idHistoriqueImage.".jpg<br>";
					echo "typeFichier = ".$typeFichier."<br>";
					
					$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesOriginaux.$dateUpload.'/'.$idHistoriqueImage.".jpg",0);
					echo 'ok|';
					$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesMini.$dateUpload.'/'.$idHistoriqueImage.".jpg",80);
					echo 'ok|';
					$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesMoyen.$dateUpload.'/'.$idHistoriqueImage.".jpg",200);
					echo 'ok|';
					$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesGrand.$dateUpload.'/'.$idHistoriqueImage.".jpg",500);
					echo "fintransfert";	
					
				}
			}
			else
			{
				echo "nombre de resultat pour la recherche de date d'apres l'idImage =".mysql_num_rows($resDate)."<br>";
			}
		}
	}
?>



