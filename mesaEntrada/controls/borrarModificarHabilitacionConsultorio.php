<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    if(isset($_POST['idMesaEntrada']))
    {
        $idMesaEntrada = $_POST['idMesaEntrada'];
        
        $estadoBaja = realizarBajaMesaEntrada($idMesaEntrada);

        switch ($estadoBaja)
        {
            case -1: $text = "La habilitación no se pudo dar de baja. Intente nuevamente.";
                break;
            case 1: $text = "La habilitación se dio de baja correctamente.";
                break;
        }
    }
    else
    {
        $text = "Hubo un error en la Base de Datos";
    }
    
    if(isset($estadoBaja))
    {
        $estado = $estadoBaja;
    }
    
    
$dev = array(
        "estado" => $estado,
        "texto" => $text,
        "importe" => 0
    );
            
            echo json_encode($dev);     
?>
