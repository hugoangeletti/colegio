<?php
$url_ConsultaGeneral = "colegiado_consulta.php?idColegiado=".$idColegiado;
$url_ConsultaTesoreria = "colegiado_tesoreria.php?idColegiado=".$idColegiado;
$url_ConsultaDomicilios = "colegiado_domicilio.php?idColegiado=".$idColegiado;
$url_ConsultaEspecialista = "colegiado_especialista.php?idColegiado=".$idColegiado;
$url_ConsultaCertificados = "colegiado_certificados.php?idColegiado=".$idColegiado;
$url_ConsultaRecetarios = "colegiado_recetarios.php?idColegiado=".$idColegiado;
$url_ConsultaNovedades = "colegiado_novedades.php?idColegiado=".$idColegiado;
$url_ConsultaFap = "colegiado_fap.php?idColegiado=".$idColegiado;
$url_Consultorios = "colegiado_consultorios.php?idColegiado=".$idColegiado;
$url_Sanciones = "colegiado_sanciones.php?idColegiado=".$idColegiado;
$url_DatosProfesionales = "colegiado_datos_profesionales.php?idColegiado=".$idColegiado;
$url_Rematriculacion = "colegiado_rematriculacion.php?idColegiado=".$idColegiado;
$url_Expedientes = "colegiado_expedientes.php?idColegiado=".$idColegiado;
require_once ('../dataAccess/verificacionColegiadoLogic.php');

$atencion = FALSE;
$mensajeAtencion = "";
$tieneTituloRetirar = tieneTituloEspecialistaParaRetirar($idColegiado);
if ($tieneTituloRetirar['estado']){
    $atencion = TRUE;
    $mensajeAtencion .= "Tiene título de especialista para retirar.<br><br>"; 
}
$tieneCostas = tieneCostas($idColegiado);
if ($tieneCostas['estado']){
    $atencion = TRUE;
    $mensajeAtencion .= "Tiene costas impagas. Galenos: ".$tieneCostas['costas']."<br><br>"; 
}
$tieneDocumentacionRetirar = tieneDocumentacionParaRetirar($idColegiado);
if ($tieneDocumentacionRetirar['estado']){
    $atencion = TRUE;
    foreach ($tieneDocumentacionRetirar['datos'] as $dato) {
        $mensajeAtencion .= "Tiene ".$dato['tipoDocumentacionRetiro']." para retirar.<br><br>"; 
    }
}
$tieneExpediente = tieneExpediente($idColegiado);
if ($tieneCostas['estado']){
    $atencion = TRUE;
    $mensajeAtencion .= "Tiene costas impagas. Galenos: ".$tieneCostas['costas']."<br><br>"; 
}

?>
<div class="row alert-info">
    <div class="col-md-9">
        <ul class="nav nav-pills" role="tablist">
            <li class="<?php if ($_SESSION['menuColegiado'] == "Consulta") { ?>active <?php } ?>"><a href="<?php echo $url_ConsultaGeneral; ?>">Datos del colegiado</a></li>
            <?php 
            if ($muestraMenuCompleto) {
            ?>
                <li class="<?php if ($_SESSION['menuColegiado'] == "Especialista") { ?>active <?php } ?>"><a href="<?php echo $url_ConsultaEspecialista; ?>">Especialista</a></li>
                <?php 
                if (!$_SESSION['user_entidad']['soloConsulta']) { 
                ?>                      
                    <li class="<?php if ($_SESSION['menuColegiado'] == "Certificados") { ?>active <?php } ?>"><a href="<?php echo $url_ConsultaCertificados; ?>">Certificados</a></li>
                    <li class="<?php if ($_SESSION['menuColegiado'] == "Recetarios") { ?>active <?php } ?>"><a href="<?php echo $url_ConsultaRecetarios; ?>">Recetarios</a></li>
                <?php 
                }
                ?>
                <li class="<?php if ($_SESSION['menuColegiado'] == "Datos en FAP") { ?>active <?php } ?>"><a href="<?php echo $url_ConsultaFap; ?>">Datos en FAP</a></li>
                <li class="<?php if ($_SESSION['menuColegiado'] == "Consultorios") { ?>active <?php } ?>"><a href="<?php echo $url_Consultorios; ?>">Consultorios</a></li>
                <?php 
                if (!$_SESSION['user_entidad']['soloConsulta']) { 
                ?>                      
                    <li class="<?php if ($_SESSION['menuColegiado'] == "Sanciones") { ?>active <?php } ?>"><a href="<?php echo $url_Sanciones; ?>">Sanciones</a></li>
                <?php 
                }
                ?>
                <?php 
                if ($tieneExpediente) {
                ?>
                    <li class="<?php if ($_SESSION['menuColegiado'] == "Expedientes") { ?>active <?php } ?>"><a href="<?php echo $url_Expedientes; ?>">Expedientes</a></li>
                <?php 
                }
                ?>
                <!--<li class="<?php if ($_SESSION['menuColegiado'] == "DatosProfesionales") { ?>active <?php } ?>"><a href="<?php echo $url_DatosProfesionales; ?>">Datos Profesionales</a></li>-->
            <?php 
            } else {
            ?>
                &nbsp;<b style="font-size: x-large;">Debe cargar el título digital para poder realizar otro trámite.</b>
            <?php 
            }
            ?>
        </ul>
    </div>  
    <div class="col-md-1">
        <?php
        if ($atencion){
        ?>
            <form  method="POST" action="colegiado_consulta.php">
                <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#myModal">ATENCION </button>
                <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
            </form>
        <?php
        }
        ?>
    </div>  
    <div class="col-md-2 text-right">
        <?php 
        if (!$_SESSION['user_entidad']['soloConsulta']) { 
        ?>                      
            <button type="submit" class="btn btn-toolbar btn-success" data-toggle="modal" data-target="#editarModal">Actualizar Datos </button>
        <?php 
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="background-color: #428bca;"></div>
</div>

