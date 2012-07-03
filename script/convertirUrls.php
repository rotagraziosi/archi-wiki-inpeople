<?php
// recuperation du fichier a partir de la liste et du repertoire identifi� par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/config.class.php');
include('../modules/archi/includes/archiImage.class.php');
function nettoyeChaine ($string) {
	return stripslashes(htmlspecialchars(trim($string)));
}

function nettoyeChiffre ($string) {
	$return = htmlspecialchars(trim($string));
	$return = preg_replace('#[^0-9]#', $return, $return);
	return $return;
}

function nettoyeDate($string) {
	return $string;
}

function html2bb($html= '')
{
	$old = $html;
	$html = trim(stripslashes($html));
	$html =tidy_repair_string($html, array('output-xhtml' => true, 'show-body-only' => true, 'doctype' => 'strict', 'drop-font-tags' => true, 'drop-proprietary-attributes' => true, 'lower-literals' => true, 'quote-ampersand' => true, 'wrap' => 0), 'utf8');
	$html =trim($html);
	$html = preg_replace('!<a(.*)href=(.+)>(.+)</a>!isU', '[url=$2]$3[/url]', $html);
	$html = preg_replace('!(&lt;|<)a(.*)href=(.+)(&gt;|>)(.+)(&lt;|<)/a(&gt;|>)!isU', '[url=$3]$5[/url]', $html);
	$html = preg_replace('!<a(.*)>(.+)</a>!isU', '$2', $html);
	$html = preg_replace('!<a(.*)href=(.+)></a>!isU', '[url]$2[/url]', $html);
	$html = preg_replace('!(&lt;|<)br(.*)(&gt;|>)!isU', "\r\n", $html);
	$html = str_replace('<p>', "\r\n", $html);
	$html = str_replace('</p>', "", $html);
	return $html;
}
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

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
$tabReg = array(
	"\\[url=\"http\\://(.+)\"\\](.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$2</a>",
	"\\[url\\]http\\://(.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$1</a>",
	"\\[url=\"(.+)\\](.+)\"\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$2</a>",
	"\\[url\\](.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$1</a>",
	"\\[url=http\\://(.+)\\](.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$2</a>",
	"\\[url\\]http\\://(.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$1</a>",
	"\\[url\\](.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$1</a>",
	"\\[url=(.+)\\](.+)\\[/url\\]" => "<a href=\"http://$1\" target=\"_blank\">$2</a>"
);








$connex = new connex();
echo "<b>Convertisseur d'url a la chaine</b><br>";
$resRechercheDescription = $connex->requeteNew("
						SELECT idHistoriqueEvenement,titre,description 
						FROM historiqueEvenement
						WHERE 
							description like \"%www.archi-strasbourg.org%\"
						OR
							description like \"%<a%\"
					
					");

					
echo "Nombre de résultats avec des url : ".mysql_num_rows($resRechercheDescription)."<br>";
$nbUrl=0;
$nbidentDossier=0;
$ok=0;
$tabCorrespondances=array();

while($fetchRechercheDescription = mysql_fetch_assoc($resRechercheDescription))
{
	echo "<form action='' name='' enctype='multipart/form-data' method='POST'>";
	echo "<table><tr><td width='150'>";
	echo $fetchRechercheDescription["idHistoriqueEvenement"]." ".$fetchRechercheDescription["titre"]."</td><td>";
	echo "<textarea name='".$fetchRechercheDescription["idHistoriqueEvenement"]."' cols='50' rows='20'>".$fetchRechercheDescription["description"]."</textarea>";
	$url="";
	$retourAdr=array();
	if(eregi("\\[url=\"(.+)\"\\](.+)\\[/url\\]",$fetchRechercheDescription["description"],$retourAdr))
	{
		$nbUrl++;
		var_dump($retourAdr);
		if(count($retourAdr[0])>1)
			echo "<font color='orange'>Plusieurs adresses dans le commentaire !!!!!!!</font><br>";
		
		// si plusieurs adresses : $retour[0][0] = adresses
		// 
		
		
			/*for($i=0 ; $i<count($retourAdr[1]) ; $i++)
			{
				echo "<br>URL : ".$retourAdr[1][$i]."<br>";
			}*/
			$url = $retourAdr[1];
			
			$iddossier="";
			if(eregi("ident=([0-9]+)$",$retourAdr[1],$retourAdrIdent) && substr_count($fetchRechercheDescription["description"],'[url')==1)
			{
				echo "<br>substr_count = ".substr_count($retourAdr[1],'[url')."<br>";
				$nbidentDossier++;
				echo "<br>iddossier = ".$retourAdrIdent[1]."<br>";
				$iddossier = $retourAdrIdent[1];
				
				// recuperation du titre dans la version archiv1 a partir de l'iddossier trouvé
				$resTitreV1 = $connex->requeteOld("SELECT titredossier, commentaires FROM dossier WHERE iddossier='".$iddossier."'");
				
				if(mysql_num_rows($resTitreV1)==1)
				{
					
					$fetchTitreV1 = mysql_fetch_assoc($resTitreV1);
					
					$titre = $fetchTitreV1['titredossier'];
					
					echo "dossier a recherche : ".$titre."<br>";
					
					if($titre !='')
					{
						// recherche d'une correspondance dans la v2
						$queryV2Titre = $connex->requeteNew("
															SELECT idHistoriqueEvenement 
															FROM historiqueEvenement
															WHERE titre = \"".$titre."\"
													
													");
						if(mysql_num_rows($queryV2Titre)==1)
						{
							echo "<font color='green'>=>Evenement trouvé!!! </font><br>";
							
							$fetchV2Titre = mysql_fetch_assoc($queryV2Titre);
							
							$tabCorrespondances[]=array("adr"=>$retourAdr,"iddossier"=>$iddossier,"idHistoriqueEvenementText"=>$fetchRechercheDescription["idHistoriqueEvenement"],"idHistoriqueEvenementUrl"=>$fetchV2Titre['idHistoriqueEvenement'],"description"=>$fetchRechercheDescription['description']);
							
							$ok++;
						}
						elseif(mysql_num_rows($queryV2Titre)>1)
						{
							echo "<font color='red'>=>plusieurs evenements correspondent par le titre !!! </font><br>";
						}
						else
						{
							echo "<font color='red'>=>pas de correpondance trouvee par le titre!!! </font><br>";
						}
						
					}
					else
					{
						// recherche a partir du debut du texte dans commentaires
						$debutCommentaire = pia_substr(strtolower($fetchTitreV1['commentaires']),0,70);
						echo "<font color='green'>reconnaissance par debut commentaire : </font>".$debutCommentaire."<br>";
						
						
						// recherche d'une correspondance dans la v2
						$queryV2Commentaire = $connex->requeteNew("
															SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement
															FROM historiqueEvenement he2, historiqueEvenement he1
															WHERE lower(he1.description) like \"%".$debutCommentaire."%\"
															AND he2.idEvenement = he1.idEvenement
															GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
															HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
													");
						if(mysql_num_rows($queryV2Commentaire)==1)
						{
							$ok++;
							echo "<font color='green'>=>Evenement trouvé!!! </font><br>";
							// MISE A JOUR
							$resQueryV2Commentaire = mysql_fetch_assoc($queryV2Commentaire);
							
							$tabCorrespondances[]=array("adr"=>$retourAdr,"iddossier"=>$iddossier,"idHistoriqueEvenementText"=>$fetchRechercheDescription["idHistoriqueEvenement"],"idHistoriqueEvenementUrl"=>$resQueryV2Commentaire['idHistoriqueEvenement'],"description"=>$fetchRechercheDescription['description']);
							
							
						}
						elseif(mysql_num_rows($queryV2Commentaire)>1)
						{
							echo "<font color='red'>=>Plusieurs correspondance trouvees :( </font><br>";
						}
						else
						{
							echo "<font color='red'>=>Aucune correspondance trouvees :( </font><br>";
						}
						
					}
				}
				else
				{
					echo "<font color='red'>Erreur :: iddossier non trouve : $iddossier</font><br>";
				}
			}
			else
			{
				echo "<font color='red'>iddossier non trouve</font><br>";
			}
		
	}
	
	echo "</td><td><input type='submit' value='valider'></td></tr></table>";
	echo "</form>";
	
}

echo "<br><br>nbUrl trouvees = ".$nbUrl."<br>";
echo "<br>nbiddosser trouvees = ".$nbidentDossier."<br>";
echo "<br>conversion possibles : ".$ok."<br>";
echo "<br>";
echo "nbCorrespondances=".count($tabCorrespondances)."<br>";
$nbRemplacements = 0;
foreach($tabCorrespondances as $indice => $correspond)
{
	echo "<br>******************************************************************************************************************************************************<br>";
	echo $correspond["adr"][0]." ".$correspond["adr"][1]." ".$correspond["iddossier"]." ".$correspond["idHistoriqueEvenementUrl"]."<br>";
	echo "<br>******************************************************************************************************************************************************<br>";
	// recherche de l'idAdresse correspondant a l'idHistoriqueEvenement => groupe d'adresse
	
	$reqAdresse = "
					SELECT ee.idEvenement as idEvenementGroupeAdresse, ae.idAdresse as idAdresse
					FROM historiqueEvenement he1
					RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
					RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
					WHERE he1.idHistoriqueEvenement = '".$correspond["idHistoriqueEvenementUrl"]."'
				";
	
	
	echo $reqAdresse;
	$resAdresse = $connex->requeteNew($reqAdresse);
	
	if(mysql_num_rows($resAdresse)==1)
	{
		$fetchAdresse = mysql_fetch_assoc($resAdresse);
		
		$str = $correspond['description'];
		if($str2 = str_replace($correspond['adr'][1],"http://###serveur###/?archiAffichage=adresseDetail&archiIdAdresse=".$fetchAdresse['idAdresse'],$str));
		{
		
			if($str2 != $str)
			{
				$nbRemplacements++;
				
				echo "<br><br>-------------------------------------------------------------------------------------------------------<br>";
				
				echo "recherche = ".$correspond['adr'][1]."<br><br>";
				
				
				echo $str."<br>";
				echo "<br>=><br>";
				echo $str2."<br>";
				echo "<br><br>";
				
				echo "depart = <a href='".$correspond['adr'][1]."'>".$correspond['adr'][1]."</a><br>";
				echo "conversion = <a target='_blank' href='http://strasbourg.pia.com.fr/~laurent/a/?archiAffichage=adresseDetail&archiIdAdresse=".$fetchAdresse['idAdresse']."'>http://###serveur###/?archiAffichage=adresseDetail&archiIdAdresse=".$fetchAdresse['idAdresse']."</a><br>";
				
				// ********************
				// * requete de mise a jour:  *
				// ********************
				
				$reqMaj = "update historiqueEvenement set description = \"".addslashes($str2)."\" where idHistoriqueEvenement='".$correspond["idHistoriqueEvenementText"]."'";
				echo "<br><br>";
				echo $reqMaj;
				echo "<br>";
				echo "-------------------------------------------------------------------------------------------------------<br><br>";
				$resMaj = $connex->requeteNew($reqMaj);
			}
		}
	}
	else
	{
		echo "<br><font color='red'>plusieurs adresses correspondent</font><br>";
	}
}
echo "nombre de remplacement : ".$nbRemplacements."<br>";

?>
</body>
</html>


