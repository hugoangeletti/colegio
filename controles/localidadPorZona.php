<?php
require_once ('../dataAccess/config.php');
include_once '../dataAccess/funcionesConector.php';
//include_once '../dataAccess/funcionesPhp.php';
include_once '../dataAccess/localidadLogic.php';

$idZona = $_GET['idZona'];
$resLocalidades = obtenerLocalidadesPorZona($idZona);
if ($resLocalidades['estado']) {
	echo '<option value = "">Seleccione una Delegación</option>';
	foreach ($resLocalidades['datos'] as $localidad) {
		echo '<option value = "'.$localidad['codigoPostal'].'">'.$localidad['nombre'].'</option>';
	}
} else {
	echo $resLocalidades['mensaje'];
}