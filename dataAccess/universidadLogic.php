<?php
//accesos a tabla colegiado
function obtenerUniversidadPorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Nombre, CorreoElectronico, TipoUniversidad, IdPaises FROM universidad WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $mail, $tipoUniversidad, $idPaises);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                'id' => $id,
                'nombre' => $nombre,
                'mail' => $mail,
                'idPaises' => $idPaises
                );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontró la universidad ".$id;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando universidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerUniversidades(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT u.Id, u.Nombre, p1.Pais
            FROM universidad u
            INNER JOIN paises p1 ON p1.Id = u.IdPaises
            ORDER BY u.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $pais);
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
                    'nombre' => trim($nombre)
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
            $resultado['mensaje'] = "No hay universidades";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando universidades";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}
