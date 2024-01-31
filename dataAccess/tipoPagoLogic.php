<?php
function obtenerTiposPago() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tipopago WHERE Estado = 'A' ORDER BY Detalle";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $importe, $cuentaContable, $codigoConcepto, $idConcepto, $estado, $cantidadHoras);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => $nombre,
                'importe' => $importe,
                'cuentaContable' => $cuentaContable,
                'codigoConcepto' => $codigoConcepto,
                'idConcepto' => $idConcepto,
                'estado' => $estado,
                'cantidadHoras' => $cantidadHoras
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
        $resultado['mensaje'] = "Error buscando Bancos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTipoValorPorId($idTipoPago) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tipopago WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idTipoPago);
    $stmt->execute();

    $stmt->bind_result($id, $nombre, $importe, $cuentaContable, $codigoConcepto, $idConcepto, $estado, $cantidadHoras);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($id)) {
            $datos = array(
                'id' => $id,
                'nombre' => $nombre,
                'importe' => $importe,
                'cuentaContable' => $cuentaContable,
                'codigoConcepto' => $codigoConcepto,
                'idConcepto' => $idConcepto,
                'estado' => $estado,
                'cantidadHoras' => $cantidadHoras
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se ecntontro el tipo de pago";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando tipo de pago";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function ontenerTiposPagoParaRecibo() {
     $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT tp.Id, tp.Detalle, tp.Importe
            FROM tipopago tp
            WHERE tp.IdConcepto IN(7, 8, 9, 11) AND tp.Estado = 'A';";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $importe);
    $stmt->store_result();

    $resultado = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $datos = array();
        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'id' => $id,
                'nombre' => $nombre,
                'importe' => $importe
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
        $resultado['mensaje'] = "Error buscando Bancos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

