
<!-- BEGIN displayTitre -->
<h1><?_("Recherche avancée d'évènement")?></h1>
<!-- END displayTitre -->
<!-- BEGIN useFormElements -->
<form action="{formAction}" name="formRechercheEvenement" method="GET">
<!-- END useFormElements -->
<!-- BEGIN noHeaderNoFooter -->
<input type='hidden' value="1" name='noHeaderNoFooter'>
<!-- END noHeaderNoFooter -->

<!-- BEGIN modeAffichage -->
<input type="hidden" name='modeAffichage' value="{modeAffichage.value}">
<!-- END modeAffichage -->

<!-- BEGIN archiAffichage -->
<input type="hidden" name="archiAffichage" value="{archiAffichage.value}" >
<!-- END archiAffichage -->


<!-- BEGIN isCalque -->
<input type='hidden' value="1" name='noHeaderNoFooter'>
<input type="hidden" name='modeAffichage' value="{modeAffichage}">
<input type='hidden' value="rechercheAvEvenementPopup" name="archiAffichage">
<!-- END isCalque -->
<!-- BEGIN noCalque -->
<input type='hidden' value="rechercheAvEvenement" name="archiAffichage">
<!-- END noCalque -->
<table>
<!-- BEGIN afficheRechercheMotCle -->
<tr>
	<td><?_("Recherche par mot clé")?></td><td><input type="text" value="{motcle}" name="motcle" /></td><td>{motcle-error}</td>
</tr>
<!-- END afficheRechercheMotCle -->
<tr><td><?_("Source")?></td><td><select name="source"><option value="0"><?_("Toutes")?></option>
	<!-- BEGIN source -->
	<option value="{source.val}" {source.selected}>{source.nom}</option>
	<!-- END source -->
	</select>
	</td><td>{source-error}</td></tr>
<tr><td><?_("Type de structure")?></td><td><select name="typeStructure"><option value="0"><?_("Toutes")?></option>
	<!-- BEGIN struct -->
	<option value="{struct.val}" {struct.selected}>{struct.nom}</option>
	<!-- END struc -->
	</select>
	</td><td>{typeStructure-error}</td></tr>
<tr><td><?_("Type d'évènement")?></td><td><select name="typeEvenement"><option value="0">Tous</option>
	<!-- BEGIN evenement -->
	<option value="{evenement.val}" {evenement.selected}>{evenement.nom}</option>
	<!-- END evenement -->
	</select>
	</td><td>{typeEvenement-error}</td></tr>
<tr><td><?_("Courant architectural")?></td><td>
	{listeCourantsArchitecturaux}
	</td><td>{courant-error}</td></tr>
<tr><td><?_("Intervalle d'années")?></td><td>
 de <input type='text' name='anneeDebut' value='{anneeDebut}'> <?_("à")?> <input type='text' name='anneeFin' value='{anneeFin}'>
 </td><td></td>{anneeDebut-error} {anneeFin-error}</tr>
	
<tr>
<td><?_("MH (classé)")?></td>
<td><input type='checkbox' name='MH' value='1' {isMH}></td>
<td>{MH-error}</td>
</tr>
<tr>
<td nowrap><?_("ISMH (inscrit)")?></td>
<td><input type='checkbox' name='ISMH' value='1' {isISMH}></td>
<td>{ISMH-error}</td>
</tr>

<!--<tr><td>Personnages / personnes</td><td><select name="personnes[]" multiple="multiple" ><option value="aucune">Aucune</option>
	<!-- BEGIN personne -->
	<option value="{personne.val}" {personne.selected}>{personne.nom}</option>
	<!-- END personne -->
	</select>
	</td><td>{personnes-error}</td></tr>
<tr>-->
<!-- BEGIN useFormElements -->
	<td></td><td><input type="submit" value="Rechercher" name="submit" /></td><td></td>
<!-- END useFormElements -->
</tr>
</table>
<!-- BEGIN useFormElements -->
</form>
<!-- END useFormElements -->
