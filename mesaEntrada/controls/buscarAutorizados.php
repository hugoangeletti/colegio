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
if (isset($_GET['cWay'])) {
    $cWay = $_GET['cWay'];

    if (date("m") >= 6) {
        $periodoActual = date("Y");
    } else {
        $periodoActual = date("Y") - 1;
    }
}
$colegiado = obtenerColegiados($_REQUEST['term']);
$colegiados = array();

while ($row = $colegiado->fetch_assoc()) {
    //Colsulto Estado Matricular
    $estadoColegiado = $row['Estado'];
    $consultaEstadoMatricular = obtenerTipoMovimiento($estadoColegiado);
    $estadoMatricular = $consultaEstadoMatricular->fetch_assoc();
    if ($estadoMatricular['Id'] == 1) {
        if (isset($cWay)) {
            if ($cWay == "Ins") {
                $inspector = obtenerInspectorPorIdColegiado($row['Id']);
                if ($inspector->num_rows == 0) {
                    $colegiados[$row['Id']] = $row['Matricula'];
                }
            }
            /* else
              {
              if($cWay == "Aut")
              {
              //Consulto Estado Tesoreria
              $estadoTesoreria = estadoTesoreriaPorColegiado($row['Id'], $periodoActual);
              if($estadoTesoreria == 0)
              {
              $colegiados[$row['Id']] = $row['Matricula'];
              }
              }
              } */
        }
    }
    if ($cWay == "Aut") {
        $colegiados[$row['Id']] = $row['Matricula'];
    }
}
echo json_encode($colegiados);
?>