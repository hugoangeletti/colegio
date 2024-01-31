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
        case 'A': $actionForm = "agregarAutoprescripcion.php";
            $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarAutoprescripcion.php";
            $readOnly = true;
            $titulo = "Baja de";
            $stHidden = true;
            $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarAutoprescripcion.php";
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
        $hayAutoprescripcion = true;
        $consultaDatosAutoprescripcion = obtenerAutoprescripcionPorId($_GET['iEvento']);
        if (!$consultaDatosAutoprescripcion) {
            $datosAutoprescripcion = null;
        } else {
            if ($consultaDatosAutoprescripcion->num_rows == 0) {
                $datosAutoprescripcion = null;
            } else {
                $datosAutoprescripcion = $consultaDatosAutoprescripcion->fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosAutoprescripcion['Matricula'];
    } else {
        $hayAutoprescripcion = false;
    }
    ?>
    <script>
        $(document).ready(function() {
            $(".tituloWrap").hide();
        });
    </script>
    <div id="titulo">
        <h3>Mesa de Entrada</h3>
        <h4><?php echo $titulo ?> Solicitud de Autoprescripción</h4>
        <?php
        if ($numeroTramite) {
            ?>
            <h4>Trámite Nº <?php echo $datosAutoprescripcion['IdMesaEntrada'] ?></h4>
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

            //$verificacion = verificarPermisoColegiado($estadoMatricular['Estado'], 1);

            if (isset($estadoMatricular)) {
                if ($estadoMatricular['Estado'] == "J") {
                    $verificacion = 1;
                } else {
                    $verificacion = 0;
                }
            } else {
                $verificacion = -1;
            }

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
                        $('#formAutoprescripcion').submit(function(e) {
                            e.preventDefault();
                            var form = $(this);
                            var post_url = form.attr('action');
                            var post_data = form.serialize();
                            var ok = true;
                            if (!verif_fecha('fecha'))
                            {
                                ok = false;
                                $("#fecha").focus();
                            }
                            if (ok)
                            {
                                $.ajax({
                                    type: 'POST',
                                    url: post_url,
                                    data: post_data,
                                    dataType: "json",
                                    success: function(msg) {
                                        alert(msg.texto);

                                        if (msg.estado == 1)
                                        {
                                            if (msg.action == 'A')
                                            {
                                                window.open('hojaRutaAutoprescripcion.php', '_blank');

                                                location.reload();
                                            }
                                            else
                                            {
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            }

                        });
                    });

                </script>     
                <?php
                $yaHizoMovimiento = obtenerMovimientosPorIdColegiadoHoy($aColegiado['Id']);
                if ($yaHizoMovimiento) {
                    $pregunta = false;
                    if (!$hayAutoprescripcion) {
                        if ($yaHizoMovimiento->num_rows != 0) {
                            $pregunta = true;
                        }
                    }

                    //if (!$pregunta) {
                    if(true){
                        ?>
                        <form id="formAutoprescripcion" action="<?php echo $actionForm ?>" method="post">
                            <table>
                                <tr>
                                    <td><b>Fecha Autoprescripción:</b></td>
                                    <td>
                                        <input id="fecha" name="fecha" type="text" required onblur="return verif_fecha(this.id);" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            $fechaDesdeInvertir = explode("-", $datosAutoprescripcion['Fecha']);
                                            $fechaDesde = $fechaDesdeInvertir[2] . "-" . $fechaDesdeInvertir[1] . "-" . $fechaDesdeInvertir[0];
                                            echo $fechaDesde;
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
                                    <td><b>Autorizado 1:</b></td>
                                    <td>
                                        <input type="text" name="autorizado" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['Autorizado'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Documento Autorizado 1:</b></td>
                                    <td>
                                        <input type="text" name="documentoAutorizado" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['DocumentoAutorizado'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Parentezco 1:</b></td>
                                    <td><input type="text" name="parentezco" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['Parentezco'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>></td>
                                </tr>
                                <tr>
                                    <td><b>Autorizado 2:</b></td>
                                    <td>
                                        <input type="text" name="autorizado2" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['Autorizado2'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Documento Autorizado 2:</b></td>
                                    <td>
                                        <input type="text" name="documentoAutorizado2" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['DocumentoAutorizado2'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Parentezco 2:</b></td>
                                    <td><input type="text" name="parentezco2" value="<?php
                                        if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                            echo $datosAutoprescripcion['Parentezco2'];
                                        }
                                        ?>" <?php
                                               if ((isset($readOnly) && ($readOnly)) || (isset($_GET['orden']))) {
                                                   echo "readonly=readonly";
                                               }
                                               ?>></td>
                                </tr>
                                <tr>
                                    <td><b>Observaciones:</b></td>
                                    <td>
                                        <textarea cols="60" rows="6" name="observaciones" <?php
                                        if (isset($readOnly) && ($readOnly)) {
                                            echo "readonly=readonly";
                                        }
                                        ?>><?php
                                                      if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
                                                          if (!is_null($datosAutoprescripcion['Observaciones'])) {
                                                              echo $datosAutoprescripcion['Observaciones'];
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
                                        <input type="hidden" name="tipoMovimiento" value="<?php echo $datosAutoprescripcion['IdTipoMovimiento'] ?>" />
                                        <?php
                                    }
                                    ?>
                                    <input type="hidden" name="idMesaEntrada" value="<?php echo $datosAutoprescripcion['IdMesaEntrada'] ?>" />
                                    <?php
                                }
                                ?>
                                <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
                                <tr></tr>
                                <tr></tr>
                                <tr></tr>
                                <tr>
                                    <?php
                                    if (isset($hayAutoprescripcion) && ($hayAutoprescripcion)) {
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
                                        ?>
                                        <td><input type="submit" value="Confirmar" /></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                            </table>
                        </form>
                        <?php
                    } else {
                        ?>
                        <br>
                        <span class="mensajeWARNING">El colegiado ya realizó un movimiento en esta fecha.</span>
                        <?php
                    }
                } else {
                    ?>
                    <br>
                    <span class="mensajeERROR">Problema en la base de datos.</span>
                    <?php
                }
            } else {
                ?>
                <br/><input type="button" onclick="location = window.location.search;" value="Volver" />
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


