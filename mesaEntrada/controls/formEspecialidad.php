<?php
require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

//echo sumarFecha(date("Y-m-d"), 2, "+", "year");

if (isset($_GET['action'])) {
    $stHidden = false;
    $readOnly = false;
    $numeroTramite = false;
    switch ($_GET['action']) {
        case 'A': $actionForm = "agregarEspecialidad.php";
            $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarEspecialidad.php";
            $readOnly = true;
            $titulo = "Baja de";
            $stHidden = true;
            $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarEspecialidad.php";
            $titulo = "Modificación de";
            $stHidden = true;
            $numeroTramite = true;
            break;
    }

    if (isset($_GET['iEvento'])) {
        $hayEspecialidad = true;
        $consultaDatosEspecialidad = obtenerEspecialidadMesaEntradaPorId($_GET['iEvento']);
        if (!$consultaDatosEspecialidad) {
            $datosEspecialidad = null;
        } else {
            if ($consultaDatosEspecialidad->num_rows == 0) {
                $datosEspecialidad = null;
            } else {
                $datosEspecialidad = $consultaDatosEspecialidad->fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosEspecialidad['Matricula'];
    } else {
        $hayEspecialidad = false;
    }
    ?>
    <div id="titulo">
        <h3>Mesa de Entrada</h3>
        <h4><?php echo $titulo ?> Solicitud de Especialidad</h4>
        <?php
        if ($numeroTramite) {
            ?>
            <h4>Trámite Nº <?php echo $datosEspecialidad['IdMesaEntrada'] ?></h4><br/>
            <h4>Expediente Nº<?php echo $datosEspecialidad['NumeroExpediente'] ?></h4>
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
        if (isset($_GET['tE'])) {
            $tipoEspecialidad = $_GET['tE'];
        } else if (isset($_POST['tipoEspecialidad'])) {
            $tipoEspecialidad = $_POST['tipoEspecialidad'];
        }

        if (!$error) {
            ?>
            <script type="text/javascript">
                $(function () {

                    $('#formEspecialidad').submit(function (e) {
                        e.preventDefault();
                        var form = $(this);
                        var post_url = form.attr('action');
                        var post_data = form.serialize();
                        var dis = $(".inputDistrito").val();
                        var ok = true;

                        if (!(typeof (dis) === "undefined"))
                        {
                            if (isNaN(dis))
                            {
                                alert("No se admiten letras en ese campo.");
                                $(".inputDistrito").focus();
                                ok = false;
                            }
                        }
                        if (ok)
                        {
                            console.log($.ajax({
                                type: 'POST',
                                url: post_url,
                                data: post_data,
                                dataType: "json",
                                success: function (msg) {


                                    if ($.trim(msg.texto) == "La especialidad se dio de alta correctamente.")
                                    {
                                        window.open('expediente.php', '_blank');

                                        location.reload();
                                    }
                                    else
                                    {
                                        alert(msg.texto);
                                    }
                                    if (($.trim(msg.texto) == "La modificación se realizó correctamente.") || ($.trim(msg.texto) == "La especialidad se dio de baja correctamente."))
                                    {

                                        location.reload();
                                    }
                                }
                            }));
                        }
                    });

                    $("input[name='tipoEspecialidad']").change(function (e) {
                        e.preventDefault();
                        var seleccionado = $("input[name='tipoEspecialidad']:checked").val();
                        $(".tdDistrito").remove();
                        if (seleccionado == "O")
                        {
                            $(".trDistrito").append("<td class='tdDistrito'><b>Distrito al cual egresa:</b></td><td class='tdDistrito'><input class='inputDistrito' type='text' name='distrito' required /></td>");
                        }
                    });
                });
            </script>    
            <div id="formularioEspecialidad">
                <form id="formEspecialidad" action="<?php echo $actionForm ?>" method="post">
                    <table>
                        <?php
                        if ($hayEspecialidad) {
                            $presenta = false;
                            $consultaEstadoDeuda = obtenerEstadoDeudaColegiadoPorIdMesaEntrada($_GET['iEvento']);
                            $estadoDeuda = $consultaEstadoDeuda->fetch_assoc();
                            if ($estadoDeuda['IdTipoEstadoCuota'] != 2) {
                                $todasEspecialidades = obtenerEspecialidades();

                                if (!$todasEspecialidades) {
                                    ?>
                                    <br>
                                    <span class="mensajeERROR">Hubo error en la Base de Datos.</span>
                                    <br>
                                    <?php
                                } else {
                                    if ($todasEspecialidades->num_rows == 0) {
                                        ?>
                                        <br>
                                        <span class="mensajeWARNING">No hay especialidades cargadas.</span>
                                        <br>
                                        <?php
                                    } else {
                                        $presenta = true;
                                        ?>
                                        <tr>
                                            <td><b>Especialidad Seleccionada:</b></td>
                                            <td>
                                                <select name="especialidad" <?php
                                                if (isset($readOnly) && ($readOnly)) {
                                                    echo "disabled";
                                                }
                                                ?>>
                                                            <?php
                                                            while ($especialidades = $todasEspecialidades->fetch_assoc()) {
                                                                ?>

                                                        <option value="<?php echo $especialidades['Id'] ?>" <?php
                                                        if ($especialidades['Id'] == $datosEspecialidad['IdEspecialidad']) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo utf8_encode($especialidades['Especialidad']) ?></option>

                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                echo "<p class='codError'>Para poder anular este movimiento, primero debe anular el recibo de pago.</p>";
                            }
                        } else {
                            if (isset($_GET['especialidad'])) {

                                $consultaTodasEspecialidades = obtenerEspecialidades();

                                if (!$consultaTodasEspecialidades) {
                                    ?>
                                    <br>
                                    <span class="mensajeERROR">Hubo error en la Base de Datos.</span>
                                    <br>
                                    <?php
                                } else {
                                    if ($consultaTodasEspecialidades->num_rows == 0) {
                                        ?>
                                        <br>
                                        <span class="mensajeWARNING">No hay especialidades cargadas.</span>
                                        <br>
                                        <?php
                                    } else {
                                        $presenta = true;
                                        ?>
                                        <tr>
                                            <td><b>Especialidad Seleccionada:</b></td>
                                            <td colspan="2">
                                                <select name="especialidad" disabled>
                                                    <?php
                                                    while ($especialidades = $consultaTodasEspecialidades->fetch_assoc()) {
                                                        ?>

                                                        <option value="<?php echo $especialidades['Id'] ?>" <?php
                                                        if ($especialidades['Id'] == $_GET['especialidad']) {
                                                            echo "selected";
                                                        }
                                                        ?>><?php echo utf8_encode($especialidades['Especialidad']) ?></option>

                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }

                                $yaTiene = obtenerEspecialidadesPorColegiado($aColegiado['Id']);
                                $esp = array();
                                if ($yaTiene) {
                                    if ($yaTiene->num_rows != 0) {
                                        while ($row = $yaTiene->fetch_assoc()) {
                                            array_push($esp, $row['idEspecialidad']);
                                        }
                                    }
                                }

                                $tE = $_GET['tE'];
                                $consultaEspecialidad = obtenerEspecialidadPorId($_GET['especialidad']);
                                $especialidad = $consultaEspecialidad->fetch_assoc();
                                $nombreEspecialidad = $especialidad['Especialidad'];
                                $presenta = true;
                                if (in_array($_GET['especialidad'], $esp)) {
                                    //$presenta = false;
                                    $consultaInfoEspecialidad = obtenerInformacionEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad']);

                                    if ($consultaInfoEspecialidad) {
                                        if ($consultaInfoEspecialidad->num_rows == 0) {
                                            ?>
                                            <tr>
                                                <td><span class="mensajeWARNING">No hay especialidad con ese id para este colegiado.</span></td>
                                            </tr>
                                            <?php
                                        } else {
                                            $infoEspecialidad = $consultaInfoEspecialidad->fetch_assoc();
                                            ?>
                                            <!--
                                            <tr>
                                                <td><b>Especialidad Seleccionada:</b></td>
                                                <td><?php echo utf8_encode($nombreEspecialidad) ?></td>
                                            </tr>
                                            -->
                                            <input type="hidden" name="especialidad" value="<?php echo $_GET['especialidad'] ?>" />
                                            <tr>
                                                <td><label for='recertificacion'>Recertificación</label></td>
                                                <td><input id='recertificacion' type="radio" name="tipoEspecialidad" value="R" <?php
                                                    $codError = 0;
                                                    $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $infoEspecialidad['idEspecialidad'], "R");
                                                    $textoCodError = "";
                                                    if ($enTramite) {
                                                        if ($enTramite->num_rows == 0) {
                                                            if (is_null($infoEspecialidad['FechaVencimiento'])) {
                                                                //echo "disabled";
                                                                //$codError = 7;
                                                                //cambiado el 19/09/2016, para que puedan presentar el desempeño
                                                                
                                                            }else
                                                            {
                                                                //$infoEspecialidad['FechaVencimiento'] = '2015-09-11';
                                                                if(date('Y-m-d') < sumarFechaCompleto($infoEspecialidad['FechaVencimiento'], 3, '-', 'month'))
                                                                {
                                                                    echo "disabled";
                                                                    $codError = 12;
                                                                    $textoCodError = invertirFecha($infoEspecialidad['FechaVencimiento']);
                                                                }
                                                            }
                                                        } else {
                                                            echo "disabled";
                                                            $codError = 8;
                                                        }
                                                    }
                                                    ?>/></td>
                                                <td>
                                                    <?php
                                                    echo mostrarLeyendaEspecialidad($codError, $textoCodError);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for='especialistaJerarquizado'>Especialista Jerarquizado</label></td>
                                                <td><input id='especialistaJerarquizado' type="radio" name="tipoEspecialidad" value="J" <?php
                                                    $codError = 0;
                                                    $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $infoEspecialidad['idEspecialidad'], "J");
                                                    if ($enTramite) {
                                                        if ($enTramite->num_rows == 0) {
                                                            //pregunta si es homologado por nacion IdTipoEspecialista == 8
                                                            if ($infoEspecialidad['IdTipoEspecialista'] == 8) {
                                                                echo "disabled";
                                                                $codError = 11;
                                                            } else {
                                                                if (!is_null($infoEspecialidad['TipoEspecialista'])) {
                                                                    if ($infoEspecialidad['TipoEspecialista'] == "J") {
                                                                        echo "disabled";
                                                                        $codError = 1;
                                                                    } else {
                                                                        if ($infoEspecialidad['TipoEspecialista'] != "C") {
                                                                            $fecha = invertirFecha($infoEspecialidad['FechaEspecialista']);
                                                                            $diferenciaFecha = calcularDiferenciaFecha($fecha);
                                                                            $vencido = false;
                                                                            if (!is_null($infoEspecialidad['FechaVencimiento'])) {
                                                                                $fechaLimiteSolicitarJer = sumarFecha($infoEspecialidad['FechaVencimiento'], 2, "+", "year");
                                                                                $fechaLimiteSolicitarJer = sumarFecha($fechaLimiteSolicitarJer, 3, "-", "month");
                                                                                if ($fechaLimiteSolicitarJer < date("Y-m-d")) {
                                                                                    $vencido = true;
                                                                                }
                                                                            }
                                                                            if (($vencido)) {
                                                                                echo "disabled";
                                                                                $codError = 6;
                                                                            } else {
                                                                                if (($diferenciaFecha < 5)) {
                                                                                    echo "disabled";
                                                                                    $codError = 3;
                                                                                }
                                                                            }
                                                                        } else {
                                                                            echo "disabled";
                                                                            $codError = 1;
                                                                        }
                                                                    }
                                                                } else {
                                                                    $fechaLimiteSolicitarCon = sumarFecha($infoEspecialidad['FechaEspecialista'], 5, "+", "year");
                                                                    $fechaLimiteSolicitarCon = sumarFecha($fechaLimiteSolicitarCon, 3, "-", "month");
                                                                    if ($fechaLimiteSolicitarCon > date("Y-m-d")) {
                                                                        echo "disabled";
                                                                        $codError = 3;
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $codError = 8;
                                                            echo "disabled";
                                                        }
                                                    } else {
                                                        $codError = 8;
                                                        echo "disabled";
                                                    }
                                                    ?>/></td>
                                                <td>
                                                    <?php
                                                    echo mostrarLeyendaEspecialidad($codError);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><label for='especialistaConsultor'>Especialista Consultor</label></td>
                                                <td><input id='especialistaConsultor' type="radio" name="tipoEspecialidad" value="C" <?php
                                                    $codError = 0;
                                                    $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $infoEspecialidad['idEspecialidad'], "C");
                                                    if ($enTramite) {
                                                        if ($enTramite->num_rows == 0) {
                                                            //pregunta si es homologado por nacion IdTipoEspecialista == 8
                                                            if ($infoEspecialidad['IdTipoEspecialista'] == 8) {
                                                                echo "disabled";
                                                                $codError = 11;
                                                            } else {
                                                                if (!is_null($infoEspecialidad['TipoEspecialista'])) {
                                                                    if ($infoEspecialidad['TipoEspecialista'] == "C") {
                                                                        echo "disabled";
                                                                        $codError = 2;
                                                                    } else {
                                                                        if ($infoEspecialidad['TipoEspecialista'] == "J") {
                                                                            $fechaTipoEspecialista = obtenerFechaTipoEspecialistaPorTipo($aColegiado['Id'], "J", $infoEspecialidad['idEspecialidad']);
                                                                            if (!$fechaTipoEspecialista) {
                                                                                echo "error";
                                                                            } else {
                                                                                if ($fechaTipoEspecialista->num_rows == 0) {
                                                                                    echo "disabled";
                                                                                    $codError = 5;
                                                                                } else {
                                                                                    $vencido = false;
                                                                                    if (!is_null($infoEspecialidad['FechaVencimiento']) && $infoEspecialidad['FechaVencimiento'] != "0000-00-00") {
                                                                                        if (sumarFecha($infoEspecialidad['FechaVencimiento'], 2, "+", "year") < date("Y-m-d")) {
                                                                                            $vencido = true;
                                                                                        }
                                                                                    }
                                                                                    if (($vencido)) {
                                                                                        echo "disabled";
                                                                                        $codError = 6;
                                                                                    } else {
                                                                                        $fecha = invertirFecha($infoEspecialidad['FechaTipoEspecialista']);
                                                                                        $diferenciaFecha = calcularDiferenciaFecha($fecha);
                                                                                        if (($diferenciaFecha < 5)) {
                                                                                            echo "disabled";
                                                                                            $codError = 3;
                                                                                        } else {
                                                                                            $fecha = invertirFecha($infoEspecialidad['FechaEspecialista']);
                                                                                            $diferenciaFecha = calcularDiferenciaFecha($fecha);
                                                                                            if (($diferenciaFecha < 15)) {
                                                                                                echo "disabled";
                                                                                                $codError = 4;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        } else {
                                                                            echo "disabled";
                                                                            $codError = 5;
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "disabled";
                                                                    $codError = 5;
                                                                }
                                                            }
                                                        } else {
                                                            $codError = 8;
                                                            echo "disabled";
                                                        }
                                                    }
                                                    ?>/></td>
                                                <td>
                                                    <?php
                                                    echo mostrarLeyendaEspecialidad($codError);
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } else {
                                    $presenta = true;
                                    if (isset($_POST['matricula'])) {
                                        $colegiado = $_POST['matricula'];
                                    } else if (isset($_GET['matricula'])) {
                                        $colegiado = $_GET['matricula'];
                                    }


                                    if (isset($_GET['tE'])) {
                                        $tE = $_GET['tE'];
                                    } else if ($_POST['tipoEspecialidad']) {
                                        $tE = $_POST['tipoEspecialidad'];
                                    }

                                    if ($tE == 3) {
                                        $consultaInfoCalificacion = obtenerInformacionEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad']);
                                        $infoCalificacion = $consultaInfoCalificacion->fetch_assoc();
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <?php
                                                $consultaNombreCabecera = obtenerFechaCalificacionAgregada(trim($aColegiado['Id']), trim($_GET['especialidad']));
                                                if ($consultaNombreCabecera) {
                                                    if ($consultaNombreCabecera->num_rows != 0) {
                                                        $nombreCabecera = $consultaNombreCabecera->fetch_assoc();
                                                        echo "por " . utf8_encode($nombreCabecera['nombreEspecialidad']);
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                        <input type="hidden" name="especialidad" value="<?php echo $_GET['especialidad'] ?>" />
                                        <td><label for='nuevaCalificacion'>Nueva Calificación Agregado</label></td>
                                        <td><input id='nuevaCalificacion' type="radio" name="tipoEspecialidad" value="A" checked="checked" <?php
                                            $codError = 0;
                                            $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "A"); //$infoCalificacion['idEspecialidad'], "A");
                                            if ($enTramite) {
                                                if ($enTramite->num_rows == 0) {
                                                    if ($consultaInfoCalificacion->num_rows == 0) {
                                                        $consultaFechaCalificacion = obtenerFechaCalificacionAgregada(trim($aColegiado['Id']), trim($_GET['especialidad']));
                                                        if ($consultaFechaCalificacion) {
                                                            if ($consultaFechaCalificacion->num_rows == 0) {
                                                                echo "disabled";
                                                                $codError = 10;
                                                            } else {
                                                                $fechaCalificacion = $consultaFechaCalificacion->fetch_assoc();
                                                                $fecha = invertirFecha($fechaCalificacion['FechaEspecialista']);
                                                                $diferenciaFecha = calcularDiferenciaFecha($fecha);
                                                                $vencido = false;
                                                                if (!is_null($infoCalificacion['FechaVencimiento'])) {
                                                                    if (sumarFecha($infoEspecialidad['FechaVencimiento'], 2, "+", "year") < date("Y-m-d")) {
                                                                        $vencido = true;
                                                                    }
                                                                }
                                                                if (($vencido)) {
                                                                    echo "disabled";
                                                                    $codError = 6;
                                                                } else {
                                                                    if ($diferenciaFecha < 2) {
                                                                        echo "disabled";
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            echo "disabled";
                                                            $codError = 9;
                                                        }
                                                    } else {
                                                        echo "disabled";
                                                        $codError = 9;
                                                    }
                                                } else {
                                                    $codError = 8;
                                                    echo "disabled";
                                                }
                                            } else {
                                                echo "disabled";
                                                $codError = 10;
                                            }
                                            ?>/></td>
                                        <td>
                                            <?php
                                            echo mostrarLeyendaEspecialidad($codError);
                                            ?>
                                        </td>
                                        </tr>
                                        <?php
                                    } else {
                                        ?>
                                        <input type="hidden" name="especialidad" value="<?php echo $_GET['especialidad'] ?>" />
                                        <tr>
                                            <td><label for='nuevaEspecialidad'>Nueva Especialidad</label></td>
                                            <td><input id='nuevaEspecialidad' type="radio" name="tipoEspecialidad" value="E" 
                                                <?php
                                                $codError = 0;
                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "E");
                                                if ($enTramite) {
                                                    if ($enTramite->num_rows != 0) {
                                                        $codError = 8;
                                                        echo "disabled";
                                                    } else {
                                                        $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "O");
                                                        if ($enTramite) {
                                                            if ($enTramite->num_rows != 0) {
                                                                $codError = 8;
                                                                echo "disabled";
                                                            } else {
                                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "X");
                                                                if ($enTramite) {
                                                                    if ($enTramite->num_rows != 0) {
                                                                        $codError = 8;
                                                                        echo "disabled";
                                                                    } else {
                                                                        $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "N");
                                                                        if ($enTramite) {
                                                                            if ($enTramite->num_rows != 0) {
                                                                                $codError = 8;
                                                                                echo "disabled";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>/></td>
                                            <td>
                                                <?php
                                                echo mostrarLeyendaEspecialidad($codError);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label for='especialistaExceptuado'>Especialista Exceptuado Art.8</label></td>
                                            <td><input id='especialistaExceptuado' type="radio" name="tipoEspecialidad" value="X" 
                                                <?php
                                                $codError = 0;
                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "X");
                                                if ($enTramite) {
                                                    if ($enTramite->num_rows != 0) {
                                                        $codError = 8;
                                                        echo "disabled";
                                                    } else {
                                                        $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "E");
                                                        if ($enTramite) {
                                                            if ($enTramite->num_rows != 0) {
                                                                $codError = 8;
                                                                echo "disabled";
                                                            } else {
                                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "O");
                                                                if ($enTramite) {
                                                                    if ($enTramite->num_rows != 0) {
                                                                        $codError = 8;
                                                                        echo "disabled";
                                                                    } else {
                                                                        $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "N");
                                                                        if ($enTramite) {
                                                                            if ($enTramite->num_rows != 0) {
                                                                                $codError = 8;
                                                                                echo "disabled";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>/></td>
                                            <td>
                                                <?php
                                                echo mostrarLeyendaEspecialidad($codError);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label for='especialistaOtroDistrito'>Especialista de Otro Distrito</label></td>
                                            <td><input id='especialistaOtroDistrito' type="radio" name="tipoEspecialidad" value="O" 
                                                <?php
                                                $codError = 0;
                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "O");
                                                if ($enTramite) {
                                                    if ($enTramite->num_rows != 0) {
                                                        $codError = 8;
                                                        echo "disabled";
                                                    } else {
                                                        $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "E");
                                                        if ($enTramite) {
                                                            if ($enTramite->num_rows != 0) {
                                                                $codError = 8;
                                                                echo "disabled";
                                                            } else {
                                                                $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "N");
                                                                if ($enTramite) {
                                                                    if ($enTramite->num_rows != 0) {
                                                                        $codError = 8;
                                                                        echo "disabled";
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>/></td>
                                            <td>
                                                <?php
                                                echo mostrarLeyendaEspecialidad($codError);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr class="trDistrito">

                                        </tr>
                                        <tr>
                                            <td><label for='expedidoPorNacion'>Expedido por Ministerio de Salud de la Nación</label></td>
                                            <td><input id='expedidoPorNacion' type="radio" name="tipoEspecialidad" value="N" 
                                                       <?php
                                                       $codError = 0;
                                                       $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "E");
                                                       if ($enTramite) {
                                                           if ($enTramite->num_rows != 0) {
                                                               $codError = 8;
                                                               echo "disabled";
                                                           } else {
                                                               $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "O");
                                                               if ($enTramite) {
                                                                   if ($enTramite->num_rows != 0) {
                                                                       $codError = 8;
                                                                       echo "disabled";
                                                                   } else {
                                                                       $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "X");
                                                                       if ($enTramite) {
                                                                           if ($enTramite->num_rows != 0) {
                                                                               $codError = 8;
                                                                               echo "disabled";
                                                                           } else {
                                                                               $enTramite = obtenerEspecialidadPorIdPorColegiado($aColegiado['Id'], $_GET['especialidad'], "N");
                                                                               if ($enTramite) {
                                                                                   if ($enTramite->num_rows != 0) {
                                                                                       $codError = 8;
                                                                                       echo "disabled";
                                                                                   }
                                                                               }
                                                                           }
                                                                       }
                                                                   }
                                                               }
                                                           }
                                                       }
                                                       ?>/></td>
                                            <td>
                                                <?php
                                                echo mostrarLeyendaEspecialidad($codError);
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
                        if ($presenta) {
                            ?>
                            <tr>
                                <td><b>Observaciones:</b></td>
                                <td colspan="2"><textarea cols="60" rows="6" name="observaciones" <?php
                                    if (isset($readOnly) && ($readOnly)) {
                                        echo "readonly=readonly";
                                    }
                                    ?>><?php
                                                              if ($hayEspecialidad) {
                                                                  echo utf8_encode($datosEspecialidad['Observaciones']);
                                                              }
                                                              ?></textarea><br/></td>
                            </tr>         
                            <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
                            <?php
                            if (isset($stHidden) && ($stHidden)) {
                                ?>
                                <input type="hidden" name="idMesaEntrada" value="<?php echo $datosEspecialidad['IdMesaEntrada'] ?>" />
                                <?php
                            }
                            ?>
                            <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />

                            <tr>
                                <?php
                                if (isset($hayEspecialidad) && ($hayEspecialidad)) {
                                    ?>
                                    <td><input type="button" onclick="location = 'listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Cancelar" /></td>
                                    <?php
                                } else {
                                    ?>
                                    <td><input type="button" onclick="location = window.location.search;" value="Cancelar" /></td>
                                    <?php
                                }
                                ?>

                                <td><input type="submit" value="Confirmar" /></td>
                            </tr>
                        </table>
                        <?php
                    } else {
                        ?>
                        <br>
                        <input type="button" onclick="location = window.location.search;" value="Volver" />
                        <?php
                    }
                    ?>
                </form>
            </div>
        </fieldset>
        <?php
    }
} else {
    echo "Hay un error";
    ?>
    <input type="button" onclick="location = window.location.search;" value="Volver" />
    <?php
}
?>