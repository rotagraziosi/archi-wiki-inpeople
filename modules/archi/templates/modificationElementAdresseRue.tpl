{onglets}
<br><br>
<?_("Modification d'un élément d'adresse de type")?> '{typeElement}'
<form name='formModif' id='formModif' action='{formAction}' enctype='multipart/form-data' method='POST'>
<input type='hidden' value='{idRue}' name='idRue'>
<table border=1>
<tr><td><?_("Pays")?></td><td id='champPays'>{paysField}</td></tr>
<tr><td><?_("Ville")?></td><td id='champVille'>{villeField}</td></tr>
<tr><td><?_("Quartier")?></td><td id='champQuartier'>{quartierField}</td></tr>
<tr><td><?_("Sous-quartier")?></td><td id='champSousQuartier'>{sousQuartierField}</td></tr>
<tr><td><?_("Intitulé")?></td><td><input type='text' value="{intitule}" name="intitule"></td></tr>
<tr><td><?_("Complément d'adresse")?></td><td><input type='text' value="{complement}" name="complement"></td></tr>
</table>
<input type='submit' name='modifier' value='Modifier' onclick="{onClickBoutonModifier}"><input type='button' name='retour' value='retour' onclick="{onClickBoutonRetour}">
</form>
