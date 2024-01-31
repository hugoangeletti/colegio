<?php
function agregarConstanciaFirma($idColegiado, $importe, $nombreArchivo) {
    $conect = conectar();
    try {
        mysqli_autocommit($conect, FALSE);
        mysqli_set_charset( $conect, 'utf8');
        $fechaCarga = date('Y-m-d');
        $sql = "INSERT INTO constanciafirma 
                (Fecha, IdUsuario, Importe, Estado, IdColegiado, NombreArchivo)
                VALUES (date(now()), ?, ?, 'A', ?, ?)";
        
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isis', $_SESSION['user_id'], $importe, $idColegiado, $nombreArchivo);
        $stmt->execute();
        $stmt->store_result();
        if (mysqli_stmt_errno($stmt) == 0) {
            $resultado['estado'] = TRUE;
            $resultado['idConstanciaFirma'] = mysqli_stmt_insert_id($stmt);
            $resultado['mensaje'] = "SE REGISTRO LA CONSTANCIA DE FIRMA CORRECTAMENTE";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REGISTRAR CONSTANCIA DE FIRMA ".mysqli_stmt_error($stmt);
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $conect->commit();
        } else {
            $conect->rollback();
        }
        desconectar($conect);
        return $resultado;
        
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL REGISTRAR CONSTANCIA DE FIRMA";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
        return $resultado;
    }	
}

function obtenerCertificacionFirmaPorFecha($fecha) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cf.Id, cf.Fecha, cf.IdUsuario, cf.Importe, cf.Estado, cf.IdColegiado, cf.NombreArchivo, c.Matricula, p.Apellido, p.Nombres, cdm.Tipo, cdm.Numero
		FROM constanciafirma cf
		INNER JOIN colegiado c ON c.Id = cf.IdColegiado
		INNER JOIN persona p ON p.Id = c.IdPersona
        LEFT JOIN cajadiariamovimiento cdm ON cdm.Id = cf.IdCajaDiariaMovimiento
		WHERE cf.Fecha = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $fecha);
    $stmt->execute();
    $stmt->bind_result($idCertificacionFirma, $fecha, $idUsuario, $importe, $estado, $idColegiado, $nombreArchivo, $matricula, $apellido, $nombre, $tipoComprobante, $numeroComprobante);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idCertificacionFirma' => $idCertificacionFirma,
                    'fecha' => $fecha,
                    'idUsuario' => $idUsuario,
                    'importe' => $importe,
                    'estado' => $estado,
                    'idColegiado' => $idColegiado,
                    'nombreArchivo' => $nombreArchivo,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'tipoComprobante' => $tipoComprobante,
                    'numeroComprobante' => $numeroComprobante
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existen certificaciones de firma.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando certificaciones de firma";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerCertificacionFirmaPorId($idConstanciaFirma) {
$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cf.Id, cf.Fecha, cf.IdUsuario, cf.Importe, cf.Estado, cf.IdColegiado, cf.NombreArchivo, c.Matricula, p.Apellido, p.Nombres
        FROM constanciafirma cf
        INNER JOIN colegiado c ON c.Id = cf.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        WHERE cf.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idConstanciaFirma);
    $stmt->execute();
    $stmt->bind_result($idCertificacionFirma, $fecha, $idUsuario, $importe, $estado, $idColegiado, $nombreArchivo, $matricula, $apellido, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'idCertificacionFirma' => $idCertificacionFirma,
                'fecha' => $fecha,
                'idUsuario' => $idUsuario,
                'importe' => $importe,
                'estado' => $estado,
                'idColegiado' => $idColegiado,
                'nombreArchivo' => $nombreArchivo,
                'matricula' => $matricula,
                'apellido' => $apellido,
                'nombre' => $nombre
            );

            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existen certificaciones de firma.";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando certificaciones de firma";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;    
}

function anularConstanciaFirmaPorId($id) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql1 = "UPDATE constanciafirma 
            SET Estado = 'A'
            WHERE Id = ?";
    $stmt1 = $conect->prepare($sql1);
    $stmt1->bind_param('i', $id);
    $stmt1->execute();
    if(mysqli_stmt_errno($stmt1) == 0) {
        $resultado['estado'] = TRUE;                                            
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';                             
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ANULAR RECIBO";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';                             
    }
    return $resultado;
}

function agregarArchivoEnConstanciaFirma($idConstanciaFirma, $nombreArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql1 = "UPDATE constanciafirma 
            SET NombreArchivo = ?
            WHERE Id = ?";
    $stmt1 = $conect->prepare($sql1);
    $stmt1->bind_param('si', $nombreArchivo, $idConstanciaFirma);
    $stmt1->execute();
    if(mysqli_stmt_errno($stmt1) == 0) {
        $resultado['estado'] = TRUE;                                            
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';                             
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL GUARDAR ARCHIVO";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';                             
    }
    return $resultado;    
}