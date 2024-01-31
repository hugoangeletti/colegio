<?php 
header('Content-Type" => application/json');
require_once ('../dataAccess/config.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/cursoLogic.php');
$asistentes = obtenerAsistentesAutocompletar();	
$data=array('result'=>true,'data'=>$asistentes['datos']);
//var_dump($asistentes['datos']);exit;
echo json_encode($data );