<?php
function obtenerDebitoGenerado($tipoDebito, $anio){
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT ed.Id, ed.FechaEnvio, ed.FechaDebito, ed.Tipo, ed.Estado, ed.NombreArchivo, ed.PathArchivo
        FROM enviodebito ed
        WHERE ed.Tipo = ? AND YEAR(ed.FechaEnvio) = ? AND ed.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $tipoDebito, $anio);
    $stmt->execute();
    $stmt->bind_result($idEnvioDebito, $fechaEnvio, $fechaDebito, $tipoDebito, $estado, $nombreArchivo, $pathArchivo);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                switch ($tipoDebito) {
                    case 'D':
                        $nombreTipoDebito = "Tarjeta de Débito";
                        break;
                    
                    case 'C':
                        $nombreTipoDebito = "Tarjeta de Crédito";
                        break;
                    
                    case 'H':
                        $nombreTipoDebito = "CBU";
                        break;
                    
                    default:
                        $nombreTipoDebito = "Sin detalle";
                        break;
                }

                switch ($estado) {
                    case 'A':
                        $nombreEstado = 'A enviar';
                        break;
                    
                    case 'E':
                        $nombreEstado = 'Enviado';
                        break;
                    
                    default:
                        $nombreEstado = "Sin detalle";
                        break;
                }
                $row = array (
                    'idEnvioDebito' => $idEnvioDebito,
                    'fechaEnvio' => $fechaEnvio,
                    'fechaDebito' => $fechaDebito,
                    'tipoDebito' => $tipoDebito,
                    'nombreTipoDebito' => $nombreTipoDebito,
                    'estado' => $estado,
                    'nombreEstado' => $nombreEstado,
                    'nombreArchivo' => $nombreArchivo,
                    'pathArchivo' => $pathArchivo
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
            $resultado['mensaje'] = "No se encontro envio debito";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando envio debito";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadoDebitoRechazado($rango, $idLugarPago){
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    if ($idLugarPago == 28) {
        $sql = "SELECT cobranza.Id, cobranzanovedades.Id, cobranzanovedades.IdColegiado,
            colegiado.Matricula, persona.Sexo, persona.Apellido, persona.Nombres, colegiadocontacto.CorreoElectronico,
            cobranzanovedades.Detalle, debitotarjeta.Tipo AS TipoTarjeta, '' AS TipoCuenta
            FROM cobranza
            INNER JOIN cobranzanovedades ON(cobranzanovedades.IdCobranza = cobranza.Id)
            INNER JOIN colegiado ON(colegiado.Id = cobranzanovedades.IdColegiado)
            INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado 
                AND tipomovimiento.Estado = 'A')
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
                AND colegiadocontacto.IdEstado = 1 
                AND colegiadocontacto.CorreoElectronico is not null 
                AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
                AND colegiadocontacto.CorreoElectronico <> '')
            LEFT JOIN debitotarjeta ON(debitotarjeta.IdColegiado = cobranzanovedades.IdColegiado 
                AND debitotarjeta.Estado IN('A', 'B'))
            LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = cobranzanovedades.IdColegiado
                AND enviomaildiariocolegiado.IdReferencia = cobranza.Id)
            WHERE cobranza.IdLugarPago = ?
                AND cobranza.EnvioMail = 'N' 
                AND enviomaildiariocolegiado.Id IS NULL 
            GROUP BY colegiado.Matricula
            ORDER BY colegiado.Matricula
            LIMIT ?";
    } else {
        $sql = "SELECT cobranza.Id, cobranzanovedades.Id, cobranzanovedades.IdColegiado,
            colegiado.Matricula, persona.Sexo, persona.Apellido, persona.Nombres, colegiadocontacto.CorreoElectronico,
            cobranzanovedades.Detalle, ' ' AS TipoTarjeta, debitocbu.Tipo AS TipoCuenta
            FROM cobranza
            INNER JOIN cobranzanovedades ON(cobranzanovedades.IdCobranza = cobranza.Id)
            INNER JOIN colegiado ON(colegiado.Id = cobranzanovedades.IdColegiado)
            INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado 
                AND tipomovimiento.Estado = 'A')
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
                AND colegiadocontacto.IdEstado = 1 
                AND colegiadocontacto.CorreoElectronico is not null 
                AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
                AND colegiadocontacto.CorreoElectronico <> '')
            LEFT JOIN debitocbu ON(debitocbu.IdColegiado = cobranzanovedades.IdColegiado 
                AND debitocbu.Estado IN('A', 'B'))
            LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = cobranzanovedades.IdColegiado 
                AND enviomaildiariocolegiado.IdReferencia = cobranzanovedades.Id)
            WHERE cobranza.IdLugarPago = ?
                AND cobranza.EnvioMail = 'N' 
                AND enviomaildiariocolegiado.Id IS NULL 
            GROUP BY colegiado.Matricula
            ORDER BY colegiado.Matricula
            LIMIT ?";
    }
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idLugarPago, $rango);
    $stmt->execute();
    $stmt->bind_result($idCobranza, $idCobranzaNovedades, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail, $detalle, $tipoTarjeta, $tipoCuenta);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                if ($idLugarPago == 28) {
                    $idReferencia = $idCobranza;
                } else {
                    $idReferencia = $idCobranzaNovedades;
                }
                $row = array (
                    'idCobranza' => $idCobranza,
                    'idReferencia' => $idReferencia,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'sexo' => $sexo,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'mail' => $mail,
                    'detalle' => $detalle,
                    'tipoTarjeta' => $tipoTarjeta,
                    'tipoCuenta' => $tipoCuenta
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
            $resultado['mensaje'] = "No se encontro rechazo de debito del colegiado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando notificacion del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadoStopDebitPorBono($rango) {
    $conect = conectar();
    //mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT cobranzanovedades.Id, cobranzanovedades.IdColegiado,
        colegiado.Matricula, persona.Sexo, persona.Apellido, persona.Nombres, colegiadocontacto.CorreoElectronico
        FROM cobranza
        INNER JOIN cobranzanovedades ON(cobranzanovedades.IdCobranza = cobranza.Id)
        INNER JOIN colegiado ON(colegiado.Id = cobranzanovedades.IdColegiado)
        INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado 
            AND tipomovimiento.Estado = 'A')
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
            AND colegiadocontacto.IdEstado = 1 
            AND colegiadocontacto.CorreoElectronico is not null 
            AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
            AND colegiadocontacto.CorreoElectronico <> '')
        INNER JOIN debitocbu ON(debitocbu.IdColegiado = cobranzanovedades.IdColegiado 
            AND debitocbu.Estado='A' AND debitocbu.IdBanco = 1)
        LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = cobranzanovedades.IdColegiado 
            AND enviomaildiariocolegiado.IdReferencia = cobranzanovedades.Id
                AND enviomaildiariocolegiado.IdEnvioMailDiario = 14)
        WHERE cobranzanovedades.IdCobranza = (SELECT MAX(Id) FROM cobranza WHERE cobranza.IdLugarPago = 30)
            AND cobranza.EnvioMail = 'N'
            AND enviomaildiariocolegiado.Id IS NULL 
        GROUP BY colegiado.Matricula
        ORDER BY colegiado.Matricula
        LIMIT ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $rango);
    $stmt->execute();
    $stmt->bind_result($idCobranzaNovedades, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $idReferencia = $idCobranzaNovedades;
                $row = array (
                    'idReferencia' => $idReferencia,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'sexo' => $sexo,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
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
            $resultado['mensaje'] = "No se encontro rechazo de debito del colegiado";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando notificacion del colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarEnvioDebito($fechaDebito, $tipoDebito, $nombreArchivo, $pathArchivo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "INSERT INTO enviodebito (FechaEnvio, FechaDebito, Tipo, NombreArchivo, PathArchivo) 
        VALUES (NOW(), ?, ?, ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssss', $fechaDebito, $tipoDebito, $nombreArchivo, $pathArchivo);
    $stmt->execute();
    //$stmt->bind_result($idCobranzaNovedades, $idColegiado, $matricula, $sexo, $apellido, $nombres, $mail);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['idEnvioDebito'] = mysqli_stmt_insert_id($stmt);
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando enviodebito";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarEnvioDebitoDetalle($idEnvioDebito, $idDebitoTarjeta) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "INSERT INTO enviodebitodetalle (IdEnvioDebito, IdDebitoTarjeta) 
        VALUES (?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idEnvioDebito, $idDebitoTarjeta);
    $stmt->execute();
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['idEnvioDebitoDetalle'] = mysqli_stmt_insert_id($stmt);
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando enviodebitodetalle";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarEnvioDebitoDetalleCuota($idEnvioDebitoDetalle, $tipoCuota, $referencia, $importe) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "INSERT INTO enviodebitodetallecuota (IdEnvioDebitoDetalle, TipoCuota, IdRelacion, Importe) 
        VALUES (?, ?, ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isis', $idEnvioDebitoDetalle, $tipoCuota, $referencia, $importe);
    $stmt->execute();
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['idEnvioDebitoDetalle'] = mysqli_stmt_insert_id($stmt);
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando enviodebitodetallecuota";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadosDebitarPorTipo($tipoDebito) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT c.Matricula, p.NumeroDocumento, d.NumeroTarjeta, d.PrimerProceso, c.Id, d.IncluyePlanPagos, d.id, d.PagoTotal
        FROM debitotarjeta d
        INNER JOIN colegiado c ON c.Id = d.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento t ON t.Id = c.Estado
        WHERE t.Estado = 'A'
        AND d.Estado = 'A' AND d.Tipo = ?
        ORDER BY c.Matricula";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $tipoDebito);
    $stmt->execute();
    $stmt->bind_result($matricula, $numeroDocumento, $numeroTarjeta, $primerProceso, $idColegiado, $incluyePlanPagos, $idDebitoTarjeta, $pagoTotal);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'matricula' => $matricula,
                    'numeroDocumento' => $numeroDocumento,
                    'numeroTarjeta' => $numeroTarjeta,
                    'primerProceso' => $primerProceso,
                    'idColegiado' => $idColegiado,
                    'incluyePlanPagos' => $incluyePlanPagos,
                    'idDebitoTarjeta' => $idDebitoTarjeta,
                    'pagoTotal' => $pagoTotal
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
            $resultado['mensaje'] = "No se encontron debitos por tipo ".$tipoDebito;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando debitos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerColegiadosDebitarPorCBU() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //agrego en notificacion los datos del colegiado
    $sql = "SELECT c.Matricula, p.NumeroDocumento, d.Tipo, d.CBUBloque1, d.CBUBloque2, d.IdBanco, c.Id, d.IncluyePlanPagos, d.id, d.PagoTotal
        FROM debitotarjeta d
        INNER JOIN colegiado c ON c.Id = d.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento t ON t.Id = c.Estado
        WHERE t.Estado = 'A' AND d.Estado = 'A' 
        ORDER BY c.Matricula";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $tipoDebito);
    $stmt->execute();
    $stmt->bind_result($matricula, $numeroDocumento, $tipoCuenta, $cbuBloque1, $cbuBloque2, $idBanco, $idColegiado, $incluyePlanPagos, $idDebitoTarjeta, $pagoTotal);
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
        if ($resultado['cantidad'] > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'matricula' => $matricula,
                    'numeroDocumento' => $numeroDocumento,
                    'tipoCuenta' => $tipoCuenta,
                    'cbuBloque1' => $cbuBloque1,
                    'cbuBloque2' => $cbuBloque2,
                    'idBanco' => $idBanco,
                    'idColegiado' => $idColegiado,
                    'incluyePlanPagos' => $incluyePlanPagos,
                    'idDebitoTarjeta' => $idDebitoTarjeta,
                    'pagoTotal' => $pagoTotal
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
            $resultado['mensaje'] = "No se encontron debitos por cbu ";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando debitos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function hayLoteDebitoAbierto($tipoDebito) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT COUNT(e.Id)
        FROM enviodebito e
        WHERE e.Tipo = ? AND e.Estado = 'A' AND e.Borrado = 0";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $tipoDebito);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $codigoDeudor = 0;
    $resultado = FALSE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0) {
                $resultado = TRUE;
            }
        }
    }
    return $resultado;
}