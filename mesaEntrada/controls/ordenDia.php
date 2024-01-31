<?php
include_once 'head_config.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';
    ?>
    <script>
        $(function() {
            $(".agregarOrden").click(function() {
                $("#page-wrap").load("ordenDiaFormOrden.php");
            });
        });
    </script>
    <div id="page-wrap">
        <br/>
        <div id="titulo">
            <h3>Listado de Órdenes</h3>
        </div>
        <br/><br/>
        <input type="button" class="agregarOrden" value="Agregar Orden" />
        <br>
        <div id="tabs" style="max-height: 2000px;">
            <ul>
                <li><a href="listadoOrdenes.php">Ver Todos</a></li>
            </ul>
        </div>
    </div>
    <?php
    include_once '../html/pie.html';
    ?>
</body>
</html>