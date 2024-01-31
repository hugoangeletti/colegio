<?php
function obtenerCertificadosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT sc.Id, sc.FechaEmision, sc.Presentado, tc.Detalle, sc.EnvioMail, sc.Mail, u.NombreCompleto, sc.Distrito
    FROM solicitudcertificados sc
    INNER JOIN tipocertificado tc ON tc.Id = sc.IdTipoCertificado
    LEFT JOIN usuario u ON u.Id = sc.IdUsuarioSolicitante
    WHERE sc.IdColegiado = ? AND sc.Estado <> 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $fechaEmision, $entregar, $tipoCertificado, $enviaMail, $mail, $usuarioSolicitante, $distrito);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoCertificado' => $id,
                    'fechaEmision' => $fechaEmision,
                    'tipoCertificado' => $tipoCertificado,
                    'entregar' => $entregar,
                    'enviaMail' => $enviaMail,
                    'mail' => $mail,
                    'usuarioSolicitante' => $usuarioSolicitante,
                    'distrito' => $distrito
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
            $resultado['mensaje'] = "El colegiado no tiene certificados emitidos.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando certificados del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerCertificadoPorId($idCertificado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM solicitudcertificados WHERE solicitudcertificados.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCertificado);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiado, $fechaSolicitud, $horaSolicitud, $idUsuarioSolicitante, $idTipoCertificado,
            $presentado, $distrito, $idUsuarioEmision, $fechaEmision, $horaEmision, $fechaEntrega, $estado, $estadoConTesoreria,
            $cuotasAdeudadas, $idNotaCambioDistrito, $conFirma, $conLeyendaTeso, $idColegiadoEspecialista, $envioMail, $mail, $hash_qr);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'idCertificado' => $id,
                'idColegiado' => $idColegiado,
                'fechaSolicitud' => $fechaSolicitud,
                'horaSolicitud' => $horaSolicitud,
                'idUsuarioSolicitante' => $idUsuarioSolicitante,
                'idTipoCertificado' => $idTipoCertificado,
                'presentado' => $presentado,
                'distrito' => $distrito,
                'idUsuarioEmision' => $idUsuarioEmision,
                'fechaEmision' => $fechaEmision,
                'horaEmision' => $horaEmision,
                'fechaEntrega' => $fechaEntrega,
                'estado' => $estado,
                'estadoConTesoreria' => $estadoConTesoreria,
                'cuotasAdeudadas' => $cuotasAdeudadas,
                'idNotaCambioDistrito' => $idNotaCambioDistrito,
                'conFirma' => $conFirma,
                'conLeyendaTeso' => $conLeyendaTeso,
                'idColegiadoEspecialista' => $idColegiadoEspecialista,
                'envioMail' => $envioMail,
                'mail' => $mail,
                'hash_qr' => $hash_qr
            );
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No existe el Certificado";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando certificado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarSolicitudCertificado($idColegiado, $idTipoCertificado, $presentado, $distrito, $codigoDeudor, $cuotasAdeudadas, $idNotaCambioDistrito, $conFirma, $conLeyendaTeso, $idColegiadoEspecialista, $enviaMail, $mail){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        $creado = date('YmdHis');
        $hash_qr = hashData($idColegiado.'_'.$matricula.'_'.$creado);

        //agrego la solicitud de certificado
        $sql="INSERT INTO solicitudcertificados
            (IdColegiado, FechaSolicitud, HoraSolicitud, IdUsuarioSolicitante, IdTipoCertificado, Presentado, 
            Distrito, IdUsuarioEmision, FechaEmision, HoraEmision, EstadoConTesoreria, CuotasAdeudadas,
            IdNotaCambioDistrito, ConFirma, ConLeyendaTeso, IdColegiadoEspecialista, EnvioMail, Estado, Mail, HashQR) 
            VALUE (?, date(now()), time(now()), ".$_SESSION['user_id'].", ?, ?, ?, ".$_SESSION['user_id'].", 
                date(now()), time(now()), ?, ?, ?, ?, ?, ?, ?, 'I', ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisssiississs', $idColegiado, $idTipoCertificado, $presentado, $distrito, $codigoDeudor,
                $cuotasAdeudadas, $idNotaCambioDistrito, $conFirma, $conLeyendaTeso, $idColegiadoEspecialista, $enviaMail, $mail, $hash_qr);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['idCertificado'] = $conect->insert_id;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GENERAR SOLICITUD DE CERTIFICADO -> ".mysqli_stmt_error($stmt);
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

function anularSolicitudCertificado($idCertificado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //agrego la solicitud de certificado
        $sql="UPDATE solicitudcertificados SET Estado = 'A' WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idCertificado);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "EL CERTIFICADO SE ANULO CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ANULAR CERTIFICADO";
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