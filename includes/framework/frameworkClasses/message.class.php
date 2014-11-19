<?php 

require_once 'includes/framework/frameworkClasses/Template.class.php';

class message{
	private $template  ;

	public function __construct($templateFile){
		$this->template = new Template("modules/archi/templates/message/");
		$this->template->set_filenames(array('message'=>'message.tpl'));
	}

	public function buildMessage($messageType, $messageContent){
		$this->template->assign_var('message-type',$messageType);
		$this->template->assign_block_vars('message',array('content'=>$messageContent));
	}

	public function displayMessage(){
		ob_start();
		$this->template->pparse('message');
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}

}
?>