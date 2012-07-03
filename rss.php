<?php
/**
 * Paramètres du site
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  Inconnue https://archi-strasbourg.org/?archiAffichage=faq
 * @link     https://archi-strasbourg.org/
 * 
 * */
header("Content-Type: application/rss+xml");
require_once "includes/framework/config.class.php";
require_once "includes/framework/localization.php";
$config = new Config();
echo "<?xml version='1.0' encoding='utf-8'?>".PHP_EOL;
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
<?php
$adresse = new archiAdresse();
$last = $adresse->getDerniersEvenementsParCategorie(5);
//print_r($last);
//dernieresAdresses, constructions, demolitions, actualites, culture, vues
$type = isset($_GET["type"])?$_GET["type"]:"actualites";
echo "<atom:link href='".$config->getUrlRacine().
"rss.php?type=".$type."' rel='self' type='application/rss+xml' />";
switch ($type) {
case "dernieresAdresses":
    $feedDesc=_("Nouvelles adresses");
    break;
case "constructions":
    $feedDesc=_("Derniers travaux");
    break;
case "demolitions":
    $feedDesc=_("Dernières démolitions");
    break;
case "actualites":
    $feedDesc=_("Actualités");
    break;
case "culture":
    $feedDesc=_("Derniers événements culturels");
    break;
case "dernieresVues":
    $feedDesc=_("Dernières vues");
    break;
}
echo "<title>".$config->titreSite." - ".$feedDesc."</title>";
echo "<link>".$config->getUrlRacine()."</link>";
echo "<description>".$config->descSite."</description>";
echo "<language>".substr(LANG, 0, 2)."</language>";
?>
<generator>ArchiWiki</generator>
<docs>http://www.rssboard.org/rss-specification</docs>
<?php
foreach ($last[$type] as $item) {
    switch ($type) {
    case "actualites":
        $titre=stripslashes($item["titre"]);
        $desc=htmlspecialchars(stripslashes($item["texte"]));
        $date=stripslashes($item["date"]);
        $link=$config->getUrlRacine()."actualites-archi-strasbourg-".
        $item["idActualite"].".html";
        break;
    case "dernieresVues":
        $arrayIntituleAdressesVuesSur = array();
        foreach ($item['listeVueSur'] as $indice => $valueVuesSur) {
            $arrayIntituleAdressesVuesSur[] = $adresse->getIntituleAdresseFrom(
                $valueVuesSur['idEvenementGroupeAdresse'],
                'idEvenementGroupeAdresse',
                array('ifTitreAfficheTitreSeulement'=>true, 'noVille'=>true,
                'noQuartier'=>true, 'noSousQuartier'=>true)
            );
        }  
        $titre = html_entity_decode(
            strip_tags(implode("/ ", $arrayIntituleAdressesVuesSur)),
            ENT_COMPAT, 'UTF-8'
        );
        $link=$config->creerUrl(
            '', 'imageDetail', array("archiIdImage"=>$item['idImage'],
            "archiRetourAffichage"=>'evenement', "archiRetourIdName"=>'idEvenement',
            "archiRetourIdValue"=>$item['idEvenementGroupeAdresse'])
        );
        $date=stripslashes($item['dateUpload']);
        break;
    default:
        $titre = html_entity_decode(
            strip_tags(
                $adresse->getIntituleAdresseAccueil(
                    $item, array("ifTitreAfficheTitreSeulement"=>true)
                )
            ),
            ENT_COMPAT, "UTF-8"
        );
        $date=isset($item["dateCreationEvenement"])?
        $item["dateCreationEvenement"]:$item["dateCreationAdresse"];
        $link=$config->creerUrl(
            '', '', array(
                'archiAffichage'=>'adresseDetail',
                "archiIdAdresse"=>$item['idAdresse'],
                "archiIdEvenementGroupeAdresse"=>$item['idEvenementGroupeAdresse']
            )
        );
    }
    echo "<item>
        <title>".$titre."</title>";
    if (isset($desc)) {
        echo "<description>".$desc."</description>";
    }
        echo "<pubDate>".date("r", strtotime($date))."</pubDate>
        <link>".$link."</link>
        <guid>".$link."</guid>
    </item>";
}
?>
    </channel>
</rss>
