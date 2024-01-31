<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';


if (isset($_POST['idMesaEntrada'])) {
    $idMesaEntrada = $_POST['idMesaEntrada'];

    if (isset($_POST['tipoAccion'])) {
        $accion = $_POST['tipoAccion'];
    }

    if ($accion == "M") {
        $fechaEntrega = invertirFecha($_POST['fechaEntrega']);
        $observaciones = $_POST['observaciones'];
        $idTipoEntrega = $_POST['idTipoEntrega'];

        $infoMesaEntrada = obtenerEntregaPorId($idMesaEntrada);
        $dataMesaEntrada = $infoMesaEntrada->fetch_assoc();
        if ($dataMesaEntrada['FechaIngreso'] != date("Y-m-d")) {
            $text = "No puede modificar un movimiento que no corresponda al día de hoy.
                            Contáctese con informática.";
            $estadoBaja = -1;
        } else {
            $estadoModificacion = realizarModificacionEntrega(trim($idMesaEntrada), trim($fechaEntrega), trim($idTipoEntrega), trim($observaciones));

            switch ($estadoModificacion) {
                case -1: $text = "La modificación no se pudo realizar. Intente nuevamente.";
                    break;
                case 1: $text = "La modificación se realizó correctamente.";
                    break;
            }
        }
    } else {
        if ($accion == "B") {
            $infoMesaEntrada = obtenerEntregaPorId($idMesaEntrada);
            $dataMesaEntrada = $infoMesaEntrada->fetch_assoc();
            if ($dataMesaEntrada['FechaIngreso'] != date("Y-m-d")) {
                $text = "No puede dar de baja un movimiento que no corresponda al día de hoy.
                            Contáctese con informática.";
                $estadoBaja = -1;
            } else {
                $estadoBaja = realizarBajaMesaEntrada($idMesaEntrada);

                switch ($estadoBaja) {
                    case -1: $text = "La entrega no se pudo dar de baja. Intente nuevamente.";
                        break;
                    case 1: $text = "La entrega se dio de baja correctamente.";
                        break;
                }
            }
        } else {
            $text = "NO ENTRA A LOS ACCION";
        }
    }
} else {
    $text = "Hubo un error en la Base de Datos";
}

if (isset($estadoBaja)) {
    $estado = $estadoBaja;
} else {
    if (isset($estadoModificacion)) {
        $estado = $estadoModificacion;
    }
}

$dev = array(
    "estado" => $estado,
    "texto" => $text,
    "importe" => 0,
    "action" => $accion
);

echo json_encode($dev);
?>
