<?php
class tableau
{
	var $tabValues;
	var $tabStyles;

	
	function __construct()
	{
		// initialisation du tableau contenant les valeurs
		$this->tabValues=array();
		$this->tabStyles=array();
		$this->tabProperties=array(); // sert a transmettre des parametres , par exemple une longueur de div dans le cas de l'affichage par div, a la saisie des contenus on pourra preciser un tableau array('x'=>valLargeur,'y'=>valLongeur)
	}
	
	public function addValue($value,$style='',$properties=array())
	{
		$this->tabValues[count($this->tabValues)]=$value;
		$this->tabStyles[count($this->tabStyles)]=$style;
		$this->tabProperties[count($this->tabProperties)]=$properties;
	}
	
	public function getNbValues()
	{
		return count($this->tabValues);
	}
	
	// alias de la fonction suivante
	public function createTable($nbColonnes=4,$styleTableau='border:1px;',$classTableau='',$cellProperty='',$tableauProperties='')
	{
		return $this->createHtmlTableFromArray($nbColonnes,$styleTableau,$classTableau,$cellProperty,$tableauProperties);
	}
	
	
	// construction d'un tableau html ou le précise le nom de colonnes en parametres
	public function createHtmlTableFromArray($nbColonnes=4,$styleTableau='border:1px;',$classTableau='',$cellProperty='',$tableauProperties='')
	{
	
		$htmlClassTableau = "";
		if($classTableau!='')
			$htmlClassTableau = " class='".$classTableau."' ";
	
		$html="<table style=\"".$styleTableau."\" ".$htmlClassTableau." ".$tableauProperties."><tbody>";
		
		
		$nbValues = count($this->tabValues);
		
		// calcul du nombre de cases totales :
		if($nbValues%$nbColonnes==0)
		{
			$nbLignes = $nbValues / $nbColonnes;
		}
		else
		{
			$nbLignes = intval($nbValues / $nbColonnes)+1;
		}
		
		$nbCases = $nbLignes * $nbColonnes;
		
		$ligne=0; // coordonnées horizontales
		$colonne=0; // coordonnées verticales
		for($i=0 ; $i<$nbCases ; $i++)
		{
			if($colonne==0)
			{
				$html.="<tr>";
			}
			
			if(isset($this->tabValues[$i]))
			{
				if(isset($this->tabStyles[$i]) && $this->tabStyles[$i]!='')
					$html.="<td ".$this->tabStyles[$i].">".$this->tabValues[$i]."</td>";
				else
					$html.="<td ".$cellProperty.">".$this->tabValues[$i]."</td>";
			}
			else
			{
				$html.="<td ".$cellProperty.">&nbsp;</td>";
			}
			
			
			if($colonne==$nbColonnes-1)
			{
				$html.="</tr>";
				$colonne=0;
				$ligne+=1;
			}
			else
			{
				$colonne+=1;
			}
		}
		
		$html=$html."</tbody></table>";
		return $html;
	}
	
	// fonction permettant d'afficher un tableau de photo par exemple avec un commentaire qui sera aligné dans la ligne suivante du tableau
	public function addValuesFromArrayLinked($arrayValues,$nbColonnes=3,$styleCellHaut='',$styleCellBas='')
	{
		
		$nbValsTotal = count($arrayValues);

		$i=1;
		$tabTemp = array();
		$indice = 0;
		$nbCols= $nbColonnes;
		$ligneCourante = 1;
		
		for($s=0 ; $s<$nbValsTotal ; $s++)
		{
		
			$this->addValue($arrayValues[$s]['celluleHaut'],$styleCellHaut);
			
			$tabTemp[$indice] = $arrayValues[$s]['celluleBas'];
			
			
			if(($i%$nbColonnes==0 || $i==$nbValsTotal))
			{
			
				if(($nbColonnes-count($tabTemp))>0)
				{
					// il y a moins de photos que le max de la ligne
					$nbACompleter = $nbColonnes-count($tabTemp);
					for($iComplet=0 ; $iComplet < $nbACompleter ; $iComplet++)
					{
						$this->addValue('&nbsp;');
					}
				}
			
				for($j=0 ; $j<$nbColonnes ; $j++)
				{
					if(isset($tabTemp[$j]))
					{
						$this->addValue($tabTemp[$j],$styleCellBas);
					}
					else
					{
						$this->addValue('&nbsp;');
					}
				}
				$tabTemp=array();
				$indice=0;
			}
			else
			{
				$indice++;
			}
			
			$i++;
		}
	}
	
	// affiche un tableau a partir d'un resultat de requete mysql
	public function displayArrayFromMysqlRes($res)
	{
		$nbColonnes = 0;
		
		$i=0;
		while($fetch = mysql_fetch_assoc($res))
		{
			if($i==0)
			{
				foreach($fetch as $nomField => $value)
				{
					$this->addValue($nomField,"style='font-weight:bold;'");
					$nbColonnes++;
				}
			}
			
			foreach($fetch as $nomField => $value)
			{
				$this->addValue($value);
			}
			
			
			$i++;
		}
		
		return $this->createHtmlTableFromArray($nbColonnes);
		
		
	}
	
	// creation d'un tableau a base de divs , permettant ainsi d'avoir des cases aux formats différents
	// attention : si problemes avec ie , mettre un <div style='clear:left;'></div> a la fin dans le conteneur
	//
	// si nbColonnes est précisé dans les parametres, on met un div autour des div afin d'aligner les cellules
	public function createHtmlDivsFromArray($params=array())
	{
		$html="";
		

		$nbValues = count($this->tabValues);
		$styleDivs = "";
		if(isset($params['styleDivs']))
			$styleDivs = $params['styleDivs'];
		
		
		if(isset($params['nbColonnes']))
			$nbColonnes=$params['nbColonnes'];
		
		$widthLigne="";
		if(isset($params['largeurLigne']))
		{
			// peut eviter des problemes de rafraichissement a rajouter avec firefox
			$widthLigne = "width:".$params['largeurLigne']."px;";
		}
		
		for($i=0 ; $i<$nbValues	; $i++)
		{
			if(isset($nbColonnes) && $i==0)
			{
				$html.="<div style='float:left;display:table;$widthLigne'>";
			}
			elseif(isset($nbColonnes) && $i%$nbColonnes==0)
			{
				$html.="</div><div style='clear:both;'></div><div style='float:left;display:table;$widthLigne'>";
			}
			
			$width="";
			if(isset($this->tabProperties[$i]) && isset($this->tabProperties[$i]['x'])) // utile par exemple si on affiche des images , afin que la largeur du div s'adapte a la largeur des images
			{
				$width="width:".$this->tabProperties[$i]['x']."px;";
			}
			$html.="<div style='float:left;$width".$styleDivs."'>".$this->tabValues[$i]."</div>";
			
			if(isset($nbColonnes) && ($i==$nbValues-1))
			{
				$html.="</div>";
			}
		}
		
		return $html;
	}
	
}
?>