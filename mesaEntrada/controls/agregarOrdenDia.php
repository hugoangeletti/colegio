<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';
    /*
     * Realizo la comprobación de si es Colegiado o Remitente,
     * para establecer las relaciones entre las variables y los campos de la
     * BD que serán cargadas.
     */
    
    if(isset($_POST))
    {
        $fecha = invertirFecha($_POST['fechaOrden']);
        $periodo = date("Y");
        $numeroOrden = $_POST['numero'];
        $fechaDesde = invertirFecha($_POST['fechaDesde']);
        $fechaHasta = invertirFecha($_POST['fechaHasta']);
        $observaciones = $_POST['observaciones'];
        
        if($fecha != "0000-00-00")
        {
                $estadoAlta = realizarAltaOrden($fecha, $periodo, $numeroOrden, $fechaDesde, $fechaHasta, $observaciones);
        }
        else
        {
            $estadoAlta = -3;
        }
        if($estadoAlta == 1)
        {
            echo "La orden se dio de alta correctamente.";
        }
        else
        {
            echo "Hubo un error al dar de alta la orden. Intente nuevamente.";
        }
    }

?>
