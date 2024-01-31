<?php
    // Se encarga de mostrar todo el listado de remitentes que se encuentran
    // en la BD, para que el usuario elija desde el popup.

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    if(isset($_GET['matricula']))
    {
        $consultaColegiado = obtenerColegiadoPorMatricula($_GET['matricula']);
        $colegiado = $consultaColegiado -> fetch_assoc();
    
        $data = $colegiado['Apellido'];
        if(!is_null($colegiado['Nombres']))
        {
            $data .= " ".$colegiado['Nombres'];

        }

        echo utf8_encode($data);
    }
    else
    {
        ?>
<br>
<span class="mensajeERROR">No corresponde a un colegiado válido.</span>
<br>
        <?php
    }
    
    
?>