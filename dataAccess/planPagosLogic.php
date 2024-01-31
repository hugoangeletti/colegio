<?php
function obtenerPlanPagosPorEstado($estado){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagos.Id, planpagos.FechaCreacion, planpagos.ImporteTotal, planpagos.Cuotas,
                colegiado.Matricula, persona.Apellido, persona.Nombres, planpagos.IdColegiado
            FROM planpagos
            Inner join colegiado on(colegiado.Id = planpagos.IdColegiado)
            inner join persona on(persona.Id = colegiado.IdPersona)
            WHERE planpagos.Estado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($idPlaPago, $fechaCreacion, $importe, $cuotas, $matricula, $apellido, $nombre, $idColegiado);
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
                    'idPlanPago' => $idPlaPago,
                    'fechaCreacion' => $fechaCreacion,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellido.' '.$nombre,
                    'idColegiado' => $idColegiado
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
            $resultado['mensaje'] = "No hay Planes de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Planes de pago";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}

function obtenerCuotaPlanPagoPorIdColegiadoVto($idColegiado, $fechaSegundoVencimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT planpagos.Id, planpagos.FechaCreacion, planpagos.ImporteTotal, planpagos.Cuotas,
                colegiado.Matricula, persona.Apellido, persona.Nombres, planpagos.IdColegiado
            FROM planpagos
            Inner join colegiado on(colegiado.Id = planpagos.IdColegiado)
            inner join persona on(persona.Id = colegiado.IdPersona)
            WHERE planpagos.Estado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $estado);
    $stmt->execute();
    $stmt->bind_result($idPlaPago, $fechaCreacion, $importe, $cuotas, $matricula, $apellido, $nombre, $idColegiado);
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
                    'idPlanPago' => $idPlaPago,
                    'fechaCreacion' => $fechaCreacion,
                    'importe' => $importe,
                    'cuotas' => $cuotas,
                    'matricula' => $matricula,
                    'apellidoNombre' => $apellido.' '.$nombre,
                    'idColegiado' => $idColegiado
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
            $resultado['mensaje'] = "No hay Planes de pago";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando Planes de pago";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
    
}