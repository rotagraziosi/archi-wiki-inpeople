<?php
class messageCollection{
	protected $errors,$warnings,$confirmations;
	public function __construct(){
		$this->errors = array();		
		$this->warnings = array();
		$this->confirmations = array();
	}
	public function addError($errorMessage){
		$error = new message('message.tpl');
		$error->buildMessage('error', $errorMessage);
		$this->errors[] = $error;
	}
	public function addWarning($warningMessage){
		$warning = new message('message.tpl');
		$warning->buildMessage('warning', $warningMessage);
		$this->warnings[] = $warning;
	}
	public function addConfirmation($confirmationMessage){
		$confirmation = new message('message.tpl');
		$confirmation->buildMessage('confirmation', $confirmationMessage);
		$this->confirmations[] = $confirmation;		
	}
	
	public function display(){
		foreach ($this->errors as $error){
			echo $error->displayMessage();
		}
		foreach ($this->warnings as $warning){
			echo $warning->displayMessage();
		}
		foreach ($this->confirmations as $confirmation){
			echo $confirmation->displayMessage();
		}
	}
}

?>