<div class="part_one">

<!-- BEGIN CarteGoogle -->
	<div class="map_block">
	<iframe
					src='{CarteGoogle.src}' id='iFrameGoogleMap'
					style='width: 275px; height: 275px;' itemprop='map'></iframe><br>{CarteGoogle.lienVoirCarteGrand}{CarteGoogle.popupGoogleMap}</td>
	</div>
<!-- END CarteGoogle -->

<!-- BEGIN listeAdressesVoisines -->
	<div class="listAdressesVoisines">
	<!-- BEGIN adresseVoisine -->
		<div class="adresseVoisine">
			<img alt="" src="{listeAdressesVoisines.adresseVoisine.urlImg}">
			<p>{listeAdressesVoisines.adresseVoisine.intitule}</p>
		</div>
	<!-- END adresseVoisine -->
	</div>
<!-- END adressesVoisines -->
</div>
	
<div class="part_two">
<!-- BEGIN sommaireEvenements -->
	<div class="sommaireEvenements">
		{sommaireEvenements.content}
	</div>
	<div class="actionsEvenement">
		<!-- BEGIN buttonAction -->
		{sommaireEvenements.buttonAction.actions}
		<!-- END buttonAction -->
	</div>
<!-- END sommaireEvenements -->
</div>	

<div class="detailEvenements">
	{listeEvenements}
</div>