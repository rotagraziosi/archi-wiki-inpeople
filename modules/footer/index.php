<?php
/**
 * Charge le template du pied-de-page
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
$t=new Template('modules/footer/templates/');

$t->set_filenames((array('footer'=>'footer.tpl')));

if ($footerJS!='') {
    $t->assign_vars(array('jsFooter'=>$footerJS));
}



/*if(!isset($config->isSiteLocal) || $config->isSiteLocal==false)
{

	$t->assign_vars(array('googleAnalytics'=>"<script type=\"text/javascript\">
var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
</script>
<script type=\"text/javascript\">
try {
var pageTracker = _gat._getTracker(\"UA-16282574-3\");
pageTracker._trackPageview();
} catch(err) {}</script>"));
}*/


$listPages=archiPage::getListFooter();
$htmlListPages="";
foreach ($listPages as $page) {
    $htmlListPages.="<li><a href='index.php?archiAffichage=page&idPage=".$page["id"]."'>".$page["title"]."</a></li>";
}
$t->assign_vars(array("listPages"=>$htmlListPages, "faq"=>$config->creerUrl("", "faq"), "contact"=>$config->creerUrl("", "contact")));


ob_start();
$t->pparse('footer');
$html=ob_get_contents();
ob_end_clean();

echo $html;

?>
