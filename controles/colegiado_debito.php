<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoDebitosLogic.php');
require_once ('../dataAccess/bancoLogic.php');
?>
<script>
function deshabilitarTipoDebito(nombreRadio)
{
    switch(nombreRadio)
    {
        case 'C':
            document.getElementById('numeroTarjeta').disabled=false;
            document.getElementById('numeroDocumento').disabled=false;
            document.getElementById('numeroCbu').disabled=true;
            document.getElementById('tipoCuenta3').disabled=true;
            document.getElementById('tipoCuenta4').disabled=true;
            break;

        case 'H':
            document.getElementById('numeroTarjeta').disabled=true;
            document.getElementById('numeroDocumento').disabled=true;
            document.getElementById('numeroCbu').disabled=false;
            document.getElementById('tipoCuenta3').disabled=false;
            document.getElementById('tipoCuenta4').disabled=false;
            break;
            
        default :
            document.getElementById('numeroTarjeta').disabled=true;
            document.getElementById('numeroDocumento').disabled=true;
            document.getElementById('numeroCbu').disabled=true;
            document.getElementById('tipoCuenta3').disabled=true;
            document.getElementById('tipoCuenta4').disabled=true;
            break;
    }
     
}
  
</script>
<?php
if (isset($_GET['idColegiado']) && isset($_GET['tipo'])) {
    $idColegiado = $_GET['idColegiado'];
    $tipo = $_GET['tipo'];
} else {
    if (isset($_POST['idColegiado'])) {
        $idColegiado = $_POST['idColegiado'];
        //$tipo = $_POST['tipo'];
    } else {
        $idColegiado = NULL;
        $tipo = NULL;
    }
}

if (isset($idColegiado)) {
    $periodoActual = $_SESSION['periodoActual'];
    $resColegiado = obtenerColegiadoPorId($idColegiado);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiado = $resColegiado['datos'];
        //include 'menuColegiado.php';
    }
    
    if (isset($_POST['mensaje'])) {
    ?>
        <div class="ocultarMensaje"> 
            <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
        </div>
     <?php
        if (isset($_POST['numeroTarjeta'])) {
            $numeroTarjeta = $_POST['numeroTarjeta'];
            $deshabilitaTarjeta = '';
            $deshabilitaCbu = 'disabled=""';
        } else {
            $numeroTarjeta = '';
        }
        if (isset($_POST['numeroCbu'])) {
            $numeroCbu = $_POST['numeroCbu'];
            $deshabilitaTarjeta = 'disabled=""';
            $deshabilitaCbu = '';
        } else {
            $numeroCbu = '';
        }
        if (isset($_POST['numeroDocumento'])) {
            $numeroDocumento = $_POST['numeroDocumento'];
        } else {
            $numeroDocumento = '';
        }
        if (isset($_POST['idBanco'])) {
            $idBanco = $_POST['idBanco'];
        } else {
            $idBanco = '';
        }
        if (isset($_POST['incluyePP'])) {
            $incluyePP = $_POST['incluyePP'];
        } else {
            $incluyePP = '';
        }
        if (isset($_POST['incluyeTotal'])) {
            $incluyeTotal = $_POST['incluyeTotal'];
        } else {
            $incluyeTotal = '';
        }
        if (isset($_POST['tipoCuenta'])) {
            $tipoCuenta = $_POST['tipoCuenta'];
        } else {
            $tipoCuenta = '';
        }
        if (isset($_POST['tipoAnterior'])) {
            $tipo = $_POST['tipoAnterior'];
            switch ($tipo) {
                case  'C':
                    $titulo = 'Actualiza Tarjeta de Crédito';
                    break;
                case  'D':
                    $titulo = 'Debe reemplazar la Tarjeta de Débito por una Tarjeta de Crédito o CBU';
                    break;
                case  'H':
                    $titulo = 'Actualiza Tarjeta de Crédito';
                    break;
                case 'N';
                    $titulo = 'Adherir al débito por Tarjeta de Crédito o CBU';
                    break;
                default:
                    $titulo = 'Error de ingreso';
                    break;
            }
        } else {
            $tipo = '';
        }
        
    } else {
        $numeroTarjeta = '';
        $numeroCbu = '';
        $numeroDocumento = '';
        $idBanco = '';
        $pagoTotal = '';
        $tipoCuenta = '';
        $deshabilitaTarjeta = '';
        $deshabilitaCbu = '';
        $incluyePP = '';
        $incluyeTotal = '';

        switch ($tipo) {
            case 'C':
                //si ya tiene debito por tarjeta de credito, cargo los datos actuales sino dejo todos los campos vacios
                $resDebito = obtenerDebitoPorIdColegiado($idColegiado);
                if ($resDebito['estado']) {
                    $debito = $resDebito['datos'];
                    $numeroTarjeta = $debito['numeroTarjeta'];
                    $numeroCbu = '';
                    $numeroDocumento = $debito['numeroDocumento'];
                    $idBanco = $debito['idBanco'];
                    $incluyePP = $debito['incluyePP'];
                    $incluyeTotal = $debito['pagoTotal'];
                    $tipoCuenta = '';
                    $deshabilitaTarjeta = '';
                    $deshabilitaCbu = 'disabled=""';
                    $fechaCarga = $debito['fechaCarga'];
                    $titulo = 'Actualiza Tarjeta de Crédito (Última actualización: '.cambiarFechaFormatoParaMostrar($fechaCarga).')';
                } else {
                ?>
                    <div class="<?php echo $resDebito['clase']; ?>" role="alert">
                        <span class="<?php echo $resDebito['icono']; ?>" aria-hidden="true"></span>
                        <span><strong><?php echo $resDebito['mensaje']; ?></strong></span>
                    </div>
                <?php
                }
                break;

            case 'D':
                //si ya tiene debito por tarjeta de debito entonces debe reemplazarla por una de credito o cbu
                $titulo = 'Debe reemplazar la Tarjeta de Débito por una Tarjeta de Crédito o CBU';
                break;

            case 'H':
                //si ya tiene debito por CBU, cargo los datos actuales sino dejo todos los campos vacios
                $resDebito = obtenerDebitoCBUPorIdColegiado($idColegiado);
                if ($resDebito['estado']) {
                    $debito = $resDebito['datos'];
                    $idDebitoCbu = $debito['id'];
                    $idBanco = $debito['idBanco'];
                    $numeroCbu = $debito['numeroCbu'];
                    $numeroDocumento = NULL;
                    $numeroTarjeta = NULL;
                    $incluyePP = $debito['incluyePP'];
                    $incluyeTotal = $debito['pagoTotal'];
                    $tipoCuenta = $debito['tipo'];
                    $deshabilitaCbu = '';
                    $deshabilitaTarjeta = 'disabled=""';
                    $fechaCarga = $debito['fechaCarga'];
                    $titulo = 'Actualiza CBU (Última actualización: '.cambiarFechaFormatoParaMostrar($fechaCarga).')';
                } else {
                ?>
                    <div class="<?php echo $resDebito['clase']; ?>" role="alert">
                        <span class="<?php echo $resDebito['icono']; ?>" aria-hidden="true"></span>
                        <span><strong><?php echo $resDebito['mensaje']; ?></strong></span>
                    </div>
                <?php
                }
                break;

            case 'N';
                $titulo = 'Adherir al débito por Tarjeta de Crédito o CBU';
                break;

            default:
                $titulo = 'Error de ingreso';
                break;
        }
    }
    ?>

<div class="panel panel-info">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-9">
                <h4>Datos de d&eacute;bito por Tarjeta de Cr&eacute;dito o CBU</h4>
            </div>
            <div class="col-md-3 text-left">
                <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="colegiado_consulta.php?idColegiado=<?php echo $idColegiado;?>">
                    <button type="submit"  class="btn btn-info" >Volver a Datos del colegiado</button>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
    <div class="row">
        <div class="col-md-2">
            <label>Matr&iacute;cula:&nbsp; </label><?php echo $colegiado['matricula']; ?>
        </div>
        <div class="col-md-4">
            <label>Apellido y Nombres:&nbsp; </label><?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?>
        </div>
        <div class="col-md-6">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center"><h4><b><?php echo $titulo; ?></b></h4></div>
    </div>
    <div class="row">&nbsp;</div>
    <form id="datosDebito" autocomplete="off" name="datosDebito" method="POST" target="_BLANK" onSubmit="" action="datosColegiadoDebito\actualiza_debito.php">
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-3">
                <label>Tipo de D&eacute;bito *</label><br>  
                <div class="radio-inline" >
                    <label><input type="radio" name="tipo" value="C" <?php if ($tipo == 'C') { ?> checked="" <?php } ?> onClick="deshabilitarTipoDebito('C');">Por Tarjeta de Cr&eacute;dito</label>
                </div>
                <div class="radio-inline" >
                    <label><input type="radio" name="tipo" value="H" <?php if ($tipo == 'H') { ?> checked="" <?php } ?> onClick="deshabilitarTipoDebito('H');">Por CBU</label>
                </div>
            </div>
            <div class="col-md-3">
                <label>Banco *</label>
                <select class="form-control" id="idBanco" name="idBanco" required="">
                    <option value="">Seleccione un Banco</option>
                    <?php
                    $resBancos = obtenerBancos();
                    if ($resBancos['estado']) {
                        foreach ($resBancos['datos'] as $row) {
                        ?>
                            <option value="<?php echo $row['id'] ?>" <?php if($idBanco == $row['id']) { echo 'selected'; } ?>><?php echo $row['nombre'] ?></option>
                        <?php
                        }
                    } else {
                        echo $resBancos['mensaje'];
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-3">
                <label>N&uacute;mero de Tarjeta de Cr&eacute;dito *</label>
                <input class="form-control" type="text" id="numeroTarjeta" name="numeroTarjeta" value="<?php echo $numeroTarjeta; ?>" placeholder="Todo junto sin separadores" maxlength="16" required="" <?php echo $deshabilitaTarjeta; ?>/>
            </div>
            <div class="col-md-3">
                <label>N&uacute;mero Documento del Titular *</label>
                <input class="form-control" type="number" maxlength="8" id="numeroDocumento" name="numeroDocumento" value="<?php echo $numeroDocumento; ?>" required="" <?php echo $deshabilitaTarjeta; ?>/>
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-3">
                <label>N&uacute;mero de CBU *</label>
                <input class="form-control" type="text" maxlength="22" id="numeroCbu" name="numeroCbu" value="<?php echo $numeroCbu; ?>" placeholder="Todo junto sin separadores" required="" <?php echo $deshabilitaCbu; ?>/>
            </div>
            <div class="col-md-3">
                <label>Tipo de Cuenta *</label><br>
                <div class="radio-inline" >
                    <label><input type="radio" name="tipoCuenta" id="tipoCuenta3" value="3" <?php if ($tipoCuenta == '3') { ?> checked="" <?php } ?> <?php echo $deshabilitaCbu; ?>>Cuenta Corriente</label>
                </div>
                <div class="radio-inline" >
                    <label><input type="radio" name="tipoCuenta" id="tipoCuenta4" value="4" <?php if ($tipoCuenta == '4') { ?> checked="" <?php } ?> <?php echo $deshabilitaCbu; ?>>Caja de Ahorro</label>
                </div>
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-3">&nbsp;</div>
            <div class="col-md-3">
                <label>Incluye Plan de Pagos *</label>
                <div class="radio-inline" >
                    <label><input type="radio" name="incluyePP" value="S" <?php if ($incluyePP == 'S') { ?> checked="" <?php } ?>>Si</label>
                </div>
                <div class="radio-inline" >
                    <label><input type="radio" name="incluyePP" value="N" <?php if ($incluyePP == 'N') { ?> checked="" <?php } ?>>No</label>
                </div>
            </div>
            <div class="col-md-3">
                <label>Incluye Pago Total *</label>
                <div class="radio-inline" >
                    <label><input type="radio" name="incluyeTotal" value="S" <?php if ($incluyeTotal == 'S') { ?> checked="" <?php } ?>>Si</label>
                </div>
                <div class="radio-inline" >
                    <label><input type="radio" name="incluyeTotal" value="N" <?php if ($incluyeTotal == 'N') { ?> checked="" <?php } ?>>No</label>
                </div>
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-3">
                <?php
                if ($tipo == 'C' || $tipo == 'H'){
                ?>
                    <a href="colegiado_debito_imprimir.php?idColegiado=<?php echo $idColegiado;?>&tipo=<?php echo $tipo; ?>" class="btn btn-default" role="button" target="_blank">Imprimir Planilla de adhesi&oacute;n</a>
                <?php
                } else {
                    echo "";
                }
                ?>
            </div>
            <div class="col-md-6 text-center">
                <h4 id="informe" style="display: none; color: #F8BB00; ">El Débito se generó con éxito.</h4>
                <button type="submit" name='confirma' id='confirma' class="btn btn-success" onclick="show('confirma', 'informe')">Confirma actualizaci&oacute;n</button>
                <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                <input type="hidden" name="tipoAnterior" id="tipoAnterior" value="<?php echo $tipo; ?>" />
            </div>
        </div>    
    </form>
<!--    <form id="formImprimirPlanilla" name="formImprimirPlanilla" method="POST" onSubmit="" action="colegiado_debito_imprimir.php?idColegiado=<?php echo $idColegiado;?>">
        <div class="row">
            <div class="text-center">
                <button type="submit" class="btn btn-default">Imprimir Planilla de adhesi&oacute;n</button>
            </div>
        </div>    
    </form>-->
    </div>
</div>
<?php
}
require_once '../html/footer.php';
?>
<script type="text/javascript">
	function show(bloq1, bloq2) {
	 obj = document.getElementById(bloq1);
	 obj.style.display = (obj.style.display=='none') ? 'block' : 'none';
         
	 obj2 = document.getElementById(bloq2);
	 obj2.style.display = (obj2.style.display=='none') ? 'block' : 'none';
}

</script>