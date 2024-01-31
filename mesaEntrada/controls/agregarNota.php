<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

/*
 * Realizo la comprobación de si es Colegiado o Remitente,
 * para establecer las relaciones entre las variables y los campos de la
 * BD que serán cargadas.
 */

if (isset($_POST)) {
    if (isset($_POST['idColegiado'])) {
        $id = $_POST['idColegiado'];
        $ColORem = "idColegiado";
        $tipoRemitente = "C";
    } else if (isset($_POST['idRemitente'])) {
        $id = $_POST['idRemitente'];
        $ColORem = "idRemitente";
        $tipoRemitente = "O";
    }
    $idTipoMesaEntrada = "3";
    $observaciones = $_POST['observaciones'];
    $tema = $_POST['tema'];

    if (isset($_POST['incluyeLista']) && ($_POST['incluyeLista'] != "")) {
        $incluye = "S";
    } else {
        $incluye = NULL;
    }

    if ($id != "") {
        $idMesaEntrada = realizarAltaMesaEntrada($id, $tipoRemitente, $ColORem, $idTipoMesaEntrada, utf8_decode($observaciones));

        $estadoAlta = realizarAltaNota($idMesaEntrada, utf8_decode($tema), $incluye);
    } else {
        $estadoAlta = -3;
    }
    if ($estadoAlta == 1) {
        //include 'hojaRuta.php';
        $text = "La nota se dio de alta correctamente.";
    } else {
        $text = "Hubo un error al dar de alta la nota. Intente nuevamente.";
    }
}
$dev = array(
    "estado" => $estadoAlta,
    "texto" => $text,
    "importe" => 0
);

echo json_encode($dev);
?>
