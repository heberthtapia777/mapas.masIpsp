<?php
	ini_set('max_execution_time', 3000);

	include 'conexion.php';
	$xml = simplexml_load_file("doc.xml");

	//echo $xml->name;

	foreach($xml->Placemark as $nodo){
		echo $nodo->name.'<br>';
		echo $nodo->description.'<br>';

		$coor = $nodo->MultiGeometry->Polygon->outerBoundaryIs->LinearRing->coordinates;

		$coor = strval($coor);

		$coor = str_replace("0 ", "", $coor);

		$arreglo = explode(",", $coor);

		echo sizeof($arreglo);

		//print_r($arreglo);

		//var_dump($arreglo);

		$sql = "INSERT INTO zona (name)	VALUES('$nodo->name')";

		$srt = $db->Execute($sql);

		$lastId = $db->insert_Id();

		$cont = sizeof($arreglo);

		//foreach ($arreglo as $key => $value) {
		for ($i=0; $i < $cont ; $i=$i+2) {
			$j = $i+1;
			echo $arreglo[$i].'<==>'.$arreglo[$j].'<br>';

			$sql = "INSERT INTO coordenadas (idZona, lat, lon) VALUES('$lastId', '$arreglo[$i]', '$arreglo[$j]')";
			//$i++;
			$srt = $db->Execute($sql);
		}
	}

	//print_r($arreglo);



?>
