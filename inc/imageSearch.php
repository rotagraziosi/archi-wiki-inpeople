<h2>Recherche d'images</h2>
<form action="index.php" method="get">
<input class="searchInput" type="search" name="query"
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
if (isset($_GET['query'])) {
    echo 'value="'.$_GET['query'].'"';
} 
?>
 />
<input type="hidden" name="archiAffichage" value="imageSearch" />
<input class="loupe" type="image" src="images/Advisa/loupe.png">
</form>
<?php

if (isset($_GET['query'])) {
    echo '<hr />';
    $keyword = mysql_real_escape_string($_GET['query']);
    $query = 'SELECT DISTINCT
        historiqueImage.idImage, historiqueImage.idHistoriqueImage, historiqueImage.description,
        historiqueAdresse.idAdresse, historiqueEvenement.idEvenement
    FROM historiqueImage
    LEFT JOIN _evenementImage ON historiqueImage.idImage = _evenementImage.idImage
    LEFT JOIN historiqueEvenement
        ON historiqueEvenement.idEvenement = _evenementImage.idEvenement
    LEFT JOIN _evenementAdresseLiee
        ON _evenementAdresseLiee.idEvenement = historiqueEvenement.idEvenement
    LEFT JOIN historiqueAdresse
        ON historiqueAdresse.idAdresse = _evenementAdresseLiee.idAdresse
    LEFT JOIN quartier ON quartier.idQuartier = historiqueAdresse.idQuartier
    WHERE (NOT ISNULL(historiqueEvenement.description))
    AND
    (historiqueImage.description LIKE "%'.$keyword.'%"
    OR historiqueEvenement.description LIKE "%'.$keyword.'%"
    OR historiqueEvenement.titre LIKE "%'.$keyword.'%"
    OR historiqueAdresse.nom LIKE "%'.$keyword.'%"
    OR quartier.nom LIKE "%'.$keyword.'%")
    GROUP BY historiqueImage.idImage
    ORDER BY (NOT(historiqueImage.description LIKE "%'.$keyword.'%")),
    (NOT( historiqueEvenement.description LIKE "%'.$keyword.'%")),
    (NOT( historiqueEvenement.titre LIKE "%'.$keyword.'%")),
    (NOT( historiqueAdresse.nom LIKE "%'.$keyword.'%")),
    (NOT( quartier.nom LIKE "%'.$keyword.'%"))
    LIMIT 100';
    $query = mysql_query($query);
    $bbcode= new bbCodeObject();
    while ($results=mysql_fetch_assoc($query)) {
        echo '<a href="'.$config->creerUrl(
            '', 'imageDetail', array('archiRetourAffichage'=>'evenement',
            'archiRetourIdName'=>'idEvenement', 'archiIdImage'=>$results['idImage'],
            'archiIdAdresse'=>$results['idAdresse'],
            'archiRetourIdValue'=>$results['idEvenement'])
        ).'"><img src="getPhotoSquare.php?id='.$results['idHistoriqueImage'].'" alt="" /></a>';
        /*
        echo '<p>'.strip_tags($bbcode->convertToDisplay(
            array('text'=>$results['description'])
        )).'</p>';
        * */
    }

} 
