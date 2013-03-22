<?php
/**
 * Classe ArchiPage
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
/**
 * Gère l'affichage des pages de texte
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
class ArchiPage
{
    
    /**
     * Get the list of all pages
     * 
     * @param string $lang Langue
     * 
     * @return array
     * */
    static function getList ($lang)
    {
        global $config;
        $query = sprintf(
            file_get_contents('sql/getListPages.sql'),
            mysql_real_escape_string($lang)
        );
        $query=$config->connexionBdd->requete($query);
        $result=array();
        while ($row = mysql_fetch_assoc($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    /**
     * Get the list of pages in menu
     * 
     * @param string $lang Langue
     * 
     * @return array
     * */
    static function getListMenu ($lang)
    {
        global $config;
        $query = sprintf(
            file_get_contents('sql/getListPagesMenu.sql'),
            mysql_real_escape_string($lang)
        );
        $query=$config->connexionBdd->requete($query);
        $result=array();
        while ($row = mysql_fetch_assoc($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    /**
     * Get the list of pages in footer
     * 
     * @param string $lang Langue
     * 
     * @return array
     * */
    static function getListFooter ($lang)
    {
        global $config;
        $query = sprintf(
            file_get_contents('sql/getListPagesFooter.sql'),
            mysql_real_escape_string($lang)
        );
        $query=$config->connexionBdd->requete($query);
        $result=array();
        while ($row = mysql_fetch_assoc($query)) {
            array_push($result, $row);
        }
        return $result;
    }
    
    /**
     * Constructeur d'ArchiPage
     * 
     * @param int    $id   ID de la page
     * @param string $lang Langue
     * 
     * @return void
     * */
    function __construct ($id=null, $lang=null)
    {
        global $config;
        $query = sprintf(
            file_get_contents("sql/getPage.sql"),
            mysql_real_escape_string($id),
            mysql_real_escape_string($lang)
        );
        $result=mysql_fetch_assoc($config->connexionBdd->requete($query));
        $this->title=$result["title"];
        $this->content=$result["text"];
        $this->lang=$result["lang"];
        $this->menu=$result["menu"];
        $this->footer=$result["footer"];
        $this->id=$id;
    }
    
    /**
     * Mettre à jour une page
     * 
     * @param string $title  Titre
     * @param string $text   Contenu
     * @param bool   $menu   Afficher dans le menu ?
     * @param bool   $footer Afficher dans le pied de page ?
     * @param string $lang   Langue
     * 
     * @return void
     * */
    function update ($title, $text, $menu, $footer, $lang)
    {
        global $config;
        $this->title=$title;
        $this->content=$text;
        $query = sprintf(
            file_get_contents("sql/updatePage.sql"),
            mysql_real_escape_string($title),
            mysql_real_escape_string($text), mysql_real_escape_string($menu),
            mysql_real_escape_string($footer), $lang, $this->id
        );
        $config->connexionBdd->requete($query);
    }
    
    /**
     * Ajouter une page
     * 
     * @param string $title  Titre
     * @param string $text   Contenu
     * @param bool   $menu   Afficher dans le menu ?
     * @param bool   $footer Afficher dans le pied de page ?
     * @param string $lang   Langue
     * @param int    $id     Identifiant
     * 
     * @return void
     * */
    function add ($title, $text, $menu, $footer, $lang, $id=null)
    {
        global $config;
        $this->title=$title;
        $this->content=$text;
        if (isset($id)) {
            $query = sprintf(
                file_get_contents("sql/addPageID.sql"),
                mysql_real_escape_string($title),
                mysql_real_escape_string($text), mysql_real_escape_string($menu),
                mysql_real_escape_string($footer), $lang, $id
            );
        } else {
            $query = sprintf(
                file_get_contents("sql/addPage.sql"),
                mysql_real_escape_string($title),
                mysql_real_escape_string($text), mysql_real_escape_string($menu),
                mysql_real_escape_string($footer), $lang
            );
        }
        $config->connexionBdd->requete($query);
    }
}
?>
