<script type="text/javascript">
$(function() {
    $( "#inspector" ).autocomplete({ minLength: 3 });
    $( "#inspector" ).autocomplete({
        source: "buscarAutorizados.php?cWay=Ins",
        select: function(event, ui){
            $("#ocultoInspector").load("colegiadoAutorizado.php?matricula="+ui.item.value);
            $(".confirmacion").show();
        }
    });
    
    $(".confirmSi").click(function(){
        $(".confirmarForm").show();
    });
    $(".confirmNo").click(function(){
        $("#ocultoInspector").text("");
        $("#inspector").val("");
        $(".confirmacion").hide();
        $(".confirmarForm").hide();
    });
    $(".cancelar").click(function(){
        $("#modalGenerarInspector").dialog("close");
    });
    
    $('#formAltaInspector').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        $.ajax({
            type: 'POST',
            url: post_url,
            data: post_data,
            success: function(msg) {
                if($.trim(msg) != "-1")
                {
                    alert("El inspector se dio de alta correctamente.");
                    $("#modalGenerarInspector").dialog("close");
                    <?php
                        if($_GET['lH']=="LIN")
                        {
                            ?>
                            window.open("listaInspectoresHabilitantes.php","_self");
                            <?php
                        }
                        else
                        {
                            ?>
                            $("#page-wrap").load("listadoHabilitaciones.php?lH=<?php echo $_GET['lH'] ?>&idIns="+$.trim(msg));
                            <?php
                        }
                        ?>
                }
                else
                {
                    alert("El inspector no pudo ser dado de alta. Intente nuevamente.");
                }
           }
        });
    });
  });
</script>
<p>Para dar de alta al colegiado, deberá cargar la matrícula, esperar a que se desplace el listado del 
   autocompletar y seleccionar con un click la que corresponda.<br/>
   De otra forma no podrá proceder con el alta.</p>
<hr>
<form id="formAltaInspector" action="agregarInspector.php" method="post">
    <table id='tablaBuscarInspector'>
        <tr>
            <td><b>Matrícula del Inspector:</b></td>
            <td><input id="inspector" class="inspector" name="matricula" type="text" placeholder="Ingrese una matrícula" required/></td>
            <td id="ocultoInspector"></td>
        </tr>
    </table>
    <div class="confirmacion" style="display: none">
        <span>¿Confirma la matrícula?</span>
        <span><a class="confirmSi">Sí</a></span>
        <span><a class="confirmNo">No</a></span>
        <br><br>
    </div>
    <div class="volver">
        <input class="cancelar" type="button" value="Cancelar" />
    </div>
    <div class="confirmarForm" style="display: none">
        <input type="submit" value="Confirmar" />
    </div>
</form>