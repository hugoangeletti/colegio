<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
//require_once ('../dataAccess/cursoLogic.php');
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
        $tituloCajaDiaria = "Por Tipo de Pago";
        include_once 'encabezado_generar_recibo.php';
?>
    <div class="panel panel-info">
        <div class="panel-body">
            <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="datosCajaDiaria/generar_recibo.php">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-4">
                        <h4>Concepto a abonar.</h4>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $resTipos = ontenerTiposPagoParaRecibo();
                    if ($resTipos['estado']) {
                        $totalRecibo = 0;
                            foreach ($resTipos['datos'] as $row) {                                        
                                $importe = $row['importe'];
                                $idTipoPago = $row['id'];
                                $nombre = $row['nombre'];

                                //si no tiene importe o es certificacion de firma, no lo muestro
                                if ($importe == 0 || $idTipoPago == 62) continue;
                                ?>
                                <div class="col-md-6">
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input class="form-check-input" name="generarRecibo[]" type="checkbox" id="<?php echo $idTipoPago; ?>"
                                                   value="<?php echo $idTipoPago.'_'.$importe; ?>" 
                                                   onclick="cambiaTotalRecibo(<?php echo $importe ?>, <?php echo $totalRecibo; ?>, <?php echo $idTipoPago; ?>)">
                                            <label class="form-check-label" for="<?php echo $idTipoPago.'_'.$importe ?>"><?php echo $nombre; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" name="importe_<?php echo $idTipoPago; ?>" id="importe_<?php echo $idTipoPago; ?>" value="<?php echo $importe; ?>" readonly >
                                    </div>
                                </div>
                            <?php
                            }
                    }
                    ?>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4">
                        <h4>Total a abonar:</h4> 
                        <input type="text" name="totalRecibo" id="totalRecibo" value="<?php echo $totalRecibo; ?>" readonly>
                    </div>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row"><?php include 'cajadiaria_forma_pago.php'; ?></div>
                <div class="row">&nbsp;</div>
                <div class="row" id="conFirmaRecibo" style="display: none;">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-4">
                        <button type="submit"  class="btn btn-default">Confirma recibo</button>
                        <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                        <input type="hidden" name="tipoRecibo" id="tipoRecibo" value="TIPO_PAGO" />
                    </div>
                </div>
            </form>
            <div class="row">&nbsp;</div>
        </div>
    </div>
<?php
    } else {
        //debe buscar el colegiado
        ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <h4>Generar recibo de Colegiación</h4>
                <h5>Seleccione al colegiado/a</h5>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <?php 
        $link_form_origen = 'cajadiaria_tipo_pago_recibo.php';
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
    
    function cambiaTotalRecibo(importe, totalRecibo, idTipoPago)
    {
        var totalRecibo = parseInt(document.getElementById('totalRecibo').value);
        var valor = parseInt(totalRecibo);
        var importe = parseInt(importe);
        var x = document.getElementById('conFirmaRecibo');

        if (document.getElementById(idTipoPago).checked)
        {
            var valor = totalRecibo + importe;
        } else {
            var valor = totalRecibo - importe;
        }

        if (valor > 0) {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
        document.getElementById('totalRecibo').value = valor;
        
        return valor;
    }
</script>

