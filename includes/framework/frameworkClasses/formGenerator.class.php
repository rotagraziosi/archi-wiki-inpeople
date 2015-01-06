<?php
// generateur de formulaire
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - ajout de la fonction getArrayFromPost

class formGenerator extends config
{
    var $fields;
    private static $instancesObject=0; // identifiant d'instance de l'objet ,  on incremente des qu'un nouvel objet formGenerator est creer ,  on obtient donc un identifiant unique que l'on recupere en meme temps
    private $identifiantObject = 0;
    private static $appelInitTinyMCE = 0;
    function __construct($connexion='')
    {
        parent::__construct();
        $this->fields=array();
        $this->elementsFormulaireMultiPage=array();
        $this->identifiantObject = self::$instancesObject;
        self::$instancesObject++;
    }
    
    // cette fonction va ajouter dans le header les initialisations et fichier requis pour utiliser tinyMCE
    // attention,  est ce que la structure du site permet un ajout dans le header ?
    function tinyMCEInit($params = array())
    {
        if (self::$appelInitTinyMCE==0) // test pour etre sûr que l'on inclu le js 1 seule fois
        {
            $this->addToJsHeader("<script type='text/javascript' src='".$this->getUrlRacine()."includes/framework/frameworkClasses/tiny_mce/tiny_mce.js'></script>");
            // debug des styles (au cas ou les icones de tinyMCE s'affiche bizzarement ,  c'est surement un conflit de styles avec la feuille de style du site
            $this->addToJsHeader("<style>#tinyMCE table{margin-bottom:0;} #tinyMCE table td{ padding : 0;}</style>");
        }
        
        
        $fieldName='defaultTinyMce';
        
        if (isset($params['fieldName']) && $params['fieldName'] !='') {
            $fieldName = $params['fieldName'];
        }
        
    
        
        $this->addToJsHeader("<script   type='text/javascript'>
            tinyMCE.init({
                mode : \"exact\", 
                elements : \"$fieldName\", 
                theme : \"advanced\", 
                force_br_newlines : true,  
                force_p_newlines : false,  
                forced_root_block : \"\", 

                theme_advanced_toolbar_location : \"top\",  
                theme_advanced_toolbar_align : \"left\",  
                theme_advanced_statusbar_location : \"bottom\", 
                theme_advanced_disable : \"styleselect\", 
                plugins : \"table,  fullscreen\", 
                theme_advanced_buttons3_add : \"tablecontrols,  fullscreen\", 
                theme_advanced_buttons1_add : \"forecolor, backcolor\", 
                relative_urls : false,
                remove_script_host : false

            });
        </script>");//
        
        
        // ces options enlevent la reecriture automatique des url pour les images ( par defaut ,  le host est enlevé ,  et on a un chemin absolu a partir de la racine du site)
        // par contre ces options ne fonctionnent pas avec la generation de pdf ,  apparement le generateur de pdf ne veut pas des chemins avec le host ...
        //remove_script_host : false, 
        //convert_urls : false, 
        
        
        
        self::$appelInitTinyMCE++;
    }
    
    
    
    
    
    function estDate($str = '')
    {
        $retour = 0;
        // $date est au format francais 
        if (preg_match('#^[0-9\-/\\\]*$#',  $str)) {
            $pattern = array('-',  '/',  '\\');
            $remplace = array ('',  '',  '');
            $str = str_replace( $pattern,  $remplace,  $str);
            $tailleDate = pia_strlen($str);
            if ($tailleDate === 8 OR $tailleDate === 6 OR $tailleDate === 5 OR $tailleDate === 4 )
                $retour = 1;
        }
        return $retour;
    }
    
    function estChaine($str)
    {
        if ( preg_match( '#^[a-zA-Z]*$#',  $str))
            return 1;
        else
            return 0;
    }

    public function estChiffre($str)
    {
        if ( is_numeric($str))
            return 1;
        else
            return 0;
    }

    // **************************************************************************************************************************************
    // affichage de la liste des donnees de la table en parametre
    // tableName = nom de la table
    // liaisonsExternes = listes des champs cherches dans un table externe afin de ne pas afficher des id dans les listes ,  pour afficher les id a la place il suffit de transmettre un tableau vide ,  ou
    // on peut desactiver le lien externe en mettant le parametre external link a false ,  ou en n'en precisant pas du tout,  si on met a false ,  
    // cela permet de garder la configuration du lien externe pour en faire autre chose (exemple si utilisation de popup pour recherche un id externe)
    // **************************************************************************************************************************************
    // exemple d'appel a la fonction pour gerer une liste de rue avec des left join vers ville sousQuartier et quartier : (ainsi que des criteres si l'utilisateur courant est moderateur) et gestion des dependances en cas de suppression d'une rue
    // =====> EXEMPLE
    //                        $optionsModerateur=array();
    //                    if ($authentification->getIdProfil()==3) // l'utilisateur courant est moderateur : on ne va donc afficher que la liste des rues que cette personne modere
    //                    {
    //                        $arrayVillesFromModerateur = $u->getArrayVillesModereesPar($authentification->getIdUtilisateur());
    //                        
    //                        if (count($arrayVillesFromModerateur)>0)
    //                            $optionsModerateur=array('sqlWhere'=>" AND idSousQuartier in (SELECT idSousQuartier FROM sousQuartier WHERE idQuartier IN (SELECT idQuartier FROM quartier WHERE idVille in (".implode(", ", $arrayVillesFromModerateur)."))) ");
    //                        else
    //                            $optionsModerateur=array('sqlWhere'=>" AND idRue='0' ");
    //                    }
    //                
    //    
    //                    $liensExternes=array(                            
    //                        'idSousQuartier'=>array('externalLeftJoin'=>true, 'sqlLeftJoin'=>"left join sousQuartier sq ON sq.idSousQuartier = rue.idSousQuartier", "fieldAliasToDisplay"=>"sq.nom as nomSousQuartier", "fieldToDisplay"=>"nomSousQuartier"), 
    //                        'idQuartier'=>array('externalLeftJoin'=>true, 'sqlLeftJoin'=>"left join quartier q ON q.idQuartier = sq.idQuartier", "fieldAliasToDisplay"=>"q.nom as nomQuartier", "fieldToDisplay"=>"nomQuartier"), 
    //                        'idVille'=>array('externalLeftJoin'=>true, 'sqlLeftJoin'=>"left join ville v ON v.idVille = q.idVille", "fieldAliasToDisplay"=>"v.nom as nomVille", "fieldToDisplay"=>"nomVille")
    //                    );
    //                    $generateur = new formGenerator();
    //                    
    //                    $dependances[0] = array('table'=>'historiqueAdresse', 'champLie'=>'idRue', 'message'=>"Attention il existe des dépendances au niveau de la table historiqueAdresse");
    //
    //                    echo $generateur->afficheFormulaireListe(array_merge($_GET, array('modeAffichageLienDetail'=>"adminAdresseDetail", "replaceAjouterButtonBy"=>"<input type='button' name='ajouter' value='ajouter' onclick=\"location.href='".$generateur->creerUrl('', 'ajoutNouvelleAdresse')."';\">"), $optionsModerateur), 
    //                    $liensExternes, 
    //                    $dependances);
    // **************************************************************************************************************************************
    // AUTRE EXEMPLE
    // comme exemple précédente mais gestion d'une liaison externe sans le leftJoin (convient pour les liaison entre un champ de la table courante et une table externe)
    //
    //
    //
    //                        $optionsModerateur=array();
    //                    if ($authentification->getIdProfil()==3) // l'utilisateur courant est moderateur : on ne va donc afficher que la liste des quartiers que cette personne modere pour sa ou ses villes
    //                    {
    //                        $arrayVillesFromModerateur = $u->getArrayVillesModereesPar($authentification->getIdUtilisateur());
    //                        
    //                        if (count($arrayVillesFromModerateur)>0)
    //                            $optionsModerateur=array('sqlWhere'=>" AND idQuartier in (SELECT idQuartier FROM quartier WHERE idVille in (".implode(", ", $arrayVillesFromModerateur).")) ");
    //                        else
    //                            $optionsModerateur=array('sqlWhere'=>" AND idQuartier='0' ");
    //                    }
    //                
    //                
    //                    $liensExternes=array(
    //                        'idQuartier'=>array('externalLink'=>true, 'externalFieldPrimaryKey'=>'idQuartier', 'externalTable'=>'quartier', 'externalFieldToDisplay'=>'nom'));
    //                    
    //                    $dependances[0] = array('table'=>'rue', 'champLie'=>'idSousQuartier', 'message'=>"Attention il existe des dépendances au niveau de la table des rues");
    //                    $dependances[1] = array('table'=>'historiqueAdresse', 'champLie'=>'idSousQuartier', 'message'=>"Attention il existe des dépendances au niveau de la table historiqueAdresse");
    //
    //                    $generateur = new formGenerator();
    //                    echo $generateur->afficheFormulaireListe(array_merge($_GET, array('modeAffichageLienDetail'=>"adminAdresseDetail", "replaceAjouterButtonBy"=>"<input type='button' name='ajouter' value='ajouter' onclick=\"location.href='".$generateur->creerUrl('', 'ajoutNouvelleAdresse', array("typeNew"=>"newSousQuartier"))."';\">"), $optionsModerateur), $liensExternes, $dependances);
    // **************************************************************************************************************************************
    public function afficheFormulaireListe($parametres=array(), $liaisonsExternes=array(), $dependances=array())
    {
        $html="";
        
        
        $date = new dateObject();
        
        $this->fields=array();
        // on recupere la liste des champs de la table
        $resListFieldsFromTable = $this->connexionBdd->requete("SHOW COLUMNS FROM ".$parametres['tableName'].";");
        
        $sqlWhere="";
        if (isset($parametres['sqlWhere'])) {
            $sqlWhere = $parametres['sqlWhere'];
        }
        
        if (isset($parametres['displayWithBBCode']) && $parametres['displayWithBBCode']==true) {
            $bbCode = new bbCodeObject();
        }
        
        
        
        
        $stringListeFields="";
        $champsDeRecherche=array();
        
        
        
        // fabrication de la liste des champs
        if (mysql_num_rows($resListFieldsFromTable)>0) {
            while($fetchFields=mysql_fetch_assoc($resListFieldsFromTable)) {
                $isPrimaryKey = false;
                if ($fetchFields['Key']=='PRI') {
                    $isPrimaryKey=true;
                }
            
                $type="";
                if (preg_match("/int/i", $fetchFields['Type'])) {
                    $type='entier';
                }
                elseif (preg_match("/varchar/i", $fetchFields['Type'])||preg_match("/longtext/i", $fetchFields['Type'])) {
                    $type='text';
                    $champsDeRecherche[]="lower(".$parametres['tableName'].".".$fetchFields['Field'].")";
                }
                elseif (preg_match("/date/i", $fetchFields['Type'])) {
                    $type='date';
                    $champsDeRecherche[]="lower(".$parametres['tableName'].".".$fetchFields['Field'].")";
                }
                
                
                $configChamp = array('name'=>$fetchFields['Field'],  'isPrimaryKey'=>$isPrimaryKey ,  'type'=>$type);
                
                if (isset($liaisonsExternes[$fetchFields['Field']])) {
                    $this->fields[]=array_merge($configChamp, $liaisonsExternes[$fetchFields['Field']]);
                }
                else
                {
                    $this->fields[]=$configChamp;
                }
                
                $stringListeFields.=$parametres['tableName'].".".$fetchFields['Field'].", ";
            }
        }
        else
        {
            echo "formGenerator :: il n'y a pas de champs pour cette table.<br>";
        }
        
        
        // ajout des champs configurés comme leftJoin
        $sqlLeftJoin="";
        foreach ($liaisonsExternes as $nomChamps => $configLiaisonExterne) {
            if (isset($configLiaisonExterne['externalLeftJoin']) && $configLiaisonExterne['externalLeftJoin']==true) {
                $sqlLeftJoin .= " ".$configLiaisonExterne['sqlLeftJoin']." ";
                $this->fields[]=array("name"=>$nomChamps, "externalLeftJoin"=>true, "fieldToDisplay"=>$configLiaisonExterne['fieldToDisplay']);
                $stringListeFields.=$configLiaisonExterne['fieldAliasToDisplay'].", ";
            }
        }
        
        
        
        
        // affichage des données dans un tableau avec pagination
        if (count($this->fields)>0) {
        
            $sqlRecherche="";
            $objetDeLaRecherche="";
            // gestion de la recherche
            if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='rechercheAdministration') {
                $sqlRecherche=" AND LOWER(CONCAT_WS(' ', ".implode(", ", $champsDeRecherche).")) LIKE \"%".mysql_real_escape_string($this->variablesPost['rechercheFormulaireAdministration'])."%\" ";
                
                $objetDeLaRecherche = $this->variablesPost['rechercheFormulaireAdministration'];
            }
            elseif (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='suppression') {
                // gestion de la suppression
                if (isset($this->variablesPost['selection']) && count($this->variablesPost['selection'])>0) {
                    // recherche du nom de champ identifiant
                    foreach ($this->fields as $field)
                    {
                        if ($field['isPrimaryKey']==true)
                        {
                            $champIdentifiant = $field['name'];
                        }
                    }

                    // on verifie les dependances
                    $erreurObj = new objetErreur();
                    if (count($dependances)>0)
                    {                        
                        foreach ($dependances as $indice => $dependance)
                        {
                            $reqVerifDependance = "SELECT * FROM ".$dependance['table']." WHERE ".$dependance['champLie']." in (".implode(", ", $this->variablesPost['selection']).")";
                            $resVerifDependance = $this->connexionBdd->requete($reqVerifDependance);
                            if (mysql_num_rows($resVerifDependance)>0)
                            {
                                $erreurObj->ajouter($dependance['message']);
                            }
                        }                        
                        
                        if ($erreurObj->getNbErreurs()>0)
                        {
                            $erreurObj->ajouter("La suppression n'a pu être effectuée,  veuillez contacter l'administrateur de la base de données");
                        }
                        
                        $html.=$erreurObj->afficher();
                        
                    }                    
                    if ($erreurObj->getNbErreurs()==0)
                    {
                        $reqDelete = "delete from ".$parametres['tableName']." where ".$champIdentifiant." in (".implode(", ", $this->variablesPost['selection']).")";
                        $resDelete = $this->connexionBdd->requete($reqDelete);
                        echo "suppression effectuée<br>";
                    }
                    
                }
                
            }
            $stringListeFields=pia_substr($stringListeFields, 0, -2);
            
            $reqNbLignesListe = "select 0 from ".$parametres['tableName']." ".$sqlLeftJoin." WHERE 1=1 ".$sqlWhere." ".$sqlRecherche;
            $resNbLignesListe = $this->connexionBdd->requete($reqNbLignesListe);
            $nbLignesTotales = mysql_num_rows($resNbLignesListe);
            
            $html.='Edition de la table : '.$parametres['tableName']."<br>";
            $html.= "Il y a ".$nbLignesTotales." enregistrements<br>";


                // nombre d'images affichées sur une page
                $nbEnregistrementsParPage = 20;
                $arrayPagination=$this->pagination(array(
                                        'nomParamPageCourante'=>'pageCourantePagination', 
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage, 
                                        'nbEnregistrementsTotaux'=>$nbLignesTotales, 
                                        'typeLiens'=>'formulaire', 
                                        'idFormulaire'=>'formListe', 
                                        'champPageCourante'=>'pageCourantePagination', 
                                        'nomChampActionFormulaireOnSubmit'=>'validationFormulaireAdministration', 
                                        'nomActionFormulaireOnSubmit'=>'rechercheAdministration'
                                        ));

            

            $reqListe = "SELECT ".$stringListeFields." FROM ".$parametres['tableName']." ".$sqlLeftJoin." WHERE 1=1 ".$sqlWhere." ".$sqlRecherche." LIMIT ".$arrayPagination['limitSqlDebut'].", ".$nbEnregistrementsParPage;
            
            
            $resListe = $this->connexionBdd->requete($reqListe);
            
            $actionForm="";
            if (isset($parametres['actionAffichageFormulaireIfSubmit']) && $parametres['actionAffichageFormulaireIfSubmit']!='') {
                $actionForm = "action='".$parametres['actionAffichageFormulaireIfSubmit']."'";
            }
            
            $html.= "<form name='formListe' ".$actionForm." id='formListe' enctype='multipart/form-data' method='POST'>";
            $html.="Recherche : <input type='text' name='rechercheFormulaireAdministration' id='rechercheFormulaireAdministration' value='".$objetDeLaRecherche."'>";
            $html.="<input type='submit' onclick=\"document.getElementById('validationFormulaireAdministration').value='rechercheAdministration';\"><br>";
            $html.=$arrayPagination['html'].'<br>';
            $html.="<input type='hidden' name='pageCourantePagination' id='pageCourantePagination' value=''>";
            $html.="<input type='hidden' name='validationFormulaireAdministration' id='validationFormulaireAdministration' value=''>";
            $html.= "<table><tr><td>Selection</td>";
            // les entetes
            foreach ($this->fields as $field) {
                $html.= "<td>".$field['name']."</td>";
            }
            $html.= "</tr>";
            
            while($fetchListe=mysql_fetch_assoc($resListe)) {
                $html.="<tr>";
                foreach ($this->fields as $field) {
                    if ($fetchListe[$field['name']] !== "0") {
                        if (isset($field['externalLink']) && $field['externalLink']==true) // si le champ courant fait appel a une table externe a la table courante
                        {
                            // ce champs va chercher des donnees dans une autre table
                            // ....
                            // a optimiser en left join si possible
                            $reqExternal = "select ".$field['externalFieldPrimaryKey'].", ".$field['externalFieldToDisplay']." from ".$field['externalTable']." where ".$field['externalFieldPrimaryKey']."='".$fetchListe[$field['name']]."'";
                            $resExternal = $this->connexionBdd->requete($reqExternal);
                            $fetchExternal = mysql_fetch_assoc($resExternal);
                            $html.="<td>".$fetchExternal[$field['externalFieldToDisplay']]."</td>";
                        }
                        elseif (isset($field['externalLeftJoin']) && $field['externalLeftJoin']==true)
                        {
                        
                            $html.="<td>".$fetchListe[$field['fieldToDisplay']]."</td>";
                        
                        
                        }
                        elseif ($field['isPrimaryKey']==true)
                        {
                            if (isset($parametres['modeAffichageLienDetail']))
                            {
                                $modeAffichage = $parametres['modeAffichageLienDetail'];
                            }
                            else
                            {
                                $modeAffichage = "administrationAfficheModification";
                            }
                                
                            $html.="<td><input type='checkbox' name='selection[]' value='".$fetchListe[$field['name']]."'></td><td><a href='".$this->creerUrl('', $modeAffichage, array('tableName'=>$parametres['tableName'],  'idModification'=>$fetchListe[$field['name']]))."'>".$fetchListe[$field['name']]."</a></td>";
                        }
                        else
                        {
                            if ($field['type']=='date')
                            {
                                $html.="<td>".$date->toFrenchAffichage(stripslashes($fetchListe[$field['name']]))."</td>";
                            }
                            else
                            {
                                if (isset($parametres['displayWithBBCode']) && $parametres['displayWithBBCode']==true)
                                {
                                    $html.="<td>".$bbCode->convertToDisplay(array('text'=>stripslashes($fetchListe[$field['name']])))."</td>";
                                }
                                else
                                {                        
                                    $html.="<td>".stripslashes($fetchListe[$field['name']])."</td>";
                                }
                            }
                        }
                    }
                }
                $html.="</tr>";
            }
            $html.="</table>";
        }
        
        
        if (!isset($parametres['noSupprimerButton']) || $parametres['noSupprimerButton']==false) {
            $html.="<input type='submit' value='Supprimer la selection' onclick=\"document.getElementById('validationFormulaireAdministration').value='suppression';\" name='supprimer'>";
        }
        
        
        if (isset($parametres['replaceAjouterButtonBy'])) {
            $html.=$parametres['replaceAjouterButtonBy'];
        }
        else
        {
            if (!isset($parametres['noAjouterButton']) || $parametres['noAjouterButton']==false) {
                $html.="<input type='button' value='ajouter' name='ajouter' onclick=\"location.href='".$this->creerUrl('', 'administrationAfficheAjout', array('tableName'=>$parametres['tableName']))."';\">";
            }
        }
        $html.="</form>";
        
        return $html;
    }
    
    // **************************************************************************************************************************************
    // formulaire de modification d'un enregistrement
    // liaisonsExternes = listes des champs cherches dans un table externe afin de ne pas afficher des id dans les listes ,  pour afficher les id a la place il suffit de transmettre un tableau vide ,  ou
    // on peut desactiver le lien externe en mettant le parametre external link a false ,  ou en n'en precisant pas du tout,  si on met a false ,  
    // cela permet de garder la configuration du lien externe pour en faire autre chose (exemple si utilisation de popup pour recherche un id externe)
    
    // exemple de parametres pour ajouter un element d'upload de photo au formulaire (ici la photo sera liee a l'enregistrement courant de la table edité,  avec pour nom de fichier l'identifiant de la table,  donc pas besoin d'enregistrer le nom du fichier dans la table on fera simplement un test pour voir si le fichier existe ou non a l'affichage)
    //            $parametres = array_merge($parametres, 
    //            array(    'afficheMiseEnFormeLongText'=>true, 
    //                    'displayWithBBCode'=>true, 
    //                    'fieldsNotInBdd'=>array(
    //                                            0=>array(
    //                                                    'name'=>'uploadLogo', 
    //                                                    'type'=>'uploadImageLiee', 
    //                                                    'redimFilesAndSizesConfig'=>array(
    //                                                            array('taille'=>0, 'nomFichierDestinationParametre'=>"###bddField[idSource]###_original.jpg", 'repertoireDestination'=>$this->cheminPhysique."images/logosSources/"), 
    //                                                            array('taille'=>200, 'nomFichierDestinationParametre'=>"###bddField[idSource]###.jpg", 'repertoireDestination'=>$this->cheminPhysique."images/logosSources/")), 
    //                                                    'valueParametrable'=>array(
    //                                                                            'affichageParametre'=>"<img src='".$this->urlImages."logosSources/###bddField[idSource]###.jpg' border=0>", 
    //                                                                            "cheminFichierATesterPourAffichage"=>$this->cheminPhysique."images/logosSources/###bddField[idSource]###.jpg"
    //                                                                            )
    //                                                    )
    //                                            )
    //                )
    //            );
    // **************************************************************************************************************************************
    public function afficheFormulaireModification($parametres=array(), $liaisonsExternes=array(), $dependances = array())
    {
        $html="<form action='".$this->creerUrl('administration', '', array('tableName'=>$parametres['tableName']))."' name='formModification' id='formModification' enctype='multipart/form-data' method='POST'>";
        $html.="<input type='hidden' name='validationFormulaireAdministration' id='validationFormulaireAdministration' value='modification'>";// grace a ce champ on indique que l'on effectue une modification a la validation du formulaire
        $this->fields=array();
        $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
        $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
        $arrayFields = array();
        $stringListeFields="";
        $primaryKeyFieldName="";
        
        $date = new dateObject();
        
        while($fetchFields = mysql_fetch_assoc($resFieldsListe)) {
                $isPrimaryKey = false;
                if ($fetchFields['Key']=='PRI') {
                    $isPrimaryKey=true;
                    $primaryKeyFieldName=$fetchFields['Field'];
                }
            
            
                $type="";
                if (preg_match("/int/i", $fetchFields['Type'])) {
                    $type='entier';
                }
                elseif (preg_match("/varchar/i", $fetchFields['Type'])) {
                    $type='text';
                }
                elseif (preg_match("/longtext/i", $fetchFields['Type'])) {
                    $type='bigText';
                }
                elseif (preg_match("/text/i", $fetchFields['Type'])) {
                    $type='text';
                }
                elseif (preg_match("/date/i", $fetchFields['Type'])) {
                    $type='date';
                }
                
                
                $configChamp = array('name'=>$fetchFields['Field'],  'isPrimaryKey'=>$isPrimaryKey ,  'type'=>$type);
                
                if (isset($liaisonsExternes[$fetchFields['Field']])) {
                    $this->fields[]=array_merge($configChamp, $liaisonsExternes[$fetchFields['Field']]);
                }
                else
                {
                    $this->fields[]=$configChamp;
                }
                
                $stringListeFields.=$fetchFields['Field'].", ";
        }
        
        $reqValeurCouranteBdd = "select * from ".$parametres['tableName']." where ".$primaryKeyFieldName."='".$parametres['id']."'";
        $resValeurCouranteBdd = $this->connexionBdd->requete($reqValeurCouranteBdd);
        $fetchValeurCouranteBdd = mysql_fetch_assoc($resValeurCouranteBdd);
        
        $html.="<table>";
        
        
        // ajout de champs qui ne sont pas relié a la base de donnée ,  mais qui peuvent afficher des infos ,  proposer un download etc
        if (isset($parametres['fieldsNotInBdd']) && is_array($parametres['fieldsNotInBdd']) && count($parametres['fieldsNotInBdd'])>0) {
            foreach ($parametres['fieldsNotInBdd'] as $indice => $valueExternalField) {
                $this->fields[] = array_merge(array('isPrimaryKey'=>false, 'fieldNotInBdd'=>true), $valueExternalField);
            }
        }
        
        foreach ($this->fields as $field) {
            $primaryKeyReadOnly='';
            if ($field['isPrimaryKey'])
                $primaryKeyReadOnly=' readonly';
            
            if (isset($field['externalLink']) && $field['externalLink']==true) // si le champ courant fait appel a une table externe a la table courante
            {
                // on va afficher une liste
                $reqExternalLink="select * from ".$field['externalTable'];
                $resExternalLink = $this->connexionBdd->requete($reqExternalLink);
                $champHtml="<select name='".$field['name']."'".$primaryKeyReadOnly.">";
                while($fetchExternalLink = mysql_fetch_assoc($resExternalLink)) {
                    $selected="";
                    if ($fetchExternalLink[$field['externalFieldPrimaryKey']]==$fetchValeurCouranteBdd[$field['name']])
                    {
                        $selected=" selected";
                    }
                    $champHtml.="<option value='".$fetchExternalLink[$field['externalFieldPrimaryKey']]."'".$selected.">".$fetchExternalLink[$field['externalFieldToDisplay']]."</option>";
                }
                $champHtml.="</select>";
            }
            elseif (isset($field['fieldNotInBdd']) && $field['fieldNotInBdd']==true) {
                $champHtml = "";
                switch($field['type']) {
                    case 'uploadImageLiee':
                    
                        $affichageParametre = "";
                        
                        if (isset($field['valueParametrable']))
                        {
                            if (isset($field['valueParametrable']['affichageParametre']))
                            {
                                // l'affichage se fait il avec une condition ?
                                if (isset($field['valueParametrable']['cheminFichierATesterPourAffichage']))
                                {
                                    // on remplace le parametre par une valeur s'il y en a un 
                                    $valueReplacementBdd = ""; // valeur que va prendre la parametre a remplacer
                                    $matches = array();
                                    preg_match("/\#\#\#bddField\[(.+)\]\#\#\#/", $field['valueParametrable']['cheminFichierATesterPourAffichage'], $matches);
                                    if (count($matches)>0)
                                    {
                                        $valueReplacementBdd = $fetchValeurCouranteBdd[$matches[1]];
                                    }

                                    if (file_exists(str_replace($matches[0], $valueReplacementBdd, $field['valueParametrable']['cheminFichierATesterPourAffichage'])))
                                    {
                                        $matches = array();
                                        preg_match("/\#\#\#bddField\[(.+)\]\#\#\#/", $field['valueParametrable']['affichageParametre'], $matches);
                                        
                                        $valueReplacementBdd = "";
                                        if (count($matches)>0)
                                        {
                                            $valueReplacementBdd = $fetchValeurCouranteBdd[$matches[1]];
                                        }
                                        $affichageParametre = str_replace($matches[0], $valueReplacementBdd, $field['valueParametrable']['affichageParametre']);
                                    }
                                }
                            }
                        }
                    
                    
                        $champHtml.="<input type='file' name='".$field['name']."' id='".$field['name']."' value=''>".$affichageParametre;
                        break;
                }
            }
            else
            {
                switch($field['type']) {
                    case "date":
                        $champHtml = "<input type='text' style='width:150px;' name='".$field['name']."' value=\"".$date->toFrenchAffichage($fetchValeurCouranteBdd[$field['name']])."\"".$primaryKeyReadOnly.">";
                        break;
                    case "text":
                        $champHtml = "<input type='text' style='width:150px;' name='".$field['name']."' value=\"".$this->getInputSpecialCharsConversion(array('text'=>$fetchValeurCouranteBdd[$field['name']]))."\"".$primaryKeyReadOnly.">";
                        break;
                    
                    case "entier":
                        $champHtml = "<input type='text' style='width:50px;' name='".$field['name']."' value=\"".$fetchValeurCouranteBdd[$field['name']]."\"".$primaryKeyReadOnly.">";
                        break;
                    
                    case "bigText":
                        $champHtml="";
                        if (isset($parametres['afficheMiseEnFormeLongText']) && $parametres['afficheMiseEnFormeLongText']==true)
                        {
                            $arrayBBCode = $this->getBBCodeJSMiseEnForme($field['name'], 'formModification');
                            $champHtml.=$arrayBBCode['boutonsHTML'];
                        }
                        $champHtml .= "<textarea cols='50' rows='5' name='".$field['name']."' id='".$field['name']."'>".stripslashes($fetchValeurCouranteBdd[$field['name']])."</textarea>";
                        
                        if (isset($parametres['afficheMiseEnFormeLongText']) && $parametres['afficheMiseEnFormeLongText']==true)
                        {
                            $champHtml.= $arrayBBCode['divAndJsAfterForm'];
                        }
                        
                        break;
                    
                
                }
            }
        
            $html.="<tr><td>".$field['name']."</td><td>".$champHtml."</td></tr>";
        }
        
        $html.="</table>";
        
        $html.="<input type='submit' value='Modifier' name='modifier'>";
        
        $alertJs = "document.getElementById('validationFormulaireAdministration').value = 'suppressionFromModifForm';document.getElementById('formModification').submit();";
        if (count($dependances)>0) {
            $msgDependances = "";
            foreach ($dependances as $indiceDependance => $dependance) {
                $reqDependance = "SELECT 0 FROM ".$dependance['table']." WHERE ".$dependance['champLie']."='".$parametres['id']."'";
                $resDependance = $this->connexionBdd->requete($reqDependance);
                if (mysql_num_rows($resDependance)>0) {
                    $msgDependances .= $dependance['message']."\\n";
                }
            }
            
            
            if ($msgDependances!='') {
                $alertJs = "msgConfirmation();";
                $html.="
                    <script  >
                    function msgConfirmation()
                    {
                        if (confirm(\"Attention : \\n $msgDependances Si vous confirmez la suppression les dépendances seront réinitialisées\"))
                        {
                            document.getElementById('validationFormulaireAdministration').value = 'suppressionFromModifForm';
                            document.getElementById('formModification').submit();
                        }
                        else
                        {
                            document.getElementById('validationFormulaireAdministration').value = 'modification';
                        }
                    }
                
                    </script>
                ";
            }
        }
        
        
        $html.="<input type='button' value='Supprimer' name='supprimer' onclick=\"$alertJs\">";
        
        
        $html.="</form>";
        
        if (isset($parametres['afficheDependancesInIFrameUrl']) && $parametres['afficheDependancesInIFrameUrl']!='') {    
            $html.="<br><b>"._("Dépendances :")."</b><br>";
            // si un lien est précisé on affiche une iframe vers ce lien (
            $html.= "<iframe width='700' height='1000' frameborder='no' src='".str_replace(urlencode("###currentId###"),  $parametres['id'],  $parametres['afficheDependancesInIFrameUrl'])."'></iframe>";
        }
        
        return $html;
    }
    
    // **************************************************************************************************************************************
    // remplace les caracteres speciaux comme les doubles quotes par des caracteres que l'on peut afficher dans un input
    // **************************************************************************************************************************************
    public function getInputSpecialCharsConversion($params = array())
    {
        $retour = "";
        if (isset($params['text']) && $params['text']!='') {
            $retour = str_replace("\"", "&quot;", $params['text']);
        }
        return $retour;
    }
    
    
    // **************************************************************************************************************************************
    // formulaire d'ajout d'un enregistrement 
    // liaisonsExternes = listes des champs cherches dans un table externe afin de ne pas afficher des id dans les listes ,  pour afficher les id a la place il suffit de transmettre un tableau vide ,  ou
    // on peut desactiver le lien externe en mettant le parametre external link a false ,  ou en n'en precisant pas du tout,  si on met a false ,  
    // cela permet de garder la configuration du lien externe pour en faire autre chose (exemple si utilisation de popup pour recherche un id externe)
    
    // exemple de parametres pour ajouter un element d'upload de photo au formulaire (ici la photo sera liee a l'enregistrement courant de la table edité,  avec pour nom de fichier l'identifiant de la table,  donc pas besoin d'enregistrer le nom du fichier dans la table on fera simplement un test pour voir si le fichier existe ou non a l'affichage)
    //            $parametres = array_merge($parametres, 
    //            array(    'afficheMiseEnFormeLongText'=>true, 
    //                    'displayWithBBCode'=>true, 
    //                    'fieldsNotInBdd'=>array(
    //                                            0=>array(
    //                                                    'name'=>'uploadLogo', 
    //                                                    'type'=>'uploadImageLiee', 
    //                                                    'redimFilesAndSizesConfig'=>array(
    //                                                            array('taille'=>0, 'nomFichierDestinationParametre'=>"###bddField[idSource]###_original.jpg", 'repertoireDestination'=>$this->cheminPhysique."images/logosSources/"), 
    //                                                            array('taille'=>200, 'nomFichierDestinationParametre'=>"###bddField[idSource]###.jpg", 'repertoireDestination'=>$this->cheminPhysique."images/logosSources/")), 
    //                                                    'valueParametrable'=>array(
    //                                                                            'affichageParametre'=>"<img src='".$this->urlImages."logosSources/###bddField[idSource]###.jpg' border=0>", 
    //                                                                            "cheminFichierATesterPourAffichage"=>$this->cheminPhysique."images/logosSources/###bddField[idSource]###.jpg"
    //                                                                            )
    //                                                    )
    //                                            )
    //                )
    //            );
    // **************************************************************************************************************************************
    public function afficheFormulaireAjout($parametres=array(), $liaisonsExternes=array())
    {
        $html="<form action='".$this->creerUrl('administration', '', array('tableName'=>$parametres['tableName']))."' name='formAjout' id='formAjout' enctype='multipart/form-data' method='POST'>";
        $html.="<input type='hidden' name='validationFormulaireAdministration' value='ajout'>"; // grace a ce champ on indique que l'on effectue un ajout a la validation du formulaire
        $this->fields=array();
        $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
        $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
        $arrayFields = array();
        $stringListeFields="";
        while($fetchFields = mysql_fetch_assoc($resFieldsListe)) {
                $isPrimaryKey = false;
                if ($fetchFields['Key']=='PRI') {
                    $isPrimaryKey=true;
                }
            
                $type="";
                if (preg_match("/int/i", $fetchFields['Type'])) {
                    $type='entier';
                }
                elseif (preg_match("/varchar/i", $fetchFields['Type'])) {
                    $type='text';
                }
                elseif (preg_match("/longtext/i", $fetchFields['Type'])) {
                    $type='bigText';
                }
                elseif (preg_match("/date/i", $fetchFields['Type'])) {
                    $type='date';
                }
                
                
                $configChamp = array('name'=>$fetchFields['Field'],  'isPrimaryKey'=>$isPrimaryKey ,  'type'=>$type);
                
                if (isset($liaisonsExternes[$fetchFields['Field']])) {
                    $this->fields[]=array_merge($configChamp, $liaisonsExternes[$fetchFields['Field']]);
                }
                else
                {
                    $this->fields[]=$configChamp;
                }
                
                $stringListeFields.=$fetchFields['Field'].", ";
        }
        
        //$reqValeurCouranteBdd = "select * from ".$parametres['tableName']." where ".$parametres['idFieldName']."='".$parametres['id']."'";
        //$resValeurCouranteBdd = $this->connexionBdd->requete($reqValeurCouranteBdd);
        //$fetchValeurCouranteBdd = mysql_fetch_assoc($resValeurCouranteBdd);
        
        
        // ajout de champs qui ne sont pas relié a la base de donnée ,  mais qui peuvent afficher des infos ,  proposer un download etc
        if (isset($parametres['fieldsNotInBdd']) && is_array($parametres['fieldsNotInBdd']) && count($parametres['fieldsNotInBdd'])>0) {
            foreach ($parametres['fieldsNotInBdd'] as $indice => $valueExternalField) {
                $this->fields[] = array_merge(array('isPrimaryKey'=>false, 'fieldNotInBdd'=>true), $valueExternalField);
            }
        }
        
        
        $html.="<table>";
        
        foreach ($this->fields as $field) {
            $primaryKeyReadOnly='';
            if (!$field['isPrimaryKey']) {
                //$primaryKeyReadOnly=' readonly';
                
                if (isset($field['externalLink']) && $field['externalLink']==true) // si le champ courant fait appel a une table externe a la table courante
                {
                    // on va afficher une liste
                    $reqExternalLink="select * from ".$field['externalTable'];
                    $resExternalLink = $this->connexionBdd->requete($reqExternalLink);
                    $champHtml="<select name='".$field['name']."'".$primaryKeyReadOnly.">";
                    while($fetchExternalLink = mysql_fetch_assoc($resExternalLink))
                    {
                        $selected="";
                        /*if ($fetchExternalLink[$field['externalFieldPrimaryKey']]==$fetchValeurCouranteBdd[$field['name']])
                        {
                            $selected=" selected";
                        }*/
                        $champHtml.="<option value='".$fetchExternalLink[$field['externalFieldPrimaryKey']]."'".$selected.">".$fetchExternalLink[$field['externalFieldToDisplay']]."</option>";
                    }
                    $champHtml.="</select>";
                }
                elseif (isset($field['fieldNotInBdd']) && $field['fieldNotInBdd']==true) {
                    $champHtml = "";
                    switch($field['type'])
                    {
                        case 'uploadImageLiee':
                            $champHtml.="<input type='file' name='".$field['name']."' id='".$field['name']."' value=''>";
                            break;
                    }
                }
                else
                {
                    switch($field['type'])
                    {
                        case "text":
                            $champHtml = "<input type='text' style='width:150px;' name='".$field['name']."' value=\"\"".$primaryKeyReadOnly.">";//".$fetchValeurCouranteBdd[$field['name']]."
                            break;
                        case "date":
                            $champHtml = "<input type='text' style='width:150px;' name='".$field['name']."' value=\"\"".$primaryKeyReadOnly.">";
                            break;
                        
                        case "entier":
                            $champHtml = "<input type='text' style='width:50px;' name='".$field['name']."' value=\"\"".$primaryKeyReadOnly.">"; //".$fetchValeurCouranteBdd[$field['name']]."
                            break;
                        case "bigText":
                            $champHtml="";
                            if (isset($parametres['afficheMiseEnFormeLongText']) && $parametres['afficheMiseEnFormeLongText']==true)
                            {
                                $arrayBBCode = $this->getBBCodeJSMiseEnForme($field['name'], 'formAjout');
                                $champHtml.=$arrayBBCode['boutonsHTML'];
                            }
                            $champHtml .= "<textarea cols='50' rows='5' name='".$field['name']."' id='".$field['name']."'></textarea>";
                            if (isset($parametres['afficheMiseEnFormeLongText']) && $parametres['afficheMiseEnFormeLongText']==true)
                            {
                                $champHtml.=$arrayBBCode['divAndJsAfterForm'];
                            }
                            break;
                    
                    }
                }
        
                $html.="<tr><td>".$field['name']."</td><td>".$champHtml."</td></tr>";
            }
        }
        
        $html.="</table>";
        
        $html.="<input type='submit' value='Ajouter' name='ajouter'>";
        
        $html.="</form>";
        
        return $html;
    }

    
    
    // **************************************************************************************************************************************
    // effectue une modification
    // **************************************************************************************************************************************
    public function modifier($parametres=array(), $liaisonsExternes=array() )
    {
        $html="";
        
        $this->fields=array();
        $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
        $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
        
        // fabrication de la chaine requete de modification
        $arrayModification=array();
        while($fetchFields = mysql_fetch_assoc($resFieldsListe)) {
            if ($fetchFields['Key']!='PRI') {
                if (preg_match("/date/i", $fetchFields['Type'])) // cas d'un champ date ,  on recupere la version francaise
                {
                    if ((pia_ereg("/", $this->variablesPost[$fetchFields['Field']]) || pia_strlen($this->variablesPost[$fetchFields['Field']])>3) && !pia_ereg("-", $this->variablesPost[$fetchFields['Field']] ))
                    {// on admet que s'il y a des / ,  c'est le format francais,  donc on convertis
                        $date = new dateObject();
                $arrayModification[]=$fetchFields['Field']."=\"".mysql_real_escape_string($date->toBdd($date->convertYears($this->variablesPost[$fetchFields['Field']])))."\"";
                    }
                    else
                    {// sinon format anglais
                        $arrayModification[]=$fetchFields['Field']."=\"".mysql_real_escape_string($this->variablesPost[$fetchFields['Field']])."\"";
                    }
                }
                else
                {
                    $arrayModification[]=$fetchFields['Field']."=\"".mysql_real_escape_string($this->variablesPost[$fetchFields['Field']])."\"";
                }
            }
            else
            {
                $primaryKeyField = $fetchFields['Field'];
            }
        }
        
        // gestion des champs supplementaires du formulaire qui ne sont pas des champs de la base de donnée
        if (isset($parametres['fieldsNotInBdd']) && is_array($parametres['fieldsNotInBdd']) && count($parametres['fieldsNotInBdd'])>0) {
            foreach ($parametres['fieldsNotInBdd'] as $indice => $valuesFields) {
                // traitement specifiques aux champs qui ne sont pas liés a la base de donnée
                switch($valuesFields['type']) {
                    case 'uploadImageLiee':
                        // ce cas d'upload n'ajoute pas de nom de fichier dans la base ,  on va en principe se referer a l'identifiant de la table courante
                        // exemple ,  on ajoute une image sur un personne,  l'image aura donc pour nom de fichier "idPersonne".jpg par exemple en fonction du parametrage
                        if (isset($_FILES[$valuesFields['name']]) && $_FILES[$valuesFields['name']]['error']) 
                        {
                            switch ($_FILES[$valuesFields['name']]['error'])
                            {
                                case 1: // UPLOAD_ERR_INI_SIZE
                                    echo"Le fichier dépasse la limite autorisée par le serveur !";
                                    break;
                                case 2: // UPLOAD_ERR_FORM_SIZE
                                    echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
                                    break;
                                case 3: // UPLOAD_ERR_PARTIAL
                                    echo "L'envoi du fichier a été interrompu pendant le transfert !";
                                    break;
                                case 4: // UPLOAD_ERR_NO_FILE
                                    echo "Le fichier que vous avez envoyé a une taille nulle !";
                                    break;
                            }
                        }
                        else 
                        {
                            // pas d'erreur d'upload
                            
                            $i = new imageObject(); // classe image du framework
                            $f = new fileObject();
                            
                            
                            
                            
                            if (isset($valuesFields['redimFilesAndSizesConfig']) && is_array($valuesFields['redimFilesAndSizesConfig']) && count($valuesFields['redimFilesAndSizesConfig'])>0)
                            {
                                foreach ($valuesFields['redimFilesAndSizesConfig'] as $indiceConfigRedims => $valueConfigRedims)
                                {    
                                    $fichierParametre = "";
                                    // on remplace le parametre par une valeur s'il y en a un 
                                    $valueReplacementBdd = ""; // valeur que va prendre la parametre a remplacer
                                    $matches = array();
                                    preg_match("/\#\#\#bddField\[(.+)\]\#\#\#/", $valueConfigRedims['nomFichierDestinationParametre'], $matches); // on detecte si il y a un parametre a remplacer par une valeur de la base de donnée ou pas
                                    if (count($matches)>0)
                                    {
                                        $req = "SELECT ".$matches[1]." FROM ".$parametres['tableName']." WHERE ".$primaryKeyField."='".$this->variablesPost[$primaryKeyField]."'";
                                        $res = $this->connexionBdd->requete($req);
                                        $fetchValeurCouranteBdd = mysql_fetch_assoc($res);
                                        $valueReplacementBdd = $fetchValeurCouranteBdd[$matches[1]];
                                        
                                        $fichierParametre = str_replace($matches[0], $valueReplacementBdd, $valueConfigRedims['nomFichierDestinationParametre']);
                                        
                                    }
                                    
                                    $imageType = strtolower($f->getExtensionFromFile($_FILES[$valuesFields['name']]['name']));
                                    
                                    if (file_exists($valueConfigRedims['repertoireDestination'].$fichierParametre))
                                    {
                                        // si un fichier du meme nom existe ,  on l'efface
                                        unlink($valueConfigRedims['repertoireDestination'].$fichierParametre);
                                    }
                                    
                                    if ($fichierParametre!='')
                                    {
                                        if (isset($valueConfigRedims['taille']))
                                        {
                                            $i->redimension($_FILES[$valuesFields['name']]['tmp_name'], $imageType, $valueConfigRedims['repertoireDestination'].$fichierParametre, $valueConfigRedims['taille'], array('redimOnlyIfFileSizesSuperiorToRedimValue'=>true));
                                        }
                                    }
                                    else
                                    {
                                        echo "Le fichier n'a pu être redimensionné. Erreur de parametrage dans la fonction du framework formGenerator::modifier()<br>";
                                    }
                                }
                                
                                
                                unlink($_FILES[$valuesFields['name']]['tmp_name']);
                            
                            }
                        }

                    
                        break;
                }
            }
        }
        
        $listeModification=implode(', ', $arrayModification);
        $requeteModification="update ".$parametres['tableName']." set ".$listeModification." where ".$primaryKeyField."='".$this->variablesPost[$primaryKeyField]."'";
        
        $this->connexionBdd->requete($requeteModification);
        
        return $html;
    }
    
    
    // **************************************************************************************************************************************
    // effectue un ajout
    // **************************************************************************************************************************************
    public function ajouter($parametres=array(), $liaisonsExternes=array() )
    {
        $html="";
        
        $this->fields=array();
        $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
        $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
        
        // fabrication de la chaine requete d'ajout
        $arrayFields=array();
        $primaryKeyField = "";
        while($fetchFields = mysql_fetch_assoc($resFieldsListe)) {
            if ($fetchFields['Key']!='PRI') {
                //$requeteModification.=$fetchFields['Field']."='".$this->variablesPost[$fetchFields['Field']]."'";
                $arrayFields[] = $fetchFields['Field'];
                if (preg_match("/date/i", $fetchFields['Type'])) // cas d'un champ date ,  on recupere la version francaise
                {
                    $d = new dateObject();
                    if ((pia_ereg("/", $this->variablesPost[$fetchFields['Field']]) || pia_strlen($this->variablesPost[$fetchFields['Field']])>3) && !pia_ereg("-", $this->variablesPost[$fetchFields['Field']] ))
                    {
                        $arrayValues[] = $d->toBdd($d->convertYears($this->variablesPost[$fetchFields['Field']]));
                    }
                    else
                    {
                        $arrayValues[] = $this->variablesPost[$fetchFields['Field']];
                    }
                    
                    
                }
                else
                {
                    
                    $arrayValues[] = $this->variablesPost[$fetchFields['Field']];
                }
            }
            else
            {
                $primaryKeyField = $fetchFields['Field'];
            }
        }
        

        
        $listeFields = implode(', ' ,  $arrayFields);
        $listeValues = implode('", "' ,  $arrayValues);
        
        $requeteAjout="insert into ".$parametres['tableName']." (".$listeFields.") VALUES (\"".$listeValues."\")";
        
        $this->connexionBdd->requete($requeteAjout);
        
        $idNouvelElement = mysql_insert_id();
        
        // gestion des champs supplementaires du formulaire qui ne sont pas des champs de la base de donnée
        if (isset($parametres['fieldsNotInBdd']) && is_array($parametres['fieldsNotInBdd']) && count($parametres['fieldsNotInBdd'])>0) {
            foreach ($parametres['fieldsNotInBdd'] as $indice => $valuesFields) {
                // traitement specifiques aux champs qui ne sont pas liés a la base de donnée
                switch($valuesFields['type']) {
                    case 'uploadImageLiee':
                        // ce cas d'upload n'ajoute pas de nom de fichier dans la base ,  on va en principe se referer a l'identifiant de la table courante
                        // exemple ,  on ajoute une image sur un personne,  l'image aura donc pour nom de fichier "idPersonne".jpg par exemple en fonction du parametrage
                        if (isset($_FILES[$valuesFields['name']]) && $_FILES[$valuesFields['name']]['error']) 
                        {
                            switch ($_FILES[$valuesFields['name']]['error'])
                            {
                                case 1: // UPLOAD_ERR_INI_SIZE
                                    echo"Le fichier dépasse la limite autorisée par le serveur !";
                                    break;
                                case 2: // UPLOAD_ERR_FORM_SIZE
                                    echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
                                    break;
                                case 3: // UPLOAD_ERR_PARTIAL
                                    echo "L'envoi du fichier a été interrompu pendant le transfert !";
                                    break;
                                case 4: // UPLOAD_ERR_NO_FILE
                                    echo "Le fichier que vous avez envoyé a une taille nulle !";
                                    break;
                            }
                        }
                        else 
                        {
                            // pas d'erreur d'upload
                            
                            $i = new imageObject(); // classe image du framework
                            $f = new fileObject();
                            
                            
                            
                            
                            if (isset($valuesFields['redimFilesAndSizesConfig']) && is_array($valuesFields['redimFilesAndSizesConfig']) && count($valuesFields['redimFilesAndSizesConfig'])>0)
                            {
                                foreach ($valuesFields['redimFilesAndSizesConfig'] as $indiceConfigRedims => $valueConfigRedims)
                                {    
                                    $fichierParametre = "";
                                    // on remplace le parametre par une valeur s'il y en a un 
                                    $valueReplacementBdd = ""; // valeur que va prendre la parametre a remplacer
                                    $matches = array();
                                    preg_match("/\#\#\#bddField\[(.+)\]\#\#\#/", $valueConfigRedims['nomFichierDestinationParametre'], $matches); // on detecte si il y a un parametre a remplacer par une valeur de la base de donnée ou pas
                                    if (count($matches)>0)
                                    {
                                        $req = "SELECT ".$matches[1]." FROM ".$parametres['tableName']." WHERE ".$primaryKeyField."='".$idNouvelElement."'";
                                        $res = $this->connexionBdd->requete($req);
                                        $fetchValeurCouranteBdd = mysql_fetch_assoc($res);
                                        $valueReplacementBdd = $fetchValeurCouranteBdd[$matches[1]];
                                        
                                        $fichierParametre = str_replace($matches[0], $valueReplacementBdd, $valueConfigRedims['nomFichierDestinationParametre']);
                                        
                                    }
                                    
                                    $imageType = strtolower($f->getExtensionFromFile($_FILES[$valuesFields['name']]['name']));
                                    
                                    if (file_exists($valueConfigRedims['repertoireDestination'].$fichierParametre))
                                    {
                                        // si un fichier du meme nom existe ,  on l'efface
                                        unlink($valueConfigRedims['repertoireDestination'].$fichierParametre);
                                    }
                                    
                                    if ($fichierParametre!='')
                                    {
                                        if (isset($valueConfigRedims['taille'])) // si taille = 0 ,  les dimensions sont les dimensions de l'original
                                        {
                                            $i->redimension($_FILES[$valuesFields['name']]['tmp_name'], $imageType, $valueConfigRedims['repertoireDestination'].$fichierParametre, $valueConfigRedims['taille'], array('redimOnlyIfFileSizesSuperiorToRedimValue'=>true));
                                        }
                                    }
                                    else
                                    {
                                        echo "Le fichier n'a pu être redimensionné. Erreur de parametrage dans la fonction du framework formGenerator::modifier()<br>";
                                    }
                                }
                                
                                unlink($_FILES[$valuesFields['name']]['tmp_name']);
                            
                            }
                        }
                    
                        break;
                }
            }
        }
        
        
        
        return $html;
    }
    
    
    // **************************************************************************************************************************************
    // supprime les elements selectionnés
    // **************************************************************************************************************************************
    public function supprimer($parametres=array(), $liaisonsExternes=array() )
    {
        $html="";

        // recuperation du nom du champ cle primaire
        $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
        $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
        $trouve=false;
        while(!$trouve && $fetchFields = mysql_fetch_assoc($resFieldsListe)) {
            if ($fetchFields['Key']=='PRI') {
                $primaryKeyFieldName = $fetchFields['Field'];
                $trouve = true;
            }
        }
                
        $listeIds = implode(', ', $this->variablesPost['selection']);
        
        
        $requeteSuppression = "delete from ".$parametres['tableName']." where ".$primaryKeyFieldName." in (".$listeIds.")";
        
        $this->connexionBdd->requete($requeteSuppression);
    
        return $html;
    }
    
    // cette fonction realise une suppression unique ,  avec verification des dependances si celles ci sont précisées ,  en general cette fonction est appelé a la validation d'une suppression grace a un bouton "supprimer" sur le detail d'un enregistrement
    public function supprimerFromModifForm($parametres = array(),  $liaisonsExternes = array(), $dependances = array())
    {
        $html = "";
        
        if (isset($this->variablesPost['validationFormulaireAdministration']) && $this->variablesPost['validationFormulaireAdministration']=='suppressionFromModifForm') {
            // recuperation du nom du champ cle primaire
            $reqFieldsListe = "SHOW COLUMNS FROM ".$parametres['tableName'].";";
            $resFieldsListe = $this->connexionBdd->requete($reqFieldsListe);
            $trouve=false;
            while(!$trouve && $fetchFields = mysql_fetch_assoc($resFieldsListe)) {
                if ($fetchFields['Key']=='PRI') {
                    $primaryKeyFieldName = $fetchFields['Field'];
                    $trouve = true;
                }
            }
            
            if (isset($this->variablesPost[$primaryKeyFieldName]) && $this->variablesPost[$primaryKeyFieldName]!='') {
                if (count($dependances)>0) {
                    foreach ($dependances as $indiceDependance => $dependance)
                    {
                        $req = "UPDATE ".$dependance['table']." SET ".$dependance['champLie']."='0' WHERE ".$dependance['champLie']."='".$this->variablesPost[$primaryKeyFieldName]."'";
                        $res = $this->connexionBdd->requete($req);
                        
                    }
                }
                
                $reqSuppr = "DELETE FROM ".$parametres['tableName']." WHERE ".$primaryKeyFieldName."='".$this->variablesPost[$primaryKeyFieldName]."'";
                $resSuppr = $this->connexionBdd->requete($reqSuppr);
                
                
                
            }
        }
        
        return $html;
    }
    
    // **************************************************************************************************************************************
    // afficher un formulaire a partir des données d'un tableau
    // **************************************************************************************************************************************
    public function afficherFromArray($parametres=array())
    {
        require_once __DIR__.'/../../recaptcha-php-1.11/recaptchalib.php';

        $html="";
        $t=new Template($this->cheminTemplates);
        
        
        $t->assign_vars(array('idFormObject'=>$this->identifiantObject));
        
        
        
        if (isset($parametres['onClickButtonSubmit'])) {
            $t->assign_vars(array('onClickButtonSubmit'=>$parametres['onClickButtonSubmit']));
        }
        
        $formName = "";
        if (isset($parametres['formName'])) {
            $t->assign_vars(array('formName'=>$parametres['formName']));
            $formName = $parametres['formName'];
        }
        else
        {
            $t->assign_vars(array('formName'=>'defaultFormName'));
            $formName = "defaultFormName";
        }
        
    
        if (isset($parametres['styleEntete'])) {
            $t->assign_vars(array('styleEntete'=>$parametres['styleEntete']));
        }
        
        if (isset($parametres['styleField'])) {
            $t->assign_vars(array('styleField'=>$parametres['styleField']));
        }
        
        if (isset($parametres['htmlSubmitButton'])) {
            $t->assign_vars(array('htmlSubmitButton'=>$parametres['htmlSubmitButton']));
        }
        
        if (isset($parametres['submitButtonId'])) {
            $t->assign_vars(array('submitButtonId'=>$parametres['submitButtonId']));
        }
        
        if (isset($parametres['onClickSubmitButton'])) {
            $t->assign_vars(array('onClickSubmitButton'=>$parametres['onClickSubmitButton']));
        }
        
        if (isset($parametres['codeHtmlSubmitButton'])) {
            $t->assign_vars(array('codeHtmlSubmitButton'=>$parametres['codeHtmlSubmitButton']));
        }

        if (isset($parametres['styleError'])) {
            $t->assign_vars(array('styleError'=>$parametres['styleError']));
        }
        
        if (isset($parametres['templateFileName'])) {
            $t->set_filenames(array('formulaire'=>$parametres['templateFileName']));
        }
        else
        {
            $t->set_filenames(array('formulaire'=>'formGeneric.tpl'));
        }
        
        if (isset($parametres['titrePage'])) {
            $t->assign_vars(array('titrePage'=>$parametres['titrePage']));
        }
        
        if (isset($parametres['formAction'])) {
            $t->assign_vars(array('formAction'=>$parametres['formAction']));
        }
        
        if (isset($parametres['codeHtmlBeforeSubmitButton'])) {
            $t->assign_vars(array('codeHtmlBeforeSubmitButton'=>$parametres['codeHtmlBeforeSubmitButton']));
        }
        
        if (isset($parametres['codeHtmlInFormAfterFields'])) {
            $t->assign_vars(array('codeHtmlInFormAfterFields'=>$parametres['codeHtmlInFormAfterFields']));
        }
        
        if (isset($parametres['codeHtmlInFormBeforeFields'])) {
            $t->assign_vars(array('codeHtmlInFormBeforeFields'=>$parametres['codeHtmlInFormBeforeFields']));
        }
        
        if (isset($parametres['codeHtmlAfterSubmitButton'])) {
            $t->assign_vars(array('codeHtmlAfterSubmitButton'=>$parametres['codeHtmlAfterSubmitButton']));
        }
        
        if (isset($parametres['submitButtonValue'])) {
            $t->assign_vars(array('formButtonName'=>$parametres['submitButtonValue']));
        }
        else
        {
            $t->assign_vars(array('formButtonName'=>_("Valider")));
        }
        
        if (isset($parametres['htmlCodeEnteteFields'])) // code sur le td des libelles (entetes) des champs
        {
            $t->assign_vars(array('htmlCodeEnteteFields'=>$parametres['htmlCodeEnteteFields']));
        }
        
        if (isset($parametres['htmlCodeFields'])) // code sur le td des champs
        {
            $t->assign_vars(array('htmlCodeFields'=>$parametres['htmlCodeFields']));
        }
        
        if (isset($parametres['tableHtmlCode'])) {
            $t->assign_vars(array('tableHtmlCode'=>$parametres['tableHtmlCode']));
        }
        
        if (isset($parametres['complementHTML'])) {
            $t->assign_vars(array('complementHTML'=>$parametres['complementHTML']));
        }

        if (isset($parametres['captcha'])) {
            $t->assign_block_vars('captcha', array());
            $t->assign_vars(array('captcha'=>recaptcha_get_html('6LeXTOASAAAAACl6GZmAT8QSrIj8yBrErlQozfWE')));
            if (isset($parametres['captcha-error'])) {
                $t->assign_vars(array('captcha-error'=>_('Captcha incorrect !')));
            }
        }
                
        if (isset($parametres['fields'])) {
            foreach ($parametres['fields'] as $fieldName => $proprietes) {
                if (isset($proprietes['forceValueTo'])) {
                    $value = $proprietes['forceValueTo'];
                }
                elseif (isset($this->variablesPost[$fieldName])) {
                    $value = $this->variablesPost[$fieldName];
                }
                else
                {
                    $value = $proprietes['default'];
                }
                
                if (!isset($proprietes['htmlCode2'])) {
                    $proprietes['htmlCode2']='';
                }
                
                
                if (!isset($proprietes['htmlCodeBeforeField'])) {
                    $proprietes['htmlCodeBeforeField']='';
                }

                switch($proprietes['type']) {
                    
                    case 'text':
                        $champ = $proprietes['htmlCodeBeforeField']."<input type='text' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        break;
                    case 'date':
                        $d = new dateObject();
                        $datePicker = "";
                        if (isset($proprietes['withDatePicker']) && $proprietes['withDatePicker']==true)
                        {
                            $datePicker = "<INPUT TYPE='button' name='datePick_$fieldName' value='Pick' onclick=\"".$d->getJsCallToDatePicker(array('toElementDestination'=>'document.'.$formName.'.'.$fieldName))."\">";
                        }
                        $champ = $proprietes['htmlCodeBeforeField']."<input type='text' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" ".$proprietes['htmlCode'].">$datePicker".$proprietes['htmlCode2']; // a rajouter => le date picker

                        break;
                    case 'file':
                        $champ = $proprietes['htmlCodeBeforeField']."<input type='file' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        break;
                    case 'password':
                        $champ = $proprietes['htmlCodeBeforeField']."<input type='password' name='".$fieldName."' value='".$value."' ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        break;
                    case 'email':
                        $champ = $proprietes['htmlCodeBeforeField']."<input type='text' name='".$fieldName."' id='".$fieldName."' value='".stripslashes($value)."' ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        break;
                    case 'bigText':
                        $champ = $proprietes['htmlCodeBeforeField']."<textarea name='".$fieldName."' id='".$fieldName."' ".$proprietes['htmlCode'].">".stripslashes($value)."</textarea>".$proprietes['htmlCode2'];
                        break;
                    case 'tinyMCE':
                        // utiliser la fonction tinyMCE.triggerSave(true, true) pour transferer le contenu de tinyMCE vers le champs textarea pour pouvoir recuperer le contenu en js
                        $this->tinyMCEInit(array('fieldName'=>$fieldName));
                        $f = new formGenerator();
                        $champ = $proprietes['htmlCodeBeforeField']."<div id='tinyMCE'><textarea name='".$fieldName."' id='".$fieldName."' ".$proprietes['htmlCode'].">".stripslashes($value)."</textarea></div>".$proprietes['htmlCode2'];
                        break;
                    case 'hidden':
                        $champ = "<input type='hidden' name='".$fieldName."' id='".$fieldName."' value='".stripslashes($value)."' ".$proprietes['htmlCode'].">";
                        break;
                    case 'captcha':
                        // modif par fabien le 13/04/2011 pour un captcha plus fort (on a été piraté sur le captcha à plusieurs reprise ce jour)
                        $champ=$proprietes['htmlCodeBeforeField']."<img id=\"captcha\" src=\"includes/securimage/securimage_show.php?sid=" . md5(uniqid(time())) . "\" alt=\"CAPTCHA Image\" valign='middle' />&nbsp;Recopiez les caractères de l'image<br> <a href=\"#\" onclick=\"document.getElementById('captcha').src = 'includes/securimage/securimage_show.php?sid=' + Math.random(); return false\">Recharger</a><br><input type='text' name='".$fieldName."' value=''>";
                        //echo md5(uniqid(time()))
                        break;
                    /*case 'optionsList':
                        $champs=$fieldName;
                        if (is_array($proprietes['elementList']))
                        {
                            foreach ($proprietes['elementList'] as $indice => $val)
                            {
                            
                            }
                        
                        }
                    
                        break;*/
                    case 'simpleList':
                        $champ="<select name='".$fieldName."' id='".$fieldName."'>";
                        
                        if (!isset($proprietes['noOptionAucun']) || $proprietes['noOptionAucun']==false)
                        {
                            $champ.="<option value='0'>Aucun</option>";
                        }
                        
                        if (is_array($proprietes['elementList']))
                        {
                            foreach ($proprietes['elementList'] as $indice => $val)
                            {
                                $selected='';
                                if ((isset($proprietes['valueSubmited']) && $proprietes['valueSubmited']!='0' && $proprietes['valueSubmited']==$val['value']) || ((!isset($proprietes['valueSubmited']) || $proprietes['valueSubmited']=='0' ) && isset($this->variablesPost[$fieldName]) && $this->variablesPost[$fieldName]==$val['value'] ))
                                {
                                    $selected='selected';
                                }
                                
                                $champ.="<option value=\"".$val['value']."\" ".$selected.">".$val['libelle']."</option>";
                            }
                        }
                        $champ.="</select>";
                        break;
                    case 'multipleList':
                        $champ="<select name='".$fieldName."[]' multiple>";
                        if (!isset($proprietes['noOptionAucun']) || $proprietes['noOptionAucun']!=true)
                        {
                            $champ.="<option value='0'>Aucun</option>";
                        }
                        
                        if (is_array($proprietes['elementList']))
                        {
                            foreach ($proprietes['elementList'] as $indice => $val)
                            {
                                $selected='';
                                
                                if ((isset($proprietes['valueSubmited']) && $proprietes['valueSubmited']!='0' && $proprietes['valueSubmited']==$val['value']) || ((!isset($proprietes['valueSubmited']) || $proprietes['valueSubmited']=='0' ) && isset($this->variablesPost[$fieldName]) && in_array($val['value'], $this->variablesPost[$fieldName]) ))
                                {
                                    $selected='selected';
                                }
                                
                                $champ.="<option value=\"".$val['value']."\" ".$selected.">".$val['libelle']."</option>";
                            }
                        }
                        $champ.="</select>".$proprietes['htmlCode2'];
                        break;
                    case 'checkbox':
                        $champ="";
                        if (isset($proprietes['elementList']) && is_array($proprietes['elementList']))
                        {
                            foreach ($proprietes['elementList'] as $indice => $val)
                            {
                                $selected='';
                                if ((isset($proprietes['valueSubmited']) && $proprietes['valueSubmited']!='0' && $proprietes['valueSubmited']==$val['value']) || ((!isset($proprietes['valueSubmited']) || $proprietes['valueSubmited']=='0' ) && isset($this->variablesPost[$fieldName]) && in_array($val['value'], $this->variablesPost[$fieldName]) ))
                                {
                                    $selected='checked';
                                }
                                
                                if (isset($proprietes['separator']))
                                {
                                    $separator=$proprietes['separator'];
                                }
                                else
                                {
                                    $separator="<br>";
                                }
                                
                                $champ .= "<input type='checkbox' name='".$fieldName."[]' id='".$fieldName.$indice."' value='".$val['value']."' ".$selected.">".$val['libelle'].$separator;
                                
                            }
                        }
                        else
                        {
                            $champ = "<input type='checkbox' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        }
                        break;
                    case 'singleCheckBox':
                        $checked='';
                        if (isset($proprietes['isChecked']) && ($proprietes['isChecked']==true || $proprietes['isChecked']==1))
                        {
                            $checked='checked';
                        }
                        $champ = "<input type='checkbox' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" $checked ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        break;
                    case 'radio':
                        $champ="";
                        if (isset($proprietes['elementList']) && is_array($proprietes['elementList']))
                        {
                            foreach ($proprietes['elementList'] as $indice => $val)
                            {
                                $selected='';
                                if ((isset($proprietes['valueSubmited']) && $proprietes['valueSubmited']!='0' && $proprietes['valueSubmited']==$val['value']) || ((!isset($proprietes['valueSubmited']) || $proprietes['valueSubmited']=='0' ) && isset($this->variablesPost[$fieldName]) && in_array($val['value'], $this->variablesPost[$fieldName]) ))
                                {
                                    $selected='checked';
                                }
                                
                                if (isset($proprietes['separator']))
                                {
                                    $separator=$proprietes['separator'];
                                }
                                else
                                {
                                    $separator="<br>";
                                }
                                
                                $champ .= "<input type='radio' name='".$fieldName."' id='".$fieldName.$indice."' value='".$val['value']."' ".$selected.">".$val['libelle'].$separator;
                                
                            }
                        }
                        else
                        {
                            $champ = "<input type='radio' name='".$fieldName."' id='".$fieldName."' value=\"".stripslashes($value)."\" ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        }
                    
                    
                        break;
                    case 'uploadImage':
                        $champ = "<input type='file' name='".$fieldName."' id='".$fieldName."' value=\"\" ".$proprietes['htmlCode'].">".$proprietes['htmlCode2'];
                        
                        if (isset($proprietes['physicalImagePathForTestExists']) && $proprietes['physicalImagePathForTestExists']!='')
                        {
                            if ($value!='' && file_exists($proprietes['physicalImagePathForTestExists'].$value))
                            {
                                $champ.="<img src='".$proprietes['urlImagePathForDisplayInForm'].$value."' border=0>";
                            }
                        }
                        break;
                    default:
                        $champ="";
                        echo "champ $fieldName défini partiellement,  framework::formGenerator";
                        break; 
                }
                
                if ($proprietes['type']=='hidden') {
                    $t->assign_block_vars('hiddenFields', array('field'=>$champ));
                }
                else
                {
                    $t->assign_block_vars('fields', array('name'=>$proprietes['libelle'], 'field'=>$champ, 'error'=>$proprietes['error']));
                }
            
            }
        }
        ob_start();
        $t->pparse('formulaire');
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    
    // gestion du retour d'un formulaire suivant le type de renvoi que l'on veut (ex : enregistrement dans une base de données ,  envoi de mail ...)
    public function gestionRetour($typeRetour='envoiMailAdmin', $configForm=array())
    {
        $mail = new mailObject();
        switch($typeRetour) {
            case 'envoiMailAdmin':
                $message="";
                $parametres = $configForm['fields'];
                $errors = $this->getArrayFromPost($parametres);
                if (count($errors)==0) {
                    foreach ($parametres as $fieldName => $properties)
                    {
                        $message.=$fieldName." : ".$this->variablesPost[$fieldName]."<br>";
                    }
                    
                    $from = $mail->getSiteMail();
                    $replyTo = $mail->getSiteMail();
                    if (isset($this->variablesPost['email']) && $this->variablesPost['email']!='')
                    {
                        $from = array("envoyeur"=>$mail->getSiteMail(), "replyTo"=>$this->variablesPost['email']);
                    }
                    
                                        
                    if (isset($configForm['logMails']) && $configForm['logMails']==true)
                    {
                        $mail->sendMailToAdministrators($from, 'Demande d\'un internaute sur archi-strasbourg.org', $message, '', true, true);
                    }
                    else
                    {
                        $mail->sendMailToAdministrators($from, 'Demande d\'un internaute sur archi-strasbourg.org', $message);
                    }
                    
                    
                    echo "Votre demande a été envoyée.<br>";
                }
                else
                {
                    $configForm['fields'] = $parametres;
                    $configForm['captcha-error'] = $errors['captcha-error'];
                    echo $this->afficherFromArray($configForm);
                }
                break;
        }
    }
    
    // fonction qui met a jour le tableau des champs du formulaire avec les donnees envoyées en post et qui renvoi aussi un tableau contenant la liste des champs obligatoires qui sont mal renseignés
    function getArrayFromPost(&$tableauTravail=array(),  $param = null)
    {
        global $config;
        require_once __DIR__.'/../../recaptcha-php-1.11/recaptchalib.php';
        if (empty($param))
            $param = $_POST;
        $errors=array();
        
        foreach ($tableauTravail as $name => $value) {
            switch($value['type']) {
                case 'multiple':
                    if (isset($param[$name]))
                        $tableauTravail[$name]['value']=$param[$name];
                    
                    if ($tableauTravail[$name]["required"])
                    {
                        if (!isset($param[$name]) OR count($param[$name]) < 1)
                        {
                            $errors[]=$name;
                            $tableauTravail[$name]['error']='Champ obligatoire';
                        }
                    }
                    break;
                case 'numeric':
                    if (isset($param[$name]))
                        $tableauTravail[$name]['value']=$param[$name];
                    
                    if ( $tableauTravail[$name]["required"] )
                    {
                        if ( $param[$name] != $tableauTravail[$name]["default"] AND $this->estChiffre($param[$name]))
                        {
                            // OK
                        }
                        else if ( !$this->estChiffre($param[$name]))
                        {
                            $errors[] = 'Ce champ requière une valeur numérique';
                            $tableauTravail[$name]['error'] = 'Ce champ requière une valeur numérique';
                        }
                        else
                        {
                            $errors[] = 'Valeur obligatoire';
                            $tableauTravail[$name]['error'] = 'Valeur obligatoire';
                        }
                    }
                    elseif (isset($tableauTravail[$name]) && isset($param[$name]) && $tableauTravail[$name]["required"]==false && $param[$name]!='' && !$this->estChiffre($param[$name]))
                    {
                            $errors[] = 'Ce champ requière une valeur numérique';
                            $tableauTravail[$name]['error'] = 'Ce champ requière une valeur numérique';
                    }
                    else
                    {
                        if (isset($param[$name]) && $param[$name]=='' )
                        {
                            $tableauTravail[$name]['value'] = $tableauTravail[$name]['default'];
                        }
                    }
                    break;
                case 'date':
                    if (isset($param[$name]))
                    {
                        $tableauTravail[$name]['value']=$param[$name];
                    
                        $dateSansSeparateur = preg_replace('#[^0-9/]#', '',  $param[$name]);
                        if ($tableauTravail[$name]['required']==true)
                        {
                            if (pia_strlen($param[$name]) > 0)
                            {
                                if (pia_strlen($dateSansSeparateur) == 4)
                                {
                                    $tableauTravail[$name]['value'] = '00/00/'.$dateSansSeparateur;
                                }
                                else
                                {
                                    $tabDate = explode ('/',  $param[$name]);
                                    if (count($tabDate) === 2)
                                    {
                                        $tableauTravail[$name]['value'] = '00/'.$tabDate[0].'/'.$tabDate[1];
                                    }
                                    else if (count($tabDate) === 3)
                                    {
                                        $tableauTravail[$name]['value'] = $tabDate[0].'/'.$tabDate[1].'/'.$tabDate[2];
                                    }
                                    else
                                    {
                                        $errors[] = 'date';
                                        $tableauTravail[$name]['error'] = 'Ce champ doit comprendre 8 chiffres';
                                    }

                                }
                            }
                        }
                    }
                    else
                    {
                        $tableauTravail[$name]['value']=$tableauTravail[$name]['default'];
                    }

                    break;
                case 'hidden':
                case 'bigText':
                case 'simpleList':
                case 'text':
                    if (isset($param[$name]))
                    {
                        $tableauTravail[$name]['value']=$param[$name];
                        
                        if ($tableauTravail[$name]["required"])
                        {
                            // le champs est requis
                            if ($param[$name]!=$tableauTravail[$name]["default"] && trim($param[$name])!="")
                            {
                                // rien
                            }
                            else
                            {
                                // erreur
                                $errors[]=$name;
                                $tableauTravail[$name]['error']='Champ obligatoire';
                            }
                        }
                    }
                    else
                    {
                        $tableauTravail[$name]['value']=$tableauTravail[$name]['default'];
                    }
                    break;
                case 'email':
                    if (isset($param[$name]))
                    {
                        $tableauTravail[$name]['value']=$param[$name];
                        
                        if ($tableauTravail[$name]["required"])
                        {
                            $mailObj = new mailObject();
                            // le champs est requis
                            if ($param[$name]!=$tableauTravail[$name]["default"] && trim($param[$name])!="" && $mailObj->isMail($param[$name]))
                            {
                                // rien
                            }
                            else
                            {
                                // erreur
                                $errors[]=$name;
                                $tableauTravail[$name]['error']='Champ obligatoire';
                            }
                        }
                    }
                    else
                    {
                        $tableauTravail[$name]['value']=$tableauTravail[$name]['default'];
                    }
                    break;
                case 'checkbox':
                
                    if (isset($param[$name]))
                    {
                        $tableauTravail[$name]['value']='1';
                    }
                    else
                    {
                        if ($tableauTravail[$name]['default']!='')
                        {
                            $tableauTravail[$name]['value']=$tableauTravail[$name]['default'];
                        }
                        else
                        {
                            $tableauTravail[$name]['value']='0';
                        }
                    }
                    break;
                case 'radio':
                    $tableauTravail[$name]['value']=$param[$name];
                    if ($tableauTravail[$name]["required"])
                    {
                        // le champs est requis
                        if ($param[$name]!=$tableauTravail[$name]["default"] && $param[$name]!="")
                        {
                            // rien
                        }
                        else
                        {
                            // erreur
                            $errors[]=$name;
                            $tableauTravail[$name]['error']='Champ obligatoire';
                        }
                    }
                    break;
                case 'checklist':
                    $tableauTravail[$name]['value']=$param[$name];
                    foreach ($param[$name] AS $idCourant)
                    {
                        if (!$this->estChiffre($idCourant))
                        {
                            $errors[]=$name;
                            $tableauTravail[$name]['error']= 'Mauvaise valeur !';
                        }
                    }
                    break;
                case 'password':
                    $tableauTravail[$name]['value']=$param[$name];
                    // si le champ est différent de '' c'est qu'on va modifier le mot de passe
                    // sinon on ne modifie pas
                    // erreur si les deux champs sont différents
                    
                    if ($tableauTravail[$name]['value']!="")
                    {
                        if (isset($tableauTravail[$name]['fieldToCompare'])) // comparaison avec un deuxieme champs mot de passe pour les confirmations (formulaire d'inscription...)
                        {
                            if ($tableauTravail[$name]['value']==$param[$tableauTravail[$name]['fieldToCompare']])
                            {
                                // rien
                            }
                            else
                            {
                                $errors[]=$name;
                                $tableauTravail[$name]['error']='Les mots de passe diffèrent';                            
                            }
                        }
                    }
                    else
                    {
                        if ($tableauTravail[$name]['required'])
                        {
                            $errors[]=$name;
                            $tableauTravail[$name]['error']='Mot de passe requis';
                        }
                    }
                    break;
                case 'captcha':
                    $securimage = new Securimage();
                    if ($securimage->check($param[$name]) == false) 
                    {
                        // the code was incorrect
                        // handle the error accordingly with your other error checking
                        $errors[]=$name;
                        $tableauTravail[$name]['error']='Erreur dans le code à recopier';
                    }
                
                    break;
                default:
                    $tableauTravail[$name]['error'] = 'Ce type de champ n\'existe pas !';
                    break;
            }
            
            // verification de l'existence d'un identifiant au moment de l'ajout quand il y a une liaison vers une autre table ,  cas des listes select,  multiple etc
            // si la valeur n'existe pas ,  on renvoi une erreur
            if (isset($tableauTravail[$name]['checkExist']) && $tableauTravail[$name]['error']=='' AND $tableauTravail[$name]['value'] != 0) {
                if (is_array($tableauTravail[$name]['value'])) // cas des listes a choix multiple qui renvoient un tableau
                {
                    $liste=implode("', '", $tableauTravail[$name]['value']);
                    $sql = "SELECT DISTINCT ".$tableauTravail[$name]['checkExist']['primaryKey']." 
                        FROM ".$tableauTravail[$name]['checkExist']['table']." 
                        WHERE ".$tableauTravail[$name]['checkExist']['primaryKey']." IN ('".$liste."')";
                    
                    $resCheck=$this->connexionBdd->requete($sql);
                    if (mysql_num_rows($resCheck) != count($tableauTravail[$name]['value']))
                    {
                        $tableauTravail[$name]['error']="Un élément a été supprimé de la base de données";
                    }
                }
                else
                {
                    $sql = "SELECT DISTINCT ".$tableauTravail[$name]['checkExist']['primaryKey']." 
                        FROM ".$tableauTravail[$name]['checkExist']['table']." 
                        WHERE ".$tableauTravail[$name]['checkExist']['primaryKey']."='".$tableauTravail[$name]['value']."' LIMIT 1";
                    
                    $resCheck=$this->connexionBdd->requete($sql);
                    if (mysql_num_rows($resCheck)==0)
                    {
                        $tableauTravail[$name]['error']="Cet élément a été supprimé de la base de données";
                    }
                }
            }
        }
    if (isset($param["recaptcha_challenge_field"])) {
    $resp = recaptcha_check_answer(
            $config->captchakey,
            $_SERVER["REMOTE_ADDR"],
            $param["recaptcha_challenge_field"],
            $param["recaptcha_response_field"]
        );
        if (!$resp->is_valid) {
            $errors['captcha-error']=$resp->error;
        }
        }
        return $errors;
    }
    
    
    // fonction permettant d'afficher une liste avec les champs choisis pour chaque colonne 
    // dans params on peut préciser chemin vers les images si on a un champs de type image
    // exemple de tableau en entree :
    // $tabListe[]=array(
    //                        'Suppr'=>array('value'=>$fetchGetCommentairesPhotos['idPhoto'], 'type'=>'checkbox', 'name'=>'suppr[]', 'checked'=>false), 
    //                        'Photo'=>array('value'=>$fetchGetCommentairesPhotos['nomFichier'], 'type'=>'image', 'name'=>'', "style"=>"width:200px"), 
    //                        'Nom de fichier'=>array('value'=>$fetchGetCommentairesPhotos['nomFichier'], 'type'=>'libelle'), 
    //                        'Commentaire'=>array('value'=>$fetchGetCommentairesPhotos['commentaire'], 'type'=>'bigText', 'name'=>'filename_'.$fetchGetCommentairesPhotos['idPhoto'])
    public function createFormListeFromArray($listeArray=array(), $params=array())
    {
        $html="";
        if (count($listeArray)>0) {
            $html.="<table>";
            if (isset($params['styleEntete']) && $params['styleEntete']!='')
                $html.="<tr style=\"".$params['styleEntete']."\">";
            else
                $html.="<tr>";
            
            foreach ($listeArray[0] as $identifiantColonne => $val) {
                    $html.="<td>".$identifiantColonne."</td>";
            }
            
            $html.="</tr>";
            
            foreach ($listeArray as $identifiant=>$valeur) {
                $html.="<tr>";
                foreach ($valeur as $nomColonne => $configColonne) {
                    switch($configColonne['type'])
                    {
                        case 'image':
                            $html.="<td><img style=\"".$configColonne['style']."\" src='".$params['cheminImages']."/".$configColonne['value']."'></td>";
                            break;
                        case 'libelle':
                            $html.="<td>".$configColonne['value']."</td>";
                            break;
                        case 'bigText':
                            $bigText = "<textarea name='".$configColonne['name']."' style=\"".$configColonne['style']."\">".$configColonne['value']."</textarea>";
                            $html.="<td>".$bigText."</td>";
                            break;
                        case 'checkbox':
                            $html.="<td><input type='checkbox' name='".$configColonne['name']."' value='".$configColonne['value']."' ";
                            if (isset($configColonne['checked']) && $configColonne['checked']==true)
                            {
                                $html.="CHECKED";
                            }
                            $html.="></td>";
                            break;
                        case 'free':
                            $html.="<td>".$configColonne['value']."</td>";
                            break;
                    }
                }
                $html.="<tr>";
            }
        
            $html.="</table>";
        }    
        return $html;
    }
    
    
    // ***************************************************************************************************************
    // table MYSQL pour la gestion des formulaires multipage :
    //
    //CREATE TABLE IF NOT EXISTS `sessionsFormulairesMultipages` (
    //`idEntree` int(11) NOT NULL auto_increment, 
    //`sessionId` varchar(255) NOT NULL, 
    //`elementId` varchar(100) NOT NULL, 
    //`value` varchar(255) NOT NULL, 
    //PRIMARY KEY  (`idEntree`)
    //) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    // ***************************************************************************************************************
    public function addElementFormulaireMultiPage($params=array())
    {
        $this->elementsFormulaireMultiPage[count($this->elementsFormulaireMultiPage)+1]=$params;
    }
    
    
    // le formulaire multipage permet de creer un formulaire ,  on sauvegarde les valeurs dans la base de donnée avec la session comme identifiant
    public function getPageFormulaireMultiPage($params=array())
    {
        $numPage=1;
        if (isset($params['numPage']))
            $numPage = $params['numPage'];
            
        $elementsPageCourante = $this->elementsFormulaireMultiPage[$numPage];
        
        return $this->afficherFromArray($elementsPageCourante['configForm']);
    }
    
    // ***************************************************************************************************************
    // affichage des boutons BBCode
    // ***************************************************************************************************************
    /*private function getBBCodeJSMiseEnForme($nomChamp="", $nomForm ="")
    {
        if ($nomForm == '') {
            $nomForm = 'formModification';
        }
        return "
        <input type=\"button\" value=\"b\" style=\"width:50px;font-weight:bold\" onclick=\"bbcode_ajout_balise('b',  '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>
        <input type=\"button\" value=\"i\" style=\"width:50px;font-style:italic\" onclick=\"bbcode_ajout_balise('i',  '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>
        <input type=\"button\" value=\"u\" style=\"width:50px;text-decoration:underline;\" onclick=\"bbcode_ajout_balise('u',  '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>
        <input type=\"button\" value=\"quote\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('quote',  '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>
        <!--<input type=\"button\" value=\"code\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('code',  'formModification',  'description');bbcode_keyup(this, 'apercu');\"/>-->
        <input type=\"button\" value=\"url interne\"  style=\"width:75px\" onclick=\"bbcode_ajout_balise('url',   '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>
        <input type=\"button\" value=\"url externe\"  style=\"width:80px\" onclick=\"bbcode_ajout_balise('urlExterne',   '".$nomForm."',  '".$nomChamp."');bbcode_keyup(this, 'apercu');\"/>";
    }*/
    
    /**
     * ?
     * 
     * @param string $nomChamp ?
     * @param string $nomForm  ?
     * 
     * @return array
     * */
    private function getBBCodeJSMiseEnForme($nomChamp = '',  $nomForm = '')
    {
        $bbCode = new bbCodeObject();
        
        $arrayRetour = $bbCode->getBoutonsMiseEnFormeTextArea(array('formName'=>$nomForm, 'fieldName'=>$nomChamp, 'idDivPrevisualisation'=>'previsualisationSource'));
        
        
        return $arrayRetour;
        
    }
    
    
    /**
     * Affichage d'un formulaire multiligne et dynamique ( pas de rechargement de page)
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getFormulaireMultiLignes($params=array())
    {
        $html="";
        $t = new tableau();
        
        $identifiantUnique=0;
        if (isset($params['identifiantUniqueFormulaire']))
            $identifiantUnique=$params['identifiantUniqueFormulaire'];


        $formDebut="<form name='frmWork_formMultiLignes$identifiantUnique' id='frmWork_formMultiLignes$identifiantUnique' enctype='multipart/form-data' method='POST' action='".$params['formAction']."'>";
        $formFin = "</form>";
        
        //$boutonMoins="<input type='button' name='frmWork_Button_Moins$identifiantUnique' value='-' onclick=\"delRow$identifiantUnique('frmWork_Tableau$identifiantUnique');\">";
        $boutonPlus="<input type='button' name='frmWork_Button_Plus$identifiantUnique' value='+' onclick=\"addRow$identifiantUnique('frmWork_Tableau$identifiantUnique');\">";
        
        $boutonSubmit = "<input type='submit' value='"._("Valider")."'>";
        // entetes du tableau
        $styleEntete="";
        if (isset($params['htmlEnteteTableau'])) {
            $styleEntete = $params['htmlEnteteTableau'];
        }
        
        if (isset($params['configForm'])) {
            $t->addValue('suppr', $styleEntete); // premiere colonnes entete des cases a cocher de suppression
            $nbColonnesTableau=0;
            foreach ($params['configForm'] as $nomChamps => $properties) {
                $nbColonnesTableau++;
                
                if ($properties['type']=='hidden') {
                    $styleHidden = "style='display:none;'";
                    $t->addValue($properties['libelle'], $styleHidden);
                } else {                
                    $t->addValue($properties['libelle'], $styleEntete);
                }
            }
        }
        
        $nbColonnesTableau++; // pour l'affichage de la colonne contenant le bouton de suppression ou la case a cocher
        
        $d = new dateObject();
        
        $js = "
        
        <script  >
          
          var nbElementsFrmWorkFormMultiLigne$identifiantUnique = 0;
            
          function delRow$identifiantUnique(id, element)
          {
            var tbody = document.getElementById(id).getElementsByTagName('TBODY')[0];
            tbody.removeChild(element);
          }
            
          
          function addRow$identifiantUnique(id)
          {
            var tbody = document.getElementById(id).getElementsByTagName('TBODY')[0];
            var row = document.createElement('TR');

            // la premiere colonne d'affichage de la case a cocher
            var td = document.createElement('TD');
            
            
            ";
        if (isset($params['alternanceStyleLignes'])) {
            $js.="
            
            moduloStyle = nbElementsFrmWorkFormMultiLigne$identifiantUnique%".count($params['alternanceStyleLignes']).";
            if (moduloStyle==0) {                
                td.className='".$params['alternanceStyleLignes'][0]."';
            }
            else
            {
                td.className='".$params['alternanceStyleLignes'][1]."';
            }
            
            
            ";
        }
        
        $js.="
        
        td.innerHTML=\"<input type='checkbox' name='frmWork_checkBoxSuppr$identifiantUnique' onclick=\\\"delRow$identifiantUnique('frmWork_Tableau$identifiantUnique', this.parentNode.parentNode)\\\" value='\"+nbElementsFrmWorkFormMultiLigne$identifiantUnique+\"'>\";
        row.appendChild(td);
        ";
        
        $i=0;
        foreach ($params['configForm'] as $nomChamps => $properties) {
            $defaultValuesTab[$nomChamps] = $properties['defaultValues'];
            
            if ($i==0)
                $nbLignes = count($properties['defaultValues']);
            
            $js.="
                var td = document.createElement('TD');
                td.innerHTML=\"".$this->getFormElementByType(array('identifiantUnique'=>$identifiantUnique, 'type'=>$properties['type'], "displayMode"=>"multiLignes", 'name'=>$nomChamps."_"))."\";
                row.appendChild(td);
            ";
            
            
            if ($properties['type']=='hidden') {
                $js.= "td.style.display='none';";
            } else {
                $js.="td.style.display='table-cell';";
            }
            
            
            $i++;
        }
        
        $js.="   
            tbody.appendChild(row);
            nbElementsFrmWorkFormMultiLigne$identifiantUnique++;
        }
        ";
        
        $js.="</script>";
        
        
        // gestion des valeurs par défaut - ou valeurs recuperée du POST par exemple
        $jsAfterDisplay="";
        
        if (isset($defaultValuesTab)) {
            for ($i=0 ; $i<$nbLignes ; $i++) {
                $jsAfterDisplay.="addRow$identifiantUnique('frmWork_Tableau$identifiantUnique');";
                foreach ($defaultValuesTab as $nomChamp => $values) {
                    $value = $defaultValuesTab[$nomChamp][$i];
                    $value = str_replace(array("\r\n"), "###RETOURLIGNE###", ($value));
                    $value = str_replace(array("\n\r"), "###RETOURLIGNE###", ($value));
                    $value = str_replace(array("\n"), "###RETOURLIGNE###", ($value));
                    $value = str_replace(array("\r"), "###RETOURLIGNE###", ($value));
                    $value = str_replace("###RETOURLIGNE###", "\\n", $value);
                    $jsAfterDisplay.="document.getElementById('".$nomChamp."_".$i."').value=\"".$value."\";";
                }
            }
        }
        
        return array('html'=>$formDebut.$js.$t->createHtmlTableFromArray($nbColonnesTableau, "border:0px solid #000000;", "", "", "id='frmWork_Tableau$identifiantUnique' name='frmWork_Tableau$identifiantUnique'").$boutonPlus.$boutonSubmit.$formFin, "jsAfterMultiLigneArray"=>"<script  >".$jsAfterDisplay."</script>");
    }
    
    
    /**
     * Renvoi les champs,  si mutililigne,  le nom du champ prend en compte le nombre d'elements affichés
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getFormElementByType($params=array())
    {
        $retour="";
        switch($params['type']) {
        case "date":
            if (isset($params['displayMode']) && $params['displayMode']=='multiLignes') {
                $htmlMultiLigneIdUniqueChamp = "\"+nbElementsFrmWorkFormMultiLigne".$params['identifiantUnique']."+\"";
            }
            $retour = "<input type='text' name='".$params['name']."$htmlMultiLigneIdUniqueChamp' id='".$params['name']."$htmlMultiLigneIdUniqueChamp' value=''>";
            break;
        case "text":
            if (isset($params['displayMode']) && $params['displayMode']=='multiLignes') {
                $htmlMultiLigneIdUniqueChamp = "\"+nbElementsFrmWorkFormMultiLigne".$params['identifiantUnique']."+\"";
            }
            $retour = "<input type='text' name='".$params['name']."$htmlMultiLigneIdUniqueChamp' id='".$params['name']."$htmlMultiLigneIdUniqueChamp' value=''>";
            break;
        case 'bigText':
            if (isset($params['displayMode']) && $params['displayMode']=='multiLignes') {
                $htmlMultiLigneIdUniqueChamp = "\"+nbElementsFrmWorkFormMultiLigne".$params['identifiantUnique']."+\"";
            }
            $retour = "<textarea name='".$params['name']."$htmlMultiLigneIdUniqueChamp' id='".$params['name']."$htmlMultiLigneIdUniqueChamp'></textarea>";
            break;
        case "hidden":
            if (isset($params['displayMode']) && $params['displayMode']=='multiLignes') {
                $htmlMultiLigneIdUniqueChamp = "\"+nbElementsFrmWorkFormMultiLigne".$params['identifiantUnique']."+\"";
            }
            $retour = "<input width=0 type='hidden' name='".$params['name']."$htmlMultiLigneIdUniqueChamp' id='".$params['name']."$htmlMultiLigneIdUniqueChamp' value=''>";
            break;
            
        }
        
        return $retour;
    }
    
    /**
     * Cette fonction permet de supprimer un element (option) cliqué dans une liste <select>
     * La fonction JavaScript prend l'id de l'element select en paramètre
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getJsFunctionSuppressionOptionSelect($params = array())
    {
        $html = "";
        if (!isset($params['noJsScriptBalises']) || $params['noJsScriptBalises']==false) {
            $html .= "<script  >";
        }
        
        $html.="
        function supprimeOptionSelect(elementId) {
            if (confirm(\"Etes vous sûr de vouloir retirer cet élément de la liste ?\")) {
                indexSuppr = document.getElementById(elementId).selectedIndex;
                

                optBackup = Array();
                optBackup['values'] = Array();
                optBackup['txt'] = Array();
                
                options = document.getElementById(elementId).options;
                
                i=0;
                indexCourant = 0;
                while(i<options.length) {
                    if (options[i].index!=indexSuppr)
                    {
                        optBackup['values'][indexCourant]= options[options[i].index].value;
                        optBackup['txt'][indexCourant] = options[options[i].index].text;
                        indexCourant++;
                    }
                    i++;
                }
                
                
                document.getElementById(elementId).options.length=0;
                for (i2 = 0 ; i2 < optBackup['values'].length; i2++) {
                    newOpt = document.createElement('OPTION');
                    newOpt.setAttribute('value', optBackup['values'][i2]);
                    
                    txt = document.createTextNode(optBackup['txt'][i2]);
                    newOpt.appendChild(txt);
                    document.getElementById(elementId).appendChild(newOpt);
                }
            
            }
        }
        ";
        
        if (!isset($params['noJsScriptBalises']) || $params['noJsScriptBalises']==false) {
            $html.="</script>";
        }
        
        return $html;
    }
        
    /**
     * Cette fonction permet de selectionner tous les elements d'une liste select => pour la recuperation des valeurs en POST,  il faut que les elements soient selectionnes
     * La fonction javascript prend l'id de l'element select en parametre
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getJsFunctionSelectAllOptions($params = array())
    {
        $html = "";
        if (!isset($params['noJsScriptBalises']) || $params['noJsScriptBalises']==false) {
            $html .= "<script  >";
        }
        
        $html.="
        function selectAllOptionSelect(elementId) {
            e = document.getElementById(elementId);
            
            for (i in e.options) {
                e.options[i].selected=true;
            }
        }
        ";
        
        if (!isset($params['noJsScriptBalises']) || $params['noJsScriptBalises']==false) {
            $html.="</script>";
        }
        
        return $html;
    }
    
}

?>
