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
$text = "";
if (isset($_POST['idColegiadoMovimientoDistritos'])) {
    $idColegiadoMovimientoDistritos = $_POST['idColegiadoMovimientoDistritos'];
    $idMesaEntrada = $_POST['idMesaEntrada'];

    if (isset($_POST['tipo']) && ($_POST['tipo'])) {

        if ($_POST['tipo'] == "B") {
            $estadoBaja = realizarBajaMovimientoDistritosBajaInscripcion($idColegiadoMovimientoDistritos, $idMesaEntrada);
        } else {
            $estadoBaja = realizarBajaMovimientoDistritos(trim($idColegiadoMovimientoDistritos));
        }
    } else {
        $estadoBaja = -1;
    }
    if ($estadoBaja == 1) {
        $text = "El movimiento fue borrado correctamente.";
    } else {
        $text = "Hubo un error al borrar el movimiento seleccionado. Intente nuevamente.";
    }
} else {
    $text = "Hubo un error en la Base de Datos";
}

$dev = array(
    "estado" => $estadoBaja,
    "texto" => $text,
    "importe" => 0
);

echo json_encode($dev);
?>
