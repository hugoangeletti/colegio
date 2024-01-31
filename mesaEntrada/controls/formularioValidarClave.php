<?php
include_once 'head_config.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

//var_dump($_SESSION);

if (isset($_GET['menu']) && ($_GET['menu'] == "ok")) {
    $_SESSION['intentos'] = 0;
}

if (isset($_GET['continue']) && ($_GET['continue'])) {
    $continue = $_GET['continue'];
} else {
    $continue = "../";
}

if (isset($_GET['title']) && ($_GET['title'])) {
    $title = $_GET['title'];
} else {
    $title = "Formulario de Validación de Contraseña";
}

if ($_SESSION['idUsuario'] == 1) {
    header("Location: " . $continue);
    exit();
}
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';
    ?>
    <div id="page-wrap">
        <div id="titulo">
            <h3><?php echo $title; ?></h3>
        </div>
        <br/><br/>
        <form method="post" action="validarClave.php">
            <table>
                <tr>
                    <td><b>Contraseña:</b></td>
                    <td><input type="password" name="clave" required autofocus></td>
                    <td><input type="submit" value="Confirmar"></td>
                </tr>
            </table>
            <br><br>
            <span>Tiene hasta <?php echo 3 - $_SESSION['intentos']; ?> intentos, de lo contrario se deslogueará automáticamente.</span>
            <input type="hidden" name="continue" value="<?php echo $continue; ?>">
            <input type="hidden" name="title" value="<?php echo $title; ?>">
        </form>
    </div>
    <?php
    include_once '../html/pie.html';
    ?>
</body>
</html>