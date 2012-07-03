<?_("Modification d'un élément d'adresse de type")?> '{typeElement}'
<form name='formModif' id='formModif' action='{formAction}' enctype='multipart/form-data' method='POST'>
<input type='hidden' value='{idQuartier}' name='idQuartier'>
<table border=1>
<tr><td><?_("Pays")?></td><td id='champPays'>{paysField}</td></tr>
<tr><td><?_("Ville")?></td><td id='champVille'>{villeField}</td></tr>
<tr><td><?_("Intitulé quartier")?></td><td><input type='text' value="{intitule}" name="intitule"></td></tr>
</table>
<input type='submit' name='modifier' value='Modifier' onclick="{onClickBoutonModifier}"><input type='button' name='retour' value='retour' onclick="{onClickBoutonRetour}">
</form>
