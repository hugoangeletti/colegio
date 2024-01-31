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
    
    $aConsultas = array();
    
    $aConsultas[0] = obtenerNotasPorFechasColegiados($fechaDesde, $fechaHasta);
    $aConsultas[1] = obtenerNotasPorFechasRemitentes($fechaDesde, $fechaHasta);
    
?>

<table id="tablaEstadisticas">
    <tr>
        <td class="izquierda"><h4>Cantidad de Notas de Colegiados</h4></td>
        <td><?php echo $aConsultas[0] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Notas de Remitentes</h4></td>
        <td><?php echo $aConsultas[1] -> num_rows ?></td>
    </tr>
</table>