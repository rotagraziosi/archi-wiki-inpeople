<?php
/**
 * Supprimer une personne
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
require_once __DIR__.'/../modules/archi/includes/archiPersonne.class.php';
$p = new ArchiPersonne($_GET["id"]);

if (isset($_POST['delete'])) {
    if ($p->supprimer()) {
        header('Location: index.php');
    }
}

echo _('Êtes-vous sûr de vouloir supprimer la personne').' <b>'.$p->prenom.
' '.$p->nom.'</b>&nbsp;?<br/>';
echo '<form class="inline" action="personnalite-'.$p->nom.'_'.$p->prenom.
'-'.$_GET["id"].'.html"><input type="submit" value="'._('Non').'" /></form>';
echo '<form action="index.php?archiAffichage=deletePerson&id='.$_GET["id"].
'" method="post" class="inline"><input type="hidden" name="delete" value="'.
true.'"/><input type="submit" value="'._('Oui').'" /></form>';
