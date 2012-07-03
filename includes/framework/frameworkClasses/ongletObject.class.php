<?php

// classe de gestion de pagination
// Dorer Laurent 2008

// historique des versions
// version 1.0 --- 


class ongletObject extends config
{
	var $ongletsArray;
	var $typeOngletsArray;
	
	var $idOngletBuilder;
	var $listeOngletJSNameArray; // permet de retourner dans un tableau la liste des intitules d'onglet et de retrouver leur identifiant javascript
	
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
	
	// php5
	function __construct($idBuilder="0",$tabOnglets=array())
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

	
	// pour php4
	function init($idBuilder="0",$tabOnglets=array())
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

	function addContent($ongletName="default",$content="",$isSelected=false,$type="default",$optionsParType="")
	{
		$this->ongletsArray[$ongletName]=$content;
		$this->typeOngletsArray[$ongletName]=$type;
		$this->optionsOngletParType[$ongletName]=$optionsParType;
		
		$this->listeOngletJSNameArray[]=$this->convertIntituleOnglet($ongletName).(count($this->ongletsArray)-1).$this->idOngletBuilder;
		if($isSelected && count($this->ongletsArray)>0)
		{
			$this->numOngletSelected=count($this->ongletsArray)-1;
		}
	}

	
	
	// le champ hidden courant représente un champ du formulaire dans lequel est affiché les onglets et permet de garder l'onglet courant actif en validant le formulaire
	function setChampHiddenCourant($nomChamp)
	{
		$this->champHiddenCourant=$nomChamp;
	}
	
	function setOngletCourant($numOnglet)
	{
		$this->numOngletSelected=$numOnglet;
	}
	
	function setStylesOnglets($stylesCSS="")
	{
		$this->stylesOnglets=$stylesCSS;
	}
	
	function setStyleContenu($css="") // utilisé seulement dans la fonction getHtmlNoDiv pour le moment
	{
		$this->styleContenu = $css;	
	}

	// retourne la liste des noms des onglets
	function getJSOngletName($i=0)
	{
		return $this->listeOngletJSNameArray[$i];
	}
	
	
	function getCountOnglets()
	{
		return count($this->ongletsArray);		
	}
	
	
	// si on affiche les onglets avec cette fonction, c'est un fonctionnement different, le contenu n'est pas dans les divs, et les liens sur les onglets sont des url
	// initialisation :
	// init(0)
	// addContent("onglet1","texte onglet1",true) // dans le contenu de l'onglet selectionné on ne va mettre que le code de l'onglet
	// addContent("onglet2","<a href='onglet2'>onglet2</a>",false) // dans l'onglet qui n'est pas selectionné , on met un lien vers la page contenant le code html de l'onglet
	function getHTMLNoDiv()
	{
		$html="";
	
		if($this->getCountOnglets()>0)
		{
			// assignation des styles en dur : a changer à terme , les styles sont les memes que pour le site en ligne => a ajouter dans la feuille de style
			if(isset($this->stylesOnglets) && $this->stylesOnglets!='')
			{
				$html=$this->stylesOnglets;
			}
			else
			{
				// style des onglets par defaut
				$html="<style>
					.OngletOn{ background-color: rgb(180, 193, 205); border-top:1px solid #000000;border-right:1px solid #000000; }
					.OngletOff{ background-color:rgb(160, 173, 185); border-bottom:1px solid #000000;border-top:1px solid #000000;border-right:1px solid #000000; }
				       </style>
				";
			}
			$html.="<table width='".$this->largeurTotale."' cellspacing=0 cellpadding=0 border=0><tr><td>";
			$html.="<a name='onglet'></a>";
			$html.="<table cellspacing=0 cellpadding=0 border=0><tr>";
			// construction du tableau contenant les intitules
			$i=0;
			
			foreach($this->ongletsArray as $fieldName => $fieldValue)
			{
				$className='OngletOff';
				if($i==$this->numOngletSelected)
				{
					$className='OngletOn';
				}
				
				$html.="<td height='25' width='".$this->largeurEtiquette."' class='".$className."' style='border-left:1px solid #000000;'>&nbsp;&nbsp;&nbsp;";
				/*if($this->champHiddenCourant<>"") // lien le champs hiddenCourant est un champs cache du formulaire permettant de stocker l'onglet actif courant
				{
					$html.="document.getElementById('".$this->champHiddenCourant."').value='".$i."';";
				}
				*/
				$html.=$fieldName."&nbsp;&nbsp;&nbsp;</td><td width='4' style='border-bottom:1px solid #000000;'>&nbsp</td>";
				$i++;
			}
			
			$styleContenu = "";
			if(isset($this->styleContenu))
			{
				$styleContenu = $this->styleContenu;
			}
			
			$html.="<td width='".( $this->largeurTotale - ($i * ($this->largeurEtiquette+4)))."' style='border-bottom:1px solid #000000;'>&nbsp;</td>";
			$html.="</tr></table></td></tr><tr><td style='border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;padding:5px;".$styleContenu."'>";
			// construction des contenus
			$i=0;
			foreach($this->ongletsArray as $fieldName => $fieldValue)
			{
				if($i==$this->numOngletSelected)
				{
					// cas ou il n'y a pas de photo dans la liste
					$html.=$fieldValue;
				}
				
				$i++;
			}
			
			$html.="</td></tr></table>";
		}
	
		return $html;
	}
	
	
	
	// cette fonction va afficher les onglets avec le contenu dans des divs
	function getHTML()
	{
		$html="";
		if($this->getCountOnglets()>0)
		{
			// assignation des styles en dur : a changer à terme , les styles sont les memes que pour le site en ligne => a ajouter dans la feuille de style
			if(isset($this->stylesOnglets) && $this->stylesOnglets!='')
			{
				$html=$this->stylesOnglets;
			}
			else
			{
				// style des onglets par defaut
				$html="<style>
					.OngletOn{ background-color: rgb(180, 193, 205); border-top:1px solid #000000;border-right:1px solid #000000; }
					.OngletOff{ background-color:rgb(160, 173, 185); border-bottom:1px solid #000000;border-top:1px solid #000000;border-right:1px solid #000000; }
				       </style>
				";
			}
			
			if(!isset($this->hauteurEtiquettes))
				$this->hauteurEtiquettes=25;
			
			if(!isset($this->styleTable))
				$this->styleTable="";
			
			if(!isset($this->styleTableEtiquettes))
				$this->styleTableEtiquettes ="";
			
			if(!isset($this->styleBorderHautContenu))
				$this->styleBorderHautContenu="style='border-bottom:1px solid #000000;'";
				
			$html.="<a name='onglet'></a>";
			$html.="<table width='".$this->largeurTotale."' cellspacing=0 cellpadding=0 border=0><tr><td height=".$this->hauteurEtiquettes." ".$this->styleTable.">";			
			$html.="<table cellspacing=0 cellpadding=0 border=0 ".$this->styleTableEtiquettes."><tr>";
			// construction du tableau contenant les intitules
			$i=0;
			$largeurGauche = 0;
			if(isset($this->largeurGauche))
			{
					$largeurGauche = $this->largeurGauche;
					$html.="<td height=".$this->hauteurEtiquettes." ".$this->styleBorderHautContenu." width='".$this->largeurGauche."'>&nbsp;</td>";
			}
			
			foreach($this->ongletsArray as $fieldName => $fieldValue)
			{
				$className='OngletOff';
				if($i==$this->numOngletSelected)
				{
					$className='OngletOn';
				}
				
				$link = "";
				if($this->isAncreOnglet==true)
				{
					$link = "href='#onglet'";
				}
				
				$link="";
				switch($this->typeOngletsArray[$fieldName])
				{
					case 'link':
						$link = "href='".$this->optionsOngletParType[$fieldName]."'";
					break;
					default:
						$link = "onclick=\"".$this->createJSDivCalls($i);
						if(isset($this->champHiddenCourant) && $this->champHiddenCourant<>"") // lien le champs hiddenCourant est un champs cache du formulaire permettant de stocker l'onglet actif courant
						{
							$link.="document.getElementById('".$this->champHiddenCourant."').value='".$i."';";
						}
						$link.="\"";
					break;
				}
				
				$spaces="";
				if($this->isSpaceInLibelle == true)
				{
					$spaces = "&nbsp;&nbsp;&nbsp;";
				}
				
				$html.="<td height='".$this->hauteurEtiquettes."' width='".$this->largeurEtiquette."' id='".$this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder."_onglet' class='".$className."' style='border-left:1px solid #000000;text-align:center;'>".$spaces."<a style='cursor:pointer;' ".$link;
				$html.=">".$fieldName."</a>".$spaces."</td><td width='4' height='".$this->hauteurEtiquettes."' ".$this->styleBorderHautContenu.">&nbsp</td>";
				$i++;
				
			}
			// dernier td => pour affichage du border //
			
			
			$html.="<td height='".$this->hauteurEtiquettes."' width='".( $this->largeurTotale - ($i * ($this->largeurEtiquette+4))-$largeurGauche)."' ".$this->styleBorderHautContenu.">&nbsp;</td>";
			
			$styleContours = "";
			if($this->isContours==true)
			{
				if(isset($this->styleContoursContenu))
				{
					$styleContours = $this->styleContoursContenu;
				}
				else
					$styleContours = "style='border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;'";
			}
			
			$html.="</tr></table></td></tr><tr><td ".$styleContours.">";
			
			
			// construction des contenus
			$i=0;
			foreach($this->ongletsArray as $fieldName => $fieldValue)
			{
				if($i==$this->numOngletSelected)
				{
					$styleDisplay='block';
					
				}
				else
				{
					$styleDisplay='none';	
				}
				
				// cas ou il n'y a pas de photo dans la liste
				if($fieldValue=="<table cellspacing='0' cellpadding='0' border='0'></table>")
				{
					$fieldValue="<table cellspacing='0' width='100%' cellpadding='0' border='1'><tr><td><br><center>Pas de document</center></br></td></tr></table>";
				}
				
				$html.="<div id='".$this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder."' style='display:".$styleDisplay.";'>".$fieldValue."</div>";
				$i++;
			}
			
			
			$html.="</td></tr></table>";
		}
		return $html;
		
	}

	function convertIntituleOnglet($intitule) // public static
	{
		$retour="";
		$retour=str_replace(" ","_",$intitule);
	
		return $retour;
	}
	
	
	function createJSDivCalls($indiceCourant=0)
	{
		$i=0;
		$js="";
		foreach($this->ongletsArray as $fieldName => $fieldValue)
		{
			if($i==$indiceCourant)
			{
				$styleDisplay='block';
				$className='OngletOn';

			}
			else
			{
				$styleDisplay='none';
				$className='OngletOff';
			}
			$js.="document.getElementById('".$this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder."').style.display='".$styleDisplay."';";
			$js.="document.getElementById('".$this->convertIntituleOnglet($fieldName.$i).$this->idOngletBuilder."_onglet').className='".$className."';";
			
			$i++;
		}
		return $js;
	}
	
	
	function setLargeurTotale($largeur)
	{
		$this->largeurTotale=$largeur;
	}
	
	function setLargeurEtiquette($largeur)
	{
		$this->largeurEtiquette=$largeur;
	}
	
	function setEspaceGaucheOnglets($largeur)
	{
		$this->largeurGauche = $largeur;
	}
	
	function setIsContours($bool=true)
	{
		$this->isContours=$bool;
	}
	
	function setIsAncre($bool=true)
	{
		$this->isAncreOnglet=$bool;
	}
	
	function setIsSpaceInLibelle($bool=true)
	{
		$this->isSpaceInLibelle=$bool;
	}
	
	function setHauteurOnglets($hauteur=25)
	{
		$this->hauteurEtiquettes = $hauteur;
	}
	
	function setStyleContoursContenu($style="style='border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;'")
	{
		$this->styleContoursContenu = $style;
	}
	
	function setStyleTable($style="")
	{
		$this->styleTable = $style;
	}
	
	function setStyleTableEtiquettes($style="")
	{
		$this->styleTableEtiquettes = $style;
	}
	
	function setStyleBorderHautContenu($style="")
	{
		$this->styleBorderHautContenu=$style;
	}
}

?>
