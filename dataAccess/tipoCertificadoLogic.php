<?php
function obtenerTipoCertificadoFiltrado($codigoDeudor) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    //condicionantes por codigoDeudor
    if ($codigoDeudor > '1') {
        $filtro = "AND DeudaPeriodosAnteriores = 'S'";
    } else {
        if ($codigoDeudor == '1') {
            $filtro = "AND DeudaPeriodoActual = 'S'";
        } else {
            $filtro = "";
        }
    }
    
    $sql="SELECT Id, Detalle, ImprimeConDeuda, ImprimirSinFotoFirma, ParaExterior
        FROM tipocertificado
        WHERE Estado = 'A' ".$filtro."
        ORDER BY Detalle";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $imprimeConDeuda, $imprimirSinFotoFirma, $paraExterior);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'id' => $id,
                    'nombre' => $nombre,
                    'imprimeConDeuda' => $imprimeConDeuda,
                    'imprimirSinFotoFirma' => $imprimirSinFotoFirma,
                    'paraExterior' => $paraExterior
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
            $resultado['mensaje'] = "No se encontraron Tipo de Certificado.";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipo de Certificado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTipoCertificadoPorId($id){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tipocertificado WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $imprimeConDeuda, $estado, $deudaPeriodoActual, $deudaPeriodosAnteriores, $conFirma, $muestraDestino, $imprimirSinFotoFirma, $paraExterior);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'id' => $id,
                    'detalle' => $detalle,
                    'imprimeConDeuda' => $imprimeConDeuda,
                    'estado' => $estado,
                    'deudaPeriodoActual' => $deudaPeriodoActual,
                    'deudaPeriodosAnteriores' => $deudaPeriodosAnteriores,
                    'conFirma' => $conFirma,
                    'muestraDestino' => $muestraDestino,
                    'imprimirSinFotoFirma' => $imprimirSinFotoFirma,
                    'paraExterior' => $paraExterior
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontro Tipo Certificado";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipo Certificado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
    
}