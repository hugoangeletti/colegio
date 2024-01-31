<?php
function validarUsuario($userName, $clave)
{
    $conect = conectar();
    $result = array();
    $sql="select * from usuario where Usuario = ? and Clave = ? and Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ss', $userName, $clave);
    $stmt->execute();

    $stmt->bind_result($idUsuario, $nombreUsuario, $clave, $nombreCompleto, $tipoUsuario, $ultimoAcceso, $estado);
    $stmt->store_result();

    if ($stmt->execute()) {
        $datos = array();
        $row = mysqli_stmt_fetch($stmt);
        if (isset($idUsuario)) {
            $datos = array(
                    'idUsuario' => $idUsuario,
                    'nombreUsuario' => $nombreUsuario,
                    'tipoUsuario' => $tipoUsuario
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
    $sql="select * from usuario where Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $stmt->bind_result($idUsuario, $nombreUsuario, $clave, $nombreCompleto, $tipoUsuario, $ultimoAcceso, $estado);

    $stmt->store_result();

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $row = mysqli_stmt_fetch($stmt);
        $datos = array(
            'idUsuario' => $idUsuario,
                'nombreUsuario' => $nombreUsuario,
                'clave' => $clave,
                'nombreCompleto' => $nombreCompleto,
                'tipoUsuario' => $tipoUsuario,
                'ultimoAcceso' => $ultimoAcceso,
                'estado' => $estado
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
    $sql = "SELECT * FROM usuario ORDER BY Usuario";
    $stmt = $conect->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($idUsuario, $nombreUsuario, $clave, $nombreCompleto, $tipoUsuario, $ultimoAcceso, $estado);
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
                'nombreCompleto' => $nombreCompleto,
                'tipoUsuario' => $tipoUsuario,
                'ultimoAcceso' => $ultimoAcceso,
                'estado' => $estado
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
    $sql="select Id, Usuario from usuario where Usuario = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('s', trim($userName));
    $stmt->execute();

    $stmt->bind_result($IdUsuario, $NombreUsuario);

    $stmt->store_result();

    if ($stmt->execute()) {
        var_dump($stmt);
        $row = mysqli_stmt_fetch($stmt);        
        if (mysqli_stmt_num_rows($stmt) > 0) {

            $datos = array(
                'IdUsuario' => $IdUsuario,
                'NombreUsuario' => $NombreUsuario
            );
            $resultado['estado'] = TRUE;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = FALSE;
            $resultado['mensaje'] = "NO SE ENCONTRO EL USUARIO";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR BUSCANDO USUARIO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    return $resultado;
}

function verificarRolUsuario($idUsuario, $idRol)
{
    $conect = conectar();
    //$sql="select count(*) as Cantidad from usuarioapprol WHERE IdAppRol = ? and IdUsuario = ?";
    $sql = "SELECT COUNT(*) AS Cantidad 
        FROM usuarioapprol 
        INNER JOIN approl ON(approl.Id = usuarioapprol.IdAppRol)
        WHERE usuarioapprol.IdAppRol = ? AND usuarioapprol.IdUsuario = ? AND approl.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idRol, $idUsuario);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->store_result();

    $result = FALSE;
    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            if ($cantidad > 0){
                $result = TRUE;
            }
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
        WHERE approl.IdApp = ? AND usuarioapprol.IdUsuario = ? AND approl.Estado = 'A'";
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

function obtenerRolesPorUsuario($idUsuario)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "select approl.Id, approl.Nombre
        from approl
        inner join usuarioapprol on(usuarioapprol.IdAppRol = approl.Id)
        where usuarioapprol.IdUsuario = ? and approl.Estado = 'A'";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $idUsuario);
    $stmt->execute();
    $stmt->bind_result($idAppRol, $nombre);
    $stmt->store_result();

    $result = array();
    if (mysqli_stmt_num_rows($stmt) >= 0) {
        $estadoConsulta = TRUE;
        $mensaje = 'OK';
        $datos = array();

        while (mysqli_stmt_fetch($stmt)) {
            $row = array (
                'idAppRol' => $idAppRol,
                'nombre' => $nombre
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
    $sql = "select approl.Nombre
            from usuarioapprol 
            inner join approl on (usuarioapprol.IdAppRol = approl.Id)
            where usuarioapprol.IdUsuario = ?";
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
        $sql = "DELETE FROM usuarioapprol WHERE IdUsuario = ?";
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
                $sql = "INSERT INTO usuarioapprol (IdUsuario, IdAppRol) VALUES (?, ?)";
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

function agregarUsuario($nombreUsuario, $clave, $nombreCompleto, $tipoUsuario){
    $conect = conectar();
    $resultado = array();
    //$claveHash = hashData($clave);
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO usuario (Usuario, Clave, NombreCompleto, TipoUsuario) "
            . "VALUES (?, ?, ?, ?)";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ssss', $nombreUsuario, $clave, $nombreCompleto, $tipoUsuario);
    if ($stmt->execute()) {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
    } else {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "ERROR AGREGANDO USUARIO";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }

    return $resultado;
}

function actualizarUsuario($idUsuario, $nombreUsuario, $clave, $nombreCompleto, $tipoUsuario, $estado){
    $conect = conectar();
    $resultado = array();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "UPDATE usuario 
            SET Usuario = ?, 
            NombreCompleto = ?, 
            Estado = ?, 
            Clave = ?, 
            TipoUsuario = ? 
            WHERE Id = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('sssssi', $nombreUsuario, $nombreCompleto, $estado, $clave, $tipoUsuario, $idUsuario);
    if (!$stmt->execute()) {
        $resultado['estado'] = FALSE;
        $resultado['mensaje'] = "Error al actualizar el usuario";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    } else {
        $resultado['estado'] = TRUE;
        $resultado['mensaje'] = "Ok";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok'; 
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