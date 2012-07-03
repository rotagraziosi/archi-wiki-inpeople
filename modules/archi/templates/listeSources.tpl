<script  >
		{codeJavascriptReturnNewElementAjoute}
</script>

<br>
Type de source :
<form action='{formAction}' name='formSource' id='formSource' method='POST' enctype='multipart/form-data'>
<table cellspacing=0 cellpadding=0 border=0>
<tr>
<td width=250>
	<select name="archiTypeSource" id='archiTypeSource' onchange='submit();'>
	<option value="0"><?_("Toutes")?></option>
	<!-- BEGIN typeSources -->
	<option value="{typeSources.id}" {typeSources.selected}>{typeSources.nom}</option>
	<!-- END typeSources -->
	</select>
</td>
<td>
	<nobr> <span style='font-size:12px;'><?_("Recherche :")?></span> <input style='width:70px;' type='text' name='archiMotCleRechercheSource' id='archiMotCleRechercheSource' value="{motCleRechercheSource}" onkeypress="if(event.keyCode==13){this.form.action+='&motCle='+document.getElementById('archiMotCleRechercheSource').value;this.form.submit();}"><input type='button' name='buttonRechercheMotCleSource' id='buttonRechercheMotCleSource' value='Ok' onclick="this.form.action+='&motCle='+document.getElementById('archiMotCleRechercheSource').value;this.form.submit();"></nobr>
</td>
</tr>
</table>
</form>

{listeAlphabetique}


<table border=0 cellspacing=0 cellpadding=0>
<tr>
<td>
<a href='{pagePrecedente}'>&lt;</a>
</td>
<!-- BEGIN pages -->
<td><a href='{pages.url}'>{pages.page}</a></td>
<!-- END pages -->
<!-- BEGIN nopage -->
<td>1</td>
<!-- END nopage -->
</td>
<td>
<a href='{pageSuivante}'>&gt;</a>
</td>
</tr>
</table>

<table border=0 cellspacing=2 cellpadding=0>
<tr><td><?_("Nom")?></td><td><?_("Type")?></td></tr>
<!-- BEGIN sources -->
<tr>
<td><a href="{sources.url}" onclick="{sources.onclick}">{sources.nom}</td><td>{sources.typeSource}</td>
</tr>
<!-- END sources -->
</table>



<!-- BEGIN isAuthorizedAjoutSource -->
<a href="{urlAjout}"><?_("Ajouter une source")?></a>
<!-- END isAuthorizedAjoutSource -->

<!-- BEGIN noSource -->
<?_("Il n'y a pas d'enregistrement.")?>
<!-- END noSource -->
