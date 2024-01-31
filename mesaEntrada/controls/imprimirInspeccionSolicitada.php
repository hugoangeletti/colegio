<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    $inspecciones = array();
    
    if(isset($_GET['iIH']))
    {
        $idInspectorHabilitacion = $_GET['iIH'];
        $inspecciones[0] = $idInspectorHabilitacion;
    }
    else
    {
        if(isset($_GET['idMS']))
        {
            if($_GET['idMS'] != "")
            {
                $inspecciones = explode(",", $_GET['idMS']);
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
</head>
    <body onload="window.print()">
<?php

    foreach ($inspecciones as $key => $val)
    {
        if(isset($_GET['iIH']))
        {
            $consultaDatoInspeccion = obtenerInspectorHabilitacionPorIdImprimir($val);
        }
        else
        {
            $consultaDatoInspeccion = obtenerInspectorHabilitacionPorIdMesaEntrada($val);
        }
        if($consultaDatoInspeccion)
        {
            if($consultaDatoInspeccion -> num_rows != 0)
            {
                $datoInspeccion = $consultaDatoInspeccion -> fetch_assoc();
                ?>

        <div class="bodyInspeccion">
        <span class="titleActaInspeccion"><u>ACTA DE INSPECCIÓN DE CONSULTORIO Nº <?php echo rellenarCeros($datoInspeccion['IdInspectorHabilitacion'],8) ?></u></span>
        <br/><br/><br/>
        <span class="lugarDerecha">Lugar y Fecha ….......................................................</span>
        <br/><br/><br/>
        <p>El Sr. Inspector Médico que acredita su identidad mediante la credencial respectiva, y cuya firma y 
            sello se inserta al pie de la presente, y en cumplimiento de lo dispuesto por la Resolución Nº 
            3740/78 del Ministerio de Bienestar Social, Decreto Nº 3280/90 y Resoluciones del Consejo Superior 
            del Colegio de Médicos de la Provincia de Buenos Aires Nº 567/04 y del Ministerio de Salud 
            Nº 3057/09, se constituye en la calle:</p>
        <span class="calleCentrada"><b><?php echo $datoInspeccion['Calle']." Nº: ".$datoInspeccion['Numero']." ".$datoInspeccion['Lateral']." ".$datoInspeccion['Piso'].$datoInspeccion['Departamento']." ".$datoInspeccion['NombreLocalidad'] ?></b></span>
        <br/><br/>
        <p>y procede a constatar: .................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................<br/><br/>
        .............................................................................................................................................</p><br/>
        <p>Se hace constar que el Sr. Inspector Médico actuante está facultado para efectuar las inspecciones y diligencias
            que fuera menester para el mejor desempeño de sus funciones necesarias para la habilitación, contralor y 
            fiscalización de los consultorios médicos en toda el área de la Provincia de Buenos Aires, así como para 
            verificar el cumplimiento de las normas establecidas por el Ministerio de Salud sobre el particular, las 
            correspondientes a la Ley de Colegiación (Decreto-Ley 5413/58), los requisitos edilicios y de instalación 
            que determinan las normas para inscripción de consultorio y su correspondiente habilitación (Res. Ministerial 
            1762/58.-
        </p>
        <p>El mismo deberá hacer entrega de una de las copias de la presente acta al interesado, que firmará la misma, 
            o en su caso el Inspector actuante hará constar su negativa.- 
        </p>
        <p>En caso de utilizarse por cualquier causa la presente acta, deberá igualmente ser remitida al Colegio 
            de Distrito y Consejo Superior con la firma del Inspector actuante, o de algún miembro de la Mesa Directiva 
            del respectivo Colegio, haciéndose saber las causas de dicha inutilización.-
        </p>
        <p>A los fines pertinentes y para constancia, se extiende la presente por duplicado, en el lugar y fecha 
            mencionados.- 
        </p>
        <br/><br/><br/>
        <div class="firmaIzquierda">
            <span><b>Firma:</b>................................................</span><br/>
            <span><b>MP Nº:</b> <?php echo $datoInspeccion['MatriculaColegiadoConsultorio'] ?></span><br/>
            <span><?php echo $datoInspeccion['ApellidoColegiadoConsultorio']." ".$datoInspeccion['NombreColegiadoConsultorio'] ?></span><br/>
            <strong>Profesional Médico</strong>
        </div>
        <div class="firmaDerecha">
            <span><b>Firma:</b>..................................................</span><br/>
            <span><b>MP Nº:</b> <?php echo $datoInspeccion['MatriculaInspector'] ?></span><br/>
            <span><?php echo $datoInspeccion['ApellidoInspector']." ".$datoInspeccion['NombreInspector'] ?></span><br/>
            <strong>Inspector Médico</strong>
        </div>
</div>
        <div class="saltopagina"></div>
                <?php
            }
        }
    }
?>
</body>
</html>