<?php
/**
 * Classe OngletObject
 * 
 * PHP Version 5.4.6
 * 
 * @category Framework
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * */

/**
 * Classe de gestion de pagination
 * 
 * PHP Version 5.4.6
 * 
 * @category Framework
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Laurent Dorer <laurent_dorer@yahoo.fr>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * */
class OngletObject extends config
{
    var $ongletsArray;
    var $typeOngletsArray;
    
    var $idOngletBuilder;
    /**
     * Permet de retourner dans un tableau la liste des intitules d'onglet
     * et de retrouver leur identifiant javascript
     * */
    var $listeOngletJSNameArray; 
    
    var $largeurTotale;
    var $largeurEtiquette;

    var $numOngletSelected;
    var $champsHiddenCourant;
    
    var $isContours;
    var $isAncreOnglet;
    var $isSpaceInLibelle;
    var $optionsOngletParType;
    var $stylesOnglets;
    var $styleContenu;
    
    /**
     * PHP 5
     * 
     * @param string $idBuilder  ?
     * @param array  $tabOnglets ?
     * 
     * @return void
     * */
    function __construct($idBuilder="0", $tabOnglets=array())
    {
        $this->ongletsArray=$tabOnglets;
        $this->typeOngletsArray=array();
        $this->idOngletBuilder=$idBuilder;
        $this->isContours = true;
        $this->isAncreOnglet = true;
        $this->isSpaceInLibelle = true;
        $this->optionsOngletParType = array();
        $this->styleContenu = "";
    }

    
    /**
     * Pour PHP 4
     * 
     * @param string $idBuilder  ?
     * @param array  $tabOnglets ?
     * 
     * @return void
     * */
    function init($idBuilder="0", $tabOnglets=array())
    {
        $this->ongletsArray=$tabOnglets;
        $this->typeOngletsArray=array();
        $this->optionsOngletParType = array();
        $this->idOngletBuilder=$idBuilder;
        $this->largeurTotale=680;
        $this->largeurEtiquette=150;
        $this->numOngletSelected=0;
        $this->champHiddenCourant="";
        $this->isContours = true;
        $this->isAncreOnglet = true;
        $this->isSpaceInLibelle = true;
        $this->styleContenu = "";
    }

    /**
     * Ajoute du contenu
     * 
     * @param string $ongletName     Nom de l'onglet
     * @param string $content        Contenu
     * @param bool   $isSelected     L'onglet est-il sélectionné ?
     * @param string $type           Type
     * @param string $optionsParType ?
     * 
     * @return void
     * */
    function addContent(
        $ongletName="default", $content="",
        $isSelected=false, $type="default", $optionsParType=""
    ) {
        $this->ongletsArray[$ongletName]=$content;
        $this->typeOngletsArray[$ongletName]=$type;
        $this->optionsOngletParType[$ongletName]=$optionsParType;
        
        $this->listeOngletJSNameArray[]=$this->convertIntituleOnglet($ongletName).
        (count($this->ongletsArray)-1).$this->idOngletBuilder;
        if ($isSelected && count($this->ongletsArray)>0) {
            $this->numOngletSelected=count($this->ongletsArray)-1;
        }
    }

    
    
    /**
     * Le champ hidden courant représente un champ du formulaire
     * dans lequel est affiché les onglets
     * et permet de garder l'onglet courant actif en validant le formulaire
     * 
     * @param string $nomChamp Nom du champ
     * 
     * @return void
     * */
    function setChampHiddenCourant($nomChamp)
    {
        $this->champHiddenCourant=$nomChamp;
    }
    
    /**
     * Définit l'onglet courant
     * 
     * @param int $numOnglet Numéro de l'onglet
     * 
     * @return void
     * */
    function setOngletCourant($numOnglet)
    {
        $this->numOngletSelected=$numOnglet;
    }
    
    /**
     * Définit le style des onglets
     * 
     * @param string $stylesCSS CSS
     * 
     * @return void
     * */
    function setStylesOnglets($stylesCSS="")
    {
        $this->stylesOnglets=$stylesCSS;
    }
    
    /**
     * Utilisé seulement dans la fonction getHtmlNoDiv pour le moment
     * 
     * @param string $css CSS
     * 
     * @return void
     * */
    function setStyleContenu($css="") 
    {
        $this->styleContenu = $css;    
    }

    /**
     * Retourne la liste des noms des onglets
     * 
     * @param int $i ?
     * 
     * @return array
     * */
    function getJSOngletName($i=0)
    {
        return $this->listeOngletJSNameArray[$i];
    }
    
    /**
     * Retourne le nombre d'onglets
     * 
     * @return int Nombre d'onglets
     * */
    function getCountOnglets()
    {
        return count($this->ongletsArray);        
    }
    
    /**
     * Si on affiche les onglets avec cette fonction,
     * c'est un fonctionnement different, le contenu n'est pas dans les divs,
     * et les liens sur les onglets sont des url
     * 
     * initialisation :
     * init(0)
     * addContent("onglet1", "texte onglet1", true)
     * Ddans le contenu de l'onglet selectionné
     * on ne va mettre que le code de l'onglet
     * addContent("onglet2", "<a href='onglet2'>onglet2</a>", false)
     * Dans l'onglet qui n'est pas selectionné,
     * on met un lien vers la page contenant le code html de l'onglet
     * 
     * @return string HTML
     * */
    function getHTMLNoDiv()
    {
        $html="";
    
        if ($this->getCountOnglets()>0) {
            /*
             * Assignation des styles en dur : a changer à terme,
             * les styles sont les memes que pour le site en ligne
             * => a ajouter dans la feuille de style
             * */
            if (isset($this->stylesOnglets) && $this->stylesOnglets!='') {
                $html=$this->stylesOnglets;
            }

            $html.="<table width='".$this->largeurTotale.
            "' cellspacing=0 cellpadding=0 border=0><tr><td>";
            $html.="<a name='onglet'></a>";
            $html.="<table cellspacing=0 cellpadding=0 border=0><tr>";
            // construction du tableau contenant les intitules
            $i=0;
            
            foreach ($this->ongletsArray as $fieldName => $fieldValue) {
                $className='OngletOff';
                if ($i==$this->numOngletSelected) {
                    $className='OngletOn';
                }
                
                $html.="<td height='25' width='".$this->largeurEtiquette.
                "' class='".$className.
                "' style='border-left:1px solid #000000;'>&nbsp;&nbsp;&nbsp;";
                /*
                 * Lien le champs hiddenCourant est un champs cache du formulaire
                 * permettant de stocker l'onglet actif courant
                 * */
                /*if ($this->champHiddenCourant<>"") 
                {
                    $html.="document.getElementById('".$this->champHiddenCourant."').
                    * value='".$i."';";
                }
                */
                $html.=$fieldName."&nbsp;&nbsp;&nbsp;</td><td width='4' ".
                "style='border-bottom:1px solid #000000;'>&nbsp</td>";
                $i++;
            }
            
            $styleContenu = "";
            if (isset($this->styleContenu)) {
                $styleContenu = $this->styleContenu;
            }
            
            $html.="<td width='".
            ( $this->largeurTotale - ($i * ($this->largeurEtiquette+4))).
            "' style='border-bottom:1px solid #000000;'>&nbsp;</td>";
            $html.="</tr></table></td></tr><tr><td ".
            "style='border-left:1px solid #000000;border-right:1px solid #000000;".
            "border-bottom:1px solid #000000;padding:5px;".$styleContenu."'>";
            // construction des contenus
            $i=0;
            foreach ($this->ongletsArray as $fieldName => $fieldValue) {
                if ($i==$this->numOngletSelected) {
                    // cas ou il n'y a pas de photo dans la liste
                    $html.=$fieldValue;
                }
                
                $i++;
            }
            
            $html.="</td></tr></table>";
        }
    
        return $html;
    }
    
    
    
    /**
     * Cette fonction va afficher les onglets avec le contenu dans des divs
     * 
     * @return string HTML
     * */
    function getHTML()
    {
        $html="";
        if ($this->getCountOnglets()>0) {
            /*
             * assignation des styles en dur : a changer à terme,
             * les styles sont les memes que pour le site en ligne
             * => a ajouter dans la feuille de style
             * */
            if (isset($this->stylesOnglets) && $this->stylesOnglets!='') {
                $html=$this->stylesOnglets;
            }

            if (!isset($this->hauteurEtiquettes)) {
                $this->hauteurEtiquettes=25;
            }
            
            if (!isset($this->styleTable)) {
                $this->styleTable="";
            }
            
            if (!isset($this->styleTableEtiquettes)) {
                $this->styleTableEtiquettes ="";
            }
            
            if (!isset($this->styleBorderHautContenu)) {
                $this->styleBorderHautContenu
                    ="style='border-bottom:1px solid #000000;'";
            }
                
            $html.="<a name='onglet'></a>";
            $html.="<table width='".$this->largeurTotale.
            "' cellspacing=0 cellpadding=0 border=0><tr><td height=".
            $this->hauteurEtiquettes." ".$this->styleTable.">";            
            $html.="<table cellspacing=0 cellpadding=0 border=0 ".
            $this->styleTableEtiquettes."><tr class='onglets'>";
            // construction du tableau contenant les intitules
            $i=0;
            $largeurGauche = 0;
            if (isset($this->largeurGauche)) {
                    $largeurGauche = $this->largeurGauche;
                    $html.="<td height=".$this->hauteurEtiquettes." ".
                    $this->styleBorderHautContenu." width='".
                    $this->largeurGauche."'>&nbsp;</td>";
            }
            
            foreach ($this->ongletsArray as $fieldName => $fieldValue) {
                $className='OngletOff';
                if ($i==$this->numOngletSelected) {
                    $className='OngletOn';
                }
                
                $link = "";
                if ($this->isAncreOnglet==true) {
                    $link = "href='#onglet'";
                }
                
                $link="";
                switch($this->typeOngletsArray[$fieldName]) {
                case 'link':
                    $link = "href='".$this->optionsOngletParType[$fieldName]."'";
                    break;
                default:
                    $link = "onclick=\"".$this->createJSDivCalls($i);
                    /* lien le champs hiddenCourant est un champs cache
                    du formulaire permettant de stocker l'onglet actif courant
                    * */
                    if (isset($this->champHiddenCourant)
                        && $this->champHiddenCourant<>""
                    ) {
                        $link.="document.getElementById('".
                        $this->champHiddenCourant."').value='".$i."';";
                    }
                    $link.="\"";
                    break;
                }
                
                $spaces="";
                if ($this->isSpaceInLibelle == true) {
                    $spaces = "&nbsp;&nbsp;&nbsp;";
                }
                
                $html.="<td height='".$this->hauteurEtiquettes."' width='".
                $this->largeurEtiquette."' id='".
                $this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder.
                "_onglet' class='".$className."' >".$spaces.
                "<a style='cursor:pointer;' ".$link;
                $html.=">".$fieldName."</a>".$spaces.
                "</td><td width='4' height='".$this->hauteurEtiquettes.
                "' ".">&nbsp</td>";
                $i++;
                
            }
            // dernier td => pour affichage du border //
            
            
            $html.="<td height='".$this->hauteurEtiquettes."' width='".
            ( $this->largeurTotale - ($i * ($this->largeurEtiquette+4))
            -$largeurGauche).
            "' >&nbsp;</td>";
            
            $styleContours = "";
            if ($this->isContours==true) {
                if (isset($this->styleContoursContenu)) {
                    $styleContours = $this->styleContoursContenu;
                } else {
                    $styleContours = "style='border-left:1px solid #000000;".
                    "border-right:1px solid #000000;".
                    "border-bottom:1px solid #000000;'";
                }
            }
            
            $html.="</tr></table></td></tr><tr><td class='event ".$styleContours.">";
            
            
            // construction des contenus
            $i=0;
            foreach ($this->ongletsArray as $fieldName => $fieldValue) {
                if ($i==$this->numOngletSelected) {
                    $styleDisplay='block';
                    
                } else {
                    $styleDisplay='none';    
                }
                $testedFieldValue=="<table cellspacing='0' cellpadding='0' ".
                "border='0'></table>"
                // cas ou il n'y a pas de photo dans la liste
                if ($fieldValue==$testedFieldValue) {
                    $fieldValue="<table cellspacing='0' width='100%' ".
                    "cellpadding='0'".
                    " border='1'><tr><td><br><center>Pas de document</center>".
                    "</br></td></tr></table>";
                }
                
                $html.="<div id='".$this->convertIntituleOnglet($fieldName.$i).
                $this->idOngletBuilder."' style='display:".$styleDisplay.
                ";'>".$fieldValue."</div>";
                $i++;
            }
            
            
            $html.="</td></tr></table>";
        }
        return $html;
        
    }

    /**
     * Convertit l'intitulé d'un onglet
     * 
     * @param string $intitule Intitulé
     * 
     * @return string
     * */
    function convertIntituleOnglet($intitule) // public static
    {
        $retour="";
        $retour=str_replace(" ", "_", $intitule);
    
        return $retour;
    }
    
    /**
     * ?
     * 
     * @param ? $indiceCourant ?
     * 
     * @return ?
     * */
    function createJSDivCalls($indiceCourant=0)
    {
        $i=0;
        $js="";
        foreach ($this->ongletsArray as $fieldName => $fieldValue) {
            if ($i==$indiceCourant) {
                $styleDisplay='block';
                $className='OngletOn';

            } else {
                $styleDisplay='none';
                $className='OngletOff';
            }
            $js.="document.getElementById('".
            $this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder.
            "').style.display='".$styleDisplay."';";
            $js.="document.getElementById('".
            $this->convertIntituleOnglet($fieldName.$i).
            $this->idOngletBuilder."_onglet').className='".$className."';";
            
            $i++;
        }
        return $js;
    }
    
    /**
     * ?
     * 
     * @param int $largeur ?
     * 
     * @return void
     * */
    function setLargeurTotale($largeur)
    {
        $this->largeurTotale=$largeur;
    }
    
    /**
     * ?
     * 
     * @param int $largeur ?
     * 
     * @return void
     * */
    function setLargeurEtiquette($largeur)
    {
        $this->largeurEtiquette=$largeur;
    }
    
    /**
     * ?
     * 
     * @param int $largeur ?
     * 
     * @return void
     * */
    function setEspaceGaucheOnglets($largeur)
    {
        $this->largeurGauche = $largeur;
    }
    
    /**
     * ?
     * 
     * @param bool $bool ?
     * 
     * @return void
     * */
    function setIsContours($bool=true)
    {
        $this->isContours=$bool;
    }
    
    /**
     * ?
     * 
     * @param bool $bool ?
     * 
     * @return void
     * */
    function setIsAncre($bool=true)
    {
        $this->isAncreOnglet=$bool;
    }
    
    /**
     * ?
     * 
     * @param bool $bool ?
     * 
     * @return void
     * */
    function setIsSpaceInLibelle($bool=true)
    {
        $this->isSpaceInLibelle=$bool;
    }
    
    /**
     * Définit la hauteur des onglets
     * 
     * @param string $hauteur Hauteur
     * 
     * @return void
     * */
    function setHauteurOnglets($hauteur=25)
    {
        $this->hauteurEtiquettes = $hauteur;
    }
    
    
    /**
     * Définit le style des contours du contenu
     * 
     * @param string $style CSS
     * 
     * @return void
     * */
    function setStyleContoursContenu(
        $style="style='border-left:1px solid #000000;".
        "border-right:1px solid #000000;".
        "border-bottom:1px solid #000000;'"
    ) {
        $this->styleContoursContenu = $style;
    }
    
    /**
     * Définit le style du tableau
     * 
     * @param string $style CSS
     * 
     * @return void
     * */
    function setStyleTable($style="")
    {
        $this->styleTable = $style;
    }
    
    /**
     * Définit le style de ?
     * 
     * @param string $style CSS
     * 
     * @return void
     * */
    function setStyleTableEtiquettes($style="")
    {
        $this->styleTableEtiquettes = $style;
    }
    
    /**
     * Définit le style de la bordure du haut
     * 
     * @param string $style CSS
     * 
     * @return void
     * */
    function setStyleBorderHautContenu($style='')
    {
        $this->styleBorderHautContenu=$style;
    }
}

?>
