<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoEspecialistaLogic.php');
require_once ('../../dataAccess/resolucionesLogic.php');

$continua = TRUE;
$accion = $_POST['accion'];
$mensaje = "";
if (isset($_POST['idColegiadoEspecialista']) && $_POST['idColegiadoEspecialista'] <> "") {
    $idColegiadoEspecialista = $_POST['idColegiadoEspecialista'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta idColegiadoEspecialista";
    $tipoMensaje = 'alert alert-danger';
}
$idTipoEspecialista = NULL;
if (isset($_POST['idTipoEspecialista']) && $_POST['idTipoEspecialista'] <> "") {
    $idTipoEspecialista = $_POST['idTipoEspecialista'];
    if ($idTipoEspecialista == EXCEPTUADO_ART_8) {
        if (isset($_POST['inciso']) && $_POST['inciso'] <> "") {
            $inciso = $_POST['inciso'];
        } else {
            $inciso = FALSE;
            $tipoMensaje = 'alert alert-danger';
            $mensaje = "Falta inciso.";
        }
    } else {
        $inciso = NULL;
    }
} else {
    $continua = FALSE;
    $mensaje .= "Falta idTipoEspecialista";
    $tipoMensaje = 'alert alert-danger';
}
if (isset($_POST['idColegiado']) && $_POST['idColegiado'] <> "") {
    $idColegiado = $_POST['idColegiado'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta idColegiado";
    $tipoMensaje = 'alert alert-danger';
}
if ($continua) {
    switch ($accion) 
    {
        case '1':
            //$resultado = agregarColegiadoEspecialista();
            break;
        case '3':
            $resultado = editarColegiadoEspecialista($idColegiadoEspecialista, $idTipoEspecialista, $inciso);
            break;
        case '2':
            //$resultado = borrarColegiadoEspecialista($idColegiadoEspecialista);
            break;
        default:
            break;
    }
} else {
    $resultado['clase'] = $tipoMensaje;
    $resultado['mensaje'] = $mensaje;
    $resultado['icono'] = "";
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
        <form name="myForm"  method="POST" action="../colegiado_especialista.php?idColegiado=<?php echo $idColegiado; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../colegiado_especialista_editar.php?id=<?php echo $idColegiadoEspecialista; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
            <input type="hidden"  name="inciso" id="inciso" value="<?php echo $inciso;?>">
            <input type="hidden"  name="accion" id="accion" value="<?php echo $accion;?>">
        </form>
    <?php
    }
    ?>
</body>

