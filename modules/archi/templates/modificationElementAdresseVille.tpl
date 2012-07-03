<?_("Modification d'un élément d'adresse de type")?> '{typeElement}'
{jsGoogleMap}
<form name='formModif' id='formModif' action='{formAction}' enctype='multipart/form-data' method='POST'>
<input type='hidden' value='{idVille}' name='idVille'>
<table border=1>
<tr><td><?_("Pays")?></td><td id='champPays'>{paysField}</td></tr>
<tr><td><?_("Intitule ville")?></td><td><input type='text' value="{intitule}" name="intitule" id='intituleVille'></td></tr>
<tr><td><?_("Code postal")?></td><td><input type='text' value="{codePostal}" name="codePostal"></td></tr>
</table>
<input type='text' name='longitude' value='{longitude}' id='longitude'>
<input type='text' name='latitude' value='{latitude}' id='latitude'>
<input type='{typeButtonModifier}' name='modifier' value='Modifier' onclick="{onClickBoutonModifier}"><input type='button' name='retour' value='retour' onclick="{onClickBoutonRetour}">
</form>
