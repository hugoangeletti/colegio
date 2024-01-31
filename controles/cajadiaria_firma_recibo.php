<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/cursoLogic.php');
require_once ('../dataAccess/cajaDiariaLogic.php');
require_once ('../dataAccess/usuarioLogic.php');
require_once ('../dataAccess/tipoPagoLogic.php');

$idUsuario = $_SESSION['user_id'];
if (isset($_GET['idColegiado'])) {
    $idColegiado = $_GET['idColegiado'];
} else {
    if (isset($_POST['idColegiado'])) {
        $idColegiado = $_POST['idColegiado'];
    } else {
        $idColegiado = NULL;
    }
}
$continua = TRUE;
$resCajaDiaria = obtenerCajaAbierta();
if ($resCajaDiaria['estado']) {
    $idCajaDiaria = $resCajaDiaria['datos']['idCajaDiaria'];
} else {
    $continua = FALSE;
}

if ($continua) {
    if (isset($idColegiado)) {
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['estado'] && $resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
            $matricula = $colegiado['matricula'];
            $apellidoNombre = $colegiado['apellido'].', '.$colegiado['nombre'];
            $tituloCajaDiaria = "Certificación de firma.";
            ?>
            <div class="col-md-12 alert alert-info">
                <div class="row">
                    <div class="col-md-9">
                        <h4>Caja Diaria - <?php echo $tituloCajaDiaria; ?></h4>
                    </div>
                    <div class="col-md-3 text-left">
                        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cajadiaria.php">
                            <button type="submit"  class="btn btn-info" >Volver a Caja Diaria</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-2"><h4>Matrícula: &nbsp;<b><?php echo $matricula; ?></b></h4></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-6"><h4>Apellido y Nombre: &nbsp;<b><?php echo $apellidoNombre; ?></b></h4></div>
                    </div>
                    <?php
                    $idTipoPago = 62; //certificacion de firma
                    $resTipoPago = obtenerTipoValorPorId($idTipoPago);
                    if ($resTipoPago['estado']) {
                        $tipoPago = $resTipoPago['datos'];
                        $totalRecibo = $tipoPago['importe'];
                        ?>
                        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="datosCajaDiaria/generar_recibo_firma.php" target="_BLANK">
                            <div class="row">
                                <div class="col-md-3">&nbsp;</div>
                                <div class="col-md-4">
                                    <h4>Total a abonar: <b>$<?php echo $totalRecibo; ?></b></h4> 
                                </div>
                            </div>
                            <div class="row">&nbsp;</div>
                            <div class="row">
                                <div class="col-md-3">&nbsp;</div>
                                <div class="col-md-4">
                                    <button type="submit"  class="btn btn-default">Confirma certificación</button>
                                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                                    <input type="hidden" name="tipoRecibo" id="tipoRecibo" value="FIRMA" />
                                </div>
                            </div>
                        </form>
                        <div class="row">&nbsp;</div>
                    <?php 
                    } else {
                    ?>
                        <div class="row">&nbsp;</div>
                        <div class="col-md-12 alert alert-danger">
                            <h4>Error en los datos, vuelva a ingresar.</h4>
                        </div>
                    <?php                    
                    }
                    ?>
                </div>
            </div>
        <?php 
        } else {
        ?>
            <div class="row">&nbsp;</div>
            <div class="col-md-12 alert alert-danger">
                <h4>Error en los datos de la matrícula, vuelva a ingresar.</h4>
            </div>
        <?php                    
        }
    } else {
        //debe buscar el colegiado
        ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <h4>Certificación de firma</h4>
                <h5>Seleccione al colegiado/a</h5>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <?php 
        $link_form_origen = 'cajadiaria_firma_recibo.php';
        include_once 'buscar_colegiado.php';
        ?>
        <div class="row">&nbsp;</div>
        <div class="row text-center">
            <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cajadiaria.php">
                <button type="submit"  class="btn btn-info" >Volver a Caja Diaria</button>
            </form>
        </div>        
        <?php 
    }
} else {
?>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="alert alert-warning">NO HAY CAJA ABIERTA, DEBE IR A CAJAS DIARIAS Y ABRIR PRIMERO UNA CAJA DEL DIA</div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row text-center">
        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cajadiaria.php">
            <button type="submit"  class="btn btn-info" >Volver a Caja Diaria</button>
        </form>
    </div>
<?php      
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
                    url: 'colegiado.php?activos=SI',
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