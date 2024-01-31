<?php
function obtenerNotificacionNota($idNotificacionNota)
{
    $sql="SELECT * FROM notificacionnota
        WHERE notificacionnota.IdNotificacionNota = ".$idNotificacionNota;
    $res=mysql_query($sql);

     if (mysql_error() != 0){
        return "-1";
     }else{
        if (mysql_num_rows($res)==0){
            return "0";
        }else{
            return $res;
        }
    }
}

function  obtenerNotificacionNotaPorIdNotificacion($idNotificacion)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM notificacionnota WHERE IdNotificacionNota = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idNotificacion);
        $stmt->execute();
        $stmt->bind_result($id, $tema, $estado, $texto, $from, $subject);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $resultado['estado'] = TRUE;
            $resultado['datos'] = array(
                        'idNotificacionNota' => $id,
                        'tema' => $texto,
                        'estado' => $estado,
                        'texto' => $texto, 
                        'from' => $from, 
                        'subject' => $subject
                    );
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay NOTA";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando NOTA";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function guardarEnvioColegiado($idNotificacionColegiado)
{
    $hoy = date('Y-m-d');
    $hora = date('H:i:s');
    conectar();
    $sql = "UPDATE notificacioncolegiado 
        SET Estado = 'V', FechaEnvio = date(now()), HoraEnvio = time(now())
        WHERE IdNotificacionColegiado = ".$idNotificacionColegiado;
    $res=mysql_query($sql);
    if (mysql_error() != 0){
        return "-1";
    }else{
        return "0";
    }
}