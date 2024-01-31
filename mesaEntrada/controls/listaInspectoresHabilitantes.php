<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';
?>
<div id="page-wrap" style="height: 680px">
    <br/>
    <div id="titulo">
        <h3>Listado de Inspectores</h3>
    </div>
    <br /><br />
    <div id="tabs">
        <ul>
            <li><a href="mostrarListadoInspectores.php?st=A">Inspectores Activos</a></li>
            <li><a href="mostrarListadoInspectores.php?st=B">Inspectores Históricos</a></li>
        </ul>
    </div>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>