<?php
/**
 * Classe Config
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
date_default_timezone_set('Europe/Paris');

require_once "includesFramework.php";
require_once "debug.php";

/**
 * Configuration du framework
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
class Config
{
    
     
    protected $bdd_host='localhost';
    protected $bdd_user='archiwiki'; //"root";
    protected $bdd_password='archi-dev';
    protected $bdd_name='archi_v2';
    protected $adminPass;

    static $default_lang = "fr_FR";
    
    // Pour l'identification unique d'un utilisateur dans la session
    public $idSite; 
        
    //Pas besoin de tout ça, on peut le détecter automatiquement
    //protected $cheminPhysique                       = '/home/pia/archiv2/';
    protected $cheminPhysique              = '/var/www/archi-strasbourg/';
    // cette variable sert au remplacement dans les descriptions
    //qui contiennent des urls vers le site du serveur ou est le site    
    protected $nomServeur                        
        = 'www.archi-strasbourg.org'; 
    protected $urlImages                         
        = 'http://www.archi-strasbourg.org/images/';
    protected $urlImagesMini                     
        = 'http://www.archi-strasbourg.org/images/mini/';
    protected $urlImagesMoyen                    
        = 'http://www.archi-strasbourg.org/images/moyen/';
    protected $urlImagesGrand                     
        = 'http://www.archi-strasbourg.org/images/grand/';
    protected $urlImagesOriginaux                
        = 'http://www.archi-strasbourg.org/images/originaux/';

    /*public $cheminPhysiqueImagesMini             
        = '/home/pia/archiv2/images/mini/';
    public $cheminPhysiqueImagesGrand             
        = '/home/pia/archiv2/images/grand/';
    public $cheminPhysiqueImagesMoyen             
        = '/home/pia/archiv2/images/moyen/';
    public $cheminPhysiqueImagesOriginaux         
        = '/home/pia/archiv2/images/originaux/';
    public $cheminPhysiqueImagesUploadMultiple    
        = '/home/pia/archiv2/images/uploadMultiple/';*/
    public $cheminPhysiqueImagesMini             
        = '/home/vhosts/fabien/archi-strasbourg-v2/images/mini/';
    public $cheminPhysiqueImagesGrand             
        = '/home/vhosts/fabien/archi-strasbourg-v2/images/grand/';
    public $cheminPhysiqueImagesMoyen             
        = '/home/vhosts/fabien/archi-strasbourg-v2/images/moyen/';
    public $cheminPhysiqueImagesOriginaux         
        = '/home/vhosts/fabien/archi-strasbourg-v2/images/originaux/';
    public $cheminPhysiqueImagesUploadMultiple    
        = '/home/vhosts/fabien/archi-strasbourg-v2/images/uploadMultiple/';
    /*public $cheminUploadMultipleApplet            
        = 'archiv2/images/uploadMultiple';*/
    public $cheminUploadMultipleApplet      
        = 'archi-strasbourg-v2/images/uploadMultiple';

    public $idTypeEvenementGroupeAdresse           ='11';
    public $idTypeStructureImmeuble                   ='3';

    public $cheminTemplates = 'modules/archi/templates/';

    public $siteMail = 'contact@archi-strasbourg.org';


    


    /*  public $cheminPhysiqueFrameWork             
         = "/home/pia/archiv2/includes/framework/";
     * */
    public $cheminPhysiqueFrameWork
        = "/var/www/archi-strasbourg/includes/framework/";
    public $urlFrameworkFromRoot
        = "http://www.archi-strasbourg.org/includes/framework/";

    protected $variablesGet;
    protected $variablesPost;

    public $date;
    
    public $connexionBdd;
    
    protected $verification;
    private static $_jsHeader = "";
    private static $_jsFooter = "";
    //protected $formulaire;
    
    public $erreurs;
    
    //public $mail;
    
    /**
     * Constructeur de Config
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    function __construct($params=array())
    {
        include __DIR__."/config.php";
        $this->variablesGet        = $_GET;
        $this->variablesPost    = $_POST;

        $this->connexionBdd = new connexionBdd(
            $this->bdd_name, $this->bdd_host,
            $this->bdd_user, $this->bdd_password
        );
        $this->session               = new objetSession();
        $this->date                   = new dateObject();
        //$this->formulaire            = new formGenerator($this->connexionBdd);
        
        //$this->mail                = new mailObject($this->connexionBdd);
        /*if (!in_array('noPEAR',$params)) {
            $config                     = parse_ini_file('BBCodeParser.ini', true);
            $options                    = 
            * &PEAR::getStaticProperty('HTML_BBCodeParser', '_options');
            $options                    = $config['HTML_BBCodeParser'];
            $this->parserBB            = new HTML_BBCodeParser($options);
        }
        */
        $this->erreurs             = new objetErreur();
        
    }
    
    /**
     * Ajoute quelque chose à la variable _jsFooter
     * 
     * @param string $js Chaine à ajouter
     * 
     * @return void
     * */
    public function addToJsFooter($js="")
    {
        self::$_jsFooter.=$js;
    }
    
    /**
     * Ajoute quelque chose à la variable _jsHeader
     * 
     * @param string $js Chaine à ajouter
     * 
     * @return void
     * */
    public function addToJsHeader($js="")
    {
        self::$_jsHeader.=$js;
    }
    
    /**
     * Obtenir la variable _jsFooter
     * 
     * @return string
     * */
    public static function getJsFooter()
    {
        return self::$_jsFooter;
    }
    
    /**
     * Obtenir la variable _jsHeader
     * 
     * @return string
     * */
    public static function getJsHeader()
    {
        return self::$_jsHeader;
    }

    /**
     * Renvoi l'id du typeEvenement = groupe d'adresse
     * afin que celui ci puisse etre modifié automatiquement
     * 
     * @return int
     * */
    function getIdTypeEvenementGroupeAdresse()
    {
        return $this->idTypeEvenementGroupeAdresse;
    }
    
    /**
     * Renvoi l'id du type structure = immeuble,
     * valeur par defaut dans le formulaire d'ajout d'un dossier
     * 
     * @return int
     * */
    function getIdTypeStructureImmeuble()
    {
        return $this->idTypeStructureImmeuble;
    }
    
    /**
     * Renvoit la racine de l'URL
     * 
     * @return string
     * */
    public function getUrlRacine()
    {
        $protocol = empty($_SERVER["HTTPS"])?"http":"https";
        if (isset($_SERVER["SERVER_NAME"])) {
            $dirname=dirname($_SERVER["PHP_SELF"]);
            if (basename($dirname)=="script") {
                $dirname = dirname($dirname);
            }
        } else {
            $dirname="";
        }
        $url =$protocol."://".$this->getNomServeur().
        $dirname;
        if ($dirname!="/") {
            $url.="/";
        } 
        return $url;
    }
    
    /**
     * Permet d'obtenir le nom de domaine
     * 
     * @return string
     * */
    public function getNomServeur()
    {
        if (isset($_SERVER["SERVER_NAME"])) {
            return $_SERVER["SERVER_NAME"];
        } else {
            return "archi-strasbourg.org";
        }
    }
    
    /**
     * Permet d'obtenir le chemin vers le dossier du site sur le serveur
     * 
     * @return string
     * */
    public function getCheminPhysique()
    {
        return dirname(dirname(__DIR__))."/";
    }
    
    /**
     * Permet d'obtenir l'URL du dossier d'une image
     * 
     * @param string $size Taille de l'image (mini, moyen, grand ou originaux)
     * @param string $name Nom de l'image (avec son extension)
     * 
     * @return string
     * */
    public function getUrlImage($size="", $name="")
    {
        if (!empty($size)) {
            $size.="/";
        }
        return $this->getUrlRacine()."images/".$size.$name;
    }
    
    /**
     * Permet d'obtenir le chemin du dossier d'une image
     * 
     * @param string $size Taille de l'image (mini, moyen, grand ou originaux)
     * 
     * @return string
     * */
    public function getCheminPhysiqueImage($size="")
    {
        if (!empty($size)) {
            $size.="/";
        }
        return $this->getCheminPhysique()."images/".$size;
    }
    
    /**
     * Créer une URL
     * 
     * @param string $action    Action à effectuer
     * @param string $affichage Page à afficher
     * @param array  $autre     Paramètres optionnels
     * @param bool   $keep      Keep current query
     * @param bool   $clean     Replace & with &amp;
     * 
     * @return string
     * */
    public function creerUrl(
        $action= null, $affichage = null, $autre = array(), $keep=false, $clean=true
    ) {
        $string = new stringObject();
        $amp=$clean?"&amp;":"&";
        
        if ($keep) {
            $url="?".htmlentities($_SERVER["QUERY_STRING"]).$amp;
            $url_existe = true;
        } else {
            $url = '?';
            $url_existe = false;
        }
        if (!empty($action)) {
            $url .= 'archiAction='.$action;
            $url_existe = true;
        }
        
        if (!empty($affichage)) {
            if ($url_existe == true) {
                $url .= $amp;
            }
            
            $url .= 'archiAffichage='.$affichage;
            $url_existe = true;
        }
        
        if (is_array($autre) && count($autre)>0) {
            $i = 0;
            foreach ($autre AS $nom => $val) {
                if (is_array($val)) {
                    foreach ($val AS $case) {
                        if ($url_existe == true) {
                            $url .= $amp;
                        }
                        $url .= $nom.'%5B%5D='.urlencode($case);
                    }
                } else {
                    if ($url_existe == true || $i>0) {
                        $url .= $amp;
                    }
                    $url .= $nom.'='.urlencode($val);
                }
                $i++;
            }
            if ($url_existe == false) {
                $url = '?'.pia_substr($url, 1);
            }
        }


        if (isset($affichage)
            && $affichage=='afficheAccueil'
            && isset($autre['archiNomVilleGeneral'])
            && $autre['archiNomVilleGeneral']!=''
        ) {
            $url = $autre['archiNomVilleGeneral']."/";
        }



        /* Si l'url est un appel simple a l'affichage d'une adresse,
         * comme sur la page d'accueil par exemple, on rewrite
         * */
        if (isset($affichage)
            && $affichage == 'adresseDetail'
            && isset($autre['archiIdAdresse'])
            && count($autre)==1
        ) {
            // rewriting
            $adresse = new archiAdresse();
            
            $fetchAdresse = $adresse
                ->getArrayAdresseFromIdAdresse($autre['archiIdAdresse']);
            $intitule = $adresse->getIntituleAdresse($fetchAdresse);
            $intitule = $string->convertStringToUrlRewrite($intitule);
            $url = 'adresse-'.$intitule."-".$autre['archiIdAdresse'].".html";
        }



        if (isset($affichage)
            && $affichage == 'detailProfilPublique'
            && isset($autre['archiIdUtilisateur'])
            && count($autre)==1
        ) {
            $url = 'profil-'.$autre['archiIdUtilisateur'].'.html';
        }
        
        if (isset($affichage)
            && $affichage== 'detailProfilPublique'
            && isset($autre['archiIdUtilisateur'])
            && isset($autre['archiIdEvenementGroupeAdresseOrigine'])
            && count($autre)==2
        ) {
            $url = 'profil-'.$autre['archiIdUtilisateur'].'-'.
            $autre['archiIdEvenementGroupeAdresseOrigine'].'.html';
        }





        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='adresseDetail'
            && isset($autre['archiIdAdresse'])
            && count($autre)>2
        ) {
            // rewriting
            $adresse = new archiAdresse();
            
            $fetchAdresse = $adresse->getArrayAdresseFromIdAdresse(
                $autre['archiIdAdresse']
            );
            $intitule = $adresse->getIntituleAdresse($fetchAdresse);
            $intitule = $string->convertStringToUrlRewrite($intitule);
            $url = 'adresse-'.$intitule."-".$autre['archiIdAdresse'].".html?check=1";
            $urlComplement="";
            foreach ($autre as $intitule => $valeur) {
                if ($intitule!='archiAffichage' || $intitule!='archiIdAdresse') {
                    $urlComplement.=$amp.$intitule."=".$valeur;
                }
            }
            
            $url.=$urlComplement;
        }    

        if (isset($affichage)
            && $affichage=='evenementListe'
            && isset($autre['selection'])
            && $autre['selection']=='personne'
            && isset($autre['id'])
        ) {
            $personne = new archiPersonne();
            $nomPrenom=$personne->getPersonneLibelle($autre['id']);
            $url = "personnalite-".$string->convertStringToUrlRewrite($nomPrenom).
            "-".$autre['id'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='adresseListe'
            && isset($autre['recherche_rue'])
            && $autre['recherche_rue']!=''
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['recherche_rue'], 'idRue'
            );
            $url = "rue-".$string->convertStringToUrlRewrite(trim($intituleRue)).
            "-".$autre['recherche_rue'].".html";
        }
    
        if (isset($affichage)
            && $affichage=='listeDossiers'
            && isset($autre['archiIdQuartier'])
            && $autre['archiIdQuartier']!=''
            && isset($autre['modeAffichageListe'])
            && $autre['modeAffichageListe']=='parRuesDeQuartier'
            && isset($autre['archiPageRuesQuartier'])
            && $autre['archiPageRuesQuartier']!=''
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['archiIdQuartier'], 'idQuartier'
            );
            $url = "quartier-".
            $string->convertStringToUrlRewrite(trim($intituleRue)).
            "-".$autre['archiIdQuartier']."-page".
            $autre['archiPageRuesQuartier'].".html";
        }




        if (isset($affichage)
            && $affichage=='adresseListe'
            && isset($autre['recherche_quartier'])
            && $autre['recherche_quartier']!=''
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['recherche_quartier'], 'idQuartier'
            );
            $url = "quartier-".
            $string->convertStringToUrlRewrite(trim($intituleRue)).
            "-".$autre['recherche_quartier'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='adresseListe'
            && isset($autre['recherche_ville'])
            && $autre['recherche_ville']!=''
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['recherche_ville'], 'idVille'
            );
            $url = "ville-".$string->convertStringToUrlRewrite(trim($intituleRue)).
            "-".$autre['recherche_ville'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='listeAdressesFromRue'
            && isset($autre['recherche_rue'])
            && $autre['recherche_rue']!=''
            && isset($autre['noAdresseSansNumero'])
            && $autre['noAdresseSansNumero']==1
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['recherche_rue'], 'idRue'
            );
            $url = "rue-adresses-".
            $string->convertStringToUrlRewrite(trim($intituleRue)).
            "-".$autre['recherche_rue'].".html";
        }

        if (isset($affichage) && $affichage=='listeAdressesFromRue'
            && isset($autre['recherche_rue']) && $autre['recherche_rue']!=''
            && !isset($autre['noAdresseSansNumero'])
        ) {
            $adresse = new archiAdresse();
            $intituleRue = $adresse->getIntituleAdresseFrom(
                $autre['recherche_rue'], 'idRue'
            );
            $url = "rue-".$string
                ->convertStringToUrlRewrite(trim($intituleRue)).
                "-".$autre['recherche_rue'].".html";
        }

        if (isset($affichage)
            && $affichage=='statistiquesAccueil'
            && count($autre)==0
        ) {
            $url = "statistiques-adresses-photos-architectes-strasbourg.html";
        }




        // *************************************************************
        /* Ceci ne sert qu'au copier coller de lien,
         * vu que l'information est de toute facon passée en session
         * */
        if (isset($affichage)
            && $affichage=='listeDossiers'
            && isset($autre['archiIdVilleGeneral'])
            && !isset($autre['modeAffichageListe'])
            && !isset($autre['archiPageCouranteVille'])
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse->getInfosVille(
                $autre['archiIdVilleGeneral'],
                array('fieldList'=>'v.nom as nomVille')
            );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-".$nomVilleGeneral
            ."-".$autre['archiIdVilleGeneral'].".html";
        }
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && !isset($autre['modeAffichageListe'])
            && isset($autre['archiIdVilleGeneral'])
            && !isset($autre['lettreCourante'])
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse->getInfosVille(
                $autre['archiIdVilleGeneral'],
                array('fieldList'=>'v.nom as nomVille')
            );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-".
            $nomVilleGeneral."-".$autre['archiIdVilleGeneral']."-page".
            $autre['archiPageCouranteVille'].".html";
        }
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && isset($autre['modeAffichageListe'])
            && isset($autre['archiIdVilleGeneral'])
            && !isset($autre['lettreCourante'])
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse->getInfosVille(
                $autre['archiIdVilleGeneral'],
                array('fieldList'=>'v.nom as nomVille')
            );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-".
            $nomVilleGeneral."-".$autre['archiIdVilleGeneral']."-page".
            $autre['archiPageCouranteVille']."-".
            $autre['modeAffichageListe'].".html";
        }
        
        // modif lettre courante
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && isset($autre['modeAffichageListe'])
            && isset($autre['archiIdVilleGeneral'])
            && isset($autre['lettreCourante'])
            && $autre['lettreCourante']!=''
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse
                ->getInfosVille(
                    $autre['archiIdVilleGeneral'],
                    array('fieldList'=>'v.nom as nomVille')
                );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-".
            $nomVilleGeneral."-".$autre['archiIdVilleGeneral']."-page".
            $autre['archiPageCouranteVille']."-".
            $autre['modeAffichageListe']."-lettre".
            $autre['lettreCourante'].".html";
            
            
        }
        
        
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && !isset($autre['archiPageCouranteVille'])
            && isset($autre['modeAffichageListe'])
            && $autre['modeAffichageListe']!=''
            && isset($autre['archiIdVilleGeneral'])
            && isset($autre['lettreCourante'])
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse
                ->getInfosVille(
                    $autre['archiIdVilleGeneral'],
                    array('fieldList'=>'v.nom as nomVille')
                );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-ville-".
            $nomVilleGeneral."-".$autre['archiIdVilleGeneral']."-".
            $autre['modeAffichageListe']."-lettre".
            $autre['lettreCourante'].".html";
        }
        
        
        
        
        if (isset($affichage)
            && $affichage=='listeDossiers'
            && !isset($autre['archiPageCouranteVille'])
            && isset($autre['modeAffichageListe'])
            && $autre['modeAffichageListe']!=''
            && isset($autre['archiIdVilleGeneral'])
            && !isset($autre['lettreCourante'])
        ) {
            $adresse = new archiAdresse();
            $stringObj = new stringObject();
            $fetchInfosVille = $adresse
                ->getInfosVille(
                    $autre['archiIdVilleGeneral'],
                    array('fieldList'=>'v.nom as nomVille')
                );
        
            $nomVilleGeneral = $stringObj
                ->convertStringToUrlRewrite($fetchInfosVille['nomVille']);
        
            $url = "dossiers-rues-quartiers-adresses-photos-ville-".
            $nomVilleGeneral."-".$autre['archiIdVilleGeneral']."-".
            $autre['modeAffichageListe'].".html";
        }
        

        
        
        // ************************************************************
        

        
        if (count($autre)==2
            && isset($autre['lettreCourante'])
            && $autre['lettreCourante']!=''
            && isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg-lettre".
            $autre['lettreCourante'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='listeDossiers'
            && count($autre)==0
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg.html";
        }
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && !isset($autre['modeAffichageListe'])
            && !isset($autre['archiIdVilleGeneral'])
            && isset($autre['lettreCourante'])
            && $autre['lettreCourante']!=''
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg-page".
            $autre['archiPageCouranteVille']."-lettre".
            $autre['lettreCourante'].".html";
        }



        
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && !isset($autre['modeAffichageListe'])
            && !isset($autre['archiIdVilleGeneral'])
            && !isset($autre['lettreCourante'])
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg-page".
            $autre['archiPageCouranteVille'].".html";
        }
        
        

        
        
        
        
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='listeDossiers'
            && isset($autre['archiPageCouranteVille'])
            && $autre['archiPageCouranteVille']!=''
            && isset($autre['modeAffichageListe'])
            && !isset($autre['archiIdVilleGeneral'])
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg-page".
            $autre['archiPageCouranteVille']."-".
            $autre['modeAffichageListe'].".html";
        }
        
        
        
        if (isset($affichage)
            && $affichage=='listeDossiers'
            && !isset($autre['archiPageCouranteVille'])
            && isset($autre['modeAffichageListe'])
            && $autre['modeAffichageListe']!=''
            && !isset($autre['archiIdVilleGeneral'])
            && !isset($autre['archiPageRuesQuartier'])
            && !isset($autre['lettreCourante'])
        ) {
            $url = "dossiers-rues-quartiers-adresses-photos-strasbourg-ville-".
            $autre['modeAffichageListe'].".html";
        }    
        
        
        if (isset($affichage)
            && $affichage=='toutesLesDemolitions'
            && count($autre)==0
        ) {
            $url = "demolitions-toutes-adresses-strasbourg-archi.html";
        }
        
        if (isset($affichage)
            && $affichage=='toutesLesDemolitions'
            && isset($autre['archiIdVilleGeneral'])
            && $autre['archiIdVilleGeneral']!=''
            && isset($autre['archiIdPaysGeneral'])
            && $autre['archiIdPaysGeneral']!=''
        ) {
            $url = "demolitions-toutes-adresses-strasbourg-archi-".
            $autre['archiIdVilleGeneral']."-".
            $autre['archiIdPaysGeneral'].".html";
        }
        
        
        if (isset($affichage)
            && $affichage=='tousLesTravaux'
            && isset($autre['archiIdVilleGeneral'])
            && $autre['archiIdVilleGeneral']!=''
            && isset($autre['archiIdPaysGeneral'])
            && $autre['archiIdPaysGeneral']!=''
        ) {
            $url = "travaux-tous-adresses-strasbourg-archi-".
            $autre['archiIdVilleGeneral']."-".
            $autre['archiIdPaysGeneral'].".html";
        }
        
        
        if (isset($affichage)
            && $affichage=='tousLesTravaux'
            && count($autre)==0
        ) {
            $url = "travaux-tous-adresses-strasbourg-archi.html";
        }
        

        if (isset($affichage)
            && $affichage=='tousLesEvenementsCulturels'
            && isset($autre['archiIdVilleGeneral'])
            && $autre['archiIdVilleGeneral']!=''
            && isset($autre['archiIdPaysGeneral'])
            && $autre['archiIdPaysGeneral']!=''
        ) {
            $url = "culture-evenements-culturels-adresses-strasbourg-archi-".
            $autre['archiIdVilleGeneral']."-".
            $autre['archiIdPaysGeneral'].".html";
        }

        if (isset($affichage)
            && $affichage=='tousLesEvenementsCulturels'
            && count($autre)==0
        ) {
            $url = "culture-evenements-culturels-adresses-strasbourg-archi.html";
        }
        
        
        
        if (isset($affichage)
            && $affichage=='recherche'
            && isset($autre['archiIdVilleGeneral'])
            && $autre['archiIdVilleGeneral']!=''
            && isset($autre['archiIdPaysGeneral'])
            && $autre['archiIdPaysGeneral']!=''
            && isset($autre['motcle'])
            && $autre['motcle']==''
            && isset($autre['submit'])
            && $autre['submit']=='Rechercher'
        ) {
            $url = "adresses-nouvelles-toutes-rues-villes-quartiers".
            "-strasbourg-archi-".
            $autre['archiIdVilleGeneral']."-".
            $autre['archiIdPaysGeneral'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='recherche'
            && !isset($autre['archiIdVilleGeneral'])
            && !isset($autre['archiIdPaysGeneral'])
            && isset($autre['motcle'])
            && $autre['motcle']==''
            && isset($autre['submit'])
            && $autre['submit']=='Rechercher'
        ) {
            $url = "adresses-nouvelles-toutes-rues-villes-quartiers".
            "-strasbourg-archi.html";
        }
        
        if (isset($affichage)
            && $affichage=='tousLesArchitectesClasses'
            && count($autre)==0
        ) {
            $url = "architectes-strasbourg-photos-classes.html";
        }
        
        if (isset($affichage)
            && $affichage=='toutesLesRuesCompletesClassees'
            && count($autre)==0
        ) {
            $url = "rues-strasbourg-photos-classees.html";
        }

        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='toutesLesRuesCompletesClassees'
            && isset($autre['archiPageCouranteRue'])
        ) {
            $url="rues-strasbourg-photos-classees-".
            $autre['archiPageCouranteRue'].".html";
        }
        
        if (isset($autre['archiAffichage'])
            && $autre['archiAffichage']=='tousLesArchitectesClasses'
            && isset($autre['archiPageCouranteArchitectes'])
        ) {
            $url="architectes-strasbourg-photos-classes-".
            $autre['archiPageCouranteArchitectes'].".html";
        }
        


        if (isset($affichage)
            && $affichage=='imageDetail'
            && isset($autre['archiIdImage'])
            && isset($autre['archiRetourAffichage'])
            && isset($autre['archiRetourIdName'])
            && isset($autre['archiRetourIdValue'])
            && !isset($autre['archiSelectionZone'])
            && !isset($action)
        ) {
            $url="photo-detail-strasbourg-".$autre['archiIdImage']."-".
            $autre['archiRetourAffichage']."-".$autre['archiRetourIdName'].
            "-".$autre['archiRetourIdValue'].".html";
        }
        
        
        if (isset($affichage)
            && $affichage=='imageDetail'
            && isset($autre['archiIdImage'])
            && isset($autre['archiRetourAffichage'])
            && isset($autre['archiRetourIdName'])
            && isset($autre['archiRetourIdValue'])
            && isset($autre['formatPhoto'])
            && !isset($autre['archiSelectionZone'])
            && !isset($action)
        ) {
            $url="photo-detail-strasbourg-".$autre['archiIdImage']."-".
            $autre['archiRetourAffichage']."-".$autre['archiRetourIdName'].
            "-".$autre['archiRetourIdValue']."-".
            $autre['formatPhoto'].".html";
        }    

        if (isset($affichage)
            && $affichage=='imageDetail'
            && isset($autre['archiIdImage'])
            && isset($autre['archiRetourAffichage'])
            && isset($autre['archiRetourIdName'])
            && isset($autre['archiIdAdresse'])
        ) {
            $url="photo-detail-strasbourg-".$autre['archiIdImage']."-".
            $autre['archiRetourAffichage']."-".$autre['archiRetourIdName'].
            "-".$autre['archiRetourIdValue']."-adresse".
            $autre['archiIdAdresse'].".html";
        }


        if (isset($affichage)
            && $affichage=='imageDetail'
            && isset($autre['archiIdImage'])
            && isset($autre['archiRetourAffichage'])
            && isset($autre['archiRetourIdName'])
            && isset($autre['archiIdAdresse'])
        ) {
        
            $libelleAdresse = "";
            if ($autre['archiIdAdresse']!='') {
                $adresse = new archiAdresse();
                $fetchAdresse = $adresse
                    ->getArrayAdresseFromIdAdresse($autre['archiIdAdresse']);
                $libelleAdresse = $adresse
                    ->getIntituleAdresse($fetchAdresse);
                $libelleAdresse = $string
                    ->convertStringToUrlRewrite($libelleAdresse);
                
            }
            
            if ($libelleAdresse != '') {
                $url="photo-detail-".$libelleAdresse."-".
                $autre['archiIdImage']."-".$autre['archiRetourAffichage'].
                "-".$autre['archiRetourIdName']."-".$autre['archiRetourIdValue'].
                "-adresse".$autre['archiIdAdresse'].".html";
            } else {
                $url="photo-detail-strasbourg-".$autre['archiIdImage'].
                "-".$autre['archiRetourAffichage']."-".$autre['archiRetourIdName'].
                "-".$autre['archiRetourIdValue']."-adresse".
                $autre['archiIdAdresse'].".html";
            }
        }


        
        if (isset($affichage)
            && $affichage=='tousLesCommentaires'
            && count($autre)==0
        ) {
            $url = "commentaires-archi-strasbourg.html";
        }
        
        if (isset($affichage)
            && $affichage=='tousLesCommentaires'
            && isset($autre['pageCourante'])
        ) {
            $url = "commentaires-archi-strasbourg-".$autre['pageCourante'].".html";
        }
        
        if (isset($affichage)
            && $affichage=='publiciteArticlesPresse'
            && count($autre)==0
        ) {
            $url = "archi-strasbourg-media-presse-publicite.html";
        }
        
        if (isset($affichage) 
            && $affichage=="afficheAccueil"
            && isset($autre['archiIdVilleGeneral'])
            && isset($autre['archiIdPaysGeneral'])
        ) {
            $adresse = new archiAdresse();
            $infosVille = $adresse->getInfosVille(
                $autre['archiIdVilleGeneral'],
                array("fieldList"=>"v.nom as nomVille")
            );
            
            $url = "accueil-ville-photos-immeubles-".$infosVille['nomVille'].
            "-".$autre['archiIdVilleGeneral']."-".
            $autre['archiIdPaysGeneral'].".html";
        }


        if (isset($affichage)
            && $affichage=="afficheSondageGrand"
            && count($autre)==0
        ) {
            $url = "sondage-financement-archi-strasbourg.html";
        }

        if (isset($affichage) 
            && $affichage=="afficheSondageResultatGrand" 
            && count($autre)==0
        ) {
            $url = "sondage-financement-archi-strasbourg-statistiques.html";
        }

        if (isset($affichage) 
            && $affichage=="afficherActualite"
            && isset($autre['archiIdActualite'])
        ) {
            $url = "actualites-archi-strasbourg-".$autre['archiIdActualite'].".html";
        }
        
        if (isset($affichage) 
            && $affichage=="toutesLesActualites" 
            && count($autre)==0
        ) {
            $url = "actualites-archi-strasbourg-liste.html";
        }
        
        if (isset($affichage) && $affichage=="toutesLesVues" && count($autre)==0) {
            $url = "vues-photos-archi-strasbourg.html";
        }

        if (isset($affichage) 
            && $affichage=="adresseListe" 
            && isset($autre['recherche_sousQuartier'])
            && $autre['recherche_sousQuartier']!=''
        ) {
            $adresse = new archiAdresse();
            $reqSousQuartier = "SELECT idSousQuartier, nom as nomSousQuartier ".
            "FROM sousQuartier WHERE idSousQuartier='".
            $autre['recherche_sousQuartier']."'";
            $resSousQuartier = $this
                ->connexionBdd->requete($reqSousQuartier);
            $fetchSousQuartier = mysql_fetch_assoc($resSousQuartier);
            if ($fetchSousQuartier['nomSousQuartier']!=''
                && $fetchSousQuartier['nomSousQuartier']!='autre'
            ) {
                $url = "sous-quartier-".$string->convertStringToUrlRewrite(
                    trim($fetchSousQuartier['nomSousQuartier'])
                )."-".$autre['recherche_sousQuartier'].".html";
            }
        }


        
        return $this->getUrlRacine().$url;
    }    
    /*public function BBversHTML ($string = '')
    {
        $this->parserBB->setText($string);
        $this->parserBB->parse();
        return $this->parserBB->getParsed();
    }
    */
    /* sert a afficher les descriptions sur les listes pour 
     * eviter d'avoir des balises bb coupées en deux
     * */
    /*public function sansBalises ($string = '')
    {
        return preg_replace('#\[(.*)\](.*)\[\/(.*)\]#', '$2', $string);
    }
    */
    
    /**
     * Obtenir le format d'image mini
     * 
     * @return int
     * */
    public function getFormatImageMini()
    {
        return 80;
    }
    
    /**
     * Obtenir le format d'image moyen
     * 
     * @return int
     * */
    public function getFormatImageMoyen()
    {
        return 200;
    }
    
    /**
     * Obtenir le format d'image grand
     * 
     * @return int
     * */
    public function getFormatImageGrand()
    {
        return 500;
    }
    
    /**
     * Obtenir le message de désabonnement à l'alerte mail
     * 
     * @return string
     * */
    public function getMessageDesabonnerAlerteMail()
    {
        return "<br><small>Pour ne plus recevoir les alertes mail, ".
        "il vous suffit de vous connecter à <a href='".$this->creerUrl('', 'afficheAccueil&modeAffichage=profil')."'>votre profil archi-strasbourg.org".
        "</a>.</small>";
    }    

    /**
     * Fonction permettant d'afficher la pagination
     * en fonction de la requete courante et de la page courante
     * 
     * @param array $parametres Paramètres
     * 
     * @return array
     * */
    public function pagination($parametres=array())
    {
        $html = '';
        $t=new Template($this->getCheminPhysique().$this->cheminTemplates);
        $t->set_filenames((array('pagination'=>'pagination.tpl')));
        
        $nbEnregistrementsTotaux = 0;
        if (isset($parametres['nbEnregistrementsTotaux'])) {
            $nbEnregistrementsTotaux = $parametres['nbEnregistrementsTotaux'];
        }
        
        $typeLiens="default";
        if (isset($parametres['typeLiens'])) {
            $typeLiens = $parametres['typeLiens'];
        }
        
        $nomParamPageCourante = '';
        if (isset($parametres['nomParamPageCourante'])) {
            $nomParamPageCourante = $parametres['nomParamPageCourante'];
        }
        
        $pageCourante=1;
        if (isset($this->variablesGet[$nomParamPageCourante]) 
            && $this->variablesGet[$nomParamPageCourante]!=''
        ) {
            $pageCourante = $this->variablesGet[$nomParamPageCourante];
        } elseif (isset($this->variablesPost[$nomParamPageCourante])
            && $this->variablesPost[$nomParamPageCourante]!=''
        ) {
            $pageCourante = $this->variablesPost[$nomParamPageCourante];
        }
    
        $nbEnregistrementsParPage = 0;
        if (isset($parametres['nbEnregistrementsParPage'])) {
            $nbEnregistrementsParPage = $parametres['nbEnregistrementsParPage'];
            
            $premierePage =1;
            // calcul du nombre de pages 
            if (($nbEnregistrementsTotaux/$nbEnregistrementsParPage)>1) {
                if ($nbEnregistrementsTotaux%$nbEnregistrementsParPage==0) {
                    $nbPages = $nbEnregistrementsTotaux/$nbEnregistrementsParPage;
                } else {
                    $nbPages = intval(
                        $nbEnregistrementsTotaux/$nbEnregistrementsParPage
                    )+1;
                }
            } else {
                // il n'y a qu'une page
                $nbPages = 1;
            }
            
            $nbPagesAffichees=$nbPages;
            if ($nbPages > 20) {
                if ($pageCourante > 10) {
                    $premierePage = $pageCourante-5;
                    $t->assign_vars(array('pointillesPrecedents'=>'...'));
                }
                if ($pageCourante < $nbPages-10) {
                    $nbPagesAffichees = $pageCourante+5;
                    $t->assign_vars(array('pointillesSuivants'=>'...'));
                }
            }

            for ($i=$premierePage ; $i<=$nbPagesAffichees ; $i++) {
                $t->assign_block_vars('pages', array('numero'=>$i));
                if ($i!=$pageCourante) {
                    switch($typeLiens) {
                    case "formulaire":
                        /* Dans ce cas, on valide le formulaire dont l'ID
                         * est passé en parametres et on passe l'id
                         * dans un champs a cet effet
                         * */
                         /*$this->creerUrl('','', array_merge($this->variablesGet,
                          * array('archiPageSource'=>$i))),
                          * */
                        $t->assign_block_vars(
                            'pages.isNotPageCourante', array(
                                'url'=>"#",
                                'onClick'=>"document.getElementById('".
                                $parametres['champPageCourante'].
                                "').value='".
                                $i."';document.getElementById('".
                                $parametres['nomChampActionFormulaireOnSubmit'].
                                "').value='".
                                $parametres['nomActionFormulaireOnSubmit'].
                                "';document.getElementById('".
                                $parametres['idFormulaire'].
                                "').submit();"
                            )
                        );
                        $t->assign_vars(
                            array(
                                'urlPremier'=>'#',
                                'onClickPremier'=>"document.getElementById('".
                                $parametres['champPageCourante'].
                                "').value='1';document.getElementById('".
                                $parametres['nomChampActionFormulaireOnSubmit'].
                                "').value='".
                                $parametres['nomActionFormulaireOnSubmit'].
                                "';document.getElementById('".
                                $parametres['idFormulaire'].
                                "').submit();",
                                'urlDernier'=>'#',
                                'onClickDernier'=>"document.getElementById('".
                                $parametres['champPageCourante'].
                                "').value='".
                                $nbPages.
                                "';document.getElementById('".
                                $parametres['nomChampActionFormulaireOnSubmit'].
                                "').value='".
                                $parametres['nomActionFormulaireOnSubmit'].
                                "';document.getElementById('".
                                $parametres['idFormulaire'].
                                "').submit();",
                            )
                        );
                        
                        if ($pageCourante>1) {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>'#',
                                    'onClickPrecedent'=>"document.getElementById('".
                                    $parametres['champPageCourante'].
                                    "').value='".
                                    ($pageCourante-1).
                                    "';document.getElementById('".
                                    $parametres['nomChampActionFormulaireOnSubmit'].
                                    "').value='".
                                    $parametres['nomActionFormulaireOnSubmit'].
                                    "';document.getElementById('".
                                    $parametres['idFormulaire'].
                                    "').submit();"
                                )
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>'#',
                                    'onClickPrecedent'=>''
                                )
                            );
                        }
                        
                        
                        if ($pageCourante<$nbPagesAffichees) {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>'#',
                                    'onClickSuivant'=>"document.getElementById('".
                                    $parametres['champPageCourante']."').value='".
                                    ($pageCourante+1)."';document.getElementById('".
                                    $parametres['nomChampActionFormulaireOnSubmit'].
                                    "').value='".
                                    $parametres['nomActionFormulaireOnSubmit'].
                                    "';document.getElementById('".
                                    $parametres['idFormulaire'].
                                    "').submit();"
                                )
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>'#',
                                    'onClickSuivant'=>''
                                )
                            );
                        }
                        break;        
                    
                    
                    case "noformulaire":
                        /* on ne valide pas de formulaire,
                         * on fabrique simplement l'url
                         * */
                        $t->assign_block_vars(
                            'pages.isNotPageCourante',
                            array(
                                'url'=>$this->creerUrl(
                                    '', '',
                                    array_merge(
                                        $this->variablesGet,
                                        array($nomParamPageCourante=>$i)
                                    )
                                ),
                                'onClick'=>""                            
                            )
                        );
                        
                        
                        
                        $t->assign_vars(
                            array(
                                'urlPremier'=>$this->creerUrl(
                                    '', '',
                                    array_merge(
                                        $this->variablesGet,
                                        array($nomParamPageCourante=>'1')
                                    )
                                ),
                                'onClickPremier'=>'',
                                'urlDernier'=>$this->creerUrl(
                                    '', '',
                                    array_merge(
                                        $this->variablesGet,
                                        array($nomParamPageCourante=>$nbPages)
                                    )
                                ),
                                'onClickDernier'=>"",
                            )
                        );
                        
                        if ($pageCourante>1) {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>$this->creerUrl(
                                        '', '',
                                        array_merge(
                                            $this->variablesGet,
                                            array($nomParamPageCourante=>
                                            ($pageCourante-1))
                                        )
                                    ),
                                    'onClickPrecedent'=>""
                                )
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>'#',
                                    'onClickPrecedent'=>''
                                )
                            );
                        }
                        
                        
                        if ($pageCourante<$nbPagesAffichees) {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>$this->creerUrl(
                                        '', '', array_merge(
                                            $this->variablesGet,
                                            array(
                                                $nomParamPageCourante=>
                                                ($pageCourante+1)
                                            )
                                        )
                                    ),
                                    'onClickSuivant'=>""
                                )
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>'#',
                                    'onClickSuivant'=>''
                                )
                            );
                        }
                        break;
                    default:
                        /* Dans ce cas le click sur un numero de page
                         * renvoi simplement 
                         * vers la meme page avec le numero de page
                         * clicqué en parametres
                         * */
                        $t->assign_block_vars(
                            'pages.isNotPageCourante',
                            array(
                            'url'=>$this->creerUrl(
                                '', '', array_merge(
                                    $this->variablesGet, array('archiPageSource'=>$i)
                                )
                            ),
                            'onClick'=>''
                            )
                        );
                        
                        
                        $t->assign_vars(
                            array(
                                'urlPremier'=>$this->creerUrl(
                                    '', '', array_merge(
                                        $this->variablesGet,
                                        array('archiPageSource'=>'1')
                                    )
                                ),
                                'onClickPremier'=>'',
                                'urlDernier'=>$this->creerUrl(
                                    '', '',
                                    array_merge(
                                        $this->variablesGet,
                                        array('archiPageSource'=>$nbPages)
                                    )
                                ),
                                'onClickDernier'=>"",
                            )
                        );
                        
                        if ($pageCourante>1) {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>$this->creerUrl(
                                        '', '', array_merge(
                                            $this->variablesGet,
                                            array(
                                                'archiPageSource'=>($pageCourante-1)
                                            )
                                        )
                                    ),
                                    'onClickPrecedent'=>"")
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlPrecedent'=>'#',
                                    'onClickPrecedent'=>''
                                )
                            );
                        }
                        
                        
                        if ($pageCourante<$nbPagesAffichees) {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>$this->creerUrl(
                                        '', '', array_merge(
                                            $this->variablesGet,
                                            array(
                                                'archiPageSource'=>($pageCourante+1)
                                            )
                                        )
                                    ),
                                    'onClickSuivant'=>""
                                )
                            );
                        } else {
                            $t->assign_vars(
                                array(
                                    'urlSuivant'=>'#',
                                    'onClickSuivant'=>''
                                )
                            );
                        }
                        break;
                    }
                } else {
                    $t->assign_block_vars('pages.isPageCourante', array());
                }
            }
        } else {
            echo "Erreur : le nombre d'enregistrements par page est de 0.";
        }
        
        
        $limitSqlDebut = $nbEnregistrementsParPage * ($pageCourante-1);
        
        ob_start();
        $t->pparse('pagination');
        $html=ob_get_contents();
        ob_end_clean();
        
        return array('html'=>$html,'limitSqlDebut'=>$limitSqlDebut);
    }    
    
    /**
     * Affiche le calendrier
     * 
     * @return string
     * */
    public function getPopupCalendrier()
    {
        $html = '';
        $t=new Template('modules/archi/templates/');
        $t->set_filenames((array('popupDate'=>'popupChoixDate.tpl')));
        
        $t->assign_vars(
            array('iframeSrc'=>$this->creerUrl(
                '', 'afficheCalendrier', array('noHeaderNoFooter'=>'1')
            ))
        );
                
        ob_start();
        $t->pparse('popupDate');
        $html=ob_get_contents();
        ob_end_clean();
        
        return $html;
    }

    /**
     * Affiche le popup d'attente
     * 
     * @return string
     * */
    public function getPopupAttente()
    {
        $divAttente="";
        $divAttente.="<div id='popupAttente' style='width:400px;height:150px;".
        "top:200px;left:180px;background-color:white;position:absolute;".
        "display:none;text-align:center;'>";
        $divAttente.="<iframe style='width:400px;border:0px;' src='".
        $this->creerUrl('', 'affichePopupAttente', array('noHeaderNoFooter'=>1)).
        "'></iframe>";
        $divAttente.="</div>";
        
        
        return $divAttente;
    }

    /**
     * Cette fonction recense les messages d'aide par page
     * 
     * @param string $modeAffichage Le module à afficher
     * 
     * @return array
     * 
     * */
    public function getHelpMessages($modeAffichage)
    {
        $help=array();
        
        
        switch($modeAffichage) {
        case 'helpEvenement':
            $help['msgButtonAddAdresse'] = _(
                "Ajouter une adresse"
            );
            $help['msgButtonDeleteAdresse'] = _(
                "Retirer la dernière adresse"
            );
            $help['msgVille'] = _(
                "Choisissez la ville"
            );
            $help['msgQuartier'] = _(
                "Choisissez un quartier appartenant à la ville selectionnée"
            );
            $help['msgSousQuartier'] = _(
                "Vous pouvez affiner en choisissant un sous quartier"
            );
            $help['msgRue'] = _(
                "Choisissez la rue et le numéro de l’adresse ".
                "dont vous voulez parler"
            );
            $help['msgLibelle'] = _(
                "Vous pouvez préciser un titre pour votre article"
            );
            $help['msgGras'] = _(
                "Selectionnez une partie de votre texte et cliquez ".
                "sur ce bouton pour mettre le texte en gras"
            );
            $help['msgItalic'] = _(
                "Selectionnez une partie de votre texte et cliquez ".
                "sur ce bouton pour mettre le texte en italique"
            );
            $help['msgUnderline'] = _(
                "Selectionnez une partie de votre texte et cliquez ".
                "sur ce bouton pour souligner le texte selectionné"
            );
            $help['msgQuotes'] = _(
                "Sélectionnez une partie de texte à mettre entre cotes ".
                "et cliquez sur ce bouton"
            );
            $help['msgCode'] = _(
                "Vous pouvez utiliser ce bouton pour insérer du code"
            );
            $help['msgUrl'] = _(
                "Insérer une adresse WEB"
            );
            $help['msgIFrame'] = _(
                "Insérer une adresse WEB (exemple : vidéo)"
            );
            $help['msgDescription'] = _(
                "Texte de votre article"
            );
            $help['msgDateDebut'] = _(
                "Datez votre article : vous pouvez préciser ".
                "une date de plusieurs manières.".PHP_EOL."Exemple : ".
                "Une année 1900".PHP_EOL."Un mois et une année 03/2008".
                PHP_EOL."Avec le jour 02/08/2008"
            );
            $help['msgDateFin'] = _(
                "Datez votre article : vous pouvez préciser ".
                "une date de plusieurs manières".
                PHP_EOL."Exemple : ".PHP_EOL."Une année 1900".PHP_EOL.
                "Un mois et une année 03/2008".PHP_EOL."Avec le jour 02/08/2008"
            );
            $help['msgSource'] = _(
                "Vous pouvez préciser une source à partir de laquelle ".
                "vous vous êtes inspiré pour écrire votre article"
            );
            $help['msgStructure'] = _(
                "Précisez le type de structure dont vous parlez"
            );
            $help['msgTypeEvenement'] = _(
                "Précisez quel est le type de l’évènement lié à votre article"
            );
            $help['msgISMH'] = _(
                "Le bâtiment est-il inscrit à l’Inventaire ".
                "Supplémentaire des Monuments Historiques"
            );
            $help['msgMH'] = _(
                "Le bâtiment est-il inscrit au Monuments Historiques"
            );
            $help['msgNbEtages'] = _(
                "Nombre d’étages du bâtiment"
            );
            $help['msgCourantArchitectural'] = _(
                "Vous pouvez préciser le courant architectural du bâtiment"
            );
            $help['msgPersonne'] = _(
                "Précisez si une personnalité est liée à l’évènement ".
                "en cliquant sur choisir".PHP_EOL.
                "Cliquez sur une personne de la liste ".
                "pour la retirer de la liste."
            );
            $help['msgValidation'] = _(
                "Validez votre article"
            );
            $help['msgNumeroArchive'] = _(
                "Renseignez dans cette case le numéro d’archive ".
                "fourni par les archives municipales si vous le connaissez. ".
                "Exemple : 44W250"
            );
            break;
        case 'helpAdresse':
            $help['msgButtonAddAdresse'] = _(
                "Ajouter une adresse"
            );
            $help['msgButtonDeleteAdresse'] = _(
                "Retirer la dernière adresse"
            );
            $help['msgVille'] = _(
                "Choisissez la ville"
            );
            $help['msgQuartier'] = _(
                "Choisissez un quartier appartenant à la ville selectionnée"
            );
            $help['msgSousQuartier'] = _(
                "Vous pouvez affiner en choisissant un sous quartier"
            );
            $help['msgRue'] = _(
                "Choisissez la rue ".
                "et le numéro de l’adresse dont vous voulez parler"
            );
            $help['msgValidation'] = _(
                "Validez votre saisie"
            );
            break;
        case 'helpImage':
            $help['msgButtonNom'] = _(
                "Donnez un intitulé à la photo"
            );
            $help['msgButtonDescription'] = _(
                "Donnez une description de l’image"
            );
            $help['msgButtonDatePriseDeVue'] = _(
                "Précisez la date de la prise de vue de la photo"
            );
            $help['msgButtonDateUpload'] = _(
                "Date à laquelle la photo à été chargé sur le site"
            );
            $help['msgButtonSource'] = _(
                "Vous pouvez préciser la source de la photo"
            );
            $help['msgEvenementsLies'] = _(
                "Voici la liste des évènements auxquels est liée l’image"
            );
            $help['msgButtonRemplacer'] = _(
                "Vous pouvez charger une autre image pour remplacer celle-ci"
            );
            $help['msgButtonNumeroArchive'] = _(
                "Vous pouvez préciser le numéro d’archive sur la photo courante."
            );
            break;
        default:
            break;
        }
        
        return $help;
    }
}


// redefinition des fonctions mbstring
if (!function_exists('pia_mail')) {

    /**
     * Alias vers mail
     * 
     * @param string $to                    Le ou les destinataires du mail
     * @param string $subject               Sujet du mail à envoyer
     * @param string $message               Message à envoyer
     * @param string $additional_headers    Chaîne à insérer à la fin
     * des en-têtes du mail
     * @param string $additional_parameters Paramètres additionnels pour la commande
     * 
     * @return bool
     * */
    function Pia_mail (
        $to, $subject, $message,
        $additional_headers = null, $additional_parameters = null
    ) {
        return mail(
            $to, $subject, $message, $additional_headers, $additional_parameters
        );
    }

    /**
     * Alias vers mb_strlen
     * 
     * @param string $str La chaîne à analyser
     * 
     * @return int
     * */
    function Pia_strlen($str)
    {
        return mb_strlen($str);
    }

    /**
     * Alias vers mb_strpos
     * 
     * @param string $haystack La chaîne à analyser
     * @param string $needle   La chaîne à chercher dans la chaîne haystack
     * @param string $offset   Doit être spécifié pour commencer à rechercher
     * un nombre arbitraire de nombre de caractères dans une chaîne
     * 
     * @return int
     * */
    function Pia_strpos($haystack, $needle, $offset = null)
    {
        if (!isset($offset)) {
            return mb_strpos($haystack, $needle);
        } else {
            return mb_strpos($haystack, $needle, $offset);
        }
    }
    
    /**
     * Alias vers mb_strrpos
     * 
     * @param string $haystack La chaîne à analyser
     * @param string $needle   La chaîne à chercher dans la chaîne haystack
     * @param string $offset   Doit être spécifié pour commencer à rechercher
     * un nombre arbitraire de nombre de caractères dans une chaîne
     * 
     * @return int
     * */
    function Pia_strrpos ($haystack, $needle, $offset = null)
    {
        if (!isset($offset)) {
            return mb_strrpos($haystack, $needle);
        } else {
            return mb_strrpos($haystack, $needle, $offset);
        }
    }

    /**
     * Alias vers mb_substr
     * 
     * @param string $string La chaîne à extraire depuis la sous-chaîne
     * @param int    $start  Position du premier caractère à utiliser depuis str
     * @param int    $length Nombre maximal de caractères à utiliser depuis str
     * 
     * @return string
     * */
    function Pia_substr ($string, $start, $length = null)
    {
        if (!isset($length)) {
            return mb_substr($string, $start);
        } else {
            return mb_substr($string, $start, $length);
        }
    }

    /**
     * Alias vers mb_strtolower
     * 
     * @param string $str La chaîne à mettre en minuscule
     * 
     * @return string
     * */
    function Pia_strtolower($str)
    {
        return mb_strtolower($str);
    }

    /**
     * Alias vers mb_strtoupper
     * 
     * @param string $str La chaîne à mettre en majuscule
     * 
     * @return string
     * */
    function Pia_strtoupper($str)
    {
        return mb_strtoupper($str);
    }
    
    /**
     * Alias vers mb_substr_count
     * 
     * @param string $haystack La chaîne à analyser
     * @param string $needle   La chaîne à chercher
     * @param string $offset   ?
     * @param string $length   ?
     * 
     * @return int
     * */
    function Pia_substrcount ($haystack, $needle, $offset = null, $length = null)
    {
        if (!isset($offset)) {
            return mb_substr_count($haystack, $needle);
        } else {
            if (!isset($length)) {
                return mb_substr_count($haystack, $needle, $offset);
            } else {
                return  mb_substr_count($haystack, $needle, $offset, $length);
            }
        }
    }

    /**
     * Alias vers mb_ereg
     * 
     * @param string $pattern L'expression rationnelle
     * @param string $string  La chaîne recherchée
     * @param string &$regs   Contient une sous-chaîne à chercher
     * 
     * @return int
     * */
    function Pia_ereg ($pattern, $string, &$regs = null)
    {
        if (!isset($regs)) {
            return mb_ereg($pattern, $string);
        } else {
            return mb_ereg($pattern, $string, $regs);
        }
    }

    /**
     * Alias vers mb_eregi
     * 
     * @param string $pattern L'expression rationnelle
     * @param string $string  La chaîne recherchée
     * @param string &$regs   Contient une sous-chaîne à chercher
     * 
     * @return int
     * */
    function Pia_eregi ($pattern, $string, &$regs = null)
    {
        if (!isset($regs)) {
            return mb_eregi($pattern, $string);
        } else {
            return mb_eregi($pattern, $string, $regs);
        }  
    }
    
    /**
     * Alias vers mb_ereg_replace
     * 
     * @param string $pattern     L'expression rationnelle
     * @param string $replacement Le texte de substitution
     * @param string $string      La chaîne recherchée
     * 
     * @return string
     * */
    function Pia_eregreplace($pattern, $replacement, $string)
    {
        return mb_ereg_replace($pattern, $replacement, $string);
    }
    
    /**
     * Alias vers mb_eregi_replace
     * 
     * @param string $pattern     L'expression rationnelle
     * @param string $replacement Le texte de substitution
     * @param string $string      La chaîne recherchée
     * 
     * @return string
     * */
    function Pia_eregireplace($pattern, $replacement, $string)
    {
        return mb_eregi_replace($pattern, $replacement, $string);
    }

    /**
     * Alias vers mb_split
     * 
     * @param string $pattern Le masque de l'expression rationnelle
     * @param string $string  La chaîne à scinder
     * @param int    $limit   Si le paramètre optionnel limit est spécifié,
     * la chaîne sera scindée en limit éléments au plus.
     * 
     * @return array
     * */
    function Pia_split($pattern, $string, $limit=null)
    {
        //array mb_split ( string $pattern , string $string [, int $limit = -1 ] )
        if (!isset($limit)) {
            return mb_split($pattern, $string);
        } else {
            return mb_split($pattern, $string, $limit);
        }
    }
    
    
    /**
     * Print debug variable content 
     * @param $variable : variable to print
     */
    function debug($variable){
    	$backtrace = debug_backtrace();
    	print_r("Line <strong>".$backtrace[0]['line']."</strong> on file : " . $backtrace[0]['file']);
    	echo "<pre>";
    	print_r($variable);
    	echo "</pre>";
    }
}
?>
