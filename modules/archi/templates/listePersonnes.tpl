<script  >
	{codeJavascriptReturnNewElementAjoute}
</script>

<div style='float:left;'>
{listeAlphabetique}
{pagination}
</div>
<div style='float:left;padding-left:5px;'>
<form action='{urlRecherchePersonne}' name='formRecherchePersonne' id='formRecherchePersonne' method='GET' enctype='multipart/form-data'>
	<input type='text' name='motCle' id='motCle' value='' style='width:70px;'>
	<input type='submit' name='validRecherche' value='OK'>
	<input type='hidden' name='noHeaderNoFooter' value='1'>
	<input type='hidden' name='modeAffichage' value='{modeAffichage}'>
	<input type='hidden' name='archiAffichage' value='{archiAffichage}'>
</form>
</div>
<div style='clear:both;'></div>
<table border=0 cellspacing=0 cellpadding=0>
<caption><?_("Personnes")?></caption>
<tr><th><?_("Nom")?></th><th><?_("Prénom")?></th><th><?_("Métier")?></th></tr>
<!-- BEGIN personne -->
	<tr><td><a href="{personne.url}" onclick="{personne.onclick}">{personne.nom}</a></td><td><a href="{personne.url}">{personne.prenom}</a></td><td>{personne.metier}</td></tr>
<!-- END personne -->
</table>

<table>
<tr>
<!-- BEGIN pages -->
<td><a href='{pages.url}'>{pages.page}</a></td>
<!-- END pages -->
</tr>
</table>

<a href="{urlAjout}"><?_("Ajouter une personne")?></a>
<!-- BEGIN noPersonne -->
<?_("Il n'y a pas d'enregistrement")?>
<!-- END noPersonne -->
