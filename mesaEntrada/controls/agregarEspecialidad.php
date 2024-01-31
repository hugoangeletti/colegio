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
        $idTipoMesaEntrada = "2";
        $observaciones = $_POST['observaciones'];
        $idTipoEspecialidad = $_POST['tipoEspecialidad'];
        $ColORem = "IdColegiado";
        if(isset($_POST['distrito']))
        {
            $distrito = $_POST['distrito'];
        }
        else
        {
            $distrito = 0;
        }
        if(isset($_POST['especialidad']))
        {
            $idEspecialidad = $_POST['especialidad'];
        }
        else
        {
            $idEspecialidad = "";
        }
        
        if($idColegiado != "")
        {
            if(($idTipoEspecialidad != "") && ($idEspecialidad != ""))
            {
                $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);
                
                $estadoAlta = realizarAltaEspecialidad($idMesaEntrada, trim($idEspecialidad), trim($idTipoEspecialidad), $distrito);
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
        if($estadoAlta == 1)
        {
            $consultaIdTipoPago = obtenerTipoPagoPorTipoEspecialidad(trim($idTipoEspecialidad));
            $idTipoPago = $consultaIdTipoPago -> fetch_assoc();
            
            $consultaTipoPago = obtenerTipoPagoPorId(trim($idTipoPago['IdTipoPago']));
            $tipoPago = $consultaTipoPago -> fetch_assoc();
            
            $consultaTipoValor = obtenerTipoValorPorId(34);
            $tipoValor = $consultaTipoValor -> fetch_assoc();
            
            $importeTotal = round($tipoValor['Valor'] * $tipoPago['CantidadHoras'],0);
            
            $deuda = realizarAltaColegiadoDeuda(trim($idColegiado),trim($idTipoPago['IdTipoPago']),$importeTotal,trim($idMesaEntrada));
            $text = "La especialidad se dio de alta correctamente.";
        }
        else
        {
            if($idEspecialidad == "")
            {
                $text = "No seleccionó la especialidad.";
            }
            else
            {
                $text = "Hubo un error al dar de alta la especialidad. Intente nuevamente.";
            }
        }
    }
    if(isset($idTipoPago))
    {
        $importe = $importeTotal;
    }
    else
    { 
        $importe = -1;
    }
    
    $dev = array(
        "estado" => $estadoAlta,
        "texto" => $text,
        "importe" => $importe
    );
            
            echo json_encode($dev);
    
?>
