<?php
//accesos a tabla colegiado
function obtenerColegiadoContactoPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cc.IdColegiadoContacto, cc.TelefonoFijo, cc.TelefonoMovil, cc.CorreoElectronico, cc.FechaCarga, od.Nombre, (SELECT cmr.Id FROM colegiadomailrechazado cmr WHERE cmr.CorreoElectronico = cc.CorreoElectronico) AS Rechazado
        FROM colegiadocontacto cc
        INNER JOIN origendomicilio od ON(od.idOrigenDomicilio = cc.IdOrigen)
        WHERE cc.IdColegiado = ? and cc.IdEstado = 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoContacto, $telefonoFijo, $telefonoMovil, $correoElectronico, $fechaCarga, $origen, $mailRechazado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if (isset($mailRechazado) && $mailRechazado > 0) {
                $mailRechazado = TRUE;
            } else {
                $mailRechazado = FALSE;
            }
            $datos = array(
                    'idColegiadoContacto' => $idColegiadoContacto,
                    'telefonoFijo' => $telefonoFijo,
                    'telefonoMovil' => $telefonoMovil,
                    'email' => $correoElectronico,
                    'fechaCarga' => $fechaCarga, 
                    'origen' => $origen,
                    'mailRechazado' => $mailRechazado
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

function agregarColegiadoContacto($idColegiado, $telefonoFijo, $telefonoMovil, $mail){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
    
        //marco como anulado el domicilio actualmente activo y luego doy de alta el nuevo domicilio
        $sql="UPDATE colegiadocontacto
            SET IdEstado = 2 
            WHERE IdColegiado = ? AND IdEstado = 1";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO colegiadocontacto
                (IdColegiado, TelefonoFijo, TelefonoMovil, CorreoElectronico, IdEstado, FechaCarga, IdUsuario, IdOrigen) 
                VALUE (?, ?, ?, ?, 1, date(now()), ".$_SESSION['user_id'].", 2)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('isss', $idColegiado, $telefonoFijo, $telefonoMovil, $mail);
            $stmt->execute();
            $stmt->store_result();
            $resultado = array(); 
            if (mysqli_stmt_errno($stmt)==0) {
                $idColegiadoContacto = $conect->insert_id;
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL ACTUALIZAR CONTACTO. PASO 2";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR CONTACTO. PASO 1";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idColegiadoContacto.')';
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

function modificarMail($idColegiado, $mail){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
    
        //marco como anulado el domicilio actualmente activo y luego doy de alta el nuevo domicilio
        $sql="UPDATE colegiadocontacto
            SET CorreoElectronico = ? 
            WHERE IdColegiado = ? AND IdEstado = 1";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('si', $mail, $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $idColegiadoContacto = $conect->insert_id;
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR MAIL.";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idColegiadoContacto.')';
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