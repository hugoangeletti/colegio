<?php
function obtenerInformePorPeriodo($periodo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM informe_contable WHERE Periodo = ? ORDER BY MesProcesado";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodo);
    $stmt->execute();
    $stmt->bind_result($id, $periodo, $mes, $origen, $fechaProceso, $idUsuario, $borrado);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'periodo' => $periodo,
                'mes' => $mes,
                'origen' => $origen,
                'fechaProceso' => $fechaProceso,
                'idUsuario' => $idUsuario,
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
        $resultado['mensaje'] = "Error buscando Informe Contable";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerInformeContablePorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM informe_contable WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $periodo, $mes, $origen, $fechaProceso, $idUsuario, $borrado);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'id' => $id,
                'periodo' => $periodo,
                'mes' => $mes,
                'origen' => $origen,
                'fechaProceso' => $fechaProceso,
                'idUsuario' => $idUsuario,
                'borrado' => $borrado
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el informe_contable";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando informe_contable";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function generarInformeContable($periodo, $mesProcesado, $origen) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        $sql="SELECT Id FROM informe_contable
             WHERE Periodo = ? AND MesProcesado = ? AND Origen = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iss', $periodo, $mesProcesado, $origen);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $row = mysqli_stmt_fetch($stmt);
            if (isset($id)) {
                $idInforme = $id;
                $resultado['estado'] = TRUE;
            } else {
                $sql="INSERT INTO informe_contable
                    (Periodo, MesProcesado, Origen, FechaProceso, IdUsuario) 
                    VALUES (?, ?, ?, NOW(), ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('issi', $periodo, $mesProcesado, $origen, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    //agrego el movimiento para hacer el seguimiento
                    $idInforme = mysqli_stmt_insert_id($stmt);
                    $resultado['estado'] = TRUE;
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR informe_contable ".mysqli_stmt_error($stmt); 
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR informe_contable";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }

        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL informe_contable HA SIDO AGREGADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idInforme'] = $idInforme;
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

function agregarInformeDetalle($idInforme, $tipoComprobante, $numeroComprobante, $fechaPago, $cliente, $codigoC, $concepto, $detalle, $importe, $lineaBejerman) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        $sql="INSERT INTO informe_contable_detalle
            (idInformeContable, TipoComprobante, NumeroComprobante, FechaPago, Cliente, CodigoC, Concepto, Detalle, Importe, LineaBejerman) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isssssssss', $idInforme, $tipoComprobante, $numeroComprobante, $fechaPago, $cliente, $codigoC, $concepto, $detalle, $importe, $lineaBejerman);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego el movimiento para hacer el seguimiento
            $idDetalle = mysqli_stmt_insert_id($stmt);
            $resultado['estado'] = TRUE;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL AGREGAR informe_contable";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }

        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL informe_contable HA SIDO AGREGADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idDetalle'] = $idDetalle;
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

function procesarInformeTXT($archivo, $origen) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        $sql="INSERT INTO informe_contable
            (Periodo, MesProcesado, Origen, FechaProceso, IdUsuario) 
            VALUES (?, ?, ?, DATE(NOW()), ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisi', $periodo, $mesProcesado, $origen, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego el movimiento para hacer el seguimiento
            $idInforme = mysqli_stmt_insert_id($stmt);
            //procesar el archivo secuencial y cargar en informe_contable_detalle

            $resultado['estado'] = TRUE;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL AGREGAR informe_contable";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }

        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL informe_contable HA SIDO AGREGADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idInforme'] = $idInforme;
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