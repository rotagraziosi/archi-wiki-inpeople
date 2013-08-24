{recapitulatifAdresses}

{recapitulatifHistoriqueEvenements}

{liensModifEvenements}

<h2>{proprietaireImages}</h2>
<form name='modifImage' id='modifImage' action='{actionFormImage}' enctype='multipart/form-data' method='post'>



<!-- BEGIN listePhotos -->
<table border=0>
<tr>
    <td>
        <table border=0>
        <tr>
        <td><img src="{listePhotos.urlImage}" /></td>
        <td style='font-size:14px;'>
        <div title="{msgButtonDateUpload}"><?_("Date d'upload de l'image :")?> <input type='hidden' value='{listePhotos.dateUpload}' name='dateUpload_{listePhotos.idHistoriqueImage}' />{listePhotos.dateUpload}</div><br><br>
        <a onclick="{listePhotos.onClickPopupPrisDepuis}" style='cursor:pointer;' title="{msgButtonPrisDepuis}"><?_("Pris depuis")?></a><br>
        
        <div id='listePrisDepuisDiv{listePhotos.idHistoriqueImage}'>{listePhotos.listePrisDepuisDiv}</div>
        
        <a onclick="{listePhotos.onClickPopupVueSur}" style='cursor:pointer;' title="{msgButtonVueSur}"><?_("Vue sur")?></a>
        
        <div id='listeVueSurDiv{listePhotos.idHistoriqueImage}'>{listePhotos.listeVueSurDiv}</div>
        
        {listePhotos.popupPrisDepuis}
        
        {listePhotos.popupVueSur}
        
        </td>
        </tr>
        </table>
    </td>
</tr>
<tr>
<td>
<table>
    <tr title="{msgButtonMiseEnForme}">
    <td class='enteteFormulaireMajPhoto'><?_("Mise en forme")?></td>
    <td>
    <input type="button" value="b" style="width:50px;font-weight:bold" onclick="bbcode_ajout_balise('b', 'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgGras}"/>
    <input type="button" value="i" style="width:50px;font-style:italic" onclick="bbcode_ajout_balise('i', 'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgItalic}"/>
    <input type="button" value="u" style="width:50px;text-decoration:underline;" onclick="bbcode_ajout_balise('u', 'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgUnderline}"/>
    <input type="button" value="quote" style="width:50px" onclick="bbcode_ajout_balise('quote', 'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgQuotes}"/>
    <!--<input type="button" value="code" style="width:50px" onclick="bbcode_ajout_balise('code', 'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgCode}" onkeyup="bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');"/>-->
    <input type="button" value="url interne"  style="width:75px" onclick="bbcode_ajout_balise('url',  'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgUrl}" onkeyup="bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');"/>
    <input type="button" value="url externe"  style="width:80px" onclick="bbcode_ajout_balise('urlExterne',  'modifImage', 'description_{listePhotos.idHistoriqueImage}');bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');" title="{msgUrl}" onkeyup="bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');"/>
    </td>
    </tr>
    <tr title="{msgButtonDescription}">
    <td class='enteteFormulaireMajPhoto'><?_("Description")?></td><td><input type='hidden' name='nom_{listePhotos.idHistoriqueImage}' value='{listePhotos.nom}' /><textarea cols='40' rows='8' name='description_{listePhotos.idHistoriqueImage}' onkeyup="bbcode_keyup(this,'apercu_{listePhotos.idHistoriqueImage}');">{listePhotos.description}</textarea></td>
    </tr>
    <tr>
    <td class='enteteFormulaireMajPhoto'><?_("Apercu")?></td>
    <td><div id='apercu_{listePhotos.idHistoriqueImage}'></div></td>
    </tr>
    <tr title="{msgButtonDatePriseDeVue}">
    <td class='enteteFormulaireMajPhoto'><?_("Date de la prise de vue")?></td><td><input type='text' name='dateCliche_{listePhotos.idHistoriqueImage}' id='dateCliche_{listePhotos.idHistoriqueImage}' value='{listePhotos.dateCliche}' />
    <input type="button" value="Choisir" onclick="{listePhotos.onClickDateCliche}">&nbsp;<input type='checkbox' name='isDateClicheEnviron_{listePhotos.idHistoriqueImage}' id='isDateClicheEnviron_{listePhotos.idHistoriqueImage}' value='1' {listePhotos.checkIsDateClicheEnviron} /> <label for="isDateClicheEnviron_{listePhotos.idHistoriqueImage}"><?_("environ")?></label>
    </td>
    </tr>
    <!-- BEGIN isDisplaySource -->
    <tr title="{msgButtonSource}">
        <td class='enteteFormulaireMajPhoto'><?_("Source")?></td>
        <td>
        <input type="text" value="{listePhotos.nomSource}" name="source_{listePhotos.idHistoriqueImage}txt" id="source_{listePhotos.idHistoriqueImage}txt" readonly>
        <input type="hidden" value="{listePhotos.idSource}" name="source_{listePhotos.idHistoriqueImage}" id="source_{listePhotos.idHistoriqueImage}">
        <input type="button" name="Choisir" value="Choisir" onclick="{listePhotos.onClickBoutonChoixSource}"><input type='button' name='razSource' value='Aucune source' onclick="document.getElementById('source_{listePhotos.idHistoriqueImage}txt').value='';document.getElementById('source_{listePhotos.idHistoriqueImage}').value='0';">
        </td><td>{listePhotos.source-error}</td></tr>
    <!-- END isDisplaySource -->
    <!-- BEGIN isNoDisplaySource -->
        <input type='hidden' value="{listePhotos.idSource}" name="source_{listePhotos.idHistoriqueImage}">
    <!-- END isNoDisplaySource -->
    <!-- BEGIN isDisplayNumeroArchive -->
    <tr title="{msgButtonNumeroArchive}">
        <td class='enteteFormulaireMajPhoto'><?_("N° archive")?></td>
        <td>
        <input type="text" value="{listePhotos.numeroArchive}" name="numeroArchive_{listePhotos.idHistoriqueImage}" id="numeroArchive_{listePhotos.idHistoriqueImage}">
        </td><td>{listePhotos.numeroArchive-error}</td>
    </tr>
    <!-- END isDisplayNumeroArchive -->
    <!-- BEGIN isNoDisplayNumeroArchive -->
        <input type="hidden" value="{listePhotos.numeroArchive}" name="numeroArchive_{listePhotos.idHistoriqueImage}" id="numeroArchive_{listePhotos.idHistoriqueImage}">
    <!-- END isNoDisplayNumeroArchive -->
    <tr style='display:none;' title="{msgEvenementsLies}">
    <td class='enteteFormulaireMajPhoto'><?_("Evenements liées")?></td>
    <td>
        <select name='listeEvenements_{listePhotos.idHistoriqueImage}[]' multiple id='listeEvenements_{listePhotos.idHistoriqueImage}'>
            <!-- BEGIN evenements -->
                <option value="{listePhotos.evenements.value}" selected>{listePhotos.evenements.nom}</options>
            <!-- END evenements -->
        </select>
    <!--<a href="{listePhotos.evenementUrl}" onclick="{listePhotos.evenementOnClick}">Lier l'image à un ou plusieurs évènement(s)</a>-->
    </td>
    <td>{listePhotos.listeAdresses-error}</td>
    </tr>

    <tr style='display:none;' title="{msgButtonRemplacer}">
        <td class='enteteFormulaireMajPhoto'><?_("Remplacer l'image par :")?></td><td><input type='file' name='fichierRemplace{listePhotos.idHistoriqueImage}' value='' /></td>
    </tr>
    <tr>
        <td class='enteteFormulaireMajPhoto'><?_("Auteur")?></td>
        <td>
        <input type="text" {listePhotos.enableAuthor} placeholder="{listePhotos.nomUpload}" name="auteur_{listePhotos.idHistoriqueImage}" id="auteur_{listePhotos.idHistoriqueImage}" value="{listePhotos.nomAuteur}">
        </td>
    </tr>
    <tr>
        <td class='enteteFormulaireMajPhoto'><?_("Licence")?></td>
        <td>
        {listePhotos.selectLicence}
        </td>
    </tr>
    <!-- BEGIN canModifyTags -->
    <tr>
        <td class='enteteFormulaireMajPhoto'><label for="tags_{listePhotos.idHistoriqueImage}"><?_("Tags (séparés par une virgule)")?></label></td>
        <td>
        <input type="text" id="tags_{listePhotos.idHistoriqueImage}" name="tags_{listePhotos.idHistoriqueImage}" value="{listePhotos.tags}" />
        </td>
    </tr>
    <!-- END canModifyTags -->
    </table>
    <!-- BEGIN canNotModifyTags -->
    <input type="hidden" id="tags_{listePhotos.idHistoriqueImage}" name="tags_{listePhotos.idHistoriqueImage}" value="{listePhotos.tags}" />
    <!-- END canNotModifyTags -->
    <select name='prisDepuis{listePhotos.idHistoriqueImage}[]' id='prisDepuis{listePhotos.idHistoriqueImage}' MULTIPLE style='display:none;'>
    {listePhotos.selectPrisDepuis}
    </select>
    
    <select name='vueSur{listePhotos.idHistoriqueImage}[]' id='vueSur{listePhotos.idHistoriqueImage}' MULTIPLE style='display:none;'>
    {listePhotos.selectVueSur}
    </select>
    
</td>
</tr>
</table>

<input type='hidden' name='idHistoriqueImage_{listePhotos.idHistoriqueImage}' value='{listePhotos.idHistoriqueImage}' />
<input type='hidden' name='idImage_{listePhotos.idHistoriqueImage}' value='{listePhotos.idImage}' />
<input type='hidden' name='idCourant_{listePhotos.idHistoriqueImage}' value='{listePhotos.idCourant}' />
<input type='hidden' name='typeLiaisonImage_{listePhotos.idHistoriqueImage}' value='{listePhotos.typeLiaisonImage}' />


<!-- END listePhotos -->




{msgPasdImage}

<!-- BEGIN isImages -->
<input type='hidden' value='{listeId}' name='listeId' />
<input onclick="document.getElementById('popupAttente').style.top=getScrollHeight()+200;document.getElementById('popupAttente').style.display='block';" name='modifierImage' value='Modifier' type='submit' />
<!-- END isImages -->
</form>


<div id='helpCalque' style='background-color:#FFFFFF; border:2px solid #000000;padding:10px;float:left;display:none;'><img src='images/aide.jpg' style="float:left;padding-right:3px;" valign="middle"><div id='helpCalqueTxt' style='padding-top:7px;'></div></div>

{popupChoixAdresse}
{popupChoixEvenement}
{popupChoixVille}
{popupChoixRue}
{popupChoixSource}
{popupCalendrier}
{popupAttente}



<script type="text/javascript" >
<!-- BEGIN listePhotos -->
bbcode_keyup(document.forms['modifImage'].elements['description_{listePhotos.idHistoriqueImage}'], 'apercu_{listePhotos.idHistoriqueImage}');
<!-- END listePhotos -->
setTimeout("majDescription()",1000);


function majDescription()
{
    <!-- BEGIN listePhotos -->
    bbcode_keyup(document.forms['modifImage'].elements['description_{listePhotos.idHistoriqueImage}'], 'apercu_{listePhotos.idHistoriqueImage}');
    <!-- END listePhotos -->
    setTimeout("majDescription()",500);
}



</script>
