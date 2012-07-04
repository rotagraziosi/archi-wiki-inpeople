<div itemscope itemtype="http://schema.org/Photograph">
<!-- BEGIN isRetour -->
<a href='{isRetour.urlRetour}'><?_("Retour")?></a>
<!-- END isRetour -->
<br>
<h2><?_("Detail de l'image")?></h2>

<p>{nomEtDateCliche}
{choixFormatPhoto}
<br />


{imageZoom}
{txtZoom}
<div id='divImage' style='position:relative;'>
<img itemprop='image' src='{cheminDetailImage}' alt="{nom}" title="{nom}" id='imageAfficheeID' usemap='#mapZones' border=0 />
<br/><br/>
<div><?_("Cette photo est disponible sous la licence suivante :")?><br/>
<div class="licence">{licence}</div></div>
<div id='{IDDivZones}' style='position:absolute;top:0px;left:0px;'></div>
</div>
<br>
<span itemprop="dateCreated">
{datePriseDeVue}</span>
<br /><div itemprop="description">{description}</div>
{infosPrisDepuis}<br />
{infosVueSur}
</p>

<!-- BEGIN isConnected -->
<a href="{urlModifierImage}"><?_("Modifier l'image")?></a>
<!-- END isConnected -->


<!-- BEGIN isAdmin -->
&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="{isAdmin.urlAfficheHistorique}"><?_("Afficher l'historique de l'image")?></a>
<!-- END isAdmin -->

<!-- BEGIN isAdminOrModerateurFromVille -->
&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="{isAdminOrModerateurFromVille.urlSupprimerImage}"><?_("Supprimer l'image")?></a>
<!-- END isAdminOrModerateurFromVille -->

<!-- BEGIN selectionZonesCliquables -->
&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="{selectionZonesCliquables.urlSelectionZone}" style='{selectionZonesCliquables.styleMenuZoneSelection}'><?_("Zone de selection")?></a>
<!-- END selectionZonesCliquables -->




<!-- BEGIN vueSur -->
<br><br>
<H2><?_("Cette photo donne une vue sur :")?></H2>
{vueSur.value}
<!-- END vueSur -->

<!-- BEGIN prisDepuis -->
<br><br>
<H2><?_("Cette photo a été prise depuis :")?></H2>
{prisDepuis.value}
<!-- END prisDepuis -->

{listeAdressesLiees}

{listeEvenementsLies}

{listeMapsZones}

{listeDivsZones}

</div>

