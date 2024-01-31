<?php
function obtenerApps() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT IdApp, Nombre FROM app WHERE Tipo = 'P' and Estado = 'A' ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => $nombre
            );
            array_push($datos, $row);
        }
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando app";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerRoles() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT approl.Id, approl.Nombre, app.Nombre 
        FROM approl 
        INNER JOIN app ON(app.IdApp = approl.IdApp)
        WHERE app.Estado = 'A' AND approl.Estado = 'A'
        ORDER BY app.Nombre, approl.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombreRol, $nombreApp);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => trim($nombreApp).' - '.trim($nombreRol)
            );
            array_push($datos, $row);
        }
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando approl";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerRolPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select Id, Nombre from app where Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'id' => $id,
                'nombre' => $nombre
                );

            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro la app";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando app";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerAppActividadPorIdApp($idApp) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select distinct appactividad.Id, appactividad.Detalle
        from appactividad
        inner join appactividadrol on(appactividadrol.IdAppActividad = appactividad.Id)
        inner join usuarioappactividadrol on(usuarioappactividadrol.IdAppActividadRol = appactividadrol.Id)
        where appactividad.IdApp = ? and appactividad.IdEstado = 1 and usuarioappactividadrol.IdUsuario = 1
        and appactividadrol.Estado = 'A' and appactividadrol.EnMenu = 'S'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idApp);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => $nombre
            );
            array_push($datos, $row);
        }
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando app";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

