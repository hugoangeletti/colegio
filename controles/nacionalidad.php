<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/paisLogic.php');
$nacionalidades = obtenerNacionalidades();
$data=array('result'=>true,'data'=>$nacionalidades['datos']);
//var_dump($colegiados['datos']);exit;
echo json_encode($data );