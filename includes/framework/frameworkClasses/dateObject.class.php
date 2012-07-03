<?php
// classe de gestion des dates
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - separation de la classe de date de l'objet config

class dateObject extends config
{
		var $tabToSort; // tableau servant pour faire des tris de date associes
	
		
		private static $initDatePicker = 0;
		function __construct()
		{
			
		}

		function toBdd($date)
		{
			$retour="";
			if(trim($date)=='' || $date=='0000-00-00')
			{
				$retour='0000-00-00';
			}
			else
			{
				// $date est au format francais 
				$tab = explode('/',$date);
				$retour = $tab[2].'-'.$tab[1].'-'.$tab[0];
			}
			return $retour;
		}
		
		function toFrench($date)
		{
			$retour = array();
			
			// c'est une date simple :
			if (pia_strpos($date, ':') === false)
			{
				$tab=explode('-',$date);
				foreach($tab AS $val)
				{
					if ($val > 0)
					{
						$retour[] = $val;
					}
				}
				
				$retour = implode( '/', array_reverse($retour));
			}
			else
			{
				list( $annee, $heure) = explode(' ', $date);
				
				$tabAnnee = explode('-', $annee);
				$retourAnnee = array();
				foreach($tabAnnee AS $val)
				{
					if ($val > 0)
					{
						$retourAnnee[] = $val;
					}
				}

				$tabHeures = explode(':', $heure);
				$retourHeure = $tabHeures[0].'H'.$tabHeures[1];

				$retour = implode( '/', array_reverse($retourAnnee)).' '.$retourHeure;
			}
			return $retour;
		}
		
		// meme fonction que toFrench , mais on enleve les 0 devant les chiffres des dates
		function toFrenchAffichage($date)
		{
			$retour = array();
			
			// c'est une date simple :
			if (pia_strpos($date, ':') === false)
			{
				$tab=explode('-',$date);
				foreach($tab AS $val)
				{
					if ($val > 0)
					{
						$retour[] = intval($val);
					}
				}
				
				$retour = implode( '/', array_reverse($retour));
			}
			else
			{
				list( $annee, $heure) = explode(' ', $date);
				
				$tabAnnee = explode('-', $annee);
				$retourAnnee = array();
				foreach($tabAnnee AS $val)
				{
					if ($val > 0)
					{
						$retourAnnee[] = intval($val);
					}
				}

				$tabHeures = explode(':', $heure);
				$retourHeure = $tabHeures[0].'H'.$tabHeures[1];

				$retour = implode( '/', array_reverse($retourAnnee)).' '.$retourHeure;
			}
			return $retour;
		}
		
		// fonction un peu plus courte que la precedente et qui permet d'afficher la date au format 01/01/2010 plutot que 1/1/2010
		function toFrenchAffichage0($dateUS='')
		{
			return date("d/m/Y",strtotime($dateUS));
		}
		
		// renvoi une date (par exemple 1850) ou ne figure qu'une annee ou qu'un mois et une annee , au format french , ensuite il suffit de passer la fonction toBdd pour l'enregistrer dans la bdd
		function convertYears($date)
		{
			$converted='';
			$dateSansSeparateur = preg_replace('#[^0-9/]#','', $date);
			if($date != '')
			{
				if (pia_strlen($dateSansSeparateur) <= 4)
				{
					$converted = '00/00/'.$date;
				}
				else
				{
					$tabDate = explode ('/', $date);
					if (count($tabDate) === 2)
					{
						$converted = '00/'.$tabDate[0].'/'.$tabDate[1];
					}
					else if (count($tabDate) === 3)
					{
						$converted = $tabDate[0].'/'.$tabDate[1].'/'.$tabDate[2];
					}
					else
					{
						//echo  "le champ date doit comprendre 8 chiffres :: convertYears (".$date.")";
					}

				}
			}
			return $converted;
		}
		
		// recupere une date au format 20080302 dans une chaine de caractere (utilisé pour recuperer la date dans le nom d'un fichier image)
		public function extractDateFromString($string="")
		{
			$retour['isDate']=false;
			$retour['dateExtracted']="0000-00-00";
			
			if(preg_match("/([0-9]{4})([0-9]{1,2})([0-9]{1,2})/", $string, $resultats))
			{
				if(checkdate($resultats[2],$resultats[3],$resultats[1]))
				{
					$retour['isDate']=true;
					$retour['dateExtracted'] = $resultats[1]."-".$resultats[2]."-".$resultats[3];
				}
			}	
			
			return $retour;
		}
		
		// la date1 est elle plus récente que la date2
		// date1 et date2 sont au format anglais avec des - , ex : 2009-24-12
		function isGreaterThan($date1="",$date2="",$greaterOrEqual=false,$accept00=false)
		{
			$retour = false;
			
			if($accept00)
			{
				// si ce parametre est specifié , on converti les dates du type 1450-00-00 en 1450-01-01
				if(pia_substr($date1,-5)=='00-00')
				{
					$date1 = pia_substr($date1,0,-5).'01-01';
				}
				
				if(pia_substr($date2,-5)=='00-00')
				{
					$date2 = pia_substr($date2,0,-5).'01-01';
				}
			}
			
			list($year1,$mon1,$day1) = explode("-",$date1);
			list($year2,$mon2,$day2) = explode("-",$date2);
			
			
			if($greaterOrEqual==false)
			{
				if($year1>$year2)
				{
					$retour = true;
				}
				elseif($year1==$year2 && $mon1>$mon2)
				{
					$retour = true;
				}
				elseif($year1==$year2 && $mon1==$mon2 && $day1>$day2)
				{
					$retour = true;
				}
			}
			else
			{
				if($year1>$year2)
				{
					$retour = true;
				}
				elseif($year1==$year2 && $mon1>$mon2)
				{
					$retour = true;
				}
				elseif($year1==$year2 && $mon1==$mon2 && $day1>$day2)
				{
					$retour = true;
				}
				elseif($year1==$year2 && $mon1==$mon2 && $day1==$day2)
				{
					$retour=true;
				}
			}
			

			return $retour;
		}
		
		// **********************************************************************************************************************************
		// fonctions permettant de mettre en place rapidement un datePicker
		// **********************************************************************************************************************************
		public function getJsIncludePopupDatePicker($params=array())
		{
			
			$retour="<script type='text/javascript' src='".$this->urlFrameworkFromRoot."frameworkClasses/datePicker/datePicker.js'></script>";
			
			return $retour;
		}
		
		// cette fonction se charge d'inclure le code js du datepicker dans le header, pas besoin de la fonction getJsIncludePopupDatePicker
		public function getJsCallToDatePicker($params=array())
		{
			if(self::$initDatePicker==0)
			{
				$this->addToJsHeader("<script type='text/javascript' src='".$this->urlFrameworkFromRoot."frameworkClasses/datePicker/datePicker.js'></script>");
				
				
				self::$initDatePicker++;// evite d'inclure le js plusieurs fois
			}
			// le parametre toElementDestination doit etre du type : document.nomFormulaire.nomElement   ( pas de getElementById )
			$retour="javascript:show_calendar('".$params['toElementDestination']."', ".$params['toElementDestination'].".value,'https://strasbourg.pia.com.fr/');";
			
			return $retour;
		}
		
		// ajoute $mth mois a la date passée en parametre
		function addMonthsToDate($mth=0,$orgDate="")
		{
			$cd = strtotime($orgDate);
			$retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mth,date('d',$cd),date('Y',$cd)));
			return $retDAY;
		}
		
		function addDaysToDate($days=0,$orgDate="")
		{
			$cd = strtotime($orgDate);
			$retDay = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
			return $retDay;
		}
		
		function getDaysBetweenTwoDates($date1="",$date2="")
		{
			//return round((strtotime($date1) - strtotime($date2))/(60*60*24)-1);
			
			$date1 = strtotime($date1);

			$date2 = strtotime($date2);

			$diff_date = $date2-$date1;
			$diff['heures'] = (int)($diff_date/(60*60));
			$diff['jours'] = (int)($diff_date/(60*60*24));
			
			return abs($diff['jours']);
		}
		
		// ******************************************************************************************************************************************
		// renvoie l'id associé a une date de debut et une date de fin auxquels correspondent la dateToCompare
		// si on met des dateEnd vides , on peut faire du classement de date , associe a un identifiant de notre choix , mais dateBegin doit toujours etre renseigné sinon la fonction s'arrete
		// exemple d'utilisation:
		//				$tab = array();
		//		while($fetch=mysql_fetch_assoc($res))
		//		{
		//			$tab[] = array('dateBegin'=>$fetch['dateDebutBail'],'dateEnd'=>$fetch['dateFinBail'],'idAssociated'=>$fetch['idDemande']);
		//		}
		//		
		//		$retour = $dateObject->getIdAssociatedInDateIntervals(array('arrayDates'=>$tab,'dateToCompare'=>date('Y-m-d')));
		// ******************************************************************************************************************************************
		public function getIdAssociatedInDateIntervals($params = array())
		{
			$retour = 0;
			$erreur = new objetErreur();
			$erreurDateBegin=false;
			if(isset($params['arrayDates']) && count($params['arrayDates'])>0 && $params['dateToCompare']!='')
			{
				foreach($params['arrayDates'] as $indice => $interval)
				{
					// verif des dates : est ce qu'elles ont toute une date de debut, sinon on ne peut pas faire la verification correctement
					if($interval['dateBegin']=='0000-00-00' || $interval['dateBegin']=='')
					{
						$erreur->ajouter("dateObject::getIdAssociatedInDateInterval => Attention, une dateBegin n'est pas valide, on stop la fonction pour eviter une mauvaise analyse des données, veuillez contacter l'administrateur.");
						$retour = 0;
						$erreurDateBegin = true;
						break;
						
					}
				}
			}
			
			
			if(!$erreurDateBegin)
			{
				// il y a toujours une date de debut, on commence l'analyse
				// on va creer un tableau ou l'on va classer les dates de debut de la plus ancienne a la plus recente
				
				// on copie le tableau dans un tableau de travail avec des indices
				$i=0;
				$tTravail = array();
				foreach($params['arrayDates'] as $indice => $intervals)
				{
					$tTravail[$i] = $intervals;
					$i++;
				}

				// maintenant on classe le tableau par date de debut
				$modif = true;
				while($modif)
				{
					$modif=false;
					for($i = 0 ; $i<count($tTravail)-1 ; $i++)
					{
						if($this->isGreaterThan($tTravail[$i]['dateBegin'],$tTravail[$i+1]['dateBegin'])) // est plus grand strictement
						{
							// on switch
							$temp = $tTravail[$i];
							$tTravail[$i] = $tTravail[$i+1];
							$tTravail[$i+1] = $temp;
							$modif=true;
						}
					}
				}

				// maintenant que le tTravail est classé , on peut faire des comparaisons avec la dateToCompare en entree de la fonction
				$indiceTableau = -1;
				
				$superieurOrEqual = true;
				if(isset($params['dateBeginSuperiorStrict']) && $params['dateBeginSuperiorStrict']==true)
				{
					$superieurOrEqual = false;
				}
				
				for($i=0 ; $i<count($tTravail) ; $i++)
				{
					if($this->isGreaterThan($params['dateToCompare'],$tTravail[$i]['dateBegin'],$superieurOrEqual))
					{
						$indiceTableau = $i;
					}
				}
				
				if($indiceTableau != '-1')
				{ // trouvé
					// verifions encore que s'il existe une date de fin d'intervalle , celui ci soit bien superieur a la dateToCompare
					if($tTravail[$indiceTableau]['dateEnd']!='0000-00-00' && $tTravail[$indiceTableau]['dateEnd']!='')
					{
						if($this->isGreaterThan($tTravail[$indiceTableau]['dateEnd'],$params['dateToCompare'],true))
						{
							$retour = $tTravail[$indiceTableau]['idAssociated'];
						}
						else
						{
							// l'intervalle de fin est plus ancien que la date de fin , on est donc aussi hors de l'intervalle
							$retour = 0;
						}
					}
					else
					{
						$retour = $tTravail[$indiceTableau]['idAssociated'];
					}
				}
				else
				{
					// pas trouvé , aucun interval ne correspond
					$retour = 0;
				}
			}
			
			if($erreur->getNbErreurs()>0)
			{
				echo $erreur->afficher();
			}
			
			
			return $retour;
		}
		
		// renvoi le nom d'un mois en fonction de son numero ex 1=> janvier , 12 => decembre
		public function getNomMoisFromNumeroMois($params = array())
		{
			$retour = "";
			if(isset($params['numMois']) && $params['numMois']!='' && $params['numMois']!='0')
			{
				switch($params['numMois'])
				{
					case '1':
						$retour = "janvier";
					break;
					case '2':
						$retour = "février";
					break;
					case '3':
						$retour = "mars";
					break;
					case '4':
						$retour = "avril";
					break;
					case '5':
						$retour = "mai";
					break;
					case '6':
						$retour = "juin";
					break;
					case '7':
						$retour = "juillet";
					break;
					case '8':
						$retour = "aout";
					break;
					case '9':
						$retour = "septembre";
					break;
					case '10':
						$retour = "octobre";
					break;
					case '11':
						$retour = "novembre";
					break;
					case '12':
						$retour = "décembre";
					break;
				}
			}
			return $retour;
		}
		
		// ************************************************************************************************************************************************************************
		// le tableau doit etre du type tableau[$indice] = array('date'=>date, + autre champs)
		public function doSortByDate($params = array())
		{
			$retour = null;
			
			$isInversion=true;
			
			for($j = 0; $isInversion ; $j++)
			{	
				$isInversion=false;
				for($i=0 ; $i<count($this->tabToSort)-1 ; $i++)
				{
					if(isset($params['order']) && $params['order']=='ASC')
					{
						if($this->isGreaterThan($this->tabToSort[$i]['date'],$this->tabToSort[$i+1]['date']))// est plus grand strictement
						{
							$isInversion=true;
							$temp = $this->tabToSort[$i];
							$this->tabToSort[$i] = null;
							$this->tabToSort[$i] = $this->tabToSort[$i+1];
							$this->tabToSort[$i+1] = null;
							$this->tabToSort[$i+1] = $temp;
						}
					}
					else
					{
						if($this->isGreaterThan($this->tabToSort[$i+1]['date'],$this->tabToSort[$i]['date']))// est plus grand strictement
						{
							$isInversion=true;
							$temp = $this->tabToSort[$i];
							$this->tabToSort[$i] = null;
							$this->tabToSort[$i] = $this->tabToSort[$i+1];
							$this->tabToSort[$i+1] = null;
							$this->tabToSort[$i+1] = $temp;
						}
					}
				}
			}
			
			return $this->tabToSort;
		}
		
		public function addElemToSort($params = array())
		{
			if(!isset($this->tabToSort) || !isset($this->tabToSort[0]))
			{
				$this->tabToSort = array();
			}
			
			if(isset($params['date']) && isset($params['value']))
			{
				$this->tabToSort[count($this->tabToSort)] = array('date'=>$params['date'],'value'=>$params['value']);
			}
		}
		
		// renvoi la date de debut et la date de fin du mois de l'annee en parametres
		public function getDebutAndFinDatesFromMois($params = array())
		{
			$retour = array('dateDebutMois'=>'','dateFinMois'=>'');
			if(isset($params['mois']) && isset($params['annee']))
			{
				$retour['dateDebutMois'] = $params['annee']."-".$params['mois']."-01";
				$retour['dateFinMois'] = $this->addDaysToDate(-1,$this->addMonthsToDate(1,$params['annee']."-".$params['mois']."-01"));
			
			}
			return $retour;
		}
		
		// renvoie une fonction JS qui permet de valider une date, il suffit de l'appel par code javascript en precisant l'id de l'element de formulaire a tester
		// la date doit etre au format fr (dd/mm/YYYY)
		public function getJsDateValidation($params = array())
		{
			if(!isset($params['noBalisesScript']) || $params['noBalisesScript']==false)
				$js="<script  >";
		
			$js .= "
				
			function checkDateValid(elementId,intituleChamp)
			{
			   verdat=document.getElementById(elementId).value;


				if(!isValidDate(verdat))
				{
					alert(\"la date \"+intituleChamp+\" n'est pas valide ou n'est pas au bon format. Format : jj/mm/aaaa\");
					return false;
				}
				else
				{
					document.getElementById(elementId).value = verdat;
					return true;
				}

				return false;
			}

			function isValidDate(d) {
var dateRegEx = /^((((0?[1-9]|[12]\d|3[01])[\.\-\/](0?[13578]|1[02])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|[12]\d|30)[\.\-\/](0?[13456789]|1[012])[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|((0?[1-9]|1\d|2[0-8])[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?\d{2}))|(29[\.\-\/]0?2[\.\-\/]((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00)))|(((0[1-9]|[12]\d|3[01])(0[13578]|1[02])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|[12]\d|30)(0[13456789]|1[012])((1[6-9]|[2-9]\d)?\d{2}))|((0[1-9]|1\d|2[0-8])02((1[6-9]|[2-9]\d)?\d{2}))|(2902((1[6-9]|[2-9]\d)?(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)|00))))$/;
return d.match(dateRegEx);
			} 
 
			
			
			";
			
			if(!isset($params['noBalisesScript']) || $params['noBalisesScript']==false)
				$js.="</script>";
			
			return $js;
		}
		
		// fonction js permettant de convertir une date fr en date us
		// utile pour les formulaires ou on veut genere un nom de fichier et on a des periodes fr par exemple
		public function getJsDateFRToDateUS($params = array())
		{
			$retour = "";
			
			if(!isset($params['noBalisesScript']) || $params['noBalisesScript']==false)
				$retour.="<script  >";
			
			$retour.="
			
			
				function getDateUs(dateFr)
				{
					jour = '';
					mois = '';
					annee = '';
					
					for(i=0 ; i< dateFr.length && dateFr.substring(i,i+1)!='/' ; i++)
					{
						jour+=dateFr.substring(i,i+1);
					}
					i++;
					for(i=i ; i< dateFr.length && dateFr.substring(i,i+1)!='/' ; i++)
					{
						mois+=dateFr.substring(i,i+1);
					}
					i++;
					for(i=i ; i< dateFr.length ; i++)
					{
						annee+=dateFr.substring(i,i+1);
					}
				
					return annee+'-'+mois+'-'+jour;
				}
			";
		
			if(!isset($params['noBalisesScript']) || $params['noBalisesScript']==false)
				$retour.="</script>";
			
			return $retour;
		
		}
		
		
		
}

?>
