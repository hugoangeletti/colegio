<?php
function obtenerSecretarioadhocBuscar($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Nombre FROM secretarioadhoc WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['nombre'] = $nombre;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay secretarioadhoc";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando secretarioadhoc";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSecretarioadhoPorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM secretarioadhoc WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $estado, $fechaCarga, $idUsuario);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                        'idSecretarioadhoc' => $id,
                        'nombre' => $nombre,
                        'estado' => $estado,
                        'fechaCarga' => $fechaCarga,
                        'idUsuario' => $idUsuario
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay secretarioadhoc";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando secretarioadhoc";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSecretariosadhoc(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM secretarioadhoc ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $estado, $fechaCarga, $idUsuario);
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
                        'id' => $id,
                        'nombre' => $nombre,
                        'estado' => $estado,
                        'fechaCarga' => $fechaCarga,
                        'idUsuario' => $idUsuario
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
            $resultado['mensaje'] = "No hay secretarioadhoc";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando secretarioadhoc";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerSecretarioadhocAutocompletar(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT Id, Nombre FROM secretarioadhoc WHERE Estado = 'A' ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
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
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay secretarioadhoc";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando secretarioadhoc";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function agregarSecretarioadhoc($nombre) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO secretarioadhoc (Nombre, FechaCarga, IdUsuario) 
        VALUES (?, now(), ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $nombre, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'secretarioadhoc HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR secretarioadhoc';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarSecretarioadhoc($idSecretarioadhoc, $nombre, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE secretarioadhoc 
            SET Nombre = ?, 
                Estado = ?, 
                IdUsuario = ?, 
                FechaCarga = now()
            WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssii', $nombre, $estado, $_SESSION['user_id'], $idSecretarioadhoc);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'secretarioadhoc HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR secretarioadhoc';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

