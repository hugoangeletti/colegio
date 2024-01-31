<?php
//accesos a tabla colegiado
function obtenerColegiadoDomicilioPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadodomicilioreal.idColegiadoDomicilioReal, colegiadodomicilioreal.Calle, 
        colegiadodomicilioreal.Lateral, colegiadodomicilioreal.Numero, colegiadodomicilioreal.Piso, 
        colegiadodomicilioreal.Departamento, colegiadodomicilioreal.idLocalidad, colegiadodomicilioreal.CodigoPostal, 
        colegiadodomicilioreal.FechaCarga, localidad.Nombre AS NombreLocalidad, origendomicilio.Nombre AS Origen
    FROM colegiadodomicilioreal
    INNER JOIN localidad ON(localidad.Id = colegiadodomicilioreal.idLocalidad)
    INNER JOIN origendomicilio ON(origendomicilio.idOrigenDomicilio = colegiadodomicilioreal.idOrigen)
    WHERE IdColegiado = ? and IdEstado = 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDomicilio, $calle, $lateral, $numero, $piso, $depto, $idLocalidad, $codigoPostal, $fechaCarga, $nombreLocalidad, $origen);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiadoDomicilio' => $idColegiadoDomicilio,
                    'calle' => $calle,
                    'lateral' => $lateral,
                    'numero' => $numero,
                    'piso' => $piso,
                    'depto' => $depto,
                    'idLocalidad' => $idLocalidad,
                    'codigoPostal' => $codigoPostal,
                    'fechaCarga' => $fechaCarga, 
                    'nombreLocalidad' => $nombreLocalidad,
                    'origen' => $origen
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerDomiciliosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadodomicilioreal.idColegiadoDomicilioReal, colegiadodomicilioreal.Calle, 
        colegiadodomicilioreal.Lateral, colegiadodomicilioreal.Numero, colegiadodomicilioreal.Piso, 
        colegiadodomicilioreal.Departamento, colegiadodomicilioreal.idLocalidad, colegiadodomicilioreal.CodigoPostal, 
        colegiadodomicilioreal.FechaCarga, localidad.Nombre AS NombreLocalidad, origendomicilio.Nombre AS Origen,
        colegiadodomicilioreal.IdEstado
    FROM colegiadodomicilioreal
    INNER JOIN localidad ON(localidad.Id = colegiadodomicilioreal.idLocalidad)
    INNER JOIN origendomicilio ON(origendomicilio.idOrigenDomicilio = colegiadodomicilioreal.idOrigen)
    WHERE IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDomicilio, $calle, $lateral, $numero, $piso, $depto, $idLocalidad, $codigoPostal, $fechaCarga, $nombreLocalidad, $origen, $idEstado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $domicilioCompleto = "";
                if ($calle) {
                    $domicilioCompleto = $calle;
                    if ($numero) {
                        $domicilioCompleto .= " Nº ".$numero;
                    }
                    if ($lateral) {
                        $domicilioCompleto .= " e/ ".$lateral;
                    }
                    if ($piso && strtoupper($piso) != "NR") {
                        $domicilioCompleto .= " Piso ".$piso;
                    }
                    if ($depto && strtoupper($depto) != "NR") {
                        $domicilioCompleto .= " Dto. ".$depto;
                    }
                }
                $row = array (
                    'idColegiadoDomicilio' => $idColegiadoDomicilio,
                    'domicilio' => $domicilioCompleto,
                    'codigoPostal' => $codigoPostal,
                    'fechaActualizacion' => $fechaCarga, 
                    'nombreLocalidad' => $nombreLocalidad.' ('.$codigoPostal.')',
                    'origen' => $origen,
                    'idEstado' => $idEstado
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay domicilios ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando domicilios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarColegiadoDomicilio($idColegiado, $calle, $numero, $lateral, $piso, $depto, $idLocalidad, $codigoPostal){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
    
        //marco como anulado el domicilio actualmente activo y luego doy de alta el nuevo domicilio
        $sql="UPDATE colegiadodomicilioreal
            SET idEstado = 2 
            WHERE IdColegiado = ? AND idEstado = 1";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO colegiadodomicilioreal
                (idColegiado, Calle, Lateral, Numero, Piso, Departamento, idLocalidad, CodigoPostal, idEstado, FechaCarga, idUsuario, idOrigen) 
                VALUE (?, ?, ?, ?, ?, ?, ?, ?, 1, date(now()), ".$_SESSION['user_id'].", 2)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('isssssis', $idColegiado, $calle, $lateral, $numero, $piso, $depto, $idLocalidad, $codigoPostal);
            $stmt->execute();
            $stmt->store_result();
            $resultado = array(); 
            if (mysqli_stmt_errno($stmt)==0) {
                $idColegiadoDomicilio = $conect->insert_id;
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL ACTUALIZAR DOMICILIO. PASO 2";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR DOMICILIO. PASO 1";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idColegiadoDomicilio.')';
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

function obtenerDomicilioProfesional($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadoconsultorio.Calle, colegiadoconsultorio.Lateral, colegiadoconsultorio.Numero,
        colegiadoconsultorio.Piso, colegiadoconsultorio.Departamento, localidad.Nombre
    FROM colegiadoconsultorio
    LEFT JOIN localidad ON(localidad.Id = colegiadoconsultorio.IdLocalidad)
    WHERE colegiadoconsultorio.IdColegiado = ? 
    AND (colegiadoconsultorio.Estado='A' AND colegiadoconsultorio.FechaBaja is null)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($calle, $lateral, $numero, $piso, $depto, $nombreLocalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $domicilioCompleto = "";
            if ($calle) {
                $domicilioCompleto = $calle;
                if ($numero) {
                    $domicilioCompleto .= " Nº ".$numero;
                }
                if ($lateral) {
                    $domicilioCompleto .= " e/ ".$lateral;
                }
                if ($piso && strtoupper($piso) != "NR") {
                    $domicilioCompleto .= " Piso ".$piso;
                }
                if ($depto && strtoupper($depto) != "NR") {
                    $domicilioCompleto .= " Dto. ".$depto;
                }
            }
            $datos = array (
                'domicilio' => $domicilioCompleto,
                'nombreLocalidad' => $nombreLocalidad
             );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay domicilios ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando domicilios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}