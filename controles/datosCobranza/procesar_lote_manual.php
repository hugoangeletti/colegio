<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/cobranzaLogic.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');
require_once ('../../dataAccess/colegiadoDebitosLogic.php');
require_once ('../../dataAccess/cursoLogic.php');

require_once ('procesarLote.php'); //funcion que aplica los pagos

function archivoValido($idLugarPago, $tipoArchivo) {
    echo 'lugar->'.$idLugarPago.' tipo->'.$tipoArchivo.'<br>';
    switch ($idLugarPago) {
        case '22':
            if ($tipoArchivo <> "text/plain") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;
        
        case '23':
            if ($tipoArchivo <> "text/csv") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;
        
        case '24':
            if ($tipoArchivo <> "text/csv") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;
        
        case '25':
            if ($tipoArchivo <> "application/octet-stream") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;
        
        case '26':
            if ($tipoArchivo <> "application/octet-stream") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;

        case '28':
            if ($tipoArchivo <> "text/plain") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;

        case '29':
            if ($tipoArchivo <> "application/octet-stream") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;

        case '30':
            if ($tipoArchivo <> "text/plain") {
                $respuesta = FALSE;
            } else {
                $respuesta = TRUE;
            }
            break;

        default:
            $respuesta = FALSE;
            break;
    }

    return $respuesta;
}

$continua = TRUE;
$mensaje = "";
if (isset($_POST['idLugarPago']) && $_POST['idLugarPago'] <> "") {
	$idLugarPago = $_POST['idLugarPago'];
} else {
	$continua = FALSE;
	$mensaje .= "Falta idLugarPago - ";
}

//var_dump($_FILES['archivoLote']); exit;
if ($_FILES['archivoLote']['name'] != "") {
    $fileTmpPath = $_FILES['archivoLote']['tmp_name'];  
    $archivoLote = $_FILES['archivoLote']['name'];
    $tipoArchivo = $_FILES['archivoLote']['type'];
    $tamanioArchivo = $_FILES['archivoLote']['size'];
    echo 'Lugar->'.$idLugarPago.' - Tipo->'.$tipoArchivo.' - archivoLote->'.$archivoLote.'<br>';
    if (archivoValido($idLugarPago, $tipoArchivo)) {
        if (verificarArchivoExistente($idLugarPago, $archivoLote)) {
            $mensaje .= "El Archivo <b>".$archivoLote."</b> ya fue procesado";
            $continua = FALSE;
        }
    } else {
        $continua = FALSE;
        $mensaje .= "Archivo no coincide con la estructra - <b>".$_FILES['archivoLote']['name']."</b>"; 
    }
} else {
	$continua = FALSE;
	$mensaje .= "Falta archivoLote - ";	
}

if ($continua) {
	//verificamo segun el lugar de pago, si el archivo es correcto y si ya no existe, y la fecha del archivo coincide con la ingresada
	switch ($idLugarPago) {
        case '22':
            // bapro
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = intval(substr($archivoLote, 16, 4));
            $mes = substr($archivoLote, 20, 2);
            $dia = substr($archivoLote, 22, 2);
            $fechaApertura = $anio.'-'.$mes.'-'.$dia;
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

        case '23':
            // PagoFacil
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = intval(substr($archivoLote, 6, 2)) + 2000;
            $mes = substr($archivoLote, 8, 2);
            $dia = substr($archivoLote, 10, 2);
            $fechaApertura = $anio.'-'.$mes.'-'.$dia;
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

        case '24':
            // Agremiacion de la plata
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = date('Y');
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

        case '25':
            // RapiPago
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = date('Y');
            $mes = substr($archivoLote, 4, 2);
            if ($mes == '12' && date('m') < 12) {
                $anio -= 1;
            }
            $dia = substr($archivoLote, 6, 2);
            //$fechaApertura = $anio.'-'.$mes.'-'.$dia;
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

        case '26':
            // Link
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = date('Y');
            $mes = substr($archivoLote, 4, 2);
            if ($mes == '12' && date('m') < 12) {
                $anio -= 1;
            }
            $dia = substr($archivoLote, 6, 2);
            //$fechaApertura = $anio.'-'.$mes.'-'.$dia;
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

        case '28':
            // Debito Tarjeta
            $nombreArchivo = $archivoLote;
            $extensionArchivo = '';

            $hayArchivos = FALSE;
            $anio = substr($archivoLote, 9, 4);
            $mes = substr($archivoLote, 13, 2);
            $dia = substr($archivoLote, 15, 2);
            //$fechaApertura = $anio.'-'.$mes.'-'.$dia;
            $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
            $archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
            echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
            //subir archivo y procesarlo
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;

		case '29':
            $fileName = explode(".", $archivoLote);
            $nombreArchivo = $fileName[0];
            $extensionArchivo = $fileName[1];
			// PagoMisCuentas
    		$hayArchivos = FALSE;
    		$anio = substr($extensionArchivo, 4, 2) + 2000;
    		$mes = substr($extensionArchivo, 2, 2);
    		$dia = substr($extensionArchivo, 0, 2);
    		$fechaApertura = $anio.'-'.$mes.'-'.$dia;
    		$path = "../../archivos/lotes/".$idLugarPago."/".$anio;
    		$archivoProcesar = $path."/".$archivoLote;
            $procesado = TRUE;
            /*
    		echo '<br>'.$path.'<br>';
            echo '<br>'.$archivoProcesar.'<br>';
            */
			//subir archivo y procesarlo
			if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
            //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

			break;
		
        case '30': //debito por CBU
            $fileName = explode(".", $archivoLote);
            $nombreArchivo = $fileName[0];
            $extensionArchivo = $fileName[1];
            //sino existe, entonces lo cargo
            if (substr($nombreArchivo, 0, 9) == "sda03504_") {
                //si es nombfre valido, entonces lo cargo
                $hayArchivos = FALSE;
                $anio = substr($nombreArchivo, 9, 2) + 2000;
                $path = "../../archivos/lotes/".$idLugarPago."/".$anio;
                $archivoProcesar = $path."/".$archivoLote;
                $procesado = TRUE;
                /*
                echo '<br>'.$path.'<br>';
                echo '<br>'.$archivoProcesar.'<br>';
                */
                //subir archivo y procesarlo
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                if (move_uploaded_file($fileTmpPath, $archivoProcesar)) {
                //if (file_exists($archivoProcesar)) {
                $respuesta = procesarLote($idLugarPago, $archivoProcesar, $archivoLote);
            }

            break;
        
		default:
			// code...
			break;
	}
}
$resultado['estado'] = $continua;
if (!isset($mensaje) || $mensaje == "") {
    $resultado['mensaje'] = "OK - Archivo procesado: ".$archivoLote;
    $resultado['clase'] = "alert alert-success";
} else {
    $resultado['mensaje'] = $mensaje;
}


    var_dump($archivoLote);
    echo '<br>';
    var_dump($continua);
    echo '<br>'.$resultado['mensaje'];
    exit;
?>

<body onLoad="document.forms['myForm'].submit()">
    <form name="myForm"  method="POST" action="../cobranza_procesar_form.php?accion=1">
        <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
        <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
        <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        <input type="hidden"  name="idLugarPago" id="idLugarPago" value="<?php echo $idLugarPago;?>">    
        <?php
        if ($resultado['estado']) {
        ?>
            <input type="hidden"  name="fechaApertura" id="fechaApertura" value="<?php echo $fechaApertura;?>">
            <input type="hidden"  name="archivoAdjuntar" id="archivoAdjuntar" value="<?php echo $archivoAdjuntar;?>">
        <?php 
    }
        ?>
    </form>
</body>

