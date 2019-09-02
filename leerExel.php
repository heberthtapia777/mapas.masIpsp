<?php
	include 'conexion.php';
	require 'Classes/PHPExcel/IOFactory.php';

	$nombreArchivo = 'recintos/001 ESC IND PEDRO DOMINGO MURILLO.xlsx';

	$objPHPExel = PHPEXCEL_IOFactory::load($nombreArchivo);

	//$objPHPExel->setActiveSheetIndex(0);


	echo $numRows = $objPHPExel->setActiveSheetIndex(0)->getHighestRow();;
	$numRows = $numRows - 1;

	//echo '<table><tr><td>circunscripcion</td><td>nombre</td><td>latitud</td><td>longitud</td></tr></table>';

	/*for ($i=4; $i <= $numRows ; $i++) {

		$circunscripcion = $objPHPExel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$zona            = $objPHPExel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$recinto         = $objPHPExel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

		$recinto1		 = str_replace(".", "", $recinto);

		$latitud         = $objPHPExel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$longitud        = $objPHPExel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

		$porcentaje      = $objPHPExel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();

		$sql = "INSERT INTO recinto (circuns, zona, name, lat, lng, porcentaje) ";
		$sql.= "VALUES ('$circunscripcion', '$zona', '$recinto1', '$latitud', '$longitud', '$porcentaje')";

		$query = $db->Execute($sql);

	}
*/

	for ($i=4; $i <= $numRows ; $i++) {

		$numMesa   = $objPHPExel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$partido1  = $objPHPExel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$partido1p = $objPHPExel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

		$partido2  = $objPHPExel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$partido2p = $objPHPExel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

		$partido3  = $objPHPExel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$partido3p = $objPHPExel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

		$partido4  = $objPHPExel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
		$partido4p = $objPHPExel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();

		$partido5  = $objPHPExel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
		$partido5p = $objPHPExel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();

		$valido    = $objPHPExel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();

		$blanco    = $objPHPExel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
		$nulo     = $objPHPExel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
		$emitido   = $objPHPExel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
		$insHab    = $objPHPExel->getActiveSheet()->getCell('R'.$i)->getCalculatedValue();

		if ($numMesa == '') {
			exit();
		}

		$rec     = "SELECT * FROM recinto WHERE name = 'ESC IND PEDRO DOMINGO MURILLO' ";
		$str     = $db->Execute($rec);
		$num 	 = $str->recordCount();
		$row     = $str->FetchRow();

		if($num>0){
			$sql = "INSERT INTO mesa (idRecinto, number, valido, blanco, nulo, emitido, inscritosHab) VALUES ('".$row['idRecinto']."', '$numMesa', '$valido', '$blanco', '$nulo', '$emitido', '$insHab') ";
			$query = $db->Execute($sql);
			$lastId = $db->insert_Id();

			$srt = "INSERT INTO voto (idMesa, idPartido, cantidad, porcentaje) ";
			$srt.= "VALUES ('$lastId', '1', '$partido1', '$partido1p' )";

			$qu  = $db->Execute($srt);

			$srt = "INSERT INTO voto (idMesa, idPartido, cantidad, porcentaje) ";
			$srt.= "VALUES ('$lastId', '2', '$partido2', '$partido2p' )";

			$qu  = $db->Execute($srt);

			$srt = "INSERT INTO voto (idMesa, idPartido, cantidad, porcentaje) ";
			$srt.= "VALUES ('$lastId', '3', '$partido3', '$partido3p' )";

			$qu  = $db->Execute($srt);

			$srt = "INSERT INTO voto (idMesa, idPartido, cantidad, porcentaje) ";
			$srt.= "VALUES ('$lastId', '4', '$partido4', '$partido4p' )";

			$qu  = $db->Execute($srt);

			$srt = "INSERT INTO voto (idMesa, idPartido, cantidad, porcentaje) ";
			$srt.= "VALUES ('$lastId', '5', '$partido5', '$partido5p' )";

			$qu  = $db->Execute($srt);

		}


	}
?>
