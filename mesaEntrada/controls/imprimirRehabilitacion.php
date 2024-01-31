<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $consultaPresidente = obtenerPresidenteDistrito(1);
    
    
    
?>

<span>Señor</span><br> 
<span>Presidente del</span><br>
<span>Colegio de Médicos - Distrito I</span><br>
<span><?php 
    if(!$consultaPresidente)
    {
        die("Hubo un error en el sistema.");
    }
    else
    {
        if($consultaPresidente -> num_rows == 0)
        {
            die("No existe presidente para el distrito seleccionado.");
        }
        else
        {
            $presidente = $consultaPresidente -> fetch_assoc();
            echo $presidente['Presidente'];
        }
    }
?>
</span><br>
<span><u><i>S/D</i></u></span><br>

<p class='textCuerpoMovimiento'>Tengo el agrado de dirigirme a Usted con motivo de solicitar la
    <b>REHABILITACIÓN</b> de mi matrícula provincial nº <b><?php echo $datoColegiado['Matricula']; ?></b> 
    perteneciente a <b><?php echo trim($datoColegiado['Apellido']).' '.trim($datoColegiado['Nombres']); ?></b>,  
    a partir del día <b><?php echo invertirFecha($datoMesaEntrada['Fecha']); ?></b></p>
<p class="textCuerpoMovimiento">Sin otro particular, saluda muy atentamente.-</p>
<br><br>

<p class='firma'>______________________________<br><br>
DR.  ______________________________<br><br>
M.P. ______________________________</p><br>