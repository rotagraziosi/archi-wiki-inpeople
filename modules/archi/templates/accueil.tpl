<div style='display:table;'>
<!--
<div class="pub">
    <strong><a href="http://m.archi-strasbourg.org/"><?_("Découvrez la version smartphone de notre site sur ")?><i>m.archi-strasbourg.org</i></a></strong><br/><br/>
    <a href="https://itunes.apple.com/fr/app/id557893157?mt=8&affId=1578782"><img src="images/Advisa/AR_appstore.jpg" alt="<?_("Disponible sur l'App Store")?>" /></a> <a href="https://play.google.com/store/apps/details?id=archi.strasbourg.dev&feature=search_result#?t=W251bGwsMSwyLDEsImFyY2hpLnN0cmFzYm91cmcuZGV2Il0."><img src="images/Advisa/AR_googleplay.jpg" alt="<?_("Disponible sur Google Play")?>" /></a>
</div>
-->
<script src="js/homeSearch.js"></script>
<div class="homeSearch" id="homeSearch">
<div class="switchSearch"><b>Texte</b> &mdash; <a id="switchSearchImg" href="index.php?archiAffichage=imageSearch">Images</a></div>
<form method="get" action="index.php?archiAffichage=recherche">
<input type="hidden" value="recherche" name="archiAffichage">
<input type="hidden" value="Rechercher" name="submit">
<input type="text" class="searchInput" placeholder="Rechercher" style="width:300px;" name="motcle" accesskey="F"><input type="image" value="Rechercher" class="loupe" name="submit" src="images/Advisa/loupe.png">
<a href="index.php?archiAffichage=rechercheAvancee">Recherche avancée</a><br>
<span><input type="checkbox" value="1" id="afficheResultatsSurCarte" name="afficheResultatsSurCarte">&nbsp;<label for="afficheResultatsSurCarte">Afficher les résultats sur une carte</label></span>
</form>

</div>

</div>


<!-- BEGIN test -->
<div class="body-content" style='display:table;'>
{test.news}
{test.lastAdd}
</div>
<!-- END test -->



<div class="news_content">
<!-- BEGIN item -->
<div class="indexItemWrapper {item.CSSClassWrapper}">
	<img alt="" src="{item.imgUrl}">
	<h5>{item.titreItem}</h5>
	<p>
	{item.textItem}
	</p>
	<a href="{item.urlItem}">{item.titreItem}</a>
</div>
<!-- END item -->

</div>



<!-- BEGIN homeCategory -->
<div class="content_item">
	{homeCategory.category}	
</div>
<!-- END homeCategory -->

<!-- BEGIN afficheEncarts -->
<div style='display:table;'>

<div class="homeTable">
{encart1}
{encart2}
{encart3}
{encart4}
{encart5}
{encart6}

</tr></table>
</div>
<!-- END afficheEncarts -->



<!-- BEGIN afficheProfil -->
<div class="monProfil">
<table><tr>
<td>
{htmlProfil}
</td>
</tr></table>
</div>
<!-- END afficheProfil -->


<!-- BEGIN afficheMonArchi -->
<div class="monArchi">
<table><tr>
<td>
{htmlMonArchi}
</td></tr></table>
</div>
<!-- END afficheMonArchi -->


</div>


{calqueHelp}


