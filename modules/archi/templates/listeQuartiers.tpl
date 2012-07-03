<script  >
	{javascript}
</script>
<select name='quartiers{identifiantUnique}' id='quartiers{identifiantUnique}' onchange="{onChangeListeQuartier}">
<option value='0'>Aucun</option>
<!-- BEGIN quartiers -->
<option value='{quartiers.id}' {quartiers.selected}>{quartiers.nom}</option>
<!-- END quartiers -->
</select>