<?php
    if(isset($_GET["Bus"]))
    {
        $bus = true;
    }
    else
    {
        $bus = false;
    }
?>
<script type="text/javascript">
    $(function(){
    <?php
        if(!$bus)
        {
    ?>
    $(".generarConsultorio").click(function(){
        $.ajax({
            url: "formConsultorio.php?colegiado=<?php echo $matricula?>",
            success: function(msg) {
                $("#modalGenerarConsultorio").html(msg);
            }
        });
        $( "#modalGenerarConsultorio" ).dialog({
            closeText: "cerrar",
            modal: true,
            minWidth:980,
            minHeight: 540,
            width:980,
            maxHeight: 550,
            maxWidth:1000,
            resizable: true,
            title: "Alta de Consultorio"
        });
    });
    <?php
        }
    ?>
    $("#tipoConsultorio").change(function(){
        var tC = $("#tipoConsultorio option:selected").val();
        $("#buscarConsultorio").attr("action","listadoConsultorios.php?mC=<?php if(!$bus){echo $aColegiado['Matricula'];}else{echo "bus";} ?>&tc="+tC);
    });
    
    $('#buscarConsultorio').submit(function(e){
        e.preventDefault();
        $("#modalBuscarConsultorio").show();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        $.ajax({
            type: 'POST',
            url: post_url,
            data: post_data,
            success: function(msg) {
                $("#modalBuscarConsultorio").html(msg);
            }
        });

        $("#modalBuscarConsultorio").dialog({
            closeText: "cerrar",
            modal: true,
            width:900,
            maxHeight: 400,
            maxWidth:1000,
            resizable: true,
            title: "Búsqueda de Consultorio (para seleccionar debe hacer doble click)"
        });
    });
});
</script>

<fieldset>
    <legend>Buscar Consultorio</legend>
    <form id="buscarConsultorio" action="listadoConsultorios.php?mC=<?php if(!$bus){echo $aColegiado['Matricula'];}else{echo "bus";} ?>&tc=" method="post">
        <table class="tablaConsultorio">
        <tr>
            <td>Tipo de Consultorio:</td>
            <td>
                <select id="tipoConsultorio" name="tipoConsultorio" required>
                    <option value="">Seleccione un Tipo de Consultorio</option>
                    <option value="I">Institución</option>
                    <option value="P">Policonsultorio</option>
                    <option value="U">Único</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Consultorio:</td>
            <td><input name="consultorio" type="text" placeholder="Ingrese una calle"/></td>
            <td><td><input type="submit" value="Buscar"/></td></td>

    <!-- 
        El botón EXAMINAR debería mostrar un popup
        con todos los colegiados para la posible selección
    -->
        </tr>
        <?php
            if(!$bus)
            {
        ?>
        <tr>
            <td></td>
            <td><a class="generarConsultorio">Generar Consultorio</a></td>
        </tr>
        <?php
            }
        ?>
    </table>
    </form>
</fieldset>
<div id="modalBuscarConsultorio" style="display: none"></div>
<div id="modalGenerarConsultorio" style="display: none"></div>