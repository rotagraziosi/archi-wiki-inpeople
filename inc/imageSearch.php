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
} else {
    echo 'href="index.php?archiAffichage=recherche"';
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
<br/>
<label for="licence">Licence&nbsp;:</label>
<?php
$query = 'SELECT id, name FROM licences;';
$query = mysql_query($query);
while ($licence=mysql_fetch_assoc($query)) {
    echo '<input ';
    if (isset($_GET['query'])) {
        if ($_GET['licence_'.$licence['id']] == 'on') {
            echo 'checked';
        }
    } else {
        echo 'checked';
    }
    echo ' type="checkbox" id="licence_'.$licence['id'].
        '" name="licence_'.$licence['id'].'" /><label for="licence_'.
        $licence['id'].'">'.$licence['name'].'</label>';
}
?>
</select>
</form>
<?php

if (isset($_GET['query']) && !empty($_GET['query'])) {
    echo '<hr />';
    $keyword = mysql_real_escape_string(trim($_GET['query']));
    $query = 'SELECT * FROM (
    SELECT DISTINCT
        historiqueImage.idImage, historiqueImage.idHistoriqueImage,
        historiqueImage.licence,
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
        IF(historiqueEvenement.description RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 4, 0) 
        + IF(historiqueImage.tags RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 20, 0) 
        + IF(historiqueImage.description RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 10, 0) 
        + IF(historiqueEvenement.titre RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 6, 0) 
        + IF(historiqueAdresse.nom RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 2, 0) 
        + IF(quartier.nom RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 2, 0) 
        + IF(historiqueEvenement.description LIKE "%'.$keyword.'%", 2, 0) 
        + IF(historiqueImage.tags LIKE "%'.$keyword.'%", 10, 0) 
        + IF(historiqueImage.description LIKE "%'.$keyword.'%", 5, 0) 
        + IF(historiqueEvenement.titre LIKE "%'.$keyword.'%", 3, 0) 
        + IF(historiqueAdresse.nom LIKE "%'.$keyword.'%", 1, 0) 
        + IF(quartier.nom LIKE "%'.$keyword.'%", 1, 0) 
        ) DESC
    ) results
    GROUP BY results.idImage
    ORDER BY (
        IF(results.tags RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 20, 0) 
        + IF(results.description RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 10, 0) 
        + IF(results.titre RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 6, 0) 
        + IF(results.nom RLIKE "[[:<:]]'.$keyword.'[[:>:]]", 2, 0) 
        + IF(results.tags LIKE "%'.$keyword.'%", 10, 0) 
        + IF(results.description LIKE "%'.$keyword.'%", 5, 0) 
        + IF(results.titre LIKE "%'.$keyword.'%", 3, 0) 
        + IF(results.nom LIKE "%'.$keyword.'%", 1, 0) 
        ) DESC
    LIMIT 96';
    $query = mysql_query($query);
    $bbcode= new bbCodeObject();
    while ($results=mysql_fetch_assoc($query)) {
        $req = 'SELECT idHistoriqueImage, licence, historiqueImage.idImage,
        dateUpload, historiqueImage.description, historiqueAdresse.idAdresse,
        historiqueEvenement.idEvenement FROM historiqueImage 
        LEFT JOIN _evenementImage
            ON historiqueImage.idImage = _evenementImage.idImage
        LEFT JOIN historiqueEvenement
            ON historiqueEvenement.idEvenement = _evenementImage.idEvenement
        LEFT JOIN  _evenementEvenement
            ON  _evenementEvenement.idEvenementAssocie
                = historiqueEvenement.idEvenement
        LEFT JOIN _adresseEvenement
            ON _adresseEvenement.idEvenement = _evenementEvenement.idEvenement
        LEFT JOIN historiqueAdresse
            ON historiqueAdresse.idAdresse = _adresseEvenement.idAdresse
        WHERE historiqueImage.idImage = '.
            mysql_real_escape_string($results['idImage']).'
        ORDER BY idHistoriqueImage DESC LIMIT 1';
        $res = $config->connexionBdd->requete($req);
        $image = mysql_fetch_assoc($res);
        if ($_GET['licence_'.$image['licence']] == 'on') {
            echo '<a class="imgResultGrp" href="'.$config->creerUrl(
                '', 'imageDetail', array('archiRetourAffichage'=>'evenement',
                'archiRetourIdName'=>'idEvenement',
                'archiIdImage'=>$image['idImage'],
                'archiIdAdresse'=>$image['idAdresse'],
                'archiRetourIdValue'=>$image['idEvenement'])
            ).'"><div class="imgResult"></div>
            <div class="imgResultHover"><img src="'.
            'photos--'.$image['dateUpload'].'-'.$image['idHistoriqueImage'].
            '-moyen.jpg'.'" alt="" /><p>'.strip_tags(
                $bbcode->convertToDisplay(
                    array('text'=>$image['description'])
                )
            ).'</p></div></a>';
        }
    }
            
} 
