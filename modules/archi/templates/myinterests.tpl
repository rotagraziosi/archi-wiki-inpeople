<div class="myInterests">
	<h1>Mes centres d'intérêts</h1>
	<!-- BEGIN interestList -->
	<div class="interestList {interestList.CSSclass}">
		<h2>{interestList.title}</h2>
		<ul>
		{interestList.vide}
		<!-- BEGIN interests -->
			<li>
			{interestList.interests.name}
			</li>
		<!-- END interests -->
		</ul>
	</div>
	<a href="{interestList.addInterest}">Ajouter un intérêt</a>
	<!-- END interestList -->
	<div class="addInterest">
		<form action="{formActionUrl}" name="{nameForm}" method="post">
			<div id="choixAdresse">
				{formAddInterest}
			</div> 
			<input type="submit" value="Ajouter">
		</form>
	</div>
</div>	