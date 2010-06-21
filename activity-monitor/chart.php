<?php

include("pChart/pData.class");
include("pChart/pChart.class");

function writeBarData( $data, $graph ) {
	if ( $data != "" ) {
		echo "Plottong " . $graph . "\n";
		$dataset1 = array();
		$dataset2 = array();
		$counter = 0;
		$toggle = true;
		$data = array_reverse (  $data, TRUE );

		foreach( $data as $key => $value ) {
			if ( $toggle ) {
				$toggle = false;
			} else {
				$key = " ";
			}
			array_push( $dataset1, $key );
			array_push( $dataset2, $value );
			$counter++;
			if ( $counter > 3 ) {
				$toggle = true;
				$counter = 0;
			}
			sort( $dataset1, SORT_NUMERIC );
		}
  // Dataset definition 
  $DataSet = new pData;
  $DataSet->AddPoint($dataset2, "Serie1");
  $DataSet->AddPoint($dataset1, "XLabel");
  $DataSet->AddAllSeries();
  $DataSet->SetAbsciseLabelSerie("XLabel");
  $DataSet->RemoveSerie("XLabel");   
  $DataSet->SetSerieName("Membership","Serie1");

  // Initialise the graph
  $Test = new pChart(700,230);
  $Test->setFontProperties("Fonts/tahoma.ttf",8);
  $Test->setGraphArea(50,30,680,200);
  $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
  $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
  $Test->drawGraphArea(255,255,255,TRUE);
  $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),'SCALE_NORMAL',150,150,150,TRUE,0,2,TRUE);   
  $Test->drawGrid(4,TRUE,230,230,230,50);

  // Draw the 0 line
  $Test->setFontProperties("Fonts/tahoma.ttf",6);
  $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

  // Draw the bar graph
  $Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);

  // Finish the graph
  $Test->setFontProperties("Fonts/tahoma.ttf",8);
  $Test->drawLegend(596,150,$DataSet->GetDataDescription(),255,255,255);
  $Test->setFontProperties("Fonts/tahoma.ttf",10);
  $Test->drawTitle(50,22,"Membership by Month",50,50,50,585);
  $Test->Render("$graph-bar.png");
	}
}

function writeData( $data, $graph ) {
	if ( $data != "" ) {
		echo "Plottong " . $graph . "\n";
		$dataset1 = array();
		$dataset2 = array();
		foreach( $data as $key => $value ) {
			array_push( $dataset1, $key );
			array_push( $dataset2, $value );
		}

		$DataSet = new pData;
		$DataSet->AddPoint($dataset2,"Serie1");
		$DataSet->AddPoint($dataset1,"Serie2");
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie("Serie2");
		$Test = new pChart(380,200);
		$Test->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);
		$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);
		// Draw the pie chart
		$Test->setFontProperties("Fonts/tahoma.ttf",8);
		$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,TRUE,TRUE,50,20,5);
		$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
		$Test->Render("$graph-pie.png");
	}
}

function writeHTML( $team, $graph, $data ) {

$membership = 0;

$karma_min = NULL;
$karma_max = NULL;
$karma_max_name = "";

$karma_avg = NULL;

foreach( $data as $key => $value ) {
	$membership++;
	if ( $karma_min == NULL || $karma_min->karma > $value->karma ) {
		$karma_min = $value;
	}
	if ( $karma_max == NULL || $karma_max->karma < $value->karma ) {
		$karma_max = $value;
		$karma_max_name = $key;
	}

	if ( $karma_avg == NULL ) {
		$karma_agv = $value->karma;
	} else {
		$karma_agv = (( $value->karma + $karma_agv ) / 2 );
	}
}

$karma_max_val = $karma_max->karma;
$karma_min_val = $karma_min->karma;
$karma_max_join = date('M', $karma_max->join ) . " of " . date('Y', $karma_max->join );



$html = <<<XYZ
<html>
	<head>
		<title>LoCo Report</title>
	</head>
	<style>
body {
	background-color:      #DCDCDC;
	font-family:           "URW Gothic L", sans-serif;
	padding:               0px;
	margin:                0px;
}
.container {
	width:                 800px;
	margin-left:           auto;
	margin-right:          auto;
	background-color:      #C0C0C0;
	-moz-border-radius:    10px;
	-webkit-border-radius: 10px;
	margin-top:            30px;
	margin-bottom:         30px;
}
.content {
	padding:               20px;
}
	</style>
	<body>
		<div class = "container" >
			<div class = "content" >
				<h1>LoCo Report, $team</h1>
				<h2>Membership by Month</h2>
				<img src = "$team-bar.png" alt = "monthly chart" />

				<h2>Membership by Month</h2>
				<img src = "$team-pie.png" alt = "yearly chart" />

				<h2>Raw Stats</h2>
				There are $membership members on this team.<br />
				<b>$karma_max_name</b> has the most karma on the team, with a count
				of $karma_max_val ( compared to a minimum of $karma_min_val! ). <b>$karma_max_name</b> joined this loco in $karma_max_join.<br />
				<br />
				The average karma on the team is $karma_agv.<br />
				<br />
			</div>
		</div>
	</body>
</html>
XYZ;



$fh = fopen($graph . "-report.html", 'w');
fwrite($fh, $html);
fclose($fh);

}

$results = array();
$results2 = array();

if ($handle = opendir('./data/')) {
	while (false !== ($file = readdir($handle))) {
		$myFile = $file;
		if ( $myFile != "." && $myFile != ".." ) {
			$fh = fopen("./data/" . $myFile, 'r');
			$theData = fgets($fh);
			fclose($fh);
			if ( $theData != "" ) {
				$array = json_decode( $theData );
				foreach ( $array as $key => $value ) {
					$string1 = date('Y', $value->join );
					$string2 = date('Ym', $value->join );
					if ( ! isset( $results[$string1] ) ) {
						$results[$string1] = 0;
					}
					if ( ! isset( $results2[$string2] ) ) {
						$results2[$string2] = 0;
					}
					$results[$string1]  = $results[$string1] + 1;
					$results2[$string2] = $results2[$string2] + 1;
				}
			}
			writeHTML( $myFile, "./charts/" . $myFile, $array );
			writeBarData( $results2, "./charts/" . $myFile );
			writeData( $results, "./charts/" . $myFile );
			$results = array();
		}
	}
	closedir($handle);
}

?>
