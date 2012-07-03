<?php

		echo "hello test !";

		//mkdir("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/tutu", 0777);   // fonctionne !!
		$e=symlink("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/44143.jpg","/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/XXXX.jpg");

		$jesuisla = file_exists("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/44143.jpg");

		exec(" ln -s /home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/44143.jpg /home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/XXXX.jpg");
		
//		$e=link("/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/44143.jpg","/home/vhosts/fabien/archi-strasbourg-v2/images/mini/2011-12-18/XXXX.jpg");
		
//		$z = link('a','b');
		
		
		echo "RESULTAT : " . $e . $z;
		
		echo "FICHIER EST LA ? : " . $jesuisla;

?>