<script  >
	{javascript}
</script>
<select name='ville{identifiantUnique}' id='ville{identifiantUnique}' onChange="{onChangeListeVille}">
<option value='0'>Aucun</option>
<!-- BEGIN villes -->
<option value='{villes.id}' {villes.selected}>{villes.nom}</option>
<!-- END villes -->
</select>