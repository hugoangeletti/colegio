<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/sumarianteLogic.php');
$sumariantes = obtenerSumarianteAutocompletar();
$data=array('result'=>true,'data'=>$sumariantes['datos']);
//var_dump($sumariantes['datos']);exit;
echo json_encode($data );