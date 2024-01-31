<?php
function obtenerRecetariosPorId($idReceta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT recetas.*, especialidad.Especialidad
    FROM recetas
    LEFT JOIN especialidad ON(especialidad.Id = recetas.IdEspecialidad)
    WHERE recetas.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idReceta);
    $stmt->execute();
    $stmt->bind_result($id, $entrega, $fecha, $serie, $desde, $hasta, $cantidad, $idUsuario, $idEspecialidad, $estado, $idColegiado, $nombreEspecialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idReceta' => $id,
                    'entrega' => $entrega,
                    'fecha' => $fecha,
                    'serie' => $serie,
                    'desde' => $desde,
                    'hasta' => $hasta,
                    'cantidad' => $cantidad,
                    'idUsuario' => $idUsuario,
                    'idEspecialidad' => $idEspecialidad,
                    'estado' => $estado,
                    'nombreEspecialidad' => $nombreEspecialidad
            );
                
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontraron las recetas.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando recetas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerRecetariosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT recetas.*, especialidad.Especialidad
    FROM recetas
    LEFT JOIN especialidad ON(especialidad.Id = recetas.IdEspecialidad)
    WHERE recetas.IdColegiado = ? AND recetas.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $entrega, $fecha, $serie, $desde, $hasta, $cantidad, $idUsuario, $idEspecialidad, $estado, $idColegiado, $nombreEspecialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idReceta' => $id,
                    'entrega' => $entrega,
                    'fecha' => $fecha,
                    'serie' => $serie,
                    'desde' => $desde,
                    'hasta' => $hasta,
                    'cantidad' => $cantidad,
                    'idUsuario' => $idUsuario,
                    'idEspecialidad' => $idEspecialidad,
                    'estado' => $estado,
                    'nombreEspecialidad' => $nombreEspecialidad
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
            $resultado['mensaje'] = "El colegiado no tiene recetas.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando recetas del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEntregaReceta($serie, $desde, $hasta, $cantidad, $idEspecialidad, $idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="SELECT MAX(Entrega) FROM recetas WHERE IdColegiado = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idColegiado);
        $stmt->execute();
        $stmt->bind_result($entrega);
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $row = mysqli_stmt_fetch($stmt);
            } else {
                $entrega = 0;
            }
        } else {
            $entrega = 0;
        }
        $entrega++;
        
        //agrego la solicitud de certificado
        $sql="INSERT INTO recetas
            (IdColegiado, Entrega, Fecha, Serie, ReciboDesde, ReciboHasta, Cantidad, IdUsuario, IdEspecialidad) 
            VALUE (?, ?, date(now()), ?, ?, ?, ?, ".$_SESSION['user_id'].", ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisiiii', $idColegiado, $entrega, $serie, $desde, $hasta, $cantidad, $idEspecialidad);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['idReceta'] = $conect->insert_id;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GENERAR ENTREGA";
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

function borrarEntregaReceta($idReceta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="UPDATE recetas SET Estado = 'B' WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idReceta);
        $stmt->execute();
        $stmt->store_result();
        
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL BORRAR ENTREGA";
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

