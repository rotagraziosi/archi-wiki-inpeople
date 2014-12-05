<?php
/**
 * Classe ArchiPage
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

/**
 * Classe de gestion des connexions a la bdd
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 -
* separation de la classe de connexion de l'objet config
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
class ConnexionBdd extends config
{
    protected $ressource;
    /**
     * Constructeur de connexionBdd
     * 
     * @param string $bddName Base de donn�es
     * @param string $host    H�te
     * @param string $user    Utilisateur
     * @param string $pass    Mot de passe
     * 
     * @return void
     * */
    function __construct($bddName, $host, $user, $pass)
    {
        $this->ressource = mysql_connect($host, $user, $pass)
            or die(file_get_contents(__DIR__.'/../../../maintenance.html'));
        mysql_select_db($bddName) or die(mysql_error());
        mysql_query('SET NAMES "utf8"') or die (mysql_error());    
    }
    
    /**
     * Se connecter � une base de donn�e
     * 
     * @param string $host     H�te
     * @param string $user     Utilisateur
     * @param string $password Mot de passe
     * @param string $bdd      Base de donn�es
     * 
     * @return void
     * */
    function connectTo($host='',$user='',$password='',$bdd='')
    {
        $this->ressource 
            = mysql_connect($host, $user, $password) or die(mysql_error());
        mysql_select_db($bdd) or die(mysql_error());
        mysql_query('SET NAMES "utf8"') or die (mysql_error());
    }
    
    /**
     * Ex�cute une requ�te SQL
     * 
     * @param string $requete    Requ�te
     * @param bool   $silencieux Afficher une erreur ?
     * 
     * @return object
     * */
    function requete($requete="",$silencieux=false)
    {
    	/*
    	global $countTest;
    	debug("Requete n°".$countTest++);
    	*/
        if ($silencieux==false) {
            $res = mysql_query($requete) 
            or 
            die($requete.' -- '.mysql_error().' -- <br/> Request in file : <b>'.debug_backtrace()[0]['file'].'</b><br/> on line <b>'.debug_backtrace()[0]['line']).'</b>';
            
            
        } else {
            $res = mysql_query($requete);
        }
        return $res;
    }
    
    /**
    * Chaque fois qu'on appelle un getLock,
    * il faut d'abord que l'utilisateur precedent ai fait un freeLock
    * (cas du blocage lors de l'enregistrement dans la base)
    * Si utilisateur a ete pr�cis�,
    * c'est donc que l'on tente de mettre a jour un element
    * (evenement par exemple, ceci ne correspond
    * pas au blocage lors de l'enregistrement dans une table)
    * Cette fonction sert donc aussi �
    * bloquer l'edition d'un element grace a isLocked
    * 
    * @param array $tableName Nom de la table
    * @param array $config    Param�tres
    * 
    * @return void
    * */
    public function getLock($tableName = array(), $config=array())
    {
        if (isset($config['minutes'])) {
            $minutes = $config['minutes'];
        } else {
            $minutes = 1;
        }
        if (isset($config['idUtilisateur'])) {
            $idUtilisateur = $config['idUtilisateur']; 
            /**
             * Un utilisateur a ete pr�cis�,
             * c'est donc que l'on tente de mettre a jour un element
             * (evenement par exemple, ceci ne correspond
             * pas au blocage lors de l'enregistrement dans une table)
             * */
        } else {
            $idUtilisateur = 0;
        }
        
        $timeOut = $minutes*60;
        $timeOutMaj = false;
        foreach ($tableName AS $nomTable) {
            
            if ($idUtilisateur != 0) {
                $qid_delete=$this->requete(
                    "DELETE FROM verrouTable WHERE verrouName='".
                    $nomTable."' AND timeOut<NOW()", true
                );
                $reqVerif = "SELECT verrouName FROM verrouTable WHERE verrouName='".
                $nomTable."' AND timeOut>NOW() and idUtilisateur='".
                $idUtilisateur."'";
                $resVerif = $this->requete($reqVerif);
                if (mysql_num_rows($resVerif)>0) {
                    /**
                     * Un verrou non expir� existe,
                     * on ne va donc pas tenter d'en creer un autre
                     * du meme non pour le meme utilisateur,
                     * cela ferait bloquer le site pendant $minutes.
                     * On met juste le timeOut du verrou a jour.
                     * */
                    $timeOutMaj=true;
                    $reqMaj = "update verrouTable set timeOut = NOW()+SEC_TO_TIME(".
                    $timeOut.") where verrouName='".$nomTable.
                    "' and idUtilisateur='".$idUtilisateur."' ";
                    $resMaj = $this->requete($reqMaj);
                }
            }
            
            if (!$timeOutMaj) {
                do {
                    $qid_delete=$this->requete(
                        "DELETE FROM verrouTable WHERE verrouName='".
                        $nomTable."' AND timeOut<NOW()", true
                    );
                    /**
                     * Le lock est valable pendant 1 minute
                     * (largement suffisement pour une operation dans la base
                     * (ajout, suppression ... )
                     * sauf si $config['minutes'] est pr�cis�
                     * */
                    $qid = $this->requete(
                        "insert into verrouTable (verrouName,timeOut,idUtilisateur)".
                        " values ('".
                        $nomTable."',NOW()+SEC_TO_TIME(".$timeOut."),".
                        $idUtilisateur.")", true
                    );
                    usleep(100000); // 100 ms ->  10 fois par seconde
                } while (!$qid);
            }
        }
    }
    
    /**
     * ???
     * 
     * @param array $tableName Nom de la table
     * @param array $config    Param�tres
     * 
     * @return void
     * */
    public function freeLock($tableName = array(),$config=array())
    {
        if (isset($config['idUtilisateur'])) {
            $idUtilisateur = $config['idUtilisateur'];
        } else {
            $idUtilisateur = 0;
        }
            
        foreach ($tableName AS $nomTable) {
            $qid_delete=$this->requete(
                "DELETE FROM verrouTable WHERE verrouName='".$nomTable.
                "' and idUtilisateur='".$idUtilisateur."'"
            );
        }
    
    }
    
    /**
     * Fonction qui permet de voir si une table
     * ou un element est bloqu�, par un utilisateur ou non
     * 
     * @param string $tableName     Nom de la table
     * @param int    $idUtilisateur Utilisateur
     * 
     * @return bool
     * */
    public function isLocked($tableName='',$idUtilisateur=0)
    {
        $sqlUtilisateur="";
        if ($idUtilisateur!=0) {
            $sqlUtilisateur = "and idUtilisateur!='".$idUtilisateur."'";
        }
        $retour=false;
        $req = "select verrouName from verrouTable where verrouName='".
        $tableName."' and timeOut>NOW() ".$sqlUtilisateur;
        $res = $this->requete($req);

        if (mysql_num_rows($res)>0) {
                $retour=true;
        }

        return $retour;
    }
    
    /**
     * Renvoi la liste des champs d'une table
     * 
     * @param string $tableName Nom de la table
     * 
     * @return array
     * */
    public function getFieldsFromTable($tableName='')
    {
        $retour = array();
        if ($tableName!='') {
            $res = $this->requete("SHOW COLUMNS FROM $tableName");
            while ($fetch = mysql_fetch_assoc($res)) {
                $retour[] = $fetch['Field'];
            }
        }
        return $retour;
    }
    
    /**
     * Obtenir toutes les tables de la base de donn�es
     * 
     * @return array
     * */
    public function getTablesFromCurrentDatabase()
    {
        $retour = array();
        
        $res = $this->requete("show tables;");
        $currentDatabaseName = $this->getCurrentDatabaseName();
        
        while ($fetch = mysql_fetch_assoc($res)) {
            $retour[] = $fetch['Tables_in_'.$currentDatabaseName];
        }
        
        return $retour;
    }
    
    /**
     * Obtenir le nom de la base de donn�es
     * 
     * @return string
     * */
    public function getCurrentDatabaseName()
    {
        $req = "SELECT DATABASE();";
        $res = $this->requete($req);
        $fetch = mysql_fetch_assoc($res);
        return $fetch['DATABASE()'];
    }
    
    /**
     * Obtenir la clef PRIMARY d'une table
     * 
     * @param string $nomTable Nom de la table
     * 
     * @return string
     * */
    public function getPrimaryKeyFieldNameFromTable($nomTable='')
    {
        $retour = "";
        if ($nomTable!='') {
            $res = $this->requete("SHOW COLUMNS FROM $nomTable");
            $trouve=false;
            while (!$trouve && $fetch = mysql_fetch_assoc($res)) {
                if ($fetch['Key']=='PRI') {
                    $retour = $fetch['Field'];
                    $trouve=true;
                }
            }
        }
        return $retour;
    }
    
    /**
     * Obtenir un tableau associatif depuis une requ�te contenue dans un fichier SQL
     * 
     * @param string $file SQL file
     * 
     * @return array
     * */
    static function getResultFromFile ($file)
    {
        global $config;
        $req = file_get_contents($file);
        $list=array();
        $result=$config->connexionBdd->requete($req);
        while (($list[] = mysql_fetch_assoc($result)) || array_pop($list)) {
            
        }
        return $list;
    }
    
    /**
     * Permet de s�curiser les variables utilis�es dans les requ�tes.
     * Attention : la fonction mysql_real_escape_string est obsol�te !
     * 
     * @param mixed $var La variable � s�curiser
     * 
     * @return string Variable s�curis�e
     * */
    function quote ($var)
    {
        return mysql_real_escape_string($var, $this->ressource);
    }
    
}

?>
