<div class='evenement' itemprop='event' itemscope itemtype="http://schema.org/Event" style='position: relative; display: table;'>
	{urlEvenementExterne}
	<!-- BEGIN menuAction -->
	<div class="menuAction"	style="margin-left: 1em; float: right; padding: 5px; display: table">
		<ul style='margin: 0px; display: table;'>
			<!-- BEGIN rowName -->
				<li class='actionEvent'>{menuAction.rowName.actionName}</li>
				<li><a href="{menuAction.rowName.urlAction}" 
				<!-- BEGIN confirmMessage -->
					onclick="if(confirm('{menuAction.rowName.confirmMessage.message}')){location.href='{menuAction.rowName.confirmMessage.url}'};"
				<!-- END confirmMessage -->
				>{menuAction.rowName.actionTarget}</a>
				<!-- BEGIN secondAction -->
						| <a href="{menuAction.rowName.secondAction.urlAction}" 
					<!-- BEGIN confirmMessage -->
						onclick="if(confirm('{menuAction.rowName.secondAction.confirmMessage.message}')){location.href='{menuAction.rowName.secondAction.confirmMessage.url}'};"
					<!-- END confirmMessage -->
					>{menuAction.rowName.secondAction.actionTarget}</a>
				<!-- END secondAction -->
				</li>
				
			<!-- END rowName -->
		</ul>
	</div>
	<!-- END menuAction -->

	<div style='min-height: 150px;'>
		<h3 itemprop="titre">{titre}</h3> - {txtEnvoi} par {utilisateur} {dateEnvoi}
		
		<a href="{lienHistoriqueEvenementCourant}">(Consulter l'historique)</a>
		

		<div class="event">
			<p>
			<ul>
				<li>{dates}</li>
				<li>{source}</li>
				<li>Structure  : {typeStructure}</li>
				<li><?_("Type d'Évènement :")?> <a href="{urlTypeEvenement}">{typeEvenement}</a>
				</li>
			</ul>
			{numeroArchive}
			<!-- BEGIN pers -->
			{pers.metier} <a href="{pers.urlEvenement}">{pers.prenom}
				{pers.nom}</a>
			<br>
			<!-- END pers -->
			</p>
			<p>{description}</p>
			<!-- BEGIN isCourantArchi -->
			<div class="courantAchitectural">
				<h4>
					<?_("Courant Architectural")?>
				</h4>
				<ul>
					<!-- BEGIN archi -->
					<li><a href="{isCourantArchi.archi.url}">{isCourantArchi.archi.nom}</a></li>
					<!-- END archi -->
				</ul>
			</div>
			<!-- END isCourantArchi -->
			<div class="historiqueEvenement">
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

<!-- BEGIN formEvenement -->
<form action='' name='formulaireEvenement' method='POST' enctype='multipart/form-data' id='formulaireEvenement'>
<input type='hidden' name='actionFormulaireEvenement' id='actionFormulaireEvenement' value=''>";
<!-- END formEvenement -->
