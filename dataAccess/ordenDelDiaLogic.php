<?php
function obtenerOrdenDelDia($anio, $estadoOrdenDia) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT *, (SELECT COUNT(*) FROM ordendeldiadetalle oddd WHERE oddd.IdOrdenDia = odd.Id AND oddd.Estado = 'A') AS Cantidad
            FROM ordendeldia odd
            WHERE Estado = ? AND SUBSTR(Fecha, 1, 4) = ?
            ORDER BY Fecha DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ss', $estadoOrdenDia, $anio);
    $stmt->execute();
    $stmt->bind_result($id, $fecha, $periodo, $numero, $fechaCarga, $idUsuario, $estado, $fechaDesde, $fechaHasta, $observaciones, $cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id,
                    'fecha' => $fecha,
                    'periodo' => $periodo,
                    'numero' => $numero, 
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario,
                    'estado' => $estado, 
                    'fechaDesde' => $fechaDesde, 
                    'fechaHasta' => $fechaHasta,
                    'observaciones' => $observaciones,
                    'cantidadDetalle' => $cantidad
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
            $resultado['mensaje'] = "No existen Orden Del Dia.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Orden Del Dia";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerOrdenDelDiaPorId($idOrdenDia){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT *
            FROM ordendeldia
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idOrdenDia);
    $stmt->execute();
    $stmt->bind_result($id, $fecha, $periodo, $numero, $fechaCarga, $idUsuario, $estado, $fechaDesde, $fechaHasta, $observaciones);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'id' => $id,
                'fecha' => $fecha,
                'periodo' => $periodo,
                'numero' => $numero, 
                'fechaCarga' => $fechaCarga,
                'idUsuario' => $idUsuario,
                'estado' => $estado, 
                'fechaDesde' => $fechaDesde, 
                'fechaHasta' => $fechaHasta,
                'observaciones' => $observaciones
             );

            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existe Orden Del Dia.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Orden Del Dia";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;	
}

function ordenDelDiaDetallePorIdOrdenDia($idOrdenDia, $tipoPlanilla) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), oddd.Id, me.IdTipoMesaEntrada, me.FechaIngreso, c.Matricula, 
            p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tm.DetalleCompleto,
            tme.Nombre as NombreMovimiento, oddd.TipoPlanilla, me.Observaciones, men.Tema, oddd.Orden
            FROM ordendeldiadetalle as oddd
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = oddd.IdMesaEntrada)
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            WHERE (oddd.Estado = 'A' OR oddd.Estado = 'P')
            AND oddd.TipoPlanilla = ?
            AND oddd.IdOrdenDia = ?
            ORDER BY oddd.Orden, me.IdMesaEntrada";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $tipoPlanilla, $idOrdenDia);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $idOrdenDiaDetalle, $idTipoMesaEntrada, $fechaIngreso, $matricula, $apellido, $nombre, $nombreRemitente, $detalleCompleto, $nombreMovimiento, $tipoPlanilla, $observaciones, $tema, $orden);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        //if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idMesaEntrada' => $idMesaEntrada,
                    'idOrdenDiaDetalle' => $idOrdenDiaDetalle,
                    'idTipoMesaEntrada' => $idTipoMesaEntrada,
                    'fechaIngreso' => $fechaIngreso,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'nombreRemitente' => $nombreRemitente,
                    'detalleCompleto' => $detalleCompleto,
                    'nombreMovimiento' => $nombreMovimiento,
                    'tipoPlanilla' => $tipoPlanilla,
                    'observaciones' => $observaciones,
                    'tema' => $tema,
                    'orden' => $orden
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        /*
        } else {
            $resultado['estado'] = TRUE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existe detalle en el Orden Del Dia.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
        */
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando detalle del Orden Del Dia";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

//se busca en mesa de entradas lo pendiente entre las fechas de entrada
function obtenerMovimientosParaOrdenDia($fechaDesde, $fechaHasta) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdTipoMesaEntrada, me.FechaIngreso, c.Matricula, p.Apellido, 
            p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, me.Observaciones, 
            men.Tema, tm.DetalleCompleto as DetalleCompleto
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN ? AND ?) AND (me.IdTipoMesaEntrada IN (1,3,4,7,8,9)) AND me.Estado = 'A'
            AND me.IdMesaEntrada NOT IN(SELECT oddd.IdMesaEntrada
                                        FROM ordendeldiadetalle as oddd
                                        INNER JOIN ordendeldia as odd ON (odd.Id = oddd.IdOrdenDia)
                                        WHERE oddd.Estado = 'A'
                                        AND odd.Estado IN('A', 'C'))
            ORDER BY me.IdMesaEntrada";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ss', $fechaDesde, $fechaHasta);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $idTipoMesaEntrada, $fechaIngreso, $matricula, $apellido, $nombre, $nombreRemitente, $nombreMovimiento, $observaciones, $tema, $detalleCompleto);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idMesaEntrada' => $idMesaEntrada,
                    'idTipoMesaEntrada' => $idTipoMesaEntrada,
                    'fechaIngreso' => $fechaIngreso,
                    'matricula' => $matricula, 
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'nombreRemitente' => $nombreRemitente, 
                    'nombreMovimiento' => $nombreMovimiento, 
                    'observaciones' => $observaciones,
                    'tema' => $tema,
                    'detalleCompleto' => $detalleCompleto
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
            $resultado['mensaje'] = "No existen items para generar Orden Del Dia.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando items para generar Orden Del Dia";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarOrdenDelDia($fecha, $periodo, $numero, $fechaDesde, $fechaHasta, $observaciones) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO ordendeldia 
                (Fecha, Periodo, Numero, FechaCarga, IdUsuario, Estado, FechaDesde, FechaHasta, Observaciones)
                VALUES (?, ?, ?, DATE(NOW()), ?, 'A', ?, ?, ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('siiisss', $fecha, $periodo, $numero, $_SESSION['user_id'], $fechaDesde, $fechaHasta, $observaciones);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL REGISTRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function agregarOrdenDelDiaDetalle($idOrdenDia, $tipoPlanilla, $idMesaEntrada) {
    try {
		$conect = conectar();
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "INSERT INTO ordendeldiadetalle 
                (IdOrdenDia, TipoPlanilla, IdMesaEntrada)
                VALUES (?, ?, ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isi', $idOrdenDia, $tipoPlanilla, $idMesaEntrada);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE REGISTRO DETALLE ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL REGISTRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function borrarDetallePorIdOrdenDia($idOrdenDia) {
    try {
		$conect = conectar();
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "DELETE FROM ordendeldiadetalle 
                WHERE IdOrdenDia = ?";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idOrdenDia);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE BORRO DETALLE ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL BORRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL REGISTRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function ordenDelDiaConDetalle($idOrdenDia) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT COUNT(*)
            FROM ordendeldiadetalle
            WHERE IdOrdenDia = ? AND Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idOrdenDia);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            $resultado = TRUE;
        } else {
            $resultado = FALSE;
        }
    } else {
        $resultado = FALSE;
    }
    
    return $resultado;	
}

function asignarTipoPlanillaAlDetalle($idOrdenDiaDetalle, $tipoPlanilla) {
	try {
		$conect = conectar();
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "UPDATE ordendeldiadetalle 
        		SET TipoPlanilla = ?
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('si', $tipoPlanilla, $idOrdenDiaDetalle);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE CAMBIO DETALLE ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL CAMBIAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL CAMBIAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }	
}

function obtenerNumeroOrdenDelDia($periodo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT MAX(Numero)
            FROM ordendeldia
            WHERE Periodo = ? AND Estado <> 'B'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $periodo);
    $stmt->execute();
    $stmt->bind_result($numero);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if (isset($numero) && $numero > 0) {
            $numero++;
        } else {
            $numero = 1;
        }
    } else {
        $numero = 1;
    }
    
    return $numero;    
}

function borrarOrdenDelDia($idOrdenDia) {
    try {
        $conect = conectar();
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "UPDATE ordendeldia 
                SET Estado = 'B'
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idOrdenDia);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE CERRO ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL CERRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL CERRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function cerrarOrdenDelDia($idOrdenDia) {
    try {
        $conect = conectar();
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $sql = "UPDATE ordendeldia 
                SET Estado = 'C'
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idOrdenDia);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "SE CERRO ORDEN DEL DIA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL CERRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
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
        $resultado['mensaje'] = "ERROR AL CERRAR ORDEN DEL DIA ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}