<?php
/**
 * Classe BBCodeObject
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
require_once "PEAR.php";
require_once "HTML/BBCodeParser.php";
/**
 * Classe permettant de gerer le bbCode (mise en forme de texte de base)
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
class BBCodeObject extends config
{
    /**
     * Constructeur de bbCodeObject
     * 
     * @param array $params Paramètres
     * 
     * @return void
     * */
    function __construct($params=array())
    {
        parent::__construct();
        
        if (!in_array('noPEAR', $params)) {
            $config                  = parse_ini_file('BBCodeParser.ini',  true);
            $pear = new PEAR();
            $options                 = $pear->getStaticProperty('HTML_BBCodeParser',  '_options');
            $options                 = $config['HTML_BBCodeParser'];
            $this->parserBB         = new HTML_BBCodeParser($options);
        }
        
        
    }
    
    /**
     * Convertir une chaîne BBcode en HTML
     * 
     * @param string $string Chaîne
     * 
     * @return string HTML
     * */
    public function bBversHTML ($string = '')
    {
                $this->parserBB->setText($string);
                $this->parserBB->parse();
                return $this->parserBB->getParsed();
    }
    
    
    
    /**
     * Renvoi les boutons de la mise en forme
     * en entree il faut le nom du formulaire et le nom de la textarea
     * ainsi que les différents messages d'aide
     * 
     * @param array $params Paramètres
     * 
     * @return array
     * */
    public function getBoutonsMiseEnFormeTextArea($params = array())
    {
        
        $formName='formulaire';
        if (isset($params['formName']) && $params['formName']!='') {
            $formName = $params['formName'];
        }
    
        $fieldName = 'champsTextArea';
        if (isset($params['fieldName']) && $params['fieldName']!='') {
            $fieldName = $params['fieldName'];
        }
        
        $mouseOverGras = "";
        $mouseOutGras = "";
        if (isset($params['msgGras']) && $params['msgGras']!='') {
            $mouseOverGras = "getContextHelp('".$params["msgGras"]."');";
            $mouseOutGras = "closeContextHelp();";
        }
    
        $mouseOverUnderline = "";
        $mouseOutUnderline = "";
        if (isset($params['msgUnderline']) && $params['msgUnderline']!='') {
            $mouseOverUnderline = "getContextHelp('".$params["msgUnderline"]."');";
            $mouseOutUnderline = "closeContextHelp();";
        }
        
        
        $mouseOverItalic = "";
        $mouseOutItalic = "";
        if (isset($params['msgItalic']) && $params['msgItalic']!='') {
            $mouseOverItalic = "getContextHelp('".$params["msgItalic"]."');";
            $mouseOutItalic = "closeContextHelp();";
        }
        

        $mouseOverQuote = "";
        $mouseOutQuote = "";
        if (isset($params['msgQuote']) && $params['msgQuote']!='') {
            $mouseOverQuote = "getContextHelp('".$params["msgQuote"]."');";
            $mouseOutQuote = "closeContextHelp();";
        }
        
        $mouseOverUrlInterne = "";
        $mouseOutUrlInterne = "";
        if (isset($params['msgUrlInterne']) && $params['msgUrlInterne']!='') {
            $mouseOverUrlInterne = "getContextHelp('".$params["msgUrlInterne"]."');";
            $mouseOutUrlInterne = "closeContextHelp();";
        }
        
        $mouseOverUrlExterne = "";
        $mouseOutUrlExterne = "";
        if (isset($params['msgUrlExterne']) && $params['msgUrlExterne']!='') {
            $mouseOverUrlExterne = "getContextHelp('".$params["msgUrlExterne"]."');";
            $mouseOutUrlExterne = "closeContextHelp();";
        }
        
        $idDivApercu = "apercu";
        if (isset($params['idDivPrevisualisation']) && $params['idDivPrevisualisation']!='') {
            $idDivApercu = $params['idDivPrevisualisation'];
        }
        
        
        
        $boutonsHTML = "<div style=''>";
        $gras = "<input type=\"button\" value=\"b\" style=\"width:50px;font-weight:bold\" onclick=\"bbcode_ajout_balise('b',  '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverGras\" onMouseOut=\"$mouseOutGras\"/>";
        $boutonsHTML.=$gras;
        $italic = "
    <input type=\"button\" value=\"i\" style=\"width:50px;font-style:italic\" onclick=\"bbcode_ajout_balise('i',  '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverItalic\" onMouseOut=\"$mouseOutItalic\"/>";
        $boutonsHTML.=$italic;
        $underline = "
    <input type=\"button\" value=\"u\" style=\"width:50px;text-decoration:underline;\" onclick=\"bbcode_ajout_balise('u',  '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverUnderline\" onMouseOut=\"$mouseOutUnderline\"/>";
        $boutonsHTML.=$underline;
        $quote = "
    <input type=\"button\" value=\"quote\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('quote',  '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverQuote\" onMouseOut=\"$mouseOutQuote\"/>";
        $boutonsHTML.=$quote;
        //<!--<input type=\"button\" value=\"code\" style=\"width:50px\" onclick=\"bbcode_ajout_balise('code',  'formAjoutCommentaire',  'commentaire');bbcode_keyup(this, 'apercu');\" onMouseOver=\"getContextHelp('{msgCode}');\" onMouseOut=\"closeContextHelp();\" onkeyup=\"bbcode_keyup(this, 'apercu');\"/>-->
        
        if (!isset($params['noUrlInterneButton']) || $params['noUrlInterneButton']==false) {
            $urlInterne = "<input type=\"button\" value=\"url interne\"  style=\"width:75px\" onclick=\"bbcode_ajout_balise('url',   '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverUrlInterne\" onMouseOut=\"$mouseOutUrlInterne\" onkeyup=\"bbcode_keyup(this, '$idDivApercu');\"/>";
            $boutonsHTML.=$urlInterne;
        }
        
        $urlExterne = "<input type=\"button\" value=\"url externe\"  style=\"width:80px\" onclick=\"bbcode_ajout_balise('urlExterne',   '$formName',  '$fieldName');bbcode_keyup(this, '$idDivApercu');\" onMouseOver=\"$mouseOverUrlExterne\" onMouseOut=\"$mouseOutUrlExterne\" onkeyup=\"bbcode_keyup(this, '$idDivApercu');\"/>";
        $boutonsHTML.=$urlExterne;
        

        $boutonsHTML.="</div>";
    
        return array('boutonsHTML'=>$boutonsHTML, 'divAndJsAfterForm'=>"<div id='$idDivApercu'></div><div id='helpCalque' style='background-color:#FFFFFF; border:2px solid #000000;padding:10px;float:left;display:none;'><img src='images/aide.jpg' style='float:left;padding-right:3px;' valign='middle'><div id='helpCalqueTxt' style='padding-top:7px;'></div></div><script type='text/javascript' >
                                    bbcode_keyup(document.forms['$formName'].elements['$fieldName'],  '$idDivApercu');setTimeout('majDescription()', 1000);
                                    function majDescription()
                                    {
                                        bbcode_keyup(document.forms['$formName'].elements['$fieldName'],  '$idDivApercu');
                                        setTimeout('majDescription()', 500);
                                    }</script>");
    }
    
    
    /**
     * Convertir le BBcode en HTML
     * 
     * @param array $params Paramètres
     * 
     * @return string HTML
     * */
    public function convertToDisplay($params=array())
    {
        $description ="";
        if (isset($params['text'])) {
            $description = $params['text'];
            
            $description = stripslashes($this->BBversHTML(htmlspecialchars($description)));//nl2br
            
            //$description = str_replace(array("\n\r", "\r\n", "\r", "\n"), "<br>", $description);
            /*$description = str_replace("\n\r", "--hop1--", $description);
            $description = str_replace("\r\n", "--hop2--", $description);
            $description = str_replace("\r", "--hop3--", $description);
            $description = str_replace("\n", "--hop4--", $description);*/
            
            $description = nl2br($description);
            
            $description = str_replace("###serveur###",  $this->getNomServeur(),  $description);

            $description = preg_replace("#\\[url=\"http\\://(.+)\"\\](.+)\\[/url\\]#isU", "<a href=\"http://\\1\">\\2</a>", $description);
            $description = preg_replace("#\\[url=\\'http\\://(.+)\\'\\](.+)\\[/url\\]#isU", "<a href=\"http://\\1\">\\2</a>", $description);
            $description = preg_replace("#\\[url\=http\\://(.+)\\](.+)\\[/url\\]#isU", "<a href=\"http://\\1\">\\2</a>", $description);


            $description = preg_replace("#\\[url=\"(.+)\"\\](.+)\\[/url\\]#isU", "<a href=\"\\1\">\\2</a>", $description);
            $description = preg_replace("#\\[url=\\'(.+)\\'\\](.+)\\[/url\\]#isU", "<a href=\"\\1\">\\2</a>", $description);
            $description = preg_replace("#\\[url=(.+)\\](.+)\\[/url\\]#isU", "<a href=\"\\1\">\\2</a>", $description);

            //$description = preg_replace("#\\[url=\"(.+)\"\\](.+)\\[/url\\]#isU", "<a href='\\1'>\\2</a>", $description);
            $description = preg_replace("#\\[url\\](.+)\\[/url\\]#isU", "<a href=\"\">\\1</a>", $description);
            $description = preg_replace("#\\[url=\\](.+)\\[/url\\]#isU", "<a href=\"\">\\1</a>", $description);


            $description = preg_replace("#\\[urlExterne\\=\"http\\:\\/\\/(.+)\"\\](.+)\\[\\/urlExterne\\]#isU", "<a href=\"http://\\1\" target=\"_blank\" id='debug1'>\\2</a>", $description);
            $description = preg_replace("#\\[urlExterne\\=\\'http\\://(.+)\\'\\](.+)\\[/urlExterne\\]#isU", "<a href=\"http://\\1\" target=\"_blank\" id='debug2'>\\2</a>", $description);
            $description = preg_replace("#\\[urlExterne\\=http\\:\\/\\/(.+)\\](.+)\\[\\/urlExterne\\]#isU", "<a href=\"http://\\1\" target=\"_blank\" id='debug3'>\\2</a>", $description);

            $description = preg_replace("#\\[urlExterne\\=\"(.+)\"\\](.+)\\[/urlExterne\\]#isU", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $description);
            $description = preg_replace("#\\[urlExterne\\=\\'(.+)\\'\\](.+)\\[/urlExterne\\]#isU", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $description);
            $description = preg_replace("#\\[urlExterne\\=(.+)\\](.+)\\[/urlExterne\\]#isU", "<a href=\"\\1\" target=\"_blank\">\\2</a>", $description);

            $description = preg_replace("#\\[urlExterne\\](.+)\\[/urlExterne\\]#isU", "<a href=\"\" target=\"_blank\">\\1</a>", $description);
            $description = preg_replace("#\\[urlExterne=\\](.+)\\[/urlExterne\\]#isU", "<a href=\"\" target=\"_blank\">\\1</a>", $description);
            $description = preg_replace("#\\[iframe\\=(.+)\\](.+)\\[/iframe\\]#isU", "<iframe src=\"\\1\" width='425' height='349'>\\2</iframe>", $description);
            $description = preg_replace("#\\[lang\\=(.+)\\](.+)\\[/lang\\]#isU", "<span lang=\"\\1\">\\2</span>",  $description);
            $description = ($description);
        } else {
            echo "<br>attention le parametre 'text' n'est pas defini dans la fonction convertToDisplay.<br>";
        }
        
        
        return $description;
    }
}
?>
