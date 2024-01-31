<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';

if (isset($_POST)) {
    if ((isset($_POST['apellido']))) {
        $okey = false;
        if ($_POST['apellido'] != "") {
            $apellido = $_POST['apellido'];
            $nombre = $_POST['nombre'];
            $colegiado = obtenerColegiadoPorApellidoNombre($apellido, $nombre);
            $okey = true;
        } else {
            ?>
            <br>
            <span class="mensajeERROR">Se olvidó de cargar el apellido.</span>
            <br>
            <?php
        }
        if ($okey) {
            if (!$colegiado) {
                ?>
                <br>
                <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                <br>
                <?php
            } else {
                if ($colegiado->num_rows == 0) {
                    ?>
                    <br>
                    <span class="mensajeWARNING">No se encontraron colegiados con ese Apellido y Nombre.</span>
                    <br>
                    <?php
                } else {
                    ?>
                    <table>
                        <tr>
                            <td><b>Matrícula</b></td>
                            <td><b>Apellido</b></td>
                            <td><b>Nombres</b></td>

                            <?php
                            while ($row = $colegiado->fetch_assoc()) {
                                echo "<tr class='dbl_colegiado' id='" . $row['Matricula'] . "'>";
                                echo "<td><p>" . $row['Matricula'] . "</p></td>";
                                echo "<td><p>" . utf8_encode($row['Apellido']) . "</p></td>";
                                echo "<td><p>" . utf8_encode($row['Nombres']) . "</p></td>";
                                echo "</tr>";
                            }
                            ?>

                        </tr>
                    </table>
                    <?php
                    /*
                     * Al igual que en buscarColegiado, se realiza una comprobación con el parámetro
                     * pasado por GET, con el fin de almacenar en la variable $action, la ruta a la
                     * cual ir luego de seleccionar al Colegiado con doble click.
                     */

                    if (isset($_GET['me'])) {
                        switch ($_GET['me']) {
                            case 1: $action = "mesaEntradaFormMovimiento.php?action=A";
                                break;
                            /*
                            case 2: $action = "mesaEntradaFormEspecialidad.php?action=A";
                                break;
                            */
                            case 3: $action = "mesaEntradaFormNota.php?action=A";
                                break;
                            case 4: $action = "mesaEntradaFormConsultorio.php?action=A";
                                break;
                            case 5: $action = "mesaEntradaFormMatriculaJ.php?action=A";
                                break;
                            case 6: $action = "buscarPorMatricula.php?BoM=ok";
                                break;
                            case 7: $action = "mesaEntradaFormAutoprescripcion.php?action=A";
                                break;
                            case 9: $action = "mesaEntradaFormExtravio.php?action=A";
                                break;
                            case 10: $action = "mesaEntradaFormEntrega.php?action=A";
                                break;
                        }
                    } else {
                        $action = "mesaEntradaFormNota.php?action=A"; //AGREGAR ACTION = A
                    }
                    ?>
                    <script type="text/javascript">
                        $(function() {
                            $(".dbl_colegiado").dblclick(function() {
                                var post_url = "<?php echo $action ?>&matricula=" + $(this).attr("id");
                                $.ajax({
                                    type: 'GET',
                                    url: post_url,
                                    success: function(msg) {
                    <?php
                    if (isset($_GET['me'])) {
                        if ($_GET['me'] == 6) {
                            ?>
                                                window.location.replace(post_url);
                            <?php
                        } else {
                            ?>
                                                $("#page-wrap").fadeOut(1, function() {
                                                    $("#page-wrap").html(msg).fadeIn(2);
                                                });
                            <?php
                        }
                    } else {
                        ?>
                                            $("#page-wrap").fadeOut(1, function() {
                                                $("#page-wrap").html(msg).fadeIn(2);
                                            });
                        <?php
                    }
                    ?>
                                    }
                                });
                                $("#modal").dialog("close");
                            });
                        });
                    </script>
                    <?php
                }
            }
        }
    }
}
?>
       