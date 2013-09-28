<?php
/**
 * Classe ArchiContenu
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 
/**
 * Gère les fonctions communes à tous les types de contenu
 * (adresses,  personnes,  etc)
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
abstract class ArchiContenu extends config
{
    
    
    /**
     * Affichage du formulaire d'ajout d'un nouveau dossier (groupe d'adresse + evenement construction)
     * 
     * @param array  $parametres Paramètres
     * @param string $type       Type (adresse ou personne)
     * 
     * @return void
     * */
    public function afficheFormulaireNouveauDossier($parametres=array(),  $type="adresse")
    {
        $html="";
        
        // initialisation de l'objet googlemap pour la recuperation des coordonnees
        $paramsGoogleMap = array('googleMapKey'=>$this->googleMapKey);

        $googleMap = new googleMap($paramsGoogleMap);

        $html.= $googleMap->getJsFunctions();
        $html.= $googleMap->getJSInitGeoCoder();

        
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('nouveauDossier'=>'nouveauDossier.tpl')));
        
        
        if ($type=="personne") {
            $formAction="ajoutNouvelPersonne";
            $t->assign_block_vars("ajoutPersonne",  array());
            $resJobs=$this->connexionBdd->requete("SELECT * FROM `metier`");
            $jobList="";
            while ($job = mysql_fetch_assoc($resJobs)) {
                if (!empty($job["nom"])) {
                    $jobList.="<option value='".$job["idMetier"]."'";
                    if (isset($_POST["metier"]) && $job["idMetier"]==$_POST["metier"]) {
                        $jobList.=" selected='selected' ";
                    }
                    $jobList.=">".$job["nom"]."</option>";
                }
            }
            $t->assign_vars(
                array(
                    'titrePage'=>_("Ajout d'une nouvelle personne (physique ou morale)"), 
                    "jobList"=>$jobList,
                    "typeBoutonValidation"=>"submit"
                )
            );   
            
            if (!empty($_POST) && !isset($_POST["archiLogin"])) {   
                $t->assign_vars(
                    array(
                        "firstname"=>$_POST["prenom"],
                        "name"=>$_POST["nom"],
                        "birth"=>$_POST["dateNaissance"],
                        "death"=>$_POST["dateDeces"],
                        "desc"=>$_POST["descriptionPerson"]
                    )
                );
            }
        } else {
            $formAction="ajoutNouveauDossier";
            if (!isset($arrayJsCoordonneesFromGoogleMap['jsFunctionCall'])) {
                $arrayJsCoordonneesFromGoogleMap['jsFunctionCall']=0;
            }
            // assignation du titre de la page
            $t->assign_vars(
                array(
                    'titrePage'=>_("Ajout d'une nouvelle adresse"), 
                    'typeBoutonValidation'=>"submit"
                )
            );
            // ********
            // on affiche la partie ajout d'une adresse 
            $t->assign_block_vars('isNotAjoutSousEvenement',  array());
            // ********
        }
        
        // ********
        // on affiche la partie "ajout d'un evenement"    
        $t->assign_block_vars('afficheAjoutEvenement',  array());
        // *******
        
        // ******
        // si la personne n'est pas admin elle verra une version simplifiée du formulaire
        $authentification = new archiAuthentification();
        $u = new ArchiUtilisateur();
        if ($authentification->estConnecte() && ($authentification->estAdmin() || $u->canAddWithoutStreet(array('idUtilisateur'=>$authentification->getIdUtilisateur())))) {
            $t->assign_block_vars('afficheAjoutEvenement.isAdmin', array());
            $t->assign_vars(array("displayQuartiers"=>'table-row'));
            $t->assign_vars(array("displaySousQuartiers"=>'table-row'));            
        } else {
            $t->assign_block_vars('afficheAjoutEvenement.isNotAdmin', array());
            $t->assign_vars(array("displayQuartiers"=>'none'));
            $t->assign_vars(array("displaySousQuartiers"=>'none'));
        }
        // ******
        
        $typeStructure=0;
        if (isset($this->variablesPost['typeStructure']) && $this->variablesPost['typeStructure']!='')
            $typeStructure = $this->variablesPost['typeStructure'];
        
        
        $groupeTypeEvenement=2; // par defaut on selectionne les evenement de type 'travaux'
        if (isset($this->variablesPost['typeGroupeEvenement']) && $this->variablesPost['typeGroupeEvenement']!='')
            $groupeTypeEvenement = $this->variablesPost['typeGroupeEvenement'];
        
        
        
        $typeEvenement=0;
        if (isset($this->variablesPost['typeEvenement']) && $this->variablesPost['typeEvenement']!='')
            $typeEvenement = $this->variablesPost['typeEvenement'];


        if (!(isset($this->variablesPost['typeEvenement']) && $this->variablesPost['typeEvenement']!='') && isset($this->variablesGet['archiOptionAjoutDossier']) && $this->variablesGet["archiOptionAjoutDossier"]=="nouvelleDemolition") {
            $groupeTypeEvenement=2; // travaux
            $typeEvenement = 6;
        }

        if (!(isset($this->variablesPost['typeEvenement']) && $this->variablesPost['typeEvenement']!='') && isset($this->variablesGet['archiOptionAjoutDossier']) && $this->variablesGet["archiOptionAjoutDossier"]=="nouvelEvenementCulturel") {
            $groupeTypeEvenement=1; // travaux
            $typeEvenement = 0;
        }

            
        $ISMH=false;
        if (isset($this->variablesPost['ISMH']))
            $ISMH=true;
    
        $MH=false;
        if (isset($this->variablesPost['MH']))
            $MH=true;
        
        $isDateDebutEnviron = false;
        if (isset($this->variablesPost['isDateDebutEnviron']))
            $isDateDebutEnviron = true;

        $personnes = array();
        if (isset($this->variablesPost['personnes']) && count($this->variablesPost['personnes'])>0)    
            $personnes = $this->variablesPost['personnes'];
        
        $ville = 0;
        if (isset($this->variablesPost['ville']) && $this->variablesPost['ville']!='0' && $this->variablesPost['ville']!='')
            $ville = $this->variablesPost['ville'];
        
        // ***********************************************************************************
        // recuperation des valeurs des champs textes du formulaire validé,  les autres champs sont mis en place individuellement
        $listeChamps = array('titre', 'source', 'sourcetxt', 'dateDebut', 'dateFin', 'nbEtages', 'description', 'ville', 'villetxt');
        
        foreach ($listeChamps as $indice => $fieldName) {
            if (isset($this->variablesPost[$fieldName]) && $this->variablesPost[$fieldName]!='') {
                $t->assign_vars(array($fieldName=>$this->variablesPost[$fieldName]));
            }
        }
                
        // gestion du favori de la ville ou si on a une ville generale courante
        if ($ville=='0' && !isset($this->variablesGet['archiIdVilleGeneral'])) {
            $reqVilleTxt = "select nom from ville where idVille = '".$this->session->getFromSession('idVilleFavoris')."'";
            $resVilleTxt = $this->connexionBdd->requete($reqVilleTxt);
            $fetchVilleTxt = mysql_fetch_assoc($resVilleTxt);
            $t->assign_vars(array('ville'=>$this->session->getFromSession('idVilleFavoris'), 'villetxt'=>$fetchVilleTxt['nom']));
            $ville = $this->session->getFromSession('idVilleFavoris');
        } elseif (isset($this->variablesGet['archiIdVilleGeneral'])) {
            $reqVilleTxt = "select nom from ville where idVille = '".$this->variablesGet['archiIdVilleGeneral']."'";
            $resVilleTxt = $this->connexionBdd->requete($reqVilleTxt);
            $fetchVilleTxt = mysql_fetch_assoc($resVilleTxt);
            $t->assign_vars(array('ville'=>$this->variablesGet['archiIdVilleGeneral'], 'villetxt'=>$fetchVilleTxt['nom']));
            $ville = $this->variablesGet['archiIdVilleGeneral'];
        }

        // ***********************************************************************************
        // si un idVille existe sur le formulaire ,  on affiche les quartiers correspondants
        if ($ville!=0) {
            $resQuartiers = $this->connexionBdd->requete("select idQuartier,  nom from quartier where idVille = '".$ville."' order by nom");
            while ($fetchQuartiers = mysql_fetch_assoc($resQuartiers)) {
                $selected = "";

                if (isset($this->variablesPost['quartiers']) && $this->variablesPost['quartiers']!='0' && $fetchQuartiers['idQuartier']==$this->variablesPost['quartiers']) {    
                    $selected=" selected";
                }
                
                if ($fetchQuartiers['nom']!='autre') {
                    if ($type=="personne") {

                    } else {
                        $t->assign_block_vars(
                            "isNotAjoutSousEvenement.quartiers",
                            array(
                                'id'        =>    $fetchQuartiers['idQuartier'], 
                                'nom'        =>    $fetchQuartiers['nom'], 
                                'selected'    =>    $selected
                            )
                        );
                    }
                }
            }
        }
        
        // ***********************************************************************************
        // si on a des personnes selectionnees ,  on les ajoute
        
        $d = new droitsObject();
        $u = new archiUtilisateur();
        
        if ($d->isAuthorized('personne_sur_evenement_ajouter', $u->getIdProfilFromUtilisateur($authentification->getIdUtilisateur()))) {
            $t->assign_vars(array("affichePersonnesBlock"=>"table-row"));
        } else {
            $t->assign_vars(array("affichePersonnesBlock"=>"none"));
        }
        
        if ($d->isAuthorized('affiche_selection_source', $u->getIdProfilFromUtilisateur($authentification->getIdUtilisateur()))) {
            $t->assign_block_vars('afficheAjoutEvenement.isDisplaySource', array());
        } else {
            $t->assign_block_vars('afficheAjoutEvenement.isNotDisplaySource', array());
        }
        
        
                
        if (count($personnes)>0) {
            $listePersonnes = implode("', '", $personnes);
            
            $reqPersonnes = "select idPersonne,  nom,  prenom from personne where idPersonne in ('".$listePersonnes."')";
            $resPersonnes = $this->connexionBdd->requete($reqPersonnes);
            while ($fetchPersonnes = mysql_fetch_assoc($resPersonnes)) {
                $t->assign_block_vars('personnes', array('id'=>$fetchPersonnes['idPersonne'], 'nom'=>$fetchPersonnes['nom'].' '.$fetchPersonnes['prenom'], 'selected'=>" selected"));
            }
        }
        
        // ***********************************************************************************
        // si un idQuartier existe sur le formulaire on affiche les sous quartier correspondants
        if (isset($this->variablesPost['quartiers']) && $this->variablesPost['quartiers']!='') {
            $resSousQuartiers = $this->connexionBdd->requete("select idSousQuartier,  nom from sousQuartier where idQuartier = '".$this->variablesPost['quartiers']."' order by nom");
        
            while ($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers)) {
                $selected = "";

                if (isset($this->variablesPost['sousQuartiers']) && $this->variablesPost['sousQuartiers']!='0' && $fetchSousQuartiers['idSousQuartier']==$this->variablesPost['sousQuartiers']) {    
                    $selected=" selected";
                }
                
                if ($fetchSousQuartiers['nom']!='autre') {
                    if ($type=="personne") {

                    } else {
                        $t->assign_block_vars(
                            "isNotAjoutSousEvenement.sousQuartiers",
                            array(
                                'id'        =>    $fetchSousQuartiers['idSousQuartier'], 
                                'nom'        =>    $fetchSousQuartiers['nom'], 
                                'selected'    =>    $selected
                            )
                        );
                    }
                }
            }
        }

        
        $numLigne=0;
        if (isset($this->variablesPost['idUnique'])) {
            foreach ($this->variablesPost['idUnique'] as $indice =>$valueIdUnique) {
                if ((isset($this->variablesGet['supprAdresse']) && $this->variablesGet['supprAdresse']==$valueIdUnique)) {
                    //
                } else {
                    $arrayAdresse[$numLigne]['idAdresse']    =0;
                    $arrayAdresse[$numLigne]['txt']         = $this->variablesPost['ruetxt'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['id']          = $this->variablesPost['rue'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['numero']      = $this->variablesPost['numero'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['indicatif']   = $this->variablesPost['indicatif'][$valueIdUnique];
                    $numLigne++;
                }
            }
            
            if (isset($this->variablesPost['ajouterAdresse'])) {
                $arrayAdresse[$numLigne]['idAdresse']    =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']     = "";
                $numLigne++;
            }
            
            if (count($this->variablesPost['idUnique'])==1 && isset($this->variablesPost['enleverAdresse'])) {
                $arrayAdresse[$numLigne]['idAdresse']    =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']     = "";
                $numLigne++;
            }
        } else {
                $arrayAdresse[$numLigne]['idAdresse']    =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']     = "";
                $numLigne++;
        }
        
        
        
        $configArrayRetrieveCoordonneesGoogleMap = array();
        
        for ($i=0; $i<$numLigne; $i++) {
            
            // affichage des indicatifs pour chaque adresse
            if ($type=="personne") {

            } else {
                $t->assign_block_vars(
                    "isNotAjoutSousEvenement.adresses",
                    array(
                        'idUnique'                    => $i, 
                        
                        'onClickBoutonChoixRue'     => "document.getElementById('paramChampAppelantRue').value= 'rue".$i."';document.getElementById('iFrameRue').src='".$this->creerUrl('', 'afficheChoixRue', array('noHeaderNoFooter'=>1))."&archiIdVille='+document.getElementById('ville').value+'&archiIdQuartier='+document.getElementById('quartiers').value+'&archiIdSousQuartier='+document.getElementById('sousQuartiers').value;document.getElementById('calqueRue').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueRue').style.display='block';", 
                        
                        "nomRue"                => $arrayAdresse[$i]["txt"], 
                        "rue"                    => $arrayAdresse[$i]["id"], 
                        "numero"                => $arrayAdresse[$i]["numero"], 
                        "onClickBoutonSupprAdresse"    => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('', 'ajoutNouveauDossier', array('supprAdresse'=>$i))."'"
                    )
                );
            }
            
            // gestion des indicatifs de chaque adresse
            $reqIndicatif = "select idIndicatif,  nom from indicatif";
            $resIndicatif = $this->connexionBdd->requete($reqIndicatif);
            while ($fetchIndicatif = mysql_fetch_assoc($resIndicatif)) {
                $selected="";
                if (isset($this->variablesPost['indicatif'.$i]) && $this->variablesPost['indicatif'.$i]!='' && $this->variablesPost['indicatif'.$i]==$fetchIndicatif['idIndicatif'])
                    $selected = " selected";
                if ($type=="personne") {

                } else {
                    $t->assign_block_vars(
                        "isNotAjoutSousEvenement.adresses.indicatifs",
                        array(
                            "id"        =>    $fetchIndicatif['idIndicatif'], 
                            "nom"        =>    $fetchIndicatif['nom'], 
                            "selected"    =>    $selected
                        )
                    );
                }
            
            }
            
            $configArrayRetrieveCoordonneesGoogleMap[$i] = array(
                                'nomChampLatitudeRetour'=>'latitude_'.$i, 
                                'nomChampLongitudeRetour'=>'longitude_'.$i, 
                                'getAdresseFromElementById'=>true, 
                                'jsAdresseValue'=>"document.getElementById('numero".$i."').value+' '+document.getElementById('rue".$i."txt').value+' '+document.getElementById('villetxt').value", 
                                'jsToExecuteIfNoAddressFound'=>"document.getElementById('latitude_".$i."').value='';document.getElementById('longitude_".$i."').value='';"
            );
        
        }
        
        

        
        
        $jsToExecute="document.getElementById('formAjoutDossier').action='".$this->creerUrl('ajoutNouveauDossier', '')."';testAdresseValideAndSubmit('formAjoutDossier');";
        $arrayJsCoordonneesFromGoogleMap = $googleMap->getJSMultipleRetriveCoordonnees(array('jsToExecuteIfOK'=>$jsToExecute), $configArrayRetrieveCoordonneesGoogleMap);
        $html.= $arrayJsCoordonneesFromGoogleMap['jsFunctionToExecute'];
        
        
        // **************************************************************************************
        // ***********************************************************************************
        // liste des courants architecturaux
        $resCourants =$this->connexionBdd->requete("select idCourantArchitectural, nom from courantArchitectural order by nom");
        $tableauHtml = new tableau();
        
        while ($fetchCourants = mysql_fetch_assoc($resCourants)) {
            $checked ="";
            if (isset($this->variablesPost["courantArchitectural"]) && in_array($fetchCourants["idCourantArchitectural"], $this->variablesPost["courantArchitectural"])) {
                $checked = " checked";
            }
            
            $tableauHtml->addValue("<input type='checkbox' name='courantArchitectural[]' value='".$fetchCourants["idCourantArchitectural"] ."' ".$checked.">&nbsp;".$fetchCourants['nom']); 
        }
        
        $t->assign_vars(array('listeCourantsArchitecturaux'=>$tableauHtml->createHtmlTableFromArray(3, 'white-space:nowrap;border:1px solid #000000;', 'listeCourantsArchitecturaux')));
        if ($type=="adresse") {
            $t->assign_block_vars("afficheAjoutEvenement.isAddress", array());
            // ***********************************************************************************
            // les des types de structures
            $resTypeStructure=$this->connexionBdd->requete("SELECT idTypeStructure,  nom FROM typeStructure order by nom");
            while ($fetchTypeStructure = mysql_fetch_assoc($resTypeStructure)) {
                $selected="";
                if ($typeStructure!='' && $typeStructure==$fetchTypeStructure["idTypeStructure"] || ($typeStructure=="" && $fetchTypeStructure["idTypeStructure"]==$this->getIdTypeStructureImmeuble())) {
                    $selected=" selected";
                }
                if ($fetchTypeStructure["idTypeStructure"]>0) {
                    $t->assign_block_vars(
                        'afficheAjoutEvenement.isAddress.typesStructure',
                        array(
                            'id'=>$fetchTypeStructure["idTypeStructure"], 
                            'nom'=>$fetchTypeStructure["nom"], 
                            'selected'=>$selected
                        )
                    );
                }
            }
        }
        
        
        // ***********************************************************************************
        // le type de groupe d'evenement
        // 1 - culturel
        // 2 - travaux
        if ($groupeTypeEvenement=='2') {
             $t->assign_vars(array('checkedTypeEvenement2'=>" checked"));
             $t->assign_vars(array('styleChampsSupplementaireCulturel'=>"display:none;", 'styleChampsSupplementaireTravaux'=>"display:block;"));
             
        } elseif ($groupeTypeEvenement=='1') {
             $t->assign_vars(array('checkedTypeEvenement1'=>" checked"));
             $t->assign_vars(array('styleChampsSupplementaireCulturel'=>"display:table-row;", 'styleChampsSupplementaireTravaux'=>"display:none;"));
        }
        
        $t->assign_vars(array('onClickTypeEvenement1'=>"appelAjax('".$this->creerUrl('', 'afficheSelectTypeEvenement', array('noHeaderNoFooter'=>1, 'archiTypeGroupeEvenement'=>'1'))."', 'typeEvenement');document.getElementById('afficheChampsSupplementairesCulturel').style.display='block';document.getElementById('afficheChampsSupplementairesTravaux').style.display='none';"));
        $t->assign_vars(array('onClickTypeEvenement2'=>"appelAjax('".$this->creerUrl('', 'afficheSelectTypeEvenement', array('noHeaderNoFooter'=>1, 'archiTypeGroupeEvenement'=>'2'))."', 'typeEvenement');document.getElementById('afficheChampsSupplementairesTravaux').style.display='block';document.getElementById('afficheChampsSupplementairesCulturel').style.display='none';"));
            
        if ($type=="adresse") {
            // ***********************************************************************************
            // les type d'evenements
            // par defaut on selectionne le typeEvenement=2 (travaux)
            $resTypeEvenement = $this->connexionBdd->requete("SELECT idTypeEvenement, nom FROM typeEvenement where groupe = '".$groupeTypeEvenement."'");
            
            while ($fetchTypeEvenement = mysql_fetch_assoc($resTypeEvenement)) {
                $selected="";
                if ($typeEvenement!="" && $typeEvenement==$fetchTypeEvenement["idTypeEvenement"]) {
                    $selected = "selected";
                }

                $t->assign_block_vars(
                    'afficheAjoutEvenement.isAddress.typesEvenement',
                    array(
                        'id'=>$fetchTypeEvenement['idTypeEvenement'], 
                        'nom'=>$fetchTypeEvenement['nom'], 
                        'selected'=>$selected
                    )
                );
            }
        }
        
        // ***********************************************************************************
        // ISMH   (inscrit au s des monuments historiques)
        // MH (monument historique)
        if ($ISMH)
            $t->assign_vars(array('ISMHchecked'=>' checked'));
        
        if ($MH)
            $t->assign_vars(array('MHchecked'=>' checked'));
        
        // ***********************************************************************************
        
        
        // ***********************************************************************************
        // autre cas pour l'affichage du champ numeroArchive ,  il faut que l'utilisateur soit autorisé à l'afficher => table utilisateur
        $utilisateur = new archiUtilisateur();
        if ($utilisateur->canChangeNumeroArchiveField(array('idUtilisateur'=>$authentification->getIdUtilisateur()))) {
            $t->assign_block_vars('afficheAjoutEvenement.canChangeNumeroArchive', array());
        } else {
            $t->assign_block_vars('afficheAjoutEvenement.noChangeNumeroArchive', array());
        }
        // ***********************************************************************************
        // idem champ dateFin
        
        if ($utilisateur->canChangeDateFinField(array('idUtilisateur'=>$authentification->getIdUtilisateur()))) {
            $t->assign_block_vars('afficheAjoutEvenement.canChangeDateFin', array());
        } else {
            $t->assign_block_vars('afficheAjoutEvenement.noChangeDateFin', array());
        }
        
        
        
        
        
        
        $recherche = new archiRecherche();
        $source = new archiSource();
        
        $onClickBoutonValider=$type=="adresse"?"affichePopupAttente();".$arrayJsCoordonneesFromGoogleMap['jsFunctionCall']:"";
        $typeBoutonValidation=$type=="adresse"?"button":"submit";
        
        $t->assign_vars(
            array(
                'formAction'                    => $this->creerUrl($formAction), 
                
                'popupCalendrier'                => $this->getPopupCalendrier(), 
                
                'popupVilles'                    => $this->getPopupChoixVille('nouveauDossier'), 
                
                'popupRues'                        => $this->getPopupChoixRue('nouveauDossier'), 
                
                'popupSources'                    => $recherche->getPopupChoixSource('nouveauDossier'), 
                
                'popupPersonnes'                => $recherche->getPopupChoixPersonne('nouveauDossier'), 
                
                'onClickBoutonAjouterAdresse'            => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('', 'ajoutNouveauDossier')."'",                                     
                'onClickBoutonEnleverAdresse'            => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('', 'ajoutNouveauDossier')."'", 
                
                'onClickBoutonValider'                  => $onClickBoutonValider,
                'typeBoutonValidation'=>$typeBoutonValidation,
                'onClickBoutonChoixVille'        =>"document.getElementById('paramChampAppelantVille').value='ville';document.getElementById('calqueVille').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueVille').style.display='block';", 
                'onChangeListeQuartier'            =>"appelAjax('".$this->creerUrl('', 'afficheSelectSousQuartier', array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value, 'listeSousQuartier')", 
                
                'onClickBoutonChoisirSource'     =>"document.getElementById('paramChampsAppelantSource').value='source';document.getElementById('calqueSource').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueSource').style.display='block';", 
                
                'onClickChoixPersonne'            =>"document.getElementById('paramChampsAppelantPersonne').value='personnes';document.getElementById('calquePersonne').style.top=(getScrollHeight()+150)+'px';document.getElementById('calquePersonne').style.display='block';", 
                
                'onClickDateDebut'                =>"document.getElementById('paramChampAppelantDate').value='dateDebut';document.getElementById('calqueDate').style.top=(getScrollHeight()+150)+'px';document.getElementById('calqueDate').style.display='block';", 
                
                'onClickDateFin'                =>"document.getElementById('paramChampAppelantDate').value='dateDebut';document.getElementById('calqueDate').style.top=(getScrollHeight()+150)+'px';document.getElementById('paramChampAppelantDate').value='dateFin';document.getElementById('calqueDate').style.display='block';", 
                

                'popupAttente'=>$this->getPopupAttente()
            )
        );//document.getElementById('formAjoutDossier').action='".$this->creerUrl('ajoutNouveauDossier', '')."';testAdresseValideAndSubmit('formAjoutDossier');
        
        // ******************************************************************************************************************************
        // on recupere les messages d'aide contextuelle et on les affiche : 
        $helpMessages = $this->getHelpMessages("helpEvenement");
        
        foreach ($helpMessages as $fieldName => $helpMessage) {
            $t->assign_vars(array($fieldName=>$helpMessage));
        }
        
        // ******************************************************************************************************************************
        
        ob_start();
        $t->pparse('nouveauDossier');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    /**
     * Code du calque de la popup choix ville : appel du contenu de l'iframe
     * 
     * @param string $modeAffichage Mode d'affichage
     * 
     * @return string HTML
     * */
    public function getPopupChoixVille($modeAffichage='')
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('popupVille'=>'popupChoixVille.tpl')));
        
        switch ($modeAffichage) {
        case 'nouveauDossier':
        case 'modifImage':
        case 'modifUtilisateur':
                
            $t->assign_vars(
                array(
                    "iframeSrc"=>$this->creerUrl('', 'afficheChoixVille', array('noHeaderNoFooter'=>1, 'modeAffichage'=>$modeAffichage)), 
                    "onClose"=>"document.getElementById('calqueVille').style.display='none';"
                )
            );
            break;
        }
        
        ob_start();
        $t->pparse('popupVille');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    /**
     * Code du calque de la popup choix rue : appel du contenu de l'iframe
     * 
     * @param string $modeAffichage Mode d'affichage
     * 
     * @return string HTML
     * */
    public function getPopupChoixRue($modeAffichage='')
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('popupChoixRue'=>'popupChoixRue.tpl')));
        
        switch ($modeAffichage) {
        case 'nouveauDossier':
        case 'modifImage':
            $t->assign_vars(
                array(
                    "iframeSrc"=>$this->creerUrl('', 'afficheChoixRue', array('noHeaderNoFooter'=>1, 'modeAffichage'=>$modeAffichage)), 
                    "onClose"=>"document.getElementById('calqueRue').style.display='none';"
                )
            );
            break;
        }
        
        ob_start();
        $t->pparse('popupChoixRue');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    /**
     * Fonction réalisant l'ajout d'un nouveau dossier (groupe d'adresses + evenement construction)
     * 
     * @return void
     * */
    public function ajouterNouveauDossier()
    {
        $mail = new mailObject();
        // ******************************************************
        // en premier lieu on ajoute l'adresse
        // ******************************************************
        $arrayNewAdresses = array();
        
        $longitude='0';
        $latitude='0';
        if (isset($this->variablesPost['idUnique']) && count($this->variablesPost['idUnique'])>0) {
            if (isset($this->variablesPost['latitude']) && isset($this->variablesPost['longitude']) && $this->variablesPost['latitude']!='' && $this->variablesPost['longitude']!='') {
                $longitude = $this->variablesPost['longitude'];
                $latitude = $this->variablesPost['latitude'];
            }
            
            foreach ($this->variablesPost['idUnique'] as $indice => $valueIdUnique) {
                if ($this->variablesPost['rue'][$valueIdUnique]!='' && $this->variablesPost['rue'][$valueIdUnique]!='0') {
                    $arrayNewAdresses[] = $this->ajouterAdresseFromArray(
                        array(
                            'idPays'=>1, 
                            'idVille'=>$this->variablesPost['ville'], 
                            'idQuartier' => $this->variablesPost['quartiers'], 
                            'idSousQuartier'=>$this->variablesPost['sousQuartiers'], 
                            'numero'=>$this->variablesPost['numero'][$valueIdUnique], 
                            'indicatif'=>$this->variablesPost['indicatif'][$valueIdUnique], 
                            'idRue'=>$this->variablesPost['rue'][$valueIdUnique], 
                            'longitude'=>$this->variablesPost['longitude'][$valueIdUnique], 
                            'latitude'=>$this->variablesPost['latitude'][$valueIdUnique]
                        )
                    );
                } else {
                    $arrayNewAdresses[] = $this->ajouterAdresseFromArray(
                        array(
                            'idPays'=>1, 
                            'idVille'=>$this->variablesPost['ville'], 
                            'idQuartier' => $this->variablesPost['quartiers'], 
                            'idSousQuartier'=>$this->variablesPost['sousQuartiers'], 
                            'numero'=>0, 
                            'indicatif'=>0, 
                            'idRue'=>0, 
                            'longitude'=>'', 
                            'latitude'=>''
                        )
                    );
                
                }
            }
            
            $this->addEvent("adresse",  $arrayNewAdresses);
            
            // enfin on regenere les caches
            //$cache = new cacheObject();
            //$cache->refreshCache();
        } else if (isset($_POST["metier"]) && !empty($_POST["nom"])) {
            $this->ajouter();
            $newPerson=array(array("idAdresse"=>mysql_insert_id()));
            $this->addEvent("personne",  $newPerson);
        } else {
            $this->erreurs->ajouter("Aucune fiche n'a pu être ajoutée. Vérifiez la saisie du champ.");
        }
    }
    
    /**
     * Ajouter un événement à la BDD
     * 
     * @param string $type             Type (adresse ou personne)
     * @param array  $arrayNewAdresses Contenu lié
     * 
     * @return void
     * */
    function addEvent ($type="adresse",  $arrayNewAdresses=array())
    {
        // ******************************************************
        // ensuite on ajoute l'evenement
        // ******************************************************
        $evenement=new archiEvenement();
        $arrayRetourEvenementNouveauDossier=$evenement->ajouterEvenementNouveauDossier();
    
        $idEvenementGroupeAdresses=0;
        $idSousEvenement=0;
        
        if ($type=="personne") {
            $linkTable="_personneEvenement";
            $field="idPersonne";
        } else {
            $linkTable="_adresseEvenement";
            $field="idAdresse";
        }
        
        
        // s'il n'y a pas eu d'erreurs ,  on peut faire l'ajout des liaisons entre evenement et adresses
        if (count($arrayRetourEvenementNouveauDossier['errors'])==0 && count($arrayNewAdresses)>0) {
            $idEvenementGroupeAdresses = $arrayRetourEvenementNouveauDossier['idEvenementGroupeAdresse'];
            $idSousEvenement = $arrayRetourEvenementNouveauDossier['idSousEvenement'];
            // liaison entre les adresses et l'evenement groupe d'adresses
            $resSupp = $this->connexionBdd->requete("delete from $linkTable where idEvenement = '".$idEvenementGroupeAdresses."'");
            
            
            // on rend la liste des identifiants unique
            $arrayNewIdAdresses=array();
            foreach ($arrayNewAdresses as $indice =>$value) {
                $arrayNewIdAdresses[] = $value['idAdresse'];
            }
            
            $arrayNewIdAdresses = array_unique($arrayNewIdAdresses);
            foreach ($arrayNewIdAdresses as $indice => $idAdresse) {
                
                $reqLiaisons = "INSERT INTO $linkTable ($field, idEvenement)
                                VALUES ('".$idAdresse."', '".$idEvenementGroupeAdresses."')
                ";
                
                $resLiaisons = $this->connexionBdd->requete($reqLiaisons);
            }
            if ($type=="personne") {
                
            } else {
                // *************************************************************************************************************************************************************
                // envoi d'un mail aux administrateur pour la moderation
                $utilisateur = new archiUtilisateur();
                
                
                $message="L'utilisateur suivant a créé un nouveau dossier : ";
                $message .= $utilisateur->getMailUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite))."<br>";
                $message .="<a href='".$this->creerUrl(
                    '', '', array('archiAffichage'=>'adresseDetail',
                    'archiIdAdresse'=>$arrayNewIdAdresses[0],
                    'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresses)
                )."'>lien vers l'article</a><br>";
                $message .="Adresses liées au nouveau dossier :<br>";
                
                $i=0;
                $adressePourSujetMail="";
                
                foreach ($arrayNewAdresses as $indice => $value) {
                    if ($i==0) {
                        $adressePourSujetMail = $this->getIntituleAdresseFrom(
                            $value['idAdresse'], 'idAdresse'
                        );
                    }
                    
                    if ($value['newAdresse']==true) {
                        // cette adresse a été créée avec le dossier
                        $message .="<a href='".$this->creerUrl(
                            '', '', array('archiAffichage'=>'adresseDetail',
                            'archiIdEvenementGroupeAdresse'
                                =>$idEvenementGroupeAdresses,
                                'archiIdAdresse'=>$value['idAdresse'])
                        )."'>".$this->getIntituleAdresseFrom(
                            $value['idAdresse'], 'idAdresse'
                        )." (nouvelle adresse)</a><br>";
                    } else {
                        /* cette adresse a été créée précédemment
                         * et est utilisée sur ce dossier
                         * */
                        $message .="<a href='".$this->creerUrl(
                            '', '', array('archiAffichage'=>'adresseDetail',
                            'archiIdEvenementGroupeAdresse'
                                =>$idEvenementGroupeAdresses,
                            'archiIdAdresse'=>$value['idAdresse'])
                        )."'>".$this->getIntituleAdresseFrom(
                            $value['idAdresse'], 'idAdresse'
                        )." (cette adresse existait déjà ".
                        "avant la création du dossier)".
                        "</a><br>";
                    }
                }
                $mail = new mailObject();
                $mail->sendMailToAdministrators(
                    $mail->getSiteMail(),
                    "archi-strasbourg.org : ".
                    "un utilisateur a créé un nouveau dossier - ".
                    $adressePourSujetMail, $message, " and alerteMail='1' ", true
                );
                $u = new archiUtilisateur();
                $u->ajouteMailEnvoiRegroupesAdministrateurs(
                    array('contenu'=>$message, 'idTypeMailRegroupement'=>3,
                    'criteres'=>" and alerteMail='1' ")
                );
                /* envoi mail aussi au moderateur si ajout
                 * sur adresse de ville que celui ci modere
                 * */
                
                
                $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille(
                    $this->variablesPost['ville'],
                    array("sqlWhere"=>" AND alerteMail='1' ")
                );
                if (count($arrayListeModerateurs)>0) {
                    foreach ($arrayListeModerateurs as $indice => $idModerateur) {
                        if ($this->session->getFromSession(
                            "utilisateurConnecte".$this->idSite
                        )!=$idModerateur) {
                            $mailModerateur = $u->getMailUtilisateur($idModerateur);
                            if ($u->isMailEnvoiImmediat($idModerateur)) {
                                $mail->sendMail(
                                    $mail->getSiteMail(), $mailModerateur,
                                    "archi-strasbourg.org : ".
                                    "un utilisateur a créé un nouveau dossier - ".
                                    $adressePourSujetMail, $message, true
                                );
                            } else {
                                // envoi regroupé
                                $u->ajouteMailEnvoiRegroupes(
                                    array('contenu'=>$message,
                                    'idDestinataire'=>$idModerateur,
                                    'idTypeMailRegroupement'=>3)
                                );
                            }
                        }
                        
                    }
                }
            }
            
            
            //$retourEvenement = $evenement->afficher($idEvenementGroupeAdresses);
            //echo $retourEvenement["html"];
            if ($type=="personne") {
                //?archiAffichage=evenementListe&selection=personne&id=
                header(
                    "Location: ".$this->creerUrl(
                        '', '', array('archiAffichage'=>'evenementListe',
                        'selection'=>"personne", 'id'=>$idAdresse),  false,  false
                    )
                );
            } else {
                header(
                    "Location: ".$this->creerUrl(
                        '', '', array('archiAffichage'=>'adresseDetail',
                        'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresses,
                        'archiIdAdresse'=>$value['idAdresse']),  false,  false
                    )
                );
            }
        }
    }
}

?>
