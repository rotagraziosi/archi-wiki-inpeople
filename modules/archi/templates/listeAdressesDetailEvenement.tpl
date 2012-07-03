	<h2><?_("Adresses :")?></h2>
	<table cellspacing=0 cellpadding=0 border=0>
	<tr>
	<!-- BEGIN carteGoogleMap -->
	<td>
		{carteGoogleMap.html}
	</td>
	<!-- END carteGoogleMap -->
	<td>
	<div style='padding-top:5px;padding-left:5px;padding-right:5px;'>
	<!-- BEGIN t -->
			{titreAdresses}
		<!-- BEGIN adresses -->
			<a href="{t.adresses.urlDetailHref}" onclick="{t.adresses.urlDetailOnClick}" style='{t.adresses.styleAdresses}'>{t.adresses.nom}</a> <br>
			<!--
			<!-- BEGIN isImagesLiees -->
			<table>
			<tr>
			<!-- BEGIN images -->
			<td>
					<div><img src='{t.adresses.isImagesLiees.images.url}'></div>
			</td>
			<!-- END images -->
			</tr>
			</table>
			<!-- END isImagesLiees -->
			-->
		<!-- END adresses -->
	<!-- END t -->
	</div>
	<td>
	</tr>
	</table>	
