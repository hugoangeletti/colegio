<?php
function obtenerUltimaRematriculacionPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT idRematriculacionColegiado, fecha
        FROM rematriculacioncolegiado
        WHERE idColegiado = ? 
        ORDER BY idRematriculacionColegiado desc
        LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $fecha);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                    'idRematriculacionColegiado' => $id,
                    'fecha' => $fecha
                 );
            
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "El colegiado no tiene Rematriculación";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Rematriculación";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerDomicilioProfesionalPorIdRematriculacionColegiado($idRematriculacionColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT colegiadodomicilioprofesional.*, localidad.Nombre AS NombreLocalidad, entidad.Nombre AS NombreEntidad 
            FROM colegiadodomicilioprofesional 
            LEFT JOIN localidad ON(localidad.Id = colegiadodomicilioprofesional.IdLocalidad)
            LEFT JOIN entidad ON(entidad.Id = colegiadodomicilioprofesional.IdEntidad)
            WHERE colegiadodomicilioprofesional.IdRematriculacionColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRematriculacionColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idColegiado, $idEstado, $entidad, $calle, $lateral, $numero, $piso, $departamento, 
            $idLocalidad, $codigoPostal, $telefono1, $telefono2, $fechaCarga, $idUsuario, $idRematriculacionColegiado,
            $idEntidad, $idOrigenWeb, $nombreLocalidad, $nombreEntidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id,
                    'idColegiado' => $idColegiado,
                    'idEstado' => $idEstado,
                    'entidad' => $entidad,
                    'calle' => $calle, 
                    'lateral' => $lateral, 
                    'numero' => $numero, 
                    'piso' => $piso, 
                    'departamento' => $departamento, 
                    'codigoPostal' => $codigoPostal, 
                    'telefono1' => $telefono1, 
                    'telefono2' => $telefono2, 
                    'fechaCarga' => $fechaCarga, 
                    'idUsuario' => $idUsuario, 
                    'idRematriculacionColegiado' => $idRematriculacionColegiado,
                    'idEntidad' => $idEntidad, 
                    'idOrigenWeb' => $idOrigenWeb, 
                    'nombreLocalidad' => $nombreLocalidad,
                    'nombreEntidad' => $nombreEntidad
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
            $resultado['mensaje'] = "No tiene consultorios declarados";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consultorios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerActividadAsistencialPorIdRematriculacionColegiado($idRematriculacionColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT actividadasistencial.*, tipoentidad.Nombre AS NombreTipoEntidad, 
                entidad.Nombre AS NombreEntidad 
            FROM actividadasistencial 
            LEFT JOIN entidad ON(entidad.Id = actividadasistencial.IdEntidad)
            LEFT JOIN tipoentidad ON(tipoentidad.Id = entidad.IdTipoEntidad)
            WHERE actividadasistencial.IdRematriculacionColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRematriculacionColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idRematriculacionColegiado, $idColegiado, $tipoInstitucion, $idEntidad, $cargo, 
            $servicio, $fechaDesdeHasta, $fechaCarga, $nombreInstitucion, $idOrigenWeb, $tipoEntidad, $nombreEntidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($tipoInstitucion == '1') {
                    $tipoInstitucionDetalle = 'Pública';
                } else {
                    if ($tipoInstitucion == '2') {
                        $tipoInstitucionDetalle = 'Privada';
                    } else {
                        $tipoInstitucionDetalle = 'Sin informar';
                    }
                }
                $row = array (
                    'idActividadAsistencial' => $id, 
                    'idRematriculacionColegiado' => $idRematriculacionColegiado,
                    'idColegiado' => $idColegiado,
                    'tipoInstitucion' => $tipoInstitucion, 
                    'tipoInstitucionDetalle' => $tipoInstitucionDetalle, 
                    'idEntidad' => $idEntidad, 
                    'cargo' => $cargo, 
                    'servicio' => $servicio, 
                    'fechaDesdeHasta' => $fechaDesdeHasta, 
                    'fechaCarga' => $fechaCarga, 
                    'nombreInstitucion' => $nombreInstitucion, 
                    'idOrigenWeb' => $idOrigenWeb, 
                    'tipoEntidad' => $tipoEntidad, 
                    'nombreEntidad' => $nombreEntidad
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
            $resultado['mensaje'] = "No tiene actividad asistencial declarada";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando actividad asistencial";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEspecialidadesPorIdRematriculacionColegiado($idRematriculacionColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT especialidaddeclarada.IdEspecialidadDeclarada, especialidaddeclarada.IdEspecialidad, 
            especialidaddeclarada.Fecha, especialidaddeclarada.NombreEntidad, especialidaddeclarada.IdOrigenWeb,
            especialidad.Especialidad
            FROM especialidaddeclarada 
            INNER JOIN especialidad ON(especialidad.Id = especialidaddeclarada.IdEspecialidad)
            WHERE especialidaddeclarada.IdRematriculacionColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRematriculacionColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $idEspecialidad, $fecha, $nombreEntidad, $idOrigenWeb, $especialidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                
                $row = array (
                    'idEspecialidadDeclarada' => $id, 
                    'idEspecialidad' => $idEspecialidad,
                    'fecha' => $fecha,
                    'nombreEntidad' => $nombreEntidad, 
                    'idOrigenWeb' => $idOrigenWeb,
                    'especialidad' => $especialidad
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
            $resultado['mensaje'] = "No tiene especialidad declarada";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando especialidades";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}