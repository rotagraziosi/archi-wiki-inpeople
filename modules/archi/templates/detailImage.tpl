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
<script src="js/navImages.js"></script>
<div id='divImage' style='position:relative;'>
<div class="navImagesWrapper">
<!-- BEGIN previous -->
<a href="{prevURL}#divImage" rel="prefetch" class="prevPic" id="prevPic"><img src="images/Advisa/balise_inver.png" alt="Précédent"/></a>
<!-- END previous -->
<figure class="fullscreenWrapper" id="fullscreenWrapper">
<img itemprop='image' class="current_picture" src='{cheminDetailImage}' data-list="{list}" data-id={imgID} data-orgid={orgId}  data-date="{imgDate}" data-format="{format}" alt="{nom}" title="{nom}" id='imageAfficheeID' usemap='#mapZones' border=0 />
<figcaption><span class="fullscreenDesc" id="fullscreenDesc">{fullscreenDesc}</span></figcaption>
</figure>
<!-- BEGIN next -->
<a href="{nextURL}#divImage" rel="next" class="nextPic" id="nextPic"><img src="images/Advisa/balise.png" alt="Suivant"/></a>
<!-- END next -->
</div>
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

