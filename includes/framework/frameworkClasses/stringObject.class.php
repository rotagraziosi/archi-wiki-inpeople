<?php

/**
 * Classe stringObject
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */

require_once "stringDiff/filediff.class.php";



//require_once("PEAR.php");
//include_once('Text/Diff.php'); // installation du package supplémentaire : en ligne de commande sous linux => "pear install Text-Diff-1.1.1"
//include_once('Text/Diff/Renderer.php');
//include_once('Text/Diff/Renderer/unified.php');
//include_once('Text/Diff/Renderer/inline.php');
//include_once('Text/Diff/Renderer/context.php');

/**
 * Bibliotheque de fonctions sur les chaines de caracteres
 * 
 * PHP Version 5.3.3
 * 
 * @category Class
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @author   Partenaire Immobilier <contact@partenaireimmo.com>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */

class StringObject extends config
{
    
    /**
     * Constructeur de stringObject
     * 
     * @param array $params Paramètres (inutilisé)
     * 
     * @return void
     * */
    function __construct($params=array())
    {
        parent::__construct();
        
    }
    
    
    /**
     * Sert a afficher les descriptions sur les listes
     * pour eviter d'avoir des balises BB coupées en deux
     * 
     * @param string $string Texte
     * 
     * @return string
     * */
    public function sansBalises ($string = '')
    {
        return preg_replace('#\[(.*)\](.*)\[\/(.*)\]#isU', '$2', $string);
    }
    
    /**
     * Retire les balises HTML d'un texte
     * 
     * @param string $string Texte
     * 
     * @return string
     * */
    public function sansBalisesHtml ($string = '')
    {
        //return preg_replace('#<(.*)>(.*)<\/(.*)>#isU', '$2', $string);
        return preg_replace("/<[^>]*>/", "", $string);
    }
    
    /**
     * Permet de retirer les accents d'une chaine
     * 
     * @param string $string Chaine
     * 
     * @return string
     * */
    public function sansAccents($string = '')
    {
        $retour = "";
        $retour = str_replace(array("é", "è", "ê", "ë", "ï", "î", "â", "ä", "à", "ô", "ö", "ü", "û"), array("e", "e", "e", "e", "i", "i", "a", "a", "a", "o", "o", "u", "u"), $string);
        $retour = str_replace(array(utf8_encode("é"), utf8_encode("è"), utf8_encode("ê"), utf8_encode("ë"), utf8_encode("ï"), utf8_encode("î"), utf8_encode("â"), utf8_encode("ä"), utf8_encode("à"), utf8_encode("ô"), utf8_encode("ö"), utf8_encode("ü"), utf8_encode("û")), array("e", "e", "e", "e", "i", "i", "a", "a", "a", "o", "o", "u", "u"), $string);
        return $retour;
    }
    
    
    /**
     * Permet de convertir une chaine afin qu'elle passe a l'url rewriting
     * (pour les adresses par exemple)
     * 
     * @param string $texte  Texte à convertir
     * @param array  $params Paramètres
     * 
     * @return string
     * */
    public function convertStringToUrlRewrite($texte, $params=array())
    {
        $caractereDefault="_";
        if (isset($params['setCaractereDefault'])) {
            $caractereDefault = $params['setCaractereDefault'];
        }
        $texte = str_replace("&nbsp;", "_", $texte);
        $texte = strip_tags($texte);
        
        $texte = pia_strtolower($texte);
        $texte = Pia_eregreplace("[\ |\']", $caractereDefault, $texte);

        $texte = str_replace(array("ô", "à", "â", "û", "î", "é", "è", "ê", "&", ";", "(", ")", "ä", "/"), array("o", "a", "a", "u", "i", "e", "e", "e", "et", $caractereDefault, $caractereDefault, $caractereDefault, "a", $caractereDefault), $texte);
        $texte = str_replace(array(utf8_encode("ô"), utf8_encode("à"), utf8_encode("â"), utf8_encode("û"), utf8_encode("î"), utf8_encode("é"), utf8_encode("è"), utf8_encode("ê"), "&", ";", "(", ")", utf8_encode("ä")), array("o", "a", "a", "u", "i", "e", "e", "e", "et", $caractereDefault, $caractereDefault, $caractereDefault, "a"), $texte);

        return urlencode($texte);
    }

    /**
     * Permet de couper un texte trop long
     * 
     * @param string $texte  Texte à couper
     * @param int    $nbMots Nombre de mots maximum à conserver
     * 
     * @return string
     * */
    public function coupureTexte($texte, $nbMots)
    {
        $retour = "";
        $texte = trim($this->sansBalisesHtml($texte));
        $tabMots = explode(" ", $texte);

        if (count($tabMots)<$nbMots)
            $nbMots = count($tabMots);
            
        $i=0;
        
        while ($i<$nbMots && !pia_ereg("\[url", $tabMots[$i])) {
            $retour.=$tabMots[$i];
            if ($i<($nbMots-1)) {
                $retour.=" ";
            }
            $i++;
        }
        
        if (pia_strlen($texte)>0 && pia_strlen($texte) > pia_strlen($retour)) {
            $retour.="…";
        }
        
        return $retour;
    }
    
    /**
     * Fonction permettant de nettoyer une url de slashes en trop 
     * (exemple : http://ri67.fr//index.html)
     * 
     * @param string $url URL à nettoyer
     * 
     * @return string
     * */
    public function cleanUrlFromDoubleSlashes($url)
    {
        if (pia_substr($url, 0, 7) == "http://") {
            $url = preg_replace("#http\\:\\/\\/(.+)#isU", "\\1", $url);
            $url = str_replace("//", "/", $url);
            $url = "http://".$url;
        } elseif (pia_substr($url, 0, 8) == "https://") {
            $url = preg_replace("#https\\:\\/\\/(.+)#isU", "\\1", $url);
            $url = str_replace("//", "/", $url);
            $url = "https://".$url;
        } else {
            $url = str_replace("//", "/", $url);
        }
        
        return $url;
    }
    
    /**
     * Fonction permettant de savoir s'il y a des caracteres en utf8 dans la chaine
     * 
     * @param string $str Chaine à tester
     * 
     * @return string
     * */
    function isUTF8($str)
    {
         // astuce pour entrer dans un test booléen ^^
         // (si c'est un tableau... ce qui est forcement vrai)
        if (is_array($str)) {
            $str = implode('', $str);
            // retourne FALSE si aucun caractere n'appartient au jeu utf8
            return !((ord($str[0]) != 239) && (ord($str[1]) != 187) && (ord($str[2]) != 191));
        } else {
            // retourne TRUE
            // si la chaine decoder et encoder est egale a elle meme
            return (utf8_encode(utf8_decode($str)) == $str);
        }    
    }
    
    /**
     * Fonction d'encodage en utf8 a partir d'autre encodage , pratique => detection
     * 
     * @param string $str Chaine à encoder
     * 
     * @return string
     * */
    function encodeToUTF8($str)
    {
        $encodage = mb_detect_encoding($str, "UTF-8, ISO-8859-1, ISO-8859-15, windows-1252", true);
        $str_utf8 = mb_convert_encoding($str, "UTF-8", $encodage);
        return $str_utf8;
    }
    
    
    /**
     * Fonction permettant de connaitre le pourcentage de caracteres d'un certain type dans une chaine
     * 
     * @param string $string        ?
     * @param string $typeRecherche ?
     * 
     * @return int
     * */
    public function getPourcentageCaracteresDeType($string='', $typeRecherche='majuscules')
    {
        $pourcentage = 0;
        
        $totalCaracteresDuTypeRecherche=0;
        
        $longueurChaine = pia_strlen($string);
        
        $borneAsciiSuperieur = null;
        $borneAsciiInferieur = null;
        
        $codeEspace = 32;
        $nbEspaces=0;
        
        $borneAsciiInferieurChiffre = 48;
        $borneAsciiSuperieurChiffre = 57;
        $nbChiffres=0;
        
        $nbAutres=0;
        
        switch($typeRecherche) {
        case 'majuscules':
            $borneAsciiInferieur = 65;// A
            $borneAsciiSuperieur = 90;// Z
            break;
        default:
            echo "Erreur : string::getPourcentageCaracteresDeType => le type transmis en parametre n'a pas ete trouvé<br>";
            break;        
        }
        
        // parcours de la chaine
        if ($borneAsciiSuperieur!=null && $borneAsciiInferieur!=null) {
            for ($i=0 ; $i<$longueurChaine ; $i++) {
                $caractereCourant = pia_substr($string, $i, 1);
                if (ord($caractereCourant) >= $borneAsciiInferieur && ord($caractereCourant)<=$borneAsciiSuperieur) {
                    $totalCaracteresDuTypeRecherche++;
                } elseif (ord($caractereCourant) == $codeEspace) {
                    $nbEspaces++;
                } elseif (ord($caractereCourant)>=$borneAsciiInferieurChiffre && ord($caractereCourant)<=$borneAsciiSuperieurChiffre) {
                    $nbChiffres++;
                } else {
                    $nbAutres++;
                }
            }
            
            if (($longueurChaine-$nbEspaces-$nbChiffres-$nbAutres)>0)
                $pourcentage = ($totalCaracteresDuTypeRecherche/($longueurChaine-$nbEspaces-$nbChiffres-$nbAutres))*100;
            
        }

        return $pourcentage;    
    }
    
    /**
     * Permet de comparer 2 fichiers
     * 
     * @param array $params Liste de paramètres
     * 
     * @return array
     * */
    public function getTexteDifferences($params = array())
    {
        $retour = "";
        if (isset($params['nouveau']) && isset($params['ancien'])) {
            $fd = new filediff(); // appel de la classe filediff integree au framework
            
            $fd->set_textes($params['nouveau'], $params['ancien']);
            
            $fd->execute();
            
            $retour['html'] = $fd->display();
            //$retour['ancien']
            //$retour['nouveau']
        }
    
        return $retour;
    }
    
    /*
    // http://articles.techrepublic.com.com/5100-10878_11-6174867.html
    // si utilisation de cette fonction , il faut enlever les commentaires pour les includes vers les bibliotheques au debut du fichier 
    public function getTxtDiffByPEAR($params = array())
    {
        $separator = '<br />';
        if (isset($params['separator']))
            $separator = $params['separator'];
    
        $diff = new Text_Diff(
            explode($separator,strip_tags(nl2br($params['ancien']), $separator)),
            explode($separator,strip_tags(nl2br($params['nouveau']), $separator))
        );
        
        $renderer = new Text_Diff_Renderer_inline();
        echo $renderer->render($diff);
        
    }
    */
}
?>
