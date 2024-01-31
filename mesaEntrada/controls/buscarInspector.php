<?php
    require_once 'seguridad.php';
?>
<script type="text/javascript">
    
$(function(){
                        $('#buscarInspector').submit(function(e){
                            e.preventDefault();
                            $("#modal").show();
                            var form = $(this);
                            var post_url = form.attr('action');
                            var post_data = form.serialize();
                            $.ajax({
                                type: 'POST',
                                url: post_url,
                                data: post_data,
                                success: function(msg) {
                                    $("#modal").html(msg);
                                }
                            });
                            
                            $("#modal").dialog({
                                closeText: "cerrar",
                                modal: true,
                                width:900,
                                maxHeight: 400,
                                maxWidth:1000,
                                resizable: true,
                                title: "Búsqueda de Inspector (para seleccionar debe hacer doble click)"
                            });
                        });
                    });
                    

$(function(){
    $(".generarInspector").click(function(){
        $.ajax({
            url: "generarInspector.php?lH=<?php echo $_GET['lH'] ?>",
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
});

function volver(){
    $("#page-wrap").load('buscarInspector.php');
}                    

</script>
<?php 

//Recibo por GET un ME que me determina el tipo de Mesa de Entrada
//para especificarle al formulario de búsqueda a qué PHP apuntar.
// 1 -> mesaEntradaFormMovimiento
// 2 -> mesaEntradaFormEspecialidad
// 3 -> mesaEntradaFormNota
// 4 -> mesaEntradaFormConsultorio
// y si no está declarado el GET es porque no ingresa desde colegiado,
// por lo tanto es una Nota y es como si se activara el 3.
if(isset($_GET['lH']))
{
    switch ($_GET['lH'])
    {
        case "S": $action = "listadoHabilitaciones.php?lH=S";
                $titulo = "Solicitadas";
                $leyenda = "Búsqueda de Inspector";
                $label_buscar = "Matrícula del Inspector Asignado:";
            break;
        case "A": $action = "listadoHabilitaciones.php?lH=A";
                $titulo = "Asignadas";
                $leyenda = "Búsqueda de Inspector";
                $label_buscar = "Matrícula del Inspector Asignado:";
            break;
        case "C": $action = "listadoHabilitaciones.php?lH=C";
                $titulo = "Confirmadas";
                $leyenda = "Búsqueda de Inspector";
                $label_buscar = "Matrícula del Inspector Asignado:";
            break;
        case "M": $action = "listadoHabilitaciones.php?lH=M";
                $titulo = "Por Matriculado";
                $leyenda = "Búsqueda Por Matriculado";
                $label_buscar = "Matrícula a buscar:";
            break;
    }
}
?>
<div id="filtros">
    <fieldset>
        <legend><?php echo $leyenda ?></legend>
        <table>
            <tr>
            <td>
            <form id="buscarInspector" action="listadoInspectores.php" method="post">
                <table id='tablaBuscarInspector'>
                    <tr>
                        <td></td>
                        <td><input name="matricula" type="text" placeholder="Ingrese Matrícula"/></td>
                        <input type="hidden" name="lH" value="<?php echo $_GET['lH'] ?>" />
                        <td><input type="submit" value="Buscar" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?php if ($_GET['lH'] != 'M') { ?>
                            <a class="generarInspector">Generar Nuevo Inspector</a>
                        <?php } ?>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </form>
            </td>
        </tr>
        </table>
        <br>
    
                <?php 
                    if(isset($_GET))
                    {
                ?>
                <input type="button" onclick="location='administracion.php'" value="Cancelar" />
                <?php
                    }
                ?>
                <div id="modal" style="display:none"></div>
                <div id="modalGenerarInspector" style="display: none"></div>
    </fieldset>
                
</div>
