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
                "8"=>$cant[7],
                "9"=>$reg['valido'],
                "10"=>$reg['blanco'],
                "11"=>$reg['nulo'],
                "12"=>$reg['emitido']
            );
    }

    //print_r($data);
    $results = array(
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData"=>$data);
    echo json_encode($results);
