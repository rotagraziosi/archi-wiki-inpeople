<?php

class archiUtilisateur extends config {
    private $nom;
    private $prenom;
    private $idUtilisateur;
    private $estAdmin;
    private $alertMail;

    function __construct() {
        parent::__construct();
    }

    private function ajouter() 
    {
    }

    // **********************************************************************************************************************************************
    // champs que l'on recupere si l'utilisateur courant n'est pas un admin
    // **********************************************************************************************************************************************
    public function getUtilisateurFieldsNotAdmin()
    {
        return array(
            'nom'         =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'prenom'        =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mail' =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mdp1' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp2'), 
            'mdp2' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp1'), 
            'ville' =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'alerteMail'       =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteCommentaires' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteAdresses' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'urlSiteWeb'=>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'afficheFormulaireContactPersoProfilPublic'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio')
        );
    
    }
    
    // **********************************************************************************************************************************************
    // champs que l'on recupere si l'utilisateur courant est moderateur
    // **********************************************************************************************************************************************
    public function getUtilisateurFieldsForModerators()
    {
        return array(
            'nom'         =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'prenom'        =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mail' =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mdp1' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp2'), 
            'mdp2' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp1'), 
            'ville' =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'alerteMail'       =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteCommentaires' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteAdresses' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'idPeriodeEnvoiMailsRegroupes'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'simpleList'), 
            'urlSiteWeb'=>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'afficheFormulaireContactPersoProfilPublic'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio')
        );
    
    }
    
    // **********************************************************************************************************************************************
    // si l'utilisateur courant est un admin,  on recupere les champs 'estAdmin' et 'alertMail' en plus ( ce que ne peut pas modifier un simple utilisateur connecté)
    // **********************************************************************************************************************************************
    public function getUtilisateurFieldsForAdmin()
    {
        return array(
            'nom'         =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'prenom'        =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mail' =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'idProfil'      =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'simpleList'), 
            'alerteMail'       =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteCommentaires' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteAdresses' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'mdp1' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp2'), 
            'mdp2' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp1'), 
            'ville' =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'idPeriodeEnvoiMailsRegroupes'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'simpleList'), 
            'urlSiteWeb'=>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'afficheFormulaireContactPersoProfilPublic'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'displayNumeroArchiveField'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'displayDateFinField'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'bannirUtilisateur'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'canCopyright'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'canModifyTags'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'),
            'canAddWithoutStreet'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio')
        );
    
    }
    
    // **********************************************************************************************************************************************
    // si l'utilisateur courant est un admin et qu'il modifie sont propre profil ,  ou celui d'un autre ,  le champs 'compteBanni' est en plus 
    // **********************************************************************************************************************************************
    public function getUtilisateurFieldsForAdminProfil()
    {
        return array(
            'nom'         =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'prenom'        =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'mail' =>array('default'=>'', 'value'=>'', 'required'=>true, 'error'=>'', 'type'=>'text'), 
            'idProfil'      =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'simpleList'), 
            'alerteMail'       =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteCommentaires' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'alerteAdresses' => array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'mdp1' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp2'), 
            'mdp2' =>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'password', 'fieldToCompare'=>'mdp1'), 
            'ville' =>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'idPeriodeEnvoiMailsRegroupes'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'simpleList'), 
            'urlSiteWeb'=>array('default'=>'', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'text'), 
            'afficheFormulaireContactPersoProfilPublic'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'displayNumeroArchiveField'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
            'displayDateFinField'=>array('default'=>'0', 'value'=>'', 'required'=>false, 'error'=>'', 'type'=>'radio'), 
        );
    
    }
    
    // **********************************************************************************************************************************************
    // modifie les infos du compte utilisateur,  suivant les droits de l'utilisateur courant + mise a jour de la session en fonction de la ville favorite choisie ( si on est l'utilisateur connecté)
    // **********************************************************************************************************************************************
    public function modifier() 
    {
        $authentification = new archiAuthentification();
        $majOK=false;
        $formulaire = new formGenerator();
        $mail = new mailObject();
        $avatarFile = new fileObject();
        $image = new imageObject();
        
        // suppression de l'avatar si la checkbox est cochée
        if (isset($this->variablesPost['supprFichierAvatar']) && $this->variablesPost['supprFichierAvatar']=='1')
        {
            if (file_exists($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/avatar.jpg"))
            {
                unlink($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/avatar.jpg");
            }
            
            if (file_exists($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/original.jpg"))
            {
                unlink($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/original.jpg");
            }
        }
        
        
        
        
        // gestion de l'image de l'avatar
        if (isset($this->variablesPost['idUtilisateurModif']) && $this->variablesPost['idUtilisateurModif']!='' && isset($_FILES['fichierAvatar']['name']) && $_FILES['fichierAvatar']['name']!='')
        {
            if (pia_strtolower($avatarFile->getExtensionFromFile($_FILES['fichierAvatar']['name']))=='jpg')
            {
                if (!file_exists($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/"))
                {
                    if (!$avatarFile->creerRepertoire($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/"))
                    {
                        echo "archiUtilisateur::modifier => erreur de création du répertoire pour l'image avatar<br>";
                    }
                }
                $avatarFile->handleUploadedFileSimpleMoveTo(array('inputFileName'=>'fichierAvatar', 'renameFileTo'=>'original.jpg', 'repertoireDestination'=>$this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/"));
                // ensuite on redimensionne l'image
                $image->redimension($this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/original.jpg", 'jpg', $this->cheminPhysique."images/avatar/".$this->variablesPost['idUtilisateurModif']."/avatar.jpg", 120);
            }
            else
            {
                echo "Le fichier de l'avatar doit être au format jpg.";
            }
            
        }
        
        
        
        // gestion infos du compte
        if ($authentification->estConnecte())
        {
            if ($authentification->estAdmin())
            {

            
            
                // cas d'un utilisateur connecté et admin et dans l'admin
                if (isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='utilisateurDetail')
                {
                    $tabForm = $this->getUtilisateurFieldsForAdmin();
                }
                elseif (isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='profil') // utilisateur connecté,  admin ,  dans son profil
                {
                    $tabForm = $this->getUtilisateurFieldsForAdminProfil();
                }
                
                
                $errors = $formulaire->getArrayFromPost($tabForm);
                
                if (count($errors)==0)
                {
                    $sqlChampDisplayNumeroArchiveField="";
                    $sqlChampDisplayDateFinField = "";
                    if (isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='utilisateurDetail')
                    {
                        $sqlChampDisplayNumeroArchiveField=", displayNumeroArchiveFieldInSaisieEvenement=\"".$tabForm['displayNumeroArchiveField']['value']."\"";
                        $sqlChampDisplayDateFinField=", displayDateFinFieldInSaisieEvenement=\"".$tabForm['displayDateFinField']['value']."\"";
                    }
                    // test sur le mot de passe
                    if ($tabForm['mdp1']['value']!='')
                    {
                        // un mot de passe a ete entré ,  donc si pas d'erreur ,  on peut faire l'ajout a la base
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    motDePasse="'.md5($tabForm['mdp1']['value']).'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    idProfil="'.$tabForm['idProfil']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    canCopyright="'.$tabForm['canCopyright']['value'].'", 
                                    canModifyTags="'.$tabForm['canModifyTags']['value'].'", 
                                    canAddWithoutStreet="'.$tabForm['canAddWithoutStreet']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    idPeriodeEnvoiMailsRegroupes="'.$tabForm['idPeriodeEnvoiMailsRegroupes']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                    '.$sqlChampDisplayNumeroArchiveField.'
                                    '.$sqlChampDisplayDateFinField.'
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';
                        
                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                    else
                    {
                        //aucun mot de passe entré ,  on ne le met pas a jour
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    idProfil="'.$tabForm['idProfil']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    canCopyright="'.$tabForm['canCopyright']['value'].'", 
                                    canModifyTags="'.$tabForm['canModifyTags']['value'].'", 
                                    canAddWithoutStreet="'.$tabForm['canAddWithoutStreet']['value'].'", 
                                    idPeriodeEnvoiMailsRegroupes="'.$tabForm['idPeriodeEnvoiMailsRegroupes']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                    '.$sqlChampDisplayNumeroArchiveField.'
                                    '.$sqlChampDisplayDateFinField.'
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';

                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                    
                    if (isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='utilisateurDetail')
                    {
                        // Si un utilisateur est banni,  on inactive son compte et on met le champs compteBanni à 1
                        $this->majBannissementUtilisateur(array('idUtilisateur'=>$this->variablesPost['idUtilisateurModif'], 'champsBanissementApresValidationFormulaire'=>$tabForm['bannirUtilisateur']['value']));
                    }
                }
            }
            elseif ($this->getIdProfilFromUtilisateur($authentification->getIdUtilisateur())=='3') // moderateurs
            {
                // cas d'un utilisateur connecté moderateurs
                $tabForm = $this->getUtilisateurFieldsForModerators();
                $errors = $formulaire->getArrayFromPost($tabForm);
                if (count($errors)==0)
                {
                    // test sur le mot de passe
                    if ($tabForm['mdp1']['value']!='')
                    {
                        // un mot de passe a ete entré ,  donc si pas d'erreur ,  on peut faire l'ajout a la base
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    motDePasse="'.md5($tabForm['mdp1']['value']).'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    idPeriodeEnvoiMailsRegroupes="'.$tabForm['idPeriodeEnvoiMailsRegroupes']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';
                        
                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                    else
                    {
                        //aucun mot de passe entré ,  on ne le met pas a jour
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    idPeriodeEnvoiMailsRegroupes="'.$tabForm['idPeriodeEnvoiMailsRegroupes']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';

                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                }
            }
            else
            {
                // cas d'un utilisateur connecté non admin
                $tabForm = $this->getUtilisateurFieldsNotAdmin();
                $errors = $formulaire->getArrayFromPost($tabForm);
                if (count($errors)==0)
                {
                    // test sur le mot de passe
                    if ($tabForm['mdp1']['value']!='')
                    {
                        // un mot de passe a ete entré ,  donc si pas d'erreur ,  on peut faire l'ajout a la base
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    motDePasse="'.md5($tabForm['mdp1']['value']).'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';
                        
                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                    else
                    {
                        //aucun mot de passe entré ,  on ne le met pas a jour
                        $reqUpdate = 'update utilisateur 
                                set nom="'.$tabForm['nom']['value'].'", 
                                    prenom="'.$tabForm['prenom']['value'].'", 
                                    mail="'.$tabForm['mail']['value'].'", 
                                    urlSiteWeb="'.$tabForm['urlSiteWeb']['value'].'", 
                                    idVilleFavoris="'.$tabForm['ville']['value'].'", 
                                    alerteCommentaires="'.$tabForm['alerteCommentaires']['value'].'", 
                                    alerteAdresses="'.$tabForm['alerteAdresses']['value'].'", 
                                    alerteMail="'.$tabForm['alerteMail']['value'].'", 
                                    displayProfilContactForm="'.$tabForm['afficheFormulaireContactPersoProfilPublic']['value'].'"
                                where idUtilisateur="'.$this->variablesPost['idUtilisateurModif'].'"
                            ';

                        if ($resUpdate = $this->connexionBdd->requete($reqUpdate))
                            $majOK=true;
                    }
                }
            }
        } else {
            echo "Vous n'êtes pas connecté.<br>";
        }
        

        if ($majOK && !$authentification->estAdmin())
        {
            echo "La mise à jour a été effectuée";
            // envoi d'un mail a l'admin
            $message = "";
            $message .= "Un utilisateur a modifié son compte ,  pour vérifier : <a href='".$this->creerUrl('', 'utilisateurDetail', array('idUtilisateur'=>$this->variablesPost['idUtilisateurModif']))."'>Cliquez ici</a>";
            $mail->sendMailToAdministrators($mail->getSiteMail(), "Un utilisateur a modifié son compte", $message, '', true);
            $this->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message, 'idTypeMailRegroupement'=>7, 'criteres'=>""));
            
            // on met a jour la session contenant la ville favorite ,  si on est soi meme l'utilisateur courant
            if ($this->variablesPost['idUtilisateurModif'] == $authentification->getIdUtilisateur())
            {
                $this->session->addToSession("idVilleFavoris", $tabForm['ville']['value']);
            }
        }
        elseif ($authentification->estAdmin())
        {
            echo "La mise à jour a été effectuée";
            if ($this->variablesPost['idUtilisateurModif'] == $authentification->getIdUtilisateur())
            {
                $this->session->addToSession("idVilleFavoris", $tabForm['ville']['value']);
            }
        } else {
            echo "La mise à jour n'a pas pu être effectuée.";
        }
        
        
        //echo $this->afficher($tabForm, $this->variablesPost['idUtilisateurModif']);
    }

    
    // cette fonction met a jour les champs concerné (compteActif,  compteBanni) de l'utilisateur ,  banni ou debanni
    public function majBannissementUtilisateur($params = array())
    {
        if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='' && isset($params['champsBanissementApresValidationFormulaire']) && $params['champsBanissementApresValidationFormulaire']!='')
        {
            $reqEtatUtilisateur = "SELECT compteBanni FROM utilisateur WHERE idUtilisateur='".$params['idUtilisateur']."'";
            $resEtatUtilisateur = $this->connexionBdd->requete($reqEtatUtilisateur);
            
            if (mysql_num_rows($resEtatUtilisateur)==1)
            {
                $fetchEtatUtilisateur = mysql_fetch_assoc($resEtatUtilisateur);
                
                if ($fetchEtatUtilisateur['compteBanni']!=$params['champsBanissementApresValidationFormulaire'])
                {
                    // il y a un changement dans l'etat ,  on met les champs concernés a jour
                    if ($params['champsBanissementApresValidationFormulaire']=='1')
                    {
                        // l'utilisateur est maintenant banni :
                        $reqBannissement = "UPDATE utilisateur SET compteBanni='1',  compteActif='0' WHERE idUtilisateur='".$params['idUtilisateur']."' ";
                        $resBannissement = $this->connexionBdd->requete($reqBannissement);
                        
                    }
                    else
                    {
                        // l'utilisateur etait banni ,  mais on l'a reactivé
                        // on verifie d'abord que le champs mail est renseigné et qu'il y a un mot de passe
                        $reqVerif = "SELECT 0 FROM utilisateur WHERE mail<>'' AND motDePasse<>'' AND idUtilisateur='".$params['idUtilisateur']."'";
                        $resVerif = $this->connexionBdd->requete($reqVerif);
                        
                        
                        if (mysql_num_rows($resVerif)==1)
                        {
                            $reqDeBan = "UPDATE utilisateur SET compteBanni='0',  compteActif='1' WHERE idUtilisateur='".$params['idUtilisateur']."'";
                            $resDeBan = $this->connexionBdd->requete($reqDeBan);
                        }
                        
                    }
                }
            }
        }
    }
    
    
    
    // **********************************************************************************************************************************************
    // affiche le formulaire de modification du compte d'un utilisateur
    // **********************************************************************************************************************************************
    public function afficher($tabTravail=array(), $idUtilisateur = '', $modeAffichage='utilisateurDetail')
    {
        $html = '';
        $tabParametresPredefinis = array('pays',  'ville');
        
        $authentification = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('ev'=>$modeAffichage.'.tpl')));
        
        
        
        if (empty($idUtilisateur) OR !is_numeric($idUtilisateur) OR $idUtilisateur < 1) 
        {
            // erreur
            $html .= 'Erreur id invalide';
            if (!$authentification->estConnecte())
            {
                echo "<script  >location.href='".$this->creerUrl('', 'authentification', array())."';</script>";
            }
        }
        else 
        {
            // si l'on n'est pas admin on ne peut modifier que son propre compte
            
            if ($authentification->estConnecte() && ($idUtilisateur == $authentification->getIdUtilisateur() || $authentification->estAdmin()))
            {
                if ($modeAffichage=='utilisateurProfil')
                {
                    
                    $s = new objetSession();
                    $infos = $u->getArrayInfosFromUtilisateur($s->getFromSession('utilisateurConnecte'.$this->idSite));
                    $t->assign_vars(array("phraseBienvenu"=>_("Bienvenue sur votre profil")." ".ucwords($infos['prenom'])." ".ucwords($infos['nom'])));
                    $t->assign_vars(array("srcImgTrombone"=>$this->getUrlImage(null,  "trombone.jpg")));
                }
                
                $sqlIdUtilisateur = $idUtilisateur;
                
                $rep = $this->getInfosModifsPerso($idUtilisateur);
                
                
                if ($rep) 
                {
                    $idUtilisateur = $rep['idUtilisateur'];
                    

                    
                    
                    if (!empty($rep['nomVille']))
                    {
                        $t->assign_block_vars('villeFavoris',  array());
                    }
                    $t->assign_vars(array(

                        'villeFavorite'        => $rep['nomVille'], 
                        'urlVilleFavorite'     => $this->creerUrl('',  'adresseListe',  array('selection'=>'ville',  'id'=>$rep['idVille'],  'debut'=>0)), 
                        'paysVilleFavorite'    => $rep['nomPays'], 
                        'urlPaysVilleFavorite' => $this->creerUrl('',  'adresseListe',  array('selection'=>'pays',  'id'=>$rep['idPays'],  'debut'=>0))
                        ));
                    

                    
                    // évènements créés par l'utilisateur
                    //$e = new archiEvenement();
                    //$evenements = $e->afficherListe(array('selection'=>'utilisateurAjout',  'id' => $idUtilisateur)); // liste des evenements de l'utilisateur
                    
                    // images créés par l'utilisateur
                    //$i = new archiImage();
                    //$images = $i->afficherListe(array('selection'=>'utilisateur',  'id' => $idUtilisateur)); // liste des images de l'utilisateur
                    
                    $t->assign_vars(array('idUtilisateurModif'=>$idUtilisateur));
                    
                    
                    
                    // ****************************
                    if (count($tabTravail)>0)
                    {
                        // recuperation des infos du formulaire
                        $nom = $tabTravail['nom']['value'];
                        $prenom = $tabTravail['prenom']['value'];
                        $mail = $tabTravail['mail']['value'];
                        if (isset($tabTravail['idProfil']['value']))
                            $idProfil = $tabTravail['idProfil']['value'];
                        if (isset($tabTravail['alerteMail']['value']))
                            $alerteMail = $tabTravail['alerteMail']['value'];
                        if (isset($tabTravail['idPeriodeEnvoiMailsRegroupes']['value']))
                            $idPeriodeEnvoiMailsRegroupes = $tabTravail['idPeriodeEnvoiMailsRegroupes']['value'];
                        if (isset($tabTravail['urlSiteWeb']['value']))
                            $urlSiteWeb = $tabTravail['urlSiteWeb']['value'];
                        if (isset($tabTravail['displayProfilContactForm']['value']))
                            $displayProfilContactForm = $tabTravail['displayProfilContactForm']['value'];
                            

                        foreach($tabTravail as $name => $value)
                        {
                            $t->assign_vars(array($name.'-error'=>$value['error']));
                        }
                        

                    }
                    else
                    {
                        // recuperation des infos de la base
                        $nom = $rep['nom'];
                        $prenom = $rep['prenom'];
                        $mail = $rep['mail'];
                        $idProfil = $rep['idProfil'];
                        $alerteMail = $rep['alerteMail'];
                        $urlSiteWeb = $rep['urlSiteWeb'];
                        $displayProfilContactForm = $rep['displayProfilContactForm'];
                        
                        $idPeriodeEnvoiMailsRegroupes = $rep['idPeriodeEnvoiMailsRegroupes'];
                    }
                    
                    
                    $d = new droitsObject();
                    $arrayProfils = $d->getArrayListeProfils();
                    
                    
                    
                    $selectProfil = "<select name='idProfil' style='width:145px;'>";
                    foreach($arrayProfils as $idProfilListe => $libelleProfilListe)
                    {
                        $checked = "";
                        if ($idProfil==$idProfilListe)
                        {
                            $checked = "selected";
                        }
                        $selectProfil.="<option value='$idProfilListe' $checked>$libelleProfilListe</option>";
                    }
                    $selectProfil.="</select>";
                    
                    // periodicite d'envoi des mails
                    $reqPeriodicite = "SELECT idPeriode, intitule FROM periodesEnvoiMailsRegroupes";
                    $resPeriodicite = $this->connexionBdd->requete($reqPeriodicite);
                    $selectPeriodiciteMail = "<select name='idPeriodeEnvoiMailsRegroupes'>";
                    while($fetchPeriodicite = mysql_fetch_assoc($resPeriodicite))
                    {
                        $selected ="";
                        if ($idPeriodeEnvoiMailsRegroupes == $fetchPeriodicite['idPeriode'])
                            $selected = "selected";
                        $selectPeriodiciteMail .= "<option value='".$fetchPeriodicite['idPeriode']."' $selected>".$fetchPeriodicite['intitule']."</option>";
                    }
                    $selectPeriodiciteMail.="</select>";
                    
                    
                    
                    
                    
                    $t->assign_block_vars('detailUtilisateur', array(
                                                'nom'=>$nom, 
                                                'prenom'=>$prenom, 
                                                'email'=>$mail, 
                                                'onClickChoixVilleFavorite'=>"document.getElementById('calqueVille').style.top=getScrollHeight()+150+'px';document.getElementById('paramChampAppelantVille').value='ville';document.getElementById('calqueVille').style.display='block';", 
                                                'ville'=>$rep['idVille'], 
                                                'villetxt'=>$rep['nomVille'], 
                                                'urlSiteWeb'=>$urlSiteWeb, 
                                                'imageAvatar'=>"<img src='".$this->getImageAvatar(array('idUtilisateur'=>$idUtilisateur))."' border=0>"
                                                ));
                    
                    $authentifie = new archiAuthentification();
                    if ($authentifie->estConnecte() && $authentifie->estAdmin())
                    {
                        $t->assign_block_vars('detailUtilisateur.utilisateurCourantIsAdmin', array());
                        $t->assign_vars(array('selectProfil'=>$selectProfil));

                        
                        //$t->assign_vars(array("urlLogsMails"=>"<a href='".$this->creerUrl('', 'afficheLogsMails', array('idUtilisateur'=>$idUtilisateur))."'>Acceder au log des mail de cet utilisateur</a>"));
                    }
                    
                    if ($modeAffichage=="utilisateurDetail")
                    {
                        if ($authentifie->estConnecte()  && $authentifie->estAdmin()) // admin ou moderateur ont le droit de changer leur periodicites (pour les moderateur ,  voir plus bas
                        {
                            $t->assign_vars(array('selectPeriodiciteMail'=>$selectPeriodiciteMail));
                            $t->assign_block_vars('detailUtilisateur.banissementUtilisateurParAdmin', array());
                            
                            if ($rep['compteBanni']=='1')
                            {
                                $t->assign_vars(array('checkDisplayBannirUtilisateurOui'=>'checked'));
                            }
                            else
                            {
                                $t->assign_vars(array('checkDisplayBannirUtilisateurNon'=>'checked'));
                            }
                            
                            
                            
                        }
                        
                        if ($this->canChangeNumeroArchiveField(array('idUtilisateur'=>$idUtilisateur)))
                        {
                            $t->assign_vars(array('checkDisplayNumeroArchiveFieldOui'=>'checked'));
                        }
                        else
                        {
                            $t->assign_vars(array('checkDisplayNumeroArchiveFieldNon'=>'checked'));
                        }
                        
                        
                        if ($this->canChangeDateFinField(array('idUtilisateur'=>$idUtilisateur)))
                        {
                            $t->assign_vars(array('checkDisplayDateFinFieldOui'=>'checked'));
                        }
                        else
                        {
                            $t->assign_vars(array('checkDisplayDateFinFieldNon'=>'checked'));
                        }
                        
                        if ($this->canCopyright(array('idUtilisateur'=>$idUtilisateur)))
                        {
                            $t->assign_vars(array('canCopyright1'=>'checked'));
                        }
                        else
                        {
                            $t->assign_vars(array('canCopyright0'=>'checked'));
                        }
                        if ($this->canModifyTags(array('idUtilisateur'=>$idUtilisateur)))
                        {
                            $t->assign_vars(array('canModifyTags1'=>'checked'));
                        }
                        else
                        {
                            $t->assign_vars(array('canModifyTags0'=>'checked'));
                        }
                        if ($this->canAddWithoutStreet(array('idUtilisateur'=>$idUtilisateur)))
                        {
                            $t->assign_vars(array('canAddWithoutStreet1'=>'checked'));
                        }
                        else
                        {
                            $t->assign_vars(array('canAddWithoutStreet0'=>'checked'));
                        }
                        
                    }
                    
                    if ($modeAffichage=='utilisateurProfil')
                    {
                        if ($authentifie->estConnecte() && ($idProfil=='3' || $idProfil=='4')) // admin ou moderateur ont le droit de changer leur periodicites
                        {
                            $t->assign_block_vars('detailUtilisateur.utilisateurCourantIsAdminOrModerateur', array());
                            $t->assign_vars(array('selectPeriodiciteMail'=>$selectPeriodiciteMail));
                        }
                    }
                    
                    
                    
                    if ($authentifie->estConnecte())
                    {
                        if ($rep['alerteCommentaires']=='1')
                            $t->assign_vars(array('checkAlertesCommentairesOui'=>'checked="checked"'));
                        else
                            $t->assign_vars(array('checkAlertesCommentairesNon'=>'checked="checked"'));
                        
                        if ($rep['alerteAdresses']=='1')
                            $t->assign_vars(array('checkAlertesAdressesOui'=>'checked="checked"'));
                        else
                            $t->assign_vars(array('checkAlertesAdressesNon'=>'checked="checked"'));
                        
                        if ($alerteMail=='1')
                            $t->assign_vars(array('checkAlerteMailOui'=>'checked="checked"'));
                        else
                            $t->assign_vars(array('checkAlerteMailNon'=>'checked="checked"'));
                        
                        if ($displayProfilContactForm=='1')
                            $t->assign_vars(array('checkContactPersoProfilOui'=>'checked="checked"'));
                        else
                            $t->assign_vars(array('checkContactPersoProfilNon'=>'checked="checked"'));
                    }                    
                    
                    if ($modeAffichage=="utilisateurDetail")
                    {
                        $t->assign_vars(array('formAction'=>$this->creerUrl('modifierUtilisateur', 'utilisateurDetail', array('modeAffichage'=>'utilisateurDetail', 'idUtilisateur'=>$idUtilisateur))));
                    }
                    else
                    {
                        $t->assign_vars(array('formAction'=>$this->creerUrl('modifierUtilisateur', 'afficheAccueil', array('modeAffichage'=>'profil'))));
                    }
                    
                    // gestion de la popup du choix de la ville
                    $adresses = new archiAdresse();
                    $t->assign_vars(array('popupChoixVille'=>$adresses->getPopupChoixVille('modifUtilisateur')));

                    ob_start();
                    $t->pparse('ev');
                    $html=ob_get_contents();
                    ob_end_clean();
                }
                else 
                {
                    $html .= 'Aucun résultat';
                }
            }
            else
            {
                if ($authentification->estConnecte())
                {
                    echo "Vous n'avez pas les droits pour effectuer cette action.<br>";
                }
                else
                {
                    echo "Vous n'êtes pas connecté.<br>";
                }
            }
        }
        
        
        $ongletUtilisateur = $html;
        
        $onglets = new ongletObject('0');
        $onglets->setLargeurTotale('700');
        $onglets->setLargeurEtiquette('200');
        $onglets->setHauteurOnglets('25');
        $onglets->setStyleContoursContenu("style='border-left:#007799 solid 2px;border-right:#007799 solid 2px;border-bottom:#007799 solid 2px;'");
        $onglets->setStyleTable("style='margin:0;padding:0;'");
        $onglets->setStyleTableEtiquettes("style='margin:0;padding:0;'");
        $onglets->setStylesOnglets('');
        $onglets->setStyleBorderHautContenu("style='border-bottom:2px solid #007799;'");
        
        
        
        $isCompteModerateur=false;
        if ($this->getIdProfilFromUtilisateur($idUtilisateur)=='3')// l'utilisateur est un moderateur
        {
            $isCompteModerateur=true;
        }
        
        if (isset($this->variablesGet['archiOnglet']) && $this->variablesGet['archiOnglet']=='listeVilles')
        {
            $afficheOngletUtilisateur = false;
            $afficheOngletListeVille = true;
            $afficheOngletLogsMails=false;
        }
        elseif (isset($this->variablesGet['recherche']))
        {
            $afficheOngletUtilisateur = false;
            $afficheOngletListeVille = false;
            $afficheOngletLogsMails=true;
        }
        
        else
        {
            $afficheOngletUtilisateur = true;
            $afficheOngletListeVille = false;
            $afficheOngletLogsMails=false;
        }
        
        if ($modeAffichage=='utilisateurDetail' && $u->isAuthorized('admin_ville_par_moderateur', $authentification->getIdUtilisateur()))
        {
            $onglets->addContent("utilisateur", $ongletUtilisateur, $afficheOngletUtilisateur);
        }
        
        if ($modeAffichage=='utilisateurDetail' && $isCompteModerateur && $u->isAuthorized('admin_ville_par_moderateur', $authentification->getIdUtilisateur()))
        {
        
            $ongletListeVille="";
            
            $reqVilles = "
                        SELECT * 
                        FROM ville 
                        WHERE nom!='autre'";
            $resVilles = $this->connexionBdd->requete($reqVilles);
            
            $reqVillesModeration = "SELECT idVille FROM utilisateurModerateurVille WHERE idUtilisateur='".$idUtilisateur."'";
            $resVillesModeration = $this->connexionBdd->requete($reqVillesModeration);
            $arrayListeVillesModerees=array();
            if (mysql_num_rows($resVillesModeration)>0)
            {
                while($fetchVillesModeration = mysql_fetch_assoc($resVillesModeration))
                {
                    $arrayListeVillesModerees[] = $fetchVillesModeration['idVille'];
                }
            }
                        
            $tableau = new tableau();
            
            while($fetchVilles = mysql_fetch_assoc($resVilles))
            {
                $checked="";
                $baliseOuvrante="";
                $baliseFermante="";
                if (in_array($fetchVilles['idVille'], $arrayListeVillesModerees))
                {
                    $checked="checked";
                    $baliseOuvrante="<b>";
                    $baliseFermante="</b>";
                }
                $tableau->addValue("<input type='checkbox' name='idVillesModerateur[]' value='".$fetchVilles['idVille']."' $checked>&nbsp;$baliseOuvrante".$fetchVilles['nom'].$baliseFermante);
            }
            
            $ongletListeVille.="<h3>Cet utilisateur modère :</h3>";
            $ongletListeVille.="<form action='".$this->creerUrl('enregistreListeVillesModerateur', 'utilisateurDetail', array('idUtilisateur'=>$idUtilisateur, 'archiOnglet'=>'listeVilles'))."' name='formulaireModerationVille' enctype='multipart/form-data' method='POST'>";
            $ongletListeVille.=$tableau->createHtmlTableFromArray(4);
            $ongletListeVille.="<input type='hidden' value='".$idUtilisateur."' name='idUtilisateurModerateur'>";
            $ongletListeVille.="<input type='submit' value='Enregistrer'>";
            $ongletListeVille.="</form>";
            

            $onglets->addContent("modération de ville", $ongletListeVille, $afficheOngletListeVille);
            
        }
        


        if ($modeAffichage=='utilisateurDetail' && $u->isAuthorized('admin_ville_par_moderateur', $authentification->getIdUtilisateur()))
        {
            // utilisateur administrateur
            $administration = new archiAdministration();
            
            $ongletLogsMail=$administration->getLoggedMails();
            
            
            $onglets->addContent("log mails", $ongletLogsMail, $afficheOngletLogsMails);
        }
        
        if ($modeAffichage=='utilisateurDetail' && $u->isAuthorized('admin_ville_par_moderateur', $authentification->getIdUtilisateur()))
        {
            $html=$onglets->getHTML();
        }
        
        return $html;
    }

    private function supprimer() {
    }
    
    public function getInfosModifsPerso($sqlIdUtilisateur=0)
    {
        /*$sql = 'SELECT u.idUtilisateur as idUtilisateur,  u.nom as nom,  u.prenom as prenom,  u.idProfil as idProfil, u.alerteCommentaires, u.alerteAdresses,  u.mail as mail,  u.alerteMail as alerteMail, 
                    COUNT(DISTINCT hI.idHistoriqueImage)- COUNT(DISTINCT hI.idImage ) AS nbModifImage,  COUNT(DISTINCT hI.idImage ) AS nbAjoutImage,  
                    COUNT(DISTINCT hE.idHistoriqueEvenement)- COUNT(DISTINCT hE.idEvenement ) AS nbModifEvenement,  COUNT(DISTINCT hE.idEvenement ) AS nbAjoutEvenement, 
                    v.idVille,  v.nom AS nomVille,  p.idPays,  p.nom AS nomPays 
                FROM utilisateur u
                LEFT JOIN historiqueImage hI ON hI.idUtilisateur = u.idUtilisateur
                LEFT JOIN historiqueEvenement hE ON hE.idUtilisateur = u.idUtilisateur
                LEFT JOIN ville v ON v.idVille=u.idVilleFavoris
                LEFT JOIN pays p  USING (idPays)
                            WHERE u.idUtilisateur='.$sqlIdUtilisateur.'
                GROUP BY u.idUtilisateur
                            LIMIT 1 ';
        */                
                            
                            
        $sqlInfosPerso= "SELECT u.idUtilisateur as idUtilisateur,  u.nom as nom,  u.prenom as prenom,  u.idProfil as idProfil, u.alerteCommentaires, u.alerteAdresses,  u.mail as mail,  u.alerteMail as alerteMail, u.idVilleFavoris as idVilleFavoris, u.idPeriodeEnvoiMailsRegroupes as idPeriodeEnvoiMailsRegroupes, u.urlSiteWeb as urlSiteWeb, u.displayProfilContactForm as displayProfilContactForm, u.compteBanni as compteBanni FROM utilisateur u WHERE u.idUtilisateur = '".$sqlIdUtilisateur."'";
        $resInfosPerso = $this->connexionBdd->requete($sqlInfosPerso);
        $fetchInfosPerso = mysql_fetch_assoc($resInfosPerso);
        
        $sqlNbModifsImage = "SELECT COUNT(DISTINCT idHistoriqueImage)- COUNT(DISTINCT idImage ) AS nbModifImage FROM historiqueImage WHERE idUtilisateur='".$sqlIdUtilisateur."'";
        $resNbModifsImage = $this->connexionBdd->requete($sqlNbModifsImage);
        $fetchNbModifsImage = mysql_fetch_assoc($resNbModifsImage);
        
        $sqlNbAjoutImage = "SELECT COUNT(DISTINCT idImage ) AS nbAjoutImage FROM historiqueImage WHERE idUtilisateur='".$sqlIdUtilisateur."'";
        $resNbAjoutImage = $this->connexionBdd->requete($sqlNbAjoutImage);
        $fetchNbAjoutImage = mysql_fetch_assoc($resNbAjoutImage);
        
        $sqlNbModifEvenement = "SELECT COUNT(DISTINCT idHistoriqueEvenement)- COUNT(DISTINCT idEvenement ) AS nbModifEvenement FROM historiqueEvenement WHERE idUtilisateur = '".$sqlIdUtilisateur."' ";
        $resNbModifEvenement = $this->connexionBdd->requete($sqlNbModifEvenement);
        $fetchNbModifEvenement = mysql_fetch_assoc($resNbModifEvenement);
        
        $sqlNbAjoutEvenement = "SELECT COUNT(DISTINCT idEvenement ) AS nbAjoutEvenement FROM historiqueEvenement WHERE idUtilisateur = '".$sqlIdUtilisateur."' ";
        $resNbAjoutEvenement = $this->connexionBdd->requete($sqlNbAjoutEvenement);
        $fetchNbAjoutEvenement = mysql_fetch_assoc($resNbAjoutEvenement );
        
        
        
        $sqlVilleFavoris = "SELECT v.idVille,  v.nom as nomVille,  p.idPays,  p.nom as nomPays FROM ville v LEFT JOIN pays p ON p.idPays = v.idPays WHERE v.idVille='".$fetchInfosPerso['idVilleFavoris']."'";
        $resVilleFavoris = $this->connexionBdd->requete($sqlVilleFavoris);
        $fetchVilleFavoris = mysql_fetch_assoc($resVilleFavoris);
        if (!isset($fetchVilleFavoris['nomVille']))
        {
            $fetchVilleFavoris['nomVille']='';
            $fetchVilleFavoris['nomPays']='';
            $fetchVilleFavoris['idVille']='';
            $fetchVilleFavoris['idPays']='';
        }

        return array_merge($fetchInfosPerso, $fetchNbModifsImage, $fetchNbAjoutImage, $fetchNbModifEvenement, $fetchNbAjoutEvenement, $fetchVilleFavoris);
    }
    
    // **********************************************************************************************************************************************
    // affiche la liste des utilisateurs du site ( administration ) 
    // **********************************************************************************************************************************************
    public function afficherListe() 
    {
        $html = '';
            
        
        // formulaire de recherche
        $f = new formGenerator();
        
        $configFields = array(
            "motCleRechercheUtilisateur"=>array('type'=>'text', 'htmlCode'=>'', 'error'=>'', 'libelle'=>'Recherche :', 'default'=>'', 'value'=>'', 'required'=>false), 
            "pageCourante" =>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'', 'value'=>'', 'required'=>false), 
            "classementDernieresConnexions" =>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'0', 'value'=>'0', 'required'=>false, 'libelle'=>'dernieres connexions'), 
            "classementNbConnexions" =>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'0', 'value'=>'0', 'required'=>false, 'libelle'=>'nbConnexions'), 
            "classementNbParticipations" =>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'0', 'value'=>'0', 'required'=>false, 'libelle'=>'nbParticipations'), 
            "classementDateCreation"=>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'0', 'value'=>'0', 'required'=>false, 'libelle'=>'dateCreation'), 
            "classementNomPrenom"=>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'0', 'value'=>'0', 'required'=>false, 'libelle'=>'nomprenom')
        );
        
        
        // gestion de la recherche
        $sqlSelect="";
        if (isset($this->variablesPost['motCleRechercheUtilisateur']) && $this->variablesPost['motCleRechercheUtilisateur']!='')
        {
            $arraySqlSelect = array();
            $arraySqlSelect[] = "nom LIKE \"%".mysql_escape_string($this->variablesPost['motCleRechercheUtilisateur'])."%\"";
            $arraySqlSelect[] = "prenom LIKE \"%".mysql_escape_string($this->variablesPost['motCleRechercheUtilisateur'])."%\"";
            $arraySqlSelect[] = "mail LIKE \"%".mysql_escape_string($this->variablesPost['motCleRechercheUtilisateur'])."%\"";
            $arraySqlSelect[] = "concat(nom, prenom, mail) LIKE \"%".mysql_escape_string($this->variablesPost['motCleRechercheUtilisateur'])."%\"";
            $sqlSelect = " AND (".implode(" OR ", $arraySqlSelect).")";
        }
        
        
        // tri par defaut
        $sqlTri = " ORDER BY nom, prenom ";

        
        // requetes par defaut
        $reqCount="
        SELECT 0
        FROM utilisateur u
        WHERE 1=1
        $sqlSelect
        $sqlTri
        ";
    //(cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
    //OR cu1.date IS NULL)
    
    
        $req = "
        SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  cu1.date as derniereConnexion
        FROM utilisateur u
        LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
        WHERE ((cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
        OR cu1.date IS NULL)
        OR cu1.idUtilisateur IS NULL)
        AND u.idUtilisateur != 0
        $sqlSelect
        $sqlTri
        ";
        
        // -- fin requete par defaut
        
        
        /*$reqCount = "
        
            SELECT 0 
            FROM utilisateur u
            LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
            LEFT JOIN connexionsUtilisateurs cu2 ON cu2.idUtilisateur = cu1.idUtilisateur
            WHERE 1=1
            $sqlSelect
            GROUP BY cu1.idUtilisateur, cu1.date
            HAVING cu1.date = max(cu2.date) OR cu1.date IS NULL
            $sqlTri
        ";
        
        $req = "
        
            SELECT u.idUtilisateur,  u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  cu1.date as derniereConnexion
            FROM utilisateur u
            LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
            LEFT JOIN connexionsUtilisateurs cu2 ON cu2.idUtilisateur = cu1.idUtilisateur
            WHERE 1=1
            $sqlSelect
            GROUP BY cu1.idUtilisateur, cu1.date
            HAVING cu1.date = max(cu2.date) OR cu1.date IS NULL
            $sqlTri
        
        
        ";
        */
                
        if (isset($this->variablesGet['recherche']) && $this->variablesGet['recherche']=='1')
        {
            $f->getArrayFromPost($configFields);
            
            $arraySqlTri=array();
            
            $triAutre = false;
            
            // classement dernieres connexions
            if ($configFields['classementDernieresConnexions']['value']!='0')
            {
            
                /*$reqCount="
                SELECT 0
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE (cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL)
                $sqlSelect
                ";
                */
                $req = "
                SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  cu1.date as derniereConnexion
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE ((cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL) OR cu1.idUtilisateur IS NULL)
                $sqlSelect
                ";
            
                if ($configFields['classementDernieresConnexions']['value']=='1')
                {
                    $req.= " ORDER BY derniereConnexion DESC ";
                    $reqCount.="";//" ORDER BY derniereConnexion DESC ";
                }
                
                if ($configFields['classementDernieresConnexions']['value']=='0')
                {
                    $req.= " ORDER BY derniereConnexion ASC ";
                    $reqCount.="";//" ORDER BY derniereConnexion ASC ";
                }
            }
            
            // classement pas nombre de connexions
            if ($configFields['classementNbConnexions']['value']!='0')
            {    

                $reqCount="
                SELECT count(cu1.idUtilisateur) as nbConnexions
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE 1=1
                $sqlSelect
                GROUP BY u.idUtilisateur
                ";
            
                $req = "
                SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  count(cu1.idUtilisateur) as nbConnexions
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE 1=1
                $sqlSelect
                GROUP BY u.idUtilisateur
                ";

            
                if ($configFields['classementNbConnexions']['value']=='1')
                {
                    $req.= " ORDER BY nbConnexions DESC ";
                    $reqCount.="";//" ORDER BY nbConnexions DESC ";
                }
                
                if ($configFields['classementNbConnexions']['value']=='0')
                {
                    $req.= " ORDER BY nbConnexions ASC ";
                    $reqCount.="";//" ORDER BY nbConnexions ASC ";
                }            
            }
            
            // classement pas nombre de participations
            if ($configFields['classementNbParticipations']['value']!='0')
            {    

                $reqCount="
                SELECT 0
                FROM utilisateur u
                
                WHERE 1=1
                $sqlSelect
                GROUP BY u.idUtilisateur
                ";
            
                $req = "
                SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  count(cu1.idUtilisateur) as nbConnexions
                FROM utilisateur u
                LEFT JOIN historiqueEvenement cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE 1=1
                $sqlSelect
                GROUP BY u.idUtilisateur
                ";

            
                if ($configFields['classementNbConnexions']['value']=='0')
                {
                    $req.= " ORDER BY nbConnexions DESC ";
                    $reqCount.="";//" ORDER BY nbConnexions DESC ";
                }
                
                if ($configFields['classementNbConnexions']['value']=='1')
                {
                    $req.= " ORDER BY nbConnexions ASC ";
                    $reqCount.="";//" ORDER BY nbConnexions ASC ";
                }            
            }
            
            if ($configFields['classementDateCreation']['value']!='0')
            {
                $reqCount="
                SELECT 0
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE (cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL)
                $sqlSelect
                ";
            
                $req = "
                SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  cu1.date as derniereConnexion
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE (cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL)
                $sqlSelect
                ";
            
                if ($configFields['classementDateCreation']['value']=='1')
                {
                    $req.= " ORDER BY dateCreation DESC, u.idUtilisateur DESC ";
                    $reqCount.="";//" ORDER BY dateCreation DESC, u.idUtilisateur DESC ";
                }
                
                if ($configFields['classementDateCreation']['value']=='0')
                {
                    $req.= " ORDER BY dateCreation ASC, u.idUtilisateur ASC ";
                    $reqCount.="";//" ORDER BY dateCreation ASC, u.idUtilisateur ASC  ";
                }
            }
            
            if ($configFields['classementNomPrenom']['value']!='0')
            {
                $reqCount="
                SELECT 0
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE (cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL)
                $sqlSelect
                ";
            
                $req = "
                SELECT u.idUtilisateur, u.nom as nom,  u.prenom as prenom,  u.dateCreation as dateCreation,  cu1.date as derniereConnexion
                FROM utilisateur u
                LEFT JOIN connexionsUtilisateurs cu1 ON cu1.idUtilisateur = u.idUtilisateur
                WHERE (cu1.date = (SELECT max(cu2.date) FROM connexionsUtilisateurs cu2 WHERE cu2.idUtilisateur = u.idUtilisateur)
                OR cu1.date IS NULL)
                $sqlSelect
                ";
            
                if ($configFields['classementNomPrenom']['value']=='1')
                {
                    $req.= " ORDER BY u.nom ASC, u.prenom ASC ";
                    $reqCount.="";//" ORDER BY u.nom ASC, prenom ASC ";
                }
                
                if ($configFields['classementNomPrenom']['value']=='0')
                {
                    $req.= " ORDER BY u.nom DESC, u.prenom DESC ";
                    $reqCount.="";//" ORDER BY u.nom DESC, u.prenom DESC ";
                }
            }
            
        }

        $configForm = array(
        'titrePage'=>'', 
        'fields'=>$configFields, 
        'submitButtonValue'=>_("Envoyer"), 
        'formAction'=>$this->creerUrl('', 'utilisateurListe', array_merge(array('recherche'=>1))), 
        'templateFileName'=>'rechercheUtilisateurs.tpl', 
        'formName'=>'formRechercheUtilisateur'
        );
        
        
        $pagination = new paginationObject();
        
        $resCount=$this->connexionBdd->requete($reqCount);
        
        
        $nbEnregistrementTotaux = mysql_num_rows($resCount);
        
        $nbEnregistrementsParPage = 15;
        
        $arrayPagination=$pagination->pagination(array(
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux, 
                                        'typeLiens'=>'formulaire', 
                                        'champPageCourante'=>'pageCourante', 
                                        'nomParamPageCourante'=>'pageCourante', 
                                        'idFormulaire'=>'formRechercheUtilisateur'
                                        ));

        // requête
        // classement par date de dernieres connexions
        

        $req = $pagination->addLimitToQuery($req);
        
        $result = $this->connexionBdd->requete($req);
        
        if (!empty($result)) {
            $t=new Template('modules/archi/templates/');
        
            $t->set_filenames((array('ev'=>'listeUtilisateur.tpl')));
            $t->assign_vars(array("recherche"=>$f->afficherFromArray($configForm)));

            while ( $rep = mysql_fetch_object( $result )) {
                $hasResult=true;
                $nbModifications = 0;
                $arrayInfosConnectionsUtilisateurs = $this->getInfosConnexions($rep->idUtilisateur, true); // on est en mode administration
                
                $derniereConnexion = '';
                if ($arrayInfosConnectionsUtilisateurs['derniereConnexion']=="c'est votre première connexion.")
                {
                    $derniereConnexion ="";
                }
                else
                {
                    $derniereConnexion = $this->date->toFrench($arrayInfosConnectionsUtilisateurs['derniereConnexion']);
                }
                
                
                $t->assign_block_vars('l',  array(
                    'url'         => $this->creerUrl('', 'utilisateurDetail', array('idUtilisateur' => $rep->idUtilisateur)), 
                    'nom'         => $rep->nom, 
                    'prenom'      => $rep->prenom, 
                    'nbConnexions' => $arrayInfosConnectionsUtilisateurs['nbConnexions'], 
                    'derniereConnexion'=>$derniereConnexion, 
                    'nbParticipationsEvenements'=>$arrayInfosConnectionsUtilisateurs["nbParticipationsEvenements"], 
                    'dateCreation'=>$this->date->toFrench($rep->dateCreation)
                    ));
                
                
                $t->assign_vars(array("pagination"=>$arrayPagination["html"]));
                $t->assign_vars(array("infosStatistiques"=>"Nombre de compte actifs : <b>".$this->getNbUtilisateursInscrits()."</b>,  nombre d'abonnés à l'alerte mail : <b>".$this->getNbUtilisateursAbonnesAlerteMail()."</b>.<br><br>"));
                            
                $t->assign_vars(array("urlTriNomPrenom"=>" href='#' onclick=\"document.getElementById('classementNomPrenom').value='1';document.getElementById('classementNbConnexions').value='0';document.getElementById('classementDernieresConnexions').value='0';document.getElementById('classementDateCreation').value='0';document.getElementById('classementNbParticipations').value='0';document.getElementById('formRechercheUtilisateur').submit();\" "));
                $t->assign_vars(array("urlTriNbConnexions"=>" href='#' onclick=\"document.getElementById('classementNomPrenom').value='0';document.getElementById('classementNbConnexions').value='1';document.getElementById('classementDernieresConnexions').value='0';document.getElementById('classementDateCreation').value='0';document.getElementById('classementNbParticipations').value='0';document.getElementById('formRechercheUtilisateur').submit();\" "));
                $t->assign_vars(array("urlTriDernieresConnexions"=>" href='#' onclick=\"document.getElementById('classementNomPrenom').value='0';document.getElementById('classementDernieresConnexions').value='1';document.getElementById('classementNbConnexions').value='0';document.getElementById('classementDateCreation').value='0';document.getElementById('classementNbParticipations').value='0';document.getElementById('formRechercheUtilisateur').submit();\" "));
                $t->assign_vars(array("urlTriDateCreation"=>" href='#' onclick=\"document.getElementById('classementNomPrenom').value='0';document.getElementById('classementDernieresConnexions').value='0';document.getElementById('classementNbConnexions').value='0';document.getElementById('classementDateCreation').value='1';document.getElementById('classementNbParticipations').value='0';document.getElementById('formRechercheUtilisateur').submit();\" "));
                $t->assign_vars(array("urlTriNbParticipations"=>" href='#' onclick=\"document.getElementById('classementNomPrenom').value='0';document.getElementById('classementDernieresConnexions').value='0';document.getElementById('classementNbConnexions').value='0';document.getElementById('classementDateCreation').value='0';document.getElementById('classementNbParticipations').value='1';document.getElementById('formRechercheUtilisateur').submit();\" "));
            }
            
            if (!isset($hasResult)) {
               $t->assign_vars(array("erreur"=>"<b>"._("Aucun résultat")."</b>"));
            }
            
            ob_start();
            $t->pparse('ev');
            $html.=ob_get_contents();
            ob_end_clean();

        }
        else {
            $html .= 'aucun résultat !';
        }
        
        return $html;
    }
    
    // *********************************************************************************************************************************************
    // renvoi l'adresse mail de l'utilisateur
    // *********************************************************************************************************************************************
    public function getMailUtilisateur($idUtilisateur=0) 
    {
        $req = "select mail from utilisateur where idUtilisateur = '".$idUtilisateur."'";
        $res = $this->connexionBdd->requete($req);
        $mail="";
        if (mysql_num_rows($res) ==1 )
        {
            $fetch = mysql_fetch_assoc($res);
            $mail=$fetch['mail'];
        } else {
            echo "Il y a un problème avec votre compte ,  veuillez contacter l'administrateur.";
        }
        
        return $mail;
    }
    
    // *********************************************************************************************************************************
    // confirmation de l'activation d'un compte par un administrateur
    // *********************************************************************************************************************************
    public function confirmInscription()
    {
        $html="";
        $mail = new mailObject();    
        // est ce que l'utilisateur courant a les droits pour confirmer une inscription ,  est il admin ?
        // si oui on recupere ces identifiants afin de stocker dans la base l'id de la personne qui a confirmé l'inscription
        //$authentification = new archiAuthentification();
        //if ($authentification->estConnecte() && $authentification->estAdmin())
        //{
            //$idAdministrateur = $authentification->getIdUtilisateur();
            
            if (isset($this->variablesGet['archiIdUtilisateur']) && isset($this->variablesGet['archiMd5']))
            {
                // on verifie que le compte n'a pas deja ete activé
                $requeteActif = "select compteActif, idActivateur, compteBanni from utilisateur where idUtilisateur = '".$this->variablesGet['archiIdUtilisateur']."' and       motDePasse = '".$this->variablesGet['archiMd5']."'";
                $resActif = $this->connexionBdd->requete($requeteActif);
                if (mysql_num_rows($resActif)==0)
                {
                    echo "Le compte n'existe pas. Veuillez contacter l'administrateur.<br>";
                }
                else
                {
                    // le compte existe
                    // on verifie que les droits n'ont pas deja été attribués
                    $fetchActif = mysql_fetch_assoc($resActif);
                    if ($fetchActif['compteBanni']=='1')
                    {
                        echo "Ce compte a été banni<br>";
                    }
                    elseif ($fetchActif['compteActif']=='1')
                    {
                        //$mailActivateur = $this->getMailUtilisateur($fetchActif['idActivateur']);
                        echo "Le compte a déjà été activé<br>";
                    
                    }
                    else
                    {
                        // on le met a jour ,  activation
                        $requeteMaj = "update utilisateur set compteActif='1',  alerteMail='1',  alerteCommentaires='1',  alerteAdresses='1' where idUtilisateur='".$this->variablesGet['archiIdUtilisateur']."' and motDePasse = '".$this->variablesGet['archiMd5']."'";//idActivateur='".$idAdministrateur."'
                        
                        if ($resMaj = $this->connexionBdd->requete($requeteMaj))
                        {
                            echo "Compte mis a jour";
                            // envoi du mail a l'utilisateur
                            $mailUtilisateur = $this->getMailUtilisateur($this->variablesGet['archiIdUtilisateur']);
                            $message = "Bonjour,  <br><br>";
                            $message .="Votre compte sur <a href='http://www.archi-strasbourg.org'>http://www.archi-strasbourg.org</a> a été activé.<br>";
                            $message .="Votre login correspond à votre adresse mail : ".$mailUtilisateur."<br>";
                            $message .="A bientôt sur archi-strasbourg.org!<br>";
                            
                            $mail->sendMail($mail->getSiteMail(), $mailUtilisateur, 'archi-strasbourg.org : Votre compte est activé', $message, true);
                            echo "Le compte ".$mailUtilisateur." est maintenant actif.<br>";
                        }
                        else
                        {
                            echo "Erreur dans la mise a jour du compte. Veuillez contacter l'administrateur<br>";
                        }
                    }
                }
            }        
        //}
        //else
        //{
        //    echo "Vous n'etes pas connecté,  ou vous n'avez pas les droits requis pour effectuer cette operation. Veuillez contacter l'administrateur.<br>";
        //}
        
        return $html;
    }
    
    public function getParametrePredefini( $nom )
    {
        $retour = NULL;
        if (isset($_COOKIE[$nom]))
            $retour = $_COOKIE[$nom];

        return $retour;
    }
    
    public function setParametrePredefini( $nom,  $valeur)
    {
        // défini le cookie pour 6 mois
        setcookie($nom,  $valeur,  $_SERVER['REQUEST_TIME']+60*60*24*182);
    }
    
    // ******************************************************************************************************************************
    // renvoi un tableau 
    // ******************************************************************************************************************************
    public function getArrayInfosFromUtilisateur($idUtilisateur=0, $params = array())
    {
        $sqlListeChamps = "nom, prenom, mail, idVilleFavoris, compteActif, idProfil, alerteCommentaires, alerteAdresses, idActivateur, dateCreation, urlSiteWeb, displayProfilContactForm";
        if (isset($params['listeChamps']) && $params['listeChamps']!='')
        {
            $sqlListeChamps = $params['listeChamps'];
        }
        $query="select $sqlListeChamps from utilisateur where idUtilisateur = '".$idUtilisateur."'";
        $res = $this->connexionBdd->requete($query);
        return mysql_fetch_assoc($res);
    }
    
    public function getIdProfilFromUtilisateur($idUtilisateur=0)
    {
        $query="select idProfil from utilisateur where idUtilisateur = '".$idUtilisateur."'";
        $res = $this->connexionBdd->requete($query);
        $fetch = mysql_fetch_assoc($res);
        return $fetch['idProfil'];
    }
    
    public function getIdProfil($idUtilisateur=0)
    {
        return $this->getIdProfilFromUtilisateur($idUtilisateur);
    }
    
    // ******************************************************************************************************************************
    // renvoi les informations de connexions de l'utilisateur
    // ******************************************************************************************************************************
    public function getInfosConnexions($idUtilisateur=0, $modeAdministrationSite = false)
    {
        $reqNbConnexions = "SELECT count(idConnexion) as nbConnexions FROM connexionsUtilisateurs WHERE idUtilisateur = '".$idUtilisateur."'";
        $resNbConnexions = $this->connexionBdd->requete($reqNbConnexions);
        $fetchNbConnexions = mysql_fetch_assoc($resNbConnexions);
        $nbConnexions = $fetchNbConnexions['nbConnexions'];
        
        // pour l'utilisateur courant,  
        // on ne cherche pas la date de la connexion courante,  mais la date de la derniere connexion
        $reqLastConnexion = "
                    
                    SELECT max(date) as derniereConnexion 
                    FROM connexionsUtilisateurs 
                    WHERE idUtilisateur = '".$idUtilisateur."'";
                    
        if ($modeAdministrationSite==false)
        {
            $reqLastConnexion .= " AND idConnexion NOT IN (SELECT max(idConnexion) FROM connexionsUtilisateurs WHERE idUtilisateur = '".$idUtilisateur."') ";
        }
        
        $resLastConnexion = $this->connexionBdd->requete($reqLastConnexion);
        $fetchLastConnexion = mysql_fetch_assoc($resLastConnexion);
        $dateLastConnexion = $fetchLastConnexion['derniereConnexion'];
        
        $premiereConnexion=false;
        if ($dateLastConnexion=='')
        {
            $premiereConnexion = true;
            $dateLastConnexion="c'est votre première connexion.";
        }
        
        // nb modifications evenements
        $reqNbParticipationsEvenements = "SELECT 0 FROM historiqueEvenement WHERE idUtilisateur='".$idUtilisateur."'";
        $resNbParticipationsEvenements = $this->connexionBdd->requete($reqNbParticipationsEvenements);
        $reqNbParticipationsImages = "SELECT 0 FROM historiqueImage WHERE idUtilisateur='".$idUtilisateur."'";
        $resNbParticipationsImages = $this->connexionBdd->requete($reqNbParticipationsImages);
        
        
        return array('nbConnexions'=>$nbConnexions, 'derniereConnexion'=>$dateLastConnexion, 'isPremiereConnexion'=>$premiereConnexion, 'nbParticipationsEvenements'=>mysql_num_rows($resNbParticipationsEvenements)+mysql_num_rows($resNbParticipationsImages));
    }
    

    // ******************************************************************************************************************************
    // renvoi le createur d'une adresse a partir de different parametre possibles en entree de la fonction
    // ******************************************************************************************************************************
    public function getCreatorsFromAdresseFrom($id=0, $type='')
    {
        $utilisateurs = array(); // on retourne un tableau car il peut y avoir plusieur auteur d'adresses pour un meme groupe d'adresses par exemple
        
        switch($type)
        {
            case 'idHistoriqueEvenement':
            //$idHistoriqueEvenementNouveau
                $req = "
                                    SELECT ha1.idUtilisateur as idUtilisateur
                                    FROM historiqueAdresse ha2,  historiqueAdresse ha1
                                    LEFT JOIN historiqueEvenement he ON he.idHistoriqueEvenement = '".$id."'
                                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he.idEvenement
                                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                                    LEFT JOIN utilisateur u ON u.idUtilisateur = ha1.idUtilisateur
                                    WHERE ha2.idAdresse = ha1.idAdresse
                                    AND ha1.idAdresse = ae.idAdresse
                                    GROUP BY ha1.idAdresse,  ha1.idHistoriqueAdresse
                                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $utilisateurs[] = $fetch['idUtilisateur'];
                }
                $utilisateurs = array_unique($utilisateurs);
            break;
            case 'idEvenementGroupeAdresse':
                $req = "
                
                    SELECT ha1.idUtilisateur as idUtilisateur
                    FROM historiqueAdresse ha1
                    LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                    WHERE ae.idEvenement = '".$id."'

                ";
                
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $utilisateurs[] = $fetch['idUtilisateur'];
                }
                $utilisateurs = array_unique($utilisateurs);
            break;
            case 'idEvenement':
            
                $req = "
                
                                    SELECT ha1.idUtilisateur as idUtilisateur
                                    FROM historiqueAdresse ha2,  historiqueAdresse ha1
                                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$id."'
                                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                                    LEFT JOIN utilisateur u ON u.idUtilisateur = ha1.idUtilisateur
                                    WHERE ha2.idAdresse = ha1.idAdresse
                                    AND ha1.idAdresse = ae.idAdresse
                                    GROUP BY ha1.idAdresse,  ha1.idHistoriqueAdresse
                                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                
                ";
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $utilisateurs[] = $fetch['idUtilisateur'];
                }
                $utilisateurs = array_unique($utilisateurs);
            break;
            
            case 'idHistoriqueImage':
                $req = "
                
                                    SELECT ha1.idUtilisateur as idUtilisateur
                                    FROM historiqueAdresse ha2,  historiqueAdresse ha1
                                    LEFT JOIN historiqueImage hi ON hi.idHistoriqueImage = '".$id."'
                                    LEFT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ei.idEvenement
                                    WHERE ha2.idAdresse = ha1.idAdresse
                                    AND ha1.idAdresse = ae.idAdresse
                                    GROUP BY ha1.idAdresse,  ha1.idHistoriqueAdresse
                                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                
                ";
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $utilisateurs[] = $fetch['idUtilisateur'];
                }
                $utilisateurs = array_unique($utilisateurs);
            break;
            
            case 'idImage':
                $req = "
                                    SELECT ha1.idUtilisateur as idUtilisateur, ha1.idAdresse as idAdresse
                                    FROM historiqueAdresse ha2,  historiqueAdresse ha1
                                    LEFT JOIN historiqueImage hi1 ON hi1.idImage = '".$id."'
                                    LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
                                    LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
                                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                                    WHERE ha2.idAdresse = ha1.idAdresse
                                    AND ha1.idAdresse = ae.idAdresse
                                    GROUP BY ha1.idAdresse, hi1.idImage,  ha1.idHistoriqueAdresse, hi1.idHistoriqueImage
                                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                ";
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $utilisateurs[] = array('idUtilisateur'=>$fetch['idUtilisateur'], 'idAdresse'=>$fetch['idAdresse']);
                }
            break;
        }
        
        return $utilisateurs;
    }
    
    // recupere les participants aux commentaires sur les groupes d'adresses
    public function getParticipantsCommentaires($idEvenementGroupeAdresse=0)
    {
        $utilisateurs = array();
        $req = "SELECT idUtilisateur FROM commentaires WHERE idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."'";
        $res = $this->connexionBdd->requete($req);
        while($fetch = mysql_fetch_assoc($res))
        {
            $utilisateurs[] = $fetch['idUtilisateur'];
        }
        return array_unique($utilisateurs);
    }
    
    // affiche le formulaire ou la personne rentre son adresse mail pour reinitialiser son mot de passe
    public function afficheFormulaireMotDePasseOublie($configFields = array())
    {
        if (count($configFields)==0)
            $configFields = $this->getFieldsFromMotDePasseOublie();
            
        $configForm = array(
        'titrePage'=>_("Vous avez perdu votre mot de passe"), 
        'fields'=>$configFields, 
        'submitButtonValue'=>_("Envoyer"), 
        'formAction'=>$this->creerUrl('handleMotDePasseOublie'), 
        'codeHtmlBeforeSubmitButton'=>_("Une fois ce formulaire validé,  un mail contenant un lien vous permettant de choisir un nouveau mot de passe vous sera envoyé.")
        );
    
        $form  = new formGenerator();
        
        return $form->afficherFromArray($configForm);
        
    }
    
    private function getFieldsFromMotDePasseOublie()
    {
        return array(
                        'adresseMail'=>array('type'=>'text', 'default'=>'', 'htmlCode'=>'', 'libelle'=>_("Entrez votre adresse mail :"), 'required'=>true, 'error'=>'', 'value'=>'', 'htmlCode'=>"style='width:300px;'")
                    );
    }
    
    // envoi du mail a l'utilisateur pour qu'il puisse redefinir son mot de passe
    public function envoiMailMotDePasseOublie()
    {
        $html="";
        $configFields = $this->getFieldsFromMotDePasseOublie();
        $f = new formGenerator();
        $errors = $f->getArrayFromPost($configFields);
        
        if (count($errors)>0)
        {
            $html .= $this->afficheFormulaireMotDePasseOublie($configFields);
        } else {
            $erreur = new objetErreur();
            // verification de l'existence du mail dans la base de donnée
            $reqVerif = "SELECT idUtilisateur FROM utilisateur WHERE mail='".$configFields['adresseMail']['value']."'";
            $resVerif = $this->connexionBdd->requete($reqVerif);
            if (mysql_num_rows($resVerif)==1)
            {
                $fetch = mysql_fetch_assoc($resVerif);
                
                $urlMd5 = md5("idUtilisateur=".$fetch['idUtilisateur']."_".$configFields['adresseMail']['value']);
                // ok
                // envoi du mail
                $message = _("Vous avez fais une demande de nouveau mot de passe,  veuillez cliquer")." <a href='".$this->creerUrl('', 'nouveauMotDePasse', array('check'=>$urlMd5, 'mail'=>$configFields['adresseMail']['value']))."'><font color=blue>"._("ici")."</font></a> "._("pour pouvoir redéfinir votre mot de passe sur le site")." www.archi-strasbourg.org<br>";
                $message.=_("ou copier coller le lien suivant dans votre navigateur :")." <br>";
                $message.= "<a href='".$this->creerUrl('', 'nouveauMotDePasse', array('check'=>$urlMd5, 'mail'=>$configFields['adresseMail']['value']))."'><font color=blue>".$this->creerUrl('', 'nouveauMotDePasse', array('check'=>$urlMd5, 'mail'=>$configFields['adresseMail']['value']))."</font></a>";
                $message.= "<br><br><br>"._("L'équipe archi-strasbourg.org")."<br>";
                
                $sujet = _("Demande de changement de mot de passe")." - www.archi-strasbourg.org";
                
                $mail = new mailObject();
                $mail->sendMail($mail->getSiteMail(), $configFields['adresseMail']['value'], $sujet, $message, true);
                
                
                // envoi de l'info aux administrateurs
                $messageAdmins = _("Un utilisateur a fait une demande de changement de mot de passe.")."<br>".$configFields['adresseMail']['value'];
                $sujetAdmins = _("Demande de changement de mot de passe sur")." archi-strasbourg.org";
                
                $mail->sendMailToAdministrators($mail->getSiteMail(), $sujetAdmins, $messageAdmins, '', true);
                $this->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$messageAdmins, 'idTypeMailRegroupement'=>8, 'criteres'=>""));
                
                $html.= _("Un mail vous a été envoyé contenant un lien pour redéfinir votre mot de passe.")."<br>";
            }
            elseif (mysql_num_rows($resVerif)>1)
            {
                // probleme ,  plusieurs compte avec la meme adresse
                $erreur->ajouter(_("Attention,  plusieurs comptes ont ce mail comme identifiant. Merci de contacter l'administrateur du site."));
            }
            elseif (mysql_num_rows($resVerif)==0)
            {
                $erreur->ajouter(_("Le mail n'a pas été trouvé dans la base de données,  merci de vérifier le mail"));
            }
            
            if ($erreur->getNbErreurs()>0) // erreurs en rapport avec la verification de l'adresse mail ( et non pas les erreurs du formulaire)
            {
                $html.= $erreur->afficher();
                $html.= $this->afficheFormulaireMotDePasseOublie();
            }

        }
        
        return $html;        
    }
    
    
    private function getFieldsFromChangementMotDePasseOublie()
    {
        return array(
                        'mdp1'=>array('type'=>'password', 'default'=>'', 'htmlCode'=>'', 'libelle'=>'Nouveau mot de passe :', 'required'=>true, 'error'=>'', 'value'=>'', 'htmlCode'=>""), 
                        'mdp2'=>array('type'=>'password', 'default'=>'', 'htmlCode'=>'', 'libelle'=>'Confirmez votre nouveau mot de passe :', 'required'=>true, 'error'=>'', 'value'=>'', 'htmlCode'=>""), 
                        'mail'=>array('type'=>'hidden', 'default'=>'', 'htmlCode'=>'', 'libelle'=>'', 'required'=>true, 'error'=>'', 'value'=>'', 'htmlCode'=>""), 
                        'md5'=>array('type'=>'hidden', 'default'=>'', 'htmlCode'=>'', 'libelle'=>'', 'required'=>true, 'error'=>'', 'value'=>'', 'htmlCode'=>"")
                    );
    }
    
    // affiche le formulaire permettant a un utilisateur de rentrer un nouveau mot de passe
    public function afficheFormulaireChangementMotDePasseOublie()
    {
        $html="";
        
        if (isset($this->variablesGet['check']) && isset($this->variablesGet['mail']) && $this->verifMD5ChangementMotDePasse($this->variablesGet['check'], $this->variablesGet['mail']))
        {
            
            $configFields = $this->getFieldsFromChangementMotDePasseOublie();
            
            $configFields['mail']['default']=$this->variablesGet['mail'];
            $configFields['md5']['default']=$this->variablesGet['check'];
            
            $configForm = array(
            'titrePage'=>_("Choisissez un nouveau mot de passe"), 
            'fields'=>$configFields, 
            'submitButtonValue'=>_("Envoyer"), 
            'formAction'=>$this->creerUrl('handleMotDePasseOublieNouveauMotDePasse')
            );
        
            $form  = new formGenerator();
            
            $html = $form->afficherFromArray($configForm);
        } else {
            $erreur = new objetErreur();
            $erreur->ajouter(_("Erreur dans le lien. Merci de contacter l'administrateur.")."<br>");
            $html.=$erreur->afficher();
        }
        
        
        return $html;
    }
    
    // gestion du changement de mot de passe quand on est passé par le formulaire "oublie de mot de passe"
    public function changementMotDePasseOublie()
    {
        $html="";
        $configFields = $this->getFieldsFromChangementMotDePasseOublie();
        $f = new formGenerator();
        
        $errors = $f->getArrayFromPost($configFields);
        
        $erreur = new objetErreur();
        
        if (isset($this->variablesPost['mail']) && $this->variablesPost['mail']!='' && isset($this->variablesPost['md5']) && $this->verifMD5ChangementMotDePasse($this->variablesPost['md5'],  $this->variablesPost['mail']))
        {
            if ($configFields['mdp1']['value']==$configFields['mdp2']['value'])
            {
                if (pia_strlen($configFields['mdp1']['value'])>=5)
                {
                    // mise a jour du mot de passe
                    $reqMaj = "update utilisateur set motDePasse=md5('".$configFields['mdp1']['value']."') where mail='".$this->variablesPost['mail']."'";
                    $resMaj = $this->connexionBdd->requete($reqMaj);
                    
                    $html.=_("Votre mot de passe a été mis à jour.")."<br>";
                }
                else
                {
                    $erreur->ajouter(_("Attention un mot de passe doit comporter au moins 5 caractères."));
                }
            }
            else
            {
                $erreur->ajouter(_("Attention le mot de passe et sa vérification ne correspondent pas."));
            }    

        } else {
            $erreur->ajouter(_("Problème changement de mot de passe : il y a un problème avec le formulaire. Merci de contacter l'administrateur."));
        }
        
        if ($erreur->getNbErreurs()>0)
        {
            $html.=$erreur->afficher();
            $html.=$this->afficheFormulaireChangementMotDePasseOublie();
        }
        
        return $html;
    }
    
    
    
    private function verifMD5ChangementMotDePasse($md5="", $mail="")
    {
        $ok = false;
        $mail = trim($mail);
        $reqVerif = "SELECT idUtilisateur FROM utilisateur WHERE mail='".$mail."'";
        
        $resVerif = $this->connexionBdd->requete($reqVerif);
        
        if (mysql_num_rows($resVerif)==1)
        {
            // est ce que la construction du md5 correspond sachant qu'elle est du type idUtilisateur=..._mailUtilisateur
            $fetch = mysql_fetch_assoc($resVerif);
            
            if ($md5 == md5("idUtilisateur=".$fetch['idUtilisateur']."_".$mail))
            {
                // ok cela correspond
                $ok = true;
            }            
        }
        
        return $ok;
    }
    
    // est ce que l'utilisateur a acces a l'element bloqué par l'element identifié par le tag
    public function isAuthorized($tagName='', $idUtilisateur=0)
    {
        $retour=false;
        $d = new droitsObject();
        $idProfil = $this->getIdProfilFromUtilisateur($idUtilisateur);
        if ($d->isAuthorized($tagName, $idProfil))
        {
            $retour=true;
        }
        
        return $retour;
    }
    
    // enregistre la liste des villes dont un moderateur s'occupe
    public function enregistreListeVillesModerateur()
    {
        $retour="";
        
        if (isset($this->variablesPost['idUtilisateurModerateur']))
        {
            // on supprime d'abord les anciennes valeurs s'il y en a
            $reqDelete = "DELETE FROM utilisateurModerateurVille WHERE idUtilisateur='".$this->variablesPost['idUtilisateurModerateur']."'";
            $resDelete = $this->connexionBdd->requete($reqDelete);
            
            // on ajoute les villes
            if (isset($this->variablesPost['idVillesModerateur']))
            {
                foreach($this->variablesPost['idVillesModerateur'] as $indice => $value)
                {
                    $reqVillesModeration="INSERT INTO utilisateurModerateurVille (idUtilisateur, idVille) VALUES ('".$this->variablesPost['idUtilisateurModerateur']."', '".$value."')";
                    $resVillesModeration = $this->connexionBdd->requete($reqVillesModeration);
                }
            }
        }
        
        return $retour;
    }
    
    
    // renvoie la liste des villes moderees par un utilisateur passé en parametres
    public function getArrayVillesModereesPar($idUtilisateur=0)
    {
        $retour=array();
        $req = "SELECT distinct idVille FROM utilisateurModerateurVille WHERE idUtilisateur=$idUtilisateur";
        $res = $this->connexionBdd->requete($req);
        if (mysql_num_rows($res)>0)
        {
            while($fetch = mysql_fetch_assoc($res))
            {
                $retour[]=$fetch['idVille'];
            }
        }
        
        return $retour;
    }
    
    // renvoi la liste des moderateurs concernant une ville
    public function getArrayIdModerateursActifsFromVille($idVille=0, $params=array())
    {
        $retour = array();
        
        $sqlWhere="";
        
        if (isset($params['sqlWhere']))
        {
            $sqlWhere = $params['sqlWhere'];
        }
        
        $req = "
                SELECT distinct u.idUtilisateur as idUtilisateur
                FROM utilisateur u
                LEFT JOIN utilisateurModerateurVille umv ON umv.idUtilisateur = u.idUtilisateur
                WHERE umv.idVille = '".$idVille."'
                AND u.compteActif='1'
                $sqlWhere
        ";
        
        $res = $this->connexionBdd->requete($req);
        
        while($fetch = mysql_fetch_assoc($res))
        {
            $retour[] = $fetch['idUtilisateur'];
        }
        
        return $retour;
    }
    
    // est ce que l'utilisateur est moderateur de la ville dont l'adresse est renseigné dans id ,  cet id peut etre un idImage,  idAdresse,  idEvenement ,  idEvenementGroupeAdresse ,  ce qui doit etre précisé dans typeId
    public function isModerateurFromVille($idUtilisateur=0, $id=0, $typeId='')
    {
        $isModerateur=false;

        // on verifie quand meme si l'utilisateur est moderateur ...
        if ($this->getIdProfil($idUtilisateur)=='3' && $typeId!='' && $idUtilisateur!=0 && $id!=0)
        {
            $arrayVillesModerees = $this->getArrayVillesModereesPar($idUtilisateur);
            $adresse = new archiAdresse();
            $idVilleAdresseCourante = $adresse->getIdVilleFrom($id, $typeId);
            
            if (in_array($idVilleAdresseCourante, $arrayVillesModerees))
            {
                $isModerateur=true;
            }
        }
        
        return $isModerateur;
    }
    
    public function isMailEnvoiImmediat($idUtilisateur=0)
    {
        $retour = false;
        if ($idUtilisateur!='' && $idUtilisateur!='0')
        {
            $req = "SELECT idPeriodeEnvoiMailsRegroupes FROM utilisateur WHERE idUtilisateur='".$idUtilisateur."'";
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            if ($fetch['idPeriodeEnvoiMailsRegroupes']=='1' || $fetch['idPeriodeEnvoiMailsRegroupes']=='0') // 1 = envoi de mail immediat ou 0
            {
                $retour = true;
            }
            
        }
        return $retour;
    }
    
    public function ajouteMailEnvoiRegroupes($params = array())
    {
        if (isset($params['contenu']) && isset($params['idDestinataire']) && isset($params['idTypeMailRegroupement']) && $params['contenu']!='' && $params['idDestinataire']!='' && $params['idTypeMailRegroupement']!='')
        {
            $req = "INSERT INTO mailsEnvoiMailsRegroupes (contenu, dateHeure, idUtilisateur, idTypeMailRegroupement) VALUES (\"".$params['contenu']."\", now(), '".$params['idDestinataire']."', '".$params['idTypeMailRegroupement']."')";
            $res = $this->connexionBdd->requete($req);
            
        } else {
            echo "ATTENTION : parametre manquant dans la fonction archiUtilisateur::ajouteMailEnvoiRegroupe,  veuillez contacter l'administrateur<br>";
        }
    
    }
    
    public function ajouteMailEnvoiRegroupesAdministrateurs($params = array())
    {
        if (isset($params['contenu']) && isset($params['idTypeMailRegroupement']) && $params['contenu']!='' && $params['idTypeMailRegroupement']!='')
        {
            $criteres = "";
            if (isset($params['criteres']))
            {
                $criteres = $params['criteres'];
            }
            
            $authentification = new archiAuthentification();
            $idUtilisateur = '0';
            if ($authentification->estConnecte())
                $idUtilisateur = $authentification->getIdUtilisateur();
        
            // n'envoi pas le mail si l'utilisateur courant est lui meme admin
            $sqlNoSendToAdmin="";
            if ($authentification->estAdmin())
            {
                $sqlNoSendToAdmin = "and idUtilisateur!='".$idUtilisateur."'";
            }
            
            // la requete exclut les admins qui recoivent le mail en immediat
            $reqAdmins = "SELECT idUtilisateur from utilisateur where idProfil='4' and compteActif='1' and idPeriodeEnvoiMailsRegroupes<>'1' and idPeriodeEnvoiMailsRegroupes<>'0' ".$criteres." ".$sqlNoSendToAdmin;
            $resAdmins = $this->connexionBdd->requete($reqAdmins);
            while($fetchAdmins = mysql_fetch_assoc($resAdmins))
            {        
                $req = "INSERT INTO mailsEnvoiMailsRegroupes (contenu, dateHeure, idUtilisateur, idTypeMailRegroupement) VALUES (\"".$params['contenu']."\", now(), '".$fetchAdmins['idUtilisateur']."', '".$params['idTypeMailRegroupement']."')";
                $res = $this->connexionBdd->requete($req);
            }
            
        } else {
            echo "ATTENTION : parametre manquant dans la fonction archiUtilisateur::ajouteMailEnvoiRegroupe,  veuillez contacter l'administrateur<br>";
        }
    
    }
    
    // si l'utilisateur a un avatar cette fonction renvoie l'image
    // sinon elle renvoi une image par defaut
    // si pas d'idUtilisateur en parametre ,  on renvoi l'image de l'avatar pour l'utilisateur connecté
    public function getImageAvatar($params = array())
    {
        $retour = "";
        $idUtilisateur = 0;
        $authentification = new archiAuthentification();
        
        if ($authentification->estConnecte())
            $idUtilisateur = $authentification->getIdUtilisateur();
        
        if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='')
            $idUtilisateur = $params['idUtilisateur'];
        
        
        if (file_exists($this->getCheminPhysique()."/images/avatar/".$idUtilisateur."/avatar.jpg"))
        {
            $retour = $this->getUrlImage()."avatar/".$idUtilisateur."/avatar.jpg";
        } else {
            $retour = $this->getUrlImage()."avatar/default.jpg";
        }
        
        return $retour;
    }
    
    public function afficheProfilPublique($params = array())
    {
        $html="";
        
        $idUtilisateur = 0;
        
        if (isset($this->variablesGet['archiIdUtilisateur']) && $this->variablesGet['archiIdUtilisateur']!='')
        {
            $idUtilisateur = $this->variablesGet['archiIdUtilisateur'];
        }
        
        if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='')
        {
            $idUtilisateur = $params['idUtilisateur'];
        }
        
        
        if (!$this->isUtilisateurBanni(array('idUtilisateur'=>$idUtilisateur)))
        {
            $infosArray = $this->getArrayInfosFromUtilisateur($idUtilisateur);
            $avatar = $this->getImageAvatar(array('idUtilisateur'=>$idUtilisateur));
            
            $html.="<h1>Profil</h1>";

            $nbParticipations = $this->getNbParticipationsUtilisateur(array('idUtilisateur'=>$idUtilisateur));

            $d = new dateObject();
            
            $dateCreationCompte = "";
            if ($infosArray['dateCreation']!='0000-00-00 00:00:00')
            {
                $dateCreationCompte = " - Date de création du compte : ".$d->toFrenchAffichage($infosArray['dateCreation'])."<br>";
            }
            
            $profil = $this->getLibelleProfil(array('idProfil'=>$infosArray['idProfil']));
            
            $villesModereesArray = $this->getArrayVillesModereesPar($idUtilisateur);
            $villesModerees = "";
            if ($infosArray['idProfil']==3)
            {
                $libelleVillesModerees = array();
                $a = new archiAdresse();
                foreach($villesModereesArray as $indice => $idVille)
                {
                    $fetchVille = $a->getInfosVille($idVille, array('fieldList'=>"v.nom as nom"));
                    $libelleVillesModerees[] = $fetchVille['nom'];
                }
                
                if (count($libelleVillesModerees)>0)
                {
                    foreach($libelleVillesModerees as $indice => $nomVille)
                    {
                        $villesModerees .= "<a href='".$this->urlRacine."$nomVille/'>$nomVille</a>,  ";
                    }
                    
                    $villesModerees = "- Villes modérées : ".pia_substr($villesModerees, 0, -2);
                }
                
            }
            
            $urlSiteWeb ="";
            
            if ($infosArray['urlSiteWeb']!='')
            {
                $urlSiteWeb = "Son site : <a href='".$infosArray['urlSiteWeb']."' target='_blank'>".$infosArray['urlSiteWeb']."</a><br>";
            }
            
            
            
            $html.="
            <div style='width:750px;'>
            
            <div style='float:left;width:150px;'>
                <img src='$avatar' border=0 style='padding:10px;'>
            </div>
            <div style='float:left;width:600px;'>
                <b>".ucfirst($infosArray['nom'])." ".ucfirst($infosArray['prenom'])."</b><br>
                - Nombre de participations : $nbParticipations (ajouts et modifications d'images,  d'événements et commentaires en tant qu'utilisateur inscrit)<br>
                $dateCreationCompte
                - Cette personne à le statut : <b>$profil</b> sur archi-strasbourg.org<br>
                $villesModerees
                $urlSiteWeb

            ";
            $mail = new mailObject();
            
            if ($infosArray['displayProfilContactForm']=='1' && $infosArray['mail']!='' && $mail->isMail($infosArray['mail'])) 
            {
                $authentification = new archiAuthentification();
                
                $mailUtilisateurConnecte = "";
                if ($authentification->estConnecte())
                {
                    $idUtilisateurConnecte = $authentification->getIdUtilisateur();
                    $mailUtilisateurConnecte = $this->getMailUtilisateur($idUtilisateurConnecte);
                    if (!$mail->isMail($mailUtilisateurConnecte))
                    {
                        $mailUtilisateurConnecte = "";
                    }
                }
                
                $f = new formGenerator();
                $bb = new bbCodeObject();
                
                
                
                $configBoutonsBBCode = array('formName'=>'messagePrive', 'fieldName'=>'message', 'noUrlInterneButton'=>true);
                
                $help = $this->getHelpMessages('helpEvenement');
                foreach($help as $index=>$value)
                {
                    $configBoutonsBBCode[$index]=$value;
                }
                $configBoutonsBBCode["msgQuote"]="Selectionnez une partie de votre texte pour le mettre entre quotes";
                $configBoutonsBBCode["msgUrlExterne"]="Tapez une url commencant par http:// ,  et selectionnez la pour en faire un lien";
                $arrayBBCode = $bb->getBoutonsMiseEnFormeTextArea($configBoutonsBBCode);
                
                if ($authentification->estConnecte())
                {
                    $configFieldsContact = array(
                        'idUtilisateurDestinataire'=>array('type'=>'hidden', 'value'=>'', 'forceValueTo'=>$idUtilisateur, 'htmlCode'=>'', 'default'=>'', 'error'=>'', 'required'=>true), 
                        'mailEnvoyeur'=>array('type'=>'email', 'value'=>'', 'forceValueTo'=>$mailUtilisateurConnecte, 'htmlCode'=>'', 'default'=>'', 'libelle'=>'Votre mail', 'error'=>'', 'required'=>true), 
                        'message'=>array('type'=>'bigText', 'value'=>'', 'htmlCode'=>"style='width:400px;height:100px;'", 'default'=>'', 'libelle'=>'Votre message', 'error'=>'', 'required'=>true, 'htmlCodeBeforeField'=>$arrayBBCode['boutonsHTML'])
                    );
                }
                else
                {
                    $configFieldsContact = array(
                        'idUtilisateurDestinataire'=>array('type'=>'hidden', 'value'=>'', 'forceValueTo'=>$idUtilisateur, 'htmlCode'=>'', 'default'=>'', 'error'=>'', 'required'=>true), 
                        'mailEnvoyeur'=>array('type'=>'email', 'value'=>'', 'forceValueTo'=>$mailUtilisateurConnecte, 'htmlCode'=>'', 'default'=>'', 'libelle'=>'Votre mail', 'error'=>'', 'required'=>true), 
                        'message'=>array('type'=>'bigText', 'value'=>'', 'htmlCode'=>"style='width:400px;height:100px;'", 'default'=>'', 'libelle'=>'Votre message', 'error'=>'', 'required'=>true, 'htmlCodeBeforeField'=>$arrayBBCode['boutonsHTML']), 
                        'captcha'=>array('type'=>'captcha', 'value'=>'', 'htmlCode'=>"", 'default'=>'', 'libelle'=>'Vérification', 'error'=>'', 'required'=>true)
                    );
                }

                if (isset($this->variablesPost['message']) )//&& !isset($this->variablesPost['sended'])
                {
                    $errors = $f->getArrayFromPost($configFieldsContact);
                    
                    $complementMsgVisiteAdresse = "";
                    if (isset($this->variablesGet['archiIdEvenementGroupeAdresseOrigine']) && $this->variablesGet['archiIdEvenementGroupeAdresseOrigine']!='')
                    {
                        // recuperation de l'intitule de l'adresse
                        $adresse = new archiAdresse();
                        $idAdresseMessage = $adresse->getIdAdresseFromIdEvenementGroupeAdresse($this->variablesGet['archiIdEvenementGroupeAdresseOrigine']);
                        $intituleAdresse = $adresse->getIntituleAdresseFrom($this->variablesGet['archiIdEvenementGroupeAdresseOrigine'], 'idEvenementGroupeAdresse', array('ifTitreAfficheTitreSeulement'=>true, 'noQuartier'=>true, 'noSousQuartier'=>true, 'noVille'=>true));
                        $complementMsgVisiteAdresse = " à visité l'adresse <a href='".$this->creerUrl('', '', array('archiAffichage'=>'adresseDetail', 'archiIdAdresse'=>$idAdresseMessage, 'archiIdEvenementGroupeAdresse'=>$this->variablesGet['archiIdEvenementGroupeAdresseOrigine']))."'>".$intituleAdresse."</a> et";
                    }
                    
                    
                    if (count($errors)==0)
                    {
                        // envoi du mail
                        
                        $contenu = "Bonjour, <br><br>";
                        $contenu.="Un utilisateur d'archi-strasbourg (<a href=\"mailto:".$this->variablesPost['mailEnvoyeur']."\">".$this->variablesPost['mailEnvoyeur']."</a>)$complementMsgVisiteAdresse vous envoie un message privé :<br><br>";
                        $contenu.=stripslashes($bb->convertToDisplay(array('text'=>$this->variablesPost['message'])));
                        $contenu.="";
                        $contenu.="";
                        if ($mail->sendMail($this->siteMail, $infosArray['mail'],  "Un utilisateur d'archi-strasbourg vous envoie un message",  $contenu,  $writeMailToLogs=false,  $this->variablesPost['mailEnvoyeur']))
                        {
                            echo "<span style='color:red;'>Mail envoyé.</span>";
                        }
                    }
                    //$configFieldsContact['sended']=array('type'=>'hidden', 'value'=>'', 'forceValueTo'=>1, 'htmlCode'=>'', 'default'=>'', 'error'=>'', 'required'=>false);
                }
                
                
                $arrayUrlViensDeAdresse = array();
                if (isset($this->variablesGet['archiIdEvenementGroupeAdresseOrigine']) && $this->variablesGet['archiIdEvenementGroupeAdresseOrigine']!='')
                {
                    $arrayUrlViensDeAdresse = array('archiIdEvenementGroupeAdresseOrigine'=>$this->variablesGet['archiIdEvenementGroupeAdresseOrigine']);
                }
            
                $configForm = array(
                    'formAction'=>$this->creerUrl('', 'detailProfilPublique', array_merge($arrayUrlViensDeAdresse, array('archiIdUtilisateur'=>$idUtilisateur))), 
                    'fields'=>$configFieldsContact, 
                    'formName'=>'messagePrive', 
                    'codeHtmlInFormAfterFields'=>"Prévisualisation :".$arrayBBCode['divAndJsAfterForm']
                );
                $html.="<br><br><h2>Lui envoyer un message personnel :</h2>";
                $html.=$f->afficherFromArray($configForm);
            }
            
            $html.="
            
            </div>
            </div>
            ";
        } else {
            echo "Cet utilisateur a été banni.<br>";
        }
        return $html;
    }
    
    public function isUtilisateurBanni($params = array())
    {
        $retour = false;
        if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='')
        {
            $req = "SELECT compteBanni FROM utilisateur WHERE idUtilisateur='".$params['idUtilisateur']."'";
            $res = $this->connexionBdd->requete($req);
            
            if (mysql_num_rows($res)==1)
            {
                $fetch = mysql_fetch_assoc($res);
                
                if ($fetch['compteBanni']=='1')
                    $retour = true;
            }
        }
        return $retour;
    }
    
    public function getNbParticipationsUtilisateur($params = array())
    {
        $idUtilisateur = 0;
        $retour = 0;
        if (isset($params['idUtilisateur']) && $params['idUtilisateur']!='') {
            $idUtilisateur = $params['idUtilisateur'];
        
            $reqEvenements = "SELECT 0 FROM historiqueEvenement WHERE idUtilisateur = '".$idUtilisateur."'";
            $resEvenements = $this->connexionBdd->requete($reqEvenements);
            $nbEvenements = mysql_num_rows($resEvenements);

            $resImages = "SELECT 0 FROM historiqueImage WHERE idUtilisateur='".$idUtilisateur."'";
            $resImages = $this->connexionBdd->requete($resImages);
            $nbImages = mysql_num_rows($resImages);

            $reqCommentairesConnectes = "SELECT 0 FROM commentaires WHERE idUtilisateur = '".$idUtilisateur."'";
            $resCommentairesConnectes = $this->connexionBdd->requete($reqCommentairesConnectes);
            $nbCommentaires = mysql_num_rows($resCommentairesConnectes);
            
            
            $retour = $nbEvenements + $nbImages + $nbCommentaires;
        }
        
        return $retour;
    }
    
    public function getLibelleProfil($params = array())
    {
        $retour = "";
        if (isset($params['idProfil']) && $params['idProfil']!='') {
            $req = "SELECT libelle FROM droitsProfils WHERE idProfil = '".$params['idProfil']."'";
            $res = $this->connexionBdd->requete($req);
            if (mysql_num_rows($res)==1) {
                $fetch = mysql_fetch_assoc($res);
                $retour = $fetch['libelle'];
            }
        }
        return $retour;
    }
    
    public function canChangeNumeroArchiveField($params = array())
    {
        $retour = false;
        $req = "SELECT displayNumeroArchiveFieldInSaisieEvenement FROM utilisateur WHERE idUtilisateur='".$params['idUtilisateur']."'";
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
        if ($fetch['displayNumeroArchiveFieldInSaisieEvenement']=='1') {
            $retour = true;
        }
        return $retour;
        
    }
    
    /**
     * Le champ date fin s'affiche pour les moderateurs ,  les admins et pour les utilisateurs dont la case est cochée dans leur profil (dans l'admin)
     * 
     * @param array $params Paramètres
     * 
     * @return bool
     * */
    public function canChangeDateFinField($params = array())
    {
        $retour = false;
        
        $authentification = new archiAuthentification();
        
        
        if ($authentification->estAdmin(array('idUtilisateur'=>$params['idUtilisateur'])) || $authentification->estModerateur(array('idUtilisateur'=>$params['idUtilisateur']))) {
            $retour = true;
        } else {
            // cas d'un profil d'utilisateur
            $req = "SELECT displayDateFinFieldInSaisieEvenement FROM utilisateur WHERE idUtilisateur = '".$params['idUtilisateur']."'";
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            if ($fetch['displayDateFinFieldInSaisieEvenement']=='1') {
                $retour = true;
            }
        }
        
        return $retour;
    }
    
    
    public function canCopyright($params = array())
    {
        $retour = false;
        
        $authentification = new archiAuthentification();
        
        
        if ($authentification->estAdmin(array('idUtilisateur'=>$params['idUtilisateur'])) || $authentification->estModerateur(array('idUtilisateur'=>$params['idUtilisateur']))) {
            $retour = true;
        } else {
            // cas d'un profil d'utilisateur
            $req = "SELECT canCopyright FROM utilisateur WHERE idUtilisateur = '".$params['idUtilisateur']."'";
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            if ($fetch['canCopyright']=='1') {
                $retour = true;
            }
        }
        
        return $retour;
    }
    
    public function canModifyTags ($params = array())
    {
        $d = new droitsObject();
        
        $req = "SELECT canModifyTags FROM utilisateur WHERE idUtilisateur = '".$params['idUtilisateur']."'";
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
                
        if ($this->isAuthorized('tags',  $params['idUtilisateur'])) {
            return true;
        } else if ($fetch['canModifyTags']=='1') {
            return true;
        } else {
            return false;
        }
    }
    
    public function canAddWithoutStreet ($params = array())
    {
        $d = new droitsObject();
        
        $req = "SELECT canAddWithoutStreet FROM utilisateur WHERE idUtilisateur = '".$params['idUtilisateur']."'";
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
                
        return $fetch['canAddWithoutStreet'];
    }
    
    /**
     * Utilisateurs ayant leur compte d'activé
     * 
     * @param array $params Paramètres
     * 
     * @return int
     * */
    public function getNbUtilisateursInscrits($params = array())
    {
        $req = "SELECT 0 FROM utilisateur WHERE compteActif='1'";
        $res = $this->connexionBdd->requete($req);
        return mysql_num_rows($res);
    }
    
    /**
     * Obtenir le nombre d'utilisateurs abonnés à l'alerte mail
     * 
     * @param array $params Paramètres
     * 
     * @return int
     */
    public function getNbUtilisateursAbonnesAlerteMail($params = array())
    {
        $req = "SELECT 0 FROM utilisateur WHERE alerteMail='1' AND compteActif='1'";
        $res = $this->connexionBdd->requete($req);
        return mysql_num_rows($res);
    }
    
    
    
    
    
}
?>
