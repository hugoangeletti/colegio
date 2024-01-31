<?php
function obtenerColegiadosResidentes($tipoFiltro){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');

    $filtro = "";
    if ($tipoFiltro == "VIGENTE") {
        $filtro = " AND cr.FechaFin >= DATE(NOW())";
    }
    if ($tipoFiltro == "NO_VIGENTE") {
        $filtro = " AND cr.FechaFin < DATE(NOW())";
    }
    $sql = "SELECT cr.Id, cr.IdColegiado, cr.FechaInicio, cr.FechaFin, cr.Opcion, cr.Adjunta, cr.Anio, cr.IdEntidad, e.Nombre, c.Matricula, p.Apellido, p.Nombres
            FROM colegiadoresidente cr
            INNER JOIN entidad e ON e.Id = cr.IdEntidad
            INNER JOIN colegiado c ON c.Id = cr.IdColegiado
            INNER JOIN persona p ON p.Id = c.IdPersona
            WHERE cr.Borrado = 0 ".$filtro;
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiado, $fechaInicio, $fechaFin, $opcion, $adjunto, $anio, $idEntidad, $nombreEntidad, $matricula, $apellido, $nombre);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idColegiadoResidente' => $id,
                    'idColegiado' => $idColegiado,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'opcion' => $opcion,
                    'adjunto' => $adjunto,
                    'anio' => $anio,
                    'idEntidad' => $idEntidad,
                    'nombreEntidad' => $nombreEntidad,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre)
                );
                array_push($datos, $row);
            }
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON RESIDENTES REGISTRADOS";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
        $resultado['estado'] = TRUE;
        $resultado['datos'] = $datos;
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando RESIDENTES REGISTRADOS";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerColegiadoResidentePorIdColegiado($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cr.Id, cr.FechaInicio, cr.FechaFin, cr.Opcion, cr.Adjunta, cr.Anio, cr.IdEntidad, e.Nombre
            FROM colegiadoresidente cr
            INNER JOIN entidad e ON e.Id = cr.IdEntidad
            WHERE cr.IdColegiado = ? AND cr.Borrado = 0 AND cr.FechaFin >= DATE(NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $fechaInicio, $fechaFin, $opcion, $adjunto, $anio, $idEntidad, $nombreEntidad);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            /*
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idColegiadoResidente' => $id,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'opcion' => $opcion,
                    'adjunta' => $adjunta,
                    'anio' => $anio,
                    'idEntidad' => $idEntidad,
                    'nombreEntidad' => $nombreEntidad
                );
                array_push($datos, $row);
            }
            */
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idColegiadoResidente' => $id,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'opcion' => $opcion,
                    'adjunto' => $adjunto,
                    'anio' => $anio,
                    'idEntidad' => $idEntidad,
                    'nombreEntidad' => $nombreEntidad
            );
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON idColegiadoResidente";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
        $resultado['estado'] = TRUE;
        $resultado['datos'] = $datos;
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Observaciones";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerColegiadoResidentePorId($idColegiadoResidente){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cr.IdColegiado, cr.FechaInicio, cr.FechaFin, cr.Opcion, cr.Adjunta, cr.Anio, cr.IdEntidad, e.Nombre
            FROM colegiadoresidente cr
            INNER JOIN entidad e ON e.Id = cr.IdEntidad
            WHERE cr.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoResidente);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $fechaInicio, $fechaFin, $opcion, $adjunto, $anio, $idEntidad, $nombreEntidad);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idColegiado' => $idColegiado,
                    'fechaInicio' => $fechaInicio,
                    'fechaFin' => $fechaFin,
                    'opcion' => $opcion,
                    'adjunto' => $adjunto,
                    'anio' => $anio,
                    'idEntidad' => $idEntidad,
                    'nombreEntidad' => $nombreEntidad
            );
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRO OBSERVACIONES";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Observaciones";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function agregarColegiadoResidente($idColegiado, $opcion, $anio, $idEntidad, $adjunta, $fechaInicio, $fechaFin) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO colegiadoresidente 
                (IdColegiado, Opcion, FechaInicio, FechaFin, Adjunta, IdUsuario, FechaCarga, Anio, IdEntidad)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('issssiii', $idColegiado, $opcion, $fechaInicio, $fechaFin, $adjunta, $_SESSION['user_id'], $anio, $idEntidad);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['idColegiadoResidente'] = mysqli_stmt_insert_id($stmt);
            $resultado['mensaje'] = "SE REGISTRO LA OPCION DEL RESIDENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR OPCION DEL RESIDENTE ".mysqli_stmt_errno($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR OPCION DEL RESIDENTE";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
    
}

function editarColegiadoResidente($idColegiadoResidente, $opcion, $anio, $idEntidad, $adjunta) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadoresidente 
            SET Opcion = ?, Adjunta = ?, Anio = ?, IdEntidad = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssiii', $opcion, $adjunta, $anio, $idEntidad, $idColegiadoResidente);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE ACTUALIZO LA OPCION DEL RESIDENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR OPCION DEL RESIDENTE ".mysqli_stmt_errno($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;    
}

function anularColegiadoResidente($idColegiadoResidente) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadoresidente 
            SET Borrado = 1, FechaBorrado = NOW()
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoResidente);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE ACTUALIZO LA OPCION DEL RESIDENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR OPCION DEL RESIDENTE ".mysqli_stmt_errno($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado; 
}