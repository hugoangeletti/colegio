<?php
function obtenerNotificacionDeudaPorId($idNotificacion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
        //agrego en notificacion los datos del colegiado
        $sql = "SELECT notificacioncolegiadodeuda.*, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota, 
                colegiadodeudaanualcuotas.Importe, colegiadodeudaanualcuotas.FechaVencimiento, 
                IF(notificacioncolegiadodeuda.IdColegiadoDeudaAnualCuota is null, 'P', 'C') AS Origen, 
                planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota, planpagoscuotas.Importe, planpagoscuotas.Vencimiento
                FROM notificacioncolegiadodeuda
                INNER JOIN notificacioncolegiado ON(notificacioncolegiado.IdNotificacionColegiado = notificacioncolegiadodeuda.IdNotificacionColegiado)
                LEFT JOIN colegiadodeudaanualcuotas ON(colegiadodeudaanualcuotas.Id = notificacioncolegiadodeuda.IdColegiadoDeudaAnualCuota)
                LEFT JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
                LEFT JOIN planpagoscuotas ON(planpagoscuotas.Id = notificacioncolegiadodeuda.IdPlanPagosCuota)
                WHERE notificacioncolegiado.IdNotificacion = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idNotificacion);
        $stmt->execute();
        $stmt->bind_result($idNotificacionColegiadoDeuda, $idNotificacionColegiado, $idColegiadoDeudaAnualCuota, $idPlanPagosCuota, $valorActualizado, $estado, $periodo, $cuota, $importe, $vencimiento, $origen, $idPlanPagos, $cuotaPlanPagos, $importePlanPagos, $vencimientoPlanPagos);
        $stmt->store_result();

        if(mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $datos = array();
                while (mysqli_stmt_fetch($stmt)) {
                    $row = array (
                        'idNotificacionColegiadoDeuda' => $idNotificacionColegiadoDeuda,
                        'idNotificacionColegiado' => $idNotificacionColegiado,
                        'idColegiadoDeudaAnualCuota' => $idColegiadoDeudaAnualCuota,
                        'idPlanPagosCuota' => $idPlanPagosCuota,
                        'valorActualizado' => $valorActualizado,
                        'periodo' => $periodo,
                        'cuota' => $cuota,
                        'importe' => $importe,
                        'vencimiento' => $vencimiento,
                        'origen' => $origen,
                        'idPlanPagos' => $idPlanPagos,
                        'cuotaPlanPagos' => $cuotaPlanPagos,
                        'importePlanPagos' => $importePlanPagos,
                        'vencimientoPlanPagos' => $vencimientoPlanPagos
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
                $resultado['mensaje'] = "No se encontro notificacion del colegiado";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando notificacion del colegiado";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        return $resultado;
}

function obtenerIdNotificacionVigente($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT MAX(notificacion.IdNotificacion) FROM notificacion
            INNER JOIN notificacioncolegiado ON(notificacioncolegiado.IdNotificacion = notificacion.IdNotificacion)
            WHERE notificacioncolegiado.IdColegiado = ? AND notificacion.FechaVencimiento > DATE(NOW())
            AND notificacion.Estado IN('A')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idNotificacion);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['mensaje'] = "OK";
            $resultado['idNotificacion'] = $idNotificacion;
        } else {
            $resultado['idNotificacion'] = 0;
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando notificacion del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadoNotificacionDeuda($rango){
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT notificacion.IdNotificacion, notificacioncolegiado.IdNotificacionColegiado, 
            notificacioncolegiado.IdColegiado,
            colegiado.Matricula, persona.Sexo, persona.Apellido, persona.Nombres, 
            colegiadocontacto.CorreoElectronico, 
            (COUNT(colegiadodeudaanualcuotas.id) + COUNT(planpagoscuotas.Importe)) as cantidad
        FROM notificacion
        INNER JOIN notificacioncolegiado ON(notificacioncolegiado.IdNotificacion = notificacion.IdNotificacion 
            AND notificacioncolegiado.TipoEnvio = 'E')
        INNER JOIN colegiado ON(colegiado.Id = notificacioncolegiado.IdColegiado)
        INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado 
            AND tipomovimiento.Estado = 'A')
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
            AND colegiadocontacto.IdEstado = 1 
            AND colegiadocontacto.CorreoElectronico is not null 
            AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
            AND colegiadocontacto.CorreoElectronico <> '')
        LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = notificacioncolegiado.IdColegiado 
            AND enviomaildiariocolegiado.IdReferencia = notificacioncolegiado.IdNotificacionColegiado)
        LEFT JOIN colegiadodeudaanual ON(colegiadodeudaanual.IdColegiado = colegiado.Id)
        LEFT JOIN colegiadodeudaanualcuotas ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual  
            AND colegiadodeudaanualcuotas.Estado = 1 
            AND colegiadodeudaanualcuotas.SegundoVencimiento < ADDDATE(date(now()), INTERVAL -5 DAY))
        LEFT JOIN planpagos ON(planpagos.IdColegiado = colegiado.Id)
        LEFT JOIN planpagoscuotas ON(planpagoscuotas.IdPlanPagos = planpagos.Id 
            AND planpagoscuotas.IdTipoEstadoCuota = 1 
            AND planpagoscuotas.SegundoVencimiento < ADDDATE(date(now()), INTERVAL -5 DAY))
        LEFT JOIN agremiacionesdebito ON(agremiacionesdebito.IdColegiado = colegiadodeudaanual.IdColegiado)
        WHERE notificacion.Estado = 'E' 
            AND enviomaildiariocolegiado.Id IS NULL 
            AND agremiacionesdebito.IdColegiado is null
        GROUP BY colegiado.Matricula
        HAVING cantidad > 0
        ORDER BY colegiado.Matricula
        LIMIT ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $rango);
    $stmt->execute();
    $stmt->bind_result($idNotificacion, $idNotificacionColegiado, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail, $cantidad);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idNotificacion' => $idNotificacion,
                    'idReferencia' => $idNotificacionColegiado,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'sexo' => $sexo,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'mail' => $mail,
                    'cantidadCuotasAdeudadas' => $cantidad
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
            $resultado['mensaje'] = "No se encontro notificacion del colegiado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando notificacion del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function generarNotificacionDeudores($idNotificacionNota, $fechaVencimiento, $matricula, $fechaCortePago, $periodoDesde, $periodoHasta, $filtroDeudor, $cantidadCuotas){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
        //agrego en notificacion los datos del colegiado
        $sql = "INSERT INTO notificacion 
            (IdNotificacionNota, FechaCreacion, IdUsuario, Estado, FechaVencimiento, Matricula, CuotasAdeudadas, PeriodoDesde, PeriodoHasta)
            VALUES (?, date(now()), ?, 'A', ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisiiii', $idNotificacionNota, $_SESSION['user_id'], $fechaVencimiento, $matricula, $cantidadCuotas, $periodoDesde, $periodoHasta);
        $stmt->execute();
        $stmt->store_result();
        
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego los datos del tipo de notificacion
            $idNotificacion = $conect->insert_id;
            $sql = "INSERT INTO notificaciondeuda (IdNotificacion, FechaCortePago, FechaVencimiento, PeriodoDesde, PeriodoHasta, FiltroDeudores)
            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('issiis', $idNotificacion, $fechaCortePago, $fechaVencimiento, $periodoDesde, $periodoHasta, $filtroDeudor);
            $stmt->execute();
            $stmt->store_result();

            $resultado = array();
            if(mysqli_stmt_errno($stmt)==0) {
                //genero el detalle de la deuda integrada en la notificacion
                //si viene la matricula inicializada, debo generar de ese solo matriculado, sino
                //genero de todos los matriculados
                if (isset($matricula)) {
                    $resColegiado = obtenerIdColegiado($matricula);
                    if ($resColegiado['estado']) {
                        $idColegiado = $resColegiado['idColegiado'];
                        $resGeneraDetalleNotificacion = generarNotificacionDetalle($conect, $idNotificacion, $idColegiado, $periodoHasta, $fechaVencimiento);
                        if ($resGeneraDetalleNotificacion['estado']) {
                            $resultado['estado'] = true;
                            $resultado['mensaje'] = "OK";
                            $resultado['clase'] = 'alert alert-success'; 
                            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
                        } else {
                            $resultado['estado'] = false;
                            $resultado['mensaje'] = "(".$idColegiado.") Mat.".$matricula.". Error al generar detalle de notificacion - ".$resGeneraDetalleNotificacion['mensaje'];
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }
                    } else {
                        $resultado['estado'] = false;
                        $resultado['mensaje'] = "Error buscando IdColegiado";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resColegiado = obtenerIdColegiadoConDeuda($periodoHasta, $fechaVencimiento, $cantidadCuotas);
                    if ($resColegiado['estado']) {
                        $idColegiado = $resColegiado['idColegiado'];
                        $resGeneraDetalleNotificacion = generarNotificacionDetalle($conect, $idNotificacion, $idColegiado, $periodoHasta, $fechaVencimiento);
                    } else {
                        $resultado['estado'] = false;
                        $resultado['mensaje'] = "Error buscado colegiados con deuda";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                }
            } else {
                $resultado['estado'] = false;
                $resultado['mensaje'] = "Error agregando NotificacionDeuda";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error agregando Notificacion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        if ($resultado['estado']) {
            $resultado['idNotificacion'] = $idNotificacion;
            $resultado['mensaje'] .= '('.$idNotificacion.')';
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

function generarNotificacionDetalle($conect,$idNotificacion, $idColegiado, $periodoHasta, $fechaVencimiento) {
    //$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
//    try {
//        /* Autocommit false para la transaccion */
//        $conect->autocommit(FALSE);
        //agrego notificacioncolegiado
        $sql = 'INSERT INTO notificacioncolegiado (IdNotificacion, IdColegiado)
                VALUES (?, ?)';
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idNotificacion, $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        $resultado['estado'] = true;
        if(mysqli_stmt_errno($stmt)==0) {
            $idNotificacionColegiado = $conect->insert_id;
            
            $sql = "(SELECT 'C' AS Origen, colegiadodeudaanualcuotas.Id AS Indice, colegiadodeudaanualcuotas.FechaVencimiento, 
                    colegiadodeudaanualcuotas.Importe
                    FROM colegiadodeudaanualcuotas
                    INNER JOIN colegiadodeudaanual 
                       ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
                    WHERE colegiadodeudaanual.IdColegiado = ?
                    AND colegiadodeudaanual.Periodo <= ?  AND colegiadodeudaanualcuotas.FechaVencimiento <= date(now())
                    AND colegiadodeudaanual.Estado = 'A'
                    AND colegiadodeudaanualcuotas.Estado = 1)

                    UNION ALL

                    (SELECT 'P' AS Origen, planpagoscuotas.Id, planpagoscuotas.Vencimiento, planpagoscuotas.Importe
                    FROM planpagoscuotas
                    INNER JOIN planpagos 
                       ON(planpagos.Id = planpagoscuotas.IdPlanPagos)
                    WHERE planpagos.IdColegiado = ?
                    AND planpagos.Estado = 'A'
                    AND planpagoscuotas.IdTipoEstadoCuota = 1)
                    ORDER BY Origen, Indice";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iii', $idColegiado, $periodoHasta, $idColegiado);
            $stmt->execute();
            $stmt->bind_result($origen, $indice, $vencimiento, $importe);
            $stmt->store_result();

            if(mysqli_stmt_errno($stmt)==0) {
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $datos = array();
                    while (mysqli_stmt_fetch($stmt)) {
                        //agrego el detalle de la notificacion
                        if ($vencimiento < date('Y-m-d')) {
                            $importeActualizado = obtenerRecargoCuota($vencimiento, $fechaVencimiento, $importe);
                        } else {
                            $importeActualizado = $importe;
                        }
                        if ($origen == 'C') {
                            $idColegiadoDeudaAnualCuota = $indice;
                            $idPlanPagosCuota = NULL;
                        } else {
                            $idColegiadoDeudaAnualCuota = NULL;
                            $idPlanPagosCuota = $indice;
                        }
                        
                        $sql1 = "INSERT INTO notificacioncolegiadodeuda (IdNotificacionColegiado, IdColegiadoDeudaAnualCuota, IdPlanPagosCuota, ValorActualizado)
                                VALUES (? , ?, ?, ?)";
                        $stmt1 = $conect->prepare($sql1);
                        $stmt1->bind_param('iiii', $idNotificacionColegiado, $idColegiadoDeudaAnualCuota, $idPlanPagosCuota, $importeActualizado);
                        $stmt1->execute();
                        $stmt1->store_result();
                        
                        if(mysqli_stmt_errno($stmt)>0) {
                            $resultado['estado'] = false;
                            $resultado['mensaje'] = "Error agregando detalle de notificacion del colegiado";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }
                    }
                    if ($resultado['estado']) {
                        $resultado['mensaje'] = "OK";
                        $resultado['clase'] = 'alert alert-success'; 
                        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
                    }
                } else {
                    $resultado['estado'] = false;
                    $resultado['mensaje'] = "Error agregando notificacion del colegiado";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = false;
                $resultado['mensaje'] = "No se encontraro deudores";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "(".$idNotificacion."-".$idColegiado."). Error al agregar Notificacion del Colegiado";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    return $resultado;

//        if ($resultado['estado']) {
//            $resultado['idNotificacion'] = $idNotificacion;
//            $conect->commit();
//            desconectar($conect);
//            return $resultado;
//        } else {
//            $conect->rollback();
//            desconectar($conect);
//            return $resultado;
//        }
//    } catch (mysqli_sql_exception $e) {
//        $conect->rollback();
//        desconectar($conect);
//        return $resultado;
//    }     
}

/* Se genera para dividir la nota en varias segun el total a cobrar, debe ser menor a $10.000
hasta que se pueda cambiar el codigo de barras de pago facil 
9/2/2022
*/
function generarNotificacionDetallePorImporte($conect,$idNotificacion, $idColegiado, $periodoHasta, $fechaVencimiento) {
    //$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
        $sql = "(SELECT 'C' AS Origen, colegiadodeudaanualcuotas.Id AS Indice, colegiadodeudaanualcuotas.FechaVencimiento, 
                colegiadodeudaanualcuotas.Importe
                FROM colegiadodeudaanualcuotas
                INNER JOIN colegiadodeudaanual 
                   ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
                WHERE colegiadodeudaanual.IdColegiado = ?
                AND colegiadodeudaanual.Periodo <= ?  AND colegiadodeudaanualcuotas.FechaVencimiento <= date(now())
                AND colegiadodeudaanual.Estado = 'A'
                AND colegiadodeudaanualcuotas.Estado = 1)

                UNION ALL
                
                (SELECT 'P' AS Origen, planpagoscuotas.Id, planpagoscuotas.Vencimiento, planpagoscuotas.Importe
                FROM planpagoscuotas
                INNER JOIN planpagos 
                   ON(planpagos.Id = planpagoscuotas.IdPlanPagos)
                WHERE planpagos.IdColegiado = ?
                AND planpagos.Estado = 'A'
                AND planpagoscuotas.IdTipoEstadoCuota = 1)
                ORDER BY Origen, Indice";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iii', $idColegiado, $periodoHasta, $idColegiado);
        $stmt->execute();
        $stmt->bind_result($origen, $indice, $vencimiento, $importe);
        $stmt->store_result();

        if(mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $datos = array();
                $totalDeuda = 0;
                while (mysqli_stmt_fetch($stmt)) {
                    if (!isset($idNotificacionColegiado)) {
                        //agrego notificacioncolegiado
                        $sql = 'INSERT INTO notificacioncolegiado (IdNotificacion, IdColegiado)
                                VALUES (?, ?)';
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('ii', $idNotificacion, $idColegiado);
                        $stmt->execute();
                        $stmt->store_result();
                        $resultado['estado'] = true;
                        if(mysqli_stmt_errno($stmt)==0) {
                            $idNotificacionColegiado = $conect->insert_id;
                        } else {
                            $resultado['estado'] = false;
                            $resultado['mensaje'] = "Error agregando notificacion del colegiado";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }            
                    }
                    if ($resultado['estado']) {
                        //agrego el detalle de la notificacion
                        if ($vencimiento < date('Y-m-d')) {
                            $importeActualizado = obtenerRecargoCuota($vencimiento, $fechaVencimiento, $importe);
                        } else {
                            $importeActualizado = $importe;
                        }
                        if ($origen == 'C') {
                            $idColegiadoDeudaAnualCuota = $indice;
                            $idPlanPagosCuota = NULL;
                        } else {
                            $idColegiadoDeudaAnualCuota = NULL;
                            $idPlanPagosCuota = $indice;
                        }
                        
                        $sql1 = "INSERT INTO notificacioncolegiadodeuda (IdNotificacionColegiado, IdColegiadoDeudaAnualCuota, IdPlanPagosCuota, ValorActualizado)
                                VALUES (? , ?, ?, ?)";
                        $stmt1 = $conect->prepare($sql1);
                        $stmt1->bind_param('iiii', $idNotificacionColegiado, $idColegiadoDeudaAnualCuota, $idPlanPagosCuota, $importeActualizado);
                        $stmt1->execute();
                        $stmt1->store_result();
                        
                        if(mysqli_stmt_errno($stmt)>0) {
                            $resultado['estado'] = false;
                            $resultado['mensaje'] = "Error agregando detalle de notificacion del colegiado";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }
                    }
                }
                if ($resultado['estado']) {
                    $resultado['mensaje'] = "OK";
                    $resultado['clase'] = 'alert alert-success'; 
                    $resultado['icono'] = 'glyphicon glyphicon-ok'; 
                }
            } else {
                $resultado['estado'] = false;
                $resultado['mensaje'] = "No se encontro deuda";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "(".$idNotificacion."-".$idColegiado."). Error al agregar Notificacion del Colegiado";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }

        if ($resultado['estado']) {
            $resultado['idNotificacion'] = $idNotificacion;
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

/* 
        !busco la deuda y se la cargo en el detalle
        clear(paraconsulta2)
        paraconsulta2{prop:sql}='select colegiadodeudaanualcuotas.Id, colegiadodeudaanualcuotas.FechaVencimiento'|
            &', colegiadodeudaanualcuotas.Importe'|
            &' from colegiadodeudaanualcuotas'|
            &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
            &' where colegiadodeudaanual.IdColegiado='& col:Id|
            &' and colegiadodeudaanual.Periodo<'& Glo:PeriodoActual|
            &' and colegiadodeudaanualcuotas.Estado=1'|
            &' order by colegiadodeudaanualcuotas.Id'

        if errorcode() then
            stop(fileerror() &' select colegiadodeudaanualcuotas.Id, colegiadodeudaanualcuotas.FechaVencimiento'|
            &', colegiadodeudaanualcuotas.Importe'|
            &' from colegiadodeudaanualcuotas'|
            &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
            &' where colegiadodeudaanual.IdColegiado='& col:Id|
            &' and colegiadodeudaanual.Periodo<'& Glo:PeriodoActual|
            &' and colegiadodeudaanualcuotas.Estado=1'|
            &' order by colegiadodeudaanualcuotas.Id')
        end

        loop
            next(paraconsulta2)
            if errorcode() then break.

            notiColDeu:IdColegiadoDeudaAnualCuota = par2:c1
            cdacu:FechaVencimiento = date(sub(par2:c2,6,2),sub(par2:c2,9,2),sub(par2:c2,1,4))
            cdacu:Importe = par2:c3
            notiColDeu:ValorActualizado = cdacu:Importe + CalculaRecargoCuotaFechaVariable(cdacu:FechaVencimiento, cdacu:Importe, notDeu:FechaVencimiento)
            
            parainsertar{prop:sql}='insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdColegiadoDeudaAnualCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdColegiadoDeudaAnualCuota &', '& notiColDeu:ValorActualizado &')'
            if errorcode() then
                stop(fileerror() & ' insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdColegiadoDeudaAnualCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdColegiadoDeudaAnualCuota &', '& notiColDeu:ValorActualizado &')')
            end
        end

        !busca deuda de plan de pagos de la misma matricula
        clear(paraconsulta2)
        paraconsulta2{prop:sql}='select planpagoscuotas.Id, planpagoscuotas.Vencimiento'|
            &', planpagoscuotas.Importe'|
            &' from planpagoscuotas'|
            &' inner join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
            &' where planpagos.IdColegiado ='& col:Id|
            &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL)'|
            &' and planpagoscuotas.IdRefinanciado is NULL'|
            &' order by planpagoscuotas.Id'

        if errorcode() then
            stop(fileerror() &' select planpagoscuotas.Id, planpagoscuotas.Vencimiento'|
            &', planpagoscuotas.Importe'|
            &' from planpagoscuotas'|
            &' inner join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
            &' where planpagos.IdColegiado ='& col:Id|
            &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL)'|
            &' and planpagoscuotas.IdRefinanciado is NULL'|
            &' order by planpagoscuotas.Id')
        end

        loop
            next(paraconsulta2)
            if errorcode() then break.

            notiColDeu:IdPlanPagosCuota = par2:c1
            PPCuo:Vencimiento = date(sub(par2:c2,6,2),sub(par2:c2,9,2),sub(par2:c2,1,4))
            PPCuo:Importe = par2:c3
            notiColDeu:ValorActualizado = PPCuo:Importe + CalculaRecargoPlanPago(PPCuo:Importe, PPCuo:Vencimiento)
            
            parainsertar{prop:sql}='insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdPlanPagosCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdPlanPagosCuota &', '& notiColDeu:ValorActualizado &')'
            if errorcode() then
                stop(fileerror() & ' insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdPlanPagosCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdPlanPagosCuota &', '& notiColDeu:ValorActualizado &')')
            end
        end
    end

    !ahora busco los deudores de solo plan de pagos
    clear(paraconsulta)

    !deuda de plan de pagos
    paraconsulta{prop:sql}='select count(planpagoscuotas.Id)'|
        &' from planpagoscuotas'|
        &' inner join planpagos on(planpagoscuotas.IdPlanPagos = planpagos.Id)'|
        &' left join notificacioncolegiado on(notificacioncolegiado.IdColegiado = planpagos.IdColegiado and notificacioncolegiado.IdNotificacion = '& noti:IdNotificacion &')'|
        &' where planpagos.IdColegiado = '& col:Id|
        &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL) and planpagoscuotas.IdRefinanciado is NULL'|
        &' and notificacioncolegiado.IdNotificacionColegiado is null'|
        &' group by planpagos.IdColegiado'

    if errorcode() then
        stop(fileerror() &' select count(planpagoscuotas.Id)'|
        &' from planpagoscuotas'|
        &' inner join planpagos on(planpagoscuotas.IdPlanPagos = planpagos.Id)'|
        &' left join notificacioncolegiado on(notificacioncolegiado.IdColegiado = planpagos.IdColegiado and notificacioncolegiado.IdNotificacion = '& noti:IdNotificacion &')'|
        &' where planpagos.IdColegiado = '& col:Id|
        &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL) and planpagoscuotas.IdRefinanciado is NULL'|
        &' and notificacioncolegiado.IdNotificacionColegiado is null'|
        &' group by planpagos.IdColegiado')
    end

    loop
        next(paraconsulta)
        if errorcode() then break.

        !es deudoor, imprimo la nota y la deuda.
        parainsertar{prop:sql}='insert into notificacioncolegiado (IdNotificacion, IdColegiado, Estado)'|
            &' Values('& noti:IdNotificacion &', '& col:Id &', "A")'
        if errorcode() then
            stop(fileerror() & ' insert into notificacioncolegiado (IdNotificacion, IdColegiado, Estado)'|
            &' Values('& noti:IdNotificacion &', '& col:Id &', "A")')
        end

        clear(parainsertar)
        parainsertar{prop:sql}='select max(IdNotificacionColegiado) from notificacioncolegiado'
        if errorcode() then
            stop(fileerror() & ' select max(IdNotificacionColegiado) from notificacioncolegiado')
        end

        next(parainsertar)

        notiCol:IdNotificacionColegiado = ParI:c1

        clear(paraconsulta2)
        paraconsulta2{prop:sql}='select planpagoscuotas.Id, planpagoscuotas.Vencimiento'|
            &', planpagoscuotas.Importe'|
            &' from planpagoscuotas'|
            &' inner join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
            &' where planpagos.IdColegiado ='& col:Id|
            &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL)'|
            &' and planpagoscuotas.IdRefinanciado is NULL'|
            &' order by planpagoscuotas.Id'

        if errorcode() then
            stop(fileerror() &' select planpagoscuotas.Id, planpagoscuotas.Vencimiento'|
            &', planpagoscuotas.Importe'|
            &' from planpagoscuotas'|
            &' inner join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
            &' where planpagos.IdColegiado ='& col:Id|
            &' and (planpagoscuotas.FechaPago = 0 or planpagoscuotas.FechaPago is NULL)'|
            &' and planpagoscuotas.IdRefinanciado is NULL'|
            &' order by planpagoscuotas.Id')
        end

        loop
            next(paraconsulta2)
            if errorcode() then break.

            notiColDeu:IdPlanPagosCuota = par2:c1
            PPCuo:Vencimiento = date(sub(par2:c2,6,2),sub(par2:c2,9,2),sub(par2:c2,1,4))
            PPCuo:Importe = par2:c3
            notiColDeu:ValorActualizado = PPCuo:Importe + CalculaRecargoPlanPago(PPCuo:Importe, PPCuo:Vencimiento)
            
            parainsertar{prop:sql}='insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdPlanPagosCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdPlanPagosCuota &', '& notiColDeu:ValorActualizado &')'
            if errorcode() then
                stop(fileerror() & ' insert into notificacioncolegiadodeuda (IdNotificacionColegiado, IdPlanPagosCuota, ValorActualizado)'|
                &' values ('& notiCol:IdNotificacionColegiado &', '& notiColDeu:IdPlanPagosCuota &', '& notiColDeu:ValorActualizado &')')
            end
        end
    end
end

clear(paraconsulta)
paraconsulta{prop:sql}='select IdNotificacionColegiado, IdColegiado from notificacioncolegiado where notificacioncolegiado.IdNotificacion='& noti:IdNotificacion
if errorcode() then
    stop(fileerror() & ' select * from notificacioncolegiado where notificacioncolegiado.IdNotificacion='& noti:IdNotificacion)
end

loop
    next(paraconsulta)
    if errorcode() then break.

    notiCol:IdNotificacionColegiado = ParC:c1
    notiCol:IdColegiado = ParC:c2
    !imprimir
    imprimirDeuda(notiCol:IdColegiado,2,notiCol:IdNotificacionColegiado,notDeu:FechaVencimiento)
    !stop()
end

 */


