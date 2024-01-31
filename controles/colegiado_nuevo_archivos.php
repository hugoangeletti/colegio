<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
?>
<script>
      $(document).ready(function()
      {
         $("#myModal").modal("show");
      });
    </script>
<?php
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoArchivoLogic.php');

if (isset($_GET['idColegiado'])) {
    $_SESSION['menuColegiado'] = "Alta";
    $periodoActual = $_SESSION['periodoActual'];
    $idColegiado = $_GET['idColegiado'];
    $tipoIngreso = "";
    if (isset($_GET['tipo']) || isset($_POST['tipoIngreso'])){
        if (isset($_GET['tipo'])) {
            $tipoIngreso = $_GET['tipo'];
        } else {
            if (isset($_POST['tipoIngreso'])) {
                $tipoIngreso = $_POST['tipoIngreso'];            
            } else {
                $idColegiado = NULL;
            }
        }
        if ($tipoIngreso == "otro") {
            $panel = "panel-warning";
            $titulo = "Alta de Matricula de Otro Distrito";
            $botonConfirma = "btn-warning";
            $siguientePaso = "colegiado_nuevo_otro.php?idColegiado=".$idColegiado."&tipo=otro";
            $asociarPaso = "datosColegiado/asociar_foto_firma.php?tipo=otro";
        } else {
            $panel = "panel-primary";
            $titulo = "Alta de Matricula dada de baja";
            $botonConfirma = "btn-primary";
            $siguientePaso = "colegiado_nuevo_baja.php?idColegiado=".$idColegiado."&tipo=baja";
            $asociarPaso = "datosColegiado/asociar_foto_firma.php?tipo=baja";
        }
    } else {
        $panel = "panel-success";
        $titulo = "Alta de matriculado del Distrito I";
        $botonConfirma = "btn-success";
        $siguientePaso = "colegiado_nuevo_paso3.php?idColegiado=".$idColegiado;
        $asociarPaso = "datosColegiado/asociar_foto_firma.php";
    }
    $display = 'none';
    
    $resColegiado = obtenerColegiadoPorId($idColegiado);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiado = $resColegiado['datos'];
        $matricula = $colegiado['matricula'];
        $tituloDigital = $colegiado['tituloDigital'];
        ?>
        <div class="panel <?php echo $panel; ?>">
            <div class="panel-heading">
                <h4><?php echo $titulo; ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (isset($_GET['err'])) {
                ?>
                    <h4>Debe tomar la foto y la firma, luego asociarla a la nueva matrícula.</h4>
                <?php 
                } else {
                ?>
                    <h4>La foto y la firma están asociadas al matriculado.</h4>
                <?php
                }
                ?>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-3">
                        Apellido y Nombres
                        <b><input class="form-control" type="text" value="<?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?>" readonly=""/></b>
                    </div>
                    <div class="col-md-2">
                        Matr&iacute;cula
                        <b><input class="form-control" type="text" value="<?php echo $colegiado['matricula']; ?>" readonly=""/></b>
                    </div>
                    <div class="col-md-7">
                        <?php
                        //busco si estan las imagenes para cargar en la tabla
                        $hayArchivos = FALSE;
                        $fileFoto = rellenarCeros($matricula, 8).'.jpg';
                        $fileFirma = rellenarCeros($matricula, 8).'.bmp';
                        if ($tituloDigital == 1) {
                            $fileTitulo = rellenarCeros($matricula, 8).'.pdf';
                        } else {
                            $fileTitulo = NULL;
                        }
                        $foto = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Fotos/".$fileFoto, "rb");
                        if ($foto) {
                            $firma = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Firmas/".$fileFirma, "rb");
                            if ($firma) {
                                if (isset($fileTitulo)) {
                                    $titulo = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Titulos/".$fileTitulo, "r");
                                    if ($titulo) {
                                        $hayArchivos = TRUE;        
                                    }
                                } else {
                                    $hayArchivos = TRUE;
                                }
                            }
                        } 
                        ?>
                            <?php
                            $tieneFotoFirma = FALSE;
                            if (isset($_GET['err'])) {
                            ?>
                                <div class="col-md-12">
                                    <div class="alert alert-danger ocultarMensaje">No se encontraron los archivos para asociar, verifique si los nombres son correctos.</div>
                                </div>
                            <?php
                            } else {
                                ?>
                                <div class="col-md-5">
                                    <?php
                                    //verifica que tenga foto y firma para mostrar
                                    $resArchivos = obtenerColegiadoArchivo($idColegiado, '1');
                                    if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                                        $archivos = $resArchivos['datos'];
                                        $fileFoto = trim($archivos['nombre']);
                                        // insertamos la foto y firma
                                        $foto = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Fotos/".$fileFoto, "rb");
                                        if ($foto) {
                                            $contents=stream_get_contents($foto);

                                            $fotoVer = base64_encode($contents);
                                            $tieneFotoFirma = TRUE;
                                            ?>
                                        <img class="img img-thumbnail" style="height: 150px " src="data:image/jpg;base64,<?php echo $fotoVer; ?>" />
                                        <br>Foto
                                    <?php
                                    }
                                }
                                ?>
                                </div>
                                <div class="col-md-5">
                                <?php
                                $resArchivos = obtenerColegiadoArchivo($idColegiado, '2');
                                if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                                    $archivos = $resArchivos['datos'];
                                    $fileFirma = trim($archivos['nombre']);
                                    $firma = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Firmas/".$fileFirma, "rb");
                                    if ($firma) {
                                        $contents=stream_get_contents($firma);
                                        $firmaVer = base64_encode($contents);
                                        $tieneFotoFirma = TRUE;
                                        ?>
                                        <img class="img img-thumbnail" src="data:image/jpg;base64,<?php echo $firmaVer; ?>" height="80" width="200" />
                                        <br>Firma
        <!--                            <form  method="POST" action="colegiado_credencial.php">
                                        <button type="submit" class="btn btn-info" name='volver' id='name'>Imprimir Credencial </button>
                                    </form>-->
                                <?php
                                    }
                                }
                                ?>
                                </div>
                                <div class="col-md-2">
                                <?php
                                if ($tituloDigital == 1) {
                                    $resArchivos = obtenerColegiadoArchivo($idColegiado, '3');
                                    if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                                        $archivos = $resArchivos['datos'];
                                        $fileTitulo = trim($archivos['nombre']);
                                        $titulo = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Titulos/".$fileTitulo, "rb");
                                        if ($titulo) {
                                            $tieneFotoFirma = TRUE;
                                            ?>
                                            <div class="alert alert-success">Título cargado</div>
                                        <?php
                                        }
                                    } else {
                                        $tieneFotoFirma = FALSE;
                                    ?>
                                        <div class="alert alert-danger">Título NO INGRESADO</div>
                                    <?php
                                    }
                                }
                                ?>
                                </div>
                            <?php
                            }
                            ?>
                    </div>
                    <div class="col-md-5">
                        <?php
                        if (!$tieneFotoFirma && $colegiado['tipoEstado'] <> 'F') {
                        ?>
                            <div class="col-md-6">
                                <?php 
                                if ($tituloDigital) {
                                ?>
                                    <h4>Debe asociar foto, firma y título digital a la nueva matrícula.</h4>
                                <?php    
                                } else {
                                ?>
                                    <h4>Debe asociar foto y firma a la nueva matrícula.</h4>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <form  method="POST" action="<?php echo $asociarPaso; ?>">
                                    <button type="submit" class="btn btn-lg btn-info" name='asociar' id='asociar' onclick="show('siguiente')">Asociar imágenes </button>
                                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                                    <input type="hidden" name="matricula" id="matricula" value="<?php echo $matricula; ?>" />
                                    <input type="hidden" name="tituloDigital" id="tituloDigital" value="<?php echo $tituloDigital; ?>" />
                                    <input type="hidden" name="accion" id="accion" value="alta" />
                                </form>
                            </div>
                        <?php
                        } else {
                            $display = 'block';
                        }
                        ?>
                    </div>
                    <div class="col-md-12 text-right">
                        <form id="siguiente" autocomplete="off" name="siguiente" style="display: <?php echo $display; ?>;" method="POST" onSubmit="" action="<?php echo $siguentePaso; ?>">
                            <div class="col-md-12 text-right">
                                <button type="submit"  class="btn <?php echo $botonConfirma; ?> btn-lg" >Siguiente </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
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
?>
<script type="text/javascript">
	function show(bloq) {
	 obj = document.getElementById(bloq);
	 obj.style.display = (obj.style.display=='none') ? 'block' : 'none';
}

</script>