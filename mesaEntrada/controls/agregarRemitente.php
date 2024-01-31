<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    if(isset($_POST))
    {
        if(!empty($_POST['nombre']))
        {
            $nombreRemitente = pasarAMayuscula($_POST['nombre']);
            
            $estadoAlta = realizarAltaRemitente(utf8_decode($nombreRemitente));
        }
        else
        {
            $estadoAlta = -2;
        }
        if($estadoAlta == 1)
        {
            $text = "El remitente se dio de alta correctamente.";
        }
        else
        {
            $text = "Hubo un error al dar de alta el movimiento. Intente nuevamente.";
        }
    }
    
    $data = array(
        "texto" => $text
    );
    
    echo json_encode($data);
?>
