<?php
/**
 * Affiche le formulaire d'adhésion
 * 
 * PHP Version 5.3.3
 * 
 * @category General
 * @package  ArchiWiki
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GNU GPL v3 https://www.gnu.org/licenses/gpl.html
 * @link     https://archi-strasbourg.org/
 * 
 * */
 ?>
<h2>Recherche d'images</h2>
<a 
<?php
if (isset($_GET['query'])) {
    echo 'href="index.php?archiAffichage=recherche&submit=Rechercher&motcle='.
        htmlspecialchars(stripslashes($_GET['query'])).'"';
} 
?>
>Texte</a> &mdash; <b>Images</b>

<form action="index.php" method="get">
<input class="searchInput" type="search" name="query"
<?php
if (isset($_GET['query'])) {
    echo 'value="'.htmlspecialchars(stripslashes($_GET['query'])).'"';
} 
?>
 />
<input type="hidden" name="archiAffichage" value="imageSearch" />
<input class="loupe" type="image" src="images/Advisa/loupe.png">
</form>
<?php

if (isset($_GET['query']) && !empty($_GET['query'])) {
    echo '<hr />';
    $keyword = mysql_real_escape_string(trim($_GET['query']));
    $query = 'SELECT * FROM (
    SELECT DISTINCT
        historiqueImage.idImage, historiqueImage.idHistoriqueImage,
        historiqueImage.tags, historiqueEvenement.titre, quartier.nom,
        historiqueImage.description, historiqueAdresse.idAdresse,
        historiqueEvenement.idEvenement, historiqueImage.dateUpload
    FROM historiqueImage
    LEFT JOIN _evenementImage ON historiqueImage.idImage = _evenementImage.idImage
    LEFT JOIN historiqueEvenement
        ON historiqueEvenement.idEvenement = _evenementImage.idEvenement
    LEFT JOIN  _evenementEvenement
        ON  _evenementEvenement.idEvenementAssocie = historiqueEvenement.idEvenement
    LEFT JOIN _adresseEvenement
        ON _adresseEvenement.idEvenement = _evenementEvenement.idEvenement
    LEFT JOIN historiqueAdresse
        ON historiqueAdresse.idAdresse = _adresseEvenement.idAdresse
    LEFT JOIN quartier ON quartier.idQuartier = historiqueAdresse.idQuartier
    WHERE (NOT ISNULL(historiqueEvenement.description))
    AND (NOT ISNULL(historiqueAdresse.idAdresse))
    AND
    (MATCH (historiqueImage.description)
        AGAINST ("+'.str_replace(' ', ' +', $keyword).'" IN BOOLEAN MODE)
    OR historiqueEvenement.description LIKE "%'.$keyword.'%"
    OR historiqueImage.tags LIKE "%'.$keyword.'%"
    OR historiqueEvenement.titre LIKE "%'.$keyword.'%"
    OR historiqueAdresse.nom LIKE "%'.$keyword.'%"
    OR quartier.nom LIKE "%'.$keyword.'%")
    ORDER BY (
        IF(historiqueEvenement.description LIKE "%'.$keyword.'%", 2, 0) 
        + IF(historiqueImage.tags LIKE "%'.$keyword.'%", 10, 0) 
        + IF(historiqueImage.description LIKE "%'.$keyword.'%", 5, 0) 
        + IF(historiqueEvenement.titre LIKE "%'.$keyword.'%", 3, 0) 
        + IF(historiqueAdresse.nom LIKE "%'.$keyword.'%", 1, 0) 
        + IF(quartier.nom LIKE "%'.$keyword.'%", 1, 0) 
        ) DESC
    LIMIT 96) results
    GROUP BY results.idImage
    ORDER BY (
        IF(results.tags LIKE "%'.$keyword.'%", 10, 0) 
        + IF(results.description LIKE "%'.$keyword.'%", 5, 0) 
        + IF(results.titre LIKE "%'.$keyword.'%", 3, 0) 
        + IF(results.nom LIKE "%'.$keyword.'%", 1, 0) 
        + IF(results.nom LIKE "%'.$keyword.'%", 1, 0) 
        ) DESC';
    $query = mysql_query($query);
    $bbcode= new bbCodeObject();
    while ($results=mysql_fetch_assoc($query)) {
        echo '<a class="imgResultGrp" href="'.$config->creerUrl(
            '', 'imageDetail', array('archiRetourAffichage'=>'evenement',
            'archiRetourIdName'=>'idEvenement', 'archiIdImage'=>$results['idImage'],
            'archiIdAdresse'=>$results['idAdresse'],
            'archiRetourIdValue'=>$results['idEvenement'])
        ).'"><div class="imgResult"></div><div class="imgResultHover"><img src="'.
        'photos--'.$results['dateUpload'].'-'.$results['idHistoriqueImage'].
        '-moyen.jpg'.'" alt="" /><p>'.strip_tags(
            $bbcode->convertToDisplay(
                array('text'=>$results['description'])
            )
        ).'</p></div></a>';
    }
            
} 
