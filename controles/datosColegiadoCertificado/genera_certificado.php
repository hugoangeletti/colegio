<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoLogic.php');
require_once ('../../dataAccess/colegiadoCertificadosLogic.php');
require_once ('../../dataAccess/colegiadoContactoLogic.php');

$continua = TRUE;
if (isset($_POST['idColegiado']) && isset($_POST['idTipoCertificado'])) {
    $idColegiado = $_POST['idColegiado'];
    $idTipoCertificado = $_POST['idTipoCertificado'];

    //si es para especialista, verifico que esté seleccioanda la especialidad
    if ($idTipoCertificado == 3) {
        if (isset($_POST['idColegiadoEspecialista'])) {
            $idColegiadoEspecialista = $_POST['idColegiadoEspecialista'];
        } else {
            $resultado['mensaje'] = "MAL ESPECIALIDAD";
            $continua = FALSE;
        }
    } else {
        $idColegiadoEspecialista = NULL;
    }
    
    //si es para cambio de distrito, verifico que venga seleccioando el distrito y la nota
    if ($idTipoCertificado == 1) {
        if (isset($_POST['distrito']) && isset($_POST['idNotaCambioDistrito'])) {
            $distrito = $_POST['distrito'];
            $idNotaCambioDistrito = $_POST['idNotaCambioDistrito'];
        } else {
            $resultado['mensaje'] = "MAL DISTRITO";
            $continua = FALSE;
        }
    } else {
        $distrito = NULL;
        $idNotaCambioDistrito = NULL;
    }

    //si envia por mail, verifica que esta cargado el mail
    if (isset($_POST['enviaMail'])) {
        $enviaMail = $_POST['enviaMail'];
        if ($enviaMail == 'S' && isset($_POST['mail'])) {
            $mail = $_POST['mail'];
//            $mailOriginal = $_POST['mailOriginal'];
//            if ($mail <> $mailOriginal) {
//                //actualizo contacto
//                $resContacto = modificarMail($idColegiado, $mail);
//            }
        } else {
            $mail = NULL;
        }
    } else {
        $enviaMail = 'N';
    }

    $conFirma = $_POST['conFirma'];
    $presentado = strtoupper($_POST['presentado']);
    $estadoConTesoreria = $_POST['estadoConTesoreria'];
    $cuotasAdeudadas = $_POST['cuotasAdeudadas'];
    $conLeyendaTeso = $_POST['conLeyendaTeso'];
    $codigoDeudor = $_POST['codigoDeudor'];
    $tipoCertificado = $_POST['tipoCertificado'];
} else {
    $resultado['mensaje'] = "ERROR EN LOS DATOS INGRESADOS";
    $continua = FALSE;
}

if ($continua){
    $resultado = agregarSolicitudCertificado($idColegiado, $idTipoCertificado, $presentado, $distrito, $codigoDeudor, $cuotasAdeudadas, $idNotaCambioDistrito, $conFirma, $conLeyendaTeso, $idColegiadoEspecialista, $enviaMail, $mail);
    if ($resultado['estado']) {
        //imprimo el certificado
        $idCertificado = $resultado['idCertificado'];
    }
} else {
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-error";
}

?>
<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
        //imprime el certificado
    ?>
        <form name="myForm" method="POST" action="imprimir_certificado.php?idColegiado=<?php echo $idColegiado;?>&idCertificado=<?php echo $idCertificado; ?>"></form>
    <?php
    } else {
        //vuelve al formulario de solicitud por error
    ?>
        <form name="myForm" method="POST" action="../colegiado_certificados_alta.php?idColegiado=<?php echo $idColegiado;?>">
            <input type="hidden" name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden" name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden" name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
            <input type="hidden"  name="mail" id="mail" value="<?php echo $mail;?>">
            <input type="hidden"  name="mailOriginal" id="mailOriginal" value="<?php echo $mailOriginal;?>">
            <input type="hidden"  name="idTipoCertificado" id="idTipoCertificado" value="<?php echo $idTipoCertificado;?>">
            <input type="hidden"  name="idEspecialidad" id="idEspecialidad" value="<?php echo $idEspecialidad;?>">
            <input type="hidden"  name="distrito" id="distrito" value="<?php echo $distrito;?>">
            <input type="hidden"  name="idNotaCambioDistrito" id="idNotaCambioDistrito" value="<?php echo $idNotaCambioDistrito;?>">
            <input type="hidden"  name="enviaMail" id="enviaMail" value="<?php echo $enviaMail;?>">
            <input type="hidden"  name="presentado" id="presentado" value="<?php echo $presentado;?>">
            <input type="hidden"  name="estadoConTesoreria" id="estadoConTesoreria" value="<?php echo $estadoConTesoreria;?>">
            <input type="hidden"  name="cuotasAdeudadas" id="cuotasAdeudadas" value="<?php echo $cuotasAdeudadas;?>">
            <input type="hidden"  name="conFirma" id="conFirma" value="<?php echo $conFirma;?>">
            <input type="hidden"  name="conLeyendaTeso" id="conLeyendaTeso" value="<?php echo $conLeyendaTeso;?>">
            <input type="hidden"  name="codigoDeudor" id="codigoDeudor" value="<?php echo $codigoDeudor;?>">
            <input type="hidden"  name="tipoCertificado" id="tipoCertificado" value="<?php echo $tipoCertificado;?>">
        </form>
    <?php
    }
    ?>
</body>

