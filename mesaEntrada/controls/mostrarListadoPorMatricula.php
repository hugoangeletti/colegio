<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../class/resultadoClass.php';
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['st'])) {
    if (isset($_GET['matricula'])) {
        $matricula = $_GET['matricula'];
    }

    $consultaColegiado = obtenerColegiadoPorMatricula($matricula);

    if ($consultaColegiado) {
        if ($consultaColegiado->num_rows != 0) {
            $datoColegiado = $consultaColegiado->fetch_assoc();
        }
    } else {
        $datoColegiado['Id'] = 0;
    }



    $tdEvento = "";
    switch ($_GET['st']) {
        case 1: $tdEvento = "<td><h4>Tipo de Trámite</h4></td>";
            $movimientosColegiado = obtenerMovimientosPorIdColegiado($datoColegiado['Id']);
            $st = 0;
            break;
        case 2: $movimientosColegiado = obtenerMovimientosPorIdColegiadoTipoMesa($datoColegiado['Id'], "1");
            $st = 1;
            break;
        case 3: $movimientosColegiado = obtenerMovimientosPorIdColegiadoTipoMesa($datoColegiado['Id'], "2");
            $st = 2;
            break;
        case 4: $movimientosColegiado = obtenerMovimientosPorIdColegiadoTipoMesa($datoColegiado['Id'], "3");
            $st = 3;
            break;
        case 5: $movimientosColegiado = obtenerMovimientosPorIdColegiadoTipoMesa($datoColegiado['Id'], "4");
            $st = 4;
            break;
        case 9: $movimientosColegiado = obtenerMovimientosPorIdColegiadoTipoMesa($datoColegiado['Id'], "9");
            $st = 9;
            break;
    }
}

if (!$movimientosColegiado) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
    <br>
    <?php
} else {
    if ($movimientosColegiado->num_rows == 0) {
        ?>
        <br>
        <span class="mensajeWARNING">No se encontraron eventos para este colegiado.</span>
        <br>
        <?php
    } else {
        $totalRegistros = $movimientosColegiado->num_rows;
        //Si hay registros
        if ($totalRegistros > 0) {
            //numero de registros por página
            $porPagina = 10;

            //por defecto mostramos la página 1
            $numPagina = 1;

            // si $_GET['page'] esta definido, usamos este número de página
            if (isset($_GET['page'])) {
                sleep(1);
                $numPagina = $_GET['page'];
            }

            //contando el desplazamiento
            $offset = ($numPagina - 1) * $porPagina;
            $cantPaginas = ceil($totalRegistros / $porPagina);

            if (isset($st)) {
                if ($st == 0) {
                    $movimientosFechaLimitado = obtenerMovimientosPorIdColegiadoLimitado($datoColegiado['Id'], $offset, $porPagina);
                } else {
                    $movimientosFechaLimitado = obtenerMovimientosPorIdColegiadoLimitadoTipoMesa($st, $datoColegiado['Id'], $offset, $porPagina);
                }
            }
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.paginar').click(function() {
                        var page = $(this).attr('data');
                        var dataString = '&page=' + page + '&matricula=<?php echo $matricula ?>&st=<?php echo $_GET['st'] ?>';

                        $.ajax({
                            type: "GET",
                            url: "mostrarListadoPorMatricula.php",
                            data: dataString,
                            success: function(data) {
                                $('#ui-tabs-' + ($("#tabs").tabs('option', 'active') + 1)).fadeIn(1000).html(data);
                            }
                        });
                    });
                });
                $(function() {
                    $(".editar").click(function() {
                        var href = $(this).attr("id");
                        $("#page-wrap").load(href);
                    });
                });
                $(function() {
                    $(".verTramite").click(function() {
                        var href = $(this).attr("id");
                        $("#page-wrap").load(href);
                    });
                });

                $(function() {
                    $(".imprimir").click(function() {
                        var id = $(this).attr("id");
                        var tipoMesaEntrada = $(this).attr("data");
                        $.ajax({
                            success: function() {
                                if (tipoMesaEntrada == 2)
                                {
                                    window.open('expediente.php?iME=' + id, '_blank');
                                }
                                else
                                {
                                    if (tipoMesaEntrada == 4)
                                    {
                                        window.open('imprimirHabilitacionConsultorio.php?iME=' + id, '_blank');
                                    }
                                    else
                                    {
                                        if (tipoMesaEntrada == 1)
                                        {
                                            window.open('hojaRutaMovimiento.php?iME=' + id, '_blank');
                                        }
                                        else
                                        {
                                            if (tipoMesaEntrada == 5)
                                            {
                                                window.open('hojaRutaMatriculaJ.php?iME=' + id, '_blank');
                                            }
                                            else
                                            {
                                                if (tipoMesaEntrada == 7)
                                                {
                                                    window.open('hojaRutaAutoprescripcion.php?iME=' + id, '_blank');
                                                }
                                                else
                                                {
                                                    if (tipoMesaEntrada == 8)
                                                    {
                                                        window.open('hojaRutaAnulacion.php?iME=' + id, '_blank');
                                                    }
                                                    else
                                                    {
                                                        if (tipoMesaEntrada == 9)
                                                        {
                                                            window.open('hojaRutaExtravio.php?iME=' + id, '_blank');
                                                        }
                                                        else
                                                        {
                                                            if (tipoMesaEntrada == 10)
                                                            {
                                                                window.open('hojaRutaEntrega.php?iME=' + id, '_blank');
                                                            }
                                                            else
                                                            {
                                                                window.open('hojaRuta.php?iME=' + id, '_blank');
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        });

                    });
                });
            </script>
            <table class='tablaTabs'>
                <tr>
                    <td><h4>Nº de Trámite</h4></td>
                    <td><h4>Fecha</h4></td>
                    <!--<td><h4>Matrícula / Remitente</h4></td>
                    <td><h4>Apellido y Nombre / Remitente</h4></td>-->
                    <?php echo $tdEvento; ?>
                    <?php
                    switch ($st) {
                        case 1:
                            ?>
                            <td><h4>Tipo Movimiento</h4></td>
                            <td><h4>Motivo Movimiento</h4></td>
                            <td><h4>Anular Movimiento</h4></td>
                            <?php
                            break;
                        case 2:
                            ?>
                            <td><h4>Especialidad</h4></td>
                            <td><h4>Tipo de Trámite</h4></td>
                            <?php
                            break;
                        case 3:
                            ?>
                            <td><h4>Tema</h4></td>
                            <?php
                            break;
                        case 4:
                            ?>
                            <td><h4>Nombre Consultorio</h4></td>
                            <td><h4>Tipo Consultorio</h4></td>
                            <td><h4>Calle</h4></td>
                            <td><h4>Especialidad</h4></td>
                            <?php
                            break;
                        case 9:
                            ?>
                            <td><h4>Tipo Denuncia</h4></td>
                            <td><h4>Fecha Denuncia</h4></td>
                            <td><h4>Fecha Extravío</h4></td>
                            <?php
                            break;
                    }
                    ?>
                    <td><h4>Ver</h4></td>
                    <td><h4>Imprimir</h4></td>
                    <td><h4>Realizó</h4></td>
                    <?php
                    while ($row = $movimientosFechaLimitado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['IdMesaEntrada'] . "</td>";
                        ?>
                        <td><?php echo invertirFecha($row['FechaIngreso']) ?></td>
                        <?php
                        /* if(!is_null($row['IdColegiado']))
                          {
                          echo "<td>".$row['Matricula']."</td>";
                          echo "<td>".$row['Apellido']." ".$row['Nombres']."</td>";
                          }
                          else
                          {
                          echo "<td>".$row['IdRemitente']."</td>";
                          echo "<td>".$row['NombreRemitente']."</td>";
                          } */
                        if ($tdEvento != "") {
                            echo "<td>" . utf8_encode($row['NombreMovimiento']);
                            if (!is_null($row['DetalleCompleto'])) {
                                echo " (" . utf8_encode($row['DetalleCompleto']) . ")";
                            }
                            if (!is_null($row['NombreDenuncia'])) {
                                echo " (" . utf8_encode($row['NombreDenuncia']) . ")";
                            }
                            if (!is_null($row['NombreEntrega'])) {
                                echo " (" . utf8_encode($row['NombreEntrega']) . ")";
                            }
                            echo "</td>";
                        }
                        switch ($row['IdTipoMesaEntrada']) {
                            case 1: $href = "mesaEntradaFormMovimiento.php";
                                break;
                            case 2: $href = "formEspecialidad.php";
                                break;
                            case 3: $href = "mesaEntradaFormNota.php";
                                break;
                            case 4: $href = "#";
                                break;
                            case 5: $href = "mesaEntradaFormMatriculaJ.php";
                                break;
                            case 7: $href = "mesaEntradaFormAutoprescripcion.php";
                                break;
                            case 8: $href = "#";
                                break;
                            case 9: $href = "mesaEntradaFormExtravio.php";
                                break;
                            case 10: $href = "mesaEntradaFormEntrega.php";
                                break;
                        }

                        switch ($st) {
                            case 1:
                                ?>
                                <td><?php echo utf8_encode($row['DetalleCompleto']) ?></td>
                                <td><?php echo utf8_encode($row['nombreCancelacion']) ?></td>

                                <?php
                                $esUltimoMovimiento = obtenerEsUltimoMovimientoPorIdColegiado($row['IdColegiado'], $row['IdMesaEntrada']);
                                $yaAnulo = obtenerAnuladoPorIdMesaEntrada($row['IdMesaEntrada']);

                                if ($esUltimoMovimiento && !$yaAnulo) {
                                    $noPuedeAnular = true;
                                    ?>
                                    <td><a class="verTramite" id='mesaEntradaFormAnulacionMovimiento.php?action=A&iEvento=<?php echo $row['IdMesaEntrada'] ?>&mT=<?php echo $matricula ?>'>Anular</a></td>
                                    <?php
                                } else {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                break;
                            case 2:
                                ?>
                                <td><?php echo utf8_encode($row['nombreEspecialidad']) ?></td>
                                <td><?php echo utf8_encode($row['TipoEspecialidad']) ?></td>
                                <?php
                                break;
                            case 3:
                                ?>
                                <td><?php echo utf8_encode($row['Tema']) ?></td>
                                <?php
                                break;
                            case 4:
                                ?>
                                <td><?php echo utf8_encode($row['nombreConsultorio']) ?></td>
                                <td><?php
                                    switch ($row['TipoConsultorio']) {
                                        case "P":
                                            echo "POLICONSULTORIO";
                                            break;
                                        case "U":
                                            echo "ÚNICO";
                                            break;
                                        case "I":
                                            echo "INSTITUCIÓN";
                                            break;
                                    }
                                    ?></td>
                                <td><?php echo $row['Calle'] . " " . $row['Lateral'] . " Nº " . $row['Numero'] ?></td>
                                <td><?php echo utf8_encode($row['nombreEspecialidad']) ?></td>
                                <?php
                                break;
                            case 9:
                                ?>
                                <td><?php echo utf8_encode($row['NombreTipoDenuncia']) ?></td>
                                <td><?php echo invertirFecha($row['FechaDenuncia']) ?></td>
                                <td><?php echo invertirFecha($row['FechaExtravio']) ?></td>
                                <?php
                                break;
                        }
                        ?>
                        <?php
                        if ($href != "#") {
                            if ($row['IdTipoMesaEntrada'] == 2) {
                                ?>
                                <td><a class="verTramite" id='verInfoFormEspecialidad.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&mT=<?php echo $matricula ?>'>Ver</a></td>
                                <?php
                            } else {
                                ?>
                                <td><a class="verTramite" id='<?php
                                    if (isset($href)) {
                                        echo $href;
                                    }
                                    ?>?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&mT=<?php echo $matricula ?>'>Ver</a></td>
                                       <?php
                                   }
                               } else {
                                   if ($row['IdTipoMesaEntrada'] != 8) {
                                       ?>
                                <td><a class="verTramite" id="verInfoFormConsultorio.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&mT=<?php echo $matricula ?>">Ver</a></td>
                                <?php
                            } else {
                                ?>
                                <td><a class="verTramite" id="verInfoFormAnulacion.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&mT=<?php echo $matricula ?>">Ver</a></td>
                                <?php
                            }
                        }
                        ?>
                        <td><a class="imprimir" id="<?php echo $row['IdMesaEntrada'] ?>" data="<?php echo $row['IdTipoMesaEntrada'] ?>">Imprimir</a></td>
                        <td><?php echo $row['Usuario'] ?></td>
                        <?php
                        echo "</tr>";
                    }
                    ?>
                </tr>
            </table>
            <?php
            if ($cantPaginas > 1) {
                echo '<div class="paginacion">';
                if ($numPagina != 1) {
                    echo '<p><a class="paginar" data="' . ($numPagina - 1) . '">Anterior</a></p>';
                }
                for ($i = 1; $i <= $cantPaginas; $i++) {
                    if ($numPagina == $i) {
                        //si muestro el índice de la página actual, no coloco enlace
                        echo '<p class="activado"><a>' . $i . '</a></p>';
                    } else {
                        //si el índice no corresponde con la página mostrada actualmente,
                        //coloco el enlace para ir a esa página
                        echo '<p><a class="paginar" data="' . $i . '">' . $i . '</a></p>';
                    }
                }
                if ($numPagina != $cantPaginas)
                    echo '<p><a class="paginar" data="' . ($numPagina + 1) . '">Siguiente</a></p>';
                echo '</div>';
            }
        }
        ?>

        <?php
    }
}
?>
    