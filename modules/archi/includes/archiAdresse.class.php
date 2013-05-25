<?php
require_once __DIR__."/ArchiContenu.class.php";

class archiAdresse extends ArchiContenu
{
    
    private $description = '';
    private $nom        = '';
    private $idRue      = '';
    private $etage      = '';
    private $numero     = '';
    private $idQuartier = '';
    private $idSousQuartier = '';
    private $idPays     = '';
    private $idVille    = '';
    private $nomRue     = '';
    private $nomQuartier    = '';
    private $nomSousQuartier= '';
    private $nomPays    = '';

    private $microstart;

    function __construct() 
    {
        parent::__construct();
        //$this->microstart=microtime(true);
    }
    
    function __destruct()
    {
        /*$fin_compte=microtime(true);
        $duree=($fin_compte-$this->microstart);
        if($duree>1)
        {   echo '<br><br>Element '.get_class().' :: g&eacute;n&eacute;r&eacute;e en '.substr($duree,0,5).' sec.'; //'.print_r(get_object_vars($this)).'
            $backtrace = debug_backtrace();
            echo  $backtrace[1]['function'];
        }*/
    }

    
    private function getAdresseFields()
    {
    
        return array(
            'nom'         =>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'text'),
            'date'        =>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'date'),
            'description' =>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'text'),
            'indicatif' =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text'),
            'numero'      =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text'),
            'rue'         =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text','checkExist'=>array('table'=>'rue','primaryKey'=>'idRue')),
            'sousQuartier'=>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text','checkExist'=>array('table'=>'sousQuartier','primaryKey'=>'idSousQuartier')),
            'quartier'    =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text','checkExist'=>array('table'=>'quartier','primaryKey'=>'idQuartier')),
            'ville'       =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text','checkExist'=>array('table'=>'ville','primaryKey'=>'idVille')),
            'pays'        =>array('default'=>'0','value'=>'','required'=>false,'error'=>'','type'=>'text','checkExist'=>array('table'=>'pays','primaryKey'=>'idPays'))
        );
    
    }
    
    
    public function getCommentairesFields()
    {
        return array(
                'nom'                   =>array('type'=>'text','default'=>'','htmlCode'=>'','libelle'=>_("Votre nom ou pseudo :"),'required'=>true,'error'=>'','value'=>''),
                'prenom'                    =>array('type'=>'text','default'=>'','htmlCode'=>'','libelle'=>_("Votre prénom :"),'required'=>false,'error'=>'','value'=>''),
                'email'                     =>array('type'=>'email','default'=>'','htmlCode'=>"style='width:250px;'",'libelle'=>_("Votre e-mail :"),'required'=>false,'error'=>'','value'=>''),
                'commentaire'               =>array('type'=>'bigText','default'=>'','htmlCode'=>"rows=5 cols=50",'libelle'=>_("Votre commentaire :"),'required'=>true,'error'=>'','value'=>''),
                'idEvenementGroupeAdresse'  => array('type'=>'hidden','default'=>'','htmlCode'=>'','libelle'=>'','required'=>false,'error'=>'','value'=>''),
                'captcha'                   =>array('type'=>'captcha','default'=>'','htmlCode'=>'','libelle'=>'','required'=>true,'error'=>'','value'=>'')
        );
    }
    
    
    // ***************************************************************************************************************************************
    // ajout d'une nouvelle adresse dans la base de données
    // ***************************************************************************************************************************************
    public function ajouter() 
    {
        $authentifie = new archiAuthentification();
        $formulaire = new formGenerator();
        if($authentifie->estConnecte())
        {
            $tabForm=$this->getAdresseFields();
            $this->connexionBdd->getLock(array('historiqueAdresse'));
            $errors = $formulaire->getArrayFromPost($tabForm); // recupere les donnees POST suivant les champs de tabForm , tabForm est passé en référence, il contient donc les valeurs des champs a la sortie de la fonction
            $this->nettoieCoordonneesAdresse($tabForm); // permet de garder l'id de la rue, quartier, sousQuartier le plus bas dans la hierarchie , a partir duquel on peut retrouver le reste
            
            if(count($errors)==0)
            {
                // on regarde si l'adresse existe deja parmis les adresses valides (non "archivees")
                $sql = "SELECT ha1.idAdresse as idAdresse
                    FROM historiqueAdresse ha1, historiqueAdresse ha2
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idRue='".$tabForm['rue']['value']."'
                    AND ha1.idSousQuartier='".$tabForm['sousQuartier']['value']."'
                    AND ha1.idQuartier='".$tabForm['quartier']['value']."'
                    AND ha1.idVille='".$tabForm['ville']['value']."'
                    AND ha1.idPays='".$tabForm['pays']['value']."'
                    AND ha1.numero = '".$tabForm['numero']['value']."'
                    AND ha1.date='".$tabForm['date']['value']."'
                    AND ha1.idIndicatif='".$tabForm['indicatif']['value']."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse 
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
                
                
                $resCheckExist=$this->connexionBdd->requete($sql);

                if(mysql_num_rows($resCheckExist)>1)
                {
                    echo "plusieurs adresse existe concernant cette adresse, pas d'ajout, veuillez prevenir l'administrateur.<br>";
                }
                elseif(mysql_num_rows($resCheckExist)==1)
                {
                    echo "l'adresse existe deja dans la base";
                    // recuperation de l'id existant
                    $fetchAdresseExistante=mysql_fetch_array($resCheckExist);
                }
                else
                {
                    // ajout
                    // recherche du nouvel idAdresse
                    $resIdAdresse = $this->connexionBdd->requete("select max(idAdresse) as maxIdAdresse from historiqueAdresse");
                    $nouvelIdAdresse=0;
                    if(mysql_num_rows($resIdAdresse)>0)
                    {
                        $fetchNouvelIdAdresse = mysql_fetch_array($resIdAdresse);
                        $nouvelIdAdresse = $fetchNouvelIdAdresse["maxIdAdresse"] + 1;
                    }
                    
                    $sql = "INSERT INTO historiqueAdresse (idUtilisateur,idAdresse,date,description,nom,idRue,numero,idQuartier,idSousQuartier,idPays,idVille) VALUES (
                    '".$authentifie->getIdUtilisateur()."',
                    '".$nouvelIdAdresse."',
                    \"".$this->date->toBdd($tabForm['date']['value'])."\",
                    \"".$tabForm['description']['value']."\",
                    \"".$tabForm['nom']['value']."\",
                    '".$tabForm['rue']['value']."',
                    '".$tabForm['numero']['value']."',
                    '".$tabForm['quartier']['value']."',
                    '".$tabForm['sousQuartier']['value']."',
                    '".$tabForm['pays']['value']."',
                    '".$tabForm['ville']['value']."'
                    )";
                    
                    $this->connexionBdd->requete($sql);
                    
                    $cache = new cacheObject();
                    $cache->refreshCache();
                }
            }
            else
            {   
                echo $this->afficheFormulaire($tabForm);
            }
            
            

            
            $this->connexionBdd->freeLock(array('historiqueAdresse'));
        }
        else
        {
            echo "erreur : Vous devez etre authentifié pour faire un ajout<br>";
        }
    }

    public function supprimer($idAdresse = 0, $idHistoriqueAdresse = 0)
    {
        $html = '';
        $formulaire = new formGenerator();  
        // en premier la suppresion de l'historique, si l'évènement n'a qu'un historique
        // on initialisera la variable idEvenement pour utiliser le code plus bas
        if ( $formulaire->estChiffre($idHistoriqueAdresse) == 1)
        {
            $sqlVerif = "SELECT hA.idAdresse, COUNT(hA.idAdresse) AS nbAdresse FROM historiqueAdresse hA
                LEFT JOIN historiqueAdresse hA2 USING (idAdresse)
                WHERE hA.idHistoriqueAdresse=".$idHistoriqueAdresse.'
                GROUP BY hA.idAdresse, hA.idHistoriqueAdresse';
            
            $rep = $this->connexionBdd->requete($sqlVerif);
            $res = mysql_fetch_object($rep);
            
            if ( $res->nbEvenement == 1)
            {
                // on initialise la variable idEvenement pour utiliser la supression d'un évènement
                $idEvenement = $res->idEvenement;
            }
            else
            {
                $t = new Template('modules/archi/templates/');
                $t->set_filenames(array('suppr'=>'suppression.tpl'));
                $t->assign_vars(array('nom'=>'Historique évènement'));

                $sql = "DELETE FROM historiqueEvenement WHERE idHistoriqueEvenement=".$idHistoriqueEvenement;
                $this->connexionBdd->requete($sql);
                $t->assign_block_vars('suppr', array('description'=>'historique evenement', 'nombre'=>mysql_affected_rows()));

                ob_start();
                $t->pparse('suppr');
                $html .= ob_get_contents();
                ob_end_clean();
            }
        }
        
        
        // suppression de toutes les informations sur l'évènement
        if ( $formulaire->estChiffre($idAdresse) == 1)
        {
            $tabSql[] = array( "DELETE FROM historiqueEvenement WHERE idEvenement=".$idEvenement, 'évènement');
            $tabSql[] = array( "DELETE FROM _evenementImage     WHERE idEvenement=".$idEvenement, 'liens images');
            $tabSql[] = array( "DELETE FROM _adresseEvenement   WHERE idEvenement=".$idEvenement, 'liens adresse');
            $tabSql[] = array( "DELETE FROM _evenementPersonne  WHERE idEvenement=".$idEvenement, 'liens personnes');
            $tabSql[] = array( "DELETE FROM _evenementEvenement WHERE idEvenement=".$idEvenement." OR idEvenementAssocie=".$idEvenement, 'liens évènements');
            
            $t = new Template('modules/archi/templates');
            $t->set_filenames(array('suppr'=>'suppression.tpl'));
            $t->assign_vars(array('nom'=>'Évènement'));

            foreach($tabSql AS $suppression)
            {
                list( $sql, $descriptionSupression) = $suppression;
                //$this->connexionBdd->requete($sql);
                $t->assign_block_vars('suppr', array('description'=>$descriptionSupression, 'nombre'=>mysql_affected_rows()));
            }

            ob_start();
            $t->pparse('suppr');
            $html .= ob_get_contents();
            ob_end_clean();
        }
        
        
        $cache = new cacheObject();
        $cache->refreshCache();
        
        return $html;
    }

    public function nettoieCoordonneesAdresse(&$tabForm=array())
    {
        if($tabForm['rue']['value']!=0)
        {
            $tabForm['sousQuartier']['value']=0;
            $tabForm['quartier']['value']=0;
            $tabForm['ville']['value']=0;
            $tabForm['pays']['value']=0;
        }
        elseif($tabForm['sousQuartier']['value']!=0)
        {
            $tabForm['rue']['value']=0;
            $tabForm['quartier']['value']=0;
            $tabForm['ville']['value']=0;
            $tabForm['pays']['value']=0;
        }
        elseif($tabForm['quartier']['value']!=0)
        {
            $tabForm['rue']['value']=0;
            $tabForm['sousQuartier']['value']=0;
            $tabForm['ville']['value']=0;
            $tabForm['pays']['value']=0;
        }
        elseif($tabForm['ville']['value']!=0)
        {
            $tabForm['rue']['value']=0;
            $tabForm['sousQuartier']['value']=0;
            $tabForm['quartier']['value']=0;
            $tabForm['pays']['value']=0;
        }
        elseif($tabForm['pays']['value']!=0)
        {
            $tabForm['rue']['value']=0;
            $tabForm['sousQuartier']['value']=0;
            $tabForm['quartier']['value']=0;
            $tabForm['ville']['value']=0;
        }
    
    }
    
    public function modifierHistorique ( $idHistoriqueAdresse )
    {
        $html = '';
        $authentifie = new archiAuthentification();
        $tabForm = array();
        $formulaire = new formGenerator();  
        if($authentifie->estConnecte())
        {
            if (isset($this->variablesPost['submit']))
            {
                $tabForm=$this->getAdresseFields();
                $errors = $formulaire->getArrayFromPost($tabForm); // recupere les donnees POST suivant les champs de tabForm , tabForm est passé en référence, il contient donc les valeurs des champs a la sortie de la fonction
                $this->nettoieCoordonneesAdresse($tabForm); // permet de garder l'id de la rue, quartier, sousQuartier le plus bas dans la hierarchie , a partir duquel on peut retrouver le reste
                
                if(count($errors)==0)
                {
                    // on regarde si l'adresse existe deja parmis les adresses valides (non "archivees")
                    $sql = "UPDATE historiqueAdresse 
                        SET
                        idUtilisateur='".$authentifie->getIdUtilisateur()."',
                        date='".$this->date->toBdd($tabForm['date']['value'])."',
                        description='".$tabForm['description']['value']."',
                        nom='".$tabForm['nom']['value']."',
                        idRue='".$tabForm['rue']['value']."',
                        numero='".$tabForm['numero']['value']."',
                        idQuartier='".$tabForm['quartier']['value']."',
                        idSousQuartier='".$tabForm['sousQuartier']['value']."',
                        idPays='".$tabForm['pays']['value']."',
                        idVille='".$tabForm['ville']['value']."'
                        WHERE idHistoriqueAdresse=".$idHistoriqueAdresse;
                    $this->connexionBdd->requete($sql);
                }
            }
            $html = $this->afficheFormulaire($tabForm, '', $idHistoriqueAdresse);
        }
        else
        {
            $html = "erreur : Vous devez etre authentifié pour faire un ajout";
        }
        return $html;
    }
    public function ajouterHistoriqueAdresse( $idAdresse )
    {
        $html = '';
        $authentifie = new archiAuthentification();
        $formulaire = new formGenerator();
        if($authentifie->estConnecte())
        {
            $tabForm=$this->getAdresseFields();
            
            if (isset($this->variablesPost['submit']))
            {
                $this->connexionBdd->getLock(array('historiqueAdresse'));
                $errors = $formulaire->getArrayFromPost($tabForm);
                $this->nettoieCoordonneesAdresse($tabForm); // permet de garder l'id de la rue, quartier, sousQuartier le plus bas dans la hierarchie , a partir duquel on peut retrouver le reste
                if(count($errors)==0)
                {
                    // on regarde si l'adresse existe deja parmis les adresses valides (non "archivees")
                    $sql = "
                        SELECT ha.idAdresse
                        FROM historiqueAdresse ha 
                        WHERE ha.idAdresse = ".$idAdresse."
                        AND 
                        (
                            (
                                ha.idRue='".mysql_escape_string($tabForm['rue']['value'])."'
                                AND ha.idSousQuartier='".mysql_escape_string($tabForm['sousQuartier']['value'])."'
                                AND ha.idQuartier='".mysql_escape_string($tabForm['quartier']['value'])."'
                                AND ha.idVille='".mysql_escape_string($tabForm['ville']['value'])."'
                                AND ha.idPays='".mysql_escape_string($tabForm['pays']['value'])."'
                                AND ha.numero = '".mysql_escape_string($tabForm['numero']['value'])."'
                                AND ha.nom='".mysql_escape_string($tabForm['nom']['value'])."'
                                AND ha.description='".mysql_escape_string($tabForm['description']['value'])."'
                            )
                            OR
                                ha.date='".$this->date->toBdd($tabForm['date']['value'])."'
                        )
                        ";
                    $resCheckExist=$this->connexionBdd->requete($sql);

                    if(mysql_num_rows($resCheckExist)>0)
                    {
                        echo "Cet enregistrement existe deja dans la base, si vous n'avez pas mis les mêmes informations, assurez vous d'avoir bien changé la date.";
                        // recuperation de l'id existant
                        $fetchAdresseExistante=mysql_fetch_array($resCheckExist);
                    }
                    else
                    {
                        // ajout
                        $sql = "INSERT INTO historiqueAdresse (idUtilisateur, idAdresse,date,description,nom,idRue,numero,idQuartier,idSousQuartier,idPays,idVille) VALUES (
                        '".$authentifie->getIdUtilisateur()."',
                        '".$idAdresse."',
                        \"".$this->date->toBdd($tabForm['date']['value'])."\",
                        \"".$tabForm['description']['value']."\",
                        \"".$tabForm['nom']['value']."\",
                        '".$tabForm['rue']['value']."',
                        '".$tabForm['numero']['value']."',
                        '".$tabForm['quartier']['value']."',
                        '".$tabForm['sousQuartier']['value']."',
                        '".$tabForm['pays']['value']."',
                        '".$tabForm['ville']['value']."'
                        )";

                        $this->connexionBdd->requete($sql);
                        
                        $cache = new cacheObject();
                        $cache->refreshCache();
                        echo 'votre modification a bien été prise en compte';
                    }
                }
                $this->connexionBdd->freeLock(array('historiqueAdresse'));
                $html .= $this->afficheFormulaire($tabForm, $idAdresse);
            }
            else
            {
                $html .= $this->afficheFormulaire(array(), $idAdresse);
            }
        }
        else
        {
            $html .= "archiAdresse::modifier() => probleme d'authentification ou d'identifiant transmis";
        }

        return $html;
        
    }
    // ***************************************************************************************************************************************
    // renvoi les Evenements lies a l'adresses ( en principe on ne retourne que des groupes d'adresses
    // ***************************************************************************************************************************************
    public function getIdEvenementsFromAdresse($idAdresse=0)
    {
        $sql = "SELECT ae.idEvenement as idEvenement,ae.idEvenement as idEvenementGroupeAdresse FROM _adresseEvenement ae WHERE ae.idAdresse = '".$idAdresse."'";
        
        return $this->connexionBdd->requete($sql);
    }
    
    // alias de la fonction précédente
    public function getIdEvenementGroupeAdresseFromAdresse($idAdresse=0)
    {
        return $this->getIdEvenementsFromAdresse($idAdresse);
    }
    
    // ***************************************************************************************************************************************
    // affiche le detail d'une adresse , c'est a dire les evenements dont le groupe d'adresse correspond
    // ***************************************************************************************************************************************
    public function afficherDetail($idAdresse=0,$idEvenementGroupeAdresse=0) 
    {
        // attention s'il y a plusieurs evenement distinct (pas associes entre eux) reliés a l'adresse on les affichera a la suite
        if (isset($_GET["archiIdAdresse"])) {
            $address=$this->getArrayAdresseFromIdAdresse($_GET["archiIdAdresse"]);
        }
        
        $trouve = false;
            
            
            // on regarde d'abord s'il existe un titre pour le groupe d'adresse
            // vu qu'un evenement groupe d'adresse est unique , on ne va pas grouper dans la requete
            $reqVerif = "
            SELECT idEvenementRecuperationTitre 
            FROM historiqueEvenement he
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = '$idAdresse'
            WHERE he.idEvenement = ae.idEvenement
            ";
        $resVerif = $this->connexionBdd->requete($reqVerif);
            if(mysql_num_rows($resVerif)>0)
            {
                $fetchVerif = mysql_fetch_assoc($resVerif);
                if($fetchVerif['idEvenementRecuperationTitre']=='0')
                {
                    $trouve=false;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']=='-1')
                {
                    $params['ifTitreAfficheTitreSeulement']=true;
                    $titre='';
                    $trouve=true;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']!='0')
                {
                    $reqTitre = "
                        SELECT he1.titre as titre
                        FROM historiqueEvenement he2, historiqueEvenement he1
                        WHERE he2.idEvenement = he1.idEvenement
                        AND he1.idEvenement = '".$fetchVerif['idEvenementRecuperationTitre']."'
                        GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)                       
                    ";
                    $resTitre = $this->connexionBdd->requete($reqTitre);
                    
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    $titre = "<span>".stripslashes($fetchTitre['titre'])."</span>";
                    if(trim($fetchTitre['titre'])!='')
                    {
                        $trouve=true;
                        
                    }
                    else
                    {
                        $trouve=true; // meme si pas de titre , ceci va permettre d'afficher l'adresse
                        $titre='';
                    }
                    
                }
            }
            
            if(!$trouve)
            {
                // avec ce parametre , on va aller chercher le premier titre rencontré sur la liste des evenements du groupe d'adresse de l'adresse
                
                $reqTitre = "
                        SELECT he1.titre as titre
                        FROM _adresseEvenement ae
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                        LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                        WHERE he1.titre!=''
                        AND ae.idAdresse = '".$idAdresse."'
                        AND he1.idTypeEvenement <>'6'
                        GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                        ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
                        LIMIT 1
                
                ";
                $resTitre = $this->connexionBdd->requete($reqTitre);
                if(mysql_num_rows($resTitre)==1)
                {
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    $titre = stripslashes($fetchTitre['titre']);
                    if(trim($fetchTitre['titre'])=='')
                    {
                        $noTitreDetected=true;
                        $titre='';
                    }
                }
            }
        $html="<div class='fb-like right' data-send='false' data-layout='button_count' data-show-faces='true' data-action='recommend'></div>
        <a href='https://twitter.com/share' class='twitter-share-button right' data-via='ArchiStrasbourg' data-lang='fr' data-related='ArchiStrasbourg'>Tweeter</a> 
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>"; 
        $html.="<h2>";
        $e=new archiEvenement();
        $archiIdEvenementGroupeAdresse=isset($_GET['archiIdEvenementGroupeAdresse'])?$_GET['archiIdEvenementGroupeAdresse']:$e->getIdEvenementGroupeAdresseFromIdEvenement($_GET["archiIdEvenement"]);
        $titre=$intituleAdresse = $this->getIntituleAdresseFrom($archiIdEvenementGroupeAdresse, "idEvenementGroupeAdresse", array("afficheTitreSiTitreSinonRien"=>true, "noHTML"=>true));
        if (isset($titre) && !empty($titre)) {
            if (isset($_GET['archiAffichage']) && $_GET['archiAffichage']=='adresseDetail') {
                $html.="<span itemprop='name'>";
            }
            $html.=$titre;
            if (isset($_GET['archiAffichage']) && $_GET['archiAffichage']=='adresseDetail') {
                $html.="</span>";
            }
            if (!empty($address["nomRue"])) {
                $html.="&nbsp;-&nbsp;";
            }
        }
        if (isset($address)) {
            if (isset($_GET['archiAffichage']) && $_GET['archiAffichage']=='adresseDetail') {
                $html.="<span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'><span itemprop='streetAddress'>";
            }
            if ($address["numero"]." " != 0) {
                $html.=$address["numero"];
                if (isset($address['nomIndicatif'])) {
                    $html.=$address["nomIndicatif"];
                }
                $html.=' ';
            }
            $html.=$address["prefixeRue"]." ".$address["nomRue"];
            if (isset($_GET['archiAffichage']) && $_GET['archiAffichage']=='adresseDetail') {
                $html.="</span>
                <meta itemprop='addressLocality' content='".$address["nomVille"]."'/>
                <meta itemprop='addressCountry' content='".$address["nomPays"]."'/>
                </span>";
            }
            
        } 
        $html.="</h2>";
        
        $evenement = new archiEvenement();
        // si le groupe d'adresse est precisé dans l'url , on ne va afficher que celui ci
        if(isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='') 
        {
            $retourEvenement = $evenement->afficher($this->variablesGet['archiIdEvenementGroupeAdresse'],'',null,array()); // cette fonction va afficher les evenements liés au groupe d'adresse
            $html.=$retourEvenement['html'];
            $html.=$this->getListeCommentaires($this->variablesGet['archiIdEvenementGroupeAdresse']);
            $html.=$this->getFormulaireCommentaires($this->variablesGet['archiIdEvenementGroupeAdresse'],$this->getCommentairesFields());
        }
        elseif($idEvenementGroupeAdresse!='' && $idEvenementGroupeAdresse !='0')
        {
            $retourEvenement = $evenement->afficher($idEvenementGroupeAdresse,'',null,array());
            $html.=$retourEvenement['html'];
            $html.=$this->getListeCommentaires($idEvenementGroupeAdresse);
            $html.=$this->getFormulaireCommentaires($idEvenementGroupeAdresse,$this->getCommentairesFields());
        }
        else
        {
            $resEvenements=$this->getIdEvenementsFromAdresse($idAdresse); //c'est l'evenement groupe d'adresse qui est relié a l'idAdresse donc on recupere un idEvenementGroupeAdresse en fait

            if(mysql_num_rows($resEvenements)==1)
            {
                // un seul evenement groupe d'adresse correspond a l'adresse cliquée
                $fetchEvenements = mysql_fetch_assoc($resEvenements);
                $retourEvenement = $evenement->afficher($fetchEvenements['idEvenement'],'',null,array()); // cette fonction va afficher les evenements liés au groupe d'adresse
                $html.=$retourEvenement['html'];
                $html.=$this->getListeCommentaires($fetchEvenements['idEvenement']);
                $html.=$this->getFormulaireCommentaires($fetchEvenements['idEvenement'],$this->getCommentairesFields());
            }
            else
            {
                // il y a plusieurs evenements groupes d'adresses qui correspondent a la meme adresse
                // on n'affiche que les evenements qui concernent la recherche 
                // archiIdEvenementGroupeAdresse , correspond au groupe d'adresse de l'adresse resultat de la recherche
                if(isset($this->variablesGet['recherche_motcle']))
                {
                    // on refait une recherche sur les evenements concernés par l'adresse correspondant a plusieurs evenements groupe adresse
                    
                    $req = "
                            SELECT ee.idEvenement as idEvenementGroupeAdresse, he1.idEvenement as idEvenement
                            FROM historiqueEvenement he2, historiqueEvenement he1
                            RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = '".$idAdresse."'
                            RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                            WHERE he2.idEvenement = he1.idEvenement
                            AND he1.idEvenement = ee.idEvenementAssocie
                            AND CONCAT_WS('',lower(he1.titre),lower(he1.description)) like \"%".strtolower($this->variablesGet['recherche_motcle'])."%\"
                            GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                    ";
                    
                    $res = $this->connexionBdd->requete($req);
                    
                    while($fetch = mysql_fetch_assoc($res))
                    {
                        $retourEvenement = $evenement->afficher($fetch['idEvenementGroupeAdresse'],'',null,array(),array()); // cette fonction va afficher les evenements liés au groupe d'adresse
                        //'rechercheAdresseCalqueObject'=>$c
                        $html.=$retourEvenement['html'];
                    }
                }
                else
                {
                    $nbGroupesAdressesAffiches = 0;
                    while($fetchEvenements = mysql_fetch_assoc($resEvenements))
                    {
                        $retourEvenement = $evenement->afficher($fetchEvenements['idEvenement'],'',null,array()); // cette fonction va afficher les evenements liés au groupe d'adresse
                        $html.=$retourEvenement['html'];
                        $nbGroupesAdressesAffiches+=count($retourEvenement['listeGroupeAdressesAffichees']);
                        if(isset($retourEvenement['listeGroupeAdressesAffichees'][0]))
                        {
                            $groupeAdresse = $retourEvenement['listeGroupeAdressesAffichees'][0];
                        }   
                    }
                    
                    if($nbGroupesAdressesAffiches==1)
                    {
                        $html.=$this->getListeCommentaires($groupeAdresse);
                        $html.=$this->getFormulaireCommentaires($groupeAdresse,$this->getCommentairesFields());
                    }
                }
            }
        }
        // popup pour la description de la source
        $s = new archiSource();
        $html.=$s->getPopupDescriptionSource();
        
        return $html;
    }
    

    // ***************************************************************************************************************************************
    // renvoi la liste des idAdresses qui correspondent a la recherche par mot cle
    // ***************************************************************************************************************************************
    public function getIdAdressesFromRecherche($criteres)
    {
        $tabIdAdresses=array();
        $sqlWhere="";
        if(isset($criteres['recherche_motcle']) && $criteres['recherche_motcle']!='')
        {
            $tabMotCle = explode(' ', $criteres['recherche_motcle']);
            foreach ($tabMotCle AS $motcle)
            {
                $motcle = mysql_escape_string($motcle);
                $sqlWhere .= "AND (CONCAT_WS('',CONVERT(ha.numero, CHAR(8)),r.prefixe, ha.nom,ha.description,r.nom,sq.nom, q.nom, v.nom, p.nom) LIKE '%".$motcle."%')";
                
            }
            /* (ha.nom LIKE '%".$motcle."%' 
                    OR ha.description LIKE '%".$motcle."%' 
                    OR ha.numero LIKE '%".$motcle."%' 
                    OR r.nom LIKE '%".$motcle."%' 
                    OR sq.nom LIKE '%".$motcle."%' 
                    OR q.nom LIKE '%".$motcle."%' 
                    OR v.nom LIKE '%".$motcle."%' 
                    OR p.nom LIKE '%".$motcle."%')
                    OR 
            */
            $req = "
                    SELECT distinct ha.idAdresse as idAdresse
                    FROM historiqueAdresse ha2, historiqueAdresse ha
                    LEFT JOIN rue r         ON r.idRue = ha.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha.idRue='0' and ha.idSousQuartier!='0' ,ha.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier!='0' ,ha.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille!='0' ,ha.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille='0' and ha.idPays!='0' ,ha.idPays ,v.idPays )

                    
                    WHERE ha2.idAdresse = ha.idAdresse
                    ".$sqlWhere."
                    GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                    HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    ";
            
            $res = $this->connexionBdd->requete($req);
            
            while($fetch = mysql_fetch_assoc($res))
            {
                $tabIdAdresses[]=$fetch['idAdresse'];

            }
        }
        return $tabIdAdresses;
    
    }
    
    
    
    // fonction qui renvoi enleve les numeros de rue des chaines de caracteres d'adresses
    private function extractNumeroFromAdresseString($strAdresse="")
    {
        $retour="";
        
        for($i=0 ; $i < pia_strlen($strAdresse) ; $i++)
        {
            if(!is_numeric(pia_substr($strAdresse,$i,1)))
                $retour.=pia_substr($strAdresse,$i,1);
        }
        
        
        
        return $retour;
    }
    
    private function getIdAdressesAutourAdressesCourante($strAdresse="")
    {
        $rue="";
        
        $numero="";
        
        $strAdresse = trim($strAdresse);
        
        for($i=0 ; $i < pia_strlen($strAdresse) ; $i++)
        {
            if(!is_numeric(pia_substr($strAdresse,$i,1)))
                $rue.=pia_substr($strAdresse,$i,1);
            else
                $numero.=pia_substr($strAdresse,$i,1);
        }
        
        $numero = trim($numero);

        $motcle = Pia_eregreplace(","," ",$rue);
                
                
        $motcleEscaped = mysql_escape_string($motcle);
        $motcleEscaped = str_replace(' ','%',$motcleEscaped);
        
        // on va chercher le numero les plus proche autour de l'adresse entree par l'utilisateur
        $sqlMotCle="
            AND
            (
                CONCAT_WS('',ind.nom,r.prefixe,r.nom,sq.nom, q.nom, v.nom, p.nom) LIKE \"%".$motcleEscaped."%\"
            )

            ";
            
        $arrayIdAdresse=array();
        if($numero!='')
        {       
            $arraySqlHaving[]=array("having"=>"ha1.numero=max(ha3.numero)","select"=>"AND ha3.numero<$numero");
            $arraySqlHaving[]=array("having"=>"ha1.numero=min(ha3.numero)","select"=>"AND ha3.numero>$numero");
            
            foreach($arraySqlHaving as $indice => $sqlOpt)
            {
                $sql = "
                        SELECT distinct ha1.idAdresse as idAdresse,ha1.numero,ha3.numero
                        FROM historiqueAdresse ha3,historiqueAdresse ha2,historiqueAdresse ha1
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        
                        LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                        
                        LEFT JOIN rue r         ON r.idRue = ha1.idRue
                        LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                        LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                        LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                        LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                        
                        WHERE
                            ha2.idAdresse = ha1.idAdresse 
                        
                        ".$sqlOpt['select']."
                        
                        AND ha1.numero<>''
                        AND ha3.idRue = ha1.idRue
                            ".$sqlMotCle." 
                        AND ae.idAdresse IS NOT NULL
                        GROUP BY ha3.idRue,ha1.idAdresse,ha1.idHistoriqueAdresse
                        HAVING ".$sqlOpt['having']." AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                        
                        ";
                
                $res = $this->connexionBdd->requete($sql);
                            
                while($fetch = mysql_fetch_assoc($res))
                {
                    $arrayIdAdresse[]=$fetch['idAdresse'];
                }
            }
        }
        
        
        return array("sqlMotCle"=>$sqlMotCle,"arrayIdAdresses"=>$arrayIdAdresse);
    }
    
    
    
    // renvoi les elements d'une adresse d'un groupe d'adresse donné , on ne renvoi pas toutes les adresses , c juste pour l'url rewriting
    public function getFetchOneAdresseElementsFromGroupeAdresse($idEvenementGroupeAdresse=0)
    {
        $req = "SELECT ha1.idAdresse as idAdresse, ha1.numero, ha1.idQuartier, ha1.idVille,ind.nom,
                ae.idEvenement as idEvenementGA,
                r.nom as nomRue,
                sq.nom as nomSousQuartier,
                q.nom as nomQuartier,
                v.nom as nomVille,
                p.nom as nomPays,
                ha1.numero as numeroAdresse, 
                ha1.idRue,
                r.prefixe as prefixeRue,
                IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                
                ha1.numero as numero,
                ha1.idHistoriqueAdresse,
                ha1.idIndicatif as idIndicatif
            
                FROM _adresseEvenement ae
                
                LEFT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                
                LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                LEFT JOIN rue r         ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                
                
                WHERE ae.idEvenement = $idEvenementGroupeAdresse
                
                GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                LIMIT 1
            ";
            $res = $this->connexionBdd->requete($req);
        
        return mysql_fetch_assoc($res);
    }
    
    
    // ***************************************************************************************************************************************
    // renvoi l'adresse mise en forme
    // ***************************************************************************************************************************************
    public function getIntituleAdresse($fetch=array(),$params=array())
    {
        $idAdresse = 0;
        // pour pouvoir afficher plusieurs adresses d'un meme groupe d'adresse si c'est le cas , on verifie si le parametre est precisé, sinon on garde le parametre d'origine en le placant juste dans un tableau
        if(isset($params['arrayIdAdressesSurMemeGroupeAdresse']) && count($params['arrayIdAdressesSurMemeGroupeAdresse'])>0)
        {
            $arrayFetch = $params['arrayIdAdressesSurMemeGroupeAdresse'];
        }
        else
        {
            $arrayFetch[] = $fetch;
        }
        
        

        
        if(isset($params['idAdresseReference']) && $params['idAdresseReference']!=0)
        {
            // si on passe une adresse de reference en parametres (idAdresse de la page courante par exemple) , on a la regle suivante
            // si le quartier et la ville sont les memes que ceux de l'adresse de reference on affiche pas le quartier et la ville de l'adresse en sortie
            $arrayAdresse=$this->getArrayAdresseFromIdAdresse($params['idAdresseReference']);
            $idSousQuartierAdresseReference = $arrayAdresse['idSousQuartier'];
            $idQuartierAdresseReference = $arrayAdresse['idQuartier'];
            $idVilleAdresseReference = $arrayAdresse['idVille'];
        }
        
        $separatorAfterTitle ='';
        if(isset($params['setSeparatorAfterTitle']))
        {
            $separatorAfterTitle = $params['setSeparatorAfterTitle'];
        }
        
        $titre="";
        $styleCSSTitre ='font-weight:bold;';
        $styleCSSAdresse ='';
        $classCSS='';
        if(isset($params['classCSSTitreAdresse']))
        {
            $classCSS="class='".$params['classCSSTitreAdresse']."'";
        }
        
        
        if(isset($params['styleCSSTitreAdresse']))
        {
            $styleCSSTitre = $params['styleCSSTitreAdresse'];
        }
        
        
        if(isset($params['styleCSSAdresse']))
        {
            $styleCSSAdresse = $params['styleCSSAdresse'];
        }
        
        if(isset($params['displayFirstTitreAdresse']) && $params['displayFirstTitreAdresse']==true || (isset($params['ifTitreAfficheTitreSeulement']) && $params['ifTitreAfficheTitreSeulement']==true) || (isset($params['afficheTitreSiTitreSinonRien']) && $params['afficheTitreSiTitreSinonRien']==true))
        {
            $sqlGroupeAdresse ="";
            if(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && $params['idEvenementGroupeAdresse']!='0')
            {
                $sqlGroupeAdresse = " AND ae.idEvenement = '".$params['idEvenementGroupeAdresse']."' ";
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($params['idEvenementGroupeAdresse']);
            }
            
            if(isset($arrayFetch[0]['idAdresse'])) {
                $idAdresse = $arrayFetch[0]['idAdresse']; // on prend le premier idAdresse trouvé , en principe c'est toujours le cas
            }
            
            $trouve = false;
            
            
            // on regarde d'abord s'il existe un titre pour le groupe d'adresse
            // vu qu'un evenement groupe d'adresse est unique , on ne va pas grouper dans la requete
            $reqVerif = "
            SELECT idEvenementRecuperationTitre 
            FROM historiqueEvenement he
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = '$idAdresse'
            WHERE he.idEvenement = ae.idEvenement
            $sqlGroupeAdresse
            ";
            
            $resVerif = $this->connexionBdd->requete($reqVerif);
            if(mysql_num_rows($resVerif)>0)
            {
                $fetchVerif = mysql_fetch_assoc($resVerif);
                if($fetchVerif['idEvenementRecuperationTitre']=='0')
                {
                    $trouve=false;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']=='-1')
                {
                    $params['ifTitreAfficheTitreSeulement']=true;
                    $titre='';
                    $trouve=true;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']!='0')
                {
                    $reqTitre = "
                        SELECT he1.titre as titre
                        FROM historiqueEvenement he2, historiqueEvenement he1
                        WHERE he2.idEvenement = he1.idEvenement
                        AND he1.idEvenement = '".$fetchVerif['idEvenementRecuperationTitre']."'
                        GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)                       
                    ";
                    $resTitre = $this->connexionBdd->requete($reqTitre);
                    
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    if (isset($params["noHTML"])) {
                        $titre=stripslashes($fetchTitre['titre']);
                    } else {
                        $titre = "<span $classCSS style='$styleCSSTitre'>".stripslashes($fetchTitre['titre'])."</span> ";
                    }
                    if(trim($fetchTitre['titre'])!='')
                    {
                        $trouve=true;
                        
                    }
                    else
                    {
                        $trouve=true; // meme si pas de titre , ceci va permettre d'afficher l'adresse
                        $titre='';
                    }
                    
                }
            }
            
            if(!$trouve)
            {
                // avec ce parametre , on va aller chercher le premier titre rencontré sur la liste des evenements du groupe d'adresse de l'adresse
                
                $reqTitre = "
                        SELECT he1.titre as titre
                        FROM _adresseEvenement ae
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                        LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                        WHERE he1.titre!=''
                        AND ae.idAdresse = '".$idAdresse."'
                         $sqlGroupeAdresse
                        AND he1.idTypeEvenement <>'6'
                        GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                        ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
                        LIMIT 1
                
                ";
                $resTitre = $this->connexionBdd->requete($reqTitre);
                if(mysql_num_rows($resTitre)==1)
                {
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    if (isset($params["noHTML"])) {
                        $titre=stripslashes($fetchTitre['titre']);
                    } else {
                        $titre = "<b $classCSS>".stripslashes($fetchTitre['titre'])."</b> ";
                    }
                    if(trim($fetchTitre['titre'])=='')
                    {
                        $noTitreDetected=true;
                        $titre='';
                    }
                }
            }
        }
        

        
        $arrayNomAdresse = array();
        $arrayNomRue = array();
        $arrayNomSousQuartier = array();
        $arrayNomQuartier = array();
        $arrayNomVille = array();
        $arrayNomAdresseSansQuartierSsQuartierVilleRue=array();
        $arrayNomAdresseSansQuartierSsQuartierVille = array();
        $arrayAdressesRegroupees = array();
        $arrayNomQuartiersRegroupes = array();
        $arrayNomVillesRegroupes = array();
        $arrayNomSousQuartiersRegroupes = array();
        $htmlStyleDebut = "";
        $htmlStyleFin = "";

        $i=0;
        
        
        
        
        foreach($arrayFetch as $indice => $fetch)
        {
            if((isset($params['displayLibelleAdresseFromGroupeAdresseOnlyIdAdresse']) && $params['displayLibelleAdresseFromGroupeAdresseOnlyIdAdresse']==$fetch['idAdresse'] )|| !isset($params['displayLibelleAdresseFromGroupeAdresseOnlyIdAdresse']))
            {
                $nomAdresse = "";
                if(isset($fetch['numero']) && $fetch['numero']!='' && $fetch['numero']!='0')
                    $nomAdresse .= $fetch['numero'];
                
                
                // recherche de l'indicatif 
                if(isset($fetch['idIndicatif']) && $fetch['idIndicatif']!='0')
                {
                    $reqIndicatif = "SELECT nom FROM indicatif WHERE idIndicatif='".$fetch['idIndicatif']."'";
                    $resIndicatif = $this->connexionBdd->requete($reqIndicatif);
                    $fetchIndicatif = mysql_fetch_assoc($resIndicatif);
                    
                    $nomAdresse .=$fetchIndicatif['nom'];
                    $fetch['indicatif'] = $fetchIndicatif['nom']; // pour le regroupement , pour avoir l'indicatif
                }
                else
                {
                    $fetch['indicatif'] = '';
                }
                
                $arrayNomAdresseSansQuartierSsQuartierVilleRue[$i] = $nomAdresse;

                if(isset($fetch['prefixeRue']))
                {
                    if(pia_substr($fetch['prefixeRue'],0,pia_strlen($fetch['prefixeRue']))=="'")
                        $nomAdresse.=' '.stripslashes($fetch['prefixeRue']).stripslashes(ucfirst($fetch['nomRue']));
                    else
                        $nomAdresse.=' '.stripslashes($fetch['prefixeRue']).' '.stripslashes(ucfirst($fetch['nomRue']));
                    
                    $arrayNomRue[$i] = stripslashes($fetch['prefixeRue']).' '.stripslashes(ucfirst($fetch['nomRue']));
                }

                
                
                $arrayNomAdresseSansQuartierSsQuartierVille[$i] = $nomAdresse;
                
                if(!isset($params['noSousQuartier']) || $params['noSousQuartier']==false)
                {
                    if(isset($fetch['nomSousQuartier']) && $fetch['nomSousQuartier']!='autre')
                    {
                        if(isset($params['idAdresseReference']) && $params['idAdresseReference']!=0 && $idSousQuartierAdresseReference ==$fetch['idSousQuartier'])
                        {
                            // on ne precise pas le sous quartier si c'est le meme que l'adresse de reference
                        }
                        else
                        {
                            $sousquartier=true;
                            if (!empty($fetch['nomSousQuartier'])) {
                                $nomAdresse .= ' ('.ucfirst($fetch['nomSousQuartier']);
                            }
                            $arrayNomSousQuartier[$i] = ucfirst($fetch['nomSousQuartier']);
                        }
                    }
                    
                    
                }
                
                if(isset($fetch['idSousQuartier'])) {
                    $arrayNomSousQuartiersRegroupes[$fetch['idSousQuartier'].$fetch['nomSousQuartier']][]=1;
                }


                if((!isset($params['noQuartier']) || $params['noQuartier']==false) || (isset($params['noQuartier']) && $params['noQuartier']==true && (!isset($fetch['idSousQuartier']) ||$fetch['idSousQuartier']=='0') || (!isset($fetch['idRue']) || $fetch['idRue']=='0') && isset($fetch['idQuartier']) && $fetch['idQuartier']!='0'))
                {
                    
                    if(isset($fetch['nomQuartier']) && $fetch['nomQuartier']!='autre')
                    {
                        if(isset($params['noQuartierCentreVille']) && $params['noQuartierCentreVille']==true && pia_strtolower($fetch['nomQuartier'])=="centre ville")
                        {
                            //$nomAdresse .= ' '.ucfirst($fetch['nomQuartier']);
                            if ($fetch['nomSousQuartier']=="autre") {
                                $nomAdresse .=" - ";
                            } else {
                                $nomAdresse .=") ";
                            }
                        }
                        else
                        {
                            if(isset($params['idAdresseReference']) && $params['idAdresseReference']!=0 && $idQuartierAdresseReference==$fetch['idQuartier'])
                            {
                                // on ne precise pas le quartier si c'est le meme que l'adresse de reference ( si celle ci est précisée)
                                if((!isset($fetch['idRue']) || $fetch['idRue']=='0')) // sauf s'il n'y a pas d'idRue , sinon on afficherait rien
                                {
                                    $nomAdresse .= ' '.ucfirst($fetch['nomQuartier']);
                                    $arrayNomQuartier[$i] = ucfirst($fetch['nomQuartier']);
                                } else if ($fetch['nomSousQuartier']!="Ellipse insulaire") {
                                    //$nomAdresse .=") ";
                                }
                            }
                            else
                            {
                                if (!empty($fetch['nomQuartier'])) {
                                    if (!empty($fetch['nomSousQuartier'])) {
                                        $nomAdresse .= isset($sousquartier)?' - ':' (';
                                    } else {
                                        $nomAdresse .= ' (';
                                    }
                                    $nomAdresse .= ucfirst($fetch['nomQuartier']).")";
                                }
                                $arrayNomQuartier[$i] = ucfirst($fetch['nomQuartier']);
                            }
                        }
                    }
                    
                }

                if(isset($fetch['idQuartier']))
                    $arrayNomQuartiersRegroupes[$fetch['idQuartier'].$fetch['nomQuartier']][]=1;
                
                

                
                
                if(!isset($params['noVille']) ||$params['noVille']==false)
                {   
                    if(isset($fetch['nomVille'])) //  && $fetch['nomVille']!='Strasbourg'
                    {
                        if(isset($params['idAdresseReference']) && $params['idAdresseReference']!=0 && $idVilleAdresseReference ==$fetch['idVille'])
                        {
                            // on ne precise pas la ville si c'est la meme que la ville de l'adresse de reference
                        }
                        else
                        {
                            $nomAdresse.= ' '.ucfirst($fetch['nomVille']);
                            $arrayNomVille[$i] = ucfirst($fetch['nomVille']);
                        }
                    }

                    
                }
                if(isset($fetch['idVille']))
                    $arrayNomVillesRegroupes[$fetch['idVille'].$fetch['nomVille']][]=1;
                
                if(isset($params['isAfficheAdresseStyle']) && $titre!='' && !isset($noTitreDetected))
                {
                    $nomAdresse="<span $styleCSSAdresse>".$nomAdresse."</span>";
                    $htmlStyleDebut = "<span $styleCSSAdresse>";
                    $htmlStyleFin = "</span>";
                }
                
                $arrayNomAdresse[$i] = $nomAdresse;
                if(isset($arrayNomRue[$i]))
                {
                    $arrayAdressesRegroupees[$arrayNomRue[$i]][] = $fetch;
                }
                $i++;
            }
        }


        
        
        $retour="";
        if(isset($params['afficheTitreSiTitreSinonRien']) && $params['afficheTitreSiTitreSinonRien']==true) {
            $retour = $titre;
        } else {
            if(isset($params['ifTitreAfficheTitreSeulement']) && $params['ifTitreAfficheTitreSeulement']==true && $titre!='')
            {
                $retour = $titre;
            }
            else
            {
                // on regarde si tous les quartiers et sous quartiers et villes des adresses du groupe d'adresse sont les memes , s'il y a plusieurs adresses , on factorises le quartier le sous quartier et la ville
                if(count($arrayNomAdresse)>1)
                {
                    /*
                    $ok = true;
                    $okRue = true;
                    $quartier = "";
                    $sousQuartier = "";
                    $ville = "";
                    $rue = "";

                    $arrayUniqueRue = array_unique($arrayNomRue);
                    if(count($arrayUniqueRue)==1 && count($arrayNomRue)>1)
                    {
                        if(count($arrayUniqueRue)==1)
                        {   
                            $rue = " ".$arrayNomRue[0];
                        }
                    }
                    else
                    {
                        $ok = false;
                    }

                    $arrayUniqueQuartier = array_unique($arrayNomQuartier);
                    if(count($arrayUniqueQuartier)==1 && count($arrayNomQuartier)>1 && isset($arrayNomQuartier[0]))
                    {
                        if(count($arrayUniqueQuartier)==1 )
                            $quartier = " ".$arrayNomQuartier[0];
                    }
                    else
                    {
                        $ok = false;
                        $okRue = false;
                    }
                    
                    $arrayUniqueSousQuartier = array_unique($arrayNomSousQuartier);
                    if(count($arrayUniqueSousQuartier)==1 && count($arrayNomSousQuartier)>1)
                    {
                        if( count($arrayUniqueSousQuartier)==1 && isset($arrayNomSousQuartier[0]))
                            $sousQuartier = " ".$arrayNomSousQuartier[0];
                        else
                        {
                            $ok=false;
                            $okRue = false;
                        }
                    }
                    else
                    {
                        $ok = false;
                        $okRue = false;
                    }


                    
                    $arrayUniqueVille = array_unique($arrayNomVille);
                    if(count($arrayUniqueVille)==1 && count($arrayNomVille)>1)
                    {
                        if(count($arrayUniqueVille)==1)
                            $ville = " ".$arrayNomVille[0];
                    }
                    else
                    {
                        $ok = false;
                        $okRue = false;
                    }
                    
                    if($okRue)
                    {   // sans la rue
                        if($titre!='')
                        {
                            $retour = "<span style='$styleCSSTitre'>".$titre."</span>".$separatorAfterTitle.$htmlStyleDebut.implode("/",$arrayNomAdresseSansQuartierSsQuartierVille).$sousQuartier.$quartier.$ville.$htmlStyleFin;
                        }
                        else
                        {
                            $retour = $htmlStyleDebut.implode("/",$arrayNomAdresseSansQuartierSsQuartierVille).$sousQuartier.$quartier.$ville.$htmlStyleFin;
                        }
                    }
                    elseif($ok)
                    {   // on factorise , rue comprise
                        if($titre!='')
                        {
                            $retour  = "<span style='$styleCSSTitre'>".$titre."</span>".$separatorAfterTitle.$htmlStyleDebut.implode("/",$arrayNomAdresseSansQuartierSsQuartierVilleRue).$rue.$sousQuartier.$quartier.$ville.$htmlStyleFin;
                        }
                        else
                        {
                            $retour  = $htmlStyleDebut.implode("/",$arrayNomAdresseSansQuartierSsQuartierVilleRue).$rue.$sousQuartier.$quartier.$ville.$htmlStyleFin;
                        }
                    }
                    else
                    {   // pas de factorisation
                        if($titre!='')
                        {
                            $retour  = "<span style='$styleCSSTitre'>".$titre."</span>".$separatorAfterTitle.implode("/",$arrayNomAdresse);
                        }
                        else
                        {
                            $retour  = implode("/",$arrayNomAdresse);
                        }
                    }
                    */
                    $nomQuartierFactorise = "";
                    $nomVilleFactorise = "";
                    $nomSousQuartierFactorise = "";
                    foreach($arrayAdressesRegroupees as $intituleRue => $fetchRues)
                    {
                        
                        foreach($fetchRues as $indice => $fetchRue)
                        {
                            if($fetchRue['numero']=='0' ||$fetchRue['numero']=='')
                                $retour.='';
                            else
                                $retour.= $fetchRue['numero'].$fetchRue['indicatif']."-";
                        }
                        $retour= pia_substr($retour,0,-(pia_strlen("-")));
                        $retour.=" ";
                        
                        // ici on rajoute le quartier et le sous quartier
                        
                        
                        $retour .= $intituleRue." ";
                        
                        if($fetchRue['nomSousQuartier']!='' && $fetchRue['nomSousQuartier']!='autre' && count($arrayNomSousQuartiersRegroupes)>1)
                        {
                            $retour.= $fetchRue['nomSousQuartier']." ";
                        }
                        else
                        {
                            $nomSousQuartierFactorise = "";
                            if(isset($fetchRue['idSousQuartier']) && $fetchRue['nomSousQuartier']!='' && $fetchRue['nomSousQuartier']!='autre')
                            {
                                $nomSousQuartierFactorise = "(".$fetch['nomSousQuartier']." ";
                            }
                        }
                        
                        if($fetchRue['nomQuartier']!='' && $fetchRue['nomQuartier']!='autre' && count($arrayNomQuartiersRegroupes)>1)
                        {
                            $retour.= $fetchRue['nomQuartier'];
                            
                        }
                        else
                        {
                            $nomQuartierFactorise = "";
                            if($fetchRue['nomQuartier']!='' && $fetchRue['nomQuartier']!='autre')
                            {
                                $nomQuartierFactorise = empty($nomSousQuartierFactorise)?"(":"- ";
                                $nomQuartierFactorise.=$fetchRue['nomQuartier'].") ";
                            } else {
                                $nomQuartierFactorise = ")";
                            }
                        }
                        
                        if($fetchRue['nomVille']!='' && count($arrayNomVillesRegroupes)>1)
                        {
                            $retour.= $fetchRue['nomVille']." ";
                        }
                        else
                        {
                            $nomVilleFactorise = $fetchRue['nomVille']." ";
                        }
                        
                        $retour.="/ ";

                    }
                    
                    $retour= pia_substr($retour,0,-(pia_strlen("/ ")));
                    
                    if(count($arrayNomSousQuartiersRegroupes)==1)
                    {
                        $retour .= $nomSousQuartierFactorise;
                    }
                    
                    if(count($arrayNomQuartiersRegroupes)==1)
                    {
                        $retour .= $nomQuartierFactorise;
                    }
                    
                    if(count($arrayNomVillesRegroupes)==1)
                    {
                        $retour .= $nomVilleFactorise;
                    }
                    
                    $retour.="";
                    
                    if($titre!='')
                    {
                        $retour = "<span style='$styleCSSTitre'>".$titre."</span>".$separatorAfterTitle.$htmlStyleDebut.$retour.$htmlStyleFin;
                    }
                    else
                    {
                        // retour = retour
                    }
                
                } else {
                    if($titre!='')
                    {
                        $retour = "<span style='$styleCSSTitre'>".$titre."</span>".$separatorAfterTitle.implode("/",$arrayNomAdresse);
                    }
                    else
                    {
                        $retour = implode("/",$arrayNomAdresse);
                    }
                }
            }
        }
        return $retour;
    }
    
    
    // ***************************************************************************************************************************************
    // renvoi l'adresse mise en forme pour la page d'accueil (adresse ne concernant que strasbourg , donc si il y a une rue , on affiche pas le quartier , etc ...
    // ***************************************************************************************************************************************
    public function getIntituleAdresseAccueil($fetch=array(),$params=array())
    {
        $titre="";
        $sqlGA="";
        $classCSS="";
        if(isset($fetch['idEvenementGroupeAdresse']) && $fetch['idEvenementGroupeAdresse']!='' && $fetch['idEvenementGroupeAdresse']!='0')
        {
            $sqlGA="AND ae.idEvenement = ".$fetch['idEvenementGroupeAdresse'];
        }
        
        if(isset($params['displayFirstTitreAdresse']) && $params['displayFirstTitreAdresse']==true || (isset($params['ifTitreAfficheTitreSeulement']) && $params['ifTitreAfficheTitreSeulement']==true))
        {
        
            $classCSS='';
            if(isset($params['classCSSTitreAdresse']))
            {
                $classCSS="class='".$params['classCSSTitreAdresse']."'";
            }
            
            // avec ce parametre , on va aller chercher le premier titre rencontré sur la liste des evenements du groupe d'adresse de l'adresse
            $idAdresse = $fetch['idAdresse'];
            
            
            $trouve = false;
            
            // on regarde d'abord s'il existe un titre pour le groupe d'adresse
            // vu qu'un evenement groupe d'adresse est unique , on ne va pas grouper dans la requete
            $reqVerif = "
            SELECT idEvenementRecuperationTitre 
            FROM historiqueEvenement he
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = $idAdresse
            WHERE he.idEvenement = ae.idEvenement
            $sqlGA
            ";

            
            $resVerif = $this->connexionBdd->requete($reqVerif);
            if(mysql_num_rows($resVerif)>0)
            {

                $fetchVerif = mysql_fetch_assoc($resVerif);
                if($fetchVerif['idEvenementRecuperationTitre']=='0')
                {
                    $trouve=false;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']=='-1')
                {
                    $params['ifTitreAfficheTitreSeulement']=true;
                    $titre='';
                    $trouve=true;
                }
                elseif($fetchVerif['idEvenementRecuperationTitre']!='0' && $fetchVerif['idEvenementRecuperationTitre']!='-1')
                {

                    $reqTitre = "
                        SELECT he1.titre as titre
                        FROM historiqueEvenement he2, historiqueEvenement he1
                        WHERE he2.idEvenement = he1.idEvenement
                        AND he1.idEvenement = '".$fetchVerif['idEvenementRecuperationTitre']."'
                        GROUP BY he1.idEvenement,he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)                       
                    ";
                    $resTitre = $this->connexionBdd->requete($reqTitre);
                    
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    $titre = "<span $classCSS>".stripslashes($fetchTitre['titre'])."</span> ";
                    $trouve=true;
                    
                }
            }
            
            if(!$trouve)
            {

                $reqTitre = "
                        SELECT he1.titre as titre
                        FROM _adresseEvenement ae
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                        LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                        WHERE he1.titre!=''
                        AND he1.idTypeEvenement <>'6'
                        AND ae.idAdresse = '".$idAdresse."'
                        $sqlGA
                        GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                        HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                        ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
                        LIMIT 1
                
                ";
                $resTitre = $this->connexionBdd->requete($reqTitre);
                if(mysql_num_rows($resTitre)==1)
                {
                    $fetchTitre = mysql_fetch_assoc($resTitre);
                    $titre = "<span $classCSS>".stripslashes($fetchTitre['titre'])."</span> ";
                }
            }
            
        }
    
    
    
        $nomAdresse = "";
        if(isset($fetch['numero']) && $fetch['numero']!='' && $fetch['numero']!='0')
            $nomAdresse .= $fetch['numero'];
        
        
        // recherche de l'indicatif 
        if(isset($fetch['idIndicatif']) && $fetch['idIndicatif']!='0')
        {
            $reqIndicatif = "SELECT nom FROM indicatif WHERE idIndicatif='".$fetch['idIndicatif']."'";
            $resIndicatif = $this->connexionBdd->requete($reqIndicatif);
            $fetchIndicatif = mysql_fetch_assoc($resIndicatif);
            
            $nomAdresse .=$fetchIndicatif['nom'];
        }
        
        if(isset($fetch['prefixeRue']))
        {
            if(pia_substr($fetch['prefixeRue'],0,pia_strlen($fetch['prefixeRue']))=="'")
                $nomAdresse.=' '.$fetch['prefixeRue'].ucfirst($fetch['nomRue']);
            else
                $nomAdresse.=' '.$fetch['prefixeRue'].' '.ucfirst($fetch['nomRue']);
        }
        
        if((!isset($params['noQuartier']) || $params['noQuartier']==false) && $fetch['nomRue']=='')
        {
            if(isset($fetch['nomQuartier']) && $fetch['nomQuartier']!='autre')
            {
                if(isset($params['noQuartierCentreVille']) && $params['noQuartierCentreVille']==true && pia_strtolower($fetch['nomQuartier'])=="centre ville")
                {
                    //$nomAdresse .= ' '.ucfirst($fetch['nomQuartier']);
                }
                else
                {
                    $nomAdresse .= ' '.ucfirst($fetch['nomQuartier']);
                }
            }
        }
        
        if((!isset($params['noSousQuartier']) || $params['noSousQuartier']==false) && $fetch['nomRue']=='')
        {
            if(isset($fetch['nomSousQuartier']) && $fetch['nomSousQuartier']!='autre')
                $nomAdresse .= ' '.ucfirst($fetch['nomSousQuartier']);
        }
        
        if(isset($fetch['nomVille']) && $fetch['nomRue']=='') //  && $fetch['nomVille']!='Strasbourg'
            $nomAdresse.= ' '.ucfirst($fetch['nomVille']);
        
        $nomAdresse = trim($nomAdresse);
        
        $retour="";
        if(isset($params['ifTitreAfficheTitreSeulement']) && $params['ifTitreAfficheTitreSeulement']==true && $titre!='' && $titre!="<span $classCSS></span> ")
            $retour = $titre;
        else
        {
            if($titre=="<span $classCSS></span> ")
            {
                $titre = "";
            }
            $retour = $titre.$nomAdresse;
        }
        
        
        return $retour;
    }
    
    
    
    // ***************************************************************************************************************************************
    // recuperation d'une image appartenant a une adresse de la rue , on regarde en premier s'il en existe une de position "1"
    // ***************************************************************************************************************************************
    public function getUrlImageFromRue($idRue,$format='mini')
    {
        $url="";
        $string = new stringObject();
        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        

        // on regarde s'il y a une image de position 1
        $queryAdresse="
        
        SELECT hi1.idHistoriqueImage as idHistoriqueImage , hi1.idImage as idImage, hi1.nom as nom , hi1.dateUpload as dateUpload , hi1.dateCliche as dateCliche
        FROM historiqueAdresse ha2,historiqueAdresse ha1
        
        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
        
        RIGHT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
        RIGHT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
        
        WHERE ha1.idRue='".$idRue."'
        AND ha2.idAdresse = ha1.idAdresse
        AND ei.position='1'
        GROUP BY hi1.idImage,ha1.idAdresse, hi1.idHistoriqueImage,ha1.idHistoriqueAdresse
        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        LIMIT 1
        ";
        
        
        
        
        
    
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        if(mysql_num_rows($resAdresse)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            //$url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            $intituleAdresse = $this->getIntituleAdresseFrom($idRue,'idRue',array('debug'=>true,'noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'','displayFirstTitreAdresse'=>false,'setSeparatorAfterTitle'=>'_'));
            $url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchAdresse['dateUpload'].'-'.$fetchAdresse['idHistoriqueImage'].'-'.$format.'.jpg';
        }
        else
        {
            // on recherche une image appartenant a un evenement de l'adresse
            // recherche des evenements de l'adresse:
            //$queryEvenements ="SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresse."'";
            
            $queryEvenements="
                                SELECT ae.idEvenement as idEvenement
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                WHERE ha2.idAdresse = ha.idAdresse
                                AND ha.idRue = '".$idRue."'
                                GROUP BY ha.idAdresse , ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            ";
            

            //echo $queryEvenements."<br>";
            $resEvenements = $this->connexionBdd->requete($queryEvenements);
            $arrayListeEvenementsGroupeAdresse=array();
            while($fetchEvenement = mysql_fetch_assoc($resEvenements))
            {
                $arrayListeEvenementsGroupeAdresse[] = $fetchEvenement['idEvenement'];
            }
            
            if(count($arrayListeEvenementsGroupeAdresse)>0)
            {
                // on recherche les evenements du groupe d'adresses
                $listeEvenementsGroupeAdresse = implode("','",$arrayListeEvenementsGroupeAdresse);
                $queryEvenementAssocies = "
                                    SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenement in ('".$listeEvenementsGroupeAdresse."')
                ";
                
                $resEvenementsAssocies = $this->connexionBdd->requete($queryEvenementAssocies);
                
                $arrayListeEvenementsAssocies=array();
                while($fetchEvenementsAssocies = mysql_fetch_assoc($resEvenementsAssocies))
                {
                    $arrayListeEvenementsAssocies[] = $fetchEvenementsAssocies['idEvenementAssocie'];
                
                }
            
                $listeEvenementsAssocies = implode("','",$arrayListeEvenementsAssocies);
                
                $queryImage = " SELECT hi.idImage as idImage , hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload
                                FROM historiqueImage hi2, historiqueImage hi
                                RIGHT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                WHERE ei.idEvenement in ('".$listeEvenementsAssocies."')
                                AND hi2.idImage = hi.idImage
                                GROUP BY hi.idImage , hi.idHistoriqueImage
                                HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                                LIMIT 1
                "; // on limit a 1 sinon cela peut prendre du temps
                
                $resImage = $this->connexionBdd->requete($queryImage);
                if(mysql_num_rows($resImage)>0)
                {
                    $fetchImage = mysql_fetch_assoc($resImage);
                    //$url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                    $intituleAdresse = $this->getIntituleAdresseFrom($idRue,'idRue',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'','displayFirstTitreAdresse'=>false,'setSeparatorAfterTitle'=>'_'));
                    $url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchImage['dateUpload'].'-'.$fetchImage['idHistoriqueImage'].'-'.$format.'.jpg';
                }
                else
                {
                    $url = $this->getUrlImage()."/transparent.gif";
                }
            }
        }
        return $url;
    }
    
    public function getIdImageFromRue($idRue,$format='mini')
    {
        $url="";
        $string = new stringObject();
        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        

        // on regarde s'il y a une image de position 1
        $queryAdresse="
        
        SELECT hi1.idHistoriqueImage as idHistoriqueImage , hi1.idImage as idImage, hi1.nom as nom , hi1.dateUpload as dateUpload , hi1.dateCliche as dateCliche
        FROM historiqueAdresse ha2,historiqueAdresse ha1
        
        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
        
        RIGHT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
        RIGHT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
        
        WHERE ha1.idRue='".$idRue."'
        AND ha2.idAdresse = ha1.idAdresse
        AND ei.position='1'
        GROUP BY hi1.idImage,ha1.idAdresse, hi1.idHistoriqueImage,ha1.idHistoriqueAdresse
        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        LIMIT 1
        ";
        
        
        
        
        
    
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        if(mysql_num_rows($resAdresse)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            //$url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            //$intituleAdresse = $this->getIntituleAdresseFrom($idRue,'idRue',array('debug'=>true,'noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'','displayFirstTitreAdresse'=>false,'setSeparatorAfterTitle'=>'_'));
            //$url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchAdresse['dateUpload'].'-'.$fetchAdresse['idHistoriqueImage'].'-'.$format.'.jpg';
            $idImage=$fetchAdresse['idHistoriqueImage'];
        }
        else
        {
            // on recherche une image appartenant a un evenement de l'adresse
            // recherche des evenements de l'adresse:
            //$queryEvenements ="SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresse."'";
            
            $queryEvenements="
                                SELECT ae.idEvenement as idEvenement
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                WHERE ha2.idAdresse = ha.idAdresse
                                AND ha.idRue = '".$idRue."'
                                GROUP BY ha.idAdresse , ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            ";
            

            //echo $queryEvenements."<br>";
            $resEvenements = $this->connexionBdd->requete($queryEvenements);
            $arrayListeEvenementsGroupeAdresse=array();
            while($fetchEvenement = mysql_fetch_assoc($resEvenements))
            {
                $arrayListeEvenementsGroupeAdresse[] = $fetchEvenement['idEvenement'];
            }
            if(count($arrayListeEvenementsGroupeAdresse)>0)
            {
                // on recherche les evenements du groupe d'adresses
                $listeEvenementsGroupeAdresse = implode("','",$arrayListeEvenementsGroupeAdresse);
                $queryEvenementAssocies = "
                                    SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenement in ('".$listeEvenementsGroupeAdresse."')
                ";
                
                $resEvenementsAssocies = $this->connexionBdd->requete($queryEvenementAssocies);
                
                $arrayListeEvenementsAssocies=array();
                while($fetchEvenementsAssocies = mysql_fetch_assoc($resEvenementsAssocies))
                {
                    $arrayListeEvenementsAssocies[] = $fetchEvenementsAssocies['idEvenementAssocie'];
                
                }
            
                $listeEvenementsAssocies = implode("','",$arrayListeEvenementsAssocies);
                
                $queryImage = " SELECT hi.idImage as idImage , hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload
                                FROM historiqueImage hi2, historiqueImage hi
                                RIGHT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                WHERE ei.idEvenement in ('".$listeEvenementsAssocies."')
                                AND hi2.idImage = hi.idImage
                                GROUP BY hi.idImage , hi.idHistoriqueImage
                                HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                                LIMIT 1
                "; // on limit a 1 sinon cela peut prendre du temps
                
                $resImage = $this->connexionBdd->requete($queryImage);
                if(mysql_num_rows($resImage)>0)
                {
                    $fetchImage = mysql_fetch_assoc($resImage);
                    //$url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                    //$intituleAdresse = $this->getIntituleAdresseFrom($idRue,'idRue',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'','displayFirstTitreAdresse'=>false,'setSeparatorAfterTitle'=>'_'));
                    //$url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchImage['dateUpload'].'-'.$fetchImage['idHistoriqueImage'].'-'.$format.'.jpg';
                    $idImage=$fetchImage['idHistoriqueImage'];
                }
                else
                {
                    $url = $this->getUrlImage()."/transparent.gif";
                }
            }
        }
        return $idImage;
    }
    // ***************************************************************************************************************************************
    // recuperation d'une image appartenant a une adresse de la rue
    // ***************************************************************************************************************************************
    public function getUrlImageFromVille($idVille,$format='mini')
    {
        $url="";

        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        
        
        
        $queryQuartier = "
                            SELECT idRue 
                            FROM rue 
                            WHERE idSousQuartier in(SELECT idSousQuartier FROM sousQuartier WHERE idQuartier in 
                            (SELECT idQuartier FROM quartier WHERE idVille = '".$idVille."'))

        ";
        
        $resQuartier = $this->connexionBdd->requete($queryQuartier);
        $arrayRues=array();
        while($fetchQuartier = mysql_fetch_assoc($resQuartier))
        {
            $arrayRues[] = $fetchQuartier['idRue'];
        }

        if(count($arrayRues)>0)
        {
            $sqlRues_reqEvenement = " AND ha.idRue in ('".implode("','",$arrayRues)."') ";
            $sqlRues_reqAdresse = " ai.idAdresse in (select idAdresse FROM historiqueAdresse where  idRue in ('".implode("','",$arrayRues)."')) ";
        }
        else
        {
            $sqlRues_reqEvenement = " AND ha.idVille='".$idVille."' ";
            $sqlRues_reqAdresse = " ai.idAdresse in (select idAdresse FROM historiqueAdresse where  idVille ='".$idVille."') ";
        }
        
        $queryAdresse = "
                        SELECT hi.idHistoriqueImage as idHistoriqueImage , hi.idImage as idImage, hi.nom as nom , hi.dateUpload as dateUpload , hi.dateCliche as dateCliche
                        FROM historiqueImage hi2, historiqueImage hi
                        RIGHT JOIN _adresseImage ai ON ai.idImage = hi.idImage
                        WHERE ".$sqlRues_reqAdresse."
                        AND hi2.idImage = hi.idImage
                        GROUP BY hi.idImage, hi.idHistoriqueImage
                        HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                        LIMIT 1
        ";
        
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        if(mysql_num_rows($resAdresse)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            $url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
        }
        else
        {
            // on recherche une image appartenant a un evenement de l'adresse
            // recherche des evenements de l'adresse:
            //$queryEvenements ="SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresse."'";
            
            $queryEvenements="
                                SELECT ae.idEvenement as idEvenement
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                WHERE ha2.idAdresse = ha.idAdresse
                                ".$sqlRues_reqEvenement."
                                GROUP BY ha.idAdresse , ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            ";
            

            //echo $queryEvenements."<br>";
            $resEvenements = $this->connexionBdd->requete($queryEvenements);
            $arrayListeEvenementsGroupeAdresse=array();
            while($fetchEvenement = mysql_fetch_assoc($resEvenements))
            {
                $arrayListeEvenementsGroupeAdresse[] = $fetchEvenement['idEvenement'];
            }
            
            if(count($arrayListeEvenementsGroupeAdresse)>0)
            {
                // on recherche les evenements du groupe d'adresses
                $listeEvenementsGroupeAdresse = implode("','",$arrayListeEvenementsGroupeAdresse);
                $queryEvenementAssocies = "
                                    SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenement in ('".$listeEvenementsGroupeAdresse."')
                ";
                
                $resEvenementsAssocies = $this->connexionBdd->requete($queryEvenementAssocies);
                
                $arrayListeEvenementsAssocies=array();
                while($fetchEvenementsAssocies = mysql_fetch_assoc($resEvenementsAssocies))
                {
                    $arrayListeEvenementsAssocies[] = $fetchEvenementsAssocies['idEvenementAssocie'];
                
                }
            
                $listeEvenementsAssocies = implode("','",$arrayListeEvenementsAssocies);
                
                $queryImage = " SELECT hi.idImage as idImage , hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload
                                FROM historiqueImage hi2, historiqueImage hi
                                RIGHT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                WHERE ei.idEvenement in ('".$listeEvenementsAssocies."')
                                AND hi2.idImage = hi.idImage
                                GROUP BY hi.idImage , hi.idHistoriqueImage
                                HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                                LIMIT 1
                "; // on limit a 1 sinon cela peut prendre du temps
                
                $resImage = $this->connexionBdd->requete($queryImage);
                if(mysql_num_rows($resImage)>0)
                {
                    $fetchImage = mysql_fetch_assoc($resImage);
                    $url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                }
            }
        }
        return $url;
    }
    // ***************************************************************************************************************************************
    // recuperation d'une image appartenant a une adresse de la rue
    // ***************************************************************************************************************************************
    public function getUrlImageFromQuartier($idQuartier,$format='mini')
    {
        $url="";

        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        
        
        
        $queryQuartier = "
                            SELECT idRue 
                            FROM rue 
                            WHERE idSousQuartier in(SELECT idSousQuartier FROM sousQuartier WHERE idQuartier='".$idQuartier."' )";
        
        $resQuartier = $this->connexionBdd->requete($queryQuartier);
        $arrayRues=array();
        while($fetchQuartier = mysql_fetch_assoc($resQuartier))
        {
            $arrayRues[] = $fetchQuartier['idRue'];
        }
        
        
        $queryAdresse = "
                        SELECT hi.idHistoriqueImage as idHistoriqueImage , hi.idImage as idImage, hi.nom as nom , hi.dateUpload as dateUpload , hi.dateCliche as dateCliche
                        FROM historiqueImage hi2, historiqueImage hi
                        RIGHT JOIN _adresseImage ai ON ai.idImage = hi.idImage
                        WHERE ai.idAdresse in (select idAdresse FROM historiqueAdresse where idRue in ('".implode("','",$arrayRues)."'))
                        AND hi2.idImage = hi.idImage
                        GROUP BY hi.idImage, hi.idHistoriqueImage
                        HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                        LIMIT 1
        ";
        
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        $string = new stringObject();
        if(mysql_num_rows($resAdresse)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            //$url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            $intituleAdresse = $this->getIntituleAdresseFrom($idQuartier,'idQuartier',array('debug'=>true,'noQuartier'=>false,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','displayFirstTitreAdresse'=>true,'setSeparatorAfterTitle'=>'_'));
            $url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchAdresse['dateUpload'].'-'.$fetchAdresse['idHistoriqueImage'].'-'.$format.'.jpg';
        }
        else
        {
            // on recherche une image appartenant a un evenement de l'adresse
            // recherche des evenements de l'adresse:
            //$queryEvenements ="SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresse."'";
            
            $queryEvenements="
                                SELECT ae.idEvenement as idEvenement
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                WHERE ha2.idAdresse = ha.idAdresse
                                AND ha.idRue in ('".implode("','",$arrayRues)."')
                                GROUP BY ha.idAdresse , ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            ";
            

            //echo $queryEvenements."<br>";
            $resEvenements = $this->connexionBdd->requete($queryEvenements);
            $arrayListeEvenementsGroupeAdresse=array();
            while($fetchEvenement = mysql_fetch_assoc($resEvenements))
            {
                $arrayListeEvenementsGroupeAdresse[] = $fetchEvenement['idEvenement'];
            }
            
            if(count($arrayListeEvenementsGroupeAdresse)>0)
            {
                // on recherche les evenements du groupe d'adresses
                $listeEvenementsGroupeAdresse = implode("','",$arrayListeEvenementsGroupeAdresse);
                $queryEvenementAssocies = "
                                    SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenement in ('".$listeEvenementsGroupeAdresse."')
                ";
                
                $resEvenementsAssocies = $this->connexionBdd->requete($queryEvenementAssocies);
                
                $arrayListeEvenementsAssocies=array();
                while($fetchEvenementsAssocies = mysql_fetch_assoc($resEvenementsAssocies))
                {
                    $arrayListeEvenementsAssocies[] = $fetchEvenementsAssocies['idEvenementAssocie'];
                
                }
            
                $listeEvenementsAssocies = implode("','",$arrayListeEvenementsAssocies);
                
                $queryImage = " SELECT hi.idImage as idImage , hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload
                                FROM historiqueImage hi2, historiqueImage hi
                                RIGHT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                WHERE ei.idEvenement in ('".$listeEvenementsAssocies."')
                                AND hi2.idImage = hi.idImage
                                GROUP BY hi.idImage , hi.idHistoriqueImage
                                HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                                LIMIT 1
                "; // on limit a 1 sinon cela peut prendre du temps
                
                $resImage = $this->connexionBdd->requete($queryImage);
                if(mysql_num_rows($resImage)>0)
                {
                    $fetchImage = mysql_fetch_assoc($resImage);
                    
                    $intituleAdresse = $this->getIntituleAdresseFrom($idQuartier,'idQuartier',array('debug'=>true,'noQuartier'=>false,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','displayFirstTitreAdresse'=>true,'setSeparatorAfterTitle'=>'_'));
                    $url = 'photos-'.$string->convertStringToUrlRewrite($intituleAdresse).'-'.$fetchImage['dateUpload'].'-'.$fetchImage['idHistoriqueImage'].'-'.$format.'.jpg';
                    
                    
                    //$url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                }
            }
        }
        return $url;
    }


    public function getUrlImageFromEvenement($idEvenement=0,$format='mini')
    {
        $url="";
        $dateUpload="";
        $idHistoriqueImage="";
        switch($format)
                {
                        case 'mini':
                                $chemin = $this->getUrlImage("mini");
                        break;
                        case 'moyen':
                                $chemin = $this->getUrlImage("moyen");
                        break;
                        case 'grand':
                                $chemin = $this->getUrlImage("grand");
                        break;
                }

        $reqImage="
            SELECT hi.idHistoriqueImage,hi.dateUpload
            FROM historiqueImage hi2, historiqueImage hi
            RIGHT JOIN _evenementImage ei ON ei.idEvenement = '".$idEvenement."' 
            WHERE hi2.idImage = hi.idImage
            AND hi.idImage = ei.idImage
            GROUP BY hi.idImage,hi.idHistoriqueImage
            HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
            LIMIT 1 
        ";
        $resImage = $this->connexionBdd->requete($reqImage);

        if(mysql_num_rows($resImage)==1)
        {
            $fetchImage = mysql_fetch_assoc($resImage);
                        $url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                        $dateUpload = $fetchImage['dateUpload'];
                        $idHistoriqueImage = $fetchImage['idHistoriqueImage'];

        }
             
           if($url=='')
                        $url = $this->getUrlImage().'transparent.gif';
                return array('url'=>$url,'dateUpload'=>$dateUpload,'idHistoriqueImage'=>$idHistoriqueImage);


    }
    
    
    
    
    // ***************************************************************************************************************************************
    // recuperation d'une image appartenant a l'adresse , s'il n'y en a pas , on en recupere une d'un evenement lié a l'adresse
    // ***************************************************************************************************************************************
    public function getUrlImageFromAdresse($idAdresse=0, $format='mini', $params=array())
    {
        $url="";
        $dateUpload = "";
        $idHistoriqueImage = "";
        $string = new stringObject();

        
        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        
        
        
        
        
        
        

        // requete desactivée (limit 0) : on n'affiche pas les images reliee a l'adresse par la table _adresseimage
        $queryAdresse = "
                        SELECT hi.idHistoriqueImage as idHistoriqueImage , hi.idImage as idImage, hi.nom as nom , hi.dateUpload as dateUpload , hi.dateCliche as dateCliche
                        FROM historiqueImage hi2, historiqueImage hi
                        RIGHT JOIN _adresseImage ai ON ai.idImage = hi.idImage
                        WHERE ai.idAdresse = '".$idAdresse."'
                        AND hi2.idImage = hi.idImage
                        GROUP BY hi.idImage, hi.idHistoriqueImage
                        HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                        LIMIT 0
        ";
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        
        if(mysql_num_rows($resAdresse)==1) // toujours 0
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            $url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            $dateUpload = $fetchAdresse['dateUpload'];
            $idHistoriqueImage = $fetchAdresse['idHistoriqueImage'];
        }
        else
        {   
            // on recherche une image appartenant a un evenement de l'adresse
            
            // recherche des evenements de l'adresse:
            $arrayListeEvenementsGroupeAdresse=array();
            $arrayImagePrincipale = array();
            
            $affichageListeAdresseFromSourceTrouvee = false;
            // l'affichage contextuel sur l'ecran des sources est desactive... 
            /*if(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource' && isset($this->variablesGet['source']) && $this->variablesGet['source']!='')
            {
                $reqImagesSource = "
                    SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.idImage as idImage, hi1.dateUpload as dateUpload
                    FROM historiqueImage hi2, historiqueImage hi1
                    LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                    WHERE hi2.idImage = hi1.idImage
                    AND hi1.idSource='".$this->variablesGet['source']."'
                    AND ee.idEvenement='".$params['idEvenementGroupeAdresse']."'
                    GROUP BY hi1.idImage , hi1.idHistoriqueImage
                    HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                    LIMIT 1
                ";
                
                $resImagesSource = $this->connexionBdd->requete($reqImagesSource);
                
                if(mysql_num_rows($resImagesSource)==1)
                {
                    $fetchImagesSource = mysql_fetch_assoc($resImagesSource);
                    $dateUpload = $fetchImagesSource['dateUpload'];
                    $idHistoriqueImage = $fetchImagesSource['idHistoriqueImage'];
                    $url = $chemin.$dateUpload."/".$idHistoriqueImage.".jpg";
                    $affichageListeAdresseFromSourceTrouvee = true;
                }
                
            }
            */
            
            $affichageListeAdressesPersonneImageTrouvee = false;
            if(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='evenementListe' && isset($this->variablesGet['selection']) && $this->variablesGet['selection']=='personne' && isset($this->variablesGet['id']) && $this->variablesGet['id']!='' && isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && !$affichageListeAdresseFromSourceTrouvee)
            {
                // si on est sur l'affichage de la liste des adresses d'un architecte par exemple , on va chercher les photos sur les evenements concernant l'architecte
                // verifions s'il y a au moins une photo sur l'evenement concerné
                
                // ajout 29/07/2010
                // si l'image principale se situe sur l'evenement concerné , on affiche l'image principale , sinon une image de l'evenement
                $image = new archiImage();
                $arrayImagePrincipale = $image->getArrayInfosImagePrincipaleFromIdGroupeAdresse(array('idEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse'],'format'=>$format));
                if($arrayImagePrincipale['trouve'])
                {
                    $affichageListeAdressesPersonneImageTrouvee=true;
                    $dateUpload = $arrayImagePrincipale['dateUpload'];
                    $idHistoriqueImage = $arrayImagePrincipale['idHistoriqueImage'];
                    $url = $chemin.$dateUpload."/".$idHistoriqueImage.".jpg";
                }
                else
                {
                
                
                    $reqPersonneEvenement = "
                        SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.idImage as idImage, hi1.dateUpload as dateUpload
                        FROM historiqueImage hi2,historiqueImage hi1
                        LEFT JOIN _evenementPersonne ep ON ep.idPersonne = ".mysql_real_escape_string($this->variablesGet['id'])."
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie=ep.idEvenement
                        LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
                        WHERE ee.idEvenement=".mysql_real_escape_string($params['idEvenementGroupeAdresse'])."
                        AND hi1.idImage = ei.idImage
                        AND hi2.idImage = hi1.idImage
                        GROUP BY hi1.idImage, hi1.idHistoriqueImage
                        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) 
                        ORDER BY ei.position DESC,hi1.idHistoriqueImage ASC
                        LIMIT 1
                    ";
                    $resPersonneEvenement = $this->connexionBdd->requete($reqPersonneEvenement);
                    if(mysql_num_rows($resPersonneEvenement)==1)
                    {
                        $fetchPersonneEvenement = mysql_fetch_assoc($resPersonneEvenement);
                        $affichageListeAdressesPersonneImageTrouvee=true;
                        
                        $dateUpload = $fetchPersonneEvenement['dateUpload'];
                        $idHistoriqueImage = $fetchPersonneEvenement['idHistoriqueImage'];
                        $url = $chemin.$dateUpload."/".$idHistoriqueImage.".jpg";
                    }
                }
            }
            
            if(isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='' && $params['idEvenementGroupeAdresse']!='0' && !$affichageListeAdressesPersonneImageTrouvee && !$affichageListeAdresseFromSourceTrouvee)
            {
                $image = new archiImage();
                
                $arrayListeEvenementsGroupeAdresse[] =$params['idEvenementGroupeAdresse'];
                
                // si un groupe d'adresse est precisé on va d'abord regarder s'il une image principale est selectionnee
                $arrayImagePrincipale = $image->getArrayInfosImagePrincipaleFromIdGroupeAdresse(array('idEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse'],'format'=>$format));
                
                
            }
            elseif(!$affichageListeAdressesPersonneImageTrouvee && !$affichageListeAdresseFromSourceTrouvee)
            {
                $queryEvenements ="SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresse."'";
                
                $resEvenements = $this->connexionBdd->requete($queryEvenements);
                
                while($fetchEvenement = mysql_fetch_assoc($resEvenements))
                {
                    $arrayListeEvenementsGroupeAdresse[] = $fetchEvenement['idEvenement'];
                }
            }
            
            
            if(isset($arrayImagePrincipale['trouve']) && $arrayImagePrincipale['trouve']==true && !$affichageListeAdressesPersonneImageTrouvee && !$affichageListeAdresseFromSourceTrouvee)
            {
                // on a trouve une photo principale sur l'evenement groupe adresse
                $url = $arrayImagePrincipale['url'];
                $dateUpload = $arrayImagePrincipale['dateUpload'];
                $idHistoriqueImage = $arrayImagePrincipale['idHistoriqueImage'];
            }
            elseif(count($arrayListeEvenementsGroupeAdresse)>0 && !$affichageListeAdressesPersonneImageTrouvee && !$affichageListeAdresseFromSourceTrouvee)
            {
                // si on est sur l'affichage des adresses concernant une personne (architecte etc ...)
                // on affiche une photo de l'evenement concerné par cette personne si il y a des photos , sinon on ne met pas ce critere dans la requete de recherche de photo
                $arrayCritereListeIdEvenementPersonne=array();
                if(isset($this->variablesGet['selection']) && $this->variablesGet['selection']=='personne')
                {
                    // voyons d'abord s'il y a des photos sur l'evenement concerné par l'architect , sinon ce n'est pas la peine d'ajouter ce critere a la recherche de la photo
                    $reqIsPhotoSurEvenement = "
                                SELECT idEvenementAssocie as idEvenementPhotoPersonne
                                FROM _evenementEvenement ee
                                LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
                                LEFT JOIN _evenementPersonne ep ON ep.idEvenement = ee.idEvenementAssocie
                                WHERE ee.idEvenement IN (".implode(",",$arrayListeEvenementsGroupeAdresse).")
                                AND ep.idPersonne = '".$this->variablesGet['id']."'
                                GROUP BY ei.idEvenement
                                HAVING count(ei.idImage)>0
                            ";
                    $resIsPhotoSurEvenement = $this->connexionBdd->requete($reqIsPhotoSurEvenement);
                    
                    if(mysql_num_rows($resIsPhotoSurEvenement)>0)
                    {
                        while($fetchIsPhotoSurEvenement = mysql_fetch_assoc($resIsPhotoSurEvenement))
                        {
                            $arrayCritereListeIdEvenementPersonne[] = $fetchIsPhotoSurEvenement['idEvenementPhotoPersonne'];
                        }
                        
                        $listeEvenementsAssocies = implode("','",$arrayCritereListeIdEvenementPersonne);
                    }
                }
                
                // si la recherche ne concerne pas un personne on fait une recherche plus generale
                if(count($arrayCritereListeIdEvenementPersonne)==0)
                {
                    // on recherche les evenements du groupe d'adresses
                    $listeEvenementsGroupeAdresse = implode("','",$arrayListeEvenementsGroupeAdresse);
                    $queryEvenementAssocies = "
                        SELECT idEvenementAssocie FROM _evenementEvenement WHERE idEvenement in ('".$listeEvenementsGroupeAdresse."')
                    ";
                    
                    $resEvenementsAssocies = $this->connexionBdd->requete($queryEvenementAssocies);
                    
                    $arrayListeEvenementsAssocies=array();  
                    while($fetchEvenementsAssocies = mysql_fetch_assoc($resEvenementsAssocies))
                    {
                        $arrayListeEvenementsAssocies[] = $fetchEvenementsAssocies['idEvenementAssocie'];
                    }
                
                    $listeEvenementsAssocies = implode("','",$arrayListeEvenementsAssocies);
                }
                
                $queryImage = " SELECT hi.idImage as idImage , hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload,ha1.numero as numero,
                                
                                r.nom as nomRue,
                                sq.nom as nomSousQuartier,
                                q.nom as nomQuartier,
                                v.nom as nomVille,
                                p.nom as nomPays,
                                ha1.numero as numeroAdresse, 
                                ha1.idRue,
                                r.prefixe as prefixeRue,
                                IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                                IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                                IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                                IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays

                                FROM historiqueImage hi2, historiqueImage hi
                                
                                RIGHT JOIN _evenementImage ei ON ei.idImage = hi.idImage
                                RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                                RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                                RIGHT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                                RIGHT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse       
                                
                                LEFT JOIN rue r         ON r.idRue = ha1.idRue
                                LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                                LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                                LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                                LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )

                                LEFT JOIN _adresseImage ai ON hi.idImage = ai.idImage
                                
                                WHERE ei.idEvenement in ('".$listeEvenementsAssocies."')
                                AND hi2.idImage = hi.idImage
                                AND ai.idImage IS NULL
                                GROUP BY hi.idImage , hi.idHistoriqueImage,ha1.idAdresse,ha1.idHistoriqueAdresse
                                HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                                ORDER BY ei.position ASC,hi.idHistoriqueImage
                                LIMIT 1
                "; // on limit a 1 sinon cela peut prendre du temps
                
                $resImage = $this->connexionBdd->requete($queryImage);
                if(mysql_num_rows($resImage)>0)
                {
                        $fetchImage = mysql_fetch_assoc($resImage);
                        //$url = $chemin.$fetchImage['dateUpload'].'/'.$fetchImage['idHistoriqueImage'].".jpg";
                        $url = 'photos-'.$string->convertStringToUrlRewrite($this->getIntituleAdresse($fetchImage)).'-'.$fetchImage['dateUpload'].'-'.$fetchImage['idHistoriqueImage'].'-'.$format.'.jpg';
                        $dateUpload = $fetchImage['dateUpload'];
                        $idHistoriqueImage = $fetchImage['idHistoriqueImage'];
                }
            }
        }
        
        // pas d'url trouvé , on va chercher dans les images vues sur l'adresse courante
        if(!$affichageListeAdressesPersonneImageTrouvee && !$affichageListeAdresseFromSourceTrouvee && $url=='' && isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='')
        {
            $reqVueSur = "
                SELECT hi1.idImage as idImage , hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload,ha1.numero as numero,
                                
                r.nom as nomRue,
                sq.nom as nomSousQuartier,
                q.nom as nomQuartier,
                v.nom as nomVille,
                p.nom as nomPays,
                ha1.numero as numeroAdresse, 
                ha1.idRue,
                r.prefixe as prefixeRue,
                IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays
                
                FROM _adresseImage ai
                LEFT JOIN historiqueImage hi1 ON hi1.idImage = ai.idImage
                LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
                LEFT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ai.idAdresse
                LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse

                LEFT JOIN rue r         ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )

                WHERE ai.idEvenementGroupeAdresse='".$params['idEvenementGroupeAdresse']."'
                AND ai.vueSur='1'
                AND ai.idAdresse<>'' AND ai.idAdresse<>'0'
                
                GROUP BY hi1.idImage , hi1.idHistoriqueImage,ha1.idAdresse,ha1.idHistoriqueAdresse
                
                
            ";
            $resVueSur = $this->connexionBdd->requete($reqVueSur);
            if(mysql_num_rows($resVueSur)>0)
            {
                $fetchVueSur = mysql_fetch_assoc($resVueSur);
                $url = 'photos-'.$string->convertStringToUrlRewrite($this->getIntituleAdresse($fetchVueSur)).'-'.$fetchVueSur['dateUpload'].'-'.$fetchVueSur['idHistoriqueImage'].'-'.$format.'.jpg';
                $dateUpload = $fetchVueSur['dateUpload'];
                $idHistoriqueImage = $fetchVueSur['idHistoriqueImage'];
            }
        }
        
        $trouveImage = true;
        if($url=='')
        {
            $url = $this->getUrlImage().'transparent.gif';
            $trouveImage = false;
        }

        return array('url'=>$url,'dateUpload'=>$dateUpload,'idHistoriqueImage'=>$idHistoriqueImage,'trouve'=>$trouveImage);
    }
    
    // fonction recuperant l'image de la demolition
    public function getUrlImageFrom($idAdresse=0,$format='mini',$sqlWhere='')
    {
        $url="";
        $dateUpload = "";
        $idHistoriqueImage = "";
        $string = new stringObject();
        /* inutile a cause de l'url rewriting
        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        */
        $req = "SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload,ha1.idAdresse as idAdresse,ha1.numero as numero,
        
        r.nom as nomRue,
        sq.nom as nomSousQuartier,
        q.nom as nomQuartier,
        v.nom as nomVille,
        p.nom as nomPays,
        ha1.numero as numeroAdresse, 
        ha1.idRue,
        r.prefixe as prefixeRue,
        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays
        
        FROM historiqueImage hi2, historiqueImage hi1
        RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = '".$idAdresse."' 
        RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        RIGHT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
        RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement=ee.idEvenementAssocie
        RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
        RIGHT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
        RIGHT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse

        LEFT JOIN rue r         ON r.idRue = ha1.idRue
        LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
        LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
        LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
        LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
        
        WHERE hi2.idImage = hi1.idImage
        AND hi1.idImage = ei.idImage
         ".$sqlWhere." 
        GROUP BY hi1.idImage,he1.idEvenement,ha1.idAdresse, hi1.idHistoriqueImage, he1.idHistoriqueEvenement, ha1.idHistoriqueAdresse
        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) 
        AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement) 
        AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        LIMIT 1";
        
        
        $res = $this->connexionBdd->requete($req);
        
        $url='';
        $dateUpload='';
        $idHistoriqueImage=0;
        
        if(mysql_num_rows($res)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($res);
            //$url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            $url = 'photos-'.$string->convertStringToUrlRewrite($this->getIntituleAdresse($fetchAdresse)).'-'.$fetchAdresse['dateUpload'].'-'.$fetchAdresse['idHistoriqueImage'].'-'.$format.'.jpg';
            $dateUpload = $fetchAdresse['dateUpload'];
            $idHistoriqueImage = $fetchAdresse['idHistoriqueImage'];
        }
        if($url=='')
            $url = $this->getUrlImage().'transparent.gif';

        return array('url'=>$url,'dateUpload'=>$dateUpload,'idHistoriqueImage'=>$idHistoriqueImage);
    }
    
    // recupere une photo de realisation d'une personne ( si c'est un architecte par exemple , on obtient une image d'une photo d'une adresse d'une de ces realisations)
    public function getUrlImageFromPersonne($idPersonne,$format='mini')
    {
        $url="";

        switch($format)
        {
            case 'mini':
                $chemin = $this->getUrlImage("mini");
            break;
            case 'moyen':
                $chemin = $this->getUrlImage("moyen");
            break;
            case 'grand':
                $chemin = $this->getUrlImage("grand");
            break;
        }
        

        // on regarde s'il y a une image de position 1
        $queryAdresse="
        
        SELECT hi1.idHistoriqueImage as idHistoriqueImage , hi1.idImage as idImage, hi1.nom as nom , hi1.dateUpload as dateUpload , hi1.dateCliche as dateCliche
        FROM historiqueAdresse ha2,historiqueAdresse ha1
        
        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
        
        RIGHT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
        RIGHT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
        
        RIGHT JOIN _evenementPersonne ep ON ep.idEvenement = ee.idEvenementAssocie
        
        WHERE ep.idPersonne='".$idPersonne."'
        AND ha2.idAdresse = ha1.idAdresse
        AND ei.position='1'
        GROUP BY hi1.idImage,ha1.idAdresse, hi1.idHistoriqueImage,ha1.idHistoriqueAdresse
        HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        LIMIT 1
        ";
        
        $resAdresse = $this->connexionBdd->requete($queryAdresse);
        if(mysql_num_rows($resAdresse)==1)
        {
            $fetchAdresse = mysql_fetch_assoc($resAdresse);
            $url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
        }
        else
        {
            $queryAdresse="
        
            SELECT hi1.idHistoriqueImage as idHistoriqueImage , hi1.idImage as idImage, hi1.nom as nom , hi1.dateUpload as dateUpload , hi1.dateCliche as dateCliche
            FROM historiqueAdresse ha2,historiqueAdresse ha1
            
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
            LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
            LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
            
            RIGHT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
            RIGHT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
            
            RIGHT JOIN _evenementPersonne ep ON ep.idEvenement = ee.idEvenementAssocie
            
            WHERE ep.idPersonne='".$idPersonne."'
            AND ha2.idAdresse = ha1.idAdresse
            GROUP BY hi1.idImage,ha1.idAdresse, hi1.idHistoriqueImage,ha1.idHistoriqueAdresse
            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            LIMIT 1
            ";
            $resAdresse = $this->connexionBdd->requete($queryAdresse);
            if(mysql_num_rows($resAdresse)>0)
            {
                $fetchAdresse = mysql_fetch_assoc($resAdresse);
                $url = $chemin.$fetchAdresse['dateUpload'].'/'.$fetchAdresse['idHistoriqueImage'].".jpg";
            }
            else
            {
                $url = $this->getUrlImage()."/transparent.gif";
            }
        }
        return $url;
    }
    // ***************************************************************************************************************************************
    // affiche l'historique d'une adresse
    // ***************************************************************************************************************************************
    public function afficherHistorique($idAdresse=0)
    {
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('historiqueAdresse'=>'historiqueAdresse.tpl')));
    
        $req = "
        SELECT 
        
        hA.idHistoriqueAdresse as idHistoriqueAdresse,
        hA.date as date,
        hA.nom as nom,
        hA.numero as numero,
        r.nom as nomRue,
        sq.nom as nomSousQuartier,
        q.nom as nomQuartier,
        v.nom as nomVille,
        p.nom as nomPays,
        hA.idRue,
        hA.idSousQuartier,
        hA.idQuartier,
        hA.idVille,
        hA.idPays,
        r.prefixe as prefixeRue
        
        FROM historiqueAdresse hA
        LEFT JOIN rue r             ON r.idRue = hA.idRue
        LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = hA.idSousQuartier
        LEFT JOIN quartier q        ON q.idQuartier = hA.idQuartier
        LEFT JOIN ville v           ON v.idVille = hA.idVille
        LEFT JOIN pays p            ON p.idPays = hA.idPays
        WHERE idAdresse='".$idAdresse."' 
        order by date DESC
        ";
        
        $res=$this->connexionBdd->requete($req);
        
        while($fetch = mysql_fetch_array($res))
        {
            $date = $this->date->toFrench($fetch['date']);
            $t->assign_block_vars('histoAdresse',array(
                'date'=> $date,
                'nom'=>$fetch['nom'],
                'coordonnees'=>$this->getAdresseToDisplay($fetch)));
        }
    
        ob_start();
        $t->pparse('historiqueAdresse');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // ***************************************************************************************************************************************
    // affiche l'adresse de l'evenement
    // ***************************************************************************************************************************************
    /*public function getAdresseFromEvenement($idEvenement=0,$separateur=' ')
    {
        $req='
            SELECT hA.idAdresse,hA.date , hA.description, hA.nom, hA.idRue, hA.etage, hA.numero, hA.idQuartier, hA.idSousQuartier, hA.idVille,hA.idPays,hA.idHistoriqueAdresse
            FROM historiqueAdresse hAb, historiqueAdresse hA
            RIGHT JOIN _adresseEvenement _aE ON hA.idAdresse = _aE.idAdresse
            where hAb.idAdresse = hA.idAdresse
            AND _aE.idAdresse='.$idEvenement.'
            group by hA.idAdresse ,hA.idHistoriqueAdresse
            having hA.date = max(hAb.date) and hA.idHistoriqueAdresse = max(hAb.idHistoriqueAdresse)
        ';
        
        $res = $this->connexionBdd->requete($req);
        
        $fetch = mysql_fetch_array($res);
        
        return $this->getAdresseToDisplay($fetch,$separateur);
    }*/
    
    
    
    // retourne un tableau contenant les infos de l'adresse
    public function getArrayAdresseFromIdAdresse($idAdresse=0)
    {
        $req = "
        select ha.idAdresse as idAdresse, 
        ha.date as date,
        ha.description as description,
        ha.nom as nom,
        ha.idHistoriqueAdresse as idHistoriqueAdresse,
        ha.numero as numero,
        IF(ha.idIndicatif='0','',i.nom) as nomIndicatif,
        ha.idRue,
        IF (ha.idSousQuartier != 0, ha.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
        IF (ha.idQuartier != 0, ha.idQuartier, sq.idQuartier) AS idQuartier,
        IF (ha.idVille != 0, ha.idVille, q.idVille) AS idVille,
        IF (ha.idPays != 0, ha.idPays, v.idPays) AS idPays,
        
        r.prefixe as prefixeRue,
        r.nom as nomRue,
        sq.nom as nomSousQuartier,
        q.nom as nomQuartier,
        v.nom as nomVille,
        p.nom as nomPays
        
        from historiqueAdresse hab, historiqueAdresse ha

        LEFT JOIN rue r         ON r.idRue = ha.idRue
        LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha.idRue='0' and ha.idSousQuartier!='0' ,ha.idSousQuartier ,r.idSousQuartier )
        LEFT JOIN quartier q        ON q.idQuartier = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier!='0' ,ha.idQuartier ,sq.idQuartier )
        LEFT JOIN ville v       ON v.idVille = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille!='0' ,ha.idVille ,q.idVille )
        LEFT JOIN indicatif i ON i.idIndicatif = ha.idIndicatif
        LEFT JOIN pays p        ON p.idPays = IF(ha.idRue='0' and ha.idSousQuartier='0' and ha.idQuartier='0' and ha.idVille='0' and ha.idPays!='0' ,ha.idPays ,v.idPays )      

        where ha.idAdresse = '".$idAdresse."'
        and hab.idAdresse = ha.idAdresse
        group by ha.idAdresse, ha.idHistoriqueAdresse
        having ha.idHistoriqueAdresse = max(hab.idHistoriqueAdresse)
        ";

        $res=$this->connexionBdd->requete($req);
        return mysql_fetch_assoc($res);
    }
    
    public function getNomAdresse($fetch = array())
    {
    
        if (empty($fetch['nomVille']))
        {
            $nomAdresse = 'Pays : '.$fetch['nomPays'];
        }
        else if (empty($fetch['nomQuartier']))
        {
            $nomAdresse = 'Ville : '.$fetch['nomVille'];
        }
        else if (empty($fetch['nomSousQuartier']))
        {
            $nomAdresse = 'Quartier : '.$fetch['nomQuartier'];
        }
        else if (empty($fetch['nomRue']))
        {
            $nomAdresse = 'Ss-Quartier : '.$fetch['nomSousQuartier'];
        } 
        else if (empty($fetch['nom']))
        {
            $nomAdresse = 'Rue : '.$fetch['nomRue'];
        }
        else
        {
            $nomAdresse=$fetch['nom'];
        }
        
        return $nomAdresse;
    }
    
    
    // ***************************************************************************************************************************************
    // dispatch des elements d'adresses recupérés d'une requete afin de recuperer les infos de l'adresse complete
    // ***************************************************************************************************************************************
    public function getAdresseToDisplay($fetch=array(),$separateur=' ')
    {
        $html='';
        $adresseArray =array();
        if($fetch['idRue']!='0')
        {
            $adresseArray = $this->getAdresseComplete($fetch['idRue'],'rue');
        }
        elseif($fetch['idQuartier']!='0')
        {
            $adresseArray = $this->getAdresseComplete($fetch['idQuartier'],'quartier');
        }
        elseif($fetch['idSousQuartier']!='0')
        {
            $adresseArray = $this->getAdresseComplete($fetch['idSousQuartier'],'sousQuartier');
        }
        elseif($fetch['idVille']!='0')
        {
            $adresseArray = $this->getAdresseComplete($fetch['idVille'],'ville');
        }
        elseif($fetch['idPays']!='0')
        {
            $adresseArray = $this->getAdresseComplete($fetch['idPays'],'pays');
        }
        
        if(count($adresseArray)>0)
        {
            $t=new Template('modules/archi/templates/');
            $t->set_filenames(array('adresseDetail'=>'adresseToDisplay.tpl'));
            $nomAdresse = '';
            if (empty($adresseArray['quartier']))
            {
                $nomAdresse = 'Ville : ';
            }
            else if (empty($adresseArray['sousQuartier']))
            {
                $nomAdresse = 'Quartier : ';
            }
            else if (empty($adresseArray['rue']))
            {
                $nomAdresse = 'Ss-Quartier : ';
            } 
            else if (empty($fetch['nom']))
            {
                if($fetch['numero']!='0')
                    $nomAdresse .= $fetch['numero'].$separateur;
                
                if($fetch['etage']!='0')
                    $nomAdresse .= 'Etage : '.$fetch['etage'].$separateur;

                $nomAdresse .= 'Rue : ';
            }
            else
            {
                if($fetch['numero']!='0')
                    $nomAdresse .= 'N°'.$fetch['numero'].$separateur;
                
                $nomAdresse .= $fetch['nom'];
            }
            
            if (!empty($adresseArray['sousQuartier']))
                $t->assign_block_vars('sousQuartier', array());
            if (!empty($adresseArray['quartier']))
                $t->assign_block_vars('quartier', array());

            $t->assign_vars(array(
                'nom' => $nomAdresse,
                'urlSousQuartier' => $this->creerUrl('', 'adresseListe', array('selection'=> 'sousQuartier', 'id'=>$fetch['idSousQuartier'], 'debut'=>0)),
                'nomSousQuartier' => $adresseArray['sousQuartier'],
                'urlQuartier' => $this->creerUrl('', 'adresseListe', array('selection'=> 'quartier', 'id'=>$fetch['idQuartier'], 'debut'=>0)),
                'nomQuartier' => $adresseArray['quartier'],
                'urlVille' => $this->creerUrl('', 'adresseListe', array('selection'=> 'ville', 'id'=>$fetch['idVille'], 'debut'=>0)),
                'nomVille' => $adresseArray['ville'],
                'urlPays'  => $this->creerUrl('', 'adresseListe', array('selection'=> 'pays',  'id'=>$fetch['idPays'], 'debut'=>0)),
                'nomPays' => $adresseArray['pays']
                ));
            
            ob_start();
            $t->pparse('adresseDetail');
            $html .= ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }
    
    
    // ***************************************************************************************************************************************
    // renvoi les infos a partir d'un id de rue, de sousquartier, de quartier, de ville, de pays 
    // ( ex : si on veux l'adresse complete d'une rue , on aura en parametres $id = idDeLaRue et $type = 'rue' et la fonction va nous renvoyer la ville le pays le quartier ... de cette rue)
    // ***************************************************************************************************************************************
    public function getAdresseComplete($id=0,$type='',$params=array())
    {
        $retour=array();
        
        switch($type)
        {
            case 'rue':
                $req = "
                            SELECT r.nom as nomRue, sq.nom as nomSousQuartier, q.nom as nomQuartier, v.nom as nomVille, p.nom as nomPays,r.prefixe as prefixeRue
                            FROM rue r, sousQuartier sq, quartier q, ville v, pays p
                            WHERE r.idRue='".$id."'
                            and sq.idSousQuartier = r.idSousQuartier
                            and q.idQuartier = sq.idQuartier
                            and v.idVille = q.idVille
                            and p.idPays = v.idPays
                    ";
                
                $res=$this->connexionBdd->requete($req);
                
                $fetch=mysql_fetch_array($res);
                $retour['prefixe']      = $fetch["prefixeRue"];
                $retour['rue']          = $fetch["nomRue"];
                $retour['sousQuartier'] = $fetch["nomSousQuartier"];
                $retour['quartier']     = $fetch["nomQuartier"];
                $retour['ville']        = $fetch["nomVille"];
                $retour['pays']         = $fetch["nomPays"];
                
            break;
            case 'sousQuartier':
                $req = "
                            SELECT sq.nom as nomSousQuartier, q.nom as nomQuartier, v.nom as nomVille, p.nom as nomPays
                            FROM sousQuartier sq, quartier q, ville v, pays p
                            WHERE sq.idSousQuartier='".$id."'
                            and q.idQuartier = sq.idQuartier
                            and v.idVille = q.idVille
                            and p.idPays = v.idPays
                    ";
                
                $res=$this->connexionBdd->requete($req);
                
                $fetch=mysql_fetch_array($res);
                
                $retour['rue']          = '';
                $retour['prefixe']      = '';
                $retour['sousQuartier'] = $fetch["nomSousQuartier"];
                $retour['quartier']     = $fetch["nomQuartier"];
                $retour['ville']        = $fetch["nomVille"];
                $retour['pays']         = $fetch["nomPays"];
            break;
            case 'quartier':
                $req = "
                            SELECT q.nom as nomQuartier, v.nom as nomVille, p.nom as nomPays
                            FROM quartier q, ville v, pays p
                            WHERE q.idQuartier = '".$id."'
                            and v.idVille = q.idVille
                            and p.idPays = v.idPays
                    ";
                
                $res=$this->connexionBdd->requete($req);
                
                $fetch=mysql_fetch_array($res);
                
                $retour['rue']          = '';
                $retour['prefixe']      = '';
                $retour['sousQuartier'] = '';
                $retour['quartier']     = $fetch["nomQuartier"];
                $retour['ville']        = $fetch["nomVille"];
                $retour['pays']         = $fetch["nomPays"];
            break;
            case 'ville':
                $req = "
                            SELECT v.nom as nomVille, p.nom as nomPays
                            FROM ville v, pays p
                            WHERE v.idVille = '".$id."'
                            and p.idPays = v.idPays
                    ";
                
                $res=$this->connexionBdd->requete($req);
                
                $fetch=mysql_fetch_array($res);
                
                $retour['rue']          = '';
                $retour['prefixe']      = '';
                $retour['sousQuartier'] = '';
                $retour['quartier']     = '';
                $retour['ville']        = $fetch["nomVille"];
                $retour['pays']         = $fetch["nomPays"];
            break;
            case 'pays':
                $req = "
                            SELECT v.nom as nomVille, p.nom as nomPays
                            FROM pays p
                            WHERE p.idPays = '".$id."'
                    ";
                
                $res=$this->connexionBdd->requete($req);
                
                $fetch=mysql_fetch_array($res);
                
                $retour['rue']          = '';
                $retour['prefixe']      = '';
                $retour['sousQuartier'] = '';
                $retour['quartier']     = '';
                $retour['ville']        = '';
                $retour['pays']         = $fetch["nomPays"];
            break;
        }
        
        
        if(isset($params['miseEnForme']) && $params['miseEnForme']==true)
        {
            // mise en forme de l'adresse
            // chaine en retour a la place du tableau
            $intituleAdresse=$retour['prefixe'].' '.$retour['rue'].' '.$retour['sousQuartier'].' '.$retour['quartier'].' '.$retour['ville'];
            
            
            $retour = $intituleAdresse;
        }
        
        
        
        return $retour;
    }
    
    // ***************************************************************************************************************************************
    // affichage du formulaire d'ajout d'une adresse
    // ***************************************************************************************************************************************
    public function afficheFormulaire($tabTravail=array(),$idAdresse=0, $idHistoriqueAdresse = 0)
    {
    
        $pays=0;
        $ville=0;
        $quartier=0;
        $sousQuartier=0;
        $rue=0;
        $formulaire = new formGenerator();  
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('adresseFormulaire'=>'adresseFormulaire.tpl')));
        
        if( $formulaire->estChiffre($idHistoriqueAdresse))
        {
            if ( count($tabTravail) == 0 )
            {
                // copie pure et dure de la version un peu plus bas avec champ idAdresse
                // À simplifier en changeant juste la requête !
                // À simplifier en changeant juste la requête !
                // À simplifier en changeant juste la requête !
                // À simplifier en changeant juste la requête !
                // recuperation des donnees enregistree en cas de modification d'une adresse 
                $resAdresse = $this->connexionBdd->requete("
                    SELECT a.idAdresse as idAdresse, DATE_FORMAT(a.date,'%d/%m/%Y') as date, a.description as description, a.nom as nom, a.idHistoriqueAdresse as idHistoriqueAdresse,
                            a.idRue as idRue,  a.numero as numero, a.idQuartier as idQuartier, a.idSousQuartier as idSousQuartier, a.idPays as idPays,
                            a.idVille as idVille
                    FROM historiqueAdresse a, historiqueAdresse b
                    WHERE a.idHistoriqueAdresse = '".$idHistoriqueAdresse."'
                    LIMIT 1
                ");
                
                $tabTravail=array();
                
                if(mysql_num_rows($resAdresse)==1)
                {
                    $fetchAdresse=mysql_fetch_array($resAdresse);
                    $tabTravail=array(
                    'nom'=>array('value'=>$fetchAdresse['nom'],'error'=>''),
                    'description'=>array('value'=>$fetchAdresse['description'],'error'=>''),
                    'date'=>array('value'=>$fetchAdresse['date'],'error'=>''),
                    'numero'=>array('value'=>$fetchAdresse['numero'],'error'=>''),
                    'pays'=>array('value'=>$fetchAdresse['idPays'],'error'=>''),
                    'ville'=>array('value'=>$fetchAdresse['idVille'],'error'=>''),
                    'quartier'=>array('value'=>$fetchAdresse['idQuartier'],'error'=>''),
                    'sousQuartier'=>array('value'=>$fetchAdresse['idSousQuartier'],'error'=>''),
                    'rue'=>array('value'=>$fetchAdresse['idRue'],'error'=>'')
                    );
                    
                    
                    $pays = $fetchAdresse['idPays'];
                    $ville = $fetchAdresse['idVille'];
                    $quartier = $fetchAdresse['idQuartier'];
                    $sousQuartier = $fetchAdresse['idSousQuartier'];
                    $rue = $fetchAdresse['idRue'];
                    
                    $arrayAdresse=array();
                    if($rue!=0)
                    {
                        $arrayAdresse=$this->getArrayAdresseFrom($rue,'rue');
                    }
                    elseif($sousQuartier!=0)
                    {
                        // recuperation des infos du  quartier, ville, pays du sousQuartier
                        $arrayAdresse=$this->getArrayAdresseFrom($sousQuartier,'sousQuartier');
                    }
                    elseif($quartier!=0)
                    {
                        // recuperation des infos  ville, pays du quartier
                        $arrayAdresse=$this->getArrayAdresseFrom($quartier,'quartier');
                    }
                    elseif($ville!=0)
                    {
                        // recuperation des infos du pays de la ville
                        $arrayAdresse=$this->getArrayAdresseFrom($ville,'ville');
                    }
                    elseif($pays!=0)
                    {
                        $arrayAdresse=$this->getArrayAdresseFrom($pays,'pays');
                    }
                    
                    if(count($arrayAdresse)>0)
                    {
                        $pays = $arrayAdresse['pays'];
                        $ville = $arrayAdresse['ville'];
                        $quartier = $arrayAdresse['quartier'];
                        $sousQuartier = $arrayAdresse['sousQuartier'];
                        $rue = $arrayAdresse['rue'];                    
                    }
                }
            }
            // assignation du bouton modifier
            $t->assign_vars(array('boutonSubmit'=>'Modifier'));
            $t->assign_vars(array('idAdresseModification'=>$idAdresse));
            $t->assign_vars(array('formAction'=>$this->creerUrl('modifAdresse')));
        }
        else if( $formulaire->estChiffre($idAdresse))
        {
            if ( count($tabTravail) == 0 )
            {
                // recuperation des donnees enregistree en cas de modification d'une adresse 
                $resAdresse = $this->connexionBdd->requete("
                    SELECT a.idAdresse as idAdresse, DATE_FORMAT(a.date,'%d/%m/%Y') as date, a.description as description, a.nom as nom, a.idHistoriqueAdresse as idHistoriqueAdresse,
                            a.idRue as idRue, a.numero as numero, a.idQuartier as idQuartier, a.idSousQuartier as idSousQuartier, a.idPays as idPays,
                            a.idVille as idVille
                    FROM historiqueAdresse a, historiqueAdresse b
                    WHERE b.idAdresse = a.idAdresse
                    AND a.idAdresse = '".$idAdresse."'
                    GROUP BY a.idAdresse,a.idHistoriqueAdresse
                    HAVING a.idHistoriqueAdresse = max(b.idHistoriqueAdresse)
                ");
                
                $tabTravail=array();
                
                if(mysql_num_rows($resAdresse)==1)
                {
                    $fetchAdresse=mysql_fetch_array($resAdresse);
                    $tabTravail=array(
                    'nom'=>array('value'=>$fetchAdresse['nom'],'error'=>''),
                    'description'=>array('value'=>$fetchAdresse['description'],'error'=>''),
                    'date'=>array('value'=>$fetchAdresse['date'],'error'=>''),
                    'numero'=>array('value'=>$fetchAdresse['numero'],'error'=>''),
                    'pays'=>array('value'=>$fetchAdresse['idPays'],'error'=>''),
                    'ville'=>array('value'=>$fetchAdresse['idVille'],'error'=>''),
                    'quartier'=>array('value'=>$fetchAdresse['idQuartier'],'error'=>''),
                    'sousQuartier'=>array('value'=>$fetchAdresse['idSousQuartier'],'error'=>''),
                    'rue'=>array('value'=>$fetchAdresse['idRue'],'error'=>'')
                    );
                    
                    
                    $pays = $fetchAdresse['idPays'];
                    $ville = $fetchAdresse['idVille'];
                    $quartier = $fetchAdresse['idQuartier'];
                    $sousQuartier = $fetchAdresse['idSousQuartier'];
                    $rue = $fetchAdresse['idRue'];
                    
                    $arrayAdresse=array();
                    if($rue!=0)
                    {
                        $arrayAdresse=$this->getArrayAdresseFrom($rue,'rue');
                    }
                    elseif($sousQuartier!=0)
                    {
                        // recuperation des infos du  quartier, ville, pays du sousQuartier
                        $arrayAdresse=$this->getArrayAdresseFrom($sousQuartier,'sousQuartier');
                    }
                    elseif($quartier!=0)
                    {
                        // recuperation des infos  ville, pays du quartier
                        $arrayAdresse=$this->getArrayAdresseFrom($quartier,'quartier');
                    }
                    elseif($ville!=0)
                    {
                        // recuperation des infos du pays de la ville
                        $arrayAdresse=$this->getArrayAdresseFrom($ville,'ville');
                    }
                    elseif($pays!=0)
                    {
                        $arrayAdresse=$this->getArrayAdresseFrom($pays,'pays');
                    }
                    
                    if(count($arrayAdresse)>0)
                    {
                        $pays = $arrayAdresse['pays'];
                        $ville = $arrayAdresse['ville'];
                        $quartier = $arrayAdresse['quartier'];
                        $sousQuartier = $arrayAdresse['sousQuartier'];
                        $rue = $arrayAdresse['rue'];                    
                    }
                }
            }
            // assignation du bouton modifier
            $t->assign_vars(array('boutonSubmit'=>'Modifier'));
            $t->assign_vars(array('idAdresseModification'=>$idAdresse));
            $t->assign_vars(array('formAction'=>$this->creerUrl('modifAdresse')));
        }
        else
        {
            // assignation du bouton ajout
            $t->assign_vars(array('boutonSubmit'=>'Ajouter'));
            $t->assign_vars(array('idAdresseModification'=>''));
            $t->assign_vars(array('formAction'=>$this->creerUrl('ajoutAdresse')));
        }

        if(isset($this->variablesPost['pays']))
            $pays = $this->variablesPost['pays'];

        if(isset($this->variablesPost['ville']))
            $ville = $this->variablesPost['ville'];
        
        if(isset($this->variablesPost['quartier']))
            $quartier = $this->variablesPost['quartier'];
        
        if(isset($this->variablesPost['sousQuartier']))
            $sousQuartier = $this->variablesPost['sousQuartier'];
        
        if(isset($this->variablesPost['rue']))
            $rue = $this->variablesPost['rue'];
        

        
        // recuperation des variables POST pour les passer au sous formulaire des adresses ( rue , pays ...)
        $arrayChoixAdresse=array();
        if($pays!='0')
        {
            $arrayChoixAdresse=array_merge(array('idPaysChoixAdresse'=>$pays),$arrayChoixAdresse);
        }
        
        if($ville!='0')
        {
            $arrayChoixAdresse=array_merge(array('idVilleChoixAdresse'=>$ville),$arrayChoixAdresse);
        }

        if($quartier!='0')
        {
            $arrayChoixAdresse=array_merge(array('idQuartierChoixAdresse'=>$quartier),$arrayChoixAdresse);
        }

        if($sousQuartier!='0')
        {
            $arrayChoixAdresse=array_merge(array('idSousQuartierChoixAdresse'=>$sousQuartier),$arrayChoixAdresse);
        }

        if($rue!='0')
        {
            $arrayChoixAdresse=array_merge(array('idRueChoixAdresse'=>$rue),$arrayChoixAdresse);
        }

        
        $t->assign_vars(array('appelAjaxJavascript'=>"<script  >appelAjax('".$this->creerUrl('','afficheChoixAdresse',array_merge(array('noHeaderNoFooter'=>'1'),$arrayChoixAdresse))."','choixAdresse');</script>"));

        

        foreach($tabTravail as $name => $value)
        {
            $t->assign_vars(array($name=>$value["value"]));
            if($value["error"]!='')
            {
                $t->assign_vars(array($name."-error" => $value["error"].' : '.$name));
            }
        }
        
        ob_start();
        $t->pparse('adresseFormulaire');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // renvoi un tableau contenant les id a partir de l'element final de l'adresse (ex : si rue !=0 , on renvoi le sousquartier, la ville le pays de cette rue)
    public function getArrayAdresseFrom($id,$type='')
    {
        $retourAdresse=array('rue'=>0,'sousQuartier'=>0,'quartier'=>0,'ville'=>0,'pays'=>0);
        
        switch($type)
        {
            case 'rue':
                    // recuperation des infos du sousQuartier, quartier, ville, pays de la rue
                    $resRue = $this->connexionBdd->requete("
                        SELECT r.idRue as idRue, sq.idSousQuartier as idSousQuartier, q.idQuartier as idQuartier,v.idVille as idVille, p.idPays as idPays
                        FROM rue r
                        LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                        LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                        LEFT JOIN ville v ON v.idVille= q.idVille
                        LEFT JOIN pays p ON p.idPays = v.idPays
                        WHERE r.idRue = '".$id."'
                    ");
                    
                    $fetchRue=mysql_fetch_array($resRue);
                    $retourAdresse['rue']=$fetchRue['idRue'];
                    $retourAdresse['sousQuartier']=$fetchRue['idSousQuartier'];
                    $retourAdresse['quartier']=$fetchRue['idQuartier'];
                    $retourAdresse['ville']=$fetchRue['idVille'];
                    $retourAdresse['pays']=$fetchRue['idPays'];

            break;
            
            case 'sousQuartier':
                    // recuperation des infos du  quartier, ville, pays du sousQuartier
                    $resSousQuartier = $this->connexionBdd->requete("
                        SELECT sq.idSousQuartier as idSousQuartier, q.idQuartier as idQuartier,v.idVille as idVille, p.idPays as idPays
                        FROM sousQuartier sq 
                        LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                        LEFT JOIN ville v ON v.idVille= q.idVille
                        LEFT JOIN pays p ON p.idPays = v.idPays
                        WHERE sq.idSousQuartier = '".$id."'
                    ");
                    
                    $fetchSousQuartier=mysql_fetch_array($resSousQuartier);
                    $retourAdresse['rue']=0;
                    $retourAdresse['sousQuartier']=$fetchSousQuartier['idSousQuartier'];
                    $retourAdresse['quartier']=$fetchSousQuartier['idQuartier'];
                    $retourAdresse['ville']=$fetchSousQuartier['idVille'];
                    $retourAdresse['pays']=$fetchSousQuartier['idPays'];
            break;
            
            case 'quartier':
                    // recuperation des infos du  quartier, ville, pays du sousQuartier
                    $resQuartier = $this->connexionBdd->requete("
                        SELECT q.idQuartier as idQuartier,v.idVille as idVille, p.idPays as idPays
                        FROM quartier q
                        LEFT JOIN ville v ON v.idVille= q.idVille
                        LEFT JOIN pays p ON p.idPays = v.idPays
                        WHERE q.idQuartier = '".$id."'
                    ");
                    
                    $fetchQuartier=mysql_fetch_array($resQuartier);
                    $retourAdresse['rue']=0;
                    $retourAdresse['sousQuartier']=0;
                    $retourAdresse['quartier']=$fetchQuartier['idQuartier'];
                    $retourAdresse['ville']=$fetchQuartier['idVille'];
                    $retourAdresse['pays']=$fetchQuartier['idPays'];
            break;
            
            case 'ville':
                    // recuperation des infos du  quartier, ville, pays du sousQuartier
                    $resVille = $this->connexionBdd->requete("
                        SELECT v.idVille as idVille, p.idPays as idPays
                        FROM ville v
                        LEFT JOIN pays p ON p.idPays = v.idPays
                        WHERE v.idVille='".$id."'
                    ");
                    
                    $fetchVille=mysql_fetch_array($resVille);
                    $retourAdresse['rue']=0;
                    $retourAdresse['sousQuartier']=0;
                    $retourAdresse['quartier']=0;
                    $retourAdresse['ville']=$fetchVille['idVille'];
                    $retourAdresse['pays']=$fetchVille['idPays'];
            break;
            
            case 'pays':
                    $retourAdresse['rue']=0;
                    $retourAdresse['sousQuartier']=0;
                    $retourAdresse['quartier']=0;
                    $retourAdresse['ville']=0;
                    $retourAdresse['pays']=$id;
            break;
            
            default:
                echo 'archiAdresse::getAdresseFrom => type inconnu';
            break;
        }
    
        return $retourAdresse;
    }
    
    
    // ***************************************************************************************************************************************
    // affichage du formulaire appelé en ajax pour le choix des rues rattachée à un sousQuartier, rattaché au quartier, ville, pays ......
    // dans les criteres on précise quelle partie du formulaire on veut afficher
    // ***************************************************************************************************************************************
    public function afficheChoixAdresse($criteres=array())
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('choixAdresse'=>'choixAdresse.tpl')));

        
        
        $sqlWherePays ="";
        
        // est ce que l'on affiche tous les champs de choix ou seulement ceux dont on a besoin ?
        $urlTypeNew='';
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        if(isset($this->variablesGet['typeNew']))
        {
            switch($this->variablesGet['typeNew'])
            {
                case 'rue':
                    $criteres['modeAffichage_rue']=1;
                break;
                case 'sousQuartier':
                    $criteres['modeAffichage_sousQuartier']=1;
                break;
                case 'quartier':
                    $criteres['modeAffichage_quartier']=1;
                break;
                case 'ville':
                    $criteres['modeAffichage_ville']=1;
                break;
                case 'pays':
                    $criteres['modeAffichage_pays']=1;
                break;          
            }
        }
        
        
        
        if(count($criteres)>0)
        {
            if(isset($criteres['modeAffichage_rue']))
            {
                //$t->assign_block_vars('isRue',array());
                $t->assign_block_vars('isSousQuartier',array());
                $t->assign_block_vars('isQuartier',array());
                $t->assign_block_vars('isVille',array());
                $t->assign_block_vars('isPays',array());
                $urlTypeNew="+'&typeNew=rue'";
            }
            if(isset($criteres['modeAffichage_sousQuartier']))
            {
                //$t->assign_block_vars('isSousQuartier',array());
                $t->assign_block_vars('isQuartier',array());
                $t->assign_block_vars('isVille',array());
                $t->assign_block_vars('isPays',array());
                $urlTypeNew="+'&typeNew=sousQuartier'";
            }
            if(isset($criteres['modeAffichage_quartier']))
            {
                //$t->assign_block_vars('isQuartier',array());
                $t->assign_block_vars('isVille',array());
                $t->assign_block_vars('isPays',array());
                $urlTypeNew="+'&typeNew=quartier'";
            }
            if(isset($criteres['modeAffichage_ville']))
            {
                //$t->assign_block_vars('isVille',array());
                $t->assign_block_vars('isPays',array());
                $urlTypeNew="+'&typeNew=ville'";
            }
            if(isset($criteres['modeAffichage_pays']))
            {
                //$t->assign_block_vars('isPays',array());
                $urlTypeNew="+'&typeNew=pays'";
            }
        }
        else
        {
            $t->assign_block_vars('isRue',array());
            $t->assign_block_vars('isSousQuartier',array());
            $t->assign_block_vars('isQuartier',array());
            $t->assign_block_vars('isVille',array());
            $t->assign_block_vars('isPays',array());
        }

        // initialisation des identifiants de pays, ville,quartier, sousquartier,rue selectionnés par l'utilisateur
        $idPaysChoixAdresse=0;
        if(isset($this->variablesGet['idPaysChoixAdresse']))
            $idPaysChoixAdresse=$this->variablesGet['idPaysChoixAdresse'];
        
        $idVilleChoixAdresse=0;
        if(isset($this->variablesGet['idVilleChoixAdresse']))
            $idVilleChoixAdresse=$this->variablesGet['idVilleChoixAdresse'];
            
        $idQuartierChoixAdresse=0;
        if(isset($this->variablesGet['idQuartierChoixAdresse']))
            $idQuartierChoixAdresse=$this->variablesGet['idQuartierChoixAdresse'];
            
        $idSousQuartierChoixAdresse=0;
        if(isset($this->variablesGet['idSousQuartierChoixAdresse']))
            $idSousQuartierChoixAdresse=$this->variablesGet['idSousQuartierChoixAdresse'];
        
        $idRueChoixAdresse=0;
        if(isset($this->variablesGet['idRueChoixAdresse']))
            $idRueChoixAdresse=$this->variablesGet['idRueChoixAdresse'];
        
        
        

        $tabUrl['pays']="&idPaysChoixAdresse='+document.getElementById('pays').value";
        $tabUrl['ville']="+'&idVilleChoixAdresse='+document.getElementById('ville').value";
        $tabUrl['sousQuartier']="+'&idSousQuartierChoixAdresse='+document.getElementById('sousQuartier').value";
        $tabUrl['quartier']="+'&idQuartierChoixAdresse='+document.getElementById('quartier').value";
        $tabUrl['rue']="+'&idRueChoixAdresse='+document.getElementById('rue').value";
        //**********************************************************************************************************************************
        // gestion des favoris :
        // s'il n'y a pas de pays choisi ni de ville et qu'il existe une information sur les favoris dans la session de l'utilisateur connecté
        // et que les affichages ne sont ni des ajout de villes ni de pays , alors on assigne 
        if($idPaysChoixAdresse==0 && $idVilleChoixAdresse==0 && $this->session->isInSession('idVilleFavoris') && $this->session->isInSession('idPaysFavoris'))
        {
            if(!isset($criteres['modeAffichage_ville']) && !isset($criteres['modeAffichage_pays']) && $a->getIdProfil()==3) // pas de selection par defaut des favoris si on est sur un affichage d'ajour de ville ou de pays
            {
                // ici cas moderateur , on ne choisit pas de ville par defaut
            }
            
            if(!isset($criteres['modeAffichage_ville']) && !isset($criteres['modeAffichage_pays']) && $a->getIdProfil()==4)
            {
                $idPaysChoixAdresse     = $this->session->getFromSession('idPaysFavoris');
                $idVilleChoixAdresse    = $this->session->getFromSession('idVilleFavoris');
            }
        }
        elseif(isset($this->variablesGet) && $this->variablesGet['archiAffichage']=='rechercheAvancee')
        {
            // par defaut , on met strasbourg dans les choix de la recherche avancee , si l'utilisateur n'est pas connecté
            $idPaysChoixAdresse = 1; // france
            $idVilleChoixAdresse = 1; // strasbourg
        }
        
        
        //***********************************************************************************************************************************
        
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // pays 
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        
        if( !isset($criteres['modeAffichage_pays'])) 
        {
            $t->assign_vars(array('paysOnChange'=>"appelAjax('index.php?archiAffichage=afficheChoixAdresse&noHeaderNoFooter=1".$tabUrl['pays'].$urlTypeNew.",'choixAdresse')"));
            
            
            if($a->getIdProfil()==3) // l'utilisateur est moderateur , on n'affiche seulements les pays des villes qu'il peut moderer et par defaut le pays = france
            {
                $arrayVillesModerees = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
                if(count($arrayVillesModerees)>0)
                {
                    $arrayIdPays=array();
                    $reqPaysModeres = "SELECT distinct idPays FROM ville WHERE idVille in (".implode(",",$arrayVillesModerees).")";
                    $resPaysModeres = $this->connexionBdd->requete($reqPaysModeres);
                    
                    if(mysql_num_rows($resPaysModeres)>0)
                    while($fetchPaysModeres=mysql_fetch_assoc($resPaysModeres))
                    {
                        $arrayIdPays[] = $fetchPaysModeres['idPays'];
                    }
                    
                    if(count($arrayIdPays)>0)
                    {
                        $sqlWherePays = " AND idPays in (".implode(",",$arrayIdPays).") ";
                    }
                    else
                    {
                        $sqlWherePays = " AND idPays=0 "; // si pas de pays trouvé , on empeche l'utilisateur de voir les autres pays
                    }
                }
            }
                        
            $resPays=$this->connexionBdd->requete("select idPays,nom from pays where lower(nom)<>'autre' ".$sqlWherePays." order by nom");
            while($fetchPays=mysql_fetch_array($resPays))
            {
                if (!empty($fetchPays['nom'])) {
                    $selectedPays='';
                    
                    // si l'utilisateur est un moderateur, on selectionne la france en pays par defaut
                    if($a->getIdProfil()==3 && $idPaysChoixAdresse==0)
                    {
                        $idPaysChoixAdresse = 1;
                    }
                    
                    
                    if($idPaysChoixAdresse !=0 && $idPaysChoixAdresse==$fetchPays['idPays'])
                    {
                        $selectedPays="selected='selected'";
                    }
                    $t->assign_block_vars('isPays.listePays', array('idPays'=>$fetchPays['idPays'], 'nomPays'=>$fetchPays['nom'], 'selected'=>$selectedPays));
                }
            }
        }
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // villes 
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($idPaysChoixAdresse!=0 && !isset($criteres['modeAffichage_ville']))
        {
            $t->assign_vars(array('villeOnChange'=>"appelAjax('index.php?archiAffichage=afficheChoixAdresse&noHeaderNoFooter=1".$tabUrl['pays'].$tabUrl['ville'].$urlTypeNew.",'choixAdresse')"));
            
            $sqlWhereVilles="";
            if($a->getIdProfil()==3) // l'utilisateur est un moderateur , on limite a l'affichage des villes qu'il modere
            {
                $arrayVillesModerees = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
                if(count($arrayVillesModerees)>0)
                {
                    $sqlWhereVilles = " AND idVille IN (".implode(",",$arrayVillesModerees).") ";
                }
                else
                {
                    $sqlWhereVilles = " AND idVille = '0' ";
                }
                
            }
            
            $resVille=$this->connexionBdd->requete("select idVille, nom from ville where idPays='".$idPaysChoixAdresse."' and lower(nom)<>'autre' ".$sqlWhereVilles." order by nom");
            while($fetchVille=mysql_fetch_array($resVille))
            {
                $selectedVille='';
                if($idVilleChoixAdresse!='0' && $idVilleChoixAdresse==$fetchVille['idVille'])
                {
                    $selectedVille='selected';
                }
                $t->assign_block_vars('isVille.listeVilles',array('idVille'=>$fetchVille['idVille'],'nomVille'=>$fetchVille['nom'],'selected'=>$selectedVille));
            }
        }
        
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // Quartier
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if( $idVilleChoixAdresse!='0' && !isset($criteres['modeAffichage_quartier']))
        {
            $t->assign_vars(array('quartierOnChange'=>"appelAjax('index.php?archiAffichage=afficheChoixAdresse&noHeaderNoFooter=1".$tabUrl['pays'].$tabUrl['ville'].$tabUrl['quartier'].$urlTypeNew.",'choixAdresse')"));

            $resQuartier = $this->connexionBdd->requete("select idQuartier, nom from quartier where idVille='".$idVilleChoixAdresse."' and lower(nom)<>'autre' order by nom");

            while($fetchQuartier=mysql_fetch_array($resQuartier))
            {
                $selectedQuartier='';
                if($idQuartierChoixAdresse!='0' && $idQuartierChoixAdresse==$fetchQuartier['idQuartier'])
                {
                    $selectedQuartier='selected';
                }
                
                $t->assign_block_vars('isQuartier.listeQuartiers',array('idQuartier'=>$fetchQuartier['idQuartier'],'nomQuartier'=>$fetchQuartier['nom'],'selected'=>$selectedQuartier));
            }
        }

        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // Sous Quartier
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($idQuartierChoixAdresse!=0 && !isset($criteres['modeAffichage_sousQuartier']))
        {
            $t->assign_vars(array('sousQuartierOnChange'=>"appelAjax('index.php?archiAffichage=afficheChoixAdresse&noHeaderNoFooter=1".$tabUrl['pays'].$tabUrl['ville'].$tabUrl['quartier'].$tabUrl['sousQuartier'].$urlTypeNew.",'choixAdresse')"));
            
            $resSousQuartier = $this->connexionBdd->requete("select idSousQuartier, nom from sousQuartier where idQuartier='".$idQuartierChoixAdresse."' and lower(nom)<>'autre' order by nom");
            while($fetchSousQuartier=mysql_fetch_array($resSousQuartier))
            {
                $selectedSousQuartier='';
                if($idSousQuartierChoixAdresse!='0' && $idSousQuartierChoixAdresse==$fetchSousQuartier['idSousQuartier'])
                {
                    $selectedSousQuartier='selected';
                }
                $t->assign_block_vars('isSousQuartier.listeSousQuartiers',array('idSousQuartier'=>$fetchSousQuartier['idSousQuartier'],'nomSousQuartier'=>$fetchSousQuartier['nom'],'selected'=>$selectedSousQuartier));
            }
        }
        
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // Rue
        // --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        if($idSousQuartierChoixAdresse!='0' && !isset($criteres['modeAffichage_rue']))
        {
            //$t->assign_vars(array('sousQuartierOnChange'=>"appelAjax('index.php?archiAffichage=afficheChoixAdresse&noHeaderNoFooter=1".$tabUrl['pays'].$tabUrl['ville'].$tabUrl['quartier'].$tabUrl['sousQuartier'].",'choixAdresse')"));
            
            $resRue = $this->connexionBdd->requete("select idRue, nom from rue where idSousQuartier='".$idSousQuartierChoixAdresse."'");
            while($fetchRue=mysql_fetch_array($resRue))
            {
                $selectedRue='';
                if($idRueChoixAdresse!='0' && $idRueChoixAdresse==$fetchRue['idRue'])
                {
                    $selectedRue='selected';
                }
                $t->assign_block_vars('isRue.listeRues',array('idRue'=>$fetchRue['idRue'],'nomRue'=>$fetchRue['nom'],'selected'=>$selectedRue));
            }
        }
        
        
        ob_start();
        $t->pparse('choixAdresse');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    
    
    
    
    public function getPopupChoixAdresse($modeAffichage='resultatRechercheAdresseCalqueImage')
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('popupChoixAdresse'=>'popupChoixAdresse.tpl')));
        $t->assign_vars(array('boutonRecherche'=>$this->creerUrl('',$modeAffichage,array('noHeaderNoFooter'=>'1')))); // voir dans le template pour la suite de l'url
        $t->assign_vars(array('contenuCalque'=>$this->afficheChoixAdresse()));
        
        ob_start();
        $t->pparse('popupChoixAdresse');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }

    
    // cette fonction permet d'ajout un nouvel element provenant du formulaire afficheFormulaireNouvelleAdresse
    // elle met a jour les tables rue, sousQuartier, quartier, ville, pays
    public function ajoutNouvelleAdresse()
    {
        $newIdPourMail = 0;
        if(isset($this->variablesPost['typeNouvelElement']) && $this->variablesPost['nouvelElement']!='')
        {
            switch($this->variablesPost['typeNouvelElement'])
            {
                case 'newRue':
                    // est ce que ce nom de rue existe deja ?
/*                  if(isset($this->variablesPost['sousQuartier']) && $this->variablesPost['sousQuartier']!='0')
                    {
                        
                        $sql="
                            SELECT idRue 
                            FROM rue 
                            WHERE idSousQuartier = '".$this->variablesPost['sousQuartier']."'
                            AND LOWER(nom) = LOWER(\"".$this->variablesPost['nouvelElement']."\")
                            AND LOWER(prefixe) = LOWER(\"".$this->variablesPost['complement']."\")
                        ";
                        $res=$this->connexionBdd->requete($sql);
                        
                        if(mysql_num_rows($res)>0)
                        {
                            $this->erreurs->ajouter("Erreur : il y a déjà un enregistrement de rue du même nom");
                        }
                        else
                        {
                            // ajout
                            $this->connexionBdd->requete("
                                INSERT INTO rue (idSousQuartier,nom,prefixe) 
                                VALUES ('".$this->variablesPost['sousQuartier']."',\"".$this->variablesPost['nouvelElement']."\",\"".$this->variablesPost['complement']."\")
                            ");
                        }
                    }
                    else
                    {
                        $this->erreurs->ajouter("Erreur : Vous n'avez pas précisé le sous-quartier");
                    }
*/
                    
                    $sousQuartier=0;
                    $quartier=0;
                    $ville=0;
                    $pays=0;
                    
                    if(isset($this->variablesPost['sousQuartier']) && $this->variablesPost['sousQuartier']!='0')
                    {
                        $sousQuartier = $this->variablesPost['sousQuartier'];
                        $typeDernier = 'sousQuartier';
                    }
                    elseif(isset($this->variablesPost['quartier']) && $this->variablesPost['quartier']!='0')
                    {
                        $quartier = $this->variablesPost['quartier'];
                        $typeDernier = 'quartier';
                    }
                    elseif(isset($this->variablesPost['ville']) && $this->variablesPost['ville']!='0')
                    {
                        $ville = $this->variablesPost['ville'];
                        $typeDernier = 'ville';
                    }
                    elseif(isset($this->variablesPost['pays']) && $this->variablesPost['pays']!='0')
                    {
                        $pays = $this->variablesPost['pays'];
                        $typeDernier = 'pays';
                    }
                    
                    if(($sousQuartier + $quartier + $ville + $pays)==0)
                    {
                        $this->erreurs->ajouter("Erreur : Il faut au moins préciser un élément auquel appartient la rue.");
                    }
                    else
                    {
                        // recherche du dernier element renseigné
                        switch($typeDernier)
                        {
                            //************************************************************************************************************************************************************
                            case 'sousQuartier':
                                // ajout d'une rue , il faut preciser un sous quartier
                                // ajoutRue va verifier si la rue n'existe pas deja et renverra un message d'erreur si c'est le cas
                                $newIdPourMail = $this->ajoutRue($this->variablesPost['sousQuartier'],$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                            break;
                            //************************************************************************************************************************************************************
                            case 'quartier':
                                // ajout d'une rue , il faut preciser un sous quartier
                                // on doit donc d'abord trouver le sousQuartier 'autre' correspondant au quartier renseigné, s'il n'existe pas , on le cree
                                $reqAutreSousQuartier = "
                                                            SELECT idSousQuartier FROM sousQuartier WHERE nom='autre' AND idQuartier = '".$quartier."'
                                                        ";
                                $resAutreSousQuartier = $this->connexionBdd->requete($reqAutreSousQuartier);
                                
                                if(mysql_num_rows($resAutreSousQuartier)==0)
                                {
                                    // pas de valeur 'autre' pour le sous quartier , il faut le creer
                                    $reqAjoutSousQuartierAutre = "insert into sousQuartier (idQuartier,nom) values ('".$quartier."','autre')";
                                    $this->connexionBdd->requete($reqAjoutSousQuartierAutre);
                                    $newId = mysql_insert_id();
                                    $newIdPourMail = $this->ajoutRue($newId,$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                                }
                                else
                                {
                                    // sous quartier autre trouve pour le quartier
                                    $fetchAutreSousQuartier = mysql_fetch_assoc($resAutreSousQuartier);
                                    
                                    $newIdPourMail = $this->ajoutRue($fetchAutreSousQuartier['idSousQuartier'],$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                                
                                }
                                
                            break;
                            //************************************************************************************************************************************************************
                            case 'ville':
                                // ajout d'une rue , il faut preciser un sous quartier
                                // recuperation du quartier 'autre' de la ville puis du sousQuartier 'autre' de ce quartier
                                $reqAutreQuartier = "SELECT idQuartier FROM quartier WHERE nom='autre' and idVille='".$ville."'";
                                $resAutreQuartier = $this->connexionBdd->requete($reqAutreQuartier);
                                
                                if(mysql_num_rows($resAutreQuartier)==0)
                                {
                                    // creation du quartier 'autre' de la ville concernee
                                    $reqAjoutQuartierAutre = "insert into quartier (idVille,nom) values ('".$ville."','autre')";
                                    $resAjoutQuartierAutre= $this->connexionBdd->requete($reqAjoutQuartierAutre);
                                    $idQuartierAutreNew = mysql_insert_id();
                                    
                                    // creation du sousQuartier 'autre' de ce nouveau quartier autre
                                    $reqAjoutSousQuartierAutre = "insert into sousQuartier (idQuartier,nom) value ('".$idQuartierAutreNew."','autre')";
                                    $resAjoutSousQuartierAutre = $this->connexionBdd->requete($reqAjoutSousQuartierAutre);
                                    $idSousQuartierAutreNew = mysql_insert_id();
                                    
                                    // ajout de la rue
                                    $newIdPourMail = $this->ajoutRue($idSousQuartierAutreNew,$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                                }
                                else
                                {
                                    // le quartier autre existe
                                    // recherche du sousQuartier qui lui appartient
                                    $fetchIdQuartier = mysql_fetch_assoc($resAutreQuartier);
                                    $quartier = $fetchIdQuartier['idQuartier'];
                                    $reqAutreSousQuartier = "
                                                                SELECT idSousQuartier FROM sousQuartier WHERE nom='autre' AND idQuartier = '".$quartier."'
                                                            ";
                                    $resAutreSousQuartier = $this->connexionBdd->requete($reqAutreSousQuartier);
                                    
                                    if(mysql_num_rows($resAutreSousQuartier)==0)
                                    {
                                        // pas de valeur 'autre' pour le sous quartier , il faut le creer
                                        $reqAjoutSousQuartierAutre = "insert into sousQuartier (idQuartier,nom) values ('".$quartier."','autre')";
                                        $this->connexionBdd->requete($reqAjoutSousQuartierAutre);
                                        $newId = mysql_insert_id();
                                        $newIdPourMail = $this->ajoutRue($newId,$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                                    }
                                    else
                                    {
                                        // sous quartier autre trouve pour le quartier
                                        $fetchAutreSousQuartier = mysql_fetch_assoc($resAutreSousQuartier);
                                        
                                        $newIdPourMail = $this->ajoutRue($fetchAutreSousQuartier['idSousQuartier'],$this->variablesPost['nouvelElement'],$this->variablesPost['complement']);
                                    }
                                }
                                
                            break;
                            //************************************************************************************************************************************************************
                            case 'pays':
                            // ajout d'une rue , il faut preciser un sous quartier
                            echo "ajout d'une rue dans un pays verrouillee. Contactez l'administrateur ou selectionnez d'abord une ville. Si celle ci n'existe pas, contactez nous pour que nous l'ajoutions.";
                            break;
                        }
                    
                    }
                    
                    
                    
                break;
                case 'newSousQuartier':
                    if(isset($this->variablesPost['quartier']) && $this->variablesPost['quartier']!='0')
                    {
                        // est ce que l'enregistrement existe deja ?
                        $sql="
                            SELECT idSousQuartier
                            FROM sousQuartier
                            WHERE idQuartier = '".$this->variablesPost['quartier']."'
                            AND LOWER(nom) = LOWER(\"".$this->variablesPost['nouvelElement']."\")
                        ";
                        $res=$this->connexionBdd->requete($sql);
                        
                        if(mysql_num_rows($res)>0)
                        {
                            $this->erreurs->ajouter("Erreur : il y a déjà un enregistrement de sous-quartier du même nom");
                        }
                        else
                        {
                            // ajout
                            $this->connexionBdd->requete("
                                INSERT INTO sousQuartier (idQuartier,nom)
                                VALUES ('".$this->variablesPost['quartier']."',\"".$this->variablesPost['nouvelElement']."\")
                            ");
                            
                            $newIdPourMail = mysql_insert_id();
                        }
                    }
                    else
                    {
                        $this->erreurs->ajouter("Erreur : Vous n'avez pas précisé le quartier");
                    }
                break;
                case 'newQuartier':
                    if(isset($this->variablesPost['ville']) && $this->variablesPost['ville']!='0')
                    {
                        // est ce que l'enregistrement existe deja ?
                        $sql="
                            SELECT idQuartier
                            FROM quartier
                            WHERE idVille = '".$this->variablesPost['ville']."'
                            AND LOWER(nom) = LOWER(\"".$this->variablesPost['nouvelElement']."\")
                        ";
                        
                        $res=$this->connexionBdd->requete($sql);
                        
                        if(mysql_num_rows($res)>0)
                        {
                            $this->erreurs->ajouter("Erreur : il y a déjà un enregistrement de quartier du même nom");                      
                        }
                        else
                        {
                            // ajout
                            $this->connexionBdd->requete("
                                INSERT INTO quartier (idVille,nom)
                                VALUES ('".$this->variablesPost['ville']."',\"".$this->variablesPost['nouvelElement']."\")
                            ");
                            
                            $newIdPourMail = mysql_insert_id();
                        }
                    }
                    else
                    {
                        $this->erreurs->ajouter("Erreur : Vous n'avez pas précisé la ville");
                    }
                break;
                case 'newVille':
                    if(isset($this->variablesPost['pays']) && $this->variablesPost['pays']!='0')
                    {
                        // est ce que l'enregistrement existe deja ?
                        $sql="
                            SELECT idVille
                            FROM ville
                            WHERE idPays = '".$this->variablesPost['pays']."'
                            AND LOWER(nom) = LOWER(\"".$this->variablesPost['nouvelElement']."\")
                        ";
                        $res=$this->connexionBdd->requete($sql);
                        if(mysql_num_rows($res)>0)
                        {
                            $this->erreurs->ajouter("Erreur : il y a déjà un enregistrement de ville du même nom");
                        }
                        else
                        {
                            // ajout
                            $this->connexionBdd->requete("
                                INSERT INTO ville (idPays,nom,codepostal,longitude,latitude)
                                VALUES ('".$this->variablesPost['pays']."',\"".$this->variablesPost['nouvelElement']."\",'".$this->variablesPost['codepostal']."','".$this->variablesPost['longitude']."','".$this->variablesPost['latitude']."')
                            ");
                            
                            $newIdPourMail = mysql_insert_id();
                        }
                    }
                    else
                    {
                        $this->erreurs->ajouter("Erreur : Vous n'avez pas précisé le pays");
                    }
                break;
                case 'newPays':
                    // est ce que l'enregistrement existe deja ?
                    $sql = "
                        SELECT idPays
                        FROM pays
                        WHERE LOWER(nom)=LOWER(\"".$this->variablesPost['nouvelElement']."\")
                    ";
                    
                    $res=$this->connexionBdd->requete($sql);
                    
                    if(mysql_num_rows($res)>0)
                    {
                        $this->erreurs->ajouter("Erreur : il y a déjà un enregistrement de pays du même nom");
                    }
                    else
                    {
                        // ajout
                        $this->connexionBdd->requete("
                            INSERT INTO pays (nom) VALUES (\"".$this->variablesPost['nouvelElement']."\")
                        ");
                        
                        $newIdPourMail = mysql_insert_id();
                    }
                break;
            }
            
            // envoi d'un mail aux admins
            if($newIdPourMail!='0' && $newIdPourMail!='')
            {
                
                $tableName="";
                $identifiantName = "";
                // correspondance entre les noms de tables
                switch($this->variablesPost['typeNouvelElement'])
                {
                    case "newRue":
                        $tableName = "rue";
                        $identifiantName = "idRue";
                    break;
                    case "newSousQuartier":
                        $tableName = "sousQuartier";
                        $identifiantName = "idSousQuartier";
                    break;
                    case "newQuartier":
                        $tableName = "quartier";
                        $identifiantName = "idQuartier";
                    break;
                    case "newVille":
                        $tableName = "ville";
                        $identifiantName = "idVille";
                    break;
                    case "newPays":
                        $tableName= "pays";
                        $identifiantName = "idPays";
                    break;
                
                }
            
            

                $mail = new mailObject();
                    
                $a = new archiAuthentification();
                $u = new archiUtilisateur();
                $arrayInfosUtilisateur = $u->getArrayInfosFromUtilisateur($a->getIdUtilisateur());
                $message = "Un élément d'adresse a été ajouté par : ".$arrayInfosUtilisateur['nom']." ".$arrayInfosUtilisateur['prenom']."<br>";
                $message .= "type d'élément : ".$tableName."<br><br>";
                $message.="<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$tableName,'idModification'=>$newIdPourMail))."'>".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$tableName,'idModification'=>$newIdPourMail))."</a>";
                
                $messageModerateur="Un élément d'adresse a été ajouté<br>";
                $messageModerateur.="type d'élément : ".$tableName."<br><br>";
                $messageModerateur.="<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$tableName,'idModification'=>$newIdPourMail))."'>".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$tableName,'idModification'=>$newIdPourMail))."</a>";
                
                $mail->sendMailToAdministrators($mail->getSiteMail(),"archi-strasbourg.org : un utilisateur a ajouté un élément d'adresse",$message," and alerteMail='1' ",true);
                
                $u = new archiUtilisateur();
                $u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>2,'criteres'=>" and alerteMail='1' "));
                
                /*
                if(in_array($tableName,array('rue','sousQuartier','quartier')))
                {
                    $idVille = $this->getIdVilleFrom($newIdPourMail,$identifiantName);
                    $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($idVille,array("sqlWhere"=>" AND alerteAdresses='1' "));
                    if(count($arrayListeModerateurs)>0)
                    {
                        foreach($arrayListeModerateurs as $indice => $idModerateur)
                        {
                            if($idModerateur!=$a->getIdUtilisateur() && $u->isAuthorized('admin_rues',$idModerateur) && $u->isAuthorized('admin_sousQuartiers',$idModerateur) && $u->isAuthorized('admin_quartiers',$idModerateur))
                            {
                                $mailModerateur = $u->getMailUtilisateur($idModerateur);
                                $mail->sendMail($mail->getSiteMail(),$mailModerateur,"archi-strasbourg.org : un utilisateur ajouté un élément d'adresse",$messageModerateur,true);
                            }
                        }
                    }
                }*/
            }
            
        }
        else
        {
            $this->erreurs->ajouter("Erreur: le champ 'intitule' est vide");
        }
    }
    
    

    
    // fonction affichant le formulaire permettant aux admins d'ajouter de nouvelles adresses (rue, sousQuartier, Quartier, ....)
    public function afficheFormulaireNouvelleAdresse($criteres=array())
    {
        $html="";
        
        // verification des droits
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        if($u->isAuthorized('admin_rues',$a->getIdUtilisateur()) && $u->isAuthorized('admin_quartiers',$a->getIdUtilisateur()) && $u->isAuthorized('admin_sousQuartiers',$a->getIdUtilisateur()))
        {
        
            $t=new Template('modules/archi/templates/');
            $t->set_filenames((array('nouvelleAdresse'=>'nouvelleAdresseFormulaire.tpl')));

            if(!isset($criteres['typeNew']))
            {
                $criteres['typeNew']='newRue';
            }
            
            $typeButtonSubmit='submit';
            
            switch ($criteres['typeNew'])
            {
                case 'newRue':
                    $t->assign_block_vars('afficheComplement',array());
                    $criteres['modeAffichage_rue'] = 1;
                break;
                case 'newSousQuartier':
                    $criteres['modeAffichage_sousQuartier'] = 1;
                break;
                case 'newQuartier':
                    $criteres['modeAffichage_quartier'] = 1;
                break;
                case 'newVille':
                    $t->assign_block_vars('afficheCodePostal',array());
                    $criteres['modeAffichage_ville'] = 1;
                    
                    $typeButtonSubmit='button';
                    $paramsGoogleMap = array('googleMapKey'=>$this->googleMapKey);

                    $googleMap = new googleMap($paramsGoogleMap);
                    $js="";
                    $js.= $googleMap->getJsFunctions();
                    $js.= $googleMap->getJSInitGeoCoder();
                    
                    $jsToExecute="document.getElementById('formAjoutNouvelleAdresse').action='".$this->creerUrl('ajoutNouvelleAdresse','')."';document.getElementById('formAjoutNouvelleAdresse').submit();";
                        
                    $arrayJsCoordonneesFromGoogleMap = $googleMap->getJSRetriveCoordonnees(array(
                        'nomChampLatitudeRetour'=>'latitude',
                        'nomChampLongitudeRetour'=>'longitude',
                        'getAdresseFromElementById'=>true,
                        'jsAdresseValue'=>"document.getElementById('nouvelElement').value+' '+document.getElementById('pays').options[document.getElementById('pays').selectedIndex].innerHTML",
                        'jsToExecuteIfOK'=>$jsToExecute,
                        'jsToExecuteIfNoAddressFound'=>$jsToExecute
                    ));

                    $js.= $arrayJsCoordonneesFromGoogleMap['jsFunctionToExecute'];
                    
                    
                    
                    $t->assign_vars(array(
                        "champsLongitudeLatitude"=>"<input type='text' name='longitude' id='longitude' value=''><input type='text' name='latitude' id='latitude' value=''>",
                        "jsLongitudeLatitude"=>$js,
                        "typeButtonSubmit"=>"button",
                        "onClickButtonSubmit"=>$arrayJsCoordonneesFromGoogleMap['jsFunctionCall']
                        )
                    
                    ); // gestion de la recherche des coordonnees googlemap
                                    
                break;
                case 'newPays':
                    $criteres['modeAffichage_pays'] = 1;
                break;
                default:
                    $criteres['modeAffichage_rue'] = 1;
            }
            
            $a = new archiAuthentification();
            if($a->getIdProfil()==3)// l'utilisateur est un moderateur , on le limite a la ville qu'il administre
            {
                $t->assign_block_vars('liensModerateur',array());
                $t->assign_vars(array(
                'urlNewRue'     =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newRue')),
                'urlNewSousQuartier'    =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newSousQuartier')),
                'urlNewQuartier'    =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newQuartier'))
                ));
            }
            else
            {
                $t->assign_block_vars('liensAdmin',array());
                $t->assign_vars(array(
                'urlNewRue'     =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newRue')),
                'urlNewSousQuartier'    =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newSousQuartier')),
                'urlNewQuartier'    =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newQuartier')),
                'urlNewVille'       =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newVille')),
                'urlNewPays'        =>  $this->creerUrl('','ajoutNouvelleAdresse',array('typeNew' => 'newPays'))
                ));
            }
            
            

            
            
            $t->assign_vars(array(
                'afficheChoixAdresse'=>$this->afficheChoixAdresse($criteres),
                'typeNouvelElement'=>$criteres['typeNew'],
                'formAction'=>$this->creerUrl('ajoutNouvelleAdresse'),
                'typeButtonSubmit'=>$typeButtonSubmit
                ));
            
            
            ob_start();
            $t->pparse('nouvelleAdresse');
            $html=ob_get_contents();
            ob_end_clean();
        }
        else
        {
            $erreurObj = new objetErreur();
            $erreurObj->ajouter("Vous n'avez pas les droits pour acceder à cette partie du site");
            echo $erreurObj->afficher();
        }
        
        return $html;
    }
    
    


    // **********************************************************************************************************************************************************************
    // cette fonction va ajouter une adresse dans la table historiqueAdresses en verifiant que celle ci n'existe pas encore , puis elle renvoi l'id de la nouvelle adresse , si l'adresse existe , elle renvoi l'id de l'adresse existante
    // dans un tableau en précisant que l'adresse existait ou non
    // **********************************************************************************************************************************************************************
    public function ajouterAdresseFromArray($adresse=array(),$params=array())
    {
        $mail = new mailObject();
        
        $arrayRetour=array();       
        
        if(count($adresse)>0)
        {
            $select ="";
            $leftJoin="";
            
            $numero=$adresse['numero'];
            $indicatif=$adresse['indicatif'];
            
            if($numero != '' && $numero !='0')
            {
                $select .=" AND ha.numero='".$numero."' ";
                if($indicatif!='' && $indicatif!='0')
                {
                    $select .=" AND ha.idIndicatif='".$indicatif."' ";
                }
                else
                {
                    $select .=" AND ha.idIndicatif='0' ";
                }
            }
            else
            {
                $select .=" AND ha.numero='0' AND ha.idIndicatif='0' ";
            }
            
            if($adresse['idRue']>0)
            {
                $select .=" AND ha.idRue ='".$adresse['idRue']."' ";
            }
            elseif($adresse['idSousQuartier']>0)
            {
                $select .=" AND ha.idSousQuartier ='".$adresse['idSousQuartier']."' AND ha.idRue='0' ";
            }
            elseif($adresse['idQuartier']>0)
            {
                $select .=" AND ha.idQuartier ='".$adresse['idQuartier']."' AND ha.idRue='0' AND ha.idSousQuartier='0' ";
            }
            elseif($adresse['idVille']>0)
            {
                $select .=" AND ha.idVille ='".$adresse['idVille']."' AND ha.idRue='0' AND ha.idSousQuartier='0' AND ha.idQuartier='0' ";
            }
            
            $reqVerifAdresse = "SELECT ha.idAdresse as idAdresse,ha.idHistoriqueAdresse as idHistoriqueAdresse
                                FROM historiqueAdresse ha, historiqueAdresse ha2
                                ".$leftJoin."
                                WHERE 1=1
                                ".$select."
                                AND ha2.idAdresse = ha.idAdresse
                                GROUP BY ha.idAdresse , ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                                ";
            
            $resVerifAdresse = $this->connexionBdd->requete($reqVerifAdresse);
            
            if(mysql_num_rows($resVerifAdresse)==1)
            {
                //l'adresse existe deja , on recupere son id
                $fetchAdresse = mysql_fetch_assoc($resVerifAdresse);
                $idAdresse = $fetchAdresse['idAdresse'];
                $arrayRetour = array("idAdresse"=>$idAdresse,"newAdresse"=>false);
                
                // on va ecraser les coordonnees googlemap dans tous les cas
                $reqIdHistoriqueAdresse = "
                    SELECT ha1.idHistoriqueAdresse as idHistoriqueAdresse, ha1.coordonneesVerrouillees as coordonneesVerrouillees
                    FROM historiqueAdresse ha1, historiqueAdresse ha2
                    WHERE
                    ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = '$idAdresse'
                    GROUP BY ha1.idAdresse, ha2.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                $resIdHistoriqueAdresse = $this->connexionBdd->requete($reqIdHistoriqueAdresse);
                if(mysql_num_rows($resIdHistoriqueAdresse)==1)
                {
                    $fetchIdHistoriqueAdresse = mysql_fetch_assoc($resIdHistoriqueAdresse);
                    if($fetchIdHistoriqueAdresse['idHistoriqueAdresse']!='' && $fetchIdHistoriqueAdresse['idHistoriqueAdresse']!='0')
                    {
                        $longitude='0';
                        $latitude='0';
                        // attention si les coordonnees on ete verrouillees , elles ne sont pas mis a jour (verrouillees si modifiées avec la popup googlemap sur le detail d'une adresse
                        if(isset($adresse['longitude']) && $adresse['longitude']!='' && isset($adresse['latitude']) && $adresse['latitude']!='' && $fetchIdHistoriqueAdresse['coordonneesVerrouillees']!='1')
                        {
                            $longitude=$adresse['longitude'];
                            $latitude=$adresse['latitude'];
                        
                            $reqUpdateCoordonnees = "UPDATE historiqueAdresse SET longitude='$longitude',latitude='$latitude' WHERE idHistoriqueAdresse='".$fetchIdHistoriqueAdresse['idHistoriqueAdresse']."'";
                            $resUpdateCoordonnees = $this->connexionBdd->requete($reqUpdateCoordonnees);
                        }
                    }
                    
                }
                
                
                
            }
            elseif(mysql_num_rows($resVerifAdresse)>1)
            {
                // il y a des adresses redondantes : envoyer un mail 
                $this->erreurs->ajouter("Il existe une adresse redondante dans la base de donnée , veuillez contacter l'administrateur");
                $fetchAdresse = mysql_fetch_assoc($resVerifAdresse);
                $idAdresse =$fetchAdresse['idAdresse']; // on prend la premiere
                $arrayRetour = array("idAdresse"=>$idAdresse,"newAdresse"=>false);
            }
            else
            {
                // on enregistre la nouvelle adresse et on renvoi son id
                $newIdAdresse = $this->getNewIdAdresse();
                
                if($adresse['idRue']>0)
                {
                    $idRue = $adresse['idRue'];
                    $idSousQuartier = 0;
                    $idQuartier = 0;
                    $idVille = 0;
                    $idPays = 0;
                }
                elseif($adresse['idSousQuartier']>0)
                {
                    $idRue = 0;
                    $idSousQuartier = $adresse['idSousQuartier'];
                    $idQuartier = 0;
                    $idVille = 0;
                    $idPays = 0;
                    $numero="";
                    $indicatif="";
                }
                elseif($adresse['idQuartier']>0)
                {
                    $idRue = 0;
                    $idSousQuartier = 0;
                    $idQuartier = $adresse['idQuartier'];
                    $idVille = 0;
                    $idPays = 0;
                    $numero="";
                    $indicatif="";
                }
                elseif($adresse['idVille']>0)
                {
                    $idRue = 0;
                    $idSousQuartier = 0;
                    $idQuartier = 0;
                    $idVille = $adresse['idVille'];
                    $idPays = 0;
                    $numero="";
                    $indicatif="";
                }
                
                
                // recuperation du nom de la rue , le complément et l'indicatif
                $reqRue = "SELECT idRue, nom, prefixe FROM rue WHERE idRue = '".$idRue."'";
                $resRue = $this->connexionBdd->requete($reqRue);
                $fetchRue = mysql_fetch_assoc($resRue);
                
                $idUtilisateur = 0;
                $auth = new archiAuthentification();
                if($auth->estConnecte())
                {
                    $idUtilisateur = $auth->getIdUtilisateur();
                }
                
                
                $longitude='0';
                $latitude='0';
                if(isset($adresse['longitude']) && $adresse['longitude']!='' && isset($adresse['latitude']) && $adresse['latitude']!='')
                {
                    $longitude=$adresse['longitude'];
                    $latitude=$adresse['latitude'];
                }
                
                $reqInsertAdresse="INSERT INTO historiqueAdresse (idAdresse,date,idRue,numero, idQuartier,idSousQuartier,idPays, idVille,nom,idIndicatif,idUtilisateur,longitude,latitude)
                                    VALUES ('".$newIdAdresse."',now(),'".$idRue."','".$numero."','".$idQuartier."','".$idSousQuartier."','".$idPays."','".$idVille."',\"".$numero.' '.$fetchRue['prefixe'].' '.$fetchRue['nom']."\",'".$indicatif."','".$idUtilisateur."','".$longitude."','".$latitude."')
                ";
                $resInsertAdresse = $this->connexionBdd->requete($reqInsertAdresse);
                $idAdresse = $newIdAdresse;
                
                $libelleAdresse = $this->getIntituleAdresse(array('nomRue'=>$fetchRue['nom'],'idIndicatif'=>$indicatif,'numero'=>$numero,'prefixeRue'=>$fetchRue['prefixe']));
                // ****************************************************************************************************************************************************************
                // envoi du mail de notification aux administrateurs => ce mail s'envoi maintenant en meme temps que le signalement d'un nouvel evenement sur une nouvelle adresse dans la fonction ajouterNouveauDossier() sauf dans le cas 
                // d'une modif d'un groupe d'adresse ( option params['envoiMailAdminSiNouvelleAdresse'])
                
                if(isset($params['envoiMailAdminSiNouvelleAdresse']) && $params['envoiMailAdminSiNouvelleAdresse']==true)
                {
                    if(isset($this->variablesGet['archiIdEvenementGroupeAdresses']))
                    {
                        $message = "Une nouvelle adresse a été ajoutée sur un groupe d'adresses existant: <a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail',"archiIdAdresse"=>$idAdresse,"archiIdEvenementGroupeAdresse"=>$this->variablesGet['archiIdEvenementGroupeAdresses']))."'>".$libelleAdresse."</a><br>";
                    }
                    else
                    {
                        $message = "Une nouvelle adresse a été ajoutée sur un groupe d'adresses existant: <a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$idAdresse))."'>".$libelleAdresse."</a><br>";
                    }
                    
                    // recuperation des infos sur l'utilisateur qui fais la modif
                    $utilisateur = new archiUtilisateur();
                    $arrayInfosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($this->session->getFromSession('utilisateurConnecte'.$this->idSite));
                    
                    $message .="<br>".$arrayInfosUtilisateur['nom']." - ".$arrayInfosUtilisateur['prenom']." - ".$arrayInfosUtilisateur['mail']."<br>";

                    $mail->sendMailToAdministrators($mail->getSiteMail(),"Ajout d'une adresse sur archi-strasbourg.org : ".$libelleAdresse,$message," and alerteMail='1' ",true);
                    
                    
                    $u = new archiUtilisateur();
                    $u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>4,'criteres'=>" and alerteMail='1' "));

                    
                }
                // ****************************************************************************************************************************************************************
                // envoi du mail aux utilisateurs dont la notification est activée (alerteMail)
                // ****************************************************************************************************************************************************************
                
                //ATTENTION :  on n'envoit plus de mails aux utilisateurs a chaque creation d'adresse, on envoi un mail de resumé de creation d'adresse chaque semaine.
                
                /*$reqUtilisateurs = "SELECT idUtilisateur,mail FROM utilisateur WHERE alerteMail='1' and compteActif='1' and estAdmin='0'";
                $resUtilisateurs = $this->connexionBdd->requete($reqUtilisateurs);
                while($fetchUtilisateurs = mysql_fetch_assoc($resUtilisateurs))
                {
                    if($fetchUtilisateurs['idUtilisateur']!=$auth->getIdUtilisateur())
                    {
                        $message="";
                        $message.="Un utilisateur a ajouté une adresse sur le site : <br>".$libelleAdresse;
                        $message.=$this->getMessageDesabonnerAlerteMail();
                        $sujet = "Une nouvelle adresse a été ajouté sur le site : <a href='".$this->creerUrl('','',array(''=>))."'>".$libelleAdresse."</a>";
                        $mail->sendMail($mail->getSiteMail(),$fetchUtilisateurs['mail'],$sujet,$message,true);
                    }
                }*/
                // ****************************************************************************************************************************************************************
                $arrayRetour = array("idAdresse"=>$idAdresse,"newAdresse"=>true);
            }
        }
        else
        {
            // erreur dans le tableau d'adresse (ajouterAdressesFromArray)
            $this->erreurs->ajouter("Erreur dans la saisie de l'adresse");
            $idAdresse = 0;
            $arrayRetour = array();
        }

        return $arrayRetour;
    }

    // renvoi les images relies a une adresse dans la table _adresseImage
    public function getAdressesFromImage($idImage=0)
    {
            $query = "
                    SELECT  ha.idAdresse as idAdresse,
                            ha.idRue as idRue,
                            ha.numero as numero,
                            ha.idQuartier as idQuartier,
                            ha.idSousQuartier as idSousQuartier,
                            ha.idPays as idPays,
                            ha.idVille as idVille,
                            v.nom as nomVille,
                            q.nom as nomQuartier,
                            sq.nom as nomSousQuartier,
                            r.nom as nomRue,
                            ha.idIndicatif as idIndicatif,
                            r.prefixe as prefixeRue
                    FROM historiqueAdresse ha2, historiqueAdresse ha
                    RIGHT JOIN _adresseImage ai ON ai.idAdresse = ha.idAdresse
                    LEFT JOIN rue r ON r.idRue = ha.idRue
                    LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                    LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                    LEFT JOIN ville v ON v.idVille = ha.idVille
                    LEFT JOIN pays p ON p.idPays = ha.idPays
                    WHERE ai.idImage = '".$idImage."'
                    AND ha2.idAdresse = ha.idAdresse
                    GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                    HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        ";
        return $this->connexionBdd->requete($query);
    }
    
    
    public function supprimerIdAdresseFromGroupeAdresse($idEvenementGroupeAdresse=0,$idAdresse=0)
    {
        $req = "DELETE FROM _adresseEvenement WHERE idEvenement='".$idEvenementGroupeAdresse."' and idAdresse='".$idAdresse."'";
        $res = $this->connexionBdd->requete($req);
    }
    
    // **********************************************************************************************************************************************************************
    // fonction recuperant les adresses d'un groupe d'adresses dans un tableau
    // **********************************************************************************************************************************************************************
    public function getAdressesFromEvenementGroupeAdresses($idEvenementGroupeAdresse=0)
    {
        $query = "
                    SELECT  ha.idAdresse as idAdresse,
                            ha.idRue as idRue,
                            ha.numero as numero,
                            ha.idQuartier as idQuartier,
                            ha.idSousQuartier as idSousQuartier,
                            ha.idPays as idPays,
                            ha.idVille as idVille,
                            v.nom as nomVille,
                            q.nom as nomQuartier,
                            sq.nom as nomSousQuartier,
                            r.nom as nomRue,
                            ha.idIndicatif as idIndicatif,
                            r.prefixe as prefixeRue,
                            ha.longitude as longitude,
                            ha.latitude as latitude
                    FROM historiqueAdresse ha2, historiqueAdresse ha
                    RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                    LEFT JOIN rue r ON r.idRue = ha.idRue
                    LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                    LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                    LEFT JOIN ville v ON v.idVille = ha.idVille
                    LEFT JOIN pays p ON p.idPays = ha.idPays
                    WHERE ae.idEvenement = '".$idEvenementGroupeAdresse."'
                    AND ha2.idAdresse = ha.idAdresse
                    GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                    HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    ORDER BY ha.idAdresse
        ";
        return $this->connexionBdd->requete($query);
    }
    
    // **********************************************************************************************************************************************************************
    // cette fonction renvoi les adresses d'un evenement
    // **********************************************************************************************************************************************************************
    public function getIdAdressesFromIdEvenement($params=array())
    {
        $req = "
                SELECT  distinct ae.idAdresse as idAdresse
                FROM _adresseEvenement ae
                LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$params['idEvenement']."'
                WHERE ae.idEvenement = ee.idEvenement
        ";
        
        return $req;
    }
    // **********************************************************************************************************************************************************************
    // cette fonction affiche le formulaire d'ajout d'adresse multiple
    // c'est a dire le formulaire d'ajout d'un dossier , sans la partie evenement
    // **********************************************************************************************************************************************************************
    public function afficheAjoutAdressesMultiple($idEvenementGroupeAdresse=0)
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('ajoutAdressesMultiple'=>'nouveauDossier.tpl')));
        
        
        
        // initialisation de l'objet googlemap pour la recuperation des coordonnees
        $paramsGoogleMap = array('googleMapKey'=>$this->googleMapKey);

        $googleMap = new googleMap($paramsGoogleMap);

        $html.= $googleMap->getJsFunctions();
        $html.= $googleMap->getJSInitGeoCoder();
        
        /*$jsToExecute="document.getElementById('formAjoutDossier').action='".$this->creerUrl('enregistreGroupeAdresses','',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse))."';testAdresseValideAndSubmit('formAjoutDossier');";
            
        $arrayJsCoordonneesFromGoogleMap = $googleMap->getJSRetriveCoordonnees(array(
            'nomChampLatitudeRetour'=>'latitude',
            'nomChampLongitudeRetour'=>'longitude',
            'getAdresseFromElementById'=>true,
            'jsAdresseValue'=>"document.getElementById('numero0').value+' '+document.getElementById('rue0txt').value+' '+document.getElementById('villetxt').value",
            'jsToExecuteIfOK'=>$jsToExecute,
            'jsToExecuteIfNoAddressFound'=>"document.getElementById('latitude').value='';document.getElementById('longitude').value='';".$jsToExecute
        ));

        $html.= $arrayJsCoordonneesFromGoogleMap['jsFunctionToExecute'];
        */
        
        // recapitulatif de(s) l'adresse(s) deja liee(s)
        $t->assign_block_vars('isNotAjoutNouvelleAdresse',array());
        $t->assign_vars(array('recapitulatifAdresse'=>$this->afficherRecapitulatifAdresses($idEvenementGroupeAdresse)));
        
        // affichage du block d'adresses
        $t->assign_block_vars('isNotAjoutSousEvenement',array());
        
        // recuperation des adresses de l'evenement groupe d'adresse
        $resAdresses = $this->getAdressesFromEvenementGroupeAdresses($idEvenementGroupeAdresse);
        
        
        
        
        $authentification = new archiAuthentification();
        
        if($authentification->estAdmin())
        {
            $t->assign_vars(array('displayQuartiers'=>'table-row'));
            $t->assign_vars(array('displaySousQuartiers'=>'table-row'));
        }
        else
        {
            $t->assign_vars(array('displayQuartiers'=>'none'));
            $t->assign_vars(array('displaySousQuartiers'=>'none'));
        
        }       
            
        
        
        // parcours des adresses recuperées de la base de données pour voir si certaines viennent d'autres quartier , ville ou sousquartier ==> si c'est le cas il faudra gerer l'affichage plusieurs formulaire , un par groupe de sousquartier identiques
        if(mysql_num_rows($resAdresses)>0)
            mysql_data_seek($resAdresses,0);
        
        $arrayVilles        =array();
        $arrayQuartiers     =array();
        $arraySousQuartiers =array();
        
        $i=0;
        while($fetchAdressesVerif = mysql_fetch_assoc($resAdresses))
        {
            $arrayVilles[]=$fetchAdressesVerif['idVille'];
            $arrayQuartiers[]=$fetchAdressesVerif['idQuartier'];
            $arraySousQuartiers[]=$fetchAdressesVerif['idSousQuartier'];
            
            // on en profite pour recuperer les coordonnées longitudes et latitudes
            if($i==0)
            {
                $longitude = $fetchAdressesVerif['longitude'];
                $latitude = $fetchAdressesVerif['latitude'];
                // on les renseigne dans le formulaire
                $t->assign_vars(array('longitude'=>$longitude,'latitude'=>$latitude));
            }
            
            $i++;
        }
        
        $arrayVilles   = array_unique($arrayVilles);
        $arrayQuartiers = array_unique($arrayQuartiers);
        $arraySousQuartiers = array_unique($arraySousQuartiers);
        if(count($arrayVilles)>1 || count($arrayQuartiers)>1 || count($arraySousQuartiers)>1)
        {
            echo "Attention il y a des adresses qui ne font pas partie du même quartier.<br>";
            // affichage de plusieurs formulaires pour chaque adresse de rue qui n'appartient pas au meme ensemble
            // a faire ?? ou limiter les groupes d'adresses au adresses appartenant à la meme rue
        }
        else
        {
            // toutes les adresses appartiennent au meme quartier, sousQuartier,ville 
            // on reprend le premier enregistrement de la requete pour chercher les infos ville, quartier, sousquartier a assigner au formulaire
            if(mysql_num_rows($resAdresses)>0)
                mysql_data_seek($resAdresses,0);
            $fetchInfosAdresse = mysql_fetch_assoc($resAdresses);
            
            // l'adresse concerne t elle une rue, un quartier , un sous quartier, une ville ... 
            // pour l'instant on considere que cela concerne une rue avec son numero
            if($fetchInfosAdresse['idRue']!='0')
            {
                // recherche de la ville, du quartier et sous quartier
                
                $infosNomsAdresse   = $this->getAdresseComplete($fetchInfosAdresse['idRue'],'rue');
                $infosIds           = $this->getArrayAdresseFrom($fetchInfosAdresse['idRue'],'rue');
                
                $t->assign_vars(array(
                                        'ville'=>$infosIds['ville'],
                                        'villetxt'=>$infosNomsAdresse['ville']
                ));
            
                // assignation du quartier
                $resQuartiers=$this->connexionBdd->requete("SELECT idQuartier,nom FROM quartier WHERE idVille = '".$infosIds['ville']."' order by nom");
                while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
                {
                    $selected="";
                    if($infosIds['quartier']==$fetchQuartiers['idQuartier'])
                        $selected = " selected";
                    
                    if($fetchQuartiers['nom']!='autre')
                    {
                        $t->assign_block_vars("isNotAjoutSousEvenement.quartiers",array(
                                                                'nom'=>$fetchQuartiers['nom'],
                                                                'id'=>$fetchQuartiers['idQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
                
                $t->assign_vars(array('onChangeListeQuartier'=>"appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,'listeSousQuartier')"));
                // assignation du sousQuartier
                $resSousQuartiers=$this->connexionBdd->requete("SELECT idSousQuartier,nom FROM sousQuartier WHERE idQuartier = '".$infosIds['quartier']."' order by nom");
                while($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers))
                {
                    $selected="";
                    if($infosIds['sousQuartier']==$fetchSousQuartiers['idSousQuartier'])
                        $selected=" selected";
                    
                    if($fetchSousQuartiers['nom']!='autre')
                    {   
                        $t->assign_block_vars("isNotAjoutSousEvenement.sousQuartiers",array(
                                                                'nom'=>$fetchSousQuartiers['nom'],
                                                                'id'=>$fetchSousQuartiers['idSousQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
            }
            elseif($fetchInfosAdresse['idSousQuartier']!='0')
            {
                // recherche de la ville, du quartier et sous quartier
                
                $infosNomsAdresse   = $this->getAdresseComplete($fetchInfosAdresse['idSousQuartier'],'sousQuartier');
                $infosIds           = $this->getArrayAdresseFrom($fetchInfosAdresse['idSousQuartier'],'sousQuartier');
                
                $t->assign_vars(array(
                                        'ville'=>$infosIds['ville'],
                                        'villetxt'=>$infosNomsAdresse['ville']
                ));
                
                // assignation du quartier
                $resQuartiers=$this->connexionBdd->requete("SELECT idQuartier,nom FROM quartier WHERE idVille = '".$infosIds['ville']."' order by nom");
                while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
                {
                    $selected="";
                    if($infosIds['quartier']==$fetchQuartiers['idQuartier'])
                        $selected = " selected";
                    
                    if($fetchQuartiers['nom']!='autre')
                    {
                        $t->assign_block_vars("isNotAjoutSousEvenement.quartiers",array(
                                                                'nom'=>$fetchQuartiers['nom'],
                                                                'id'=>$fetchQuartiers['idQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
                
                $t->assign_vars(array('onChangeListeQuartier'=>"appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,'listeSousQuartier')"));
                // assignation du sousQuartier
                $resSousQuartiers=$this->connexionBdd->requete("SELECT idSousQuartier,nom FROM sousQuartier WHERE idQuartier = '".$infosIds['quartier']."' order by nom");
                while($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers))
                {
                    $selected="";
                    if($infosIds['sousQuartier']==$fetchSousQuartiers['idSousQuartier'])
                        $selected=" selected";
                    
                    if($fetchSousQuartiers['nom']!='autre')
                    {   
                        $t->assign_block_vars("isNotAjoutSousEvenement.sousQuartiers",array(
                                                                'nom'=>$fetchSousQuartiers['nom'],
                                                                'id'=>$fetchSousQuartiers['idSousQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
                
            }
            elseif($fetchInfosAdresse['idQuartier']!='0')
            {
                // recherche de la ville, du quartier et sous quartier
                
                $infosNomsAdresse   = $this->getAdresseComplete($fetchInfosAdresse['idQuartier'],'quartier');
                $infosIds           = $this->getArrayAdresseFrom($fetchInfosAdresse['idQuartier'],'quartier');
                
                $t->assign_vars(array(
                                        'ville'=>$infosIds['ville'],
                                        'villetxt'=>$infosNomsAdresse['ville']
                ));
                
                // assignation du quartier
                $resQuartiers=$this->connexionBdd->requete("SELECT idQuartier,nom FROM quartier WHERE idVille = '".$infosIds['ville']."' order by nom");
                while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
                {
                    $selected="";
                    if($infosIds['quartier']==$fetchQuartiers['idQuartier'])
                        $selected = " selected";
                    
                    if($fetchQuartiers['nom']!='autre')
                    {
                        $t->assign_block_vars("isNotAjoutSousEvenement.quartiers",array(
                                                                'nom'=>$fetchQuartiers['nom'],
                                                                'id'=>$fetchQuartiers['idQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
                
                $t->assign_vars(array('onChangeListeQuartier'=>"appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,'listeSousQuartier')"));
                // assignation du sousQuartier
                $resSousQuartiers=$this->connexionBdd->requete("SELECT idSousQuartier,nom FROM sousQuartier WHERE idQuartier = '".$infosIds['quartier']."' order by nom");
                while($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers))
                {
                    $selected="";
                    if($infosIds['sousQuartier']==$fetchSousQuartiers['idSousQuartier'])
                        $selected=" selected";
                    
                    if($fetchSousQuartiers['nom']!='autre')
                    {   
                        $t->assign_block_vars("isNotAjoutSousEvenement.sousQuartiers",array(
                                                                'nom'=>$fetchSousQuartiers['nom'],
                                                                'id'=>$fetchSousQuartiers['idSousQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
            }
            elseif($fetchInfosAdresse['idVille']!='0')
            {
                $infosNomsAdresse   = $this->getAdresseComplete($fetchInfosAdresse['idVille'],'ville');
                $infosIds           = $this->getArrayAdresseFrom($fetchInfosAdresse['idVille'],'ville');
                
                $t->assign_vars(array(
                                        'ville'=>$infosIds['ville'],
                                        'villetxt'=>$infosNomsAdresse['ville']
                ));
                
                // assignation du quartier
                $resQuartiers=$this->connexionBdd->requete("SELECT idQuartier,nom FROM quartier WHERE idVille = '".$infosIds['ville']."' order by nom");
                while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
                {
                    $selected="";
                    if($infosIds['quartier']==$fetchQuartiers['idQuartier'])
                        $selected = " selected";
                    
                    if($fetchQuartiers['nom']!='autre')
                    {
                        $t->assign_block_vars("isNotAjoutSousEvenement.quartiers",array(
                                                                'nom'=>$fetchQuartiers['nom'],
                                                                'id'=>$fetchQuartiers['idQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
                
                $t->assign_vars(array('onChangeListeQuartier'=>"appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,'listeSousQuartier')"));
                // assignation du sousQuartier
                $resSousQuartiers=$this->connexionBdd->requete("SELECT idSousQuartier,nom FROM sousQuartier WHERE idQuartier = '".$infosIds['quartier']."' order by nom");
                while($fetchSousQuartiers = mysql_fetch_assoc($resSousQuartiers))
                {
                    $selected="";
                    if($infosIds['sousQuartier']==$fetchSousQuartiers['idSousQuartier'])
                        $selected=" selected";
                    
                    if($fetchSousQuartiers['nom']!='autre')
                    {   
                        $t->assign_block_vars("isNotAjoutSousEvenement.sousQuartiers",array(
                                                                'nom'=>$fetchSousQuartiers['nom'],
                                                                'id'=>$fetchSousQuartiers['idSousQuartier'],
                                                                'selected'=>$selected
                                                        ));
                    }
                }
            }
        }
        
        
        // parcours des adresses
        
        $numLigne=0;
        if(isset($this->variablesPost['idUnique']))
        {
            foreach($this->variablesPost['idUnique'] as $indice =>$valueIdUnique)
            {
                if((isset($this->variablesGet['supprAdresse']) && $this->variablesGet['supprAdresse']==$valueIdUnique))
                {
                    //
                }
                else
                {
                    $arrayAdresse[$numLigne]['idAdresse']   =0;
                    $arrayAdresse[$numLigne]['txt']         = $this->variablesPost['ruetxt'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['id']          = $this->variablesPost['rue'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['numero']      = $this->variablesPost['numero'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['indicatif']   = $this->variablesPost['indicatif'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['longitude']   = $this->variablesPost['longitude'][$valueIdUnique];
                    $arrayAdresse[$numLigne]['latitude']    = $this->variablesPost['latitude'][$valueIdUnique];
                    $numLigne++;
                }
            }
            
            if(isset($this->variablesPost['ajouterAdresse']))
            {
                $arrayAdresse[$numLigne]['idAdresse']   =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']   = "";
                $arrayAdresse[$numLigne]['longitude']   = "";
                $arrayAdresse[$numLigne]['latitude']    = "";
                $numLigne++;
            }
            
            if(count($this->variablesPost['idUnique'])==1 && isset($this->variablesPost['enleverAdresse']))
            {
                $arrayAdresse[$numLigne]['idAdresse']   =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']   = "";
                $arrayAdresse[$numLigne]['longitude']   = "";
                $arrayAdresse[$numLigne]['latitude']    = "";
                $numLigne++;
            }
        }
        else
        {
            $resAdresses = $this->getAdressesFromEvenementGroupeAdresses($idEvenementGroupeAdresse);
            if(mysql_num_rows($resAdresses)>0 && !isset($this->variablesPost['ajouterAdresse']))
            {
                mysql_data_seek($resAdresses,0);
                while($fetchAdresses = mysql_fetch_assoc($resAdresses))
                {
                    $arrayAdresse[$numLigne]['idAdresse']  =  $fetchAdresses['idAdresse'];
                    $arrayAdresse[$numLigne]['txt']         = $fetchAdresses['nomRue'];
                    $arrayAdresse[$numLigne]['id']          = $fetchAdresses['idRue'];
                    $arrayAdresse[$numLigne]['numero']      = $fetchAdresses['numero'];
                    $arrayAdresse[$numLigne]['indicatif']   = $fetchAdresses['idIndicatif'];
                    $arrayAdresse[$numLigne]['prefixe']     = $fetchAdresses['prefixeRue'];
                    $arrayAdresse[$numLigne]['longitude']   = $fetchAdresses['longitude'];
                    $arrayAdresse[$numLigne]['latitude']    = $fetchAdresses['latitude'];
                    $numLigne++;
                }
            }
            else
            {
                $arrayAdresse[$numLigne]['idAdresse']   =0;
                $arrayAdresse[$numLigne]['txt']         = "";
                $arrayAdresse[$numLigne]['id']          = "";
                $arrayAdresse[$numLigne]['numero']      = "";
                $arrayAdresse[$numLigne]['indicatif']   = "";
                $arrayAdresse[$numLigne]['prefixe']     = "";
                $arrayAdresse[$numLigne]['longitude']   = "";
                $arrayAdresse[$numLigne]['latitude']    = "";
                $numLigne++;
            }
        }
        
        $configArrayRetrieveCoordonneesGoogleMap = array();
        
        for($i=0 ; $i<$numLigne ; $i++)
        {
            // affichage des indicatifs pour chaque adresse
            $nomRue = $arrayAdresse[$i]["txt"];
            if(isset($arrayAdresse[$i]['prefixe']) && $arrayAdresse[$i]['prefixe']!='')
            {
                $nomRue = $arrayAdresse[$i]['prefixe'].' '.$arrayAdresse[$i]["txt"];
            }
            
            
            $coordonnees = $this->getCoordonneesFrom($arrayAdresse[$i]['idAdresse'],'idAdresse');
            
            
            
            $t->assign_block_vars("isNotAjoutSousEvenement.adresses",array(
                                                    'idUnique'                  => $i,
                                                    'idAdresse'                 => $arrayAdresse[$i]['idAdresse'],
                                                    'onClickBoutonChoixRue'     => "document.getElementById('paramChampAppelantRue').value= 'rue".$i."';document.getElementById('iFrameRue').src='".$this->creerUrl('','afficheChoixRue',array('noHeaderNoFooter'=>1))."&archiIdVille='+document.getElementById('ville').value+'&archiIdQuartier='+document.getElementById('quartiers').value+'&archiIdSousQuartier='+document.getElementById('sousQuartiers').value;document.getElementById('calqueRue').style.display='block';document.getElementById('calqueRue').style.top=(getScrollHeight()+50)+'px'",
                                                    "onClickBoutonSupprAdresse" => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('','formulaireGroupeAdresses',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse,'supprAdresse'=>$i))."'",
                                                    "nomRue"                => $nomRue,
                                                    "rue"                   => $arrayAdresse[$i]["id"],
                                                    "numero"                => $arrayAdresse[$i]["numero"],
                                                    "longitude"             =>$coordonnees['longitude'],
                                                    "latitude"              =>$coordonnees['latitude']
                                                ));
            
            // gestion des indicatifs de chaque adresse
            $reqIndicatif = "select idIndicatif, nom from indicatif";
            $resIndicatif = $this->connexionBdd->requete($reqIndicatif);
            while($fetchIndicatif = mysql_fetch_assoc($resIndicatif))
            {
                $selected="";
                //(isset($this->variablesPost['indicatif'.$i]) && $this->variablesPost['indicatif'.$i]!='' && $this->variablesPost['indicatif'.$i]==$fetchIndicatif['idIndicatif'])     ||
                if(($arrayAdresse[$i]['indicatif']==$fetchIndicatif['idIndicatif']))
                {
                    $selected = " selected";
                }
                $t->assign_block_vars("isNotAjoutSousEvenement.adresses.indicatifs",array(
                                                "id"        =>  $fetchIndicatif['idIndicatif'],
                                                "nom"       =>  $fetchIndicatif['nom'],
                                                "selected"  =>  $selected
                ));
            }
            
            

            
            $configArrayRetrieveCoordonneesGoogleMap[$i] = array(
                                'nomChampLatitudeRetour'=>'latitude_'.$i,
                                'nomChampLongitudeRetour'=>'longitude_'.$i,
                                'getAdresseFromElementById'=>true,
                                'jsAdresseValue'=>"document.getElementById('numero".$i."').value+' '+document.getElementById('rue".$i."txt').value+' '+document.getElementById('villetxt').value",
                                'jsToExecuteIfNoAddressFound'=>""
            );
        }
        
        $jsToExecute="document.getElementById('formAjoutDossier').action='".$this->creerUrl('enregistreGroupeAdresses','',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse), false, false)."';testAdresseValideAndSubmit('formAjoutDossier');";
        $arrayJsCoordonneesFromGoogleMap = $googleMap->getJSMultipleRetriveCoordonnees(array('jsToExecuteIfOK'=>$jsToExecute,'jsCodeForWaitingWhileLocalization'=>"document.getElementById('popupAttente').style.top=(getScrollHeight()+200)+'px';document.getElementById('popupAttente').style.display='block';"),$configArrayRetrieveCoordonneesGoogleMap);
        $html.= $arrayJsCoordonneesFromGoogleMap['jsFunctionToExecute'];
        // ***********************************************************************************
        // assignation des boutons
        $t->assign_vars(array(
                                    'formAction'                    => $this->creerUrl('','formulaireGroupeAdresses',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse)),
                                    
                                    'popupVilles'                   => $this->getPopupChoixVille('nouveauDossier'),
                                    
                                    'popupRues'                     => $this->getPopupChoixRue('nouveauDossier'),
                                    
                                    'onClickBoutonAjouterAdresse'           => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('','formulaireGroupeAdresses',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse))."'",                                    
                                    'onClickBoutonEnleverAdresse'           => "document.getElementById('formAjoutDossier').action='".$this->creerUrl('','formulaireGroupeAdresses',array('archiIdEvenementGroupeAdresses'=>$idEvenementGroupeAdresse))."'",
                                    'onClickBoutonValider'                  => $arrayJsCoordonneesFromGoogleMap['jsFunctionCall'],
                                    
                                    'typeBoutonValidation'=>"button",
                                        
                                    'onClickBoutonChoixVille'       =>"document.getElementById('paramChampAppelantVille').value='ville';document.getElementById('calqueVille').style.display='block';",
                                    
                                    'onChangeListeQuartier'         =>"appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('listeQuartier').value,'listeSousQuartiers')",
                                    'popupAttente'                  =>$this->getPopupAttente()
                            ));
        
        
        // ***********************************************************************************
        // messages d'aide contextuelle
        $helpMessages = $this->getHelpMessages('helpAdresse');
        
        foreach($helpMessages as $fieldName => $message)
        {
            $t->assign_vars(array($fieldName=>$message));
        }
        // ***********************************************************************************
        
            
        ob_start();
        $t->pparse('ajoutAdressesMultiple');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }

    // **********************************************************************************************************************************************************************
    // recupere la liste des rues d'un quartier, sous quartier , ville 
    // attention si on met une adresse en parametre , on recupere quand meme un tableau en sortie et la valeur est stockée a l'indice 0
    // **********************************************************************************************************************************************************************
    public function getIdRuesFrom($id,$type='')
    {
    
        $arrayIdRues=array();
        
        switch($type)
        {
            case 'ville':
                $reqVilles = "
                                    SELECT distinct r.idRue as idRue, lower(substr(r.nom,1,1)) as lettre
                                    FROM rue r 
                                    RIGHT JOIN historiqueAdresse ha ON ha.idRue = r.idRue
                                    RIGHT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                                    RIGHT JOIN quartier q ON q.idQuartier = sq.idQuartier
                                    RIGHT JOIN ville v ON q.idVille = v.idVille
                                    WHERE v.idVille = '".$id."'
                                    AND r.idRue IS NOT NULL
                                    ORDER BY lettre
                ";
                
                $resVilles = $this->connexionBdd->requete($reqVilles);
                
                while($fetchVilles = mysql_fetch_assoc($resVilles))
                {
                    $arrayIdRues[] = $fetchVilles['idRue'];
                }
            break;
            case 'pays':
                $reqVilles = "
                                    SELECT distinct r.idRue as idRue, lower(substr(r.nom,1,1)) as lettre
                                    FROM rue r 
                                    RIGHT JOIN historiqueAdresse ha ON ha.idRue = r.idRue
                                    RIGHT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                                    RIGHT JOIN quartier q ON q.idQuartier = sq.idQuartier
                                    RIGHT JOIN ville v ON q.idVille = v.idVille
                                    WHERE v.idPays = '".$id."'
                                    AND r.idRue IS NOT NULL
                                    ORDER BY lettre
                ";
                
                $resVilles = $this->connexionBdd->requete($reqVilles);
                
                while($fetchVilles = mysql_fetch_assoc($resVilles))
                {
                    $arrayIdRues[] = $fetchVilles['idRue'];
                }
            break;
            case 'quartier':
                $req = "
                    SELECT r.idRue as idRue
                    FROM rue r
                    LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                    LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                    WHERE q.idQuartier = '".$id."'
                    ORDER BY r.nom
                    ";
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    $arrayIdRues[] = $fetch['idRue'];
                }
            break;
            
            case 'sousQuartier':
            
            break;
            case 'idAdresse':
                $req = "
                    SELECT ha1.idRue as idRue
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    WHERE 
                        ha1.idAdresse = '".$id."'
                    AND ha2.idAdresse = ha1.idAdresse
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)       
                ";
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
                $arrayIdRues[0] = $fetch['idRue'];
            
            break;
        }
        
        return $arrayIdRues;
    }
    
    // **********************************************************************************************************************************************************************
    // renvoi la liste des quartiers d'une ville
    // **********************************************************************************************************************************************************************
    public function getIdQuartiersFrom($id,$type='')
    {
        $arrayIdQuartier=array();
        switch($type)
        {
            case 'ville':
                $reqQuartiers = "   SELECT distinct q.idQuartier as idQuartier 
                                    FROM quartier q 
                                    RIGHT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                                    RIGHT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                                    RIGHT JOIN historiqueAdresse ha ON ha.idRue = r.idRue
                                    WHERE q.idVille = '".$id."'
                                    ";
                $resQuartiers = $this->connexionBdd->requete($reqQuartiers);
                while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
                {
                    $arrayIdQuartier[] = $fetchQuartiers['idQuartier'];
                }
            break;
            case 'idAdresse':
                
            break;
        }
        return $arrayIdQuartier;
    }
    
    /**
     * Affiche les adresses par regroupements
     * Quand on clique sur un regroupement , on affiche la liste des elements contenant ce regroupement
     * 
     * @return string
     * */
    public function afficheListeRegroupee()
    {
        $html="";
        $t=new Template($this->getCheminPhysique().$this->cheminTemplates);
        $t->set_filenames((array('listeRegroupee'=>'listeRegroupee.tpl')));
        
        $r = new archiRecherche();
        
        
        if(isset($this->variablesGet['modeAffichageListe']))
        {
            $modeAffichageListe = $this->variablesGet['modeAffichageListe'];
        } else {
            $modeAffichageListe = 'default';
        }
        
        $idVilleGeneral = 1;  // strasbourg par defaut
        $s = new objetSession();
        
        if($s->isInSession('archiIdVilleGeneral') && $s->getFromSession('archiIdVilleGeneral')!='')
        {
            $idVilleGeneral = $s->getFromSession('archiIdVilleGeneral');
        }
        elseif(isset($this->variablesGet['archiIdVilleGeneral']))
        {
            $idVilleGeneral = $this->variablesGet['archiIdVilleGeneral'];
            $s->addToSession('archiIdVilleGeneral',$idVilleGeneral);
        }
        
        $fetchInfosVille = $this->getInfosVille($idVilleGeneral,array('fieldList'=>'v.nom as nomVille'));
        
        $nomVilleGeneral = $fetchInfosVille['nomVille'];
        
        $t->assign_vars(array('liens'=>"<a href='".$this->creerUrl('','listeDossiers',array('modeAffichageListe'=>'parRues','archiIdVilleGeneral'=>$idVilleGeneral))."'>"._("Par rues de")." ".$nomVilleGeneral."</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href='".$this->creerUrl('','listeDossiers',array('modeAffichageListe'=>'parQuartiers','archiIdVilleGeneral'=>$idVilleGeneral))."'>"._("Par quartiers de")." ".$nomVilleGeneral."</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href='".$this->creerUrl('','listeDossiers',array('modeAffichageListe'=>'parVilles'))."'>"._("Par villes")."</a>",
        
        ));
        
        
        switch($modeAffichageListe)
        {
            case 'parQuartiers':
                $pagination = new paginationObject();
                
                $arrayQuartiers = $this->getIdQuartiersFrom($idVilleGeneral,'ville');
                
                $arrayQuartiersNotEmpty = $r->getIdQuartiersNotEmpty();
                
                
                $arrayListeQuartiersNotEmpty = array();
                $arrayListeAlphabetique = array();
                foreach($arrayQuartiers as $indice => $idQuartier)
                {
                    if(in_array($idQuartier,$arrayQuartiersNotEmpty['arrayListeQuartiersNonVides']))
                    {
                        $arrayListeQuartiersNotEmpty[] = $idQuartier;
                        
                    }
                }
                
                

                
                
                // pagination
                $nbEnregistrementTotaux = count($arrayListeQuartiersNotEmpty);
                
                // nombre d'images affichées sur une page
                $nbEnregistrementsParPage = 12;
                $arrayPagination=$pagination->pagination(array(
                                        'nomParamPageCourante'=>'archiPageCouranteVille',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire'
                                        ));
                
                $t->assign_vars(array('pagination'=>$arrayPagination['html']));
                
                $req = "SELECT idQuartier, nom 
                        FROM quartier 
                        WHERE idQuartier in ('".implode("','",$arrayListeQuartiersNotEmpty)."')
                        and nom<>'autre'
                        ORDER BY nom ASC
                        LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage."
                        ";
                
                $tableau = new tableau();
                
                $res = $this->connexionBdd->requete($req);
                while($fetch = mysql_fetch_assoc($res))
                {
                    // pour chaque ville on va regarder s'il y a des adresses qui correspondent et on les compte
                    /*$reqCount = "
                                SELECT distinct ha.idAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                LEFT JOIN sousQuartier sq ON sq.idQuartier = '".$fetch['idQuartier']."'
                                LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                                WHERE 
                                    (ha.idRue = IFNULL(r.idRue , 0)
                                OR  ha.idQuartier = '".$fetch['idQuartier']."'
                                OR  ha.idSousQuartier = IFNULL(sq.idSousQuartier,0))
                                AND ha2.idHistoriqueAdresse = ha.idHistoriqueAdresse
                                GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    ";*/
                    
                    
                    
                    $reqRuesQuartier = "SELECT idRue FROM rue WHERE idSousQuartier IN (SELECT idSousQuartier FROM sousQuartier WHERE idQuartier=".$fetch['idQuartier'].")";
                    $reqSousQuartier = "SELECT idSousQuartier FROM sousQuartier WHERE idQuartier = ".$fetch['idQuartier'];
                    
                    $nbAdressesTotales=0;
                    $nbAdressesRues =0;
                    $nbAdressesSousQuartiers=0;
                    $nbAdressesQuartiers=0;
                    
                    $resRuesQuartier = $this->connexionBdd->requete($reqRuesQuartier);
                    while($fetchRuesQuartier = mysql_fetch_assoc($resRuesQuartier))
                    {
                        $reqCountRuesQuartier = "SELECT count(distinct idAdresse) as nbAdressesRues from historiqueAdresse WHERE idRue='".$fetchRuesQuartier['idRue']."'";
                        $resCountRuesQuartier = $this->connexionBdd->requete($reqCountRuesQuartier);
                        $fetchAdressesRues = mysql_fetch_assoc($resCountRuesQuartier);
                        $nbAdressesRues += $fetchAdressesRues['nbAdressesRues'];
                    }
                    
                    $resSousQuartier = $this->connexionBdd->requete($reqSousQuartier);
                    while($fetchSousQuartier = mysql_fetch_assoc($resSousQuartier))
                    {
                        $reqCountSousQuartierQuartier = "SELECT count(distinct idAdresse) as nbAdressesSousQuartiers FROM historiqueAdresse WHERE idSousQuartier='".$fetchSousQuartier['idSousQuartier']."'";
                        $resCountSousQuartierQuartier = $this->connexionBdd->requete($reqCountSousQuartierQuartier);
                        $fetchAdressesSousQuartiers = mysql_fetch_assoc($resCountSousQuartierQuartier);
                        $nbAdressesSousQuartiers += $fetchAdressesSousQuartiers['nbAdressesSousQuartiers'];
                    }
                    
                    $reqCountQuartiers = "SELECT count(distinct idAdresse) as nbAdressesQuartier FROM historiqueAdresse WHERE idQuartier = '".$fetch['idQuartier']."'";
                    $resCountQuartiers = $this->connexionBdd->requete($reqCountQuartiers);
                    $fetchCountQuartiers = mysql_fetch_assoc($resCountQuartiers);
                    $nbAdressesQuartiers += $fetchCountQuartiers['nbAdressesQuartier'];
                    
                    
                    $nbAdressesTotales = $nbAdressesRues + $nbAdressesSousQuartiers + $nbAdressesQuartiers;
                    
                    $nbResultats = "(".$nbAdressesTotales.")";
                    
                
                    $htmlPhoto='';
                    $urlPhoto = $this->getUrlImageFromQuartier($fetch['idQuartier'],'moyen');
                    if(!pia_ereg("transparent.gif",$urlPhoto))  // la fonction renvoi le lien vers une photo transparente si elle ne trouve pas de photo de du quartier
                    {
                        $htmlPhoto = "<a href='".$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetch['idQuartier']))."'><img src='".$urlPhoto."' border=0></a>";
                    }
                    else
                    {
                        // pas de photo
                        $htmlPhoto = "<span style='float:right;margin:0px;padding:0px;'><a href='".$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetch['idQuartier']))."'><table border='' style='margin:0px;padding:0px;border:1px solid #000000;font-size:11px;background-image:url(".$this->getUrlImage()."imageDefautArchiv2.jpg);' width=200 height=150><tr><td align=center valign=center style='padding-top:100px;'>Pas de photo</td></tr></table></a></span>";
                    }
                
                                        
                    $lien = "<a href='".$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetch['idQuartier']))."'>".stripslashes($fetch['nom'])."</a>".$nbResultats;
                    if($nbAdressesTotales>0)
                    {
                        $tableau->addValue($htmlPhoto."<br>".$lien);
                    }
                }
                
                $t->assign_vars(array('elements'=>$tableau->createHtmlTableFromArray(3)));
            break;
            
            case "parVilles":
                
                $arrayVillesNotEmpty = $r->getIdVillesNotEmpty();
                $arrayListeVillesAvecAdresses = $arrayVillesNotEmpty["arrayIdVilles"];
                $listeVilleAvecAdresses = implode(",",$arrayListeVillesAvecAdresses);
                //pagination
                $reqNbVilles = "
                        SELECT 0
                        FROM ville
                        where (idPays = '1' or idPays='2')
                        AND idVille in (".$listeVilleAvecAdresses.")
                        and nom <>'autre'
                        ";
                $resNbVilles = $this->connexionBdd->requete($reqNbVilles);
                $nbEnregistrementTotaux = mysql_num_rows($resNbVilles);
            
                // nombre d'images affichées sur une page
                $nbEnregistrementsParPage = 12;
                $arrayPagination=$this->pagination(array(
                                        'nomParamPageCourante'=>'archiPageCouranteVille',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire'
                                        ));



                $t->assign_vars(array('pagination'=>$arrayPagination['html']));
                
                $req = "
                        SELECT v.nom,v.idVille, lower(substr(v.nom,1,1)) as lettre
                        FROM ville v
                        
                        WHERE (v.idPays = '1' or v.idPays='2')
                        AND v.nom<>'autre'
                        AND idVille in (".$listeVilleAvecAdresses.")
                        ORDER BY lettre
                        LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
                        
                $tableau = new tableau();
    
                $res = $this->connexionBdd->requete($req);
                
                while($fetch = mysql_fetch_assoc($res))
                {
                    // pour chaque ville on va compter les adresses qui correspondent
                    /*$reqCount = "
                                SELECT count(distinct ha.idAdresse) as nbAdresses,ha.idAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha
                                LEFT JOIN quartier q ON q.idVille = '".$fetch['idVille']."'
                                LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                                LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                                WHERE 
                                (   (ha.idRue = IFNULL(r.idRue , 0) and ha.idQuartier=0 and ha.idSousQuartier=0 and ha.idVille=0)
                                OR  
                                    (ha.idRue=0 and ha.idQuartier = IFNULL(q.idQuartier,0) and ha.idSousQuartier=0 and ha.idVille=0)
                                OR  
                                    (ha.idRue=0 and ha.idQuartier=0 and ha.idSousQuartier = IFNULL(sq.idSousQuartier,0) and ha.idVille=0)
                                OR  
                                    (ha.idRue=0 and ha.idQuartier=0 and ha.idSousQuartier =0 and ha.idVille = '".$fetch['idVille']."')
                                )
                                AND 
                                ha2.idHistoriqueAdresse = ha.idHistoriqueAdresse
                                GROUP BY ha.idAdresse, ha.idHistoriqueAdresse
                                HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    ";*/
                    
                    
                    $listeRuesVille = "SELECT idRue FROM rue WHERE idSousQuartier IN (SELECT idSousQuartier FROM sousQuartier WHERE idQuartier IN (SELECT idQuartier FROM quartier WHERE idVille='".$fetch['idVille']."'))";
                    $listeSousQuartiersVille = "SELECT idSousQuartier FROM sousQuartier WHERE idQuartier IN (SELECT idQuartier FROM quartier WHERE idVille='".$fetch['idVille']."')";
                    $listeQuartiersVille = "SELECT idQuartier FROM quartier WHERE idVille='".$fetch['idVille']."'";
                    
                    
                    // comptage du nombre d'adresse liés a ces elements d'adresses
                    $resRuesVille = $this->connexionBdd->requete($listeRuesVille);
                    $nbRues = 0;
                    while($fetchRuesVilles = mysql_fetch_assoc($resRuesVille))
                    {
                        $reqNbAdressesRue = "SELECT count(distinct idAdresse) as nbAdressesRue FROM historiqueAdresse WHERE idRue='".$fetchRuesVilles['idRue']."'";
                        $resNbAdressesRue = $this->connexionBdd->requete($reqNbAdressesRue);
                        $fetchNbAdressesRue = mysql_fetch_assoc($resNbAdressesRue);
                        $nbRues += $fetchNbAdressesRue['nbAdressesRue'];
                    }
                    
                    $resSousQuartiersVille = $this->connexionBdd->requete($listeSousQuartiersVille);
                    $nbSousQuartiers = 0;
                    while($fetchSousQuartiersVille = mysql_fetch_assoc($resSousQuartiersVille))
                    {
                        $reqNbAdressesSousQuartier = "SELECT count(distinct idAdresse) as nbAdressesSousQuartiers FROM historiqueAdresse WHERE idSousQuartier='".$fetchSousQuartiersVille['idSousQuartier']."'";
                        $resNbAdressesSousQuartier = $this->connexionBdd->requete($reqNbAdressesSousQuartier);
                        $fetchNbAdressesSousQuartier = mysql_fetch_assoc($resNbAdressesSousQuartier);
                        $nbSousQuartiers += $fetchNbAdressesSousQuartier['nbAdressesSousQuartiers'];
                    }
                    
                    $resQuartiersVille = $this->connexionBdd->requete($listeQuartiersVille);
                    $nbQuartiers = 0;
                    while($fetchQuartiersVille = mysql_fetch_assoc($resQuartiersVille))
                    {
                        $reqNbAdressesQuartier = "SELECT count(distinct idAdresse) as nbAdressesQuartiers FROM historiqueAdresse WHERE idQuartier='".$fetchQuartiersVille['idQuartier']."'";
                        $resNbAdressesQuartier = $this->connexionBdd->requete($reqNbAdressesQuartier);
                        $fetchNbAdressesQuartier = mysql_fetch_assoc($resNbAdressesQuartier);
                        $nbQuartiers += $fetchNbAdressesQuartier['nbAdressesQuartiers'];
                    }
                    
                    $nbVilles = 0;
                    $reqNbAdressesVilles = "SELECT count(distinct idAdresse) as nbAdressesVilles FROM historiqueAdresse WHERE idVille='".$fetch['idVille']."'";
                    $resNbAdressesVilles = $this->connexionBdd->requete($reqNbAdressesVilles);
                    $fetchNbAdressesVilles = mysql_fetch_assoc($resNbAdressesVilles);
                    $nbVilles = $fetchNbAdressesVilles['nbAdressesVilles'];
                    
                    
                    $totalAdresses = $nbRues + $nbQuartiers + $nbVilles;
                    
                    

                    
                    $nbResultats = " (".$totalAdresses.")";
                    
                    
                    $htmlPhoto="";
                    $urlPhoto = $this->getUrlImageFromVille($fetch['idVille'],'moyen');
                    if($urlPhoto!='')
                        $htmlPhoto = "<a href='".$this->creerUrl('','adresseListe',array('recherche_ville'=>$fetch['idVille']))."'><img src='".$urlPhoto."' border=0></a>";
                    
                    $lien = "<a href='".$this->creerUrl('','adresseListe',array('recherche_ville'=>$fetch['idVille']))."'>".stripslashes($fetch['nom'])."</a>".$nbResultats."<br>";
                    if($totalAdresses>0)
                    {
                        $tableau->addValue($htmlPhoto."<br>".$lien);
                    }
                    
                }
                
                $t->assign_vars(array('elements'=>$tableau->createHtmlTableFromArray(3)));
                
            break;
            case "parRuesDeQuartier":
            default:
                // DEFAULT : affichage parRues
                // s'il n'y a pas de criteres on affiche les dossiers des rues de strasbourg
                $r = new archiRecherche();
                $s = new stringObject();
                
                if(isset($this->variablesGet['archiIdQuartier']) && $this->variablesGet['archiIdQuartier']!='')
                {
                    $arrayRues = $this->getIdRuesFrom($this->variablesGet['archiIdQuartier'] , 'quartier');
                }
                else
                {
                    $arrayRues = $this->getIdRuesFrom($idVilleGeneral , 'ville');
                }
                
                $arrayRuesNotEmpty = $r->getIdRuesNotEmpty();
                $arrayIdRuesNotEmpty=array();
                $arrayListeAlphabetique = array();
                
                foreach($arrayRues as $indice => $idRue)
                {
                    if(in_array($idRue,$arrayRuesNotEmpty['arrayIdRues']))
                    {
                        $arrayIdRuesNotEmpty[] = $idRue;
                        $reqInitiales = "SELECT lower(substring(nom,1,1)) as initiale FROM rue WHERE idRue='".$idRue."'";
                        $resInitiales = $this->connexionBdd->requete($reqInitiales);
                        $fetchInitiales = mysql_fetch_assoc($resInitiales);
                        $initiale = $fetchInitiales['initiale']; 
                        $arrayListeAlphabetique[] = $s->sansAccents($initiale);// enleve les accents
                        
                        if(!isset($this->variablesGet['lettreCourante']) || isset($this->variablesGet['lettreCourante']) && $s->sansAccents($initiale)==$this->variablesGet['lettreCourante'])
                        {
                            $arrayIdRuesNotEmptyWithSelection[] = $idRue; // prend en compte le nombre de rue selectionnée ou non par leur initiales
                        }
                        
                        
                    }
                }
                
                $sqlCritere = "";
                if(isset($this->variablesGet['lettreCourante']) && $this->variablesGet['lettreCourante']!='')
                {
                    $sqlCritere = " AND lower(SUBSTRING(nom,1,1))=lower('".$this->variablesGet['lettreCourante']."') ";
                }

                $nbEnregistrementTotaux = count($arrayIdRuesNotEmptyWithSelection);
                
                // nombre d'images affichées sur une page
                $nbEnregistrementsParPage = 40;
                $pagination = new paginationObject();
                
                if(isset($this->variablesGet['archiIdQuartier']))
                {
                    $arrayPagination=$pagination->pagination(array(
                                        'nomParamPageCourante'=>'archiPageRuesQuartier',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'freeLink',
                                        'urlFreeLink'=>$this->creerUrl('','listeDossiers',array('archiIdQuartier'=>$this->variablesGet['archiIdQuartier'],'modeAffichageListe'=>'parRuesDeQuartier','archiPageRuesQuartier'=>'##numPage##'))
                                        
                                        ));
                }
                else
                {
                    $arrayPagination=$pagination->pagination(array(
                                        'nomParamPageCourante'=>'archiPageCouranteVille',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire',
                                        'arrayListeAlphabetique'=>$arrayListeAlphabetique
                                        ));
                }


                $t->assign_vars(array('pagination'=>$arrayPagination['html']));
                
                $req = "
                        SELECT nom,idRue,prefixe, lower(substr(nom,1,1)) as lettre
                        FROM rue
                        WHERE 
                         idRue in ('".implode("','",$arrayIdRuesNotEmpty)."')
                        $sqlCritere
                        ORDER BY lettre
                        LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
                        

                $res = $this->connexionBdd->requete($req);
                
                $tableau = new tableau();
                
                while($fetch = mysql_fetch_assoc($res))
                {
                
                    // pour chaque rue on va regarder s'il y a des adresses qui correspondent et on les compte (affichage du nombre de resultats)
                    $reqCount = "SELECT count(distinct idAdresse) as nbAdresses FROM historiqueAdresse WHERE idRue='".$fetch['idRue']."'";
                
                    $resCount = $this->connexionBdd->requete($reqCount);
                    $fetchCount = mysql_fetch_assoc($resCount);
                    $valCount = $fetchCount['nbAdresses'];
                    $nbResultats = " (".$valCount.")";
                
                
                    $htmlPhoto='';
                    //$urlPhoto = $this->getUrlImageFromRue($fetch['idRue'],'moyen');
                    $urlPhoto="getPhotoSquare.php?id=".$this->getIdImageFromRue($fetch['idRue']);
                    if(!pia_ereg("transparent.gif",$urlPhoto))  // la fonction renvoi le lien vers une photo transparente si elle ne trouve pas de photo de la rue
                    {
                        $htmlPhoto = "<a href='".$this->creerUrl('', 'adresseListe', array('recherche_rue'=>$fetch['idRue']))."'><img src='".$urlPhoto."' border=0></a><br>";
                    }
                    else
                    {
                        // pas de photo
                        $htmlPhoto = "<a style='margin:0px;padding:0px;' href='".$this->creerUrl('','adresseListe',array('recherche_rue'=>$fetch['idRue']))."'><table border='' style='margin:0px;padding:0px;border:1px solid #000000;font-size:11px;background-image:url(".$this->getUrlImage()."imageDefautArchiv2.jpg);' width=200 height=150><tr><td align=center valign=center style='padding-top:100px;'>Pas de photo</td></tr></table></a>";
                    }
                
                    $lien = "<a href='".$this->creerUrl('','adresseListe',array('recherche_rue'=>$fetch['idRue']))."'>".stripslashes($fetch['prefixe']." ".$fetch['nom'])."</a>".$nbResultats;
                    if($valCount>0)
                    {
                        $tableau->addValue($htmlPhoto.$lien);
                    }
                    
                }
                
                $t->assign_vars(array('elements'=>$tableau->createHtmlTableFromArray(5)));

            break;
            
        }
        
        
        ob_start();
        $t->pparse('listeRegroupee');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // **********************************************************************************************************************************************************************
    // enregistrement des adresses liees a un evenement groupe d'adresses
    // **********************************************************************************************************************************************************************
    public function enregistreGroupeAdresses($idEvenementGroupeAdresses=0)
    {
        $this->connexionBdd->getLock(array('historiqueAdresse','_adresseEvenement'));
        $arrayIdAdresses = array();
        
        $longitude = '0';
        $latitude = '0';
        if(isset($this->variablesPost['latitude']) && isset($this->variablesPost['longitude']) && $this->variablesPost['latitude']!='' && $this->variablesPost['longitude']!='')
        {
            $longitude = $this->variablesPost['longitude'];
            $latitude = $this->variablesPost['latitude'];
        }
        
        if(isset($this->variablesPost['idUnique']))
        {
            foreach($this->variablesPost['idUnique'] as $indice => $valueIdUnique)
            {
                if($this->variablesPost['rue'][$valueIdUnique]!='' && $this->variablesPost['rue'][$valueIdUnique]!='0')
                {
                    $arrayAdresses[] = $this->ajouterAdresseFromArray(array(
                                                        'idPays'=>1,
                                                        'idVille'=>$this->variablesPost['ville'],
                                                        'idQuartier' => $this->variablesPost['quartiers'],
                                                        'idSousQuartier'=>$this->variablesPost['sousQuartiers'],
                                                        'numero'=>$this->variablesPost['numero'][$valueIdUnique],
                                                        'indicatif'=>$this->variablesPost['indicatif'][$valueIdUnique],
                                                        'idRue'=>$this->variablesPost['rue'][$valueIdUnique],
                                                        'longitude'=>$this->variablesPost['longitude'][$valueIdUnique],
                                                        'latitude'=>$this->variablesPost['latitude'][$valueIdUnique]
                                                ),array('envoiMailAdminSiNouvelleAdresse'=>true));
                }
                else
                {
                    $arrayAdresses[] = $this->ajouterAdresseFromArray(array(
                                                        'idPays'=>1,
                                                        'idVille'=>$this->variablesPost['ville'],
                                                        'idQuartier' => $this->variablesPost['quartiers'],
                                                        'idSousQuartier'=>$this->variablesPost['sousQuartiers'],
                                                        'numero'=>0,
                                                        'indicatif'=>0,
                                                        'idRue'=>0,
                                                        'longitude'=>'',
                                                        'latitude'=>''
                                                ),array('envoiMailAdminSiNouvelleAdresse'=>true));
                
                }
            }
        }
        
        // on effaces les liaisons précédentes
        $resDelete = $this->connexionBdd->requete("DELETE FROM _adresseEvenement where idEvenement = '".$idEvenementGroupeAdresses."'");
        
        // on effectue la liaison entre les adresses enregistrées a partir du formulaire, et l'evenement:
        foreach($arrayAdresses as $indice => $value)
        {
            $queryLiaison = "INSERT INTO _adresseEvenement (idAdresse,idEvenement) VALUES ('".$value['idAdresse']."','".$idEvenementGroupeAdresses."')";
            $resLiaison = $this->connexionBdd->requete($queryLiaison);
        }
        
        
        //$evenement = new archiEvenement();
        
        //$retourEvenement = $evenement->afficher($idEvenementGroupeAdresses);
        
        $this->connexionBdd->freeLock(array('historiqueAdresse','_adresseEvenement'));

        // rafraichissement des caches
        //$cache = new cacheObject();
        //$cache->refreshCache();
        
        //echo $retourEvenement['html'].$this->getListeCommentaires($idEvenementGroupeAdresses).$this->getFormulaireCommentaires($idEvenementGroupeAdresses,$this->getCommentairesFields());;
        header("Location: ".$this->creerUrl('', '', array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$value['idAdresse'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresses), false, false));
        //echo "<script>lollocation.href='".$this->creerUrl('', '', array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$value['idAdresse'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresses), false, false)."';</script>";
        
    }
    
    
    
    
    // **********************************************************************************************************************************************************************
    // contenu de la popup pour le choix d'une ville
    // **********************************************************************************************************************************************************************
    public function afficheChoixVille($lettre='a')
    {

        if(isset($this->variablesGet['archiLettre']) && $this->variablesGet['archiLettre']!='')
            $lettre = $this->variablesGet['archiLettre'];
        
        $modeAffichage="";
        if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']!='')
        {
            $modeAffichage = $this->variablesGet['modeAffichage'];
        }
        
        
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeVille'=>'listeVille.tpl')));

        
        $reqNbVille = "
            select 0
            from ville 
            where lower(substr(nom,1,1))='".$lettre."'
            and nom<>'autre'
            order by nom
        ";
        $resNbVille = $this->connexionBdd->requete($reqNbVille);
        
        $nbEnregistrementTotaux = mysql_num_rows($resNbVille);
        
        
        if($nbEnregistrementTotaux ==0)
        {
            // recherche de la premiere lettre ou il y a des resultats
            $reqNbVilleNew = "
                    select lower(substr(v.nom,1,1)) as lettre
                    from ville v
                    WHERE 1=1
                    and nom<>'autre'
                    order by lettre ASC
                    LIMIT 1
                    ";
            $resNbVilleNew = $this->connexionBdd->requete($reqNbVilleNew);
            $nbEnregistrementTotaux = mysql_num_rows($resNbVilleNew);// nombre d'enregistrements pour la nouvelle lettre courante
            // recuperation de la nouvelle lettre courante
            $fetchNewLettre = mysql_fetch_assoc($resNbVilleNew);
            $lettre = $fetchNewLettre['lettre'];
        }
        
        
        // nombre d'images affichées sur une page
        $nbEnregistrementsParPage = 5;
        $arrayPagination=$this->pagination(array(
                                        'nomParamPageCourante'=>'archiPageCouranteVille',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire'
                                        ));
        
        
        
        // affichage des villes de france
        $reqVille = "
            select idVille,nom,codepostal, lower(substr(nom,1,1)) as lettre
            from ville 
            where lower(substr(nom,1,1))='".$lettre."'
            and nom<>'autre'
            order by nom,lettre ASC
            LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
            
        $resVille=$this->connexionBdd->requete($reqVille);
        
        
        $html .= $this->afficheListeAlphabetique(array('champ'=>'nom','table'=>'ville','where'=>" and nom<>'autre' "),$modeAffichage);
        
        $html .=$arrayPagination['html']."<br>";

        if(mysql_num_rows($resVille)>0)
        {
            mysql_data_seek($resVille, 0); // on replace le curseur au debut de la liste des enregistrements
        }
        else
        {
            echo "Pas de résultat.";
        }

        
        
        while($fetchVille = mysql_fetch_assoc($resVille))
        {
                
                switch($modeAffichage)
                {
                    case 'nouveauDossier':
                    case 'modifUtilisateur':
                    default:
                                    $t->assign_block_vars('villes',array(
                                                    'url'=>"#",
                                                    'onclick'=>"parent.document.getElementById(parent.document.getElementById('paramChampAppelantVille').value+'txt').value='".addslashes($fetchVille['nom'])."';parent.document.getElementById(parent.document.getElementById('paramChampAppelantVille').value).value='".$fetchVille['idVille']."';parent.document.getElementById('calqueVille').style.display='none';parent.appelAjax('".$this->creerUrl('','afficheSelectQuartier',array('noHeaderNoFooter'=>1))."&archiIdVille='+parent.document.getElementById(parent.document.getElementById('paramChampAppelantVille').value).value,'listeQuartier',true);parent.document.getElementById('sousQuartiers').innerHTML='<option value=0>Aucun</option>';",
                                                    'nom'=>$fetchVille['nom']
                ));
                    break;
                    
                }
                
                

        }
        
        
        ob_start();
        $t->pparse('listeVille');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    /**
     * Affiche la liste des adresses suivant criteres et modeAffichage ,  modeAffichage permet de préciser ou sera affiché la liste (calque ou non,  et lieu du calque) 
    // afin de changer le comportement des liens et limiter certains affichages,  non utiles dans la popup par exemple
    // cette fonction ,  permet maintenant aussi de renvoyer les resultats dans un tableau ,  sans forcement les affichers (utile dans la recherche ...)
    * 
    * @param array  $criteres      Critères
    * @param string $modeAffichage Mode d'affichage
    * @param array  $params        Paramètres
    * 
    * @return array
    * */
    public function afficherListe($criteres = array() ,  $modeAffichage='', $params = array())
    {
        $html="";
        
        $sqlId='';
        
        $nbAdresses=0;
        
        $arrayRetour=array(); // tableau de renvoyé a la sortie de la fonction contenant les liens
        
        $arrayRetourLiensVoirBatiments = array(); // tableau renvoyé a la sortie de la fonction contenant les liens "voir tous les batiments de la rue/quartier"
        
        $arrayIdAdressesRetour=array(); // tableau renvoyé a la sortir de la fonction contenant la liste des idAdresses ,  permet d'economiser quelques requetes
        
        $arrayIdEvenementsGARetour = array(); // tableau avec la liste des evenements de groupes d'adresses
        
        
        $tabParametresAutorises = array('ordre',  'tri',  'limit', 'archiIdEvenement',  'debut',  'selection',  'id', 'recherche_rue', 'recherche_quartier', 'recherche_ville', 'recherche_longitude', 'recherche_latitude', 'recherche_rayon', 'recherche_groupesAdressesFromAdresse', 'recherche_sousQuartier', 'displayAdresseIfNoCoordonneesGroupeAdresse');
        
        $sqlMotCle="";
        $sqlSelectMotCle="";
        $sqlOrderByPoidsMotCle="";
        $sqlOrderBy=" ha1.date ";
        
        
        foreach ($tabParametresAutorises as $param) {
            if (isset($this->variablesGet[$param]) and !isset($criteres[$param]))
                $criteres[$param] = $this->variablesGet[$param];
        }
        if (isset($criteres['recherche_groupesAdressesFromAdresse']) && $criteres['recherche_groupesAdressesFromAdresse']!='') {
            $tabSqlWhere[] = ' AND ha1.idAdresse='.$criteres['recherche_groupesAdressesFromAdresse'];
            if (isset($criteres['displayAdresseIfNoCoordonneesGroupeAdresse']) && $criteres['displayAdresseIfNoCoordonneesGroupeAdresse']==1) {
                $tabSqlWhere[] = " (ae.longitudeGroupeAdresse='0' AND ae.latitudeGroupeAdresse='0') " ;
            }
        }
        if (!empty($criteres['recherche_rue']) && $criteres['recherche_rue']!='0') {
        
            // une rue a ete precisée par l'utilisateur
            $tabSqlWhere[] = ' AND ha1.idRue='.$criteres['recherche_rue'];
        
            if (isset($this->variablesGet['noAdresseSansNumero'])) {
                $tabSqlWhere[]=' ha1.numero<>0 AND ha1.numero IS NOT NULL ';
            }

            $sqlOrderBy = "ha1.numero ASC,  ha1.date";
        } else if (!empty($criteres['recherche_sousQuartier']) && $criteres['recherche_sousQuartier']!='0') {
            // un sous quartier a ete précisé par l'utilisateur,  on recupere donc les rue de ce sous quartier
            $rep = $this->connexionBdd->requete("SELECT idRue FROM rue WHERE idSousQuartier=".$criteres['recherche_sousQuartier']);
            while ($res = mysql_fetch_object($rep)) {
                $tabIdRue[] = $res->idRue;
            }
            if (count($tabIdRue) > 0)
                $tabSqlWhere[] = ' AND (ha1.idRue IN(\''.implode("', '",  array_unique($tabIdRue)).'\') OR ha1.idSousQuartier='.$criteres['recherche_sousQuartier'].')';
            else
                $tabSqlWhere[] = ' AND ha1.idSousQuartier='.$criteres['recherche_sousQuartier'];
        } else if (!empty($criteres['recherche_quartier']) && $criteres['recherche_quartier']!='0') {
            $rep = $this->connexionBdd->requete(
                "SELECT r.idRue,  sQ.idSousQuartier 
                FROM sousQuartier sQ 
                LEFT JOIN rue r USING(idSousQuartier)
                WHERE sQ.idQuartier=".$criteres['recherche_quartier']
            );
            while ($res = mysql_fetch_object($rep)) {
                if (!empty($res->idRue))
                    $tabIdRue[]          = $res->idRue;
                
                if (!empty($res->idSousQuartier))
                    $tabIdSousQuartier[] = $res->idSousQuartier;
            }
            
            $sqlSelect = ' AND (';
            if (count($tabIdRue) > 0)
                $sqlSelect .= ' ha1.idRue IN(\''.implode("', '",  array_unique($tabIdRue)).'\') OR ';
            if (count($tabIdSousQuartier) > 0)
                $sqlSelect .= ' ha1.idSousQuartier IN(\''.implode("', '",  array_unique($tabIdSousQuartier)).'\') OR ';
            
            $sqlSelect .= ' ha1.idQuartier='.$criteres['recherche_quartier'] .')';
            $tabSqlWhere[] = $sqlSelect;
        } elseif (!empty($criteres['recherche_ville']) && $criteres['recherche_ville']!='0') {
            $s = new objetSession();
            $s->addToSession('archiIdVilleGeneral', $criteres['recherche_ville']);
            // une ville a ete précisée,  on cherche les quartiers,  sous quartier et rues correspondant
            $rep = $this->connexionBdd->requete(
                "
                SELECT q.idQuartier as idQuartier,  sq.idSousQuartier as idSousQuartier,  r.idRue as idRue
                FROM ville v
                LEFT JOIN quartier q ON q.idVille = v.idVille
                LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                WHERE v.idVille = '".$criteres['recherche_ville']."'
                "
            );
            
            $tabIdRue=array();
            $tabIdSousQuartier=array();
            $tabIdQuartier=array();
            
            while ($fetch = mysql_fetch_assoc($rep)) {
                if ($fetch['idRue']!=null && $fetch['idRue']!='0')
                    $tabIdRue[] = $fetch['idRue'];
                if ($fetch['idSousQuartier']!=null && $fetch['idSousQuartier']!='0')
                    $tabIdSousQuartier[] = $fetch['idSousQuartier'];
                if ($fetch['idQuartier']!=null && $fetch['idQuartier']!='0')
                    $tabIdQuartier[] = $fetch['idQuartier'];
            }
        
            if (mysql_num_rows($rep)>0) {
        
                $sqlSelect=' AND (';
            
            
                if (count($tabIdQuartier)>0) {
                    $sqlSelect .=' ha1.idQuartier IN (\''.implode("', '", array_unique($tabIdQuartier)).'\') OR ';
                }
                
                if (count($tabIdSousQuartier)>0) {
                    $sqlSelect .=' ha1.idSousQuartier IN (\''.implode("', '", array_unique($tabIdSousQuartier)).'\') OR ';
                }
                
                if (count($tabIdRue)>0) {
                    $sqlSelect .=' ha1.idRue IN (\''.implode("', '", array_unique($tabIdRue)).'\') OR ';
                }
                
                $sqlSelect .=' ha1.idVille='.$criteres['recherche_ville'].')';
                $tabSqlWhere[]=$sqlSelect;
            }
        } elseif (!empty($criteres['recherche_pays']) && $criteres['recherche_pays']!='0') {
            // une ville a ete précisée,  on cherche les quartiers,  sous quartier et rues correspondant
            $rep = $this->connexionBdd->requete(
                "
                SELECT v.idVille as idVille,  q.idQuartier as idQuartier,  sq.idSousQuartier as idSousQuartier,  r.idRue as idRue
                FROM pays p
                LEFT JOIN ville v on v.idPays = p.idPays
                LEFT JOIN quartier q ON q.idVille = v.idVille
                LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                WHERE p.idPays = '".$criteres['recherche_pays']."'
                "
            );
        
            $tabIdRue=array();
            $tabIdSousQuartier=array();
            $tabIdQuartier=array();
            $tabIdVille=array();
            
            while ($fetch = mysql_fetch_assoc($rep)) {
                if ($fetch['idRue']!=null && $fetch['idRue']!='0')
                    $tabIdRue[] = $fetch['idRue'];
                if ($fetch['idSousQuartier']!=null && $fetch['idSousQuartier']!='0')
                    $tabIdSousQuartier[] = $fetch['idSousQuartier'];
                if ($fetch['idQuartier']!=null && $fetch['idQuartier']!='0')
                    $tabIdQuartier[] = $fetch['idQuartier'];
                if ($fetch['idVille']!=null && $fetch['idVille']!='0')
                    $tabIdVille[] = $fetch['idVille'];
            }

            if (mysql_num_rows($rep)>0) {
                $sqlSelect=' AND (';
            
                if (count($tabIdVille)>0) {
                    $sqlSelect .=' ha1.idVille IN (\''.implode("', '", array_unique($tabIdVille)).'\') OR ';
                }

                if (count($tabIdQuartier)>0) {
                    $sqlSelect .=' ha1.idQuartier IN (\''.implode("', '", array_unique($tabIdQuartier)).'\') OR ';
                }
                
                if (count($tabIdSousQuartier)>0) {
                    $sqlSelect .=' ha1.idSousQuartier IN (\''.implode("', '", array_unique($tabIdSousQuartier)).'\') OR ';
                }
                
                if (count($tabIdRue)>0) {
                    $sqlSelect .=' ha1.idRue IN (\''.implode("', '", array_unique($tabIdRue)).'\') OR ';
                }
                
                $sqlSelect .='  ha1.idPays='.$criteres['recherche_pays'].')';
                $tabSqlWhere[]=$sqlSelect;
            }
        }
        
        $sqlAdressesSupplementaires="";
        $tabidAdresses=array();
        //$listeIdEvenementsGroupeAdresse="";
        $whereVillesModerees="";
        if (!empty($criteres['recherche_motcle'])) {
            // recherche du mot cle dans les adresses
            
            $motcle = $criteres['recherche_motcle'];
            
            
            //$motcle = stripslashes($motcle);
            $motcle = Pia_eregreplace(", ", " ", $motcle);
            $motcle = Pia_eregreplace("\"", " ", $motcle);
            

            
            if ($modeAffichage=='popupAjoutAdressesLieesSurEvenement') {
                // si on est en mode affichage popupAjoutAdressesLieesSurEvenement,  on est dans la popup de selection d'adresse pour lier un evenement a une ou plusieurs adresses
                // dans le cas d'un moderateur ,  il faut donc limiter sa recherche aux adresses de la ville qu'il administre
                $authentification = new archiAuthentification();
                $u = new archiUtilisateur();
                if ($authentification->estConnecte() && $u->isAuthorized('evenement_lier_adresses', $authentification->getIdUtilisateur())) {
                    if ($u->getIdProfil($authentification->getIdUtilisateur())==3) {
                        $arrayVillesModerees = $u->getArrayVillesModereesPar($authentification->getIdUtilisateur());
                        if (count($arrayVillesModerees)>0) {
                            $whereVillesModerees=" AND v.idVille IN (".implode(', ', $arrayVillesModerees).") ";
                        } else {
                            $whereVillesModerees=" AND v.idVille=0 "; // si le moderateur n'a aucune ville en moderation on limite sa recherche a aucune ville
                        }
                    }
                }
            }
            
            $motcleEscaped = str_replace(' ', '%', $motcle);
            $motcleEscaped = str_replace("'", '', $motcleEscaped);
            $motcleEscaped = mysql_escape_string($motcleEscaped);
            $motcleEscaped = str_replace("\\", "%", $motcleEscaped);
            
            
            $motcleEntier = $motcle;
            $motcleEntierEscaped = str_replace("'", '', $motcleEntier);
            $motcleEntierEscaped = mysql_escape_string($motcleEntierEscaped);
            $motcleEntierEscaped = str_replace("\\", " ", $motcleEntierEscaped);
            
            
            
            $motcleEntierEscaped = "REGEXP \"[[:<:]]".$motcleEntierEscaped."[[:>:]]\"";
            
            $motcleAdresseEntiere = "REGEXP \"[[:<:]]".str_replace(array("'", "\\"), array('', ''), $motcleEntier)."[[:>:]]\"";
            
            //                    OR he1.titre ".$motcleEntierEscaped."
            //OR he1.description ".$motcleEntierEscaped." 
            
            $sqlMotCle="
                AND
                (
                    q.nom LIKE \"%".$motcleEscaped."%\"
                    OR sq.nom LIKE \"%".$motcleEscaped."%\"
                    OR v.nom LIKE \"%".$motcleEscaped."%\"
                    OR p.nom LIKE \"%".$motcleEscaped."%\"
                    OR r.nom LIKE \"%".$motcleEscaped."%\"
                    OR he1.titre LIKE \"%".$motcleEscaped."%\"
                    OR he1.description LIKE \"%".$motcleEscaped."%\" 

                    OR CONCAT_WS('', he1.titre, CONVERT(ha1.numero USING utf8), ind.nom, r.prefixe, r.nom, sq.nom,  q.nom,  v.nom,  p.nom) LIKE '%".$motcleEscaped."%'
                    OR CONCAT_WS('', pers.nom, pers.prenom) LIKE \"%".$motcleEscaped."%\"
                    OR CONCAT_WS('', pers.prenom, pers.nom) LIKE \"%".$motcleEscaped."%\"
                )";
                
            $sqlSelectMotCle="";//, IF(ha1.idQuartier !=0 and ha1.idRue=0 and ha1.numero=0 and q.nom  LIKE \"%".$motcle."%\", 1000000000, 0) as poidsSpeQuartier
            
            
            $sqlOrderBy="(
                              IF(r.nom LIKE \"%".$motcle."%\", 10000, 0) 
                            + IF(sq.nom LIKE \"%".$motcle."%\", 10000, 0) 
                            + IF(q.nom  LIKE \"%".$motcle."%\", 10000, 0) 
                            + IF(v.nom  LIKE \"%".$motcle."%\", 10000, 0) 
                            + IF(he1.titre LIKE \"%".$motcle."%\", 100, 0) 
                            + IF(he1.description LIKE \"%".$motcle."%\", 10, 0) 
                            + IF(he1.titre ".$motcleEntierEscaped.", 2000000000000000000, 0) 
                            + IF(he1.description ".$motcleEntierEscaped.", 200000000000000000, 0) 
                            + IF(ha1.idQuartier !=0 and ha1.idRue=0 and ha1.numero=0 and q.nom  LIKE \"%".$motcle."%\", 1000000000, 0)
                            + IF(ha1.numero=0 and CONCAT_WS('', r.prefixe, ' ', r.nom) ".$motcleAdresseEntiere.", 1000000000000000000000000000, 0)
                            + IF(ha1.numero=0 and ha1.idRue=0 and ha1.idQuartier=0 and ha1.idSousQuartier=0 and v.nom LIKE \"%".$motcle."%\", 10000000000000000, 0)
                            + IF(CONCAT_WS('', CONVERT(ha1.numero USING utf8), ind.nom, r.prefixe, r.nom, sq.nom,  q.nom,  v.nom,  p.nom) LIKE '%".$motcleEscaped."%', 1000000000000000000000000000, 0)
                            + IF(CONCAT_WS('', CONVERT(ha1.numero USING utf8), ' ', r.prefixe, ' ', r.nom) ".$motcleAdresseEntiere.", 1000000000000000000000000000, 0)
                            + IF(CONCAT_WS('', he1.titre, CONVERT(ha1.numero USING utf8), ' ', r.prefixe, ' ', r.nom) LIKE \"%".$motcleEscaped."%\", 2000000000000000000000000000, 0)
                            + IF(CONCAT_WS('', pers.nom, pers.prenom) LIKE \"%".$motcleEscaped."%\", 20000000000000000, 0)
                            + IF(CONCAT_WS('', pers.prenom, pers.nom) LIKE \"%".$motcleEscaped."%\", 20000000000000000, 0)
                            )
                            ";
            
            
            //$sqlOrderByPoidsMotCle="poidsTotal DESC";
            //
        }
        
        
        // ************************************************************************************************************************************************
        // recherche sur un element de l'evenement
        if (!isset($sqlWhere))
            $sqlWhere = '';
        if (!isset($sqlJoin))
            $sqlJoin = ''; // debug laurent
        if (isset($criteres['recherche_courant']) && $criteres['recherche_courant']!='') {
            $courant = implode("', '",  $criteres['recherche_courant']);
            $sqlWhere .= ' AND _eCA.idCourantArchitectural IN (\''.$courant.'\') ';
            $sqlJoin  .= " LEFT JOIN _evenementCourantArchitectural _eCA ON _eCA.idEvenement = he1.idEvenement ";
        }

        if (isset($criteres['recherche_typeStructure']) && $criteres['recherche_typeStructure']!='' && $criteres['recherche_typeStructure']!='0') {
            $sqlWhere .= " AND he1.idTypeStructure='".$criteres['recherche_typeStructure']."' ";
        }

        if (isset($criteres['recherche_typeEvenement']) && !empty($criteres['recherche_typeEvenement'])) {
            $sqlWhere .= " AND he1.idTypeEvenement='".$criteres['recherche_typeEvenement']."' ";
        }

        if (isset($criteres['recherche_source']) && !empty($criteres['recherche_source'])) {
            $sqlWhere .= " AND he1.idSource='".$criteres['recherche_source']."' ";
        }

        if (isset($criteres['recherche_personnes']) && !empty($criteres['recherche_personnes'])) {
            $personnes = implode("', '",  $criteres['recherche_personnes']);
            $sqlWhere .= " AND _eP.idPersonne IN (\'".$personnes."\') ";
            $sqlJoin  .= " LEFT JOIN _evenementPersonne _eP ON _eP.idEvenement = he1.idEvenement ";
        }
        
        if (isset($criteres['recherche_anneeDebut']) && $criteres['recherche_anneeDebut']!='') {
            $sqlWhere .= " AND (extract(YEAR FROM he1.dateDebut)>='".$criteres['recherche_anneeDebut']."') ";
        }
        
        if (isset($criteres['recherche_anneeFin']) && $criteres['recherche_anneeDebut']!='') {
            $sqlWhere .= " AND (extract(YEAR FROM he1.dateDebut)<='".$criteres['recherche_anneeFin']."') ";
        }
        
        if (isset($criteres['recherche_MH']) && $criteres['recherche_MH']=='1' && isset($criteres['recherche_ISMH']) && $criteres['recherche_ISMH']=='1') {
            // ATTENTION,  si les deux cases sont cochées ,  on fait un "OU" entre les deux champs pour le résultat de la recherche
            $sqlWhere .= " AND (he1.MH='1' OR he1.ISMH='1') ";
        } else {
            if (isset($criteres['recherche_MH']) && $criteres['recherche_MH']=='1') {
                $sqlWhere .= " AND he1.MH='1' ";
            }
            
            if (isset($criteres['recherche_ISMH']) && $criteres['recherche_ISMH']=='1') {
                $sqlWhere .= " AND he1.ISMH='1' ";
            }
        }

        
        
        // ************************************************************************************************************************************************
        
        
        $sqlSelectCoordonnees="";
        // criteres concernant les coordonnées longitudes et latitude : recherche des adresses dans un rayon de n metres autour des coordonnées donnees en criteres
        if (isset($criteres['recherche_latitude']) && isset($criteres['recherche_longitude']) && isset($criteres['recherche_rayon']) && $criteres['recherche_latitude']!='' && $criteres['recherche_longitude']!='' && $criteres['recherche_rayon']!='') {
            $sqlSelectCoordonnees=" AND ha1.latitude<>0 AND ha1.longitude<>0 AND ha1.latitude<>'' AND ha1.longitude<>'' AND ((acos(sin(".$criteres['recherche_latitude']."*PI()/180) * sin(ha1.latitude*PI()/180) + cos(".$criteres['recherche_latitude']."*PI()/180) * cos(ha1.latitude*PI()/180) * cos((".$criteres['recherche_longitude']." - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000<".$criteres['recherche_rayon']." ";
        }
        
        
                
        // ****************************************************************************************************
        // ajout d'adresses pouvant provenir de la recherche avancee d'evenements
        // ****************************************************************************************************
        if (isset($criteres['adressesSupplementairesExternes'])) {
            $listeIdAdresses = implode("', '", array_unique(array_merge($tabidAdresses, $criteres['adressesSupplementairesExternes'])));
            $sqlAdressesSupplementaires = " AND ha1.idAdresse in ('".$listeIdAdresses."') ";
            $sqlOrderBy = " CAST(ha1.numero as signed) ASC,  1 ";
        }

        
        $sqlEvenementsGroupeAdressesSupplementaires = "";
        if (isset($criteres['groupesAdressesSupplementairesExternes'])) {
            $listeGroupesAdresses = implode("','", array_unique($criteres['groupesAdressesSupplementairesExternes']));
            $sqlEvenementsGroupeAdressesSupplementaires = "AND ee.idEvenement in ('".$listeGroupesAdresses."')";
            $sqlOrderBy = " CAST(ha1.numero as signed) ASC,  1 "; 
        }


        if (!isset($criteres['ordre'])) {
            $sqlOrdre = 'nomRue';
        } else {
            switch ($criteres['ordre']) {
            case 'rue':    $sqlOrdre = 'nomRue';
                break;
            case 'sousQuartier':    $sqlOrdre = 'nomSousQuartier';
                break;
            case 'quartier':    $sqlOrdre = 'nomQuartier';
                break;
            case 'ville':        $sqlOrdre = 'nomVille';
                break;
            case 'pays':        $sqlOrdre = 'nomPays';
                break;
            default:        $sqlOrdre = 'nomRue';
            }
        }
        
        
        
        if (isset($criteres['selection']) AND is_numeric($criteres['id']) AND $criteres['id'] > 0) {
            switch ($criteres['selection']) {
            case 'rue':        $tabSqlWhere[] = ' AND (ha1.idRue='.$criteres['id'].' OR r.idRue='.$criteres['id'].')';
                break;
            case 'ville':        $tabSqlWhere[] = ' AND (ha1.idVille='.$criteres['id'].' OR v.idVille='.$criteres['id'].')';
                break;
            case 'quartier':    $tabSqlWhere[] = ' AND (ha1.idQuartier='.$criteres['id'].' OR q.idQuartier='.$criteres['id'].')';
                break;
            case 'sousQuartier':    $tabSqlWhere[] = ' AND (ha1.idSousQuartier='.$criteres['id'].' OR sq.idSousQuartier='.$criteres['id'].')';
                break;
            case 'pays':        $tabSqlWhere[] = ' AND (ha1.idPays='.$criteres['id'].' OR p.idPays='.$criteres['id'].')';
                break;
            default:        
                break;
            }
        }
        
        // si l'on precise un id evenement dans les criteres    (attention : idEvenement est de type GROUPE d'ADRESSES)
        if (!isset($sqlJoin))
            $sqlJoin ="";
            
        $selectEvenement="";
        if (isset($criteres['archiIdEvenement'])) {
            $tabSqlWhere[] = " AND ae.idEvenement = '".$criteres['archiIdEvenement']."'";
        }

        if (!isset($sqlWhere)) // debug laurent : je ne sais pas pourquoi cela n'a pas ete initialisé plus haut ,  a verifier
            $sqlWhere = '';
            
        if (isset($tabSqlWhere)) {
            $prefix = '';
            foreach ($tabSqlWhere AS $val) {
                $sqlWhere .= ' '.$prefix.' '.$val;
                $prefix    = 'AND';
            }
        }
        /*        else {
            $sqlWhere ='1';
        }
        */
        if (!isset($criteres['debut']) OR !is_numeric($criteres['debut']) OR $criteres['debut'] < 1) {
            $sqlLimit = '0,  10';
            $valDebutSuivant = 0;
            $valDebutPrecedent = 0;
            $criteres['debut'] = 0;
        } else {
            $sqlLimit = $criteres['debut'].',  10';
            if ($criteres['debut'] > 9 )
                $valDebutPrecedent = $criteres['debut'] -10;
            else
                $valDebutPrecedent = 0;
            $valDebutSuivant = $criteres['debut'];
        }
        
        
        // permet de rajouter une limite au retour de la requete principale ,  utilisé dans les modes ou l'on affiche pas le resultat ,  mais ou l'on recupere les idAdresses seulement dans le tableau de retour
        if (isset($params['sqlLimitExterne']) && $params['sqlLimitExterne']!='') {
            $sqlLimit = " ".$params['sqlLimitExterne']." ";
        }
        

        if (isset($criteres['tri'])) {
            if ($criteres['tri'] == 'desc')
                $sqlTri = 'DESC';
            else
                $sqlTri = 'ASC';
        } else {
            $sqlTri = 'ASC';
        }
        
        // si une ville generale est precisé sinon c'est strasbourg
        if (isset($this->variablesGet['archiIdVilleGeneral']) && $this->variablesGet['archiIdVilleGeneral']!='' && isset($this->variablesGet['archiIdPaysGeneral']) && $this->variablesGet['archiIdPaysGeneral']!='') {
            $sqlWhere .= " AND v.idVille = '".$this->variablesGet['archiIdVilleGeneral']."' AND p.idPays='".$this->variablesGet['archiIdPaysGeneral']."' ";
        } else {
            //$sqlWhere = " AND v.idVille = '1' AND p.idPays='1' ";
        }
        
        if (isset($criteres['toutesLesDemolitions'])) {
            $arrayIdAdressesDemolitions = $this->getIdAdressesFromCriteres(array('whereSql'=>"te.nom = 'Démolition'"));
            $sqlAdressesSupplementaires = " AND ha1.idAdresse in ('".implode("', '", $arrayIdAdressesDemolitions)."')";
            if (!isset($sqlWhere))
                $sqlWhere="";

        }
        
        if (isset($criteres['tousLesTravaux'])) {
            if (!isset($sqlWhere))
                $sqlWhere="";
            
            $arrayIdAdressesTravaux = $this->getIdAdressesFromCriteres(array('whereSql'=>"(te.nom ='Construction' and EXTRACT(YEAR FROM he1.dateDebut)='".date('Y')."') or te.nom='Rénovation' or te.nom='Extension' or te.nom='Transformation' or te.nom='Ravalement'"));
            $sqlAdressesSupplementaires = " AND ha1.idAdresse in ('".implode("', '", $arrayIdAdressesTravaux)."')";
            
        }
        
        if (isset($criteres['tousLesEvenementsCulturels'])) {
            $arrayIdAdressesEvenements = $this->getIdAdressesFromCriteres(array('whereSql'=>"te.groupe='1'"));
            $sqlAdressesSupplementaires = " AND ha1.idAdresse in ('".implode("', '", $arrayIdAdressesEvenements)."')";
            if (!isset($sqlWhere))
                $sqlWhere="";
        }
        
        $sqlSelectionExterne="";
        if (isset($criteres['sqlSelectionExterne'])) {
            $sqlSelectionExterne=$criteres['sqlSelectionExterne'];
        }
        
        
        
        
        // bidouille pour que l'on affiche encore toutes les adresses en mode detail dans l'encars qui affiche la liste des adresses,  ce qui va faire que la requete ne renvoie des groupes d'adresses en double
        $critereSelectionIdAdressesModeAffichageListeAdressesCount = "";
        $critereSelectionIdAdressesModeAffichageListeAdressesRequete = "";
        if ($modeAffichage == 'listeDesAdressesDuGroupeAdressesSurDetailAdresse') {
            $critereSelectionIdAdressesModeAffichageListeAdressesCount = ",  ha1.idAdresse";
            $critereSelectionIdAdressesModeAffichageListeAdressesRequete = ",  ha1.idAdresse as idAdresse,  ha1.numero,  ha1.idQuartier,  ha1.idVille, ind.nom, 
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
                IF (ha1.idPays != 0,  ha1.idPays,  v.idPays) AS idPays, 
                
                ha1.numero as numero, 
                ha1.idHistoriqueAdresse, 
                ha1.idIndicatif as idIndicatif";
        }
        


        
        $sqlCount = "
                SELECT distinct ee.idEvenement as idEvenementGroupeAdresse    $critereSelectionIdAdressesModeAffichageListeAdressesCount
                
                FROM historiqueAdresse ha2, historiqueAdresse ha1
                
                
                
                RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                
                
                LEFT JOIN rue r         ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' , ha1.idSousQuartier , r.idSousQuartier )
                LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' , ha1.idQuartier , sq.idQuartier )
                LEFT JOIN ville v        ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' , ha1.idVille , q.idVille )
                LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' , ha1.idPays , v.idPays )
                
                LEFT JOIN _evenementPersonne ep ON ep.idEvenement = he1.idEvenement
                LEFT JOIN personne pers ON pers.idPersonne = ep.idPersonne
                LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                ".$sqlJoin."
                WHERE
                    ha2.idAdresse = ha1.idAdresse
                ". $sqlWhere ." ".$sqlAdressesSupplementaires." ".$sqlEvenementsGroupeAdressesSupplementaires." ".$sqlSelectionExterne." ".$sqlMotCle." 
                ".$sqlSelectCoordonnees." ".$whereVillesModerees."
                GROUP BY ha1.idAdresse,  he1.idEvenement, ha1.idHistoriqueAdresse, he1.idHistoriqueEvenement
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
        ";
        
        
        $result = $this->connexionBdd->requete($sqlCount);
        
        
        $nbReponses = mysql_num_rows($result);
        $nbResult=$nbReponses;
        
        
        if (($nbResult - $valDebutSuivant ) > 10)
            $valDebutSuivant += 10;
        
        
        
        $nbAdresses = $nbReponses;
        $sqlAdressesSupplementairesRechercheRelancee="";
        // on a effectue une recherche par mot cle ,  mais pas de resultat,  on va donc voir si la recherche concerne une adresse precise et rechercher les numeros autour de cette adresse
        if ($nbAdresses==0 && isset($criteres['recherche_motcle']) && $criteres['recherche_motcle']!='' && !isset($this->variablesGet['relanceRecherche'])) {
            // si la recherche par mot cle n'a pas donné de resultat ,  on la relance avec seulement le nom de la rue sans l'eventuel numero d'adresse qui a pu etre ajouté par l'utilisateur
            
            $retourAdressesAutour = $this->getIdAdressesAutourAdressesCourante($criteres['recherche_motcle']);
            $sqlMotCle = $retourAdressesAutour['sqlMotCle'];
            $arrayAdressesProches = $retourAdressesAutour['arrayIdAdresses'];
            $sqlAdressesSupplementairesRechercheRelancee=" AND ha1.idAdresse in ('".implode("', '", $arrayAdressesProches)."') ";
            $nbAdresses = count($arrayAdressesProches);
            $nbResult = $nbAdresses;
            $nbReponses = $nbAdresses;
            
        }
        
        
        
    
        $t=new Template('modules/archi/templates/');
        
        if (!isset($criteres['useTemplateFile'])) {
            $t->set_filenames(array('listeAdresses'=>'listeAdresses.tpl'));
        } else {
            $t->set_filenames(array('listeAdresses'=>$criteres['useTemplateFile']));
        }
        
        // gestion du titre de la liste des adresses
        if (!isset($criteres['cacheTitre'])) {
            if (isset($criteres['titre']))
                $t->assign_vars(array('titre'=>$criteres['titre']));
            else {
                if (isset($this->variablesGet['selection']) && $this->variablesGet['selection']=='personne' && isset($this->variablesGet['id']) && $this->variablesGet['id']!='0') {
                    // on affiche un titre et une description
                    $personneObj = new archiPersonne();
                    $infosPersonne = $personneObj->getInfosPersonne($this->variablesGet['id']);
                    $titre="";
                    if (isset($infosPersonne['nomMetier']) && $infosPersonne['nomMetier']!="") {
                        $titre = "<span itemprop='jobTitle'>".ucwords(stripslashes($infosPersonne['nomMetier']))."</span> : ";
                    }
                    $titre.="<span itemprop='name'><span itemprop='familyName'>".ucwords(stripslashes($infosPersonne['nom']))."</span> <span itemprop='givenName'>".ucwords(stripslashes($infosPersonne['prenom']))."</span></span>";
                    
                    $description="<div class='personHeader tableauResumeAdresse'>";

                    $authentification = new archiAuthentification();

                    if ($authentification->estConnecte()) {
                        $description.="<ul style='float:right;'><li><a href='".$this->creerUrl("", "editPerson", array("id"=>$_GET["id"]))."'>"._("Modifier")."</a></li>";
                        $description.="<li><a href='".$this->creerUrl("", "choosePicturePerson", array("id"=>$_GET["id"]))."'>"._("Sélectionner l'image principale")."</a></li>";
                        
                        if ($authentification->estAdmin()) {
                            $description.="<li><a href='".$this->creerUrl("", "deletePerson", array("id"=>$_GET["id"]))."'>"._("Supprimer")."</a></li>";
                        }
                        $description.='</ul>';
                    }
                    $description.="<img src='".archiPersonne::getImage($this->variablesGet['id'])."' alt=''/>
                    <div style='display:inline-block;'>";
                    if ($infosPersonne['dateNaissance']!='0000-00-00') {
                        $description.="<br>"._("Date de naissance :")." <span itemprop='birthDate'>".$this->date->toFrench($infosPersonne['dateNaissance'])."</span>";
                    }
                    if ($infosPersonne['dateDeces']!='0000-00-00') {
                        $description.="<br>"._("Date de décès :")." <span itemprop='deathDate'>".$this->date->toFrench($infosPersonne['dateDeces'])."</span>";
                    }
                    $description.="</div><br/>";
                    $relatedPeople=archiPersonne::getRelatedPeople($this->variablesGet['id']);
                    if (!empty($relatedPeople)) {
                        $description.= "<div style='float:right;'>";
                        $description.="<h3>Personnes liées :</h3>";
                        $description.="<ul>";
                        foreach ($relatedPeople as $relatedPerson) {
                            $name=archiPersonne::getName($relatedPerson);
                            $description.="<li><a href='".$this->creerUrl("", "evenementListe", array("selection"=>"personne", "id"=>$relatedPerson))."'>".$name->prenom." ".$name->nom."</a></li>";
                        }
                        $description.="</ul></div>";
                    }
                    $description.="<div style='clear:right;'></div>";
                    //$bbCode = new bbCodeObject();
                    
                    //$descriptionPersonne = $bbCode->convertToDisplay(array('text'=>$infosPersonne['description']));
                    $description.="</div><br/><br/>";
                    $e = new archiEvenement();
                    $reqEvent = "
                    SELECT idEvenement 
                    FROM _personneEvenement
                    WHERE idPersonne = ".$this->variablesGet['id']."
                    ";
                    $resEvent = $this->connexionBdd->requete($reqEvent);
                    if (mysql_num_rows($resEvent)>0) {
                        $fetchEvent = mysql_fetch_assoc($resEvent);
                        $e= $e->afficher($fetchEvent["idEvenement"],  "personne");
                        $description.= $e["html"];
                    }
                    
                    //$description.="<br><br><h2>"._("Liste de ses réalisations :")."</h2>";
                    
                    
                    $t->assign_vars(
                        array(
                            'titre'=>$titre, 'description'=>$description,
                            "divBegin"=>"<div itemscope itemtype='http://schema.org/Person'>",
                            "divEnd"=>"</div>"
                        )
                    );
                } elseif (isset($this->variablesGet['selection']) && $this->variablesGet['selection']=='source' && isset($this->variablesGet['id']) && $this->variablesGet['id']!='') {
                    $sourceObj = new archiSource();
                    $bbCode = new bbCodeObject();
                    $resSource = $sourceObj->getMysqlResSource(array('sqlFields'=>"s.nom as nomSource,  s.description as descriptionSource,  ts.nom as nomTypeSource", 'sqlWhere'=>" AND s.idSource ='".$this->variablesGet['id']."' "));
                    $fetchSource = mysql_fetch_assoc($resSource);
                    
                    $titre = "Source : ".$fetchSource['nomSource'];
                    $description = "";
                    if ($fetchSource['nomTypeSource']!='') {
                        $description .= "<b>Type de source : </b>".$fetchSource['nomTypeSource']."<br><br>";
                    }

                    $description .= $fetchSource['descriptionSource'];
                    $t->assign_vars(array('titre'=>$titre));
                    $t->assign_vars(array('description'=>"<br>".$bbCode->convertToDisplay(array('text'=>$description))."<br><br><h2>Liste des évènements concernés :</h2>"));
                } elseif (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource' && isset($this->variablesGet['source']) && $this->variablesGet['source']!='') {
                    if (!isset($this->variablesGet['noDescription'])) {
                        $bbCode = new bbCodeObject();
                        $reqSource = "SELECT nom,  description FROM source WHERE idSource='".$this->variablesGet['source']."'";
                        $resSource = $this->connexionBdd->requete($reqSource);
                        $fetchSource = mysql_fetch_assoc($resSource);
                        $t->assign_vars(array('titre'=>'Source : '.stripslashes($fetchSource['nom'])));
                        
                        
                        $logoSource = "";
                        $colspan = "";
                        if (file_exists($this->getCheminPhysique()."images/logosSources/".$this->variablesGet['source'].".jpg")) {
                            $logoSource = "<td><a href='".$this->creerUrl('', 'afficheGrandFormatSource', array("archiIdSource"=>$this->variablesGet['source']))."'><img src='".$this->getUrlImage()."logosSources/".$this->variablesGet['source'].".jpg' border=0></a></td>";
                            $colspan = "colspan=2";
                        }
                        
                        
                        $t->assign_vars(array('description'=>"<table border=''><tr>$logoSource<td>".$bbCode->convertToDisplay(array('text'=>stripslashes($fetchSource['description'])))."</td></tr><tr><td $colspan><b><div style='font-size:12px;'>Voici la liste des adresses où nous mentionnons cette source</div></b></td></tr></table>"));
                    }
                } else {
                    $t->assign_vars(array('titre'=>'Adresses'));
                }
            }
        }
        
        // gestion de la pagination de la recherche
        switch ($modeAffichage) {
        case 'calqueImage':
            $urlPrecedent = '#';
            $urlPrecedentOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutPrecedent)))."', 'resultatsAdresse')";
            $urlSuivant = '#';
            $urlSuivantOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutSuivant)))."', 'resultatsAdresse')";
            break;
        case 'calqueImageChampsMultiples':
            $urlPrecedent = '#';
            $urlPrecedentOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutPrecedent)))."', 'resultatsAdresse')";
            $urlSuivant = '#';
            $urlSuivantOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutSuivant)))."', 'resultatsAdresse')";
            break;
        case 'calqueEvenement':
                $urlPrecedent = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutPrecedent)));
                $urlPrecedentOnClick = "";
                $urlSuivant = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutSuivant)));
                $urlSuivantOnClick = "";
                $tabTempo = array(
                array(   'url'     => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom'))), 
                    'urlOnClick' => '', 
                    'titre'   => 'Titre', 
                    'urlDesc' => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom',  'tri'   => 'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc'  => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom',  'tri'   => 'asc'))), 
                    'urlAscOnClick' => ''), 
                array(   'url'     => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image'))), 
                    'urlOnClick' => '', 
                    'titre'   => 'Image', 
                    'urlDesc' => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image',  'tri'   => 'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc'  => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image',  'tri'   => 'asc'))), 
                    'urlAscOnClick' => '')
            );
            
            break;
        default:
            $urlPrecedent = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutPrecedent)));
            $urlPrecedentOnClick = "";
            $urlSuivant = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $valDebutSuivant)));
            $urlSuivantOnClick = "";
            $tabTempo = array(
                /*array(   'url'     => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image'))), 
                    'urlOnClick' => '', 
                    'titre'   => 'Image', 
                    'urlDesc' => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image',  'tri'   => 'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc'  => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'image',  'tri'   => 'asc'))), 
                    'urlAscOnClick' => ''),*/
                array(   'url'     => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom'))), 
                    'urlOnClick' => '', 
                    'titre'   => 'Titre', 
                    'urlDesc' => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom',  'tri'   => 'desc'))), 
                    'urlDescOnClick' => '', 
                    'urlAsc'  => $this->creerUrl('', '', array_merge($this->variablesGet,  array('ordre' => 'nom',  'tri'   => 'asc'))), 
                    'urlAscOnClick' => '')
                
                
            );
        }
        
        if (!isset($_GET["selection"]) || $_GET["selection"]!="personne") {
            // on peut cacher l'affichage du nombre de reponses suivant l'affichage souhaité
            if (!isset($criteres['cacheNbReponses'])) {
                $nbReponses=$nbReponses." ".ngettext("réponse", "réponses", $nbReponses);
            } else {
                $nbReponses="";
            }
                
            
            $t->assign_block_vars(
                't',  array(
                    'urlPrecedent'         => $urlPrecedent, 
                    'urlPrecedentOnClick'    => $urlPrecedentOnClick, 
                    'urlSuivant'           => $urlSuivant, 
                    'urlSuivantOnClick'    => $urlSuivantOnClick, 
                    'nbReponses'           => $nbReponses
                )
            );
        
            if (!isset($criteres['cacheEnteteTri'])) {
                for ($i=0; $i<count($tabTempo); $i++) {
                    $t->assign_block_vars('t.liens',  $tabTempo[$i]);
                }
            }
            
            if ($criteres['debut'] == 0 OR $criteres['debut'] < 51) {
                $debutNav = 0;
                $finNav   = 200;
            } else {
                $debutNav = $criteres['debut']-50;
                $finNav   = $debutNav + 150;
            }
            for ($i=$debutNav; $i<$finNav ; $i+=10) {
                if ($criteres['debut'] == $i-10 && $nbReponses>0) {
                    $t->assign_block_vars('t.nav.courant',  array());
                }
                
                if ($nbReponses > $i) {
                    switch ($modeAffichage) {
                    case 'calqueImage':
                        $urlNbOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $i)))."', 'resultatsAdresse');";
                        $urlNb = '#';
                        break;
                    case 'calqueEvenement':
                        //$urlNbOnClick = "appelAjax('".$this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $i)))."', 'resultatsAdresse');";
                        //$urlNb = '#';
                        $urlNbOnClick = '';
                        $urlNb = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $i)));
                        break;
                    case 'calqueImageChampsMultiples':
                        $urlNbOnClick = '';
                        $urlNb = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $i)));                    
                        break;
                    default:
                        $urlNbOnClick = '';
                        $urlNb = $this->creerUrl('', '', array_merge($this->variablesGet,  array('debut' => $i)));
                    }
                    $t->assign_block_vars(
                        't.nav', 
                        array(
                            'urlNbOnClick' => $urlNbOnClick, 
                            'urlNb'        => $urlNb, 
                            'nb'           => ($i/10)+1)
                    );
                    
                }
            }
        
        



        
        $sql = "
        SELECT distinct ee.idEvenement as idEvenementGA $critereSelectionIdAdressesModeAffichageListeAdressesRequete
        
        
        FROM historiqueAdresse ha2, historiqueAdresse ha1
        
        
        
        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
        LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
        LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
        
        LEFT JOIN _evenementPersonne ep ON ep.idEvenement = he1.idEvenement
        LEFT JOIN personne pers ON pers.idPersonne = ep.idPersonne
        LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
        
        LEFT JOIN rue r         ON r.idRue = ha1.idRue
        LEFT JOIN sousQuartier sq    ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' , ha1.idSousQuartier , r.idSousQuartier )
        LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' , ha1.idQuartier , sq.idQuartier )
        LEFT JOIN ville v        ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' , ha1.idVille , q.idVille )
        LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' , ha1.idPays , v.idPays )
        ".$sqlJoin."
        
        
        WHERE
            ha2.idAdresse = ha1.idAdresse 
            
            ". $sqlWhere ." ".$sqlAdressesSupplementaires." ".$sqlEvenementsGroupeAdressesSupplementaires." ".$sqlSelectionExterne." ".$sqlMotCle." 
            ".$sqlSelectCoordonnees." ".$sqlAdressesSupplementairesRechercheRelancee." ".$whereVillesModerees."
        AND ae.idAdresse IS NOT NULL
        GROUP BY ha1.idAdresse, he1.idEvenement, ha1.idHistoriqueAdresse,  he1.idHistoriqueEvenement
        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
        ORDER BY  ".$sqlOrderBy."
                    DESC,  CAST(ha1.numero as signed) ASC";
        if (!isset($params['sqlNoLimit']) || $params['sqlNoLimit']==false) {
            $sql.= " LIMIT ".$sqlLimit."
        ";////ORDER BY  ".pia_substr($sqlOrderByPoidsMotCle, 0, -1)."
        }
        
    
        
        
        
        //echo $sql."<br><br>";
        // ***************************************************************************************************************************************
        // affichage des resultats de la recherche
        // ***************************************************************************************************************************************
        $requeteAdresse = $this->connexionBdd->requete($sql);

        
        
        
        // dans le cas de la popup on ne veut pas afficher le detail d'une adresse
        // ceci arrive quand le resultat de la recherche ne renvoit qu'un resultat ,  par defaut on va sur l'evenement,  sauf pour les cas suivant:
        switch ($modeAffichage) {
        case 'popupAjoutAdressesLieesSurEvenement':
        case 'popupRechercheAdressePrisDepuis':
        case 'popupRechercheAdresseVueSur':
        case 'popupRechercheAdresseAdminParcours':
        case 'popupDeplacerEvenementVersGroupeAdresse':
        case 'personnalite':
            $criteres['desactivateRedirection']=true; 
        default:
            break;
        }
        
        // dans le cas de la liste des adresses des sources (menu 'nos sources'),  on ne fait pas de redirection sur le detail s'il n'y a qu'une seule adresse
        if (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource') {
            $criteres['desactivateRedirection']=true; 
        }
        
        
        // si on utilise le template par defaut ,  c'est qu'on est dans un affichage de resultat de recherche ,  sinon c'est un affichage de detail d'adresse
        if (!isset($criteres['useTemplateFile']) && $nbReponses==1 && !isset($criteres['desactivateRedirection'])) {
            $fetch=mysql_fetch_assoc($requeteAdresse);
            // s'il n'y a qu'un seul resultat a la recherche on redirige automatiquement vers le resultat
            // a voir pour ne pas utiliser le javascript et afficher directement avec la fonction afficherDetail
            
            $fetchIdAdresse = $this->getFetchOneAdresseElementsFromGroupeAdresse($fetch['idEvenementGA']);
            
            
            header("Location: ".$this->creerUrl('', '', array('archiAffichage'=>'adresseDetail', 'archiIdAdresse'=>$fetchIdAdresse['idAdresse'], 'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGA']), false, false));
        } else {
            if (mysql_num_rows($requeteAdresse)>0) {
                while ($fetch=mysql_fetch_assoc($requeteAdresse)) {
                    // on recupere un idAdresse,  idQuartier ,  idRue etc appartenant au groupe d'adresse pour l'urlRewriting
                    
                    if ($modeAffichage != 'listeDesAdressesDuGroupeAdressesSurDetailAdresse') {
                        $fetch = $this->getFetchOneAdresseElementsFromGroupeAdresse($fetch['idEvenementGA']);
                    }
                    
                    // *******************************************************************************************************************
                    // recuperation de l'adresse


                    // *******************************************************************************************************************
                    
                    // *******************************************************************************************************************
                    // titre de l'evenement ajouté derriere l'adresse
                    // on prend le premier titre qui n'est pas vide
                    // et on fabrique le lien avec l'ancre vers l'evenement qui pourra etre cliqué directement
                    // on a une adresse qui correspond a un groupe d'adresse ,  on va donc chercher le titre des evenements qui correspondent au groupe d'adresse
                    
                    switch ($modeAffichage) {
                    case 'popupRechercheAdressePrisDepuis':
                    case 'popupRechercheAdresseVueSur':
                        // pas d'affichage des evenements dans le cas des popups sur la modification d'image
                        $titresEvenements="";
                        break;
                    default:
                    
                        // dans les autres cas on affiche les evenements
                        $titresEvenements="";
                        $tabTitresEvenements = array();
                        $reqTitresEvenements = "
                            SELECT he1.titre as titre ,  ae.idAdresse as idAdresse,  he1.idSource as idSource,  he1.numeroArchive as numeroArchive
                            FROM historiqueEvenement he1,  historiqueEvenement he2
                            RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ".$fetch['idEvenementGA']."
                            RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                            WHERE he2.idEvenement = he1.idEvenement
                            AND he1.idEvenement = ee.idEvenementAssocie
                            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
                            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                            ORDER BY he1.dateDebut, he1.idHistoriqueEvenement
                        ";//RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = '".$fetch['idAdresse']."'
                        
                        $resTitresEvenements = $this->connexionBdd->requete($reqTitresEvenements);
                        
                        $positionAncre=0;
                        while ($fetchTitresEvenements = mysql_fetch_assoc($resTitresEvenements)) {
                            if (trim($fetchTitresEvenements['titre'])!='') {
                                $baliseDebutEvenementConcerne = "";
                                $baliseFinEvenementConcerne = "";

                                // si on est en mode affichage de la liste des sources sur menu "nos sources" 
                                if ((isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource' && isset($this->variablesGet['source']) && ($fetchTitresEvenements['idSource']==$this->variablesGet['source']))||($fetchTitresEvenements['idSource']==0 && $fetchTitresEvenements['numeroArchive']!='' && isset($this->variablesGet['source']) && $this->variablesGet['source']==24)) {
                                    $baliseDebutEvenementConcerne = "<b>";
                                    $baliseFinEvenementConcerne = "</b>";
                                }
                                $tabTitresEvenements[] = "<a href='".$this->creerUrl('', '', array('archiIdAdresse'=>$fetch['idAdresse'], 'archiAffichage'=>'adresseDetail', 'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGA'], 'debut'=>''))."#".$positionAncre."'>".$baliseDebutEvenementConcerne.stripslashes($fetchTitresEvenements['titre']).$baliseFinEvenementConcerne."</a>";
                            }
                            $positionAncre++;
                        }
                        $titresEvenements = implode(' - ', $tabTitresEvenements);
                        
                        break;
                    }
                    // *******************************************************************************************************************
                    $stringObject = new stringObject();
                    
                    
                    // recherche de l'intitule de l'adresse
                    if (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']!='recherche') {
                        switch ($this->variablesGet['archiAffichage']) {
                        case 'listeAdressesFromRue':
                            $nomAdresse ='';
                            $nomAdresse = stripslashes($this->getIntituleAdresseFrom($fetch['idEvenementGA'], 'idEvenementGroupeAdresse', array('styleCSSAdresse'=>"style='font-size:12px;'", 'displayFirstTitreAdresse'=>true, 'isAfficheAdresseStyle'=>true)));
                            //$nomAdresse = stripslashes($this->getIntituleAdresse($fetch, array('styleCSSAdresse'=>"style='font-size:12px;'", 'displayFirstTitreAdresse'=>true, 'isAfficheAdresseStyle'=>true)));
                            $nomAdresseNoStyle= stripslashes($this->getIntituleAdresseFrom($fetch['idEvenementGA'], 'idEvenementGroupeAdresse'));
                            break;
                        default:
                            $nomAdresse ='';
                            $nomAdresse = stripslashes($this->getIntituleAdresse($fetch));
                            $nomAdresseNoStyle= $nomAdresse;
                            break;
                        }

                    } elseif (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='recherche') {
                        $nomAdresse ='';
                        $nomAdresse = stripslashes($this->getIntituleAdresseFrom($fetch['idEvenementGA'], 'idEvenementGroupeAdresse', array('styleCSSAdresse'=>"style='font-size:12px;'", 'displayFirstTitreAdresse'=>true, 'isAfficheAdresseStyle'=>true)));
                        //$nomAdresse = stripslashes($this->getIntituleAdresse($fetch, array('styleCSSAdresse'=>"style='font-size:12px;'", 'displayFirstTitreAdresse'=>true, 'isAfficheAdresseStyle'=>true)));
                        $nomAdresseNoStyle= stripslashes($this->getIntituleAdresseFrom($fetch['idEvenementGA'], 'idEvenementGroupeAdresse'));
                    } else {
                        $nomAdresse ='';
                        $nomAdresse = stripslashes($this->getIntituleAdresse($fetch));
                        $nomAdresseNoStyle= $nomAdresse;
                    }
                    
                    
                    
                    // mise en place du lien de l'adresse suivant l'affichage ou l'on est
                    switch ($modeAffichage) {
                    case 'calqueImage':
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "document.getElementById(document.getElementById('paramChampsAppelantAdresse').value).value='".$fetch['idAdresse']."';document.getElementById(document.getElementById('paramChampsAppelantAdresse').value+'txt').value='".$nomAdresse."';document.getElementById('calqueAdresse').style.display='none';";
                        /*$urlNomRue        = '#';
                        $urlNomRueOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomSousQuartier = '#';
                        $urlNomSousQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomQuartier = '#';
                        $urlNomQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomVille = '#';
                        $urlNomVilleOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomPays = '#';
                        $urlNomPaysOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)))."', 'resultatsAdresse');";
                        */
                        break;
                    case 'calqueImageChampsMultiples':
                        // les liens renvoient la valeur dans un champ select et non pas dans un champ texte ,  mais la meme popup peut etre appelé plusieurs fois pour plusieurs champs differents du meme type
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById(parent.document.getElementById('paramChampsAppelantAdresse').value).innerHTML+='<option selected=\'selected\' value=\'".$fetch['idAdresse']."\'>".addslashes($nomAdresse)."</option>';";
                        $urlNomRue        = '#';
                        $urlNomRueOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomSousQuartier = '#';
                        $urlNomSousQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomQuartier = '#';
                        $urlNomQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomVille = '#';
                        $urlNomVilleOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomPays = '#';
                        $urlNomPaysOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)))."', 'resultatsAdresse');";
                        break;
                    case 'calqueImageChampsMultiplesRetourSimple':
                        // les liens renvoient la valeur dans un champ texte et non pas dans une liste multiple ,  mais la meme popup peut etre appelé plusieurs fois pour plusieurs champs differents du meme type
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById(parent.document.getElementById('paramChampsAppelantAdresse').value).value='".$fetch['idAdresse']."'; parent.document.getElementById(parent.document.getElementById('paramChampsAppelantAdresse').value+'txt').value='".addslashes($nomAdresse)."';";
                        $urlNomRue        = '#';
                        $urlNomRueOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomSousQuartier = '#';
                        $urlNomSousQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomQuartier = '#';
                        $urlNomQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomVille = '#';
                        $urlNomVilleOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomPays = '#';
                        $urlNomPaysOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)))."', 'resultatsAdresse');";
                        break;
                    case 'calqueEvenement':
                        $urlDetailHref = "#";
                        $urlDetailOnClick = "parent.document.getElementById('adresses').innerHTML+='<option selected=\'selected\' value=\'".$fetch['idAdresse']."\'>".addslashes($nomAdresse)."</option>';";
                        $urlNomRue        = '#';
                        $urlNomRueOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomSousQuartier = '#';
                        $urlNomSousQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomQuartier = '#';
                        $urlNomQuartierOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomVille = '#';
                        $urlNomVilleOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)))."', 'resultatsAdresse');";
                        $urlNomPays = '#';
                        $urlNomPaysOnClick = "appelAjax('".$this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)))."', 'resultatsAdresse');";
                        break;
                    case 'popupRechercheAdressePrisDepuis':
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById('listePrisDepuisDiv'+parent.document.getElementById('identifiantRetour').value).innerHTML+='".str_replace(array("'", "\""), array("\\'", "&#34;"), $nomAdresse)."<a  style=\'cursor:pointer;\' onclick=\'retirerPrisDepuis(&#34;".$fetch['idAdresse']."_".$fetch['idEvenementGA']."&#34;, '+parent.document.getElementById('identifiantRetour').value+');\'>(-)</a><br>';parent.document.getElementById('prisDepuis'+parent.document.getElementById('identifiantRetour').value).innerHTML+='<option value=\'".$fetch["idAdresse"]."_".$fetch['idEvenementGA']."\' SELECTED>".str_replace(array("'", "\""), array("\'", "&#34;"), $nomAdresse)."</option>';";
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                        break;
                    case 'popupRechercheAdresseVueSur':
                    
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById('listeVueSurDiv'+parent.document.getElementById('identifiantRetour').value).innerHTML+='".str_replace(array("'", "\""), array("\\'", "    &#34;"), $nomAdresse)."<a style=\'cursor:pointer;\' onclick=\' retirerVueSur(&#34;".$fetch['idAdresse']."_".$fetch['idEvenementGA']."&#34;, &#34;'+parent.document.getElementById('identifiantRetour').value+'&#34;); \'>(-)</a><br>';
                        
                        parent.document.getElementById('vueSur'+parent.document.getElementById('identifiantRetour').value).innerHTML+='<option value=\'".$fetch['idAdresse']."_".$fetch['idEvenementGA']."\' SELECTED>".str_replace(array("'", "\""), array("\'", "&#34;"), $nomAdresse)."</option>';";
                        
                        /*
                        
                        parent.document.getElementById('listeVueSurDiv'+parent.document.getElementById('identifiantRetour').value).innerHTML+='".str_replace("'", "\\'", $nomAdresse)."<a  style=\'cursor:pointer\' onclick=\\\'retirerVueSur(\\\\\'".$fetch['idAdresse']."_".$fetch['idEvenementGA']."\\\\\', '+parent.document.getElementById('identifiantRetour').value+');\\\'>(-)</a><br>';parent.document.getElementById('vueSur'+parent.document.getElementById('identifiantRetour').value).innerHTML+='<option value=\\\'".$fetch["idAdresse"]."_".$fetch['idEvenementGA']."\\\' SELECTED>".str_replace("'", "\'", $nomAdresse)."</option>';
                        
                        
                        */
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                        break;
                    case "popupAjoutAdressesLieesSurEvenement":
                    
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById('listeGroupesAdressesLiees').innerHTML+='".str_replace("'", "\\'", $nomAdresse)."<a  style=\'cursor:pointer\' onclick=\'retirerGroupeAdresse(".$fetch['idEvenementGA'].");\'>(-)</a><br>';parent.document.getElementById('listeIdGroupesAdressesLiees').innerHTML+='<option value=\'".$fetch["idEvenementGA"]."\' SELECTED>".str_replace("'", "\'", $nomAdresse)."</option>';";
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                    
                        break;
                    case "popupDeplacerEvenementVersGroupeAdresse":
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "if (confirm('Etes vous sûr de vouloir deplacer cet évènement ?')){parent.location.href='".$this->creerUrl('deplacerEvenementVersGA', 'evenement', array('idEvenementADeplacer'=>$this->variablesGet['idEvenementADeplacer'], 'deplacerVersIdGroupeAdresse'=>$fetch['idEvenementGA'], 'idEvenement'=>$this->variablesGet['idEvenementADeplacer']))."';}";
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                        break;
                    case 'popupRechercheAdresseAdminParcours':
                        $urlDetailHref    = "#";
                        $urlDetailOnClick = "parent.document.getElementById('libelleEvenementGroupeAdresse').value='".str_replace("'", "\\'", $nomAdresseNoStyle)."';parent.document.getElementById('idEvenementGroupeAdresse').value='".$fetch['idEvenementGA']."';parent.document.getElementById('divpopupChoixAdresses').style.display='none';";
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                        break;
                    default:
                        $criteresFiltres = array();
                        /*foreach ($criteres as $critereName => $critereValue) {
                            if (!in_array($critereName, array('sqlSelectionExterne', 'titre'))) {
                                $criteresFiltres[$critereName] = $critereValue;
                            }
                        }*/
                    

                        $urlDetailHref    = $this->creerUrl('', '', array_merge($criteresFiltres, array('archiIdAdresse'=>$fetch['idAdresse'], 'archiAffichage'=>'adresseDetail', 'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGA'], 'debut'=>'')));
                        $urlDetailOnClick = '';
                        $urlNomRue        = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'rue',  'id'=>$fetch['idRue'],  'debut'=>0)));
                        $urlNomRueOnClick = '';
                        $urlNomSousQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'sousQuartier',  'id'=>$fetch['idSousQuartier'],  'debut'=>0)));
                        $urlNomSousQuartierOnClick = '';
                        $urlNomQuartier = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'quartier',  'id'=>$fetch['idQuartier'],  'debut'=>0)));
                        $urlNomQuartierOnClick = '';
                        $urlNomVille = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'ville',  'id'=>$fetch['idVille'],  'debut'=>0)));
                        $urlNomVilleOnClick = '';
                        $urlNomPays = $this->creerUrl('',  '',  array_merge($this->variablesGet,  array('selection'=>'pays',  'id'=>$fetch['idPays'],  'debut'=>0)));
                        $urlNomPaysOnClick = '';
                        
                        
                        // patch laurent pour gerer l'affichage de la liste des dependances d'une source dans l'admin
                        if (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource' && isset($this->variablesGet['source']) && $this->variablesGet['source']!='' && isset($this->variablesGet['modeAdmin']) && $this->variablesGet['modeAdmin']=='1') {
                            $urlDetailOnClick = "parent.document.location.href='".$urlDetailHref."'";
                            $urlDetailHref = "#";
                        }
                        
                        
                        break;
                    }
                    
                    
                    // recuperation d'une image d'illustration pour l'affichage de l'adresse
                    // on s'etonnera peut etre de voir en parametre un groupe d'adresse ET un idAdresse ,  voir l'historique
                    $illustration = $this->getUrlImageFromAdresse($fetch['idAdresse'], 'mini', array('idEvenementGroupeAdresse'=>$fetch['idEvenementGA']));


                    
                    // si on est en mode d'affichage du detail d'une adresse,  on affiche le titre de l'evenement avant la liste des adresses concernées par le groupe d'adresse
                    $titreAdresse = "";
                    $styleAdresse = "";
                    if ($modeAffichage=='listeDesAdressesDuGroupeAdressesSurDetailAdresse') {
                        $idAdresse=0;
                        
                        if (isset($criteres['archiIdEvenement'])) {
                            $reqAdresse = $this->getIdAdressesFromIdEvenement(array('idEvenement'=>$criteres['archiIdEvenement']));
                            $resAdresse = $this->connexionBdd->requete($reqAdresse);
                            $fetchAdresse = mysql_fetch_assoc($resAdresse);
                            $idAdresse = $fetchAdresse['idAdresse'];
                            
                            
                            $e = new archiEvenement();

                            $idEvenementTitreAdresses = $e->getIdEvenementTitre(array("idEvenementGroupeAdresse"=>$criteres['archiIdEvenement']));
                            $reqTitreAdresse = "
                                SELECT he1.titre as titre
                                FROM historiqueEvenement he2,  historiqueEvenement he1
                                WHERE he2.idEvenement = he1.idEvenement
                                AND he1.idEvenement='".$idEvenementTitreAdresses."'
                                GROUP BY he1.idEvenement,  he1.idHistoriqueEvenement
                                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                            ";
                            $resTitreAdresse = $this->connexionBdd->requete($reqTitreAdresse);
                            $fetchTitreAdresse = mysql_fetch_assoc($resTitreAdresse);
                            $titreAdresse = stripslashes($fetchTitreAdresse['titre']);
                            $t->assign_vars(array('titreAdresses'=>$titreAdresse."<br>"));
                            if ($idEvenementTitreAdresses!=0 && $titreAdresse!='')
                                $styleAdresse = "font-size:13px;";
                        } elseif (isset($this->variablesGet['archiIdEvenement'])) {
                            $reqAdresse = $this->getIdAdressesFromIdEvenement(array('idEvenement'=>$this->variablesGet['archiIdEvenement']));
                            $resAdresse = $this->connexionBdd->requete($reqAdresse);
                            $fetchAdresse = mysql_fetch_assoc($resAdresse);
                            $idAdresse = $fetchAdresse['idAdresse'];
                            
                            
                            $e = new archiEvenement();

                            $idEvenementTitreAdresses = $e->getIdEvenementTitre(array("idEvenementGroupeAdresse"=>$this->variablesGet['archiIdEvenement']));
                            $reqTitreAdresse = "
                                SELECT he1.titre as titre
                                FROM historiqueEvenement he2,  historiqueEvenement he1
                                WHERE he2.idEvenement = he1.idEvenement
                                AND he1.idEvenement='".$idEvenementTitreAdresses."'
                                GROUP BY he1.idEvenement,  he1.idHistoriqueEvenement
                                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                            ";
                            $resTitreAdresse = $this->connexionBdd->requete($reqTitreAdresse);
                            $fetchTitreAdresse = mysql_fetch_assoc($resTitreAdresse);
                            $titreAdresse = stripslashes($fetchTitreAdresse['titre']);
                            $t->assign_vars(array('titreAdresses'=>$titreAdresse."<br>"));
                            if ($idEvenementTitreAdresses!=0 && $titreAdresse!='')
                                $styleAdresse = "font-size:13px;";
                        } elseif (isset($this->variablesGet['archiIdAdresse'])) {
                            $idAdresse = $this->variablesGet['archiIdAdresse'];
                            
                            $e = new archiEvenement();
                            $resGroupeAdresses = $this->getIdEvenementGroupeAdresseFromAdresse($idAdresse);
                            $fetchGroupeAdresses = mysql_fetch_assoc($resGroupeAdresses);
                            $idEvenementTitreAdresses = $e->getIdEvenementTitre(array("idEvenementGroupeAdresse"=>$fetchGroupeAdresses['idEvenement']));
                            $reqTitreAdresse = "
                                SELECT he1.titre as titre
                                FROM historiqueEvenement he2,  historiqueEvenement he1
                                WHERE he2.idEvenement = he1.idEvenement
                                AND he1.idEvenement='".$idEvenementTitreAdresses."'
                                GROUP BY he1.idEvenement,  he1.idHistoriqueEvenement
                                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                            ";
                            $resTitreAdresse = $this->connexionBdd->requete($reqTitreAdresse);
                            $fetchTitreAdresse = mysql_fetch_assoc($resTitreAdresse);
                            $titreAdresse = stripslashes($fetchTitreAdresse['titre']);
                            $t->assign_vars(array('titreAdresses'=>$titreAdresse."<br>"));
                            if ($idEvenementTitreAdresses!=0 && $titreAdresse!='')
                                $styleAdresse = "font-size:13px;";
                        }
                        
                    

                    }
                    
                    
                    
                    // a la suite de titresEvenements je rajoutes les images qui concerne la source courante quand on est en mode listeAdressesFromSource
                    if (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeAdressesFromSource' && isset($this->variablesGet['source']) && $this->variablesGet['source']!='') {    
                        $reqImagesSource = "
                            SELECT hi1.idHistoriqueImage as idHistoriqueImage,  hi1.dateUpload as dateUpload, hi1.idImage as idImage,  ee.idEvenementAssocie as idEvenementAssocie
                            FROM historiqueImage hi2,  historiqueImage hi1
                            LEFT JOIN _evenementEvenement ee ON ee.idEvenement = '".$fetch['idEvenementGA']."'
                            LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
                            WHERE hi2.idImage = hi1.idImage
                            AND hi1.idSource = '".$this->variablesGet['source']."'
                            AND hi1.idImage = ei.idImage
                            GROUP BY hi1.idImage,  hi1.idHistoriqueImage
                            HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                        ";
                        $resImagesSource = $this->connexionBdd->requete($reqImagesSource);
                        if (mysql_num_rows($resImagesSource)>0) {
                            $idAdresseSource = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetch['idEvenementGA']);
                            if ($titresEvenements!="")
                                $titresEvenements.="<br>";
                            while ($fetchImagesSource = mysql_fetch_assoc($resImagesSource)) {
                                $lienDetailImageSource = $this->creerUrl('', 'imageDetail', array('archiRetourAffichage'=>'evenement', 'archiRetourIdName'=>'idEvenement', 'archiIdImage'=>$fetchImagesSource['idImage'], 'archiIdAdresse'=>$idAdresseSource, 'archiRetourIdValue'=>$fetchImagesSource['idEvenementAssocie']));
                                // ancien code avec l'extension '.jpg' qui buggait (pas d'affichage de miniature)
                                //$titresEvenements.="<div style='float:left;padding-right:5px;'><a href='".$lienDetailImageSource."'><img src='".$this->getUrlImage("mini").$fetchImagesSource['dateUpload'].'/'.$fetchImagesSource['idHistoriqueImage']."'.jpg'' border=0></a></div>";
                                // debug fabien du 20/12/2011
                                $titresEvenements.="<div style='float:left;padding-right:5px;'><a href='".$lienDetailImageSource."'><img src='".$this->getUrlImage("mini").$fetchImagesSource['dateUpload'].'/'.$fetchImagesSource['idHistoriqueImage'].".jpg'" . " border=0></a></div>";
                            }
                            $titresEvenements.="<div style='clear:both;'></div>";
                        }
                        
                        
                    }
                    
                    
                    
                    
                    $t->assign_block_vars(
                        't.adresses',
                        array(
                            'styleAdresses' => $styleAdresse, 
                            'nom'        => $nomAdresse, 
                            'titresEvenements'        => $titresEvenements, 
                            'nomRue'    => $fetch['nomRue'], 
                            'nomSousQuartier'    => $fetch['nomSousQuartier'], 
                            'nomQuartier'        => $fetch['nomQuartier'], 
                            'nomVille'        => $fetch['nomVille'], 
                            'nomPays'        => $fetch['nomPays'], 
                            'urlNomRue'        => $urlNomRue, 
                            'urlNomRueOnClick'    => $urlNomRueOnClick, 
                            'urlNomSousQuartier'    => $urlNomSousQuartier, 
                            'urlNomSousQuartierOnClick' => $urlNomSousQuartierOnClick, 
                            'urlNomQuartier'    => $urlNomQuartier, 
                            'urlNomQuartierOnClick'    => $urlNomQuartierOnClick, 
                            'urlNomVille'        => $urlNomVille, 
                            'urlNomVilleOnClick'    => $urlNomVilleOnClick, 
                            'urlNomPays'        => $urlNomPays, 
                            'urlNomPaysOnClick'    => $urlNomPaysOnClick, 
                            'urlDetailHref'        => $urlDetailHref, 
                            'urlDetailOnClick'     => $urlDetailOnClick, 
                            'urlImageIllustration'    => 'getPhotoSquare.php?id='.$illustration['idHistoriqueImage'], 
                            'alt'=>''
                            //'alt'=>str_replace("'", " ", $nomAdresseNoStyle)
                        )
                    );
                    
                    
                    
                    $arrayRetour[] = "<a href='".$this->creerUrl('', '', array('archiIdAdresse'=>$fetch['idAdresse'], 'archiAffichage'=>'adresseDetail', 'debut'=>''))."'>".$nomAdresse."</a>";
                    $arrayIdAdressesRetour[] = $fetch['idAdresse'];
                    $arrayIdEvenementsGARetour[] = $fetch['idEvenementGA'];
                    // **********************************************************************************************************************************************
                    // lien vers la liste des adresses relatives a l'adresse courante:
                    // **********************************************************************************************************************************************
                    $arrayRetourLiensVoirBatiments['urlAutresBiensRue']=$this->creerUrl('', 'adresseListe', array('recherche_rue'=>$fetch['idRue']));
                    $arrayRetourLiensVoirBatiments['urlAutresBiensQuartier']=$this->creerUrl('', 'adresseListe', array('recherche_quartier'=>$fetch['idQuartier']));
                    // **********************************************************************************************************************************************
                    // recherche des images liées a l'adresse et uniquement les images de la table de liaison _adresseImage
                    // **********************************************************************************************************************************************
                    $reqImageLiees = "    SELECT hi.idImage as idImage, hi.idHistoriqueImage as idHistoriqueImage, hi.dateUpload as dateUpload
                                        FROM historiqueImage hi2 ,  historiqueImage hi
                                        RIGHT JOIN _adresseImage ai ON hi.idImage = ai.idImage
                                        WHERE hi2.idImage = hi.idImage
                                        AND ai.idAdresse = '".$fetch['idAdresse']."'
                                        GROUP BY hi.idImage, hi.idHistoriqueImage
                                        HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
                    ";
                    
                    $resImagesLiees = $this->connexionBdd->requete($reqImageLiees);
                    if (count($resImagesLiees)>0) {
                        $t->assign_block_vars('t.adresses.isImagesLiees', array());
                    }    
                    while ($fetchImagesLiees = mysql_fetch_assoc($resImagesLiees)) {
                        // on affiche l'image liee a l'adresse seulement si elle est différente de l'image d'illustration
                        //if ($fetchImagesLiees['idHistoriqueImage'] != $illustration['idHistoriqueImage'])
                        //{
                            $t->assign_block_vars('t.adresses.isImagesLiees.images', array('url'=>$this->getUrlImage("mini").$fetchImagesLiees['dateUpload'].'/'.$fetchImagesLiees['idHistoriqueImage'].'.jpg'));
                        //}
                    }
                    
                    
                    
                    
                    
                    // **********************************************************************************************************************************************
                    $idDerniereAdresse = $fetch['idAdresse'];
                    
                }
                
                // si on affiche a partir du template : listeAdressesDetailEvenement.tpl c'est qu'on est sur le detail d'un evenement
                /*if ($criteres["useTemplateFile"]=="listeAdressesDetailEvenement.tpl") {
                    $coordonnees = $this->getCoordonneesFrom($idDerniereAdresse, 'idAdresse');
                    if ($coordonnees['latitude']!='0' && $coordonnees['longitude']!='0') {
                        // affichage de la googleMap
                        $gm = new googleMap(array(
                                    'googleMapKey'=>$this->googleMapKey, 
                                    'zoom'=>13, 
                                    'height'=>200, 
                                    'width'=>200, 
                                    'noDisplayZoomSelectionSquare'=>true, 
                                    'noDisplayMapTypeButtons'=>true, 
                                    'noDisplayEchelle'=>true, 
                                    'noDisplayZoomSlider'=>true
                        ));
                        $pointMarkerHTML = "
                        <script  >
                            point = new GLatLng(".$coordonnees['latitude'].", ".$coordonnees['longitude'].");
                            marker = new GMarker(point);
                            map.addOverlay(marker);
                        </script>";
                        
                        $t->assign_block_vars("carteGoogleMap", array("html"=>$gm->getJsFunctions().$gm->getHTML().$pointMarkerHTML));
                    }
                }*/
            }
        }
    }
        ob_start();
        $t->pparse('listeAdresses');
        $html=ob_get_contents();
        ob_end_clean();

        
        return array('html'=>$html, 'nbAdresses'=>$nbAdresses, 'arrayLiens'=>$arrayRetour, 'arrayIdAdresses'=>$arrayIdAdressesRetour, 'arrayIdEvenementsGroupeAdresse'=>$arrayIdEvenementsGARetour, 'arrayRetourLiensVoirBatiments'=>$arrayRetourLiensVoirBatiments);
    }
    
    // **********************************************************************************************************************************************************************
    // affichage de la liste alphabetique en fonction du resultat de la requete (on affiche les lettres ou il y a un resultat dans la requete)
    // **********************************************************************************************************************************************************************
    public function afficheListeAlphabetique($criteres=array(),$modeAffichage='')
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeAlphabetique'=>'listeAlphabetique.tpl')));
        
        $join="";
        if(isset($criteres['join']))
            $join = $criteres['join'];
        
        $where="";
        if(isset($criteres['where']))
        {
            $where = 'WHERE 1=1 '.$criteres['where'];
        }
        
        $req = "select distinct lower(substr(".$criteres['champ'].",1,1)) as lettre from ".$criteres['table'].' '.$join.' '.$where.' order by lettre asc';        
        
        
        $res = $this->connexionBdd->requete($req);
        
        $listeLettres=array();
        while($fetch = mysql_fetch_assoc($res))
        {
            $listeLettres[]=$fetch['lettre'];
        }
        $listeLettres = array_unique($listeLettres);
        
        foreach($listeLettres as $indice => $lettre)
        {
            switch($modeAffichage)
            {
                case "modifImage":              
                case 'nouveauDossier':
                case 'popupVille':
                case 'modifUtilisateur':
                    $t->assign_block_vars('lettres',array(
                                                            'lettre'=>$lettre,
                                                            'url'=>$this->creerUrl('','afficheChoixVille',array('noHeaderNoFooter'=>1,'archiLettre'=>$lettre,'modeAffichage'=>$modeAffichage)),
                                                            'onclick'=>''
                                                        ));
                break;
                case 'popupRue':
                
                        $t->assign_block_vars('lettres',array(
                                                            'lettre'=>$lettre,
                                                            'url'=>"#",
                                                            "onclick"=>"location.href='".$this->creerUrl('','afficheChoixRue',array('noHeaderNoFooter'=>1,'archiLettre'=>$lettre))."&archiIdVille='+document.getElementById('ville').value+'&archiIdQuartier='+document.getElementById('quartier').value+'&archiIdSousQuartier='+document.getElementById('sousQuartier').value;"
                                                        ));
                break;
                
                default:
                    echo "Erreur : archiAdresse::afficheListeAlphabetique => precisez un mode d'affichage<br>";
                break;
            }
        }
        ob_start();
        $t->pparse('listeAlphabetique');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // **********************************************************************************************************************************************************************
    // affiche le champ select avec la liste des quartiers en fonction d'un parametre get archiIdVille
    // possibilité de passer un identifiant unique dans les parametres GET
    // ou par parametres de fonctions $params['identifiantUnique'] , .... idem pour idVille, idQuartier
    // **********************************************************************************************************************************************************************
    public function afficheSelectQuartier($params=array())
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeQuartiers'=>'listeQuartiers.tpl')));
        
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        
        $sqlWhereVillesModerees = "";
        if($a->getIdProfil()==3) // l'utilisateur est un moderateur , on affiche que les quartiers qu'il peut moderer suivant les villes qu'il peut moderer
        {
            $arrayIdVilles = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            
            if(count($arrayIdVilles)>0)
            {
                $sqlWhereVillesModerees = " AND idVille IN (".implode(",",$arrayIdVilles).") ";
            }
            else
            {
                $sqlWhereVillesModerees = " AND idVille =0 "; // si le moderateur n'a pas de ville a moderer on affiche aucun quartier (sinon la requete les afficheraient tous)
            }
        }
        

        $fromVille="";
        if(isset($params['idVille']) && $params['idVille']!='')
        {
            $fromVille = " AND idVille='".$params['idVille']."' ";
        }
        elseif(isset($this->variablesGet['archiIdVille']) && $this->variablesGet['archiIdVille']!='')
        {
            $fromVille = " AND idVille='".$this->variablesGet['archiIdVille']."' ";
        }
        elseif(isset($this->variablesGet['idVille']) && $this->variablesGet['idVille']!='')
        {
            $fromVille = " AND idVille='".$this->variablesGet['idVille']."' ";
        }
    
        $resQuartiers=$this->connexionBdd->requete("select idQuartier,nom from quartier where 1=1 and nom<>'autre' ".$sqlWhereVillesModerees." ".$fromVille." order by nom");
        while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
        {
                $selected="";
                if(isset($params['idQuartier']) && $params['idQuartier']!='' && $params['idQuartier'] == $fetchQuartiers['idQuartier'])
                {
                    $selected=" selected ";
                }
                
                $t->assign_block_vars("quartiers",array(
                                                        'nom'=>$fetchQuartiers['nom'],
                                                        'id'=>$fetchQuartiers['idQuartier'],
                                                        'selected'=>$selected
                                                ));
        }
        
        
        
        // on peut desactiver les mise a jour des elements
        $javascriptSousQuartier = "";
        if(!isset($params['noSousQuartier']) || $params['noSousQuartier']!=true)
        {
            $javascriptSousQuartier ="
                                document.getElementById('sousQuartiers').innerHTML='<option value=0>Aucun</option>';
                    document.getElementById('sousQuartiers').selectedIndex=0;
                    appelAjax('".$this->creerUrl('','afficheSelectSousQuartier',array('noHeaderNoFooter'=>1))."&archiIdQuartier='+document.getElementById('quartiers').value,'champSousQuartier')
            ";
        }
        
        $t->assign_vars(array('javascript'=>"
                function onChangeListeQuartier()
                {
                    ".$javascriptSousQuartier."
                }
        "));
        
        $t->assign_vars(array('onChangeListeQuartier'=>"onChangeListeQuartier();"));
            

        
        ob_start();
        $t->pparse('listeQuartiers');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    // **********************************************************************************************************************************************************************
    // affiche le champ select avec la liste des sous quartiers en fonction d'un parametre get archiIdQuartier
    // possibilité de passer un identifiant unique dans les parametres GET
    // ou par parametre de fonction : identifiantUnique, idQuartier, idSousQuartier selectionne
    // **********************************************************************************************************************************************************************
    public function afficheSelectSousQuartier($params=array())
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeSousQuartiers'=>'listeSousQuartiers.tpl')));
        
        
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $sqlWhereVillesModerees = "";
        if($a->getIdProfil()==3) // l'utilisateur est un moderateur , on affiche que les quartiers qu'il peut moderer suivant les villes qu'il peut moderer
        {
            $arrayIdVilles = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            
            if(count($arrayIdVilles)>0)
            {
                $sqlWhereVillesModerees = " AND idQuartier IN (SELECT idQuartier FROM quartier WHERE idVille in (".implode(",",$arrayIdVilles).")) ";
            }
            else
            {
                $sqlWhereVillesModerees = " AND idQuartier =0 "; // si le moderateur n'a pas de ville a moderer on affiche aucun quartier (sinon la requete les afficheraient tous)
            }
        }
        
        $identifiantUnique="";
        
        if(isset($params['identifiantUnique']))
        {
            $identifiantUnique = $params['identifiantUnique'];
        }
        elseif(isset($this->variablesGet['identifiantUnique']))
        {
            $identifiantUnique=$this->variablesGet['identifiantUnique'];
        }
        
        $t->assign_vars(array('identifiantUnique'=>$identifiantUnique));
        
        if((isset($this->variablesGet['archiIdQuartier']) && $this->variablesGet['archiIdQuartier']!='') || (isset($params['idQuartier']) && $params['idQuartier']!=''))
        {
            $fromQuartier="";
            if(isset($params['idQuartier']) && $params['idQuartier']!='')
            {
                $whereQuartier = " AND idQuartier='".$params['idQuartier']."' ";
            }
            elseif(isset($this->variablesGet['archiIdQuartier']) && $this->variablesGet['archiIdQuartier']!='')
            {
                $whereQuartier = " AND idQuartier='".$this->variablesGet['archiIdQuartier']."' ";
            }
            
            $resQuartiers=$this->connexionBdd->requete("select idSousQuartier,nom from sousQuartier where 1=1 and nom<>'autre' ".$sqlWhereVillesModerees." ".$whereQuartier." order by nom");
            while($fetchQuartiers = mysql_fetch_assoc($resQuartiers))
            {
                // si un sousQuartier doit etre selectionne par defaut
                $selected="";
                if(isset($params['idSousQuartier']) && $params['idSousQuartier']!='' && $params['idSousQuartier']==$fetchQuartiers['idSousQuartier'])
                {
                    $selected = " selected ";
                }
            
                if($fetchQuartiers['nom']!='autre')
                {
                    $t->assign_block_vars("sousQuartiers",array(
                                                            'nom'=>$fetchQuartiers['nom'],
                                                            'id'=>$fetchQuartiers['idSousQuartier'],
                                                            'selected'=>$selected
                                                    ));
                }
            }
        }
        else
        {
            echo "archiAdresses::afficheSelectQuartier => parametre archiIdVille manquant<br>";
        }
        
        ob_start();
        $t->pparse('listeSousQuartiers');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // affichage de la liste des villes sous forme d'un champ select
    public function afficheSelectVille($params=array())
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeVille'=>'listeVilleSelect.tpl')));
        
        
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $sqlWhereVillesModerees = "";
        if($a->getIdProfil()==3) // l'utilisateur est un moderateur , on affiche que les quartiers qu'il peut moderer suivant les villes qu'il peut moderer
        {
            $arrayIdVilles = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            
            if(count($arrayIdVilles)>0)
            {
                $sqlWhereVillesModerees = " AND idVille IN (".implode(",",$arrayIdVilles).") ";
            }
            else
            {
                $sqlWhereVillesModerees = " AND idVille =0 "; // si le moderateur n'a pas de ville a moderer on affiche aucun (sinon la requete les afficheraient tous)
            }
        }
        
        
        
        $wherePays = "";
        if(isset($params['idPays']) && $params['idPays']!='')
        {
            $wherePays = " AND idPays = '".$params['idPays']."' ";
        }
        
        if(isset($this->variablesGet["idPays"]) && $this->variablesGet["idPays"]!='')
        {
            $wherePays=" AND idPays = '".$this->variablesGet['idPays']."' ";
        }
        
        
        $javascriptQuartier = "";
        if(!isset($params['noQuartier']) || $params['noQuartier']!=true)
        {
            $javascriptQuartier = "
                    document.getElementById('quartiers').innerHTML='<option value=0>Aucun</option>';
                    document.getElementById('quartiers').selectedIndex=0;
                    appelAjax('?archiAffichage=afficheSelectQuartier&noHeaderNoFooter=1&idVille='+document.getElementById('ville').value,'champQuartier');
                ";
        }
        
        $javascriptSousQuartier = "";
        if(!isset($params['noSousQuartier']) || $params['noSousQuartier']!=true)
        {
            $javascriptSousQuartier = "
                document.getElementById('sousQuartiers').innerHTML='<option value=0>Aucun</option>';
                document.getElementById('sousQuartiers').selectedIndex=0;
            ";
        }
        
        $t->assign_vars(array('javascript'=>"
            function onChangeListeVille()
            {
                ".$javascriptSousQuartier."
                ".$javascriptQuartier."
            }
            "));
        
        
        // assignation du javascript pour l'ajax
        $t->assign_vars(array('onChangeListeVille'=>"onChangeListeVille();"));
        
        
        $reqVille = "SELECT idVille, nom FROM ville WHERE 1=1 and nom<>'autre' ".$sqlWhereVillesModerees." ".$wherePays;
        
        $resVille = $this->connexionBdd->requete($reqVille);
        
        while($fetchVille = mysql_fetch_assoc($resVille))
        {
            $selected="";
            if(isset($params['idVille']) && $params['idVille']!='' && $params['idVille'] == $fetchVille['idVille'])
            {
                $selected=" selected ";
            }
            
            $t->assign_block_vars('villes',array(
                                                    'id'    =>$fetchVille['idVille'],
                                                    'nom'   =>$fetchVille['nom'],
                                                    'selected'=>$selected
                                                ));
        }
        
        ob_start();
        $t->pparse('listeVille');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    // affichage de la liste des pays sous forme d'un champ select
    public function afficheSelectPays($params=array())
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listePays'=>'listePaysSelect.tpl')));
        
        
        
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $sqlWhereVillesModerees = "";
        if($a->getIdProfil()==3) // l'utilisateur est un moderateur , on affiche que les quartiers qu'il peut moderer suivant les villes qu'il peut moderer
        {
            $arrayIdVilles = $u->getArrayVillesModereesPar($a->getIdUtilisateur());
            $arrayIdPays = array();
            // recuperation des pays concernés par les villes qu'administre le moderateur
            $reqPaysModeres = "SELECT distinct idPays FROM ville WHERE idVille IN (".implode(",",$arrayIdVilles).") ";
            $resPaysModeres = $this->connexionBdd->requete($reqPaysModeres);
            if(mysql_num_rows($resPaysModeres)>0)
            {
                while($fetchPaysModeres = mysql_fetch_assoc($resPaysModeres))
                {
                    $arrayIdPays[] = $fetchPaysModeres['idPays'];
                }
                
                $sqlWhereVillesModerees = " AND idPays IN (".implode(",",$arrayIdPays).") ";
            }
            else
            {
                $sqlWhereVillesModerees = " AND idPays = 0 "; // si le moderateur ne gere aucune ville , on rajoute ce critere pour ne pas afficher toutes les villes
            }
            
        }       
        
        
        
        $reqPays = "SELECT nom, idPays FROM pays WHERE 1=1 ".$sqlWhereVillesModerees;
        $resPays = $this->connexionBdd->requete($reqPays);
        
        $javascriptVille="";
        if(!isset($params['noVille']) || $params['noVille']!=true)
        {
            $javascriptVille = "                            
                            document.getElementById('ville').innerHTML='<option value=0>Aucun</option>';
                            document.getElementById('ville').selectedIndex=0;
                            appelAjax('?archiAffichage=afficheSelectVille&noHeaderNoFooter=1&idPays='+document.getElementById('pays').value,'champVille');
                            ";
        }
        
        $javascriptQuartier="";
        if(!isset($params['noQuartier']) || $params['noQuartier']!=true)
        {
            $javascriptQuartier="
                            document.getElementById('quartiers').innerHTML='<option value=0>Aucun</option>';
                            document.getElementById('quartiers').selectedIndex=0;
            ";
        }
        
        $javascriptSousQuartier="";
        if(!isset($params['noSousQuartier']) || $params['noSousQuartier']!=true)
        {
            $javascriptSousQuartier="
                            document.getElementById('sousQuartiers').innerHTML='<option value=0>Aucun</option>';
                            document.getElementById('sousQuartiers').selectedIndex=0;
            ";
        }
        
        
        $t->assign_vars(array('javascript'=>"
                        function onChangeListePays()
                        {
                            ".$javascriptSousQuartier."
                            ".$javascriptQuartier."
                            ".$javascriptVille."
                        }
                        "));
        
        
        // assignation du javascript pour l'ajax
        $t->assign_vars(array('onChangeListePays'=>"onChangeListePays();"));
        
        while($fetchPays = mysql_fetch_assoc($resPays))
        {
            $selected="";
            if(isset($params['idPays']) && $params['idPays']!='' && $params['idPays'] == $fetchPays['idPays'])
            {
                $selected=" selected ";
            }
            $t->assign_block_vars('pays',array(
                                                    'id'=>$fetchPays['idPays'],
                                                    'nom'=>$fetchPays['nom'],
                                                    'selected'=>$selected
                                                ));
        }
        
        ob_start();
        $t->pparse('listePays');
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    
    public function afficheSelectTypeEvenement()
    {
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeTypeEvenement'=>'listeTypeEvenement.tpl')));
        
        
        if(isset($this->variablesGet['archiTypeGroupeEvenement']) && $this->variablesGet['archiTypeGroupeEvenement']!='')
        {
            $res = $this->connexionBdd->requete("select idTypeEvenement, nom from typeEvenement where groupe = '".$this->variablesGet['archiTypeGroupeEvenement']."' order by position ASC");
            while($fetch = mysql_fetch_assoc($res))
            {
                $t->assign_block_vars('typesEvenement',array(
                                                                'id'=>$fetch['idTypeEvenement'],
                                                                'nom'=>$fetch['nom']
                                                            ));
            
            }
        }
        
        
        
        
        ob_start();
        $t->pparse('listeTypeEvenement');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }
    
    
    
    
    // **********************************************************************************************************************************************************************
    // code du contenu de la popup choix rue 
    // **********************************************************************************************************************************************************************
    public function afficheChoixRue($lettre='a')
    {
        if(isset($this->variablesGet['archiLettre']) && $this->variablesGet['archiLettre']!='')
        {
            $lettre = $this->variablesGet['archiLettre'];
        }
        
        $modeAffichage="";
        if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']!='')
            $modeAffichage=$this->variablesGet['modeAffichage'];
        
        $html="";
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('listeRues'=>'listeRues.tpl')));
        
        
        // recuperation des parametres
        $idPays         = 1;
        $idVille        = 0;
        $idQuartier     = 0;
        $idSousQuartier = 0;
        
        $sqlLeftJoin="";
        $sqlSelection="";
        // mise en place des parametres de requete sql pour recherche des rue d'une ville
        if(isset($this->variablesGet['archiIdVille']) && $this->variablesGet['archiIdVille']!='0' && $this->variablesGet['archiIdVille']!='undefined' && $this->variablesGet['archiIdVille']!='')
        {
            $idVille = $this->variablesGet['archiIdVille'];
            $sqlLeftJoin  = " RIGHT JOIN quartier q ON q.idVille = '".$idVille."' ";
            $sqlLeftJoin .= " RIGHT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier ";
            $sqlSelection = " AND r.idSousQuartier = sq.idSousQuartier ";
            $t->assign_vars(array('idVille'=>$idVille));
        }
        
        // mise en place des parametres de requete sql pour recherche des rue d'un quartier 
        if(isset($this->variablesGet['archiIdQuartier']) && $this->variablesGet['archiIdQuartier']!='0' && $this->variablesGet['archiIdQuartier']!='undefined' && $this->variablesGet['archiIdQuartier']!='')
        {
            $idQuartier = $this->variablesGet['archiIdQuartier'];
            $sqlLeftJoin  = " RIGHT JOIN sousQuartier sq ON sq.idQuartier = '".$idQuartier."' ";
            $sqlSelection = " AND r.idSousQuartier = sq.idSousQuartier ";
            $t->assign_vars(array('idQuartier'=>$idQuartier));
        }
        
        // mise en place des parametres de requete sql pour recherche des rue d'un sous quartier
        if(isset($this->variablesGet['archiIdSousQuartier']) && $this->variablesGet['archiIdSousQuartier']!='0' && $this->variablesGet['archiIdSousQuartier']!='undefined' && $this->variablesGet['archiIdSousQuartier']!='')
        {
            $idSousQuartier = $this->variablesGet['archiIdSousQuartier'];
            $sqlLeftJoin  = "";
            $sqlSelection = " AND r.idSousQuartier = '".$idSousQuartier."'";
            $t->assign_vars(array('idSousQuartier'=>$idSousQuartier));
        }
        
        
        $reqNbRue = "
                    select lower(substr(r.nom,1,1)) as lettre,r.nom
                    from rue r
                    ".$sqlLeftJoin."
                    WHERE 1=1
                    ".$sqlSelection."
                    and lower(substr(r.nom,1,1))='".$lettre."'
                    order by r.nom ASC
                    ";
        
        $resNbRue = $this->connexionBdd->requete($reqNbRue);
        
        $nbEnregistrementTotaux = mysql_num_rows($resNbRue); // nombre d'enregistrements pour la lettre courante
        
        if($nbEnregistrementTotaux ==0)
        {
            // recherche de la premiere lettre ou il y a des resultats
            $reqNbRueNew = "
                    select lower(substr(r.nom,1,1)) as lettre,r.nom
                    from rue r
                    ".$sqlLeftJoin."
                    WHERE 1=1
                    ".$sqlSelection."
                    order by r.nom ASC
                    LIMIT 1
                    ";
            $resNbRueNew = $this->connexionBdd->requete($reqNbRueNew);
            $nbEnregistrementTotaux = mysql_num_rows($resNbRueNew);// nombre d'enregistrements pour la nouvelle lettre courante
            // recuperation de la nouvelle lettre courante
            $fetchNewLettre = mysql_fetch_assoc($resNbRueNew);
            $lettre = $fetchNewLettre['lettre'];
        }
        
        // nombre d'images affichées sur une page
        $nbEnregistrementsParPage = 20;
        $arrayPagination=$this->pagination(array(
                                        'nomParamPageCourante'=>'archiPageCouranteVille',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire'
                                        ));
        
        $reqRue = "
                    select r.idRue as idRue, r.nom as nom, lower(substr(r.nom,1,1)) as lettre, prefixe
                    from rue r
                    ".$sqlLeftJoin."
                    WHERE 1=1
                    ".$sqlSelection."
                    and lower(substr(r.nom,1,1))='".$lettre."'
                    order by r.nom ASC
                    LIMIT ".$arrayPagination['limitSqlDebut'].",".$nbEnregistrementsParPage;
        $resRue = $this->connexionBdd->requete($reqRue);
        
        
        $html.=$this->afficheListeAlphabetique(array('champ'=>'r.nom','table'=>'rue r','join'=>$sqlLeftJoin,'where'=>$sqlSelection),'popupRue');
        $html.=$arrayPagination['html']."<br>";
        
        if($nbEnregistrementTotaux>0) // est qu'il y a des enregistrement pour la rue courante ?
        {
            mysql_data_seek($resRue,0); // on replace le curseur au debut de la liste des enregistrements
        }
        else
        {
            echo "Pas de résultat.";
        }

        $tableauHTML = new tableau();
        $colonneHTML="";
        $i=0;
        // ajout au tableau
        while($fetchRue = mysql_fetch_assoc($resRue))
        {
                $nom = $fetchRue['nom'] ;
                $nomGoogleMap = $nom;
                if($fetchRue['prefixe']!='' && isset($fetchRue['prefixe']))
                {
                    $nomGoogleMap = $fetchRue['prefixe'].' '.$nom;
                    $nom = $nom.' ('.$fetchRue['prefixe'].')';
                }
                
            
                /*$t->assign_block_vars('rues',array(
                                                    'url'=>"#",
                                                    'onclick'=>"parent.document.getElementById(parent.document.getElementById('paramChampAppelantRue').value+'txt').value='".addslashes($nom)."';parent.document.getElementById(parent.document.getElementById('paramChampAppelantRue').value).value='".$fetchRue['idRue']."';parent.document.getElementById('calqueRue').style.display='none';",
                                                    'nom'=>$nom
                ));*/
                
                $colonneHTML .= "<a href=\"#\" onclick=\"parent.document.getElementById(parent.document.getElementById('paramChampAppelantRue').value+'txt').value='".addslashes($nomGoogleMap)."';parent.document.getElementById(parent.document.getElementById('paramChampAppelantRue').value).value='".$fetchRue['idRue']."';parent.document.getElementById('calqueRue').style.display='none';\">".$nom."</a><br>";
                if($i==9)
                {
                    $tableauHTML->addValue($colonneHTML);
                    $colonneHTML="";
                }
                
                $i++;
        }
        
        if($colonneHTML!='')
        {
            $tableauHTML->addValue($colonneHTML);
        }
        
        $t->assign_vars(array('rues'=>$tableauHTML->createHtmlTableFromArray(2,'border=0;width:400px;font-size:12px;vertical-align:top;','',"valign='top' border=0")));
        
        ob_start();
        $t->pparse('listeRues');
        $html.=ob_get_contents();
        ob_end_clean();

        return $html;
    }

    // **********************************************************************************************************************************************************************
    // recupere l'id d'une nouvelle adresse (id suivant le plus haut)
    // **********************************************************************************************************************************************************************
    public function getNewIdAdresse()
    {
        $req="select max(idAdresse) as newId from historiqueAdresse";
        $res = $this->connexionBdd->requete($req);
        $fetchAdresse = mysql_fetch_assoc($res);
        return $fetchAdresse['newId']+1;
    }
    
    // **********************************************************************************************************************************************************************
    // recuperation d'un encart affichant les adresses pour les derniers evenements  NEST PLUS UTILISE !!!!!!!!!!!!!!!! => a supprimer a terme , une fois la page d'accueil sera validee
    // **********************************************************************************************************************************************************************
    public function afficheEncart($modeAffichage='',$arrayIdAdressesNePasAfficher=array())
    {
        $html="";
        $retour = array();
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('encartAccueil'=>'encartAccueil.tpl')));
        ob_start();
        $string = new stringObject();
        
        switch($modeAffichage)
        {
            // ****************************************************************************************************************************************************************************
            // AFFICHAGE DES ENCARTS DES DERNIERES DEMOLITIONS
            // ****************************************************************************************************************************************************************************
            case 'demolition':
                $titre = "Dernières Démolitions";
                $where = " he.idTypeEvenement='6' ";
                $t->assign_vars(array('lienVersTout'=>"<a href='".$this->creerUrl('','toutesLesDemolitions',array())."'>Toutes les démolitions</a>"));
                // cas des trois encart sauf celui des derniereAdresses
                // recherche des derniers evenenements 

                $req="
                                        SELECT distinct ha.idAdresse as idAdresse,ee.idEvenementAssocie as idEvenementAssocie,ha.date,ha.numero as numero,ha.idRue as idRue,
                                        ha.idQuartier as idQuartier, ha.idSousQuartier as idSousQuartier, ha.idVille as idVille,ha.idIndicatif as idIndicatif,he1.idEvenement as idEvenement,he1.dateCreationEvenement as dateCreationEvenement, he1.description as description,
                                        
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue
                                        
                                        FROM historiqueAdresse ha2, historiqueAdresse ha
                                        
                                        RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                        RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                                        RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                                        RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                                        
                                        LEFT JOIN rue r ON r.idRue = ha.idRue
                                        LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                                        LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                                        LEFT JOIN ville v ON v.idVille = ha.idVille
                                        
                                        WHERE ha2.idAdresse = ha.idAdresse
                                        AND he1.idTypeEvenement = '6'
                                        GROUP BY ha.idAdresse, he1.idEvenement , ha.idHistoriqueAdresse, he1.idHistoriqueEvenement
                                        HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                                        ORDER BY he1.dateCreationEvenement DESC,ha.idHistoriqueAdresse DESC
                                        LIMIT 5
                ";
                
                
                $res = $this->connexionBdd->requete($req);
                $i=0;
                $arrayIdAdresses=array();
                while($fetch = mysql_fetch_assoc($res))
                {
                    if($i==0 && $fetch['idAdresse']!='') // premier evenement affiché avec sa photo
                    {
                        // on recupere une photo de l'evenement de la demolition
                        $urlImage = $this->getUrlImageFrom($fetch['idAdresse'],'moyen',"AND he1.idTypeEvenement='6'");
                        if(preg_match('/transparent/i',$urlImage['url'])==true)
                        {
                            $urlImage = $this->getUrlImageFromAdresse($fetch['idAdresse'],'moyen');
                        }
                        
                        if(preg_match('/transparent/i',$urlImage['url'])==false) // recherche du mot 'transparent' dans la chaine, i indique l'insensibilité a la casse
                        {
                            $t->assign_vars(array('photoAdresse1'=>"<img style='border:1px #000000 solid;margin-right:2px;float:left;' align='middle' src='".$urlImage['url']."'>"));
                        }
                        
                        $t->assign_vars(array('descriptionAdresse1'=>"<div><a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a><br>".stripslashes($string->sansBalises($string->coupureTexte($fetch['description'],20)))."</div>"));
                    }
                    elseif(!in_array($fetch['idAdresse'],$arrayIdAdresses) && $fetch['idAdresse']!='')
                    {
                        $t->assign_block_vars('listeAdressesSuivantes',array('lien'=>"<a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a>"));
                    }
                    
                    $arrayIdAdresses[] = $fetch['idAdresse'];

                    $i++;
                }
                
            break;
            // ****************************************************************************************************************************************************************************
            // AFFICHAGE DES ENCARTS DES DERNIERES ADRESSES AJOUTEES
            // ****************************************************************************************************************************************************************************
            case 'derniereAdresses':
                // on cherche les derniers groupes d'adresses
                $titre = "Nouvelles adresses";
                $t->assign_vars(array('lienVersTout'=>"<a href='".$this->creerUrl('','recherche',array('motcle'=>'','submit'=>'Rechercher'))."'>Toutes les adresses</a>"));

                $listeAdressesNePasAfficher="";
                if(isset($arrayIdAdressesNePasAfficher) && count($arrayIdAdressesNePasAfficher)>0)
                {
                    $listeAdressesNePasAfficher = implode("','",$arrayIdAdressesNePasAfficher);
                    $listeAdressesNePasAfficher = "AND ha.idAdresse not in ('".$listeAdressesNePasAfficher."')";
                }

            
                // cas de l'encars des dernieres adresses
                $req="
                                        SELECT distinct ha.idAdresse as idAdresse,ee.idEvenementAssocie as idEvenementAssocie,ha.date,ha.numero as numero,ha.idRue as idRue,
                                        ha.idQuartier as idQuartier, ha.idSousQuartier as idSousQuartier, ha.idVille as idVille,ha.idIndicatif as idIndicatif,he1.idEvenement as idEvenement,he1.dateCreationEvenement as dateCreationEvenement, he1.description as description,
                                        
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue
                                        
                                        FROM historiqueAdresse ha2, historiqueAdresse ha
                                        
                                        RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                        RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                                        RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                                        RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                                        
                                        LEFT JOIN rue r ON r.idRue = ha.idRue
                                        LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                                        LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                                        LEFT JOIN ville v ON v.idVille = ha.idVille
                                        
                                        WHERE ha2.idAdresse = ha.idAdresse
                                        AND YEAR(he1.dateDebut)<>'".date("Y")."'
                                        ".$listeAdressesNePasAfficher."
                                        GROUP BY ha.idAdresse, he1.idEvenement , ha.idHistoriqueAdresse, he1.idHistoriqueEvenement
                                        HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                                        ORDER BY ha.date DESC,ha.idHistoriqueAdresse DESC
                                        LIMIT 5
                ";
                
                $res = $this->connexionBdd->requete($req);
                $i=0;
                $arrayIdAdresses=array();
                while($fetch = mysql_fetch_assoc($res))
                {
                    if($i==0 && $fetch['idAdresse']!='') // premier evenement affiché avec sa photo
                    {
                        // on recupere une photo du premier evenement travaux de l'adresse
                        $urlImage = $this->getUrlImageFrom($fetch['idAdresse'],'moyen',"AND he1.idTypeEvenement not in (1,2,3,4,5,6,11) ");
                        if(preg_match('/transparent/i',$urlImage['url'])==true)
                        {
                            $urlImage = $this->getUrlImageFromAdresse($fetch['idAdresse'],'moyen');
                        }
                        
                        if(preg_match('/transparent/i',$urlImage['url'])==false) // recherche du mot 'transparent' dans la chaine, i indique l'insensibilité a la casse
                        {
                            $t->assign_vars(array('photoAdresse1'=>"<img style='border:1px #000000 solid;margin-right:2px;float:left;' align='middle' src='".$urlImage['url']."'>"));
                        }
                        
                        $t->assign_vars(array('descriptionAdresse1'=>"<div><a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a><br>".stripslashes($string->sansBalises($string->coupureTexte($fetch['description'],20)))."</div>"));
                    }
                    elseif(!in_array($fetch['idAdresse'],$arrayIdAdresses) && $fetch['idAdresse']!='')
                    {
                        $t->assign_block_vars('listeAdressesSuivantes',array('lien'=>"<a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a>"));
                    }
                    
                    $arrayIdAdresses[] = $fetch['idAdresse'];

                    $i++;
                }
            break;
                    
            // ****************************************************************************************************************************************************************************
            // AFFICHAGE DES ENCARTS DES EVENEMENTS TRAVAUX
            // ****************************************************************************************************************************************************************************
            case 'travaux':
                $titre = "Derniers travaux";
                $t->assign_vars(array('lienVersTout'=>"<a href='".$this->creerUrl('','tousLesTravaux',array())."'>Tous les travaux</a>"));
                // recherche des derniers evenenements 
                //he.idTypeEvenement in (1,2,3,4,5) 
                $req="
                                        SELECT distinct ha.idAdresse as idAdresse,ee.idEvenementAssocie as idEvenementAssocie,ha.date,ha.numero as numero,ha.idRue as idRue,
                                        ha.idQuartier as idQuartier, ha.idSousQuartier as idSousQuartier, ha.idVille as idVille,ha.idIndicatif as idIndicatif,he1.idEvenement as idEvenement,he1.dateCreationEvenement as dateCreationEvenement, he1.description as description,
                                        
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue
                                        
                                        FROM historiqueAdresse ha2, historiqueAdresse ha
                                        
                                        RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                        RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                                        RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                                        RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                                        
                                        LEFT JOIN rue r ON r.idRue = ha.idRue
                                        LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                                        LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                                        LEFT JOIN ville v ON v.idVille = ha.idVille
                                        
                                        WHERE ha2.idAdresse = ha.idAdresse
                                        AND (he1.idTypeEvenement in (2,3,4,5) OR (he1.idTypeEvenement='1' AND YEAR(he1.dateDebut)='".date("Y")."'))
                                        
                                        GROUP BY ha.idAdresse, he1.idEvenement , ha.idHistoriqueAdresse, he1.idHistoriqueEvenement
                                        HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                                        ORDER BY he1.dateCreationEvenement DESC,ha.idHistoriqueAdresse DESC
                                        LIMIT 5
                ";
                
                $res = $this->connexionBdd->requete($req);
                $i=0;
                $arrayIdAdresses=array();
                while($fetch = mysql_fetch_assoc($res))
                {
                    if($i==0 && $fetch['idAdresse']!='') // premier evenement affiché avec sa photo
                    {
                        // on recupere une photo du premier evenement travaux de l'adresse
                        $urlImage = $this->getUrlImageFrom($fetch['idAdresse'],'moyen',"AND he1.idTypeEvenement in (1,2,3,4,5) ");
                        if(preg_match('/transparent/i',$urlImage['url'])==true)
                        {
                            $urlImage = $this->getUrlImageFromAdresse($fetch['idAdresse'],'moyen');
                        }
                        
                        if(preg_match('/transparent/i',$urlImage['url'])==false) // recherche du mot 'transparent' dans la chaine, i indique l'insensibilité a la casse
                        {
                            $t->assign_vars(array('photoAdresse1'=>"<img style='border:1px #000000 solid;margin-right:2px;float:left;' align='middle' src='".$urlImage['url']."'>"));
                        }
                        
                        $t->assign_vars(array('descriptionAdresse1'=>"<div><a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a><br>".stripslashes($string->sansBalises($string->coupureTexte($fetch['description'],20)))."</div>"));
                    }
                    elseif(!in_array($fetch['idAdresse'],$arrayIdAdresses) && $fetch['idAdresse']!='')
                    {
                        $t->assign_block_vars('listeAdressesSuivantes',array('lien'=>"<a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a>"));
                    }
                    
                    $arrayIdAdresses[] = $fetch['idAdresse'];

                    $i++;
                }

            break;
            // ****************************************************************************************************************************************************************************
            // AFFICHAGE DES ENCARTS DES EVENEMENTS CULTURELS
            // ****************************************************************************************************************************************************************************
            case 'culturel':
                $titre = "Derniers évènements culturels";
                $t->assign_vars(array('lienVersTout'=>"<a href='".$this->creerUrl('','tousLesEvenementsCulturels',array())."'>Tous les évènements culturels</a>"));
                // cas des trois encart sauf celui des derniereAdresses
                // recherche des derniers evenenements 
                $req="
                                        SELECT distinct ha.idAdresse as idAdresse,ee.idEvenementAssocie as idEvenementAssocie,ha.date,ha.numero as numero,ha.idRue as idRue,
                                        ha.idQuartier as idQuartier, ha.idSousQuartier as idSousQuartier, ha.idVille as idVille,ha.idIndicatif as idIndicatif,he1.idEvenement as idEvenement,he1.dateCreationEvenement as dateCreationEvenement, he1.description as description,
                                        
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue
                                        
                                        FROM historiqueAdresse ha2, historiqueAdresse ha
                                        
                                        RIGHT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                                        RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                                        RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                                        RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                                        
                                        LEFT JOIN rue r ON r.idRue = ha.idRue
                                        LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha.idSousQuartier
                                        LEFT JOIN quartier q ON q.idQuartier = ha.idQuartier
                                        LEFT JOIN ville v ON v.idVille = ha.idVille
                                        
                                        WHERE ha2.idAdresse = ha.idAdresse
                                        AND he1.idTypeEvenement not in (1,2,3,4,5,6,11) 
                                        GROUP BY ha.idAdresse, he1.idEvenement , ha.idHistoriqueAdresse, he1.idHistoriqueEvenement
                                        HAVING ha.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
                                        ORDER BY he1.dateCreationEvenement DESC,ha.idHistoriqueAdresse DESC
                                        LIMIT 5
                ";
                
                $res = $this->connexionBdd->requete($req);
                $i=0;
                $arrayIdAdresses=array();
                while($fetch = mysql_fetch_assoc($res))
                {
                    if($i==0 && $fetch['idAdresse']!='') // premier evenement affiché avec sa photo
                    {
                        // on recupere une photo du premier evenement travaux de l'adresse
                        $urlImage = $this->getUrlImageFrom($fetch['idAdresse'],'moyen',"AND he1.idTypeEvenement not in (1,2,3,4,5,6,11) ");
                        if(preg_match('/transparent/i',$urlImage['url'])==true)
                        {
                            $urlImage = $this->getUrlImageFromAdresse($fetch['idAdresse'],'moyen');
                        }
                        
                        if(preg_match('/transparent/i',$urlImage['url'])==false) // recherche du mot 'transparent' dans la chaine, i indique l'insensibilité a la casse
                        {
                            $t->assign_vars(array('photoAdresse1'=>"<img style='border:1px #000000 solid;margin-right:2px;float:left;' align='middle' src='".$urlImage['url']."'>"));
                        }
                        
                        $t->assign_vars(array('descriptionAdresse1'=>"<div><a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a><br>".stripslashes($string->sansBalises($string->coupureTexte($fetch['description'],20)))."</div>"));
                    }
                    elseif(!in_array($fetch['idAdresse'],$arrayIdAdresses) && $fetch['idAdresse']!='')
                    {
                        $t->assign_block_vars('listeAdressesSuivantes',array('lien'=>"<a href='".$this->creerUrl('','adresseDetail',array("archiIdAdresse"=>$fetch['idAdresse']))."' style='font-size:12px;'>".date('d/m/Y',strtotime($fetch['dateCreationEvenement'])).' '.$this->getIntituleAdresse($fetch)."</a>"));
                    }
                    
                    $arrayIdAdresses[] = $fetch['idAdresse'];

                    $i++;
                }

            break;
        }

        
        $t->assign_vars(array('titre'=>$titre));
        
        $t->pparse('encartAccueil');
        $html.=ob_get_contents();
        ob_end_clean();
        $retour =array("html"=>$html , "arrayIdAdresses"=>$arrayIdAdresses);
        return $retour;
    }
    
    //  ************************************************************************************************************************
    // affichage des derniers evenements par categorie , travaux, culturel , dernieresAdresses,demolitions
    // on remplis un tableau au fur et a mesure , le but etant de realiser l'operation en 1 seul requete afin d'eviter les repetitions
    // et en meme temps rend plus rapide le traitement qu'avec 4 requetes distinctes
    //  ************************************************************************************************************************
    public function getDerniersEvenementsParCategorie($nbAdressesParEncart=5,$params=array())
    {
        // ville de Strasbourg par defaut
        $sqlWhere = "AND v.idVille=1";
        if(isset($params['idVille']) && $params['idVille']!='')
        {
            $sqlWhere = "AND v.idVille=".$params['idVille'];
        }
        
        
        $reqEvenements = "
        
            SELECT  he1.idEvenement as idEvenement, he1.dateCreationEvenement as dateCreationEvenement,he1.dateDebut as dateDebut,extract(YEAR FROM he1.dateDebut) as annneeDebut, he1.idTypeEvenement as idTypeEvenement,
                    ha1.idAdresse as idAdresse, ha1.date as dateAdresse, ha1.numero as numero, ha1.idRue as idRue, ha1.idQuartier as idQuartier, 
                    ha1.idSousQuartier as idSousQuartier, ha1.idPays as idPays, ha1.idVille as idVille, ha1.idIndicatif as idIndicatif,
                                        
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue,
                    ae.idEvenement as idEvenementGroupeAdresses
                    
                                        
            FROM historiqueEvenement he2, historiqueEvenement he1
            RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
            RIGHT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
            RIGHT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
            RIGHT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
            
            LEFT JOIN typeEvenement te ON te.idTypeEvenement = he1.idTypeEvenement
            
            LEFT JOIN rue r         ON r.idRue = ha1.idRue
            LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
            LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
            LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
            LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
        
        
        
            WHERE he2.idEvenement = he1.idEvenement
            
            ".$sqlWhere."

            GROUP BY he1.idEvenement,ha1.idAdresse, he1.idHistoriqueEvenement, ha1.idHistoriqueAdresse
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) 
            ORDER BY he1.dateCreationEvenement DESC
        ";//,dateCreationEvenement DESC,dateAdresse DESC
        
        
        /*
                    LEFT JOIN rue r ON r.idRue = ha1.idRue
            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = ha1.idSousQuartier
            LEFT JOIN quartier q ON q.idQuartier = ha1.idQuartier
            LEFT JOIN ville v ON v.idVille = ha1.idVille
        
        */
                
        $resEvenements = $this->connexionBdd->requete($reqEvenements);
                
        $tabAdressesEvenementsAffichees=array(); // tableau contenant les idAdresses qu'il ne faudra pas reafficher 
        $tabEvenementGroupeAdressesAffichees = array(); // on ne reaffiche pas les adresses appartenant au meme groupe d'adresse sinon redondance au niveau de certain titre lors de l'affichage (adresses différentes mais titre identiques)
        
        $tabConstruction=array();
        $tabDemolition=array();
        $tabCulturel=array();
        $tabDernieresAdresses=array();
        
        $tabAdressesNouvellesAdressesAffichees=array();
        
        $isPhotoContruction=false;
        $isPhotoDemolition = false;
        $isPhotoCulturel = false;
        $image = new archiImage();
        while($fetchEvenements = mysql_fetch_assoc($resEvenements))
        {
            //if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees))
            //{
                //$this->getUrlImage("moyen")."/".$fetchEvenements['dateUpload']."/".$fetchEvenements['idHistoriqueImage'].".jpg"
                $positionEvenement = $this->getPositionFromEvenement($fetchEvenements['idEvenement']);
                $infosAdresseCourante = array(
                    "idAdresse"=>$fetchEvenements['idAdresse'],
                    "idIndicatif"=>$fetchEvenements['idIndicatif'],
                    "numero"=>$fetchEvenements['numero'],
                    "nomRue"=>$fetchEvenements['nomRue'],
                    "nomQuartier"=>$fetchEvenements['nomQuartier'],
                    "nomSousQuartier"=>$fetchEvenements['nomSousQuartier'],
                    "nomVille"=>$fetchEvenements['nomVille'],
                    "prefixeRue"=>$fetchEvenements['prefixeRue'],
                    "dateCreationEvenement"=>$fetchEvenements['dateCreationEvenement'],
                    "positionEvenement"=>$positionEvenement,
                    "idEvenement"=>$fetchEvenements['idEvenement'],
                    "idEvenementGroupeAdresse"=>$fetchEvenements['idEvenementGroupeAdresses']
                );
                //"titreEvenement"=>$fetchEvenements['titreEvenement'],
                //"description"=>$fetchEvenements['descriptionEvenement'],
                //"idHistoriqueImage"=>$fetchEvenements['idHistoriqueImage'],
                //"dateUpload"=>$fetchEvenements['dateUpload'],
                                                    
                switch($fetchEvenements['idTypeEvenement'])
                {
                    // TRAVAUX OU NOUVELLE ADRESSE suivant l'annee
                    case '1': // construction
                        if($fetchEvenements['annneeDebut']==date('Y'))
                        {
                            // TRAVAUX
                            if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees) && !in_array($fetchEvenements['idEvenementGroupeAdresses'],$tabEvenementGroupeAdressesAffichees))
                            {
                                //if(count($tabConstruction)<5)
                                //{
                                    $tabConstruction[]=$infosAdresseCourante;

                                    $tabAdressesEvenementsAffichees[] = $fetchEvenements['idAdresse'];
                                    $tabEvenementGroupeAdressesAffichees[] = $fetchEvenements['idEvenementGroupeAdresses'];
                                //}
                                
                                $reqImages = $image->getImagesEvenementsFromAdresse($fetchEvenements['idAdresse'],array('idEvenementGroupeAdresse'=>$fetchEvenements['idEvenementGroupeAdresses']));
                                if(mysql_num_rows($reqImages)>0)
                                {
                                    $isPhotoContruction = true;
                                }
                            }
                        }
                        else
                        {
                            // NOUVELLES ADRESSES
                            /*if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees))
                            {
                                if(count($tabDernieresAdresses)<5)
                                {
                                    $tabDernieresAdresses = $infosAdresseCourante;
                                    $tabAdressesEvenementsAffichees[] = $fetchEvenements['idAdresse'];
                                    $tabAdressesNouvellesAdressesAffichees[] = $fetchEvenements['idAdresse'];
                                }
                            }*/
                        }
                        
                    // TRAVAUX
                    break;
                    case '2': // renovation
                    case '3': // extension
                    case '4': // transformation
                    case '5': // ravalement
                        //if($fetchEvenements['annneeDebut']==date('Y'))
                        //{
                            // TRAVAUX
                            
                            if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees) && !in_array($fetchEvenements['idEvenementGroupeAdresses'],$tabEvenementGroupeAdressesAffichees))
                            {
                                //if(count($tabConstruction)<5)
                                //{
                                    $tabConstruction[]=$infosAdresseCourante;
                                                            
                                    $tabAdressesEvenementsAffichees[] = $fetchEvenements['idAdresse'];
                                    $tabEvenementGroupeAdressesAffichees[] = $fetchEvenements['idEvenementGroupeAdresses'];
                                //}
                                
                                $reqImages = $image->getImagesEvenementsFromAdresse($fetchEvenements['idAdresse'],array('idEvenementGroupeAdresse'=>$fetchEvenements['idEvenementGroupeAdresses']));
                                if(mysql_num_rows($reqImages)>0)
                                {
                                    $isPhotoContruction = true;
                                }
                                
                            }
                        //}
                    break;
                    // DEMOLITIONS
                    case '6': // demolition
                        if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees) && !in_array($fetchEvenements['idEvenementGroupeAdresses'],$tabEvenementGroupeAdressesAffichees))
                        {
                            //if(count($tabDemolition)<5)
                            //{
                                $tabDemolition[]=$infosAdresseCourante;
                                                    
                                $tabAdressesEvenementsAffichees[] = $fetchEvenements['idAdresse'];
                                $tabEvenementGroupeAdressesAffichees[] = $fetchEvenements['idEvenementGroupeAdresses'];
                            //}
                            
                            $reqImages = $image->getImagesEvenementsFromAdresse($fetchEvenements['idAdresse'],array('idEvenementGroupeAdresse'=>$fetchEvenements['idEvenementGroupeAdresses']));
                            if(mysql_num_rows($reqImages)>0)
                            {
                                $isPhotoDemolition = true;
                            }
                        }
                    break;
                    // EVENEMENTS CULTURELS
                    case '14': // visite exceptionnelle
                    case '13': // portes ouvertes
                    case '12': // inauguration
                    case '15': // fête
                    case '16': // Journée du Patrimoine
                    case '18': // Journée du bâtiment
                    case '19': // Exposition
                    case '20': // Information (Nouveautés)
                        if(!in_array($fetchEvenements['idAdresse'],$tabAdressesEvenementsAffichees) && !in_array($fetchEvenements['idEvenementGroupeAdresses'],$tabEvenementGroupeAdressesAffichees))
                        {
                            //if(count($tabCulturel)<5)
                            //{
                                $tabCulturel[]=$infosAdresseCourante;
                                                    
                                $tabAdressesEvenementsAffichees[] = $fetchEvenements['idAdresse'];
                                $tabEvenementGroupeAdressesAffichees[] = $fetchEvenements['idEvenementGroupeAdresses'];
                            //}
                            
                            $reqImages = $image->getImagesEvenementsFromAdresse($fetchEvenements['idAdresse'],array('idEvenementGroupeAdresse'=>$fetchEvenements['idEvenementGroupeAdresses']));
                            if(mysql_num_rows($reqImages)>0)
                            {
                                $isPhotoCulturel = true;
                            }
                            
                        }
                    break;
                    
                }
            //}
            
            if(count($tabConstruction)>=5 && count($tabDemolition)>=5 && count($tabCulturel)>=5 && $isPhotoCulturel && $isPhotoDemolition && $isPhotoContruction)
            {
                break;
            }
        }



        $tabAdressesEvenementsAffichees = array_unique($tabAdressesEvenementsAffichees);
        
        $sqlAdressesExclues="";
        if(count($tabAdressesEvenementsAffichees)>0)
        {
            $sqlAdressesExclues=" AND ha1.idAdresse NOT IN ('".implode("','",$tabAdressesEvenementsAffichees)."') ";
        }
        
        

        // 2 - les dernieres adresses ajoutées moins celles deja affichées dans les rubriques précédentes
        /*$reqAdresses = "
            SELECT  ha1.idAdresse as idAdresse, ha1.numero as numero, ha1.idRue as idRue , ha1.idQuartier as idQuartier, ha1.idSousQuartier as idSousQuartier,
                    ha1.idVille as idVille,ha1.idPays as idPays, ha1.idIndicatif as idIndicatif,ha1.date as dateCreationAdresse,hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload, hi1.idHistoriqueImage,hi1.idImage,
                    
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue,ha1.date as date
                    


            FROM historiqueAdresse ha2, historiqueAdresse ha1
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
            LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
            
            

            LEFT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
            LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
            LEFT JOIN _evenementImage ei ON ei.idEvenement = he1.idEvenement
            LEFT JOIN _evenementImage ei2 ON ei2.idEvenement = he1.idEvenement
            LEFT JOIN historiqueImage hi1 ON hi1.idImage = ei.idImage
            LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
            
            
            
            LEFT JOIN rue r         ON r.idRue = ha1.idRue
            LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
            LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
            LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
            LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
            
            
            WHERE ha2.idAdresse = ha1.idAdresse
            ".$sqlWhere."
            ".$sqlAdressesExclues."
            GROUP BY ha1.idAdresse ,he1.idEvenement,hi1.idImage, ha1.idHistoriqueAdresse, he1.idHistoriqueEvenement, hi1.idHistoriqueImage,ei.position
            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement) and hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ei.position = min(ei2.position)
            ORDER BY ha1.date DESC
        ";
        */

        
        //
        
        $reqAdresses = "
            SELECT  ha1.idAdresse as idAdresse, ha1.date as dateCreationAdresse,ha1.numero as numero, ha1.idRue as idRue , ha1.idQuartier as idQuartier, ha1.idSousQuartier as idSousQuartier,
                    ha1.idVille as idVille,ha1.idPays as idPays, ha1.idIndicatif as idIndicatif,
                    
                                        r.nom as nomRue,
                                        q.nom as nomQuartier,
                                        sq.nom as nomSousQuartier,
                                        v.nom as nomVille,
                                        r.prefixe as prefixeRue,ha1.date as date,
                        ae.idEvenement as idEvenementGroupeAdresses
                    


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
            ".$sqlWhere."
            ".$sqlAdressesExclues."
            GROUP BY ha1.idAdresse ,he1.idEvenement, ha1.idHistoriqueAdresse, he1.idHistoriqueEvenement
            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ORDER BY ha1.date DESC
        ";
        
        
        $resAdresses = $this->connexionBdd->requete($reqAdresses);
        $image = new archiImage();
        $isImageAdresses=false;
        while($fetchAdresses = mysql_fetch_assoc($resAdresses))
        {
            if(!in_array($fetchAdresses['idAdresse'],$tabAdressesNouvellesAdressesAffichees) && !in_array($fetchAdresses['idEvenementGroupeAdresses'],$tabEvenementGroupeAdressesAffichees))
            {
            
                $tabAdressesNouvellesAdressesAffichees[]=$fetchAdresses['idAdresse'];
                $tabEvenementGroupeAdressesAffichees[] = $fetchAdresses['idEvenementGroupeAdresses'];
                //$this->getUrlImage("moyen")."/".$fetchAdresses['dateUpload']."/".$fetchAdresses['idHistoriqueImage'].".jpg"
                $infosAdresseCourante = array(
                                                        "idAdresse"=>$fetchAdresses['idAdresse'],
                                                        "idIndicatif"=>$fetchAdresses['idIndicatif'],
                                                        "numero"=>$fetchAdresses['numero'],
                                                        "nomRue"=>$fetchAdresses['nomRue'],
                                                        "nomQuartier"=>$fetchAdresses['nomQuartier'],
                                                        "nomSousQuartier"=>$fetchAdresses['nomSousQuartier'],
                                                        "nomVille"=>$fetchAdresses['nomVille'],
                                                        "prefixeRue"=>$fetchAdresses['prefixeRue'],
                                                        "idEvenementGroupeAdresse"=>$fetchAdresses['idEvenementGroupeAdresses'],
                                                        "dateCreationAdresse"=>$fetchAdresses['dateCreationAdresse']
                                                        
                                                        );// ,"description"=>"" 

                $tabDernieresAdresses[] = $infosAdresseCourante;
            }
            
            
            $resImages = $image->getImagesEvenementsFromAdresse($fetchAdresses['idAdresse']);
            if(mysql_num_rows($resImages)>0)
            {
                $isImageAdresses=true;
            }
            
            if(count($tabDernieresAdresses)>=25 && $isImageAdresses)
            {
                break;
            }
        }
        

        // il faut maintenant parcourir les tableaux pour afficher les images de facon correcte
        $image = new archiImage();
        
        $indiceElementPremierePosition=array();
        $imageElementPremierePosition=array();
        
        // *******************************************************************************
        // recuperation de l'image qui sera affichée pour les demolitions 
        $trouveImageDemolition=false;
        $i=0;
        $tab5Demolitions= array();
        foreach($tabDemolition as $indice => $value)
        {
            if(!$trouveImageDemolition)
            {
                if(!isset($value['idHistoriqueImage']) || $value['idHistoriqueImage']=='0' )
                {
                    // ici on recherche une autre image pour la meme adresse car il n'y en avait pas sur l'evenement concerné
                    //$resImagesDemolitionFromAdresse = $image->getImagesEvenementsFromAdresse($value['idAdresse']);
                    //$fetchImageDemolition = mysql_fetch_assoc($resImagesDemolitionFromAdresse);
                    $fetch = $this->getFirstImageFromEvenement($value['idEvenement']);
                    if(isset($fetch['idHistoriqueImage']) && $fetch['idHistoriqueImage']!='' && $fetch['idHistoriqueImage']!='0')
                    {
                        $imageElementPremierePosition['demolition'] =$fetch;//array('idHistoriqueImage'=> $fetchImageDemolition['idHistoriqueImage'], 'dateUpload'=>$fetchImageDemolition['dateUpload'] );
                        
                        //$indiceElementPremierePosition['demolition'] = $indice;
                        $trouveImageDemolition=true;
                        $tab5Demolitions[0] = $value;
                        $indiceElementPremierePosition['demolition'] = 0;
                        
                    }
                    else
                    {
                        $trouveImageDemolition=false;
                    }
                    
                }
                /*else
                {
                    $trouveImageDemolition=true;
                    $imageElementPremierePosition['demolition'] = array('idHistoriqueImage'=> $value['idHistoriqueImage'], 'dateUpload'=>$value['dateUpload'] );
                    $indiceElementPremierePosition['demolition'] = $indice;
                }*/
            }
            
            $i++;
        }
        
        // si l'on a pas trouvé d'image sur les evenements on va en chercher au niveau de tous les evenements de l'adresse
        $trouveImageDemolitionSurAdresse=false;
        if(!$trouveImageDemolition)
        {
            foreach($tabDemolition as $indice => $value)
            {
                if(!$trouveImageDemolitionSurAdresse)
                {
                    $resImagesDemolitionFromAdresse=$image->getImagesEvenementsFromAdresse($value['idAdresse'],array('idEvenementGroupeAdresse'=>$value['idEvenementGroupeAdresse']));
                    if(mysql_num_rows($resImagesDemolitionFromAdresse)>0)
                    {
                        $fetch = mysql_fetch_assoc($resImagesDemolitionFromAdresse);
                        $imageElementPremierePosition['demolition'] = $fetch;
                        //$indiceElementPremierePosition['demolition'] = $indice;
                        $trouveImageDemolitionSurAdresse=true;
                        
                        $tab5Demolitions[0] = $value;
                        $indiceElementPremierePosition['demolition'] = 0;
                    }
                }
            }
        }
        
        
        // on recupere l'evenement qui comporte l'image et on limite le tableau en sortie a 5
        $i=1;
        if($trouveImageDemolitionSurAdresse || $trouveImageDemolition) // en principe maintenant c'est toujours possible , vu qu'on parcours tout et on s'arrete seulement s'il y a une image dans la boucle précédente
        {
            foreach($tabDemolition as $indice => $value)
            {
                if($i>4)
                {
                    break;
                }
                else
                {
                    if($value['idEvenementGroupeAdresse']!=$tab5Demolitions[0]['idEvenementGroupeAdresse']) // le tableau d'indice 0 est deja renseigne
                    {
                        $tab5Demolitions[$i] = $value;
                        $i++;
                    }
                    
                }
            }
        
        
        }
        // *******************************************************************************
        // recuperation de l'image qui sera affichee pour les derniers travaux
        
        
        $trouveImageConstruction=false;
        $i=0;
        $tab5Constructions= array();
        foreach($tabConstruction as $indice => $value)
        {
            if(!$trouveImageConstruction)
            {
                if(!isset($value['idHistoriqueImage']) || $value['idHistoriqueImage']=='0')
                {
                    // ici on recherche une autre image pour la meme adresse car il n'y en avait pas sur l'evenement concerné
                    //$resImagesConstructionFromAdresse = $image->getImagesEvenementsFromAdresse($value['idAdresse']);
                    //$fetchImageConstruction = mysql_fetch_assoc($resImagesConstructionFromAdresse);
                    $fetch = $this->getFirstImageFromEvenement($value['idEvenement']);
                    if (isset($fetch['idHistoriqueImage']) && $fetch['idHistoriqueImage']!='' && $fetch['idHistoriqueImage']!='0') {
                        $imageElementPremierePosition['construction'] = $fetch;//array('idHistoriqueImage'=> $fetchImageConstruction['idHistoriqueImage'], 'dateUpload'=>$fetchImageConstruction['dateUpload'] );
                        //$indiceElementPremierePosition['construction'] = $indice;
                        $trouveImageConstruction=true;
                        $tab5Constructions[0] = $value;
                        $indiceElementPremierePosition['construction'] = 0;
                        
                    }
                    else
                    {
                        $trouveImageConstruction=false;
                    }

                    
                }
                /*else
                {
                    $trouveImageConstruction=true;
                    $imageElementPremierePosition['construction'] = array('idHistoriqueImage'=> $value['idHistoriqueImage'], 'dateUpload'=>$value['dateUpload'] );
                    $indiceElementPremierePosition['construction'] = $indice;
                }*/
            }
            
            $i++;
        }
        
        // si l'on a pas trouvé d'image sur les evenements on va en chercher au niveau de tous les evenements de l'adresse
        $trouveImageConstructionSurAdresse=false;
        if(!$trouveImageConstruction)
        {
            foreach($tabConstruction as $indice => $value)
            {
                if(!$trouveImageConstructionSurAdresse)
                {
                    $resImagesConstructionFromAdresse=$image->getImagesEvenementsFromAdresse($value['idAdresse'],array('idEvenementGroupeAdresse'=>$value['idEvenementGroupeAdresse']));
                    if(mysql_num_rows($resImagesConstructionFromAdresse)>0)
                    {
                        $fetch = mysql_fetch_assoc($resImagesConstructionFromAdresse);
                        $imageElementPremierePosition['construction'] = $fetch;
                        //$indiceElementPremierePosition['construction'] = $indice;
                        $trouveImageConstructionSurAdresse=true;
                        
                        
                        $tab5Constructions[0] = $value;
                        $indiceElementPremierePosition['construction'] = 0;
                    }
                }
            }
        }
        
        // on recupere l'evenement qui comporte l'image et on limite le tableau en sortie a 5
        $i=1;
        // en principe maintenant c'est toujours possible, vu qu'on parcours tout et on s'arrete seulement s'il y a une image dans la boucle précédente
        if ($trouveImageConstructionSurAdresse || $trouveImageConstruction) {
            foreach($tabConstruction as $indice => $value)
            {
                if ($i>4) {
                    break;
                } else {
                    if(isset($tab5Constructions[0]) && $value['idEvenementGroupeAdresse']!=$tab5Constructions[0]['idEvenementGroupeAdresse'])
                    {
                        $tab5Constructions[$i] = $value;
                        $i++;
                    }
                }
            }
        }
        
        
        
        
        
        

        // *******************************************************************************
        // recuperation de l'image qui sera affichee pour les derniers evenements culturels
        $trouveImageCulturel=false;
        $i=0;
        $tab5Culturel = array();
        foreach($tabCulturel as $indice => $value)
        {
            if(!$trouveImageCulturel)
            {
                if(!isset($value['idHistoriqueImage']) || $value['idHistoriqueImage']=='0')
                {
                    // ici on recherche une autre image pour la meme adresse car il n'y en avait pas sur l'evenement concerné
                    //$resImagesCulturelFromAdresse = $this->getFirstImageFromEvenement($value['idEvenement']);//$image->getImagesEvenementsFromAdresse($value['idAdresse']);
                    //$fetchImageCulturel = mysql_fetch_assoc($resImagesCulturelFromAdresse);
                    $fetch = $this->getFirstImageFromEvenement($value['idEvenement']);
                    if(isset($fetch['idHistoriqueImage']) && $fetch['idHistoriqueImage']!='' && $fetch['idHistoriqueImage']!='0')
                    {
                        $imageElementPremierePosition['culturel'] = $fetch;//$this->getFirstImageFromEvenement($value['idEvenement']);//array('idHistoriqueImage'=> $fetchImageCulturel['idHistoriqueImage'], 'dateUpload'=>$fetchImageCulturel['dateUpload'] );
                        
                        //$indiceElementPremierePosition['culturel'] = $indice;
                        $trouveImageCulturel=true;
                        $indiceElementPremierePosition['culturel'] = 0;
                        $tab5Culturel[0] = $value;
                    }
                    else
                    {
                        $trouveImageCulturel=false;
                    }
                }
                /*else
                {
                    $trouveImageCulturel=true;
                    $imageElementPremierePosition['culturel'] = array('idHistoriqueImage'=> $value['idHistoriqueImage'], 'dateUpload'=>$value['dateUpload'] );
                    $indiceElementPremierePosition['culturel'] = $indice;
                }*/
            }
            
            $i++;
        }
        
        // si l'on a pas trouvé d'image sur les evenements on va en chercher au niveau de tous les evenements de l'adresse
        $trouveImageCulturelSurAdresse=false;
        if(!$trouveImageCulturel)
        {
            foreach($tabCulturel as $indice => $value)
            {
                if(!$trouveImageCulturelSurAdresse)
                {
                    $resImagesCulturelFromAdresse=$image->getImagesEvenementsFromAdresse($value['idAdresse'],array('idEvenementGroupeAdresse'=>$value['idEvenementGroupeAdresse']));
                    if(mysql_num_rows($resImagesCulturelFromAdresse)>0)
                    {
                        $fetch = mysql_fetch_assoc($resImagesCulturelFromAdresse);
                        $imageElementPremierePosition['culturel'] = $fetch;
                        //$indiceElementPremierePosition['culturel'] = $indice;
                        $trouveImageCulturelSurAdresse=true;
                        
                        $indiceElementPremierePosition['culturel'] = 0;
                        $tab5Culturel[0] = $value;
                    }
                }
            }
        }
        
        
        // on recupere l'evenement qui comporte l'image et on limite le tableau en sortie a 5
        $i=1;
        if($trouveImageCulturelSurAdresse || $trouveImageCulturel) // en principe maintenant c'est toujours possible , vu qu'on parcours tout et on s'arrete seulement s'il y a une image dans la boucle précédente
        {
            foreach($tabCulturel as $indice => $value)
            {
                if($i>4)
                {
                    break;
                }
                else
                {
                    if($value['idEvenementGroupeAdresse']!=$tab5Culturel[0]['idEvenementGroupeAdresse'])
                    {
                        $tab5Culturel[$i] = $value;
                        $i++;
                    }
                }
            }
        }
        
        
        

        // *******************************************************************************
        // recuperation de l'image qui sera affichee pour les dernieres adresses ajoutées
        
        
        $trouveImageDernieresAdresses=false;
        $i=0;
        $tab5DernieresAdresses=array();
        foreach($tabDernieresAdresses as $indice => $value)
        {
            if(!$trouveImageDernieresAdresses)
            {
                if(!isset($value['idHistoriqueImage']) || $value['idHistoriqueImage']=='0' )
                {
                    // ici on recherche une autre image pour la meme adresse car il n'y en avait pas sur l'evenement concerné
                    $resImagesDernieresAdressesFromAdresse = $image->getImagesEvenementsFromAdresse($value['idAdresse']);
                    
                    if(mysql_num_rows($resImagesDernieresAdressesFromAdresse)>0)
                    {
                        $fetchImageDernieresAdresses = mysql_fetch_assoc($resImagesDernieresAdressesFromAdresse);
                        
                        $imageElementPremierePosition['dernieresAdresses'] = array('idHistoriqueImage'=> $fetchImageDernieresAdresses['idHistoriqueImage'], 'dateUpload'=>$fetchImageDernieresAdresses['dateUpload'] );
                        //$indiceElementPremierePosition['dernieresAdresses'] = $indice;
                        $trouveImageDernieresAdresses=true;
                        //$tabDernieresAdresses[$indice]['description'] = $this->getDescriptionEvenementForDerniereAdresse($value['idAdresse']);
                        
                        
                        
                        $tab5DernieresAdresses[0]=$value;
                        $tab5DernieresAdresses[0]['description'] = $this->getDescriptionEvenementForDerniereAdresse($value['idAdresse']);
                        $indiceElementPremierePosition['dernieresAdresses'] = 0;
                    }
                }
                /*else
                {
                    $trouveImageDernieresAdresses=true;
                    $imageElementPremierePosition['dernieresAdresses'] = array('idHistoriqueImage'=> $value['idHistoriqueImage'], 'dateUpload'=>$value['dateUpload'] );
                    $indiceElementPremierePosition['dernieresAdresses'] = $indice;
                    $tabDernieresAdresses[$indice]['description'] = $this->getDescriptionEvenementForDerniereAdresse($value['idAdresse']);
                }*/
            }
            
            $i++;
        }
        
        $i=1;
        foreach($tabDernieresAdresses as $indice => $value)
        {
            if($i>4)
            {
                break;
            }
            else
            {
                if($value['idAdresse']!=$tab5DernieresAdresses[0]['idAdresse'])
                {
                    $tab5DernieresAdresses[$i]=$value;
                    $i++;
                }
            }
            
            
        }
        
        // **********************************************************************************************************************************
        // encars des dernieres vues
        
        $tabDernieresVues = $image->getDernieresVues(array('sqlLimit'=>"LIMIT 5",'noAdressesDoublons'=>true,'listeIdGroupesAdressesVueSurANePasAfficher'=>$tabEvenementGroupeAdressesAffichees));
        
        // **********************************************************************************************************************************
        // encart des actualites
        $accueil = new archiAccueil();
        $tabActualites = $accueil->getDernieresActualites(array('sqlLimit'=>"LIMIT 5",'sqlWhere'=>" AND desactive<>'1' "));
        

        
        return array("dernieresAdresses"=>$tab5DernieresAdresses,"constructions"=>$tab5Constructions,"demolitions"=>$tab5Demolitions,"culture"=>$tab5Culturel,"indiceEvenementsPremierePositions"=>$indiceElementPremierePosition,"imagesEvenementsPremieresPositions"=>$imageElementPremierePosition,"dernieresVues"=>$tabDernieresVues,"actualites"=>$tabActualites);
    }
    
    
    
    public function getPositionFromEvenement($idEvenement=0)
    {
        $positionRetour=0;
        
        $req = "
        
            SELECT distinct he1.idEvenement as idEvenement
            FROM 
                historiqueEvenement he1,historiqueEvenement he2
            RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$idEvenement."'
            RIGHT JOIN _evenementEvenement ee2 ON ee2.idEvenement = ee.idEvenement
            WHERE
                he2.idEvenement = he1.idEvenement
            AND he1.idEvenement = ee2.idEvenementAssocie
            GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
            HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
            ORDER BY he1.dateDebut,he1.idHistoriqueEvenement
        ";
        
        $res = $this->connexionBdd->requete($req);
        
        $position=0;
        $trouve=false;

        while(!$trouve && $fetch = mysql_fetch_assoc($res))
        {
            if($idEvenement == $fetch['idEvenement'])
            {
                $positionRetour = $position;
                $trouve=true;
            }
            $position++;
        }
        
        return $positionRetour;
        
    }
    
    // recupere une description appartenant à un evenement de l'adresse donnée en parametre
    function getDescriptionEvenementForDerniereAdresse($idAdresse=0)
    {
        $req = "
                SELECT he1.description as description
                FROM historiqueEvenement he1, historiqueEvenement he2
                LEFT JOIN historiqueAdresse ha1 ON ha1.idAdresse = '".$idAdresse."'
                LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                WHERE
                    he1.idEvenement = ee.idEvenementAssocie
                AND
                    he2.idEvenement = he1.idEvenement
                AND he1.description<>''
                GROUP BY he1.idEvenement,ha1.idAdresse,he1.idHistoriqueEvenement,ha1.idHistoriqueAdresse
                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        ";
        
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch['description'];
    }
    
    
    
    // ************************************************************************************************************************
    // ajoute une rue a partir de l'idSousQuartier avec verification de l'existence de la rue
    // ************************************************************************************************************************
    private function ajoutRue($idSousQuartier,$nom,$prefixe)
    {
        // on verifie qu'il n'existe pas deja une rue du meme nom dans la ville
        // recherche de la ville
        $reqVille = "
                    SELECT distinct v.idVille as idVille
                    FROM ville v 
                    LEFT JOIN quartier q ON q.idVille = v.idVille
                    LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                    WHERE
                        sq.idSousQuartier = '$idSousQuartier'
                    ";
        $resVille = $this->connexionBdd->requete($reqVille);
        $adresseExistante = 0;
        if(mysql_num_rows($resVille)>0)
        {
            $fetchVille = mysql_fetch_assoc($resVille);
            $idVille =$fetchVille['idVille'];
            
            
            $reqVerif = "
            SELECT r.idRue
            FROM rue r
            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
            LEFT JOIN ville v ON v.idVille = q.idVille
            WHERE       
            LOWER(r.nom) = LOWER(\"".$nom."\")
            AND LOWER(r.prefixe) = LOWER(\"".$prefixe."\")
            AND v.idVille = '".$idVille."'
            ";
            
            $resVerif=$this->connexionBdd->requete($reqVerif);
            if(mysql_num_rows($resVerif)>0)
            {
                $adresseExistante = 1;
            }
            
        }
    
    
        $sql="
            SELECT idRue 
            FROM rue 
            WHERE idSousQuartier = '".$idSousQuartier."'
            AND LOWER(nom) = LOWER(\"".$nom."\")
            AND LOWER(prefixe) = LOWER(\"".$prefixe."\")
        ";
        
        $res=$this->connexionBdd->requete($sql);
        $newIdRue = 0;
        if($adresseExistante || mysql_num_rows($res)>0)
        {
            $this->erreurs->ajouter(_("Erreur :")." "._("il y a déjà un enregistrement de rue du même nom dans cette ville"));
        }
        else
        {
            // ajout
            $this->connexionBdd->requete("
                INSERT INTO rue (idSousQuartier,nom,prefixe) 
                VALUES ('".$idSousQuartier."',\"".$nom."\",\"".$prefixe."\")
            ");
            
            $newIdRue = mysql_insert_id();
            
        }
        
        return $newIdRue;
    }
    
    // ************************************************************************************************************************
    //   te.nom ='Construction' or te.nom='Rénovation' or te.nom='Extension' or te.nom='Transformation' or te.nom='Ravalement'
    //  te.nom = 'Démolition'
    // ************************************************************************************************************************
    public function getIdAdressesFromCriteres($parametres=array())
    {
        $arrayIdAdresses=array();
        $limitSql="";
        $whereSql="";
        
        if(isset($parametres['limitSql']))
            $limitSql = $parametres['limitSql'];
        
        if(isset($parametres['whereSql']))
            $whereSql = $parametres['whereSql'];
    
        $sql = "
                SELECT ae.idEvenement, ae.idAdresse,he1.titre , ha1.numero
                FROM _adresseEvenement ae
                RIGHT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                RIGHT JOIN historiqueEvenement he1 ON he1.idEvenement = ee.idEvenementAssocie
                RIGHT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
                RIGHT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                RIGHT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                RIGHT JOIN typeEvenement te ON te.idTypeEvenement = he1.idTypeEvenement
                WHERE ".$whereSql."
                GROUP BY he1.idEvenement,ha1.idAdresse, he1.idHistoriqueEvenement, ha1.idHistoriqueAdresse
                HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement) AND ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ".$limitSql."
        ";
        
        $res=$this->connexionBdd->requete($sql);
        
        while($fetch = mysql_fetch_assoc($res))
        {
            $arrayIdAdresses[] = $fetch['idAdresse'];
        }
        
        return array_unique($arrayIdAdresses);
    }
    
    // ************************************************************************************************************************
    // fonction affichant le recapitulatif des adresses de l'evenement groupe d'adresse donné
    // ************************************************************************************************************************
    public function afficherRecapitulatifAdresses($idEvenementGroupeAdresse=0)
    {
        $html="";
        $t = new Template('modules/archi/templates/');
        $t->set_filenames((array('recapitulatifAdresses'=>'recapitulatifAdresses.tpl')));

        $retourAdresse=$this->afficherListe(
            array(
                'archiIdEvenement'=>$idEvenementGroupeAdresse, 'useTemplateFile'=>'listeAdressesDetailEvenement.tpl'
            ),
            'listeDesAdressesDuGroupeAdressesSurDetailAdresse'
        );
        
        $t->assign_vars(array('recapitulatifAdresses'=>$retourAdresse['html']));
        
        $t->assign_vars(
            array(
                'urlAutresBiensRue'=>$retourAdresse['arrayRetourLiensVoirBatiments']['urlAutresBiensRue'],
                'urlAutresBiensQuartier'=>$retourAdresse['arrayRetourLiensVoirBatiments']['urlAutresBiensQuartier']
            )
        );
        
        $idAdresseCourante = 0;
        if(isset($this->variablesGet['archiIdAdresse']))
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        
        
        
        // ************************************************************************************************************************
        // affichage carte googlemap dans une iframe
        
        // && $coordonnees['latitude']>48.3776285 && $coordonnees['latitude']<48.78554409 && $coordonnees['longitude']>7.47482299 && $coordonnees['longitude']<7.993927001
        $coordonnees = $this->getCoordonneesFrom($retourAdresse['arrayIdAdresses'][0],'idAdresse');
        
        if(count($coordonnees)==2 && $coordonnees['longitude']!='' && $coordonnees['latitude']!='' && $coordonnees['longitude']!='0' && $coordonnees['latitude']!='0' && $coordonnees['longitude']>0 && $coordonnees['latitude']>0 )
        {
            $evenement = new archiEvenement();
            $calqueGoogleMap = new calqueObject(array('idPopup'=>10));
            
            $contenuIFramePopup = $evenement->getContenuIFramePopupGoogleMap(array(
                                        'idAdresseCourante'=>$idAdresseCourante,
                                        'calqueObject'=>$calqueGoogleMap,
                                        'idEvenementGroupeAdresseCourant'=>$idEvenementGroupeAdresse
                                        ));
        
        
            $t->assign_block_vars('isCarteGoogle',array(
                    'src'=>$this->creerUrl('','afficheGoogleMapIframe',array('noHeaderNoFooter'=>1,'longitude'=>$coordonnees['longitude'],'latitude'=>$coordonnees['latitude'],'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'archiIdAdresse'=>$idAdresseCourante)),
                    'lienVoirCarteGrand'=>"<a href='#' onclick=\"".$calqueGoogleMap->getJsOpenPopupNoDraggableWithBackgroundOpacity()."document.getElementById('iFrameDivPopupGM').src='".$this->creerUrl('','afficheGoogleMapIframe',array('longitude'=>$coordonnees['longitude'],'latitude'=>$coordonnees['latitude'],'noHeaderNoFooter'=>true,'archiIdAdresse'=>$idAdresseCourante,'archiIdEvenementGroupeAdresse'=>$idEvenementGroupeAdresse,'modeAffichage'=>'popupDetailAdresse'))."';\" style='font-size:11px;'>"._("voir la carte en + grand")."</a>",
                    'popupGoogleMap'=>$calqueGoogleMap->getDivNoDraggableWithBackgroundOpacity(array('top'=>20,'lienSrcIFrame'=>'','contenu'=>$contenuIFramePopup))
                    ));
            $t->assign_vars(array('largeurTableauAdresse'=>420,'hauteurRecapAdresse'=>'277'));
        }
        else
        {
            $t->assign_vars(array('largeurTableauAdresse'=>700,'hauteurRecapAdresse'=>''));
        }
        // ************************************************************************************************************************
        
        
        
        
        ob_start();
        $t->pparse('recapitulatifAdresses');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    // ************************************************************************************************************************
    // affiche le formulaire d'ajout d'un commentaire
    // ************************************************************************************************************************
    public function getFormulaireCommentaires($idEvenementGroupeAdresse=0,$fieldsCommentaires=array())
    {
        $html="";
        
        $e = new archiEvenement();
        $idEvenementGroupeAdresse = $e->getIdEvenementGroupeAdresseFromIdEvenement($idEvenementGroupeAdresse);
        
        
        $fieldsCommentaires["idEvenementGroupeAdresse"]['default'] = $idEvenementGroupeAdresse;
        
        // si un utilisateur est connecté , on renseigne directement ces infos , mais on lui laisse la possibilité de modifier
        $authentification = new archiAuthentification();

        if($authentification->estConnecte())
        {
            $idUtilisateur = $authentification->getIdUtilisateur();
            $utilisateur = new archiUtilisateur();
            $fetchUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($idUtilisateur);
            // patchs pour que l'on reaffiche les nom prenom et mail une fois le formulaire validé et le commentaire enregistré
            if(isset($this->variablesPost['nom']))
            {
                unset($this->variablesPost['nom']);
                unset($_POST['nom']);
            }
            if(isset($this->variablesPost['prenom']))
            {
                unset($this->variablesPost['prenom']);
                unset($_POST['prenom']);
            }
            if(isset($this->variablesPost['email']))
            {
                unset($this->variablesPost['email']);
                unset($_POST['email']);
            }
            $fieldsCommentaires['nom']['default']=stripslashes($fetchUtilisateur['nom']);
            $fieldsCommentaires['prenom']['default']=stripslashes($fetchUtilisateur['prenom']);
            $fieldsCommentaires['email']['default']=$fetchUtilisateur['mail'];
            
            
            $fieldsCommentaires['nom']['type']='hidden';
            $fieldsCommentaires['prenom']['type']='hidden';
            $fieldsCommentaires['email']['type']='hidden';
            
            unset($fieldsCommentaires['captcha']); // pas de captcha quand on est connecté
            
        }
        
        $help = $this->getHelpMessages('helpEvenement');
        
        $bbMiseEnFormBoutons= "<div style=''><input type=\"button\" value=\"b\" style=\"width:50px;font-weight:bold\" onclick=\"bbcode_ajout_balise('b', 'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('".$help["msgGras"]."');\" onMouseOut=\"closeContextHelp();\"/>
    <input type=\"button\" value=\"i\" style=\"width:50px;font-style:italic\" onclick=\"bbcode_ajout_balise('i', 'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('".$help["msgItalic"]."');\" onMouseOut=\"closeContextHelp();\"/>
    <input type=\"button\" value=\"u\" style=\"width:50px;text-decoration:underline;\" onclick=\"bbcode_ajout_balise('u', 'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('".$help["msgUnderline"]."');\" onMouseOut=\"closeContextHelp();\"/>
    <input type=\"button\" value=\"quote\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('quote', 'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('".$help["msgQuotes"]."');\" onMouseOut=\"closeContextHelp();\"/>
    <!--<input type=\"button\" value=\"code\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('code', 'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('{msgCode}');\" onMouseOut=\"closeContextHelp();\" onkeyup=\"bbcode_keyup(this,'apercu');\"/>-->
    <input type=\"button\" value=\"url interne\"  style=\"width:75px\" onclick=\"bbcode_ajout_balise('url',  'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('Insérer une adresse WEB interne à archi-strasbourg.org');\" onMouseOut=\"closeContextHelp();\" onkeyup=\"bbcode_keyup(this,'apercu');\"/>
    <input type=\"button\" value=\"url externe\"  style=\"width:80px\" onclick=\"bbcode_ajout_balise('urlExterne',  'formAjoutCommentaire', 'commentaire');bbcode_keyup(this,'apercu');\" onMouseOver=\"getContextHelp('Insérer une adresse WEB externe à archi-strasbourg.org');\" onMouseOut=\"closeContextHelp();\" onkeyup=\"bbcode_keyup(this,'apercu');\"/></div>";
    
        $fieldsCommentaires['commentaire']['htmlCodeBeforeField'] = $bbMiseEnFormBoutons;
    
        $tabCommentaires = array(   'titrePage'=>_("Ajouter un commentaire"),
                                    'formName'=>'formAjoutCommentaire',
                                    'formAction'=>$this->creerUrl('enregistreCommentaire','',array()),
                                    'tableHtmlCode'=>" class='formAjoutCommentaire'",
                                    'codeHtmlInFormAfterFields'=>_("Prévisualisation :")."<div id='apercu'></div><div id='helpCalque' style='background-color:#FFFFFF; border:2px solid #000000;padding:10px;float:left;display:none;'><img src='images/aide.jpg' style='float:left;padding-right:3px;' valign='middle'><div id='helpCalqueTxt' style='padding-top:7px;'></div></div><script type='text/javascript' >
                                    bbcode_keyup(document.forms['formAjoutCommentaire'].elements['commentaire'], 'apercu');setTimeout('majDescription()',1000);
                                    function majDescription()
                                    {
                                        bbcode_keyup(document.forms['formAjoutCommentaire'].elements['commentaire'], 'apercu');
                                        setTimeout('majDescription()',500);
                                    }</script>",
                                    'fields'=>$fieldsCommentaires);
        
        
        $formulaire = new formGenerator();
        
        
        $bbCode = new bbCodeObject();
        

        $html.= $formulaire->afficherFromArray($tabCommentaires);
        
    
        return $html;
    }
    
    // ************************************************************************************************************************
    // affiche la liste des commentaires pour un groupe d'adresse donné
    // ************************************************************************************************************************
    public function getListeCommentaires($idEvenementGroupeAdresse=0)
    {
        $bbCode = new bbCodeObject();
        $u = new archiUtilisateur();
        $html="";
        $t = new Template('modules/archi/templates/');
        $t->set_filenames((array('listeCommentaires'=>'listeCommentaires.tpl')));
        
        $req = "SELECT c.idCommentaire as idCommentaire,c.nom as nom,c.prenom as prenom,c.email as email,DATE_FORMAT(c.date,'"._("%d/%m/%Y à %kh%i")."') as dateF,c.commentaire as commentaire,c.idUtilisateur as idUtilisateur, u.urlSiteWeb as urlSiteWeb
                FROM commentaires c
                LEFT JOIN utilisateur u ON u.idUtilisateur = c.idUtilisateur
                WHERE c.idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."'
                AND CommentaireValide=1
                ORDER BY date DESC
        ";
        
        
        $res = $this->connexionBdd->requete($req);
        
        
        $t->assign_vars(array('tableHtmlCode'=>"  "));
        
        if(mysql_num_rows($res)==0)
        {
            $t->assign_vars(array("msg"=>_("Il n'y a pas encore de commentaires pour cette adresse.")."<br><br>"));
        }
        
        $authentification = new archiAuthentification();
        
        
        
        // si l'utilisateur est administrateur, on affiche le bouton de suppression d'un commentaire
        $isAdmin=false;
        if($authentification->estConnecte() && $authentification->estAdmin())
        {
            $isAdmin=true;
        }
        
        
        while($fetch = mysql_fetch_assoc($res))
        {
            $adresseMail = "";
            $boutonSupprimer="";
            $urlSiteWeb = "";
            if($fetch['urlSiteWeb']!='')
            {
                $urlSiteWeb = "<br><a itemprop='url' href='".$fetch['urlSiteWeb']."' target='_blank'><span style='font-size:9px;color:#FFFFFF;'>".$fetch['urlSiteWeb']."</span></a>";
            }
            
            if($isAdmin)
            {
                $archiIdAdresse='';
                if(isset($this->variablesGet['archiIdAdresse']))
                {
                    $archiIdAdresse = $this->variablesGet['archiIdAdresse'];
                }
                $boutonSupprimer = "<input type='button' value='supprimer' onclick=\"location.href='".$this->creerUrl('supprimerCommentaire','',array('archiIdCommentaire'=>$fetch['idCommentaire'],'archiIdAdresse'=>$archiIdAdresse))."';\">";
                $adresseMail = "<br><a style='font-size:9px;color:#FFFFFF;' itemprop='email' href='mailto:".$fetch['email']."'>".$fetch['email']."</a>";
            }
            $t->assign_block_vars('commentaires',array(
                'infosPersonne'=>"".$fetch['dateF'].' : <span itemprop="name">'.$fetch['nom'].' '.$fetch['prenom']."</span>",
                'adresseMail'=>$adresseMail,
                'commentaire'=>"<img itemprop='image' src='".$u->getImageAvatar(array('idUtilisateur'=>$fetch['idUtilisateur']))."' border=0 align=left style='padding-right:5px;padding-bottom:5px;'>".$bbCode->convertToDisplay(array('text'=>stripslashes($fetch['commentaire']))), 
                'boutonSupprimer'=>$boutonSupprimer,
                'siteWeb'=>$urlSiteWeb)
                );
        }
        
        ob_start();
        $t->pparse('listeCommentaires');
        $html .= ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Fonction qui permet d'afficher les derniers commentaires postés par les internautes
     * */
    public function getDerniersCommentaires($params=array())
    {
        $html = '';
        $string = new stringObject();
        $bbCode = new bbCodeObject();
        
        $sqlLimit="LIMIT 5";
                
        
        if(isset($params['afficherTous']) && $params['afficherTous']==true)
        {
            $reqCount = "
                SELECT distinct idCommentaire
                FROM commentaires c
                WHERE CommentaireValide=1
            ";
            
            $resCount = $this->connexionBdd->requete($reqCount);
            
            $nbEnregistrementTotaux = mysql_num_rows($resCount);
        
            $pagination = new paginationObject();
            $nbEnregistrementsParPage = 15;
            $arrayPagination=$pagination->pagination(array(
                                        'nomParamPageCourante'=>'pageCourante',
                                        'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
                                        'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
                                        'typeLiens'=>'noformulaire'
                                        ));
            
            $sqlLimit=$pagination->addLimitToQuery();
            
        }

        
        $req = "
                SELECT distinct idCommentaire, email,nom,prenom,commentaire,idEvenementGroupeAdresse,DATE_FORMAT(date,'%d/%m/%Y') as dateF, date
                FROM commentaires c
                LEFT JOIN _adresseEvenement ae ON ae.idEvenement = c.idEvenementGroupeAdresse
                WHERE CommentaireValide=1
                ORDER BY date DESC
                $sqlLimit
            ";
            
        $res = $this->connexionBdd->requete($req);
        
        
        // on affiche l'encart seulement s'il y a au moins un commentaire
        if(mysql_num_rows($res)>0)
        {
            $t = new Template('modules/archi/templates/');
            
            if(isset($params['afficherTous']) && $params['afficherTous']==true)
            {
                $t->set_filenames(array('derniersCommentaires'=>'tousLesCommentaires.tpl'));
            }
            else
            {
                $t->set_filenames(array('derniersCommentaires'=>'encartAccueilCommentaires.tpl'));
                $t->assign_vars(array('urlTousLesCommentaires'=>"<a href='".$this->creerUrl('','tousLesCommentaires')."'>"._("Tous les commentaires")."</a>"));
            }
            
            if(isset($params['afficherTous']) && $params['afficherTous']==true)
            {
                $t->assign_vars(array('pagination'=>$arrayPagination['html']));
            }
            
            while($fetch = mysql_fetch_assoc($res))
            {
                // recuperation de l'adresse concernée
                $resAdresses = $this->getAdressesFromEvenementGroupeAdresses($fetch['idEvenementGroupeAdresse']);
                $arrayIntituleAdresses = array();
                while($fetchAdresses = mysql_fetch_assoc($resAdresses))
                {
                    $arrayIntituleAdresses[]=$this->getIntituleAdresse($fetchAdresses);
                }
                
                
                
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetch['idEvenementGroupeAdresse']);
                //$fetchAdresse = $this->getArrayAdresseFromIdAdresse($idAdresse);
                //$intituleAdresse = $this->getIntituleAdresse($fetchAdresse);
                $imageSurListeTousLesCommentaires="";
                $urlAdresse = $this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$idAdresse,'archiIdEvenementGroupeAdresse'=>$fetch['idEvenementGroupeAdresse']));
                if(isset($params['afficherTous']) && $params['afficherTous']==true)
                {
                    $txtCommentaire = stripslashes(stripslashes($fetch['commentaire']));
                    $arrayImage = $this->getUrlImageFromAdresse($idAdresse,'mini',array('idEvenementGroupeAdresse'=>$fetch['idEvenementGroupeAdresse']));
                    $imageSurListeTousLesCommentaires="<div style='float:left;display:block;overflow:visible;padding-right:3px;width:80px;text-align:center;'><a href='".$urlAdresse."'><img src='".$arrayImage['url']."' border=0 align=middle></a></div>";
                }
                else
                {
                    $txtCommentaire = stripslashes($fetch['commentaire']);
                    
                    $arrayTxtCommentaire = explode(" ",$txtCommentaire);
                    foreach($arrayTxtCommentaire as $indice => $value)
                    {
                        if(pia_strlen($arrayTxtCommentaire[$indice])>30)
                        {
                            $arrayTxtCommentaire[$indice] = pia_substr($arrayTxtCommentaire[$indice],0,30)."...";
                        }
                    }
                    
                    $txtCommentaire = $string->coupureTexte(implode(" ",$arrayTxtCommentaire),10);
                }
                $t->assign_block_vars('commentaires',array(
                                                    'commentaire'=>$bbCode->convertToDisplay(array('text'=>$txtCommentaire)),
                                                    'pseudo'=>"<div style='display:block;overflow:auto;text-decoration:none;font-weight:normal;'>".$imageSurListeTousLesCommentaires."<span style='display:block;font-weight:normal;'>".$fetch['dateF']." "._("de")." <span style='color:#507391;font-size:9px;font-weight:normal;'>".$fetch['nom'].' '.$fetch['prenom']."</span>"."<br>"._("pour")." <a href=\"".$urlAdresse."\" style='color:#507391;font-size:9px;'>".str_replace("( - )", "", implode(" / ", $arrayIntituleAdresses))."</a></span></div><div style='clear:both;'></div>"
                                                    ));
            }

            ob_start();
            $t->pparse('derniersCommentaires');
            $html .= ob_get_contents();
            ob_end_clean();
        }
        
        
        return $html;
    }
    
    
    // ************************************************************************************************************************
    // enregistrement du commentaire
    // ************************************************************************************************************************
    public function enregistreCommentaire()
    {
        $auth = new archiAuthentification();
        $fieldsCommentaires=$this->getCommentairesFields();
        $formulaire = new formGenerator();  
        
        
        if($auth->estConnecte())
        {
            unset($fieldsCommentaires['captcha']);
        }
        
        $error = $formulaire->getArrayFromPost($fieldsCommentaires);
        
        if(count($error)==0)
        {
            
            
            $idUtilisateur=0;
            if($auth->estConnecte())
            {
                $idUtilisateur = $auth->getIdUtilisateur();
                // suite au SPAM mise en place d'un champ CommentaireValide 0/1 (by fabien 13/01/2012)
                $CommentaireValide=1;
                $user = new archiUtilisateur();
                $userInfos = $user->getArrayInfosFromUtilisateur($idUtilisateur);
            }
            else
            {
                $CommentaireValide=0;
            }

            
            // enregistrement du nouveau commentaire
            //$req = "insert into commentaires (nom,prenom,email,commentaire,idEvenementGroupeAdresse,date,idUtilisateur) values (\"".addslashes(strip_tags($this->variablesPost['nom']))."\",\"".addslashes(strip_tags($this->variablesPost['prenom']))."\",\"".addslashes(strip_tags($this->variablesPost['email']))."\",\"".addslashes(strip_tags($this->variablesPost['commentaire']))."\",'".$this->variablesPost['idEvenementGroupeAdresse']."',now(),'".$idUtilisateur."')";
            $nom=$auth->estConnecte()?$userInfos["nom"]:$this->variablesPost['nom'];
            $prenom=$auth->estConnecte()?$userInfos["prenom"]:$this->variablesPost['prenom'];
            $email=$auth->estConnecte()?$user->getMailUtilisateur($idUtilisateur):$this->variablesPost['email'];
            
            $req = "insert into commentaires (nom,prenom,email,commentaire,idEvenementGroupeAdresse,date,idUtilisateur,CommentaireValide) values (\"".addslashes(strip_tags($nom))."\",\"".addslashes(strip_tags($prenom))."\",\"".addslashes(strip_tags($email))."\",\"".addslashes(strip_tags($this->variablesPost['commentaire']))."\",'".$this->variablesPost['idEvenementGroupeAdresse']."',now(),'".$idUtilisateur. "'," . $CommentaireValide . ")";
            
            $res = $this->connexionBdd->requete($req);
            
            // retour a l'affichage de l'adresse
            $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($this->variablesPost['idEvenementGroupeAdresse']);


            
            // ************************************************************************************************************************************************
            // envoi d'un mail a tous les participants pour le groupe d'adresse
            // ************************************************************************************************************************************************
            $mail = new mailObject();
            $utilisateur = new archiUtilisateur();
            $arrayUtilisateurs = $utilisateur->getParticipantsCommentaires($this->variablesPost['idEvenementGroupeAdresse']);
            $arrayCreatorAdresse = $utilisateur->getCreatorsFromAdresseFrom($this->variablesPost['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse');
            $arrayUtilisateurs = array_merge($arrayUtilisateurs,$arrayCreatorAdresse);
            $arrayUtilisateurs = array_unique($arrayUtilisateurs);
            $intituleAdresse = $this->getIntituleAdresseFrom($idAdresse,'idAdresse');
            foreach($arrayUtilisateurs as $indice => $idUtilisateurAdresse)
            {
                if($idUtilisateurAdresse != $auth->getIdUtilisateur())
                {
                    $infosUtilisateur = $utilisateur->getArrayInfosFromUtilisateur($idUtilisateurAdresse);
                    if($infosUtilisateur['alerteCommentaires']=='1' && $infosUtilisateur['compteActif']=='1' && $infosUtilisateur['idProfil']!='4')
                    {
                        $message = "Un utilisateur a ajouté un commentaire sur une adresse ou vous avez participé.";
                        $message.= "Pour vous rendre sur l'adresse : <a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$idAdresse,'archiIdEvenementGroupeAdresse'=>$this->variablesPost['idEvenementGroupeAdresse']))."'>".$intituleAdresse."</a><br>";
                        $message.= $this->getMessageDesabonnerAlerteMail();
                        $mail->sendMail($mail->getSiteMail(),$infosUtilisateur['mail'],'Ajout d\'un commentaire sur une adresse sur laquelle vous avez participé.',$message,true);
                        
                    }
                }
            }
            // ************************************************************************************************************************************************
            
            
            
            
            // envoi d'un mail aux administrateur pour la moderation
            $message="Un utilisateur a ajouté un commentaire sur archiV2 : <br>";
            $message .= "nom ou pseudo : ".strip_tags($this->variablesPost['nom'])."<br>";
            $message .= "prenom : ".strip_tags($this->variablesPost['prenom'])."<br>";
            $message .= "email : ".strip_tags($this->variablesPost['email'])."<br>";
            $message .= "commentaire : ".stripslashes(strip_tags($this->variablesPost['commentaire']))."<br>";
            $message .="<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdEvenementGroupeAdresse'=>$this->variablesPost['idEvenementGroupeAdresse'],'archiIdAdresse'=>$idAdresse))."'>".$intituleAdresse."</a><br>";
            $mail = new mailObject();
            
            $envoyeur['envoyeur'] = $mail->getSiteMail();
            $envoyeur['replyTo'] = strip_tags($this->variablesPost['email']);
            $mail->sendMailToAdministrators($envoyeur,'Un utilisateur a ajouté un commentaire',$message," AND alerteCommentaires='1' ",true,true);
            $u = new archiUtilisateur();
            //$u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>5,'criteres'=>" and alerteCommentaires='1' "));
            
            
            
            // *************************************************************************************************************************************************************
            // envoi mail aussi au moderateur si ajout sur adresse de ville que celui ci modere
            
            
            $arrayVilles=array();
            $arrayVilles[] = $this->getIdVilleFrom($idAdresse,'idAdresse');
            $arrayVilles = array_unique($arrayVilles);
            
            $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($arrayVilles[0],array("sqlWhere"=>" AND alerteCommentaires='1' "));
            if(count($arrayListeModerateurs)>0)
            {
                foreach($arrayListeModerateurs as $indice => $idModerateur)
                {
                    if($auth->getIdUtilisateur()!=$idModerateur)
                    {
                        //if($u->isMailEnvoiImmediat($idModerateur))
                        //{
                            // pas de mail regroupé finalement
                            $mailModerateur = $u->getMailUtilisateur($idModerateur);
                            
                            $mail->sendMail($mail->getSiteMail(),$mailModerateur,'Un utilisateur a ajouté un commentaire',$message,true);
                        //}
                        //else
                        //{
                        //  $u->ajouteMailEnvoiRegroupes(array('contenu'=>$message,'idDestinataire'=>$idModerateur,'idTypeMailRegroupement'=>5));
                        //}
                    }
                }
            }
            // *************************************************************************************************************************************************************
            
            
                        
            // remise a zero des variables en post sinon on va reafficher les infos
            $_POST['commentaire']="";
            $_POST['email']="";
            $_POST['nom']="";
            $_POST['prenom']="";
            
            $this->variablesGet['archiIdEvenementGroupeAdresse'] = $this->variablesPost['idEvenementGroupeAdresse'];
            echo $this->afficherDetail($idAdresse);
        }
        else
        {
            $this->erreurs->ajouter('Il y a une erreur dans le formulaire.');
            echo $this->erreurs->afficher();
            echo $this->getListeCommentaires($this->variablesPost['idEvenementGroupeAdresse']);
            echo $this->getFormulaireCommentaires($this->variablesPost['idEvenementGroupeAdresse'],$fieldsCommentaires);
        }
    }
    
    // ************************************************************************************************************************
    // supprime un commentaire a partir de son idCommentaire
    // ************************************************************************************************************************
    public function deleteCommentaire($criteres=array())
    {
        if(isset($this->variablesGet['archiIdCommentaire']) && $this->variablesGet['archiIdCommentaire']!='')
        {
            $req = "DELETE FROM commentaires WHERE idCommentaire = '".$this->variablesGet['archiIdCommentaire']."'";
            $res = $this->connexionBdd->requete($req);
        }
        
        // redirection javascript ... pas terrible ca , a changer
        if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='')
        {
            echo "<script langage='javascript'>location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$this->variablesGet['archiIdAdresse']), false, false)."';</script>";
        }
    }
    
    // ************************************************************************************************************************
    // supprime tous les commentaires d'un idEvenementGroupeAdresse
    // ************************************************************************************************************************
    public function deleteCommentairesFromIdEvenement($idEvenementGroupeAdresse=0)
    {
        $req = "DELETE FROM commentaires WHERE idEvenementGroupeAdresse='".$idEvenementGroupeAdresse."'";
        $res = $this->connexionBdd->requete($req);
    }
    
    
    // ************************************************************************************************************************
    // recupere le premier idAdresse d'un evenement groupeAdresse
    // ************************************************************************************************************************
    public function getIdAdresseFromIdEvenementGroupeAdresse($idEvenementGroupeAdresse=0)
    {
        // recherche de l'idAdresse
        $req = "
                SELECT idAdresse
                FROM _adresseEvenement 
                WHERE idEvenement = '".$idEvenementGroupeAdresse."'
        ";
        
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch['idAdresse'];
    }
    
    // ************************************************************************************************************************
    // recupere la liste des adresses qui sont identique au l'adresses courantes de l'evenement groupe d'adresse transmis en parametre
    // ************************************************************************************************************************
    public function getAdressesMemeLocalite($idAdresse=0,$typeLocalite='rue')
    {
        // lien retour vers l'affichage du detail de l'adresse
        $html="<a href=\"".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$idAdresse))."\">Retour</a><br>";
        
        // recuperation des adresses liees
        $reqAdresses = "
        
            SELECT ha1.idAdresse as idAdresse,ha1.idRue as idRue, ha1.idQuartier as idQuartier, ha1.idSousQuartier as idSousQuartier, ha1.idPays as idPays , ha1.idVille as idVille
            FROM historiqueAdresse ha2, historiqueAdresse ha1
            WHERE ha2.idAdresse = ha1.idAdresse
            AND ha1.idAdresse = '".$idAdresse."'
            GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
        ";
        
        $res = $this->connexionBdd->requete($reqAdresses);
        
        $rues = array();
        $quartiers = array();
        $sousQuartiers = array();
        $pays = array();
        $villes = array();
        $sql="";
        $tabIdAdressesNotToDisplay = array();
        $arrayComplementTitre=array();
        if(mysql_num_rows($res)>0)
        {
            while($fetch = mysql_fetch_assoc($res))
            {
                //$tabAdresses[] = array('idRue'=>$fetch['idRue'],'idQuartier'=>$fetch['idQuartier'],'idSousQuartier'=>$fetch['idSousQuartier'],'idPays'=>$fetch['idPays'],'idVille'=>$fetch['idVille']);
                if($typeLocalite=="rue")
                {
                    if($fetch['idRue']!='0')
                        $rues[] = $fetch['idRue'];
                    
                    $arrayComplementTitre[]="à la rue";
                }
                elseif($typeLocalite=="quartier")
                {
                    // recherche du quartier
                    if($fetch['idRue']!='0')
                    {
                        // recherche du quartier de la rue
                        $reqQuartier = "
                            SELECT q.idQuartier as idQuartier 
                            FROM quartier q
                            RIGHT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                            RIGHT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                            WHERE r.idRue = '".$fetch['idRue']."'
                        ";
                        
                        $resQuartier = $this->connexionBdd->requete($reqQuartier);
                        
                        while($fetchQuartier = mysql_fetch_assoc($resQuartier))
                        {
                            $quartiers[] = $fetchQuartier['idQuartier'];
                        }
                    }
                    
                    if($fetch['idQuartier']!='0')
                        $quartiers[] = $fetch['idQuartier'];
                        
                    $arrayComplementTitre[]="au quartier";
                }
                
                $tabIdAdressesNotToDisplay[]=$fetch['idAdresse'];
            }
            
            if(count($rues)>0)
            {
                $sql.=" AND ha1.idRue in ('".implode("','",array_unique($rues))."') ";
                
            }
                
            if(count($quartiers)>0)
            {
                $sql.=" AND q.idQuartier in ('".implode("','",array_unique($quartiers))."') ";
                
            }
            
            if(count($rues)==0 && count($quartiers)==0)
            {
                $sql.=" AND ha1.idAdresse ='0' ";
            }
            
            
            $sql.=" AND ha1.idAdresse not in ('".implode("','",$tabIdAdressesNotToDisplay)."') ";
            
        }
        
        // affichage de la liste des adresses
        $retour = $this->afficherListe(array('sqlSelectionExterne'=>$sql,'titre'=>'Adresses relatives '.implode(",",$arrayComplementTitre).': ','desactivateRedirection'=>1));
        
        $html .=$retour['html'];
        
        return $html;
    }
    
    
    public function enregistreHistoriqueNomsRues($params=array())
    {
        //﻿array(8) { ["idHistoriqueNomRue_0"]=>  string(0) "" ["annee_0"]=>  string(4) "1955" ["nomRue_0"]=>  string(5) "plop1" ["commentaire_0"]=>  string(5) "plop2" ["idHistoriqueNomRue_1"]=>  string(0) "" ["annee_1"]=>  string(4) "1956" ["nomRue_1"]=>  string(5) "plop3" ["commentaire_1"]=>  string(5) "plop4" } 
        
        $d = new dateObject();
        
        $tabAnnees = array();
        $tabNomsRues = array();
        $tabCommentaires = array();
        $tabPrefixes = array();
        foreach($this->variablesPost as $intule => $value)
        {
            $tabIntitule = explode("_",$intule);
            $indice = $tabIntitule[1];
            
            switch($tabIntitule[0])
            {
                case 'annee':
                    $tabAnnees[$indice] = $value;
                break;
                case 'nomRue':
                    $tabNomsRues[$indice] = $value;
                break;
                case 'commentaire':
                    $tabCommentaires[$indice] = $value;
                break;
                case 'prefixe':
                    $tabPrefixes[$indice] = $value;
                break;
            }
        }
        
        // on efface d'abord les valeurs précédentes
        $reqDelete = "DELETE FROM historiqueNomsRues WHERE idRue=".$this->variablesGet['idModification'];
        $resDelete = $this->connexionBdd->requete($reqDelete);
        
        // ensuite on enregistre les nouvelles valeurs
        if(count($tabAnnees)>0)
        {
            foreach($tabAnnees as $indice => $value)
            {
                $reqInsert="
                    INSERT INTO historiqueNomsRues 
                        (idRue,annee,nomRue,commentaire,prefixe)
                    VALUES
                        ('".$this->variablesGet['idModification']."',\"".$d->toBdd($d->convertYears($tabAnnees[$indice]))."\",\"".mysql_real_escape_string($tabNomsRues[$indice])."\",\"".mysql_real_escape_string($tabCommentaires[$indice])."\",\"".mysql_real_escape_string($tabPrefixes[$indice])."\")
                ";
                $resInsert=$this->connexionBdd->requete($reqInsert);
                
            }
        }
        
        
        
    }

    // ************************************************************************************************************************
    // affichage du formulaire de modification d'un element d'adresse (rue, quartier, sous quartier, ville ...)
    // ************************************************************************************************************************
    public function afficheFormulaireModificationElementAdresse($parametres=array())
    {
        // verification des droits
        $a = new archiAuthentification();
        $u = new archiUtilisateur();
        
        $html="";
        
        if($a->estAdmin() || ($u->isAuthorized('admin_rues',$a->getIdUtilisateur()) && $u->isAuthorized('admin_quartiers',$a->getIdUtilisateur()) && $u->isAuthorized('admin_sousQuartiers',$a->getIdUtilisateur())))
        {
            $htmlDependances="";
            $t = new Template('modules/archi/templates/');
            
            
            $idRue          = 0;
            $idQuartier     = 0;
            $idSousQuartier = 0;
            $idVille        = 0;
            $idPays         = 0;
                    
            
            switch($parametres['tableName'])
            {
                case 'rue':
                    
                    $t->set_filenames((array('modificationElementAdresse'=>'modificationElementAdresseRue.tpl')));
                    
                    $idRue = $parametres['id'];
                    
                    $req = "
                            SELECT r.nom as nomRue, r.prefixe as prefixeRue,sq.idSousQuartier as idSousQuartier,
                                    q.idQuartier as idQuartier, v.idVille as idVille, p.idPays as idPays
                            FROM rue r 
                            LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                            LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                            LEFT JOIN ville v ON v.idVille = q.idVille
                            LEFT JOIN pays p ON p.idPays = v.idPays
                            WHERE idRue = '".$idRue."'";
                    
                    $res = $this->connexionBdd->requete($req);
                    
                    if(mysql_num_rows($res)==1)
                    {
                        $fetch=mysql_fetch_assoc($res);
                        
                        $t->assign_vars(array(
                            'intitule'=>stripslashes($fetch['nomRue']),
                            'complement'=>stripslashes($fetch['prefixeRue']),
                            'sousQuartierField'=>$this->afficheSelectSousQuartier(array('idQuartier'=>$fetch['idQuartier'],'idSousQuartier'=>$fetch['idSousQuartier'])),
                            'quartierField'=>$this->afficheSelectQuartier(array('idVille'=>$fetch['idVille'],'idQuartier'=>$fetch['idQuartier'])),
                            'villeField'=>$this->afficheSelectVille(array('idPays'=>$fetch['idPays'],'idVille'=>$fetch['idVille'])),
                            'paysField'=>$this->afficheSelectPays(array('idPays'=>$fetch['idPays'])),
                            'idRue'=>$idRue
                        ));
                    }
                    
                    // ************************************************************************************************************************
                    // affichage des dependances liées a la rue:
                    // ************************************************************************************************************************
                    $htmlDependances.="<h3>Dependances sur les adresses affichées sur le site</h3>";

                    $arrayDependances = $this->getDependancesFrom($idRue,'idRue');
                    
                    $lienSupprRue="";
                    if($arrayDependances['nbDependances']==0)
                    {
                        $lienSupprRue = "<a href='".$this->creerUrl('supprimerRueFromAdminRue','adminElementAdresse',array('tableName'=>'rue','idRueSuppr'=>$idRue))."'>Supprimer la rue</a>";
                    }
                    
                    $htmlDependances.="nombre de dépendances = ".$arrayDependances['nbDependances']." $lienSupprRue<br>";
                    $tableau = new tableau();
                    
                    $tableau->addValue("adresses","style='font-weight:bold'");
                    $tableau->addValue("nombre d'evenements associés","style='font-weight:bold'");
                    
                    foreach($arrayDependances['arrayDependances'] as $indice => $value)
                    {
                        $lienSupprAdresse = "";
                        if($value['nbEvenementsAssocies']==0)
                        {
                            $lienSupprAdresse = "&nbsp;&nbsp;&nbsp;<a href='".$this->creerUrl('supprimerAdresseFromAdminRue','adminAdresseDetail',array('tableName'=>'rue','idModification'=>$idRue,'idAdresseSuppr'=>$value['idAdresse']))."'>Supprimer l'adresse</a>";
                        }
                    
                    
                        $tableau->addValue("<a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$value['idAdresse']))."'>".$this->getIntituleAdresseFrom($value['idAdresse'],'idAdresse')."</a>");
                        $tableau->addValue($value['nbEvenementsAssocies'].$lienSupprAdresse);
                    }
                    
                    $htmlDependances .=$tableau->createHtmlTableFromArray(2);
                    
                break;
                
                case 'sousQuartier':
                
                    $t->set_filenames((array('modificationElementAdresse'=>'modificationElementAdresseSousQuartier.tpl')));
                
                    $idSousQuartier = $parametres['id'];
                    
                    $req = "
                        SELECT sq.nom as nomSousQuartier, sq.idSousQuartier as idSousQuartier,
                                    q.idQuartier as idQuartier, v.idVille as idVille, p.idPays as idPays
                        FROM sousQuartier sq
                        LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                        LEFT JOIN ville v ON v.idVille = q.idVille
                        LEFT JOIN pays p ON p.idPays = v.idPays
                        WHERE sq.idSousQuartier= '".$idSousQuartier."'";
                    $res = $this->connexionBdd->requete($req);
                    
                    if(mysql_num_rows($res)==1)
                    {
                        $fetch=mysql_fetch_assoc($res);
                        
                        $t->assign_vars(array(
                            'intitule'=>stripslashes($fetch['nomSousQuartier']),
                            'quartierField'=>$this->afficheSelectQuartier(array('idVille'=>$fetch['idVille'],'idQuartier'=>$fetch['idQuartier'],'noSousQuartier'=>true)),
                            'villeField'=>$this->afficheSelectVille(array('idPays'=>$fetch['idPays'],'idVille'=>$fetch['idVille'],'noSousQuartier'=>true)),
                            'paysField'=>$this->afficheSelectPays(array('idPays'=>$fetch['idPays'],'noSousQuartier'=>true)),
                            'idSousQuartier'=>$idSousQuartier
                        ));
                    }
                    
                    
                    // ************************************************************************************************************************
                    // affichage des dependances liées au sous quartier
                    // ************************************************************************************************************************
                    $htmlDependances.="<h3>Dependances sur les sous quartiers</h3>";

                    $arrayDependances = $this->getDependancesFrom($idSousQuartier,'idSousQuartier');
                    $htmlDependances.="nombre de dépendances = ".$arrayDependances['nbDependances']."<br>";
                    $tableauAdresses = new tableau();
                    $tableauRues = new tableau();
                    
                    
                    $tableauAdresses->addValue("adresses","style='font-weight:bold'");
                    $tableauAdresses->addValue("nombre d'evenements associés","style='font-weight:bold'");
                    
                    $tableauRues->addValue("rues dépendantes de ce sous quartier","style='font-weight:bold'");
                    
                    foreach($arrayDependances['arrayDependances'] as $indice => $value)
                    {
                        
                        if(isset($value['idAdresse']))
                        {
                            $tableauAdresses->addValue("<a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$value['idAdresse']))."'>".$this->getIntituleAdresseFrom($value['idAdresse'],'idAdresse')."</a>");
                            $tableauAdresses->addValue($value['nbEvenementsAssocies']);
                        }
                            
                        
                        
                        if(isset($value['idRue']))
                        {
                            $intituleRue = $this->getAdresseComplete($value['idRue'],'rue',array('miseEnForme'=>true));
                            $tableauRues->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'rue','idModification'=>$value['idRue']))."'>".$intituleRue."</a>");
                        }
                        
                        
                    }
                    
                    $htmlDependances .=$tableauAdresses->createHtmlTableFromArray(2);
                    $htmlDependances .=$tableauRues->createHtmlTableFromArray(1);
                    
                break;
                case 'quartier':
                    
                    $t->set_filenames((array('modificationElementAdresse'=>'modificationElementAdresseQuartier.tpl')));
                
                    $idQuartier = $parametres['id'];
                    
                    $req = "
                            SELECT q.nom as nomQuartier,v.idVille as idVille, p.idPays as idPays
                            FROM quartier q
                            LEFT JOIN ville v ON v.idVille = q.idVille
                            LEFT JOIN pays p ON p.idPays = v.idPays
                            WHERE q.idQuartier = '".$idQuartier."'
                    ";
                    $res = $this->connexionBdd->requete($req);
                    
                    
                    if(mysql_num_rows($res)==1)
                    {
                        $fetch=mysql_fetch_assoc($res);
                        
                        $t->assign_vars(array(
                            'intitule'=>stripslashes($fetch['nomQuartier']),
                            'villeField'=>$this->afficheSelectVille(array('idPays'=>$fetch['idPays'],'idVille'=>$fetch['idVille'],'noSousQuartier'=>true,'noQuartier'=>true)),
                            'paysField'=>$this->afficheSelectPays(array('idPays'=>$fetch['idPays'],'noSousQuartier'=>true,'noQuartier'=>true)),
                            'idQuartier'=>$idQuartier
                        ));
                    }
                    
                    
                    
                    // ************************************************************************************************************************
                    // affichage des dependances liées au  quartier
                    // ************************************************************************************************************************
                    $htmlDependances.="<h3>Dependances sur quartiers</h3>";

                    $arrayDependances = $this->getDependancesFrom($idQuartier,'idQuartier');
                    $htmlDependances.="nombre de dépendances = ".$arrayDependances['nbDependances']."<br>";
                    $tableauAdresses = new tableau();
                    $tableauRues = new tableau();
                    $tableauSousQuartiers = new tableau();
                    
                    $tableauAdresses->addValue("adresses","style='font-weight:bold'");
                    $tableauAdresses->addValue("nombre d'evenements associés","style='font-weight:bold'");
                    
                    $tableauRues->addValue("rues dépendantes de ce quartier","style='font-weight:bold'");
                    
                    $tableauSousQuartiers->addValue("sous quartiers dépendants de ce quartier","style='font-weight:bold'");
                    
                    foreach($arrayDependances['arrayDependances'] as $indice => $value)
                    {
                        
                        if(isset($value['idAdresse']))
                        {
                            $tableauAdresses->addValue("<a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$value['idAdresse']))."'>".$this->getIntituleAdresseFrom($value['idAdresse'],'idAdresse')."</a>");
                            $tableauAdresses->addValue($value['nbEvenementsAssocies']);
                        }
                            
                        
                        
                        if(isset($value['idRue']))
                        {
                            $intituleRue = $this->getAdresseComplete($value['idRue'],'rue',array('miseEnForme'=>true));
                            $tableauRues->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'rue','idModification'=>$value['idRue']))."'>".$intituleRue."</a>");
                        }
                        
                        
                        if(isset($value['idSousQuartier']))
                        {
                            $intituleSousQuartier = $this->getAdresseComplete($value['idSousQuartier'],'sousQuartier',array('miseEnForme'=>true));
                            $tableauSousQuartiers->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'sousQuartier','idModification'=>$value['idSousQuartier']))."'>".$intituleSousQuartier."</a>");
                        }
                        
                        
                    }

                    $htmlDependances .=$tableauAdresses->createHtmlTableFromArray(2);
                    $htmlDependances .=$tableauRues->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauSousQuartiers->createHtmlTableFromArray(1);
                    
                    
                    
                break;
                case 'ville':
                    $googleMap = new googleMap(array('googleMapKey'=>$this->googleMapKey));
                    
                    $jsGoogleMap="";
                    $jsGoogleMap.= $googleMap->getJsFunctions();
                    $jsGoogleMap.= $googleMap->getJSInitGeoCoder();
            
                    $jsToExecute="document.getElementById('formModif').action='".$this->creerUrl('adminEnregistreModifAdresse','',$parametres)."';document.getElementById('formModif').submit();";
                        
                    $arrayJsCoordonneesFromGoogleMap = $googleMap->getJSRetriveCoordonnees(array(
                        'nomChampLatitudeRetour'=>'latitude',
                        'nomChampLongitudeRetour'=>'longitude',
                        'getAdresseFromElementById'=>true,
                        'jsAdresseValue'=>"document.getElementById('intituleVille').value+' '+document.getElementById('pays').options[document.getElementById('pays').selectedIndex].innerHTML",
                        'jsToExecuteIfOK'=>$jsToExecute,
                        'jsToExecuteIfNoAddressFound'=>"document.getElementById('latitude').value='';document.getElementById('longitude').value='';".$jsToExecute
                    ));

                    $jsGoogleMap.= $arrayJsCoordonneesFromGoogleMap['jsFunctionToExecute'];
                    
                    
                    
                    $t->set_filenames((array('modificationElementAdresse'=>'modificationElementAdresseVille.tpl')));
                    $idVille = $parametres['id'];
                    
                    $req = "
                            SELECT v.nom as nomVille,v.codepostal as codePostal, p.idPays as idPays,v.longitude as longitude, v.latitude as latitude
                            FROM ville v
                            LEFT JOIN pays p ON p.idPays = v.idPays
                            WHERE v.idVille = '".$idVille."'
                    ";
                    $res = $this->connexionBdd->requete($req);
                    
                    if(mysql_num_rows($res)==1)
                    {
                        $fetch=mysql_fetch_assoc($res);
                        
                        $t->assign_vars(array(
                            'jsGoogleMap'=>$jsGoogleMap,
                            'intitule'=>stripslashes($fetch['nomVille']),
                            'codePostal' => $fetch['codePostal'],
                            'paysField'=>$this->afficheSelectPays(array('idPays'=>$fetch['idPays'],'noSousQuartier'=>true,'noQuartier'=>true,'noVille'=>true)),
                            'latitude'=>$fetch['latitude'],
                            'longitude'=>$fetch['longitude'],
                            'idVille'=>$idVille
                        ));
                    }
                    
                    
                    
                    // ************************************************************************************************************************
                    // affichage des dependances liées a la ville
                    // ************************************************************************************************************************
                    $htmlDependances.="<h3>Dependances sur la ville</h3>";

                    $arrayDependances = $this->getDependancesFrom($idVille,'idVille');
                    $htmlDependances.="nombre de dépendances = ".$arrayDependances['nbDependances']."<br>";
                    $tableauAdresses = new tableau();
                    $tableauRues = new tableau();
                    $tableauSousQuartiers = new tableau();
                    $tableauQuartiers = new tableau();
                    
                    $tableauAdresses->addValue("adresses","style='font-weight:bold'");
                    $tableauAdresses->addValue("nombre d'evenements associés","style='font-weight:bold'");
                    
                    $tableauRues->addValue("rues dépendantes de cette ville","style='font-weight:bold'");
                    
                    $tableauSousQuartiers->addValue("sous quartiers dépendants de cette ville","style='font-weight:bold'");
                    
                    $tableauQuartiers->addValue("quartiers dépendants de cette ville","style='font-weight:bold'");
                    
                    foreach($arrayDependances['arrayDependances'] as $indice => $value)
                    {
                        
                        if(isset($value['idAdresse']))
                        {
                            $tableauAdresses->addValue("<a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$value['idAdresse']))."'>".$this->getIntituleAdresseFrom($value['idAdresse'],'idAdresse')."</a>");
                            $tableauAdresses->addValue($value['nbEvenementsAssocies']);
                        }
                            
                        
                        
                        if(isset($value['idRue']))
                        {
                            $intituleRue = $this->getAdresseComplete($value['idRue'],'rue',array('miseEnForme'=>true));
                            $tableauRues->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'rue','idModification'=>$value['idRue']))."'>".$intituleRue."</a>");
                        }
                        
                        
                        if(isset($value['idSousQuartier']))
                        {
                            $intituleSousQuartier = $this->getAdresseComplete($value['idSousQuartier'],'sousQuartier',array('miseEnForme'=>true));
                            $tableauSousQuartiers->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'sousQuartier','idModification'=>$value['idSousQuartier']))."'>".$intituleSousQuartier."</a>");
                        }
                        
                        if(isset($value['idQuartier']))
                        {
                            $intituleQuartier = $this->getAdresseComplete($value['idQuartier'],'quartier',array('miseEnForme'=>true));
                            $tableauQuartiers->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'quartier','idModification'=>$value['idQuartier']))."'>".$intituleQuartier."</a>");
                            
                        }
                        
                        
                    }

                    $htmlDependances .=$tableauAdresses->createHtmlTableFromArray(2);
                    $htmlDependances .=$tableauRues->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauSousQuartiers->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauQuartiers->createHtmlTableFromArray(1);
                    
                    
                    
                break;  

                case 'pays':
                    $t->set_filenames((array('modificationElementAdresse'=>'modificationElementAdressePays.tpl')));
                    $idPays = $parametres['id'];
                    
                    $req = "
                            SELECT p.nom as nomPays
                            FROM pays p
                            WHERE p.idPays = '".$idPays."'
                    ";
                    $res = $this->connexionBdd->requete($req);
                    
                    if(mysql_num_rows($res)==1)
                    {
                        $fetch=mysql_fetch_assoc($res);
                        
                        $t->assign_vars(array(
                            'intitule'=>stripslashes($fetch['nomPays']),
                            'idPays'=>$idPays
                        ));
                    }
                    
                    
                    
                    
                    // ************************************************************************************************************************
                    // affichage des dependances liées a la ville
                    // ************************************************************************************************************************
                    $htmlDependances.="<h3>Dependances sur le pays</h3>";

                    $arrayDependances = $this->getDependancesFrom($idPays,'idPays');
                    $htmlDependances.="nombre de dépendances = ".$arrayDependances['nbDependances']."<br>";
                    $tableauAdresses = new tableau();
                    $tableauRues = new tableau();
                    $tableauSousQuartiers = new tableau();
                    $tableauQuartiers = new tableau();
                    $tableauVilles = new tableau();
                    
                    $tableauAdresses->addValue("adresses","style='font-weight:bold'");
                    $tableauAdresses->addValue("nombre d'evenements associés","style='font-weight:bold'");
                    
                    $tableauRues->addValue("rues dépendantes de ce pays","style='font-weight:bold'");
                    
                    $tableauSousQuartiers->addValue("sous quartiers dépendants de ce pays","style='font-weight:bold'");
                    
                    $tableauQuartiers->addValue("quartiers dépendants de ce pays","style='font-weight:bold'");
                    
                    $tableauVilles->addValue("villes dépendantes de ce pays","style='font-weight:bold'");
                    
                    foreach($arrayDependances['arrayDependances'] as $indice => $value)
                    {
                        
                        if(isset($value['idAdresse']))
                        {
                            $tableauAdresses->addValue("<a href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$value['idAdresse']))."'>".$this->getIntituleAdresseFrom($value['idAdresse'],'idAdresse')."</a>");

                            $tableauAdresses->addValue($value['nbEvenementsAssocies']);
                        }
                            
                        
                        
                        if(isset($value['idRue']))
                        {
                            $intituleRue = $this->getAdresseComplete($value['idRue'],'rue',array('miseEnForme'=>true));
                            $tableauRues->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'rue','idModification'=>$value['idRue']))."'>".$intituleRue."</a>");
                        }
                        
                        
                        if(isset($value['idSousQuartier']))
                        {
                            $intituleSousQuartier = $this->getAdresseComplete($value['idSousQuartier'],'sousQuartier',array('miseEnForme'=>true));
                            $tableauSousQuartiers->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'sousQuartier','idModification'=>$value['idSousQuartier']))."'>".$intituleSousQuartier."</a>");
                        }
                        
                        if(isset($value['idQuartier']))
                        {
                            $intituleQuartier = $this->getAdresseComplete($value['idQuartier'],'quartier',array('miseEnForme'=>true));
                            $tableauQuartiers->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'quartier','idModification'=>$value['idQuartier']))."'>".$intituleQuartier."</a>");
                            
                        }
                        
                        if(isset($value['idVille']))
                        {
                            $intituleVille = $this->getAdresseComplete($value['idVille'],'ville',array('miseEnForme'=>true));
                            $tableauVilles->addValue("<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>'ville','idModification'=>$value['idVille']))."'>".$intituleVille."</a>");
                        }
                    }

                    $htmlDependances .=$tableauAdresses->createHtmlTableFromArray(2);
                    $htmlDependances .=$tableauRues->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauSousQuartiers->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauQuartiers->createHtmlTableFromArray(1);
                    $htmlDependances .=$tableauVilles->createHtmlTableFromArray(1);
                    
                break;
            }


            
            switch($parametres['tableName'])
            {
                case 'ville':
                    // on adapte le bouton modifier pour que la recherche googleMap des coordonnees se lance a la validation du formulaire
                    $typeButtonModifier="button";
                    $onClickButtonModifier = $arrayJsCoordonneesFromGoogleMap['jsFunctionCall'];
                break;
                default:
                    $typeButtonModifier = "submit";
                    $onClickButtonModifier = "document.getElementById('formModif').action='".$this->creerUrl('adminEnregistreModifAdresse','',$parametres)."';";
                break;
            }
            
            
            $t->assign_vars(array(
                        'typeElement'               =>$parametres['tableName'],
                        'typeButtonModifier'        =>$typeButtonModifier,
                        'onClickBoutonModifier'     =>$onClickButtonModifier,
                        'onClickBoutonRetour'       =>"location.href='".$this->creerUrl('','adminElementAdresse',array('tableName'=>$parametres['tableName']))."';"
                        ));
            
            ob_start();
            $t->pparse('modificationElementAdresse');
            $html .= ob_get_contents();
            $html.="<br><br><h2>Dependances</h2>";
            $html.="(concernant les adresses, deux evenements différents peuvent afficher la meme adresse , dans ce cas l'adresse est liée a deux groupes d'adresses différents.<br><br>";
            $html.= $htmlDependances;
            ob_end_clean();
            
            
            // dans le cas de la rue , on affiche un onglet pour l'historique des noms de rue
            if($parametres['tableName']=='rue')
            {
                $d = new dateObject();
                $reqVals = "SELECT * FROM historiqueNomsRues WHERE idRue = '".$parametres['id']."'";
                $resVals = $this->connexionBdd->requete($reqVals);
                
                $valsIdHistoriques = array();
                $valsIdRues = array();
                $valsAnnees = array();
                $valsNomsRues = array();
                $valsCommentaires = array();
                $valsPrefixes = array();
                $i=0;
                while($fetchVals = mysql_fetch_assoc($resVals))
                {
                    $valsIdHistoriques[$i]  = $fetchVals['idHistoriqueNomRue'];
                    $valsIdRues[$i]         = $fetchVals['idRue'];
                    $valsAnnees[$i]         = $d->toFrench($fetchVals['annee']);
                    $valsNomsRues[$i]       = $fetchVals['nomRue'];
                    $valsCommentaires[$i]   = $fetchVals['commentaire'];
                    $valPrefixes[$i]        = $fetchVals['prefixe'];
                    $i++;
                }
                
                if($i==0)
                {
                    $valsIdHistoriques[$i]  = '';
                    $valsIdRues[$i]         = '';
                    $valsAnnees[$i]         = '';
                    $valsNomsRues[$i]       = '';
                    $valsCommentaires[$i]   = '';
                    $valPrefixes[$i]        = '';
                }
            
            
                $configForm=array(
                        "idHistoriqueNomRue"=>array("type"=>"hidden","libelle"=>"idHistoriqueNomRue","defaultValues"=>$valsIdHistoriques,"error"=>"","default"=>""),
                        "annee"=>array("type"=>"date","libelle"=>"annee","defaultValues"=>$valsAnnees,"error"=>"","default"=>""),
                        "prefixe"=>array("type"=>"text","libelle"=>"prefixe","defaultValues"=>$valPrefixes,"error"=>"","default"=>""),
                        "nomRue"=>array("type"=>"text","libelle"=>"nomRue","defaultValues"=>$valsNomsRues,"error"=>"","default"=>""),
                        "commentaire"=>array("type"=>"bigText","libelle"=>"commentaire","defaultValues"=>$valsCommentaires,"error"=>"","default"=>"")                   
                );
                
                
                $f = new formGenerator();
                
                $arrayFormulaireMultiLignes = $f->getFormulaireMultiLignes(array("configForm"=>$configForm,"formAction"=>$this->creerUrl('enregistreHistoriqueNomsRues','adminAdresseDetail',array("tableName"=>'rue',"idModification"=>$parametres['id']))));
                
                $htmlHistoriqueNomRue=$arrayFormulaireMultiLignes['html'];
                
            
                $onglets = new ongletObject();
                $onglets->init(0);
                $onglets->setStyleTable("style='margin:0px;padding:0px;'");
                $onglets->setStyleTableEtiquettes("style='margin:0px;padding:0px;'");
                $onglets->addContent("Modifier un élément de rue ",$html);
                $onglets->addContent("Historique du nom de la rue",$htmlHistoriqueNomRue);
                
                $html=$onglets->getHTML();
                
                $html.=$arrayFormulaireMultiLignes['jsAfterMultiLigneArray'];
                
            }
        
        }
        else
        {
            $erreurObj = new objetErreur();
            $erreurObj->ajouter("Vous n'avez pas les droits pour accéder à cette partie du site");
            echo $erreurObj->afficher();
        }
        
        return $html;
    }
    
    
    // enregistrer les modification d'une adresse (rue quartier sous quartier ... ) dans la partie administration du site
    public function enregistreModificationAdresse()
    {
        $errors = array();
        if(isset($this->variablesGet['tableName']) && $this->variablesGet['tableName']!='')
        {
            switch($this->variablesGet['tableName'])
            {
                // **********************************************************************************************************************************************************
                // rue
                // **********************************************************************************************************************************************************
                case 'rue':
                    $req="";
                    if(isset($this->variablesPost['sousQuartiers']) && $this->variablesPost['sousQuartiers']!='0')
                    {
                        $req = "UPDATE rue SET idSousQuartier='".$this->variablesPost['sousQuartiers']."', nom=\"".$this->variablesPost['intitule']."\", prefixe=\"".$this->variablesPost['complement']."\" WHERE idRue = '".$this->variablesPost['idRue']."'";
                        
                        $idModifie = $this->variablesPost['idRue'];     
                        
                    }
                    elseif(isset($this->variablesPost['quartiers']) && $this->variablesPost['quartiers']!='0')
                    {
                        // recherche du sous quartier 'autre' , s'il n'existe pas , on le cree
                        $reqSousQuartierAutre = "
                                    SELECT idSousQuartier 
                                    FROM sousQuartier 
                                    WHERE idQuartier = '".$this->variablesPost['quartiers']."' 
                                    AND nom='autre'
                                    ";
                        
                        $resSousQuartierAutre = $this->connexionBdd->requete($reqSousQuartierAutre);
                        $idSousQuartierAutre = 0;
                        if(mysql_num_rows($resSousQuartierAutre)==1)
                        {
                            // le sousQuartier autre existe , on recupere son ID
                            $fetchSousQuartierAutre = mysql_fetch_assoc($resSousQuartierAutre);
                            $idSousQuartierAutre = $fetchSousQuartierAutre['idSousQuartier'];
                        }
                        
                        if(mysql_num_rows($resSousQuartierAutre)>1)
                        {
                            $errors[]="Probleme detecté dans les sousQuartiers 'autre', merci de contacter l'administrateur.<br>";
                        }
                        
                        if(mysql_num_rows($resSousQuartierAutre)==0)
                        {
                            // le sousQuartier 'autre' pour le quartier n'existe pas , il faut le creer et renvoyer son ID
                            $reqInsertNouveauSousQuartierAutre = "INSERT INTO sousQuartier (idQuartier,nom) VALUES ('".$this->variablesPost['quartiers']."',\"autre\")";
                            $resInsertNouveauSousQuartierAutre = $this->connexionBdd->requete($reqInsertNouveauSousQuartierAutre);
                            $idSousQuartierAutre = mysql_insert_id();
                        }
                        
                        if($idSousQuartierAutre!=0)
                        {
                        
                            $req = "UPDATE rue SET idSousQuartier='".$idSousQuartierAutre."', nom=\"".$this->variablesPost['intitule']."\", prefixe=\"".$this->variablesPost['complement']."\" WHERE idRue = '".$this->variablesPost['idRue']."'";
                            $idModifie = $this->variablesPost['idRue'];
                        }
                        else
                        {
                            $errors[] = "Il y a eu un souci a la selection automatique du sous quartier, merci de contacter l'administrateur.<br>";
                        }
                    }
                    elseif(isset($this->variablesPost['ville']) && $this->variablesPost['ville']!='0')
                    {
                        // l'adresse est une ville , ceci ne sera pas beaucoup utilisé car en principe une adresse est au moins une ville et un quartier , mais il a des adresses qui ne sont que des villes quand meme
                        // recherche du quartier 'autre' de la ville s'il n'existe pas , on le cree
                        $reqQuartierAutre = "SELECT idQuartier FROM quartier WHERE idVille='".$this->variablesPost['ville']."' and nom='autre'";
                        $resQuartierAutre = $this->connexionBdd->requete($reqQuartierAutre);
                        
                        $idQuartierAutre = 0;
                        if(mysql_num_rows($resQuartierAutre)==1)
                        {
                            // le quartier autre existe, on passe au sousQuartier
                            $fetchQuartierAutre = mysql_fetch_assoc($resQuartierAutre);
                            $idQuartierAutre = $fetchQuartierAutre['idQuartier'];
                            
                            // on verifie que le sousQuartiers 'autre' existe pour le quartier 'autre' trouve
                            
                            // recherche du sous quartier 'autre' , s'il n'existe pas , on le cree
                            $reqSousQuartierAutre = "
                                        SELECT idSousQuartier 
                                        FROM sousQuartier 
                                        WHERE idQuartier = '".$idQuartierAutre."' 
                                        AND nom='autre'
                                        ";
                            
                            $resSousQuartierAutre = $this->connexionBdd->requete($reqSousQuartierAutre);
                            $idSousQuartierAutre = 0;
                            if(mysql_num_rows($resSousQuartierAutre)==1)
                            {
                                // le sousQuartier autre existe , on recupere son ID
                                $fetchSousQuartierAutre = mysql_fetch_assoc($resSousQuartierAutre);
                                $idSousQuartierAutre = $fetchSousQuartierAutre['idSousQuartier'];
                            }
                            
                            if(mysql_num_rows($resSousQuartierAutre)>1)
                            {
                                $errors[] = "Probleme detecté dans les sousQuartiers 'autre', merci de contacter l'administrateur.<br>";
                            }
                            
                            if(mysql_num_rows($resSousQuartierAutre)==0)
                            {
                                // le sousQuartier 'autre' pour le quartier n'existe pas , il faut le creer et renvoyer son ID
                                $reqInsertNouveauSousQuartierAutre = "INSERT INTO sousQuartier (idQuartier,nom) VALUES ('".$idQuartierAutre."',\"autre\")";
                                $resInsertNouveauSousQuartierAutre = $this->connexionBdd->requete($reqInsertNouveauSousQuartierAutre);
                                $idSousQuartierAutre = mysql_insert_id();
                            }
                            
                            if($idSousQuartierAutre!=0)
                            {
                            
                                $req = "UPDATE rue SET idSousQuartier='".$idSousQuartierAutre."', nom=\"".$this->variablesPost['intitule']."\", prefixe=\"".$this->variablesPost['complement']."\" WHERE idRue = '".$this->variablesPost['idRue']."'";
                            }
                            else
                            {
                                $errors[] = "Il y a eu un souci a la selection automatique du sous quartier, merci de contacter l'administrateur.<br>";
                            }
                            
                            if(mysql_num_rows($resQuartierAutre)>1)
                            {
                                $errors[] =  "Probleme detecté dans les quartiers 'autre', merci de contacter l'administrateur.<br>";
                            }
                        }
                        if(mysql_num_rows($resQuartierAutre)==0)
                        {
                            // le quartier autre n'existe pas , il faut le cree
                            $reqInsertQuartierAutre = "INSERT INTO quartier (idVille,nom) VALUES ('".$this->variablesPost['ville']."','autre')";
                            $resInsertQuartierAutre = $this->connexionBdd->requete($reqInsertQuartierAutre);
                            $idQuartierAutre = mysql_insert_id();
                            
                            // on ajoute aussi le sousQuartier 'autre' pour le quartier 'autre' cree , on va quand meme regarder s'il n'existe pas un enregistrement pour etre sur qu'il n'y a pas de souci d'integrité
                                                        // recherche du sous quartier 'autre' , s'il n'existe pas , on le cree
                            $reqSousQuartierAutre = "
                                        SELECT idSousQuartier 
                                        FROM sousQuartier 
                                        WHERE idQuartier = '".$idQuartierAutre."' 
                                        AND nom='autre'
                                        ";
                            
                            $resSousQuartierAutre = $this->connexionBdd->requete($reqSousQuartierAutre);
                            $idSousQuartierAutre = 0;
                            if(mysql_num_rows($resSousQuartierAutre)==1)
                            {
                                // le sousQuartier autre existe , on recupere son ID
                                $fetchSousQuartierAutre = mysql_fetch_assoc($resSousQuartierAutre);
                                $idSousQuartierAutre = $fetchSousQuartierAutre['idSousQuartier'];
                            }
                            
                            if(mysql_num_rows($resSousQuartierAutre)>1)
                            {
                                $errors[] =  "Probleme detecté dans les sousQuartiers 'autre', merci de contacter l'administrateur.<br>";
                            }
                            
                            if(mysql_num_rows($resSousQuartierAutre)==0)
                            {
                                // le sousQuartier 'autre' pour le quartier n'existe pas , il faut le creer et renvoyer son ID
                                $reqInsertNouveauSousQuartierAutre = "INSERT INTO sousQuartier (idQuartier,nom) VALUES ('".$idQuartierAutre."',\"autre\")";
                                $resInsertNouveauSousQuartierAutre = $this->connexionBdd->requete($reqInsertNouveauSousQuartierAutre);
                                $idSousQuartierAutre = mysql_insert_id();
                            }
                            
                            if($idSousQuartierAutre!=0)
                            {
                            
                                $req = "UPDATE rue SET idSousQuartier='".$idSousQuartierAutre."', nom=\"".$this->variablesPost['intitule']."\", prefixe=\"".$this->variablesPost['complement']."\" WHERE idRue = '".$this->variablesPost['idRue']."'";
                            }
                            else
                            {
                                $errors[] =  "Il y a eu un souci a la selection automatique du sous quartier, merci de contacter l'administrateur.<br>";
                            }
                            
                            if(mysql_num_rows($resQuartierAutre)>1)
                            {
                                $errors[] =  "Probleme detecté dans les quartiers 'autre', merci de contacter l'administrateur.<br>";
                            }
                            
                        }
                        
                    }
                    
                    
                    if($req!="")
                    {
                        // execution de la requete de mise a jour de la rue
                        $res = $this->connexionBdd->requete($req);
                        $idModifie = $this->variablesPost['idRue'];
                        
                    }
                    else
                    {
                        $errors[] =  "erreur dans la modification de la rue<br>";
                    }
                    
                break;
                // **********************************************************************************************************************************************************
                // sous quartier
                // **********************************************************************************************************************************************************
                case 'sousQuartier':
                    // un sousQuartier appartient toujours a un quartier
                    
                    //﻿array(8) { ["idRue"]=>  string(1) "5" ["pays"]=>  string(1) "1" ["ville"]=>  string(1) "1" ["quartiers"]=>  string(2) "13" ["sousQuartiers"]=>  string(1) "8" ["intitule"]=>  string(13) "Saint-Nicolas" ["complement"]=>  string(4) "quai" ["modifier"]=>  string(8) "Modifier" }
                    $req="";
                    if($this->variablesPost['quartiers']!='0' && $this->variablesPost['ville']!='0' && $this->variablesPost['pays']!='0')
                    {
                        $req = "UPDATE sousQuartier SET idQuartier = '".$this->variablesPost['quartiers']."',nom=\"".$this->variablesPost['intitule']."\" WHERE idSousQuartier = '".$this->variablesPost['idSousQuartier']."'";
                        
                    }
                    else
                    {
                        $errors[] =  "Erreur : un sous quartier ne peut pas appartenir a aucun quartier.<br>";
                    }
                
                    if($req!="")
                    {
                        // execution de la requete de mise a jour de la rue
                        $res = $this->connexionBdd->requete($req);
                        $idModifie = $this->variablesPost['idSousQuartier'];
                    }
                    else
                    {
                        $errors[] =  "erreur dans la modification du sous quartier<br>";
                    }
                break;
                // **********************************************************************************************************************************************************
                // quartier
                // **********************************************************************************************************************************************************
                case 'quartier':
                    // un quartier appartient toujours a une ville
                    
                    //﻿array(8) { ["idRue"]=>  string(1) "5" ["pays"]=>  string(1) "1" ["ville"]=>  string(1) "1" ["quartiers"]=>  string(2) "13" ["sousQuartiers"]=>  string(1) "8" ["intitule"]=>  string(13) "Saint-Nicolas" ["complement"]=>  string(4) "quai" ["modifier"]=>  string(8) "Modifier" }
                    $req="";
                    if($this->variablesPost['ville']!='0' && $this->variablesPost['pays']!='0')
                    {
                        $req = "UPDATE quartier SET idVille = '".$this->variablesPost['ville']."',nom=\"".$this->variablesPost['intitule']."\" WHERE idQuartier = '".$this->variablesPost['idQuartier']."'";
                        
                    }
                    else
                    {
                        $errors[] =  "Erreur : un quartier ne peut pas appartenir a aucune ville.<br>";
                    }
                
                    if($req!="")
                    {
                        // execution de la requete de mise a jour de du quartier
                        $res = $this->connexionBdd->requete($req);
                        $idModifie = $this->variablesPost['idQuartier'];
                    }
                    else
                    {
                        $errors[] =  "erreur dans la modification du quartier<br>";
                    }
                break;
                // **********************************************************************************************************************************************************
                // ville
                // **********************************************************************************************************************************************************
                case 'ville':
                    // une ville appartient toujours a un pays
                    
                    //﻿array(8) { ["idRue"]=>  string(1) "5" ["pays"]=>  string(1) "1" ["ville"]=>  string(1) "1" ["quartiers"]=>  string(2) "13" ["sousQuartiers"]=>  string(1) "8" ["intitule"]=>  string(13) "Saint-Nicolas" ["complement"]=>  string(4) "quai" ["modifier"]=>  string(8) "Modifier" }
                    $req="";
                    if($this->variablesPost['pays']!='0')
                    {
                        $req = "UPDATE ville SET idPays = '".$this->variablesPost['pays']."',nom=\"".$this->variablesPost['intitule']."\",codepostal=\"".$this->variablesPost['codePostal']."\", longitude='".$this->variablesPost['longitude']."',latitude='".$this->variablesPost['latitude']."' WHERE idVille = '".$this->variablesPost['idVille']."'";
                        
                    }
                    else
                    {
                        $errors[] =  "Erreur : une ville ne peut pas appartenir a aucun pays.<br>";
                    }
                
                    if($req!="")
                    {
                        // execution de la requete de mise a jour de la ville
                        $res = $this->connexionBdd->requete($req);
                        $idModifie = $this->variablesPost['idVille'];
                    }
                    else
                    {
                        $errors[] =  "erreur dans la modification de la ville<br>";
                    }
                break;
                // **********************************************************************************************************************************************************
                // pays
                // **********************************************************************************************************************************************************
                case 'pays':
                    $req = "UPDATE pays SET nom=\"".$this->variablesPost['intitule']."\" WHERE idPays = '".$this->variablesPost['idPays']."'";
                    $res = $this->connexionBdd->requete($req);
                    $idModifie = $this->variablesPost['idPays'];
                break;
                
            }
            
            if(isset($this->variablesGet['tableName']) && isset($req) && $req!='')
            {
            
                $mail = new mailObject();
                $a = new archiAuthentification();
                $u = new archiUtilisateur();
                $arrayInfosUtilisateur = $u->getArrayInfosFromUtilisateur($a->getIdUtilisateur());
                
                $messageAdmins = "Un élément d'adresse a été modifié";
                $messageAdmins .=" par : ".$arrayInfosUtilisateur['nom']." ".$arrayInfosUtilisateur['prenom']."<br>";
                
                $messageAdmins .= "type d'élément : ".$this->variablesGet['tableName']."<br><br>";
                $messageAdmins.="<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$this->variablesGet['tableName'],'idModification'=>$idModifie))."'>".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$this->variablesGet['tableName'],'idModification'=>$idModifie))."</a>";
                
                $messageModerateur ="Un élément d'adresse a été modifié<br>";
                $messageModerateur .="type d'élément : ".$this->variablesGet['tableName']."<br><br>";
                $messageModerateur.="<a href='".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$this->variablesGet['tableName'],'idModification'=>$idModifie))."'>".$this->creerUrl('','adminAdresseDetail',array('tableName'=>$this->variablesGet['tableName'],'idModification'=>$idModifie))."</a>";
                
                
                $mail->sendMailToAdministrators($mail->getSiteMail(),"archi-strasbourg.org : un utilisateur modifié un élément d'adresse",$messageAdmins," and alerteMail='1' ",true);
                
                $u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$messageAdmins,'idTypeMailRegroupement'=>6,'criteres'=>" and alerteMail='1' "));
                
                
                $tableName = $this->variablesGet['tableName'];
                // envoi de mail aux moderateur de la ville
                /*if(in_array($tableName,array('rue','sousQuartier','quartier')))
                {
                    $identifiantName='';
                    switch($tableName)
                    {
                        case 'rue':
                            $identifiantName='idRue';
                        break;
                        case 'sousQuartier':
                            $identifiantName='idSousQuartier';
                        break;
                        case 'quartier':
                            $identifiantName='idQuartier';
                        break;
                    }
                
                    $idVille = $this->getIdVilleFrom($idModifie,$identifiantName);
                    $arrayListeModerateurs = $u->getArrayIdModerateursActifsFromVille($idVille,array("sqlWhere"=>" AND alerteAdresses='1' "));
                    if(count($arrayListeModerateurs)>0)
                    {
                        foreach($arrayListeModerateurs as $indice => $idModerateur)
                        {
                            if($idModerateur!=$a->getIdUtilisateur() && $u->isAuthorized('admin_rues',$idModerateur) && $u->isAuthorized('admin_sousQuartiers',$idModerateur) && $u->isAuthorized('admin_quartiers',$idModerateur))
                            {
                                $mailModerateur = $u->getMailUtilisateur($idModerateur);
                                $mail->sendMail($mail->getSiteMail(),$mailModerateur,"archi-strasbourg.org : un utilisateur a modifié un élément d'adresse",$messageModerateur,true);
                            }
                        }
                    }
                }*/
            }
        }
        return $errors;
    }

    // recupere la premiere image d'un evenement en fonction de sa position
    public function getFirstImageFromEvenement($idEvenement=0)
    {
        $fetch=array();
        
        $reqVerifTri = "
                    SELECT ei.idImage
                    FROM _evenementImage ei
                    WHERE ei.idEvenement = '".$idEvenement."'
                    AND position<>0
        ";
        
        $resVerifTri = $this->connexionBdd->requete($reqVerifTri);
        
        if(mysql_num_rows($resVerifTri)>0)
        {
            // les images ont deja ete triés , on prend celle qui a la plus petite position <>0
            $req = "
                SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload
                FROM historiqueImage hi2,historiqueImage hi1
                RIGHT JOIN _evenementImage ei1 ON ei1.idEvenement ='".$idEvenement."'
                RIGHT JOIN _evenementImage ei2 ON ei2.idEvenement = ei1.idEvenement
                WHERE hi1.idImage = ei1.idImage
                AND hi2.idImage = hi1.idImage
                AND ei1.position<>'0'
                AND ei2.position<>'0'
                GROUP BY hi1.idImage,ei1.position,hi1.idHistoriqueImage
                HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage) AND ei1.position = min(ei2.position)
                ORDER BY ei1.position ASC
            ";
            
            $res = $this->connexionBdd->requete($req);
            
            $fetch = mysql_fetch_assoc($res);
            
        }
        else
        {
            // pas d'image de position differente de zero , on selectionne la derniere ajoutée de l'evenement
            $req = "
                SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload
                FROM historiqueImage hi2,historiqueImage hi1
                RIGHT JOIN _evenementImage ei1 ON ei1.idEvenement ='".$idEvenement."'
                WHERE hi1.idImage = ei1.idImage
                AND hi2.idImage = hi1.idImage
                GROUP BY hi1.idImage,hi1.idHistoriqueImage
                HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                ORDER BY hi1.idHistoriqueImage
            ";
            
            $res = $this->connexionBdd->requete($req);
            
            if(mysql_num_rows($res)==0)
            {
                // pas d'image pour l'evenement courant, on va en chercher une pour l'adresse courante de l'evenement ( donc dans un evenement de la meme adresse)
                $req = "
                    SELECT hi1.idHistoriqueImage as idHistoriqueImage, hi1.dateUpload as dateUpload,ei1.position as position
                    FROM historiqueImage hi2,historiqueImage hi1
                    
                    RIGHT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$idEvenement."'
                    LEFT JOIN _evenementEvenement ee2 ON ee2.idEvenement = ee.idEvenement
                    RIGHT JOIN _evenementEvenement ee3 ON ee3.idEvenement = ee2.idEvenementAssocie
                    RIGHT JOIN _evenementImage ei1 ON ei1.idEvenement = ee3.idEvenementAssocie
                    
                    WHERE hi1.idImage = ei1.idImage
                    AND hi2.idImage = hi1.idImage
                    GROUP BY hi1.idImage,hi1.idHistoriqueImage
                    HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
                    ORDER BY position, hi1.idHistoriqueImage
                ";
                
                $res = $this->connexionBdd->requete($req);
                while($fetchImagesAdresses = mysql_fetch_assoc($res))
                {
                    if($fetchImagesAdresses['position']=='1')
                    {
                        $fetch = $fetchImagesAdresses;
                        break;
                    }
                    else
                    {
                        $fetch = $fetchImagesAdresses;
                    }
                }
                
            }
            else
            {
                $fetch = mysql_fetch_assoc($res);
            }
        }
        
        return $fetch;
    }
    
    public function getIntituleAdresseFrom($id=0,$type='',$params=array())
    {
        
        switch($type)
        {
            case 'idEvenement':
                $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.numero, ha1.idQuartier, ha1.idVille,ind.nom,
                
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        ha1.numero as numeroAdresse, 
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        ha1.numero as numero,
                        ha1.idHistoriqueAdresse,
                        ha1.idIndicatif as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = '".$id."'
                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                    
                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = ae.idAdresse
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                $res = $this->connexionBdd->requete($req);

                $fetch = mysql_fetch_assoc($res);
                
            break;
            case 'idHistoriqueEvenement':
            
            break;
            case 'idEvenementGroupeAdresse':
                
                $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.numero, ha1.idQuartier, ha1.idVille,ind.nom,
                
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        ha1.numero as numeroAdresse, 
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        ha1.numero as numero,
                        ha1.idHistoriqueAdresse,
                        ha1.idIndicatif as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = '".$id."'
                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                    
                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = ae.idAdresse
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    ORDER BY ha1.numero
                ";
                $res = $this->connexionBdd->requete($req);
                $arrayFetch = array(); // liste des idAdresses dans le cas du groupe d'adresse ou il y a plusieurs adresse, fonctionne pour le moment uniquement si on fais la requete sur un groupe d'adresse
                
                while($fetch = mysql_fetch_assoc($res))
                {
                    $arrayFetch[] = $fetch;
                }
                $params['arrayIdAdressesSurMemeGroupeAdresse'] = $arrayFetch;
                $params['idEvenementGroupeAdresse'] = $id; // pour que la fonction getIntituleAdresse recherche le titre concernant le groupe d'adresse et pas l'adresse en general , sinon probleme d'affichage de titre sur la recherche etc...
            break;
            case 'idAdresse':
                $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.numero, ha1.idQuartier, ha1.idVille,ind.nom,
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        ha1.numero as numeroAdresse, 
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        ha1.numero as numero,
                        ha1.idHistoriqueAdresse,
                        ha1.idIndicatif as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1

                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = '".$id."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            break;
            case 'idRue':
                // selectionne les adresses d'une rue données (seulement les adresses avec un numero)
                $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.idQuartier, ha1.idVille,ind.nom,
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        
                        ha1.idHistoriqueAdresse,
                        ha1.idIndicatif as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1

                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idRue='".$id."'
                    AND ha1.numero<>'' 
                    AND ha1.numero<>'0'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            
            break;
            case 'idRueWithNoNumeroAuthorized':
                // selectionne les adresses d'une rue données (seulement les adresses avec un numero)
                $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.idQuartier, ha1.idVille,ind.nom,
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        
                        ha1.idHistoriqueAdresse,
                        '0' as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1

                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idRue='".$id."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    LIMIT 1
                    ";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            
            break;
            
            case 'idQuartier':
                // selectionne les adresses d'une rue données (seulement les adresses avec un numero)
                $req = "
                    SELECT distinct ha1.idQuartier, ha1.idVille,ind.nom,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays
                        
                        
                    FROM historiqueAdresse ha2, historiqueAdresse ha1

                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND q.idQuartier='".$id."'
                    AND ha1.numero<>'' 
                    AND ha1.numero<>'0'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            
            break;
            
            case 'idVille':
                // selectionne les adresses d'une rue données (seulement les adresses avec un numero)
                $req = "
                    SELECT v.nom as nomVille, p.nom as nomPays
                    FROM ville v
                    LEFT JOIN pays p ON p.idPays = v.idPays
                    WHERE idVille = '".$id."'
                    ";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            
            break;
            
            case 'idImage':
                    $req = "
                    SELECT distinct ha1.idAdresse as idAdresse, ha1.numero, ha1.idQuartier, ha1.idVille,ind.nom,
                        r.nom as nomRue,
                        sq.nom as nomSousQuartier,
                        q.nom as nomQuartier,
                        v.nom as nomVille,
                        p.nom as nomPays,
                        ha1.numero as numeroAdresse, 
                        ha1.idRue,
                        r.prefixe as prefixeRue,
                        IF (ha1.idSousQuartier != 0, ha1.idSousQuartier, r.idSousQuartier) AS idSousQuartier,
                        IF (ha1.idQuartier != 0, ha1.idQuartier, sq.idQuartier) AS idQuartier,
                        IF (ha1.idVille != 0, ha1.idVille, q.idVille) AS idVille,
                        IF (ha1.idPays != 0, ha1.idPays, v.idPays) AS idPays,
                        
                        ha1.numero as numero,
                        ha1.idHistoriqueAdresse,
                        ha1.idIndicatif as idIndicatif
                    FROM historiqueAdresse ha2, historiqueAdresse ha1

                    LEFT JOIN indicatif ind ON ind.idIndicatif = ha1.idIndicatif
                
                    LEFT JOIN rue r         ON r.idRue = ha1.idRue
                    LEFT JOIN sousQuartier sq   ON sq.idSousQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier!='0' ,ha1.idSousQuartier ,r.idSousQuartier )
                    LEFT JOIN quartier q        ON q.idQuartier = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier!='0' ,ha1.idQuartier ,sq.idQuartier )
                    LEFT JOIN ville v       ON v.idVille = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille!='0' ,ha1.idVille ,q.idVille )
                    LEFT JOIN pays p        ON p.idPays = IF(ha1.idRue='0' and ha1.idSousQuartier='0' and ha1.idQuartier='0' and ha1.idVille='0' and ha1.idPays!='0' ,ha1.idPays ,v.idPays )
                    LEFT JOIN _adresseEvenement ae ON ha1.idAdresse = ae.idAdresse
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    LEFT JOIN _evenementImage ei ON ei.idEvenement = ee.idEvenementAssocie
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ei.idImage = '".$id."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)";
                
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
            break;
        }
        
        return $this->getIntituleAdresse($fetch, $params);
    }
    
    
    // affiche la googlemap a coté des adresses sur le detail d'une adresse dans une iframe
    public function getGoogleMapIframe($params = array())
    {
        $html="";

        if(isset($this->variablesGet['longitude']) && $this->variablesGet['longitude']!='' && isset($this->variablesGet['latitude']) && $this->variablesGet['latitude']!='')
        {
            $ajax = new ajaxObject();
            $html.=$ajax->getAjaxFunctions();
        
        
        
            $longitude = $this->variablesGet['longitude'];
            $latitude = $this->variablesGet['latitude'];
            

            
            $listeCoords = array();
            
            
            // si archiIdAdresse est précisé , on remplace les coordonnées par celle de l'adresse ( car celle envoyée sont celle de la premiere du groupe d'adresse)
            $isCoordonneesAdresseCouranteValide = true;
            $isCoordonneesGroupeAdresseOK = false;
            
            
            if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='' && isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='')
            {
                // s'il existe des coordonnées pour le groupe d'adresse dans la table _adresseEvenement, on prend celles ci
                $reqCoordonneesGroupeAdresse = "SELECT longitudeGroupeAdresse,latitudeGroupeAdresse FROM _adresseEvenement WHERE idAdresse='".$this->variablesGet['archiIdAdresse']."' AND idEvenement='".$this->variablesGet['archiIdEvenementGroupeAdresse']."' AND longitudeGroupeAdresse<>'0' AND latitudeGroupeAdresse<>'0'";
                $resCoordonneesGroupeAdresse = $this->connexionBdd->requete($reqCoordonneesGroupeAdresse);
                if(mysql_num_rows($resCoordonneesGroupeAdresse)>0)
                {
                    $fetchCoordonneesGroupeAdresse = mysql_fetch_assoc($resCoordonneesGroupeAdresse);
                    $longitude = $fetchCoordonneesGroupeAdresse['longitudeGroupeAdresse'];
                    $latitude = $fetchCoordonneesGroupeAdresse['latitudeGroupeAdresse'];
                    $isCoordonneesGroupeAdresseOK = true;
                }
            
            }
            
            
            
            if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='' && $this->variablesGet['archiIdAdresse']!='0' && !$isCoordonneesGroupeAdresseOK)
            {
                $reqCoordonnees = "
                    SELECT ha1.longitude as longitude, ha1.latitude as latitude
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    WHERE ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = '".$this->variablesGet['archiIdAdresse']."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resCoordonnees = $this->connexionBdd->requete($reqCoordonnees);
                
                if(mysql_num_rows($resCoordonnees)==1)
                {
                    $fetchCoordonnees = mysql_fetch_assoc($resCoordonnees);
                    if($fetchCoordonnees['longitude']!='' && $fetchCoordonnees['latitude']!='' && $fetchCoordonnees['longitude']!='0' && $fetchCoordonnees['latitude']!='0')
                    {
                        $longitude = $fetchCoordonnees['longitude'];
                        $latitude = $fetchCoordonnees['latitude'];
                    }
                    else
                    {
                        $isCoordonneesAdresseCouranteValide = false;
                    }
                }
                
            }
            
            $affichageCoordonneesVille = false;
            if($longitude<0 || $latitude<0 || $longitude==0 || $latitude==0 || !$isCoordonneesAdresseCouranteValide)
            {
                // on detection une longitude ou latitude negative : il doit y avoir eu une erreur de detection des parametres
                // on va centrer la carte sur la ville
                
                if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='' && $this->variablesGet['archiIdAdresse']!='0')
                {
                    // recuperation de l'idville de l'adresse courante
                    $idVille = $this->getIdVilleFrom($this->variablesGet['archiIdAdresse'],'idAdresse');
                    // coordonnees de la ville
                    $reqCoordonneesVille = "SELECT longitude,latitude,nom FROM ville WHERE idVille = '".$idVille."'";
                    $resCoordonneesVille = $this->connexionBdd->requete($reqCoordonneesVille);
                    $fetchCoordonneesVille = mysql_fetch_assoc($resCoordonneesVille);
                    $longitude = $fetchCoordonneesVille['longitude'];
                    $latitude = $fetchCoordonneesVille['latitude'];
                    $affichageCoordonneesVille = true;
                }
                
            }
            

            // rayon en metres
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse')
            { // dans le cas de l'affichage de la popup sur le detail de l'adresse on prend un rayon plus grand, donc plus d'adresses affichées
                $rayon = 200;
            }
            else
            {
                $rayon = 200;
            }
            
            $arrayGoogleMapCoord = $this->getArrayGoogleMapConfigCoordonneesFromCenter(array('longitude'=>$longitude,'latitude'=>$latitude,'rayon'=>$rayon));
            
            $listeCoords = $arrayGoogleMapCoord['arrayConfigCoordonnees'];
            // verification des droits
            $isAuthorizedToDrag = false;
            $widthGoogleMapModePopup = 650;
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse' )
            {
                if(isset($this->variablesGet['archiIdAdresse']) && $this->variablesGet['archiIdAdresse']!='' && isset($this->variablesGet['archiIdEvenementGroupeAdresse']) && $this->variablesGet['archiIdEvenementGroupeAdresse']!='')
                {
                    $idAdresseCentree = $this->variablesGet['archiIdAdresse'];
                    $idEvenementGroupeAdresseCentre = $this->variablesGet['archiIdEvenementGroupeAdresse'];
                    
                    
                    $utilisateur = new archiUtilisateur();
                    $authentification = new archiAuthentification();
                    
                    // verification des droits
                    if($utilisateur->isAuthorized('googlemap_change_coordonnees',$authentification->getIdUtilisateur()))
                    {
                        if($utilisateur->getIdProfil($authentification->getIdUtilisateur())==4 || ($utilisateur->getIdProfil($authentification->getIdUtilisateur())==3 
                            && $utilisateur->isModerateurFromVille($authentification->getIdUtilisateur(),$idAdresseCentree,'idAdresse')))
                        {
                            $isAuthorizedToDrag = true;
                            $widthGoogleMapModePopup = 650;
                        }
                    }
                }
            }
            
            
            
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse')
            {
                // affichage pour la popup sur le detail d'une adresse
                if($affichageCoordonneesVille)
                {
                    $gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>$widthGoogleMapModePopup,'height'=>500,'zoom'=>11,'noDisplayZoomSelectionSquare'=>true,'noDisplayZoomSlider'=>false,'zoomType'=>'mini','noDisplayEchelle'=>true,'noDisplayMapTypeButtons'=>false,'centerLong'=>$longitude,'centerLat'=>$latitude,'mapType'=>'G_SATELLITE_MAP'));
                }
                else
                {
                    $gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>$widthGoogleMapModePopup,'height'=>500,'zoom'=>17,'noDisplayZoomSelectionSquare'=>true,'noDisplayZoomSlider'=>false,'zoomType'=>'mini','noDisplayEchelle'=>true,'noDisplayMapTypeButtons'=>false,'centerLong'=>$longitude,'centerLat'=>$latitude,'mapType'=>'G_SATELLITE_MAP'));
                }
            }
            else
            {
                if($affichageCoordonneesVille)
                {
                    $gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>275,'height'=>275,'zoom'=>10,'noDisplayZoomSelectionSquare'=>true,'noDisplayZoomSlider'=>false,'zoomType'=>'mini','noDisplayEchelle'=>true,'noDisplayMapTypeButtons'=>true,'centerLong'=>$longitude,'centerLat'=>$latitude,'mapType'=>'G_SATELLITE_MAP'));
                }
                else
                {
                    // affichage par defaut , s'affichage en haut de la page detail d'une adresse , a gauche de l'encars
                    $gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>275,'height'=>275,'zoom'=>16,'noDisplayZoomSelectionSquare'=>true,'noDisplayZoomSlider'=>false,'zoomType'=>'mini','noDisplayEchelle'=>true,'noDisplayMapTypeButtons'=>true,'centerLong'=>$longitude,'centerLat'=>$latitude,'mapType'=>'G_SATELLITE_MAP'));//,'divStyle'=>'margin-top:-17px;'
                }
            }
            
            
            $html.=$gm->getJsFunctions();
            //$html.="<script  >".$gm->setFunctionAddPointsCallableFromChild()."</script>";
            $html.=$gm->getMap(array('listeCoordonnees'=>$listeCoords,'urlImageIcon'=>$this->getUrlImage()."pointGM.png",'pathImageIcon'=>$this->getCheminPhysique()."images/pointGM.png"));
            
            // on ajoute le markeur central a la main
            $html.="<script  >
                var iconHome = new GIcon();
                iconHome.image = \"".$this->getUrlImage()."placeMarker.png\";
                //iconHome.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                iconHome.iconSize = new GSize(19, 32);
                iconHome.shadowSize = new GSize(22, 20);
                iconHome.iconAnchor = new GPoint(5, 26);
                iconHome.infoWindowAnchor = new GPoint(5, 1);
                
                //var iconMarkerHome = new GIcon(iconHome);
                ";
            
            
            
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse' && $isAuthorizedToDrag)
            {
                $html.="markerHome = new GMarker(new GLatLng(".$latitude.",".$longitude."),{icon:iconHome, draggable: true});";
            }
            else
            {           
                $html.="markerHome = new GMarker(new GLatLng(".$latitude.",".$longitude."),{icon:iconHome});";
            }
            
            $html.="map.addOverlay(markerHome);";
            
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse' && $isAuthorizedToDrag)
            {
                $html.="markerHome.enableDragging();";
            }
            
            if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']=='popupDetailAdresse' && $isAuthorizedToDrag)
            {
                $html.="GEvent.addListener(markerHome,'drag',function(){parent.window.document.getElementById('latitudeUser').value=markerHome.getPoint().lat();parent.window.document.getElementById('longitudeUser').value=markerHome.getPoint().lng();parent.window.document.getElementById('validationCoordonnees').style.display='';});
                ";
                
                $html.="GEvent.addListener(
                            map,
                            'dragend',
                            function(){appelAjaxReturnJs('".html_entity_decode($this->creerUrl('','majGoogleMapNewCenter',array('noRefresh'=>1,'noHTMLHeaderFooter'=>1,'noHeaderNoFooter'=>1,'latitudeHome'=>$latitude,'longitudeHome'=>$longitude)))."&longitudeCenter='+map.getCenter().lng()+'&latitudeCenter='+map.getCenter().lat(),'divListeAdressesAjax')}
                            );";
            }
            else
            {
                    $html.="GEvent.addListener(
                            map,
                            'dragend',
                            function(){appelAjaxReturnJs('".html_entity_decode($this->creerUrl('','majGoogleMapNewCenter',array('noHTMLHeaderFooter'=>1,'noHeaderNoFooter'=>1,'latitudeHome'=>$latitude,'longitudeHome'=>$longitude)))."&longitudeCenter='+map.getCenter().lng()+'&latitudeCenter='+map.getCenter().lat(),'divListeAdressesAjax')}
                            );";
                    //$html.="GEvent.addListener(map,'dragend',function(){document.getElementById('iFrameMajCenter').src='".$this->creerUrl('','majGoogleMapNewCenter',array('noHeaderNoFooter'=>1,'latitudeHome'=>$latitude,'longitudeHome'=>$longitude))."&longitudeCenter='+map.getCenter().lng()+'&latitudeCenter='+map.getCenter().lat();});";
            }           
                

            
            //$html.=$gm->setFunctionAddPointsCallableFromChild(array());
            
            $html.="</script>";
            //$html.="<div id='jsMiseAJourCenter' ><iframe id='iFrameMajCenter' src=''></iframe></div>";//style='position:absolute;left:0px;top:0px;'
            
            
            
        }
        
        
        
        return $html;
    
    }
    
    
    
    // ******************************************************************************************************************************************************
    // cette fonction renvoi les infos pour l'affichage de la google map
    // - si on passe en parametres une latitude et longitude et un rayon , on affiche les points autour
    // - si on passes des tableaux d'idAdresses ou idEvenementsGroupesAdresses , la fonction se charge simplement de creer les lables et les liens destinés a la fonction getMap de l'objet googleMap

    // on peut donc passer un tableau d'id adresse a la fonction, ce tableau doit contenir les longitudes et latitudes associees
    // ajout : on peut maintenant aussi passer un tableau de groupes d'adresses , la fonction devient difficile a lire , on en rajoutera pas plus
    public function getArrayGoogleMapConfigCoordonneesFromCenter($params = array())
    {
        $listeCoords = array();
        if(!isset($params['arrayIdAdresses']) && !isset($params['arrayIdEvenementsGroupeAdresse']))
        {
            $latitude = $params['latitude'];
            $longitude = $params['longitude'];
            $rayon = $params['rayon'];
            
            $reqListeCoords = "
                        SELECT ((acos(sin($latitude*PI()/180) * sin(ha1.latitude*PI()/180) + cos($latitude*PI()/180) * cos(ha1.latitude*PI()/180) * cos(($longitude - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344) as distanceFromPoint,ha1.longitude as longitude,ha1.latitude as latitude,ha1.idAdresse as idAdresse
                        FROM historiqueAdresse ha1,historiqueAdresse ha2
                        WHERE 

                            ha1.latitude<>''    
                            AND 
                            ha1.longitude<>''
                            AND 
                            ((acos(sin($latitude*PI()/180) * sin(ha1.latitude*PI()/180) + cos($latitude*PI()/180) * cos(ha1.latitude*PI()/180) * cos(($longitude - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000<$rayon


                        AND ha2.idAdresse = ha1.idAdresse
                        
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) 
                ";
                
            $resListeCoords = $this->connexionBdd->requete($reqListeCoords);
            $nbListeCoords = mysql_num_rows($resListeCoords);
            
            $fetchListeCoordsArray = array();
            $arrayIdAdresses = array();
            while($fetchListeCoordsAdresses = mysql_fetch_assoc($resListeCoords))
            {
                $fetchListeCoordsArray[] = $fetchListeCoordsAdresses;
                $arrayIdAdresses[] = $fetchListeCoordsAdresses['idAdresse'];
            }
            
            $sqlCritere = "";
            if(count($arrayIdAdresses)>0)
            {
                $sqlCritere = " AND ae.idAdresse NOT IN (".implode(",",$arrayIdAdresses).") ";
            }
            
            // on fait la requete pour une recherche sur la table _adresseEvenement
            $reqListeCoordsGA = "
            
                        SELECT ((acos(sin($latitude*PI()/180) * sin(ae.latitudeGroupeAdresse*PI()/180) + cos($latitude*PI()/180) * cos(ae.latitudeGroupeAdresse*PI()/180) * cos(($longitude - ae.longitudeGroupeAdresse)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344) as distanceFromPoint,ae.longitudeGroupeAdresse as longitude,ae.latitudeGroupeAdresse as latitude,ae.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                        FROM _adresseEvenement ae
                        WHERE 

                            ae.latitudeGroupeAdresse<>''    
                            AND 
                            ae.longitudeGroupeAdresse<>''
                            AND 
                            ae.latitudeGroupeAdresse<>'0'   
                            AND 
                            ae.longitudeGroupeAdresse<>'0'
                         $sqlCritere
                        AND
                        ((acos(sin($latitude*PI()/180) * sin(ae.latitudeGroupeAdresse*PI()/180) + cos($latitude*PI()/180) * cos(ae.latitudeGroupeAdresse*PI()/180) * cos(($longitude - ae.longitudeGroupeAdresse)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000<$rayon
            
            ";
            
            
            $resListeCoordsGA = $this->connexionBdd->requete($reqListeCoordsGA);
            $nbListeCoords+=mysql_num_rows($resListeCoordsGA);
            while($fetchListeCoordsAdressesGA = mysql_fetch_assoc($resListeCoordsGA))
            {
                $fetchListeCoordsArray[] = $fetchListeCoordsAdressesGA;
                
            }
            
            
            
            
        }
        $i=0;
        $indiceArrayId = 0;
        $ok = true;
        while($ok)
        {
            if(!isset($params['arrayIdAdresses']) && !isset($params['arrayIdEvenementsGroupeAdresse']))
            {
                //$fetchListeCoords = mysql_fetch_assoc($resListeCoords);
                if(!isset($fetchListeCoordsArray[$indiceArrayId]))
                    break;
                $fetchListeCoords = $fetchListeCoordsArray[$indiceArrayId];
                if($indiceArrayId>=$nbListeCoords-1)
                {
                    $ok = false;
                }
            }
            elseif(isset($params['arrayIdAdresses']))
            {
                if(!isset($params['arrayIdAdresses'][$indiceArrayId]))
                    break;
                $fetchListeCoords = $params['arrayIdAdresses'][$indiceArrayId];
            }
            elseif(isset($params['arrayIdEvenementsGroupeAdresse']))
            {
                if(!isset($params['arrayIdEvenementsGroupeAdresse'][$indiceArrayId]))
                    break;
                    
                $fetchListeCoords = $params['arrayIdEvenementsGroupeAdresse'][$indiceArrayId];
            }
            
            // on verifie si un groupe d'adresse est bien rattaché a l'adresse , car on conserve les adresses , mais on ne les affiches pas quand elles ne sont pas reliées a un groupe d'adresse
            // si c'est un tableau de groupes d'adresses qui est transmis , dans ce cas on verifie bien qu'il existe , dans ce cas , cette requete est utilie pour recuperer un idAdresse du groupe d'adresse
            
            if(isset($fetchListeCoords['idEvenementGroupeAdresse']))
            {
                $reqVerifGA = "SELECT idEvenement,idAdresse, longitudeGroupeAdresse,latitudeGroupeAdresse FROM _adresseEvenement WHERE idEvenement='".$fetchListeCoords['idEvenementGroupeAdresse']."'";
            }
            elseif(isset($fetchListeCoords['idAdresse']))
            {
                $reqVerifGA = "SELECT idEvenement, longitudeGroupeAdresse,latitudeGroupeAdresse FROM _adresseEvenement WHERE idAdresse='".$fetchListeCoords['idAdresse']."'";
            }
            else
            {
                $reqVerifGA = "SELECT 0 FROM _adresseEvenement WHERE 0=1"; // debug laurent dans le cas ou il n'y a aucun point ... cette fonction a trop ete adaptée deja, a refaire si nouvelle grande adaptation requise
            }

            
            $resVerifGA = $this->connexionBdd->requete($reqVerifGA);
            
            if(mysql_num_rows($resVerifGA)==1 || isset($fetchListeCoords['idEvenementGroupeAdresse'])) // dans le cas ou on a passé un tableau de groupe d'adresses en parametre de la fonction, vu que l'on traite le groupe d'adresse, on force le passage ici , les autres conditions traitent des idAdresses et celle ci fait ce qu'il faut
            {
                $fetchGA = mysql_fetch_assoc($resVerifGA);
                
                //$fetchGA['idEvenement'],'idEvenementGroupeAdresse'
                if(isset($fetchListeCoords['idEvenementGroupeAdresse']))
                {
                    $infosAdresses = $this->getIntituleAdresseFrom($fetchListeCoords['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','displayFirstTitreAdresse'=>true,'setSeparatorAfterTitle'=>'<br>'));
                }
                else
                {
                    $infosAdresses = $this->getIntituleAdresseFrom($fetchListeCoords['idAdresse'],'idAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','displayFirstTitreAdresse'=>true,'setSeparatorAfterTitle'=>'<br>'));
                }
                
                // un seul groupe d'adresse , on fais une redirection vers la page du detail de l'adresse
                $listeCoords[$i]['longitude'] = $fetchListeCoords['longitude'];
                $listeCoords[$i]['latitude'] = $fetchListeCoords['latitude'];
                $listeCoords[$i]['libelle'] = '';
                $listeCoords[$i]['label'] = $infosAdresses;
                
                if(isset($fetchListeCoords['idAdresse']))
                    $listeCoords[$i]['idAdresse'] = $fetchListeCoords['idAdresse'];
                else
                    $listeCoords[$i]['idAdresse'] = $fetchGA['idAdresse']; // dans le cas ou on passe un tableau de groupes d'adresses a la fonction
                    
                if(isset($params['urlIcon']) && $params['urlIcon']!='')
                {
                    $listeCoords[$i]['urlIcon'] = $params['urlIcon'];
                }
                
                if(isset($params['dimIconX']) && $params['dimIconX']!='')
                {
                    $listeCoords[$i]['dimIconX'] = $params['dimIconX'];
                }
                
                if(isset($params['dimIconY']) && $params['dimIconY']!='')
                {
                    $listeCoords[$i]['dimIconY'] = $params['dimIconY'];
                }
                
                
                //$listeCoords[$i]['idEvenementGroupeAdresse'] = $fetchGA['idEvenement'];
                if(isset($params['urlRedirectedToParent']) && $params['urlRedirectedToParent']==true) // si on appel d'une iframe dans une iframe
                {
                    $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse'], "archiIdEvenementGroupeAdresse"=>$fetchGA['idEvenement']), false, false)."';";
                }
                else
                {
                    if(isset($fetchListeCoords['idAdresse']))
                    {
                        $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse'], "archiIdEvenementGroupeAdresse"=>$fetchGA['idEvenement']), false, false)."';";
                    }
                    else
                    {
                        // cas ou l'on a transmis un tableau de groupes d'adresses a la fonction, dans ce cas l'idAdresse est recuperé par la requete qui verifie le nombre de groupe d'adresses
                        $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.document.location.href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdEvenementGroupeAdresse'=>$fetchGA['idEvenement'],'archiIdAdresse'=>$fetchGA['idAdresse']), false, false)."';";
                    }
                }
                $listeCoords[$i]['jsCodeOnMouseOverMarker'] = "currentLabel.show();";//"currentMarker.openInfoWindowHtml(\"<table width=150><tr><td><span style='font-size:9px;'>$infosAdresses<br><a  href='#' onclick=\\\"parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse']))."'\\\">Afficher</a></span></td></tr></table>\");";
                $listeCoords[$i]['jsCodeOnMouseOutMarker'] = "currentLabel.hide();";//setTimeout('currentMarker.closeInfoWindow()',1000);
                $i++;
            }
            elseif(mysql_num_rows($resVerifGA)>1)
            { // plusieurs groupes d'adresses sur la meme adresse , en principe ce cas n'est pas possible si l'on a passé des groupes d'adresses en parametre 
                $infosAdresses = "";
                $j=0;
                $infosAdressesTitres = "";
                $infosAdressesTitresGroupeAdresse = "";
                while($fetchGA = mysql_fetch_assoc($resVerifGA))
                {
                
                    if($fetchGA['longitudeGroupeAdresse']!='0' && $fetchGA['latitudeGroupeAdresse']!='0')
                    {
                        // des coordonnees spécifiques pour le groupe d'adresse on été spécifiée
                        // s'il y a plusieurs groupes d'adresses il faut proposer a l'utilisateur de choisir entre eux par le formulaire de recherche
                        $infosAdresses =$this->getIntituleAdresseFrom($fetchGA['idEvenement'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','setSeparatorAfterTitle'=>'<br>'))."<br>";

                        $titre = $this->getIntituleAdresseFrom($fetchGA['idEvenement'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','afficheTitreSiTitreSinonRien'=>true,'setSeparatorAfterTitle'=>' '));
                        $infosAdressesTitresGroupeAdresse="";
                        if(trim($titre)!='')
                        {
                            $infosAdressesTitresGroupeAdresse="<span style='font-weight:bold;font-size:9px;'>".$titre."</span><br>";
                        }
                        $listeCoords[$i]['longitude'] = $fetchGA['longitudeGroupeAdresse'];
                        $listeCoords[$i]['latitude'] = $fetchGA['latitudeGroupeAdresse'];
                        $listeCoords[$i]['libelle'] = '';
                        $listeCoords[$i]['label'] = $infosAdressesTitresGroupeAdresse.$infosAdresses;
                        $listeCoords[$i]['idAdresse'] = $fetchListeCoords['idAdresse'];
                        if(isset($params['urlIcon']) && $params['urlIcon']!='')
                        {
                            $listeCoords[$i]['urlIcon'] = $params['urlIcon'];
                        }
                        //$listeCoords[$i]['idEvenementGroupeAdresse'] = $fetchGA['idEvenement'];
                        if(isset($params['urlRedirectedToParent']) && $params['urlRedirectedToParent']==true) // si on appel d'une iframe dans une iframe
                        {
                            $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdEvenementGroupeAdresse'=>$fetchGA['idEvenement'],'archiIdAdresse'=>$fetchListeCoords['idAdresse']), false, false)."';";
                        }
                        else
                        {
                            $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdEvenementGroupeAdresse'=>$fetchGA['idEvenement'],'archiIdAdresse'=>$fetchListeCoords['idAdresse']), false, false)."';";
                        }
                        $listeCoords[$i]['jsCodeOnMouseOverMarker'] = "currentLabel.show();";//"currentMarker.openInfoWindowHtml(\"<table width=150><tr><td><span style='font-size:9px;width:200px;'>$infosAdresses<br><a href='#' onclick=\\\"parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse']))."'\\\">Afficher</a></span></td></tr></table>\");";
                        $listeCoords[$i]['jsCodeOnMouseOutMarker'] = "currentLabel.hide();";//w = window.open(); for(i in currentMarker){w.document.writeln(i+' '+currentMarker[i]+'<br>')}  // setTimeout('currentMarker.closeInfoWindow()',1000);
                        $listeCoords[$i]['jsCodeMarker'] = "";
                        $i++;
                    
                    
                    }
                    else
                    {
                        if($j==0)
                        {
                            $infosAdresses =$this->getIntituleAdresseFrom($fetchGA['idEvenement'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','setSeparatorAfterTitle'=>'<br>'))."<br>";
                        }
                        $titre = $this->getIntituleAdresseFrom($fetchGA['idEvenement'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true,'styleCSSTitreAdresse'=>'font-weight:bold;font-size:9px;','afficheTitreSiTitreSinonRien'=>true,'setSeparatorAfterTitle'=>' '));
                        if(trim($titre)!='')
                        {
                            $infosAdressesTitres .="<span style='font-weight:bold;font-size:9px;'>".$titre."</span><br>";
                            if($j==0)
                            {
                                $infosAdresses=$infosAdresses;
                            }
                        }
                        $j++;
                    }
                }
                
                if($j>0) // s'il y a des adresses à regrouper
                {
                    if($j==1) // une seule adresse
                    {
                        // finalement il n'y a qu'une seule adresse "regroupée" qui n'a pas de coordonnees specifique a groupe d'adresse (sur la meme adresse, les autres groupes d'adresses ont des coordonnees renseignées) , on ne va donc pas afficher le resultat dans la recherche, mais on va aller sur le groupe d'adresse directement
                        // recherche du groupe d'adresse
                        $reqGroupeAdresseO = "SELECT idEvenement FROM _adresseEvenement WHERE longitudeGroupeAdresse='0' AND latitudeGroupeAdresse='0' AND idAdresse='".$fetchListeCoords['idAdresse']."'";
                        $resGroupeAdresse0 = $this->connexionBdd->requete($reqGroupeAdresseO);
                        if(mysql_num_rows($resGroupeAdresse0)==1)
                        {
                            $fetchGroupeAdresse0 = mysql_fetch_assoc($resGroupeAdresse0);
                            
                            $listeCoords[$i]['longitude'] = $fetchListeCoords['longitude'];
                            $listeCoords[$i]['latitude'] = $fetchListeCoords['latitude'];
                            $listeCoords[$i]['libelle'] = '';
                            $listeCoords[$i]['label'] = $infosAdressesTitres.$infosAdresses;
                            $listeCoords[$i]['idAdresse'] = $fetchListeCoords['idAdresse'];
                            if(isset($params['urlIcon']) && $params['urlIcon']!='')
                            {
                                $listeCoords[$i]['urlIcon'] = $params['urlIcon'];
                            }
                            //$listeCoords[$i]['idEvenementGroupeAdresse'] = $fetchGA['idEvenement'];
                            if(isset($params['urlRedirectedToParent']) && $params['urlRedirectedToParent']==true) // si on appel d'une iframe dans une iframe
                            {
                                $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdEvenementGroupeAdresse'=>$fetchGroupeAdresse0['idEvenement'],'archiIdAdresse'=>$fetchListeCoords['idAdresse']), false, false)."';";
                            }
                            else
                            {
                                $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdEvenementGroupeAdresse'=>$fetchGroupeAdresse0['idEvenement'],'archiIdAdresse'=>$fetchListeCoords['idAdresse']), false, false)."';";
                            }
                            $listeCoords[$i]['jsCodeOnMouseOverMarker'] = "currentLabel.show();";//"currentMarker.openInfoWindowHtml(\"<table width=150><tr><td><span style='font-size:9px;width:200px;'>$infosAdresses<br><a href='#' onclick=\\\"parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse']))."'\\\">Afficher</a></span></td></tr></table>\");";
                            $listeCoords[$i]['jsCodeOnMouseOutMarker'] = "currentLabel.hide();";//w = window.open(); for(i in currentMarker){w.document.writeln(i+' '+currentMarker[i]+'<br>')}  // setTimeout('currentMarker.closeInfoWindow()',1000);
                            $listeCoords[$i]['jsCodeMarker'] = "";
                            $i++;
                        }
                        else
                        {
                            echo "Erreur : il y a plusieurs groupes d'adresses sur la meme adresse : ".$fetchListeCoords['idAdresse']." merci de contacter l'administrateur.<br>";
                        }
                    }
                    else
                    { 
                        // plusieurs adresses dont les groupes d'adresses n'ont pas de coordonnees specifiques
                        // s'il y a plusieurs groupes d'adresses il faut proposer a l'utilisateur de choisir entre eux par le formulaire de recherche
                        $listeCoords[$i]['longitude'] = $fetchListeCoords['longitude'];
                        $listeCoords[$i]['latitude'] = $fetchListeCoords['latitude'];
                        $listeCoords[$i]['libelle'] = '';
                        $listeCoords[$i]['label'] = $infosAdressesTitres.$infosAdresses;
                        $listeCoords[$i]['idAdresse'] = $fetchListeCoords['idAdresse'];
                        if(isset($params['urlIcon']) && $params['urlIcon']!='')
                        {
                            $listeCoords[$i]['urlIcon'] = $params['urlIcon'];
                        }
                        //$listeCoords[$i]['idEvenementGroupeAdresse'] = $fetchGA['idEvenement'];
                        if(isset($params['urlRedirectedToParent']) && $params['urlRedirectedToParent']==true) // si on appel d'une iframe dans une iframe
                        {
                            $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.parent.document.location.href='".$this->creerUrl('','adresseListe',array('recherche_groupesAdressesFromAdresse'=>$fetchListeCoords['idAdresse'],'displayAdresseIfNoCoordonneesGroupeAdresse'=>1))."';"; // ce lien va effectuer une recherche sur l'adresse renseignée en parametre et renvoie les groupes d'adresses ou aucune coordonnees de latitude et longitude ne sont liée au couple "groupe d'adresse-adresse" dans la table _adresseEvenement
                        }
                        else
                        {
                            $listeCoords[$i]['jsCodeOnClickMarker'] = "parent.document.location.href='".$this->creerUrl('','adresseListe',array('recherche_groupesAdressesFromAdresse'=>$fetchListeCoords['idAdresse'],'displayAdresseIfNoCoordonneesGroupeAdresse'=>1))."';";// ce lien va effectuer une recherche sur l'adresse renseignée en parametre et renvoie les groupes d'adresses ou aucune coordonnees de latitude et longitude ne sont liée au couple "groupe d'adresse-adresse" dans la table _adresseEvenement
                        }
                        $listeCoords[$i]['jsCodeOnMouseOverMarker'] = "currentLabel.show();";//"currentMarker.openInfoWindowHtml(\"<table width=150><tr><td><span style='font-size:9px;width:200px;'>$infosAdresses<br><a href='#' onclick=\\\"parent.document.location.href='".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchListeCoords['idAdresse']))."'\\\">Afficher</a></span></td></tr></table>\");";
                        $listeCoords[$i]['jsCodeOnMouseOutMarker'] = "currentLabel.hide();";//w = window.open(); for(i in currentMarker){w.document.writeln(i+' '+currentMarker[i]+'<br>')}  // setTimeout('currentMarker.closeInfoWindow()',1000);
                        $listeCoords[$i]['jsCodeMarker'] = "";
                        $i++;
                    }
                }
            }
            $indiceArrayId++;
            
        }
            
        return array('arrayConfigCoordonnees'=>$listeCoords);
    }
    
    
    
    public function getCoordonneesFrom($id=0,$type='')
    {
        $longitude=0;
        $latitude=0;
        
        switch($type)
        {
            case 'idVille':
                $req = "SELECT latitude, longitude FROM ville WHERE idVille='".$id."'";
                $res = $this->connexionBdd->requete($req);
                $fetch = mysql_fetch_assoc($res);
                $longitude = $fetch['longitude'];
                $latitude = $fetch['latitude'];
            break;
            case 'idEvenementGroupeAdresse':
                $req = "
                        SELECT ha1.latitude as latitude, ha1.longitude as longitude
                        FROM _adresseEvenement ae
                        LEFT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse                        
                        WHERE ha2.idAdresse = ha1.idAdresse
                        AND ha1.longitude!=''
                        AND ha1.latitude!=''
                        AND ha1.longitude!='0'
                        AND ha1.latitude!='0'
                        AND ha1.longitude IS NOT NULL
                        AND ha1.latitude IS NOT NULL
                        AND ae.idEvenement = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)           
                    ";
                $res = $this->connexionBdd->requete($req);
                if(mysql_num_rows($res)>0)
                {
                    $fetch = mysql_fetch_assoc($res);
                    $longitude = $fetch["longitude"];
                    $latitude = $fetch["latitude"];
                }
            
            break;
            case 'idAdresse':
                $req = "
                        SELECT ha1.latitude as latitude,ha1.longitude as longitude
                        FROM historiqueAdresse ha2, historiqueAdresse ha1
                        WHERE
                            ha2.idAdresse = ha1.idAdresse
                        AND ha1.idAdresse = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                        ";
                $res = $this->connexionBdd->requete($req);
                if(mysql_num_rows($res)>0)
                {
                    $fetch = mysql_fetch_assoc($res);
                    $longitude = $fetch["longitude"];
                    $latitude = $fetch["latitude"];
                }
            break;
            
            case 'idHistoriqueAdresse':
            
            break;
        }
        
        return array("longitude"=>$longitude,"latitude"=>$latitude);
    }
    
    // renvoi un tableau comportant le nombre de dependances d'un element d'adresse ainsi que la liste des adresses dependantes
    public function getDependancesFrom($id=0, $type='')
    {
        $nbDependances = '';
        $arrayDependances=array();
        
        switch($type)
        {
            case 'idRue':
                $reqDependancesAdresses = "
                        SELECT ha1.idAdresse as idAdresse, ha1.idHistoriqueAdresse as idHistoriqueAdresse, count(ee.idEvenementAssocie) as nbEvenementsAssocies
                        FROM historiqueAdresse ha1
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                        
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        
                                                
                        WHERE ha1.idRue = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse,ee.idEvenement
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resDependancesAdresses = $this->connexionBdd->requete($reqDependancesAdresses);
                $nbDependances=0;
                if(mysql_num_rows($resDependancesAdresses)>0)
                {
                    while($fetchDependancesAdresses = mysql_fetch_assoc($resDependancesAdresses))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idAdresse"=>$fetchDependancesAdresses['idAdresse'],"nbEvenementsAssocies"=>$fetchDependancesAdresses['nbEvenementsAssocies']);
                    }
                }
            break;
            
            case 'idSousQuartier':
                $reqDependancesAdresses = "
                        SELECT ha1.idAdresse as idAdresse, ha1.idHistoriqueAdresse as idHistoriqueAdresse, count(ee.idEvenementAssocie) as nbEvenementsAssocies
                        FROM historiqueAdresse ha1
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                        
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        
                                                
                        WHERE ha1.idSousQuartier = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse,ee.idEvenement
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resDependancesAdresses = $this->connexionBdd->requete($reqDependancesAdresses);
                if(mysql_num_rows($resDependancesAdresses)>0)
                {
                    $nbDependances=0;
                    while($fetchDependancesAdresses = mysql_fetch_assoc($resDependancesAdresses))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idAdresse"=>$fetchDependancesAdresses['idAdresse'],"nbEvenementsAssocies"=>$fetchDependancesAdresses['nbEvenementsAssocies']);
                    }
                }
            
                $reqDependancesRues = "
                
                    SELECT idRue
                    FROM rue
                    WHERE idSousQuartier = '".$id."'
                
                ";
                
                $resDependancesRues = $this->connexionBdd->requete($reqDependancesRues);
                
                if(mysql_num_rows($resDependancesRues)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesRues = mysql_fetch_assoc($resDependancesRues))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idRue"=>$fetchDependancesRues['idRue']);
                    }
                }               
            break;
            
            case 'idQuartier':
            
                $reqDependancesAdresses = "
                        SELECT ha1.idAdresse as idAdresse, ha1.idHistoriqueAdresse as idHistoriqueAdresse, count(ee.idEvenementAssocie) as nbEvenementsAssocies
                        FROM historiqueAdresse ha1
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                        
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        
                                                
                        WHERE ha1.idQuartier = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse,ee.idEvenement
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resDependancesAdresses = $this->connexionBdd->requete($reqDependancesAdresses);
                if(mysql_num_rows($resDependancesAdresses)>0)
                {
                    $nbDependances=0;
                    while($fetchDependancesAdresses = mysql_fetch_assoc($resDependancesAdresses))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idAdresse"=>$fetchDependancesAdresses['idAdresse'],"nbEvenementsAssocies"=>$fetchDependancesAdresses['nbEvenementsAssocies']);
                    }
                }
                
                
                
                $reqDependancesRues = "
                
                    SELECT idRue
                    FROM rue
                    WHERE idSousQuartier in ( select idSousQuartier from sousQuartier WHERE idQuartier='".$id."')
                
                ";
                
                $resDependancesRues = $this->connexionBdd->requete($reqDependancesRues);

                if(mysql_num_rows($resDependancesRues)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesRues = mysql_fetch_assoc($resDependancesRues))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idRue"=>$fetchDependancesRues['idRue']);
                    }
                }
                
                $reqDependancesSousQuartiers = "
                    SELECT idSousQuartier 
                    FROM sousQuartier WHERE idQuartier='".$id."'
                ";
                
                $resDependancesSousQuartiers = $this->connexionBdd->requete($reqDependancesSousQuartiers);
                
                if(mysql_num_rows($resDependancesSousQuartiers)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesSousQuartiers = mysql_fetch_assoc($resDependancesSousQuartiers))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idSousQuartier"=>$fetchDependancesSousQuartiers['idSousQuartier']);
                    }
                    
                }
                
            break;
            
            case 'idVille':
                $reqDependancesAdresses = "
                        SELECT ha1.idAdresse as idAdresse, ha1.idHistoriqueAdresse as idHistoriqueAdresse, count(ee.idEvenementAssocie) as nbEvenementsAssocies
                        FROM historiqueAdresse ha1
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                        
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        
                                                
                        WHERE ha1.idVille = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse,ee.idEvenement
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resDependancesAdresses = $this->connexionBdd->requete($reqDependancesAdresses);
                if(mysql_num_rows($resDependancesAdresses)>0)
                {
                    $nbDependances=0;
                    while($fetchDependancesAdresses = mysql_fetch_assoc($resDependancesAdresses))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idAdresse"=>$fetchDependancesAdresses['idAdresse'],"nbEvenementsAssocies"=>$fetchDependancesAdresses['nbEvenementsAssocies']);
                    }
                }
                
                
                
                $reqDependancesRues = "
                
                    SELECT idRue
                    FROM rue
                    WHERE idSousQuartier in ( 
                        select idSousQuartier from sousQuartier WHERE idQuartier in ( 
                            select idQuartier from quartier where idVille='".$id."'))
                
                ";
                
                $resDependancesRues = $this->connexionBdd->requete($reqDependancesRues);

                if(mysql_num_rows($resDependancesRues)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesRues = mysql_fetch_assoc($resDependancesRues))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idRue"=>$fetchDependancesRues['idRue']);
                    }
                }
                
                $reqDependancesSousQuartiers = "
                    SELECT idSousQuartier 
                    FROM sousQuartier WHERE idQuartier in ( select idQuartier from quartier WHERE idVille='".$id."')
                ";
                
                $resDependancesSousQuartiers = $this->connexionBdd->requete($reqDependancesSousQuartiers);
                
                if(mysql_num_rows($resDependancesSousQuartiers)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesSousQuartiers = mysql_fetch_assoc($resDependancesSousQuartiers))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idSousQuartier"=>$fetchDependancesSousQuartiers['idSousQuartier']);
                    }
                    
                }
                
                
                
                
                
                $reqDependancesQuartiers = "
                    select idQuartier from quartier WHERE idVille='".$id."'
                ";
                
                $resDependancesQuartiers = $this->connexionBdd->requete($reqDependancesQuartiers);
                
                if(mysql_num_rows($resDependancesQuartiers)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesQuartiers = mysql_fetch_assoc($resDependancesQuartiers))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idQuartier"=>$fetchDependancesQuartiers['idQuartier']);
                    }
                    
                }
            
            
            break;
            case 'idPays':
                $reqDependancesAdresses = "
                        SELECT ha1.idAdresse as idAdresse, ha1.idHistoriqueAdresse as idHistoriqueAdresse, count(ee.idEvenementAssocie) as nbEvenementsAssocies
                        FROM historiqueAdresse ha1
                        LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                        
                        
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        
                                                
                        WHERE ha1.idPays = '".$id."'
                        GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse,ee.idEvenement
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resDependancesAdresses = $this->connexionBdd->requete($reqDependancesAdresses);
                if(mysql_num_rows($resDependancesAdresses)>0)
                {
                    $nbDependances=0;
                    while($fetchDependancesAdresses = mysql_fetch_assoc($resDependancesAdresses))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idAdresse"=>$fetchDependancesAdresses['idAdresse'],"nbEvenementsAssocies"=>$fetchDependancesAdresses['nbEvenementsAssocies']);
                    }
                }
                
                
                
                $reqDependancesRues = "
                
                    SELECT idRue
                    FROM rue
                    WHERE idSousQuartier in ( 
                        select idSousQuartier from sousQuartier WHERE idQuartier in ( 
                            select idQuartier from quartier where idVille in (select idVille FROM ville WHERE idPays='".$id."'))
                    )
                
                ";
                
                $resDependancesRues = $this->connexionBdd->requete($reqDependancesRues);

                if(mysql_num_rows($resDependancesRues)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesRues = mysql_fetch_assoc($resDependancesRues))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idRue"=>$fetchDependancesRues['idRue']);
                    }
                }
                
                $reqDependancesSousQuartiers = "
                    SELECT idSousQuartier 
                    FROM sousQuartier WHERE idQuartier in ( select idQuartier from quartier WHERE idVille in ( SELECT idVille FROM ville WHERE idPays='".$id."'))
                ";
                
                $resDependancesSousQuartiers = $this->connexionBdd->requete($reqDependancesSousQuartiers);
                
                if(mysql_num_rows($resDependancesSousQuartiers)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesSousQuartiers = mysql_fetch_assoc($resDependancesSousQuartiers))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idSousQuartier"=>$fetchDependancesSousQuartiers['idSousQuartier']);
                    }
                    
                }
                
                
                
                
                
                $reqDependancesQuartiers = "
                    select idQuartier from quartier WHERE idVille in (SELECT idVille FROM ville WHERE idPays = '".$id."')
                ";
                
                $resDependancesQuartiers = $this->connexionBdd->requete($reqDependancesQuartiers);
                
                if(mysql_num_rows($resDependancesQuartiers)>0)
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                    
                    while($fetchDependancesQuartiers = mysql_fetch_assoc($resDependancesQuartiers))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idQuartier"=>$fetchDependancesQuartiers['idQuartier']);
                    }
                    
                }
                
                
                $reqDependancesVilles = "SELECT idVille FROM ville WHERE idPays = '".$id."'";
                $resDependancesVilles = $this->connexionBdd->requete($reqDependancesVilles);
                
                if(mysql_num_rows($resDependancesVilles))
                {
                    if($nbDependances=='')
                        $nbDependances=0;
                        
                    while($fetchDependancesVilles = mysql_fetch_assoc($resDependancesVilles))
                    {
                        $nbDependances++;
                        $arrayDependances[]=array("idVille"=>$fetchDependancesVilles['idVille']);
                    }
                }
                
            
            
            break;
            
        }
        
        return array('nbDependances'=>$nbDependances,'arrayDependances'=>$arrayDependances);
    }
    

    // renvoi la ville correspondante a une adresse ou d'autres elements
    public function getIdVilleFrom($id=0, $type='')
    {
        $idVille=0;
        
        switch($type)
        {
            case 'idImage':
                
                $idAdresseFromImage = "";
                // recherche de la ville correspondante a l'image
                $reqAdresse = "
                    SELECT ha1.idRue as idRue,ha1.idQuartier as idQuartier,ha1.idSousQuartier as idSousQuartier,ha1.idVille as idVille
                    FROM historiqueAdresse ha1
                    LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                    LEFT JOIN _evenementImage ei ON ei.idImage = '$id'
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
                    LEFT JOIN _adresseEvenement ae ON ae.idEvenement = ee.idEvenement
                    WHERE ha1.idAdresse = ae.idAdresse
                    GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resAdresse = $this->connexionBdd->requete($reqAdresse);
                while($fetchAdresse = mysql_fetch_assoc($resAdresse))
                {
                    if($fetchAdresse['idVille']!='0')
                        $idVille = $fetchAdresse['idVille'];
                    
                    if($fetchAdresse['idRue']!='0')
                    {
                        $reqRue="
                                SELECT idVille 
                                FROM quartier 
                                WHERE idQuartier IN 
                                    (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier IN 
                                        (SELECT idSousQuartier FROM rue WHERE idRue='".$fetchAdresse['idRue']."')
                                    )
                                ";
                        $resRue = $this->connexionBdd->requete($reqRue);
                        $fetchRue = mysql_fetch_assoc($resRue);
                        $idVille = $fetchRue['idVille'];
                    }
                    
                    if($fetchAdresse['idQuartier']!='0')
                    {
                        $reqQuartier="
                            SELECT idVille FROM quartier WHERE idQuartier ='".$fetchAdresse['idQuartier']."'                        
                        ";
                        $resQuartier = $this->connexionBdd->requete($reqQuartier);
                        $fetchQuartier = mysql_fetch_assoc($resQuartier);
                        $idVille = $fetchQuartier['idVille'];
                    }
                    
                    if($fetchAdresse['idSousQuartier']!='0')
                    {
                        $reqSousQuartier = "SELECT idVille FROM quartier WHERE idQuartier IN (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier='".$fetchAdresse['idSousQuartier']."')";
                        $resSousQuartier = $this->connexionBdd->requete($reqSousQuartier);
                        $fetchSousQuartier = mysql_fetch_assoc($resSousQuartier);
                        $idVille = $fetchSousQuartier['idVille'];
                    }
                }
            
            break;
            case 'idAdresse':
                // recherche de la ville correspondante a l'idAdresse
                $reqAdresse = "
                    SELECT ha1.idRue as idRue,ha1.idQuartier as idQuartier,ha1.idSousQuartier as idSousQuartier,ha1.idVille as idVille
                    FROM historiqueAdresse ha1
                    LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                    WHERE ha1.idAdresse = '$id'
                    GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resAdresse = $this->connexionBdd->requete($reqAdresse);
                while($fetchAdresse = mysql_fetch_assoc($resAdresse))
                {
                    if($fetchAdresse['idVille']!='0')
                        $idVille = $fetchAdresse['idVille'];
                    
                    if($fetchAdresse['idRue']!='0')
                    {
                        $reqRue="
                                SELECT idVille 
                                FROM quartier 
                                WHERE idQuartier IN 
                                    (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier IN 
                                        (SELECT idSousQuartier FROM rue WHERE idRue='".$fetchAdresse['idRue']."')
                                    )
                                ";
                        $resRue = $this->connexionBdd->requete($reqRue);
                        $fetchRue = mysql_fetch_assoc($resRue);
                        $idVille = $fetchRue['idVille'];
                    }
                    
                    if($fetchAdresse['idQuartier']!='0')
                    {
                        $reqQuartier="
                            SELECT idVille FROM quartier WHERE idQuartier ='".$fetchAdresse['idQuartier']."'                        
                        ";
                        $resQuartier = $this->connexionBdd->requete($reqQuartier);
                        $fetchQuartier = mysql_fetch_assoc($resQuartier);
                        $idVille = $fetchQuartier['idVille'];
                    }
                    
                    if($fetchAdresse['idSousQuartier']!='0')
                    {
                        $reqSousQuartier = "SELECT idVille FROM quartier WHERE idQuartier IN (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier='".$fetchAdresse['idSousQuartier']."')";
                        $resSousQuartier = $this->connexionBdd->requete($reqSousQuartier);
                        $fetchSousQuartier = mysql_fetch_assoc($resSousQuartier);
                        $idVille = $fetchSousQuartier['idVille'];
                    }
                    
                }
                
            break;
            case 'idEvenementGroupeAdresse':
            case 'idEvenement':
                
                $evenement = new archiEvenement();
                $idEvenementGroupeAdresse = $evenement->getIdEvenementGroupeAdresseFromIdEvenement($id);
            
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($idEvenementGroupeAdresse);
                
                // recherche de la ville correspondante a l'idAdresse
                $reqAdresse = "
                    SELECT ha1.idRue as idRue,ha1.idQuartier as idQuartier,ha1.idSousQuartier as idSousQuartier,ha1.idVille as idVille
                    FROM historiqueAdresse ha1
                    LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
                    WHERE ha1.idAdresse = '".$idAdresse."'
                    GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resAdresse = $this->connexionBdd->requete($reqAdresse);
                while($fetchAdresse = mysql_fetch_assoc($resAdresse))
                {
                    if($fetchAdresse['idVille']!='0')
                        $idVille = $fetchAdresse['idVille'];
                    
                    if($fetchAdresse['idRue']!='0')
                    {
                        $reqRue="
                                SELECT idVille 
                                FROM quartier 
                                WHERE idQuartier IN 
                                    (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier IN 
                                        (SELECT idSousQuartier FROM rue WHERE idRue='".$fetchAdresse['idRue']."')
                                    )
                                ";
                        $resRue = $this->connexionBdd->requete($reqRue);
                        $fetchRue = mysql_fetch_assoc($resRue);
                        $idVille = $fetchRue['idVille'];
                    }
                    
                    if($fetchAdresse['idQuartier']!='0')
                    {
                        $reqQuartier="
                            SELECT idVille FROM quartier WHERE idQuartier ='".$fetchAdresse['idQuartier']."'                        
                        ";
                        $resQuartier = $this->connexionBdd->requete($reqQuartier);
                        $fetchQuartier = mysql_fetch_assoc($resQuartier);
                        $idVille = $fetchQuartier['idVille'];
                    }
                    
                    if($fetchAdresse['idSousQuartier']!='0')
                    {
                        $reqSousQuartier = "SELECT idVille FROM quartier WHERE idQuartier IN (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier='".$fetchAdresse['idSousQuartier']."')";
                        $resSousQuartier = $this->connexionBdd->requete($reqSousQuartier);
                        $fetchSousQuartier = mysql_fetch_assoc($resSousQuartier);
                        $idVille = $fetchSousQuartier['idVille'];
                    }
                    
                }
            break;
            case 'idRue':
                $req = "
                    SELECT v.idVille as idVille 
                    FROM ville v
                    LEFT JOIN quartier q ON q.idVille = v.idVille
                    LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                    LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                    WHERE r.idRue = '".$id."'
                    ";
                $res = $this->connexionBdd->requete($req);
                if(mysql_num_rows($res)==1)
                {   
                    $fetch = mysql_fetch_assoc($res);
                    $idVille = $fetch['idVille'];
                }
            break;
            case 'idSousQuartier':
                $req = "
                    SELECT v.idVille as idVille 
                    FROM ville v
                    LEFT JOIN quartier q ON q.idVille = v.idVille
                    LEFT JOIN sousQuartier sq ON sq.idQuartier = q.idQuartier
                    WHERE sq.idSousQuartier = '".$id."'
                
                ";
                
                $res = $this->connexionBdd->requete($req);
                if(mysql_num_rows($res)==1)
                {   
                    $fetch = mysql_fetch_assoc($res);
                    $idVille = $fetch['idVille'];
                }
            break;
            case 'idQuartier':
                $req = "
                    SELECT v.idVille as idVille 
                    FROM ville v
                    LEFT JOIN quartier q ON q.idVille = v.idVille
                    WHERE q.idQuartier = '".$id."'
                ";
                
                $res = $this->connexionBdd->requete($req);
                if(mysql_num_rows($res)==1)
                {   
                    $fetch = mysql_fetch_assoc($res);
                    $idVille = $fetch['idVille'];
                }
            break;
        }
        
        return $idVille;
    }
    
    // renvoi un tableau contenant des infos sur la ville en parametre
    public function getInfosVille($idVille=0,$params=array())
    {
        $fieldList = " * ";
        if(isset($params['fieldList']) && $params['fieldList']!='')
        {
            $fieldList = $params['fieldList'];
        }
        $req = "SELECT $fieldList 
                FROM ville v
                LEFT JOIN pays p ON p.idPays = v.idPays
                WHERE v.idVille = $idVille";
        $res = $this->connexionBdd->requete($req);
        return mysql_fetch_assoc($res);
    }
    
    // renvoi l'idVille a partir du nom de la ville en parametre
    public function getIdVilleFromNomVille($nomVille='')
    {
        $retour = 0;
        $req = "SELECT idVille FROM ville WHERE nom LIKE \"%".$nomVille."%\"";
        $res = $this->connexionBdd->requete($req);
        if(mysql_num_rows($res)>0)
        {   
            $fetch = mysql_fetch_assoc($res);
            $retour = $fetch['idVille'];
        }
        
        return $retour;
    }
    
    
    // affiche l'encars de la liste des adresses concernée par le groupe d'adresse courant sur le detail d'une adresse  
    public function getArrayEncartAdressesImmeublesAvantApres($params = array())
    {
        $html = "";

        
        $t = new Template('modules/archi/templates/');
        $t->set_filenames(array('encartAdresse'=>'encartAdresseDetailAdresse.tpl'));
        
        
        $i = new archiImage();
        if(isset($this->variablesGet['archiIdAdresse']))
        {
            $idAdresseCourante = $this->variablesGet['archiIdAdresse'];
        }
        else
        {
            $idAdresseCourante = $this->getIdAdresseFromIdEvenementGroupeAdresse($params['idEvenementGroupeAdresse']);
        }
        // recherche de l'image principale courante , sinon celle de position 1 , sinon celle par defaut
        // image centrale = image2
        $arrayImage2 = $this->getUrlImageFromAdresse(0,'moyen',array('idEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']));
        
        $isPhotoCentrale = false;
        $dimensions = array();
        if($arrayImage2['trouve'])
        {
            $isPhotoCentrale = true;
            list($w,$h) = getimagesize($this->getCheminPhysique()."images/moyen/".$arrayImage2['dateUpload']."/".$arrayImage2['idHistoriqueImage'].".jpg");
            $newWGrand = round(75*$w/100);
            $newHGrand = round(75*$h/100);
            
            $newWPetit = round(35*$w/100);
            $newHPetit = round(35*$h/100);
            
            $t->assign_vars(array('image2'=>"<div id='divImagePetit2' style='display:none;'><img src='".$arrayImage2['url']."' alt='' width=$newWPetit height=$newHPetit id='image2Petit'></div><div id='divImageGrand2' style='display:block;'><img src='getPhotoSquare.php?id=".$arrayImage2['idHistoriqueImage']."' alt=''  id='image2Grand' itemprop='image'></div>"));
            
            

        }
        else
        {
            // image par defaut , si aucune image liee a l'adresse
            $isPhotoCentrale = true;
            list($w,$h) = getimagesize($this->getUrlRacine()."getPhotoSquare.php");
            $newWGrand = round(75*$w/100);
            $newHGrand = round(75*$h/100);
            
            $newWPetit = round(35*$w/100);
            $newHPetit = round(35*$h/100);
            $t->assign_vars(array('image2'=>"<div id='divImagePetit2' style='display:none;'><img src='getPhotoSquare.php' alt='' width=$newWPetit height=$newHPetit id='image2Petit'></div><div id='divImageGrand2' style='display:block;'><img src='getPhotoSquare.php' alt='' width=$newWGrand height=$newHGrand id='image2Grand'></div>"));
        }

        
        // fabrication de la liste des adresses affichées sur l'encart
        // adresse courante affichée en rouge:
        //$txtAdresseCourante = $this->getIntituleAdresseFrom($idAdresseCourante,'idAdresse',array('noSousQuartier'=>true,'noQuartier'=>true,'noVille'=>true));
        
        // recherche des autres adresses du groupe d'adresse courant    
        //$txtAutreAdressesGroupeAdresse = "";
        $txtAdresses = "";
        $reqAdresseDuGroupeAdresse = "
            SELECT ha1.idAdresse as idAdresse,ha1.numero as numero, ha1.idRue as idRue, IF(ha1.idIndicatif='0','',i.nom) as nomIndicatif, ha1.idQuartier as idQuartier, ha1.idSousQuartier as idSousQuartier
            FROM historiqueAdresse ha2, historiqueAdresse ha1
            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
            LEFT JOIN indicatif i ON i.idIndicatif = ha1.idIndicatif
            WHERE ha2.idAdresse = ha1.idAdresse
            AND ae.idEvenement ='".$params['idEvenementGroupeAdresse']."'
            
            GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            ORDER BY ha1.numero,ha1.idRue
        ";//AND ha1.idAdresse<>'".$idAdresseCourante."'
        
        
        
        $resAdresseDuGroupeAdresse = $this->connexionBdd->requete($reqAdresseDuGroupeAdresse);
        if(mysql_num_rows($resAdresseDuGroupeAdresse)>0)
        {
            $arrayNumero = array();
            while($fetchAdressesGroupeAdresse = mysql_fetch_assoc($resAdresseDuGroupeAdresse))
            {
                $isAdresseCourante = false;
                if($idAdresseCourante == $fetchAdressesGroupeAdresse['idAdresse'])
                {
                    $isAdresseCourante = true;
                }
                
                if($fetchAdressesGroupeAdresse['idRue']=='0' || $fetchAdressesGroupeAdresse['idRue']=='')
                {
                    if($fetchAdressesGroupeAdresse['idQuartier']!='' && $fetchAdressesGroupeAdresse['idQuartier']!='0')
                    {
                        $arrayNumero[$this->getIntituleAdresseFrom($fetchAdressesGroupeAdresse['idAdresse'],'idAdresse',array('noSousQuartier'=>true,'noQuartier'=>false,'noVille'=>true))][] = array('indicatif'=>$fetchAdressesGroupeAdresse['nomIndicatif'],'numero'=>$fetchAdressesGroupeAdresse['numero'],'url'=>$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$fetchAdressesGroupeAdresse['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse'])),'isAdresseCourante'=>$isAdresseCourante);
                    }
                    
                    if($fetchAdressesGroupeAdresse['idSousQuartier']!='' && $fetchAdressesGroupeAdresse['idSousQuartier']!='0')
                    {
                        $arrayNumero[$this->getIntituleAdresseFrom($fetchAdressesGroupeAdresse['idAdresse'],'idAdresse',array('noSousQuartier'=>false,'noQuartier'=>true,'noVille'=>true))][] = array('indicatif'=>$fetchAdressesGroupeAdresse['nomIndicatif'],'numero'=>$fetchAdressesGroupeAdresse['numero'],'url'=>$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$fetchAdressesGroupeAdresse['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse'])),'isAdresseCourante'=>$isAdresseCourante);
                    }
                }
                else
                {
                    $arrayNumero[$this->getIntituleAdresseFrom($fetchAdressesGroupeAdresse['idRue'],'idRueWithNoNumeroAuthorized',array('noSousQuartier'=>true,'noQuartier'=>true,'noVille'=>true))][] = array('indicatif'=>$fetchAdressesGroupeAdresse['nomIndicatif'],'numero'=>$fetchAdressesGroupeAdresse['numero'],'url'=>$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$fetchAdressesGroupeAdresse['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse'])),'isAdresseCourante'=>$isAdresseCourante);
                }   
                //$txtAutreAdressesGroupeAdresse .= "<br><a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$fetchAutresAdressesGroupeAdresse['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']))."'>".$this->getIntituleAdresseFrom($fetchAutresAdressesGroupeAdresse['idAdresse'],'idAdresse',array('noSousQuartier'=>true,'noQuartier'=>true,'noVille'=>true))."</a>";
                
            }
        }
        
        // affichage adresses regroupees
        foreach($arrayNumero as $intituleRue => $arrayInfosNumero)
        {
            $isSelectedRue = false;
            $isUnSeulNumeroSurGroupeAdresse = false;
            if(count($arrayInfosNumero)==1)
            {// s'il n'y a qu'un seul numero dans le groupe d'adresse de la rue courante , on fait le lien href sur tout le texte de l'adresse , pas seulement sur le numero
                if($arrayInfosNumero[0]['numero']=='0')
                    $arrayInfosNumero[0]['numero'] = '';
                
                if($arrayInfosNumero[0]['isAdresseCourante']==true)
                {

                    $txtAdresses.="<a href='".$arrayInfosNumero[0]['url']."' style='font-weight:bold;'>".$arrayInfosNumero[0]['numero'].$arrayInfosNumero[0]['indicatif']." ".$intituleRue."</a><span style='color:#4b4b4b'>-</span>";
                    $isUnSeulNumeroSurGroupeAdresse = true;
                }
                else
                {
                    $txtAdresses.="<a href='".$arrayInfosNumero[0]['url']."'>".$arrayInfosNumero[0]['numero'].$arrayInfosNumero[0]['indicatif']." ".$intituleRue."</a><span style='color:#4b4b4b'>-</span>";
                    $isUnSeulNumeroSurGroupeAdresse = true;
                }
            }
            else
            {
                foreach($arrayInfosNumero as $indice => $infosNumero)
                {
                    if($infosNumero['isAdresseCourante']==true)
                    {
                        if($infosNumero['numero']=='' || $infosNumero['numero']=='0')
                        {
                            $isSelectedRue = true;
                        }
                        else
                        {
                            $txtAdresses.="<a href='".$infosNumero['url']."'>".$infosNumero['numero'].$infosNumero['indicatif']."</a><span style='color:#4b4b4b'>-</span>";
                            $isSelectedRue = true;
                        }
                    }
                    else
                    {
                        if($infosNumero['numero']=='' || $infosNumero['numero']=='0')
                        {
                            //rien
                        }
                        else
                        {
                            $txtAdresses.="<a href='".$infosNumero['url']."'>".$infosNumero['numero'].$infosNumero['indicatif']."</a><span style='color:#4b4b4b'>-</span>";
                        }
                    }
                }
            }
            
            $txtAdresses = pia_substr($txtAdresses,0,-(pia_strlen("<span style='color:#4b4b4b'>-</span>")));
            
            if(!$isUnSeulNumeroSurGroupeAdresse)
            {
                if($isSelectedRue)
                {
                    $txtAdresses.="<span >".$intituleRue."</span><br>";
                }
                else
                {
                    $txtAdresses.="<span style='color:#4B4B4B;'>".$intituleRue."</span><br>";
                }
            }
            else
            {
                $txtAdresses.="<br>";
            }
        }
        
        $txtAdresses = pia_substr($txtAdresses,0,-(pia_strlen("<br>")));
        
        $t->assign_vars(array('adresse2'=>$txtAdresses));
        
        
        // ensuite on recherche les groupes d'adresses autour de l'adresse courante, sans afficher les adresses du meme groupe d'adresse que le courant
        $arrayIdAdresses = $this->getArrayIdAdressesNearCurrentAdresse(array('idAdresse'=>$idAdresseCourante,'idEvenementGroupeAdresseCourant'=>$params['idEvenementGroupeAdresse']));
        if(isset($arrayIdAdresses['avant']['idAdresse']))
        {
            $infosImageAvant = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'avant','idAdresse'=>$arrayIdAdresses['avant']['idAdresse'],'idEvenementGroupeAdresse'=>$arrayIdAdresses['avant']['idEvenementGroupeAdresse']));
            $t->assign_vars(array('image1'=>$infosImageAvant['image']));
            $t->assign_vars(array('adresse1'=>$infosImageAvant['adresse']));
        }
        
        if(isset($arrayIdAdresses['apres']['idAdresse']))
        {
            $infosImageApres = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'apres','idAdresse'=>$arrayIdAdresses['apres']['idAdresse'],'idEvenementGroupeAdresse'=>$arrayIdAdresses['apres']['idEvenementGroupeAdresse']));
            $t->assign_vars(array('image3'=>$infosImageApres['image']));
            $t->assign_vars(array('adresse3'=>$infosImageApres['adresse']));
        }
        
        
        // si une adresse avant ou apres n'a pas ete trouvée , on va chercher les adresses les plus proches qui ne sont pas dans cette rue (car si elles sont dans cette rue , elles sont forcement dans le parcours
        if((isset($arrayIdAdresses['apres']['idAdresse']) && !isset($arrayIdAdresses['avant']['idAdresse'])) || (!isset($arrayIdAdresses['apres']['idAdresse']) && isset($arrayIdAdresses['avant']['idAdresse'])))
        {
            $arrayCoordonnees = $this->getCoordonneesFrom($idAdresseCourante,'idAdresse');
            $arrayRue = $this->getIdRuesFrom($idAdresseCourante,'idAdresse');
            $idVilleAdresseCourante = $this->getIdVilleFrom($idAdresseCourante,'idAdresse'); // idVilleCourante pour rester dans les adresses de la ville en cours , et pas avoir en resultat une adresse d'une autre ville
            
            if($arrayCoordonnees['latitude']=='')
                $arrayCoordonnees['latitude']=0;
            if($arrayCoordonnees['longitude']=='')
                $arrayCoordonnees['longitude']=0;
            
            
            $reqAdresseProche = "
                SELECT ha1.idAdresse as idAdresse, ((acos(sin(".$arrayCoordonnees['latitude']."*PI()/180) * sin(ha1.latitude*PI()/180) + cos(".$arrayCoordonnees['latitude']."*PI()/180) * cos(ha1.latitude*PI()/180) * cos((".$arrayCoordonnees['longitude']." - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000 as distance, ae.idEvenement as idEvenementGroupeAdresse
                FROM historiqueAdresse ha2, historiqueAdresse ha1
                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                LEFT JOIN rue r ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                LEFT JOIN ville v ON v.idVille = q.idVille
                WHERE
                    ha2.idAdresse = ha1.idAdresse
                AND ha1.idRue<>'".$arrayRue[0]."'
                AND v.idVille = '".$idVilleAdresseCourante."'
                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse,ee.idEvenement
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND count(ee.idEvenement)>0
                ORDER BY ((acos(sin(".$arrayCoordonnees['latitude']."*PI()/180) * sin(ha1.latitude*PI()/180) + cos(".$arrayCoordonnees['latitude']."*PI()/180) * cos(ha1.latitude*PI()/180) * cos((".$arrayCoordonnees['longitude']." - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000 ASC
                LIMIT 1
            ";
            
            $resAdresseProche = $this->connexionBdd->requete($reqAdresseProche);
            $fetchAdresseProche = mysql_fetch_assoc($resAdresseProche);
            
            // affichage de l'adresse
            if(!isset($arrayIdAdresses['avant']['idAdresse']))
            {
                $infosImageAvant = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'avant','idAdresse'=>$fetchAdresseProche['idAdresse'],'idEvenementGroupeAdresse'=>$fetchAdresseProche['idEvenementGroupeAdresse']));
                $t->assign_vars(array('image1'=>$infosImageAvant['image']));
                $t->assign_vars(array('adresse1'=>$infosImageAvant['adresse']));
            
            }
            elseif(!isset($arrayIdAdresses['apres']['idAdresse']))
            {
                $infosImageAvant = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'apres','idAdresse'=>$fetchAdresseProche['idAdresse'],'idEvenementGroupeAdresse'=>$fetchAdresseProche['idEvenementGroupeAdresse']));
                $t->assign_vars(array('image3'=>$infosImageAvant['image']));
                $t->assign_vars(array('adresse3'=>$infosImageAvant['adresse']));
            }
            
        
        }
        elseif(!isset($arrayIdAdresses['apres']['idAdresse']) && !isset($arrayIdAdresses['avant']['idAdresse'])) // s'il n'y pas ni adresse avant ni adresse apres, on prend l'adresse la plus proche qui n'est pas dans la rue , on la met a gauche , l'adresse la plus proche suivante on la met a droite
        {
            $arrayCoordonnees = $this->getCoordonneesFrom($idAdresseCourante,'idAdresse');
            $arrayRue = $this->getIdRuesFrom($idAdresseCourante,'idAdresse');
            $idVilleAdresseCourante = $this->getIdVilleFrom($idAdresseCourante,'idAdresse'); // idVilleCourante pour rester dans les adresses de la ville en cours , et pas avoir en resultat une adresse d'une autre ville
            
            if($arrayCoordonnees['latitude']=='')
                $arrayCoordonnees['latitude']=0;
            if($arrayCoordonnees['longitude']=='')
                $arrayCoordonnees['longitude']=0;
            
            $reqAdressesProches = "
                SELECT DISTINCT ae.idEvenement as idEvenementGroupeAdresse
                FROM _adresseEvenement ae
                LEFT JOIN historiqueAdresse ha1 ON ha1.idAdresse = ae.idAdresse
                LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse 
                LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement 
                LEFT JOIN rue r ON r.idRue = ha1.idRue
                LEFT JOIN sousQuartier sq ON sq.idSousQuartier = r.idSousQuartier
                LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
                LEFT JOIN ville v ON v.idVille = q.idVille              
                WHERE
                    ha2.idAdresse = ha1.idAdresse
                AND ha1.idRue<>'".$arrayRue[0]."'
                AND v.idVille = '".$idVilleAdresseCourante."'
                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse,ee.idEvenement
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)  AND count(ee.idEvenement)>0
                ORDER BY ((acos(sin(".$arrayCoordonnees['latitude']."*PI()/180) * sin(ha1.latitude*PI()/180) + cos(".$arrayCoordonnees['latitude']."*PI()/180) * cos(ha1.latitude*PI()/180) * cos((".$arrayCoordonnees['longitude']." - ha1.longitude)*PI()/180))/ pi() * 180.0)* 60 * 1.1515 * 1.609344)*1000 ASC
                
            ";
            
            $resAdressesProches = $this->connexionBdd->requete($reqAdressesProches);
            
            
            $fetchAdressesProches = mysql_fetch_assoc($resAdressesProches);
            
            $idAdresseAvant = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetchAdressesProches['idEvenementGroupeAdresse']);
            
            $infosImageAvant = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'avant','idAdresse'=>$idAdresseAvant,'idEvenementGroupeAdresse'=>$fetchAdressesProches['idEvenementGroupeAdresse']));
            if(isset($infosImageAvant['image']))
                $t->assign_vars(array('image1'=>$infosImageAvant['image']));
            if(isset($infosImageAvant['adresse']))
                $t->assign_vars(array('adresse1'=>$infosImageAvant['adresse']));
            
            $fetchAdressesProches = mysql_fetch_assoc($resAdressesProches);
            $idAdresseApres = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetchAdressesProches['idEvenementGroupeAdresse']);
            $infosImageApres = $this->getArrayInfosImageAvantOrApres(array('positionImage'=>'apres','idAdresse'=>$idAdresseApres,'idEvenementGroupeAdresse'=>$fetchAdressesProches['idEvenementGroupeAdresse']));
            if(isset($infosImageApres['image']))
                $t->assign_vars(array('image3'=>$infosImageApres['image']));
            if(isset($infosImageApres['adresse']))  
            $t->assign_vars(array('adresse3'=>$infosImageApres['adresse']));
            
        }
        
        $titre = $this->getIntituleAdresseFrom($params['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('afficheTitreSiTitreSinonRien'=>true));
        if($titre!='')
        {
            $t->assign_block_vars('isTitre',array('titre'=>$titre));
        }
        
        ob_start();
        $t->pparse('encartAdresse');
        $html .= ob_get_contents();
        ob_end_clean();
        
        // javascript a mettre dans le header , on le retourne donc dans la fonction
        $jsHeader = "
            <script  >
            function imageOver(numImage)
            {
                if(numImage==1)
                {
                    if(document.getElementById('divImagePetit1'))
                    {
                        document.getElementById('divImagePetit1').style.display='none';
                        document.getElementById('divImageGrand1').style.display='block';
                    }
                    
                    if(document.getElementById('divImagePetit2'))
                    {
                        document.getElementById('divImageGrand2').style.display='none';
                        document.getElementById('divImagePetit2').style.display='block';
                    }
                    
                    if(document.getElementById('divImagePetit3'))
                    {
                        document.getElementById('divImageGrand3').style.display='none';
                        document.getElementById('divImagePetit3').style.display='block';
                    }
                }
                
                if(numImage==2)
                {
                    /*document.getElementById('divImagePetit1').style.display='none';
                    document.getElementById('divImageGrand2').style.display='none';
                    document.getElementById('divImageGrand3').style.display='none';
                    document.getElementById('divImageGrand1').style.display='block';
                    document.getElementById('divImagePetit2').style.display='block';
                    document.getElementById('divImagePetit3').style.display='block';*/
                }
            
                if(numImage==3)
                {
                    if(document.getElementById('divImagePetit1'))
                    {
                        document.getElementById('divImagePetit1').style.display='none';
                        document.getElementById('divImageGrand1').style.display='block';
                    }
                    
                    if(document.getElementById('divImagePetit2'))
                    {
                        document.getElementById('divImageGrand2').style.display='none';
                        document.getElementById('divImagePetit2').style.display='block';
                    }
                    
                    if(document.getElementById('divImagePetit3'))
                    {
                        document.getElementById('divImageGrand3').style.display='block';
                        document.getElementById('divImagePetit3').style.display='none';
                    }
                }
            }

            function imageOut(numImage)
            {
                if(numImage==1)
                {
                    if(document.getElementById('divImagePetit2'))
                    {
                        document.getElementById('divImagePetit2').style.display='block';
                        document.getElementById('divImageGrand2').style.display='none';
                    }
                    
                    if(document.getElementById('divImagePetit3'))
                    {
                        document.getElementById('divImagePetit3').style.display='block';
                        document.getElementById('divImageGrand3').style.display='none';
                    }
                }
                else
                if(numImage==2)
                {
                    if(document.getElementById('divImagePetit1'))
                    {
                        document.getElementById('divImagePetit1').style.display='block';
                        document.getElementById('divImageGrand1').style.display='none';
                    }
                    
                    if(document.getElementById('divImagePetit3'))
                    {
                        document.getElementById('divImagePetit3').style.display='block';
                        document.getElementById('divImageGrand3').style.display='none';
                    }
                
                }
                else
                if(numImage==3)
                {
                    if(document.getElementById('divImagePetit1'))
                    {
                        document.getElementById('divImagePetit1').style.display='block';
                        document.getElementById('divImageGrand1').style.display='none';
                    }
                    
                    if(document.getElementById('divImagePetit2'))
                    {
                        document.getElementById('divImagePetit2').style.display='block';
                        document.getElementById('divImageGrand2').style.display='none';
                    }
                }
                else
                {
                
                    if(document.getElementById('divImagePetit1'))
                    {
                        document.getElementById('divImagePetit1').style.display='block';
                        document.getElementById('divImageGrand1').style.display='none';
                    }
                    
                    if(document.getElementById('divImagePetit2'))
                    {
                        document.getElementById('divImagePetit2').style.display='none';
                        document.getElementById('divImageGrand2').style.display='block';
                    }
                    
                    if(document.getElementById('divImagePetit3'))
                    {
                        document.getElementById('divImagePetit3').style.display='block';
                        document.getElementById('divImageGrand3').style.display='none';
                    }
                }

            
            }
            </script>
        ";
        
        $this->addToJsHeader($jsHeader);
        
        
        return array('html'=>$html,'isPhotoCentrale'=>$isPhotoCentrale);
    }
    
    
    // renvoi l'image et l'adresse des image pour l'adresse donnees pour l'affichage dans l'encars des adresses
    // l'image du milieu est géré différement , elle est donc recuperée a part dans la fonction appelante
    public function getArrayInfosImageAvantOrApres($params = array())
    {
        $retour = array();
        
        if(isset($params['idAdresse']) && $params['idAdresse']!='' && isset($params['positionImage']) && $params['positionImage']!='' && isset($params['idEvenementGroupeAdresse']) && $params['idEvenementGroupeAdresse']!='')
        {
            if($params['positionImage']=='avant')
            {
                $numeroImage=1;
            }
            
            if($params['positionImage']=='apres')
            {
                $numeroImage=3;  // le numero 2 correspond a l'image centrale, non gerée dans cette fonction
            }
            
            $arrayImage = $this->getUrlImageFromAdresse(0,'moyen',array('idEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']));
            
            if($arrayImage['trouve'])
            {
                list($w,$h) = getimagesize($this->getCheminPhysique()."images/moyen/".$arrayImage['dateUpload']."/".$arrayImage['idHistoriqueImage'].".jpg");
                    
                $newWGrand = round(75*$w/100);
                $newHGrand = round(75*$h/100);
                    
                $newWPetit = round(35*$w/100);
                $newHPetit = round(35*$h/100);
                
                
                $image = "<div id='divImagePetit".$numeroImage."' style='display:block;'><img src='".$arrayImage['url']."'  width=$newWPetit height=$newHPetit id='image".$numeroImage."Petit' alt=''></div><div id='divImageGrand".$numeroImage."' style='display:none;'><a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$params['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']))."'><img src='getPhotoSquare.php?id=".$arrayImage['idHistoriqueImage']."'   height=$newHGrand alt='' id='image".$numeroImage."Grand'></a></div>";
            }
            else
            {
                list($w,$h) = getimagesize($this->getCheminPhysique()."images/imageDefautArchiv2.jpg");
                $newWGrand = round(75*$w/100);
                $newHGrand = round(75*$h/100);
                    
                $newWPetit = round(35*$w/100);
                $newHPetit = round(35*$h/100);
                
                
                $image = "<div id='divImagePetit".$numeroImage."' style='display:block;'><img src='".$this->getUrlImage()."imageDefautArchiv2.jpg' alt='' width=$newWPetit height=$newHPetit id='image".$numeroImage."Petit'></div><div id='divImageGrand".$numeroImage."' style='display:none;'><a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$params['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']))."'><img alt='' src='".$this->getUrlImage()."imageDefautArchiv2.jpg'  width=$newWGrand height=$newHGrand id='image".$numeroImage."Grand'></a></div>";
            }
            $adresse = "<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdAdresse'=>$params['idAdresse'],'archiIdEvenementGroupeAdresse'=>$params['idEvenementGroupeAdresse']))."'>".$this->getIntituleAdresseFrom($params['idAdresse'],'idAdresse',array('noSousQuartier'=>true,'noQuartier'=>true,'noVille'=>true))."</a>";
            $retour = array('image'=>$image,'adresse'=>$adresse);
        }
    
        return $retour;
    }
    
    
    
    
    
    
    
    
    
    
    // renvoi les adresses situées autour de l'adresse courante ... ex : adresse courante = 3 rue de la ziegelau , =====> on renvoi le 2 rue de la ziegelau et le 4 , ci ceux ci existents , sinon on recherche le precedent et le suivant immediat
    // contrairement a la fonction getIdAdressesAutourAdressesCourante qui se base sur l'intitule de l'adresse sous forme de chaine de caractere , ici on se base sur le groupe d'adresse , donc c'est plus rapide pour l'affichage de l'encart
    public function getArrayIdAdressesNearCurrentAdresse($params = array())
    {
        $retour = array();
    
        if(isset($params['idAdresse']) && $params['idAdresse']!='' && isset($params['idEvenementGroupeAdresseCourant']) && $params['idEvenementGroupeAdresseCourant']!='')
        {
            // d'abord on va cherche l'idRue de l'adresse courante :
            $reqRue = "
                SELECT ha1.idRue as idRue,ha1.numero as numero, ha1.idSousQuartier as idSousQuartier, ha1.idQuartier as idQuartier, ha1.idIndicatif as idIndicatif
                FROM historiqueAdresse ha2, historiqueAdresse ha1
                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                WHERE 
                    ha2.idAdresse = ha1.idAdresse
                AND ha1.idAdresse = '".$params['idAdresse']."'
                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
            ";
            
            $resRue = $this->connexionBdd->requete($reqRue);
            
            $fetchRue = mysql_fetch_assoc($resRue);
            
            $idRue = $fetchRue['idRue'];
            $idSousQuartier = $fetchRue['idSousQuartier'];
            $idQuartier = $fetchRue['idQuartier'];
            $numero = $fetchRue['numero'];
            $idIndicatif = $fetchRue['idIndicatif'];
            if($idRue != '0' && $numero != '0' && $idRue!='' && $numero !='')
            {
                $reqNumeroAvant = "
                    SELECT max(ha.numero) as numero,ha.idIndicatif as idIndicatif, count(ee.idEvenementAssocie)
                    FROM historiqueAdresse ha
                    LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    WHERE 
                        ha.idRue='$idRue'
                    AND (ha.numero<$numero OR (ha.numero='$numero' AND ha.idIndicatif<'$idIndicatif' AND ha.idIndicatif<>'') OR ('$idIndicatif'<>'' AND ha.idIndicatif='' AND ha.numero='$numero' ) )
                    AND ha.numero<>''
                    AND ha.numero<>'0'
                    AND ha.numero IS NOT NULL
                    AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                    GROUP BY ha.idRue,ee.idEvenementAssocie
                    HAVING count(ee.idEvenementAssocie)>0
                    ORDER BY ha.numero DESC, ha.idIndicatif DESC
                    LIMIT 1
                ";
                $resNumeroAvant = $this->connexionBdd->requete($reqNumeroAvant);
                $fetchNumeroAvant = mysql_fetch_assoc($resNumeroAvant);
                if(isset($fetchNumeroAvant['numero']))
                {
                    $reqAvant = "
                        SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                        FROM historiqueAdresse ha2, historiqueAdresse ha1
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        WHERE
                            ha2.idAdresse = ha1.idAdresse
                        AND ha1.idRue='$idRue'
                        AND ha1.idIndicatif='".$fetchNumeroAvant['idIndicatif']."'
                        AND ha1.numero='".$fetchNumeroAvant['numero']."'
                        AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                        GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    
                    ";
                    
                    
                    $resAvant = $this->connexionBdd->requete($reqAvant);
                    
                    $fetchAvant = mysql_fetch_assoc($resAvant);
                    
                    $retour['avant'] = $fetchAvant;
                }
                
                $reqNumeroApres = "
                    SELECT ha.numero as numero,ha.idIndicatif as idIndicatif,count(ee.idEvenementAssocie)
                    FROM historiqueAdresse ha
                    LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                    LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                    WHERE 
                        ha.idRue='$idRue'
                    AND (
                            ha.numero>'$numero' OR 
                            (ha.numero='$numero' AND ha.idIndicatif>'$idIndicatif' AND ha.idIndicatif<>'')
                            
                        )
                    AND ha.numero<>''
                    AND ha.numero<>'0'
                    AND ha.numero IS NOT NULL
                    AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                    GROUP BY ha.idRue,ee.idEvenementAssocie
                    HAVING count(ee.idEvenementAssocie)>0
                    ORDER BY ha.numero ASC,ha.idIndicatif  ASC
                    LIMIT 1
                ";
                
                $resNumeroApres = $this->connexionBdd->requete($reqNumeroApres);
                $fetchNumeroApres = mysql_fetch_assoc($resNumeroApres);
                if(isset($fetchNumeroApres['numero']))
                {
                    
                    
                    $reqApres = "
                        SELECT ha1.idAdresse as idAdresse, ae.idEvenement as idEvenementGroupeAdresse
                        FROM historiqueAdresse ha2, historiqueAdresse ha1
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                        WHERE
                            ha2.idAdresse = ha1.idAdresse
                        AND ha1.idRue='$idRue'
                        AND ha1.numero='".$fetchNumeroApres['numero']."'
                        AND ha1.idIndicatif = '".$fetchNumeroApres['idIndicatif']."'
                        AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                        GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                        HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                    
                    ";
                    
                    $resApres = $this->connexionBdd->requete($reqApres);
                    
                    $fetchApres = mysql_fetch_assoc($resApres);
                    $retour['apres'] = $fetchApres;
                }
            }
            else
            {
                // rue sans numero
                if(($numero=='0' || $numero=='') && $idRue!='' && $idRue!='0')
                {
                    // si pas de numero , on va chercher les deux premieres adresses de la rue
                    $reqPremiersNumeros = "
                        SELECT distinct ha.numero, count(ee.idEvenementAssocie)
                        FROM historiqueAdresse ha
                        LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                        LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                        WHERE 
                            ha.idRue='$idRue'
                        AND ha.numero<>''
                        AND ha.numero<>'0'
                        AND ha.numero IS NOT NULL
                        AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                        GROUP BY ha.idRue,ee.idEvenementAssocie
                        HAVING count(ee.idEvenementAssocie)>0
                        ORDER BY ha.numero ASC
                        LIMIT 2
                    ";
                    
                    $resPremiersNumeros = $this->connexionBdd->requete($reqPremiersNumeros);
                    
                    if(mysql_num_rows($resPremiersNumeros)==1)
                    {
                        $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                        
                        // une seule adresse trouvee pour la rue
                        $reqAvant = "
                            SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                            FROM historiqueAdresse ha2, historiqueAdresse ha1
                            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                            WHERE
                                ha2.idAdresse = ha1.idAdresse
                            AND ha1.idRue='$idRue'
                            AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                            AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                            GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                        
                        ";
                        
                        $resAvant = $this->connexionBdd->requete($reqAvant);
                        $fetchAvant = mysql_fetch_assoc($resAvant);
                        $retour['avant'] = $fetchAvant;
                        
                    }
                    elseif(mysql_num_rows($resPremiersNumeros)==2)
                    {
                        // ok deux adresses trouvees pour la rue
                        $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                        $reqAvant = "
                            SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                            FROM historiqueAdresse ha2, historiqueAdresse ha1
                            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                            WHERE
                                ha2.idAdresse = ha1.idAdresse
                            AND ha1.idRue='$idRue'
                            AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                            AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                            GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                        
                        ";
                        
                        $resAvant = $this->connexionBdd->requete($reqAvant);
                        $fetchAvant = mysql_fetch_assoc($resAvant);
                        $retour['avant'] = $fetchAvant;
                        
                        $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                        $reqApres = "
                            SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                            FROM historiqueAdresse ha2, historiqueAdresse ha1
                            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                            WHERE
                                ha2.idAdresse = ha1.idAdresse
                            AND ha1.idRue='$idRue'
                            AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                            AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                            GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                            HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                        
                        ";
                        
                        $resApres = $this->connexionBdd->requete($reqApres);
                        $fetchApres = mysql_fetch_assoc($resApres);
                        $retour['apres'] = $fetchApres;
                        
                        
                        
                    }
                    else
                    {
                        // aucune adresse trouvee pour la rue
                    }
                    
                }
                elseif(($numero=='0' || $numero=='') && ($idRue=='' || $idRue=='0'))
                {
                    // quartier sousQuartier ou ville ?
                    if($idQuartier!='' && $idQuartier!='0')
                    {
                        // si pas de numero , on va chercher les deux premieres adresses de la rue
                        $reqPremiersNumeros = "
                            SELECT distinct ha.numero,r.idRue as idRue, count(ee.idEvenementAssocie)
                            FROM historiqueAdresse ha
                            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                            LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                            LEFT JOIN sousQuartier sq ON sq.idQuartier = '$idQuartier'
                            LEFT JOIN rue r ON r.idSousQuartier = sq.idSousQuartier
                            WHERE 
                            ha.idRue = r.idRue
                            AND ha.numero<>''
                            AND ha.numero<>'0'
                            AND ha.numero IS NOT NULL
                            AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                            GROUP BY ha.idRue,ee.idEvenementAssocie
                            HAVING count(ee.idEvenementAssocie)>0
                            ORDER BY ha.numero ASC
                            LIMIT 2
                        ";
                        
                        $resPremiersNumeros = $this->connexionBdd->requete($reqPremiersNumeros);
                        
                        if(mysql_num_rows($resPremiersNumeros)==1)
                        {
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            
                            // une seule adresse trouvee pour la rue
                            $reqAvant = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resAvant = $this->connexionBdd->requete($reqAvant);
                            $fetchAvant = mysql_fetch_assoc($resAvant);
                            $retour['avant'] = $fetchAvant;
                            
                        }
                        elseif(mysql_num_rows($resPremiersNumeros)==2)
                        {
                            // ok deux adresses trouvees pour la rue
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            $reqAvant = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resAvant = $this->connexionBdd->requete($reqAvant);
                            $fetchAvant = mysql_fetch_assoc($resAvant);
                            $retour['avant'] = $fetchAvant;
                            
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            $reqApres = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resApres = $this->connexionBdd->requete($reqApres);
                            $fetchApres = mysql_fetch_assoc($resApres);
                            $retour['apres'] = $fetchApres;
                            
                            
                            
                        }
                        else
                        {
                            // aucune adresse trouvee pour la rue
                        }
                    }
                    elseif($idSousQuartier!='' && $idSousQuartier!='0')
                    {
                        // si pas de numero , on va chercher les deux premieres adresses de la rue
                        $reqPremiersNumeros = "
                            SELECT distinct ha.numero,r.idRue as idRue, count(ee.idEvenementAssocie)
                            FROM historiqueAdresse ha
                            LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha.idAdresse
                            LEFT JOIN _evenementEvenement ee ON ee.idEvenement = ae.idEvenement
                            LEFT JOIN rue r ON r.idSousQuartier = '$idSousQuartier'
                            WHERE 
                                ha.idRue=r.idRue
                            AND ha.numero<>''
                            AND ha.numero<>'0'
                            AND ha.numero IS NOT NULL
                            AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                            GROUP BY ha.idRue,ee.idEvenementAssocie
                            HAVING count(ee.idEvenementAssocie)>0
                            ORDER BY ha.numero ASC
                            LIMIT 2
                        ";
                        
                        $resPremiersNumeros = $this->connexionBdd->requete($reqPremiersNumeros);
                        
                        if(mysql_num_rows($resPremiersNumeros)==1)
                        {
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            
                            // une seule adresse trouvee pour la rue
                            $reqAvant = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resAvant = $this->connexionBdd->requete($reqAvant);
                            $fetchAvant = mysql_fetch_assoc($resAvant);
                            $retour['avant'] = $fetchAvant;
                            
                        }
                        elseif(mysql_num_rows($resPremiersNumeros)==2)
                        {
                            // ok deux adresses trouvees pour la rue
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            $reqAvant = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resAvant = $this->connexionBdd->requete($reqAvant);
                            $fetchAvant = mysql_fetch_assoc($resAvant);
                            $retour['avant'] = $fetchAvant;
                            
                            $fetchPremiersNumeros = mysql_fetch_assoc($resPremiersNumeros);
                            $reqApres = "
                                SELECT ha1.idAdresse as idAdresse,ae.idEvenement as idEvenementGroupeAdresse
                                FROM historiqueAdresse ha2, historiqueAdresse ha1
                                LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
                                WHERE
                                    ha2.idAdresse = ha1.idAdresse
                                AND ha1.idRue='".$fetchPremiersNumeros['idRue']."'
                                AND ha1.numero='".$fetchPremiersNumeros['numero']."'
                                AND ae.idEvenement<>'".$params['idEvenementGroupeAdresseCourant']."'
                                GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                                HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                            
                            ";
                            
                            $resApres = $this->connexionBdd->requete($reqApres);
                            $fetchApres = mysql_fetch_assoc($resApres);
                            $retour['apres'] = $fetchApres;
                        }
                        else
                        {
                            // aucune adresse trouvee pour la rue
                        }
                    }
                }
            }
        }
        
        return $retour;
    }
    
    // enregistrement de nouvelle coordonnées googlemap provenant de la carte google map agrandie
    public function enregistreNouvellesCoordonneesAdresseGoogleMapEtVerrouillage($params = array())
    {
        $retour = true;
        
        if(isset($this->variablesPost['idEvenementGroupeAdresseCourant']) && $this->variablesPost['idEvenementGroupeAdresseCourant']!=''  && $this->variablesPost['idEvenementGroupeAdresseCourant']!='0' && isset($this->variablesPost['idAdresseCourante']) && $this->variablesPost['idAdresseCourante']!='' && $this->variablesPost['idAdresseCourante']!='0' && isset($this->variablesPost['longitudeUser']) && $this->variablesPost['longitudeUser']!='' && isset($this->variablesPost['latitudeUser']) && $this->variablesPost['latitudeUser']!='')
        {
        
            // s'il y a plusieurs groupes d'adresses liés a la meme adresse , on enregistre les coordonnees du groupe d'adresse courant dans la table _adresseEvenement, ainsi on aura la meme adresse affichée plusieurs fois a des endroits differents
            // exemple : pour la place de la republique , il y a 3 groupes d'adresses différents, que l'on affiche pas regroupés mais séparés
            $reqGroupesAdresse = "SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$this->variablesPost['idAdresseCourante']."'";
            $resGroupesAdresse = $this->connexionBdd->requete($reqGroupesAdresse);
            
            if(mysql_num_rows($resGroupesAdresse)>1 && $this->variablesPost['idEvenementGroupeAdresseCourant']!='')
            {
                // plusieurs groupes d'adresses reliés a l'adresse , on ne s'occupe que du groupe d'adresse courant
                // on va donc mettre a jour les coordonnées dans la table _adresseEvenement
                $reqUpdateCoordonneesGroupeAdresse = "UPDATE _adresseEvenement set longitudeGroupeAdresse='".$this->variablesPost['longitudeUser']."', latitudeGroupeAdresse='".$this->variablesPost['latitudeUser']."' WHERE idAdresse='".$this->variablesPost['idAdresseCourante']."' AND idEvenement='".$this->variablesPost['idEvenementGroupeAdresseCourant']."'";
                $resUpdateCoordonneesGroupeAdresse = $this->connexionBdd->requete($reqUpdateCoordonneesGroupeAdresse);
            }
            else
            {
                // il n'y a qu'un groupe d'adresse relié a l'adresse
                // recherche de l'idHistoriqueAdresse
                $reqHistoriqueAdresse = "
                    SELECT ha1.idHistoriqueAdresse as idHistoriqueAdresse
                    FROM historiqueAdresse ha2, historiqueAdresse ha1
                    WHERE 
                        ha2.idAdresse = ha1.idAdresse
                    AND ha1.idAdresse = '".$this->variablesPost['idAdresseCourante']."'
                    GROUP BY ha1.idAdresse, ha1.idHistoriqueAdresse
                    HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)
                ";
                
                $resHistoriqueAdresse = $this->connexionBdd->requete($reqHistoriqueAdresse);
                $fetchHistoriqueAdresse = mysql_fetch_assoc($resHistoriqueAdresse);
                $idHistoriqueAdresse = $fetchHistoriqueAdresse['idHistoriqueAdresse'];
                
                
                
                
                
                
                // mise a jour des coordonnées
                if(isset($idHistoriqueAdresse) && $idHistoriqueAdresse!='' && $idHistoriqueAdresse!='0')
                {
                    $reqUpdate = "UPDATE historiqueAdresse SET longitude='".$this->variablesPost['longitudeUser']."',latitude='".$this->variablesPost['latitudeUser']."',coordonneesVerrouillees='1' WHERE idHistoriqueAdresse='".$idHistoriqueAdresse."' ";
                    $resUpdate = $this->connexionBdd->requete($reqUpdate);
                }
                else
                {
                    echo "<script  >alert(\"Erreur de récupération de l'adresse courante. Veuillez contacter l'administrateur.\");</script>";
                    $retour = false;
                }
            }
        }
        else
        {
            echo "<script  >alert(\"Paramètre manquant pour la modification. Veuillez contacter l'administrateur.\");</script>";
            $retour = false;
        }
        
        return $retour;
    }
    
    // renvoi du code javascript pour l'ajout de nouveaux points sur la carte google map dont le centre a changé
    public function getJsGoogleMapNewCenter($params = array())
    {
        //$googleMap = new googleMap(array('googleMapKey'=>$this->googleMapKey));
        $retour="";
        //$retour=  $googleMap->getJsFunctions();
        $rayon = 250;
        if(isset($this->variablesGet['rayon']) && $this->variablesGet['rayon']!='')
        {
            $rayon = $this->variablesGet['rayon'];
        }
        
        
        $arrayConfigCoordonnees = $this->getArrayGoogleMapConfigCoordonneesFromCenter(array('urlRedirectedToParent'=>true,'longitude'=>$this->variablesGet['longitudeCenter'],'latitude'=>$this->variablesGet['latitudeCenter'],'rayon'=>$rayon));
        
        
        if(isset($this->variablesGet['noRefresh']) && $this->variablesGet['noRefresh']=='1')
        {
        
        }
        else
        {
            $retour .="map.clearOverlays();";
        }
        
        
        
        if(isset($this->variablesGet['noRefresh']) && $this->variablesGet['noRefresh']=='1')
        {
            // pas besoin de replace le markeur central
        }
        else
        {
            // on replace le markeur indiquant l'adresse courante
            if(isset($this->variablesGet['latitudeHome']) && isset($this->variablesGet['longitudeHome']) && $this->variablesGet['latitudeHome']!='' && $this->variablesGet['longitudeHome']!='')
            {
                $retour.="
                
                var iconHome = new GIcon();
                    
                iconHome.image = \"".$this->getUrlImage()."placeMarker.png\";
                //iconHome.shadow = \"http://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                iconHome.iconSize = new GSize(19, 32);
                iconHome.shadowSize = new GSize(22, 20);
                iconHome.iconAnchor = new GPoint(5, 26);
                iconHome.infoWindowAnchor = new GPoint(5, 1);
                
                markerHome = new GMarker(new GLatLng(".$this->variablesGet['latitudeHome'].",".$this->variablesGet['longitudeHome']."),{icon:iconHome});
                map.addOverlay(markerHome);
                ";
                
            }
        }
        
        $retour.="
                var icon = new GIcon();
                //icon.image = image;
                
            
                icon.image = '".$this->getUrlImage()."pointGM.png';
                icon.shadow = '';
                icon.iconSize = new GSize(9, 9);
                icon.shadowSize = new GSize(22, 20);
                icon.iconAnchor = new GPoint(0, 0); // 2,24
                icon.infoWindowAnchor = new GPoint(5, 1);
                var iconMarker = new GIcon(icon);"
                ;
        foreach($arrayConfigCoordonnees['arrayConfigCoordonnees'] as $indice => $values)
        {
            $retour.="
                    point$indice = new GLatLng(".$values['latitude'].",".$values['longitude'].");
                    marker$indice = new GMarker(point$indice,iconMarker);
                    overlay$indice = map.addOverlay(marker$indice);";
            $retour.="                  var eLabel$indice = new ELabel(point$indice,\"".str_replace("\"","&quot;",$values['label'])."\",\"styleLabelGoogleMap\");
                    eLabel$indice.pixelOffset = new GSize(20,-10);
                    map.addOverlay(eLabel$indice);
                    eLabel$indice.hide();";
            $retour.="function onClickFunction$indice(overlay, point){currentMarker = marker$indice; currentLabel=eLabel$indice; ".$values['jsCodeOnClickMarker']."}";
                    $retour.="GEvent.addListener(marker$indice, 'click', onClickFunction$indice);";
                
                
                if(isset($values['jsCodeOnMouseOverMarker']))
                {
                    $retour.="function onMouseOverFunction$indice(overlay,point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOverMarker']."}";
                    $retour.="GEvent.addListener(marker$indice,'mouseover',onMouseOverFunction$indice);";
                
                }
                
                if(isset($values['jsCodeOnMouseOutMarker']))
                {
                    $retour.="function onMouseOutFunction$indice(overlay,point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOutMarker']."}";
                    $retour.="GEvent.addListener(marker$indice,'mouseout',onMouseOutFunction$indice);";
                }
        }
        
        return $retour;
    }
    
    // affiche la google map des parcours
    public function getGoogleMapParcours($params = array())
    {
        $html="";
        $idParcours = 0;
        if(isset($this->variablesGet['archiIdParcours']) && $this->variablesGet['archiIdParcours']!='')
        {
            $idParcours = $this->variablesGet['archiIdParcours'];
        }
        
        if(isset($params['idParcours']) && $params['idParcours']!='')
        {
            $idParcours = $params['idParcours'];
        }
        
        $width=500;
        if(isset($params['width']) && $params['width']!='')
        {
            $width = $params['width'];
        }
        
        if($idParcours!=0)
        {
        
            $gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>$width));
            $html.=$gm->getJsFunctions();
            
            
            $reqParcours = "SELECT idEtape,idEvenementGroupeAdresse,position FROM etapesParcoursArt WHERE idParcours='".$idParcours."' ORDER BY position ASC";
            $resParcours = $this->connexionBdd->requete($reqParcours);
            
            $numEtape=1;
            $arrayEtapes = array();
            $i=0;
            while($fetchParcours = mysql_fetch_assoc($resParcours))
            {
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetchParcours['idEvenementGroupeAdresse']);
                $coordonnees = $this->getCoordonneesFrom($fetchParcours['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse');
                $intituleAdresse = $this->getIntituleAdresseFrom($fetchParcours['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse'); // attention ceci concernera une adresse parmi plusieurs possible dans le groupe d'adresse , a modifier
                $intituleAdresseRechercheGeo = $this->getIntituleAdresseFrom($fetchParcours['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('noQuartier'=>true,'noSousQuartier'=>true));
                $arrayEtapes[$i]['latitude']=$coordonnees['latitude'];
                $arrayEtapes[$i]['longitude']=$coordonnees['longitude'];
                $arrayEtapes[$i]['libelle']='';
                $arrayEtapes[$i]['adresseForGeolocalisation'] = $intituleAdresseRechercheGeo;
                $arrayEtapes[$i]['label']=$intituleAdresse;
                $arrayEtapes[$i]['jsCodeOnMouseOverMarker'] = "currentLabel.show();";
                $arrayEtapes[$i]['jsCodeOnMouseOutMarker'] = "currentLabel.hide();";
                $arrayEtapes[$i]['jsCodeOnClickMarker'] = "location.href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail',"archiIdAdresse"=>$idAdresse,"archiIdEvenementGroupeadresse"=>$fetchParcours['idEvenementGroupeAdresse']), false, false)."';";
                $numEtape++;
                $i++;
            }
            
            if(isset($params['getCoordonneesParcours']) && $params['getCoordonneesParcours']==true)
            {
                $html.= $gm->getMap(array('idDivDisplayEtapesText'=>'parcoursDetail','travelMode'=>'walking','listeCoordonneesParcours'=>$arrayEtapes,'urlImageIcon'=>$this->getUrlImage()."pointGM.png",'pathImageIcon'=>$this->getCheminPhysique()."images/pointGM.png",'getCoordonneesParcours'=>true,'actionFormOnSubmitVertices'=>$this->creerUrl('','',array()),'noDisplayParcoursGoogleAutomaticDescription'=>true));
            }
            else
            {
                $polyline=mysql_fetch_object($this->connexionBdd->requete("SELECT trace, levels FROM parcoursArt WHERE idParcours = '$idParcours'"));

                $html.= $gm->getMap(array('idDivDisplayEtapesText'=>'parcoursDetail','travelMode'=>'walking','listeCoordonneesParcours'=>$arrayEtapes,'urlImageIcon'=>$this->getUrlImage()."pointGM.png",'pathImageIcon'=>$this->getCheminPhysique()."images/pointGM.png", "polyline"=>$polyline->trace, "levels"=>$polyline->levels));
            }
            
            if(isset($this->variablesPost['submitVertices']))
            {
                // on recupere donc les vertices du parcours
                // enregistrement dans la table et reperage des etapes
                
                // d'abord on supprime les valeurs precedentes
                $reqSupprVertices = "DELETE FROM verticesParcours WHERE idParcours='".$idParcours."'";
                $resSupprVertices = $this->connexionBdd->requete($reqSupprVertices);
                foreach($this->variablesPost['longitudes'] as $indice => $valueLongitude)
                {
                    $valueLatitude = $this->variablesPost['latitudes'][$indice];
                    
                    $reqAddVertices = "INSERT INTO verticesParcours (idParcours,idEtape,longitude,latitude,position) VALUES ('".$idParcours."','0','".$valueLongitude."','".$valueLatitude."','".($indice+1)."')";
                    $resAddVertices = $this->connexionBdd->requete($reqAddVertices);
                }
                
                // ensuite on affiche chacun des points avec des marqueurs deplacables
                
            }
        }
        
        return $html;
    }
    
    public function getParcoursListe($params = array())
    {
        $html = "";
        
        $page = new archiPage(9, LANG);
        
        // liste des parcours
        $html.='<h2>'.$page->title.'</h2>';
        
        $resParcours = $this->getMysqlParcours(array('sqlOrderBy'=>'ORDER BY dateAjoutParcours DESC, idParcours DESC'));
        
        $t = new tableau();
        
        if(mysql_num_rows($resParcours)==0)
        {
            $html.="Aucun parcours n'est disponible pour le moment.";
        }
        
        $s = new stringObject();
        
        
        $html.=stripcslashes($page->content);
        
        $i=0;
        while($fetchParcours = mysql_fetch_assoc($resParcours))
        {
            $photoTrouvee = false;
            $photo = "&nbsp;";
            
            $urlParcours = $this->creerUrl('','detailParcours',array('archiIdParcours'=>$fetchParcours['idParcours']));
            
            //if($i==0)
            //{
                // recuperation des etapes pour afficher la premiere photo rencontree
                $reqEtapes = "SELECT idEtape, commentaireEtape FROM etapesParcoursArt WHERE idParcours='".$fetchParcours['idParcours']."'";
                $resEtapes = $this->connexionBdd->requete($reqEtapes);
                $commentaire = "";
                $isCommentaire = false;
                if(mysql_num_rows($resEtapes)>0)
                {
                    while(!$photoTrouvee && $fetchEtapes = mysql_fetch_assoc($resEtapes))
                    {
                        $arrayPhoto = $this->getPhotoFromEtape(array('idEtape'=>$fetchEtapes['idEtape']));
                        
                        if($arrayPhoto['trouve']==true)
                        {
                            $photoTrouvee = true;
                            
                            $photo = "<a href='".$urlParcours."'><img src='".$arrayPhoto['url']."' border=0></a>";
                        }
                        
                        if(!$isCommentaire)
                        {
                            if($fetchEtapes['commentaireEtape']!='')
                            {
                                $commentaire = $fetchEtapes['commentaireEtape'];
                                $isCommentaire = true;
                            }
                        }
                    }
                }
            //}

            $t->addValue($photo);
            $t->addValue("<a href='".$urlParcours."'>".stripslashes($fetchParcours['libelleParcours'])."</a><br>".$s->coupureTexte($s->sansBalisesHtml(stripslashes($commentaire)),10)."<br>".mysql_num_rows($resEtapes)." étapes");
            
            $i++;
        }
        
        $html.=$t->createHtmlTableFromArray(2);
        
        return $html;
    }
    
    public function getParcoursDetail($params = array())
    {
        $html = "";
        $idParcours = 0;
        if(isset($this->variablesGet['archiIdParcours']) && $this->variablesGet['archiIdParcours']!='')
        {
            $idParcours = $this->variablesGet['archiIdParcours'];
        }
        
        if(isset($params['idParcours']) && $params['idParcours']!='')
        {
            $idParcours = $params['idParcours'];
        }
        
        if($idParcours!=0)
        {
            $bbCode = new bbCodeObject();
            
            $resParcours = $this->getMysqlParcours(array('sqlWhere'=>"AND idParcours='".$idParcours."'"));

            $fetchParcours = mysql_fetch_assoc($resParcours);
        
            $html.="<h1>".stripslashes($fetchParcours['libelleParcours'])."</h1>";
            $html.=$this->getGoogleMapParcours(array('idParcours'=>$idParcours,'width'=>700));
            
            // affichage de la liste des etapes
            $alphaChars = 'abcdefghijklmnopqrstuvwxyz'; // on fait simple
            
            $reqEtapes = "SELECT idEtape,idEvenementGroupeAdresse,commentaireEtape,position FROM etapesParcoursArt WHERE idParcours='".$idParcours."' ORDER BY position ASC";
            $resEtapes = $this->connexionBdd->requete($reqEtapes);
            $t = new tableau();
            
            $i=0;
            while($fetchEtapes = mysql_fetch_assoc($resEtapes))
            {
            
                $arrayPhoto = $this->getPhotoFromEtape(array('idEtape'=>$fetchEtapes['idEtape']));
                $photo = "&nbsp;";
                if($arrayPhoto['trouve'])
                {
                    $photo = "<img src='".$arrayPhoto['url']."' border=0>";
                }
            
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetchEtapes['idEvenementGroupeAdresse']);
                
                $intituleAdresse = $this->getIntituleAdresseFrom($fetchEtapes['idEvenementGroupeAdresse'],'idEvenementGroupeAdresse',array('setSeparatorAfterTitle'=>'<br>','displayFirstTitreAdresse'=>true,'noVille'=>true,'noQuartier'=>true,'noSousQuartier'=>true));
                
                
                $marqueur = "<div style=\"font-size:10px; padding-top:3px; width:20px; height:34px; background-repeat:no-repeat; background-image:url(".$this->getUrlImage()."greenMarkerGM.gif); font-weight:bold; text-align:center;\">".($i+1)."</div>";
            
                $t->addValue($marqueur);
                $t->addValue("<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdEvenementGroupeAdresse'=>$fetchEtapes['idEvenementGroupeAdresse'],'archiIdAdresse'=>$idAdresse))."'>$photo</a>", "align=center");
                $t->addValue("<a href='".$this->creerUrl('','',array('archiAffichage'=>'adresseDetail','archiIdEvenementGroupeAdresse'=>$fetchEtapes['idEvenementGroupeAdresse'],'archiIdAdresse'=>$idAdresse))."'>".$intituleAdresse."</a>");
                $t->addValue(stripslashes($bbCode->convertToDisplay(array('text'=>$fetchEtapes['commentaireEtape']))));
                $i++;
            }
            
            require_once(__DIR__.'/archiParcours.php');
            $parcours = new ArchiParcours($_GET['archiIdParcours']);
            $html.='<p>'.$parcours->desc.'</p>';
            $html.= "<br><h2>Etapes du parcours : </h2>";
            $html.= $t->createTable(4);
            
        }
        
        return $html;
    }
    
    

    
    public function getMysqlParcours($params = array())
    {
        $sqlWhere = "";
        if(isset($params['sqlWhere']) && $params['sqlWhere']!='')
        {
            $sqlWhere = $params['sqlWhere'];
        }
        
        $sqlOrderBy = "";
        if(isset($params['sqlOrderBy']) && $params['sqlOrderBy']!='')
        {
            $sqlOrderBy = $params['sqlOrderBy'];
        }
        $req = "
            SELECT idParcours, libelleParcours, isActif
            FROM parcoursArt
            WHERE 1=1
            $sqlWhere
            $sqlOrderBy
            ";
        
        return $this->connexionBdd->requete($req);
    }
    
    public function supprimerAdresseFromAdminRue($params = array())
    {
        if(isset($this->variablesGet['idAdresseSuppr']) && $this->variablesGet['idAdresseSuppr']!='')
        {
            $erreurObject = new objetErreur();
            
            $idAdresseSuppr = $this->variablesGet['idAdresseSuppr'];
            
            $reqVerifGA = "SELECT idEvenement FROM _adresseEvenement WHERE idAdresse = '".$idAdresseSuppr."'";
            $resVerifGA = $this->connexionBdd->requete($reqVerifGA);
            $tabGA = array();
            if(mysql_num_rows($resVerifGA)>0)
            {
                $erreurObject->ajouter("Attention, des groupes d'adresse sont encore liés a l'adresse, veuillez contacter l'administrateur.");
            }
            elseif(mysql_num_rows($resVerifGA)==0)
            {
                // on supprime les historiques de l'adresse,  la verif precedente est suffisante en principe
                $reqSuppr = "DELETE FROM historiqueAdresse WHERE idAdresse = '".$idAdresseSuppr."'";
                $resSuppr = $this->connexionBdd->requete($reqSuppr);
            }
            
            if($erreurObject->getNbErreurs()>0)
            {
                echo $erreurObject->afficher();
            }
        }
    }
    
    public function supprimerRueFromAdminRue($params = array())
    {
        if(isset($this->variablesGet['idRueSuppr']) && $this->variablesGet['idRueSuppr']!='')
        {
            $erreurObject = new objetErreur();
            $idRueSuppr = $this->variablesGet['idRueSuppr'];
            
            $reqVerifAdresse = "SELECT idRue FROM historiqueAdresse WHERE idRue = '".$idRueSuppr."'";
            $resVerifAdresse = $this->connexionBdd->requete($reqVerifAdresse);
            
            if(mysql_num_rows($resVerifAdresse)>0)
            {
                $erreurObject->ajouter("Attention, des adresses sont encore liées a cette rue, veuillez contacter l'administrateur");
            }
            elseif(mysql_num_rows($resVerifAdresse)==0)
            {
                $reqDelete = "DELETE FROM rue WHERE idRue='".$idRueSuppr."'";
                $resDelete = $this->connexionBdd->requete($reqDelete);
                
            }
            
            if($erreurObject->getNbErreurs()>0)
            {
                echo $erreurObject->afficher();
            }
        }
    
    }
    
    // fonction qui permet de savoir s'il y a un parcours actif parmis la liste des parcours
    // si on precise un idParcours en parametre on regarde alors si ce parcours est actif
    public function isParcoursActif($params = array())
    {
        $retour = false;
        
        if(isset($params['idParcours']) && $params['idParcours']!='')
        {
            $req = "SELECT isActif FROM parcoursArt WHERE idParcours = '".$params['idParcours']."'";
            $res = $this->connexionBdd->requete($req);
            if(mysql_num_rows($res)>0)
            {
                $fetch = mysql_fetch_assoc($res);
                if($fetch['isActif']=='1')
                {
                    $retour = true;
                }
            }
        }
        else
        {
            // est ce qu'un parcours est actif
            $req = "SELECT 0 FROM parcoursArt WHERE isActif='1'";
            $res = $this->connexionBdd->requete($req);
            if(mysql_num_rows($res)>0)
            {
                $retour = true;
            }
        }
        
        
        return $retour;
    }
    
    // renvoie la photo courante de l'adresse de l'etape
    public function getPhotoFromEtape($params = array())
    {
        $retour = array('trouve'=>false);
        
        if(isset($params['idEtape']) && $params['idEtape']!='' && $params['idEtape']!='0')
        {
            // recuperation du groupe d'adresse de l'etape
            $req = "SELECT idEvenementGroupeAdresse FROM etapesParcoursArt WHERE idEtape='".$params['idEtape']."'";
            $res = $this->connexionBdd->requete($req);
            $fetch = mysql_fetch_assoc($res);
            
            if(isset($fetch['idEvenementGroupeAdresse']))
            {
                $i = new archiImage();
                
                $idAdresse = $this->getIdAdresseFromIdEvenementGroupeAdresse($fetch['idEvenementGroupeAdresse']);
                $idEvenementGroupeAdresse = $fetch['idEvenementGroupeAdresse'];
                
                $format = 'mini';
                if(isset($params['format']) && $params['format']!='')
                {
                    $format = $params['format'];
                }
                
                $illustration = $this->getUrlImageFromAdresse($idAdresse,$format,array('idEvenementGroupeAdresse'=>$idEvenementGroupeAdresse));
                
                $retour = $illustration;
            }
        }
        return $retour;
    }
    
}

?>
