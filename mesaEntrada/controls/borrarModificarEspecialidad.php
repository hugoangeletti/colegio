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
        
        if(isset($_POST['tipoAccion']))
        {
            $accion = $_POST['tipoAccion'];
        }
        
        if(isset($_POST['especialidad']))
        {
            $idEspecialidad = $_POST['especialidad'];
        }
        
        $observaciones = $_POST['observaciones'];
        
        if($accion == "M")
        {
            if($idEspecialidad != "-")
            {
                    $estadoModificacion = realizarModificacionEspecialidad(trim($idMesaEntrada), trim($idEspecialidad), trim($observaciones));
            
                    switch ($estadoModificacion)
                    {
                        case -1: $text = "La modificación no se pudo realizar. Intente nuevamente.";
                            break;
                        case 1: $text = "La modificación se realizó correctamente.";
                            break;
                    }
            }
            else
            {
                $text = "No seleccionó ninguna especialidad.";
            }
        }
        else
            if($accion == "B")
            {
                $estadoBaja = realizarBajaMesaEntrada($idMesaEntrada);
                
                switch ($estadoBaja)
                {
                    case -1: $text = "La especialidad no se pudo dar de baja. Intente nuevamente.";
                        break;
                    case 1: $text = "La especialidad se dio de baja correctamente.";
                        break;
                }
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
    else
    {
        if(isset($estadoModificacion))
        {
            $estado = $estadoModificacion;
        }
    }
    
    $dev = array(
        "estado" => $estado,
        "texto" => $text,
        "importe" => 0
    );
            
            echo json_encode($dev);     
?>
