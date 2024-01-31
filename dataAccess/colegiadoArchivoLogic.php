<?php
//accesos a tabla colegiado
function obtenerColegiadoArchivo($idColegiado, $idTipo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Carpeta, Nombre
        FROM colegiadoarchivo
        WHERE IdColegiado = ?
        AND TipoArchivo = ?
        AND IdEstado = 1
        ORDER BY FechaCarga DESC
        LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiado, $idTipo);
    $stmt->execute();
    $stmt->bind_result($carpeta, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $resultado['estado'] = TRUE;
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'carpeta' => $carpeta,
                    'nombre' => $nombre
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
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

function agregarArchivo($idColegiado, $tipoArchivo, $nombreArchivo, $idEstado, $idRematriculacionColegiado, $idOrigen){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //anulamos si existe un registro anterior
        $sql="UPDATE colegiadoarchivo
            SET IdEstado = 2, FechaCarga = date(NOW()), IdUsuario = ".$_SESSION['user_id']."
            WHERE IdColegiado = ? AND TipoArchivo = ? AND IdEstado = 1";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('is', $idColegiado, $tipoArchivo);
        $stmt->execute();
        $stmt->store_result();

        //agrego la solicitud de certificado
        $sql="INSERT INTO colegiadoarchivo
            (IdColegiado, TipoArchivo, Nombre, IdEstado, IdRematriculacionColegiado, IdOrigen, 
            FechaCarga, IdUsuario) 
            VALUE (?, ?, ?, ?, ?, ?, date(now()), ".$_SESSION['user_id'].")";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('issiii', $idColegiado, $tipoArchivo, $nombreArchivo, $idEstado, $idRematriculacionColegiado,
                $idOrigen);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['idColegiadoArchivo'] = $conect->insert_id;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GENERAR COLEGIADO ARCHIVO";
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