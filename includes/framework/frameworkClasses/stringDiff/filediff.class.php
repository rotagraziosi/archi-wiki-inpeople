<?php

	// classe pour afficher les différences entre deux fichiers , 
	// modifiee pour qu'elles prennent aussi des textes (et pas que des fichiers)


	define ('FD_LINE_NORMAL', 1);
	define ('FD_LINE_ADDED', 2);
	define ('FD_LINE_DELETED', 3);
	define ('FD_INLINE_NORMAL', 4);
	define ('FD_INLINE_ADDED', 5);
	define ('FD_INLINE_DELETED', 6);
	
	class filediff {
		private $files = array();
		private $line_format = array();
		private $header;
		private $footer;
		
		private $isText;
		
		private $uid;
		public function __construct() {
			$this->files['new'] = array();
			$this->files['old'] = array();
			$this->files['res'] = array();
		
			$this->uid = md5(microtime());
		
			$this->set_line_format('<tr><td>{line:'. $this->uid .'}</td><td>{value:'. $this->uid .'}</td></tr>', FD_LINE_NORMAL);
			$this->set_line_format('<tr style="background-color:#99ffaa;"><td>{line:'. $this->uid .'}</td><td>{value:'. $this->uid .'}</td></tr>', FD_LINE_ADDED);
			$this->set_line_format('<tr style="background-color:#ff9999;"><td>{line:'. $this->uid .'}</td><td>{value:'. $this->uid .'}</td></tr>', FD_LINE_DELETED);
			$this->set_line_format('<span>{value:'. $this->uid .'}</span>', FD_INLINE_NORMAL);
			$this->set_line_format('<span style="background-color:#44ff55;">{value:'. $this->uid .'}</span>', FD_INLINE_ADDED);
			$this->set_line_format('<span style="background-color:#ff6666;">{value:'. $this->uid .'}</span>', FD_INLINE_DELETED);
 
			$this->set_header('<table>');
			$this->set_footer('</table>');
		}
		
		
		public function set_textes($new, $old, $separator='<br>')
		{
			$this->isText = true;
			$this->files['new']['content'] = explode($separator,strip_tags(nl2br($new),$separator));
			$this->files['old']['content'] = explode($separator,strip_tags(nl2br($old),$separator));
			$this->files['res']['content'] = array();
			
		}
		
		
		
		public function set_files($new, $old)
		{
			$this->isText = false;
			if (! file_exists($new) )
			{
				trigger_error('Le fichier '.$new.' n\'existe pas');
			}
			elseif (! file_exists($old) )
			{
				trigger_error('Le fichier '.$old.' n\'existe pas');			
			}
			else
			{
				$this->files['new'] = array(
					'name' => $new,
					'content' => file($new)
				);
				$this->files['old'] = array(
					'name' => $new,
					'content' => file($old)
				);
				$this->files['res']['content'] = array();
			}
		}
		public function set_destination($res)
		{
			$this->files['res']['name'][$res];
		}
		public function set_line_format($format, $type)
		{
			switch ($type)
			{
				case FD_LINE_NORMAL:
				case FD_LINE_ADDED:
				case FD_LINE_DELETED:
				case FD_INLINE_NORMAL:
				case FD_INLINE_ADDED:
				case FD_INLINE_DELETED:
					$this->line_format[$type] = str_replace(
						array('{value}', '{line}'),
						array('{value:'. $this->uid .'}','{line:'. $this->uid .'}'),
						$format
					);
					break;
				default:
					echo 'error: set_line_format';
					break;						
			}
		}
		public function set_header($header) { $this->header = $header; }
		public function set_footer($footer) { $this->footer = $footer; }
		
		public function display()
		{
			$html = "";
			$html.= $this->header;
			if(isset($this->isText) && $this->isText==true)
			{
				$html.= implode("<br>", $this->files['res']['content']);
			}
			else
			{
				$html.= implode("\n", $this->files['res']['content']);
			}
			$html.= $this->footer;
			
			return $html;
		}
		public function execute($dest = null)
		{
			// On récupère le tableau des ajouts
			$adds = array_diff($this->files['new']['content'], $this->files['old']['content']);
			// On récupère le tableau des suppressions
			$dels = array_diff($this->files['old']['content'], $this->files['new']['content']);
			$i = 0;
			// Tant que le nouveau fichier n'a pas été parcourru entièrement
			foreach($this->files['new']['content'] as $k => $v)
			{
				// Si la ligne est présente dans le tableau des suppressions
				if ( isset($dels[$i]) )
				{
					// On vérifie si c'est une modification inline
					if (isset($adds[$k])) {
						$res = $this->execute_inline( $dels[$i], $adds[$k], FD_INLINE_DELETED, FD_INLINE_NORMAL, $k);
					}
					else {
						$res = $this->replace($this->line_format[FD_INLINE_DELETED], $dels[$i], $k);
					}
					$this->push_line($res,$k, FD_LINE_DELETED);					
					$i++;					
				}
				// Sinon si c'est un ajout
				if ( isset($adds[$k]) )
				{
					// On vérifie si c'est une modification inline
					if ( isset($dels[$i-1]) ) {
						$res = $this->execute_inline( $adds[$k], $dels[$i-1], FD_INLINE_ADDED, FD_INLINE_NORMAL, $k);			
					}
					else {
						$res = $this->replace($this->line_format[FD_INLINE_ADDED], $adds[$k], $k);
					}				
					$this->push_line($res,$k, FD_LINE_ADDED);										
				}
				else
				{
					$v = $this->replace($this->line_format[FD_INLINE_NORMAL], $v, $k);
					$this->push_line($v,$k, FD_LINE_NORMAL);
					$i++;
				}				
			}
			if (isset($dest)) {
				$this->save_file($dest);
			}
			elseif (isset($this->files['res']['name'])) {
				$this->save_file($this->files['res']['name']);
			}
		}
		private function save_file($dest)
		{
			file_put_contents(
				$dest,
				array_merge(
					(array)$this->header,
					$this->files['res']['content'],
					(array)$this->footer
				)
			);
		}
		private function replace ($src, $value = null, $line = null)
		{
			return str_replace(
				array('{value:'. $this->uid .'}','{line:'. $this->uid .'}'),
				array($value, $line),
				$src
			);
		}
		private function execute_inline($new, $old, $new_format, $old_format, $i)
		{
			$line_new = split(' ', $new);
			$line_old = split(' ', $old);
			$modif = array_diff($line_new, $line_old);
			
			$res = ' ';
			$n = $m = '';
			
			
			// Tant que nous n'avons pas parcourru toutes les entités de la ligne
			foreach($line_new as $j => $w)
			{
				// Vérifie si l'entité est présente dans le tableau des modifs
				if ( isset($modif[$j]) )
				{
					// J'utilise ici un buffer pour stocker temporairement pour ne par morceler des données d'un
					// meme type (exemple: INLINE_NORMAL) en plusieures morceaux							
					if ( strlen($n) )
					{
						$res .= $this->replace($this->line_format[$old_format], $n, $i);
						$n = '';
					}
					$m .= $w . ' ';
				}
				else
				{
					// Buffer...
					if ( strlen($m) )
					{
						$res .= $this->replace($this->line_format[$new_format], substr($m,0,-1), $i);
						$m = '';
						$n = ' ';
					}
					$n .= $w . ' ';
				}
			}
			// On vide les buffers
			if (strlen($m)) { $res .= $this->replace($this->line_format[$new_format], $m, $i); }
			elseif (strlen($n)) { $res .= $this->replace($this->line_format[$old_format], $n, $i); }
			
			return $res;
		}
		private function push_line($value, $i, $type)
		{
			$value = str_replace("\r\n", '', $value);
			$this->files['res']['content'][] = $this->replace($this->line_format[$type], $value, $i+1);
		}
	}
?>