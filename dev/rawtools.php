<!DOCTYPE html>
<html lang="de">
<head>
<META CHARSET="UTF-8">
<title>rawtooler</title>
<script type="text/javascript" src="jQuery/jquery-2.1.1.js"></script>
<script type="text/javascript" src="jQuery/jquery-ui.js"></script>
<?php
include 'grid.php';

if (!isset($_GET['mode'])){
	?>
	</head>
	<body>
	Missing mode parameter!
	<?php 
}else{
	if($_GET['mode']=="insertlinklist"){//Einfügen aus einer Textdatei mit Faktoren als Ajay Funktion damit man sieht, wie weit es ist
		if(!isset($_GET['finishmode'])||!isset($_GET['gridlink'])||!isset($_GET['historylink'])||!isset($_GET['filelink'])){
			?>
			</head>
			<body>
			Missing parameter for insertlinklist-mode!
			<?php 
		}else{
			$gridlink=$_GET['gridlink'];
			$histlink=$_GET['historylink'];
			$filelink=$_GET['filelink'];
			$finishmode=$_GET['finishmode'];
			if(isset($_GET['new'])){
				new grid($gridlink, false, $histlink, false);
				//echo "created new grid and history<br>\n";
			}
			$list=explode("\n",file_get_contents($filelink));
			foreach ($list as $key=>$entry){
				$list[$key]=explode("\t", $entry);
			}
			echo '<script type="text/javascript">';
			echo 'var gridlink="'.$gridlink.'";';
			echo 'var histlink="'.$histlink.'";';
			echo 'var finishmode="'.$finishmode.'";';
			$linklisttext='var linklist = [';
			$factorlisttext='var factorlist = [';
			foreach ($list as $entry){
				$linklisttext.='"'.$entry[0].'",'."\n";
				$factorlisttext.='"'.($entry[1]*1).'",'."\n";
			}
			$linklisttext=substr($linklisttext, 0,strlen($linklisttext)-1).'];'."\n";
			$factorlisttext=substr($factorlisttext, 0,strlen($factorlisttext)-1).'];'."\n";
			echo $linklisttext;
			echo $factorlisttext;
			?>
			$(document).ready(function(){
				
				console.log("doAction is true "+linklist.length);
				$("#info").text("Races will be added.Progress 0 / "+(linklist.length)+". Current:");
				addRace(0);		
				
			});

			function addRace(num){
			$("#info").text("Races will be added.Progress "+(num)+" / "+linklist.length+". Current:"+linklist[num]);
			var request = $.ajax({
				  url: "addSingleRace.php",
				  method: "POST",
				  data: { racepath : linklist[num], factor : factorlist[num], gridpath : gridlink, histpath : histlink, finisher : finishmode}
				});
				 
				request.done(function( msg ) {
				  console.log("Bild wurde gespeichert");
				  $("#info").text("Races will be added.Progress "+(num+1)+" / "+linklist.length+". Current:");
				  if(num+1<linklist.length) addRace(num+1);
				});
				return;
			}
			<?php 
			echo '</script></head><body><p id="info">Info</p>';
		}
	}elseif($_GET['mode']=="rename"){//Zum Umbenennen der Fahrer im Grid
		if(!isset($_GET['gridlink'])||!isset($_GET['filelink'])){
			?>
								</head>
								<body>
								Missing parameter for rename-mode!
								<?php
		}else{
			$grid=file_get_contents($_GET['gridlink']);
			$name=explode("\n",file_get_contents($_GET['filelink']));
			foreach ($name as $key => $entry){
				$name[$key]=explode("\t", $entry);
			}
			foreach ($name as $entry){
				$grid=str_replace("<name>$entry[0]</name>", "<name>$entry[1]</name>", $grid);
			}
			if(file_put_contents($_GET['gridlink'], $grid)) echo "</head><body>Gridfile succecfully renamed";
			else echo "</head><body>Gridfile couldnt be renamed";
		}
	}elseif($_GET['mode']=="eventize"){//Eventize der History
		if(!isset($_GET['historylink'])||!isset($_GET['filelink'])){
			?>
					</head>
					<body>
					Missing parameter for eventize-mode!
					<?php 
				}else{
					echo "</script></head><body>";
					$xml=simplexml_load_file($_GET['historylink']);
					$xmlneu=simplexml_load_file($_GET['historylink']);
					$nodecount=$xml->count();
					$deleted=0;
					for($i=0;$i<$nodecount;$i++){
						if(isset($xml->race[$i+1])){
							$currentnode=$xml->race[$i];
							$nextnode=$xml->race[$i+1];
							if(strval($currentnode['date']) == strval($nextnode['date'])){
								unset($xmlneu->race[$i-$deleted]);
								$deleted++;
							} 
						}
					}
					$nodecount2=$xmlneu->count();
					$xmlneu->saveXML($_GET['filelink']);
					echo "From $nodecount races reduced to $nodecount2 races.";
				}
	}
}
?>
</body>
</html>