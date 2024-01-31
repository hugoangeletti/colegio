<?php
    require_once 'seguridad.php';

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $consultaCantidadSolicitudes = obtenerHabilitacionesSolicitadasPorIdConsultorio($_GET['idConsultorio']);
    $consultaCantidadAsignadas = obtenerHabilitacionesAsignadasPorIdConsultorio($_GET['idConsultorio']);
    $consultaCantidadConfirmaciones = obtenerHabilitacionesConfirmadasPorIdConsultorio($_GET['idConsultorio']);
    
    $cantidadSolicitudes = $consultaCantidadSolicitudes -> num_rows;
    $cantidadAsignadas = $consultaCantidadAsignadas -> num_rows;
    $cantidadConfirmaciones = $consultaCantidadConfirmaciones -> num_rows;
    
    $cantidadTotal = $cantidadSolicitudes + $cantidadAsignadas + $cantidadConfirmaciones;
?>
<script type="text/javascript">
    $(function(){
        $(".verDetalle").click(function(){
            var href = $(this).attr("id");
            $.ajax({
                url: href,
                success: function(msg) {
                    $("#modalVerDetalle").html(msg);
                }
            });
            $( "#modalVerDetalle" ).dialog({
                closeText: "cerrar",
                modal: true,
                minWidth:680,
                minHeight: 100,
                width:880,
                maxHeight: 450,
                maxWidth:1000,
                resizable: true,
                title: "Ver Detalle"
            });
        });
    });
        
</script>

<table id="tablaEstadisticas">
    <tr>
        <td class="izquierda"><h4>Cantidad de Solicitudes</h4></td>
        <td><?php echo $cantidadTotal ?></td>
        <td><a class="verDetalle" id="mostrarHabilitacionesParaBusqueda.php?lH=Y&idConsultorio=<?php echo $_GET['idConsultorio'] ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Pendientes a Asignar</h4></td>
        <td><?php echo $cantidadSolicitudes ?></td>
        <td><a class="verDetalle" id="mostrarHabilitacionesParaBusqueda.php?lH=S&idConsultorio=<?php echo $_GET['idConsultorio'] ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Asignadas</h4></td>
        <td><?php echo $cantidadAsignadas ?></td>
        <td><a class="verDetalle" id="mostrarHabilitacionesParaBusqueda.php?lH=A&idConsultorio=<?php echo $_GET['idConsultorio'] ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Confirmaciones</h4></td>
        <td><?php echo $cantidadConfirmaciones ?></td>
        <td><a class="verDetalle" id="mostrarHabilitacionesParaBusqueda.php?lH=C&idConsultorio=<?php echo $_GET['idConsultorio'] ?>">VerDetalle</a></td>
    </tr>
</table>
<div id="modalVerDetalle" style="display: none"></div>