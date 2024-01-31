<?php
function obtenerTodos($distrito){
    if ($distrito == '1') {
        $porDistrito = " AND r.DistritoOrigen = 1";
    } else {
        $porDistrito = " AND r.DistritoOrigen <> 1";
    }
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT r.Id, r.FechaAlta, r.Numero, r.Apellido, r.Nombre, p.Nacionalidad, td.NombreCompleto, r.NumeroDocumento, 
        r.NumeroPasaporte, r.FechaIngreso, r.Estado, r.FechaVencimiento, r.DistritoOrigen
        FROM registro_dnu_260_2020 r
        INNER JOIN paises p ON(p.Id = r.IdPais)
        INNER JOIN tipodocumento td ON(td.IdTipoDocumento = r.IdTipoDocumento) 
        WHERE r.Estado IN('A', 'B', 'V') ".$porDistrito;
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idRegistro, $fechaAlta, $numero, $apellido, $nombres, $nacionalidad, $tipoDocumento, 
            $numeroDocumento, $numeroPasaporte, $fechaIngreso, $estado, $fechaVencimiento, $distrito);
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
                    'idRegistro' => $idRegistro,
                    'fechaAlta' => $fechaAlta,
                    'numero' => $numero,
                    'apellidoNombre' => trim($apellido).' '.trim($nombres),
                    'nacionalidad' => $nacionalidad,
                    'tipoDocumento' => $tipoDocumento,
                    'numeroDocumento' => $numeroDocumento,
                    'numeroPasaporte' => $numeroPasaporte,
                    'fechaIngreso' => $fechaIngreso,
                    'estado' => $estado,
                    'fechaVencimiento' => $fechaVencimiento,
                    'distrito' => $distrito
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
            $resultado['mensaje'] = "No hay registros";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando registros";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}


function obtenerRegistroPorId($idRegistro){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT r.*, p.Nacionalidad, td.NombreCompleto
        FROM registro_dnu_260_2020 r
        INNER JOIN paises p ON(p.Id = r.IdPais)
        INNER JOIN tipodocumento td ON(td.IdTipoDocumento = r.IdTipoDocumento)
        WHERE r.Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRegistro);
    $stmt->execute();
    $stmt->bind_result($idRegistro, $fechaAlta, $numero, $apellido, $nombres, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
            $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $fechaExpedicion, $especialidad,
            $domicilioParticular, $localidadParticular, $cpParticular, $telefonoFijo, $telefonoMovil, $mail, $estado, $idUsuario, $fechaCarga, $fechaInicioValidaTitulo, $fechaVencimiento, $distrito, $nacionalidad, $tipoDocumento);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'idRegistro' => $idRegistro,
                'fechaAlta' => $fechaAlta,
                'numero' => $numero,
                'apellido' => $apellido,
                'nombre' => $nombres,
                'idPais' => $idPais,
                'sexo' => $sexo,
                'fechaNacimiento' => $fechaNacimiento,
                'estadoCivil' => $estadoCivil,
                'idTipoDocumento' => $idTipoDocumento,
                'numeroDocumento' => $numeroDocumento,
                'numeroPasaporte' => $numeroPasaporte,
                'fechaIngreso' => $fechaIngreso,
                'universidad' => $universidad,
                'fechaTitulo' => $fechaExpedicion,
                'especialidad' => $especialidad,
                'domicilioParticular' => $domicilioParticular,
                'localidadParticular' => $localidadParticular,
                'codigoPostalParticular' => $cpParticular,
                'telefonoFijo' => $telefonoFijo, 
                'telefonoMovil' => $telefonoMovil, 
                'mail' => $mail, 
                'estado' => $estado, 
                'idUsuario' => $idUsuario, 
                'fechaCarga' => $fechaCarga,
                'fechaInicioValidaTitulo' => $fechaInicioValidaTitulo,
                'fechaVencimiento' => $fechaVencimiento,
                'distrito' => $distrito,
                'nacionalidad' => $nacionalidad, 
                'tipoDocumento' => $tipoDocumento
             );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay registro";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando registro";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;

}

function numeroDocumentoExiste($tipoDocumento, $numeroDocumento){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT COUNT(Id) AS Cantidad FROM registro_dnu_260_2020 WHERE IdTipoDocumento = ? AND NumeroDocumento = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $tipoDocumento, $numeroDocumento);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0) {
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "Numero de Documento YA EXISTE EN LA BASE DE DATOS";
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "No existe Numero de Documento";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al buscar el Numero de Documento";
    }
    
    return $resultado;
}

function obtenerNumeroRegistro() {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT MAX(Numero) FROM registro_dnu_260_2020 WHERE DistritoOrigen = 1";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($numero);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $row = mysqli_stmt_fetch($stmt);
        if ($numero > 0) {
            $resultado['estado'] = TRUE;
            $resultado['numero'] = $numero + 1;
            $resultado['mensaje'] = "OK";
        } else {
            $resultado['estado'] = TRUE;
            $resultado['numero'] = 1000001;
            $resultado['mensaje'] = "OK, inicial";
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al buscar el Numero de Documento";
    }
    
    return $resultado;
}

function agregarRegistro($numero, $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil, $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $fechaTitulo, $especialidad, $domicilioParticular, $localidadParticular, $codigoPostalParticular, $entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoFijo, $telefonoMovil, $mail, $fechaInicioValidaTitulo, $telefonoProfesional, $distrito) 
{
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $fechaAlta = date('Y-m-d');
        $fechaVencimiento = sumarRestarSobreFecha($fechaAlta, 60, 'day', '+');
        $sql="INSERT INTO  registro_dnu_260_2020 
            (FechaAlta, Numero, Apellido, Nombre, IdPais, Sexo, FechaNacimiento, EstadoCivil, IdTipoDocumento, NumeroDocumento, NumeroPasaporte, FechaIngreso, Universidad, FechaExpedicion, Especialidad, DomicilioParticular, LocalidadParticular, 
            CPParticular, TelefonoFijo, TelefonoMovil, Mail, IdUsuario, FechaCarga, FechaInicioValidaTitulo, FechaVencimiento, DistritoOrigen) 
            VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sssisssissssssssssssissi', $numero, $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil, $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $fechaTitulo, $especialidad, $domicilioParticular, $localidadParticular, $codigoPostalParticular, $telefonoFijo, $telefonoMovil, $mail, $_SESSION['user_id'], $fechaInicioValidaTitulo, $fechaVencimiento, $distrito);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            //agrego el movimiento para hacer el seguimiento
            $idRegistro = mysqli_stmt_insert_id($stmt);
            $resultado['estado'] = TRUE;

            //agregor los datos del domicilio laboral
            $resultado = agregarDatoLaboral($idRegistro, $entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoProfesional, $conect);
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL AGREGAR REGISTRO ";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    

        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL REGISTRO HA SIDO ACTUALIZADO CORRECTAMENTE';
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

function modificarRegistro($apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
            $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad, $domicilioParticular, $localidadParticular, $codigoPostalParticular, $telefonoFijo, $telefonoMovil, $mail, $fechaInicioValidaTitulo, $idRegistro, $numero, $distrito, $datosAnteriores) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $sql="UPDATE registro_dnu_260_2020 
            SET Apellido = ?, Nombre = ?, IdPais = ?, Sexo = ?, FechaNacimiento = ?, EstadoCivil = ?, 
            IdTipoDocumento = ?, NumeroDocumento = ?, NumeroPasaporte = ?, FechaIngreso = ?, Universidad = ?, FechaExpedicion = ?, 
            Especialidad = ?, DomicilioParticular = ?, LocalidadParticular = ?, CPParticular = ?, TelefonoFijo = ?, TelefonoMovil = ?, Mail = ?, IdUsuario = ?, FechaInicioValidaTitulo = ?, FechaCarga = NOW(), Numero = ?, DistritoOrigen = ?
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssisssissssssssssssisiii', $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil, $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad, $domicilioParticular, $localidadParticular, $codigoPostalParticular, $telefonoFijo, $telefonoMovil, $mail, $_SESSION['user_id'], $fechaInicioValidaTitulo, $numero, $distrito, $idRegistro);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO log_tabla 
                    (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
                    VALUES ('registro_dnu_260_2020', ?, now(), 'modificacion', ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iis', $idRegistro, $_SESSION['user_id'], serialize($datosAnteriores));
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "DATOS DEL REGISTRO SE ACTUALIZARON CON EXITO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR DATOS";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR REGISTRO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL REGISTRO HA SIDO ACTUALIZADO CORRECTAMENTE';
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

function borrarRegistro($idRegistro, $tipoBaja, $matricula, $revalida, $convalida, $constanciaLaboral) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $sql="UPDATE registro_dnu_260_2020 
                SET Estado = 'B', FechaCarga = NOW(), IdUsuario = ? 
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $idRegistro);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO registro_dnu_260_baja 
                    (IdRegistro_dnu_260_2020, Fecha, TipoBaja, Matricula, Revalida, Convalida, ConstanciaLaboral, FechaCarga, IdUsuario) 
                    VALUES (?, DATE(NOW()), ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('isiiiii', $idRegistro, $tipoBaja, $matricula, $revalida, $convalida, $constanciaLaboral, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "REGISTRO DADO DE BAJA";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL REGISTRAR BAJA";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL DAR DE BAJA AL REGISTRO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
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

function activarRegistro($idRegistro) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $sql="UPDATE registro_dnu_260_2020 
                SET Estado = 'A', FechaCarga = NOW(), IdUsuario = ? 
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $_SESSION['user_id'], $idRegistro);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="UPDATE registro_dnu_260_baja 
                SET Estado = 'B', FechaCarga = NOW(), IdUsuario = ? 
                WHERE IdRegistro_dnu_260_2020 = ?";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('ii', $_SESSION['user_id'], $idRegistro);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "REGISTRO REACTIVADO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL REACTIVAR EL REGISTRO.";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL REACTIVAR EL REGISTRO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
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

function renovarRegistro($idRegistro, $fechaRenovacion) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $fechaVencimiento = sumarRestarSobreFecha($fechaRenovacion, 60, 'day', '+');
        $resultado = array();
        $sql="UPDATE registro_dnu_260_2020 
                SET Estado = 'A', FechaCarga = NOW(), IdUsuario = ?, FechaVencimiento = ? 
                WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('isi', $_SESSION['user_id'], $fechaVencimiento, $idRegistro);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO registro_dnu_260_renovacion 
                    (IdRegistro_dnu_260_2020, Fecha, FechaCarga, IdUsuario) 
                    VALUES (?, ?, NOW(), ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('isi', $idRegistro, $fechaRenovacion, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "EL REGISTRO HA SIDO RENOVADO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL RENOVAR EL REGISTRO.";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL RENOVAR EL REGISTRO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
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


function agregarRegistroCertificado($idRegistro, $paraEnviar, $distrito, $enviaMail, $mailDestino) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="INSERT INTO  registro_dnu_260_certificado 
        (IdRegistro_dnu_260_2020, ParaEnviar, Distrito, EnviaMail, Mail, FechaEmision, IdUsuario)
        VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isissi', $idRegistro, $paraEnviar, $distrito, $enviaMail, $mailDestino, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['idCertificado'] = mysqli_stmt_insert_id($stmt);
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR REGISTRO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function obtenerDatosLaborales($idRegistro)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT *
        FROM registro_dnu_260_laboral
        WHERE IdRegistro_dnu_260_2020 = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRegistro);
    $stmt->execute();
    $stmt->bind_result($idRegistroLaboral, $idRegistro, $entidad, $domicilioProfesional, $localidadProfesional, $cpProfesional, $telefonoProfesional, $estado, $idUsuario, $fechaCarga);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {             
                if ($estado == 'A') {
                    $estado = "Activo";
                } else {
                    if ($estado == 'B') {
                        $estado = 'BAJA';
                    } else {
                        $estado = "";
                    }
                }  
                $row = array (
                    'idRegistroLaboral' => $idRegistroLaboral,
                    'idRegistro' => $idRegistro,
                    'entidad' => $entidad,
                    'domicilioProfesional' => trim($domicilioProfesional),
                    'localidadProfesional' => $localidadProfesional,
                    'cpProfesional' => $cpProfesional,
                    'telefonoProfesional' => $telefonoProfesional,
                    'estado' => $estado,
                    'idUsuario' => $idUsuario,
                    'fechaCarga' => $fechaCarga
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
            $resultado['mensaje'] = "No hay registros";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando registros";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function obtenerDatosLaboralesPorId($idRegistroLaboral){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM registro_dnu_260_laboral WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idRegistroLaboral);
    $stmt->execute();
    $stmt->bind_result($idRegistroLaboral, $idRegistro, $entidad, $domicilioProfesional, $localidadProfesional, $cpProfesional, $telefonoProfesional, $estado, $idUsuario, $fechaCarga);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0)
    {
        if (mysqli_stmt_num_rows($stmt) >= 0) 
        {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array (
                'idRegistroLaboral' => $idRegistroLaboral,
                'idRegistro' => $idRegistro,
                'entidad' => $entidad,
                'domicilioProfesional' => $domicilioProfesional,
                'localidadProfesional' => $localidadProfesional,
                'codigoPostalProfesional' => $cpProfesional,
                'telefonoProfesional' => $telefonoProfesional, 
                'estado' => $estado,
                'idUsuario' => $idUsuario,
                'fechaCarga' => $fechaCarga
             );
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = true;
            $resultado['mensaje'] = "No hay registro";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando registro";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function agregarDatoLaboral($idRegistro, $entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoProfesional, $conect) {
    if (!isset($conect)) {
        $conect = conectar();
    }
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="INSERT INTO  registro_dnu_260_laboral 
        (IdRegistro_dnu_260_2020, Entidad, DomicilioProfesional, LocalidadProfesional, CPProfesional, TelefonoProfesional, IdUsuario, FechaCarga) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('isssssi', $idRegistro, $entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoProfesional, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $idInspector = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'EL DATO LABORAL HA SIDO AGREGADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR DATO LABORAL ".mysqli_stmt_error($stmt);
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function modificarDatoLaboral($entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoProfesional, $idRegistroLaboral, $datosAnteriores) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $sql="UPDATE registro_dnu_260_laboral 
            SET Entidad = ?, DomicilioProfesional = ?, LocalidadProfesional = ?, CPProfesional = ?, TelefonoProfesional = ?, IdUsuario = ?, FechaCarga = NOW() 
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('sssssii', $entidad, $domicilioProfesional, $localidadProfesional, $codigoPostalProfesional, $telefonoProfesional, $_SESSION['user_id'], $idRegistroLaboral);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO log_tabla 
                    (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
                    VALUES ('registro_dnu_260_laboral', ?, now(), 'modificacion', ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iis', $idRegistroLaboral, $_SESSION['user_id'], serialize($datosAnteriores));
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "DATO LABORAL SE ACTUALIZARON CON EXITO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR DATO LABORAL";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR DATO LABORAL";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL REGISTRO HA SIDO ACTUALIZADO CORRECTAMENTE';
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

function borrarDatoLaboral($idRegistroLaboral){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="UPDATE registro_dnu_260_laboral 
        SET Estado = 'B', IdUsuario = ?, FechaCarga = NOW() 
        WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['user_id'], $idRegistroLaboral);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "DATO LABORAL SE BORRO CON EXITO";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL BORRAR DATO LABORAL";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
        
    return $resultado;
}

/* se actualizo para sacar los campos laborales */

function agregarRegistroCompleto($numero, $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
            $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad,
            $domicilioParticular, $localidadParticular, $codigoPostalParticular, $domicilioProfesional, $localidadProfesional, 
            $codigoPostalProfesional, $telefonoFijo, $telefonoMovil, $mail, $fechaInicioValidaTitulo) {
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $resultado = array();
    $sql="INSERT INTO  registro_dnu_260_2020 
        (FechaAlta, Numero, Apellido, Nombre, IdPais, Sexo, FechaNacimiento, EstadoCivil, IdTipoDocumento, NumeroDocumento, 
        NumeroPasaporte, FechaIngreso, Universidad, FechaExpedicion, Especialidad, DomicilioParticular, LocalidadParticular, 
        CPParticular, DomicilioProfesional, LocalidadProfesional, CPProfesional, TelefonoFijo, TelefonoMovil, Mail, IdUsuario, FechaCarga, FechaInicioValidaTitulo) 
        VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ississsisssssssssssssssis', $numero, $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
            $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad,
            $domicilioParticular, $localidadParticular, $codigoPostalParticular, $domicilioProfesional, $localidadProfesional, 
            $codigoPostalProfesional, $telefonoFijo, $telefonoMovil, $mail, $_SESSION['user_id'], $fechaInicioValidaTitulo);
    $stmt->execute();
    $stmt->store_result();
    if(mysqli_stmt_errno($stmt)==0) {
        //agrego el movimiento para hacer el seguimiento
        $idInspector = mysqli_stmt_insert_id($stmt);
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = 'EL REGISTRO HA SIDO AGREGADO';
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AL AGREGAR REGISTRO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }    
    return $resultado;
}

function modificarRegistroCompleto($apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
            $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad,
            $domicilioParticular, $localidadParticular, $codigoPostalParticular, $domicilioProfesional, $localidadProfesional, 
            $codigoPostalProfesional, $telefonoFijo, $telefonoMovil, $mail, $fechaInicioValidaTitulo, $idRegistro, $datosAnteriores) {
    try {
        /* Autocommit false para la transaccion */
        $conect = conectar();
        mysqli_set_charset( $conect, 'utf8');
        $conect->autocommit(FALSE);
        $resultado = array();
        $sql="UPDATE registro_dnu_260_2020 
            SET Apellido = ?, Nombre = ?, IdPais = ?, Sexo = ?, FechaNacimiento = ?, EstadoCivil = ?, 
            IdTipoDocumento = ?, NumeroDocumento = ?, NumeroPasaporte = ?, FechaIngreso = ?, Universidad = ?, FechaExpedicion = ?, 
            Especialidad = ?, DomicilioParticular = ?, LocalidadParticular = ?, CPParticular = ?, DomicilioProfesional = ?, 
            LocalidadProfesional = ?, CPProfesional = ?, TelefonoFijo = ?, TelefonoMovil = ?, Mail = ?, IdUsuario = ?, FechaInicioValidaTitulo = ?, FechaCarga = NOW() 
            WHERE Id = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ssisssisssssssssssssssisi', $apellido, $nombre, $idPais, $sexo, $fechaNacimiento, $estadoCivil,
                $idTipoDocumento, $numeroDocumento, $numeroPasaporte, $fechaIngreso, $universidad, $FechaExpedicion, $especialidad,
                $domicilioParticular, $localidadParticular, $codigoPostalParticular, $domicilioProfesional, $localidadProfesional, 
                $codigoPostalProfesional, $telefonoFijo, $telefonoMovil, $mail, $_SESSION['user_id'], $fechaInicioValidaTitulo, $idRegistro);
        $stmt->execute();
        $stmt->store_result();
        if(mysqli_stmt_errno($stmt)==0) {
            $sql="INSERT INTO log_tabla 
                    (Tabla, IdTabla, Fecha, TipoMovimiento, IdUsuario, Datos) 
                    VALUES ('registro_dnu_260_2020', ?, now(), 'modificacion', ?, ?)";
            $stmt = $conect->prepare($sql);
            $stmt->bind_param('iis', $idRegistro, $_SESSION['user_id'], serialize($datosAnteriores));
            $stmt->execute();
            $stmt->store_result();
            if(mysqli_stmt_errno($stmt)==0) {
                $resultado['estado'] = TRUE;
                $resultado['mensaje'] = "DATOS DEL REGISTRO SE ACTUALIZARON CON EXITO";
                $resultado['clase'] = 'alert alert-success'; 
                $resultado['icono'] = 'glyphicon glyphicon-ok'; 
            } else {
                $resultado['estado'] = FALSE;
                $resultado['mensaje'] = "ERROR AL MODIFICAR DATOS";
                $resultado['clase'] = 'alert alert-error'; 
                $resultado['icono'] = 'glyphicon glyphicon-remove';
            }
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "ERROR AL MODIFICAR REGISTRO";
            $resultado['clase'] = 'alert alert-error'; 
            $resultado['icono'] = 'glyphicon glyphicon-remove';
        }    
        
        if ($resultado['estado']) {
            $resultado['mensaje'] = 'EL REGISTRO HA SIDO ACTUALIZADO CORRECTAMENTE';
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

