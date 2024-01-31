<?php
function obtenerEleccionesPorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM elecciones WHERE IdElecciones = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $estado, $anio);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                        'idElecciones' => $id,
                        'detalle' => $detalle,
                        'estado' => $estado,
                        'anio' => $anio
                    );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay elecciones";
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

function obtenerEleccionesPorEstado($estado){
    $conect = conectar();
    if (isset($estado)) {
        $conEstado = "WHERE Estado = '".$estado."'";
    } else {
        $conEstado = "";
    }
    mysqli_set_charset( $conect, 'utf8');
    
    $sql = "SELECT * FROM elecciones ".$conEstado." ORDER BY Anio DESC";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $estado, $anio);
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
                    'idElecciones' => $id,
                    'detalle' => $detalle,
                    'estado' => $estado,
                    'anio' => $anio
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
            $resultado['mensaje'] = "No hay elecciones";
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

function agregarElecciones($detalle, $anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO elecciones (Detalle, Estado, Anio) 
        VALUES (?, 'A',?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $detalle, $anio);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Elecciones HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Elecciones';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarElecciones($idElecciones, $detalle, $estado, $anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE elecciones 
            SET Detalle = ?, Estado = ?, Anio = ?
            WHERE IdElecciones = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssii', $detalle, $estado, $anio, $idElecciones);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Elecciones HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Elecciones';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarElecciones($idElecciones){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE elecciones SET 
                Estado = 'B'
                WHERE IdElecciones = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idElecciones);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Elecciones HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Elecciones';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function eleccionesActiva(){
    //return FALSE;
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT MAX(IdElecciones)
        FROM elecciones 
        WHERE Estado = 'A'";
    
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idElecciones);
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);
        return $idElecciones;
    } else {
        return NULL;
    }
}

