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
    $idTipoMesaEntrada = "7";
    $observaciones = $_POST['observaciones'];
    $ColORem = "IdColegiado";
    $fecha = invertirFecha($_POST['fecha']);
    $autorizado = $_POST['autorizado'];
    $documentoAutorizado = $_POST['documentoAutorizado'];
    $parentezco = $_POST['parentezco'];
    $autorizado2 = $_POST['autorizado2'];
    $documentoAutorizado2 = $_POST['documentoAutorizado2'];
    $parentezco2 = $_POST['parentezco2'];

    if ($idColegiado != "") {
        $yaHay = obtenerAutoprescripcionPorIdColegiado($idColegiado);

        if (!$yaHay) {
            $estadoAlta = 89;
        } else {
            if ($yaHay->num_rows == 0) {
                $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);

                $estadoAlta = realizarAltaAutoprescripcion($idMesaEntrada, $autorizado, $fecha, $documentoAutorizado, $parentezco, $autorizado2, $documentoAutorizado2, $parentezco2);
            } else {
                $estadoAlta = 89;
            }
        }
    } else {
        $estadoAlta = -1;
    }

    if ($estadoAlta == 1) {
        $text = "La autoprescripción se dio de alta correctamente.";
    } else {
        if ($estadoAlta == 89) {
            $text = "El trámite que desea realizar ya se efectuó el día de hoy.";
        } else {
            $text = "Hubo un error al dar de alta la autoprescripción. Intente nuevamente.";
        }
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
