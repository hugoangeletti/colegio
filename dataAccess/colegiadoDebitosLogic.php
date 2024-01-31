<?php
function adheridoAlDebito($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Tipo
            FROM debitotarjeta 
            WHERE IdColegiado = ? AND Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($tipo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $resultado['tipo'] = $tipo;
            $resultado['estado'] = TRUE;
        } else {
            $resultado['estado'] = FALSE;
        }
    } else {
        $resultado['estado'] = FALSE;
    }

    if (!$resultado['estado']) {
        //busco en debito por cbu
        $sql="SELECT Id
                FROM debitocbu 
                WHERE IdColegiado = ? AND Estado = 'A'";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idColegiado);
        $stmt->execute();
        $stmt->bind_result($tipo);
        $stmt->store_result();

        $resultado = array();
        if(mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $resultado['tipo'] = 'H';
                $resultado['estado'] = TRUE;
            } else {
                $resultado['estado'] = FALSE;
            }
        } else {
            $resultado['estado'] = FALSE;
        }
    }

    return $resultado;
}

function obtenerDebitoPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT NumeroTarjeta, Tipo, NumeroDocumento, FechaCarga, IdBanco, IncluyePlanPagos, PagoTotal
            FROM debitotarjeta 
            WHERE IdColegiado = ? AND Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($numeroTarjeta, $tipo, $numeroDocumento, $fechaCarga, $idBanco, $incluyePP, $pagoTotal);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'numeroTarjeta' => $numeroTarjeta,
                    'tipo' => $tipo,
                    'numeroDocumento' => $numeroDocumento,
                    'fechaCarga' => $fechaCarga,
                    'idBanco' => $idBanco, 
                    'incluyePP' => $incluyePP,
                    'pagoTotal' => $pagoTotal
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerDebitoCBUPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, IdBanco, Tipo, CBUBloque1, CBUBloque2, FechaCarga, IncluyePlanPagos, PagoTotal
            FROM debitocbu 
            WHERE IdColegiado = ? AND Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idBanco, $tipo, $cbuBloque1, $cbuBloque2, $fechaCarga , $incluyePP, $pagoTotal);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'id' => $id,
                    'idBanco' => $idBanco,
                    'tipo' => $tipo,
                    'numeroCbu' => trim($cbuBloque1.$cbuBloque2),
                    'fechaCarga' => $fechaCarga,
                    'incluyePP' => $incluyePP,
                    'pagoTotal' => $pagoTotal
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarColegiadoDebito($idColegiado, $idBanco, $tipo, $numeroTarjeta, $numeroDocumento, $incluyePP, $incluyeTotal, $tipoAnterior, $numeroCbu, $tipoCuenta){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);
        
        //elimino en debitotarjeta y/o debitocbu si existen
        if ($tipoAnterior == 'H') {
            //elimino de debitocbu
            $sql="UPDATE debitocbu
                SET Estado = 'B' 
                WHERE IdColegiado = ? AND Estado = 'A'";
        } else {
            //elimino de debitotarjeta
            $sql="UPDATE debitotarjeta
                SET Estado = 'B' 
                WHERE IdColegiado = ? AND Estado = 'A'";
        }
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt)==0) {
            if ($tipo == 'C') {
                $sql="INSERT INTO debitotarjeta (IdColegiado, NumeroTarjeta, Tipo, NumeroDocumento, FechaCarga, 
                    IdBanco, IncluyePlanPagos, PagoTotal, IdUsuario)
                    VALUES (?, ?, ?, ?, date(now()), ?, ?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('issiissi', $idColegiado, $numeroTarjeta, $tipo, $numeroDocumento, $idBanco, $incluyePP, $incluyeTotal, $_SESSION['user_id']);
            } else {
                $bloque1 = substr($numeroCbu, 0, 8);
                $bloque2 = substr($numeroCbu, 8, 14);
                $sql="INSERT INTO debitocbu (IdColegiado, CBUBloque1, CBUBloque2, Tipo, FechaCarga, IdBanco, IncluyePlanPagos, PagoTotal, IdUsuario)
                    VALUES (?, ?, ?, ?, date(now()), ?, ?, ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('isssissi', $idColegiado, $bloque1, $bloque2, $tipoCuenta,$idBanco, $incluyePP, $incluyeTotal, $_SESSION['user_id']);
            }
            $stmt->execute();
            $stmt->store_result();

            if(mysqli_stmt_errno($stmt)==0) {
                //agrego los datos del tipo de notificacion
                $idDebito = $conect->insert_id;
                $resultado['estado'] = TRUE;
                $resultado['idDebito'] = $idDebito;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "Error agregando el debito. ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "Error al actualizar debito anterior";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idDebito.')';
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

function obtenerDebitoCBUporIdDebito($idDebito) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT d.IdColegiado, p.NumeroDocumento, c.Matricula
        FROM debitocbu d
        INNER JOIN colegiado c ON c.Id = d.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN enviodebitodetalle edd ON edd.IdDebitoTarjeta = d.Id
        WHERE edd.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idDebito);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $numeroDocumento, $matricula);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiado' => $idColegiado,
                    'numeroDocumento' => $numeroDocumento,
                    'matricula' => $matricula
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado en debito cbu".$idDebito;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando debito cbu";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;    
}