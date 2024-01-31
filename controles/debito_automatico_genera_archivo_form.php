<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');

$periodo = $_SESSION['periodoActual'];
$continua = TRUE;
if (isset($_POST['tipoDebito']) && str_contains($_POST['tipoDebito'], "DCH")) {
    $tipoDebito = $_POST['tipoDebito'];
} else {
    $tipoDebito = NULL;
}
if (isset($_POST['fechaDebito']) && $_POST['fechaDebito'] <> "") {
    $fechaDebito = $_POST['fechaProceso'];
} else {
    $fechaDebito = sumarRestarSobreFecha(date('Y-m-d'), 1, 'day', '+');
}
?>
<div class="panel panel-info">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-12">
                <h4>Genera lote para débito automático</h4>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <?php
        if (isset($_POST['mensaje']) && $_POST['mensaje'] == "OK") {
        ?>
           <div class="row">
                <div class="col-md-6 text-left"><h4>El proceso de generación ha finalizado <b><?php echo $_POST['mensaje'] ?></b></h4></div>
            </div>
         <?php
        } else {
        ?>
        <div class="row">&nbsp;</div>
        <form id="datosDebitoAutomatico" autocomplete="off" name="datosDebitoAutomatico" method="POST" action="datosDebitoAutomatico/genera_archivo.php">
            <div class="row">
                <div class="col-md-2">
                    <label>Fecha débito: </label>
                    <input class="form-control" type="date" name="fechaDebito" id="fechaDebito" value="<?php echo $fechaDebito ?>" required>
                </div>
                <div class="col-md-2">
                    <label>Tipo débito: </label>
                    <select class="form-control" id="tipoDebito" name="tipoDebito" required>
                        <option value="" selected>Seleccion tipo de débito</option>
                        <option value="D" <?php if("D" == $tipoDebito) { echo 'selected'; } ?>>Tarjeta de Débito</option>
                        <option value="C" <?php if("C" == $tipoDebito) { echo 'selected'; } ?>>Tarjeta de Crédito</option>
                        <option value="H" <?php if("H" == $tipoDebito) { echo 'selected'; } ?>>CBU</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <br>
                    <button type="submit"  class="btn btn-success" onclick="waitingDialog.show('Generando Home Banking...');setTimeout(function () {waitingDialog.hide();}, 500000);">Generar liquidación </button>
                    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
                </div>
            </div>    
        </form>
        <?php
        }
        ?>
    </div>    
</div>
<?php
require_once '../html/footer.php';
?>
<script type="text/javascript"> 
    $(document).ready(function () { $('.dropdown-toggle').dropdown(); }); 
    
    var waitingDialog = waitingDialog || (function ($) {
    'use strict';

    // Creating modal dialog's DOM
    var $dialog = $(
        '<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
        '<div class="modal-dialog modal-m">' +
        '<div class="modal-content">' +
            '<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
            '<div class="modal-body">' +
                '<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
            '</div>' +
        '</div></div></div>');

    return {
        /**
         * Opens our dialog
         * @param message Custom message
         * @param options Custom options:
         *                options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
         *                options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
         */
        show: function (message, options) {
            // Assigning defaults
            if (typeof options === 'undefined') {
                options = {};
            }
            if (typeof message === 'undefined') {
                message = 'Loading';
            }
            var settings = $.extend({
                dialogSize: 'm',
                progressType: '',
                onHide: null // This callback runs after the dialog was hidden
            }, options);

            // Configuring dialog
            $dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
            $dialog.find('.progress-bar').attr('class', 'progress-bar');
            if (settings.progressType) {
                $dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
            }
            $dialog.find('h3').text(message);
            // Adding callbacks
            if (typeof settings.onHide === 'function') {
                $dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
                    settings.onHide.call($dialog);
                });
            }
            // Opening dialog
            $dialog.modal();
        },
        /**
         * Closes dialog
         */
        hide: function () {
            $dialog.modal('hide');
        }
    };

})(jQuery);
    
 </script>