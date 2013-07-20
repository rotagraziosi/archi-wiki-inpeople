<!-- BEGIN titreEtLiens -->
<h1><?_("Recherche")?></h1>
<!-- END titreEtLiens -->
<div class="switchSearch"><b>Texte</b> &mdash; <a href="index.php?query={motcle}&archiAffichage=imageSearch">Images</a></div>
<form action="{formAction}" method="get">
<input type='hidden' name='archiAffichage' value='recherche'>
<input type='hidden' name='submit' value='Rechercher'>

<!-- BEGIN noHeaderNoFooter -->
<input type='hidden' name='noHeaderNoFooter' value='1'>
<!-- END noHeaderNoFooter -->

<!-- BEGIN modeAffichage -->
<input type='hidden' name='modeAffichage' value='{modeAffichage.value}'>
<!-- END modeAffichage -->


<!-- BEGIN parametres -->
<input type='hidden' name="{parametres.nom}" id="{parametres.id}" value="{parametres.value}">
<!-- END parametres -->

<input type="text" accesskey="F"  name="motcle" value="{motcle}" style='{motCleStyle}' placeholder="<?_("Rechercher")?>" class="searchInput" /><input type="image" src="images/Advisa/loupe.png" name="submit" class="loupe" value="<?_("Rechercher")?>" />&nbsp;
<!-- BEGIN displayRechercheAvancee -->
<a href='{urlRechercheAvancee}'><?_("Recherche avancée")?></a><br />
<!-- END displayRechercheAvancee -->
<!-- BEGIN displayCheckBoxResultatsCarte -->
<span><input type='checkbox' name='afficheResultatsSurCarte' id='afficheResultatsSurCarte' value='1' {checkBoxAfficheResultatsSurCarte}>&nbsp;<label for="afficheResultatsSurCarte"><?_("Afficher les 100 premiers résultats sur une carte")?></label></span>
<!-- END displayCheckBoxResultatsCarte -->
</form>
