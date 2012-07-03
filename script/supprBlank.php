<?php
// recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
// recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
// comme nom idHistoriqueImage 

ini_set ('max_execution_time', 0);
include('PEAR.php');
include('HTML/BBCodeParser.php');
include('../includes/framework/config.class.php');
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


class connexionBD extends config
{
	public $connexion;
	
	function __construct()
	{
		parent::__construct();
		$this->connexion = $this->connexionBdd;
	}
}

$connex = new connexionBD();

if(isset($_GET['idModif']) && $_GET['idModif']!='' && $_GET['idModif']!='0' && isset($_POST['desc-'.$_GET['idModif']]))
{
	// enregistrement de la modification
	$description = $_POST['desc-'.$_GET['idModif']];
	$description = mysql_escape_string($description);
	$req = "update historiqueEvenement set description=\"".$description."\" where idHistoriqueEvenement = '".$_GET['idModif']."'";
	$res = $connex->connexion->requete($req);
	echo $req;
}




/*$regExp[0] = array( "regle"=>"\\[url=\"(.+)\"\\](.+)\\[/url\\]", "remplacement"=>"<a href=\"http://$1\" target=\"_blank\">$2</a>" );
$regExp[1] = array( "regle"=>"\\[url=(.+)\\](.+)\\[/url\\]", "remplacement"=>"<a href=\"http://$1\" target=\"_blank\">$2</a>" );
$regExp[2] = array( "regle"=>"\\<a\ href\=\\'(.+)\\'\\>(.+)\\</a\\>", "remplacement"=>"" );
*/

$regExp[0] = array("regle"=>"\\[url");
$regExp[1] = array("regle"=>"\\<a");

$req = "	
		SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement,he1.description as description
		FROM historiqueEvenement he2,historiqueEvenement he1
		WHERE he2.idEvenement = he1.idEvenement
		GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
		HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
		";
		
		
		

$res = $connex->connexion->requete($req);


$tableau = new tableau();
$tableau->addValue('regle utilisee');
$tableau->addValue('idHistoriqueEvenement');
$tableau->addValue('description');
$tableau->addValue('validation');

while($fetch = mysql_fetch_assoc($res))
{
	$description = stripslashes($fetch['description']);
	$description = stripslashes($description);
	foreach($regExp as $indice => $expReg)
	{
		//echo $expReg['regle']."<br>";
		if(eregi($expReg['regle'],$description,$retourAdr))
		{
			
			$tableau->addValue($indice."<br>".$expReg['regle']);
			$tableau->addValue($fetch['idHistoriqueEvenement']);
			$tableau->addValue("<textarea cols='70' rows='10' name='desc-".$fetch['idHistoriqueEvenement']."'>".$description."</textarea>");
			$tableau->addValue("<input type='button' name='ok-".$fetch['idHistoriqueEvenement']."' value='OK' onclick=\"document.getElementById('formModif').action='supprBlank.php?idModif=".$fetch['idHistoriqueEvenement']."';document.getElementById('formModif').submit();\">");
		}
	}
}
echo "<form action='supprBlank.php' name='formModif' id='formModif' enctype='multipart/form-data' method='POST'>";
echo $tableau->createHtmlTableFromArray(4);
echo "</form>";

?>
</body>
</html>


