<?php
function obtenerTiposObservacion(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM tipoobservacion";
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'nombre' => $nombre
                );
                array_push($datos, $row);
            }
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON TIPOS DE OBSERVACION";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
        $resultado['estado'] = TRUE;
        $resultado['datos'] = $datos;
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando tipos de Observacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerColegiadoObservaciones($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT co.id, co.Observaciones, co.FechaCarga, 
            co.Estado, u.Usuario, co.IdTipoObservacion, tob.Nombre
            FROM colegiadoobservacion co
            LEFT JOIN usuario u ON(u.Id = co.IdUsuario)
            INNER JOIN tipoobservacion tob ON tob.Id = co.IdTipoObservacion
            WHERE co.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $observaciones, $fechaCarga, $estado, $usuario, $idTipoObservacion, $tipoObservacion);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'observaciones' => $observaciones,
                    'nombreUsuario' => $usuario,
                    'fechaCarga' => $fechaCarga,
                    'estado' => $estado,
                    'idTipoObservacion' => $idTipoObservacion,
                    'tipoObservacion' => $tipoObservacion
                );
                array_push($datos, $row);
            }
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON OBSERVACIONES";
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

function obtenerColegiadoObservacionPorId($idColegiadoObservacion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT co.Observaciones, co.FechaCarga, 
            co.Estado, u.Usuario, co.IdTipoObservacion, tob.Nombre
            FROM colegiadoobservacion co
            LEFT JOIN usuario u ON(u.Id = co.IdUsuario)
            INNER JOIN tipoobservacion tob ON tob.Id = co.IdTipoObservacion
            WHERE co.id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoObservacion);
    $stmt->execute();
    $stmt->bind_result($observaciones, $fechaCarga, $estado, $usuario, $idTipoObservacion, $tipoObservacion);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'observaciones' => $observaciones,
                'nombreUsuario' => $usuario,
                'fechaCarga' => $fechaCarga,
                'estado' => $estado,
                'idTipoObservacion' => $idTipoObservacion,
                'tipoObservacion' => $tipoObservacion
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

function agregarColegiadoObservacion($idColegiado, $observaciones, $idTipoObservacion) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO colegiadoobservacion 
                (Observaciones, IdColegiado, IdUsuario, FechaCarga, Estado, IdTipoObservacion)
                VALUES (?, ?, ?, ?, 'A', ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('siisi', $observaciones, $idColegiado, $_SESSION['user_id'], $fechaCarga, $idTipoObservacion);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO LA OBSERVACION CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR OBSERVACION ".mysqli_stmt_errno($stmt);
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
        $resultado['mensaje'] = "ERROR AL REGISTRAR OBSERVACION";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
    
}

function agregarAdjunto($idColegiadoObservacion, $archivoSubido, $tipoArchivo, $nombreArchivo, $pathArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO colegiadoobservacionadjunto 
            (IdColegiadoObservacion, ArchivoSubido, TipoArchivo, NombreArchivo, PathArchivo, IdUsuario, FechaCarga, Estado)
            VALUES (?, ?, ?, ?, ?, ?, now(), 'A')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('issssi', $idColegiadoObservacion, $archivoSubido, $tipoArchivo, $nombreArchivo, $pathArchivo, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE REGISTRO ADJUNTO CORRECTAMENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ADJUNTAR IMAGEN ".mysqli_stmt_errno($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;
}

function editarColegiadoObservacion($idColegiadoObservacion, $observaciones, $estado, $idTipoObservacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadoobservacion 
            SET Observaciones = ?, Estado = ?, IdUsuario = ?, FechaCarga = now(), IdTipoObservacion = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssiii', $observaciones, $estado, $_SESSION['user_id'], $idTipoObservacion, $idColegiadoObservacion);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE ACTUALIZO LA OBSERVACION CORRECTAMENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR OBSERVACION ".mysqli_stmt_errno($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;    
}

function obtenerAdjuntoPorObservacion($idColegiadoObservacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT *
            FROM colegiadoobservacionadjunto 
            WHERE IdColegiadoObservacion = ? AND Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoObservacion);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiadoObservacion, $subido, $tipoArchivo, $nombreArchivo, $pathArchivo, $idUsuario, $fechaCarga, $estado);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'idColegiadoObservacion' => $idColegiadoObservacion,
                    'subido' => $subido,
                    'tipoArchivo' => $tipoArchivo,
                    'nombreArchivo' => $nombreArchivo,
                    'pathArchivo' => $pathArchivo,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga,
                    'estado' => $estado
                );
                array_push($datos, $row);
            }
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON ADJUNTOS";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando adjuntos";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function eliminarAdjunto($idAdjunto) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadoobservacionadjunto 
            SET Estado = 'B', IdUsuario = ?, FechaCarga = now()
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $idAdjunto);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE ELIMINO ADJUNTO CORRECTAMENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ELIMINAR IMAGEN ".mysqli_stmt_errno($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;    
}

function obtenerAdjunto($idAdjunto) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT *
            FROM colegiadoobservacionadjunto 
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAdjunto);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiadoObservacion, $subido, $tipoArchivo, $nombreArchivo, $pathArchivo, $idUsuario, $fechaCarga, $estado);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                   'id' => $id,
                    'idColegiadoObservacion' => $idColegiadoObservacion,
                    'subido' => $subido,
                    'tipoArchivo' => $tipoArchivo,
                    'nombreArchivo' => $nombreArchivo,
                    'pathArchivo' => $pathArchivo,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga,
                    'estado' => $estado
                );
 
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRARON ADJUNTOS";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando adjuntos";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}