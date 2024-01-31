<?php
    require_once 'seguridad.php';

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $IdEnvioUniversidad = $_GET['idEU'];
    
    $envio_colegiados = obtenerEnvioUniversidadColegiadosPorIdEU($IdEnvioUniversidad);
    
?>

<table id="tablaEstadisticas">
    <tr>
        <th>Matrícula</th>
        <th>Apellido</th>
        <th>Nombres</th>
        <th>Fecha Matriculación</th>
    </tr>
<?php
    
    if($envio_colegiados)
    {
        if($envio_colegiados -> num_rows != 0)
        {
            while($row = $envio_colegiados -> fetch_assoc())
            {
?>
    <tr>
        <td><?php echo $row['Matricula'] ?></td>
        <td><?php echo utf8_encode($row['Apellido']) ?></td>
        <td><?php echo utf8_encode($row['Nombres']) ?></td>
        <td><?php echo invertirFecha($row['FechaMatriculacion']) ?></td>
    </tr>
<?php
            }
        }
        else
        {
        ?>
    <tr>
        <td class="mensajeWARNING">No hay colegiados.</td>
    </tr>
        <?php
        }
    }
    
?>
</table>