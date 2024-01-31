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
                    $listadoHabilitaciones = obtenerHabilitacionesSolicitadas();
                break;
            case "S":
                    $listadoHabilitaciones = obtenerHabilitacionesSolicitadas();
                break;
            case "A":
                    $listadoHabilitaciones = obtenerHabilitacionesAsignadas($_GET['idIns']);
                break;
            case "C":
                    $listadoHabilitaciones = obtenerHabilitacionesConfirmadas($_GET['idIns']);
                break;
        }
    }
    
?>
<script type="text/javascript">
$(function(){
    $(".borrar").click(function(){
        if(confirm("¿Está seguro que desea dar de baja esta inspección asignada?"))
        {
            var dataPost = $(this).attr("id");
            $.post("borrarModificarInspeccion.php", { idInspectorHabilitacion: dataPost, tipoAccion: "B" }, function(data){
                alert(data);
                $("#page-wrap").load("listadoHabilitaciones.php?lH=<?php echo $_GET['lH'] ?>&idIns=<?php if(isset($_GET['idIns']))echo $_GET['idIns'] ?>");
            });    
        }
    });
    
    $(".editarHabilitacionAsignada").click(function(){
    var idIH = $(this).attr("id");
        $.ajax({
            url: "formEditarHabilitacionAsignada.php?lH=<?php echo $_GET['lH'] ?>&idIns=<?php if(isset($_GET['idIns']))echo $_GET['idIns'] ?>&idIH="+idIH,
            success: function(msg) {
                $("#modalEditarHabilitacionAsignada").html(msg);
            }
        });
        $( "#modalEditarHabilitacionAsignada" ).dialog({
            closeText: "cerrar",
            modal: true,
            minWidth:680,
            minHeight: 250,
            width:880,
            maxHeight: 200,
            maxWidth:1000,
            resizable: true,
            title: "Confirmar Habilitación Asignada"
        });
    });
    
    $(".editarHabilitacionConfirmada").click(function(){
    var idIH = $(this).attr("id");
        $.ajax({
            url: "formEditarHabilitacionConfirmada.php?lH=<?php echo $_GET['lH'] ?>&idIns=<?php if(isset($_GET['idIns']))echo $_GET['idIns'] ?>&idIH="+idIH,
            success: function(msg) {
                $("#modalEditarHabilitacionConfirmada").html(msg);
            }
        });
        $( "#modalEditarHabilitacionConfirmada" ).dialog({
            closeText: "cerrar",
            modal: true,
            minWidth:680,
            minHeight: 250,
            width:880,
            maxHeight: 200,
            maxWidth:1000,
            resizable: true,
            title: "Editar Habilitación Confirmada"
        });
    });
    
    $('#formTablaHabilitaciones').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        $.ajax({
            type: 'POST',
            url: post_url,
            data: post_data,
            dataType: "json",
            success: function(msg) {
                if($.trim(msg.msg) == "Las inspecciones se dieron de alta correctamente.")
                {
                    alert(msg.msg);
                    <?php
                        if($_GET['lH'] == "S")
                        {
                            ?>
                    window.open('imprimirInspeccionSolicitada.php?idMS='+msg.array,'_blank');
                            <?php
                        }
                    ?>
                    
                
                    location.reload();
                }
                else
                {
                    alert(msg.msg);
                }
                if(($.trim(msg.msg) == "La modificación se realizó correctamente.")||($.trim(msg.msg) == "La habilitación se dio de baja correctamente."))
                    {
                        alert(msg.msg);
                        location.reload();
                    }
           }
        });
    });
    
    $(".imprimir").click(function(){
        var id = $(this).attr("id");
        $.ajax({
            success: function(){
                window.open('imprimirInspeccionSolicitada.php?iIH='+id,'_blank');
            }
        });
        
    });
});
</script>
<form id='formTablaHabilitaciones' action="agregarInspecciones.php?lH=<?php echo $_GET['lH'] ?>" method="post">
<table id="tablaHabilitaciones">
        <tr>
            <td><h4>Dirección</h4></td>
            <td><h4>Localidad</h4></td>
            <td><h4>Teléfono</h4></td>
            <td><h4>Matrícula</h4></td>
            <td><h4>Apellido y Nombre</h4></td>
            <td><h4>Mail</h4></td>
            <?php
            if(isset($_GET['lH']))
            {
                switch ($_GET['lH'])
                {
                    case "S":
                            $colspan = 7;
                        ?>
            <td><h4>Check</h4></td>
                        <?php
                        break;
                    case "A":
                            $colspan = 9;
                        ?>
            <td><h4>Editar</h4></td>
            <td><h4>Borrar</h4></td>
            <td><h4>Imprimir</h4></td>
                        <?php
                        break;
                    case "C":
                            $colspan = 7;
                        ?>
            <td><h4>Habilitado</h4></td>
            <td><h4>Editar</h4></td>
                        <?php
                        break;
                    default :
                            $colspan = 6;
                        break;
                }
            }
            ?>
        </tr>
            <?php
            if(!$listadoHabilitaciones)
            {
                ?>
        <tr>
            <td colspan="<?php echo $colspan; ?>"><span class="mensajeERROR">Hubo un error en la base de datos.</span></td>
        </tr>
                <?php
            }
            else
            {
                if($listadoHabilitaciones -> num_rows == 0)
                {
                    ?>
        <tr>
            <td colspan="<?php echo $colspan; ?>"><span class="mensajeWARNING">No se encuentran solicitudes de habilitaciones.</span></td>
        </tr>
                    <?php
                }
                else
                {
                    while ($row = $listadoHabilitaciones -> fetch_assoc())
                    {
            ?>
        <tr>
            <td><?php echo $row['Calle']." ".$row['Lateral']." ".$row['Numero']." ".$row['Piso'].$row['Departamento'] ?></td>
            <td><?php echo $row['NombreLocalidad'] ?></td>
            <td><?php echo $row['Telefono'] ?></td>
            <td><?php echo $row['Matricula'] ?></td>
            <td><?php echo utf8_encode($row['Apellido'])." ".utf8_encode($row['Nombres']) ?></td>
            <td><?php echo $row['Email'] ?></td>
            <?php
            if(isset($_GET['lH']))
            {
                switch ($_GET['lH'])
                {
                    case "S":
                        ?>
            <td><input type="checkbox" name="habilitaciones[]" value="<?php if(isset($_GET['lH'])){if(($_GET['lH'] == "S")){echo $row['IdMesaEntrada'];}else{if(($_GET['lH'] == "A")){echo $row['IdInspectorHabilitacion'];}}} ?>" /></td>
                        <?php
                        break;
                    case "A":
                        ?>
            <td><a class="editarHabilitacionAsignada" id='<?php echo $row['IdInspectorHabilitacion'] ?>'>Editar</a></td>
            <td><a class="borrar" id='<?php echo $row['IdInspectorHabilitacion'] ?>'>Borrar</a></td>
            <td><a class="imprimir" id="<?php echo $row['IdInspectorHabilitacion'] ?>">Imprimir</a></td>
                        <?php
                        break;
                    case "C":
                        ?>
            <td><?php if($row['EstadoInspeccion'] == "H"){echo "SI";}else if($row['EstadoInspeccion'] == "N"){echo "NO";} ?></td>
            <td><a class="editarHabilitacionConfirmada" id='<?php echo $row['IdInspectorHabilitacion'] ?>'>Editar</a></td>        
                <?php
                        break;
                }
            }
            ?>
        </tr>
            <?php
                    }
                }
            }
            ?>
</table>
<br /><br />
<input type="hidden" name="idIns" value="<?php if(isset($_GET['idIns'])){echo $_GET['idIns'];} ?>" />
<table class='botonesForm'>
    <tr>
        <?php
            if($_GET['lH'] != "Y")
            {
        ?>
        <td><input type="button" onclick="location=window.location.search;" value="Cancelar" /></td>
        <?php
        if($_GET['lH'] == "S")
        {
            ?>
        <td><input type="submit" value="Confirmar" /></td>
            <?php
        }
            }
        ?>
    </tr>
</table>
</form>
<div id="modalEditarHabilitacionAsignada" style="display: none"></div>
<div id="modalEditarHabilitacionConfirmada" style="display: none"></div>