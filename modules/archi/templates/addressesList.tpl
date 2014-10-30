<h1>{titre}</h1>

{nbReponses}
<a href="{urlPrecedent}" onclick="{urlPrecedentOnClick}">&lt;</a>
<!-- BEGIN nav -->
	<!-- BEGIN courant -->
		<strong>
	<!-- END courant -->
	<a href="{nav.urlNb}" onclick="{nav.urlNbOnClick}">{nav.nb}</a>
	<!-- BEGIN courant -->
		</strong>
	<!-- END courant -->
<!-- END nav -->

<a href="{urlSuivant}" onclick="{urlSuivantOnClick}">&gt;</a>

<table class="results">
	<tr>
	<!-- BEGIN liens -->
		<td>
			<a href="{liens.url}" onclick="{liens.urlOnClick}">{liens.titre}</a> 
			<a href="{liens.urlDesc}" onclick="{liens.urlDescOnClick}"><img src="images/Advisa/balisebas.png" alt="&darr;" /></a> 
			<a href="{liens.urlAsc}" onclick="{liens.urlAscOnClick}"><img src="images/Advisa/balisehaut.png" alt="&uarr;" /></a>
			</td>
	<!-- END liens -->
	</tr>
	
	<!-- BEGIN adresses -->
	<tr class="listAddressItem">
        <td>
        	<a href="{adresses.urlDetailHref}">
        		<img src='{adresses.urlImageIllustration}' border=0 alt="{adresses.alt}" title="{adresses.alt}">
        	</a> 
        	<span>
        	<br/>
	        	<a href="{adresses.urlDetailHref}" onclick="{adresses.urlDetailOnClick}">
	        		{adresses.nom}
	        	</a>
        	</span>
        	<br/>
        	<span style='font-size:11px;'>
        		{adresses.titresEvenements}
        	</span>
        </td>
	</tr>
	<!-- END adresses -->
</table>
<br />

