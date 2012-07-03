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
		
		$this->requeteNew("set names 'utf8'");
		$this->requeteOld("set names 'utf8'");
		
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
	?>
	<html>
	<body>
	<?php
	
	
	$connex = new connex();
	// iddossier 	idetat 	idutilisateur 	idadresse 	idville 	numerovoie 	idquartier 	idsousquartier 	idtypecourantarchitecture 	iddossierpere 	idpersonne 	anneeconstruction 	datedossier 	commentaires 	
	// idtypeimage 	idtypedossier 	titredossier 	trifils 	estvide 	indicatif
	$res = $connex->requeteOld("
		SELECT iddossier, idpersonne, anneeconstruction, datedossier, commentaires, titredossier
		FROM dossier
		WHERE idpersonne IS NOT NULL and idpersonne<>'0'
	");
	
	echo "nombre de dossiers qui sont reliés a une personne : ".mysql_num_rows($res)."<br>";
	
	$tabiddossierCorrespondancesNonTrouvees = array();
	$tabiddossierCorrespondancesMultiples = array();
	$tabiddossierCorrespondancesSansTitreSansCommentaire = array();
	$tabCorrespondancesOK=array();
	$tabCorrespondancesDejaEffectuee=array();
	$tabPersonnesPastrouvee=array();
	$tabCorrespondancesConfirmees=array();
	$tabCorrespondances=array(); // tableau utilisé pour effectuer les insert dans la table de liaison
	while($fetch = mysql_fetch_assoc($res))
	{
		if($fetch['titredossier']!='' && $fetch['commentaires']!='' && $fetch['datedossier']!='' && $fetch['anneeconstruction']!='')
		{
			echo "==titre==".$fetch['titredossier'].'<br>';
			echo "==commentaire==".$fetch['commentaires'].' <br>';
			$reqRechercheCorrespondance = "
											SELECT he1.idEvenement as idEvenement,he1.titre as titre
											FROM historiqueEvenement he2, historiqueEvenement he1
											WHERE he1.idEvenement = he2.idEvenement
											AND he1.titre = \"".$fetch['titredossier']."\"
											AND he1.dateDebut = '".$fetch['anneeconstruction']."-00-00'
											AND he1.dateCreationEvenement = '".$fetch['datedossier']." 00:00:00'
											GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
											HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
			";
			
			$resRechercheCorrespondance = $connex->requeteNew($reqRechercheCorrespondance);
			
			if(mysql_num_rows($resRechercheCorrespondance)==0)
			{
				echo "<font color='red'>pas de correspondance trouvee pour le titre</font><br>";
				$tabiddossierCorrespondancesNonTrouvees[] = $fetch['iddossier'];
				
			}
			elseif(mysql_num_rows($resRechercheCorrespondance)==1)
			{
				echo "<font color='green'>Une correspondance trouvee</font><br>";
				$fetchRechercheCorrespondance = mysql_fetch_assoc($resRechercheCorrespondance);
				echo $fetchRechercheCorrespondance['titre']."<br>";
				
				if(in_array($fetch['iddossier'],$tabCorrespondancesOK))
				{
					echo "Correspondance deja effectuee : attention<br>";
					$tabCorrespondancesDejaEffectuee[] = $fetch['iddossier'];
				}
				else
				{
					echo "Correspondance ajoutée<br>";
					$tabCorrespondancesOK[]=$fetch['iddossier'];
					
					
					$reqPersonneOld = "
									SELECT idpersonne,prenompersonne,nompersonne,datenaissance,datedeces
									FROM personne
									WHERE idpersonne = '".$fetch['idpersonne']."'
					";
					
					$resPersonneOld = $connex->requeteOld($reqPersonneOld);
					
					while($fetchPersonneOld = mysql_fetch_assoc($resPersonneOld))
					{
						echo "<br><br>iddossier = ".$fetch['iddossier'].' '.$fetchPersonneOld['prenompersonne'].' '.$fetchPersonneOld['nompersonne']."<br><br>";
						
						// recherche de la personne dans archiv2
						$reqPersonneNew = "select idPersonne,nom,prenom from personne where replace(nom,'&amp;','&') = '".$fetchPersonneOld['nompersonne']."' and replace(prenom,'&amp;','&')='".$fetchPersonneOld['prenompersonne']."' and dateNaissance = '".$fetchPersonneOld['datenaissance']."' and dateDeces = '".$fetchPersonneOld['datedeces']."'";
						
						$resPersonneNew = $connex->requeteNew($reqPersonneNew);
						
						if(mysql_num_rows($resPersonneNew)==1)
						{
							if(!in_array($fetchPersonneOld['idpersonne'],$tabCorrespondancesConfirmees))
							{
								echo "<font color='green'>**** une correspondance sur le nom && prenom trouvée , OK ****</font><br>";
								$tabCorrespondancesConfirmees[]=$fetchPersonneOld['idpersonne'];
								
								$fetchPersonneNew = mysql_fetch_assoc($resPersonneNew);
								$tabCorrespondances[$fetchRechercheCorrespondance['idEvenement']] = $fetchPersonneNew['idPersonne'];
								
							}
						}
						elseif(mysql_num_rows($resPersonneNew)==0)
						{
							echo "<font color='red'>**** aucune correspondance sur la personne de trouvee,abandon ****</font><br>";
							$tabPersonnesPastrouvee[] = $fetchPersonneOld['idpersonne'];
						}
						else
						{	
							echo "<font color='red'>**** Plusieurs correspondances trouvee pour la personne, abandon ****</font>";
						}
					}
				}
			}
			else
			{
				echo "<font color='red'>Plusieurs correspondances trouvees, abandon</font><br>";
				$tabiddossierCorrespondancesMultiples[] = $fetch['iddossier'];
			}
		}
		else
		{
			echo "<font color='red'>pas de titre ni commentaire, abandon</font><br>";
			$tabiddossierCorrespondancesSansTitreSansCommentaire[]=$fetch['iddossier'];
		}
	}
	
	echo "nombre de correspondances non trouvees : ".count($tabiddossierCorrespondancesNonTrouvees)."<br>";
	echo "nombre de correspondances multiples :".count($tabiddossierCorrespondancesMultiples)."<br>";
	echo "nombre de correspondances impossibles (pas de titre ni de commentaire): ".count($tabiddossierCorrespondancesSansTitreSansCommentaire)."<br>";
	echo "nombre de correspondances trouvees : ".count($tabCorrespondancesOK)."<br>";
	echo "nombre de correspondances deja effectuees : ".count($tabCorrespondancesDejaEffectuee)."<br>";
	echo "iddossier posant problemes : ";
	$listeDossiersProblemes = implode(",",$tabiddossierCorrespondancesSansTitreSansCommentaire);
	echo $listeDossiersProblemes."<br>";
	echo "idpersonnes pas trouvee dans la nouvelle base 'personnes' : ";
	$listePersonnesPasTrouvees = implode(",",$tabPersonnesPastrouvee);
	echo $listePersonnesPasTrouvees."<br>";
	echo "correspondances ok avec les personnes : ".count($tabCorrespondancesConfirmees)."<br>";
	
	echo "requetes a copier coller : <br>";
	
	foreach($tabCorrespondances as $idEvenement => $idPersonne)
	{
		echo "insert into _evenementPersonne (idEvenement,idPersonne) values ('".$idEvenement."','".$idPersonne."');<br>";
	}
	
	?>
	</body>
	</html>
	<?php
?>



