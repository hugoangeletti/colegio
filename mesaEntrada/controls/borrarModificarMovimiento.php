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
        
        
        
        if($accion == "M")
        {
            $idTipoMovimiento = $_POST['tipoMovimiento'];
        
            $idMotivo = $_POST['motivo'];

            $observaciones = $_POST['observaciones'];

            $fechaDesdeInvertir = explode("-", $_POST['fechaDesde']);
            $fechaDesde = $fechaDesdeInvertir[2]."-".$fechaDesdeInvertir[1]."-".$fechaDesdeInvertir[0];
            if($idTipoMovimiento != "")
            {
                if($idMotivo != "")
                {
                    $estadoModificacion = realizarModificacionMovimiento(trim($idMesaEntrada), trim($fechaDesde), trim($idTipoMovimiento), trim($idMotivo) ,trim($observaciones));
            
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
                    $text = "No seleccionó motivo";
                }
            }
            else
            {
                $text = "No seleccionó ningún tipo de movimiento.";
            }
        }
        else
            if($accion == "B")
            {
                $infoMesaEntrada = obtenerMovimientoPorId($idMesaEntrada);
                $dataMesaEntrada = $infoMesaEntrada -> fetch_assoc();
                if($dataMesaEntrada['FechaIngreso'] != date("Y-m-d"))
                {
                    $text = "No puede dar de baja un movimiento que no corresponda al día de hoy.
                            Contáctese con informática.";
                    $estadoBaja = -1;
                }
                else
                {
                    $estadoBaja = realizarBajaMesaEntrada($idMesaEntrada);

                    switch ($estadoBaja)
                    {
                        case -1: $text = "El movimiento no se pudo dar de baja. Intente nuevamente.";
                            break;
                        case 1: $estadoBaja = rollBackBajaMovimiento($idMesaEntrada);
                                if($estadoBaja == 1)
                                {
                                    $text = "El movimiento se dio de baja correctamente.";
                                }
                                else
                                {
                                    $text = "El movimiento no se pudo dar de baja. Intente nuevamente.";
                                }

                            break;
                    }
                }
            }else{
                $text = "NO ENTRA A LOS ACCION";
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
