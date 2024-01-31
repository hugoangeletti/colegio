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
<script type="text/javascript">
    $(function(){
        $(".cancelarConsultorio").click(function(){
            $("#modalGenerarConsultorio").dialog("close");
        });
        
        $('#agregarConsultorio').submit(function(e){
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
                        alert("El consultorio se dio de alta correctamente.");
                        $("#modalGenerarConsultorio").dialog("close");
                        $("#page-wrap").load("filtroHabilitacion.php?idConsultorio="+$.trim(msg)+"&action=A&matricula=<?php echo $_GET['colegiado'] ?>");
                    }
                    else
                    {
                        alert("El consultorio no pudo ser dado de alta. Intente nuevamente.");
                    }
               }
            });
        });
        
        $("#partido").change(function () {
            $("#partido option:selected").each(function () {
                var elegido=$(this).val();
                $.post("localidades.php", { idZona: elegido }, function(data){
                    $("#localidad").html(data);
                    $("#CP").val($("#localidad option:selected").attr("data"));
                });            
            });
        });
        $("#localidad").change(function(){
            $("#CP").val($("#localidad option:selected").attr("data"));
        });
    });
</script>

<form id="agregarConsultorio" action="agregarConsultorio.php" method="post">
    <fieldset>
        <legend>Alta de Consultorio</legend>
        <table class="tablaRemitente">
            <tr>
                <td><b>Tipo de Consultorio:</b></td>
                <td><select name="tipoConsultorio" required>
                        <option value="">Seleccione un Tipo</option>
                        <option value="I">Institución</option>
                        <option value="P">Policonsultorio</option>
                        <option value="U">Único</option>
                    </select>
                </td>
                <td><b>Nombre del Consultorio:</b></td>
                <td><input name="nombreConsultorio" type="text" placeholder="Ingrese el Nombre del Consultorio" style="width: 240px"/></td>
                <td><b>Cantidad de Consultorios:</b></td>
                <td><input name="cantConsultorios" type="text" required placeholder="Ingrese una Cantidad"/></td>
            </tr>
            <tr>
                <td><b>Calle:</b></td>
                <td><input name="calle" type="text" required placeholder="Ingrese la Calle"/></td>
                <td><b>Lateral:</b></td>
                <td><input name="lateral" type="text" placeholder="Ingrese el Lateral"/></td>
                <td><b>Nº:</b></td>
                <td><input name="numero" type="text" required placeholder="Ingrese el Número"/></td>
            </tr>
            <tr>
                <td><b>Piso:</b></td>
                <td><input name="piso" type="text" placeholder="Ingrese el Piso"/></td>
                <td><b>Departamento:</b></td>
                <td><input name="departamento" type="text" placeholder="Ingrese el Departamento" style="width: 180px"/></td>
            </tr>
            <tr>
                <td><b>Teléfono:</b></td>
                <td><input name="tel" type="text" required placeholder="Ingrese un Teléfono"/></td>
            </tr>
            <tr>
                <td><b>Partido:</b></td>
                <td><select id="partido" required>
                        <option value="">Seleccione un Partido</option>
                        <?php
                            $partidos = obtenerPartidos();
                            if($partidos)
                            {
                                if($partidos -> num_rows != 0)
                                {
                                    while($row = $partidos -> fetch_assoc()){
                                        echo "<option value=".$row['Id']." ";
                                        echo ">".utf8_encode($row['Nombre'])."</option>";
                                    }
                                }
                            }
                        ?>
                    </select>
                </td>
                <td><b>Localidad:</b></td>
                <td>
                    <select id="localidad" required name="localidad">
                        <option value="">Seleccione una Localidad</option>
                    </select>
                </td>
                <td><b>Código Postal:</b></td>
                <td><input id="CP" name="CP" type="text" required placeholder="Ingrese Código Postal"/></td>
            </tr>
            <tr>
                <td><b>Días y Horarios:</b></td>
                <td colspan="6"><textarea cols="60" rows="6" name="observaciones"></textarea></td>
            </tr>
            <tr>
                <td><input type="button" class="cancelarConsultorio" value="Cancelar" /></td>
                <td><input type="submit" value="Confirmar" /></td>
            </tr>
        </table>
    </fieldset>
</form>