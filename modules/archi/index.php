<?php
/**
 * Charge les différents modules
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
$authentification = new archiAuthentification();

/* Cette variable sert a ne pas appeler deux fois le formulaire d'authentification
 * s'il est requis aussi pour les 'affichages' et les 'actions'
 * */
$afficheAuthentificationAction=false; 

// definition des champs du formulaire de contact
$fieldsContactForm = array(
                        'nom'=>array('type'=>'text', 'default'=>'',
                        'htmlCode'=>'', 'libelle'=>_("Votre nom :"),
                        'required'=>false, 'error'=>'', 'value'=>''),
                        'prenom'=>array('type'=>'text', 'default'=>'',
                        'htmlCode'=>'', 'libelle'=>_("Votre prénom :"),
                        'required'=>false, 'error'=>'', 'value'=>''),
                        'email'=>array('type'=>'text', 'default'=>'',
                        'htmlCode'=>"style='width:250px;'",
                        'libelle'=>_("Votre e-mail :"), 'required'=>true,
                        'error'=>'', 'value'=>''),
                        'message'=>array('type'=>'bigText', 'default'=>'',
                        'htmlCode'=>'cols=40 rows=8',
                        'libelle'=>_("Votre message :"),
                        'required'=>true, 'error'=>'', 'value'=>'')
);

$configFormContact = array(
    'logMails'=>true, 'titrePage'=>_("Contactez-nous"),
    'fields'=>$fieldsContactForm, 'submitButtonValue'=>_("Envoyer"),
    'formAction'=>$authentification->creerUrl('handleFormulaireContact', '')
);



$listeActionsAuthentificationNonRequise=array(
    'handleMotDePasseOublieNouveauMotDePasse', 'validAuthentification',
    'handleMotDePasseOublie', 'confirmInscription', 'deconnexion',
    'validInscription', 'handleFormulaireContact',
    'enregistreCommentaire', 'enregistrerEntreeSondage'
);


//$s = new objetSession();
//echo $s->getFromSession('archiIdVilleGeneral');


if (isset($_GET['archiAction'])) {
    $archiAction=$_GET['archiAction'];
    
    // Authentification non requise pour ces actions : 
    switch($archiAction) {
    case 'ajouterActu':
        $admin = new archiAdministration();
        $admin->ajouterActualite();
        break;
    case 'modifierActu':
        $admin = new archiAdministration();
        $admin->modifierActualite();
        break;
    case 'supprimerActu':
        $admin = new archiAdministration();
        $admin->supprimerActualite();
        break;
    case 'enregistrerEntreeSondage':
        $a = new archiAccueil();
        $a->enregistreEntreeSondage();
        break;
    case 'handleMotDePasseOublieNouveauMotDePasse':
        // gestion de la mise a jour du mot de passe
        $utilisateur = new archiUtilisateur();
        echo $utilisateur->changementMotDePasseOublie();
        break;
    case 'handleMotDePasseOublie':
        // gestion de l'envoi de mail pour le mot de passe oublie
        $utilisateur = new archiUtilisateur();
        echo $utilisateur->envoiMailMotDePasseOublie();
        break;            
    case 'confirmInscription':
        $utilisateur = new archiUtilisateur();
        echo $utilisateur->confirmInscription();
        break;
    case 'deconnexion':
        $auth = new archiAuthentification();
        $auth -> deconnexion();
        break;
    case 'validInscription':
        $inscription = new archiAuthentification();
        $inscription->inscription();
        break;
    case 'handleFormulaireContact':
        $formulaire = new formGenerator();
        $formulaire->gestionRetour('envoiMailAdmin', $configFormContact);
        break;
    case 'enregistreCommentaire':
        $a = new archiAdresse();
        $a->enregistreCommentaire();
        
        if (!$authentification->estConnecte()) {
            echo "<SCRIPT>";
            echo "alert('"._("Merci pour votre commentaire.")."\\n".
            _(
                "Vous allez recevoir un mail contenant un lien ".
                "permettant de valider le commentaire."
            ).
            "\\n"."A bientôt !')";
            //echo "alert(\"salut\")";
            echo "</SCRIPT>";
        }

        
        break;
    case 'validAuthentification':
        $connexionUtilisateur= new archiAuthentification();
        $login=isset($_POST['archiLogin'])?$_POST['archiLogin']:"";
        $mdp=isset($_POST['archiMdp'])?$_POST['archiMdp']:"";
        $cookie=isset($_POST['cookie'])?$_POST['cookie']:"";
        if (isset($_GET['assertion'])) {
            $connexionUtilisateur->browserID($_GET['assertion']);
            echo $connexionUtilisateur->erreurs->afficher();
        } else {
            $connexionUtilisateur->connexion($login, $mdp, $cookie);
            echo $connexionUtilisateur->erreurs->afficher();
        }
        if (isset($_GET['archiActionPrecedente']) 
            && ($_GET['archiActionPrecedente']==''
            || $_GET['archiActionPrecedente']=='validAuthentification'
            || $_GET['archiActionPrecedente']=='deconnexion')
            && !$connexionUtilisateur->erreurs->existe()
        ) {
            if (isset($_GET['archiAffichage'])
                && $_GET['archiAffichage']=='ajoutNouveauDossier'
            ) {
                $_GET['archiAffichage'] = 'ajoutNouveauDossier';
            } elseif (isset($_GET['archiAffichage'])
                && $_GET['archiAffichage']=='imageDetail'
            ) {
                $_GET['archiAffichage'] = 'imageDetail';
            } elseif (isset($_GET['archiAffichage']) 
                && $_GET['archiAffichage']!='adresseDetail'
            ) {
                $_GET['archiAffichage'] = 'afficheAccueil';
                $_GET['modeAffichage'] = 'monArchi';
            } elseif (!isset($_GET['archiAffichage'])) {
                $_GET['archiAffichage'] = 'afficheAccueil';
                $_GET['modeAffichage'] = 'monArchi';
            }
        }
        break;
    }
    
    /* archiActionPrecedente permet de recuperer l'action precedente
     * pour le formulaire d'authentification
     * et permettra de lancer l'action requerant une authentification
     * apres la validation de ce formulaire d'authentification
     * */
    if (isset($_GET['archiActionPrecedente']) 
        && $_GET['archiActionPrecedente']!=''
    ) {
        $archiAction = $_GET['archiActionPrecedente'];
    }
    
    // Authentification requise pour ces actions :
    if ($authentification->estConnecte()) {
        switch($archiAction) {
        case 'refreshCache':
            $cache = new cacheObject();
            $cache->refreshCachedPages();
            break;
        case 'resetCache':
            $cache = new cacheObject();
            $cache->resetCache();
            break;
        case 'administration':
            $administration = new archiAdministration();
            // Affichage de la liste des données de la table selectionnée
            echo $administration->administre(
                array('tableName'=>$_GET['tableName'], 'noAjouterButton'=>1,
                'noSupprimerButton'=>1), 'action'
            ); 
            break;
        case 'adminEnregistreModifAdresse':
            $adresse = new archiAdresse();
            $errors = $adresse->enregistreModificationAdresse();
            if (count($errors)==0) {
                $generateur = new formGenerator();
                echo $generateur->afficheFormulaireListe(
                    array_merge(
                        $_GET, array(
                            'modeAffichageLienDetail'=>"adminAdresseDetail",
                            "actionAffichageFormulaireIfSubmit"
                                =>$adresse->creerUrl(
                                    '', 'adminElementAdresse',
                                    array('tableName'=>$_GET['tableName'])
                                )
                        )
                    )
                );
            } else {
                echo _("Erreur lors de l'enregistrement.")."<br>";
            }
            break;            
        case 'modifImageAdressesLiees':
                $image = new archiImage();
                $image = $image->modifierLiaisonAdresse($_GET['archiIdImage']);
            break;
        case 'ajoutNouvelleAdresse':
            $adresse = new archiAdresse();
            $adresse->ajoutNouvelleAdresse();
            if ($adresse->erreurs->existe()) {
                echo $adresse->erreurs->afficher();
                /* Affichage du formulaire permettant d'ajouter
                 * de nouvelle rues, sousquartier, quartier, ville, pays, etc.
                 * */
                $criteres=array();
            
                if (isset($_GET['typeNew'])) {
                    $criteres['typeNew']=$_GET['typeNew'];
                }
            
                echo $adresse -> afficheFormulaireNouvelleAdresse($criteres);
            } else {
                $criteres=array();
            
                if (isset($_GET['typeNew'])) {
                    $criteres['typeNew']=$_GET['typeNew'];
                }
                echo _("Enregistrement effectué");
                echo $adresse ->afficheFormulaireNouvelleAdresse($criteres);
            }
            break;
        case 'ajoutAdresse':
            $adresse = new archiAdresse();
            $adresse->ajouter();            
            break;
        case 'ajoutEvenement':
            $evenement = new archiEvenement();
            $html = $evenement->ajouter();
            echo $evenement->erreurs->afficher();
            echo $html;
            break;
        case 'ajoutImage':
            $image = new  archiImage();
            $image->ajouter();
            break;
        case 'ajoutHistoriqueAdresse':
            $adresse = new archiAdresse();
            echo $adresse->ajouterHistoriqueAdresse($_GET['archiIdAdresse']);
            break;
        case 'ajouterPersonne':
            $personne = new archiPersonne();
            $newIdPersonne = $personne->ajouter();
            //|| $personne->erreurs->tabFormExiste()
            if ($personne->erreurs->existe()) {
                echo $personne->erreurs->afficher();
                echo $personne->afficherFormulaire();
            } else {
                echo _("Enregistrement effectué");
                echo $personne->afficherListe(
                    array("newIdPersonneAdded"=>$newIdPersonne)
                );
            }
            break;    
        case 'ajouterSource':
            $source = new archiSource();
            $newIdSource = $source->ajouter();
            if ($source->erreurs->existe()
                || $source->erreurs->tabFormExiste()
            ) {
                echo $source->erreurs->afficher();
                echo $source->afficherFormulaire();
            } else {
                /* On transmet le nouveau idSource crée
                 * pour le renvoi automatique de la popup
                 * apres creation d'une nouvelle source
                 * (si on est en mode popup suivant le mode affichage)
                 * */
                echo $source->afficherListe(
                    array("newIdSourceAdded"=>$newIdSource)
                ); 
            }
            break;    

        case 'modifImage':
            $image = new archiImage();
            $image->modifier();
            break;
        case 'modifierEvenement':
            $evenement = new archiEvenement();
            $html = $evenement->modifier($_GET['archiIdEvenement']);
            echo $evenement->erreurs->afficher();
            echo $html;
            break;
        case 'modifierHistoriqueAdresse':
            $adresse = new archiAdresse();
            $html = $adresse->modifierHistorique($_GET['archiIdHistoriqueAdresse']);
            echo $adresse->erreurs->afficher();
            echo $html;
            break;
        case 'modifierUtilisateur':
            $utilisateur = new archiUtilisateur();
            $utilisateur->modifier();
            break;
        case 'rendreAdmin':
            $auth  = new archiAuthentification();
            $html = $auth->rendreAdmin($_GET['id']);
            echo $auth->erreurs->afficher();
            echo $html;
            break;
        case 'supprimerAdresse':
            $adresse = new archiAdresse();
            /* Note de laurent :
             * Attention cette fonction ne fais pas forcement
             * ce qui est attendu, à vérifier.
             * (Pas sûr qu'elle soit utilisée.)
             * */
            echo $adresse->supprimer($_GET['archiIdAdresse']); 
            break;
        case 'supprimerAdresseHistorique':
            $adresse = new archiAdresse();
            echo $adresse->supprimer('', $_GET['archiIdHistoriqueAdresse']);
            break;
        case 'supprimerAdresseFromAdminRue':
            $adresse = new archiAdresse();
            /* Cette fonction va supprimer une adresse
             * et son historique de la table historiqueAdresse,
             * s'il n'y a plus rien qui y est lié.
             * */
            $adresse->supprimerAdresseFromAdminRue();    
            break;
        case 'supprimerRueFromAdminRue':
            $adresse = new archiAdresse();
            $adresse->supprimerRueFromAdminRue(); 
            break;
        case 'supprimerEvenement':
            $evenement = new archiEvenement();
            echo $evenement->supprimer($_GET['archiIdEvenement']);
            break;
        case 'supprimerHistoriqueEvenement':
            $evenement = new archiEvenement();
            echo $evenement->supprimer('', $_GET['archiIdHistoriqueEvenement']);
            break;

        case 'ajoutNouveauDossier':
            $adresses = new archiAdresse();
            $adresses->ajouterNouveauDossier();
            if ($adresses->erreurs->existe()
                || $adresses->erreurs->tabFormExiste()
            ) {
                echo $adresses->erreurs->afficher();
                echo $adresses->afficheFormulaireNouveauDossier();
            }
            break;
        case 'ajoutNouvelPersonne':
            $personne = new archiPersonne();
            $personne->ajouterNouveauDossier("personne");
            if ($personne->erreurs->existe()
                || $personne->erreurs->tabFormExiste()
            ) {
                echo $personne->erreurs->afficher();
                echo $personne->afficheFormulaireNouveauDossier(array(), "personne");
            }
            break;
        case 'enregistreGroupeAdresses':
            $adresses = new archiAdresse();
            $adresses->enregistreGroupeAdresses(
                $_GET['archiIdEvenementGroupeAdresses']
            );
            break;
        case 'deleteImage':
            $image = new archiImage();
            $image->deleteImage(
                $_GET['archiIdImage'],
                array('retourSurGroupeAdresse'=>true)
            );
            break;
        case 'supprimerCommentaire':
            if ($authentification->estAdmin()) {
                $adresse = new archiAdresse();
                $adresse->deleteCommentaire();
            }
            break;
        case 'enregistrePositionsImages':
            $image = new archiImage();
            $image -> enregistrePositionImages();
            break;
        case 'enregistreDroits':
            $d = new droitsObject();
            $d->enregistreDroits();
            break;
        case 'enregistreListeVillesModerateur':
            $u = new archiUtilisateur();
            $u->enregistreListeVillesModerateur();            
            break;
        case 'deplacerImagesSelectionnees':
            $i = new archiImage();
            $i->deplacerImagesSelectionnees();
            break;
        case 'supprimerImagesSelectionnees':
            $i = new archiImage();
            $i->supprimerImagesSelectionnees();
            break;
        case 'enregistreAdressesLieesAEvenement':
            $e = new archiEvenement();
            $e->enregistreAdressesLieesAEvenement();            
            break;
        case 'selectTitreAdresse':
            $e = new archiEvenement();
            $e->enregistreSelectionTitreGroupeAdresse();
            break;
        case 'enregistreHistoriqueNomsRues':
            $a = new archiAdresse();
            $a->enregistreHistoriqueNomsRues();
            break;
        case 'enregistreZoneImage':
            $i = new archiImage();
            $i->enregistreZoneImage();
            break;
        case 'deplacerEvenementVersNouveauGA':
            $e = new archiEvenement();
            $e->deplacerEvenementVersMemeAdresseNouveauGA();
            break;
        case 'deplacerEvenementVersGA':
            $e = new archiEvenement();
            $e->deplacerEvenementVersGroupeAdresse();
            break;
        case 'enregistreSelectionImagePrincipale':
            $e = new archiEvenement();
            $e->enregistreSelectionImagePrincipale();
            break;
        case 'enregistreNouvelleCoordonneesGoogleMap':
            $a = new archiAdresse();
            $a->enregistreNouvellesCoordonneesAdresseGoogleMapEtVerrouillage();
            break;
        case 'ajouterParcours': // parcours art nouveau => admin
            $admin = new archiAdministration();
            echo $admin->ajouterParcours();
            break;
        case 'modifierParcours': // parcours art nouveau => admin
            $admin = new archiAdministration();
            echo $admin->modifierParcours();
            break;
        case 'ajouterEtapeParcours':
            $admin = new archiAdministration();
            echo $admin->ajouterEtapeParcours();
            break;
        case 'modifierEtapeParcours':
            $admin = new archiAdministration();
            echo $admin->modifierEtapeParcours();
            break;
        case 'supprimerEtapeParcours':
            $admin = new archiAdministration();
            echo $admin->supprimerEtapeParcours();
            break;
        case 'enregistrerOrdreEtapesParcours':
            $admin = new archiAdministration();
            echo $admin->enregistrerOrdresEtapes();
            break;
        case 'supprimerParcours':
            $admin = new archiAdministration();
            $admin->supprimerParcours();
            break;
        
        case 'enregistrerPositionnementEvenements':
            $e = new archiEvenement();
            $e->enregistrerPositionnementEvenements();
            break;
        case 'regenereImageFromUploadDir':
            $i = new archiImage();
            $i->regenereImageFromUploadDirectory();
            break;
        
        
        default:
            break;
        }
    } else {
        if (!in_array($archiAction, $listeActionsAuthentificationNonRequise)) {
            echo $authentification->afficheFormulaireAuthentification();
            // On appelle le formulaire d'authentification 
            $afficheAuthentificationAction=true; 
        }
    }
}




// Liste des affichages qui ont besoin d'une authentification
$modesAffichagesAvecAuthentification = array('ajoutImageBibliotheque',
'ajoutImageEvenement', 'ajouterEvenement', 'ajoutImageAdresse',
'ajouterEvenementSurAdresse', 'ajouterSousEvenement', 'ajoutNouvelleAdresse',
'modifierImagesUtilisateur', 'modifierImageEvenement', 'modifierImageAdresse',
'modifierImage', 'modifierEvenement', 'modifierAdresse', 'utilisateurDetail',
'formulaireGroupeAdresses', 'afficheSelectTypeEvenement',
'afficheSelectSousQuartier', 'afficheSelectQuartier', 'afficheChoixRue',
'afficheChoixVille', 'ajoutNouveauDossier', 'afficherAjouterPersonne',
'afficherAjouterSource', 'administration', 'comparaisonEvenement',
'afficheLogsMails', 'adminDroits', 'adminSondages', 'adminParcours',
'administrationAfficheAjout', 'listeTypeSourceDependancesSourcesAdmin',
'adminPages', 'editPage', 'editPerson', 'choosePicturePerson', 'deletePerson',
'choosePersonEventImage');

if (isset($_GET['archiAffichage'])) {
    $archiAffichage=$_GET['archiAffichage'];
    
    if (in_array($archiAffichage, $modesAffichagesAvecAuthentification) 
        && !$authentification->estConnecte()
    ) {
        /* Le formulaire a-t-il deja ete appelé
         * par une 'action' requerant une connexion ?
         * */
        if (!$afficheAuthentificationAction) {
            echo $authentification->afficheFormulaireAuthentification();
        }
    } else {
        $u = new archiUtilisateur();
        
        switch($archiAffichage)
        {
        case 'administration':
            $administration = new archiAdministration();
            if (!isset($_GET['tableName'])) {
                /* Affiche le menu qui permet de selectionner
                 * la table que l'on administrer
                 * */
                echo $administration->afficheMenu(); 
            } else {
                // Affichage de la liste des données de la table selectionnée
                echo $administration->administre(
                    array('tableName'=>$_GET['tableName'],
                    'noSupprimerButton'=>1), 'liste'
                ); 
            }
            break;
        case 'adminSondages':
            $archiAccueil = new archiAccueil();
            echo $archiAccueil->afficheResumeSondage(array('idSondage'=>1));
            break;
        case 'adminElementAdresse':
            switch($_GET['tableName']) {
            case 'rue':
                $administration=new archiAdministration();
                echo $administration->getAdminListeRues();
                
                break;
            case 'sousQuartier':
                $administration = new archiAdministration();
                echo $administration->getAdminListeSousQuartiers();
                break;
            case 'quartier':
            
                $optionsModerateur=array();
                /* L'utilisateur courant est moderateur :
                 * on ne va donc afficher que la liste des quartiers
                 * que cette personne modere pour sa ou ses villes
                 * */
                if ($authentification->getIdProfil()==3) {
                    $arrayVillesFromModerateur = $u->getArrayVillesModereesPar(
                        $authentification->getIdUtilisateur()
                    );
                    
                    if (count($arrayVillesFromModerateur)>0) {
                        $optionsModerateur=array('sqlWhere'=>" AND idVille in (".
                        implode(",", $arrayVillesFromModerateur).") ");
                    } else {
                        $optionsModerateur=array('sqlWhere'=>" AND idVille='0' ");
                    }
                }
            
            
                $liensExternes=array(
                    'idVille'=>array('externalLink'=>true,
                    'externalFieldPrimaryKey'=>'idVille',
                    'externalTable'=>'ville',
                    'externalFieldToDisplay'=>'nom'));
                $dependances[0] = array('table'=>'sousQuartier',
                'champLie'=>'idQuartier',
                'message'=>"Attention il existe des dépendances ".
                "au niveau de la table des sous quartiers");
                $dependances[1] = array('table'=>'historiqueAdresse',
                'champLie'=>'idQuartier',
                'message'=>"Attention il existe des dépendances au niveau".
                " de la table historiqueAdresse");
                $generateur = new formGenerator();
                echo $generateur->afficheFormulaireListe(
                    array_merge(
                        $_GET, array('modeAffichageLienDetail'=>"adminAdresseDetail",
                        "replaceAjouterButtonBy"
                            =>"<input type='button' name='ajouter' ".
                        "value='ajouter' onclick=\"location.href='".
                        $generateur->creerUrl(
                            '', 'ajoutNouvelleAdresse',
                            array("typeNew"=>"newQuartier")
                        )
                        ."';\">"
                        ), $optionsModerateur
                    ), $liensExternes, $dependances
                );
                break;
            case 'ville':
                $liensExternes=array(
                    'idPays'=>array('externalLink'=>true,
                    'externalFieldPrimaryKey'=>'idPays',
                    'externalTable'=>'pays',
                    'externalFieldToDisplay'=>'nom'));
                
                $dependances[0] = array('table'=>'quartier', 'champLie'=>'idVille',
                'message'=>"Attention il existe des dépendances au ".
                "niveau de la table des quartiers");
                $dependances[1] = array('table'=>'historiqueAdresse',
                'champLie'=>'idVille', 'message'=>"Attention il existe ".
                "des dépendances au niveau de la table historiqueAdresse");

                $generateur = new formGenerator();
                echo $generateur->afficheFormulaireListe(
                    array_merge(
                        $_GET, array('modeAffichageLienDetail'=>"adminAdresseDetail",
                        "replaceAjouterButtonBy"
                            =>"<input type='button' name='ajouter' value='ajouter' ".
                            "onclick=\"location.href='".
                            $generateur->creerUrl(
                                '', 'ajoutNouvelleAdresse',
                                array("typeNew"=>"newVille")
                            )."';\">")
                    ), $liensExternes, $dependances
                );
                break;
            case 'pays':
                $dependances[0] = array('table'=>'ville', 'champLie'=>'idPays',
                'message'=>"Attention il existe des dépendances ".
                "au niveau de la table des villes");
                $dependances[1] = array('table'=>'historiqueAdresse',
                'champLie'=>'idPays',
                'message'=>"Attention il existe des dépendances ".
                "au niveau de la table historiqueAdresse");

                $generateur = new formGenerator();
                echo $generateur->afficheFormulaireListe(
                    array_merge(
                        $_GET, array('modeAffichageLienDetail'=>"adminAdresseDetail")
                    ),
                    array(), $dependances
                );
                break;

            }
            break;
        case 'adminAdresseDetail':
            $adresse = new archiAdresse();
            $params = array(
                'tableName'=>$_GET['tableName'],
                'id'=>$_GET['idModification']
            );
            echo $adresse->afficheFormulaireModificationElementAdresse($params);
            break;        
        case 'administrationAfficheModification':
            $administration = new archiAdministration();
            echo $administration->administre(
                array('tableName'=>$_GET['tableName'],
                'id'=>$_GET['idModification']), 'modification'
            );
             
            break;
        case 'administrationAfficheAjout':
            $administration = new archiAdministration();
            echo $administration->administre(
                array('tableName'=>$_GET['tableName']), 'ajout'
            );
            
            break;
        case 'adminPersonne':
            $liensExternes=array(
                    'idMetier'=>array('externalLink'=>true,
                    'externalFieldPrimaryKey'=>'idMetier',
                    'externalTable'=>'metier',
                    'externalFieldToDisplay'=>'nom'
            ));
            $parametres['tableName']='personne';
            $parametres['modeAffichageLienDetail']='adminPersonneDetail';
            $parametres['replaceAjouterButtonBy'] = "<input type='button' ".
            "name='boutonAjouter' value='Ajouter une personne' ".
            "onClick=\"location.href='index.php?".
            "archiAffichage=adminAjoutPersonneDetail';\">";
            $parametres['displayWithBBCode'] = true;
            $generateur = new formGenerator();
            echo $generateur->afficheFormulaireListe($parametres, $liensExternes);
            break;
        case "adminPersonneDetail":
            $liensExternes=array(
                    'idMetier'=>array('externalLink'=>true,
                    'externalFieldPrimaryKey'=>'idMetier',
                    'externalTable'=>'metier',
                    'externalFieldToDisplay'=>'nom'
            ));
            $parametres['tableName']='personne';
            $parametres['modeAffichageLienDetail']='adminPersonneDetail';
            $parametres['id']=$_GET['idModification'];
            $parametres['afficheMiseEnFormeLongText']=true;
            $generateur = new formGenerator();
            echo $generateur->afficheFormulaireModification(
                $parametres, $liensExternes
            );
            
            break;
        case "adminAjoutPersonneDetail":
            $liensExternes=array(
                'idMetier'=>array(
                    'externalLink'=>true,
                    'externalFieldPrimaryKey'=>'idMetier',
                    'externalTable'=>'metier',
                    'externalFieldToDisplay'=>'nom'
                )
            );
            $parametres['tableName']='personne';
            $parametres['modeAffichageLienDetail']='adminPersonneDetail';
            $parametres['afficheMiseEnFormeLongText']=true;
            $generateur = new formGenerator();
            echo $generateur->afficheFormulaireAjout(
                $parametres, $liensExternes
            );
            break;
        case 'accueil':
            $derniersEvenements = new archiEvenement();
            echo $derniersEvenements->afficherListe(
                array('ordre'=>'dateCreation', 'tri'=>'desc'),
                'derniersEvenements.tpl'
            );
            break;
        case 'afficheChoixAdresse':
            $criteres=array();
            if (isset($_GET['typeNew'])) {
                $criteres=array('typeNew'=>$_GET['typeNew']);
            }
            $a = new archiAdresse();
            echo $a->afficheChoixAdresse($criteres);
            break;
        case 'authentification':
            /* Le formulaire a-t-il deja ete appelé par
             * une 'action' requerant une connexion ? 
             * */
            if (!$afficheAuthentificationAction) {
                $auth = new archiAuthentification();
                echo $auth->afficheFormulaireAuthentification();
            }
            break;
        case 'authentificationImage':
            /* Le formulaire a-t-il deja ete appelé par
             * une 'action' requerant une connexion ? 
             * */
            if (!$afficheAuthentificationAction) {
                $auth = new archiAuthentification();
                echo $auth->afficheFormulaireAuthentification(
                    'noCompact',
                    array(
                        'msg'=>"<b>Pour voir les photos au format moyen ".
                        "ou l'original vous devez être connecté. ".
                        "Si vous n'avez pas encore de compte utilisateur".
                        " pour vous connecter, cliquez <a href='".
                        $auth->creerUrl('', 'inscription')."'>ici</a></b>"
                    )
                );
            }
            break;
        case 'evenementListe':
            $a = new archiEvenement();
            echo $a->afficherListe();
            break;
        case 'ajoutAdresse':
            $a = new archiAdresse();
            echo $a->afficheFormulaire();
            break;
        case 'adresseDetail':
            $a = new archiAdresse();
            echo $a->afficherDetail($_GET['archiIdAdresse']);
            break;
        case 'adresseListe':
            $a = new archiAdresse();
            $retourAdresse = $a->afficherListe();
            echo $retourAdresse['html'];
            break;
        case 'evenement':
            $a = new archiEvenement();
            $retour = $a->afficher($_GET['idEvenement']);
            echo $retour['html'];
            break;
        case 'imageDetail':
            $a = new archiImage();
            echo $a->afficher($_GET['archiIdImage']);
            break;
        case 'imageListe':
            $a = new archiImage();
            echo $a->afficherListe();
            break;
        case 'afficheHistoriqueImage':
            $i = new archiImage();
            echo $i->afficheHistoriqueImage($_GET['archiIdImage']);
            break;
        case 'inscription':
            $auth = new archiAuthentification();
            echo $auth->afficheFormulaireInscription();
            break;    
        case 'historiqueEvenement':
            $evenement = new archiEvenement();
            echo $evenement->afficherHistorique($_GET['idEvenement']);
            break;    

        case 'navigationAdresse':
            $a = new archiRecherche();
            echo $a->navigationAdresse();
            break;
        case 'personne':
            $a = new archiPersonne();
            echo $a->afficher($_GET['idPersonne']);
            break;
        case 'historiqueEvenementDetail':
            $evenement = new archiEvenement();
            echo $evenement->afficherHistoriqueDetail(
                $_GET['idHistoriqueEvenement']
            );
            break;
        case 'personneListe':
            $p = new archiPersonne();
            $criteres=array();
            
            if (isset($_GET["alphaPersonne"])) {
                $criteres['alphaPersonne']=$_GET["alphaPersonne"];
            } else {
                $criteres['alphaPersonne']='a';
            }
            
            if (isset($_GET["archiPagePersonne"])) {
                $criteres['archiPagePersonne']=$_GET['archiPagePersonne'];
            }
            
            echo $p->afficherListe($criteres);
            break;
        case 'recherche':
            $a = new archiRecherche();
            echo $a->rechercher();
            if (isset($_GET["motcle"]) 
                && $_GET["modeAffichage"]!="popupRechercheAdresseVueSur"
            ) {
                $pos=isset($_GET["pos"])?$_GET["pos"]:1;
                echo archiPersonne::search($_GET["motcle"], $pos);
            }
            break;
        case 'source':
            $s = new archiSource();
            echo $s->afficher($_GET['idSource']);
            break;

        case 'utilisateurListe':
            $a = new archiUtilisateur();
            echo $a->afficherListe();
            break;

        case "sourceListe":
            $s  = new archiSource();
            $criteres=array();
            if (isset($_POST['archiTypeSource']) 
                && $_POST['archiTypeSource'] != '0'
            ) {
                $criteres['archiTypeSource'] = $_POST['archiTypeSource'];
            }
            
            if (isset($_GET['archiTypeSource']) && $_GET['archiTypeSource']!='') {
                $criteres['archiTypeSource'] = $_GET['archiTypeSource'];
            }
            
            if (isset($_GET["alphaSource"])) {
                $criteres['alphaSource']=$_GET["alphaSource"];
            } else {
                $criteres['alphaSource']='a';
            }
            
            if (isset($_GET["archiPageSource"])) {
                $criteres['archiPageSource']=$_GET['archiPageSource'];
            }
            echo $s->afficherListe($criteres);
            break;
        
        case 'rechercheAvEvenement':
            $r= new archiRecherche();
            //$criteres['modeAffichage'] = 'calqueEvenement';
            echo $r->rechercheAvanceeEvenement();
            break;    
        case 'listeAdressesFromSource':
            $r= new archiRecherche();
            //$criteres['modeAffichage'] = 'calqueEvenement';
            echo $r->rechercheAvanceeEvenement(array('sansFormulaire'=>true));
            print archiPersonne::getPersonsFromSource($_GET['source']);
            break;
        case 'afficheGrandFormatSource':
            $s = new archiSource();
            echo $s->afficheImageSourceOriginal();
            break;
        case 'rechercheAvAdresse':
            $r= new archiRecherche();
            echo $r->rechercheAvanceeAdresse();
            break;
        case 'rechercheParCarte':
            $r = new archiRecherche();
            echo $r->afficheCarteRecherche(array('centrerSurVilleGeneral'=>true));
            break;
        case 'rechercheAvEvenementPopup':
            $r= new archiRecherche();
            if (isset($_GET['modeAffichage'])) {
                $criteres['modeAffichage'] = $_GET['modeAffichage'];
            } else {
                $criteres['modeAffichage'] = 'calqueEvenement';
            }
            
            echo $r->rechercheAvanceeEvenement($criteres);
            break;
        case 'afficheCalendrier':
            print('<div class="calendar">');
            include 'includes/calendar/calendar.php';
            calendar(
                array(
                    "DATE_URL"=>"perso",
                    "URL_DAY_DATE_FORMAT"=>"d/m/Y",
                    "LANGUAGE_CODE"=>"fr"
                )
            );
            print('</div>');
            break;
        // Liste d'adresses regroupee, pseudo dossier par rue
        case 'listeDossiers':
            $a = new archiAdresse();
            echo $a->afficheListeRegroupee();
            
            break;
        // Parties statiques
        case 'faq':
            $s = new archiStatic();
            echo $s->afficheFaq();
            break;
        case 'edito':
            $s = new archiStatic();
            echo $s->afficheEdito();
            break;
        case 'quiSommesNous':
            $s = new archiStatic();
            echo $s->afficheQuiSommesNous();
            break;
        //Gestion des historiques de quiSommesNous : add by fabien le 26/03/2012
        
        case 'quiSommesNousLaurent':
            $s = new archiStatic();
            echo $s->afficheQuiSommesNousLaurent2009();
            break;
        
        case 'quiSommesNousContributeurs':
            $s = new archiStatic();
            echo $s->afficheQuiSommesNousContributeurs2010();
            break;
        case 'quiSommesNousCreationAssociation':
            $s = new archiStatic();
            echo $s->afficheQuiSommesNousCreationAssociation2011();
            break;
        
        
        case 'faireUnDon':
            $s = new archiStatic();
            echo $s->afficheFaireUnDon();
            break;
        // ajout fabien du 30/11/2011
        case 'donateurs':                        
            $s = new archiStatic();
            echo $s->afficheDonateurs();
            break;
        // Popup source : operation ajout dans la popup
        case 'afficherAjouterSource':
            $source = new archiSource();
            echo $source->afficherFormulaire();
            break;
        //Popup personne : operation ajout dans la popup
        case 'afficherAjouterPersonne':
            $personne = new archiPersonne();
            echo $personne->afficherFormulaire();
            break;
        // Cas de l'ajout d'un nouveau dossier
        case 'ajoutNouveauDossier':
            $a = new archiAdresse();
            /* Un dossier est une notion qui regroupe un evenement
             * groupe d'adresse et un evenement 'construction'
             * */
            echo $a->afficheFormulaireNouveauDossier(); 
            break;    
        case "editPerson":
            include "inc/editPerson.php";
            break;
        case "choosePicturePerson":
            include "inc/choosePicturePerson.php";
            break;
        case "deletePerson":
            include "inc/deletePerson.php";
            break;
        case 'membership':
            include 'inc/membership.php';
            break;
        case 'imageSearch':
            include 'inc/imageSearch.php';
            break;
        case 'choosePersonEventImage':
            include 'inc/choosePersonEventImage.php';
            break;
        case "ajoutNouvelPersonne":
            $auth=new archiAuthentification();
            if ($auth->estConnecte()) {
                $p = new archiPersonne();
                echo $p->afficheFormulaireNouveauDossier(array(), "personne");
            } else {
                if (!$afficheAuthentificationAction) {
                    $auth = new archiAuthentification();
                    echo $auth->afficheFormulaireAuthentification();
                }
            }
            break;
        case 'afficheChoixVille':
            $a = new archiAdresse();
            // affichage du formulaire de choix de ville par ordre alphabetique  
            echo $a->afficheChoixVille();       
            break;
        case 'afficheChoixRue':
            $a = new archiAdresse();
            echo $a->afficheChoixRue();
            break;
        case 'afficheSelectQuartier':
            $a = new archiAdresse();
            echo $a->afficheSelectQuartier();
            break;
        case 'afficheSelectSousQuartier':
            $a = new archiAdresse();
            echo $a->afficheSelectSousQuartier();
            break;
        case 'afficheSelectVille':
            $a = new archiAdresse();
            echo $a->afficheSelectVille();
            break;
        case 'afficheSelectTypeEvenement':
            $a = new archiAdresse();
            echo $a->afficheSelectTypeEvenement();
            break;
        case 'formulaireGroupeAdresses':
            $a = new archiAdresse();
            echo $a->afficheAjoutAdressesMultiple(
                $_GET['archiIdEvenementGroupeAdresses']
            );
            break;
        case 'utilisateurDetail':
            $a = new archiUtilisateur();
            echo $a->afficher(array(), $_GET['idUtilisateur']);
            break;
        case 'modifierAdresse':
            $a = new archiAdresse();
            echo $a->afficheFormulaire(array(), $_GET['archiIdAdresseModification']);
            break;
        case 'modifierEvenement':
            $ev = new archiEvenement();
            $html = $ev->modifier($_GET['archiIdEvenement']);
            echo $ev->erreurs->afficher();
            echo $html;
            break;
        case 'modifierImageMultiple':
            /* ATTENTION listeId correspond a une liste d'HISTORIQUE d'image,
             * donc il faut d'abord recuperer les idImages correspondant
             * pour les envoyer a la fonction afficherFormulaireModification
             * */
            if (isset($_POST['listeId'])) {
                $arrayIdImage=array();
                $arrayIdHistoriqueImage = explode(",", $_POST['listeId']);
                foreach ($arrayIdHistoriqueImage as $indice => $idHistoriqueImage) {
                    /* recuperation des idImages du formulaire
                     * pour les passer a l'affichage de modification
                     * */
                    if (isset($_POST['idImage_'.$idHistoriqueImage])) {
                        $arrayIdImage[] = $_POST['idImage_'.$idHistoriqueImage];
                    }
                }
                
                $a = new archiImage();
                echo $a -> afficherFormulaireModification(0, '', $arrayIdImage);
            } else {
                echo "index.php : modifierImageMultiple : ".
                "il manque un parametre, veuillez contacter l'administrateur.<br>";
            }
            break;
        case 'modifierImage':
            $a = new archiImage();
            echo $a -> afficherFormulaireModification(
                0, '', array($_GET['archiIdImageModification'])
            );
            break;
        case 'modifierPositionsImages':
            $a = new archiImage();
            echo $a->afficheFormulaireModifPosition(
                array('idEvenement'=>$_GET['archiIdEvenement'])
            );
            break;
        case 'modifierImageAdresse':
            $a = new archiImage();
            echo $a -> afficherFormulaireModification(
                $_GET['archiIdAdresseModification'], 'adresse'
            );
            break;
        case 'modifierImageEvenement':
            $image = new archiImage();
            echo $image->afficherFormulaireModification(
                $_GET['archiIdEvenement'], 'evenement'
            );
            break;
        case 'modifierImagesUtilisateur':
            $authentifie = new archiAuthentification();
            $image = new archiImage();
            echo  $image->afficherFormulaireModification(
                $authentifie->getIdUtilisateur(), 'utilisateur'
            );
            break;
        case 'ajoutNouvelleAdresse':
            /* affichage du formulaire permettant d'ajouter
             * de nouvelle rues, sousquartier, quartier, ville, pays ...
             * */
            $criteres=array();
            
            if (isset($_GET['typeNew'])) {
                $criteres['typeNew']=$_GET['typeNew'];
            }
            
            $adresse = new archiAdresse();
            echo $adresse -> afficheFormulaireNouvelleAdresse($criteres);
            break;
        case 'ajouterSousEvenement':
            $evenement = new archiEvenement();
            echo $evenement->afficheFormulaire(
                $evenement->getEvenementFields('nouveauDossier'), '',
                $_GET['archiIdEvenement'], 'evenement'
            );
            break;
        case 'ajouterEvenementSurAdresse':
            $evenement = new archiEvenement();
            echo $evenement->afficheFormulaire(
                $evenement->getEvenementFields('nouveauDossier'), '',
                $_GET['archiIdAdresse'], 'adresse'
            );
            break;
        case 'ajoutImageAdresse':
            $image = new archiImage();
            echo $image->afficherFormulaireAjout(
                $_GET['archiIdAdresse'], 'adresse'
            );
            break;
        case 'ajouterEvenement':
            $ev = new archiEvenement();
            echo $ev->afficheFormulaire();
            break;
        case 'ajoutImageEvenement':
            $image = new archiImage();
            echo $image->afficherFormulaireAjout(
                $_GET['archiIdEvenement'], 'evenement'
            );
            break;
        case 'ajoutImageBibliotheque':
            $image = new archiImage();
            echo $image->afficherFormulaireAjout();
            break;
        case 'afficheImageOriginale':
            $image = new archiImage();
            echo $image->afficheImageOriginale($_GET['archiIdImage']);
            break;
        case 'comparaisonEvenement':
            $evenement = new archiEvenement();
            echo $evenement->afficheComparateur();
            break;
        
        case 'contact':
            
            $nom="";
            $prenom="";
            $email="";
            
            if ($authentification->estConnecte()) {
                $utilisateur = new archiUtilisateur();
                $arrayUtilisateur = $utilisateur
                    ->getArrayInfosFromUtilisateur(
                        $authentification->getIdUtilisateur()
                    );
                $nom=$arrayUtilisateur['nom'];
                $prenom=$arrayUtilisateur['prenom'];
                $email=$arrayUtilisateur['mail'];
            }
            
            
            $fieldsContactForm['nom']['default'] = $nom;
            $fieldsContactForm['prenom']['default'] = $prenom;
            $fieldsContactForm['email']['default'] = $email;
            
            $configFormContact['fields'] = $fieldsContactForm;
            
            $contact = new formGenerator();
            echo $contact->afficherFromArray($configFormContact);
            break;
        
        case 'affichePopupAttente':
            echo "<div style='text-align:center;".
            "border:1px solid #000000;height:148px;'>";
            echo "<div style='padding-top:20px;'>".
            "<img src='images/indicator.gif'></div>";
            echo "<div style='padding-top:20px;'>".
                "Veuillez patienter, le chargement est en cours...</div>";
            echo "</div>";
            break;
        
        case 'toutesLesDemolitions':
            $a = new archiAdresse();
            $retour = $a->afficherListe(array('toutesLesDemolitions'=>1));
            echo $retour['html'];
            break;
        
        case 'tousLesTravaux':
            $a = new archiAdresse();
            $retour = $a->afficherListe(array('tousLesTravaux'=>1));
            echo $retour['html'];
            break;
        
        case 'tousLesEvenementsCulturels':
            $a = new archiAdresse();
            $retour = $a->afficherListe(array('tousLesEvenementsCulturels'=>1));
            echo $retour['html'];
            break;
        case 'afficherListeRelative':
            $a = new archiAdresse();
            echo $a->getAdressesMemeLocalite(
                $_GET['archiIdAdresse'], $_GET['archiTypeLocalite']
            );
            break;
        
        case 'listeAdressesFromRue':
            $a = new archiAdresse();
            $tab = $a->afficherListe(
                array('recherche_rue'=>$_GET['recherche_rue'])
            );
            echo $tab['html'];
            break;
        
        case 'affichePopupDescriptionSource':
            $s = new archiSource();
            echo $s->getPopupDescriptionSource();
            break;
        
        case 'descriptionSource':
            $s=new archiSource();
            echo $s->afficheDescriptionSource($_GET['archiIdSource']);
            break;
        
        case 'statistiquesAccueil':
            $accueil = new archiAccueil();
            echo $accueil->afficheStatistiques();
            break;
        case 'afficheProfil':
            $accueil = new archiAccueil();
            echo $accueil->afficheAccueil();
            break;
        case 'tousLesArchitectesClasses':
            $accueil = new archiAccueil();
            echo $accueil->getListeArchitectesProductifs(
                array(
                    "setTitre"=>"<H1>Classement des architectes ".
                    "les plus productifs : </H1>"
                )
            );
            break;
        case 'toutesLesRuesCompletesClassees':
            $accueil = new archiAccueil();
            echo $accueil->getListeRuesCompletes(
                array(
                    "setTitre"
                        =>"<H1>Classement des rues où".
                        " figurent le plus d'adresses : </H1>"
                    )
            );
            break;
        case 'afficheLogsMails':
            $administration = new archiAdministration();
            echo $administration->getLoggedMails();
            break;
        case 'formulaireMotDePasseOublie':
            $u = new archiUtilisateur();
            echo $u->afficheFormulaireMotDePasseOublie();
            break;
        case 'nouveauMotDePasse':
            $u = new archiUtilisateur();
            echo $u->afficheFormulaireChangementMotDePasseOublie();
            break;
        case 'tousLesCommentaires':
            $a = new archiAdresse();
            echo $a->getDerniersCommentaires(
                array('afficherTous'=>true)
            );
            break;
        case 'publiciteArticlesPresse':
            $s = new archiStatic();
            echo $s->affichePresseMediaPublicite();
            break;
        case 'adminDroits':
            $d = new archiAdministration();
            echo $d->getFormulaireGestionDroits();
            break;
        case 'afficheCarteBasRhin':
            $recherche = new archiRecherche();
            echo $recherche->afficheCarteRecherche(array('zoom'=>10));
            break;
        case 'afficheFormulaireEvenementAdresseLiee':
            $e = new archiEvenement();
            echo $e->afficheFormulaireAdresseLieeEvenement(
                array('idEvenement'=>$_GET['idEvenement'])
            );
            break;
        case 'consultationHistoriqueEvenement':
            $e = new archiEvenement();
            echo $e->afficheHistoriqueEvenement(
                array('idEvenement'=>$_GET['archiIdEvenement'])
            );
            break;
        case 'affichePopupSelectionZoneVueSur':
            $i = new archiImage();
            echo $i->getImagesVueSur(
                array('modeAffichage'=>'affichePopupSelectionZoneVueSur',
                'idImage'=>$_GET['archiIdImage'])
            );
            break;
        case 'afficheSondageGrand':
            $a = new archiAccueil();
            echo $a->gestionSondage(
                array('afficheToutLeTexte'=>true,
                'modeAffichage'=>'noFormulaire')
            );
            break;
        case 'afficheSondageResultatGrand':
            $a  = new archiAccueil();
            echo $a->gestionSondage(
                array('afficheToutLeTexte'=>true,
                'modeAffichage'=>'resultatAccueil')
            );
            break;
        case 'afficheGoogleMapIframe':
            $a = new archiAdresse();
            echo $a->getGoogleMapIframe();
            break;
        case 'afficherActualite':
            $a = new archiAccueil();
            echo $a->getActualiteDetail();
            break;
        case 'toutesLesVues':
            $i = new archiImage();
            echo $i->getHtmlToutesLesVues();
            break;
        case 'toutesLesActualites':
            $a = new archiAccueil();
            echo $a->getHtmlToutesLesActualites();
            break;
        case 'majGoogleMapNewCenter':
            $a = new archiAdresse();
            echo $a->getJsGoogleMapNewCenter();
            break;
        case 'adminListeParcours':
            $admin = new archiAdministration();
            echo $admin->getHtmlAdminParcoursListe();
            break;
        case 'ajouterParcoursFormulaireAdmin':
        case 'modifierParcoursFormulaireAdmin':
            $admin = new archiAdministration();
            echo $admin->getHtmlFormulaireParcours();
            break;
        case 'etapesParcoursFormulaire':
            $admin = new archiAdministration();
            echo $admin->getHtmlGestionEtapesParcours();
            break;
        case 'carteGoogleMapParcoursArt':
            $adresse = new archiAdresse();
            echo $adresse->getGoogleMapParcours(
                array('getCoordonneesParcours'=>true)
            );
            break;
        case 'parcours':
            $a = new archiAdresse();
            echo $a->getParcoursListe();
            break;
        case 'detailParcours':
            $a = new archiAdresse();
            echo $a->getParcoursDetail();
            break;
        case 'detailProfilPublique':
            $u = new archiUtilisateur();
            echo $u->afficheProfilPublique();
            break;
        case 'donAnnule':
            echo "<h1>Faire un don</h1><br>";
            echo "La transaction a été annulée. <a href='/'>Retour</a>";
            break;
        case 'donOk':
            echo "<h1>Faire un don</h1><br>";
            echo "archi-strasbourg.org vous remercie pour votre don. ".
                "<a href='/'>Retour</a>";
            break;
        case 'afficheImagesFrom':
            $i = new archiImage();
            echo $i->afficheImagesFromUtilisateurDebug();
            break;
        case 'testImagesCRC':
            $i = new archiImage();
            echo $i->testImagesCRC();
            break;
        case 'nosSources':
            $s = new archiSource();
            echo $s->afficherListeSourcesAvecLogos();
            break;
        case 'listeTypeSourceDependancesSourcesAdmin':
            $s = new archiSource();
            echo $s->listeSourcesDependantsDeTypeSource();
            break;
        case 'adminActualites':
            $admin = new archiAdministration();
            echo $admin->listeActualites();
            echo $admin->formulaireActualites();
            break;
        case "previsualisationActualite":
            $admin = new archiAdministration();
            echo $admin->previsualisationActualitePopup();
            break;
        case "rechercheAvancee":
            $r = new archiRecherche();
            echo $r->afficheFormulaireRechercheAvancee();
            break;
        case "resultatsRechercheAvancee":
            $r = new archiRecherche();
            echo $r->afficheResultatsRechercheAvancee();
            break;
        case "page":
            include "inc/page.php";
            break;
        case "adminPages":
            include "inc/admin/pages.php";
            break;
        case "editPage":
            include "inc/admin/editPage.php";
            break;
        case "batchLicence":
            include "inc/user/batchLicence.php";
            break;
        }
    }
}

if ((count($_POST)==0 && count($_GET)==0) 
    || (count($_GET)==1 && isset($_GET["lang"]))
    || (isset($_GET['archiAffichage'])
    && $_GET['archiAffichage']=='afficheAccueil')
) {
    
    
    
    $accueil = new archiAccueil();
    //echo $accueil->gestionSondage(array('modeAffichage'=>'resultatAccueil'));
    echo $accueil->afficheAccueil();
}

?>
