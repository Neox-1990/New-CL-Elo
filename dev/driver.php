<?php
/*
 * Klasse die einen Fahrer repräsentiert und dessen Attribute verwaltet
 */
class driver{
	private $name;
	private $lfsname;
	private $raceStarted;
	private $raceFinished;
	private $positionarray;
	private $elo;
	
	//Konstruktor einfach gehalten
	function __construct($lfsname,$elo){
		$this->name=$lfsname;
		$this->lfsname=$lfsname;
		$this->raceStarted=0;
		$this->raceFinished=0;
		$this->positionarray=$this->createPositionArray();
		$this->elo=floatval($elo);
	}
	
	public function setName($name){
		$this->name=$name;
	}
	public function getName(){
		return $this->name;
	}
	
	public function getLfsName(){
		return $this->lfsname;
	}
	
	public function setRaceStarted($n){
		$this->raceStarted=$n;
	}
	public function addRaceStarted(){
		$this->raceStarted+=1;
	}
	public function getRaceStarted(){
		return intval($this->raceStarted);
	}
	
	public function setRaceFinished($n){
		$this->raceFinished=$n;
	}
	public function addRaceFinished(){
		$this->raceFinished+=1;
	}
	public function getRaceFinished(){
		return intval($this->raceFinished);
	}
	
	public function setPositionArray($pArray){
		$this->positionarray=$pArray;
	}
	public function addPositionArray($position){
		$this->positionarray[$position]++;
	}
	public function getPositionArray(){
		return $this->positionarray;
	}
	
	//$x = Rennfaktor, $n = Gesamtzahl der Fahrer, $P = Position des Fahrers, $R_med = Durchschnittselo des Feldes
	public function setElo($x, $n, $P, $R_med){
		$R_new = floatval($this->elo + ((30*$x*($n-0.5))/$n)*((($n-$P)/($n-1))-(1/(1+pow(10,(($R_med-$this->elo)/400))))));
		$this->elo = floatval(round($R_new,2));
	}
	public function getElo(){
		return floatval(round($this->elo,2));
	}
	
	private function createPositionArray(){
		$pArray = array();
		for($i=1;$i<=40;$i++){
			$pArray[$i]=0;
		}
		return $pArray;
	}
}