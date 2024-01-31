<?php
function tieneTituloEspecialistaParaRetirar($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT COUNT(tituloespecialista.IdTituloEspecialista)
            FROM tituloespecialista
            INNER JOIN resoluciondetalle ON(resoluciondetalle.Id = tituloespecialista.IdResolucionDetalle)
            WHERE resoluciondetalle.IdColegiado = ?
            AND tituloespecialista.FechaEmision >= '2016-01-01'
            AND tituloespecialista.FechaEntrega is null";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            $resultado['estado'] = TRUE;
        }
    }
    
    return $resultado;
}

function tieneCostas($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT sum(colegiadosanciongasto.CantidadGalenos)
        FROM colegiadosanciongasto
        INNER JOIN colegiadosancion ON(colegiadosancion.id = colegiadosanciongasto.IdColegiadoSancion)
        WHERE colegiadosancion.IdColegiado = ?
        AND (colegiadosanciongasto.FechaPago is NULL OR colegiadosanciongasto.FechaPago = 0)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($cantidadGalenos);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidadGalenos > 0) {
                $resultado['estado'] = TRUE;
                $resultado['costas'] = $cantidadGalenos;
            }
        }
    }
    
    return $resultado;
}

function tieneDocumentacionParaRetirar($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT rd.Id, tdr.Nombre
        FROM retirodocumentacion rd
        INNER JOIN tipodocumentacionretiro tdr ON tdr.Id = rd.IdTipoDocumentacionRetiro
        WHERE rd.IdColegiado = ? AND rd.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idRetiroDocumentacion, $tipoDocumentacionRetiro);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idRetiroDocumentacion' => $idRetiroDocumentacion,
                    'tipoDocumentacionRetiro' => $tipoDocumentacionRetiro
                );
                array_push($datos, $row);
            }
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
        } else {
            $resultado['estado'] = FALSE;
        }
    }
    
    return $resultado;
}

function tienePagosPotTituloEspecialista($idColegiado, $fechaDesde, $fechaHasta) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cajadiariamovimientodetalle.CodigoPago, tipopago.Detalle, 
            cajadiariamovimientootro.Descripcion, cajadiariamovimiento.Fecha, 
            cajadiariamovimientodetalle.Monto
            FROM cajadiariamovimientodetalle
            INNER JOIN cajadiariamovimiento on(cajadiariamovimiento.Id = cajadiariamovimientodetalle.IdCajaDiariaMovimiento)
            LEFT JOIN cajadiariamovimientootro on(cajadiariamovimientootro.IdCajaDiariaMovimiento = cajadiariamovimiento.Id)
            INNER JOIN tipopago on(tipopago.Id = cajadiariamovimientodetalle.CodigoPago)
            WHERE cajadiariamovimiento.IdColegiado = ?
            AND cajadiariamovimiento.Fecha BETWEEN ? AND ?
            AND cajadiariamovimientodetalle.CodigoPago in(55, 72, 59, 38, 61, 37, 52, 56)
            AND cajadiariamovimiento.Estado <> 'A'
            ORDER BY cajadiariamovimiento.Fecha";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iss', $idColegiado, $fechaDesde, $fechaHasta);
    $stmt->execute();
    $stmt->bind_result($codigoPago, $detalle, $descripcion, $fechaPago, $monto);
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'codigoPago' => $codigoPago,
                    'detalle' => $detalle,
                    'descripcion' => $descripcion,
                    'fechaPago' => $fechaPago,
                    'monto' => $monto
                );
                array_push($datos, $row);
            }
            $resultado['estado'] = TRUE;
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRO PAGO";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando PAGO";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerColegiadoAutoprescripcion($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT mesaentrada.IdMesaEntrada, mesaentrada.FechaIngreso, usuario.Usuario, 
        mesaentradaautoprescripcion.Autorizado, mesaentradaautoprescripcion.DocumentoAutorizado, 
        mesaentradaautoprescripcion.Parentezco, mesaentradaautoprescripcion.Autorizado2, 
        mesaentradaautoprescripcion.DocumentoAutorizado2, mesaentradaautoprescripcion.Parentezco2
    FROM mesaentrada 
    INNER JOIN mesaentradaautoprescripcion ON(mesaentradaautoprescripcion.IdMesaEntrada = mesaentrada.IdMesaEntrada)
    LEFT JOIN usuario ON(usuario.Id = mesaentrada.IdUsuario)
    WHERE mesaentrada.IdColegiado = ? AND mesaentrada.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idMesaEntrada, $fechaIngreso, $nombreUsuario, $autorizado1, $documento1, $parentezco1, $autorizado2, $documento2, $parentezco2);
    $stmt->store_result();

    $resultado['estado'] = FALSE;
    if(mysqli_stmt_errno($stmt)==0)
    {
        $datos = array();
        if (mysqli_stmt_num_rows($stmt) > 0) {
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $idMesaEntrada,
                    'fechaIngreso' => $fechaIngreso,
                    'nombreUsuario' => $nombreUsuario,
                    'autorizado1' => $autorizado1,
                    'documento1' => $documento1,
                    'parentezco1' => $parentezco1,
                    'autorizado2' => $autorizado2,
                    'documento2' => $documento2,
                    'parentezco2' => $parentezco2
                );
                array_push($datos, $row);
            }
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok';
        } else {
            $resultado['mensaje'] = "NO SE ENCONTRO AUTOPRESCRIPCION";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
        $resultado['estado'] = TRUE;
        $resultado['datos'] = $datos;
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando AUTOPRESCRIPCION";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function tieneExpediente($idColegiado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT (COUNT(*) + (SELECT COUNT(*) FROM eticaexpedientedenunciados eed WHERE eed.IdColegiado = ?)) AS cantidad
        FROM eticaexpediente ee
        WHERE ee.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idColegiado, $idColegiado);
    $stmt->execute();

    $stmt->bind_result($cantidad);

    $stmt->store_result();
    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}
