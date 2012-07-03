<?php
/**
 * Affiche une page de contenu
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
$page=new archiPage($_GET["idPage"], LANG);
if (empty($page->title)) { 
    $page=new archiPage($_GET["idPage"], Config::$default_lang);
}
echo "<article itemscope itemtype='http://schema.org/WebPage'>";
echo "<meta itemprop='inLanguage' content='".substr($page->lang, 0, 2)."' />";
echo "<h1 itemprop='name'>".$page->title."</h1>";
echo "<p itemprop='text'>".stripslashes($page->content)."</p>";


echo "</article>";
?>
