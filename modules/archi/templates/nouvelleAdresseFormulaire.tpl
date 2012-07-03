{jsLongitudeLatitude}

<form action="{formAction}" name="formAjoutNouvelleAdresse" id='formAjoutNouvelleAdresse' enctype='multipart/form-data' method='POST'>
<p><?_("Vous voulez ajouter un(e) :")?>&nbsp;

<!-- BEGIN liensAdmin -->
<a href='{urlNewRue}'><?_("rue")?></a> &nbsp;
<a href='{urlNewSousQuartier}'><?_("sous-quartier")?></a>&nbsp;
<a href='{urlNewQuartier}'><?_("quartier")?></a>&nbsp;
<a href='{urlNewVille}'><?_("ville")?></a>&nbsp;
<a href='{urlNewPays}'><?_("pays")?></a>&nbsp;
<!-- END liensAdmin -->


<!-- BEGIN liensModerateur -->
<a href='{urlNewRue}'><?_("rue")?></a> &nbsp;
<a href='{urlNewSousQuartier}'><?_("sous-quartier")?></a>&nbsp;
<a href='{urlNewQuartier}'><?_("quartier")?></a>&nbsp;
<!-- END liensModerateur -->


</p>

<div id='choixAdresse'>{afficheChoixAdresse}</div>

<table border=0>
<tr>
<td><?_("Intitule nouvel élément")?> ({typeNouvelElement}):</td><td><input type="text" name="nouvelElement" id='nouvelElement' value="{nouvelElement}"></td>
</tr>
<!-- BEGIN afficheCodePostal -->
<tr>
<td><?_("Code postal :")?></td><td><input type='text' name="codepostal" value="{codepostal}"></td>
</tr>
<!-- END afficheCodePostal -->

<!-- BEGIN afficheComplement -->
<tr>
<td><?_("complément d'adresse :")?></td><td><input type='text' name="complement" value="{complement}"> (ex : 'rue de' ou 'place du' )</td>
</tr>
<!-- END afficheComplement -->
</table>
{champsLongitudeLatitude}
<input type='hidden' value="{typeNouvelElement}" name='typeNouvelElement'>
<br>
<input type='{typeButtonSubmit}' onclick="{onClickButtonSubmit}" value='<?_("Valider")?>'>
</form>
