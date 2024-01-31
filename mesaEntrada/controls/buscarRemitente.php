<script type="text/javascript">

    $(function(){
                        $('#buscarPorNombre').submit(function(e){
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
                            
                            $( "#modal" ).dialog({
                                closeText: "cerrar",
                                modal: true,
                                width:900,
                                maxHeight: 400,
                                maxWidth:1000,
                                resizable: true,
                                title: "Búsqueda de Remitente (para seleccionar debe hacer doble click)"
                            });
                        });
                        
                        
                    });
                    
function volver(){
    $("#page-wrap").load('buscarRemitente.php');
}                    
</script>
<?php
    if(isset($_GET['bus']))
    {
        $action = "listadoRemitentes.php?Bus=ok";
    }
    else
    {
        $action = "listadoRemitentes.php";
    }
?>
<div id="filtros">
    <fieldset>
        <legend>Búsqueda de Remitente</legend>
    <form id="buscarPorNombre" action="<?php echo $action ?>" method="post">
        <fieldset>
            <legend>Buscar por Nombre</legend>
            <table>
                <tr>
                    <td>Nombre:</td>
                    <td><input name="nombre" type="text" placeholder="Ingrese el Nombre del Remitente" style="width: 200px;"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Buscar" /></td>
                </tr>
            </table>
        </fieldset>
    </form>
        <!--<a class="generarRemitente">Generar Nuevo Remitente</a>-->
        <br><br>
                <?php 
                    if(isset($_GET))
                    {
                ?>
                <input type="button" onclick="location='administracion.php'" value="Cancelar" />
                <?php
                    }
                ?>
                <div id="modal" style="display:none"></div>
                <div id="modalGenerarRemitente" style="display:none"></div>
                
</div>
