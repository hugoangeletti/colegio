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

<p class='textCuerpoMovimiento'>Tengo el agrado de dirigirme a Usted, a los efectos de solicitar la
    <b>BAJA POR EGRESO DEFINITIVO</b> de mi matrícula provincial Nº <b><?php echo $datoColegiado['Matricula']; ?></b>,  
    perteneciente a <b><?php echo trim($datoColegiado['Apellido']).' '.trim($datoColegiado['Nombres']); ?></b>,  
    a partir del día <b><?php echo invertirFecha($datoMesaEntrada['Fecha']); ?></b>, con motivo 
    de haber dejado de ejercer la profesión en jurisdicción de este Distrito I, para continuar en el 
    <b>Distrito____</b></p>
<p class="textCuerpoMovimiento">Sin otro particular, saluda muy atentamente.-</p>
<br><br>

<div class="firmaDefuncion">
<table class="tablaConsultorio">
    <tr>
        <td>FIRMA</td>
        <td>____________________________</td>
    </tr>
    <tr>
        <td>ACLARACIÓN</td>
        <td>____________________________</td>
    </tr>  
    <tr>
        <td>M.P.</td>
        <td>____________________________</td>
    </tr>
    <tr>
        <td>DOMICILIO</td>
        <td>____________________________</td>
    </tr>
    <tr>
        <td>LOCALIDAD</td>
        <td>____________________________</td>
    </tr>
    <tr>
        <td>TELÉFONO</td>
        <td>____________________________</td>
    </tr>
</table>
</div>