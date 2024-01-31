<?php
function obtenerTipoMovimiento() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tipomovimiento ORDER BY DetalleCompleto";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $detalleCompleto, $rehabilitable, $generaCtaCte, $estado, $mesaEntradas, $temporalidad, $motivoInactividad);
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
                    'detalle' => $detalle,
                    'detalleCompleto' => $detalleCompleto,
                    'rehabilitable' => $rehabilitable,
                    'paraExterior' => $generaCtaCte,
                    'estado' => $estado,
                    'mesaEntradas' => $mesaEntradas,
                    'temporalidad' => $temporalidad,
                    'motivoInactividad' => $motivoInactividad
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
            $resultado['mensaje'] = "No se encontraron Tipo de Movimiento.";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipo de Movimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerTipoMovimientoPorId($id){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM tipomovimiento WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($id, $detalle, $detalleCompleto, $rehabilitable, $generaCtaCte, $estado, $mesaEntradas, $temporalidad, $motivoInactividad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'id' => $id,
                    'detalle' => $detalle,
                    'detalleCompleto' => $detalleCompleto,
                    'rehabilitable' => $rehabilitable,
                    'paraExterior' => $generaCtaCte,
                    'estado' => $estado,
                    'mesaEntradas' => $mesaEntradas,
                    'temporalidad' => $temporalidad,
                    'motivoInactividad' => $motivoInactividad
                );

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontro Tipo Movimiento";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Tipo Movimiento";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
    
}