<h1><?_("Ajouter une personne")?></h1>
<p><?_("Vous pouvez ajouter une personne ici.")?></p>
<p><?_("Remplissez le champ métier pour ajouter un nouveau métier. Sinon, prenez-en un existant.")?></p>
<form action="{urlAction}" name="ajoutPersonne" method="post">
<table>
<tr>
	<td><?_("Nom")?></td><td><input type="text" name="nom" value="{nom}" /></td><td>{nom-error}</td>
</tr>
<tr>
	<td><?_("Prénom")?></td><td><input type="text" name="prenom" value="{prenom}" /></td><td>{prenom-error}</td>
</tr>
<tr>
	<td><?_("Métier")?></td><td><select name="metier">
	<!-- BEGIN metier -->
		<option value="{metier.valeur}">{metier.nom}</option>
	<!-- END metier -->
	</select></td><td>{metier-error}</td>
</tr>
<!-- BEGIN allowAjouterMetier -->
<tr>
	<td><?_("Ajouter un métier")?></td><td><input type="text" name="nouveauMetier" value="{nouveauMetier}" /></td><td>{nouveauMetier-error}</td>
</tr>
<!-- END allowAjouterMetier -->
<tr>
	<td><?_("Date de naissance")?></td><td><input type="text" name="dateNaissance" value="{dateNaissance}" /><a href="javascript:show_calendar('document.ajoutPersonne.dateNaissance', document.ajoutPersonne.dateNaissance.value,'{dateNaissance}');"><img src="images/cal.gif" width="16" height="16" alt="Cliquez ici pour définir une date" title="Cliquez ici pour définir une date" /></a></td><td>{dateNaissance-error}</td>
</tr>
<tr>
	<td><?_("Date de décès")?></td><td><input type="text" name="dateDeces" value="{dateDeces}" /><a href="javascript:show_calendar('document.ajoutPersonne.dateDeces', document.ajoutPersonne.dateDeces.value,'{dateDeces}');"><img src="images/cal.gif" width="16" height="16" alt="Cliquez ici pour définir une date" title="Cliquez ici pour définir une date" /></a></td><td>{dateDeces-error}</td>
</tr>
<tr>
	<td><?_("Description")?></td><td><textarea name="description" rows="15" cols="35" >{description}</textarea></td><td>{description-error}</td>
</tr>
<tr>
	<td></td><td><input type='button' name='annulation' value='Annuler' onclick="{boutonAnnulation}"><input type="submit" name="submit" value="Créer la personne" /></td><td></td>
</tr> 
</table>
</form>
