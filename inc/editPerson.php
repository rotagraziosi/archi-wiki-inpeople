<?php
/**
 * Editer une personne
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
if (isset($_POST["evenementGroupeAdresse"])) {
    $personne = new archiPersonne();
    $personne->modifier(
        $_GET["id"], $_POST["prenom"], $_POST["nom"], $_POST["metier"],
        $_POST["dateNaissance"], $_POST["dateDeces"]
    );
}

$t=new Template('modules/archi/templates/');
$t->set_filenames((array('nouveauDossier'=>'nouveauDossier.tpl')));

$formAction="ajoutNouvelPersonne";
$t->assign_block_vars("ajoutPersonne",  array());
$resJobs=$config->connexionBdd->requete("SELECT * FROM `metier`");

$resPerson=$config->connexionBdd->requete(
    "SELECT * FROM `personne` WHERE idPersonne='".
    mysql_escape_string($_GET["id"])."'"
);
$p = mysql_fetch_object($resPerson);

$jobList="";
while ($job = mysql_fetch_assoc($resJobs)) {
    if (!empty($job["nom"])) {
        $jobList.="<option value='".$job["idMetier"]."'";
        if ($job["idMetier"]==$p->idMetier) {
            $jobList.=" selected='selected' ";
        }
        $jobList.=">".$job["nom"]."</option>";
    }
}
$t->assign_vars(
    array(
        'titrePage'=>_("Edition de")." <a href='".$config->creerUrl(
            "", "evenementListe", array("selection"=>"personne", "id"=>869)
        )."'>".$p->prenom." ".$p->nom."</a>", 
        "jobList"=>$jobList,
        "typeBoutonValidation"=>"submit"
    )
);   


$t->assign_vars(
    array(
        "firstname"=>$p->prenom,
        "name"=>$p->nom,
        "birth"=>$config->date->toFrench($p->dateNaissance),
        "death"=>$config->date->toFrench($p->dateDeces),
        "desc"=>$p->description
    )
);

echo $t->pparse('nouveauDossier');
?>
