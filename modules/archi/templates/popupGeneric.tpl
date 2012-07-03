<div id='{divIdPopup}' class="calque" style='display:none;background-color:#C0C0C0;position:absolute;top:{top}px; left:{left}px;'>
		
		{hiddenFields}
		
		<input type='hidden' name='identifiantRetour' id='identifiantRetour' value=''>
		<table>
		<tr>
			<td class='tdEntetePopup'><div class='texteEntetePopup'>{titrePopup}</div><div class='boutonFermerPopup'><input type='button' value='Fermer' name='Fermer' onclick="{codeJsFermer}document.getElementById('{divIdPopup}').style.display='none';"></div></td>
		</tr>
		<tr>
			<td style='padding:0px;margin:0px;' id='{tdIdPopup}' width='{width}' height='{height}'><iframe style='border:1px #000000 solid;padding:0px;margin:0px;' id='{iFrameIdPopup}' frameborder=0 width='{width}' height='{height}' src='{lienSrcIFrame}'></iframe></td>
		</tr>
		</table>
</div>
