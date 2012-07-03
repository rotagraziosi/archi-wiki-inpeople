<script>
var current{idFormObject}=0;

function changeCurrentPoint{idFormObject}()
{
	var t = new Array();
	t[0] = " ...";
	t[1] = ". ..";
	t[2] = ".. .";
	t[3] = "... ";
	document.getElementById('points{idFormObject}').innerHTML=t[current{idFormObject}];
	current{idFormObject}++;
	if(current{idFormObject}==4)
	{
		current{idFormObject}=0;
	}
	
	setTimeout("changeCurrentPoint{idFormObject}()", 100);
}
</script>

<table {tableHtmlCode}>
<tr>
<td>
<h1>{titrePage}</h1>
<form action='{formAction}' name='{formName}' id='{formName}' method='POST' enctype='multipart/form-data'>
{codeHtmlInFormBeforeFields}
<table>
<!-- BEGIN fields -->
<tr><td>{fields.name}</td><td>{fields.field}</td><td>{fields.error}</td></tr>
<!-- END fields -->
<tr><td colspan=3>{codeHtmlBeforeSubmitButton}<input type='submit' value='{formButtonName}' name='valider' id='{submitButtonId}' onclick="document.getElementById('msgFormulaireGenerator{idFormObject}').innerHTML='Chargement en cours ';setTimeout('changeCurrentPoint{idFormObject}()', 100);{onClickSubmitButton}" {codeHtmlSubmitButton}>{codeHtmlAfterSubmitButton}<span id='msgFormulaireGenerator{idFormObject}'></span><span id='points{idFormObject}'></span></td></tr>
</table>
<!-- BEGIN hiddenFields -->
{hiddenFields.field}
<!-- END hiddenFields -->
{codeHtmlInFormAfterFields}
</form>
</td>
</tr>
</table>
