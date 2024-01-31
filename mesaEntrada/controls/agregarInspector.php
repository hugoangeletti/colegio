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
        if(isset($_POST['matricula']))
        {
            $matricula = $_POST['matricula'];
            $consultaColegiado = obtenerColegiadoPorMatricula($matricula);
            $colegiado = $consultaColegiado -> fetch_assoc();
            $estadoAlta = realizarAltaInspector($colegiado['Id']);
        }
        else
        {
            $estadoAlta = -1;
        }
    }
    else
    {
        $estadoAlta = -1;
    }
        if($estadoAlta != -1)
        {
            echo $estadoAlta;
        }
        else
        {
            echo "-1";
        }
?>
