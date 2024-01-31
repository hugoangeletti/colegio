<?php
function obtenerPlanPagoPorId($idPlanPago) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM planpagos WHERE planpagos.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPlanPago);
    $stmt->execute();
    $stmt->bind_result($idPlanPago, $fechaCreacion, $idUsuario, $importeTotal, $cuotas, $importePeriodoActual,
            $importePeriodos, $importeOtroPP, $recargoFinanciero, $estado, $importeSAP, $recargoExtensionCuotas,
            $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idPlanPago' => $idPlanPago,
                    'fechaCreacion' => $fechaCreacion,
                    'idUsuario' => $idUsuario,
                    'importeTotal' => $importeTotal,
                    'cuotas' => $cuotas,
                    'importePeriodoActual' => $importePeriodoActual,
                    'importePeriodos' => $importePeriodos,
                    'importeOtroPP' => $importeOtroPP,
                    'recargoFinanciero' => $recargoFinanciero,
                    'estado' => $estado,
                    'importeSAP' => $importeSAP,
                    'recargoExtensionCuotas' => $recargoExtensionCuotas,
                    'idColegiado' => $idColegiado
                 );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
    
}

function obtenerPlanPagoCuotaPorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM planpagoscuotas WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($idPlaPagoCuota, $idPlanPago, $cuota, $importe, $vencimiento, $fechaPago, $estado, $importeFap, $idRefinanciado, $segundoVencimiento, $segundoImporte, $idTipoEstadoCuota, $fechaActualizacion);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            if ($vencimiento < date('Y-m-d')){
                $importeActualizado = obtenerRecargoCuota($vencimiento, date('Y-m-d'), $importe);
            } else {
                $importeActualizado = $importe;
            }

            $datos = array(
                    'idPlaPagoCuota' => $idPlaPagoCuota,
                    'idPlanPago' => $idPlanPago,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'vencimiento' => $vencimiento,
                    'fechaPago' => $fechaPago,
                    'estado' => $estado,
                    'importeActualizado' => $importeActualizado,
                    'idRefinanciado' => $idRefinanciado,
                    'segundoVencimiento' => $segundoVencimiento,
                    'segundoImporte' => $segundoImporte,
                    'idTipoEstadoCuota' => $idTipoEstadoCuota,
                    'fechaActualizacion' => $fechaActualizacion
                 );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
    
}

function obtenerColegiadoPlanPago($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagos.Id, planpagoscuotas.Cuota, planpagoscuotas.Importe, planpagoscuotas.Vencimiento
            FROM planpagoscuotas 
            INNER JOIN planpagos ON(planpagos.Id = planpagoscuotas.IdPlanPagos)
            WHERE planpagos.IdColegiado = ?
            AND planpagos.Estado = 'A'
            AND planpagoscuotas.IdTipoEstadoCuota = 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idPlaPagoCuota, $cuota, $importe, $vencimiento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                //verifico el vencimiento, sino le calculo el recargo
                if ($vencimiento < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimiento, date('Y-m-d'), $importe);
                } else {
                    $importeActualizado = $importe;
                }
                $row = array (
                    'idPlanPago' => $idPlaPagoCuota,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'importeActualizado' => $importeActualizado,
                    'vencimiento' => $vencimiento
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
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function tienePlanPagos($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT count(planpagoscuotas.Cuota) AS Cantidad
            FROM planpagoscuotas 
            INNER JOIN planpagos ON(planpagos.Id = planpagoscuotas.IdPlanPagos)
            WHERE planpagos.IdColegiado = ?
            AND planpagos.Estado = 'A'
            AND planpagoscuotas.IdTipoEstadoCuota = 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0) {
                $resultado['estado'] = TRUE;
            }
        }
    }

    return $resultado;    
}

function obtenerPlanPagoPorIdColegiado($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagos.Id, planpagos.FechaCreacion, planpagos.ImporteTotal, planpagos.Cuotas, planpagos.Estado
            FROM planpagos
            WHERE planpagos.IdColegiado = ? AND planpagos.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idPlaPago, $fechaCreacion, $importe, $cuotas, $estado);
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
                    'idPlanPago' => $idPlaPago,
                    'cuotas' => $cuotas,
                    'importe' => $importe,
                    'fechaCreacion' => $fechaCreacion,
                    'estado' => $estado
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
            $resultado['mensaje'] = "No hay Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerPlanPagosCuotasPorIdPlanPago($idPlanPago){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagoscuotas.Id, planpagoscuotas.Cuota, planpagoscuotas.Importe, 
            planpagoscuotas.Vencimiento, planpagoscuotas.IdTipoEstadoCuota, planpagoscuotas.FechaPago
            FROM planpagoscuotas 
            WHERE planpagoscuotas.IdPlanPagos = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPlanPago);
    $stmt->execute();
    $stmt->bind_result($idPlaPagoCuota, $cuota, $importe, $vencimiento, $estado, $fechaPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                //verifico el vencimiento, sino le calculo el recargo si no esta paga
                if ($estado == 1 && $vencimiento < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimiento, date('Y-m-d'), $importe);
                    $vencimiento = sumarRestarSobreFecha(date('Y-m-d'), 7, 'day', '+');
                } else {
                    $importeActualizado = $importe;
                }
                $row = array (
                    'idPlanPagoCuota' => $idPlaPagoCuota,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'importeActualizado' => $importeActualizado,
                    'vencimiento' => $vencimiento,
                    'fechaPago' => $fechaPago,
                    'estado' => $estado
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
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerDeudaPlanPagosPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagoscuotas.Id, planpagoscuotas.Importe, planpagoscuotas.Vencimiento, planpagoscuotas.Cuota,
        planpagoscuotas.IdPlanPagos
        FROM planpagoscuotas
        INNER JOIN planpagos ON(planpagos.Id = planpagoscuotas.IdPlanPagos)
        LEFT JOIN pagosnoregistrados ON(pagosnoregistrados.Recibo = planpagoscuotas.Id AND pagosnoregistrados.TipoPago='P')
        WHERE planpagos.IdColegiado = ?
        AND planpagoscuotas.IdTipoEstadoCuota=1
        AND pagosnoregistrados.Id IS NULL
        ORDER BY planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idPlanPagosCuotas, $importe, $vencimiento, $cuota, $idPlanPagos);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($vencimiento < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimiento, date('Y-m-d'), $importe);
                } else {
                    $importeActualizado = $importe;
                }
                $row = array (
                    'idPlanPagosCuotas' => $idPlanPagosCuotas,
                    'importe' => $importe,
                    'importeActualizado' => $importeActualizado,
                    'vencimiento' => $vencimiento,
                    'cuota' => $cuota,
                    'idPlanPagos' => $idPlanPagos
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
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function agregarColegiadoPlanPagos($idColegiado, $deudaPlanPago, $deudaAnterior, $cuotas, $totalFinanciar, $valorCuota, $recargoExtension){
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        //agregamos el plan de pagos
        $sql = "INSERT INTO planpagos (FechaCreacion, IdUsuario, ImporteTotal, Cuotas,  
            ImportePeriodos, ImporteOtroPP, Estado, RecargoExtensionCuotas, IdColegiado)
            VALUES (date(now()), ?, ?, ?, ?, ?, 'A', ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isisssi', $_SESSION['user_id'], $totalFinanciar, $cuotas, $deudaAnterior, $deudaPlanPago, $recargoExtension, $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $idPlanPago = $conect->insert_id;
            //marcamos las cuotas que se incluyen, de cuotas de colegiacion y si tiene de plan anterior
            $sql = "UPDATE colegiadodeudaanualcuotas, colegiadodeudaanual
                    SET colegiadodeudaanualcuotas.IdPlanPago = ?, 
                        colegiadodeudaanualcuotas.Estado = 3
                    WHERE colegiadodeudaanual.IdColegiado = ?
                        AND colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual
                        AND colegiadodeudaanual.Periodo <> ?
                        AND colegiadodeudaanualcuotas.Estado = 1
                        AND (colegiadodeudaanualcuotas.IdPlanPago = 0 
                            OR colegiadodeudaanualcuotas.IdPlanPago is null)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iii', $idPlanPago, $idColegiado, $_SESSION['periodoActual']);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt) != 0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DE COLEGIACION";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
            
            if ($resultado['estado']) {
                $sql = "UPDATE planpagoscuotas, planpagos
                        SET planpagoscuotas.IdRefinanciado = ?, 
                            planpagoscuotas.IdTipoEstadoCuota = 3,
                            planpagos.Estado = 'R'
                        WHERE planpagos.IdColegiado = ?
                            AND planpagos.Id = planpagoscuotas.IdPlanPagos
                            AND planpagoscuotas.IdTipoEstadoCuota = 1
                            AND (planpagoscuotas.IdRefinanciado = 0 
                                OR planpagoscuotas.IdRefinanciado is null)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idPlanPago, $idColegiado);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DEL PLAN DE PAGOS ANTERIOR";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }

                if ($resultado['estado']) { 
                    //generamos las cuotas
                    $cuota = 1;
                    $fecha = new DateTime();
                    date_add($fecha, date_interval_create_from_date_string('10 days'));
                    $fechaVencimiento = $fecha->format('Y-m-d');
                    $segundoImporte = round($valorCuota * 1.015, 0);
                    while ($cuota <= $cuotas) {
                        date_add($fecha, date_interval_create_from_date_string('10 days'));
                        $segundoVencimiento = $fecha->format('Y-m-d');
                        $sql = "INSERT INTO planpagoscuotas 
                            (IdPlanPagos, Cuota, Importe, Vencimiento, SegundoVencimiento, SegundoImporte, IdTipoEstadoCuota)
                            VALUES (?, ?, ?, ?, ?, ?, 1)";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('iissss', $idPlanPago, $cuota, $valorCuota, $fechaVencimiento, $segundoVencimiento, $segundoImporte);
                        $stmt->execute();
                        $stmt->store_result();
                        if (mysqli_stmt_errno($stmt)!=0) {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DEL PLAN DE PAGOS ANTERIOR";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                            break;
                        }
                        $fecha = new DateTime($fechaVencimiento);
                        $intervalo = new DateInterval('P1M');

                        $fecha->add($intervalo);
                        $fechaVencimiento = $fecha->format('Y-m-d');
                        $cuota++;
                    }            
                    
                    if ($resultado['estado']) { 
                        $resultado['idPlanPago'] = $idPlanPago;
                        $resultado['mensaje'] = "OK";
                        $resultado['clase'] = 'alert alert-success'; 
                        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
                    }
                }
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GENERAR PLAN DE PAGOS";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idPlanPago.')';
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
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL GENERAR PLAN DE PAGOS";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }

}

function anularColegiadoPlanPagos($idPlanPago){
    $conect = conectar();
    try {
        //$conect->autocommit(FALSE);
        mysqli_autocommit($conect, FALSE);
        $resultado['estado'] = TRUE;
        mysqli_set_charset( $conect, 'utf8');
        //marcamos las cuotas que se incluyen, de cuotas de colegiacion y si tiene de plan anterior
        $sql = "UPDATE colegiadodeudaanualcuotas
                SET colegiadodeudaanualcuotas.IdPlanPago = NULL, 
                    colegiadodeudaanualcuotas.Estado = 1
                WHERE colegiadodeudaanualcuotas.IdPlanPago = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idPlanPago);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) != 0) {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DE COLEGIACION";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
            
        if ($resultado['estado']) {
            $sql = "UPDATE planpagoscuotas
                    SET planpagoscuotas.IdRefinanciado = NULL, 
                        planpagoscuotas.IdTipoEstadoCuota = 1
                    WHERE planpagoscuotas.IdRefinanciado = ?
                        AND planpagoscuotas.IdTipoEstadoCuota = 3";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $idPlanPago);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt) != 0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DEL PLAN DE PAGOS ANTERIOR";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }

            if ($resultado['estado']) {
                $sql = "UPDATE planpagos
                        SET planpagos.Estado = 'N'
                        WHERE planpagos.Id = ?";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('i', $idPlanPago);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DEL PLAN DE PAGOS ANTERIOR";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
                
                if ($resultado['estado']) { 
                    //borramos las cuotas
                    $sql = "DELETE FROM planpagoscuotas 
                        WHERE planpagoscuotas.IdPlanPagos = ?";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('i', $idPlanPago);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt) != 0) {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL ELIMINAR CUOTAS DEL PLAN DE PAGOS";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                }
            }
        } 

        if ($resultado['estado']) {
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
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
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL GENERAR PLAN DE PAGOS";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function obtenerCuotasEnPlanPagos($idPlanPago) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT 'C', colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota, colegiadodeudaanualcuotas.Importe
        FROM colegiadodeudaanualcuotas
        INNER JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
        WHERE colegiadodeudaanualcuotas.IdPlanPago = ?)
        UNION
        (SELECT 'PP', planpagoscuotas.IdPlanPagos AS Periodo, planpagoscuotas.Cuota, planpagoscuotas.Importe
        FROM planpagoscuotas 
        WHERE planpagoscuotas.IdRefinanciado = ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idPlanPago, $idPlanPago);
    $stmt->execute();
    $stmt->bind_result($tipo, $periodo, $cuota, $importe);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $periodoAnterior = 0;
            $tipoAnterior = '';
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($periodo <> $periodoAnterior) {
                    if ($periodoAnterior <> 0) {
                        if ($tipoAnterior == 'C') {
                            $elTipo = 'Periodo: ';
                        } else {
                            $elTipo = 'Plan de Pagos: ';
                        }
                        $lineaPeriodo = $elTipo.'<b>'.$periodoAnterior.'</b> - cuotas: <b>'.$cuotas.'</b>';
                        array_push($datos, $lineaPeriodo);
                    }
                    $periodoAnterior = $periodo;
                    $tipoAnterior = $tipo;
                    $cuotas = '';
                }
                $cuotas .= $cuota.'-';
            }
            if ($periodoAnterior <> 0) {
                if ($tipoAnterior == 'C') {
                    $elTipo = 'Periodo: ';
                } else {
                    $elTipo = 'Plan de Pagos: ';
                }
                $lineaPeriodo = $elTipo.'<b>'.$periodoAnterior.'</b> - cuotas: <b>'.$cuotas.'</b>';
                array_push($datos, $lineaPeriodo);
            }            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de Plan de pagos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Plan de pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}