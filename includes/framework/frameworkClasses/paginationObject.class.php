<?php


// classe de gestion de pagination
// Dorer Laurent 2008

// historique des versions
// version 1.0 --- 

// 27-10-2008 : version 1.1 -- ajout du type de lien freeLink , pour pouvoir faire des liens libres avec en option le numero de page ##numPage##

/*
UTILISATION 

				$pagination = new paginationObject();
				$arrayPagination=$pagination->pagination(array(
										'nomParamPageCourante'=>'archiPageCouranteVille',
										'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
										'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
										'typeLiens'=>'noformulaire'
				));
				
			cette fonction retourne :
			return array('html'=>$html,'limitSqlDebut'=>$limitSqlDebut);
			
			
			a rajouter dans la requete a paginer
			LIMIT ".$limitSqlDebut.",".$nbEnregistrementsParPage."

*/




class paginationObject extends config
{
	private $limitSqlDebut;
	private $nbEnregistrementsParPage;
	
	function __construct($connexion='')
	{
		parent::__construct();	//
		
		$this->limitSqlDebut=0;
		$this->nbEnregistrementsParPage=0;
	}

	// ****************************************************************************************************************************************
	// fonction permettant d'afficher la pagination en fonction de la requete courante et de la page courante
	// exemple avec gestion de formulaire :
	/*	$pagination = new paginationObject();
		
		$nbEnregistrementsParPage = 15;
		$arrayPagination=$pagination->pagination(array(
										'nbEnregistrementsParPage'=>$nbEnregistrementsParPage,
										'nbEnregistrementsTotaux'=>$nbEnregistrementTotaux,
										'typeLiens'=>'formulaire',
										'champPageCourante'=>'pageCourante',
										'nomParamPageCourante'=>'pageCourante',
										'idFormulaire'=>'formRechercheLogsMail'
										));
		
		
		REMARQUE : il faut ajouter le champ caché "pageCourante" dans le formulaire !!
	
	*/
	// ****************************************************************************************************************************************
	public function pagination($parametres=array())
	{
		$html = '';
		
		$t=new Template($this->cheminTemplates);
		$t->set_filenames((array('pagination'=>'pagination.tpl')));
		
		$nbEnregistrementsTotaux = 0;
		if(isset($parametres['nbEnregistrementsTotaux']))
			$nbEnregistrementsTotaux = $parametres['nbEnregistrementsTotaux'];
		
		
		$typeLiens="default";
		if(isset($parametres['typeLiens']))
			$typeLiens = $parametres['typeLiens'];
	
		
		$nomParamPageCourante = '';
		if(isset($parametres['nomParamPageCourante']))
			$nomParamPageCourante = $parametres['nomParamPageCourante'];
		
		
		$pageCourante=1;
		if(isset($this->variablesGet[$nomParamPageCourante]) && $this->variablesGet[$nomParamPageCourante]!='')
			$pageCourante = $this->variablesGet[$nomParamPageCourante];
		elseif(isset($this->variablesPost[$nomParamPageCourante]) && $this->variablesPost[$nomParamPageCourante]!='')
			$pageCourante = $this->variablesPost[$nomParamPageCourante];
		
		// le parametre setPageCouranteTo , permet d'assigner le numero de la page courante , inutile pour une utilisation normale de la pagination
		if(isset($parametres['setPageCouranteTo']))
		{
			$pageCourante = $parametres['setPageCouranteTo'];
		}
		
		// *************************************************************************************************************************
		// si une requete concernant la liste alphabetiques est precisée , on va afficher la liste alphabetique en fonction
		// dans la requete il faut avoir précisé le champ de la liste des lettres disponible : "paginationAlphabetique"
		$tabLettresAlpha = array();
		
		if(isset($parametres['arrayListeAlphabetique']) && $parametres['arrayListeAlphabetique']!='')
		{
			$nomParametreLettreCourante = "lettreCourante";
			if(isset($parametres['nomParametreLettreCourante']))
				$nomParametreLettreCourante = $parametres['nomParametreLettreCourante'];
				
		
			if(count($parametres['arrayListeAlphabetique'])>0)
			{
				$tabLettresAlpha = array_unique($parametres['arrayListeAlphabetique']);
				sort($tabLettresAlpha);
				$listeLiensAlphabetiques = "";
				
				$GET = $this->variablesGet;
				if(isset($GET['archiPageCouranteVille']))
					unset($GET['archiPageCouranteVille']); // pour que l'on ne transmette pas le parametre de la page courante quand on clique sur une lettre
				
				
				foreach($tabLettresAlpha as $indice => $lettre)
				{
					$listeLiensAlphabetiques.=" <a href='".$this->creerUrl('','',array_merge($GET,array($nomParametreLettreCourante=>$lettre)))."'>$lettre</a>&nbsp;";
				}
				
				
				$t->assign_vars(array('listeAlphabetique'=>$listeLiensAlphabetiques."<br><br>"));
			}
		}
		// *************************************************************************************************************************
		
		$nbEnregistrementsParPage = 0;
		if(isset($parametres['nbEnregistrementsParPage']))
		{
		
			$nbEnregistrementsParPage = $parametres['nbEnregistrementsParPage'];
			$premierePage =1;
			// calcul du nombre de pages 
			if(($nbEnregistrementsTotaux/$nbEnregistrementsParPage)>1)
			{
				if($nbEnregistrementsTotaux%$nbEnregistrementsParPage==0)
				{
					$nbPages = $nbEnregistrementsTotaux/$nbEnregistrementsParPage;
				}
				else
				{
					$nbPages = intval($nbEnregistrementsTotaux/$nbEnregistrementsParPage)+1;
				}
			}
			else
			{
				// il n'y a qu'une page
				$nbPages = 1;
			}
			
			$nbPagesAffichees=$nbPages;
			if($nbPages > 20)
			{
				if($pageCourante > 10)
				{
					$premierePage = $pageCourante-5;
					$t->assign_vars(array('pointillesPrecedents'=>'...'));
				}
				if($pageCourante < $nbPages-10)
				{
					$nbPagesAffichees = $pageCourante+5;
					$t->assign_vars(array('pointillesSuivants'=>'...'));
				}
			}

			for($i=$premierePage ; $i<=$nbPagesAffichees ; $i++)
			{
				$t->assign_block_vars('pages',array('numero'=>$i,'stylePageCourante'=>'font-weight:bold;'));
				if($i!=$pageCourante)
				{
					switch($typeLiens)
					{
						case "formulaire":
							// dans ce cas , on valide le formulaire dont l'ID est passé en parametres et on passe l'id dans un champs a cet effet     //$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>$i))),
							$gestionChampAction="";
							if(isset($parametres['nomChampActionFormulaireOnSubmit']) && isset($parametres['nomActionFormulaireOnSubmit']))
							{
								// permet de gerer la mise a jour d'un champ avant la validation du formulaire
								$gestionChampAction = "document.getElementById('".$parametres['nomChampActionFormulaireOnSubmit']."').value='".$parametres['nomActionFormulaireOnSubmit']."';";
							}
							
							$t->assign_block_vars('pages.isNotPageCourante',array(
								'url'=>"#",
								'onClick'=>"document.getElementById('".$parametres['champPageCourante']."').value='".$i."';".$gestionChampAction."document.getElementById('".$parametres['idFormulaire']."').submit();"
							));
							
							
								$t->assign_vars(array(
								'urlPremier'=>'#',
								'onClickPremier'=>"document.getElementById('".$parametres['champPageCourante']."').value='1';".$gestionChampAction."document.getElementById('".$parametres['idFormulaire']."').submit();",
								'urlDernier'=>'#',
								'onClickDernier'=>"document.getElementById('".$parametres['champPageCourante']."').value='".$nbPages."';".$gestionChampAction."document.getElementById('".$parametres['idFormulaire']."').submit();",
								));
							
							if($pageCourante>1)
							{
								$t->assign_vars(array(
										'urlPrecedent'=>'#',
										'onClickPrecedent'=>"document.getElementById('".$parametres['champPageCourante']."').value='".($pageCourante-1)."';".$gestionChampAction."document.getElementById('".$parametres['idFormulaire']."').submit();"));
							}
							else
							{
								$t->assign_vars(array(
										'urlPrecedent'=>'#',
										'onClickPrecedent'=>''));
							}
							
							
							if($pageCourante<$nbPagesAffichees)
							{
								$t->assign_vars(array(
										'urlSuivant'=>'#',
										'onClickSuivant'=>"document.getElementById('".$parametres['champPageCourante']."').value='".($pageCourante+1)."';".$gestionChampAction."document.getElementById('".$parametres['idFormulaire']."').submit();"));
							}
							else
							{
								$t->assign_vars(array(
										'urlSuivant'=>'#',
										'onClickSuivant'=>''));
							}
							
							
						break;		
						
						
						case "noformulaire":
							// on ne valide pas de formulaire , on fabrique simplement l'url
							$t->assign_block_vars('pages.isNotPageCourante',array(
														'url'=>$this->creerUrl('','',array_merge($this->variablesGet,array($nomParamPageCourante=>$i))),
														'onClick'=>""							
							));
							
							
							$t->assign_vars(array(
								'urlPremier'=>$this->creerUrl('','',array_merge($this->variablesGet,array($nomParamPageCourante=>'1'))),
								'onClickPremier'=>'',
								'urlDernier'=>$this->creerUrl('','',array_merge($this->variablesGet,array($nomParamPageCourante=>$nbPages))),
								'onClickDernier'=>"",
								));
							
							if($pageCourante>1)
							{
								$t->assign_vars(array(
										'urlPrecedent'=>$this->creerUrl('','',array_merge($this->variablesGet,array($nomParamPageCourante=>($pageCourante-1)))),
										'onClickPrecedent'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlPrecedent'=>'#',
										'onClickPrecedent'=>''));
							}
							
							
							if($pageCourante<$nbPagesAffichees)
							{
								$t->assign_vars(array(
										'urlSuivant'=>$this->creerUrl('','',array_merge($this->variablesGet,array($nomParamPageCourante=>($pageCourante+1)))),
										'onClickSuivant'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlSuivant'=>'#',
										'onClickSuivant'=>''));
							}
						
						break;
						case "freeLink":
							$t->assign_block_vars('pages.isNotPageCourante',array(
														'url'=>str_replace('##numPage##',$i,$parametres['urlFreeLink']),
														'onClick'=>""
							));
							
							
							
							$t->assign_vars(array(
								'urlPremier'=>str_replace('##numPage##','1',$parametres['urlFreeLink']),
								'onClickPremier'=>'',
								'urlDernier'=>str_replace('##numPage##',$nbPages,$parametres['urlFreeLink']),
								'onClickDernier'=>"",
								));
							
							if($pageCourante>1)
							{
								$t->assign_vars(array(
										'urlPrecedent'=>str_replace('##numPage##',($pageCourante-1),$parametres['urlFreeLink']),
										'onClickPrecedent'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlPrecedent'=>'#',
										'onClickPrecedent'=>''));
							}
							
							
							if($pageCourante<$nbPagesAffichees)
							{
								$t->assign_vars(array(
										'urlSuivant'=>str_replace('##numPage##',($pageCourante+1),$parametres['urlFreeLink']),
										'onClickSuivant'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlSuivant'=>'#',
										'onClickSuivant'=>''));
							}
							
							
							
							
							
						break;
						default:
							// dans ce cas le click sur un numero de page renvoi simplement vers la meme page avec le numero de page clicqué en parametres
							$t->assign_block_vars('pages.isNotPageCourante',array(
								'url'=>$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>$i))),
								'onClick'=>''
							));
							
							$t->assign_vars(array(
								'urlPremier'=>$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>'1'))),
								'onClickPremier'=>'',
								'urlDernier'=>$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>$nbPages))),
								'onClickDernier'=>"",
								));
							
							if($pageCourante>1)
							{
								$t->assign_vars(array(
										'urlPrecedent'=>$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>($pageCourante-1)))),
										'onClickPrecedent'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlPrecedent'=>'#',
										'onClickPrecedent'=>''));
							}
							
							
							if($pageCourante<$nbPagesAffichees)
							{
								$t->assign_vars(array(
										'urlSuivant'=>$this->creerUrl('','',array_merge($this->variablesGet,array('archiPageSource'=>($pageCourante+1)))),
										'onClickSuivant'=>""));
							}
							else
							{
								$t->assign_vars(array(
										'urlSuivant'=>'#',
										'onClickSuivant'=>''));
							}
							
							
						break;
					}
				}
				else
				{
					$t->assign_block_vars('pages.isPageCourante',array());
				}
			}
		}
		else
			echo "Erreur : le nombre d'enregistrements par page est de 0.";
		
		
		
		$limitSqlDebut = $nbEnregistrementsParPage * ($pageCourante-1);
		
		ob_start();
		$t->pparse('pagination');
		$html=ob_get_contents();
		ob_end_clean();
		
		$this->limitSqlDebut = $limitSqlDebut;
		$this->nbEnregistrementsParPage = $nbEnregistrementsParPage;
		
		// gestion des boutons suivant et precedent
		$lienBoutonSuivant="";
		$lienBoutonPrecedent="";

		if($pageCourante==1 && $pageCourante==$nbPages)
		{
			$lienBoutonPrecedent="#";
			$lienBoutonSuivant="#";
		}
		elseif($pageCourante==1)
		{
			$lienBoutonPrecedent="#";
			//$lienBoutonSuivant="annonces.php?".$nomParamPageCourante."=".($pageCourante+1);
			$lienBoutonSuivant = ($pageCourante+1);
		}
		elseif($pageCourante==$nbPages)
		{
			$lienBoutonPrecedent=($pageCourante-1);
			$lienBoutonSuivant="#";
		}
		else
		{
			$lienBoutonSuivant=($pageCourante+1);
			$lienBoutonPrecedent=($pageCourante-1);
		}
		
		// retour supplementaire si on précise la page courante en retour , pratique pour garder la page courante si on fais des actions sur le formulaire ou la liste qui demandes une mise a jour de la page
		$retourSupp = array();
		if(isset($parametres['nomParamPageCourante']) && $parametres['nomParamPageCourante']!='')
		{
			$retourSupp = array('pageCouranteUrl'=>"&".$parametres['nomParamPageCourante']."=".$pageCourante);
		}
		
		
		return array_merge(array('html'=>$html,'limitSqlDebut'=>$limitSqlDebut,'lienSuivant'=>$lienBoutonSuivant,'lienPrecedent'=>$lienBoutonPrecedent,'nbPages'=>$nbPages),$retourSupp);
	}
	
	// ajoute les limites a la requete
	public function addLimitToQuery($req="")
	{
		$req = $req." LIMIT ".$this->limitSqlDebut.",".$this->nbEnregistrementsParPage;
		return $req;
	}
	
	
}
?>
