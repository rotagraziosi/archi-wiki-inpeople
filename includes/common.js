
function gestionSelectElement(elementId,msgConfirm)
{field=document.getElementById(elementId);if(confirm(msgConfirm))
{selectedIndexASupprimer=field.options.selectedIndex;indice=new Array();texte=new Array();for(i=0;i<field.length;i++)
{indice[i]=field.options[i].value;texte[i]=field.options[i].text;}
field.innerHTML='';j=0;for(i=0;i<indice.length;i++)
{if(i!=selectedIndexASupprimer)
{field.options[j]=new Option(texte[i],indice[i]);j++;}}}
for(i=0;i<field.length;i++)
{field.options[i].selected=true;}}