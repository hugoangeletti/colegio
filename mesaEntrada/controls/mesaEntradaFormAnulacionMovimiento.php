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
        <h4>Alta de Solicitud de Anulación de Movimiento Matricular</h4>
        <h4>Está a punto de Anular el Trámite Nº <?php echo $datosMovimiento['IdMesaEntrada'] ?></h4>
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

                <form id="formMovimiento" action="agregarAnulacionMovimiento.php?idColegiado=<?php echo $aColegiado['Id'] ?>&id=<?php echo $datosMovimiento['IdMesaEntradaMovimiento'] ?>" method="post">
                    <table>
                        <tr>
                            <td><b>Motivo de Anulación:</b></td>
                            <td>
                                <textarea cols="60" rows="6" name="observaciones"></textarea>
                            </td>
                        </tr>
                        <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
                        <input type="hidden" name="idMesaEntradaMovimiento" value="<?php echo $datosMovimiento['IdMesaEntradaMovimiento'] ?>" />
                        <tr></tr>
                        <tr></tr>
                        <tr></tr>
                        <tr>
                            <td><input type="button" onclick="location = 'buscarPorMatricula.php?BoM=ok&matricula=<?php echo $aColegiado['Matricula'] ?>'" value="Volver"/></td>
                            <td><input type="submit" value="Confirmar" /></td>
                        </tr>
                    </table>
                </form>
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


