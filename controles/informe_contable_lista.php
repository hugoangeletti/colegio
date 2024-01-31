<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/informeContableLogic.php');
?>
<script>
            $(document).ready(function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":10,
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
                                "sTitle": "Listado de Habilitaciones Solicitadas",
                                "sPdfOrientation": "portrait",
                                "sFileName": "listado_de_habilitaciones_solicitadas.pdf"
//                              "sPdfOrientation": "landscape",
//                              "sPdfSize": "letter",  ('A[3-4]', 'letter', 'legal' or 'tabloid')
                            }
                            
                    ]
                    }
                });
            });
            
   
</script>
<?php
if (isset($_POST['periodo']) && $_POST['periodo'] != ""){
    $periodo = $_POST['periodo'];
} else {
    $periodo = $_SESSION['periodoActual'];
}

?>
<div class="panel panel-info">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-3">
                <h4>Informes Contables por período</h4>
            </div>
            <div class="col-md-1">
                <form method="POST" action="informe_contable_lista.php">
                    <select class="form-control" id="periodo" name="periodo" required onChange="this.form.submit()">
                        <?php
                        $anio = date('Y');
                        while ($anio >= 2019) {
                        ?>
                            <option value="<?php echo $anio; ?>" <?php if($anio == $periodo) { echo 'selected'; } ?>><?php echo $anio; ?></option>
                        <?php
                            $anio--;
                        }
                        ?>
                    </select>
                </form>
            </div>
            <div class="col-md-6">&nbsp;</div>
            <div class="col-md-2">
                <form id="datos" name="datos" method="POST" action="informe_contable_form.php">
                    <button type="submit"  class="btn btn-primary btn-lg">Generar informe </button>
                    <input type="hidden" id="periodo" name="periodo" value="<?php echo $periodo; ?>">
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <?php
        if (isset($_POST['mensaje']) && $_POST['mensaje'] == "OK") {
        ?>
            <div class="row">
                <div class="<?php echo $_POST['clase']; ?>">
                    <h4><b><?php echo $_POST['mensaje'] ?></b></h4>
                </div>
            </div>
         <?php
        } else {
        ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $resInformes = obtenerInformePorPeriodo($periodo);
                    if ($resInformes['estado']) {
                        ?>
                        <table id="tablaOrdenada" class="display">
                            <thead>
                                <th>Mes</th>
                                <th>Origen</th>
                                <th>Fecha Proceso</th>
                                <th>Descargar</th>
                            </thead>
                            <tbody>
                        <?php
                        foreach ($resInformes['datos'] as $datos) {
                            if ($datos['borrado'] == 0) {
                                $idInformeContable = $datos['id'];
                                $periodo = $datos['periodo'];
                                $mes = $datos['mes'];
                                $origen = $datos['origen'];
                                $fechaProceso = $datos['fechaProceso'];
                                $idUsuario = $datos['idUsuario'];
                                ?>
                                <tr>
                                    <td><?php echo $mes; ?></td>
                                    <td><?php echo $origen; ?></td>
                                    <td><?php echo cambiarFechaFormatoParaMostrar($fechaProceso); ?></td>
                                    <td>BOTON</td>
                                </tr>
                        <?php    
                            }
                        } 
                        ?>
                        </tbody>
                    </table>
                    <?php
                    } else {
                        ?>
                        <div class="col-md-12">
                            <div class="<?php echo $resColegiacion['clase']; ?>" role="alert">
                                <span class="<?php echo $resColegiacion['icono']; ?>" aria-hidden="true"></span>
                                <span><strong><?php echo $resColegiacion['mensaje']; ?></strong></span>
                            </div>        
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>    
</div>
<?php
require_once '../html/footer.php';
