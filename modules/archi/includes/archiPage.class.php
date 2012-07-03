<?php
/**
 * Classe ArchiPage
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
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
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
class ArchiPage
{
    
    /**
     * Get the list of all pages
     * 
     * @return array
     * */
    static function getList ()
    {
        return connexionBdd::getResultFromFile("sql/getListPages.sql");
    }
    
    /**
     * Get the list of pages in menu
     * 
     * @return array
     * */
    static function getListMenu ()
    {
        return connexionBdd::getResultFromFile("sql/getListPagesMenu.sql");
    }
    
    /**
     * Get the list of pages in footer
     * 
     * @return array
     * */
    static function getListFooter ()
    {
        return connexionBdd::getResultFromFile("sql/getListPagesFooter.sql");
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
     * 
     * @return void
     * */
    function update ($title, $text, $menu, $footer)
    {
        global $config;
        $this->title=$title;
        $this->content=$text;
        $query = sprintf(
            file_get_contents("sql/updatePage.sql"),
            mysql_real_escape_string($title),
            mysql_real_escape_string($text), mysql_real_escape_string($menu),
            mysql_real_escape_string($footer), $this->id
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
     * 
     * @return void
     * */
    function add ($title, $text, $menu, $footer)
    {
        global $config;
        $this->title=$title;
        $this->content=$text;
        $query = sprintf(
            file_get_contents("sql/addPage.sql"), mysql_real_escape_string($title),
            mysql_real_escape_string($text), mysql_real_escape_string($menu),
            mysql_real_escape_string($footer)
        );
        $config->connexionBdd->requete($query);
    }
}
?>
