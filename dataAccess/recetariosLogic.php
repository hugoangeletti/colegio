<?php
function obtenerRecetariosPorAnio($anio){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT recetas.Id, recetas.Entrega, recetas.Fecha, recetas.Serie, recetas.ReciboDesde, 
            recetas.ReciboHasta, recetas.Cantidad, recetas.Estado, colegiado.Matricula, persona.Apellido, 
            persona.Nombres, especialidad.Especialidad, usuario.Usuario 
        FROM recetas 
        INNER JOIN colegiado ON(colegiado.Id = recetas.IdColegiado)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN especialidad ON(especialidad.Id = recetas.IdEspecialidad)
        LEFT JOIN usuario ON(usuario.Id = recetas.IdUsuario)
        WHERE YEAR(Fecha) = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $anio);
    $stmt->execute();
    $stmt->bind_result($id, $entrega, $fecha, $serie, $numeroDesde, $numeroHasta, $cantidad, $estado, $matricula, $apellido, $nombre, $nombreEspecialidad, $usuario);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idRecetas' => $id,
                    'entrega' => $entrega,
                    'fecha' => $fecha,
                    'serie' => $serie,
                    'numeroDesde' => $numeroDesde,
                    'numeroHasta' => $numeroHasta,
                    'cantidad' => $cantidad,
                    'estado' => $estado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).' '.  trim($nombre),
                    'nombreEspecialidad' => $nombreEspecialidad,
                    'nombreUsuario' => $usuario
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
            $resultado['mensaje'] = "No se encontraron recetarios";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando recetarios";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function buscarRecetarios($matricula, $serie, $numero){
    if (isset($matricula) || isset($serie) || isset($numero)) {
        $conMatricula = "";
        if (isset($matricula)) {
            $conMatricula = "colegiado.Matricula = ?";
        }

        $conSerie = "";
        if (isset($serie)) {
            if ($conMatricula != "") {
                $conMatricula .= " AND ";
            }
            $conSerie = "recetas.Serie = ?";
        }
        
        $conNumero = "";
        if (isset($numero)) {
            if ($conMatricula != "" || $conSerie != "") {
                $conNumero = " AND";
            }
            $conNumero .= " ? BETWEEN recetas.ReciboDesde AND recetas.ReciboHasta";
        }
        
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $sql="SELECT recetas.Id, recetas.Entrega, recetas.Fecha, recetas.Serie, recetas.ReciboDesde, 
                recetas.ReciboHasta, recetas.Cantidad, recetas.Estado, colegiado.Matricula, persona.Apellido, 
                persona.Nombres, especialidad.Especialidad, usuario.Usuario 
            FROM recetas 
            INNER JOIN colegiado ON(colegiado.Id = recetas.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN especialidad ON(especialidad.Id = recetas.IdEspecialidad)
            LEFT JOIN usuario ON(usuario.Id = recetas.IdUsuario)
            WHERE 1=1 AND ".$conMatricula.$conSerie.$conNumero;
        
        $stmt = $conect->prepare($sql);
        if (isset($matricula) && isset($serie) && isset($numero)) {
            $stmt->bind_param('isi', $matricula, $serie, $numero);
        } else {
            if (isset($matricula) && isset($serie)) {
                $stmt->bind_param('is', $matricula, $serie);
            } else {
                if (isset($matricula) && isset($numero)) {
                    $stmt->bind_param('ii', $matricula, $numero);
                } else {
                    if (isset($matricula)) {
                        $stmt->bind_param('i', $matricula);
                    } else {
                        if (isset($serie) && isset($numero)) {
                            $stmt->bind_param('si', $serie, $numero);
                        } else {
                            if (isset($serie)) {
                                $stmt->bind_param('s', $serie);
                            } else {
                                $stmt->bind_param('i', $numero);
                            }                            
                        }
                    }
                }
            }        
        }
        $stmt->execute();
        $stmt->bind_result($id, $entrega, $fecha, $serie, $numeroDesde, $numeroHasta, $cantidad, $estado, $matricula, $apellido, $nombre, $nombreEspecialidad, $usuario);
        $stmt->store_result();

        $resultado = array();
        if(mysqli_stmt_errno($stmt)==0) {
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $datos = array();
                while (mysqli_stmt_fetch($stmt)) 
                {
                    $row = array (
                        'idRecetas' => $id,
                        'entrega' => $entrega,
                        'fecha' => $fecha,
                        'serie' => $serie,
                        'numeroDesde' => $numeroDesde,
                        'numeroHasta' => $numeroHasta,
                        'cantidad' => $cantidad,
                        'estado' => $estado,
                        'matricula' => $matricula,
                        'apellidoNombre' => trim($apellido).' '.  trim($nombre),
                        'nombreEspecialidad' => $nombreEspecialidad,
                        'nombreUsuario' => $usuario
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
                $resultado['mensaje'] = "No se encontraron recetarios";
                $resultado['clase'] = 'alert alert-warning'; 
                $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
            }
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "Error buscando recetarios";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Debe ingresar datos ";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}
