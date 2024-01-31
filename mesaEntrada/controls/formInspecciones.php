<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    /*
     * S -> Habilitaciones Solicitadas
     * A -> Habilitaciones Asignadas
     * C -> Habilitaciones Confirmadas
     */
    if(isset($_GET['lH']))
    {
        switch ($_GET['lH'])
        {
            case "S":
                    $titulo = "Asignar Inspecciones";
                break;
            case "A":
                    $titulo = "Habilitaciones Asignadas";
                break;
            case "C":
                    $titulo = "Habilitaciones Confirmadas";
                break;
            case "M":
                    $titulo = "Habilitaciones por Matrícula";
                break;
        }
    }
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
        <h3><?php echo $titulo ?></h3>
    </div>
    <br /><br />
    <?php
    require_once 'buscarInspector.php';
    ?>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>