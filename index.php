<?php
/**
 * Fichier d'index
 * 
 * Appelle les autres fichiers
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
//Debug mode ?
error_reporting(E_ERROR);

if (isset($_SERVER['HTTP_REFERER'])
    && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) == 'm.archi-strasbourg.org'
) {
    setcookie('nomobile');
} else if (!isset($_COOKIE['nomobile'])) {
    include_once 'includes/Mobile_Detect.php';
    $detect = new Mobile_Detect();
    if ($detect->isMobile() && !$detect->isTablet()) {
        if (isset($_GET['archiIdEvenementGroupeAdresse'])) {
            header(
                'Location: http://m.archi-strasbourg.org/adresses/'.
                $_GET['archiIdAdresse'].'/'.
                $_GET['archiIdEvenementGroupeAdresse'].'.html'
            );
        } else {
            header('Location: http://m.archi-strasbourg.org/');
        }
    }
}

/* 'noHeaderNoFooter' permet de ne pas retourner
 * et inclure les fichiers qui sont inutiles lors d'un appel ajax
 * */
 
session_start();

date_default_timezone_set('Europe/Paris');

$microstart=microtime(true);

/*a l'appel des différentes fonctions qui retourne du javascript,
 * on va placer ce javascript dans le header ou le footer suivant les cas,
 * ce qui évite d'en avoir trop en plein milieu de la page
 * et encourage la creation de fonctions javascript 
 * plutot que de mettre beaucoup de code dans les tags onclick ou onmouseover etc
 * */
$jsHeader=""; 
$jsFooter="";

//include('/home/pia/pear/PEAR/PEAR.php');
//include('/home/pia/pear/PEAR/HTML/BBCodeParser.php');

//Pas la peine de charger toutes les classes à chaque fois
require_once "includes/framework/autoload.php";

require 'includes/framework/config.class.php';

//Traduction
require 'includes/framework/localization.php';

$config = new config();
require_once 'includes/securimage/securimage.php'; // gestion du captcha

/*include('modules/archi/includes/archiEvenement.class.php');
include('modules/archi/includes/archiImage.class.php');
include('modules/archi/includes/archiAdresse.class.php');
include('modules/archi/includes/archiAuthentification.class.php');
include('modules/archi/includes/archiCourantArchitectural.class.php');
include('modules/archi/includes/archiPersonne.class.php');
include('modules/archi/includes/archiSource.class.php');
include('modules/archi/includes/archiRecherche.class.php');
include('modules/archi/includes/archiUtilisateur.class.php');
include('modules/archi/includes/archiStatic.class.php');
include('modules/archi/includes/archiAccueil.class.php');
include('modules/archi/includes/archiAdministration.class.php');
* */




//
//     Lancement du module principal
//
ob_start();
if (isset($_GET['module'])) {
    include 'modules/'.$_GET['module'].'/index.php';
} else {
    include 'modules/archi/index.php';
}
$htmlModule = ob_get_contents();
ob_end_clean();


//
//     HEADER
//
ob_start();
if (!isset($_GET['noHTMLHeaderFooter'])) {
    if (!isset($_GET["noHeaderNoFooter"]) && !isset($_POST["noHeaderNoFooter"])) {
        $headerJS = "";
        if (config::getJsHeader()!='') {
            $headerJS = config::getJsHeader();
        }
        include 'modules/header/index.php';
    } else {
        $headerJS = "";
        if (config::getJsHeader()!='') {
            $headerJS = config::getJsHeader();
        }
        $ajaxObj = new ajaxObject();
        ?>
        <html>
        <head>
        <link href="css/default.css" rel="stylesheet" type="text/css" />
        <script type='text/javascript' src='includes/datePicker.js'></script>
        <script type='text/javascript' src='includes/bbcode.js'></script>
        <?php  echo $ajaxObj->getAjaxFunctions(); ?>
        <script type='text/javascript' src='includes/common.js'></script>
        <?php  echo $headerJS; ?>
        </head>
        <body>
        <?php
    }
}
$htmlHeader = ob_get_contents();
ob_end_clean();

echo $htmlHeader;
echo $htmlModule;
if (!isset($_GET['noHTMLHeaderFooter'])) {
    if (!isset($_GET["noHeaderNoFooter"]) && !isset($_POST["noHeaderNoFooter"])) {
        $footerJS = "";
        if (config::getJsFooter()!='') {
            $footerJS = config::getJsFooter();
        }
        include 'modules/footer/index.php';
    } else {
        $footerJS = "";
        if (config::getJsFooter()!='') {
            $footerJS = config::getJsFooter();
        }
        ?>
        <?php echo $footerJS; 
        
        
        if (!isset($config->isSiteLocal) || $config->isSiteLocal==false) {
            echo "<script type='text/javascript' src='js/analytics.js'></script>";
        }
        
        ?>
        </body>
        </html><?php
    }
}
/* Du HTML après le </body>, eurk !
$fin_compte=microtime(true);
$duree=($fin_compte-$microstart);
$authDebug = new archiAuthentification();
if (!isset($_GET['noHTMLHeaderFooter'])) {
    if ($authDebug->estAdmin()) {
        echo '<br><br>Page g&eacute;n&eacute;r&eacute;e en '.
        substr($duree, 0, 5).' sec.';
    }
}*/
?>
