<?php include 'standard/constants.php';?>
<?php include 'standard/top.php'; ?>

<!-- additional head links  -->
<script src="<?php echo D3_LINK;?>"></script>
<script src="<?php echo JQUERY_LINK;?>"></script>
<script src="js/custom/showSingleDriverFX.js"></script>
<!-- additional head links  -->
<?php include 'standard/header.php'; ?>

<!-- mainpart -->
<?php 
include_once 'dev/driver.php';
include_once 'standard/tools.php';

if(isset($_GET['gridpath']) && isset($_GET['lfsname']) && isset($_GET['histpath'])){
	$xml = simplexml_load_file($_GET['gridpath']);
	$lfsname=urlencode($_GET['lfsname']);
	$xpath="/grid/driver[@lfsname=\"".strval($lfsname)."\"]";
	$tempdriver = $xml->xpath($xpath);
	if($tempdriver!=false) {
		$tempdriver = $tempdriver[0];
		$driver = new driver($lfsname, $tempdriver->elo);
		$driver->setName($tempdriver->name);
		$driver->setPositionArray(explode("#",$tempdriver->positions));
		$driver->setRaceFinished($tempdriver->racefinished);
		$driver->setRaceStarted($tempdriver->racestarted);
		
		$xml=simplexml_load_file($_GET['histpath']);
		$xpath='//driver[@lfsname="'.$driver->getLfsName().'"]';
		$xpath2='//driver[@lfsname="'.$driver->getLfsName().'"]/..';
		$search=$xml->xpath($xpath);
		$search2=$xml->xpath($xpath2);
		$highestElo=$search[0];
		$lowestElo=$search[0];
		$index=0;$indexHighest=0;$indexLowest=0;
		$eloHistoryArray = array();
		foreach ($search as $elo){
			if(intval($elo)>intval($highestElo)){
				$highestElo=$elo;
				$indexHighest=$index;
			}
			if(intval($elo)<intval($lowestElo)){
				$lowestElo=$elo;
				$indexLowest=$index;
			}
			$date=strval($search2[$index]['date']);
			$eloHistoryArray[$date]=floatval($elo);
			$index++;
		}
		$highestDate=$search2[$indexHighest]['date'];
		$lowestDate=$search2[$indexLowest]['date'];
		echo '<table id="driverinfo">
				<tr>
					<td colspan="3" id="infoheader">driver information<img src="img/HistogrammIcon.png" onClick="activateHisto()"><img src="img/DonutChartIcon.png" onClick="activateDonut()"></td>
				</tr>
				<tr>
					<td class="top">name (lfsworld):</td>
					<td class="info">'.$driver->getName().'<a target="_blank" href="http://www.lfsworld.net/?win=stats&racer='.$driver->getLfsName().'"><img src="img/stats.gif"</a></td>
					<td id="positions" rowspan="3"><noscript>unfortunatly you dont allow JavaScript, so i can not show you this fancy statistics, sorry</noscript></td>
				</tr>
				<tr>
					<td class="top">current elo:<br>highest elo:<br>lowest elo:</td>
					<td class="info">'.$driver->getElo().' pts<p title="reached after the race on '.$highestDate.'">'.$highestElo.' pts</p><p title="reached after the race on '.$lowestDate.'">'.$lowestElo.' pts</p></td>
				</tr>
				<tr>
					<td class="top">races started:<br>races finished:<br>finish ratio:</td>
					<td class="info">'.$driver->getRaceStarted().' races<br>'.$driver->getRaceFinished().' races<br>'.(100*round(floatval($driver->getRaceFinished())/floatval($driver->getRaceStarted()),3)).'%</td>
				</tr>
				<tr>
					<td colspan="3" id="history"><noscript>unfortunatly you dont allow JavaScript, so i can not show you this fancy statistics, sorry</noscript></td>
				</tr>
				</table>';
		$varArray = array();
		foreach ($driver->getPositionArray() as $key => $val){
			$varArray[$key+1] = $val;
		}
		$varArray = makeJavaScriptArray("positions", $varArray, true, true);
		echo "<script>\n$varArray</script>\n";
		echo "<script src=\"js/custom/positionsHistogramm.js\"></script>";
		echo "<script src=\"js/custom/positionsDonut.js\"></script>";
		$lastelo=0;
		$eloHistoryArray2 = array();
		foreach ($eloHistoryArray as $date => $elo){
			if($lastelo!=$elo){
				$eloHistoryArray2[$date]=$elo;
				$lastelo=$elo;
			}
		}
		$historyJSstring = "var historyArray = new Array(\n";
		$counter=0;
		foreach ($eloHistoryArray2 as $date => $elo){
			$historyJSstring .= "{date:\"$date\", elo:\"$elo\"}";
			if($counter+1!=sizeof($eloHistoryArray2))$historyJSstring .= ",\n";
			$counter++;
		}
		$historyJSstring .= ");";
		echo "<script>$historyJSstring\n</script>";
		echo "<script src=\"js/custom/historyGraph.js\"></script>";
	}else{
		echo "<p>Error: unknown driver.</p>";
	}
}else{
	echo "<p>Dont play with the GET-parameters!!!</p>";
}
/**
function sortDateArray($a, $b){
	$a=explode(".", $a);
	$b=explode(".", $b);
	if(intval($a[2])==intval($b[2])){
		if(intval($a[1])==intval($b[1])){
			if(intval($a[0])==intval($b[0])) return 0;
			elseif(intval($a[0])<intval($b[0])) return -1;
			else 1;
		}elseif(intval($a[1])<intval($b[1])) return -1;
		else return 1;
	}elseif(intval($a[2])<intval($b[2])) return -1;
	else return 1;
}*/
?>
<!-- mainpart -->
<?php $footerLink="greentext";?>
<?php include 'standard/bottom.php'; ?>