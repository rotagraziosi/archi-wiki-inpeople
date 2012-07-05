<?php
/**
 * Classe ArchiAdministration
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
/**
 * Gère certaines fonctions liées à l'administration
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
class ArchiAdministration extends config
{
    /**
     * Constructeur d'archiAdministration
     * 
     * @return void
     * */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Affichage du menu d'administration
     * 
     * @return string HTML
     * */
    public function afficheMenu()
    {
        $html                                    = "";
        $t                                       = new Template('modules/archi/templates/');
        $t->set_filenames((array('administration'=>'administration.tpl')));
        $menu                                    = array();
        $u                                       = new archiUtilisateur();
        $a                                       = new archiAuthentification();
        $idUtilisateur                           = $a->getIdUtilisateur();
        if ($u->isAuthorized('admin_droits', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Droits")=>$this->creerUrl('', 'adminDroits')));
        }
        if ($u->isAuthorized('admin_actualites', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Actualités")=>$this->creerUrl('', 'adminActualites')));
        }
        $menu[_("Pages")] = $this->creerUrl('', 'adminPages', array());
        if ($u->isAuthorized('admin_sources', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Sources")=>$this->creerUrl('', 'administration', array('tableName'=>'source'))));
        }
        if ($u->isAuthorized('admin_types_sources', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Types de sources")=>$this->creerUrl('', 'administration', array('tableName'=>'typeSource'))));
        }
        if ($u->isAuthorized('admin_types_structures', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Types de structures")=>$this->creerUrl('', 'administration', array('tableName'=>'typeStructure'))));
        }
        if ($u->isAuthorized('admin_courants_architecturaux', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Courant architecturaux")=>$this->creerUrl('', 'administration', array('tableName'=>'courantArchitectural'))));
        }
        $menu[_("Gestion des personnes")] = array();
        if ($u->isAuthorized('admin_metiers', $idUtilisateur)) {
            $menu[_("Gestion des personnes")] = array_merge($menu[_("Gestion des personnes")], array(_("Métiers")=>$this->creerUrl('', 'administration', array('tableName'=>'metier'))));
        }
        if ($u->isAuthorized('admin_personnes', $idUtilisateur)) {
            $menu[_("Gestion des personnes")] = array_merge($menu[_("Gestion des personnes")], array(_("Modifier une personne")=>$this->creerUrl('', 'adminPersonne', array())));
        }
        $menu[_("Gestion des adresses")] = array();
        if ($u->isAuthorized('admin_adresses', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Ajout d'éléments d'adresse (rue, sous-quartier, quartier, ville, pays)")=>$this->creerUrl('', 'ajoutNouvelleAdresse', array())));
        }
        if ($u->isAuthorized('admin_rues', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Gérer les rues")=>$this->creerUrl('', 'adminElementAdresse', array('tableName'=>'rue'))));
        }
        if ($u->isAuthorized('admin_sousQuartiers', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Gérer les sous-quartiers")=>$this->creerUrl('', 'adminElementAdresse', array('tableName'=>'sousQuartier'))));
        }
        if ($u->isAuthorized('admin_quartiers', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Gérer les quartiers")=>$this->creerUrl('', 'adminElementAdresse', array('tableName'=>'quartier'))));
        }
        if ($u->isAuthorized('admin_villes', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Gérer les villes")=>$this->creerUrl('', 'adminElementAdresse', array('tableName'=>'ville'))));
        }
        if ($u->isAuthorized('admin_pays', $idUtilisateur)) {
            $menu[_("Gestion des adresses")] = array_merge($menu[_("Gestion des adresses")], array(_("Gérer les pays")=>$this->creerUrl('', 'adminElementAdresse', array('tableName'=>'pays'))));
        }
        if ($u->isAuthorized('admin_affiche_resultats_sondages', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Voir les statistiques des sondages")=>$this->creerUrl('', 'adminSondages', array())));
        }
        if ($u->isAuthorized('admin_parcours', $idUtilisateur)) {
            $menu = array_merge($menu, array(_("Gérer les parcours")=>$this->creerUrl('', 'adminListeParcours', array())));
        }
        if ($u->isAuthorized('admin_traduction', $idUtilisateur)) {
            $menu[_("Traduction")] = array(_("Traduction")=>$this->tradLink, _("Détecter les nouvelles chaines à traduire")=>"script/updateTranslation.php", _("Appliquer les nouvelles traductions")=>"script/applyTranslation.php");;
            //$menu[_("Détecter les nouvelles chaines à traduire")] = "script/updateTranslation.php";
            //$menu[_("Appliquer les nouvelles traductions")] = "script/applyTranslation.php";
        }
        foreach ($menu as $nom=>$url) {
            if (is_array($url)) {
                $menuHTML=$nom._(" :");
                $menuHTML.="<ul>";
                foreach ($url as $subName=>$subURL) {
                    $menuHTML.="<li><a href='".$subURL."'>".$subName."</a></li>";
                }
                $menuHTML.="</ul>";
            } else {
                $menuHTML="<a href='".$url."'>".$nom."</a>";
                //<a href="{listeMenu.url}">{listeMenu.nom}</a>
            }
            $t->assign_block_vars('listeMenu', array("menu"=>$menuHTML));
        }
        ob_start();
        $t->pparse('administration');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    /**
     * Cette fonction va appeler le generateur de formulaire qui va permettre de gerer la table voulue
     * 
     * @param array  $parametres    Paramètres
     * @param string $modeAffichage Mode d'affichage
     * 
     * @return string HTML
     * */
    public function administre($parametres = array(), $modeAffichage = 'liste')
    {
        $html          = "";
        $liensExternes = array();
        $nomTable      = '';
        if (isset($parametres['tableName']) && $parametres['tableName']!='') {
            $nomTable = $parametres['tableName'];
        }
        $dependances = array();
        switch ($nomTable) {
        case "rue":
            $liensExternes = array('idSousQuartier'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idSousQuartier', 'externalTable'=>'sousQuartier', 'externalFieldToDisplay'=>'nom'));
            break;

        case "ville":
            $liensExternes = array('idPays'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idPays', 'externalTable'=>'pays', 'externalFieldToDisplay'=>'nom'));
            break;

        case "quartier":
            $liensExternes = array('idVille'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idVille', 'externalTable'=>'ville', 'externalFieldToDisplay'=>'nom'));
            break;

        case "sousQuartier":
            $liensExternes = array('idQuartier'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idQuartier', 'externalTable'=>'quartier', 'externalFieldToDisplay'=>'nom'));
            break;

        case "source":
            $liensExternes  = array('idTypeSource'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idTypeSource', 'externalTable'=>'typeSource', 'externalFieldToDisplay'=>'nom'));
            $parametres     = array_merge($parametres, array('afficheMiseEnFormeLongText'=>true, 'displayWithBBCode'=>true, 'fieldsNotInBdd'=>array(0=>array('name'=>'uploadLogo', 'type'=>'uploadImageLiee', 'redimFilesAndSizesConfig'=>array(0=>array('taille'=>0, 'nomFichierDestinationParametre'=>"###bddField[idSource]###_original.jpg", 'repertoireDestination'=>$this->getCheminPhysique()."images/logosSources/"), 1=>array('taille'=>200, 'nomFichierDestinationParametre'=>"###bddField[idSource]###.jpg", 'repertoireDestination'=>$this->getCheminPhysique()."images/logosSources/")), 'valueParametrable'=>array('affichageParametre'=>"<img src='".$this->getUrlImage()."logosSources/###bddField[idSource]###.jpg' border=0>", "cheminFichierATesterPourAffichage"=>$this->getCheminPhysique()."images/logosSources/###bddField[idSource]###.jpg"))), 'afficheDependancesInIFrameUrl'=>$this->creerUrl('', 'listeAdressesFromSource', array('source'=>'###currentId###', 'submit'=>'Rechercher', 'noHeaderNoFooter'=>1, 'noDescription'=>1, 'modeAdmin'=>1))));
            $dependances[0] = array('table'=>'historiqueEvenement', 'champLie'=>'idSource', 'message'=>"il existe des dépendances au niveau de la table des évènements");
            $dependances[1] = array('table'=>'historiqueImage', 'champLie'=>'idSource', 'message'=>"il existe des dépendances au niveau de la table des images");
            break;

        case 'typeSource':
            $dependances[0] = array('table'=>'source', 'champLie'=>'idTypeSource', 'message'=>"il existe des dépendances au niveau de la table des sources");
            $parametres = array_merge($parametres, array('afficheDependancesInIFrameUrl'=>$this->creerUrl('', 'listeTypeSourceDependancesSourcesAdmin', array('idTypeSource'=>'###currentId###', 'noHeaderNoFooter'=>1))));
            break;
        }
        $generateur = new formGenerator();

        // est ce que l'on effectue d'abord une action ? (ajout modif suppression ?)
        if (isset($this->variablesPost['validationFormulaireAdministration'])) {

            // si le formulaire a ete validé
            // actions
            switch ($this->variablesPost['validationFormulaireAdministration']) {
            case 'modification':
                $generateur->modifier($parametres, $liensExternes);
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes, $dependances);
                break;
            case 'ajout':
                $generateur->ajouter($parametres, $liensExternes);
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes);
                break;
            case 'suppression':
                $generateur->supprimer($parametres, $liensExternes, $dependances);
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes, $dependances);
                break;
            case 'rechercheAdministration':
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes);
                break;
            case 'suppressionFromModifForm':
                $generateur->supprimerFromModifForm($parametres, $liensExternes, $dependances);
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes, $dependances);
                break;
            }
        } else {
            // affichage des formulaires
            switch ($modeAffichage) {
            case 'liste':
                $html .= $generateur->afficheFormulaireListe($parametres, $liensExternes, $dependances);
                break;

            case 'modification':
                $adresse = new archiAdresse();

                //$html.=$adresse->afficheFormulaireModificationElementAdresse($parametres);
                $html .= $generateur->afficheFormulaireModification($parametres, $liensExternes, $dependances);
                break;

            case 'ajout':
                $html .= $generateur->afficheFormulaireAjout($parametres, $liensExternes);
                break;
            }
        }
        return $html;
    }

    /** 
     * Récupere la liste des mails loggés
     * Si un parametres idUtilisateur est précisé en GET , on affiche pas la colonne des mails ( inutile sur la fiche de l'utilisateur)
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getLoggedMails($params = array())
    {
        $html             = "";
        $titre            = "<h1>Log des mails du site";
        $sqlSelect        = "";
        $arrayUtilisateur = array();
        if (isset($this->variablesGet['idUtilisateur'])) {
            $u                     = new archiUtilisateur();
            $arrayInfosUtilisateur = $u->getArrayInfosFromUtilisateur($this->variablesGet['idUtilisateur']);
            $sqlSelect             = " AND destinataire='".$arrayInfosUtilisateur['mail']."' ";
            $arrayUtilisateur      = array('idUtilisateur'=>$this->variablesGet['idUtilisateur']);

            // pour le passage de parametres
            $titre .= " pour ".$arrayInfosUtilisateur['nom']." ".$arrayInfosUtilisateur['prenom'];
        }
        $titre .= "</h1>";
        $html .= $titre;

        // gestion de la recherche
        $sqlSelectRecherche = "";
        if (isset($this->variablesGet['recherche']) && $this->variablesGet['recherche']=='1' && isset($this->variablesPost['motCleRechercheLogsMail'])) {
            $motCle                    = $this->variablesPost['motCleRechercheLogsMail'];
            $arraySqlSelectRecherche[] = "destinataire LIKE \"%".$motCle."%\"";
            $arraySqlSelectRecherche[] = "sujet LIKE \"%".$motCle."%\"";
            $arraySqlSelectRecherche[] = "message LIKE \"%".$motCle."%\"";
            $arraySqlSelectRecherche[] = "date LIKE \"%".$motCle."%\"";
            $sqlSelectRecherche        = " AND (".implode(" OR ", $arraySqlSelectRecherche).") ";
        }

        // formulaire de recherche
        $f = new formGenerator();
        $configFields = array("motCleRechercheLogsMail"=>array('type'=>'text', 'htmlCode'=>'', 'error'=>'', 'libelle'=>'Recherche :', 'default'=>'', 'value'=>'', 'required'=>false), "pageCourante"=>array('type'=>'hidden', 'htmlCode'=>'', 'error'=>'', 'default'=>'', 'value'=>'', 'required'=>false));
        if (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='utilisateurDetail') {
            $configForm = array('titrePage'=>'', 'fields'=>$configFields, 'submitButtonValue'=>'Envoyer', 'formAction'=>$this->creerUrl('', 'utilisateurDetail', array_merge(array('recherche'=>1), $arrayUtilisateur)), 'templateFileName'=>'rechercheLogsMail.tpl', 'formName'=>'formRechercheLogsMail');
        } else {
            $configForm = array('titrePage'=>'', 'fields'=>$configFields, 'submitButtonValue'=>'Envoyer', 'formAction'=>$this->creerUrl('', 'afficheLogsMails', array_merge(array('recherche'=>1), $arrayUtilisateur)), 'templateFileName'=>'rechercheLogsMail.tpl', 'formName'=>'formRechercheLogsMail');
        }
        $html                    .= $f->afficherFromArray($configForm);
        $reqCount                 = "SELECT idMail FROM logMails WHERE 1=1 $sqlSelect $sqlSelectRecherche";
        $resCount                 = $this->connexionBdd->requete($reqCount);
        $nbEnregistrementTotaux   = mysql_num_rows($resCount);
        $pagination               = new paginationObject();
        $nbEnregistrementsParPage = 15;
        $arrayPagination          = $pagination->pagination(array('nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux, 'typeLiens'=>'formulaire', 'champPageCourante'=>'pageCourante', 'nomParamPageCourante'=>'pageCourante', 'idFormulaire'=>'formRechercheLogsMail'));
        $req                      = "
                SELECT * 
                FROM logMails 
                LEFT JOIN utilisateur ON utilisateur.mail = logMails.destinataire 
                WHERE 1=1 $sqlSelect $sqlSelectRecherche
        ";

        // on affiche pas les mails de debugs
        $req     = $pagination->addLimitToQuery($req);
        $res     = $this->connexionBdd->requete($req);
        $tableau = new tableau();
        if (!isset($this->variablesGet['idUtilisateur'])) {
            $tableau->addValue("<h3>Destinataire</h3>");
        }
        $tableau->addValue("<h3>Sujet</h3>");
        $tableau->addValue("<h3>Message</h3>");
        $tableau->addValue("<h3>Date</h3>");
        while ($fetch = mysql_fetch_assoc($res)) {
            if (!isset($this->variablesGet['idUtilisateur'])) {
                $tableau->addValue("<a href='".$this->creerUrl('', 'afficheLogsMails', array('idUtilisateur'=>$fetch["idUtilisateur"]))."'>".$fetch["destinataire"]."</a>");
            }
            $tableau->addValue(stripslashes(stripslashes($fetch["sujet"])));
            $tableau->addValue(stripslashes("<textarea style='width:250px;height:150px;'>".stripslashes($fetch["message"]))."</textarea>");
            $tableau->addValue($this->date->toFrench($fetch["date"]));
        }
        $html .= $arrayPagination['html'];
        $nbColonnes = 3;
        if (!isset($this->variablesGet['idUtilisateur'])) {
            $nbColonnes = 4;
        }
        $html .= $tableau->createHtmlTableFromArray($nbColonnes);
        $html .= $arrayPagination['html'];
        return $html;
    }

    /**
     * Affichage du formulaire de gestion des droits
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getFormulaireGestionDroits($params = array())
    {
        $d                          = new droitsObject();
        $arrayProfilCourant         = $d->getProfilCourantFormulaire();
        $retour                     = "<h1>Gestion des droits ".$arrayProfilCourant['libelle']."</h1>";
        $paramsUrl                  = array();
        $paramsUrl['archiIdProfil'] = $arrayProfilCourant['idProfil'];
        $retour                    .= "<form action='".$this->creerUrl('enregistreDroits', 'adminDroits', $paramsUrl)."' method='POST' enctype='multipart/form-data' name='formDroits'>";
        $arrayListeProfils          = $d->getArrayListeProfils();
        foreach ($arrayListeProfils as $idProfil=>$intitule) {
            $retour .= "<a href='".$this->creerUrl('', 'adminDroits', array('archiIdProfil'=>$idProfil))."'>".$intitule."</a> ";
        }
        $retour                  .= "<br><br>";
        $retour                  .= $d->getFormulaireGestionDroits($params);
        $retour                  .= "<input type='submit' name='submitDroits' value='Enregistrer'>";
        $retour                  .= "</form>";
        $reqUtilisateursCount     = "SELECT 0 FROM utilisateur WHERE idProfil = '".$arrayProfilCourant['idProfil']."'";
        $resUtilisateursCount     = $this->connexionBdd->requete($reqUtilisateursCount);
        $p                        = new paginationObject();
        $nbEnregistrementsParPage = 20;
        $arrayPagination          = $p->pagination(array('nomParamPageCourante'=>'archiPageCouranteVille', 'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 'nbEnregistrementsTotaux'=>mysql_num_rows($resUtilisateursCount), 'typeLiens'=>'noformulaire'));
        $reqUtilisateurs          = "SELECT nom, prenom, mail FROM utilisateur WHERE idProfil = '".$arrayProfilCourant['idProfil']."'";
        $reqUtilisateurs          = $p->addLimitToQuery($reqUtilisateurs);
        $resUtilisateurs          = $this->connexionBdd->requete($reqUtilisateurs);
        $t                        = new tableau();
        while ($fetchUtilisateurs = mysql_fetch_assoc($resUtilisateurs)) {
            $t->addValue($fetchUtilisateurs['nom']);
            $t->addValue($fetchUtilisateurs['prenom']);
            $t->addValue($fetchUtilisateurs['mail']);
        }
        if (mysql_num_rows($resUtilisateurs)>0) {
            $retour .= "<b>Liste des utilisateurs concernés :</b><br><br>".$arrayPagination['html']."<br>".$t->createTable(3);
        }
        return $retour;
    }

    /**
     * Affiche la liste des rue pour l'administration
     * 
     * @return string HTML
     * */
    public function getAdminListeRues()
    {
        $html     = "";
        $a        = new archiAuthentification();
        $u        = new archiUtilisateur();
        $sqlWhere = "";

        // si l'utilisateur est un moderateur on le limite a la gestion des rues des villes qu'il modere
        if ($a->getIdProfil()==3) {
            $arrayVillesFromModerateur = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            if (count($arrayVillesFromModerateur)>0) {
                $sqlWhere = " AND r.idSousQuartier in (SELECT idSousQuartier FROM sousQuartier WHERE idQuartier IN (SELECT idQuartier FROM quartier WHERE idVille in (".implode(",", $arrayVillesFromModerateur)."))) ";
            } else {
                $sqlWhere = " AND r.idRue='0' ";
            }
        }

        // recherche
        $objetDeLaRecherche = "";
        $sqlRecherche = "";
        if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='rechercheAdministration' && $this->variablesPost['rechercheFormulaireAdministration']!='') {
            $objetDeLaRecherche = stripslashes($this->variablesPost['rechercheFormulaireAdministration']);
            $sqlRecherche = " AND LOWER(CONCAT_WS(' ',prefixe,r.nom,sq.nom,q.nom,v.nom)) LIKE \"%".pia_strtolower($objetDeLaRecherche)."%\" ";
        }

        // suppression
        // on verifie les dependances
        if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='suppression') {
            // gestion de la suppression
            if (isset($this->variablesPost['selection']) && count($this->variablesPost['selection'])>0) {
                //nom de champ identifiant
                $champIdentifiant = "idRue";
                $dependances[0]   = array('table'=>'historiqueAdresse', 'champLie'=>'idRue', 'message'=>"Attention il existe des dépendances au niveau de la table historiqueAdresse");
                $erreurObj        = new objetErreur();
                if (count($dependances)>0) {
                    foreach ($dependances as $indice=>$dependance) {
                        $reqVerifDependance = "SELECT * FROM ".$dependance['table']." WHERE ".$dependance['champLie']." in (".implode(",", $this->variablesPost['selection']).")";
                        $resVerifDependance = $this->connexionBdd->requete($reqVerifDependance);
                        if (mysql_num_rows($resVerifDependance)>0) {
                            $erreurObj->ajouter($dependance['message']);
                        }
                    }
                    if ($erreurObj->getNbErreurs()>0) {
                        $erreurObj->ajouter("La suppression n'a pu être effectuée, veuillez contacter l'administrateur de la base de données");
                    }
                    $html .= $erreurObj->afficher();
                }
                if ($erreurObj->getNbErreurs()==0) {
                    $reqDelete = "delete from rue where ".$champIdentifiant." in (".implode(",", $this->variablesPost['selection']).")";
                    $resDelete = $this->connexionBdd->requete($reqDelete);
                    echo "suppression effectuée<br>";
                }
            }
        }
        $reqCount        = "SELECT r.idRue as idRue, r.nom as nomRue, r.prefixe as prefixe, sq.nom as nomSousQuartier, q.nom as nomQuartier,v.nom as nomVille
            FROM rue r
            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
            LEFT JOIN ville v ON v.idVille = q.idVille
            WHERE 1=1
            $sqlWhere
            $sqlRecherche
            ";
        $resCount        = $this->connexionBdd->requete($reqCount);
        $nbLignesTotales = mysql_num_rows($resCount);

        // pagination
        $nbEnregistrementsParPage = 20;
        $pagination               = new paginationObject();
        $arrayPagination          = $pagination->pagination(array('nomParamPageCourante'=>'pageCourantePagination', 'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 'nbEnregistrementsTotaux'=>$nbLignesTotales, 'typeLiens'=>'formulaire', 'idFormulaire'=>'formListe', 'champPageCourante'=>'pageCourantePagination', 'nomChampActionFormulaireOnSubmit'=>'validationFormulaireAdministration', 'nomActionFormulaireOnSubmit'=>'rechercheAdministration'));
        $req                      = "
        
            SELECT r.idRue as idRue, r.nom as nomRue, r.prefixe as prefixe, sq.nom as nomSousQuartier, q.nom as nomQuartier,v.nom as nomVille
            FROM rue r
            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
            LEFT JOIN ville v ON v.idVille = q.idVille
            WHERE 1=1 $sqlWhere $sqlRecherche
            LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
        $res                      = $this->connexionBdd->requete($req);
        $tableau                  = new tableau();
        $tableau->addValue("Sélection");
        $tableau->addValue("idRue");
        $tableau->addValue("prefixe");
        $tableau->addValue("nom de la rue");
        $tableau->addValue("localisation");
        while ($fetch = mysql_fetch_assoc($res)) {
            if ($fetch['idRue']!=0) {
                $tableau->addValue("<input type='checkbox' name='selection[]' value='".$fetch['idRue']."'>");
                $tableau->addValue("<a href='".$this->creerUrl('', 'adminAdresseDetail', array('tableName'=>'rue', 'idModification'=>$fetch['idRue']))."'>".$fetch['idRue']."</a>");
                $tableau->addValue(stripslashes($fetch['prefixe']));
                $tableau->addValue(stripslashes($fetch['nomRue']));
                $localisation = array();
                if ($fetch['nomSousQuartier']!='' && $fetch['nomSousQuartier']!='autre') {
                    $localisation[] = $fetch['nomSousQuartier'];
                }
                if ($fetch['nomQuartier']!='' && $fetch['nomQuartier']!='autre') {
                    $localisation[] = $fetch['nomQuartier'];
                }
                if ($fetch['nomVille']!='' && $fetch['nomVille']!='autre') {
                    $localisation[] = $fetch['nomVille'];
                }
                $tableau->addValue(stripslashes(implode(" - ", $localisation)));
            }
        }
        $html .= "<form name='formListe' id='formListe' enctype='multipart/form-data' method='POST'>";
        $html .= "Recherche : <input type='text' name='rechercheFormulaireAdministration' id='rechercheFormulaireAdministration' value='".$objetDeLaRecherche."'>";
        $html .= "<input type='submit' onclick=\"document.getElementById('validationFormulaireAdministration').value='rechercheAdministration';\"><br>";
        $html .= $arrayPagination['html'].'<br>';
        $html .= "<input type='hidden' name='pageCourantePagination' id='pageCourantePagination' value=''>";
        $html .= "<input type='hidden' name='validationFormulaireAdministration' id='validationFormulaireAdministration' value=''>";
        $html .= $tableau->createHtmlTableFromArray(5);
        $html .= "<input type='submit' value='Supprimer la selection' onclick=\"document.getElementById('validationFormulaireAdministration').value='suppression';\" name='supprimer'>";
        $html .= "<input type='button' name='ajouter' value='ajouter' onclick=\"location.href='".$this->creerUrl('', 'ajoutNouvelleAdresse')."';\">";
        $html .= "</form>";
        return $html;
    }

    /**
     * Affiche la liste des rue pour l'administration
     * 
     * @return string HTML
     * */
    public function getAdminListeSousQuartiers()
    {
        $html     = "";
        $a        = new archiAuthentification();
        $u        = new archiUtilisateur();
        $sqlWhere = "";

        // si l'utilisateur est un moderateur on le limite a la gestion des rues des villes qu'il modere
        if ($a->getIdProfil()==3) {
            $arrayVillesFromModerateur = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            if (count($arrayVillesFromModerateur)>0) {
                $sqlWhere = " AND sq.idQuartier in (SELECT idQuartier FROM quartier WHERE idVille in (".implode(",", $arrayVillesFromModerateur).")) ";
            } else {
                $sqlWhere = " AND sq.idQuartier='0' ";
            }
        }

        // recherche
        $objetDeLaRecherche = "";
        $sqlRecherche = "";
        if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='rechercheAdministration' && $this->variablesPost['rechercheFormulaireAdministration']!='') {
            $objetDeLaRecherche = stripslashes($this->variablesPost['rechercheFormulaireAdministration']);
            $sqlRecherche = " AND LOWER(CONCAT_WS(' ',sq.nom,q.nom,v.nom)) LIKE \"%".pia_strtolower($objetDeLaRecherche)."%\" ";
        }

        // suppression
        // on verifie les dependances
        if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='suppression') {
            // gestion de la suppression
            if (isset($this->variablesPost['selection']) && count($this->variablesPost['selection'])>0) {
                //nom de champ identifiant
                $champIdentifiant = "idSousQuartier";
                $dependances[0]   = array('table'=>'rue', 'champLie'=>'idSousQuartier', 'message'=>"Attention il existe des dépendances au niveau de la table des rues");
                $dependances[1]   = array('table'=>'historiqueAdresse', 'champLie'=>'idSousQuartier', 'message'=>"Attention il existe des dépendances au niveau de la table historiqueAdresse");
                $erreurObj        = new objetErreur();
                if (count($dependances)>0) {
                    foreach ($dependances as $indice=>$dependance) {
                        $reqVerifDependance = "SELECT * FROM ".$dependance['table']." WHERE ".$dependance['champLie']." in (".implode(",", $this->variablesPost['selection']).")";
                        $resVerifDependance = $this->connexionBdd->requete($reqVerifDependance);
                        if (mysql_num_rows($resVerifDependance)>0) {
                            $erreurObj->ajouter($dependance['message']);
                        }
                    }
                    if ($erreurObj->getNbErreurs()>0) {
                        $erreurObj->ajouter("La suppression n'a pu être effectuée, veuillez contacter l'administrateur de la base de données");
                    }
                    $html .= $erreurObj->afficher();
                }
                if ($erreurObj->getNbErreurs()==0) {
                    $reqDelete = "delete from sousQuartier where ".$champIdentifiant." in (".implode(",", $this->variablesPost['selection']).")";
                    $resDelete = $this->connexionBdd->requete($reqDelete);
                    echo "suppression effectuée<br>";
                }
            }
        }
        $reqCount        = "SELECT sq.nom as nomSousQuartier, q.nom as nomQuartier,v.nom as nomVille
            FROM sousQuartier sq
            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
            LEFT JOIN ville v ON v.idVille = q.idVille
            WHERE 1=1
            $sqlWhere
            $sqlRecherche
            ";
        $resCount        = $this->connexionBdd->requete($reqCount);
        $nbLignesTotales = mysql_num_rows($resCount);

        // pagination
        $nbEnregistrementsParPage = 20;
        $pagination               = new paginationObject();
        $arrayPagination          = $pagination->pagination(array('nomParamPageCourante'=>'pageCourantePagination', 'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 'nbEnregistrementsTotaux'=>$nbLignesTotales, 'typeLiens'=>'formulaire', 'idFormulaire'=>'formListe', 'champPageCourante'=>'pageCourantePagination', 'nomChampActionFormulaireOnSubmit'=>'validationFormulaireAdministration', 'nomActionFormulaireOnSubmit'=>'rechercheAdministration'));
        $req                      = "
        
            SELECT sq.idSousQuartier as idSousQuartier,sq.nom as nomSousQuartier, q.nom as nomQuartier,v.nom as nomVille
            FROM sousQuartier sq
            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
            LEFT JOIN ville v ON v.idVille = q.idVille
            WHERE 1=1 $sqlWhere $sqlRecherche
            LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
        $res                      = $this->connexionBdd->requete($req);
        $tableau                  = new tableau();
        $tableau->addValue("Sélection");
        $tableau->addValue("idSousQuartier");
        $tableau->addValue("nom du sous quartier");
        $tableau->addValue("localisation");
        while ($fetch = mysql_fetch_assoc($res)) {
            $tableau->addValue("<input type='checkbox' name='selection[]' value='".$fetch['idSousQuartier']."'>");
            $tableau->addValue("<a href='".$this->creerUrl('', 'adminAdresseDetail', array('tableName'=>'sousQuartier', 'idModification'=>$fetch['idSousQuartier']))."'>".$fetch['idSousQuartier']."</a>");
            $tableau->addValue(stripslashes($fetch['nomSousQuartier']));
            $localisation = array();
            if ($fetch['nomQuartier']!='' && $fetch['nomQuartier']!='autre') {
                $localisation[] = $fetch['nomQuartier'];
            }
            if ($fetch['nomVille']!='' && $fetch['nomVille']!='autre') {
                $localisation[] = $fetch['nomVille'];
            }
            $tableau->addValue(stripslashes(implode(" - ", $localisation)));
        }
        $html .= "<form name='formListe' id='formListe' enctype='multipart/form-data' method='POST'>";
        $html .= "Recherche : <input type='text' name='rechercheFormulaireAdministration' id='rechercheFormulaireAdministration' value='".$objetDeLaRecherche."'>";
        $html .= "<input type='submit' onclick=\"document.getElementById('validationFormulaireAdministration').value='rechercheAdministration';\"><br>";
        $html .= $arrayPagination['html'].'<br>';
        $html .= "<input type='hidden' name='pageCourantePagination' id='pageCourantePagination' value=''>";
        $html .= "<input type='hidden' name='validationFormulaireAdministration' id='validationFormulaireAdministration' value=''>";
        $html .= $tableau->createHtmlTableFromArray(4);
        $html .= "<input type='submit' value='Supprimer la selection' onclick=\"document.getElementById('validationFormulaireAdministration').value='suppression';\" name='supprimer'>";
        $html .= "<input type='button' name='ajouter' value='ajouter' onclick=\"location.href='".$this->creerUrl('', 'ajoutNouvelleAdresse', array('typeNew'=>'newSousQuartier'))."';\">";
        $html .= "</form>";
        return $html;
    }
    /**
     * Afficher la liste des parcours dans l'administration
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getHtmlAdminParcoursListe($params = array())
    {
        $html     = "<h1>Liste des parcours</h1>";
        $html    .= "<form action='' name='listesParcours' id='listesParcours' method='POST' enctype='multipart/form-data'>";
        $reqListe = "SELECT idParcours,libelleParcours FROM parcoursArt";
        $resListe = $this->connexionBdd->requete($reqListe);
        $t        = new tableau();
        $t->addValue('selection');
        $t->addValue('idParcours');
        $t->addValue('libelle');
        while ($fetchListe = mysql_fetch_assoc($resListe)) {
            $t->addValue("<input type='checkbox' name='selectParcours[]' id='selectParcours".$fetchListe['idParcours']."' value='".$fetchListe['idParcours']."'>");
            $t->addValue("<a href='".$this->creerUrl('', 'modifierParcoursFormulaireAdmin', array('archiIdParcours'=>$fetchListe['idParcours']))."'>".$fetchListe['idParcours']."</a>");
            $t->addValue("<a href='".$this->creerUrl('', 'etapesParcoursFormulaire', array('archiIdParcours'=>$fetchListe['idParcours']))."'>".stripslashes($fetchListe['libelleParcours'])."</a>");
        }
        $html .= $t->createHtmlTableFromArray(3);
        $html .= "<input type='button' name='supprSelection' id='supprSelection' value='Supprimer la sélection' onclick=\"if(confirm('Etes-vous certain de vouloir supprimer les parcours sélectionnés ?')){document.getElementById('listesParcours').action='".$this->creerUrl('supprimerParcours', 'adminListeParcours', array())."';document.getElementById('listesParcours').submit();}\">";
        $html .= "<input type='button' name='ajouterParcours' id='ajouterParcours' value='Ajouter' onclick=\"location.href='".$this->creerUrl('', 'ajouterParcoursFormulaireAdmin', array())."';\">";
        $html .= "</form>";
        return $html;
    }

    /**
     * Supprimer un ou plusieurs parcours (sélection dans la liste par checkbox)
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function supprimerParcours($params = array())
    {
        if (isset($this->variablesPost['selectParcours']) && is_array($this->variablesPost['selectParcours']) && count($this->variablesPost['selectParcours'])>0) {
            foreach ($this->variablesPost['selectParcours'] as $indice=>$value) {
                if ($value!='') {
                    $reqSupprEtapes   = "DELETE FROM etapesParcoursArt WHERE idParcours='".$value."'";
                    $resSupprEtapes   = $this->connexionBdd->requete($reqSupprEtapes);
                    $reqSupprParcours = "DELETE FROM parcoursArt WHERE idParcours='".$value."'";
                    $resSupprParcours = $this->connexionBdd->requete($reqSupprParcours);
                }
            }
        }
    }
    
    /**
     * Afficher le formulaire d'ajout/édition de parcours
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getHtmlFormulaireParcours($params = array())
    {
        $html                = "";
        $bbCode              = new bbCodeObject();
        $recherche           = new archiRecherche();
        $idParcours          = 0;
        $libelleParcours     = "";
        $titre               = "Ajout d'un parcours";
        $formAction          = $this->creerUrl('ajouterParcours', 'adminListeParcours', array());
        $checkedActif        = false;
        $dateAjout           = date("d/m/Y");
        $commentaireParcours = "";
        $idSource            = "0";
        $libelleSource       = "";
        if (isset($this->variablesGet['archiIdParcours']) && $this->variablesGet['archiIdParcours']!='') {
            $d                   = new dateObject();
            $titre               = "Modification d'un parcours";
            $idParcours          = $this->variablesGet['archiIdParcours'];
            $reqParcours         = "
            SELECT p.idParcours as idParcours,p.libelleParcours as libelleParcours,p.isActif as isActif,p.dateAjoutParcours as dateAjoutParcours,p.commentaireParcours as commentaireParcours,
                    s.nom as nomSource, tp.nom as nomTypeSource, s.idSource as idSource
            FROM parcoursArt p
            LEFT JOIN source s ON s.idSource = p.idSource
            LEFT JOIN typeSource tp ON tp.idTypeSource = s.idTypeSource
            WHERE p.idParcours = '".$idParcours."'";
            $resParcours         = $this->connexionBdd->requete($reqParcours);
            $fetchParcours       = mysql_fetch_assoc($resParcours);
            $libelleParcours     = $fetchParcours['libelleParcours'];
            $commentaireParcours = $fetchParcours['commentaireParcours'];
            $libelleSource       = stripslashes($fetchParcours['nomSource']." (".$fetchParcours['nomTypeSource'].")");
            $idSource            = $fetchParcours['idSource'];
            $dateAjout           = $d->toFrenchAffichage($fetchParcours['dateAjoutParcours']);
            if ($fetchParcours['isActif']==1) {
                $checkedActif = true;
            }
            $formAction = $this->creerUrl('modifierParcours', 'adminListeParcours', array());
        }
        $html        .= "<h1>$titre</h1>";
        $arrayBBCode  = $bbCode->getBoutonsMiseEnFormeTextArea(array('formName'=>'formParcours', 'fieldName'=>'commentaireParcours'));
        //coords
        $reqListeEtapes              = "SELECT idEvenementGroupeAdresse FROM etapesParcoursArt WHERE idParcours = '".$idParcours."'";
        $resListeEtapes              = $this->connexionBdd->requete($reqListeEtapes);
        $a = new archiAdresse();
        while ($fetchListeEtapes = mysql_fetch_assoc($resListeEtapes)) {
            $coords=$a->getCoordonneesFrom($fetchListeEtapes['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse');
            $points[]=$coords["latitude"].", ".$coords["longitude"];
        }
        $html.="<br/><br/>";
        include_once "includes/class.polylineEncoder.php";
        foreach ($points as $key => $point) {
            $points[$key] = explode(',', $point);
        }

        $encoder = new PolylineEncoder();
        @$polyline = $encoder->encode($points);
        $reqTrace              = "SELECT trace FROM `parcoursArt` WHERE `idParcours` = ".mysql_escape_string($idParcours);
        $resTrace              = $this->connexionBdd->requete($reqTrace);
        $trace = mysql_fetch_assoc($resTrace);
        //
        $configFields = array(
            'idParcours'=>array('libelle'=>'idParcours', 'type'=>'hidden', 'error'=>'', 'value'=>'', 'default'=>$idParcours, 'htmlCode'=>''),
            'dateAjoutParcours'=>array('libelle'=>'date ajout', 'type'=>'date', 'error'=>'', 'value'=>'', 'default'=>$dateAjout, 'htmlCode'=>'', 'withDatePicker'=>true),
            'isActif'=>array('libelle'=>'est actif', 'type'=>'singleCheckBox', 'error'=>'', 'value'=>'', 'default'=>'', 'htmlCode'=>'', 'isChecked'=>$checkedActif, 'forceValueTo'=>'1'),
            'libelleParcours'=>array('libelle'=>'libelle', 'type'=>'text', 'error'=>'', 'value'=>'', 'default'=>$libelleParcours, 'htmlCode'=>"style='width:300px;'"),
            'commentaireParcours'=>array('libelle'=>'commentaire', 'type'=>'bigText', 'error'=>'', 'value'=>'', 'default'=>$commentaireParcours, 'htmlCode'=>"style='width:400px;height:200px;'", 'htmlCodeBeforeField'=>$arrayBBCode['boutonsHTML'], 'htmlCode2'=>$arrayBBCode['divAndJsAfterForm']),
            'idSourcetxt'=>array('libelle'=>'source', 'type'=>'text', 'error'=>'', 'value'=>'', 'default'=>$libelleSource, 'htmlCode'=>'', 'htmlCode2'=>"<input type='button' name='choixSource' onclick=\"document.getElementById('calqueSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueSource').style.display='block';document.getElementById('paramChampsAppelantSource').value='idSource';\" value='Choisir'>"),
            'idSource'=>array('libelle'=>'idSource', 'type'=>'hidden', 'error'=>'', 'value'=>'', 'default'=>$idSource, 'htmlCode'=>""),
            "polyline"=>array("libelle"=>_("Liste des coordonnées")." ("._("à utiliser avec")." <a href='https://developers.google.com/maps/documentation/utilities/polylineutility'>Interactive Polyline Encoder</a>)", "type"=>"text", "default"=>$polyline->points, "htmlCode"=>"readonly onclick='this.select();'", "error"=>""),
            "levels"=>array("libelle"=>_("Niveaux"), "type"=>"text", "default"=>$polyline->levels, "htmlCode"=>"readonly onclick='this.select();'", "error"=>""),
            "trace"=>array("libelle"=>_("Coordonnées détaillées"), "type"=>"text", "default"=>$trace["trace"], "htmlCode"=>"", "error"=>"")
        );
        $configForm   = array('fields'=>$configFields, 'formAction'=>$formAction, 'formName'=>'formParcours');
        $f            = new formGenerator();
        $html        .= $f->afficherFromArray($configForm);
        $this->addToJsHeader($recherche->getPopupChoixSource('ajoutModifParcoursAdmin'));
        
        
        return $html;
    }
    
    /**
     * Ajouter un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function ajouterParcours($params = array())
    {
        if (isset($this->variablesPost['libelleParcours']) && $this->variablesPost['libelleParcours']!='' && isset($this->variablesPost['dateAjoutParcours']) && $this->variablesPost['dateAjoutParcours']!='') {
            $d = new dateObject();
            $parcoursActif = '0';
            if (isset($this->variablesPost['isActif']) && $this->variablesPost['isActif']=='1') {
                $parcoursActif = '1';
            }
            $reqInsert  = "INSERT INTO parcoursArt (libelleParcours, isActif, dateAjoutParcours,commentaireParcours, idSource ) VALUES (";
            $reqInsert .= "\"".mysql_real_escape_string($this->variablesPost['libelleParcours'])."\",";
            $reqInsert .= "'".$parcoursActif."',";
            $reqInsert .= "'".$d->toBdd($this->variablesPost['dateAjoutParcours'])."',";
            $reqInsert .= "\"".mysql_real_escape_string($this->variablesPost['commentaireParcours'])."\",";
            $reqInsert .= "'".$this->variablesPost['idSource']."'";
            $reqInsert .= ")";
            $resInsert  = $this->connexionBdd->requete($reqInsert);
        }
    }
    
    /**
     * Modifier un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function modifierParcours($params = array())
    {
        if (isset($this->variablesPost['idParcours']) && $this->variablesPost['idParcours']!='' && $this->variablesPost['idParcours']!='0' && isset($this->variablesPost['libelleParcours']) && $this->variablesPost['libelleParcours']!='' && isset($this->variablesPost['dateAjoutParcours']) && $this->variablesPost['dateAjoutParcours']!='') {
            $d = new dateObject();
            $parcoursActif = '0';
            if (isset($this->variablesPost['isActif']) && $this->variablesPost['isActif']=='1') {
                $parcoursActif = '1';
            }
            $reqUpdate = "UPDATE parcoursArt set dateAjoutParcours='".$d->toBdd($this->variablesPost['dateAjoutParcours'])."',isActif='".$parcoursActif."',libelleParcours=\"".mysql_real_escape_string($this->variablesPost['libelleParcours'])."\",commentaireParcours=\"".mysql_real_escape_string($this->variablesPost['commentaireParcours'])."\",idSource='".$this->variablesPost['idSource']."', trace='".mysql_escape_string($this->variablesPost['trace'])."'
            WHERE idParcours='".$this->variablesPost['idParcours']."'";
            $resUpdate = $this->connexionBdd->requete($reqUpdate);
        }
    }
    
    /**
     * Afficher la gestion des étapes d'un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getHtmlGestionEtapesParcours($params = array())
    {
        $html = "<h1>Gestion des étapes du parcours</h1>";
        $idParcours = 0;
        if (isset($this->variablesGet['archiIdParcours']) && $this->variablesGet['archiIdParcours']!='') {
            $f                           = new formGenerator();
            $a                           = new archiAdresse();
            $bbCode                      = new bbCodeObject();
            $s                           = new stringObject();
            $formName                    = 'formGestionEtapes';
            $popupChoixAdresses          = new calqueObject(array('idPopup'=>'popupChoixAdresses'));
            $popupVisualisationGoogleMap = new calqueObject(array('idPopup'=>'popupVisualisationGoogleMap'));
            $idParcours                  = $this->variablesGet['archiIdParcours'];
            $reqNbEtapes                 = "SELECT idEtape FROM etapesParcoursArt WHERE idParcours = '".$idParcours."'";
            $resNbEtapes                 = $this->connexionBdd->requete($reqNbEtapes);
            $html                       .= "<br>nombre d'étapes : ".mysql_num_rows($resNbEtapes)."<br>";
            $reqListeEtapes              = "SELECT idEtape,idEvenementGroupeAdresse,position,commentaireEtape FROM etapesParcoursArt WHERE idParcours = '".$idParcours."' ORDER BY position DESC,idEtape ASC";
            $resListeEtapes              = $this->connexionBdd->requete($reqListeEtapes);
            $listeTriableObject          = new imageObject();

            // dans l'objet image , il y a une fonction qui permet de creer des listes triables par drag and drop
            $html            .= "<script>".$listeTriableObject->getJSFunctionsDragAndDrop()."</script>";
            $i                = 0;
            $arrayListeEtapes = array();
            while ($fetchListeEtapes = mysql_fetch_assoc($resListeEtapes)) {
                $arrayListeEtapes[$i]['idEtape']     = array('value'=>$fetchListeEtapes['idEtape'], 'type'=>'identifiant');
                $arrayListeEtapes[$i]['#']     = array('value'=>$i+1, 'type'=>'free');
                $arrayListeEtapes[$i]['&nbsp;']      = array('value'=>"<a href='".$this->creerUrl('', 'etapesParcoursFormulaire', array('archiIdEtape'=>$fetchListeEtapes['idEtape'], 'archiIdParcours'=>$idParcours))."'>".$fetchListeEtapes['idEtape']."</a>", 'type'=>'free', 'widthColonne'=>50);
                $arrayListeEtapes[$i]['adresse']     = array('value'=>$a->getIntituleAdresseFrom($fetchListeEtapes['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse', array('setSeparatorAfterTitle'=>'<br>', 'displayFirstTitreAdresse'=>true, 'noVille'=>true, 'noQuartier'=>true, 'noSousQuartier'=>true)), 'type'=>'free', 'widthColonne'=>250);
                $arrayListeEtapes[$i]['commentaire'] = array('type'=>'free', 'widthColonne'=>400, 'value'=>$s->coupureTexte($s->sansBalisesHtml(stripslashes($fetchListeEtapes['commentaireEtape'])), 10));
                $coords=$a->getCoordonneesFrom($fetchListeEtapes['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse');
                $i++;
            }
            $boutonValidationOrdreEtapes = "<input type='button' name='validationOrdreAffichage' value='Validation ordre des étapes' onclick=\"document.getElementById('".$formName."').action='".$this->creerUrl('enregistrerOrdreEtapesParcours', 'etapesParcoursFormulaire', array('archiIdParcours'=>$idParcours))."';".$listeTriableObject->getJSSubmitDragAndDrop()."document.getElementById('".$formName."').submit();\">";
            $html                       .= $listeTriableObject->createSortableFormListeFromArray($arrayListeEtapes, array('styleEntete'=>'font-weight:bold;background-color:#007799;color:#FFFFFF;')).$boutonValidationOrdreEtapes;
            $sousTitre                   = "Ajout d'une étape";
            $idEtape                     = 0;
            $libelleAdresse              = '';
            $commentaireEtape            = '';
            $idAdresse                   = '';
            $idEvenementGroupeAdresse    = 0;
            $boutonNouveau               = "";
            $boutonVisualisation         = "<input type='button' name='visualisation' value='Visualiser' onclick=\"".$popupVisualisationGoogleMap->getJSOpenPopup()."document.getElementById('".$popupVisualisationGoogleMap->getJSIFrameId()."').src='".$this->creerUrl('', 'carteGoogleMapParcoursArt', array('noHeaderNoFooter'=>1, 'archiIdParcours'=>$idParcours))."'\">";
            $boutonRetour                = "<input type='button' name='retour' value='Retour' onclick=\"location.href='".$this->creerUrl('', 'adminListeParcours', array())."';\">";
            $boutonSupprimer             = "";
            
            // ajout d'une nouvelle etape
            $formAction = $this->creerUrl('ajouterEtapeParcours', 'etapesParcoursFormulaire', array('archiIdParcours'=>$idParcours));
            
            if (mysql_num_rows($resNbEtapes)==0) {
                // si pas d'etapes encore , on peut definir un message
            } else {
                // il y a une ou plusieurs etapes dans le parcours
                if (isset($this->variablesGet['archiIdEtape']) && $this->variablesGet['archiIdEtape']!='') {
                    $idEtape                  = $this->variablesGet['archiIdEtape'];
                    $reqEtape                 = "SELECT idEvenementGroupeAdresse,commentaireEtape FROM etapesParcoursArt WHERE idEtape='".$idEtape."'";
                    $resEtape                 = $this->connexionBdd->requete($reqEtape);
                    $fetchEtape               = mysql_fetch_assoc($resEtape);
                    $sousTitre                = _("Modification d'une étape");
                    $formAction               = $this->creerUrl('modifierEtapeParcours', 'etapesParcoursFormulaire', array('archiIdParcours'=>$idParcours, 'archiIdEtape'=>$this->variablesGet['archiIdEtape']));
                    $commentaireEtape         = $fetchEtape['commentaireEtape'];
                    $libelleAdresse           = $a->getIntituleAdresseFrom($fetchEtape['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse');
                    $idAdresse                = '';
                    $idEvenementGroupeAdresse = $fetchEtape['idEvenementGroupeAdresse'];
                    $boutonNouveau            = "<input type='button' name='boutonNouveau' value='Nouveau' onclick=\"location.href='".$this->creerUrl('', 'etapesParcoursFormulaire', array('archiIdParcours'=>$idParcours))."';\">";
                    $boutonSupprimer          = "<input type='button' name='boutonSupprimer' value='Supprimer' onclick=\"location.href='".$this->creerUrl('supprimerEtapeParcours', 'etapesParcoursFormulaire', array('archiIdParcours'=>$idParcours, 'archiIdEtapeSupprimer'=>$idEtape))."';\">";
                    $coord=$a->getCoordonneesFrom($fetchEtape['idEvenementGroupeAdresse'], 'idEvenementGroupeAdresse');
                } else {
                    $coord=array("latitude"=>"", "longitude"=>"");
                }
            }
            $bbCodeElementsArray = $bbCode->getBoutonsMiseEnFormeTextArea(array('formName'=>$formName, 'fieldName'=>'commentaireEtape'));
            $html.="<hr/>";

            // formulaire ajout-modif etape
            $html        .= "<h2>$sousTitre</h2>";
            $configFields = array('idParcours'=>array('libelle'=>'idParcours', 'type'=>'hidden', 'error'=>'', 'value'=>'', 'forceValueTo'=>$idParcours, 'htmlCode'=>''),
            'idEtape'=>array('libelle'=>'idEtape', 'type'=>'hidden', 'error'=>'', 'value'=>'', 'forceValueTo'=>$idEtape, 'htmlCode'=>''),
            'libelleEvenementGroupeAdresse'=>array('libelle'=>_('Adresse'), 'type'=>'text', 'error'=>'', 'value'=>'', 'forceValueTo'=>$libelleAdresse, 'htmlCode'=>"style='width:300px;' disabled", 'htmlCode2'=>"<input type='button' name='choixAdresse' value='Choisir' onclick=\"document.getElementById('".$popupChoixAdresses->getJSDivId()."').style.top=(getScrollHeight()+70)+'px';".$popupChoixAdresses->getJSOpenPopup()."document.getElementById('".$popupChoixAdresses->getJSIFrameId()."').src='".$this->creerUrl('', 'recherche', array('noHeaderNoFooter'=>1, 'modeAffichage'=>'popupRechercheAdresseAdminParcours'))."';\">"),
            'latitude'=>array('libelle'=>_('Latitude'), 'type'=>'text', 'error'=>'', 'value'=>'', 'forceValueTo'=>$coord["latitude"], 'htmlCode'=>"style='width:300px;' readonly onclick='this.select();'"),
            'longitude'=>array('libelle'=>_('Longitude'), 'type'=>'text', 'error'=>'', 'value'=>'', 'forceValueTo'=>$coord["longitude"], 'htmlCode'=>"style='width:300px;' readonly onclick='this.select();'"),
            'idEvenementGroupeAdresse'=>array('libelle'=>'idEvenementGroupeAdresse', 'type'=>'hidden', 'error'=>'', 'value'=>'', 'forceValueTo'=>$idEvenementGroupeAdresse, 'htmlCode'=>"style='width:300px;'"),
            'commentaireEtape'=>array('libelle'=>'commentaire', 'type'=>'bigText', 'error'=>'', 'value'=>'', 'forceValueTo'=>$commentaireEtape, 'default'=>'', 'htmlCode'=>"style='width:500px;height:200px;'", 'htmlCodeBeforeField'=>$bbCodeElementsArray['boutonsHTML'], 'htmlCode2'=>$bbCodeElementsArray['divAndJsAfterForm']));
            $configForm   = array('fields'=>$configFields, 'formAction'=>$formAction, 'formName'=>$formName, 'onClickSubmitButton'=>$listeTriableObject->getJSSubmitDragAndDrop(), 'codeHtmlAfterSubmitButton'=>$boutonSupprimer.$boutonNouveau.$boutonVisualisation.$boutonRetour, 'codeHtmlInFormAfterFields'=>$listeTriableObject->getJSInitAfterListDragAndDrop());
            $html        .= $f->afficherFromArray($configForm);
            $html        .= $popupChoixAdresses->getDiv(array('lienSrcIFrame'=>$this->creerUrl('', 'recherche', array('noHeaderNoFooter'=>1, 'modeAffichage'=>'popupRechercheAdresseAdminParcours')), 'width'=>750, 'height'=>500, 'left'=>10, 'top'=>70, 'titre'=>'archi-strasbourg.org : Parcours'));
            $html        .= $popupVisualisationGoogleMap->getDiv(array('lienSrcIFrame'=>''));
            // on ne precharge pas la carte google map sinon il y a un bug au niveau de la carte , le centre se placerai en haut a gauche
            $html .= "<script  >".$popupChoixAdresses->getJsToDragADiv()."</script>";

            // on rend le div deplacable
            $html .= "<script  >".$popupVisualisationGoogleMap->getJsToDragADiv()."</script>";

            // on rend le div deplacable
        }
        return $html;
    }
    
    /**
     * Ajouter une étape à un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function ajouterEtapeParcours($params = array())
    {
        if (isset($this->variablesPost['idEvenementGroupeAdresse']) && $this->variablesPost['idEvenementGroupeAdresse']!='') {
            // recuperation de la position courante
            $reqPos    = "SELECT idEtape FROM etapesParcoursArt WHERE idParcours = '".$this->variablesPost['idParcours']."'";
            $resPos    = $this->connexionBdd->requete($reqPos);
            $nbEtapes  = mysql_num_rows($resPos);
            $position  = 0;
            $reqInsert = "INSERT INTO etapesParcoursArt (idParcours,idEvenementGroupeAdresse,position,commentaireEtape) VALUES ('".$this->variablesPost['idParcours']."','".$this->variablesPost['idEvenementGroupeAdresse']."','".$position."',\"".mysql_real_escape_string($this->variablesPost['commentaireEtape'])."\")";
            $resInsert = $this->connexionBdd->requete($reqInsert);
            $this->enregistrerOrdresEtapes();
        }
    }
    
    /**
     * Modifier une étape d'un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function modifierEtapeParcours($params = array())
    {
        if (isset($this->variablesPost['idEvenementGroupeAdresse']) && $this->variablesPost['idEvenementGroupeAdresse']!='' && isset($this->variablesPost['idEtape']) && $this->variablesPost['idEtape']!='') {
            $reqUpdate = "update etapesParcoursArt set commentaireEtape=\"".mysql_real_escape_string($this->variablesPost['commentaireEtape'])."\",idEvenementGroupeAdresse='".$this->variablesPost['idEvenementGroupeAdresse']."' WHERE idEtape='".$this->variablesPost['idEtape']."'";
            $resUpdate = $this->connexionBdd->requete($reqUpdate);
            $this->enregistrerOrdresEtapes();
        }
    }
    
    /**
     * Enregistrer l'ordre des étapes d'un parcours
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function enregistrerOrdresEtapes($params = array())
    {
        $imObj      = new imageObject();
        $ordreArray = $imObj->getArrayFromPostDragAndDrop();
        $i          = count($ordreArray);
        foreach ($ordreArray as $indice=>$idEtape) {

            // l'indice commence a 1 
            $reqUpdate = "UPDATE etapesParcoursArt SET position='".$i."' WHERE idEtape='".$idEtape."'";
            $resUpdate = $this->connexionBdd->requete($reqUpdate);
            $i--;
        }
    }
    
    /**
     * Supprimer une étape d'un parcous
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function supprimerEtapeParcours($params = array())
    {
        $auth = new archiAuthentification();
        if ($auth->estConnecte()) {
            if (isset($this->variablesGet['archiIdEtapeSupprimer']) && $this->variablesGet['archiIdEtapeSupprimer']!='') {
                $req = "DELETE FROM etapesParcoursArt WHERE idEtape='".$this->variablesGet['archiIdEtapeSupprimer']."'";
                $res = $this->connexionBdd->requete($req);
            }
        } else {
            echo "Vous n'êtes pas connecté.";
        }
    }
    
    /**
     * Liste des actualités
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function listeActualites($params = array())
    {
        $html                     = "";
        $t                        = new tableau();
        $d                        = new dateObject();
        $pagination               = new paginationObject();
        $nbEnregistrementsParPage = 5;
        $reqCount                 = "SELECT 0 FROM actualites";
        $resCount                 = $this->connexionBdd->requete($reqCount);
        $arrayPagination          = $pagination->pagination(array('nomParamPageCourante'=>'page', 'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 'nbEnregistrementsTotaux'=>mysql_num_rows($resCount), 'typeLiens'=>'noformulaire'));
        $req                      = "SELECT idActualite, titre,sousTitre,date,photoIllustration,texte,urlFichier,desactive FROM actualites ORDER BY date DESC";
        $req                      = $pagination->addLimitToQuery($req);
        $res                      = $this->connexionBdd->requete($req);
        $t->addValue("Date");
        $t->addValue("Statut");
        $t->addValue("Titre");
        while ($fetch = mysql_fetch_assoc($res)) {
            $t->addValue("<a href='".$this->creerUrl('', 'adminActualites', array('archiIdActualite'=>$fetch['idActualite']))."'>".$d->toFrenchAffichage($fetch['date'])."</a>");
            if ($fetch['desactive']==1) {
                $t->addValue("desactivée");
            } else {
                $t->addValue("active");
            }
            $t->addValue($fetch['titre']);
        }
        $html .= $arrayPagination['html'];
        $html .= $t->createHtmlTableFromArray(3);
        return $html;
    }
    
    /**
     * Popup de prévisualisation d'une actualité
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function previsualisationActualitePopup($params = array())
    {
        $html  = "";
        $html .= "<script  >";
        $html .= "titreBalisesAvant=\"<div align=center><h2>\";";
        $html .= "titreBalisesApres=\"</h2></div>\";";
        $html .= "sousTitreBalisesAvant=\"<div align=center><h3>\";";
        $html .= "sousTitreBalisesApres=\"</h3></div>\";";
        $html .= "document.write(titreBalisesAvant+parent.document.getElementById('titre').value+titreBalisesApres+'<br>');";
        $html .= "document.write(sousTitreBalisesAvant+parent.document.getElementById('sousTitre').value+sousTitreBalisesApres+'<br>');";
        $html .= "document.write(parent.document.getElementById('texte').value);";
        $html .= "</script>";
        return $html;
    }
    
    /**
     * Affichage du formulaire d'ajout/édition
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function formulaireActualites($params = array())
    {
        $html = "";
        $f    = new formGenerator();
        $d    = new dateObject();
        $c    = new calqueObject();
        $this->addToJsHeader("<script>".$c->getJSScrollHeight()."</script>");
        $html                                    .= $c->getDiv(array('width'=>900, 'lienSrcIFrame'=>'', 'titre'=>'Prévisualisation'));
        $idActualite                              = 0;
        $boutonNouveau                            = "";
        $boutonSupprimer                          = "";
        $boutonPrevisualiser                      = "<input type='button' name='previsualisation' value='Prévisualisation' onclick=\"tinyMCE.triggerSave(true,true);document.getElementById('".$c->getJSDivId()."').style.top=(50+getScrollHeight())+'px';document.getElementById('".$c->getJSIFrameId()."').src='".$this->creerUrl('', 'previsualisationActualite', array())."';".$c->getJSOpenPopup()."\">";
        $boutonValiderEtEnvoiMailPrevisualisation = "<input type='button' name='valideEtEnvoi' id='valideEtEnvoi' value=\"Envoyer le mail tout de suite\" onclick=\"document.getElementById('formActu').action+='&previsualisationMail=1';document.getElementById('formActu').submit();\">
        <input type='button' name='valideEtPrev' id='valideEtPrev' value=\"Prévisualiser le mail\" onclick=\"document.getElementById('formActu').action+='&previsualisationMail=1&preview=1';document.getElementById('formActu').setAttribute('target', '_blank'); document.getElementById('formActu').submit();\">";
        if (isset($this->variablesGet['archiIdActualite']) && $this->variablesGet['archiIdActualite']!='') {
            $idActualite           = $this->variablesGet['archiIdActualite'];
            $reqActu               = "SELECT date,titre, sousTitre,photoIllustration, texte,urlFichier, fichierPdf, desactive,texteMailHebdomadaire,envoiMailHebdomadaire FROM actualites WHERE idActualite='".$idActualite."'";
            $resActu               = $this->connexionBdd->requete($reqActu);
            $fetchActu             = mysql_fetch_assoc($resActu);
            $date                  = $d->toFrenchAffichage($fetchActu['date']);
            $titre                 = $fetchActu['titre'];
            $sousTitre             = $fetchActu['sousTitre'];
            $illustration          = $fetchActu['photoIllustration'];
            $description           = $fetchActu['texte'];
            $urlFichier            = $fetchActu['urlFichier'];
            $fichierPdf            = $fetchActu['fichierPdf'];
            $isDesactivated        = $fetchActu['desactive'];
            $texteMailHebdomadaire = $fetchActu['texteMailHebdomadaire'];
            $isEnvoi               = $fetchActu['envoiMailHebdomadaire'];
            $formAction            = $this->creerUrl('modifierActu', 'adminActualites', array('archiIdActualite'=>$idActualite));
            $boutonNouveau         = "<input type='button' name='nouveauButton' value='Nouveau' onclick=\"location.href='".$this->creerUrl('', 'adminActualites', array())."'\">";
            $boutonSupprimer       = "<input type='button' name='supprimerBouton' value='Supprimer' onclick=\"location.href='".$this->creerUrl('supprimerActu', 'adminActualites', array("idActuSuppr"=>$idActualite))."'\">";
        } else {
            $date                  = date("d/m/Y");
            $titre                 = "";
            $sousTitre             = "";
            $illustration          = "";
            $description           = "";
            $urlFichier            = "";
            $fichierPdf            = "";
            $isDesactivated        = 0;
            $texteMailHebdomadaire = "";
            $isEnvoi               = 0;
            $formAction            = $this->creerUrl('ajouterActu', 'adminActualites', array('check'=>1));

            // check sert juste a ne pas avoir a gerer le ? ou le & dans l'url quand on rajoute un parametre en js
        }
        $idActualitePathImage = "";
        if ($idActualite!=0) {
            $idActualitePathImage = $idActualite;
        }

        // bibliotheques d'images
        $fileObject    = new fileObject();
        $arrayFiles    = $fileObject->getListeFichiersArrayFrom($this->getCheminPhysique()."images/actualites/".$idActualite."/");
        $listeFichiers = "";
        $t             = new tableau();
        foreach ($arrayFiles as $indiceFichier=>$fichier) {
            if (strtolower($fileObject->getExtensionFromFile($fichier))=='jpg' || strtolower($fileObject->getExtensionFromFile($fichier))=='gif' || strtolower($fileObject->getExtensionFromFile($fichier))=='png') {
                $t->addValue("<a onclick='injectInTinyMce(\"<img src=\\\"".$this->getUrlImage()."actualites/".$idActualite."/".str_replace("'", "\\\'", $fichier)."\\\" border=0>\");'><img src=\"".$this->getUrlImage()."actualites/".$idActualite."/".$fichier."\" border=0 width=100 height=100></a>");
                $t->addValue("$fichier");
            } else {
                $t->addValue("&nbsp;");
                $t->addValue("$fichier");
            }
        }
        $listeFichiers = $t->createTable(2);
        $gestionBibliothequeImages = "Bibliothèque d'images de l'actualité <span style='font-size:11px;'>(cliquez sur une image pour l'inserer à la position du curseur)</span> : <div id='listeFichiers' style='background-color:#87CEFF;width:300px;height:200px;overflow:scroll;'>".$listeFichiers."</div>";
        $this->addToJsHeader(
            "<script>
                function injectInTinyMce(txt)
                {
                    tinyMCE.execInstanceCommand('texte','mceInsertContent',false,txt);
                }
                
                function goRedim(idActualite)
                {
                    // validation du formulaire pour creer l'identifiant de l'actu et pouvoir ranger les photos
                    document.getElementById('formActu').submit();
                }
            </script>"
        );

        // on ne gere pas de timestamp , on assume que seul l'admin fera des mises a jour des actualités
        $applet       = $fileObject->getAppletUploadMultiple(array('cheminApplet'=>$this->getUrlRacine()."/includes/", 'uploadDirPart1'=>$this->getCheminPhysique()."images/", 'uploadDirPart2'=>"uploadMultipleActualites/", 'jsFunctionNameOnExit'=>"goRedim($idActualite)"));
        $configFields = array('idActualite'=>array('libelle'=>"idActualite", 'type'=>'hidden', 'required'=>true, 'value'=>'', 'forceValueTo'=>$idActualite, 'htmlCode'=>'', 'error'=>''), 'desactive'=>array('libelle'=>"désactiver", 'type'=>'singleCheckBox', 'required'=>false, 'value'=>'', 'forceValueTo'=>'1', 'isChecked'=>$isDesactivated, 'htmlCode'=>'', 'error'=>'', 'default'=>''), 'date'=>array('libelle'=>"date", 'withDatePicker'=>true, 'type'=>'date', 'required'=>true, 'value'=>'', 'forceValueTo'=>$date, 'htmlCode'=>'', 'error'=>''), 'titre'=>array('libelle'=>"titre", 'type'=>'text', 'required'=>false, 'value'=>'', 'forceValueTo'=>$titre, 'htmlCode'=>"style='width:300px;'", 'error'=>''), 'sousTitre'=>array('libelle'=>"sous-titre", 'type'=>'text', 'required'=>false, 'value'=>'', 'forceValueTo'=>$sousTitre, 'htmlCode'=>"style='width:300px;'", 'error'=>''), 'photoIllustration'=>array('libelle'=>"illustration", 'type'=>'uploadImage', 'required'=>false, 'value'=>'', 'forceValueTo'=>$illustration, 'physicalImagePathForTestExists'=>$this->getCheminPhysique()."images/actualites/".$idActualitePathImage."/", 'urlImagePathForDisplayInForm'=>$this->getUrlImage()."actualites/".$idActualitePathImage."/", 'htmlCode'=>'', 'error'=>''), 'texte'=>array('libelle'=>"description", 'type'=>'tinyMCE', 'required'=>false, 'value'=>'', 'forceValueTo'=>$description, 'htmlCode'=>"cols=80 rows=30", 'error'=>'', 'htmlCode2'=>$gestionBibliothequeImages."<div><a onclick=\"if(document.getElementById('divApplet').style.display=='none'){document.getElementById('divApplet').style.display='block';}else{document.getElementById('divApplet').style.display='none';}\" style='cursor:pointer;'>Voir/cacher ajouter une image</a></div><div id='divApplet' style='float:left;display:none'>".$applet."</div>"), 'urlFichier'=>array('libelle'=>"url redirection", 'type'=>'text', 'required'=>false, 'value'=>'', 'forceValueTo'=>$urlFichier, 'htmlCode'=>"style='width:300px;'", 'error'=>''), 'fichierPdf'=>array('libelle'=>"fichier pdf", 'type'=>'text', 'required'=>false, 'value'=>'', 'forceValueTo'=>$fichierPdf, 'htmlCode'=>"style='width:300px;'", 'error'=>''), 'texteMailHebdomadaire'=>array('libelle'=>"description mail hebdomadaire <span style='color:red;'>(ne pas oublier de préciser le titre)</span>", 'type'=>'tinyMCE', 'required'=>false, 'value'=>'', 'forceValueTo'=>$texteMailHebdomadaire, 'htmlCode'=>"cols=80 rows=30", 'error'=>''), 'envoiMailHebdomadaire'=>array('libelle'=>"envoi avec la newsletter hebdomadaire", 'type'=>'singleCheckBox', 'required'=>false, 'value'=>'', 'forceValueTo'=>'1', 'isChecked'=>$isEnvoi, 'htmlCode'=>'', 'error'=>'', 'default'=>''));
        $configForm   = array("fields"=>$configFields, 'formAction'=>$formAction, 'codeHtmlAfterSubmitButton'=>$boutonNouveau.$boutonSupprimer.$boutonPrevisualiser.$boutonValiderEtEnvoiMailPrevisualisation, 'formName'=>'formActu');
        $html        .= $f->afficherFromArray($configForm);
        return $html;
    }
    
    /**
     * Ajouter une actualité
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function ajouterActualite($params = array())
    {
        if (isset($this->variablesPost['idActualite']) && $this->variablesPost['idActualite']!='') {
            $d = new dateObject();
            $desactive = 0;
            if (isset($this->variablesPost['desactive']) && $this->variablesPost['desactive']=='1') {
                $desactive = 1;
            }
            $envoi = 0;
            if (isset($this->variablesPost['envoiMailHebdomadaire']) && $this->variablesPost['envoiMailHebdomadaire']=='1') {
                $envoi = 1;
            }
            $req = "INSERT INTO actualites (titre,sousTitre,`date`,texte,urlFichier,fichierPdf,desactive,texteMailHebdomadaire,envoiMailHebdomadaire) VALUES (
            \"".mysql_real_escape_string($this->variablesPost['titre'])."\",
            \"".mysql_real_escape_string($this->variablesPost['sousTitre'])."\",
            \"".$d->toBdd(mysql_real_escape_string($this->variablesPost['date']))."\",
            \"".mysql_real_escape_string($this->variablesPost['texte'])."\",
            \"".mysql_real_escape_string($this->variablesPost['urlFichier'])."\",
            \"".mysql_real_escape_string($this->variablesPost['fichierPdf'])."\",
            '".$desactive."',
            \"".mysql_real_escape_string($this->variablesPost['texteMailHebdomadaire'])."\",
            '".$envoi."'
            ) ";
            $res = $this->connexionBdd->requete($req);

            //\"".mysql_real_escape_string($this->variablesPost['photoIllustration'])."\",
            $idNewActualite = mysql_insert_id();
            if (!file_exists($this->getCheminPhysique()."images/actualites/$idNewActualite/")) {
                mkdir($this->getCheminPhysique()."images/actualites/$idNewActualite/");
            }
            $f = new fileObject();

            // gestion de l'upload
            if (isset($_FILES['photoIllustration']) && !$_FILES['photoIllustration']['error']) {
                $f->handleUploadedFileSimpleMoveTo(array('inputFileName'=>'photoIllustration', 'redimensionneImageConfig'=>array(200=>array('destination'=>$this->getCheminPhysique()."images/actualites/".$idNewActualite."/illustration200.jpg"))));

                // on met a jour la bdd , meme si dans les prochaines actu cela ne sert a rien de garder le champs , vu le fonctionnement, on le garde pour les anciennes actus
                $reqIllustration = "UPDATE actualites SET photoIllustration='illustration200.jpg' WHERE idActualite='".$idNewActualite."' ";
                $resIllustration = $this->connexionBdd->requete($reqIllustration);
            }

            // on verifie dans le repertoire d'uploadMultiple pour voir s'il y a des images a transferer
            if (file_exists($this->getCheminPhysique()."images/uploadMultipleActualites/")) {
                $f->convertDirectoryFilesNamesToUTF8(array('repertoire'=>$this->getCheminPhysique()."images/uploadMultipleActualites/"));
                $arrayFiles = $f->getListeFichiersArrayFrom($this->getCheminPhysique()."images/uploadMultipleActualites/");
                foreach ($arrayFiles as $indice=>$fichier) {
                    // on deplace tous les fichiers dans le repertoire de l'actu , ceux qui auront le meme nom seront ecrasés.
                    if ($fichier!='.' && $fichier!='..') {
                        rename($this->getCheminPhysique()."images/uploadMultipleActualites/".$fichier, $this->getCheminPhysique()."images/actualites/$idNewActualite/".$f->removeSpecialCharFromFileName($fichier));
                    }
                }
                $f->convertDirectoryFilesNamesToUTF8(array('repertoire'=>$this->getCheminPhysique()."images/actualites/$idNewActualite/"));
            }

            // creation automatique du fichier pdf
            if ($this->variablesPost['urlFichier']=='') {

                // evite de creer un fichier pdf pour rien
                $pdfObject = new pdfObject();
                $pdfObject->setContent(stripslashes($this->variablesPost['titre'].$this->variablesPost['sousTitre'].$this->variablesPost['texte']));
                $pdfObject->writeToFile($this->getCheminPhysique()."images/actualites/$idNewActualite/versionPdf.pdf");
                if (file_exists($this->getCheminPhysique()."images/actualites/$idNewActualite/versionPdf.pdf")) {

                    // pas genial , mais c'est pour garder la compatibilié avec les news precedentes qui n'etait pas generee automatiquement en pdf sur lesquelles on precisait le nom du fichier que l'on uploadait a la main 
                    $reqUpdatePdf = "UPDATE actualites SET fichierPdf='versionPdf.pdf' WHERE idActualite = '$idNewActualite'";
                }
                $resUpdatePdf = $this->connexionBdd->requete($reqUpdatePdf);
            }
        }
        if (isset($this->variablesGet['previsualisationMail']) && $this->variablesGet['previsualisationMail']=='1') {
            $auth=new archiAuthentification();
            $preview=isset($_GET["preview"])?"&preview=1":"";
            echo "<script>location.href='".$this->getUrlRacine()."/script/cronMailsNouvellesAdresses.php?modePrevisualisationAdmin=1&idActualite=$idNewActualite&idUtilisateur=".$auth->getIdUtilisateur().$preview."';</script>";

            //$this->creerUrl('','', array("modePrevisualisationAdmin"=>1,"idActualite"=>$idActualite,"idUtilisateur"=>$authentification->getIdUtilisateur()))
        }
    }
    
    /**
     * Modifier une actualité
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function modifierActualite($params = array())
    {
        if (isset($this->variablesPost['idActualite']) && $this->variablesPost['idActualite']!='') {
            $authentification = new archiAuthentification();
            $idActualite      = $this->variablesPost['idActualite'];
            $d                = new dateObject();
            $desactive        = 0;
            if (isset($this->variablesPost['desactive']) && $this->variablesPost['desactive']=='1') {
                $desactive = 1;
            }
            $envoi = 0;
            if (isset($this->variablesPost['envoiMailHebdomadaire']) && $this->variablesPost['envoiMailHebdomadaire']=='1') {
                $envoi = 1;
            }
            $req = "UPDATE actualites SET 
                titre = \"".mysql_real_escape_string($this->variablesPost['titre'])."\",
                sousTitre = \"".mysql_real_escape_string($this->variablesPost['sousTitre'])."\",
                `date` = \"".$d->toBdd(mysql_real_escape_string($this->variablesPost['date']))."\",
                texte = \"".mysql_real_escape_string($this->variablesPost['texte'])."\",
                urlFichier = \"".mysql_real_escape_string($this->variablesPost['urlFichier'])."\",
                fichierPdf = \"".mysql_real_escape_string($this->variablesPost['fichierPdf'])."\",
                desactive = '".$desactive."',
                texteMailHebdomadaire = \"".mysql_real_escape_string($this->variablesPost['texteMailHebdomadaire'])."\",
                envoiMailHebdomadaire = '".$envoi."'
                WHERE idActualite = '".$idActualite."'
            ";
            $res = $this->connexionBdd->requete($req);

            //photoIllustration = \"".mysql_real_escape_string($this->variablesPost['photoIllustration'])."\",
            $f = new fileObject();
            if (!file_exists($this->getCheminPhysique()."images/actualites/".$idActualite."/")) {
                mkdir($this->getCheminPhysique()."images/actualites/".$idActualite."/");
            }

            // gestion de l'upload
            if (isset($_FILES['photoIllustration']) && !$_FILES['photoIllustration']['error']) {
                if (file_exists($this->getCheminPhysique()."images/actualites/".$idActualite."/illustration200.jpg")) {
                    unlink($this->getCheminPhysique()."images/actualites/".$idActualite."/illustration200.jpg");
                }
                $f->handleUploadedFileSimpleMoveTo(array('inputFileName'=>'photoIllustration', 'redimensionneImageConfig'=>array(200=>array('destination'=>$this->getCheminPhysique()."images/actualites/".$idActualite."/illustration200.jpg"))));

                // on met a jour la bdd , meme si dans les prochaines actu cela ne sert a rien de garder le champs , vu le fonctionnement, on le garde pour les anciennes actus
                $reqIllustration = "UPDATE actualites SET photoIllustration='illustration200.jpg' WHERE idActualite='".$idActualite."' ";
                $resIllustration = $this->connexionBdd->requete($reqIllustration);
            }

            // on verifie dans le repertoire d'uploadMultiple pour voir s'il y a des images a transferer
            if (file_exists($this->getCheminPhysique()."images/uploadMultipleActualites/")) {
                $f->convertDirectoryFilesNamesToUTF8(array('repertoire'=>$this->getCheminPhysique()."images/uploadMultipleActualites/"));
                $arrayFiles = $f->getListeFichiersArrayFrom($this->getCheminPhysique()."images/uploadMultipleActualites/");
                foreach ($arrayFiles as $indice=>$fichier) {
                    // on deplace tous les fichiers dans le repertoire de l'actu , ceux qui auront le meme nom seront ecrasés.
                    if ($fichier!='.' && $fichier!='..') {
                        rename($this->getCheminPhysique()."images/uploadMultipleActualites/".$fichier, $this->getCheminPhysique()."images/actualites/".$idActualite."/".$f->removeSpecialCharFromFileName($fichier));
                    }
                }
                $f->convertDirectoryFilesNamesToUTF8(array('repertoire'=>$this->getCheminPhysique()."images/actualites/$idActualite/"));
            }
            if ($this->variablesPost['urlFichier']=='') {

                // evite de creer un fichier pdf pour rien, si urlFichier est renseigné, c'est que c'est une redirection vers une page , pas du texte saisi dans la news
                    // creation automatique du fichier pdf
                $pdfObject = new pdfObject();
                $titre     = "<div align=center><h2>".stripslashes($this->variablesPost['titre'])."</h2></div><br>";
                $sousTitre = "<div align=center><h3>".stripslashes($this->variablesPost['sousTitre'])."</h3></div><br>";
                $texte     = stripslashes($this->variablesPost['texte']);
                $pdfObject->setContent(stripslashes($titre.$sousTitre.$texte));
                $pdfObject->writeToFile($this->getCheminPhysique()."images/actualites/$idActualite/versionPdf.pdf");
                if (file_exists($this->getCheminPhysique()."images/actualites/$idActualite/versionPdf.pdf")) {
                    $reqUpdatePdf = "UPDATE actualites SET fichierPdf='versionPdf.pdf' WHERE idActualite = '$idActualite'";
                    $resUpdatePdf = $this->connexionBdd->requete($reqUpdatePdf);
                }
            }
            if (isset($this->variablesGet['previsualisationMail']) && $this->variablesGet['previsualisationMail']=='1') {
                $preview=isset($_GET["preview"])?"&preview=1":"";
                echo "<script>location.href='".$this->getUrlRacine()."/script/cronMailsNouvellesAdresses.php?modePrevisualisationAdmin=1&idActualite=$idActualite&idUtilisateur=".$authentification->getIdUtilisateur().$preview."';</script>";

                //$this->creerUrl('','', array("modePrevisualisationAdmin"=>1,"idActualite"=>$idActualite,"idUtilisateur"=>$authentification->getIdUtilisateur()))
            }
        }
    }
    
    /**
     * Supprimer une actualité
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    public function supprimerActualite($params = array())
    {
        if (isset($this->variablesGet['idActuSuppr']) && $this->variablesGet['idActuSuppr']!='') {
            $req = "DELETE FROM actualites WHERE idActualite = '".$this->variablesGet['idActuSuppr']."'";
            $res = $this->connexionBdd->requete($req);

            // fichiers :
            if (file_exists($this->getCheminPhysique()."images/actualites/".$this->variablesGet['idActuSuppr']."/")) {
                exec("rm -rf ".$this->getCheminPhysique()."images/actualites/".$this->variablesGet['idActuSuppr']."/* ");
            }
        }
    }
}
?>
