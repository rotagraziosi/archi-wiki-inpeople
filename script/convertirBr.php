<?php
// recuperation du fichier a partir de la liste et du repertoire identifiÈ par iddossier
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
	/*$old = $html;
	$html = trim(stripslashes($html));
	//$html =tidy_repair_string($html, array('output-xhtml' => true, 'show-body-only' => true, 'doctype' => 'strict', 'drop-font-tags' => true, 'drop-proprietary-attributes' => true, 'lower-literals' => true, 'quote-ampersand' => true, 'wrap' => 0), 'utf8');
	$html =trim($html);
	$html = preg_replace('!<a(.*)href=(.+)>(.+)</a>!isU', '[url=$2]$3[/url]', $html);
	$html = preg_replace('!(&lt;|<)a(.*)href=(.+)(&gt;|>)(.+)(&lt;|<)/a(&gt;|>)!isU', '[url=$3]$5[/url]', $html);
	$html = preg_replace('!<a(.*)>(.+)</a>!isU', '$2', $html);
	$html = preg_replace('!<a(.*)href=(.+)></a>!isU', '[url]$2[/url]', $html);
	//$html = preg_replace('!(&lt;|<)br(.*)(&gt;|>)!isU', "\r\n", $html);
	$html = str_replace('<p>', "\r\n", $html);
	$html = str_replace('</p>', "", $html);*/
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
echo "<b>Remise a jour des evenements ou les br on ete supprimes</b><br>";
$resRechercheDescription = $connex->requeteNew("
						SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement,he1.titre as titre,he1.description as description
						FROM historiqueEvenement he2, historiqueEvenement he1
						WHERE he2.idEvenement = he1.idEvenement
						AND he1.dateCreationEvenement<'2008-05-25 00:00:00'
						AND he1.idTypeEvenement<>'11'
						GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
						HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
					");

					
echo "Nombre de r√©sultats avec des url : ".mysql_num_rows($resRechercheDescription)."<br>";


while($fetchRechercheDescription = mysql_fetch_assoc($resRechercheDescription))
{

	$idHistoriqueEvenementAMettreAJour=$fetchRechercheDescription["idHistoriqueEvenement"];
	echo "<br>idHistoriqueAMettreAJour = ".$idHistoriqueEvenementAMettreAJour."<br>";

	/*if(substr_count($fetchRechercheDescription['description'],"\r\n")>0)
	{
		echo "<table border=1><tr><td>".$fetchRechercheDescription['idHistoriqueEvenement']." ".$fetchRechercheDescription['titre']."</td><td>".str_replace("\r","<br>",$fetchRechercheDescription['description'])."</td></tr></table><br><br><br>";
	}*/

	$resTitreV1 = $connex->requeteOld("select * from dossier where titredossier like \"%".$fetchRechercheDescription['titre']."%\"");
	
	
	if(mysql_num_rows($resTitreV1)==1)
	{
		echo "<font color='green'>titre trouve</font><br>";
		echo "update<br>";
		$fetchTitreV1 = mysql_fetch_assoc($resTitreV1);
		$reqUpdateTitre = "update historiqueEvenement set description = \"".$fetchTitreV1['commentaires']."\" where idHistoriqueEvenement='".$idHistoriqueEvenementAMettreAJour."'";
		
		$resUpdateTitre = $connex->requeteNew($reqUpdateTitre);
		
		echo "idHistoriqueEvenement courant = ".$idHistoriqueEvenementAMettreAJour."<br>";
		echo "iddossier courant = ".$fetchTitreV1['iddossier']."<br>";
		
	}
	elseif(mysql_num_rows($resTitreV1)>1)
	{
		echo "<font color='red'>plusieurs titre correspondent<br>";
		echo "recherche au niveau du commentaire<br></font>";
		$resCommentaireV1 = $connex->requeteOld("select * from dossier where commentaires like \"%".pia_substr($fetchRechercheDescription['description'],0,20)."%\"");
		
		if(mysql_num_rows($resCommentaireV1)==1)
		{
			echo "<font color='green'>=>commentaire OK trouve</font><br>";

			$fetchCommentaireV1 = mysql_fetch_assoc($resCommentaireV1);
			
			
			echo "idHistoriqueEvenement courant = ".$idHistoriqueEvenementAMettreAJour."<br>";
			echo "iddossier courant = ".$fetchCommentaireV1['iddossier']."<br>";
			
			$reqUpdateCommentaire = "update historiqueEvenement set description = \"".$fetchCommentaireV1['commentaires']."\" where idHistoriqueEvenement='".$idHistoriqueEvenementAMettreAJour."'";
			$resUpdateCommentaire = $connex->requeteNew($reqUpdateCommentaire);
			
		}
		elseif(mysql_num_rows($resCommentaireV1)>1)
		{
			echo "<font color='red'>plusieurs correspondances , abandon => ( pour idHistoriqueEvenement = ".$idHistoriqueEvenementAMettreAJour.")</font><br>";
		}
		else
		{
			echo "<font color='red'>non trouve</font>";
		}
		
		
		
	}
	else
	{
		echo "<font color='red'>non trouve</font><br>";
	}
}


?>
</body>
</html>


