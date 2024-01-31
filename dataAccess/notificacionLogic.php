<?php
function obtenerNotificaciones($idNotificacionNota, $anio) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT n.*, nd.Tema, p.Apellido, p.Nombres, (SELECT COUNT(*) FROM notificacioncolegiado nc WHERE nc.IdNotificacion = n.IdNotificacion) AS Cantidad_Matriculas
		FROM notificacion n
		INNER JOIN notificacionnota nd ON nd.IdNotificacionNota = n.IdNotificacionNota
		LEFT JOIN colegiado c ON c.Matricula = n.Matricula
		LEFT JOIN persona p ON p.Id = c.IdPersona
		WHERE n.IdNotificacionNota = ? AND YEAR(n.FechaCreacion) = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idNotificacionNota, $anio);
    $stmt->execute();
    $stmt->bind_result($idNotificacion, $idNotificacionNota, $fechaCreacion, $fechaEmision, $idUsuario, $estado, $filtroDeudores, $tipoEnvio, $periodoDesde, $periodoHasta, $matricula, $cuotasAdeudadas, $fechaVencimiento, $periodoProceso, $temaNotificacion, $apellido, $nombre, $cantidadMatriculasConDeuda);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idNotificacion' => $idNotificacion,
                    'idNotificacionNota' => $idNotificacionNota,
                    'fechaCreacion' => $fechaCreacion,
                    'fechaEmision' => $fechaEmision,
                    'idUsuario' => $idUsuario,
                    'estado' => $estado,
                    'filtroDeudores' => $filtroDeudores,
                    'tipoEnvio' => $tipoEnvio,
                    'periodoDesde' => $periodoDesde,
                    'periodoHasta' => $periodoHasta,
                    'matricula' => $matricula,
                    'cuotasAdeudadas' => $cuotasAdeudadas,
                    'fechaVencimiento' => $fechaVencimiento,
                    'periodoProceso' => $periodoProceso,
                    'temaNotificacion' => $temaNotificacion,
                    'apellidoNombre' => trim($apellido).' '.trim($nombre),
                    'cantidadMatriculasConDeuda' => $cantidadMatriculasConDeuda
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
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No existen notificaciones";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando notificaciones";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;

}

function agregarNotificacion($idNotificacionNota, $filtroDeudores, $tipoEnvio, $fechaVencimiento, $periodoDesde, $periodoHasta) {
	$conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();

    $sql = "INSERT INTO notificacion (IdNotificacionNota, FechaCreacion, IdUsuario, Estado, FiltroDeudores, TipoEnvio, PeriodoDesde, PeriodoHasta, FechaVencimiento)
            VALUES(?, time(NOW()), ?, ?, 'A')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssi', $fechaCaja, $saldoInicial, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $idCajaDiaria = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
        $resultado['idCajaDiaria'] = $idCajaDiaria;
        $resultado['mensaje'] = 'OK';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';            	
    } else {
        $resultado['estado'] = FALSE;
        $resultado['idCajaDiaria'] = NULL;
        $resultado['mensaje'] = 'ERROR al abrir cajadiaria';
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';            	
    }
    return $resultado;
}

