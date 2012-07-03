<!-- BEGIN evAsso -->
<div class="evenementAssocies">
<table>
	<caption><?_("Evenements associés")?> {evAsso.typeEvenementAssocie}</caption>
	<tr><th><?_("Titre")?></th><th><?_("Type de structure")?></th><th><?_("Type d'évenemement")?></th></tr>
	<!-- BEGIN associe -->
	<tr><td><a href="{evAsso.associe.url}">{evAsso.associe.titre}</a></td><td>{evAsso.associe.typeStructure}</td><td>{evAsso.associe.typeEvenement}</td></tr>
	<!-- END associe  -->
</table>
</div>
<!-- END evAsso -->
{msgNoEvenement}
