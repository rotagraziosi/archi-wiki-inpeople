<?php
// classe de gestion des calques
// Dorer Laurent 2008

// historique des versions
// version 1.0 --- 08/10/2008 -

// objet permettant de manipuler la session
class calqueObject extends config
{
		private $idPopup;
		private $urlImageIllustration;
		
		function __construct($params=array())
		{
			parent::__construct();
					
			if(isset($params['idPopup']))
			{
				$this->idPopup=$params['idPopup'];
			}
			else
			{
				$this->idPopup='popupDefaultId';
			}
			
			if(isset($params['urlImageAideIllustration']))
			{
				$this->urlImageIllustration = $params['urlImageAideIllustration'];
			}
			else
			{
				$this->urlImageIllustration ="images/aide.jpg";
			}
		}

		// a placer en fin de page
		function getJSFunctionContextualHelp()
		{
			return $this->getJSScrollHeight()."

					// affiche l'aide contextuelle
					function getContextHelp(msg)
					{
						if(msg)
						{
							document.getElementById('helpCalque').style.display='block';
							document.getElementById('helpCalque').style.position='absolute';
							document.getElementById('helpCalque').style.top=mouseY+15+'px';
							document.getElementById('helpCalque').style.left=mouseX+50+'px';
							document.getElementById('helpCalqueTxt').innerHTML=msg;
						}
					}

					function closeContextHelp()
					{
						document.getElementById('helpCalque').style.display='none';
					}

					// --- RECUPERATION DES COORDONNEES DE LA SOURIS ---
					var mouseX;
					var mouseY;

					function sourisxy(e)
					{	
						x = (navigator.appName==\"Netscape\") ? e.pageX : event.x + document.body.scrollLeft;
						y = (navigator.appName==\"Netscape\") ? e.pageY : event.y + document.body.scrollTop;
						mouseX = x;
						mouseY = y;
					}

					if(navigator.appName.substring(0,3) == \"Net\")
					{
						document.captureEvents(Event.mousemove);
					}

					document.onmousemove = sourisxy;
					";
		}
		
		// renvoi le div a placer sur la page. Ce div est le calque qui s'affiche lors de l'appelle de la fonction js getContextHelp
		// a placer en fin de page
		public function getHtmlDivContextualHelp()
		{
			return "<div id='helpCalque' style='background-color:#FFFFFF; border:2px solid #000000;padding:10px;float:left;display:none;'><img src='".$this->urlImageIllustration."' style=\"float:left;padding-right:3px;\" valign=\"middle\"><div id='helpCalqueTxt' style='padding-top:7px;'></div></div>";
		}
		
		// retour de fonction a placer dans le onMouseOver de l'element sur lequel on desire une aide contextuelle
		public function getJsContextHelpOnMouseOver($contenu='')
		{
			$contenu = str_replace('"','&#34;',$contenu);
			$contenu = str_replace('\'','&#146;',$contenu);
			return "getContextHelp('".$contenu."');";
		}
		
		// retour de fonction a placer dans le onMouseOut de l'element sur lequel on desire pouvoir fermer le calque d'aide
		public function getJSContextHelpOnMouseOut()
		{
			return "closeContextHelp();";
		}
		
		
		
		// renvoi une "popup" qui ne peut pas etre bougé et dont le fond couvre toute la page internet , et transparent
		public function getDivNoDraggableWithBackgroundOpacity($params = array())
		{
			$width = 500;
			if(isset($params['width']) && $params['width']!='')
			{
				$width = $params['width'];
			}

			$height = 500;
			if(isset($params['height']) && $params['height']!='')
			{
				$height = $params['height'];
			}
		
			$top = 50;
			if(isset($params['top']) && $params['top']!='')
			{
				$top = $params['top'];
			}
			
			$left = 100;
			if(isset($params['left']) && $params['left']!='')
			{
				$left = $params['left'];
			}
		
			$retour = "";
			
			$retour.= "<div id='backgroundDiv".$this->idPopup."' style='z-index:10000;display:none;background-color:black;opacity:0.50;position:absolute;top:0px;left:0px;height:5000px;width:5000px;filter:alpha(opacity=50);' onclick=\"document.getElementById('backgroundDiv".$this->idPopup."').style.display='none';document.getElementById('popupDiv".$this->idPopup."').style.display='none';\">";
			$retour.="</div>";
			
			
			$retour.="<div id='popupDiv".$this->idPopup."' style='z-index:10001;display:none;position:absolute;background-color:white;top:".$top."px;left:".$left."px;height:".$height."px;width:".$width."px;padding:0px;margin:0px;'>".$params['contenu']."</div>";
			
			
			return $retour;
		}
		
		public function getJsOpenPopupNoDraggableWithBackgroundOpacity()
		{
			return "document.getElementById('backgroundDiv".$this->idPopup."').style.display='block';document.getElementById('popupDiv".$this->idPopup."').style.display='block';";
		}
		
		public function getJsClosePopupNoDraggableWithBackgroundOpacity()
		{
			return "document.getElementById('backgroundDiv".$this->idPopup."').style.display='none';document.getElementById('popupDiv".$this->idPopup."').style.display='none';";
		}
		

		// renvoi le code HTML de la popup en calque
		public function getDiv($params=array())
		{
		
			$t=new Template($this->cheminTemplates);
			$t->set_filenames((array('popup'=>'popupGeneric.tpl')));
		
		
			$width = 500;
			if(isset($params['width']) && $params['width']!='')
			{
				$width = $params['width'];
			}
			
			$height = 500;
			if(isset($params['height']) && $params['height']!='')
			{
				$height = $params['height'];
			}
			
			$left = 100;
			if(isset($params['left']) && $params['left']!='')
			{
				$left = $params['left'];
			}
			
			$top = 50;
			if(isset($params['top']) && $params['top']!='')
			{
				$top = $params['top'];
			}
			
			$titrePopup="";
			if(isset($params['titre']) && $params['titre']!='')
			{
				$titrePopup = $params['titre'];
			}
			
			$codeJsFermer="";
			if(isset($params['codeJsFermerButton']) && $params['codeJsFermerButton']!='')
			{
				$codeJsFermer = $params['codeJsFermerButton'];
			}
			
			
			
			$hiddenFields="";
			if(isset($params['hiddenFields']))
			{
				foreach($params['hiddenFields'] as $indice => $value)
				{
					$hiddenFields .= "<input type='hidden' id='".$indice."' name='".$indice."' value='".$value."'>";
				}
			}
			
			$t->assign_vars(array(
					'width'=>$width,
					'height'=>$height,
					'left'=>$left,
					'top'=> $top,
					'hiddenFields'=>$hiddenFields,
					'divIdPopup'=>'div'.$this->idPopup,
					'tdIdPopup'=>'td'.$this->idPopup,
					'iFrameIdPopup'=>'iFrame'.$this->idPopup,
					'lienSrcIFrame'=>$params['lienSrcIFrame'],
					'titrePopup'=>$titrePopup,
					'codeJsFermer'=>$codeJsFermer
			));
		
		
			ob_start();
			$t->pparse('popup');
			$html=ob_get_contents();
			ob_end_clean();
		
		
			return $html;
		}
		
		function getJSScrollHeight()
		{
			return "
			function getScrollHeight()
			{
			   var hauteur = (navigator.appName == 'Microsoft Internet Explorer') ? document.body.scrollTop : window.pageYOffset;
			   return hauteur;
			}";
		}
		
		
		function getJSOpenPopup($idRetour=0)
		{
			$html="";
			$html.="document.getElementById('div".$this->idPopup."').style.display='block';document.getElementById('identifiantRetour').value='".$idRetour."';";
			return $html;
		}
		
		function getJSClosePopup()
		{
			$html="";
			$html.="document.getElementById('div".$this->idPopup."').style.display='none';";
			return $html;
		}
		
		function getJSIFrameId()
		{
			return "iFrame".$this->idPopup;
		}
		
		function getJSDivId()
		{
			return "div".$this->idPopup;
		}
		
		// ************************************************************************************************************************************************************************
		// affichage d'une popup d'attente de chargement de page
		public function getPopupAttente($popupID='popupAttente',$contenu="Veuillez patienter, chargement...",$styleDiv='padding-top:70px;')
		{
			$divAttente="";
			$divAttente.="<div id='$popupID' style='width:400px;height:150px;top:200px;left:180px;background-color:white;position:absolute;display:none;text-align:center;border:1px solid #000000;'>";
			//$divAttente.="<iframe style='width:400px;border:0px;' src='".$this->creerUrl('','affichePopupAttente',array('noHeaderNoFooter'=>1))."'></iframe>";
			$divAttente.="<div style='$styleDiv'>$contenu</div>";
			$divAttente.="</div>";
			
			
			return $divAttente;
		}
		// code javascript pour l'appel de la popup d'attente
		public function getJSOpenPopupAttente($popupID='popupAttente')
		{
			return "document.getElementById('$popupID').style.display='block';";
			
		}
		// ************************************************************************************************************************************************************************
		
		
		
		// rendre un calque deplacable avec la souris
		// a placer a la fin du code de la page, permet de deplace la fenetre créée par l'objet
		public function getJsToDragADiv()
		{
			$html="";
			$html.="
				var ie".$this->idPopup." = (document.all)? true:false;
				var ns4".$this->idPopup." = (document.layers)? true:false;
				var ns6".$this->idPopup." = (document.getElementById)? true:false;
			";
			
			// fonction start a appeler en premier dans le onload par exemple
			$html.="
			
			function start() {

			if (ie".$this->idPopup.") {
            // lance gereEvenements quand on appuie sur le bouton de la souris
            document.getElementById('".$this->getJSDivId()."').onmousedown= gereEvenements".$this->idPopup."; }else if (ns4".$this->idPopup.") {
            // lance gereEvenements quand on appuie sur le bouton de la souris
            document.captureEvents(Event.MOUSEDOWN);
            document.onmousedown=gereEvenements; }else if (ns6".$this->idPopup.") {
            // lance gereEvenements quand on appuie sur le bouton de la souris
            document.getElementById('".$this->getJSDivId()."').addEventListener('mousedown',gereEvenements".$this->idPopup.", false); }

			}
			
			
			function gereEvenements".$this->idPopup."(e) {

			if (ie".$this->idPopup.") {
            //Récupération de la position de la souris
            window.lastX=event.clientX;
            window.lastY=event.clientY;
            // lance doDrag tant que l'on appuie sur le bouton de la souris en la bougeant
            document.onmousemove=doDrag".$this->idPopup.";
            // lance endDrag quand on relache le bouton de la souris
            document.onmouseup=endDrag".$this->idPopup."; }else if (ns4".$this->idPopup.") {
            //Récupération de la position de la souris
            window.lastX=e.pageX;
            window.lastY=e.pageY;
            // lance doDrag tant que l'on appuie sur le bouton de la souris en la bougeant
            document.captureEvents(Event.MOUSEMOVE)
            document.onmousemove=doDrag".$this->idPopup.";
            // lance endDrag quand on relache le bouton de la souris
            document.captureEvents(Event.MOUSEUP)
            document.onmouseup=endDrag".$this->idPopup."; }else if (ns6".$this->idPopup.") {
            //Récupération de la position de la souris
            window.lastX=e.clientX;
            window.lastY=e.clientY;
            // lance doDrag tant que l'on appuie sur le bouton de la souris en la bougeant
            window.onmousemove=doDrag".$this->idPopup.";
            // lance endDrag quand on relache le bouton de la souris
            window.onmouseup=endDrag".$this->idPopup."; }

			}
			
			function doDrag".$this->idPopup."(e) {
				
			if (ie".$this->idPopup.") {
            // Calcul de l'écart de position de la souris
            var difX=event.clientX-window.lastX;
            var difY=event.clientY-window.lastY;
            //Récupération de la position du div et ajout de l'écart de position de la souris
            var newX1 = parseInt(".$this->getJSDivId().".style.left)+difX;
            var newY1 = parseInt(".$this->getJSDivId().".style.top)+difY;
            // Assignation des nouvelles coordonnées au div
            ".$this->getJSDivId().".style.left=newX1+'px';
            ".$this->getJSDivId().".style.top=newY1+'px';
            //Assignation de l'anciènne position de la souris
            window.lastX=event.clientX;
            window.lastY=event.clientY; }else if (ns4".$this->idPopup.") {
            // Calcul de l'écart de position de la souris
            var difX=e.pageX-window.lastX;
            var difY=e.pageY-window.lastY;
            //Récupération de la position du div et ajout de l'écart de position de la souris
            var newX1 = parseInt(document.layers.".$this->getJSDivId().".left)+difX;
            var newY1 = parseInt(document.layers.".$this->getJSDivId().".top)+difY;
            // Assignation des nouvelles coordonnées au div
            document.layers.".$this->getJSDivId().".left=newX1;
            document.layers.".$this->getJSDivId().".top=newY1;
            //Assignation de l'anciènne position de la souris
            window.lastX=e.pageX;
            window.lastY=e.pageY; }else if (ns6".$this->idPopup.") {
            // Calcul de l'écart de position de la souris
            var difX=e.clientX-window.lastX;
            var difY=e.clientY-window.lastY;
            //Récupération de la position du div et ajout de l'écart de position de la souris
            var newX1 = parseInt(document.getElementById('".$this->getJSDivId()."').style.left)+difX;
            var newY1 = parseInt(document.getElementById('".$this->getJSDivId()."').style.top)+difY;
            // Assignation des nouvelles coordonnées au div
            document.getElementById('".$this->getJSDivId()."').style.left=newX1+'px';
            document.getElementById('".$this->getJSDivId()."').style.top=newY1+'px';
            //Assignation de l'anciènne position de la souris
            window.lastX=e.clientX;
            window.lastY=e.clientY; } 

			}
			
			
			function endDrag".$this->idPopup."(e) {

		    if (ie".$this->idPopup." || ns4".$this->idPopup.") {
				//Réinitialisation du onmousemove
				document.onmousemove=null; }else if (ns6".$this->idPopup.") {
				//Réinitialisation du onmousemove
				window.onmousemove=null; }

			}
			
			
			start();
			
			";
			return $html;
		
		}
		
		// ***************************************************************************************************************************************
		// utilisation : donner comme id au div a scroller : DIV_MOVE
		// rentrer en parametre les limites de scroll dans la page
		// la condition (conditionsToScrollBegin) est du type :
		// if((document.getElementById('texteSuite').style.display=='block' && y_<4731) || (document.getElementById('texteSuite').style.display=='none' && y_<175))
		// ici si un div de la page est afficher , on va scoller jusqua 4731 , sinon si le div est fermé on scroll jusqu'a 175
		// ***************************************************************************************************************************************
		public function getJsScrollDivWithPage($params = array())
		{
			$html="";
			$identifiantDivToScroll = "DIV_MOVE";
			if(isset($params['identifiantDivToScroll']) && $params['identifiantDivToScroll']!='')
			{
				$identifiantDivToScroll = $params['identifiantDivToScroll'];
			}
			
			$conditionsToScrollBegin="";
			$conditionsToScrollEnd="";
			if(isset($params['conditionsToScrollBegin']) && $params['conditionsToScrollBegin']!='')
			{
				$conditionsToScrollBegin=$params['conditionsToScrollBegin']."{"; // les accolades de la condition sont ajoutées automatiquement
				$conditionsToScrollEnd="}";
			}
			

			$baliseJsDebut="<script  >";
			$baliseJsFin="</script>";
			if(isset($params['withoutJsBalises']) && $params['withoutJsBalises']==true)
			{
				$baliseJsDebut="";
				$baliseJsFin="";
			}
			
		
			$html.="$baliseJsDebut
		     //---------------------------------------------------------
		     // Nom Document : gf_scroll_div.js
		     // Auteur : G.Ferraz
		     // Objet : menu flottant
		     // Creation : 01.01.2007
		     //---------------------------------------------------------
		     // Mise à Jour : 01.11.2007
		     //---------------------------------------------------------
		     // OUTILS /////////////////////////////
		     //---------------------------------------------
		     function Add_Event( obj_, event_, func_, mode_){
		     if( obj_.addEventListener)
		     obj_.addEventListener( event_, func_, mode_? mode_:false);
		     else
		     obj_.attachEvent( 'on'+event_, func_);
		     }
		     //----------------------
		     function GetScrollPage(){
		     var Left;
		     var Top;
		     var DocRef;
		    
		     if( window.innerWidth){
		     with( window){
		     Left = pageXOffset;
		     Top = pageYOffset;
		     }
		     }
		     else{ // Cas Explorer a part
		     if( document.documentElement && document.documentElement.clientWidth)
		     DocRef = document.documentElement;
		     else
		     DocRef = document.body;
		    
		     with( DocRef){
		     Left = scrollLeft;
		     Top = scrollTop;
		     }
		     }
		     return({top:Top, left:Left});
		     }
		     //---------------------------
		     function ObjGetPosition(obj_){
		     var PosX = 0;
		     var PosY = 0;
		     //-- suivant type en parametre
		     if( typeof(obj_)=='object')
		     var Obj = obj_;
		     else
		     var Obj = document.getElementById( obj_);
		     //-- Si l'objet existe
		     if( Obj){
		     //-- Recup. Position Objet
		     PosX = Obj.offsetLeft;
		     PosY = Obj.offsetTop;
		     //-- Si propriete existe
		     if( Obj.offsetParent){
		     //-- Tant qu'un parent existe
		     while( Obj = Obj.offsetParent){
		     if( Obj.offsetParent){ // on ne prend pas le BODY
		     //-- Ajout position Parent
		     PosX += Obj.offsetLeft;
		     PosY += Obj.offsetTop;
		     }
		     }
		     }
		     }
		     //-- Retour des positions
		     return({left:PosX, top:PosY});
		     }
		     //-------------------------------------
		     // MENU FLOTTANT //////////////////////
		     //-------------------------------------
		     var IdTimer_1;
		     var IdTimer_2;
		     var O_DivScroll;
		     var Rapport = 1.0/20.0; // On divise par 20
		     var Mini = 2* Rapport;
		     //-----------------------
		     function DIV_Scroll( id_){
		     var Obj = document.getElementById( id_);
		     this.Obj = Obj;
		     if( Obj){
		     Obj.style.position = 'absolute'; // IMPERATIF
		     //-- Recup position de depart
		     var Pos = ObjGetPosition( id_);
		     this.PosX = Pos.left;
		     this.PosY = Pos.top;
		     this.DebX = this.PosX;
		     this.DebY = this.PosY;
		     this.NewX = 0;
		     this.NewY = 0;
		     this.Move = DIV_Deplace;
		     }
		     }
		     //---------------------------
		     function DIV_Deplace( x_, y_){
		     if( arguments[0] != null){
		     this.PosX = x_;
		     this.Obj.style.left = parseInt(x_) +'px';
		     }
		     if( arguments[1] != null){
				$conditionsToScrollBegin
				this.PosY = y_;
				this.Obj.style.top = parseInt(y_) +'px';
				$conditionsToScrollEnd
		     }
		     }
		     //---------------------------
		     function DIV_Replace( x_, y_){
		     //-- Calcul Delta deplacement
		     var Delta_X = (x_ -O_DivScroll.PosX) *Rapport;
		     var Delta_Y = (y_ -O_DivScroll.PosY) *Rapport;
		     //-- Test si fin deplacement
		     if((( Delta_Y < Mini)&&( Delta_Y > -Mini))&&
		     (( Delta_X < Mini)&&( Delta_X > -Mini))){
		     clearInterval( IdTimer_1);
		     O_DivScroll.Move( x_, y_);
		     }
		     else{
		     O_DivScroll.Move( O_DivScroll.PosX +Delta_X, O_DivScroll.PosY +Delta_Y);
		     }
		     }
		     //------------------------
		     function DIV_CheckScroll(){
		     var Scroll = GetScrollPage();
		     //-- New position du menu
		     O_DivScroll.NewX = Scroll.left +O_DivScroll.DebX;
		     O_DivScroll.NewY = Scroll.top +O_DivScroll.DebY;
		     //-- Si pas la bonne Position
		     if(( O_DivScroll.PosY != O_DivScroll.NewY)||( O_DivScroll.PosX != O_DivScroll.NewX)){
		     //-- Clear l'encours
		     clearInterval( IdTimer_1);
		     IdTimer_1 = setInterval(\"DIV_Replace(\" + O_DivScroll.NewX +\",\" + O_DivScroll.NewY +\")\", 10);
		     }
		     return( true);
		     }
		     //-----------------------
		     function DIV_InitScroll(){
		     //-- Recup position Objet
		     O_DivScroll = new DIV_Scroll('$identifiantDivToScroll');
		     //-- Lance inspection si existe
		     if( O_DivScroll.Obj)
		     IdTimer_2 = setInterval('DIV_CheckScroll()',100);
		     }
		     //========================================
		     Add_Event( window, 'load', DIV_InitScroll);
		     //-- EOF --
			$baliseJsFin
			";
			
			return $html;
		}
		
}
?>
