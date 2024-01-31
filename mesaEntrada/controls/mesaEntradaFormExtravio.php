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
    $stHidden = false;
    $readOnly = false;
    $numeroTramite = false;
    switch ($_GET['action']) {
        case 'A': $actionForm = "agregarDenunciaExtravio.php";
            $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarDenunciaExtravio.php";
            $readOnly = true;
            $titulo = "Baja de";
//$titulo = "Anulación de";
            $stHidden = true;
            $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarDenunciaExtravio.php";
            $titulo = "Modificación de";
            $stHidden = true;
            $numeroTramite = true;
            break;
        case 'V':
            $actionForm = "";
            $readOnly = true;
            $titulo = "Información de";
            $stHidden = true;
            $numeroTramite = true;
            break;
    }

    if (isset($_GET['iEvento'])) {
        $hayMovimiento = true;
        $consultaDatosMovimiento = obtenerDenunciaPorId($_GET['iEvento']);
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
    ?>
    <script>
        $(document).ready(function() {
            $(".tituloWrap").hide();
        });
    </script>
    <div id="titulo">
        <h3>Mesa de Entrada</h3>
        <h4><?php echo $titulo ?> Solicitud de Denuncia de Extravío o Falsificación</h4>
        <?php
        if ($numeroTramite) {
            ?>
            <h4>Trámite Nº <?php echo $datosMovimiento['IdMesaEntrada'] ?></h4>
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

            $permiso = true;
            if ($permiso) {
                ?>
                <script type="text/javascript">
                    $(function() {
                        console.log($('#formExtravio').submit(function(e) {
                            e.preventDefault();
                            var form = $(this);
                            var post_url = form.attr('action');
                            var post_data = form.serialize();
                            var ok = true;

                            if (ok)
                            {
                                console.log($.ajax({
                                    type: 'POST',
                                    url: post_url,
                                    data: post_data,
                                    dataType: "json",
                                    success: function(msg) {
                                        alert(msg.texto);
                                        if (msg.estado == 1)
                                        {
                                            if (msg.action == "A")
                                            {
                                                window.open('hojaRutaExtravio.php', '_blank');
                                            }
                                            location.reload();
                                        }
                                    }
                                }));
                            }

                        }));
                    });
                </script>     
                <form id="formExtravio" action="<?php echo $actionForm ?>" method="post">
                    <table>
                        <tr>
                            <td><b>Tipo de Denuncia:</b></td>
                            <td>
                                <select id="idTipoDenuncia" name="idTipoDenuncia" required <?php
                                if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                    echo "disabled";
                                }
                                ?>>
                                    <option value="">Seleccione un Tipo</option>
                                    <?php
                                    $tiposDenuncia = obtenerTiposDenuncia();
                                    if ($tiposDenuncia) {
                                        if ($tiposDenuncia->num_rows != 0) {
                                            while ($row = $tiposDenuncia->fetch_assoc()) {
                                                ?>
                                                <option value="<?php echo $row['Id'] ?>" <?php
                                                if (isset($hayMovimiento) && ($hayMovimiento)) {
                                                    if ($row['Id'] == $datosMovimiento['IdTipoDenuncia']) {
                                                        echo "selected";
                                                    }
                                                }
                                                ?>><?php echo utf8_encode($row['Nombre']) ?></option>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Fecha Denuncia:</b></td>
                            <td>
                                <input id="fechaDenuncia" name="fechaDenuncia" type="text" required onblur="return verif_fecha(this.id);" value="<?php
                                if (isset($hayMovimiento) && ($hayMovimiento)) {/* $fechaDesdeInvertir = explode("-", $datosMovimiento['Fecha']); $fechaDesde = $fechaDesdeInvertir[2]."-".$fechaDesdeInvertir[1]."-".$fechaDesdeInvertir[0]; */
                                    $fechaDenunciaInvertir = explode("-", $datosMovimiento['FechaDenuncia']);
                                    $fechaDenuncia = $fechaDenunciaInvertir[2] . "-" . $fechaDenunciaInvertir[1] . "-" . $fechaDenunciaInvertir[0];
                                    echo $fechaDenuncia;
                                } else {
                                    echo date("d-m-Y");
                                }
                                ?>" <?php
                                       if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                           echo "readonly=readonly";
                                       }
                                       ?>/> Debe Ingresar la Fecha con este formato(dd-mm-aaaa)
                            </td>
                        </tr>
                        <tr>
                            <td><b>Fecha Extravío / Falsificación / Robo:</b></td>
                            <td>
                                <input id="fechaExtravio" name="fechaExtravio" type="text" required onblur="return verif_fecha(this.id);" value="<?php
                                if (isset($hayMovimiento) && ($hayMovimiento)) {/* $fechaDesdeInvertir = explode("-", $datosMovimiento['Fecha']); $fechaDesde = $fechaDesdeInvertir[2]."-".$fechaDesdeInvertir[1]."-".$fechaDesdeInvertir[0]; */
                                    $fechaExtravioInvertir = explode("-", $datosMovimiento['FechaExtravio']);
                                    $fechaExtravio = $fechaExtravioInvertir[2] . "-" . $fechaExtravioInvertir[1] . "-" . $fechaExtravioInvertir[0];
                                    echo $fechaExtravio;
                                } else {
                                    echo date("d-m-Y");
                                }
                                ?>" <?php
                                       if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                           echo "readonly=readonly";
                                       }
                                       ?>/> Debe Ingresar la Fecha con este formato(dd-mm-aaaa)
                            </td>
                        </tr>
                        <tr>
                            <td><b>Observaciones:</b></td>
                            <td>
                                <textarea cols="60" rows="6" name="observaciones" <?php
                                if (isset($readOnly) && ($readOnly)) {
                                    echo "readonly=readonly";
                                }
                                ?>><?php
                                              if (isset($hayMovimiento) && ($hayMovimiento)) {
                                                  if (!is_null($datosMovimiento['Observaciones'])) {
                                                      echo $datosMovimiento['Observaciones'];
                                                  }
                                              }
                                              ?></textarea>
                            </td>
                        </tr>
                        <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
                        <?php
                        if (isset($stHidden) && ($stHidden)) {
                            if (isset($_GET['orden'])) {
                                ?>
                                <input type="hidden" name="idTipoDenuncia" value="<?php echo $datosMovimiento['IdTipoDenuncia'] ?>" />
                                <?php
                            }
                            ?>
                            <input type="hidden" name="idMesaEntrada" value="<?php echo $datosMovimiento['IdMesaEntrada'] ?>" />
                            <?php
                        }
                        ?>
                        <input type = "hidden" name = "tipoAccion" value = "<?php echo $_GET['action'] ?>" />
                        <tr></tr>
                        <tr></tr>
                        <tr></tr>
                        <tr>
                            <?php
                            if (isset($hayMovimiento) && ($hayMovimiento)) {
                                if ($_GET['action'] == "V") {
                                    if (isset($_GET['fecha'])) {
                                        ?>
                                        <td><input type="button" onclick="location = 'listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Volver" /></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td><input type="button" onclick="location = 'buscarPorMatricula.php?BoM=ok&matricula=<?php echo $_GET['mT'] ?>'" value="Volver"/></td>
                                        <?php
                                    }
                                } else {
                                    if (isset($_GET['fecha'])) {
                                        ?>
                                        <td><input type="button" onclick="location = 'listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Cancelar" /></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td><input type="button" onclick="$('#page-wrap').load('ordenDiaListadoDetalle.php?iOrden=<?php echo $_GET['orden'] ?>&st=<?php echo $_GET['st'] ?>');" value="Volver"/></td>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <td><input type="button" onclick="location = window.location.search;" value="Cancelar" /></td>
                                <?php
                            }

                            if ($_GET['action'] != 'V') {
                                if (!$noPuedeAnular) {
                                    ?>
                                    <td><input type="submit" value="Confirmar" /></td>
                                    <?php
                                }
                            }
                            ?>
                        </tr>
                    </table>
                </form>
                <?php
            }
        } else {
            ?>
            <br/><input type="button" onclick="location = window.location.search;" value="Volver" />
            <?php
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


