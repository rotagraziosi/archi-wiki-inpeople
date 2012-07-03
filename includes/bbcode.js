var isMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
var regexp = new RegExp("[\r]","gi");

function bbcode_ajout_balise(selec, nomForm, nomTextarea)
{
	if (isMozilla) 
	{
	// Si on est sur Mozilla

		oField = document.forms[nomForm].elements[nomTextarea];

		objectValue = oField.value;

		deb = oField.selectionStart;
		fin = oField.selectionEnd;

		objectValueDeb = objectValue.substring( 0 , oField.selectionStart );
		objectValueFin = objectValue.substring( oField.selectionEnd , oField.textLength );
		objectSelected = objectValue.substring( oField.selectionStart ,oField.selectionEnd );

	//	alert("Debut:'"+objectValueDeb+"' ("+deb+")\nFin:'"+objectValueFin+"' ("+fin+")\n\nSelectionné:'"+objectSelected+"'("+(fin-deb)+")");
			
		oField.value = objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]" + objectValueFin;
		oField.selectionStart = objectValueDeb.length;
		oField.selectionEnd = (objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]").length;
		oField.focus();
		oField.setSelectionRange(
			objectValueDeb.length + selec.length + 2,
			objectValueDeb.length + selec.length + 2);
	}
	else
	{
	// Si on est sur IE
		
		oField = document.forms[nomForm].elements[nomTextarea];
		var str = document.selection.createRange().text;

		if (str.length>0)
		{
		// Si on a selectionné du texte
			var sel = document.selection.createRange();
			sel.text = "[" + selec + "]" + str + "[/" + selec + "]";
			sel.collapse();
			sel.select();
		}
		else
		{
			oField.focus(oField.caretPos);
		//	alert(oField.caretPos+"\n"+oField.value.length+"\n")
			oField.focus(oField.value.length);
			oField.caretPos = document.selection.createRange().duplicate();
			
			var bidon = "%~%";
			var orig = oField.value;
			oField.caretPos.text = bidon;
			var i = oField.value.search(bidon);
			oField.value = orig.substr(0,i) + "[" + selec + "][/" + selec + "]" + orig.substr(i, oField.value.length);
			var r = 0;
			for(n = 0; n < i; n++)
			{if(regexp.test(oField.value.substr(n,2)) == true){r++;}};
			pos = i + 2 + selec.length - r;
			//placer(document.forms['news'].elements['newst'], pos);
			var r = oField.createTextRange();
			r.moveStart('character', pos);
			r.collapse();
			r.select();

		}
	}
}

function bbcode_apercu (str) {
	str = str.split("&").join("&amp;").split("<").join("&lt;").split(">").join("&gt;");
	var
	tab = [
			["\\[urlExterne=\"http\\://(.*?)\"\\](.*?)\\[\/urlExterne\\]", "<a href=\"http://$1\" target=\"_blank\">$2</a>"],
			["\\[urlExterne=http\\://(.*?)\\](.*?)\\[\/urlExterne\\]", "<a href=\"http://$1\" target=\"_blank\">$2</a>"],
			["\\[urlExterne=\"(.*?)\"\\](.*?)\\[\/urlExterne\\]", "<a href=\"$1\" target=\"_blank\">$2</a>"],
			["\\[urlExterne=\'(.*?)\'\\](.*?)\\[\/urlExterne\\]", "<a href=\"$1\" target=\"_blank\">$2</a>"],
			["\\[urlExterne\\](.*?)\\[\/urlExterne\\]", "<a href=\"http://$1\" target=\"_blank\">$1</a>"],
			["\\[url=\"http\\://(.*?)\"\\](.*?)\\[\/url\\]", "<a href=\"http://$1\">$2</a>"],
			["\\[url\\]http\\://(.*?)\\[\/url\\]", "<a href=\"http://$1\">$1</a>"],
			["\\[url=\"(.*?)\"\\]\"(.*?)\"\\[\/url\\]", "<a href=\"http://$1\">$2</a>"],
			["\\[url\\](.*?)\\[\/url\\]", "<a href=\"http://$1\">$1</a>"],
			["\\[url=http\\://(.*?)\\](.*?)\\[\/url\\]", "<a href=\"http://$1\">$2</a>"],
			["\\[url\\]http\\://(.*?)\\[\/url\\]", "<a href=\"http://$1\">$1</a>"],
			["\\[url\\](.*?)\\[\/url\\]", "<a href=\"http://$1\">$1</a>"],
			["\\[url=(.*?)\\](.*?)\\[\/url\\]", "<a href=\"http://$1\">$2</a>"],
			["\\[b\\](.*?)\\[/b\\]", "<strong>$1</strong>"],
			["\\[u\\](.*?)\\[/u\\]", "<u>$1</u>"],
			["\\[i\\](.*?)\\[/i\\]", "<em>$1</em>"],
			["\\[quote\\](.*?)\\[/quote\\]","\"$1\""],
	["\\n","<br />"]
	];
	for (var i=0; i < tab.length; i++)
	{
		str = str.replace(new RegExp(tab[i][0],"g"), tab[i][1]);
	}
	return str;
}

function bbcode_keyup (champ, id) {

	var str = bbcode_apercu(champ.value);
	document.getElementById(id).innerHTML = str;
}

