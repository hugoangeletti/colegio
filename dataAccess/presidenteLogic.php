<?php
function obtenerPresidenteDistrito($distrito){
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = 'SELECT Presidente, Romanos FROM distritos WHERE Distrito = ?';
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('i', $distrito);
    $stmt->execute();
    $stmt->bind_result($nombre, $romanos);
    $stmt->store_result();

    $resultado = array();
    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = TRUE;
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            $datos = array(
                    'nombre' => $nombre,
                    'romanos' => $romanos
                    );
            
            $resultado['datos'] = $datos;
            $resultado['mensaje'] = "OK";
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['datos'] = NULL;
            $resultado['mensaje'] = "No hay Presidente ".$distrito;
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Presidente ".$distrito;
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
    
}
