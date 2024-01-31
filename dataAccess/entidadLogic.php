<?php
//accesos a tabla colegiado
function obtenerEntidadPorId($idEntidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT e.Id, e.IdTipoEntidad, e.Nombre, e.Domicilio, e.IdLocalidad, e.CodigoPostal, e.Telefonos, e.Email, te.Nombre, l.Nombre
            FROM entidad e
            LEFT JOIN tipoentidad te ON te.Id = e.IdTipoEntidad
            LEFT JOIN localidad l ON l.Id = e.IdLocalidad
            WHERE e.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEntidad);
    $stmt->execute();
    $stmt->bind_result($idEntidad, $idTipoEntidad, $nombreEntidad, $domicilio, $idLocalidad, $codigoPostal, $telefonos, $mail, $nombreTipoEntidad, $nombreLocalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idEntidad' => $idEntidad,
                    'idTipoEntidad' => $idTipoEntidad,
                    'nombreEntidad' => $nombreEntidad,
                    'domicilio' => $domicilio,
                    'idLocalidad' => $idLocalidad,
                    'codigoPostal' => $codigoPostal,
                    'telefonos' => $telefonos,
                    'mail' => $mail,
                    'nombreTipoEntidad' => $nombreTipoEntidad,
                    'nombreLocalidad' => $nombreLocalidad
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay localidad ".$idEntidad;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando entidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEntidadesAutocompletar($idTipoEntidad){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (isset($idTipoEntidad)) {
        $filtro = " WHERE e.idTipoEntidad = ".$idTipoEntidad;
    } else {
        $filtro = "";
    }
    $sql = "SELECT e.Id, e.Nombre, l.Nombre
            FROM entidad e
            LEFT JOIN localidad l ON l.Id = e.IdLocalidad ".$filtro." 
            ORDER BY e.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idEntidad, $nombreEntidad, $nombreLocalidad);
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
                    'id' => $idEntidad,
                    'nombre' => trim($nombreEntidad).' ('.trim($nombreLocalidad).')'
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
            $resultado['mensaje'] = "No hay entidades";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando entidades";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

