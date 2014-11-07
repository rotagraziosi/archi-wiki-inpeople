<?php

// classe de gestion des erreurs
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - separation de la classe d'erreurs de l'objet config

class objetErreur extends messagesObject
{
	private $existe  	  = false;
	private $tabFormExiste    = false;
	private $array_erreurs   = array();
	public  $nbMessages= 0;
	private $erreursDansFormulaire = array();
	
	
	function __construct(){
		parent::__construct();
		$this->templateFile = 'listeErreurs.tpl';
	}

	function getNbErreurs()
	{
		return $this->nbMessages;
	}
	
	function ajouter($elem)
	{
		if(is_array($elem))
		{
			$this->erreursDansFormulaire = $elem;
			$this->tabFormExiste         = true;
		}
		else
		{
			$this->array_erreurs[]   = $elem;
			$this->existe      = true;
			$this->nbMessages += 1;
		}
	}

	function getErreursFromFormulaire()
	{
		return $this->erreursDansFormulaire;
	}
	
	
	function afficher()
	{
		$html = '';
		
		if ($this->existe())
		{
			$t = new Template('modules/archi/templates/');
			$t->set_filenames(array('afficherErreurs'=>'listeErreurs.tpl' ));
			foreach($this->array_erreurs AS $message)
			{
				$t->assign_block_vars('erreur', array('message'=>$message));
			}

			ob_start();
			$t->pparse('afficherErreurs');
			$html = ob_get_contents();
			ob_get_clean();
		}
		
		return $html;
	}

	function existe()
	{
		if ($this->existe === true)
			return true;
		else
			return false;
	}

	// dans le cas ou on ajoute toute une configuration de formulaire , on peut ainsi la recuperer et recuperer les champs "error" de ce formulaire, 
	// on sait donc qu'il y a une erreur dans le formulaire , car on a utilis� la fonction d'ajout d'erreur , 
	// et l'erreur se recupere donc directement dans ce tableau , ou meme on peut afficher directement les erreurs dans le template en parcourant le tableau de config de ce formulaire
	function tabFormExiste()
	{
		if ($this->tabFormExiste === true)
			return true;
		else
			return false;
	}
}

?>