<?php
function obtenerMovimientosPorIdEticaExpediente($id, $tipoUsuario){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eticaexpedientemovimiento.Id, eticaexpedientemovimiento.Derivado, eticaexpedientemovimiento.Fecha, eticaexpedientemovimiento.FechaMovimiento,
        eticaestado.Nombre as Estado, usuario.NombreCompleto as NombreUsuario, eticaexpedientemovimiento.Observacion,
        eticaexpedientemovimiento.IdEticaEstado
        FROM eticaexpedientemovimiento
        LEFT JOIN eticaestado ON(eticaestado.Id = eticaexpedientemovimiento.IdEticaEstado)
        INNER JOIN usuario ON(usuario.Id = eticaexpedientemovimiento.IdUsuario)
        WHERE eticaexpedientemovimiento.IdEticaExpediente = ?
        AND usuario.TipoUsuario = ? AND eticaexpedientemovimiento.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $id, $tipoUsuario);
    $stmt->execute();

    $stmt->bind_result($idEticaExpedienteMovimiento, $derivado, $fecha, $fechaMovimiento, $estado, $usuario, $observacion, $idEticaEstado);

    $stmt->store_result();
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idEticaExpedienteMovimiento' => $idEticaExpedienteMovimiento,
                    'derivado' => $derivado,
                    'fecha' => $fecha,
                    'fechaMovimiento' => $fechaMovimiento,
                    'idEticaEstado' => $idEticaEstado,
                    'estado' => $estado,
                    'usuario' => $usuario,
                    'observacion' => $observacion
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
            $resultado['mensaje'] = "No hay movimientos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando movimientos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEticaExpedienteMovimientoPorId($idEticaExpedienteMovimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM eticaexpedientemovimiento WHERE eticaexpedientemovimiento.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpedienteMovimiento);
    $stmt->execute();

    $stmt->bind_result($idEticaExpedienteMovimiento, $idEticaExpediente, $idEticaEstado, $derivado, $fecha, $fechaMovimiento, $idUsuario, $observacion, $borrado);

    $stmt->store_result();
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idEticaExpedienteMovimiento' => $idEticaExpedienteMovimiento,
                    'idEticaExpediente' => $idEticaExpediente,
                    'idEticaEstado' => $idEticaEstado,
                    'derivado' => $derivado,
                    'fecha' => $fecha,
                    'fechaMovimiento' => $fechaMovimiento,
                    'idUsuario' => $idUsuario,
                    'observacion' => $observacion,
                    'borrado' => $borrado
            );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay movimientos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando movimientos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEticaExpedienteMovimiento($idEticaExpediente, $idEticaEstado, $derivado, $observacion, $fecha, $conect) {
    if (!isset($conect)) {
        $conect = conectar();
    }
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eticaexpedientemovimiento 
            (IdEticaExpediente, IdEticaEstado, Derivado, Observacion, Fecha, FechaMovimiento, IdUsuario) 
            VALUES (?, ?, ?, ?, ?, now(), ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iisssi', $idEticaExpediente, $idEticaEstado, $derivado, $observacion, $fecha, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //modifico el estadoetica en eticaexpediente
        if (isset($idEticaEstado)) {
            $sql="UPDATE eticaexpediente
                    SET IdEticaEstado = ?
                    WHERE Id = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idEticaEstado, $idEticaExpediente);
            $stmt->execute();
            $stmt->store_result();
        }
        $estadoConsulta = TRUE;
        $mensaje = 'Movimiento HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Movimiento';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function borrarEticaExpedienteMovimiento($idEticaExpedienteMovimiento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eticaexpedientemovimiento 
        SET Borrado = 1,
            FechaMovimiento = NOW(),
            IdUsuario = ".$_SESSION['user_id']."
            WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpedienteMovimiento);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $sql="SELECT eem.IdEticaExpediente, ee.IdEticaEstado
            FROM eticaexpedientemovimiento eem
            INNER JOIN eticaexpediente ee ON ee.Id = eem.IdEticaExpediente
            WHERE eem.Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idEticaExpedienteMovimiento);
        $stmt->execute();        
        $stmt->bind_result($idEticaExpediente, $idEticaEstado);

        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idEticaExpediente' => $idEticaExpediente,
                    'idEticaEstado' => $idEticaEstado,
            );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = 'Expediente NO ENCONTRADO';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = 'ERROR AL BORRAR Expediente';
    }
    return $resultado; 
}

function modificaEticaExpedienteMovimiento($idEticaExpedienteMovimiento, $idEticaExpediente, $idEticaEstado, $derivado, $observacion, $fecha) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eticaexpedientemovimiento
        SET Borrado = 1
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpedienteMovimiento);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agregar el movimiento con los nuevos datos
        $resultado = agregarEticaExpedienteMovimiento($idEticaExpediente, $idEticaEstado, $derivado, $observacion, $fecha, $conect);
        if ($resultado['estado']) {
            $estadoConsulta = TRUE;
            $mensaje = 'idEticaExpedienteMovimiento HA SIDO MODIFICADO';
        } else {
            $estadoConsulta = FALSE;
            $mensaje = 'ERROR AL AGREGAR idEticaExpedienteMovimiento';    
        }
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR idEticaExpedienteMovimiento';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}