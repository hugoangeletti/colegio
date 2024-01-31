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
    $tiposEspecialidades = obtenerTiposEspecialidades();
    
    if($tiposEspecialidades)
    {
        if($tiposEspecialidades -> num_rows != 0)
        {
            while($row = $tiposEspecialidades -> fetch_assoc())
            {
                $consultaEspecialidad = obtenerEspecialidadesPorFechasPorTipo($fechaDesde, $fechaHasta, $row['Codigo']);
                array_push($aConsultas, $consultaEspecialidad);
            }
        }
    }
    
?>

<table id="tablaEstadisticas">
    <tr>
        <td class="izquierda"><h4>Cantidad de Nuevas Especialidades</h4></td>
        <td><?php echo $aConsultas[0] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Especialistas Exceptuados Art.8</h4></td>
        <td><?php echo $aConsultas[1] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Especialistas Jerarquizados</h4></td>
        <td><?php echo $aConsultas[2] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Especialista Consultor</h4></td>
        <td><?php echo $aConsultas[3] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Calificación Agregada</h4></td>
        <td><?php echo $aConsultas[4] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Recertificaciones</h4></td>
        <td><?php echo $aConsultas[5] -> num_rows ?></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Especialista de Otro Distrito</h4></td>
        <td><?php echo $aConsultas[6] -> num_rows ?></td>
    </tr>
</table>