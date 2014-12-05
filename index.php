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
$countTest=0;

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
require_once "includes/framework/autoload.php";
require 'includes/framework/config.class.php';
require 'includes/framework/localization.php';//Traduction
require_once 'includes/securimage/securimage.php'; // gestion du captcha

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
$ajaxObj = new ajaxObject();
$config = new config();


ob_start();
if (isset($_GET['module'])) {
    include 'modules/'.$_GET['module'].'/index.php';
} else {
    include 'modules/archi/index.php';
}
$htmlModule = ob_get_contents();
ob_end_clean();

$analyticsJSvar ='';
$footerJS = "";

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
    }
}
$htmlHeader = ob_get_contents();
ob_end_clean();

if (!isset($_GET['noHTMLHeaderFooter'])) {
    if (!isset($_GET["noHeaderNoFooter"]) && !isset($_POST["noHeaderNoFooter"])) {
        if (config::getJsFooter()!='') {
            $footerJS = config::getJsFooter();
        }
        include 'modules/footer/index.php';
    } else {
        $footerJS = "";
        if (config::getJsFooter()!='') {
            $footerJS = config::getJsFooter();
        }
        
        
        if (!isset($config->isSiteLocal) || $config->isSiteLocal==false) {
			$analyticsJSvar =  "<script type='text/javascript' src='js/analytics.js'></script>";
        }
    }
}


$t = new Template('modules/archi/templates/general/');
$t->set_filenames((array('template'=>'template.tpl')));
$ajaxObj = new ajaxObject();
$t->assign_vars(array(
	'ajaxFunctions'=> $ajaxObj->getAjaxFunctions(),
	'headerJS'=> $headerJS,
	'analyticsJS' => $analyticsJSvar,
	'htmlHeader'=> $htmlHeader,
	'htmlModule' =>$htmlModule,
	'header' => $header,
	'content'=>'',
	'footer'=>$footer

));

ob_start();
$t->pparse('template');
$page = ob_get_contents();
ob_end_clean();
echo $page;
?>
