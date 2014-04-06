<?php
/**
 * Valide les commentaires grace à un identifiant unique
 * 
 * PHP Version 5.3.3
 * 
 * @category Script
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * */
require_once __DIR__.'/../includes/framework/config.class.php';    

$config = new config();
$req = "UPDATE commentaires SET CommentaireValide=1 WHERE uniqid='".
    mysql_real_escape_string($_GET['uniqid'])."';";
$res = $config->connexionBdd->requete($req);

$req = "SELECT idEvenementGroupeAdresse FROM commentaires WHERE uniqid='".
    mysql_real_escape_string($_GET['uniqid'])."';";
$res = $config->connexionBdd->requete($req);
$fetch = mysql_fetch_assoc($res);

header(
    'Location: '.html_entity_decode(
        $config->creerUrl(
            '', 'adresseDetail',
            array(
                'archiIdEvenementGroupeAdresse'=>$fetch["idEvenementGroupeAdresse"]
            )
        )
    )
);
?>
