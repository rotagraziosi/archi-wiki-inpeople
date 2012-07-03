<h1><?_("Présentation d'une personne")?></h1>
<h2>{prenom} {nom}</h2>
<table>
<caption><?_("Infomations minimales")?></caption>
<tr><th><?_("Métier")?></th><td>{metier}</td></tr>
<tr><th><?_("Naissance")?></th><td>{dateNaissance}</td></tr>
<tr><th><?_("Décès")?></th><td>{dateDeces}</td></tr>
</table>
<h2><?_("Description")?></h2>
<p>{description}</p>
{evenementLies}
<a href="{urlAjout}"><?_("Ajouter une personne")?></a>
