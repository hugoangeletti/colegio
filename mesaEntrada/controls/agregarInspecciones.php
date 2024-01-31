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
        if(isset($_POST['idIns']))
        {
            $idInspector = $_POST['idIns'];
            if(isset($_POST['habilitaciones']))
            {
                $habilitaciones = $_POST['habilitaciones'];
                if(isset($_GET['lH']))
                {
                    if($_GET['lH'] == "S")
                    {
                        foreach ($habilitaciones as $key => $val)
                        {
                            $estadoAlta = realizarAltaInspeccionSolicitada($idInspector, $val);
                        }
                    }
                    else
                    {
                        $estadoAlta = -3;
                    }
                }
                else
                {
                    $estadoAlta = -3;
                }
            }
            else
            {
                $estadoAlta = -3;
            }
        }
        else
        {
            $estadoAlta = -3;
        }
    }
    else
    {
        $estadoAlta = -3;
    }
        if($estadoAlta == 1)
        {
            $msg = "Las inspecciones se dieron de alta correctamente.";
        }
        else
        {
            $msg = "Las inspecciones no se pudieron dar de alta. Intente nuevamente.";
        }
        
        $data = array(
            "msg" => $msg,
            "array" => $habilitaciones
        );
        
        echo json_encode($data);
?>
