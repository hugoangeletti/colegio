<?php
function obtenerTramites($estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tramite WHERE Estado = ? ORDER BY Fecha DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $fecha, $fechaDesde, $fechaHasta, $estado, $destino, $idUsuario, $tipoTramite);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idTramite' => $id,
                'detalle' => $detalle,
                'fecha' => $fecha,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'destino' => $destino,
                'idUsuario' => $idUsuario,
                'tipoTramite' => $tipoTramite
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
        $resultado['mensaje'] = "Error buscando Tramites";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTramitePorId($idTramite) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tramite WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idTramite);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $fecha, $fechaDesde, $fechaHasta, $estado, $destino, $idUsuario, $tipoTramite);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'idTramite' => $id,
                'detalle' => $detalle,
                'fecha' => $fecha,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'destino' => $destino,
                'idUsuario' => $idUsuario,
                'tipoTramite' => $tipoTramite
                );
        }
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Tramites";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTramiteDetalle($idTramite) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT td.Id, td.IdTipoMovimiento, td.Fecha, c.Matricula, p.Apellido, p.Nombres, tm.Detalle, td.DistritoCambio
        FROM tramitedetalle td
        INNER JOIN colegiado c ON c.Id = td.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento tm ON tm.Id = td.IdTipoMovimiento
        INNER JOIN tipotramite tt ON tt.Id = td.IdTipoTramite
        WHERE td.IdTramite = ?
        ORDER BY td.Fecha";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idTramite);
    $stmt->execute();
    $stmt->bind_result($id, $idTipoMovimiento, $fecha, $matricula, $apellido, $nombre, $nombreMovimiento, $distritoCambio);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idTramiteDetalle' => $id,
                'idTipoMovimiento' => $idTipoMovimiento,
                'fecha' => $fecha,
                'matricula' => $matricula,
                'apellido' => $apellido,
                'nombre' => $nombre,
                'nombreMovimiento' => $nombreMovimiento,
                'distritoCambio' => $distritoCambio
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
        $resultado['mensaje'] = "Error buscando Detalle del Tramite";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function agregarTramites($fechaDesde, $fechaHasta, $detalle, $tipoTramite) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        $continuar = TRUE;
        if ($tipoTramite == "M") {
            //obtengo los movimientos y las altas entre fechas
            $sql="SELECT 'ALTA' as Tipo, Id AS IdColegiado, FechaMatriculacion AS FechaDesde, NULL AS FechaHasta, 1 AS IdMovimiento, NULL AS DistritoCambio
                FROM colegiado
                WHERE (FechaMatriculacion >= ? AND FechaMatriculacion <= ?)
                AND DistritoOrigen = 1

                UNION ALL
                            
                SELECT 'MOVIMIENTO' as Tipo, IdColegiado, FechaDesde, FechaHasta, IdMovimiento, DistritoCambio
                FROM colegiadomovimiento
                WHERE ((fechacarga >= ? AND fechacarga <= ?)
                OR (FechaCargaRehabilitacion >= ? AND FechaCargaRehabilitacion <= ?))
                AND  estado<>'A'";
            $stmt1 = $conect->prepare($sql);
            $stmt1->bind_param('ssssss', $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta);
        } else {
            if ($tipoTramite == "F") {
                $sql = "SELECT 'FALLECIDOS' as Tipo, cm.IdColegiado, cm.FechaDesde, cm.FechaHasta, cm.IdMovimiento, cm.DistritoCambio
                    FROM colegiadomovimiento cm
                    INNER JOIN tipomovimiento tm ON tm.Id = cm.IdMovimiento
                    WHERE ((cm.FechaCarga >= ? AND cm.FechaCarga <= ?)
                    OR (cm.FechaCargaRehabilitacion >= ? AND cm.FechaCargaRehabilitacion <= ?))
                    AND cm.Estado <> 'A'
                    AND tm.Estado = 'F'";
                $stmt1 = $conect->prepare($sql);
                $stmt1->bind_param('ssss', $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta);
            } else {
                if ($tipoTramite == "J") {
                    $sql = "SELECT 'FALLECIDOS' as Tipo, cm.IdColegiado, cm.FechaDesde, cm.FechaHasta, cm.IdMovimiento, cm.DistritoCambio
                        FROM colegiadomovimiento cm
                        INNER JOIN tipomovimiento tm ON tm.Id = cm.IdMovimiento
                        WHERE ((cm.FechaCarga >= ? AND cm.FechaCarga <= ?)
                        OR (cm.FechaCargaRehabilitacion >= ? AND cm.FechaCargaRehabilitacion <= ?))
                        AND cm.Estado <> 'A'
                        AND tm.Estado = 'J'";
                    $stmt1 = $conect->prepare($sql);
                    $stmt1->bind_param('ssss', $fechaDesde, $fechaHasta, $fechaDesde, $fechaHasta);
                } else {
                    $continuar = FALSE;
                }                
            }
        }

        if ($continuar) {
            $stmt1->execute();
            $stmt1->bind_result($tipo, $idColegiado, $fecha, $fechaHastaMovimiento, $idTipoMovimiento, $distritoCambio);
            $stmt1->store_result();

            $resultado = array();
            if(mysqli_stmt_errno($stmt1)==0) {
                $idTramite = NULL;
                $continua = TRUE;
                while (mysqli_stmt_fetch($stmt1) && $continua) {
                    if (!isset($idTramite)) {
                        $sql="INSERT INTO tramite 
                            (Detalle, Fecha, FechaDesde, FechaHasta, Estado) 
                            VALUES (?, date(now()), ?, ?, 'G')";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('sss', $detalle, $fechaDesde, $fechaHasta);

                        $stmt->execute();
                        $stmt->store_result();
                        if (mysqli_stmt_errno($stmt)==0) {
                            $idTramite = $conect->insert_id;
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL AGREGAR LISTADO. ".mysqli_stmt_error($stmt);
                            $resultado['clase'] = 'alert alert-danger'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                            $continua = FALSE;
                        }
                    } 
                    if ($continua) {
                        $cargar = TRUE;
                        if ($tipo == "MOVIMIENTO") {
                            $fechaVer = sumarRestarSobreFecha($fechaDesde, 30, 'day', '-');
                            if (isset($fechaHastaMovimiento)) {
                                if ($fechaHastaMovimiento > $fechaVer) {
                                    $idTipoMovimiento = 20;
                                    $fecha = $fechaHastaMovimiento;
                                } else {
                                    $cargar = FALSE;
                                }
                            } else {
                                if ($fecha < $fechaVer) {
                                    $cargar = FALSE;
                                }
                            }
                        }
                        if ($cargar) {
                            $sql="INSERT INTO tramitedetalle 
                                (IdTramite, IdTipoTramite, IdTipoMovimiento, Fecha, DistritoCambio, IdColegiado) 
                                VALUES (?, 1, ?, ?, ?, ?)";
                            $stmt = $conect->prepare($sql);
                            $stmt->bind_param('iissi', $idTramite, $idTipoMovimiento, $fecha, $distritoCambio, $idColegiado);
                            $stmt->execute();
                            $stmt->store_result();
                        }
                    }
                }
                if (!isset($idTramite)) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "NO EXISTEN MOVIMIENTOS. ".mysqli_stmt_error($stmt);
                    $resultado['clase'] = 'alert alert-danger'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                } else {
                    $resultado['estado'] = TRUE;
                    $resultado['mensaje'] = "OK";
                    $resultado['clase'] = 'alert alert-success'; 
                    $resultado['icono'] = 'glyphicon glyphicon-ok';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL BUSCAR MOVIMIENTOS. ".mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-danger'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR DATOS DE ENTRADA";
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        if ($resultado['estado']) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'SE GENERO EL LISTADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idTramite'] = $idTramite;
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

/*
function obtenerBancoPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select Id, Nombre from banco where Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'id' => $id,
                'nombre' => $nombre
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el banco";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando banco";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}
*/


