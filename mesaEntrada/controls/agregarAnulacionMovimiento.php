<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_POST) || isset($_GET)) {
    if (isset($_GET['idColegiado'])) {
        $idColegiado = $_GET['idColegiado'];
    } else {
        $idColegiado = $_POST['idColegiado'];
    }
    if (isset($_GET['id'])) {
        $idMesaEntradaMovimiento = $_GET['id'];
    } else {
        $idMesaEntradaMovimiento = $_POST['idMesaEntradaMovimiento'];
    }
    if (isset($_POST['observaciones'])) {
        $observaciones = $_POST['observaciones'];
    } else {
        $observaciones = "Solciitado por mail";
    }
    $tipoRemitente = "C";
    $idTipoMesaEntrada = "8";
    $ColORem = "IdColegiado";

    if ($idColegiado != "") {
        $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);

        $estadoAlta = realizarAltaAnulacionMovimiento($idMesaEntrada, $idMesaEntradaMovimiento);

        //$estadoAlta = 1;
        if ($estadoAlta == 1) {
            $idMesaEntrada = obtenerMesaEntradaPorIdMovimiento($idMesaEntradaMovimiento);
            
            //echo "IDME -> ".$idMesaEntrada;
            
            $estadoAlta = rollBackBajaMovimiento($idMesaEntrada);
        }
    } else {
        $estadoAlta = -1;
    }

    if ($estadoAlta == 1) {
        $text = "La anulación se realizó correctamente.";
    } else {
        $text = "Hubo un error al realizar la anulación. Intente nuevamente.";
    }
}

$dev = array(
    "estado" => $estadoAlta,
    "texto" => $text,
    "importe" => 0,
    "action" => 'A'
);

echo json_encode($dev);
?>
