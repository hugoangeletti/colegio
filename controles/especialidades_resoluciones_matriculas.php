<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/eleccionesLogic.php');
require_once ('../dataAccess/resolucionesLogic.php');
?>
<script>
    $(document).ready(function () {
        $('#tablaOrdenada').DataTable({
            "iDisplayLength":10,
            "order": [[ 1, "asc" ], [ 0, "asc"]],
            "language": {
                "url": "../public/lang/esp.lang"
            },
            dom: 'T<"clear">lfrtip',
            tableTools: {
               "sSwfPath": "../public/swf/copy_csv_xls_pdf.swf", 
               "aButtons": [
                    {
                        "sExtends": "pdf",
                        "mColumns" : [0, 1, 2, 3, 4],
//                                "oSelectorOpts": {
//                                    page: 'current'
//                                }
                        "sTitle": "Listado de Elecciones",
                        "sPdfOrientation": "portrait",
                        "sFileName": "listado_de_elecciones.pdf"
//                              "sPdfOrientation": "landscape",
//                              "sPdfSize": "letter",  ('A[3-4]', 'letter', 'legal' or 'tabloid')
                    }

            ]
            }
        });
    });
            
    function confirmar()
    {
        if(confirm('¿Estas seguro de abrir la resolución?'))
            return true;
        else
            return false;
    }

    function confirmarCierre()
    {
        if(confirm('¿Estas seguro de cerrar la resolución?'))
            return true;
        else
            return false;
    }

</script>

<?php
$continua = TRUE;
$titulo = 'Matrículas de la Resolución ';
$fechaInicioCodigoQR = '2023-05-01';

if (isset($_GET['idResolucion'])) {
    $idResolucion = $_GET['idResolucion'];
    
    //obtengo la resolucion
    $resResolucion = obtenerResolucionPorId($idResolucion);
    if ($resResolucion['estado']) {
        $resolucion = $resResolucion['datos'];
        $estadoResoluciones = $resolucion['estado'];
        $anioResoluciones = date("Y",strtotime($resolucion['fecha']));
        $estadoResolucion = $resolucion['estado'];
        $tipoEspecialistaResolucion = $resolucion['tipoEspecialista'];
        $detalleTipoResolucion = $resolucion['detalleTipoResolucion'];
        $fechaResolucion = $resolucion['fecha'];
        switch ($estadoResolucion) {
            case "A":
                $estadoResolucionTitulo = " - Estado: ABIERTA";
                break;

            case "C":
                $estadoResolucionTitulo = " - Estado: CERRADA";
                break;

            case "E":
                $estadoResolucionTitulo = " - Estado: ENVIADA A CONSEJO";
                break;

            default:
                $estadoResolucionTitulo = "";
                break;
        }
        $titulo .= 'Nº '.$resolucion['numero'].' de fecha '.  cambiarFechaFormatoParaMostrar($fechaResolucion).' - '.$detalleTipoResolucion.$estadoResolucionTitulo;
    }
} else {    
    $continua = FALSE;
}

if (isset($_POST['mensaje']))
{
 ?>
   <div class="ocultarMensaje"> 
   <p class="<?php echo $_POST['tipomensaje'];?>"><?php echo $_POST['mensaje'];?></p>  
   </div>
 <?php    
}   
?> 
<div class="panel panel-default">
<div class="panel-heading"><h4><b><?php echo $titulo; ?> </b></h4></div>
<div class="panel-body">
    <div class="row">
        <?php
        if ($estadoResolucion == "A") {
        ?>
        <div class="col-xs-9">&nbsp;</div>
        <div class="col-xs-3 text-right">
            <a href="especialidades_resoluciones_matricula_form.php?idResolucion=<?php echo $idResolucion; ?>&estado=<?php echo $estadoResoluciones; ?>&accion=1&anio=<?php echo $anioResoluciones ?>" class="btn btn-success btn-lg">
                Nueva Matrícula</a>
        </div>
        <?php 
        }
        ?>
    </div>
    <div class="row">&nbsp;</div>
    <?php
    $resMatriculas = obtenerMatriculasPorIdResolucion($idResolucion);   
    //var_dump($resMatriculas); exit;
    if ($resMatriculas['estado']){
    ?>
        <div class="row">
            <table id="tablaOrdenada" class="display table-responsive">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Matrícula</th>
                        <th>Apellido y Nombre</th>
                        <th>Especialidad</th>
                        <th>Tipo</th>
                        <?php if ($tipoEspecialistaResolucion == 'R') { ?> 
                            <th>Fecha Recertificación</th>
                        <?php } ?>
                        <th>Estado</th>
                        <th style="width: 30px">Eliminar</th>
                        <?php if ($fechaResolucion >= $fechaInicioCodigoQR) { ?> 
                            <th style="width: 30px">QR</th>
                        <?php } ?>
                    </tr>
                </thead>
          <tbody>
              <?php
              foreach ($resMatriculas['datos'] as $dato) 
              {
                  $idResolucionDetalle = $dato['idResolucionDetalle'];
                  $matricula = $dato['matricula'];
                  $apellidoNombre = trim($dato['apellido'].' '.trim($dato['nombre']));
                  $especialidad = $dato['especialidad'];
                  $tipoInciso = $dato['nombreTipoEspecialista'].' '.$dato['inciso'];
                  $fecha = $dato['fechaRecertificacion'];
                  $estado = $dato['estado'];
                  $hash_qr = $dato['hash_qr'];
                  if (isset($dato['idColegiadoEspecialista']) && $dato['idColegiadoEspecialista'] <> "") {
                    $idColegiadoEspecialista = $dato['idColegiadoEspecialista'];
                  } else {
                    $idColegiadoEspecialista = $dato['idColegiadoEspecialistaPorRecertificacion'];
                  }
                  $idTipoEspecialista = $dato['idTipoEspecialista'];
                  //var_dump($dato);
              ?>
                <tr>
                	<td><?php echo $idResolucionDetalle;?></td>
        			<td><?php echo $matricula;?></td>
        			<td><?php echo $apellidoNombre;?></td>
        			<td><?php echo $especialidad;?></td>
        			<td><?php echo $tipoInciso;?></td>
                    <?php 
                    if ($tipoEspecialistaResolucion == 'R') { ?> 
                        <td><?php echo cambiarFechaFormatoParaMostrar($fecha);?></td>
                    <?php
                    } ?>
                    <td><?php echo $estado; ?></td>
                    <td style="text-align: center;"><a href="especialidades_resolucion_matricula_borrar.php?id=<?php echo $idResolucionDetalle; ?>&idResolucion=<?php echo $idResolucion; ?>" 
                                       class="btn btn-danger glyphicon glyphicon-erase btn-sm" onclick="return confirmar()"></a>
                        <!--<a href="especialidades_resoluciones_matricula_form.php?idResolucion=<?php echo $idResolucion; ?>&estado=<?php echo $estadoResoluciones; ?>&accion=3&anio=<?php echo $anioResoluciones ?>&id=<?php echo $idResolucionDetalle; ?>" class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></a>-->
                    </td>
                    <?php if ($fechaResolucion >= $fechaInicioCodigoQR) { ?>
                    <td style="text-align: center;">
                        <?php 
                        if (isset($hash_qr)) {
                            echo 'QR generado';
                        } else {
                        ?>
                            <!--<a href="datosTituloEspecialista/imprimir_titulo_especialista_colegio.php?id=<?php echo $idResolucionDetalle.'&idColegiadoEspecialista='.$idColegiadoEspecialista; ?>" class="btn btn-info center-block btn-sm">Generar QR</a>-->
                            <a href="especialidades_resoluciones_titulo_form.php?id=<?php echo $idResolucionDetalle.'&idColegiadoEspecialista='.$idColegiadoEspecialista; ?>" class="btn btn-info center-block btn-sm">Generar QR</a>
                        <?php 
                        }
                        ?>
                    </td>
                    <?php } ?>
                </tr>
                  <?php
                  }
              ?>
              
	   </tbody>
        </table>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <?php
            if ($estadoResolucion == "A") {
            ?>
                <div class="col-md-3">
                    <a href="especialidades_resoluciones_imprimir.php?idResolucion=<?php echo $idResolucion; ?>" class="btn btn-warning btn-lg" target="_BLANK">Enviar a Consejo</a>
                </div>
            <?php
            } else {
                //if ($estadoResolucion == "E") {
            ?>
                <div class="col-md-3">
                    <a href="datosResoluciones/abm_resolucion.php?accion=A&idResolucion=<?php echo $idResolucion; ?>" class="btn btn-warning btn-lg" onclick="return confirmar()">Abrir resolución</a>
                </div>
                <div class="col-md-3">
                    <a href="datosResoluciones/cerrar_resolucion.php?idResolucion=<?php echo $idResolucion; ?>" class="btn btn-primary btn-lg"  onclick="return confirmarCierre()">Cerrar resolución</a>
                </div>
            <?php
                //}
            }
            ?>
        </div>
    <?php
} else {
?>
    <div class="<?php echo $resMatriculas['clase']; ?>" role="alert">
        <span class="<?php echo $resMatriculas['icono']; ?>" ></span>
        <span><strong><?php echo $resMatriculas['mensaje']; ?></strong></span>
    </div>
<?php
}    
?>
</div>
</div>
<div class="col-md-12 text-right">            
    <form id="formVolver" name="formVolver" method="POST" onSubmit="" action="especialidades_resoluciones.php">
        <button type="submit"  class="btn btn-success" >Volver </button>
        <input type="hidden" id="estadoResoluciones" name="estadoResoluciones" value="<?php echo $estadoResoluciones; ?>">
        <input type="hidden" id="anioResoluciones" name="anioResoluciones" value="<?php echo $anioResoluciones; ?>">
    </form>
</div>
<div class="row">&nbsp;</div>
<?php
require_once '../html/footer.php';