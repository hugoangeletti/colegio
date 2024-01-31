<?php
//accesos a tabla colegiado
function obtenerLocalidadPorId($idLocalidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT localidad.Nombre, localidad.CodigoPostal, localidad.idZona, zonas.Nombre
            FROM localidad
            LEFT JOIN zonas ON(zonas.Id = localidad.IdZona)
            WHERE localidad.Id = ?
            ORDER BY localidad.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idLocalidad);
    $stmt->execute();
    $stmt->bind_result($nombreLocalidad, $codigoPostal, $idZona, $nombreZona);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'nombreLocalidad' => $nombreLocalidad,
                    'codigoPostal' => $codigoPostal,
                    'idZona' => $idZona,
                    'nombreZona' => $nombreZona
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay localidad ".$idLocalidad;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerLocalidadBuscar($idLocalidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT localidad.Nombre, localidad.CodigoPostal
            FROM localidad
            LEFT JOIN zonas ON(zonas.Id = localidad.IdZona)
            WHERE localidad.Id = ?
            ORDER BY localidad.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idLocalidad);
    $stmt->execute();
    $stmt->bind_result($nombreLocalidad, $codigoPostal);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['colegiadoBuscar'] = $nombreLocalidad;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay localidad ".$idLocalidad;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerLocalidadesAutocompletar(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT localidad.Id, localidad.Nombre, localidad.CodigoPostal
            FROM localidad
            ORDER BY localidad.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idLocalidad, $nombreLocalidad, $codigoPostal);
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
                    'id' => $idLocalidad,
                    'nombre' => $nombreLocalidad
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
            $resultado['mensaje'] = "No hay localidades";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidades";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerLocalidadesPorZona($idZona){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT l.Id, l.Nombre, l.CodigoPostal
            FROM localidad l
            WHERE l.idZona = ?
            ORDER BY l.CodigoPostal, l.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idZona);
    $stmt->execute();
    $stmt->bind_result($idLocalidad, $nombreLocalidad, $codigoPostal);
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
                    'id' => $idLocalidad,
                    'nombre' => trim($codigoPostal).' - '.trim($nombreLocalidad),
                    'codigoPostal' => $codigoPostal
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
            $resultado['mensaje'] = "No hay localidades";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando localidades";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}
