<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    /*
     * Y -> Habilitaciones Solicitadas
     * S -> Asignar Inspecciones
     * A -> Habilitaciones Asignadas
     * C -> Habilitaciones Confirmadas
     */
    
    if(isset($_GET['lH']))
    {
        switch ($_GET['lH'])
        {
            case "Y":
                    $listadoHabilitaciones = obtenerTodasHabilitacionesSolicitadas($_GET['idConsultorio']);
                break;
            case "S":
                    $listadoHabilitaciones = obtenerHabilitacionesSolicitadasPorIdConsultorio($_GET['idConsultorio']);
                break;
            case "A":
                    $listadoHabilitaciones = obtenerHabilitacionesAsignadasPorIdConsultorio($_GET['idConsultorio']);
                break;
            case "C":
                    $listadoHabilitaciones = obtenerHabilitacionesConfirmadasPorIdConsultorio($_GET['idConsultorio']);
                break;
        }
    }
    
?>
<style>
    .verTramite{
        cursor: pointer;
    }
    .imprimir{
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    $(function(){
        $(".verTramite").click(function(){
            var href = $(this).attr("id");
            $("#modalVerDetalle").dialog("close");
            $("#page-wrap").load(href); 
        });
    });
    $(function(){
        $(".imprimir").click(function(){
            var id = $(this).attr("id");
            $.ajax({
                success: function(){
                    $("#modalVerDetalle").dialog("close");
                    window.open('imprimirHabilitacionConsultorio.php?iME='+id,'_blank');
                }
            });

        });
    });
</script>
<table class='tablaTabs'>
    <tr>
        <td><h4>Nº de Trámite</h4></td>
        <td><h4>Matrícula</h4></td>
        <td><h4>Apellido y Nombre</h4></td>
        <td><h4>Ver</h4></td>
        <td><h4>Imprimir</h4></td>
        <td><h4>Realizó</h4></td>
    </tr>
        <?php
                if($listadoHabilitaciones -> num_rows == 0)
                {
        ?>
    <tr>
        <td colspan="5"><span class="mensajeWARNING">No se encontraron habilitaciones de este tipo para este consultorio.</span></td>
    </tr>
        <?php
                }else
                {
                    while($row = $listadoHabilitaciones -> fetch_assoc()){
            ?>
                        <tr>
                            <td><?php echo $row['IdMesaEntrada'] ?></td>
                            <td><?php echo $row['Matricula'] ?></td>
                            <td><?php echo $row['Apellido']." ".$row['Nombres'] ?></td>
                            <td><a class="verTramite" id="verInfoFormConsultorio.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&consultorio=<?php echo $row['IdConsultorio'] ?>">Ver</a></td>
                            <td><a class="imprimir" id="<?php echo $row['IdMesaEntrada'] ?>" data="<?php echo $row['IdTipoMesaEntrada'] ?>">Imprimir</a></td>
                            <td><?php echo $row['Usuario'] ?></td>
                        </tr>
            <?php
                    }
                }
        ?>
</table>