<?php
function  obtenerEnvioDiario()
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT enviomaildiario.Id, enviomaildiario.Detalle, enviomaildiario.Rango, enviomaildiario.Texto,
        enviomaildiario.From, enviomaildiario.Subject
        FROM enviomaildiario
        WHERE enviomaildiario.Envia = 'S'";
        //WHERE enviomaildiario.Id = 9";
        //
        $stmt = $conect->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($idEnvio, $detalle, $rango, $texto, $from, $subject);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idEnvio' => $idEnvio,
                        'detalle' => $detalle,
                        'rango' => $rango,
                        'texto' => $texto,
                        'from' => $from,
                        'subject' => $subject
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
            $resultado['mensaje'] = "No hay Envios";
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

function guardarEnvioColegiado($idEnvio, $idColegiado, $idReferencia, $error, $estado)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO enviomaildiariocolegiado 
        (IdEnvioMailDiario, IdColegiado, IdReferencia, FechaEnvio, Error, Estado)
        VALUES (?, ?, ?, NOW(), ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iiiss', $idEnvio, $idColegiado, $idReferencia, $error, $estado);
    $stmt->execute();
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando Notificacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiadoCambiosLink($rango) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    /*
    $sql="SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, cc.CorreoElectronico
        FROM colegiado c
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento tm ON tm.Id = c.Estado
        INNER JOIN colegiadocontacto cc ON(cc.IdColegiado = c.Id AND cc.IdEstado = 1 AND cc.CorreoElectronico is not null AND UPPER(cc.CorreoElectronico) <> 'NR' AND cc.CorreoElectronico <> '')
        LEFT JOIN enviomaildiariocolegiado emdc ON(emdc.IdColegiado = c.Id AND emdc.IdReferencia = c.Id)
        LEFT JOIN agremiacionesdebito ad ON ad.IdColegiado = c.Id
        LEFT JOIN debitotarjeta dt ON (dt.IdColegiado = c.Id AND dt.Estado = 'A')
        LEFT JOIN debitocbu dc ON (dc.IdColegiado = c.Id AND dc.Estado = 'A')
        WHERE tm.Estado IN('A')
        AND emdc.Id IS NULL 
        AND dt.id IS NULL AND dc.Id IS NULL AND ad.Id IS NULL
        ORDER BY c.Matricula
        LIMIT ?";
    */
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, cc.CorreoElectronico
        FROM colegiado c
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento tm ON tm.Id = c.Estado
        INNER JOIN colegiadocontacto cc ON(cc.IdColegiado = c.Id AND cc.IdEstado = 1 AND cc.CorreoElectronico is not null AND UPPER(cc.CorreoElectronico) <> 'NR' AND cc.CorreoElectronico <> '')
        LEFT JOIN enviomaildiariocolegiado emdc ON(emdc.IdColegiado = c.Id AND emdc.IdReferencia = c.Id AND emdc.Estado IN('A', 'O'))
        LEFT JOIN agremiacionesdebito ad ON ad.IdColegiado = c.Id
        LEFT JOIN debitotarjeta dt ON (dt.IdColegiado = c.Id AND dt.Estado = 'A')
        LEFT JOIN debitocbu dc ON (dc.IdColegiado = c.Id AND dc.Estado = 'A')
        LEFT JOIN colegiadomailrechazado cmr ON cmr.IdColegiado = c.Id
        WHERE tm.Estado IN('A')
        AND emdc.Id IS NULL 
        AND dt.id IS NULL AND dc.Id IS NULL AND ad.Id IS NULL AND cmr.Id IS NULL 
        AND c.Id IN (SELECT da.IdColegiado FROM colegiadodeudaanual da INNER JOIN colegiadodeudaanualcuotas dac ON dac.IdColegiadoDeudaAnual = da.Id LEFT JOIN cobranzadetalle cd ON cd.Recibo = dac.Id LEFT JOIN cobranza co ON co.Id = cd.IdLoteCobranza WHERE da.IdColegiado = c.Id AND da.Periodo = ".$_SESSION['periodoActual']." AND da.Estado = 'A' AND (co.IdLugarPago <> 26 OR cd.Id IS NULL))
        ORDER BY c.Matricula
        LIMIT ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $rango);
        $stmt->execute();
        $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres, $sexo, $mail);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idColegiado' => $idColegiado,
                        'idReferencia' => $idColegiado,
                        'matricula' => $matricula,
                        'apellido' => $apellido,
                        'nombres' => $nombres,
                        'sexo' => $sexo,
                        'mail' => $mail
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
            $resultado['mensaje'] = "No hay pendientes de notificacion";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando pendientes de notificacion";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}