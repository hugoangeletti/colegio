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
        $idColegiado = $_POST['idColegiado'];
        $tipoRemitente = "C";
        $idTipoMesaEntrada = "4";
        $observaciones = "";
        $idConsultorio = $_POST['idConsultorio'];
        //$idEspecialidad = $_POST['especialidad'];
        $especialidades = $_POST['especialidad'];
        $ColORem = "IdColegiado";
        $idTipoPago = $_POST['idTipoPago'];
        if(isset($_POST['autorizados']))
        {
            $autorizados = $_POST['autorizados'];
        }
        
        if($idColegiado != "")
        {
            if($idConsultorio != "")
            {
                if($especialidades != "")
                {
                    $consultaYaHay = obtenerHabilitacionPorConsultorioPorColegiado($idColegiado, $idConsultorio);
                    
                    if(!$consultaYaHay)
                    {
                        $estadoAlta = 89;
                    }
                    else
                    {
                        if($consultaYaHay -> num_rows == 0)
                        {
                            $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);

                            //$estadoAlta = realizarAltaHabilitacionConsultorio($idMesaEntrada, $idConsultorio, $idEspecialidad);
                            
                            foreach ($especialidades as $key => $idEspecialidad)
                            {
                                if($idEspecialidad != "")
                                {
                                    $estadoAlta = realizarAltaHabilitacionConsultorio($idMesaEntrada, $idConsultorio, $idEspecialidad);
                                }
                            }

                            if(($estadoAlta == 1) && (isset($autorizados)))
                            {

                                    $idMesaEntradaConsultorio = obtenerUltimaMesaEntradaConsultorio();
                                    if(!$idMesaEntradaConsultorio)
                                    {
                                        $datoId = -1;
                                    }
                                    else
                                    {
                                        $datoId = $idMesaEntradaConsultorio -> fetch_assoc();
                                    }
                                    
                                    foreach ($autorizados as $key => $matricula)
                                    {
                                        if($matricula != "")
                                        {
                                            $consultaColegiado = obtenerColegiadoPorMatricula($matricula);
                                            if($consultaColegiado)
                                            {
                                                if($consultaColegiado -> num_rows != 0)
                                                {
                                                    $colegiado = $consultaColegiado -> fetch_assoc();
                                                    $estadoAlta = realizarAltaHabilitacionConsultorioAutorizado($datoId['IdMesaEntradaConsultorio'], $colegiado['Id']);
                                                }
                                            }
                                        }
                                    }
                            }
                        }
                        else
                        {
                            $estadoAlta = 89;
                        }
                    }
                }
                else
                {
                    $estadoAlta = -4;
                }
            }
            else 
            {
                $estadoAlta = -2;
            }
        
        }
        else
        {
            $estadoAlta = -3;
        }
        if(($estadoAlta == 1))
        {
            $consultaTipoPago = obtenerTipoPagoPorId($idTipoPago);
            $tipoPago = $consultaTipoPago -> fetch_assoc();
            
            $consultaTipoValor = obtenerTipoValorPorId(34);
            $tipoValor = $consultaTipoValor -> fetch_assoc();
            
            $importeTotal = round($tipoValor['Valor'] * $tipoPago['CantidadHoras'],0);
            
            $deuda = realizarAltaColegiadoDeuda(trim($idColegiado),trim($tipoPago['Id']),$importeTotal,trim($idMesaEntrada));
            $text = "La habilitación se dio de alta correctamente.";
        }
        else
        {
            if($estadoAlta == 89)
            {
                $text = "El trámite que desea realizar ya se efectuó el día de hoy.";
            }
            else
            {
                $text = "Hubo un error al dar de alta la habilitación. Intente nuevamente.";
            }
        }
    }
    else
    {
        $text = "Hubo un error al dar de alta la habilitación. Intente nuevamente.";
    }
    if(isset($tipoPago))
    {
        $importe = $importeTotal;
    }
    else
    {
        $importe = 0;
    }
    $dev = array(
        "estado" => $estadoAlta,
        "texto" => $text,
        "importe" => $importe
    );
            
    echo json_encode($dev); 
?>
