<h1><?_("Statistiques")?></h1>

<style>
.tableau{border:0px;}
.tableau td{font-size:12px;text-align:left;}
.enteteTableau {background-color:#007799;color:#FFFFFF;text-align:center;}
</style>

<br>
<ul>
<li><?_("La base archi-strasbourg comprend <b>{nbAdresses}</b> adresses sur environ 20000 recensées sur Strasbourg.")?></li>
<li><?_("Il y a en tout")?> <b>{nbEvenements}</b> <?_("évènements decrivant chacune de ces adresses.")?></li>
<li><b>{nbPhotos}</b> <?_("photos illustrent ces évènements.")?></li>
</ul>
<br><br>

<h2><?_("Les dix architectes les plus productifs :")?></h2>

{architectes}

{voirTousLesArchitectes}
<br>
<br>
<h2><?_("Les dix rues où il y a le plus d'adresses :")?></h2>

{rues}

{voirToutesLesRues}
