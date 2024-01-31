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

    $notasIncluyenMovimiento = obtenerNotasIncluyenMovimiento();
    ?>
    <div id="page-wrap">
        <div id="titulo">
            <h3>Movimientos Otros Distritos</h3>
        </div>
        <br><br>
        <div>
            <table id="tablaEstadisticas">
                <tr>
                    <td><h4>Id</h4></td>
                    <td><h4>Remitente</h4></td>
                    <td><h4>Tema</h4></td>
                    <td><h4>Fecha</h4></td>
                    <td><h4>Detalle</h4></td>
                </tr>
                <?php
                if (!$notasIncluyenMovimiento) {
                    ?>
                    <tr>
                        <td colspan="5"><span class="mensajeERROR">Hubo un error en la base de datos.</span></td>
                    </tr>
                    <?php
                } else {
                    if ($notasIncluyenMovimiento->num_rows == 0) {
                        ?>
                        <tr>
                            <td colspan="5"><span class="mensajeWARNING">No se encuentran notas que incluyan movimientos.</span></td>
                        </tr>
                        <?php
                    } else {
                        while ($row = $notasIncluyenMovimiento->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['IdRemitente']; ?></td>
                                <td><?php echo utf8_encode($row['NombreRemitente']); ?></td>
                                <td><?php echo utf8_encode($row['Tema']) ?></td>
                                <td><?php echo invertirFecha($row['FechaIngreso']) ?></td>
                                <td><a href="movimientosDistritosDetalle.php?idM=<?php echo $row['IdMesaEntrada']; ?>&idR=<?php echo $row['IdRemitente']; ?>">Detalle</a></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </table>
        </div>
        <br/><br/>

    </div>
    <?php
    include_once '../html/pie.html';
    ?>
</body>
</html>