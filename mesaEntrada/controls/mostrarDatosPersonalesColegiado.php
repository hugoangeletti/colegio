<?php
 
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    $consultaDatosPersonalesColegiado = obtenerDatosPersonalesColegiadoPorId($aColegiado['Id']);

    if(!$consultaDatosPersonalesColegiado)
    {
        ?>
<tr><td colspan='6'><span class="mensajeERROR">Hubo un error en el sistema.</span></td></tr>
        <?php
    }
    else
    {
        if($consultaDatosPersonalesColegiado -> num_rows == 0)
        {
            ?>
<tr><td colspan='6'><span class="mensajeWARNING">El colegiado no presenta datos personales en el sistema.</span></td></tr>
            <?php
        }
        else
        {
            $datosPersonalesColegiado = $consultaDatosPersonalesColegiado -> fetch_assoc();
            ?>

<table>
    <tr>
        <td><b>Domicilio Particular:</b></td>
        <td><?php echo utf8_encode($datosPersonalesColegiado['Calle'])." ".utf8_encode($datosPersonalesColegiado['Lateral'])." Nº ".$datosPersonalesColegiado['Numero']." ".$datosPersonalesColegiado['Piso'].$datosPersonalesColegiado['Departamento'] ?></td>
    </tr>
    <tr>
        <td><b>Teléfono Fijo:</b></td>
        <td><?php echo $datosPersonalesColegiado['TelefonoFijo'] ?></td>
    </tr>
    <tr>
        <td><b>Teléfono Celular:</b></td>
        <td><?php echo $datosPersonalesColegiado['TelefonoMovil'] ?></td>
    </tr>
    <tr>
        <td><b>E-Mail:</b></td>
        <td><?php echo utf8_encode($datosPersonalesColegiado['CorreoElectronico']) ?></td>
    </tr>
</table>

            <?php
        }
    }
?>