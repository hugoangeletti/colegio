<?php
function obtenerFalsosMedicosPorEstado($estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM falsosmedicos WHERE Estado = ? ORDER BY Apellido, Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($id, $apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, $fechaDenuncia, $observaciones, $remitido, $estado, $fechaCarga, $idUsuario);
    $stmt->store_result();
    $resultado = array();
    if (mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'nroDocumento' => $nroDocumento,
                    'matricula' => $matricula,
                    'origenMatricula' => $origenMatricula,
                    'fechaDenuncia' => $fechaDenuncia,
                    'observaciones' => $observaciones,
                    'remitido' => $remitido,
                    'estado' => $estado,
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario
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
            $resultado['mensaje'] = "NO HAY REGISTRO DE DENUNCIAS DE FALSOS MEDICOS";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR BUSCANDO DENUNCIAS DE FALSOS MEDICOS";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerFalsosMedicosPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM falsosmedicos WHERE ID = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, $fechaDenuncia, $observaciones, $remitido, $estado, $fechaCarga, $idUsuario);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'id' => $id,
                'apellido' => $apellido,
                'nombre' => $nombre,
                'nroDocumento' => $nroDocumento,
                'matricula' => $matricula,
                'origenMatricula' => $origenMatricula,
                'fechaDenuncia' => $fechaDenuncia,
                'observaciones' => $observaciones,
                'remitido' => $remitido,
                'estado' => $estado,
                'fechaCarga' => $fechaCarga,
                'idUsuario' => $idUsuario
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el banco";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando banco";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function agregarFalsosMedicos($apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, $fechaDenuncia, $observaciones, $remitido) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO falsosmedicos 
                (Apellido, Nombre, NumeroDocumento, Matricula, OrigenMatricula, FechaDenuncia, Observaciones, Remitido, Estado, FechaCarga, IdUsuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'A', now(), ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssiissssi', $apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, $fechaDenuncia, $observaciones, $remitido, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO FALSO MEDICO CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR FALSO MEDICO";
            $resultado['clase'] = 'alert alert-error'; 
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
        $resultado['mensaje'] = "ERROR AL REGISTRAR FALSO MEDICO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function editarFalsosMedicos($idFalsoMedicos, $apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, $fechaDenuncia, $observaciones, $remitido, $estado) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "UPDATE falsosmedicos 
                SET Apellido = ?, 
                    Nombre = ?, 
                    NumeroDocumento = ?, 
                    Matricula = ?, 
                    OrigenMatricula = ?, 
                    FechaDenuncia = ?, 
                    Observaciones = ?, 
                    Remitido = ?, 
                    Estado = ?, 
                    FechaCarga = now(), 
                    IdUsuario = ?
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssiisssssii', $apellido, $nombre, $nroDocumento, $matricula, $origenMatricula, 
                $fechaDenuncia, $observaciones, $remitido, $estado, $_SESSION['user_id'], $idFalsoMedicos);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            if ($estado == 'A') {
                $resultado['mensaje'] = "SE ACTUALIZO FALSO MEDICO CORRECTAMENTE";
            } else {
                $resultado['mensaje'] = "SE ANULO FALSO MEDICO CORRECTAMENTE";
            }
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR FALSO MEDICO";
            $resultado['clase'] = 'alert alert-warning'; 
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
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR FALSO MEDICO";
        $resultado['clase'] = 'alert alert-warning'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}