<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
//require_once ('../dataAccess/eticaExpedienteLogic.php');
//require_once ('../dataAccess/sumarianteLogic.php');
//require_once ('../dataAccess/colegiadoLogic.php');
//require_once ('../dataAccess/secretarioadhocLogic.php');

$accion = $_POST['accion'];
if ($accion == 4) {
    $idSumariante = $_POST['idSumariante'];
    $tipoSumariante = $_POST['tipoSumariante'];
    $estadoExpediente = NULL;
} else {
    $estadoExpediente = $_POST['estadoExpediente'];
    $idSumariante = NULL;
    $tipoSumariante = NULL;
}
$idEticaExpediente = NULL;
$continua = TRUE;

$idColegiado = NULL;
$nroExpediente = "";
$caratula = "";
$observaciones = "";
$colegiadoBuscar = NULL;
$idSumarianteTitular = NULL;
$sumarianteTitularBuscar = NULL;
$idSumarianteSuplente = NULL;
$sumarianteSuplenteBuscar = NULL;
$idSecretarioadhoc = NULL;
$secretarioadhoc = NULL;

if (isset($_POST['idEticaExpediente'])){
    $idEticaExpediente = $_POST['idEticaExpediente'];
    $resEticaExpediente = obtenerEticaExpedientePorId($idEticaExpediente);
    if ($resEticaExpediente['estado']){
        $datos = $resEticaExpediente['datos'];
        $idEticaExpediente = $datos['idEticaExpediente'];
        $idColegiado = $datos['idColegiado'];
        $resColegiado = obtenerColegiadoBuscar($idColegiado);
        $colegiadoBuscar = $resColegiado['colegiadoBuscar'];
        $nroExpediente = $datos['nroExpediente'];
        $caratula = $datos['caratula'];
        $observaciones = $datos['observaciones'];
        $idSumarianteTitular = $datos['idSumarianteTitular'];
        if ($idSumarianteTitular){
            $resSumariante = obtenerSumarianteBuscar($idSumarianteTitular);
            $sumarianteTitularBuscar = $resSumariante['sumarianteBuscar'];
        }
        $idSumarianteSuplente = $datos['idSumarianteSuplente'];
        if ($idSumarianteSuplente){
            $resSumariante = obtenerSumarianteBuscar($idSumarianteSuplente);
            $sumarianteSuplenteBuscar = $resSumariante['sumarianteBuscar'];
        }
        $idSecretarioadhoc = $datos['idSecretarioadhoc'];
        if ($idSecretarioadhoc){
            $resSecretario = obtenerSecretarioadhocBuscar($idSecretarioadhoc);
            $secretarioadhoc = $resSecretario['nombre'];
        }

        $titulo="Editar Curso";
        $nombreBoton="Editar Curso";
    } else {
        //error al buscar expediente
        $continua = FALSE;
    }
} else {
    $titulo="Nuevo Curso";
    $nombreBoton="Guardar Curso";
}
        
if ($continua){
?>
    <?php
    if (isset($_POST['mensaje']))
    {
     ?>
        <div id="divMensaje"> 
            <p class="<?php echo $_POST['tipomensaje'];?>"><?php echo $_POST['mensaje'];?></p>  
        </div>
     <?php    
        $idColegiado = $_POST['idColegiado'];
        $nroExpediente = $_POST['nroExpediente'];
        $caratula = $_POST['caratula'];
        $observaciones = $_POST['observaciones'];
        $colegiadoBuscar = $_POST['colegiado_buscar'];
        if (isset($_POST['idSumarianteTitular'])){
            $idSumarianteTitular = $_POST['idSumarianteTitular'];
            $sumarianteTitularBuscar = $_POST['sumarianteTitular'];
        }
        if (isset($_POST['idSumarianteSuplente'])){
            $idSumarianteSuplente = $_POST['idSumarianteSuplente'];
            $sumarianteSuplenteBuscar = $_POST['sumarianteSuplente'];
        }
        if (isset($_POST['idSecretarioadhoc'])){
            $idSecretarioadhoc = $_POST['idSecretarioadhoc'];
            $secretarioadhoc = $_POST['secretarioadhoc'];
        }
    }   
    ?>  
    <div class="container-fluid">
        <div class="panel panel-default">
        <div class="panel-heading"><h4><b><?php echo $titulo; ?></b></h4></div>
        <div class="panel-body"> 
            <form id="formCursos" name="formCursos" method="POST" onSubmit="" action="datosEticaExpediente\abm_eticaExpediente.php">
                <div class="row">
                    <div class="col-md-7">
                        <b>T&iacute;tulo</b>  
                        <input class="form-control" autocomplete="OFF" type="text" name="colegiado_buscar" id="colegiado_buscar" placeholder="Ingrese Matrícula o Apellido del colegiao" value="<?php echo $colegiadoBuscar; ?>" required=""/>
                        <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado ?>" required="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <b>Tema</b>  
                        <input type="text" class="form-control" id="nroExpediente" name="nroExpediente" value="<?php echo $nroExpediente; ?>" placeholder="Número de Expediente" required="">
                    </div>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-7">
                        <b>D&iacute;as</b>  
                        <input type="text" class="form-control" id="caratula" name="caratula" value="<?php echo $caratula; ?>" placeholder="Carátula del Expediente" required="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <b>Fechas</b>  
                        <input type="text" class="form-control" id="caratula" name="caratula" value="<?php echo $caratula; ?>" placeholder="Carátula del Expediente" required="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <b>Sal&oacute;n</b>  
                        <input type="text" class="form-control" id="caratula" name="caratula" value="<?php echo $caratula; ?>" placeholder="Carátula del Expediente" required="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <b>Lugar</b>  
                        <input type="text" class="form-control" id="caratula" name="caratula" value="<?php echo $caratula; ?>" placeholder="Carátula del Expediente" required="">
                    </div>
                </div>

                
                
                
                
                
                
                <?php
                if ($accion != 4){
                ?>
                    <div class="row">
                         <div style="text-align:center">
                             <button type="submit"  class="btn btn-success " ><?php echo $nombreBoton; ?></button>
                         </div>
                    </div>  

                    <input type="hidden" name="idEticaExpediente" id="idEticaExpediente" value="<?php echo $idEticaExpediente; ?>" />
                    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
                <?php
                }
                ?>
         </form>   
        <!-- BOTON VOLVER -->    
        <div class="col-md-12" style="text-align:right;">
            <?php if ($accion == 4) {
                ?>
                <form  method="POST" action="sumariante_expedientes.php">
                    <button type="submit" class="btn btn-info" name='volver' id='name'>Volver </button>
                    <input type="hidden" name="idSumariante" id="idSumariante" value="<?php echo $idSumariante; ?>" />
                    <input type="hidden" name="tipoSumariante" id="tipoSumariante" value="<?php echo $tipoSumariante; ?>" />
                </form>
            <?php
            } else {?>
                <form  method="POST" action="eticaExpediente_lista.php">
                    <button type="submit" class="btn btn-info" name='volver' id='name'>Volver </button>
                    <input type="hidden" name="estadoExpediente" id="estadoExpediente" value="<?php echo $estadoExpediente; ?>" />
                </form>
            <?php
            }
            ?>
        </div>  
        </div>
     </div>

    <?php    
    require_once '../html/footer.php';
    ?>
    </div>
<?php
}
?>
<!--AUTOCOMLETE-->
<script src="../public/js/bootstrap3-typeahead.js"></script>    
<script language="JavaScript">
    $(function(){
        var nameIdMap = {};
        $('#colegiado_buscar').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'colegiado.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idColegiado').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });  
    
    $(function(){
        var nameIdMap = {};
        $('#sumarianteTitular').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'sumariante.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idSumarianteTitular').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });  
    
    $(function(){
        var nameIdMap = {};
        $('#sumarianteSuplente').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'sumariante.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idSumarianteSuplente').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });
    
    $(function(){
        var nameIdMap = {};
        $('#secretarioadhoc').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'secretarioadhoc.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idSecretarioadhoc').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });
    
    $(document).ready(function() {
    $("input[type=radio]").click(function(event){
        var valor = $(event.target).val();
        if(valor =="S"){
            $("#sumariantes").show();
        } else if (valor == "A") {
            $("#sumariantes").hide();
        } else { 
            // Otra cosa
        }
    });
});
</script>