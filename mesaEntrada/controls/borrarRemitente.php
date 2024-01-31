<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    if(isset($_POST['idRemitente']))
    {
        $idRemitente = $_POST['idRemitente'];
        
        $tieneAcciones = obtenerNotasPorRemitente($idRemitente);
        
        if($tieneAcciones)
        {
            if($tieneAcciones -> num_rows == 0)
            {
                $estadoBaja = realizarBajaRemitente($idRemitente);

                switch ($estadoBaja)
                {
                    case -1: echo "El remitente no se pudo dar de baja. Intente nuevamente.";
                        break;
                    case 1: echo "El remitente se dio de baja correctamente.";
                        break;
                }
            }
            else
            {
                echo "El remitente que desea eliminar tiene notas asociadas, no podrá eliminarlo.";
            }
        }
        else
        {
            echo "Hubo un error en la Base de Datos";
        }
    }
    else
    {
        echo "Hubo un error en la Base de Datos";
    }

?>
