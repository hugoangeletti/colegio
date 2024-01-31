<?php
function obtenerColegiacionAnual() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiacion_anual WHERE Borrado = 0 ORDER BY Periodo DESC, Antiguedad";
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $antiguedad, $importe, $primerVencimiento, $cuotas, $pagoTotal, $vencimientoPagoTotal, $idUsuario, $fechaCarga, $borrado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiacionAnual' => $id,
                    'periodo' => $periodo,
                    'antiguedad' => $antiguedad,
                    'importe' => $importe,
                    'vencimientoCuotaUno' => $primerVencimiento,
                    'cuotas' => $cuotas,
                    'pagoTotal' => $pagoTotal, 
                    'vencimientoPagoTotal' => $vencimientoPagoTotal,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga,
                    'borrado' => $borrado
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
            $resultado['mensaje'] = "NO SE ENCONTRARON DATOS DE COLEGIACION ANUAL";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando DATOS DE COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiacionAnualPorId($idColegiacionAnual) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiacion_anual WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiacionAnual);
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $antiguedad, $importe, $primerVencimiento, $cuotas, $pagoTotal, $vencimientoPagoTotal, $idUsuario, $fechaCarga, $borrado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idColegiacionAnual' => $id,
                    'periodo' => $periodo,
                    'antiguedad' => $antiguedad,
                    'importe' => $importe,
                    'vencimientoCuotaUno' => $primerVencimiento,
                    'cuotas' => $cuotas,
                    'pagoTotal' => $pagoTotal, 
                    'vencimientoPagoTotal' => $vencimientoPagoTotal,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga,
                    'borrado' => $borrado
                );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "NO SE ENCONTRO COLEGIACION ANUAL";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiacionAnualPorPeriodo($periodoActual, $antiguedad) {
    if (isset($antiguedad)) {
        $conAntiguedad = " AND Antiguedad = ".$antiguedad;
    } else {
        $conAntiguedad = "";
    }
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiacion_anual 
        WHERE Periodo = ? AND Borrado = 0 ".$conAntiguedad."
        ORDER BY Antiguedad";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodoActual);
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $antiguedad, $importe, $primerVencimiento, $cuotas, $pagoTotal, $vencimientoPagoTotal, $idUsuario, $fechaCarga, $borrado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiacionAnual' => $id,
                    'periodo' => $periodo,
                    'antiguedad' => $antiguedad,
                    'importe' => $importe,
                    'vencimientoCuotaUno' => $primerVencimiento,
                    'cuotas' => $cuotas,
                    'pagoTotal' => $pagoTotal, 
                    'vencimientoPagoTotal' => $vencimientoPagoTotal,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga,
                    'borrado' => $borrado
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
            $resultado['mensaje'] = "NO SE ENCONTRARON DATOS DE COLEGIACION ANUAL";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando DATOS DE COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiacionAnualCuotas($periodoActual) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
        //obtenemos las cuotas a generar si es que no vienen por parametro
    $sql = "SELECT ca.Antiguedad, cac.Cuota, cac.Importe, cac.FechaVencimiento
        FROM colegiacion_anual_cuota cac
        INNER JOIN colegiacion_anual ca ON ( ca.Periodo = ? AND ca.Id = cac.IdColegiacionAnual)
        WHERE cac.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodoActual);
    $stmt->execute();
    $stmt->bind_result($antiguedad, $cuota, $importe, $fechaVencimiento);
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $cuotasLiquidar = array();
        while (mysqli_stmt_fetch($stmt)) 
        {
            $row = array (
                'antiguedad' => $antiguedad,
                'cuota' => $cuota,
                'importe' => $importe,
                'fechaVencimiento' => $fechaVencimiento
                );
            array_push($cuotasLiquidar, $row);
        }   
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $cuotasLiquidar;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL BUSCAR CUOTAS colegiacion_anual_cuotas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerPagoTotal($periodo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT PagoTotal, VencimientoPagoTotal 
        FROM colegiacion_anual 
        WHERE Periodo = ? AND Borrado = 0 
        ORDER BY Antiguedad";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodo);
    $stmt->execute();
    $stmt->bind_result($pagoTotal, $vencimientoPagoTotal);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'importe' => $pagoTotal, 
                    'vencimiento' => $vencimientoPagoTotal
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
            $resultado['mensaje'] = "NO SE ENCONTRARON DATOS DE PAGO TOTAL";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando DATOS DE PAGO TOTAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerCuotasAgregar($periodo, $cuotaInicio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ca.Antiguedad, ca.Importe, cac.Cuota, cac.Importe, cac.FechaVencimiento
        FROM colegiacion_anual ca
        INNER JOIN colegiacion_anual_cuota cac ON cac.IdColegiacionAnual = ca.Id
        WHERE ca.Periodo = ? AND cac.Cuota >= ?
        ORDER BY ca.Antiguedad, cac.Cuota";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $periodo, $cuotaInicio);
    $stmt->execute();
    $stmt->bind_result($antiguedad, $importeTotal, $cuota, $importe, $vencimiento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'antiguedad' => $antiguedad,
                    'importeTotal' => $importeTotal,
                    'cuota' => $cuota,
                    'importe' => $importe,
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "NO SE ENCONTRARON DATOS DE COLEGIACION ANUAL";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando DATOS DE COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;    
}

function agregarColegiacionAnual($periodo, $cuotas, $antiguedad, $importe, $vencimientoCuotaUno, $pagoTotal, $vencimientoPagoTotal) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql = "INSERT INTO colegiacion_anual 
            (Periodo, Antiguedad, Cuotas, Importe, PrimerVencimiento, PagoTotal, VencimientoPagoTotal, IdUsuario, FechaCarga)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiissssi', $periodo, $antiguedad, $cuotas, $importe, $vencimientoCuotaUno, $pagoTotal, $vencimientoPagoTotal, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE REGISTRO COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR COLEGIACION ANUAL ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;
}

function editarColegiacionAnual($idColegiacionAnual, $periodo, $cuotas, $antiguedad, $importe, $vencimientoCuotaUno, $pagoTotal, $vencimientoPagoTotal) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql = "UPDATE colegiacion_anual 
            SET Periodo = ?, Antiguedad = ?, Cuotas = ?, Importe = ?, PrimerVencimiento = ?, PagoTotal = ?, VencimientoPagoTotal = ?, IdUsuario = ?, FechaCarga = NOW()
            WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiissssii', $periodo, $antiguedad, $cuotas, $importe, $vencimientoCuotaUno, $pagoTotal, $vencimientoPagoTotal, $_SESSION['user_id'], $idColegiacionAnual);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_errno($stmt) == 0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "SE MODIFICO COLEGIACION ANUAL";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL MODIFICAR COLEGIACION ANUAL ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;
}

function generarColegiacionAnual($idColegiado, $antiguedad, $estadoMatricular, $datosColegiacion, $descuentaPagos, $cuotasVerificar, $conect, $cuotasLiquidar) {
    //agrega colegiacion anual y cuotas
    $periodoActual = $datosColegiacion['periodo'];
    try {
        /* Autocommit false para la transaccion */
        if (!isset($conect)) {
            $conect = conectar();
        }
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $resultado['estado'] = TRUE;

        $importeTotal = $datosColegiacion['importe'];
        $importeDescontar = 0;
        
        if (!isset($cuotasLiquidar)) {
            $resCuotasColegiacion = obtenerColegiacionAnualCuotas($periodoActual);
            if ($resCuotasColegiacion['estado']) {
                $cuotasLiquidar = $resCuotasColegiacion['datos'];
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL BUSCAR CUOTAS colegiacion_anual_cuotas";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        }

        if ($resultado['estado']) {
            //se agrega colegiadodeudaanua
            $sql="INSERT INTO colegiadodeudaanual
                    (IdColegiado, Periodo, Importe, Cuotas, Antiguedad, EstadoMatricular, FechaCreacion, ImporteDescuento) 
                    VALUE (?, ?, ?, ?, ?, ?, date(now()), ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iisiiis', $idColegiado, $periodoActual, $datosColegiacion['importe'], $datosColegiacion['cuotas'], $antiguedad, $estadoMatricular, $importeDescontar);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt)==0) {
                $idColegiadoDeudaAnual = $conect->insert_id;
                $sql="INSERT INTO log_tabla 
                    (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                    VALUES ('colegiadodeudaanual', ?, now(), 'alta', ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idColegiadoDeudaAnual, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
                    $resultado['estado'] = TRUE;
                    //agrego las cuotas
                    foreach ($cuotasLiquidar as $datosCuota) {
                        //si no es de la misma antiguedad lo salteo
                        if ($antiguedad <> $datosCuota['antiguedad']) { continue; }

                        $primerVencimiento = $datosCuota['fechaVencimiento'];
                        $segundoVencimiento = $datosCuota['fechaVencimiento'];
                        $cuota = $datosCuota['cuota'];
                        $importe = $datosCuota['importe'];
                        $recargo = $datosCuota['importe'];
                        $estadoCuota = 1;
                        if ($primerVencimiento <= date('Y-m-d')) {
                            $estadoCuota = 5;
                        }
                        $sql1 = "INSERT INTO colegiadodeudaanualcuotas
                                (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento, Estado) 
                                VALUE (?, ?, ?, ?, ?, ?, ?)";
                        $stmt1 = $conect->prepare($sql1);
                        $stmt1->bind_param('iissssi', $idColegiadoDeudaAnual, $cuota, $importe, $primerVencimiento, $recargo, $segundoVencimiento, $estadoCuota);
                        $stmt1->execute();
                        $stmt1->store_result();
                        if (mysqli_stmt_errno($stmt1)<>0) {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] .= "ERROR AL AGREGAR CUOTAS DE COLEGIACION";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }                            
                    }

                    //se inserta el pago total si no esta vencido
                    if ($resultado['estado']) {
                        if ($datosColegiacion['vencimientoPagoTotal'] > date('Y-m-d')) {
                            if ($importeDescontar > 0) {
                                $importePagoTotal = ($datosColegiacion['importe'] - $importeDescontar) * 0.90;    
                            } else {
                                $importePagoTotal = $datosColegiacion['pagoTotal'];
                            }
                            
                            $sql1 = "INSERT INTO colegiadodeudaanualtotal
                                    (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado) 
                                    VALUE (?, ?, ?, ?)";
                            $stmt1 = $conect->prepare($sql1);
                            $stmt1->bind_param('issi', $idColegiadoDeudaAnual, $importePagoTotal, $datosColegiacion['vencimientoPagoTotal'], $estadoCuota);
                            $stmt1->execute();
                            $stmt1->store_result();
                            if (mysqli_stmt_errno($stmt1)<>0) {
                                $resultado['estado'] = FALSE;
                                $resultado['mensaje'] .= "ERROR AL AGREGAR PAGO TOTAL DE COLEGIACION";
                                $resultado['clase'] = 'alert alert-error'; 
                                $resultado['icono'] = 'glyphicon glyphicon-remove';
                            }
                        }
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] .= "ERROR AL BUSCAR CUOTAS DE COLEGIACION".mysqli_stmt_error($stmt1);
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL_LOG";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL. ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        }

        if ($resultado['estado']) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'SE GENERO LA DEUDA ANUAL CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
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

function regenerarColegiacionAnual_2021($idColegiadoDeudaAnual, $cuotas, $pagoTotal) {
    //agrega colegiacion anual y cuotas
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "";
        foreach ($cuotas as $cuotaGenera) {
            $estadoCuota = 1;
            $cuota = $cuotaGenera['cuota'];
            $vencimiento = $cuotaGenera['vencimiento'];
            $importe = $cuotaGenera['importe'];
            $importeTotal = $cuotaGenera['importeTotal'];
            //si la fecha de vencimiento de la cuota es dentro de 10 dias, entonces no se cobra
            //if ($primerVencimiento <= sumarRestarSobreFecha(date('Y-m-d'), 10, 'day', '+')) {
            if ($vencimiento <= date('Y-m-d')) {
                $estadoCuota = 5;
            }

            $segundoVencimiento = $vencimiento;
            $recargo = $importe;
            //}
            $sql1 = "INSERT INTO colegiadodeudaanualcuotas
                (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento, Estado) 
                VALUE (?, ?, ?, ?, ?, ?, ?)";
            $stmt1 = $conect->prepare($sql1);
            $stmt1->bind_param('iissssi', $idColegiadoDeudaAnual, $cuota, $importe, $vencimiento, $recargo, $vencimiento, $estadoCuota);
            $stmt1->execute();
            $stmt1->store_result();
            if (mysqli_stmt_errno($stmt1)<>0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] .= "ERROR AL AGREGAR CUOTAS DE COLEGIACION";
                $resultado['clase'] = 'alert alert-error';
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        }

        //carga el importe total en colegiadodeudaanual
        if ($resultado['estado']) {
            $sql1 = "UPDATE colegiadodeudaanual
                SET Importe = ?, FechaCreacion = DATE(NOW())
                WHERE Id = ?";
            $stmt1 = $conect->prepare($sql1);
            $stmt1->bind_param('si', $importeTotal, $idColegiadoDeudaAnual);
            $stmt1->execute();
            $stmt1->store_result();
            if (mysqli_stmt_errno($stmt1)<>0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] .= "ERROR AL ACTUALIZAR EL TOTAL DE COLEGIACION";
                $resultado['clase'] = 'alert alert-error';
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }    
        }

        //se inserta el pago total si no esta vencido
        if ($resultado['estado']) {
            if ($pagoTotal['vencimiento'] > date('Y-m-d')) {
                $sql1 = "INSERT INTO colegiadodeudaanualtotal
                            (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado) 
                            VALUE (?, ?, ?, ?)";
                $stmt1 = $conect->prepare($sql1);
                $stmt1->bind_param('issi', $idColegiadoDeudaAnual, $pagoTotal['importe'], $pagoTotal['vencimiento'], $estadoCuota);
                $stmt1->execute();
                $stmt1->store_result();
                if (mysqli_stmt_errno($stmt1)<>0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL AGREGAR PAGO TOTAL DE COLEGIACION";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL BUSCAR CUOTAS DE COLEGIACION".mysqli_stmt_error($stmt1);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'SE GENERO LA DEUDA ANUAL CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
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

function generarColegiacionAnual_DividiendoPorCuotas($idColegiado, $antiguedad, $estadoMatricular, $datosColegiacion, $descuentaPagos, $cuotasVerificar, $conect) {
    //agrega colegiacion anual y cuotas
    $periodoActual = $datosColegiacion['periodo'];
    try {
        /* Autocommit false para la transaccion */
        if (!isset($conect)) {
            $conect = conectar();
        }
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        $importeTotal = $datosColegiacion['importe'];
        $importeDescontar = 0;
        /*
        //solo para el periodo 2020
        if ($descuentaPagos == "S") {
            //si se desuentan cuotas abonadas del periodo anterio
            $periodoVerificar = $periodoActual-1;
            $sql = "SELECT cdac.Estado, COUNT(cdac.Cuota), SUM(cdac.Importe)
                FROM colegiadodeudaanualcuotas cdac
                INNER JOIN colegiadodeudaanual cda ON cda.Id = cdac.IdColegiadoDeudaAnual
                WHERE cda.IdColegiado = ? AND cda.Periodo = ? 
                AND cdac.Cuota IN(".$cuotasVerificar.")
                GROUP BY cdac.Estado";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idColegiado, $periodoVerificar);
            $stmt->execute();
            $stmt->bind_result($estado, $cantidad, $importe);
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                while (mysqli_stmt_fetch($stmt)) 
                {
                    if ($estado == 2 || $estado == 8) {
                        $importeDescontar += $importe;
                    }
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL BUSCAR CUOTAS ABONADAS";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        }
        $impoorteTotal -= $importeDescontar;
        */

        //se agrega colegiadodeudaanua
        $sql="INSERT INTO colegiadodeudaanual
                (IdColegiado, Periodo, Importe, Cuotas, Antiguedad, EstadoMatricular, FechaCreacion, ImporteDescuento) 
                VALUE (?, ?, ?, ?, ?, ?, date(now()), ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisiiis', $idColegiado, $periodoActual, $datosColegiacion['importe'], $datosColegiacion['cuotas'], $antiguedad, $estadoMatricular, $importeDescontar);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $idColegiadoDeudaAnual = $conect->insert_id;
            $sql="INSERT INTO log_tabla 
                (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                VALUES ('colegiadodeudaanual', ?, now(), 'alta', ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idColegiadoDeudaAnual, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
                $resultado['estado'] = TRUE;
                //agrego las cuotas
                $primerVencimiento = new DateTime($datosColegiacion['vencimientoCuotaUno']);
                $importeAnual = $importeTotal;
                $importe = $importeTotal / $datosColegiacion['cuotas'];

                $importeRedondeado = round($importe, -1);
                $importeRedondeadoTotal = $importeRedondeado * $datosColegiacion['cuotas'];
                $importeUltimaCuota = $importeRedondeado + ($importeAnual - $importeRedondeadoTotal);
                $importe = $importeRedondeado;
                $cuota = 1;
                while ($cuota <= $datosColegiacion['cuotas'] && $resultado['estado']) {
                    $estadoCuota = 1;
                    
                    //si la fecha de vencimiento de la cuota es dentro de 10 dias, entonces no se cobra
                    //if ($primerVencimiento <= sumarRestarSobreFecha(date('Y-m-d'), 10, 'day', '+')) {
                    if ($primerVencimiento->format('Y-m-d') <= date('Y-m-d')) {
                        $estadoCuota = 5;
                    }

                    $segundoVencimiento = $primerVencimiento->format('Y-m-d');
                    if ($cuota == $datosColegiacion['cuotas']) {
                        $importe = $importeUltimaCuota;
                    }
                    //if (!isset($recargo)) {
                    $recargo = $importe;
                    //}
                    $sql1 = "INSERT INTO colegiadodeudaanualcuotas
                            (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento, Estado) 
                            VALUE (?, ?, ?, ?, ?, ?, ?)";
                    $stmt1 = $conect->prepare($sql1);
                    $stmt1->bind_param('iissssi', $idColegiadoDeudaAnual, $cuota, $importe, $primerVencimiento->format('Y-m-d'), $recargo, $segundoVencimiento, $estadoCuota);
                    $stmt1->execute();
                    $stmt1->store_result();
                    if (mysqli_stmt_errno($stmt1)<>0) {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] .= "ERROR AL AGREGAR CUOTAS DE COLEGIACION";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }    

                    $cuota++;  
                    //$primerVencimiento->add(new DateInterval('P30D'));           
                    $primerVencimiento->add(new DateInterval('P1D'));           
                    $primerVencimiento->modify('last day of this month');
                }

                //se inserta el pago total si no esta vencido
                if ($resultado['estado']) {
                    if ($datosColegiacion['vencimientoPagoTotal'] > date('Y-m-d')) {
                        if ($importeDescontar > 0) {
                            $importePagoTotal = ($datosColegiacion['importe'] - $importeDescontar) * 0.90;    
                        } else {
                            $importePagoTotal = $datosColegiacion['pagoTotal'];
                        }
                        
                        $sql1 = "INSERT INTO colegiadodeudaanualtotal
                                (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado) 
                                VALUE (?, ?, ?, ?)";
                        $stmt1 = $conect->prepare($sql1);
                        $stmt1->bind_param('issi', $idColegiadoDeudaAnual, $importePagoTotal, $datosColegiacion['vencimientoPagoTotal'], $estadoCuota);
                        $stmt1->execute();
                        $stmt1->store_result();
                        if (mysqli_stmt_errno($stmt1)<>0) {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] .= "ERROR AL AGREGAR PAGO TOTAL DE COLEGIACION";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL BUSCAR CUOTAS DE COLEGIACION".mysqli_stmt_error($stmt1);
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL_LOG";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL. ".mysqli_stmt_error($stmt);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'SE GENERO LA DEUDA ANUAL CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
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

