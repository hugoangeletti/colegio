<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';

    if(isset($_GET['iOrden']))
    {
        $idOrdenDia = $_GET['iOrden'];
        
        $estadoBaja = realizarBajaOrdenDelDiaDetalle($idOrdenDia);
                
        switch ($estadoBaja)
        {
            case -1: echo "El detalle no se pudo dar de baja. Intente nuevamente.";
                break;
            case 1: echo "El detalle fue eliminado correctamente.";
                break;
        }
    }
    else
    {
        echo "Hubo un error en la Base de Datos";
    }

?>
