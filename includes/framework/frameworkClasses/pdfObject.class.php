<?php
// classe de creation de fichier pdf utilisant la librairie html2pdf et fpdf qui doivent etre copiés dans le repertoire pdf du framework
// Dorer Laurent 2010

// attention la class utilisée html2pdf.class.php n'accepte pas l'encodage en utf8
// pour le moment on fait un conversion ... attention , pas de symbol euro donc

// historique des versions
// version 1.0 --- 26/02/2010 - creation de l'objet
// version 1.1 --- 28/04/2010 - nouvelle version du convertisseur (php5 utf8)

include_once("pdf/html2pdf.class.php");
class pdfObject extends config
{
	var $objetPdf;
	
	function __construct()
	{
		$this->objetPdf =  new HTML2PDF('P','A4');
		//$this->objetPdf->setEncoding("ISO-8859-15"); // UTF8 pas accepté , voir pour les mises a jour de la librairie
		// UTF8 par defaut maintenant
	}
	
	
	// le contenu est de l'html sans les balises html et body, donc juste le contenu
	// vu que la class n'accepte pas l'utf8 pour le moment, on fait un utf8_decode et un remplacement automatique du symbol euro
	public function setContent($content='')
	{
		//$this->objetPdf->WriteHTML(utf8_decode(str_replace("€","euro",$content)));
		$this->objetPdf->WriteHTML($content);
	}
	
	// chemin represente le chemin physique et le nom du fichier à generer
	public function writeToFile($chemin='')
	{
		$retour = true;
		$contentPDF = $this->objetPdf->Output($chemin,true);	
		
		touch($chemin);
		if (is_writable($chemin)) 
		{
			  if (!$handle = fopen($chemin, 'a')) 
			  {
				   echo "Impossible d'ouvrir le fichier ($chemin)";
				   $retour = false;
			  }
			  if (fwrite($handle, $contentPDF) === FALSE) 
			  {
				   echo "Impossible d'écrire dans le fichier ($chemin)";
				   $retour = false;
			  }
			  fclose($handle);
		}
		else
		{
			echo "Impossible d'utiliser le fichier creer. ($chemin) ";
			$retour = false;
		}
		
		return $retour;
	}
}

?>