<h1>{titre}</h1>
<!-- BEGIN useFormElements -->
<form action="{formAction}" name="formRechercheAdresse" method="get">
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
<input type="hidden" name="archiAffichage" value="rechercheAvAdressePopup" >
<input type="hidden" name='modeAffichage' value="{modeAffichage}">
<!-- END isCalque -->
<!-- BEGIN noCalque -->
<input type="hidden" name="archiAffichage" value="rechercheAvAdresse" >
<!-- END noCalque -->
<table>

<!-- BEGIN afficheRechercheMotCle -->
<tr>
	<td><?_("Recherche par mot clé")?></td><td><input type="text" value="{motcle}" name="motcle" /></td><td>{motcle-error}</td>
</tr>
<!-- END afficheRechercheMotCle -->
<tr>
	<td><?_("Adresse")?></td><td id="choixAdresse">{formulaireChoixAdresse}</td><td></td>
</tr>
<!-- BEGIN useFormElements -->
<tr>
	<td></td><td><input type="submit" value="Rechercher" name="submit" /></td><td></td>
</tr>
<!-- END useFormElements -->
</table>
<!-- BEGIN useFormElements -->
</form>
<!-- END usrFormElements -->
