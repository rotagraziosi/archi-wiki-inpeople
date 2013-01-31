<script type='text/javascript'>
//<![CDATA[ 
	function getIdChoixEvenementAndSendAjax($elementId)
	{
		retour = '&motcle='+document.forms['formRechercheEvenement'].elements['motcle'].value+
			'&source='+document.forms['formRechercheEvenement'].elements['source'].value+
			'&typeStructure='+document.forms['formRechercheEvenement'].elements['typeStructure'].value+
			'&typeEvenement='+document.forms['formRechercheEvenement'].elements['typeEvenement'].value+
			'&rechercher=Rechercher';
		retour += getElementsListe('formRechercheEvenement', 'courant[]');
		retour += getElementsListe('formRechercheEvenement', 'personnes[]');
		appelAjax('{boutonRecherche}'+retour,$elementId);
	}
//]]>
</script>

<div id='calqueEvenement' style='display:none;position:absolute; top:50px; left:100px;'>
	<input type='hidden' name='paramChampsAppelantEvenement' id='paramChampsAppelantEvenement' value='' />
	<table>
	<tr>
		<td><input type="button" name="Fermer" value="Fermer" onclick="document.getElementById('calqueEvenement').style.display='none';"></td>
	</tr>
	<tr>
		<td id='rechercheEvenement'><div id='choixEvenement'><iframe frameborder=0 width="600" height="500" src='{iframeSrc}'></iframe></div></td><!-- {contenuCalque} -->
	</tr>
	<tr>
		<td>
			<div id='resultatsEvenement'></div>
		</td>
	</tr>
	</table>
</div>
