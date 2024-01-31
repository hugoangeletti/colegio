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
    $matricula = $_POST['matricula'];
    $idTipoMovimiento = $_POST['tipoMovimiento'];
    if (isset($_POST['fechaDesde']) && ($_POST['fechaDesde'] != "")) {
        $fechaDesde = invertirFecha($_POST['fechaDesde']);
    } else {
        $fechaDesde = -1;
    }
    if (isset($_POST['fechaHasta']) && ($_POST['fechaHasta'] != "")) {
        $fechaHasta = invertirFecha($_POST['fechaHasta']);
    } else {
        $fechaHasta = -1;
    }

    $distritoCambio = $_POST['distritoCambio'];
    $distritoOrigen = $_POST['distritoOrigen'];
    $observaciones = $_POST['observaciones'];

    $idMesaEntrada = $_POST['idMesaEntrada'];

    $estadoAlta = -1;

    if ($idMesaEntrada == "") {
        $text = "Hubo un error al recibir los datos necesarios. Intente nuevamente.";
    } elseif ($matricula == "") {
        $text = "La matrícula está vacía.";
    } elseif ($idTipoMovimiento == "") {
        $text = "No seleccionó el movimiento.";
    } elseif ($fechaDesde == "") {
        $text = "La fecha desde está vacía.";
    } else {
        $colegiado = obtenerColegiadoPorMatricula($matricula);

        if (!$colegiado) {
            $text = "Hubo un error en la matrícula.";
        } else {
            if ($colegiado->num_rows == 0) {
                $text = "No existe ningún colegiado con esa matrícula.";
            } else {
                $datoColegiado = $colegiado->fetch_assoc();

                if ($idTipoMovimiento == 1) {

                    $colegiadoMovimiento = obtenerColegiadoMovimientoPorDistrito($datoColegiado['Id'], $distritoOrigen);

                    //var_dump($colegiadoMovimiento);
                    if ($colegiadoMovimiento->num_rows == 0) {
                        $text = "El distrito del cual se quiere realizar la baja, no corresponde con un distrito válido para dicho colegiado.";
                    } else {
                        $datoColegiadoMovimiento = $colegiadoMovimiento->fetch_assoc();

                        $estadoAlta = realizarBajaInscripcionOtroDistrito($datoColegiadoMovimiento['Id'], trim($fechaHasta), $datoColegiado['Id'], $idMesaEntrada);
                        if ($estadoAlta == 1) {
                            $text = "El movimiento se registró correctamente.";
                        } else {
                            $text = "Hubo un error al registrar el movimiento. Intente nuevamente.";
                        }
                    }
                } else {
                    $estadoAlta = realizarAltaMovimientoDistritos($datoColegiado['Id'], trim($idTipoMovimiento), trim($fechaDesde), trim($fechaHasta), trim($distritoCambio), trim($distritoOrigen), trim($idMesaEntrada), trim($observaciones));
                    if ($estadoAlta == 1) {
                        $text = "El movimiento se dio de alta correctamente.";
                    } else {
                        $text = "Hubo un error al dar de alta el movimiento. Intente nuevamente.";
                    }
                }
            }
        }
    }
} else {
    $estadoAlta = -1;
    $text = "Hubo un error al recibir los datos necesarios. Intente nuevamente.";
}
$dev = array(
    "estado" => $estadoAlta,
    "texto" => $text,
    "importe" => 0
);

echo json_encode($dev);
?>
