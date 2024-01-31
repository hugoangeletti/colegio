<?php
function obtenerTodos() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiadoconsultorio.*, localidad.Nombre AS NombreLocalidad FROM colegiadoconsultorio 
            LEFT JOIN localidad ON(localidad.Id = colegiadoconsultorio.IdLocalidad)";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $calle, $lateral, $numero, $piso, $departamento, $telefono, $codigoLocalidad,
            $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $estado, $observacion, $fechaBaja, 
            $idLocalidad, $idColegiado, $resolucion, $idRematriculacionColegiado, $hash_qr, $nombreLocalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($estado == 'A') {
                    $estadoDetalle = 'Activo';
                } else {
                    $estadoDetalle = 'Dado de baja';
                }
                $row = array (
                    'id' => $id, 
                    'calle' => $calle, 
                    'lateral' => $lateral, 
                    'numero' => $numero, 
                    'piso' => $piso, 
                    'departamento' => $departamento, 
                    'telefono' => $telefono, 
                    'codigoPostal' => $codigoPostal, 
                    'fechaHabilitacion' => $fechaHabilitacion, 
                    'ultimaInspeccion' => $ultimaInspeccion, 
                    'estado' => $estado, 
                    'estadoDetalle' => $estadoDetalle, 
                    'observacion' => $observacion, 
                    'fechaBaja' => $fechaBaja, 
                    'idLocalidad' => $idLocalidad, 
                    'idColegiado' => $idColegiado, 
                    'resolucion' => $resolucion, 
                    'idRematriculacionColegiado' => $idRematriculacionColegiado,
                    'hash_qr' => $hash_qr,
                    'nombreLocalidad' => $nombreLocalidad
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontraron consultorios";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consultorios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerConsultoriosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiadoconsultorio.*, localidad.Nombre AS NombreLocalidad FROM colegiadoconsultorio 
            LEFT JOIN localidad ON(localidad.Id = colegiadoconsultorio.IdLocalidad)
            WHERE colegiadoconsultorio.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $calle, $lateral, $numero, $piso, $departamento, $telefono, $codigoLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $estado, $observacion, $fechaBaja, $idLocalidad, $idColegiado, $resolucion, $idRematriculacionColegiado, $fechaCarga, $idUsuario, $hash_qr, $nombreLocalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($estado == 'A') {
                    $estadoDetalle = 'Activo';
                } else {
                    $estadoDetalle = 'Dado de baja';
                }
                $row = array (
                    'id' => $id, 
                    'calle' => $calle, 
                    'lateral' => $lateral, 
                    'numero' => $numero, 
                    'piso' => $piso, 
                    'departamento' => $departamento, 
                    'telefono' => $telefono, 
                    'codigoPostal' => $codigoPostal, 
                    'fechaHabilitacion' => $fechaHabilitacion, 
                    'ultimaInspeccion' => $ultimaInspeccion, 
                    'estado' => $estado, 
                    'estadoDetalle' => $estadoDetalle, 
                    'observacion' => $observacion, 
                    'fechaBaja' => $fechaBaja, 
                    'idLocalidad' => $idLocalidad, 
                    'idColegiado' => $idColegiado, 
                    'resolucion' => $resolucion, 
                    'idRematriculacionColegiado' => $idRematriculacionColegiado,
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario,
                    'hash_qr' => $hash_qr,
                    'nombreLocalidad' => $nombreLocalidad
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontraron consultorios";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consultorios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerConsultorioPorId($idConsultorio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiadoconsultorio.*, localidad.Nombre AS NombreLocalidad FROM colegiadoconsultorio 
            LEFT JOIN localidad ON(localidad.Id = colegiadoconsultorio.IdLocalidad)
            WHERE colegiadoconsultorio.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idConsultorio);
    $stmt->execute();
    $stmt->bind_result($id, $calle, $lateral, $numero, $piso, $departamento, $telefono, $codigoLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $estado, $observacion, $fechaBaja, $idLocalidad, $idColegiado, $resolucion, $idRematriculacionColegiado, $fechaCarga, $idUsuario, $hash_qr, $nombreLocalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $estadoDetalle = 'Activo';
            if ($estado == 'B') {
                $estadoDetalle = 'Dado de baja';
            }
            $datos = array (
                    'id' => $id, 
                    'calle' => $calle, 
                    'lateral' => $lateral, 
                    'numero' => $numero, 
                    'piso' => $piso, 
                    'departamento' => $departamento, 
                    'telefono' => $telefono, 
                    'codigoPostal' => $codigoPostal, 
                    'fechaHabilitacion' => $fechaHabilitacion, 
                    'ultimaInspeccion' => $ultimaInspeccion, 
                    'estado' => $estado, 
                    'estadoDetalle' => $estadoDetalle, 
                    'observacion' => $observacion, 
                    'fechaBaja' => $fechaBaja, 
                    'idLocalidad' => $idLocalidad, 
                    'idColegiado' => $idColegiado, 
                    'resolucion' => $resolucion, 
                    'idRematriculacionColegiado' => $idRematriculacionColegiado,
                    'hash_qr' => $hash_qr,
                    'nombreLocalidad' => $nombreLocalidad
                 );
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontro consultorio";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consultorio";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarColegiadoConsultorio($idColegiado, $calle, $numero, $lateral, $piso, $depto, $telefono, $idLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $observacion, $resolucion, $fechaBaja) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
    
        $sql="INSERT INTO colegiadoconsultorio
            (idColegiado, Calle, Lateral, Numero, Piso, Departamento, Telefono, idLocalidad, CodigoPostal, 
            Estado, FechaHabilitacion, UltimaInspeccion, Observacion, FechaBaja, Resolucion, FechaCarga, idUsuario) 
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, 'A', ?, ?, ?, ?, ?, date(now()), ".$_SESSION['user_id'].")";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('issssssissssss', $idColegiado, $calle, $lateral, $numero, $piso, $depto, $telefono,
                $idLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $observacion, $fechaBaja, $resolucion);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $idColegiadoConsultorio = $conect->insert_id;
            $resultado['estado'] = TRUE;
            $resultado['idColegiadoConsultorio'] = $idColegiadoConsultorio;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR CONSULTORIO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }

}

function eliminarColegiadoConsultorio($idConsultorio, $fechaBaja, $observacion){
        $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
    
        $sql="UPDATE colegiadoconsultorio
            SET Estado = 'B', 
                FechaBaja = ?, 
                IdUsuario = ?, 
                FechaCarga = now(), 
                Observacion = ?
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sisi', $fechaBaja, $_SESSION['user_id'], $observacion, $idConsultorio);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ELIMINAR CONSULTORIO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idConsultorio.')';
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function modificarColegiadoConsultorio($idConsultorio, $calle, $numero, $lateral, $piso, $depto, $telefono, $idLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $observacion, $resolucion, $fechaBaja) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
        $sql="UPDATE colegiadoconsultorio
            SET Calle = ?, Numero = ?, Lateral = ?, Piso = ?, Departamento = ?, Telefono = ?,
            IdLocalidad = ?, CodigoPostal = ?, FechaHabilitacion = ?, UltimaInspeccion = ?, Observacion = ?,
            Resolucion = ?, FechaBaja = ?, FechaCarga = now(), IdUsuario = ?
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssssssissssssii', $calle, $numero, $lateral, $piso, $depto, $telefono, 
                $idLocalidad, $codigoPostal, $fechaHabilitacion, $ultimaInspeccion, $observacion, 
                $resolucion, $fechaBaja, $_SESSION['user_id'], $idConsultorio);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR CONSULTORIO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        if ($resultado['estado']) {
            $resultado['mensaje'] .= ' ('.$idConsultorio.')';
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function guardarQrColegiadoConsultorio($idColegiadoConsultorio, $hash_qr) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    //obtengo proxima ENTREGA
    $sql="UPDATE colegiadoconsultorio
            SET HashQR = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $hash_qr, $idColegiadoConsultorio);
    $stmt->execute();
    $stmt->store_result();
      
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR CODIGO QR. ".  mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado; 

}