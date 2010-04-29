<?php

include("pChart/pData.class");
include("pChart/pChart.class");

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
		$Test->Render("$graph.png");
	}
}

$results = array();

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
//					$string = date('Ym', $value);
					$string = date('Y', $value );
					if ( ! isset( $results[$string] ) ) {
						$results[$string] = 0;
					}
					$results[$string] = $results[$string] + 1;
				}
			}
			writeData( $results, "./charts/" . $myFile );
			$results = array();
		}
	}
	closedir($handle);
}

?>
