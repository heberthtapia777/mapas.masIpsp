<?php
    ini_set('max_execution_time', 2000);
   include 'conexion.php';

   $data = json_decode($data);

    $data->json[1] = '{"type":"FeatureCollection","features":[';

    for ($i=1; $i <= 641 ; $i++) {

        $data->json[1] = $data->json[1].'{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[';

        $strQuery = "SELECT * FROM zona AS z, coordenadas AS c WHERE z.idZona = c.idZona AND c.idZona = '$i' ";

        $srtQ = $db->Execute($strQuery);

        $recordCount = $srtQ->recordCount();

        $c = 0;

        while ( $row = $srtQ->FetchRow() ) {

            if ($row['lat'] != 0) {
                $data->json[1] = $data->json[1].'['.$row['lat'].','.$row['lon'].'],';

                $name  = $row['name'];

            }
        }
        if ($i != 641) {
            $data->json[1] = $data->json[1].']]},"name": "'.$name.'"},';
        }else{
            $data->json[1] = $data->json[1].']]},"name": "'.$name.'"}';
        }

    }

    $data->json[1] = $data->json[1]."]}";

    $string = $data->json[1];

    echo $string = stripslashes($string);



    /*$json_string = json_encode($string);
    $file = 'coordenadas1.geojson';
    file_put_contents($file, $json_string);*/

 ?>
