<?php
/**
 * Classe DroitsObject
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
// FRAMEWORK Gestion des droits 0.1
// laurent dorer 21/04/2009

// cette classe fonctionne avec des tables sql :
// pour la gestion des droits :
/*
une table droits (liaison entre les tables elements du site et profil
une table elements du site (identification d'elements du site ou l'on applique les droits)
une table de profil (administrateur, modéteur, utilisateur, internaute etc)
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

class DroitsObject extends config
{
    
    /**
     * Constructeur de DroitsObject
     * 
     * @return void
     * */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Enregistre les droits
     * 
     * @return void
     * */
    public function enregistreDroits()
    {
        if (isset($this->variablesPost['idProfilCourant']) && $this->variablesPost['idProfilCourant']!='') {    
            $reqDelete = "DELETE FROM droits WHERE idProfil='".$this->variablesPost['idProfilCourant']."'";
            $resDelete = $this->connexionBdd->requete($reqDelete);
            
            if (isset($this->variablesPost['idElementSite'])) {
                foreach ($this->variablesPost['idElementSite'] as $indice => $value) {
                    $reqInsert="INSERT INTO droits (idProfil,idElementSite,acces) VALUES ('".$this->variablesPost['idProfilCourant']."','".$value."','1')";
                    $resInsert=$this->connexionBdd->requete($reqInsert);
                }
            }
        }
    }
    
    /**
     * Formulaire de mise a jour d'un profil
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getFormulaireGestionDroits($params=array())
    {
        $retour="";
        
        $idProfilCourant = 1;
        if (isset($this->variablesGet['archiIdProfil']) && $this->variablesGet['archiIdProfil']!='' && $this->variablesGet['archiIdProfil']!='0') {
            $idProfilCourant = $this->variablesGet['archiIdProfil'];
        }
        
        $reqElements = "SELECT * FROM droitsElementsSite";//"SELECT * FROM droits WHERE idProfil=(SELECT idProfil FROM droitsProfils WHERE libelle='".$params['nomProfil']."')";
        $resElements = $this->connexionBdd->requete($reqElements);
        $tableau = new tableau();
        
        $tableau->addValue("Tag élément du site", "style='font-weight:bold;'");
        $tableau->addValue("droits", "style='font-weight:bold;'");
        
        while ($fetchElements = mysql_fetch_assoc($resElements)) {
            $tableau->addValue($fetchElements['libelle']);
            
            $reqProfil = "
                    SELECT d.idDroit as idDroit, d.idProfil as idProfil,d.idElementSite as idElementSite, d.acces as acces,
                            des.libelle as libelleElementSite,dp.libelle as libelleProfil
                    FROM droits d
                    LEFT JOIN droitsElementsSite des ON des.idElementSite = d.idElementSite
                    LEFT JOIN droitsProfils dp ON dp.idProfil = d.idProfil
                    WHERE dp.idProfil='".$idProfilCourant."'
                    AND des.idElementSite='".$fetchElements['idElementSite']."'
                    ";
            
            $resProfil = $this->connexionBdd->requete($reqProfil);
            $fetchProfil=mysql_fetch_assoc($resProfil);
            
            

            $checked="";
            if (isset($fetchProfil['acces']) && $fetchProfil['acces']=='1') {
                $checked="checked";
            }
            $tableau->addValue("<input type='checkbox' name='idElementSite[]' value='".$fetchElements['idElementSite']."' $checked>");
        }
        
        $retour = $tableau->createHtmlTableFromArray(2);
        $retour.="<input type='hidden' name='idProfilCourant' value='".$idProfilCourant."'>";
        return $retour;
    }
    
    /**
     * ?
     * 
     * @return array
     * */
    public function getProfilCourantFormulaire()
    {
        $retour=array();
        
        $idProfilCourant = 1;
        if (isset($this->variablesGet['archiIdProfil']) && $this->variablesGet['archiIdProfil']!='' && $this->variablesGet['archiIdProfil']!='0') {
            $idProfilCourant = $this->variablesGet['archiIdProfil'];
        }
        
        $req = "SELECT libelle,idProfil FROM droitsProfils WHERE idProfil='".$idProfilCourant."'";
        $res = $this->connexionBdd->requete($req);
        $fetch = mysql_fetch_assoc($res);
        $retour=array('libelle'=>$fetch['libelle'],"idProfil"=>$fetch['idProfil']);
        return $retour;
    }
    
    /**
     * Obtenir un profil depuis son ID
     * 
     * @param int $idProfil ID du profil
     * 
     * @return array
     * */
    public function getProfilFromIdProfil($idProfil=0)
    {
        $retour = array();
        $req = "SELECT idProfil,libelle FROM droitsProfils WHERE idProfil = '".$idProfil."'";
        $res = $this->connexionBdd->requete($req);
        
        $fetch = mysql_fetch_assoc($res);
        
        return $fetch;
    }
    
    /**
     * Obtenir une liste des profils
     * 
     * @return array
     * */
    public function getArrayListeProfils()
    {
        $retour=array();
        $req = "SELECT idProfil,libelle FROM droitsProfils";
        $res = $this->connexionBdd->requete($req);
        while ($fetch = mysql_fetch_assoc($res)) {
            $retour[$fetch['idProfil']]=$fetch['libelle'];
        }
        return $retour;
    }
    
    
    /**
     * Vérifie si un utilisateur est autorisé à effectuer une action
     * 
     * @param string $tagName     Nom du droit
     * @param int    $userProfile ID de l'utilisateur
     * 
     * @return bool
     * */
    public function isAuthorized($tagName='',$userProfile=0)
    {
        $retour=false;
        if ($tagName!='' && $userProfile!=0) {
            $reqVerif = "
                        SELECT d.acces as acces
                        FROM droits d
                        RIGHT JOIN droitsElementsSite des ON des.idElementSite = d.idElementSite
                        WHERE d.idProfil = '".$userProfile."'
                        AND des.libelle='".$tagName."'
                        ";
            $resVerif = $this->connexionBdd->requete($reqVerif);
            $fetchVerif = mysql_fetch_assoc($resVerif);
            if ($fetchVerif['acces']=='1')
                $retour=true;
                        
        }
        
        return $retour;
    }
    
}
?>
