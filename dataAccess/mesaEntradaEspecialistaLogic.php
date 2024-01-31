<?php
function obtenerEspecialistaPorExpediente($expediente, $anio){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, e.Id, e.Especialidad, mee.IdMesaEntradaEspecialidad, ce.FechaRecertificacion, ce.FechaVencimiento, e.IdTipoEspecialidad, te.Nombre, tes.Nombre, me.IdMesaEntrada, me.FechaIngreso, me.EstadoMatricular, me.EstadoTesoreria, mee.IncisoArticulo8, mee.Distrito, tes1.IdTipoEspecialista, tes.IdTipoEspecialista
        FROM mesaentradaespecialidad mee
        INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
        INNER JOIN colegiado c ON(c.Id = me.IdColegiado)
        INNER JOIN persona p ON(p.Id = c.IdPersona)
        INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad)
        INNER JOIN tipoespecialista tes ON(tes.IdTipoEspecialista = mee.IdTipoEspecialista)
        LEFT JOIN colegiadoespecialista ce ON(ce.IdColegiado = c.Id AND ce.Especialidad = mee.IdEspecialidad)
        LEFT JOIN tipoespecialista tes1 ON(tes1.IdTipoEspecialista = ce.IdTipoEspecialista)
        LEFT JOIN colegiadoespecialistatipo cet ON(cet.IdColegiadoEspecialista = ce.Id)
        LEFT JOIN tipoespecialidad te ON(te.id = e.IdTipoEspecialidad)
        WHERE mee.NumeroExpediente = ? AND mee.AnioExpediente = ? AND me.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $expediente, $anio);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombre, $idEspecialidad, $nombreEspecialidad, $idMesaEntradaEspecialidad, $ultimaRecertificacion, $fechaVencimiento, $idTipoEspecialidad, $nombreTipoEspecialidad, $nombreTipoEspecialista, $idMesaEntrada, $fechaMesaEntrada, $estadoMatricular, $estadoTesoreria, $inciso, $distrito, $idTipoEspecialistaColegiadoEspecialista, $idTipoEspecialista);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = TRUE;
            
            if ($idTipoEspecialista == EXCEPTUADO_ART_8) {
                //si es por excepcion, le agregamos el inciso y la leyenda
                if (isset($inciso) && $inciso <> "") {
                    $nombreTipoEspecialista .= ' Inciso '.$inciso.' ('.  obtenerDetalleIncisoEspecialistaArt8($inciso).')';
                }
            }

            if ($idTipoEspecialista == OTRO_DISTRITO) {
                //si es de otro distrito le agragmos la leyenda con el ditrito de origen
                if (isset($distrito) && $distrito <> "") {
                    $nombreTipoEspecialista .= ' (Origen: Distrito '.obtenerNumeroRomano($distrito).')';
                }
            }

            $datos = array(
                'idColegiado' => $idColegiado,
                'matricula' => $matricula,
                'apellidoNombre' => trim($apellido).' '.trim($nombre),
                'idEspecialidad' => $idEspecialidad,
                'nombreEspecialidad' => $nombreEspecialidad,
                'idMesaEntradaEspecialidad' => $idMesaEntradaEspecialidad,
                'ultimaRecertificacion' => $ultimaRecertificacion,
                'fechaVencimiento' => $fechaVencimiento,
                'idTipoEspecialidad' => $idTipoEspecialidad,
                'nombreTipoEspecialidad' => $nombreTipoEspecialidad,
                'nombreTipoEspecialista' => $nombreTipoEspecialista,
                'idMesaEntrada' => $idMesaEntrada,
                'fechaMesaEntrada' => $fechaMesaEntrada,
                'estadoMatricular' => $estadoMatricular,
                'estadoTesoreria' => $estadoTesoreria,
                'inciso' => $inciso,
                'distrito' => $distrito,
                'idTipoEspecialistaColegiadoEspecialista' => $idTipoEspecialistaColegiadoEspecialista,
                'idTipoEspecialista' => $idTipoEspecialista
            );
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontró EXPEDIENTE ".$expediente.'/'.$anio;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando EXPEDIENTE ".$expediente.'/'.$anio;
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerExpedientesPorFechaMatricula($fecha, $matricula) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    
    if (isset($matricula) && $matricula > 0) {
        $where = "WHERE me.Estado = 'A' AND c.Matricula = ?";
    } else {
        $where = "WHERE me.Estado = 'A' AND me.FechaIngreso = ?";
    }
    
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, e.Id, e.Especialidad, mee.IdMesaEntradaEspecialidad, mee.IdTipoEspecialista, ce.FechaRecertificacion, ce.FechaVencimiento, e.IdTipoEspecialidad, te.Nombre, tes.Nombre, me.IdMesaEntrada, me.FechaIngreso, me.EstadoMatricular, me.EstadoTesoreria, mee.IncisoArticulo8, mee.Distrito, mee.NumeroExpediente, mee.AnioExpediente, tes1.IdTipoEspecialista
        FROM mesaentradaespecialidad mee
        INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
        INNER JOIN colegiado c ON(c.Id = me.IdColegiado)
        INNER JOIN persona p ON(p.Id = c.IdPersona)
        INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad)
        INNER JOIN tipoespecialista tes ON(tes.IdTipoEspecialista = mee.IdTipoEspecialista)
        LEFT JOIN colegiadoespecialista ce ON(ce.IdColegiado = c.Id AND ce.Especialidad = mee.IdEspecialidad)
        LEFT JOIN tipoespecialista tes1 ON(tes1.IdTipoEspecialista = ce.IdTipoEspecialista)
        LEFT JOIN colegiadoespecialistatipo cet ON(cet.IdColegiadoEspecialista = ce.Id)
        LEFT JOIN tipoespecialidad te ON(te.id = e.IdTipoEspecialidad) ".$where;
    
        //WHERE me.Estado = 'A' AND me.FechaIngreso = ?";
    $stmt = $conect->prepare($sql);
    if (isset($matricula) && $matricula > 0) {
        $stmt->bind_param('i', $matricula);
    } else {
        $stmt->bind_param('s', $fecha);
    }
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombre, $idEspecialidad, $nombreEspecialidad, $idMesaEntradaEspecialidad, $idTipoEspecialista, $ultimaRecertificacion, $fechaVencimiento, $idTipoEspecialidad, $nombreTipoEspecialidad, $nombreTipoEspecialista, $idMesaEntrada, $fechaMesaEntrada, $estadoMatricular, $estadoTesoreria, $inciso, $distrito, $numeroExpediente, $anioExpediente, $idTipoEspecialistaColegiadoEspecialista);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($idTipoEspecialista == EXCEPTUADO_ART_8) {
                    //si es por excepcion, le agregamos el inciso y la leyenda
                    if (isset($inciso) && $inciso <> "") {
                        $nombreTipoEspecialista .= ' Inciso '.$inciso.' ('.  obtenerDetalleIncisoEspecialistaArt8($inciso).')';
                    }
                } else {
                    if ($idTipoEspecialista == OTRO_DISTRITO) {
                        //si es de otro distrito le agragmos la leyenda con el ditrito de origen
                        if (isset($distrito) && $distrito <> "") {
                            $nombreTipoEspecialista .= ' (Origen: Distrito '.obtenerNumeroRomano($distrito).')';
                        }
                    }
                }

                $row = array (
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'idEspecialidad' => $idEspecialidad,
                    'nombreEspecialidad' => $nombreEspecialidad,
                    'idMesaEntradaEspecialidad' => $idMesaEntradaEspecialidad,
                    'idTipoEspecialista' => $idTipoEspecialista,
                    'ultimaRecertificacion' => $ultimaRecertificacion,
                    'fechaVencimiento' => $fechaVencimiento,
                    'idTipoEspecialidad' => $idTipoEspecialidad,
                    'nombreTipoEspecialidad' => $nombreTipoEspecialidad,
                    'nombreTipoEspecialista' => $nombreTipoEspecialista,
                    'idMesaEntrada' => $idMesaEntrada,
                    'fechaMesaEntrada' => $fechaMesaEntrada,
                    'estadoMatricular' => $estadoMatricular,
                    'estadoTesoreria' => $estadoTesoreria,
                    'inciso' => $inciso,
                    'distrito' => $distrito,
                    'numeroExpediente' => $numeroExpediente,
                    'anioExpediente' => $anioExpediente,
                    'idTipoEspecialistaColegiadoEspecialista' => $idTipoEspecialistaColegiadoEspecialista
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
            $resultado['mensaje'] = "No existen expedientes.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando expedientes";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function realizarAltaMesaEntrada($idColegiado, $idTipoEspecialista, $idEspecialidad, $idTipoMovimiento, $estadoTesoreria, $distrito, $incisoArticulo8) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();

        //busco el importe para cargar en mesaentrada        
        $sql = "SELECT te.IdTipoEspecialista, te.Nombre, te.Codigo, te.IdTipoPago, te.IdTipoResolucion
            FROM tipoespecialista te
            WHERE te.IdTipoEspecialista = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idTipoEspecialista);
        $stmt->execute();
        $stmt->bind_result($id, $nombre, $codigo, $idTipoPago, $idTipoResolucion);
        $stmt->store_result();

        if(mysqli_stmt_errno($stmt) == 0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $row = mysqli_stmt_fetch($stmt);

                $sql = "INSERT INTO mesaentrada(TipoRemitente, IdColegiado, IdTipoMesaEntrada, FechaIngreso, 
                        Estado, IdUsuario, EstadoMatricular, EstadoTesoreria, IdTipoPago, Pagado)
                        VALUES('C', ?, 2, date(now()), 'A', ?, ?, ?, ?, 0)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iiiis', $idColegiado, $_SESSION['user_id'], $idTipoMovimiento, $estadoTesoreria, $idTipoPago);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)==0) {
                    $idMesaEntrada = mysqli_stmt_insert_id($stmt);
                    
                    //obtener el nro de expediente del año corriente
                    $anioExpediente = date('Y');
                    $resExpediente = obtenerUltimoNumeroExpediente($anioExpediente);
                    if ($resExpediente['estado']) {
                        $numeroExpediente = $resExpediente['numeroExpediente'];

                        $sql="INSERT INTO mesaentradaespecialidad 
                            (IdMesaEntrada, IdEspecialidad, NumeroExpediente, AnioExpediente, Distrito, IncisoArticulo8, IdTipoEspecialista) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('iiiiisi', $idMesaEntrada, $idEspecialidad, $numeroExpediente, $anioExpediente, $distrito, $incisoArticulo8, $idTipoEspecialista);
                        $stmt->execute();
                        $stmt->store_result();
                        if(mysqli_stmt_errno($stmt)==0) {
                            $idMesaEntradaEspecialidad = mysqli_stmt_insert_id($stmt);

                            $resultado['estado'] = TRUE;
                            $resultado['mensaje'] = 'El movimiento en Mesa de Entrada se registro correctamente';
                            $resultado['clase'] = 'alert alert-success'; 
                            $resultado['icono'] = 'glyphicon glyphicon-ok';
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] = "ERROR AL AGREGAR mesaentradamovimiento ".mysqli_stmt_error($stmt);
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                            exit;
                        }
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL BUSCAR EL NUMERO DE EXPEDIENTE DEL AÑO ".$anio;
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR mesaentrada";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR mesaentrada, no se encontro tipo de especialista";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR mesaentrada ".mysqli_stmt_error($stmt);
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $datos['idMesaEntrada'] = $idMesaEntrada;
            $datos['idMesaEntradaEspecialidad'] = $idMesaEntradaEspecialidad;
            $datos['numeroExpediente'] = $numeroExpediente;
            $datos['anioExpediente'] = $anioExpediente;
            $resultado['datos'] = $datos;
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= ' (DEBE IR AL SISTEMA DE MESA DE ENTRADAS Y REGISTRAR EL MOVIMIENTO)';
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = ' (ERROR)';
        return $resultado;
    }    
}

function realizarModificacionMesaEntrada($idMesaEntradaEspecialidad, $idTipoEspecialista, $idEspecialidad, $distrito, $inciso) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql="UPDATE mesaentradaespecialidad 
        SET IdEspecialidad = ?, 
            IdTipoEspecialista = ?, 
            Distrito = ?, 
            IncisoArticulo8 = ? 
        WHERE IdMesaEntradaEspecialidad = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiisi', $idEspecialidad, $idTipoEspecialista, $distrito, $inciso, $idMesaEntradaEspecialidad);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'El movimiento en Mesa de Entrada se modifico correctamente';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL modificar mesaentradaespecialidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function realizarBajaMesaEntrada($idMesaEntradaEspecialidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
        
    $sql="UPDATE mesaentrada me
        INNER JOIN mesaentradaespecialidad mee ON(mee.IdMesaEntrada = me.IdMesaEntrada)
        SET Estado = 'B'
        WHERE mee.IdMesaEntradaEspecialidad = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idMesaEntradaEspecialidad);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'El movimiento en Mesa de Entrada se elimino correctamente';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL eliminar mesaentradaespecialidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerUltimoNumeroExpediente($anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT MAX(mee.NumeroExpediente) as numero
            FROM mesaentradaespecialidad mee
            INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
            WHERE mee.AnioExpediente = ? AND me.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $anio);
    $stmt->execute();
    $stmt->bind_result($numero);
    $stmt->store_result();

    $resultado = array();
    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if (isset($numero)) {
            $resultado['numeroExpediente'] = $numero + 1;
        } else {
            $resultado['numeroExpediente'] = 1;            
        }
        $resultado['estado'] = TRUE;
    }
    return $resultado;
}

function expedienteIngresadoPendiente($idColegiado, $idEspecialidad, $tipoEspecilidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT mee.NumeroExpediente, mee.AnioExpediente
            FROM mesaentradaespecialidad mee
            INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
            LEFT JOIN resoluciondetalle rd ON(rd.IdMesaEntradaEspecialidad = mee.IdMesaEntradaEspecialidad)            
            WHERE me.IdColegiado = ? AND mee.IdEspecialidad = ? AND mee.IdTipoEspecialista = ? 
            AND me.Estado = 'A' AND rd.Id IS NULL AND NOW() < date_add(me.FechaIngreso, INTERVAL 365 DAY)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iis', $idColegiado, $idEspecialidad, $tipoEspecilidad);
    $stmt->execute();
    $stmt->bind_result($numero, $anio);
    $stmt->store_result();

    $resultado = array();
    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            if (isset($numero)) {
                $resultado['numeroExpediente'] = $numero;
                $resultado['anioExpediente'] = $anio;            
            }
            $resultado['estado'] = TRUE;
        }
    }
    return $resultado;    
}

function obtenerMesaEntradaEspecialistaPorId($idMesaEntradaEspecialidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, e.Id, e.Especialidad, mee.IdMesaEntradaEspecialidad, 
        mee.IdTipoEspecialista, ce.FechaRecertificacion, ce.FechaVencimiento, e.IdTipoEspecialidad, te.Nombre, tes.Nombre,
        me.IdMesaEntrada, me.FechaIngreso, me.EstadoMatricular, me.EstadoTesoreria, mee.NumeroExpediente, mee.AnioExpediente,
        mee.IncisoArticulo8, mee.Distrito, ce.FechaEspecialista
        FROM mesaentradaespecialidad mee
        INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
        INNER JOIN colegiado c ON(c.Id = me.IdColegiado)
        INNER JOIN persona p ON(p.Id = c.IdPersona)
        INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad)
        INNER JOIN tipoespecialista tes ON(tes.IdTipoEspecialista = mee.IdTipoEspecialista)
        LEFT JOIN colegiadoespecialista ce ON(ce.IdColegiado = c.Id AND ce.Especialidad = mee.IdEspecialidad)
        LEFT JOIN colegiadoespecialistatipo cet ON(cet.IdColegiadoEspecialista = ce.Id)
        LEFT JOIN tipoespecialidad te ON(te.id = e.IdTipoEspecialidad)
        WHERE mee.IdMesaEntradaEspecialidad = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idMesaEntradaEspecialidad);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombre, $idEspecialidad, $nombreEspecialidad, $idMesaEntradaEspecialidad, $idTipoEspecialista, $ultimaRecertificacion, $fechaVencimiento, $idTipoEspecialidad, $nombreTipoEspecialidad, $nombreTipoEspecialista, $idMesaEntrada, $fechaMesaEntrada, $estadoMatricular, $estadoTesoreria, $numeroExpediente, $anioExpediente, $inciso, $distrito, $fechaEspecialista);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = TRUE;
            $datos = array(
                'idColegiado' => $idColegiado,
                'matricula' => $matricula,
                'apellidoNombre' => trim($apellido).' '.trim($nombre),
                'idEspecialidad' => $idEspecialidad,
                'nombreEspecialidad' => $nombreEspecialidad,
                'idMesaEntradaEspecialidad' => $idMesaEntradaEspecialidad,
                'idTipoEspecialista' => $idTipoEspecialista,
                'ultimaRecertificacion' => $ultimaRecertificacion,
                'fechaVencimiento' => $fechaVencimiento,
                'idTipoEspecialidad' => $idTipoEspecialidad,
                'nombreTipoEspecialidad' => $nombreTipoEspecialidad,
                'nombreTipoEspecialista' => $nombreTipoEspecialista,
                'idMesaEntrada' => $idMesaEntrada,
                'fechaMesaEntrada' => $fechaMesaEntrada,
                'estadoMatricular' => $estadoMatricular,
                'estadoTesoreria' => $estadoTesoreria,
                'numeroExpediente' => $numeroExpediente,
                'anioExpediente' => $anioExpediente,
                'inciso' => $inciso,
                'distrito' => $distrito,
                'fechaEspecialista' => $fechaEspecialista
            );
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontró EXPEDIENTE ".$expediente.'/'.$anio;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando EXPEDIENTE ".$expediente.'/'.$anio;
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerMesaEntradaEspecialistaParaResolucion($tipoResolucion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    
    if ($tipoResolucion == 'E') {
        //unificamos tipo E, X, J, C
        $filtro = "IN ('E', 'X', 'J', 'C', 'U')";
    } else {
        $filtro = "= '".$tipoResolucion."'";
    }
    
    $sql = "SELECT mee.IdMesaEntradaEspecialidad, me.IdColegiado, c.Matricula, p.Apellido, p.Nombres, me.FechaIngreso, mee.IdEspecialidad, e.Especialidad, te.Nombre, mee.NumeroExpediente, mee.AnioExpediente, mee.IncisoArticulo8, mee.IdTipoEspecialista
            FROM mesaentradaespecialidad mee
            INNER JOIN mesaentrada me ON(me.IdMesaEntrada = mee.IdMesaEntrada)
            INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad)
            INNER JOIN colegiado c ON(c.Id = me.IdColegiado)
            INNER JOIN persona p ON(p.Id = c.IdPersona)
            INNER JOIN tipoespecialista te ON(te.IdTipoEspecialista = mee.IdTipoEspecialista)
            INNER JOIN tiporesolucion tr on tr.Id = te.idTipoResolucion
            LEFT JOIN resoluciondetalle rd ON(rd.IdMesaEntradaEspecialidad = mee.IdMesaEntradaEspecialidad)
            WHERE me.Estado = 'A' 
            AND rd.Id IS NULL 
            AND NOW() < date_add(me.FechaIngreso, INTERVAL 365 DAY)
            AND tr.TipoEspecialista ".$filtro."
            ORDER BY mee.AnioExpediente DESC, mee.NumeroExpediente DESC";
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('s', $tipoResolucion);
    $stmt->execute();
    $stmt->bind_result($idMesaEntradaEspecialidad, $idColegiado, $matricula, $apellido, $nombre, $fechaIngreso, $idEspecialidad, $nombreEspecialidad, $nombreTipoEspecialista, $numeroExpediente, $anioExpediente, $inciso, $idTipoEspecialista);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idMesaEntradaEspecialidad' => $idMesaEntradaEspecialidad,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'fechaIngreso' => $fechaIngreso,
                    'idEspecialidad' => $idEspecialidad,
                    'nombreEspecialidad' => $nombreEspecialidad,
                    'nombreTipoEspecialista' => $nombreTipoEspecialista,
                    'numeroExpediente' => $numeroExpediente,
                    'anioExpediente' => $anioExpediente,
                    'inciso' => $inciso,
                    'idTipoEspecialista' => $idTipoEspecialista
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
            $resultado['mensaje'] = "No existen expedientes.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando expedientes";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}

function obtenerDeudaEspecialistas($fechaVencimiento = NULL) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (isset($fechaVencimiento)) {
        $conVencimiento = "AND me.FechaIngreso >= '".$fechaVencimiento."'";
    } else {
        $conVencimiento = "";
    }
    $sql = "SELECT c.Matricula, c.Id, p.Apellido, p.Nombres, SUM(tp.Importe) AS Total, (SELECT GROUP_CONCAT(me1.IdMesaEntrada) FROM mesaentrada me1
        INNER JOIN mesaentradaespecialidad mee ON mee.IdMesaEntrada = me1.IdMesaEntrada
        LEFT JOIN tipoespecialista te ON te.IdTipoEspecialista = mee.IdTipoEspecialista
        INNER JOIN especialidad e ON e.Id = mee.IdEspecialidad
        WHERE me1.IdTipoPago > 0 AND me1.Pagado = 0 AND me1.IdColegiado = c.Id AND me1.Estado <> 'B') AS IdMesaEntrada 
        FROM mesaentrada me
        INNER JOIN colegiado c ON c.Id = me.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipopago tp ON tp.Id = me.IdTipoPago
        WHERE me.IdTipoPago > 0 AND me.Pagado = 0 AND me.Estado <> 'B' ".$conVencimiento." 
        GROUP BY c.Matricula, p.Apellido, p.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($matricula, $idColegiado, $apellido, $nombre, $total, $listaIdMesaEntrada);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'listaIdMesaEntrada' => $listaIdMesaEntrada,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'total' => $total
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
            $resultado['mensaje'] = "No existen expedientes a cobrar.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando expedientes a cobrar";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;    
}

function obtenerMesaEntradaEspecialistasAPagar($listaIdMesaEntrada) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT me.IdMesaEntrada, me.FechaIngreso, mee.NumeroExpediente, mee.AnioExpediente, e.Especialidad, mee.IdTipoEspecialista, mee.IncisoArticulo8, te.Nombre, tp.Importe 
        FROM mesaentrada me
        INNER JOIN mesaentradaespecialidad mee ON mee.IdMesaEntrada = me.IdMesaEntrada
        LEFT JOIN tipoespecialista te ON te.IdTipoEspecialista = mee.IdTipoEspecialista
        INNER JOIN especialidad e ON e.Id = mee.IdEspecialidad
        INNER JOIN tipopago tp ON tp.Id = me.IdTipoPago
        WHERE me.IdMesaEntrada IN(".$listaIdMesaEntrada.")";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $fechaIngreso, $numeroExpediente, $anioExpediente, $especialidad, $idTipoEspecialista, $incisoArticulo8, $nombreTipoEspecialidad, $importe);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idMesaEntrada' => $idMesaEntrada,
                    'fechaIngreso' => $fechaIngreso,
                    'numeroExpediente' => $numeroExpediente,
                    'anioExpediente' => $anioExpediente,
                    'especialidad' => $especialidad,
                    'idTipoEspecialista' => $idTipoEspecialista,
                    'incisoArticulo8' => $incisoArticulo8,
                    'nombreTipoEspecialidad' => $nombreTipoEspecialidad,
                    'importe' => $importe
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
            $resultado['mensaje'] = "No existen expedientes a cobrar.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando expedientes a cobrar";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerMesaEntradaPorId($idMesaEntrada) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT me.IdTipoPago, tp.Importe 
        FROM mesaentrada me
        INNER JOIN tipopago tp ON tp.Id = me.IdTipoPago
        WHERE me.IdMesaEntrada = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idMesaEntrada);
    $stmt->execute();
    $stmt->bind_result($idTipoPago, $importe);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idTipoPago' => $idTipoPago,
                    'importe' => $importe
                 );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existe idMesaEntrada ".$idMesaEntrada;
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando mesa de entrada ".$idMesaEntrada;
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEspecialidadPorIdMesaEntrada($idMesaEntrada) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT e.Id, e.Especialidad, mee.IdMesaEntradaEspecialidad, mee.IdTipoEspecialista, e.IdTipoEspecialidad, tes.Nombre, me.FechaIngreso, mee.NumeroExpediente, mee.AnioExpediente, mee.IncisoArticulo8
        FROM mesaentradaespecialidad mee
        INNER JOIN mesaentrada me ON me.IdMesaEntrada = mee.IdMesaEntrada
        INNER JOIN especialidad e ON(e.Id = mee.IdEspecialidad)
        INNER JOIN tipoespecialista tes ON(tes.IdTipoEspecialista = mee.IdTipoEspecialista)
        WHERE mee.IdMesaEntrada = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idMesaEntrada);
    $stmt->execute();
    $stmt->bind_result($idEspecialidad, $nombreEspecialidad, $idMesaEntradaEspecialidad, $idTipoEspecialista, $idTipoEspecialidad, $nombreTipoEspecialista, $fechaMesaEntrada, $numeroExpediente, $anioExpediente, $incisoArticulo8);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = TRUE;
            $datos = array(
                'idEspecialidad' => $idEspecialidad,
                'nombreEspecialidad' => $nombreEspecialidad,
                'idMesaEntradaEspecialidad' => $idMesaEntradaEspecialidad,
                'idTipoEspecialista' => $idTipoEspecialista,
                'idTipoEspecialidad' => $idTipoEspecialidad,
                'nombreTipoEspecialista' => $nombreTipoEspecialista,
                'fechaMesaEntrada' => $fechaMesaEntrada,
                'numeroExpediente' => $numeroExpediente,
                'anioExpediente' => $anioExpediente,
                'incisoArticulo8' => $incisoArticulo8
            );
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontró EXPEDIENTE ".$expediente.'/'.$anio;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando EXPEDIENTE ".$expediente.'/'.$anio;
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}
