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
require_once ('../dataAccess/colegiadoDomicilioLogic.php');
require_once ('../dataAccess/colegiadoContactoLogic.php');
require_once ('../dataAccess/colegiadoArchivoLogic.php');
require_once ('../dataAccess/colegiadoDeudaAnualLogic.php');
require_once ('../dataAccess/colegiadoMovimientoLogic.php');
require_once ('../dataAccess/colegiadoDebitosLogic.php');
require_once ('../dataAccess/colegiadoObservacionLogic.php');
require_once ('../dataAccess/colegiadoResidenteLogic.php');

if (!isset($_POST['idColegiado']) && !isset($_GET['idColegiado'])) {
?>
    <div class="row">&nbsp;</div>
    <h4 class="alert alert-info"><b>Consulta de colegiados</b></h4>
    <div class="row">
        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="colegiado_consulta.php">
            <div class="row">
                <div class="col-md-3" style="text-align: right;">
                    <label>Matr&iacute;cula o Apellido y Nombre *</label>
                </div>
                <div class="col-md-7">
                    <input class="form-control" autofocus autocomplete="OFF" type="text" name="colegiado_buscar" id="colegiado_buscar" placeholder="Ingrese Matrícula o Apellido del colegiado" required=""/>
                    <input type="hidden" name="idColegiado" id="idColegiado" required="" />
                </div>
                <div class="col-md-2">
                    <button type="submit"  class="btn btn-success">Confirma colegiado</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">&nbsp;</div>
    <div class="row text-center">
        <img src="../public/images/logo-transp.png" alt="Colegio de M{edicos Distrito I">
    </div>
    <div class="row">&nbsp;</div>
<?php
} else {
    $_SESSION['menuColegiado'] = "Consulta";
    $periodoActual = $_SESSION['periodoActual'];
    $aJubFal = array('J', 'F');
    
    if (isset($_POST['idColegiado']) && $_POST['idColegiado']<>'') {
        $idColegiado = $_POST['idColegiado'];
    } else {
        if (isset($_GET['idColegiado'])) {
            $idColegiado = $_GET['idColegiado'];
        } else {
            $idColegiado = NULL;
        }
    } 
    
    if (isset($idColegiado)) {
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['estado'] && $resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
            $matricula = $colegiado['matricula'];

            //primero se verifica el titulo digital. Si es con titulo digital y no esta cargado el pdf no se muestra el menu completo
            $existeColegiadoTitulo = TRUE;
            $muestraMenuCompleto = TRUE;
            $resColegiadoTitulo = obtenerTitulosPorColegiado($idColegiado);
            if ($resColegiadoTitulo['estado']){
                $datoTitulo = $resColegiadoTitulo['datos'];
                $idColegiadoTitulo = $datoTitulo['idColegiadoTitulo'];
                $fechaRevalida = $datoTitulo['fechaRevalida'];
                $digital = $datoTitulo['digital'];
                if ($digital == 1) {
                    $tituloDigital = 'SI';
                    $resArchivos = obtenerColegiadoArchivo($idColegiado, '3');
                    if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                        $archivos = $resArchivos['datos'];
                        $fileTitulo = trim($archivos['nombre']);
                        $imagenTitulo = fopen("ftp://webcolmed:web.2017@192.168.2.50:21/Titulos/".$fileTitulo, "r");
                        if ($imagenTitulo) {
                            $tieneTituloDigital = TRUE;
                        } else {
                            $tieneTituloDigital = FALSE;
                            $muestraMenuCompleto = FALSE;
                        }
                    } else {
                        $fileTitulo = NULL;
                        $tieneTituloDigital = FALSE;
                        $muestraMenuCompleto = FALSE;
                        //echo $resArchivos['mensaje'];
                    }

                } else {
                    $tituloDigital = 'NO';
                    //var_dump($existeColegiadoTitulo);
                }
            } else {
                $existeColegiadoTitulo = FALSE;
                //echo $resColegiadoTitulo['mensaje'];
            }
            include 'menuColegiado.php';
            
            ?>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-5">
                            Apellido y Nombres
                            <b><input class="form-control" type="text" value="<?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-2">
                            Matr&iacute;cula
                            <b><input class="form-control" type="text" value="<?php echo $matricula; ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-2">
                            N&ordm; de Documento
                            <b><input class="form-control" type="text" value="<?php echo $colegiado['tipoDocumento'].' - '.$colegiado['numeroDocumento']; ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-3">
                            Fecha de Nacimiento
                            <?php
                            $nacimiento = cambiarFechaFormatoParaMostrar($colegiado['fechaNacimiento']);
                            //if ($colegiado['tipoEstado'] == 'A' || $colegiado['tipoEstado'] == 'I'){
                                $nacimiento .= ' - '.calcular_edad($colegiado['fechaNacimiento']);
                            //}
                            ?>
                            <b><input class="form-control" type="text" value="<?php echo $nacimiento; ?>" readonly=""/></b>
                        </div>
                    </div>

                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            if ($colegiado['tipoEstado'] == 'A' || $colegiado['tipoEstado'] == 'I'){
                                $estiloColegiado = ' style="color: green; font-size: large;"';
                            } else {
                                $estiloColegiado = ' style="color: red;"';
                            }
                            ?>
                            Estado Matricular &nbsp;&nbsp;
                            <?php
                            if (colegiadoTieneMovimientos($idColegiado)){
                            ?>
                                <a href="colegiado_movimientos.php?idColegiado=<?php echo $idColegiado; ?>">Ver movimientos matriculares</a>
                            <?php
                            } else {
                                if ($colegiado['estado'] == 36){
                                    if (verificarRolUsuario($_SESSION['user_id'], 43)) {
                            ?>
                                        <a href="colegiado_nuevo_baja.php?idColegiado=<?php echo $idColegiado; ?>">Ver movimientos matriculares</a>
                            <?php
                                    }
                                }
                            }
                            ?>
                            &nbsp;&nbsp;
                            <a href="colegiado_movimientos_distritos.php?idColegiado=<?php echo $idColegiado; ?>">Movimientos Otros Distritos</a>
                            <input class="form-control" type="text" <?php echo $estiloColegiado; ?> 
                                value="<?php 
                                    $elEstado = trim(obtenerDetalleTipoEstado($colegiado['tipoEstado']));
                                    if (isset($elEstado) && $elEstado <> "") {
                                        $elEstado .= ' - ';
                                    }
                                    echo $elEstado.$colegiado['movimientoCompleto']; ?>" 
                                readonly=""/>
                        </div>

                        <?php
                        if (!in_array($colegiado['tipoEstado'], $aJubFal)){
                        ?>
                            <div class="col-md-5">
                                Estado con Tesorer&iacute;a
                                <?php
                                //verifico si se encuentra adherido al debito automatico, tarjetas o cbu
                                $resDebito = adheridoAlDebito($idColegiado);
                                if ($resDebito['estado']){
                                    $adherido = 'Adherido al Débito por ';
                                    $tipoDebito = $resDebito['tipo'];
                                    switch ($tipoDebito) {
                                        case 'D':
                                            $adherido .= 'Tarjeta de Débito';
                                            break;

                                        case 'C':
                                            $adherido .= 'Tarjeta de Crédito';
                                            break;

                                        case 'H':
                                            $adherido .= 'CBU';
                                            break;

                                        default:
                                            break;
                                    }
                                    $estiloDebito = ' style="color: green;"';
                                } else {
                                    $adherido = 'Adherir al Débito Automático';
                                    $estiloDebito = '';
                                    $tipoDebito = 'N'; //indica que es nuevo en el debito
                                }
                                ?>
                                &nbsp;&nbsp;&nbsp;
                                <b>
                                    <a <?php echo $estiloDebito; ?> href="colegiado_debito.php?idColegiado=<?php echo $idColegiado; ?>&tipo=<?php echo $tipoDebito; ?>"><?php echo $adherido; ?></a>
                                </b>
                                <?php
                                //obtengo el estado actual con tesoreria
                                $resEstadoTeso = estadoTesoreriaPorColegiado($idColegiado, $periodoActual);
                                if ($resEstadoTeso['estado']){
                                    $codigo = $resEstadoTeso['codigoDeudor'];
                                    $resEstadoTesoreria = estadoTesoreria($codigo);
                                    if ($resEstadoTesoreria['estado']){
                                        $estadoTesoreria = $resEstadoTesoreria['estadoTesoreria'];
                                    } else {
                                        $estadoTesoreria = $resEstadoTesoreria['mensaje'];
                                    }
                                } else {
                                    $estadoTesoreria = $resEstadoTeso['mensaje'];
                                }

                                if ($codigo == 0){
                                    $estiloTesoreria = ' style="color: green; font-size: large;"';
                                } else {
                                    $estiloTesoreria = ' style="color: red;"';
                                }
                                ?>
                                <input class="form-control" type="text" <?php echo $estiloTesoreria; ?> value="<?php echo $estadoTesoreria  ?>" readonly=""/>
                            </div>
                        <?php
                        } else {
                            //obtengo la fecha de la cancelacion
                            $resMovimiento = obtenerMovimientoMatricular($idColegiado, $colegiado['estado']);
                            if ($resMovimiento['estado']) {
                                $movimiento = $resMovimiento['datos'];
                                $fechaCancelacion = cambiarFechaFormatoParaMostrar($movimiento['fechaDesde']);
                            } else {
                                $fechaCancelacion = $resMovimiento['mensaje'];
                            }
                        ?>
                            <div class="col-md-3">
                                Fecha de la cancelaci&oacute;n
                                <input class="form-control" type="text" <?php echo $estiloColegiado; ?> value="<?php echo $fechaCancelacion  ?>" readonly=""/>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                        <?php
                        }
                        ?>
                        <div class="col-md-1">      
                            <?php 
                            if (!$_SESSION['user_entidad']['soloConsulta']) { 
                            ?>                      
                                <a href="<?php echo $url_ConsultaTesoreria; ?>" class="btn btn-info" role="button">Cuotas<br>y Pagos</a>                    
                            <?php 
                            }
                            ?>
                        </div>
                    </div>

                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-4">
                            Matriculado el
                            <b><input class="form-control" type="text" value="<?php echo cambiarFechaFormatoParaMostrar($colegiado['fechaMatriculacion']).'  -  '.calcular_edad($colegiado['fechaMatriculacion']); ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-1">
                            Tomo
                            <b><input class="form-control" type="text" value="<?php echo $colegiado['tomo']; ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-1">
                            Folio
                            <b><input class="form-control" type="text" value="<?php echo $colegiado['folio']; ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-2">
                            Matr&iacute;cula Nacional
                            <b><input class="form-control" type="text" value="<?php if ($colegiado['matriculaNacional'] != "") { echo $colegiado['matriculaNacional']; } else { echo 'No registra'; } ?>" readonly=""/></b>
                        </div>
                        <div class="col-md-4">
                            Nacionalidad
                            <b><input class="form-control" type="text" value="<?php echo $colegiado['nacionalidad']; ?>" readonly=""/></b>
                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <?php
                        if ($existeColegiadoTitulo) {
                        ?>
                            <div class="col-md-2">
                                T&iacute;tulo
                                <b><input class="form-control" type="text" value="<?php echo $datoTitulo['tipoTitulo']; ?>" readonly=""/></b>
                            </div>
                            <div class="col-md-2">
                                Fecha T&iacute;tulo
                                <b><input class="form-control" type="text" value="<?php echo cambiarFechaFormatoParaMostrar($datoTitulo['fechaTitulo']).'  -  '.calcular_edad($datoTitulo['fechaTitulo']); ?>" readonly=""/></b>
                            </div>
                            <div class="col-md-1">
                                T&iacute;tulo Digital
                                <b><input class="form-control" type="text" value="<?php echo $tituloDigital; ?>" readonly=""/></b>
                            </div>
                            <div class="col-md-5">
                                Otorgado por
                                <b><input class="form-control" type="text" value="<?php echo $datoTitulo['universidad']; ?>" readonly=""/></b>
                            </div>
                            <div class="col-md-2">
                                Fecha Reválida/Conválida
                                <?php
                                if (isset($fechaRevalida) && $fechaRevalida <> "") {
                                ?>
                                    <b><input class="form-control" type="text" value="<?php echo cambiarFechaFormatoParaMostrar($fechaRevalida); ?>" readonly /></b>
                                <?php 
                                } else {
                                    echo '<br><b>NO APLICA</b>';
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
            <div class="row">&nbsp;</div>
            <?php
            $resDomicilio = obtenerColegiadoDomicilioPorIdColegiado($idColegiado);
            if ($resDomicilio['estado'] && isset($resDomicilio['datos'])) {
                $domicilio = $resDomicilio['datos'];
                ?>
            <div class="row">
                <?php
                //armo el domicilio
                $domicilioCompleto = "";
                if ($domicilio['calle']) {
                    $domicilioCompleto = $domicilio['calle'];
                    if ($domicilio['numero']) {
                        $domicilioCompleto .= " Nº ".$domicilio['numero'];
                    }
                    if ($domicilio['lateral']) {
                        $domicilioCompleto .= " e/ ".$domicilio['lateral'];
                    }
                    if ($domicilio['piso'] && strtoupper($domicilio['piso']) != "NR") {
                        $domicilioCompleto .= " Piso ".$domicilio['piso'];
                    }
                    if ($domicilio['depto'] && strtoupper($domicilio['depto']) != "NR") {
                        $domicilioCompleto .= " Dto. ".$domicilio['depto'];
                    }
                }
                $nombreLocalidad = $domicilio['nombreLocalidad'];
                $codigoPostal = $domicilio['codigoPostal'];
                ?>
                <div class="col-md-5">
                    Domicilio actual
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo $url_ConsultaDomicilios; ?>">Ver domicilios anteriores</a>
                    <b><input class="form-control" type="text" value="<?php echo $domicilioCompleto; ?>" readonly=""/></b>
                </div>
                <div class="col-md-6">
                    Localidad
                    <b><input class="form-control" type="text" value="<?php echo $nombreLocalidad; ?>" readonly=""/></b>
                </div>
                <div class="col-md-1">
                    Côdigo Postal
                    <b><input class="form-control" type="text" value="<?php echo $codigoPostal; ?>" readonly=""/></b>
                </div>
            </div>
            <?php
            } else {
                ?>
                    <div class="<?php echo $resDomicilio['clase']; ?>" role="alert">
                        <span class="<?php echo $resDomicilio['icono']; ?>" aria-hidden="true"></span>
                        <span><strong><?php echo $resDomicilio['mensaje']; ?></strong></span>
                    </div>        
                <?php
            }
            ?>
            <div class="row">&nbsp;</div>
            <?php
            $resContactos = obtenerColegiadoContactoPorIdColegiado($idColegiado);
            if ($resContactos['estado'] && isset($resContactos['datos'])) {
                $contactos = $resContactos['datos'];
                $mailRechazado = $contactos['mailRechazado'];
                $claseMail = '';
                $leyendaMail = '';
                if ($mailRechazado) {
                    $claseMail = 'alert alert-danger';
                    $leyendaMail = 'El mail registrado fue rechazo, verifique con el profesional.';
                }
                ?>
                <div class="row">
                    <div class="col-md-5">
                        Email&nbsp;&nbsp;
                        <b><input class="form-control <?php echo $claseMail ?>" type="text" value="<?php echo $contactos['email']; ?>" readonly=""/></b>
                        <h5 class="<?php echo $claseMail ?>"><?php echo $leyendaMail; ?></h5>
                    </div>
                    <div class="col-md-3">
                        Tel&eacute;fono fijo
                        <b><input class="form-control" type="text" value="<?php echo $contactos['telefonoFijo'] ; ?>" readonly=""/></b>
                    </div>
                    <div class="col-md-4">
                        Tel&eacute;fono M&oacute;vil
                        <b><input class="form-control" type="text" value="<?php echo $contactos['telefonoMovil'] ; ?>" readonly=""/></b>
                    </div>
                </div>
            <?php
            } else {
                ?>
                    <div class="<?php echo $resContactos['clase']; ?>" role="alert">
                        <span class="<?php echo $resContactos['icono']; ?>" aria-hidden="true"></span>
                        <span><strong><?php echo $resContactos['mensaje']; ?></strong></span>
                    </div>        
                <?php
            }
            ?>
                    
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <?php
                            $_SESSION['tieneFoto'] = FALSE;
                            $tieneFotoFirma = FALSE;
                            if (isset($_GET['err'])) {
                            ?>
                                <div class="alert alert-danger ocultarMensaje">No se encontraron los archivos para asociar, verifique si los nombres son correctos.</div>
                            <?php
                            } else {
                                //verifica que tenga foto y firma para mostrar
                                $resArchivos = obtenerColegiadoArchivo($idColegiado, '1');
                                if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                                    $archivos = $resArchivos['datos'];
                                    $fileFoto = trim($archivos['nombre']);
                                    // insertamos la foto y firma
                                    $foto = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Fotos/".$fileFoto, "rb");
                                    if ($foto) {
                                        $contents=stream_get_contents($foto);
                                        fclose ($foto);

                                        $fotoVer = base64_encode($contents);
                                        $tieneFotoFirma = TRUE;
                                        $_SESSION['tieneFoto'] = TRUE;
                                        ?>
                                    <img class="img img-thumbnail" width="150" src="data:image/jpg;base64,<?php echo $fotoVer; ?>" />
                                <?php
                                    }
                                } else {
                                    //var_dump($resArchivos);
                                }
                                $resArchivos = obtenerColegiadoArchivo($idColegiado, '2');
                                if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                                    $archivos = $resArchivos['datos'];
                                    $fileFirma = trim($archivos['nombre']);
                                    $firma = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Firmas/".$fileFirma, "rb");
                                    if ($firma) {
                                        $contents=stream_get_contents($firma);
                                        fclose ($firma);
                                        $firmaVer = base64_encode($contents);
                                        $tieneFotoFirma = TRUE;
                                        ?>
                                        <!--<div class="row">&nbsp;</div>-->
                                        <img class="img img-thumbnail" src="data:image/jpg;base64,<?php echo $firmaVer; ?>" width="160" />
                                        <!--<div class="row">&nbsp;</div>-->
        <!--                            <form  method="POST" action="colegiado_credencial.php">
                                        <button type="submit" class="btn btn-info" name='volver' id='name'>Imprimir Credencial </button>
                                    </form>-->
                                <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-12 text-center">
                            <a href="datosColegiadoCertificado/imprimir_legajo.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-info" target="_BLANK">
                                Imprimir Legajo <br>(Uso interno)
                            </a>
                        </div>
                    <?php 
                    if (!$_SESSION['user_entidad']['soloConsulta']) { 
                    ?>                      
                        <?php
                        //si tiene permiso mostramos las Novedades
                        if ($digital == 1 && $tieneTituloDigital) {
                        ?>
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-12 text-center">
                                <a href="titulo_digital.php?fileTitulo=<?php echo $fileTitulo; ?>" class="btn btn-info" target="_BLANK">
                                Ver Título
                            </a>
                            </div>
                        <?php
                        } else {
                            if ($digital == 1) {
                            ?>
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-12 text-center">
                                    <div class="alert alert-danger">Falta cargar el título digital</div>
                                </div>
                            <?php 
                            }
                        }
                        ?>
                        <?php
                        //si tiene permiso mostramos las Novedades
                        if (verificarRolUsuario($_SESSION['user_id'], 33)) {
                        ?>
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#novedadesModal">Notas </button>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        //verificamos si tiene novedades para mostrar
                        $resAutoprescripcion = obtenerColegiadoAutoprescripcion($idColegiado);
                        if ($resAutoprescripcion['estado'] && isset($resAutoprescripcion['datos']) && sizeof($resAutoprescripcion['datos']) > 0){
                        ?>
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#autoprescripcionModal">Autoprescripción</button>
                                <!--<a href="colegiado_autoprescripcion.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-danger">Autoprescripción</a>-->
                            </div>
                        <?php
                        }
                        $classObs = "btn btn-info";
                        $resObservaciones = obtenerColegiadoObservaciones($idColegiado);
                        if ($resObservaciones['estado'] && isset($resObservaciones) && sizeof($resObservaciones['datos']) > 0){
                            $classObs = "btn btn-danger";
                        }
                        ?>
                            <div class="col-md-12">&nbsp;
                            </div>
                            <div class="col-md-12 text-center">
                                <a href="colegiado_observaciones.php?idColegiado=<?php echo $idColegiado; ?>" class="<?php echo $classObs; ?>">Observaciones</a>
                            </div>
                            <div class="col-md-12">&nbsp;
                            </div>
                            <?php 
                            if (verificarRolUsuario($_SESSION['user_id'], 79)) {
                                $resColegiadoResidente = obtenerColegiadoResidentePorIdColegiado($idColegiado);
                                if ($resColegiadoResidente['estado'] && sizeof($resColegiadoResidente['datos']) > 0) {
                                    $colegiadoResidente = $resColegiadoResidente['datos'];
                                    $opcion = " (".$colegiadoResidente['opcion'].")";                        
                                } else {
                                    $opcion = "";
                                }
                                ?>
                                <div class="col-md-12 text-center">
                                    <a href="colegiado_residente_opcion.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-primary">Opción residente <?php echo $opcion; ?></a>
                                </div>
                            <?php 
                            }
                            ?>
                    <?php 
                    }
                    ?>
                    </div>
                    <div class="row col-md-12 text-center text-danger">
                    <?php 
                    if (!$_SESSION['user_entidad']['soloConsulta']) { 
                    ?>                      
                        <?php
                        if ($colegiado['tipoEstado'] <> 'F') {
                            //si no es fallecido, verificamos si tiene cargadas las foto y firma, en caso de no tenerlas se deben asociar
                            if (!$tieneFotoFirma) {
                            ?>
                                <h3>Debe tomar FOTO y FIRMA</h3>
                                <form  method="POST" action="datosColegiado/asociar_foto_firma.php">
                                    <button type="submit" class="btn btn-lg btn-info" name='asociar' id='asociar'>Asociar imágenes </button>
                                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                                    <input type="hidden" name="matricula" id="matricula" value="<?php echo $matricula; ?>" />
                                    <input type="hidden" name="tituloDigital" id="tituloDigital" value="<?php echo $tituloDigital; ?>" />
                                </form>
                            <?php
                            } else {
                                if ($tituloDigital == 'SI' && !$tieneTituloDigital) {
                                ?>
                                    <h3>Debe cargar TITULO DIGITAL</h3>
                                    <form  method="POST" action="datosColegiado/asociar_foto_firma.php">
                                        <button type="submit" class="btn btn-lg btn-info" name='asociar' id='asociar'>Asociar Título </button>
                                        <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                                        <input type="hidden" name="matricula" id="matricula" value="<?php echo $matricula; ?>" />
                                        <input type="hidden" name="tituloDigital" id="tituloDigital" value="<?php echo $tituloDigital; ?>" />
                                    </form>
                                <?php
                                } else {
                                }
                            }
                        }
                        ?>
                    <?php 
                    }
                    ?>
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
        //segun la condicion:   1. Tiene titulo de especialista para retirar.
        //                      2. Tiene costas impagas.
        //if ($atencion && ($_SESSION['mostrarAtencion'] <> "Mostro" || isset($_POST['mostrarAtencion']))) {
        
        if (isset($atencion) && $atencion && isset($_POST['idColegiado'])) {
        ?>
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header alert alert-danger">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Atenci&oacute;n</h4>
                        </div>              
                        <!-- dialog body -->
                        <div class="modal-body">
                            <h4><?php echo $mensajeAtencion; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
<!-- Modal -->
<div id="autoprescripcionModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header alert alert-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Autoprescripción</h4>
      </div>
      <div class="modal-body">
          <p>
              <?php 
                    if ($resAutoprescripcion['estado'] && isset($resAutoprescripcion['datos']) && sizeof($resAutoprescripcion['datos']) > 0){
                      ?>
                        <table width="100%" id="" class="display">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">Fecha Ingreso</th>
                                    <th style="text-align: center;">Autorizado</th>
                                    <th style="text-align: center;">Documento</th>
                                    <th style="text-align: center;">Parentezco</th>
                                    <th style="text-align: center;">Autorizado</th>
                                    <th style="text-align: center;">Documento</th>
                                    <th style="text-align: center;">Parentezco</th>
                                    <th style="text-align: center;">Realizó</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($resAutoprescripcion['datos'] as $autoprescripcion) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo cambiarFechaFormatoParaMostrar($autoprescripcion['fechaIngreso']); ?></td>
                                        <?php
                                        if ($autoprescripcion['autorizado1'] <> '') {
                                        ?>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['autorizado1']; ?></td>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['documento1']; ?></td>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['parentezco1']; ?></td>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['autorizado2']; ?></td>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['documento2']; ?></td>
                                            <td style="text-align: center;"><?php echo $autoprescripcion['parentezco2']; ?></td>
                                        <?php 
                                        } else {
                                        ?>
                                            <td colspan="6" style="text-align: center;">Personal</td>
                                        <?php
                                        }
                                        ?>
                                        <td style="text-align: center;"><?php echo $autoprescripcion['nombreUsuario']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                <?php
              }
              ?>
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>        

<div id="novedadesModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header alert alert-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Novedades</h4>
      </div>
      <div class="modal-body">
          <p>
            <?php 
            $resNota = obtenerColegiadoNota($idColegiado);
            if ($resNota['estado'] && isset($resNota['datos'])){
                if (sizeof($resNota['datos']) > 0) {
                    $nota = $resNota['datos']['nota'];
                    $idColegiadoNota = $resNota['datos']['idColegiadoNota'];
                } else {
                    $nota = '';
                    $idColegiadoNota = NULL;
                }
            ?>
                <form id="nota" autocomplete="off" name="nota" method="POST" action="datosColegiado/notas.php?idColegiado=<?php echo $idColegiado; ?>">
                    <div class="col-md-12">
                        <textarea class="form-control" name="nota" id="nota" rows="10" ><?php echo $nota; ?></textarea>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit"  class="btn btn-lg" >Guardar</button>
                        <input type="hidden" name="idColegiadoNota" id="idColegiadoNota" value="<?php echo $idColegiadoNota; ?>" />
                    </div>
                </form>                
            <?php
            }
            ?>
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>        
        
<div id="editarModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header alert alert-success">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Actualizar datos del colegiado</h4>
            </div>              
            <!-- dialog body -->
            <div class="modal-body">
                <?php
                if ($colegiado['tipoEstado'] <> 'F'){
                ?>
                    <a href="persona_actualizar.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-success btn-lg btn-block">Actualizar datos personales</a>
                    <a href="matricula_actualizar.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-success btn-lg btn-block">Actualizar datos de matriculación</a>
                    <a href="titulo_actualizar.php?idColegiado=<?php echo $idColegiado; ?>&id=<?php echo $idColegiadoTitulo; ?>" class="btn btn-success btn-lg btn-block">Actualizar datos del título</a>
                    <a href="domicilio_actualizar.php?idColegiado=<?php echo $idColegiado; ?>&ori=consulta" class="btn btn-success btn-lg btn-block">Actualizar domicilio</a>
                    <a href="actualizar_contacto.php?idColegiado=<?php echo $idColegiado; ?>" class="btn btn-success btn-lg btn-block">Actualizar contacto</a>
                <?php
                }
                ?>
            </div>
            <!-- dialog buttons -->
            <!--<div class="modal-footer"><button type="button" class="btn btn-primary">OK</button></div>-->
        </div>
    </div>
</div>

    <?php
    } else {
    ?>
        <div class="row alert alert-danger">
            <h4>
                Atenci&oacute;n: no ingres&oacute; correcatmente los datos del colegiado.
                <a class="" href="colegiado_consulta.php">Haga click aqui</a>
            </h4>
        </div>              
    <?php
    }
}
require_once '../html/footer.php';
?>
<!--AUTOCOMLETE-->
<script src="../public/js/bootstrap3-typeahead.js"></script>    
<script language="JavaScript">
    $(function(){
        var nameIdMap = {};
        $('#colegiado_buscar').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'colegiado.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idColegiado').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });  
    
</script>