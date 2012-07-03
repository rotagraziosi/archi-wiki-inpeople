{recapitulatifAdresse}

<h2>{typeTitre} <?_("d'un Évènement")?></h2>

<form action="{urlAction}" id='formAjout' name='formAjout' method="post" enctype='multipart/form-data'>
<table>


<!-- BEGIN isNotAffichageGroupeAdresse -->

<tr><td><?_("Libelle")?></td><td><input type="text" value="{titre}" name="titre" /></td><td>{titre-error}</td></tr>
<tr><td></td><td colspan="2"><input type="button" value="b" style="width:50px;font-weight:bold" onclick="bbcode_ajout_balise('b', 'formAjout', 'description');bbcode_keyup(this,'apercu');" />
	<input type="button" value="i" style="width:50px;font-style:italic" onclick="bbcode_ajout_balise('i', 'formAjout', 'description');bbcode_keyup(this,'apercu');" />
	<input type="button" value="u" style="width:50px;text-decoration:underline" onclick="bbcode_ajout_balise('u', 'formAjout', 'description');bbcode_keyup(this,'apercu');" />
	<input type="button" value="quote"style="width:50px" onclick="bbcode_ajout_balise('quote', 'formAjout', 'description');bbcode_keyup(this,'apercu');" />
	<input type="button" value="code" style="width:50px" onclick="bbcode_ajout_balise('code', 'formAjout', 'description');bbcode_keyup(this,'apercu');" />
	<input type="button" value="url"  style="width:50px" onclick="bbcode_ajout_balise('url',  'formAjout', 'description');bbcode_keyup(this,'apercu');" /></td>
</tr>
<tr>
	<td><?_("Description")?>   </td><td><textarea name="description" cols="50" rows="15"  onkeyup="bbcode_keyup(this,'apercu');">{description}</textarea></td><td>{description-error}</td>
</tr>
<tr>
	<td><?_("Aperçu")?></td><td><div id='apercu'><pre></pre></div></td><td></td>
</tr>
<tr>
	<td><?_("Date de début")?> </td><td><input type="text" value="{dateDebut}" name="dateDebut" id='dateDebut' /><input type="button" value="Choisir" name="Choisir" onclick="{onClickDateDebut}">
	</td><td>{dateDebut-error}</td>
</tr>
<tr>
	<td><?_("Date de fin")?> </td><td><input type="text" value="{dateFin}" name="dateFin" id='dateFin' /><input type="button" value="Choisir" name="Choisir" onclick="{onClickDateFin}"></td><td>{dateFin-error}</td></tr>
<tr><td><?_("Source")?></td><td>
	<input type='text' name='nomSource' id='sourcetxt' value='{nomSource}' disabled='disabled'><input type="button" name="Choisir" value="Choisir" onclick="{onClickChoixSource}" /><input type='hidden' name='idSource' id='source' value='{idSource}'>
	<td>{idSource-error}</td>
	</tr>
<tr><td><?_("Type de structure")?></td><td><select name="typeStructure"><option value="0"><?_("Aucun")?></option>
	<!-- BEGIN typeStructure -->
	<option value="{isNotAffichageGroupeAdresse.typeStructure.id}" {isNotAffichageGroupeAdresse.typeStructure.selected}>{isNotAffichageGroupeAdresse.typeStructure.nom}</option>
	<!-- END typeStructure -->
	</select>
	</td><td>{typeStructure-error}</td></tr>
	<tr>
	<td colspan=3 style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;border-left:1px #000000 solid;border-right:1px #000000 solid;">
	
	
	<table border=0>
	<tr><td><?_("Type d'évènement")?> </td><td>
	<input type='radio' name='typeGroupeEvenement' value = "1" onclick="{onClickTypeEvenement1}" {checkedTypeEvenement1}>&nbsp;<?_("Culturel")?>&nbsp;
	<input type='radio' name='typeGroupeEvenement' value = "2" onclick="{onClickTypeEvenement2}" {checkedTypeEvenement2}>&nbsp;<?_("Travaux")?>
	<div id="typeEvenement">
	<select name='typeEvenement'>
		<!-- BEGIN typeEvenement -->
		<option value='{isNotAffichageGroupeAdresse.typeEvenement.id}' {isNotAffichageGroupeAdresse.typeEvenement.selected}>{isNotAffichageGroupeAdresse.typeEvenement.nom}</option>
		<!-- END typeEvenement -->
	</select>
	</div>
	</td>
	<td>{typeEvenement-error}</td></tr>
	</table>
	
	<div id="afficheChampsSupplementairesCulturel" style="{styleChampsSupplementaireCulturel}">
	<table border=0>
	<tr>
	<td><?_("ISMH")?></td><td><input type="checkbox" name="ISMH" value="ISMH" {ISMHchecked}></td><td></td>
	</tr>
	<tr>
	<td><?_("MH")?></td><td><input type="checkbox" name="MH" value="MH" {MHchecked}></td><td></td>
	</tr>
	</table>
	</div>
	
	<div id="afficheChampsSupplementairesTravaux" style="{styleChampsSupplementaireTravaux}">
	<table border=0>
	<tr>
	<td><?_("Nombre d'étages")?></td><td><input type='text' value="{nbEtages}" name="nbEtages" style="width:50px;"></td>
	</tr>
	<tr>
	<td><?_("Courant Architectural")?></td><td>
		{courantsArchitecturaux} {courant-error}
	</td>
	</tr>
	</table>
	</div>
	</td>
</tr>
<tr><td><?_("Personnages")?>/<?_("personnes")?></td><td><select name="personnes[]" id='personnes' multiple="multiple" style="height:100px;" >
	<!-- BEGIN personnes -->
	<option value="{isNotAffichageGroupeAdresse.personnes.val}" selected="selected">{isNotAffichageGroupeAdresse.personnes.nom}</option>
	<!-- END personnes -->
	</select><input type="button" name="Choisir" value="Choisir" onclick="{onClickChoixPersonne}">
	</td><td>{personnes-error}</td></tr>

<!-- END isNotAffichageGroupeAdresse -->

<!-- BEGIN ajouterAdresses -->
<tr><td><?_("Lier à des adresses")?></td><td><select name="adresses[]" id='adresses' multiple="multiple" style="height:100px;">
	<!-- BEGIN adresses -->
	<option value="{ajouterAdresses.adresses.val}" selected="selected">{ajouterAdresses.adresses.nom}</option>
	<!-- END adresses -->
	</select><a href='#' onclick="document.getElementById('calqueAdresse').style.display='block';"><?_("Ajouter des adresses à l'évènement")?></a></td>
	<td>{adresses-error}</td></tr>
<!-- END ajouterAdresses -->


<!-- BEGIN ajouterEvenements -->
<tr><td><?_("Lier à d'autres évènements")?></td><td><select name="evenements[]" id='evenements' multiple="multiple" style="height:100px;">
	<!-- BEGIN evenements -->
	<option value="{evenements.val}" selected="selected">{evenements.nom}</option>
	<!-- END evenements -->
	</select><a href='#' onclick="document.getElementById('calqueEvenement').style.display='block';"><?_("Ajouter un évènement")?></a></td>
	<td>{evenements-error}</td></tr>
<!-- END ajouterEvenements -->


<input type='hidden' value="{evenementGroupeAdresse}" name='evenementGroupeAdresse'>


<tr><td></td><td><input type="submit" value="{boutonValidation}" name="{nomBoutonValidation}" /></td><td>{estmodif}</td></tr>
</table>

</form>

{calqueAjoutAdresse}
{calqueAjoutEvenement}
{calqueAjoutPersonne}
{calqueAjoutSource}
{popupCalendrier}

<script type="text/javascript" >bbcode_keyup(document.forms['formAjout'].elements['description'], 'apercu');</script>
