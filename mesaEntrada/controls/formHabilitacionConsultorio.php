<?php 
    require_once 'head_config.php';
?>
<script type="text/javascript">
$(function() {
    $( "#autorizado" ).autocomplete({
        source: "buscarAutorizados.php"
    });
  });
</script>
</head>
<body>
<table>
    <tr>
         <td><b>Matrícula del Colegiado Autorizado 1:</b></td>
         <td><input id="autorizado" class="autorizado" type="text" name="autorizados[]" /></td>
     </tr>
     <tr>
         <td><b>Matrícula del Colegiado Autorizado 2:</b></td>
         <td><input class="autorizado" type="text" name="autorizados[]" /></td>
     </tr>
     <tr>
         <td><b>Matrícula del Colegiado Autorizado 3:</b></td>
         <td><input class="autorizado" type="text" name="autorizados[]" /></td>
     </tr>
</table>
    <div class="oculto" style="display: none"></div>
</body>
</html>
