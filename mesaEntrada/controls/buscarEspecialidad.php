<script type="text/javascript">

    $(function(){
                        $('#buscarEspecialidad').submit(function(e){
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
                                title: "Búsqueda de Especialidad (para seleccionar debe hacer doble click)"
                            });
                        });
                    });
                    
</script>

<div id="filtros">
    <?php
        if (isset($aColegiado['Matricula']))
        {
            $mColegiado = "?mC=".$aColegiado['Matricula'];//."&tE=".$_POST['tipoEspecialidad'];
        }
        else
        {
            $mColegiado = "";
        }
        /*
        switch ($_POST['tipoEspecialidad'])
        {
            case "E": $bLegend = "Especialidad";
                break;
            case "A": $bLegend = "Calificación Agregada";
                break;
            case "X": $bLegend = "Especialidad";
                break;
            case "O": $bLegend = "Especialidad";
                break;
        }
         * 
         */
    ?>
    <fieldset>
        <legend>Búsqueda de Especialidad</legend>
    <form id="buscarEspecialidad" action="listadoEspecialidades.php<?php echo $mColegiado ?>" method="post">
        <fieldset>
            <legend>Buscar por Nombre</legend>
            <table>
                <tr>
                    <td>Nombre:</td>
                    <td><input name="nombre" type="text" placeholder="Ingrese el Nombre de la Especialidad" style="width: 250px"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Buscar" /></td>
                </tr>
            </table>
        </fieldset>
    </form>
    </fieldset>
    <div id="modal" style="display:none"></div>
</div>
