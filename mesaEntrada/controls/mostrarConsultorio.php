<?php
 
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
    $consultaDatosConsultorio = obtenerDatosConsultorioPorId($_GET['idConsultorio']);

    if(!$consultaDatosConsultorio)
    {
        ?>
<tr><td colspan='6'><span class="mensajeERROR">Hubo un error en el sistema.</span></td></tr>
        <?php
    }
    else
    {
        if($consultaDatosConsultorio -> num_rows == 0)
        {
            ?>
<tr><td colspan='6'><span class="mensajeWARNING">El colegiado no presenta datos personales en el sistema.</span></td></tr>
            <?php
        }
        else
        {
            $datosConsultorio = $consultaDatosConsultorio -> fetch_assoc();
            ?>
<fieldset>
    <legend>Datos del Consultorio</legend>
<table>
    <?php
        if($datosConsultorio['nombreConsultorio']!="")
        {
    ?>
    <tr>
        <td><b>Nombre del Consultorio:</b></td>
        <td><?php echo $datosConsultorio['nombreConsultorio'] ?></td>
    </tr>
    <?php
        }
    ?>
    <tr>
        <td><b>Domicilio:</b></td>
        <td><?php echo utf8_encode($datosConsultorio['Calle'])." ".utf8_encode($datosConsultorio['Lateral'])." Nº ".$datosConsultorio['Numero']." ".$datosConsultorio['Piso'].$datosConsultorio['Departamento'] ?></td>
    </tr>
    <tr>
        <td><b>Teléfono:</b></td>
        <td><?php echo $datosConsultorio['Telefono'] ?></td>
    </tr>
    <tr>
        <td><b>Localidad:</b></td>
        <td><?php echo $datosConsultorio['nombreLocalidad'] ?></td>
    </tr>
</table>
</fieldset>
            <?php
        }
    }
?>