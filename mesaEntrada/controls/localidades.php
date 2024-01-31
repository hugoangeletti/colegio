<?php
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    $data = "";
    if(isset($_POST['idZona']))
    {
        $localidades = obtenerLocalidadesPorIdZona($_POST['idZona']);
        if($localidades)
        {
            if($localidades -> num_rows != 0)
            {
                while($row = $localidades -> fetch_assoc())
                {
                    $data = $data."<option value='".$row['Id']."'"; 
                    if(isset($_POST['idLocalidad']))
                    {
                        if($_POST['idLocalidad'] != "")
                        {
                            if($row['Id'] == $_POST['idLocalidad'])
                            {
                                $data .= "selected";
                            }
                        }
                    }
                    $data .= " data=".$row['CodigoPostal'].">".utf8_encode($row['Nombre'])."</option>";
                }
            }
        }
    }
    echo $data;
?>
