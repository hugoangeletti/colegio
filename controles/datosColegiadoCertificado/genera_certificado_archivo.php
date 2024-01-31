<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoLogic.php');
require_once ('../../dataAccess/colegiadoCertificadosLogic.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');

$continua = TRUE;
if (isset($_POST['archivo']) && isset($_POST['idTipoCertificado'])) {
    $archivo = $_POST['archivo'];
    $idTipoCertificado = $_POST['idTipoCertificado'];

    $enviaMail = 'N';
    $conFirma = 'S';
    $presentado = $_POST['presentado'];
    $conLeyendaTeso = 'S';
} else {
    $resultado['mensaje'] = "ERROR EN LOS DATOS INGRESADOS";
    $continua = FALSE;
}

if ($continua){
    $certificados = array();
    $fp = fopen ('../'.$archivo,"r");
    while ($data = fgetcsv ($fp, 1000, ";")) {
        $num = sizeof($data);
        $apellidoNombreSolicitado = $data[0];
        $matricula = trim($data[1]);
        $continua = TRUE;
        $resColegiado = obtenerIdColegiado($matricula);
        if ($resColegiado['estado']) {
            $idColegiado = $resColegiado['idColegiado'];
            $resColegiado = obtenerColegiadoPorId($idColegiado);
            if ($resColegiado['estado']) {
                $colegiado = $resColegiado['datos'];
                $estadoMatricular = trim(obtenerDetalleTipoEstado($colegiado['tipoEstado'])).' - '.$colegiado['movimientoCompleto'];
                $estadoTesoreria = "";
                $resEstadoTeso = estadoTesoreriaPorColegiado($idColegiado, $_SESSION['periodoActual']);
                if ($resEstadoTeso['estado']){
                    $codigoDeudor = $resEstadoTeso['codigoDeudor'];
                    $cuotasAdeudadas = $resEstadoTeso['cuotasAdeudadas'];
                    $resEstadoTesoreria = estadoTesoreria($codigoDeudor);
                    if ($resEstadoTesoreria['estado']){
                        $estadoTesoreria .= $resEstadoTesoreria['estadoTesoreria'];
                    } else {
                        $estadoTesoreria.= $resEstadoTesoreria['mensaje'];
                    }
                } else {
                    $estadoTesoreria = $resEstadoTeso['mensaje'];
                }
                            
                $fechaAlta = cambiarFechaFormatoParaMostrar($colegiado['fechaMatriculacion']);
            } else {
                $continua = FALSE;
            }
        } else {
            $continua = FALSE;
        }
        if ($continua) {
            $resultado = agregarSolicitudCertificado($idColegiado, $idTipoCertificado, $presentado, null, $codigoDeudor, $cuotasAdeudadas, null, $conFirma, $conLeyendaTeso, null, 'N', null);

            if ($resultado['estado']) {
                //cargo el idCertificado en el arreglo para imprimir
                //$row = array (
                //    'idCertificado' => $resultado['idCertificado']
                // );
                array_push($certificados, $resultado['idCertificado']);
            }
        }
    }
    $resultado['estado'] = TRUE;
} else {
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-error";
    $resultado['estado'] = FALSE;
}

?>
<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {

        $certificadosSerial = serialize($certificados);
        
    ?>
        <form name="myForm" method="POST" action="imprimir_certificado_archivo.php">
            <input type="hidden" name="certificados" id="certificados" value="<?php echo $certificadosSerial; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm" method="POST" action="../listado_por_entidad.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        </form>
    <?php
    }
    ?>
    <a href="../administracion.php">VOLVER</a>
</body>

