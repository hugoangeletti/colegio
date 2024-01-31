<?php
function obtenerSanciones($estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadosancion.Id, colegiadosancion.Matricula, colegiadosancion.ApellidoNombres, 
        colegiadosancion.Ley, colegiadosancion.FechaDesde, colegiadosancion.FechaHasta, 
        colegiadosancion.Articulo, colegiadosancion.Detalle, colegiadosancion.Distrito, 
        colegiadosancion.Provincia, colegiadosancion.IdColegiado, colegiadosanciongasto.CantidadGalenos, 
        colegiadosanciongasto.FechaPago, colegiadosanciongasto.id
        FROM colegiadosancion 
        LEFT JOIN colegiadosanciongasto ON(colegiadosanciongasto.IdColegiadoSancion = colegiadosancion.Id AND colegiadosanciongasto.Estado = 'A')
        WHERE colegiadosancion.Estado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($id, $matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, $articulo, $detalle, $distrito, $provincia, $idColegiado, $cantidadGalenos, $fechaPago, $idCostas);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoSancion' => $id,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellidoNombre,
                    'ley' => $ley,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta, 
                    'articulo' => $articulo,
                    'detalle' => $detalle,
                    'distrito' => $distrito,
                    'provincia' => $provincia,
                    'idColegiado' => $idColegiado,
                    'cantidadGalenos' => $cantidadGalenos,
                    'fechaPago' => $fechaPago,
                    'idCostas' => $idCostas
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
            $resultado['mensaje'] = "NO SE ENCONTRARON SANCIONES";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Sanciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSancionPorId($idColegiadoSancion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Matricula, ApellidoNombres, Ley, FechaDesde, FechaHasta, Articulo, Codigo, Detalle, 
            Distrito, Provincia, IdColegiado, Estado
        FROM colegiadosancion
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoSancion);
    $stmt->execute();
    $stmt->bind_result($id, $matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, $articulo, $codigo, $detalle, $distrito, $provincia, $idColegiado, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            switch ($estado) {
                case 'A':
                    $estadoDetalle = 'Activa';
                    break;

                case 'B':
                    $estadoDetalle = 'Anulada';
                    break;

                default:
                    $estadoDetalle = 'Mal Cargada';
                    break;
            }
            $datos = array (
                    'idColegiadoSancion' => $id,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellidoNombre,
                    'ley' => $ley,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta, 
                    'articulo' => $articulo,
                    'codigo' => $codigo,
                    'detalle' => $detalle,
                    'distrito' => $distrito,
                    'provincia' => $provincia,
                    'idColegiado' => $idColegiado,
                    'estado' => $estado,
                    'estadoDetalle' => $estadoDetalle
                );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "NO SE ENCONTRO LA SANCION";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Sanciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSancionesPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Ley, FechaDesde, FechaHasta, Articulo, Detalle, Distrito, Provincia, Estado
    FROM colegiadosancion
    WHERE IdColegiado = ?";
    //AND articulo<>'52a' AND articulo<>'52b'
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $ley, $fechaDesde, $fechaHasta, $articulo, $detalle, $distrito, $provincia, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                switch ($estado) {
                    case 'A':
                        $estadoDetalle = 'ACTIVA';
                        break;

                    case 'B':
                        $estadoDetalle = 'ANULADA';
                        break;

                    default:
                        $estadoDetalle = 'Mal Cargada';
                        break;
                }
                $row = array (
                    'idColegiadoSancion' => $id,
                    'ley' => $ley,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta, 
                    'articulo' => $articulo,
                    'detalle' => $detalle,
                    'distrito' => $distrito,
                    'provincia' => $provincia,
                    'estado' => $estado,
                    'estadoDetalle' => $estadoDetalle
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
            $resultado['mensaje'] = "El colegiado no tiene Sanciones.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Sanciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarSancion($matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, $articulo, $codigo, 
        $detalle, $distrito, $provincia, $idColegiado) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO colegiadosancion 
                (Matricula, ApellidoNombres, Ley, FechaDesde, FechaHasta, Articulo, Codigo, Detalle, Distrito, 
                Provincia, IdUsuario, FechaCarga, IdColegiado, Estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'A')";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isssssssssisi', $matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, 
                $articulo, $codigo, $detalle, $distrito, $provincia, $_SESSION['user_id'], $fechaCarga, 
                $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO LA SANCION CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR SANCION ".mysqli_stmt_errno($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR FALSO MEDICO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function editarSancion($idColegiadoSancion, $matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, $articulo, $codigo, 
        $detalle, $distrito, $provincia, $idColegiado, $estado) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "UPDATE colegiadosancion 
                SET Matricula = ?, 
                ApellidoNombres = ?, 
                Ley = ?, 
                FechaDesde = ?, 
                FechaHasta = ?, 
                Articulo = ?, 
                Codigo = ?, 
                Detalle = ?, 
                Distrito = ?, 
                Provincia = ?, 
                IdUsuario = ?, 
                FechaCarga = ?, 
                IdColegiado = ?, 
                Estado = ?
                WHERE Id = ?";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isssssssssisisi', $matricula, $apellidoNombre, $ley, $fechaDesde, $fechaHasta, 
                $articulo, $codigo, $detalle, $distrito, $provincia, $_SESSION['user_id'], $fechaCarga, 
                $idColegiado, $estado, $idColegiadoSancion);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE ACTUALIZO LA SANCION CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR SANCION ".mysqli_stmt_errno($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR FALSO MEDICO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function obtenerCostasPorId($idCostas) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiadosanciongasto WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCostas);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiadoSancion, $cantidadGalenos, $fechaVencimiento, $fechaPago, $estado, $importePagado, $idUsuario, $fechaCarga);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            switch ($estado) {
                case 'A':
                    $estadoDetalle = 'A pagar';
                    break;

                case 'B':
                    $estadoDetalle = 'Anulada';
                    break;

                case 'P':
                    $estadoDetalle = 'Abonada';
                    break;

                default:
                    $estadoDetalle = 'Mal Cargada';
                    break;
            }
            $datos = array (
                    'idColegiadoSancion' => $idColegiadoSancion,
                    'cantidadGalenos' => $cantidadGalenos,
                    'fechaVencimiento' => $fechaVencimiento,
                    'fechaPago' => $fechaPago,
                    'importePagado' => $importePagado,
                    'estado' => $estado,
                    'estadoDetalle' => $estadoDetalle,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga
                );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "NO SE ENCONTRO COSTAS";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando COSTAS";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarCostas($idColegiadoSancion, $cantidadGalenos, $fechaVencimiento) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO colegiadosanciongasto 
                (IdColegiadoSancion, CantidadGalenos, FechaVencimiento, Estado, IdUsuario, FechaCarga)
                VALUES (?, ?, ?, 'A', ?, ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisis', $idColegiadoSancion, $cantidadGalenos, $fechaVencimiento, $_SESSION['user_id'], $fechaCarga);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO COSTAS CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR COSTAS ".mysqli_stmt_errno($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR COSTAS";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function editarCostas($idCostas, $cantidadGalenos, $fechaVencimiento, $estado) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "UPDATE colegiadosanciongasto 
                SET CantidadGalenos = ?, 
                    FechaVencimiento = ?, 
                    Estado = ?, 
                    IdUsuario = ?, 
                    FechaCarga = ?
                WHERE id = ?";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('issisi', $cantidadGalenos, $fechaVencimiento, $estado, $_SESSION['user_id'], $fechaCarga, $idCostas);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE ACTUALIZO COSTAS CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR COSTAS ".mysqli_stmt_errno($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR COSTAS";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

//function obtenerSancionesTodasPorIdColegiado($idColegiado) {
//    $conect = conectar();
//    mysqli_set_charset( $conect, 'utf8');
//    $sql="SELECT Id, Ley, FechaDesde, FechaHasta, Articulo, Detalle, Distrito, Provincia
//        FROM colegiadosancion
//        WHERE IdColegiado = ?";
//    $stmt = $conect->prepare($sql);
//    $stmt->bind_param('i', $idColegiado);
//    $stmt->execute();
//    $stmt->bind_result($id, $ley, $fechaDesde, $fechaHasta, $articulo, $detalle, $distrito, $provincia);
//    $stmt->store_result();
//
//    $resultado = array();
//    if(mysqli_stmt_errno($stmt)==0) {
//        if (mysqli_stmt_num_rows($stmt) > 0) {
//            $datos = array();
//            while (mysqli_stmt_fetch($stmt)) 
//            {
//                $row = array (
//                    'idColegiadoSancion' => $id,
//                    'ley' => $ley,
//                    'fechaDesde' => $fechaDesde,
//                    'fechaHasta' => $fechaHasta, 
//                    'articulo' => $articulo,
//                    'detalle' => $detalle,
//                    'distrito' => $distrito,
//                    'provincia' => $provincia
//                 );
//                array_push($datos, $row);
//            }
//            $resultado['estado'] = TRUE;
//            $resultado['mensaje'] = "OK";
//            $resultado['datos'] = $datos;
//            $resultado['clase'] = 'alert alert-success'; 
//            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
//        } else {
//            $resultado['estado'] = FALSE;
//            $resultado['datos'] = NULL;
//            $resultado['mensaje'] = "El colegiado no tiene Sanciones.";
//            $resultado['clase'] = 'alert alert-warning'; 
//            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
//        }
//    } else {
//        $resultado['estado'] = false;
//        $resultado['mensaje'] = "Error buscando Sanciones";
//        $resultado['clase'] = 'alert alert-error'; 
//        $resultado['icono'] = 'glyphicon glyphicon-remove';
//    }
//    
//    return $resultado;
//}
