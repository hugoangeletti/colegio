<?php
function obtenerSumarianteBuscar($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Matricula, persona.Apellido, persona.Nombres "
            . "FROM sumariante "
            . "INNER JOIN colegiado ON(colegiado.Id = sumariante.IdColegiado)"
            . "INNER JOIN persona ON(persona.Id = colegiado.IdPersona)"
            . "WHERE sumariante.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($matricula, $apellido, $nombres);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['sumarianteBuscar'] = $apellido." ".$nombres." (".$matricula.")";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay sumariante";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando sumariante";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSumariantePorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Matricula, persona.Apellido, persona.Nombres, sumariante.Estado, 
            sumariante.IdColegiado
            FROM sumariante 
            INNER JOIN colegiado ON(colegiado.Id = sumariante.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            WHERE sumariante.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($matricula, $apellido, $nombres, $estado, $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                        'idSumariante' => $id,
                        'sumarianteBuscar' => $apellido." ".$nombres." (".$matricula.")",
                        'idColegiado' => $idColegiado,
                        'estado' => $estado
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay sumariante";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando sumariante";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerSumariantes(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT sumariante.Id, colegiado.Matricula, persona.Apellido, persona.Nombres, sumariante.Estado, sumariante.IdColegiado
            FROM sumariante
            INNER JOIN colegiado ON(colegiado.Id = sumariante.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            ORDER BY persona.Apellido, persona.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idSumariante, $matricula, $apellido, $nombres, $estado, $idColegiado);
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
                    'id' => $idSumariante,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'matricula' => $matricula,
                    'estado' => $estado,
                    'idColegiado' => $idColegiado
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
            $resultado['mensaje'] = "No hay sumariantes";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando sumariantes";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerSumarianteAutocompletar(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT sumariante.Id, colegiado.Matricula, persona.Apellido, persona.Nombres 
            FROM sumariante
            INNER JOIN colegiado ON(colegiado.Id = sumariante.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            ORDER BY persona.Apellido, persona.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idSumariante, $matricula, $apellido, $nombres);
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
                    'id' => $idSumariante,
                    'nombre' => $apellido." ".$nombres." (".$matricula.")"
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
            $resultado['mensaje'] = "No hay sumariantes";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando sumariantes";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function agregarSumariante($idColegiado, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO sumariante (IdColegiado, Estado, FechaCarga, IdUsuario) 
        VALUES (?, ?, now(), ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isi', $idColegiado, $estado, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Sumariante HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Sumariante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarSumariante($idSumariante, $idColegiado, $estado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE sumariante 
            SET IdColegiado = ?, Estado = ?, IdUsuario = ?, FechaCarga = now()
            WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isii', $idColegiado, $estado, $_SESSION['user_id'], $idSumariante);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Sumariante HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Sumariante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarSumariante($idSumariante){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE sumariante SET 
                Estado = 'B',
                FechaCarga = now()
                WHERE Id = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idSumariante);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Sumariante HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Sumariante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function esSumariante($nombreUsuario){
    //return FALSE;
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id
        FROM sumariante 
        WHERE NombreUsuario = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $nombreUsuario);
    $stmt->execute();
    $stmt->bind_result($idSumariante);
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);
        return $idSumariante;
    } else {
        return NULL;
    }
}

