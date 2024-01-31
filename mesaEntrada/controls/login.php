<html>
<head>
<title>Administracion</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../images/logosh.gif" type="image/x-icon" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<link href="../css/Basic.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">

$(document).ready(function(){
    if($(window).width() < 961){
        $("#ingreso").addClass('cuerpo960');
    }else if($(window).width() > 1261){
        $("#ingreso").addClass('cuerpo1260');        
    }
    
    $("#zone-bar li em").click(function() {
		   		var hidden = $(this).parents("li").children("ul").is(":hidden");
		   		
				$("#zone-bar>ul>li>ul").hide();        
			   	$("#zone-bar>ul>li>a").removeClass();
			   		
			   	if (hidden) {
			   		$(this)
				   		.parents("li").children("ul").toggle()
				   		.parents("li").children("a").addClass("zoneCur");
				   	} 
			   });
});

</script>
<?php include("../dataAccess/conection.php"); ?>
<body>
<?php include("encabezado.php");	    ?> 
<div id="ingreso" style="height:400px">
	<fieldset style="width:300px" class="login">
	<div id="login" style="width:200px" class="login">
	<h4>
	Iniciar sesi&oacute;n</h4>
        <form name="form1" method="post" action="control.php">
          <div align="center">
            <?php if ((isset($_GET['error']))&&($_GET["error"]=="SI")){ ?>
            	<font color="#FF0000">Datos Incorrectos</font> 
            <?php } 
				else{
					echo ' ';
				}?>
          </div>
		  <div style="font-size:14px; color:#000000;">
          Usuario:</div>
          <div>
            <input name="userName" type="text" id="userName" style="font-size:14px; width:200px; height:28px"></div>
          <br>
		  <div style="font-size:14px; color:#000000">
          Contrase&ntilde;a:</div>
          <div>  
            <input name="clave" type="password" id="clave" style="font-size:14px; width:200px; height:28px">
          </div>
		  <br />
          <div>
			<input type="submit" name="Submit" class="groovybutton" value="Acceder" title="">
          </div>
      </form>
    </div>
 	</fieldset>
</div>
<?php include("../html/pie.html");?>
</body>
</html>



