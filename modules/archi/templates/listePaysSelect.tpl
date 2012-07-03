<script  >
{javascript}
</script>
<select name='pays{identifiantUnique}' id='pays{identifiantUnique}' onchange="{onChangeListePays}">
<option value='0'>Aucun</option>
<!-- BEGIN pays -->
<option value='{pays.id}' {pays.selected}>{pays.nom}</option>
<!-- END pays -->
</select>