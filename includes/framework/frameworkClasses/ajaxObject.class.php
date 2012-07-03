<?php
// ajaxObject v1.1 : mise en classe des fonctions

// appel a la fonction ajax en javascript : 
//  appelAjax(chemin vers la page que l'on appelle , id de l'element que l'on met a jour , est ce que le retour se fait dans la fenetre parent ?)

class ajaxObject extends config
{

	function __construct()
	{
		parent::__construct();
	}

	function getAjaxFunctions()
	{
		$html="
		<script  >
			var http; // Notre objet XMLHttpRequest
			var byIDContainer;
			var returnToParent;
			var returnExecJavascript;
			returnExecJavascript=false;
			function createRequestObject()
			{
			    var http;
			    if (window.XMLHttpRequest)
			    { // Mozilla, Safari, IE7 ...
			        http = new XMLHttpRequest();
			    }
			    else if (window.ActiveXObject)
			    { // Internet Explorer 6
			        http = new ActiveXObject('Microsoft.XMLHTTP');
			    }
			    return http;
			}

			function appelAjax(url,containerID,isReturnToParent)
			{
				//alert(url);
			    returnToParent=isReturnToParent;
				byIDContainer = containerID;
			    http = createRequestObject();
			    http.open('GET', url, true);
			    http.onreadystatechange = handleAJAXReturn;
			    http.send(null);
			}
			
			
			function appelAjaxReturnJs(url,containerID,isReturnToParent)
			{
				//alert(url);
				returnExecJavascript = true;
			    returnToParent=isReturnToParent;
				byIDContainer = containerID;
			    http = createRequestObject();
			    http.open('GET', url, true);
			    http.onreadystatechange = handleAJAXReturn;
			    http.send(null);
			}

			function handleAJAXReturn()
			{
			    if (http.readyState == 4)
			    {
			        if (http.status == 200)
			        {
			            if(returnToParent==true)
						{
							if(returnExecJavascript)
							{
								eval(http.responseText);
							}
							else
							{
								parent.document.getElementById(byIDContainer).innerHTML = http.responseText;
							}
						}
						else
						{
							if(returnExecJavascript)
							{
								eval(http.responseText);
							}
							else
							{
								document.getElementById(byIDContainer).innerHTML = http.responseText;
							}
						}
					}
			        else
			        {
			            alert('Erreur lors de l\'appel Ajax');
			        }
			    }
			}
		</script>
		";
		return $html;
	}
}
?>