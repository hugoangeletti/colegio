<?php
function obtenerTiposResoluciones(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id, Detalle, TipoEspecialista
            FROM tiporesolucion
            WHERE Estado = 'A'
            ORDER BY Detalle";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $tipoEspecialista);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id,
                    'nombre' => $nombre,
                    'tipoEspecialista' => $tipoEspecialista
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay Tipos de resoluciones";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipos de resoluciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerTipoResolucionPorId($idTipoResolucion) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Detalle, TipoEspecialista
            FROM tiporesolucion 
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idTipoResolucion);
    $stmt->execute();

    $stmt->bind_result($id, $nombre, $tipoEspecialista);
    $stmt->store_result();

    if ($stmt->execute()) {
        if (mysqli_stmt_num_rows($stmt) >= 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                'id' => $id,
                    'nombre' => $nombre,
                    'tipoEspecialista' => $tipoEspecialista,
                    'idIngresado' => $idTipoResolucion
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay Tipo de resolucion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipos de resoluciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}



