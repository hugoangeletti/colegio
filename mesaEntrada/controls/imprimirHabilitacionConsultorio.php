<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    $aColegiado = array();
    if(isset($_GET['iME']))
    {
        $idMesaEntrada = $_GET['iME'];
        $consultaInfoHabilitacion = obtenerHabilitacionConsultorioPorId($_GET['iME']);
        
    }
    else
    {
        $consultaIdMesaEntrada = obtenerNumeroHojaRuta();
        
        $idMesaEntrada = $consultaIdMesaEntrada -> fetch_assoc();
        if(!$idMesaEntrada)
        {
            $idMesaEntrada = "";
        }
        else
        {
            $consultaInfoHabilitacion = obtenerHabilitacionConsultorioPorId($idMesaEntrada['IdMesaEntrada']);
        }
    }
    $infoHabilitacion = $consultaInfoHabilitacion -> fetch_assoc();
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
    <body onload="window.print()">
        <div id='container'>
            <div id='titulo'>
                <h3>FORMULARIO DE HABILITACIÓN DE CONSULTORIO</h3>
            </div>
            <br />
            <h3><p class='textDerecha'>Nº <?php echo rellenarCeros($infoHabilitacion['IdMesaEntradaConsultorio'],8) ?></p></h3>
            <br />
        <p class='textDerecha'>La Plata, <?php echo date("d")." de ".obtenerNombreMes(date("Y-m-d"))." de ".date("Y") ?></p>
        <br /><br />
        <div class='textIzquierda'>
            <span>Sr. Presidente del</span>
            <br />
            <span><b>Colegio de Médicos - DISTRITO I</b></span>
            <br />
            <span><u><i>S/D</i></u></span>
        </div>
        <br /><br />
        <p class='textCuerpo'>Tengo el agrado de dirigirme a Usted, y por su intermedio 
           a quien corresponda a los efectos de solicitar la <b>HABILITACIÓN DE MI 
               CONSULTORIO</b><?php if(!is_null($infoHabilitacion['Nombre'])){ echo $infoHabilitacion['Nombre']; } ?>, ubicado en la CALLE <?php echo $infoHabilitacion['Calle']." ".$infoHabilitacion['Lateral']." Nº ".$infoHabilitacion['Numero']." ".$infoHabilitacion['Piso'].$infoHabilitacion['Departamento'] ?> con teléfono <?php echo $infoHabilitacion['Telefono'] ?>,
           en la LOCALIDAD de <?php echo $infoHabilitacion['NombreLocalidad'] ?>.
           En dicho consultorio atenderé en los días y horarios <b><?php echo $infoHabilitacion['Horarios'] ?></b>, realizando la ESPECIALIDAD de <?php echo utf8_encode($infoHabilitacion['NombreEspecialidad']) ?>.-
        </p>
        <p class='textCuerpo'>
           Sin otro particular, saluda a Usted muy atentamente.-
        </p>
        <br /><br />
        <?php
            $autorizados = obtenerColegiadoAutorizadosConsultorio($infoHabilitacion['IdMesaEntradaConsultorio']);
            
            if($autorizados)
            {
                if($autorizados -> num_rows != 0)
                {
                    ?>
        <h4><u>Médicos Autorizados</u></h4>
        <table>
            <?php
                while($row = $autorizados -> fetch_assoc())
                {
            ?>
            <tr>
                <td><?php echo $row['Matricula'] ?></td>
                <td><?php echo utf8_encode($row['Apellido'])." ".utf8_encode($row['Nombres']) ?></td>
            </tr>
            <?php
                }
            ?>
        </table>
                    <?php
                }
            }
        ?>
        <br /><br />
        <table>
        <?php
            $aColegiado['Id'] = $infoHabilitacion['IdColegiado'];
            require_once 'mostrarDatosPersonalesColegiado.php';
        ?>
        </table>
        <br />
        <div class="firma">
        <p>Firma: _______________________________
            <br /><br />
           <?php
                $consultaColegiado = obtenerColegiadoPorId($infoHabilitacion['IdColegiado']);
                if($consultaColegiado)
                {
                    if($consultaColegiado -> num_rows != 0)
                    {
                        $colegiado = $consultaColegiado -> fetch_assoc();
                        ?>
                        <?php echo utf8_encode($colegiado['Apellido'])." ".utf8_encode($colegiado['Nombres']) ?><br />
                        <b>MP:</b><?php echo $colegiado['Matricula'] ?>
                        <?php
                    }
                }
           ?>
           </p>
        </div>
        <div class='final'>
<div class="lineaBajoTablaConsultorio"></div>
<p class="textoBajoTablaConsultorio">User: <?php echo $_SESSION['idUsuario']." - ".$_SESSION['user']?></p>
<?php 
    // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
    date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<p> 
    Emitido el: <?php echo date("d/m/Y h:i:s a") ?>
</p>
        </div>
        </div>
</body>
</html>