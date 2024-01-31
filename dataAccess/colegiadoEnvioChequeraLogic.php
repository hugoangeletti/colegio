<?php
/*
function obtenerColegiadoEnvioChequera($periodoActual, $rango){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "select colegiadodeudaanual.Id, colegiado.Matricula, persona.Apellido, persona.Nombres, 
        debitotarjeta.id as IdDebitoTarjeta, agremiacionesdebito.Id as IdDebitoAgremiacion, 
        debitocbu.Id as IdDebitoCbu, colegiadocontacto.CorreoElectronico, lugarpago.Detalle as LugarPago, 
        banco.Nombre as NombreBanco, debitotarjeta.Tipo as TipoTarjeta, b.Nombre as BancoCBU, persona.Sexo
    from colegiadodeudaanual
    inner join colegiado on(colegiado.Id = colegiadodeudaanual.IdColegiado)
    inner join persona on(persona.Id = colegiado.IdPersona)
    inner join colegiadodomicilioreal on(colegiadodomicilioreal.idColegiado = colegiado.Id and colegiadodomicilioreal.idEstado = 1)
    inner join colegiadocontacto on(colegiadocontacto.IdColegiado = colegiado.Id and colegiadocontacto.IdEstado = 1)
    left join enviomailchequera on(enviomailchequera.IdColegiadoDeudaAnual = colegiadodeudaanual.Id)
    left join debitotarjeta on(debitotarjeta.IdColegiado = colegiado.Id and debitotarjeta.Estado = 'A')
    left join banco on(banco.Id = debitotarjeta.IdBanco)
    left join debitocbu on(debitocbu.IdColegiado = colegiado.Id and debitocbu.Estado = 'A')
    left join banco as b on(b.Id = debitocbu.IdBanco)
    left join agremiacionesdebito on(agremiacionesdebito.IdColegiado = colegiado.Id)
    left join lugarpago on(lugarpago.Id = agremiacionesdebito.IdLugarPago)
    where colegiadodeudaanual.Periodo = ?
    and colegiado.Estado in(1, 5, 10)
    and enviomailchequera.Id is null
    and colegiadocontacto.CorreoElectronico <> '' and colegiadocontacto.CorreoElectronico <> 'NR' and colegiadocontacto.CorreoElectronico <> 'nr'
    limit 0, ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $periodoActual, $rango);
    $stmt->execute();
    $stmt->bind_result($idColegiadoDeudaAnual, $matricula, $apellido, $nombre, $idDebitoTarjeta, $idDebitoAgremiacion, $idDebitoCbu, $mail, $lugarPago, $banco, $tipoTarjeta, $bancoCbu, $sexo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoDeudaAnual' => $idColegiadoDeudaAnual,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre,
                    'idDebitoTarjeta' => $idDebitoTarjeta,
                    'idDebitoAgremiacion' => $idDebitoAgremiacion,
                    'idDebitoCbu' => $idDebitoCbu,
                    'mail' => $mail,
                    'lugarPago' => $lugarPago,
                    'banco' => $banco,
                    'tipoTarjeta' => $tipoTarjeta,
                    'bancoCbu' => $bancoCbu,
                    'sexo' => $sexo
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay chequeras";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando chequeras";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}
*/
function guardarEnvioChequera($idEnvioMail, $idColegiadoDeudaAnual){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO enviomailchequera 
            (IdEnvioMail, IdColegiadoDeudaAnual, Fecha)
            VALUES (?, ?, now())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idEnvioMail, $idColegiadoDeudaAnual);
    $stmt->execute();
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';        
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando enviomailchequera";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}
        