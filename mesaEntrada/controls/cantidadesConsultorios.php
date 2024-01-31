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
    $tiposConsultorios = array("U","P","I");
    
    foreach ($tiposConsultorios as $tc)
    {
        $consultaConsultorio = obtenerConsultoriosPorFechasPorTipo($fechaDesde, $fechaHasta, $tc);
        array_push($aConsultas, $consultaConsultorio);
    }
    
?>

<table id="tablaEstadisticas">
    <tr>
        <td class="izquierda"><h4>Cantidad de Consultorios Únicos</h4></td>
        <td><?php echo $aConsultas[0] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Policonsultorios</h4></td>
        <td><?php echo $aConsultas[1] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Instituciones</h4></td>
        <td><?php echo $aConsultas[2] -> num_rows ?></td>
    </tr>
</table>