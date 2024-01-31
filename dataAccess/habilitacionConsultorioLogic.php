<?php
function obtenerHabilitacionesSolicitadas(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdColegiado, me.FechaIngreso, 
        me.Observaciones, c.Matricula, p.Apellido, p.Nombres, con.Calle, 
        con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
        mec.IdConsultorio, l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
        FROM mesaentrada as me
        INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
        INNER JOIN persona as p ON(p.Id = c.IdPersona)
        INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id AND cc.IdEstado = 1)
        INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
        INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
        INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
        INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
        WHERE me.IdTipoMesaEntrada = 4 AND con.Estado = 'A' AND me.Estado = 'A' AND mec.Estado = 'A'
        AND me.IdMesaEntrada NOT IN(SELECT ih.IdMesaEntrada
                                    FROM inspectorhabilitacion as ih
                                    WHERE ih.Estado = 'A')
        GROUP BY me.IdMesaEntrada";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $idColegiado, $fechaIngreso, $observaciones, $matricula, $apellido, $nombres, 
            $calle, $lateral, $numero, $piso, $depto, $telefono, $horarios, $idConsultorio, $localidad, $especialidad, $mail);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $domicilioCompleto = $calle;
                if (isset($numero) && strtoupper($numero) != "NR" && $numero != "") {
                    $domicilioCompleto .= " Nº ".$numero;
                }
                if (isset($lateral) && $lateral != "") {
                    $domicilioCompleto .= " e/ ".$lateral;
                }
                if (isset($piso) && strtoupper($piso) != "NR" && $piso != "") {
                    $domicilioCompleto .= " Piso ".$piso;
                }
                if (isset($depto) && strtoupper($depto) != "NR" && $depto != "") {
                    $domicilioCompleto .= " Dto. ".$depto;
                }
                $row = array (
                    'idMesaEntrada' => $idMesaEntrada,
                    'idColegiado' => $idColegiado,
                    'fechaIngreso' => $fechaIngreso,
                    'observaciones' => $observaciones,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido)." ".trim($nombres),
                    'domicilio' => $domicilioCompleto,
                    'telefono' => $telefono,
                    'horarios' => $horarios,
                    'idConsultorio' => $idConsultorio,
                    'localidad' => $localidad,
                    'especialidad' => $especialidad,
                    'mail' => $mail
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
            $resultado['mensaje'] = "No hay habilitaciones solicitadas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando habilitaciones solicitadas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerHabilitacionSolicitadaPorId($idMesaEntrada){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT me.IdMesaEntrada, mec.IdMesaEntradaConsultorio, me.IdColegiado, me.FechaIngreso, 
        me.Observaciones, c.Matricula, p.Apellido, p.Nombres, con.Calle, 
        con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
        mec.IdConsultorio, l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
        FROM mesaentrada as me
        INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
        INNER JOIN persona as p ON(p.Id = c.IdPersona)
        INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id AND cc.IdEstado = 1)
        INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
        INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
        INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
        INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
        WHERE me.IdMesaEntrada = ? AND mec.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idMesaEntrada);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $idMesaEntradaConsultorio, $idColegiado, $fechaIngreso, $observaciones, $matricula, $apellido, $nombres, 
            $calle, $lateral, $numero, $piso, $depto, $telefono, $horarios, $idConsultorio, $localidad, $especialidad, $mail);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $domicilioCompleto = $calle;
            if (isset($numero) && strtoupper($numero) != "NR" && $numero != "") {
                $domicilioCompleto .= " Nº ".$numero;
            }
            if (isset($lateral) && $lateral != "") {
                $domicilioCompleto .= " e/ ".$lateral;
            }
            if (isset($piso) && strtoupper($piso) != "NR" && $piso != "") {
                $domicilioCompleto .= " Piso ".$piso;
            }
            if (isset($depto) && strtoupper($depto) != "NR" && $depto != "") {
                $domicilioCompleto .= " Dto. ".$depto;
            }
            $datos = array (
                'idMesaEntrada' => $idMesaEntrada,
                'idMesaEntradaConsultorio' => $idMesaEntradaConsultorio,
                'idColegiado' => $idColegiado,
                'fechaIngreso' => $fechaIngreso,
                'observaciones' => $observaciones,
                'matricula' => $matricula,
                'apellidoNombre' => trim($apellido)." ".trim($nombres),
                'domicilio' => $domicilioCompleto,
                'telefono' => $telefono,
                'horarios' => $horarios,
                'idConsultorio' => $idConsultorio,
                'localidad' => $localidad,
                'especialidad' => $especialidad,
                'mail' => $mail
             );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay habilitaciones solicitadas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando habilitaciones solicitadas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerHabilitacionesAsignadasPorInspector($idInspector) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $conInspector = ' ';
    if (isset($idInspector) && $idInspector != "") {
        $conInspector = ' AND ih.IdInspector =  '.$idInspector;
    } 
    $sql = "SELECT DISTINCT(ih.IdInspectorHabilitacion), me.IdMesaEntrada, me.IdColegiado,  
            me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, 
            con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, 
            con.Observaciones as Horarios, l.Nombre as NombreLocalidad, 
            e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id and cc.IdEstado = 1)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            WHERE ih.FechaInspeccion IS NULL
            AND ih.Estado = 'A' ".$conInspector."             
            GROUP BY me.IdMesaEntrada";
    
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idInspectorHabilitacion, $idMesaEntrada, $idColegiado, $fechaIngreso, $observaciones, 
            $matricula, $apellido, $nombres, $calle, $lateral, $numero, $piso, $depto, $telefono, $horarios, 
            $localidad, $especialidad, $mail);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $domicilioCompleto = $calle;
                if (isset($numero) && strtoupper($numero) != "NR" && $numero != "") {
                    $domicilioCompleto .= " Nº ".$numero;
                }
                if (isset($lateral) && $lateral != "") {
                    $domicilioCompleto .= " e/ ".$lateral;
                }
                if (isset($piso) && strtoupper($piso) != "NR" && $piso != "") {
                    $domicilioCompleto .= " Piso ".$piso;
                }
                if (isset($depto) && strtoupper($depto) != "NR" && $depto != "") {
                    $domicilioCompleto .= " Dto. ".$depto;
                }
                $row = array (
                    'idInspectorHabilitacion' => $idInspectorHabilitacion,
                    'idMesaEntrada' => $idMesaEntrada,
                    'idColegiado' => $idColegiado,
                    'fechaIngreso' => $fechaIngreso,
                    'observaciones' => $observaciones,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido)." ".trim($nombres),
                    'domicilio' => $domicilioCompleto,
                    'telefono' => $telefono,
                    'horarios' => $horarios,
                    'localidad' => $localidad,
                    'especialidad' => $especialidad,
                    'mail' => $mail
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
            $resultado['mensaje'] = "No hay habilitaciones solicitadas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando habilitaciones solicitadas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerHabilitacionesConfirmadasPorInspector($idInspector) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $conInspector = ' ';
    if (isset($idInspector) && $idInspector != "") {
        $conInspector = ' AND ih.IdInspector =  '.$idInspector;
    } 
    $sql = "SELECT DISTINCT(ih.IdInspectorHabilitacion), me.IdMesaEntrada, me.IdColegiado,  
            me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, 
            con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, 
            con.Observaciones as Horarios, l.Nombre as NombreLocalidad, 
            e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email, ih.FechaInspeccion,
            ih.FechaHabilitacion
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id and cc.IdEstado = 1)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            WHERE ih.FechaInspeccion IS NOT NULL
            AND ih.Estado = 'A' ".$conInspector." AND ih.EstadoInspeccion <> 'B'            
            GROUP BY me.IdMesaEntrada";
    
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idInspectorHabilitacion, $idMesaEntrada, $idColegiado, $fechaIngreso, $observaciones, 
            $matricula, $apellido, $nombres, $calle, $lateral, $numero, $piso, $depto, $telefono, $horarios, 
            $localidad, $especialidad, $mail, $fechaInspeccion, $fechaHabilitacion);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $domicilioCompleto = $calle;
                if (isset($numero) && strtoupper($numero) != "NR" && $numero != "") {
                    $domicilioCompleto .= " Nº ".$numero;
                }
                if (isset($lateral) && $lateral != "") {
                    $domicilioCompleto .= " e/ ".$lateral;
                }
                if (isset($piso) && strtoupper($piso) != "NR" && $piso != "") {
                    $domicilioCompleto .= " Piso ".$piso;
                }
                if (isset($depto) && strtoupper($depto) != "NR" && $depto != "") {
                    $domicilioCompleto .= " Dto. ".$depto;
                }
                $row = array (
                    'idInspectorHabilitacion' => $idInspectorHabilitacion,
                    'idMesaEntrada' => $idMesaEntrada,
                    'idColegiado' => $idColegiado,
                    'fechaIngreso' => $fechaIngreso,
                    'observaciones' => $observaciones,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido)." ".trim($nombres),
                    'domicilio' => $domicilioCompleto,
                    'telefono' => $telefono,
                    'horarios' => $horarios,
                    'localidad' => $localidad,
                    'especialidad' => $especialidad,
                    'mail' => $mail,
                    'fechaInspeccion' => $fechaInspeccion,
                    'fechaHabilitacion' => $fechaHabilitacion
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
            $resultado['mensaje'] = "No hay habilitaciones solicitadas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando habilitaciones solicitadas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function borrarSolicitudHabilitacion($idMesaEntrada) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="UPDATE mesaentradaconsultorio 
            SET Estado = 'B', 
            FechaBaja = NOW(), 
            IdUsuarioBaja = ? 
            WHERE IdMesaEntrada = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $idMesaEntrada);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'LA SOLICITUDA DE HABILITACION HA SIDO BORRADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] .= "ERROR AL BORRAR SOLICITUD DE HABILITACION";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function obtenerInspectores($estadoInspectores){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT i.IdInspector, c.Matricula, p.Apellido, p.Nombres
        FROM inspector as i
        INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
        INNER JOIN persona as p ON(p.Id = c.IdPersona)
        WHERE i.Estado = ?
        ORDER BY p.Apellido, p.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estadoInspectores);
    $stmt->execute();
    $stmt->bind_result($idInspector, $matricula, $apellido, $nombres);
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
                    'idInspector' => $idInspector,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido)." ".trim($nombres)
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
            $resultado['mensaje'] = "No hay inspectores";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando inspectores";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerInspectorPorId($idInspector){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT i.IdColegiado, c.Matricula, p.Apellido, p.Nombres
        FROM inspector as i
        INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
        INNER JOIN persona as p ON(p.Id = c.IdPersona)
        WHERE i.IdInspector = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idInspector);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idInspector' => $idInspector,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido)." ".trim($nombres)
                 );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay inspector";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando inspector";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function existeInspector($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT IdInspector as Cantidad FROM inspector WHERE IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idInspector);
    $stmt->store_result();

    $resultado = NULL;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($idInspector > 0) {
                $resultado = $idInspector;
            } 
        }
    } 
    return $resultado;
    
}

function agregarInspector($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="INSERT INTO inspector 
            (IdColegiado, FechaCarga, IdUsuarioCarga) 
            VALUES (?, DATE(NOW()), ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $idInspector = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'EL INSPECTOR HA SIDO AGREGADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR INSPECTOR";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function borrarInspector($idInspector, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    if ($estado == 'A') {
        $estado = 'B';
    } else {
        $estado = 'A';
    }
    $sql="UPDATE inspector 
            SET Estado = ?, FechaBaja = DATE(NOW()), IdUsuarioBaja = ? WHERE IdInspector = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sii', $estado, $_SESSION['user_id'], $idInspector);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'EL INSPECTOR HA SIDO ACTUALIZADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] .= "ERROR AL ACTUALIZADO INSPECTOR";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function asignarInspectorAHabilitacion($idInspector, $idsMesaEntrada) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $datos = array();
    foreach ($idsMesaEntrada as $idMesaEntrada) {
        $sql="INSERT INTO inspectorhabilitacion 
                (IdInspector, IdMesaEntrada, FechaAsignacion, Estado) 
                VALUES (?, ?, DATE(NOW()), 'A')";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idInspector, $idMesaEntrada);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego el movimiento para hacer el seguimiento
            $idInspeccion = mysqli_stmt_insert_id($stmt);
            $row = array (
                    'idInspeccion' => $idInspeccion
                 );
            array_push($datos, $row);
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'EL INSPECTOR HA SIDO ASIGNADO';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL ASIGNAR INSPECTOR";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
    }
    if ($resultado['estado']) {
        $resultado['datos'] = $datos;
    }
    return $resultado;
}

function desasignarInspectorAHabilitacion($idInspectorHabilitacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="UPDATE inspectorhabilitacion 
        SET Estado = 'B'
        WHERE IdInspectorHabilitacion = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idInspectorHabilitacion);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'EL INSPECTOR HA SIDO DESASIGNADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] .= "ERROR AL DESASIGNAR INSPECTOR";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
    
}

function obtenerInspeccionPorId($idInspectorHabilitacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT c.Matricula as MatriculaInspector, p.Apellido as ApellidoInspector, p.Nombres as NombreInspector, 
            loc.Nombre as NombreLocalidad, con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, 
            col.Matricula as MatriculaColegiadoConsultorio, per.Apellido as ApellidoColegiadoConsultorio, 
            per.Nombres as NombreColegiadoConsultorio, ih.FechaInspeccion, ih.FechaHabilitacion, 
            ih.EstadoInspeccion, ih.MotivoNoHabilitacion
            FROM inspectorhabilitacion as ih
            INNER JOIN inspector as i ON (i.IdInspector = ih.IdInspector)
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN colegiado as col ON (col.Id = me.IdColegiado)
            INNER JOIN persona as per ON (per.Id = col.IdPersona)
            INNER JOIN localidad as loc ON (loc.Id = con.IdLocalidad)
            WHERE ih.IdInspectorHabilitacion = ?
            AND ih.Estado = 'A'";
    $res = conectar()->query($sql);
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idInspectorHabilitacion);
    $stmt->execute();
    $stmt->bind_result($matriculaInspector, $apellidoInspector, $nombreInspector, $localidad, $calle, $lateral, $numero, $piso,
            $depto, $matriculaColegiado, $apellidoColegiado, $nombreColegiado, $fechaInspeccion, $fechaHabilitacion, $estadoInspeccion, $motivoNoHabilita);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $domicilioCompleto = $calle;
            if (isset($numero) && strtoupper($numero) != "NR" && $numero != "") {
                $domicilioCompleto .= " Nº ".$numero;
            }
            if (isset($lateral) && $lateral != "") {
                $domicilioCompleto .= " e/ ".$lateral;
            }
            if (isset($piso) && strtoupper($piso) != "NR" && $piso != "") {
                $domicilioCompleto .= " Piso ".$piso;
            }
            if (isset($depto) && strtoupper($depto) != "NR" && $depto != "") {
                $domicilioCompleto .= " Dto. ".$depto;
            }
            if (isset($localidad) && $localidad != "") {
                $domicilioCompleto .= " ".$localidad;
            }
            $datos = array(
                    'matriculaInspector' => $matriculaInspector,
                    'apellidoNombreInspector' => trim($apellidoInspector).' '.$nombreInspector,
                    'matriculaColegiado' => $matriculaColegiado,
                    'apellidoNombreColegiado' => trim($apellidoColegiado)." ".trim($nombreColegiado),
                    'domicilio' => $domicilioCompleto,
                    'fechaInspeccion' => $fechaInspeccion,
                    'fechaHabilitacion' => $fechaHabilitacion,
                    'estadoInspeccion' => $estadoInspeccion,
                    'motivoNoHabilita' => $motivoNoHabilita
                 );
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay inspeccion asociada";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando inspeccion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function confirmarInspeccion($IdInspectorHabilitacion, $fechaHabilitacion, $fechaInspeccion, $observaciones, $estadoInspeccion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="UPDATE inspectorhabilitacion 
            SET FechaInspeccion = ?, FechaHabilitacion = ?, EstadoInspeccion = ?, MotivoNoHabilitacion = ? 
            WHERE IdInspectorHabilitacion = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssssi', $fechaInspeccion, $fechaHabilitacion, $estadoInspeccion, $observaciones, $IdInspectorHabilitacion);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'SE GUARDO LA INSPECCION CON EXITO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] .= "ERROR AL GUARDAR LA INSPECCION";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}