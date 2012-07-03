<h1><?_("Évènement")?></h1>
<!-- BEGIN t -->
	( {t.nbReponses} ) <?_("réponses :")?>
	<a href="{t.urlPrecedent}" onclick="{t.urlPrecedentOnClick}">&lt;</a>
	<!-- BEGIN nav -->
		<!-- BEGIN courant -->
		<strong>
		<!-- END courant -->
		<a href="{t.nav.urlNb}" onclick="{t.nav.urlNbOnClick}">{t.nav.nb}</a>
		<!-- BEGIN courant -->
		</strong>
		<!-- END courant -->
	<!-- END nav -->
	<a href="{t.urlSuivant}" onclick="{t.urlSuivantOnClick}">&gt;</a>
	<table>
	<tr>
	<!-- BEGIN liens -->
		<th><a href="{t.liens.url}" onclick="{t.liens.urlOnClick}">{t.liens.titre}</a> <a href="{t.liens.urlDesc}" onclick="{t.liens.urlDescOnClick}">&darr;</a> <a href="{t.liens.urlAsc}" onclick="{t.liens.urlAscOnClick}">&uarr;</a></th>
	<!-- END liens -->
	</tr>
	<!-- BEGIN boucle -->
	<tr>
		<td><a href="{t.boucle.url}" onclick="{t.boucle.urlOnClick}">{t.boucle.titre}</a></td>
		<td>{t.boucle.description}</td>
		<td><a href="{t.boucle.urlType}" onclick="{t.boucle.urlTypeOnClick}">{t.boucle.type}</a></td>
		<td><a href="{t.boucle.urlStructure}" onclick="{t.boucle.urlStructureOnClick}">{t.boucle.structure}</a></td>
		<td><a href="{t.boucle.urlSource}" onclick="{t.boucle.urlSourceOnClick}">{t.boucle.source}</a></td></tr>
	<!-- END boucle -->
	</table>
<!-- END t -->
<!-- BEGIN aucunResult -->
<p><strong>{aucunResult.message}</strong></p>
<!-- END aucunResult -->
<a href="{ajouterEvenement}"><?_("Ajouter un évènement")?></a>
