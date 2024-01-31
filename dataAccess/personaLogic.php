<?php
function obtenerPersonaPorId($idPersona) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM persona WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPersona);
    $stmt->execute();
    $stmt->bind_result($id, $apellido, $nombres, $sexo, $tipoDocumento, $numeroDocumento, $fechaNacimiento, $idPaises, $fechaCarga, $fechaActualizacion, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idPersona' => $id,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'sexo' => $sexo, 
                    'tipoDocumento' => $tipoDocumento, 
                    'numeroDocumento' => $numeroDocumento,
                    'fechaNacimiento' => $fechaNacimiento,
                    'idNacionalidad' => $idPaises,
                    'fechaCarga' => $fechaCarga,
                    'fechaActualizacion' => $fechaActualizacion,
                    'estado' => $estado
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['estado'] = TRUE;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay persona ".$idPersona;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando persona";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}

function obtenerPersonaPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT persona.* 
        FROM persona 
        INNER JOIN colegiado ON(colegiado.IdPersona = persona.Id)
        WHERE colegiado.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $apellido, $nombres, $sexo, $tipoDocumento, $numeroDocumento, $fechaNacimiento, $idPaises, $fechaCarga, $fechaActualizacion, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idPersona' => $id,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'sexo' => $sexo, 
                    'tipoDocumento' => $tipoDocumento, 
                    'numeroDocumento' => $numeroDocumento,
                    'fechaNacimiento' => $fechaNacimiento,
                    'idNacionalidad' => $idPaises,
                    'fechaCarga' => $fechaCarga,
                    'fechaActualizacion' => $fechaActualizacion,
                    'estado' => $estado
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['estado'] = TRUE;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay persona ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando persona";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}

function obtenerPersonasActivas() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT persona.Id, persona.Apellido, persona.Nombres, persona.NumeroDocumento, persona.Sexo,
                persona.FechaNacimiento, paises.Nacionalidad
            FROM persona 
            INNER JOIN paises ON(paises.Id = persona.IdPaises)
            WHERE persona.Estado = 'A'
            ORDER BY persona.Apellido, persona.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idPersona, $apellido, $nombres, $numeroDocumento, $sexo, $fechaNacimiento, $nacionalidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($sexo == 'M') {
                    $sexo = 'Masculino';
                } else {
                    $sexo = 'Femenino';
                }
                $row = array (
                    'id' => $idPersona,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'numeroDocumento' => $numeroDocumento,
                    'sexo' => $sexo,
                    'fechaNacimiento' => $fechaNacimiento,
                    'nacionalidad' => $nacionalidad
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
            $resultado['mensaje'] = "No hay personas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando personas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function numeroDocumentoExiste($tipoDocumento, $numeroDocumento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT COUNT(Id) AS Cantidad FROM persona WHERE TipoDocumento = ? AND NumeroDocumento = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $tipoDocumento, $numeroDocumento);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "Numero de Documento YA EXISTE EN LA BASE DE DATOS";
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No existe Numero de Documento";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al buscar el Numero de Documento";
    }
    
    return $resultado;
}

function agregarPersona($apellido, $nombres, $sexo, $tipoDocumento, $numeroDocumento, $fechaNacimiento, $idPaises) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO persona 
        (Apellido, Nombres, Sexo, TipoDocumento, NumeroDocumento, FechaNacimiento, IdPaises, FechaCarga) 
        VALUES (?, ?, ?, ?, ?, ?, ?, DATE(NOW()))";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssiisi', $apellido, $nombres, $sexo, $tipoDocumento, $numeroDocumento, $fechaNacimiento, $idPaises);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $idPersona = mysqli_stmt_insert_id($stmt);
        $sql="INSERT INTO log_tabla 
            (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
            VALUES ('persona', ?, now(), 'alta', ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idPersona, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['idPersona'] = $idPersona;
            $resultado['mensaje'] = 'LA PERSONA HA SIDO AGREGADA';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR PERSONA";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR PERSONA";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function modificarPersona($idPersona, $apellido, $nombres, $sexo, $numeroDocumento, $fechaNacimiento, $idPaises, $persona) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE persona 
        SET Apellido = ?, 
            Nombres = ?, 
            Sexo = ?, 
            NumeroDocumento = ?, 
            FechaNacimiento = ?, 
            IdPaises = ?, 
            FechaActualizacion = DATE(NOW()) 
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssisii', $apellido, $nombres, $sexo, $numeroDocumento, $fechaNacimiento, $idPaises, $idPersona);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $sql="INSERT INTO log_tabla 
            (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
            VALUES ('persona', ?, now(), 'modificacion', ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('iis', $idPersona, $_SESSION['user_id'], serialize($persona));
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'LA PERSONA HA SIDO MODIFICADA';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR PERSONA";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL MODIFICAR PERSONA";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function bajaPersona($idPersona) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE persona 
        SET Estado = 'B'
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idPersona);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $sql="INSERT INTO log_tabla 
            (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
            VALUES ('persona', ?, now(), 'borrada', ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idPersona, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'LA PERSONA HA SIDO ELIMINADA';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ELIMINAR PERSONA";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ELIMINAR PERSONA";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}