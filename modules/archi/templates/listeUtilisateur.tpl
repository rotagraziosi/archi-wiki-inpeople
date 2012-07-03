<h1><?_("Liste des utilisateurs")?></h1>

{recherche}

{infosStatistiques}
{pagination}
<table>
<tr>
	<th style='border-bottom: 1px solid #000000;'><a {urlTriNomPrenom}><?_("Utilisateurs")?></a></th>
	<th style='border-bottom: 1px solid #000000;'><a {urlTriNbConnexions}><?_("Nombre de connexions")?></a></th>
	<th style='border-bottom: 1px solid #000000;'><a {urlTriNbParticipations}><?_("Nombre de participations")?></a></th>
	<th style='border-bottom: 1px solid #000000;'><a {urlTriDernieresConnexions}><?_("Date de la dernière connexion")?></a></th>
	<th style='border-bottom: 1px solid #000000;'><a {urlTriDateCreation}><?_("Date de création du compte")?></a></th>

</tr>
<!-- BEGIN l -->
<tr>
	<th><a href="{l.url}">{l.nom} {l.prenom}</a></th>
	<th>{l.nbConnexions}</th>
	<th>{l.nbParticipationsEvenements}</th>
	<th>{l.derniereConnexion}</th>
	<th>{l.dateCreation}</th>

</tr>
<!-- END l -->
</table>
{erreur}
