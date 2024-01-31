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
        $tipoConsultorio = $_POST['tipoConsultorio'];
        $nombreConsultorio = $_POST['nombreConsultorio'];
        if($tipoConsultorio != "")
        {
            if($tipoConsultorio == "U")
            {
                $cantidad = 1;
            }
            else
            {
                if($tipoConsultorio == "P")
                {
                    $cantidad = $_POST['cantConsultorios'];
                }
                else
                {
                    $cantidad = 0;
                }
            }
        }
        
        $calle = $_POST['calle'];
        $lateral = $_POST['lateral'];
        $numero = $_POST['numero'];
        $piso = $_POST['piso'];
        $dpto = $_POST['departamento'];
        $tel = $_POST['tel'];
        $idLocalidad = $_POST['localidad'];
        $cp = $_POST['CP'];
        $observaciones = $_POST['observaciones'];
        
        if($tipoConsultorio != "")
        {
            if($idLocalidad != "")
            {
                $estadoAlta = realizarAltaConsultorio($tipoConsultorio, trim($nombreConsultorio), trim($calle), trim($lateral), trim($numero), trim($piso), trim($dpto), trim($tel), $idLocalidad, trim($cp), trim($observaciones), trim($cantidad));
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
        }
    else
    {
        echo "-1";
    }

?>
