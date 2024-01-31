<?php
function obtenerPersonaPorDiploma($idEvento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM diploma WHERE IdEvento = ? ORDER BY ApellidoNombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEvento);
    $stmt->execute();
    $stmt->bind_result($id, $idEvento, $apellidoNombre, $matricula, $caracter, $email, $nombrePdf, $path);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'idEvento' => $idEvento,
                    'apellidoNombre' => $apellidoNombre,
                    'matricula' => $matricula,
                    'caracter' => $caracter,
                    'email' => $email,
                    'nombrePdf' => $nombrePdf,
                    'path' => $path
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
            $resultado['mensaje'] = "No hay personas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando personas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerEventoPorId($id) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM evento WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($id, $nombre, $fecha, $plantilla, $nombreCertificado);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                    'id' => $id,
                    'nombre' => $nombre,
                    'fecha' => $fecha,
                    'plantilla' => $plantilla,
                    'nombreCertificado' => $nombreCertificado
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro evento";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando evento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerDiplomasEnviar($rango) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT e.Nombre, e.Fecha, d.Id, d.ApellidoNombre, d.Matricula, d.Caracter, d.Mail, d.NombrePDF, d.Path
        FROM diploma d
        INNER JOIN evento e ON e.Id = d.IdEvento
        LEFT JOIN enviomaildiariocolegiado emdc ON emdc.IdReferencia = d.Id
        WHERE emdc.Id IS NULL 
        ORDER BY e.Nombre, d.ApellidoNombre
        LIMIT ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $rango);
    $stmt->execute();
    $stmt->bind_result($nombreEvento, $fechaEvento, $idDiploma, $apellidoNombre, $matricula, $caracter, $email, $nombrePdf, $path);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'nombreEvento' => $nombreEvento,
                    'fechaEvento' => $fechaEvento,
                    'idReferencia' => $idDiploma,
                    'apellido' => $apellidoNombre,
                    'nombres' => "",
                    'matricula' => $matricula,
                    'caracter' => $caracter,
                    'mail' => $email,
                    'nombrePdf' => $nombrePdf,
                    'path' => $path
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
            $resultado['mensaje'] = "No hay personas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando personas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function guardarPdfDiploma($idDiploma, $nombreArchivo, $estructura) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE diploma
            SET NombrePDF = ?, Path = ?
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssi', $nombreArchivo, $estructura, $idDiploma);
    $stmt->execute();
    //$stmt->bind_result($id, $nombre, $fecha, $plantilla, $nombreCertificado);
    $stmt->store_result();

    if ($stmt->execute()) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error cargando PDF";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}