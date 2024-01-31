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
    if (isset($_GET['remitente'])) {
        $remitente = $_GET['remitente'];
    } else {
        $remitente = 0;
    }

    switch ($_GET['st']) {
        case 4: $notasRemitente = obtenerNotasPorRemitente($remitente);
            $st = 3;
            break;
    }
}

if (!$notasRemitente) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
    <br>
    <?php
} else {
    if ($notasRemitente->num_rows == 0) {
        ?>
        <br>
        <span class="mensajeWARNING">No se encontraron eventos para este colegiado.</span>
        <br>
        <?php
    } else {
        $totalRegistros = $notasRemitente->num_rows;
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
                $notasFechaLimitado = obtenerNotasPorRemitenteLimitado($remitente, $offset, $porPagina);
            }
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.paginar').click(function() {
                        var page = $(this).attr('data');
                        var dataString = '&page=' + page + '&remitente=<?php echo $remitente ?>&st=<?php echo $_GET['st'] ?>';

                        $.ajax({
                            type: "GET",
                            url: "mostrarListadoPorRemitente.php",
                            data: dataString,
                            success: function(data) {
                                $('#ui-tabs-' + ($("#tabs").tabs('option', 'active') + 1)).fadeIn(1000).html(data);
                            }
                        });
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
                        $.ajax({
                            success: function() {
                                window.open('hojaRuta.php?iME=' + id, '_blank');
                            }
                        });

                    });
                });
            </script>
            <table class='tablaTabs'>
                <tr>
                    <td><h4>Nº de Trámite</h4></td>
                    <td style="width: 100px;"><h4>Fecha</h4></td>
                    <td><h4>Tema</h4></td>
                    <td><h4>Ver</h4></td>
                    <td><h4>Imprimir</h4></td>
                    <td><h4>Realizó</h4></td>
                </tr>
            <?php
            if (!$notasFechaLimitado) {
                ?>
                    <tr>
                        <td colspan="5"><span class="mensajeERROR">Hubo un error en la base de datos.</span></td>
                    </tr>
                <?php
            } else {
                if ($notasFechaLimitado->num_rows == 0) {
                    ?>
                        <tr>
                            <td colspan="5"><span class="mensajeWARNING">No hay notas para este remitente.</span></td>
                        </tr>
                        <?php
                    } else {

                        while ($row = $notasFechaLimitado->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['IdMesaEntrada']; ?></td>
                                <td><?php echo invertirFecha($row['FechaIngreso']); ?></td>
                                <td><?php echo utf8_encode($row['Tema']); ?></td>
                                <!--/*echo "<td>".$row['IdRemitente']."</td>";
                                echo "<td>".utf8_encode($row['NombreRemitente'])."</td>";*/-->
                            <?php
                            $href = "mesaEntradaFormNota.php";
                            ?>
                                <td><a class="verTramite" id='<?php if (isset($href)) {
                            echo $href;
                        } ?>?action=V&iEvento=<?php echo $row['IdMesaEntrada'] ?>&rT=<?php echo $row['IdRemitente'] ?>'>Ver</a></td>
                                <td><a class="imprimir" id="<?php echo $row['IdMesaEntrada'] ?>" data="<?php echo $row['IdTipoMesaEntrada'] ?>">Imprimir</a></td>
                                <td><?php echo $row['Usuario'] ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
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
    