<?php
/**
 * Récuperation du fichier a partir de la liste et du repertoire identifié par iddossier
 * Recherche de la date dans la base de donnee archiv2,  enregistrements dans les repertoires en redimensionnant avec comme nom idHistoriqueImage 
 * 
 * PHP Version 5.3.3
 * 
 * @category Script
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * */

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

ini_set('max_execution_time',  0);
require_once 'PEAR.php';
require_once 'HTML/BBCodeParser.php';
//include('/home/pia/archiv2/includes/framework/config.class.php');


//$borneMin = "NOW()";
//$borneMax = "SUBDATE(NOW(),  INTERVAL 7 DAY)";

//$borneMin = "SUBDATE(NOW(), INTERVAL 7 DAY)";
//$borneMax = "SUBDATE(NOW(),  INTERVAL 14 DAY)";

require_once __DIR__.'/../includes/framework/config.class.php';
require_once __DIR__.'/../modules/archi/includes/archiAdresse.class.php';
require_once __DIR__.'/../modules/archi/includes/archiEvenement.class.php';
    


$config = new config();
$d = new dateObject();
$mail = new mailObject();

$idPeriode = "";
if ((isset($argv[1]) && $argv[1]!='') || (isset($_GET['idPeriode']) && $_GET['idPeriode']!='')) {
    if(isset($argv[1]) && $argv[1]!='' && $argv[1]!='0' && $argv[1]!='1')
        $idPeriode = trim($argv[1]);
    if(isset($_GET['idPeriode']) && $_GET['idPeriode']!='' && $_GET['idPeriode']!='0' && $_GET['idPeriode']!='1')
        $idPeriode = $_GET['idPeriode'];
    
    
    // recuperation des mails
    // on envoi aussi au personnes qui ont une periode immediate 0 ou 1,  car s'ils y a des messages regroupés en attente pour eux ,  ca veut dire qu'ils on changé la periode entre temps (cela sert de purge)
    $req = "
    SELECT m.idMail as idMail, m.dateHeure as dateHeure, m.idUtilisateur as idUtilisateur,  m.contenu as contenu, m.idTypeMailRegroupement as idTypeMailRegroupement
    FROM mailsEnvoiMailsRegroupes m
    LEFT JOIN utilisateur u ON u.idUtilisateur = m.idUtilisateur
    WHERE
        (u.idPeriodeEnvoiMailsRegroupes = '".$idPeriode."'
        OR u.idPeriodeEnvoiMailsRegroupes='1'
        OR u.idPeriodeEnvoiMailsRegroupes='0')
        
    ORDER BY m.dateHeure DESC
    ";//AND u.idUtilisateur='30'
    
    
    $res = $config->connexionBdd->requete($req);
    $arrayRegroupementTypeMail = array();
    // regroupement pas utilisateur et par type de mail (nouvelle image ,  modif evenement etc)
    while ($fetch = mysql_fetch_assoc($res)) {
        if(!isset($arrayRegroupementTypeMail[$fetch['idUtilisateur']]))
            $arrayRegroupementTypeMail[$fetch['idUtilisateur']] = array();
        
        if(!isset($arrayRegroupementTypeMail[$fetch['idUtilisateur']][$fetch['idTypeMailRegroupement']]))
            $arrayRegroupementTypeMail[$fetch['idUtilisateur']][$fetch['idTypeMailRegroupement']] = array();
        
        $arrayRegroupementTypeMail[$fetch['idUtilisateur']][$fetch['idTypeMailRegroupement']][] = array(
                                                'idUtilisateur'=>$fetch['idUtilisateur'], 
                                                'contenu'=>$fetch['contenu'], 
                                                'dateHeure'=>$fetch['dateHeure'], 
                                                'idTypeMailRegroupement'=>$fetch['idTypeMailRegroupement'], 
                                                'idMail'=>$fetch['idMail']
                                                );
    }
    
    
    foreach ($arrayRegroupementTypeMail as $idUtilisateur => $valueTypeMailRegroupement) {
        $arrayMailsASupprimer= array();
        $message= "<b>Modifications apportées sur le site archi-strasbourg.org</b><br><br>";
        foreach ($valueTypeMailRegroupement as $idTypeMailRegroupement => $valueMail) {
            // recup de l'intitule de la rubrique de mail regroupee
            $reqIntituleRegroupement = "SELECT intitule FROM typesMailsEnvoiMailsRegroupes WHERE idTypeMail = '".$idTypeMailRegroupement."'";
            $resIntituleRegroupement = $config->connexionBdd->requete($reqIntituleRegroupement);
            $fetchIntituleRegroupement = mysql_fetch_assoc($resIntituleRegroupement);
            $message.="<b>".$fetchIntituleRegroupement['intitule']."</b> : <br>";
            
            foreach ($valueMail as $indice => $value) {
                $message.=" - <i>".$d->toFrenchAffichage($value['dateHeure'])." :</i> ".$value['contenu']."<br>";
                $arrayMailsASupprimer[] = $value['idMail'];
            }
        }
        $message.="<br>".$config->getMessageDesabonnerAlerteMail();
        // recup du mail de la personne
        $reqMail = "SELECT mail FROM utilisateur WHERE idUtilisateur='".$idUtilisateur."'";
        $resMail = $config->connexionBdd->requete($reqMail);
        $fetchMail = mysql_fetch_assoc($resMail);
        $sujet = "archi-strasbourg.org : Modifications sur le site";
        $mail->sendMail($mail->getSiteMail(), trim($fetchMail['mail']), $sujet, $message);
        //$mail->sendMail($mail->getSiteMail(), "fabien.romary@gmail.com", "maintenance archiv2 envoi mail regroupe pour ".trim($fetchMail['mail']), $reqMail);

        // Stockage du mail dans les logs
        $reqStock = "INSERT INTO logMails (destinataire, sujet, message, date) VALUES ('".trim($fetchMail['mail'])."', \"".mysql_real_escape_string($sujet)."\", \"".mysql_real_escape_string($message)."\", now())";
        $resStock = $config->connexionBdd->requete($reqStock);
        
        // On supprime les mail regroupés 
        if (count($arrayMailsASupprimer)>0) {
            $reqMails = "DELETE FROM mailsEnvoiMailsRegroupes WHERE idMail IN (".implode(", ", $arrayMailsASupprimer).")";
            $resMails = $config->connexionBdd->requete($reqMails);
            
            //$mail->sendMail($mail->getSiteMail(), "laurent.dorer@gmail.com", "maintenance archiv2", $reqMails);
        } else {
            $mail->sendMail($mail->getSiteMail(), "fabien.romary@gmail.com", "maintenance archiv2 probleme", "mail vide envoyé ?");
        }
        
        
        
    }
    
    
    
    
    
}
/*
$contenu = "test recuperation arguments et cron";
$sujet = "test laurent cron params = ".$param;

$mail->sendMail($mail->getSiteMail(), "laurent_dorer@yahoo.fr", $sujet, $contenu);
$mail->sendMail($mail->getSiteMail(), "laurent.dorer@ri67.fr", $sujet, $contenu);
*/
/*
$messageFin="<br>L'équipe archi-strasbourg.org<br>";
    $messageFin.=$config->getMessageDesabonnerAlerteMail();

    
    $messageHTML = $messageIntro.$messageStrasbourg.$messageStrasModif.$messageAutres.$messageAutresModif.$messageFin;
    
    
    $sujet = "Nouvelles adresses et adresses modifiées sur archi-strasbourg.org.";

    
    $reqUtilisateurs = "SELECT idUtilisateur, mail FROM utilisateur WHERE alerteMail='1' and compteActif='1'";
    $resUtilisateurs = $config->connexionBdd->requete($reqUtilisateurs);

       $mail->sendMail($mail->getSiteMail(), "laurent.dorer@ri67.fr", $sujet, $messageHTML, true);
       $mail->sendMail($mail->getSiteMail(), "laurent.dorer@gmail.com", $sujet, $messageHTML, true);

    if(isset($_GET['debug']) && $_GET['debug']=='1')
    {
        $mail->sendMail($mail->getSiteMail(), "laurent.dorer@ri67.fr", $sujet, $messageHTML, true);
        $mail->sendMail($mail->getSiteMail(), "laurent.dorer@gmail.com", $sujet, $messageHTML, true);
    }
    else
    {
        while($fetchUtilisateurs = mysql_fetch_assoc($resUtilisateurs))
        {
            $mail->sendMail($mail->getSiteMail(), $fetchUtilisateurs['mail'], $sujet, $messageHTML, true); // $fetchUtilisateurs['mail']
        }
    }
*/
//}

?>
