<?php
/**
 * Gestion des pages
 * 
 * PHP Version 5.3.3
 * 
 * @category Admin
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
echo "<h2>"._("Gestion des pages")."</h2>";
$listPages=archiPage::getList();
$htmlListPages="";
echo "<ul>";
foreach ($listPages as $page) {
    echo "<li><a href='index.php?archiAffichage=editPage&idPage=".
    $page["id"]."&langPage=".$page["lang"]."'>".$page["title"].
    "</a></li>";
}
echo "</ul>";
echo "<a href='index.php?archiAffichage=editPage&new=".true."'>".
_("Ajouter une page")."</a>";
?>
