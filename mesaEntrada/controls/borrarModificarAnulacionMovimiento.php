<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

$estadoBaja = -1;
if (isset($_POST['idMesaEntrada'])) {
    $idMesaEntrada = $_POST['idMesaEntrada'];

    $estadoBaja = realizarBajaAnulacion(trim($idMesaEntrada));
    obtenerUltimoColegiadoMovimientoPorIdColegiado();

    switch ($estadoBaja) {
        case -1: $text = "La anulación no se pudo dar de baja. Intente nuevamente.";
            break;
        case 1: $text = "La anulación se dio de baja correctamente.";
            break;
    }
} else {
    $text = "Hubo un error en la Base de Datos";
}

$estado = $estadoBaja;

$dev = array(
    "estado" => $estado,
    "texto" => $text,
    "importe" => 0,
    "action" => $accion
);

echo json_encode($dev);
?>
