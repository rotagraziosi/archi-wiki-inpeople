<?php
/**
 * Classe GoogleMap
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
 * Classe pour gérer l'affichage des cartes
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
 * */
class GoogleMap extends config
{
    var $googleMapNameId;
    var $googleMapKeyProperty;
    var $googleMapWidth;
    var $googleMapHeight;
    var $coordonnees;
    var $markerOnClickType;
    var $setTimeOutPaquets;
    var $debugMode;
    var $googleMapZoom;
    var $divStyle;
    var $mapType;
    
    var $centerLong;
    var $centerLat;
    
    
    var $noDisplayZoomSelectionSquare;
    var $zoomType;
    var $noDisplayEchelle;
    var $noDisplayMapTypeButtons;
    var $noDisplayMiniZoom;
    
    /**
     * Constructeur de la classe GoogleMap
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    function __construct($params=array())
    {
        $this->noDisplayZoomSelectionSquare=false;
        $this->noDisplayZoomSlider=false;
        $this->zoomType = '';
        $this->noDisplayEchelle=false;
        $this->noDisplayMapTypeButtons=false;
                
        parent::__construct();
        if (isset($params['googleMapNameId']) && $params['googleMapNameId']!='') {
            $this->googleMapNameId = $params['googleMapNameId'];
        } else {
            $this->googleMapNameId='divMap';
        }
        
        if (isset($params['mapType']) && $params['mapType']!='') {
            $this->mapType=$params['mapType'];
        } else {
            $this->mapType='';
        }
        
        
        if (isset($params['height']) && $params['height']!='') {
            $this->googleMapHeight = $params['height'];
        } else {
            $this->googleMapHeight = '300';
        }
        
        if (isset($params['divStyle']) && $params['divStyle']!='') {
            $this->divStyle=$params['divStyle'];
        } else {
            $this->divStyle='';
        }
        
        if (isset($params['width']) && $params['width']!='') {
            $this->googleMapWidth = $params['width'];
        } else {
            $this->googleMapWidth = '500';
        }
        
        if (isset($params['setOnClickType']) && $params['setOnClickType']!='') {
            $this->markerOnClickType = $params['setOnClickType'];
        } else {
            $this->markerOnClickType = 'link';
        }
        
        if (isset($params['setTimeOutPaquets']) && $params['setTimeOutPaquets']!='') {
            $this->setTimeOutPaquets = $params['setTimeOutPaquets'];
        } else {
            $this->setTimeOutPaquets = 5000;
        }
        
        if (isset($params['debugMode']) && $params['debugMode']==true) {
            $this->debugMode = true;
        } else {
            $this->debugMode = false;
        }
        
        if (isset($params['zoom']) && $params['zoom']!='') {
            $this->googleMapZoom = $params['zoom'];
        } else {
            $this->googleMapZoom = 10;
        }
        
        if (isset($params['noDisplayZoomSelectionSquare']) && $params['noDisplayZoomSelectionSquare']==true) {
            $this->noDisplayZoomSelectionSquare=true;
        }
        
        if (isset($params['noDisplayZoomSlider']) && $params['noDisplayZoomSlider']==true) {
            $this->noDisplayZoomSlider=true;
        }
        
        if (isset($params['noDisplayEchelle']) && $params['noDisplayEchelle']==true) {
            $this->noDisplayEchelle=true;
        }
                
        if (isset($params['noDisplayMapTypeButtons']) && $params['noDisplayMapTypeButtons']==true) {
            $this->noDisplayMapTypeButtons=true;
        }
        
        if (isset($params['zoomType']) && $params['zoomType']!='') {
            $this->zoomType=$params['zoomType'];
        }
        
        
        if (isset($params['centerLong']) && isset($params['centerLat'])) {
            $this->centerLong = $params['centerLong'];
            $this->centerLat = $params['centerLat'];
        } else {
            $this->centerLong = "7.7400"; // on centre sur strasbourg par defaut
            $this->centerLat = "48.585000";
        }
                
        $this->googleMapKeyProperty = $params['googleMapKey'];
        $this->coordonnees = array();
    }
    
    /**
     * Ajouter une adresse ?
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    function addAdresse($params=array())
    {
        $index = count($this->coordonnees);
        
        if (isset($params['adresse']) && $params['adresse']!='') {
            $this->coordonnees[$index]['adresse'] = $params['adresse'];
        } else {
            $this->coordonnees[$index]['adresse'] = '';
        }
        
        if (isset($params['link']) && $params['link']!='') {
            $this->coordonnees[$index]['link'] = $params['link'];
        } else {
            $this->coordonnees[$index]['link']='';
        }
        
        if (isset($params['imageFlag']) && $params['imageFlag']!='') {
            $this->coordonnees[$index]['imageFlag']=$params['imageFlag'];
        } else {
            $this->coordonnees[$index]['imageFlag']='';
        }
        
        if (isset($params['longitude']) && $params['longitude']!='' && isset($params['latitude']) && $params['latitude']!='') {
            $this->coordonnees[$index]['longitude'] = $params['longitude'];
            $this->coordonnees[$index]['latitude'] = $params['latitude'];
        }
        
        if (isset($params['setImageWidth'])) {
            $this->coordonnees[$index]['imageWidth'] = $params['setImageWidth'];
        }
        
        if (isset($params['setImageHeight'])) {
            $this->coordonnees[$index]['imageHeight'] = $params['setImageHeight'];
        }
        
        if (isset($params['pathToImageFlag']) && $params['pathToImageFlag']!='') {
            $this->coordonnees[$index]['pathToImageFlag']=$params['pathToImageFlag'];
        } else {
            $this->coordonnees[$index]['pathToImageFlag']='';
        }
    }
    
    /**
     * ?
     * 
     * @return string HTML
     * */
    public function getHtmlFromAdresses()
    {   
        $html="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px;'>Veuilliez patienter pendant le chargement de la carte...</div>";
    
        $html.="<script  >";

        if (count($this->coordonnees)>0) {
            foreach ($this->coordonnees as $indice => $value) {
                if (isset($value['link'])) {
                    $html.="tabAdresses[".$indice."]=\"".$value['link']."\";\n";
                }
            }
        }
        
        $html.="</script>";
        //$html.="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px; background-color:lime;'>Veuilliez patienter pendant le chargement de la carte...</div>";
        
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
            
        //$html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        $html.="<script>load();</script>";
        
        // fonction appelant les affichages de coordonnées ,  appels regroupées dans une fonction qui groupe les coordonnées par paquet ,  afin de ne pas trop en envoyer a la fois
        if (count($this->coordonnees)>0) {
            $html.="<script>";
            $html.="var numPaquet=0;\n";
            $html.="var timer;\n";
            $html.="startTimerPaquets();\n";
            $html.="function startTimerPaquets()\n";
            $html.="{";
            $html.="afficheCoordonneesParPaquets();\n";
            $html.="timer = setInterval(\"afficheCoordonneesParPaquets()\", ".$this->setTimeOutPaquets.");\n";
            $html.="}\n";
            
            $html.="function afficheCoordonneesParPaquets(){\n";
            $i=0;
            $numPaquet = 0;
            foreach ($this->coordonnees as $indice => $value) {
                $image = ", \"https://www.google.com/mapfiles/marker.png\"";//
                if (isset($value['imageFlag']) && $value['imageFlag']!='') {
                    $image = ", \"".$value['imageFlag']."\"";
                }
                
                if ($i%10==0) {
                    $html.="if (numPaquet==".$numPaquet.")\n";
                    $html.="{\n";
                    $iDebut = $i;
                }
                
                $html.=" getCoordonnees(\"".$value['adresse']."\", ".$indice." ".$image.");\n";
                
                if ($i==$iDebut+9 || $i==count($this->coordonnees)-1 ) {
                    $html.="}\n";
                    $numPaquet++;
                }
                $i++;
            }
            

            $html.="if (numPaquet>".$numPaquet.")\n";
            $html.="{\n";
            $html.="clearInterval(timer);\n";
            $html.="}\n";
            $html.="numPaquet++;\n";
            $html.="}\n";
            $html.="</script>";
        }
        

        
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
        
        $html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        return $html;
    }
    
    
    
    /**
     * Même fonction que la precedente ,  mais celle ci fonctionne a partir des coordonnees geographiques plutot que l'adresse
     * Récuperation des coordonnées par une boucle sur la fonction addAdresse
     * 
     * @return string HTML
     * */
    public function getHtmlFromAdressesNoPauseWithGeoLocalization()
    {   
        $html="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px;'>Veuillez patienter pendant le chargement de la carte...</div>";
    
        $html.="<script  >";

        /*if (count($this->coordonnees)>0) {
            foreach ($this->coordonnees as $indice => $value) {
                if (isset($value['link'])) {
                    $html.="tabAdresses[".$indice."]=\"".$value['link']."\";\n";
                }
            }
        }*/
        
        $html.="</script>";
        //$html.="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px; background-color:lime;'>Veuilliez patienter pendant le chargement de la carte...</div>";
        
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
            
        //$html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        $html.="<script>load();</script>";
        
        // fonction appelant les affichages de coordonnées ,  appels regroupées dans une fonction qui groupe les coordonnées par paquet ,  afin de ne pas trop en envoyer a la fois
        /*if (count($this->coordonnees)>0) {
            $html.="<script  >";
            $html.="var numPaquet=0;\n";
            $html.="var timer;\n";
            $html.="startTimerPaquets();\n";
            $html.="function startTimerPaquets()\n";
            $html.="{";
            $html.="afficheCoordonneesParPaquets();\n";
            $html.="timer = setInterval(\"afficheCoordonneesParPaquets()\", ".$this->setTimeOutPaquets.");\n";
            $html.="}\n";
            
            $html.="function afficheCoordonneesParPaquets(){\n";
            $i=0;
            $numPaquet = 0;
            foreach ($this->coordonnees as $indice => $value) {
                $image = ", \"http://www.google.com/mapfiles/marker.png\"";//
                if (isset($value['imageFlag']) && $value['imageFlag']!='') {
                    $image = ", \"".$value['imageFlag']."\"";
                }
                
                if ($i%10==0) {
                    $html.="if (numPaquet==".$numPaquet.")\n";
                    $html.="{\n";
                    $iDebut = $i;
                }
                
                $html.=" getCoordonnees(\"".$value['adresse']."\", ".$indice." ".$image.");\n";
                
                if ($i==$iDebut+9 || $i==count($this->coordonnees)-1 ) {
                    $html.="}\n";
                    $numPaquet++;
                }
                $i++;
            }
            

            $html.="if (numPaquet>".$numPaquet.")\n";
            $html.="{\n";
            $html.="clearInterval(timer);\n";
            $html.="}\n";
            $html.="numPaquet++;\n";
            $html.="}\n";
            $html.="</script>";
        }*/
        
        
        $html.="<script language = 'javascript'>";
        foreach ($this->coordonnees as $indice => $values) {
            if (isset($values['latitude']) && $values['latitude']!='' && isset($values['longitude']) && $values['longitude']!='') {
                $urlImage = $values['imageFlag'];
                if (!isset($values['imageHeight']) && !isset($values['imageWidth'])) {
                    list($imageSizeX,  $imageSizeY,  $typeImage,  $attrImage) = getimagesize($values['pathToImageFlag']);
                } else {
                    $imageSizeX = $values['imageWidth'];
                    $imageSizeY = $values['imageHeight'];
                }
                    
                $html.="
                    var icon = new GIcon();
                    //icon.image = image;
                    
                
                    icon.image = \"$urlImage\";
                    icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                    icon.iconSize = new GSize($imageSizeX,  $imageSizeY);
                    icon.shadowSize = new GSize(22,  20);
                    icon.iconAnchor = new GPoint(2,  24);
                    icon.infoWindowAnchor = new GPoint(5,  1);
                    var iconMarker = new GIcon(icon);";
            
            
            
                $html.="
                    
                    point$indice = new GLatLng(".$values['latitude'].", ".$values['longitude'].");
                    marker$indice = new GMarker(point$indice, iconMarker);
                    overlay$indice = map.addOverlay(marker$indice);
                    //marker$indice.openInfoWindowHtml(\"".$values['link']."\");
                    
                    ";
                
                $html.="
                            function onClickFunction$indice(overlay,  point){marker$indice.openInfoWindowHtml(\"".$values['link']."\");}";
                
                $html.="GEvent.addListener(marker$indice,  'click',  onClickFunction$indice);";
            }

                
        }
        
        $html.="</script>";
        

        
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
        
        $html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        return $html;
    }
    
    
    
    /**
     * Affiche la carte
     * Si l'on veut rajouter des evenements a cette carte ,  il faut ajouter le code des evenements apres l'appel a cette fonction, car c'est ici que l'on cree "map"
     * 
     * @return string HTML
     * */
    public function getHTML()
    {
    
            $html="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px; ".$this->divStyle."'>Veuilliez patienter pendant le chargement de la carte...</div>";
    
        //$html.="<script  >";
        /*
        if (count($this->coordonnees)>0) {
            foreach ($this->coordonnees as $indice => $value) {
                if (isset($value['link'])) {
                    $html.="tabAdresses[".$indice."]=\"".$value['link']."\";\n";
                }
            }
        }
        
        $html.="</script>";
        $html.="<div id='".$this->googleMapNameId."' style='width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px; background-color:lime;'>Veuilliez patienter pendant le chargement de la carte...</div>";
        
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
            
        $html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        $html.="<script  >load();</script>";
        
        // fonction appelant les affichages de coordonnées ,  appels regroupées dans une fonction qui groupe les coordonnées par paquet ,  afin de ne pas trop en envoyer a la fois
        if (count($this->coordonnees)>0) {
            $html.="<script  >";
            $html.="var numPaquet=0;\n";
            $html.="var timer;\n";
            $html.="startTimerPaquets();\n";
            $html.="function startTimerPaquets()\n";
            $html.="{";
            $html.="afficheCoordonneesParPaquets();\n";
            $html.="timer = setInterval(\"afficheCoordonneesParPaquets()\", ".$this->setTimeOutPaquets.");\n";
            $html.="}\n";
            
            $html.="function afficheCoordonneesParPaquets(){\n";
            $i=0;
            $numPaquet = 0;
            foreach ($this->coordonnees as $indice => $value) {
                $image = ", \"http://www.google.com/mapfiles/marker.png\"";//
                if (isset($value['imageFlag']) && $value['imageFlag']!='') {
                    $image = ", \"".$value['imageFlag']."\"";
                }
                
                if ($i%10==0) {
                    $html.="if (numPaquet==".$numPaquet.")\n";
                    $html.="{\n";
                    $iDebut = $i;
                }
                
                $html.=" getCoordonnees(\"".$value['adresse']."\", ".$indice." ".$image.");\n";
                
                if ($i==$iDebut+9 || $i==count($this->coordonnees)-1 ) {
                    $html.="}\n";
                    $numPaquet++;
                }
                $i++;
            }
            

            $html.="if (numPaquet>".$numPaquet.")\n";
            $html.="{\n";
            $html.="clearInterval(timer);\n";
            $html.="}\n";
            $html.="numPaquet++;\n";
            $html.="}\n";
            $html.="</script>";
        }
        */

        /*
        if ($this->debugMode)
            $displayDebug='block';
        else
            $displayDebug='none';
            */
        //$html.="<div style='width:500px; height:300px;overflow:scroll;display:".$displayDebug.";' id='debugGoogleMap'></div>";
        $html.="<script  >load();</script>";
        $html.="<script  >";
        if (isset($params['urlImageIcon']) && isset($params['pathImageIcon'])) {
            $urlImage = $params['urlImageIcon'];

            list($imageSizeX,  $imageSizeY,  $typeImage,  $attrImage) = getimagesize($params['pathImageIcon']);
            
            $html.="
                var icon = new GIcon();
                //icon.image = image;
                
            
                icon.image = \"$urlImage\";
                icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize($imageSizeX,  $imageSizeY);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(2,  24);
                icon.infoWindowAnchor = new GPoint(5,  1);
                var iconMarker = new GIcon(icon);
            ";
        } else {
            $html.="
                var icon = new GIcon();
                //icon.image = image;
                var iconMarker = new GIcon(icon);
                  
                icon.image = \"https://labs.google.com/ridefinder/images/mm_20_red.png\";
                icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize(30,  24);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(2,  24);
                icon.infoWindowAnchor = new GPoint(5,  1);
            ";
        }
        $html.="</script>";
        
        if (isset($params['listeCoordonnees'])) {
            $html.="<script  >";
            foreach ($params['listeCoordonnees'] as $indice => $values) {
                $html.="
                
                    point$indice = new GLatLng(".$values['latitude'].", ".$values['longitude'].");
                    marker$indice = new GMarker(point$indice, iconMarker);
                    overlay$indice = map.addOverlay(marker$indice);
                    //marker$indice.openInfoWindowHtml(\"".$values['libelle']."\");
                    
                    ";
                
                if (isset($values['jsCodeOnClickMarker'])) {
                    $html.="
                            function onClickFunction$indice(overlay,  point){".$values['jsCodeOnClickMarker']."}";
                
                    $html.="GEvent.addListener(marker$indice,  'click',  onClickFunction$indice);";
                }
                
                if (isset($values['jsCodeOnMouseOverMarker'])) {
                    $html.="function onMouseOverFunction$indice(overlay, point){".$values['jsOnMouseOverMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseover', onMouseOverFunction$indice);";
                
                }
                
                if (isset($values['jsCodeOnMouseOutMarker'])) {
                    $html.="function onMouseOutFunction$indice(overlay, point){".$values['jsCodeOnMouseOutMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseout', onMouseOutFunction$indice);";
                
                }
                
            }
            $html.="</script>";
        }
        
        
        
        return $html;
    }
    
    /**
     * ?
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getJsFunctions($params=array())
    {
        $html="";
        
        $urlImage = "https://labs.google.com/ridefinder/images/mm_20_red.png";
        $imageSizeX = "24";
        $imageSizeY = "30";

        // pour preciser que l'on veut une version stable : v=2.s
        // la derniere version v=2.x
        // version chaipakoi v=2   
        $html.="<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$this->googleMapKeyProperty."\" type=\"text/javascript\"></script>";
        $html.="<script>
                var map;
                var geocoder;
                var icon = new GIcon();
                var tabAdresses = new Array();
                icon.image = \"$urlImage\";
                icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize($imageSizeX,  $imageSizeY);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(2,  24);
                icon.infoWindowAnchor = new GPoint(5,  1);

                // addAddressToMap() is called when the geocoder returns an
                // answer.  It adds a marker to the map with an open info window
                // showing the nicely formatted version of the address and the country code.
                function addAddressToMap(response) {

                    if (!response || response.Status.code != 200) {
                        alert(\"L'adresse n'est pas correcte. Exemple : 22 rue de bâle strasbourg,  france\");
                    } 
                    else {
                        place = response.Placemark[0];
                        point = new GLatLng(place.Point.coordinates[1],  place.Point.coordinates[0]);
                        marker = new GMarker(point);
                        map.addOverlay(marker);
                        marker.openInfoWindowHtml(place.address + '<br>' + '<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
                    }

                }

                // showLocation() is called when you click on the Search button
                // in the form.  It geocodes the address entered into the form
                // and adds a marker to the map at that location.
                function showLocation() {
                    var address = document.forms[0].q.value;
                    geocoder.getLocations(address,  addAddressToMap);
                }

                // findLocation() is used to enter the sample addresses into the form.
                function findLocation(address) {
                    document.forms[0].q.value = address;
                    showLocation();
                }

                function createMarker(point,  index, image) {
                  // Create a lettered icon for this point using our icon class

                  var letter = String.fromCharCode(\"A\".charCodeAt(0) + index);
                  icon.image = image;
                  var iconMarker = new GIcon(icon);
                  
                  var marker = new GMarker(point,  iconMarker);
                    
                  GEvent.addListener(marker, \"click\", function(){
                    
                    marker.openInfoWindowHtml(tabAdresses[index]);
                    
                  });
                  /*
                  GEvent.addListener(marker,  \"click\",  function() {
                ";  
        switch($this->markerOnClickType) {
        case 'alert':
            $html.="alert(tabAdresses[index]); ";
            break;
        case 'link':
        default:
            $html.="location.href = tabAdresses[index]; ";
            break;
        }
            
            $html.="      
                  });
                  */
                  return marker;
                }

                function getCoordonnees(address,  index, image) {
                  geocoder.getLatLng(
                    address, 
                    function(point) {
                      if (!point) {
                        document.getElementById('debugGoogleMap').innerHTML+=address + \" not found<br>\";
                      } else {
                        document.getElementById('debugGoogleMap').innerHTML+=address + \"<img src='\"+image+\"'><br>\";
                        map.addOverlay(new createMarker(point,  index, image));
                      }
                    }
                  );
                }               
                </script>";
        
            // GZoom
            $html.="<script>
            function GZoomControl(oBoxStyle, oOptions, oCallbacks) {
    //box style options
  GZoomControl.G.style = {
    nOpacity:.2, 
    sColor:\"#000\", 
    sBorder:\"2px solid blue\"
  };
  var style=GZoomControl.G.style;
  for (var s in oBoxStyle) {style[s]=oBoxStyle[s]};
  var aStyle=style.sBorder.split(' ');
  style.nOutlineWidth=parseInt(aStyle[0].replace(/\D/g, ''));
  style.sOutlineColor=aStyle[2];
  style.sIEAlpha='alpha(opacity='+(style.nOpacity*100)+')';
    
    // Other options
    GZoomControl.G.options={
        bForceCheckResize:false, 
        sButtonHTML:'zoom ...', 
        oButtonStartingStyle:{width:'52px', border:'1px solid black', padding:'0px 5px 1px 5px'}, 
        oButtonStyle:{background:'#FFF'}, 
        sButtonZoomingHTML:'Drag a region on the map', 
        oButtonZoomingStyle:{background:'#FF0'}, 
        nOverlayRemoveMS:6000, 
        bStickyZoom:false
    };
    
    for (var s in oOptions) {GZoomControl.G.options[s]=oOptions[s]};
    
    // callbacks: buttonClick,  dragStart, dragging,  dragEnd
    if (oCallbacks == null) {oCallbacks={}};
    GZoomControl.G.callbacks=oCallbacks;
}

GZoomControl.prototype = new GControl();

//class globals
GZoomControl.G={
  bDragging:false, 
  mct:null, 
  mcr:null, 
  mcb:null, 
  mcl:null, 
    oMapPos:null, 
    oOutline:null, 
    nMapWidth:0, 
    nMapHeight:0, 
    nMapRatio:0, 
    nStartX:0, 
    nStartY:0, 
    nBorderCorrect:0
};

GZoomControl.prototype.initButton_=function(oMapContainer) {
    var G=GZoomControl.G;
    var oButton = document.createElement('div');
    oButton.innerHTML=G.options.sButtonHTML;
    oButton.id='gzoom-control';
    acl.style([oButton], {cursor:'pointer', zIndex:200});
    acl.style([oButton], G.options.oButtonStartingStyle);
    acl.style([oButton], G.options.oButtonStyle);
    oMapContainer.appendChild(oButton);
    return oButton;
};

GZoomControl.prototype.setButtonMode_=function(sMode){
    var G=GZoomControl.G;
    if (sMode=='zooming') {
        G.oButton.innerHTML=G.options.sButtonZoomingHTML;
        acl.style([G.oButton], G.options.oButtonZoomingStyle);
    } else {
        G.oButton.innerHTML=G.options.sButtonHTML;
        acl.style([G.oButton], G.options.oButtonStyle);
    }
};

// ******************************************************************************************
// Methods required by Google maps -- initialize and getDefaultPosition
// ******************************************************************************************
GZoomControl.prototype.initialize = function(oMap) {
  var G=GZoomControl.G;
    var oMC=oMap.getContainer();
  //DOM:button
    var oButton=this.initButton_(oMC);

    //DOM:map covers
    var o = document.createElement(\"div\");
  o.id='gzoom-map-cover';
    o.innerHTML='<div id=\"gzoom-outline\" style=\"position:absolute;display:none;\"></div><div id=\"gzoom-mct\" style=\"position:absolute;display:none;\"></div><div id=\"gzoom-mcl\" style=\"position:absolute;display:none;\"></div><div id=\"gzoom-mcr\" style=\"position:absolute;display:none;\"></div><div id=\"gzoom-mcb\" style=\"position:absolute;display:none;\"></div>';
    acl.style([o], {position:'absolute', display:'none', overflow:'hidden', cursor:'crosshair', zIndex:101});
    oMC.appendChild(o);

  // add event listeners
    GEvent.addDomListener(oButton,  'click',  GZoomControl.prototype.buttonClick_);
    GEvent.addDomListener(o,  'mousedown',  GZoomControl.prototype.coverMousedown_);
    GEvent.addDomListener(document,  'mousemove',  GZoomControl.prototype.drag_);
    GEvent.addDomListener(document,  'mouseup',  GZoomControl.prototype.mouseup_);

  // get globals
    G.oMapPos=acl.getElementPosition(oMap.getContainer());
    G.oOutline=\$id(\"gzoom-outline\"); 
    G.oButton=\$id(\"gzoom-control\");
    G.mc=\$id(\"gzoom-map-cover\");
    G.mct=\$id(\"gzoom-mct\");
    G.mcr=\$id(\"gzoom-mcr\");
    G.mcb=\$id(\"gzoom-mcb\");
    G.mcl=\$id(\"gzoom-mcl\");
    G.oMap = oMap;

    G.nBorderCorrect = G.style.nOutlineWidth*2; 
  this.setDimensions_();

  //styles
  this.initStyles_();

  //debug(\"Finished Initializing gzoom control\");  
  return oButton;
};

// Default location for the control
GZoomControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_LEFT,  new GSize(3,  120));
};

// ******************************************************************************************
// Private methods
// ******************************************************************************************
GZoomControl.prototype.coverMousedown_ = function(e){
  var G=GZoomControl.G;
  var oPos = GZoomControl.prototype.getRelPos_(e);
  //debug(\"Mouse down at \"+oPos.left+\",  \"+oPos.top);
  G.nStartX=oPos.left;
  G.nStartY=oPos.top;
  
    acl.style([G.mc], {background:'transparent', opacity:1, filter:'alpha(opacity=100)'});
  acl.style([G.oOutline], {left:G.nStartX+'px', top:G.nStartY+'px', display:'block', width:'1px', height:'1px'});
  G.bDragging=true;

  G.mct.style.top=(G.nStartY-G.nMapHeight)+'px';
  G.mct.style.display='block';
  G.mcl.style.left=(G.nStartX-G.nMapWidth)+'px';
  G.mcl.style.top=(G.nStartY)+'px';
  G.mcl.style.display='block';

  G.mcr.style.left=(G.nStartX)+'px';
  G.mcr.style.top=(G.nStartY)+'px';
  G.mcr.style.display='block';
  G.mcb.style.left=(G.nStartX)+'px';
  G.mcb.style.top=(G.nStartY)+'px';
  G.mcb.style.width='0px';
  G.mcb.style.display='block';

    // invoke the callback if provided
    if (G.callbacks.dragStart !=null){G.callbacks.dragStart(G.nStartX, G.nStartY)};

  //debug(\"mouse down done\");
  return false;
};

GZoomControl.prototype.drag_=function(e){
  var G=GZoomControl.G;
  if (G.bDragging) {
    var oPos=GZoomControl.prototype.getRelPos_(e);
    oRec = GZoomControl.prototype.getRectangle_(G.nStartX, G.nStartY, oPos, G.nMapRatio);
    G.oOutline.style.width=oRec.nWidth+\"px\";
    G.oOutline.style.height=oRec.nHeight+\"px\";
    
    G.mcr.style.left=(oRec.nEndX+G.nBorderCorrect)+'px';
    G.mcb.style.top=(oRec.nEndY+G.nBorderCorrect)+'px';
    G.mcb.style.width=(oRec.nWidth+G.nBorderCorrect)+'px';
        
        // invoke callback if provided
        if (G.callbacks.dragging !=null){G.callbacks.dragging(G.nStartX, G.nStartY, oRec.nEndX, oRec.nEndY)};
        
    return false;
  }  
};
GZoomControl.prototype.mouseup_=function(e){
  var G=GZoomControl.G;
  if (G.bDragging) {
    var oPos = GZoomControl.prototype.getRelPos_(e);
    G.bDragging=false;
    
    var oRec = GZoomControl.prototype.getRectangle_(G.nStartX, G.nStartY, oPos, G.nMapRatio);
    //debug(\"mouse up at \"+oRec.nEndX+\",  \"+oRec.nEndY+\". Height/width=\"+oRec.nWidth+\", \"+oRec.nHeight); 

    GZoomControl.prototype.resetDragZoom_();

        var nwpx=new GPoint(oRec.nStartX, oRec.nStartY);
        var nepx=new GPoint(oRec.nEndX, oRec.nStartY);
        var sepx=new GPoint(oRec.nEndX, oRec.nEndY);
        var swpx=new GPoint(oRec.nStartX, oRec.nEndY);
        var nw = G.oMap.fromContainerPixelToLatLng(nwpx); 
    var ne = G.oMap.fromContainerPixelToLatLng(nepx); 
    var se = G.oMap.fromContainerPixelToLatLng(sepx); 
    var sw = G.oMap.fromContainerPixelToLatLng(swpx); 

    var oZoomArea = new GPolyline([nw, ne, se, sw, nw], G.style.sOutlineColor, G.style.nOutlineWidth+1, .4);

    try{
      G.oMap.addOverlay(oZoomArea);
      setTimeout (function(){G.oMap.removeOverlay(oZoomArea)}, G.options.nOverlayRemoveMS);  
    }catch(e){
      jslog.error(\"error adding zoomarea overlay:\"+e.message);
    }

    oBounds=new GLatLngBounds(sw, ne);
    nZoom=G.oMap.getBoundsZoomLevel(oBounds);
    oCenter=oBounds.getCenter();
    G.oMap.setCenter(oCenter,  nZoom);

        // invoke callback if provided
        if (G.callbacks.dragEnd !=null){G.callbacks.dragEnd(nw, ne, se, sw, nwpx, nepx, sepx, swpx)};
        
        //re-init if sticky
        if (G.options.bStickyZoom) {GZoomControl.prototype.initCover_()};       
  }
};

// set the cover sizes according to the size of the map
GZoomControl.prototype.setDimensions_=function() {
  var G=GZoomControl.G;
    if (G.options.bForceCheckResize){G.oMap.checkResize()};
  var oSize = G.oMap.getSize();
  G.nMapWidth  = oSize.width;
  G.nMapHeight = oSize.height;
  G.nMapRatio  = G.nMapHeight/G.nMapWidth;
    acl.style([G.mc, G.mct, G.mcr, G.mcb, G.mcl], {width:G.nMapWidth+'px',  height:G.nMapHeight+'px'});
};

GZoomControl.prototype.initStyles_=function(){
  var G=GZoomControl.G;
    acl.style([G.mc, G.mct, G.mcr, G.mcb, G.mcl], {filter:G.style.sIEAlpha, opacity:G.style.nOpacity, background:G.style.sColor});
  G.oOutline.style.border=G.style.sBorder;  
  //debug(\"done initStyles_\");    
};

// The zoom button's click handler.
GZoomControl.prototype.buttonClick_=function(){
  if (GZoomControl.G.mc.style.display=='block'){ // reset if clicked before dragging
    GZoomControl.prototype.resetDragZoom_();
  } else {
        GZoomControl.prototype.initCover_();
    }
};

// Shows the cover over the map
GZoomControl.prototype.initCover_=function(){
  var G=GZoomControl.G;
    G.oMapPos=acl.getElementPosition(G.oMap.getContainer());
    GZoomControl.prototype.setDimensions_();
    GZoomControl.prototype.setButtonMode_('zooming');
    acl.style([G.mc], {display:'block', background:G.style.sColor});
    acl.style([G.oOutline], {width:'0px', height:'0px'});
    //invoke callback if provided
    if (GZoomControl.G.callbacks['buttonClick'] !=null){GZoomControl.G.callbacks.buttonClick()};
    //debug(\"done initCover_\");
};

GZoomControl.prototype.getRelPos_=function(e) {
  var oPos=acl.getMousePosition (e);
  var G=GZoomControl.G;
  return {top:(oPos.top-G.oMapPos.top), left:(oPos.left-G.oMapPos.left)};
};

GZoomControl.prototype.getRectangle_=function(nStartX, nStartY, oPos, nRatio){
    var dX=oPos.left-nStartX;
    var dY=oPos.top-nStartY;
    if (dX <0) dX =dX*-1;
    if (dY <0) dY =dY*-1;
    delta = dX > dY ? dX : dY;

  return {
    nStartX:nStartX, 
    nStartY:nStartY, 
    nEndX:nStartX+delta, 
    nEndY:nStartY+parseInt(delta*nRatio), 
    nWidth:delta, 
    nHeight:parseInt(delta*nRatio)
  }
};

GZoomControl.prototype.resetDragZoom_=function() {
    var G=GZoomControl.G;
    acl.style([G.mc, G.mct, G.mcr, G.mcb, G.mcl], {display:'none', opacity:G.style.nOpacity, filter:G.style.sIEAlpha});
    G.oOutline.style.display='none';    
    GZoomControl.prototype.setButtonMode_('normal');
  //debug(\"done with reset drag zoom\");
};

/* alias get element by id */
function \$id(sId) { return document.getElementById(sId); }
/* utility functions in acl namespace */
if (!window['acldefined']) {var acl={};window['acldefined']=true;}//only set the acl namespace once,  then set a flag

/* A general-purpose function to get the absolute position of
the mouse */
acl.getMousePosition=function(e) {
    var posx = 0;
    var posy = 0;
    if (!e) var e = window.event;
    if (e.pageX || e.pageY) {
        posx = e.pageX;
        posy = e.pageY;
    } else if (e.clientX || e.clientY){
        posx = e.clientX + (document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft);
        posy = e.clientY + (document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop);
    }   
    return {left:posx,  top:posy};  
};

/*
To Use: 
    var pos = acl.getElementPosition(element);
    var left = pos.left;
    var top = pos.top;
*/
acl.getElementPosition=function(eElement) {
  var nLeftPos = eElement.offsetLeft;          // initialize var to store calculations
    var nTopPos = eElement.offsetTop;            // initialize var to store calculations
    var eParElement = eElement.offsetParent;     // identify first offset parent element  
    while (eParElement != null ) {                // move up through element hierarchy
        nLeftPos += eParElement.offsetLeft;      // appending left offset of each parent
        nTopPos += eParElement.offsetTop;  
        eParElement = eParElement.offsetParent;  // until no more offset parents exist
    }
    return {left:nLeftPos,  top:nTopPos};
};
//elements is either a coma-delimited list of ids or an array of DOM objects. o is a hash of styles to be applied
//example: style('d1, d2', {color:'yellow'});  
acl.style=function(a, o){
    if (typeof(a)=='string') {a=acl.getManyElements(a);}
    for (var i=0;i<a.length;i++){
        for (var s in o) { a[i].style[s]=o[s];}
    }
};
acl.getManyElements=function(s){        
    t=s.split(', ');
    a=[];
    for (var i=0;i<t.length;i++){a[a.length]=\$id(t[i])};
    return a;
};

        
        function load() {
        //if (GBrowserIsCompatible()) {\n
        
            map = new GMap2(document.getElementById(\"".$this->googleMapNameId."\"));//, {size:new GSize(".$this->googleMapWidth.", ".$this->googleMapHeight.")}              
            ";

        if ($this->mapType!='')
            $html.="map.setMapType(".$this->mapType.");";
        
        
        if (!$this->noDisplayZoomSlider && $this->zoomType!='mini')
            $html.="map.addControl(new GLargeMapControl());";
        elseif (!$this->noDisplayZoomSlider && $this->zoomType=='mini')
            $html.="map.addControl(new GSmallZoomControl());";
            
        if (!$this->noDisplayMapTypeButtons)
            $html.="map.addControl(new GMapTypeControl());";
        
        if (!$this->noDisplayEchelle)
            $html.="map.addControl(new GScaleControl()) ;";
    
            //$html.="map.addControl(new GSmallMapControl());";
            
        if (!$this->noDisplayZoomSelectionSquare)
            $html.="map.addControl(new GZoomControl(), new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(50, 7)));";
            
            $html.="map.setCenter(new GLatLng(".$this->centerLat.", ".$this->centerLong."),  ".$this->googleMapZoom.");
            //map.addControl(new GLargeMapControl());
            geocoder = new GClientGeocoder();
            ";
        if (isset($params['jsOnLoad']))
            $html.=$params['jsOnLoad'];
        $html.="
        //}\n
        }</script>";    
        
        // objet elabel permettant d'ajouter des labels sur la carte google map
        $html.="<script  >
        // ELabel.js 
        //
        //   This Javascript is provided by Mike Williams
        //   Community Church Javascript Team
        //   http://www.bisphamchurch.org.uk/   
        //   http://econym.org.uk/gmap/
        //
        //   This work is licenced under a Creative Commons Licence
        //   http://creativecommons.org/licenses/by/2.0/uk/
        //
        // Version 0.2      the .copy() parameters were wrong
        // version 1.0      added .show() .hide() .setContents() .setPoint() .setOpacity() .overlap
        // version 1.1      Works with GMarkerManager in v2.67,  v2.68,  v2.69,  v2.70 and v2.71
        // version 1.2      Works with GMarkerManager in v2.72,  v2.73,  v2.74 and v2.75
        // version 1.3      add .isHidden()
        // version 1.4      permit .hide and .show to be used before addOverlay()
        // version 1.5      fix positioning bug while label is hidden
        // version 1.6      added .supportsHide()
        // version 1.7      fix .supportsHide()
        // version 1.8      remove the old GMarkerManager support due to clashes with v2.143


              function ELabel(point,  html,  classname,  pixelOffset,  percentOpacity,  overlap) {
                // Mandatory parameters
                this.point = point;
                this.html = html;
                
                // Optional parameters
                this.classname = classname||\"\";
                this.pixelOffset = pixelOffset||new GSize(0, 0);
                if (percentOpacity) {
                  if (percentOpacity<0){percentOpacity=0;}
                  if (percentOpacity>100){percentOpacity=100;}
                }        
                this.percentOpacity = percentOpacity;
                this.overlap=overlap||false;
                this.hidden = false;
              } 
              
              ELabel.prototype = new GOverlay();

              ELabel.prototype.initialize = function(map) {
                var div = document.createElement(\"div\");
                div.style.position = \"absolute\";
                div.innerHTML = '<div class=\"' + this.classname + '\">' + this.html + '</div>' ;
                map.getPane(G_MAP_FLOAT_SHADOW_PANE).appendChild(div);
                this.map_ = map;
                this.div_ = div;
                if (this.percentOpacity) {        
                  if (typeof(div.style.filter)=='string'){div.style.filter='alpha(opacity:'+this.percentOpacity+')';}
                  if (typeof(div.style.KHTMLOpacity)=='string'){div.style.KHTMLOpacity=this.percentOpacity/100;}
                  if (typeof(div.style.MozOpacity)=='string'){div.style.MozOpacity=this.percentOpacity/100;}
                  if (typeof(div.style.opacity)=='string'){div.style.opacity=this.percentOpacity/100;}
                }
                if (this.overlap) {
                  var z = GOverlay.getZIndex(this.point.lat());
                  this.div_.style.zIndex = z;
                }
                if (this.hidden) {
                  this.hide();
                }
              }

              ELabel.prototype.remove = function() {
                this.div_.parentNode.removeChild(this.div_);
              }

              ELabel.prototype.copy = function() {
                return new ELabel(this.point,  this.html,  this.classname,  this.pixelOffset,  this.percentOpacity,  this.overlap);
              }

              ELabel.prototype.redraw = function(force) {
                var p = this.map_.fromLatLngToDivPixel(this.point);
                var h = parseInt(this.div_.clientHeight);
                this.div_.style.left = (p.x + this.pixelOffset.width) + \"px\";
                this.div_.style.top = (p.y +this.pixelOffset.height - h) + \"px\";
              }

              ELabel.prototype.show = function() {
                if (this.div_) {
                  this.div_.style.display=\"\";
                  this.redraw();
                }
                this.hidden = false;
              }
              
              ELabel.prototype.hide = function() {
                if (this.div_) {
                  this.div_.style.display=\"none\";
                }
                this.hidden = true;
              }
              
              ELabel.prototype.isHidden = function() {
                return this.hidden;
              }
              
              ELabel.prototype.supportsHide = function() {
                return true;
              }

              ELabel.prototype.setContents = function(html) {
                this.html = html;
                this.div_.innerHTML = '<div class=\"' + this.classname + '\">' + this.html + '</div>' ;
                this.redraw(true);
              }
              
              ELabel.prototype.setPoint = function(point) {
                this.point = point;
                if (this.overlap) {
                  var z = GOverlay.getZIndex(this.point.lat());
                  this.div_.style.zIndex = z;
                }
                this.redraw(true);
              }
              
              ELabel.prototype.setOpacity = function(percentOpacity) {
                if (percentOpacity) {
                  if (percentOpacity<0){percentOpacity=0;}
                  if (percentOpacity>100){percentOpacity=100;}
                }        
                this.percentOpacity = percentOpacity;
                if (this.percentOpacity) {        
                  if (typeof(this.div_.style.filter)=='string'){this.div_.style.filter='alpha(opacity:'+this.percentOpacity+')';}
                  if (typeof(this.div_.style.KHTMLOpacity)=='string'){this.div_.style.KHTMLOpacity=this.percentOpacity/100;}
                  if (typeof(this.div_.style.MozOpacity)=='string'){this.div_.style.MozOpacity=this.percentOpacity/100;}
                  if (typeof(this.div_.style.opacity)=='string'){this.div_.style.opacity=this.percentOpacity/100;}
                }
              }

              ELabel.prototype.getPoint = function() {
                return this.point;
              }
              </script>";
        
        
        
        
        
        return $html;
    }
    
    /**
     * Attention que tout soit bien initialisé avant d'appeler cette fonction !
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function setOnClickEvent($params = array())
    {
        // ajoute un evenement
        $html="<script langage='javascript'>";
        
        if (isset($params['jsCode'])) {
            //$html.="function onClickFunction(overlay,  point)";
            $html.="GEvent.addListener(map,  'click',  function(overlay,  point){if (point){".$params['jsCode']."}});";
        } else {
            // fonctions permettant de renvoyer l'adresse a partir du point cliqué sur la carte,  on enleve le numero de l'adresse par javascript pour n'avoir que la rue
            $html.=" 
                function IsNumeric(input) {
                   return (input - 0) == input && input.length > 0;
                }

                function convertToUrlAdressSeach(str) {                   
                    var stop = false;
                    var posIni=0;
                    i=0;
                    if (IsNumeric(str.charAt(0)))
                    {
                        for(i=0; i<str.length && !stop ; i++)
                        {
                            if ( str.charAt(i)==' ')
                            {   
                                stop=true;
                                posIni = i;
                            
                            }
                        }
                    }
                
                    return str.substring(i, str.length);
                }
                
                
                function showAddress(response) {
                  map.clearOverlays();
                  if (!response || response.Status.code != 200) {
                    alert('Status Code:' + response.Status.code);
                  } else {
                    place = response.Placemark[0];
                    point = new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]);
                    marker = new GMarker(point);
                    map.addOverlay(marker);
                    marker.openInfoWindowHtml(
                        '<b>orig latlng:</b>' + response.name + '<br/>' + 
                        '<b>latlng:</b>' + place.Point.coordinates[1] + ', ' + place.Point.coordinates[0] + '<br>' +
                        '<b>Status Code:</b>' + response.Status.code + '<br>' +
                        '<b>Status Request:</b>' + response.Status.request + '<br>' +
                        '<b>Address:</b>' + place.address + '<br>' +
                        '<b>Accuracy:</b>' + place.AddressDetails.Accuracy + '<br>' +
                        '<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
                        
                        ".$params['jsAction']."
                        //ex : location.href = '?archiAffichage=recherche&submit=Rechercher&motcle='+convertToUrlAdressSeach(place.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.Thoroughfare.ThoroughfareName);
                        
                        
                        // place.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName === 'Alsace'
                        // place.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.SubAdministrativeAreaName === 'Bas Rhin'
                        // place.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName === 'Strasbourg'
                        // place.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.Thoroughfare.ThoroughfareName === '3 boulevard du president wilson'
                        
                  }
                }
            
            
                map.disableDoubleClickZoom();
                GEvent.addListener(map,  'click',  function(overlay,  point){\n
                map.clearOverlays();\n
                if (point) {\n
                map.addOverlay(new GMarker(point));\n
                map.panTo(point);\n
                document.getElementById('debug').value = point.lat() + ' ' + point.lng();\n
                geocoder.getLocations(point,  showAddress);
                
                

            }});";
        }   
        

        $html.="</script>";
        return $html;
    }
    
    /**
     * Ceci est utile quand on utilise pas la fonction load qui permet d'afficher une carte,  ici on affiche pas de carte, on se sert juste de l'API Google Maps
     * 
     * @return string HTML
     * */
    public function getJSInitGeoCoder()
    {
        return "<script>geocoder = new GClientGeocoder();</script>";
    }
    

    /**
     * Cette fonction permet de recuperer les longitudes et latitudes d'une adresse ( elle gere plusieurs appels differents grace a l'identifiant qui peut etre passé en parametre)
     * Elle renvoi la fonction recuperant les coordonnées et la ligne qui appelle cette fonction ,  cette ligne peut etre placée sur un bouton ou executée directement dans le code entourée des balises de script
     * 
     * @param array $params Paramètres
     * 
     * @return array
     * */
    public function getJSRetriveCoordonnees($params=array())
    {
        if (isset($params['identifiant']))
            $identifiant = $params['identifiant'];
        else
            $identifiant = '';
        
        if (isset($params['adresse']))
            $adresse = $params['adresse'];
        else
            $adresse = '';
        
        if (isset($params['nomChampLatitudeRetour']))
            $nomChampLatitudeRetour = $params['nomChampLatitudeRetour'];
        else
            $nomChampLatitudeRetour = "latitude";
        
        if (isset($params['nomChampLongitudeRetour']))
            $nomChampLongitudeRetour = $params['nomChampLongitudeRetour'];
        else
            $nomChampLongitudeRetour = "longitude";
            
        if (isset($params['getAdresseFromElementById']) && $params['getAdresseFromElementById']==true) {
            $location = $params['jsAdresseValue'];
        }
        else
            $location = "\\\"".$adresse."\\\"";
        
        $jsIfOK="";
        if (isset($params['jsToExecuteIfOK']))
            $jsIfOK= $params['jsToExecuteIfOK'];
        
        $jsIfNoAddressFound="";
        if (isset($params['jsToExecuteIfNoAddressFound']))
            $jsIfNoAddressFound = $params['jsToExecuteIfNoAddressFound'];
        
        
        $fonction =  "<script  >

                function getPointGMFrameWork".$identifiant."(response) {
                    if (response.Status.code != 200) 
                    {
                        //document.getElementById('debug').innerHTML+=\"erreur adresse = $adresse <br>\";
                        $jsIfNoAddressFound
                    }
                    else 
                    {
                            place = response.Placemark[0];
                            document.getElementById('$nomChampLatitudeRetour$identifiant').value = place.Point.coordinates[1];
                            document.getElementById('$nomChampLongitudeRetour$identifiant').value = place.Point.coordinates[0];
                            $jsIfOK
                            
                    }
                }               
            </script>";
        
        $appelFonction="geocoder.getLocations($location,  getPointGMFrameWork".$identifiant.");";
        
        return array('jsFunctionToExecute'=>$fonction,  'jsFunctionCall'=>$appelFonction);
    }
    
    
    /**
     * Même fonction que la precedente mais permet de rapatrier plusieurs adresses
     * 
     * @param array $params       Paramètres
     * @param array $configFields ?
     * 
     * @return array
     * */
    public function getJSMultipleRetriveCoordonnees($params=array(), $configFields = array())
    {
        if (isset($params['identifiant']))
            $identifiantUniqueFonction = $params['identifiant'];
        else
            $identifiantUniqueFonction = '';
        
        $jsIfOK="";
        if (isset($params['jsToExecuteIfOK']))
            $jsIfOK= $params['jsToExecuteIfOK'];
        
        $jsIfNoAddressFound="";
        if (isset($params['jsToExecuteIfNoAddressFound']))
            $jsIfNoAddressFound = $params['jsToExecuteIfNoAddressFound'];
        
        
        $fonction =  "<script>";
        $fonction.= "var erreurGetGoogleMap = 0;";
        $fonction.= "var trouveGetGoogleMap = 0;";
        foreach ($configFields as $identifiant => $values) {
            if (isset($values['nomChampLatitudeRetour']))
                $nomChampLatitudeRetour = $values['nomChampLatitudeRetour'];
            else
                $nomChampLatitudeRetour = "latitude";
            
            if (isset($values['nomChampLongitudeRetour']))
                $nomChampLongitudeRetour = $values['nomChampLongitudeRetour'];
            else
                $nomChampLongitudeRetour = "longitude";

            if (isset($values['adresse']))
                $adresse = $values['adresse'];
            else
                $adresse = '';
                
            $fonction.="
            

            
            
                function getPointGMFrameWork".$identifiantUniqueFonction."_".$identifiant."(response) {

                        if (response.Status.code == 200)
                        {
                            place = response.Placemark[0];
                            document.getElementById('$nomChampLatitudeRetour').value = place.Point.coordinates[1];
                            document.getElementById('$nomChampLongitudeRetour').value = place.Point.coordinates[0];
                            trouveGetGoogleMap++;
                        }
                }";
        }
        
        $fonction.="
        

            function validGetMultipleAdresse$identifiantUniqueFonction() {
                $jsIfOK
            }
        ";
        $fonction.="</script>";
        
        $appelFonction = "";
        if (isset($params['jsCodeForWaitingWhileLocalization']))
            $appelFonction.=$params['jsCodeForWaitingWhileLocalization'];
        foreach ($configFields as $identifiant => $values) {
            $location="";
            if (isset($values['getAdresseFromElementById']) && $values['getAdresseFromElementById']==true) {
                $location = $values['jsAdresseValue'];
            }

            $appelFonction.="geocoder.getLocations($location,  getPointGMFrameWork".$identifiantUniqueFonction."_".$identifiant.");";
            
            
            //$appelFonction.="alert($location);";
        }
        $appelFonction.="setTimeout('validGetMultipleAdresse$identifiantUniqueFonction()', 3000);";
        
        $appelFonction .="";
        return array('jsFunctionToExecute'=>$fonction,  'jsFunctionCall'=>$appelFonction);
    }
    
    /**
     * ?
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function setFunctionAddPointsCallableFromChild($params = array())
    {
        $html = "
            function addPoint(longitude, latitude, labelText, onClick) {
                
                var icon = new GIcon();
                
                
                  
                icon.image = \"https://labs.google.com/ridefinder/images/mm_20_red.png\";
                icon.shadow = '';//\"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize(30,  24);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(0, 0); //2, 24
                icon.infoWindowAnchor = new GPoint(5,  1);
                
                var iconMarker = new GIcon(icon);
                
                point = new GLatLng(latitude, longitude);
                marker = new GMarker(point, iconMarker);
                overlay = map.addOverlay(marker);
            
                //var eLabel = new ELabel(point, labelText, \"styleLabelGoogleMap\");
                //eLabel.pixelOffset = new GSize(20, -10);
                //map.addOverlay(eLabel);
                //eLabel.hide();
                
                //function onClickFunction(overlay,  point){currentMarker = marker; currentLabel=eLabel; onClick}
                //GEvent.addListener(marker,  'click',  onClickFunction); 
            }
        
        ";
        
        return $html;
    }
    
    /**
     * Affiche la carte et charge les fonctions sans options supplémentaires contrairement à getHTML
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function getMap($params = array())
    {
        $html="";
        /*$html="w = window.open();
        
            obj = parent.window;
        
            for(i in obj) {
                w.document.write(i+' => '+obj[i]+'<br>');
            }
        
        ";
        */
        if (isset($params['mapIsOnParentDocument']) && $params['mapIsOnParentDocument']==true) {
            if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
                $html.="map = parent.window.map;";
            } else {
                $html.="<script language = 'javascript'>map = parent.window.map;</script>";
            }
        }
        

        
        
        if (isset($params['addPointsOnMapMode'])  && $params['addPointsOnMapMode']==true) {
            // la carte est deja affichee
            // on se contente de rajouter des points
        } else {
            $html.="<div id='".$this->googleMapNameId."' style='padding:0px;margin:0px;width: ".$this->googleMapWidth."px; height: ".$this->googleMapHeight."px; background-color:lime;".$this->divStyle."'>Veuillez patienter pendant le chargement de la carte...</div>";
            // dans le cas d'un parcours de type 'walking' ,  a pied ,  il faut preciser le div avec l'affichage des informations du chemin,  sinon le parcours ne s'affichera pas
            if (isset($params['idDivDisplayEtapesText']) && $params['idDivDisplayEtapesText']!='') {
                $html.="<div id='".$params['idDivDisplayEtapesText']."' style=''></div>";
            }
            $html.="<script>load();</script>";
        }
        
        if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
            // pas de balise script
        } else {
            $html.="<script>";
        }
        
        if (isset($params['urlImageIcon']) && isset($params['pathImageIcon'])) {
            $urlImage = $params['urlImageIcon'];
            list($imageSizeX,  $imageSizeY,  $typeImage,  $attrImage) = getimagesize($params['pathImageIcon']);
            
            $html.="
                var icon = new GIcon();
                //icon.image = image;
                
            
                icon.image = \"$urlImage\";
                icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize($imageSizeX,  $imageSizeY);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(0,  0); // 2, 24
                icon.infoWindowAnchor = new GPoint(5,  1);
                var iconMarker = new GIcon(icon);
            ";
        } else {
            $html.="
                var icon = new GIcon();
                //icon.image = image;
                var iconMarker = new GIcon(icon);
                  
                icon.image = \"https://labs.google.com/ridefinder/images/mm_20_red.png\";
                icon.shadow = \"https://labs.google.com/ridefinder/images/mm_20_shadow.png\";
                icon.iconSize = new GSize(30,  24);
                icon.shadowSize = new GSize(22,  20);
                icon.iconAnchor = new GPoint(0, 0); //2, 24
                icon.infoWindowAnchor = new GPoint(5,  1);";
        }
        
        if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
            // pas de balise script
        } else {
            $html.="</script>";
        }
        
        
        if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
            // si pas de balise  ,  pas de code html non plus
        } else {
            if (isset($params['styleLabel'])) {
                $html.="<style type=\"text/css\">.styleLabelGoogleMap {".$params['styleLabel']."}</style>";
            } else {
                $html.="<style type=\"text/css\">.styleLabelGoogleMap {background-color:#FFFFD5;font-size:9px;width:170px;border:1px #006699 solid;padding:2px;}</style>";
            }
        }
        
        
        if (isset($params['listeCoordonnees'])) {
            if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
                // pas de balise de script
            } else {
                $html.="<script  >";
            }
            
            $Ymax = 0;
            $Ymin = 0;
            $Xmax = 0;
            $Xmin = 0;
            $i = 0;
            foreach ($params['listeCoordonnees'] as $indice => $values) {
                if (isset($values['urlIcon']) && $values['urlIcon']!='') {
                    $dimX = 19;
                    $dimY = 32;
                    
                    if (isset($values['dimIconX']))
                        $dimX = $values['dimIconX'];
                    if (isset($values['dimIconY']))
                        $dimY = $values['dimIconY'];
                    
                    
                    $html.="
                    
                        var icon = new GIcon();
                        icon.image = \"".$values['urlIcon']."\";
                        icon.shadow = '';
                        icon.iconSize = new GSize($dimX,  $dimY);
                        icon.shadowSize = new GSize(22,  20);
                        icon.iconAnchor = new GPoint(0,  0); // 2, 24
                        icon.infoWindowAnchor = new GPoint(5,  1);
                        var iconMarker = new GIcon(icon);
                    
                    
                    ";
                }
                $html.="
                
                    point$indice = new GLatLng(".$values['latitude'].", ".$values['longitude'].");
                    marker$indice = new GMarker(point$indice, iconMarker);
                    overlay$indice = map.addOverlay(marker$indice);
                    //marker$indice.openInfoWindowHtml(\"".$values['libelle']."\");
                ";
                
                if (isset($values['label'])) {
                    $html.="
                    var eLabel$indice = new ELabel(point$indice, \"".str_replace("\"", "&quot;", $values['label'])."\", \"styleLabelGoogleMap\");
                    eLabel$indice.pixelOffset = new GSize(20, -10);
                    map.addOverlay(eLabel$indice);
                    eLabel$indice.hide();
                    ";
                } else {
                    $html.= "var eLabel$indice = null; ";
                }
                
                if (isset($params['setAutomaticCentering']) && $params['setAutomaticCentering']==true) {
                    // verif pour que l'on reste a peu pres dans les coordonnees de la france ( verif a retirer si besoin)
                    if ($values['latitude']>47 && $values['latitude']<49 && $values['longitude']>7 && $values['longitude']<8) {
                        if ($i == 0) {
                            $yMax = $values['latitude'];
                            $yMin = $values['latitude'];
                            $xMax = $values['longitude'];
                            $xMin = $values['longitude'];
                            $i++;
                        }
                        
                        
                        if ($values['latitude']>$yMax)
                            $yMax = $values['latitude'];
                        
                        if ($values['latitude']<$yMin)
                            $yMin = $values['latitude'];
                        
                        if ($values['longitude']>$xMax)
                            $xMax = $values['longitude'];
                        
                        if ($values['longitude']<$xMin)
                            $xMin = $values['longitude'];
                        
                        //$html.=" alert(' $yMax $yMin $xMax $xMin'); ";
                    }
                }
                
                
                if (isset($values['jsCodeOnClickMarker'])) {
                    $html.="function onClickFunction$indice(overlay,  point){currentMarker = marker$indice; currentLabel=eLabel$indice; ".$values['jsCodeOnClickMarker']."}";
                    $html.="GEvent.addListener(marker$indice,  'click',  onClickFunction$indice);";
                }
                
                if (isset($values['jsCodeOnMouseOverMarker'])) {
                    $html.="function onMouseOverFunction$indice(overlay, point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOverMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseover', onMouseOverFunction$indice);";
                
                }
                
                if (isset($values['jsCodeOnMouseOutMarker'])) {
                    $html.="function onMouseOutFunction$indice(overlay, point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOutMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseout', onMouseOutFunction$indice);";
                
                }
                
            }
            
            if (isset($params['setAutomaticCentering']) && $params['setAutomaticCentering']==true && isset($yMax)) {
                $html.="
                    var max_lat = $yMax;
                    var min_lat = $yMin;
                    var max_lon = $xMax;
                    var min_lon = $xMin;
                    // calcul du zoom
                    var bounds = new GLatLngBounds;
                    bounds.extend(new GLatLng(min_lon,  min_lat));
                    bounds.extend(new GLatLng(max_lon,  max_lat));
                    var zoom = map.getBoundsZoomLevel(bounds); 
                
                    // calcul du centre
                    var centreLat = (min_lat+max_lat)/2;
                    var centreLong = (min_lon+max_lon)/2;
                    map.setCenter(new GLatLng(centreLat, centreLong), zoom); 
                    //alert(max_lat+' '+min_lat+' '+max_lon+' '+min_lon);

                    
                    
                ";
            
            }
            
            
            if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
                // pas de balise de script
            } else {
                $html.="</script>";
            }
        }
        
        // coordonnees de parcours (itineraire)
        if (isset($params['listeCoordonneesParcours'])) {
            if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
                // pas de balise de script
            } else {
                $html.="<script  >";
            }
            
            
            
            
            $html.="
            
                var directionsDiv;
            ";
            
            
            $html.="var options = {};";
            
            if (isset($params['travelMode']) && $params['travelMode']!='') {
                switch($params['travelMode']) {
                case 'walking':
                    // attention ici le div est apparement obligatoire dans ce cas
                    $html.="options.travelMode=G_TRAVEL_MODE_WALKING ; ";
                    $html.="options.locale='fr'; ";
                    $html.="options.avoidHighways=true; ";
                    $html.="directionsDiv = document.getElementById('".$params['idDivDisplayEtapesText']."'); ";
                    if (!isset($params['noDisplayParcoursGoogleAutomaticDescription']) || $params['noDisplayParcoursGoogleAutomaticDescription']==false) {
                        $html.="directionsDiv.style.display='none'; ";
                    }
                    break;
                case 'driving':
                default:
                    // cas par defaut
                    $html.="options.avoidHighways=true; ";
                    break;
                }
            }
            
            
            $html.="
                gdir = new GDirections(map, directionsDiv);
            ";
            if (isset($params['getCoordonneesParcours']) && $params['getCoordonneesParcours']==true) {
                $html.="
                    GEvent.addListener(gdir, 'load', onGDirectionLoaded);";
            }
            $html.="
                numWP = 0;
                wp = new Array();
                ";
            foreach ($params['listeCoordonneesParcours'] as $indice => $values) {
                if (isset($values['urlIcon']) && $values['urlIcon']!='') {
                    $dimX = 19;
                    $dimY = 32;
                    
                    if (isset($values['dimIconX']))
                        $dimX = $values['dimIconX'];
                    if (isset($values['dimIconY']))
                        $dimY = $values['dimIconY'];
                    
                    

                    
                    
                    $html.="
                    
                        var icon = new GIcon();
                        icon.image = \"".$values['urlIcon']."\";
                        icon.shadow = '';
                        icon.iconSize = new GSize($dimX,  $dimY);
                        icon.shadowSize = new GSize(22,  20);
                        icon.iconAnchor = new GPoint(0,  0); // 2, 24
                        icon.infoWindowAnchor = new GPoint(5,  1);
                        var iconMarker = new GIcon(icon);
                    
                    
                    ";
                }
                $html.="
                
                    point$indice = new GLatLng(".$values['latitude'].", ".$values['longitude'].");
                    marker$indice = new GMarker(point$indice, iconMarker);
                    overlay$indice = map.addOverlay(marker$indice);
                    //marker$indice.openInfoWindowHtml(\"".$values['libelle']."\");
                ";
                
                
                $html.="
                    
                    wp[numWP] = point$indice;
                    
                    numWP++;
                    ";
                
                if (isset($values['label'])) {
                    $html.="
                    var eLabel$indice = new ELabel(point$indice, \"".str_replace("\"", "&quot;", $values['label'])."\", \"styleLabelGoogleMap\");
                    eLabel$indice.pixelOffset = new GSize(20, -10);
                    map.addOverlay(eLabel$indice);
                    eLabel$indice.hide();
                    ";
                } else {
                    $html.= "var eLabel$indice = null; ";
                }
                
                
                
                
                if (isset($values['jsCodeOnClickMarker'])) {
                    $html.="function onClickFunction$indice(overlay,  point){currentMarker = marker$indice; currentLabel=eLabel$indice; ".$values['jsCodeOnClickMarker']."}";
                    $html.="GEvent.addListener(marker$indice,  'click',  onClickFunction$indice);";
                }
                
                if (isset($values['jsCodeOnMouseOverMarker'])) {
                    $html.="function onMouseOverFunction$indice(overlay, point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOverMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseover', onMouseOverFunction$indice);";
                
                }
                
                if (isset($values['jsCodeOnMouseOutMarker'])) {
                    $html.="function onMouseOutFunction$indice(overlay, point){currentMarker = marker$indice; currentLabel = eLabel$indice; ".$values['jsCodeOnMouseOutMarker']."}";
                    $html.="GEvent.addListener(marker$indice, 'mouseout', onMouseOutFunction$indice);";
                
                }
                
            }
            



            //$html.="gdir.loadFromWaypoints(wp, options); ";
            $lastPoint=end($params['listeCoordonneesParcours']);
            $html.="var encodedPolyline = new GPolyline.fromEncoded({
                        points: '".$params['polyline']."', 
                        levels: '".$params['levels']."', 
                        zoomFactor: 32, 
                        numLevels: 4
                    });
                    map.setCenter(new GLatLng(".$lastPoint['latitude'].', '.$lastPoint['longitude']."),  14);
                    map.addOverlay(encodedPolyline);";
            
            
            // récuperation des coordonnées du tracé
            if (isset($params['getCoordonneesParcours']) && $params['getCoordonneesParcours']==true) {
                $html.="
                function onGDirectionLoaded() {
                    polyline = gdir.getPolyline();

                    formForm = document.createElement('FORM');
                    formForm.setAttribute('name', 'formVertices');
                    formForm.setAttribute('action', '');
                    formForm.setAttribute('method', 'POST');
                    formForm.setAttribute('enctype', 'multipart/form-data');

                    if (polyline)
                    for(i=0 ; i<polyline.getVertexCount() ; i++)
                    {
                        longitude = polyline.getVertex(i).lng();
                        latitude = polyline.getVertex(i).lat();
                        
                        
                        formInputLongitude = document.createElement('INPUT');
                        formInputLongitude.setAttribute('type', 'text');
                        formInputLongitude.setAttribute('name', 'longitudes['+i+']');
                        formInputLongitude.setAttribute('value', longitude);
                        
                        formInputLatitude = document.createElement('INPUT');
                        formInputLatitude.setAttribute('type', 'text');
                        formInputLatitude.setAttribute('name', 'latitudes['+i+']');
                        formInputLatitude.setAttribute('value', latitude);
                        
                        formForm.appendChild(formInputLongitude);
                        formForm.appendChild(formInputLatitude);
                        
                    }
                    formSubmitButton = document.createElement('INPUT');
                    formSubmitButton.setAttribute('type', 'submit');
                    formSubmitButton.setAttribute('name', 'submitVertices');
                    formSubmitButton.setAttribute('value', 'Modifier le chemin entre les étapes');
                    
                    formForm.appendChild(formSubmitButton);
                    document.body.appendChild(formForm);
                }
                
                
                ";
            }
            
            if (isset($params['noScriptBalises']) && $params['noScriptBalises']==true) {
                // pas de balise de script
            } else {
                $html.="</script>";
            }
        }
        
        
        
        
                
        return $html;
    }
    
    /**
     * Calcul de distance
     * 
     * @param int $lat1 Latitude 1
     * @param int $lon1 Longitude 1
     * @param int $lat2 Latitude 2
     * @param int $lon2 Longitude 2
     * 
     * @return int Distance
     * */
    public function distance($lat1=0, $lon1=0, $lat2=0,  $lon2=0) 
    {
          $theta = $lon1 - $lon2;
          $dist = sin(_deg2rad($lat1)) * sin(_deg2rad($lat2)) + cos(_deg2rad($lat1)) * cos(_deg2rad($lat2)) * cos(_deg2rad($theta));
          $dist = acos($dist);
          $dist = _rad2deg($dist);
          $dist = $dist * 60 * 1.1515;
          $dist = $dist * 1.609344;

          return $dist;
    }

    /**
     * This function converts decimal degrees to radians
     * 
     * @param int $deg Degrees
     * 
     * @return int Radians
     * */
    private function _deg2rad($deg=0) 
    {
          return ($deg * pi() / 180.0);
    }

    /**
     * This function converts radians to decimal degrees
     * 
     * @param int $rad Radians
     * 
     * @return int Degrees
     * */
    private function _rad2deg($rad=0) 
    {
          return ($rad / pi() * 180.0);
    }
}
?>
