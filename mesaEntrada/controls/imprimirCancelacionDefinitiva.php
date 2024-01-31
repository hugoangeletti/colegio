<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
?>

<span>Sr. Presidente del</span><br>
<span>Colegio de Médicos - DISTRITO I</span><br>
<span><u><i>S/D</i></u></span><br>

<p class='textCuerpoMovimiento'>Tengo el agrado de dirigirme a Usted, y por su 
intermedio a quien corresponda, con el motivo de solicitarle la
<b>CANCELACIÓN DEFINITIVA</b> de la M.P. Nº <b><?php echo $datoColegiado['Matricula']; ?></b> 
perteneciente al Dr. <b><?php echo utf8_encode($datoColegiado['Apellido'])." ".utf8_encode($datoColegiado['Nombres']); ?></b> 
a partir del día <b><?php echo invertirFecha($datoMesaEntrada['Fecha']); ?></b> 
dado que no ejerceré más mi profesión en La Provincia de Buenos Aires.</p>
<p class="textCuerpoMovimiento">Sin otro particular, saluda muy atentamente.-</p>
<br><br>

<p class='firma'>______________________________<br><br>
DR.  ______________________________<br><br>
M.P. ______________________________</p><br>