<?php
//require_once ('../dataAccess/config.php');
//permisoLogueado();
//require_once ('../html/head.php');
//require_once ('../html/header.php');
//require_once ('../dataAccess/funcionesConector.php');
//require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/formaPagoLogic.php');
require_once ('../dataAccess/bancoLogic.php');
$totalRecibo = 1000;
$importe = $totalRecibo;
$importe_efectivo = $importe;
$idBanco = NULL;
?>
<div class="panel panel-info">
    <div class="panel-body" style="background-color: lightblue;">
        <div class="row">
            <div class="col-md-2">&nbsp;</div>
            <div class="col-md-4">
                <h4>Forma de pago.</h4>
            </div>
        </div>
        <?php
        $resBancos = obtenerBancos();
        $resFormaPago = obtenerFormasPago();
        if ($resFormaPago['estado']) {
        ?>
            <div class="row">
                <div class="col-md-2">&nbsp;</div>
            <?php
            foreach ($resFormaPago['datos'] as $row) {                                   
                $idFormaPago = $row['id'];
                $nombre = $row['leyenda'];
                ?>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" name="formaPago" type="radio" id="formaPago_<?php echo $idFormaPago; ?>" value="<?php echo $idFormaPago; ?>" required>
                        <label class="form-check-label" for="formaPago_<?php echo $idFormaPago; ?>"><?php echo $nombre; ?></label>
                    </div>
                </div>
            <?php 
            }
            ?>
            </div>    
            <div class="row">&nbsp;</div>
            <div class="row" id="bloque_banco" >
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-4"><h4>Datos del comprobante de Tarjetas o Transferencia</h4></div>
                </div>
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-2">
                        <label>Banco</label>
                        <select class="form-control" id="idBanco" name="idBanco">
                            <option value="">Seleccione banco</option>
                            <?php
                            foreach ($resBancos['datos'] as $banco) {                                        
                            ?>
                                <option value="<?php echo $banco['id']; ?>" <?php if ($idBanco == $banco['id']) { echo 'selected'; } ?> ><?php echo $banco['nombre']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Comprobante</label>
                        <input type="text" class="form-control" name="comprobante" id="comprobante" placeholder="Número de transacción o transferencia">
                    </div>        
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>