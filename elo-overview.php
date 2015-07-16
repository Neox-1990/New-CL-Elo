<?php include_once 'standard/constants.php';?>
<?php include 'standard/top.php'; ?>

<!-- additional head links  -->

<!-- additional head links  -->
<?php include 'standard/header.php'; ?>

<!-- mainpart -->
<h1 class="green">Driver Elo-Overview</h1>
<p>Here you see the overview of the elo points for every driver, who started at least five times in a Cityliga race since season seven. 
It is sorted by elo from high to low, but by clicking the text in the table headers, you can sort them different. 
Clicking a name in the table take you to a page with detailed information of that particular driver. 
Use you browser build in search function (ex. CTRL + F) to look for a certain driver.</p>
<?php 
include_once 'dev/driver.php';
include_once 'standard/tools.php';
set_time_limit(300);
if(isset($_GET['gridpath']) && $_GET['histpath']){
	$xml = simplexml_load_file($_GET['gridpath']);
	$xml2=simplexml_load_file($_GET['histpath']);
	$driverarray = array();
	foreach ($xml->driver as $driver){
		$elo=$driver->elo;
		if($_GET['sortby']=="highestelo"){
			$xpath='//driver[@lfsname="'.$driver->lfsname.'"]';
			$search=$xml2->xpath($xpath);
			$highestElo=$search[0];
			foreach ($search as $elo){
				if(intval($elo)>intval($highestElo)) $highestElo=$elo;
			}
			$elo=$highestElo;
		}
		$tempDriver = new driver($driver->lfsname, $elo);		
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
		if($_GET['sortby']=="elo" || $_GET['sortby']=="highestelo"){
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
	echo "<table id=\"overview\"><tr>
				<th id=\"poshead\">pos.</th>
				<th id=\"namehead\">name<a href=\"#name\">*</a></th>
				<th id=\"elohead\"><a href=\"".$_SERVER['PHP_SELF'].getGetString("sortby", "elo")."\" title=\"sort by elorating\">elo-rating</a></th>
				<th id=\"rhead\"><a href=\"".$_SERVER['PHP_SELF'].getGetString("sortby", "startedraces")."\" title=\"sort by races started\">race<br>started</th>
				<th id=\"fhead\"><a href=\"".$_SERVER['PHP_SELF'].getGetString("sortby", "finishedraces")."\" title=\"sort by races finished\">race<br>finished</th>
				<th id=\"frhead\"><a href=\"".$_SERVER['PHP_SELF'].getGetString("sortby", "startedfinishedratio")."\" title=\"sort by finished - started ratio\">finished<p>────────</p>started</th>
				</tr>";
	foreach ($driverarray as $pos => $driver){
		echo "<tr>
			<td class=\"poscell\">".($pos+1).".</td>
			<td class=\"namecell\"><a href=\"showSingleDriver.php?gridpath=".GRID_LINK."&histpath=".HISTORY_LINK."&lfsname=".$driver->getLfsName()."\">".$driver->getName()."</a></td>
			<td class=\"elocell\">".$driver->getElo()."</td>
			<td class=\"rcell\">".$driver->getRaceStarted()."</td>
			<td class=\"fcell\">".$driver->getRaceFinished()."</td>
			<td class=\"frcell\">".(100*round(floatval($driver->getRaceFinished())/floatval($driver->getRaceStarted()),2))."%</td>
			</tr>";
	}
	echo "</table>";
	
}else{
	echo "<p>Dont play with the GET-parameters!!!</p>";
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
<h2 class="green"><a name="name">name*</a></h2>
<p>If you find yourself with the wrong teamtag or name in general in the table, feel free to contact me. 
I will gladly replace the name with the one you like to have.</p>
<!-- mainpart -->
<?php $footerLink="greentext";?>
<?php include 'standard/bottom.php'; ?>