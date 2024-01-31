<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoLogic.php');
require_once ('../../dataAccess/notificacionLogic.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');

require_once('../../tcpdf/config/lang/spa.php');
require_once('../../tcpdf/tcpdf.php');

$continua = TRUE;
$mensaje = "";
$resultado = NULL;

if (isset($_POST['idNotificacionNota']) && $_POST['idNotificacionNota'] <> "") {
    $idNotificacionNota = $_POST['idNotificacionNota'];
} else {
    $idNotificacionNota = NULL;
    $mensaje .= 'Falta idNotificacionNota - ';
    $continua = FALSE;
}
if (isset($_POST['filtroDeudores']) && $_POST['filtroDeudores'] <> "") {
    $filtroDeudores = $_POST['filtroDeudores'];
} else {
    $filtroDeudores = NULL;
    $mensaje .= 'Falta filtroDeudores - ';
    $continua = FALSE;
}
if (isset($_POST['periodoDesde']) && $_POST['periodoDesde'] <> "") {
    $periodoDesde = $_POST['periodoDesde'];
} else {
    $periodoDesde = NULL;
    $mensaje .= 'Falta periodoDesde - ';
    $continua = FALSE;
}
if (isset($_POST['periodoHasta']) && $_POST['periodoHasta'] <> "") {
    $periodoHasta = $_POST['periodoHasta'];
} else {
    $periodoHasta = NULL;
    $mensaje .= 'Falta periodoHasta - ';
    $continua = FALSE;
}
if (isset($_POST['fechaVencimiento']) && $_POST['fechaVencimiento'] <> "") {
    $fechaVencimiento = $_POST['fechaVencimiento'];
} else {
    $fechaVencimiento = NULL;
    $mensaje .= 'Falta fechaVencimiento - ';
    $continua = FALSE;
}

if (isset($_POST['idColegiado']) && $_POST['idColegiado'] <> "") {
    $idColegiado = $_POST['idColegiado'];
    $resColegiado = obtenerColegiadoPorId($idColegiado);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiado = $resColegiado['datos'];
        $matricula = $colegiado['matricula'];
        $apellidoNombre = trim($colegiado['apellido']).', '.trim($colegiado['nombre']);
        $sexo = $colegiado['sexo'];
    } else {
        $matricula = NULL;
        $mensaje .= 'Falta idColegiado - ';
        $continua = FALSE;
    }
}

if ($continua) {
    $resDeudores = obtenerDeudoresParaNotificar($matricula);
    if (isset($matricula) && $matricula > 0) {
        $porMatricula = " AND c.Matricula = ".$matricula;
    } else {
        $porMatricula = "";
    }


}
?>
<body onLoad="document.forms['myForm'].submit()">
    <form name="myForm"  method="POST" action="../certificacion_firma_nueva.php?idColegiado=<?php echo $idColegiado.'&id='.$idConstanciaFirma; ?>">
    </form>
</body>
