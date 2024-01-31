<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';

    
    if(isset($_POST['iOrden']))
    {
        $idOrdenDia = $_POST['iOrden'];
        
        if(isset($_POST['tipoAccion']))
        {
            $accion = $_POST['tipoAccion'];
        }
        
        $fecha = invertirFecha($_POST['fechaOrden']);
        $fechaDesde = invertirFecha($_POST['fechaDesde']);
        $fechaHasta = invertirFecha($_POST['fechaHasta']);
        $observaciones = $_POST['observaciones'];
        
        if($accion == "M")
        {
            $estadoModificacion = realizarModificacionOrden(trim($idOrdenDia),trim($fecha),trim($fechaDesde),trim($fechaHasta),trim($observaciones));
            
            switch ($estadoModificacion)
            {
                case -1: echo "La modificación no se pudo realizar. Intente nuevamente.";
                    break;
                case 1: echo "La modificación se realizó correctamente.";
                    break;
            }
        }
        else
            if($accion == "B")
            {
                $estadoBaja = realizarBajaOrden($idOrdenDia);
                
                switch ($estadoBaja)
                {
                    case -1: echo "La nota no se pudo dar de baja. Intente nuevamente.";
                        break;
                    case 1: echo "La orden se dio de baja correctamente.";
                        break;
                }
            }
    }
    else
    {
        echo "Hubo un error en la Base de Datos";
    }

?>
