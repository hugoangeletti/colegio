<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');
require_once ('../../dataAccess/colegiadoPlanPagoLogic.php');
require_once ('../../dataAccess/debitoAutomaticoLogic.php');

$periodoActual = $_SESSION['periodoActual'];
$continua = TRUE;
$mensaje = "";
if (isset($_POST['tipoDebito']) && str_contains("DCH", $_POST['tipoDebito'])) {
    $tipoDebito = $_POST['tipoDebito'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta tipoDebito ".$_POST['tipoDebito']." - ";
}
if (isset($_POST['fechaDebito']) && $_POST['fechaDebito'] <> "") {
    $fechaDebito = $_POST['fechaDebito'];
    $fecha = new DateTime($fechaDebito);
    $fechaVencimientoDebito = $fecha->format('Y-m-t');
} else {
    $continua = FALSE;
    $mensaje .= "Falta fechaDebito - ";
}
if ($continua) {
    switch ($tipoDebito) {
        case 'D':
        case 'C':
            // tarjeta de debito
            $resDebitos = obtenerColegiadosDebitarPorTipo($tipoDebito);
            if ($resDebitos['estado']) {
                echo 'Cantidad de colegiados -> '.$resDebitos['cantidad'].'<br>';
                if ($resDebitos['cantidad'] > 0) {
                    //se agrega el lote de enviodebito
                    $nombreArchivo = 'DEBLIQ'.$tipoDebito.'.txt';
                    $pathArchivo = 'archivos/envioLote/tarjeta_debito/'.date('Ymd');
                    $resEnvio = agregarEnvioDebito($fechaDebito, $tipoDebito, $nombreArchivo, $pathArchivo);
                    if ($resEnvio['estado']) {
                        $idEnvioDebito = $resEnvio['idEnvioDebito'];
                        //recorro los colegiados adheridos al debito por Tarjeta (Debito/Credito)                
                        foreach ($resDebitos['datos'] as $dato) {
                            $matricula = $dato['matricula'];
                            $identificador = $dato['numeroDocumento'];
                            $numeroTarjeta = $dato['numeroTarjeta'];
                            $primerProceso = $dato['primerProceso'];
                            $idColegiado = $dato['idColegiado'];
                            $incluyePlanPagos = $dato['incluyePlanPagos'];
                            $idDebitoTarjeta = $dato['idDebitoTarjeta'];
                            $pagoTotal = $dato['pagoTotal'];
                            $idEnvioDebitoDetalle = NULL;

                            $resDeudaAnual = obtenerColegiadoDeudaAnualAPagar($idColegiado);
                            if ($resDeudaAnual['estado']) {
                                //recorro las cuotas adeudadas 
                                foreach ($resDeudaAnual['datos'] as $deuda) {
                                    //si no esta generado enviodebitodetale lo agrego para luego agregar las cuotas del colegiado
                                    if (!isset($idEnvioDebitoDetalle) || $idEnvioDebitoDetalle == 0) {
                                        $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                        if ($resEnvioDetalle['estado']) {
                                            $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                        } else {
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                            exit;
                                        }
                                    }
                                    //preparo los datos para cargar en enviodebitodetallecuotas
                                    $referencia = $deuda['idColegiadoDeudaAnualCuota'];
                                    $periodo = $deuda['periodo'];
                                    $fechaVencimiento = $deuda['fechaVencimiento'];
                                    $importeActualizado = $deuda['importeActualizado'];
                                    if ($pagoTotal == 'S' && $periodo == $periodoActual) { continue; }
                                    if ($periodo == $periodoActual && $fechaVencimiento > $fechaVencimientoDebito) { continue; }

                                    $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'C', $referencia, $importeActualizado);
                                    if (!$resEnvioDetalleCuota['estado']) { 
                                        $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                        exit;
                                    }
                                }
                            }

                            //si se le debita el total
                            if ($pagoTotal = 'S') {
                                $resPagoTotal = obtenerPagoTotalVigentePorIdColegiado($idColegiado, $periodoActual);
                                if ($resPagoTotal['estado']) {
                                    $pagoTotal = $resPagoTotal['datos'];
                                    $referencia = $pagoTotal['idColegiadoDeudaAnualTotal'];
                                    $importe = $pagoTotal['importe'];
                                    $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                    if ($resEnvioDetalle['estado']) {
                                        $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                        $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'T', $referencia, $importe);
                                        if (!$resEnvioDetalleCuota['estado']) { 
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                        }
                                    } else {
                                        $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                    }
                                } else {
                                    $mensaje .= $resPagoTotal['mensaje'];
                                }
                            }

                            //si solicito debito de plan de pagos
                            if ($incluyePlanPagos = 'S') {
                                $resPlanPago = obtenerDeudaPlanPagosPorIdColegiado($idColegiado);
                                if ($resPlanPago['estado']) {
                                    foreach ($resPlanPago as $planPago) {
                                        if (!isset($idEnvioDebitoDetalle) || $IdEnvioDebitoDetalle == 0) {
                                            $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                            if ($resEnvioDetalle['estado']) {
                                                $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                            } else {
                                                $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                                exit;
                                            }
                                        }
                                        //preparo los datos para cargar en enviodebitodetallecuotas
                                        $referencia = $planPago['idPlaPagoCuota'];
                                        $importeActualizado = $planPago['importeActualizado'];

                                        $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'P', $referencia, $importeActualizado);
                                        if (!$resEnvioDetalleCuota['estado']) { 
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula.' Plan de pago';
                                            exit;
                                        }
                                    }
                                } else {
                                    $mensaje .= $resPlanPago['mensaje'];
                                }
                            }
                        }
                    } else {
                        $mensaje .= $resEnvio['mensaje'];
                    }
                } else {
                    $mensaje .= 'No hay debitos para procesar';
                }
            } else {
                $mensaje = $resDebitos['mensaje'];
            }
            break;
        
        case 'H':
            // cbu
            $resDebitos = obtenerColegiadosDebitarPorCBU();
            if ($resDebitos['estado']) {
                echo 'Cantidad de colegiados -> '.$resDebitos['cantidad'].'<br>';
                if ($resDebitos['cantidad'] > 0) {
                    //se agrega el lote de enviodebito
                    $nombreArchivo = 'DEBITO POR CBU';
                    $pathArchivo = 'archivos/envioLote/cbu/'.date('Ymd');
                    $resEnvio = agregarEnvioDebito($fechaDebito, $tipoDebito, $nombreArchivo, $pathArchivo);
                    if ($resEnvio['estado']) {
                        $idEnvioDebito = $resEnvio['idEnvioDebito'];
                        //recorro los colegiados adheridos al debito por Tarjeta (Debito/Credito)                
                        foreach ($resDebitos['datos'] as $dato) {
                            $matricula = $dato['matricula'];
                            $identificador = $dato['numeroDocumento'];
                            $tipoCuenta = $dato['tipoCuenta'];
                            $cbuBloque1 = $dato['cbuBloque1'];
                            $idBanco = $dato['idBanco'];
                            $cbuBloque2 = $dato['cbuBloque2'];
                            $idColegiado = $dato['idColegiado'];
                            $incluyePlanPagos = $dato['incluyePlanPagos'];
                            $idDebitoCbu = $dato['idDebitoCbu'];
                            $pagoTotal = $dato['pagoTotal'];
                            $idEnvioDebitoDetalle = NULL;

                            $resDeudaAnual = obtenerColegiadoDeudaAnualAPagar($idColegiado);
                            if ($resDeudaAnual['estado']) {
                                //recorro las cuotas adeudadas 
                                foreach ($resDeudaAnual['datos'] as $deuda) {
                                    //si no esta generado enviodebitodetale lo agrego para luego agregar las cuotas del colegiado
                                    if (!isset($idEnvioDebitoDetalle) || $IdEnvioDebitoDetalle == 0) {
                                        $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                        if ($resEnvioDetalle['estado']) {
                                            $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                        } else {
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                            exit;
                                        }
                                    }
                                    //preparo los datos para cargar en enviodebitodetallecuotas
                                    $referencia = $deuda['idColegiadoDeudaAnualCuota'];
                                    $periodo = $deuda['periodo'];
                                    $fechaVencimiento = $deuda['fechaVencimiento'];
                                    $importeActualizado = $deuda['importeActualizado'];
                                    if ($pagoTotal = 'S' && $periodo == $periodoActual) { continue; }
                                    if ($periodo == $periodoActual && $fechaVencimiento > $fechaVencimientoDebito) { continue; }

                                    $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'C', $referencia, $importeActualizado);
                                    if (!$resEnvioDetalleCuota['estado']) { 
                                        $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                        exit;
                                    }
                                }
                            }

                            //si se le debita el total
                            if ($pagoTotal = 'S') {
                                $resPagoTotal = obtenerPagoTotalVigentePorIdColegiado($idColegiado, $periodoActual);
                                if ($resPagoTotal['estado']) {
                                    $pagoTotal = $resPagoTotal['datos'];
                                    $referencia = $pagoTotal['idColegiadoDeudaAnualTotal'];
                                    $importe = $pagoTotal['importe'];
                                    $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                    if ($resEnvioDetalle['estado']) {
                                        $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                        $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'T', $referencia, $importe);
                                        if (!$resEnvioDetalleCuota['estado']) { 
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                        }
                                    } else {
                                        $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                    }
                                } else {
                                    $mensaje .= $resPagoTotal['mensaje'];
                                }
                            }

                            //si solicito debito de plan de pagos
                            if ($incluyePlanPagos = 'S') {
                                $resPlanPago = obtenerDeudaPlanPagosPorIdColegiado($idColegiado);
                                if ($resPlanPago['estado']) {
                                    foreach ($resPlanPago as $planPago) {
                                        if (!isset($idEnvioDebitoDetalle) || $IdEnvioDebitoDetalle == 0) {
                                            $resEnvioDetalle = agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta);
                                            if ($resEnvioDetalle['estado']) {
                                                $idEnvioDebitoDetalle = $resEnvioDetalle['idEnvioDebitoDetalle'];
                                            } else {
                                                $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula;
                                                exit;
                                            }
                                        }
                                        //preparo los datos para cargar en enviodebitodetallecuotas
                                        $referencia = $planPago['idPlaPagoCuota'];
                                        $importeActualizado = $planPago['importeActualizado'];

                                        $resEnvioDetalleCuota = agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, 'P', $referencia, $importeActualizado);
                                        if (!$resEnvioDetalleCuota['estado']) { 
                                            $mensaje .= "Error agregando enviodebitodetalle para ".$idDebitoTarjeta." - > matricula: ".$matricula.' Plan de pago';
                                            exit;
                                        }
                                    }
                                } else {
                                    $mensaje .= $resPlanPago['mensaje'];
                                }
                            }
                        }
                    } else {
                        $mensaje .= $resEnvio['mensaje'];
                    }
                } else {
                    $mensaje .= 'No hay debitos para procesar';
                }
            } else {
                $mensaje = $resDebitos['mensaje'];
            }
            break;
        
        default:
            // code...
            break;
    }
} else {
    echo $mensaje;
}

echo $mensaje; exit;