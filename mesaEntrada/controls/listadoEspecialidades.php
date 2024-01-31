<?php
// Se encarga de mostrar todo el listado de remitentes que se encuentran
// en la BD, para que el usuario elija desde el popup.

require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_POST)) {
    if ((isset($_POST['nombre']))) {
        $okey = false;
        if ($_POST['nombre'] != "") {
            $nombre = $_POST['nombre'];
            if(isset($_GET['mC'])) {
                $matricula = $_GET['mC'];
            } else {
                $matricula = NULL;
            }
            /*
              if(isset($_GET['tE']))
              {
              $idTipoEspecialidad = "";
              $especialidades = obtenerEspecialidadesPorNombrePorTipo(trim($nombre), $idTipoEspecialidad);
              }
              else
              {
             * 
             */
            $especialidades = obtenerEspecialidadesPorNombre(trim($nombre), $matricula);
            //}
            $okey = true;
        } else {
            ?>
            <br>
            <span class="mensajeERROR">Se olvidó de cargar el nombre.</span>
            <br>
            <?php
        }
        if ($okey) {
            if (!$especialidades) {
                ?>
                <br>
                <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                <br>
                <?php
            } else {
                if ($especialidades->num_rows == 0) {
                    ?>
                    <br>
                    <span class="mensajeWARNING">No se encontraron especialidades con ese nombre.</span>
                    <br>
                    <?php
                } else {
                    ?>

                    <table>
                        <tr>
                            <td><b>Nombre</b></td>
                            <td style="padding-left: 50px;"><b>Nombre Especialidad Cabecera</b></td>

                    <?php
                    while ($row = $especialidades->fetch_assoc()) {
                        ?>
                            <tr class='dbl_especialidad' id='<?php echo $row['idEspecialidad']; ?>' data='<?php echo $row['IdTipoEspecialidad'] ?>'>
                                <td><p><?php echo utf8_encode($row['NombreEspecialidad']); ?></p></td>
                        <?php
                        if (!is_null($row['NombreDependiente'])) {
                            ?>
                                    <td style="padding-left: 50px;"><p><?php echo utf8_encode($row['NombreDependiente']) ?></p></td>
                            <?php
                        } else {
                            $nombreEspecialidadCabecera = obtenerNombreEspecialidadCabeceraPorIdEspecialidad($row['idEspecialidad']);

                            if (!is_null($nombreEspecialidadCabecera)) {
                                $datoEspecialidad = $nombreEspecialidadCabecera->fetch_assoc();
                            } else {
                                $datoEspecialidad = null;
                            }

                            if (!is_null($datoEspecialidad)) {
                                ?>
                                        <td style="padding-left: 50px;"><p><?php echo utf8_encode($datoEspecialidad['NombreEspecialidad']) ?></p></td>
                                        <?php
                                    }
                                }
                                ?>
                            </tr>
                                <?php
                            }
                            ?>

                    </tr>
                    </table>
                            <?php
                            if (isset($_GET['mC'])) {
                                $param = "&action=A&matricula=" . $_GET['mC'];
                            } else {
                                $param = "";
                            }
                            ?>
                    <script type="text/javascript">
                        $(function() {
                            $(".dbl_especialidad").dblclick(function() {
                                var post_url = "formEspecialidad.php?especialidad=" + $(this).attr("id") + "&tE=" + $(this).attr("data") + "<?php echo $param ?>";
                                $.ajax({
                                    type: 'GET',
                                    url: post_url,
                                    success: function(msg) {
                                        $("#page-wrap").fadeOut(1, function() {
                                            $("#page-wrap").html(msg).fadeIn(2);
                                        });
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
       