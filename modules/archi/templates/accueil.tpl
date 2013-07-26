<div style='display:table;'>
<!--
<div class="pub">
    <strong><a href="http://m.archi-strasbourg.org/"><?_("Découvrez la version smartphone de notre site sur ")?><i>m.archi-strasbourg.org</i></a></strong><br/><br/>
    <a href="https://itunes.apple.com/fr/app/id557893157?mt=8&affId=1578782"><img src="images/Advisa/AR_appstore.jpg" alt="<?_("Disponible sur l'App Store")?>" /></a> <a href="https://play.google.com/store/apps/details?id=archi.strasbourg.dev&feature=search_result#?t=W251bGwsMSwyLDEsImFyY2hpLnN0cmFzYm91cmcuZGV2Il0."><img src="images/Advisa/AR_googleplay.jpg" alt="<?_("Disponible sur Google Play")?>" /></a>
</div>
-->
<div class="homeSearch">
<div class="switchSearch"><b>Texte</b> &mdash; <a href="index.php?archiAffichage=imageSearch">Images</a></div>
<form method="get" action="http://localhost/archi-strasbourg.org/?archiAffichage=recherche">
<input type="hidden" value="recherche" name="archiAffichage">
<input type="hidden" value="Rechercher" name="submit">





<input type="text" class="searchInput" placeholder="Rechercher" style="width:300px;" name="motcle" accesskey="F"><input type="image" value="Rechercher" class="loupe" name="submit" src="images/Advisa/loupe.png">
<br/><a href="http://localhost/archi-strasbourg.org/?archiAffichage=rechercheAvancee">Recherche avancée</a><br>
<span><input type="checkbox" value="1" id="afficheResultatsSurCarte" name="afficheResultatsSurCarte">&nbsp;<label for="afficheResultatsSurCarte">Afficher les résultats sur une carte</label></span>
</form>

</div>
<!--<TABLE>
<TR><TD></TD></TR>
</TABLE>-->
<P></P>

<!--
<div style='text-align:center;'>{infos}</div>
-->

<P></P>

<!--
<div style="padding-top:7px;height:20px;width:200px;display:table;
<!-- BEGIN estConnecte -->
float:left;
<!-- END estConnecte -->
background-color:#666666;color:#FFFFFF;text-align:center;font-size:12px;font-weight:bold;padding-bottom:3px;float:left;">
{onglet1}
</div>

<!-- BEGIN estConnecte -->
<div style="padding-top:7px;height:20px;width:200px;display:table;float:left;background-color:#007799;color:#FFFFFF;text-align:center;font-size:12px;font-weight:bold;padding-bottom:3px;">
{onglet2}
</div>
<div style="padding-top:7px;height:20px;width:200px;display:table;float:left;background-color:#FF8800;color:#FFFFFF;text-align:center;font-size:12px;font-weight:bold;padding-bottom:3px;">
{onglet3}
</div>
<!-- END estConnecte -->
-->

<!-- BEGIN afficheEncarts -->
<div style='display:table;' class="clear">
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

<!-- bouton "Ajoutez votre adresse" 
<a href='{urlAjoutAdresse}' onmouseover="document.getElementById('imageAjouterAdresse').src='images/ajoutAdresse2.jpg';" onmouseout="document.getElementById('imageAjouterAdresse').src='images/ajoutAdresse1.jpg';"><img src='images/ajoutAdresse1.jpg' id='imageAjouterAdresse' border=0></a>
<br>
-->



<!--
<strong>== Pourquoi ajouter votre adresse ? ==</strong><br><br>

Bonne question. En ajoutant votre adresse vous contribuez au développement du site. Mais d'abord qu'entend t-on par "votre adresse" ? Et bien cela peut être l'immeuble ou la maison que vous occupez. Un immeuble que vous aimez mais que vous ne trouvez pas sur le site. Avec le développement des appareils photo numériques, il devient très simple de prendre une photo, et de la copier sur l'ordinateur. Ajouter une adresse dans www.archi-strasbourg.org ne prend pas plus de 20 secondes. Copier la photo 10 secondes de plus...<br><br>

Ensuite si vous n'avez aucune information concernant l'adresse que vous avez ajouté, laissez faire les internautes... www.archi-strasbourg.org est une site collaboratif, c'est à dire que tout le monde pourra ajouter des informations qui pourront être commentées et rectifiées en cas d'erreur.
<br><br>
Si la passion "vous mange" vous pourrez ensuite rajouter autant d'adresses que vous le souhaitez et peut être même devenir un administrateur du site si vous souhaitez vous investir davantage.
<br><br>
A terme www.archi-strasbourg.org a l'ambition tout d'abord de couvrir tout Strasbourg et pourquoi pas d'autres villes de France. 
-->


</div>



{calqueHelp}
