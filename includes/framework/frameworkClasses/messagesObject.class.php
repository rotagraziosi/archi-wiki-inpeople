<?php
class messagesObject {
	private $existe  	  = false;
	private $tabFormExiste    = false;
	private $array_messages   = array();
	protected $templateFile ="";
	
	function __construct(){			
	}
	
	/**
	 *
	 * @return number of messages stored
	 */
	function getNbMessages(){
		return count($array_messages);
	}

	/**
	 * Add a message to the array
	 *
	 * @param Single message or array of messages $elem
	 */
	function ajouter($elem){
		echo "ajouter";
		
		if(is_array($elem)){
			foreach ($elem as $e){
				$array_messages[] =$e;
			}
		}
		else
		{
			$array_messages[]   = $elem;
		}
		
	}
	function existe(){
		return count($array_messages)>=1;
	}
}
?>