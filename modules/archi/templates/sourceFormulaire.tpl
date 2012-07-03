<h1><?_("Ajouter une source")?></h1>

<p><?_("Vous pouvez ajouter une source ici, si vous entrez un type de source dans le champ approprié, vous ajouterez un nouveau type de source.")?></p>

<form action="{urlAction}" name="ajoutSource" method="post">
<table>
<tr>
	<td><?_("Nom")?></td><td><input type="text" name="nom" value="{nom}" /></td><td>{nom-error}</td>
</tr>
<tr>
	<td><?_("Description")?></td><td><textarea name="description" rows="5" cols="35" >{description}</textarea></td><td>{description-error}</td>
</tr>
<tr>
	<td><?_("Type de source")?></td><td><select name="type">
	<!-- BEGIN type -->
		<option value="{type.valeur}">{type.nom}</option>
	<!-- END type -->
	</select></td><td>{type-error}</td>
</tr>
<!-- BEGIN allowNewType -->
<tr>
	<td><?_("Nouveau type de source")?></td><td><input type="text" name="nomNouveauType" value="{nomNouveauType}" /></td><td>{nomNouveauType-error}</td>
</tr>
<!-- END allowNewType -->
<tr>
	<td></td><td><input type='button' name='annuler' value='Annuler' onclick="{boutonAnnulation}"><input type="submit" name="submit" value="Créer la source" /></td><td></td>
</tr>
</table>
</form>
