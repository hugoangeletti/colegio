<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/especialidadLogic.php');
$especialidades = obtenerEspecialidadesAutocompletar();
$data=array('result'=>true,'data'=>$especialidades['datos']);
//var_dump($especialidades['datos']);exit;
echo json_encode($data);