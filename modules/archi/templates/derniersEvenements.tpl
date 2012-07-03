<h1><?_("Les derniers évènements")?></h1>
<!-- BEGIN t -->
	<table border=1>
	<!-- BEGIN boucle -->
	<tr>
		<td><a href="{t.boucle.url}">{t.boucle.titre}</a></td>
		<td>{t.boucle.description}</td>
	<!-- END boucle -->
	</table>
<!-- END t -->
<!-- BEGIN aucunResult -->
<p><strong>{aucunResult.message}</strong></p>
<!-- END aucunResult -->
