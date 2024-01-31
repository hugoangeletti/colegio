<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/cajaDiariaLogic.php');
require_once ('../../dataAccess/mesaEntradaEspecialistaLogic.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');

$continua = TRUE;
$mensaje = "";
$resultado = NULL;
/*
if (isset($_POST['listaIdMesaEntrada']) && $_POST['listaIdMesaEntrada'] <> "") {
    $listaIdMesaEntrada = $_POST['listaIdMesaEntrada'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta listaIdMesaEntrada. ";
}
*/

if (isset($_POST['idColegiado']) && $_POST['idColegiado'] <> "") {
    $idColegiado = $_POST['idColegiado'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta colegiado. ";
}
if (isset($_POST['generarRecibo']) && $_POST['generarRecibo']) {
    $generarRecibo = $_POST['generarRecibo'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta generarRecibo. ";
}
if (isset($_POST['tipoRecibo']) && $_POST['tipoRecibo']) {
    $tipoRecibo = $_POST['tipoRecibo'];
    $generarReciboPP = NULL;
    if ($tipoRecibo == "CUOTAS") {
        if (isset($_POST['generarReciboPP']) && $_POST['generarReciboPP']) {
            $generarReciboPP = $_POST['generarReciboPP'];
        }
    }
} else {
    $continua = FALSE;
    $mensaje .= "Falta tipoRecibo. ";
}

if ($continua) {
    $resultado = generarReciboCajaDiaria($idColegiado, $tipoRecibo, $generarRecibo, $generarReciboPP);
} else {
    $resultado['mensaje'] = $mensaje;
    $resultado['estado'] = FALSE;
}
/*
var_dump($_POST);
echo '<br>';
var_dump($resultado);
exit;
*/
?>
<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
    ?>
        <form name="myForm"  method="POST" action="../cajadiaria_especialistas_listado.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../cajadiaria_especialistas_recibo.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
            <input type="hidden"  name="idColegiado" id="idColegiado" value="<?php echo $idColegiado;?>">
            <input type="hidden"  name="listaIdMesaEntrada" id="listaIdMesaEntrada" value="<?php echo $listaIdMesaEntrada;?>">
        </form>
    <?php
    }
    ?>
</body>


