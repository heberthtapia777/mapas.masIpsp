<?php
    include '../conexion.php';

    // Realizar una consulta SQL
    $sql = "SELECT * FROM recinto";

    $query = $db->Execute($sql);

    $numRows = $query->recordCount();

    $geojson = array(
        'type' => 'FeatureCollection',
        'features' => array()
    );
    $c = 0;
    // Sabemos que nuestra conexión a MySQL y nuestra consulta
    // tuvieron éxito, pero ¿tenemos un resultado?
    if ($numRows === 0) {
        echo "Sin eventos.";
        exit;
    }
    while ($row = $query->FetchRow()) {
        // Se obtiene la fecha.
        //$evento_fecha = new DateTime($row['evento_fecha']);
        //$fecha = $evento_fecha->format('d-m-Y');

        $evento = array(
            'type' => 'Feature',
            "id" => $c,
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array(
                    $row['lng'],
                    $row['lat']
                )
            ),
            'properties' => array(
                '@id'            => $row['idRecinto'],
                'id'             => $row['idRecinto'],
                'circuncripcion' => $row['circuns'],
                'zona'           => $row['zona'],
                'recinto'        => $row['name'],
                'porcentaje'     => $row['porcentaje']
                //'fecha' => $fecha
            )
        );
        array_push($geojson['features'], $evento);
        $c++;
    }
    header('Content-type: application/json', true);
    echo json_encode($geojson);
