<script  >
	function validFormulaire()
	{
		document.getElementById('popupAttente').style.top=getScrollHeight()+200;
		document.getElementById('popupAttente').style.display='block';
		document.getElementById('formImageMulti').submit();
	}
</script>



{recapitulatifAdresses}

{recapitulatifHistoriqueEvenements}

{liensModifEvenements}



<h2><?_("Ajouter une photo")?></h2>

<!-- debug fabien du 29/11/11 on choisi par défaut le choix upload un seul fichier -->
<!--
<p>Plusieurs Images : <input type='radio' name='choixUpload' value='simple' onclick="document.getElementById('formMultiImage').style.display='block';document.getElementById('formUneImage').style.display='none';" />
Une image : <input type='radio' checked="checked" name='choixUpload' value='multi' onclick="document.getElementById('formUneImage').style.display='block';document.getElementById('formMultiImage').style.display='none';" />
</p>
-->


<!--
<div id='formMultiImage' style='display:block;'>

<div id='helpCalque' style='width:400px;background-color:#FFFFFF; border:2px solid #000000;padding:10px;display:block;margin-left:25px;margin-bottom:25px;'><img src='images/aide.jpg' style="float:left;padding-right:3px;" valign="middle"><div id='helpCalqueTxt' style='padding-top:7px;'>{msgUploadMultiple}</div></div>

<div>
{appletJava}
<form action='{formActionAjoutImage}' enctype='multipart/form-data' method='post' name='formImageMulti' id='formImageMulti'>
<p><input type='hidden' name='typeAjout' value='multi' />
<input type='hidden' name='liaisonImage' value='{liaisonImage}' />
<input type='hidden' name='idCourant' value='{idCourant}' />
<input type='hidden' name='cheminUploadMultiple' value='{cheminUploadMultiple}' />
<input type='hidden' name='formulaireRetour' value="{formulaireRetour}" />
A la fin des transferts vous serez automatiquement redirigé vers la page de modification des images ajoutées.</p> 
</form>
</div>
</div>

-->


<!--<div id='formUneImage' style='display:none;'>-->
<form action='{formActionAjoutImage}' enctype='multipart/form-data' method='post' name='formImageSimple' id='formImageSimple'>
<table>
<!--
<tr>
<td>Nom</td><td><input type='text' name='nom' value='{nom}'></td>
</tr>
<tr>
<td>Description</td><td><textarea name='description'>{description}</textarea></td>
</tr>
<tr>
<td>Date de la prise de vue</td><td><input type='text' name='dateCliche' id='dateCliche' value='{dateCliche}'><a href="javascript:show_calendar('document.formImage.dateCliche', document.formImage.dateCliche.value,'{cheminImages}');"><img src="images/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a></td>
</tr>
-->
<tr>
	<td colspan="2">
    <b><?_("Attention :")?></b>
    <ul>
    <li><?_("Si vous êtes l'auteur de l'image, vous devez accepter de la publier sous licence")?> <a href="https://creativecommons.org/licenses/by-sa/3.0/fr/">CC-BY-SA</a>.</li>
    <li><?_("Si vous n'êtes pas l'auteur de l'image, assurez-vous que celle-ci soit libre ou dans le domaine public.")?></li>
    </ul>
    </td>
</tr>
<tr>
	<td><?_("Fichier")?></td><td><input type='file' name='fichier' value='{fichier}' /></td>
</tr>
<tr>
	<td colspan="2"><input onclick="document.getElementById('popupAttente').style.top=getScrollHeight()+200;document.getElementById('popupAttente').style.display='block';" type='submit' value='Upload' name='submitOneImage' />
	<input type='hidden' name='typeAjout' value='simple' />
	<input type='hidden' name='liaisonImage' value='{liaisonImage}' />
	<input type='hidden' name='idCourant' value='{idCourant}' />
	<input type='hidden' name='formulaireAppelant' value="{formulaireAppelant}" />
	</td>
</tr>
</table>
</form>
<!--</div>-->

{popupAttente}
