<?php

/*
 * Objekt, welches Fahrer hält und Rennen zum Berechnen der Elo dieser Fahrer verwaltet
 * Außerdem sind auch hier die Funktionen zum speichern der Grid-Fahrer.xml und History.xml
 * Alles weitere Arbeitet dann eigentlich auf die Daten (xml) die von Grid produziert werden.
 * Ein Beispiel wie Grid verwendet werden kann sieht man in addSingleRace.php, was eine AJAX Funktion war
 * die ich zum erstellen des grundsätzlichen Datengrundsatzes von CL07-20 benutzt habe, wo ich einfach nur
 * alle MPres Rennlinks mit den entprechenden Faktoren durchgegangen bin
 * Das Umbennen hab ich in rawtools mit umgesetzt, wo es auch funktionen gibt um mehrfache Datums 
 * in der History zu beseitigen, so dass da nur noch der jeweilige letzte Datensatz des Datums da steht
 */

include_once 'driver.php';

class grid{
	//Array mit Fahrern
	private $drivers;
	//Pfad wo Griddatei.xml gespeichert wird || In Griddatei.xml stehen die Fahrer mit ihren aktuellen Elo und Ankommer Werten
	private $gridSavePath;
	//Pfad wo Historydatei.xml gespeichert wird || In Historydatei.xml werden pro Rennen alle bis dahin bekannten Fahrer mit ihrem Elowert gespeichert zusammen mit dem Datum des Rennens
	private $historySavePath;
	
	/*Konstruktor 
	 * $gridSavePath = Wo soll grid gespeichert werden incl dateiname.xml, 
	 * $loadGrid = soll Grid geladen werden (bool), 
	 * $historyDavePath = Wo soll history gespeichert werden incl. dateiname.xml, 
	 * $loadHistory = soll History geladen werden (bool)
	 */
	function __construct($gridSavePath,$loadGrid,$historySavePath,$loadHistory){
		$this->gridSavePath=$gridSavePath;
		$this->historySavePath=$historySavePath;
		$this->drivers = array();//leeres Grid anlegen, und gegebenen falls laden
		if($loadGrid){
			$this->loadGrid($gridSavePath);
		}
		$xml = simplexml_load_string("<grid></grid>");//grid XML anlegen
		$xml->saveXML($this->gridSavePath);//und schon mal speichern
		if(!$loadHistory){ //Falls neue History angelegt werden soll, schon mal XML anlegen und speichern
			$xml = simplexml_load_string("<history></history>");
			$xml->saveXML($this->historySavePath);
		}
	}
	
	/*Hauptfunktion wo ein Rennen eingepflegt wird: 
	 * $resultArray = Array mit den Fahrern (urlencode LFSname) in der Ankommerreihenfolge (beginnend bei 0), 
	 * $finished = anzahl der Fahrer, die nicht ausgefallen sind, 
	 * $factor = Faktor des Rennens, 
	 * $date = Datum des Rennens DD.MM.YYYY, 
	 * $finishermode = bei 0 wird für alle Fahrer der Elowert berechnet, bei 1 nur für die Ankommer*/
	public function addRace($resultArray,$finished,$factor,$date,$finisherMode){
		$eloMed = 0;
		$driverInDriversCount = 0;
		$finisher = sizeof($resultArray);//Finisher sind zunächst erst einmal alle Fahrer im Array
		if($finisherMode) $finisher=$finished;//Falls anderer Modus wird der Wert überschrieben
		foreach ($resultArray as $driver){//Ersteinmal zählen, wie viele Fahrer aus dem Ergebnis bereits im Grid sind
			if($this->isDriverInDrivers($driver)) $driverInDriversCount++;
		}
		if($driverInDriversCount==0){//Falls alles neue Fahrer sind, werden sie mit 1000 Elo initial eingepfegt
			foreach ($resultArray as $driver){
				$this->addDriver($driver, 1000);
			}
		}
		$eloMed = $this->getEloMed($resultArray);//Bestimmen des aktuellen Elomittelwert ohne die neuen Fahrer
		foreach ($resultArray as $driver){//Den neuen Fahrern diesen Wert zuweisen
			if(!isset($this->drivers[$driver])){
				$this->addDriver($driver, $eloMed);
			}
		}
		$eloMed = $this->getEloMed($resultArray);//Noch einmal den Elomittelwert bestimmen
		foreach ($resultArray as $pos => $driver){//Für jeden Fahrer die entsprechenen Ergebnisse einpflegen
			$this->drivers[$driver]->addPositionArray($pos+1);
			$this->drivers[$driver]->addRaceStarted();
			if($pos<$finished){
				$this->drivers[$driver]->addRaceFinished();
			}
			if($pos<$finisher){
				$this->drivers[$driver]->setElo($factor, $finisher, $pos+1, $eloMed);
			}
		}
		$this->saveHistory($date);//Und abschließend Speichern
	}
	
	/*
	 * Funktion zum speichern des Grids.
	 * Aus den Positionsarray wird eine Reihe gemacht
	 * Anschließend wird alles in den Elementen gespeichert
	 * Extra Publicfunktion, falls gleich mehrere Rennen auf einmal eingefügt werden sollen
	 */
	public function saveGrid(){
		$xml = simplexml_load_file($this->gridSavePath);
		foreach ($this->drivers as $driver){
			$positionsArray="".$driver->getPositionArray()[1];
			for($i=2;$i<=40;$i++){
				$positionsArray.="#".$driver->getPositionArray()[$i];
			}
			$driverElement=$xml->addChild("driver");
			$driverElement->addAttribute("lfsname",$driver->getLfsName());
			$driverElement->addChild("name",$driver->getName());
			$driverElement->addChild("lfsname",$driver->getLfsName());
			$driverElement->addChild("racestarted",$driver->getRaceStarted());
			$driverElement->addChild("racefinished",$driver->getRaceFinished());
			$driverElement->addChild("positions",$positionsArray);
			$driverElement->addChild("elo",$driver->getElo());
		}
		$xml->saveXML($this->gridSavePath);
	}
	
	/*
	 * depricated Funktion, die ich mal zum umbenenen hatte
	 */
	public function nameSubstitution($nameListPath){
		$names=explode("\n", file_get_contents($nameListPath));
		foreach($names as $key => $line){
			$line = explode("\t", $line);
			$names[$line[0]]=$line[1];
		}
		foreach ($this->drivers as $driver){
			if(isset($names[$driver->getLfsName()])){
				$driver->setName($names[$driver->getLfsName()]);
			}else{
				$driver->setName($driver->getLfsName()."(unknown)");
			}
		}
	}
	
	/*
	 * Läd Grid aus xml und speichert jeden einzelnen Fahrer in $drivers Array
	 */
	private function loadGrid($path){
		$xml=simplexml_load_file($path);
		foreach ($xml->driver as $driver){
			$lfsname=$driver->lfsname;
			$name=$driver->name;
			$racestarted=$driver->racestarted;
			$racefinished=$driver->racefinished;
			$elo=$driver->elo;
			$positionString=explode("#",$driver->positions);
			$positions=array();
			foreach ($positionString as $key => $val){
				$positions[$key+1]=$val+0;
			}
			$newDriver = new driver($lfsname, $elo);
			$newDriver->setName($name);
			$newDriver->setPositionArray($positions);
			$newDriver->setRaceFinished($racefinished);
			$newDriver->setRaceStarted($racestarted);
			$this->drivers[strval($lfsname)]=$newDriver;
		}
	}
	
	/*
	 * Prüft ob ein LFSworld nick (urlencoded) bereits in drivers ist
	 */
	private function isDriverInDrivers($lfsworld){
		$result=false;
		foreach ($this->drivers as $driver){
			if ($driver->getLfsName()==$lfsworld){
				$result=true;
				break;
			}
		}
		return $result;
	}
	/*
	 * Neuen Fahrer mit entsprechenden Elo in $drivers einfügen
	 */
	private function addDriver($lfsworld,$elo){
		$newDriver = new driver($lfsworld, $elo+0);
		$this->drivers[$lfsworld]=$newDriver;
	}
	/*
	 * Eloschnitt in resultArray berechnen
	 */
	private function getEloMed($resultArray){
		$result=0;
		$counter=0;
		foreach ($resultArray as $driver){
			if(isset($this->drivers[$driver])){
				$result+=$this->drivers[$driver]->getElo();
				$counter++;
			}
		}
		if($counter==0) return 0;
		else return floatval($result/$counter);
	}
	/*
	 * Hängt alle bekannten Fahrer in History hinten an
	 */
	private function saveHistory($date){
		$xml = simplexml_load_file($this->historySavePath);
		$race = $xml->addChild("race");
		$race->addAttribute("date",$date);
		foreach ($this->drivers as $driver){
			$driverElement=$race->addChild("driver",$driver->getElo());
			$driverElement->addAttribute("lfsname",$driver->getLfsName());
		}
		$xml->saveXML($this->historySavePath);
	}
}