<?php
function obtenerResolucionPorId($idResolucion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT r.*, tr.Detalle, tr.TipoEspecialista
        FROM resolucion r
        INNER JOIN tiporesolucion tr ON(tr.Id = r.TipoResolucion)
        WHERE r.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idResolucion);
    $stmt->execute();
    $stmt->bind_result($id, $numero, $fecha, $detalle, $idTipoResolucion, $estado, $fechaCarga, $idUsuario, $detalleTipoResolucion, $tipoEspecialista);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idResolucion' => $id,
                    'numero' => $numero,
                    'fecha' => $fecha,
                    'detalle' => $detalle,
                    'idTipoResolucion' => $idTipoResolucion,
                    'estado' => $estado,
                    'detalleTipoResolucion' => $detalleTipoResolucion,
                    'tipoEspecialista' => $tipoEspecialista
            );
                
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro la resolucion.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando resolucion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerResolucionesPorEstado($estado, $anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT resolucion.Id, resolucion.Numero, resolucion.Fecha, resolucion.Detalle, tiporesolucion.Detalle
        FROM resolucion 
        INNER JOIN tiporesolucion ON(tiporesolucion.Id = resolucion.TipoResolucion)
        WHERE resolucion.Estado = ? AND YEAR(resolucion.Fecha) = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $estado, $anio);
    $stmt->execute();
    $stmt->bind_result($id, $numero, $fecha, $detalle, $tipo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idResolucion' => $id,
                    'numero' => $numero,
                    'fecha' => $fecha,
                    'detalle' => $detalle,
                    'detalleTipo' => $tipo
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
            $resultado['mensaje'] = "No existen resoluciones.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando resoluciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarResolucion($numero, $fecha, $detalle, $tipoResolucion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //agrego la solicitud de certificado
        $sql="INSERT INTO resolucion
            (Numero, Fecha, Detalle, TipoResolucion, FechaCarga, IdUsuario) 
            VALUE (?, ?, ?, ?, date(now()), ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sssii', $numero, $fecha, $detalle, $tipoResolucion, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $idResolucion = $conect->insert_id;
            //agrego el detalle de la resolucion
            /*
            $hayIdsMesaEntrada = 0;
            foreach ($idsMesaEntrada as $idMesaEntradaEspecialidad) {
                //con el idMesaEntradaEspecialidad vamos a buscar los datos para completar resoluciondetalle
                $resMesaEntradaEspecialidad = obtenerMesaEntradaEspecialistaPorId($idMesaEntradaEspecialidad);
                if ($resMesaEntradaEspecialidad['estado']) {
                    $mesaEntradaEspecialidad = $resMesaEntradaEspecialidad['datos'];
                    $idEspecialidad = $mesaEntradaEspecialidad['idEspecialidad'];
                    $tipoEspecialista = $mesaEntradaEspecialidad['tipoTramiteEspecialista'];
                    $idColegiado = $mesaEntradaEspecialidad['idColegiado'];
                    $inciso = $mesaEntradaEspecialidad['inciso'];
                    $sql = "INSERT INTO resoluciondetalle 
                            (IdResolucion, Especialidad, TipoEspecialista, Estado, IdColegiado, IncisoArticulo8, IdMesaEntradaEspecialidad)
                            VALUES (?, ?, ?, '0', ?, ?, ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('iisisi', $idResolucion, $idEspecialidad, $tipoEspecialista, $idColegiado, $inciso, $idMesaEntradaEspecialidad);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt) != 0) {
                        $hayIdsMesaEntrada = -1;
                    }
                    $hayIdsMesaEntrada++;
                } else {
                    $hayIdsMesaEntrada = -2;
                }
            }
            if ($hayIdsMesaEntrada > 0) {
                $resultado['estado'] = TRUE;
                $resultado['idResolucion'] = $idResolucion;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL GENERAR EL DETALLE";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
             * 
             */
            $resultado['estado'] = TRUE;
            $resultado['idResolucion'] = $idResolucion;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GUARDAR RESOLUCION";
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

function modificarResolucion($idResolucion, $numero, $fecha, $detalle){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="UPDATE resolucion 
                SET Numero = ?, Fecha = ?, Detalle = ?
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sssi', $numero, $fecha, $detalle, $idResolucion);
        $stmt->execute();
        $stmt->store_result();
        
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR RESOLUCION. ".  mysqli_stmt_error($stmt);
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

function cambiarEstadoResolucion($idResolucion, $estadoOrigen, $estadoCambio){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="UPDATE resolucion 
                SET Estado = ?
                WHERE Id = ? AND Estado = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sis', $estadoCambio, $idResolucion, $estadoOrigen);
        $stmt->execute();
        $stmt->store_result();
        
        if (mysqli_stmt_errno($stmt)==0) {
            $sql="UPDATE resoluciondetalle 
                    SET Estado = 1
                    WHERE IdResolucion = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $idResolucion);
            $stmt->execute();
            $stmt->store_result();

            if (mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR RESOLUCION. ".  mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR RESOLUCION. ".  mysqli_stmt_error($stmt);
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

function obtenerMatriculasPorIdResolucion($idResolucion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT resoluciondetalle.Id, resoluciondetalle.TipoEspecialista, tipoespecialista.Nombre, especialidad.Especialidad, colegiado.Matricula, persona.Apellido, persona.Sexo,
                persona.Nombres, resoluciondetalle.FechaAprobada, resoluciondetalle.IncisoArticulo8, resoluciondetalle.Estado,
                mesaentradaespecialidad.NumeroExpediente, mesaentradaespecialidad.AnioExpediente, '' as FechaEspecialista, '' as FechaEspecialista2, '' as FechaRecertificacion, '' as FechaVencimiento, te2.Nombre AS Origen, colegiadoespecialista.IncisoArticulo8 AS EspecialistaInciso, te2.Codigo AS CodigoEspecialista, colegiadoespecialista.HashQR, colegiadoespecialista.Id, NULL AS por_recertificacion
            FROM resoluciondetalle
            INNER JOIN colegiado ON(colegiado.Id = resoluciondetalle.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN especialidad ON(especialidad.Id = resoluciondetalle.Especialidad)
            INNER JOIN tipoespecialista ON(tipoespecialista.Codigo = resoluciondetalle.TipoEspecialista)
            LEFT JOIN mesaentradaespecialidad ON(mesaentradaespecialidad.IdMesaEntradaEspecialidad = resoluciondetalle.IdMesaEntradaEspecialidad)
            LEFT JOIN colegiadoespecialista ON(colegiadoespecialista.IdColegiado = colegiado.Id AND colegiadoespecialista.Especialidad = especialidad.Id AND colegiadoespecialista.Estado = 'A')
            LEFT JOIN tipoespecialista te2 ON(te2.IdTipoEspecialista = colegiadoespecialista.IdTipoEspecialista)
            WHERE resoluciondetalle.IdResolucion = ? AND resoluciondetalle.TipoEspecialista = 'C'
            ORDER BY colegiado.Matricula)
            
UNION ALL

(SELECT resoluciondetalle.Id, resoluciondetalle.TipoEspecialista, tipoespecialista.Nombre, especialidad.Especialidad, colegiado.Matricula, persona.Apellido, persona.Sexo,
                persona.Nombres, resoluciondetalle.FechaAprobada, resoluciondetalle.IncisoArticulo8, resoluciondetalle.Estado,
                mesaentradaespecialidad.NumeroExpediente, mesaentradaespecialidad.AnioExpediente, '' as FechaEspecialista, '' as FechaEspecialista2, '' as FechaRecertificacion, '' as FechaVencimiento, te2.Nombre AS Origen, colegiadoespecialista.IncisoArticulo8 AS EspecialistaInciso, te2.Codigo AS CodigoEspecialista, colegiadoespecialista.HashQR, colegiadoespecialista.Id, NULL AS por_recertificacion
            FROM resoluciondetalle
            INNER JOIN colegiado ON(colegiado.Id = resoluciondetalle.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN especialidad ON(especialidad.Id = resoluciondetalle.Especialidad)
            INNER JOIN tipoespecialista ON(tipoespecialista.Codigo = resoluciondetalle.TipoEspecialista)
            LEFT JOIN mesaentradaespecialidad ON(mesaentradaespecialidad.IdMesaEntradaEspecialidad = resoluciondetalle.IdMesaEntradaEspecialidad)
            LEFT JOIN colegiadoespecialista ON(colegiadoespecialista.IdColegiado = colegiado.Id AND colegiadoespecialista.Especialidad = especialidad.Id AND colegiadoespecialista.Estado = 'A')
            LEFT JOIN tipoespecialista te2 ON(te2.IdTipoEspecialista = colegiadoespecialista.IdTipoEspecialista)
            WHERE resoluciondetalle.IdResolucion = ? AND resoluciondetalle.TipoEspecialista = 'J'
            ORDER BY colegiado.Matricula)
            
UNION ALL

(SELECT resoluciondetalle.Id, resoluciondetalle.TipoEspecialista, tipoespecialista.Nombre, especialidad.Especialidad, colegiado.Matricula, persona.Apellido, persona.Sexo,
                persona.Nombres, resoluciondetalle.FechaAprobada, resoluciondetalle.IncisoArticulo8, resoluciondetalle.Estado,
                mesaentradaespecialidad.NumeroExpediente, mesaentradaespecialidad.AnioExpediente, colegiadoespecialista.FechaEspecialista, (SELECT MAX(ce2.FechaEspecialista) FROM colegiadoespecialista ce2 WHERE ce2.IdColegiado = resoluciondetalle.IdColegiado AND ce2.Especialidad = resoluciondetalle.Especialidad) AS FechaEspecialista2,
                resoluciondetalle.FechaRecertificacion, colegiadoespecialista.FechaVencimiento, te2.Nombre AS Origen, colegiadoespecialista.IncisoArticulo8 AS EspecialistaInciso, te2.Codigo AS CodigoEspecialista, colegiadoespecialista.HashQR, colegiadoespecialista.Id, cer.IdColegiadoEspecialista AS por_recertificacion
            FROM resoluciondetalle
            INNER JOIN colegiado ON(colegiado.Id = resoluciondetalle.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN especialidad ON(especialidad.Id = resoluciondetalle.Especialidad)
            LEFT JOIN tipoespecialista ON(tipoespecialista.Codigo = resoluciondetalle.TipoEspecialista)
            LEFT JOIN mesaentradaespecialidad ON(mesaentradaespecialidad.IdMesaEntradaEspecialidad = resoluciondetalle.IdMesaEntradaEspecialidad)
            LEFT JOIN colegiadoespecialista ON(colegiadoespecialista.IdResolucionDetalle = resoluciondetalle.Id AND colegiadoespecialista.Estado = 'A')
            LEFT JOIN colegiadoespecialistarecertificaciones cer ON cer.IdResolucionDetalle = resoluciondetalle.Id
            LEFT JOIN tipoespecialista te2 ON(te2.IdTipoEspecialista = colegiadoespecialista.IdTipoEspecialista)
            WHERE resoluciondetalle.IdResolucion = ? AND resoluciondetalle.TipoEspecialista NOT IN('C', 'J', 'R') AND resoluciondetalle.Estado IN(0, 1) 
            ORDER BY colegiado.Matricula)

UNION ALL

(SELECT DISTINCT resoluciondetalle.Id, resoluciondetalle.TipoEspecialista, tipoespecialista.Nombre, especialidad.Especialidad, colegiado.Matricula, persona.Apellido, persona.Sexo,
                persona.Nombres, resoluciondetalle.FechaAprobada, resoluciondetalle.IncisoArticulo8, resoluciondetalle.Estado,
                mesaentradaespecialidad.NumeroExpediente, mesaentradaespecialidad.AnioExpediente, colegiadoespecialista.FechaEspecialista, (SELECT MAX(ce2.FechaEspecialista) FROM colegiadoespecialista ce2 WHERE ce2.IdColegiado = resoluciondetalle.IdColegiado AND ce2.Especialidad = resoluciondetalle.Especialidad) AS FechaEspecialista2,
                resoluciondetalle.FechaRecertificacion, colegiadoespecialista.FechaVencimiento, te2.Nombre AS Origen, colegiadoespecialista.IncisoArticulo8 AS EspecialistaInciso, te2.Codigo AS CodigoEspecialista, colegiadoespecialista.HashQR, colegiadoespecialista.Id, cer.IdColegiadoEspecialista AS por_recertificacion
            FROM resoluciondetalle
            INNER JOIN colegiado ON(colegiado.Id = resoluciondetalle.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN especialidad ON(especialidad.Id = resoluciondetalle.Especialidad)
            LEFT JOIN tipoespecialista ON(tipoespecialista.Codigo = resoluciondetalle.TipoEspecialista)
            LEFT JOIN mesaentradaespecialidad ON(mesaentradaespecialidad.IdMesaEntradaEspecialidad = resoluciondetalle.IdMesaEntradaEspecialidad)
            LEFT JOIN colegiadoespecialistarecertificaciones cer ON cer.IdResolucionDetalle = resoluciondetalle.Id
            LEFT JOIN colegiadoespecialista ON(colegiadoespecialista.Id = cer.IdColegiadoEspecialista)
            LEFT JOIN tipoespecialista te2 ON(te2.IdTipoEspecialista = colegiadoespecialista.IdTipoEspecialista)
            WHERE resoluciondetalle.IdResolucion = ? AND resoluciondetalle.TipoEspecialista = 'R' AND resoluciondetalle.Estado IN(0, 1) 
            ORDER BY colegiado.Matricula)";
    //LEFT JOIN colegiadoespecialista ON(colegiadoespecialista.IdColegiado = resoluciondetalle.IdColegiado AND colegiadoespecialista.Especialidad = resoluciondetalle.Especialidad AND colegiadoespecialista.Estado = 'A')

    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiii', $idResolucion, $idResolucion, $idResolucion, $idResolucion);
    $stmt->execute();
    $stmt->bind_result($id, $codigoEspecialista, $tipoEspecialista, $especialidad, $matricula, $apellido, $sexo, $nombre, $fechaAprobacion, $inciso, $estado, $nroExpediente, $anioExpediente, $fechaEspecialista, $fechaEspecialista2, $fechaRecertificacion, $fechaVencimiento, $origen, $especialistaInciso, $codigoEspecialista, $hash_qr, $idColegiadoEspecialista, $idColegiadoEspecialistaPorRecertificacion);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                switch ($estado) {
                    case '0':
                        $estado = 'Enviar a Consejo';
                        break;

                    case '1':
                        $estado = 'Aprobado';
                        break;

                    case '2':
                        $estado = 'Desaprobado';
                        break;

                    case '3':
                        $estado = 'Ausente';
                        break;

                    case '4':
                        $estado = 'Dado de baja';
                        break;

                    default:
                        $estado = '';
                        break;
                }
                $row = array (
                    'idResolucionDetalle' => $id,
                    'codigoEspecialista' => $codigoEspecialista,
                    'tipoEspecialista' => $tipoEspecialista,
                    'especialidad' => $especialidad,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'sexo' => $sexo,
                    'fechaAprobacion' => $fechaAprobacion,
                    'inciso' => $inciso,
                    'estado' => $estado,
                    'nroExpediente' => $nroExpediente,
                    'anioExpediente' => $anioExpediente,
                    'fechaEspecialista' => $fechaEspecialista,
                    'fechaEspecialista2' => $fechaEspecialista2,
                    'fechaRecertificacion' => $fechaRecertificacion,
                    'fechaVencimiento' => $fechaVencimiento,
                    'origen' => $origen,
                    'especialistaInciso' => $especialistaInciso,
                    'hash_qr' => $hash_qr,
                    'idColegiadoEspecialista' => $idColegiadoEspecialista,
                    'idColegiadoEspecialistaPorRecertificacion' => $idColegiadoEspecialistaPorRecertificacion
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay Matriculas en la resolucion";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Matriculas en la resolucion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerDetalleResolucionPorId($idResolucion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');

    //primero se actulizan las fecha de aprobacion
    $sql = "UPDATE resoluciondetalle rd
        INNER JOIN resolucion r ON r.Id = rd.IdResolucion
        SET rd.FechaAprobada = r.Fecha
        WHERE r.Fecha <> rd.FechaAprobada";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $sql = "SELECT rd.Id, rd.IdColegiado, rd.Especialidad, rd.TipoEspecialista, rd.Estado, rd.FechaAprobada, rd.FechaRecertificacion, 
                    p.FechaNacimiento, te.IdTipoEspecialista, mee.Distrito, ce.Id AS IdColegiadoEspecialista, cet.Id AS IdColegiadoEspecialistaTipo,
                    rd.IncisoArticulo8
                FROM resoluciondetalle rd
                INNER JOIN colegiado c ON(c.Id = rd.IdColegiado)
                INNER JOIN persona p ON(p.Id = c.IdPersona)
                INNER JOIN tipoespecialista te ON(te.Codigo = rd.TipoEspecialista)
                LEFT JOIN mesaentradaespecialidad mee ON(mee.IdMesaEntradaEspecialidad = rd.IdMesaEntradaEspecialidad)
                LEFT JOIN colegiadoespecialista ce ON(ce.IdColegiado = c.Id AND ce.Especialidad = rd.Especialidad AND ((ce.IdTipoEspecialista = te.IdTipoEspecialista AND ce.IncisoArticulo8 = rd.IncisoArticulo8) OR rd.TipoEspecialista IN('R', 'J', 'C')))
                LEFT JOIN colegiadoespecialistatipo cet ON(cet.IdColegiadoEspecialista = ce.Id AND cet.TipoEspecialista = rd.TipoEspecialista)
                WHERE rd.IdResolucion = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idResolucion);
        $stmt->execute();
        $stmt->bind_result($idResolucionDetalle, $idColegiado, $idEspecialidad, $tipoEspecialista, $idEstadoResolucionDetalle, $fechaAprobacion, $fechaRecertificacion, $fechaNacimiento, $idTipoEspecialista, $distrito, $idColegiadoEspecialista, $idColegiadoEspecialistaTipo, $incisoArticulo8);
        $stmt->store_result();

        $resultado = array();
        if(mysqli_stmt_errno($stmt)==0)
        {
            if (mysqli_stmt_num_rows($stmt) >= 0) 
            {
                $datos = array();
                while (mysqli_stmt_fetch($stmt)) 
                {
                    $row = array (
                        'idResolucionDetalle' => $idResolucionDetalle,
                        'idColegiado' => $idColegiado,
                        'idEspecialidad' => $idEspecialidad,
                        'tipoEspecialista' => $tipoEspecialista,
                        'idEstadoResolucionDetalle' => $idEstadoResolucionDetalle,
                        'fechaAprobacion' => $fechaAprobacion,
                        'fechaRecertificacion' => $fechaRecertificacion,
                        'fechaNacimiento' => $fechaNacimiento,
                        'idTipoEspecialista' => $idTipoEspecialista,
                        'distrito' => $distrito,
                        'idColegiadoEspecialista' => $idColegiadoEspecialista,
                        'idColegiadoEspecialistaTipo' => $idColegiadoEspecialistaTipo,
                        'incisoArticulo8' => $incisoArticulo8
                     );
                    array_push($datos, $row);
                    //si la fecha de recertificacion viene vacia, le cargo la fecha de aprobacion
                    if (!isset($fechaRecertificacion)) {
                        $fechaRecertificacion = $fechaAprobacion;
                    }
                    
                }
                $resultado['estado'] = true;
                $resultado['mensaje'] = "OK";
                $resultado['datos'] = $datos;
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = true;
                $resultado['mensaje'] = "No hay Matriculas en la resolucion";
                $resultado['clase'] = 'alert alert-info'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando Matriculas en la resolucion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error validando fecha de aprobacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado;
    
}

function obtenerResolucionDetallePorId($idResolucionDetalle) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT rd.IdResolucion, rd.TipoEspecialista, rd.Especialidad, rd.Estado, rd.FechaAprobada, rd.FechaRecertificacion, rd.IncisoArticulo8, rd.IdColegiado, c.Matricula, p.Apellido, p.Nombres, e.Especialidad, te.Nombre, r.TipoResolucion, tr.Detalle, r.Numero, p.Sexo
        FROM resoluciondetalle rd
        INNER JOIN resolucion r ON r.Id = rd.IdResolucion
        INNER JOIN colegiado c ON c.Id = rd.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN especialidad e ON e.Id = rd.Especialidad
        INNER JOIN tipoespecialista te ON te.Codigo = rd.TipoEspecialista
        INNER JOIN tiporesolucion tr ON tr.Id = r.TipoResolucion
        WHERE rd.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idResolucionDetalle);
    $stmt->execute();
    $stmt->bind_result($idResolucion, $tipo, $especialidad, $estado, $fechaAprobada, $fechaRecertificacion, $inciso, $idColegiado, $matricula, $apellido, $nombre, $especialidadDetalle, $tipoEspecialista, $idTipoResolucion, $tipoResolucion, $numeroResolucion, $sexo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idResolucion' => $idResolucion,
                    'tipo' => $tipo,
                    'especialidad' => $especialidad,
                    'especialidadDetalle' => $especialidadDetalle,
                    'estado' => $estado,
                    'fechaAprobada' => $fechaAprobada,
                    'fechaRecertificacion' => $fechaRecertificacion,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'inciso' => $inciso,
                    'tipoEspecialista' => $tipoEspecialista,
                    'idTipoResolucion' => $idTipoResolucion,
                    'tipoResolucion' => $tipoResolucion,
                    'numeroResolucion' => $numeroResolucion,
                    'sexo' => $sexo
            );
                
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro la matricula en la resolucion resolucion.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando matricula de la resolucion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerTiposEspecialista(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT te.IdTipoEspecialista, te.Nombre, te.Codigo, te.IdTipoPago, te.IdTipoResolucion
            FROM tipoespecialista te ORDER BY te.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $codigo, $idTipoPago, $idTipoResolucion);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id,
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'idTipoPago' => $idTipoPago,
                    'idTipoResolucion' => $idTipoResolucion
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay Tipos de especialista";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipos de especialista";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarResolucionDetalle($idResolucion, $idMesaEntradaEspecialidad, $idEspecialidad, $tipoEspecialista, $fechaAprobada, $fechaRecertificacion, $idEspecialistaBaja, $idColegiado, $inciso) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="INSERT INTO resoluciondetalle (IdResolucion, Especialidad, TipoEspecialista, Estado, FechaAprobada, FechaRecertificacion, IdEspecialistaBaja, IdColegiado, IncisoArticulo8, IdMesaEntradaEspecialidad)
                VALUES (?, ?, ?, '0', ?, ?, ?, ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iisssiisi', $idResolucion, $idEspecialidad, $tipoEspecialista, $fechaAprobada, $fechaRecertificacion, $idEspecialistaBaja, $idColegiado, $inciso, $idMesaEntradaEspecialidad);
        $stmt->execute();
        $stmt->store_result();
        
        if (mysqli_stmt_errno($stmt)==0) {
            /*
            //agrego en colegiadoespecialista
            $fechaVencimiento = sumarRestarSobreFecha($fechaAprobada, 5, 'year', '+');
            $sql = "INSERT INTO colegiadoespecialista
                    (IdColegiado, Especialidad, IdUsuario, FechaCarga, FechaEspecialista, Colegio, FechaVencimiento, Estado, IdTipoEspecialista)
                    VALUES 
                    (?, ?, ?, date(now()), ?, 1, ?, 'A', ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iiissi', $idColegiado, $idEspecialidad, $_SESSION['user_id'], $fechaAprobada, $fechaVencimiento, $tipoEspecialista);
            $stmt->execute();
            $stmt->store_result();

            if (mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "OK";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL AGREGAR LA ESPECIALIDAD AL COLEGIADO. ".  mysqli_stmt_error($stmt);
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
             * 
             */
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR MATRICULA A LA RESOLUCION. ".  mysqli_stmt_error($stmt);
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

function realizarBajaResolucionDetalle($idResolucionDetalle) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //obtengo proxima ENTREGA
        $sql="DELETE FROM resoluciondetalle 
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idResolucionDetalle);
        $stmt->execute();
        $stmt->store_result();
        
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR RESOLUCION. ".  mysqli_stmt_error($stmt);
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

function cambiarEstadoResolucionDetalle($idResolucionDetalle, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    //obtengo proxima ENTREGA
    $sql="UPDATE resoluciondetalle 
            SET Estado = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $estado, $idResolucionDetalle);
    $stmt->execute();
    $stmt->store_result();
      
    if (mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL MODIFICAR RESOLUCION. ".  mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado;    
}

/*
function guardarQrResolucionDetalle($idResolucionDetalle, $hash_qr, $pathArchivo, $nombreArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    //obtengo proxima ENTREGA
    $sql="UPDATE resoluciondetalle 
            SET HashQR = ?,
                PathArchivo = ?,
                NombreArchivo = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssi', $hash_qr, $pathArchivo, $nombreArchivo, $idResolucionDetalle);
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

function noExisteCodigoQR($idResolucionDetalle) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT HashQR FROM resoluciondetalle
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idResolucionDetalle);
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
*/

function obtenerAnexosResolucion($idResolucion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM resolucionanexo ra
        WHERE ra.IdResolucion = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idResolucion);
    $stmt->execute();
    $stmt->bind_result($id, $idResolucion, $observacion, $fechaCarga, $idUsuario, $borrado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) >= 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idResolucionAnexo' => $id,
                    'idResolucion' => $idResolucion,
                    'observacion' => $observacion,
                    'fechaCarga' => $fechaCarga,
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro Anexo de la resolucion.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Anexo de resolucion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}

function obtenerResolucionAnexoPorId($idAnexo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM resolucionanexo ra
        WHERE ra.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAnexo);
    $stmt->execute();
    $stmt->bind_result($id, $idResolucion, $observacion, $fechaCarga, $idUsuario, $borrado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idResolucionAnexo' => $id,
                    'idResolucion' => $idResolucion,
                    'observacion' => $observacion,
                    'fechaCarga' => $fechaCarga,
                    'idUsuario' => $idUsuario,
                    'borrado' => $borrado
            );
                
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro Anexo de la resolucion.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Anexo de resolucion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarResolucionAnexo($idResolucion, $observacion, $borrado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //agrego la solicitud de certificado
        $sql="INSERT INTO resolucionanexo
            (IdResolucion, Observacion, FechaCarga, IdUsuario, Borrado) 
            VALUE (?, ?, now(), ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isii', $idResolucion, $observacion, $_SESSION['user_id'], $borrado);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $idAnexo = $conect->insert_id;
            $resultado['estado'] = TRUE;
            $resultado['idAnexo'] = $idAnexo;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GUARDAR RESOLUCION";
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

function modificarResolucionAnexo($idAnexo, $observacion, $borrado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    try {
        /* Autocommit false para la transaccion */
        $conect->autocommit(FALSE);

        //agrego la solicitud de certificado
        $sql="UPDATE resolucionanexo
            SET Observacion = ?, 
                FechaCarga = NOW(), 
                IdUsuario = ?, 
                Borrado = ?
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('siii', $observacion, $_SESSION['user_id'], $borrado, $idAnexo);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GUARDAR RESOLUCION";
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

//Constantes
define('EXAMEN_COLEGIO', 1);
define('EXCEPTUADO_ART_8', 2);
define('JERARQUIZADO', 3);
define('CONSULTOR', 4);
define('CALIFICACION_AGREGADA', 5);
define('RECERTIFICACION', 6);
define('OTRO_DISTRITO', 7);
define('RECONOCIMIENTO_NACION', 8);
define('CONVENIO_UNLP', 9);
