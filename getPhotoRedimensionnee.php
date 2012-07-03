<?php

//session_start();
//include("./include/config.php");
//include("includes/framework/config.class.php");
extract($_GET,EXTR_OVERWRITE);

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
resizeImg($photoOriginale, $dimX, $dimX);

?>