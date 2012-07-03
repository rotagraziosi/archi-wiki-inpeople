<?php
// -------------------------------------------------------------------------------------------------------------
// version 1.01
// -------------------------------------------------------------------------------------------------------------
// version 1.0 - mise en ligne
// version 1.01 - correction bug , sauvegarde des parametres GET pour le rafraichissement du cache
// -------------------------------------------------------------------------------------------------------------
// gestion du cache
// -------------------------------------------------------------------------------------------------------------
// table utilisées dans la base de données :
//
// CREATE TABLE `gestionFichiersCache` (
//  `idCache` int(11) NOT NULL auto_increment,
//  `className` varchar(255) character set utf8 NOT NULL,
//  `methodName` varchar(255) character set utf8 NOT NULL,
//  `params` text character set utf8 NOT NULL,
//  `fileName` varchar(255) character set utf8 NOT NULL,
//  PRIMARY KEY  (`idCache`)
//)
//
//-------------------------------------------------------------------------------------------------------------
// utilisation :
// $this->gestionCache($objectName='',$methodToCall='',$nomFichierCache = '')
//
// => objectName est le nom de la classe appelé , methodToCall est la method de la classe précédent qui va permettre d'afficher l'element que l'on veut cacher , nomFichierCache est le nom du fichier (optionnel , car par defaut le nom de fichier est créé en fonction de la page appelé)
// pour que le cache fonctionne bien , il est préférable que les parametres transmis a la page qui doit s'afficher soient des parametres transmis en GET , le POST n'est pas géré pour le moment


class cacheObject extends config
{
	protected $repertoire;
	protected $fichierCourant;
	
	function __construct()
	{
		parent::__construct();
		$this->repertoire = $this->cheminPhysique.'/cache/';
		$this->fichierCourant = 'default';
	}

	// cette fonction va remettre le cache a zero
	// et donc le cache sera recréé fur et a mesure que l'on accedera aux pages qui sont destinés être caché
	public function resetCache()
	{
		// suppression des fichiers dans le repertoire du cache
		$file = new fileObject();
		$arrayFiles = $file ->getListeFichiersArrayFrom($this->repertoire);
		foreach($arrayFiles as $indice =>$filename)
		{
			unlink($this->repertoire.$filename);
		}
		
		// vidage de la table de gestion du cache
		$reqReset = "delete from gestionFichiersCache";
		$resReset = $this->connexionBdd->requete($reqReset);
		echo "Reset du cache effectué<br>";
	}
	
	
	// fonction permettant de regenere les pages de caches a partir de la table de la base de donn�es des appelles qui ont deja ete fait
	// de temps en temps il faudra faire un reset du cache afin de supprimer les pages de caches qui ont pu etre generés en trop car elles ont ete supprimées ( ces pages ne devrait pas affecter le fonctionnement du site , seulement le temps de generation du cache)
	// lors de l'appel a cette fonction il faut penser a inclure les classes requises
	public function refreshCachedPages()
	{
		$this->connexionBdd->getLock(array('refreshCache'));
		// parcours du repertoire
		$file = new fileObject();
		
		$resPages = $this->connexionBdd->requete("SELECT * FROM gestionFichiersCache");
		$backupGet = $_GET;
		while($fetchPages = mysql_fetch_assoc($resPages))
		{
			$_GET = unserialize($fetchPages['params']);
			$methodName = $fetchPages['methodName'];
			$className = $fetchPages['className'];
			
			$object = new $className;
			
			ob_start();
			echo call_user_method($methodName,$object);
			$html = ob_get_contents();
			ob_end_clean();
			
			$this->deleteCacheFile($fetchPages['fileName']);
			$this->createCache($html,$fetchPages['fileName']);
			//echo $fetchPages['fileName']." - rafraichi<br>";
		}
		$_GET = $backupGet;
		$this->connexionBdd->freeLock(array('refreshCache'));
	}
	
	
	public function refreshCache()
	{
		// $this->refreshCachedPages();
		//$this->connexionBdd->getLock(array('refreshCache'));
		//exec("/usr/bin/php -f ".$this->cheminPhysique."script/refreshCache.php");
		//$this->connexionBdd->freeLock(array('refreshCache'));
	}
	
	
	public function gestionCache($objectName='',$methodToCall='',$nomFichierCache = '')
	{
		$html='';
		
		if($nomFichierCache=='')
		{
			if(isset($_SERVER['argv'][0]) && $_SERVER['argv'][0]!='')
				$nomFichierCache = $_SERVER['argv'][0].'.html';
			else
				$nomFichierCache = str_replace('/','',$_SERVER['REQUEST_URI'].'index.html');
		}
		
		$this->fichierCourant = $nomFichierCache;
		if(!file_exists($this->repertoire.$this->fichierCourant))
		{
			$c = new $objectName; // instantiation de l'objet suivant le nom de classe entree en parametre
			// stockage dans la base de données des nouveaux parametres
			$className = get_class($c);
			$methodName = $methodToCall;
			$parametersListe = serialize($_GET);
			
			$resVerif = $this->connexionBdd->requete("
								SELECT * 
								FROM gestionFichiersCache 
								WHERE className='".$className."'
								AND methodName='".$methodName."'
								AND params=\"".addslashes($parametersListe)."\"
								AND fileName=\"".$nomFichierCache."\"
								");
			
			
			if(mysql_num_rows($resVerif)==0)
			{
				// on stocke les parametres d'appels de pages afin de pouvoir les regenerer par la suite
				$reqInsert = $this->connexionBdd->requete("INSERT INTO gestionFichiersCache (className,methodName,params,fileName) VALUES ('".$className."','".$methodName."',\"".addslashes($parametersListe)."\",'".$nomFichierCache."')");
			}
			
			
			
			// creation du fichier cache
			

			$codeHTML= call_user_method($methodToCall,$c);
			
			//echo "<br>creation OK !<br>";
			
			$this->createCache($codeHTML,$nomFichierCache);
			$html = $codeHTML;
		}
		else
		{
			// le fichier existe
			// on lit sont contenu et on le renvoi
			$html = $this->getFichierCache($this->fichierCourant);
		}
		return $html;
	}
	
	
	function createCache($codeHTML='',$nomFichierCache = 'fileName')
	{
		$this->fichierCourant = $nomFichierCache;
		
		$file = new fileObject();
		$file ->writeToFile($codeHTML,$this->repertoire.$nomFichierCache,'a');
		return $codeHTML;
	}
	
	function setRepertoireCache($repertoire='')
	{
		$this->repertoire = $repertoire;
	}
	
	// recuperation du contenu du fichier
	function getFichierCache($fichier)
	{
		// lecture du fichier
		$file = new fileObject();
		$contenu = $file->readFromFile($this->repertoire.$fichier);
		
		return $contenu;
	}
	
	
	function deleteCacheFile($fichier)
	{
		if(file_exists($this->repertoire.$fichier))
		{
			unlink($this->repertoire.$fichier);
		}
	}
}

?>