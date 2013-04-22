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
if (isset($_POST["title-".$config->langs[0]])) {
    if (isset($_GET["new"])) {
        $page=new archiPage();
    } else {
        $page=new archiPage($_GET["idPage"], $lang);
    }
    $page->menu=isset($_POST["menu"])?1:0;
    $page->footer=isset($_POST["footer"])?1:0;
    if (isset($_GET["new"])) {
        foreach ($config->langs as $lang) {
            if (isset($id)) {
                $page->add(
                    $_POST["title-".$lang], $_POST["content-".$lang],
                    $page->menu,  $page->footer, $lang, $id
                );
            } else {
                $page->add(
                    $_POST["title-".$lang], $_POST["content-".$lang],
                    $page->menu,  $page->footer, $lang
                );
                $id=mysql_insert_id();
            }
        }
        header("Location: ".$config->creerUrl("", "adminPages"));
    } else {
        foreach ($config->langs as $lang) {
            $page->update(
                $_POST["title-".$lang], $_POST["content-".$lang],
                $page->menu,  $page->footer, $lang
            );
        }
    }
}

echo "<script src='includes/framework/frameworkClasses/tiny_mce/tiny_mce.js'>
</script>";
echo "<script src='js/tinyMCE.js'></script>";
echo "<a class='right' href='".$config->creerUrl("", "adminPages")."'>".
_("Retour")."</a>";
echo "<h2>"._("Edition :")." ".$page->title."</h2>";
echo "<form method='POST'>";
foreach ($config->langs as $lang) {
    if (isset($_GET["new"])) {
        $page=new archiPage();
    } else {
        $page=new archiPage($_GET["idPage"], $lang);
    }
    echo "<h3>".$lang."</h3><br/><br/>";
    echo "<label for='title'>"._("Titre :").
    "</label> <input name='title-".$lang."' id='title' value='".
    htmlspecialchars($page->title, ENT_QUOTES)."'/><br/><br/>";
    echo "<label for='tinyMCE-".$lang."'>"._("Contenu :")."</label><br/>"; 
    echo "<textarea id='tinyMCE-".$lang."' name='content-".$lang."'>".
    stripslashes($page->content)."</textarea><br/>";
    echo '<hr/>';
}
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
