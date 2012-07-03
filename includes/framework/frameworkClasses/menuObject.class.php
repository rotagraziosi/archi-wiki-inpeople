<?php

// classe de gestion de pagination
// Dorer Laurent 2008

// historique des versions
// version 1.0 --- 


class menuObject extends config
{
	var $listeElems;
	var $identifiantUniqueMenu;
	
	// php5
	function __construct($identifiantUniqueMenu="0")
	{
		parent::__construct();
		$this->listeElems=array();
		$this->identifiantUniqueMenu = $identifiantUniqueMenu;
	}

	public function addElement($titre="",$contenu="",$onClick="",$htmlDiv="",$cssDiv="",$url="",$id="",$largeurColonne="")
	{
		if($id!='')
			$id = "menu_".$this->identifiantUniqueMenu."_".$id;
		$this->listeElems[count($this->listeElems)] = array("titre"=>$titre,"contenu"=>$contenu,"onClick"=>$onClick,"htmlDiv"=>$htmlDiv,"cssDiv"=>$cssDiv,"url"=>$url,"id"=>$id,"largeur"=>$largeurColonne);
	}
	
	public function getNbElements()
	{
		return count($this->listeElems);
	}
	
	public function getMenuRetractableSimple($params=array())
	{
		$retour = "";
		
		$retour .= "
			<script  >
				function frmWorkActionMenuOnClick(elementId)
				{
					if(document.getElementById(elementId).style.display=='block')
					{	document.getElementById(elementId).style.display = 'none';
					}
					else
					{
						document.getElementById(elementId).style.display = 'block';
					}
				}
			</script>
		";
		
		$i=0;
		foreach($this->listeElems as $indice => $value)
		{
			$identifiantElementMenu = $i;
			$retour.="<div>";
			$htmlJsCheckBox="";
			if(isset($params['menuAfficheCheckBox']))
			{
				$htmlJsCheckBox.="if(document.getElementById('frmWorkMenuDiv_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."').style.display=='block'){document.getElementById('frmWorkMenuChBox_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."').checked=true;}else{document.getElementById('frmWorkMenuChBox_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."').checked=false;}";
			}
			
			$retour.="<a style='cursor:pointer;' onclick=\"frmWorkActionMenuOnClick('frmWorkMenuDiv_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."');$htmlJsCheckBox".$value['onClick']."\">";
			
			
			if(isset($params['menuAfficheCroix']))
			{
				if(isset($params['menuCroixHtmlSpan']))
					$retour.="<span ".$params['menuCroixHtmlSpan'].">+</span>";
				else
					$retour.="<span>+</span>";
			}
			
			if(isset($params['menuAfficheCheckBox']))
			{
				$checkedDefault='';
				if(isset($params['indiceMenuOuvertParDefaut']) && $identifiantElementMenu==$params['indiceMenuOuvertParDefaut'])
					$checkedDefault='checked';
				
				$retour.="<input type='checkbox' id='frmWorkMenuChBox_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."' name='frmWorkMenuChBox_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."' $checkedDefault>";
			}
			
			
			$retour.="".$value['titre']."";
			$retour.="</a>";
			
			$displayStyle='none';
			if(isset($params['indiceMenuOuvertParDefaut']) && $identifiantElementMenu==$params['indiceMenuOuvertParDefaut'])
				$displayStyle='block';

			$retour.="<div id='frmWorkMenuDiv_".$this->identifiantUniqueMenu."_".$identifiantElementMenu."' ".$value['htmlDiv']." style='display:$displayStyle;".$value['cssDiv']."'>";
			$retour.="".$value['contenu']."";
			$retour.="</div>";
			$retour.="</div>";
			
			$i++;
		}
		
		return $retour;
	}
	
	
	// renvoi l'affichage d'un menu horizontale
	public function getMenuHTMLHorizontal($params=array())
	{
		$html="";
		
		$separateurMenu = "&nbsp;|&nbsp;";
		if(isset($params['separateurMenu']))
		{
			$separateurMenu = $params['separateurMenu'];
		}
		
		$nbElements = count($this->listeElems);
		$i=0;
		foreach($this->listeElems as $indice => $value)
		{
			$html.="<a href='".$value['url']."'>".$value['titre']."</a>";
			if($i+1 < $nbElements)
				$html.=$separateurMenu;
			$i++;
		}
		
		return $html;
	}
	
	
	// pour un bon fonctionnement, les elements de menu doivent avoir l'id renseigné, ainsi que la hauteur et la largeur des menus principaux , le contenu est un div ou une table pouvant contenir des sous menus
	public function getMenuHTMLHorizontalAvecSousMenusJS($params = array())
	{
		$html = "";
		
		
		$i=0;
		// pour chaque menu principal , on va creer un div que l'on place en fonction de la cellule du tableau ou on met le menu principal , ce code javascript devra etre executer a la fin de la page , donc on le renvoie dans un tableau a part
		$html = "<table cellspacing=0 cellpadding=0 border=0 id='table_".$this->identifiantUniqueMenu."'><tr>";
		$html2 = "";
		$js = "";
		$jsFermer="";
		$jsAfficher = "";
		$jsDivsFermerTout = "";
		$jsFermerTout = "";
		$positionX = 0; // position des menus par rapport a leur largeur (utile uniquement pour ie , sous ff on aurait pu se contenter de la fonction offsetLeft
		foreach($this->listeElems as $indice => $value)
		{
			if($indice>0)
				$positionX += $this->listeElems[$indice-1]['largeur'];
			
			$css = "";
			if(isset($value['cssDiv']) && $value['cssDiv']!='')
			{
				$css = $value['cssDiv'];
			}
			
			
			$mouseOver = "";
			if(isset($value['id']) && $value['id']!='')
			{
				$mouseOver = "fermeDivsMenus_".$this->identifiantUniqueMenu."('".$value['id']."');afficher_".$this->identifiantUniqueMenu."_".$value['id']."();";
			}
			
			$titre = $value['titre'];
			if($value['url']!='')
			{
				$titre = "<a href='".$value['url']."'>".$value['titre']."</a>";
			}
			$html.="<td style='margin:0;padding:0;$css' id='td_".$this->identifiantUniqueMenu."_".$value['id']."' width=".$value['largeur']." onMouseOver=\"".$mouseOver."\" valign=top>
			
			<table cellspacing=0 cellpadding=0 border=0 width=".$value['largeur']." height='100%'  style='margin:0;padding:0;$css'>
			<tr>
			<td width=6 style='background-image:url(".$this->urlImages."ri67/bordOngletHautGauche.gif);background-position:top left;background-repeat:no-repeat;'>
			</td>
			<td>			
			".$titre."
			</td>
			<td width=6 style='background-image:url(".$this->urlImages."ri67/bordOngletHautDroit.gif);background-position:top right;background-repeat:no-repeat;'>
			</td>
			</tr>
			</table></td>";
			if(isset($value['id']) && $value['id']!='')
			{
				$html2 .= "<div id='div_".$this->identifiantUniqueMenu."_".$value['id']."' style='position:absolute;display:none;' onmouseout='fermeTousMenus(this);'>".$value['contenu']."</div>";
				$jsFermer.="if(idDiv=='".$value['id']."'){";
				$jsFermer.=$this->getClosedDivsExceptCurrent($value['id']);
				$jsFermer.="}";
				$jsAfficher .="function afficher_".$this->identifiantUniqueMenu."_".$value['id']."()";
				$jsAfficher .= "{";//alert('ethop');
				$jsAfficher.=" document.getElementById('div_".$this->identifiantUniqueMenu."_".$value['id']."').style.display='block';";
				$jsAfficher.="}";
				
				$js.="document.getElementById('div_".$this->identifiantUniqueMenu."_".$value['id']."').style.top = parseInt(document.getElementById('table_".$this->identifiantUniqueMenu."').offsetTop+document.getElementById('table_".$this->identifiantUniqueMenu."').clientHeight)+'px';";
				
				$js.="vx = parseInt(document.getElementById('table_".$this->identifiantUniqueMenu."').offsetLeft+".$positionX.");";
				
				$js.="document.getElementById('div_".$this->identifiantUniqueMenu."_".$value['id']."').style.left = vx+'px';";
				$jsDivsFermerTout.="fermeDivsMenus_".$this->identifiantUniqueMenu."('".$value['id']."');";
				$i++;
			}
		}
		
		$html.="</tr></table>";
		
		$jsFermer="function fermeDivsMenus_".$this->identifiantUniqueMenu."(idDiv){".$jsFermer."}";

		//$jsFermer.="\nw = window.open();\n o = document.getElementById('table_".$this->identifiantUniqueMenu."');\n for(i in o){w.document.writeln(o[i]+' '+i+'<br>');}";
		
		$jsFermerTout = "function fermeTousMenus(p){var e=window.event||arguments.callee.caller.arguments[0]; if(zxcCkEventObj(e,p)){".$jsDivsFermerTout."} } ";
		$jsFermerTout.="function fermerTousMenusOnClickBody(){".$jsDivsFermerTout."}";
		
		// pour eviter l' 'event bubbling' => detects si la souris sort de l'element donné ou va seulement sur un sous element de l'element donné , exemple : un element = un div , un sous element = un <a href> a l'interieur de ce div
		$jsFermerTout .= "function zxcCkEventObj(e,p){
			 if (!e) var e=window.event;
			 e.cancelBubble=true;
			 if (e.stopPropagation) e.stopPropagation();
			 if (e.target) eobj=e.target;
			 else if (e.srcElement) eobj=e.srcElement;
			 if (eobj.nodeType==3) eobj=eobj.parentNode;
			 var eobj=(e.relatedTarget)?e.relatedTarget:(e.type=='mouseout')?e.toElement:e.fromElement;
			 if (!eobj||eobj==p) return false;
			 while (eobj.parentNode){
			  if (eobj==p) return false;
			  eobj=eobj.parentNode;
			 }
			 return true;
			}
		document.body.onclick=fermerTousMenusOnClickBody;
";
		
		
		
		return array('html'=>$html.$html2,'js'=>$jsAfficher.$jsFermer.$js.$jsFermerTout);
	}
	
	
	public function getClosedDivsExceptCurrent($currentId='')
	{
		$retour = "";
		foreach($this->listeElems as $indice=> $value)
		{
			if(isset($value['id']) && $value['id']!='' && $value['id']!=$currentId)
			{
				$retour.="document.getElementById('div_".$this->identifiantUniqueMenu."_".$value['id']."').style.display='none';";
			}
		}
		return $retour;
	}

}

?>