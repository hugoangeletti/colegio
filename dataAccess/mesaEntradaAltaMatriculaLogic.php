<?php
function realizarAltaMesaEntrada($idColegiado, $idTipoMovimiento, $distrito) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        
        $sql = "INSERT INTO mesaentrada(TipoRemitente, IdColegiado, IdTipoMesaEntrada, FechaIngreso, 
                Estado, IdUsuario, EstadoMatricular, EstadoTesoreria)
                VALUES('C', ?, 1, date(now()), 'A', ?, ?, 0)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iii', $idColegiado, $_SESSION['user_id'], $idTipoMovimiento);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $idMesaEntrada = mysqli_stmt_insert_id($stmt);
            switch ($idTipoMovimiento) {
                case 5:
                    $idMotivo = 11; //Ingreso Definitivo como Colegiado
                    break;

                case 8:
                    $idMotivo = 8; //Inscripción al Distrito I
                    break;

                case 10:
                    $idMotivo = 7; //Cambio de Distrito
                    break;

                default:
                    $idMotivo = NULL;
                    break;
            }
            $sql="INSERT INTO mesaentradamovimiento 
                (IdMesaEntrada, IdTipoMovimiento, Fecha, IdMotivoCancelacion, Distrito) 
                VALUES (?, ?, date(now()), ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iiii', $idMesaEntrada, $idTipoMovimiento, $idMotivo, $distrito);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $sql = "UPDATE colegiado 
                        SET Estado = ? 
                        WHERE Id = ?";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idTipoMovimiento, $idColegiado);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    $sql = "INSERT INTO colegiadomovimiento(IdColegiado, IdMovimiento, FechaDesde, DistritoCambio, 
                            IdUsuarioCarga, FechaCarga, Estado) 
                            VALUES(?, ?, date(now()), ?, ".$_SESSION['user_id'].", date(now()), 'O')";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('iis', $idColegiado, $idTipoMovimiento, $distrito);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $idColegiadoMovimiento = mysqli_stmt_insert_id($stmt);
                        $sql = "INSERT INTO colegiadomovimientomesaentrada(IdColegiadoMovimiento, IdMesaEntrada)
                                VALUES(?, ?)";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('ii', $idColegiadoMovimiento, $idMesaEntrada);
                        $stmt->execute();
                        $stmt->store_result();
                        if(mysqli_stmt_errno($stmt)==0) {
                            $resultado['estado'] = TRUE;
                            $resultado['mensaje'] = 'El movimiento en Mesa de Entrada se registro correctamente';
                            $resultado['clase'] = 'alert alert-success'; 
                            $resultado['icono'] = 'glyphicon glyphicon-ok';
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL AGREGAR colegiadomovimientomesaentrada";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL AGREGAR colegiadomovimiento";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL ACTUALIZAR colegiado";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR mesaentradamovimiento";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR mesaentrada";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'El movimiento en Mesa de Entrada se registro correctamente';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idMesaEntrada'] = $idMesaEntrada;
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            $resultado['mensaje'] .= ' (DEBE IR AL SISTEMA DE MESA DE ENTRADAS Y REGISTRAR EL MOVIMIENTO)';
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }    
}

function noHayMesaEntradaRegistrada($idColegiado, $idTipoMovimiento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT IdMesaEntrada
            FROM mesaentrada WHERE IdColegiado = ? AND EstadoMatricular = ? 
            AND IdTipoMesaEntrada = 1
            AND FechaIngreso = DATE(NOW())
            AND Estado = 'A'
            LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idTipoMovimiento);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = FALSE;
            $resultado['idMesaEntrada'] = $id;
        } else {
            $resultado['estado'] = TRUE;
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['idMesaEntrada'] = 0;        
    }
    return $resultado;
    
}