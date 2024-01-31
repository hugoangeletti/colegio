<?php
require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['idConsultorio']) && ($_GET['idConsultorio'] != "")) {
    $idConsultorio = $_GET['idConsultorio'];
    if (isset($_GET['page']) && ($_GET['page'] != "")) {
        $page = $_GET['page'];
    } else {
        $page = 1;
    }

    if (isset($_GET['tipo']) && ($_GET['tipo'] != "")) {
        $tipoConsultorio = $_GET['tipo'];
    } else {
        $tipoConsultorio = "T";
    }
} else {
    $idConsultorio = -1;
}

$consultaConsultorio = obtenerConsultorioPorId($idConsultorio);
$datoConsultorio = $consultaConsultorio->fetch_assoc();
?>
<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';
    ?>
    <div id="page-wrap" style="height: 680px">
        <script type="text/javascript">
            $(function() {
                $('#modificarConsultorio').submit(function(e) {
                    e.preventDefault();
                    var form = $(this);
                    var post_url = form.attr('action');
                    var post_data = form.serialize();
                    $.ajax({
                        type: 'POST',
                        url: post_url,
                        data: post_data,
                        dataType: "json",
                        success: function(result) {
                            alert(result.texto);
                            if (result.estado == 1)
                            {
                                window.location.replace("vistaListadoConsultorios.php?tipo=<?php echo $tipoConsultorio ?>&page=<?php echo $page ?>");
                            }
                        }
                    });
                });

                $("#partido").change(function() {
                    $("#partido option:selected").each(function() {
                        var elegido = $(this).val();
                        $.post("localidades.php", {idZona: elegido}, function(data) {
                            $("#localidad").html(data);
                            $("#CP").val($("#localidad option:selected").attr("data"));
                        });
                    });
                });
                $("#localidad").change(function() {
                    $("#CP").val($("#localidad option:selected").attr("data"));
                });
            });
        </script>
        <br>
        <div id="titulo">
            <h3>Modificación de Consultorio</h3>
        </div>
        <br>
        <form id="modificarConsultorio" action="borrarModificarConsultorio.php" method="post">
            <fieldset>
                <legend></legend>
                <table class="tablaRemitente">
                    <tr>
                        <td><b>Tipo de Consultorio:</b></td>
                        <td><select name="tipoConsultorio" required>
                                <option value="">Seleccione un Tipo</option>
                                <option value="I" <?php
                                if ($datoConsultorio['TipoConsultorio'] == "I") {
                                    echo "selected";
                                }
                                ?>>Institución</option>
                                <option value="P" <?php
                                if ($datoConsultorio['TipoConsultorio'] == "P") {
                                    echo "selected";
                                }
                                ?>>Policonsultorio</option>
                                <option value="U" <?php
                                if ($datoConsultorio['TipoConsultorio'] == "U") {
                                    echo "selected";
                                }
                                ?>>Único</option>
                            </select>
                        </td>
                        <td><b>Nombre del Consultorio:</b></td>
                        <td><input name="nombreConsultorio" type="text" placeholder="Ingrese el Nombre del Consultorio" style="width: 240px" value="<?php echo $datoConsultorio['Nombre'] ?>"/></td>
                        <td><b>Cantidad de Consultorios:</b></td>
                        <td><input name="cantConsultorios" type="text" required placeholder="Ingrese una Cantidad" value="<?php echo $datoConsultorio['CantidadConsultorios'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><b>Calle:</b></td>
                        <td><input name="calle" type="text" required placeholder="Ingrese la Calle" value="<?php echo $datoConsultorio['Calle'] ?>"/></td>
                        <td><b>Lateral:</b></td>
                        <td><input name="lateral" type="text" placeholder="Ingrese el Lateral" value="<?php echo $datoConsultorio['Lateral'] ?>"/></td>
                        <td><b>Nº:</b></td>
                        <td><input name="numero" type="text" required placeholder="Ingrese el Número" value="<?php echo $datoConsultorio['Numero'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><b>Piso:</b></td>
                        <td><input name="piso" type="text" placeholder="Ingrese el Piso" value="<?php echo $datoConsultorio['Piso'] ?>"/></td>
                        <td><b>Departamento:</b></td>
                        <td><input name="departamento" type="text" placeholder="Ingrese el Departamento" style="width: 180px" value="<?php echo $datoConsultorio['Departamento'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><b>Teléfono:</b></td>
                        <td><input name="tel" type="text" required placeholder="Ingrese un Teléfono" value="<?php echo $datoConsultorio['Telefono'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><b>Partido:</b></td>
                        <td><select id="partido" required>
                                <option value="">Seleccione un Partido</option>
                                <?php
                                $partidos = obtenerPartidos();
                                if ($partidos) {
                                    if ($partidos->num_rows != 0) {
                                        while ($row = $partidos->fetch_assoc()) {
                                            ?>
                                            <option value="<?php echo $row['Id']; ?>" 
                                            <?php
                                            if ($row['Id'] == $datoConsultorio['idZona']) {
                                                echo "selected";
                                            }
                                            ?>>
                                            <?php echo utf8_encode($row['Nombre']); ?>
                                            </option>
                                            <?php
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
                                <?php
                                $localidades = obtenerLocalidadesPorIdZona($datoConsultorio['idZona']);
                                if ($localidades) {
                                    if ($localidades->num_rows != 0) {
                                        while ($row = $localidades->fetch_assoc()) {
                                            ?>
                                            <option value="<?php echo $row['Id']; ?>" 
                                            <?php
                                            if ($row['Id'] == $datoConsultorio['IdLocalidad']) {
                                                echo "selected";
                                            }
                                            ?>>
                                            <?php echo utf8_encode($row['Nombre']); ?>
                                            </option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td><b>Código Postal:</b></td>
                        <td><input id="CP" name="CP" type="text" required placeholder="Ingrese Código Postal" value="<?php echo $datoConsultorio['CodigoPostal'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><b>Días y Horarios:</b></td>
                        <td colspan="6"><textarea cols="60" rows="6" name="observaciones"><?php echo $datoConsultorio['Observaciones'] ?></textarea></td>
                    </tr>
                    <input type="hidden" name="tipoAccion" value="M"/>
                    <input type="hidden" name="idConsultorio" value="<?php echo $idConsultorio ?>" />
                    <tr>
                        <td><input type="button" onclick="location = 'vistaListadoConsultorios.php?tipo=<?php echo $tipoConsultorio ?>'" value="Cancelar" /></td>
                        <td><input type="submit" value="Confirmar" /></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    <?php
    include_once '../html/pie.html';
    ?>
</body>
</html>