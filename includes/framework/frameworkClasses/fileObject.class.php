<?php
// classe de gestion des dates
// Dorer Laurent 2008

// historique des versions
// version 1.1 --- 11/06/2008 - separation de la classe de date de l'objet config

class fileObject extends config
{
	function __construct()
	{
		parent::__construct();
	}

		
	// appel de l'applet qui gere l'upload multiple
	// attention l'applet n'est pas configurée pour fonctionner sur ie6 , a modifier !!
	
	function getAppletUploadMultiple($params=array())
	{
		$html="";
		
		if(isset($params['javascriptFunction']) && $params['javascriptFunction']!="")
		{
			$html.="<script  >";
			$html.=$params['javascriptFunction'];
			$html.="</script>";
		}
		
		//ex uploadDirPart1 = public_html/pia/img/photos
		// ex uploadDirPart2 = 1789 ( idOffre)
		/*$html.="
		<object classid=\"java:FtpApplet.class\" type=\"application/x-java-applet\" archive=\"".$params['cheminApplet']."/sFtpApplet.jar\" width=\"500\" height=\"500\">

<param name=\"pathImg\" value=\"".$params['uploadDirPart1']."\" />
<param name=\"idOffre\" value=\"".$params['uploadDirPart2']."\" />
<param name=\"functionCalledOnExit\" value=\"".$params['jsFunctionNameOnExit']."\" />
<param name=\"mayscript\" value=\"true\" /></object>";
		*/
		
		
		$codeApplet="
		<!-- The following code will only be interpreted by IE --> 
		<!--[if IE]> <!-->  
		<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" width=\"500\" height=\"500\" name=\"FtpApplet\"> 
		<param name=\"java_code\" value=\"FtpApplet.class\" />  
		<param name=\"java_codebase\" value=\"".$params['cheminApplet']."/\" />  
		<param name=\"java_archive\" value=\"sFtpApplet.jar\" />  
		<param name=\"type\" value=\"application/x-java-applet;version=1.5\" /> 

		<param name=\"pathImg\" value=\"".$params['uploadDirPart1']."\" />\n
		<param name=\"idOffre\" value=\"".$params['uploadDirPart2']."\" />\n
		<param name=\"functionCalledOnExit\" value=\"".$params['jsFunctionNameOnExit']."\" />\n
		<param name=\"mayscript\" value=\"true\" />


		<!--<![endif]--> 
		<!-- The following code will NOT be interpreted by IE --> 
		<!--[if !IE]> <!-->  
		<object classid=\"java:FtpApplet.class\" type=\"application/x-java-applet\" archive=\"".$params['cheminApplet']."/sFtpApplet.jar\" width=\"500\" height=\"500\">  
		<!-- Konqueror browser needs the following param -->  
		<param name=\"archive\" value=\"".$params['cheminApplet']."/sFtpApplet.jar\" />
		<param name=\"pathImg\" value=\"".$params['uploadDirPart1']."\" />\n
		<param name=\"idOffre\" value=\"".$params['uploadDirPart2']."\" />\n
		<param name=\"functionCalledOnExit\" value=\"".$params['jsFunctionNameOnExit']."\" />\n
		<param name=\"mayscript\" value=\"true\" />
		<!--<![endif]--> 
		<span style='font-size:11px;color:red;'>Attention, java n'est pas installé ou la version installée est trop ancienne, installez le en <a href='http://www.java.com/fr/download/' target='_blank'>cliquant ici</a>, vérifiez aussi que votre navigateur accepte le Java<br>sinon vous pouvez aussi ajouter vos photos une par une en cliquant sur l'option 'une image'.</span>		
		</object>

		";
		$html.=$codeApplet;
		return $html;
	}
	
	// creation d'un repertoire avec mise en place des droits et conversion des nom de fichiers en utf8
	public function creerRepertoire($repertoire, $droits=0777)
	{
		if(file_exists($repertoire))
		{
			if(!chmod($repertoire,$droits))
				return false;
		}
		else
		{
			if(!mkdir($repertoire,$droits))
				return false;
			
			if(!chmod($repertoire,$droits))
				return false;
		}

		// conversion des noms de fichier au format utf8 si existant
		exec("convmv -f iso-8859-1 -t utf-8 -r ".$repertoire."/* --notest",$retourExec); // laurent : conversion des noms du repertoire en UTF8
		
		return true;
	}
	
	
	// fonction permettant de parcourir un repertoire et d'ajouter tout les noms de fichiers de ce repertoire dans une table de base de donnée en precisant l'id de a quoi est rattaché le fichier dans la base 
	// ex : si c'est une offre on precisera l'id de l'offre a laquelle on ajoute ces fichiers parcourus (comme cela doit etre le cas le plus souvent dans la base de donnée) , sinon on modifiera la fonction
	// si un champ de libelle de fichier est renseigné, on gere le nom de fichier reel et le libelle du fichier d'origine (on garde ainsi le vrai nom du fichier dans la base de donnée)
	public function addFilesFromDirectoryToBdd($repertoireAParcourir,$nomTable,$bddContextIdFieldName,$bddFileNameField,$bddPosition="",$idContext,$bddPrimaryKeyField='',$renommerFichierAutomatique=false,$prefixeFichierRenomme='PIA',$champLibelleFichier="")
	{
		// parcours du repertoire
		
		$newPosition=0;
		if($bddPosition!='')
		{
			// recuperation de la derniere position des photos
			$queryPosition="SELECT MAX(".$bddPosition.") as position FROM ".$nomTable." where $bddContextIdFieldName='".$idContext."'";
			$resPosition = $this->connexionBdd->requete($queryPosition);
			$fetchPosition = mysql_fetch_assoc($resPosition);
			if(isset($fetchPosition['position']))
			{
				$newPosition=$fetchPosition['position'];
			}
		}
		
		
		if($repertoire = opendir($repertoireAParcourir)) 
		{
			exec("convmv -f iso-8859-1 -t utf-8 -r ".$repertoireAParcourir."/* --notest",$retourExec); // laurent : conversion des noms du repertoire en UTF8
			
			$numFichierCourant=$newPosition+1;
			while($fichier = readdir($repertoire))
			{
				if($fichier!='.' && $fichier!='..' && !is_dir($repertoireAParcourir.'/'.$fichier))
				{
					$ancienNomFichier = $fichier;

					$reqVerifDocument = "select * from $nomTable where $bddContextIdFieldName='".$idContext."' and $bddFileNameField=\"".$fichier."\"";
					
					$resVerifDocument = requete($reqVerifDocument);
					if(mysql_num_rows($resVerifDocument)==0)
					{
						if($renommerFichierAutomatique)
						{
							// recuper le nouvel id de photo
							$reqNewId = "SELECT MAX(".$bddPrimaryKeyField.") as dernierId FROM ".$nomTable;
							$resNewId = $this->connexionBdd->requete($reqNewId);
							$fetchNewId = mysql_fetch_assoc($resNewId);
							$newId = $fetchNewId['dernierId']+1;
							
							$extensionFichierOrigine = $this->getExtensionFromFile($fichier);
							if($extensionFichierOrigine!='')
							{
								$nouveauNomFichier = $prefixeFichierRenomme.$idContext."_".$newId.".".pia_strtolower($extensionFichierOrigine);
							}
							else
							{
								$nouveauNomFichier = $prefixeFichierRenomme.$idContext."_".$newId;
							}
							
							
							rename($repertoireAParcourir.$fichier,$repertoireAParcourir.$nouveauNomFichier);
							
							$fichier = $nouveauNomFichier;
						}
						// on rajoute le fichier dans la bdd pour la demande courante
						if($bddPosition!='')
						{
							if($champLibelleFichier!='')
							{
								$reqInsertDocument="insert into $nomTable ($bddContextIdFieldName,$bddFileNameField,$champLibelleFichier,$bddPosition) values ('".$idContext."',\"".$fichier."\",\"".$ancienNomFichier."\",'".$numFichierCourant."')";
							}
							else
							{
								$reqInsertDocument="insert into $nomTable ($bddContextIdFieldName,$bddFileNameField,$bddPosition) values ('".$idContext."',\"".$fichier."\",'".$numFichierCourant."')";
							}
							
							$resInsertDocument = requete($reqInsertDocument);
						}
						else
						{
							if($champLibelleFichier!='')
							{
								$reqInsertDocument="insert into $nomTable ($bddContextIdFieldName,$champLibelleFichier,$bddFileNameField) values ('".$idContext."',\"".$ancienNomFichier."\",\"".$fichier."\")";
							}
							else
							{
								$reqInsertDocument="insert into $nomTable ($bddContextIdFieldName,$bddFileNameField) values ('".$idContext."',\"".$fichier."\")";
							}
							$resInsertDocument = requete($reqInsertDocument);
						}
						$numFichierCourant++;
					}
				}
			}
		}
	}
	
	
	public function getListeFichiersArrayFrom($repertoireAParcourir,$params=array())
	{
		$arrayRetour=array();
		if(file_exists($repertoireAParcourir))
		{
			if(isset($params['getTableauInfosFichiers']) && $params['getTableauInfosFichiers']==true)
			{
				// parcours du répertoire
				if($repertoire = opendir($repertoireAParcourir)) 
				{
					while($fichier = readdir($repertoire))
					{
						if(isset($params['getAll']) && $params['getAll']==true)
						{
							if((isset($params['noUpDir']) && $params['noUpDir']==true && $fichier=='..') || (isset($params['noCurrentDir']) && $params['noCurrentDir']==true && $fichier=='.'))
							{
							
							}
							else
							{
								$isDir=false;
								if(is_dir($repertoireAParcourir.$fichier))
									$isDir=true;
									
								$arrayRetour[]=array("filename"=>$fichier,"isDir"=>$isDir);
							}
						}
						else
						{
							if($fichier!='.' && $fichier!='..' && !is_dir($repertoireAParcourir.$fichier))
							{
								$arrayRetour[]=array("filename"=>$fichier,"isDir"=>false);
							}
						}
					}
				}
			
			}
			else
			{
				// parcours du repertoire
				if($repertoire = opendir($repertoireAParcourir)) 
				{
					while($fichier = readdir($repertoire))
					{
						if(isset($params['getAll']) && $params['getAll']==true)
						{
							$arrayRetour[]=$fichier;
						}
						else
						{
							if($fichier!='.' && $fichier!='..' && !is_dir($repertoireAParcourir.$fichier))
							{
								$arrayRetour[]=$fichier;
							}
						}
					}
				}
			}
		}
		return $arrayRetour;
		
	}
	
	// ecriture dans un fichier de '$contenu'
	// si le fichier n'existe pas , il est cree , mais pas le repertoire
	public function writeToFile($contenu='',$cheminFichier='',$modeOuvertureFichier='a')
	{
		switch($modeOuvertureFichier)
		{
			case "a":
				$handleFichier=fopen($cheminFichier,$modeOuvertureFichier);
				fwrite($handleFichier,$contenu);
				fclose($handleFichier);
			break;
			
			default:
				echo "writeToFile : pas de mode d'ecriture défini.<br>";
			break;
		}
	}
	
	// lecture du fichier et retour du contenu en un bloc
	public function readFromFile($fichier='')
	{
		$contenu = fread(fopen($fichier, "r"), filesize($fichier));
		return $contenu;
	}
	
	// renvoi le nom d'extension du fichier
	public function getExtensionFromFile($fichier='')
	{
		$retour = "";
		
		$split = explode(".",$fichier);
		
		if(count($split)>1)
		{
			$retour = $split[count($split)-1];
		}
		return $retour;
	}
	
	// renvoi la partie gauche du nom de fichier, la partie avant l'extension
	public function getFileNameWithoutExtension($fichier='')
	{
		$retour="";
		$trouve=false;
		for($i=pia_strlen($fichier)-1 ; $i>0 && !$trouve ; $i--)
		{
			if(pia_substr($fichier,$i,1)=='.')
			{
				$trouve = true;
			}
		}
		
		if($trouve)
		{
			$retour=pia_substr($fichier,0,$i+1);
		}
		else
		{
			$retour = $fichier;
		}
		
		return $retour;
	}
	
	// ajoute un numero a la fin de la partie gauche du fichier , si le fichier existe dans le repertoire en parametre
	public function getNewFileNameIfFileNameExistsIn($fileName='',$directory='')
	{
		$extension = $this->getExtensionFromFile($fileName);
		$partieGauche = $this->getFileNameWithoutExtension($fileName);
		
		$i=0;
		while(file_exists($directory.$partieGauche.".".$extension))
		{
			$partieGauche.=$i;
			$i++;
		}
		
		return $partieGauche.".".$extension;
	}
	
	// gere un champ "input file", et copie le fichier uploadé vers le repertoire voulu
	// inputFileName = name de l'element file du formulaire html
	// possibilité de faire des redimensions
	// ATTENTION le repertoire de destination doit avoir ete créé avant l'appel de cette fonction, car celle ci ne le cree par , utiliser "creerRepertoire"
	public function handleUploadedFileSimpleMoveTo($params = array())
	{
		if(isset($params['inputFileName']) && $params['inputFileName']!='')
		{
			if(isset($_FILES[$params['inputFileName']]) && count($_FILES[$params['inputFileName']])>0)
			{
				if ($_FILES[$params['inputFileName']]['error']) 
				{
					switch ($_FILES[$params['inputFileName']]['error'])
					{
	                   case 1: // UPLOAD_ERR_INI_SIZE
	                   echo"Le fichier dépasse la limite autorisée par le serveur.";
	                   break;
	                   case 2: // UPLOAD_ERR_FORM_SIZE
	                   echo "Le fichier dépasse la limite autorisée dans le formulaire HTML.";
	                   break;
	                   case 3: // UPLOAD_ERR_PARTIAL
	                   echo "L'envoi du fichier a été interrompu pendant le transfert.";
	                   break;
	                   case 4: // UPLOAD_ERR_NO_FILE
	                   echo "Le fichier que vous avez envoyé a une taille nulle.";
	                   break;
					}
				}
				else
				{
					// pas d'erreur , on deplace le fichier temporaire vers la destination voulue
					if(isset($params['redimensionneImageConfig']) && is_array($params['redimensionneImageConfig']) && count($params['redimensionneImageConfig'])>0)
					{
						$i = new imageObject();
						foreach($params['redimensionneImageConfig'] as $tailleMax => $configRedim)
						{
							$i->redimension($_FILES[$params['inputFileName']]['tmp_name'],$this->getExtensionFromFile($_FILES[$params['inputFileName']]['name']),$configRedim['destination'],$tailleMax);
						}
					}
					elseif(isset($params['repertoireDestination']) && $params['repertoireDestination']!='')
					{
						$fileNameDestination = $_FILES[$params['inputFileName']]['name'];
						if(isset($params['renameFileTo']) && $params['renameFileTo']!='')
						{
							$fileNameDestination = $params['renameFileTo'];
						}
						
						move_uploaded_file($_FILES[$params['inputFileName']]['tmp_name'], $params['repertoireDestination']."/".$fileNameDestination);

					}
					else
					{
						echo "parametre manquant => framework/fileObject::handleUploadedFileSimpleMoveTo<br>";
					}
				}
			}
		}
	
	
	}
	
	// renvoie la taille d'un fichier , mise en forme (octets, kilooctets , mega ...)
	function fileSize($fichier)
	{
		$taille_fichier = "0";

		$taille_fichier = filesize($fichier);
		if ($taille_fichier >= 1073741824) 
		{
			$taille_fichier = round($taille_fichier / 1073741824 * 100) / 100 . " Go";
		}
		elseif ($taille_fichier >= 1048576) 
		{
			$taille_fichier = round($taille_fichier / 1048576 * 100) / 100 . " Mo";
		}
		elseif ($taille_fichier >= 1024) 
		{
			$taille_fichier = round($taille_fichier / 1024 * 100) / 100 . " Ko";
		}
		else 
		{
			$taille_fichier = $taille_fichier . " o";
		}

		return $taille_fichier;
	}
	
	// fonction qui renvoie une valeur de CRC pour un fichier => attention php > 5.1.2
	// permet de verifier l'integrité d'une copie de fichier entre la source et la destination par exemple
	// attention voir si ca pose probleme sur un systeme 64 bits
	function crc32_file($filename)
	{
		return hash_file ('CRC32', $filename , FALSE );
	}

	// utile pour que php puisse lire certains nom de fichier
	public function convertDirectoryFilesNamesToUTF8($params = array())
	{
		if(isset($params['repertoire']) && $params['repertoire']!='')
		{
			$slash = "/";
			if(pia_substr($params['repertoire'],-1)=='/')
			{
				$slash = "";
			}
			exec("convmv -f iso-8859-1 -t utf-8 -r ".$params['repertoire'].$slash."* --notest");
		}
	}
	
	
	
	// permet de convertir une chaine afin qu'elle passe a l'url rewriting ( pour les adresses par exemple)
	public function removeSpecialCharFromFileName($texte,$params=array())
	{
		$caractereDefault="_";
		if(isset($params['setCaractereDefault']))
		{
			$caractereDefault = $params['setCaractereDefault'];
		}
		$texte = str_replace("&nbsp;","_",$texte);
		$texte = strip_tags($texte);
		
		$texte = pia_strtolower($texte);
		$texte = pia_ereg_replace("[\ |\']",$caractereDefault,$texte);

		$texte = str_replace(array("ô","à","â","û","î","é","è","ê","&",";","(",")","ä"),array("o","a","a","u","i","e","e","e","et",$caractereDefault,$caractereDefault,$caractereDefault,"a"),$texte);
		$texte = str_replace(array(utf8_encode("ô"),utf8_encode("à"),utf8_encode("â"),utf8_encode("û"),utf8_encode("î"),utf8_encode("é"),utf8_encode("è"),utf8_encode("ê"),"&",";","(",")",utf8_encode("ä")),array("o","a","a","u","i","e","e","e","et",$caractereDefault,$caractereDefault,$caractereDefault,"a"),$texte);

		return $texte;
	}
	
}

?>
