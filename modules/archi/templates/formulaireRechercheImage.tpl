<form action="{formAction}" name="rechercheImages" id="rechercheImages" enctype="multipart/form-data" method='POST'>
<H1><?_("Recherche images")?></H1>
<table>
<tr>
<td><?_("Recherche par intitulé/description")?></td>
<td align='right'><input type='text' name="motCle" value="{motCle}"></td>
<td>&nbsp;</td>
</tr>
<tr>
<td><?_("Recherche par date d'upload :")?></td>
<td><?_("du :")?> <input type='text' name="dateUploadDu" id="dateUploadDu" value="{dateUploadDu}"><a href='#' onclick="document.getElementById('paramChampAppelantDate').value='dateUploadDu';document.getElementById('calqueDate').style.display='block';"><?_("Choisir")?></a></td>
<td><?_("au :")?> <input type='text' name="dateUploadAu" id="dateUploadAu" value="{dateUploadAu}"><a href='#' onclick="document.getElementById('paramChampAppelantDate').value='dateUploadAu';document.getElementById('calqueDate').style.display='block';"><?_("Choisir")?></a></td>
</tr>
<tr>
<td><?_("Recherche par date de prise de vue :")?></td>
<td><?_("du :")?> <input type='text' name="datePriseDeVueDu" id="datePriseDeVueDu" value="{datePriseDeVueDu}"><a href='#' onclick="document.getElementById('paramChampAppelantDate').value='datePriseDeVueDu';document.getElementById('calqueDate').style.display='block';"><?_("Choisir")?></a></td>
<td><?_("au :")?> <input type='text' name="datePriseDeVueAu" id="datePriseDeVueAu" value="{datePriseDeVueAu}"><a href='#' onclick="document.getElementById('paramChampAppelantDate').value='datePriseDeVueAu';document.getElementById('calqueDate').style.display='block';"><?_("Choisir")?></a></td>
</tr>
</table>
<input type='button' onclick="document.getElementById('pageCourante').value='1'; document.getElementById('rechercheImages').submit();" value='Rechercher' name='rechercheImage'>

<input type='hidden' id="pageCourante" name="pageCourante" value="{pageCourante}">
</form>

{popupCalendrier}
