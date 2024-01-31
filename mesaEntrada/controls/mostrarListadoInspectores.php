<?php
require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
<script type="text/javascript">
$(function(){
    $(".generarInspector").click(function(){
        $.ajax({
            url: "generarInspector.php?lH=LIN",
            success: function(msg) {
                $("#modalGenerarInspector").html(msg);
            }
        });
        $( "#modalGenerarInspector" ).dialog({
            closeText: "cerrar",
            modal: true,
            minWidth:600,
            minHeight: 240,
            width:700,
            maxHeight: 700,
            maxWidth:400,
            resizable: true,
            title: "Alta de Inspector"
        });
    });
    
    $(".borrar").click(function(){
        if(confirm("¿Está seguro que desea dar de baja a este inspector?"))
        {
            var dataPost = $(this).attr("id");
            $.post("borrarInspector.php", { idInspector: dataPost }, function(data){
                alert(data);
                location.reload();
            });    
        }
    });
});
</script>
    <?php
    if(isset($_GET['st']))
    {
        $inspectores = obtenerInspectoresPorEstado($_GET['st']);
    }
    else
    {
        $inspectores = obtenerInspectores();
    }
    
    if(!$inspectores)
    {
        ?>
        <br>
        <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
        <br>
        <?php
    }
    else
    {
        if($inspectores -> num_rows == 0)
        {
            ?>
            <br>
            <p class="mensajeWARNING">No se encontraron inspectores.<br>
                  Para agregar uno nuevo oprima el botón Generar Nuevo Inspector.</p>
            <br>
            <?php
        }
        else
        {
             ?>

        <table id="tablaHabilitaciones" class='tablaCentrada'>
            <tr>
                <td><h4>Matrícula</h4></td>
                <td><h4>Apellido y Nombre</h4></td>
                <?php
                        if(isset($_GET['st']))
                        {
                            if($_GET['st'] == "A")
                            {
                                ?>
                    <td><h4>Borrar</h4></td>
                                <?php
                            }
                        }
                ?>
            <tr>
                <?php
                while ($row = $inspectores -> fetch_assoc())
                {
                    ?>
                <tr>
                    <td><p><?php echo $row['Matricula'] ?></p></td>
                    <td><p><?php echo utf8_encode($row['Apellido'])." ".utf8_encode($row['Nombres']) ?></p></td>
                    <?php
                        if(isset($_GET['st']))
                        {
                            if($_GET['st'] == "A")
                            {
                                ?>
                    <td><a class="borrar" id='<?php echo $row['IdInspector'] ?>'>Borrar</a></td>
                                <?php
                            }
                        }
                    ?>
                </tr>
                    <?php

                }
                ?>
        </table>
    
    

<?php
        }
    }
?>
<br><br>
    <a class="generarInspector" style="color: dodgerblue">Generar Nuevo Inspector</a>
</div>
    <div id="modalGenerarInspector" style="display: none"></div>