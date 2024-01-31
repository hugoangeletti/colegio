<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    if(isset($_GET['iME']))
    {
        $idMesaEntrada = $_GET['iME'];
        $datofechaIngreso = obtenerFechaMesaEntrada($idMesaEntrada);
        if(!$datofechaIngreso)
        {
            $fechaIngreso = "";
        }
        else
        {
            $fechaIngreso = $datofechaIngreso -> fetch_assoc();
        }
        $fechaIngresoInvertir = explode("-", $fechaIngreso['FechaIngreso']); 
        $fecha = $fechaIngresoInvertir[2]."/".$fechaIngresoInvertir[1]."/".$fechaIngresoInvertir[0];
    }
    else
    {
        $idMesaEntrada = obtenerNumeroHojaRuta();
        
        if(!$idMesaEntrada)
        {
            $idMesaEntrada = "";
        }
        else
        {
            $datoId = $idMesaEntrada -> fetch_assoc();
            $idMesaEntrada = $datoId['IdMesaEntrada'];
        }
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
    <body onload="window.print()">
<table class="encabezadoHojaRuta">
    <td>
        <img src="../images/logosh.gif" width="100" height="100" longdesc="Colegio de Medicos - Distrito I" /><br />
        <h2> Colegio de M&eacute;dicos <br />
             Provincia  de Buenos Aires <br />
             Distrito I </h2><br />
             <h4>Av. 51 Nº 723 - Tel. 425-6311 - LA PLATA</h4>
    </td>
    <td class="tdHDR">
        <h1>HOJA DE RUTA</h1><br />
        <h2>Nº <?php echo trim(rellenarCeros($idMesaEntrada, 8)) ?></h2><br /><br />
        <h2>Fecha: <?php if(isset($fecha)){ echo $fecha;} else { echo date("d/m/Y"); } ?></h2>
        <br />
    </td>
</table>
        <h2 style="text-align: center">Solicitud de Matrícula J</h2>
        <?php
        $consultaDatoMesaEntrada = obtenerMatriculaJPorId($idMesaEntrada);
    
        if(!$consultaDatoMesaEntrada)
        {
            die("Hubo un error en el sistema.");
        }
        else
        {
            if($consultaDatoMesaEntrada -> num_rows == 0)
            {
                die("El sistema no registra movimiento en la mesa de entrada.");
            }
            else
            {
                $datoMesaEntrada = $consultaDatoMesaEntrada -> fetch_assoc();
                if(!is_null($datoMesaEntrada['IdColegiado']))
                {
                    $consultaDatoColegiado = obtenerColegiadoPorId($datoMesaEntrada['IdColegiado']);

                    if(!$consultaDatoColegiado)
                    {
                        die("Hubo un error en el sistema.");
                    }
                    else
                    {
                        if($consultaDatoColegiado -> num_rows == 0)
                        {
                            die("El sistema no registra al colegiado que desea obtener.");
                        }
                        else
                        {
                            $datoColegiado = $consultaDatoColegiado -> fetch_assoc();
                            $okey = true;
                            $matricula = $datoColegiado['Matricula'];
                            require_once 'mostrarColegiadoImprimir.php';
                            echo "<br>";
                            ?>
            <table>
                <tr>
                    <td><b>Observaciones:</b></td>
                    <td><?php echo $datoMesaEntrada['Observaciones'] ?></td>
                </tr>
            </table>
                            <?php
                        }
                    }
                }
                else
                {
                    $consultaDatoRemitente = obtenerRemitentePorId($datoMesaEntrada['IdRemitente']);

                    if(!$consultaDatoRemitente)
                    {
                        die("Hubo un error en el sistema.");
                    }
                    else
                    {
                        if($consultaDatoRemitente -> num_rows == 0)
                        {
                            die("El sistema no registra al remitente que desea obtener.");
                        }
                        else
                        {
                            $datoRemitente = $consultaDatoRemitente -> fetch_assoc();
                            ?>
            <table>
                <tr>
                    <td><b>Remitente: </b></td>
                    <td><?php echo $datoRemitente['Nombre']; ?></td>
                </tr>
            </table>
            <br/>
                            <?php
                        }
                    }
                }
            }
        }
        ?>
<table class="cuerpoHojaRuta">
    <th class="encabezadoTabla">Decisión de la Mesa Directiva</th>
    <th class="encabezadoTabla">Firma</th>
    <tr class="cuerpoTabla">
        <td class="tdIzq"></td>
        <td></td>
    </tr>
</table>
<div class="lineaBajoTabla"></div>
<p class="textoBajoTabla">Realizó: <?php echo $datoMesaEntrada['Usuario']; //$_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
<?php 
    // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
    date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<p> 
    Emitido el: <?php echo date("d/m/Y h:i:s a") ?>
</p>
</body>
</html>