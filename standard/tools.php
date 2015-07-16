<?php
function getGetString($getkey,$getparameter){
	$result="?";
	foreach ($_GET as $key => $parameter){
		if($key==$getkey && $getkey!="") $result.=$key."=".$getparameter."&";
		else $result.=$key."=".$parameter."&";
	}
	if(!isset($_GET[$getkey]) && ""!=$getkey) $result.=$getkey."=".$getparameter;
	return $result;
}

function makeJavaScriptArray($varName, $varArray, $numericKeys, $numericVals){
	$result = "var $varName = [];\n";
	foreach ($varArray as $key => $val){
		$index;
		if ($numericKeys) $index=$key;
		else $index="\"$key\"";
		$value;
		if($numericVals) $value=$val;
		else $value="\"$val\"";
		$result .= "\t$varName"."[$index] = $value;\n";
	}
	return $result;
}