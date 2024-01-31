<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    if(isset($_POST['idInspectorHabilitacion']))
    {
        $idInspectorHabilitacion = $_POST['idInspectorHabilitacion'];
        if(isset($_POST['tipoAccion']))
        {
            $accion = $_POST['tipoAccion'];
        }
        
        //$observaciones = $_POST['observaciones'];
        
        if($accion == "M")
        {
            if(isset($_POST['fechaInspeccion']))
            {
                $fechaInspeccion = invertirFecha($_POST['fechaInspeccion']);
                if($fechaInspeccion != "")
                {
                    if(isset($_POST['habilitado']))
                    {
                        $habilitado = $_POST['habilitado'];
                        if($habilitado == "S")
                        {
                            if(isset($_POST['fechaHabilitacion']))
                            {
                                $fechaHabilitacion = invertirFecha($_POST['fechaHabilitacion']);
                                if($fechaHabilitacion != "")
                                {
                                    $estadoModificacion = realizarModificacionInspectorHabilitacionPorSi(trim($idInspectorHabilitacion),trim($fechaInspeccion),trim($fechaHabilitacion));
                                    
                                    if($estadoModificacion == 1)
                                    {
                                        $consultaInspeccionHabilitacion = obtenerInspectorHabilitacionPorId($idInspectorHabilitacion);
                                        
                                        if(!$consultaInspeccionHabilitacion)
                                        {
                                            $estadoModificacion = -1;
                                        }
                                        else
                                        {
                                            if($consultaInspeccionHabilitacion -> num_rows == 0)
                                            {
                                                $estadoModificacion = -1;
                                            }
                                            else
                                            {
                                                $inspeccionHabilitacion = $consultaInspeccionHabilitacion -> fetch_assoc();
                                                $consultaDatosMesaEntrada = obtenerMesaEntradaConsultorioPorIdMesaEntrada($inspeccionHabilitacion['IdMesaEntrada']);
                                                
                                                if(!$consultaDatosMesaEntrada)
                                                {
                                                    $estadoModificacion = -1;
                                                }
                                                else
                                                {
                                                    if($consultaDatosMesaEntrada -> num_rows == 0)
                                                    {
                                                        $estadoModificacion = -1;
                                                    }
                                                    else
                                                    {
                                                        $datosMesaEntrada = $consultaDatosMesaEntrada -> fetch_assoc();
                                                        $estadoModificacion = realizarAltaConsultorioColegiado($datosMesaEntrada['IdConsultorio'],$datosMesaEntrada['IdColegiado'],$idInspectorHabilitacion);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            if($habilitado == "N")
                            {
                                $estadoModificacion = realizarModificacionInspectorHabilitacionPorNo(trim($idInspectorHabilitacion),trim($fechaInspeccion));
                            }
                        }
                    }
                }
            }
            
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
                $estadoBaja = realizarBajaInspectorHabilitacion($idInspectorHabilitacion);
                
                switch ($estadoBaja)
                {
                    case -1: echo "La inspección asignada no se pudo dar de baja. Intente nuevamente.";
                        break;
                    case 1: echo "La inspección asignada se dio de baja correctamente.";
                        break;
                }
            }
    }
    else
    {
        echo "Hubo un error en la Base de Datos";
    }

?>
