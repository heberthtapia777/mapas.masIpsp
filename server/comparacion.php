<?php
    include '../conexion.php';

    $id = $_REQUEST[id];

    //$idElec = 3;

    // Realizar una consulta SQL
    $sql = "SELECT r.name, re.idElecciones, r.porcentaje, r.idRecinto, e.name AS eleccion
            FROM recinto AS r, recintoElec AS re, elecciones AS e
            WHERE r.idRecinto = re.idRecinto
            AND e.idElecciones = re.idElecciones
            AND r.name LIKE (
            SELECT name
            FROM recinto
            WHERE idRecinto = $id) ORDER BY (e.name)";

    $query = $db->Execute($sql);

    $numRows = $query->recordCount();

    $data = json_decode($data);

    $c = 0;
    $sum = 0;

    while ($row = $query->FetchRow()) {
        $data->por[$c] = $row['porcentaje'];
        $data->elec[$c] = $row['idElecciones'];

        $sum = $sum + $data->por[$c];
        $c++;
    }
    $pro = ($sum / $numRows);

    $data->media = round($pro, 2);

    $data->num = $numRows;

    header('Content-type: application/json', true);
    echo json_encode($data);

?>
