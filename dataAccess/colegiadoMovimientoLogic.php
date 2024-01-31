<?php
function obtenerMovimientosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadomovimiento.Id, colegiadomovimiento.IdMovimiento, colegiadomovimiento.FechaDesde, 
        colegiadomovimiento.FechaHasta, colegiadomovimiento.DistritoCambio, colegiadomovimiento.DistritoOrigen, 
        colegiadomovimiento.IdPatologia, tipomovimiento.DetalleCompleto, patologia.Nombre, colegiadomovimiento.FechaCarga,
        distritos.Romanos
    FROM colegiadomovimiento
    INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiadomovimiento.IdMovimiento)
    LEFT JOIN patologia ON(patologia.Id = colegiadomovimiento.IdPatologia)
    LEFT JOIN distritos ON(distritos.Id = colegiadomovimiento.DistritoCambio)
    WHERE IdColegiado = ?
    AND colegiadomovimiento.Estado = 'O'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoMovimiento, $idTipoMovimietno, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, $idPatologia, $detalleMovimiento, $nombrePatologia, $fechaCarga, $romanos);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoMovimiento' => $idColegiadoMovimiento,
                    'idTipoMovimietno' => $idTipoMovimietno,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta, 
                    'distritoCambio' => $distritoCambio,
                    'distritoOrigen' => $distritoOrigen,
                    'idPatologia' => $idPatologia, 
                    'detalleMovimiento' => $detalleMovimiento, 
                    'nombrePatologia' => $nombrePatologia,
                    'fechaCarga' => $fechaCarga,
                    'romanos' => $romanos
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
            $resultado['mensaje'] = "El colegiado no tiene Movimientos Matriculares.";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando nombrePatologia";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function colegiadoTieneMovimientos($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT count(Id) AS Cantidad
            FROM colegiadomovimiento 
            WHERE IdColegiado = ?
            AND Estado = 'O'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt) == 0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0) {
                $resultado['estado'] = TRUE;
            }
        }
    }
    return $resultado;
}

function colegiadoTieneMovimientosOtrosDistritos($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT count(Id) AS Cantidad
            FROM colegiadomovimientodistritos 
            WHERE IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt) == 0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0) {
                $resultado['estado'] = TRUE;
            }
        }
    }
    return $resultado;
}

function obtenerMovimientoPorId($idColegiadoMovimiento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cm.IdColegiado, cm.FechaDesde, cm.FechaHasta, cm.DistritoCambio, cm.DistritoOrigen, cm.IdPatologia, tm.DetalleCompleto
            FROM colegiadomovimiento cm
            INNER JOIN tipomovimiento tm ON(tm.Id = cm.IdMovimiento)
            WHERE cm.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoMovimiento);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, $idPatologia, $detalleCompleto);
    $stmt->store_result();

    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt) == 0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $datos['idColegiado'] = $idColegiado;
            $datos['fechaDesde'] = $fechaDesde;
            $datos['fechaHasta'] = $fechaHasta;
            $datos['distritoCambio'] = $distritoCambio;
            $datos['distritoOrigen'] = $distritoOrigen;
            $datos['idPatologia'] = $idPatologia;
            $datos['detalleMovimiento'] = $detalleCompleto;
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontro el Movimiento. (".$idColegiado." - ".$tipoEstado.")";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Movimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function obtenerMovimientoMatricular($idColegiado, $tipoEstado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, FechaDesde, FechaHasta, DistritoCambio, DistritoOrigen, IdPatologia
            FROM colegiadomovimiento 
            WHERE IdColegiado = ?
            AND IdMovimiento = ?
            AND Estado = 'O'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $tipoEstado);
    $stmt->execute();
    $stmt->bind_result($id, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, $idPatologia);
    $stmt->store_result();

    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt) == 0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $datos['id'] = $id;
            $datos['fechaDesde'] = $fechaDesde;
            $datos['fechaHasta'] = $fechaHasta;
            $datos['distritoCambio'] = $distritoCambio;
            $datos['distritoOrigen'] = $distritoOrigen;
            $datos['idPatologia'] = $idPatologia;
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontro el Movimiento. (".$idColegiado." - ".$tipoEstado.")";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Movimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function obtenerUltimoMovimiento($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cm.IdMovimiento
            FROM colegiadomovimiento cm
            WHERE cm.IdColegiado = ? AND cm.Estado = 'O'
            ORDER BY cm.FechaDesde DESC 
            LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idTipoMovimiento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt) == 0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $resultado['idTipoMovimiento'] = $idTipoMovimiento;
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontró Movimiento.";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Movimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function obtenerMovimientosOtrosDistritosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiadomovimientodistritos WHERE IdColegiado = ? AND (Estado <> 'B' OR ESTADO IS NULL)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiado, $idMovimietno, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, 
            $idUsuarioCarga, $fechaCarga, $estado, $observacionOtroDistrito, $fechaCargaRehabilitacion, $idUsuarioRehabilitacion, $idMesaEntrada);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoMovimiento' => $id,
                    'idMovimietno' => $idMovimietno,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta, 
                    'distritoCambio' => $distritoCambio,
                    'distritoOrigen' => $distritoOrigen,
                    'idUsuario' => $idUsuarioCarga, 
                    'fechaCarga' => $fechaCarga,
                    'observaciones' => $observacionOtroDistrito
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
            $resultado['mensaje'] = "El colegiado no tiene Movimientos de Otros Distritos.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Movimientos de Otros Distritos";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarColegiadoMovimiento($idColegiado, $idTipoMovimiento, $distritoCambio, $fechaDesde, $fechaHasta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    //agrego la solicitud de certificado
    $sql="INSERT INTO colegiadomovimiento
        (IdColegiado, IdMovimiento, FechaDesde, FechaHasta, DistritoCambio, IdUsuarioCarga, FechaCarga, Estado) 
        VALUE (?, ?, ?, ?, ?, ?, date(now()), 'O')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iisssi', $idColegiado, $idTipoMovimiento, $fechaDesde, $fechaHasta, $distritoCambio, $_SESSION['user_id']); 
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['idColegiadoMovimiento'] = $conect->insert_id;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR MOVIMIENTO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function modificarColegiadoMovimiento($idColegiadoMovimiento, $idTipoMovimiento, $distritoCambio, $fechaDesde, $fechaHasta, $estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE colegiadomovimiento
        SET IdMovimiento = ?, FechaDesde = ?, FechaHasta = ?, DistritoCambio = ?, 
        IdUsuarioCarga = ?, FechaCarga = date(now()), Estado = ?
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssisi', $idTipoMovimiento, $fechaDesde, $fechaHasta, $distritoCambio, $_SESSION['user_id'], $estado, $idColegiadoMovimiento); 
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "EL MOVIMIENTO DE ACTUALIZO CORRECTAMENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR MOVIMIENTO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado;
}

function agregarMovimientoOtroDistrito($idColegiado, $distritoOrigen, $distritoCambio, $fechaDesde, $fechaHasta, $observaciones){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    //agrego la solicitud de certificado
    $sql="INSERT INTO colegiadomovimientodistritos
        (IdColegiado, FechaDesde, FechaHasta, DistritoCambio, DistritoOrigen, IdUsuarioCarga, FechaCarga, ObservacionOtroDistrito) 
        VALUE (?, ?, ?, ?, ?, ?, date(now()), ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('issiiis', $idColegiado, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, $_SESSION['user_id'], $observaciones); 
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['idColegiadoMovimientoOtro'] = $conect->insert_id;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR MOVIMIENTO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function anularMovimientoOtroDistrito($idMovimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    
    $observaciones = "ANULADA POR USUARIO ".$_SESSION['user_entidad']['nombreUsuario']." el dia y hora ".date('d-m-Y H:i:s');
    //agrego la solicitud de certificado
    $sql="UPDATE colegiadomovimientodistritos
        SET Estado = 'B', 
        ObservacionOtroDistrito = CONCAT(ObservacionOtroDistrito, ' ', ?)
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $observaciones, $idMovimiento); 
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ANULAR MOVIMIENTO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function patologiaColegiadoMovimiento($idColegiadoMovimiento, $idPatologia) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    
    $sql="UPDATE colegiadomovimiento
        SET IdPatologia = ?
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idPatologia, $idColegiadoMovimiento); 
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL MODIFICAR MOVIMIENTO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}