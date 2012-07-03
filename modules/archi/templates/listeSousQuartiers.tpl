<select name='sousQuartiers{identifiantUnique}' id='sousQuartiers{identifiantUnique}' onchange="{onChangeListeSousQuartiers}">
<option value='0'>Aucun</option>
<!-- BEGIN sousQuartiers -->
<option value='{sousQuartiers.id}' {sousQuartiers.selected}>{sousQuartiers.nom}</option>
<!-- END sousQuartiers -->
</select>