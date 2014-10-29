<?php

class archiRecherche extends config {

	function __construct() {
		parent::__construct();
	}


	public function navigationAdresse( $criteres = array())
	{
		$html = '';
		$tabParametresAutorises = array('idPays', 'idVille', 'idQuartier', 'idSousQuartier', 'idRue');
		$sqlLimit = '0, 10';
		foreach ($tabParametresAutorises AS $param) {
			if (isset($this->variablesGet[$param]) AND !isset($criteres[$param])) {
				$criteres[$param] = $this->variablesGet[$param];
			}
		}

		if (isset($criteres['idSousQuartier'])){
			// affichage des villes
			$sql = 'SELECT r.nom, r.idRue, sq.nom AS nomSousQuartier, q.idQuartier FROM rue r
					LEFT JOIN sousQuartier sq ON sq.idSousQuartier=r.idSousQuartier
					LEFT JOIN quartier q ON q.idQuartier=sq.idQuartier
					WHERE r.idSousQuartier=1 LIMIT '.$sqlLimit;
			if (is_numeric($criteres['idSousQuartier']) AND $criteres['idSousQuartier']>0) {
				$sql = 'SELECT r.nom, r.idRue, sq.nom AS nomSousQuartier, q.idQuartier FROM rue r
						LEFT JOIN sousQuartier sq ON sq.idSousQuartier=r.idSousQuartier
						LEFT JOIN quartier q ON q.idQuartier=sq.idQuartier
						WHERE r.idSousQuartier='.$criteres['idSousQuartier'].' LIMIT '.$sqlLimit;
			}

			$resultat = $this->connexionBdd->requete($sql);
			$t = new Template('modules/archi/templates/');

			$t->set_filenames((array('liste'=>'listeNavigationAdresse.tpl')));
			while ($rep = mysql_fetch_object($resultat)) {
				$t->assign_block_vars('l', array(
						'url' => $this->creerUrl('', 'navigationAdresse', $this->variablesGet),
						'nom' => $rep->nom,
				));
				$nomParent = $rep->nomSousQuartier;
				$idParent  = $rep->idQuartier;
			}
			if (isset($idParent)) {
				$t->assign_vars(array(
						'urlParent' => $this->creerUrl('', 'navigationAdresse', array('idQuartier' => $idParent)),
						'nomParent' => $nomParent));
			}

			ob_start();
			$t->pparse('liste');
			$html .= ob_get_contents();
			ob_end_clean();
		}
		else if (isset($criteres['idQuartier'])) {
			// affichage des villes
			$sql = 'SELECT sq.nom, sq.idSousQuartier, q.nom AS nomQuartier, v.idVille FROM sousQuartier sq
					LEFT JOIN quartier q ON q.idQuartier=sq.idQuartier
					LEFT JOIN ville v ON v.idVille=q.idVille
					WHERE sq.idQuartier=1 LIMIT '.$sqlLimit;
			if (is_numeric($criteres['idQuartier']) AND $criteres['idQuartier']>0) {
				$sql = 'SELECT sq.nom, sq.idSousQuartier, q.nom AS nomQuartier, v.idVille FROM sousQuartier sq
						LEFT JOIN quartier q ON q.idQuartier=sq.idQuartier
						LEFT JOIN ville v ON v.idVille=q.idVille
						WHERE sq.idQuartier='.$criteres['idQuartier'].' LIMIT '.$sqlLimit;
			}

			$resultat = $this->connexionBdd->requete($sql);
			$t = new Template('modules/archi/templates/');

			$t->set_filenames((array('liste'=>'listeNavigationAdresse.tpl')));
			while ($rep = mysql_fetch_object($resultat)) {
				$t->assign_block_vars('l', array(
						'url' => $this->creerUrl('', 'navigationAdresse', array('idSousQuartier' => $rep->idSousQuartier)),
						'nom' => $rep->nom,
				));
				$nomParent = $rep->nomQuartier;
				$idParent  = $rep->idVille;
			}
			$t->assign_vars(array(
					'urlParent' => $this->creerUrl('', 'navigationAdresse', array('idVille' => $idParent)),
					'nomParent' => $nomParent));

			ob_start();
			$t->pparse('liste');
			$html .= ob_get_contents();
			ob_end_clean();
		}
		else if (isset($criteres['idVille'])) {
			// affichage des villes
			$sql = 'SELECT q.nom, q.idQuartier, v.nom AS nomVille, p.idPays FROM quartier q
					LEFT JOIN ville v ON v.idVille=q.idVille
					LEFT JOIN pays p ON v.idPays=p.idPays
					WHERE q.idVille=1 LIMIT '.$sqlLimit;
			if (is_numeric($criteres['idVille']) AND $criteres['idVille']>0) {
				$sql = 'SELECT q.nom, q.idQuartier, v.nom AS nomVille, p.idPays FROM quartier q
						LEFT JOIN ville v ON v.idVille=q.idVille
						LEFT JOIN pays p ON v.idPays=p.idPays
						WHERE q.idVille='.$criteres['idVille'].' LIMIT '.$sqlLimit;
			}

			$resultat = $this->connexionBdd->requete($sql);
			$t = new Template('modules/archi/templates/');

			$t->set_filenames((array('liste'=>'listeNavigationAdresse.tpl')));
			while ($rep = mysql_fetch_object($resultat)) {
				$t->assign_block_vars('l', array(
						'url' => $this->creerUrl('', 'navigationAdresse', array('idQuartier' => $rep->idQuartier)),
						'nom' => $rep->nom,
				));
				$nomVille = $rep->nomVille;
				$idPays  = $rep->idPays;
			}
			$t->assign_vars(array(
					'urlParent' => $this->creerUrl('', 'navigationAdresse', array('idPays' => $idPays)),
					'nomParent' => $nomVille));

			ob_start();
			$t->pparse('liste');
			$html .= ob_get_contents();
			ob_end_clean();
		}
		else if (isset($criteres['idPays'])) {
			// affichage des villes
			$sql = 'SELECT v.nom, v.idVille FROM ville v
					WHERE v.idPays=1 LIMIT '.$sqlLimit;
			if (is_numeric($criteres['idPays']) AND $criteres['idPays']>0) {
				$sql = 'SELECT v.nom, v.idVille, p.nom AS nomPays, p.idPays FROM ville v
						LEFT JOIN pays p ON p.idPays=v.idPays
						WHERE v.idPays='.$criteres['idPays'].' LIMIT '.$sqlLimit;
			}

			$resultat = $this->connexionBdd->requete($sql);
			$t = new Template('modules/archi/templates/');

			$t->set_filenames((array('liste'=>'listeNavigationAdresse.tpl')));
			$t->assign_vars(array(
					'urlParent' => $this->creerUrl('', 'navigationAdresse'),
					'nomParent' => 'Voir les Pays'));

			while ($rep = mysql_fetch_object($resultat)) {
				$t->assign_block_vars('l', array(
						'url' => $this->creerUrl('', 'navigationAdresse', array('idVille' => $rep->idVille)),
						'nom' => $rep->nom,
				));
			}
			ob_start();
			$t->pparse('liste');
			$html .= ob_get_contents();
			ob_end_clean();
		}
		else {
			// affichage des pays

			$sql = 'SELECT p.nom, p.idPays FROM pays p';

			$resultat = $this->connexionBdd->requete($sql);
			$t = new Template('modules/archi/templates/');

			$t->set_filenames((array('liste'=>'listeNavigationAdresse.tpl')));
			$t->assign_vars(array(
					'urlParent' => $this->creerUrl('', 'navigationAdresse', $this->variablesGet),
					'nomParent' => 'Aucun parents'));

			while ($rep = mysql_fetch_object($resultat)) {
				$t->assign_block_vars('l', array(
						'url' => $this->creerUrl('', 'navigationAdresse', array('idPays' => $rep->idPays)),
						'nom' => $rep->nom,
				));
			}
			ob_start();
			$t->pparse('liste');
			$html .= ob_get_contents();
			ob_end_clean();
		}


		return $html;
	}

	public function rechercher()
	{
		$html = '';
		$tabForm = array();
		$resAvAdresse="";
		$formulaire = new formGenerator();

		$checkedCheckBox  = false;

		if(isset($this->variablesGet['submit']))
		{
			$tabForm=array(
					'motcle'    => array('default'=> '', 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'));

			$erreur = $formulaire->getArrayFromPost($tabForm, $_GET);

			if (count($erreur) == 0) {
				$options=array('sansFormulaire' => 1);

				$modeAffichage='';
				if(isset($this->variablesGet['modeAffichage']))
					$options['modeAffichage'] = $this->variablesGet['modeAffichage'];

				if (isset($this->variablesGet['afficheResultatsSurCarte'])
				&& $this->variablesGet['afficheResultatsSurCarte']==1) {

					$checkedCheckBox = true;
					$criteres = array('recherche_motcle'=>$tabForm['motcle']['value']);
					$adresses = new archiAdresse();
					$retourAdresses = $adresses->afficherListe($criteres, $modeAffichage,array('sqlNoLimit'=>true));


					$arrayIdAdresses = $retourAdresses['arrayIdAdresses'];

					$gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>700, 'height'=>500, 'zoom'=>13));

					$this->addToJsHeader($gm->getJsFunctions()); // ajout des fonctions de google map dans le header

					// on affiche uniquement les 100 premieres coordonnées
					// preparation du tableau de liste de coordonnées a transmettre a la classe googlemap
					$listeCoordonnees = array();
					$arrayIdAdressesConfigGMap = array();

					for($i=0; $i<100 && isset($arrayIdAdresses[$i]) ; $i++)
					{

						$reqCoordonnees = "
								SELECT ha1.latitude as latitude, ha1.longitude as longitude
								FROM historiqueAdresse ha2, historiqueAdresse ha1
								WHERE ha2.idAdresse = ha1.idAdresse
								AND ha1.longitude!=''
								AND ha1.latitude!=''
								AND ha1.longitude!='0'
								AND ha1.latitude!='0'
								AND ha1.longitude IS NOT NULL
								AND ha1.latitude IS NOT NULL
								AND ha1.idAdresse = '".$arrayIdAdresses[$i]."'
										GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
										HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)

										";
						$resCoordonnees = $this->connexionBdd->requete($reqCoordonnees);


						$arrayIdAdressesConfigGMap[$i]['idAdresse'] = $arrayIdAdresses[$i];
						if(mysql_num_rows($resCoordonnees)>0)
						{
							$fetchCoordonnees = mysql_fetch_assoc($resCoordonnees);
							$arrayIdAdressesConfigGMap[$i]['longitude'] = $fetchCoordonnees['longitude'];
							$arrayIdAdressesConfigGMap[$i]['latitude'] = $fetchCoordonnees['latitude'];
						}
						else
						{
							$arrayIdAdressesConfigGMap[$i]['longitude'] = 0;
							$arrayIdAdressesConfigGMap[$i]['latitude'] = 0;
						}

					}

					$retourConfig = $adresses
					->getArrayGoogleMapConfigCoordonneesFromCenter(
							array('arrayIdAdresses'=>$arrayIdAdressesConfigGMap)
					);
					$resAvAdresse.="<br>".$retourAdresses['nbAdresses'].
					" résultats.<br>";
					$resAvAdresse.=$gm->getMap(
							array(
									'listeCoordonnees'=>$retourConfig['arrayConfigCoordonnees'],
									'urlImageIcon'=>$this->getUrlImage()."pointGM.png",
									'pathImageIcon'=>$this->getCheminPhysique()."images/pointGM.png",
									'setAutomaticCentering'=>true
							)
					);


				} else {
					$resAvAdresse   = $this->rechercheAvanceeAdresse($options);
				}
			}
		}



		if(isset($this->variablesGet['modeAffichage'])
		&& (
				$this->variablesGet['modeAffichage'] == 'popupAjoutAdressesLieesSurEvenement'
				|| $this->variablesGet['modeAffichage'] == 'popupDeplacerEvenementVersGroupeAdresse'
				|| $this->variablesGet['modeAffichage'] == 'popupRechercheAdresseAdminParcours'
				|| $this->variablesGet['modeAffichage'] == 'popupRechercheAdresseVueSur'
				|| $this->variablesGet['modeAffichage'] == 'popupRechercheAdressePrisDepuis'
		)
		)
		{
			$html.="<h2>Selection d'adresses:</h2><br>";
			$html .= "<div style='text-align:center;'>".$this->afficheFormulaire($tabForm,0,array('noDisplayRechercheAvancee'=>true,'noDisplayCheckBoxResultatsCarte'=>true))."</div>"; // n'affiche pas le titre et les liens vers la recherche par carte etc ...
		} else {
			$html .= "<div>".$this->afficheFormulaire($tabForm,1,array('isCheckBoxAfficheResultatSurCarteChecked'=>$checkedCheckBox))."</div>";
		}

		$html .= $resAvAdresse;
		return $html;
	}

	public function rechercheAvanceeEvenement($ordres = array())
	{
		$html = '';
		$tabFormulaire = array();
		$criteres      = array();

		$modeAffichage='';
		if(isset($this->variablesGet['modeAffichage']))
			$modeAffichage = $this->variablesGet['modeAffichage'];


		if (isset($ordres['modeAffichage']))
			$modeAffichage = $ordres['modeAffichage'];


		$formulaire = new formGenerator();
		if (isset($this->variablesGet['submit']))
		{

			$tabFormulaire = array(
					'motcle'    => array('default'=> '', 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
					'courant'       => array('default'=> '0'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
					'typeStructure'    => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
					'typeEvenement'    => array('default'=> '0'  , 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
					'source'    => array('default'=> '0' , 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'source', 'primaryKey'=> 'idSource')),
					'personnes'     => array('default'=> 'aucune' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
					'anneeDebut'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
					'anneeFin'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
					'MH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
					'ISMH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox')
			);

			$erreur = $formulaire->getArrayFromPost( $tabFormulaire, $_GET);

			if (count($erreur) == 0)
			{
				foreach ($tabFormulaire AS $nom => $valeur) {
					$criteres['recherche_'.$nom] = $valeur['value'];
				}


				$evenement = new archiEvenement();

				$arrayListeEvenements= $evenement->getIdEvenementsFromRecherche($criteres, 'listeEvenement.tpl', $modeAffichage);

				$arrayListeEvenements = array_unique($arrayListeEvenements);

				$adresse = new archiAdresse();

				$arrayListeEvenementsParents=array();
				foreach($arrayListeEvenements as $idEvenementFils)
				{
					//$resAdresse = $adresse->getAdressesFromEvenementGroupeAdresses($evenement->getParent($idEvenementFils));
					//$fetchAdresse = mysql_fetch_assoc($resAdresse);
					//$html .= "<a href=\"".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchAdresse['idAdresse']))."\">".stripslashes($adresse->getIntituleAdresse($fetchAdresse))."</a><br><br>";
					$arrayListeEvenementsParents[] = $evenement->getParent($idEvenementFils);
				}

				// dans le cas de l'affichage des sources du site , on va rajouter la liste des adresses ou les photos sont concernées aussi par la source courante
				if(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage'] == "listeAdressesFromSource" && isset($this->variablesGet['source']) && $this->variablesGet['source']!='')
				{
					$reqImages = "
							SELECT distinct ae.idEvenement as idEvenementGA
							FROM _adresseEvenement ae
							LEFT JOIN historiqueImage hi1 ON hi1.idSource = '".$this->variablesGet['source']."'
									LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
									LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
									LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
									WHERE 1=1
									AND ae.idEvenement = ee.idEvenement
									GROUP BY hi1.idImage, hi1.idHistoriqueImage
									HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)

									";

					$resImages = $this->connexionBdd->requete($reqImages);

					while($fetchImages = mysql_fetch_assoc($resImages))
					{
						$arrayListeEvenementsParents[] = $fetchImages['idEvenementGA'];
					}
					$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);

					if(isset($this->variablesGet['source']) && $this->variablesGet['source']=='24') // si la selection courante pour sur les archives municipales
					{
						// on comptabilise les images avec un numero d'archive sans idSource (comptabilisé avec la source "archives municipales"
						$reqImagesAvecNumeroArchiveSansIdSource="
								SELECT distinct ae.idEvenement as idEvenementGA
								FROM _adresseEvenement ae
								LEFT JOIN historiqueImage hi1 ON hi1.numeroArchive<>''
								LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
								LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
								LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
								WHERE 1=1
								AND (hi1.idSource='0' OR hi1.idSource='')
								AND ae.idEvenement = ee.idEvenement
								GROUP BY hi1.idImage, hi1.idHistoriqueImage
								HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
								";
						$resImagesAvecNumeroArchiveSansIdSource = $this->connexionBdd->requete($reqImagesAvecNumeroArchiveSansIdSource);

						while($fetchImagesAvecNumeroArchiveSansIdSource = mysql_fetch_assoc($resImagesAvecNumeroArchiveSansIdSource))
						{
							$arrayListeEvenementsParents[] = $fetchImagesAvecNumeroArchiveSansIdSource['idEvenementGA'];
						}
						$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);
					}


					if(isset($this->variablesGet['source']) && $this->variablesGet['source']=='24') // si la selection courante pour sur les archives municipales
					{
						// on comptabilise les evenements  avec un numero d'archive sans idSource (comptabilisé avec la source "archives municipales"
						$reqEvenementAvecNumeroArchiveSansIdSource="
								SELECT distinct ae.idEvenement as idEvenementGA
								FROM _adresseEvenement ae
								LEFT JOIN historiqueEvenement he1 ON he1.numeroArchive<>''
								LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
								LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
								WHERE 1=1
								AND (he1.idSource='0' OR he1.idSource='')
								AND ae.idEvenement = ee.idEvenement
								GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
								HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
								";
						$resEvenementAvecNumeroArchiveSansIdSource = $this->connexionBdd->requete($reqEvenementAvecNumeroArchiveSansIdSource);

						while($fetchEvenementAvecNumeroArchiveSansIdSource = mysql_fetch_assoc($resEvenementAvecNumeroArchiveSansIdSource))
						{
							$arrayListeEvenementsParents[] = $fetchEvenementAvecNumeroArchiveSansIdSource['idEvenementGA'];
						}
						$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);
					}
				}


				$retourAdresses = $adresse->afficherListe(array('groupesAdressesSupplementairesExternes'=>$arrayListeEvenementsParents), $modeAffichage);
				$html.= $retourAdresses['html'];

			}

		}

		if (!isset($ordres['sansFormulaire']))
			$html .= $this->afficheFormulaireEvenement($tabFormulaire, $ordres);

		return $html;
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

	public function rechercheAvanceeAdresse($ordres = array())
	{
		$html = '';
		$tabForm = array();

		$formulaire = new formGenerator();

		$modeAffichage='';

		if(isset($this->variablesGet['modeAffichage']))
			$modeAffichage = $this->variablesGet['modeAffichage'];

		if (isset($ordres['modeAffichage']))
			$modeAffichage = $ordres['modeAffichage'];


		if (isset($this->variablesGet['submit']))
		{
			$tabForm=array(
					'motcle'       => array('default'=> '' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
					'pays'         => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'pays', 'primaryKey'=> 'idPays')),
					'ville'           => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'ville', 'primaryKey'=> 'idVille')),
					'quartier'     => array('default'=> '0', 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'quartier', 'primaryKey'=> 'idQuartier')),
					'sousQuartier' => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'sousQuartier', 'primaryKey'=> 'idSousQuartier')),
					'rue'          => array('default'=> '0' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'rue', 'primaryKey'=> 'idRue'))
			);

			$erreur = $formulaire->getArrayFromPost($tabForm, $_GET);


			if (count($erreur) == 0)
			{
				foreach ($tabForm AS $nom => $valeur)
				{
					$criteres['recherche_'.$nom] = $valeur['value'];
				}

				if(isset($this->variablesGet['longitude']) && isset($this->variablesGet['latitude']))
				{
					$criteres['recherche_latitude'] = $this->variablesGet['latitude'];
					$criteres['recherche_longitude']= $this->variablesGet['longitude'];
					$criteres['recherche_rayon'] = "100"; // on effectue la recherche dans un rayon de 100m
				}

				$adresses = new archiAdresse();
				$retourAdresses = $adresses->afficherListe($criteres, $modeAffichage);
				$html     .= $retourAdresses['html'];
			}
		}

		if (!isset($ordres['sansFormulaire']))
		{
			$html .= $this->afficheFormulaireAdresse($tabForm, $modeAffichage);
		}

		return $html;
	}


	public function afficheFormulaireEvenement ( $tabTravail = array(), $ordres = array())
	{
		$modeAffichage = "";
		if(isset($this->variablesGet['modeAffichage']))
			$modeAffichage=$this->variablesGet['modeAffichage'];

		if(isset($ordres['modeAffichage']))
			$modeAffichage=$ordres['modeAffichage'];

		/******
		 ** Source - répration
		**/
		$sqlSource = 'SELECT idSource, nom, idTypeSource FROM source';
		$tabSource = array();
		if ($result = $this->connexionBdd->requete($sqlSource))
		{
			while ($rep = mysql_fetch_object($result))
			{
				if (!empty($rep->nom)) {
					$tabSource[$rep->idSource] = $rep->nom;
				}
			}
		}

		/******
		 ** TypeStructure- répration
		**/
		$sqlTypeStructure = 'SELECT idTypeStructure, nom FROM typeStructure';
		$tabTypeStructure = array();
		if ($result = $this->connexionBdd->requete($sqlTypeStructure))
		{
			while ($rep = mysql_fetch_object($result))
			{
				if (!empty($rep->nom))
					$tabTypeStructure[$rep->idTypeStructure] = $rep->nom;
			}
		}


		/******
		 ** Type Éèment- répration
		**/
		$sqlTypeEvenement = 'SELECT idTypeEvenement, nom FROM typeEvenement WHERE idTypeEvenement<>"'.$this->getIdTypeEvenementGroupeAdresse().'"';
		$tabTypeEvenement = array();
		if ($result = $this->connexionBdd->requete($sqlTypeEvenement))
		{
			while ($rep = mysql_fetch_object($result))
			{
				if (!empty($rep->nom))
					$tabTypeEvenement[$rep->idTypeEvenement] = $rep->nom;
			}
		}


		/******
		 ** Courant Architecturaux - répration
		**/
		$sqlCourantArchitectural = 'SELECT idCourantArchitectural, nom FROM courantArchitectural';
		$tabCourantArchitectural = array();
		if ($result = $this->connexionBdd->requete($sqlCourantArchitectural))
		{
			while ($rep = mysql_fetch_object($result))
			{
				if (!empty($rep->nom))
					$tabCourantArchitectural[$rep->idCourantArchitectural] = $rep->nom;
			}
		}


		/******
		 ** Courant Architecturaux - répration
		**/
		$sqlCourantArchitectural = 'SELECT idCourantArchitectural, nom FROM courantArchitectural';
		$tabCourantArchitectural = array();
		if ($result = $this->connexionBdd->requete($sqlCourantArchitectural))
		{
			while ($rep = mysql_fetch_object($result))
			{
				$tabCourantArchitectural[$rep->idCourantArchitectural] = $rep->nom;
			}
		}

		/******
		 ** Personnes- répration
		**/
		$sqlPersonne = 'SELECT idPersonne, nom FROM personne';
		$tabPersonne = array();
		if ($result = $this->connexionBdd->requete($sqlPersonne))
		{
			while ($rep = mysql_fetch_object($result))
			{
				if (!empty($rep->nom))
					$tabPersonne[$rep->idPersonne] = $rep->nom;
			}
		}



		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('rechercheFormulaire'=>'rechercheAvanceeEvenementFormulaire.tpl')));

		$adresse = new archiAdresse();

		if(isset($modeAffichage) && $modeAffichage!='')
		{
			switch($modeAffichage)
			{
				case 'calqueEvenement':
				case 'calqueImage':
				case 'calqueEvenementChampsMultiples':
					$t->assign_vars(array('formAction' => $this->creerUrl('','rechercheAvEvenementPopup','',array('noHeaderNoFooter'=>'1'))));
					$t->assign_block_vars('isCalque',array());
					$t->assign_vars(array('modeAffichage'=>$ordres['modeAffichage']));
					break;
				case 'popupRechercheAdressePrisDepuis':
					// cas de la popup sur le lien prisDepuis sur le formulaire de modif d'une photo
					$t->assign_block_vars('modeAffichage',array('value'=>'popupRechercheAdressePrisDepuis'));
					$t->assign_block_vars('archiAffichage',array('value'=>'rechercheAvEvenement'));
					$t->assign_block_vars('noHeaderNoFooter',array());
					break;
				case 'popupRechercheAdresseVueSur':
					// cas de la popup sur le lien prisDepuis sur le formulaire de modif d'une photo
					$t->assign_block_vars('modeAffichage',array('value'=>'popupRechercheAdresseVueSur'));
					$t->assign_block_vars('archiAffichage',array('value'=>'rechercheAvEvenement'));
					$t->assign_block_vars('noHeaderNoFooter',array());
					break;
				case 'rechercheAvancee':
					//advancedSearch
					//$t->assign_block_vars('archiAffichage',array('value'=>'resultatsRechercheAvancee'));
					$t->assign_block_vars('archiAffichage',array('value'=>'advancedSearch'));
					break;

				default:
					$t->assign_block_vars('noCalque',array());
					break;
			}
		} else {
			if(isset($this->variablesGet['noHeaderNoFooter']))
				$t->assign_block_vars('noHeaderNoFooter',array());

			$t->assign_vars(array('formAction' => $this->creerUrl('','rechercheAvEvenement')));
			$t->assign_block_vars('noCalque',array());
		}
		// Si on affiche sur un calque, on n'affiche pas le bouton submit qui est gé en javascript pour AJAX
		/*if (isset($ordres['modeAffichage']) && $ordres['modeAffichage']=='calqueEvenement')
		{
		$t->assign_vars(array('formAction' => $this->creerUrl('','rechercheAvEvenementPopup','',array('noHeaderNoFooter'=>'1'))));
		$t->assign_block_vars('isCalque',array());
		$t->assign_vars(array('noHeaderNoFooter' => "1"));
		} else {
		$t->assign_vars(array('formAction' => $this->creerUrl('','rechercheAvEvenement')));
		}
		*/
		/**************
		 **  Affichage des Erreurs et valeurs
		**/

		if(!isset($ordres['noTitre']) || $ordres['noTitre']==false)
		{
			$t->assign_block_vars('displayTitre',array());
		}

		if(!isset($ordres['noRechercheParMotCle']) || $ordres['noRechercheParMotCle']==false)
		{
			$t->assign_block_vars('afficheRechercheMotCle',array());
		}

		if(!isset($ordres['noFormElement']) || $ordres['noFormElement']==false)
		{
			$t->assign_block_vars('useFormElements',array());
		}


		foreach($tabTravail as $name => $value)
		{
			$t->assign_vars(array($name=>$value["value"]));
			if($value["error"]!='')
			{
				$t->assign_vars(array($name."-error" => $value["error"]));
			}
		}

		/**************
		 **  Affichage des listes d'options
		**/

		if (!empty($tabSource))
		{
			foreach($tabSource AS $id => $nom)
			{
				if (isset($tabTravail['source']['value']) && $tabTravail['source']['value'] == $id)
					$selected='selected="selected"';
				else
					$selected='';
				$t->assign_block_vars('source', array('val'=> $id, 'nom'=> stripslashes($nom), 'selected'=> $selected));
			}
		}



		if (!empty($tabTypeStructure))
		{
			foreach($tabTypeStructure AS $id => $nom)
			{
				if (isset( $tabTravail['typeStructure']) && $tabTravail['typeStructure']['value'] == $id)
					$selected = 'selected="selected"';
				else
					$selected = '';
				$t->assign_block_vars('struct', array('val'=> $id, 'nom'=> $nom, 'selected'=> $selected));
			}
		}

		if (!empty($tabTypeEvenement))
		{
			foreach($tabTypeEvenement AS $id => $nom)
			{
				if (isset( $tabTravail['typeEvenement']) && $tabTravail['typeEvenement']['value'] == $id)
					$selected = 'selected="selected"';
				else
					$selected = '';
				$t->assign_block_vars('evenement', array('val'=> $id, 'nom'=> $nom, 'selected'=> $selected));
			}
		}

		if (!empty($tabCourantArchitectural))
		{
			$tableauMiseEnPageCourants = new tableau();
			foreach($tabCourantArchitectural AS $id => $nom)
			{
				if (isset( $tabTravail['courant']) && is_array($tabTravail['courant']['value']) && in_array($id, $tabTravail['courant']['value']))
					$selected = 'checked="checked"';
				else
					$selected = '';
				//$t->assign_block_vars('courant', array('val'=> $id, 'nom'=> $nom, 'selected'=> $selected));


				$tableauMiseEnPageCourants->addValue("<input type='checkbox' name='courant[]' value='$id' $selected> $nom");

			}
			$t->assign_vars(array("listeCourantsArchitecturaux"=>$tableauMiseEnPageCourants->createHtmlTableFromArray(5,"white-space:nowrap;font-size:12px; font-color:#000000;")));
		}

		if (!empty($tabPersonne))
		{
			foreach($tabPersonne AS $id => $nom)
			{
				if (isset( $tabTravail['personnes']) && is_array($tabTravail['personnes']['value'])  && in_array($id, $tabTravail['personnes']['value']))
					$selected = 'selected="selected"';
				else
					$selected = '';
				$t->assign_block_vars('personne', array('val'=> $id, 'nom'=> $nom, 'selected'=> $selected));
			}
		}


		if(isset($tabTravail['MH']['value']) && $tabTravail['MH']['value']=='1')
		{
			$t->assign_vars(array('isMH'=>'checked'));
		}

		if(isset($tabTravail['ISMH']['value']) && $tabTravail['ISMH']['value']=='1')
		{
			$t->assign_vars(array('isISMH'=>'checked'));
		}


		ob_start();
		$t->pparse('rechercheFormulaire');
		$html=ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function afficheResultatsRechercheAvancee($params = array())
	{
		$html ="";
		$modeAffichage = 'recherche';
		$formulaire = new formGenerator();

		if (isset($this->variablesGet['submitRechercheAvancee']))
		{

			$adresses = new archiAdresse();

			$tabForm=array(
					'motcle'       => array('default'=> '' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
					'pays'         => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'pays', 'primaryKey'=> 'idPays')),
					'ville'           => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'ville', 'primaryKey'=> 'idVille')),
					'quartier'     => array('default'=> '0', 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'quartier', 'primaryKey'=> 'idQuartier')),
					'sousQuartier' => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'sousQuartier', 'primaryKey'=> 'idSousQuartier')),
					'rue'          => array('default'=> '0' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'rue', 'primaryKey'=> 'idRue')),
					'courant'       => array('default'=> '0'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
					'typeStructure'    => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
					'typeEvenement'    => array('default'=> '0'  , 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
					'source'    => array('default'=> '0' , 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
							array('table'=> 'source', 'primaryKey'=> 'idSource')),
					'personnes'     => array('default'=> 'aucune' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
							array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
					'anneeDebut'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
					'anneeFin'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
					'MH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
					'ISMH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox')
			);



			$erreur = $formulaire->getArrayFromPost($tabForm, $_GET);

			if(count($erreur)==0)
			{
				foreach ($tabForm AS $nom => $valeur)
				{
					$criteres['recherche_'.$nom] = $valeur['value'];
				}


				if(isset($this->variablesGet['afficheResultatsSurCarte']) && $this->variablesGet['afficheResultatsSurCarte']=='1')
				{
					$checkedCheckBox = true;

					$adresses = new archiAdresse();
					$retourAdresses = $adresses->afficherListe($criteres, $modeAffichage,array('sqlLimitExterne'=>"150")); // on limite les resultats a 150 pour avoir au moins 100 groupes d'adresses différents


					//$arrayIdAdresses = $retourAdresses['arrayIdAdresses'];
					$arrayIdEvenementsGA = $retourAdresses['arrayIdEvenementsGroupeAdresse'];

					$gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'width'=>700, 'height'=>500, 'zoom'=>13));

					$this->addToJsHeader($gm->getJsFunctions()); // ajout des fonctions de google map dans le header

					// on affiche uniquement les 100 premieres coordonnées
					// preparation du tableau de liste de coordonnées a transmettre a la classe googlemap
					$listeCoordonnees = array();
					$arrayIdEvenementGroupeAdressesConfigGMap = array();

					for($i=0; $i<100 && isset($arrayIdEvenementsGA[$i]) ; $i++)
					{

						$reqCoordonnees = "
								SELECT     IF(ae.latitudeGroupeAdresse<>'0',ae.latitudeGroupeAdresse,ha1.latitude) as latitude,
								IF(ae.longitudeGroupeAdresse<>'0', ae.longitudeGroupeAdresse,ha1.longitude) as longitude
								FROM historiqueAdresse ha2, historiqueAdresse ha1
								LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse
								WHERE ha2.idAdresse = ha1.idAdresse
								AND ha1.longitude!=''
								AND ha1.latitude!=''
								AND ha1.longitude!='0'
								AND ha1.latitude!='0'
								AND ha1.longitude IS NOT NULL
								AND ha1.latitude IS NOT NULL
								AND ae.idEvenement = '".$arrayIdEvenementsGA[$i]."'
										GROUP BY ha1.idAdresse,ha1.idHistoriqueAdresse
										HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse)

										";
						$resCoordonnees = $this->connexionBdd->requete($reqCoordonnees);


						$arrayIdEvenementGroupeAdressesConfigGMap[$i]['idEvenementGroupeAdresse'] = $arrayIdEvenementsGA[$i];
						if(mysql_num_rows($resCoordonnees)>0)
						{
							$fetchCoordonnees = mysql_fetch_assoc($resCoordonnees);
							$arrayIdEvenementGroupeAdressesConfigGMap[$i]['longitude'] = $fetchCoordonnees['longitude'];
							$arrayIdEvenementGroupeAdressesConfigGMap[$i]['latitude'] = $fetchCoordonnees['latitude'];
						}
						else
						{
							$arrayIdEvenementGroupeAdressesConfigGMap[$i]['longitude'] = 0;
							$arrayIdEvenementGroupeAdressesConfigGMap[$i]['latitude'] = 0;
						}

					}

					$retourConfig = $adresses->getArrayGoogleMapConfigCoordonneesFromCenter(array('arrayIdEvenementsGroupeAdresse'=>$arrayIdEvenementGroupeAdressesConfigGMap));
					$html= "<h1>"._("Résultats de la recherche avancée :")."</h1>";

					if($retourAdresses['nbAdresses']==0)
					{
						$html.=_("aucun résultat.")."<br><br>";
					}
					else
					{
						$html.="<br>".$retourAdresses['nbAdresses']." "._("résultats.")."<br>";
					}

					$html.=$gm->getMap(array('listeCoordonnees'=>$retourConfig['arrayConfigCoordonnees'],'urlImageIcon'=>$this->urlImages."pointGM.png",'pathImageIcon'=>$this->getCheminPhysique()."images/pointGM.png",'setAutomaticCentering'=>true));


				}
				else
				{
					$retourAdresses = $adresses->afficherListe($criteres, $modeAffichage);
					if($retourAdresses['nbAdresses']==0)
					{
						$html = "<h1>"._("Adresses :")."</h1>";
						$html .= _("Aucun résultat.");
					}
					else
					{
						$html = $retourAdresses['html'];
					}
				}
			}
		}

		return $html;
	}



	/*public function afficheResultatsRechercheAvancee($params = array())
	 {
	// *******************************************************************************************************
	// recherche pour la partie adresses
	$formulaire = new formGenerator();

	$arrayAdressesRechercheAdresses = array();
	$arrayListeEvenementsParents = array();

	$modeAffichage = 'recherche';



	if (isset($this->variablesGet['submitRechercheAvancee']))
	{
	$tabForm=array(
			'motcle'       => array('default'=> '' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
			'pays'         => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'pays', 'primaryKey'=> 'idPays')),
			'ville'           => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'ville', 'primaryKey'=> 'idVille')),
			'quartier'     => array('default'=> '0', 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'quartier', 'primaryKey'=> 'idQuartier')),
			'sousQuartier' => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'sousQuartier', 'primaryKey'=> 'idSousQuartier')),
			'rue'          => array('default'=> '0' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
					array('table'=> 'rue', 'primaryKey'=> 'idRue'))
	);

	$erreur = $formulaire->getArrayFromPost($tabForm, $_GET);


	if (count($erreur) == 0)
	{
	foreach ($tabForm AS $nom => $valeur)
	{
	$criteres['recherche_'.$nom] = $valeur['value'];
	}

	if(isset($this->variablesGet['longitude']) && isset($this->variablesGet['latitude']))
	{
	$criteres['recherche_latitude'] = $this->variablesGet['latitude'];
	$criteres['recherche_longitude']= $this->variablesGet['longitude'];
	$criteres['recherche_rayon'] = "100"; // on effectue la recherche dans un rayon de 100m
	}

	$adresses = new archiAdresse();
	$retourAdresses = $adresses->afficherListe($criteres, $modeAffichage,array('sqlNoLimit'=>true));
	$arrayAdressesRechercheAdresses = $retourAdresses['arrayIdAdresses'];
	//$html     .= $retourAdresses['html'];
	}
	}

	// *******************************************************************************************************
	// recherche pour la partie evenement


	if (isset($this->variablesGet['submitRechercheAvancee']))
	{

	$tabFormulaire = array(
			'motcle'    => array('default'=> '', 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
			'courant'       => array('default'=> '0'  , 'value' => '', 'required'=>false ,'error'=>'','type'=>'multiple', 'checkExist'=>
					array('table'=> 'courantArchitectural', 'primaryKey'=> 'idCourantArchitectural')),
			'typeStructure'    => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'typeStructure', 'primaryKey'=> 'idTypeStructure')),
			'typeEvenement'    => array('default'=> '0'  , 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'typeEvenement', 'primaryKey'=> 'idTypeEvenement')),
			'source'    => array('default'=> '0' , 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
					array('table'=> 'source', 'primaryKey'=> 'idSource')),
			'personnes'     => array('default'=> 'aucune' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
					array('table'=> 'personne', 'primaryKey'=> 'idPersonne')),
			'anneeDebut'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
			'anneeFin'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'numeric'),
			'MH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox'),
			'ISMH'=>array('default'=>'','value'=>'','required'=>false,'error'=>'','type'=>'checkbox')
	);

	$erreur = $formulaire->getArrayFromPost( $tabFormulaire, $_GET);

	if (count($erreur) == 0)
	{
	foreach ($tabFormulaire AS $nom => $valeur) {
	$criteres['recherche_'.$nom] = $valeur['value'];
	}


	$evenement = new archiEvenement();

	$arrayListeEvenements= $evenement->getIdEvenementsFromRecherche($criteres, 'listeEvenement.tpl', $modeAffichage);

	$arrayListeEvenements = array_unique($arrayListeEvenements);

	$adresse = new archiAdresse();

	$arrayListeEvenementsParents=array();
	foreach($arrayListeEvenements as $idEvenementFils)
	{
	//$resAdresse = $adresse->getAdressesFromEvenementGroupeAdresses($evenement->getParent($idEvenementFils));
	//$fetchAdresse = mysql_fetch_assoc($resAdresse);
	//$html .= "<a href=\"".$this->creerUrl('','adresseDetail',array('archiIdAdresse'=>$fetchAdresse['idAdresse']))."\">".stripslashes($adresse->getIntituleAdresse($fetchAdresse))."</a><br><br>";
	$arrayListeEvenementsParents[] = $evenement->getParent($idEvenementFils);
	}



	// dans le cas de l'affichage des sources du site , on va rajouter la liste des adresses ou les photos sont concernées aussi par la source courante
	if(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage'] == "listeAdressesFromSource" && isset($this->variablesGet['source']) && $this->variablesGet['source']!='')
	{
	$reqImages = "
	SELECT distinct ae.idEvenement as idEvenementGA
	FROM _adresseEvenement ae
	LEFT JOIN historiqueImage hi1 ON hi1.idSource = '".$this->variablesGet['source']."'
	LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
	LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
	LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
	WHERE 1=1
	AND ae.idEvenement = ee.idEvenement
	GROUP BY hi1.idImage, hi1.idHistoriqueImage
	HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)

	";

	$resImages = $this->connexionBdd->requete($reqImages);

	while($fetchImages = mysql_fetch_assoc($resImages))
	{
	$arrayListeEvenementsParents[] = $fetchImages['idEvenementGA'];
	}
	$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);

	if(isset($this->variablesGet['source']) && $this->variablesGet['source']=='24') // si la selection courante pour sur les archives municipales
	{
	// on comptabilise les images avec un numero d'archive sans idSource (comptabilisé avec la source "archives municipales"
			$reqImagesAvecNumeroArchiveSansIdSource="
			SELECT distinct ae.idEvenement as idEvenementGA
			FROM _adresseEvenement ae
			LEFT JOIN historiqueImage hi1 ON hi1.numeroArchive<>''
			LEFT JOIN historiqueImage hi2 ON hi2.idImage = hi1.idImage
			LEFT JOIN _evenementImage ei ON ei.idImage = hi1.idImage
			LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = ei.idEvenement
			WHERE 1=1
			AND (hi1.idSource='0' OR hi1.idSource='')
			AND ae.idEvenement = ee.idEvenement
			GROUP BY hi1.idImage, hi1.idHistoriqueImage
			HAVING hi1.idHistoriqueImage = max(hi2.idHistoriqueImage)
			";
			$resImagesAvecNumeroArchiveSansIdSource = $this->connexionBdd->requete($reqImagesAvecNumeroArchiveSansIdSource);

			while($fetchImagesAvecNumeroArchiveSansIdSource = mysql_fetch_assoc($resImagesAvecNumeroArchiveSansIdSource))
			{
			$arrayListeEvenementsParents[] = $fetchImagesAvecNumeroArchiveSansIdSource['idEvenementGA'];
			}
			$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);
			}


			if(isset($this->variablesGet['source']) && $this->variablesGet['source']=='24') // si la selection courante pour sur les archives municipales
	{
			// on comptabilise les evenements  avec un numero d'archive sans idSource (comptabilisé avec la source "archives municipales"
					$reqEvenementAvecNumeroArchiveSansIdSource="
					SELECT distinct ae.idEvenement as idEvenementGA
					FROM _adresseEvenement ae
					LEFT JOIN historiqueEvenement he1 ON he1.numeroArchive<>''
					LEFT JOIN historiqueEvenement he2 ON he2.idEvenement = he1.idEvenement
					LEFT JOIN _evenementEvenement ee ON ee.idEvenementAssocie = he1.idEvenement
					WHERE 1=1
					AND (he1.idSource='0' OR he1.idSource='')
					AND ae.idEvenement = ee.idEvenement
					GROUP BY he1.idEvenement, he1.idHistoriqueEvenement
					HAVING he1.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
					";
					$resEvenementAvecNumeroArchiveSansIdSource = $this->connexionBdd->requete($reqEvenementAvecNumeroArchiveSansIdSource);

					while($fetchEvenementAvecNumeroArchiveSansIdSource = mysql_fetch_assoc($resEvenementAvecNumeroArchiveSansIdSource))
					{
					$arrayListeEvenementsParents[] = $fetchEvenementAvecNumeroArchiveSansIdSource['idEvenementGA'];
					}
					$arrayListeEvenementsParents = array_unique($arrayListeEvenementsParents);
					}
					}


					//$retourAdresses = $adresse->afficherListe(array('groupesAdressesSupplementairesExternes'=>$arrayListeEvenementsParents), $modeAffichage);




					//$html.= $retourAdresses['html'];

					}

					}

					$arrayIdEvenementsGroupesAdressesResultats = array();
					if(count($arrayAdressesRechercheAdresses)>0 && count($arrayListeEvenementsParents)>0)
					{

					// on recoupe
					foreach($arrayListeEvenementsParents as $indice => $idEvenementGroupeAdresse)
					{
					// recherche des adresses du groupe d'adresse
					$reqIdEvenementsGroupeAdresses = "SELECT idAdresse FROM _adresseEvenement WHERE idEvenement='".$idEvenementGroupeAdresse."'";
					$resIdEvenementsGroupeAdresses = $this->connexionBdd->requete($reqIdEvenementsGroupeAdresses);
					while($fetchIdEvenementsGroupeAdresses = mysql_fetch_assoc($resIdEvenementsGroupeAdresses))
					{
					if(in_array($fetchIdEvenementsGroupeAdresses['idAdresse'],$arrayAdressesRechercheAdresses))
					{
					$arrayIdEvenementsGroupesAdressesResultats[] = $idEvenementGroupeAdresse;
					}
					}

					}
					}
					elseif(count($arrayAdressesRechercheAdresses)>0 || count($arrayListeEvenementsParents)>0)
					{
					if(count($arrayAdressesRechercheAdresses)>0)
					{
					// recherche des groupes d'adresses des adresses correspondantes
					foreach($arrayAdressesRechercheAdresses as $indice => $idAdresse)
					{
					$reqAdresses = "SELECT idEvenement FROM _adresseEvenement WHERE idAdresse='".$idAdresse."'";
					$resAdresses = $this->connexionBdd->requete($reqAdresses);
					while($fetchAdresses = mysql_fetch_assoc($resAdresses))
					{
					$arrayIdEvenementsGroupesAdressesResultats[] = $fetchAdresses['idEvenement'];
					}
					}
					}

					if(count($arrayListeEvenementsParents)>0)
					{
					$arrayIdEvenementsGroupesAdressesResultats = $arrayListeEvenementsParents;
					}
					}


					if(count($arrayIdEvenementsGroupesAdressesResultats)>0)
					{
					$arrayIdEvenementsGroupesAdressesResultats = array_unique($arrayIdEvenementsGroupesAdressesResultats);
					$retourAdresses = $adresse->afficherListe(array('groupesAdressesSupplementairesExternes'=>$arrayIdEvenementsGroupesAdressesResultats), $modeAffichage);
					$html= $retourAdresses['html'];
					} else {
					$html="aucun résultat.";
					}



					return $html;
					}*/

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

		$t->assign_vars(array(
				'formAction' => $this->creerUrl('','rechercheAvAdresse'),
				'formulaireChoixAdresse' => $adresse->afficheChoixAdresse()));

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

	public function afficheFormulaireRechercheAvancee($params = array())
	{
		//advancedSearch
		$html = "";
		$html .= "<form action='".$this->creerUrl('','advancedSearch',array())."' name='formRechercheAvancee' method='GET' enctype='multipart/form-data'>";
		//$html .= "<form action='".$this->creerUrl('','resultatsRechercheAvancee',array())."' name='formRechercheAvancee' method='GET' enctype='multipart/form-data'>";
		$html .= $this->afficheFormulaireAdresse(array(),'rechercheAvancee',array('titre'=>_("Recherche avancée"),"noFormElement"=>true,"noRechercheParMotCle"=>false));
		$html .= $this->afficheFormulaireEvenement(array(), array('modeAffichage'=>'rechercheAvancee','noTitre'=>true,"noFormElement"=>true,"noRechercheParMotCle"=>true));
		$html .= "<input type='submit' name='submitRechercheAvancee' value='Recherche'>&nbsp;&nbsp;";
		$html .= "<input type='checkbox' name='afficheResultatsSurCarte' id='afficheResultatsSurCarte' value='1'>&nbsp;"._("Afficher les 100 premiers résultats sur une carte.");
		$html .= "</form>";

		return $html;
	}



	public function getPopupChoixEvenement($modeAffichage='resultatRechercheCalqueEvenement')
	{
		$html="";

		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('popupChoixEvenement'=>'popupChoixEvenement.tpl')));

		switch($modeAffichage)
		{
			case 'resultatRechercheCalqueEvenement':
				$critereModeAffichage='calqueEvenement';
				break;
			case 'resultatRechercheEvenementCalqueImageChampMultiple':
				$critereModeAffichage='calqueEvenementChampsMultiples';
				break;
			default:
				$critereModeAffichage='calqueEvenement';
				break;
		}

		$t->assign_vars(array(

				'iframeSrc' => $this->creerUrl('','rechercheAvEvenementPopup',array('noHeaderNoFooter'=>'1','modeAffichage'=>$critereModeAffichage))));
		//'contenuCalque'   => $this->rechercheAvanceeEvenement(array('resultatRechercheCalqueEvenement'=> 1))
		//'boutonRecherche' => $this->creerUrl('rechercheAvEvenementPopUp','',array('noHeaderNoFooter'=>'1', 'sansFormulaire'=>1, 'modeAffichage'=>'calqueEvenement')),
		ob_start();
		$t->pparse('popupChoixEvenement');
		$html=ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getPopupChoixAdresse($modeAffichage='resultatRechercheAdresseCalqueImage')
	{
		$html="";
		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('popupChoixAdresse'=>'popupChoixAdresse.tpl')));

		switch($modeAffichage)
		{
			case 'resultatRechercheAdresseCalqueImage':
				$critereModeAffichage = 'calqueImage';
				break;
			case 'resultatRechercheAdresseCalqueImageChampMultiple':
				$critereModeAffichage = 'calqueImageChampsMultiples';
				break;
			case 'resultatRechercheAdresseCalqueImageChampMultipleRetourSimple':
				$critereModeAffichage = 'calqueImageChampsMultiplesRetourSimple';
				break;
			default:
				$critereModeAffichage = 'calqueEvenement';
				break;
		}


		// on defini le contenu de la popup
		$t->assign_vars(array('iframeSrc' => $this->creerUrl('','rechercheAvAdressePopup',array('noHeaderNoFooter'=>'1','modeAffichage'=>$critereModeAffichage))));


		//'boutonRecherche' => $this->creerUrl('','rechercheAvAdressePopup',array('noHeaderNoFooter' => '1',  'sansFormulaire'=>1, 'modeAffichage'=>$critereModeAffichage)),
		//'contenuCalque'   => $this->rechercheAvanceeAdresse(array('resultatRechercheCalqueEvenement' => 1))

		ob_start();
		$t->pparse('popupChoixAdresse');
		$html=ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function getPopupChoixPersonne($modeAffichage='resultatRechercheCalquePersonne')
	{
		$html="";
		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('popupChoixPersonne'=>'popupChoixPersonne.tpl')));

		$t->assign_vars(array('iframeSrc'=> $this->creerUrl('','personneListe',array('noHeaderNoFooter'=>'1','modeAffichage'=>$modeAffichage)))); // on defini le contenu de la popup

		ob_start();
		$t->pparse('popupChoixPersonne');
		$html=ob_get_contents();
		ob_end_clean();

		return $html;
	}


	public function getPopupChoixSource($modeAffichage='resultatRechercheCalqueSource')
	{
		$html="";
		$t=new Template('modules/archi/templates/');
		$t->set_filenames((array('popupChoixSource'=>'popupChoixSource.tpl')));

		// on definit le contenu de la popup
		$t->assign_vars(array('iframeSrc'=> $this->creerUrl('','sourceListe',array('noHeaderNoFooter'=>'1','modeAffichage'=>$modeAffichage))));


		ob_start();
		$t->pparse('popupChoixSource');
		$html=ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function afficheCarteRecherche($params=array())
	{
		$html="";

		$s = new objetSession();
		$a = new archiAdresse();
		$okCoord = false;
		if(isset($params['centrerSurVilleGeneral']) && $params['centrerSurVilleGeneral']==true && $s->isInSession('archiIdVilleGeneral') && $s->getFromSession('archiIdVilleGeneral')!='')
		{
			// on recupere les coordonnées geogrphiques de la ville
			$reqCoordVille = "SELECT longitude,latitude FROM ville WHERE idVille = '".$s->getFromSession('archiIdVilleGeneral')."'";
			$resCoordVille = $this->connexionBdd->requete($reqCoordVille);
			if(mysql_num_rows($resCoordVille)>0)
			{
				$fetchCoordVille = mysql_fetch_assoc($resCoordVille);
				if($fetchCoordVille['longitude']!='' && $fetchCoordVille['latitude']!='')
				{
					$villeLongitude = $fetchCoordVille['longitude'];
					$villeLatitude = $fetchCoordVille['latitude'];
					$okCoord = true;
				}
			}

		}

		if(!$okCoord)
		{
			$villeLongitude = "7.7400"; // strasbourg
			$villeLatitude = "48.585000";
		}


		// recherche des villes
		$arrayRetourIdVilles = $this->getIdVillesNotEmpty();

		$arrayCoords = array();
		foreach($arrayRetourIdVilles['coordonneesParIdVille'] as $idVille => $value)
		{

			if($value['longitude']!='' && $value['latitude']!='')
			{
				$arrayNomVille = $a->getInfosVille($idVille,array("fieldList"=>'v.nom as nomVille'));
				$nomVille = $arrayNomVille['nomVille'];

				$arrayCoords[] = array(
						'libelle'=>'NomVille',
						'longitude'=>$value['longitude'],
						'latitude'=>$value['latitude'],
						'jsCodeOnClickMarker'=>"location.href='".$this->creerUrl('','afficheAccueil',array("archiNomVilleGeneral"=>$nomVille))."';"
				);
			}
		}

		$zoom=15;
		if(($s->isInSession('archiIdVilleGeneral') && $s->getFromSession('archiIdVilleGeneral')=='1') || !$s->isInSession('archiIdVilleGeneral'))
		{    // pour strasbourg on met un zoom un peu plus eloigné
			$zoom=13;
		}
		elseif(isset($params['zoom']))
		{
			$zoom = $params['zoom'];
		}


		// si le zoom est assez proche , on affiche les points des adresses autour du centre courant de la carte
		/*if($zoom>13)
		{

		$arrayGoogleMapCoord = $a->getArrayGoogleMapConfigCoordonneesFromCenter(array('longitude'=>$villeLongitude,'latitude'=>$villeLatitude,'rayon'=>500,'urlIcon'=>$this->urlImages."pointGM.png","dimIconX"=>'9',"dimIconY"=>'9'));
		$listeCoords = $arrayGoogleMapCoord['arrayConfigCoordonnees'];


		$arrayCoords = array_merge($arrayCoords,$listeCoords);

		}

		*/


		$gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'zoom'=>$zoom,'height'=>500,'width'=>700,'centerLong'=>$villeLongitude,'centerLat'=>$villeLatitude));

		$this->addToJsHeader($gm->getJsFunctions());

		//$html.="<h2>Cliquez sur un point de la carte pour afficher la liste des immeubles a proximité.</h2>";

		$html.="<h2>Recherche par carte</h2>";


		$html.=$gm->getMap(array('listeCoordonnees'=>$arrayCoords,"urlImageIcon"=>$this->getUrlImage(null, "maison2.png"),"pathImageIcon"=>$this->getCheminPhysique()."images/maison2.png")); // load

		// affichage de la legende sous la carte
		$t = new tableau();
		$html.="<h3>Légende : </h3>";
		$t->addValue("<img src='".$this->urlImages."maison2.png' border=0>","align=center");
		$t->addValue("Cliquez sur la maison bleue pour accéder à l'accueil de la ville.");
		$t->addValue("<img src='".$this->urlImages."legendeCarteGM.jpg' border=0>","align=center");
		$t->addValue("Cliquez sur la rue qui vous intéresse pour voir les bâtiments qui bordent la zone.");

		$html.=$t->createHtmlTableFromArray(2);

		// evenement quand on clique sur la carte ( mais pas sur une maison
		$html.=$gm->setOnClickEvent(array('jsCode'=>"location.href='?archiAffichage=recherche&submit=Rechercher&longitude='+point.lng()+'&latitude='+point.lat();"));

		/*if($zoom>13)
		 {

		$html.="<script  >";
		$html.="GEvent.addListener(
				map,
				'dragend',
				function(){appelAjaxReturnJs('".$this->creerUrl('','majGoogleMapNewCenter',array('noRefresh'=>0,'noHTMLHeaderFooter'=>1,'noHeaderNoFooter'=>1,'latitudeHome'=>$villeLatitude,'longitudeHome'=>$villeLongitude))."&longitudeCenter='+map.getCenter().lng()+'&latitudeCenter='+map.getCenter().lat()+'&rayon=800','divListeAdressesAjax')}
		);";
		$html.="</script>";
		$html.="<div id='divListeAdressesAjax' style='background-color:lime;'></div>";
		}
		*/



		return $html;
	}

	// renvoi l'arborescente courante basée sur les villes du bas rhin , donc la ville courante selectionnée , ou strasbourg par defaut
	public function getHtmlArborescence()
	{
		$liens=array();
		$adresse = new archiAdresse();

		$s = new objetSession();

		if (isset($this->variablesGet['archiAffichage'])
		&& $this->variablesGet['archiAffichage']=='evenement'
				&& isset($this->variablesGet['idEvenement'])
				&& $this->variablesGet['idEvenement']!=''
						) {
			$fetchVille = $adresse->getInfosVille(
					$adresse->getIdVilleFrom($this->variablesGet['idEvenement'],'idEvenementGroupeAdresse'),
					array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays')
			);
			$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
			$liens[] = array(
					'libelle'=>$fetchVille['nomVille'],
					'url'=>$this->creerUrl(
							'', 'afficheAccueil',
							array('archiIdVilleGeneral'=>$fetchVille['idVille'],
									'archiIdPaysGeneral'=>$fetchVille['idPays'])
					)
			);
			$s->addToSession('archiIdVilleGeneral', $fetchVille['idVille']);
		}
		elseif (((isset($this->variablesGet['archiAffichage'])
				&& $this->variablesGet['archiAffichage']!='afficheCarteBasRhin')
				|| !isset($this->variablesGet['archiAffichage']))
				&& (isset($this->variablesGet['archiIdAdresse'])
						||isset($this->variablesGet['archiIdImage']))) {
			if (isset($this->variablesGet['archiIdAdresse'])
			&& $this->variablesGet['archiIdAdresse']!=''
					) {
				$fetchVille = $adresse->getInfosVille(
						$adresse->getIdVilleFrom($this->variablesGet['archiIdAdresse'],'idAdresse'),
						array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays')
				);
				$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
				$liens[] = array(
						'libelle'=>$fetchVille['nomVille'],
						'url'=>$this->creerUrl(
								'', 'afficheAccueil',
								array('archiIdVilleGeneral'=>$fetchVille['idVille'],
										'archiIdPaysGeneral'=>$fetchVille['idPays'])
						)
				);
				$s->addToSession('archiIdVilleGeneral', $fetchVille['idVille']);

				$arrayAdresse = $adresse
				->getArrayAdresseFromIdAdresse(
						$this->variablesGet['archiIdAdresse']
				);


				if ($arrayAdresse['nomQuartier']!=''
						&& strtolower($arrayAdresse['nomQuartier'])!='autre'
								) {
					$liens[] = array(
							'libelle'=>ucfirst($arrayAdresse['nomQuartier']),
							'url'=>$this->creerUrl(
									'', 'adresseListe',
									array('recherche_quartier'=>$arrayAdresse['idQuartier'])
							)
					);

					if ($arrayAdresse['nomSousQuartier']!=''
							&& strtolower($arrayAdresse['nomSousQuartier'])!='autre'
									) {
						$liens[] = array(
								'libelle'=>ucfirst($arrayAdresse['nomSousQuartier']),
								'url'=>$this->creerUrl(
										'', 'adresseListe', array(
												'recherche_sousQuartier'=>$arrayAdresse['idSousQuartier']
										)
								)
						);
					}
				}
			}
			elseif (isset($this->variablesGet['archiIdImage'])
					&& $this->variablesGet['archiIdImage']!=''
			) {
				$i = new archiImage();
				$fetchVille = $adresse->getInfosVille($adresse->getIdVilleFrom($i->getIdAdresseFromIdImage($this->variablesGet['archiIdImage']),'idAdresse'),array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays'));
				$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
				$liens[] = array('libelle'=>$fetchVille['nomVille'] , 'url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$fetchVille['idVille'],'archiIdPaysGeneral'=>$fetchVille['idPays'])));
				$s->addToSession('archiIdVilleGeneral',$fetchVille['idVille']);
			}
		}
		elseif (isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='adresseListe' && isset($this->variablesGet['recherche_quartier']) && $this->variablesGet['recherche_quartier'])
		{
			$fetchVille = $adresse->getInfosVille($adresse->getIdVilleFrom($this->variablesGet['recherche_quartier'],'idQuartier'),array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays'));
			$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
			$liens[] = array('libelle'=>$fetchVille['nomVille'] , 'url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$fetchVille['idVille'],'archiIdPaysGeneral'=>$fetchVille['idPays'])));
			$s->addToSession('archiIdVilleGeneral',$fetchVille['idVille']);

			$reqQuartier = "SELECT idQuartier,nom as nomQuartier FROM quartier WHERE idQuartier='".$this->variablesGet['recherche_quartier']."'";
			$resQuartier = $this->connexionBdd->requete($reqQuartier);
			if(mysql_num_rows($resQuartier)>0)
			{
				$fetchQuartier = mysql_fetch_assoc($resQuartier);

				if($fetchQuartier['nomQuartier']!='' && strtolower($fetchQuartier['nomQuartier'])!='autre')
				{
					$liens[] = array('libelle'=>ucfirst($fetchQuartier['nomQuartier']),'url'=>$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetchQuartier['idQuartier'])));
				}
			}
		}
		elseif(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='adresseListe' && isset($this->variablesGet['recherche_sousQuartier']) && $this->variablesGet['recherche_sousQuartier']!='')
		{
			$fetchVille = $adresse->getInfosVille($adresse->getIdVilleFrom($this->variablesGet['recherche_sousQuartier'],'idSousQuartier'),array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays'));
			$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
			$liens[] = array('libelle'=>$fetchVille['nomVille'] , 'url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$fetchVille['idVille'],'archiIdPaysGeneral'=>$fetchVille['idPays'])));
			$s->addToSession('archiIdVilleGeneral',$fetchVille['idVille']);

			$reqSousQuartier = "
					SELECT sq.idSousQuartier as idSousQuartier,sq.nom as nomSousQuartier, q.nom as nomQuartier , q.idQuartier as idQuartier
					FROM sousQuartier sq
					LEFT JOIN quartier q ON q.idQuartier = sq.idQuartier
					WHERE sq.idSousQuartier='".$this->variablesGet['recherche_sousQuartier']."'";

			$resSousQuartier = $this->connexionBdd->requete($reqSousQuartier);
			if(mysql_num_rows($resSousQuartier)>0)
			{
				$fetchSousQuartier = mysql_fetch_assoc($resSousQuartier);


				if($fetchSousQuartier['nomQuartier']!='' && strtolower($fetchSousQuartier['nomQuartier'])!='autre')
				{
					$liens[] = array('libelle'=>ucfirst($fetchSousQuartier['nomQuartier']),'url'=>$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetchSousQuartier['idQuartier'])));
				}

				if($fetchSousQuartier['nomSousQuartier']!='' && strtolower($fetchSousQuartier['nomSousQuartier'])!='autre')
				{
					$liens[] = array('libelle'=>ucfirst($fetchSousQuartier['nomSousQuartier']),'url'=>$this->creerUrl('','adresseListe',array('recherche_sousQuartier'=>$fetchSousQuartier['idSousQuartier'])));
				}
			}
		}
		elseif(isset($this->variablesGet['archiAffichage']) && $this->variablesGet['archiAffichage']=='listeDossiers' && isset($this->variablesGet['archiIdQuartier']) && $this->variablesGet['archiIdQuartier']!=''  && isset($this->variablesGet['modeAffichageListe']) && $this->variablesGet['modeAffichageListe']=='parRuesDeQuartier')
		{
			$fetchVille = $adresse->getInfosVille($adresse->getIdVilleFrom($this->variablesGet['archiIdQuartier'],'idQuartier'),array('fieldList'=>'v.idVille as idVille,v.nom as nomVille,v.idPays as idPays, p.nom as nomPays'));
			$liens[] = array('libelle'=>$fetchVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
			$liens[] = array('libelle'=>$fetchVille['nomVille'] , 'url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$fetchVille['idVille'],'archiIdPaysGeneral'=>$fetchVille['idPays'])));
			$s->addToSession('archiIdVilleGeneral',$fetchVille['idVille']);


			$reqQuartier = "SELECT idQuartier, nom FROM quartier WHERE idQuartier = '".$this->variablesGet['archiIdQuartier']."'";

			$resQuartier = $this->connexionBdd->requete($reqQuartier);
			if(mysql_num_rows($resQuartier)>0)
			{
				$fetchQuartier = mysql_fetch_assoc($resQuartier);

				if($fetchQuartier['nom']!='' && strtolower($fetchQuartier['nom'])!='autre')
				{
					$liens[] = array('libelle'=>ucfirst($fetchQuartier['nom']),'url'=>$this->creerUrl('','adresseListe',array('recherche_quartier'=>$fetchQuartier['idQuartier'])));
				}
			}




		}
		elseif($s->isInSession('archiIdVilleGeneral') && $s->getFromSession('archiIdVilleGeneral')!='')
		{
			$arrayInfosVille = $adresse->getInfosVille($s->getFromSession('archiIdVilleGeneral'),array("fieldList"=>" v.nom as nomVille,v.idVille as idVille, v.idPays as idPays, p.nom as nomPays"));
			$liens[] = array('libelle'=>$arrayInfosVille['nomPays'],'url'=>$this->creerUrl('','afficheCarteBasRhin'));
			$liens[] = array('libelle'=>$arrayInfosVille['nomVille'],'url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$arrayInfosVille['idVille'],'archiIdPaysGeneral'=>$arrayInfosVille['idPays'])));
		} else {
			$liens[] = array('libelle'=>'Strasbourg','url'=>$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>1,'archiIdPaysGeneral'=>1)));
		}



		$retour="";
		foreach($liens as $indice => $value)
		{

			$retour.="&nbsp;<a href='".$value['url']."'>"
					.$value['libelle']."</a>&nbsp;>";
		}


		$retour=pia_substr($retour,0,-1);

		return $retour;
	}

	// affiche la carte googleMap du bas rhin avec les villes ou il y a des adresses avec des evenements dans la base de donnée
	/*public function getGoogleMapBasRhin()
	{
	$html="";
	$gm = new googleMap(array('googleMapKey'=>$this->googleMapKey,'height'=>500,'width'=>600));
	$a = new archiAdresse();

	// recuperation de la liste des villes ou il y a des adresses
	$arrayRetourIdVilles = $this->getIdVillesNotEmpty();

	$arrayCoords = array();
	foreach($arrayRetourIdVilles['coordonneesParIdVille'] as $idVille => $value)
	{

	if($value['longitude']!='' && $value['latitude']!='')
	{
	$arrayNomVille = $a->getInfosVille($idVille,array("fieldList"=>'v.nom as nomVille'));
	$nomVille = $arrayNomVille['nomVille'];

	$arrayCoords[] = array(
			'libelle'=>'NomVille',
			'longitude'=>$value['longitude'],
			'latitude'=>$value['latitude'],
			'jsCodeOnClickMarker'=>"location.href='".$this->creerUrl('','afficheAccueil',array("archiNomVilleGeneral"=>$nomVille))."';"
	); // "location.href='".$this->creerUrl('','afficheAccueil',array('archiIdVilleGeneral'=>$idVille,'archiIdPaysGeneral'=>$value['idPays']))."';"
	}
	}

	$html.="<h1>Choix de la ville courante</h1>";

	$html.=$gm->getJsFunctions();

	$html.=$gm->getMap(array('listeCoordonnees'=>$arrayCoords,"urlImageIcon"=>$this->urlImages."maison2.png","pathImageIcon"=>$this->cheminPhysique."images/maison2.png"));

	return $html;
	}*/

	public function getIdVillesNotEmpty($params=array())
	{
		// une ville doit s'afficher si:
		// elle comporte des adresses qui comportent elle meme des evenements
		// les adresses sont selectionnees directement ou indirectement par les rues quartiers sousquartiers lui appartenant
		$a = new archiAdresse();

		// liste des adresses qui ont des evenements
		$req = "

				SELECT  ha1.idAdresse,

				IF(ha1.idVille<>0,     ha1.idVille,

				IF(ha1.idQuartier<>0, (SELECT idVille FROM quartier WHERE idQuartier = ha1.idQuartier),
				IF(ha1.idSousQuartier<>0,(SELECT idVille FROM quartier WHERE idQuartier = (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier =ha1.idSousQuartier )),
				IF(ha1.idRue<>0,(SELECT idVille FROM quartier WHERE idQuartier IN (SELECT idQuartier FROM sousQuartier WHERE idSousQuartier = (SELECT idSousQuartier FROM rue WHERE idRue = ha1.idRue))),0
				)
				)
				)
				)
				as idVilleAdresse


				FROM historiqueAdresse ha1

				LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
				LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse


				WHERE ha1.idPays=0

				GROUP BY ha1.idAdresse,ae.idAdresse,ha1.idHistoriqueAdresse
				HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) AND count(ae.idAdresse)>0

				";
		$res = $this->connexionBdd->requete($req);
		$arrayIdVilles = array();
		$coordonnees=array();
		while($fetch = mysql_fetch_assoc($res))
		{
			//$reqVille = "SELECT nom FROM ville WHERE idVille='".$fetch['idVilleAdresse']."'";
			//$resVille = $this->connexionBdd->requete($reqVille);
			//$fetchVille = mysql_fetch_assoc($resVille);
			//if($fetchVille['nom']!='autre')
			//{
			$arrayIdVilles[] = $fetch['idVilleAdresse'];
			$villeCoordonnees = "";

			$fetchInfosVille = $a->getInfosVille($fetch['idVilleAdresse'],array("fieldList"=>"longitude,latitude,v.idPays as idPays"));

			if(!isset($coords[$fetch['idVilleAdresse']]) && $fetchInfosVille['latitude']!='' && $fetchInfosVille['longitude']!='')
				$coords[$fetch['idVilleAdresse']] = array('latitude'=>$fetchInfosVille['latitude'],'longitude'=>$fetchInfosVille['longitude'],'idPays'=>$fetchInfosVille['idPays']); // recupere les coordonnées d'une adresse appartenant a la ville
			//}
		}

		$arrayIdVilles = array_unique($arrayIdVilles);
		return array('arrayIdVilles'=>$arrayIdVilles,'coordonneesParIdVille'=>$coords);
	}




	public function getIdRuesNotEmpty($params=array())
	{
		// liste des rues qui n'ont pas d'adreses
		$req = "

				SELECT  ha1.idAdresse,

				ha1.idRue as idRueAdresse

				FROM historiqueAdresse ha1

				LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
				LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse


				WHERE ha1.nom != ''
				GROUP BY ha1.idAdresse,ae.idAdresse,ha1.idHistoriqueAdresse
				HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and count(ae.idEvenement)>0

				";

		$res = $this->connexionBdd->requete($req);
		$arrayListeRuesNonVides=array();

		while($fetch = mysql_fetch_assoc($res))
		{
			if(!in_array($fetch['idRueAdresse'],$arrayListeRuesNonVides))
				$arrayListeRuesNonVides[] = $fetch['idRueAdresse'];
		}



		return array("arrayIdRues"=>$arrayListeRuesNonVides);
	}

	public function getIdQuartiersNotEmpty($params=array())
	{

		// liste des quartiers non vides
		$req = "

				SELECT  ha1.idAdresse,

				IF(ha1.idQuartier<>0, ha1.idQuartier,
				IF(ha1.idSousQuartier<>0,(SELECT idQuartier FROM sousQuartier WHERE idSousQuartier =ha1.idSousQuartier ),
				IF(ha1.idRue<>0,(SELECT idQuartier FROM sousQuartier WHERE idSousQuartier = (SELECT idSousQuartier FROM rue WHERE idRue = ha1.idRue)),0
				)
				)
				)
				as idQuartierAdresse
				,count(ae.idEvenement)


				FROM historiqueAdresse ha1

				LEFT JOIN historiqueAdresse ha2 ON ha2.idAdresse = ha1.idAdresse
				LEFT JOIN _adresseEvenement ae ON ae.idAdresse = ha1.idAdresse

				WHERE ha1.nom!=''

				GROUP BY ha1.idAdresse,ae.idAdresse,ha1.idHistoriqueAdresse
				HAVING ha1.idHistoriqueAdresse = max(ha2.idHistoriqueAdresse) and count(ae.idEvenement)>0

				";


		$res = $this->connexionBdd->requete($req);
		$arrayListeQuartiersNonVides=array();

		while($fetch = mysql_fetch_assoc($res))
		{
			if(!in_array($fetch['idQuartierAdresse'],$arrayListeQuartiersNonVides))
				$arrayListeQuartiersNonVides[] = $fetch['idQuartierAdresse'];
		}
		$arrayListeQuartiersNonVides = array_unique($arrayListeQuartiersNonVides);

		return array("arrayListeQuartiersNonVides"=>$arrayListeQuartiersNonVides);

	}



	/*
	 * Make a classic search with keywords only
	*/
	public function search(){
		$html = '';
		$tabForm = array();
		$resAvAdresse="";
		$formulaire = new formGenerator();
		$checkedCheckBox  = false;


		if(!empty($this->variablesGet['motcle'])){
			$html.="pas empty";

		}
		//If no keyword specified, show an error
		//TODO : Create a common error method showing an error message with a red panel
		else{
			$html.="Erreur ! Aucun mot-clé n'a été spécifié. Veuillez entrer un mot-clé pour effectuer une recherche.";
		}

		return $this->advancedSearch();
	}
	
	
	/*
	 * Make an advanced search with criterias such as city or street specified
	*/
	public function advancedSearch(){
		$html = '';
		$tabForm = array();
		$resAvAdresse="";
		$formulaire = new formGenerator();
		$checkedCheckBox  = false;
		$sqlWhereTab = array();

		/*
		 * Detecting parameters selected in the form and build the query with those criterias
		*/
		$tabForm=array(
				'motcle'       => array('default'=> '' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'text'),
				'pays'         => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
						array('table'=> 'pays', 'primaryKey'=> 'idPays')),
				'ville'           => array('default'=> '0', 'value' => '', 'required'=>false ,'error'=>'','type'=>'numeric', 'checkExist'=>
						array('table'=> 'ville', 'primaryKey'=> 'idVille')),
				'quartier'     => array('default'=> '0', 'value' => '', 'required'=>false  ,'error'=>'','type'=>'numeric', 'checkExist'=>
						array('table'=> 'quartier', 'primaryKey'=> 'idQuartier')),
				'sousQuartier' => array('default'=> '0', 'value' => '', 'required'=>false, 'error'=>'','type'=>'numeric', 'checkExist'=>
						array('table'=> 'sousQuartier', 'primaryKey'=> 'idSousQuartier')),
				'rue'          => array('default'=> '0' , 'value' => '', 'required'=>false,'error'=>'','type'=>'multiple', 'checkExist'=>
						array('table'=> 'rue', 'primaryKey'=> 'idRue'))
		);


		$sqlWhere = $this->buildWhereClause($this->variablesGet); // Generating the where clause with the criterias
		$idHistoriqueEvenementArray = $this->getIdHistoAdresses($sqlWhere); //Execute the fulltext research with the Where clause generated by

		$a = new archiAdresse();
		$html .=$a->displayList($idHistoriqueEvenementArray);
		return $html;
	}

	
	
	/**
	 * Return the where clause of the reseach with the criterias in parameter
	 * @param research $criterias :
	 * Expecting to have the following type of array to work properly
	 * Ex :
	 * *************************************************************************
	 * Array
		(
		[archiAffichage] => advancedSearch
		[motcle] =>
		[pays] => 1
		[ville] => 1
		[quartier] => 1
		[sousQuartier] => 19
		[rue] => 1168
		[source] => 1
		[typeStructure] => 3
		[typeEvenement] => 1
		[courant] => Array
		(
		[0] => 1
		[1] => 6
		[2] => 11
		)

		[anneeDebut] => 1995
		[anneeFin] => 2010
		[MH] => 1
		[ISMH] => 1
		[submitRechercheAvancee] => Recherche
		)
	 * *************************************************************************
	 */
	private function buildWhereClause($criterias ){
		debug($criterias);
		$sqlWhere = '';
		$sqlWhereTab = array();
		$motcle="";
		if(isset($criterias['motcle'])){
			$motcle = $criterias['motcle'];
		}
				$arrayCriterias = array(
				array('motcle',$motcle),
				array('pays' ,'idPays'),
				array('ville','idVille'),
				array('quartier','idQuartier'),
				array('sousQuartier','idSousQuartier'),
				array('typeStructure','idTypeStructure'),
				array('typeEvenement','idTypeEvenement'),
				array('source','idsource'),
				array('anneeDebut','dateDebut'),
				array('anneeFin', 'dateFin'),
				array('MH','MH'), //Monuement historique
				array('ISMH','ISMH')

		);
				
		foreach ($arrayCriterias as $id){
			if(isset($criterias[$id[0]])){
				if($id[0]!='motcle'){
					if($criterias[$id[0]]!=0){
						$sqlWhereTab[] = $id[1].' = '.$criterias[$id[0]].'';
					}
				}
				else{
					if(!empty($criterias[$id[0]])){
						$sqlWhereTab[] = "MATCH(nomRue, nomQuartier, nomSousQuartier, nomVille, nomPays, prefixeRue,numeroAdresse,  description, titre , nomPersonne, prenomPersonne, concat1,concat2,concat3) AGAINST ('".$criterias[$id[0]]."') ";
					}
				}
			}
		}

		/*
		 *  Where clause assembly
		*/
		$numItems = count($sqlWhereTab);
		$i = 0;

		if($numItems>0){
			$sqlWhere = "WHERE ";
			foreach ($sqlWhereTab as $clause){
				//Finding the last occurence
				if(++$i === $numItems) {
					$sqlWhere  .= $clause . " ";
				}
				//Regulare cases
				else{
					$sqlWhere  .= $clause . " AND ";
				}
			}
		}
		
		return $sqlWhere;
	}


	/**
	 * 
	 * @param string $sqlWhere : Prebuild where clause
	 * @return multitype:unknown
	 */
	private function getIdHistoAdresses($sqlWhere = '',$motcle =''){
		if(isset($motcle) && $motcle!=''){
			$request = "SELECT idHistoriqueAdresse, idEvenementGA, nomRue,nomSousQuartier,nomQuartier,nomVille,nomPays,prefixeRue,description,titre,nomPersonne, prenomPersonne, numeroAdresse,concat1,concat2,concat3 ,
				(
				10 * (MATCH (nomRue) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				10 * (MATCH (nomSousQuartier) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				10 * (MATCH (nomQuartier) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				10 * (MATCH (nomVille) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				10 * (MATCH (nomPays) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				1 * (MATCH (description) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				1 * (MATCH (titre) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				1000 * (MATCH (concat1) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				100 * (MATCH (concat2) AGAINST ('".$motcle."' IN BOOLEAN MODE)) +
				100 * (MATCH (concat3) AGAINST ('".$motcle."' IN BOOLEAN MODE))
			
				) as relevance
			
				FROM recherchetmp "
					.$sqlWhere.
					"ORDER BY relevance DESC
				;";
		}
		else{
			$request = "SELECT idHistoriqueAdresse, idEvenementGA, nomRue,nomSousQuartier,nomQuartier,nomVille,nomPays,prefixeRue,description,titre,nomPersonne, prenomPersonne, numeroAdresse,concat1,concat2,concat3 ,
			1 as relevance
			
				FROM recherchetmp "
					.$sqlWhere.
					"ORDER BY relevance DESC
				;";
		}
		
		$idHistoriqueAdresse  = array();
		$res = $this->connexionBdd->requete($request);
		while($fetch = mysql_fetch_assoc($res)){
			$idHistoriqueAdresse[] = $fetch['idHistoriqueAdresse'];
		}
		return $idHistoriqueAdresse;
	}
}
?>
