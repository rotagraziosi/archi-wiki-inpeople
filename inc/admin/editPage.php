<?php
/**
 * Edition/ajout d'une page
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
if (isset($_GET["new"])) {
    $page=new archiPage();
} else {
    $page=new archiPage($_GET["idPage"], $_GET["langPage"]);
}
if (isset($_POST["title"])) {
    $page->menu=isset($_POST["menu"])?1:0;
    $page->footer=isset($_POST["footer"])?1:0;
    if (isset($_GET["new"])) {
        $page->add($_POST["title"], $_POST["content"], $page->menu,  $page->footer);
        header("Location: ".$config->creerUrl("", "adminPages"));
    } else {
        $page->update(
            $_POST["title"], $_POST["content"], $page->menu,  $page->footer
        );
    }
}

echo "<script src='includes/framework/frameworkClasses/tiny_mce/tiny_mce.js'>
</script>";
echo "<script src='js/tinyMCE.js'></script>";
echo "<a class='right' href='".$config->creerUrl("", "adminPages")."'>".
_("Retour")."</a>";
echo "<h2>"._("Edition :")." ".$page->title."</h2>";
echo "<form method='POST'>";
echo "<label for='title'>"._("Titre :").
"</label> <input name='title' id='title' value='".$page->title."'/><br/><br/>";
echo "<label for='tinyMCE'>"._("Contenu :")."</label><br/>"; 
echo "<textarea id='tinyMCE' name='content'>".
stripslashes($page->content)."</textarea><br/>";
echo "<input type='checkbox' id='menu' name='menu'";
if ($page->menu) {
    echo "checked='checked'";
}
echo "/><label for='menu'>"._("Afficher dans le menu")."</label><br/>";
echo "<input type='checkbox' id='footer' name='footer'";
if ($page->footer) {
    echo "checked='checked'";
}
echo "/><label for='footer'>"._("Afficher dans le pied-de-page")."</label><br/>";
echo "<br/><input type='submit' />";
echo "</form>";
?>
