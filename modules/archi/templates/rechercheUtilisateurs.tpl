<script  >

var current=0;

function changeCurrentPoint()
{
	var t = new Array();
	t[0] = " ...";
	t[1] = ". ..";
	t[2] = ".. .";
	t[3] = "... ";
	document.getElementById('points').innerHTML=t[current];
	current++;
	if(current==4)
	{
		current=0;
	}
	
	setTimeout("changeCurrentPoint()", 100);
}
</script><table {tableHtmlCode} border=0 cellspacing=0 cellpadding=0><tr><td><form style='padding:0;margin:0;' action='{formAction}' name='{formName}' id='{formName}' method='POST' enctype='multipart/form-data'>
<!-- BEGIN hiddenFields -->
{hiddenFields.field}
<!-- END hiddenFields -->
<!-- BEGIN fields -->
{fields.name}{fields.field}{fields.error}{codeHtmlBeforeSubmitButton}
<input type='submit' value='<?_("Envoyer")?>' name='valider' onclick="document.getElementById('msgFormulaireGenerator').innerHTML='Chargement en cours ';setTimeout('changeCurrentPoint()', 100);">
<span id='msgFormulaireGenerator'></span><span id='points'></span>
<!-- END fields -->
</form></td></tr></table>
