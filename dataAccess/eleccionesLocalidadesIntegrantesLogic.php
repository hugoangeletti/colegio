<?php
function obtenerIntegrantesPorIdEleccionesLocalidadLista($idEleccionesLocalidadLista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidadlistaintegrantes.IdELListaIntegrante, eleccioneslocalidadlistaintegrantes.Matricula, 
        eleccioneslocalidadlistaintegrantes.Cargo, eleccioneslocalidadlistaintegrantes.Orden, 
        eleccioneslocalidadlistaintegrantes.Estado, CONCAT(persona.Apellido, ' ', persona.Nombres) AS ApellidoNombre,
        colegiado.Id
        FROM eleccioneslocalidadlistaintegrantes 
        INNER JOIN colegiado ON(colegiado.Matricula = eleccioneslocalidadlistaintegrantes.Matricula)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        WHERE eleccioneslocalidadlistaintegrantes.IdELLista = ?
        AND eleccioneslocalidadlistaintegrantes.Estado = 'A'
        ORDER BY eleccioneslocalidadlistaintegrantes.Cargo DESC, eleccioneslocalidadlistaintegrantes.Orden";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadLista);
    $stmt->execute();
    $stmt->bind_result($id, $matricula, $cargo, $orden, $estado, $apellidoNombre, $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idEleccionesLocalidadIntegrante' => $id,
                        'matricula' => $matricula,
                        'apellidoNombre' => $apellidoNombre,
                        'cargo' => $cargo,
                        'orden' => $orden,
                        'estado' => $estado,
                        'idColegiado' => $idColegiado
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
            $resultado['mensaje'] = "No hay integrantes de la lista";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando integrantes de la lista";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerEleccionesLocalidadListaIntegrantesPorId($idEleccionesLocalidadListaIntegrante) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidadlistaintegrantes.IdELLista, eleccioneslocalidadlistaintegrantes.Matricula, 
        eleccioneslocalidadlistaintegrantes.Cargo, eleccioneslocalidadlistaintegrantes.Orden, 
        eleccioneslocalidadlistaintegrantes.Estado, CONCAT(persona.Apellido, ' ', persona.Nombres) AS ApellidoNombre,
        colegiado.Id
        FROM eleccioneslocalidadlistaintegrantes 
        LEFT JOIN colegiado ON(colegiado.Matricula = eleccioneslocalidadlistaintegrantes.Matricula)
        LEFT JOIN persona ON(persona.Id = colegiado.IdPersona)
        WHERE eleccioneslocalidadlistaintegrantes.IdELListaIntegrante = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadListaIntegrante);
    $stmt->execute();
    $stmt->bind_result($idELLista, $matricula, $cargo, $orden, $estado, $apellidoNombre, $idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                        'idEleccionesLocalidad' => $idELLista,
                        'matricula' => $matricula,
                        'apellidoNombre' => $apellidoNombre,
                        'cargo' => $cargo,
                        'orden' => $orden,
                        'estado' => $estado,
                        'idColegiado' => $idColegiado
                    );
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay listas";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando listas de la localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarEleccionesLocalidadesListaIntegrantes($idEleccionesLocalidadLista, $matricula, $apellidoNombre, $cargo, $orden) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="INSERT INTO eleccioneslocalidadlistaintegrantes (IdELLista, Matricula, ApellidoNombre, Cargo, Orden) 
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iissi', $idEleccionesLocalidadLista, $matricula, $apellidoNombre, $cargo, $orden);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Integrante HA SIDO AGREGADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL AGREGAR Integrante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result;
}

function editarEleccionesLocalidadesListaIntegrante($idEleccionesLocalidadListaIntegrante, $matricula, $apellidoNombre, $cargo, $orden) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eleccioneslocalidadlistaintegrantes 
            SET Matricula = ?, ApellidoNombre = ?, Cargo = ?, Orden = ?
            WHERE IdELListaIntegrante = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('issii', $matricula, $apellidoNombre, $cargo, $orden, $idEleccionesLocalidadListaIntegrante);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if ($stmt->errno == 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Integrante HA SIDO MODIFICADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL MODIFICAR Integrante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function borrarEleccionesLocalidadesListaIntegrante($idEleccionesLocalidadListaIntegrante){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="UPDATE eleccioneslocalidadlistaintegrantes
          SET Estado = 'B' 
          WHERE IdELListaIntegrante = ?";
    
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadListaIntegrante);
    $stmt->execute();
    $stmt->store_result();
    $result = array(); 
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'Integrante HA SIDO BORRADO';
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'ERROR AL BORRAR Integrante';
    }
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    return $result; 
}

function obtenerCantidadIntegrantesPorCargo($idEleccionesLocalidadLista, $cargo){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT  COUNT(eleccioneslocalidadlistaintegrantes.IdELListaIntegrante) AS Cantidad
            FROM eleccioneslocalidadlistaintegrantes
            WHERE eleccioneslocalidadlistaintegrantes.IdELLista = ?
            AND eleccioneslocalidadlistaintegrantes.Cargo = ?
            AND eleccioneslocalidadlistaintegrantes.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $idEleccionesLocalidadLista, $cargo);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        $row = mysqli_stmt_fetch($stmt);
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['cantidad'] = $cantidad;
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando listas de la localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerObservacionesIntegrante($idEleccionesLocalidadListaIntegrante) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidadlistaintegrantes.IdELLista, eleccioneslocalidadlistaintegrantes.Matricula,
        eleccioneslocalidadlistaintegrantes.Cargo, eleccioneslocalidadlistaintegrantes.Orden, 
        tipomovimiento.Estado, CONCAT(persona.Apellido, ' ', persona.Nombres) AS ApellidoNombre, 
        colegiado.FechaMatriculacion, zonas.Zona, colegiado.Id, zonas.Nombre
        FROM eleccioneslocalidadlistaintegrantes 
        INNER JOIN colegiado ON(colegiado.Matricula = eleccioneslocalidadlistaintegrantes.Matricula)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado)
        INNER JOIN colegiadodomicilioreal ON(colegiadodomicilioreal.idColegiado = colegiado.Id and colegiadodomicilioreal.idEstado = 1)
        INNER JOIN localidad ON(localidad.Id = colegiadodomicilioreal.idLocalidad)
        INNER JOIN zonas ON(zonas.Id = localidad.idZona)
        WHERE eleccioneslocalidadlistaintegrantes.IdELListaIntegrante = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idEleccionesLocalidadListaIntegrante);
    $stmt->execute();
    $stmt->bind_result($idELLista, $matricula, $cargo, $orden, $estadoMatricular, $apellidoNombre, $fechaMatriculacion, $zona, $idColegiado, $zonaNombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            //obtengo el estado actual con tesoreria
            $periodoActual = $_SESSION['periodoActual'];
            $resEstadoTeso = estadoTesoreriaPorColegiado($idColegiado, $periodoActual);
            if ($resEstadoTeso['estado']){
                $codigo = $resEstadoTeso['codigoDeudor'];
                $resEstadoTesoreria = estadoTesoreria($codigo);
                if ($resEstadoTesoreria['estado']){
                    $estadoTesoreria = $resEstadoTesoreria['estadoTesoreria'];
                } else {
                    $estadoTesoreria = $resEstadoTesoreria['mensaje'];
                }
            } else {
                $estadoTesoreria = $resEstadoTeso['mensaje'];
            }

            $aniosColegiado = calcular_edad($fechaMatriculacion);
            $laAntiguedad = explode(" ", $aniosColegiado);
            $edad = $laAntiguedad[0];
            $antiguedad = 'Menos de 2 años';
            if (2<= $edad && $edad<=10) {
                $antiguedad = 'C (Más de 2 años)';
            } elseif ($edad>10) {
                $antiguedad = 'T (Más de 10 años)';
            }

            $datos = array (
                        'idEleccionesLocalidad' => $idELLista,
                        'matricula' => $matricula,
                        'apellidoNombre' => $apellidoNombre,
                        'cargo' => $cargo,
                        'orden' => $orden,
                        'estadoMatricular' => $estadoMatricular,
                        'estadoTesoreria' => $estadoTesoreria,
                        'antiguedad' => $antiguedad,
                        'zona' => $zona,
                        'zonaNombre' => $zonaNombre
                    );
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay listas";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando listas de la localidad";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function matriculaExisteEnLista($matricula, $idEleccionesLocalidadLista) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT eleccioneslocalidadlista.IdELLista, eleccioneslocalidad.Localidad, 
        eleccioneslocalidad.LocalidadDetalle, eleccioneslocalidadlista.Nombre, 
        eleccioneslocalidadlista.TipoLista, eleccioneslocalidadlistaintegrantes.Cargo, 
        eleccioneslocalidadlistaintegrantes.Orden
        FROM eleccioneslocalidadlistaintegrantes
        LEFT JOIN eleccioneslocalidadlista ON(eleccioneslocalidadlista.IdELLista = eleccioneslocalidadlistaintegrantes.IdELLista)
        LEFT JOIN eleccioneslocalidad ON(eleccioneslocalidad.IdEleccionesLocalidad = eleccioneslocalidadlista.IdEleccionesLocalidad)
        LEFT JOIN elecciones ON(elecciones.IdElecciones = eleccioneslocalidad.IdElecciones)
        WHERE Matricula = ?
        AND eleccioneslocalidadlistaintegrantes.Estado = 'A'
        AND elecciones.Anio = YEAR(NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $matricula);
    $stmt->execute();
    $stmt->bind_result($id, $localidad, $localidadDetalle, $listaNombre, $tipoLista, $cargo, $orden);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $otraLista = '';
            $resultado['estado'] = false;
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($id <> $idEleccionesLocalidadLista) {
                    //si no es de la misma lista, entonces lo cargo en el arreglo
                    if ($otraLista <> '') {
                        $otraLista .= '<br>';
                    }
                    switch ($cargo) {
                        case 'T':
                            $cargo = 'Titular';
                            break;

                        case 'S':
                            $cargo = 'Suplente';
                            break;

                        default:
                            break;
                    }
                    $otraLista .= 'Lista: <b>'.$listaNombre.'</b> de <b>'.$localidadDetalle.'</b> Cargo: <b>'.$cargo.'</b> Orden: <b>'.$orden.'</b>';
                    $resultado['estado'] = true;
                }
            }
            $resultado['otraLista'] = $otraLista;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay integrantes de la lista";
            $resultado['clase'] = 'alert alert-warning'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando integrantes de la lista";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

