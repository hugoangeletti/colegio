<?php
if(isset($_POST['tipoSolicitante']))
{
    if($_POST['tipoSolicitante'] == 'colegiado')
    {
        require 'buscarColegiado.php';
    }
    else if($_POST['tipoSolicitante'] == 'remitente') 
    {
        require 'buscarRemitente.php';
    }
}
?>
