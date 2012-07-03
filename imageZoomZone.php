<?php

//session_start();

include("includes/framework/config.class.php");

$config = new config();

$idImage 					= $_GET['idImage'];
$idEvenementGroupeAdresse 	= $_GET['idEvenementGroupeAdresse'];
$idAdresseCourante			= $_GET['idAdresseCourante'];
$idHistoriqueImage 			= $_GET['idHistorique'];
$dateUpload					= $_GET['date'];


$sqlWhere = "";
if($idAdresseCourante!=0 && $idAdresseCourante!='')
{
	//$sqlWhere .= " AND idAdresse='$idAdresseCourante' ";
}

$erreur = false;
if($idEvenementGroupeAdresse != 0 && $idEvenementGroupeAdresse != '' && $idImage != 0 && $idImage != '')
{
	// recuperation de la zone concernée de l'image sur laquelle on va effectuer le zoom
	// en principe si on fait un appel a ce fichier pour afficher le zoom , c'est que le test sur la zone a ete effectué , et la zone existe bien
	$req = "
	SELECT coordonneesZoneImage, largeurBaseZoneImage, longueurBaseZoneImage
	FROM _adresseImage ai
	WHERE idImage = '".$idImage."'
	AND idEvenementGroupeAdresse = '".$idEvenementGroupeAdresse."'
	AND vueSur='1'
	$sqlWhere
	";

	$res = $config->connexionBdd->requete($req);

	if(mysql_num_rows($res)>0)
	{
		$fetch = mysql_fetch_assoc($res);
	
		$originalSizes 		= getimagesize($config->getCheminPhysiqueImage("originaux").$dateUpload."/".$idHistoriqueImage.".jpg");
		$originalWidth 		= $originalSizes[0];
		$originalHeight		= $originalSizes[1];
		$originalPhysique 	= $config->getCheminPhysiqueImage("originaux").$dateUpload."/".$idHistoriqueImage.".jpg";
		
		$largeurBase 	= $fetch['largeurBaseZoneImage'];
		$longueurBase	= $fetch['longueurBaseZoneImage'];
		
		
		
		$rapport = $originalHeight/$longueurBase;
		
		
		list($x1,$y1,$x2,$y2) = explode(",",$fetch['coordonneesZoneImage']);
		
		
		$largeurZone = abs($x2-$x1);
		$hauteurZone = abs($y2-$y1);
		
		
		$largeurDestination = $largeurZone;//*$rapport;
		$hauteurDestination = $hauteurZone;//*$rapport;
		
		
		// on va limiter l'affichage a un max de 200 en hauteur et 200 en largeur
		if($largeurDestination>200 || $hauteurDestination>200)
		{
			if($largeurDestination>200)
			{
				$hauteurDestination = $hauteurDestination*200/$largeurDestination;
				$largeurDestination = 200;
			}
			
			if($hauteurDestination>200)
			{
				$largeurDestination = $largeurDestination*200/$hauteurDestination;
				$hauteurDestination = 200;
			}
		}
		elseif($largeurDestination < 200 && $hauteurDestination < 200)
		{
			if($largeurDestination>$hauteurDestination)
			{
				$hauteurDestination = $hauteurDestination*200/$largeurDestination;
				$largeurDestination = 200;
			}
			else
			{
				$largeurDestination = $largeurDestination*200/$hauteurDestination;
				$hauteurDestination = 200;
			}
		
		}
		
		
		
		
		$imgOriginale = imagecreatefromjpeg($originalPhysique);
		$imgDestination = imagecreatetruecolor($largeurDestination,$hauteurDestination);

		$xOriginale = $x1*$rapport;
		
		
		$yOriginale = $y1*$rapport;
		
		$largeurZone = $largeurZone*$rapport;
		$hauteurZone = $hauteurZone*$rapport;
		
		$largeurZoneBak = $largeurZone;
		$hauteurZoneBak = $hauteurZone;
		
		$is10Percent = true;
		if($is10Percent)
		{	
			
		
			$xOriginale -= ($largeurZone*10/100); // 10%
			$yOriginale -= ($hauteurZone*10/100); // 10%
			
			
			$largeurZone += ($largeurZone*20/100); // 10% de chaque coté
			$hauteurZone += ($hauteurZone*20/100); //10% de chaque coté

			if($xOriginale+$largeurZone>$originalWidth)
			{
				$largeurZone = $originalWidth - $xOriginale;
			}
			
			if($yOriginale+$hauteurZone>$originalHeight)
			{
				$hauteurZone = $originalHeight - $yOriginale;
			}
			
			if($yOriginale<0)
			{
				$yOriginale=0;
			}
			
			if($xOriginale<0)
			{
				$xOriginale=0;
			}
			
		}
		
		imagecopyresampled($imgDestination,$imgOriginale,0,0,$xOriginale,$yOriginale,$largeurDestination,$hauteurDestination,$largeurZone,$hauteurZone);
		
		//$imgo = imagecreatefromjpeg($originalPhysique);
		
		ob_start();
		header('Content-type: image/jpeg');
		//imagejpeg($imgOriginale,null,100);
		imagejpeg($imgDestination,null,100);
		ob_end_flush();
		
		
		
	}
	else
	{
		$erreur = true;
	}



}
else
{
	$erreur = true;
}

// s'il y a une erreur , on va afficher une image par defaut
if($erreur)
{


}



function resizeImg($imgfile, $maxwidth, $maxheight) {
  
  $font = "./DejaVuSerif.ttf";
	$txt = "www.archi-strasbourg.org";

	$imgo = imagecreatefromjpeg($imgfile);
	$widtho  = imagesx($imgo);
	$heighto = imagesy($imgo);

	$rx = ($widtho > $maxwidth)		? $maxwidth / $widtho	: 1 ;
	$ry = ($heighto > $maxheight)	? $maxheight / $heighto	: 1 ;

	$c = min($rx, $ry);

	$widthr  = $widtho  * $c;
	$heightr = $heighto * $c;

	$x = floor(($maxwidth  - $widthr ) / 2);
	$y = floor(($maxheight - $heightr) / 2);
	
	$imgr = imagecreatetruecolor($widthr, $heightr);
	//$blue = imagecolorallocatealpha($imgr, 180, 193, 205, 0);
	
	//imagefill($imgr, 0, 0, $blue);
	imagecopyresized($imgr, $imgo, 0, 0, 0, 0, $widthr, $heightr, $widtho, $heighto);
	
	//$color_txt = imagecolorallocatealpha($imgr, 175, 175, 175, 0); 
	//imagettftext($imgr, 12, 0, $widthr - 135, $heightr - 3, $color_txt, $font, $txt);

	//$imgt = imagecreatetruecolor($maxwidth, $maxheight);	
	//$blue = imagecolorallocatealpha($imgt, 255, 235, 136, 0); 
	//imagefill($imgt, 0, 0, $blue);
	//imagecopyresized($imgt, $imgr, $x, $y, 0, 0, $widthr, $heightr, $widthr, $heightr);

	imagecopyresampled($imgr, $imgo, 0, 0, 0, 0, $widthr, $heightr, $widtho, $heighto);
	ob_start();
	header('Content-type: image/jpeg');
	imagejpeg($imgr,null,100);
	ob_end_flush();
	//imagedestroy($imgt);
	imagedestroy($imgo);
	imagedestroy($imgr);
}
putenv('GDFONTPATH=.');



//$cheminphoto = $RepertoirePhoto.$idDescription."/";
//$imgfile = $cheminphoto.$file;
//$imgsize = $_SESSION['dim_img'];
//$imgsize = array($imglargeur,$imghauteur);
//$config = new config();
//resizeImg($photoOriginale, $dimX, $dimX);

?>
