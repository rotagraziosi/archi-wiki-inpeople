<table>
<tr>
	<td><?_("Intitulé")?></td><td><?_("Valeurs")?></td>
</tr>
<!-- BEGIN isPays -->
<tr>
	<td><?_("Pays")?></td>
	<td>
		<select name='pays' id='pays' onchange="{paysOnChange}">
			<option value='0'><?_("Tous")?></option>
			<!-- BEGIN listePays -->
			<option value='{isPays.listePays.idPays}' {isPays.listePays.selected}>{isPays.listePays.nomPays}</option>
			<!-- END listePays -->
		</select>
	</td>
</tr>
<!-- END isPays -->
<!-- BEGIN isVille -->
<tr>
	<td><?_("Ville")?></td>
	<td>
		<select name='ville' id='ville' onchange="{villeOnChange}">
			<option value='0'><?_("Toutes")?></option>
			<!-- BEGIN listeVilles -->
			<option value='{isVille.listeVilles.idVille}' {isVille.listeVilles.selected}>{isVille.listeVilles.nomVille}</option>
			<!-- END listeVilles -->
		</select>
	</td>
</tr>
<!-- END isVille -->
<!-- BEGIN isQuartier -->
<tr>
	<td><?_("Quartier")?></td>
	<td>
		<select name='quartier' id='quartier' onchange="{quartierOnChange}">
			<option value='0'><?_("Tous")?></option>
			<!-- BEGIN listeQuartiers -->
			<option value='{isQuartier.listeQuartiers.idQuartier}' {isQuartier.listeQuartiers.selected}>{isQuartier.listeQuartiers.nomQuartier}</option>
			<!-- END listeQuartiers -->
		</select>
	</td>
</tr>
<!-- END isQuartier -->
<!-- BEGIN isSousQuartier -->
<tr>
	<td><?_("Sous-quartier")?></td>
	<td>
		<select name='sousQuartier' id='sousQuartier' onchange="{sousQuartierOnChange}">
			<option value='0'><?_("Tous")?></option>
			<!-- BEGIN listeSousQuartiers -->
			<option value='{isSousQuartier.listeSousQuartiers.idSousQuartier}' {isSousQuartier.listeSousQuartiers.selected}>{isSousQuartier.listeSousQuartiers.nomSousQuartier}</option>
			<!-- END listeSousQuartiers -->
		</select>
	</td>
</tr>
<!-- END isSousQuartier -->
<!-- BEGIN isRue -->
<tr>
	<td><?_("Rue")?></td>
	<td>
		<select name='rue' id='rue' onchange="{rueOnChange}">
			<option value='0'><?_("Toutes")?></option>
			<!-- BEGIN listeRues -->
			<option value='{isRue.listeRues.idRue}' {isRue.listeRues.selected}>{isRue.listeRues.nomRue}</option>
			<!-- END listeRues -->
		</select>
	</td>
</tr>
<!-- END isRue -->
</table>
