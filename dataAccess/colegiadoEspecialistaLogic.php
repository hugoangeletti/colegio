<?php
function obtenerCantidadEspecialidadesPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT e.Especialidad, e.CodigoRes62707, e.Id, COUNT(ce.Id) AS Cantidad
        FROM colegiadoespecialista ce
        INNER JOIN especialidad e ON e.Id = ce.Especialidad
        WHERE ce.IdColegiado = ? AND ce.Estado = 'A'
        GROUP BY e.Especialidad, e.CodigoRes62707, e.Id";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($nombreEspecialidad, $codigoEspecialidad, $idEspecialidad, $cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'nombreEspecialidad' => $nombreEspecialidad, 
                    'codigoEspecialidad' => $codigoEspecialidad,
                    'idEspecialidad' => $idEspecialidad,
                    'cantidad' => $cantidad
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
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEspecialidadPorIdColegiadoIdEspecialidad($idColegiado, $idEspecialidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ce.Id, ce.FechaCarga, ce.FechaEspecialista, ce.FechaRecertificacion, ce.Colegio, ce.FechaVencimiento, te.Nombre, te.IdTipoEspecialista, ce.IncisoArticulo8
    FROM colegiadoespecialista ce
    LEFT JOIN tipoespecialista te ON te.IdTipoEspecialista = ce.IdTipoEspecialista
    WHERE ce.IdColegiado = ? AND ce.Especialidad = ? AND ce.Estado = 'A'
    ORDER BY ce.FechaEspecialista DESC
    LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idEspecialidad);
    $stmt->execute();
    $stmt->bind_result($idColegiadoEspecialista, $fechaCarga, $fechaEspecialista, $fechaRecertificacion, $distritoOrigen, $fechaVencimiento, $tipoespecialista, $idTipoEspecialista, $incisoArticulo8);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($idTipoEspecialista == RECONOCIMIENTO_NACION) {
                $distritoOrigen = "NACIÓN";
            }
            $datos = array (
                'idColegiadoEspecialista' => $idColegiadoEspecialista,
                'fechaCarga' => $fechaCarga,
                'fechaEspecialista' => $fechaEspecialista,
                'fechaRecertificacion' => $fechaRecertificacion, 
                'distritoOrigen' => $distritoOrigen,
                'fechaVencimiento' => $fechaVencimiento,
                'tipoespecialista' => $tipoespecialista, 
                'idTipoEspecialista' => $idTipoEspecialista,
                'especialistaInciso' => $incisoArticulo8,
                'origen' => $tipoespecialista
             );

            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEspecialidadesPorIdColegiado($idColegiado, $orden = NULL) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (!isset($orden)) {
        $orden = 'DESC';
    }

    $sql="SELECT ce.Id, ce.FechaCarga, ce.FechaEspecialista, ce.FechaRecertificacion, ce.Colegio, ce.FechaVencimiento, 
        te.Nombre, e.Especialidad, e.CodigoRes62707, ce.Estado, e.Id, ce.IdTipoEspecialista, ce.IncisoArticulo8, ce.FechaEspecialistaOrigen
    FROM colegiadoespecialista ce
    INNER JOIN especialidad e ON e.Id = ce.Especialidad
    LEFT JOIN tipoespecialista te ON te.IdTipoEspecialista = ce.IdTipoEspecialista
    WHERE ce.IdColegiado = ? AND ce.Estado = 'A'
    ORDER BY ce.Especialidad, ce.FechaEspecialista ".$orden;
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoEspecialista, $fechaCarga, $fechaEspecialista, $fechaRecertificacion, $distritoOrigen, $fechaVencimiento, $tipoespecialista, $nombreEspecialidad, $codigoEspecialidad, $estado, $idEspecialidad, $idTipoEspecialista, $incisoArticulo8, $fechaEspecialistaOrigen);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($idTipoEspecialista == RECONOCIMIENTO_NACION) {
                    $distritoOrigen = "NACIÓN";
                }
                if (isset($incisoArticulo8) && $incisoArticulo8 <> "") {
                    $tipoespecialista = trim($tipoespecialista.' Inc.'.$incisoArticulo8);
                }

                $row = array (
                    'idColegiadoEspecialista' => $idColegiadoEspecialista,
                    'fechaCarga' => $fechaCarga,
                    'fechaEspecialista' => $fechaEspecialista,
                    'fechaRecertificacion' => $fechaRecertificacion, 
                    'distritoOrigen' => $distritoOrigen,
                    'fechaVencimiento' => $fechaVencimiento,
                    'tipoespecialista' => $tipoespecialista, 
                    'nombreEspecialidad' => $nombreEspecialidad, 
                    'codigoEspecialidad' => $codigoEspecialidad,
                    'estado' => $estado,
                    'idEspecialidad' => $idEspecialidad,
                    'idTipoEspecialista' => $idTipoEspecialista,
                    'especialistaInciso' => $incisoArticulo8,
                    'origen' => $tipoespecialista,
                    'fechaEspecialistaOrigen' => $fechaEspecialistaOrigen
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
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEspecialidadesPorIdColegiadoVigentes($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ce.Id, ce.FechaEspecialista, e.Especialidad
    FROM colegiadoespecialista ce
    INNER JOIN especialidad e ON e.Id = ce.Especialidad
    WHERE ce.IdColegiado = ? AND ce.Estado = 'A'
    ORDER BY ce.FechaEspecialista DESC";
    //LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoEspecialista, $fechaEspecialista, $nombreEspecialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoEspecialista' => $idColegiadoEspecialista,
                    'fechaEspecialista' => $fechaEspecialista,
                    'nombreEspecialidad' => $nombreEspecialidad
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
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function especialidadesConCaducidad($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ce.FechaEspecialista, ce.FechaRecertificacion, 
        ce.Colegio, especialidad.Especialidad, ce.FechaVencimiento,
        ce.Especialidad as IdEspecilidad
        FROM colegiadoespecialista ce
        INNER JOIN especialidad on(especialidad.Id = ce.Especialidad) 
        WHERE ce.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($fechaEspecialista, $fechaRecertificacion, $colegio, $especialidad, $fechaVencimiento, $idEspecialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $caducidad = "";
                $resBaja = verBajaEspecialista($idColegiado, $idEspecialidad, $fechaEspecialista);
                if (!$resBaja['estado']) {
                    $fechaCaducidad = $fechaVencimiento;
                    iF (isset($fechaCaducidad) && $fechaCaducidad <> "0000-00-00" && substr($fechaCaducidad, 0, 4)<=$_SESSION['periodoActual']+1) {
                        if (date('Y-m-d') > $fechaCaducidad) {
                            $caducidad = 'Venció el ';
                        } else {
                            $caducidad = 'Caduca el ';
                        }
                        $caducidad .= cambiarFechaFormatoParaMostrar($fechaCaducidad);

                        $row = array (
                            'especialidad' => $especialidad,
                            'caducidad' => $caducidad
                         );
                        array_push($datos, $row);
                    }
                }
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay estados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando estados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;            
}

function verBajaEspecialista($idColegiado, $idEspecialidad, $fechaEspecialista){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select Numero, Fecha
        from resolucion
        inner join resoluciondetalle on(resolucion.id = resoluciondetalle.IdResolucion)
        where resoluciondetalle.IdColegiado = ? and resoluciondetalle.Especialidad = ?
        and resolucion.Fecha > ? and resolucion.TipoResolucion = 3";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iis', $idColegiado, $idEspecialidad, $fechaEspecialista);
    $stmt->execute();
    $stmt->bind_result($numeroResolucion, $fecha);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                'numero' => $numeroResolucion,
                'fecha' => $fecha
            );
            $resultado['datos'] = $datos;
            $resultado['estado'] = TRUE;
        } else {
            $resultado['estado'] = FALSE;
        }
    } else {
        $resultado['estado'] = FALSE;
    }
    
    return $resultado;
}

function obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, $idTipoEspecialista){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Fecha 
        FROM colegiadoespecialistatipo 
        WHERE IdColegiadoEspecialista = ?
        AND IdTipoEspecialista = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idColegiadoEspecialista, $idTipoEspecialista);
    $stmt->execute();
    $stmt->bind_result($fecha);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $resultado['estado'] = TRUE;
            $resultado['fecha'] = $fecha;
        } else {
            $resultado['estado'] = FALSE;
        }
    } else {
        $resultado['estado'] = false;
    }
    
    return $resultado;
}

function obtenerColegiadoEspecialistaPorId($idColegiadoEspecialista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ce.Id, ce.FechaCarga, ce.FechaEspecialista, ce.FechaRecertificacion, ce.Colegio, ce.FechaVencimiento, 
        te.Nombre, e.Especialidad, e.CodigoRes62707, ce.Estado, e.Id, e.IdTipoEspecialidad, te.IdTipoEspecialista, ce.IdResolucionDetalle, ce.IncisoArticulo8, ce.IdColegiado
    FROM colegiadoespecialista ce
    INNER JOIN especialidad e ON(e.Id = ce.Especialidad)
    LEFT JOIN tipoespecialista te ON(te.IdTipoEspecialista = ce.IdTipoEspecialista)
    WHERE ce.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoEspecialista);
    $stmt->execute();
    $stmt->bind_result($idColegiadoEspecialista, $fechaCarga, $fechaEspecialista, $fechaRecertificacion, $distritoOrigen, $fechaVencimiento, $tipoespecialista, $nombreEspecialidad, $codigoEspecialidad, $estado, $idEspecialidad, $idTipoEspecialidad, $idTipoEspecialista, $idResolucionDetalle, $incisoArticulo8, $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($idTipoEspecialidad < 3) {
                $tipoEspecialidad = 'Especialista';
            } else {
                $tipoEspecialidad = 'Calificación Agregada';
            }
            if ($idTipoEspecialista == RECONOCIMIENTO_NACION) {
                $distritoOrigen = "NACIÓN";
            }
            $datos = array (
                'idColegiado' => $idColegiado,
                'fechaCarga' => $fechaCarga,
                'fechaEspecialista' => $fechaEspecialista,
                'fechaRecertificacion' => $fechaRecertificacion, 
                'distritoOrigen' => $distritoOrigen,
                'fechaVencimiento' => $fechaVencimiento,
                'tipoespecialista' => $tipoespecialista, 
                'nombreEspecialidad' => $nombreEspecialidad, 
                'codigoEspecialidad' => $codigoEspecialidad,
                'estado' => $estado,
                'idEspecialidad' => $idEspecialidad,
                'idTipoEspecialista' => $idTipoEspecialista,
                'idResolucionDetalle' => $idResolucionDetalle,
                'incisoArticulo8' => $incisoArticulo8
             );

            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}

function obtenerEspecialistasConVencimientoParaNotificar($anio, $anioDesde, $rango) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadoespecialista.Id, especialidad.Especialidad, colegiadoespecialista.IdColegiado, 
            colegiado.Matricula, persona.Apellido, persona.Nombres, persona.Sexo, colegiadocontacto.CorreoElectronico, 
            colegiadoespecialista.FechaVencimiento
        FROM colegiadoespecialista
        INNER JOIN colegiado ON(colegiado.Id = colegiadoespecialista.IdColegiado)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
            AND colegiadocontacto.IdEstado = 1 
            AND colegiadocontacto.CorreoElectronico is not null 
            AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
            AND colegiadocontacto.CorreoElectronico <> '')
        INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado)
        INNER JOIN especialidad ON(especialidad.Id = colegiadoespecialista.Especialidad)
        LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = colegiadoespecialista.IdColegiado 
            AND enviomaildiariocolegiado.IdReferencia = colegiadoespecialista.Id)
        WHERE tipomovimiento.Estado = 'A'
            AND (year(colegiadoespecialista.FechaVencimiento) = ?
            OR (year(colegiadoespecialista.FechaVencimiento) >= ? 
            AND year(colegiadoespecialista.FechaVencimiento) < ?))
        ORDER BY colegiado.Matricula
        LIMIT ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iiii', $anio, $anioDesde, $anio, $rango);
        $stmt->execute();
        $stmt->bind_result($idColegiadoEspecialista, $especialidad, $idColegiado, $matricula, $apellido, $nombre, $sexo, $mail, $fechaVencimiento);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idReferencia' => $idColegiadoEspecialista,
                        'especialidad' => $especialidad,
                        'idColegiado' => $idColegiado,
                        'matricula' => $matricula,
                        'sexo' => $sexo,
                        'apellido' => $apellido,
                        'nombres' => $nombre,
                        'mail' => $mail,
                        'fechaVencimiento' => $fechaVencimiento
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
            $resultado['mensaje'] = "No hay Vencimientos a Enviar";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Titulos con Vencimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}

function agregarEspecialista($idEspecialidad, $fechaEspecialista, $colegio, $fechaVencimiento, $idColegiado, $idTipoEspecialista, $idResolucionDetalle, $incisoArticulo8) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT colegiadoespecialista (Especialidad, IdUsuario, FechaCarga, FechaEspecialista, Colegio, FechaVencimiento, Estado, IdColegiado, IdTipoEspecialista, IdResolucionDetalle, IncisoArticulo8)
          VALUES (?, ?, date(now()), ?, ?, ?, 'A', ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisssiiis', $idEspecialidad, $_SESSION['user_id'], $fechaEspecialista, $colegio, $fechaVencimiento, $idColegiado, $idTipoEspecialista, $idResolucionDetalle, $incisoArticulo8);
        $stmt->execute();
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['idColegiadoEspecialista'] = $conect->insert_id;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Titulos con Vencimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEspecialistaTipo($idColegiadoEspecialista, $idTipoEspecialista, $fechaAprobacion, $distrito, $idResolucionDetalle) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT colegiadoespecialistatipo 
            (IdColegiadoEspecialista, IdTipoEspecialista, Fecha, Colegio, IdUsuario, FechaCarga, IdResolucionDetalle)
          VALUES (?, ?, ?, 1, ?, date(now()), ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisii', $idColegiadoEspecialista, $idTipoEspecialista, $fechaAprobacion, $_SESSION['user_id'], $idResolucionDetalle);
        $stmt->execute();
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Titulos con Vencimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarRecertificacion($idColegiadoEspecialista, $fechaRecertificacion, $fechaVencimiento, $idResolucionDetalle) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        $sql="UPDATE colegiadoespecialista 
            SET FechaRecertificacion = ?, 
                FechaVencimiento = ?
            WHERE Id = ?";
        //echo $sql;
        //echo '<br>'.$fechaRecertificacion.' - '.$fechaVencimiento.' - '.$idColegiadoEspecialista;
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssi', $fechaRecertificacion, $fechaVencimiento, $idColegiadoEspecialista);
        $stmt->execute();
        $stmt->store_result();

        if(mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $sql="INSERT INTO colegiadoespecialistarecertificaciones
                (IdColegiadoEspecialista, IdResolucionDetalle) 
                VALUE (?, ?)";
            //echo $sql;
            //echo '<br>'.$idResolucionDetalle.' - '.$idColegiadoEspecialista;
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idColegiadoEspecialista, $idResolucionDetalle);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt)<>0) {
                $resultado['estado'] = false;
                $resultado['mensaje'] = "Error insertando recertificaciones";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error al actualizar la recertificacion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        if ($resultado['estado']) {
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

function existeEspecialista($idColegiado, $idEspecialidad, $tipoEspecialista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if ($tipoEspecialista == "Especialista") {
        $sql="SELECT COUNT(ce.Id) AS Cantidad
            FROM colegiadoespecialista ce
            WHERE ce.IdColegiado = ? AND ce.Especialidad = ? AND ce.Estado = 'A'";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idColegiado, $idEspecialidad);
    } else {
        
    }
   
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            if ($cantidad > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function obtenerColegiadoEspecialistaPorColegiadoEspecialidad($idColegiado, $idEspecialidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ce.Id, ce.FechaCarga, ce.FechaEspecialista, 
        ce.FechaRecertificacion, ce.Colegio, ce.FechaVencimiento, 
        tipoespecialista.Nombre, especialidad.Especialidad, especialidad.CodigoRes62707, ce.Estado, 
        especialidad.Id
    FROM colegiadoespecialista ce
    INNER JOIN especialidad e ON e.Id = ce.Especialidad
    LEFT JOIN tipoespecialista te ON te.IdTipoEspecialista = ce.IdTipoEspecialista
    WHERE ce.IdColegiado = ? AND ce.Especialidad = ? AND ce.Estado = 'A'
    ORDER BY ce.FechaEspecialista DESC";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idEspecialidad);
    $stmt->execute();
    $stmt->bind_result($idColegiadoEspecialista, $fechaCarga, $fechaEspecialista, $fechaRecertificacion, $distritoOrigen, $fechaVencimiento, $nombreTipoEspecialista, $nombreEspecialidad, $codigoEspecialidad, $estado, $idEspecialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idColegiadoEspecialista' => $idColegiadoEspecialista,
                    'fechaCarga' => $fechaCarga,
                    'fechaEspecialista' => $fechaEspecialista,
                    'fechaRecertificacion' => $fechaRecertificacion, 
                    'distritoOrigen' => $distritoOrigen,
                    'fechaVencimiento' => $fechaVencimiento,
                    'nombreTipoEspecialista' => $nombreTipoEspecialista, 
                    'nombreEspecialidad' => $nombreEspecialidad, 
                    'codigoEspecialidad' => $codigoEspecialidad,
                    'estado' => $estado,
                    'idEspecialidad' => $idEspecialidad,
                    'idColegiado' => $idColegiado
                 );
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "El colegiado no tiene especialidades.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function guardarQrColegiadoEspecialista($idColegiadoEspecialista, $hash_qr, $pathArchivo, $nombreArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    //obtengo proxima ENTREGA
    $sql="UPDATE colegiadoespecialista 
            SET HashQR = ?,
                PathArchivo = ?,
                NombreArchivo = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssi', $hash_qr, $pathArchivo, $nombreArchivo, $idColegiadoEspecialista);
    $stmt->execute();
    $stmt->store_result();
      
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR CODIGO QR. ".  mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado; 
}

function noExisteCodigoQR($idColegiadoEspecialista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT HashQR FROM colegiadoespecialista
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoEspecialista);
    $stmt->execute();
    $stmt->bind_result($hash_qr);
    $stmt->store_result();

    $resultado = TRUE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) >= 0) {
            $row = mysqli_stmt_fetch($stmt);
            if (isset($hash_qr)) {
                $resultado = FALSE;
            }
        }
    }
    return $resultado;
}

function editarColegiadoEspecialista($idColegiadoEspecialista, $idTipoEspecialista, $inciso) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    //obtengo proxima ENTREGA
    $sql="UPDATE colegiadoespecialista ce
        LEFT JOIN resoluciondetalle rd ON rd.Id = ce.IdResolucionDetalle
        LEFT JOIN mesaentradaespecialidad mee ON mee.IdMesaEntradaEspecialidad = rd.IdMesaEntradaEspecialidad
        SET ce.IncisoArticulo8 = ?,
            rd.IncisoArticulo8 = ?,
            mee.IncisoArticulo8 = ?,
            ce.IdTipoEspecialista = ?,
            rd.IdTipoEspecialista = ?,
            mee.IdTipoEspecialista = ?
        WHERE ce.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssiiii', $inciso, $inciso, $inciso, $idTipoEspecialista, $idTipoEspecialista, $idTipoEspecialista, $idColegiadoEspecialista);
    $stmt->execute();
    $stmt->store_result();
      
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR CODIGO QR. ".  mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado; 
}