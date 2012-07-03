<h1><?_("Ajouter une adresse")?></h1>
<form action="" name='formAdresse' id='formAdresse' method='post' enctype='multipart/form-data'>
<table>
<tr>
	<td><?_("Intitule :"?></td><td><input type='text' name='nom' value='{nom}' /></td><td>{nom-error}</td>
</tr>
<tr>
	<td><?_("Date :")?></td><td><input type='text' name='date' value='{date}' /><a href="javascript:show_calendar('document.formAdresse.date', document.formAdresse.date.value,'{date}');"><img src="images/cal.gif" width="16" height="16" alt="Cliquez ici pour définir une date" title="Cliquez ici pour définir une date" /></a></td><td>{date-error}</td>
</tr>
<tr>
	<td colspan="3"><input type="button" value="b" style="width:50px;font-weight:bold" onclick="bbcode_ajout_balise('b', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" />
<input type="button" value="i" style="width:50px;font-style:italic" onclick="bbcode_ajout_balise('i', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" />
<input type="button" value="u" style="width:50px;text-decoration:underline" onclick="bbcode_ajout_balise('u', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" />
<input type="button" value="quote"style="width:50px" onclick="bbcode_ajout_balise('quote', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" />
<input type="button" value="code"style="width:50px"  onclick="bbcode_ajout_balise('code', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" />
<input type="button" value="url"style="width:50px"   onclick="bbcode_ajout_balise('url', 'formAdresse', 'description');bbcode_keyup(this,'apercu');" /></td></tr>
<tr>
	<td><?_("Description :")?></td><td><textarea cols="45" rows="15" name='description' onkeyup="bbcode_keyup(this,'apercu');">{description}</textarea></td><td>{description-error}</td>
</tr>
<tr>
	<td><?_("Aperçu")?></td><td><div id='apercu'></div></td><td></td>
</tr>
<tr>
	<td><?_("Etage :")?></td><td><input type='text' name='etage' value='{etage}' /></td><td>{etage-error}</td>
</tr>
<tr>
	<td><?_("Numéro :")?></td><td><input type='text' name='numero' value='{numero}' /></td><td>{numero-error}</td>
</tr>
<tr>
	<td><?_("ISMH :")?></td><td><input type='text' name='ISMH' value='{ISMH}' /></td><td>{ISMH-error}</td>
</tr>
<tr>
	<td><?_("MH :")?></td><td><input type='text' name='MH' value='{MH}' /></td><td>{MH-error}</td>
</tr>
<tr>
<td><?_("Choix de l'adresse")?></td><td id='choixAdresse' style='background-color:lime;'></td>
	<td colspan='2'>
	{pays-error}<br />
	{ville-error}<br />
	{quartier-error}<br />
	{sousQuartier-error}<br />
	{rue-error}
	</td>
</tr>
</table>
<input type='hidden' name='idAdresseModification' value='{idAdresseModification}' />
<input type='submit' name='submit' value='{boutonSubmit}' />
</form>
{appelAjaxJavascript}
<script type="text/javascript" >bbcode_keyup (document.forms['formAdresse'].elements['description'], 'apercu');</script>

