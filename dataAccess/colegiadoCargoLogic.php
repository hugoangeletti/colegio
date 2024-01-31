<?php
function obtenerColegiadoCargoPorId($idColegiadoCargo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = 'SELECT cc1.Nombre, cc.FechaDesde, cc.FechaHasta, cc.Estado, cc.FechaMesaDesde, cc.FechaMesaHasta, c.Matricula, p.Apellido, p.Nombres
        FROM colegiadocargo cc
        INNER JOIN cargocolegio cc1 ON cc1.IdCargo = cc.IdCargoColegio
        INNER JOIN colegiado c ON c.Id = cc.IdColegiado
        INNER JOIN persona p ON p.Id = c.IdPersona
        WHERE cc.IdColegiadoCargo = ?';
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoCargo);
    $stmt->execute();
    $stmt->bind_result($nombreCargo, $fechaDesde, $fechaHasta, $estado, $fechaMesaDesde, $fechaMesaHasta, $matricula, $apellido, $nombre);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'nombreCargo' => $nombreCargo,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'estado' => $estado,
                    'fechaMesaDesde' => $fechaMesaDesde,
                    'fechaMesaHasta' => $fechaMesaHasta,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombre
                    );
                array_push($datos, $row);
            }            
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontraron cargos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cargos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
   
}

function obtenerCargosColegioPorColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = 'SELECT colegiadocargo.IdColegiadoCargo, cargocolegio.Nombre, colegiadocargo.FechaDesde, colegiadocargo.FechaHasta, colegiadocargo.Estado
        FROM colegiadocargo 
        INNER JOIN cargocolegio ON(cargocolegio.IdCargo = colegiadocargo.IdCargoColegio)
        WHERE colegiadocargo.IdColegiado = ?';
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $nombre, $fechaDesde, $fechaHasta, $estado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoCargo' => $id,
                    'nombreCargo' => $nombre,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'estado' => $estado
                    );
                array_push($datos, $row);
            }            
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontraron cargos";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando cargos";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
   
}

function obtenerConsejeros() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadocargo.IdColegiadoCargo, colegiado.Id, colegiado.Matricula, persona.Apellido, persona.Nombres, 
        cargocolegio.Nombre, colegiadocargo.FechaDesde, colegiadocargo.FechaHasta, colegiadocontacto.TelefonoFijo, colegiadocontacto.TelefonoMovil, colegiadocontacto.CorreoElectronico
            FROM colegiadocargo
            INNER JOIN colegiado ON(colegiado.Id = colegiadocargo.IdColegiado)
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN cargocolegio ON(cargocolegio.IdCargo = colegiadocargo.IdCargoColegio)
            INNER JOIN colegiadocontacto ON (colegiadocontacto.IdColegiado = colegiado.Id AND colegiadocontacto.IdEstado = 1)
            WHERE cargocolegio.IdTipoCargo = 1 AND colegiadocargo.Estado<>'B'
            ORDER BY persona.Apellido, persona.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idColegiadoCargo, $idColegiado, $matricula, $apellido, $nombres, $nombreCargo, $fechaDesde, $fechaHasta, $telefonoFijo, $telefonoMovil, $mail);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiadoCargo' => $idColegiadoCargo,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'nombreCargo' => $nombreCargo,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'telefonoFijo' => $telefonoFijo,
                    'telefonoMovil' => $telefonoMovil,
                    'mail' => $mail
                );
                array_push($datos, $row);
            }            
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontraron consejeros";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consejeros";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerConsejerosVigentes() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT cc.IdColegiadoCargo, c.Id, c.Matricula, p.Apellido, p.Nombres, cc1.Nombre, cc.FechaDesde, cc.FechaHasta, cdr.Calle, cdr.Lateral, cdr.Numero, cdr.Piso, cdr.Departamento, l.Nombre, cdr.CodigoPostal, cc2.TelefonoFijo, cc2.TelefonoMovil, cc2.CorreoElectronico
        FROM colegiadocargo cc
        INNER JOIN cargocolegio cc1 ON (cc1.IdCargo = cc.IdCargoColegio)
        INNER JOIN colegiado c ON (c.Id = cc.IdColegiado)
        INNER JOIN persona p ON p.Id = c.IdPersona
        LEFT JOIN colegiadodomicilioreal cdr ON (cdr.idColegiado = c.Id and cdr.idEstado = 1)
        LEFT JOIN localidad l ON l.Id = cdr.idLocalidad
        LEFT JOIN colegiadocontacto cc2 ON (cc2.IdColegiado = c.Id and cc2.IdEstado = 1)
        WHERE cc.FechaDesde <= DATE(NOW()) AND cc.FechaHasta >= DATE(NOW())
        AND cc.Estado = 'A'
        AND cc1.IdTipoCargo = 1
        ORDER BY p.Apellido, p.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idColegiadoCargo, $idColegiado, $matricula, $apellido, $nombres, $nombreCargo, $fechaDesde, $fechaHasta, $calle, $lateral, $numeroCasa, $piso, $departamento, $nombreLocalidad, $codigoPostal, $telefonoFijo, $telefonoMovil, $correoElectronico);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $domicilioCompleto = "";
                $localidad = "";
                if ($calle) {
                    $domicilioCompleto = $calle;
                    if ($numeroCasa) {
                        $domicilioCompleto .= " Nº ".$numeroCasa;
                    }
                    if ($lateral) {
                        $domicilioCompleto .= " e/ ".$lateral;
                    }
                    if ($piso && strtoupper($piso) != "NR") {
                        $domicilioCompleto .= " Piso ".$piso;
                    }
                    if ($departamento && strtoupper($departamento) != "NR") {
                        $domicilioCompleto .= " Dto. ".$departamento;
                    }
                    if ($nombreLocalidad) {
                        $localidad = $nombreLocalidad.' ('.$codigoPostal.')';
                    }
                }

                $telefonos = "";
                if ($telefonoFijo && strtoupper($telefonoFijo) != "NR") {
                    $telefonos .= $telefonoFijo.'<br>';
                }
                if ($telefonoMovil && strtoupper($telefonoMovil) != "NR") {
                    $telefonos .= $telefonoMovil.'<br>';
                }

                $row = array (
                    'idColegiadoCargo' => $idColegiadoCargo,
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'nombreCargo' => $nombreCargo,
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'domicilioCompleto' => $domicilioCompleto,
                    'localidad' => $localidad,
                    'telefonos' => $telefonos,
                    'mail' => $correoElectronico
                    );
                array_push($datos, $row);
            }            
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No se encontraron consejeros";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando consejeros";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}
//function obtenerIdColegiado($matricula) {
//    $conect = conectar();
//    mysqli_set_charset( $conect, 'utf8');
//    $sql="SELECT colegiado.Id
//            FROM colegiado 
//            WHERE colegiado.Matricula = ?";
//    $stmt = $conect->prepare($sql);
//    $stmt->bind_param('i', $matricula);
//    $stmt->execute();
//    $stmt->bind_result($idColegiado);
//    $stmt->store_result();
//
//    $resultado = array();
//    if(mysqli_stmt_errno($stmt)==0) {
//        $resultado['estado'] = TRUE;
//        if (mysqli_stmt_num_rows($stmt) > 0) {
//            $row = mysqli_stmt_fetch($stmt);
//            
//            $resultado['idColegiado'] = $idColegiado;
//            $resultado['mensaje'] = "OK";
//            $resultado['clase'] = 'alert alert-success'; 
//            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
//        } else {
//            $resultado['datos'] = NULL;
//            $resultado['mensaje'] = "No hay colegiado ".$matricula;
//            $resultado['clase'] = 'alert alert-info'; 
//            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
//        }
//    } else {
//        $resultado['estado'] = false;
//        $resultado['mensaje'] = "Error buscando colegiado";
//        $resultado['clase'] = 'alert alert-error'; 
//        $resultado['icono'] = 'glyphicon glyphicon-remove';
//    }
//    
//    return $resultado;
//}
//
//function obtenerColegiadoBuscar($idColegiado) {
//    $conect = conectar();
//    mysqli_set_charset( $conect, 'utf8');
//    $sql="SELECT colegiado.Matricula, persona.Apellido, persona.Nombres, persona.NumeroDocumento "
//            . "FROM colegiado "
//            . "INNER JOIN persona ON(persona.Id = colegiado.IdPersona)"
//            . "WHERE colegiado.Id = ?";
//    $stmt = $conect->prepare($sql);
//    $stmt->bind_param('i', $idColegiado);
//    $stmt->execute();
//    $stmt->bind_result($matricula, $apellido, $nombres, $numDocumento);
//    $stmt->store_result();
//
//    $resultado = array();
//    if(mysqli_stmt_errno($stmt)==0)
//    {
//        if (mysqli_stmt_num_rows($stmt) > 0) {
//            $row = mysqli_stmt_fetch($stmt);
//
//            $resultado['estado'] = true;
//            $resultado['mensaje'] = "OK";
//            $resultado['colegiadoBuscar'] = $matricula.' - '.$apellido." ".$nombres." (".$numDocumento.")";
//            $resultado['clase'] = 'alert alert-success'; 
//            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
//        } else {
//            $resultado['estado'] = true;
//            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
//            $resultado['clase'] = 'alert alert-info'; 
//            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
//        }
//    } else {
//        $resultado['estado'] = false;
//        $resultado['mensaje'] = "Error buscando colegiado";
//        $resultado['clase'] = 'alert alert-error'; 
//        $resultado['icono'] = 'glyphicon glyphicon-remove';
//    }
//    
//    return $resultado;
//}
//
//function obtenerColegiadosAutocompletar(){
//    $conect = conectar();
//    mysqli_set_charset( $conect, 'utf8');
//    $sql = "SELECT colegiado.Id, colegiado.Matricula, persona.Apellido, persona.Nombres, persona.NumeroDocumento "
//            . "FROM colegiado "
//            . "INNER JOIN persona ON(persona.Id = colegiado.IdPersona)"
//            . "ORDER BY persona.Apellido, persona.Nombres";
//    $stmt = $conect->prepare($sql);
//    $stmt->execute();
//    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres, $numDocumento);
//    $stmt->store_result();
//
//    $resultado = array();
//    if(mysqli_stmt_errno($stmt)==0)
//    {
//        if (mysqli_stmt_num_rows($stmt) >= 0) 
//        {
//            $datos = array();
//            while (mysqli_stmt_fetch($stmt)) 
//            {
//                $row = array (
//                    'id' => $idColegiado,
//                    'nombre' => $matricula.' - '.$apellido." ".$nombres." (".$numDocumento.")"
//                 );
//                array_push($datos, $row);
//            }
//            $resultado['estado'] = true;
//            $resultado['mensaje'] = "OK";
//            $resultado['datos'] = $datos;
//            $resultado['clase'] = 'alert alert-success'; 
//            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
//        } else {
//            $resultado['estado'] = true;
//            $resultado['mensaje'] = "No hay expedientes";
//            $resultado['clase'] = 'alert alert-info'; 
//            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
//        }
//    } else {
//        $resultado['estado'] = false;
//        $resultado['mensaje'] = "Error buscando expedientes";
//        $resultado['clase'] = 'alert alert-error'; 
//        $resultado['icono'] = 'glyphicon glyphicon-remove';
//    }
//    return $resultado;
//
//}
//
////accesos a tabla colegiadotitulo
//function obtenerTitulosPorColegiado($idColegiado) {
//    $conect = conectar();
//    mysqli_set_charset( $conect, 'utf8');
//    $sql="SELECT colegiadotitulo.IdColegiadoTitulo, colegiadotitulo.FechaTitulo, tipotitulo.Nombre AS TipoTitulo,
//            universidad.Nombre AS Universidad
//            FROM colegiadotitulo
//            INNER JOIN tipotitulo ON(tipotitulo.IdTipoTitulo = colegiadotitulo.IdTipoTitulo)
//            INNER JOIN universidad ON(universidad.Id = colegiadotitulo.IdUniversidad)
//            WHERE colegiadotitulo.IdColegiado = ?";
//    $stmt = $conect->prepare($sql);
//    $stmt->bind_param('i', $idColegiado);
//    $stmt->execute();
//    $stmt->bind_result($idColegiadoTitulo, $fechaTitulo, $tipoTitulo, $universidad);
//    $stmt->store_result();
//
//    $resultado = array();
//    if(mysqli_stmt_errno($stmt)==0) {
//        $resultado['estado'] = TRUE;
//        if (mysqli_stmt_num_rows($stmt) > 0) {
//            $row = mysqli_stmt_fetch($stmt);
//            $datos = array(
//                    'idColegiadoTitulo' => $idColegiadoTitulo,
//                    'fechaTitulo' => $fechaTitulo,
//                    'tipoTitulo' => $tipoTitulo,
//                    'universidad' => $universidad
//                    );
//            
//            $resultado['datos'] = $datos;
//            $resultado['mensaje'] = "OK";
//            $resultado['clase'] = 'alert alert-success'; 
//            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
//        } else {
//            $resultado['datos'] = NULL;
//            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
//            $resultado['clase'] = 'alert alert-info'; 
//            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
//        }
//    } else {
//        $resultado['estado'] = false;
//        $resultado['mensaje'] = "Error buscando colegiado";
//        $resultado['clase'] = 'alert alert-error'; 
//        $resultado['icono'] = 'glyphicon glyphicon-remove';
//    }
//    
//    return $resultado;
//}
//
//function obtenerDetalleTipoEstado($tipoEstado){
//    switch ($tipoEstado) {
//        case 'A':
//            return 'Activo - ';
//            break;
//
//        case 'C':
//            return 'Baja - ';
//            break;
//
////        case 'F':
////            return 'Fallecido';
////            break;
////
////        case 'J':
////            return 'Jubilado';
////            break;
////
////        case 'J':
////            return 'Inscripto al Distrito I';
////            break;
////
//        default:
//            return '';
//            break;
//    }
//}