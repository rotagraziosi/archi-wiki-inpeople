<?php

include('../includes/config.class.php');

$mysqliNew = new mysqli("localhost", "archiv2", "jido", "ARCHI_V2");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}

$mysqliOld = new mysqli("localhost", "archiv2", "jido", "ARCHI_UTF8_LAURENT");
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}
$mysqliNew->query("SET NAMES 'utf8'");
$mysqliOld->query("SET NAMES 'utf8'");

function html2bb($html= '')
{
	$html = nl2br(trim(stripslashes($html)));
	$html =tidy_repair_string($html, array('output-xhtml' => true, 'show-body-only' => true, 'doctype' => 'strict', 'drop-font-tags' => true, 'drop-proprietary-attributes' => true, 'lower-literals' => true, 'quote-ampersand' => true, 'wrap' => 0), 'utf8');
	$html =trim($html);
	$html = preg_replace('!<a(.*)href=(.+)>(.+)</a>!isU', '[url=$2]$3[/url]', $html);
	$html = preg_replace('!<a(.*)>(.+)</a>!isU', '$2', $html);
	$html = preg_replace('!<a(.*)href=(.+)></a>!isU', '[url]$2[/url]', $html);
	$html = preg_replace('!<br(.*)>!isU', '\r\n', $html);
	$html = str_replace('<p>', '\r\n', $html);
	$html = str_replace('</p>', '\r\n', $html);
	return htmlspecialchars($html);
}

$regex='#<#';
echo '<h1>Dossiers</h1>';
if ($resOld = $mysqliOld->query("SELECT iddossier,titredossier, commentaires, textecommentaire FROM dossier
				LEFT JOIN commentaire USING(iddossier)"))
{
	while ($rep = $resOld->fetch_object())
	{
		//echo '<h3>'.$rep->iddossier.'</h3>';
		if (preg_match_all($regex, $rep->commentaires, $match))
		{
			echo '<p>'.html2bb($rep->commentaires).'</p>';
		}
		if (preg_match_all($regex, $rep->titredossier, $match))
		{
			echo '<p>'.html2bb($rep->titredossier).'</p>';
		}
		if (preg_match_all($regex, $rep->textecommentaire, $match))
		{
			echo '<p>'.html2bb($rep->textecommentaire).'</p>';
		}
			
	}
}
?>
