<!DOCTYPE html>
<html lang="de">
<head>
<META CHARSET="UTF-8">
<title>Simple Show Grid</title>
<link rel="stylesheet" type="text/css" href="style/simpleShowGrid.css">
</head>
<body>
<?php 
include_once 'driver.php';
if(isset($_GET['gridpath'])){
	$xml = simplexml_load_file($_GET['gridpath']);
	$driverarray = array();
	foreach ($xml->driver as $driver){
		$tempDriver = new driver($driver->lfsname, $driver->elo);
		$tempDriver->setName($driver->name);
		$tempDriver->setRaceFinished($driver->racefinished);
		$tempDriver->setRaceStarted($driver->racestarted);
		$tempDriver->setPositionArray(explode("#",$driver->positions));
		if(isset ($_GET['finishlimit'])){
			if(intval($driver->racefinished)<intval($_GET['finishlimit'])) continue;
		}
		if(isset ($_GET['startlimit'])){
			if(intval($driver->racestarted)<intval($_GET['startlimit'])) continue;
		}
		$driverarray[]=$tempDriver;
	}
	$additionalparameter="";
	if(isset ($_GET['finishlimit'])) $additionalparameter.="&finishlimit=".($_GET['finishlimit']);
	if(isset ($_GET['startlimit'])) $additionalparameter.="&startlimit=".($_GET['startlimit']);
	
	if(isset($_GET['sortby'])){
		if($_GET['sortby']=="elo"){
			usort($driverarray, "sortByElo");
		}
		if($_GET['sortby']=="finishedraces"){
			usort($driverarray, "sortByFinishedRaces");
		}
		if($_GET['sortby']=="startedraces"){
			usort($driverarray, "sortByStartedRaces");
		}
		if($_GET['sortby']=="startedfinishedratio"){
			usort($driverarray, "sortByStartedFinishedRatio");
		}
	}
	echo "<table class=\"bigtable\"><tr><th>Name(Lfsworld)</th><th><a href=\"".$_SERVER['PHP_SELF']."?gridpath=".$_GET['gridpath']."&sortby=startedraces".$additionalparameter."\">Starts</a> <a href=\"".$_SERVER['PHP_SELF']."?gridpath=".$_GET['gridpath']."&sortby=startedfinishedratio".$additionalparameter."\"> / </a> <a href=\"".$_SERVER['PHP_SELF']."?gridpath=".$_GET['gridpath']."&sortby=finishedraces".$additionalparameter."\">Finished</a></th><th><a href=\"".$_SERVER['PHP_SELF']."?gridpath=".$_GET['gridpath']."&sortby=elo".$additionalparameter."\">Elo</></th><th>Positions</th></tr>";
	foreach ($driverarray as $pos => $driver){
		echo "<tr><td>".($pos+1)." <a href=\"http://www.lfsworld.net/?win=stats&racer=".$driver->getLfsName()."\" target=\"_blank\">".$driver->getName()."</a></td><td>".$driver->getRaceStarted()."/".$driver->getRaceFinished()."</td><td>".$driver->getElo()."</td><td class=\"p\"><table class=\"positions\"><tr>";
		foreach ($driver->getPositionArray() as $key => $val){
			echo "<td>".($key+1).".</td>";
		}
		echo "</tr><tr>";
		foreach ($driver->getPositionArray() as $key => $val){
			echo "<td>".$val."</td>";
		}
		echo "</tr></table></td></tr>";
	}
	echo "</table>";
}

function sortByElo($driver1,$driver2){
	if($driver1->getElo()==$driver2->getElo()) return 0;
	if($driver1->getElo()>$driver2->getElo()) return -1;
	else return 1;
}
function sortByFinishedRaces($driver1,$driver2){
	if($driver1->getRaceFinished()==$driver2->getRaceFinished()) return 0;
	if($driver1->getRaceFinished()>$driver2->getRaceFinished()) return -1;
	else return 1;
}
function sortByStartedRaces($driver1,$driver2){
	if($driver1->getRaceStarted()==$driver2->getRaceStarted()) return 0;
	if($driver1->getRaceStarted()>$driver2->getRaceStarted()) return -1;
	else return 1;
}
function sortByStartedFinishedRatio($driver1,$driver2){
	if(floatval($driver1->getRaceFinished()/$driver1->getRaceStarted())==floatval($driver2->getRaceFinished()/$driver2->getRaceStarted())){
		if($driver1->getRaceStarted()==$driver2->getRaceStarted()) return 0;
		if($driver1->getRaceStarted()>$driver2->getRaceStarted()) return -1;
		else return 1;
	}
	if(floatval($driver1->getRaceFinished()/$driver1->getRaceStarted())>floatval($driver2->getRaceFinished()/$driver2->getRaceStarted())) return -1;
	else return 1;
}
?>
</body>
</html>
