<?php
function crearEnvioMail(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eticaexpediente (IdColegiado, NumeroExpediente, Caratula, Observaciones, IdUsuario, Fecha, IdSumarianteTitular, IdSumarianteSuplente, Estado, IdSecretarioadhoc) 
        VALUES (?, ?, ?, ?, ?, now(), ?, ?, ?, ?)";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssiiisi', $idColegiado, $nroExpediente, $caratula, $observaciones, $_SESSION['user_id'], $idSumarianteTitular, $idSumarianteSuplente, $estado, $idSecretarioadhoc);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        //agrego el movimiento para hacer el seguimiento
        $idEticaExpediente = mysqli_stmt_insert_id($stmt);
        $sql="INSERT INTO eticaexpedientemovimiento 
            (IdEticaExpediente, IdEticaEstado, Fecha, IdUsuario) 
            VALUES (?, 1, now(), ?)";

        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idEticaExpediente, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        
        $estadoConsulta = TRUE;
        $mensaje = 'Expediente HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Expediente';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;    
}


