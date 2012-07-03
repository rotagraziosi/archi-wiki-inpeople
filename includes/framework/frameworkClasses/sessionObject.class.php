<?php
// classe de gestion des sessions
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - separation de la classe de session de l'objet config

// objet permettant de manipuler la session

class objetSession extends config
{
		function __construct()
		{
			
		}

		function addToSession($elem,$val)
		{
			$_SESSION[$elem]=$val;
		}
		
		function deleteFromSession($elem)
		{
			unset($_SESSION[$elem]);
		}
		
		function getFromSession($elem)
		{
			$ret='';
			if(isset($_SESSION[$elem]))
				$ret = $_SESSION[$elem];
				
			return $ret;
		}
		
		function isInSession($elem)
		{
			return isset($_SESSION[$elem]);
		}
		
		function getSessionArray()
		{
			return $_SESSION;
		}
}

?>
