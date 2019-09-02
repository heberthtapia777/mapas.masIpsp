<?php
    include '../conexion.php';

    $id = $_REQUEST['id'];

    // Realizar una consulta SQL
    $sql = "SELECT * FROM recinto WHERE idRecinto = $id";

    $query = $db->Execute($sql);

    $numRows = $query->recordCount();

    $row = $query->FetchRow();

    $data = new stdClass();

    $data->title   = 'ELECCIONES GENERALES 2014';
    $data->circuns = $row['circuns'];
    $data->zona    = $row['zona'];
    $data->name    = $row['name'];

    $sql = "SELECT * FROM mesa AS m, voto AS v WHERE m.idRecinto = 1 AND m.idMesa = v.idMesa ORDER BY (v.idMesa) ASC";
    $str = $db->Execute($sql);

    //$data->tbody = '<tbody>';

    while ($reg = $str->FetchRow()) {

        $data->tbody.= "<tr>";
            $data->tbody.= "<td>".$reg['number']."</td>";

            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            $data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";

            $data->tbody.= "<td>".$reg['valido']."</td>";
            $data->tbody.= "<td>".$reg['blanco']."</td>";
            $data->tbody.= "<td>".$reg['nulo']."</td>";
            $data->tbody.= "<td>".$reg['emitido']."</td>";
        $data->tbody.= "</tr>";

    }

    //$data->tbody.= '</tbody>';
    header('Content-type: application/json', true);
    echo json_encode($data);
