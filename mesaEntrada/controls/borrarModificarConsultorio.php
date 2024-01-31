<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

$estado = -1;
$text = "";

if (isset($_POST['idConsultorio'])) {
    $idConsultorio = $_POST['idConsultorio'];

    if (isset($_POST['tipoAccion'])) {
        $accion = $_POST['tipoAccion'];
    }

    if ($accion == "M") {

        $tipoConsultorio = $_POST['tipoConsultorio'];
        $nombreConsultorio = $_POST['nombreConsultorio'];
        if ($tipoConsultorio != "") {
            if ($tipoConsultorio == "U") {
                $cantidad = 1;
            } else {
                if ($tipoConsultorio == "P") {
                    $cantidad = $_POST['cantConsultorios'];
                } else {
                    $cantidad = 0;
                }
            }
        }

        $calle = $_POST['calle'];
        $lateral = $_POST['lateral'];
        $numero = $_POST['numero'];
        $piso = $_POST['piso'];
        $dpto = $_POST['departamento'];
        $tel = $_POST['tel'];
        $idLocalidad = $_POST['localidad'];
        $cp = $_POST['CP'];
        $observaciones = $_POST['observaciones'];


        if ($tipoConsultorio != "") {
            if ($idLocalidad != "") {
                $estado = realizarModificacionConsultorio($tipoConsultorio, trim($nombreConsultorio), trim($calle), trim($lateral), trim($numero), trim($piso), trim($dpto), trim($tel), $idLocalidad, trim($cp), trim($observaciones), trim($cantidad), $idConsultorio);

                if ($estado == 1) {
                    $text = "La modificación del consultorio fue exitosa.";
                } else {
                    $text = "La modificación del consultorio no se pudo llevar a cabo correctamente. Vuelva a intentarno en unos minutos.";
                }
            } else {
                $text = "Se olvidó de seleccionar la localidad del consultorio.";
            }
        } else {
            $text = "Se olvidó de seleccionar el tipo de consultorio.";
        }
    } else {
        if ($accion == "B") {
            $estado = realizarBajaConsultorio($idConsultorio);

            if ($estado == 1) {
                $text = "El consultorio se dio de baja correctamente.";
            } else {
                $text = "El consultorio no se pudo dar de baja. Intente nuevamente.";
            }
        }
    }
} else {
    $text = "Hubo un error en la Base de Datos";
}

$dev = array(
    "estado" => $estado,
    "texto" => $text
);

echo json_encode($dev);
?>
