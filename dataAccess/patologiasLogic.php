<?php
function obtenerPatologias() {
    $conect = conectar();
    mysqli_set_charset($conect, 'utf8');
    $sql="SELECT Id, Codigo, Nombre FROM patologia ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $codigo, $nombre);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idPatologia' => $id,
                'codigo' => $codigo,
                'nombre' => $nombre
            );
            array_push($datos, $row);
        }
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Patologias";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerPatologiasAutocompletar() {
    $conect = conectar();
    mysqli_set_charset($conect, 'utf8');
    $sql="SELECT Id, Codigo, Nombre FROM patologia ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $codigo, $nombre);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => $codigo.' - '.trim($nombre)
            );
            array_push($datos, $row);
        }
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Patologias";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerNombrePatologia($idPatologia) {
        $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Codigo, Nombre FROM patologia WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPatologia);
    $stmt->execute();
    $stmt->bind_result($id, $codigo, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                'id' => $id,
                'nombre' => $codigo.' - '.trim($nombre)
                );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontró la patologia ".$id;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando patologia";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}