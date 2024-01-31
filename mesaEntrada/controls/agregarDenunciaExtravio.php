<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_POST)) {
    $idColegiado = $_POST['idColegiado'];
    $tipoRemitente = "C";
    $idTipoMesaEntrada = "9";
    $observaciones = $_POST['observaciones'];
    $ColORem = "IdColegiado";
    $fechaDenuncia = invertirFecha($_POST['fechaDenuncia']);
    $fechaExtravio = invertirFecha($_POST['fechaExtravio']);
    $idTipoDenuncia = $_POST['idTipoDenuncia'];

    if ($idColegiado != "") {
        $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);

        $estadoAlta = realizarAltaDenunciaExtravio($idMesaEntrada, $fechaDenuncia, $fechaExtravio, $idTipoDenuncia);
    } else {
        $estadoAlta = -1;
    }

    if ($estadoAlta == 1) {
        $text = "La denuncia de extravío se dio de alta correctamente.";
    } else {
        $text = "Hubo un error al dar de alta la denuncia de extravío. Intente nuevamente.";
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
