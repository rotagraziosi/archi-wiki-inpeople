<?php
// recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/config.class.php');
include('../modules/archi/includes/archiImage.class.php');

class connex extends config
{
	public $connex;
	public $connectOld;
	public $connectNEW;
	
	function __construct()
	{
		parent::__construct();	
		//$this->connex = $this->connexionBdd;
		$connect0 = mysql_connect("localhost","archiv2","fd89ind") or die("probleme connexion0");
		$connect = mysql_connect("localhost","archiv2","fd89ind",true) or die("probleme connexion");
		mysql_select_db("ARCHI_V2",$connect0) or die("select db archiv2");
		mysql_select_db("archi_old", $connect) or die("select db archiold");
		$this->connectOLD = $connect;
		$this->connectNEW = $connect0;
		
	}
	
	function requeteNew($req)
	{
		return mysql_query($req, $this->connectNEW);
	}
	
	function requeteOld($req)
	{
		return mysql_query($req, $this->connectOLD);
	}
	
	
}
/*
$cheminImagesArchiv1 = '/home/laurent/public_html/archilaurent/photos/originaux/';

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
		if(isset($_POST["idHistoriqueImage"]) && $_POST["idHistoriqueImage"]!="")
		{
			// recherche de la date
			$resDate = $connex->connex->requete("select dateUpload from historiqueImage where idHistoriqueImage='".$_POST["idHistoriqueImage"]."';");
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
					$idHistoriqueImage = $_POST["idHistoriqueImage"];
					
					
					echo "fichierSource = ".$fichierSource."<br>";
					echo "destination = ".$i->cheminPhysiqueImagesMini.$dateUpload.'/'.$idHistoriqueImage.".jpg<br>";
					echo "typeFichier = ".$typeFichier."<br>";
					
					
					//$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesOriginaux.$dateUpload.'/'.$idHistoriqueImage.".jpg",0);
					//echo 'ok|';
					//$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesMini.$dateUpload.'/'.$idHistoriqueImage.".jpg",80);
					//echo 'ok|';
					//$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesMoyen.$dateUpload.'/'.$idHistoriqueImage.".jpg",200);
					//echo 'ok|';
					//$i->redimension( $fichierSource, $typeFichier, $i->cheminPhysiqueImagesGrand.$dateUpload.'/'.$idHistoriqueImage.".jpg",500);
				}
			}
			else
			{
				echo "nombre de resultat pour la recherche de date d'apres l'idHistoriqueImage =".mysql_num_rows($resDate)."<br>";
			}
		}
	}
	*/
	
	
	$connex = new connex();
	
	$res = $connex->requeteNew("
	
			SELECT DISTINCT ee.idEvenement AS evenementGroupeAdresseSansAdresse
			FROM historiqueEvenement he2, historiqueEvenement he1
			RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
			LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
			WHERE he1.idEvenement = he2.idEvenement
			AND ae.idEvenement IS NULL
			GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
			HAVING he1.idHistoriqueEvenement = max( he2.idHistoriqueEvenement)
	
	");
	
	while($fetch = mysql_fetch_assoc($res))
	{
		$resPremier = $connex->requeteNew("
			SELECT he1.idEvenement as idEvenement,ee.idEvenement as idEvenementGroupeAdresse,he1.titre as titre, he1.description as description
			FROM historiqueEvenement he2,historiqueEvenement he1
			RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = '".$fetch['evenementGroupeAdresseSansAdresse']."'
			WHERE he2.idEvenement = he1.idEvenement
			AND he1.idEvenement = ee.idEvenementAssocie
			AND he1.titre!='\"\"' AND he1.titre!='\"\" nouvel an'
			GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
			HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
			LIMIT 1
		");
		
		while($fetchPremier = mysql_fetch_assoc($resPremier))
		{
			echo $fetchPremier['titre'].' '.$fetchPremier['idEvenement'].' '.$fetchPremier['idEvenementGroupeAdresse'].'<br>';
			
			$resCorrespondance = $connex->requeteOld("
					SELECT v.codepostal as cp,d.iddossier as iddossier,d.titredossier as titredossier, d.idville as idville,d.idquartier as idquartier,v.nomville 
					FROM dossier d 
					LEFT JOIN ville v ON v.idville = d.idville 
					WHERE d.titredossier = \"".$fetchPremier['titre']."\" 
					and d.idquartier = '0'
					and substr(v.codepostal,1,2) ='67'
			");
			echo "SELECT d.iddossier as iddossier,d.titredossier as titredossier, d.idville as idville,v.nomville FROM dossier d LEFT JOIN ville v ON v.idville = d.idville WHERE d.titredossier = \"".$fetchPremier['titre']."\"<br>";
			while($fetchCorrespondance = mysql_fetch_assoc($resCorrespondance))
			{
				echo "=>".$fetchCorrespondance['titredossier']." ".$fetchCorrespondance['nomville']." ".$fetchCorrespondance['cp'].' '.$fetchCorrespondance['idquartier']."<br>";
				
				// le dossier a ete trouve 
				
				// verif si l'adresse existe dans archiv2
				//$resArchiv2 = $connex->requeteNew("");
				// recuperation de la ville dans archiv2
				$resNewVille = $connex->requeteNew("select idVille,nom from ville where nom='".$fetchCorrespondance['nomville']."'");
				while($fetchNewVille = mysql_fetch_assoc($resNewVille))
				{
					echo "Ajouter ?=====>".$fetchNewVille['idVille'].' '.$fetchNewVille['nom']."<br>";
					
					$resNewAdresse = $connex->requeteNew("
							SELECT ha1.idAdresse
							FROM historiqueAdresse ha2, historiqueAdresse ha1
							WHERE 
							ha1.idVille = '".$fetchNewVille['idVille']."'
							AND ha1.idQuartier='0'
							AND ha1.idSousQuartier='0'
							AND ha1.idPays='0'
							AND ha1.idRue='0'
							AND ha2.idAdresse = ha1.idAdresse
							GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
							HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)");
					echo "SELECT ha1.idAdresse
							FROM historiqueAdresse ha2, historiqueAdresse ha1
							WHERE 
							ha1.idVille = '".$fetchNewVille['idVille']."'
							AND ha1.idQuartier='0'
							AND ha1.idSousQuartier='0'
							AND ha1.idPays='0'
							AND ha1.idRue='0'
							AND ha2.idAdresse = ha1.idAdresse
							GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
							HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
					$idAdresseLiaison=0;
					if(mysql_num_rows($resNewAdresse)>0)
					{
						echo "<br><br>==========l'adresse existe===========<br><br>";
						$fetchNewAdresse = mysql_fetch_assoc($resNewAdresse);
						$idAdresseLiaison = $fetchNewAdresse['idAdresse'];
					}
					else
					{
						echo "<br><br>======creation de l'adresse=========<br><br>";
						
						$reqNewIdAdresse = "select max(idAdresse) as idAdresseMAX from historiqueAdresse";
						$resNewIdAdresse = $connex->requeteNew($reqNewIdAdresse);
						$fetchNewIdAdresse = mysql_fetch_assoc($resNewIdAdresse);
						$newIdAdresse = $fetchNewIdAdresse["idAdresseMAX"]+1;
						$reqInsert="insert into historiqueAdresse (idAdresse,idVille,idQuartier,idSousQuartier,idPays,idRue) values ('".$newIdAdresse."','".$fetchNewVille['idVille']."','0','0','0','0')";
						echo $reqInsert."<br><br><br><br><br>";
						$resAjout=$connex->requeteNew($reqInsert) or die ('error insert');
						$idAdresseLiaison = $newIdAdresse;
					}
					echo "idAdresseLiaison = ".$idAdresseLiaison."<br>";
					if($idAdresseLiaison!=0)
					{
						// insertion de la nouvelle liaison
						$reqLiaison = "insert into _adresseEvenement (idAdresse,idEvenement) 
						values ('".$idAdresseLiaison."','".$fetch['evenementGroupeAdresseSansAdresse']."')";
						echo $reqLiaison."<br><br><br><br><br>";
						$connex->requeteNew($reqLiaison);
					}
				}
			}
		}
	}
	
?>



