<?php

class archiSource extends config 
{

	public $idSource;
	public $nom;
	public $description;
	public $type;
	
	function __construct() {
		parent::__construct();
	}

	public function ajouter() 
	{
		$newIdSource=0;
		$formulaire = new formGenerator();	
		if (isset($this->variablesPost['submit']))
		{
			$modeAffichage='';
			if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']!='')
			{
				$modeAffichage = $this->variablesGet['modeAffichage'];
			}
			
			switch($modeAffichage)
			{
				case 'nouveauDossier':
				case 'modifEvenement':
					$tabForm = array(
						'nom'			=> array('default'=> '', 'value' => '', 'required'=>true,'error'=>'','type'=>'text'),
						'description'	=> array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
						'type'			=> array('default'=> 'aucune' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'numeric', 'checkExist'=>
										array('table'=> 'typeSource', 'primaryKey'=> 'idTypeSource')));
				
				break;
				default:
					$tabForm = array(
						'nom'		=> array('default'=> '', 'value' => '', 'required'=>true,'error'=>'','type'=>'text'),
						'description'	=> array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
						'nomNouveauType'=> array('default'=> '', 'value' => '', 'required'=>false,'error'=>'','type'=>'text'),
						'type'		=> array('default'=> 'aucune' , 'value' => '', 'required'=>false, 'error'=>'', 'type'=>'numeric', 'checkExist'=>
										array('table'=> 'typeSource', 'primaryKey'=> 'idTypeSource')));
				break;
			
			}

			$erreur = $formulaire->getArrayFromPost($tabForm);
			
			if (count($erreur) === 0)
			{
				$nom    = mysql_escape_string($tabForm['nom']['value']);
				
				$description='';
				if(isset($tabForm['description']['value']))
					$description   = mysql_escape_string(htmlspecialchars($tabForm['description']['value']));
				
					
				
				//
				// Récupération de l'id du type de source

				// c'est un nouveau type de source :
				if (isset($tabForm['nomNouveauType']['value']) &&  !empty( $tabForm['nomNouveauType']['value'] ))
				{
					
					// vérification si le type n'existe pas déjà
					$nomNouveauType = mysql_escape_string( $tabForm['nomNouveauType']['value'] );
					$sql = "SELECT idTypeSource FROM typeSource WHERE nom='".$nomNouveauType."' LIMIT 1";
					$rep = $this->connexionBdd->requete($sql);
					if ($res = mysql_fetch_object($rep))
					{
						// il existe
						$idTypeSource = $res->idTypeSource;
					}
					else
					{
						// il n'existe pas
						$sql = "INSERT INTO typeSource (nom) VALUES ('".$nomNouveauType."')";
						$this->connexionBdd->requete($sql);
						$idTypeSource = mysql_insert_id();
					}
				}
				// sinon, si le type est bien renseigné
				else if ($formulaire->estChiffre($tabForm['type']['value']))
				{
					$idTypeSource = $tabForm['type']['value'];
				}
				// sinon, rien ne vas, on met une valeur par défaut
				else
				{
					$this->erreurs->ajouter(_("Ajout de source : type de source incorrect ! Utilisateur du choix par défaut = 1"));
					echo _("Erreur ajout source");
					$idTypeSource = 1;
				}
				

				$sql ="INSERT INTO source (nom, idTypeSource, description) VALUES 
					('".$nom."',".$idTypeSource.",'".$description."')";

				$this->connexionBdd->requete($sql);
				$newIdSource = mysql_insert_id();
				
				
				$mail = new mailObject();
				$message = _("Une nouvelle source a été ajoutée :")." <br>";
				$message .= $nom." : ".$description."<br><br>";
				$message.="<a href='".$this->creerUrl('','administrationAfficheModification',array('tableName'=>'source','idModification'=>$newIdSource))."'>".$this->creerUrl('','administrationAfficheModification',array('tableName'=>'source','idModification'=>$newIdSource))."</a>";
				$mail->sendMailToAdministrators($mail->getSiteMail(),"archi-strasbourg.org : "._("un utilisateur ajouté une source"),$message," and alerteMail='1' ",true);
				$u = new archiUtilisateur();				
				$u->ajouteMailEnvoiRegroupesAdministrateurs(array('contenu'=>$message,'idTypeMailRegroupement'=>9,'criteres'=>" and alerteMail='1' "));
				
			}
			else
			{
				$this->erreurs->ajouter($tabForm);
			}
		}
		
		return $newIdSource;
	}
	
	public function afficherFormulaire()
	{
		$html = '';
		
		$u = new archiUtilisateur();
		$a = new archiAuthentification();
		
		
		
		$t = new Template('modules/archi/templates');
		$t->set_filenames(array('formulaire'=>'sourceFormulaire.tpl'));
	
		$modeAffichage='';
		if(isset($this->variablesGet["modeAffichage"]) && $this->variablesGet["modeAffichage"]!='')
			$modeAffichage = $this->variablesGet["modeAffichage"];
		
		
		switch($modeAffichage)
		{
			case 'nouveauDossier':
			case 'modifEvenement':
			
			break;
			default:
			
				if($a->estConnecte())
				{	
					if($a->estAdmin())
					{
						$t->assign_block_vars('allowNewType',array());
					}
				}
			break;
		}
		
		// action du formulaire
		$t->assign_vars(array(
						'urlAction'=>$this->creerUrl('ajouterSource','',array('noHeaderNoFooter'=>1,'modeAffichage'=>$modeAffichage)),
						'boutonAnnulation' => "location.href='".$this->creerUrl('','sourceListe',array('noHeaderNoFooter'=>1,'modeAffichage'=>$modeAffichage))."';"
						));
		
		// récupération des métiers
		$sql = "SELECT nom, idTypeSource FROM typeSource";
		$rep = $this->connexionBdd->requete($sql);
		while ($res = mysql_fetch_object($rep))
		{
			$t->assign_block_vars('type', array('valeur'=>$res->idTypeSource, 'nom'=>$res->nom));
		}

		if($this->erreurs->tabFormExiste())
		{
			foreach ($this->erreurs->getErreursFromFormulaire() AS $name => $value)
			{
				$val = htmlspecialchars(stripslashes($value["value"]));

				$t->assign_vars(array( $name => $val, $name."-error" => $value["error"]));
			}
		}
		
		ob_start();
		$t->pparse('formulaire');
		$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}

	public function modifier() {
	}

	public function supprimer() {
	}

	public function afficher($id) {
		$html = '';
		$formulaire = new formGenerator();
		$bbCode = new bbCodeObject();
		
		if ($formulaire->estChiffre($id) == 1)
		{
			$sqlId = $id;
		}
		else
		{
			echo _("Erreur, l'id n'est pas valide ! Id automatiquement défini");
			$sqlId = '(SELECT MAX(idSource) FROM source)';
		}
		$sql = "SELECT s.idSource, s.nom AS nomSource, s.idTypeSource, s.description, tS.nom AS nomTypeSource
			FROM source s 
			LEFT JOIN typeSource tS USING (idTypeSource) 
			WHERE idSource = ".$sqlId." LIMIT 1";
		
		if ($rep = $this->connexionBdd->requete($sql))
		{
			$res = mysql_fetch_object($rep);
			$t = new Template('modules/archi/templates/');
			$t->set_filenames((array('ev'=>'source.tpl')));
			$e = new archiEvenement();
			$retourEvenement = $e->afficherListe(array('selection' => 'source', 'id' => $res->idSource));
			$t->assign_vars(array(
				'nom'     => stripslashes($res->nomSource),
				'nomType' => $res->nomTypeSource,
				'description'   => stripslashes($res->description),
				'urlAjout'      => $this->creerUrl('ajouterSource'),
				'evenementLies' => $retourEvenement['html']
				));
			
			ob_start();
			$t->pparse('ev');
			$html .= ob_get_contents();
			ob_end_clean();
		}
		else
		{
			echo _("Erreur, aucun résultat");
		}

		return $html;
	}

	
	
	public function afficherListeAlphabetique($affichage='',$lettreCourante='a', $tableauLettres = array(),$params = array())
	{
		$html="";
		$t = new Template('modules/archi/templates/');
		$t->set_filenames((array('listeAlpha'=>'listeAlphabetique.tpl')));

		//$liste = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$liste = $tableauLettres;
		
		
		
		$autresCriteresUrl = "";
		if(isset($params['paramsUrl']) && $params['paramsUrl']!='')
		{
			$autresCriteresUrl = $params['paramsUrl'];
		}
		
		
		
		foreach($liste as $indice => $value)
		{
            if ($value!="\\") {
                $t->assign_block_vars('lettres',array('lettre'=>$value,'url'=>'#','onclick'=>"document.getElementById('formSource').action+='&alphaSource=".$value."$autresCriteresUrl';document.getElementById('formSource').submit();"));
                //$this->creerUrl('','',array_merge($this->variablesGet,array('alphaSource'=>$value,'archiPageSource'=>'1','archiAffichage'=>$affichage))),'lettre'=>$value));
            }
		}

		ob_start();
		$t->pparse('listeAlpha');
		$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	
	public function afficherListe($criteres = array(),$modeAffichage='')
	{		
		$html='';

		$t = new Template('modules/archi/templates/');
		$t->set_filenames((array('listeSource'=>'listeSources.tpl')));
		
		if(isset($this->variablesGet['modeAffichage']) && $this->variablesGet['modeAffichage']!='')
			$modeAffichage = $this->variablesGet['modeAffichage'];

		$u = new archiUtilisateur();
		$a = new archiAuthentification();
		if($a->estConnecte())
		{
			if($u->isAuthorized('ajout_source',$a->getIdUtilisateur()))
			{
				$t->assign_block_vars('isAuthorizedAjoutSource',array());
			}
		}
		
		
		// critere recherche par mot cle
		$sqlMotCle = "";
		$arrayMotCle = array();
		$urlMotCle = "";
		$isAffichageResultatRechercheMotCle = false;
		if(isset($this->variablesGet['motCle']) && $this->variablesGet['motCle']!='')
		{
			$sqlMotCle = " AND LOWER(CONCAT(s.nom)) LIKE \"%".$this->variablesGet['motCle']."%\" ";
			
			$arrayMotCle = array('motCle'=>$this->variablesGet['motCle']);
			$urlMotCle = "&motCle=".$this->variablesGet['motCle'];
			$t->assign_vars(array('motCleRechercheSource'=>$this->variablesGet['motCle']));
			
			$isAffichageResultatRechercheMotCle = true;
		}
		
		
		
		switch($modeAffichage)
		{
			case 'nouveauDossier':
			case 'modifEvenement':
			case 'modifImage':
			case 'ajoutModifParcoursAdmin':
				$t->assign_vars(array('urlAjout'      => $this->creerUrl('','afficherAjouterSource',array('noHeaderNoFooter'=>1,'modeAffichage'=>$modeAffichage))));
				$t->assign_vars(array('formAction'	  => $this->creerUrl('','sourceListe',array('noHeaderNoFooter'=>1,'modeAffichage'=>$modeAffichage))));
				// si c'est un affichage apres un ajout , on renvoi le nouvel element
				if(isset($criteres['newIdSourceAdded']) && $criteres['newIdSourceAdded']!='0')
				{
					$t->assign_vars(array('codeJavascriptReturnNewElementAjoute'=>"
						parent.document.getElementById(parent.document.getElementById('paramChampsAppelantSource').value).value='".$criteres['newIdSourceAdded']."';
						parent.document.getElementById(parent.document.getElementById('paramChampsAppelantSource').value+'txt').value='".str_replace("'","\'",stripslashes($this->getSourceLibelle($criteres['newIdSourceAdded'])))."';
						parent.document.getElementById('calqueSource').style.display='none';
					"));
				}

			break;
			default:
				$t->assign_vars(array('urlAjout'      => $this->creerUrl('','ajouterSource')));
				$t->assign_vars(array('formAction'	  => $this->creerUrl('','sourceListe')));
			break;
		
		}
		

		// analyse des criteres
		
		$sqlTypeSource ="";
		$arrayUrlTypeSource = array();
		if(isset($criteres['archiTypeSource']))
		{
			$sqlTypeSource = " AND ts.idTypeSource = '".$criteres['archiTypeSource']."' ";
			$arrayUrlTypeSource = array('archiTypeSource'=>$criteres['archiTypeSource']);
		}

		


		
		
		
		// récupération du nombre de résultats par lettres
		$tabLettres = array();
		$sqlLettreCourante = '';
		if($isAffichageResultatRechercheMotCle)
		{
			// en mode d'affichage de resultat de recherche , on affiche pas la liste alphabetique
			// donc on ne groupe pas par lettre dans la requete
			$sqlComptageResultat = "SELECT LOWER(SUBSTRING(REPLACE(s.nom,\"\\\"\",\"\"), 1,1)) AS lettre FROM source s 
				LEFT JOIN typeSource ts USING (idTypeSource)
				WHERE 1 ".$sqlTypeSource." ".$sqlMotCle." ";
		}
		else
		{
			$sqlComptageResultat = "SELECT LOWER(SUBSTRING(REPLACE(s.nom,\"\\\"\",\"\"), 1,1)) AS lettre FROM source s 
				LEFT JOIN typeSource ts USING (idTypeSource)
				WHERE 1 ".$sqlTypeSource." ".$sqlMotCle." GROUP BY LOWER(SUBSTRING(REPLACE(s.nom,\"\\\"\",\"\"), 1,1))";

			$rep = $this->connexionBdd->requete($sqlComptageResultat);
			while ($res = mysql_fetch_object($rep))
				$tabLettres[] = $res->lettre;

			// si aucune lettre n'est précisée, on indique la première lettre ayant des résultats
			// si le tableau de lettres est défini
			// et que la première lettre n'existe pas ou que la première lettre n'existe pas dans le tableau
			if ( count($tabLettres) > 0 AND  (!isset($criteres['alphaSource']) OR (!in_array($criteres['alphaSource'], $tabLettres))))
				$criteres['alphaSource'] = $tabLettres[0];
			
			
			if(isset($criteres['alphaSource']))
			{
				$sqlLettreCourante = " AND LOWER(SUBSTRING(REPLACE(s.nom,\"\\\"\",\"\"),1,1)) = '".$criteres['alphaSource']."' ";
			}
		}
		$nbEnregistrementsParPage='3';
		if(isset($criteres['nbEnregistrements']))
		{
			$nbEnregistrementsParPage=$criteres['nbEnregistrements'];
		}
		
		$debutEnregistrement = '0';
		if(isset($criteres['archiPageSource']))
		{
			$debutEnregistrement = ($criteres['archiPageSource']-1)*$nbEnregistrementsParPage;
		}
		
		
		// affichage de la liste alphabetique
		$lettre='';
		if(isset($tabLettre[0]))
			$lettre = $tabLettre[0];

		// on affiche les lettres si on est pas en recherche
		$t->assign_vars(array('listeAlphabetique'=>$this->afficherListeAlphabetique('sourceListe', $lettre, $tabLettres,array('paramsUrl'=>$urlMotCle)))); // 'sourceListe' correspond au cas d'affichage de index.php
		
		// recuperation des types de sources
		$reqTypesSources="select idTypeSource, nom from typeSource";
		$resTypesSources=$this->connexionBdd->requete($reqTypesSources);
		$selected='';
		while($fetchTypesSources = mysql_fetch_array($resTypesSources))
		{
			$selected='';
			if(isset($criteres['archiTypeSource']) && $criteres['archiTypeSource']==$fetchTypesSources['idTypeSource'])
			{
				$selected = 'selected';
			}
			$t->assign_block_vars('typeSources',array('id'=>$fetchTypesSources['idTypeSource'],'nom'=>$fetchTypesSources['nom'],'selected'=>$selected));		
		}
		
		
		// recuperation du nombre de personne de la lettre courante , pour les numeros de pages
		$reqSources = "
			SELECT s.idSource as idSource, s.nom as nom, s.idTypeSource as idTypeSource,ts.nom as nomTypeSource, s.description as description
			FROM source s
			LEFT JOIN typeSource ts ON ts.idTypeSource = s.idTypeSource
			WHERE 1=1
			".$sqlLettreCourante."
			".$sqlTypeSource."
			$sqlMotCle
			ORDER BY nom
		";
		
		
		
		$resSources=$this->connexionBdd->requete($reqSources);
		$nbEnregistrements = mysql_num_rows($resSources);
		
		// recuperation du nombre de pages a afficher
		if($nbEnregistrements > $nbEnregistrementsParPage)
		{
			if($nbEnregistrements%$nbEnregistrementsParPage!=0)
			{
				// on prend la partie entiere de la division + 1
				$nbPages = intval($nbEnregistrements/$nbEnregistrementsParPage)+1;
			}
			else
			{
				$nbPages = $nbEnregistrements/$nbEnregistrementsParPage;
			}

			for($i=1 ; $i<=$nbPages; $i++)
			{
				switch($modeAffichage)
				{
					case "nouveauDossier":
						$t->assign_block_vars('pages',array(
										'page'=>$i,
										'url'=>$this->creerUrl('','',array_merge($arrayUrlTypeSource,$this->variablesGet,array('archiPageSource'=>$i,'archiAffichage'=>'sourceListe'))),
										'onclick'=>""
										)
									);//afficheRechercheSourcePopup
					break;
					case 'modifEvenement':
					case 'modifImage':
					case 'ajoutModifParcoursAdmin':
					default:
						$t->assign_block_vars('pages',array(
										'page'=>$i,
										'url'=>$this->creerUrl('','',array_merge($arrayUrlTypeSource,$this->variablesGet,array('archiPageSource'=>$i,'archiAffichage'=>'sourceListe'))),
										'onclick'=>""
										)
									);
									
						if($i==1 && $nbPages>1)
						{
							$t->assign_vars(array("pageSuivante"=>$this->creerUrl('','',array_merge($arrayUrlTypeSource,$this->variablesGet,array('archiPageSource'=>($i+1),'archiAffichage'=>'sourceListe')))));
						}
						
						if($i>1 && $i<=$nbPages)
						{
							$t->assign_vars(array("pagePrecedente"=>$this->creerUrl('','',array_merge($arrayUrlTypeSource,$this->variablesGet,array('archiPageSource'=>($i-1),'archiAffichage'=>'sourceListe')))));
						}
						
					break;
				}
			}
		}
		else
		{
			$nbPages=1;
			$t->assign_block_vars('nopage',array());
		}
		
		
		if($nbEnregistrements>0)
		{
			mysql_data_seek($resSources, $debutEnregistrement);
			$fetchSource=mysql_fetch_assoc($resSources);
			$i=0;
			while($i<$nbEnregistrementsParPage && isset($fetchSource['nom']))
			{
				
				
				switch($modeAffichage)
				{
					case 'popup':
						$t->assign_block_vars('sources',array(
												'nom'=>$fetchSource['nom'],
												'typeSource'=>$fetchSource['nomTypeSource'],
												'url'=>'#',
												'onclick'=>"parent.document.getElementById('idSource').value='".$fetchSource['idSource']."';parent.document.getElementById('nomSource').value='".addslashes($fetchSource['nom'])." ".$fetchSource['nomTypeSource']."';parent.document.getElementById('calqueSource').style.display='none';"
										));
					break;
					case 'ajoutEvenement':
					case 'nouveauDossier':
					case 'modifEvenement':
					case 'modifImage':
					case 'ajoutModifParcoursAdmin':
						$t->assign_block_vars('sources',array(
												'nom'=>stripslashes($fetchSource['nom']),
												'typeSource'=>"(".stripslashes($fetchSource['nomTypeSource']).")",
												'url'=>'#',
												'onclick'=>"parent.document.getElementById(parent.document.getElementById('paramChampsAppelantSource').value).value='".$fetchSource['idSource']."';parent.document.getElementById(parent.document.getElementById('paramChampsAppelantSource').value+'txt').value='".str_replace(array("\"","'"),array("&quot;","\'"),stripslashes($fetchSource['nom']))." (".$fetchSource['nomTypeSource'].")';parent.document.getElementById('calqueSource').style.display='none';"
										));
					break;
					default:
						$t->assign_block_vars('sources',array(
												'nom'=>$fetchSource['nom'],
												'url'=>$this->creerUrl('', 'source', array('idSource'=>$fetchSource['idSource'])),
												'typeSource'=>$fetchSource['nomTypeSource'],
												));
					break;
				}
				
				$i++;
				$fetchSource=mysql_fetch_assoc($resSources);
			}
		}
		else
		{
			$t->assign_block_vars('noSource',array());
		}
		
		
		ob_start();
		$t->pparse('listeSource');
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}

	// ****************************************************************************************************************************************
	// fonction qui renvoi le libelle d'une source
	// ****************************************************************************************************************************************
	public function getSourceLibelle($idSource=0)
	{
		$req="select nom from source where idSource = '".$idSource."'";
		$res = $this->connexionBdd->requete($req);
		$fetch = mysql_fetch_assoc($res);
		return $fetch['nom'];
	}
	
	// ****************************************************************************************************************************************
	// affichage de la popup pour la description de la source sur le detail d'une adresse
	// ****************************************************************************************************************************************
	public function getPopupDescriptionSource()
	{
		$html="";
		$t = new Template('modules/archi/templates/');
		$t->set_filenames((array('calqueDescriptionSource'=>'popupDescriptionSource.tpl')));
	
		//$t->assign_vars(array('iframeSrc'=>$this->creerUrl('','descriptionSource',array('archiIdSource'=>$idSource,'noHeaderNoFooter'=>1))));
	
		ob_start();
		$t->pparse('calqueDescriptionSource');
		$html .= ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	// ****************************************************************************************************************************************
	// affichage de la description de la popup dans la popup du detail d'une adresse
	// ****************************************************************************************************************************************
	public function afficheDescriptionSource($idSource=0)
	{
		$html="";
		
		$req = "select nom,description from source where idSource ='".$idSource."'";
		$res = $this->connexionBdd->requete($req);
		
		if(mysql_num_rows($res)>0)
		{
			$bbCode = new bbCodeObject();
			
			while($fetch=mysql_fetch_assoc($res))
			{
				$html.="<h2>".stripslashes($fetch['nom'])."</h2><br>";
				if(trim($fetch['description'])!='')
				{
					$html.=$bbCode->convertToDisplay(array('text'=>(stripslashes($fetch['description']))));
				}
				else
				{
					$html.=_("Il n'y a pas de description pour cette source.");
				}
			}
		}
		return $html;
	}
	
	// renvoi les enregistrements de source selon les criteres
	public function getMysqlResSource($params = array())
	{
		$retour = null;
		
		$sqlFields = "*";
		if(isset($params['sqlFields']) && $params['sqlFields']!='')
		{
			$sqlFields = $params['sqlFields'];
		}
		
		$sqlWhere = "";
		if(isset($params['sqlWhere']) && $params['sqlWhere']!='')
		{
			$sqlWhere = $params['sqlWhere'];
		}
		
		$req = "
			SELECT $sqlFields
			FROM source s
			LEFT JOIN typeSource ts ON ts.idTypeSource = s.idTypeSource
			WHERE 1=1
			$sqlWhere
			";
		
		$res = $this->connexionBdd->requete($req);
		
		return $res;
	}
	
	public function afficherListeSourcesAvecLogos($params = array())
	{
		$s = new stringObject();
		$bbCode = new bbCodeObject();
		
		$html = "<h1>"._("Nos sources")."</h1><br>";
		$html.="<br>"._("L'une des difficultés d'un site collaboratif, c'est la crédibilité que l'on peut apporter aux informations renseignées par chaque internaute. C'est pourquoi, par soucis de transparence, nous vous livrons ici de façon exhaustive l'ensemble des sources qui sont consultées pour enrichir le site :")."<br><br>";
		$reqCount = "SELECT 0 FROM source";
		$resCount = $this->connexionBdd->requete($reqCount);
		
		$nbSourcesTotal = mysql_num_rows($resCount);
		$nbEnregistrementsParPage = 15;
		
		
		$pagination = new paginationObject();
		$arrayPagination=$pagination->pagination(array(
										'nomParamPageCourante'=>'archiPageNosSources',
										'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
										'nbEnregistrementsTotaux'=>$nbSourcesTotal,
										'typeLiens'=>'noformulaire'
		));
		
		
		
		$reqArchivesMunicipalesSansIdSourceEvenement = "
				SELECT distinct he.idEvenement FROM historiqueEvenement he,historiqueEvenement he2 WHERE he2.idEvenement=he.idEvenement AND he.numeroArchive<>'' AND (he.idSource = '0' OR he.idSource = '')
				GROUP BY he.idEvenement, he.idHistoriqueEvenement
				HAVING he.idHistoriqueEvenement = max(he2.idHistoriqueEvenement)
				";
		
		$resArchivesMunicipalesSansIdSourceEvenement = $this->connexionBdd->requete($reqArchivesMunicipalesSansIdSourceEvenement);
		$nbEvenementsSansIdSourceANDArchivesMunicipales = mysql_num_rows($resArchivesMunicipalesSansIdSourceEvenement);
		
		$reqArchivesMunicipalesSansIdSourceImage = "
				SELECT distinct hi.idImage FROM historiqueImage hi, historiqueImage hi2 WHERE hi2.idImage = hi.idImage AND hi.numeroArchive<>'' AND (hi.idSource='0' OR hi.idSource='')
				GROUP BY hi.idImage , hi.idHistoriqueImage
				HAVING hi.idHistoriqueImage = max(hi2.idHistoriqueImage)
				";
		$resArchivesMunicipalesSansIdSourceImage = $this->connexionBdd->requete($reqArchivesMunicipalesSansIdSourceImage);
		$nbImagesSansIdSourceANDArchivesMunicipales = mysql_num_rows($resArchivesMunicipalesSansIdSourceImage);
		
		
		
		$nbArchivesMunicipalesSansIdSource = $nbEvenementsSansIdSourceANDArchivesMunicipales+$nbImagesSansIdSourceANDArchivesMunicipales;
		
		$req = "
			SELECT idSource , 
			(
				(SELECT count(distinct he.idEvenement)  FROM historiqueEvenement he WHERE he.idSource = s.idSource )
			+ 
				(SELECT count(distinct hi.idImage) FROM historiqueImage hi WHERE hi.idSource = s.idSource)
			+ IF(idSource=24,$nbArchivesMunicipalesSansIdSource,0)
			) 
			as sumnb
			FROM source s
			ORDER BY sumnb DESC
		";

		
		
		$req = $pagination->addLimitToQuery($req);
		
		$res = $this->connexionBdd->requete($req);
		
		$t = new tableau();
		$t->addValue("");
		$t->addValue("<b>"._("Sources")."</b>");
		$t->addValue("<b><span style='font-size:12px;'>"._("Nombre de liaisons (articles ou photos)")."</span></b>");
		while($fetch = mysql_fetch_assoc($res))
		{
			$reqSource = "
					SELECT s.idSource as idSource, s.nom as nomSource , ts.nom as nomTypeSource, s.description as description
					FROM source s
					LEFT JOIN typeSource ts ON ts.idTypeSource = s.idTypeSource
					WHERE idSource = '".$fetch['idSource']."'
					";
			$resSource = $this->connexionBdd->requete($reqSource);
			
			$fetchSource = mysql_fetch_assoc($resSource);
			
			
			$image = "&nbsp;";
			$url = $this->creerUrl('','listeAdressesFromSource', array('source'=>$fetch['idSource'], 'submit'=>'Rechercher'));
			if(file_exists($this->getCheminPhysique()."images/logosSources/".$fetch['idSource'].".jpg"))
			{
				$image = "<a href='".$url."'><img src='".$this->getUrlImage()."logosSources/".$fetch['idSource'].".jpg' border=0></a>";
			}
			
			$nomTypeSource = "";
			if(isset($fetchSource['nomTypeSource']) && $fetchSource['nomTypeSource']!='')
			{
				$nomTypeSource = " (".$fetchSource['nomTypeSource'].")";
			}
			
			$description = $s->coupureTexte(strip_tags($bbCode->convertToDisplay(array('text'=>stripslashes($fetchSource['description'])))),10);
			
			$t->addValue($image,"style='width:200px;'");
			$t->addValue("<a href='".$url."'>".stripslashes($fetchSource['nomSource']).$nomTypeSource."</a><br>".$description);
			$t->addValue($fetch['sumnb']);
		}
		
		$html.=$arrayPagination['html'];
		$html.=$t->createHtmlTableFromArray(3);
        $html.=$arrayPagination['html'];
		
		return $html;
	}
	
	public function afficheImageSourceOriginal($params = array())
	{
		$html = "";
		if(isset($this->variablesGet['archiIdSource']) && $this->variablesGet['archiIdSource']!='')
		{
			$idSource = $this->variablesGet['archiIdSource'];
			
			$req = "SELECT nom FROM source WHERE idSource = '".$idSource."'";
			$res = $this->connexionBdd->requete($req);
			$fetch = mysql_fetch_assoc($res);
			
			$html.="<a href='".$this->creerUrl('','listeAdressesFromSource',array('source'=>$idSource,'submit'=>'Rechercher'))."'>&lt;Retour</a>";
			$html.="<h1>Source : ".$fetch['nom']."</h1>";
			
			if(file_exists($this->cheminPhysique."images/logosSources/".$idSource."_original.jpg"))
			{
				$html.="<img src='".$this->urlImages."logosSources/".$idSource."_original.jpg' border=0>";
			}
			
		}
	
		return $html;
	}
	
	public function listeSourcesDependantsDeTypeSource($params = array())
	{
		$html = "";
		
		if(isset($this->variablesGet['idTypeSource']) && $this->variablesGet['idTypeSource']!='')
		{
			$req = "SELECT idSource, nom FROM source WHERE idTypeSource = '".$this->variablesGet['idTypeSource']."'";
			$res = $this->connexionBdd->requete($req);
			
			while($fetch = mysql_fetch_assoc($res))
			{
				$html.="<a href='#' onclick=\"parent.document.location.href='".$this->creerUrl('','administrationAfficheModification',array('tableName'=>'source','idModification'=>$fetch['idSource']))."';\">".stripslashes($fetch['nom'])."</a><br>";
			}
		}
		
		return $html;
	}
}
?>
