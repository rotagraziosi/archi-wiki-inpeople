	var http; // Notre objet XMLHttpRequest
	var byIDContainer;
	var returnToParent;
	
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

	function handleAJAXReturn()
	{
	    if (http.readyState == 4)
	    {
	        if (http.status == 200)
	        {
	            if(returnToParent==true)
				{
					parent.document.getElementById(byIDContainer).innerHTML = http.responseText;
				}
				else
				{
					document.getElementById(byIDContainer).innerHTML = http.responseText;
				}
			}
	        else
	        {
	            alert('Erreur lors de l\'appel Ajax');
	        }
	    }
	}
