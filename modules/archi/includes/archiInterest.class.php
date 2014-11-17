<?php 
/**
 * Class archiInterest
 *
 * Responsible for the interests of a user
 *
 * @author Antoine Rota Graziosi / InPeople
 *
 */

class archiInterest extends config{
	protected $userId;

	function __construct(){
		$auth = new archiAuthentification();
		$this->userId = $auth->getIdUtilisateur();
		parent::__construct();
	}

	/**
	 * Displaying user interests
	 *
	 * @return string
	 */
	public function displayMyInterest(){
		$html ="";
		$formulaire = new formGenerator();
		$utils = new archiUtils();
		$ajax = new ajaxObject();
		$html.=$ajax->getAjaxFunctions();

		$t = new Template($this->getCheminPhysique().$this->cheminTemplates);
		$t->set_filenames(array('myinterests'=>'myinterests.tpl'));


		//debug($interestsArray);
		$a = new archiAdresse();
		//$formAddInterest = $utils->afficheFormulaireAdresse(array(),'ajouterInteret',array());

		//Generate address form
		$formAddressAddInterest=$a->afficheChoixAdresse();


		$paramsFields= array();
		$paramsFields[] = array('table' => 'pays' ,'value' => 'idPays','title'=>'nom');
		$paramsFields[] = array('table' => 'ville' ,'value' => 'idVille','title'=>'nom');
		$paramsFields[] = array('table' => 'quartier' ,'value' => 'idQuartier','title'=>'nom');
		$paramsFields[] = array('table' => 'sousQuartier' ,'value' => 'idSousQuartier','title'=>'nom');
		$paramsFields[] = array('table' => 'rue' ,'value' => 'idRue','title'=>'nom');
		$paramsFields[] = array('table' => 'historiqueAdresse' ,'value' => 'idHistoriqueAdresse','title'=>'nom');
		$paramsFields[] = array('table' => 'personne' ,'value' => 'idPersonne');



		$formActionUrl = $this->creerUrl('','saveInterest',array());

		foreach ($paramsFields as $params){
			$options[] = $this->getAllField($params);
		}


		$paramsRequest[]=array('table'=> '_interetRue','field' =>'idRue' , 'associateTable' => 'rue');
		$paramsRequest[]=array('table'=> '_interetSousQuartier','field' =>'idSousQuartier', 'associateTable' => 'sousQuartier');
		$paramsRequest[]=array('table'=> '_interetQuartier','field' =>'idQuartier', 'associateTable' => 'quartier');
		$paramsRequest[]=array('table'=> '_interetVille','field' =>'idVille', 'associateTable' => 'ville');
		$paramsRequest[]=array('table'=> '_interetPays','field' =>'idPays', 'associateTable' => 'pays');
		$paramsRequest[]=array('table'=> '_interetPersonne','field' =>'idPersonne', 'associateTable' => 'personne');
		$paramsRequest[]=array('table'=> '_interetAdresse','field' =>'idHistoriqueAdresse', 'associateTable' => 'historiqueAdresse');

		$userInterest = $this->getAllInterest($paramsRequest);

		//debug($userInterest);
		
		foreach ($userInterest as $interestByCat){
				
			//debug($interestByCat);
			if(!isset($interestByCat[0]['vide'])){
				$t->assign_block_vars('interestList',array('title'=>'Liste des '.$interestByCat[0]['titre'].' dans les centre d\'intéret','CSSclass'=>'interestList'));
				
				foreach ($interestByCat as $interest){
					//debug($interest);
					switch ($interest['associateTable']){
						case 'personne':
							$t->assign_block_vars('interestList.interests',array('name'=>$interest['nom']." ".$interest['prenom']));
							break;
						default:
							$t->assign_block_vars('interestList.interests',array('name'=>$interest['nom']));

					}
				}
			}
			else{
				$t->assign_block_vars('interestList',array('vide'=>'Aucun résultat','title'=>'Liste des '.$interestByCat[0]['titre'].' dans les centre d\'intéret','CSSclass'=>'interestList'));
			}
				
		}

		$t->assign_vars(array(
				'formAddInterest' => $formAddressAddInterest,
				'formActionUrl' => $formActionUrl,
				'nameForm'=>'saveInterest'
		));

		ob_start();
		$t->pparse('myinterests');
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}


	/**
	 * This method save the interest with
	 */
	public function saveInterest(){
		$interets = $this->variablesPost;


		$requestParameters = array();
		if($interets['rue']!=0){
			$requestParameters[]=array('table'=>'_interetRue','fieldName1'=>'idUtilisateur','fieldName2'=>'idRue','idInteret'=>$interets['rue'],'userId'=>$this->userId);
		}
		if($interets['sousQuartier']!=0){
			$requestParameters[]=array('table'=>'_interetSousQuartier','fieldName1'=>'idUtilisateur','fieldName2'=>'idSousQuartier','idInteret'=>$interets['sousQuartier'],'userId'=>$this->userId);
		}
		if($interets['quartier']!=0){
			$requestParameters[]=array('table'=>'_interetQuartier','fieldName1'=>'idUtilisateur','fieldName2'=>'idQuartier','idInteret'=>$interets['quartier'],'userId'=>$this->userId);
		}
		if($interets['ville']!=0){
			$requestParameters[]=array('table'=>'_interetVille','fieldName1'=>'idUtilisateur','fieldName2'=>'idVille','idInteret'=>$interets['ville'],'userId'=>$this->userId);
		}
		if($interets['pays']!=0){
			$requestParameters[]=array('table'=>'_interetPays','fieldName1'=>'idUtilisateur','fieldName2'=>'idPays','idInteret'=>$interets['pays'],'userId'=>$this->userId);
		}

		foreach ($requestParameters as $rp){
			//Insert if not exists
			$requete= "
					INSERT INTO ".$rp['table']." (".$rp['fieldName1'].",".$rp['fieldName2'].")
					SELECT '".$this->userId."', '".$rp['idInteret']."'
					FROM `".$rp['table']."`
					WHERE NOT EXISTS (SELECT * FROM `".$rp['table']."`
					WHERE ".$rp['fieldName1']."='".$this->userId."' AND ".$rp['fieldName2']."='".$rp['idInteret']."')
					LIMIT 1
					";
			$res = $this->connexionBdd->requete($requete,false);
		}

		//TODO : Handle the errors with the insert

		$this->erreurs->ajouter("Added with success");
		echo $this->erreurs->afficher();

	}


	/*
	 * Private functions
	*/


	/**
	 * Get the list of the countries in the interest list of the user
	 *
	 * @return multitype:String array of the countries name
	 */
	private function getCountryInterest(){
		$countriesList = array();
		$requete = "
				SELECT p.idPays , p.nom , i.pays_idPays
				FROM pays p
				LEFT JOIN _interetPays i on i.pays_idPays = p.idPays
				WHERE i.utilisateur_idUtilisateur  = ".$this->userId."
						";

		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$countriesList[]=$fetch['nom'];
		}
		return $countriesList;
	}

	/**
	 * Get the list of the cities in the interest list of the user
	 *
	 * @return multitype:String array of the cities name
	 */
	private function getCityInterest(){
		$citiesList = array();
		$requete = "
				SELECT v.idVille , v.nom , i.ville_idVille
				FROM ville v
				LEFT JOIN _interetVille i on i.ville_idVille = v.idVille
				WHERE i.utilisateur_idUtilisateur  = ".$this->userId."
						";

		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$citiesList []=$fetch['nom'];
		}
		return $citiesList;
	}

	/**
	 * Get the list of the neighborhood in the interest list if the user
	 *
	 * @return multitype: array of neighborhood
	 */
	private function getSubNeighborhoodInterest(){
		$sneighborhood = array();
		$requete ="
				SELECT sq.idSousQuartier , sq.nom , i.sousQuartier_idSousQuartier
				FROM sousQuartier sq
				LEFT JOIN _interetSousQuartier i on i.sousQuartier_idSousQuartier = sq.idSousQuartier
				WHERE i.utilisateur_idUtilisateur  = ".$this->userId."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$sneighborhood []=$fetch['nom'];
		}
		return $sneighborhood;
	}

	private function getNeighborhoodInterest(){
		$neighborhood = array();
		$requete ="
				SELECT sq.idQuartier , sq.nom , i.idQuartier
				FROM quartier sq
				LEFT JOIN _interetQuartier i on i.idQuartier = sq.idQuartier
				WHERE i.idUtilisateur  = ".$this->userId."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$neighborhood []=$fetch['nom'];
		}
		return $neighborhood;
	}


	/**
	 * Get the list of the streets in the interest list if the user
	 *
	 * @return multitype: array of streets
	 */
	private function getStreetInterest(){
		$street = array();
		$requete ="
				SELECT r.idRue , r.nom , i.rue_idRue
				FROM rue r
				LEFT JOIN _interetRue i on i.rue_idRue = r.idSousQuartier
				WHERE i.utilisateur_idUtilisateur  = ".$this->userId."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$street[]=$fetch['nom'];
		}
		return $street;
	}


	/**
	 * Get the list of the addresses in the interest list if the user
	 *
	 * @return multitype: array of addresses
	 */
	private function getAddressInterest(){
		$address = array();
		$requete ="
				SELECT a.idHistoriqueAdresse , a.nom , i.idHistoriqueAdresse
				FROM historiqueAdresse a
				LEFT JOIN _interetAdresse i on i.idHistoriqueAdresse = a.idHistoriqueAdresse
				WHERE i.idUtilisateur  = ".$this->userId."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$address[]=$fetch['nom'];
		}
		return $address;
	}


	/**
	 * Get the list of the person in the interest list if the user
	 *
	 * @return multitype: array of persons
	 */
	private function getPersonInterest(){
		$person = array();
		$requete ="
				SELECT p.idPersonne , p.nom ,p.prenom, i.rue_idRue
				FROM personne p
				LEFT JOIN _interetRue i on i.personne_idPersonne = p.idPersonne
				WHERE i.utilisateur_idUtilisateur  = ".$this->userId."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			$person[]=$fetch['nom']." " . $fetch['prenom'];
		}
		return $person;
	}



	/**
	 * Generate a form for selecting interest
	 *
	 * @param unknown $paramsForm : parameters of the form
	 * @return html of the form to insert
	 */
	private function generateFormInterest($paramsForm){
		$html='';
		if(!isset($paramsForm)){
			$this->erreurs->ajouter("Erreur dans la génération du formulaire, aucuns paramètres n'as été spécifié");
			$this->erreurs->afficher();
			return null;
		}
		else{
			$t = new Template($this->getCheminPhysique().$this->cheminTemplates);
			$t->set_filenames(array('form'=>'interestForm.tpl'));
				
			$t->assign_vars(array(
					'idField' => $paramsForm['idField'],
					'nom' => $paramsForm['nom'],
					'submitValue' => $paramsForm['submitValue']
			));
				
			foreach ($paramsForm['option'] as $option){
				$t->assign_block_vars('option',array(
						'value'=>$option['value'],
						'title'=> $option['title']
				));
			}

			ob_start();
			$t->pparse('form');
			$html .= ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}


	/**
	 * Get all field to process  the select option (unused now)
	 *
	 * @param unknown $params :
	 * @return multitype:multitype:unknown  multitype:string Ambigous <>
	 */
	private function getAllField($params){
		$result = array();
		$requete = "
				SELECT *
				FROM ".$params['table']."
						";
		$res = $this->connexionBdd->requete($requete);
		while ($fetch = mysql_fetch_assoc($res)) {
			if($params['table'] !='personne'){
				$temp = array('value'=>$fetch[$params['value']] , 'title' =>$fetch[$params['title']]);
				$result[]=$temp;
			}
			else{
				$result[]=array('value'=>$fetch[$params['value']] , 'title' =>$fetch['nom'] . " ". $fetch['prenom']);
			}
		}
		return $result;
	}


	/**
	 * Get all the interest of current user
	 *
	 * @param unknown $params : array with tables name
	 * @return multitype:multitype:unknown  multitype:string Ambigous <> array('value'=> res , 'title' => res)
	 */
	private function getAllInterest($arrayParams = array()){
		//$result = array();

		foreach ($arrayParams as $params){
			$subArray = array();

			$requete = "
					SELECT *
					FROM ".$params['table']." t
					LEFT JOIN ".$params['associateTable']." at on at.".$params['field']."= t.".$params['field']."
					WHERE t.idUtilisateur = ".$this->userId."
					";
		
			$res = $this->connexionBdd->requete($requete);
			if(mysql_num_rows($res)==0){
				$titre='';
				if($params['associateTable']=='sousQuartier'){
					$titre = 'sous quartier';
				}
				elseif($params['associateTable']=='historiqueAdresse'){
					$titre ='adresse' ;
				}
				else{
					$titre=$params['associateTable'] ;
				}
				$subArray=array(array_merge(array('vide'=>true,'titre'=>$titre),$params));
				
			}
			
			while ($fetch = mysql_fetch_assoc($res)) {
				
				$temp = array_merge($fetch,$params);
				if($temp['associateTable']=='sousQuartier'){
					$temp = array_merge($temp,array('titre' =>'sous quartier' ));
				}
				elseif($temp['associateTable']=='historiqueAdresse'){
					$temp = array_merge($temp,array('titre' =>'adresse' ));
				}
				else{
					$temp = array_merge($temp,array('titre' =>$temp['associateTable'] ));
				}
				$subArray[]=$temp;
			}
			if(!empty($subArray)){
				$result[] = $subArray;
			}
		}
		return $result;
	}







	private function generateFormPersonne(){
		$this->userId;




	}

}
?>
