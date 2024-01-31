<?php
include_once 'head_config.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';

    if (isset($_GET['idM']) && ($_GET['idM'] != "")) {
        $idMesaEntrada = $_GET['idM'];
    } else {
        $idMesaEntrada = -1;
    }

    if (isset($_GET['idR']) && ($_GET['idR'] != "")) {
        $idRemitente = $_GET['idR'];
    } else {
        $idRemitente = -1;
    }

    $remitente = obtenerRemitentePorId($idRemitente);

    if ($remitente) {
        if ($remitente->num_rows > 0) {
            $datoRemitente = $remitente->fetch_assoc();
            $nombreRemitente = $datoRemitente['Nombre'];
        }
    }
    
    ?>
    <script>
        
        $(function() {
            $(".agregarMovimiento").click(function() {
                var idM = $(this).attr("id");
                $.ajax({
                    url: "movimientosDistritosFormNuevoMovimiento.php?idM=" + idM,
                    success: function(msg) {
                        $("#modalAgregarMovimiento").html(msg);
                    }
                });
                $("#modalAgregarMovimiento").dialog({
                    closeText: "cerrar",
                    modal: true,
                    minWidth: 680,
                    minHeight: 250,
                    width: 880,
                    maxHeight: 500,
                    maxWidth: 1000,
                    resizable: true,
                    title: "Agregar Movimiento"
                });
            });
        });
        $(function() {
            $(".imprimir").click(function() {
                var id = $(this).attr("id");
                var idRemitente = $(this).attr("data");
                $.ajax({
                    success: function() {
                        window.open('movimientosDistritosPlanilla.php?idM=' + id + '&idR=' + idRemitente, '_blank');
                    }
                });
            });
        });
    </script>
    <div id="page-wrap" style="max-height: 5000px;">
        <div id="titulo">
            <h3>Movimientos Otros Distritos</h3>
            <h4>Nota Nº <?php echo $idMesaEntrada ?><?php
                if (isset($nombreRemitente)) {
                    echo " | " . utf8_encode($nombreRemitente);
                }
                ?></h4>
        </div>
        <br><br>
        <div>
            <input id="<?php echo $idMesaEntrada ?>" class="agregarMovimiento" type="button" value="Agregar Movimiento" style="margin-right: 100px;">
            <a class="imprimir" id="<?php echo $idMesaEntrada; ?>" data="<?php echo $idRemitente ?>">Imprimir Listado</a>
            <br><br>
            <div id="tabs" style="max-width: 4000px">
                <ul>
                    <li><a href="vistaMovimientosDistritosDetalle.php?tipo=B&idM=<?php echo $idMesaEntrada ?>">Baja de Inscripción</a></li>
                    <li><a href="vistaMovimientosDistritosDetalle.php?tipo=O&idM=<?php echo $idMesaEntrada ?>">Otros Movimientos</a></li>
                </ul>
            </div>
        </div>
        <br/><br/>
        <input type="button" onclick="window.history.back();" value="Volver" />
    </div>
    <?php
    include_once '../html/pie.html';
    ?>
    <div id="modalAgregarMovimiento" style="display:none"></div>
</body>
</html>