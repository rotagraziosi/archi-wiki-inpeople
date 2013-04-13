<?php
/**
 * Recuperation du fichier a partir de la liste et du repertoire identifié par iddossier
 * Recherche de la date dans la base de donnee archiv2, enregistrements dans les repertoires en redimensionnant avec
 * comme nom idHistoriqueImage
 * 
 * PHP Version 5.3.3
 * 
 * @category Script
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
if (isset($_GET["testMail"])) {
    session_start();
}
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
ini_set('max_execution_time', 0);
require 'PEAR.php';
require 'HTML/BBCodeParser.php';

//include('/home/pia/archiv2/includes/framework/config.class.php');
$borneMin = "NOW()";
$borneMax = "SUBDATE(NOW(), INTERVAL 7 DAY)";

//$borneMin = "SUBDATE(NOW(),INTERVAL 7 DAY)";
//$borneMax = "SUBDATE(NOW(), INTERVAL 14 DAY)";

//  include('/home/pia/archiv2/includes/framework/config.class.php');
require __DIR__.'/../includes/framework/config.class.php';

//  include_once('/home/pia/archiv2/modules/archi/includes/archiAdresse.class.php');
require_once __DIR__.'/../modules/archi/includes/archiAdresse.class.php';

//  include_once('/home/pia/archiv2/modules/archi/includes/archiEvenement.class.php');
require_once __DIR__.'/../modules/archi/includes/archiEvenement.class.php';
require_once __DIR__.'/../modules/archi/includes/archiPersonne.class.php';

$config = new config();

// on recherche que les adresses un evenement est associe
$reqNouvellesAdressesDeLaSemaine = "
                    SELECT ha1.idAdresse as idAdresse, count(ee.idEvenementAssocie),ha1.date as date, v.nom as nomVille, ae.idEvenement as idEvenementGroupeAdresse
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    
                    LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    LEFT JOIN historiqueEvenement he ON he.idEvenement = ae.idEvenement
                    
                    
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                            
                    
                    AND he.dateCreationEvenement < $borneMin
                    AND he.dateCreationEvenement >= $borneMax
                    GROUP BY ha1.idAdresse,ee.idEvenement, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and count(ee.idEvenementAssocie)>0
                    ORDER BY nomVille,date
            ";
$resNouvellesAdressesDeLaSemaine = $config->connexionBdd->requete($reqNouvellesAdressesDeLaSemaine);
$a                               = new archiAdresse();
$e                               = new archiEvenement();
$arrayAdresses                   = array();
$arrayListeAdresses              = array();

// contient la liste des adresses pour ne pas afficher la meme adresse dans la liste des adresse modifiees
$arrayGroupeEvenements = array();
while ($fetchNouvellesAdresses = mysql_fetch_assoc($resNouvellesAdressesDeLaSemaine)) {
    $arrayListeAdresses[] = $fetchNouvellesAdresses['idAdresse'];

    //$resEvenementGroupeAdresse = $a->getIdEvenementGroupeAdresseFromAdresse($fetchNouvellesAdresses['idAdresse']);
    //$idEvenementGroupeAdresse = mysql_fetch_assoc($resEvenementGroupeAdresse);
    if (!in_array($fetchNouvellesAdresses['idEvenementGroupeAdresse'], $arrayGroupeEvenements)) {
        $arrayGroupeEvenements[] = $fetchNouvellesAdresses['idEvenementGroupeAdresse'];
        $arrayAdresses[$fetchNouvellesAdresses['nomVille']][] = array("idAdresse"=>$fetchNouvellesAdresses['idAdresse'], "libelle"=>$a->getIntituleAdresseFrom($fetchNouvellesAdresses['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse', array('displayFirstTitreAdresse'=>true)), "url"=>$config->creerUrl('', '', array('archiAffichage'=>'adresseDetail', 'archiIdAdresse'=>$fetchNouvellesAdresses['idAdresse'], 'archiIdEvenementGroupeAdresse'=>$fetchNouvellesAdresses['idEvenementGroupeAdresse'])), 'idEvenementGroupeAdresse'=>$fetchNouvellesAdresses['idEvenementGroupeAdresse']);
    }
}

// recherche des adresses dont des evenements ont ete modifiés dans la semaine passée
// recherche des evenements créés pendant la semaine, ensuite on verifiera lequels sont des mises a jour
$reqEvenementsCrees     = "
    SELECT distinct he1.idEvenement as idEvenement
    from historiqueEvenement he1
    WHERE 1=1
    AND he1.dateCreationEvenement < $borneMin
    AND he1.dateCreationEvenement >= $borneMax
";
$resEvenementsCrees     = $config->connexionBdd->requete($reqEvenementsCrees);
$arrayEvenementsCrees   = array();
$arrayAdressesModifiees = array();
while ($fetchEvenementsCrees = mysql_fetch_assoc($resEvenementsCrees)) {
    $reqVerif = "SELECT idHistoriqueEvenement FROM historiqueEvenement WHERE idEvenement = '".$fetchEvenementsCrees['idEvenement']."'";
    $resVerif = $config->connexionBdd->requete($reqVerif);
    if (mysql_num_rows($resVerif)>1) {

        // il y a eu au moins une mise a jour et celle ci a ete effectuée cette semaine 
        $arrayEvenementsCrees[] = $fetchEvenementsCrees['idEvenement'];
    }
}

// idem pour les images :
$reqImagesCrees    = "
    SELECT distinct idImage 
    FROM historiqueImage 
    WHERE dateUpload < $borneMin
    AND dateUpload >= $borneMax
";
$resImagesCrees    = $config->connexionBdd->requete($reqImagesCrees);
$arrayImagesCreees = array();
while ($fetchImagesAjoutees = mysql_fetch_assoc($resImagesCrees)) {
    // si on ajoute une image , c'est comme si on modifie l'evenement , donc je ne fais pas le test pour voir s'il y a un historique sur l'image
    /*$reqVerif = "SELECT idHistoriqueImage FROM historiqueImage WHERE idImage = '".$fetchImagesAjoutees['idImage']."'";
    $resVerif = $config->connexionBdd->requete($reqVerif);
    if(mysql_num_rows($resVerif)>1) // il y a eu au moins une mise a jour et celle ci a ete effectuée cette semaine
    {*/
    $arrayImagesCreees[] = $fetchImagesAjoutees['idImage'];

    // recherche de l'evenement concerné
    $reqEvenementImage = "SELECT idEvenement FROM _evenementImage WHERE idImage = '".$fetchImagesAjoutees['idImage']."'";
    $resEvenementImage = $config->connexionBdd->requete($reqEvenementImage);
    if (mysql_num_rows($resEvenementImage)>0) {
        $fetchEvenementImage = mysql_fetch_assoc($resEvenementImage);

        // on merge a la liste des evenements crees (modifiés)
        $arrayEvenementsCrees[] = $fetchEvenementImage['idEvenement'];
    }

    //}
}
$arrayEvenementsCrees = array_unique($arrayEvenementsCrees);
if (count($arrayEvenementsCrees)>0) {
    // recherche de l'adresse correspondante
    $reqAdresseEvenementsCrees = "
            SELECT distinct ha1.idAdresse as idAdresse, v.nom as nomVille,ee.idEvenementAssocie,ae.idEvenement as idEvenementGroupeAdresse
            FROM  historiqueAdresse ha2, historiqueAdresse ha1
            RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
            RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement AND ee.idEvenementAssocie IN (".implode(",", $arrayEvenementsCrees).")
            
            LEFT JOIN rue r         ON r.idRue = ha1.idRue
            LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
            LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
            LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
            LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
            
            WHERE 
                1=1
                AND ha2.idAdresse = ha1.idAdresse   
            GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            ORDER BY v.nom
        ";
    $resAdresseEvenementsCrees = $config->connexionBdd->requete($reqAdresseEvenementsCrees);
    $arrayListeEvGA            = array();
    while ($fetchAdresseEvenementsCrees = mysql_fetch_assoc($resAdresseEvenementsCrees)) {
        if (!in_array($fetchAdresseEvenementsCrees['idEvenementGroupeAdresse'], $arrayListeEvGA)) {

            //!in_array($fetchAdresseEvenementsCrees['idAdresse'],$arrayListeAdresses) && {
                $arrayListeEvGA[] = $fetchAdresseEvenementsCrees['idEvenementGroupeAdresse'];
        }

        // histoire de ne pas afficher plusieurs fois le meme groupe d'adresse
        $arrayAdressesModifiees[$fetchAdresseEvenementsCrees['nomVille']][] = array("idAdresse"=>$fetchAdresseEvenementsCrees['idAdresse'], "libelle"=>$a->getIntituleAdresseFrom($fetchAdresseEvenementsCrees['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse', array('displayFirstTitreAdresse'=>true)), "url"=>$config->creerUrl('', 'adresseDetail', array('archiAffichage'=>'adresseDetail', 'archiIdAdresse'=>$fetchAdresseEvenementsCrees['idAdresse'], 'archiIdEvenementGroupeAdresse'=>$fetchAdresseEvenementsCrees['idEvenementGroupeAdresse'])), 'idEvenementGroupeAdresse'=>$fetchAdresseEvenementsCrees['idEvenementGroupeAdresse']);
    }
}


/*
SELECT distinct ha1.idAdresse as idAdresse, count(he2.idHistoriqueEvenement),ha1.date as date, v.nom as nomVille,he1.dateCreationEvenement,he2.idEvenement
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    
                    LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                    LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                    
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    
                    AND he1.dateCreationEvenement < NOW()
                    AND he1.dateCreationEvenement >= SUBDATE(NOW(), INTERVAL 7 DAY)
                    GROUP BY ha1.idAdresse,he1.idEvenement, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and count(he2.idHistoriqueEvenement)>1
                    ORDER BY nomVille,date
                    
                    
";*/
$arrayEvenementsModifiees = array();

// ************************************************************************************************************************************************************************************
// construction du complement suivant les infos de la table complementNewsLetterHebdo
$messageComplement = "<br>";

//$reqComplement = "SELECT idComplement, date, texte FROM complementNewsLetterHebdo WHERE date<$borneMin AND date>=$borneMax";
$sqlCritere = "";
if (!isset($_GET['modePrevisualisationAdmin']) || $_GET['modePrevisualisationAdmin']!='1') {
    $sqlCritere .= " AND date<$borneMin AND date>=$borneMax AND envoiMailHebdomadaire='1' AND desactive<>'1' ";
}
if (isset($_GET['idActualite']) && $_GET['idActualite']!='') {
    $sqlCritere .= " AND idActualite='".$_GET['idActualite']."' ";
}
$reqComplement = "SELECT idActualite, date, texteMailHebdomadaire, titre FROM actualites WHERE 1=1  $sqlCritere";
$resComplement = $config->connexionBdd->requete($reqComplement);
if (mysql_num_rows($resComplement)==1) {
    $fetchComplement = mysql_fetch_assoc($resComplement);

    //$messageComplement= str_replace("###cheminImages###",$config->getUrlRacine()."images/",$fetchComplement['texte']);
    //$messageComplement=str_replace("###cheminRacine###",$config->getUrlRacine(),$messageComplement)."<br><br>";
    $messageComplement .= str_replace("<img src=\"images/actualites", "<img src=\"".$config->getUrlRacine()."images/actualites", stripslashes($fetchComplement['texteMailHebdomadaire']))."<br>";
    if (!isset($_GET["modePrevisualisationAdmin"])) {
        $messageComplement .= "<a href='http://www.archi-strasbourg.org/actualites-archi-strasbourg-".$fetchComplement['idActualite'].".html' target='_blank'>lire la suite</a>";
    }
    $messageComplement .= "<br><br>";
}
if (count($arrayAdresses)>0 || count($arrayAdressesModifiees)>0) {
    $adressesModifieesVillesAffichees = array();

    // liste des villes deja affichees
    $adressesModifieesAffichees = array();
    $mail                       = new mailObject();
    $messageIntro               = "Bonjour,<br><br>";
    $messageIntro              .= $messageComplement;
    if (!isset($_GET["modePrevisualisationAdmin"])) {
        $messageIntro .= "Voici les adresses qui ont été créées ou modifiées cette semaine sur <a href='http://www.archi-strasbourg.org'>http://www.archi-strasbourg.org</a> :<br><br>";
    }
    $messageStrasbourg = "";
    $messageAutres     = "";
    $messageStrasModif = "";

    // les adresses modifiees de strasbourg, si strasbourg pas dans la liste comportant de nouvelles adresses
    $messageAutresModif = "";

    // les adresses modifiees des villes autres , si pas dans la liste comportant de nouvelles adresses
    $is1 = 0;

    // indices pour savoir si on affiche le titre de la rubrique ou pas ( 'nouvelles adresses' , ou 'adresses modifiees')
    $is2 = 0;
    $is3 = 0;
    $iv1 = 0;
    $iv2 = 0;
    $iv3 = 0;
    foreach ($arrayAdresses as $indiceVille=>$valueVille) {
        if (!in_array($indiceVille, $adressesModifieesVillesAffichees)) {
            $adressesModifieesVillesAffichees[] = $indiceVille;
        }
        if ($indiceVille=='Strasbourg') {
            $message = "<b>".$indiceVille."</b><br>";
            foreach ($valueVille as $indiceAdresse=>$valueAdresse) {
                if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                    if ($is1==0) {
                        $message .= "Nouvelles Adresses : <br>";
                    }
                    $message .= "<a href='".$valueAdresse['url']."'>".$valueAdresse['libelle']."</a><br>".PHP_EOL;
                    $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                    $is1++;
                }
            }
            $is1 = 0;
            $messageStrasbourg .= $message;
            if (isset($arrayAdressesModifiees[$indiceVille]) && count($arrayAdressesModifiees[$indiceVille])>0) {
                $message = "";
                foreach ($arrayAdressesModifiees[$indiceVille] as $indiceAdresse=>$valueAdresse) {
                    if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                        if ($is2==0) {
                            $messageStrasbourg .= "<br>Adresses modifiées :<br>";
                        }
                        $titre                        = "";
                        $titre                        = $a->getIntituleAdresseFrom($valueAdresse['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse', array('displayFirstTitreAdresse'=>true));
                        $message                     .= "<a href='".$valueAdresse['url']."'>".$titre."</a><br>".PHP_EOL;
                        $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                        $adressesModifieesAffichees   = array_unique($adressesModifieesAffichees);
                        $is2++;
                    }
                }
                $is2 = 0;
                $messageStrasbourg .= $message;
            }
        } else {
            $message = "<br><b>".$indiceVille."</b><br>";
            foreach ($valueVille as $indiceAdresse=>$valueAdresse) {
                if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                    if ($iv1==0) {
                        $message .= "Nouvelles Adresses : <br>";
                    }
                    $message .= "<a href='".$valueAdresse['url']."'>".$valueAdresse['libelle']."</a><br>".PHP_EOL;
                    $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                    $iv1++;
                }
            }
            $iv1 = 0;
            $messageAutres .= $message;
            if (isset($arrayAdressesModifiees[$indiceVille]) && count($arrayAdressesModifiees[$indiceVille])>0) {
                $message = "";
                foreach ($arrayAdressesModifiees[$indiceVille] as $indiceAdresse=>$valueAdresse) {
                    if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                        if ($iv2==0) {
                            $messageAutres .= "<br>Adresses modifiées :<br>";
                        }
                        $resIdEvenementGroupeAdresse = $a->getIdEvenementGroupeAdresseFromAdresse($valueAdresse['idAdresse']);
                        $titre = "";
                        if (mysql_num_rows($resIdEvenementGroupeAdresse)>0) {
                            $fetchIdEvenementGroupeAdresse = mysql_fetch_assoc($resIdEvenementGroupeAdresse);
                            $titre = stripslashes($e->getTitreFromFirstChildEvenement($fetchIdEvenementGroupeAdresse['idEvenement']));
                            if (trim($titre)!='') {
                                $titre .= ' - ';
                            }
                        }
                        $message                     .= "<a href='".$valueAdresse['url']."'>".$titre.$valueAdresse['libelle']."</a><br>".PHP_EOL;
                        $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                        $adressesModifieesAffichees   = array_unique($adressesModifieesAffichees);
                        $iv2++;
                    }
                }
                $iv2 = 0;
                $messageAutres .= $message;
            }
        }
    }

    // on affiche encore les adresses des villes qui n'ont pas ete affichees ci dessus ( adresses non ajoutées cette semaine, mais modifiées)
    foreach ($arrayAdressesModifiees as $indiceVille=>$valueVille) {
        if (!in_array($indiceVille, $adressesModifieesVillesAffichees)) {
            if ($indiceVille=="Strasbourg") {
                // la ville n'a pas de nouvelles adresses , mais des adresses modifiees
                foreach ($valueVille as $indiceAdresse=>$valueAdresse) {
                    if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                        if ($is3==0) {
                            $messageStrasModif .= "<br><b>Strasbourg</b><br>Adresses modifiées : <br>";
                        }
                        $resIdEvenementGroupeAdresse = $a->getIdEvenementGroupeAdresseFromAdresse($valueAdresse['idAdresse']);
                        $titre = "";
                        if (mysql_num_rows($resIdEvenementGroupeAdresse)>0) {
                            $fetchIdEvenementGroupeAdresse = mysql_fetch_assoc($resIdEvenementGroupeAdresse);
                            $titre = stripslashes($e->getTitreFromFirstChildEvenement($fetchIdEvenementGroupeAdresse['idEvenement']));
                            if (trim($titre)!='') {
                                $titre .= ' - ';
                            }
                        }
                        $messageStrasModif           .= "<a href='".$valueAdresse['url']."'>".$titre.$valueAdresse['libelle']."</a><br>".PHP_EOL;
                        $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                        $adressesModifieesAffichees   = array_unique($adressesModifieesAffichees);
                        $is3++;
                    }
                }
                $is3 = 0;
            } else {
                // la ville n'a pas de nouvelles adresses, mais des adresses modifiees
                foreach ($valueVille as $indiceAdresse=>$valueAdresse) {
                    if (!in_array($valueAdresse['idEvenementGroupeAdresse'], $adressesModifieesAffichees)) {
                        if ($iv3==0) {
                            $messageAutresModif .= "<br><b>$indiceVille</b><br>Adresses modifiées : <br>";
                        }
                        $resIdEvenementGroupeAdresse = $a->getIdEvenementGroupeAdresseFromAdresse($valueAdresse['idAdresse']);
                        $titre = "";
                        if (mysql_num_rows($resIdEvenementGroupeAdresse)>0) {
                            $fetchIdEvenementGroupeAdresse = mysql_fetch_assoc($resIdEvenementGroupeAdresse);
                            $titre = stripslashes($e->getTitreFromFirstChildEvenement($fetchIdEvenementGroupeAdresse['idEvenement']));
                            if (trim($titre)!='') {
                                $titre .= ' - ';
                            }
                        }
                        $messageAutresModif          .= "<a href='".$valueAdresse['url']."'>".$titre.$valueAdresse['libelle']."</a><br>".PHP_EOL;
                        $adressesModifieesAffichees[] = $valueAdresse['idEvenementGroupeAdresse'];
                        $adressesModifieesAffichees   = array_unique($adressesModifieesAffichees);
                        $iv3++;
                    }
                }
                $iv3 = 0;
            }
        }
    }
    $messageFin = "<br>L'équipe archi-strasbourg.org<br>";
    $messageFin .= $config->getMessageDesabonnerAlerteMail();
    
    $reqNewPeople = "
                    SELECT pers.idPersonne, pers.nom, pers.prenom
                    FROM personne pers
                    
                    LEFT JOIN _personneEvenement ae ON ae.idPersonne = pers.idPersonne
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    LEFT JOIN historiqueEvenement he ON he.idEvenement = ae.idEvenement
                    

                            
                    
                    WHERE he.dateCreationEvenement < $borneMin
                    AND he.dateCreationEvenement >= $borneMax
                    GROUP BY pers.idPersonne,ee.idEvenement
                    HAVING count(ee.idEvenementAssocie)>0
                    ORDER BY he.dateCreationEvenement
            ";
    $resNewPeople = $config->connexionBdd->requete($reqNewPeople);
    $messagePeople="<h4>Nouvelles personnes :</h4>
    <ul>";
    while ($newPerson= mysql_fetch_object($resNewPeople)) {
        $messagePeople.="<li><a href='".$config->creerUrl("", "evenementListe", array("selection"=>"personne", "id"=>$newPerson->idPersonne))."'>".$newPerson->prenom." ".$newPerson->nom."</a></li>".PHP_EOL;
    }
    $messagePeople.="</ul>";
    
    if (isset($fetchComplement['titre'])) {
        $sujet = $fetchComplement['titre'];
    } else {
        $sujet = "Nouvelles adresses et adresses modifiées sur archi-strasbourg.org.";
    }
    $messageHTML = "<!Doctype HTML>
    <html>
    <head>
    <title>".stripslashes($sujet)."</title>
    <meta charset='UTF-8' />
    </head>
    <body>";
    if (isset($_GET["modePrevisualisationAdmin"]) && !isset($_GET["testMail"])) {
        $messageHTML .= $messageIntro.$messageFin;
    } else {
        $messageHTML .= $messageIntro.$messageStrasbourg.$messageStrasModif.$messageAutres.$messageAutresModif.$messagePeople.$messageFin;
    }
    $messageHTML .= "</body></html>";
    
    if ((isset($_SERVER["SERVER_NAME"]) && !isset($_GET["modePrevisualisationAdmin"])) || isset($_GET["preview"])) {
        print_r($messageHTML);
        if (isset($_GET["testMail"])) {
            include_once __DIR__."/../modules/archi/includes/archiAuthentification.class.php";
            include_once __DIR__."/../modules/archi/includes/archiUtilisateur.class.php";
            $auth = new archiAuthentification();
            $idUtilisateur = $auth->getIdUtilisateur();
            $u = new archiUtilisateur();
            $mailUtilisateur = $u->getMailUtilisateur($idUtilisateur);
            $mail->sendMail($mail->getSiteMail(), $mailUtilisateur, $sujet, $messageHTML, false);
        }
    } else {
        $reqUtilisateurs = "SELECT idUtilisateur,mail FROM utilisateur WHERE alerteMail='1' and compteActif='1'";
        $resUtilisateurs = $config->connexionBdd->requete($reqUtilisateurs);

        //         $mail->sendMail($mail->getSiteMail(),"laurent.dorer@ri67.fr",$sujet,$messageHTML,true);
        //         $mail->sendMail($mail->getSiteMail(),"laurent.dorer@gmail.com",$sujet,$messageHTML,true);
        //$mail->sendMail($mail->getSiteMail(),"fabien.romary@ri67.fr",$sujet,$messageHTML,true);
        //$mail->sendMail($mail->getSiteMail(),"fabien.romary@gmail.com",$sujet,$messageHTML,true);
        //$mail->sendMail($mail->getSiteMail(),"fabien.romary@partenaireimmo.com",$sujet,$messageHTML,true);
        //$mail->sendMail($mail->getSiteMail(),"laurent_dorer@yahoo.fr",$sujet,$messageHTML,true);
        if (isset($_GET['debug']) && $_GET['debug']=='1') {
            //          $mail->sendMail($mail->getSiteMail(),"laurent.dorer@ri67.fr",$sujet,$messageHTML,true);
            //$mail->sendMail($mail->getSiteMail(),"fabien.romary@gmail.com",$sujet,$messageHTML,true);
        } else {
            while ($fetchUtilisateurs = mysql_fetch_assoc($resUtilisateurs)) {
                $mail->sendMail($mail->getSiteMail(), $fetchUtilisateurs['mail'], $sujet, $messageHTML, true);

                // $fetchUtilisateurs['mail']
            }
        }
        if (isset($_GET["modePrevisualisationAdmin"])) {
            header("Location: ".$config->getUrlRacine()."?archiAffichage=adminActualites");
        }
    }
}
?>
