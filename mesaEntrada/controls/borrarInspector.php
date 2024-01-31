<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    if(isset($_POST['idInspector']))
    {
        $idInspector = $_POST['idInspector'];
        
        $estadoBaja = realizarBajaInspector($idInspector);

        switch ($estadoBaja)
        {
            case -1: echo "El inspector no se pudo dar de baja. Intente nuevamente.";
                break;
            case 1: echo "El inspector se dio de baja correctamente.";
                break;
        }
    }
    else
    {
        echo "Hubo un error en la Base de Datos";
    }

?>
