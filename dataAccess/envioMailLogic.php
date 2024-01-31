<?php
function obtenerEnvioDisponible()
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT enviomail.IdEnvioMail, enviomail.IdNotificacion, enviomail.CantidadEnviar, enviomail.Rango, 
        enviomail.CantidadEnviados, enviomail.Pdf
        FROM enviomail
        WHERE enviomail.FechaInicioEnvio <= date(now())
        and enviomail.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idEnvioMail, $idNotificacion, $cantidadEnviar, $rango, $cantidadEnviados, $pdf);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idEnvioMail' => $idEnvioMail,
                    'idNotificacion' => $idNotificacion,
                    'cantidadEnviar' => $cantidadEnviar,
                    'rango' => $rango,
                    'cantidadEnviados' => $cantidadEnviados,
                    'pdf' => $pdf
                    );
            
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay envios";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando envios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;}

function guardarEnvios($idEnvioMail, $cantidadEnviados, $cantidadEnviar)
{
    $hoy = date('Y-m-d');
    $hora = date('H:i:s');
    if ($cantidadEnviados >= $cantidadEnviar){
        $estado = 'E';
    }else{
        $estado = 'A';
    }
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE enviomail 
        SET enviomail.CantidadEnviados = enviomail.CantidadEnviados + ?, 
            enviomail.FechaUltimoEnvio = ?, 
            enviomail.HoraUltimoEnvio = ?, 
            enviomail.Estado = ?
        WHERE enviomail.IdEnvioMail = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssi', $cantidadEnviados, $hoy, $hora);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';
    }else{
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL ACTUALIZAR EL ENVIO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}
