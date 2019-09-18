<?php
    include '../conexion.php';

    $idRecinto = $_REQUEST['id'];
    $idElecciones = $_REQUEST['idElec'];
    /** por el momento id del recinto 1 */
    //$idRecinto = 2;

    //$idElecciones = 1;

    $srt = "SELECT * FROM partido WHERE idElecciones = $idElecciones";
    $con = $db->Execute($srt);
    $file = $con->FetchRow();
    $numRows = $con->recordCount();

    $sql = "SELECT * FROM mesa AS m, voto AS v, recinto AS r, recintoElec AS re WHERE m.idRecinto = $idRecinto AND r.idRecinto = $idRecinto AND m.idMesa = v.idMesa AND m.idRecinto = r.idRecinto AND re.idElecciones = $idElecciones AND r.idRecinto = re.idRecinto ";
    $str = $db->Execute($sql);

    //print_r($str);

    $mesa = "SELECT * FROM mesa WHERE idRecinto = $idRecinto";
    $sqlMesa = $db->Execute($mesa);

    $numMesa = $sqlMesa->recordCount();

    $data = array();
    $cant = array();

    while ($reg = $str->FetchRow()) {

        for ($i=0; $i < $numRows; $i++) {
            $cant[$i] = $reg['cantidad'];
            if($i != ($numRows-1)){
                $reg = $str->FetchRow();
            }
        }

        $data[] = array(
                "0"=>$reg['number'],
                "1"=>$cant[0],
                "2"=>$cant[1],
                "3"=>$cant[2],
                "4"=>$cant[3],
                "5"=>$cant[4],
                "6"=>$cant[5],
                "7"=>$cant[6],
                "8"=>$reg['valido'],
                "9"=>$reg['blanco'],
                "10"=>$reg['nulo'],
                "11"=>$reg['emitido']
            );
    }

    //print_r($data);
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
