<?php
/**
 * Script pour copier des personnes de l'ancien systèmes vers le nouveau
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
require_once "includes/framework/config.class.php";
require_once "modules/archi/includes/archiEvenement.class.php";
$config=new Config();
$req = "
    SELECT idPersonne, description
    FROM personne";
$res = $config->connexionBdd->requete($req);
$e=new archiEvenement();
while ($person=mysql_fetch_object($res)) {
    $req2 = "
    SELECT *
    FROM _personneEvenement
    WHERE idPersonne = ".$person->idPersonne;
    if (!mysql_fetch_object($config->connexionBdd->requete($req2))) {
        $idEvenement=$e->getNewIdEvenement();
        $idSousEvenement=$idEvenement+1;
        $req3 = "
        INSERT INTO `historiqueEvenement` (
            `idEvenement`,
            `dateCreationEvenement`,
            `idTypeEvenement`
        )
        VALUES (
            '".$idEvenement."',
            now(),
            '".$e->getIdTypeEvenementGroupeAdresse()."'
        )";
        $config->connexionBdd->requete($req3);
        $req3 = "
        INSERT INTO `historiqueEvenement` (
            `idEvenement`, `description`,
            `dateCreationEvenement`
        )
        VALUES (
            '".$idSousEvenement."',
            '".mysql_escape_string($person->description)."',
            now()
        )";
        $config->connexionBdd->requete($req3);
        $req3 = "
        INSERT INTO `_personneEvenement` (
            `idPersonne`, `idEvenement`
        )
        VALUES (
            '".$person->idPersonne."',
            ".$idEvenement."
        )";
        $config->connexionBdd->requete($req3);
        $req3 = "
            insert into _evenementEvenement (idEvenement,idEvenementAssocie) 
            values ('".$idEvenement."','".$idSousEvenement."');
        ";
        $config->connexionBdd->requete($req3);
    }
}
?>
