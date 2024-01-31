<?php

    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    if(isset($_GET['idConsultorio']))
    {
        $idConsultorio = $_GET['idConsultorio'];
        
        $consultaCantConsultorioME = obtenerCantidadConsultoriosMesaEntrada($idConsultorio);
        $cantConsultorioME = $consultaCantConsultorioME -> fetch_assoc();
        $consultaCantConsultorioCC = obtenerCantidadConsultoriosHabilitados($idConsultorio);
        $cantConsultorioCC = $consultaCantConsultorioCC -> fetch_assoc();
        $consultaCantPoliconsultorio = obtenerConsultorioPorId($idConsultorio);
        $cantPoliconsultorio = $consultaCantPoliconsultorio -> fetch_assoc();
        
        if(($cantConsultorioME['cant']+$cantConsultorioCC['cant'])<$cantPoliconsultorio['CantidadConsultorios'])
        {
            $idTipoPago = 46;
            
        }
        else
        {
            $idTipoPago = 53;
        }
    }
    
    $href = "mesaEntradaFormConsultorio.php?idConsultorio=".$idConsultorio."&action=A&matricula=".$_GET['matricula']."&idTP=".$idTipoPago;
    header("Location: ".$href);

?>
