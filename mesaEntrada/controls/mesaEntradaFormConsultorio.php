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
 * El formulario recibe un action el cual establecerá la acción dentro del
 * form, dependiendo Alta - Baja o Modificación.
 * 
 * En todos los casos se establece el título que se mostrará al usuario.
 * En el caso de Baja y Vista, los campos del formularios se verán 
 * deshabilitados, sin darle opción al usuario que los modifique.
 * 
 * Se establecen ciertas variables que determinan el pasaje de valores
 * necesarios para visualizar el requerimiento.
 */

if (isset($_GET['action'])) {
    $stHidden = false;
    $readOnly = false;
    $numeroTramite = false;
    if (isset($_GET['idTP'])) {
        $idTipoPago = $_GET['idTP'];
    }
    switch ($_GET['action']) {
        case 'A': $actionForm = "agregarHabilitacionConsultorio.php";
            $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarHabilitacionConsultorio.php";
            $readOnly = true;
            $titulo = "Baja de";
            $stHidden = true;
            $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarHabilitacionConsultorio.php";
            $titulo = "Modificación de";
            $stHidden = true;
            $numeroTramite = true;
            break;
    }
    $idZona = "";

    /*
     * iEvento es el idMesaEntrada que se obtiene desde el Listado por Fecha de
     * Mesa de Entrada, con el cual se obtendrán todos los valores de la BD
     * correspondientes a las Habilitaciones de Consultorio.
     */

    /*
     * Todas las funciones que se encuentran en este php devuelven un objeto
     * query, el cual deberá controlarse a partir de las sentencias explícitas
     * de abajo, como por ejemplo $consultaDatosHabilitacion.
     */

    if (isset($_GET['iEvento'])) {
        $hayHabilitacionConsultorio = true;
        $consultaDatosHabilitacion = obtenerHabilitacionConsultorioPorId($_GET['iEvento']);
        if (!$consultaDatosHabilitacion) {
            $datosHabilitacion = null;
        } else {
            if ($consultaDatosHabilitacion->num_rows == 0) {
                $datosHabilitacion = null;
            } else {
                $datosHabilitacion = $consultaDatosHabilitacion->fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosHabilitacion['Matricula'];
        $consultaLocalidad = obtenerPartidoPorIdLocalidad($datosHabilitacion['IdLocalidad']);
        $localidad = $consultaLocalidad->fetch_assoc();
        $idZona = $localidad['IdZona'];
    } else {
        $hayHabilitacionConsultorio = false;
    }
    ?>
    <script>
        $(document).ready(function() {
            $(".tituloWrap").hide();
        });
    </script>
    <div id="titulo">
        <h3>Mesa de Entrada</h3>
        <h4><?php echo $titulo ?> Solicitud de Habilitación de Consultorio</h4>
        <?php
        if ($numeroTramite) {
            ?>
            <h4>Trámite Nº <?php echo $datosHabilitacion['IdMesaEntrada'] ?></h4>
            <?php
        }
        ?>
    </div>

    <fieldset>
        <?php
        include 'mostrarColegiado.php';

        /*
         * mostrarColegiado muestra la parte estática del colegiado
         * y devuelve una variable $error que determina si hubo algún error
         * al realizar la consulta, lo cual no debería mostrar nada
         * sólo el botón volver que se encuentra en el else.
         */

        if (!$error) {

            $verificacion = verificarPermisoColegiadoHabilitacionConsultorio($estadoMatricular['Estado'], 1, $estadoTesoreria);

            $permiso = false;
            switch ($verificacion) {
                case -1:
                    ?>
                    <br>
                    <span class="mensajeERROR">Hubo Error en la Verificación</span>
                    <br>
                    <?php
                    break;
                case 0:
                    ?>
                    <br>
                    <span class="mensajeERROR">No tiene permiso para realizar este evento.</span>
                    <br>
                    <?php
                    break;
                default :
                    $permiso = true;
                    break;
            }

            if ($permiso) {
                ?>
                <script type="text/javascript">
                    $(function() {

                        $('#formHabilitacionConsultorio').submit(function(e) {
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
                                    if ($.trim(msg.texto) == "La habilitación se dio de alta correctamente.")
                                    {
                                        alert("El importe de la habilitación de consultorio realizada es: $" + msg.importe);
                                        window.open('imprimirHabilitacionConsultorio.php', '_blank');

                                        location.reload();
                                    }
                                    else
                                    {
                                        alert(msg.texto);
                                    }
                                    if (($.trim(msg.texto) == "La modificación se realizó correctamente.") || ($.trim(msg.texto) == "La habilitación se dio de baja correctamente."))
                                    {
                                        location.reload();
                                    }
                                }
                            });
                        });
                    });

                </script>
                <style>
                    .ui-autocomplete {
                        max-height: 100px;
                        overflow-y: auto;
                        /* prevent horizontal scrollbar */
                        overflow-x: hidden;
                    }
                    /* IE 6 doesn't support max-height
                     * we use height instead, but this forces the menu to always be this tall
                     */
                    * html .ui-autocomplete {
                        height: 100px;
                    }
                </style>
                <script>
                    $(function() {
                        $(".autorizado").autocomplete({minLength: 3});
                        $(".autorizado").autocomplete({
                            source: "buscarAutorizados.php?cWay=Aut"
                        });
                        $("#autorizado1").autocomplete({
                            select: function(event, ui) {
                                console.log($.post("comprobarAutorizados.php", {matricula: ui.item.value}, function(result) {
                                    if (!result.estado)
                                    {
                                        $("#autorizado1").val("");
                                        alert(result.texto);
                                    }
                                    else
                                    {
                                        $("#ocultoAutorizado1").load("colegiadoAutorizado.php?matricula=" + ui.item.value);
                                        $("#autorizado2").attr("disabled", false);
                                    }
                                }, "json"));
                            }
                        });
                        $("#autorizado2").autocomplete({
                            select: function(event, ui) {
                                console.log($.post("comprobarAutorizados.php", {matricula: ui.item.value}, function(result) {
                                    if (!result.estado)
                                    {
                                        $("#autorizado2").val("");
                                        alert(result.texto);
                                    }
                                    else
                                    {
                                        $("#ocultoAutorizado2").load("colegiadoAutorizado.php?matricula=" + ui.item.value);
                                        $("#autorizado3").attr("disabled", false);
                                    }
                                }, "json"));
                            }
                        });
                        $("#autorizado3").autocomplete({
                            select: function(event, ui) {
                                console.log($.post("comprobarAutorizados.php", {matricula: ui.item.value}, function(result) {
                                    if (!result.estado)
                                    {
                                        $("#autorizado3").val("");
                                        alert(result.texto);
                                    }
                                    else
                                    {
                                        $("#ocultoAutorizado3").load("colegiadoAutorizado.php?matricula=" + ui.item.value);
                                    }
                                }, "json"));
                            }
                        });
                    });


                    $(function() {
                        $(".especialidadUno").change(function() {
                            $(".especialidadUno option:selected").each(function() {
                                especialidad = $(this).val();
                                if (especialidad != "")
                                {
                                    $(".especialidadAlternativa").attr("disabled", false);
                                }
                                else
                                {
                                    $(".especialidadAlternativa").attr("disabled", true);
                                }
                            });
                        });
                    });
                </script>
                <?php
                if (!isset($_GET['idConsultorio'])) {
                    ?>
                    <div class="divGenerarConsultorio">
                        <br>
                        <?php
                        require_once 'buscarConsultorio.php';
                        ?>
                        <br/><input type="button" onclick="location = window.location.search;" value="Volver" />
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="divFormularioConsultorio">
                        <form id="formHabilitacionConsultorio" action="<?php echo $actionForm ?>" method="post">
                            <br>
                            <?php
                            require_once 'mostrarConsultorio.php';
                            ?>
                            <br>
                            <table class="tablaConsultorio">
                                <tr>
                                    <td><b>Especialidad:</b></td>
                                    <td colspan='5'>
                                        <select name="especialidad[]" required class="especialidadUno">
                                            <option value="">Seleccione una Especialidad</option>
                                            <?php
                                            $especialidades = obtenerEspecialidades();
                                            if ($especialidades) {
                                                if ($especialidades->num_rows != 0) {
                                                    while ($row = $especialidades->fetch_assoc()) {
                                                        echo "<option value=" . $row['Id'] . " ";
                                                        echo ">" . utf8_encode($row['Especialidad']) . "</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Especialidad Alternativa:</b></td>
                                    <td colspan='5'>
                                        <select name="especialidad[]" disabled class="especialidadAlternativa">
                                            <option value="">Seleccione una Especialidad</option>
                                            <?php
                                            $especialidades = obtenerEspecialidades();
                                            if ($especialidades) {
                                                if ($especialidades->num_rows != 0) {
                                                    while ($row = $especialidades->fetch_assoc()) {
                                                        echo "<option value=" . $row['Id'] . " ";
                                                        echo ">" . utf8_encode($row['Especialidad']) . "</option>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                if ($datosConsultorio['TipoConsultorio'] == "U") {
                                    ?>
                                    <tr>
                                        <td colspan="5">(Para buscar al colegiado autorizado, deberá cargar al menos los primero 3 dígitos de su matrícula)</td>
                                    </tr>
                                    <tr>
                                        <td><b>Matrícula del Colegiado Autorizado 1:</b></td>
                                        <td><input id="autorizado1" class="autorizado" type="text" name="autorizados[]" placeholder="Ingrese una matrícula"/></td>
                                        <td id="ocultoAutorizado1"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Matrícula del Colegiado Autorizado 2:</b></td>
                                        <td><input id="autorizado2" class="autorizado" type="text" name="autorizados[]" disabled placeholder="Ingrese una matrícula"/></td>
                                        <td id="ocultoAutorizado2"></td>
                                    </tr>
                                    <tr>
                                        <td><b>Matrícula del Colegiado Autorizado 3:</b></td>
                                        <td><input id="autorizado3" class="autorizado" type="text" name="autorizados[]" disabled placeholder="Ingrese una matrícula"/></td>
                                        <td id="ocultoAutorizado3"></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <br>
                            <?php
                            require_once 'mostrarDatosPersonalesColegiado.php';
                            ?>
                            <br>
                            <input type="hidden" name="idConsultorio" value="<?php echo $_GET['idConsultorio'] ?>"/>
                            <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
                            <input type="hidden" name="idTipoPago" value="<?php echo $idTipoPago ?>" />
                            <?php
                            if (isset($stHidden) && ($stHidden)) {
                                ?>
                                <input type="hidden" name="idMesaEntrada" value="<?php echo $datosHabilitacion['IdMesaEntrada'] ?>" />
                                <?php
                            }
                            ?>
                            <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
                            <table>
                                <tr>
                                    <td><input type="button" onclick="location = window.location.search;" value="Cancelar" /></td>
                                    <td><input type="submit" value="Confirmar" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <?php
                }
            }
        } else {
            ?>
            <br/><input type="button" onclick="location = window.location.search;" value="Volver" />
            <?php
        }
    } else {
        echo "Hay un error";
        ?>
        <input type="button" onclick="location = window.location.search;" value="Volver" />
        <?php
    }
    ?>
</fieldset>



