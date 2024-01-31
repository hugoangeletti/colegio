<?php
function obtenerPagosNoRegistrados($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT pagosnoregistrados.Id, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota, 
            pagosnoregistrados.Recibo, pagosnoregistrados.FechaPago, pagosnoregistrados.FechaCarga, 
            pagosnoregistrados.IdUsuario, pagosnoregistrados.Detalle, lugarpago.Detalle AS LugarDePago,
            pagosnoregistrados.TipoPago, planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota
            FROM pagosnoregistrados
            INNER JOIN lugarpago ON(lugarpago.Id = pagosnoregistrados.IdLugarDePago)
            LEFT JOIN colegiadodeudaanualcuotas ON(colegiadodeudaanualcuotas.Id = pagosnoregistrados.Recibo AND 
                    pagosnoregistrados.TipoPago = 'C')
            LEFT JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            LEFT JOIN planpagoscuotas ON(planpagoscuotas.Id = pagosnoregistrados.Recibo AND 
                    pagosnoregistrados.TipoPago = 'P')
            WHERE pagosnoregistrados.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idPagoNoRegistrado, $periodo, $cuota, $recibo, $fechaPago, $fechaCarga, $idUsuario, 
            $detalle, $lugarPago, $tipoPago, $idPlanPago, $cuotaPlanPago);
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
                    'idPagoNoRegistrado' => $idPagoNoRegistrado,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'recibo' => $recibo,
                    'fechaPago' => $fechaPago,
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario,
                    'detalle' => $detalle,
                    'lugarPago' => $lugarPago,
                    'tipoPago' => $tipoPago,
                    'idPlanPago' => $idPlanPago,
                    'cuotaPlanPago' => $cuotaPlanPago
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
            $resultado['mensaje'] = "No hay Pagos No Registrados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Pagos No Registrados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function agregarPagosNoRegistrados($idColegiado, $idLugarPago, $fechaPago, $observaciones, $lasCuotas, $lasCuotasPP) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        //marcamos las cuotas que se incluyen, de cuotas de colegiacion y si tiene de plan anterior
        $hayCuotas = 0;
        $resultado['estado'] = TRUE;
        foreach ($lasCuotas as $value) {
            $sql = "INSERT INTO pagosnoregistrados 
                    (Recibo, IdUsuario, FechaCarga, FechaPago, IdLugarDePago, Detalle, Estado, TipoPago, IdColegiado)
                    VALUES (?, ?, ?, ?, ?, ?, 'A', 'C', ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iissisi', $value, $_SESSION['user_id'], $fechaCarga, $fechaPago, $idLugarPago, $observaciones, $idColegiado);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt) != 0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL GENERAR EL DETALLE";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
                break;
            }
            $hayCuotas++;
        }
        if ($resultado['estado']) { 
            foreach ($lasCuotasPP as $value) {
                $sql = "INSERT INTO pagosnoregistrados 
                        (Recibo, IdUsuario, FechaCarga, FechaPago, IdLugarDePago, Detalle, Estado, TipoPago, IdColegiado)
                        VALUES (?, ?, ?, ?, ?, ?, 'A', 'P', ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iissisi', $value, $_SESSION['user_id'], $fechaCarga, $fechaPago, $idLugarPago, $observaciones, $idColegiado);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL GENERAR EL DETALLE";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                    break;
                }
                $hayCuotas++;
            }
            if ($hayCuotas > 0) {
                $resultado['estado'] = TRUE;               
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "NO SE REGISTRARON PAGOS";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL CARGAR PAGOS NO REGISTRADOS";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = "SE REGISTRARON PAGOS CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
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
        $resultado['mensaje'] = "ERROR AL CARGAR PAGOS NO REGISTRADOS";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function obtenerPagoNoregistradoPorId($idPagoNoRegistrado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT pagosnoregistrados.Id, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota, 
            pagosnoregistrados.Recibo, pagosnoregistrados.FechaPago, pagosnoregistrados.FechaCarga, 
            pagosnoregistrados.IdUsuario, pagosnoregistrados.Detalle, lugarpago.Detalle AS LugarDePago,
            pagosnoregistrados.TipoPago, planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota
            FROM pagosnoregistrados
            INNER JOIN lugarpago ON(lugarpago.Id = pagosnoregistrados.IdLugarDePago)
            LEFT JOIN colegiadodeudaanualcuotas ON(colegiadodeudaanualcuotas.Id = pagosnoregistrados.Recibo AND 
                    pagosnoregistrados.TipoPago = 'C')
            LEFT JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            LEFT JOIN planpagoscuotas ON(planpagoscuotas.Id = pagosnoregistrados.Recibo AND 
                    pagosnoregistrados.TipoPago = 'P')
            WHERE pagosnoregistrados.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPagoNoRegistrado);
    $stmt->execute();
    $stmt->bind_result($idPagoNoRegistrado, $periodo, $cuota, $recibo, $fechaPago, $fechaCarga, $idUsuario, 
            $detalle, $lugarPago, $tipoPago, $idPlanPago, $cuotaPlanPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idPagoNoRegistrado' => $idPagoNoRegistrado,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'recibo' => $recibo,
                    'fechaPago' => $fechaPago,
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario,
                    'detalle' => $detalle,
                    'lugarPago' => $lugarPago,
                    'tipoPago' => $tipoPago,
                    'idPlanPago' => $idPlanPago,
                    'cuotaPlanPago' => $cuotaPlanPago
                 );
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay Pagos No Registrados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Pagos No Registrados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function anularPagoNoRegistrado($idPagoNoRegistrado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "DELETE FROM pagosnoregistrados WHERE pagosnoregistrados.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPagoNoRegistrado);
    $stmt->execute();
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "EL PAGO NO REGISTRADO SE ELIMINO CORRECTAMENTE";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "No se pudo eliminar el Pago No Registrado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}