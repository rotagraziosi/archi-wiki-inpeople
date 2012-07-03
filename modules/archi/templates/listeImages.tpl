<h2><?_("Images")?></h2>
( {nbReponses} ) <?_("réponses :")?>
<!-- BEGIN pages -->
	<a href="{pages.url}" onclick="{pages.urlOnClick}">{pages.page}</a>
<!-- END pages -->
<table>
<caption>Liste</caption>
<tr>
<!-- BEGIN liens -->
	<th><a href="{liens.url}" onclick="{liens.urlOnClick}">{liens.titre}</a> <a href="{liens.urlDesc}" onclick="{liens.urlDescOnClick}">&darr;</a> <a href="{liens.urlAsc}" onclick="{liens.urlAscOnClick}">&uarr;</a></th>
<!-- END liens -->
</tr>
<!-- BEGIN image -->
<tr>
	<td><a href="{image.url}"><img src="{image.urlImage}" title="{image.nom}" alt="{image.description}" /></a></td>
	<td>{image.nom}</td>
	<td>{image.description}</td>
	<td>{image.source}</td>
</tr>
<!-- END image -->
</table>
<a href='{lienAjoutImage}'><?_("Ajouter une image dans la bibliothèque")?></a>
