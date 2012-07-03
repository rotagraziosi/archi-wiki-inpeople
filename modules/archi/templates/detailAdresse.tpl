<h1><?_("Détails de l'adresse")?></h1>
<!--
<h3>{nom}</h3>
<p>{description}</p>
-->
<!-- BEGIN historique -->
<div class="historiqueAdresse" style="border:2px solid #ff8800;border-bottom:none; border-right: none;margin:5px 0px;padding:3px;">
<h2>{historique.nom}</h2>
<!-- BEGIN connecte -->
<div style="float:right;"><a href="{historique.urlModifier}"><?_("Modifier cet historique")?></a></div>
<!-- END connecte -->
<p><span>{historique.date}</span> {historique.description}</p>
</div>
<!-- END historique -->

{listeImages}
{listeEvenements}
{historiqueAdresse}

<br />
<!-- BEGIN connecte -->
<a href="{connecte.lienFormulaireImage}"><?_("Ajouter une image à l'adresse")?></a>
<a href="{connecte.lienImages}"><?_("Modifier vos images")?></a>
<a href="{connecte.lienAjoutHistoriqueAdresse}"><?_("Ajouter un historique à cette adresse")?></a>
<a href="{connecte.ajoutAdresse}"><?_("Ajouter une adresse")?></a>
<a href="{connecte.ajoutEvenementSurAdresse}"><?_("Ajouter un evenement à cette adresse")?></a>
<a href="{connecte.urlSupprimerAdresse}"><?_("Supprimer cette adresse")?></a>
<a href="{connecte.urlSupprimerHistorique}"><?_("Supprimer cet historique")?></a>
<!-- END connecte -->
<!-- BEGIN pasConnecte -->
<p><?_("Connectez-vous pour pouvoir ajouter ou modifier une adresse.")?></p>
<!-- END pasConnecte -->

