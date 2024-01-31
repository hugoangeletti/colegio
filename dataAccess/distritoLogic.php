<?php
function obtenerDistritos() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM distritos ORDER BY Distrito";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $distrito, $romano, $presidente, $domicilio, $email, $pagina);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'distrito' => $distrito,
                    'romano' => $romano,
                    'presidente' => $presidente,
                    'domicilio' => $domicilio,
                    'mail' => $email,
                    'pagina' => $pagina
                );
                array_push($datos, $row);
            }
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay Distritos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Distritos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerDistritoPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select * from distritos where Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $distrito, $romano, $presidente, $domicilio, $email, $pagina);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                    'id' => $id,
                    'distrito' => $distrito,
                    'romano' => $romano,
                    'presidente' => $presidente,
                    'domicilio' => $domicilio,
                    'mail' => $email,
                    'pagina' => $pagina
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro Distrito";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Distrito";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function editarDistrito($idDistrito, $presidente, $domicilio, $mail, $pagina) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE distritos 
        SET Presidente = ?, 
            Domicilio = ?, 
            Email = ?, 
            Pagina = ?
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssssi', $presidente, $domicilio, $mail, $pagina, $idDistrito);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->execute()) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "Distrito actualizado correctamente";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error actualizando Distrito";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;    
}