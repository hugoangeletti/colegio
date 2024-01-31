<?php
function obtenerConsultorios($calle, $lateral, $numero) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    
    if (isset($calle) && $calle <> "") {
        $calleCC = "cc.Calle LIKE '".$calle."%'";
        $calleCP = "cp.Calle LIKE '".$calle."%'";
    } else {
        $calleCC = NULL;
    }
    
    if (isset($lateral) && $lateral <> "") {
        $conLateralCC = " AND cc.Lateral LIKE '".$lateral."%'";
        $conLateralCP = " AND cp.Lateral LIKE '".$lateral."%'";
    } else {
        $conLateralCC = "";
        $conLateralCP = "";
    }
    
    if (isset($numero) && $numero <> "") {
        $conNumeroCC = " AND cc.Numero LIKE '".$numero."%'";
        $conNumeroCP = " AND cp.Numero LIKE '".$numero."%'";
    } else {
        $conNumeroCC = "";
        $conNumeroCP = "";
    }
    
    if (isset($calleCC)) {
        $sql="(SELECT cc.Id, cc.Calle, cc.Lateral, cc.Numero, l.Nombre, c.Matricula, p.Apellido, p.Nombres, cc.Estado, cc.FechaBaja, cc.FechaHabilitacion, cc.Observacion, 'CONSULTORIO'
            FROM colegiadoconsultorio cc
            LEFT JOIN localidad l ON(l.Id = cc.IdLocalidad)
            INNER JOIN colegiado c ON(c.Id = cc.IdColegiado)
            INNER JOIN persona p ON(p.Id = c.IdPersona)
            WHERE ".$calleCC.$conLateralCC.$conNumeroCC.")

            UNION

            (SELECT cp.Id, cp.Calle, cp.Lateral, cp.Numero, l.Nombre, c.Matricula, p.Apellido, p.Nombres, cp.IdEstado, cp.FechaBaja, cp.FechaCarga, '', 'DOMICILIPROFESIONAL'
            FROM colegiadodomicilioprofesional cp
            LEFT JOIN localidad l ON(l.Id = cp.IdLocalidad)
            INNER JOIN colegiado c ON(c.Id = cp.IdColegiado)
            INNER JOIN persona p ON(p.Id = c.IdPersona)
            WHERE ".$calleCP.$conLateralCP.$conNumeroCP.")";
        
        $stmt = $conect->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id, $calle, $lateral, $numero, $localidadNombre, $matricula, $apellido, $nombre, $estado, $fechaBaja, $fechaHabilitacion, $observacion, $origen);
        $stmt->store_result();

        $resultado = array();
        if (mysqli_stmt_num_rows($stmt) >= 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                if ($estado == 'B' || $estado == '2') {
                    $estado = 'BAJA con fecha: ';
                    if (isset($fechaBaja) && $fechaBaja <> '') {
                        $estado .= cambiarFechaFormatoParaMostrar(substr($fechaBaja, 0, 10));
                    }
                } else {
                    $estado = 'ACTIVO';
                }
                $row = array (
                    'id' => $id,
                    'calle' => $calle,
                    'lateral' => $lateral,
                    'numero' => $numero,
                    'localidad' => $localidadNombre,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'estado' => $estado,
                    'fechaHabilitacion' => $fechaHabilitacion,
                    'observacion' => $observacion,
                    'origen' => $origen
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
            $resultado['mensaje'] = "Error buscando Consultorios";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "FALTAN PARAMETROS";
        $resultado['clase'] = 'alert alert-warning'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
        
    return $resultado;
}
