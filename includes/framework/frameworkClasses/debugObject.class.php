<?php
// classe d'outils pour le debuggage de site php
// Dorer Laurent 2008

// historique des versions
// version 1.0

class debugObject extends config
{
	
	var $microTime;
	
	function __construct()
	{
		parent::__construct();
		$this->microTime=0;
	}

	public function startChrono()
	{
		$this->microstart=microtime(true);
	}
	
	public function stopChrono()
	{
		$fin_compte=microtime(true);
		$duree=($fin_compte-$this->microstart);
		return $duree;
	}
	
	public function getJSProperties($params=array())
	{
		if(isset($params['object']))
		{
			echo "<script  >";
			echo "wFrameWorkDebug = window.open();";
			echo "wFrameWorkDebug.document.write('Properties : <br>');";
			echo "var objDebug = ".$params['object'].";";
			echo "for(i in objDebug)";
			echo "{";
			echo "	wFrameWorkDebug.document.writeln(i+'<br>');";
			echo "}";
			echo "</script>";
		}
	}
	
	
	
}
?>