<?php
function obtenerColegiadoDeudaAnualPorPeriodo($periodo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cda.Id, cda.Importe, cda.Cuotas, cda.Antiguedad
        FROM colegiadodeudaanual cda
        WHERE cda.Periodo = ? AND cda.Estado <> 'B'
        ORDER BY cda.Antiguedad, cda.Id";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodo);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $importe, $cuotas, $antiguedad);
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
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'antiguedad' => $antiguedad
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerDeudaPeriodosAnterioresPorIdColegiado($idColegiado) {

    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cda.Id, cda.Periodo, cdac.Cuota, cdac.Importe, cdac.FechaVencimiento, cdac.Recargo, cdac.SegundoVencimiento, cdac.Id
        FROM colegiadodeudaanual cda
        INNER JOIN colegiadodeudaanualcuotas cdac ON cdac.IdColegiadoDeudaAnual = cda.Id
        WHERE cda.IdColegiado = ? 
        AND cda.Periodo < ? 
        AND cda.Estado = 'A'
        AND cdac.Estado = 1
        ORDER BY cda.Periodo, cdac.Cuota";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $_SESSION['periodoActual']);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $periodo, $cuota, $importe, $fechaVencimiento, $importeSegundoVto, $fechaSegundoVencimiento, $idColegiadoDeudaAnualCuota);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                //calcula recargo a la fecha actual 
                $vencimientoActual = date('Y-m-d');
                $recargo = obtenerRecargoCuota($fechaVencimiento, $vencimientoActual, $importe);
                $row = array (
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'fechaVencimiento' => $fechaVencimiento,
                    'importeSegundoVto' => $importeSegundoVto,
                    'fechaSegundoVencimiento' => $fechaSegundoVencimiento,
                    'recargo' => $recargo,
                    'vencimientoActual' => $vencimientoActual,
                    'idDeuda' => $idColegiadoDeudaAnualCuota
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadosEmisionAnualTotal($periodoActual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    /*
    $sql = "SELECT cda.IdColegiado
        FROM colegiadodeudaanual cda
        WHERE cda.Periodo = ?
            AND cda.EmisionTotal = 'N'";
    */
    $sql = "SELECT DISTINCT cda.IdColegiado
        FROM colegiadodeudaanual cda
        INNER JOIN colegiado ON colegiado.Id = cda.IdColegiado
        LEFT JOIN cobranzadetalle cd ON(cd.IdColegiado = cda.IdColegiado AND cd.Periodo = cda.Periodo)
        LEFT JOIN cobranza c ON(c.Id = cd.IdLoteCobranza AND c.IdLugarPago IN(6, 7, 10, 16, 24, 28, 30))
        WHERE cda.Periodo = ?
        AND (SELECT COUNT(cdac.Id) FROM colegiadodeudaanualcuotas cdac WHERE cdac.IdColegiadoDeudaAnual = cda.Id AND cdac.Estado = 1) > 0
        AND c.Id IS NULL
        AND cda.EmisionTotal = 'N'
          ORDER BY colegiado.Matricula";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodoActual);
    $stmt->execute();
    $stmt->bind_result($idColegiado);
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
                    'idColegiado' => $idColegiado
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadoDeudaAnualPorIdColegiado($idColegiado, $matricula, $periodoDesde, $periodoHasta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT Id, Periodo, Importe, Cuotas, Estado
        FROM colegiadodeudaanual 
        WHERE IdColegiado = ?
        AND Periodo >= ? AND Periodo <= ?) ";
    
    if ($matricula <> 0) {
        $sql .= "UNION ALL

        (SELECT 'Id', colegiadodeudahistorico.Periodo, SUM(colegiadodeudahistorico.Importe) AS Importe, 0, 'C'
        FROM colegiadodeudahistorico
        WHERE colegiadodeudahistorico.Matricula = ? 
        AND colegiadodeudahistorico.FechaPago IS NOT NULL AND colegiadodeudahistorico.FechaPago <> '0000-00-00'
        GROUP BY colegiadodeudahistorico.Periodo) ";
    }

    $sql .= "ORDER BY Periodo DESC";
    $stmt = $conect->prepare($sql);
    if ($matricula <> 0) {
        $stmt->bind_param('iiii', $idColegiado, $periodoDesde, $periodoHasta, $matricula);
    } else {
        $stmt->bind_param('iii', $idColegiado, $periodoDesde, $periodoHasta);
    }
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $importe, $cuotas, $estado);
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
                    'id' => $id,
                    'periodo' => $periodo,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'estado' => $estado
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerColegiadoDeudaAnualPorIdColegiadoEstado($idColegiado, $estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id, Periodo, Importe, Cuotas, Estado
        FROM colegiadodeudaanual 
        WHERE IdColegiado = ?
        AND Estado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiado, $estado);
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $importe, $cuotas, $estado);
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
                    'id' => $id,
                    'periodo' => $periodo,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'estado' => $estado
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerDeudaAnualCuotas($idColegiadoDeudaAnual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdac.Id, cda.Periodo, cdac.Cuota, cdac.Importe, cdac.FechaVencimiento, cdac.Recargo, cdac.SegundoVencimiento, cdac.Estado, planpagos.Estado AS EstadPP, cdac.IdPlanPago, cdac.FechaPago, sc.FechaSolicitud
            FROM colegiadodeudaanualcuotas cdac
            INNER JOIN colegiadodeudaanual cda ON(cda.Id = cdac.IdColegiadoDeudaAnual)
            LEFT JOIN planpagos ON(planpagos.Id = cdac.IdPlanPago)
            LEFT JOIN solicitudcondonaciondetalle scd ON scd.IdColegiadoDeudaCondonada = cdac.Id
            LEFT JOIN solicitudcondonacion sc ON sc.Id = scd.IdSolicitudCondonacion
            WHERE cda.Id = ?
            AND cdac.Estado <= 4";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnualCuota, $periodo, $cuota, $importeUno, $vencimientoUno, $importeDos, $vencimientoDos, $estado, $estadoPP, $idPlanPago, $fechaPago, $fechaCondonacion);
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
                if ($vencimientoDos < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimientoDos, date('Y-m-d'), $importeDos);
                } else {
                    $importeActualizado = $importeDos;
                }
                
                $row = array (
                    'idColegiadoDeudaAnualCuota' => $idColegiadoDeudaAnualCuota,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importe' => $importeUno,
                    'importeActualizado' => $importeActualizado,
                    'vencimiento' => $vencimientoDos,
                    'estado' => $estado,
                    'estadoPP' => $estadoPP,
                    'idPlanPago' => $idPlanPago,
                    'fechaPago' => $fechaPago,
                    'fechaCondonacion' => $fechaCondonacion
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de colegiacion pendiente de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerColegiadoDeudaAnualAPagar($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT dac.Id, da.Periodo, dac.Cuota, dac.Importe, dac.FechaVencimiento, dac.Recargo, dac.SegundoVencimiento, p.Id
            FROM colegiadodeudaanualcuotas dac
            INNER JOIN colegiadodeudaanual da ON da.Id = dac.IdColegiadoDeudaAnual
            LEFT JOIN pagosnoregistrados p ON (p.Recibo = dac.Id AND p.TipoPago='C')
            WHERE da.IdColegiado = ?
            AND dac.Estado = 1
            AND p.Id IS NULL
            ORDER BY da.Periodo, dac.Cuota";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnualCuota, $periodo, $cuota, $importeUno, $vencimientoUno, $importeDos, $vencimientoDos, $idPagoNoRegistrado);
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
                if (!isset($importeDos) || $importeDos == 0) {
                    $importeDos = $importeUno;
                }
                if ($vencimientoDos < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimientoDos, date('Y-m-d'), $importeDos);
                } else {
                    $importeActualizado = $importeDos;
                }
                $row = array (
                    'idColegiadoDeudaAnualCuota' => $idColegiadoDeudaAnualCuota,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importeUno' => $importeUno,
                    'importeActualizado' => $importeActualizado,
                    'vencimiento' => $vencimientoDos,
                    'fechaVencimiento' => $vencimientoDos,
                    'idPagoNoRegistrado' => $idPagoNoRegistrado
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de colegiacion pendiente de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerCuotasPeriodoActualParaHomeBanking($idColegiado, $periodoActual, $fechaVencimiento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT dac.Id, da.Periodo, dac.Cuota, dac.Importe, dac.FechaVencimiento, dac.Recargo, dac.SegundoVencimiento, c.Codigo, c.MensajeTicket, c.MensajePantalla
            FROM colegiadodeudaanualcuotas dac
            INNER JOIN colegiadodeudaanual da ON da.Id = dac.IdColegiadoDeudaAnual
            INNER JOIN home_banking_concepto c ON c.CuotaPeriodo = dac.Cuota
            WHERE da.IdColegiado = ? AND da.Periodo = ?
            AND dac.Estado = 1 AND dac.FechaVencimiento <= ?
            ORDER BY da.Periodo, dac.Cuota";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iis', $idColegiado, $periodoActual, $fechaVencimiento);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnualCuota, $periodo, $cuota, $importeUno, $vencimientoUno, $importeDos, $vencimientoDos, $concepto, $mensajeTicket, $mensajePantalla);
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
                if (!isset($importeDos) || $importeDos == 0) {
                    $importeDos = $importeUno;
                }
                if ($vencimientoDos < date('Y-m-d')){
                    $importeActualizado = obtenerRecargoCuota($vencimientoDos, date('Y-m-d'), $importeDos);
                } else {
                    $importeActualizado = $importeDos;
                }
                $row = array (
                    'idColegiadoDeudaAnualCuota' => $idColegiadoDeudaAnualCuota,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'importeUno' => $importeUno,
                    'importeActualizado' => $importeActualizado,
                    'fechaVencimiento' => $vencimientoDos,
                    'concepto' => $concepto,
                    'mensajeTicket' => $mensajeTicket,
                    'mensajePantalla' => $mensajePantalla
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de colegiacion pendiente de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerIdColegiadoConDeuda($periodo, $fechaVencimiento, $cantidadCuotas){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiado.Id, count(colegiadodeudaanualcuotas.Id) as Cantidad, colegiadocontacto.CorreoElectronico
            FROM colegiado
            INNER JOIN colegiadodeudaanual 
                ON(colegiado.Id = colegiadodeudaanual.IdColegiado 
                AND colegiadodeudaanual.Estado='A' 
                AND colegiadodeudaanual.Periodo <= ?)
            INNER JOIN colegiadodeudaanualcuotas 
                ON(colegiadodeudaanualcuotas.IdColegiadoDeudaAnual = colegiadodeudaanual.Id 
                AND colegiadodeudaanualcuotas.SegundoVencimiento <= ? 
                AND colegiadodeudaanualcuotas.Estado = 1)
            LEFT JOIN colegiadocontacto 
                ON(colegiadocontacto.IdColegiado = colegiado.Id 
                AND colegiadocontacto.IdEstado = 1)
            LEFT JOIN agremiacionesdebito 
                ON(agremiacionesdebito.IdColegiado = colegiado.Id)
            WHERE colegiado.Estado IN(0, 1, 5, 8, 10)
            AND agremiacionesdebito.Id is null
            GROUP BY colegiado.Id, colegiado.Matricula
            HAVING Cantidad > ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isi', $periodo, $fechaVencimiento, $cantidadCuotas);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $cantidad, $mail);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) { 
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idColegiado' => $idColegiado,
                    'cantidad' => $cantidad,
                    'mail' => $mail
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de colegiacion pendiente de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiadoDeudaAnual($periodoActual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Id, colegiado.Matricula, 
        persona.Apellido, persona.Nombres
        FROM colegiadodeudaanual 
        INNER JOIN colegiado ON(colegiado.Id = colegiadodeudaanual.IdColegiado)
		  INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        WHERE colegiadodeudaanual.Periodo = ? 
        ORDER BY colegiado.Matricula";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodoActual);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $idColegiadoDeudaAnual, $matricula, $apellido, $nombre);
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
                    'idColegiado' => $idColegiado,
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiacion anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerColegiadoParaEmisionChequera($periodo, $emitirPor, $idZona, $codigoPostal, $idAgremiacion, $calleDesde, $calleHasta) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $continuar = TRUE;
    if (isset($emitirPor)) {
        if (isset($idZona)) {
            $filtroEmitirPor = "l.idZona = ".$idZona;
            if ($idZona == 4) {
                if (isset($calleDesde) && isset($calleHasta) && $calleDesde <= $calleHasta) {
                    $filtroEmitirPor .= " AND cdr.CodigoPostal = '".$codigoPostal."' AND cdr.Calle >= '".trim($calleDesde)."' AND cdr.Calle <= '".trim($calleHasta)."' AND ad.Id IS NULL AND dt.id IS NULL AND dc.id IS NULL"; 
                } else {
                    $continuar = FALSE;
                }
            } 
            $filtroEmitirPor .= " AND (cc.CorreoElectronico IS NULL OR cc.CorreoElectronico = '' OR cc.CorreoElectronico = 'NR')";
        } else {
            if (isset($idAgremiacion)) {
                $filtroEmitirPor = "ad.IdLugarPago = ".$idAgremiacion. " AND ad.Periodo = ".$periodo;
            } else {
                $continuar = FALSE;
            }
        }
    } else {
        $continuar = FALSE;
    }
    if ($continuar) {
        //se imprimen solo a los que no tienen correo electronico
        $sql = "SELECT c.Id
            FROM colegiadodeudaanual cda
            INNER JOIN colegiado c ON (c.Id = cda.IdColegiado)
            LEFT JOIN colegiadodomicilioreal cdr ON (cdr.IdColegiado = c.Id and cdr.IdEstado = 1)
            LEFT JOIN colegiadocontacto cc ON (cc.IdColegiado = c.Id and cc.IdEstado = 1)
            LEFT JOIN localidad l ON (l.Id = cdr.IdLocalidad)
            LEFT JOIN agremiacionesdebito ad ON (ad.IdColegiado = c.Id AND ad.Periodo = ?)
            LEFT JOIN debitotarjeta dt ON (dt.IdColegiado = c.Id and dt.Estado = 'A')
            LEFT JOIN debitocbu dc ON (dc.IdColegiado = c.Id and dc.Estado = 'A')
            WHERE cda.Periodo = ? AND cda.Estado = 'A' AND c.Estado IN(1, 5, 10) AND ".$filtroEmitirPor." ORDER BY cdr.CodigoPostal, l.Nombre, cdr.Calle, cdr.Numero";
        //echo $sql.' - '.$periodo;
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $periodo, $periodo);
        $stmt->execute();
        $stmt->bind_result($idColegiado);
        $stmt->store_result();

        $resultado = array();
        if(mysqli_stmt_errno($stmt)==0)
        {
            
            if (mysqli_stmt_num_rows($stmt) > 0) 
            {
                $datos = array();
                while (mysqli_stmt_fetch($stmt)) 
                {
                    /*
                    $row = array (
                        'idColegiado' => $idColegiado,
                        'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                        'matricula' => $matricula,
                        'apellido' => $apellido,
                        'nombre' => $nombre,
                        'sexo' => $sexo,
                        'calle' => $calle,
                        'lateral' => $lateral,
                        'numeroCalle' => $numeroCalle,
                        'piso' => $piso,
                        'departamento' => $departamento,
                        'localidadNombre' => $localidadNombre,
                        'codigoPostal' => $codigoPostal,
                        'fechaNacimiento' => $fechaNacimiento
                        );
                        */
                    $row = array($idColegiado);
                    array_push($datos, $row);
                }
                $resultado['estado'] = true;
                $resultado['mensaje'] = "OK";
                $resultado['datos'] = $datos;
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "No hay colegiacion anual";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error en los datos ingresados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}
//recargo cuota
function obtenerRecargoCuota($vencimiento, $vencimientoActual, $importeRecargo) {
    if ($vencimiento < $vencimientoActual) {
        if (isset($_SESSION['indiceRecargo']) && $_SESSION['indiceRecargo'] > 0) {
            $recargo = $_SESSION['indiceRecargo'];
        } else {
            $resRecargo = obtenerIndiceRecargo(date('Y-m-d'), 20);
            if ($resRecargo['estado']) {
                $recargo = $resRecargo['indiceRecargo'];
            } else {
                $recargo = 1.5;
            }
            $_SESSION['indiceRecargo'] = $recargo;
        }
    
        $meses = (substr($vencimientoActual, 0, 4) - substr($vencimiento, 0, 4)) * 12;
        $meses +=(substr($vencimientoActual, 5, 2) - substr($vencimiento, 5, 2));

        if ($meses < 0) {
            $meses = 0;
        }
        $recargo *= $meses;
        $recargoCuota = round($importeRecargo * $recargo / 100);
        $recargoCuota += $importeRecargo;
    } else {
        $recargoCuota = $importeRecargo;
    }
    return($recargoCuota);
}

function obtenerIndiceRecargo($fecha, $codigoPago){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Valor FROM tablavalores WHERE IdValor = ? AND Fecha <= ? ORDER BY Fecha DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $codigo, $fecha);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = TRUE;
            $resultado['indiceRecargo'] = $valor;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay indice de recargo";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando indice de recargo";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

//obtiene el detalle del estado en tesoreria
function estadoTesoreria($codigo){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Nombre FROM estadotesoreria WHERE Codigo = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $codigo);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estadoTesoreria'] = $nombre;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay estado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando estado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}


//obtiene si es deudor
function estadoTesoreriaPorColegiado($idColegiado, $periodoActual){
    $cantidadPeriodoActual = 0;
    $cantidadPeriodosAnteriores = 0;
    $cantidadPlanPagos = 0;
    
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT COUNT(colegiadodeudaanualcuotas.id) as cantidad
            FROM colegiadodeudaanualcuotas 
            INNER JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            LEFT JOIN agremiacionesdebito ON(agremiacionesdebito.IdColegiado = colegiadodeudaanual.IdColegiado)
            LEFT JOIN pagosnoregistrados ON(pagosnoregistrados.Recibo = colegiadodeudaanualcuotas.Id AND pagosnoregistrados.TipoPago='C')
            WHERE colegiadodeudaanual.IdColegiado = ?
            AND colegiadodeudaanual.Periodo = ?
            AND colegiadodeudaanualcuotas.Estado = 1
            AND colegiadodeudaanualcuotas.SegundoVencimiento < ADDDATE(date(now()), INTERVAL -5 DAY) 
            AND agremiacionesdebito.IdColegiado is null
            AND pagosnoregistrados.IdColegiado is null";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $periodoActual);
    $stmt->execute();
    $stmt->bind_result($cantidadPeriodoActual);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidadPeriodoActual >= 1) {
                $codigoDeudor = 1;
            }
        }
       
        //verifica la deuda de periodos anteriores
        $sql = "SELECT COUNT(colegiadodeudaanualcuotas.id) as cantidad
                FROM colegiadodeudaanualcuotas 
                INNER JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
                LEFT JOIN agremiacionesdebito ON(agremiacionesdebito.IdColegiado = colegiadodeudaanual.IdColegiado)
                LEFT JOIN pagosnoregistrados ON(pagosnoregistrados.Recibo = colegiadodeudaanualcuotas.Id AND pagosnoregistrados.TipoPago='C')
                WHERE colegiadodeudaanual.IdColegiado = ?
                AND colegiadodeudaanual.Periodo < ?
                AND colegiadodeudaanualcuotas.Estado = 1
                AND pagosnoregistrados.IdColegiado is null";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idColegiado, $periodoActual);
        $stmt->execute();
        $stmt->bind_result($cantidadPeriodosAnteriores);
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $row = mysqli_stmt_fetch($stmt);
                if ($cantidadPeriodosAnteriores >= 1) {
                    if($codigoDeudor == 1) {
                        $codigoDeudor = 2;
                    } else {
                        $codigoDeudor = 3;
                    }
                }
            }
            
            // verifico los planes de pago
            $sql = "SELECT COUNT(planpagoscuotas.id) as cantidad
                    FROM planpagoscuotas
                    INNER JOIN planpagos ON(planpagos.id = planpagoscuotas.idplanpagos)
                    LEFT JOIN pagosnoregistrados ON(pagosnoregistrados.Recibo = planpagoscuotas.Id AND pagosnoregistrados.TipoPago='P')
                    WHERE planpagos.IdColegiado = ?
                    AND planpagoscuotas.IdTipoEstadoCuota = 1
                    AND planpagoscuotas.Vencimiento <= '".date("Y-m-d")."'
                    AND pagosnoregistrados.IdColegiado is null";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $idColegiado);
            $stmt->execute();
            $stmt->bind_result($cantidadPlanPagos);
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $row = mysqli_stmt_fetch($stmt);
                    if($cantidadPlanPagos >= 1)
                    {
                        switch ($codigoDeudor){
                            case 0: $codigoDeudor = 7;
                                break;
                            case 1: $codigoDeudor = 4;
                                break;
                            case 2: $codigoDeudor = 5;
                                break;
                            case 3: $codigoDeudor = 6;
                                break;
                            default : $codigoDeudor = 8;
                                break;
                        }
                    }
                }
            }
        }
        $resultado['codigoDeudor'] = $codigoDeudor;
        $resultado['cuotasAdeudadas'] = $cantidadPeriodoActual + $cantidadPeriodosAnteriores + $cantidadPlanPagos;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando estado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiadoDeudaAnualCuotaPorId($idColegiadoDeudaAnualCuota){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdac.IdColegiadoDeudaAnual, cda.Periodo, cdac.Cuota, cdac.Importe, cdac.FechaVencimiento, cdac.Recargo, cdac.SegundoVencimiento
            FROM colegiadodeudaanualcuotas cdac
            INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
            WHERE cdac.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnualCuota);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $periodo, $cuota, $importe, $fechaVencimiento, $recargo, $segundoVencimiento);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
            $datos['periodo'] = $periodo;
            $datos['cuota'] = $cuota;
            $datos['importe'] = $importe;
            $datos['fechaVencimiento'] = $fechaVencimiento;
            $datos['recargo'] = $recargo;
            $datos['segundoVencimiento'] = $segundoVencimiento;

            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando estado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando estado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;            
}

function obtenerPagoTotalPorIdDeudaAnual($idColegiadoDeudaAnual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id, Importe, FechaVencimiento
            FROM colegiadodeudaanualtotal WHERE IdColegiadoDeudaAnual = ? AND FechaVencimiento > date(now())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->bind_result($id, $importe, $fechaVencimiento);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos['idColegiadoDeudaAnualTotal'] = 8000000 + $id;
            $datos['cuota'] = 0;
            $datos['importe'] = $importe;
            $datos['fechaVencimiento'] = $fechaVencimiento;
            $datos['codigoBarra'] = obtenerCodigoBarra44($datos['idColegiadoDeudaAnualTotal'], $importe, $importe, $fechaVencimiento, $fechaVencimiento, NULL);

            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando estado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando estado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;            
}

function obtenerPagoTotalVigentePorIdColegiado($idColegiado, $periodoActual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT dat.Id, dat.Importe, dat.FechaVencimiento
        FROM colegiadodeudaanualtotal dat
        INNER JOIN colegiadodeudaanual da ON da.Id = dat.IdColegiadoDeudaAnual
        WHERE da.IdColegiado = ?
        AND da.Periodo = ?
        AND FechaVencimiento >= date(NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiado, $periodoActual);
    $stmt->execute();
    $stmt->bind_result($id, $importe, $fechaVencimiento);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos['idColegiadoDeudaAnualTotal'] = 8000000 + $id;
            $datos['cuota'] = 0;
            $datos['importe'] = $importe;
            $datos['fechaVencimiento'] = $fechaVencimiento;
            $datos['codigoBarra'] = obtenerCodigoBarra44($datos['idColegiadoDeudaAnualTotal'], $importe, $importe, $fechaVencimiento, $fechaVencimiento, NULL);

            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando pago toal";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando pago toal";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;            
}

function obtenerPagoTotalPorIdDeudaAnual_2021($idColegiadoDeudaAnual, $importeAgregarPagoTotal){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id, Importe, FechaVencimiento
            FROM colegiadodeudaanualtotal WHERE IdColegiadoDeudaAnual = ? AND FechaVencimiento > date(now())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->bind_result($id, $importe, $fechaVencimiento);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos['idColegiadoDeudaAnualTotal'] = 8000000 + $id;
            $datos['cuota'] = 0;
            $continua = TRUE;
            if ($importeAgregarPagoTotal > 0) {                
                $importe += $importeAgregarPagoTotal;
                $resGuardaPagoTotal = actualizarMontoPagoTotal($idColegiadoDeudaAnual, $importeAgregarPagoTotal);
                if (!$resGuardaPagoTotal['estado']) {
                    $continua = FALSE;
                }
            }

            //solo para el periodo 2021 se le suma el importeAgregarPagoToal
            if ($continua) {
                $datos['importe'] = $importe;
                $datos['fechaVencimiento'] = $fechaVencimiento;
                $datos['codigoBarra'] = obtenerCodigoBarra44($datos['idColegiadoDeudaAnualTotal'], $importe, $importe, $fechaVencimiento, $fechaVencimiento, NULL);

                $resultado['datos'] = $datos;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = false;
                $resultado['mensaje'] = "Error actualizando pago total";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando pago toal";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;            
}

function actualizarMontoPagoTotal($idColegiadoDeudaAnual, $importeActualizado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadodeudaanualtotal 
        SET CuotasImpagas = ?
        WHERE IdColegiadoDeudaAnual = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $importeActualizado, $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando estado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;            
}

function obtenerColegiadoDeudaAnualCuotas($idColegiadoDeudaAnual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Importe, FechaVencimiento
            FROM colegiadodeudaanualcuotas 
            WHERE colegiadodeudaanualcuotas.IdColegiadoDeudaAnual = ?
            AND colegiadodeudaanualcuotas.Estado = 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->bind_result($importe, $vencimiento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) { 
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'importe' => $importe,
                    'vencimiento' => $vencimiento
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay cuotas de colegiacion pendiente de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cuotas de colegiacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function noTieneDeudaAnual($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id
            FROM colegiadodeudaanual WHERE IdColegiado = ? AND Periodo = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $_SESSION['periodoActual']);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            return FALSE;
        }
    }
    return TRUE;
}

function marcarEmitidoColegiadoDeudaAnual($idColegiadoDeudaAnual) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE colegiadodeudaanual 
            SET EmisionTotal = 'S' 
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
    } else {
        $resultado['estado'] = FALSE;
    }
    return $resultado;
}

function obtenerCodigoBarra44($idrecibo, $imp1, $imp2, $fecha1, $fecha2, $reciboPP) {
    /*
      2022-02-18 - se extiende el campo importe a 2 digitos mas para la parte entera
      Importe = format(cdacu:Importe*100,@n08)
      SegundoImporte = format(cdacu:Recargo*100,@n08)

      !armo el 1� vto, en juliana
      dias# = (cdacu:FechaVencimiento - Date(1,1,Year(cdacu:FechaVencimiento))) + 1
      FechaVencimiento = format(sub(Year(cdacu:FechaVencimiento),3,2),@n02) & format(dias#,@n03)

      !armo el 2� vto, en juliana
      dias# = (cdacu:SegundoVencimiento - Date(1,1,Year(cdacu:SegundoVencimiento))) + 1
      SegundoVencimiento = format(sub(Year(cdacu:SegundoVencimiento),3,2),@n02) & format(dias#,@n03)

      !Cuenta esta formado '40' + periodo(02) + numero de recibo
      Loc:LineaCodeBar = Utility & Entidad & Importe & FechaVencimiento & Cuenta |
      & SegundoImporte & SegundoVencimiento

     */
    //Armar el codigo de barras
    //tomo la fecha juliana del 1�vto.

    $dia1 = new DateTime($fecha1);

    $cvto10 = sprintf("%'03s", $dia1->format('z') + 1);
    $cvto10 = substr($fecha1, 2, 2) . $cvto10;

    //tomo la fecha juliana del 2�vto.
    $dia2 = new DateTime($fecha2);

    $cvto20 = sprintf("%'03s", $dia2->format('z') + 1);
    $cvto20 = substr($fecha2, 2, 2) . $cvto20;

    //Armo Cuota
    //$cuota = sprintf("%'02s", $ncuota) . sprintf("%'02s", $ncuota);
    //Armo Cuenta
    $Utility = "093";
    if ($idrecibo > 0) {
        $Cuenta = "40" . sprintf("%'07s", $idrecibo);
    } else {
        $Cuenta = "40" . sprintf("%'07s", $reciboPP);
    }
    $Entidad = "70108";

    //Armo LineaCodeBar
    $imp1 = number_format($imp1, 2, '', '');
    $imp2 = number_format($imp2, 2, '', '');

    $lineacodebar = $Utility . $Entidad . sprintf("%'08s", $imp1) . $cvto10 . $Cuenta . sprintf("%'08s", $imp2) . $cvto20;

    //calculo digito verificador

    $referencia = '135793579357935793579357935793579357935793579';
    $total = 0;
    for ($i = 0; $i <= 44; $i++) {
        $parcial = intval(substr($lineacodebar, $i, 1)) * intval(substr($referencia, $i, 1));
        $total = $total + $parcial;
    }

    $resultadosuma = $total / 2;
    $modulo10 = intval($resultadosuma) / 10;
    $digito = ($modulo10 - intval($modulo10)) * 10;

    $codigo = $lineacodebar . $digito;

    return($codigo);
}

function obtenerCodigoBarra($idrecibo, $imp1, $imp2, $fecha1, $fecha2, $reciboPP) {
    /*
      Importe = format(cdacu:Importe*100,@n06)
      SegundoImporte = format(cdacu:Recargo*100,@n06)

      !armo el 1� vto, en juliana
      dias# = (cdacu:FechaVencimiento - Date(1,1,Year(cdacu:FechaVencimiento))) + 1
      FechaVencimiento = format(sub(Year(cdacu:FechaVencimiento),3,2),@n02) & format(dias#,@n03)

      !armo el 2� vto, en juliana
      dias# = (cdacu:SegundoVencimiento - Date(1,1,Year(cdacu:SegundoVencimiento))) + 1
      SegundoVencimiento = format(sub(Year(cdacu:SegundoVencimiento),3,2),@n02) & format(dias#,@n03)

      !Cuenta esta formado '40' + periodo(02) + numero de recibo
      Loc:LineaCodeBar = Utility & Entidad & Importe & FechaVencimiento & Cuenta |
      & SegundoImporte & SegundoVencimiento

     */
    //Armar el codigo de barras
    //tomo la fecha juliana del 1�vto.

    $dia1 = new DateTime($fecha1);

    $cvto10 = sprintf("%'03s", $dia1->format('z') + 1);
    $cvto10 = substr($fecha1, 2, 2) . $cvto10;

    //tomo la fecha juliana del 2�vto.
    $dia2 = new DateTime($fecha2);

    $cvto20 = sprintf("%'03s", $dia2->format('z') + 1);
    $cvto20 = substr($fecha2, 2, 2) . $cvto20;

    //Armo Cuota
    //$cuota = sprintf("%'02s", $ncuota) . sprintf("%'02s", $ncuota);
    //Armo Cuenta
    $Utility = "093";
    if ($idrecibo > 0) {
        $Cuenta = "40" . sprintf("%'07s", $idrecibo);
    } else {
        $Cuenta = "40" . sprintf("%'07s", $reciboPP);
    }
    $Entidad = "70108";

    //Armo LineaCodeBar
    $imp1 = number_format($imp1, 2, '', '');
    $imp2 = number_format($imp2, 2, '', '');

    $lineacodebar = $Utility . $Entidad . sprintf("%'06s", $imp1) . $cvto10 . $Cuenta . sprintf("%'06s", $imp2) . $cvto20;

    //calculo digito verificador

    $referencia = '135793579357935793579357935793579357935793579';
    $total = 0;
    for ($i = 0; $i <= 40; $i++) {
        $parcial = substr($lineacodebar, $i, 1) * substr($referencia, $i, 1);
        $total = $total + $parcial;
    }

    $resultadosuma = $total / 2;
    $modulo10 = intval($resultadosuma) / 10;
    $digito = ($modulo10 - intval($modulo10)) * 10;

    $codigo = $lineacodebar . $digito;

    return($codigo);
}

function obtenerColegiadoEnvioChequera($periodoActual, $rango){
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT cda.Id, cda.IdColegiado, c.Matricula, p.Sexo, p.Apellido, p.Nombres, cc.CorreoElectronico, cda.Importe, cda.Cuotas, cdat.Importe, cdat.FechaVencimiento, dc.Id, dt.id
        FROM colegiadodeudaanual cda
        INNER JOIN colegiadodeudaanualtotal cdat ON cdat.IdColegiadoDeudaAnual = cda.Id
        INNER JOIN colegiado c ON(c.Id = cda.IdColegiado)
        INNER JOIN tipomovimiento tm ON(tm.Id = c.Estado AND tm.Estado = 'A')
        INNER JOIN persona p ON(p.Id = c.IdPersona)
        INNER JOIN colegiadocontacto cc ON(cc.IdColegiado = c.Id AND cc.IdEstado = 1 AND cc.CorreoElectronico is not null AND UPPER(cc.CorreoElectronico) <> 'NR' AND cc.CorreoElectronico <> '')
        LEFT JOIN enviomaildiariocolegiado emdc ON(emdc.IdColegiado = c.Id AND emdc.IdReferencia = cda.Id)
        LEFT JOIN agremiacionesdebito ad ON(ad.IdColegiado = c.Id)
        LEFT JOIN debitocbu dc ON(dc.IdColegiado = c.Id AND dc.Estado = 'A')
        LEFT JOIN debitotarjeta dt ON(dt.IdColegiado = c.Id AND dt.Estado = 'A')
        WHERE cda.Periodo = ?
            AND emdc.Id IS NULL AND ad.Id is NULL
        ORDER BY c.Matricula
        LIMIT ?";
//            AND c.Matricula = 18990
//            AND dc.Id IS NULL AND dt.id IS NULL 
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $periodoActual, $rango);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail, $importe, $cuotas, $importeTotal, $fechaVencimiento, $idDebitoCBU, $idDebitoTarjeta);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'idReferencia' => $idColegiadoDeudaAnual,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'sexo' => $sexo,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'mail' => $mail,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'importeTotal' => $importeTotal,
                    'fechaVencimiento' => $fechaVencimiento,
                    'idDebitoCBU' => $idDebitoCBU,
                    'idDebitoTarjeta' => $idDebitoTarjeta
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
            $resultado['mensaje'] = "No se encontro deuda anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando deuda anual";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadoEnvioChequera2021($periodoActual, $rango){
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT cda.Id, cda.IdColegiado, c.Matricula, p.Sexo, p.Apellido, p.Nombres, cc.CorreoElectronico, cda.Importe, cda.ImporteDescuento, dc.Id, dt.id
        FROM colegiadodeudaanual cda
        INNER JOIN colegiado c ON(c.Id = cda.IdColegiado)
        INNER JOIN tipomovimiento tm ON(tm.Id = c.Estado AND tm.Estado = 'A')
        INNER JOIN persona p ON(p.Id = c.IdPersona)
        INNER JOIN colegiadocontacto cc ON(cc.IdColegiado = c.Id AND cc.IdEstado = 1 AND cc.CorreoElectronico is not null AND UPPER(cc.CorreoElectronico) <> 'NR' AND cc.CorreoElectronico <> '')
        LEFT JOIN enviomaildiariocolegiado emdc ON(emdc.IdColegiado = c.Id AND emdc.IdReferencia = cda.Id)
        LEFT JOIN agremiacionesdebito ad ON(ad.IdColegiado = c.Id)
        LEFT JOIN debitocbu dc ON(dc.IdColegiado = c.Id AND dc.Estado = 'A')
        LEFT JOIN debitotarjeta dt ON(dt.IdColegiado = c.Id AND dt.Estado = 'A')
        WHERE cda.Periodo = ? AND cda.EmisionTotal = 'S'
            AND emdc.Id IS NULL AND ad.Id is NULL
        ORDER BY c.Matricula
        LIMIT ?";
//            AND c.Matricula = 18990
//            AND dc.Id IS NULL AND dt.id IS NULL 
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $periodoActual, $rango);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail, $importe, $importeDescuento, $idDebitoCBU, $idDebitoTarjeta);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'idReferencia' => $idColegiadoDeudaAnual,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'sexo' => $sexo,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'mail' => $mail,
                    'importe' => $importe,
                    'importeDescuento' => $importeDescuento,
                    'idDebitoCBU' => $idDebitoCBU,
                    'idDebitoTarjeta' => $idDebitoTarjeta
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
            $resultado['mensaje'] = "No se encontro deuda anual";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando deuda anual";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerValorCuotaPuraNotificacionDeuda($idNotificacionColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cda.IdColegiado, SUM(cdac.Importe)
        FROM colegiadodeudaanualcuotas cdac
        INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
        INNER JOIN notificacioncolegiadodeuda ncd ON ncd.IdColegiadoDeudaAnualCuota = cdac.id
        WHERE ncd.IdNotificacionColegiado = ?
        GROUP BY cda.IdColegiado";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idNotificacionColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $importe);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if (isset($importe) && $importe > 0) {
            $datos = array('idColegiado' => $idColegiado, 'importe' => $importe);
            $resultado['datos'] = $datos;
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontro importe de cuota pura";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando deuda anual";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerComprobantePorMatriculaCuota($matricula, $periodo, $cuota) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdac.Id
        FROM colegiadodeudaanualcuotas cdac
        INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
        INNER JOIN colegiado c ON c.Id = cda.IdColegiado
        WHERE c.Matricula = ? AND cda.Periodo = ? AND cdac.Cuota = ?
        GROUP BY cda.IdColegiado";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iii', $matricula, $periodo, $cuota);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnualCuota);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    $resultado['estado'] = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if (isset($idColegiadoDeudaAnualCuota) && $idColegiadoDeudaAnualCuota > 0) {
            $resultado = $idColegiadoDeudaAnualCuota; 
        } else {
            $resultado = NULL;
        }
    } else {
        $resultado = NULL;
    }
    return $resultado;
}

function marcarOpcionResidentePorIdColegiado($idColegiado, $periodo, $opcion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');

    $continua = TRUE;
    switch ($opcion) {
        case 'EXENCION':
            $estadoActual = 1;
            $estadoNuevo = 6;
            break;
        
        case 'PAGO_CUOTA':
            $estadoActual = 6;
            $estadoNuevo = 1;
            break;

        default:
            $continua = FALSE;
            break;
    }

    if ($continua) {
        $sql = "UPDATE colegiadodeudaanualcuotas cdac
                INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                SET cdac.Estado = ?
                WHERE cda.IdColegiado = ? AND cda.Periodo = ? AND cdac.Estado = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iiii', $estadoNuevo, $idColegiado, $periodo, $estadoActual);
        $stmt->execute();
        //$stmt->bind_result($idColegiadoDeudaAnualCuota);
        $stmt->store_result();

        $codigoDeudor = 0;
        $resultado = array();
        $resultado['estado'] = TRUE;
        if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = true;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando deuda anual";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error sin opcion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}
