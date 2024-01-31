<?php
//accesos a tabla colegiado
function obtenerColegiadoPorMatricula($matricula) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Id, colegiado.Matricula, colegiado.Tomo, colegiado.Folio, colegiado.FechaMatriculacion, 
            colegiado.MatriculaNacional, colegiado.DistritoOrigen, persona.Apellido, persona.Nombres, persona.Sexo, 
            persona.NumeroDocumento, persona.FechaNacimiento, tipomovimiento.Detalle AS MovDetalle, 
            tipomovimiento.DetalleCompleto AS MovDetalleCompleto, tipomovimiento.Estado AS TipoEstado, 
            tipodocumento.Nombre AS TipoDocumento, paises.Nacionalidad, colegiado.Estado, colegiadotitulo.FechaTitulo,
            colegiado.Estado
            FROM colegiado 
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN colegiadotitulo ON(colegiadotitulo.IdColegiado = colegiado.Id)
            INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado)
            INNER JOIN tipodocumento ON(tipodocumento.IdTipoDocumento = persona.TipoDocumento)
            INNER JOIN paises ON(paises.Id = persona.IdPaises)
            WHERE colegiado.Matricula = ? 
            LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $matricula);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $tomo, $folio, $fechaMatriculacion, $matriculaNacional, $distritoOrigen, $apellido, $nombres, $sexo, $numeroDocumento, $fechaNacimiento, $detalleMovimiento, $movimientoCompleto, $tipoEstado, $tipoDocumento, $nacionalidad, $estado, $fechaTitulo, $idEstadoMatricular);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'tomo' => $tomo,
                    'folio' => $folio,
                    'fechaMatriculacion' => $fechaMatriculacion,
                    'matriculaNacional' => $matriculaNacional,
                    'distritoOrigen' => $distritoOrigen,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'sexo' => $sexo, 
                    'numeroDocumento' => $numeroDocumento,
                    'fechaNacimiento' => $fechaNacimiento,
                    'detalleMovimiento' => $detalleMovimiento,
                    'movimientoCompleto' => $movimientoCompleto,
                    'tipoEstado' => $tipoEstado,
                    'tipoDocumento' => $tipoDocumento, 
                    'nacionalidad' => $nacionalidad,
                    'estado' => $estado,
                    'fechaTitulo' => $fechaTitulo,
                    'idEstadoMatricular' => $idEstadoMatricular
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiadoPorId($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Matricula, colegiado.Tomo, colegiado.Folio, colegiado.FechaMatriculacion, 
            colegiado.MatriculaNacional, colegiado.DistritoOrigen, persona.Apellido, persona.Nombres, persona.Sexo, 
            persona.NumeroDocumento, persona.FechaNacimiento, tipomovimiento.Detalle AS MovDetalle, 
            tipomovimiento.DetalleCompleto AS MovDetalleCompleto, tipomovimiento.Estado AS TipoEstado, 
            tipodocumento.Nombre AS TipoDocumento, paises.Nacionalidad, colegiado.Estado, colegiadotitulo.FechaTitulo,
            colegiado.Estado, colegiadotitulo.Digital
            FROM colegiado 
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
            INNER JOIN colegiadotitulo ON(colegiadotitulo.IdColegiado = colegiado.Id)
            INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado)
            INNER JOIN tipodocumento ON(tipodocumento.IdTipoDocumento = persona.TipoDocumento)
            INNER JOIN paises ON(paises.Id = persona.IdPaises)
            WHERE colegiado.Id = ? 
            LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($matricula, $tomo, $folio, $fechaMatriculacion, $matriculaNacional, $distritoOrigen, $apellido, $nombres, $sexo, $numeroDocumento, $fechaNacimiento, $detalleMovimiento, $movimientoCompleto, $tipoEstado, $tipoDocumento, $nacionalidad, $estado, $fechaTitulo, $idEstadoMatricular, $tituloDigital);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'matricula' => $matricula,
                    'tomo' => $tomo,
                    'folio' => $folio,
                    'fechaMatriculacion' => $fechaMatriculacion,
                    'matriculaNacional' => $matriculaNacional,
                    'distritoOrigen' => $distritoOrigen,
                    'apellido' => $apellido,
                    'nombre' => $nombres,
                    'sexo' => $sexo, 
                    'numeroDocumento' => $numeroDocumento,
                    'fechaNacimiento' => $fechaNacimiento,
                    'detalleMovimiento' => $detalleMovimiento,
                    'movimientoCompleto' => $movimientoCompleto,
                    'tipoEstado' => $tipoEstado,
                    'tipoDocumento' => $tipoDocumento, 
                    'nacionalidad' => $nacionalidad,
                    'estado' => $estado,
                    'fechaTitulo' => $fechaTitulo,
                    'idEstadoMatricular' => $idEstadoMatricular,
                    'tituloDigital' => $tituloDigital
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerIdColegiado($matricula) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Id
            FROM colegiado 
            WHERE colegiado.Matricula = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $matricula);
    $stmt->execute();
    $stmt->bind_result($idColegiado);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $resultado['idColegiado'] = $idColegiado;
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No hay colegiado ".$matricula;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerColegiadosActivos($matricula){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if (isset($matricula) && $matricula > 0) {
        $porMatricula = "AND c.Matricula = ".$matricula;
    } else {
        $porMatricula = "";
    }
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.NumeroDocumento 
        FROM colegiado c
        INNER JOIN persona p ON p.Id = c.IdPersona
        INNER JOIN tipomovimiento tm ON tm.Id = c.Estado
        WHERE tm.Estado = 'A' ".$porMatricula."
        ORDER BY c.Matricula";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres, $numDocumento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellido' => trim($apellido),
                    'nombre' => trim($nombres),
                    'numeroDocumento' => $numDocumento
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
            $resultado['mensaje'] = "No hay Matriculas activas";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Matriculas activas";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function obtenerColegiadosAutocompletar($tipo){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    if ($tipo == 'activos') {
        $activos = "INNER JOIN tipomovimiento tm ON(tm.Id = c.Estado AND tm.Estado NOT IN('F', 'J'))";
    } else {
        $activos = "";
    }
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.NumeroDocumento 
            FROM colegiado c
            INNER JOIN persona p ON(p.Id = c.IdPersona)
            ".$activos."
            ORDER BY p.Apellido, p.Nombres";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres, $numDocumento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'id' => $idColegiado,
                    'nombre' => $matricula.' - '.trim($apellido)." ".trim($nombres)." (DNI ".$numDocumento.")"
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
            $resultado['mensaje'] = "No hay expedientes";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando expedientes";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function agregarColegiado($tomo, $folio, $fechaMatriculacion, $matricula, $estado, $apellido, $nombres, $sexo, $tipoDocumento, 
            $numeroDocumento, $fechaNacimiento, $idPaises, $matriculaNacional, $distritoOrigen, $calle, $numero, $piso, $depto, 
            $lateral, $idLocalidad, $codigoPostal, $telefonoFijo, $telefonoMovil, $mail, $idTipoTitulo, $fechaTitulo, $idUniversidad, $tituloDigital, $fechaRevalida){
    
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        //primero agrego la persona, con el idPersona, luego agrego al colegiado y sus tablas hijas
        $sql="INSERT INTO persona 
            (Apellido, Nombres, Sexo, TipoDocumento, NumeroDocumento, FechaNacimiento, IdPaises, FechaCarga) 
            VALUES (?, ?, ?, ?, ?, ?, ?, DATE(NOW()))";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sssiisi', $apellido, $nombres, $sexo, $tipoDocumento, $numeroDocumento, $fechaNacimiento, $idPaises);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego el movimiento para hacer el seguimiento
            $idPersona = mysqli_stmt_insert_id($stmt);
            $sql="INSERT INTO log_tabla 
                (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                VALUES ('persona', ?, now(), 'alta', ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $idPersona, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['idPersona'] = $idPersona;
                $resultado['mensaje'] = 'LA PERSONA HA SIDO AGREGADA';
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] .= "ERROR AL AGREGAR PERSONA";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL AGREGAR PERSONA";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            //continua agregando tablas
            //ahora inserto colegiado
            $sql="INSERT INTO colegiado 
                (Matricula, Tomo, Folio, FechaMatriculacion, Estado, MatriculaNacional, FechaCarga, IdPersona, DistritoOrigen) 
                VALUES (?, ?, ?, ?, ?, ?, DATE(NOW()), ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iiisiiii', $matricula, $tomo, $folio, $fechaMatriculacion, $estado, $matriculaNacional, $idPersona, $distritoOrigen);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                //agrego el movimiento para hacer el seguimiento
                $idColegiado = mysqli_stmt_insert_id($stmt);
                $sql="INSERT INTO log_tabla 
                    (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                    VALUES ('colegiado', ?, now(), 'alta', ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idColegiado, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->store_result();
                if(mysqli_stmt_errno($stmt)<>0) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_LOG";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }

            if ($resultado['estado']) {
                //agrega los datos del domicilio real
                $sql="INSERT INTO colegiadodomicilioreal
                    (idColegiado, Calle, Lateral, Numero, Piso, Departamento, idLocalidad, CodigoPostal, idEstado, FechaCarga, idUsuario, idOrigen) 
                    VALUE (?, ?, ?, ?, ?, ?, ?, ?, 1, date(now()), ".$_SESSION['user_id'].", 2)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('isssssis', $idColegiado, $calle, $lateral, $numero, $piso, $depto, $idLocalidad, $codigoPostal);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt)==0) {
                    $idColegiadoDomicilio = $conect->insert_id;
                    $sql="INSERT INTO log_tabla 
                        (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                        VALUES ('colegiadodomicilioreal', ?, now(), 'alta', ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('ii', $idColegiadoDomicilio, $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $resultado['idColegiadoDomicilio'] = $idColegiadoDomicilio;
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_DOMICILIO_LOG";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL AGREGAR DOMICILIO.";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }

            if ($resultado['estado']) {
                //agrega los datos de contacto
                $sql="INSERT INTO colegiadocontacto
                    (IdColegiado, TelefonoFijo, TelefonoMovil, CorreoElectronico, IdEstado, FechaCarga, IdUsuario, IdOrigen) 
                    VALUE (?, ?, ?, ?, 1, date(now()), ".$_SESSION['user_id'].", 2)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('isss', $idColegiado, $telefonoFijo, $telefonoMovil, $mail);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt)==0) {
                    $idColegiadoContacto = $conect->insert_id;
                    $sql="INSERT INTO log_tabla 
                        (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                        VALUES ('colegiadocontacto', ?, now(), 'alta', ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('ii', $idColegiadoContacto, $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $resultado['idColegiadoContacto'] = $idColegiadoContacto;
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_CONTACTO_LOG";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] .= "ERROR AL AGREGAR CONTACTO.";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }
            
            if ($resultado['estado']) {
                //agrega los datos de contacto
                $sql="INSERT INTO colegiadotitulo
                        (IdColegiado, IdTipoTitulo, IdUniversidad, FechaTitulo, FechaCarga, IdUsuario, Digital, FechaRevalida) 
                        VALUE (?, ?, ?, ?, date(now()), ".$_SESSION['user_id'].", ?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('iiisis', $idColegiado, $idTipoTitulo, $idUniversidad, $fechaTitulo, $tituloDigital, $fechaRevalida);
                $stmt->execute();
                $stmt->store_result();
                if (mysqli_stmt_errno($stmt)==0) {
                    $idColegiadoTitulo = $conect->insert_id;
                    $sql="INSERT INTO log_tabla 
                        (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                        VALUES ('colegiadotitulo', ?, now(), 'alta', ?)";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('ii', $idColegiadoTitulo, $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->store_result();
                    if(mysqli_stmt_errno($stmt)==0) {
                        $resultado['idColegiadoTitulo'] = $idColegiadoTitulo;
                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_TITULO_LOG";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO TITULO";
                    $resultado['clase'] = 'alert alert-error'; 
                    $resultado['icono'] = 'glyphicon glyphicon-remove';
                }
            }
            
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = $resultado['mensaje'];
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }

        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL COLEGIADO HA SIDO AGREGADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $resultado['idColegiado'] = $idColegiado;
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }     
}

function actualizarEstado($idColegiado, $idTipoMovimiento) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="UPDATE colegiado
        SET Estado = ?
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idTipoMovimiento, $idColegiado);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $sql="INSERT INTO log_tabla 
            (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
            VALUES ('colegiado', ?, now(), 'modificaEstado', ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $idColegiado, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = 'ESTADO ACTUALIZADO';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] .= "ERROR AL MODIFICAR ESTADO LOG";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] .= "ERROR AL MODIFICAR ESTADO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function cantidadColegiadosPorAntiguedad($fechaCalculo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql = "(SELECT 1, COUNT(c.Id)
        FROM colegiado c
        INNER JOIN colegiadotitulo ct ON ct.IdColegiado = c.Id
        WHERE c.Estado IN(0, 1, 5, 10)
        AND ct.FechaTitulo >= ?)

        UNION

        (SELECT 2, COUNT(c.Id)
        FROM colegiado c
        INNER JOIN colegiadotitulo ct ON ct.IdColegiado = c.Id
        WHERE c.Estado IN(0, 1, 5, 10)
        AND ct.FechaTitulo < ?)
        ";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ss', $fechaCalculo, $fechaCalculo);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($antiguedad, $cantidad);
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'antiguedad' => $antiguedad,
                    'cantidad' => $cantidad
                    );
                array_push($datos, $row);
            }   
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRARON COLEGIADOS";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando colegiados";
    }
    return $resultado;
}
//fin tabla colegiado

//accesos a tabla colegiadotitulo
function obtenerTitulosPorColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT ct.IdColegiadoTitulo, ct.FechaTitulo, tipotitulo.Nombre AS TipoTitulo,
            universidad.Nombre AS Universidad, ct.IdTipoTitulo, ct.IdUniversidad, ct.Digital, ct.FechaRevalida
            FROM colegiadotitulo ct
            INNER JOIN tipotitulo ON(tipotitulo.IdTipoTitulo = ct.IdTipoTitulo)
            INNER JOIN universidad ON(universidad.Id = ct.IdUniversidad)
            WHERE ct.IdColegiado = ? LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($idColegiadoTitulo, $fechaTitulo, $tipoTitulo, $universidad, $idTipoTitulo, $idUniversidad, $digital, $fechaRevalida);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiadoTitulo' => $idColegiadoTitulo,
                    'fechaTitulo' => $fechaTitulo,
                    'tipoTitulo' => $tipoTitulo,
                    'universidad' => $universidad,
                    'idTipoTitulo' => $idTipoTitulo,
                    'idUniversidad' => $idUniversidad,
                    'digital' => $digital,
                    'fechaRevalida' => $fechaRevalida
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerTitulosPorIdColegiadoTitulo($idColegiadoTitulo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiadotitulo.IdColegiado, colegiadotitulo.FechaTitulo, tipotitulo.Nombre AS TipoTitulo,
            universidad.Nombre AS Universidad, colegiadotitulo.IdTipoTitulo, colegiadotitulo.IdUniversidad, colegiadotitulo.Digital
            FROM colegiadotitulo
            INNER JOIN tipotitulo ON(tipotitulo.IdTipoTitulo = colegiadotitulo.IdTipoTitulo)
            INNER JOIN universidad ON(universidad.Id = colegiadotitulo.IdUniversidad)
            WHERE colegiadotitulo.IdColegiadoTitulo = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiadoTitulo);
    $stmt->execute();
    $stmt->bind_result($idColegiado, $fechaTitulo, $tipoTitulo, $universidad, $idTipoTitulo, $idUniversidad, $tituloDigital);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiadoTitulo' => $idColegiadoTitulo,
                    'idColegiado' => $idColegiado,
                    'fechaTitulo' => $fechaTitulo,
                    'tipoTitulo' => $tipoTitulo,
                    'universidad' => $universidad,
                    'idTipoTitulo' => $idTipoTitulo,
                    'idUniversidad' => $idUniversidad,
                    'tituloDigital' => $tituloDigital
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiadoTitulo ".$idColegiadoTitulo;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}


function generarDeudaAnual_anterior2020($idColegiado, $fechaTitulo, $estado) {
    //agrega colegiacion anual y cuotas
    $periodoActual = $_SESSION['periodoActual'];
    $fechaDesde = $periodoActual.'-06-30';
    $fechaHasta = ($periodoActual+1).'-04-30';
    $fechaTituloMinima = $periodoActual.'-05-31';

    //si el alta se produce dentro de la vigencia del periodo, entonces se genera deuda
    if (date('Y-m-d') > $fechaDesde && date('Y-m-d') <= $fechaHasta) {
        try {
            /* Autocommit false para la transaccion */
            $conect = conectar();
            mysqli_set_charset( $conect, 'utf8');
            $conect->autocommit(FALSE);
            $resultado = array();
            $resultado['estado'] = TRUE;
            //calculo antiguedad y obtengo las cuotas a generar
            if ($fechaTitulo > $fechaTituloMinima) {
                $antiguedad = 1;
            } else {
                $antiguedad = calcular_antiguedad($fechaTitulo, $fechaTituloMinima);
            }
            //echo $fechaTitulo.' - '.$antiguedad;
            //obtenemos las cuotas del periodo actual
            $sql = "SELECT Id, Valor, Cuotas, PagoTotal, VtoPagoTotal
                    FROM valoranualcolegiacion WHERE Periodo = ? AND Antiguedad = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $periodoActual, $antiguedad);
            $stmt->execute();
            $stmt->bind_result($idValorAnualColegiacion, $importeTotal, $cuotas, $pagoTotal, $vtoPagoTotal);
            $stmt->store_result();
            if (mysqli_stmt_errno($stmt)==0) {
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $row = mysqli_stmt_fetch($stmt);
                    $sql="INSERT INTO colegiadodeudaanual
                            (IdColegiado, Periodo, Importe, Cuotas, Antiguedad, EstadoMatricular, FechaCreacion, Estado) 
                            VALUE (?, ?, ?, ?, ?, ?, date(now()), 'A')";
                    $stmt = $conect->prepare($sql);
                    $stmt->bind_param('iisiii', $idColegiado, $periodoActual, $importeTotal, $cuotas, $antiguedad, $estado);
                    $stmt->execute();
                    $stmt->store_result();
                    if (mysqli_stmt_errno($stmt)==0) {
                        $idColegiadoDeudaAnual = $conect->insert_id;
                        $sql="INSERT INTO log_tabla 
                            (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario) 
                            VALUES ('colegiadodeudaanual', ?, now(), 'alta', ?)";
                        $stmt = $conect->prepare($sql);
                        $stmt->bind_param('ii', $idColegiadoDeudaAnual, $_SESSION['user_id']);
                        $stmt->execute();
                        $stmt->store_result();
                        if(mysqli_stmt_errno($stmt)==0) {
                            $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
                            //agrego las cuotas
                            $sql = "SELECT Id, Cuota, ValorColegiacion, FechaVencimiento, SegundoVencimiento, Recargo
                                    FROM valorcuotacolegiacion WHERE IdValorAnualColegiacion = ?";
                            $stmt = $conect->prepare($sql);
                            $stmt->bind_param('i', $idValorAnualColegiacion);
                            $stmt->execute();
                            $stmt->bind_result($idValorCuotaColegiacion, $cuota, $importe, $primerVencimiento, $segundoVencimiento, $recargo);
                            $stmt->store_result();
                            if (mysqli_stmt_errno($stmt)==0) {
                                if (mysqli_stmt_num_rows($stmt) > 0) {
                                    while (mysqli_stmt_fetch($stmt) && $resultado['estado']) 
                                    {
                                        $estadoCuota = 1;
                                        //$fechaCompara = sumarRestarSobreFecha(date('Y-m-d'), 10, 'day', '-');
                                        //si la fecha de vencimiento de la cuota es dentro de 10 dias, entonces no se cobra
                                        if ($primerVencimiento <= sumarRestarSobreFecha(date('Y-m-d'), 10, 'day', '+')) {
                                            $estadoCuota = 5;
                                        }

                                        if (!isset($recargo)) {
                                            $recargo = $importe;
                                        }
                                        $sql1 = "INSERT INTO colegiadodeudaanualcuotas
                                                (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento, Estado) 
                                                VALUE (?, ?, ?, ?, ?, ?, ?)";
                                        $stmt1 = $conect->prepare($sql1);
                                        $stmt1->bind_param('iissssi', $idColegiadoDeudaAnual, $cuota, $importe, $primerVencimiento, $recargo, $segundoVencimiento, $estadoCuota);
                                        $stmt1->execute();
                                        $stmt1->store_result();
                                        if (mysqli_stmt_errno($stmt1)<>0) {
                                            $resultado['estado'] = FALSE;
                                            $resultado['mensaje'] .= "ERROR AL AGREGAR CUOTAS DE COLEGIACION";
                                            $resultado['clase'] = 'alert alert-error'; 
                                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                                        }
                                    }

                                    //se inserta el pago total si no esta vencido
                                    if ($resultado['estado'] && $vtoPagoTotal > date('Y-m-d')) {
                                        $sql1 = "INSERT INTO colegiadodeudaanualtotal
                                                (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado) 
                                                VALUE (?, ?, ?, ?)";
                                        $stmt1 = $conect->prepare($sql1);
                                        $stmt1->bind_param('issi', $idColegiadoDeudaAnual, $pagoTotal, $vtoPagoTotal, $estadoCuota);
                                        $stmt1->execute();
                                        $stmt1->store_result();
                                        if (mysqli_stmt_errno($stmt1)<>0) {
                                            $resultado['estado'] = FALSE;
                                            $resultado['mensaje'] .= "ERROR AL AGREGAR PAGO TOTAL DE COLEGIACION";
                                            $resultado['clase'] = 'alert alert-error'; 
                                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                                        }
                                    }
                                }
                            } else {
                                $resultado['estado'] = FALSE;
                                $resultado['mensaje'] .= "ERROR AL BUSCAR CUOTAS DE COLEGIACION";
                                $resultado['clase'] = 'alert alert-error'; 
                                $resultado['icono'] = 'glyphicon glyphicon-remove';
                            }
                        } else {
                            $resultado['estado'] = FALSE;
                            $resultado['mensaje'] .= "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL_LOG";
                            $resultado['clase'] = 'alert alert-error'; 
                            $resultado['icono'] = 'glyphicon glyphicon-remove';
                        }

                    } else {
                        $resultado['estado'] = FALSE;
                        $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO_DEUDA_ANUAL";
                        $resultado['clase'] = 'alert alert-error'; 
                        $resultado['icono'] = 'glyphicon glyphicon-remove';
                    }
                } else {
                    //no hay cuotas para generar la colegiacion
                    $resultado['estado'] = TRUE;
                    $resultado['mensaje'] = "NO SE GENERO LA DEUDA ANUAL, NO HAY PERIODO PARA LIQUIDAR";
                    $resultado['clase'] = 'alert alert-info'; 
                    $resultado['icono'] = 'glyphicon glyphicon-info-sign';
                }
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL BUSCAR valoranualcolegiacion";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
            
            if ($resultado['estado']) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = 'SE GENERO LA DEUDA ANUAL CORRECTAMENTE';
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
                $resultado['idColegiadoDeudaAnual'] = $idColegiadoDeudaAnual;
                $conect->commit();
                desconectar($conect);
                return $resultado;
            } else {
                $conect->rollback();
                desconectar($conect);
                return $resultado;
            }
        } catch (mysqli_sql_exception $e) {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = 'NO SE GENERA DEUDA, ESTA FUERA DEL PERIODO VIGENTE';
        $resultado['clase'] = 'alert alert-info'; 
        $resultado['icono'] = 'glyphicon glyphicon-info-sign'; 
        return $resultado;
    }
}

function agregarColegiadoTitulo($idColegiado, $idTipoTitulo, $fechaTitulo, $idUniversidad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array(); 
    $sql="INSERT INTO colegiadotitulo
            (IdColegiado, IdTipoTitulo, IdUniversidad, FechaTitulo, FechaCarga, IdUsuario) 
            VALUE (?, ?, ?, ?, date(now()), ".$_SESSION['user_id'].")";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i11s', $idColegiado, $idTipoTitulo, $idUniversidad, $fechaTitulo);
    $stmt->execute();
    $stmt->store_result();
    $resultado = array(); 
    if (mysqli_stmt_errno($stmt)==0) {
        $idColegiadoTitulo = $conect->insert_id;
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "COLEGIADO TITULO SE AGREGO CON EXITO";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO TITULO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
        
    return $resultado;
}
//fin colegiadotitulo

function obtenerDetalleTipoEstado($tipoEstado){
    switch ($tipoEstado) {
        case 'A':
            $estado = 'Activo';
            break;

        case 'C':
            $estado = 'Baja';
            break;

//        case 'F':
//            return 'Fallecido';
//            break;
//
//        case 'J':
//            return 'Jubilado';
//            break;
//
        case 'I':
            $estado = 'Inscripto al Distrito I';
            break;

        default:
            $estado = '';
            break;
    }
    
    return $estado;
}

function obtenerFirmaPorCargo($idCargo){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT cargocolegio.Nombre, colegiado.Matricula, persona.Apellido, persona.Nombres
        FROM colegiadocargo
        INNER JOIN cargocolegio ON(cargocolegio.IdCargo = colegiadocargo.IdCargoColegio)
        INNER JOIN colegiado ON(colegiado.Id = colegiadocargo.IdColegiado)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        WHERE colegiadocargo.IdCargoColegio = ?
        AND DATE(NOW()) BETWEEN colegiadocargo.FechaMesaDesde AND colegiadocargo.FechaMesaHasta
        ORDER BY colegiadocargo.IdCargoColegio DESC
        LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idCargo);
    $stmt->execute();
    $stmt->bind_result($nombreCargo, $matricula, $apellido, $nombres);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'nombreCargo' => $nombreCargo,
                    'matricula' => $matricula,
                    'apellido' => $apellido,
                    'nombre' => $nombres
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay Secretario General";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Secretario General";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerNuevoTomoFolioMatricula(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Tomo, colegiado.Folio, colegiado.Matricula
        FROM colegiado
        WHERE colegiado.DistritoOrigen = 1
        ORDER BY colegiado.Matricula DESC
        LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($tomo, $folio, $matricula);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $matricula++;
            if ($matricula>199999) {
                $matricula = 1010000;
            }
            
            $datos = array(
                'tomo' => $tomo,
                'folio' => $folio,
                'matricula' => $matricula
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro ultimo tomo y folio";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando ultimo tomo y folio";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function obtenerNuevoTomoFolioOtroDistrito(){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Tomo, colegiado.Folio
        FROM colegiado
        WHERE colegiado.DistritoOrigen != 1
        ORDER BY  colegiado.Tomo DESC, colegiado.Folio DESC
        LIMIT 1";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($tomo, $folio);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $datos = array(
                'tomo' => $tomo,
                'folio' => $folio);
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No se encontro ultimo tomo y folio";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando ultimo tomo y folio";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function matriculaExiste($matricula) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT COUNT(Id) AS Cantidad FROM colegiado WHERE Matricula = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $matricula);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "Numero de Matricula YA EXISTE EN LA BASE DE DATOS";
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No existe Numero de Matricula";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al buscar el Numero de Matricula";
    }
    
    return $resultado;
}

function obtenerColegiadoNota($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT * FROM colegiadonota WHERE colegiadonota.IdColegiado = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($id, $nota, $idColegiado, $idUsuario, $fechaCarga);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'idColegiadoNota' => $id,
                    'nota' => $nota,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay nota ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando nota";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function agregarColegiadoNota($idColegiado, $nota) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //primero agrego la persona, con el idPersona, luego agrego al colegiado y sus tablas hijas
    $sql="INSERT INTO colegiadonota
        (IdColegiado, Nota, IdUsuario, FechaCarga) 
        VALUES (?, ?, ?, NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isi', $idColegiado, $nota, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $idColegiadoNota = $conect->insert_id;
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "COLEGIADO NOTA SE AGREGO CON EXITO";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO NOTA";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function editarColegiadoNota($idColegiadoNota, $nota) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    //primero agrego la persona, con el idPersona, luego agrego al colegiado y sus tablas hijas
    $sql="UPDATE colegiadonota
        SET Nota = ?, IdUsuario = ?, FechaCarga = NOW()
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sii', $nota, $_SESSION['user_id'], $idColegiadoNota);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "COLEGIADO NOTA SE AGREGO CON EXITO";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR COLEGIADO NOTA";
        $resultado['clase'] = 'alert alert-danger'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;    
}

function obtenerColegiadosPaginacion($inicio, $limite, $buscar, $orden){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    if (isset($buscar)) {
        //agregar la busqueda
        if (is_numeric($buscar)) {
            $conBusqueda = "WHERE (colegiado.Matricula = ".$buscar." or persona.NumeroDocumento = ".$buscar.")";
        } else {
            $conBusqueda = "WHERE (persona.Apellido like '".$buscar."%')";
        }
    } else {
        $conBusqueda = " ";
    }
    
    $ordenado = "ORDER BY colegiado.Matricula";
    if (isset($orden) && $orden == 'A') {
        $ordenado = "ORDER BY persona.Apellido, persona.Nombres";
    }
    $sql = "SELECT colegiado.Id, colegiado.Matricula, persona.Apellido, persona.Nombres, 
            persona.NumeroDocumento, tm.Detalle 
            FROM colegiado
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona) 
            INNER JOIN tipomovimiento tm ON(tm.Id = colegiado.Estado)
            ".$conBusqueda." ".$ordenado." 
            LIMIT ".$inicio.", ".$limite."";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($idColegiado, $matricula, $apellido, $nombres, $numeroDocumento, $tipoMovimiento);
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'apellidoNombre' => trim($apellido).', '.trim($nombres),
                    'numeroDocumento' => $numeroDocumento,
                    'tipoMovimiento' => $tipoMovimiento
                    );
                array_push($datos, $row);
            }   
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRARON COLEGIADOS";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando colegiados";
    }
    return $resultado;
}

function obtenerCantidadMatriculasPaginacion($buscar){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    if (isset($buscar)) {
        //agregar la busqueda
        if (is_numeric($buscar)) {
            $conBusqueda = "WHERE (colegiado.Matricula = ".$buscar." or persona.NumeroDocumento = ".$buscar.")";
        } else {
            $conBusqueda = "WHERE (persona.Apellido like '".$buscar."%')";
        }
    } else {
        $conBusqueda = " ";
    }
    $sql = "SELECT COUNT(colegiado.Id) as Cantidad
            FROM colegiado
            INNER JOIN persona ON(persona.Id = colegiado.IdPersona) ".$conBusqueda;
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cantidad);

    if(mysqli_stmt_errno($stmt)==0) {
        $estadoConsulta = TRUE;
        $mensaje = 'OK';
        $row = mysqli_stmt_fetch($stmt);
        $resultado['estado'] = $estadoConsulta;
        $resultado['mensaje'] = $mensaje;
        $resultado['cantidad'] = $cantidad;
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando reclamos";
    }
    return $resultado;
}

function obtenerColegiadoParaLiquidacion($periodo, $fechaCalculoAntiguedad) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql = "SELECT c.Id, c.Matricula, c.Estado, ct.FechaTitulo, TIMESTAMPDIFF(YEAR,ct.FechaTitulo, ?) AS Antiguedad
            FROM colegiado c
            INNER JOIN colegiadotitulo ct ON ct.IdColegiado = c.Id
            LEFT JOIN colegiadodeudaanual cda ON (cda.IdColegiado = c.Id AND cda.Periodo = ?)
            WHERE c.Estado in(0, 1, 5, 10) AND cda.Id IS NULL 
            ORDER BY ct.FechaTitulo
            limit 50";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $fechaCalculoAntiguedad, $periodo);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($idColegiado, $matricula, $estado, $fechaTitulo, $antiguedad);
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                if ($antiguedad < 5) {
                    $antiguedad = 1;
                } else {
                    $antiguedad = 2;
                }
                $row = array (
                    'idColegiado' => $idColegiado,
                    'matricula' => $matricula,
                    'estado' => $estado,
                    'fechaTitulo' => $fechaTitulo,
                    'antiguedad' => $antiguedad
                    );
                array_push($datos, $row);
            }   
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRARON COLEGIADOS";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error buscando colegiados";
    }
    return $resultado;
}

function obtenerMatriculaPorIdColegiado($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Tomo, colegiado.Folio, colegiado.FechaMatriculacion, colegiado.MatriculaNacional
            FROM colegiado 
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($tomo, $folio, $fechaMatriculacion, $matriculaNacional);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'tomo' => $tomo,
                    'folio' => $folio,
                    'fechaMatriculacion' => $fechaMatriculacion,
                    'matriculaNacional' => $matriculaNacional
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;    
}

function modificarMatricula($idColegiado, $fechaMatriculacion, $tomo, $folio, $matriculaNacional, $datosAnteriores) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        //primero agrego la persona, con el idPersona, luego agrego al colegiado y sus tablas hijas
        $sql="UPDATE colegiado
            SET FechaMatriculacion = ?, Tomo = ?, Folio = ?, MatriculaNacional = ?
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('siisi', $fechaMatriculacion, $tomo, $folio, $matriculaNacional, $idColegiado);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO log_tabla 
                (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
                VALUES ('colegiado', ?, now(), 'modificacion', ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iis', $idColegiado, $_SESSION['user_id'], serialize($datosAnteriores));
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "DATOS DE LA MATRICULACION SE ACTUALIZARON CON EXITO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR DATOS MATRICULACION";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }

        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR DATOS DE LA MATRICULACION";
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL COLEGIADO HA SIDO ACTUALIZADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function modificarTitulo($idColegiadoTitulo, $idTipoTitulo, $fechaTitulo, $idUniversidad, $datosAnteriores, $tituloDigital) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        
        //primero agrego la persona, con el idPersona, luego agrego al colegiado y sus tablas hijas
        $sql="UPDATE colegiadotitulo
            SET IdTipoTitulo = ?, FechaTitulo = ?, IdUniversidad = ?, Digital = ?
            WHERE IdColegiadoTitulo = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isiii', $idTipoTitulo, $fechaTitulo, $idUniversidad, $tituloDigital, $idColegiadoTitulo);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO log_tabla 
                (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
                VALUES ('colegiadotitulo', ?, now(), 'modificacion', ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iis', $idColegiadoTitulo, $_SESSION['user_id'], serialize($datosAnteriores));
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "DATOS DEL TITULO SE ACTUALIZARON CON EXITO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR DATOS DEL TITULO";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }

        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL ACTUALIZAR DATOS DEL TITULO";
            $resultado['clase'] = 'alert alert-danger'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL TITULO HA SIDO ACTUALIZADO CORRECTAMENTE';
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            $conect->commit();
            desconectar($conect);
            return $resultado;
        } else {
            $conect->rollback();
            desconectar($conect);
            return $resultado;
        }
    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

define("NACIONALIDAD_UNIVERSIDAD", 54);

/*
function obtenerColegiadoBuscar($idColegiado) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT colegiado.Matricula, persona.Apellido, persona.Nombres, persona.NumeroDocumento "
            . "FROM colegiado "
            . "INNER JOIN persona ON(persona.Id = colegiado.IdPersona)"
            . "WHERE colegiado.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idColegiado);
    $stmt->execute();
    $stmt->bind_result($matricula, $apellido, $nombres, $numDocumento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);

            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['colegiadoBuscar'] = $matricula.' - '.trim($apellido)." ".trim($nombres)." (DNI ".$numDocumento.")";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay colegiado ".$idColegiado;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}
*/