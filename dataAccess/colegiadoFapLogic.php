<?php
function colegiadoTieneFap($idColegiado, $fecha){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT count(sapcaratula.Id) AS Cantidad
            FROM sapcaratula 
            INNER JOIN sapaconsejodetalle ON(sapaconsejodetalle.IdSAP = sapcaratula.Id)
            WHERE sapcaratula.IdColegiado = ?
            AND sapcaratula.Estado IN('E', 'A', 'M')
            AND sapaconsejodetalle.FechaAprobacion >= ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiado, $fecha);
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

function obtenerCausasFAPPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM sapcaratula WHERE IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $matricula, $fechaRecepcion, $fechaIngreso, $nombreCausa, $demandantes, $abogados, 
            $idJuzgado, $idTipoCausa, $idDepartamentoJudicial, $honorarios, $estado, $tipoSistema, $fechaHecho, 
            $lugarHecho, $ambito, $especialidad, $litigioSinGasto, $honorariosProfesionales, $gastosColegio, 
            $indemnizaciones, $montoAbonadoColegio, $condicion, $caratulaDefinitiva, $domicilioHecho, $telefonoHecho, 
            $fechaNotificacion, $lugarNotificacion, $recepcion, $inscriptoDistrito, $fechaInscripcion, $tieneCobertura, 
            $nombreCobertura, $coberturaDesde, $montoCobertura, $edad, $sexo, $otrosProfesionales, $plazoContestacion, 
            $domicilioReal, $domicilioProfesional, $domicilioNotificacion, $telefonoParticular, $celular, $mail, 
            $conCedula, $conFotoDemanda, $conFotoHC, $conFotoFicha, $notaDetalle, $conOtros, $recepciono, $fechaResolucion, 
            $numeroResolucion, $numeroCausa, $observaciones, $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id, 
                    'matricula' => $matricula, 
                    'fechaRecepcion' => $fechaRecepcion, 
                    'fechaIngreso' => $fechaIngreso, 
                    'nombreCausa' => $nombreCausa, 
                    'demandantes' => $demandantes, 
                    'abogados' => $abogados, 
                    'idJuzgado' => $idJuzgado, 
                    'idTipoCausa' => $idTipoCausa, 
                    'idDepartamentoJudicial' => $idDepartamentoJudicial, 
                    'honorarios' => $honorarios, 
                    'estado' => $estado,
                    'estadoDetalle' => obtenerEstadoFap($estado),
                    'tipoSistema' => $tipoSistema, 
                    'fechaHecho' => $fechaHecho, 
                    'lugarHecho' => $lugarHecho, 
                    'ambito' => $ambito, 
                    'especialidad' => $especialidad, 
                    'litigioSinGasto' => $litigioSinGasto, 
                    'honorariosProfesionales' => $honorariosProfesionales, 
                    'gastosColegio' => $gastosColegio, 
                    'indemnizaciones' => $indemnizaciones, 
                    'montoAbonadoColegio' => $montoAbonadoColegio, 
                    'condicion' => $condicion, 
                    'caratulaDefinitiva' => $caratulaDefinitiva, 
                    'domicilioHecho' => $domicilioHecho, 
                    'telefonoHecho' => $telefonoHecho, 
                    'fechaNotificacion' => $fechaNotificacion, 
                    'lugarNotificacion' => $lugarNotificacion, 
                    'recepcion' => $recepcion, 
                    'inscriptoDistrito' => $inscriptoDistrito, 
                    'fechaInscripcion' => $fechaInscripcion, 
                    'tieneCobertura' => $tieneCobertura, 
                    'nombreCobertura' => $nombreCobertura, 
                    'coberturaDesde' => $coberturaDesde, 
                    'montoCobertura' => $montoCobertura, 
                    'edad' => $edad, 
                    'sexo' => $sexo, 
                    'otrosProfesionales' => $otrosProfesionales, 
                    'plazoContestacion' => $plazoContestacion, 
                    'domicilioReal' => $domicilioReal, 
                    'domicilioProfesional' => $domicilioProfesional, 
                    'domicilioNotificacion' => $domicilioNotificacion, 
                    'telefonoParticular' => $telefonoParticular, 
                    'celular' => $celular, 
                    'mail' => $mail, 
                    'conDeuda' => $conCedula, 
                    'conFotoDemanda' => $conFotoDemanda, 
                    'conFotoHC' => $conFotoHC, 
                    'conFotoFicha' => $conFotoFicha, 
                    'notaDetalle' => $notaDetalle, 
                    'conOtros' => $conOtros, 
                    'recepciono' => $recepciono, 
                    'fechaResolucion' => $fechaResolucion, 
                    'numeroResolucion' => $numeroResolucion, 
                    'numeroCausa' => $numeroCausa, 
                    'observaciones' => $observaciones, 
                    'idColegiado' => $idColegiado
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
            $resultado['mensaje'] = "No se encontraron caratulas del FAP";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando caratulas del FAP";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}
