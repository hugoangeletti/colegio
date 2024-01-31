<?php
function validarUsuario($userName, $clave)
{
    $conect = conectar();
    $result = array();
    $sql="select Id from usuario where Usuario = ? and Clave = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ss', $userName, $clave);
    $stmt->execute();

    $stmt->bind_result($idUsuario);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($idUsuario)) {
            $datos = array(
                    'idUsuario' => $idUsuario,
                    'nombreUsuario' => $userName
                    );

            $result = array(
                'estado' => true,
                'mensaje' => "Ok",
                'datos' => $datos
            );
        } else {
            $result = array(
                'estado' => TRUE,
                'mensaje' => "El usuario y contraseña ingresados no son validos",
                'datos' => $datos
                );
        }
    } else {
         $result = array(
                'estado' => false,
                'mensaje' => "Error al acceder a los datos, intente mas tarde"
                );
    }
    return $result;
 }

function obtenerUsuarioPorId($id)
{
    $result = array();
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select * from pa_usuario where IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($idUsuario, $nombreUsuario, $clave, $estado, $correo, $cambioClave, $ultimoAcceso, $intentosFallidos);

    $stmt->store_result();

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array(
            'idUsuario' => $idUsuario,
            'nombreUsuario' => $nombreUsuario,
            'clave' => $clave,
            'estado' => $estado,
            'correo' => $correo,
            'cambioClave' => $cambioClave,
            'ultimoAcceso' => $ultimoAcceso,
            'intentosFallidos' => $intentosFallidos
        );
        $result['datos'] = $datos;
        $result['estado'] = TRUE;
        $result['mensaje'] = "Ok";
    } else {
        $result['estado'] = FALSE;
        $result['mensaje'] = "No se encontro el usuario, vuelva a intentar.";
    }
    return $result;
}

//HUGO
function obtenerUsuarios()
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "SELECT * FROM pa_usuario ORDER BY UserName";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idUsuario, $nombreUsuario, $clave, $estado, $correo, $cambioClave, $ultimoAcceso, $intentosFallidos);
    $stmt->store_result();

    $result = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'OK';
        $datos = array();

        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idUsuario' => $idUsuario,
                'nombreUsuario' => $nombreUsuario,
                'clave' => $clave,
                'estado' => $estado,
                'correo' => $correo,
                'cambioClave' => $cambioClave,
                'ultimoAcceso' => $ultimoAcceso,
                'intentosFallidos' => $intentosFallidos
            );
            array_push($datos, $row);
        }
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'Error en la consulta.';
        //$datos = NULL;
    }
    
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    $result['datos'] = $datos;

    return $result;

}

function obtenerUsuarioPorNombre($userName)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="select IdUsuario, NombreUsuario from pa_usuario where NombreUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', $userName);
    $stmt->execute();

    $stmt->bind_result($IdUsuario, $NombreUsuario);

    $stmt->store_result();

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);

        $result = array(
            'estado' => true,
            'IdUsuario' => $IdUsuario,
            'NombreUsuario' => $NombreUsuario
        );
    } else {
        $result = array(
            'estado' => false
        );
    }
    return $result;
}

function verificarRolUsuario($idUsuario, $idRol)
{
    $conect = conectar();
    $sql="select count(*) as Cantidad from usuarioapprol WHERE IdAppRol = ? and IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idRol, $idUsuario);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $result = FALSE;
    if ($stmt->execute()) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0){
            $result = TRUE;
        }
    } 
    return $result;
}
 
function verificarAppUsuario($idUsuario, $idApp)
{
    $conect = conectar();
    $sql="SELECT COUNT(*) AS Cantidad 
        FROM usuarioapprol 
        INNER JOIN approl ON(approl.Id = usuarioapprol.IdAppRol)
        WHERE approl.IdApp = ? AND usuarioapprol.IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idApp, $idUsuario);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $result = FALSE;
    if ($stmt->execute()) {
        $row = mysqli_stmt_fetch($stmt);
        if ($cantidad > 0){
            $result = TRUE;
        }
    } 
    return $result;
}
 
function obtenerRolUsuario($idUsuario, $idApp)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "select approl.Id, approl.Nombre, approl.Link
        from approl
        inner join usuarioapprol on(usuarioapprol.IdAppRol = approl.Id)
        where approl.IdApp = ? and usuarioapprol.IdUsuario = ?
        and approl.Estado = 'A' and approl.EnMenu = 'S'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idApp, $idUsuario);
    $stmt->execute();
    $stmt->bind_result($idAppRol, $nombre, $link);
    $stmt->store_result();

    $result = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'OK';
        $datos = array();

        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idAppRol' => $idAppRol,
                'nombre' => $nombre,
                'link' => $link
            );
            array_push($datos, $row);
        }
    } else {
        $estadoConsulta = FALSE;
        $mensaje = 'Error en la consulta.';
        //$datos = NULL;
    }
    
    $result['estado'] = $estadoConsulta;
    $result['mensaje'] = $mensaje;
    $result['datos'] = $datos;

    return $result;
}

function obtenerLeyendaRoles($idUsuario){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "select pa_rol.Nombre
            from pa_usuariorol 
            inner join pa_rol on (pa_usuariorol.IdRol = pa_rol.Id)
            where pa_usuariorol.IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idUsuario);
    $stmt->execute();
    $stmt->bind_result($nombre);
    $stmt->store_result();
    
    $leyenda="";
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        while (mysqli_stmt_fetch($stmt)) {
            $leyenda = $leyenda.$nombre." - ";
        }
        $hasta=(strlen($leyenda)-2);
            
        $leyenda=substr($leyenda,0,$hasta);
    } 
    return $leyenda;
}

function actualizarUsuarioRol($idUsuario, $arrayIdRoles){
    $conect = conectar();
    $resultado['estado'] = TRUE;
    try {
    //elimina los roles que ya tenga asignados
        mysqli_set_charset( $conect, 'utf8');
        $sql = "DELETE FROM pa_usuariorol WHERE IdUsuario = ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('i', $idUsuario);

        if (!$stmt->execute()) {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "Error en eliminando roles del usuario";
            $conect->rollback();
            desconectar($conect);
        } else {
            //crea los nuevos roles
            $cantidadRoles = sizeof($arrayIdRoles);
            for($i = 0; $i < $cantidadRoles; $i++){
                $sql = "INSERT INTO pa_usuariorol (IdUsuario, IdRol) VALUES (?, ?)";
                $stmt = $conect->prepare($sql);
                $stmt->bind_param('ii', $idUsuario, $arrayIdRoles[$i]);
                if (!$stmt->execute()) {
                    $resultado['estado'] = FALSE;
                    $resultado['mensaje'] = "Error en eliminando roles del usuario";

                    $conect->rollback();
                    desconectar($conect);
                } 
            }
        }
        return $resultado;

    } catch (mysqli_sql_exception $e) {
        $conect->rollback();
        desconectar($conect);
        return $resultado;
    }
}

function agregarUsuario($nombreUsuario, $clave, $estado, $correo){
    $conect = conectar();
    $resultado = array();
    $claveHash = hashData($clave);
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO pa_usuario (UserName, Clave, Estado, Correo, CambioClave) "
            . "VALUES (?, ?, ?, ?, now())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssss', $nombreUsuario, $claveHash, $estado, $correo);
    if (!$stmt->execute()) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al insertar el usuario";
    } else {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "Ok";
    }
    
    return $resultado;
}

function actualizarUsuario($idUsuario, $nombreUsuario, $correo, $estado){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE pa_usuario "
            . "SET UserName = ?, "
            . "Estado = ?, "
            . "Correo = ?, "
            . "CambioClave = now() "
            . "WHERE IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssi', $nombreUsuario, $estado, $correo, $idUsuario);
    if (!$stmt->execute()) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al actualizar el usuario";
    } else {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "Ok";
    }
    
    return $resultado;
}

function actualizarClaveUsuario($idUsuario, $clave){
    $conect = conectar();
    $resultado = array();
    $claveHash = hashData($clave);
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE pa_usuario "
            . "SET Clave = ?, "
            . "CambioClave = now() "
            . "WHERE IdUsuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('si', $claveHash, $idUsuario);
    if (!$stmt->execute()) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al actualizar la contrase&ntilde;a del usuario";
    } else {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "Ok";
    }
    
    return $resultado;
}

function logUsuario($idUsuario){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE usuario SET UltimoAcceso = now() WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idUsuario);
    if (!$stmt->execute()) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al actualizar la contrase&ntilde;a del usuario";
    } else {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "Ok";
    }
    
    return $resultado;
}