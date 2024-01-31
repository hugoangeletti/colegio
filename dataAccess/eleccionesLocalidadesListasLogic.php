<?php
function obtenerListasPorIdEleccionesLocalidad($idEleccionesLocalidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidadlista.IdELLista, eleccioneslocalidadlista.Nombre, 
            eleccioneslocalidadlista.TipoLista, 
            COUNT(eleccioneslocalidadlistaintegrantes.IdELListaIntegrante) AS Integrantes
        FROM eleccioneslocalidadlista 
        LEFT JOIN eleccioneslocalidadlistaintegrantes ON(eleccioneslocalidadlistaintegrantes.IdELLista = eleccioneslocalidadlista.IdELLista)
        WHERE IdEleccionesLocalidad = ?
        GROUP BY eleccioneslocalidadlista.IdELLista, eleccioneslocalidadlista.Nombre, eleccioneslocalidadlista.TipoLista";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidad);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $tipoLista, $cantIntegrantes);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idEleccionesLocalidadLista' => $id,
                        'nombre' => $nombre,
                        'tipoLista' => $tipoLista,
                        'cantIntegrantes' => $cantIntegrantes
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
            $resultado['mensaje'] = "No hay listas";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando listas de la localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEleccionesLocalidadListaPorId($idEleccionesLocalidadLista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM eleccioneslocalidadlista WHERE IdELLista = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadLista);
    $stmt->execute();
    $stmt->bind_result($id, $idEleccionesLocalidad, $nombre, $tipoLista);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                        'idEleccionesLocalidadLista' => $id,
                        'idEleccionesLocalidad' => $idEleccionesLocalidad,
                        'nombre' => $nombre,
                        'tipoLista' => $tipoLista
                    );
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay listas";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando listas de la localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEleccionesLocalidadesLista($idEleccionesLocalidad, $nombre, $tipoLista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eleccioneslocalidadlista (IdEleccionesLocalidad, Nombre, TipoLista) 
        VALUES (?, ?, ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iss', $idEleccionesLocalidad, $nombre, $tipoLista);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Lista HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Lista';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarEleccionesLocalidadesLista($idEleccionesLocalidadLista, $nombre, $tipoLista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eleccioneslocalidadlista 
            SET Nombre = ?, TipoLista = ?
            WHERE IdELLista = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssi', $nombre, $tipoLista, $idEleccionesLocalidadLista);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Lista HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Lista';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarEleccionesLocalidadesLista($idEleccionesLocalidadLista){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="DELETE FROM eleccioneslocalidadlista WHERE IdELLista = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadLista);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Lista HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Lista';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

