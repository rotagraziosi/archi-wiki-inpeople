<?php
/**
 * Logiciel : librairie complementaire a utiliser avec HTML2PDF
 * 
 * Convertisseur json => HTML2PDF pour  dessiner plus facilement 
 * Distribué sous la licence LGPL. 
 *
 * @author		Laurent MINGUET <webmaster@html2pdf.fr>
 */

function convertJsonToHTML2PDF($json)
{
	// conversion en tableau
	$json = preg_replace('/([a-z]+):/', '"$1":', $json);
	$inf = array();
	$tmp = explode('},{', substr($json, 1, -1));
	foreach($tmp as $v)
		$inf[] = json_decode('{'.$v.'}', true);
	unset($tmp); unset($json);
	
	$content = '';
	foreach($inf as $val)
	{
		$path = '<path style="';
		if (isset($val['stroke']) && $val['stroke']=='none') unset($val['stroke']);
		if (isset($val['stroke']))
		{
			if (isset($val['stroke-width']))
			{
				$path.= 'stroke-width:'.$val['stroke-width'].'mm;';
				unset($val['stroke-width']);
			}
			else
			{
				$path.= 'stroke-width:1mm;';
			}
			$path.= 'stroke:'.$val['stroke'].';';
			unset($val['stroke']);
		}
		
		if (isset($val['fill']))
		{
			$path.= 'fill:'.$val['fill'].';';
			unset($val['fill']);
		}
		$path.='" ';
		if (isset($val['type']))
		{
			switch($val['type'])
			{
				case 'path':
					$path.='d="'.$val['path'].'" ';
					unset($val['path']);
					break;
				default:
					echo 'Type <b>'.$val['type'].'</b> non connu';
					exit;	
			}
			unset($val['type']);
		}
		if (count($val))
		{
			echo 'prop non reconnu: <pre>'.print_r($val).'</pre>'; exit;	
		}
		$path.= '>';
		$content.= $path;
	}
	
	return $content;
}