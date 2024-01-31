<?php
function obtenerExpedientePorEstado($estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ee.Id, ee.IdColegiado, ee.NumeroExpediente, ee.Caratula, ee.Observaciones, ee.IdEticaEstado, ee.IdUsuario, ee.Fecha, c.Matricula, p.Apellido, p.Nombres, es.Nombre, ee.Denunciante, ee.FechaReunionConsejo, (SELECT GROUP_CONCAT(c1.Matricula, '-', p1.Apellido, ' ', p1.Nombres, ' ') FROM eticaexpedientedenunciados eed 
            LEFT JOIN colegiado c1 ON c1.Id = eed.IdColegiado
            LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
            WHERE eed.IdEticaExpediente = ee.Id AND eed.Borrado = 0) AS OtrosDenunciados
         FROM eticaexpediente ee
         INNER JOIN colegiado c ON c.Id = ee.IdColegiado
         INNER JOIN persona p ON p.Id = c.IdPersona
         INNER JOIN eticaestado es ON es.Id = ee.IdEticaEstado
         where ee.Estado = ? AND Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();

    $stmt->bind_result($idEticaExpediente, $idColegiado, $nroExpediente, $caratula, $observaciones, $idEticaEstado, $idUsuario, $fecha, $matricula, $apellido, $nombres, $eticaEstado, $denunciante, $fechaReunionConsejo, $otrosDenunciados);

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
                    'idEticaExpediente' => $idEticaExpediente,
                    'idColegiado' => $idColegiado,
                    'nroExpediente' => $nroExpediente,
                    'caratula' => $caratula,
                    'observaciones' => $observaciones,
                    'idEticaEstado' => $idEticaEstado,
                    'idUsuario' => $idUsuario,
                    'fecha' => $fecha,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'eticaEstado' => $eticaEstado,
                    'denunciante' => $denunciante,
                    'fechaReunionConsejo' => $fechaReunionConsejo,
                    'otrosDenunciados' => $otrosDenunciados
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay expedientes";
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

function obtenerExpedientePorEstadoUsuario($estado, $idUsuario){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ee.Id, ee.IdColegiado, ee.NumeroExpediente, ee.Caratula, ee.Observaciones, ee.IdEticaEstado, ee.IdUsuario, ee.Fecha, c.Matricula, p.Apellido, p.Nombres, es.Nombre, ee.Denunciante, ee.FechaReunionConsejo, (SELECT GROUP_CONCAT(c1.Matricula, '-', p1.Apellido, ' ', p1.Nombres, ' ') FROM eticaexpedientedenunciados eed 
            LEFT JOIN colegiado c1 ON c1.Id = eed.IdColegiado
            LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
            WHERE eed.IdEticaExpediente = ee.Id AND eed.Borrado = 0) AS OtrosDenunciados
         FROM eticaexpediente ee
         INNER JOIN colegiado c ON c.Id = ee.IdColegiado
         INNER JOIN persona p ON p.Id = c.IdPersona
         INNER JOIN eticaestado es ON es.Id = ee.IdEticaEstado
         WHERE e.Estado = ? AND ee.IdSumarianteTitular = ? AND Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $estado, $idUsuario);
    $stmt->execute();

    $stmt->bind_result($idEticaExpediente, $idColegiado, $nroExpediente, $caratula, $observaciones, $idEticaEstado, $idUsuario, $fecha, $matricula, $apellido, $nombres, $eticaEstado, $denunciante, $fechaReunionConsejo, $otrosDenunciados);

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
                    'idEticaExpediente' => $idEticaExpediente,
                    'idColegiado' => $idColegiado,
                    'nroExpediente' => $nroExpediente,
                    'caratula' => $caratula,
                    'observaciones' => $observaciones,
                    'idEticaEstado' => $idEticaEstado,
                    'idUsuario' => $idUsuario,
                    'fecha' => $fecha,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'eticaEstado' => $eticaEstado,
                    'denunciante' => $denunciante,
                    'fechaReunionConsejo' => $fechaReunionConsejo,
                    'otrosDenunciados' => $otrosDenunciados
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay expedientes";
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


function obtenerEticaExpedientePorId($id){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select eticaexpediente.Id, eticaexpediente.IdColegiado, eticaexpediente.NumeroExpediente, eticaexpediente.Caratula,
            eticaexpediente.Observaciones, eticaexpediente.IdEticaEstado, eticaexpediente.IdUsuario, eticaexpediente.Fecha,
            colegiado.Matricula, persona.Apellido, persona.Nombres, eticaexpediente.IdSumarianteTitular, 
            eticaexpediente.IdSumarianteSuplente, eticaexpediente.IdSecretarioadhoc, eticaexpediente.denunciante, 
            eticaexpediente.FechaReunionConsejo, eticaexpediente.Estado
         from eticaexpediente
         inner join colegiado on(colegiado.Id = eticaexpediente.IdColegiado)
         inner join persona on(persona.Id = colegiado.IdPersona)
         where eticaexpediente.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($idEticaExpediente, $idColegiado, $nroExpediente, $caratula, $observaciones, $idEticaEstado, $idUsuario, $fecha, $matricula, $apellido, $nombres, $idSumarianteTitular, $idSumarianteSuplente, $idSecretarioadhoc, $denunciante, $fechaReunionConsejo, $estadoExpediente);

    $stmt->store_result();
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $datos = array(
                        'idEticaExpediente' => $idEticaExpediente,
                        'idColegiado' => $idColegiado,
                        'nroExpediente' => $nroExpediente,
                        'caratula' => $caratula,
                        'observaciones' => $observaciones,
                        'idEticaEstado' => $idEticaEstado,
                        'idUsuario' => $idUsuario,
                        'fecha' => $fecha,
                        'matricula' => $matricula,
                        'apellido' => $apellido,
                        'nombres' => $nombres,
                        'idSumarianteTitular' => $idSumarianteTitular,
                        'idSumarianteSuplente' => $idSumarianteSuplente,
                        'idSecretarioadhoc' => $idSecretarioadhoc,
                        'denunciante' => $denunciante,
                        'fechaReunionConsejo' => $fechaReunionConsejo,
                        'estadoExpediente' => $estadoExpediente
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay expedientes";
            $resultado['clase'] = 'alert alert-info'; 
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

function agregarEticaExpediente($idColegiado, $caratula, $nroExpediente, $observaciones, $idSumarianteTitular, $idSumarianteSuplente, $estado, $idSecretarioadhoc, $denunciante, $fechaReunionConsejo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eticaexpediente 
        (IdColegiado, Denunciante, NumeroExpediente, FechaReunionConsejo, Caratula, Observaciones, IdUsuario, Fecha, 
        IdSumarianteTitular, IdSumarianteSuplente, Estado, IdSecretarioadhoc) 
        VALUES (?, ?, ?, ?, ?, ?, ?, now(), ?, ?, ?, ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssssiiisi', $idColegiado, $denunciante, $nroExpediente, $fechaReunionConsejo, $caratula, 
            $observaciones, $_SESSION['user_id'], $idSumarianteTitular, $idSumarianteSuplente, $estado, 
            $idSecretarioadhoc);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idEticaExpediente = mysqli_stmt_insert_id($stmt);
        $sql="INSERT INTO eticaexpedientemovimiento 
            (IdEticaExpediente, IdEticaEstado, Fecha, IdUsuario) 
            VALUES (?, 1, now(), ?)";

        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idEticaExpediente, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        
        $estadoConsulta = TRUE;
        $mensaje = 'Expediente HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Expediente';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarEticaExpediente($idEticaExpediente, $idColegiado, $caratula, $nroExpediente, $observaciones, $idSumarianteTitular, $idSumarianteSuplente, $estado, $idSecretarioadhoc, $denunciante, $fechaReunionConsejo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eticaexpediente 
            SET IdColegiado = ?,
                NumeroExpediente = ?,
                Caratula = ?,
                Observaciones = ?,
                IdUsuario = ?,
                Fecha = now(),
                IdSumarianteTitular = ?, 
                IdSumarianteSuplente = ?,
                Estado = ?,
                IdSecretarioadhoc = ?,
                Denunciante = ?, 
                FechaReunionConsejo = ?
            WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssiiisissi', $idColegiado, $nroExpediente, $caratula, $observaciones, $_SESSION['user_id'], $idSumarianteTitular, $idSumarianteSuplente, $estado, $idSecretarioadhoc, $denunciante, $fechaReunionConsejo, $idEticaExpediente);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Expediente HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Expediente';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarEticaExpediente($idEticaExpediente){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eticaexpediente SET 
                Estado = 'B'
                WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpediente);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Expediente HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Expediente';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function obtenerExpedientePorSumarianteTipo($idSumariante, $tipoSumariante){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $filtro = "";
    if ($tipoSumariante == "T") {
        $filtro = "where eticaexpediente.IdSumarianteTitular = ".$idSumariante;
    } elseif ($tipoSumariante == "S") {
        $filtro = "where eticaexpediente.IdSumarianteSuplente = ".$idSumariante;
    }
    
    $sql="select eticaexpediente.Id, eticaexpediente.IdColegiado, eticaexpediente.NumeroExpediente, eticaexpediente.Caratula,
            eticaexpediente.Observaciones, eticaexpediente.IdEticaEstado, eticaexpediente.IdUsuario, eticaexpediente.Fecha,
            colegiado.Matricula, persona.Apellido, persona.Nombres, eticaestado.Nombre, eticaexpediente.Denunciante, 
            eticaexpediente.FechaReunionConsejo
         from eticaexpediente
         inner join colegiado on(colegiado.Id = eticaexpediente.IdColegiado)
         inner join persona on(persona.Id = colegiado.IdPersona)
         inner join eticaestado on(eticaestado.Id = eticaexpediente.IdEticaEstado) ".$filtro;
    $stmt = $conect->prepare($sql);
    $stmt->execute();

    $stmt->bind_result($idEticaExpediente, $idColegiado, $nroExpediente, $caratula, $observaciones, $idEticaEstado, $idUsuario, $fecha, $matricula, $apellido, $nombres, $eticaEstado, $denunciante, $fechaReunionConsejo);

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
                    'idEticaExpediente' => $idEticaExpediente,
                    'idColegiado' => $idColegiado,
                    'nroExpediente' => $nroExpediente,
                    'caratula' => $caratula,
                    'observaciones' => $observaciones,
                    'idEticaEstado' => $idEticaEstado,
                    'idUsuario' => $idUsuario,
                    'fecha' => $fecha,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'eticaEstado' => $eticaEstado,
                    'denunciante' => $denunciante,
                    'fechaReunionConsejo' => $fechaReunionConsejo
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay expedientes";
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

function obtenerOtrosDenunciadosPorIdEticaExpediente($idEticaExpediente){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eed.Id, eed.IdColegiado, c.Matricula, p.Apellido, p.Nombres
        FROM eticaexpedientedenunciados eed
        INNER JOIN colegiado c ON c.Id = eed.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        WHERE eed.IdEticaExpediente = ? AND eed.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpediente);
    $stmt->execute();

    $stmt->bind_result($idEticaExpedienteOtroDenunciado, $idColegiado, $matricula, $apellido, $nombre);

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
                    'idEticaExpedienteOtroDenunciado' => $idEticaExpedienteOtroDenunciado,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay otros denunciados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando denunciados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarOtrosDenunciados($idEticaExpediente, $idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    $sql="INSERT INTO eticaexpedientedenunciados 
        (IdEticaExpediente, IdColegiado, IdUsuario, FechaCarga) 
        VALUES (?, ?, ?, NOW())";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iii', $idEticaExpediente, $idColegiado, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando denunciados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function borrarOtrosDenunciados($idEticaExpedienteOtroDenunciado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    $sql="UPDATE eticaexpedientedenunciados 
        SET Borrado = 1, 
            IdUsuario = ?,
            FechaCarga = NOW()
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $idEticaExpedienteOtroDenunciado);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->errno == 0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error borrando denunciados";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerOtroDenunciadoPorId($idEticaExpedienteOtroDenunciado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT *
         FROM eticaexpedientedenunciados
         where Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEticaExpedienteOtroDenunciado);
    $stmt->execute();

    $stmt->bind_result($id, $idEticaExpediente, $idColegiado, $fechaCarga, $idUsuario, $borrado);

    $stmt->store_result();
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $datos = array(
                        'idEticaExpedienteOtroDenunciado' => $id,
                        'idEticaExpediente' => $idEticaExpediente,
                        'idColegiado' => $idColegiado
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay expedientes";
            $resultado['clase'] = 'alert alert-info'; 
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

function obtenerExpedientePorIdColegiado($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT 'DENUNCIADO' AS RolDenuncia, ee.Id, ee.IdColegiado, ee.NumeroExpediente, ee.Caratula, ee.Observaciones, ee.IdEticaEstado, ee.IdUsuario, ee.Fecha, c.Matricula, p.Apellido, p.Nombres, es.Nombre, ee.Denunciante, ee.FechaReunionConsejo, (SELECT GROUP_CONCAT(c1.Matricula, '-', p1.Apellido, ' ', p1.Nombres, ' ') FROM eticaexpedientedenunciados eed 
            LEFT JOIN colegiado c1 ON c1.Id = eed.IdColegiado
            LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
            WHERE eed.IdEticaExpediente = ee.Id AND eed.Borrado = 0) AS OtrosDenunciados
         FROM eticaexpediente ee
         INNER JOIN colegiado c ON c.Id = ee.IdColegiado
         INNER JOIN persona p ON p.Id = c.IdPersona
         INNER JOIN eticaestado es ON es.Id = ee.IdEticaEstado
         WHERE ee.IdColegiado = ?

         UNION ALL

        SELECT 'OTRO DENUNCIADO' AS RolDenuncia, ee.Id, ee.IdColegiado, ee.NumeroExpediente, ee.Caratula, ee.Observaciones, ee.IdEticaEstado, ee.IdUsuario, ee.Fecha, c.Matricula, p.Apellido, p.Nombres, es.Nombre, ee.Denunciante, ee.FechaReunionConsejo, (SELECT GROUP_CONCAT(c1.Matricula, '-', p1.Apellido, ' ', p1.Nombres, ' ') FROM eticaexpedientedenunciados eed 
            LEFT JOIN colegiado c1 ON c1.Id = eed.IdColegiado
            LEFT JOIN persona p1 ON p1.Id = c1.IdPersona
            WHERE eed.IdEticaExpediente = ee.Id AND eed.Borrado = 0) AS OtrosDenunciados
         FROM eticaexpedientedenunciados eed
            INNER JOIN eticaexpediente ee  ON eed.IdEticaExpediente = ee.Id
         INNER JOIN colegiado c ON c.Id = ee.IdColegiado
         INNER JOIN persona p ON p.Id = c.IdPersona
         INNER JOIN eticaestado es ON es.Id = ee.IdEticaEstado
         WHERE eed.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idColegiado);
    $stmt->execute();

    $stmt->bind_result($rolDenuncia, $idEticaExpediente, $idColegiado, $nroExpediente, $caratula, $observaciones, $idEticaEstado, $idUsuario, $fecha, $matricula, $apellido, $nombres, $eticaEstado, $denunciante, $fechaReunionConsejo, $otrosDenunciados);

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
                    'rolDenuncia' => $rolDenuncia,
                    'idEticaExpediente' => $idEticaExpediente,
                    'idColegiado' => $idColegiado,
                    'nroExpediente' => $nroExpediente,
                    'caratula' => $caratula,
                    'observaciones' => $observaciones,
                    'idEticaEstado' => $idEticaEstado,
                    'idUsuario' => $idUsuario,
                    'fecha' => $fecha,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'eticaEstado' => $eticaEstado,
                    'denunciante' => $denunciante,
                    'fechaReunionConsejo' => $fechaReunionConsejo,
                    'otrosDenunciados' => $otrosDenunciados
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay expedientes";
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
