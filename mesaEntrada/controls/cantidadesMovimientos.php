<?php
    require_once 'seguridad.php';

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $fechaDesde = $_GET['fD'];
    $fechaHasta = $_GET['fH'];
    
    $tiposMovimientos = obtenerTiposMovimientos();
    
?>

<table id="tablaEstadisticas">
<?php
    
    if($tiposMovimientos)
    {
        if($tiposMovimientos -> num_rows != 0)
        {
            while($row = $tiposMovimientos -> fetch_assoc())
            {
                $consultaMovimientos = obtenerMovimientosPorFechasPorTipo($fechaDesde, $fechaHasta, $row['Id']);
                if($consultaMovimientos -> num_rows != 0)
                {
?>
    <tr>
        <td class="izquierda"><h4><?php echo utf8_encode($row['DetalleCompleto']) ?></h4></td>
        <td><?php echo $consultaMovimientos -> num_rows ?></td>
    </tr>
<?php
                }
            }
        }
        else
        {
        ?>
    <tr>
        <td class="mensajeWARNING">No hay movimientos realizados en este período de tiempo.</td>
    </tr>
        <?php
        }
    }
    
?>
</table>