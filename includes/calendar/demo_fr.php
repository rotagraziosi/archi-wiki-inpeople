<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <link rel="stylesheet" href="calendar.css" type="text/css" />
 <title>D&eacute;monstration du calendrier</title>
</head>

<body>
<p>Note&nbsp;: la conservation des URL a &eacute;t&eacute; d&eacute;sactiv&eacute;e et il n'y a pas d'URL
cliquable sur les dates.</p>

<?php require_once("calendar.php"); ?>

<h1>Calendrier sans session</h1>
<?php Calendar(array("PREFIX" => "cal1_", "PRESERVE_URL" => false, "DATE_URL" => "/target/target.php")); ?>

<h1>Calendrier avec session</h1>
<?php Calendar(array("PREFIX" => "cal2_", "PRESERVE_URL" => false, "USE_SESSION" => true, "DATE_URL" => "target/target.php")); ?>

<h1>Calendrier en JavaScript (sans session)</h1>
<script type="text/javascript" src="calendar_js.php?PREFIX=cal3_&amp;PRESERVE_URL=false&amp;DATE_URL=target%2ftarget.php"></script>

<h1>Calendrier en tant que valeur de retour</h1>
<?php
$html_code = Calendar(array("PREFIX" => "cal4_", "PRESERVE_URL" => false, "OUTPUT_MODE" => "return", "DATE_URL" => "target/target.php"));
// Commenter la ligne suivante fait "disparaitre" le calendrier
echo $html_code;
?>

</body>
</html>
