<?php

class archiImage extends config
{
    private $nom;
    private $dateUpload;
    private $dateCliche;
    private $description;
    private $idProprietaire;
    private $idImage;
    
    
    function __construct($idImage = null)
    {
        $this->idImage = $idImage;
        parent::__construct();
    }
    
    public function supprimer()
    {
    
    }

    // effectue la modification des informations de la table de liaison entre adresses et images
    public function modifierLiaisonAdresse($idImage=0)
    {
        $authentifie = new archiAuthentification();

        if (isset($this->variablesPost['listeIdAdresses']) && $this->variablesPost['listeIdAdresses']!='') {
            $arrayListeId = explode(',  ',  $this->variablesPost['listeIdAdresses']);
            
            // on supprime d'abord les enregistrements précédents
            $resDelete = $this->connexionBdd->requete("delete from _adresseImage where idImage = '".$idImage."'");
            
            foreach ($arrayListeId as $indice => $idAdresse) {
                $prisDepuis ='0';
                if (isset($this->variablesPost['prisDepuis_'.$idAdresse]))
                    $prisDepuis = $this->variablesPost['prisDepuis_'.$idAdresse];
                $etage ="";
                if (isset($this->variablesPost['etage_'.$idAdresse]))
                    $etage = $this->variablesPost['etage_'.$idAdresse];
                $hauteur="";
                if (isset($this->variablesPost['hauteur_'.$idAdresse]))
                    $hauteur = $this->variablesPost['hauteur_'.$idAdresse];
                $seSitue='0';
                if (isset($this->variablesPost['seSitue_'.$idAdresse]))
                    $seSitue = $this->variablesPost['seSitue_'.$idAdresse];
                
                $reqMajLiaison = "
                    INSERT INTO _adresseImage (idImage, idAdresse, seSitue, prisDepuis, etage, hauteur)
                    VALUES ('".$idImage."',  '".$idAdresse."',  '".$seSitue."',  '".$prisDepuis."',  '".$etage."',  '".$hauteur."')                
                ";
                
                if ($resMajLiaison = $this->connexionBdd->requete($reqMajLiaison))
                {
                    echo "Enregistrement effectué.";
                    echo $this->afficher($idImage);
                }
            }
        }
    }
    
    // ************************************************************************************************************************************************************************************
    // MODIFICATION
    // fonctions effectuant la modification d'une image pour les cas adresses et evenement (chaque image indépendament suivant le formulaire envoyé)
    // ************************************************************************************************************************************************************************************
    public function modifier()
    {

    
        set_time_limit(0);
        $mail = new mailObject();    
        $authentifie = new archiAuthentification();
        $arrayListeIdImage=array();
        $dateDuJour = date("Y-m-d");
        //$authentifie->estConnecte() &&
        if (isset($this->variablesPost['listeId']) && $this->variablesPost['listeId']!='') {
            $adresses = new archiAdresse();
            
            $arrayListeIdHistoriqueImage = explode(',  ',  $this->variablesPost['listeId']);
            
            foreach ($arrayListeIdHistoriqueImage as $indice => $idHistorique) {
                if (isset($this->variablesPost['idCourant_'.$idHistorique])) {
                    $idCourant = $this->variablesPost['idCourant_'.$idHistorique];
                }
                
                if (isset($this->variablesPost['typeLiaisonImage_'.$idHistorique])) {
                    $typeLiaisonImage = $this->variablesPost['typeLiaisonImage_'.$idHistorique];
                }
                
                
                
                switch($typeLiaisonImage)
                {
                    case 'adresse':
                        $listeChamps=array('nom',  'description',  'seSitue',  'prisDepuis',  'etage',  'hauteur',  'dateCliche',  'dateUpload',  'source',  'isDateClicheEnviron', "licence", "auteur", "tags");
                    break;
                    case 'evenement':
                        $listeChamps=array('nom',  'description',  'dateCliche',  'dateUpload',  'source',  'isDateClicheEnviron',  'numeroArchive', "licence", "auteur", "tags");
                    break;
                    default:
                        $listeChamps=array('nom',  'description',  'dateCliche',  'dateUpload',  'source',  'isDateClicheEnviron',  'numeroArchive', "licence", "auteur", "tags");
                    break;
                }
            
            
                $idImage = $this->variablesPost['idImage_'.$idHistorique];
                
                $arrayListeIdImage[]=$idImage;
                // **********************************************************************
                // upload des photos remplacantes s'il y a lieu    
                if (isset($_FILES['fichierRemplace'.$idHistorique]['name']) && $_FILES['fichierRemplace'.$idHistorique]['name']!='')
                {
                    $authentifie = new archiAuthentification();
                    //$authentifie->estConnecte() &&
                    if ( extension_loaded('gd')) {
                        // nommage de l'image en fonction de l'id
                        // recuperation du type de fichier
                        // et conversion en jpg s'il le faut
                        // ajout d'un nouvel id dans l'historique image
                        $resAjout=$this->connexionBdd->requete('
                            insert into historiqueImage (idImage, dateUpload, idUtilisateur) 
                            values ("'.$idImage.'",  "'.$dateDuJour.'",  "'.$authentifie->getIdUtilisateur().'")
                            ');
                        
                        $nouvelIdHistoriqueImage=mysql_insert_id();
                        
                        // creation des repertoires a la date du jour s'ils n'existent pas
                        if (!is_dir($this->getCheminPhysiqueImage("originaux").$dateDuJour)) {
                            mkdir($this->getCheminPhysiqueImage("originaux").$dateDuJour)       or die('erreur création : '.$this->getCheminPhysiqueImage("originaux").$dateDuJour);
                            chmod($this->getCheminPhysiqueImage("originaux").$dateDuJour,  0777) or die('erreur chmod : '.$this->getCheminPhysiqueImage("originaux").$dateDuJour);
                        }
                        if (!is_dir($this->getCheminPhysiqueImage("mini").$dateDuJour)) {
                            mkdir($this->getCheminPhysiqueImage("mini"). $dateDuJour)       or die('erreur création : '.$this->getCheminPhysiqueImage("mini").$dateDuJour);
                            chmod($this->getCheminPhysiqueImage("mini"). $dateDuJour,  0777) or die('erreur chmod : '.$this->getCheminPhysiqueImage("mini").$dateDuJour);
                        }
                        if (!is_dir($this->getCheminPhysiqueImage("moyen").$dateDuJour)) {
                            mkdir($this->getCheminPhysiqueImage("moyen").$dateDuJour)       or die('erreur création : '.$this->getCheminPhysiqueImage("moyen").$dateDuJour);
                            chmod($this->getCheminPhysiqueImage("moyen").$dateDuJour,  0777) or die('erreur chmod : '.$this->getCheminPhysiqueImage("moyen").$dateDuJour);
                        }
                        if (!is_dir($this->getCheminPhysiqueImage("grand").$dateDuJour)) {
                            mkdir($this->getCheminPhysiqueImage("grand").$dateDuJour)       or die('erreur création : '.$this->getCheminPhysiqueImage("grand").$dateDuJour);
                            chmod($this->getCheminPhysiqueImage("grand").$dateDuJour,  0777) or die('erreur chmod : '.$this->getCheminPhysiqueImage("grand").$dateDuJour);
                        }
                                                
                        
                        // conversion en jpeg quelque soit le format géré
                        // 1- l'image est sauvegardee tel quel  (0 pour le redimensionnement)
                        $this->redimension($_FILES['fichierRemplace'.$idHistorique]['tmp_name'],  pia_substr(strtolower($_FILES['fichierRemplace'.$idHistorique]['name']),  -3),  $this->getCheminPhysiqueImage("originaux").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  0);
                        // 2- redimensionnement au format mini
                        
                        $this->redimension($_FILES['fichierRemplace'.$idHistorique]['tmp_name'],  pia_substr(strtolower($_FILES['fichierRemplace'.$idHistorique]['name']),  -3),  $this->getCheminPhysiqueImage("mini").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageMini());
                        
                        // 3- redimensionnement au format moyen
                        $this->redimension($_FILES['fichierRemplace'.$idHistorique]['tmp_name'],  pia_substr(strtolower($_FILES['fichierRemplace'.$idHistorique]['name']),  -3),  $this->getCheminPhysiqueImage("moyen").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageMoyen());
                        
                        // 4- redimensionnement au format grand
                        $this->redimension($_FILES['fichierRemplace'.$idHistorique]['tmp_name'],  pia_substr(strtolower($_FILES['fichierRemplace'.$idHistorique]['name']),  -3),  $this->getCheminPhysiqueImage("grand").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageGrand());

                        
                        if (file_exists($_FILES['fichierRemplace'.$idHistorique]['tmp_name'])) {
                            echo "le fichier est uploadé<br>";
                        }
                        else
                            echo "Erreur::le fichier ne figure pas sur le serveur !!<br>";
                        
                        unlink($_FILES['fichierRemplace'.$idHistorique]['tmp_name']);
                        
                        
                    } else {
                        echo "Il s'est produit une erreur lors de l'upload,  la session est terminée ou la bibliothèque gd n'est pas installé sur le serveur.<br>";
                    }
                }
                
                // **********************************************************************
                // enregistrement des infos de l'image dans la base
                // recuperation des champs par une image
                $tableauChamps=array(); // contient les infos d'une image
                foreach ($listeChamps as $indice => $name)
                {
                    if ($name=='isDateClicheEnviron') {
                        if (isset($this->variablesPost[$name."_".$idHistorique]) && $this->variablesPost[$name."_".$idHistorique]=='1')
                            $tableauChamps[$name] = 1;
                        else
                            $tableauChamps[$name] = 0;
                    } else {
                        $tableauChamps[$name] = isset($this->variablesPost[$name."_".$idHistorique])?$this->variablesPost[$name."_".$idHistorique]:"";
                    }
                }
                
                $idImage = $this->variablesPost['idImage_'.$idHistorique];
                
                // il y a 0000-00-00 dans la base de donnée si la date n'est pas fournie dans le formulaire
                $dateCliche = $tableauChamps['dateCliche'];
                if ($tableauChamps['dateCliche']=='')
                {
                    $dateCliche='0000-00-00';
                }
                
                
                
                
                // est ce que les proprietes de l'image dans la base sont les memes que celles qui ont ete validees dans le formulaire
                // si oui => pas de modif
                // sinon => ajout a l'historique de l'image
                
                $resCompareImage= $this->connexionBdd->requete("
                    SELECT 
                        idHistoriqueImage
                    FROM historiqueImage 
                    WHERE idHistoriqueImage = '".mysql_real_escape_string($idHistorique)."'
                    AND dateUpload = '".mysql_real_escape_string($this->date->toBdd($tableauChamps['dateUpload']))."'
                    AND dateCliche = '".mysql_real_escape_string($this->date->toBdd($this->date->convertYears($dateCliche)))."'
                    AND description = '".mysql_real_escape_string($tableauChamps['description'])."'
                    AND isDateClicheEnviron = '".mysql_real_escape_string($tableauChamps['isDateClicheEnviron'])."'
                    AND nom = '".mysql_real_escape_string($tableauChamps['nom'])."'
                    AND idSource = '".mysql_real_escape_string($tableauChamps['source'])."'
                    AND licence = '".mysql_real_escape_string($tableauChamps["licence"])."'
                    AND auteur = '".mysql_real_escape_string($tableauChamps["auteur"])."'
                    AND tags = '".mysql_real_escape_string($tableauChamps["tags"])."'
                    AND numeroArchive=\"".mysql_real_escape_string($tableauChamps['numeroArchive'])."\"
                ");
                if (mysql_num_rows($resCompareImage)==0)
                {
                    // l'image avec des proprietes identiques n'existe pas  donc on en deduit que les proprietes de l'image ont changés ,  on ajoute donc un nouvel historiqueImage et on modifie les données dans la table de liaison
                    $resImage = $this->connexionBdd->requete("
                        INSERT INTO historiqueImage (idImage,  nom,  dateUpload,  dateCliche,  description,  idUtilisateur,  idSource, isDateClicheEnviron, auteur, licence, tags, numeroArchive) 
                        VALUES 
                        ('".mysql_real_escape_string($idImage)."', 
                            '".mysql_real_escape_string($tableauChamps['nom'])."', 
                            '".mysql_real_escape_string($this->date->toBdd($tableauChamps['dateUpload']))."', 
                            '".mysql_real_escape_string($this->date->toBdd($this->date->convertYears($dateCliche)))."', 
                            '".mysql_real_escape_string($tableauChamps['description'])."', 
                            '".mysql_real_escape_string($authentifie->getIdUtilisateur())."', 
                            '".mysql_real_escape_string($tableauChamps['source'])."', 
                            '".mysql_real_escape_string($tableauChamps['isDateClicheEnviron'])."', 
                            '".mysql_real_escape_string($tableauChamps["auteur"])."', 
                            '".mysql_real_escape_string($tableauChamps["licence"])."', 
                            '".mysql_real_escape_string($tableauChamps["tags"])."', 
                            \"".mysql_real_escape_string($tableauChamps['numeroArchive'])."\"
                        )
                    ");
                    
                    
                    // end debug fabien  17/12/2011
                    
                    echo "L'image a été modifiée";
                    $newIdHistoriqueImage = mysql_insert_id();
                    $idHistoriqueImagePrecedent=$idHistorique;
                    
                    // ancienne code qui ne marche plus avec php 5.3.3 .... bizzare !
                    //symlink("./".$idHistoriqueImagePrecedent.'.jpg',  $this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');

                    exec(" ln -s ". "./".$idHistoriqueImagePrecedent.'.jpg' . " " . $this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');

/*                    $erreurcode=symlink($this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$idHistoriqueImagePrecedent.'.jpg',  $this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    symlink("./".$idHistoriqueImagePrecedent.'.jpg',  "./".$newIdHistoriqueImage.'.jpg');
                    
                    mkdir("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/tutu",  0777);   // fonctionne !!
                    
                    echo " <br> idHistoriqueImagePrecedent : ./" . $idHistoriqueImagePrecedent . ".jpg"."<br>";
                    
                    echo symlink("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/44143.jpg",  "/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/XXXX.jpg");
                    
                    echo $this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$idHistoriqueImagePrecedent.'.jpg';
                    
                    echo "\n\n";
                    
                    echo $this->getCheminPhysiqueImage("mini").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg';
*/                    
                    // debug fabien  17/12/2011
//                    echo "ERR " . $erreurcode . "ERR";
//                    exit;
                    exec(" ln -s ". "./".$idHistoriqueImagePrecedent.'.jpg' . " " . $this->getCheminPhysiqueImage("moyen").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    exec(" ln -s ". "./".$idHistoriqueImagePrecedent.'.jpg' . " " . $this->getCheminPhysiqueImage("grand").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    exec(" ln -s ". "./".$idHistoriqueImagePrecedent.'.jpg' . " " . $this->getCheminPhysiqueImage("originaux").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    
                    // ne fonctionne plus avec php 5.3.3
/*                    symlink("./".$idHistoriqueImagePrecedent.'.jpg',  $this->getCheminPhysiqueImage("moyen").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    symlink("./".$idHistoriqueImagePrecedent.'.jpg',  $this->getCheminPhysiqueImage("grand").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
                    symlink("./".$idHistoriqueImagePrecedent.'.jpg',  $this->getCheminPhysiqueImage("originaux").$this->date->toBdd($tableauChamps['dateUpload']).'/'.$newIdHistoriqueImage.'.jpg');
*/                
                }
                
                
                // ****************************************************************
                // LIAISON DE L'IMAGE ET DES ADRESSES (vueSur et prisDepuis)
                // on enregistre les informations des adresses liees à la photo
                // ****************************************************************
                
                // on recupere d'abord les zones existantes pour ne pas les perdres ,  on les replacera ensuite sur les liaisons inserees
                $tabRecupZonesPrisDepuis = array();
                $reqRecupZones = "SELECT * FROM _adresseImage WHERE idImage = '".$idImage."' AND prisDepuis='1' AND coordonneesZoneImage<>'' AND largeurBaseZoneImage<>'' AND longueurBaseZoneImage<>''";
                $resRecupZones = $this->connexionBdd->requete($reqRecupZones);
                
                while ($fetchRecupZones = mysql_fetch_assoc($resRecupZones))
                {
                    $tabRecupZonesPrisDepuis[$fetchRecupZones['idAdresse']] = array(
                                                                                'idAdresse'=>$fetchRecupZones['idAdresse'], 
                                                                                'idEvenementGroupeAdresse'=>$fetchRecupZones['idEvenementGroupeAdresse'], 
                                                                                'idImage'=>$fetchRecupZones['idImage'], 
                                                                                'coordonneesZoneImage'=>$fetchRecupZones['coordonneesZoneImage'], 
                                                                                'largeurBaseZoneImage'=>$fetchRecupZones['largeurBaseZoneImage'], 
                                                                                'longueurBaseZoneImage'=>$fetchRecupZones['longueurBaseZoneImage']
                    );
                }
                
                
                //prisDepuis
                // on supprime d'abord les informations des adresses liées à la photo afin de pouvoir ajouter les nouvelles adresses
                $reqDeletePrisDepuis = "delete from _adresseImage where idImage = '".$idImage."' AND prisDepuis='1'";
                $resDeletePrisDepuis = $this->connexionBdd->requete($reqDeletePrisDepuis);
                // enregistrement des liaisons 
                if (isset($this->variablesPost['prisDepuis'.$idHistorique]) && count($this->variablesPost['prisDepuis'.$idHistorique])>0)
                {
                    foreach ($this->variablesPost['prisDepuis'.$idHistorique] as $indice => $value) {
                        $arrayAdresseGroupeAdresseImage = explode("_",  $value);
                        $idAdresse = $arrayAdresseGroupeAdresseImage[0];
                        $idEvenementGroupeAdresse = $arrayAdresseGroupeAdresseImage[1];
                        // verification que l'enregistrement n'existe pas deja
                        $reqVerifPrisDepuis = "SELECT idImage FROM _adresseImage WHERE idImage = '".$idImage."' AND idAdresse = '".$idAdresse."' AND idEvenementGroupeAdresse='".$idEvenementGroupeAdresse."' AND prisDepuis ='1';";
                        $resVerifPrisDepuis = $this->connexionBdd->requete($reqVerifPrisDepuis);
                        if (mysql_num_rows($resVerifPrisDepuis)==0) {
                            $champs="";
                            $values="";
                            if (isset($tabRecupZonesPrisDepuis[$value]) && count($tabRecupZonesPrisDepuis[$value])>0)
                            {
                                $champs=",  coordonneesZoneImage,  largeurBaseZoneImage,  longueurBaseZoneImage";
                                $values=",  '".$tabRecupZonesPrisDepuis[$value]['coordonneesZoneImage']."',  '".$tabRecupZonesPrisDepuis[$value]['largeurBaseZoneImage']."',  '".$tabRecupZonesPrisDepuis[$value]['longueurBaseZoneImage']."' ";
                            }
                        
                            $reqPrisDepuis = "INSERT INTO _adresseImage (idImage,  idAdresse, idEvenementGroupeAdresse,  prisDepuis $champs) VALUES ('".$idImage."',  '".$idAdresse."',  '".$idEvenementGroupeAdresse."',  '1' $values)";
                            $resPrisDepuis = $this->connexionBdd->requete($reqPrisDepuis);
                        }
                    }
                }

                // vueSur
                
                
                // on recupere d'abord les zones existantes pour ne pas les perdres ,  on les replacera ensuite sur les liaisons inserees
                $tabRecupZonesVuesSur = array();
                $reqRecupZones = "SELECT * FROM _adresseImage WHERE idImage = '".$idImage."' AND vueSur='1' AND coordonneesZoneImage<>'' AND largeurBaseZoneImage<>'' AND longueurBaseZoneImage<>''";
                $resRecupZones = $this->connexionBdd->requete($reqRecupZones);
                
                while ($fetchRecupZones = mysql_fetch_assoc($resRecupZones))
                {
                    $tabRecupZonesVuesSur[$fetchRecupZones['idAdresse']."_".$fetchRecupZones['idEvenementGroupeAdresse']] = array(
                                                                                'idAdresse'=>$fetchRecupZones['idAdresse'], 
                                                                                'idEvenementGroupeAdresse'=>$fetchRecupZones['idEvenementGroupeAdresse'], 
                                                                                'idImage'=>$fetchRecupZones['idImage'], 
                                                                                'coordonneesZoneImage'=>$fetchRecupZones['coordonneesZoneImage'], 
                                                                                'largeurBaseZoneImage'=>$fetchRecupZones['largeurBaseZoneImage'], 
                                                                                'longueurBaseZoneImage'=>$fetchRecupZones['longueurBaseZoneImage']
                    );
                }
                
                
                
                $reqDeleteVueSur = "delete from _adresseImage where idImage='".$idImage."' AND vueSur='1'";
                $resDeleteVueSur = $this->connexionBdd->requete($reqDeleteVueSur);

                if (isset($this->variablesPost['vueSur'.$idHistorique]) && count($this->variablesPost['vueSur'.$idHistorique])>0)
                {
                    foreach ($this->variablesPost['vueSur'.$idHistorique] as $indice => $value) {
                        $arrayAdresseGroupeAdresseImage = explode("_",  $value);
                        $idAdresse = $arrayAdresseGroupeAdresseImage[0];
                        $idEvenementGroupeAdresse = $arrayAdresseGroupeAdresseImage[1];
                        // verification que l'enregistrement n'existe pas deja
                        $reqVerifVueSur = "SELECT idImage FROM _adresseImage WHERE idImage = '".$idImage."' AND idAdresse = '".$idAdresse."' AND idEvenementGroupeAdresse='".$idEvenementGroupeAdresse."' AND vueSur ='1';";
                        
                        $resVerifVueSur = $this->connexionBdd->requete($reqVerifVueSur);
                        if (mysql_num_rows($resVerifVueSur)==0) {
                            $champs="";
                            $values="";
                            if (isset($tabRecupZonesVuesSur[$value]) && count($tabRecupZonesVuesSur[$value])>0)
                            {
                                $champs=",  coordonneesZoneImage,  largeurBaseZoneImage,  longueurBaseZoneImage";
                                $values=",  '".$tabRecupZonesVuesSur[$value]['coordonneesZoneImage']."',  '".$tabRecupZonesVuesSur[$value]['largeurBaseZoneImage']."',  '".$tabRecupZonesVuesSur[$value]['longueurBaseZoneImage']."' ";
                            }
                        
                            $reqVueSur = "INSERT INTO _adresseImage (idImage,  idAdresse, idEvenementGroupeAdresse,  vueSur $champs) VALUES ('".$idImage."',  '".$idAdresse."',  '".$idEvenementGroupeAdresse."',  '1' $values)";
                            
                            $resVueSur = $this->connexionBdd->requete($reqVueSur);
                        }
                    }
                }
                
                
                
                // ****************************************************************
                // LIAISON DE L'IMAGE ET DES EVENEMENTS
                // on enregistre les informations des evenements lies à la photo
                // ****************************************************************

               
        
                
                // on lie les evenements selectionnés avec l'adresse
                if (isset($this->variablesPost['listeEvenements_'.$idHistorique]))
                {
                    $arrayListeEvenements = array_unique($this->variablesPost['listeEvenements_'.$idHistorique]);
                    
                    foreach ($arrayListeEvenements as $indice => $valueIdEvenement) {
                        $resPos = $this->connexionBdd->requete("SELECT position from _evenementImage where idImage = '".$idImage."' AND idEvenement = '".$valueIdEvenement."'");
                        $pos=mysql_fetch_row($resPos);
                        // On supprime les informations des evenements lies à la photo afin de pouvoir ajouter les nouveaux evenements
                        $resDeleteEvenements = $this->connexionBdd->requete("delete from _evenementImage where idImage = '".$idImage."' AND idEvenement = '".$valueIdEvenement."'");
                        $reqInsertEvenements = $this->connexionBdd->requete("INSERT INTO _evenementImage (idImage, idEvenement, position) VALUES ('".$idImage."',  '".$valueIdEvenement."', '".$pos[0]."')
                        ");
                    }
                }
            }
        }
        
        
        // envoi d'un mail aux administrateurs
        $message ="";
        foreach ($arrayListeIdImage as $indice => $idImageModifiee) {
            if ($idPerson=archiPersonne::isPerson($this->getIdEvenementGroupeAdresseFromImage(array("idImage"=>$idImageModifiee, "type"=>"personne")))) {
                $archiRetourIdValue=$this->getIdEvenementGroupeAdresseFromImage(array("idImage"=>$idImageModifiee, "type"=>"personne"));
            } else {
                $archiRetourIdValue=$this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageModifiee));
            }
            $message.="<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$idImageModifiee,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvement',  'archiRetourIdValue'=>$archiRetourIdValue))."'>Image ".$idImageModifiee."</a><br>";
        }
        
        // recuperation d'une adresse sur laquelle ont ete liées les images ( c'est un formulaire capable de traiter des images d'adresses différentes,  mais cela n'arrive pas)
        $intituleAdresse="";
        if ($idImageModifiee!='0' && $idImageModifiee!='') {
            $a = new archiAdresse();
            $intituleAdresse = $a->getIntituleAdresseFrom($idImageModifiee,  'idImage');
        }
        
        $message ="Images modifiées : $intituleAdresse<br>".$message;
        
        
        
        // recuperation des infos sur l'utilisateur qui fais la modif
        $utilisateur = new archiUtilisateur();
        $arrayInfosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
        
        $message .="<br>".$arrayInfosUtilisateur['nom']." - ".$arrayInfosUtilisateur['prenom']." - ".$arrayInfosUtilisateur['mail']."<br>";
        
        $mail->sendMailToAdministrators($mail->getSiteMail(),  "Modification d'images - ".$intituleAdresse,  $message,  " AND alerteAdresses='1' ", true);
        $utilisateur->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,  'idTypeMailRegroupement'=>12,  'criteres'=>" and alerteAdresses='1' "));
        
        
        // *************************************************************************************************************************************************************
        // envoi mail aussi au moderateur si ajout sur adresse de ville que celui ci modere
        $u = new archiUtilisateur();
        $adresse = new archiAdresse();
        $arrayVilles=array();
        $arrayVilles[] = $adresse->getIdVilleFrom($this->getIdAdresseFromIdImage($idImageModifiee),  'idAdresse');
        $arrayVilles = array_unique($arrayVilles);
        
        $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($arrayVilles[0],  array("sqlWhere"=>" AND alerteAdresses='1' "));
        if (count($arrayListeModerateurs)>0) {
            foreach ($arrayListeModerateurs as $indice => $idModerateur) {
                if ($idModerateur!=$this->session->getFromSession('utilisateurConnecte'.$this->idSite))
                {
                    if ($u->isMailEnvoiImmediat($idModerateur)) {
                        $mailModerateur = $u->getMailUtilisateur($idModerateur);
                        $mail->sendMail($mail->getSiteMail(),  $mailModerateur,  "Modification d'images - ".$intituleAdresse,  $message, true);
                    } else {
                        $u->ajouteMailEnvoiRegroupes(array('contenu'=>$message,  'idDestinataire'=>$idModerateur,  'idTypeMailRegroupement'=>12));
                    }
                }
            }
        }
        // *************************************************************************************************************************************************************
        
        //archiAffichage=imageDetail&archiIdImage=28940&archiRetourAffichage=evenement&archiRetourIdName=idEvenement&archiRetourIdValue=18149
        $idEvenementGroupeAdresse=$this->getIdEvenementGroupeAdresseFromImage(array("idImage"=>$idImageModifiee, "type"=>"personne"));
        if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
            header("Location: ".$this->creerUrl("", "evenementListe", array("selection"=>"personne", "id"=>$idPerson), false, false));
        }
        
        // ************************************************************************************************************************************************
        // envoi d'un mail pour l'auteur de l'adresse
        // ************************************************************************************************************************************************
        $mail = new mailObject();
        $utilisateur = new archiUtilisateur();
        
        foreach ($arrayListeIdImage as $indice => $idImage) {
            $arrayUtilisateurs[] = $utilisateur->getCreatorsFromAdresseFrom($idImage,  'idImage');
        }
        
        foreach ($arrayUtilisateurs as $indice => $tabUtilImage) {
            if ($tabUtilImage[0]['idUtilisateur'] != $authentifie->getIdUtilisateur()) {
                $infosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($tabUtilImage[0]['idUtilisateur']);
                if ($infosUtilisateur['alerteAdresses']=='1' && $infosUtilisateur['idProfil']!='4' && $infosUtilisateur['compteActif']=='1')
                {
                    $idEvenementGroupeAdresseRetour = $this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageModifiee));
                    $message = "Un utilisateur a modifié une image sur une adresse ou vous avez participé.";
                    $message.= "Pour vous rendre sur l'évènement : <a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresseRetour,  'archiIdAdresse'=>$tabUtilImage[0]['idAdresse']))."'>".$adresse->getIntituleAdresseFrom($tabUtilImage[0]['idAdresse'],  "idAdresse")."</a><br>";
                    $message.= $this->getMessageDesabonnerAlerteMail();
                    
                    if ($utilisateur->isMailEnvoiImmediat($tabUtilImage[0]['idUtilisateur'])) {
                        $mail->sendMail($mail->getSiteMail(),  $infosUtilisateur['mail'],  'Modification d\'une image sur une adresse sur laquelle vous avez participé - '.$intituleAdresse,  $message, true);
                    } else {
                        $utilisateur->ajouteMailEnvoiRegroupes(array('contenu'=>$message,  'idDestinataire'=>$tabUtilImage[0]['idUtilisateur'],  'idTypeMailRegroupement'=>12));
                    }
                    
                }
            }
        }
        // ************************************************************************************************************************************************
        
        if (isset($this->variablesPost['formulaireRetour']) && $this->variablesPost['formulaireRetour']!='') {
            switch($this->variablesPost['formulaireRetour']) {
                case 'evenement':
                    $evenement = new archiEvenement();
                    echo $evenement->afficher($idCourant);
                break;
            }
        } else {
            if (isset($this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse']!='') {
                $idEvenementGroupeAdresseRetour = $this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse'];
            } else {
                $idEvenementGroupeAdresseRetour = $this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageModifiee));
            }
            
            if ($idEvenementGroupeAdresseRetour!=0) {
                $a = new archiAdresse();
                echo $a->afficherDetail(0,  $idEvenementGroupeAdresseRetour);
            } else {
                echo $this->afficher($idImageModifiee);
            }
        }
    }
    
    // renvoi un groupe d'adresse auquel appartient l'image passé en parametre
    public function getIdEvenementGroupeAdresseFromImage($params = array())
    {
        $retour = 0;
        if (isset($params['idImage']) && $params['idImage']!='') {
            if (isset($params['type']) && $params['type']=="personne") {
                $req = "
                    SELECT ae.idEvenement as idEvenementGroupeAdresse
                    FROM _personneEvenement ae 
                    LEFT JOIN _evenementImage ei ON ei.idImage = '".$params['idImage']."'
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                    WHERE ae.idEvenement = ee.idEvenement
                    LIMIT 1
                ";
            } else {
                $req = "
                    SELECT ae.idEvenement as idEvenementGroupeAdresse
                    FROM _adresseEvenement ae 
                    LEFT JOIN _evenementImage ei ON ei.idImage = '".$params['idImage']."'
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                    WHERE ae.idEvenement = ee.idEvenement
                    LIMIT 1
                ";
            }
            $res = $this->connexionBdd->requete($req);
            if (mysql_num_rows($res)>0) {
                $fetch = mysql_fetch_assoc($res);
                $retour = $fetch['idEvenementGroupeAdresse'];
            }

        }
    
        return $retour;
    }
    
    
    // recupere la premiere adresse trouvée a laquelle l'image precisée appartient
    public function getIdAdresseFromIdImage($idImage)
    {
        $retour=0;
        $req = "
                    SELECT ea.idAdresse as idAdresse
                    FROM _evenementImage ei
                    RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                    RIGHT JOIN _adresseEvenement ea ON ea.idEvenement = ee.idEvenement
                    WHERE ei.idImage = '".$idImage."'
                    LIMIT 1
                    ";

        $res = $this->connexionBdd->requete($req);
        if (mysql_num_rows($res)>0) {
            $fetch = mysql_fetch_assoc($res);
            $retour = $fetch['idAdresse'];
        }
        
        return $retour;
    }
    
    public function afficheImageOriginale($idImage=0)
    {
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('detailImage'=>'detailImage.tpl')));
        $authentification = new archiAuthentification();
        
        $resImage = $this->connexionBdd->requete("
        select hI.nom, hI.dateUpload, hI.dateCliche, hI.description, hI.idUtilisateur, hI.idHistoriqueImage
        from historiqueImage hI,  historiqueImage hI2
        where hI.idImage='".$idImage."'
        and hI2.idImage=hI.idImage
        group by hI.idImage, hI.idHistoriqueImage
        having hI.idHistoriqueImage = max(hI2.idHistoriqueImage)
        ");
        
        
        $fetch=mysql_fetch_array($resImage);
        
        /*
        stripslashes($fetch['nom'])
         $this->date->toFrench($fetch['dateCliche'])
        */
        $nomEtDateCliche="";
        
        if (stripslashes($fetch['nom'])!='' && $this->date->toFrench($fetch['dateCliche'])!='')
            $nomEtDateCliche=stripslashes($fetch['nom']).' - '.$this->date->toFrench($fetch['dateCliche']);
        else
            $nomEtDateCLiche=stripslashes($fetch['nom']).$this->date->toFrench($fetch['dateCliche']); // on affiche les deux car on sait qu'il y en a un qui est vide,  donc pas de probleme
        
        
        $description = $fetch['description'];
        $description = str_replace("\\r\\n",  "<br>",  $description);
        $description = str_replace("\\n\\r",  "<br>",  $description);
        $description = str_replace("\\n",  "<br>",  $description);
        
        $t->assign_vars(array(
            'cheminDetailImage' => $this->getUrlImage("originaux").$fetch['dateUpload'].'/'.$fetch['idHistoriqueImage'].".jpg", 
            'nomEtDateCliche'               => $nomEtDateCliche,  
            'description' => stripslashes($description)
        ));
        
        //$authentifie = new archiAuthentification();
        /*if (1==1)//$authentifie->estConnecte() {
            
        //'formulaireModification'=>$this->afficherFormulaireModification($fetch['idHistoriqueImage'],  'adresse')
            $t->assign_block_vars('connecte',  array(
                ''        => 
                ));
            
        } else {
            $t->assign_block_vars('pasConnecte',  array());
        }*/
        
        /*$adresses = new archiAdresse();
        $t->assign_vars(array('listeAdressesLiees'=>$this->afficherAdressesLiees($idImage)));
        
        
        $evenements = new archiEvenement();
        $t->assign_vars(array('listeEvenementsLies'=>$this->afficherEvenementsLies($idImage)));
        */
        
        /*
        $t->assign_vars(array('urlModifierImage'=>$this->creerUrl('',  'modifierImage',  array('archiIdImageModification'=>$idImage))));
        */
        // gestion du lien de retour à la page précédente
        if (isset($this->variablesGet['archiRetourAffichage']) && isset($this->variablesGet['archiRetourIdName']) && isset($this->variablesGet['archiRetourIdValue'])) {
            $t->assign_block_vars('isRetour',  array('urlRetour'=>$this->creerUrl('',  $this->variablesGet['archiRetourAffichage'],  array($this->variablesGet['archiRetourIdName']=>$this->variablesGet['archiRetourIdValue']))));
        }
        
        ob_start();
        $t->pparse('detailImage');
        $html=ob_get_contents();
        ob_end_clean();
        
        return $html;
        
    
    }
    
    
    // ******************************************************************************************************************************************
    // affichage du detail d'une image
    // ******************************************************************************************************************************************
    public function afficher($idImage=0)
    {
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('detailImage'=>'detailImage.tpl')));
        $adresse = new archiAdresse();
        $evenement = new archiEvenement();
        $authentification = new archiAuthentification();
        $string = new stringObject();
        $u = new archiUtilisateur();
        $d = new droitsObject();
        
        $html="";
        
        if (((isset($this->variablesGet['formatPhoto']) && $this->variablesGet['formatPhoto']=='moyenRedim') || (isset($this->variablesGet['formatPhoto']) && $this->variablesGet['formatPhoto']=='original')) && !$authentification->estConnecte()) {
            // il faut etre connecté et donc inscrit pour pouvoir voir les images dans un format different
            $html.=$authentification->afficheFormulaireAuthentification('noCompact',  array("msg"=>"<b>Pour voir les photos au format moyen ou l'original vous devez être connecté. Si vous n'avez pas encore de compte utilisateur pour vous connecter,  cliquez <a href='".$this->creerUrl('',  'inscription')."'>ici</a></b>"));
        } else {
            if ($authentification->estConnecte()) {
                $t->assign_block_vars("isConnected",  array());
                
                $idProfilUtilisateur = $u->getIdProfilFromUtilisateur($authentification->getIdUtilisateur());

                if ($d->isAuthorized('image_supprimer',  $idProfilUtilisateur))
                {
                    require_once __DIR__.'/archiPersonne.class.php';
                    
                    $e = new ArchiEvenement();
                    // on verifie que l'utilisateur est moderateur de la ville ou est admin
                    if ($u->isModerateurFromVille($authentification->getIdUtilisateur(),  $idImage,  'idImage')
                        || $idProfilUtilisateur == '4'
                        || ArchiPersonne::isPerson($e->getIdEvenementGroupeAdresseFromIdEvenement($_GET['archiRetourIdValue']))
                    ) {
                        if (isset($this->variablesGet['archiRetourAffichage'])) {
                            $t->assign_block_vars('isAdminOrModerateurFromVille',  array('urlSupprimerImage'=>$this->creerUrl('deleteImage',  '',  array('archiIdImage'=>$idImage,  'archiRetourAffichage'=>$this->variablesGet['archiRetourAffichage'],  'archiRetourIdName'=>$this->variablesGet['archiRetourIdName'],  'archiRetourIdValue'=>$this->variablesGet['archiRetourIdValue']))));
                        } else {
                            $t->assign_block_vars('isAdminOrModerateurFromVille',  array('urlSupprimerImage'=>$this->creerUrl('deleteImage',  '',  array('archiIdImage'=>$idImage))));
                        }
                    }
                }
                
                
                
                
                if ($authentification->estAdmin())
                {
                
                    if (isset($this->variablesGet['archiRetourAffichage']) && isset($this->variablesGet['archiRetourIdName']) && isset($this->variablesGet['archiRetourIdValue'])) {
                        $t->assign_block_vars('isAdmin',  array('urlAfficheHistorique'=>$this->creerUrl('',  'afficheHistoriqueImage',  array('archiIdImage'=>$idImage,  'archiRetourAffichage'=>$this->variablesGet['archiRetourAffichage'],  'archiRetourIdName'=>$this->variablesGet['archiRetourIdName'],  'archiRetourIdValue'=>$this->variablesGet['archiRetourIdValue']))
                                            ));
                    } else {
                        $t->assign_block_vars('isAdmin',  array('urlAfficheHistorique'=>$this->creerUrl('',  'afficheHistoriqueImage',  array('archiIdImage'=>$idImage))
                                        ));
                    }
                }
            }
            
            // gestion des liens vers le detail de la photo et autres formats
            if (isset($this->variablesGet["formatPhoto"])) {
                switch($this->variablesGet["formatPhoto"])
                {
                    case 'petit':
                        $classLienPetit="formatAffichePhotoSelected";
                        $classLienMoyen="formatAffichePhoto";
                        $classLienOriginal="formatAffichePhoto";
                        $formatPhoto = "grand"; // petit correspond au format 'grand' en interne
                        $formatPhotoUrl = "petit";
                    break;
                    case 'moyenRedim':
                        $classLienPetit="formatAffichePhoto";
                        $classLienMoyen="formatAffichePhotoSelected";
                        $classLienOriginal="formatAffichePhoto";
                        $formatPhoto = "moyenRedim";
                        $formatPhotoUrl = "moyenRedim";
                    break;
                    case 'original':
                        $classLienPetit="formatAffichePhoto";
                        $classLienMoyen="formatAffichePhoto";
                        $classLienOriginal="formatAffichePhotoSelected";
                        $formatPhoto = "originaux";
                        $formatPhotoUrl = "original";
                    break;
                }
            } else {
                $classLienPetit="formatAffichePhotoSelected";
                $classLienMoyen="formatAffichePhoto";
                $classLienOriginal="formatAffichePhoto";
                $formatPhoto = "grand";
                $formatPhotoUrl = "petit";
            }
            
            if (isset($this->variablesGet['archiRetourAffichage'])) {
                $t->assign_vars(array("choixFormatPhoto"=>"<span style='color:#007799;'>Photo au format : <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>$this->variablesGet['archiRetourAffichage'],  "archiRetourIdName"=>$this->variablesGet['archiRetourIdName'],  "archiRetourIdValue"=>$this->variablesGet['archiRetourIdValue'],  "formatPhoto"=>'petit'))."' class='$classLienPetit'>Petit</a> | <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>$this->variablesGet['archiRetourAffichage'],  "archiRetourIdName"=>$this->variablesGet['archiRetourIdName'],  "archiRetourIdValue"=>$this->variablesGet['archiRetourIdValue'],  "formatPhoto"=>'moyenRedim'))."' class='$classLienMoyen'>Moyen</a> | <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>$this->variablesGet['archiRetourAffichage'],  "archiRetourIdName"=>$this->variablesGet['archiRetourIdName'],  "archiRetourIdValue"=>$this->variablesGet['archiRetourIdValue'],  "formatPhoto"=>'original'))."' class='$classLienOriginal'>Original</a></span><br />"));
            } else {
                $idAdresseRetour = $this->getIdAdresseFromIdImage($idImage);
                $t->assign_vars(array("choixFormatPhoto"=>"<span style='color:#007799;'>Photo au format : <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>'adresseDetail',  "archiRetourIdName"=>'archiIdAdresse',  "archiRetourIdValue"=>$idAdresseRetour,  "formatPhoto"=>'petit'))."' class='$classLienPetit'>Petit</a> | <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>'adresseDetail',  "archiRetourIdName"=>'archiIdAdresse',  "archiRetourIdValue"=>$idAdresseRetour,  "formatPhoto"=>'moyenRedim'))."' class='$classLienMoyen'>Moyen</a> | <a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$idImage,  "archiRetourAffichage"=>'adresseDetail',  "archiRetourIdName"=>'archiIdAdresse',  "archiRetourIdValue"=>$idAdresseRetour,  "formatPhoto"=>'original'))."' class='$classLienOriginal'>Original</a></span><br />"));
            }
            
            // ***********************************************************************************************************************
            // on prend l'adresse de reference de la page si elle existe sinon celle de l'evenement courant et sinon on prend l'adresse de reference de l'image
            if (isset($this->variablesGet['archiIdAdresse'])) {
                $idAdresseReference = $this->variablesGet['archiIdAdresse'];
            }
            elseif (isset($this->variablesGet['archiRetourIdName']) && $this->variablesGet['archiRetourIdName']=='idEvenement' && isset($this->variablesGet['archiRetourIdValue'])) {
                $idAdresseReference =$adresse->getIdAdresseFromIdEvenementGroupeAdresse($this->variablesGet['archiRetourIdValue']);
            } else {
                $idAdresseReference = $this->getIdAdresseFromIdImage($idImage);
            }

            
            // **********************************************************************************************************************
            
            $arrayInfosImage = $this->getInfosCompletesFromIdImage($idImage,  array('idAdresseReference'=>$idAdresseReference,  'displayFirstTitreAdresse'=>true,  'classCSSTitreAdresse'=>"textePrisDepuisVueSur",  'withZonesOnMouseOver'=>true));
            
            if (count($arrayInfosImage['vueSurLiens'])>0) {
            
                if (count($arrayInfosImage['vueSurLiens'])>1)
                {
                    $t->assign_vars(array("infosVueSur"=>"<span class='textePrisDepuisVueSurEntete'>Vue sur :</span>&nbsp;<br>".implode("<br>",  $arrayInfosImage['vueSurLiens'])."<br>"));
                } else {
                    $t->assign_vars(array("infosVueSur"=>"<span class='textePrisDepuisVueSurEntete'>Vue sur </span>&nbsp;".implode(" / ",  $arrayInfosImage['vueSurLiens'])."<br>"));
                }
            
                
            }
            
            
            if (count($arrayInfosImage['prisDepuisLiens'])>0) {
                $t->assign_vars(array("infosPrisDepuis"=>"<br><span class='textePrisDepuisVueSurEntete'>Pris depuis</span>&nbsp; ".implode(" / ",  $arrayInfosImage['prisDepuisLiens'])."<br>"));
            }
            
            $e = new archiEvenement();
            if (archiPersonne::isPerson($e->getIdEvenementGroupeAdresseFromIdEvenement($_GET["archiRetourIdValue"]))) {
               $resImage = $this->connexionBdd->requete("SELECT * FROM `historiqueImage` WHERE `idImage` =".$idImage.' ORDER BY idHistoriqueImage DESC');
            } else {
                $resImage = $this->connexionBdd->requete(
                    "SELECT * FROM `historiqueImage` WHERE `idImage` = $idImage
                    ORDER BY idHistoriqueImage DESC"
                );
                /*$resImage = $this->connexionBdd->requete("
                select hI.idSource,  hI.nom, hI.dateUpload, hI.dateCliche, hI.description, hI.idUtilisateur, hI.idHistoriqueImage, ha1.idAdresse as idAdresse, ha1.numero as numero, 
                hI.isDateClicheEnviron as isDateClicheEnviron, 
                r.nom as nomRue, 
                sq.nom as nomSousQuartier, 
                q.nom as nomQuartier, 
                v.nom as nomVille, 
                p.nom as nomPays, 
                ha1.numero as numeroAdresse,  
                hI.numeroArchive as numeroArchive, 
                ha1.idRue, 
                r.prefixe as prefixeRue, 
                IF (ha1.idSousQuartier != 0,  ha1.idSousQuartier,  r.idSousQuartier) AS idSousQuartier, 
                IF (ha1.idQuartier != 0,  ha1.idQuartier,  sq.idQuartier) AS idQuartier, 
                IF (ha1.idVille != 0,  ha1.idVille,  q.idVille) AS idVille, 
                IF (ha1.idPays != 0,  ha1.idPays,  v.idPays) AS idPays
                
                from historiqueImage hI2, historiqueImage hI
                
                RIGHT JOIN _evenementImage ei ON ei.idImage = hI.idImage
                RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                RIGHT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                RIGHT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse    
                
                
                LEFT JOIN rue r         ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = if (ha1.idRue='0' and ha1.idSousQuartier!='0' , ha1.idSousQuartier , r.idSousQuartier )
                LEFT JOIN quartier q        ON q.idQuartier = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' , ha1.idQuartier , sq.idQuartier )
                LEFT JOIN ville v        ON v.idVille = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' , ha1.idVille , q.idVille )
                LEFT JOIN pays p        ON p.idPays = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' , ha1.idPays , v.idPays )
                
                
                
                
                where hI.idImage='$idImage'
                and hI2.idImage=hI.idImage
                group by hI.idImage, hI.idHistoriqueImage, ha1.idAdresse,  ha1.idHistoriqueAdresse
                having hI.idHistoriqueImage = max(hI2.idHistoriqueImage) and ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                LIMIT 1
                ");*/
            }
            
            $fetch=mysql_fetch_array($resImage);
            /*
            stripslashes($fetch['nom'])
             $this->date->toFrench($fetch['dateCliche'])
            */
            $nomEtDateCliche="";
            
            if (stripslashes($fetch['nom'])!='' && $this->date->toFrench($fetch['dateCliche'])!='')
                $nomEtDateCliche=stripslashes($fetch['nom']).' - '.$this->date->toFrench($fetch['dateCliche']);
            else
                $nomEtDateCLiche=stripslashes($fetch['nom']).$this->date->toFrench($fetch['dateCliche']); // on affiche les deux car on sait qu'il y en a un qui est vide,  donc pas de probleme
            $datePriseDeVue = "";
            if ($this->date->toFrench($fetch['dateCliche'])!='') {
                $environ = "";
                if ($fetch['isDateClicheEnviron']=='1')
                {
                    $environ = "environ ";
                }
                $datePriseDeVue = "Date : $environ".$this->date->toFrench($fetch['dateCliche']);
            }
            
            
            $description = $fetch['description'];

            $bbCode = new bbCodeObject();
            if (!empty($description)) {
                $description = $bbCode->convertToDisplay(array('text'=>$description));
            }
            
            
            if (isset($fetch['idSource']) && $fetch['idSource']!='' && $fetch['idSource']!='0') {
                $reqSource = "
                    SELECT s.idSource as idSource,  s.nom as nomSource,  ts.nom as nomTypeSource
                    FROM source s
                    LEFT JOIN typeSource ts ON ts.idTypeSource = s.idTypeSource
                    WHERE s.idSource = '".$fetch['idSource']."'";
                $resSource = $this->connexionBdd->requete($reqSource);
                $fetchSource = mysql_fetch_assoc($resSource);
                $description.="<br>Source : <a href='".$this->creerUrl('',  'listeAdressesFromSource',  array('source'=>$fetch['idSource'],  'submit'=>'Rechercher'))."'>".stripslashes($fetchSource['nomSource'])." (".stripslashes($fetchSource['nomTypeSource']).")</a>";
            }
            
            if (isset($fetch['numeroArchive']) && $fetch['numeroArchive']!='') {
            // modif fabien du 15/04/2011 suite mail directrice Archives de Strasbourg Mme Perry Laurence
                $description.="<br>Cote Archives de Strasbourg : ".$fetch['numeroArchive'];
            }
            
            $intituleAdresse = $adresse->getIntituleAdresse($fetch);
            
            $reqImages = "
            SELECT idImage FROM _evenementImage WHERE idEvenement = ".mysql_real_escape_string($_GET['archiRetourIdValue'])." ORDER BY position
            ";
        
            $resImages = $this->connexionBdd->requete($reqImages);
            $found = false;
            while ($row = mysql_fetch_assoc($resImages)) {
                if(intval($row['idImage']) == $_GET['archiIdImage']) {
                    if (isset($prev)) {
                        $prevImage=$prev;
                        $t->assign_block_vars('previous',  array());
                    }
                    $next = true;
                } else if(isset($next)) {
                    $nextImage=$row;
                    $t->assign_block_vars('next',  array());
                    break;
                }
                $prev=$row;
            }
            
            $reqImages = "
            SELECT (SELECT idHistoriqueImage from historiqueImage  WHERE _evenementImage.idImage = historiqueImage.idImage ORDER BY idHistoriqueImage DESC LIMIT 1), (SELECT dateUpload from historiqueImage WHERE _evenementImage.idImage = historiqueImage.idImage ORDER BY idHistoriqueImage DESC LIMIT 1), (SELECT description from historiqueImage WHERE _evenementImage.idImage = historiqueImage.idImage ORDER BY idHistoriqueImage DESC LIMIT 1), idImage FROM _evenementImage  WHERE idEvenement = ".mysql_real_escape_string($_GET['archiRetourIdValue'])." ORDER BY position
            ";
            $resImages = $this->connexionBdd->requete($reqImages);
            $imgList = array();
            while ($row = mysql_fetch_row($resImages)) {
                $imgList[] = $row;
            }
            
            $intituleAdresseNoQuartierNoVille = $adresse->getIntituleAdresse($fetch,  array('noQuartier'=>true,  'noSousQuartier'=>true,  'noVille'=>true));
            
            $format=isset($_GET['formatPhoto'])?$_GET['formatPhoto']:'petit';
            
            if ($u->canModifyTags(array('idUtilisateur'=>$authentification->getIdUtilisateur())))
                {
                $tags = 'Tags&nbsp;: ';
                if (empty($fetch["tags"])) {
                    $tags .= '<i>(aucun)</i>';
                } else {
                    $tags .= stripslashes($fetch["tags"]);
                }
            } else {
                $tags='';
            }
            
            $t->assign_vars(array(
                'datePriseDeVue'=>$datePriseDeVue, 
                'cheminDetailImage' => 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetch['dateUpload'].'-'.$fetch['idHistoriqueImage'].'-'.$formatPhoto.'.jpg', 
                'nomEtDateCliche'  => $nomEtDateCliche,  
                'tags' => $tags, 
                'description' => $description, 
                'fullscreenDesc' => strip_tags($description), 
                'nom'=>$intituleAdresseNoQuartierNoVille, 
                'IDDivImage'=>"divImage_".$idImage, 
                'IDDivZones'=>"divZones_".$idImage,
                'nextURL'=>$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $nextImage['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$_GET['archiRetourIdValue'], "formatPhoto"=>$format)),
                'prevURL'=>$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $prevImage['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$_GET['archiRetourIdValue'], "formatPhoto"=>$format)),
                'list'=>rawurlencode(json_encode($imgList)),
                'imgID'=>rawurlencode(json_encode(array($fetch['idHistoriqueImage'], $fetch['idImage']))),
                'imgDate'=>$fetch['dateUpload'],
                'orgId'=>$fetch['idImage'],
                'format'=>$format
            ));
            //$this->urlImagesGrand.$fetch['dateUpload'].'/'.$fetch['idHistoriqueImage'].".jpg"
            
            // si affichage du detail sans modification ,  on affiche les zones cliquables
            
            
            // pour la selection de zone modifiable
            // on ne l'affiche que si l'image figure dans la table _adresseImage et comporte des adresses sur lesquelles le champ vueSur est a 1
            if (count($arrayInfosImage['vueSurLiens'])>0 && $authentification->estConnecte() && $u->isAuthorized('selection_zones_photo',  $authentification->getIdUtilisateur())) {
                if ($u->getIdProfil($authentification->getIdUtilisateur())==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(),  $idImage,  'idImage'))
                {
                    $styleMenuZoneSelection="";
                    if (isset($this->variablesGet['archiSelectionZone']) && $this->variablesGet['archiSelectionZone']=='1') {
                        // menu en rouge si on est sur la selection de zone
                        $styleMenuZoneSelection="color:#FF0000;";
                    }
                    
                    $t->assign_block_vars('selectionZonesCliquables',  array('styleMenuZoneSelection'=>$styleMenuZoneSelection,  'urlSelectionZone'=>$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$idImage,  'archiRetourAffichage'=>$this->variablesGet['archiRetourAffichage'], 
                                            'archiRetourIdName'=>$this->variablesGet['archiRetourIdName'], 
                                            'archiRetourIdValue'=>$this->variablesGet['archiRetourIdValue'], 
                                            'archiSelectionZone'=>1,  'formatPhoto'=>$formatPhotoUrl))));
                }
            }
            
            
            
            
            
            
            $authentifie = new archiAuthentification();
            
            
            // gestion du lien de retour à la page précédente
            if (isset($this->variablesGet['archiRetourAffichage']) && isset($this->variablesGet['archiRetourIdName']) && isset($this->variablesGet['archiRetourIdValue'])) {
                        //$t->assign_block_vars('isRetour',  array('urlRetour'=>$this->creerUrl('',  $this->variablesGet['archiRetourAffichage'],  array($this->variablesGet['archiRetourIdName']=>$this->variablesGet['archiRetourIdValue']))));
                // affichage de l'encars des adresses
                $idEvenementGroupeAdresse = $evenement->getIdEvenementGroupeAdresseFromIdEvenement($this->variablesGet['archiRetourIdValue']);
                if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                    $person= new archiPersonne();
                    $infos=$person->getInfosPersonne($idPerson);
                    $html.="<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>";
                } else {
                    $html.=$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse);
                }
                
                $t->assign_vars(array('urlModifierImage'=>$this->creerUrl('',  'modifierImage',  array('archiIdImageModification'=>$idImage,  'archiIdEvenementGroupeAdresseAffichageAdresse'=>$idEvenementGroupeAdresse))));
            }
            elseif (isset($idAdresseRetour) && $idAdresseRetour!='' && $idAdresseRetour!='0') {
                $resIdGroupeAdresse = $adresse->getIdEvenementGroupeAdresseFromAdresse($idAdresseRetour);
                $fetchIdGroupeAdresse = mysql_fetch_assoc($resIdGroupeAdresse);
                
                $html.=$adresse->afficherRecapitulatifAdresses($fetchIdGroupeAdresse['idEvenement']);
                $t->assign_vars(array('urlModifierImage'=>$this->creerUrl('',  'modifierImage',  array('archiIdImageModification'=>$idImage,  'archiIdEvenementGroupeAdresseAffichageAdresse'=>$fetchIdGroupeAdresse['idEvenement']))));
            } else {
                $t->assign_vars(array('urlModifierImage'=>$this->creerUrl('',  'modifierImage',  array('archiIdImageModification'=>$idImage))));
            }
            
            
            // ***********************************************************************************************************************
            if (isset($this->variablesGet['archiRetourIdName']) && $this->variablesGet['archiRetourIdName']=='idEvenement' && isset($this->variablesGet['archiRetourIdValue'])) {
                $reqVerifZone = "
                    SELECT 0
                    FROM _adresseImage ai
                    WHERE
                        ai.idImage='".$idImage."'
                    AND ai.idEvenementGroupeAdresse = '".$this->variablesGet['archiRetourIdValue']."'
                    AND vueSur='1'
                    AND ai.coordonneesZoneImage<>''
                    AND ai.largeurBaseZoneImage<>''
                    AND ai.longueurBaseZoneImage<>''
                    
                ";
                $resVerifZone = $this->connexionBdd->requete($reqVerifZone);
                if (mysql_num_rows($resVerifZone)>0)
                {
                    $imageZoom = $this->getUrlRacine()."imageZoomZone.php?idImage=".$idImage."&idEvenementGroupeAdresse=".$this->variablesGet['archiRetourIdValue']."&idAdresseCourante=".$idAdresseReference."&date=".$fetch['dateUpload']."&idHistorique=".$fetch['idHistoriqueImage'];
                    $t->assign_vars(array('imageZoom'=>"<img src='$imageZoom' alt=''>"));
                    $t->assign_vars(array('txtZoom'=>"<br>"._("L'image ci-dessus est un zoom de l'image ci-dessous :")));
                }
            }
            $licence=$this->getLicence($idImage);
            $textLicence="<img src='images/licences/".$licence["logo"]."' alt=''/> ";
            if (!empty($licence["link"])) {
                $textLicence.="<a rel='license' href='".$licence["link"]."'>";
            }
            $textLicence.=$licence["name"];
            if (!empty($licence["link"])) {
                $textLicence.="</a>";
            }
            $auteur=$this->getAuteur($idImage);
            if (is_array($auteur)) {
                if (!empty($auteur["nom"]) && $auteur["nom"]!=" ") {
                    $textLicence.=" (<span itemprop='author'><a rel='author' href='profil-".$auteur["id"].".html'>".$auteur["nom"]."</a></span>)";
                } 
            } else {
                $textLicence.=" (<span itemprop='author'>".$auteur."</span>)";
            }
            $t->assign_vars(array("licence"=>$textLicence));
            // ***********************************************************************************************************************
            ob_start();
            $t->pparse('detailImage');
            $html.=ob_get_contents();
            ob_end_clean();
            $image = new imageObject();
            $html.=$image->getJsSetOpacityFunction();
            $html.=$image->getJsCodeDrawFunctions();
            
            if (isset($this->variablesGet['archiSelectionZone']) && $this->variablesGet['archiSelectionZone']=='1') {
                // recuperation des longueur et largeur de l'image affiché pour avoir le rapport de base entre ces deux dimensions,  comme cela on pourra reporter les zones sur les images d'autres longueurs et largeurs grace au taux calculé
                $calque = new calqueObject(array('idPopup'=>'popupSelectAdresseVueSurZone'));
                
                // calque de selection d'adresse une fois que la zone est selectionnee
                $html.=$calque->getDiv(array('height'=>300,  'lienSrcIFrame'=>$this->creerUrl('',  'affichePopupSelectionZoneVueSur',  array("noHeaderNoFooter"=>'1',  "archiIdImage"=>$idImage)),  "titre"=>"Selectionnez l'adresse de la zone",  "codeJsFermerButton"=>"location.href=location.href;"));
                $html.="<script  >".$calque->getJsToDragADiv()."</script>";
 // fonctions de trace,  obligatoire si on veut afficher les zones
                $html.=$image->getJsCodeSelectionZone(array('nomIDImage'=>'divImage',  'tracePolygoneResultat'=>true, 
                                'onZoneSelectedAction'=>"
                                        document.getElementById('".$calque->getJSDivId()."').style.display='block';
                                        imageElement = document.getElementById('imageAfficheeID');
                                        document.getElementById('largeurBaseImageZoneSelection').value=imageElement.clientWidth;
                                        document.getElementById('longueurBaseImageZoneSelection').value=imageElement.clientHeight;
                                ", 
                                'addHTMLElementsToFormValidatedAfterZoneSelection'=>"<input type='hidden' id='idAdresseRetourZone' name='idAdresseRetourZone' value=''>
                                <input type='hidden' name='largeurBaseImageZoneSelection' id='largeurBaseImageZoneSelection' value=''>
                                <input type='hidden' name='longueurBaseImageZoneSelection' id='longueurBaseImageZoneSelection' value=''>
                                ", 
                                'actionFormValidateZone'=>$this->creerUrl('enregistreZoneImage',  'imageDetail',  array('archiIdImage'=>$idImage,  'archiRetourAffichage'=>$this->variablesGet['archiRetourAffichage'],  'archiRetourIdName'=>$this->variablesGet['archiRetourIdName'],  'archiRetourIdValue'=>$this->variablesGet['archiRetourIdValue']))
                                ));
            }
            

            // affichage des zones existantes,  map sur l'image et divs des formes
            

            
            if ($formatPhoto == 'moyenRedim') {
                // vu que le format de la photo moyenRedim est generé a la volée,  il faut calculer les hauteurs et largeurs en fonction d'une autre photo. Ici on prendra le format grand c'est suffisamment precis pas besoin de prendre le format original
                // les photos redimensionnées ont une largeur X de 700px ,  voir le htaccess
                $sizes = getimagesize($this->getCheminPhysiqueImage("grand").$fetch['dateUpload']."/".$fetch['idHistoriqueImage'].".jpg");
                // on converti les dimensions
                $sizeX=0;
                $sizeY=0;
                if ($sizes[0]>=$sizes[1])
                {
                    $sizeX=700;
                    $sizeY=round((700*$sizes[0]) / $sizes[1]);
                } else {
                    $sizeY = 700;
                    $sizeX = round((700*$sizes[1]) / $sizes[0]);
                }
                $arrayZones = $this->getDivsAndMapsZonesImagesVueSur(array("idImage"=>$idImage,  'largeurImageCourante'=>$sizeX,  'longueurImageCourante'=>$sizeY));
                
            } else {
                $sizes = getimagesize($this->getCheminPhysique()."images/".$formatPhoto."/".$fetch['dateUpload']."/".$fetch['idHistoriqueImage'].".jpg");
                $arrayZones = $this->getDivsAndMapsZonesImagesVueSur(array("idImage"=>$idImage,  'largeurImageCourante'=>$sizes[0],  'longueurImageCourante'=>$sizes[1]));
            }
            
            $html.=$arrayZones['htmlDivs'];
            $html.=$arrayZones['htmlMaps'];
            $html.=$arrayZones['htmlJs'];
            
            
            
            

        }
        
        return $html;
    }
    
    // recuperation du code html des divs des zones de l'image
    public function getDivsAndMapsZonesImagesVueSur($params=array())
    {
        $htmlDivs="";
        $htmlMaps="";
        $htmlJs="";
        $htmlMouseOut="";
        $arrayIDDivs = array();
        $idImage=0;
        if (isset($params['idImage']))
            $idImage = $params['idImage'];
        
        $largeurImageCourante=0;
        if (isset($params['largeurImageCourante']))
            $largeurImageCourante = $params['largeurImageCourante'];

        $longueurImageCourante=0;
        if (isset($params['longueurImageCourante']))
            $longueurImageCourante = $params['longueurImageCourante'];
        
        
        $jsIDImage="imageAfficheeID"; // id image sur le detail de la photo (identifiant element javascript,  pas id de bdd)
        if (isset($params['idHTMLImage']) && $params['idHTMLImage']!='') {
            $jsIDImage=$params['idHTMLImage'];
        }
        
        $jsIDMapZone = "mapZones";
        if (isset($params['idHTMLMap']) && $params['idHTMLMap']!='') {
            $jsIDMapZone = $params['idHTMLMap'];
        }
        
        $divIdGroupeAdresseCourant="";
        if (isset($params['idGroupeAdresseCourant']) && $params['idGroupeAdresseCourant']!='') {
            $idGroupeAdresseCourant = $params['idGroupeAdresseCourant'];
            $divIdGroupeAdresseCourant = "_".$params['idGroupeAdresseCourant'];
        }
        
        
        $htmlJS="";
        
        $req = "SELECT idAdresse, idEvenementGroupeAdresse, coordonneesZoneImage, largeurBaseZoneImage, longueurBaseZoneImage FROM _adresseImage WHERE idImage=$idImage AND vueSur=1 AND coordonneesZoneImage<>''";
        $res = $this->connexionBdd->requete($req);
        
        if (mysql_num_rows($res)>0) {
            $a = new archiAdresse();
            
            while ($fetch = mysql_fetch_assoc($res)) {
                if ($fetch['idEvenementGroupeAdresse']!='0' && $fetch['idEvenementGroupeAdresse']!='')
                {
                    $intituleAdresse = $a->getIntituleAdresseFrom($fetch['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
                } else {
                    $intituleAdresse = $a->getIntituleAdresseFrom($fetch['idAdresse'],  'idAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
                }
                $htmlDivs.="";//<div id='divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage."'></div>";
                // ATTENTION il faut que les fonctions javascript de dessins de forme soient initialisées
                //$htmlJs.="<script  >var jg = new jsGraphics('divImage');</script>";
                $arrayCoords = array();
                $arrayCoords = explode(",  ",  $fetch['coordonneesZoneImage']);
                if ($largeurImageCourante!=$fetch['largeurBaseZoneImage'] || $longueurImageCourante!=$fetch['longueurBaseZoneImage'])
                {
                    // on modifie les coordonnees en fonction du rapport entre les coordonnees de l'image originale et celles de l'image affichée,  on admet que l'image n'est pas deformée à l'affichage
                    $arrayCoordsEchelle=array();
                    foreach ($arrayCoords as $indice => $value) {
                        $arrayCoordsEchelle[] = round($largeurImageCourante*$value / $fetch['largeurBaseZoneImage']);
                        
                    }
                    $coords = implode(",  ",  $arrayCoordsEchelle);
                } else {
                    $arrayCoordsEchelle = explode(",",  $arrayCoords[0]);
                    $coords = $fetch['coordonneesZoneImage'];
                }
                if (isset($arrayCoordsEchelle[1])) {
                    $largeur = $arrayCoordsEchelle[2] - $arrayCoordsEchelle[0];
                    $hauteur = $arrayCoordsEchelle[3] - $arrayCoordsEchelle[1];
                    
                    // code pour dessiner dans le div un rectangle plein
                    // il faut que la fonction set_opacity soit definie => voir objet image du framework
                    if ($fetch['idEvenementGroupeAdresse']!='' && $fetch['idEvenementGroupeAdresse']!='0') {
                        $url = $this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdAdresse'=>$fetch['idAdresse'],  'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGroupeAdresse']));
                    } else {
                        $url = $this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetch['idAdresse']));
                    }
                    $htmlJs.="
                    <script>
                        /*var jg = new jsGraphics('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage."');
                        jg.setColor('#0000ff');
                        jg.fillRect(".$arrayCoordsEchelle[0].",  ".$arrayCoordsEchelle[1].",  $largeur,  $hauteur); 
                        jg.paint();
                        */
                        
                        divZone = document.createElement('div');
                        
                        
                        imgAffichee = document.getElementById('$jsIDImage'); // identifiant balise image
                        //divZone = document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."');
                        divZone.style.position='absolute';
                        divZone.style.left=".$arrayCoordsEchelle[0]."+'px';//+imgAffichee.offsetLeft;
                        divZone.style.top = ".$arrayCoordsEchelle[1]."+'px';//-imgAffichee.clientHeight;//+imgAffichee.offsetTop;
                        divZone.style.width = $largeur+'px';
                        divZone.style.height = $hauteur+'px';
                        divZone.style.border = '3px solid #FF0000';
                        divZone.style.backgroundColor = '';
                        divZone.style.display='none';
                        divZone.style.cursor='pointer';
                        divZone.style.overflow = 'visible';
                        divZone.setAttribute('onmouseout', \"document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';document.getElementById('divInfosVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';\");
                        divZone.setAttribute('onclick', \"location.href='".$url."';\");
                        
                        divZone.setAttribute('id', \"divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."\");
                        
                        divInfos = document.createElement('div');
                        divInfos.style.position='absolute';
                        //divInfos.style.display = 'inline';
                        divInfos.style.backgroundColor = '#FFFFFF';
                        divInfos.innerHTML=\"<span style='font-size:11px;white-space:nowrap;overflow:visible;'>".$intituleAdresse."</span>\";
                        divInfos.style.whiteSpace = 'nowrap';
                        divInfos.style.overflow='visible';
                        divInfos.style.top = divZone.style.top;
                        divInfos.style.left = divZone.style.left;
                        divInfos.setAttribute('id', \"divInfosVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."\");
                        divInfos.setAttribute('onmouseout', \"document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';document.getElementById('divInfosVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';\");
                        divInfos.style.display='none';
                        
                        
                        divZones = document.getElementById('divZones_".$idImage.$divIdGroupeAdresseCourant."');
                        divZones.style.position='absolute';
                        divZones.style.cursor='pointer';
                        divZones.appendChild(divZone);
                        divZones.appendChild(divInfos);
                        set_opacity('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."', 40);

                    </script>";
                    //refreshDims_".$fetch['idAdresse']."_image_".$idImage."();
                    $htmlMaps.="<area shape='rect' onmouseover=\"document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='block';document.getElementById('divInfosVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='block';\" coords='".$coords."' onclick=\"location.href='".$url."';\" style='cursor:pointer;' onmouseout=\"if (document.all){document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';document.getElementById('divInfosVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';}\">";
                    $htmlMouseOut.="document.getElementById('divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant."').style.display='none';";
                    $arrayIDDivs[] = "divZonesVueSurAdresse_".$fetch['idAdresse']."_image_".$idImage.$divIdGroupeAdresseCourant;
                }
            }
            
            $htmlMaps="<MAP NAME='".$jsIDMapZone."'>".$htmlMaps."</MAP>";
        }
        
        
        return array("htmlDivs"=>$htmlDivs,  "htmlMaps"=>$htmlMaps,  "htmlJs"=>$htmlJs,  "htmlMouseOut"=>$htmlMouseOut,  "arrayNomsIDDivsZones"=>$arrayIDDivs);
    }
    
    // enregistrement de la zone
    public function enregistreZoneImage()
    {
        if (
            isset($this->variablesPost['largeurBaseImageZoneSelection']) &&
            isset($this->variablesPost['longueurBaseImageZoneSelection']) &&
            count($this->variablesPost['inputArrayPointX'])==2 &&
            count($this->variablesPost['inputArrayPointY'])==2 &&
            isset($this->variablesPost['idAdresseRetourZone']) && 
            isset($this->variablesGet['archiIdImage']) &&
            $this->variablesPost['largeurBaseImageZoneSelection']!='' && 
            $this->variablesPost['longueurBaseImageZoneSelection']!=''
        ) {
            $arrayCoords=array();
            foreach ($this->variablesPost['inputArrayPointX'] as $indice => $value) {
                $arrayCoords[] = $this->variablesPost['inputArrayPointX'][$indice];
                $arrayCoords[] = $this->variablesPost['inputArrayPointY'][$indice];
            }
            
            
            $arrayAdresse = explode("_",  $this->variablesPost['idAdresseRetourZone']);
            $idAdresse = $arrayAdresse[0];
            $idEvenementGroupeAdresse = $arrayAdresse[1];
            
            
        
            $req = "UPDATE _adresseImage SET 
                            coordonneesZoneImage='".implode(",  ",  $arrayCoords)."',  
                            largeurBaseZoneImage='".$this->variablesPost['largeurBaseImageZoneSelection']."',  
                            longueurBaseZoneImage='".$this->variablesPost['longueurBaseImageZoneSelection']."' 
                            WHERE idImage = '".$this->variablesGet['archiIdImage']."' 
                            AND idAdresse='".$idAdresse."' 
                            AND idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."'
                            AND vueSur='1'";
            $res = $this->connexionBdd->requete($req);
        
            echo "Enregistrement de la nouvelle zone effectué.<br>";
        }
        
    
    }
    
    
    
    // ************************************************************************************************************************************************************
    // fonction renvoyant le code html des images vue sur par rapport a l'image passée en parametre
    // ************************************************************************************************************************************************************
    public function getImagesVueSur($params=array())
    {
        $html="";

        $adresse = new archiAdresse();
        
        $modeAffichage='';
        if (isset($params['modeAffichage']) && $params['modeAffichage']!='') {
            $modeAffichage = $params['modeAffichage'];
        }
        
        
        $format = 'mini';
        if (isset($params['format']))
            $format = $params['format'];
        
        
        $reqVueSur = "SELECT idAdresse, idEvenementGroupeAdresse FROM _adresseImage WHERE idImage = '".$params['idImage']."' AND vueSur='1'";
        $resVueSur = $this->connexionBdd->requete($reqVueSur);
        $tableauVueSur = new tableau();
        $arrayTemp=array();
        $i=0;
        while ($fetchVueSur=mysql_fetch_assoc($resVueSur)) {
            if ($fetchVueSur['idEvenementGroupeAdresse']!='0') {
                $nomAdresse = $adresse->getIntituleAdresseFrom($fetchVueSur['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
            } else {
                $nomAdresse = $adresse->getIntituleAdresseFrom($fetchVueSur['idAdresse'],  'idAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
            }
            $infosImage = $adresse->getUrlImageFrom($fetchVueSur['idAdresse'],  $format,  'AND ei.position=1');
            if ($infosImage['idHistoriqueImage']=='0') {
                $infosImage = $adresse->getUrlImageFrom($fetchVueSur['idAdresse'],  $format);
            }
            
            switch($modeAffichage) {
                case 'affichePopupSelectionZoneVueSur':
                    $tableauVueSur->addValue("<a onclick=\"parent.document.getElementById('idAdresseRetourZone').value='".$fetchVueSur['idAdresse']."_".$fetchVueSur['idEvenementGroupeAdresse']."';parent.document.getElementById('formRetourArrayPoints').submit();\">".$nomAdresse."</a>");
                break;
                default:
                    $arrayTemp[$i]['celluleHaut']="<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchVueSur['idAdresse']))."'><img src='".$infosImage['url']."' alt=''></a>";
                    $arrayTemp[$i]['celluleBas']="<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchVueSur['idAdresse']))."'>".$nomAdresse."</a>";
                break;
            }
            $i++;
        }
        
        switch($modeAffichage) {
            case 'affichePopupSelectionZoneVueSur':
                $html=$tableauVueSur->createHtmlTableFromArray(1,  "",  "",  "",  "");
            break;
            default:
                $tableauVueSur->addValuesFromArrayLinked($arrayTemp, 4,  "",  "style='font-size:12px;width:200px;'");
                
                
                if ($i>0)
                {
                    $html=$tableauVueSur->createHtmlTableFromArray(4,  "",  "",  "",  "");
                }
            break;
        }
        
        return $html;
    }
    
    // ************************************************************************************************************************************************************
    // fonction renvoyant le code html contenant les images pris depuis par rapport a l'idImage passé en parametre
    // ************************************************************************************************************************************************************
    public function getImagesPrisDepuis($params=array())
    {
        $html="";
        
        $adresse = new archiAdresse();
        
        $format = 'mini';
        if (isset($params['format']))
            $format = $params['format'];
        
        
        $reqPrisDepuis = "SELECT idAdresse FROM _adresseImage WHERE idImage = '".$params['idImage']."' AND prisDepuis='1'";
        $resPrisDepuis = $this->connexionBdd->requete($reqPrisDepuis);
        $tableauPrisDepuis = new tableau();
        $arrayTemp=array();
        $i=0;
        while ($fetchPrisDepuis=mysql_fetch_assoc($resPrisDepuis)) {
            $nomAdresse = $adresse->getIntituleAdresseFrom($fetchPrisDepuis['idAdresse'],  'idAdresse');
            $infosImage = $adresse->getUrlImageFrom($fetchPrisDepuis['idAdresse'],  $format,  'AND ei.position=1');
            if ($infosImage['idHistoriqueImage']=='0') {
                $infosImage = $adresse->getUrlImageFrom($fetchPrisDepuis['idAdresse'],  $format);
            }
            
            $arrayTemp[$i]['celluleHaut']="<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchPrisDepuis['idAdresse']))."'><img src='".$infosImage['url']."' alt=''></a>";
            $arrayTemp[$i]['celluleBas']="<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchPrisDepuis['idAdresse']))."'>".$nomAdresse."</a>";
            $i++;
        }
        
        $tableauPrisDepuis->addValuesFromArrayLinked($arrayTemp, 4,  "",  "style='font-size:12px;width:200px;'");
    
    
        if ($i>0) {
            $html=$tableauPrisDepuis->createHtmlTableFromArray(4,  "",  "",  "",  "");
        }

        
        return $html;
    }
    
    // ************************************************************************************************************************************************************
    // fonction qui permet de recuperer les evenements lies à une image
    // ************************************************************************************************************************************************************
    public function afficherEvenementsLies($idImage=0)
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeEvenementsLies'=>'listeEvenementsLiesAImage.tpl')));
        
        $reqEvenementsLies="
            SELECT he.titre
            FROM historiqueEvenement heb, historiqueEvenement he
            RIGHT JOIN _evenementImage ei ON ei.idEvenement=he.idEvenement
            WHERE he.idTypeEvenement != '3'
            AND heb.idTypeEvenement != '3'
            AND heb.idEvenement = he.idEvenement
            AND ei.idImage = '".$idImage."'
            GROUP BY he.idEvenement, he.idHistoriqueEvenement
            HAVING he.idHistoriqueEvenement = max(heb.idHistoriqueEvenement)
        ";
        
        $resEvenementsLies = $this->connexionBdd->requete($reqEvenementsLies);
        
        while ($fetchEvenementsLies=mysql_fetch_assoc($resEvenementsLies)) {
            $t->assign_block_vars('evenementsLies',  array('titre'=>$fetchEvenementsLies['titre']));
        }
                
        ob_start();
        $t->pparse('listeEvenementsLies');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // ************************************************************************************************************************************************************
    // fonction qui permet de recuperer les adresses liees à une image
    // ************************************************************************************************************************************************************
    public function afficherAdressesLiees($idImage=0)
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeAdresses'=>'listeAdressesLiesAImage.tpl')));
        
        $authentification = new archiAuthentification();
        
        if ($authentification->estConnecte()) {
            $t->assign_block_vars('isConnected',  array());
        }
        
        
        
        
        $reqAdressesLiees="
        SELECT ha.numero as numero,  ai.etage as etage, 
        ha.idRue as idRue,  ha.idSousQuartier as idSousQuartier,  ha.idQuartier as idQuartier,  ha.idVille as idVille,  ha.idPays as idPays, 
        ai.prisDepuis as prisDepuis,  ai.seSitue as seSitue,  ai.hauteur as hauteur, ai.idAdresse as idAdresse
        
        FROM historiqueAdresse hab,  historiqueAdresse ha
        
        RIGHT JOIN _adresseImage ai ON ai.idAdresse = ha.idAdresse
        
        LEFT JOIN rue r         ON r.idRue = ha.idRue
        LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = ha.idSousQuartier 
        LEFT JOIN quartier q        ON q.idQuartier = ha.idQuartier 
        LEFT JOIN ville v        ON v.idVille = ha.idVille 
        LEFT JOIN pays p        ON p.idPays = ha.idPays
        
        WHERE ai.idImage = '".$idImage."'
            AND hab.idAdresse = ha.idAdresse 
        
        GROUP BY ha.idAdresse,  ha.idHistoriqueAdresse
        
        HAVING ha.idHistoriqueAdresse = max(hab.idHistoriqueAdresse)
        ";
        
        
        $resAdressesLiees=$this->connexionBdd->requete($reqAdressesLiees);
        $adresses = new archiAdresse();
        $recherche = new archiRecherche();
        $arrayListeIdAdresses=array();
        while ($fetch = mysql_fetch_assoc($resAdressesLiees)) {
            $nomAdressePrisDepuis         = $adresses->getNomAdresse($adresses->getArrayAdresseFromIdAdresse($fetch['prisDepuis']));
            $nomAdresseSeSitue             = $adresses->getNomAdresse($adresses->getArrayAdresseFromIdAdresse($fetch['seSitue']));
            
            $t->assign_block_vars('adressesLiees',  array(
                            'intitule'                =>$adresses->getAdresseToDisplay($fetch), 
                            'hauteur'                =>$fetch['hauteur'], 
                            'etage'                    =>$fetch['etage'], 
                            'prisDepuis'            =>$fetch['prisDepuis'], 
                            'idAdresse'             =>$fetch['idAdresse'], 
                            'urlPopupSeSitue'         => "#", 
                            'onClickPopupSeSitue'    => "document.getElementById('paramChampsAppelantAdresse').value='seSitue_".$fetch['idAdresse']."';document.getElementById('calqueAdresse').style.display='block';", 
                            'urlPopupPrisDepuis'    => "#", 
                            'onClickPopupPrisDepuis'=> "document.getElementById('paramChampsAppelantAdresse').value='prisDepuis_".$fetch['idAdresse']."';document.getElementById('calqueAdresse').style.display='block';", 
                            
                            'prisDepuisTxt'            =>$nomAdressePrisDepuis, 
                            'seSitueTxt'            =>$nomAdresseSeSitue
                            ));
            $arrayListeIdAdresses[]=$fetch['idAdresse'];
        }
        
        $t->assign_vars(array(    'formAction'            =>$this->creerUrl('modifImageAdressesLiees',  '',  array('archiIdImage'=>$idImage)), 
                                'listeIdAdresses'        =>implode(',  ',  $arrayListeIdAdresses), 
                                'popupAdresses'         => $recherche->getPopupChoixAdresse('resultatRechercheAdresseCalqueImageChampMultipleRetourSimple')        
        ));
        
        
        ob_start();
        $t->pparse('listeAdresses');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }    

    // ************************************************************************************************************************************************************
    // recuperation des photos qui montrent aussi l'adresse courante a partir d'autres adresses
    public function getAutresPhotosVuesSurAdresse($listeAdresses=array(),  $format='mini',  $params=array())
    {
        $html="";
        $adresse = new archiAdresse();
        $string = new stringObject();
        $bbCode = new bbCodeObject();
        
        
        $idAdresseCourante = 0;
        if (isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='') {
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        }
        
        
        $sqlOneImage="";
        if (isset($params['getOneIdImageFromEvenement']) && $params['getOneIdImageFromEvenement']==true) {
            $sqlOneImage = "AND ai.idImage='".$params['idImage']."' AND ee.idEvenementAssocie=".$params['idEvenement']." ";
        }
        
        $sqlListeAdresses="";
        if (isset($listeAdresses) && !isset($params['getOneIdImageFromEvenement'])) {
            $sqlListeAdresses = "AND ai.idAdresse IN (".implode(",  ",  $listeAdresses).") ";
        }
        
        $sqlNoDisplayIdImages="";
        if (isset($params['noDiplayIdImage']) && count($params['noDiplayIdImage'])>0) {
            $sqlNoDisplayIdImages = "AND ai.idImage NOT IN (".implode(",  ",  $params['noDiplayIdImage']).")";
        
        }
        
        
        $sqlGroupeAdresse = "";
        if (isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='0') {
            $sqlGroupeAdresse = "AND ai.idEvenementGroupeAdresse = '".$params['idEvenementGroupeAdresse']."'";
        }
        
        $idEvenementGroupeAdresseEvenementAffiche="";
        $divIdEvenementGroupeAdresseEvenementAffiche="";
        if (isset($params['idGroupeAdresseEvenementAffiche'])) {
            $idEvenementGroupeAdresseEvenementAffiche=$params['idGroupeAdresseEvenementAffiche'];
            $divIdEvenementGroupeAdresseEvenementAffiche = "_".$params['idGroupeAdresseEvenementAffiche'];
        }
        
        
        // recherche des photos :
        $reqPhotos = "
                        SELECT hi1.idHistoriqueImage, hi1.idImage as idImage,  hi1.dateUpload, ai.idAdresse, hi1.description, ae.idEvenement as idEvenementGroupeAdresseCourant
                        FROM historiqueImage hi2,  historiqueImage hi1
                        LEFT JOIN _adresseImage ai ON ai.idImage = hi1.idImage
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ai.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        WHERE hi2.idImage = hi1.idImage
                        $sqlListeAdresses
                        $sqlOneImage
                        $sqlNoDisplayIdImages
                        $sqlGroupeAdresse
                        AND ai.vueSur='1'
                        GROUP BY hi1.idImage,  hi1.idHistoriqueImage
                        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
        ";
        
        
        
        $resPhotos = $this->connexionBdd->requete($reqPhotos);
        $tabPhotos=array();
        
        while ($fetchPhotos = mysql_fetch_assoc($resPhotos)) {
            $tabPhotos[$fetchPhotos['idImage']]['infosImage'] = array(
                            'idHistoriqueImage'=>$fetchPhotos['idHistoriqueImage'], 
                            'idImage'=>$fetchPhotos['idImage'], 
                            'dateUpload'=>$fetchPhotos['dateUpload'], 
                            'description'=>$fetchPhotos['description'], 
                            'idAdresse'=>$fetchPhotos['idAdresse'], 
                            'idEvenementGroupeAdresseCourant'=>$fetchPhotos['idEvenementGroupeAdresseCourant']
            );
            
        }
        
        $i=0;
        $tabTemp=array();
        $tableau = new tableau();
        $zones=array();
        foreach ($tabPhotos as $idImage => $valuesPhoto) {
        
            // la photo comporte t elle des zones pointant sur l'adresse courante :
            $reqVerifZone = "
            SELECT 0
            FROM _adresseImage ai
            WHERE idImage='".$valuesPhoto['infosImage']['idImage']."'
            AND idEvenementGroupeAdresse='".$params['idGroupeAdresseEvenementAffiche']."'
            AND vueSur='1'
            AND coordonneesZoneImage<>''
            AND largeurBaseZoneImage<>''
            AND longueurBaseZoneImage<>''
            ";
            $resVerifZone = $this->connexionBdd->requete($reqVerifZone);
            $isZonesOnImageForGA = false;
            if (mysql_num_rows($resVerifZone)>0) {
                $isZonesOnImageForGA = true;
            }
        
        
            // recherche de l'adresse depuis laquelle a ete prise la photo
            $reqPriseDepuis = "SELECT ai.idAdresse,  ai.idEvenementGroupeAdresse
                                FROM _adresseImage ai
                                WHERE ai.idImage = ".$valuesPhoto['infosImage']['idImage']."
                                AND ai.prisDepuis='1'
            ";
            $resPriseDepuis = $this->connexionBdd->requete($reqPriseDepuis);
            $liensAdresses=array();
            $intituleAdresse = "";
            
            if (isset($valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']) && $valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']!='' && $valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']!='0') {
                $intituleAdressePhoto = $adresse->getIntituleAdresseFrom($valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant'],  'idEvenementGroupeAdresse');
            } else {
                $intituleAdressePhoto = $adresse->getIntituleAdresseFrom($valuesPhoto['infosImage']['idAdresse'],  'idAdresse');
            }
            
            
            if (mysql_num_rows($resPriseDepuis)>0) {
                while ($fetchPriseDepuis = mysql_fetch_assoc($resPriseDepuis))
                {
                    if ($fetchPriseDepuis['idEvenementGroupeAdresse']=='0' || $fetchPriseDepuis['idEvenementGroupeAdresse']=='') {
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($fetchPriseDepuis['idAdresse'],  'idAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'idAdresseReference'=>$listeAdresses[0]));
                    } else {
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($fetchPriseDepuis['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'idAdresseReference'=>$listeAdresses[0]));
                    }
                    
                    if (trim($intituleAdresse)== '') {
                        $liensAdresses[] = "?";
                    } else {
                        if ($fetchPriseDepuis['idEvenementGroupeAdresse']=='0' || $fetchPriseDepuis['idEvenementGroupeAdresse']=='') {
                            $liensAdresses[] = "<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchPriseDepuis['idAdresse']))."'>".$intituleAdresse."</a>";
                        } else {
                            $liensAdresses[] = "<a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$fetchPriseDepuis['idEvenementGroupeAdresse'],  'archiIdAdresse'=>$fetchPriseDepuis['idAdresse']))."'>".$intituleAdresse."</a>";
                        }
                    }
                }
            } else {
                $liensAdresses[] = "?";
            }
            
            
            $intituleAdresseUrl = $intituleAdresse;
            
            $descriptionImage="<br>".$bbCode->convertToDisplay(array('text'=>$valuesPhoto['infosImage']['description']));
            
        
            if (isset($params['setZoomOnImageZone']) && $params['setZoomOnImageZone']==true && $isZonesOnImageForGA) {
                if ($idAdresseCourante !=0)
                {
                    $tabTemp[$i]['celluleBas'] = "<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant'],  'archiIdAdresse'=>$idAdresseCourante))."'><img src='".$this->getUrlImage()."Advisa/loupe.png"."' alt='' /></a>&nbsp;Pris depuis ".implode(" / ",  $liensAdresses).$descriptionImage;
                } else {
                    $tabTemp[$i]['celluleBas'] = "<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']))."'><img src='".$this->getUrlImage()."Advisa/loupe.png"."' alt='' /></a>&nbsp;Pris depuis ".implode(" / ",  $liensAdresses).$descriptionImage;
                }
            } else {
                $tabTemp[$i]['celluleBas'] = "Pris depuis ".implode(" / ",  $liensAdresses).$descriptionImage;
            }
            
            $sizes = getimagesize($this->getCheminPhysique()."images/".$format."/".$valuesPhoto['infosImage']['dateUpload']."/".$valuesPhoto['infosImage']['idHistoriqueImage'].".jpg");
            $arrayZones = $this->getDivsAndMapsZonesImagesVueSur(array("idImage"=>$valuesPhoto['infosImage']['idImage'],  'largeurImageCourante'=>$sizes[0],  'longueurImageCourante'=>$sizes[1],  'idHTMLImage'=>'imageEvenement_'.$valuesPhoto['infosImage']['idImage'],  'idHTMLMap'=>'mapZone_'.$valuesPhoto['infosImage']['idImage'].$divIdEvenementGroupeAdresseEvenementAffiche,  "idGroupeAdresseCourant"=>$idEvenementGroupeAdresseEvenementAffiche));
            
            
            if ($idAdresseCourante !=0) {
                $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant'],  'archiIdAdresse'=>$idAdresseCourante));
            } else {
                $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']));
            }
            
            // construction du test pour IE ... si les zones de cliques ne sont pas visible ,  on permet le clique pour voir le detail de l'image ,  sinon c'est que l'on a cliqué sur une zone,  ou en tout cas l'une de celles ci est visible
            $arrayTestDivsJs=array();
            foreach ($arrayZones['arrayNomsIDDivsZones'] as $indice => $value) {
                $arrayTestDivsJs[]="document.getElementById('".$value."').style.display=='none'";
            }

            $htmlOnClick="";
            if (isset($params['setZoomOnImageZone']) && $params['setZoomOnImageZone']==true && $isZonesOnImageForGA) {
                $htmlOnClick=$url;
            }
            elseif (count($arrayTestDivsJs)>0) {
                if (isset($this->variablesGet['afficheSelectionImagePrincipale']) && $this->variablesGet['afficheSelectionImagePrincipale']=='1')
                {
                    $htmlOnClick=$this->creerUrl('enregistreSelectionImagePrincipale',  'evenement',  array('idEvenement'=>$this->variablesGet['idEvenement'],  'idImage'=>$valuesPhoto['infosImage']['idImage']));
                } else {
                    $htmlOnClick=$url;
                }
            } else {
                if (isset($this->variablesGet['afficheSelectionImagePrincipale']) && $this->variablesGet['afficheSelectionImagePrincipale']=='1')
                {
                    $htmlOnClick=$this->creerUrl('enregistreSelectionImagePrincipale',  'evenement',  array('idEvenement'=>$this->variablesGet['idEvenement'],  'idImage'=>$valuesPhoto['infosImage']['idImage']));
                } else {
                    $htmlOnClick=$url;
                }
            }
            
            
            $tabTemp[$i]['celluleHaut'] = '<div><a href="'.$htmlOnClick.'"><span class="imgResultGrp"><div class="imgResultHover"><img itemprop="image" id="image'.$valuesPhoto['infosImage']['idHistoriqueImage'].$divParamIdGroupeAdresseAffiche.'"  alt="'.htmlspecialchars($alt).'"  src="'.
            'photos--'.$valuesPhoto['infosImage']['dateUpload'].'-'.$valuesPhoto['infosImage']['idHistoriqueImage'].
            '-moyen.jpg'.'" class="eventImage" /><p>'.strip_tags($bbCode->convertToDisplay(array('text'=>$valuesPhoto['infosImage']['description']))).'</p></div></span><br></a></div>';
            
            $zones[$i] = $arrayZones['htmlDivs'];
            $zones[$i] .=$arrayZones['htmlMaps'];
            $zones[$i] .=$arrayZones['htmlJs'];
            
            
            $i++;
        
        }
        
        $htmlZones = "";
        foreach ($zones as $indice => $valueZone) {
            $htmlZones.=$valueZone;
        }
        
        if (isset($params['getOneIdImageFromEvenement']) && $params['getOneIdImageFromEvenement']==true) {
            // dans le cas ou on est aller chercher une seule idImage ( pour l'affichage des images dispatchée par date d'image et d'evenement par exemple ,  on renvoie un table d'1 seule colonne pour la mise en page
            /*$tableau->addValuesFromArrayLinked($tabTemp, 1,  "align=center",  "style='font-size:12px;width:200px;'");
            if ($i>0) {
                $html=$tableau->createHtmlTableFromArray(1,  "",  "",  "",  "");
            }*/
            if ($i>0) {
                $html="<table><tr><td style='padding:0;'>";
                $html.=$tabTemp[0]['celluleHaut'];
                $html.="</td></tr><tr><td>";
                $html.=$tabTemp[0]['celluleBas'];
                $html.="</td></tr></table>";
            }
        } else {
            $tableau->addValuesFromArrayLinked($tabTemp, 3,  "align='center'",  "style='font-size:12px;width:200px;'");
            if ($i>0) {
                $html=$tableau->createHtmlTableFromArray(3,  "",  "",  "",  "");
            }
        }

        return array('htmlVueSur'=>$html,  'htmlZonesDivMapJs'=>$htmlZones);
    }
    
    // ************************************************************************************************************************************************************
    // recuperation des photos qui entourent l'adresse courante
    public function getAutresPhotosPrisesDepuisAdresse($listeAdresses=array(),  $format='mini',  $params=array())
    {
        $adresse = new archiAdresse();
        $string = new stringObject();
        $bbCode = new bbCodeObject();
        
        
        $sqlGroupeAdresse = "";
        if (isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='0') {
            $sqlGroupeAdresse = "AND ai.idEvenementGroupeAdresse = '".$params['idEvenementGroupeAdresse']."'";
        }
        
        $idAdresseCourante = 0;
        if (isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='') {
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        }
        
        
        
        
        $html="";
        
        // recherche des photos :
        $reqPhotos = "
                        SELECT hi1.idHistoriqueImage, hi1.idImage as idImage,  hi1.dateUpload, ai.idAdresse, hi1.description, ae.idEvenement as idEvenementGroupeAdresseCourant
                        FROM historiqueImage hi2,  historiqueImage hi1
                        LEFT JOIN _adresseImage ai ON ai.idImage = hi1.idImage
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ai.idAdresse
                        WHERE hi2.idImage = hi1.idImage
                        AND ai.idAdresse IN (".implode(",  ",  $listeAdresses).")
                        AND ai.prisDepuis='1'
                        $sqlGroupeAdresse
                        GROUP BY hi1.idImage,  hi1.idHistoriqueImage
                        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
        ";
        
        $resPhotos = $this->connexionBdd->requete($reqPhotos);
        $tabPhotos=array();
        
        while ($fetchPhotos = mysql_fetch_assoc($resPhotos)) {
            // on verifie que cette photo n'est pas deja affichée dans la liste des evenements courant,  en tant qu'image vueSur ,  sinon cela va faire doublon
            if (isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && $this->isImagePriseDepuisAndVueSurOnSameGroupeAdresse(array('idImage'=>$fetchPhotos['idImage'],  'idEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']))) {
                 // on ajoute pas la photo au tableau
            } else {
                $tabPhotos[$fetchPhotos['idImage']]['infosImage'] = array(
                                'idHistoriqueImage'=>$fetchPhotos['idHistoriqueImage'], 
                                'idImage'=>$fetchPhotos['idImage'], 
                                'dateUpload'=>$fetchPhotos['dateUpload'], 
                                'description'=>$fetchPhotos['description'], 
                                'idAdresse'=>$fetchPhotos['idAdresse'], 
                                'idEvenementGroupeAdresseCourant'=>$fetchPhotos['idEvenementGroupeAdresseCourant']
                );
            }
            
        }
        
        $i=0;
        $tabTemp=array();
        $tableau = new tableau();
        foreach ($tabPhotos as $idImage => $valuesPhoto) {
            // recherche de l'adresse depuis laquelle a ete prise la photo
            $reqPriseDepuis = "SELECT ai.idAdresse,  ai.idEvenementGroupeAdresse
                                FROM _adresseImage ai
                                WHERE ai.idImage = ".$valuesPhoto['infosImage']['idImage']."
                                AND ai.vueSur='1'
            ";
            $resPriseDepuis = $this->connexionBdd->requete($reqPriseDepuis);
            $liensAdresses=array();
            $intituleAdresse = "";
            $intituleAdressePhoto = $adresse->getIntituleAdresseFrom($valuesPhoto['infosImage']['idAdresse'],  'idAdresse');
            if (mysql_num_rows($resPriseDepuis)>0) {
                while ($fetchPriseDepuis = mysql_fetch_assoc($resPriseDepuis))
                {
                    if ($fetchPriseDepuis['idEvenementGroupeAdresse']!='0' && $fetchPriseDepuis['idEvenementGroupeAdresse']!='') {
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($fetchPriseDepuis['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'idAdresseReference'=>$listeAdresses[0]));
                    } else {
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($fetchPriseDepuis['idAdresse'],  'idAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'idAdresseReference'=>$listeAdresses[0]));
                        
                    }
                    
                    if (trim($intituleAdresse)== '') {
                        $liensAdresses[] = "?";
                    } else {
                        if ($fetchPriseDepuis['idEvenementGroupeAdresse']!='0' && $fetchPriseDepuis['idEvenementGroupeAdresse']!='') {
                            $liensAdresses[] = "<a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$fetchPriseDepuis['idEvenementGroupeAdresse'],  'archiIdAdresse'=>$fetchPriseDepuis['idAdresse']))."'>".$intituleAdresse."</a>";
                        } else {
                            $liensAdresses[] = "<a href='".$this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchPriseDepuis['idAdresse']))."'>".$intituleAdresse."</a>";
                        }
                    }
                }
            } else {
                $liensAdresses[] = "?";
            }
            
            
            $intituleAdresseUrl = $intituleAdresse;
            
            $descriptionImage="<br>".$bbCode->convertToDisplay(array('text'=>$valuesPhoto['infosImage']['description']));
            
        
            
            $tabTemp[$i]['celluleBas'] = "Vue sur ".implode(" / ",  $liensAdresses).$descriptionImage;
                        
            $sizes = getimagesize($this->getCheminPhysique()."images/".$format."/".$valuesPhoto['infosImage']['dateUpload']."/".$valuesPhoto['infosImage']['idHistoriqueImage'].".jpg");
            $arrayZones = $this->getDivsAndMapsZonesImagesVueSur(array("idImage"=>$valuesPhoto['infosImage']['idImage'],  'largeurImageCourante'=>$sizes[0],  'longueurImageCourante'=>$sizes[1],  'idHTMLImage'=>'imageEvenement_'.$valuesPhoto['infosImage']['idImage'],  'idHTMLMap'=>'mapZone_'.$valuesPhoto['infosImage']['idImage']));
            
            if (isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='') {
                // on revient sur le groupe d'adresse courant
                if ($idAdresseCourante!=0)
                {
                    $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$this->variablesGet['archiIdEvenementGroupeAdresse'],  'archiIdAdresse'=>$idAdresseCourante));
                } else {
                    $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$this->variablesGet['archiIdEvenementGroupeAdresse']));
                }
            } else {
                // on revient sur le groupe d'adresse de l'image
                if ($idAdresseCourante!=0)
                {
                    
                    $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant'],  'archiIdAdresse'=>$idAdresseCourante));
                } else {
                    $url = $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $valuesPhoto['infosImage']['idImage'],  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$valuesPhoto['infosImage']['idEvenementGroupeAdresseCourant']));
                }
            }
            
            // construction du test pour IE ... si les zones de cliques ne sont pas visible ,  on permet le clique pour voir le detail de l'image ,  sinon c'est que l'on a cliqué sur une zone,  ou en tout cas l'une de celles ci est visible
            $arrayTestDivsJs=array();
            foreach ($arrayZones['arrayNomsIDDivsZones'] as $indice => $value) {
                $arrayTestDivsJs[]="document.getElementById('".$value."').style.display=='none'";
            }
            $htmlOnClick="";
            if (count($arrayTestDivsJs)>0) {
                $htmlOnClick=$url;
            } else {
                if (isset($this->variablesGet['afficheSelectionImagePrincipale']) && $this->variablesGet['afficheSelectionImagePrincipale']=='1')
                {
                    $htmlOnClick=$this->creerUrl('enregistreSelectionImagePrincipale',  'evenement',  array('idEvenement'=>$this->variablesGet['idEvenement'],  'idImage'=>$valuesPhoto['infosImage']['idImage']));                
                } else {
                    $htmlOnClick=$url;
                }
            }
            
            $tabTemp[$i]['celluleHaut'] = '<div><a href="'.$htmlOnClick.'"><span class="imgResultGrp"><div class="imgResultHover"><img itemprop="image" id="image'.$valuesPhoto['infosImage']['idHistoriqueImage'].$divParamIdGroupeAdresseAffiche.'"  alt="'.htmlspecialchars($alt).'"  src="'.
            'photos--'.$valuesPhoto['infosImage']['dateUpload'].'-'.$valuesPhoto['infosImage']['idHistoriqueImage'].
            '-moyen.jpg'.'" class="eventImage" /><p>'.strip_tags($bbCode->convertToDisplay(array('text'=>$valuesPhoto['infosImage']['description']))).'</p></div></span><br></a></div>';
            $tabTemp[$i]['celluleHaut'] .=$arrayZones['htmlDivs'];
            $tabTemp[$i]['celluleHaut'] .=$arrayZones['htmlMaps'];
            $tabTemp[$i]['celluleHaut'] .=$arrayZones['htmlJs'];

            $i++;
        }
        
        
        $tableau->addValuesFromArrayLinked($tabTemp, 3,  "align='center'",  "style='font-size:12px;width:200px;'");
        
        if ($i>0) {
            $html.=$tableau->createHtmlTableFromArray(3,  "",  "",  "",  "");
        }
        
        return $html;
    }
    
    // est ce que l'image appartenant au groupe d'adresse donné est a la fois vue sur et prise depuis ?
    public function isImagePriseDepuisAndVueSurOnSameGroupeAdresse($params = array())
    {
        $retour = false;
        if (isset($params['idImage']) && $params['idImage']!='' && isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && $params['idEvenementGroupeAdresse']!='0') {
            $isPrisDepuis = false;
            $isVueSur = false;
            
            $reqPrisDepuis = "SELECT 0 FROM _adresseImage WHERE idImage = '".$params['idImage']."' AND idEvenementGroupeAdresse='".$params['idEvenementGroupeAdresse']."' AND prisDepuis='1'";
            $resPrisDepuis = $this->connexionBdd->requete($reqPrisDepuis);
            if (mysql_num_rows($resPrisDepuis)>0) {
                $isPrisDepuis = true;
            }
            
            $reqVueSur = "SELECT 0 FROM _adresseImage WHERE idImage = '".$params['idImage']."' AND idEvenementGroupeAdresse='".$params['idEvenementGroupeAdresse']."' AND vueSur='1'";
            $resVueSur = $this->connexionBdd->requete($reqVueSur);
            if (mysql_num_rows($resVueSur)>0) {
                $isVueSur = true;
            }
            
            if ($isPrisDepuis && $isVueSur) {
                $retour = true;
            }
        }
        
        return $retour;
    }
    
    // ************************************************************************************************************************************************************
    public function afficherFromEvenement($idEvenement=0,  $params=array())
    {
        /*
        **    IMAGES LIÉES
        */
        //$t=new Template('modules/archi/templates/');
        //$t->set_filenames(array('listeImages'=>'listeImagesAssocies.tpl'));
        $zonesHTML="";
        $string = new stringObject();
        $adresse = new archiAdresse();
        $evenement = new archiEvenement();
        $bbCode = new bbCodeObject();
        $authentification = new archiAuthentification();
        
        
        // dans le cas de plusieur groupe d'adresse sur la meme page avec la meme photo affichee sur chacun,  on recupere le groupe d'adresse pour pouvoir identifier separement l'affichage des divs de la zone
        $paramIdGroupeAdresseEvenementAffiche="";
        $divParamIdGroupeAdresseAffiche = "";
        if (isset($params['idGroupeAdresseEvenementAffiche']) && $params['idGroupeAdresseEvenementAffiche']!='' && $params['idGroupeAdresseEvenementAffiche']!='0') {
            $paramIdGroupeAdresseEvenementAffiche=$params['idGroupeAdresseEvenementAffiche'];
            $divParamIdGroupeAdresseAffiche="_".$params['idGroupeAdresseEvenementAffiche'];
        }
        
        $idAdresseCourante = 0;
        if (isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='') {
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        }
        
        
        // si le parametre est defini on n'affiche pas sur la liste des photos de l'evenement en cours d'affichage,  les photos qui sont affichées en tant que vue sur et prise depuis l'adresse courante
        $listeImagesDejaAffichees=array();
        
        // adresses a lesquelles appartient l'evenement courant
        $listeIdAdresses = array();
        $reqAdresses = "
                SELECT distinct ha1.idAdresse as idAdresse
                FROM historiqueAdresse ha2,  historiqueAdresse ha1
                
                LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$idEvenement."'
                LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                
                WHERE ha2.idAdresse = ha1.idAdresse
                AND ha1.idAdresse = ae.idAdresse
                GROUP BY ha1.idAdresse,  ha1.idHistoriqueAdresse
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            ";
        $resAdresses = $this->connexionBdd->requete($reqAdresses);
        
        while ($fetchAdresses = mysql_fetch_assoc($resAdresses)) {
            $listeIdAdresses[] = $fetchAdresses['idAdresse'];
        }
        
        
        if (isset($params['withoutImagesVuesSurPrisesDepuis']) && $params['withoutImagesVuesSurPrisesDepuis']==true) {

            if(!empty($listeIdAdresses)) {
                // on recupere la liste des images qui sont deja affichee dans les encarts vueSur et prisDepuis
                $reqImagesDejaAffichees = "
                        SELECT distinct ai.idImage
                        FROM _adresseImage ai
                        LEFT JOIN _evenementImage ei ON ei.idImage = ai.idImage
                        WHERE 
                            idAdresse IN (".implode(',  ',  $listeIdAdresses).")
                        AND ai.vueSur = '1'
                        OR ai.prisDepuis = '1'
                ";
                
                $resImagesDejaAffichees = $this->connexionBdd->requete($reqImagesDejaAffichees);
                
                while ($fetchImagesDejaAffichees = mysql_fetch_assoc($resImagesDejaAffichees))
                {
                    $listeImagesDejaAffichees[] = $fetchImagesDejaAffichees['idImage'];
                }
            }
        }
        
        
        $sqlVueSurPrisDepuisWhereParam="";
        if (count($listeImagesDejaAffichees)>0) {
            $sqlVueSurPrisDepuisWhereParam="AND ei.idImage NOT IN (".implode(",  ",  $listeImagesDejaAffichees).")";
        }
        
        // recuperation des images
        $reqImages = "
        SELECT hi1.idImage,  hi1.idHistoriqueImage,  hi1.nom,  hi1.description,  hi1.dateUpload,  hi1.dateCliche
        FROM _evenementImage ei
        LEFT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
        LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
        WHERE ei.idEvenement = '".$idEvenement."'
        $sqlVueSurPrisDepuisWhereParam
        GROUP BY hi1.idImage ,  hi1.idHistoriqueImage
        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
        ORDER BY ei.position, hi1.idHistoriqueImage
        ";
        
        $resImages = $this->connexionBdd->requete($reqImages);
        $listeImages=array();
        while ($fetchImages = mysql_fetch_assoc($resImages)) {
            $listeImages[$fetchImages['idImage']] = array(
                                                            'idImage'=>$fetchImages['idImage'], 
                                                            'idHistoriqueImage'=>$fetchImages['idHistoriqueImage'], 
                                                            'nom'=>$fetchImages['nom'], 
                                                            'description'=>$fetchImages['description'], 
                                                            'dateUpload'=>$fetchImages['dateUpload'], 
                                                            'dateCliche'=>$fetchImages['dateCliche']
            );
        }
        
        // recuperation d'une adresse pour chacune des images pour l'affichage de l'adresse dans l'url de la photo
        $tab = new tableau();
        foreach ($listeImages as $idImage => $valuesImage) {
        
            $reqAdresse = "    SELECT ha1.numero as numero, 
                                    r.nom as nomRue, 
                                    sq.nom as nomSousQuartier, 
                                    q.nom as nomQuartier, 
                                    v.nom as nomVille, 
                                    p.nom as nomPays, 
                                    ha1.numero as numeroAdresse,  
                                    ha1.idRue, 
                                    r.prefixe as prefixeRue, 
                                    IF (ha1.idSousQuartier != 0,  ha1.idSousQuartier,  r.idSousQuartier) AS idSousQuartier, 
                                    IF (ha1.idQuartier != 0,  ha1.idQuartier,  sq.idQuartier) AS idQuartier, 
                                    IF (ha1.idVille != 0,  ha1.idVille,  q.idVille) AS idVille, 
                                    IF (ha1.idPays != 0,  ha1.idPays,  v.idPays) AS idPays
                                    
                            
                            FROM historiqueAdresse ha2,  historiqueAdresse ha1
                            
                            LEFT JOIN _evenementImage ei ON ei.idImage = '".$idImage."'
                            LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                            LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                            
                            
                            
                            LEFT JOIN rue r ON r.idRue = ha1.idRue
                            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = if (ha1.idRue='0' and ha1.idSousQuartier!='0' , ha1.idSousQuartier , r.idSousQuartier )
                            LEFT JOIN quartier q ON q.idQuartier = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' , ha1.idQuartier , sq.idQuartier )
                            LEFT JOIN ville v ON v.idVille = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' , ha1.idVille , q.idVille )
                            LEFT JOIN pays p ON p.idPays = if (ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' , ha1.idPays , v.idPays )
                            
                            
                            WHERE ha2.idAdresse = ha1.idAdresse
                            
                            AND ha1.idAdresse = ae.idAdresse
                            GROUP BY ha1.idAdresse,  ha1.idHistoriqueAdresse
                            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            LIMIT 1
            ";
            
            $resAdresse = $this->connexionBdd->requete($reqAdresse);
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            
            $intituleAdresse = trim($adresse->getIntituleAdresse($fetchAdresse));
            $intituleAdresseAlt = trim(strip_tags(str_replace("'",  " ",  $intituleAdresse)));
            
            $title = trim($string->sansBalises(strip_tags(stripslashes($valuesImage['description']))).' '.$intituleAdresseAlt);
            $alt = trim($string->sansBalises(strip_tags(stripslashes($valuesImage['description']))).' '.$intituleAdresseAlt);
            
            
            $imageHTML="";
            if ($authentification->estConnecte() && isset($this->variablesGet['afficheSelectionImage']) && $this->variablesGet['afficheSelectionImage']=='1') {
                $hrefImage="";
                $onClickImage="if (document.getElementById('checkboxSelectionImages_".$valuesImage['idHistoriqueImage']."').checked){document.getElementById('checkboxSelectionImages_".$valuesImage['idHistoriqueImage']."').checked=false;document.getElementById('image".$valuesImage['idHistoriqueImage'].$divParamIdGroupeAdresseAffiche."').parentNode.style.border='none';}else{document.getElementById('checkboxSelectionImages_".$valuesImage['idHistoriqueImage']."').checked=true;document.getElementById('image".$valuesImage['idHistoriqueImage'].$divParamIdGroupeAdresseAffiche."').parentNode.style.border ='thin solid #FF8800';}";
                $imageHTML .="<input type='checkbox' name='checkboxSelectionImages[]' id='checkboxSelectionImages_".$valuesImage['idHistoriqueImage']."' value='".$idEvenement."_".$valuesImage['idHistoriqueImage']."' style='display:none;'>";
            }
            elseif ($authentification->estConnecte() && isset($this->variablesGet['afficheSelectionImagePrincipale']) && $this->variablesGet['afficheSelectionImagePrincipale']=='1') {
                $hrefImage = "href='".$this->creerUrl('enregistreSelectionImagePrincipale',  'evenement',  array('idEvenement'=>$this->variablesGet['idEvenement'],  'idImage'=>$idImage))."'";
                $onClickImage = "";
                $imageHTML .="";
            } else {
                if ($idAdresseCourante !=0)
                {
                    $hrefImage = "href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $idImage,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$idEvenement,  'archiIdAdresse'=>$idAdresseCourante))."'";
                } else {
                    $hrefImage = "href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage' => $idImage,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$idEvenement))."'";
                }
                $onClickImage = "";
            }
                
            //href="'.$hrefImage.'"
            $formatAffichagePhoto = "moyen";
            $largeurImage="";
            $sizes=array();
            // pour gerer les format panoramiques ,  on va afficher l'image au format "grand" ,  l'image au format petit est trop flou
            if (file_exists($this->getCheminPhysiqueImage("mini").$valuesImage['dateUpload']."/".$valuesImage['idHistoriqueImage'].".jpg")) {
                $arrayImageSize = getimagesize($this->getCheminPhysiqueImage("mini").$valuesImage['dateUpload']."/".$valuesImage['idHistoriqueImage'].".jpg");
                if ($arrayImageSize[0]/$arrayImageSize[1]>2.5)
                {
                    $formatAffichagePhoto = "grand";
                }
                $arrayImageSizeAff = getimagesize($this->getCheminPhysique()."images/$formatAffichagePhoto/".$valuesImage['dateUpload']."/".$valuesImage['idHistoriqueImage'].".jpg");
                $sizes['x'] = $arrayImageSizeAff[0];
                $sizes['y'] = $arrayImageSizeAff[1];
            }
            /*
             * echo '<a class="imgResultGrp" href="'.$config->creerUrl(
                '', 'imageDetail', array('archiRetourAffichage'=>'evenement',
                'archiRetourIdName'=>'idEvenement',
                'archiIdImage'=>$image['idImage'],
                'archiIdAdresse'=>$image['idAdresse'],
                'archiRetourIdValue'=>$image['idEvenement'])
            ).'"><div class="imgResult"></div>
            <div class="imgResultHover"><img src="'.
            'photos--'.$image['dateUpload'].'-'.$image['idHistoriqueImage'].
            '-moyen.jpg'.'" alt="" /><p>'.strip_tags(
                $bbcode->convertToDisplay(
                    array('text'=>$image['description'])
                )
            ).'</p></div></a>';*/
            $imageHTML .= '<a class="imgResultGrp" '.$hrefImage.'><div class="imgResultHover"><img itemprop="image" onclick="'.$onClickImage.'" id="image'.$valuesImage['idHistoriqueImage'].$divParamIdGroupeAdresseAffiche.'"  alt="'.htmlspecialchars($alt).'"  src="'.
            'photos--'.$valuesImage['dateUpload'].'-'.$valuesImage['idHistoriqueImage'].
            '-moyen.jpg'.'" class="eventImage" /><p>'.strip_tags($bbCode->convertToDisplay(array('text'=>$valuesImage['description']))).'</p></div></a><div class="imgDesc">'.$bbCode->convertToDisplay(array('text'=>$valuesImage['description'])).'</div><br>';//src=\'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$valuesImage['dateUpload'].'-'.$valuesImage['idHistoriqueImage'].'-'.$formatAffichagePhoto.'.jpg\'
        
            $tab->addValue($imageHTML,  '',  $sizes);
        }
        
        // recuperation des images VuesSur classée suivant la date ,  si la date correspond,  on l'affiche dans l'evenement
        // pour l'instant on va les afficher a la suite des autres images...
        // par contre on ne les affiche pas quand on est en mode de selection d'image (pour la deplacer d'un evenement à l'autre
        if (isset($params['imagesVuesSurLinkedByDate']) && count($params['imagesVuesSurLinkedByDate'])>0   && (!isset($this->variablesGet['afficheSelectionImage']) || $this->variablesGet['afficheSelectionImage']!='1') ) {
            $i=0;
            foreach ($params['imagesVuesSurLinkedByDate'] as $indice => $valuesImage) {
                // ici on se sert de getAutresPhotosVuesSurAdresse uniquement pour renvoyer l'affichage de la photo avec les infos concernant celle ci
                $arrayRetourVueSur = $this->getAutresPhotosVuesSurAdresse($listeIdAdresses,  'moyen',  array('getOneIdImageFromEvenement'=>true,  'idImage'=>$valuesImage['idImage'],  'idEvenement'=>$idEvenement,  'idGroupeAdresseEvenementAffiche'=>$paramIdGroupeAdresseEvenementAffiche,  'setZoomOnImageZone'=>true));
                $tab->addValue($arrayRetourVueSur["htmlVueSur"],  "");
                $zonesHTML.=$arrayRetourVueSur['htmlZonesDivMapJs'];
                $i++;
            }
        }
        
        
        
        if ($authentification->estConnecte()) {
            // si on est connecté ,  on laisse un padding top pour que les photos ne se chevauches pas avec le menu des evenements
            //$html = "<div style='display:table;padding-top:50px;'>".$tab->createHtmlTableFromArray(3,  '',  '',  'align="center" valign="top" style="font-size:13px;"')."</div>";
            $html = "<div class='gallery'>".$tab->createHtmlDivsFromArray(array("styleDivs"=>"text-align:center;display:table;padding-left:5px;padding-bottom:5px;position:relative; width:130px; height:130px;",  "nbColonnes"=>4))."<div style='clear:both;'></div></div>";
        } else {
            //$html = "<div style='display:table;'>".$tab->createHtmlTableFromArray(3,  '',  '',  'align="center" valign="top" style="font-size:13px;"')."</div>";
            $html = "<div class='gallery'>".$tab->createHtmlDivsFromArray(array("styleDivs"=>"display:table;padding-left:5px;padding-bottom:5px;position:relative; width:130px; height:130px;",  "nbColonnes"=>5))."<div style='clear:left;'></div></div>";
        }
        
        return $html.$zonesHTML;

    }    
    
    // ************************************************************************************************************************************************************
    public function afficherFromAdresse($idAdresse=0)
    {
        /*
        **    IMAGES LIEES
        */
        
        $t=new Template('modules/archi/templates/');
        $t->set_filenames(array('listeImages'=>'listeImagesAssocies.tpl'));
        
        $rep = $this->connexionBdd->requete('
            SELECT  hI.idImage,  hI.idHistoriqueImage,  hI.nom,  hI.description, hI.dateUpload
                 FROM _adresseImage _eI
            RIGHT JOIN historiqueImage hI  ON hI.idImage  = _eI.idImage
            RIGHT JOIN historiqueImage hI2 ON hI2.idImage = _eI.idImage
            WHERE _eI.idAdresse='.$idAdresse.' 
            GROUP BY hI.idImage, hI.idHistoriqueImage HAVING hI.idHistoriqueImage=MAX(hI2.idHistoriqueImage) ORDER BY hI.idHistoriqueImage ASC');
            
        if (mysql_affected_rows() > 0) {
            $t->assign_block_vars('imAsso',  array());
        }

        while ( $res = mysql_fetch_object($rep)) {
            $t->assign_block_vars('imAsso.associe',  array(
                'lien' => $this->creerUrl('',  'imageDetail',  array(
                                            'archiIdImage' => $res->idImage, 
                                            'archiRetourAffichage'=>'adresseDetail', 
                                            'archiRetourIdName'=>'archiIdAdresse', 
                                            'archiRetourIdValue'=>$idAdresse
                                            )), 
                'urlImage' => $this->getUrlImage("moyen").$res->dateUpload.'/'.$res->idHistoriqueImage.'.jpg', 
                'description' => stripslashes($res->description)));
        }
        
        ob_start();
        $t->pparse('listeImages');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // affiche le formulaire d'ajout d'une image suivant les ecrans : adresse,  evenements.
    public function afficherFormulaireAjout($id=0,  $type='')
    {
    
    // debug fabien 24-07-2011 mis à jour le 29/11/2011 car le problème de copie persiste...
    
    echo "<B>IMPORTANT : </B>Pour copier une image sur le serveur <b>il faut cliquez ci-dessous sur parcourir</b> puis choisir la photo dans vos dossiers et enfin cliquer sur 'upload'<br>";
//    echo "Maintenance en cours du transfert de fichier,  le problème sera résolu d'ici quelques jours.<br>";
//    echo "L'ensemble des fonctionnalité du site reste accessible.<br>";
//    echo "<br><a href='http://www.archi-strasbourg.org'>Revenir au site www.archi-strasbourg.org</a><br><br>";
//    echo "Merci pour votre compréhension.<br>";
//    echo "F. Romary";
//    exit;
    
    
        /*
        **    IMAGES Formulaire
        */
        set_time_limit(0);
        $html="";


        
        // si la personne est connectée ,  on cree le repertoire temporaire des images pour l'upload multiple
        $authentifie = new archiAuthentification();
        $nomRepertoireTemporaire=$authentifie->getIdUtilisateur().'-'.time();

        if (!is_dir($this->getCheminPhysique()."/images/uploadMultiple/".$nomRepertoireTemporaire)) {
            mkdir($this->getCheminPhysique()."/images/uploadMultiple/".$nomRepertoireTemporaire);        
            chmod($this->getCheminPhysique()."/images/uploadMultiple/".$nomRepertoireTemporaire,  0777);
        }

        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('imageForm'=>'imageUploadFormulaire.tpl')));
        
        $adresse = new archiAdresse();
        $evenement = new archiEvenement();
        
        $idEvenementGroupeAdresse = $evenement->getParent($this->variablesGet['archiIdEvenement']);
        $idEvenementCourant = $this->variablesGet['archiIdEvenement'];
        
        if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
            $person= new archiPersonne();
            $infos=$person->getInfosPersonne($idPerson);
            $t->assign_vars(array('recapitulatifAdresses'=>"<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>"));
        } else {
            $t->assign_vars(array("recapitulatifAdresses"=>$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse)));
        }
        
        $t->assign_vars(array("recapitulatifHistoriqueEvenements"=>$evenement->afficherRecapitulatifAncres($idEvenementGroupeAdresse,  $idEvenementCourant)));
        
        $t->assign_vars(array("liensModifEvenements"=>$evenement->afficherLiensModificationEvenement($idEvenementCourant)));
        
        $t->assign_vars(array('cheminImages'=>$this->getUrlImage()));
        $t->assign_vars(array('liaisonImage'=>$type));
        $t->assign_vars(array('formulaireRetour'=>$type));
        $t->assign_vars(array('idCourant'=>$id));
        $t->assign_vars(array('formActionAjoutImage'=>$this->creerUrl('ajoutImage')));
        //$t->assign_vars(array('pathImg'=>$this->cheminUploadMultipleApplet)); // param1 de l'applet
        //$t->assign_vars(array('idOffre'=>$nomRepertoireTemporaire)); // param2 de l'applet
        $t->assign_vars(array('cheminUploadMultiple'=>$nomRepertoireTemporaire));

        // mise en commentaire du transfert multiple (applet java) by fabien 29/11/2011
        /*
        $codeApplet="
        <!-- The following code will only be interpreted by IE --> 
        <!--[if IE]> <!-->  
        <object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" width=\"500\" height=\"500\" name=\"FtpApplet\"> 
        <param name=\"java_code\" value=\"FtpApplet.class\" />  
        <param name=\"java_codebase\" value=\"includes/\" />  
        <param name=\"java_archive\" value=\"sFtpApplet.jar\" />  
        <param name=\"type\" value=\"application/x-java-applet;version=1.5\" /> 

        <param name=\"pathImg\" value=\"".$this->cheminUploadMultipleApplet."\" />\n
        <param name=\"idOffre\" value=\"".$nomRepertoireTemporaire."\" />\n
        <param name=\"functionCalledOnExit\" value=\"validFormulaire\" />\n
        <param name=\"mayscript\" value=\"true\" />


        <!--<![endif]--> 
        <!-- The following code will NOT be interpreted by IE --> 
        <!--[if !IE]> <!-->  
        <object classid=\"java:FtpApplet.class\" type=\"application/x-java-applet\" archive=\"includes/sFtpApplet.jar\" width=\"500\" height=\"500\">  
        <!-- Konqueror browser needs the following param -->  
        <param name=\"archive\" value=\"includes/sFtpApplet.jar\" />
        <param name=\"pathImg\" value=\"".$this->cheminUploadMultipleApplet."\" />\n
        <param name=\"idOffre\" value=\"".$nomRepertoireTemporaire."\" />\n
        <param name=\"functionCalledOnExit\" value=\"validFormulaire\" />\n
        <param name=\"mayscript\" value=\"true\" />
        <!--<![endif]--> 
        <span style='font-size:11px;color:red;'>Attention,  java n'est pas installé ou la version installée est trop ancienne,  installez le en <a href='http://www.java.com/fr/download/' target='_blank'>cliquant ici</a>,  vérifiez aussi que votre navigateur accepte le Java<br>sinon vous pouvez aussi ajouter vos photos une par une en cliquant sur l'option 'une image'.</span>        
        </object>

        ";
        
        
        $t->assign_vars(array('appletJava'=>$codeApplet));
        
        
        
        $t->assign_vars(array('popupAttente'=>$this->getPopupAttente()));
        
        $t->assign_vars(array('msgUploadMultiple'=>"<h3>Aide pour l'ajout de photo multiple</h3><h2>en 3 étapes</h2>1 - Glisser et déposez vos photos dans la liste ci dessous.<br>2 - Cliquez sur 'Lancez le transfert'<br>3 - Une fois les transferts achevés vous serez automatiquement redirigé<br><br><br>Vous pouvez charger des fichiers au format gif ou jpg"));
        
        */
        
        ob_start();                            // debug fabien 29/11/2011 (mise en commentaire de la ligne)

        
        $t->pparse('imageForm');
        $html=ob_get_contents();
        ob_end_clean();                    // debug fabien 25/07/2011 (mise en commentaire de la ligne)
        
        return $html;
    }
    
    //  *****************************************************************************************************************************************************************
    //  affiche le formulaire de modification de photos multiple ,  on lui passe un liste d'idImage dans le tableau arrayListeIdImages
    //  *****************************************************************************************************************************************************************
    public function afficherFormulaireModification($id=0,  $type='',  $arrayListeIdImages=array())
    {
        $html="";
        
        $utilisateur = new archiUtilisateur();
        $authentification = new archiAuthentification();
        
        //echo "afficherFormulaireModification : id=".$id." type=".$type;
        $arrayModifUrlParams = array();
        
        echo "<script>
        
            function retirerPrisDepuis(idAdresseValue, identifiantUniqueRetour) {
                document.getElementById('listePrisDepuisDiv'+identifiantUniqueRetour).innerHTML='';
                
                if (idAdresseValue!=0)
                {
                    selectField = document.getElementById('prisDepuis'+identifiantUniqueRetour);
                    divField = document.getElementById('listePrisDepuisDiv'+identifiantUniqueRetour);
                    for(i=0 ; i<selectField.options.length; i++ ) {
                        if (selectField.options[i]!=null) {
                            if (selectField.options[i].value==idAdresseValue)
                            {
                                indiceARetirer = i;
                            }
                            else
                            {
                                divField.innerHTML+=selectField.options[i].innerHTML+'<a href=\'#\' style=\'cursor:pointer;\' onclick=\"retirerPrisDepuis(\''+selectField.options[i].value+'\',  '+identifiantUniqueRetour+')\">(-)</a><br>';
                            }
                        }
                    }
                    
                    selectField.options[indiceARetirer]=null;
                }
            
            }
            
            
            function retirerVueSur(idAdresseValue, identifiantUniqueRetour) {
                document.getElementById('listeVueSurDiv'+identifiantUniqueRetour).innerHTML='';
                
                if (idAdresseValue!=0)
                {
                    selectField = document.getElementById('vueSur'+identifiantUniqueRetour);
                    divField = document.getElementById('listeVueSurDiv'+identifiantUniqueRetour);
                    for(i=0 ; i<selectField.options.length; i++ ) {
                        if (selectField.options[i]!=null) {
                            if (selectField.options[i].value==idAdresseValue)
                            {
                                indiceARetirer = i;
                            }
                            else
                            {
                                divField.innerHTML+=selectField.options[i].innerHTML+'<a href=\'#\' style=\'cursor:pointer;\' onclick=\\\"retirerVueSur(\''+selectField.options[i].value+'\',  '+identifiantUniqueRetour+')\\\">(-)</a><br>';
                            }
                        }
                    }
                    
                    selectField.options[indiceARetirer]=null;
                }
            
            }
            </script>
        ";
        
        
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('imageFormModif'=>'imageFormulaire.tpl')));
        /******
        ** Source - récupration
        **/
        $sqlSource = 'SELECT idSource,  nom,  idTypeSource FROM source';
        $tabSource = array();
        if ($result = $this->connexionBdd->requete($sqlSource)) {
            while ($rep = mysql_fetch_object($result)) {
                $tabSource[$rep->idSource] = $rep->nom;
            }
        }
    
        switch($type) {
            case 'adresse':
            break;
            case 'evenement':
                // recherche des images liées a l'evenement
                $arrayListeIdImages=array();
                if (isset($this->variablesGet['archiIdEvenement']) && $this->variablesGet['archiIdEvenement']!='0' && $this->variablesGet['archiIdEvenement']!='')
                {
                    $evenement = new archiEvenement();
                    $adresse = new archiAdresse();
                    // affichages des recapitulatifs en haut de la page ( adresse + titres des evenements)
                    $idEvenementGroupeAdresse = $evenement->getParent($this->variablesGet['archiIdEvenement']);
                    $idEvenementCourant = $this->variablesGet['archiIdEvenement'];
                    if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                        $person= new archiPersonne();
                        $infos=$person->getInfosPersonne($idPerson);
                        $t->assign_vars(array('recapitulatifAdresses'=>"<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>"));
                    } else {
                        $t->assign_vars(array("recapitulatifAdresses"=>$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse)));
                    }
                    
                    $t->assign_vars(array("recapitulatifHistoriqueEvenements"=>$evenement->afficherRecapitulatifAncres($idEvenementGroupeAdresse,  $idEvenementCourant)));
                    
                    $t->assign_vars(array("liensModifEvenements"=>$evenement->afficherLiensModificationEvenement($idEvenementCourant)));
                    
                    $reqEvenement = "SELECT distinct idImage from _evenementImage WHERE idEvenement ='".$this->variablesGet['archiIdEvenement']."' ORDER BY position";
                    $resEvenement = $this->connexionBdd->requete($reqEvenement);
                    while ($fetchEvenement = mysql_fetch_assoc($resEvenement)) {
                        $arrayListeIdImages[] = $fetchEvenement['idImage'];
                    }
                }
            
            break;
            
            default:
                if (count($arrayListeIdImages)==1) // cas de la modification d'une seule image ,  on va chercher l'evenement qu'elle illustre pour afficher le recapitulatif de l'adresse
                {
                    $evenement = new archiEvenement();
                    $adresse = new archiAdresse();
                    if (isset($this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse']!='') {
                        $idEvenementGroupeAdresse = $this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse'];
                        $arrayModifUrlParams = array('archiIdEvenementGroupeAdresseAffichageAdresse'=>$this->variablesGet['archiIdEvenementGroupeAdresseAffichageAdresse']);
                    } else {
                        $idEvenementImageUnique = $this->getArrayIdEvenementFromIdImage($arrayListeIdImages[0]);
                        $idEvenementGroupeAdresse = $evenement->getParent($idEvenementImageUnique);
                    }
                    if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                        $person= new archiPersonne();
                        $infos=$person->getInfosPersonne($idPerson);
                        $t->assign_vars(array('recapitulatifAdresses'=>"<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>"));
                    } else {
                        $t->assign_vars(array("recapitulatifAdresses"=>$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse)));
                    }
                }
            break;
        }
        
        //echo "afficherFormulaireModification $type";
        // cas ou le ne precise pas de idCourant ni de type de liaison ( la photo n'est ni liee a un evenement ni une adresse)
        // on modifie une liste d'images
        // *****************************************************************************************************************************************************************
        if (count($arrayListeIdImages)>0) {
            $t->assign_block_vars('isImages',  array());
        
        
            $listeIdImagesAModifier=implode("',  '",  $arrayListeIdImages);
            
            $requeteImages = "
                SELECT 
                    hi1.idHistoriqueImage as idHistoriqueImage, hi1.idImage as idImage, hi1.nom as nom,  
                    hi1.dateUpload as dateUpload, hi1.dateCliche as dateCliche, hi1.description as description, hi1.tags as tags,
                    hi1.idUtilisateur as idUtilisateur, u.nom as nomUtilisateur,  u.prenom as prenomUtilisateur, 
                    hi1.idSource as idSource,  hi1.isDateClicheEnviron as isDateClicheEnviron, 
                    hi1.numeroArchive as numeroArchive, 
                    if (_ai.idImage IS NULL,  0, 1) as isAdresseImage, 
                    if (_ei.idImage IS NULL,  0, 1) as isEvenementImage, 
                    
                    _ai.seSitue as seSitue, 
                    _ai.prisDepuis as prisDepuis, 
                    _ai.etage as etage, 
                    _ai.hauteur as hauteur, 
                    _ai.idAdresse as idAdresse, 
                    _ei.idEvenement as idEvenement
                    
                FROM historiqueImage hi2, historiqueImage hi1
                LEFT JOIN _evenementImage _ei ON _ei.idImage = hi1.idImage
                LEFT JOIN _adresseImage _ai ON _ai.idImage = hi1.idImage
                LEFT JOIN utilisateur u ON u.idUtilisateur = hi1.idUtilisateur
                
                WHERE hi2.idImage = hi1.idImage
                AND hi1.idImage IN ('".$listeIdImagesAModifier."')
                GROUP BY hi1.idImage, hi1.idHistoriqueImage
                HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) 

            ";
            $resImage=$this->connexionBdd->requete($requeteImages);
            
            $i=0;
            $nomUtilisateur ="";
            $prenomUtilisateur ="";
            $listeId=array();
            $adresseObject = new archiAdresse();
            
            
            
            
            // ********************************************************************************************************************************************
            // boucle sur les images
            // ********************************************************************************************************************************************
            while ($fetch=mysql_fetch_array($resImage)) {
                if ($i==0)
                {
                    $nomUtilisateur = $fetch["nomUtilisateur"];
                    $prenomUtilisateur = $fetch["prenomUtilisateur"];
                }
                
                $dateCliche="";
                if ($this->date->toFrench($fetch['dateCliche'])=='00/00/0000')
                {
                    $dateCliche = "";
                } else {
                    $dateCliche = $this->date->toFrench($fetch['dateCliche']);
                }
                
                

                
                
                // ***********************************************************
                // GESTION AFFICHAGE ADRESSES CHAMPS MULTIPLES
                // gestion de l'affichage des adresses de chaque photo
                //$arrayGereAffichage = $this->gereAffichageAdresses($fetch['idHistoriqueImage'],  $fetch['idImage'],  'modif');
                // ***********************************************************
                //$nbAdressesAffichees = $arrayGereAffichage['nbAdressesAffichees'];
                
                $adresseObject = new archiAdresse();
                $stringObject = new stringObject();
                // adresses prisDepuis
                $reqPrisDepuis="SELECT idImage, idAdresse, idEvenementGroupeAdresse FROM _adresseImage WHERE idImage='".$fetch['idImage']."' AND prisDepuis='1'";
                $resPrisDepuis = $this->connexionBdd->requete($reqPrisDepuis);

                $selectPrisDepuisHTML = "";
                $divPrisDepuisHTML = "";
                while ($fetchPrisDepuis = mysql_fetch_assoc($resPrisDepuis))
                {
                    if ($fetchPrisDepuis['idEvenementGroupeAdresse']!='0') {
                        $nomAdresse = $adresseObject->getIntituleAdresseFrom($fetchPrisDepuis['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
                    } else {
                        $nomAdresse = $adresseObject->getIntituleAdresseFrom($fetchPrisDepuis['idAdresse'],  'idAdresse');
                    }
                    $selectPrisDepuisHTML .= "<option value='".$fetchPrisDepuis['idAdresse']."_".$fetchPrisDepuis['idEvenementGroupeAdresse']."' SELECTED>".$nomAdresse."</option>";
                    $divPrisDepuisHTML .= $nomAdresse."<a onclick=\"retirerPrisDepuis('".$fetchPrisDepuis['idAdresse']."_".$fetchPrisDepuis['idEvenementGroupeAdresse']."',  ".$fetch['idHistoriqueImage'].");\" style='cursor:pointer;'>(-)</a><br>";
                }
                
                
                // adresses vueSur
                $reqVueSur="SELECT idImage, idAdresse, idEvenementGroupeAdresse FROM _adresseImage WHERE idImage='".$fetch['idImage']."' AND vueSur='1'";
                $resVueSur = $this->connexionBdd->requete($reqVueSur);
                
                $selectVueSurHTML = "";
                $divVueSurHTML = "";
                while ($fetchVueSur = mysql_fetch_assoc($resVueSur))
                {
                    if ($fetchVueSur['idEvenementGroupeAdresse']!='0') {
                        $nomAdresse = $adresseObject->getIntituleAdresseFrom($fetchVueSur['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true));
                    } else {
                        $nomAdresse = $adresseObject->getIntituleAdresseFrom($fetchVueSur['idAdresse'],  'idAdresse');
                    }
                    $selectVueSurHTML .= "<option value='".$fetchVueSur['idAdresse']."_".$fetchVueSur['idEvenementGroupeAdresse']."' SELECTED>".$nomAdresse."</option>";
                    $divVueSurHTML .= $nomAdresse."<a onclick=\"retirerVueSur('".$fetchVueSur['idAdresse']."_".$fetchVueSur['idEvenementGroupeAdresse']."',  ".$fetch['idHistoriqueImage'].");\" style='cursor:pointer;'>(-)</a><br>";
                }
                
                
                
                // ***********************************************************
                // RECUPERATION DES SOURCES
                // on recupere le nom de la source
                // ***********************************************************
                $reqSource="
                            select s.idSource as idSource, s.nom as nom,  ts.nom as nomTS ,  if (ts.nom<>'', concat('(', ts.nom,  ')'),  '')as nomTypeSource
                            from source s
                            left join typeSource ts ON ts.idTypeSource = s.idTypeSource
                            where idSource = '".$fetch['idSource']."'";
                            
                $resSource = $this->connexionBdd->requete($reqSource);
                $fetchSource=mysql_fetch_assoc($resSource);
                // ***********************************************************

                
                // ***********************************************************
                // recuperation des données POST,  avec par defaut les valeurs de la base de données
                // ***********************************************************
                $checkIsDateClicheEnviron=0;
                if ($fetch['isDateClicheEnviron']==1)
                    $checkIsDateClicheEnviron=1;
                    
                $tabForm = array(
                                        'nom_'.$fetch['idHistoriqueImage']=>array('type'=>'text',  'required'=>false,  'value'=>'',  'default'=>$fetch['nom']), 
                                        'description_'.$fetch['idHistoriqueImage']=>array('type'=>'text',  'required'=>false,  'value'=>'',  'default'=>$fetch['description']), 
                                        'dateUpload_'.$fetch['idHistoriqueImage']=>array('type'=>'date',  'required'=>false,  'value'=>'',  'default'=>$this->date->toFrench($fetch['dateUpload'])), 
                                        'dateCliche_'.$fetch['idHistoriqueImage']=>array('type'=>'date',  'required'=>false,  'value'=>'',  'default'=>$this->date->toFrench($fetch['dateCliche'])), 
                                        'isDateClicheEnviron_'.$fetch['idHistoriqueImage']=>array('type'=>'checkbox',  'required'=>false,  'value'=>'',  'default'=>$checkIsDateClicheEnviron), 
                                        
                                        'source'.$fetch['idHistoriqueImage']=>array('type'=>'text',  'required'=>false,  'value'=>'',  'default'=>$fetch['idSource']), 
                                        'source'.$fetch['idHistoriqueImage'].'txt'=>array('type'=>'text',  'required'=>false,  'value'=>'',  'default'=>$fetchSource['nom'].' '.$fetchSource['nomTypeSource'].''), 
                                        'numeroArchive_'.$fetch['idHistoriqueImage']=>array('type'=>'text',  'required'=>false,'value'=>'',  'default'=>$fetch['numeroArchive'])
                                        
                                );
                                    
                                    
                $formulaire = new formGenerator();    
                // appel de la fonction recuperant les valeurs du formulaire
                $errors = $formulaire->getArrayFromPost($tabForm);
                // ***********************************************************
                
                // ***********************************************************
                // assignation des champs de l'adresse
                // ***********************************************************
                $popupPrisDepuis = new calqueObject(array('idPopup'=>'popupPrisDepuis'.$fetch['idHistoriqueImage']));
                $popupVueSur = new calqueObject(array('idPopup'=>'popupVueSur'.$fetch['idHistoriqueImage']));
                
                
                $checkIsDateClicheEnviron = '';
                if ($tabForm['isDateClicheEnviron_'.$fetch['idHistoriqueImage']]['value']=='1')
                    $checkIsDateClicheEnviron = 'checked';
                    
                $auteur=$this->getAuteur($fetch['idImage']);
                if (is_array($auteur)) {
                    $nomUpload=$auteur["nom"];
                    $nomAuteur="";
                } else {
                    $nomAuteur=$auteur;
                    $nomUpload="";
                }
                
                $licence=$this->getLicence($fetch['idImage']);
                $idUtilisateur=$authentification->getIdUtilisateur();
                $utilisateur=new ArchiUtilisateur();
                if ($licence["id"]==3 && !$utilisateur->isAuthorized('admin_licences', $idUtilisateur) && !$utilisateur->canCopyright(array('idUtilisateur'=>$idUtilisateur))) {
                    $selectLicenceHTML="<span title='".htmlspecialchars(_("Seul l'auteur peut changer la licence de cette image."), ENT_QUOTES)."'>".$licence["name"]."</span>";
                    $enableAuthor="disabled='disabled'";
                } else {
                    $enableAuthor="";
                    $reqLicence=$this->connexionBdd->requete("SELECT * FROM licences");
                    $selectLicenceHTML="";
                    while ($fetchLicence=mysql_fetch_assoc($reqLicence)) {
                        if ($fetchLicence["id"]!=3 || $utilisateur->isAuthorized('admin_licences', $idUtilisateur) || $utilisateur->canCopyright(array('idUtilisateur'=>$idUtilisateur))) {
                            $selectLicenceHTML.="<input type='radio' value='".$fetchLicence["id"]."' id='licence_".$fetchLicence["id"]."' name='licence_".$fetch['idHistoriqueImage']."'";
                            if ($fetchLicence["id"]==$licence["id"]) {
                                $selectLicenceHTML.=" checked='checked' ";
                            }
                            $selectLicenceHTML.="/>";
                            $selectLicenceHTML.="<label title='".htmlspecialchars($fetchLicence["description"], ENT_QUOTES)."' for='licence_".$fetchLicence["id"]."'>";
                            if (!empty($fetchLicence["link"])) {
                                $selectLicenceHTML.="<a href='".$fetchLicence["link"]."'>";
                            }
                            $selectLicenceHTML.=$fetchLicence["name"];
                            if (!empty($fetchLicence["link"])) {
                                $selectLicenceHTML.="</a>";
                            }
                            $selectLicenceHTML.="</label>";
                        }
                    }
                }
                
                $t->assign_block_vars('listePhotos',  array(
        'onClickPopupPrisDepuis'=>"document.getElementById('".$popupPrisDepuis->getJSDivId()."').style.top=(getScrollHeight()+70)+'px';".$popupPrisDepuis->getJSOpenPopup($fetch['idHistoriqueImage'])."document.getElementById('".$popupPrisDepuis->getJSIFrameId()."').src='".$this->creerUrl('',  'recherche',  array('noHeaderNoFooter'=>1,  'modeAffichage'=>'popupRechercheAdressePrisDepuis'))."';", 
        'popupPrisDepuis'   =>$popupPrisDepuis->getDiv(array('lienSrcIFrame'=>$this->creerUrl('',  'recherche',  array('noHeaderNoFooter'=>1,  'modeAffichage'=>'popupRechercheAdressePrisDepuis')),  'width'=>750,  'height'=>500,  'left'=>10,  'top'=>70,  'titre'=>'archi-strasbourg.org : Pris Depuis')), 
        'selectPrisDepuis'=> $selectPrisDepuisHTML, 
        'listePrisDepuisDiv'=>$divPrisDepuisHTML, 
        'onClickPopupVueSur'=>"document.getElementById('".$popupVueSur->getJSDivId()."').style.top=(getScrollHeight()+70)+'px';".$popupVueSur->getJSOpenPopup($fetch['idHistoriqueImage'])."document.getElementById('".$popupVueSur->getJSIFrameId()."').src='".$this->creerUrl('',  'recherche',  array('noHeaderNoFooter'=>1,  'modeAffichage'=>'popupRechercheAdresseVueSur'))."';", 
        'popupVueSur'=>$popupVueSur->getDiv(array('lienSrcIFrame'=>$this->creerUrl('',  'recherche',  array('noHeaderNoFooter'=>1,  'modeAffichage'=>'popupRechercheAdresseVueSur')),  'width'=>750,  'height'=>500,  'left'=>10,  'top'=>70,  'titre'=>'archi-strasbourg.org : Vue Sur')), 
        'selectVueSur'=>$selectVueSurHTML, 
        'listeVueSurDiv'=>$divVueSurHTML, 
        
        'urlImage'            =>$this->getUrlImage("grand").'/'.$fetch['dateUpload'].'/'.$fetch['idHistoriqueImage'].'.jpg', 
        'nom'                =>$tabForm['nom_'.$fetch['idHistoriqueImage']]['value'], 
        'description'        =>stripslashes($tabForm['description_'.$fetch['idHistoriqueImage']]['value']), 
        'idHistoriqueImage'    =>$fetch['idHistoriqueImage'], 
        'dateUpload'        =>$tabForm['dateUpload_'.$fetch['idHistoriqueImage']]['value'], 
        'dateCliche'        =>$tabForm['dateCliche_'.$fetch['idHistoriqueImage']]['value'], 
        'numeroArchive'        =>$tabForm['numeroArchive_'.$fetch['idHistoriqueImage']]['value'], 
        'checkIsDateClicheEnviron' => $checkIsDateClicheEnviron, 
        'idImage'            =>$fetch['idImage'], 
        
        
        'adresseUrl'        =>'#', 
        
        'adresseOnClick'    =>"document.getElementById('calqueAdresse').style.display='block';document.getElementById('paramChampsAppelantAdresse').value='listeAdresses_".$fetch['idHistoriqueImage']."';", 
        
        'evenementUrl'        =>'#', 
        
        'evenementOnClick'    =>"document.getElementById('calqueEvenement').style.display='block';document.getElementById('paramChampsAppelantEvenement').value='listeEvenements_".$fetch['idHistoriqueImage']."';", 
        
        
        'onClickBoutonAjouterAdresse'=> "document.getElementById('modifImage').action='".$this->creerUrl('',  'modifierImageMultiple')."'", 
        
        'onClickBoutonEnleverAdresse'=> "document.getElementById('modifImage').action='".$this->creerUrl('',  'modifierImageMultiple')."'", 
        
        'onClickBoutonChoixVille'        =>"document.getElementById('calqueVille').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueVille').style.display='block';document.getElementById('paramChampAppelantVille').value='ville".$fetch['idHistoriqueImage']."';document.getElementById('paramChampVilleIdentifiantUnique').value='".$fetch['idHistoriqueImage']."'", 

        'onChangeListeQuartier'            =>"appelAjax('".$this->creerUrl('',  'afficheSelectSousQuartier',  array('noHeaderNoFooter'=>1,  'identifiantUnique'=>$fetch['idHistoriqueImage']))."&archiIdQuartier='+document.getElementById('quartiers".$fetch['idHistoriqueImage']."').value,  'listeSousQuartier".$fetch['idHistoriqueImage']."')", 
        
        'onClickBoutonChoixSource'=>"document.getElementById('calqueSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueSource').style.display='block';document.getElementById('paramChampsAppelantSource').value='source_".$fetch['idHistoriqueImage']."';", 
        
        
        'onClickDateCliche'=>"document.getElementById('paramChampAppelantDate').value='dateCliche_".$fetch['idHistoriqueImage']."';document.getElementById('calqueDate').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDate').style.display='block';", 
        
        'idSource'=>$tabForm['source'.$fetch['idHistoriqueImage']]['value'], 
        
        'nomSource'=>$tabForm['source'.$fetch['idHistoriqueImage'].'txt']['value'],
        
        "nomAuteur"=>$nomAuteur,
        "nomUpload"=>$nomUpload,
        "tags"=>$fetch['tags'],
        "selectLicence"=>$selectLicenceHTML,
        "enableAuthor"=>$enableAuthor
        
        
                ));
                if ($utilisateur->canChangeNumeroArchiveField(array('idUtilisateur'=>$authentification->getIdUtilisateur())))
                {
                    $t->assign_block_vars('listePhotos.isDisplayNumeroArchive',  array());
                } else {
                    $t->assign_block_vars('listePhotos.isNoDisplayNumeroArchive',  array());
                }
                
                if ($utilisateur->canModifyTags(array('idUtilisateur'=>$authentification->getIdUtilisateur())))
                {
                    $t->assign_block_vars('listePhotos.canModifyTags',  array());
                } else {
                    $t->assign_block_vars('listePhotos.canNotModifyTags',  array());
                }
                
                if ($utilisateur->isAuthorized('affiche_selection_source',  $authentification->getIdUtilisateur()))
                {
                    $t->assign_block_vars('listePhotos.isDisplaySource',  array());
                } else {
                    $t->assign_block_vars('listePhotos.isNoDisplaySource',  array());
                }
                
                
                // ***************************************************
                // IDENTIFIANT d'HISTORIQUES IMAGES
                // recuperation de la liste des identifiants d'images
                $listeId[]=$fetch['idHistoriqueImage'];
                // ***************************************************
                

                
                // ***************************************************
                // SOURCES
                // création de la liste des sources
                // ***************************************************
                if (!empty($tabSource))
                {
                    foreach ($tabSource AS $id => $nom) {
                        if (isset($tabTravail['source']['value']) && $tabTravail['source']['value'] == $id)
                            $selected='selected="selected"';
                        else
                            $selected='';
                        $t->assign_block_vars('listePhotos.source',  array('val'=> $id,  'nom'=> $nom,  'selected'=> $selected));
                    }
                }
                
                // recuperation des evenements lies à l'image
                $resEvenementsLies=$this->getFetchEvenementsLies($fetch['idImage']);
                while ($fetchEvenementsLies = mysql_fetch_assoc($resEvenementsLies))
                {
                    $t->assign_block_vars('listePhotos.evenements',  array('value'=>$fetchEvenementsLies['idEvenement'],  'nom'=>$fetchEvenementsLies['titre']));
                }
                
                $i++;
            }
            
            // ***************************************************
            $t->assign_vars(array(    "proprietaireImages"=>"Images concernant l'adresse", 
                                    'actionFormImage'=>$this->creerUrl('modifImage',  '',  $arrayModifUrlParams), 
                                    'listeId'=> implode(',  ',  $listeId)
                                ));
            // ***************************************************
            

            
            // ***************************************************
            // pour les calques :
            // ***************************************************
            $recherche = new archiRecherche();
            $adresse = new archiAdresse();
            
            
            $t->assign_vars(array(
                'popupChoixAdresse'   => $recherche->getPopupChoixAdresse('resultatRechercheAdresseCalqueImageChampMultiple'), 
                'popupChoixEvenement' => $recherche->getPopupChoixEvenement('resultatRechercheEvenementCalqueImageChampMultiple'), 
                'popupChoixSource'=>$recherche->getPopupChoixSource('modifImage'), 
                'popupCalendrier'=>$this->getPopupCalendrier(), 
                'popupAttente'=>$this->getPopupAttente()
            ));
            
            
            
        } else {
            $t->assign_vars(array('msgPasdImage'=>"Il n'y a pas d'image."));
        }
        
        
        // *********************************************************************************
        // recuperation des aides contextuelles
        $helpMessages = $this->getHelpMessages('helpImage');
        $helpMessages = array_merge($helpMessages,  $this->getHelpMessages('helpAdresse'));
        foreach ($helpMessages as $fieldName => $message) {
            $t->assign_vars(array($fieldName => $message));
        }
        
        // *********************************************************************************
        
        
        
        ob_start();
        $t->pparse('imageFormModif');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    // *****************************************************************************************************************************************************************************
    // gestion des adresses sur le formulaire d'une image
    // identifiantUniqueImage = est l'identifiant unique pour l'ensemble de la gestion de(s)  l'adresses liée(s) a l'image ,  dans le cas des image on utilise l'idHistoriqueImage
    // idImage est l'identifiant d'une image pour recuperer les adresses liees a cette image
    // *****************************************************************************************************************************************************************************
    public function gereAffichageAdresses($identifiantUniqueImage=0,  $idImage=0,  $modeRecuperationDonnees='ajout')
    {
        $nbAdressesEnregistrees=0; // renseigné si mode = modif
        
        $adresse = new archiAdresse();
        
        
        $retourAdresses=array();
        
        // *************************************************************************
        // GESTION DU NOMBRE D'ADRESSES
        // recuperation du nombre d'adresses
        // *************************************************************************
        $nbAdressesAffichees = 1;
        if (isset($this->variablesPost['nbAdressesAffichees'.$identifiantUniqueImage]) && $this->variablesPost['nbAdressesAffichees'.$identifiantUniqueImage]!='0') {
            $nbAdressesAffichees = $this->variablesPost['nbAdressesAffichees'.$identifiantUniqueImage];
        }        
        
        // gestion de l'affichage des champs d'adresses
        if (isset($this->variablesPost['ajouterAdresse'.$identifiantUniqueImage])) {
            $nbAdressesAffichees = $nbAdressesAffichees + 1;
            //$t->assign_vars(array('nbAdressesAffichees'=>$nbAdressesAffichees));
        }
        elseif (isset($this->variablesPost['enleverAdresse'.$identifiantUniqueImage])) {
            $nbAdressesAffichees = $nbAdressesAffichees - 1;
            //$t->assign_vars(array('nbAdressesAffichees'.$identifiantUniqueImage=>$nbAdressesAffichees));
        } else {
            //$t->assign_vars(array('nbAdressesAffichees'.$identifiantUniqueImage=>1));
        }
        // *************************************************************************

        
        // *************************************************************************
        // SI ON EDITE DES IMAGES VENANT D'ETRE UPLOADEES
        // on recupere les donnees en POST
        // *************************************************************************
        if ($modeRecuperationDonnees == 'ajout') {
        
                $ville = 0;
                if (isset($this->variablesPost['ville'.$identifiantUniqueImage]) && $this->variablesPost['ville'.$identifiantUniqueImage]!='0' && $this->variablesPost['ville'.$identifiantUniqueImage]!='')
                    $ville = $this->variablesPost['ville'.$identifiantUniqueImage];
                
                
                $retourVille=array();
                // gestion du favori de la ville
                if ($ville=='0')
                {
                    $reqVilleTxt = "select nom from ville where idVille = '".$this->session->getFromSession('idVilleFavoris')."'";
                    $resVilleTxt = $this->connexionBdd->requete($reqVilleTxt);
                    $fetchVilleTxt = mysql_fetch_assoc($resVilleTxt);
                    //$t->assign_vars(array('ville'=>$this->session->getFromSession('idVilleFavoris'),  'villetxt'=>$fetchVilleTxt['nom']));
                    $ville = $this->session->getFromSession('idVilleFavoris');
                    $retourVille=array('ville'=>$this->session->getFromSession('idVilleFavoris'),  'villetxt'=>$fetchVilleTxt['nom']);
                }
        

                $retourQuartiers=array();
                // ***********************************************************************************
                // si un idVille existe sur le formulaire ,  on affiche les quartiers correspondants
                if ($ville!='0')
                {
                    $retourVille=array();
                    // recherche de la ville et de son identifiant
                    $reqVille = "select idVille, nom from ville where idVille='".$ville."'";
                    $resVille = $this->connexionBdd->requete($reqVille);
                    $fetchVille = mysql_fetch_assoc($resVille);
                    
                    $retourVille=array('ville'=>$fetchVille['idVille'],  'villetxt'=>$fetchVille['nom']);
                    
                    // recherche des quartiers de la ville
                    $resQuartiers = $this->connexionBdd->requete("select idQuartier,  nom from quartier where idVille = '".$ville."'");
                    while ($fetchQuartiers = mysql_fetch_assoc($resQuartiers)) {
                        $selected = "";

                        if (isset($this->variablesPost['quartiers'.$identifiantUniqueImage]) && $this->variablesPost['quartiers'.$identifiantUniqueImage]!='0' && $fetchQuartiers['idQuartier']==$this->variablesPost['quartiers'.$identifiantUniqueImage]) {    
                            $selected=" selected";
                        }

                        /*$t->assign_block_vars("quartiers",  array(
                                                                    'id'        =>    $fetchQuartiers['idQuartier'], 
                                                                    'nom'        =>    $fetchQuartiers['nom'], 
                                                                    'selected'    =>    $selected
                                                                ));
                        */
                        
                        $retourQuartiers[] = array('id'=>$fetchQuartiers['idQuartier'],  'nom'=>$fetchQuartiers['nom'],  'selected'=>$selected);
                        
                    }
                }
        
        
                $retourSousQuartiers=array();
                // ***********************************************************************************
                // si un idQuartier existe sur le formulaire on affiche les sous quartier correspondants
                if (isset($this->variablesPost['quartiers'.$identifiantUniqueImage]) && $this->variablesPost['quartiers'.$identifiantUniqueImage]!='')
                {
                    $resSousQuartiers = $this->connexionBdd->requete("select idSousQuartier,  nom from sousQuartier where idQuartier = '".$this->variablesPost['quartiers'.$identifiantUniqueImage]."'");
                
                    while ($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers)) {
                        $selected = "";

                        if (isset($this->variablesPost['sousQuartiers'.$identifiantUniqueImage]) && $this->variablesPost['sousQuartiers'.$identifiantUniqueImage]!='0' && $fetchSousQuartiers['idSousQuartier']==$this->variablesPost['sousQuartiers'.$identifiantUniqueImage]) {    
                            $selected=" selected";
                        }

                        /*$t->assign_block_vars("sousQuartiers",  array(
                                                                    'id'        =>    $fetchSousQuartiers['idSousQuartier'], 
                                                                    'nom'        =>    $fetchSousQuartiers['nom'], 
                                                                    'selected'    =>    $selected
                                                                ));
                        */
                        $retourSousQuartiers[]=array('id'=>$fetchSousQuartiers['idSousQuartier'],  'nom'=>$fetchSousQuartiers['nom'],  'selected'=>$selected);
                    }
                }
        }
        
        
        
        if ($modeRecuperationDonnees == 'modif') {
                // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // RECUPERATION DES DONNEES DES ADRESSES A PARTIR DE LA BASE DE DONNEES
                // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // parcours des adresses recuperées de la base de données pour voir si certaines viennent d'autres quartier ,  ville ou sousquartier ==> si c'est le cas il faudra gerer l'affichage plusieurs formulaire ,  un par groupe de sousquartier identiques
                // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                // recuperation des adresses liees à l'image
                $adresses = new archiAdresse();
                $resAdresses = $adresses->getAdressesFromImage($idImage);
            
                
                // recuperation du nombre d'adresses
                $nbAdressesEnregistrees = mysql_num_rows($resAdresses);
                if ($nbAdressesEnregistrees > $nbAdressesAffichees && !isset($this->variablesPost['enleverAdresse'.$identifiantUniqueImage]))
                    $nbAdressesAffichees = $nbAdressesEnregistrees;
                
                if ($nbAdressesEnregistrees>0)
                    mysql_data_seek($resAdresses,  0);
                    
                $arrayVilles        =array();
                $arrayQuartiers     =array();
                $arraySousQuartiers    =array();
                
                while ($fetchAdressesVerif = mysql_fetch_assoc($resAdresses))
                {
                    $arrayVilles[]=$fetchAdressesVerif['idVille'];
                    $arrayQuartiers[]=$fetchAdressesVerif['idQuartier'];
                    $arraySousQuartiers[]=$fetchAdressesVerif['idSousQuartier'];
                }
                
                $arrayVilles   = array_unique($arrayVilles);
                $arrayQuartiers = array_unique($arrayQuartiers);
                $arraySousQuartiers = array_unique($arraySousQuartiers);
                if (count($arrayVilles)>1 || count($arrayQuartiers)>1 || count($arraySousQuartiers)>1)
                {
                    echo "Attention il y a des adresses qui ne font pas partie du même quartier.<br>";
                    // affichage de plusieurs formulaires pour chaque adresse de rue qui n'appartient pas au meme ensemble
                    // a faire ?? ou limiter les groupes d'adresses au adresses appartenant à la meme rue
                } else {
                    // toutes les adresses appartiennent au meme quartier,  sousQuartier, ville 
                    // on reprend le premier enregistrement de la requete pour chercher les infos ville,  quartier,  sousquartier a assigner au formulaire
                    if ($nbAdressesEnregistrees>0)
                        mysql_data_seek($resAdresses,  0);
                    
                    $fetchInfosAdresse = mysql_fetch_assoc($resAdresses);
                    
                    // l'adresse concerne t elle une rue,  un quartier ,  un sous quartier,  une ville ... 
                    // pour l'instant on considere que cela concerne une rue avec son numero
                    $retourQuartiers = array();
                    $retourSousQuartiers = array();
                    if ($fetchInfosAdresse['idRue']!='0') {
                        // recherche de la ville,  du quartier et sous quartier
                        
                        $infosNomsAdresse     = $adresse->getAdresseComplete($fetchInfosAdresse['idRue'],  'rue');
                        $infosIds             = $adresse->getArrayAdresseFrom($fetchInfosAdresse['idRue'],  'rue');
                        
                        /*$t->assign_vars(array(
                                                'ville'=>$infosIds['ville'], 
                                                'villetxt'=>$infosNomsAdresse['ville']
                        ));*/
                        $retourVille=array('ville'=>$infosIds['ville'],  'villetxt'=>$infosNomsAdresse['ville']);

                        
                        // assignation du quartier
                        $resQuartiers=$this->connexionBdd->requete("SELECT idQuartier, nom FROM quartier WHERE idVille = '".$infosIds['ville']."'");
                        
                        while ($fetchQuartiers = mysql_fetch_assoc($resQuartiers)) {
                            $selected="";
                            if ($infosIds['quartier']==$fetchQuartiers['idQuartier'])
                                $selected = " selected";
                                
                            /*$t->assign_block_vars("quartiers",  array(
                                                                        'nom'=>$fetchQuartiers['nom'], 
                                                                        'id'=>$fetchQuartiers['idQuartier'], 
                                                                        'selected'=>$selected
                                                                ));
                            */
                            $retourQuartiers[] = array('nom'=>$fetchQuartiers['nom'],  'id'=>$fetchQuartiers['idQuartier'],  'selected'=>$selected);
                        }
                        
                        /*$t->assign_vars(array('onChangeListeQuartier'=>"appelAjax('".$this->creerUrl('',  'afficheSelectSousQuartier',  array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,  'listeSousQuartier')"));*/
                        
                        
                        
                        
                        // assignation du sousQuartier
                        $resSousQuartiers=$this->connexionBdd->requete("SELECT idSousQuartier, nom FROM sousQuartier WHERE idQuartier = '".$infosIds['quartier']."'");
                        while ($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers)) {
                            $selected="";
                            if ($infosIds['sousQuartier']==$fetchSousQuartiers['idSousQuartier'])
                                $selected=" selected";
                                
                            /*$t->assign_block_vars("sousQuartiers",  array(
                                                                        'nom'=>$fetchSousQuartiers['nom'], 
                                                                        'id'=>$fetchSousQuartiers['idSousQuartier'], 
                                                                        'selected'=>$selected
                                                                ));
                            */
                            $retourSousQuartiers[] = array('nom'=>$fetchSousQuartiers['nom'],  'id'=>$fetchSousQuartiers['idSousQuartier'],  'selected'=>$selected);
                        }
                    }
                    elseif ($fetchInfosAdresse['idSousQuartier']!='0') {
                        echo "archiAdresse::afficheAjoutAdressesMultiple : ajout d'une localisation sur un sous quartier entier désactivée. Contactez l'administrateur.";
                    }
                    elseif ($fetchInfosAdresse['idQuartier']!='0') {
                        echo "archiAdresse::afficheAjoutAdressesMultiple : ajout d'une localisation sur un quartier entier désactivée. Contactez l'administrateur.";
                    }
                    elseif ($fetchInfosAdresse['idVille']!='0') {
                        echo "archiAdresse::afficheAjoutAdressesMultiple : ajout d'une localisation sur une ville entière désactivée. Contactez l'administrateur.";
                    }
                }
        } // fin boucle sur la partie 'modif'
        
        
        
        $retourAdresses=array();
        // parcours des adresses
        for($i=0 ; $i<$nbAdressesAffichees ; $i++) {
            if ($modeRecuperationDonnees == 'ajout' && isset($this->variablesPost['rue'.$i.'_'.$identifiantUniqueImage])) {
            
                $arrayAdresse[$i]['txt']         = $this->variablesPost['rue'.$i.'_'.$identifiantUniqueImage."txt"];
                $arrayAdresse[$i]['id']          = $this->variablesPost['rue'.$i.'_'.$identifiantUniqueImage];
                $arrayAdresse[$i]['numero']      = $this->variablesPost['numero'.$i.'_'.$identifiantUniqueImage];
                $arrayAdresse[$i]['indicatif']  = $this->variablesPost['indicatif'.$i.'_'.$identifiantUniqueImage];
            } else {
                if ($modeRecuperationDonnees == 'modif' && $i<$nbAdressesEnregistrees)
                {
                    mysql_data_seek($resAdresses,  $i);
                    $fetchAdresses = mysql_fetch_assoc($resAdresses);
                    
                    $arrayAdresse[$i]['txt']         = $fetchAdresses['nomRue'];
                    $arrayAdresse[$i]['id']          = $fetchAdresses['idRue'];
                    $arrayAdresse[$i]['numero']      = $fetchAdresses['numero'];
                    $arrayAdresse[$i]['indicatif']     = $fetchAdresses['idIndicatif'];
                } else {
                    $arrayAdresse[$i]['txt']         = "";
                    $arrayAdresse[$i]['id']          = "";
                    $arrayAdresse[$i]['numero']      = "";
                    $arrayAdresse[$i]['indicatif']     = "";
                }
            }

            
            // affichage des indicatifs pour chaque adresse
            
            /*$t->assign_block_vars("adresses",  array(
                                                    'idUnique'                    => $i, 
                                                    
                                                    'onClickBoutonChoixRue'     => "document.getElementById('paramChampAppelantRue').value= 'rue".$i."';document.getElementById('iFrameRue').src='".$this->creerUrl('',  'afficheChoixRue',  array('noHeaderNoFooter'=>1))."&archiIdVille='+document.getElementById('ville').value+'&archiIdQuartier='+document.getElementById('quartiers').value+'&archiIdSousQuartier='+document.getElementById('sousQuartiers').value;document.getElementById('calqueRue').style.display='block';", 
                                                    
                                                    "nomRue"                => $arrayAdresse[$i]["txt"], 
                                                    "rue"                    => $arrayAdresse[$i]["id"], 
                                                    "numero"                => $arrayAdresse[$i]["numero"]
                                                ));
            */
            
            
            $retourIndicatifsAdresses=array();
            // gestion des indicatifs de chaque adresse
            $reqIndicatif = "select idIndicatif,  nom from indicatif";
            $resIndicatif = $this->connexionBdd->requete($reqIndicatif);
            
            while ($fetchIndicatif = mysql_fetch_assoc($resIndicatif)) {
                $selected="";
                if (    (isset($this->variablesPost['indicatif'.$i]) && $this->variablesPost['indicatif'.$i]!='' && $this->variablesPost['indicatif'.$i]==$fetchIndicatif['idIndicatif'])     ||    ($arrayAdresse[$i]['indicatif']==$fetchIndicatif['idIndicatif']))
                {
                    $selected = " selected";
                }
                /*$t->assign_block_vars("adresses.indicatifs",  array(
                                                "id"        =>    $fetchIndicatif['idIndicatif'], 
                                                "nom"        =>    $fetchIndicatif['nom'], 
                                                "selected"    =>    $selected
                ));
                */
                
                $retourIndicatifsAdresses[] = array(    "id"        =>    $fetchIndicatif['idIndicatif'], 
                                                        "nom"        =>    $fetchIndicatif['nom'], 
                                                        "selected"    =>    $selected
                                        );
            }
            
            
            
            $retourAdresses[] = array(
                                        'idUnique'=>$i ,  
                                        'onClickBoutonChoixRue'=>"document.getElementById('calqueRue').style.top=(getScrollHeight()+150)+'px';document.getElementById('paramChampAppelantRue').value= 'rue".$i."_".$identifiantUniqueImage."';document.getElementById('iFrameRue').src='".$this->creerUrl('',  'afficheChoixRue',  array('noHeaderNoFooter'=>1))."&archiIdVille='+document.getElementById('ville".$identifiantUniqueImage."').value+'&archiIdQuartier='+document.getElementById('quartiers".$identifiantUniqueImage."').value+'&archiIdSousQuartier='+document.getElementById('sousQuartiers".$identifiantUniqueImage."').value;document.getElementById('calqueRue').style.display='block';", 
                                        "nomRue"                => $arrayAdresse[$i]["txt"], 
                                        "rue"                    => $arrayAdresse[$i]["id"], 
                                        "numero"                => $arrayAdresse[$i]["numero"], 
                                        "indicatifs"            => $retourIndicatifsAdresses
                                    );
            

        }//fin boucle sur les adresses
        
        $retour = array(
                        'nbAdressesAffichees'=>$nbAdressesAffichees, 
                        'nbAdressesEnregistrees'=>$nbAdressesEnregistrees, 
                        'quartiers'=>$retourQuartiers, 
                        'sousQuartiers'=>$retourSousQuartiers, 
                        'adresses'=>$retourAdresses, 
                        'ville'=>$retourVille
                    );

        return $retour;
    }
    
    // *****************************************************************************************************************************************************************************
    // ajout d'image
    // *****************************************************************************************************************************************************************************
    public function ajouter()
    {
        // ON LOCK LA TABLE historique image pour qu'il n'y ai pas d'ajout d'image qui se chevauche entre utilisateur
        $this->connexionBdd->getLock(array('historiqueImage'));
        
        $dateDuJour = date("Y-m-d");
        
        
        $listeIdNouvellesImages=array(); // ce tableau contient la liste des idImages des nouvelles photos ajoutée,  ce tableau est simplement transmis au formulaire de modification qui s'affiche a la fin de l'ajout
        $rapportTransfert=array(); // ce tableau contient le resultat du retour de la fonction de redimensionnement ,  ok ou pas
        // creation des repertoires datés
        if (!is_dir($this->getCheminPhysiqueImage("mini").$dateDuJour)) {
            mkdir($this->getCheminPhysiqueImage("originaux").$dateDuJour);
            chmod($this->getCheminPhysiqueImage("originaux").$dateDuJour,  0777);
            mkdir($this->getCheminPhysiqueImage("mini").$dateDuJour);
            chmod($this->getCheminPhysiqueImage("mini").$dateDuJour,  0777);
            mkdir($this->getCheminPhysiqueImage("moyen").$dateDuJour);
            chmod($this->getCheminPhysiqueImage("moyen").$dateDuJour,  0777);
            mkdir($this->getCheminPhysiqueImage("grand").$dateDuJour);
            chmod($this->getCheminPhysiqueImage("grand").$dateDuJour,  0777);
        }
        
        // ************************************************************************************************************************************************************************************
        if (isset($this->variablesPost['typeAjout']) && $this->variablesPost['typeAjout']=='simple')// **************************************************************************
        {
            // *******************************************************************************************************************************************************************************
            if ((isset($_FILES['fichier']['name'])&&($_FILES['fichier']['error'] == UPLOAD_ERR_OK)) && isset($this->variablesPost['idCourant'])  && isset($this->variablesPost['liaisonImage']))//&& $this->variablesPost['idCourant']!='' && $this->variablesPost['idCourant']!='0'  // idCourant peut etre vide ainsi que liaisonImage
            {
            
                // on analyse le nom de fichier pour voir s'il y a une date a extraire et a inclure dans la base de données
                $dateCliche="0000-00-00";
                $dateObj = new dateObject();
                $retourAnalyseNomFichier=$dateObj->extractDateFromString($_FILES['fichier']['name']);
                if ($retourAnalyseNomFichier['isDate'])
                {
                    $dateCliche=$retourAnalyseNomFichier["dateExtracted"];
                }
            
            
                // creation d'un nouvel id d'image
                // recuperation de l'id le plus haut
                $this->idImage=$nouveauIdImage=$this->getNewIdImage();
                $listeIdNouvellesImages[]=$nouveauIdImage;
                $authentifie = new archiAuthentification();
                
                if (extension_loaded('gd'))
                {
                    // nommage de l'image en fonction de l'id
                    // recuperation du type de fichier
                    // et conversion en jpg s'il le faut
                    
                    // ajout d'un nouvel id dans l'historique image
                    
                    $resAjout=$this->connexionBdd->requete('
                        insert into historiqueImage (idImage, dateUpload, dateCliche, idUtilisateur) 
                        values ("'.$nouveauIdImage.'",  "'.$dateDuJour.'",  "'.$dateCliche.'",  "'.$authentifie->getIdUtilisateur().'")
                        ');
                    
                    $nouvelIdHistoriqueImage=mysql_insert_id();
                    $erreurRedimension=false;
                    // conversion en jpeg quelque soit le format géré
                    // 1- l'image est sauvegardee tel quel  (0 pour le redimensionnement)
                    if (!$this->redimension($_FILES['fichier']['tmp_name'],  pia_substr(strtolower($_FILES['fichier']['name']),  -3),  $this->getCheminPhysiqueImage("originaux").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  0)) {
                        $erreurRedimension=true;
                    }
                    // 2- redimensionnement au format mini
                    
                    if (!$this->redimension($_FILES['fichier']['tmp_name'],  pia_substr(strtolower($_FILES['fichier']['name']),  -3),  $this->getCheminPhysiqueImage("mini").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageMini())) {
                        $erreurRedimension=true;
                    }
                    
                    // 3- redimensionnement au format moyen
                    if (!$this->redimension($_FILES['fichier']['tmp_name'],  pia_substr(strtolower($_FILES['fichier']['name']),  -3),  $this->getCheminPhysiqueImage("moyen").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageMoyen())) {
                        $erreurRedimension=true;
                    }
                    
                    // 4- redimensionnement au format grand
                    if (!$this->redimension($_FILES['fichier']['tmp_name'],  pia_substr(strtolower($_FILES['fichier']['name']),  -3),  $this->getCheminPhysiqueImage("grand").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg",  $this->getFormatImageGrand())) {
                        $erreurRedimension=true;
                    }

                    unlink($_FILES['fichier']['tmp_name']);

                    $rapportTransfert[$nouveauIdImage]=array("erreurRedimension"=>$erreurRedimension);
                    
                    // ajout a la table _adresseImage
                    switch($this->variablesPost['liaisonImage']) {
                        case 'adresse':
                            $resLiaison = $this->connexionBdd->requete('insert into _adresseImage (idImage, idAdresse, seSitue, prisDepuis, etage, hauteur) values ("'.$nouveauIdImage.'",  "'.$this->variablesPost['idCourant'].'",  0,  0,  0,  0)');
                        break;
                        case 'evenement':
                            $resLiaison = $this->connexionBdd->requete('insert into _evenementImage (idImage, idEvenement) values ("'.$nouveauIdImage.'",  "'.$this->variablesPost['idCourant'].'")');
                        break;
                        default:
                            // cas ou l'on upload simplement dans la bibliotheque des images sans préciser si l'image concerne un evenement ou une adresse
                        break;
                    }
                    
                } else {
                    echo "Il s'est produit une erreur lors de l'upload,  la session est terminée ou la bibliothèque gd n'est pas installé sur le serveur.<br>";
                }
            } else {
                echo "erreur lors de l'upload : archiImage::ajouter() : peut etre que l'image est de taille trop importante.<br>";
            }
            // *******************************************************************************************************************************************************************************
        }
        elseif (isset($this->variablesPost['typeAjout']) && $this->variablesPost['typeAjout']=='multi')// **********************************************************************
        {
            // *******************************************************************************************************************************************************************************
            // traitement des fichiers uploades par FTP
            if (isset($this->variablesPost['idCourant'])  && isset($this->variablesPost['liaisonImage'])) //&& $this->variablesPost['idCourant']!='' && $this->variablesPost['idCourant']!='0'
            {
            
                //echo "cheminUploadMultiple=".$this->variablesPost["cheminUploadMultiple"]."<br>";
                //echo "idCourant=".$this->variablesPost["idCourant"]."<br>";
                //echo "liaisonImage=".$this->variablesPost["liaisonImage"]."<br>";
                    
                $repertoireUpload=$this->variablesPost["cheminUploadMultiple"];
                
                // conversion des noms de fichier en utf8 de tout le repertoire
                exec("convmv -f iso-8859-1 -t utf-8 -r ".$this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/* --notest",  $retourExec);
                
                $authentifie = new archiAuthentification();
                $nbSuppression=0;
                if (($directory = opendir($this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload))) // && $authentifie->estConnecte()
                {
                    // parcours du repertoire
                    while ($fichier = readdir($directory)) {
                        if ($fichier!="." && $fichier !=".." && !is_dir($fichier)) {
                            
                            // on analyse le nom de fichier pour voir s'il y a une date a extraire et a inclure dans la base de données
                            $dateCliche="0000-00-00";
                            $dateObj = new dateObject();
                            $retourAnalyseNomFichier=$dateObj->extractDateFromString($fichier);
                            if ($retourAnalyseNomFichier['isDate'])
                            {
                                $dateCliche=$retourAnalyseNomFichier["dateExtracted"];
                            }
                            
                            // recuperation de l'id le plus haut ( on le fait a chaque fois pour etre sur de ne pas 
                            $nouveauIdImage = $this->getNewIdImage();
                            
                            // tableau transmis a la fonction de modifications pour savoir quelles ont ete les nouvelles images ajoutees
                            $listeIdNouvellesImages[]=$nouveauIdImage;
                            
                            if ( extension_loaded('gd'))
                            {
                                $resAjout=$this->connexionBdd->requete('
                                    insert into historiqueImage (idImage, dateUpload, dateCliche, idUtilisateur,  idSource) 
                                    values ("'.$nouveauIdImage.'",  "'.$dateDuJour.'",  "'.$dateCliche.'",  "'.$authentifie->getIdUtilisateur().'",  0)
                                    ');
                                
                                $nouvelIdHistoriqueImage=mysql_insert_id();
                                
                                // on ajoute le chemin de l'image uploadee du repertoire uploadMultiple pour pouvoir regenerer les fichiers a partir de celle ci ,  au cas ou des images redimensionnees sont corrompues
                                // les images uploadees ne seront donc plus effacees
                                $reqAjoutImageUploadee = "
                                    INSERT INTO imagesUploadeesPourRegeneration 
                                        (idImage, idHistoriqueImage, cheminImageUploadee) 
                                    VALUES ('".$nouveauIdImage."',  '".$nouvelIdHistoriqueImage."', \"".$repertoireUpload."/".$fichier."\") ";
                                
                                $resAjoutImageUploadee = $this->connexionBdd->requete($reqAjoutImageUploadee);
                                
                                
                                
                                //redimensionnement 
                                //echo "debug ".$fichier." ==> ".pia_substr(strtolower($fichier),  -3)."<br>";
                                // originaux
                                $erreurRedimension=false;
                                if (!$this->redimension(
                                    $this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/".$fichier, 
                                    pia_substr(strtolower($fichier),  -3), 
                                    $this->getCheminPhysiqueImage("originaux").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg", 
                                    0
                                ))
                                {
                                    $erreurRedimension=true;
                                }
                                
                                
                                //mini
                                if (!$this->redimension(
                                    $this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/".$fichier, 
                                    pia_substr(strtolower($fichier),  -3), 
                                    $this->getCheminPhysiqueImage("mini").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg", 
                                    $this->getFormatImageMini()
                                ))
                                {
                                    $erreurRedimension=true;
                                }
                                
                                //moyen
                                if (!$this->redimension(
                                    $this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/".$fichier, 
                                    pia_substr(strtolower($fichier),  -3), 
                                    $this->getCheminPhysiqueImage("moyen").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg", 
                                    $this->getFormatImageMoyen()
                                ))
                                {
                                    $erreurRedimension=true;
                                }
                                
                                
                                //grand
                                if (!$this->redimension(
                                    $this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/".$fichier, 
                                    pia_substr(strtolower($fichier),  -3), 
                                    $this->getCheminPhysiqueImage("grand").$dateDuJour."/".$nouvelIdHistoriqueImage.".jpg", 
                                    $this->getFormatImageGrand()
                                ))
                                {
                                    $erreurRedimension=true;
                                }
                                
                                //unlink($this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload."/".$fichier);
                                
                                // suppression des fichiers du repertoire upload ,  suppression du repertoire
                                $nbSuppression++;
                                
                                $rapportTransfert[$nouveauIdImage]=array("erreurRedimension"=>$erreurRedimension);
                                
                                // ajout a la table _adresseImage
                                switch($this->variablesPost['liaisonImage'])
                                {
                                    case 'adresse':
                                        $resLiaison = $this->connexionBdd->requete('insert into _adresseImage (idImage, idAdresse, description, seSitue, prisDepuis, etage, hauteur) values ("'.$nouveauIdImage.'",  "'.$this->variablesPost['idCourant'].'",  "",  0,  0,  0,  0)');
                                    break;
                                    case 'evenement':
                                        $resLiaison = $this->connexionBdd->requete('insert into _evenementImage (idImage, idEvenement) values ("'.$nouveauIdImage.'",  "'.$this->variablesPost['idCourant'].'")');
                                        
                                    break;
                                    default:
                                    
                                    break;
                                }
                            }
                        }
                    }
                    
                }
                if ($nbSuppression>0)
                {
                    // on supprime le repertoire (si des fichiers ont ete ajoutés)
                    //rmdir($this->getCheminPhysique()."/images/uploadMultiple/".$repertoireUpload);
                }
            
            }
        }
        //$this->afficherListe($id ,  $type)
        
        // on libere la table
        $this->connexionBdd->freeLock(array('historiqueImage'));
        
        
        
        // envoi du mail au administrateur
        $message="De nouvelles images ont été uploadées : <br>";
        
        $intituleAdresse="";
        switch($this->variablesPost['liaisonImage']) {
        case 'evenement':
            $a = new archiAdresse();
            $reqAdresse = $a->getIdAdressesFromIdEvenement(array('idEvenement'=>$this->variablesPost['idCourant']));
            $resAdresse = $this->connexionBdd->requete($reqAdresse);
            $fetchAdresse = mysql_fetch_assoc($resAdresse); // on prend la premiere adresse qui vient
            $intituleAdresse=$a->getIntituleAdresseFrom($fetchAdresse['idAdresse'],  'idAdresse');
            
            foreach ($listeIdNouvellesImages as $idImageNouvelle) {
                $msgErreur="";
                if ($rapportTransfert[$idImageNouvelle]['erreurRedimension']==true) {
                    $msgErreur=" ATTENTION : il y a eu un problème avec cette image (format incorrect) ";
                }
                $message.="<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$idImageNouvelle,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageNouvelle))))."'>Image ".$idImageNouvelle."</a>$msgErreur<br>";
                
            }                    
            $evenement = new archiEvenement();
            $idEvenementGroupeAdresse = $evenement->getIdEvenementGroupeAdresseFromIdEvenement($this->variablesPost['idCourant']);
            
            $message.="<a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,  'archiIdAdresse'=>$fetchAdresse['idAdresse']))."'>".$intituleAdresse."</a><br>";
            break;
        case 'adresse':
            $a = new archiAdresse();
            $intituleAdresse=$a->getIntituleAdresseFrom($this->variablesPost['idCourant'],  'idAdresse');
            
            foreach ($listeIdNouvellesImages as $idImageNouvelle) {
                $msgErreur="";
                if ($rapportTransfert[$idImageNouvelle]['erreurRedimension']==true) {
                    $msgErreur=" ATTENTION : il y a eu un problème avec cette image (format incorrect) ";
                }
                $message.="<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$idImageNouvelle,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageNouvelle))))."'>Image ".$idImageNouvelle."</a>$msgErreur<br>";
            }
            
            $arrayUrl = array();
            if (isset($idImageNouvelle)) {
                $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageNouvelle));
                $arrayUrl=array('archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse);
            }
            
            $message.="<a href='".$this->creerUrl('',  '',  array_merge($arrayUrl,  array('archiAffichage'=>'adresseDetail',  'archiIdAdresse'=>$this->variablesPost['idCourant'])))."'>".$intituleAdresse."</a><br>";
            break;
        default:
            $message.="De nouvelle images ont été ajoutées à la bibliothèque :<br>";
            foreach ($listeIdNouvellesImages as $idImageNouvelle) {
                $msgErreur="";
                if ($rapportTransfert[$idImageNouvelle]['erreurRedimension']==true) {
                    $msgErreur=" ATTENTION : il y a eu un problème avec cette image (format incorrect) ";
                }
                $message.="<a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$idImageNouvelle,  'archiRetourAffichage'=>'evenement',  'archiRetourIdName'=>'idEvenement',  'archiRetourIdValue'=>$this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImageNouvelle))))."'>Image ".$idImageNouvelle."</a>$msgErreur<br>";
                
            }
            break;
        }
        $mail = new mailObject();    
        // recuperation des infos sur l'utilisateur qui fais la modif
        $utilisateur = new archiUtilisateur();
        $arrayInfosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
        
        $message .="<br>".$arrayInfosUtilisateur['nom']." - ".$arrayInfosUtilisateur['prenom']." - ".$arrayInfosUtilisateur['mail']."<br>";

        $mail->sendMailToAdministrators($mail->getSiteMail(),  'Nouvelles images ajoutées - '.$intituleAdresse,  $message,  " AND alerteAdresses = '1' ", true);
        $utilisateur->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,  'idTypeMailRegroupement'=>13,  'criteres'=>" and alerteAdresses='1' "));
        // ************************************************************************************************************************************************
        // envoi d'un mail pour l'auteur de l'adresse
        // ************************************************************************************************************************************************
        $mail = new mailObject();
        $utilisateur = new archiUtilisateur();
        $auth = new archiAuthentification();
        $arrayUtilisateurs = $utilisateur->getCreatorsFromAdresseFrom($this->variablesPost['idCourant'],  'idEvenement'); // a modifier quand on pourra ajouter des photos sur une adresse
        $adresse = new archiAdresse();
        $intituleAdresse = $adresse->getIntituleAdresseFrom($this->variablesPost['idCourant'],  'idEvenement');
        $evenement = new archiEvenement();
        foreach ($arrayUtilisateurs as $indice => $idUtilisateurAdresse) {
            if ($idUtilisateurAdresse != $auth->getIdUtilisateur()) {
                $infosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($idUtilisateurAdresse);
                if ($infosUtilisateur['alerteAdresses']=='1' && $infosUtilisateur['compteActif']=='1' && $infosUtilisateur['idProfil']!='4') {
                    $messageIntro = "Un utilisateur a ajouté une ou plusieurs images sur une adresse dont vous êtes l'auteur.<br>";
                    
                    $idEvenementGroupeAdresse = $evenement->getIdEvenementGroupeAdresseFromIdEvenement($this->variablesPost['idCourant']);
                    $adresse = new archiAdresse();
                    $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$this->variablesPost['idCourant']));
                    $resAdresses = $this->connexionBdd->requete($reqAdresses);
                    $fetchAdresses = mysql_fetch_assoc($resAdresses);
                    
                    $message= "Pour vous rendre sur l'évènement : <a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdAdresse'=>$fetchAdresses['idAdresse'],  'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))."'>".$intituleAdresse."</a><br>";
                    $messageFin= $this->getMessageDesabonnerAlerteMail();
                    if ($utilisateur->isMailEnvoiImmediat($idUtilisateurAdresse)) {
                        $mail->sendMail($mail->getSiteMail(),  $infosUtilisateur['mail'],  'Ajout de photos sur une adresse dont vous êtes l\'auteur.',  $messageIntro.$message.$messageFin, true);
                    } else {
                        $utilisateur->ajouteMailEnvoiRegroupes(array('contenu'=>$message,  'idDestinataire'=>$idUtilisateurAdresse,  'idTypeMailRegroupement'=>13));
                    }
                }
            }
        }
        // ************************************************************************************************************************************************
        
        
        // *************************************************************************************************************************************************************
        // envoi mail aussi au moderateur si ajout sur adresse de ville que celui ci modere
        $u = new archiUtilisateur();
        
        $arrayVilles=array();
        $arrayVilles[] = $adresse->getIdVilleFrom($this->variablesPost['idCourant'],  'idEvenement');
        $arrayVilles = array_unique($arrayVilles);
        
        $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($arrayVilles[0],  array("sqlWhere"=>" AND alerteAdresses='1' "));
        if (count($arrayListeModerateurs)>0) {
            foreach ($arrayListeModerateurs as $indice => $idModerateur) {
                if ($idModerateur!=$this->session->getFromSession('utilisateurConnecte'.$this->idSite)) {
                    if ($u->isMailEnvoiImmediat($idModerateur)) {
                        $mailModerateur = $u->getMailUtilisateur($idModerateur);
                        $mail->sendMail($mail->getSiteMail(),  $mailModerateur,  'Nouvelles images ajoutées -'.$intituleAdresse,  $message, true);
                    } else {
                        $u->ajouteMailEnvoiRegroupes(array('contenu'=>$message,  'idDestinataire'=>$idModerateur,  'idTypeMailRegroupement'=>13));
                    }
                }
            }
        }
        // *************************************************************************************************************************************************************
        
        
        
        
        
        // on appelle le formulaire permettant de mettre a jour les infos concernant les photos
        echo $this->afficherFormulaireModification(0,  '',  $listeIdNouvellesImages);
    }
    
    /**
     * Redimensionne une image ,  si newWidth =0 ,  pas de redimensionnement
     * 
     * @param string $imageFile Nom du fichier
     * @param string $imageType Type du fichier
     * @param string $chemin    Chemin
     * @param int    $newLength Nouvelle longueur
     * 
     * @return bool
     * */
    public function redimension($imageFile='',  $imageType='jpg',  $chemin='',  $newLength=0)
    {
        set_time_limit(0);
        //sleep(1);
        $imageOK=true;
        $f = new fileObject();
        
        switch($imageType) {
        case 'gif':
            $im=imagecreatefromgif($imageFile);
            break;
        
        case 'jpg':
            // attention pour les jpg ,  si on ne les redimensionne pas ,  on va se contenter de la copier (pour essayer d'eviter les problemes d'images tronquees)
            if ($newLength!=0)
                $im=imagecreatefromjpeg($imageFile); 
            break;
        case 'peg': // pour les format 'jpeg' ,  cette detection devrait suffir
                $im=imagecreatefromjpeg($imageFile);
            break;
        
        case 'png':
            $im=imagecreatefrompng($imageFile);
            break;
        
        default:
            echo 'format d\'image non supporté';
            $imageOK=false;
            break;
        }
        
        list($originalWidth,  $originalHeight,  $type,  $attr) = getimagesize($imageFile);
        
        if ($newLength==0) {
            // on ne redimensionne pas
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
            
            if ($imageType=='jpg') {
                // dans le cas d'une image jpg ,  on se contente de la copier
                if (!copy($imageFile,  $chemin)) {
                    $imageOK=false;
                } else {
                    // verification du CRC
                    $i=0;
                    //echo "crc32 image file : ".$f->crc32_file($imageFile)."<br><br>";
                    //echo "crc32 chemin : ".$f->crc32_file($chemin)."<br><br>";

                    while (!($f->crc32_file($imageFile)==$f->crc32_file($chemin))) {
                        copy($imageFile,  $chemin);
                        $i++;
                        
                        if ($i>=5) {
                            echo "il y a une erreur a la copie de l'image originale. Effacez l'image et retentez l'opération,  si le problème persiste merci de contacter l'administrateur.<br>";
                            $m = new mailObject();
                            $m->sendMail("images@archi-strasbourg.org",  "laurent_dorer@yahoo.fr",  "probleme image corrompue",  "il y a eu plus de 5 tentatives pour des crc errones => $imageFile => $chemin");
                            break;
                        }
                    }
                }
                
            } else {
                $imDestination = imagecreatetruecolor($newWidth,  $newHeight);
                imagecopyresampled($imDestination,  $im,  0,  0,  0,  0,  $newWidth,  $newHeight,  $originalWidth,  $originalHeight);
                imagejpeg($imDestination,  $chemin,  100);
                
                imagedestroy($im);
                imagedestroy($imDestination);
            }
        } elseif ($originalWidth>$originalHeight) {
            /*if ($originalWidth/$originalHeight>2.5) {
                // on admet qu'a ce ratio entre la longueur et la hauteur ,  on a affaire a une image panoramique,  on va donc calculer les dimensions de la nouvelle image en fonction de sa hauteur
                $newHeight = $newLength;
                $newWidth     = round($originalWidth * $newHeight / $originalHeight);
            } else {*/
                $newWidth=$newLength;
                $newHeight     = round($originalHeight * $newWidth / $originalWidth);
            //}
            $imDestination = imagecreatetruecolor($newWidth,  $newHeight);
            imagecopyresampled($imDestination,  $im,  0,  0,  0,  0,  $newWidth,  $newHeight,  $originalWidth,  $originalHeight);
            imagejpeg($imDestination,  $chemin,  100);
            
            imagedestroy($im);
            imagedestroy($imDestination);
        } else {
            $newHeight = $newLength;
            $newWidth     = round($originalWidth * $newHeight / $originalHeight);
            
            $imDestination = imagecreatetruecolor($newWidth,  $newHeight);
            imagecopyresampled($imDestination,  $im,  0,  0,  0,  0,  $newWidth,  $newHeight,  $originalWidth,  $originalHeight);
            imagejpeg($imDestination,  $chemin,  100);
            
            imagedestroy($im);
            imagedestroy($imDestination);
        }
        
        

        return $imageOK;
    }
    
    /**
     * Fonction permettant d'afficher le formulaire de recherche sur les images
     * 
     * @return string HTML
     * */
    public function afficherFormulaireRecherche()
    {
        $html = '';
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('rechercheImage'=>'formulaireRechercheImage.tpl')));
        
        $t->assign_vars(array('formAction'=>$this->creerUrl('',  'imageListe',  array())));
        
        $listeChampsFormulaire = array("motCle",  "dateUploadDu",  "dateUploadAu",  "datePriseDeVueDu",  "datePriseDeVueAu",  "pageCourante");
        
        foreach ($listeChampsFormulaire as $indice =>$value) {
            if (isset($this->variablesPost[$value]))
                $t->assign_vars(array($value=>$this->variablesPost[$value]));
        }
        
        $t->assign_vars(array('popupCalendrier'=>$this->getPopupCalendrier()));
        
        ob_start();
        $t->pparse('rechercheImage');
        $html=ob_get_contents();
        ob_end_clean();
        
        return $html;
    
    }
    
    /**
     * Fonction qui affiche la liste des photos suivant les criteres fournis dans le tableau 'criteres'
     * 
     * @param array $criteres Critères
     * 
     * @return string HTML
     * */
    public function afficherListe($criteres=array())
    {
        $html = '';
        
        $html .= $this->afficherFormulaireRecherche();
        $formulaire = new formGenerator();    
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('detailImage'=>'listeImages.tpl')));
        $tabParametresAutorises = array('ordre',  'tri',  'nbEnregistrements',  'selection',  'id',  "motCle",  "dateUploadDu",  "dateUploadAu",  "datePriseDeVueDu",  "datePriseDeVueAu",  "pageCourante");
        
        
        // lien pour l'ajout d'une ou plusieur images dans la bibliotheque ,  sans préciser si celle(s) ci appartient (iennent) à une adresse ou un evenement
        $t->assign_vars(array('lienAjoutImage'=>$this->creerUrl('',  'ajoutImageBibliotheque')));
        
        foreach ($tabParametresAutorises AS $param) {
            if (isset($this->variablesPost[$param]) AND !isset($criteres[$param]))
                $criteres[$param] = $this->variablesPost[$param];
        }
        
        foreach ($tabParametresAutorises AS $param) {
            if (isset($this->variablesGet[$param]) AND !isset($criteres[$param]))
                $criteres[$param] = $this->variablesGet[$param];
        }
        
        if (!isset($criteres['pageCourante'])) {
            $criteres['pageCourante']=1;
        }        
        
        if ( !isset( $criteres['selection']) OR !isset($criteres['id']) OR $formulaire->estChiffre($criteres['id']) != 1) {
            $sqlWhere = '1=1';
        } else {
            switch( $criteres['selection']) {
            case 'utilisateur':        $sqlWhere = 'hI.idUtilisateur='.$criteres['id'];
                break;
            default:        $sqlWhere = '1=1';
            }
        }
        
        
        if ( !isset( $criteres['ordre'] )) {
            $sqlOrdre = 'hI.nom';
        } else {
            switch( $criteres['ordre']) {
            case 'nom':        $sqlOrdre = 'hI.nom';
                break;
            case 'description':    $sqlOrdre = 'hI.description';
                break;
            case 'source':        $sqlOrdre = 'nomSource';
                break;
            case 'dateAjout':    $sqlOrdre = 'hI.dateUpload';
                break;
            default:        $sqlOrdre = 'hI.nom';
            }
        }
        
        if (isset( $criteres['tri'])) {
            if ($criteres['tri'] == 'desc') {
                $sqlTri = 'DESC';
            } else {
                $sqlTri = 'ASC';
            }
        } else {
            $sqlTri = 'ASC';
        }
        
        // ***********************************
        // criteres sur les dates
        //  **********************************
        $sqlRecherche="";
        // criteres sur les dates d'upload
        if (isset($criteres['dateUploadDu']) && $criteres['dateUploadDu']!='' && (!isset($criteres['dateUploadAu']) || $criteres['dateUploadAu']=='')) {
            $sqlRecherche .= " and hI.dateUpload='".$this->date->toBdd($criteres['dateUploadDu'])."' ";
        }
        
        if (isset($criteres['dateUploadDu']) && $criteres['dateUploadDu']!='' && (!isset($criteres['dateUploadAu']) || $criteres['dateUploadAu']=='')) {
            $sqlRecherche .= " and hI.dateUpload='".$this->date->toBdd($criteres['dateUploadDu'])."' ";
        }
        
        if (isset($criteres['dateUploadDu']) && $criteres['dateUploadDu']!='' && isset($criteres['dateUploadAu']) && $criteres['dateUploadAu']!='') {
            $sqlRecherche .= " and hI.dateUpload>='".$this->date->toBdd($criteres['dateUploadDu'])."' and hI.dateUpload<='".$criteres['dateUploadAu']."' ";
        }
        
        // criteres sur les dates de prises de vues
        if (isset($criteres['datePriseDeVueDu']) && $criteres['datePriseDeVueDu']!='' && (!isset($criteres['datePriseDeVuAu']) || $criteres['datePriseDeVuAu']=='')) {
            $sqlRecherche .= " and hI.dateCliche='".$this->date->toBdd($criteres['datePriseDeVueDu'])."' ";
        }
        
        if (isset($criteres['datePriseDeVueDu']) && $criteres['datePriseDeVueDu']!='' && (!isset($criteres['datePriseDeVueAu']) || $criteres['datePriseDeVueAu']=='')) {
            $sqlRecherche .= " and hI.dateCliche='".$this->date->toBdd($criteres['datePriseDeVueDu'])."' ";
        }
        
        if (isset($criteres['datePriseDeVueDu']) && $criteres['datePriseDeVueDu']!='' && isset($criteres['datePriseDeVueAu']) && $criteres['datePriseDeVueAu']!='') {
            $sqlRecherche .= " and hI.dateCliche>='".$criteres['datePriseDeVueDu']."' and hI.dateCliche<='".$criteres['datePriseDeVueAu']."' ";
        }
        
        // criteres sur le nom/description
        if (isset($criteres['motCle']) && $criteres['motCle']!='') {
            $sqlRecherche .=" and ( ";
            $arrayListeMots = explode(" ",  $criteres['motCle']);
            foreach ($arrayListeMots as $indice => $value) {
                $sqlRecherche .= " hI.nom LIKE \"%".$value."%\" or hI.description LIKE \"%".$value."%\" or ";
            }
            if (count($arrayListeMots)>0) {
                $sqlRecherche = substr($sqlRecherche,  0,  pia_strlen($sqlRecherche)-3);
            }
            $sqlRecherche .= ") ";
        }
        
        //  **********************************
        
        // nombre d'images totales
        //$sqlNbEnregistrements = "SELECT distinct idImage from historiqueImage WHERE ".$sqlWhere.;
        $sqlNbEnregistrements="SELECT hI.nom,  hI.idImage,  CONCAT(hI.dateUpload,  '/',  hI.idHistoriqueImage) AS urlImage,  hI.description,  s.nom AS nomSource 
            FROM historiqueImage hI2,  historiqueImage hI 
            LEFT JOIN source s USING (idSource)
            WHERE ".$sqlWhere." AND hI.idImage = hI2.idImage 
            ".$sqlRecherche."
            GROUP BY hI.idImage,  hI.idHistoriqueImage HAVING hI.idHistoriqueImage=MAX(hI2.idHistoriqueImage) 
            ORDER BY ".$sqlOrdre." ".$sqlTri;
        
        $resNbEnregistrements = $this->connexionBdd->requete($sqlNbEnregistrements);
        $nbEnregistrementTotaux = mysql_num_rows($resNbEnregistrements);        
        
        // nombre d'images affichées sur une page
        $nbEnregistrementsParPage = 5;
        $arrayPagination=$this->pagination(
            array(
            'nomParamPageCourante'=>'pageCourante', 
            'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 
            'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux, 
            'typeLiens'=>'formulaire', 
            'champPageCourante'=>'pageCourante', 
            'idFormulaire'=>'rechercheImages'
            )
        );
        
        echo $arrayPagination['html'];
        
        
        
        $sql="SELECT hI.nom,  hI.idImage,  CONCAT(hI.dateUpload,  '/',  hI.idHistoriqueImage) AS urlImage,  hI.description,  s.nom AS nomSource 
            FROM historiqueImage hI2,  historiqueImage hI 
            LEFT JOIN source s USING (idSource)
            WHERE ".$sqlWhere." AND hI.idImage = hI2.idImage 
            ".$sqlRecherche."
            GROUP BY hI.idImage,  hI.idHistoriqueImage HAVING hI.idHistoriqueImage=MAX(hI2.idHistoriqueImage) 
            ORDER BY ".$sqlOrdre." ".$sqlTri." LIMIT ".$arrayPagination['limitSqlDebut'].",  ".$nbEnregistrementsParPage;
        
        $rep = $this->connexionBdd->requete($sql);

        $t->assign_vars(array('nbReponses'=>$nbEnregistrementTotaux));
        
        if ($nbEnregistrementTotaux>0) {
            $tabLiens= array(
                array(
                    'titre' => 'Image', 
                    'url'   => '', 
                    'urlOnClick' => '', 
                    'urlDesc' => '', 
                    'urlDescOnClick' => '', 
                    'urlAsc' => '', 
                    'urlAscOnClick' => ''), 
                array(
                    'titre' => 'nom', 
                    'url'   => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'nom',  'pageCourante'=>1))), 
                    'urlOnClick' => '', 
                    'urlDesc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'nom',  'pageCourante'=>1,  'tri'=>'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'nom',  'pageCourante'=>1,  'tri'=>'asc'))), 
                    'urlAscOnClick' => ''), 
                array(
                    'titre' => 'description', 
                    'url'   => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'description',  'pageCourante'=>1))), 
                    'urlOnClick' => '', 
                    'urlDesc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'description',  'pageCourante'=>1,  'tri'=>'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'description',  'pageCourante'=>1,  'tri'=>'asc'))), 
                    'urlAscOnClick' => ''), 
                array(
                    'titre' => 'source', 
                    'url'   => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'source',  'pageCourante'=>1))), 
                    'urlOnClick' => '', 
                    'urlDesc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'source',  'pageCourante'=>1,  'tri'=>'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc' => $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('ordre'=>'source',  'pageCourante'=>1,  'tri'=>'asc'))), 
                    'urlAscOnClick' => '')
                );
                
            $nbLiens = count($tabLiens);
            for ( $i=0; $i<$nbLiens; $i++) {
                $t->assign_block_vars('liens',  $tabLiens[$i]);
            }
            
            
            while ($res =mysql_fetch_object($rep)) {
                $t->assign_block_vars(
                    'image',  array(
                        'url'        => $this->creerUrl('',  'imageDetail',  array('archiIdImage' => $res->idImage)), 
                        'urlImage'     => $this->getUrlImage("mini").$res->urlImage, 
                        'nom'        => htmlspecialchars(stripslashes($res->nom)), 
                        'description'    => htmlspecialchars(stripslashes($res->description)), 
                        'source'    => $res->nomSource
                    )
                );
            }
        }
        
        ob_start();
        $t->pparse('detailImage');
        $html.=ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Retourne un nouvel idImage pour l'ajout d'une nouvelle image
     * 
     * @return int
     * */
    public function getNewIdImage()
    {
        $resMaxId    =    $this->connexionBdd->requete('select max(idImage) as maxIdImage from historiqueImage');
        $fetchMaxId    =    mysql_fetch_array($resMaxId);

        if (isset($fetchMaxId['maxIdImage']))
            $nouveauIdImage=$fetchMaxId['maxIdImage']+1;
        else
            $nouveauIdImage=1;
        
        return $nouveauIdImage;
    }
    
    /**
     * Recuperation de la liste des adresses lies enregistrees dans la base pour la table _adresseImage
     * 
     * @param int $idImage ID de l'image
     * 
     * @return Resource
     * */
    public function getFetchAdressesLiees($idImage=0)
    {
        $reqAdressesLiees = "
            SELECT ha.idAdresse as idAdresse, 
            ha.nom as nom, 
            r.nom as nomRue, 
            sq.nom as nomSousQuartier, 
            q.nom as nomQuartier, 
            v.nom as nomVille, 
            p.nom as nomPays, 
            
            
            ha.idRue as idRue, 
            IF (ha.idSousQuartier != 0,  ha.idSousQuartier,  r.idSousQuartier) AS idSousQuartier, 
            IF (ha.idQuartier != 0,  ha.idQuartier,  sq.idQuartier) AS idQuartier, 
            IF (ha.idVille != 0,  ha.idVille,  q.idVille) AS idVille, 
            IF (ha.idPays != 0,  ha.idPays,  v.idPays) AS idPays
            FROM historiqueAdresse hab,  historiqueAdresse ha
            RIGHT JOIN _adresseImage ai ON ai.idAdresse = ha.idAdresse
            
            LEFT JOIN rue r         ON r.idRue = ha.idRue
            LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = if (ha.idRue='0' and ha.idSousQuartier!='0' , ha.idSousQuartier , r.idSousQuartier )
            LEFT JOIN quartier q        ON q.idQuartier = if (ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier!='0' , ha.idQuartier , sq.idQuartier )
            LEFT JOIN ville v        ON v.idVille = if (ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille!='0' , ha.idVille , q.idVille )
            LEFT JOIN pays p        ON p.idPays = if (ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille='0' and ha.idPays!='0' , ha.idPays , v.idPays )
            
            
            
            WHERE hab.idAdresse = ha.idAdresse
            AND ai.idImage = '".$idImage."'
            GROUP BY ha.idAdresse,  ha.idHistoriqueAdresse
            HAVING ha.idHistoriqueAdresse = max(hab.idHistoriqueAdresse)
        ";
        
        $resAdressesLiees=$this->connexionBdd->requete($reqAdressesLiees);
        return $resAdressesLiees;
    }
    
    /**
     * Recuperation des evenements lies a l'image
     * 
     * @param int $idImage ID de l'image
     * 
     * @return Resource
     * */
    public function getFetchEvenementsLies($idImage=0)
    {
        $reqEvenementsLies
            ="SELECT he.idEvenement as idEvenement,  he.titre as titre
            FROM historiqueEvenement heb,  historiqueEvenement he
            RIGHT JOIN _evenementImage ei ON ei.idEvenement = he.idEvenement
            WHERE heb.idEvenement = he.idEvenement
            AND ei.idImage = '".$idImage."'
            GROUP BY he.idEvenement,  he.idHistoriqueEvenement
            HAVING he.idHistoriqueEvenement = max(heb.idHistoriqueEvenement)                        
        ";
        
        $resEvenementsLies = $this->connexionBdd->requete($reqEvenementsLies);
        return $resEvenementsLies;
    }

    /**
     * Supprime les images d'un evenement si celle ci ne sont pas liées a une adresse,  ou un autre evenement
     * 
     * @param int $idEvenement ID de l'événement
     * 
     * @return void
     * */
    public function deleteImagesFromIdEvenement($idEvenement=0)
    {
        // recuperation de la liste des images et suppression des images :
        $reqImages = "
                        SELECT distinct hi.idImage as idImage
                        FROM historiqueImage hi
                        RIGHT JOIN _evenementImage ei ON ei.idEvenement = '".$idEvenement."'
                        WHERE hi.idImage = ei.idImage
                    ";
        
        $resImages = $this->connexionBdd->requete($reqImages);
        
        // liste des images concernées : 
        $tabIdImages = array();
        
        while ($fetchImages = mysql_fetch_assoc($resImages)) {
            $tabIdImages[]=$fetchImages['idImage'];
        }
        
        $tabIdImages = array_unique($tabIdImages);
        
        // verification et suppression
        foreach ($tabIdImages as $idImage) {
            // on verifie que l'image courante n'est pas liée a une adresse
            $reqVerifAdresse = "SELECT idAdresse FROM _adresseImage WHERE idImage='".$idImage."'";
            $resVerifAdresse = $this->connexionBdd->requete($reqVerifAdresse);
            
            if (mysql_num_rows($resVerifAdresse)==0) {
                // l'image n'est pas liee a une adresse ,  on peut donc la supprimer,  ainsi que tout son historique
                
                
                // suppression de l'image complete desactivée ... a voir
                
                
                /*$reqInfosImage = "
                                SELECT idHistoriqueImage ,  idImage ,  dateUpload 
                                FROM historiqueImage
                                WHERE idImage = '".$idImage."'
                ";
                
                $resInfosImage=$this->connexionBdd->requete($reqInfosImage);
                
                while ($fetchInfosImage = mysql_fetch_assoc($resInfosImage))
                {
                    
                    if (unlink($this->getCheminPhysiqueImage("originaux").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                        echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." originale... OK<br>";
                    } else {
                        echo "probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." originale.<br>";
                    }
                    
                    if (unlink($this->getCheminPhysiqueImage("mini").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                        echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." mini... OK<br>";
                    } else {
                        echo "probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." mini.<br>";
                    }
                    
                    if (unlink($this->getCheminPhysiqueImage("moyen").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                        echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." moyen... OK<br>";
                    } else {
                        echo "probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." moyen.<br>";
                    }
                    
                    if (unlink($this->getCheminPhysiqueImage("grand").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                        echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." grand... OK<br>";
                    } else {
                        echo "probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." grand.<br>";
                    }
                    
                    $reqDeleteHistorique = "DELETE FROM historiqueImage WHERE idHistoriqueImage = '".$fetchInfosImage['idHistoriqueImage']."'";
                    $resDeleteHistorique = $this->connexionBdd->requete($reqDeleteHistorique);
                    
                    
                    echo "image ".$fetchInfosImage['idHistoriqueImage']." supprimée. <br>";
                    
                }
                */
            }
            
            // suppression des liaisons
            $reqDeleteLiaisons = "DELETE FROM _evenementImage WHERE idImage = '".$idImage."'";
            $resDeleteLiaisons = $this->connexionBdd->requete($reqDeleteLiaisons);
            
            
            // on supprime aussi dans la table des images uploadées (table qui sert pour la regeneration des fichiers
            $reqDeleteUpload = "DELETE FROM imagesUploadeesPourRegeneration WHERE idImage='".$idImage."'";
            $resDeleteUpload = $this->connexionBdd->requete($reqDeleteUpload);
        }
    }
    
    /**
     * Fonction supprimant physiquement et dans la bdd une image donnée
     * 
     * @param int   $idImage ID de l'image
     * @param array $params  Paramètres
     * 
     * @return void
     * */
    public function deleteImage($idImage=0,  $params = array())
    {
        $a = new archiAdresse();
        $erreurObj = new objetErreur();
        // recuperation du groupe d'adresse de l'image pour l'affichage au retour
        if ($idPerson=archiPersonne::isPerson($this->getIdEvenementGroupeAdresseFromImage(array("idImage"=>$idImage, "type"=>"personne")))) {
            $type="personne";
        } else {
            $type=null;
        }
        $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromImage(array('idImage'=>$idImage, "type"=>$type));
        $u = new archiUtilisateur();
        $authentification  = new archiAuthentification();
        
        $idProfilUtilisateur = $u->getIdProfilFromUtilisateur($authentification->getIdUtilisateur());
        
        $d = new droitsObject();
        
        
        if (($d->isAuthorized('image_supprimer',  $idProfilUtilisateur)) && ($u->isModerateurFromVille($authentification->getIdUtilisateur(),  $idImage,  'idImage') || $idProfilUtilisateur == '4' )) {
            $reqInfosImage = "
                            SELECT idHistoriqueImage ,  idImage ,  dateUpload 
                            FROM historiqueImage
                            WHERE idImage = '".$idImage."'
            ";
            
            $resInfosImage=$this->connexionBdd->requete($reqInfosImage);
            
            while ($fetchInfosImage = mysql_fetch_assoc($resInfosImage)) {
                
                if (unlink($this->getCheminPhysiqueImage("originaux").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                    //echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." originale... OK<br>";
                } else {
                    $erreurObj->ajouter("probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." originale.<br>");
                }
                
                if (unlink($this->getCheminPhysiqueImage("mini").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                    //echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." mini... OK<br>";
                } else {
                    $erreurObj->ajouter("probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." mini.<br>");
                }
                
                if (unlink($this->getCheminPhysiqueImage("moyen").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                    //echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." moyen... OK<br>";
                } else {
                    $erreurObj->ajouter("probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." moyen.<br>");
                }
                
                if (unlink($this->getCheminPhysiqueImage("grand").$fetchInfosImage['dateUpload']."/".$fetchInfosImage['idHistoriqueImage'].".jpg")) {
                    //echo "suppression image ".$fetchInfosImage['idHistoriqueImage']." grand... OK<br>";
                } else {
                    $erreurObj->ajouter("probleme suppression image ".$fetchInfosImage['idHistoriqueImage']." grand.<br>");
                }
                
                $reqDeleteHistorique = "DELETE FROM historiqueImage WHERE idHistoriqueImage = '".$fetchInfosImage['idHistoriqueImage']."'";
                $resDeleteHistorique = $this->connexionBdd->requete($reqDeleteHistorique);
                
                // suppression de l'image dans la table des fichiers uploades
                $reqDeleteUpload = "DELETE FROM imagesUploadeesPourRegeneration WHERE idHistoriqueImage = '".$fetchInfosImage['idHistoriqueImage']."'";
                $resDeleteUpload = $this->connexionBdd->requete($reqDeleteUpload);
                
                //echo "image ".$fetchInfosImage['idHistoriqueImage']." supprimée. <br>";
                
            }
            
            $reqDeleteImageAdresseImage = "DELETE FROM _adresseImage WHERE idImage = '".$idImage."'";
            $resDeteleImageAdresseImage = $this->connexionBdd->requete($reqDeleteImageAdresseImage);
            
            $reqDeleteImageEvenementImage = "DELETE FROM _evenementImage WHERE idImage ='".$idImage."'";
            $resDeleteImageEvenementImage = $this->connexionBdd->requete($reqDeleteImageEvenementImage);
        
        }
        if ($idPerson) {
            header("Location: ".$this->creerUrl("", "evenementListe", array("selection"=>"personne", "id"=>$idPerson), false, false));
        }
        
        if ($erreurObj->getNbErreurs()>0) {
            echo $erreurObj->afficher();
        } else {
            echo "image supprimée.<br>";
        }
        
        if (isset($params['retourSurGroupeAdresse']) && $params['retourSurGroupeAdresse']==true) {
            echo $a->afficherDetail(0,  $idEvenementGroupeAdresse);
        }
        
    }
    
    /**
     * Fonction permettant a l'administrateur de visualiser l'historique de l'image
     * 
     * @param int $idImage ID de l'image
     * 
     * @return string HTML
     * */
    public function afficheHistoriqueImage($idImage=0)
    {
        $u = new archiUtilisateur();
        $html = "";
        
        $req = "SELECT idHistoriqueImage, dateUpload, idImage, idUtilisateur, description, dateCliche, isDateClicheEnviron, idSource FROM historiqueImage WHERE idImage='".$idImage."'";
        $res = $this->connexionBdd->requete($req);
        
        $t = new tableau();
        $d = new dateObject();
        $bb = new bbCodeObject();
        $s = new archiSource();
        
        $authentification  = new archiAuthentification();
        $droitsObject = new droitsObject();
        $idProfilUtilisateur = $u->getIdProfilFromUtilisateur($authentification->getIdUtilisateur());
        
        $isRegenerationPossible = false;
        if ($droitsObject->isAuthorized('image_regenerer',  $idProfilUtilisateur)) {
            $isRegenerationPossible = true;
        }
        
        
        while ($fetch = mysql_fetch_assoc($res)) {
            //$html.="<img src='".$this->getUrlImage("moyen").$fetch['dateUpload']."/".$fetch['idHistoriqueImage'].".jpg"."'><br>";
            $t->addValue("<img src='".$this->getUrlImage("moyen").$fetch['dateUpload']."/".$fetch['idHistoriqueImage'].".jpg"."'>",  "valign=top");
            $arrayInfosUtilisateur = $u->getArrayInfosFromUtilisateur($fetch['idUtilisateur'],  array('listeChamps'=>'nom, prenom'));
            $libelleUtilisateur = $arrayInfosUtilisateur['nom']." ".$arrayInfosUtilisateur['prenom'];
            
            $dateCliche = " - ";
            if ($fetch['dateCliche']!='0000-00-00') {
                $environ = "";
                if ($fetch['isDateClicheEnviron']=='1') {
                    $environ = "Environ ";
                }
                $dateCliche    = $environ.$d->toFrenchAffichage($fetch['dateCliche']);
            }
            
            $libelleSource= $s->getSourceLibelle($fetch['idSource']);
            if ($libelleSource != '') {
                $libelleSource = "<tr><td><b>source : </b>".$libelleSource."</td></tr>";
            }
                
            
            $description = "";
            if ($fetch['description']!='') {
                $description = "<tr><td><b>description :</b><br>".$bb->convertToDisplay(array('text'=>$fetch['description']))."</td></tr>";
            }
            
            $detailHistoriqueImage = "<table><tr><td>de <a href='".$this->creerUrl('',  'detailProfilPublique',  array('archiIdUtilisateur'=>$fetch['idUtilisateur']))."'>".$libelleUtilisateur."</a> (le ".$d->toFrenchAffichage($fetch['dateUpload']).")</td></tr><tr><td><b>date cliché : </b>".$dateCliche."</td></tr>".$description."".$libelleSource."</table>";
            
            
            $t->addValue($detailHistoriqueImage);
            
            if ($isRegenerationPossible) {
                $reqRegenerationAvailable = "SELECT idHistoriqueImage ,  idImage ,  cheminImageUploadee FROM imagesUploadeesPourRegeneration WHERE idHistoriqueImage = '".$fetch['idHistoriqueImage']."' AND idImage='".$idImage."'";
                $resRegenerationAvailable = $this->connexionBdd->requete($reqRegenerationAvailable);
                
                if (mysql_num_rows($resRegenerationAvailable)>0) {
                    $fetchRegenerationAvailable = mysql_fetch_assoc($resRegenerationAvailable);
                    
                    if (file_exists($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenerationAvailable['cheminImageUploadee'])) {
                        $t->addValue("<input type='button' name='regenere' value='régéréner les photos à partir de la source' onclick=\"location.href='".$this->creerUrl('regenereImageFromUploadDir',  'imageDetail',  array('archiIdHistoriqueImage'=>$fetch['idHistoriqueImage'],  'archiIdImage'=>$idImage))."';\">");
                    } else {
                        $t->addValue("Enregistrement trouvé,  mais fichier source de régénération inexistant.");
                    }
                } else {
                    $t->addValue("Pas de régénération possible");
                }
            }
        }
        // En principe cette variable represente un idEvenement,  a changer pour l'id evenementGroupeAdresse
        if (isset($this->variablesGet['archiRetourIdValue']) && $this->variablesGet['archiRetourIdValue']!='') {
            $adresse = new archiAdresse();
            $evenement = new archiEvenement();
            $idEvenementGroupeAdresse = $evenement->getIdEvenementGroupeAdresseFromIdEvenement($this->variablesGet['archiRetourIdValue']);
            $html.=$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse);
        }
        
        $html.="<h2>Historique de l'image $idImage</h2>";
        
        if ($isRegenerationPossible) {
            $html.=$t->createHtmlTableFromArray(3,  '',  '',  '');
        } else {
            $html.=$t->createHtmlTableFromArray(2,  '',  '',  '');
        }
        return $html;
    }
    
    
    /**
     * Recuperation des images d'une adresse en passant par les images liée aux evenements (et non aux adresses)
     * 
     * @param int   $idAdresse ID de l'adresse
     * @param array $params    Paramètres
     * 
     * @return Resource
     * */
    public function getImagesEvenementsFromAdresse($idAdresse=0,  $params=array())
    {
        $sqlGA = "";
        if (isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && $params['idEvenementGroupeAdresse']!='0') {
            $sqlGA = "AND ae.idEvenement = '".$params['idEvenementGroupeAdresse']."'";
        }
    
        // on va d'abord chercher les images qui ont une position a 1 ,  sinon on tri suivant l'idHistoriqueImage
        $req1 = "
                    SELECT hi1.idHistoriqueImage as idHistoriqueImage,  hi1.idImage as idImage,  hi1.dateUpload as dateUpload
            FROM historiqueImage hi2,  historiqueImage hi1
            
            RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = '".$idAdresse."'
            RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
            RIGHT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
            
            WHERE hi2.idImage = hi1.idImage
            AND ei.position<>0
            AND hi1.idImage = ei.idImage
            $sqlGA
            GROUP BY hi1.idImage, hi1.idHistoriqueImage
            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
            ORDER BY ei.position, hi1.idHistoriqueImage
        ";
        $res = $this->connexionBdd->requete($req1);
        if (mysql_num_rows($res)==0) {
            $req = "
            
                SELECT hi1.idHistoriqueImage as idHistoriqueImage,  hi1.idImage as idImage,  hi1.dateUpload as dateUpload
                FROM historiqueImage hi2,  historiqueImage hi1
                
                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = '".$idAdresse."'
                RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                RIGHT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
                
                WHERE hi2.idImage = hi1.idImage
                AND hi1.idImage = ei.idImage
                $sqlGA
                GROUP BY hi1.idImage, hi1.idHistoriqueImage
                HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                ORDER BY hi1.idHistoriqueImage
            ";
            
            $res = $this->connexionBdd->requete($req);
        }
        
        
        
        return $res;        
    }
    
    /**
     * Affichage de la liste des image dans le formulaire de modif de position
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function afficheFormulaireModifPosition($params=array())
    {
        $html="";
        
        $imageObj = new imageObject();
        $adresse = new archiAdresse();
        
        $html.="<h1>Position des images</h1><br>";
        
        
        $evenement = new archiEvenement();
        
        // affichages des recapitulatifs en haut de la page ( adresse + titres des evenements)
        $idEvenementGroupeAdresse = $evenement->getParent($this->variablesGet['archiIdEvenement']);
        
        $html.=$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse);
        $html.=$evenement->afficherRecapitulatifAncres($idEvenementGroupeAdresse,  $params['idEvenement']);
        $html.=$evenement->afficherLiensModificationEvenement($params['idEvenement']);
        
        $html.="Deplacez les images par drag and drop.";
        $html.= "<script>".$imageObj->getJSFunctionsDragAndDrop()."</script>";
        
        $reqImages = $this->getImagesFromEvenement(array('idEvenement'=>$params['idEvenement'],  'select'=>"hi1.idHistoriqueImage as idHistoriqueImage,  hi1.dateUpload as dateUpload"));
        $resImages = $this->connexionBdd->requete($reqImages);
        
        while ($fetch = mysql_fetch_assoc($resImages)) {
            $imageObj->addImageDragAndDrop(array('imageSrc'=>'getPhotoSquare.php?id='.$fetch['idHistoriqueImage'],  'idHistoriqueImage'=>$fetch['idHistoriqueImage']));
        }
        
        
        $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$params['idEvenement']));
        $resAdresses = $this->connexionBdd->requete($reqAdresses);
        $idAdresse = 0;
        if (mysql_num_rows($resAdresses)>0) {
            $fetchAdresses = mysql_fetch_assoc($resAdresses);
            $idAdresse = $fetchAdresses['idAdresse'];
        }
        
        $html.="<form action='".$this->creerUrl('enregistrePositionsImages',  'adresseDetail',  array('archiIdEvenement'=>$params['idEvenement'],  'archiIdAdresse'=>$idAdresse))."' name='formDragAndDrop' id='formDragAndDrop' method='POST' enctype='multipart/form-data'>";
        $html.="<table><tr><td>";
        $html.=$imageObj->getDragAndDrop();
        $html.="</td></tr></table>";
        $html.="<input type='submit' onclick=\"".$imageObj->getJSSubmitDragAndDrop()."\" name='validePosition' value='"._("Valider")."'>";
        $html.="</form>";
        
        return $html;
    }
    
    /**
     * Renvoi la liste des images d'un evenement en parametre sous forme de resultat de requete
     * 
     * @param array $params Paramètres
     * 
     * @return string Requête
     * */
    public function getImagesFromEvenement($params=array())
    {
        $req = "
                SELECT ".mysql_real_escape_string($params['select'])."
                FROM _evenementImage ei
                LEFT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
                LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
                WHERE idEvenement = ".mysql_real_escape_string($params['idEvenement'])."
                GROUP BY hi1.idImage,  hi1.idHistoriqueImage
                HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                ORDER BY ei.position, hi1.idHistoriqueImage
            ";
        return $req;
    }
    
    /**
     * Obtenir l'IdHistorique de l'image actuelle
     * 
     * @return int
     * */
    function getIdHistoriqueImage ()
    {
        $req = 'SELECT idHistoriqueImage FROM historiqueImage WHERE idImage = '.mysql_real_escape_string($this->idImage).' ORDER BY idHistoriqueImage DESC LIMIT 1';
        $res = $this->connexionBdd->requete($req);
        return mysql_fetch_object($res)->idHistoriqueImage;
    }
    
    
    /**
     * Enregistre les positions de l'image
     * 
     * @return void
     * */
    public function enregistrePositionImages()
    {
        $imageObj = new imageObject();
        
        $liste = $imageObj->getArrayFromPostDragAndDrop();
        
        foreach ($liste as $position => $idHistoriqueImage) {
            $reqIdImage = "
                            SELECT idImage 
                            FROM historiqueImage
                            WHERE
                                idHistoriqueImage = '".$idHistoriqueImage."'
                        ";
            $resIdImage = $this->connexionBdd->requete($reqIdImage);
            $fetchIdImage = mysql_fetch_assoc($resIdImage);
            
            $this->connexionBdd->requete("UPDATE _evenementImage SET position='".$position."' WHERE idImage='".$fetchIdImage['idImage']."'");
        }
    }

    /**
     * Recuperation des adresses auquelles appartient l'idImage
     * 
     * @param int $idImage ID de l'image
     * 
     * @return Resource
     * */
    public function getIdAdressesFromIdImage($idImage=0)
    {
        $req = "SELECT DISTINCT ae.idAdresse AS idAdresse
                FROM _adresseEvenement ae
                LEFT JOIN _evenementImage ei ON ei.idImage = '".$idImage."'
                LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                WHERE ae.idEvenement = ee.idEvenement
        ";
        return $this->connexionBdd->requete($req);
    }
    
    /**
     * Deplace les images selectionnées sur le detail d'un evenement par le bouton (importation d'images selectionnées)
     * 
     * @return void
     * */
    public function deplacerImagesSelectionnees()
    {
        if (isset($this->variablesGet['deplacerVersIdEvenement']) && $this->variablesGet['deplacerVersIdEvenement']!='') {
            if (isset($this->variablesPost['checkboxSelectionImages']) && count($this->variablesPost['checkboxSelectionImages']) >0) {
                foreach ($this->variablesPost['checkboxSelectionImages'] as $indice => $value) {
                    list($idEvenementDepart,  $idHistoriqueImage) = explode("_",  $value);
                    
                    if ($idEvenementDepart!='0' && $idEvenementDepart!='' && $idHistoriqueImage!='' && $idHistoriqueImage!='0') {
                        // on recupere l'idImage
                        $reqIdImage = "SELECT idImage FROM historiqueImage WHERE idHistoriqueImage = '".$idHistoriqueImage."'";
                        
                        $resIdImage = $this->connexionBdd->requete($reqIdImage);
                        
                        if (mysql_num_rows($resIdImage)>0) {
                            $fetchIdImage = mysql_fetch_assoc($resIdImage);
                            $idImage = $fetchIdImage["idImage"];
                            $reqVerif = "SELECT idEvenement,  idImage FROM _evenementImage WHERE idEvenement = '".$idEvenementDepart."' AND idImage='".$idImage."'";
                            $resVerif = $this->connexionBdd->requete($reqVerif);
                            if (mysql_num_rows($resVerif)>0) {
                                $reqUpdate = "UPDATE _evenementImage SET idEvenement='".$this->variablesGet['deplacerVersIdEvenement']."' WHERE idEvenement='".$idEvenementDepart."' AND idImage='".$idImage."'";
                                $resUpdate = $this->connexionBdd->requete($reqUpdate);
                            }
                        }
                    }
                }
            }
        }
    
        echo "<br>images déplacées<br>";
    }
    
    /**
     * Supprimer les images selectionnees
     * 
     * @return void
     * */
    public function supprimerImagesSelectionnees()
    {
        if (isset($this->variablesPost['actionFormulaireEvenement']) && $this->variablesPost['actionFormulaireEvenement']=='supprimerImages' && isset($this->variablesPost['checkboxSelectionImages']) && count($this->variablesPost['checkboxSelectionImages']) >0) {
            foreach ($this->variablesPost['checkboxSelectionImages'] as $indice => $value) {
                list($idEvenementDepart,  $idHistoriqueImage) = explode("_",  $value);
                
                if ($idEvenementDepart!='0' && $idEvenementDepart!='' && $idHistoriqueImage!='' && $idHistoriqueImage!='0') {
                    // on recupere l'idImage
                    $reqIdImage = "SELECT idImage FROM historiqueImage WHERE idHistoriqueImage = '".$idHistoriqueImage."'";
                    
                    $resIdImage = $this->connexionBdd->requete($reqIdImage);
                    
                    if (mysql_num_rows($resIdImage)>0) {
                        $fetchIdImage = mysql_fetch_assoc($resIdImage);
                        $idImage = $fetchIdImage["idImage"];
                        $reqVerif = "SELECT idEvenement,  idImage FROM _evenementImage WHERE idEvenement = '".$idEvenementDepart."' AND idImage='".$idImage."'";
                        $resVerif = $this->connexionBdd->requete($reqVerif);
                        if (mysql_num_rows($resVerif)>0) {
                            //supprimer
                            $this->deleteImage($idImage); // supprime tout ce qu'il faut
                        }
                    }
                }
            }
        }
        echo "<br>images supprimées<br>";
    }

    /**
     * Renvoi les evenements auxquelles appartiennent la photo
     * 
     * @param int $idImage ID de l'image
     * 
     * @return int ID de l'événement
     * */
    public function getArrayIdEvenementFromIdImage($idImage=0)
    {
        $req="
            SELECT ei.idEvenement as idEvenement
            FROM _evenementImage ei
            WHERE ei.idImage = $idImage
            ";
        $res = $this->connexionBdd->requete($req);
        
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch['idEvenement'];
        
    
    }
    
    /**
     * Renvoi un tableau contenant le max d'infos sur une image
     * 
     * @param int   $idImage ID de l'image
     * @param array $params  Paramètres
     * 
     * @return array
     * */
    public function getInfosCompletesFromIdImage($idImage=0,  $params=array())
    {
    
        $retour = array("idHistoriqueImage"=>'',  "idUtilisateur"=>'',  "dateUpload"=>'',  "dateCliche"=>'',  "description"=>'',  "vueSurIdAdresses"=>array(),  "prisDepuisIdAdresses"=>array(),  "vueSurLiens"=>array(),  "prisDepuisLiens"=>array());
    
        $reqImage="
                SELECT hi1.idHistoriqueImage as idHistoriqueImage,  hi1.idUtilisateur as idUtilisateur,  hi1.dateUpload as dateUpload,  hi1.dateCliche as dateCliche,  hi1.description as description
                FROM historiqueImage hi2,  historiqueImage hi1
                WHERE hi2.idImage = hi1.idImage
                AND hi1.idImage=$idImage
                ORDER BY hi1.idHistoriqueImage DESC
                ";
        $resImage = $this->connexionBdd->requete($reqImage);
        
        if (mysql_num_rows($resImage)>0) {
            $adresse = new archiAdresse();
            $fetchImage = mysql_fetch_assoc($resImage);
            $retour['idHistoriqueImage']=$fetchImage['idHistoriqueImage'];
            $retour['idUtilisateur']=$fetchImage['idUtilisateur'];
            $retour['dateUpload']=$fetchImage['dateUpload'];
            $retour['dateCliche']=$fetchImage['dateCliche'];
            $retour['description']=$fetchImage['description'];            
            
            $arrayIntituleAdressesVueSur=array();
            $arrayLiensHTMLVueSur = array();
            $reqImageVueSur = "SELECT * FROM _adresseImage WHERE idImage=$idImage AND vueSur='1'";
            $resImageVueSur = $this->connexionBdd->requete($reqImageVueSur);
            if (mysql_num_rows($resImageVueSur)>0) {
                while ($fetchImageVueSur = mysql_fetch_assoc($resImageVueSur)) {
                    $retour['vueSurIdAdresses'][]=$fetchImageVueSur['idAdresse'];
                    
                    if ($fetchImageVueSur['idEvenementGroupeAdresse']!='0') {
                        $intitule = $adresse->getIntituleAdresseFrom($fetchImageVueSur['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  $params);
                        $url = $this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$fetchImageVueSur['idEvenementGroupeAdresse'],  'archiIdAdresse'=>$fetchImageVueSur['idAdresse']));
                    } else {
                        $intitule = $adresse->getIntituleAdresseFrom($fetchImageVueSur['idAdresse'],  'idAdresse',  $params);
                        $url = $this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchImageVueSur['idAdresse']));
                    }
                    
                    //$arrayIntituleAdressesVueSur[] = array("intitule"=>$intitule,  "lien"=>$url,  "lienHTML"=>"<a href='$url'>$intitule</a>");
                    
                    
                    $mouseOver="";
                    $mouseOut="";
                    if (isset($params['withZonesOnMouseOver']) && $params['withZonesOnMouseOver']) {
                        $mouseOver="onMouseOver=\"document.getElementById('divZonesVueSurAdresse_".$fetchImageVueSur['idAdresse']."_image_".$idImage."').style.display='block';document.getElementById('divInfosVueSurAdresse_".$fetchImageVueSur['idAdresse']."_image_".$idImage."').style.display='block';\"";
                        $mouseOut="onMouseOut=\"document.getElementById('divZonesVueSurAdresse_".$fetchImageVueSur['idAdresse']."_image_".$idImage."').style.display='none';document.getElementById('divInfosVueSurAdresse_".$fetchImageVueSur['idAdresse']."_image_".$idImage."').style.display='none';\"";
                    }
                    
                    $arrayLiensHTMLVueSur[] = "<a href='$url' $mouseOver $mouseOut>$intitule</a>";
                }
                
                $retour['vueSurLiens'] = $arrayLiensHTMLVueSur;
            }
            
            $arrayIntituleAdressesPrisDepuis=array();
            $arrayLienHTMLPrisDepuis = array();
            $reqImagePrisDepuis = "SELECT * FROM _adresseImage WHERE idImage=$idImage AND prisDepuis='1'";
            $resImagePrisDepuis = $this->connexionBdd->requete($reqImagePrisDepuis);
            if (mysql_num_rows($resImagePrisDepuis)>0) {
                while ($fetchImagePrisDepuis = mysql_fetch_assoc($resImagePrisDepuis)) {
                    $retour['prisDepuisIdAdresses'][]=$fetchImagePrisDepuis['idAdresse'];
                    if ($fetchImagePrisDepuis['idEvenementGroupeAdresse']!='0') {
                        $intitule = $adresse->getIntituleAdresseFrom($fetchImagePrisDepuis['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  $params);
                        $url = $this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdEvenementGroupeAdresse'=>$fetchImagePrisDepuis['idEvenementGroupeAdresse'],  'archiIdAdresse'=>$fetchImagePrisDepuis['idAdresse']));
                    } else {
                        $intitule = $adresse->getIntituleAdresseFrom($fetchImagePrisDepuis['idAdresse'],  'idAdresse',  $params);
                        $url = $this->creerUrl('',  'adresseDetail',  array('archiIdAdresse'=>$fetchImagePrisDepuis['idAdresse']));
                    }
                    
                    //$arrayIntituleAdressesPrisDepuis[] = array("intitule"=>$intitule,  "lien"=>$url,  "lienHTML"=>"<a href='$url'>$intitule</a>");
                    $arrayLienHTMLPrisDepuis[] = "<a href='$url'>$intitule</a>";
                }
                $retour['prisDepuisLiens']= $arrayLienHTMLPrisDepuis;
            }
        }
                
        return $retour;
    }
    
    /**
     * Recupere l'image principale
     * 
     * @param array $params Paramètres
     * 
     * @return array
     * */
    public function getArrayInfosImagePrincipaleFromIdGroupeAdresse($params = array())
    {
        $retour = array();
        
        $retour['trouve'] = false;
        if (isset($params['idEvenementGroupeAdresse']) && isset($params['format'])) {
            $idEvenementGroupeAdresse = $params['idEvenementGroupeAdresse'];
            
            $req = "
                SELECT he1.idImagePrincipale as idImagePrincipale
                FROM historiqueEvenement he2,  historiqueEvenement he1
                WHERE he1.idEvenement = $idEvenementGroupeAdresse
                AND he1.idTypeEvenement='11'
                AND he1.idImagePrincipale !='0'
                AND he2.idEvenement = he1.idEvenement
                GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ";
            
            $res = $this->connexionBdd->requete($req);
            
            if (mysql_num_rows($res)>0) {
                $fetch = mysql_fetch_assoc($res);
                if (isset($fetch['idImagePrincipale']) && $fetch['idImagePrincipale']!='0' && $fetch['idImagePrincipale']!='') {
                    // idImage trouvé
                    $reqImage = "
                        SELECT hi1.dateUpload as dateUpload,  hi1.idHistoriqueImage as idHistoriqueImage
                        FROM historiqueImage hi2,  historiqueImage hi1
                        WHERE hi1.idImage = '".$fetch['idImagePrincipale']."'
                        AND hi2.idImage = hi1.idImage
                        GROUP BY hi1.idImage ,  hi1.idHistoriqueImage
                        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)            
                    
                    ";
                    $resImage = $this->connexionBdd->requete($reqImage);
                    
                    
                    
                    if (mysql_num_rows($resImage)>0) {
                        $string = new stringObject();
                        $adresse = new archiAdresse();
                        
                        $fetchImage = mysql_fetch_assoc($resImage);
                    
                        $retour['idImage'] = $fetch['idImagePrincipale'];
                        $retour['idHistoriqueImage'] = $fetchImage['idHistoriqueImage'];
                        $retour['dateUpload'] = $fetchImage['dateUpload'];
                        $retour['trouve'] = true;
                        
                        $intitule = $adresse->getIntituleAdresseFrom($fetch['idImagePrincipale'],  'idImage');
                        
                        
                        $retour['url'] = 'photos-'.$string->convertStringToUrlRewrite($intitule).'-'.$fetchImage['dateUpload'].'-'.$fetchImage['idHistoriqueImage'].'-'.$params['format'].'.jpg';
                    
                    }
                }
                
            }
        } else {
            echo "<br>ATTENTION : format ou evenementGroupeAdresse non précisé dans archiImage::getArrayInfosImagePrincipaleFromIdGroupeAdresse<br>";
        }
        
        return $retour;
    }
    
    
    /** 
     * Renvoi la description de l'image dont l'idImage est passé en parametre
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getDescriptionFromIdImage($params = array())
    {
        $html="";
        
        $req = "
            SELECT hi1.description as description
            FROM historiqueImage hi2,  historiqueImage hi1
            WHERE hi2.idImage = hi1.idImage
            AND hi1.idImage = '".$params['idImage']."'
            GROUP BY hi1.idImage,  hi1.idHistoriqueImage
            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
        ";
        
        $res = $this->connexionBdd->requete($req);
        if (mysql_num_rows($res)>0) {
            $fetch = mysql_fetch_assoc($res);
            $html = stripslashes($fetch['description']);
        }
        
        return $html;
    
    }
    
    
    /**
     * Fonction pour la page d'accueil qui recupere les dernieres images "vueSur"
     * 
     * @param array $params Paramètres
     * 
     * @return mixed
     * */
    public function getDernieresVues($params = array())
    {
        $sqlLimit = "";
        if (isset($params['sqlLimit']) && $params['sqlLimit']!='') {
            $sqlLimit = $params['sqlLimit'];
        }
        
        
        $sqlFields = ", hi1.dateUpload as dateUpload, hi1.idHistoriqueImage as idHistoriqueImage, ai.idEvenementGroupeAdresse as idEvenementGroupeAdresse";
        if (isset($params['sqlFields']) && $params['sqlFields']!='') {
            $sqlFields = ",  ".$params['sqlFields'];
        }
        
        if (isset($params['getNbVuesTotal']) && $params['getNbVuesTotal']==true) {
            $sqlFields = "";
            $sqlLimit = "";
        }
        
        $sqlWhere = "";
        if (isset($params['listeIdGroupesAdressesVueSurANePasAfficher']) && is_array($params['listeIdGroupesAdressesVueSurANePasAfficher']) && count($params['listeIdGroupesAdressesVueSurANePasAfficher'])>0) {
            $sqlWhere = " AND ai.idEvenementGroupeAdresse NOT IN (".implode(",  ",  $params['listeIdGroupesAdressesVueSurANePasAfficher']).") ";
        }

        // par defaut on accepte les doublons d'adresses
        $sqlSelect = "ai.idImage as idImage,  ai.idEvenementGroupeAdresse as idEvenementGroupeAdresse, hi1.dateUpload as dateUpload,  hi1.idHistoriqueImage as idHistoriqueImage";
        if (isset($params['noAdressesDoublons']) && $params['noAdressesDoublons'] == true) {
            // si on ne veut pas de doublons au niveau des adresses ,  on selectionne uniquement les adresses différentes dans cette requete ,  et une autre requete a l'interieur de la boucle cherchera les infos sur les images
            // ce cas gere le fait qu'on puisse avoir plusieurs nouvelles photos sur la meme adresse,  or par exemple sur la page d'accueil on ne veut afficher que des adresses différentes
            $sqlSelect = "distinct ai.idAdresse as idAdresse";
        }
        
        $reqVuesAdresses = "
            SELECT $sqlSelect
            FROM _adresseImage ai
            LEFT JOIN historiqueImage hi1 ON hi1.idImage = ai.idImage
            LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
            WHERE ai.vueSur='1'
            AND ai.idEvenementGroupeAdresse<>'0'
            $sqlWhere
            GROUP BY hi1.idImage,  hi1.idHistoriqueImage
            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
            ORDER BY hi1.dateUpload DESC
            $sqlLimit
            ";
        
        
        $resVuesAdresses=$this->connexionBdd->requete($reqVuesAdresses);
        
        if (!isset($params['getNbVuesTotal']) || $params['getNbVuesTotal']!=true) {
            $retour = array();
            $i=0;

            while ($fetchVuesAdresses = mysql_fetch_assoc($resVuesAdresses)) {
                if (isset($params['noAdressesDoublons']) && $params['noAdressesDoublons'] == true) {
                    // si on ne veut pas de doublons d'adresses dans la liste,  dans la requete precedente on a fait un distinct sur les adresses,  et donc on cherche les infos dont on a besoin dans la requete suivante
                    $reqVues = " 
                        SELECT ai.idImage as idImage,  ai.idEvenementGroupeAdresse as idEvenementGroupeAdresse, hi1.dateUpload as dateUpload,  hi1.idHistoriqueImage as idHistoriqueImage
                        FROM _adresseImage ai
                        LEFT JOIN historiqueImage hi1 ON hi1.idImage = ai.idImage
                        LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
                        WHERE ai.vueSur='1'
                        AND ai.idEvenementGroupeAdresse<>'0'
                        AND ai.idAdresse = '".$fetchVuesAdresses['idAdresse']."'
                        GROUP BY hi1.idImage,  hi1.idHistoriqueImage
                        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                        LIMIT 1
                    ";
                    
                    
                    $resVues = $this->connexionBdd->requete($reqVues);
                    $fetchVues = mysql_fetch_assoc($resVues);
                } else {
                    $fetchVues = $fetchVuesAdresses;
                }
                
                
                $reqPrisDepuis = "
                    SELECT ai.idAdresse as idAdresse, ai.idEvenementGroupeAdresse as idEvenementGroupeAdresse
                    FROM _adresseImage ai
                    WHERE prisDepuis='1'
                    AND idImage = '".$fetchVues['idImage']."'
                    
                ";
                $resPrisDepuis = $this->connexionBdd->requete($reqPrisDepuis);
                $fetchVues['listePrisDepuis'] = array();
                while ($fetchPrisDepuis = mysql_fetch_assoc($resPrisDepuis)) {
                    $fetchVues['listePrisDepuis'][] = $fetchPrisDepuis;
                }
                
                
                
                $reqVueSur = "
                    SELECT ai.idAdresse as idAdresse, ai.idEvenementGroupeAdresse as idEvenementGroupeAdresse
                    FROM _adresseImage ai
                    WHERE vueSur='1'
                    AND idImage='".$fetchVues['idImage']."'
                
                ";
                
                $resVuesSur = $this->connexionBdd->requete($reqVueSur);
                
                $fetchVues['listeVueSur'] = array();
                while ($fetchVuesSur = mysql_fetch_assoc($resVuesSur)) {
                    $fetchVues['listeVueSur'][] = $fetchVuesSur;
                }
                
                $retour[$i] = $fetchVues;
                $i++;
                    
                
            }
        } else {
            $retour = mysql_num_rows($resVuesAdresses);
        }
        return $retour;
    }
    
    /** 
     * Renvoi l'affichage de toutes les vues avec pagination pour le lien sur la page d'accueil
     * 
     * @return string
     * */
    public function getHtmlToutesLesVues()
    {
        $html = "<h1>Vues</h1><br>";
        
        $pagination = new paginationObject();
        $d = new dateObject();
        $adresse = new archiAdresse();
        $string = new stringObject();
        $bbCode = new bbCodeObject();
        
        $nbEnregistrementTotaux = $this->getDernieresVues(array("getNbVuesTotal"=>true));
        
        
        $nbEnregistrementsParPage=15;
        
        $arrayPagination=$pagination->pagination(
            array(
                'nomParamPageCourante'=>'page', 
                'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 
                'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux, 
                'typeLiens'=>'noformulaire'
            )
        );
        
        $arrayVues = $this->getDernieresVues(array("sqlLimit"=>"LIMIT ".$arrayPagination['limitSqlDebut'].",  ".$nbEnregistrementsParPage));
        
        $tab = new tableau();
        $i=0;
        foreach ($arrayVues as $indice => $value) {
            $arrayIntituleAdressesVuesSur = array();
            foreach ($value['listeVueSur'] as $indice => $valueVuesSur) {
                $arrayIntituleAdressesVuesSur[] = $adresse->getIntituleAdresseFrom($valueVuesSur['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'noVille'=>true,  'noQuartier'=>true,  'noSousQuartier'=>true));
            }
            
            $arrayIntituleAdressesPrisDepuis = array();
            foreach ($value['listePrisDepuis'] as $indice => $valuePrisDepuis) {
                $arrayIntituleAdressesPrisDepuis[] = "<a href='".$this->creerUrl('',  '',  array('archiAffichage'=>'adresseDetail',  'archiIdAdresse'=>$valuePrisDepuis['idAdresse'],  'archiIdEvenementGroupeAdresse'=>$valuePrisDepuis['idEvenementGroupeAdresse']))."'>".$adresse->getIntituleAdresseFrom($valuePrisDepuis['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'noVille'=>true,  'noQuartier'=>true,  'noSousQuartier'=>true))."</a>";
            }
            
            
            $intituleAdresse1Adresse = $adresse->getIntituleAdresseFrom($value['idEvenementGroupeAdresse'],  'idEvenementGroupeAdresse',  array('ifTitreAfficheTitreSeulement'=>true,  'noVille'=>true,  'noQuartier'=>true,  'noSousQuartier'=>true));
            $intituleAdresseAlt =  strip_tags(str_replace("\"",  "'",  $intituleAdresse1Adresse));
            
            $intituleAdresseVueSur = implode("/",  $arrayIntituleAdressesVuesSur);
            $intituleAdressePrisDepuis = implode("",  $arrayIntituleAdressesPrisDepuis);
            
            $urlImage = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse1Adresse).'-'.$value['dateUpload'].'-'.$value['idHistoriqueImage'].'-mini.jpg';
            
            
            $tab->addValue("<a href='".$this->creerUrl('',  'imageDetail',  array("archiIdImage"=>$value['idImage'],  "archiRetourAffichage"=>'evenement',  "archiRetourIdName"=>'idEvenement',  "archiRetourIdValue"=>$value['idEvenementGroupeAdresse']))."'>".date('d/m/Y', strtotime($value['dateUpload']))." ".$intituleAdresseVueSur."</a><br><span style='font-weight:bold;font-size:14px;'>Pris depuis</span> <span style='font-size:14px;'>".$intituleAdressePrisDepuis."</span><br><span style='font-size:12px;'>".$string->coupureTexte($bbCode->convertToDisplay(array('text'=>$this->getDescriptionFromIdImage(array("idImage"=>$value['idImage'])))), 15)."</span>");
            $tab->addValue("<img style='margin-right:2px;float:left;' align='middle' src='".$urlImage."' alt='' title=\"".$intituleAdresseAlt."\" alt=\"".$intituleAdresseAlt."\">");

            $i++;
        }
        
        $html.=$arrayPagination['html'];
        $html.=$tab->createHtmlTableFromArray(2,  'margin:0;padding:0;',  '',  '');
        
    
        return $html;
    }
    
    /**
     * Fonction permettant d'afficher les images d'un utilisateur,
     * fonction de debug
     * 
     * @param array $params Paramètres
     * 
     * @return string
     * */
    public function afficheImagesFromUtilisateurDebug($params = array())
    {
        $html = "";
        $f = new fileObject();
        
        $sqlLimit = 10;
        if (isset($this->variablesGet['limit']) && $this->variablesGet['limit']!='') {
            $sqlLimit = $this->variablesGet['limit'];
        }
        
        $sqlDateDebut = "";
        if (isset($this->variablesGet['dateDebut']) && $this->variablesGet['dateDebut']!='') {
            $sqlDateDebut = "AND h1.dateUpload>='".$this->variablesGet['dateDebut']."' ";
        }
        
        $sqlIdUtilisateur ="";
        if (isset($this->variablesGet['idUtilisateur']) && $this->variablesGet['idUtilisateur']!='') {
            $sqlIdUtilisateur = "AND h1.idUtilisateur = '".$this->variablesGet['idUtilisateur']."'";
        }
        

        $reqImages = "
            SELECT h1.dateUpload as dateUpload, h1.idImage as idImage, h1.idHistoriqueImage as idHistoriqueImage, h1.idUtilisateur as idUtilisateur
            FROM historiqueImage h2,  historiqueImage h1
            WHERE 1=1
            $sqlIdUtilisateur
            AND h2.idImage = h1.idImage
            $sqlDateDebut
            GROUP BY h1.idImage, h1.idHistoriqueImage
            HAVING h1.idHistoriqueImage = max(h2.idHistoriqueImage)
            ORDER BY h1.dateUpload DESC
            LIMIT $sqlLimit
            ";
        
        $resImages = $this->connexionBdd->requete($reqImages);
        
        while ($fetchImages = mysql_fetch_assoc($resImages)) {
            $html.="<img src='".$this->getUrlImage("originaux").$fetchImages['dateUpload']."/".$fetchImages['idHistoriqueImage'].".jpg' alt=''><br><a href='".$this->creerUrl('',  'imageDetail',  array('archiIdImage'=>$fetchImages['idImage']))."'>".$fetchImages['idUtilisateur']." ".$fetchImages['dateUpload']."</a> ";
            $html.=$f->fileSize($this->getCheminPhysiqueImage("originaux").$fetchImages['dateUpload']."/".$fetchImages['idHistoriqueImage'].".jpg");
            $html.="<br>";
        }

        
        return $html;
    }
    
    /**
     * Test ponctuel pour voir le crc d'une image et voir si la fonction de CRC32 marche
     * 
     * @param array $params Paramètres
     * 
     * @return string
     * */
    public function testImagesCRC($params = array())
    {
        $retour = "";
        
        $f = new fileObject();
        echo $f->crc32_file($this->getCheminPhysique()."images/testImages/17736.jpg");
        echo "<br>";
        echo $f->crc32_file($this->getCheminPhysique()."images/testImages/P1070499.JPG");
        
        return $retour;
    }
    
    /**
     * ?
     * 
     * @param array $params Paramètres
     * 
     * @return  void
     * */
    public function regenereImageFromUploadDirectory($params = array())
    {
        if (isset($this->variablesGet['archiIdHistoriqueImage']) && $this->variablesGet['archiIdHistoriqueImage']!='' && isset($this->variablesGet['archiIdImage']) && $this->variablesGet['archiIdImage']!='') {
            $idHistoriqueImage = $this->variablesGet['archiIdHistoriqueImage'];
            $idImage = $this->variablesGet['archiIdImage'];
            
            $reqRegenere = "SELECT idImage, cheminImageUploadee, idHistoriqueImage FROM imagesUploadeesPourRegeneration WHERE idHistoriqueImage='".$idHistoriqueImage."' AND idImage='".$idImage."'";
            $resRegenere = $this->connexionBdd->requete($reqRegenere);
            
            if (mysql_num_rows($resRegenere)>0) {
                $fetchRegenere = mysql_fetch_assoc($resRegenere);
                
                if (file_exists($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee'])) {
                
                    // on recupere les infos de l'image de destination (dateUpload etc)
                    $reqImage = "
                            SELECT hi.idImage as idImage,  hi.idHistoriqueImage as idHistoriqueImage,  hi.dateUpload as dateUpload
                            FROM historiqueImage hi
                            WHERE
                                hi.idHistoriqueImage = '".$fetchRegenere['idHistoriqueImage']."'
                            AND
                                hi.idImage = '".$fetchRegenere['idImage']."'
                            ";
                    $resImage = $this->connexionBdd->requete($reqImage);
                    
                    
                    if (mysql_num_rows($resImage)>0) {
                        $fetchImage = mysql_fetch_assoc($resImage);
                        $dateUpload = $fetchImage['dateUpload'];
                
                
                        if (!$this->redimension($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee'],  pia_substr(strtolower($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee']),  -3),  $this->getCheminPhysiqueImage("originaux").$dateUpload."/".$fetchRegenere['idHistoriqueImage'].".jpg",  0)) {
                            echo "Il y a eu un problème avec la génération du fichier de format 'original'<br>";
                        } else {
                            echo "Image format 'original' ... régénéré<br>";
                        }
                        // 2- redimensionnement au format mini
                        
                        if (!$this->redimension($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee'],  pia_substr(strtolower($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee']),  -3),  $this->getCheminPhysiqueImage("mini").$dateUpload."/".$fetchRegenere['idHistoriqueImage'].".jpg",  $this->getFormatImageMini())) {
                            echo "Il y a eu un problème avec la génération du fichier de format 'mini'<br>";
                        } else {
                            echo "Image format 'mini' ... régénéré<br>";
                        }
                        
                        // 3- redimensionnement au format moyen
                        if (!$this->redimension($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee'],  pia_substr(strtolower($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee']),  -3),  $this->getCheminPhysiqueImage("moyen").$dateUpload."/".$fetchRegenere['idHistoriqueImage'].".jpg",  $this->getFormatImageMoyen())) {
                            echo "Il y a eu un problème avec la génération du fichier de format 'moyen'<br>";
                        } else {
                            echo "Image format 'moyen' ... régénéré<br>";
                        }
                        
                        // 4- redimensionnement au format grand
                        if (!$this->redimension($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee'],  pia_substr(strtolower($this->getCheminPhysique()."/images/uploadMultiple/".$fetchRegenere['cheminImageUploadee']),  -3),  $this->getCheminPhysiqueImage("grand").$dateUpload."/".$fetchRegenere['idHistoriqueImage'].".jpg",  $this->getFormatImageGrand())) {
                            echo "Il y a eu un problème avec la génération du fichier de format 'grand'<br>";
                        } else {
                            echo "Image format 'grand' ... régénéré<br>";
                        }
                        
                    }
                }
            }
        }
    }
    
    /**
     * Obtenir la licence d'une photo
     * 
     * @param int $idImage ID de l'image
     * 
     * @return array
     */
    function getLicence($idImage=null)
    {
        $idImage = isset($idImage)?$idImage:$this->getID();
        $licence = mysql_fetch_assoc($this->connexionBdd->requete("SELECT licence FROM historiqueImage WHERE idImage = '".$idImage."' ORDER BY idHistoriqueImage DESC"));
        $licence = ($licence["licence"]==0)?1:$licence["licence"];
        return mysql_fetch_assoc($this->connexionBdd->requete("SELECT * FROM licences WHERE id = '".$licence."'"));
    }
    
    /**
     * Obtenir l'ID de l'image actuelle
     * 
     * @return int ID
     * */
    function getID ()
    {
        if (isset($this->variablesGet["archiIdImageModification"])) {
            return $this->variablesGet["archiIdImageModification"];
        } else if (isset($this->variablesGet["archiIdImage"])) {
            return $this->variablesGet["archiIdImage"];
        } else {
            return $this->idImage;
        }
    }
    
    /**
     * Obtenir l'auteur d'une photo
     * 
     * @param int $idImage ID de l'image
     * 
     * @return mixed Tableau si c'est un utilisateur, chaine sinon
     * */
    function getAuteur($idImage=null)
    {
        $idImage = isset($idImage)?$idImage:$this->getID();
        $auteur=mysql_fetch_assoc($this->connexionBdd->requete("SELECT auteur FROM historiqueImage WHERE idImage = '".$idImage."' ORDER BY idHistoriqueImage DESC"));
        if (!empty($auteur["auteur"])) {
            return $auteur["auteur"];
        } else {
            $idAuteur = mysql_fetch_assoc($this->connexionBdd->requete("SELECT idUtilisateur FROM historiqueImage WHERE idImage = '".$idImage."'"));
            $auteur = mysql_fetch_assoc($this->connexionBdd->requete("SELECT `nom`,  `prenom` FROM utilisateur WHERE idUtilisateur = '".$idAuteur["idUtilisateur"]."'"));
            return array("nom"=>$auteur["prenom"]." ".$auteur["nom"],  "id"=>$idAuteur["idUtilisateur"]);
        }
    }
}
?>
