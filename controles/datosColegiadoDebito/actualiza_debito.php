<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoDebitosLogic.php');

$idColegiado = $_POST['idColegiado'];
$continua = TRUE;

if (isset($_POST['tipo']) && isset($_POST['idBanco'])) {
    $tipo = $_POST['tipo'];
    $tipoAnterior = $_POST['tipoAnterior'];
    $idBanco = $_POST['idBanco'];
    $incluyePP = $_POST['incluyePP'];
    $incluyeTotal = $_POST['incluyeTotal'];
    $numeroDocumento = NULL;
    $numeroTarjeta = NULL;
    $numeroCbu = NULL;
    $tipoCuenta = NULL;
    if ($tipo == 'C') {
        if (isset($_POST['numeroTarjeta']) && isset($_POST['numeroDocumento'])) {
            $numeroTarjeta = $_POST['numeroTarjeta'];
            $numeroDocumento = $_POST['numeroDocumento'];
        } else {
            $continua = FALSE;
            $tipoMensaje = 'alert alert-danger';
            $mensaje = "Faltan datos de la tarjeta, verifique.";
        }
    } else {
        if (isset($_POST['numeroCbu']) && isset($_POST['tipoCuenta'])) {
            $numeroCbu = $_POST['numeroCbu'];
            $tipoCuenta = $_POST['tipoCuenta'];
        } else {
            $continua = FALSE;
            $tipoMensaje = 'alert alert-danger';
            $mensaje = "Faltan datos del CBU, verifique.";
        }
    }
} else {
    $mensaje = 'Falta tipo de debito/banco';
    $continua = FALSE;
}
if ($continua){
    $resultado = agregarColegiadoDebito($idColegiado, $idBanco, $tipo, $numeroTarjeta, $numeroDocumento, $incluyePP, $incluyeTotal, $tipoAnterior, $numeroCbu, $tipoCuenta);
} else {
    $resultado['mensaje'] = "ERROR EN LOS DATOS INGRESADOS. ".$mensaje;
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-error";
}

?>

<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
    ?>
    <form name="myForm"  method="POST" action="../colegiado_debito_imprimir.php?idColegiado=<?php echo $idColegiado; ?>&tipo=<?php echo $tipo; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../colegiado_debito.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="glyphicon glyphicon-exclamation-sign">
            <input type="hidden"  name="clase" id="clase" value="alert alert-info">
            <input type="hidden"  name="idColegiado" id="idColegiado" value="<?php echo $idColegiado;?>">
            <input type="hidden"  name="tipo" id="tipo" value="<?php echo $tipo;?>">
            <input type="hidden"  name="tipoAnterior" id="tipoAnterior" value="<?php echo $tipoAnterior;?>">
            <input type="hidden"  name="idBanco" id="idBanco" value="<?php echo $idBanco;?>">
            <input type="hidden"  name="numeroTarjeta" id="numeroTarjeta" value="<?php echo $numeroTarjeta;?>">
            <input type="hidden"  name="numeroDocumento" id="numeroDocumento" value="<?php echo $numeroDocumento;?>">
            <input type="hidden"  name="numeroCbu" id="numeroCbu" value="<?php echo $numeroCbu;?>">
            <input type="hidden"  name="tipoCuenta" id="tipoCuenta" value="<?php echo $tipoCuenta;?>">
            <input type="hidden"  name="incluyePP" id="incluyePP" value="<?php echo $incluyePP;?>">
            <input type="hidden"  name="incluyeTotal" id="incluyeTotal" value="<?php echo $incluyeTotal;?>">
        </form>
    <?php
    }
    ?>
</body>

