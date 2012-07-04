{divBegin}
<h1>{titre}</h1>
{description}
<!-- BEGIN t -->	
	
	{t.nbReponses}
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
	<table class="results">
	<tr>
	<!-- BEGIN liens -->
		<td><a href="{t.liens.url}" onclick="{t.liens.urlOnClick}">{t.liens.titre}</a> <a href="{t.liens.urlDesc}" onclick="{t.liens.urlDescOnClick}">&darr;</a> <a href="{t.liens.urlAsc}" onclick="{t.liens.urlAscOnClick}">&uarr;</a></td>
	<!-- END liens -->
	</tr>
	<!-- BEGIN adresses -->
	<tr>
		<td><a href="{t.adresses.urlDetailHref}" onclick="{t.adresses.urlDetailOnClick}">{t.adresses.nom}</a><div style='font-size:11px;'>{t.adresses.titresEvenements}</div></td>
		<td><a href="{t.adresses.urlDetailHref}"><img src='{t.adresses.urlImageIllustration}' border=0 alt="{t.adresses.alt}" title="{t.adresses.alt}"></a></td>
		<!--<td><a href="{t.adresses.urlNomRue}" onclick="{t.adresses.urlNomRueOnClick}">{t.adresses.nomRue}</a></td>
		<td><a href="{t.adresses.urlNomSousQuartier}" onclick="{t.adresses.urlNomSousQuartierOnClick}">{t.adresses.nomSousQuartier}</a></td>
		<td><a href="{t.adresses.urlNomQuartier}" onclick="{t.adresses.urlNomQuartierOnClick}">{t.adresses.nomQuartier}</a></td>
		<td><a href="{t.adresses.urlNomVille}" onclick="{t.adresses.urlNomVilleOnClick}">{t.adresses.nomVille}</a></td>
		<td><a href="{t.adresses.urlNomPays}" onclick="{t.adresses.urlNomPaysOnClick}">{t.adresses.nomPays}</a></td>-->
	</tr>
	<!-- END adresses -->
	</table>
	<br />
<!-- END t -->

{divEnd}
