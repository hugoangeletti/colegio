<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $_GET['lH'] = "Y";
?>
<?php
include_once 'head_config.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';
?>
<div id="page-wrap">
    <br/>
    <div id="titulo">
        <h3>Habilitaciones Solicitadas</h3>
    </div>
    <br /><br />
    <?php require_once 'tablaHabilitaciones.php'; ?>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>