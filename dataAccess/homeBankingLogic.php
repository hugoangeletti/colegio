<?php
function obtenerHomaBankingPorId($idHomeBankingArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT a.Id, a.FechaProceso, a.PeriodoProceso, a.FechaPrimerVto, a.ImportePrimerVto, a.Codigo, a.Control, a.Refresh, a.PagoMisCuentas, a.PathArchivos
        FROM home_banking_archivo a
        WHERE a.Id = ?
        AND a.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idHomeBankingArchivo);
    $stmt->execute();
    $stmt->bind_result($idHomeBankingArchivo, $fechaProceso, $periodoProceso, $fechaPrimerVencimiento, $importe, $codigo, $control, $refresh, $pagoMisCuentas, $pathArchivo);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idHomeBankingArchivo' => $idHomeBankingArchivo,
                    'fechaProceso' => $fechaProceso,
                    'periodoProceso' => $periodoProceso,
                    'fechaPrimerVencimiento' => $fechaPrimerVencimiento,
                    'importe' => $importe,
                    'codigo' => $codigo,
                    'control' => $control,
                    'refresh' => $refresh,
                    'pagoMisCuentas' => $pagoMisCuentas,
                    'pathArchivo' => $pathArchivo
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontro envio home banking";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando envio home banking";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerHomaBankingGenerados($anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT a.Id, a.FechaProceso, a.PeriodoProceso, a.FechaPrimerVto, a.ImportePrimerVto, a.Codigo, a.Control, a.Refresh, a.PagoMisCuentas, a.PathArchivos
        FROM home_banking_archivo a
        WHERE SUBSTR(a.PeriodoProceso, 1, 4) = ?
        AND a.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $anio);
    $stmt->execute();
    $stmt->bind_result($idHomeBankingArchivo, $fechaProceso, $periodoProceso, $fechaPrimerVencimiento, $importe, $codigo, $control, $refresh, $pagoMisCuentas, $pathArchivo);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idHomeBankingArchivo' => $idHomeBankingArchivo,
                    'fechaProceso' => $fechaProceso,
                    'periodoProceso' => $periodoProceso,
                    'fechaPrimerVencimiento' => $fechaPrimerVencimiento,
                    'importe' => $importe,
                    'codigo' => $codigo,
                    'control' => $control,
                    'refresh' => $refresh,
                    'pagoMisCuentas' => $pagoMisCuentas,
                    'pathArchivo' => $pathArchivo
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
            $resultado['mensaje'] = "No se encontro envio home banking";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando envio home banking";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function agregarHomeBankingArchivo($fechaPrimerVencimiento, $fechaSegundoVencimiento, $codigoLiquidacion, $control, $refresh, $pagoMisCuentas, $path) {
    try {
        /* Autocommit false para la transaccion */
        if (!isset($conect)) {
            $conect = conectar();
        }
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        //se agrega linea en link
        $periodoProceso = date('Y').date('m');
        $sql="INSERT INTO home_banking_archivo
                (FechaProceso, PeriodoProceso, IdUsuario, FechaPrimerVto, ImportePrimerVto, FechaSegundoVto, ImporteSegundoVto, Codigo, Control, Refresh, PagoMisCuentas, PathArchivos) 
                VALUE (NOW(), ?, ?, ?, 0, ?, 0, ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisssssss', $periodoProceso, $_SESSION['user_id'], $fechaPrimerVencimiento, $fechaSegundoVencimiento, $codigoLiquidacion, $control, $refresh, $pagoMisCuentas, $path);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $idHomeBankingArchivo = mysqli_stmt_insert_id($stmt);
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
            $resultado['idHomeBankingArchivo'] = $idHomeBankingArchivo;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL INSERTAR home_banking_archivo -> ".mysqli_stmt_error($stmt1);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }                    
        
        if ($resultado['estado']) {
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL INSERTAR home_banking_archivo -> ".mysqli_stmt_error($stmt1);
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL INSERTAR home_banking_archivo ".mysqli_stmt_error($stmt);
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function agregarEnvioHomeBanking($idHomeBankingArchivo, $concepto, $idColegiado, $idAsistente, $fechaPrimerVencimiento, $importe, $fechaSegundoVencimiento, $mensajeTicket, $mensajePantalla, $codigobarra, $arrayCuotas){
    try {
        /* Autocommit false para la transaccion */
        if (!isset($conect)) {
            $conect = conectar();
        }
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        //se agrega linea en link
        $sql="INSERT INTO home_banking_archivo_concepto
                (IdHomeBankingArchivo, Concepto, IdColegiado, IdAsistente, FechaPrimerVto, ImportePrimerVto, FechaSegundoVto, ImporteSegundoVto, MensajeTicket, MensajePantalla, CodigoBarras) 
                VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isiisssssss', $idHomeBankingArchivo, $concepto, $idColegiado, $idAsistente, $fechaPrimerVencimiento, $importe, $fechaSegundoVencimiento, $importe, $mensajeTicket, $mensajePantalla, $codigobarra);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $idHomeBankingArchivoConcepto = mysqli_stmt_insert_id($stmt);
            foreach ($arrayCuotas as $cuotaDeuda) {
	        	//ahora agregamos las cuotas que se incluyen en la deuda
	        	$idDeuda = $cuotaDeuda['idDeuda'];
	        	$importePrimerVto = $cuotaDeuda['recargo'];
                $importeSegundoVto = $cuotaDeuda['recargo'];
            	$sql="INSERT INTO home_banking_archivo_concepto_detalle (IdHomeBankingArchivoConcepto, IdDeuda, ImportePrimerVto, ImporteSegundoVto) 
                    VALUES (?, ?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iiss', $idHomeBankingArchivoConcepto, $idDeuda, $importePrimerVto, $importeSegundoVto);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    $resultado['estado'] = TRUE;
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL INSERTAR CUOTAS EN linkpagosdetalle -> ".mysqli_stmt_error($stmt1);
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                    exit;
                }                    
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL INSERTAR CUOTAS EN linkpagos -> ".mysqli_stmt_error($stmt);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }                    
        
        if ($resultado['estado']) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'SE GENERO EL CONCEPTO';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL INSERTAR CUOTAS EN linkpagos -> ".mysqli_stmt_error($stmt);
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL INSERTAR CUOTAS EN linkpagos.";
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function obtenerHomeBankingConceptoPorIdArchivo($idHomeBankingArchivo) {
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT a.Id, a.Concepto, c.Matricula, a.IdAsistente, a.FechaSegundoVto, a.ImporteSegundoVto, a.MensajeTicket, a.MensajePantalla
        FROM home_banking_archivo_concepto a
        LEFT JOIN colegiado c ON c.Id = a.IdColegiado
        WHERE a.IdHomeBankingArchivo = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idHomeBankingArchivo);
    $stmt->execute();
    $stmt->bind_result($idHomeBankingArchivoConcepto, $concepto, $matricula, $idAsistente, $fechaVencimiento, $importe, $mensajeTicket, $mensajePantalla);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idHomeBankingArchivoConcepto' => $idHomeBankingArchivoConcepto,
                    'concepto' => $concepto,
                    'matricula' => $matricula,
                    'idAsistente' => $idAsistente,
                    'fechaVencimiento' => $fechaVencimiento,
                    'importe' => $importe,
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
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontro envio home banking";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando envio home banking";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function actualizarHomeBankingArchivos($idHomeBankingArchivo, $total, $control, $refresh, $pagoMisCuentas, $path) {
    try {
        /* Autocommit false para la transaccion */
        if (!isset($conect)) {
            $conect = conectar();
        }
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        //se agrega linea en link
        $periodoProceso = date('Y').date('m');
        $sql="UPDATE home_banking_archivo
                SET ImportePrimerVto = ?, ImporteSegundoVto = ?, Control = ?, Refresh = ?, PagoMisCuentas = ?, PathArchivos = ?
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssssssi', $total, $total, $control, $refresh, $pagoMisCuentas, $path, $idHomeBankingArchivo);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL ACTUALIZAR home_banking_archivo -> ".mysqli_stmt_error($stmt1);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }                    
        
        if ($resultado['estado']) {
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL ACTUALIZAR home_banking_archivo -> ".mysqli_stmt_error($stmt1);
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR home_banking_archivo ".mysqli_stmt_error($stmt);
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }

}