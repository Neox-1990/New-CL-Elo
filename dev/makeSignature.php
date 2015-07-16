<?php

/*
 * Simple as Fuck
 */
//Header("Content-Type: image/png");
if(isset($_GET['gridpath'])){
	$xml = simplexml_load_file($_GET['gridpath']);
	foreach($xml->driver as $driver){
		$elo=$driver->elo;
		$starts=$driver->racestarted;
		$finished=$driver->racefinished;
		$ratio=floatval(round(floatval($finished)/floatval($starts),3)*100)." %";
		$name="../sig/".urldecode($driver->lfsname).".png";
		$img = imagecreatefrompng("signature-default.png");
		$black=imagecolorallocate($img, 0, 0, 0);
		imagettftext($img, 13, 0, 198, 18, $black, 'arialbd.ttf', $elo);
		imagettftext($img, 13, 0, 300, 18, $black, 'arialbd.ttf', $finished."/".$starts);
		imagettftext($img, 13, 0, 411, 18, $black, 'arialbd.ttf', $ratio);
		imagepng($img,$name);
		//imagepng($img);
		imagedestroy($img);
	}
	echo "done";
}else{
	echo "fehlender gridpath";
}

?>