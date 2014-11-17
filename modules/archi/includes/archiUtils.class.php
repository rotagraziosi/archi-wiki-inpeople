<?php

/**
 * Class archiUtils
 *
 * Utils function to reuse in several places
 *
 * @author Antoine Rota Graziosi / InPeople
 *
 */

class archiUtils extends config{

	function __construct(){
		parent::__construct();
	}
	
	public function afficheFormulaire($tabTravail = array(), $afficheTitreEtLiens = 1,$params = array())
	{
		$t=new Template('modules/archi/templates/');
		$t->set_filenames(array('rechercheSimple'=>'rechercheSimple.tpl'));
	
	
		$arrayModeAffichage = array();
		if(isset($this->variablesGet['noHeaderNoFooter']))
		{
			$t->assign_block_vars('noHeaderNoFooter',array());
			$arrayModeAffichage['noHeaderNoFooter']=1;
		}
	
	
		if(isset($this->variablesGet['modeAffichage']))
		{
			$t->assign_block_vars('modeAffichage',array('value'=>$this->variablesGet['modeAffichage']));
			$arrayModeAffichage['modeAffichage']=$this->variablesGet['modeAffichage'];
		}
	
		if(isset($params['isCheckBoxAfficheResultatSurCarteChecked']) && $params['isCheckBoxAfficheResultatSurCarteChecked']==true)
		{
			$t->assign_vars(array('checkBoxAfficheResultatsSurCarte'=>'checked'));
		}
	
		$idEvenementADeplacer=0;
		// parametre idEvenementADeplacer dans le cas d'un mode d'affichage 'popupDeplacerEvenementVersGroupeAdresse'
		if(isset($this->variablesGet['idEvenementADeplacer']))
		{
			$idEvenementADeplacer=$this->variablesGet['idEvenementADeplacer'];
		}
	
		if(isset($this->variablesPost['idEvenementADeplacer']))
		{
			$idEvenementADeplacer=$this->variablesPost['idEvenementADeplacer'];
		}
	
		if($idEvenementADeplacer!=0)
		{
			$t->assign_block_vars('parametres',array('nom'=>'idEvenementADeplacer','id'=>'idEvenementADeplacer','value'=>$idEvenementADeplacer));
		}
	
	
		$t->assign_vars(array('formAction'=>$this->creerUrl('','recherche')));
		$t->assign_vars(array('urlRechercheAvancee'=>$this->creerUrl('','rechercheAvancee')));
		//$t->assign_vars(array('rechercheAvEvenement'=>$this->creerUrl('','rechercheAvEvenement',$arrayModeAffichage)));
		//$t->assign_vars(array('rechercheAvAdresse'=>$this->creerUrl('','rechercheAvAdresse',$arrayModeAffichage)));
		//$t->assign_vars(array('rechercheParCarte'=>$this->creerUrl('','rechercheParCarte',$arrayModeAffichage)));
		/**************
		**  Affichage des Erreurs et valeurs
		**/
		if (count($tabTravail) > 0)
		{
			foreach($tabTravail AS $name => $value)
			{
				$t->assign_vars(array($name => stripslashes(htmlspecialchars($value["value"]))));
				if($value["error"]!='')
				{
					$t->assign_vars(array($name."-error" => $value["error"]));
				}
			}
		}
	
		if ($afficheTitreEtLiens === 1)
		{
			$t->assign_block_vars('titreEtLiens', array());
			$t->assign_vars(array("motCleStyle"=>"width:300px;"));
		}
	
		if(!isset($params['noDisplayRechercheAvancee']) || $params['noDisplayRechercheAvancee']==false)
		{
			$t->assign_block_vars('displayRechercheAvancee',array());
		}
	
		if(!isset($params['noDisplayCheckBoxResultatsCarte']) || $params['noDisplayCheckBoxResultatsCarte']==false)
		{
			$t->assign_block_vars('displayCheckBoxResultatsCarte',array());
		}
	
		ob_start();
		$t->pparse('rechercheSimple');
		$html = ob_get_contents();
		ob_end_clean();
	
		return $html;
	}
	
	
	public function afficheFormulaireAdresse($tabTravail = array(),$modeAffichage='', $params = array())
	{
		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('rechercheFormulaire'=>'rechercheAvanceeAdresseFormulaire.tpl')));
	
		$adresse = new archiAdresse();
	
		switch($modeAffichage)
		{
			case 'calqueEvenement':
			case 'calqueImage':
			case 'calqueImageChampsMultiples':
			case 'calqueImageChampsMultiplesRetourSimple':
				$t->assign_block_vars('isCalque',array());
				$t->assign_vars(array('modeAffichage'=>$modeAffichage));
				break;
			case 'popupRechercheAdressePrisDepuis':
				// cas de la popup sur le lien prisDepuis sur le formulaire de modif d'une photo
				$t->assign_block_vars('modeAffichage',array('value'=>'popupRechercheAdressePrisDepuis'));
				$t->assign_block_vars('archiAffichage',array('value'=>'rechercheAvAdresse'));
				break;
			case 'popupRechercheAdresseVueSur':
				// cas de la popup sur le lien prisDepuis sur le formulaire de modif d'une photo
				$t->assign_block_vars('modeAffichage',array('value'=>'popupRechercheAdresseVueSur'));
				$t->assign_block_vars('archiAffichage',array('value'=>'rechercheAvAdresse'));
				break;
			case 'popupDeplacerEvenementVersGroupeAdresse':
				$t->assign_block_vars('modeAffichage',array('value'=>'popupDeplacerEvenementVersGroupeAdresse'));
				break;
			case 'rechercheAvancee':
				//$t->assign_block_vars('archiAffichage',array('value'=>'resultatsRechercheAvancee'));
				$t->assign_block_vars('archiAffichage',array('value'=>'advancedSearch'));
				break;
			case 'ajouterInteret':
				//$t->assign_block_vars('archiAffichage',array('value'=>'advancedSearch'));
				break;
			default:
				$t->assign_block_vars('noCalque',array());
				break;
		}
	
	
	
		if(isset($params['titre']) && $params['titre']!='')
		{
			$t->assign_vars(array('titre'=>$params['titre']));
		} else {
			$t->assign_vars(array('titre'=>"Recherche Adresse"));
		}
	
		if(!isset($params['noRechercheParMotCle']) || $params['noRechercheParMotCle'] == false)
		{
			$t->assign_block_vars("afficheRechercheMotCle",array());
		}
	
		if(isset($this->variablesGet['noHeaderNoFooter']))
		{
			$t->assign_block_vars('noHeaderNoFooter',array());
		}
	
		if(isset($this->variablesGet['idEvenementADeplacer']))
		{
			echo $this->variablesGet['idEvenementADeplacer'];
		}
	
		if(!isset($params['noFormElement']) || $params['noFormElement']==false)
		{
			$t->assign_block_vars('useFormElements',array());
		}
		//debug( $adresse->afficheChoixAdresse());
		if($modeAffichage == 'ajouterInteret'){
			$t->assign_vars(array(
					'formulaireChoixAdresse' => $adresse->afficheChoixAdresse()));
				
		}
		else{
			$t->assign_vars(array(
					'formAction' => $this->creerUrl('','rechercheAvAdresse'),
					'formulaireChoixAdresse' => $adresse->afficheChoixAdresse()));
		}
		foreach($tabTravail as $name => $value)
		{
			$t->assign_vars(array($name=>$value["value"]));
			if($value["error"]!='')
			{
				$t->assign_vars(array($name."-error" => $value["error"]));
			}
		}
	
		ob_start();
		$t->pparse('rechercheFormulaire');
		$html = ob_get_contents();
		ob_end_clean();
	
		return $html;
	}
}
?>