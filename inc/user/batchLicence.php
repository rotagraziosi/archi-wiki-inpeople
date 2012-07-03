<?php
/**
 * Permet à un utilisateur de passer toutes es images sous licence CC-BY-SA
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
$user = new archiUtilisateur();
$auth = new archiAuthentification();
$user= $user->getArrayInfosFromUtilisateur($auth->getIdUtilisateur());
if (isset($_POST["accept"])) {
    if ($config->connexionBdd->requete(
        "UPDATE `historiqueImage` SET `licence` = '1' WHERE idUtilisateur = '".
        $auth->getIdUtilisateur()."' AND licence = '3';"
    )) {
        header(
            "Location: ".$config->creerUrl(
                "", "afficheAccueil", array("modeAffichage"=>"monArchi"),
                false, false
            )
        );
    }
}
echo "<form method='POST' action='".$config->creerUrl("", "batchLicence")."'>";
echo "<q>"._("Moi, ").$user["nom"]." ".$user["prenom"]." ".
_(
    "accepte de publier les images que j'ai contribué
    au site archi-strasbourg.org sous licence "
).
"<a href='https://creativecommons.org/licenses/by-sa/3.0/fr/'>CC-BY-SA</a>.</q>";
echo "<input type='hidden' name='accept' value='".true."' />";
echo "<br/><input type='submit' />";
echo "</form>";
?>
