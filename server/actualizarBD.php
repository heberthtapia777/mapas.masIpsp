<?php
    include '../conexion.php';

    $sql = "SELECT * FROM recinto AS r WHERE r.name LIKE 'ESC %' ";

    $query = $db->Execute($sql);

    $numRows = $query->recordCount();

    while ( $row = $query->FetchRow() ) {

        $string = str_replace("ESC ","ESCUELA ",$row['name']);

        $id = $row['idRecinto'];

        $str = "UPDATE recinto SET name = '$string' WHERE idRecinto = $id ";

        $exe = $db->Execute($str);

    }

?>
