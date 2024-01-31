<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/fapLogic.php');
?>
<script>
$(document).ready(
    function () {
                $('#tablaEspecialista').DataTable({
                    "iDisplayLength":8,
                     "order": [[ 0, "desc" ], [ 1, "asc"]],
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                       "sSwfPath": "../public/swf/copy_csv_xls_pdf.swf", 
                       
                       "aButtons": [
                            {
                                "sExtends": "pdf",
                                "mColumns" : [0, 1, 2, 3,4],
//                                "oSelectorOpts": {
//                                    page: 'current'
//                                }
                                "sTitle": "Listado de Llamadas",
                                "sPdfOrientation": "portrait",
                                "sFileName": "ListadoDeLlamadas.pdf"
//                              "sPdfOrientation": "landscape",
//                              "sPdfSize": "letter",  ('A[3-4]', 'letter', 'legal' or 'tabloid')
                            }
                            
                    ]
                    }
                });
    }
);
</script>
<?php
if (isset($_GET['idColegiado'])) {
    $_SESSION['menuColegiado'] = "Datos en FAP";
    $periodoActual = $_SESSION['periodoActual'];
    $idColegiado = $_GET['idColegiado'];
    $resColegiado = obtenerColegiadoPorId($idColegiado);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiado = $resColegiado['datos'];
        $muestraMenuCompleto = TRUE;
        include 'menuColegiado.php';
        ?>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-6">
                <label>Apellido y Nombres:&nbsp; </label><?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?>
                <label>- Matr&iacute;cula:&nbsp; </label><?php echo $colegiado['matricula']; ?>
            </div>
            <div class="col-md-3"><h4><b>Datos en FAP</b></h4></div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <?php
        //busco las especialidades
        $resFap = obtenerCausasPorIdColegiado($idColegiado);
        if ($resFap['estado']){
        ?>
            <div class="row">
                <div class="col-md-12">
                <table  id="tablaEspecialista" class="display">
                    <thead>
                        <tr>
                            <th style="text-align: center; display: none;">Id</th>
                            <th style="text-align: center;">Fecha Ingreso</th>
                            <th style="text-align: center;">Estado</th>
                            <th style="text-align: center;">Nombre de la Causa</th>
                            <th style="text-align: center;">Nº de resoluci&oacute;n por</th>
                            <th style="text-align: center;">Nº de causa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($resFap['datos'] as $dato){
                            $id = $dato['id'];
                            $fechaIngreso = cambiarFechaFormatoParaMostrar($dato['fechaIngreso']);
                            $estadoDetalle = $dato['estadoDetalle'];
                            $nombreCausa = $dato['nombreCausa'];
                            $numeroResolucion = $dato['numeroResolucion'];
                            $numeroCausa = $dato['numeroCausa'];
                            ?>
                            <tr>
                                <td style="display: none"><?php echo $id;?></td>
                                <td style="text-align: left;"><?php echo $fechaIngreso;?></td>
                                <td style="text-align: center;"><?php echo $estadoDetalle;?></td>
                                <td style="text-align: center;"><?php echo $nombreCausa;?></td>
                                <td style="text-align: center;"><?php echo $numeroResolucion;?></td>
                                <td style="text-align: center;"><?php echo $numeroCausa;?></td>
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
            <div class="<?php echo $resFap['clase']; ?>" role="alert">
                <span class="<?php echo $resFap['icono']; ?>" aria-hidden="true"></span>
                <span><strong><?php echo $resFap['mensaje']; ?></strong></span>
            </div>        
        <?php        
        }
    } else {
    ?>
        <div class="<?php echo $resColegiado['clase']; ?>" role="alert">
            <span class="<?php echo $resColegiado['icono']; ?>" aria-hidden="true"></span>
            <span><strong><?php echo $resColegiado['mensaje']; ?></strong></span>
        </div>        
    <?php        
    }
}
require_once '../html/footer.php';
