<?php 
    require_once 'seguridad.php';
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Colegio de M&eacute;dicos Distrito I</title>
<link rel="shortcut icon" href="../images/logosh.gif" type="image/x-icon" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<link href="../css/Basic.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="../css/jquery-ui-1.10.3.custom.css" />
<script src="../js/jquery-1.10.1.js"></script>
<script src="../js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript">
    /*
     * Carga en la div #page-wrap el contenido debido al menú
     */
    <?php
        if (isset($_GET['me']))
        {
            if(($_GET['me'] == 1)||($_GET['me'] == 2)||($_GET['me'] == 4)||($_GET['me'] == 5)||($_GET['me'] == 7)||($_GET['me'] == 9)||($_GET['me'] == 10))
            {
     ?>
                $(document).ready(function(){
                    $("#page-wrap").load('buscarColegiado.php?me=<?php echo $_GET['me'] ?>');
                });
    <?php
            }
            else
            {   if($_GET['me'] == 3)
                {
    ?>            
                $(document).ready(function(){
                    $("#page-wrap").html("<form id='formSolicitante' action='mesaEntradaNotas.php' method='post'>\n\
                                          <fieldset><legend>Nota solicitada por:</legend><label for='colegiado'>Colegiado<input type='radio' class='tipoSolicitante' name='tipoSolicitante' id='colegiado' value='colegiado' checked='checked' /></label>\n\
                                          <label for='remitente'>Otro Remitente<input type='radio' class='tipoSolicitante' name='tipoSolicitante' id='remitente' value='remitente' /></label>\n\
                                          </fieldset></form>\n\
                                          <div id='consulta'></div>");
                });
    <?php       }
            }
            
        }
    ?>
</script>