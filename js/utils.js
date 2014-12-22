/**
 * utils.js
 * 
 * Bunch of functions used here and there
 * 
 * @author Antoine Rota Graziosi - InPeople 2014
 * 
 */


/**
 * add VueSur elt on photo
 */
function addVueSur(idEvenementGroupeAdresse , idAdresse , nom){
	
	/*
	 * Adding remove link to the div
	 */
	identifiantRetour = parent.document.getElementById('identifiantRetour').value;
	idTargetDiv = "listeVueSurDiv"+identifiantRetour;
	param1 = idAdresse+"_"+idEvenementGroupeAdresse;
	param2 = identifiantRetour;
	
	retirerVueParam = param1 +","+ param2;
	contentToAdd = "<p>" +nom + "<a style='cursor:pointer;' onclick='retirerVueSur("+idAdresse+","+ identifiantRetour +");' > (-)</a></p>";
	
	var div = document.createElement('div');
	div.innerHTML = contentToAdd;
	
	parent.document.getElementById(idTargetDiv).appendChild(div) ;
	
	/*
	 * Adding correct id to the option input
	 */
	
	optionToAdd = "<option value=\'"+param1+"\' SELECTED>"+nom+"</option>";
	idTargetDiv = "prisDepuis"+identifiantRetour;
	parent.document.getElementById(idTargetDiv).innerHTML += optionToAdd;
	
}	


function addPrisDepuis(idEvenementGroupeAdresse , idAdresse , nom){
	identifiantRetour = parent.document.getElementById('identifiantRetour').value;
	idTargetDiv = "listePrisDepuisDiv"+identifiantRetour;
	
	param1 = idAdresse+"_"+idEvenementGroupeAdresse;
	param2 = identifiantRetour;
	
	retirerPrisDepuis = param1 +","+ param2;
	contentToAdd = "<p>" +nom + "<a style='cursor:pointer;' onclick='retirerPrisDepuis("+idAdresse+","+identifiantRetour+");' > (-)</a></p>";

	var div = document.createElement('div');
	div.innerHTML = contentToAdd;
	
	parent.document.getElementById(idTargetDiv).appendChild(div) ;
	
	
	/*
	 * Add option to the select form input
	 */
	optionToAdd = "<option value=\'"+param1+"\' SELECTED>"+nom+"</option>";
	idTargetDiv = "vueSur"+identifiantRetour;
	parent.document.getElementById(idTargetDiv).innerHTML += optionToAdd;
	console.log();
}

