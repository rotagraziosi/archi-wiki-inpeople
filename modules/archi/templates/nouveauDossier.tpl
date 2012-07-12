<script>
// verification de la precision de l'adresse et interdiction d'ajouter une adresse avec juste une ville sans autre précision de localisation
function testAdresseValideAndSubmit(idFormulaire)
{
	var noError = true;
	
	//calcul du nombre d'adresses affichees
	nbAdresses=0;
	
	for(i=0 ; document.getElementById('rue'+i)!=null && i<20;i++)
	{
		nbAdresses++;
	}
	
	
	if(document.getElementById('ville').value=='0')
	{
		alert('<?_("Une ville doit être précisée.")?>');
		noError=false;
	}
	else if(document.getElementById('ville')!='0' && document.getElementById('quartiers').value=='0' && document.getElementById('sousQuartiers').value=='0' && nbAdresses==0)
	{
		alert("<?_("Il faut au moins préciser un quartier pour l'adresse.")?>");
		noError=false;
	}
	else if(nbAdresses>0 && document.getElementById('ville')!='0' && document.getElementById('quartiers').value=='0' && document.getElementById('sousQuartiers').value=='0')
	{
		// on verifie que pour les adresses ou il n'y a que la ville de précisée, qu'elles comportent bien au moins un nom de rue
		for(i=0 ; i<nbAdresses ; i++)
		{
			if(document.getElementById('rue'+i).value=='0' || document.getElementById('rue'+i).value=='')
			{
				alert('<?_("Pour valider une adresse il faut préciser une rue ou au moins un quartier ou un sous quartier en plus de la ville.")?>');
				noError=false;
			}
		}
	}
	
	if(noError==true)
	{
		document.getElementById(idFormulaire).submit();
	}
}


function affichePopupAttente()
{
		document.getElementById('popupAttente').style.top=(getScrollHeight()+200)+'px';
		document.getElementById('popupAttente').style.display='block';
}
</script>


<!-- BEGIN isNotAjoutNouvelleAdresse -->
{recapitulatifAdresse}
<!-- END isNotAjoutNouvelleAdresse -->

{recaptitulatifAncres}

{liensModifEvenements}

<h2>{typeTitre}</h2>
<h1>{titrePage}</h1>

<form action="{formAction}" name='formAjoutDossier' id='formAjoutDossier' method='POST' enctype='multipart/form-data'>

<!-- BEGIN ajoutPersonne -->
<input type='hidden' name='submit' id='submit' />
<table style='border:solid 2px #666666;' width='700'>
<tr>
<td>
<h2><?_("Personne")?></h2>
<table border=0>
<tr>
<td class='enteteAdresses'><label for="name"><?_("Nom")?></label></td>
<td><input type='text' name='nom' id='name' value="{name}"></td>
</tr>
<tr>
<td class='enteteAdresses'><label for="firstname"><?_("Prénom")?></label></td>
<td><input type='text' name='prenom' id='firstname' value="{firstname}"></td>
</tr>
<tr>
<td class='enteteAdresses'><label for="job"><?_("Métier")?></label></td>
<td><select type='text' name='metier' id='job'>
{jobList}
</select>
</td>
</tr>
<tr>
<td class='enteteAdresses'><label for="birth"><?_("Date de naissance")?></label></td>
<td><input type='date' name='dateNaissance' id='birth' value="{birth}"></td>
</tr>
<tr>
<td class='enteteAdresses'><label for="death"><?_("Date de décès")?></label></td>
<td><input type='date' name='dateDeces' id='death' value="{death}"></td>
</tr>


</table>
</td>
</tr>
</table>

<!-- END ajoutPersonne -->

<!-- BEGIN isNotAjoutSousEvenement -->

<table style='border:solid 2px #666666;' width='700'>
<tr>
<td>
<input type='submit' value="+" name='ajouterAdresse' onclick="{onClickBoutonAjouterAdresse}"  title="{msgButtonAddAdresse}" onMouseOut="closeContextHelp();">
<!--<input type='submit' value="-" name='enleverAdresse' onclick="{onClickBoutonEnleverAdresse}"  title="{msgButtonDeleteAdresse}" onMouseOut="closeContextHelp();">-->
<table border=0>
<tr title="{msgVille}" onMouseOut="closeContextHelp();">
<td class='enteteAdresses'><?_("Ville")?></td><td><input type='text' name='villetxt' id='villetxt' value="{villetxt}" readonly><input type='hidden' id='ville' name='ville' value="{ville}"><input type='button' value='Choisir' onclick="{onClickBoutonChoixVille}" >
	<!--<input type='hidden' value='{latitude}' name='latitude' id='latitude'>
	<input type='hidden' value='{longitude}' name='longitude' id='longitude'>-->
</td>
</tr>


<tr title="{msgQuartier}" onMouseOut="closeContextHelp();" style='display:{displayQuartiers};'>
<td class='enteteAdresses'><?_("Quartier")?></td><td id='listeQuartier'>
	<select name="quartiers" id='quartiers' onchange="{onChangeListeQuartier}">
		<option value="0"><?_("Aucun")?></option>
		<!-- BEGIN quartiers -->
		<option value="{isNotAjoutSousEvenement.quartiers.id}" {isNotAjoutSousEvenement.quartiers.selected}>{isNotAjoutSousEvenement.quartiers.nom}</option>
		<!-- END quartiers -->
	</select>
</td>
</tr>
<tr title="{msgSousQuartier}" onMouseOut="closeContextHelp();" style='display:{displaySousQuartiers};'>
<td class='enteteAdresses'><?_("Sous-quartier")?></td><td id='listeSousQuartier'>
	<select name="sousQuartiers" id='sousQuartiers'>
		<option value="0"><?_("Aucun")?></option>
		<!-- BEGIN sousQuartiers -->
		<option value="{isNotAjoutSousEvenement.sousQuartiers.id}" {isNotAjoutSousEvenement.sousQuartiers.selected}>{isNotAjoutSousEvenement.sousQuartiers.nom}</option>
		<!-- END sousQuartiers -->
	</select>
</td>
<td>
&nbsp;
</td>
</tr>


<tr>
<td class='enteteAdresses'><?_("Numéro")?></td><td class='enteteAdresses'><?_("Indicatif")?></td><td class='enteteAdresses'><?_("Rue")?></td><td class='enteteAdresses'><?_("Suppr")?></td></tr>



<!-- BEGIN adresses -->
<tr title="{msgRue}" onMouseOut="closeContextHelp();">
<td><input type='text' name='numero[]' id='numero{isNotAjoutSousEvenement.adresses.idUnique}' value='{isNotAjoutSousEvenement.adresses.numero}' style='width:50px;'></td>
<td>
	<select name='indicatif[]'>
		<option value='0'><?_("Aucun")?></option>
		<!-- BEGIN indicatifs -->
		<option value="{isNotAjoutSousEvenement.adresses.indicatifs.id}" {isNotAjoutSousEvenement.adresses.indicatifs.selected}>{isNotAjoutSousEvenement.adresses.indicatifs.nom}</option>
		<!-- END indicatifs -->
	</select>
</td>
<td>
	<input type ='text' name='ruetxt[]' id='rue{isNotAjoutSousEvenement.adresses.idUnique}txt' value="{isNotAjoutSousEvenement.adresses.nomRue}" readonly><input type ='hidden' name='rue[]' id='rue{isNotAjoutSousEvenement.adresses.idUnique}' value='{isNotAjoutSousEvenement.adresses.rue}'><input type='button' value='choisir' onclick="{isNotAjoutSousEvenement.adresses.onClickBoutonChoixRue}">
	&nbsp;
</td>
<td>
	<input type='hidden' value='{isNotAjoutSousEvenement.adresses.idAdresse}' name='idAdresse[]'>
	<input type='hidden' value='{isNotAjoutSousEvenement.adresses.idUnique}' name='idUnique[]'>
	<input type='hidden' value='{isNotAjoutSousEvenement.adresses.longitude}' name='longitude[]' id='longitude_{isNotAjoutSousEvenement.adresses.idUnique}'>
	<input type='hidden' value='{isNotAjoutSousEvenement.adresses.latitude}' name='latitude[]' id='latitude_{isNotAjoutSousEvenement.adresses.idUnique}'>
	<input type='submit' value='-' name='enleverAdresse' onclick="{isNotAjoutSousEvenement.adresses.onClickBoutonSupprAdresse}">
</td>
</tr>
<!-- END adresses -->




<tr><td colspan=4 style='font-size:12px;'><?_("Si vous souhaitez que nous ajoutions une rue, un quartier, un sous quartier ou une ville, faites nous en part grâce au formulaire de")?> <a href='?archiAffichage=contact'><?_("contact")?></a>.</td></tr>
</table>

</td>
</tr>
</table>



<!-- END isNotAjoutSousEvenement -->




<!-- BEGIN afficheAjoutEvenement -->
<br>

<table style='border:solid 2px #666666;'  width='700'>
<tr>
<td>


<h2><?_("Événement")?></h2>

<table>
<tr title="{msgLibelle}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Libellé")?></td><td><input type='text' name='titre' value="{titre}"></td><td></td>
</tr>
<tr><td class='enteteFormulaireDossier'><?_("Mise en forme")?></td><td colspan="2"><input type="button" value="b" style="width:50px;font-weight:bold" onclick="bbcode_ajout_balise('b', 'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgGras}" onMouseOut="closeContextHelp();"/>
	<input type="button" value="i" style="width:50px;font-style:italic" onclick="bbcode_ajout_balise('i', 'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgItalic}" onMouseOut="closeContextHelp();"/>
	<input type="button" value="u" style="width:50px;text-decoration:underline;" onclick="bbcode_ajout_balise('u', 'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgUnderline}" onMouseOut="closeContextHelp();"/>
	<input type="button" value="quote" style="width:50px" onclick="bbcode_ajout_balise('quote', 'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgQuotes}" onMouseOut="closeContextHelp();"/>
	<!--<input type="button" value="code" style="width:50px" onclick="bbcode_ajout_balise('code', 'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgCode}" onMouseOut="closeContextHelp();" onkeyup="bbcode_keyup(this,'apercu');"/>-->
	<input type="button" value="url interne"  style="width:75px" onclick="bbcode_ajout_balise('url',  'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgUrl}" onMouseOut="closeContextHelp();" onkeyup="bbcode_keyup(this,'apercu');"/>
	<input type="button" value="url externe"  style="width:80px" onclick="bbcode_ajout_balise('urlExterne',  'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgUrl}" onMouseOut="closeContextHelp();" onkeyup="bbcode_keyup(this,'apercu');"/>
    <input type="button" value="iframe"  style="width:80px" onclick="bbcode_ajout_balise('iframe',  'formAjoutDossier', 'description');bbcode_keyup(this,'apercu');" title="{msgIFrame}" onMouseOut="closeContextHelp();" onkeyup="bbcode_keyup(this,'apercu');"/>
	</td>
</tr>
<tr title="{msgDescription}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Description")?></td><td><textarea id="textarea_desc" name='description' onkeyup="bbcode_keyup(this,'apercu');" cols="50" rows="15">{description}</textarea></td><td></td>
</tr>
<tr>
<td class='enteteFormulaireDossier'><?_("Aperçu")?></td><td><div id='apercu'><pre></pre></div></td><td></td>
</tr>
<tr title="{msgDateDebut}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><span><?_("Date début")?></span></td>
<td><input type='text' value='{dateDebut}' name='dateDebut' id='dateDebut'><input type="button" value="Choisir" onclick="{onClickDateDebut}">&nbsp;<input type='checkbox' name='isDateDebutEnviron' id='isDateDebutEnviron' value='1' {isDateDebutEnviron} > <label for="isDateDebutEnviron"><?_("environ")?></label></td><td></td>
</tr>



<!-- BEGIN canChangeDateFin -->
<tr title="{msgDateFin}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Date fin")?></td><td><input type='text' value='{dateFin}' name='dateFin' id='dateFin'><input type="button" value="Choisir" name="Choisir" onclick="{onClickDateFin}"></td><td></td>
</tr>
<!-- END canChangeDateFin -->

<!-- BEGIN noChangeDateFin -->
<input type='hidden' name='dateFin' id='dateFin' value="{dateFin}">
<!-- END noChangeDateFin -->



<!-- BEGIN isDisplaySource -->
<tr title="{msgSource}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Source")?></td><td>
<input type="hidden" value="{source}" name="source" id='source'>
<input type="text" value="{sourcetxt}" name="sourcetxt" id="sourcetxt" readonly><input type='button' value="choisir" name="choisir" onclick="{onClickBoutonChoisirSource}">
<input type='button' name='razSource' value='Aucune source' onclick="document.getElementById('source').value='0'; document.getElementById('sourcetxt').value='';">
</td><td></td>
</tr>
<!-- END isDisplaySource -->

<!-- BEGIN isNotDisplaySource -->
<input type='hidden' name="source" id='source' value='{source}'>
<!-- END isNotDisplaySource -->

<!-- BEGIN canChangeNumeroArchive -->
<tr title="{msgNumeroArchive}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Numéro d'archive")?></td><td><input type='text' name='numeroArchive' id='numeroArchive' value="{numeroArchive}"></td></tr>
<!-- END canChangeNumeroArchive -->
<!-- BEGIN noChangeNumeroArchive -->
<input type='hidden' name='numeroArchive' id='numeroArchive' value="{numeroArchive}">
<!-- END noChangeNumeroArchive -->

<!-- BEGIN isAddress -->
<tr title="{msgStructure}" onMouseOut="closeContextHelp();">
<td class='enteteFormulaireDossier'><?_("Type de structure")?></td><td>

<select name='typeStructure'>
	<option value="0"><?_("Aucun")?></option>
	<!-- BEGIN typesStructure -->
	<option value='{afficheAjoutEvenement.isAddress.typesStructure.id}' {afficheAjoutEvenement.isAddress.typesStructure.selected}>{afficheAjoutEvenement.isAddress.typesStructure.nom}</option>
	<!-- END typesStructure -->
</select>

</td><td></td>
</tr>
<!-- END isAddress -->


<tr title="{msgPersonne}" onMouseOut="closeContextHelp();" style='display:{affichePersonnesBlock};'>
<td  class='enteteFormulaireDossier'><?_("Personnes")?></td>
<td>
<select name='personnes[]' id='personnes' multiple='multiple' readonly='readonly' onclick="gestionSelectElement('personnes', '<?_("Voulez vous vraiment supprimer cette personne de cet évènement ?")?>');">
	<!-- BEGIN personnes -->
	<option value='{afficheAjoutEvenement.personnes.id}' {afficheAjoutEvenement.personnes.selected}>{afficheAjoutEvenement.personnes.nom}</option>
	<!-- END personnes -->
</select><input type="button" name="Choisir" value="Choisir" onclick="{onClickChoixPersonne}">
</td><td></td>
</tr>






<tr>
<!-- BEGIN isAddress -->
<td colspan=3 style="border-top:1px #000000 solid;border-bottom:1px #000000 solid;border-left:1px #000000 solid;border-right:1px #000000 solid;">
	<div>
		<table border=0>
		<tr title="{msgTypeEvenement}" onMouseOut="closeContextHelp();">
		<td class='enteteFormulaireDossierTypeEvenement' width='150'><?_("Type d'évènement")?></td>
		<td class='choixFormulaireTypeEvenement'><input type='radio' name='typeGroupeEvenement' id='typeGroupeEvenement1' value="1" onclick="{onClickTypeEvenement1};onChangeTypeEvenement();" {checkedTypeEvenement1}>&nbsp;<?_("Culturel")?>&nbsp;&nbsp;
		<input type='radio' name='typeGroupeEvenement' value = "2" onclick="{onClickTypeEvenement2};onChangeTypeEvenement();" {checkedTypeEvenement2}>&nbsp;<?_("Travaux")?>
		<div id="typeEvenement">
			<select name='typeEvenement' id='selectTypeEvenement' onchange="onChangeTypeEvenement();">
				<!-- BEGIN typesEvenement -->
				<option value='{afficheAjoutEvenement.isAddress.typesEvenement.id}' {afficheAjoutEvenement.isAddress.typesEvenement.selected}>{afficheAjoutEvenement.isAddress.typesEvenement.nom}</option>
				<!-- END typesEvenement -->
			</select>
		</div>
		</td><td>&nbsp;</td>
		</tr>
		</table></div><div id="afficheChampsSupplementairesCulturel" style="{styleChampsSupplementaireCulturel}; width:600px;">
		<table border=0>
		<tr title="{msgISMH}" onMouseOut="closeContextHelp();">
		<td width='150' class='enteteFormulaireDossier'><?_("ISMH (inscrit)")?></td><td><input type="checkbox" name="ISMH" value="ISMH" {ISMHchecked}></td><td></td>
		</tr>
		<tr title="{msgMH}" onMouseOut="closeContextHelp();">
		<td class='enteteFormulaireDossier'><?_("MH (classé)")?></td><td><input type="checkbox" name="MH" value="MH" {MHchecked}></td><td></td>
		</tr>
		</table>
	</div>
    
    <div id="afficheChampsSupplementairesTravaux" style="{styleChampsSupplementaireTravaux}; width:600px;">
		<table border=0>
		
		
		<!-- BEGIN isAdmin -->
		<tr title="{msgNbEtages}" onMouseOut="closeContextHelp();">
		<td class='enteteFormulaireDossier' width='150'><?_("Nombre d'étages")?></td><td><input type='text' value="{nbEtages}" name="nbEtages" style="width:50px;"></td>
		</tr>
		<!-- END isAdmin -->
		
		<!-- BEGIN isNotAdmin -->
		<input type='hidden' name="nbEtages" value=''>
		<!-- END isNotAdmin -->
		
		
		
		<tr  title="{msgCourantArchitectural}" onMouseOut="closeContextHelp();">
		<td class='enteteFormulaireDossier'><?_("Courant architectural")?></td><td class='listeCourantsArchitecturaux'>
			{listeCourantsArchitecturaux}
		</td>
		</tr>
        
		</table>
        
	</div>
    
</td>
<!-- END isAddress -->
</tr>
</table></td>
</tr>
</table>

<!-- END afficheAjoutEvenement -->

<input type='{typeBoutonValidation}' name='{nomBoutonValidation}' value='<?_("Valider")?>' onclick="{onClickBoutonValider}" title="{msgValidation}" id="submitBtn" onMouseOut="closeContextHelp();">

<input type='hidden' value="{evenementGroupeAdresse}" name='evenementGroupeAdresse'>
</form>


<div id='helpCalque' style='background-color:#FFFFFF; border:2px solid #000000;padding:10px;float:left;display:none;'><img src='images/aide.jpg' style="float:left;padding-right:3px;" valign="middle"><div id='helpCalqueTxt' style='padding-top:7px;'></div></div>

{popupVilles}
{popupRues}
{popupSources}
{popupCalendrier}
{popupPersonnes}


{popupAttente}

<script type="text/javascript" >
bbcode_keyup(document.forms['formAjoutDossier'].elements['description'], 'apercu');
setTimeout("majDescription()",1000);


function majDescription()
{
	bbcode_keyup(document.forms['formAjoutDossier'].elements['description'], 'apercu');
	setTimeout("majDescription()",500);
}

function onChangeTypeEvenement()
{
	//document.getElementById('msgDateDebut').innerHTML="Date "+document.getElementById('selectTypeEvenement').options[document.getElementById('selectTypeEvenement').selectedIndex].innerHTML;
}


function initMsgDebut()
{
	onChangeTypeEvenement();
}

initMsgDebut();


setInterval("onChangeTypeEvenement()",1000);

</script>
