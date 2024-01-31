<?php
function obtenerRetiroDocumentacionPorEstado($estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT rd.Id, rd.FechaCarga, rd.IdTipoDocumentacionRetiro, rd.Observacion, rd.IdUsuarioCarga, rd.FechaRetiro, rd.IdUsuarioRetiro, c.Matricula, p.Apellido, p.Nombres, c.Id, tdr.Nombre, p.NumeroDocumento
            FROM retirodocumentacion rd
            Inner join colegiado c on(c.Id = rd.IdColegiado)
            inner join persona p on(p.Id = c.IdPersona)
            inner join tipodocumentacionretiro tdr on tdr.Id = rd.IdTipoDocumentacionRetiro
            WHERE rd.Estado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($idRetiro, $fechaCarga, $idTipoDocumentacionRetiro, $observacion, $idUsuarioCarga, $fechaRetiro, $idUsuarioRetiro, $matricula, $apellido, $nombre, $idColegiado, $tipoDocumentacionRetiro, $numeroDocumento);
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
                    'idRetiro' => $idRetiro,
                    'fechaCarga' => $fechaCarga,
                    'idTipoDocumentacionRetiro' => $idTipoDocumentacionRetiro,
                    'observacion' => $observacion,
                    'idUsuarioCarga' => $idUsuarioCarga,
                    'fechaRetiro' => $fechaRetiro,
                    'idUsuarioRetiro' => $idUsuarioRetiro,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellido.' '.$nombre,
                    'idColegiado' => $idColegiado,
                    'tipoDocumentacionRetiro' => $tipoDocumentacionRetiro,
                    'numeroDocumento' => $numeroDocumento
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
            $resultado['mensaje'] = "No hay Retiros Documentacion";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Retiros Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerRetiroDocumentacionPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT rd.Id, rd.FechaCarga, rd.IdTipoDocumentacionRetiro, rd.Observacion, rd.IdUsuarioCarga, rd.FechaRetiro, rd.IdUsuarioRetiro, rd.Estado, c.Matricula, p.Apellido, p.Nombres, c.Id, tdr.Nombre, p.NumeroDocumento
            FROM retirodocumentacion rd
            Inner join colegiado c on(c.Id = rd.IdColegiado)
            inner join persona p on(p.Id = c.IdPersona)
            inner join tipodocumentacionretiro tdr on tdr.Id = rd.IdTipoDocumentacionRetiro
            WHERE rd.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($idRetiro, $fechaCarga, $idTipoDocumentacionRetiro, $observacion, $idUsuarioCarga, $fechaRetiro, $idUsuarioRetiro, $estado, $matricula, $apellido, $nombre, $idColegiado, $tipoDocumentacionRetiro, $numeroDocumento);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                    'idRetiro' => $idRetiro,
                    'fechaCarga' => $fechaCarga,
                    'idTipoDocumentacionRetiro' => $idTipoDocumentacionRetiro,
                    'observacion' => $observacion,
                    'idUsuarioCarga' => $idUsuarioCarga,
                    'fechaRetiro' => $fechaRetiro,
                    'idUsuarioRetiro' => $idUsuarioRetiro,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'idColegiado' => $idColegiado,
                    'tipoDocumentacionRetiro' => $tipoDocumentacionRetiro,
                    'numeroDocumento' => $numeroDocumento,
                    'estadoRetiro' => $estado
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el retiro de Documentacion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando retiro de Documentacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerTiposDocumentacion() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    
    $sql="SELECT Id, Nombre
        FROM tipodocumentacionretiro
        ORDER BY Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
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
            $resultado['mensaje'] = "No se encontraron Tipo de Documentacion.";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipo de Documentacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTipoDocumentacionRetiroPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select *
            FROM tipodocumentacionretiro
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($idTipoDocumentacionRetiro, $nombre);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                    'idTipoDocumentacionRetiro' => $idTipoDocumentacionRetiro,
                    'nombre' => $nombre
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el tipo de Documentacion";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando tipo de Documentacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerTipoDocumentacionRetiro() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM tipodocumentacionretiro";
    $stmt = $conect->prepare($sql);
    //$stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($id, $nombre);
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
            $resultado['mensaje'] = "No hay Tipos de Documentacion";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Tipos de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarRetiroDocumentacion($idColegiado, $idTipoDocumentacionRetiro, $observacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO retirodocumentacion 
        (IdColegiado, IdTipoDocumentacionRetiro, Observacion, IdUsuarioCarga, FechaCarga) 
        VALUES (?, ?, ?, ?, now())";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iisi', $idColegiado, $idTipoDocumentacionRetiro, $observacion, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idRetiroDocumentacion = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['idRetiroDocumentacion'] = $idRetiroDocumentacion;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error agregando Retiro de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function editarRetiroDocumentacion($idRetiroDocumentacion, $idColegiado, $idTipoDocumentacionRetiro, $observacion) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE retirodocumentacion 
        SET IdColegiado = ?, 
            IdTipoDocumentacionRetiro = ?, 
            Observacion = ?, 
            IdUsuarioCarga = ?, 
            FechaCarga = NOW() 
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iisii', $idColegiado, $idTipoDocumentacionRetiro, $observacion, $_SESSION['user_id'], $idRetiroDocumentacion);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idRetiroDocumentacion = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error modificando Retiro de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function borrarRetiroDocumentacion($idRetiroDocumentacion, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if ($estado == "B") {
        $idUsuarioBorrado = NULL;
        $fechaBorrado = NULL;
    } else {
        $idUsuarioBorrado = $_SESSION['user_id'];
        $fechaBorrado = date('Y-m-d H:i:s');
    }
    $sql="UPDATE retirodocumentacion 
        SET Estado = ?, 
            IdUsuarioBorrado = ?, 
            FechaBorrado = ?,
            IdUsuarioRetiro = NULL,
            FechaRetiro = NULL 
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sisi', $estado, $idUsuarioBorrado, $fechaBorrado, $idRetiroDocumentacion);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idRetiroDocumentacion = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error borrando Retiro de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function marcarEntregaRetiroDocumentacion($idRetiroDocumentacion, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if ($estado == "E") {
        $idUsuarioRetiro = $_SESSION['user_id'];
        $fechaRetiro = date('Y-m-d H:i:s');
    } else {
        $idUsuarioRetiro = NULL;
        $fechaRetiro = NULL;
    }
    $sql="UPDATE retirodocumentacion 
        SET Estado = ?, 
            IdUsuarioRetiro = ?, 
            FechaRetiro = ?,
            IdUsuarioBorrado = NULL,
            FechaBorrado = NULL 
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sisi', $estado, $idUsuarioRetiro, $fechaRetiro, $idRetiroDocumentacion);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idRetiroDocumentacion = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error marcando la entrega Retiro de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    //var_dump($resultado); exit;
    return $resultado;
}

function agregarTipoDocumentacion($nombre) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO tipodocumentacionretiro 
        (Nombre) VALUES (?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idTipoDocumentacionRetiro = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['idTipoDocumentacionRetiro'] = $idTipoDocumentacionRetiro;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error agregando Tipo de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function editarTipoDocumentacion($idTipoDocumentacionRetiro, $nombre) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE tipodocumentacionretiro 
        SET Nombre = ? 
        WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $nombre, $idTipoDocumentacionRetiro);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idRetiroDocumentacion = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error modificando Tipo de Documentacion";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}