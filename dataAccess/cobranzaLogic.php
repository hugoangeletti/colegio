<?php
function obtenerLotes($anio, $idLugarPago) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (isset($anio) && $anio <> "0") {
        $porAnio = " AND YEAR(c.FechaApertura) = ".$anio;
    } else {
        $porAnio = "";
    }
    if (isset($idLugarPago) && $idLugarPago <> "") {
        $porLugar = " AND c.IdLugarPago = ".$idLugarPago;
    } else {
        $porLugar = "";
    }
    $sql="SELECT c.Id, c.IdLugarPago, lp.Detalle AS NombreLugarPago, c.CantidadComprobantes, c.TotalRecaudacion, c.FechaApertura, c.TipoLote, c.NumeroLoteManual, c.DiferenciaImporte, c.DiferenciaComprobantes, c.Estado, c.Archivo, c.FechaProceso, c.IdUsuarioProceso, c.EnvioMail, c.Observacion
        FROM cobranza c
        INNER JOIN lugarpago lp ON lp.Id = c.IdLugarPago
        WHERE 1 = 1".$porAnio.$porLugar." ORDER BY c.FechaApertura";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $idLugarPago, $nombreLugarPago, $cantidadComprobantes, $totalRecaudacion, $fechaApertura, $tipoLote, $numeroLoteManual, $diferenciaImporte, $diferenciaComprobantes, $estado, $archivo, $fechaProceso, $idUsuario, $envioMail, $observaciones);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'idLugarPago' => $idLugarPago,
                'nombreLugarPago' => $nombreLugarPago,
                'cantidadComprobantes' => $cantidadComprobantes,
                'totalRecaudacion' => $totalRecaudacion,
                'fechaApertura' => $fechaApertura,
                'tipoLote' => $tipoLote,
                'numeroLoteManual' => $numeroLoteManual,
                'diferenciaImporte' => $diferenciaImporte,
                'diferenciaComprobantes' => $diferenciaComprobantes,
                'estado' => $estado,
                'archivo' => $archivo,
                'fechaProceso' => $fechaProceso,
                'idUsuario' => $idUsuario,
                'envioMail' => $envioMail,
                'observaciones' => $observaciones
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
        $resultado['mensaje'] = "Error buscando lotes";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function verificarArchivoExistente($idLugarPago, $archivoLote) {
    $anio = date('Y');
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT COUNT(Id) AS Cantidad
            FROM cobranza
            WHERE IdLugarPago = ? AND Archivo = ? AND Estado <> 'B' AND YEAR(FechaApertura) = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iss', $idLugarPago, $archivoLote, $anio);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function obtenerLote($idLugarPago, $archivoLote) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id
            FROM cobranza
            WHERE IdLugarPago = ? AND Archivo = ? AND Estado <> 'B'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idLugarPago, $archivoLote);
    $stmt->execute();
    $stmt->bind_result($idCobranza);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($idCobranza > 0) {
            $resultado['idCobranza'] = $idCobranza;
        } else {
            $resultado['idCobranza'] = NULL;
        }
    } else {
        $resultado['idCobranza'] = NULL;
    }
    return $resultado;
}

function obtenerLotePorId($idCobranza) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT c.Id, c.IdLugarPago, c.FechaApertura, c.TotalRecaudacion, c.CantidadComprobantes, c.Estado, lp.Detalle
        FROM cobranza c
        INNER JOIN lugarpago lp ON lp.Id = c.IdLugarPago
        WHERE c.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCobranza);
    $stmt->execute();
    $stmt->bind_result($idCobranza, $idLugarPago, $fechaApertura, $totalRecaudacion, $cantidadComprobantes, $estado, $lugarPago);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array (
                'idCobranza' => $idCobranza,
                'idLugarPago' => $idLugarPago,
                'lugarPago' => $lugarPago,
                'cantidadComprobantes' => $cantidadComprobantes,
                'totalRecaudacion' => $totalRecaudacion,
                'fechaApertura' => $fechaApertura,
                'estado' => $estado
            );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando lote";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    return $resultado;
}

function obtenerDetalleLote($idCobranza){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cd.Id, cd.Periodo, cd.Cuota, cd.FechaPago, cd.Importe, cd.Recibo, cd.Recargo, cd.IdColegiado, cd.IdAsistente, cd.CodigoPago, tp.Detalle, c.Matricula, p.Apellido, p.Nombres, ca.ApellidoNombre, cd.TipoPago
        FROM cobranzadetalle cd
        INNER JOIN tipopago tp ON tp.Id = cd.CodigoPago
        LEFT JOIN colegiado c ON c.Id = cd.IdColegiado
        LEFT JOIN persona p ON p.Id = c.IdPersona
        LEFT JOIN cursosasistente ca ON ca.Id = cd.IdAsistente
        WHERE cd.IdLoteCobranza = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCobranza);
    $stmt->execute();
    $stmt->bind_result($idCobranzaDetalle, $periodo, $cuota, $fechaPago, $importe, $recibo, $recargo, $idColegiado, $idAsistente, $idTipoPago, $tipoPago, $matricula, $apellido, $nombre, $asistente, $detalleTipoPago);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idCobranzaDetalle' => $idCobranzaDetalle,
                'periodo' => $periodo,
                'cuota' => $cuota,
                'fechaPago' => $fechaPago,
                'importe' => $importe,
                'recibo' => $recibo,
                'recargo' => $recargo,
                'idColegiado' => $idColegiado,
                'idAsistente' => $idAsistente,
                'idTipoPago' => $idTipoPago,
                'tipoPago' => $tipoPago,
                'matricula' => $matricula,
                'apellido' => $apellido,
                'nombre' => $nombre,
                'asistente' => $asistente,
                'detalleTipoPago' => $detalleTipoPago
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
        $resultado['mensaje'] = "Error buscando pagos del lote ".$idCobranza;
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerNovedadesLote($idCobranza){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cn.Id, cn.IdColegiado, c.Matricula, p.Apellido, p.Nombres, cn.Detalle
        FROM cobranzanovedades cn
        INNER JOIN colegiado c ON c.Id = cn.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        WHERE cn.IdCobranza = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCobranza);
    $stmt->execute();
    $stmt->bind_result($idCobranzaNovedades, $idColegiado, $matricula, $apellido, $nombre, $detalle);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idCobranzaNovedades' => $idCobranzaNovedades,
                'idColegiado' => $idColegiado,
                'matricula' => $matricula,
                'apellido' => $apellido,
                'nombre' => $nombre,
                'detalle' => $detalle
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
        $resultado['mensaje'] = "Error buscando novedades del lote ".$idCobranza;
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function agregarLoteCobranza($idLugarPago, $fechaApertura, $archivo) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO cobranza (IdLugarPago, FechaApertura, Estado, Archivo, FechaProceso, IdUsuarioProceso, TipoLote)
            VALUES (?, ?, 'A', ?, DATE(NOW()), ?, 'E')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('issi', $idLugarPago, $fechaApertura, $archivo, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['idCobranza'] = $conect->insert_id;
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error cargando PDF";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function modificarLoteCobranza($idCobranza, $comprobantesRendido, $importeRendido, $cantidadComprobantes, $totalRecaudacion, $observacion) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');

    $diferenciaComprobantes = $comprobantesRendido - $cantidadComprobantes;
    $diferenciaImporte = $importeRendido - $totalRecaudacion;
    $sql="UPDATE cobranza
            SET CantidadComprobantes = ?, 
                TotalRecaudacion = ?,
                DiferenciaImporte = ?,
                DiferenciaComprobantes = ?,
                Estado = 'C',
                Observacion = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssssi', $comprobantesRendido, $importeRendido, $diferenciaImporte, $diferenciaComprobantes, $observacion, $idCobranza);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error cargando cobranza";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function procesarPagoDebitoTarjeta($idCobranza, $fechaPago, $importeParcial, $comprobante, $tipoTarjeta, $tipoComprobante) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    switch ($tipoComprobante) {
        case '1':
            // pago de plan de pagos
            
            $sql = "SELECT Id, IdPlanPagos, Cuota 
                FROM planpagoscuotas 
                WHERE IdPlanPagos = ? AND FechaPago IS NULL 
                ORDER BY Cuota 
                LIMIT 1";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $comprobante);
            $stmt->execute();

            $stmt->bind_result($comprobante, $periodo, $cuota);
            $stmt->store_result();

            if (mysqli_stmt_errno($stmt) == 0) {
                $row = mysqli_stmt_fetch($stmt);
                $idAsistente = NULL;
                $resultado = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, $idAsistente, $cuota);    
            } else {
                $resultado['estado'] = FALSE;
                $resultado['aplicado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL BUSCAR CUOTA DEL PLAN DE PAGOS";
            }
            break;
         
        case '2':
            // pago de cuota de colegiacion
            $periodo = NULL;
            $cuota = NULL;
            $idAsistente = NULL;

            $resultado = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, $idAsistente, $cuota);
            break;

        case '8';
            $comprobante = substr($comprobante, 2, 6);
            $periodo = $_SESSION['periodoActual'];
            $cuota = 0;
            $idAsistente = NULL;

            $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, $idAsistente, $cuota);            
         
         default:
            // error en el tipo
            $tipoPago = 3; //cuota de colegiacion
            $periodo = 0;
            $cuota = 0;
            $recargo = 0;
            $codigoPago = 1; //periodo actual
            $idAsistente = NULL;
            $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
            $resultado['aplicado'] = FALSE;

            break;
    } 
    
    return $resultado;
}

function cargarNovedades($idCobranza, $idColegiado, $numeroDocumento, $observaciones) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    if (!isset($idColegiado)) {
        //no viene el idColegiado, debo buscarlo por numeroDocumento
        $sql = "SELECT c.Id 
                FROM colegiado c 
                INNER JOIN persona p ON p.Id = c.IdPersona
                where p.NumeroDocumento = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $numeroDocumento);
        $stmt->execute();

        $stmt->bind_result($idColegiado);
        $stmt->store_result();

        if (mysqli_stmt_errno($stmt) == 0) {
            $row = mysqli_stmt_fetch($stmt);
        } else {
            $idColegiado = NULL;
        }
    }
    if (isset($idColegiado)) {
        $sql = "INSERT INTO cobranzanovedades (IdCobranza, IdColegiado, Detalle)
            VALUES (?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iis', $idCobranza, $idColegiado, $observaciones);
        $stmt->execute();
        $stmt->store_result();

        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "Error al cargar observaciones";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al cargar observaciones";
    }
    return $resultado;
}

function procesarPagoHomeBanking($idCobranza, $idLinkPagos, $matricula, $fechaPago, $importeParcial, $comprobante, $concepto) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    
    //obtenemos el registro en linkpagos
    if (isset($idLinkPagos)) {
        $idLinkPagos = intval($idLinkPagos);

        //echo 'idLinkPagos->'.$idLinkPagos.' - matricula->'.$matricula.'<br>';

        $sql="SELECT lp.Concepto, lp.ImportePrimerVto, lp.ImporteSegundoVto
            FROM linkpagos lp
            WHERE lp.id = ? AND lp.Matricula = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idLinkPagos, $matricula);
        $stmt->execute();

        $stmt->bind_result($concepto, $importePrimerVto, $importeSegundoVto);
        $stmt->store_result();

    } else {
        //echo 'concepto->'.$concepto.' - matricula->'.$matricula.'<br>';

        $sql="SELECT lp.id, lp.ImportePrimerVto, lp.ImporteSegundoVto
            FROM linkpagos lp
            WHERE lp.Concepto = ? AND lp.Matricula = ?
            ORDER BY lp.id DESC
            LIMIT 1";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('si', $concepto, $matricula);
        $stmt->execute();

        $stmt->bind_result($idLinkPagos, $importePrimerVto, $importeSegundoVto);
        $stmt->store_result();
    }

    if (mysqli_stmt_errno($stmt) == 0) {
        $row = mysqli_stmt_fetch($stmt);

        //por error en linkpagos busco el concepto del maximo idlinkpagos por matricula, sino no encuentre el registro
        if (!isset($concepto) || $concepto == "") {
            //busco concepto
            //echo 'entro sin concepto->'.$concepto.'<br>';
            $sql = "SELECT  lp.Concepto, lp.Id, lp.ImportePrimerVto, lp.ImporteSegundoVto
                    FROM linkpagos lp
                    WHERE lp.Matricula = ?
                    ORDER BY lp.Id DESC
                    LIMIT 1";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $matricula);
            $stmt->execute();

            $stmt->bind_result($concepto, $idLinkPagos, $importePrimerVto, $importeSegundoVto);
            $stmt->store_result();
            $row = mysqli_stmt_fetch($stmt);
        }                            

        //echo 'idLinkPagos->'.$idLinkPagos.'<br>';

        //procesar los registros segun el concepto
        //echo 'Concepto->'.$concepto.' - importe->'.$importePrimerVto.'<br>';
        switch ($concepto) {
            case '001': 
                //echo 'Entro por deuda de colegiacion'.'<br>';
                //deuda cuotas
                $sqlDeuda = "SELECT lpd.IdDeuda, lpd.ImportePrimerVto, lpd.ImporteSegundoVto, cda.Periodo, cdac.Cuota, cdac.Importe, cdac.Recargo
                    FROM linkpagosdetalle lpd
                    LEFT JOIN colegiadodeudaanualcuotas cdac ON cdac.Id = lpd.IdDeuda
                    LEFT JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                    WHERE lpd.IdLinkPagos = ?";
                $stmtDeuda = $conect->prepare($sqlDeuda);
                $stmtDeuda->bind_param('i', $idLinkPagos);
                $stmtDeuda->execute();

                $stmtDeuda->bind_result($idColegiadoDeudaAnualCuota, $importePrimerVto, $importeSegundoVto, $periodo, $cuota, $importe, $recargo);
                $stmtDeuda->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
                    while (mysqli_stmt_fetch($stmtDeuda)) {
                        $comprobante = $idColegiadoDeudaAnualCuota;
                        $tipoComprobante = 2; //Cuota de colegiacion para cargaPago

                        $importeAplicar = 
                        $resCarga = cargaPago($importePrimerVto, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, NULL, $cuota);
                    }
                } else {
                    $idColegiado = NULL;
                    $tipoPago = 3;
                    $periodo = 0;
                    $cuota = 0;
                    $recargo = 0;
                    $codigoPago = 3; //periodos anteriores
                    $idAsistente = NULL;
                    $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                    $resultado['aplicado'] = FALSE;
                }
                break;

            case '002': //Deuda PlanPagos
            case '003': //Cuota PlanPagos
                //echo 'Entro por plan de pagos'.'<br>';
                //obtener los registros incluidos
                $sqlDeuda = "SELECT lpd.IdDeuda, lpd.ImportePrimerVto, ppc.IdPlanPagos, ppc.Cuota, ppc.Cuota, ppc.Importe
                    FROM linkpagosdetalle lpd
                    INNER JOIN planpagoscuotas ppc ON ppc.Id = lpd.IdDeuda
                    WHERE lpd.IdLinkPagos = ?";
                $stmtDeuda = $conect->prepare($sqlDeuda);
                $stmtDeuda->bind_param('i', $idLinkPagos);
                $stmtDeuda->execute();

                $stmtDeuda->bind_result($idPlanPagosCuotas, $importePrimerVto, $idPlanPago, $cuota, $importe);
                $stmtDeuda->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
                    while (mysqli_stmt_fetch($stmtDeuda)) {
                        $comprobante = $idPlanPagosCuotas;
                        $tipoComprobante = 1; //Cuota de plan de pagos para cargaPago

                        $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $idPlanPago, $fechaPago, $idCobranza, NULL, $cuota);
                    }
                } else {
                    $idColegiado = NULL;
                    $tipoPago = 2; //plan de pagos
                    $periodo = 0;
                    $cuota = 0;
                    $recargo = 0;
                    $codigoPago = 2; //plan de pagos
                    $idAsistente = NULL;
                    $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                    $resultado['aplicado'] = FALSE;
                }
                break;

            case ($concepto >= '004' && $concepto <= '013'):
                //echo 'Entro por colegiacion'.'<br>';
                //obtener los registros incluidos
                $sqlDeuda = "SELECT lpd.IdDeuda, lpd.ImportePrimerVto, cda.Periodo, cdac.Cuota, cdac.Importe
                    FROM linkpagosdetalle lpd
                    INNER JOIN colegiadodeudaanualcuotas cdac ON cdac.Id = lpd.IdDeuda
                    INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                    WHERE lpd.IdLinkPagos = ?";
                $stmtDeuda = $conect->prepare($sqlDeuda);
                $stmtDeuda->bind_param('i', $idLinkPagos);
                $stmtDeuda->execute();

                $stmtDeuda->bind_result($idColegiadoDeudaAnualCuota, $importePrimerVto, $periodo, $cuota, $importe);
                $stmtDeuda->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
                    //echo 'Encontro cuotas aplicar'.'<br>';
                    while (mysqli_stmt_fetch($stmtDeuda)) {
                        $comprobante = $idColegiadoDeudaAnualCuota;
                        $tipoComprobante = 2; //Cuota de colegiacion para cargaPago

                        $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, NULL, $cuota);
                    }
                } else {
                    //echo 'NO Encontro cuotas aplicar'.'<br>';
                    $resColegiado = obtenerIdColegiado($matricula);
                    if ($resColegiado['estado']) {
                        $idColegiado = $resColegiado['idColegiado'];
                    } else {
                        $idColegiado = NULL;
                    }
                    $tipoPago = 3; //cuota de colegiacion
                    $periodo = 0;
                    $cuota = 0;
                    $recargo = 0;
                    $codigoPago = 1; //periodo actual
                    $idAsistente = NULL;
                    $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                    $resultado['aplicado'] = FALSE;
                }
                break;

            case '014':
                //echo 'Entro por pago total'.'<br>';
                //obtener los registros incluidos
                $sqlDeuda = "SELECT lpd.IdDeuda, lpd.ImportePrimerVto, cda.Periodo
                    FROM linkpagosdetalle lpd
                    INNER JOIN colegiadodeudaanualtotal cdat ON cdat.Id = lpd.IdDeuda
                    INNER JOIN colegiadodeudaanual cda ON cda.Id = cdat.IdColegiadoDeudaAnual
                    WHERE lpd.IdLinkPagos = ?";
                $stmtDeuda = $conect->prepare($sqlDeuda);
                $stmtDeuda->bind_param('i', $idLinkPagos);
                $stmtDeuda->execute();

                $stmtDeuda->bind_result($idColegiadoDeudaAnualTotal, $importePrimerVto, $periodo);
                $stmtDeuda->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
                    //echo 'Encontro cuotas aplicar'.'<br>';
                    while (mysqli_stmt_fetch($stmtDeuda)) {
                        $comprobante = $idColegiadoDeudaAnualTotal;
                        $tipoComprobante = 8; //Pago Total para cargaPago
                        $cuota = 0; //para Pago Total va cero

                        $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, NULL, $cuota);
                    }
                } else {
                    //echo 'NO Encontro cuotas aplicar'.'<br>';
                    $resColegiado = obtenerIdColegiado($matricula);
                    if ($resColegiado['estado']) {
                        $idColegiado = $resColegiado['idColegiado'];
                    } else {
                        $idColegiado = NULL;
                    }
                    $tipoPago = 3; //cuota de colegiacion
                    $periodo = 0;
                    $cuota = 0;
                    $recargo = 0;
                    $codigoPago = 1; //periodo actual
                    $idAsistente = NULL;
                    $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                    $resultado['aplicado'] = FALSE;
                }
                break;

            case ($concepto >= '200' && $concepto <= '299'): //Cuota Curso
                //echo 'Entro por curso'.'<br>';
                //obtener los registros incluidos
                $idAsistente = $matricula;
                $sqlDeuda = "SELECT lpd.IdDeuda, lpd.ImportePrimerVto, cac.Cuota, cac.Importe
                    FROM linkpagosdetalle lpd
                    INNER JOIN cursosasistentecuotas cac ON cac.Id = lpd.IdDeuda
                    WHERE lpd.IdLinkPagos = ?";
                $stmtDeuda = $conect->prepare($sqlDeuda);
                $stmtDeuda->bind_param('i', $idLinkPagos);
                $stmtDeuda->execute();

                $stmtDeuda->bind_result($idCursosAsistenteCuota, $importePrimerVto, $cuota, $importe);
                $stmtDeuda->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
                    //echo 'Encontro cuotas aplicar'.'<br>';
                    while (mysqli_stmt_fetch($stmtDeuda)) {
                        $comprobante = $idCursosAsistenteCuota;
                        $periodo = 0;
                        $tipoComprobante = 6; //cuota de curso

                        $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, 0, $fechaPago, $idCobranza, $idAsistente, $cuota);
                    }
                } else {
                    //echo 'NO Encontro cuotas aplicar'.'<br>';
                    $idColegiado = NULL;
                    $tipoPago = 7; //cursos
                    $periodo = 0;
                    $cuota = 0;
                    $recargo = 0;
                    $codigoPago = 10; //cursos

                    $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                    $resultado['aplicado'] = FALSE;
                }
                break;

            default:
                //echo 'No entro por concepto'.'<br>';
                //el concepto es erroneo, cargo el pago solo en el lote sin datos
                $idColegiado = NULL;
                $tipoPago = 0; //error de concepto
                $periodo = 0;
                $cuota = 0;
                $recargo = 0;
                $codigoPago = 0; //error de concepto
                $idAsistente = NULL;
                $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                $resultado['aplicado'] = FALSE;
                $resultado['mensaje'] .= ' - Codigo erroneo -> '.$concepto; 

        }
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    } else {
        echo 'no encontrado'.'<br>';
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando linkpagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function procesarPagoCBU($idCobranza, $fechaPago, $importeParcial, $idEnvioDebitoDetalle) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');

    $sql="SELECT 'C' AS TipoCuota, cdac.Id, cda.Periodo, cdac.Cuota, eddc.Importe, cda.IdColegiado, cdac.Importe, cdac.Estado
        FROM enviodebitodetallecuota eddc
        INNER JOIN enviodebitodetalle edd ON edd.Id = eddc.IdEnvioDebitoDetalle
        INNER JOIN colegiadodeudaanualcuotas cdac ON cdac.Id = eddc.IdRelacion
        INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
        WHERE edd.Id = ? AND eddc.TipoCuota = 'C'
        
        UNION ALL

        SELECT 'P' AS TipoCuota, ppc.Id, ppc.IdPlanPagos, ppc.Cuota, eddc.Importe, pp.IdColegiado, ppc.Importe, ppc.IdTipoEstadoCuota
        FROM enviodebitodetallecuota eddc
        INNER JOIN enviodebitodetalle edd ON edd.Id = eddc.IdEnvioDebitoDetalle
        INNER JOIN planpagoscuotas ppc ON ppc.Id = eddc.IdRelacion
        INNER JOIN planpagos pp ON pp.Id = ppc.IdPlanPagos
        WHERE edd.Id = ? AND eddc.TipoCuota = 'P'";
    $stmtDeuda = $conect->prepare($sql);
    $stmtDeuda->bind_param('ii', $idEnvioDebitoDetalle, $idEnvioDebitoDetalle);
    $stmtDeuda->execute();

    $stmtDeuda->bind_result($tipoCuota, $idReferencia, $periodo_planpago, $cuota, $importeParcial, $idColegiado, $importeOriginal, $estado);
    $stmtDeuda->store_result();

    if (mysqli_stmt_errno($stmtDeuda) == 0) {
        //$rowDeuda = mysqli_stmt_fetch($stmtDeuda);
        $aplicado = TRUE;
        while (mysqli_stmt_fetch($stmtDeuda)) {
            if (isset($tipoCuota)) {
                if ($tipoCuota == 'C') { 
                    //es cuota de colegiacion
                    $comprobante = $idReferencia;
                    $tipoComprobante = 2; //Cuota de colegiacion para cargaPago
                    $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo_planpago, $fechaPago, $idCobranza, NULL, $cuota);
                } else {
                    if ($tipoCuota == 'P') {
                        $comprobante = $idReferencia;
                        $tipoComprobante = 1; //Cuota de plan de pagos para cargaPago
                        $resCarga = cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo_planpago, $fechaPago, $idCobranza, NULL, $cuota);
                    } else {
                        $aplicado = FALSE;
                    }
                }
            } else {
                $aplicado = FALSE;
            }
        }
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    } else {
        $idColegiado = NULL;
        $tipoPago = 3;
        $periodo = 0;
        $cuota = 0;
        $recargo = 0;
        $codigoPago = 3; //periodos anteriores
        $idAsistente = NULL;
        $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
        $resultado['aplicado'] = FALSE;
        //echo 'no encontrado'.'<br>';
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando linkpagos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function cargaPago($importeParcial, $tipoComprobante, $comprobante, $periodo, $fechaPago, $idCobranza, $idAsistente, $cuota) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');

    switch ($tipoComprobante) {
        case '1':
            //es cuota de plan de pagos
            $sql = "SELECT pp.IdColegiado, pp.Id, ppc.Cuota, ppc.Importe, ppc.IdTipoEstadoCuota
                FROM planpagoscuotas ppc
                INNER JOIN planpagos pp ON pp.Id = ppc.IdPlanPagos
                WHERE ppc.id = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $comprobante);
            $stmt->execute();

            $stmt->bind_result($idColegiado, $periodo, $cuota, $importeOriginal, $estado);
            $stmt->store_result();

            $aplicado = FALSE;
            if (mysqli_stmt_errno($stmt) == 0) {
                $row = mysqli_stmt_fetch($stmt);
                if ($estado == 1) {
                    //si el estado del comprobante esta en 1, entonces aplicamos el pago 
                    $resAplica = aplicarPagoPlanPago($comprobante, $fechaPago);
                    if ($resAplica['estado']) {
                        $aplicado = TRUE;
                    }
                } 
            }

            if (!$aplicado) {
                //echo "Hubo error al aplicar pago "; exit;
                //busco la primer cuota impaga y se la aplico, sino genero como pago doble
                $sql = "SELECT ppc.Cuota, ppc.Importe, ppc.Id
                    FROM planpagoscuotas ppc
                    INNER JOIN planpagos pp ON pp.Id = ppc.IdPlanPagos
                    WHERE pp.IdColegiado = ?
                        AND pp.Id = ?
                        AND ppc.IdTipoEstadoCuota IN(1, 5)
                    ORDER BY ppc.Estado, ppc.Id
                    LIMIT 1";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idColegiado, $periodo);
                $stmt->execute();

                $stmt->bind_result($cuota, $importeOriginal, $idPlanPagoCuota);
                $stmt->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    $row = mysqli_stmt_fetch($stmt);
                    if ($idPlanPagoCuota > 0) {
                        $resAplica = aplicarPagoPlanPago($idPlanPagoCuota, $fechaPago);
                        if ($resAplica['estado']) {
                            $aplicado = TRUE;
                        }
                    }
                } 
            }

            if (!$aplicado) {
                //si no encontro cuotas sin aplicar, entonces le sumo un 10 a la cuota para agregarlo al lote como pago doble
                if (isset($cuota) && $cuota > 0) {
                    $cuota += 20;
                } else {
                    $cuota = 20;
                }
            }

            $tipoPago = 2; //plan de pagos
            $codigoPago = 2; //plan de pagos
            $recargo = 0;
            if ($aplicado && $importeParcial > $importeOriginal) {
                $recargo = $importeParcial - $importeOriginal;
            }
            $idAsistente = NULL;
            $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
            $resultado['aplicado'] = $aplicado;
            break;

        case '2':
        case '0':
            //es cuota de colegiacion en base de datos
            //tomo los datos de la chequera para imputar el pago si esta pendiente de pago (Estado=1)
            $sql = "SELECT cda.IdColegiado, cda.Periodo, cdac.Cuota, cdac.Importe, cdac.Estado
                FROM colegiadodeudaanualcuotas cdac
                INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                WHERE cdac.id = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $comprobante);
            $stmt->execute();

            $stmt->bind_result($idColegiado, $periodo, $cuota, $importeOriginal, $estado);
            $stmt->store_result();

            $aplicado = FALSE;
            if (mysqli_stmt_errno($stmt) == 0) {
                //foreach ($variable as $key => $value) {
                    // code...
                //}
                $row = mysqli_stmt_fetch($stmt);
                if ($estado == 1) {
                    //si el estado del comprobante esta en 1, entonces aplicamos el pago 
                    $resAplica = aplicarPagoDeudaAnual($comprobante, $fechaPago);
                    if ($resAplica['estado']) {
                        $aplicado = TRUE;
                    }
                } else {
                    $cuotaOriginal = $cuota;
                    $importeOriginalOriginal = $importeOriginal;                    
                }
            }
            //echo "importeOriginalOriginal->".$importeOriginalOriginal.'<br>';

            if (!$aplicado) {
                //echo "Hubo error al aplicar pago "; exit;
                //busco la primer cuota impaga y se la aplico, sino genero como pago doble
                $cuotaOriginal = $cuota;
                $sql = "SELECT cdac.Cuota, cdac.Importe, cdac.Id
                    FROM colegiadodeudaanualcuotas cdac
                    INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                    WHERE cda.IdColegiado = ? 
                        AND cda.Periodo = ?
                        AND cdac.Estado = 1
                    ORDER BY cdac.Estado, cdac.Id
                    LIMIT 1";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idColegiado, $periodo);
                $stmt->execute();

                $stmt->bind_result($cuota, $importeOriginal, $idColegiadoDeudaAnualCuota);
                $stmt->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    $row = mysqli_stmt_fetch($stmt);
                    if ($idColegiadoDeudaAnualCuota > 0) {
                        $resAplica = aplicarPagoDeudaAnual($idColegiadoDeudaAnualCuota, $fechaPago);
                        if ($resAplica['estado']) {
                            $aplicado = TRUE;
                        }
                    }
                } 
            }

            if (!$aplicado) {
                //si no encontro cuotas sin aplicar, entonces le sumo un 10 a la cuota para agregarlo al lote como pago doble
                if (isset($cuotaOriginal) && $cuotaOriginal > 0) {
                    $cuota = $cuotaOriginal + 10;
                } else {
                    $cuota = 20;
                }
            }

            if ($importeParcial > $importeOriginal) {
                // si la cuota ya estaba aplicada debo mantener el importeOriginalOriginal para calcular el recargo
                if (isset($importeOriginal) && $importeOriginal > 0) {
                    $recargo = $importeParcial - $importeOriginal;
                } else {
                    $recargo = $importeParcial - $importeOriginalOriginal;
                }
            } else {
                $recargo = 0;
            }
            if ($periodo == $_SESSION['periodoActual']) {
                $codigoPago = 1; //periodo actual
            } else {
                $codigoPago = 3; //periodos anteriores
            }
            $tipoPago = 3;
            $idAsistente = NULL;

            //echo 'Agrega pago al lote'.'<br>';
            $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
            $resultado['aplicado'] = $aplicado;
            break;

        case 4: //pago por notificacion de deuda
        case 9: //pago por notificacion de deuda
            //se aplica el pago a todas las cuotas que estan la nota de deuda

            $resAplica = aplicarPagoNotaDeuda($comprobante, $fechaPago);
            if ($resAplica['estado']) {
                //obtengo el valor sin recargo para obtener el importe de recargo
                $recargo = 0;
                $idColegiado = NULL;
                $resCuotaPura = obtenerValorCuotaPuraNotificacionDeuda($comprobante);
                if ($resCuotaPura['estado']) {
                    $cuotaPura = $resCuotaPura['datos'];
                    $importeCuotaPura = $cuotaPura['importe'];
                    $idColegiado = $cuotaPura['idColegiado'];
                    if ($importeCuotaPura < $importeParcial) {
                        $recargo = $importeParcial - $importeCuotaPura;
                    }
                }
                $codigoPago = 3; //periodos anteriores
                $tipoPago = 4;
                $idAsistente = NULL;
                $periodo = 0;
                $cuota = 0;
                //echo 'Agrega pago al lote'.'<br>';
                $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
                $resultado['aplicado'] = TRUE;
            } else {
                $resultado['aplicado'] = FALSE;
            }

            break;
        
        case '6':
            //es cuota de curso
            $sql = "SELECT cac.Cuota, cac.Importe, cac.FechaPago
                FROM cursosasistentecuotas cac
                WHERE cac.Id = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $comprobante);
            $stmt->execute();

            $stmt->bind_result($cuota, $importeOriginal, $fechaPagoCuota);
            $stmt->store_result();

            $aplicado = FALSE;
            if (mysqli_stmt_errno($stmt) == 0) {
                $row = mysqli_stmt_fetch($stmt);
                if (!isset($fechaPagoCuota) || $fechaPagoCuota == '0000-00-00') {
                    //si la fecha de pago es null o cero, entonces aplicamos el pago 
                    $resAplica = aplicarPagoCurso($comprobante, $fechaPago);
                    if ($resAplica['estado']) {
                        $aplicado = TRUE;
                    }
                } 
            }

            if (!$aplicado) {
                //echo "Hubo error al aplicar pago "; exit;
                //busco la primer cuota impaga y se la aplico, sino genero como pago doble
                $sql = "SELECT cac.Cuota, cac.Importe, cac.Id
                    FROM cursosasistentecuotas cac
                    WHERE cac.IdCursosAsistente = ?
                        AND (cac.FechaPago IS NULL OR cac.FechaPago = '0000-00-00')
                    ORDER BY cac.Id
                    LIMIT 1";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('i', $idAsistente);
                $stmt->execute();

                $stmt->bind_result($cuota, $importeOriginal, $idCursosAsistenteCuota);
                $stmt->store_result();

                if (mysqli_stmt_errno($stmt) == 0) {
                    $row = mysqli_stmt_fetch($stmt);
                    if ($idCursosAsistenteCuota > 0) {
                        $resAplica = aplicarPagoCurso($idCursosAsistenteCuota, $fechaPago);
                        if ($resAplica['estado']) {
                            $aplicado = TRUE;
                        }
                    }
                } 
            }

            if (!$aplicado) {
                //si no encontro cuotas sin aplicar, entonces le sumo un 10 a la cuota para agregarlo al lote como pago doble
                if (isset($cuota) && $cuota > 0) {
                    $cuota += 10;
                } else {
                    $cuota = 20;
                }
            }

            $tipoPago = 7; //cursos
            $codigoPago = 10; //cursos
            $recargo = 0;
            $idColegiado = NULL;
            $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
            $resultado['aplicado'] = $aplicado;
            break;

        case 8: //recibo pago total
            $sql = "SELECT colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Periodo, colegiadodeudaanualtotal.Importe, colegiadodeudaanual.Id, colegiadodeudaanualtotal.IdEstado
                FROM colegiadodeudaanualtotal
                INNER JOIN colegiadodeudaanual ON colegiadodeudaanual.Id = colegiadodeudaanualtotal.IdColegiadoDeudaAnual
                WHERE colegiadodeudaanualtotal.Id = ?";

            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $comprobante);
            $stmt->execute();

            $stmt->bind_result($idColegiado, $periodo, $importeOriginal, $idColegiadoDeudaAnual, $estado);
            $stmt->store_result();

            $aplicado = FALSE;
            if (mysqli_stmt_errno($stmt) == 0) {
                $row = mysqli_stmt_fetch($stmt);
                if ($estado == 1) {
                    //si el estado del comprobante esta en 1, entonces aplicamos el pago 
                    $sql = "UPDATE colegiadodeudaanual cda, colegiadodeudaanualcuotas cdac, colegiadodeudaanualtotal cdat
                        SET cda.Estado = 'C', cdac.FechaPago = ?, cdac.Estado = 8, cdac.FechaActualizacion = DATE(NOW()), cdat.FechaPago = ?, cdat.IdEstado = 2, cdat.FechaActualizacion = DATE(NOW())
                        WHERE cdat.Id = ?
                        AND cda.Id = cdat.IdColegiadoDeudaAnual AND cdac.IdColegiadoDeudaAnual = cda.Id AND cdac.Estado = 1";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('ssi', $fechaPago, $fechaPago, $comprobante);
                    $stmt->execute();
                    $stmt->store_result();
                    $aplicado = TRUE;
                } 
            }
            $tipoPago = 8;
            $idAsistente = NULL;
            $recargo = 0;
            $codigoPago = 1; //periodo actual

            /*
            echo 'Agrega pagoTotal al lote->'.$idCobranza.'<br>';
            var_dump($resultado);
            echo '<br>';
            */
            $resultado = agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importeParcial, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente);
            $resultado['aplicado'] = $aplicado;
            break;

        default:
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "El tipo de comprobante es invalido";
            break;
    }

    return $resultado;
}

function aplicarPagoDeudaAnual($comprobante, $fechaPago) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
   //imputa el pago en la cuota
    $sql = "UPDATE colegiadodeudaanualcuotas
            SET FechaPago = ?, 
            Estado = 2, 
            FechaActualizacion = DATE(NOW())
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $fechaPago, $comprobante);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error actualizando cuotas";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function aplicarPagoPlanPago($comprobante, $fechaPago) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
   //imputa el pago en la cuota
    $sql = "UPDATE planpagoscuotas
            SET FechaPago = ?, 
                Estado = '3', 
                IdTipoEstadoCuota = 2, 
                FechaActualizacion = DATE(NOW())
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $fechaPago, $comprobante);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error actualizando cuotas";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function aplicarPagoCurso($comprobante, $fechaPago) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
   //imputa el pago en la cuota
    $sql = "UPDATE cursosasistentecuotas
        SET FechaPago = ?, 
            Recibo = ?, 
            FechaActualizacion = DATE(NOW())
        WHERE id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sii', $fechaPago, $comprobante, $comprobante);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error actualizando cuotas";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function aplicarPagoNotaDeuda($comprobante, $fechaPago){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
   //imputa el pago en la cuota
    $sql = "UPDATE colegiadodeudaanualcuotas cdac
        INNER JOIN notificacioncolegiadodeuda ncd ON ncd.IdColegiadoDeudaAnualCuota = cdac.id
        SET cdac.FechaPago = ?, 
        cdac.FechaActualizacion = DATE(NOW()), 
        cdac.Estado = 2
        WHERE ncd.IdNotificacionColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $fechaPago, $comprobante);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error actualizando cuotas";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;

}

//function agregaPagoCC($idCobranza, $periodo, $cuota, $fechaPago, $importe, $recibo, $tipoPago, $recargo, $idColegiado, $fechaCarga) {
function agregarPagoLote($idCobranza, $idColegiado, $periodo, $cuota, $fechaPago, $importe, $comprobante, $tipoPago, $recargo, $codigoPago, $idAsistente) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    //imputa el pago en la cuota
    $sql = "INSERT INTO cobranzadetalle (IdLoteCobranza, Periodo, Cuota, FechaPago, Importe, Recibo, TipoPago, Recargo, IdColegiado, IdAsistente, FechaCarga, CodigoPago)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, DATE(NOW()), ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiissiisiii', $idCobranza, $periodo, $cuota, $fechaPago, $importe, $comprobante, $tipoPago, $recargo, $idColegiado, $idAsistente, $codigoPago);
    $stmt->execute();
    $stmt->store_result();

    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al cargar comprobante";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

    /*
        case pTipoComprobante
        of 4    !pago de nota de deudores
            !obtener los registros incluidos
            Relate:ParaConsulta.open
            clear(paraconsulta)
            IdNotificacionColegiado# = sub(pComprobante,2,6)

            paraconsulta{prop:sql}='select colegiadodeudaanualcuotas.id, colegiadodeudaanual.periodo'|
                &', colegiadodeudaanualcuotas.cuota, notificacioncolegiadodeuda.ValorActualizado, colegiadodeudaanual.IdColegiado'|
                &', colegiadodeudaanualcuotas.Importe'|
                &' from colegiadodeudaanualcuotas'|
                &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                &' inner join notificacioncolegiadodeuda on(notificacioncolegiadodeuda.IdColegiadoDeudaAnualCuota = colegiadodeudaanualcuotas.id)'|
                &' inner join notificacioncolegiado on(notificacioncolegiado.IdNotificacionColegiado = notificacioncolegiadodeuda.IdNotificacionColegiado)'|
                &' where notificacioncolegiado.IdNotificacionColegiado = '& IdNotificacionColegiado#|
                &' order by colegiadodeudaanual.periodo, colegiadodeudaanualcuotas.cuota'

            if errorcode() then
                stop(fileerror() &' select colegiadodeudaanualcuotas.id, colegiadodeudaanual.periodo'|
                &', colegiadodeudaanualcuotas.cuota, notificacioncolegiadodeuda.ValorActualizado, colegiadodeudaanual.IdColegiado'|
                &', colegiadodeudaanualcuotas.Importe'|
                &' from colegiadodeudaanualcuotas'|
                &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                &' inner join notificacioncolegiadodeuda on(notificacioncolegiadodeuda.IdColegiadoDeudaAnualCuota = colegiadodeudaanualcuotas.id)'|
                &' inner join notificacioncolegiado on(notificacioncolegiado.IdNotificacionColegiado = notificacioncolegiadodeuda.IdNotificacionColegiado)'|
                &' where notificacioncolegiado.IdNotificacionColegiado = '& IdNotificacionColegiado#|
                &' order by colegiadodeudaanual.periodo, colegiadodeudaanualcuotas.cuota')
            end
            loop
                next(paraconsulta)
                if errorcode() then break.
                clear(parainsertar)
                parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
                    &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                    &' where id='& ParC:c1
                if errorcode() then
                    stop(fileerror() &' update colegiadodeudaanualcuotas'|
                    &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                    &' where id='& ParC:c1)
                end

                CDet:IdLoteCobranza = pIdLote
                CDet:IdColegiado = ParC:c5
                CDet:Periodo = ParC:c2
                CDet:Cuota = ParC:c3
                CDet:FechaPago = pFechaPago
                CDet:Importe = ParC:c4
                CDet:Recibo = pComprobante
                CDet:TipoPago = 4
                CDet:Recargo = CDet:Importe - ParC:c6

                Do AgregaPagoCC
                Loc:TipoPago = 'C'
                Do AplicaEnPNR
            end
            Relate:ParaConsulta.close

        of 5    !recibo de caja, debo aplicar segun el detalle del recibo
                !Tomo el detalle y aplico los pagos segun el tipo
            relate:cajadiariamovimientodetalle.open
            clear(cajadiariamovimientodetalle)

            cajadiariamovimientodetalle{prop:sql}='select * from cajadiariamovimientodetalle'|
                &' where IdCajaDiariaMovimiento='& pComprobante

            if errorcode() then
                stop(fileerror() &' select * from cajadiariamovimientodetalle where IdCajaDiariaMovimiento='& pComprobante)
            end
            loop
                next(cajadiariamovimientodetalle)
                if errorcode() then break.

                case CDMDe:CodigoPago
                    of 1    !periodo actual
                    orof 3    !periodos anteriores
                        Do AplicoCtaCte
                    of 2    !plan de pagos
                        Do AplicoCtaCtePP
                    of 10   !cursos
                        Do AplicoCtaCteCurso
                end
            end
            relate:cajadiariamovimientodetalle.close

            Do AgregaPagoCC

        of 6
            !es cuota de curso
            CDet:Recibo = pComprobante
            clear(parainsertar)
            parainsertar{prop:sql}='update cursosasistentecuotas'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", Recibo='& CDet:Recibo &', FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where id='& CDet:Recibo
            if errorcode() then
                stop(fileerror() &' update cursosasistentecuotas'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", Recibo='& CDet:Recibo &', FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where id='& CDet:Recibo)
            end

            !tomo los datos de la chequera para imputar el pago
            clear(parainsertar)
            parainsertar{prop:sql}='select IdCursosAsistente, Cuota from cursosasistentecuotas'|
                &' where id='& CDet:Recibo
            if errorcode() then
                stop(fileerror() &' select IdCursosAsistente, Cuota from cursosasistentecuotas'|
                &' where id='& CDet:Recibo)
            end

            next(parainsertar)
            !agrego el pago al lote
            CDet:IdLoteCobranza = pIdLote
            CDet:IdAsistente = ParI:c1
            CDet:Periodo = 0
            CDet:Cuota = ParI:c2
            CDet:FechaPago = pFechaPago
            CDet:Importe = pImporteParcial
            CDet:Recibo = pComprobante
            CDet:TipoPago = 7
            CDet:Recargo=0

            Do AgregaPagoCurso

        of 8    !recibo pago total
            relate:parainsertar.open
            clear(parainsertar)

            parainsertar{prop:sql}='select colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Periodo'|
                &', colegiadodeudaanualtotal.Importe, colegiadodeudaanual.Id'|
                &' from colegiadodeudaanualtotal'|
                &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualtotal.IdColegiadoDeudaAnual)'|
                &' where colegiadodeudaanualtotal.Id='& pComprobante

            if errorcode() then
                stop(fileerror() &' select colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Periodo'|
                &', colegiadodeudaanualtotal.Importe, colegiadodeudaanual.Id'|
                &' from colegiadodeudaanualtotal'|
                &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualtotal.IdColegiadoDeudaAnual)'|
                &' where colegiadodeudaanualtotal.Id='& pComprobante)
            end
            next(parainsertar)
            CDet:IdLoteCobranza = pIdLote
            CDet:IdColegiado = ParI:c1
            CDet:Periodo = ParI:c2
            CDet:FechaPago = pFechaPago
            CDet:Importe = pImporteParcial
            CDet:Recibo = pComprobante
            CDet:TipoPago = 8
            CDet:Cuota = 0
            CDet:Recargo = CDet:Importe - ParI:c3
            Loc:IdColegiadoDeudaAnual = ParI:c4

            clear(parainsertar)
            !marco pagado el total
            parainsertar{prop:sql}='update colegiadodeudaanualtotal'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", IdEstado = 2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where Id='& pComprobante
            if errorcode() then
                stop(fileerror() &' update colegiadodeudaanualtotal'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", IdEstado = 2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where Id='& pComprobante)
            end

            !marco las cuotas como abonadas con pago total
            parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", TipoCaja="T", Estado=8, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where IdColegiadoDeudaAnual='& Loc:IdColegiadoDeudaAnual
            if errorcode() then
                stop(fileerror() &' update colegiadodeudaanualcuotas'|
                &' set FechaPago="'& format(pFechaPago,@d10-) &'", TipoCaja="T", Estado=8, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                &' where IdColegiadoDeudaAnual='& Loc:IdColegiadoDeudaAnual)
            end

            !marco las deudaanual como cerrado
            parainsertar{prop:sql}='update colegiadodeudaanual'|
                &' set Estado="C"'|
                &' where Id = '& Loc:IdColegiadoDeudaAnual
            if errorcode() then
                stop(fileerror() &' update colegiadodeudaanual'|
                &' set Estado="C"'|
                &' where Id = '& Loc:IdColegiadoDeudaAnual)
            end

            relate:parainsertar.close

            Do AgregaPagoCC

        of 9    !debito por cbu
            !obtener los registros incluidos
            Relate:ParaConsulta.open
            clear(paraconsulta)
            IdEnvioDebitoDetalle# = pComprobante

            paraconsulta{prop:sql}='select colegiadodeudaanualcuotas.Id, colegiadodeudaanual.Periodo'|
                &' , colegiadodeudaanualcuotas.Cuota, enviodebitodetallecuota.Importe, colegiadodeudaanual.IdColegiado,'|
                &' colegiadodeudaanualcuotas.Importe, planpagoscuotas.Id, planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota,'|
                &' planpagos.IdColegiado, planpagoscuotas.Importe, colegiadodeudaanualcuotas.Estado'|
                &' from enviodebitodetallecuota'|
                &' inner join enviodebitodetalle on (enviodebitodetalle.Id = enviodebitodetallecuota.IdEnvioDebitoDetalle)'|
                &' left join colegiadodeudaanualcuotas on(colegiadodeudaanualcuotas.Id = enviodebitodetallecuota.IdRelacion)'|
                &' left join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                &' left join planpagoscuotas on(planpagoscuotas.Id = enviodebitodetallecuota.IdRelacion)'|
                &' left join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
                &' where enviodebitodetalle.Id = '& IdEnvioDebitoDetalle#

            if errorcode() then
                stop(fileerror() &' select colegiadodeudaanualcuotas.Id, colegiadodeudaanual.Periodo'|
                &' , colegiadodeudaanualcuotas.Cuota, enviodebitodetallecuota.Importe, colegiadodeudaanual.IdColegiado,'|
                &' colegiadodeudaanualcuotas.Importe, planpagoscuotas.Id, planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota,'|
                &' planpagos.IdColegiado, planpagoscuotas.Importe'|
                &' from enviodebitodetallecuota'|
                &' inner join enviodebitodetalle on (enviodebitodetalle.Id = enviodebitodetallecuota.IdEnvioDebitoDetalle)'|
                &' left join colegiadodeudaanualcuotas on(colegiadodeudaanualcuotas.Id = enviodebitodetallecuota.IdRelacion)'|
                &' left join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                &' left join planpagoscuotas on(planpagoscuotas.Id = enviodebitodetallecuota.IdRelacion)'|
                &' left join planpagos on(planpagos.Id = planpagoscuotas.IdPlanPagos)'|
                &' where enviodebitodetalle.Id = '& IdEnvioDebitoDetalle#)
            end
            loop
                next(paraconsulta)
                if errorcode() then break.
                if ParC:c1 > 0 then
                    clear(parainsertar)
                    parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                        &' where id='& ParC:c1
                    if errorcode() then
                        stop(fileerror() &' update colegiadodeudaanualcuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                        &' where id='& ParC:c1)
                    end

                    CDet:IdLoteCobranza = pIdLote
                    CDet:IdColegiado = ParC:c5
                    CDet:Periodo = ParC:c2
                    CDet:Cuota = ParC:c3
                    CDet:FechaPago = pFechaPago
                    CDet:Importe = ParC:c4
                    CDet:Recibo = pComprobante
                    CDet:TipoPago = 3
                    CDet:Recargo = CDet:Importe - ParC:c6

!            CDet:IdLoteCobranza = pIdLote
!            CDet:IdColegiado = ParI:c1
!            CDet:Periodo = ParI:c2
!            CDet:FechaPago = pFechaPago
!            CDet:Importe = pImporteParcial
!            CDet:Recibo = pComprobante
!            CDet:TipoPago = 3
!            CDet:Cuota = ParI:c3
!            CDet:Recargo = CDet:Importe - ParI:c4

            if ParC:c12='1' then !estado deudor
                !imputa el pago en la cuota
                parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
                    &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                    &' where id='& pComprobante
                if errorcode() then
                    stop(fileerror() &' update colegiadodeudaanualcuotas'|
                    &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                    &' where id='& pComprobante)
                end
            else
                !si ya esta paga, busco la primera impaga del matriculado
                clear(parainsertar)
                parainsertar{prop:sql}='select colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota'|
                    &', colegiadodeudaanualcuotas.Importe, colegiadodeudaanualcuotas.Id'|
                    &' from colegiadodeudaanualcuotas'|
                    &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                    &' where colegiadodeudaanual.IdColegiado='& CDet:IdColegiado &' and colegiadodeudaanual.Periodo='& CDet:Periodo|
                    &' and colegiadodeudaanualcuotas.Estado in(1, 5)'|
                    &' order by colegiadodeudaanualcuotas.Id'
                if errorcode() then
                    stop(fileerror() &' select colegiadodeudaanual.IdColegiado, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota'|
                    &', colegiadodeudaanualcuotas.Importe, colegiadodeudaanualcuotas.Id'|
                    &' from colegiadodeudaanualcuotas'|
                    &' inner join colegiadodeudaanual on(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)'|
                    &' where colegiadodeudaanual.IdColegiado='& CDet:IdColegiado &' and colegiadodeudaanual.Periodo='& CDet:Periodo|
                    &' and colegiadodeudaanualcuotas.Estado in(1, 5)'|
                    &' order by colegiadodeudaanualcuotas.Id')
                end

                next(parainsertar)
                if ParI:c1=CDet:IdColegiado then
                    CDet:Cuota = ParI:c3
                    CDet:Recargo = CDet:Importe - ParI:c4

                    !imputa el pago en la cuota
                    parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                        &' where id='& ParI:c5
                    if errorcode() then
                        stop(fileerror() &' update colegiadodeudaanualcuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
                        &' where id='& ParI:c5)
                    end
                else
                    CDet:Cuota += 10
                end
            end
                    Do AgregaPagoCC
                    Loc:TipoPago = 'C'
                    Do AplicaEnPNR
                end

                if ParC:c7 > 0 then
                    clear(parainsertar)
                    parainsertar{prop:sql}='update planpagoscuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'", '|
                        &' IdTipoEstadoCuota = 2'|
                        &' where Id='& ParC:c7
                    if errorcode() then
                        stop(fileerror() &' update planpagoscuotas'|
                        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'", '|
                        &' IdTipoEstadoCuota = 2'|
                        &' where Id='& ParC:c7)
                    end

                    CDet:IdLoteCobranza = pIdLote
                    CDet:IdColegiado = ParC:c10
                    CDet:Periodo = ParC:c8
                    CDet:Cuota = ParC:c9
                    CDet:FechaPago = pFechaPago
                    CDet:Importe = ParC:c4
                    CDet:Recibo = pComprobante
                    CDet:TipoPago = 2
                    CDet:Recargo = CDet:Importe - ParC:c11

                    Do AgregaPagoCC
                    Loc:TipoPago = 'P'
                    Do AplicaEnPNR
                end

            end
            Relate:ParaConsulta.close

    end

AgregaPagoCurso Routine
    parainsertar{prop:sql}='insert into cobranzadetalle'|
        &' (IdLoteCobranza, Periodo, Cuota, FechaPago, Importe, Recibo, TipoPago, Recargo, IdAsistente, FechaCarga)'|
        &' values('& CDet:IdLoteCobranza &', '& CDet:Periodo &', '& CDet:Cuota|
        &', "'& format(CDet:FechaPago,@d10-) &'", '& CDet:Importe &', '& CDet:Recibo &', "'& CDet:TipoPago|
        &'", '& CDet:Recargo &', '& CDet:IdAsistente &', "'& format(Glo:Hoy,@d10-) &'")'
    if errorcode() then
        stop(fileerror() &' insert into cobranzadetalle'|
        &' (IdLoteCobranza, Periodo, Cuota, FechaPago, Importe, Recibo, TipoPago, Recargo, IdAsistente, FechaCarga)'|
        &' values('& CDet:IdLoteCobranza &', '& CDet:Periodo &', '& CDet:Cuota|
        &', "'& format(CDet:FechaPago,@d10-) &'", '& CDet:Importe &', '& CDet:Recibo &', "'& CDet:TipoPago|
        &'", '& CDet:Recargo &', '& CDet:IdAsistente &', "'& format(Glo:Hoy,@d10-) &'")')
    end

AplicoCtaCte    routine
    relate:parainsertar.open
    clear(parainsertar)
    parainsertar{prop:sql}='update colegiadodeudaanualcuotas'|
        &' set fechapago="'& format(pFechaPago,@d10-) &'", TipoCaja="L", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where id='& CDMDe:Indice
    if errorcode() then
        stop(fileerror() &' update colegiadodeudaanualcuotas'|
        &' set fechapago="'& format(pFechaPago,@d10-) &'", TipoCaja="L", Estado=2, FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where id='& CDMDe:Indice)
    end
    relate:parainsertar.close

AplicoCtaCtePP    routine
    relate:parainsertar.open
    clear(parainsertar)
    parainsertar{prop:sql}='update planpagoscuotas'|
        &' set fechapago="'& format(pFechaPago,@d10-) &'", FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where idplanpagos='& CDMDe:Indice &' and cuota='& CDMDe:Cuota
    if errorcode() then
        stop(fileerror() &' update planpagoscuotas set fechapago="'& format(pFechaPago,@d10-) &'", FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where idplanpagos='& CDMDe:Indice &' and cuota='& CDMDe:Cuota)
    end
    relate:parainsertar.close

AplicoCtaCteCurso    routine
    relate:parainsertar.open
    clear(parainsertar)
    parainsertar{prop:sql}='update cursosasistentecuotas'|
        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Recibo='& CDmov:Id &', FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where Id='& CDMDe:Indice
    if errorcode() then
        stop(fileerror() &' update cursosasistentecuotas'|
        &' set FechaPago="'& format(pFechaPago,@d10-) &'", Recibo='& CDmov:Id &', FechaActualizacion = "'& format(Glo:Hoy,@d10-) &'"'|
        &' where Id='& CDMDe:Indice)
    end
    relate:parainsertar.close

AplicaEnPNR routine     !aplica el pago si existe en PagosNoRegistrados
    relate:parainsertar.open
    clear(parainsertar)
    parainsertar{prop:sql}='update pagosnoregistrados set Estado="A"'|
        &' where Recibo='& pComprobante &' and TipoPago="'& Loc:TipoPago &'"'
    if errorcode() then
        stop(fileerror() &' update pagosnoregistrados set Estado="A"'|
        &' where Recibo='& pComprobante &' and TipoPago="'& Loc:TipoPago &'"')
    end
    relate:parainsertar.close
    */
