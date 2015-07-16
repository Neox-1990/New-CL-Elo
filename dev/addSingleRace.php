<?php
include 'grid.php';
set_time_limit(60);
$racelink=$_POST['racepath'];
$factor=$_POST['factor'];
$gridpath=$_POST['gridpath'];
$histpath=$_POST['histpath'];
$finishermode=false;
if($_POST['finisher']==1) $finishermode=true;
$grid1 = new grid($gridpath, true, $histpath, true);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $racelink);
curl_setopt($ch, CURLOPT_REFERER, 'http://cl.racemore.de/mpres/league/CL/');
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
$agent="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36";
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$date = getRaceDate($output);
$startersAndFinishers = explode(" of ", getFinishedRacers($output));
$starter=$startersAndFinishers[1];
$finisher=$startersAndFinishers[0];
$resultArray = getResultArray($output, $starter);
$grid1->addRace($resultArray, $finisher, $factor, $date, $finishermode);
$grid1->saveGrid();
echo "done";

//Holt datum aus DOM
function getRaceDate($htmlString){
	$pos = strpos($htmlString, '<td class="taba">Time</td>');
	$timeString= substr($htmlString,$pos+53,10);
	return $timeString;
}

//Holt Anzahl der Ankommer aus DOM
function getFinishedRacers($htmlString){
	$pos = strpos($htmlString, 'cars finished the race');
	$finishedString= substr($htmlString, $pos-9,8);
	return $finishedString;
}

//Holt Ergebnis Array aus DOM mit den urlencoded LFSnamen
function getResultArray($htmlString, $finished){
	$offset=0;
	$result = array();
	for($i=0;$i<$finished;$i++){
		$pos = strpos($htmlString,'http://lfsworld.com/?win=stats&amp;racer',$offset);
		$driverString = substr($htmlString, $pos+41, 64);
		$driverString = explode('">', $driverString)[0];
		$result[$i]=$driverString;
		$offset = $pos+100;
	}
	return $result;
}

?>