<script type='text/javascript'>
//<![CDATA[
	function getIdChoixAdresseAndSendAjax($elementId)
	{
		retour = '&motcle='+document.forms['formRechercheAdresse'].elements['motcle'].value+
			'&pays='+document.forms['formRechercheAdresse'].elements['motcle'].value+
			'&ville='+document.forms['formRechercheAdresse'].elements['ville'].value+
			'&quartier='+document.forms['formRechercheAdresse'].elements['quartier'].value+
			'&sousQuartier='+document.forms['formRechercheAdresse'].elements['sousQuartier'].value+
			'&rue='+document.forms['formRechercheAdresse'].elements['rue'].value+
			'&rechercher=Rechercher';
		alert(retour);
		appelAjax('{boutonRecherche}'+retour,$elementId);
	}
//]]>
</script>

<div id='calqueAdresse' style='display:none;background-color:#C0C0C0;position:absolute;top:50px; left:100px;'>
	<input type='text' name='paramChampsAppelantAdresse' id='paramChampsAppelantAdresse' value='' />
	<table>
	<tr>
		<td><input type="button" value="Fermer" name="Fermer" onclick="document.getElementById('calqueAdresse').style.display='none';"></td>
	</tr>
	<tr>
		<td id='rechercheAdresse'><iframe frameborder=0 width="600" height="500" src='{iframeSrc}'></iframe></td> <!-- {contenuCalque} -->
	</tr>
	<tr>
		<td>
			<div id='resultatsAdresse'></div>
		</td>
	</tr>
	</table>
</div>
