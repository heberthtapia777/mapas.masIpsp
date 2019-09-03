<?php
    include '../conexion.php';

    $idRecinto = $_REQUEST['id'];
    /** por el momento id del recinto 1 */
    $idRecinto = 1;

    $idElecciones = 1;

    $srt = "SELECT * FROM partido WHERE idElecciones = $idElecciones";
    $con = $db->Execute($srt);
    $file = $con->FetchRow();
    $numRows = $con->recordCount();
    $numRows = $numRows;

    // Realizar una consulta SQL
    $sql = "SELECT * FROM recinto WHERE idRecinto = $idRecinto";

    $query = $db->Execute($sql);

    $row = $query->FetchRow();

    $sql = "SELECT * FROM mesa AS m, voto AS v WHERE m.idRecinto = $idRecinto AND m.idMesa = v.idMesa ORDER BY (v.idMesa) ASC";
    $str = $db->Execute($sql);

    //print_r($str);

    $mesa = "SELECT * FROM mesa WHERE idRecinto = $idRecinto";
    $sqlMesa = $db->Execute($mesa);

    $numMesa = $sqlMesa->recordCount();

    $data = Array();
    $cant = array();

    while ($reg = $str->FetchRow()) {
        $cant[0] = $reg['cantidad'];

        $reg = $str->FetchRow();
        $cant[1] = $reg['cantidad'];

        $reg = $str->FetchRow();
        $cant[2] = $reg['cantidad'];

        $reg = $str->FetchRow();
        $cant[3] = $reg['cantidad'];

        $reg = $str->FetchRow();
        $cant[4] = $reg['cantidad'];


        $data[] = array(
                "0"=>$reg['number'],
                "1"=>$cant[0],
                "2"=>$cant[1],
                "3"=>$cant[2],
                "4"=>$cant[3],
                "5"=>$cant[4],
                "6"=>$reg['valido'],
                "7"=>$reg['blanco'],
                "8"=>$reg['nulo'],
                "9"=>$reg['emitido']
            );
        }
        $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData"=>$data);
        echo json_encode($results);






    //while ($reg = $str->FetchRow()) {

       /* for ($i=1; $i <= $numMesa ; $i++) {
            for ($j=1; $j <= ($numRows+4); $j++) {

                $reg = $str->FetchRow();
                $data->celda[$i][$j] = $reg['number'];
                $j++;
                $data->celda[$i][$j] = $reg['cantidad'];

                $reg = $str->FetchRow();
                $j++;
                $data->celda[$i][$j] = $reg['cantidad'];

                $reg = $str->FetchRow();
                $j++;
                $data->celda[$i][$j] = $reg['cantidad'];

                $reg = $str->FetchRow();
                $j++;
                $data->celda[$i][$j] = $reg['cantidad'];

                $reg = $str->FetchRow();
                $j++;
                $data->celda[$i][$j] = $reg['cantidad'];

                $j++;
                $data->celda[$i][$j] = $reg['valido'];
                $j++;
                $data->celda[$i][$j] = $reg['blanco'];
                $j++;
                $data->celda[$i][$j] = $reg['nulo'];
                $j++;
                $data->celda[$i][$j] = $reg['emitido'];
            }


        }*/


       /* $data->celda

        $data->tbody.= "<tr>";
            $data->tbody.= "<td>".$reg['number']."</td>";

            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
            $reg = $str->FetchRow();
            $data->tbody.= "<td>".$reg['cantidad']."</td>";
            //$data->tbody.= "<td>".$reg['porcentaje']."</td>";
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

    }*/

    //$data->tbody.= '</tbody>';
    //header('Content-type: application/json', true);
    //echo json_encode($data);
