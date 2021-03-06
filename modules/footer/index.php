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



$listPages=archiPage::getListFooter(LANG);
$htmlListPages="";
foreach ($listPages as $page) {
    $htmlListPages.="<li><a href='index.php?archiAffichage=page&idPage=".
    $page["id"]."'>".$page["title"]."</a></li>";
}
$t->assign_vars(
    array("listPages"=>$htmlListPages, "faq"=>$config->creerUrl("", "faq"),
    "contact"=>$config->creerUrl("", "contact"))
);


ob_start();
$t->pparse('footer');
$html=ob_get_contents();
ob_end_clean();

$footer = $html;
//echo $html;

?>
