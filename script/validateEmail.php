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
require_once __DIR__.'/../modules/archi/includes/archiUtilisateur.class.php';
require_once __DIR__.'/../modules/archi/includes/archiAdresse.class.php';
require_once __DIR__.'/../modules/archi/includes/archiAuthentification.class.php';

$config = new config();
$req = "UPDATE commentaires SET CommentaireValide=1 WHERE uniqid='".
    mysql_real_escape_string($_GET['uniqid'])."';";
$res = $config->connexionBdd->requete($req);

$req = "SELECT nom, prenom, email, commentaire, idEvenementGroupeAdresse FROM commentaires WHERE uniqid='".
    mysql_real_escape_string($_GET['uniqid'])."';";
$res = $config->connexionBdd->requete($req);
$fetch = mysql_fetch_assoc($res);
if ($fetch) {
    $u = new archiUtilisateur();
    $a = new archiAdresse();
    $idAdresse = $a->getIdAdresseFromIdEvenementGroupeAdresse($fetch['idEvenementGroupeAdresse']);
    $intituleAdresse = $a->getIntituleAdresseFrom($idAdresse, 'idAdresse');
    $message="Un utilisateur a ajouté un commentaire sur archiV2 : <br>";
    $message .= "nom ou pseudo : ".strip_tags($fetch['nom'])."<br>";
    $message .= "prenom : ".strip_tags($fetch['prenom'])."<br>";
    $message .= "email : ".strip_tags($fetch['email'])."<br>";
    $message .= "commentaire : ".stripslashes(strip_tags($fetch['commentaire']))."<br>";
    $message .="<a href='".$config->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGroupeAdresse'],'archiIdAdresse'=>$idAdresse))."'>".$intituleAdresse."</a><br>";
    $mail = new mailObject();
    $envoyeur['envoyeur'] = $mail->getSiteMail();
    $envoyeur['replyTo'] = strip_tags($fetch['email']);
    $mail->sendMailToAdministrators($envoyeur,'Un utilisateur a ajouté un commentaire', $message, " AND alerteCommentaires='1' ", true, true);
}
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
