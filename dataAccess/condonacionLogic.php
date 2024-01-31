<?php
function obtenerCondonacionesPorEstado($estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT solicitudcondonacion.Id, solicitudcondonacion.FechaSolicitud, usuario.Usuario, 
            responsable.Nombre, solicitudcondonacion.Observacion, solicitudcondonacion.QueCondona, 
            solicitudcondonacion.IdColegiado, colegiado.Matricula, persona.Apellido, persona.Nombres,
            tipocondonacion.Nombre
            FROM solicitudcondonacion
            INNER JOIN colegiado on(colegiado.Id = solicitudcondonacion.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN tipocondonacion ON(tipocondonacion.Id = solicitudcondonacion.IdTipoCondonacion)
            INNER JOIN usuario ON(usuario.Id = solicitudcondonacion.IdUsuario)
            INNER JOIN responsable ON(responsable.Id = solicitudcondonacion.IdResponsableCondonacion)
            WHERE solicitudcondonacion.EstadoCondonacion = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($idCondonacion, $fechaSolicitud, $usuario, $responsable, $observacion, $queCondona, 
            $idColegiado, $matricula, $apellido, $nombre, $motivo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($queCondona == 'P') {
                    $queCondona = 'Cuotas de Plan de Pagos';
                } else {
                    $queCondona = 'Cuotas de colegiación';
                }
            
                $row = array (
                    'idCondonacion' => $idCondonacion,
                    'fechaSolicitud' => $fechaSolicitud,
                    'usuario' => $usuario,
                    'responsable' => $responsable,
                    'realizo' => $usuario,
                    'queCondona' => $queCondona,
                    'observacion' => $observacion,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellido.' '.$nombre,
                    'idColegiado' => $idColegiado,
                    'motivo' => $motivo
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
            $resultado['mensaje'] = "No hay Condonaciones";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Condonaciones";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerCondonacionPorId($idCondonacion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT solicitudcondonacion.Id, solicitudcondonacion.FechaSolicitud, usuario.Usuario, 
            responsable.Nombre, solicitudcondonacion.Observacion, solicitudcondonacion.QueCondona, 
            solicitudcondonacion.IdColegiado, solicitudcondonacion.EstadoCondonacion, 
            colegiado.Matricula, persona.Apellido, persona.Nombres, tipocondonacion.Nombre
            FROM solicitudcondonacion
            INNER JOIN colegiado on(colegiado.Id = solicitudcondonacion.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN tipocondonacion ON(tipocondonacion.Id = solicitudcondonacion.IdTipoCondonacion)
            INNER JOIN usuario ON(usuario.Id = solicitudcondonacion.IdUsuario)
            INNER JOIN responsable ON(responsable.Id = solicitudcondonacion.IdResponsableCondonacion)
            WHERE solicitudcondonacion.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCondonacion);
    $stmt->execute();
    $stmt->bind_result($idCondonacion, $fechaSolicitud, $usuario, $responsable, $observacion, $queCondona, 
            $idColegiado, $estado, $matricula, $apellido, $nombre, $motivo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            $row = mysqli_stmt_fetch($stmt);
            //verifico el vencimiento, sino le calculo el recargo si no esta paga
            /*
            switch ($estado) {
                case 'A':
                    $estado = 'Abierta';
                    break;

                case 'C':
                    $estado = 'Condonada';
                    break;

                case 'B':
                    $estado = 'Anulada';
                    break;
                
                default:
                    $estado = '';
                    break;
            }
            */
            
            $datos['idCondonacion'] = $idCondonacion;
            $datos['fechaSolicitud'] = $fechaSolicitud;
            $datos['realizo'] = $usuario;
            $datos['responsable'] = $responsable;
            $datos['observacion'] = $observacion;
            $datos['queCondona'] = $queCondona;
            $datos['idColegiado'] = $idColegiado;
            $datos['estado'] = $estado;
            $datos['matricula'] = $matricula;
            $datos['apellidoNombre'] = trim($apellido).' '.trim($nombre);
            $datos['motivo'] = $motivo;
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No se encontró la condonación";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando condonación";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerCondonacionDetalle($idCondonacion){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT solicitudcondonaciondetalle.Id, colegiadodeudaanual.Periodo, colegiadodeudaanualcuotas.Cuota, 
            colegiadodeudaanualcuotas.Importe, colegiadodeudaanualcuotas.FechaVencimiento, 
            planpagoscuotas.IdPlanPagos, planpagoscuotas.Cuota, planpagoscuotas.Importe, planpagoscuotas.Vencimiento
            FROM solicitudcondonaciondetalle
            LEFT JOIN colegiadodeudaanualcuotas ON(colegiadodeudaanualcuotas.Id = solicitudcondonaciondetalle.IdColegiadoDeudaCondonada)
            LEFT JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            LEFT JOIN planpagoscuotas ON(planpagoscuotas.Id = solicitudcondonaciondetalle.IdColegiadoDeudaCondonadaAnterior)
            WHERE solicitudcondonaciondetalle.IdSolicitudCondonacion = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $idCondonacion);
    $stmt->execute();
    $stmt->bind_result($idCondonacionDetalle, $periodo, $cuotaColegiacion, $importeColegiacion, 
            $vencimientoColegiacion, $idPlanPago, $cuotaPP, $importePP, $vencimientoPP);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($periodo == NULL) {
                    $queCondona = 'Cuotas de Plan de Pagos';
                    $laCuota = $idPlanPago.'-'.$cuotaPP;
                    $importe = $importePP;
                    $vencimiento = $vencimientoPP;
                } else {
                    $queCondona = 'Cuotas de colegiación';
                    $laCuota = $periodo.'-'.$cuotaColegiacion;
                    $importe = $importeColegiacion;
                    $vencimiento = $vencimientoColegiacion;
                }
            
                $row = array (
                    'idCondonacionDetalle' => $idCondonacionDetalle,
                    'queCondona' => $queCondona,
                    'laCuota' => $laCuota,
                    'importe' => $importe,
                    'vencimiento' => $vencimiento
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
            $resultado['mensaje'] = "No hay Detalle de Condonación";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Detalle de la Condonación";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerResponsables(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM responsable WHERE Estado = 'A' ORDER BY Id desc";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idResponsable, $nombre, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $idResponsable,
                    'nombre' => $nombre
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
            $resultado['mensaje'] = "No hay Responsables";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Responsables";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerTipoCondonacion(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM tipocondonacion ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idTipoCondonacion, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $idTipoCondonacion,
                    'nombre' => $nombre
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
            $resultado['mensaje'] = "No hay Motivos cargados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Motivos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function agregarColegiadoCondonacion($idColegiado, $idResponsable, $idTipoCondonacion, $observaciones, $todas, $lasCuotas, $lasCuotasPP){
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        //agregamos el plan de pagos
        $sql = "INSERT INTO solicitudcondonacion (FechaSolicitud, IdTipoCondonacion, IdUsuario, IdResponsableCondonacion,  
            EstadoCondonacion, Observacion, QueCondona, IdColegiado)
            VALUES (date(now()), ?, ?, ?, 'C', ?, 'T', ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iiisi', $idTipoCondonacion, $_SESSION['user_id'], $idResponsable, $observaciones, $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        $resultado = array(); 
        if (mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'LA CONDONACIÓN SE GENERÓ CORRECTAMENTE';
            $idCondonacion = $conect->insert_id;
            
            if ($todas == 'S') {
                //traigo las cuotas a condonar
                $resDeuda = obtenerColegiadoDeudaAnualAPagar($idColegiado);
                if ($resDeuda['estado']) {
                    $i = 0;
                    foreach ($resDeuda['datos'] as $value) {
                        $lasCuotas[$i] = $value['idColegiadoDeudaAnualCuota'];      
                        $i++;
                    }
                } else {
                    $lasCuotas = array();
                }
            }
            
            //marcamos las cuotas que se incluyen, de cuotas de colegiacion y si tiene de plan anterior
            $hayCuotas = 0;
            foreach ($lasCuotas as $value) {
                $sql = "INSERT INTO solicitudcondonaciondetalle (IdSolicitudCondonacion, IdColegiadoDeudaCondonada)
                        VALUES (?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idCondonacion, $value);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL GENERAR EL DETALLE";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                    break;
                }
                $hayCuotas++;
            }
            if ($resultado['estado'] && $hayCuotas > 0) {
                $sql = "UPDATE colegiadodeudaanualcuotas, solicitudcondonaciondetalle
                        SET colegiadodeudaanualcuotas.Estado=4
                        WHERE solicitudcondonaciondetalle.IdSolicitudCondonacion = ?
                        AND solicitudcondonaciondetalle.IdColegiadoDeudaCondonada = colegiadodeudaanualcuotas.id";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('i', $idCondonacion);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DE COLEGIACION";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }
            if ($resultado['estado']) { 
                //marcamos las cuotas de planes de pago
                if ($todas == 'S') {
                    $resDeudaPP = obtenerDeudaPlanPagosPorIdColegiado($idColegiado);
                    if ($resDeudaPP['estado']) {
                        $i = 0;
                        foreach ($resDeudaPP['datos'] as $value) {
                            $lasCuotasPP[$i] = $value['idPlanPagosCuotas'];      
                            $i++;
                        }
                    } else {
                        $lasCuotasPP = array();
                    }
                }
                $hayCuotas = 0;
                foreach ($lasCuotasPP as $value) {
                    $sql = "INSERT INTO solicitudcondonaciondetalle (IdSolicitudCondonacion, IdColegiadoDeudaCondonadaAnterior)
                            VALUES (?, ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('ii', $idCondonacion, $value);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt) != 0) {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL GENERAR EL DETALLE";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                        break;
                    }
                    $hayCuotas++;
                }
                if ($resultado['estado'] && $hayCuotas > 0) {
                    $sql = "UPDATE planpagoscuotas, solicitudcondonaciondetalle
                            SET planpagoscuotas.IdTipoEstadoCuota=4
                            WHERE solicitudcondonaciondetalle.IdSolicitudCondonacion = ?
                            AND solicitudcondonaciondetalle.IdColegiadoDeudaCondonadaAnterior = planpagoscuotas.id";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('i', $idCondonacion);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt) != 0) {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DE PLAN DE PAGOS";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                }
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL GENERAR PLAN DE PAGOS";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] .= '('.$idCondonacion.')';
            $resultado['idCondonacion'] = $idCondonacion;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
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
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL GENERAR PLAN DE PAGOS";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}

function anularCondonacion($idCondonacion){
    $conect = conectar();
    try {
        //$conect->autocommit(FALSE);
        mysqli_autocommit($conect, FALSE);
        $resultado['estado'] = TRUE;
        mysqli_set_charset( $conect, 'utf8');
        //marcamos las cuotas que se incluyen, de cuotas de colegiacion y si tiene de plan anterior
        $sql = "UPDATE colegiadodeudaanualcuotas, solicitudcondonaciondetalle
                SET colegiadodeudaanualcuotas.Estado = 1
                WHERE solicitudcondonaciondetalle.IdSolicitudCondonacion = ?
                AND solicitudcondonaciondetalle.IdColegiadoDeudaCondonada = colegiadodeudaanualcuotas.id";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idCondonacion);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) != 0) {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DE COLEGIACION";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
            
        if ($resultado['estado']) {
            $sql = "UPDATE planpagoscuotas, solicitudcondonaciondetalle
                    SET planpagoscuotas.IdTipoEstadoCuota = 1 
                    WHERE solicitudcondonaciondetalle.IdSolicitudCondonacion = ?
                    AND solicitudcondonaciondetalle.IdColegiadoDeudaCondonadaAnterior = planpagoscuotas.id";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('i', $idCondonacion);
            $stmt->execute();
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt) != 0) {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MARCAR CUOTAS DEL PLAN DE PAGOS";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }

            if ($resultado['estado']) {
                $sql = "UPDATE solicitudcondonacion SET EstadoCondonacion = 'B' WHERE Id = ?";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('i', $idCondonacion);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt) != 0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL MARCAR CONDONACION";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
                
                if ($resultado['estado']) { 
                    //borramos las cuotas
                    $sql = "DELETE FROM solicitudcondonaciondetalle WHERE IdSolicitudCondonacion = ?";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('i', $idCondonacion);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt) != 0) {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL ELIMINAR CUOTAS DE LA CONDONACION";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                }
            }
        } 

        if ($resultado['estado']) {
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
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
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ANULAR CONDONACION";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }
}