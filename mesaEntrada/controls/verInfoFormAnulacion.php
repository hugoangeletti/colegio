<?php
require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['action'])) {
    if (isset($_GET['iEvento'])) {
        $hayMovimiento = true;
        $consultaDatosMovimiento = obtenerMovimientoPorId($_GET['iEvento']);
        if (!$consultaDatosMovimiento) {
            $datosMovimiento = null;
        } else {
            if ($consultaDatosMovimiento->num_rows == 0) {
                $datosMovimiento = null;
            } else {
                $datosMovimiento = $consultaDatosMovimiento->fetch_assoc();

                $consultaMovimientoAnulado = obtenerMovimientoPorIdMovimiento($datosMovimiento['IdMesaEntradaMovimientoAnulado']);

                if (!$consultaMovimientoAnulado) {
                    die("Hubo un error en el sistema.");
                } else {
                    if ($consultaMovimientoAnulado->num_rows == 0) {
                        die("El sistema no registra el movimiento anulado.");
                    } else {

                        $datoMovimientoAnulado = $consultaMovimientoAnulado->fetch_assoc();
                    }
                }
            }
        }
        $okey = true;
        $matricula = $datosMovimiento['Matricula'];
    } else {
        $hayMovimiento = false;
    }
    $noPuedeAnular = false;
    $permiso = true;
    ?>
    <script>
        $(document).ready(function() {
            $(".tituloWrap").hide();
        });
    </script>
    <div id="titulo">
        <h3>Mesa de Entrada</h3>
        <h4>Información de Solicitud de Anulación de Movimiento Matricular</h4>
        <h4>Trámite Nº <?php echo $datosMovimiento['IdMesaEntrada'] ?></h4>
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
            if ($permiso) {
                ?>
                <script type="text/javascript">
                    $(function() {
                        console.log($('#formMovimiento').submit(function(e) {
                            e.preventDefault();
                            var form = $(this);
                            var post_url = form.attr('action');
                            var post_data = form.serialize();

                            console.log($.ajax({
                                type: 'POST',
                                url: post_url,
                                data: post_data,
                                dataType: "json",
                                success: function(msg) {
                                    alert(msg.texto);
                                    if (msg.estado == 1)
                                    {

                                        window.open('hojaRutaAnulacion.php', '_blank');

                                        location.reload();
                                    }
                                }
                            }));

                        }));
                    });
                </script>     

                <table>
                    <tr><td><b>Movimiento Anulado:</b></td>
                        <td><?php echo utf8_encode($datoMovimientoAnulado['DetalleCompleto']) . " - " . utf8_encode($datoMovimientoAnulado['NombreMotivoCancelacion']); ?></td>
                    </tr>
                    <tr>
                        <td><b>Motivo de Anulación:</b></td>
                        <td>
                            <textarea cols="60" rows="6" name="observaciones" readonly="true"><?php echo $datosMovimiento['Observaciones']; ?></textarea>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="button" onclick="location = window.location.search;" value="Volver" />
                <?php
            }
        }
    } else {
        ?>
        <br>
        <span class="mensajeERROR">Hay un error</span>
        <br>
        <input type="button" onclick="location = window.location.search;" value="Volver" />
        <?php
    }
    ?>
</fieldset>


