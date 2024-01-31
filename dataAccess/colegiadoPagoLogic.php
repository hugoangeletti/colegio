<?php
function obtenerPagosColegiacionPorIdColegiado($idColegiado, $fechaDesde, $fechaHasta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT cobranzadetalle.Periodo, cobranzadetalle.Cuota, cobranzadetalle.FechaPago AS FechaPago, 
            cobranzadetalle.Importe, cobranzadetalle.Recibo, lugarpago.Detalle AS DetalleLugarPago, 
            tipopago.Detalle AS TipoPago, cobranzadetalle.TipoPago AS IdTipoPago
            FROM cobranzadetalle
            INNER JOIN cobranza on(cobranza.Id = cobranzadetalle.IdLoteCobranza)
            INNER JOIN lugarpago on(lugarpago.Id = cobranza.IdLugarPago)
            LEFT JOIN tipopago ON(tipopago.Id = cobranzadetalle.TipoPago)
            WHERE cobranzadetalle.IdColegiado = ?
            AND cobranzadetalle.FechaPago BETWEEN ? AND ?
            AND cobranzadetalle.TipoPago IN(1, 2, 3, 4, 5, 8))
            UNION
            (SELECT cajadiariamovimientodetalle.Periodo, cajadiariamovimientodetalle.Cuota, cajadiariamovimiento.Fecha AS FechaPago, 
            cajadiariamovimientodetalle.Monto, cajadiariamovimiento.Numero, 'Caja Diaria', 
            tipopago.Detalle AS TipoPago, cajadiariamovimientodetalle.CodigoPago AS IdTipoPago
            FROM cajadiariamovimientodetalle
            INNER JOIN cajadiariamovimiento on(cajadiariamovimiento.Id = cajadiariamovimientodetalle.IdCajaDiariaMovimiento)
            LEFT JOIN tipopago ON(tipopago.Id = cajadiariamovimientodetalle.CodigoPago)
            WHERE cajadiariamovimiento.IdColegiado = ?
            AND cajadiariamovimiento.Estado <> 'A'
            AND cajadiariamovimiento.Fecha BETWEEN ? AND ?
            AND cajadiariamovimientodetalle.CodigoPago IN(1, 2, 3, 5, 8))
            ORDER BY FechaPago DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ississ', $idColegiado, $fechaDesde, $fechaHasta, $idColegiado, $fechaDesde, $fechaHasta);
    $stmt->execute();
    $stmt->bind_result($periodo, $cuota, $fechaPago, $importe, $recibo, $lugarPago, $tipoPago, $idTipoPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'fechaPago' => $fechaPago,
                    'recibo' => $recibo,
                    'lugarPago' => $lugarPago,
                    'tipoPago' => $tipoPago,
                    'idTipoPago' => $idTipoPago
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
            $resultado['mensaje'] = "No hay pagos registrados";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerPagosPorIdColegiado($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT cobranzadetalle.Periodo, cobranzadetalle.Cuota, cobranzadetalle.FechaPago AS FechaPago, 
            cobranzadetalle.Importe, cobranzadetalle.Recibo, lugarpago.Detalle AS DetalleLugarPago, 
            tipopago.Detalle AS TipoPago, cobranzadetalle.TipoPago AS IdTipoPago
            FROM cobranzadetalle
            INNER JOIN cobranza on(cobranza.Id = cobranzadetalle.IdLoteCobranza)
            INNER JOIN lugarpago on(lugarpago.Id = cobranza.IdLugarPago)
            INNER JOIN tipopago ON(tipopago.Id = cobranzadetalle.TipoPago)
            WHERE cobranzadetalle.IdColegiado = ?
            AND (cobranzadetalle.FechaPago >= ADDDATE(date(now()), INTERVAL -3 YEAR) AND cobranzadetalle.FechaPago <= date(now())))
            UNION
            (SELECT cajadiariamovimientodetalle.Periodo, cajadiariamovimientodetalle.Cuota, cajadiariamovimiento.Fecha AS FechaPago, 
            cajadiariamovimientodetalle.Monto, cajadiariamovimiento.Numero, 'Caja Diaria', 
            tipopago.Detalle AS TipoPago, cajadiariamovimientodetalle.CodigoPago AS IdTipoPago
            FROM cajadiariamovimientodetalle
            INNER JOIN cajadiariamovimiento on(cajadiariamovimiento.Id = cajadiariamovimientodetalle.IdCajaDiariaMovimiento)
            INNER JOIN tipopago ON(tipopago.Id = cajadiariamovimientodetalle.CodigoPago)
            WHERE cajadiariamovimiento.IdColegiado = ?
            AND (cajadiariamovimiento.Fecha >= ADDDATE(date(now()), INTERVAL -3 YEAR) AND cajadiariamovimiento.Fecha <= date(now())))
            ORDER BY FechaPago DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idColegiado);
    $stmt->execute();
    $stmt->bind_result($periodo, $cuota, $fechaPago, $importe, $recibo, $lugarPago, $tipoPago, $idTipoPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'fechaPago' => $fechaPago,
                    'recibo' => $recibo,
                    'lugarPago' => $lugarPago,
                    'tipoPago' => $tipoPago,
                    'idTipoPago' => $idTipoPago
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
            $resultado['mensaje'] = "No hay pagos registrados";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerPagoNoRegistrado($idColegiadoDeudaAnualCuota, $tipoPago){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "select Estado from pagosnoregistrados where Recibo = ? and TipoPago = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiadoDeudaAnualCuota, $tipoPago);
    $stmt->execute();
    $stmt->bind_result($estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $pagoNoRegistrado = '';
            if ($estado == 'C'){
                $pagoNoRegistrado = 'Pago No Registrado';
            } 
            $datos = array(
                    'pagoNoRegistrado' => $pagoNoRegistrado
                    );
            
            
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay pagos no registrados";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando pagos no registrados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerPagosPorOtrosConceptos($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT cobranzadetalle.FechaPago AS FechaPago, cobranzadetalle.Importe, cobranzadetalle.Recibo, 
            tipopago.Detalle, lugarpago.Detalle
            FROM cobranzadetalle
            INNER JOIN cobranza on(cobranza.Id = cobranzadetalle.IdLoteCobranza)
            INNER JOIN lugarpago on(lugarpago.Id = cobranza.IdLugarPago)
            LEFT JOIN tipopago ON(tipopago.Id = cobranzadetalle.TipoPago)
            WHERE cobranzadetalle.IdColegiado = ?
            AND cobranzadetalle.TipoPago NOT IN(1, 2, 3, 5, 8))

            UNION ALL

            (SELECT cajadiariamovimiento.Fecha AS FechaPago, cajadiariamovimientodetalle.Monto, cajadiariamovimiento.Numero, 
            tipopago.Detalle, 'Caja Diaria'
            FROM cajadiariamovimientodetalle
            INNER JOIN cajadiariamovimiento on(cajadiariamovimiento.Id = cajadiariamovimientodetalle.IdCajaDiariaMovimiento)
            LEFT JOIN tipopago ON(tipopago.Id = cajadiariamovimientodetalle.CodigoPago)
            WHERE cajadiariamovimiento.IdColegiado = ?
            AND cajadiariamovimientodetalle.CodigoPago NOT IN(1, 2, 3, 5, 8))
            ORDER BY FechaPago DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idColegiado);
    $stmt->execute();
    $stmt->bind_result($fechaPago, $importe, $recibo, $tipoPago, $lugarPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'importe' => $importe,
                    'fechaPago' => $fechaPago,
                    'numeroRecibo' => $recibo,
                    'tipoPago' => $tipoPago,
                    'lugarPago' => $lugarPago
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
            $resultado['mensaje'] = "No hay pagos registrados";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando pagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

