<!-- BEGIN simple -->
<div class='evenement'
	itemprop='event' itemscope itemtype="http://schema.org/Event"
	style='position: relative; display: table;'>
	{urlEvenementExterne}
	<!-- BEGIN menuAction -->
	<div class="menuAction"
		style="margin-left: 1em; float: right; padding: 5px; display: {divDisplayMenuAction">

		<ul style='margin: 0px; display: table;'>
			<li class='actionEvent'><?_("Ajouter")?></li>
			<li><a href="{ajouterImage}"><?_("Images")?> </a> <!-- | Évènement -->
			</li>
			<li class='actionEvent'><?_("Modifier")?></li>
			<li><a href="{modifierImage}"><?_("Images")?> </a> | <a
				href="{modifierEvenement}"><?_("Évènement")?> </a></li>
			<!-- BEGIN isAdminOrModerateurFromVille -->
			<li class='actionEvent'><?_("Supprimer")?></li>
			<li><a
				onclick="if(confirm('Voulez vous vraiment supprimer cet évènement ?')){location.href='{supprimerEvenement}'};"
				href="#">Évènement</a> <!-- END isAdminOrModerateurFromVille --> <!-- BEGIN isAdmin -->
				<!-- BEGIN isAffichageSelectionImages --> | <a
				onclick="{onClickSupprimerImage}" href="{urlSupprimerImage}"><?_("Images")?>
			</a> <!-- END isAffichageSelectionImages -->
			</li>
			<!-- BEGIN isAffichageSelectionImages -->
			<li class='actionEvent'><?_("Importer")?></li>
			<li><a onclick="{onClickImporterImage}" href="{urlImporterImage}"><?_("Images selectionnées")?>
			</a></li>
			<!-- END isAffichageSelectionImages -->
			<!-- END isAdmin -->
			<!-- BEGIN afficheElementMenuLierAdresse -->
			<li class='actionEvent'><?_("Lier")?></li>
			<li><a href="{urlLierAdresses}"><?_("Adresses")?> </a></li>
			<!-- END afficheElementMenuLierAdresse -->
			<!-- BEGIN afficheElementMenuDeplacerEvenement -->
			<li class='actionEvent'><?_("Déplacer")?></li>
			<li><a href="#" onclick="{onClickDeplacerVersAdresses}"><?_("Vers Adresse")?>
			</a> | <a href="{urlDeplacerVersNouveauGroupeAdresse}">+Créer</a></li>
			<!-- END afficheElementMenuDeplacerEvenement -->
		</ul>

		<ul style='margin: 0px; display: table;'>
			<li class='actionEvent'><?_("Ajouter")?></li>
			<li><a href="{ajouterImage}"><?_("Images")?> </a> <!-- | Évènement -->
			</li>
			<li class='actionEvent'><?_("Modifier")?></li>
			<li><a href="{modifierImage}"><?_("Images")?> </a> | <a
				href="{modifierEvenement}"><?_("Évènement")?> </a></li>
			<!-- BEGIN isAdminOrModerateurFromVille -->
			<li class='actionEvent'><?_("Supprimer")?></li>
			<li><a
				onclick="if(confirm('Voulez vous vraiment supprimer cet évènement ?')){location.href='{supprimerEvenement}'};"
				href="#">Évènement</a> <!-- END isAdminOrModerateurFromVille --> <!-- BEGIN isAdmin -->
				<!-- BEGIN isAffichageSelectionImages --> | <a
				onclick="{onClickSupprimerImage}" href="{urlSupprimerImage}"><?_("Images")?>
			</a> <!-- END isAffichageSelectionImages -->
			</li>
			<!-- BEGIN isAffichageSelectionImages -->
			<li class='actionEvent'><?_("Importer")?></li>
			<li><a onclick="{onClickImporterImage}" href="{urlImporterImage}"><?_("Images selectionnées")?>
			</a></li>
			<!-- END isAffichageSelectionImages -->
			<!-- END isAdmin -->
			<!-- BEGIN afficheElementMenuLierAdresse -->
			<li class='actionEvent'><?_("Lier")?></li>
			<li><a href="{urlLierAdresses}"><?_("Adresses")?> </a></li>
			<!-- END afficheElementMenuLierAdresse -->
			<!-- BEGIN afficheElementMenuDeplacerEvenement -->
			<li class='actionEvent'><?_("Déplacer")?></li>
			<li><a href="#" onclick="{onClickDeplacerVersAdresses}"><?_("Vers Adresse")?>
			</a> | <a href="{urlDeplacerVersNouveauGroupeAdresse}">+Créer</a></li>
			<!-- END afficheElementMenuDeplacerEvenement -->
		</ul>

	</div>
	<!-- END menuAction -->



	<div style='min-height: 150px;'>
		{titre} - {txtEnvoi} par {utilisateur} {dateEnvoi}
		{lienHistoriqueEvenementCourant}



		<div class="event">
			<p>
			
			
			<ul>
				<li>{dates}</li>
				<li>{source}</li>
				<li>{typeStructure}</li>
				<li><?_("Type d'Évènement :")?> <a href="{urlTypeEvenement}">{typeEvenement}</a>
				</li>
			</ul>
			{numeroArchive}
			<!-- BEGIN pers -->
			{simple.pers.metier} <a href="{simple.pers.urlEvenement}">{simple.pers.prenom}
				{simple.pers.nom}</a>
			<!-- BEGIN connected -->
			<!--<small><a href="index.php?archiAffichage=choosePersonEventImage&idPerson={simple.pers.idPerson}&idEvent={simple.pers.idEvent}">(choisir l'image principale)</a></small>-->
			<!-- END connected -->
			<br>
			<!-- END pers -->
			</p>
			<p>{description}</p>
			<!-- BEGIN isCourantArchi -->
			<div class="courantAchitectural">
				<h4>
					<?_("Courant Architectural")?>
					</h3>
					<ul>
						<!-- BEGIN archi -->
						<li><a href="{simple.isCourantArchi.archi.url}">{simple.isCourantArchi.archi.nom}</a>
						</li>
						<!-- END archi -->
					</ul>
			
			</div>
			<!-- END isCourantArchi -->
			<div class="historiqueEvenement">
				<!-- BEGIN menuAction -->
				<!-- Il y a {nbHistorique} historique sur cet Évènement.-->
				<!-- END menuAction -->
				<!-- BEGIN histo -->
				<br /> <a href="{histo.url}"><?_("Voir l'historique")?> </a>
				<!-- END histo -->
			</div>
			{imagesLiees} {evenementsParents} {listeAdressesLiees}
			{evenementsLiesPersonne}
		</div>

	</div>
	<!-- BEGIN commentaireEvenement -->
	<div class="commentaireEvenement">
		{listeCommentaireEvenement} 
		{formulaireCommentaireEvenement} 
	</div>
	<!-- END commentaireEvenement -->
</div>
<!-- END simple -->




<!-- BEGIN noSimple -->
<!-- BEGIN isCarteGoogle -->
<table style='margin-bottom: 0;'>
	<tr>
		<td style='padding: 0; margin: 0;'><iframe
				src='{noSimple.isCarteGoogle.src}' id='iFrameGoogleMap'
				style='width: 275px; height: 275px;' itemprop='map'></iframe><br>{noSimple.isCarteGoogle.lienVoirCarteGrand}{noSimple.isCarteGoogle.popupGoogleMap}</td>

		<td
			style='padding-left: 5px; padding-right: 0px; margin: 0; padding-top: 0; padding-bottom: 0;'>
			<!-- END isCarteGoogle --> <!-- BEGIN adresses -->
			<table border="" class='tableauResumeAdresse'
				style='width: {largeurTableauResumeAdresse'>

				<tr>
					<td
						style='margin-bottom: 0px; padding: 0; font-size: 13px; vertical-align: bottom; text-align: right;'>
						<!-- BEGIN isConnected -->
						<li><a href="{urlLierAdresseAEvenement}">{intituleActionAdresses}</a>
							&nbsp;</li>
					</td>
				</tr>
				<tr>
					<td>
						<!-- END isConnected -->

						<table border="" style='margin: 0px; padding: 0;'>
							<tr>
								<td style='margin: 0px; padding: 0;'>{noSimple.adresses.adressesLiees}</td>
							</tr>
							<tr>
								<td
									style='margin: 0; padding: 0; text-align: right; font-size: 12px; vertical-align: bottom;'>
									<ul>
										<li class="seeAll"><a href="{urlAutresBiensRue}"><?_("Voir tous les bâtiments de cette rue...")?>
										</a></li>
										<li class="seeAll"><a href="{urlAutresBiensQuartier}"><?_("Voir tous les bâtiments de ce quartier...")?>
										</a></li>
									</ul>
								</td>
							</tr>
						</table> <!-- END adresses -->
					</td>
				</tr>
			</table> <!-- BEGIN isCarteGoogle -->
		</td>
	</tr>
</table>
<!-- END isCarteGoogle -->
<!-- END noSimple -->

<!-- BEGIN noSimple -->
<table
	class="large">
	<tr>
		<td><h2 class="black">
				<?_("Historique des évènements")?>
			</h2></td>
	</tr>
	<tr>
		<td>
			<ul>
				<!-- BEGIN ancres -->
				<li class="inside square" style='color: #000000; font-size: 12px;'><a
					href='{noSimple.ancres.url}'>{noSimple.ancres.titre}</a></li>
				<!-- END ancres -->
			</ul>
		</td>
		<td rowspan='{nbEvenements}' style='vertical-align: top;'><div
				style='float: right; font-size: 13px; vertical-align: top;'>
				<ul>
					<li class="addEvent"><a href="{urlAjouterEvenement}"
						style='white-space: nowrap;'><?_("Ajouter un évènement")?> </a></li>
					<!-- BEGIN isConnected -->
					<!-- BEGIN afficheLienSelectionImages -->
					<li class="addEvent"><a href="{urlDeplacerImages}"
						style='white-space: nowrap;'><?_("Sélectionner des images")?> </a>
					</li>
					<!-- END afficheLienSelectionImages -->
					<!-- BEGIN afficheLienSelectionImagePrincipale -->
					<li class="addEvent"><a href="{urlSelectionImagePrincipale}"
						style='white-space: nowrap;'><?_("Sélectionner l'image principale")?>
					</a></li>
					<!-- END afficheLienSelectionImagePrincipale -->
					<!-- BEGIN afficheLienSelectionTitre -->
					<li class="addEvent"><a href="{urlSelectionTitreAdresse}"
						style='white-space: nowrap;'><?_("Sélectionner le titre")?> </a></li>
					<!-- END afficheLienSelectionTitre -->
					<!-- BEGIN afficheLienPositionnementEvenements -->
					<li class="addEvent"><a href='{urlPositionnementEvenements}'
						style='white-space: nowrap;'><?_("Repositionner les évènements")?>
					</a></li>
					<!-- END afficheLienPositionnementEvenements -->

					<!-- END isConnected -->
				</ul>
			</div></td>
	</tr>
	<!-- BEGIN modePositionnementEvenements -->
	<tr>
		<td>
			<form action='{formActionPositionnementEvenements}'
				name='formPositionnementEvenements'
				id='formPositionnementEvenements' method='POST'
				enctype='multipart/form-data'>
				{sortableFormListe} <input type='button'
					value='Valider le positionnement'
					onclick="{onClickValidationPositionnementEvenement}">
			</form>
		</td>
	</tr>
	<!-- END modePositionnementEvenements -->

	<!-- BEGIN choixEvenementSansTitre -->
	<tr>
		<td style='color: #000000; font-size: 12px;'><li type='square'
			class="inside"><a href='{noSimple.choixEvenementSansTitre.url}'>{noSimple.choixEvenementSansTitre.titre}</a>
		</li></td>
	</tr>
	<!-- END choixEvenementSansTitre -->
</table>

<!-- END noSimple -->





<!-- BEGIN noSimple -->
<!-- BEGIN isHistoriqueNomsRue -->
<table style='border: 2px solid #666666;' border="" class="large">
	<tr>
		<td>
			<h2>
				<?_("Historique des noms de la rue")?>
			</h2>
		</td>
	</tr>
	<tr>
		<td style='font-size: 13px; color: #000000;'>
			<ul>
				<!-- END isHistoriqueNomsRue -->
				<!-- BEGIN listeHistoriqueNomsRue -->
				<li type='square' class="inside">{noSimple.listeHistoriqueNomsRue.annee}
					: {noSimple.listeHistoriqueNomsRue.nomRue}
					<div style='padding-left: 15px;'>{noSimple.listeHistoriqueNomsRue.commentaire}</div>
				</li>
				<!-- END listeHistoriqueNomsRue -->
				<!-- BEGIN isHistoriqueNomsRue -->
			</ul>
		</td>
	</tr>
</table>
<!-- END isHistoriqueNomsRue -->
<!-- END noSimple -->




<!-- BEGIN evenementLie -->
<a id='{evenementLie.numeroAncre}'></a>
<div>
{evenementLie.evenement}
{evenementLie.listCommentaires}
{evenementLie.formulaireCommentaire}
{evenementLie.idbrol}
</div>
<!-- END evenementLie -->


<!-- BEGIN noSimple -->

<!-- BEGIN autresVuesSur -->
<div class="event">
	<H5>
		<?_("Autres vues sur")?>
		{listeAdressesCourantes}
	</H5>
	{noSimple.autresVuesSur.value}
</div>
<!-- END autresVuesSur -->

<!-- BEGIN autresPrisesDepuis -->
<div class="event">
	<H5>
		<?_("Vues prises depuis")?>
		{listeAdressesCourantes}
	</H5>
	{noSimple.autresPrisesDepuis.value}
</div>
<!-- END autresPrisesDepuis -->
<!-- END noSimple -->



{divDeplacerEvenementVersGA}

