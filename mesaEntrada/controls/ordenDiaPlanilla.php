<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    require_once '../dataAccess/ordenDiaLogic.php';
    
    $inicios = array(1, 8, 15, 22, 29, 36, 43, 50, 57, 64, 71, 78, 85, 92, 99);
    $fines = array(7, 14, 21, 28, 35, 42, 49, 56, 63, 70, 77, 84, 91, 98);
    
    
    if(isset($_GET['iOrden']))
    {
        if(isset($_GET['planilla']))
        {
            $idOrdenDia = $_GET['iOrden'];
            $planilla = $_GET['planilla'];
        }
    }
    $imprimir = obtenerMovimientosPorIdOrdenDiaPorPlanilla($idOrdenDia, $planilla);
    
    switch ($planilla)
    {
        case 1:
            $titulo = "Asuntos Internos";
            break;
        case 2:
            $titulo = "Notas Recibidas";
            break;
    }
    
    if(!$imprimir)
    {
        die("Hubo un error en el sistema.");
    }
    else
    {
        if($imprimir -> num_rows == 0)
        {
            die("No existe ninguna orden con esos parámetros.");
        }
        else
        {
            $consultaInfoOrden = obtenerOrdenPorId($idOrdenDia);
            $infoOrden = $consultaInfoOrden -> fetch_assoc();
            $i = 1;
            while($tabla = $imprimir -> fetch_assoc())
            {
                $length = strlen($i);
                //mb_substr($i, $length-1, $length)
                if(in_array($i, $inicios))
                {
                ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
<body onload="window.print();">
<table class="encabezadoPlanilla">
    <td>
        <img src="../images/logosh.gif" width="70" height="70" longdesc="Colegio de Medicos - Distrito I" /><br />
    </td>
    <td>
        <h4> Colegio de M&eacute;dicos Distrito I<br /></h4>
            Calle 51 Nº 723. La Plata - Tel/Fax: 425-6311.<br />
            E-Mail: <span class="subrayado">info@colmed1.org.ar</span><br />
            Web: <span class="subrayado">www.colmed1.org.ar</span>
    </td>
</table>
    <br />
<div id="tituloPlanilla">
    <h4><span class="subrayado">REUNIÓN DE MESA DIRECTIVA - COLEGIO DE MÉDICOS</span></h4>
    <h4><span class="subrayado">DISTRITO I - Período <?php if ((date("Y") . "-06-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-12-31"))) {$anioProximo = $infoOrden['Periodo'] + 1; $period = $infoOrden['Periodo']."/".$anioProximo;} elseif ((date("Y") . "-01-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-05-31"))) {$anio = $infoOrden['Periodo'] - 1; $period = $anio."/".$infoOrden['Periodo'];} if(isset($period)){echo $period;}else{echo $infoOrden['Numero'];} ?> (Nº <?php echo $infoOrden['Numero']; ?>)</span></h4>
    <h4><span class="subrayado"><?php echo pasarAMayuscula(obtenerNombreDia($infoOrden['Fecha']))." ".mostrarSoloDia($infoOrden['Fecha']); ?>
            DE <?php echo pasarAMayuscula(obtenerNombreMes($infoOrden['Fecha'])); ?> DE <?php echo mostrarSoloAnio($infoOrden['Fecha']) ?>.-</span></h4>
</div>
    <h4><span class="subrayado tituloPlantilla"><?php echo $titulo ?></span></h4>
    <table class="tablaPlanilla">
        <?php
                }
        ?>
        <tr> 
            <td><?php echo $tabla['TipoPlanilla'] ?>.<?php echo $i; echo " ME: ".$tabla['IdMesaEntrada'];?>.- <?php if(!is_null($tabla['Matricula'])){echo " ".utf8_encode($tabla['Apellido'])." ".utf8_encode($tabla['Nombres']);}else{echo utf8_encode($tabla['NombreRemitente']);} ?> 
                <?php if(!is_null($tabla['Tema'])){echo substr(utf8_encode($tabla['Tema']),0, 120);}else{if(!is_null($tabla['DetalleCompleto'])){echo substr(utf8_encode($tabla['DetalleCompleto']),0, 120);}} ?> <?php if(!is_null($tabla['Observaciones'])){echo substr(utf8_encode($tabla['Observaciones']),0,0);}else{echo "";} if(($tabla['Orden'] != 0)){echo " (&nbsp;".rellenarCeros($tabla['Orden'], 4)."&nbsp;) ";}?>
            </td>
            <td></td>
        </tr>
                <?php
                //mb_substr($i, $length-1, $length)
                if(in_array($i,$fines))
                {
                    $yaFinaliza = true;
                ?>
    </table>
    <br />
<p class="textoBajoTablaPlanilla">User: <?php echo $_SESSION['idUsuario']." - ".$_SESSION['user']?></p>
<?php 
    // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
    date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<p>Emitido el: <?php echo date("d/m/Y h:i:s a") ?></p>
</body>
</html>
<div class="saltopagina"></div>
                <?php
                }
                else
                {
                    $yaFinaliza = false;
                }
                $i++;
            }
            
            if(!$yaFinaliza)
            {
            
        ?>

    </table>
    <br/>
<p class="textoBajoTablaPlanilla">User: <?php echo $_SESSION['idUsuario']." - ".$_SESSION['user']?></p>
<?php 
    // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
    date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<p>Emitido el: <?php echo date("d/m/Y h:i:s a") ?></p>
</body>
</html>
            <?php
            }
        }
    }
?>
