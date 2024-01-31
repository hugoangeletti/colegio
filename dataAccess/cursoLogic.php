<?php
function obtenerAsistentePorId($idAsistente){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ca.IdCursos, ca.ApellidoNombre, ca.IdColegiado, cur.Titulo
		FROM cursosasistente ca
        INNER JOIN cursos cur ON cur.Id = ca.IdCursos
		WHERE ca.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAsistente);
    $stmt->execute();
    $stmt->bind_result($idCurso, $apellidoNombre, $idColegiado, $tituloCurso);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
		$datos = array (
                'idCurso' => $idCurso,
                'apellidoNombre' => $apellidoNombre,
                'idColegiado' => $idColegiado,
                'tituloCurso' => $tituloCurso
            );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando asistente";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerAsistentePorIdCuotaCurso($idCursosAsistenteCuota){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ca.Id, ca.IdCursos, cac.Cuota, cac.Importe, cac.FechaVencimiento
        FROM cursosasistente ca
        INNER JOIN cursosasistentecuotas cac ON cac.IdCursosAsistente = ca.Id
        WHERE cac.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCursosAsistenteCuota);
    $stmt->execute();
    $stmt->bind_result($idAsistente, $idCurso, $cuota, $importe, $fechaVencimiento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array (
                'idAsistente' => $idAsistente,
                'idCurso' => $idCurso,
                'cuota' => $cuota,
                'importe' => $importe,
                'fechaVencimiento' => $fechaVencimiento
            );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando lotes";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerNombreCursoAsistente($idAsistente) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT c.Titulo
        FROM cursos c
        INNER JOIN cursosasistente ca ON ca.IdCursos = c.Id
        WHERE ca.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAsistente);
    $stmt->execute();
    $stmt->bind_result($titulo);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['titulo'] = $titulo;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando lotes";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;
}

function obtenerCuotasCursoAPagar($idAsistente) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id, Cuota, Importe, FechaVencimiento, DetalleCuota
        FROM cursosasistentecuotas 
        WHERE IdCursosAsistente = ? AND (FechaPago IS NULL OR FechaPago = '0000-00-00')";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAsistente);
    $stmt->execute();
    $stmt->bind_result($id, $cuota, $importe, $fechaVencimiento, $detalleCuota);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idCursosAsistenteCuota' => $id,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'fechaVencimiento' => $fechaVencimiento,
                    'detalleCuota' => $detalleCuota
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
            $resultado['mensaje'] = "No hay cuotas a pagar";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando cuots a pagar";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerCuotasCursosParaHomeBanking($fechaVencimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cac.Id, ca.IdCursos, cac.Cuota, cac.Importe, cac.FechaVencimiento, ca.Id
        FROM cursosasistentecuotas cac
        INNER JOIN cursosasistente ca ON (ca.Id = cac.IdCursosAsistente AND ca.Estado = 'S')
        WHERE  ca.IdCursos IN(308, 314, 319, 322, 326, 331, 332)
        AND cac.FechaVencimiento <= ?
        AND (cac.FechaPago = '0000-00-00' OR cac.FechaPago IS NULL)
        ORDER BY ca.Id, cac.Id";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $fechaVencimiento);
    $stmt->execute();
    $stmt->bind_result($idCursosAsistenteCuota, $idCursos, $cuota, $importe, $fechaVencimiento, $idCursosAsistente);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) {
                $row = array (
                    'idCursosAsistenteCuota' => $id,
                    'idCursos' => $idCursos,
                    'cuota' => $cuota,
                    'importe' => $importe,
                    'fechaVencimiento' => $fechaVencimiento,
                    'idCursosAsistente' => $idCursosAsistente
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
            $resultado['mensaje'] = "No hay cuotas a pagar";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando cuots a pagar";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }
    
    return $resultado;
}

function obtenerAsistentesAutocompletar(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT ca.Id, c.Matricula, ca.ApellidoNombre, cur.Titulo, ca.Estado
            FROM cursosasistente ca
            INNER JOIN cursos cur on cur.Id = ca.IdCursos
            LEFT JOIN colegiado c on c.Id = ca.IdColegiado
            WHERE ca.Estado = 'S' AND cur.Estado = 'A'
            ORDER BY ca.ApellidoNombre";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idCursosAsistente, $matricula, $apellidoNombre, $titulo, $asiste);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($asiste == 'S') {
                    $asiste = 'Asiste';
                } else {
                    $asiste = 'NO ASISTE';
                }
                $row = array (
                    'id' => $idCursosAsistente,
                    'nombre' => trim($apellidoNombre).' - '.trim($titulo).' ('.$asiste.') - '.$matricula
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay asistente";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando asistente";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerCuotaCursoPorId($idCursosAsistenteCuota) {
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT *
        FROM cursosasistentecuotas
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idAsistente);
    $stmt->execute();
    $stmt->bind_result($idCursosAsistenteCuota, $idCursosAsistente, $cuota, $importe, $fechaVencimiento, $fechaPago, $recibo, $detalleCuota, $fechaActualizacion);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array (
                'idCursosAsistenteCuota' => $idCursosAsistenteCuota,
                'idCursosAsistente' => $idCursosAsistente,
                'cuota' => $cuota,
                'importe' => $importe,
                'fechaVencimiento' => $fechaVencimiento,
                'fechaPago' => $fechaPago,
                'recibo' => $recibo,
                'detalleCuota' => $detalleCuota,
                'fechaActualizacion' => $fechaActualizacion
            );
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['datos'] = $datos;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando cuota";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
    }

    return $resultado;

}