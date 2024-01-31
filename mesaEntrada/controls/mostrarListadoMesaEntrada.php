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
    if (isset($_GET['fecha'])) {
        $fechaActual = $_GET['fecha'];
    } else {
        $fechaActual = date("Y-m-d");
    }
    $tdEvento = "";
    switch ($_GET['st']) {
        case 1: $tdEvento = "<td><h4>Tipo de Trámite</h4></td>";
            $movimientosFecha = obtenerMovimientosPorFecha($fechaActual);
            $st = 0;
            break;
        case 2: $tdDetalle = "<td><h4>Tipo de Movimiento</h4></td>";
            $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "1");
            $st = 1;
            break;
        case 3: $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "2");
            $st = 2;
            break;
        case 4: $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "3");
            $st = 3;
            break;
        case 5: $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "4");
            $st = 4;
            break;
        case 6: $movimientosFecha = obtenerMatriculasJPorFecha($fechaActual);
            $st = 5;
            break;
        case 7: $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "7");
            $st = 7;
            break;
        case 9: $movimientosFecha = obtenerMovimientosPorFechaTipoMesa($fechaActual, "9");
            $st = 9;
            break;
    }
}

if (!$movimientosFecha) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
    <br>
    <?php
} else {
    if ($movimientosFecha->num_rows == 0) {
        ?>
        <br>
        <span class="mensajeWARNING">No se encontraron eventos en esta fecha.</span>
        <br>
        <?php
    } else {
        $totalRegistros = $movimientosFecha->num_rows;
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
                    $movimientosFechaLimitado = obtenerMovimientosPorFechaLimitado($fechaActual, $offset, $porPagina);
                } else {
                    $movimientosFechaLimitado = obtenerMovimientosPorFechaLimitadoTipoMesa($st, $fechaActual, $offset, $porPagina);
                }
            }
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.paginar').click(function() {
                        var page = $(this).attr('data');
                        var dataString = '&page=' + page + '&fecha=<?php echo $fechaActual ?>&st=<?php echo $_GET['st'] ?>';

                        $.ajax({
                            type: "GET",
                            url: "mostrarListadoMesaEntrada.php",
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
                    $(".borrar").click(function() {
                        var nTramite = $(this).attr("id");
                        if (confirm("¿Está seguro que desea dar de baja la habilitación de consultorio con Nº de Trámite " + nTramite + "?"))
                        {
                            var dataPost = $(this).attr("id");
                            $.post("borrarModificarHabilitacionConsultorio.php", {idMesaEntrada: dataPost, func: "getEstadoAndTextoAndImporte"}, function(data) {
                                alert(data.texto);
                                location.reload();
                            }, "json");
                        }
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
                                    //window.open('expediente.php?iME=' + id, '_blank');
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
                                                    //window.open('hojaRuta.php?iME=' + id, '_blank');
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
                    <td><h4>Matrícula</h4></td>
                    <td><h4>Remitente</h4></td>
                    <td><h4>Apellido y Nombre / Remitente</h4></td>
                    <?php echo $tdEvento; ?>
                    <?php
                    if (isset($tdDetalle)) {
                        echo $tdDetalle;
                    }
                    ?>
                    <td><h4>Ver</h4></td>
                    <td><h4>Editar</h4></td>
                    <td><h4>Borrar</h4></td>
                    <td><h4>Imprimir</h4></td>
                    <td><h4>Realizó</h4></td>
                    <?php
                    while ($row = $movimientosFechaLimitado->fetch_assoc()) {
                        ?>
                    <tr>
                        <?php
                        if (!is_null($row['IdColegiado'])) {
                            ?>
                            <td><?php echo $row['Matricula']; ?></td>
                            <td></td>
                            <td><?php echo utf8_encode($row['Apellido']) . " " . utf8_encode($row['Nombres']); ?></td>
                            <?php
                        } else {
                            echo "<td></td>";
                            echo "<td>" . $row['IdRemitente'] . "</td>";
                            echo "<td>" . utf8_encode($row['NombreRemitente']) . "</td>";
                        }
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
                        if (isset($tdDetalle) && ($tdDetalle != "")) {
                            if (!is_null($row['DetalleCompleto'])) {
                                echo "<td>" . utf8_encode($row['DetalleCompleto']) . "</td>";
                            }
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
                        if ($href != "#") {
                            if ($row['IdTipoMesaEntrada'] == 2) {
                                ?>
                                <td><a class="verTramite" id='verInfoFormEspecialidad.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>'>Ver</a></td>
                                <td></td>
                                <td><a class="editar" id='<?php
                                    if (isset($href)) {
                                        echo $href;
                                    }
                                    ?>?action=B&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>'>Borrar</a></td>
                                       <?php
                                   } else {
                                       ?>
                                <td><a class="verTramite" id='<?php
                                    if (isset($href)) {
                                        echo $href;
                                    }
                                    ?>?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>'>Ver</a></td>
                                       <?php
                                       if ($row['IdTipoMesaEntrada'] != 1) {
                                           ?>
                                    <td><a class="editar" id='<?php
                                        if (isset($href)) {
                                            echo $href;
                                        }
                                        ?>?action=M&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>'>Editar</a></td>
                                           <?php
                                       } else {
                                           ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                                <td><a class="editar" id='<?php
                                    if (isset($href)) {
                                        echo $href;
                                    }
                                    ?>?action=B&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>'>Borrar</a></td>
                                       <?php
                                   }
                               } else {
                                   if ($row['IdTipoMesaEntrada'] != 8) {
                                       ?>
                                <td><a class="verTramite" id="verInfoFormConsultorio.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>">Ver</a></td>
                                <td></td>
                                <td><a class="borrar" id='<?php echo $row['IdMesaEntrada'] ?>'>Borrar</a></td>
                                <?php
                            } else {
                                ?>
                                <td><a class="verTramite" id="verInfoFormAnulacion.php?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&fecha=<?php echo invertirFecha($fechaActual) ?>">Ver</a></td>
                                <td></td>
                                <td></td>
                                <?php
                            }
                        }
                        //if ($row['IdTipoMesaEntrada'] != 7) {
                        ?>
                        <td><a class="imprimir" id="<?php echo $row['IdMesaEntrada'] ?>" data="<?php echo $row['IdTipoMesaEntrada'] ?>">Imprimir</a></td>
                        <td><?php echo $row['Usuario'] ?></td>
                        <?php
                        //} else {
                        ?>
                        <!--<td></td>-->
                        <?php
                        //}
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
    