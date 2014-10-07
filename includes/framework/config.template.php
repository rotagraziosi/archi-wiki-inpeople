<?php
/**
 * Paramètres du site
 * 
 * PHP Version 5.3.3
 * 
 * @category Config
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
$this->bdd_host     = "localhost";
$this->bdd_user     = "";
$this->bdd_password = "";
$this->bdd_name     = "archi-strasbourg";

// Pour l'identification unique d'un utilisateur dans la session
$this->idSite = ""; 

//Mot de passe universel
$this->adminPass=="";

$this->mail     = "contact@archi-strasbourg.org";
$this->authorLink = "https://plus.google.com/105153588863548583248/posts";
$this->tradLink = "http://archi-strasbourg.org:8080/projects/archi/";

$this->titreSite = "Archi-Strasbourg.org";
$this->descSite=_("Architecture, photos et patrimoine de Strasbourg.");
$this->langs=array('fr_FR', 'de_DE', 'en_US');

//Pour le plugin reCaptcha
$this->captchakey='';

?>
