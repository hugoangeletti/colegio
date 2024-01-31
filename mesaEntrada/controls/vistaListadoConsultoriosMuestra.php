<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../class/resultadoClass.php';
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['tipo'])) {
    $tipoConsultorio = $_GET['tipo'];

    if (isset($_GET['calle'])) {
        $calle = $_GET['calle'];
    } else {
        $calle = "";
    }

    switch ($tipoConsultorio) {
        case "T": $consultorios = obtenerConsultorios($calle);
            break;
        default : $consultorios = obtenerConsultorioPorTipo($tipoConsultorio, $calle);
            break;
    }
}

if (!$consultorios) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
    <br>
    <?php
} else {
    if ($consultorios->num_rows == 0) {
        ?>
        <br>
        <span class="mensajeWARNING">No se encontraron consultorios.</span>
        <br>
        <?php
    } else {
        $totalRegistros = $consultorios->num_rows;
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

            if (isset($tipoConsultorio)) {
                if (isset($_GET['calle'])) {
                    $calle = $_GET['calle'];
                } else {
                    $calle = "";
                }

                if ($tipoConsultorio == "T") {
                    $consultoriosLimitado = obtenerConsultoriosLimitado($offset, $porPagina, $calle);
                } else {
                    $consultoriosLimitado = obtenerConsultorioPorTipoLimitado($tipoConsultorio, $offset, $porPagina, $calle);
                }
            }
            ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('.paginar').click(function() {
                        var page = $(this).attr('data');
                        var dataString = '&page=' + page + '&tipo=<?php echo $tipoConsultorio ?>';
                        $.ajax({
                            type: "GET",
                            url: "vistaListadoConsultoriosMuestra.php",
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
                        window.location.replace(href);
                    });
                });
                $(function() {
                    $(".borrar").click(function() {
                        if (confirm("¿Está seguro que desea dar de baja este consultorio?"))
                        {
                            var dataPost = $(this).attr("id");
                            console.log($.post("borrarModificarConsultorio.php", {idConsultorio: dataPost, tipoAccion: "B", func: "getEstadoAndTextoAndImporte"}, function(data) {
                                alert(data.texto);
                                location.reload();
                            }, "json"));
                        }
                    });
                });
            </script>
            <table class='tablaTabs'>
                <tr>
                    <td><h4>Tipo de Consultorio</h4></td>
                    <td><h4>Nombre</h4></td>
                    <td><h4>Calle</h4></td>
                    <td><h4>Teléfono</h4></td>
                    <td><h4>Localidad</h4></td>
                    <td><h4>Fecha Ingreso</h4></td>
                    <td><h4>Editar</h4></td>
                    <td><h4>Borrar</h4></td>
                </tr>
                <?php
                while ($row = $consultoriosLimitado->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            switch ($row['TipoConsultorio']) {
                                case "P":
                                    echo "Policonsultorio";
                                    break;
                                case "I":
                                    echo "Institución";
                                    break;
                                case "U":
                                    echo "Único";
                                    break;
                            }
                            ?>
                        </td>
                        <td><?php echo $row['Nombre'] ?></td>
                        <td><?php echo $row['Calle'] . " Nº " . $row['Numero'] . " " . $row['Lateral'] ?></td>
                        <td><?php echo $row['Telefono'] ?></td>
                        <td><?php echo $row['NombreLocalidad'] ?></td>
                        <td><?php echo invertirFecha($row['FechaCarga']) ?></td>
                        <td><a class="borrar" id='<?php echo $row['IdConsultorio'] ?>'>Borrar</a></td>

                        <td><a class="editar" id='vistaConsultorioModificar.php?idConsultorio=<?php echo $row['IdConsultorio'] ?>&tipo=<?php echo $tipoConsultorio ?>&page=<?php echo $numPagina ?>'>Editar</a></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
            <?php
            if ($cantPaginas > 1) {
                ?>
                <div class="paginacion">
                    <?php
                    if ($numPagina != 1) {
                        ?>
                        <p><a class="paginar" data="<?php echo ($numPagina - 1); ?>">Anterior</a></p>
                        <?php
                    }
                    for ($i = 1; $i <= $cantPaginas; $i++) {
                        if ($numPagina == $i) {
                            //si muestro el índice de la página actual, no coloco enlace
                            ?>
                            <p class="activado"><a><?php echo $i; ?></a></p>
                            <?php
                        } else {
                            //si el índice no corresponde con la página mostrada actualmente,
                            //coloco el enlace para ir a esa página
                            ?>
                            <p><a class="paginar" data="<?php echo $i; ?>"><?php echo $i; ?></a></p>
                            <?php
                        }
                    }
                    if ($numPagina != $cantPaginas) {
                        ?>
                        <p><a class="paginar" data="<?php echo ($numPagina + 1); ?>">Siguiente</a></p>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>

        <?php
    }
}
?>
    