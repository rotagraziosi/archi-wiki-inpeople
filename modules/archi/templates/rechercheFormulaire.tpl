<h1><?_("Recherche générale")?></h1>
<form action="{formAction}" name="formRecherche" method="get">
<table>
<caption><?_("Formulaire")?></caption>
<tr>
	<td><?_("Recherche par mot clé")?></td><td><input type="text" value="{motcle}" name="motcle" /></td><td>{motcle-error}</td>
</tr>
<tr><td><?_("Source")?></td><td><select name="source"><option value="0"><?_("Aucune")?></option>
	<!-- BEGIN source -->
	<option value="{source.val}" {source.selected}>{source.nom}</option>
	<!-- END source -->
	</select>
	</td><td>{source-error}</td></tr>
<tr><td><?_("Type de structure")?></td><td><select name="typeStructure"><option value="0"><?_("Aucun")?></option>
	<!-- BEGIN struct -->
	<option value="{struct.val}" {struct.selected}>{struct.nom}</option>
	<!-- END struc -->
	</select>
	</td><td>{typeStructure-error}</td></tr>
<tr><td><?_("Type d'évènement")?></td><td><select name="typeEvenement"><option value="0"><?_("Aucun")?></option>
	<!-- BEGIN evenement -->
	<option value="{evenement.val}" {evenement.selected}>{evenement.nom}</option>
	<!-- END evenement -->
	</select>
	</td><td>{typeEvenement-error}</td></tr>
<tr>
	<td><?_("Adresse")?></td><td id="choixAdresse">{formulaireChoixAdresse}</td><td></td>
</tr>
<tr>
	<td></td><td><input type="submit" value="<?_("Recherche")?>" name="submit" /></td><td></td>
</tr>
</table>
</form>
