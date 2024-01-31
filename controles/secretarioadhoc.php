<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/secretarioadhocLogic.php');
$secretarios = obtenerSecretarioadhocAutocompletar();
$data=array('result'=>true,'data'=>$secretarios['datos']);
//var_dump($sumariantes['datos']);exit;
echo json_encode($data );