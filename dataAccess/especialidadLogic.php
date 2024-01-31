<?php
function obtenerEspecialidades() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM especialidad WHERE Estado = 'A' ORDER BY Especialidad";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $codigo, $codigoRes, $idTipoEspecialidad, $estado, $idPadre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idEspecialidad' => $id,
                    'nombreEspecialidad' => $nombre,
                    'codigoResolucion' => $codigoRes,
                    'idTipoEspecialidad' => $idTipoEspecialidad, 
                    'estado' => $estado,
                    'idPadre' => $idPadre
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
            $resultado['mensaje'] = "No se encontraron especialidades.";
            $resultado['clase'] = 'alert alert-info'; 
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

function obtenerEspecialidadesParaExpedientes($idColegiado, $idTipoEspecialidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (isset($idTipoEspecialidad) && $idTipoEspecialidad == CONVENIO_UNLP) {
        $sql = "SELECT e.Id, e.Especialidad, e.Codigo, e.CodigoRes62707, e.IdTipoEspecialidad, e.Estado, e.IdPadre 
            FROM especialidad e
            WHERE e.Estado = 'A' 
            ORDER BY e.Especialidad";
    } else {
        //lo sacamos por el momento para cargar los que piden por UNLP y ya tinen la especialidad por el colegio
        $sql = "SELECT e.Id, e.Especialidad, e.Codigo, e.CodigoRes62707, e.IdTipoEspecialidad, e.Estado, e.IdPadre 
            FROM especialidad e
            LEFT JOIN colegiadoespecialista ce ON (ce.Especialidad = e.Id AND ce.IdColegiado = ?)
            WHERE e.Estado = 'A' 
            AND (ce.Id IS NULL OR ce.IdTipoEspecialista = 8)
            ORDER BY e.Especialidad";
    }
    $stmt = $conect->prepare($sql);
    if (isset($idColegiado) && $idColegiado > 0) {
        $stmt->bind_param('i', $idColegiado);
    }
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $codigo, $codigoRes, $idTipoEspecialidad, $estado, $idPadre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idEspecialidad' => $id,
                    'nombreEspecialidad' => $nombre,
                    'codigoResolucion' => $codigoRes,
                    'idTipoEspecialidad' => $idTipoEspecialidad, 
                    'estado' => $estado,
                    'idPadre' => $idPadre
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
            $resultado['mensaje'] = "No se encontraron especialidades.";
            $resultado['clase'] = 'alert alert-info'; 
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

function obtenerEspecialidadesAutocompletar() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "(SELECT Id, Especialidad AS Nombre
            FROM especialidad
            WHERE Estado = 'A' AND IdTipoEspecialidad <> 3)

            UNION

            (SELECT e.Id, concat(e.Especialidad, ' - ',especialidad.Especialidad) AS Nombre
            FROM especialidad
            INNER JOIN especialidad e ON(e.IdPadre = especialidad.Id)
            WHERE e.Estado = 'A' AND e.IdTipoEspecialidad = 3)

            ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $id,
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontraron especialidades.";
            $resultado['clase'] = 'alert alert-info'; 
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

function obtenerCalificacionesAgregadasSegunEspecialidadOtorgada($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT e.*, ep.Especialidad
        FROM especialidad e 
        INNER JOIN colegiadoespecialista ce ON(ce.Especialidad = e.IdPadre)
        INNER JOIN especialidad ep ON(ep.Id = e.IdPadre)
        LEFT JOIN colegiadoespecialista ce1 ON (ce1.Especialidad = e.Id AND ce1.IdColegiado = ?)
        WHERE ce.IdColegiado = ? AND e.Estado = 'A' AND e.IdTipoEspecialidad = 3
        AND ce.IdTipoEspecialista <> 8 AND ce1.Id IS NULL
        ORDER BY e.Especialidad";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $codigo, $codigoRes, $idTipoEspecialidad, $estado, $idPadre, $especialidadPadre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idEspecialidad' => $id,
                    'nombreEspecialidad' => $nombre,
                    'codigoResolucion' => $codigoRes,
                    'idTipoEspecialidad' => $idTipoEspecialidad, 
                    'estado' => $estado,
                    'idPadre' => $idPadre,
                    'nombreEspecialidadPadre' => $especialidadPadre
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
            $resultado['mensaje'] = "No se encontraron especialidades.";
            $resultado['clase'] = 'alert alert-info'; 
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