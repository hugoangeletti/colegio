<?php

// Se encarga de mostrar todo el listado de remitentes que se encuentran
// en la BD, para que el usuario elija desde el popup.

require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';
if (date("m") >= 6) {
    $periodoActual = date("Y");
} else {
    $periodoActual = date("Y") - 1;
}

$activos = array(1, 5, 10, 19, 20, 8);

//Obtengo la matricula por POST
$matricula = $_POST['matricula'];
//Seteo el estado de retorno en false
$estado = false;
$texto = "";
//Traigo de la BD la info del colegiado por la matricula
$consultaColegiado = obtenerColegiadoPorMatricula($matricula);
$datoColegiado = $consultaColegiado -> fetch_assoc();
//Seteo el estado del colegiado y obtengo su estado matricular
$estadoColegiado = $datoColegiado['Estado'];
$consultaEstadoMatricular = obtenerTipoMovimiento($estadoColegiado);
$estadoMatricular = $consultaEstadoMatricular->fetch_assoc();

//Pregunto por todas los estados que correspondan a ACTIVO
if (in_array($estadoMatricular['Id'], $activos)) {
    //Consulto Estado Tesoreria
    $estadoTesoreria = estadoTesoreriaPorColegiado($datoColegiado['Id'], $periodoActual);
    if ($estadoTesoreria == 0) {
        //Si esta al dia y corresponde con ACTIVO, seteo estado de retorno
        //en true
        $estado = true;
    } else {
        $texto = "El colegiado que desea autorizar no cuenta con el estado de tesorería AL DÍA.";
    }
} else {
    $texto = "El colegiado que desea autorizar no es un colegiado ACTIVO.";
}

$data = array(
    'estado' => $estado,
    'texto' => $texto
);

echo json_encode($data);
?>