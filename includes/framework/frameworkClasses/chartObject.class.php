<?php

// classe de gestion de graphiques
// Dorer Laurent 2009

// historique des versions
// version 1.0 --- 
// version 1.1 --- multigraphe (plusieurs valeurs sur une meme barre

class chartObject extends config
{
	var $listeValues;
	var $listeLibelles;
	var $listeStyles;
	var $listeModesAffichage;
	// php5
	function __construct()
	{
		$this->listeValues = array();
		$this->listeLibelles = array();
		$this->listeStyles = array();
		$this->listeModesAffichage = array();
	}

	
	public function addValue($params = array())
	{
		if(isset($params['value']))
		{
			$this->listeValues[count($this->listeValues)] = $params['value'];
		}
		else
		{
			$this->listeValues[count($this->listeValues)] = 0;
		}
		
		if(isset($params['libelle']))
		{
			$this->listeLibelles[count($this->listeLibelles)] = $params['libelle'];
		}
		else
		{
			$this->listeLibelles[count($this->listeLibelles)] ='';
		}
		
		if(isset($params['style']))
		{
			$this->listeStyles[count($this->listeStyles)] = $params['style'];
		}
		else
		{
			$this->listeStyles[count($this->listeStyles)] = "background-color:red;";
		}
		
		if(isset($params['modeAffichage']))
		{
			$this->listeModesAffichage[count($this->listeModesAffichage)] = $params['modeAffichage'];
		}
		else
		{
			$this->listeModesAffichage[count($this->listeModesAffichage)] = "";
		}
	}
	
	
	// construction d'un graphique en html avec des tables
	public function getHtml($params = array())
	{
		$html="";
		$maxValue = $this->getMaxValue(); // cette valeur va permettre de calculer l'echelle la plus pratique a utiliser
		
		$nbChiffres = pia_strlen($maxValue);
		
		$unite='1';
		for($i=0 ; $i<$nbChiffres-2 ; $i++)
		{
			$unite.='0';
		}
		
		$roundedValue = round($maxValue,-$nbChiffres+2)+$unite;
		
		
		$hauteur = 200;
		if(isset($params['hauteur']) && $params['hauteur']!='')
		{
			$hauteur = $params['hauteur'];
		}
		
		
		$colWidth=20;
		if(isset($params['colWidth']) && $params['colWidth']!='')
		{
			$colWidth=$params['colWidth'];
		}
		
		// echelle
		$heightEchelle = $hauteur;
		
		$html.="<table cellspacing=0 cellpadding=0 border=0 height=".($heightEchelle+50+50)."><tr><td valign=bottom>";
		$html.="<table border=0 cellspacing=0 cellpadding=0 style='background-color:white;' height=".($heightEchelle+50).">";
		$html.="<tr>";
		$html.="<td valign=top>";
		$html.=" <table height=$heightEchelle width=60 border=0 cellspacing=0 cellpadding=0 style=''><tr><td width=60 valign=bottom>";
		$html.="<div style='position:relative;'>";
		$html.="<div style='position:absolute;bottom:".round($heightEchelle/4)."px;left:0;width:60px;text-align:right;'>".round($roundedValue/4)."_</div>";
		$html.="<div style='position:absolute;bottom:".round($heightEchelle/2)."px;left:0;width:60px;text-align:right;'>".round($roundedValue/2)."_</div>";
		$html.="<div style='position:absolute;bottom:".round($heightEchelle/(4/3))."px;left:0;width:60px;text-align:right;'>".round($roundedValue/(4/3))."_</div>";
		$html.="<div style='position:absolute;bottom:0px;left:0;width:60px;text-align:right;'>0_</div>";
		$html.="<div style='position:absolute;bottom:".$heightEchelle."px;left:0;width:60px;text-align:right;'>".$roundedValue."_</div>";
		$html.="</div>";
		$html.="</td><td style='background-color:black;' width=2></td></tr></table>";
		$html.="</td>";
		
		foreach($this->listeValues as $indice => $value)
		{
			if(is_array($value))
			{

				if(!isset($this->listeModesAffichage[$indice]) || $this->listeModesAffichage[$indice]!='cumul')
				{
					asort($value);
				}
		
				$html.="<td valign=bottom height=200>";
				
				$html.="<table cellspacing=0 cellpadding=0 border=0  width=$colWidth style=''>";
				$valPrecedente = 0;
				$arrayHtmlGraph = array();
				foreach($value as $index => $values)
				{
					if(isset($this->listeModesAffichage[$indice]) && $this->listeModesAffichage[$indice]=='cumul')
					{
						// dans ce cas on cumul les barres , on affiche la premiere puis la seconde au dessus
						$height1 = round($value[$index]*$hauteur/$roundedValue);
						$arrayHtmlGraph[] = "<tr><td style='".$this->listeStyles[$indice][$index]."' height=".$height1."></td></tr>";
					}
					else
					{
						// dans ce cas on affiche les barres les une derriere les autres , et ont chacune comme ordonnee 0 au depart
						$height1 = round((($value[$index] - $valPrecedente) * $hauteur) / $roundedValue);
						$arrayHtmlGraph[] = "<tr><td style='".$this->listeStyles[$indice][$index]."' height=".$height1."></td></tr>";
						$valPrecedente = $value[$index] - $valPrecedente;
					}
				}
				
				// et on affiche a l'envers
				for($i=count($arrayHtmlGraph)-1 ; $i>=0 ; $i--)
				{
					$html.=$arrayHtmlGraph[$i];
				}
				
				$html.="<tr height=50><td style='background-color:white;border-top:1px solid black;font-size:9px;' valign=top>".$this->listeLibelles[$indice]."</td></tr>";//
				$html.="</table>";
				$html.="</td>";
			}
			else
			{
				$html.="<td valign=bottom height=200>";
				$height = round($value * $hauteur / $roundedValue);
				$html.="<table cellspacing=0 cellpadding=0 border=0 height=".($height+50)." width=$colWidth style='".$this->listeStyles[$indice]."'>";
				$html.="<tr>";
				$html.="<td valign=top></td>";
				$html.="</tr>";
				$html.="<tr height=50><td style='background-color:white;border-top:1px solid black;font-size:9px;' valign=top>".$this->listeLibelles[$indice]."</td></tr>";//
				$html.="</table>";
				$html.="</td>";
			}
		}
		
		$html.="</tr>";
		$html.="</table>";
		$html.="</tr>";
		$html.="</table>";
		
		
		return $html;
	}
	
	// renvoi la valeur maximal parmis la liste de valeur
	public function getMaxValue()
	{
		$retour = 0;
		foreach($this->listeValues as $indice => $value)
		{
			if(is_array($value))
			{
				for($i=0 ; $i<count($value) ; $i++)
				{
					if($value[$i]>$retour)
						$retour = $value[$i];
				}
			}
			else
			{
				if($value>$retour)
					$retour = $value;
			}
		}
		
		return $retour;
	}
	
	
	
}

?>