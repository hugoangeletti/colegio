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
        $idTipoMesaEntrada = "5";
        $observaciones = $_POST['observaciones'];
        $ColORem = "IdColegiado";
        
        if($idColegiado != "")
        {
            $yaHay = obtenerMatriculaJPorIdColegiado($idColegiado);
                
            if(!$yaHay)
            {
                $estadoAlta = 89;
            }
            else
            {
                if($yaHay -> num_rows == 0)
                {
                    $estadoAlta = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);
                }
                else
                {
                    $estadoAlta = 89;
                }
            }
        }
        else 
        {
            $estadoAlta = -1;
        }
        
        if($estadoAlta != -1)
        {
            $consultaTipoPago = obtenerTipoPagoPorId(67);
            $tipoPago = $consultaTipoPago -> fetch_assoc();
            
            $consultaTipoValor = obtenerTipoValorPorId(34);
            $tipoValor = $consultaTipoValor -> fetch_assoc();
            
            $importeTotal = round($tipoValor['Valor'] * $tipoPago['CantidadHoras'],0);
            
            $deuda = realizarAltaColegiadoDeuda(trim($idColegiado),trim($tipoPago['Id']),$importeTotal,trim($estadoAlta));
            $text = "La matrícula J se dio de alta correctamente.";
        }
        else
        {
            if($estadoAlta == 89)
            {
                $text = "El trámite que desea realizar ya se efectuó el día de hoy.";
            }
            else
            {
                $text = "Hubo un error al dar de alta la matrícula J. Intente nuevamente.";
            }
        }
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
