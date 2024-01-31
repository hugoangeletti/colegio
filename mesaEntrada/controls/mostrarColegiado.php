<?php
 
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    /*
     * $aColegiado -> arreglo de un Colegiado en Particular, únicamente
     * cuando es buscado por Matrícula.
     */
    
    $aColegiado = array(
                        "Id" => "",
                        "Matricula" => "", 
                        "Apellido" => "", 
                        "Nombre" => "", 
                        "EstadoMatricular" => "", 
                        "EstadoTesoreria" => ""
        );
    
    if(date("m") >= 6)
    {
        $periodoActual = date("Y");
    }
    else
    {
        $periodoActual = date("Y") - 1;
    }
    
    if(!isset($okey))
    {
        $okey = false;
    }
    if(isset($_GET['matricula']))
    {
        $matricula = $_GET['matricula'];
        $okey = true;
    }
    if(isset($_POST))
    {
        
        if((isset($_POST['matricula'])))
        {
            if($_POST['matricula'] != "")
            {
                $matricula = $_POST['matricula'];
                $okey = true;
            }else
            {
                ?>
            <br>
            <span class="mensajeERROR">Se olvidó de cargar la matrícula.</span>
            <br>
                <?php
            }
        }
    }
        if($okey)
        {
            $consultaColegiado = obtenerColegiadoPorMatricula($matricula);
            if(!$consultaColegiado)
            {
                ?>
            <br>
            <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
            <br>
                <?php
                $error = true;
            }
            else
            {
                if($consultaColegiado -> num_rows == 0)
                {
                ?>
            <br>
            <span class="mensajeWARNING">No se encontró el colegiado con la matrícula ingresada.</span>
            <br>
                <?php
                    $error = true;
                }
                else
                {
                    $colegiado = $consultaColegiado -> fetch_assoc();
                    $aColegiado['Id'] = $colegiado['Id'];
                    $aColegiado['Matricula'] = $colegiado['Matricula'];
                    $aColegiado['Apellido'] = utf8_encode($colegiado['Apellido']);
                    $aColegiado['Nombre'] = utf8_encode($colegiado['Nombres']);

                    //Colsulto Estado Matricular
                    $estadoColegiado = $colegiado['Estado'];
                    $consultaEstadoMatricular = obtenerTipoMovimiento($estadoColegiado);

                    if(!$consultaEstadoMatricular)
                    {
                        ?>
                    <br>
                    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                    <br>
                        <?php
                        $error = true;
                    }
                    else
                    {
                        if($consultaEstadoMatricular -> num_rows == 0)
                        {
                            ?>
                        <br>
                        <span class="mensajeWARNING">No corresponde a un estado matricular válido.</span>
                        <br>
                            <?php
                            $error = true;
                        }
                        else
                        {
                            $estadoMatricular = $consultaEstadoMatricular -> fetch_assoc();
                            $aColegiado['EstadoMatricular'] = estadoColegiado($estadoMatricular['Estado'])." (".$estadoMatricular['DetalleCompleto'].")";
                            $error = false;
                        }
                    }

                    //Consulto Estado Tesoreria
                    $estadoTesoreria = estadoTesoreriaPorColegiado($colegiado['Id'], $periodoActual);

                    $consultaNombreEstado = estadoTesoreria($estadoTesoreria);
                    $nombreEstado = $consultaNombreEstado -> fetch_assoc();
                    $aColegiado['EstadoTesoreria'] = $nombreEstado['Nombre'];
                    
                    //Consulta si tiene titulos de especialistas para ser entregados desde el 1/9/2016
                    $consultaTieneTitulos = tieneTituloEspecialistaParaRetirar($aColegiado['Id']);
                    if(!$consultaTieneTitulos)
                    {
                        ?>
                    <br>
                    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                    <br>
                        <?php
                        $error = true;
                    }
                    else
                    {
                        if($consultaTieneTitulos -> num_rows == 0)
                        {
                            ?>
                        <br>
                        <span class="mensajeWARNING">No corresponde a un estado matricular válido.</span>
                        <br>
                            <?php
                            $error = true;
                        }
                        else
                        {
                            $tieneTitulos = FALSE;
                            $cantidad = $consultaTieneTitulos -> fetch_assoc();
                            if ($cantidad['Cantidad'] > 0){
                                $error = false;
                                $tieneTitulos = TRUE;
                            }
                        }
                    }
                }
            }
            if(!$error)
            {
                switch ($estadoMatricular['Estado'])
                {
                    case "A":
                        $class = "textoOk";
                        break;
                    case "I":
                        $class = "textoOk";
                        break;
                    default :
                        $class = "textoError";
                        break;
                }
                
                switch ($nombreEstado['Codigo'])
                {
                    case 0:
                            $classT = "textoOk";
                        break;
                    default :
                            $classT = "textoError";
                        break;
                }
                ?>
<table>
    <tr>
        <td><b>Matrícula:</b></td>
        <td><?php echo $aColegiado['Matricula'] ?></td>
    </tr>
    <tr>
        <td><b>Apellido y Nombre:</b></td>
        <td><?php echo $aColegiado['Apellido']." ".$aColegiado['Nombre'] ?></td>
    </tr>
    <tr>
        <td><b>Estado Matricular:</b></td>
        <td><span class="<?php echo $class ?>"><?php echo utf8_encode($aColegiado['EstadoMatricular']) ?></span></td>
    </tr>
    <tr>
        <td><b>Estado con Tesorería:</b></td>
        <td><span class="<?php echo $classT ?>"><?php echo utf8_encode($aColegiado['EstadoTesoreria']) ?></span></td>
    </tr>
    <?php
    if ($tieneTitulos){
        ?>
    <tr>
        <td>&nbsp;</td>
        <td><p class="textoOk"><b>El profesional tiene t&iacute;tulo de especialista para retirar!</b></p></td>
    </tr>
    <?php
    }
    ?>
</table>
                <?php

            }
            
            }
        else
        {
            $error = true;
            
        }
            ?>
