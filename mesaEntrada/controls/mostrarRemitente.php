<?php
    
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    $aRemitente = array(
                        "Id" => "",
                        "Nombre" => "" 
                        );
    
    if(!isset($okey))
    {
        $okey = false;
    }
    if(isset($_GET['remitente']))
    {
        $remitente = $_GET['remitente'];
        $okey = true;
    }
        $consultaRemitente = obtenerRemitentePorId($remitente);
        $remitente = $consultaRemitente -> fetch_assoc();
        $aRemitente['Id'] = $remitente['id'];
        $aRemitente['Nombre'] = $remitente['Nombre'];
    if($okey)
    {
           $error = false;
            if(!$error)
            {
?>
<table>
    <tr>
        <td><b>ID:</b></td>
        <td><?php echo $aRemitente['Id'] ?></td>
    </tr>
    <tr>
        <td><b>Nombre:</b></td>
        <td><?php echo utf8_encode($aRemitente['Nombre']) ?></td>
    </tr>
</table>
<?php
            }
            
        }
        else
        {
            $error = true;
            
        }
            ?>
