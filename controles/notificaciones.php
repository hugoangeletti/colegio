<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/notificacionLogic.php');
?>
<script>
$(document).ready(
    function () {
                $('#tablaNotificaciones').DataTable({
                    "iDisplayLength":25,
                     "order": [[ 0, "desc" ], [ 1, "asc"]],
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    "bLengthChange": true,
                    "bFilter": true,
                    dom: 'T<"clear">lfrtip'
                });
    }
);

function confirmaAnular()
{
    if(confirm('¿Estas seguro de ANULAR este RECIBO?'))
        return true;
    else
        return false;
}
</script>
<?php
if (isset($_POST['mensaje'])) {
?>
   <div class="ocultarMensaje"> 
       <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
   </div>
<?php
}
$continua = TRUE;
$mensaje = '';
if (isset($_POST['anio'])) {
    $anio = $_POST['anio'];
} else {
    $anio = date('Y');
}
?>
<div class="panel panel-info">
<div class="panel-heading">
    <h4>
        <b>Notificaciones de deuda</b>
    </h4>
</div>
<?php
if ($continua) {
?>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="notificaciones.php">
                    <div class="col-xs-3">
                        <b>Año</b>
                        <select class="form-control" id="anio" name="anio" onChange='this.form.submit()'>
                            <?php 
                            $anioSelect = date('Y');
                            while ($anioSelect >= 2011) {
                            ?>
                                <option value="<?php echo $anioSelect; ?>" <?php if($anio == $anioSelect) { echo 'selected'; } ?>><?php echo $anioSelect; ?></option>
                            <?php
                                $anioSelect--;
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
                <br>
                <form method="POST" action="notificaciones_generar_form.php">
                    <button type="submit" class="btn btn-success">Generar notificación de deuda</button>
                </form>
            </div>
        </div>
        <br>
        <?php
        $resNotificaciones = obtenerNotificaciones(1, $anio);
        if ($resNotificaciones['estado']) {
        ?>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <table  id="tablaNotificaciones" class="display">
                        <thead>
                            <tr>
                                <th style="display: none;">Id</th>
                                <th style="text-align: center;">Fecha</th>
                                <!--<th style="text-align: center;">Tema</th>-->
                                <th style="text-align: center;">Matricula</th>
                                <th style="text-align: left;">Apellido y Nombre</th>
                                <th style="text-align: center;">Estado</th>
                                <th style="text-align: center;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($resNotificaciones['datos'] as $dato){
                                $idNotificacion = $dato['idNotificacion'];
                                $idNotificacionNota = $dato['idNotificacionNota'];
                                $temaNotificacion = $dato['temaNotificacion'];
                                $fecha = $dato['fechaCreacion'];
                                $matricula = $dato['matricula'];
                                if (isset($matricula) && $matricula <> "") {
                                    $apellidoNombre = $dato['apellidoNombre'];
                                    $cantidadMatriculasConDeuda = NULL;
                                } else {
                                    $cantidadMatriculasConDeuda = $dato['cantidadMatriculasConDeuda'];
                                    $apellidoNombre = "<b>PADRON COMPLETO (Cantidad matriculas con deuda: ".$cantidadMatriculasConDeuda.")</b>";
                                }
                                $estado = $dato['estado'];
                                if (isset($estado)) {
                                    if ($estado == 'B') {
                                        $estadoDetalle = 'ANULADO';
                                        $style = 'color: red;';
                                    } else {
                                        $estadoDetalle = 'OK';
                                        $style = '';
                                    }
                                }
                                ?>
                                <tr>
                                    <td style="display: none;"><?php echo $idNotificacion; ?></td>
                                    <td style="text-align: center;"><?php echo cambiarFechaFormatoParaMostrar($fecha);?></td>
                                    <!--<td style="text-align: center;"><?php echo $temaNotificacion?></td>-->
                                    <td style="text-align: center;"><?php echo $matricula?></td>
                                    <td style="text-align: left;"><?php echo $apellidoNombre;?></td>
                                    <td style="text-align: center; <?php echo $style; ?>"><?php echo $estadoDetalle;?></td>
                                    <td>
                                        <a href="notificaciones_detalle.php?id=<?php echo $idNotificacion; ?>" class="btn btn-primary">Ver Detalle</a>
                                        <a href="notificaciones_imprimir.php?id=<?php echo $idNotificacion; ?>" class="btn btn-primary" >Imprimir</a>
                                        <?php 
                                        if ($estado == 'A') {
                                        ?>
                                            <a href="datosNotificacion/anular_notificacion.php?id=<?php echo $idNotificacion; ?>" class="btn btn-danger" onclick="return confirmaAnular()">Anular</a>
                                        <?php 
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="row">&nbsp;</div>
            <div class="<?php echo $resNotificaciones['clase']; ?>" role="alert">
                <span class="<?php echo $resNotificaciones['icono']; ?>" aria-hidden="true"></span>
                <span><strong><?php echo $resNotificaciones['mensaje']; ?></strong></span>
            </div>        
        <?php        
        }
        ?>
    </div>
<?php
} else {
?>
    <div class="row">&nbsp;</div>
    <div class="alert alert-danger" role="alert">
        <span><strong><?php echo $mensaje; ?></strong></span>
    </div>        
<?php
}
?>
</div>
<?php
require_once '../html/footer.php';
