<?php
function obtenerLocalidadesPorIdElecciones($idElecciones) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidad.*, zonas.Nombre 
        FROM eleccioneslocalidad 
        INNER JOIN zonas ON(zonas.Zona = eleccioneslocalidad.Localidad)
        WHERE IdElecciones = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idElecciones);
    $stmt->execute();
    $stmt->bind_result($id, $idElecciones, $localidad, $cantDelegados, $cantElectores, $cantValidos, $cantAnulados, $cantEnBlanco, $cociente, $localidadDetalle, $zona);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idEleccionesLocalidad' => $id,
                        'idElecciones' => $idElecciones,
                        'codigoLocalidad' => $localidad,
                        'cantDelegados' => $cantDelegados,
                        'cantElectores' => $cantElectores,
                        'cantValidos' => $cantValidos,
                        'cantAnulados' => $cantAnulados,
                        'cantEnBlanco' => $cantEnBlanco,
                        'cociente' => $cociente,
                        'localidadDetalle' => $zona,
                        'detalle' => $localidadDetalle
                    );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay localidades";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidades de las elecciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEleccionesLocalidadPorId($idEleccionesLocalidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidad.*, zonas.Nombre 
            FROM eleccioneslocalidad 
            INNER JOIN zonas ON(zonas.Zona = eleccioneslocalidad.Localidad)
            WHERE IdEleccionesLocalidad = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidad);
    $stmt->execute();
    $stmt->bind_result($id, $idElecciones, $localidad, $cantDelegados, $cantElectores, $cantValidos, $cantAnulados, $cantEnBlanco, $cociente, $localidadDetalle, $zona);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                        'idEleccionesLocalidad' => $id,
                        'idElecciones' => $idElecciones,
                        'codigoLocalidad' => $localidad,
                        'cantDelegados' => $cantDelegados,
                        'cantElectores' => $cantElectores,
                        'cantValidos' => $cantValidos,
                        'cantAnulados' => $cantAnulados,
                        'cantEnBlanco' => $cantEnBlanco,
                        'cociente' => $cociente,
                        'localidadDetalle' => $zona
                    );
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay localidades";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidades de las elecciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEleccionesLocalidades($idElecciones, $codigoLocalidad, $cantDelegados) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eleccioneslocalidad (IdElecciones, Localidad, CantidadDelegados) 
        VALUES (?, ?, ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isis', $idElecciones, $codigoLocalidad, $cantDelegados);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Localidad HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Localidad';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarEleccionesLocalidades($idEleccionesLocalidad, $codigoLocalidad, $cantDelegados) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eleccioneslocalidad 
            SET Localidad = ?, CantidadDelegados = ?
            WHERE IdEleccionesLocalidad = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sii', $codigoLocalidad, $cantDelegados, $detalleLocalidad, $idEleccionesLocalidad);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Localidad HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Localidad';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarEleccionesLocalidades($idEleccionesLocalidad){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="DELETE FROM eleccioneslocalidad WHERE IdEleccionesLocalidad = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidad);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Localidad HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Localidad';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

