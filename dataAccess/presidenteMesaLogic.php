<?php
function obtenerPresidentesMesaParaNotificar($anio, $rango) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT elmtm.IdELMTurnoMatricula, c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, 
        cc.CorreoElectronico, elm.Detalle, elm.Fecha, elt.HoraDesde, elt.HoraHasta
        FROM eleccioneslocalidadmesaturnomatricula elmtm
        INNER JOIN eleccioneslocalidadmesaturno elmt ON(elmt.IdELMTurno = elmtm.IdELMTurno)
        INNER JOIN eleccioneslocalidadturno elt ON(elt.IdELTurno = elmt.IdELTurno)
        INNER JOIN eleccioneslocalidadmesa elm ON(elm.IdELMesa = elmt.IdELMesa)
        INNER JOIN eleccioneslocalidad el ON(el.IdEleccionesLocalidad = elm.IdEleccionesLocalidad)
        INNER JOIN elecciones e ON(e.IdElecciones = el.IdElecciones)
        INNER JOIN colegiado c ON(c.Id = elmtm.IdColegiado AND c.Estado IN(1, 5, 10))
        INNER JOIN persona p ON(p.Id = c.IdPersona) 
        INNER JOIN colegiadocontacto cc ON(c.Id = cc.idColegiado AND cc.idEstado = 1)
        LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = elmtm.IdColegiado
            AND enviomaildiariocolegiado.IdReferencia = elmtm.IdELMTurnoMatricula)
        WHERE e.Anio = ? AND e.Estado = 'A'
            AND enviomaildiariocolegiado.Id IS NULL
        LIMIT ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $anio, $rango);
    $stmt->execute();
    $stmt->bind_result($idPresidente, $idColegiado, $matricula, $apellido, $nombres, $sexo, $mail, $mesa, $fecha, $horaDesde, $horaHasta);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                /*
                if ($sexo == 'M') {
                    $sexo = 'Masculino';
                } else {
                    $sexo = 'Femenino';
                }
                */
                $dia = substr($fecha, 8, 2);
                $mes = obtenerMes(substr($fecha, 5, 2));
                $laFecha = $dia.' de '.$mes;
                $row = array (
                    'idReferencia' => $idPresidente,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombres' => $nombres,
                    'sexo' => $sexo,
                    'mail' => $mail,
                    'mesa' => $mesa,
                    'fecha' => $laFecha,
                    'hora' => substr($horaDesde, 0, 5).' a '.substr($horaHasta, 0, 5)
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
            $resultado['mensaje'] = "No hay personas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando personas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

