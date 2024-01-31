<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/colegiadoLogic.php');
if (isset($_GET['activos']) && $_GET['activos'] == "SI") {
	$colegiados = obtenerColegiadosAutocompletar('activos');	
} else {
	$colegiados = obtenerColegiadosAutocompletar('todos');
}

$data=array('result'=>true,'data'=>$colegiados['datos']);
//var_dump($colegiados['datos']);exit;
echo json_encode($data );