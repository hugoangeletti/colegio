<?php
function obtenerCajasDiarias($anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cd.Id, cd.FechaApertura, cd.HoraApertura, cd.TotalRecaudacion, cd.FechaCierre, cd.Estado
		FROM cajadiaria cd
		WHERE SUBSTR(cd.FechaApertura, 1, 4) = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $anio);
    $stmt->execute();
    $stmt->bind_result($idCajaDiaria, $fechaApertura, $horaApertura, $totalRecaudacion, $fechaCierre, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idCajaDiaria' => $idCajaDiaria,
                    'fechaApertura' => $fechaApertura,
                    'horaApertura' => $horaApertura,
                    'totalRecaudacion' => $totalRecaudacion,
                    'fechaCierre' => $fechaCierre,
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existen caja diaria para el año seleccionado";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cajas diarias";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}

function obtenerCajaDiariaPorId($idCajaDiaria) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cd.*
		FROM cajadiaria cd
		WHERE cd.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiaria);
    $stmt->execute();
    $stmt->bind_result($idCajaDiaria, $fechaApertura, $horaApertura, $saldoInicial, $totalRecaudacion, $diferenciaImporte, $fechaCierre, $horaCierre, $saldoFinal, $idUsuarioApertura, $idUsuarioCierre, $estado, $peridoContable, $numeroParte);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array (
            'idCajaDiaria' => $idCajaDiaria,
            'fechaApertura' => $fechaApertura,
            'horaApertura' => $horaApertura,
            'saldoInicial' => $saldoInicial,
            'totalRecaudacion' => $totalRecaudacion,
            'diferenciaImporte' => $diferenciaImporte,
            'fechaCierre' => $fechaCierre,
            'saldoFinal' => $saldoFinal,
            'idUsuarioApertura' => $idUsuarioApertura,
            'idUsuarioCierre' => $idUsuarioCierre,
            'estado' => $estado
         );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cajas diarias";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerCajaAbierta() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
	$sql = "SELECT MAX(cd.Id), cd.FechaApertura FROM cajadiaria cd WHEre Estado='A'";
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('ii', $expediente, $anio);
    $stmt->execute();
    $stmt->bind_result($idCajaDiaria, $fechaApertura);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if (isset($idCajaDiaria) && $idCajaDiaria > 0) {
            	$datos = array(
            			'idCajaDiaria' => $idCajaDiaria,
            			'fechaApertura' => $fechaApertura
            		);
                $resultado['estado'] = TRUE;
                $resultado['datos'] = $datos;
            } else {
                $resultado['estado'] = TRUE;
                $resultado['datos'] = NULL;
            }
        } else {
            $resultado['estado'] = FALSE;
        }
    } else {
        $resultado['estado'] = FALSE;
    }
    return $resultado;
}

function abririCajaDiaria($fechaCaja, $saldoInicial) {
	$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql = "INSERT INTO cajadiaria (FechaApertura, HoraApertura, SaldoInicial, IdUsuarioApertura, Estado)
            VALUES(?, time(NOW()), ?, ?, 'A')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssi', $fechaCaja, $saldoInicial, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $idCajaDiaria = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
        $resultado['idCajaDiaria'] = $idCajaDiaria;
        $resultado['mensaje'] = 'OK';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';            	
    } else {
        $resultado['estado'] = FALSE;
        $resultado['idCajaDiaria'] = NULL;
        $resultado['mensaje'] = 'ERROR al abrir cajadiaria';
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';            	
    }
    return $resultado;
}

function cerrarCajaDiaria($idCajaDiaria, $totalRecaudacion) {
	$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql = "UPDATE cajadiaria 
    		SET FechaCierre = date(now()), 
    			HoraCierre = time(NOW()), 
    			TotalRecaudacion = ?, 
    			SaldoFinal = (SaldoInicial + ?), 
    			IdUsuarioCierre = ?,
                Estado = 'C'
			WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssii', $totalRecaudacion, $totalRecaudacion, $_SESSION['user_id'], $idCajaDiaria);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['idCajaDiaria'] = $idCajaDiaria;
        $resultado['mensaje'] = 'OK';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';            	
    } else {
        $resultado['estado'] = FALSE;
        $resultado['idCajaDiaria'] = NULL;
        $resultado['mensaje'] = 'ERROR al cerrar cajadiaria';
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';            	
    }
    return $resultado;
}

function generarReciboCajaDiariaOtrosIngresos($tipoRecibo, $nombre, $cuit, $domicilio, $concepto, $importe, $tipoPago, $idFormaPago, $idBanco, $comprobante){
    $continua = TRUE;
    $resCajaDiaria = obtenerCajaAbierta();
    if ($resCajaDiaria['estado']) {
        $idCajaDiaria = $resCajaDiaria['datos']['idCajaDiaria'];
    } else {
        $continua = FALSE;
        $mensaje .= 'No hay caja del día abierta, debe ir a generar una caja. ';
    }

    $tipoComprobante = 'RE';
    $resRecibo = ObtenerNumeroComprobante($tipoComprobante);
    if ($resRecibo['numeroRecibo']) {
        $numeroRecibo = $resRecibo['numeroRecibo'];
    } else {
        $continua = FALSE;
        $mensaje .= 'No se pudo obtener el numeroRecibo por tipoComprobante. ';     
    }

    if ($continua) {
        try {
            /* Autocommit false para la transaccion */
            $conect = conectar();
            mysqli_set_charset( $conect, 'utf8');
            $conect->autocommit(FALSE);
            $resultado = array();

            $sql = "INSERT INTO cajadiariamovimiento (IdCajaDiaria, Fecha, Hora, Monto, IdUsuario, Tipo, Numero, Estado)
                    VALUES(?, date(NOW()), time(NOW()), ?, ?, 'RE', ?, 'I')";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iiii', $idCajaDiaria, $importe, $_SESSION['user_id'], $numeroRecibo);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $idCajaDiariaMovimiento = mysqli_stmt_insert_id($stmt);
                //guardar los datos de la persona/razon social
                $sql = "INSERT INTO cajadiariamovimientootro (IdCajaDiariaMovimiento, Descripcion, Domicilio, CUIT)
                        VALUES(?, ?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('isss', $idCajaDiariaMovimiento, $nombre, $domicilio, $cuit);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    //agregamos el concepto
                    $sql = "INSERT INTO cajadiariamovimientodetalle (IdCajaDiariaMovimiento, CodigoPago, Monto, Concepto)
                            VALUES(?, ?, ?, ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('iiss', $idCajaDiariaMovimiento, $tipoPago, $importe, $concepto);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $sql = "INSERT INTO cajadiariamovimientopago (IdCajaDiariaMovimiento, IdFormaPago, IdBanco, Monto, Detalle)
                                VALUES(?, ?, ?, ?, ?)";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('iiiss', $idCajaDiariaMovimiento, $idFormaPago, $idBanco, $importe, $comprobante);
                        $stmt->execute();
                        $stmt->store_result();
                        if(mysqli_stmt_errno($stmt)==0) {
                            //si es FIRMA, constancias de firmas debo marcar en constanciafirma que ya fueron emitidos los recibos
                            if ($tipoRecibo == "FIRMA") {
                                $sql = "UPDATE constanciafirma 
                                        SET IdCajaDiariaMovimiento = ?
                                        WHERE Fecha = DATE(NOW()) AND IdCajaDiariaMovimiento IS NULL";
                                $stmt = $conect->prepare($sql);
                                $stmt->bind_param('i', $idCajaDiariaMovimiento);
                                $stmt->execute();
                                $stmt->store_result();                            
                            }
                            $resultado['estado'] = TRUE;
                            $resultado['mensaje'] = "OK";
                            $resultado['clase'] = 'alert alert-success'; 
                            $resultado['icono'] = 'glyphicon glyphicon-ok';             
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientodetalle ".mysqli_stmt_error($stmt);
                            $resultado['clase'] = 'alert alert-danger'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';             
                        }
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientodetalle ".mysqli_stmt_error($stmt);
                        $resultado['clase'] = 'alert alert-danger'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';             
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientootro ".mysqli_stmt_error($stmt);
                    $resultado['clase'] = 'alert alert-danger'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';             
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiaria ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-danger'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';             
            }

            if ($resultado['estado']) {
                $resultado['idCajaDiariaMovimiento'] = $idCajaDiariaMovimiento;
                $conect->commit();
                desconectar($conect);
                return $resultado;
            } else {
                $conect->rollback();
                desconectar($conect);
                $resultado['mensaje'] .= ' (Generar del Recibo por Cajas Diarias, manualmente)';
                return $resultado;
            }
        } catch (mysqli_sql_exception $e) {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }   
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL OBTENER cajadiaria ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';             
        return $resultado;
    }
}

function generarReciboCajaDiaria($idColegiado, $tipoRecibo, $generarRecibo, $generarReciboPP, $conRecargo, $idAsistente, $idFormaPago, $idBanco, $comprobante){
    $continua = TRUE;
	$resCajaDiaria = obtenerCajaAbierta();
	if ($resCajaDiaria['estado']) {
	    $idCajaDiaria = $resCajaDiaria['datos']['idCajaDiaria'];
	} else {
	    $continua = FALSE;
	    $mensaje .= 'No hay caja del día abierta, debe ir a generar una caja. ';
	}

	$tipoComprobante = 'RE';
	$resRecibo = ObtenerNumeroComprobante($tipoComprobante);
	if ($resRecibo['numeroRecibo']) {
		$numeroRecibo = $resRecibo['numeroRecibo'];
	} else {
	    $continua = FALSE;
	    $mensaje .= 'No se pudo obtener el numeroRecibo por tipoComprobante. ';		
	}

	if ($continua) {
		/*
		print_r($generarRecibo);
		echo '<br>';
		echo 'idCajaDiaria->'.$idCajaDiaria.' numeroRecibo->'.$numeroRecibo.'<br>';
		exit;
		*/
		try {
			/* Autocommit false para la transaccion */
	        $conect = conectar();
	        mysqli_set_charset( $conect, 'utf8');
	        $conect->autocommit(FALSE);
	        $resultado = array();

	        //obtengo el detalle para generar el recibo
			//obtengo la deuda, inicializo los campos a mostrar

	        $sql = "INSERT INTO cajadiariamovimiento (IdCajaDiaria, Fecha, Hora, Monto, IdUsuario, Tipo, Numero, IdColegiado, Estado, IdAsistente)
                    VALUES(?, date(NOW()), time(NOW()), 0, ?, 'RE', ?, ?, 'I', ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iiiii', $idCajaDiaria, $_SESSION['user_id'], $numeroRecibo, $idColegiado, $idAsistente);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $idCajaDiariaMovimiento = mysqli_stmt_insert_id($stmt);
	            $totalDeuda = 0;
                if (isset($generarRecibo)) {
                    foreach ($generarRecibo as $row) {
                    	//print($row);
                    	//echo '<br>';
    					switch ($tipoRecibo) {
    						case 'ESPECIALISTAS':
                                $recargo = 0;
    							$idMesaEntrada = $row;
    							$indice = $idMesaEntrada;
    				            $resMesa = obtenerMesaEntradaPorId($idMesaEntrada);
    				            if ($resMesa['estado']) {
    				            	$mesa = $resMesa['datos'];
    				            	$monto = $mesa['importe'];
    				            	$codigoPago = $mesa['idTipoPago'];
    			                	$periodo = NULL;
    			                	$cuota = NULL;
    				            } else {
    				            	$monto = 0;
    				            	$codigoPago = NULL;
    				            }
    							break;
    						
                            case 'CUOTAS':
                                echo $row; 
                                $id = explode('_', $row);
                                $monto = $id[1];
                                if (isset($id[2]) && $id[2] == 'PT') {
                                    //es un pago total
                                    $indice = intval(substr($id[0], 1, 7));
                                    $periodo = $_SESSION['periodoActual'];
                                    $cuota = 0;
                                    $importeOriginal = $monto;
                                    $codigoPago = 1;
                                    $recargo = 0;
                                    $sql = "UPDATE colegiadodeudaanualtotal cdat
                                            INNER JOIN colegiadodeudaanual cda ON cda.Id = cdat.IdColegiadoDeudaAnual
                                            INNER JOIN colegiadodeudaanualcuotas cdac ON cdac.IdColegiadoDeudaAnual = cda.Id
                                            SET cdat.IdEstado = 2, 
                                                cdat.FechaPago = date(NOW()), 
                                                cdat.FechaActualizacion = date(NOW()),
                                                cdac.Estado = 8,
                                                cdac.FechaPago = date(NOW()),
                                                cdac.FechaActualizacion = date(NOW())
                                            WHERE cdat.Id = ?";
                                    $stmt = $conect->prepare($sql);
                                    $stmt->bind_param('i', $indice);
                                    $stmt->execute();
                                    if(mysqli_stmt_errno($stmt) != 0) {
                                        $continua = FALSE;
                                    }
                                } else {
                                    $indice = $id[0];
                                    $resCuota = obtenerColegiadoDeudaAnualCuotaPorId($indice);
                                    if ($resCuota['estado']) {
                                        $periodo = $resCuota['datos']['periodo'];
                                        $cuota = $resCuota['datos']['cuota'];
                                        $importeOriginal = $resCuota['datos']['importe'];
                                        if ($periodo == $_SESSION['periodoActual']) {
                                            $codigoPago = 1;
                                        } else {
                                            $codigoPago = 3;
                                        }
                                        if ($conRecargo == 'SI' && $monto > $importeOriginal) {
                                            $recargo = $monto - $importeOriginal;
                                        } else {
                                            $recargo = 0;
                                        }

                                        //imputa el pago en la deuda
                                         if ($cuota > 0) {
                                            $sql = "UPDATE colegiadodeudaanualcuotas cdac
                                                    SET cdac.FechaPago = date(NOW()), 
                                                        cdac.Estado = 2, 
                                                        cdac.FechaActualizacion = date(NOW())
                                                    WHERE cdac.Id = ?";
                                        } else {
                                            $sql = "UPDATE colegiadodeudaanualtotal cdat
                                                    SET cdat.IdEstado = 2, 
                                                        cdat.FechaPago = date(NOW()), 
                                                        cdat.FechaActualizacion = date(NOW())
                                                    WHERE cdat.Id = ?";
                                        }
                                        $stmt = $conect->prepare($sql);
                                        $stmt->bind_param('i', $indice);
                                        $stmt->execute();
                                        if(mysqli_stmt_errno($stmt) != 0) {
                                            $continua = FALSE;
                                        }
                                    } else {
                                        $monto = 0;
                                        $codigoPago = NULL;
                                    }
                                }
                                break;
                            
                            case 'CURSOS':
                                $id = explode('_', $row);
                                $indice = $id[0];
                                $monto = $id[1];
                                $resCuota = obtenerCuotaCursoPorId($indice);
                                if ($resCuota['estado']) {
                                    $cuota = $resCuota['datos']['cuota'];
                                    $importe = $resCuota['datos']['importe'];
                                    $codigoPago = 10;
                                    $recargo = 0;

                                    //imputa el pago en la deuda
                                    $sql = "UPDATE cursosasistentecuotas cac
                                            SET cac.FechaPago = date(NOW()), 
                                                cac.Recibo = ?, 
                                                cac.FechaActualizacion = date(NOW())
                                            WHERE cac.Id = ?";
                                    $stmt = $conect->prepare($sql);
                                    $stmt->bind_param('ii', $numeroRecibo, $indice);
                                    $stmt->execute();
                                    if(mysqli_stmt_errno($stmt) != 0) {
                                        $continua = FALSE;
                                    }
                                } else {
                                    $monto = 0;
                                    $codigoPago = NULL;
                                }
                                break;
                            
                            case 'TIPO_PAGO':
                                $id = explode('_', $row);
                                $codigoPago = $id[0];
                                $monto = $id[1];
                                $indice = NULL;
                                $periodo = NULL;
                                $cuota = NULL;
                                $recargo = NULL;
                                break;
                            
                            case 'DEVOLUCION':
                                $id = explode('_', $row);
                                $codigoPago = $id[0];
                                $monto = $id[1];
                                $indice = NULL;
                                $periodo = NULL;
                                $cuota = NULL;
                                $recargo = NULL;
                                break;

    						default:
    							// code...
    							break;
    					}
    					$totalDeuda += $monto;
                    	if (isset($codigoPago)) {
    				        $sql = "INSERT INTO cajadiariamovimientodetalle (IdCajaDiariaMovimiento, CodigoPago, Indice, Monto, Periodo, Cuota, Recargo)
    			                    VALUES(?, ?, ?, ?, ?, ?, ?)";
    			            $stmt = $conect->prepare($sql);
    			            $stmt->bind_param('iiisiis', $idCajaDiariaMovimiento, $codigoPago, $indice, $monto, $periodo, $cuota, $recargo);
    			            $stmt->execute();
    			            $stmt->store_result();
    			            if(mysqli_stmt_errno($stmt) != 0) {
    			                $resultado['estado'] = FALSE;
    			                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientodetalle";
    			                $resultado['clase'] = 'alert alert-danger'; 
    			                $resultado['icono'] = 'glyphicon glyphicon-remove';            	
    							break;		            	
    			            } else {
    			                $resultado['estado'] = TRUE;			            	
    			            }
    		        	} else {
    		                $resultado['estado'] = FALSE;
    		                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimiento";
    		                $resultado['clase'] = 'alert alert-danger'; 
    		                $resultado['icono'] = 'glyphicon glyphicon-remove';            	
    						break;		            	
    		        	}
                    }
                }
                if (isset($generarReciboPP)) {
                    foreach ($generarReciboPP as $row) {
                        //print($row);
                        //echo '<br>';
                        switch ($tipoRecibo) {
                            case 'CUOTAS':
                                $idPlaPagoCuota = $row;
                                $indice = $idPlaPagoCuota;
                                $resMesa = obtenerPlanPagoCuotaPorId($idPlaPagoCuota);
                                if ($resMesa['estado']) {
                                    $mesa = $resMesa['datos'];
                                    $monto = $mesa['importeActualizado'];
                                    $codigoPago = 2; //Plan de pagos
                                    $periodo = NULL;
                                    $cuota = $mesa['cuota'];
                                    $sql = "UPDATE planpagoscuotas ppc
                                            SET ppc.FechaPago = date(NOW()), 
                                                ppc.IdTipoEstadoCuota = 2, 
                                                ppc.FechaActualizacion = date(NOW())
                                            WHERE ppc.Id = ?";
                                    $stmt = $conect->prepare($sql);
                                    $stmt->bind_param('i', $idPlaPagoCuota);
                                    $stmt->execute();
                                    if(mysqli_stmt_errno($stmt) != 0) {
                                        $continua = FALSE;
                                    }
                                } else {
                                    $monto = 0;
                                    $codigoPago = NULL;
                                }
                                break;
                            
                            default:
                                // code...
                                break;
                        }
                        $totalDeuda += $monto;
                        if (isset($codigoPago)) {
                            $sql = "INSERT INTO cajadiariamovimientodetalle (IdCajaDiariaMovimiento, CodigoPago, Indice, Monto, Periodo, Cuota)
                                    VALUES(?, ?, ?, ?, ?, ?)";
                            $stmt = $conect->prepare($sql);
                            $stmt->bind_param('iiisii', $idCajaDiariaMovimiento, $codigoPago, $indice, $monto, $periodo, $cuota);
                            $stmt->execute();
                            $stmt->store_result();
                            if(mysqli_stmt_errno($stmt) != 0) {
                                $resultado['estado'] = FALSE;
                                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientodetalle";
                                $resultado['clase'] = 'alert alert-danger'; 
                                $resultado['icono'] = 'glyphicon glyphicon-remove';             
                                break;                      
                            } else {
                                $resultado['estado'] = TRUE;                            
                            }
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimiento";
                            $resultado['clase'] = 'alert alert-danger'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';             
                            break;                      
                        }
                    }
                }

                //carga la forma de pago
                $sql = "INSERT INTO cajadiariamovimientopago (IdCajaDiariaMovimiento, IdFormaPago, IdBanco, Monto, Detalle)
                                VALUES(?, ?, ?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iiiss', $idCajaDiariaMovimiento, $idFormaPago, $idBanco, $totalDeuda, $comprobante);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    $resultado['estado'] = TRUE;
                    $resultado['mensaje'] = "OK";
                    $resultado['clase'] = 'alert alert-success'; 
                    $resultado['icono'] = 'glyphicon glyphicon-ok';             
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientopago ".mysqli_stmt_error($stmt);
                    $resultado['clase'] = 'alert alert-danger'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';             
                }

            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiaria ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-danger'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';            	
            }

            if ($resultado['estado']) {
            	if ($totalDeuda > 0) {
            		//actualiza el recibo
            		$sql = "UPDATE cajadiariamovimiento 
            				SET Monto = ? 
            				WHERE Id = ?";
	            	$stmt = $conect->prepare($sql);
	            	$stmt->bind_param('si', $totalDeuda, $idCajaDiariaMovimiento);
	            	$stmt->execute();

	            	//marca como pagado en mesaentrada
	            	foreach ($generarRecibo as $row) {
						switch ($tipoRecibo) {
							case 'ESPECIALISTAS':
								$idMesaEntrada = $row;
			            		$sql = "UPDATE mesaentrada 
			            				SET Pagado = 1 
			            				WHERE IdMesaEntrada = ?";
				            	$stmt = $conect->prepare($sql);
				            	$stmt->bind_param('i', $idMesaEntrada);
				            	$stmt->execute();
			            		break;
			            }
			        }
            	}
	            $resultado['idCajaDiariaMovimiento'] = $idCajaDiariaMovimiento;
	            $conect->commit();
	            desconectar($conect);
	            return $resultado;
	        } else {
	            $conect->rollback();
	            desconectar($conect);
	            $resultado['mensaje'] .= ' (Generar del Recibo por Cajas Diarias, manualmente)';
	            return $resultado;
	        }
	    } catch (mysqli_sql_exception $e) {
	        $conect->rollback();
	        desconectar($conect);
	        return $resultado;
	    }   
	}
}

function generarDevolucionCajaDiaria($idColegiado, $tipoPago, $idFormaPago, $importe) {
    $continua = TRUE;
    $resCajaDiaria = obtenerCajaAbierta();
    if ($resCajaDiaria['estado']) {
        $idCajaDiaria = $resCajaDiaria['datos']['idCajaDiaria'];
    } else {
        $continua = FALSE;
        $mensaje .= 'No hay caja del día abierta, debe ir a generar una caja. ';
    }

    $tipoComprobante = 'NC';
    $resRecibo = ObtenerNumeroComprobante($tipoComprobante);
    if ($resRecibo['numeroRecibo']) {
        $numeroRecibo = $resRecibo['numeroRecibo'];
    } else {
        $continua = FALSE;
        $mensaje .= 'No se pudo obtener el numeroRecibo por tipoComprobante. ';     
    }

    if ($continua) {
        /*
        print_r($generarRecibo);
        echo '<br>';
        echo 'idCajaDiaria->'.$idCajaDiaria.' numeroRecibo->'.$numeroRecibo.'<br>';
        exit;
        */
        try {
            /* Autocommit false para la transaccion */
            $conect = conectar();
            mysqli_set_charset( $conect, 'utf8');
            $conect->autocommit(FALSE);
            $resultado = array();

            //obtengo el detalle para generar el recibo
            //obtengo la deuda, inicializo los campos a mostrar

            $sql = "INSERT INTO cajadiariamovimiento (IdCajaDiaria, Fecha, Hora, Monto, IdUsuario, Tipo, Numero, IdColegiado, Estado, IdAsistente)
                    VALUES(?, date(NOW()), time(NOW()), ?, ?, ?, ?, ?, 'I', NULL)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('isisii', $idCajaDiaria, $importe, $_SESSION['user_id'], $tipoComprobante, $numeroRecibo, $idColegiado);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $idCajaDiariaMovimiento = mysqli_stmt_insert_id($stmt);
                $sql = "INSERT INTO cajadiariamovimientodetalle (IdCajaDiariaMovimiento, CodigoPago, Monto)
                        VALUES(?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iis', $idCajaDiariaMovimiento, $tipoPago, $importe);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientodetalle";
                    $resultado['clase'] = 'alert alert-danger'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';             
                } else {
                    //carga la forma de pago
                    $sql = "INSERT INTO cajadiariamovimientopago (IdCajaDiariaMovimiento, IdFormaPago, Monto)
                                    VALUES(?, ?, ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('iis', $idCajaDiariaMovimiento, $idFormaPago, $importe);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $resultado['estado'] = TRUE;
                        $resultado['mensaje'] = "OK";
                        $resultado['clase'] = 'alert alert-success'; 
                        $resultado['icono'] = 'glyphicon glyphicon-ok';             
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL AGREGAR cajadiariamovimientopago ".mysqli_stmt_error($stmt);
                        $resultado['clase'] = 'alert alert-danger'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';             
                    }
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR cajadiaria ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-danger'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';             
            }

            if ($resultado['estado']) {
                $resultado['idCajaDiariaMovimiento'] = $idCajaDiariaMovimiento;
                $conect->commit();
                desconectar($conect);
                return $resultado;
            } else {
                $conect->rollback();
                desconectar($conect);
                $resultado['mensaje'] .= ' (Generar del Recibo por Cajas Diarias, manualmente)';
                return $resultado;
            }
        } catch (mysqli_sql_exception $e) {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }   
    }
}

function anularReciboCajaDiaria($idCajaDiariaMovimiento) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        //obtengo el detalle para generar el recibo
        //obtengo la deuda, inicializo los campos a mostrar

        $sql = "SELECT Id, CodigoPago, Indice, Periodo, Cuota
                FROM cajadiariamovimientodetalle
                WHERE IdCajaDiariaMovimiento = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idCajaDiariaMovimiento);
        $stmt->execute();
        $stmt->bind_result($idCajaDiariaMovimientoDetalle, $codigoPago, $indice, $periodo, $cuota);
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $continua = TRUE;
            while (mysqli_stmt_fetch($stmt) && $continua) {
                switch ($codigoPago) {
                    case '72':
                    case '38':
                    case '59':
                    case '37':
                    case '82':
                    case '52':
                    case '55':
                    case '61':
                        //son codigo de pago de especialistas
                        $sql1 = "UPDATE mesaentrada 
                                SET Pagado = 0 
                                WHERE IdMesaEntrada = ?";
                        break;

                    case '1':
                    case '3':
                        //son codigo de pago de cuotas de colegiacion, 1: periodo actual - 3: periodos anteriores
                        if ($cuota > 0) {
                            $sql1 = "UPDATE colegiadodeudaanualcuotas cdac
                                    SET cdac.FechaPago = '0000-00-00', 
                                        cdac.Estado = 1, 
                                        cdac.FechaActualizacion = NULL
                                    WHERE cdac.Id = ?";
                        } else {
                            $sql1 = "UPDATE colegiadodeudaanualtotal cdat
                                    SET cdat.IdEstado = 1, 
                                        cdat.FechaPago = NULL, 
                                        cdat.FechaActualizacion = NULL
                                    WHERE cdat.Id = ?";

                        }
                        break;

                    case '2':
                        //codigo de pago de cuotas de plan de pagos
                        $sql1 = "UPDATE planpagoscuotas ppc
                                SET ppc.FechaPago = NULL, 
                                    ppc.Estado = '', 
                                    ppc.IdTipoEstadoCuota = 1, 
                                    ppc.FechaActualizacion = NULL
                                WHERE ppc.Id = ?";
                        break;

                    case '10':
                        //codigo de pago de cuotas de cursos
                        $sql1 = "UPDATE cursosasistentecuotas cac
                                SET cac.FechaPago = NULL, 
                                    cac.Recibo = 0, 
                                    cac.FechaActualizacion = NULL
                                WHERE cac.Id = ?";
                        break;

                    case '62':
                        //son codigo de pago de constancia de firma, le paso el idcajadiariamovimiento
                        $indice = $idCajaDiariaMovimiento;
                        $sql1 = "UPDATE constanciafirma 
                                SET IdCajaDiariaMovimiento = NULL 
                                WHERE IdCajaDiariaMovimiento = ?";
                        break;

                    default:
                        //es un codigo de pago que no tiene deuda generada
                        $sql1 = NULL;
                        break;
                }
                if (isset($sql1)) {
                    $stmt1 = $conect->prepare($sql1);
                    $stmt1->bind_param('i', $indice);
                    $stmt1->execute();
                    if(mysqli_stmt_errno($stmt1) != 0) {
                        $continua = FALSE;
                    }
                }
            }
            if ($continua) {
                $sql = "UPDATE cajadiariamovimiento 
                        SET Estado = 'A'
                        WHERE Id = ?";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('i', $idCajaDiariaMovimiento);
                $stmt->execute();
                $resultado['estado'] = TRUE;                                            
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL ANULAR RECIBO";
                $resultado['clase'] = 'alert alert-danger'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';                             
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR ANULAR RECIBO ".mysqli_stmt_error($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';             
        }

        if ($resultado['estado']) {
            $resultado['mensaje'] = "OK - RECIBO ANULADO";
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
        return $resultado;
    }   
}

function ObtenerNumeroComprobante($tipoComprobante) {
	$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
	$sql = "SELECT max(Numero) FROM cajadiariamovimiento WHEre Tipo = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $tipoComprobante);
    $stmt->execute();
    $stmt->bind_result($numeroRecibo);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        //if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);
        if (isset($numeroRecibo) && $numeroRecibo > 0) {
			$resultado['numeroRecibo'] = $numeroRecibo + 1;
        } else {
            $resultado['numeroRecibo'] = NULL;
        }
    } else {
        $resultado['numeroRecibo'] = NULL;
    }
    return $resultado;
} 

function obtenerCajaDiariaMovimientos($idCajaDiaria) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdm.Id, cdm.Fecha, cdm.Hora, cdm.Monto, cdm.Tipo, cdm.Numero, cdm.IdAsistente, cdm.IdColegiado, u.Usuario, cdm.Estado, 
		CASE 
		    WHEN cdm.IdAsistente IS NOT NULL THEN if (ca.IdColegiado IS NOT NULL, CONCAT(p1.Apellido, ' ', p1.Nombres), ca.ApellidoNombre)
		    WHEN cdm.IdColegiado IS NOT NULL THEN CONCAT(p.Apellido, ' ', p.Nombres)
		    WHEN (cdm.IdAsistente IS NULL AND cdm.IdColegiado IS NULL) THEN cdmo.Descripcion
		END AS ApellidoNombre,
		CASE 
		    WHEN cdm.IdAsistente IS NOT NULL THEN if (ca.IdColegiado IS NOT NULL, c1.Matricula, NULL)
		    WHEN cdm.IdColegiado IS NOT NULL THEN c.Matricula
		END AS Matricula,
        fp.Detalle AS FormaDePago,
        b.Nombre AS NombreBanco,
        cmp.Detalle AS Comprobante

		FROM cajadiariamovimiento cdm
		LEFT JOIN colegiado c ON c.Id = cdm.IdColegiado
		LEFT JOIN persona p ON p.Id = c.IdPersona
		LEFT JOIN cursosasistente ca ON ca.Id = cdm.IdAsistente
		LEFT JOIN cursos cur ON cur.Id = ca.IdCursos
		LEFT JOIN colegiado c1 ON c1.Id = ca.IdColegiado
		LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
		LEFT JOIN cajadiariamovimientootro cdmo ON cdmo.IdCajaDiariaMovimiento = cdm.Id
		LEFT JOIN usuario u ON u.Id = cdm.IdUsuario
        LEFT JOIN cajadiariamovimientopago cmp ON cmp.IdCajaDiariaMovimiento = cdm.Id
        LEFT JOIN formapago fp ON fp.Id = cmp.IdFormaPago
        LEFT JOIN banco b ON b.Id = cmp.IdBanco
		WHERE cdm.IdCajaDiaria = ?
        ORDER BY cdm.Id";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiaria);
    $stmt->execute();
    $stmt->bind_result($idCajaDiariaMovimiento, $fechaPago, $horaPago, $monto, $tipo, $numero, $idAsistente, $idColegiado, $usuario, $estado, $apellidoNombre, $matricula, $formaDePago, $nombreBanco, $comprobante);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idCajaDiariaMovimiento' => $idCajaDiariaMovimiento,
                    'fechaPago' => $fechaPago,
                    'horaPago' => $horaPago,
                    'monto' => $monto,
                    'tipo' => $tipo,
                    'numero' => $numero,
                    'idAsistente' => $idAsistente,
                    'idColegiado' => $idColegiado,
                    'usuario' => $usuario,
                    'apellidoNombre' => $apellidoNombre,
                    'matricula' => $matricula,
                    'estado' => $estado,
                    'formaDePago' => $formaDePago,
                    'nombreBanco' => $nombreBanco,
                    'comprobante' => $comprobante
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
            $resultado['mensaje'] = "No existen movimientos en la caja diaria";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando movimientos en la caja diaria";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}

function obtenerCajaDiariaResumenCuenta($idCajaDiaria) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT tp.Detalle AS Concepto, cdmd.CodigoPago, tp.CuentaContable, SUM(cdmd.Monto) AS TotalConcepto
        FROM cajadiariamovimientodetalle cdmd
        INNER JOIN cajadiariamovimiento cdm ON cdm.Id = cdmd.IdCajaDiariaMovimiento
        INNER JOIN tipopago tp ON tp.Id = cdmd.CodigoPago
        WHERE cdm.IdCajaDiaria = ?
        AND cdm.Estado <> 'A'
        GROUP BY tp.Detalle, cdmd.CodigoPago, tp.CuentaContable";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiaria);
    $stmt->execute();
    $stmt->bind_result($concepto, $codigoPago, $cuentaContable, $totalConcepto);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'concepto' => $concepto,
                    'codigoPago' => $codigoPago,
                    'cuentaContable' => $cuentaContable,
                    'totalConcepto' => $totalConcepto
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
            $resultado['mensaje'] = "No existen movimientos en la caja diaria";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando movimientos en la caja diaria";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}

function obtenerTotalRecaudacion($idCajaDiaria) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT SUM(cdm.Monto), COUNT(cdm.Id) FROM cajadiariamovimiento cdm WHERE cdm.IdCajaDiaria = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiaria);
    $stmt->execute();
    $stmt->bind_result($totalRecaudacion, $cantidadComprobantes);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0 ) {
	        $row = mysqli_stmt_fetch($stmt);
	        if (!isset($totalRecaudacion)) {
	        	$totalRecaudacion = 0.00;
	        }
	        $datos = array('totalRecaudacion' => $totalRecaudacion, 'cantidadComprobantes' => $cantidadComprobantes);
        } else {
	        $datos = array('totalRecaudacion' => 0, 'cantidadComprobantes' => 0);
        }
    } else {
        $datos = NULL;
    }
    
    return $datos;
}

function obtenerCajaDiariaMovimientoPorId($idCajaDiariaMovimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdm.IdCajaDiaria, cdm.Fecha, cdm.Hora, cdm.Monto, cdm.Tipo, cdm.Numero, cdm.IdAsistente, cdm.IdColegiado, u.Usuario, cdm.Estado, 
        CASE 
            WHEN cdm.IdAsistente IS NOT NULL THEN if (ca.IdColegiado IS NOT NULL, CONCAT(p1.Apellido, ' ', p1.Nombres), ca.ApellidoNombre)
            WHEN cdm.IdColegiado IS NOT NULL THEN CONCAT(p.Apellido, ' ', p.Nombres)
            WHEN (cdm.IdAsistente IS NULL AND cdm.IdColegiado IS NULL) THEN cdmo.Descripcion
        END AS ApellidoNombre,
        CASE 
            WHEN cdm.IdColegiado IS NOT NULL THEN (SELECT CONCAT(cdr.Calle, ' - ', cdr.Numero, ' (', l.Nombre,')') 
                                                    FROM colegiadodomicilioreal cdr 
                                                    LEFT JOIN localidad l ON l.Id = cdr.idLocalidad 
                                                    WHERE cdr.idColegiado = c.Id AND cdr.idEstado = 1)
            WHEN (cdm.IdAsistente IS NULL AND cdm.IdColegiado IS NULL) THEN cdmo.Domicilio
        END AS Domicilio,
        
        CASE 
            WHEN cdm.IdAsistente IS NOT NULL THEN if (ca.IdColegiado IS NOT NULL, c1.Matricula, NULL)
            WHEN cdm.IdColegiado IS NOT NULL THEN c.Matricula
        END AS Matricula,
        
        cdmo.CUIT

        FROM cajadiariamovimiento cdm
        LEFT JOIN colegiado c ON c.Id = cdm.IdColegiado
        LEFT JOIN persona p ON p.Id = c.IdPersona
        LEFT JOIN cursosasistente ca ON ca.Id = cdm.IdAsistente
        LEFT JOIN cursos cur ON cur.Id = ca.IdCursos
        LEFT JOIN colegiado c1 ON c1.Id = ca.IdColegiado
        LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
        LEFT JOIN cajadiariamovimientootro cdmo ON cdmo.IdCajaDiariaMovimiento = cdm.Id
        LEFT JOIN usuario u ON u.Id = cdm.IdUsuario
        WHERE cdm.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiariaMovimiento);
    $stmt->execute();
    $stmt->bind_result($idCajaDiaria, $fechaPago, $horaPago, $monto, $tipo, $numero, $idAsistente, $idColegiado, $usuario, $estado, $apellidoNombre, $domicilio, $matricula, $cuit);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array (
            'idCajaDiaria' => $idCajaDiaria,
            'fechaPago' => $fechaPago,
            'horaPago' => $horaPago,
            'monto' => $monto,
            'tipoRecibo' => $tipo,
            'numeroRecibo' => $numero,
            'idAsistente' => $idAsistente,
            'idColegiado' => $idColegiado,
            'usuario' => $usuario,
            'estadoRecibo' => $estado,
            'apellidoNombre' => $apellidoNombre,
            'matricula' => $matricula,
            'domicilio' => $domicilio,
            'cuit' => $cuit
         );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Movimiento";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerCajaDiariaMovimientoDetallePorId($idCajaDiariaMovimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cdmd.*, tp.Detalle,
                (CASE
                    WHEN cdmd.CodigoPago IN('72', '38', '59', '37', '82', '52', '55', '61') THEN (SELECT e.Especialidad FROM mesaentradaespecialidad mee INNER JOIN mesaentrada me ON me.IdMesaEntrada = mee.IdMesaEntrada INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad) INNER JOIN tipoespecialista tes ON(tes.IdTipoEspecialista = mee.IdTipoEspecialista) WHERE mee.IdMesaEntrada = cdmd.Indice)
                    WHEN cdmd.CodigoPago = '10' THEN (SELECT CONCAT(cur.Titulo, ' - ', cac.DetalleCuota) FROM cursos cur INNER JOIN cursosasistente ca ON ca.IdCursos = cur.Id INNER JOIN cursosasistentecuotas cac ON cac.IdCursosAsistente = ca.Id WHERE cac.Id = cdmd.Indice)
                    ELSE ''
                END) AS Detalle
            FROM cajadiariamovimientodetalle cdmd
            INNER JOIN tipopago tp ON tp.Id = cdmd.CodigoPago
            WHERE cdmd.IdCajaDiariaMovimiento = ?";
    //echo $sql.' -> '.$idCajaDiariaMovimiento;
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCajaDiariaMovimiento);
    $stmt->execute();
    $stmt->bind_result($idCajaDiariaMovimientoDetalle, $idCajaDiariaMovimiento, $codigoPago, $indice, $monto, $periodo, $cuota, $condonacion, $recargo, $concepto, $tipoPago, $detalle);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idCajaDiariaMovimientoDetalle' => $idCajaDiariaMovimientoDetalle,
                    'codigoPago' => $codigoPago,
                    'indice' => $indice,
                    'monto' => $monto,
                    'periodo' => $periodo,
                    'cuota' => $cuota,
                    'condonacion' => $condonacion,
                    'recargo' => $recargo,
                    'tipoPago' => $tipoPago,
                    'detalle' => $detalle.$concepto
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
            $resultado['mensaje'] = "No existe detalle para el recibo seleccionado";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando detalle para el recibo seleccionado";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}
