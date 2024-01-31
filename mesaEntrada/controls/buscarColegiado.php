<?php
require_once 'seguridad.php';
?>
<script type="text/javascript">

    $(function() {
        $('#buscarColegiado').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function(msg) {
<?php
if (isset($_GET['me'])) {
    if ($_GET['me'] == 6) {
        ?>
                            window.location.replace(post_url + "&" + post_data);
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
        });
    });

    $(function() {
        $('#buscarPorNombre').submit(function(e) {
            e.preventDefault();
            $("#modal").show();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function(msg) {
                    $("#modal").html(msg);
                }
            });

            $("#modal").dialog({
                closeText: "cerrar",
                modal: true,
                width: 900,
                maxHeight: 400,
                maxWidth: 1000,
                resizable: true,
                title: "Búsqueda de Colegiado (para seleccionar debe hacer doble click)"
            });
        });
    });

    $(function() {
        $(".dbl_colegiado").dblclick(function() {
            var form = $("#buscarColegiado");
            var post_url = form.attr('action');
            var post_data = form.serialize();
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function(msg) {
                    $("#page-wrap").fadeOut(1, function() {
                        $("#page-wrap").html(msg).fadeIn(2);
                    });
                }
            });
        });
    });

    function volver() {
        $("#page-wrap").load('buscarColegiado.php');
    }

</script>
<?php
//Recibo por GET un ME que me determina el tipo de Mesa de Entrada
//para especificarle al formulario de búsqueda a qué PHP apuntar.
// 1 -> mesaEntradaFormMovimiento
// 2 -> mesaEntradaFormEspecialidad
// 3 -> mesaEntradaFormNota
// 4 -> mesaEntradaFormConsultorio
// y si no está declarado el GET es porque no ingresa desde colegiado,
// por lo tanto es una Nota y es como si se activara el 3.
if (isset($_GET['me'])) {
    switch ($_GET['me']) {
        case 1: $action = "mesaEntradaFormMovimiento.php?action=A";
            $codigoMe = 1;
            //$titulo = "Movimiento Matricular";
            break;
        /*
        case 2: $action = "mesaEntradaFormEspecialidad.php?action=A";
            $codigoMe = 2;
            //$titulo = "Especialidad";
            break;
        */
        case 3: $action = "mesaEntradaFormNota.php?action=A";
            $codigoMe = 3;
            //$titulo = "Notas/Oficio";
            break;
        case 4: $action = "mesaEntradaFormConsultorio.php?action=A";
            $codigoMe = 4;
            break;
        case 5: $action = "mesaEntradaFormMatriculaJ.php?action=A";
            $codigoMe = 5;
            break;
        case 6: $action = "buscarPorMatricula.php?BoM=ok";
            $codigoMe = 6;
            break;
        case 7: $action = "mesaEntradaFormAutoprescripcion.php?action=A";
            $codigoMe = 7;
            break;
        case 9: $action = "mesaEntradaFormExtravio.php?action=A";
            $codigoMe = 9;
            break;
        case 10: $action = "mesaEntradaFormEntrega.php?action=A";
            $codigoMe = 10;
            break;
    }
} else {
    $action = "mesaEntradaFormNota.php?action=A";
    $codigoMe = 3;
    //$titulo = "Notas/Oficio";
}
?>
<div id="filtros">
    <fieldset>
        <legend>Búsqueda de Matriculado</legend>
        <table>
            <tr>
                <td>
                    <form id="buscarColegiado" action="<?php echo $action ?>" method="post">
                        <fieldset class='porMatricula'>
                            <legend>Buscar por Matrícula</legend>
                            <table>
                                <tr>
                                    <td>Matrícula:</td>
                                    <td><input name="matricula" type="text" placeholder="Ingrese la Matrícula" /></td>
                                    <td></td>
                                    <!-- 
                                        El botón EXAMINAR debería mostrar un popup
                                        con todos los colegiados para la posible selección
                                    -->
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><input type="submit" value="Buscar"/></td>
                                </tr>
                            </table>
                        </fieldset>
                    </form>
                </td>
                <?php
// El $codigoMe determina al igual que el GET['me'], el tipo de Mesa
// Entrada que se va a realizar, y se lo pasa a listadoColegiados.
// Esto, para que sepa dónde redirigir una vez que selecciones por 
// doble click al colegiado.
                ?>
                <td>
                    <form id="buscarPorNombre" action="listadoColegiados.php?me=<?php echo $codigoMe ?>" method="post">
                        <fieldset class='porApellido'>
                            <legend>Buscar por Apellido y Nombre</legend>
                            <table>
                                <tr>
                                    <td>Apellido:</td>
                                    <td><input name="apellido" type="text" placeholder="Ingrese Apellido"/></td>
                                </tr>
                                <tr>
                                    <td>Nombre:</td>
                                    <td><input name="nombre" type="text" placeholder="Ingrese Nombre"/></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="submit" value="Buscar" /></td>
                                </tr>
                            </table>
                        </fieldset>
                    </form>
                </td>
            </tr>
        </table>

        <?php
        if (isset($_GET)) {
            ?>
            <input type="button" onclick="location = 'administracion.php'" value="Cancelar" />
            <?php
        }
        ?>
        <div id="modal" style="display:none"></div>
    </fieldset>

</div>
