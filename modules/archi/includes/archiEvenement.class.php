<?php

class archiEvenement extends config
{
    private $intitule;
    private $dateDebut;
    private $dateFin;
    private $titre;
    private $idEvenement;

    function __construct($idEvenement = null)
    {
        $this->idEvenement = $idEvenement;
        parent::__construct();
    }
    
    public function getEvenementFields($typeFormulaire='')
    {
        $tab=array();
        switch($typeFormulaire)
        {
            case 'nouveauDossier':
                $tab= array(
                'titre'                     => array('default'=>'','value'=>'', 'required'=>false , 'error'=>'' , 'type'=>'text'),
                'typeEvenement'             => array('default'=> '0'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                        array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
                'courant'       => array('default'=> '0'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'multiple', 'checkExist'=>
                                        array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
                'typeStructure' => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                        array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
                'personnes' => array('default'=> '0' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
                                array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
                'source'    => array('default'=> '0' , 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                        array('table'=> 'source', 'primaryKey'=> 'idSource')),
                'description'   => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
                'dateDebut' => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'date'),
                'isDateDebutEnviron' =>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'dateFin'   => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'date'),
                'nbEtages'=>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text'),
                'ISMH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'MH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'numeroArchive'=>array('default'=>'','value'=>'', 'required'=>false , 'error'=>'' , 'type'=>'text')
                );
            break;
            default:
                $tab = array(
                'titre'     => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
                'description'   => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
                'dateDebut' => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'date'),
                'isDateDebutEnviron' =>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'dateFin'   => array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'date'),
                'idSource'  => array('default'=> '' , 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                array('table'=> 'source', 'primaryKey'=> 'idSource')),
                'nbEtages' => array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'text'),
                'typeStructure' => array('default'=> 'aucune ', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
                'typeEvenement' => array('default'=> 'aucun'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
                'personnes' => array('default'=> 'aucune' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
                                array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
                'courant'       => array('default'=> 'aucun'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'multiple', 'checkExist'=>
                                array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
                'idEvenement'   => array('default'=> ''  , 'value' => '', 'required'=>false,'error'=>'','type'=>'numeric', 'checkExist'=>
                                array('table'=> 'historiqueEvenement', 'primaryKey'=> 'idEvenement')),
                'adresses'  => array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'' , 'type'=>'multiple', 'checkExist'=>
                                array('table'=>'historiqueAdresse','primaryKey'=>'idAdresse')),
                'evenements'    => array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'' , 'type'=>'multiple', 'checkExist'=>
                                array('table'=>'historiqueEvenement','primaryKey'=>'idEvenement')),
                'ISMH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'MH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
                'numeroArchive'=>array('default'=>'','value'=>'', 'required'=>false , 'error'=>'' , 'type'=>'text')
                );
            break;
        }
        
        return $tab;
    }
    
    // **********************************************************************************************************************************************************************
    // ajoute un evenement provenant du formulaire d'ajout evenement + groupe adresse
    // retourne l'id de l'evenement groupe d'adresses
    // **********************************************************************************************************************************************************************
    public function ajouterEvenementNouveauDossier()
    {
        $retour = array('idEvenementGroupeAdresse'=>0,'idSousEvenement'=>0,'errors'=>array());
        
        $idEvenementGroupeAdresse=0;
        $formulaire = new formGenerator();  
        $tabForm=$this->getEvenementFields('nouveauDossier');
        $errors = $formulaire->getArrayFromPost($tabForm);
        
        $authentification = new archiAuthentification();
        $idUtilisateur   = $authentification->getIdUtilisateur();
        
        if(count($errors)==0)
        {
            //$this->connexionBdd->getLock(array('historiqueEvenement'));
                
            // *****************************************************
            // ajout de l'evenement groupe d'adresses
            $idEvenementGroupeAdresse = $this->getNewIdEvenement();
            
            
            // creation de l'evenement parent groupe d'adresse
            $sql = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement,dateCreationEvenement)
                    VALUES (".$idEvenementGroupeAdresse.", '', '', '', '', ".$tabForm['source']['value'].", ".$idUtilisateur.", 0, ".$this->getIdTypeEvenementGroupeAdresse().",now())";
            
            $this->connexionBdd->requete($sql);
            
            // *****************************************************
            // on recupere l'id de l'evenement enfant ( construction)
            $idSousEvenement = $this->getNewIdEvenement();
            
            
            // ajout de l'evenement fils
            $libelle            =   $tabForm['titre']['value'];
            $description        =   $tabForm['description']['value'];
            $dateDebut          =   $this->date->toBdd($this->date->convertYears($tabForm['dateDebut']['value']));
            $isDateDebutEnviron =   $tabForm['isDateDebutEnviron']['value'];
            $dateFin            =   $this->date->toBdd($this->date->convertYears($tabForm['dateFin']['value']));
            $idSource           =   $tabForm['source']['value'];
            
            $idTypeStructure    = $tabForm['typeStructure']['value'];
            $idTypeEvenement    = $tabForm['typeEvenement']['value'];
            $numeroArchive      = $tabForm['numeroArchive']['value'];
            
            
            $groupeFromTypeEvenement = $this->getGroupeFromTypeEvenement($idTypeEvenement);
            
            
            
            switch($groupeFromTypeEvenement)
            {
                case '1':
                    // culturel
                    $ISMH   = $tabForm['ISMH']['value'];
                    $MH     = $tabForm['MH']['value'];
                    $nbEtages='';
                break;
                
                case '2':
                    // travaux
                    $ISMH=0;
                    $MH=0;
                    $nbEtages = $tabForm['nbEtages']['value'];
                    
                    // enregistrement des courants architecturaux liés au sous evenement 'construction'
                    $courants = new archiCourantArchitectural();
                    $courants->enregistreLiaisonEvenement($idSousEvenement);

                break;
            }
            //Valeurs vides si le contenu n'est pas concerné
            $ISMH=isset($ISMH)?$ISMH:"";
            $MH=isset($MH)?$MH:"";
            $nbEtages=isset($nbEtages)?$nbEtages:"";
            $idTypeStructure=isset($idTypeStructure)?$idTypeStructure:0;
            $idTypeEvenement=isset($idTypeEvenement)?$idTypeEvenement:0;
            
            $sqlHistoriqueEvenement = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement,dateCreationEvenement,ISMH , MH , nbEtages,isDateDebutEnviron,numeroArchive)
            VALUES (".mysql_real_escape_string($idSousEvenement).", \"".mysql_real_escape_string($libelle)."\", \"".mysql_real_escape_string($description)."\", '".mysql_real_escape_string($dateDebut)."', '".mysql_real_escape_string($dateFin)."', ".mysql_real_escape_string($idSource).", ".mysql_real_escape_string($idUtilisateur).", '".mysql_real_escape_string($idTypeStructure)."', '".mysql_real_escape_string($idTypeEvenement)."', now(), '".mysql_real_escape_string($ISMH)."', '".mysql_real_escape_string($MH)."', '".mysql_real_escape_string($nbEtages)."', '".mysql_real_escape_string($isDateDebutEnviron)."',\"".mysql_real_escape_string($numeroArchive)."\")";
            $this->connexionBdd->requete($sqlHistoriqueEvenement );


            // on relie l'evenement pere (groupe d'adresse ) à l'evenement fils
            $sqlAssociationNettoie = "delete from _evenementEvenement where idEvenement = '".$idEvenementGroupeAdresse."'";
            $resAssociationNettoie = $this->connexionBdd->requete($sqlAssociationNettoie);
            $sqlAssociation = "insert into _evenementEvenement (idEvenement,idEvenementAssocie) values ('".$idEvenementGroupeAdresse."','".$idSousEvenement."')";
            $resAssociation = $this->connexionBdd->requete($sqlAssociation);


            // on met a jour les positions des evenements du groupe d'adresse (meme s'il n'y en a qu'une)
            $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'idNouvelEvenement'=>$idSousEvenement));

            
            // enregistrement des personnes liées au sous evenement 'construction'
            $personnes = new archiPersonne();
            $personnes->enregistreLiaisonEvenement($idSousEvenement);
            
            //$this->connexionBdd->freeLock(array('historiqueEvenement'));
            $retour = array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'idSousEvenement'=>$idSousEvenement,'errors'=>array());
        }
        else
        {
            // erreur dans la saisie de l'evenement
            $this->erreurs->ajouter("Erreur dans la saisie de l'évènement");
            echo "erreur dans la saise de l'evenement";
            $retour = array('idEvenementGroupeAdresse'=>0,'idSousEvenement'=>0,'errors'=>$errors);
        }
        
        return $retour;
    }
    
    // **********************************************************************************************************************************************************************
    // recupere le groupe a partir d'un type d'evenement ( groupe = travaux ou culturel )
    // **********************************************************************************************************************************************************************
    public function getGroupeFromTypeEvenement($idTypeEvenement=0)
    {
        $req = "select groupe from typeEvenement where idTypeEvenement = '".$idTypeEvenement."'";
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch['groupe'];
    }
    
    // **********************************************************************************************************************************************************************
    // 
    // **********************************************************************************************************************************************************************
    public function ajouter()
    {
        $html = '';
        $erreur = array();
        $tabForm = array();
        $ajoutOk = false;
        $aAuthentification = new archiAuthentification();
        $formulaire = new formGenerator();
        if ( $aAuthentification->estConnecte() == 0)
        {
            echo 'utilisateur non connecté';
            $this->erreurs->ajouter('Vous n\'êtes pas connecté !');
        }
        else if (isset($this->variablesPost['ajoutSousEvenement']))
        {
            $tabForm=$this->getEvenementFields('nouveauDossier');
            
            $erreur = $formulaire->getArrayFromPost($tabForm);

            if (count($erreur) == 0)
            {
                $this->connexionBdd->getLock(array('historiqueEvenement'));
                
                //***************
                //**  GESTION DES VARIABLES
                //**
                
                $titre              = mysql_escape_string( $tabForm['titre']['value'] );
                $description        = mysql_escape_string( $tabForm['description']['value'] );
                $dateDebut          = $this->date->toBdd($this->date->convertYears($tabForm['dateDebut']['value']));
                $isDateDebutEnviron = $tabForm['isDateDebutEnviron']['value'];
                $dateFin            = $this->date->toBdd($this->date->convertYears($tabForm['dateFin']['value']));
                $idSource=0;
                if($tabForm['source']['value']!='')
                $idSource    = $tabForm['source']['value'];
                $idUtilisateur   = $aAuthentification->getIdUtilisateur();
                $idTypeStructure = $tabForm['typeStructure']['value'];
                $idTypeEvenement = $tabForm['typeEvenement']['value'];
                $nbEtages = $tabForm['nbEtages']['value'];
                
                
                $ISMH           = $tabForm['ISMH']['value'];
                $MH             = $tabForm['MH']['value'];
                
                
                //**************
                //**  VERIFICATION DOUBLON
                //**
                
                $sqlVerificationDoublon = "
                SELECT he1.idEvenement
                FROM historiqueEvenement he2, historiqueEvenement he1
                RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = '".$this->variablesPost['evenementGroupeAdresse']."'
                WHERE he2.idEvenement = he1.idEvenement
                AND he1.titre='".$titre."'
                AND he1.idEvenement = ee.idEvenementAssocie
                AND he1.description = \"".$description."\"
                AND he1.dateDebut = \"".$dateDebut."\"
                AND he1.isDateDebutEnviron = '".$isDateDebutEnviron."'
                AND he1.dateFin = \"".$dateFin."\"
                AND he1.idSource = '".$idSource."'
                AND he1.idTypeStructure='".$idTypeStructure."'
                AND he1.idTypeEvenement='".$idTypeEvenement."'
                GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                ";
                
        

                $res = $this->connexionBdd->requete($sqlVerificationDoublon);
                
                if (mysql_num_rows($res) != 0)
                {
                    $this->erreurs->ajouter(_("Exactement le même enregistrement existe déjà !"));
                    
                    // on défini les informations pour pouvoir tout de même afficher l'évènement
                    $rep         = mysql_fetch_object($res);
                    $idEvenement = $rep->idEvenement;
                    $ajoutOk     = true;
                }
                else
                {
                    //***************
                    //**  ENREGISTREMENT
                    //**
                    $idEvenement = $this->getNewIdEvenement();
                    // si c'est un évènement sans évènements parents
                    // c'est qu'il faut créer un evenement vide
                    // ayant pour type d'évènement un groupe d'adresses
                    // sur lequel on va donc lier les adresses
                    
                    if (!isset($this->variablesPost['evenementGroupeAdresse']) || $this->variablesPost['evenementGroupeAdresse']=='')//(empty($tabForm['evenements']['value']))
                    {
                        // creation de l'evenement parent groupe d'adresse
                        $sql = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement,dateCreationEvenement)
                            VALUES (".$idEvenement.", '', '', '', '', ".$idSource.", ".$idUtilisateur.", 0, '".$this->getIdTypeEvenementGroupeAdresse()."', now())";

                        $this->connexionBdd->requete($sql);
                        $tabForm['evenements']['value'][] = $idEvenement;
                        $idSousEvenement =$idEvenement+ 1; // id du sous evenements lié à l'evenement groupe d'adresses
                        
                        // on lie les adresses a l'evenement groupe d'adresse
                        if (!empty($tabForm['adresses']['value']))
                        {
                            $sqlEvenementAdresse = "INSERT INTO _adresseEvenement (idAdresse,idEvenement) VALUES ";
                            foreach ( array_unique($tabForm['adresses']['value']) AS $idAdresse)
                            {
                                $sqlEvenementAdresse .= '('.$idAdresse.', '.$idEvenement.'),';
                            }
                            $sqlEvenementAdresse = pia_substr($sqlEvenementAdresse,0,-1);
                            $this->connexionBdd->requete($sqlEvenementAdresse);
                        }
                    }
                    else
                    {
                        $idSousEvenement = $idEvenement; // si on est dans le cas d'un ajout de sous evenement simple , sans creation de groupe d'adresse, on recupere l'id d'un nouvel element virtuel (getNewIdEVenement)
                    }
                    
                    if (!empty($tabForm['courant']['value']))
                    {
                        $sqlEvenementCourantArchitectural = "INSERT INTO _evenementCourantArchitectural (idCourantArchitectural, idEvenement) VALUES ";
                        foreach ( array_unique($tabForm['courant']['value']) AS $idCourant)
                        {
                            $sqlEvenementCourantArchitectural .= '('.$idCourant.', '.$idSousEvenement.'),';
                        }
                        $sqlEvenementCourantArchitectural = pia_substr( $sqlEvenementCourantArchitectural, 0, -1);

                        $this->connexionBdd->requete($sqlEvenementCourantArchitectural);
                    }
                    
                    
                    
                    if (!empty($tabForm['personnes']['value']))
                    {
                        $sqlEvenementPersonne = "INSERT INTO _evenementPersonne (idPersonne, idEvenement) VALUES ";
                        foreach ( array_unique($tabForm['personnes']['value']) AS $idPersonne)
                        {
                            $sqlEvenementPersonne .= '('.$idPersonne.', '.$idSousEvenement.'),';
                        }
                        $sqlEvenementPersonne = pia_substr( $sqlEvenementPersonne, 0, -1);
                        
                        $this->connexionBdd->requete($sqlEvenementPersonne);
                    }
                    
                     
                    // ajout de l'evenement enfant à l'evenement groupe d'adresse
                    
                    if (!isset($this->variablesPost['evenementGroupeAdresse']) || $this->variablesPost['evenementGroupeAdresse']=='')//(empty($tabForm['evenements']['value']))
                    {
                        // cas liaison entre le nouvel evenement groupe d'adresse et le nouvel evenement fils // ajout d'un evenement
                        $sqlEvenementEvenement = "
                            insert into _evenementEvenement (idEvenement,idEvenementAssocie) 
                            values ('".$idEvenement."','".$idSousEvenement."');
                        ";
                        $this->connexionBdd->requete($sqlEvenementEvenement);
                    }
                    else
                    {
                        // cas liaison entre l'element groupe d'adresse precisé dans $tabForm['evenementGroupeAdresse'] et le nouvel evenement fils // ajout d'un sous evenement
                        $sqlEvenementEvenement = "
                            insert into _evenementEvenement (idEvenement,idEvenementAssocie)
                            values ('".$this->variablesPost['evenementGroupeAdresse']."','".$idSousEvenement."')
                        ";
                        $this->connexionBdd->requete($sqlEvenementEvenement);
                    }
                    
                    // debug
                    if($nbEtages=='')
                        $nbEtages=0;
                        
                    // ajout de l'evenement fils
                    $sqlHistoriqueEvenement = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement,dateCreationEvenement , nbEtages, ISMH, MH,isDateDebutEnviron)
                      VALUES (".$idSousEvenement.", \"".$titre."\", \"".$description."\", '".$dateDebut."', '".$dateFin."', ".$idSource.", ".$idUtilisateur.", '".$idTypeStructure."', '".$idTypeEvenement."',now(),".$nbEtages.",'".$ISMH."','".$MH."','".$isDateDebutEnviron."')";

                    
                    $this->connexionBdd->requete($sqlHistoriqueEvenement);


                    $ajoutOk = true;
                }

                // libération de la table
                $this->connexionBdd->freeLock(array('historiqueEvenement'));
            }
        }
        
        if ($ajoutOk === true)
        {
            if(isset($idSousEvenement))
            {
                $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromIdEvenement($idSousEvenement);
                
                
                
                // d'abord on classe les evenements (les positions précédentes sont conservees)
                $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'idNouvelEvenement'=>$idSousEvenement));
            
            
            
                $mail = new mailObject();
                $adresse = new archiAdresse();
                $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$idSousEvenement));
                
                $resAdresses = $this->connexionBdd->requete($reqAdresses);
                $arrayVilles=array();
                $arrayAdresses = array();
                while($fetchAdresses = mysql_fetch_assoc($resAdresses))
                {
                    $arrayVilles[] = $adresse->getIdVilleFrom($fetchAdresses['idAdresse'],'idAdresse');
                    $arrayAdresses[] = $fetchAdresses['idAdresse'];
                }
                
                $arrayVilles = array_unique($arrayVilles);
                $arrayAdresses = array_unique($arrayAdresses);
                
                if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                    header("Location: ".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson), false, false));
                }
                
                // ************************************************************************************************************************************************
                // envoi d'un mail a l'auteur de l'adresse
                // ************************************************************************************************************************************************
                $utilisateur = new archiUtilisateur();
                $arrayUtilisateurs = $utilisateur->getCreatorsFromAdresseFrom($idSousEvenement,'idEvenement');
                
                $intituleAdresse = $adresse->getIntituleAdresseFrom($idSousEvenement,'idEvenement');
                foreach($arrayUtilisateurs as $indice => $idUtilisateurAdresse)
                {
                    if($idUtilisateurAdresse != $aAuthentification->getIdUtilisateur())
                    {
                        $infosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($idUtilisateurAdresse);
                        if($infosUtilisateur['alerteAdresses']=='1' && $infosUtilisateur['compteActif']=='1' && $infosUtilisateur['idProfil']!='4')
                        {
                            $messageDebut = "Un utilisateur a ajouté un évènement sur une adresse dont vous êtes l'auteur.";
                            $messageDebut.= "Pour vous rendre sur l'évènement : <br>";
                            $messageDebut.="<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$arrayAdresses[0],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))."'>".$intituleAdresse."</a><br>";
                            $messageFin= $this->getMessageDesabonnerAlerteMail();
                            
                            if($utilisateur->isMailEnvoiImmediat($idUtilisateurAdresse))                        
                            {
                                $mail->sendMail($mail->getSiteMail(),$infosUtilisateur['mail'],'Mise a jour d\'un évènement d\'une adresse dont vous êtes l\'auteur.',$messageDebut.$messageFin,true);
                            }
                            else
                            {
                                $utilisateur->ajouteMailEnvoiRegroupes(array('contenu'=>$messageDebut,'idDestinataire'=>$idUtilisateurAdresse,'idTypeMailRegroupement'=>10));
                            }
                        }
                    }
                }
                // ************************************************************************************************************************************************
                
                // ************************************************************************************************************************************************
                // envoi au administrateurs qui sont en mode alerteAdresse
                // ************************************************************************************************************************************************
                
                $message = "";
                $message .= "Un nouvel évènement a été ajouté sur l'adresse suivante : <br>";
                $message.="<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$arrayAdresses[0],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))."'>".$adresse->getIntituleAdresseFrom($idSousEvenement,'idEvenement')."</a>";
                
                $mail->sendMailToAdministrators($mail->getSiteMail(),"Un nouvel évènement a été ajouté - ".$intituleAdresse,$message," AND alerteAdresses='1' ",true);
                $utilisateur->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>10,'criteres'=>" and alerteAdresses='1' "));
                // ************************************************************************************************************************************************
            
                // *************************************************************************************************************************************************************
                // envoi mail aussi au moderateur si ajout sur adresse de ville que celui ci modere
                $u = new archiUtilisateur();
                
                
                $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($arrayVilles[0],array("sqlWhere"=>" AND alerteAdresses='1' "));
                if(count($arrayListeModerateurs)>0)
                {
                    foreach($arrayListeModerateurs as $indice => $idModerateur)
                    {
                        if($aAuthentification->getIdUtilisateur()!=$idModerateur)
                        {
                            if($u->isMailEnvoiImmediat($idModerateur))
                            {
                                $mailModerateur = $u->getMailUtilisateur($idModerateur);
                                $mail->sendMail($mail->getSiteMail(),$mailModerateur,"Un nouvel évènement a été ajouté - ".$intituleAdresse,$message,true);
                            }
                            else
                            {
                                $u->ajouteMailEnvoiRegroupes(array('contenu'=>$message,'idDestinataire'=>$idModerateur,'idTypeMailRegroupement'=>10));
                            }
                        }
                    }
                }

                // *************************************************************************************************************************************************************
            
                $retourArray = $this->afficher($idSousEvenement); // afficher le sousEvenement revient a afficher l'evenement parent de type groupe d'adresse
                $html .= $retourArray['html'];
                $adresse = new archiAdresse();
                $html.=$adresse->getListeCommentaires($idSousEvenement);
                $html.=$adresse->getFormulaireCommentaires($idSousEvenement,$adresse->getCommentairesFields());
            }
        }
        else
        {
            $html .= $this->afficheFormulaire($tabForm);
        }

        return $html;
    }
    
    public function recupTableauTravail($idEvenement)
    {
        $formulaire = new formGenerator();
        if( $formulaire->estChiffre($idEvenement))
        {
            $id = $idEvenement;
        }
        else
        {
            $id = 0;
        }
        //************************************
        //***     Création du tableau de valeurs
        //************************************

        /*$sqlEvenement = "SELECT hE.titre, hE.description, DATE_FORMAT(hE.dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(hE.dateFin, '%d/%m/%Y') AS dateFin, hE.idTypeStructure, hE.idTypeEvenement, hE.idSource, _eCA.idCourantArchitectural, _eP.idPersonne, _aE.idAdresse, 
            IF( _eE.idEvenementAssocie != hE.idEvenement, _eE.idEvenementAssocie, _eE.idEvenement) AS idEvenementAssocie
            FROM historiqueEvenement hE2, historiqueEvenement hE 
            LEFT JOIN _evenementPersonne _eP USING (idEvenement)  
            LEFT JOIN _evenementCourantArchitectural _eCA USING (idEvenement) 
            LEFT JOIN _adresseEvenement _aE USING (idEvenement) 
            LEFT JOIN _evenementEvenement _eE ON _eE.idEvenement=hE.idEvenement OR _eE.idEvenementAssocie=hE.idEvenement
            WHERE  hE.idEvenement=".$id." AND hE.idEvenement=hE2.idEvenement
            GROUP BY _eP.idPersonne, _aE.idAdresse, idEvenementAssocie, hE.idEvenement, hE.idHistoriqueEvenement HAVING hE.idHistoriqueEvenement=MAX(hE2.idHistoriqueEvenement) 
            ORDER BY hE.idHistoriqueEvenement DESC";
        */
        
    
        
        $sqlPersonne = "SELECT idPersonne FROM _evenementPersonne WHERE idEvenement = '".$id."'";
        $resPersonne = $this->connexionBdd->requete($sqlPersonne);
        $tabIdPersonne=array();
        while($fetchPersonne = mysql_fetch_assoc($resPersonne))
        {
            $tabIdPersonne[]=$fetchPersonne['idPersonne'];
        }
        
        $sqlCourant = "SELECT idCourantArchitectural FROM _evenementCourantArchitectural WHERE idEvenement='".$id."'";
        $resCourant = $this->connexionBdd->requete($sqlCourant);
        $tabIdCourant=array();
        while($fetchCourant=mysql_fetch_assoc($resCourant))
        {
            $tabIdCourant[] = $fetchCourant['idCourantArchitectural'];
        }
        
        $sqlAdresses = "SELECT idAdresse FROM _adresseEvenement WHERE idEvenement ='".$id."'";
        $resAdresses = $this->connexionBdd->requete($sqlAdresses);
        $tabIdAdresses=array();
        while($fetchAdresses=mysql_fetch_assoc($resAdresses))
        {
            $tabIdAdresses[] = $fetchAdresses['idAdresse'];
        }
        

        
        $sqlEvenementAssocies = "SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenementAssocie='".$id."'";
        $resEvenementAssocies = $this->connexionBdd->requete($sqlEvenementAssocies);
        $tabIdEvenement=array();
        while($fetchEvenement=mysql_fetch_assoc($resEvenementAssocies))
        {
            $tabIdEvenement[]=$fetchEvenement['idEvenementAssocie'];
        }
        
        $sqlEvenement = "SELECT hE.titre, hE.description, DATE_FORMAT(hE.dateDebut, '%d/%m/%Y') AS dateDebut, DATE_FORMAT(hE.dateFin, '%d/%m/%Y') AS dateFin, hE.idTypeStructure, hE.idTypeEvenement, hE.idSource, hE.nbEtages, hE.ISMH, hE.MH, hE.isDateDebutEnviron,hE.numeroArchive as numeroArchive
        FROM historiqueEvenement hE2, historiqueEvenement hE 
        WHERE  hE.idEvenement=".$id." AND hE2.idEvenement=hE.idEvenement
        GROUP BY hE.idEvenement,hE.idHIstoriqueEvenement
        HAVING hE.idHistoriqueEvenement=max(hE2.idHistoriqueEvenement)
        ORDER BY hE.idHistoriqueEvenement DESC";
        
        
        if ( $rep = $this->connexionBdd->requete($sqlEvenement))
        {
            $ok = false;
            while ($res = mysql_fetch_object($rep))
            {
                $info = $res;
                $ok = true;
            }

            if($ok == false)
            {
                $info->titre = "";
                $info->description = "";
                $info->dateDebut = "";
                $info->idSource = "";
                $info->idTypeStructure = "";
                $info->idTypeEvenement = "";
                $info->nbEtages = "";
                $info->ISMH = "";
                $info->MH = "";
                $info->numeroArchive = "";
                $info->isDateDebutEnviron = "";
                $info->dateFin = "";
            }
            
            $tabForm=array(
                'titre'      => array('default'=> ''       , 'value' => $info->titre,          'required'=>false,'error'=>'','type'=>'text'),
                'description'    => array('default'=> ''       , 'value' => $info->description,    'required'=>false,'error'=>'','type'=>'text'),
                'dateDebut'  => array('default'=> ''       , 'value' => $info->dateDebut,      'required'=>true,'error'=>'','type'=>'date'),
                'isDateDebutEnviron' => array('default'=>'','value'=>$info->isDateDebutEnviron,'required'=>false,'error'=>'','type'=>'checkbox'),
                'dateFin'    => array('default'=> ''       , 'value' => $info->dateFin,        'required'=>false,'error'=>'','type'=>'date'),
                'source'     => array('default'=> 'aucune' , 'value' => $info->idSource,       'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                                                array('table'=> 'source', 'primaryKey'=> 'idSource')),
                'typeStructure'  => array('default'=> 'aucune ', 'value' => $info->idTypeStructure,'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                                                array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
                'typeEvenement'  => array('default'=> 'aucun'  , 'value' => $info->idTypeEvenement,'required'=>true ,'error'=>'','type'=>'numeric', 'checkExist'=>
                                                                array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
                'personnes'  => array('default'=> 'aucune' , 'value' => array_unique($tabIdPersonne), 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
                                                                array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
                'courant' => array('default'=> 'aucun'  , 'value' => array_unique($tabIdCourant),  'required'=>false ,'error'=>'','type'=>'multiple','checkExist'=>
                                array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
                'adresses'   => array('default'=>'', 'value'=>array_unique($tabIdAdresses), 'required'=>false, 'error'=>'' , 'type'=>'multiple', 'checkExist'=>
                                array('table'=>'historiqueAdresse','primaryKey'=>'idAdresse')),
                'evenements'     => array('default'=>'', 'value'=>array_unique($tabIdEvenement), 'required'=>false, 'error'=>'' , 'type'=>'multiple', 'checkExist'=>
                                array('table'=>'historiqueEvenement','primaryKey'=>'idEvenement')),
                'nbEtages'=>array('default'=>'','value'=>$info->nbEtages,'required'=>false,'error'=>'','type'=>'text'),
                'ISMH'=>array('default'=>'','value'=>$info->ISMH,'required'=>false,'error'=>'','type'=>'checkbox'),
                'MH'=>array('default'=>'','value'=>$info->MH,'required'=>false,'error'=>'','type'=>'checkbox'),
                'numeroArchive'=>array('default'=>'' , 'value' => $info->numeroArchive,'required'=>false,'error'=>'','type'=>'text')
                );
        }
        
        return $tabForm;
    }
    
    // **************************************************************************************************************************************************************************
    public function modifier($id)
    {
        $aAuthentification = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $html = "<script src='js/confirm.js'></script>";
        $ajoutOk = false;
        $formulaire = new formGenerator();
        $mail = new mailObject();
        // *************************************************************************************************************************************
        // recuperation de l'idHistoriqueEvenement Courant pour le renvoyer en tant que idHistoriquePrecedent a la fin de la modification ( visualisation de l'administrateur de l'ancien evenement)
        // *************************************************************************************************************************************
        $reqIdHistoriqueEvenementAvantModif = "
                SELECT he1.idHistoriqueEvenement as idHistoriqueEvenementAvantModif
                FROM historiqueEvenement he2,historiqueEvenement he1
                WHERE he2.idEvenement = he1.idEvenement
                AND he1.idEvenement = '".$id."'
                GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                HAVING he1.idHistoriqueEvenement=max(he2.idHistoriqueEvenement)
        ";
        
        $resIdHistoriqueEvenementAvantModif = $this->connexionBdd->requete($reqIdHistoriqueEvenementAvantModif);
        $fetchIdHistoriqueEvenementAvantModif = mysql_fetch_assoc($resIdHistoriqueEvenementAvantModif);
        $idHistoriqueEvenementAvantModif = $fetchIdHistoriqueEvenementAvantModif['idHistoriqueEvenementAvantModif'];
        // *************************************************************************************************************************************
        
        
        $idHistoriqueEvenementNouveau = 0; // cette variable va contenir le nouvel idHistoriqueEvenement une fois celui ci créé
        
        
        $idProfilUtilisateur = $u->getIdProfilFromUtilisateur($aAuthentification->getIdUtilisateur());
        
        if( !$formulaire->estChiffre($id))
        {
            $this->erreurs->ajouter('Modification d\'un évènement : l\'identifiant est incorrect !');
        }
        
        if ($aAuthentification->estConnecte() != true)
        {
            $this->erreurs->ajouter('Modification d\'un évènement : vous n\'etes pas connecté !');
        }
        else if (isset($this->variablesPost['evenementSimple'])) // evenementSimple correspond au nom du bouton submit du formulaire, on enregistre les modifications
        {
            // modification de l'evnement
            $afficheFormulaire = true;

            $tabForm=$this->getEvenementFields('nouveauDossier'); // memes champs qu'un ajout
            $erreur = $formulaire->getArrayFromPost($tabForm);

            if (count($erreur) == 0)
            {
                $this->connexionBdd->getLock(array('historiqueEvenement'));
                $ajoutOk = true;
                $nouvelEnregistrement = false;
                $nouvelleLiaison      = false;
                
                $tabAncien= $this->recupTableauTravail($id);//$tabForm['idEvenement']['value']
                
                
                // ****************************************************************************************
                // ici on va chercher s'il existe des differences entre l'enregistrement de l'evenement précédent et les nouvelles données,
                // s'il n'y a pas de différences , on ne modifie rien
                // s'il y a des différences , on ajoute un nouvel historique a l'evenement existant
                // ****************************************************************************************
                $tabDesChampEntrainantNouveauEvenement = array('titre', 'description', 'dateDebut', 'dateFin', 'source', 'typeStructure', 'typeEvenement','nbEtages','ISMH','MH','isDateDebutEnviron','numeroArchive');

                foreach ($tabDesChampEntrainantNouveauEvenement AS $champ)
                {
                    if ($tabForm[$champ]['value'] != $tabAncien[$champ]['value'])
                    {
                        $nouvelEnregistrement = true;
                    }
                }

                // ****************************************************************************************
                // ici on cherche s'il y a des différences au niveau des tables qui sont liées
                // ****************************************************************************************
                $tabDesChampDeLiaison = array('courant', 'personnes');//'adresses', 'evenements'
                foreach ($tabDesChampDeLiaison AS $champ)
                {
                    if (is_array($tabForm[$champ]['value']))
                    {
                        $taille_intersection = count(array_intersect( $tabForm[$champ]['value'], $tabAncien[$champ]['value']));
                        $taille_ancien = count($tabAncien[$champ]['value']);
                        $taille_nouveau = count($tabForm[$champ]['value']);
                        if ($taille_intersection != $taille_ancien OR $taille_intersection != $taille_nouveau)
                        {
                            $isModifLiaison[$champ] = 1;
                            $nouvelleLiaison = true;
                        }
                    }
                }

                if ($nouvelEnregistrement == true)
                {
                    //********************************************
                    //**  AJOUT d'un nouvel historique a l'evenement existant **
                    //********************************************
                    
                    $titre       = mysql_escape_string( $tabForm['titre']['value'] );
                    $description = mysql_escape_string( $tabForm['description']['value'] );
                    $dateDebut   = $this->date->toBdd($this->date->convertYears($tabForm['dateDebut']['value']));
                    if($idProfilUtilisateur == 3 || $idProfilUtilisateur == 2 || $idProfilUtilisateur == 1)
                    {
                        //$dateFin = $this->date->toBdd($tabAncien['dateFin']['value']);
                        $idSource = $tabAncien['source']['value'];
                        $nbEtages = $tabAncien['nbEtages']['value'];
                        
                    }
                    else
                    {
                        //$dateFin     = $this->date->toBdd($this->date->convertYears($tabForm['dateFin']['value']));
                        $idSource    = $tabForm['source']['value'];
                        $nbEtages       = $tabForm['nbEtages']['value'];
                    }
                    
                    
                    $dateFin     = $this->date->toBdd($this->date->convertYears($tabForm['dateFin']['value']));
                    $idUtilisateur   = $aAuthentification->getIdUtilisateur();
                    $idTypeStructure = $tabForm['typeStructure']['value'];
                    $idTypeEvenement = $tabForm['typeEvenement']['value'];
                    
                    $ISMH           = $tabForm['ISMH']['value'];
                    $MH             = $tabForm['MH']['value'];
                    $isDateDebutEnviron = $tabForm['isDateDebutEnviron']['value'];
                    $numeroArchive  = $tabForm['numeroArchive']['value'];
                    
                    // debug
                    if($nbEtages=='')
                        $nbEtages = 0;
                    
                    
                    $idEvenement = $id;//$tabForm['idEvenement']['value'];
                    $sqlHistoriqueEvenement = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement, dateCreationEvenement,nbEtages, ISMH , MH, isDateDebutEnviron,numeroArchive)
                      VALUES (".$idEvenement.", \"".$titre."\", \"".$description."\", '".$dateDebut."', '".$dateFin."', ".$idSource.", ".$idUtilisateur.", '".$idTypeStructure."', '".$idTypeEvenement."', NOW(),'".$nbEtages."','".$ISMH."','".$MH."','".$isDateDebutEnviron."',\"".$numeroArchive."\")";
                    
                    $this->connexionBdd->requete($sqlHistoriqueEvenement);
                    
                    $idHistoriqueEvenementNouveau = mysql_insert_id();
                    
                    
                    // recuperation de l'idEvenementGroupeAdresse
                    $idEvGA = $this->getIdEvenementGroupeAdresseFromIdEvenement($idEvenement);
                    // mise a jour des positions (la fonction verifiera d'abord si la date a ete changé avant de mettre a jour , sinon c'est pas la peine
                    $this->majPositionsEvenements(array("idEvenementGroupeAdresse"=>$idEvGA ,"idEvenementModifie"=>$idEvenement));
                }

                /*if ($nouvelleLiaison == true)
                {*/
                    $idEvenement = $id;//$tabForm['idEvenement']['value'];
                    
                    // ********************************************************************************************************
                    // GESTION DES COURANTS ARCHITECTURAUX
                    if(is_array($tabForm['courant']['value']) && count($tabForm['courant']['value'])>0)
                    {
                        $sqlNettoyeCourantArchitectural   = 'DELETE FROM _evenementCourantArchitectural WHERE idEvenement='.$idEvenement;
                        $sqlEvenementCourantArchitectural = 'INSERT INTO _evenementCourantArchitectural (idCourantArchitectural, idEvenement) VALUES ';
                        foreach ( array_unique($tabForm['courant']['value']) AS $idCourant)
                        {
                            $sqlEvenementCourantArchitectural .= '('.$idCourant.', '.$idEvenement.'),';
                        }
                        $sqlEvenementCourantArchitectural = pia_substr( $sqlEvenementCourantArchitectural, 0, -1);
                        
                        $this->connexionBdd->requete($sqlNettoyeCourantArchitectural);
                        $this->connexionBdd->requete($sqlEvenementCourantArchitectural);
                    }
                    elseif(isset($tabForm['courant']['value']) && !is_array($tabForm['courant']['value']))
                    {
                        $sqlNettoyeCourantArchitectural   = 'DELETE FROM _evenementCourantArchitectural WHERE idEvenement='.$idEvenement;
                        $this->connexionBdd->requete($sqlNettoyeCourantArchitectural);
                    }
                    // ********************************************************************************************************

                    
                    
                    
                    // ********************************************************************************************************
                    // GESTION DES PERSONNES
                    if($idProfilUtilisateur == 2 || $idProfilUtilisateur == 1)
                    {
                        // la modif des personnes est authorisee seulement pour les moderateurs et les admins
                    }
                    else
                    {
                    
                        if(is_array($tabForm['personnes']['value']) && count($tabForm['personnes']['value'])>0)
                        {
                            $sqlNettoyePersonne = 'DELETE FROM _evenementPersonne WHERE idEvenement='.$idEvenement;
                            $this->connexionBdd->requete($sqlNettoyePersonne);
                            
                            if (isset($tabForm['personnes']['value']) && count($tabForm['personnes']['value'])>0)
                            {
                                $sqlEvenementPersonne = 'INSERT INTO _evenementPersonne (idPersonne, idEvenement) VALUES ';
                                foreach ( array_unique($tabForm['personnes']['value']) AS $idPersonne)
                                {
                                    $sqlEvenementPersonne .= '('.$idPersonne.', '.$idEvenement.'),';
                                }
                                $sqlEvenementPersonne = pia_substr( $sqlEvenementPersonne, 0, -1);
                                
                                $this->connexionBdd->requete($sqlEvenementPersonne);
                            }
                        }
                        else
                        {   // de toute facon on nettoie les valeurs si le champ est vide
                            $sqlNettoyePersonne = 'DELETE FROM _evenementPersonne WHERE idEvenement='.$idEvenement;
                            $this->connexionBdd->requete($sqlNettoyePersonne);
                        }
                    }
                    // ********************************************************************************************************
                    
                    /*
                    if (isset($isModifLiaison['adresses']))
                    {
                        $sqlNettoyeAdresse = 'DELETE FROM _adresseEvenement WHERE idEvenement='.$idEvenement;
                        $this->connexionBdd->requete($sqlNettoyeAdresse);
                        
                        if (!empty($tabForm['adresses']['value']))
                        {
                            $sqlAdresseEvenement = 'INSERT INTO _adresseEvenement (idAdresse, idEvenement) VALUES ';
                            foreach ( array_unique($tabForm['adresses']['value']) AS $idAdresse)
                            {
                                $sqlAdresseEvenement .= '('.$idAdresse.', '.$idEvenement.'),';
                            }
                            $sqlAdresseEvenement = pia_substr( $sqlAdresseEvenement, 0, -1);
                            $this->connexionBdd->requete($sqlAdresseEvenement);
                        }
                    }
                    
                    if (isset($isModifLiaison['evenements']))
                    {
                        $sqlNettoyeEvenement = 'DELETE FROM _evenementEvenement WHERE idEvenement='.$idEvenement;
                        $this->connexionBdd->requete($sqlNettoyeEvenement);
                        
                        if (!empty($tabForm['evenements']['value']))
                        {
                            $sqlEvenementEvenement = 'INSERT INTO _evenementEvenement (idEvenementAssocie, idEvenement) VALUES ';
                            foreach ( array_unique($tabForm['evenements']['value']) AS $idEvenementParent)
                            {
                                $sqlEvenementEvenement .= '('.$idEvenement.', '.$idEvenementParent.'),';
                            }
                            $sqlEvenementEvenement = pia_substr( $sqlEvenementEvenement, 0, -1);
                            $this->connexionBdd->requete($sqlEvenementEvenement);
                        }
                    }*/
                //}
                
                
                $this->connexionBdd->freeLock(array('historiqueEvenement'));
            }
        }
        elseif(isset($this->variablesPost['evenementGroupeAdresse']) && $this->variablesPost['evenementGroupeAdresse']!='') // on est connecté
        {
            
            $this->connexionBdd->getLock(array('_adresseEvenement'));
            
            // cas d'un groupe d'adresse ( le formulaire d'ou l'on provient ne comporte que des listes d'adresses
            // on enregistre les adresses liees
            $idEvenementGroupeAdresse = $this->variablesPost['evenementGroupeAdresse'];
            
            // ajout des nouvelles adresses liées
            if(isset($this->variablesPost['adresses']))
            {
                // on supprime d'abord les anciennes adresses liées
                $resSupprimer = $this->connexionBdd->requete("delete from _adresseEvenement where idEvenement = '".$idEvenementGroupeAdresse."'");

                // et on lie les nouvelles adresses
                foreach($this->variablesPost['adresses'] as $indice => $idAdresse)
                {
                    $resInsert = $this->connexionBdd->requete("insert into _adresseEvenement (idEvenement,idAdresse) values ('".$idEvenementGroupeAdresse."','".$idAdresse."') ");
                }
            }
            $ajoutOk = true;
            
            $this->connexionBdd->freeLock(array('_adresseEvenement'));
        }
        
        
        
        if ($ajoutOk === true)
        {
            
            // ************************************************************************************************************************************************
            // envoi d'un mail a l'auteur de l'adresse
            // ************************************************************************************************************************************************
            $utilisateur = new archiUtilisateur();
            $arrayUtilisateurs = $utilisateur->getCreatorsFromAdresseFrom($idHistoriqueEvenementNouveau,'idHistoriqueEvenement');
            $adresse = new archiAdresse();
            $intituleAdresse = $adresse->getIntituleAdresseFrom($id,'idEvenement');
            
            $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromIdEvenement($id);
            if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                header("Location: ".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson), false, false));
            }
            
            $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$id));
            $resAdresses = $this->connexionBdd->requete($reqAdresses);
            $fetchAdresses = mysql_fetch_assoc($resAdresses);
            
            
            
            foreach($arrayUtilisateurs as $indice => $idUtilisateurAdresse)
            {
                if($idUtilisateurAdresse != $aAuthentification->getIdUtilisateur())
                {
                    $infosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($idUtilisateurAdresse);
                    if($infosUtilisateur['alerteAdresses']=='1' && $infosUtilisateur['idProfil']!='4' && $infosUtilisateur['compteActif']=='1')
                    {
                        $messageDebut = "Un utilisateur a modifié une adresse dont vous êtes l'auteur.";
                        $messageDebut.= "Pour vous rendre sur l'évènement : <a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$fetchAdresses['idAdresse'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))."'>".$intituleAdresse."</a><br>";
                        $messageFin= $this->getMessageDesabonnerAlerteMail();
                        if($utilisateur->isMailEnvoiImmediat($idUtilisateurAdresse))
                        {
                            $mail->sendMail($mail->getSiteMail(),$infosUtilisateur['mail'],'Mise a jour d\'un évènement d\'une adresse dont vous êtes l\'auteur. - '.$intituleAdresse,$messageDebut.$messageFin,true);
                        }
                        else
                        {
                            $utilisateur->ajouteMailEnvoiRegroupes(array('contenu'=>$messageDebut,'idDestinataire'=>$idUtilisateurAdresse,'idTypeMailRegroupement'=>11));
                        }
                    }
                }
            }
            // ************************************************************************************************************************************************
            
            // 
        
        
            //$message="Un évènement a été édité. :<br> <a href='".$this->creerUrl('','evenement',array('idEvenement'=>$id))."'>Lien vers l'évènement</a><br>";
            $message="Un évènement a été édité.<br> Il concerne l'adresse : $intituleAdresse<br> <a href='".$this->creerUrl('','comparaisonEvenement',array('idHistoriqueEvenementNouveau'=>$idHistoriqueEvenementNouveau,'idHistoriqueEvenementAncien'=>$idHistoriqueEvenementAvantModif))."'>Comparer les deux versions</a></br>";
            
            // recuperation des infos sur l'utilisateur qui fais la modif
            $utilisateur = new archiUtilisateur();
            $arrayInfosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
            
            $message .="<br>".$arrayInfosUtilisateur['nom']." - ".$arrayInfosUtilisateur['prenom']." - ".$arrayInfosUtilisateur['mail']."<br>";

            
            $mail->sendMailToAdministrators($mail->getSiteMail(),"Edition d'un évènement - ".$intituleAdresse,$message," AND alerteAdresses='1' ",true);
            $utilisateur->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>11,'criteres'=>" and alerteAdresses='1' "));
            
            
            // affichage du detail de l'adresse
            $reqAdresse = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$id));
            
            $resAdresse = $this->connexionBdd->requete($reqAdresse);
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            $html = $adresse->afficherDetail('',$idEvenementGroupeAdresse);
            /*$arrayAffichage = $this->afficher($id);
            $html .= $arrayAffichage['html'];
            */
            
            
            // *************************************************************************************************************************************************************
            // envoi mail aussi au moderateur si ajout sur adresse de ville que celui ci modere
            $u = new archiUtilisateur();
            
            if($idHistoriqueEvenementNouveau!=0)
            {
                $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$id));
                
                $resAdresses = $this->connexionBdd->requete($reqAdresses);
                $arrayVilles=array();
                while($fetchAdresses = mysql_fetch_assoc($resAdresses))
                {
                    $arrayVilles[] = $adresse->getIdVilleFrom($fetchAdresses['idAdresse'],'idAdresse');
                }
                
                $arrayVilles = array_unique($arrayVilles);
                
                
                $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($arrayVilles[0],array("sqlWhere"=>" AND alerteAdresses='1' "));
                if(count($arrayListeModerateurs)>0)
                {
                    foreach($arrayListeModerateurs as $indice => $idModerateur)
                    {
                        if($idModerateur!=$this->session->getFromSession('utilisateurConnecte'.$this->idSite))
                        {
                            if($u->isMailEnvoiImmediat($idModerateur))
                            {
                                $mailModerateur = $u->getMailUtilisateur($idModerateur);
                                $mail->sendMail($mail->getSiteMail(),$mailModerateur,"Edition d'un évènement - ".$intituleAdresse,$message,true);
                            }
                            else
                            {
                                $u->ajouteMailEnvoiRegroupes(array('contenu'=>$message,'idDestinataire'=>$idModerateur,'idTypeMailRegroupement'=>11));
                            }
                        }
                    }
                }
            }
            // *************************************************************************************************************************************************************
                        
            
            
            // on libere l'element
            $this->connexionBdd->freeLock(array('evenement'.$id),array('idUtilisateur'=>$aAuthentification->getIdUtilisateur()));
        }
        else
        {
            // est ce que l'element est deja en cours d'edition
            if($this->connexionBdd->isLocked('evenement'.$id,$aAuthentification->getIdUtilisateur()))
            {
                $this->erreurs->ajouter(_("Cet évènement est déjà en cours d'édition. Veuillez retenter dans quelques minutes."));
                //echo $this->erreurs->afficher();
            }
            else
            {
                // reservation de l'element
                $this->connexionBdd->getLock(array('evenement'.$id),array('minutes'=>10,'idUtilisateur'=>$aAuthentification->getIdUtilisateur()));
                
                // affichage du formulaire de modifications
                
                // pour eviter les problemes d'affichage d'erreur si l'utilisateur utilise le bouton de retour du navigateur quand l'evenement a été supprimé , on va afficher un message si l'evenement n'existe pas
                $reqVerif = "SELECT 0 FROM historiqueEvenement WHERE idEvenement = '$id'";
                $resVerif = $this->connexionBdd->requete($reqVerif);
                
                if(mysql_num_rows($resVerif)>0)
                {
                    $tabForm = $this->recupTableauTravail($id);
                    $html .= $this->afficheFormulaire($tabForm, $id);
                }
                else
                {
                    $html .= "L'évènement n'existe pas.";
                }
            }
        }

        return $html;
    }
    // *************************************************************************************************************************************
    public function supprimer($idEvenement, $idHistoriqueEvenement='')
    {
        $html = '';
        $idEvenementGroupeAdresse = 0;
        if($idHistoriqueEvenement !='')
        {

            $adresse = new archiAdresse();
            $reqRecupIdEvenementGroupeAdresse = "
                            SELECT DISTINCT ee.idEvenement as idEvenementGroupeAdresse
                            FROM historiqueEvenement he
                            LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he.idEvenement
                            WHERE idHistoriqueEvenement ='".$idHistoriqueEvenement."'";
            $resRecupIdEvenementGroupeAdresse = $this->connexionBdd->requete($reqRecupIdEvenementGroupeAdresse);
            $fetchRecupIdEvenementGroupeAdresse = mysql_fetch_assoc($resRecupIdEvenementGroupeAdresse);
            $idEvenementGroupeAdresse = $fetchRecupIdEvenementGroupeAdresse['idEvenementGroupeAdresse'];            
            
        
            // recup d'idAdresse pour l'affichage du detail de l'adresse a la fin de la suppression
            $reqSuppHistorique = "DELETE FROM historiqueEvenement WHERE idHistoriqueEvenement = '".$idHistoriqueEvenement."'";
            $resSupprHistorique = $this->connexionBdd->requete($reqSuppHistorique);

            
            // maj des position au cas ou , meme si dans ce cas en principe ce n'est pas la peine (a moins que ce ne soit le dernier historique du groupe d'adresse
            $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'refreshAfterDelete'=>true));
            
            
            $idAdresse = $adresse->getIdAdresseFromIdEvenementGroupeAdresse($idEvenementGroupeAdresse);
             
        }
        else
        {

            $image = new archiImage();
            $personne = new archiPersonne();
            $courant = new archiCourantArchitectural();
            $adresse = new archiAdresse();

            // suppression d'un evenement
            // verification que l'evenement est le seul ou pas lié au groupe d'adresse
            $idEvenementGroupeAdresse = $this->getParent($idEvenement);
            
            // on verifie que l'evenement n'a qu'un seul parent
            $reqVerifParent = "
                                SELECT idEvenement
                                FROM _evenementEvenement
                                WHERE idEvenementAssocie = '".$idEvenement."'
            ";
            
            $resVerifParent = $this->connexionBdd->requete($reqVerifParent);
            if(mysql_num_rows($resVerifParent)==1)
            {

                // on verifie que l'evenement parent est bien un groupe d'adresse ( pour le jour ou on ajoutera les sous sous evenements)
                if($this->isEvenementGroupeAdresse($idEvenementGroupeAdresse))
                {
                    // comptage du nombre d'elements du groupe d'adresse
                    $req = "select idEvenementAssocie from _evenementEvenement where idEvenement = '".$idEvenementGroupeAdresse."'";
                    $res = $this->connexionBdd->requete($req);
                    
                    if(mysql_num_rows($res)==1)
                    {
                        // un seul element a supprimer , on supprime aussi le groupe d'adresse dans un deuxieme temps , voir plus bas, et donc les commentaires lies a ce groupe d'adresse

                        // evenement images
                        $image->deleteImagesFromIdEvenement($idEvenement);
                        // personnes
                        $personne->deleteLiaisonsPersonneFromIdEvenement($idEvenement);
                        // courant architectural
                        $courant->deleteLiaisonsCourantFromIdEvenement($idEvenement);
                        // commentaires
                        //$adresse->deleteCommentairesFromIdEvenement($idEvenementGroupeAdresse);
                        // adresses liees à l'evenement , on supprime la liaison
                        $this->deleteLiaisonsAdressesLieesSurEvenement($idEvenement);
                        
                        // quand il y aura des sous sous evenements , il faudra aussi les supprimer
                        // ...
                        $reqDeleteEvenement = "DELETE FROM historiqueEvenement WHERE idEvenement = '".$idEvenement."'";
                        $resDeleteEvenement = $this->connexionBdd->requete($reqDeleteEvenement );
                        $reqDeleteEvenementEvenement = "DELETE FROM _evenementEvenement WHERE idEvenementAssocie = '".$idEvenement."'";
                        $resDeleteEvenementEvenement = $this->connexionBdd->requete($reqDeleteEvenementEvenement);
                        //$reqDeleteAdresseEvenement = "DELETE FROM _adresseEvenement WHERE idEvenement='".."'";
                        
                        // maj des position
                        $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'refreshAfterDelete'=>true)); // dans ce cas la fonction va se charger de supprimer les positions des evenements du groupe d'adresse supprimé
                    }
                    elseif(mysql_num_rows($res)>1)
                    {
                        $image = new archiImage();
                        $personne = new archiPersonne();
                        $courant = new archiCourantArchitectural();
                        // plusieurs elements , on supprime simplement l'evenement passé en parametre de la fonction
                        
                        // evenement images
                        $image->deleteImagesFromIdEvenement($idEvenement);
                        // personnes
                        $personne->deleteLiaisonsPersonneFromIdEvenement($idEvenement);             
                        // courant architectural
                        $courant->deleteLiaisonsCourantFromIdEvenement($idEvenement);
                        // quand il y aura des sous sous evenements , il faudra aussi les supprimer
                        // ...
                        // adresses liees à l'evenement , on supprime la liaison
                        $this->deleteLiaisonsAdressesLieesSurEvenement($idEvenement);
                        
                        $reqDeleteEvenementEvenement = "DELETE FROM _evenementEvenement WHERE idEvenementAssocie = '".$idEvenement."'";
                        $resDeleteEvenementEvenement = $this->connexionBdd->requete($reqDeleteEvenementEvenement);
                        //supression de l'evenement
                        $reqDeleteEvenement = "DELETE FROM historiqueEvenement WHERE idEvenement = '".$idEvenement."'";
                        $resDeleteEvenement = $this->connexionBdd->requete($reqDeleteEvenement );
                        
                        $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'refreshAfterDelete'=>true)); // dans ce cas la fonction va mettre a jour les positions des evenements pour que ceux ci se suivent, et supprimera la liaison vers l'evenement qui n'existe plus
                    }
                    elseif(mysql_num_rows($res)==0)
                    {
                        // probleme , il n'y a pas d'evenements fils correspondant a l'idEvenementGroupeAdresse
                        echo "l'évènement n'existe plus. Veuillez contacter l'administrateur.<br>";
                    }
                    
                }
                else
                {
                    echo "L'évènement parent n'est pas de type groupe d'adresse : veuillez contactez l'administrateur.<br>";
                }
            }
            elseif(mysql_num_rows($resVerifParent)>1)
            {

                echo "Erreur : l'evenement appartient a plusieurs groupes d'adresses : contactez l'administrateur<br>";
            }
            else
            {
                echo "Erreur : l'evenement n'appartient a aucun groupe d'adresse : contactez l'administrateur<br>";
            }
            
            
            
            // pour l'affichage de retour : on recupere l'adresse
            $idAdresse = $adresse->getIdAdresseFromIdEvenementGroupeAdresse($idEvenementGroupeAdresse); // attention si l'evenement groupe adresse a ete supprimé precedemment
            
            
            $nbEvenementLiesAuGroupeAdresse = $this->getNbEvenementsFromGroupeAdresse($idEvenementGroupeAdresse);

            if($nbEvenementLiesAuGroupeAdresse==0 && $idEvenementGroupeAdresse!='' && $idEvenementGroupeAdresse !='0')
            {

                // s'il n'y a aucun evenement lié au groupe d'adresses , on peut supprimer le groupe d'adresse et les liaisons vers celui ci
                $reqDeleteGroupeAdresseHistorique = "DELETE FROM historiqueEvenement WHERE idEvenement = '".$idEvenementGroupeAdresse."'";
                $resDeleteGroupeAdresseHistorique = $this->connexionBdd->requete($reqDeleteGroupeAdresseHistorique);
                
                $reqDeleteAdresseGroupeAdresse = "DELETE FROM _adresseEvenement WHERE idEvenement = '".$idEvenementGroupeAdresse."'";
                $resDeleteAdresseGroupeAdresse = $this->connexionBdd->requete($reqDeleteAdresseGroupeAdresse);
                
                // on supprime aussi les liaisons vers le groupe d'adresse dans les adresses liés
                $reqDeleteLiaisonsAdressesLiees = "DELETE FROM _evenementAdresseLiee WHERE idEvenementGroupeAdresse='".$idEvenementGroupeAdresse."'";
                $resDeleteAdresseGroupeAdresse = $this->connexionBdd->requete($reqDeleteLiaisonsAdressesLiees);
                
                // supprimons aussi les liaisons vueSur et prisDepuis sur le groupe d'adresse
                $reqDeleteVueSurPrisDepuis = "DELETE FROM _adresseImage WHERE idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."' AND (vueSur='1' OR prisDepuis='1')";
                $resDeleteVueSurPrisDepuis = $this->connexionBdd->requete($reqDeleteVueSurPrisDepuis);
                
                // suppression des liaisons entre evenement et evenement groupe d'adresse
                $reqDeleteEvenementGAEvenementAssocie = "DELETE FROM _evenementEvenement WHERE idEvenement='".$idEvenementGroupeAdresse."'";
                $resDeleteEvenementGAEvenementAssocie = $this->connexionBdd->requete($reqDeleteEvenementGAEvenementAssocie);
            }
        }
        if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
            header("Location: ".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson), false, false));
        }
        $retourArray = $this->afficher($idEvenementGroupeAdresse);
        $html =  $retourArray['html'];
        $adresse = new archiAdresse();
        $html.=$adresse->getListeCommentaires($idEvenementGroupeAdresse);
        $html.=$adresse->getFormulaireCommentaires($idEvenementGroupeAdresse,$adresse->getCommentairesFields());
        echo $html;
    }
    
    
    // suppression des liaisons vers des adresses de l'evenement passé en parametre
    public function deleteLiaisonsAdressesLieesSurEvenement($idEvenement=0)
    {
        if($idEvenement!=0)
        {
            $req = "DELETE FROM _evenementAdresseLiee WHERE idEvenement=$idEvenement";
            $res = $this->connexionBdd->requete($req);
        }
    }
    
    
    
    // recupere les idEvenement des adresses n'appartenant pas au groupe d'adresse courant, mais liés par la table _evenementAdresseLiee
    public function getIdEvenementFromEvenementAdressesLiees($idEvenementGroupeAdresse=0)
    {
        $reqEvenementsAdressesLiees = "
                SELECT distinct idEvenement
                FROM _evenementAdresseLiee
                WHERE idEvenementGroupeAdresse='$idEvenementGroupeAdresse'";
                
        
        $resEvenementsAdressesLiees = $this->connexionBdd->requete($reqEvenementsAdressesLiees);
        $arrayEvenementsAdressesLiees = array();
        while($fetchEvenementsAdressesLiees = mysql_fetch_assoc($resEvenementsAdressesLiees))
        {
            $arrayEvenementsAdressesLiees[] = $fetchEvenementsAdressesLiees['idEvenement'];
        }
        
        return $arrayEvenementsAdressesLiees;
    }
    
    
    
    
    // *************************************************************************************************************************************
    // le parametre idHistoriqueEvenement n'est plus utilisé
    public function afficher($idEvenement = null,$modeAffichage='', $idHistoriqueEvenement = null, $paramChampCache=array(),$params=array())
    {
        $html = '';
        
        $erreurObject = new objetErreur();
        
        
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('ev'=>'evenement.tpl'));
        $isMH   =   false;
        $isISMH =   false;
        $isAffichageSingleEvenement=false;
        $listeGroupeAdressesAffichees = array(); // liste des groupes d'adresses reellement affichés , donc dans cette liste ne figure pas les groupe d'adresses ou il n'y a pas d'evenements liés
        
        $authentification = new archiAuthentification();
        $u = new archiUtilisateur();
        
        // on renvoi le nom de type de structure ( ceci sert a voir si on affiche aussi le type de structure sur l'evenement suivant du groupe d'adresse (pour eviter les redondances) )
        $retourIdTypeStructure="";
        // on renvoi le titre de l'evenement pour l'affichage des ancres
        $retourTitreAncre="";
        $retourNomTypeEvenement="";
        $retourDate="";
        $retourDateFin = "";
        
        $adresse = new archiAdresse();
        
        $imageObject = new imageObject(); // objet image du framework
        
        $idAdresseCourante = 0;
        if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='')
        {
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        }
        
        $t->assign_vars(array("divDisplayMenuAction"=>"none"));
        
        
        
        
        
        
        
        // fabrication de la requete en fonction des parametres
        $sqlEvenementsAdressesLiees="";
        if (empty($idEvenement))
        {
            if($modeAffichage=='consultationHistoriqueEvenement')
            {
                // on affiche les idHistoriqueEvenement de l'evenement choisi
                $isAffichageSingleEvenement=true;
                $sqlWhere = 'hE.idHistoriqueEvenement='.$idHistoriqueEvenement;
            }
            
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='comparaisonEvenement')
            {
                // debug pour eviter un message d'erreur si l'evenement n'existe pas dans l'affichage de la comparaison d'evenement ancien/nouveau
                
            
                
            }
        } else {
            if($modeAffichage=='simple')
            {
                // l'idEvenement en parametre correspond a l'evenement enfant a afficher
                $sqlWhere = 'hE.idEvenement='.$idEvenement;
            }
            else
            {
                // l'idEvenement en parametres correspond a un evenement enfant on va donc chercher l'evenement parent qui correspond au groupe d'adresses
                $sqlWhere = '(hE.idEvenement=
                IF (
                (SELECT _eE.idEvenement FROM _evenementEvenement _eE 
                    LEFT JOIN historiqueEvenement USING (idEvenement)
                    LEFT JOIN typeEvenement tE USING (idTypeEvenement) WHERE idEvenementAssocie='.$idEvenement.' LIMIT 1),
                (SELECT _eE.idEvenement FROM _evenementEvenement _eE 
                    LEFT JOIN historiqueEvenement USING (idEvenement)
                    LEFT JOIN typeEvenement tE USING (idTypeEvenement) WHERE idEvenementAssocie='.$idEvenement.' LIMIT 1),
                '.$idEvenement.')) ';
                
                // si on est en mode de deplacement d'image
                // ou de selection de titre
                // on rajoute le formulaire sur la page
                if($authentification->estConnecte() && ((isset($this->variablesGet['afficheSelectionImage']) && $this->variablesGet['afficheSelectionImage']=='1')||(isset($this->variablesGet['afficheSelectionTitre']) && $this->variablesGet['afficheSelectionTitre']=='1') ))
                {
                    $html.="<form action='' name='formulaireEvenement' method='POST' enctype='multipart/form-data' id='formulaireEvenement'>
                        <input type='hidden' name='actionFormulaireEvenement' id='actionFormulaireEvenement' value=''>";
                }
            }
        }
        
        if (isset($sqlWhere)) {

            /*
            **  REQUÊTE
            */
            // recuperation des données de l'evenement groupe d'adresses
            // on ne fait pas de group by   having 
            // on prend simplement le dernier enregistrement classé selon l'idHistoriqueEvenement 
            // et donc le nombre de resultats de la requete permet de recuperer le nombre d'historiques sur l'evenement dans la foulée
            $sql = 'SELECT  hE.idEvenement, hE.idHistoriqueEvenement, hE.titre, hE.idSource, hE.idTypeStructure, hE.idTypeEvenement, hE.description, hE.dateDebut, hE.dateFin, hE.dateDebut, hE.dateFin, tE.nom AS nomTypeEvenement, tS.nom AS nomTypeStructure, s.nom AS nomSource, u.nom AS nomUtilisateur,u.prenom as prenomUtilisateur, tE.groupe, hE.ISMH , hE.MH, date_format(hE.dateCreationEvenement,"'._("%e/%m/%Y à %kh%i").'") as dateCreationEvenement,hE.isDateDebutEnviron as isDateDebutEnviron, u.idUtilisateur as idUtilisateur, hE.numeroArchive as numeroArchive
                FROM historiqueEvenement hE 
                LEFT JOIN source s      ON s.idSource = hE.idSource 
                LEFT JOIN typeStructure tS  ON tS.idTypeStructure = hE.idTypeStructure 
                LEFT JOIN typeEvenement tE  ON tE.idTypeEvenement = hE.idTypeEvenement
                LEFT JOIN utilisateur u     ON u.idUtilisateur = hE.idUtilisateur
                WHERE '.$sqlWhere.' 
                ORDER BY hE.idHistoriqueEvenement DESC';
        
        
            $rep = $this->connexionBdd->requete($sql);
        } else {
            echo "<p><strong>Cette fiche n'existe plus, merci de <a href='".$this->creerUrl("", "contact")."'>contacter un administrateur</a>.</strong></p>";
        }
        
        // lien vers le formulaire d'ajout d'une adresse pour un evenement parent
        // recherche de l'evenement parent
        $idEvenementGroupeAdresse=$this->getParent($idEvenement);
        if($idEvenementGroupeAdresse==0)
        {
            $idEvenementGroupeAdresse = $idEvenement; // pas de parent trouvé , donc l'evenement est lui meme un parent
        }
        
        // ***************************
        // gestion mode deplacement d'images
        // ***************************
        $styleColorDeplacementActif="";
        $afficheSelectionImages=1;
        if($authentification->estConnecte() && isset($this->variablesGet['afficheSelectionImage']) && $this->variablesGet['afficheSelectionImage']=='1')
        {
            $styleColorDeplacementActif="color:red;";
            $afficheSelectionImages=0;
        }
        $t->assign_vars(
            array(
                'urlLierAdresseAEvenement'=>$this->creerUrl('','formulaireGroupeAdresses',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse)),
                'urlAjouterEvenement'=>$this->creerUrl('', 'ajouterSousEvenement',array('archiIdEvenement'=>$idEvenementGroupeAdresse)),
                'urlDeplacerImages'=>$this->creerUrl('', 'evenement',array('idEvenement'=>$idEvenementGroupeAdresse,'afficheSelectionImage'=>$afficheSelectionImages)),
                'styleModeDeplacementImageActif'=>"$styleColorDeplacementActif;"
            )
        );
        
        // **********************************
        // gestion selection du titre du groupe d'adresse
        // **********************************
        $styleColorSelectionTitreActif = "";
        $afficheSelectionTitre = 1;
        if($authentification->estConnecte() && isset($this->variablesGet['afficheSelectionTitre']) && $this->variablesGet['afficheSelectionTitre']=='1')
        {
            $styleColorSelectionTitreActif = "color:red;";
            $afficheSelectionTitre = 0;
        }
        

        $t->assign_vars(
            array(
                'urlSelectionTitreAdresse'=>$this->creerUrl('','evenement',array('idEvenement'=>$idEvenementGroupeAdresse,'afficheSelectionTitre'=>$afficheSelectionTitre)),
                'styleModeChoixTitre'=>$styleColorSelectionTitreActif
            )
        );
        
        // *******************************************
        // gestion selection de l'image principale du groupe d'adresse
        // *******************************************
        $styleColorSelectionImagePrincipaleActif = "";
        $afficheSelectionImagePrincipale = 1;
        if ($authentification->estConnecte() && isset($this->variablesGet['afficheSelectionImagePrincipale']) && $this->variablesGet['afficheSelectionImagePrincipale']=='1') {
            $styleColorSelectionImagePrincipaleActif = "color:red";
            $afficheSelectionImagePrincipale = 0;
        }   
        
        $t->assign_vars(
            array(              
                'urlSelectionImagePrincipale'=>$this->creerUrl('','evenement',array('idEvenement'=>$idEvenementGroupeAdresse,'afficheSelectionImagePrincipale'=>$afficheSelectionImagePrincipale)),
                'styleModeSelectionImagePrincipale'=>$styleColorSelectionImagePrincipaleActif
            )
        );
        
        
        // *******************************************
        // gestion positionnement evenements
        // *******************************************
        $styleColorPositionnementEvenements = "";
        $affichePositionnementEvenements = 1;
        if($authentification->estConnecte() && isset($this->variablesGet['affichePositionnementEvenements']) && $this->variablesGet['affichePositionnementEvenements']=='1')
        {
            $styleColorPositionnementEvenements = "color:red";
            $affichePositionnementEvenements = 0;
            
            $this->addToJsHeader($imageObject->getJSFunctionsDragAndDrop(array('withBalisesScript'=>true))); // rajoute les fonctions de deplacement d'elements dans le header du formulaire
        }
        
        
        
        $t->assign_vars(
            array(              
                'urlPositionnementEvenements'=>$this->creerUrl('', 'evenement', array('archiIdAdresse'=>$idAdresseCourante,'idEvenement'=>$idEvenementGroupeAdresse, 'affichePositionnementEvenements'=>$affichePositionnementEvenements)),
                'styleModePositionnementEvenements'=>$styleColorPositionnementEvenements
            )
        );
        
        
        
        if (isset($sqlWhere) && mysql_num_rows($rep) > 0)
        {

            $nbHistorique  = mysql_num_rows($rep)-1; // on ne compte pas le groupe d'adresse qui a le meme idEvenement
            $res = mysql_fetch_object($rep);
            
            $idEvenement = $res->idEvenement;

            // si c'est un groupe d'adresse, on n'affiche pas le détail de l'évènement, juste ses évènements enfants
            if ($res->groupe!=3)
            {
                if ($modeAffichage === 'simple' || $modeAffichage=='consultationHistoriqueEvenement')
                {
                    $t->assign_block_vars('simple', array());

                    
                    
                    // si l'evenement est un evenement externe au groupe d'adresse , on affiche un menu different a droite
                    if(isset($params['isLieFromOtherAdresse']) && $params['isLieFromOtherAdresse']==true)
                    {
                        // on recupere d'abord l'idAdresse de cet evenement externe
                        $reqAdresses = $adresse->getIdAdressesFromIdEvenement(array('idEvenement'=>$idEvenement));
                        $resAdresses = $this->connexionBdd->requete($reqAdresses);
                        $fetchAdressesExternes = mysql_fetch_assoc($resAdresses);
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($fetchAdressesExternes['idAdresse'],'idAdresse');
                        //$t->assign_vars(array("divDisplayMenuAction"=>"none"));
                        
                        $ancrePositionAdresseLiee = "";
                        
                        $positionSurAdresseOrigine = $adresse->getPositionFromEvenement($idEvenement);
                        
                        if($positionSurAdresseOrigine!='' && $positionSurAdresseOrigine!='0')
                        {
                            $ancrePositionAdresseLiee = "#".$positionSurAdresseOrigine;
                        }
                        
                        $t->assign_vars(array("urlEvenementExterne"=>"<small>"._("Adresse d'origine :")." <a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchAdressesExternes['idAdresse'])).$ancrePositionAdresseLiee."'>".$intituleAdresse."</a></small>"));
                    }
                    else
                    {
                        if($modeAffichage!='consultationHistoriqueEvenement')
                            $t->assign_vars(array("divDisplayMenuAction"=>"table"));
                    }
                    
                    
                    if($authentification->estConnecte())
                    {
                        $t->assign_block_vars('simple.menuAction',array());
                        if($modeAffichage!='consultationHistoriqueEvenement')
                        {
                            if(isset($params['isLieFromOtherAdresse']) && $params['isLieFromOtherAdresse']==true)
                            {
                                $t->assign_vars(array("divDisplayMenuAction"=>"none")); // si un utilisateur est connecté , et que l'evenement est un evenement lie , on affiche pas le menu d'action
                            }
                            else
                            {
                                $t->assign_vars(array("divDisplayMenuAction"=>"table")); // si un utilisateur est connecté et que l'evenement n'est pas un evenement lié, on affiche le menu d'action
                            }
                        }
                        else
                        {
                            $t->assign_vars(array("divDisplayMenuAction"=>"none")); // si on est en mode d'affichage de l'historique d'un evenement, on ne propose pas de menu action
                        }
                        
                        
                        if($u->isAuthorized('evenement_lier_adresses',$authentification->getIdUtilisateur()))
                        {
                            if (!archiPersonne::isPerson($idEvenementGroupeAdresse) && ($u->getIdProfil($authentification->getIdUtilisateur())==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(), $idEvenementGroupeAdresse, 'idEvenementGroupeAdresse')))
                            {
                                $t->assign_block_vars('simple.menuAction.afficheElementMenuLierAdresse', array());
                            }
                        }
                    }
                    
                    if($authentification->estAdmin())
                    {
                        $t->assign_block_vars('simple.menuAction.isAdmin', array());
                    }
                    
                    if($u->isAuthorized('evenement_supprimer',$authentification->getIdUtilisateur()))
                    {
                        $isModerateurFromVilleCourante = false;
                        if($authentification->getIdProfil() == 3) // moderateur
                        {
                            $arrayVillesModerees = $u->getArrayVillesModereesPar($authentification->getIdUtilisateur());
                            $idVilleAdresseCourante = $adresse->getIdVilleFrom($idEvenement, 'idEvenement');
                            if(in_array($idVilleAdresseCourante,$arrayVillesModerees))
                            {
                                $isModerateurFromVilleCourante=true;
                            }
                        }
                        
                        if($authentification->getIdProfil() == 4 || $isModerateurFromVilleCourante) // est on administrateur ou moderateur de la ville ?
                        {
                            $t->assign_block_vars('simple.menuAction.isAdminOrModerateurFromVille',array());
                        }
                    }
                    
                    if(!archiPersonne::isPerson($idEvenementGroupeAdresse) && $u->isAuthorized('evenement_deplacer', $authentification->getIdUtilisateur()))
                    {
                        $t->assign_block_vars('simple.menuAction.afficheElementMenuDeplacerEvenement',array());
                    }
                    
                    if($authentification->estConnecte() && $authentification->estAdmin() && isset($this->variablesGet['afficheSelectionImage']) && $this->variablesGet['afficheSelectionImage']=='1')
                    {
                        $t->assign_block_vars('simple.menuAction.isAdmin.isAffichageSelectionImages',array());
                    }
                }
                

                // ******************************************************************************************************
                // Affichage des dates
                $dateTxt="";
                $environDateDebutTxt = "";
                if($res->isDateDebutEnviron=='1')
                {
                    $environDateDebutTxt = "environ ";
                }
                
                
                switch(strtolower($res->nomTypeEvenement))
                {
                    case 'information (nouveautés)':
                    case 'extension':
                    case 'inauguration':
                    case 'exposition':
                        $articleAvantTypeEvenement = "de l'";
                    break;
                    default:
                        $articleAvantTypeEvenement = "de";
                    break;
                }
                
                if (substr($res->dateDebut, 5)=="00-00"){
                    $datetime=substr($res->dateDebut, 0, 4);
                } else {
                    $datetime = $res->dateDebut;
                }
                
                if($res->nomTypeEvenement =='Construction')
                {
                    if($res->dateDebut!='0000-00-00')
                    {
                        

                        $dateTxt=_("Année de construction :")." <time itemprop='startDate' datetime='".$datetime."'>".$environDateDebutTxt.$this->date->toFrenchAffichage($res->dateDebut)."</time>";
                    }
                    
                    if($res->dateFin!='0000-00-00')
                    {
                        $dateTxt.=" (-> ".$this->date->toFrenchAffichage($res->dateFin).")";
                        $retourDateFin = " (-> ".$this->date->toFrenchAffichage($res->dateFin).")";
                    }
                }
                else
                {
                    if($res->dateDebut != '0000-00-00')
                    {
                        
                        if($res->MH != '1' && $res->ISMH != '1')
                        {
                            
                            if (pia_strlen($this->date->toFrench($res->dateDebut))<=4) {
                                if (archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                                    $nomTypeEvenement = "début";
                                } else {
                                    $nomTypeEvenement=strtolower($res->nomTypeEvenement);
                                }
                                $dateTxt=_("Année")." ".$articleAvantTypeEvenement." <time itemprop='startDate' datetime='".$datetime."'>".$nomTypeEvenement." : $environDateDebutTxt".$this->date->toFrenchAffichage($res->dateDebut)."</time>";
                            } else {
                                if (archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                                    $typeEvenement = "";
                                } else {
                                    $typeEvenement=$articleAvantTypeEvenement." ".strtolower($res->nomTypeEvenement);
                                }
                                $dateTxt=_("Date")." <time itemprop='startDate' datetime='".$datetime."'>".$typeEvenement." : $environDateDebutTxt".$this->date->toFrenchAffichage($res->dateDebut)."</time>";
                            }
                        }
                        
                        if($res->MH=='1')
                        {
                            $dateTxt= "<b>"._("Classement Monument Historique")."</b> $environDateDebutTxt le ".$this->date->toFrenchAffichage($res->dateDebut);
                            $isMH=true;
                        }
                        
                        if($res->ISMH == '1')
                        {
                            $dateTxt = "<b>"._("Inscription à l'Inventaire Supplémentaire des Monuments Historiques")."</b> $environDateDebutTxt : ".$this->date->toFrenchAffichage($res->dateDebut);
                            if($res->MH =='1')
                            {
                                $dateTxt.="<br><b>"._("Classement Monument Historique")."</b> $environDateDebutTxt : ".$this->date->toFrenchAffichage($res->dateDebut);
                            }
                            $isISMH=true;
                        }
                    }
                    
                    if($res->dateFin != '0000-00-00')
                    {
                        if(pia_strlen($this->date->toFrench($res->dateFin))<=4)
                            $dateTxt.=" "._("à")." ".$this->date->toFrenchAffichage($res->dateFin);
                        else
                            $dateTxt.=" "._("au")." ".$this->date->toFrenchAffichage($res->dateFin);
                    }
                }
                // ******************************************************************************************************
                
                // on renseigne la variable nomTypeStructure pour la renvoyer au retour de la fonction
                $retourIdTypeStructure = $res->idTypeStructure;
                // idem pour le titre qui sera affiché en resume pour le tableau de liens
                $retourTitreAncre="";
                if(stripslashes($res->titre)!='')
                    $retourTitreAncre .= htmlspecialchars(stripslashes($res->titre));
                else
                    $retourTitreAncre .= _("Sans titre");
                    
                $retourNomTypeEvenement=$res->nomTypeEvenement;
                $retourDate=$environDateDebutTxt." ".$this->date->toFrench($res->dateDebut);
                $dateDebut=$res->dateDebut;
                if(isset($paramChampCache['idTypeStructurePrecedent']) && $res->idTypeStructure==$paramChampCache['idTypeStructurePrecedent'])
                {
                    $typeStructure='';
                }
                else
                {
                    if (archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                        $typeStructure="";
                    } else {
                        $typeStructure="Structure : <a href='".$this->creerUrl('', 'evenementListe', array('selection' => 'typeStructure', 'id' => $res->idTypeStructure))."'>".$res->nomTypeStructure."</a><br>";
                    }
                }
                
                $source = "";
                if($res->idSource!=0)
                {
                    $source="Source : <a href='".$this->creerUrl('','listeAdressesFromSource',array('source'=>$res->idSource,'submit'=>'Rechercher'))."' onmouseover=\"document.getElementById('calqueDescriptionSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDescriptionSource').style.display='block';document.getElementById('iframe').src='".$this->creerUrl('','descriptionSource',array('archiIdSource'=>$res->idSource,'noHeaderNoFooter'=>1))."';\" onmouseout=\"document.getElementById('calqueDescriptionSource').style.display='none';\">".stripslashes($res->nomSource)."</a><br>";
                }
                else
                {
                    // exception temporaire (?)
                    if($res->numeroArchive!='') // s'il y a un numero d'archive et pas de source précisée , on dit que la source est "archives municipales" (voir fabien)
                    {
                        $reqSourceStatic ="SELECT nom as nomSource FROM source WHERE idSource='24'";
                        $resSourceStatic = $this->connexionBdd->requete($reqSourceStatic);
                        $fetchSourceStatic = mysql_fetch_assoc($resSourceStatic);
                        $source="Source : <a href='".$this->creerUrl('','listeAdressesFromSource',array('submit'=>'Rechercher','source'=>24))."' onmouseover=\"document.getElementById('calqueDescriptionSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDescriptionSource').style.display='block';document.getElementById('iframe').src='".$this->creerUrl('','descriptionSource',array('archiIdSource'=>24,'noHeaderNoFooter'=>1))."';\" onmouseout=\"document.getElementById('calqueDescriptionSource').style.display='none';\">".stripslashes($fetchSourceStatic['nomSource'])."</a><br>";
                    }
                
                }

                // DESCRIPTION ---
                $bbCode = new bbCodeObject();
                $description = $bbCode->convertToDisplay(array('text'=>$res->description));
                $description = empty($description)?"":"<div itemprop='description' class='desc'>".$description."</div>";
                // --
                
                
                
                
                // numeroArchive
                $numeroArchive = "";
                if($res->numeroArchive!='')
                {
                // modif fabien du 15/04/2011 suite mail directrice Archives de Strasbourg Mme Perry Laurence
                    $numeroArchive = "Cote Archives de Strasbourg : ".$res->numeroArchive."<br>";
                }
                
                
                
                
                

                $adressesLieesHTML = $this->getAdressesLieesAEvenement(array('modeRetour'=>'affichageSurDetailEvenement','idEvenement'=>$idEvenement));
                if($adressesLieesHTML!='')
                {
                    $adressesLieesHTML="<b>"._("Liste des adresses liées :")."</b> <br>".$adressesLieesHTML;
                }
                
                
                // recherche s'il y a un historique sur l'evenement courant ( plusieurs mises à jour)
                $lienHistoriqueEvenementCourant="";
                if($authentification->estConnecte() && $this->getNbEvenementsInHistorique(array('idEvenement'=>$idEvenement))>1 && $modeAffichage!='consultationHistoriqueEvenement' && (!isset($params['isLieFromOtherAdresse']) || $params['isLieFromOtherAdresse']!=true))
                {
                    $lienHistoriqueEvenementCourant="<a href='".$this->creerUrl('','consultationHistoriqueEvenement',array('archiIdEvenement'=>$idEvenement))."'>("._("Consulter l'historique").")</a>";
                }
                
                $onClickEvenementDeplacerVersGA="";
                if($authentification->estConnecte() && $u->isAuthorized('evenement_deplacer',$authentification->getIdUtilisateur()) && $modeAffichage!='consultationHistoriqueEvenement' && isset($params['calquePopupDeplacerEvenement']))
                {
                    $onClickEvenementDeplacerVersGA = "document.getElementById('".$params['calquePopupDeplacerEvenement']->getJSIFrameId()."').src='".$this->creerUrl('','recherche',array('noHeaderNoFooter'=>1,'modeAffichage'=>"popupDeplacerEvenementVersGroupeAdresse","idEvenementADeplacer"=>$res->idEvenement))."';document.getElementById('".$params['calquePopupDeplacerEvenement']->getJSDivId()."').style.display='block';";
                }
                
                $txtEnvoi = _("Envoyé");
                $dateEnvoi="";

                if(!$this->isFirstIdHistoriqueEvenementFromHistorique($res->idHistoriqueEvenement))
                {
                    $txtEnvoi = _("Modifié");
                }
                $dateEnvoi = _("le")." ".$res->dateCreationEvenement;
                
                
                $arrayIdAdresseToUrl = array();
                if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='')
                {
                    $arrayIdAdresseToUrl = array('archiIdAdresse'=>$this->variablesGet['archiIdAdresse']);
                }
                
                $resTypeEvenement = $this->connexionBdd->requete("SELECT idTypeEvenement,nom FROM typeEvenement where groupe = '".$this->getGroupeFromTypeEvenement($res->idTypeEvenement)."'");
        
                $fetchTypeEvenement = mysql_fetch_assoc($resTypeEvenement);
                
                if (archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                    $typeEvenement=_("Biographie");
                    $urlTypeEvenement="";
                } else {
                    $typeEvenement=$res->nomTypeEvenement;
                    $urlTypeEvenement=$this->creerUrl('', 'evenementListe', array('selection' => 'typeEvenement', 'id' => $res->idTypeEvenement));
                }
                $titre=empty($res->titre)?"<meta itemprop='name' content='".$fetchTypeEvenement['nom']."' />":"<h3 itemprop='name'>".htmlspecialchars(stripslashes($res->titre))."</h3>";
                //
                $t->assign_vars(array(
                    'txtEnvoi'    => $txtEnvoi,
                    'dateEnvoi'     =>$dateEnvoi,
                    'titre'       => $titre, 
                    'utilisateur' => "<a href='".$this->creerUrl('','detailProfilPublique',array('archiIdUtilisateur'=>$res->idUtilisateur,'archiIdEvenementGroupeAdresseOrigine'=>$idEvenementGroupeAdresse))."'>".$res->nomUtilisateur." ".$res->prenomUtilisateur."</a>",
                    'dates'   => $dateTxt,
                    'typeEvenement' => $typeEvenement, 
                    'typeStructure' => $typeStructure, 
                    'idSource'  => $res->idSource,
                    'source'    => $source,
                    'description'   => $description,
                    'urlTypeEvenement' => $urlTypeEvenement,
                    'urlTypeStructure' => $this->creerUrl('', 'evenementListe', array('selection' => 'typeStructure', 'id' => $res->idTypeStructure)),
                    'ajouterEvenement'           => '',
                    'supprimerEvenement'             => $this->creerUrl('supprimerEvenement', '',  array('archiIdEvenement'=>$res->idEvenement)),
                    'supprimerHistoriqueEvenement'   => $this->creerUrl('supprimerHistoriqueEvenement', '',array('archiIdHistoriqueEvenement'=>$res->idHistoriqueEvenement)),
                    'ajouterImage'     => $this->creerUrl('','ajoutImageEvenement',array('archiIdEvenement'=>$res->idEvenement)),
                    'modifierImage'    => $this->creerUrl('','modifierImageEvenement',array('archiIdEvenement'=>$res->idEvenement)),
                    'modifierEvenement'=> $this->creerUrl('', 'modifierEvenement', array_merge(array('archiIdEvenement' => $res->idEvenement,'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse),$arrayIdAdresseToUrl)),
                    
                    'urlImporterImage'=>"#",
                    'onClickImporterImage'=>"document.getElementById('formulaireEvenement').action='".$this->creerUrl('deplacerImagesSelectionnees','evenement',array('idEvenement'=>$idEvenement))."&deplacerVersIdEvenement=".$res->idEvenement."';document.getElementById('actionFormulaireEvenement').value='deplacerImages';if(confirm('Voulez-vous vraiment déplacer ces images ?')){document.getElementById('formulaireEvenement').submit();}",
                    'urlSupprimerImage'=>"#",
                    'onClickSupprimerImage'=>"document.getElementById('formulaireEvenement').action='".$this->creerUrl('supprimerImagesSelectionnees','evenement',array('idEvenement'=>$idEvenement))."';document.getElementById('actionFormulaireEvenement').value='supprimerImages';if(confirm('Voulez-vous vraiment supprimer ces images ?')){document.getElementById('formulaireEvenement').submit();}",
                    'urlLierAdresses'=>$this->creerUrl('','afficheFormulaireEvenementAdresseLiee',array('idEvenement'=>$res->idEvenement)),
                    'listeAdressesLiees'=>$adressesLieesHTML,
                    'lienHistoriqueEvenementCourant'=>$lienHistoriqueEvenementCourant,
                    
                    'urlDeplacerVersNouveauGroupeAdresse'=>$this->creerUrl('deplacerEvenementVersNouveauGA','evenement',array('idEvenement'=>$res->idEvenement)),
                    
                    'onClickDeplacerVersAdresses'=>$onClickEvenementDeplacerVersGA,
                    'numeroArchive'=>$numeroArchive
                    ));
                    

                $idEvenement = $res->idEvenement;
                
                
                // affichage des images de l'evenement
                if($modeAffichage!='consultationHistoriqueEvenement')
                {
                    $images = new archiImage();
                    
                    $arrayImagesVuesSurByDate=array();
                    if(isset($params['imagesVuesSurLinkedByDate']) && count($params['imagesVuesSurLinkedByDate'])>0)
                    {
                        $arrayImagesVuesSurByDate = $params['imagesVuesSurLinkedByDate'];
                    }
                    
                    $t->assign_vars(array( 'imagesLiees' => $images->afficherFromEvenement($idEvenement, array('withoutImagesVuesSurPrisesDepuis'=>true,'imagesVuesSurLinkedByDate'=>$arrayImagesVuesSurByDate,'idGroupeAdresseEvenementAffiche'=>$idEvenementGroupeAdresse))));
                }
                
                if (($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) && isset($params["nextEvent"])) {
                    $req = "
                        SELECT dateDebut
                        FROM historiqueEvenement
                        WHERE idEvenement = '".$params["nextEvent"]."'
                        ORDER BY idHistoriqueEvenement DESC LIMIT 1
                    ";
                    
                    $res = $this->connexionBdd->requete($req);
                    $date2 =mysql_fetch_object($res);
                    $linkedEventsHTML=archiPersonne::displayEvenementsLies($idPerson, $dateDebut, $date2->dateDebut);
                    
                    $t->assign_vars(array("evenementsLiesPersonne" => $linkedEventsHTML));
                } else if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                    $linkedEventsHTML=archiPersonne::displayEvenementsLies($idPerson, $dateDebut, 3000);
                    
                    $t->assign_vars(array("evenementsLiesPersonne" => $linkedEventsHTML));
                }
                
                /*
                **  COURANTS ARCHI
                */

                $rep = $this->connexionBdd->requete('
                    SELECT  cA.idCourantArchitectural, cA.nom 
                         FROM _evenementCourantArchitectural _eA 
                    LEFT JOIN courantArchitectural cA  ON cA.idCourantArchitectural  = _eA.idCourantArchitectural 
                    WHERE _eA.idEvenement='.$idEvenement.' 
                    ORDER BY cA.nom ASC');
                
                if(mysql_num_rows($rep)>0)
                {
                    $t->assign_block_vars('simple.isCourantArchi',array());
                    while( $res = mysql_fetch_object($rep)) 
                    {
                        $t->assign_block_vars('simple.isCourantArchi.archi', array(
                            'url' => $this->creerUrl('', 'evenementListe', array('selection' => 'courant', 'id' => $res->idCourantArchitectural)),
                            'nom' => $res->nom));
                    }
                }

                /*
                **  PERSONNES
                */

                $rep = $this->connexionBdd->requete('
                    SELECT  p.idPersonne, m.nom as metier, p.nom, p.prenom 
                         FROM _evenementPersonne _eP
                    LEFT JOIN personne p ON p.idPersonne = _eP.idPersonne 
                    LEFT JOIN metier m ON m.idMetier = p.idMetier
                    WHERE _eP.idEvenement='.$idEvenement.' 
                    ORDER BY p.nom DESC');
                
                $metier="";
                while( $res = mysql_fetch_object($rep)) 
                {
                
                    if(isset($res->metier) && $res->metier!='')
                    {
                        $metier = $res->metier.' : ';
                    }
                    $t->assign_block_vars('simple.pers', array(
                        'urlDetail'    => $this->creerUrl('', 'personne', array('idPersonne' => $res->idPersonne)),
                        'urlEvenement' => $this->creerUrl('', 'evenementListe', array('selection' => 'personne', 'id' => $res->idPersonne)),
                        'nom' => stripslashes($res->nom),
                        'prenom' => stripslashes($res->prenom),
                        'metier' => stripslashes($metier),
                        'idPerson' => $res->idPersonne,
                        'idEvent' => $idEvenement
                    ));
                    if($authentification->estConnecte())
                    {
                        $t->assign_block_vars('simple.pers.connected',array());
                    }
                }
                
                //*
                //**    Historique
                //*/

                $t->assign_vars(array( 'nbHistorique' => $nbHistorique));
                if ($nbHistorique > 0) {
                    $t->assign_block_vars('histo', array('url' => $this->creerUrl('', 'historiqueEvenement', array('idEvenement' => $idEvenement))));
                }
            }
            
            // *************************************************************************************************************************************
            // affichage des adresses et evenements lies dans la partie supérieur du detail
            // que l'évènement soit un groupe d'adresse ou non, on affiche les enfants
            // *************************************************************************************************************************************
            if($modeAffichage!='simple' && $modeAffichage!='consultationHistoriqueEvenement')
            {
                $tabIdEvenementsLies=$this->getEvenementsLies($idEvenement); // recherche des sous evenements (idEvenementAssocie) du groupe d'adresse
                
                if(count($tabIdEvenementsLies)>0)
                {
                    $listeGroupeAdressesAffichees[] =  $idEvenement;
                    $t->assign_block_vars('noSimple',array());  // vu que la fonction afficher est recursive , on affiche cette partie uniquement en affichant 'non simple' 
                                                                //=> affichage simple = affichage de l'evenement => affichage non simple = affichage de la page ... 
                    
                    if ($modeAffichage=="personne") {
                        
                    } else {
                        $retourAdresse=$adresse->afficherListe(array('archiIdEvenement'=>$idEvenement,'useTemplateFile'=>'listeAdressesDetailEvenement.tpl'),"listeDesAdressesDuGroupeAdressesSurDetailAdresse"); // modeAffichage => listeDesAdressesDuGroupeAdressesSurDetailAdresse
                    }
                    
                    $idAdresseCourante = 0;
                    if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='')
                    {
                        $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
                    }
                    if ($modeAffichage!="personne") {
                        // ************************************************************************************************************************
                        // affichage carte googlemap dans une iframe
                        
                        $coordonnees = $adresse->getCoordonneesFrom($idEvenement,'idEvenementGroupeAdresse');//$retourAdresse['arrayIdAdresses'][0]
                        // si pas de coordonnees , on va chercher les coordonnees de la ville courante
                        if($coordonnees['longitude']==0 && $coordonnees['latitude']==0)
                        {
                            $idVilleAdresseCourante = $adresse->getIdVilleFrom($idAdresseCourante,'idAdresse');
                            $coordonnees = $adresse->getCoordonneesFrom($idVilleAdresseCourante,'idVille');
                        }
                        
                    
                    
                        $calqueGoogleMap = new calqueObject(array('idPopup'=>10));
                        $contenuIFramePopup = $this->getContenuIFramePopupGoogleMap(array(
                                            'idAdresseCourante'=>$idAdresseCourante,
                                            'calqueObject'=>$calqueGoogleMap,
                                            'idEvenementGroupeAdresseCourant'=>$idEvenement
                                            ));
                        // ********************************************************************************************************************************
                            
                            
                        $t->assign_block_vars('noSimple.isCarteGoogle',array(
                                        'src'=>$this->creerUrl('','afficheGoogleMapIframe',array('noHeaderNoFooter'=>1,'longitude'=>$coordonnees['longitude'],'latitude'=>$coordonnees['latitude'],'archiIdAdresse'=>$idAdresseCourante,'archiIdEvenementGroupeAdresse'=>$idEvenement)),
                                        'lienVoirCarteGrand'=>"<a href='#' onclick=\"".$calqueGoogleMap->getJsOpenPopupNoDraggableWithBackgroundOpacity()."document.getElementById('iFrameDivPopupGM').src='".$this->creerUrl('','afficheGoogleMapIframe',array('longitude'=>$coordonnees['longitude'],'latitude'=>$coordonnees['latitude'],'noHeaderNoFooter'=>true,'archiIdAdresse'=>$idAdresseCourante,'archiIdEvenementGroupeAdresse'=>$idEvenement,'modeAffichage'=>'popupDetailAdresse'))."';\" class='bigger' style='font-size:11px;'>"._("Voir la carte en + grand")."</a>",
                                        'popupGoogleMap'=>$calqueGoogleMap->getDivNoDraggableWithBackgroundOpacity(array('top'=>20,'lienSrcIFrame'=>'','contenu'=>$contenuIFramePopup))
                        ));//'popupGoogleMap'=>$calqueGoogleMap->getDiv(array('width'=>550,'height'=>570,'lienSrcIFrame'=>''))."<script  >".$calqueGoogleMap->getJsToDragADiv()."</script>"
                        $t->assign_vars(array('largeurTableauResumeAdresse'=>415,'hauteurRecapAdresse'=>'270'));
                        
                        // ************************************************************************************************************************
                        // affichage de l'encars des adresses avec les photos avant et apres l'adresse courante
                        $arrayEncartAdresses = $adresse->getArrayEncartAdressesImmeublesAvantApres(array('idEvenementGroupeAdresse'=>$idEvenement));
                        $t->assign_block_vars('noSimple.adresses', array(
                            'adressesLiees' => $arrayEncartAdresses['html'])); 
                        
                        $t->assign_vars(
                            array(
                                'urlAutresBiensRue'=>$retourAdresse['arrayRetourLiensVoirBatiments']['urlAutresBiensRue'],
                                'urlAutresBiensQuartier'=>$retourAdresse['arrayRetourLiensVoirBatiments']['urlAutresBiensQuartier']
                            )
                        );
                        
                        if($authentification->estConnecte())
                        {
                            $t->assign_block_vars('noSimple.isConnected', array());
                            $t->assign_block_vars('noSimple.adresses.isConnected', array());
                        }
                        
                        
                        if($retourAdresse['nbAdresses']==0)
                        {
                            $t->assign_vars(array('intituleActionAdresses'=>_("Ajouter une adresse")));
                        }
                        else
                        {
                            $t->assign_vars(array('intituleActionAdresses'=>_("Modifier")));
                        }
                        
                        
                        
                        if($authentification->estConnecte() && $authentification->estAdmin())
                        {
                            // si l'utilisateur est connecté et est admin, on affiche le lien pour selectionner des images
                            $t->assign_block_vars('noSimple.isConnected.afficheLienSelectionImages',array());               
                        }
                    
                        if($authentification->estConnecte() && $u->isAuthorized('affiche_menu_evenement_choix_titre',$authentification->getIdUtilisateur()))
                        {
                            if($u->getIdProfil($authentification->getIdUtilisateur())==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(),$idEvenementGroupeAdresse,'idEvenementGroupeAdresse'))
                            {
                                // tout utilisateur autorisé (admin+moderateur de la ville)  peut changer le titre par defaut du groupe d'adresse en selectionnant l'evenement de son choix ( le titre est récupéré de cet evenement),si l'autorisation est faite dans la gestion des droites de l'admin du site
                                $t->assign_block_vars('noSimple.isConnected.afficheLienSelectionTitre',array());
                            }
                        }
                        
                        if($authentification->estConnecte() && $u->isAuthorized('affiche_menu_evenement_choix_image_principale',$authentification->getIdUtilisateur()))
                        {
                            if($u->getIdProfil($authentification->getIdUtilisateur())==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(),$idEvenementGroupeAdresse,'idEvenementGroupeAdresse'))
                            {
                                // un utilisateur moderateur de la ville de l'adresse courant ou un admin peuvent selectionner ou changer l'image principale du groupe d'adresse,si l'autorisation est faite dans la gestion des droites de l'admin du site
                                $t->assign_block_vars('noSimple.isConnected.afficheLienSelectionImagePrincipale',array());
                            }
                        }
                        
                        
                        if($authentification->estConnecte() && $u->isAuthorized('affiche_menu_evenement_positionnement_evenements',$authentification->getIdUtilisateur()))
                        {
                            if($u->getIdProfil($authentification->getIdUtilisateur())==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(),$idEvenementGroupeAdresse,'idEvenementGroupeAdresse'))
                            {
                                // un utilisateur moderateur de la ville courante ou admin peuvent changer la position des evenements, independemment de la date de debut de l'evenement ,si l'autorisation est faite dans la gestion des droites de l'admin du site
                                $t->assign_block_vars('noSimple.isConnected.afficheLienPositionnementEvenements',array());
                            }
                        }
                        
                    }
                    
                    
                    //popup pour la recherche d'adresse dans le cas d'un deplacement d'evenement
                    if($authentification->estConnecte() && $u->isAuthorized('evenement_deplacer',$authentification->getIdUtilisateur()))
                    {
                        $c = new calqueObject(array('idPopup'=>"divPopupDeplacerEvenementVersGA".$idEvenementGroupeAdresse));
                        $t->assign_vars(array('divDeplacerEvenementVersGA'=>$c->getDiv(array(
                                'lienSrcIFrame'=>$this->creerUrl('','recherche',array('noHeaderNoFooter'=>1,'modeAffichage'=>'popupDeplacerEvenementVersGroupeAdresse')),'titrePopup'=>_("Recherche d'adresses"),'width'=>700))));
                        
                        $params['calquePopupDeplacerEvenement'] = $c;
                    }
                    
                    $images = new archiImage();
                    // **********************************************************************************************************************************************************
                    // recherche des images en rapport avec l'adresse courante
                    
                    $resAdressesCourantes= $adresse->getAdressesFromEvenementGroupeAdresses($idEvenement);
                    $listeAdressesFromEvenement=array();
                    while($fetchAdressesCourantes = mysql_fetch_assoc($resAdressesCourantes))
                    {
                        //$fetchAdressesCourantes = mysql_fetch_assoc($resAdressesCourantes);
                        $listeAdressesFromEvenement[] = $fetchAdressesCourantes['idAdresse'];
                    }
                    
                    
                    if ($modeAffichage=="personne") {
                        
                    } else {
                        $arrayCorrespondancesVuesSur = $this->getArrayCorrespondancesIdImageVuesSurAndEvenementByDateFromGA($idEvenementGroupeAdresse);
                        $arrayNoDiplayIdImageVueSur=array();
                        foreach($arrayCorrespondancesVuesSur as $indice => $values)
                        {
                            foreach($values as $indice => $value)
                            {
                                $arrayNoDiplayIdImageVueSur[] = $value['idImage'];
                            }
                        
                        }
                    }
                    if(count($listeAdressesFromEvenement)>0)
                    {
                        $arrayAutresVuesSur = $images->getAutresPhotosVuesSurAdresse($listeAdressesFromEvenement,'moyen',array('noDiplayIdImage'=>$arrayNoDiplayIdImageVueSur,'idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'idGroupeAdresseEvenementAffiche'=>$idEvenementGroupeAdresse,'setZoomOnImageZone'=>true));
                        
                        $autresVuesSurHTML=$arrayAutresVuesSur['htmlVueSur'];
                        $codeARajouterALaFin=$arrayAutresVuesSur['htmlZonesDivMapJs'];
                        $autresPrisesDepuisHTML = $images->getAutresPhotosPrisesDepuisAdresse($listeAdressesFromEvenement,'moyen',array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'idGroupeAdresseEvenementAffiche'=>$idEvenementGroupeAdresse));
                        
                    
                        if($autresVuesSurHTML!='')
                        {
                            $t->assign_block_vars('noSimple.autresVuesSur',array('value'=>$autresVuesSurHTML));
                        }
                        
                        if($autresPrisesDepuisHTML!='')
                        {
                            $t->assign_block_vars('noSimple.autresPrisesDepuis',array('value'=>$autresPrisesDepuisHTML));
                        }
                        
                        
                        $arrayLiens = array();
                        if ($modeAffichage!="personne") {
                            $arrayListeAdressesCourantes = array_unique($retourAdresse['arrayIdAdresses']);
                        }
                        
                        //foreach($arrayListeAdressesCourantes as $indice => $idAdresseCourante)
                        //{
                            $arrayLiens[]="<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$idAdresseCourante,'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))."'>".$adresse->getIntituleAdresseFrom($idEvenementGroupeAdresse,'idEvenementGroupeAdresse',array("noQuartier"=>true,"noVille"=>true,"noSousQuartier"=>true,"ifTitreAfficheTitreSeulement"=>true))."</a>";
                        //}
                        
                        $t->assign_vars(array('listeAdressesCourantes'=>implode(" / ",$arrayLiens)));
                    }
                    // **********************************************************************************************************************************************************
                    // **********************************************************************************************************************************************************
                    // **********************************************************************************************************************************************************
                    // affichage des evenements de l'evenement groupe d'adresses
                    // **********************************************************************************************************************************************************
                    $champsCacheTransmis=array();
                    // 
                    
                    $idEvenementWhereIsTitre = $this->getIdEvenementTitre(array("idEvenementGroupeAdresse"=>$idEvenementGroupeAdresse));
                    $idAdresse = $adresse->getIdAdresseFromIdEvenementGroupeAdresse($idEvenementGroupeAdresse);
                    
                    
                    // initialisation des variables pour la creation du tableau de configuration de la liste triable par glisser deposer des evenements dans l'onglet historique des evenements
                    if (isset($this->variablesGet['affichePositionnementEvenements']) && $this->variablesGet['affichePositionnementEvenements']=='1') {
                        $arrayConfigPositionnementEvenement = array();
                        $indicePositionnement=0;
                    }
                    
                    $i=0;
                    // parcours des evenements
                    foreach ($tabIdEvenementsLies as $indice => $value) {
                        $params['isLieFromOtherAdresse'] = $value['isLieFromOtherAdresse'];
                        $params['imagesVuesSurLinkedByDate'] = array();
                        if (isset($tabIdEvenementsLies[$i+1]["idEvenementAssocie"])) {
                            $params["nextEvent"]=$tabIdEvenementsLies[$i+1]["idEvenementAssocie"];
                        } else {
                            $params["nextEvent"]=null;
                        }
                        if(isset($arrayCorrespondancesVuesSur[$value['idEvenementAssocie']]))
                            $params['imagesVuesSurLinkedByDate'] = $arrayCorrespondancesVuesSur[$value['idEvenementAssocie']];
                        // le type de structure est le meme que l'evenement precedent , on ne l'affiche pas, c'est pour cela qu'on le transmet a la fonction d'affichage
                        $retourEvenement = $this->afficher($value['idEvenementAssocie'], 'simple', null, $champsCacheTransmis,$params);
                        $champsCacheTransmis=array('idTypeStructurePrecedent'=>$retourEvenement['idTypeStructure']);
                        
                        $titreAncre = $retourEvenement['titreAncre'];
                                            
                        if($titreAncre == 'Sans titre' && ($retourEvenement['isMH'] || $retourEvenement['isISMH'])) {
                            if($retourEvenement['isMH']) {
                                $titreAncre = "<b>"._("Classement aux Monuments Historiques")."</b>";
                            }
                            
                            if($retourEvenement['isISMH']) {
                                $titreAncre = "<b>"._("Inscription à l'Inventaire Supplémentaire des Monuments Historiques")."</b>";
                            }
                        } elseif($titreAncre != _("Sans titre") && ($retourEvenement['isMH'] || $retourEvenement['isISMH'])) {
                            if($retourEvenement['isMH'])
                            {
                                $titreAncre .= " <b>(MH)</b>";
                            }
                            
                                                        
                            if($retourEvenement['isISMH'])
                            {
                                $titreAncre .= " <b>(ISMH)</b>";
                            }
                        }
                        
                        if($retourEvenement['nomTypeEvenement']!='')
                        {
                            $titreAncre.=" - ".$retourEvenement['nomTypeEvenement'];
                        }
                        if($retourEvenement['date']!='')
                        {
                            $titreAncre.=" - ".$retourEvenement['date'];
                        }
                        
                        if($retourEvenement['dateFin']!='')
                        {
                            $titreAncre.=" ".$retourEvenement['dateFin'];
                        }
                        
                        
                        
                        if(isset($this->variablesGet['affichePositionnementEvenements']) && $this->variablesGet['affichePositionnementEvenements']=='1')
                        {
                            // si on est en mode de positionnement des evenements manuellement
                            $arrayConfigPositionnementEvenement[$indicePositionnement]['idEvenement']   = array('value'=>$value['idEvenementAssocie'],'type'=>'identifiant');
                            $arrayConfigPositionnementEvenement[$indicePositionnement]['illustration']  = array('value'=>"<img src='".$this->urlImages."doubleFlechesVerticales.jpg' border=0>",'type'=>'free');
                            $arrayConfigPositionnementEvenement[$indicePositionnement]['titre']         = array('value'=>$titreAncre,'type'=>'free','styleColonneDonnees'=>'font-size:12px;');
                            
                            
                            $indicePositionnement++;
                            /*$t->assign_block_vars('noSimple.modePositionnementEvenements',array());
                            
                            $t->assign_block_vars('noSimple.modePositionnementEvenements.titresEvenements',array(
                                                            'imagesIllustrationGlisserDeposer'=>$this->urlImages."",
                                                            'titre' => $titreAncre
                                                            
                                            ));
                            */
                            //'url'=> $this->creerUrl('selectTitreAdresse','adresseDetail',array('archiIdAdresse'=>$idAdresse,'idEvenementTitreSelection'=>$value['idEvenementAssocie'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))
                        }
                        elseif(isset($this->variablesGet['afficheSelectionTitre']) && $this->variablesGet['afficheSelectionTitre']=='1')
                        {
                            // si on est en mode de selection d'un titre le titre courant s'affiche en rouge
                            if($idEvenementWhereIsTitre == $value['idEvenementAssocie'])
                            {
                                $titreAncre="<span style='color:red;'>".$titreAncre."</span>";
                            }
                            $t->assign_block_vars('noSimple.ancres',array(
                                                            'titre' => $titreAncre,
                                                            'url'=> $this->creerUrl('selectTitreAdresse','adresseDetail',array('archiIdAdresse'=>$idAdresse,'idEvenementTitreSelection'=>$value['idEvenementAssocie'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))
                                            ));
                            
                        }
                        else
                        {
                            $t->assign_block_vars('noSimple.ancres',array(
                                                            'titre' => $titreAncre,
                                                            'url'=> "#".$indice
                                            ));
                        }
                        
                        $t->assign_block_vars( 'evenementLie', array(
                                                            'evenement' => $retourEvenement['html'],
                                                            'numeroAncre'=>$indice
                                            ));
                                            
                        $i++;
                    }
                    
                    
                    // affichage de la liste triable a la place de l'historique des evenements, quand on est en mode positionnement d'evenements
                    if(isset($this->variablesGet['affichePositionnementEvenements']) && $this->variablesGet['affichePositionnementEvenements']=='1')
                    {
                        $t->assign_block_vars('noSimple.modePositionnementEvenements',array()); // affiche le bloc
                        $t->assign_vars(array(
                            'sortableFormListe'=>$imageObject->createSortableFormListeFromArray($arrayConfigPositionnementEvenement,array('noEntetesTableau'=>true)).$imageObject->getJSInitAfterListDragAndDrop('slideshow0',array('onlyHiddenFormField'=>true)),
                            'onClickValidationPositionnementEvenement'=>$imageObject->getJSSubmitDragAndDrop()."document.getElementById('formPositionnementEvenements').submit();",
                            'formActionPositionnementEvenements'=>$this->creerUrl('enregistrerPositionnementEvenements','adresseDetail',array('archiIdAdresse'=>$idAdresseCourante,'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))
                            ));
                        
                        
                        // ajout du code d'initialisation au niveau de la fin de la page (footer)
                        $this->addToJsFooter($imageObject->getJSInitAfterListDragAndDrop('slideshow0',array('onlyJavascriptInitCode'=>true)));
                        
                        
                    }
                    
                    // ajout de la possibilité de préciser que l'on ne veut pas de titre , l'adresse s'affichera donc pour l'evenement a la place d'un titre
                    if (isset($this->variablesGet['afficheSelectionTitre']) && $this->variablesGet['afficheSelectionTitre']=='1')
                    {
                        $titreAncre="Pas de titre";
                        if($idEvenementWhereIsTitre == -1)
                        {
                            $titreAncre="<span style='color:red;'>Pas de titre</span>";
                        }
                        $t->assign_block_vars('noSimple.choixEvenementSansTitre',array(
                                                        'titre' => $titreAncre,
                                                        'url'=> $this->creerUrl('selectTitreAdresse','adresseDetail',array('archiIdAdresse'=>$idAdresse,'idEvenementTitreSelection'=>-1,'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse))
                                        ));
                    
                    }
                    
                    
                    
                    // affichage de la liste des historiques de nom de rue pour l'adresse si celle ci ne concerne qu'une rue , sans numero de rue.
                    $infosAdresseCourante = $adresse->getArrayAdresseFromIdAdresse($idAdresse);
                    
                    if(($infosAdresseCourante['numero']=='0' || $infosAdresseCourante['numero']=='') && ($infosAdresseCourante['idRue']!='' || $infosAdresseCourante['idRue']!='0'))
                    {
                        $d = new dateObject();
                        // dans ce cas on va chercher les infos de l'historique de la rue
                        $reqHistoriqueNomRue = "SELECT * FROM historiqueNomsRues WHERE idRue = '".$infosAdresseCourante['idRue']."' ORDER BY annee";
                        $resHistoriqueNomRue = $this->connexionBdd->requete($reqHistoriqueNomRue);
                        if(mysql_num_rows($resHistoriqueNomRue)>0)
                        {
                            $t->assign_block_vars("noSimple.isHistoriqueNomsRue",array());
                            while($fetchHistoriqueNomRue = mysql_fetch_assoc($resHistoriqueNomRue))
                            {
                                if($fetchHistoriqueNomRue['prefixe']!='')
                                    $nomRue = stripslashes($fetchHistoriqueNomRue['prefixe'])." ".stripslashes($fetchHistoriqueNomRue['nomRue']);
                                else
                                    $nomRue = stripslashes($fetchHistoriqueNomRue['nomRue']);
                                
                                $t->assign_block_vars("noSimple.listeHistoriqueNomsRue",array(
                                                "annee"=>$d->toFrenchAffichage($fetchHistoriqueNomRue['annee']),
                                                "nomRue"=>$nomRue,
                                                "commentaire"=>stripslashes($fetchHistoriqueNomRue['commentaire']))
                                );
                            }
                        }
                    }
                    
                    $t->assign_vars(array('nbEvenements'=>count($tabIdEvenementsLies)+1));
                } else {
                    
                }
            }
            // *************************************************************************************************************************************
        } else {
            echo 'aucun résultat !';
        }


        
        ob_start();
        $t->pparse('ev');
        $html .= ob_get_contents();
        ob_end_clean();
        

        if(isset($codeARajouterALaFin))
            $html.=$codeARajouterALaFin; // a cause du JS executé a la fin sinon il ne trouve pas certains divs
        
        
        // on ferme le formulaire s'il a ete ouvert
        if($modeAffichage!='simple' && $modeAffichage!='consultationHistoriqueEvenement')
        {
            if($authentification->estConnecte() && ((isset($this->variablesGet['afficheSelectionImage']) && $this->variablesGet['afficheSelectionImage']=='1') || (isset($this->variablesGet['afficheSelectionTitre']) && $this->variablesGet['afficheSelectionTitre']=='1')))
            {
                $html.="</form>";
            }
        }
                
        
        return array('html'=>$html,'idTypeStructure'=>$retourIdTypeStructure,'titreAncre'=>$retourTitreAncre,'nomTypeEvenement'=>$retourNomTypeEvenement,'date'=>$retourDate,'isMH'=>$isMH,'isISMH'=>$isISMH,'listeGroupeAdressesAffichees'=>$listeGroupeAdressesAffichees,'dateFin'=>$retourDateFin);
    }
    
    // *****************************************************************************************************************************************************************************************************
    // renvoi le contenu de la popup google map (celle qui est fixe avec le fond assombri, imitation google)
    public function getContenuIFramePopupGoogleMap($params = array())
    {
        $authentification = new archiAuthentification();
        $u = new archiUtilisateur();
        $adresse = new archiAdresse();
        
        $idEvenementGroupeAdresseCourant = $params['idEvenementGroupeAdresseCourant'];
        $idAdresseCourante = $params['idAdresseCourante'];
        $calqueGoogleMap = $params['calqueObject'];
        
        
        $isAuthorizedModifierCoordonnees = false;
                    
        if($authentification->estConnecte() && $u->isAuthorized('googlemap_change_coordonnees',$authentification->getIdUtilisateur()))
        {
            if($authentification->getIdProfil()==4 || $u->isModerateurFromVille($authentification->getIdUtilisateur(),$idAdresseCourante,'idAdresse'))
            {
                $isAuthorizedModifierCoordonnees = true;
            }
        }

        
        $contenuIFramePopup = "";
        
        // ********************************************************************************************************************************
        // formulaire de modification des coordonnees

        $contenuIFramePopup .= "<table border=1 style='padding:0px;margin:0px;'><tr><td colspan=2><a href='#' onclick=\"".$calqueGoogleMap->getJsClosePopupNoDraggableWithBackgroundOpacity()."\">Fermer</a></td></tr><tr><td style='width:200px;'>";
        
        // affichage des adresses et du titre
        $contenuIFramePopup.="<span style='font-size:11px;'>".$adresse->getIntituleAdresseFrom($idEvenementGroupeAdresseCourant,'idEvenementGroupeAdresse',array("noQuartier"=>true,"noVille"=>true,"noSousQuartier"=>true,"ifTitreAfficheTitreSeulement"=>true))."</span>";
        $contenuIFramePopup.="<br>";
        $contenuIFramePopup.="<span style='font-size:9px;'>".$adresse->getIntituleAdresseFrom($idAdresseCourante,'idAdresse',array("noQuartier"=>true,"noVille"=>true,"noSousQuartier"=>true))."</span>";
        $contenuIFramePopup.="<br>";
        $contenuIFramePopup.="<br>";
        $contenuIFramePopup.="<br>";
        
        if($isAuthorizedModifierCoordonnees)
        {
            $contenuIFramePopup.="<span class='textePetit'>Déplacez le marqueur central <img src='".$this->getUrlImage()."placeMarker.png"."' border=0> pour indiquer de nouvelles coordonnées pour l'adresse courante puis validez.</span>";
            
            // generation du formulaire de recuperation des coordonnees
            $form = new formGenerator();
            
            $configFormFields = array(
                'longitudeUser'=>array('type'=>'hidden','default'=>'','libelle'=>'longitude','error'=>'','value'=>'','htmlCode'=>''),
                'latitudeUser'=>array('type'=>'hidden','default'=>'','libelle'=>'latitude','error'=>'','value'=>'','htmlCode'=>''),
                'idEvenementGroupeAdresseCourant'=>array('type'=>'hidden','default'=>$idEvenementGroupeAdresseCourant,'libelle'=>'idEvenementGroupeAdresseCourant','error'=>'','value'=>'','htmlCode'=>''),
                'idAdresseCourante'=>array('type'=>'hidden','default'=>$idAdresseCourante,'libelle'=>'idAdresseCourante','error'=>'','value'=>'','htmlCode'=>'')
            );
            
            $configForm = array(
                    'fields'=>$configFormFields,
                    'formAction'=>$this->creerUrl('enregistreNouvelleCoordonneesGoogleMap','adresseDetail',array('archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresseCourant,'archiIdAdresse'=>$idAdresseCourante)),
                    'formName'=>'formCoordonnees',
                    'submitButtonValue'=>_("Valider"),
                    'submitButtonId'=>'validationCoordonnees',
                    'codeHtmlSubmitButton'=>"style='display:none;'"
                    );
            
            $contenuIFramePopup .= $form->afficherFromArray($configForm);
        }
        $contenuIFramePopup .= "</td><td style='width:650px;'>";
        $contenuIFramePopup .= "<iframe id='iFrameDivPopupGM' style='padding:0;margin:0;border:0; overflow:hidden;width:650px;height:480px;'  ></iframe></td></tr></table>";
        
        
        
        
        return $contenuIFramePopup;
    }
    
    
    
    // **********************************************************************************************
    // recherche s'il y a des images 'vuesSur' a associer sur les evenements du groupe d'adresse de l'evenement passé en parametre
    // on retour un tableau de correspondance entre les evenements du groupe d'adresses et les images vuesSur a afficher
    // dans la liste des images liées à l'évènement
    // **********************************************************************************************
    public function getArrayCorrespondancesIdImageVuesSurAndEvenementByDateFromGA($idGA=0)
    {
        $retour=array();
        // recuperation des adresses du groupe d'adresse
        $reqAdresse = "SELECT idAdresse FROM _adresseEvenement WHERE idEvenement = '$idGA'";
        $resAdresse = $this->connexionBdd->requete($reqAdresse);
        $arrayAdresses = array();
        while($fetchAdresse = mysql_fetch_assoc($resAdresse))
        {
            $arrayAdresses[] = $fetchAdresse['idAdresse'];
        }
        
        // parcours du tableau d'evenement, avec recherche dans les images vuesSur liées a l'adresse du groupe d'adresse
        $reqVuesSur = "
            SELECT ai.idImage as idImage, hi1.dateCliche,hi1.idHistoriqueImage as idHistoriqueImage, hi1.nom as nom, hi1.description as description, hi1.dateUpload as dateUpload
            FROM _adresseImage ai
            LEFT JOIN historiqueImage hi1 ON hi1.idImage = ai.idImage
            LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
            WHERE ai.vueSur = '1' 
            AND hi1.dateCliche<>'0000-00-00'
            AND ai.idAdresse IN (".implode(",",$arrayAdresses).")
            
            AND ai.idEvenementGroupeAdresse='$idGA'
            
            GROUP BY hi1.idImage,hi1.idHistoriqueImage
            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
            
            
            ";
        
        $resVuesSur = $this->connexionBdd->requete($reqVuesSur);
        
        
        if(mysql_num_rows($resVuesSur)>0)
        { // il y a des images vues sur
        
            $arrayEvenementsDates=array();
            $reqEvenements = "
                SELECT he1.idEvenement as idEvenement,he1.dateDebut as dateDebutEvenement,he1.dateFin as dateFinEvenement
                FROM historiqueEvenement he2, historiqueEvenement he1
                LEFT JOIN _evenementEvenement ee ON he1.idEvenement = ee.idEvenementAssocie
                WHERE he2.idEvenement = he1.idEvenement
                AND ee.idEvenement = $idGA
                AND he1.dateDebut<>'0000-00-00'
                GROUP BY he1.idEvenement , he1.idHistoriqueEvenement
                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
                ";
            
            $resEvenements = $this->connexionBdd->requete($reqEvenements);
            $i=0;
            while($fetchEvenements = mysql_fetch_assoc($resEvenements))
            {
                $arrayEvenementsDates[$i] = array('idEvenement'=>$fetchEvenements['idEvenement'],'dateDebut'=>$fetchEvenements['dateDebutEvenement'],'dateFin'=>$fetchEvenements['dateFinEvenement']);
                $i++;
            }
            
            
            $d = new dateObject();
            while($fetchVuesSur = mysql_fetch_assoc($resVuesSur))
            {
                for($i=0 ; $i<count($arrayEvenementsDates); $i++)
                {
                    if($arrayEvenementsDates[$i]['dateFin']!='0000-00-00')
                    {
                        if($d->isGreaterThan($arrayEvenementsDates[$i]['dateFin'],$fetchVuesSur['dateCliche'],true,true)
                            && $d->isGreaterThan($fetchVuesSur['dateCliche'],$arrayEvenementsDates[$i]['dateDebut'],true,true)
                            && $d->isGreaterThan($arrayEvenementsDates[$i]['dateDebut'],$fetchVuesSur['dateCliche'],false,true)
                        )
                        {// $arrayEvenementsDates[$i]['dateFin']>=$fetchVuesSur['dateCliche'] 
                        //$arrayEvenementsDates[$i]['dateDebut']<=$fetchVuesSur['dateCliche'] 
                        // $fetchVuesSur['dateCliche']<$arrayEvenementsDates[$i]['dateDebut'] 
                            if(!isset($retour[$arrayEvenementsDates[$i]['idEvenement']]) || !in_array($fetchVuesSur,$retour[$arrayEvenementsDates[$i]['idEvenement']]))
                                $retour[$arrayEvenementsDates[$i]['idEvenement']][] = $fetchVuesSur;
                        }
                    }
                    elseif(isset($arrayEvenementsDates[$i+1]['dateDebut']) 
                        && $d->isGreaterThan($fetchVuesSur['dateCliche'],$arrayEvenementsDates[$i]['dateDebut'],true,true)
                        && $d->isGreaterThan($arrayEvenementsDates[$i+1]['dateDebut'],$fetchVuesSur['dateCliche'],false,true)
                    )
                    {//$arrayEvenementsDates[$i]['dateDebut']<=$fetchVuesSur['dateCliche'] 
                    // $fetchVuesSur['dateCliche']<$arrayEvenementsDates[$i]['dateDebut'] 
                        if(!isset($retour[$arrayEvenementsDates[$i]['idEvenement']]) || !in_array($fetchVuesSur,$retour[$arrayEvenementsDates[$i]['idEvenement']]))
                            $retour[$arrayEvenementsDates[$i]['idEvenement']][] = $fetchVuesSur;
                    }
                    elseif(!isset($arrayEvenementsDates[$i+1]['dateDebut']) 
                        && $d->isGreaterThan($fetchVuesSur['dateCliche'],$arrayEvenementsDates[$i]['dateDebut'],true,true)
                    )
                    {//$arrayEvenementsDates[$i]['dateDebut']<=$fetchVuesSur['dateCliche']
                        if(!isset($retour[$arrayEvenementsDates[$i]['idEvenement']]) || !in_array($fetchVuesSur,$retour[$arrayEvenementsDates[$i]['idEvenement']]))
                            $retour[$arrayEvenementsDates[$i]['idEvenement']][] = $fetchVuesSur;
                    }
                }
            }
        }
        return $retour;
    }
    
    
    
    // ******************************************************************************************
    // compte le nombre d'evenement qui ont le meme idEvenement => nombre de mise a jour de l'evenement+evenement courant
    // ******************************************************************************************
    public function getNbEvenementsInHistorique($params=array())
    {
        $retour=0;
        if(isset($params['idEvenement']))
        {
            $req = "SELECT count(idHistoriqueEvenement) as nb FROM historiqueEvenement WHERE idEvenement=".$params['idEvenement'];
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            $retour = $fetch['nb'];
        }
        
        return $retour;
    }
    
    // ******************************************************************************************
    // affichage de la liste des historiques d'evenements
    // ******************************************************************************************
    public function afficheHistoriqueEvenement($params=array())
    {
        $html="";
        $adresse = new archiAdresse();
        if(isset($params['idEvenement']))
        {
            $req = "SELECT idHistoriqueEvenement FROM historiqueEvenement WHERE idEvenement=".$params['idEvenement']." order by dateCreationEvenement ASC";
            $res = $this->connexionBdd->requete($req);
            
            $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromIdEvenement($params['idEvenement']);
            if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                $person= new archiPersonne();
                $infos=$person->getInfosPersonne($idPerson);
                $html.="<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>";
            } else {
                $html.=$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse);
            }
            $html.=$this->afficherRecapitulatifAncres($idEvenementGroupeAdresse, $params['idEvenement']);

            $html.="<h2>"._("Historique de mise à jour")."</h2><br>";
            
            while($fetch = mysql_fetch_assoc($res))
            {
                $arrayAfficher = $this->afficher(null,'consultationHistoriqueEvenement',$fetch['idHistoriqueEvenement']);
                $html.="<div class='divConsultationHistoriqueEvenement'>".$arrayAfficher['html']."</div>";
            }
                    
        }
        return $html;
    }
    
    
    // ******************************************************************************************
    // est ce que l'idHistoriqueEvenement passé en parametre est le premier de l'historique (le premier qui a ete envoyé),
    // les autres idHistoriqueEvenement du meme evenement sont donc des mises a jour de celui ci
    // ******************************************************************************************
    public function isFirstIdHistoriqueEvenementFromHistorique($idHistoriqueEvenement=0)
    {
        $retour = false;
        // recuperation de l'idEvenement 
        $reqIdEvenement = "SELECT idEvenement FROM historiqueEvenement WHERE idHistoriqueEvenement=$idHistoriqueEvenement";
        $resIdEvenement = $this->connexionBdd->requete($reqIdEvenement);
        $fetchIdEvenement = mysql_fetch_assoc($resIdEvenement);
        $idEvenement = $fetchIdEvenement['idEvenement'];
        
        // on recherche l'idHistoriqueEvenement du premier evenement ayant comme idEvenement l'idEvenement de l'idHistoriqueEvenement passé en parametre
        $reqIdHistoriqueEvenement = "
                    SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement
                    FROM historiqueEvenement he2, historiqueEvenement he1
                    WHERE he1.idEvenement=$idEvenement
                    AND he2.idEvenement = he1.idEvenement
                    GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                    HAVING he1.idHistoriqueEvenement = min(he2.idHistoriqueEvenement)
        ";
        $resIdHistoriqueEvenement = $this->connexionBdd->requete($reqIdHistoriqueEvenement);
        $fetchIdHistoriqueEvenement = mysql_fetch_assoc($resIdHistoriqueEvenement);
        if($fetchIdHistoriqueEvenement['idHistoriqueEvenement'] == $idHistoriqueEvenement)
        {
            $retour = true;
        }
        return $retour;
    }
    
    // ******************************************************************************************
    // enregistrement de la selection du titre
    // ******************************************************************************************
    public function enregistreSelectionTitreGroupeAdresse($params=array())
    {
        if(isset($this->variablesGet['idEvenementTitreSelection']) && $this->variablesGet['idEvenementTitreSelection']!='' && isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='')
        {
            $idEvenementGroupeAdresse = $this->variablesGet['archiIdEvenementGroupeAdresse'];
            $req = "update historiqueEvenement set idEvenementRecuperationTitre=".$this->variablesGet['idEvenementTitreSelection']." WHERE idEvenement=$idEvenementGroupeAdresse";
            
            $res = $this->connexionBdd->requete($req);
        }
    }
    
    
    // ******************************************************************************************
    // renvoi l'idEvenement qui affiche le titre , 
    // si pas d'idEvenement précisé sur le groupe d'adresse, on renvoi l'id du premier evenement, si celui ci a un titre
    // sinon on renvoi 0
    // ******************************************************************************************
    public function getIdEvenementTitre($params=array())
    {
        $retour = 0;
        $trouve=false;
        
        if(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='0')
        {
            $req = "SELECT idEvenementRecuperationTitre FROM historiqueEvenement WHERE idEvenement=".$params['idEvenementGroupeAdresse'];
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            
            // on verifie que l'evenement existe, on ne sait jamais
            if($fetch['idEvenementRecuperationTitre']=='-1') // dans ce cas on affiche pas de titre mais l'adresse a la place
            {
                $retour='-1';
                $trouve=true;
            }
            elseif($fetch['idEvenementRecuperationTitre']!='0')
            {
                $reqVerifEvenementTitre = "SELECT idHistoriqueEvenement from historiqueEvenement WHERE idEvenement=".$fetch['idEvenementRecuperationTitre'];
                $resVerifEvenementTitre = $this->connexionBdd->requete($reqVerifEvenementTitre);
                if(mysql_num_rows($resVerifEvenementTitre)>0)
                {
                    $retour = $fetch['idEvenementRecuperationTitre'];
                    $trouve=true;
                }
            }
            
            if(!$trouve)
            {
                // on renvoi l'id du premier evenement qui a un titre
                // s'il n'y en a pas on renvoi 0
                $reqIdEvenementTitre="
                    SELECT distinct he1.idEvenement as idEvenementTitre
                    FROM historiqueEvenement he2, historiqueEvenement he1
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ".$params['idEvenementGroupeAdresse']."
                    WHERE he1.titre!=''
                    AND he1.idTypeEvenement<>6
                    AND he2.idEvenement = he1.idEvenement
                    AND he1.idEvenement = ee.idEvenementAssocie
                    GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                    HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                    ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
                    LIMIT 1
                ";
                
                $resIdEvenementTitre = $this->connexionBdd->requete($reqIdEvenementTitre);
                if(mysql_num_rows($resIdEvenementTitre)>0)
                {
                    $fetchIdEvenementTitre = mysql_fetch_assoc($resIdEvenementTitre);
                    $retour = $fetchIdEvenementTitre['idEvenementTitre'];
                }
            }
        }       
        
        return $retour;
    }
    
    
    // ******************************************************************************************
    // affichage du formulaire de liaison entre les evenements existants et les autres adresses
    // utilisation de la table "_evenementAdressesLiees"
    // ******************************************************************************************
    public function afficheFormulaireAdresseLieeEvenement($params=array())
    {
        $html="";
        $a = new archiAdresse();
        
        $c = new calqueObject();

        // on place la popup dans le code
        $html.=$c->getDiv(array('width'=>700,
                    'height'=>400,
                    'left'=>200,
                    'top'=> 200,
                    'lienSrcIFrame'=>$this->creerUrl('','recherche',array('noHeaderNoFooter'=>1,'modeAffichage'=>'popupAjoutAdressesLieesSurEvenement')),
                    'titrePopup'=>"recherche d'adresses"));

        // rendre la popup deplacable
        $html.="<script  >".$c->getJsToDragADiv()."</script>";
        
        $html.="
        <script  >
            function retirerGroupeAdresse(idGroupeAdresseValue)
            {
                document.getElementById('listeGroupesAdressesLiees').innerHTML=''; // on vide le div
                
                if(idGroupeAdresseValue!=0)
                {
                    selectField = document.getElementById('listeIdGroupesAdressesLiees');
                    divField = document.getElementById('listeGroupesAdressesLiees');
                    for(i=0 ; i<selectField.options.length; i++ )
                    {
                        if(selectField.options[i]!=null)
                        {
                            if(selectField.options[i].value==idGroupeAdresseValue)
                            {
                                indiceARetirer = i;
                            }
                            else
                            {
                                divField.innerHTML+=selectField.options[i].innerHTML+'<a href=\'#\' style=\'cursor:pointer;\' onclick=\'retirerGroupeAdresse('+selectField.options[i].value+')\'>(-)</a><br>';
                            }
                        }
                    }
                    
                    selectField.options[indiceARetirer]=null;
                }
            
            }
            
        </script>";
        
        if(isset($params['idEvenement']))
        {
            $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromIdEvenement($params['idEvenement']);
            $html.=$a->afficherRecapitulatifAdresses($idEvenementGroupeAdresse);
            $html.=$this->afficherRecapitulatifAncres($idEvenementGroupeAdresse,$params['idEvenement']);
            $html.="<h1>Lier des adresses</h1>";
            
            $reqAdresses = $a->getIdAdressesFromIdEvenement(array('idEvenement'=>$params['idEvenement']));
            $resAdresses = $this->connexionBdd->requete($reqAdresses);
            $fetchAdresses = mysql_fetch_assoc($resAdresses); // on prend la premiere adresse
            
            
                        $codeAppelPopupRechercheAdresseLiee="document.getElementById('".$c->getJSIFrameId()."').src='".$this->creerUrl('',  'recherche',array('noHeaderNoFooter'=>1,'modeAffichage'=>'popupAjoutAdressesLieesSurEvenement'))."';";
                    
            // ensuite on place le code pour l'affichage
            $codeAppelPopupRechercheAdresseLiee.=$c->getJSOpenPopup();
            
            $html.=_("Ajouter des adresses qui seront liées à cet evenement :")." <a onclick=\"$codeAppelPopupRechercheAdresseLiee\" style='cursor:pointer;'>Ajouter</a><br>";  
            
            $html.="<form action='".$this->creerUrl('enregistreAdressesLieesAEvenement','adresseDetail',array('idEvenement'=>$params['idEvenement'],'archiIdAdresse'=>$fetchAdresses['idAdresse']))."' name='formAdressesLiees' method='POST' enctype='multipart/form-data'>";
            $html.="<select name='listeIdGroupesAdressesLiees[]' id='listeIdGroupesAdressesLiees' multiple style='display:none;'>".$this->getAdressesLieesAEvenement(array('modeRetour'=>'optionsListeSelect','idEvenement'=>$params['idEvenement']))."</select>";
            $html.="<div id='listeGroupesAdressesLiees'>".$this->getAdressesLieesAEvenement(array('modeRetour'=>'affichageHTML','idEvenement'=>$params['idEvenement']))."</div>";
            
        
            $html.="<input type='submit' value='"._("Enregistrer")."'>";
            $html.="</form>";
        }
    
        return $html;
    }
    
    // *********************************************************************
    // cette fonction renvoie en html la liste des adresses liées a un evenement
    // utilisation de la table "_evenementAdressesLiees"
    // *********************************************************************
    public function getAdressesLieesAEvenement($params=array())
    {
        $html="";
        $req = "SELECT distinct idEvenementGroupeAdresse 
                FROM _evenementAdresseLiee 
                WHERE idEvenement=".$params['idEvenement']."";
        $res = $this->connexionBdd->requete($req);
        $a = new archiAdresse();
        while($fetch = mysql_fetch_assoc($res))
        {
            switch($params['modeRetour'])
            {
                case 'affichageHTML':
                    $intituleAdresse = stripslashes($a->getIntituleAdresseFrom($fetch['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('displayFirstTitreAdresse'=>true)));
                    $html.=$intituleAdresse."(<a style='cursor:pointer;' onclick=\"retirerGroupeAdresse(".$fetch['idEvenementGroupeAdresse'].")\">-</a>)<br>";
                break;
                case 'optionsListeSelect':
                    $intituleAdresse = stripslashes($a->getIntituleAdresseFrom($fetch['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('displayFirstTitreAdresse'=>true)));
                    $html.="<option value='".$fetch['idEvenementGroupeAdresse']."' SELECTED>".$intituleAdresse."</option>";
                break;
                case 'affichageSurDetailEvenement':
                    $intituleAdresse = stripslashes($a->getIntituleAdresseFrom($fetch['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('displayFirstTitreAdresse'=>true)));
                    $idAdresse = $a->getIdAdresseFromIdEvenementGroupeAdresse($fetch['idEvenementGroupeAdresse']);
                    $html.="<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$idAdresse,'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGroupeAdresse']))."'>".$intituleAdresse."</a><br>";
                break;
            }
            
        }
        return $html;
    }
    
    // *********************************************************************
    // enregistrement des adresses liées à un evenement
    // utilisation de la table "_evenementAdressesLiees"
    // *********************************************************************
    public function enregistreAdressesLieesAEvenement()
    {
        if(isset($this->variablesGet['idEvenement']) && $this->variablesGet['idEvenement']!='' )
        {
            // d'abord il faut supprimer les ancienne liaisons de positions d'evenements auquel n'appartiennent pas les evenements , ceux ci seront ensuite remis en place avec la fonction majPositionsEvenements
            $reqEvenementLie = "SELECT idEvenement,idEvenementGroupeAdresse FROM _evenementAdresseLiee WHERE idEvenement='".$this->variablesGet['idEvenement']."'";
            $resEvenementLie = $this->connexionBdd->requete($reqEvenementLie);
            while($fetchEvenementLie = mysql_fetch_assoc($resEvenementLie))
            {
                $idEvenement = $fetchEvenementLie['idEvenement'];
                $idEvenementGA = $fetchEvenementLie['idEvenementGroupeAdresse'];
                
                // suppression des liaisons precedentes une par une
                $reqSuppr = "DELETE FROM _evenementAdresseLiee WHERE idEvenement='".$idEvenement."' AND idEvenementGroupeAdresse='".$idEvenementGA."'";
                $resSuppr = $this->connexionBdd->requete($reqSuppr);
                // maj des positions du groupe d'adresse concerne, si le groupe d'adresse ne comporte plus l'adresse
                if(
                            !isset($this->variablesPost['listeIdGroupesAdressesLiees']) 
                        || !is_array($this->variablesPost['listeIdGroupesAdressesLiees']) 
                        || count($this->variablesPost['listeIdGroupesAdressesLiees'])==0
                        || !in_array($idEvenementGA,$this->variablesPost['listeIdGroupesAdressesLiees'])
                )
                {
                    // si l'une des conditions est remplie , on peut mettre a jour les groupes d'adresses qui ne seront plus liés pour qu'ils ne tiennent plus compte de la position (un simple refresh des positions)
                    // vu que cette fonction quand elle fait un refresh supprime d'abord tous les enregistrements des positions concernée et les regeneres , elle supprimera donc les anciennes adresses liées
                    // sinon si l'evenement groupe d'adresses est dans la liste des groupes d'adresses liés , pas besoin de rafraichir (sinon cela effacerait la position de l'evenement puisqu'on vient de l'effacer de la liste (on va le remettre un peu plus bas dans le code)
                    $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$idEvenementGA,'refreshAfterDelete'=>true));
                }
                
            }
        
        
            // ajout des liaisons vers les adresses selectionnées
            // d'abord on verifie qu'il n'y a pas de doublons dans la liste , car c'est possible
            
            $arrayIdAdresses = array();
            
            if(isset($this->variablesPost['listeIdGroupesAdressesLiees']))
            {
                foreach($this->variablesPost['listeIdGroupesAdressesLiees'] as $indice => $value)
                {
                    $arrayIdGroupesAdresses[] = $value;
                }
                
                $arrayIdGroupesAdresses=array_unique($arrayIdGroupesAdresses);      
                
                foreach($arrayIdGroupesAdresses as $indice => $value)
                {           
                    $reqAjout = "INSERT INTO _evenementAdresseLiee (idEvenement,idEvenementGroupeAdresse) VALUES ('".$this->variablesGet['idEvenement']."','".$value."')";
                    $resAjout = $this->connexionBdd->requete($reqAjout);
                    
                    
                    // on met a jour les positions des evenements sur chacun des groupes d'adresses lié, la fonction de mise a jour se comporte alors comme si on ajoute un nouvel element, pour que celui ci soit bien placé (si on fais un refresh simple avec cette fonction, elle ne positionnera pas le nouvel evenement
                    $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$value,'idNouvelEvenement'=>$this->variablesGet['idEvenement']));
                }
            }
        }
    }
    
    
    
    
    
    // *********************************************************************
    // recupere le titre du premier evenement
    // *********************************************************************
    public function getTitreFromFirstChildEvenement($idEvenementGroupeAdresse=0)
    {
        
        $query="
        select he.titre as titre
        from historiqueEvenement he2, historiqueEvenement he
        where he2.idEvenement = he.idEvenement
        and he.idEvenement =(select min(idEvenementAssocie) from _evenementEvenement where idEvenement = '".$idEvenementGroupeAdresse."')
        group by he.idEvenement, he.idHistoriqueEvenement
        having he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
        ";
        
        $res = $this->connexionBdd->requete($query);
        $fetch=mysql_fetch_assoc($res);
        
        $retour = $fetch['titre'];
        
        return $retour;
    }
    
    // *********************************************************************
    // recupere le titre et la description du premier evenement
    // *********************************************************************
    public function getDescriptionAndTitreFromFirstChildEvenement($idEvenementGroupeAdresse=0)
    {
        
        $query="
        select he.titre as titre,he.description as description
        from historiqueEvenement he2, historiqueEvenement he
        where he2.idEvenement = he.idEvenement
        and he.idEvenement =(select min(idEvenementAssocie) from _evenementEvenement where idEvenement = '".$idEvenementGroupeAdresse."')
        group by he.idEvenement, he.idHistoriqueEvenement
        having he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
        ";
        
        $res = $this->connexionBdd->requete($query);
        $fetch=mysql_fetch_assoc($res);
        
        $retour = array('titre'=>$fetch['titre'],'description'=>$fetch['description']);
        
        return $retour;
    }
    
    // renvoi les evenements lies enfants d'un groupe d'adresses
    public function getEvenementsLies($idEvenement=0,$afficheEvenementsAdressesLiees=true,$params = array())
    {
        $sqlEvenementsAdresses="";
        $arrayEvenementsAdressesLiees=array();
        if($afficheEvenementsAdressesLiees)
        {
            $arrayEvenementsAdressesLiees = $this->getIdEvenementFromEvenementAdressesLiees($idEvenement); // idEvenement est un groupe d'adresses

            if(count($arrayEvenementsAdressesLiees)>0)
            {
                $sqlEvenementsAdresses=" OR ee.idEvenementAssocie IN (".implode(",",$arrayEvenementsAdressesLiees).") ";
            }
        }
        
        
        // on vérifie s'il y a des positionnements définis pour les evenements de ce groupe d'adresse
        
        
        $sqlOrderBy = "ORDER BY he1.dateDebut,he1.idHistoriqueEvenement";
        $sqlLeftJoin = "";
        $sqlWhere = "";
        if($this->isPositionsDefiniesPourGroupeAdresse(array('idEvenementGroupeAdresse'=>$idEvenement)))
        {
            $sqlOrderBy =  " ORDER BY IF(pg.position IS NULL,0, pg.position) ASC "; // prend le pas sur le classement par defaut , donc on ne concatene pas
            $sqlLeftJoin .= " LEFT JOIN positionsEvenements pg ON pg.idEvenement = ee.idEvenementAssocie ";
            $sqlWhere .= " AND (pg.idEvenementGroupeAdresse = ee.idEvenement OR pg.idEvenementGroupeAdresse IS NULL) ";
        }
        
        
        
        // tableau contenant les evenements dont on ne veut pas qu'ils apparaissents dans le tableau de retour (par exemple pour la mise a jour des positions des evenements sous ajout d'un nouvel evenement) =>
        if(isset($params['notInIdEvenementArray']) && count($params['notInIdEvenementsArray'])>0) 
        {
            $sqlWhere.=" AND ee.idEvenementAssocie NOT IN (".implode(",",$params['notInIdEvenementsArray']).") ";
        }
        
        $tabEvenement=array();

        $sql = "
        
            SELECT distinct ee.idEvenementAssocie
            FROM _evenementEvenement ee
            LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
            LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
            $sqlLeftJoin
            WHERE ee.idEvenement = '".$idEvenement."'
            $sqlWhere
            $sqlEvenementsAdresses
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            $sqlOrderBy
        
        ";


        $res = $this->connexionBdd->requete($sql);
        
        while($fetch = mysql_fetch_assoc($res))
        {
            if(in_array($fetch['idEvenementAssocie'],$arrayEvenementsAdressesLiees))
                $tabEvenement[]=array("idEvenementAssocie"=>$fetch['idEvenementAssocie'],"isLieFromOtherAdresse"=>true);
            else
                $tabEvenement[]=array("idEvenementAssocie"=>$fetch['idEvenementAssocie'],"isLieFromOtherAdresse"=>false);
        }

        return $tabEvenement;
    }
    
    
    // fonction permettant de voir si une ou plusieurs positions ont été definies dans le classement manuel des evenements sur un groupe d'adresse
    public function isPositionsDefiniesPourGroupeAdresse($params = array())
    {
        $retour = false;
        if(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='')
        {
            $req = "SELECT 0 FROM positionsEvenements WHERE idEvenementGroupeAdresse='".$params['idEvenementGroupeAdresse']."' LIMIT 1";
            $res = $this->connexionBdd->requete($req);
            if(mysql_num_rows($res)>0)
            {
                $retour = true;
            }
        }
        return $retour;
    }

    public function getEvenementsParents($idEvenement=0)
    {
        $tabEvenement=array();
        
        $sql="
            SELECT DISTINCT idEvenement
            FROM _evenementEvenement _ee
            WHERE idEvenementAssocie = '".$idEvenement."'
        ";
        
        $res = $this->connexionBdd->requete($sql);
        
        while($fetch = mysql_fetch_assoc($res))
        {
            $tabEvenement[] = $fetch['idEvenement'];
        }
        
        return $tabEvenement;
    }
    
    public function afficherEvenementsLies($idEvenement=0)
    {
        /*
        **  EVENEMENT ASSOCIÉS
        */
        $html='';
        $t = new Template('modules/archi/templates/');
        
        $t->set_filenames((array('evl'=>'evenementsLies.tpl')));
        
        $sql = 'SELECT  hE.idEvenement, hE.idHistoriqueEvenement, hE.titre, hE.idTypeStructure, hE.idTypeEvenement, 
                tE.nom AS nomTypeEvenement, tS.nom AS nomTypeStructure FROM _evenementEvenement _eE 
            LEFT JOIN historiqueEvenement hE  ON hE.idEvenement = IF(_eE.idEvenement='.$idEvenement.', _eE.idEvenementAssocie, _eE.idEvenement) 
            LEFT JOIN historiqueEvenement hE2 ON hE2.idEvenement = IF(_eE.idEvenement='.$idEvenement.', _eE.idEvenementAssocie, _eE.idEvenement) 
            LEFT JOIN typeStructure tS    ON tS.idTypeStructure = hE.idTypeStructure 
            LEFT JOIN typeEvenement tE    ON tE.idTypeEvenement = hE.idTypeEvenement  
            WHERE _eE.idEvenement='.$idEvenement.' OR _eE.idEvenementAssocie='.$idEvenement.'
            GROUP BY hE.idEvenement,hE.idHistoriqueEvenement HAVING hE.idHistoriqueEvenement=MAX(hE2.idHistoriqueEvenement) ORDER BY hE.idHistoriqueEvenement ASC';
        $rep = $this->connexionBdd->requete($sql);
        
        if (mysql_num_rows($rep) > 0) {
            $t->assign_block_vars('evAsso', array());
        }
        else
        {
            $t->assign_vars(array('msgNoEvenement'=>"Il n'y a pas d'évenenement associé"));
        }

        while( $res = mysql_fetch_object($rep)) {
            $t->assign_block_vars('evAsso.associe', array(
                'url' => $this->creerUrl('', 'evenement', array('idEvenement' => $res->idEvenement)),
                'titre' => $res->titre,
                'typeStructure' => $res->nomTypeStructure,
                'typeEvenement' => $res->nomTypeEvenement));
        }
        
        ob_start();
        $t->pparse('evl');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    public function afficherEvenementsParents($idEvenement=0)
    {
        /*
        **  EVENEMENT ASSOCIÉS
        */
        $html='';
        $t = new Template('modules/archi/templates/');
        
        $t->set_filenames((array('evl'=>'evenementsLies.tpl')));
        
        $sql = "
            SELECT he.idEvenement as idEvenement, he.idHistoriqueEvenement as idHistoriqueEvenement,
            he.titre as titre, he.idTypeStructure as idTypeStructure, he.idTypeEvenement as idTypeEvenement, te.nom as nomTypeEvenement,
            ts.nom as nomTypeStructure
            FROM _evenementEvenement _ee,  historiqueEvenement he2, historiqueEvenement he
            LEFT JOIN typeStructure ts ON ts.idTypeStructure = he.idTypeStructure
            LEFT JOIN typeEvenement te ON te.idTypeEvenement = he.idTypeEvenement
            WHERE _ee.idEvenement = he.idEvenement
            AND he2.idEvenement = he.idEvenement
            AND _ee.idEvenementAssocie = '".$idEvenement."'
            GROUP BY he.idEvenement, he.idHistoriqueEvenement
            HAVING he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ORDER BY he.idHistoriqueEvenement ASC
        ";
        
        
        $rep = $this->connexionBdd->requete($sql);
        
        if (mysql_num_rows($rep) > 0) {
            $t->assign_block_vars('evAsso', array());
        }
        else
        {
            $t->assign_vars(array('msgNoEvenement'=>"Il n'y a pas d'evenement parent"));
        }

        $t->assign_block_vars('evAsso',array('typeEvenementAssocie' => 'parent'  ));
        
        while( $res = mysql_fetch_object($rep)) {
            $t->assign_block_vars('evAsso.associe', array(
                'url' => $this->creerUrl('', 'evenement', array('idEvenement' => $res->idEvenement)),
                'titre' => $res->titre,
                'typeStructure' => $res->nomTypeStructure,
                'typeEvenement' => $res->nomTypeEvenement));
        }
        
        ob_start();
        $t->pparse('evl');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    // resultat de la recherche d'evenements, renvoi une liste d'adresses
    public function afficherListe($criteres=array())
    {
        $html="";
        $evenement = new archiEvenement();
        $arrayListeEvenements= $evenement->getIdEvenementsFromRecherche($criteres);
        
        $arrayListeEvenements = array_unique($arrayListeEvenements);
        
        $adresse = new archiAdresse();
        
        $arrayListeGroupesEvenementsParents=array();
        foreach($arrayListeEvenements as $idEvenementFils)
        {
            //$resAdresse = $adresse->getAdressesFromEvenementGroupeAdresses($evenement->getParent($idEvenementFils));
            //$fetchAdresse = mysql_fetch_assoc($resAdresse);
            //$html .= "<a href=\"".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchAdresse['idAdresse']))."\">".stripslashes($adresse->getIntituleAdresse($fetchAdresse))."</a><br><br>";
            //$arrayListeEvenementsParents[] = $fetchAdresse['idAdresse'];
            $arrayListeGroupesEvenementsParents[] = $evenement->getParent($idEvenementFils);
        }
        
        $retour=$adresse->afficherListe(array('groupesAdressesSupplementairesExternes'=>$arrayListeGroupesEvenementsParents),'personnalite');
        $html.=$retour['html'];
        return $html;
    
    }
    // cette fonction renvoi les idEvenements d'une recherche avancee en fonction des criteres du formulaire de recherche avancee sur les evenements
    public function getIdEvenementsFromRecherche($criteres=array(), $templateName='listeEvenement.tpl', $modeAffichage = '')
    {
        $html = '';
        $sqlJoin = '';
        $tabParametresAutorises = array('ordre', 'tri', 'debut', 'selection', 'id');
        
        $arrayIdEvenements=array();
        
        foreach ($tabParametresAutorises AS $param) {
            if (isset($this->variablesGet[$param]) AND !isset($criteres[$param]))
                $criteres[$param] = $this->variablesGet[$param];
        }
        
        //
        // si c'est une recherche alors on ne cherche pas dans les type d'évènements étant des groupes d'adresses
        // !!!!! ATTENTION à l'ID du type d'évènement en cas de vidage et changement des ID
        // !!!!! ATTENTION à l'ID du type d'évènement en cas de vidage et changement des ID
        // !!!!! ATTENTION à l'ID du type d'évènement en cas de vidage et changement des ID
        // !!!!! ATTENTION à l'ID du type d'évènement en cas de vidage et changement des ID
        //$tabSqlWhere[] = 'hE.idTypeEvenement!=3';



        if (!empty($criteres['recherche_motcle']))
        {
            $tabMotCle = explode(' ',$criteres['recherche_motcle']);
            foreach ($tabMotCle AS $motcle)
            {
                $motcle = mysql_escape_string($motcle);
                $tabSqlWhere[]= "(hE.titre LIKE '%".$motcle."%' OR hE.description LIKE '%".$motcle."%')";
            }
        }

        if (!empty($criteres['recherche_courant']))
        {
            $courant = implode( "','", $criteres['recherche_courant']);
            $tabSqlWhere[] = '_eCA.idCourantArchitectural IN (\''.$courant.'\')';
            $tabSqlJoin[]  = 'LEFT JOIN _evenementCourantArchitectural _eCA USING (idEvenement) ';
        }

        if (!empty($criteres['recherche_typeStructure']))
        {
            $tabSqlWhere[] = 'hE.idTypeStructure='.$criteres['recherche_typeStructure'];
        }

        if (!empty($criteres['recherche_typeEvenement']))
        {
            $tabSqlWhere[] = 'hE.idTypeEvenement='.$criteres['recherche_typeEvenement'];
        }

        if (!empty($criteres['recherche_source']))
        {
            $tabSqlWhere[] = 'hE.idSource='.$criteres['recherche_source'];
        }

        if (!empty($criteres['recherche_personnes']))
        {
            $personnes = implode( "','", $criteres['recherche_personnes']);
            $tabSqlWhere[] = '_eP.idPersonne IN (\''.$personnes.'\')';
            $tabSqlJoin[]  = 'LEFT JOIN _evenementPersonne _eP USING (idEvenement)';
        }
        
        if(isset($criteres['recherche_anneeDebut']) && $criteres['recherche_anneeDebut']!='')
        {
            $tabSqlWhere[] = '(extract(YEAR FROM hE.dateDebut)>='.$criteres['recherche_anneeDebut'].')';
        }
        
        if(isset($criteres['recherche_anneeFin']) && $criteres['recherche_anneeDebut']!='')
        {
            $tabSqlWhere[] = '(extract(YEAR FROM hE.dateDebut)<='.$criteres['recherche_anneeFin'].')';
        }
        
        if(isset($criteres['recherche_MH']) && $criteres['recherche_MH']=='1' && isset($criteres['recherche_ISMH']) && $criteres['recherche_ISMH']=='1')
        {
            // ATTENTION, si les deux cases sont cochées , on fait un "OU" entre les deux champs pour le résultat de la recherche
            $tabSqlWhere[] = "(hE.MH='1' OR hE.ISMH='1')";
        }
        else
        {
            if(isset($criteres['recherche_MH']) && $criteres['recherche_MH']=='1')
            {
                $tabSqlWhere[] = "hE.MH='1'";
            }
            
            if(isset($criteres['recherche_ISMH']) && $criteres['recherche_ISMH']=='1')
            {
                $tabSqlWhere[] = "hE.ISMH='1'";
            }
        }

        
        

        if ( !isset( $criteres['ordre'] )) {
            $sqlOrdre = 'hE.titre';
        }
        else {
            switch( $criteres['ordre']) {
                case 'description': $sqlOrdre = 'hE.description';
                    break;
                case 'source':      $sqlOrdre = 's.nom';
                    break;
                case 'structure':   $sqlOrdre = 'tS.nom';
                    break;
                case 'titre':       $sqlOrdre = 'hE.titre';
                    break;
                case 'type':        $sqlOrdre = 'tE.nom';
                    break;
                case 'dateCreation':    $sqlOrdre = 'hE.dateCreationEvenement';
                    break;
                default:        $sqlOrdre = 'hE.titre';
            }
        }
        
        if ( !isset( $criteres['debut'] ) OR !is_numeric( $criteres['debut'] ) OR $criteres['debut'] < 1) 
        {
            $criteres['debut'] = 0;
            $sqlLimit = '0, 10';
            $valDebutSuivant = 0;
            $valDebutPrecedent = 0;
        }
        else 
        {
            $sqlLimit = $criteres['debut'].', 10';
            if ( $criteres['debut'] > 9 )
                $valDebutPrecedent = $criteres['debut'] -10;
            else
                $valDebutPrecedent = 0;
            $valDebutSuivant = $criteres['debut'];
        }
        
        if ( isset( $criteres['selection'] ) AND is_numeric( $criteres['id'] ))
        {
            switch( $criteres['selection']) {
                case 'typeStructure':   $tabSqlWhere[] = 'hE.idTypeStructure='.$criteres['id'];
                    break;
                case 'typeEvenement':   $tabSqlWhere[] = 'hE.idTypeEvenement='.$criteres['id'];
                    break;
                case 'source':      $tabSqlWhere[] = 'hE.idSource='.$criteres['id'];
                    break;
                case 'utilisateurAjout': $tabSqlWhere[] = 'hE.idUtilisateur='.$criteres['id'];
                    break;
                case 'personne':        $tabSqlWhere[] = '_eP.idPersonne='.$criteres['id'];
                            $tabSqlJoin[]  = 'LEFT JOIN _evenementPersonne _eP USING (idEvenement) ';
                    break;
                case 'courant':         $tabSqlWhere[] = '_eCA.idCourantArchitectural='.$criteres['id'];
                            $tabSqlJoin[]  = 'LEFT JOIN _evenementCourantArchitectural _eCA USING (idEvenement) ';
                    break;
            }
        }


        if (isset( $criteres['tri'])) {
            if ($criteres['tri'] == 'desc')
                $sqlTri = 'DESC';
            else
                $sqlTri = 'ASC';
        }
        else {
            $sqlTri = 'ASC';
        }
        
        $sqlWhere = '';
        if( isset($tabSqlWhere) && count($tabSqlWhere)>0)
        {
            $sqlWhere = implode(' AND ',$tabSqlWhere);
        }
        else
        {
            $sqlWhere ='1';
        }

        if( isset($tabSqlJoin))
        {
            foreach ($tabSqlJoin AS $val)
            {
                $sqlJoin .= ' '.$val;
            }
        }
        
        $sqlCount = 'SELECT hE.idEvenement, hE.idHistoriqueEvenement
            FROM historiqueEvenement hE2, historiqueEvenement hE 
            '.$sqlJoin.'
                        WHERE '. $sqlWhere .' AND hE.idEvenement = hE2.idEvenement
                        GROUP BY hE.idEvenement,hE.idHistoriqueEvenement HAVING hE.idHistoriqueEvenement=MAX(hE2.idHistoriqueEvenement) ORDER BY '.$sqlOrdre.' '.$sqlTri;

        $result = $this->connexionBdd->requete($sqlCount);
        
        if (( mysql_num_rows($result) - $valDebutSuivant ) > 10)
            $valDebutSuivant += 10;
        $nbReponses = mysql_num_rows($result);
        
        // requête
        $sql = 'SELECT hE.idEvenement, hE.idHistoriqueEvenement
            FROM historiqueEvenement hE2, historiqueEvenement hE
                        LEFT JOIN source s ON hE.idSource = s.idSource  
                        LEFT JOIN typeStructure tS ON hE.idTypeStructure = tS.idTypeStructure  
                        LEFT JOIN typeEvenement tE ON hE.idTypeEvenement = tE.idTypeEvenement  
            '.$sqlJoin.'
                        WHERE '. $sqlWhere .' AND hE.idEvenement = hE2.idEvenement
                        GROUP BY hE.idEvenement,hE.idHistoriqueEvenement HAVING hE.idHistoriqueEvenement=MAX(hE2.idHistoriqueEvenement) ORDER BY '.$sqlOrdre.' '.$sqlTri;//.' LIMIT '.$sqlLimit.' ' // modif laurent , pas de limite dans cette requetes sinon on n'affichera pas tous les resultats
        //hE.titre, hE.idSource, hE.idTypeStructure, hE.idTypeEvenement, hE.description AS description, s.nom as nomSource, hE.dateCreationEvenement, // modif laurent pour accelerer la recherche
            //tS.nom as nomTypeStructure, tE.nom as nomTypeEvenement 
        
        
        $result  = $this->connexionBdd->requete($sql);
        
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('ev'=>$templateName)));
        
        $t->assign_vars(array('ajouterEvenement' => $this->creerUrl('', 'ajouterEvenement')));
        
        
        if (mysql_num_rows($result) > 0) 
        {
            // **********************************************************************
            // boucle sur les resultats de la requete
            // **********************************************************************
            while ( $rep = mysql_fetch_object( $result )) 
            {
                $arrayIdEvenements[]=$rep->idEvenement;

            }
        }
        
        return $arrayIdEvenements;
    }

    public function afficherFromAdresse($idAdresse=0)
    {
            
        /*
        **  EVENEMENT ASSOCIÉS
        */
        $html='';

        $result = $this->connexionBdd->requete('SELECT idEvenement FROM _adresseEvenement WHERE idAdresse='.$idAdresse);
        if (mysql_num_rows($result) > 0)
        {
            while($res = mysql_fetch_object($result))
            {
                
                $html .= '<div style="margin:5px 0px;">'.$this->afficher( $res->idEvenement).'</div>';
            }
        }
        
        return $html;
    }
    
    // *******************************************************************************************************************************************************
    // affiche le formulaire d'ajout d'un evenement
    // *******************************************************************************************************************************************************
    public function afficheFormulaire($tabTravail = array(), $modif='', $idParent=0, $typeParentId='')
    {
        $html = '';
        $t = new Template('modules/archi/templates/');
        //$t->set_filenames((array('evl'=>'evenementFormulaire.tpl')));
        $t->set_filenames((array('evl'=>'nouveauDossier.tpl')));
        $isEvenementGroupeAdresse = false;
        
        $formulaire = new formGenerator();
        
        $groupeTypeEvenement=2; // par defaut on selectionne les evenement de type 'travaux'
        if(isset($this->variablesPost['typeGroupeEvenement']) && $this->variablesPost['typeGroupeEvenement']!='')
            $groupeTypeEvenement = $this->variablesPost['typeGroupeEvenement'];


        $t->assign_vars(array('typeBoutonValidation'=>"button")); // quand test javascript sur les champs de l'adresse , type = button 

            
        // cas d'ajout d'un sous evenement ou d'un evenement appartenant a une adresse
        // on n'affiche pas les listes d'evenements et d'adresses
        if($idParent<>0)
        {   
            // est ce que l'on veut lier l'evenement a un evenement que l'on a précisé en parametre ou a une adresse ?
            switch($typeParentId)
            {
                case 'evenement':
                    // CAS OU L'ON VA AJOUTER UN SOUS EVENEMENT A L'EVENEMENT GROUPE d'ADRESSE
                    
                    $t->assign_block_vars('isNotAjoutNouvelleAdresse',array());
                    if ($idPerson=archiPersonne::isPerson($idParent)) {
                        $person= new archiPersonne();
                        $infos=$person->getInfosPersonne($idPerson);
                        $t->assign_vars(array('recapitulatifAdresse'=>"<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>"));
                    } else {
                        $adresse = new archiAdresse();
                        $t->assign_vars(array('recapitulatifAdresse'=>$adresse->afficherRecapitulatifAdresses($idParent)));
                    }
                    $t->assign_block_vars('afficheAjoutEvenement',array());
                    //$t->assign_block_vars('isNotAffichageGroupeAdresse',array());
                    $t->assign_vars(array('evenementGroupeAdresse'=>$idParent));
                    $t->assign_vars(array('nomBoutonValidation'=>'ajoutSousEvenement'));
                    $t->assign_vars(array('typeBoutonValidation'=>"submit"));
                break;
                case 'adresse':
                    $tabTravail['adresses']['value']=array($idParent);
                    $t->assign_block_vars('ajouterAdresses'  , array());
                break;
            }
        } else {
            // l'evenement est il un groupe d'adresse , si oui , on n'affichera que le champ des adresses
            if($modif != '')
            {
                // c'est une modification d'evenement
                
                // on affiche le recapitulatif de l'adresse en haut de page
                $t->assign_block_vars('isNotAjoutNouvelleAdresse',array());

                
                $idEvenementGroupeAdresse = $this->getParent($modif);
                
                
                $adresse = new archiAdresse();
                
                if ($idPerson=archiPersonne::isPerson($idEvenementGroupeAdresse)) {
                    $person= new archiPersonne();
                    $infos=$person->getInfosPersonne($idPerson);
                    $t->assign_vars(array('recapitulatifAdresse'=>"<h2 class='h1'><a href='".$this->creerUrl('', '', array('archiAffichage'=>'evenementListe', 'selection'=>"personne", 'id'=>$idPerson))."'>".$infos["prenom"]." ".$infos["nom"]."</a></h2>"));
                } else {
                    $t->assign_vars(array('recapitulatifAdresse'=>$adresse->afficherRecapitulatifAdresses($idEvenementGroupeAdresse)));
                }
                
                $t->assign_vars(array('recaptitulatifAncres'=>$this->afficherRecapitulatifAncres($idEvenementGroupeAdresse,$modif)));
                
                $t->assign_vars(array("liensModifEvenements"=>$this->afficherLiensModificationEvenement($modif)));          
                
                
                $isEvenementGroupeAdresse = $this->isEvenementGroupeAdresse($modif);
                if($isEvenementGroupeAdresse)
                {
                    // l'evenement courant est de type groupe d'adresse
                    // on affiche rien a par l'adresse

                    // on renseigne le champs cache du formulaire pour recuperer rapidement l'identifiant lors de la validation de la modification
                    $t->assign_vars(array('evenementGroupeAdresse'=>$modif));
                    
                    $t->assign_block_vars('ajouterAdresses'  , array());
                    
                    //on recupere les adresses liées a cet evenement
                    $reqAdressesGroupeAdresses = "
                                                    SELECT ha.idAdresse as idAdresse
                                                    from historiqueAdresse ha2, historiqueAdresse ha
                                                    right join _adresseEvenement ae on ae.idAdresse = ha.idAdresse
                                                    where ae.idEvenement = '".$modif."'
                                                    and ha2.idAdresse = ha.idAdresse
                                                    group by ha.idAdresse,ha.idHistoriqueAdresse
                                                    having ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)                                                    
                                                ";
                    $resAdressesGroupeAdresses = $this->connexionBdd->requete($reqAdressesGroupeAdresses);
                    while($fetchAdressesGroupeAdresses = mysql_fetch_assoc($resAdressesGroupeAdresses))
                    {
                        $tabTravail["adresses"]['value'][] = $fetchAdressesGroupeAdresses['idAdresse'];
                    }                   
                } else {
                    // MODIFICATION d'UN EVENEMENT (juste l'evenement, pas d'adresse , pas de groupe d'adresse)
                    // initialisation des champs du template que l'on va afficher
                    $t->assign_block_vars('afficheAjoutEvenement',array());
                    $t->assign_vars(array('nomBoutonValidation'=>'evenementSimple'));   
                    $t->assign_vars(array('typeBoutonValidation'=>"submit"));                   
                }
            }
            else
            {
                // ce n'est pas une modification d'evenement
                $t->assign_block_vars('afficheAjoutEvenement',array());
                
            }
        }
        
        
        //***********************************************************************************************
        // TYPE STRUCTURE
        // dans le cas d'un ajout de sous evenement
        // par defaut on recupere le type de structure du premier evenement enregistré 'qui n'est pas un groupe d'adresses'
        //***********************************************************************************************
        $heriteIdTypeStructure='';
        if($idParent<>'0' && $typeParentId=='evenement')
        {
            
            $sqlTypeStructureHerite = "
                SELECT he.idTypeStructure as idTypeStructure
                FROM historiqueEvenement he2, historiqueEvenement he
                WHERE he.idEvenement =(select min(ee.idEvenementAssocie) from _evenementEvenement ee where ee.idEvenement = '".$idParent."')
                AND he2.idEvenement = he.idEvenement
                GROUP BY he.idEvenement,he.idHistoriqueEvenement
                HAVING he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)                
                ";
            
            $resTypeStructureHerite = $this->connexionBdd->requete($sqlTypeStructureHerite);
            
            if (mysql_num_rows($resTypeStructureHerite)==1) {
                $fetchTypeStructureHerite = mysql_fetch_assoc($resTypeStructureHerite);         
                $heriteIdTypeStructure = $fetchTypeStructureHerite['idTypeStructure'];
            }
            //echo "heriteIdTypeStructure=".$heriteIdTypeStructure;
        }
        if (!isset($idEvenementGroupeAdresse)) {
            $idEvenementGroupeAdresse=$_GET["archiIdEvenement"];
        }
        
        if (!archiPersonne::isPerson($idEvenementGroupeAdresse)) {
            $t->assign_block_vars("afficheAjoutEvenement.isAddress", array());
            $sqlTypeStructure = 'SELECT idTypeStructure, nom FROM typeStructure order by nom';
            if ($result = $this->connexionBdd->requete($sqlTypeStructure))
            {
                while ($fetchTypeStructure = mysql_fetch_object($result))
                {
                    if ($fetchTypeStructure->idTypeStructure !=0) {
                        //$tabTypeStructure[$rep->idTypeStructure] = $rep->nom;
                        if ((isset( $tabTravail['typeStructure']) && $tabTravail['typeStructure']['value'] == $fetchTypeStructure->idTypeStructure) || ( $heriteIdTypeStructure!='' && $fetchTypeStructure->idTypeStructure == $heriteIdTypeStructure ))
                            $selected = 'selected="selected"';
                        else
                            $selected = '';
                        $t->assign_block_vars(
                            'afficheAjoutEvenement.isAddress.typesStructure', array(
                                'id'=> $fetchTypeStructure->idTypeStructure, 
                                'nom'=> $fetchTypeStructure->nom, 
                                'selected'=> $selected
                            )
                        );
                    }
                }
            }
        }
        
        // ***********************************************************************************
        // si la personne n'est pas admin elle verra une version simplifiée du formulaire
        $authentification = new archiAuthentification();
        if($authentification->estConnecte() && $authentification->estAdmin())
        {
            $t->assign_block_vars('afficheAjoutEvenement.isAdmin',array());
        }
        else
        {
            $t->assign_block_vars('afficheAjoutEvenement.isNotAdmin',array());
        }
        // ******
        // ***********************************************************************************
        // autre cas pour l'affichage du champ numeroArchive , il faut que l'utilisateur soit autorisé à l'afficher => table utilisateur
        $utilisateur = new archiUtilisateur();
        if($utilisateur->canChangeNumeroArchiveField(array('idUtilisateur'=>$authentification->getIdUtilisateur())))
        {
            $t->assign_block_vars('afficheAjoutEvenement.canChangeNumeroArchive',array());
        }
        else
        {
            $t->assign_block_vars('afficheAjoutEvenement.noChangeNumeroArchive',array());
        }
        if($utilisateur->canChangeDateFinField(array('idUtilisateur'=>$authentification->getIdUtilisateur())))
        {
            $t->assign_block_vars('afficheAjoutEvenement.canChangeDateFin',array());
        }
        else
        {
            $t->assign_block_vars('afficheAjoutEvenement.noChangeDateFin',array());
        }
        
        
        
        
        if($utilisateur->isAuthorized('affiche_selection_source',$authentification->getIdUtilisateur()))
        {
            $t->assign_block_vars('afficheAjoutEvenement.isDisplaySource',array());
        }
        else
        {
            $t->assign_block_vars('afficheAjoutEvenement.isNotDisplaySource',array());
        }
        
        
        
        // ******
        // ***********************************************************************************
        // le type de groupe d'evenement
        // 1 - culturel
        // 2 - travaux
        
        $t->assign_vars(array('onClickTypeEvenement1'=>"appelAjax('".$this->creerUrl('','afficheSelectTypeEvenement',array('noHeaderNoFooter'=>1,'archiTypeGroupeEvenement'=>'1'))."','typeEvenement');document.getElementById('afficheChampsSupplementairesCulturel').style.display='block';document.getElementById('afficheChampsSupplementairesTravaux').style.display='none';"));
        $t->assign_vars(array('onClickTypeEvenement2'=>"appelAjax('".$this->creerUrl('','afficheSelectTypeEvenement',array('noHeaderNoFooter'=>1,'archiTypeGroupeEvenement'=>'2'))."','typeEvenement');document.getElementById('afficheChampsSupplementairesTravaux').style.display='block';document.getElementById('afficheChampsSupplementairesCulturel').style.display='none';"));
        
        // si c'est une modification de l'evenement il faut chercher l'id de type de groupe d'evenement afin d'afficher les bons div , et donc on reassigne la variable groupeTypeEvenement
        if($modif!='')
        {
            if(isset($tabTravail['typeEvenement']['value']))
            {
                $reqGroupeTypeEvenement = "select groupe from typeEvenement where idTypeEvenement='".$tabTravail['typeEvenement']['value']."'";
                $resGroupeTypeEvenement = $this->connexionBdd->requete($reqGroupeTypeEvenement);
                $fetchGroupeTypeEvenement = mysql_fetch_assoc($resGroupeTypeEvenement);
                $groupeTypeEvenement = $fetchGroupeTypeEvenement['groupe'];
            }
        }

        // on affiche le bon div
        if($groupeTypeEvenement==1) // 1: culturel
        {
            $t->assign_vars(array('checkedTypeEvenement1'=>" checked"));
            $t->assign_vars(array('styleChampsSupplementaireTravaux'=>"display:none;"));
            $t->assign_vars(array('styleChampsSupplementaireCulturel'=>"display:block;"));
            
            if(isset($tabTravail['ISMH']) && $tabTravail['ISMH']['value']=='1')
            {
                $t->assign_vars(array('ISMHchecked'=>" checked"));
            }
            
            if(isset($tabTravail['MH']) && $tabTravail['MH']['value']=='1')
            {
                $t->assign_vars(array('MHchecked'=>" checked"));
            }
        }
        elseif($groupeTypeEvenement==2) // 2: travaux
        {
            $t->assign_vars(array('nbEtages'=>$tabTravail['nbEtages']['value']));
            $t->assign_vars(array('checkedTypeEvenement2'=>" checked"));
            $t->assign_vars(array('styleChampsSupplementaireTravaux'=>"display:block;"));
            $t->assign_vars(array('styleChampsSupplementaireCulturel'=>"display:none;"));
        }
        
        

        if(isset($tabTravail['isDateDebutEnviron']) && $tabTravail['isDateDebutEnviron']['value']=='1')
        {
            $t->assign_vars(array('isDateDebutEnviron'=>" checked"));
        }
        
        if (!archiPersonne::isPerson($idEvenementGroupeAdresse)) {
            // les type d'evenements
            // par defaut on selectionne le typeEvenement=2 (travaux)
            $resTypeEvenement = $this->connexionBdd->requete("SELECT idTypeEvenement,nom FROM typeEvenement where groupe = '".$groupeTypeEvenement."'");
            while($fetchTypeEvenement = mysql_fetch_assoc($resTypeEvenement))
            {
                $selected="";
                if (isset( $tabTravail['typeEvenement']) && $tabTravail['typeEvenement']['value'] == $fetchTypeEvenement["idTypeEvenement"])
                {
                    $selected = "selected";
                }

                $t->assign_block_vars('afficheAjoutEvenement.isAddress.typesEvenement',array(
                                                            'id'=>$fetchTypeEvenement['idTypeEvenement'],
                                                            'nom'=>$fetchTypeEvenement['nom'],
                                                            'selected'=>$selected
                                                    ));
            }
        }
        
        // ***********************************************************************************
        //** Courant Architecturaux - récupération
        //*
        $sqlCourantArchitectural = 'SELECT idCourantArchitectural, nom FROM courantArchitectural order by nom';
        //$tabCourantArchitectural = array();
        $tableauCourants = new tableau();
        if ($result = $this->connexionBdd->requete($sqlCourantArchitectural))
        {
            while ($fetchCourant = mysql_fetch_object($result))
            {
                //$tabCourantArchitectural[$rep->idCourantArchitectural] = $rep->nom;
                if (isset( $tabTravail['courant']) && is_array($tabTravail['courant']['value']) && in_array($fetchCourant->idCourantArchitectural, $tabTravail['courant']['value']) && $groupeTypeEvenement==2)
                    $selected = 'checked';
                else
                    $selected = '';
                
                $tableauCourants->addValue("<input type='checkbox' name='courant[]' value='".$fetchCourant->idCourantArchitectural."' ".$selected.">&nbsp;".$fetchCourant->nom);
                
                //$t->assign_block_vars('isNotAffichageGroupeAdresse.courant', array('id'=> $fetchCourant->idCourantArchitectural, 'nom'=> $fetchCourant->nom, 'selected'=> $selected));
            }
            
            $t->assign_vars(array('listeCourantsArchitecturaux'=>$tableauCourants->createHtmlTableFromArray(3,"white-space:nowrap;font-size:12px; font-color:#000000;",'listeCourantsArchitecturaux')));
        }
        
        // ***********************************************************************************
        // assignation du bouton de validation : ajout ou modif
        if ( $formulaire->estChiffre($modif))
        {
            $t->assign_vars(array(
                'typeTitre' => 'Modification',
                'estmodif'  => '<input type="hidden" name="idEvenement" value="'.$modif.'" />',
                'boutonValidation' => 'Modifier',
                'formAction' => $this->creerUrl('modifierEvenement', '', array('archiIdEvenement'=>$modif))
                ));
        }
        else
        {
            $t->assign_vars(array(
                'typeTitre' => 'Ajout',
                'estmodif'  => '',
                'boutonValidation'=>'Ajouter',
                'formAction' => $this->creerUrl('ajoutEvenement')
                ));
        }
        
        // ***********************************************************************************
        //**  Affichage des Erreurs
        //**

        foreach($tabTravail as $name => $value)
        {
            if(!is_array($value["value"]))
            {
                if($value['type']=='date')
                {
                    $val = $this->date->toFrench($this->date->toBdd($value['value']));
                }
                else
                    $val = htmlspecialchars(stripslashes($value["value"])); // nécessaire pour la description
            }
            else
            {
                    $val = $value["value"];
            }
            
            if($value['type']!='checkbox')
            {
                $t->assign_vars(array( $name => $val));
            }
            
            if(isset($value['error']) && $value["error"]!='')
            {
                $t->assign_vars(array($name."-error" => $value["error"]));
            }
        }

        
        
        // ***********************************************************************************
        //**  Affichage des listes d'options
        //**

        // recherche des intitules de la source de l'evenement courant
        if(!empty($tabTravail['source']['value']))
        {
            $sql = "
            SELECT s.idSource as idSource, s.nom as nom , ts.nom as nomTypeSource
            FROM source s
            LEFT JOIN typeSource ts ON ts.idTypeSource = s.idTypeSource
            WHERE s.idSource ='".$tabTravail['source']['value']."'";
            
            $rep = $this->connexionBdd->requete($sql);
            $fetchSource = mysql_fetch_assoc($rep);

            $t->assign_vars(array('source'=> $tabTravail['source']['value'], 'sourcetxt'=>stripslashes($fetchSource['nom']).' '.$fetchSource['nomTypeSource']));
        }
        

    
        // les personnes
        $d = new droitsObject();
        
        if($d->isAuthorized('personne_sur_evenement_modifier',$authentification->getIdProfil()))
        {
            $t->assign_vars(array("affichePersonnesBlock"=>"table-row"));
            
            if(!empty($tabTravail['personnes']['value']))
            {
                foreach($tabTravail['personnes']['value'] AS $id => $val)
                {
                    $tabTravail['personnes']['value'][$id] = mysql_escape_string($val);
                }
                $sqlIdPersonne = implode("','", $tabTravail['personnes']['value']);

                $sql = "SELECT idPersonne, nom, prenom FROM personne WHERE idPersonne IN ('".$sqlIdPersonne."')";
                
                $rep = $this->connexionBdd->requete($sql);
                $tabPersonne=array();
                while ($res = mysql_fetch_object($rep))
                {
                    $tabPersonne[$res->idPersonne] = $res->nom.' '.$res->prenom;
                }

                foreach($tabPersonne AS $id => $val)
                {
                
                    $t->assign_block_vars('afficheAjoutEvenement.personnes', array('id'=> $id, 'nom'=>$val,'selected'=>'selected'));
                }
            }
        }
        else
        {
            $t->assign_vars(array("affichePersonnesBlock"=>"none"));
        }
        // les adresses 
        /*if (!empty($tabTravail['adresses']['value']))
        {
            foreach( $tabTravail['adresses']['value'] AS $id => $val)
            {
                $tabTravail['adresses']['value'][$id] = mysql_escape_string($val);
            }
            $sqlIdAdresse = implode("','", $tabTravail['adresses']['value']);
            // recuperation des noms des adresses
            $sql = "
                SELECT ha.idAdresse as idAdresse, ha.nom as nom
                FROM historiqueAdresse ha2, historiqueAdresse ha
                WHERE ha.idAdresse IN ('".$sqlIdAdresse."')
                AND ha.idAdresse = ha2.idAdresse
                GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            ";
            $rep = $this->connexionBdd->requete($sql);
            while ($res = mysql_fetch_object($rep))
            {
                $tabAdresse[$res->idAdresse] = $res->nom;
            }
            
            $adresse = new archiAdresse();
            foreach($tabTravail['adresses']['value'] AS $id)
            {
                // recherche du libelle a afficher pour l'adresse
                $nom = $adresse->getAdresseToDisplay($adresse->getArrayAdresseFromIdAdresse($id));//$adresse->getAdresseToDisplay($id);
                $t->assign_block_vars('ajouterAdresses.adresses', array('val'=> $id, 'nom'=> $nom));
            }
        }
        
        // les evenements lies ( pas utilisé pour le moment)
        if (!empty($tabTravail['evenements']['value']))
        {
            // modif laurent pour ajout d'un sous evenement à l'evenement groupe d'adresse
            if(count($tabTravail['evenements']['value'])==1)
            {
                foreach($tabTravail['evenements']['value'] as $indice => $value)
                {
                    // recherche de l'evenement parent :
                    $resGroupeAdresse   = $this->connexionBdd->requete("select distinct idEvenement from _evenementEvenement where idEvenementAssocie='".$value."'");
                    $fetchGroupeAdresse = mysql_fetch_assoc($resGroupeAdresse);
                    $t->assign_vars(array('evenementGroupeAdresse'=>$fetchGroupeAdresse['idEvenement']));
                }
            }
            else
            {
                $this->mail->sendMail('archiV2',$this->mail->getAdmin(),'ArchiV2-archiEvenements::il y a plusieurs evenements lies','il y a plusieurs evenements lies idEvenement='.$fetchGroupeAdresse['idEvenement']);
            }
            

        }
        */
        
        
        
        // ***********************************************************************************
        // gestion des appels des popups de dates
        $t->assign_vars(array(      'onClickDateDebut'              =>"document.getElementById('paramChampAppelantDate').value='dateDebut';document.getElementById('calqueDate').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDate').style.display='block';",
                                    
                                    'onClickDateFin'                =>"document.getElementById('paramChampAppelantDate').value='dateFin';document.getElementById('calqueDate').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDate').style.display='block';"
                        ));
        
        
        
        // bouton popup source
        $t->assign_vars(array(
                        'onClickBoutonChoisirSource'=>"document.getElementById('paramChampsAppelantSource').value='source';document.getElementById('calqueSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueSource').style.display='block';",
                        'onClickChoixPersonne'=>"document.getElementById('paramChampsAppelantPersonne').value='personne';document.getElementById('calquePersonne').style.top=(getScrollHeight()+150)+'px';document.getElementById('calquePersonne').style.display='block';"
                        ));
        
        
        $recherche = new archiRecherche();
        //  liaison avec les adresses
        $t->assign_vars(array(
            'popupPersonnes'  => $recherche->getPopupChoixPersonne('modifEvenement'),
            'popupSources'     => $recherche->getPopupChoixSource('modifEvenement'),
            'popupCalendrier'      => $this->getPopupCalendrier()
            ));

        
        // ******************************************************************************************************************************
        // on recupere les messages d'aide contextuelle et on les affiche : 
        $helpMessages = $this->getHelpMessages("helpEvenement");
        
        foreach($helpMessages as $fieldName => $helpMessage)
        {
            $t->assign_vars(array($fieldName=>$helpMessage));
        }
        // ******************************************************************************************************************************
        
        ob_start();
        $t->pparse('evl');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    
    
    // ******************************************************************************************************************************************************************************
    public function afficherHistorique($idEvenement)
    {
        //
        // affiche la liste des modifications faites sur un évènement
        //
        $html = '';
        $formulaire = new formGenerator();
        $string = new stringObject();
        if ($formulaire->estChiffre($idEvenement))
        {
            $sqlHistorique = 'SELECT titre, description, dateCreationEvenement, idHistoriqueEvenement FROM historiqueEvenement WHERE idEvenement='.$idEvenement.' ORDER BY dateCreationEvenement DESC';
            if ($rep = $this->connexionBdd->requete($sqlHistorique))
            {
                $t = new Template('modules/archi/templates');
                $t->set_filenames(array('historiqueEvenement'=>'historiqueEvenement.tpl'));
                while($res = mysql_fetch_object($rep))
                {
                    $t->assign_block_vars('historique', array(
                        'titre'      => htmlspecialchars($res->titre), 
                        'description'=> pia_substr($string->sansBalises($res->description), 0, 50), 
                        'url'        => $this->creerUrl('', 'historiqueEvenementDetail', array('idHistoriqueEvenement'=>$res->idHistoriqueEvenement)),
                        'dateModif'  => $this->date->toFrench($res->dateCreationEvenement)));
                }
                ob_start();
                $t->pparse('historiqueEvenement');
                $html .= ob_get_contents();
                ob_end_clean();
            }
        }
        else
        {
            $html .= 'erreur chiffre';
        }

        return $html;
    }
    
    public function afficherHistoriqueDetail($idHistoriqueEvenement)
    {
        //
        // affiche un historique évènement
        //

        $html = $this->afficher('','', $idHistoriqueEvenement);
        return $html;
    }

    // ************************************************************************************************************************************************
    // recherche un nouvel id pour un nouvel idEvenement
    // ************************************************************************************************************************************************
    public function getNewIdEvenement()
    {
        $res = $this->connexionBdd->requete('SELECT (MAX(idEvenement)+1) AS idEvenement FROM historiqueEvenement');
        $rep = mysql_fetch_object($res);
        $idEvenement = $rep->idEvenement;
        
        // debut si aucun enregistrement $idEvenement = NULL, donc on le redéfini
        if (!isset($idEvenement))
            $idEvenement = 1;
        
        return $idEvenement;
    }
    
    // ************************************************************************************************************************************************
    // renvoie l'evenement parent de l'evenement passé en parametre
    // ************************************************************************************************************************************************
    public function getParent($idEvenement=0)
    {
        $retour = 0;
        

        // l'evenement n'est pas un groupe d'adresse donc on cherche son parent de type groupe d'adresse            
        $req = "select distinct idEvenement from _evenementEvenement where idEvenementAssocie = '".$idEvenement."'";
        $res = $this->connexionBdd->requete($req);
        if(mysql_num_rows($res)==1)
        {
            $fetch = mysql_fetch_assoc($res);
            $retour = $fetch['idEvenement'];
        }
        
        return $retour;
    }
    
    // ************************************************************************************************************************************************
    // fonction qui renvoi un booleen indiquant si l'evenement est un groupe d'adresse ou non
    // ************************************************************************************************************************************************
    public function isEvenementGroupeAdresse($idEvenement=0)
    {
        $retour = false;
        $req = " 
                select he.idHistoriqueEvenement
                from historiqueEvenement he2, historiqueEvenement he
                left join typeEvenement te on te.idTypeEvenement = he.idTypeEvenement
                where he2.idEvenement = he.idEvenement
                and he.idEvenement = '".$idEvenement."'
                and he.idTypeEvenement = '".$this->getIdTypeEvenementGroupeAdresse()."'
                group by he.idEvenement, he.idHistoriqueEvenement
                having he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
        ";
        $res = $this->connexionBdd->requete($req);
        
        if(mysql_num_rows($res)==1)
        {
            $retour = true;
        }
        return $retour;
    }

    // ***************************************************************************************************************************************************
    // fonction effectuant une recherche par mot cle sur les evenements et renvoyant les idAdresse des evenements concernés, et renvoi aussi le titre de l'evenement concerné pour l'affichage dans la recherche
    // ***************************************************************************************************************************************************
    public function getIdAdressesFromRecherche($criteres)
    {
        $tabIdAdresses=array();
        $tabIdEvenementsGroupeAdresse=array();
        
        if(isset($criteres['recherche_motcle']) && $criteres['recherche_motcle']!='')
        {
            
            $tabMotCle = explode(' ',$criteres['recherche_motcle']);
            
            $sqlWhere='';
            $sqlWherePersonne="";
            
            for($i=0 ; $i <count($tabMotCle) ; $i++)
            {
                $motcle = mysql_escape_string($tabMotCle[$i]);
                
                $tabSqlWhere[]= "  (he.titre LIKE '%".$motcle."%' OR he.description LIKE '%".$motcle."%')";
                
                $sqlWherePersonne .=" AND (p.nom LIKE '%".$motcle."%' OR p.prenom LIKE '%".$motcle."%') ";
                
            }
            
            if(count($tabSqlWhere)>0)
                $sqlWhere = "AND (".implode(" AND ",$tabSqlWhere);

            $reqPersonne = "
            
                        SELECT p.nom as nom,p.prenom as prenom, p.idPersonne , ep.idEvenement as idEvenement
                        FROM personne p
                        RIGHT JOIN _evenementPersonne ep ON ep.idPersonne = p.idPersonne
                        WHERE 1 ".$sqlWherePersonne;

            $resPersonne = $this->connexionBdd->requete($reqPersonne);
            $tabIdEvenementPersonne=array();
            while($fetchPersonne = mysql_fetch_assoc($resPersonne))
            {
                $tabIdEvenementPersonne[] = $fetchPersonne['idEvenement'];
            }
            
            $listeIdEvenementPersonne="";
            if(count($tabIdEvenementPersonne)>0)
            {
                $listeIdEvenementPersonne = " OR he.idEvenement in ('".implode("','",$tabIdEvenementPersonne)."') ";
                
                $sqlWhere .=$listeIdEvenementPersonne.")";
                
            }
            else
            {
                $sqlWhere .=")";
            }
                
            
            $req = "
                    SELECT distinct ae.idAdresse as idAdresse,ee.idEvenement as idEvenementGroupeAdresse
                    FROM historiqueEvenement he2, historiqueEvenement he
                    RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he.idEvenement
                    RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                    WHERE he2.idEvenement = he.idEvenement
                    ".$sqlWhere."
                    GROUP BY he.idEvenement, he.idHistoriqueEvenement
                    HAVING he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                    ";
            
            $res = $this->connexionBdd->requete($req);
            
            while($fetch = mysql_fetch_assoc($res))
            {
                $tabIdAdresses[]=$fetch['idAdresse'];
                $tabIdEvenementsGroupeAdresse[]=$fetch['idEvenementGroupeAdresse'];
            }
        }
        
        $tabIdAdresses=array_unique($tabIdAdresses);
        $tabIdEvenementsGroupeAdresse=array_unique($tabIdEvenementsGroupeAdresse);
        
        return array('idAdresses'=>$tabIdAdresses,'idEvenementsGroupeAdresse'=>$tabIdEvenementsGroupeAdresse);
    }
    
    // ************************************************************************************************************************************************
    // fonction permettant d'afficher deux evenements l'un a coté de l'autre pour pouvoir effectuer une comparaison
    // ************************************************************************************************************************************************
    public function afficheComparateur($criteres=array())
    {
        $html="";
        $t = new Template('modules/archi/templates/');
        //$t->set_filenames((array('evl'=>'evenementFormulaire.tpl')));
        $t->set_filenames((array('comparaison'=>'comparaisonEvenements.tpl')));
        
        $s = new stringObject();
        
        // *****************************************************************************************************************
        // recuperation des parametres  , idHistoriqueEvenementAncien et idHistoriqueEvenementNouveau
        $idHistoriqueEvenementAncien =0;
        $idHistoriqueEvenementNouveau=0;
        
        // post
        if(isset($this->variablesGet['idHistoriqueEvenementAncien']) && $this->variablesGet['idHistoriqueEvenementAncien']!='' && $this->variablesGet['idHistoriqueEvenementAncien']!='0')
            $idHistoriqueEvenementAncien = $this->variablesGet['idHistoriqueEvenementAncien'];
        
        if(isset($this->variablesGet['idHistoriqueEvenementNouveau']) && $this->variablesGet['idHistoriqueEvenementNouveau']!='' && $this->variablesGet['idHistoriqueEvenementNouveau']!='0')
            $idHistoriqueEvenementNouveau = $this->variablesGet['idHistoriqueEvenementNouveau'];

        // ou criteres
        if(isset($criteres['idHistoriqueEvenementAncien']) && $criteres['idHistoriqueEvenementAncien']!='' && $criteres['idHistoriqueEvenementAncien']!='0')
            $idHistoriqueEvenementAncien = $criteres['idHistoriqueEvenementAncien'];
        
        if(isset($criteres['idHistoriqueEvenementNouveau']) && $criteres['idHistoriqueEvenementNouveau']!='' && $criteres['idHistoriqueEvenementNouveau']!='0')
            $idHistoriqueEvenementNouveau = $criteres['idHistoriqueEvenementNouveau'];
        
        // *****************************************************************************************************************
        
        // affichage de la page courante ( donc avec le nouvel idHistoriqueEvenement ) 
        $evenement = new archiEvenement();
        
        // on recupere l'idEvenement
        $query = "select idEvenement from historiqueEvenement where idHistoriqueEvenement = '".$idHistoriqueEvenementNouveau."'";
        $res = $this->connexionBdd->requete($query);
        $fetch = mysql_fetch_assoc($res);
        
        $retourCourant="";
        if(isset($fetch['idEvenement']))
        {
            $retourCourant = $this->afficher($fetch['idEvenement']);


            $retourNouveau = $evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementNouveau);
            
            $retourAncien = $evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementAncien);
            
            // en dessous nous affichons le tableau de comparaison
            $t->assign_vars(array(
                                'evenementCourant'=>$retourCourant['html'],
                                'versionAvant'=>$retourAncien,
                                'versionMaintenant'=>$retourNouveau,
                                'lienVersionPrecedente'=>"location.href='".$this->creerUrl('supprimerHistoriqueEvenement','',array('archiIdHistoriqueEvenement'=>$idHistoriqueEvenementNouveau))."';"
                                ));
            
            $html.="<b>Différences dans la description : </b><br>";
            $arrayDiffHtml = $s->getTexteDifferences(array('nouveau'=>stripslashes($evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementNouveau,array('returnDescriptionOnly'=>true))),'ancien'=>stripslashes($evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementAncien,array('returnDescriptionOnly'=>true)))));
            $html.=$arrayDiffHtml['html'];
            //$html.= $s->getTxtDiffByPEAR(array('nouveau'=>stripslashes($evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementNouveau,array('returnDescriptionOnly'=>true))),'ancien'=>stripslashes($evenement->afficherFromIdHistoriqueEvenement($idHistoriqueEvenementAncien,array('returnDescriptionOnly'=>true)))));
            // => ok mais a mettre en forme
            
            
            
            ob_start();
            $t->pparse('comparaison');
            $html .= ob_get_contents();
            ob_end_clean();
        
        }
        else
        {
            $erreurObject = new objetErreur();
            $erreurObject->ajouter("L'évènement ne peut pas être affiché. Celui-ci a été effacé.");
            $html.=$erreurObject->afficher();
            
            
        }
        
        return $html;
    }
    
    public function afficherFromIdHistoriqueEvenement($idHistoriqueEvenement,$params = array())
    {
        $html="";

    
        $query = "select * from historiqueEvenement where idHistoriqueEvenement = '".$idHistoriqueEvenement."'";
        $res = $this->connexionBdd->requete($query);
        $fetch = mysql_fetch_assoc($res);
        if(isset($params['returnDescriptionOnly']) && $params['returnDescriptionOnly']==true)
        {
            $html = $fetch['description'];
        }
        else
        {
            $t = new Template('modules/archi/templates/');
        
            $t->set_filenames((array('comparaison2'=>'comparaisonAffichageEvenement.tpl')));
            
            $ISMH = "NON";
            if($fetch['ISMH']=='1')
            {   
                $ISMH = "OUI";
            }
            
            $MH = "NON";
            if($fetch['MH']=='1')
            {
                $HM = "OUI";
            }
            
            $t->assign_vars(array(
                                    'dateDebut'=>$fetch['dateDebut'],
                                    'dateFin'=>$fetch['dateFin'],
                                    'description'=>stripslashes($fetch['description']),
                                    'titre'=>stripslashes($fetch['titre']),
                                    'nbEtages'=>$fetch['nbEtages'],
                                    'ISMH'=>$ISMH,
                                    'MH'=>$MH
            ));
            
        
            ob_start();
            $t->pparse('comparaison2');
            $html .= ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }

    // **********************************************************************************************************
    // fonction permettant l'affichage des ancres a plusieurs endroits
    // **********************************************************************************************************
    public function afficherRecapitulatifAncres($idEvenementGroupeAdresse=0,$idEvenementCourant=0)          
    {
        $html="";
        $t = new Template('modules/archi/templates/');
        $t->set_filenames((array('recapitulatifAncres'=>'recapitulatifAncres.tpl')));

        $resEvenementsLies=$this->getInfosEvenementsLiesForAncres($idEvenementGroupeAdresse);
        
        while($fetch = mysql_fetch_assoc($resEvenementsLies))
        {
            $couleur = "#000000";
            
            if($fetch['idEvenement'] == $idEvenementCourant)
            {
                $couleur = "#FF0000";
            }
            
            
            // le type de structure est le meme que l'evenement precedent , on ne l'affiche pas, c'est pour cela qu'on le transmet a la fonction d'affichage
            
            $titreAncre = _("Sans titre");
            if(trim($fetch['titre'])!='')
            {
                $titreAncre = $fetch['titre'];
            }
            
            if($fetch['nomTypeEvenement']!='')
            {
                $titreAncre.=" - ".$fetch['nomTypeEvenement'];
            }
            if($fetch['date']!='' && $fetch['date']!='0000-00-00')
            {
                $titreAncre.=" - ".$this->date->toFrench($fetch['date']);
            }
            
            $t->assign_block_vars('ancres',array(
                                                'titre' => stripslashes($titreAncre),
                                                'couleur'=>$couleur
                                ));
        }
        
        ob_start();
        $t->pparse('recapitulatifAncres');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    // ********************************************************************************************************
    // renvoi les informations pour les titres sur les ancres
    // ********************************************************************************************************
    public function getInfosEvenementsLiesForAncres($idEvenementGroupeAdresse = 0)
    {
        $sql = "
            SELECT DISTINCT ee.idEvenementAssocie as idEvenement, he1.dateCreationEvenement as dateCreationEvenement, he1.titre as titre, he1.dateDebut as date, te.nom as nomTypeEvenement
            FROM historiqueEvenement he2, historiqueEvenement he1
            LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
            LEFT JOIN typeEvenement te ON te.idTypeEvenement = he1.idTypeEvenement
            LEFT JOIN positionsEvenements ON positionsEvenements.idEvenement = he1.idEvenement
            WHERE he2.idEvenement = he1.idEvenement
            AND ee.idEvenement = '".$idEvenementGroupeAdresse."'
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ORDER BY positionsEvenements.position
        ";
        
        $res = $this->connexionBdd->requete($sql);
    
        return $res;
    }
    
    // ********************************************************************************************************
    // afficher des liens de modification d'un evenement (ajout modif image , modif evenement)
    // ********************************************************************************************************
    public function afficherLiensModificationEvenement($idEvenement=0)
    {
        $html="";
        $t = new Template('modules/archi/templates/');
        $t->set_filenames((array('liensOngletsEvenement'=>'liensOngletsEvenement.tpl')));
        
        $couleur = "";
        
        $liens = array( "Ajouter des images" => array('url'=>$this->creerUrl('','ajoutImageEvenement',array('archiIdEvenement'=>$idEvenement)),'archiAffichage'=>'ajoutImageEvenement'),
                        "Modifier les images"=>array('url'=>$this->creerUrl('','modifierImageEvenement',array('archiIdEvenement'=>$idEvenement)),'archiAffichage'=>'modifierImageEvenement'),
                        "Position des images"=>array('url'=>$this->creerUrl('','modifierPositionsImages',array('archiIdEvenement'=>$idEvenement)),'archiAffichage'=>'modifierPositionsImages'),
                        "Modifier l'évènement"=>array('url'=>$this->creerUrl('','modifierEvenement',array('archiIdEvenement'=>$idEvenement)),'archiAffichage'=>'modifierEvenement')
                        
        );
        
        foreach($liens as $titre => $elementsLiens)
        {
            $couleur = "";
            if(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']==$elementsLiens['archiAffichage'])
            {
                $couleur = "current";
            }
            $t->assign_block_vars("liens",array("couleur"=>$couleur,"titre"=>$titre,"url"=>$elementsLiens['url']));
        }
        
        ob_start();
        $t->pparse('liensOngletsEvenement');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    // ********************************************************************************************************
    // recupere le nombre d'evenements liés à un groupe d'adresses
    // ********************************************************************************************************
    public function getNbEvenementsFromGroupeAdresse($idEvenementGroupeAdresse=0)
    {
        // on supprime le groupe d'adresse s'il est vide
        $reqIsGroupeAdresseVide = "SELECT * FROM _evenementEvenement WHERE idEvenement = '".$idEvenementGroupeAdresse."'";
        $resIsGroupeAdresseVide = $this->connexionBdd->requete($reqIsGroupeAdresseVide);
        
        return mysql_num_rows($resIsGroupeAdresseVide);
    }

    public function getDescription($idEvenement=0)
    {
        $req = "
            SELECT he1.description as description
            FROM historiqueEvenement he1, historiqueEvenement he2
            WHERE he2.idEvenement = he1.idEvenement
            AND he1.idEvenement = '".$idEvenement."'
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ";
        
        $res = $this->connexionBdd->requete($req);
        
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch['description'];
    }

    function getTitle()
    {
        $req = "
            SELECT he1.titre as title
            FROM historiqueEvenement he1, historiqueEvenement he2
            WHERE he2.idEvenement = he1.idEvenement
            AND he1.idEvenement = ".mysql_real_escape_string($this->idEvenement)."
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ";
        
        $res = $this->connexionBdd->requete($req);
        
        $fetch = mysql_fetch_assoc($res);

        if (empty($fetch['title'])) {
            $req = "
            SELECT he1.idTypeEvenement as type,
            he1.dateDebut as date
            FROM historiqueEvenement he1, historiqueEvenement he2
            WHERE he2.idEvenement = he1.idEvenement
            AND he1.idEvenement = ".mysql_real_escape_string($this->idEvenement)."
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ";
            $res = $this->connexionBdd->requete($req);
            $event = mysql_fetch_assoc($res);
            
            $req = "
            SELECT nom as name
            FROM typeEvenement
            WHERE idTypeEvenement = ".mysql_real_escape_string($event['type'])."
            ";
            $res = $this->connexionBdd->requete($req);
            $type = mysql_fetch_assoc($res);
            
            if (substr($event['date'], 5)=="00-00"){
                $event['date']=substr($event['date'], 0, 4);
            } 
            
            return $type['name'].' ('.$event['date'].')';
        } else {
            return $fetch['title'];
        }
    }

    // fonction permettant de recuperer l'idEvenementGroupeAdresse a partir d'un evenement
    // avec verification si l'idEvenement entré en parametre n'est pas un groupe d'adresse , et dans ce cas , on renvoi le parametre en entree
    public function getIdEvenementGroupeAdresseFromIdEvenement($idEvenement=0)
    {
        $retour = 0;
        
        if($this->isEvenementGroupeAdresse($idEvenement))
        {
            $retour = $idEvenement;
        }
        else
        {
            $req = "SELECT idEvenement FROM _evenementEvenement WHERE idEvenementAssocie = '".$idEvenement."' LIMIT 1";
            $res = $this->connexionBdd->requete($req);
            if(mysql_num_rows($res)>0)
            {
                $fetch = mysql_fetch_assoc($res);
                $retour = $fetch['idEvenement'];
            }
        }

        return $retour;
    }
    
    // on prend un evenement existant et on creer un nouveau groupe d'adresse lié a la meme adresse
    public function deplacerEvenementVersMemeAdresseNouveauGA($params=array())
    {
        
        $idEvenement = 0;
        
        if(isset($params['idEvenement']))
        {
            $idEvenement = $params['idEvenement'];
        }
        
        if(isset($this->variablesGet['idEvenement']))
        {
            $idEvenement = $this->variablesGet['idEvenement'];
        }
        
        $authentification = new archiAuthentification();
        $idUtilisateur   = $authentification->getIdUtilisateur();
        
        // recuperation des id adresses de l'evenement
        $reqAdresses = "
            SELECT distinct ae.idAdresse as idAdresse,ae.idEvenement as idEvenement
            FROM _adresseEvenement ae
            LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$idEvenement."'
            WHERE ae.idEvenement = ee.idEvenement
        ";
        $resAdresses = $this->connexionBdd->requete($reqAdresses);
        $arrayIdAdresses = array();
        $arrayIdGroupeAdressesPrecedent = array();
        $i=0;
        while($fetchAdresses = mysql_fetch_assoc($resAdresses))
        {
            $arrayIdGroupeAdressesPrecedent[] = $fetchAdresses['idEvenement'];
            $arrayIdAdresses[] = $fetchAdresses['idAdresse'];
            $i++;
        }
        
        // creation nouveau groupe d'adresses
        $arrayIdGroupeAdressesPrecedent = array_unique($arrayIdGroupeAdressesPrecedent);
        if(count($arrayIdGroupeAdressesPrecedent)==1 && $arrayIdGroupeAdressesPrecedent[0]!='' && $arrayIdGroupeAdressesPrecedent[0]!='0')
        {
            $idNouveauGroupeAdresse = $this->getNewIdEvenement();
            
            // creation de l'evenement parent groupe d'adresse
            $sqlGA = "INSERT INTO historiqueEvenement (idEvenement, titre, description, dateDebut, dateFin, idSource, idUtilisateur, idTypeStructure, idTypeEvenement,dateCreationEvenement)
                    VALUES (".$idNouveauGroupeAdresse.", '', '', '', '', 0, ".$idUtilisateur.", 0, ".$this->getIdTypeEvenementGroupeAdresse().",now())";
            $this->connexionBdd->requete($sqlGA);
            
            // association entre l'evenement et le groupe d'adresse
            $sqlEvenementEvenement = "INSERT INTO _evenementEvenement (idEvenement,idEvenementAssocie) VALUES ('".$idNouveauGroupeAdresse."','".$idEvenement."')";
            $resEvenementEvenement = $this->connexionBdd->requete($sqlEvenementEvenement);
            
            // suppression de l'ancienne liaison avec l'ancien groupe d'adresse
            $sqlSupprEvenementEvenement = "DELETE FROM _evenementEvenement WHERE idEvenement = '".$arrayIdGroupeAdressesPrecedent[0]."' AND idEvenementAssocie='".$idEvenement."'";
            $resSupprEvenementEvenement = $this->connexionBdd->requete($sqlSupprEvenementEvenement);
            // ajout de la liaison entre les adresses et le nouveau groupe d'adresse
            foreach($arrayIdAdresses as $indice => $value)
            {
                $reqAdresseGroupeAdresse = "INSERT INTO _adresseEvenement (idAdresse, idEvenement) VALUES ('".$value."','".$idNouveauGroupeAdresse."') ";
                $resAdresseGroupeAdresse = $this->connexionBdd->requete($reqAdresseGroupeAdresse);
            }
            
            // est ce que le groupe d'adresse precedent est vide ?
            $reqVerifGAPrecedent = "SELECT idEvenement FROM _adresseEvenement WHERE idEvenement='".$arrayIdGroupeAdressesPrecedent[0]."' AND idEvenement NOT IN (SELECT idEvenement FROM _evenementEvenement)";
            $resVerifGAPrecedent = $this->connexionBdd->requete($reqVerifGAPrecedent);
            if(mysql_num_rows($resVerifGAPrecedent)>0)
            {
                $reqSupprLiaison = "DELETE FROM _adresseEvenement WHERE idEvenement='".$arrayIdGroupeAdressesPrecedent[0]."'";
                $resSupprLiaison = $this->connexionBdd->requete($reqSupprLiaison);
            
            }
        }
        else
        {
            $e = new objetErreur();
            $e->ajouter("Il y a plusieurs groupes d'adresses pour le meme evenement, veuillez contacter l'administrateur.");
            echo $e->afficher();
        }
    }

    // on deplace un evenement vers un groupe d'adresse selectionné dans la popup de recherche d'adresse avec le mode d'affichage 'popupDeplacerEvenementVersGroupeAdresse'
    public function deplacerEvenementVersGroupeAdresse($params = array())
    {
        $idEvenementADeplacer = 0;
        if(isset($this->variablesGet['idEvenementADeplacer']))
        {
            $idEvenementADeplacer = $this->variablesGet['idEvenementADeplacer'];
        }
        
        if(isset($params['idEvenementADeplacer']))
        {
            $idEvenementADeplacer = $params['idEvenementADeplacer'];
        }
        
        $deplacerVersIdGroupeAdresse = 0;
        if(isset($this->variablesGet['deplacerVersIdGroupeAdresse']))
        {
            $deplacerVersIdGroupeAdresse = $this->variablesGet['deplacerVersIdGroupeAdresse'];
        }
        
        if(isset($params['deplacerVersIdGroupeAdresse']))
        {
            $deplacerVersIdGroupeAdresse = $params['deplacerVersIdGroupeAdresse'];
        }
        
        if($idEvenementADeplacer!='0' && $idEvenementADeplacer!='' && $deplacerVersIdGroupeAdresse!='0' && $deplacerVersIdGroupeAdresse!='')
        {
            
            // recuperation de l'idEvenementGroupeAdresse precedent
            $reqGAPrecedent = "SELECT idEvenement FROM _evenementEvenement WHERE idEvenementAssocie = '".$idEvenementADeplacer."'";
            $resGAPrecedent = $this->connexionBdd->requete($reqGAPrecedent);
            $arrayIdEvenementGAPrecedent=array();
            while($fetchGAPrecedent = mysql_fetch_assoc($resGAPrecedent))
            {
                $arrayIdEvenementGAPrecedent[] = $fetchGAPrecedent['idEvenement'];
            }
            
            if(count($arrayIdEvenementGAPrecedent)==1 && $arrayIdEvenementGAPrecedent[0]!='' && $arrayIdEvenementGAPrecedent[0]!='0')
            {
                // on supprime la liaison précédente avec le groupe d'adresse précédent
                $reqSupprEvenementEvenement = "DELETE FROM _evenementEvenement WHERE idEvenement = '".$arrayIdEvenementGAPrecedent[0]."' AND idEvenementAssocie='".$idEvenementADeplacer."'";
                $resSupprEvenementEvenement = $this->connexionBdd->requete($reqSupprEvenementEvenement);
                
                // il faut alors rafraichir les positions des evenements du groupe d'adresse, pour qu'elles se suivent bien
                $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$arrayIdEvenementGAPrecedent[0],'refreshAfterDelete'=>true));
                
                
                
                // on ajoute la liaison de l'evenement et du nouveau groupe d'adresse dans la table _evenementEvenement
                $reqEvenementEvenement = "INSERT INTO _evenementEvenement (idEvenement,idEvenementAssocie) VALUES ('".$deplacerVersIdGroupeAdresse."','".$idEvenementADeplacer."')";
                
                $resEvenementEvenement = $this->connexionBdd->requete($reqEvenementEvenement);
                
                // et on rafraichit les positions des evenements du groupe d'adresses de destination avec le nouvel evenement ajouté
                $this->majPositionsEvenements(array('idEvenementGroupeAdresse'=>$deplacerVersIdGroupeAdresse,'idNouvelEvenement'=>$idEvenementADeplacer));
                
                // est ce que le groupe d'adresse précédent est vide ?
                $reqLiaisonAdresseEvenement = "SELECT idEvenement FROM _adresseEvenement WHERE idEvenement='".$arrayIdEvenementGAPrecedent[0]."' AND idEvenement NOT IN (SELECT idEvenement FROM _evenementEvenement)";
                $resLiaisonAdresseEvenement = $this->connexionBdd->requete($reqLiaisonAdresseEvenement);
                if(mysql_num_rows($resLiaisonAdresseEvenement)>0)
                {
                    $reqSupprLiaison = "DELETE FROM _adresseEvenement WHERE idEvenement='".$arrayIdEvenementGAPrecedent[0]."'";
                    $resSupprLiaison = $this->connexionBdd->requete($reqSupprLiaison);
                    // pour les positions, s'il n'y avait plus d'evenement, les positions on d'abord ete supprimé dans la fonction de rafraichissement
                }
                
                
            }
            else
            {
                $e = new objetErreur();
                $e->ajouter(_("Evenement impossible à deplacer, veuillez contacter l'administrateur"));
                echo $e->afficher();
            }
        }
    }
    
    
    // enregistrement de l'image principale selectionnee pour un groupe d'adresse
    public function enregistreSelectionImagePrincipale($params = array())
    {
        $authentification = new archiAuthentification();
        if($authentification->estConnecte() && isset($this->variablesGet['idEvenement']) && $this->variablesGet['idEvenement']!='' && isset($this->variablesGet['idImage']) && $this->variablesGet['idImage']!='')
        {
            $idEvenementGroupeAdresse = $this->getIdEvenementGroupeAdresseFromIdEvenement($this->variablesGet['idEvenement']);
            
            // recherche de l'historiqueEvenement de l'evenement groupe adresse
            $reqHistorique = "
                    SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement
                    FROM historiqueEvenement he2, historiqueEvenement he1
                    WHERE he1.idEvenement = '".$idEvenementGroupeAdresse."'
                    AND he2.idEvenement = he1.idEvenement
                    GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                    HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ";
            
            $resHistorique = $this->connexionBdd->requete($reqHistorique);
            
            if(mysql_num_rows($resHistorique)==1)
            {
                $fetchHistorique = mysql_fetch_assoc($resHistorique);
                
                $idHistoriqueEvenement = $fetchHistorique['idHistoriqueEvenement'];
                
                $reqUpdate = "UPDATE historiqueEvenement SET idImagePrincipale='".$this->variablesGet['idImage']."' WHERE idHistoriqueEvenement='".$idHistoriqueEvenement."'";
                $resUpdate = $this->connexionBdd->requete($reqUpdate);
                echo "Image selectionnée<br>";
            }
            else
            {
                echo "Erreur de recupération de groupe d'adresse, merci de contacter l'administrateur<br>";
            }
        }
    }
    
    
    // enregistrement des positionnements d'evenement dans un groupe d'adresse
    // dans la table : positionsEvenements
    public function enregistrerPositionnementEvenements($params = array())
    {
        
        if(isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='' && isset($this->variablesPost['listeDragAndDrop']) && $this->variablesPost['listeDragAndDrop']!='')
        {
        
            // suppression des valeurs de positions precedentes
            $reqDelete = "DELETE FROM positionsEvenements WHERE idEvenementGroupeAdresse = '".$this->variablesGet['archiIdEvenementGroupeAdresse']."'";
            $resDelete = $this->connexionBdd->requete($reqDelete);
            
            
            // ajout des nouvelles valeurs
            $arrayEvenementsOrdonnes = explode("|",$this->variablesPost['listeDragAndDrop']);
            
            $position = 1;
            foreach($arrayEvenementsOrdonnes as $indice => $idEvenement)
            {
                $reqInsert = "INSERT INTO positionsEvenements (idEvenementGroupeAdresse,idEvenement,position) VALUES ('".$this->variablesGet['archiIdEvenementGroupeAdresse']."','".$idEvenement."','".$position."') ";
                $resInsert = $this->connexionBdd->requete($reqInsert);
                
                
                $position++;
            }
    
        }
    }
    
    // met a jour la  position des evenements d'une adresse ou a ete ajouté un nouvel evenement
    // on va donc trouver la position du nouvel evenement et renvoyer les nouvelles positions de tous les evenements pour les mettres toutes a jour
    public function majPositionsEvenements($params = array())
    {
        $retour = true;
        $tabTravail = array();
        
        if(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && !isset($params['idNouvelEvenement']) && isset($params['refreshAfterDelete']) && $params['refreshAfterDelete']==true)
        {
            // on met a jour les positions apres la suppression d'un evenement sur le groupe d'adresse par exemple
            // donc en fait on ne fait qu'un rafraichissement des positions
            // si l'evenement supprimé etait le dernier du groupe d'adresse , le passage par cette fonction va permettre la suppression des liaisons dans la table positionEvenement , en theorie du dernier evenement qui restait , voir d'autres s'il y a eu un probleme (en principe cela n'arrive pas)
            $tabIdEvenementsLies=$this->getEvenementsLies($params['idEvenementGroupeAdresse'],true);

            // on a recuperer le tableau des positions enregistrées, on peut donc supprimer les positions precedentes, 
            $reqDelete = "DELETE FROM positionsEvenements WHERE idEvenementGroupeAdresse = '".$params['idEvenementGroupeAdresse']."'";
            $resDelete = $this->connexionBdd->requete($reqDelete);
            
            // et inserer les nouvelles positions
            $position = 1;
            foreach($tabIdEvenementsLies as $indice => $value)
            {
                $reqPositions = "INSERT INTO positionsEvenements (idEvenementGroupeAdresse, idEvenement, position) VALUES ('".$params['idEvenementGroupeAdresse']."','".$value['idEvenementAssocie']."','".$position."') ";
                
                $resPositions = $this->connexionBdd->requete($reqPositions);
                
                $position++;
            }
        
        }
        elseif(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && ((isset($params['idNouvelEvenement']) && $params['idNouvelEvenement']!='') || (isset($params['idEvenementModifie']) &&  $params['idEvenementModifie']!='')))
        {
            $effectueMaj = false;
            // dans le cas d'un evenement modifie , on ne fait de mise a jour que si la date de l'evenement a ete changée par rapport a son dernier historique      
            // 
            
            if(isset($params['idNouvelEvenement']) && $params['idNouvelEvenement']!='')
            {
                $idEvenementConcerne = $params['idNouvelEvenement'];
                $effectueMaj = true;
            }
            elseif(isset($params['idEvenementModifie']) &&  $params['idEvenementModifie']!='')
            {
                // verification du dernier historique
                $idEvenementConcerne = $params['idEvenementModifie'];
                
                
                
                $reqDernierHistoriqueEvenementModifie = "
                    
                    SELECT he1.idHistoriqueEvenement as idHistoriqueEvenement, he1.dateDebut as dateDebutDernierHistorique
                    FROM historiqueEvenement he2, historiqueEvenement he1
                    WHERE he1.idEvenement = he2.idEvenement
                    AND he1.idEvenement = '".$idEvenementConcerne."'
                    GROUP BY he1.idEvenement , he1.idHistoriqueEvenement
                    HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                ";
                
                $resDernierHistoriqueEvenementModifie = $this->connexionBdd->requete($reqDernierHistoriqueEvenementModifie);
                
                if(mysql_num_rows($resDernierHistoriqueEvenementModifie)==1)
                {
                    $fetchDernierHistoriqueEvenementModifie = mysql_fetch_assoc($resDernierHistoriqueEvenementModifie);
                    
                    if(isset($fetchDernierHistoriqueEvenementModifie['idHistoriqueEvenement']) && $fetchDernierHistoriqueEvenementModifie['idHistoriqueEvenement']!='')
                    {
                        $reqVerifChangementDate = "
                        
                            SELECT he1.dateDebut as dateDebutAvantDernier
                            FROM historiqueEvenement he2, historiqueEvenement he1
                            WHERE he1.idEvenement = he2.idEvenement
                            AND he1.idEvenement='".$idEvenementConcerne."'
                            AND he1.idHistoriqueEvenement<>'".$fetchDernierHistoriqueEvenementModifie['idHistoriqueEvenement']."'
                            AND he2.idHistoriqueEvenement<>'".$fetchDernierHistoriqueEvenementModifie['idHistoriqueEvenement']."'
                            GROUP BY he1.idEvenement , he1.idHistoriqueEvenement
                            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                        
                        ";
                        
                        $resVerifChangementDate = $this->connexionBdd->requete($reqVerifChangementDate);
                        
                        
                        if(mysql_num_rows($resVerifChangementDate)>0)
                        {
                            $fetchVerifChangementDate = mysql_fetch_assoc($resVerifChangementDate);
                            
                            if($fetchVerifChangementDate['dateDebutAvantDernier']!=$fetchDernierHistoriqueEvenementModifie['dateDebutDernierHistorique'])
                            {
                                // si dernier historique , date différente
                                $effectueMaj = true; // la date a ete changé, on met a jour les positions
                            }
                            else
                            {
                                // si dernier historique date identique
                                $effectueMaj = false; // pas la peine de mettre a jour les positions
                            }
                        }
                    }
                }
            }
            
            if($effectueMaj)
            {
                // mise a jour apres ajout d'un nouvel evenement sur le groupe d'adresses
                $d = new dateObject();
                
                $tabIdEvenementsLies=$this->getEvenementsLies($params['idEvenementGroupeAdresse'],true);
                
                
                $position = 1;
                $dateNouvelEvenement = "0000-00-00";
                foreach($tabIdEvenementsLies as $indice => $value)
                {
                    if($value['idEvenementAssocie']!=$idEvenementConcerne)
                    {
                        $tabTravail[$position]['idEvenement'] = $value['idEvenementAssocie'];

                        // recuperation des dates des evenements
                        $reqDates = "
                            SELECT h1.idEvenement as idEvenement, h1.dateDebut as dateDebut
                            FROM historiqueEvenement h2, historiqueEvenement h1
                            
                            WHERE h2.idEvenement = h1.idEvenement
                            AND h1.idEvenement = '".$value['idEvenementAssocie']."'
                            GROUP BY h1.idEvenement, h1.idHistoriqueEvenement
                            HAVING h1.idHistoriqueEvenement = max(h2.idHistoriqueEvenement)
                            ";
                        
                        $resDates = $this->connexionBdd->requete($reqDates);
                        
                        
                        $tabTravail[$position]['dateDebut'] = "0000-00-00";
                        if(mysql_num_rows($resDates)>0)
                        {
                            $fetchDates = mysql_fetch_assoc($resDates);
                            
                            $tabTravail[$position]['dateDebut'] = $fetchDates['dateDebut'];
                        }
                        
                        
                        $position++;
                    }
                    else
                    {
                        $fetchDates = "";
                        $reqDates = "
                            SELECT h1.idEvenement as idEvenement, h1.dateDebut as dateDebut
                            FROM historiqueEvenement h2, historiqueEvenement h1
                            
                            WHERE h2.idEvenement = h1.idEvenement
                            AND h1.idEvenement = '".$value['idEvenementAssocie']."'
                            GROUP BY h1.idEvenement, h1.idHistoriqueEvenement
                            HAVING h1.idHistoriqueEvenement = max(h2.idHistoriqueEvenement)
                            ";
                        
                        $resDates = $this->connexionBdd->requete($reqDates);
                        
                        $fetchDates = mysql_fetch_assoc($resDates);
                            
                        $dateNouvelEvenement = $fetchDates['dateDebut'];
                        if(pia_substr($dateNouvelEvenement,4,6)=='-00-00')
                            $dateNouvelEvenement = pia_substr($dateNouvelEvenement,0,4)."-01-01";

                    }
                }
                

                // parcours des dates pour voir ou on va inserer l'evenement
                //echo "dateNouvelEvenement = ".$dateNouvelEvenement."<br>";
                $positionNouvelEvenement =1;
                if($dateNouvelEvenement!='0000-00-00')
                {
                    foreach($tabTravail as $position => $valueEvenement)
                    {
                        // on transforme la date en date valide et analysable par la fonction de comparaison de dates
                        if(pia_substr($tabTravail[$position]['dateDebut'],4,6)=='-00-00')
                        {
                            $tabTravail[$position]['dateDebut'] = pia_substr($tabTravail[$position]['dateDebut'],0,4)."-01-01";
                        }
                        //echo "dateDebut = ".pia_substr($tabTravail[$position]['dateDebut'],4,6)."    ".pia_substr($tabTravail[$position]['dateDebut'],0,4)."      ".$tabTravail[$position]['dateDebut']."<br>";
                        if($tabTravail[$position]['dateDebut']!='0000-00-00' && $d->isGreaterThan($dateNouvelEvenement,$tabTravail[$position]['dateDebut']))
                        {
                            $positionNouvelEvenement = $position+1;
                        }
                        elseif($tabTravail[$position]['dateDebut']=='0000-00-00')
                        {
                            $positionNouvelEvenement = $position+1;
                        }

                    }
                }
                else
                {
                    $positionNouvelEvenement=1;
                }
                
                
                
                //echo "positionNouvelEvenement = ".$positionNouvelEvenement."<br>";

                // insertion dans un tableau trié
                $nouveauTableau = array();
                $nouvellesPositions = 1;
                $decalageApresInsertion=0;
                


                foreach($tabTravail as $position => $valueEvenement)
                {
                    if($position == $positionNouvelEvenement)
                    {
                        $decalageApresInsertion=1;
                        $nouveauTableau[$position]['idEvenement'] = $idEvenementConcerne;
                    }

                    $nouveauTableau[$position+$decalageApresInsertion]['idEvenement'] = $valueEvenement['idEvenement'];
                }
                
                if($positionNouvelEvenement>count($tabTravail)) // si le nouvel evenement se place en derniere position (le nouvel element n'est donc pas encore dans tabTravail , on le rajoute a la fin du tableau
                {
                    $nouveauTableau[$positionNouvelEvenement]['idEvenement'] = $idEvenementConcerne;
                }
                


                // mise a jour de la table positionEvenements
                // suppr des valeurs du groupe d'adresse precedentes
                $reqDelete = "DELETE FROM positionsEvenements WHERE idEvenementGroupeAdresse = '".$params['idEvenementGroupeAdresse']."'";
                $resDelete = $this->connexionBdd->requete($reqDelete);
                
                foreach($nouveauTableau as $positionFinale => $value)
                {
                    $reqPositions = "INSERT INTO positionsEvenements (idEvenementGroupeAdresse, idEvenement, position) VALUES ('".$params['idEvenementGroupeAdresse']."','".$value['idEvenement']."','".$positionFinale."') ";
                    
                    $resPositions = $this->connexionBdd->requete($reqPositions);
                }
            
            }
            
        }
        return $retour;
        
    }
    
    
    
    
    
}

function array_diff_assoc_recursive($array1, $array2)
{
    foreach($array1 as $key => $value)
    {
        if(is_array($value))
        {
              if(!isset($array2[$key]))
              {
                  $difference[$key] = $value;
              }
              elseif(!is_array($array2[$key]))
              {
                  $difference[$key] = $value;
              }
              else
              {
                  $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                  if($new_diff != FALSE)
                  {
                        $difference[$key] = $new_diff;
                  }
              }
          }
          elseif(!isset($array2[$key]) || $array2[$key] != $value)
          {
              $difference[$key] = $value;
          }
    }
    return !isset($difference) ? 0 : $difference;
}
?>
