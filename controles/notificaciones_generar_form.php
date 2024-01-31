<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/notificacionNotaLogic.php');

$continuar = TRUE;
$idUsuario = $_SESSION['user_id'];
$idNotificacionNota = 1; //notificacion de deuda de colegiacion
$filtroDeudores = 'T'; //todos los deudores
$filtroDeudoresNombre = 'Todos los deudores';
$tipoEnvio = 'A'; //tipo de envio por mail y por correo
$tipoEnvioNombre = 'Tipo de envío por mail y por correo';
$periodoDesde = 0; //periodo desde el inicio
$periodoHasta = $_SESSION['periodoActual']; //periodo hasta el actual
$date = new DateTime('now');
$date->modify('last day of this month');
$fechaVencimiento = $date->format('Y-m-d');
$idColegiado = NULL;

$resTipoNota = obtenerNotificacionNotaPorIdNotificacion($idNotificacionNota);
if ($resTipoNota['estado']) {
    $tipoNota = $resTipoNota['datos'];
    $temaNotificacion = $tipoNota['tema'];
} else {
    $continuar = FALSE;
}

?>
<div class="col-md-12 alert alert-info">
    <div class="row">
        <div class="col-md-9">
            <h4>Generar Notificaciones de deuda de colegiaciòn</h4>
        </div>
        <div class="col-md-3 text-left">
            <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="notificaciones.php">
                <button type="submit"  class="btn btn-info" >Volver</button>
            </form>
        </div>
    </div>
</div>
<?php 
if ($continuar) {
?>
    <div class="panel panel-info">
        <div class="panel-body">
            <form class="form-control" id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="datosNotificacion/generar_notificacion.php" >
                <div class="row">
                    <div class="col-md-2">
                        <label>Tipo de notificación: &nbsp;</label>
                        <input type="text" class="form-control" name="idNotificacionNota" id="idNotificacionNota" value="<?php echo $idNotificacionNota; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Filtro deudores: &nbsp;</label>
                        <input type="text" class="form-control" name="filtroDeudores" id="filtroDeudores" value="<?php echo $filtroDeudores; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>Período desde: &nbsp;</label>
                        <input type="number" class="form-control" name="periodoDesde" id="periodoDesde" value="<?php echo $periodoDesde; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Período hasta: &nbsp;</label>
                        <input type="number" class="form-control" name="periodoHasta" id="periodoHasta" value="<?php echo $periodoHasta; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Fecha vencimiento: &nbsp;</label>
                        <input type="date" class="form-control" name="fechaVencimiento" id="fechaVencimiento" value="<?php echo $fechaVencimiento; ?>">
                    </div>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit"  class="btn btn-default">Confirma notificación</button>
                        <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php
} else {
?>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="alert alert-warning">ACCESO INCORRECTO</div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row text-center">
        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="notificaciones.php">
            <button type="submit"  class="btn btn-info" >Volver</button>
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