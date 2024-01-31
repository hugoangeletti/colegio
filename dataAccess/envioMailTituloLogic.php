<?php
function  obtenerEnvioMailTitulo()
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT Id FROM enviomailtitulo WHERE Estado = 'A' LIMIT 1";
        $stmt = $conect->prepare($sql);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $row = mysqli_stmt_fetch($stmt);
            
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['idEnvioMailTitulo'] = $id;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay enviomailtitulo";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando enviomailtitulo";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function  obtenerTitulosParaEnviar($anio, $rango)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql="SELECT distinct tituloespecialista.IdTituloEspecialista, especialidad.Especialidad, resoluciondetalle.IdTipoEspecialista, 
            resoluciondetalle.IdColegiado, colegiado.Matricula, persona.Apellido, persona.Nombres, persona.Sexo,
            colegiadocontacto.CorreoElectronico
        FROM tituloespecialista
        INNER JOIN resoluciondetalle ON(resoluciondetalle.Id = tituloespecialista.IdResolucionDetalle)
        INNER JOIN colegiado ON(colegiado.Id = resoluciondetalle.IdColegiado)
        INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
        INNER JOIN colegiadocontacto ON(colegiadocontacto.IdColegiado = colegiado.Id 
            AND colegiadocontacto.IdEstado = 1 
            AND colegiadocontacto.CorreoElectronico is not null 
            AND UPPER(colegiadocontacto.CorreoElectronico) <> 'NR' 
            AND colegiadocontacto.CorreoElectronico <> '')
        INNER JOIN tipomovimiento ON(tipomovimiento.Id = colegiado.Estado)
        INNER JOIN especialidad ON(especialidad.Id = resoluciondetalle.Especialidad)
        LEFT JOIN enviomailtitulocolegiado ON(enviomailtitulocolegiado.IdColegiado = resoluciondetalle.IdColegiado)
        LEFT JOIN enviomailtitulo ON(enviomailtitulo.Id = enviomailtitulocolegiado.IdEnvioMailTitulo 
                AND enviomailtitulo.Estado = 'A')
        LEFT JOIN enviomaildiariocolegiado ON(enviomaildiariocolegiado.IdColegiado = resoluciondetalle.IdColegiado 
            AND enviomaildiariocolegiado.IdReferencia = tituloespecialista.IdTituloEspecialista)
        WHERE tipomovimiento.Estado = 'A'
                AND year(resoluciondetalle.FechaAprobada) >= ?
                AND tituloespecialista.FechaEntrega is null
                AND enviomailtitulocolegiado.Id is null
        ORDER BY colegiado.Matricula
        LIMIT ?";
        $stmt = $conect->prepare($sql);
        $stmt->bind_param('ii', $anio, $rango);
        $stmt->execute();
        $stmt->bind_result($idTituloEspecialista, $especialidad, $idTipoEspecialista, $idColegiado, $matricula, $apellido, $nombre, $sexo, $mail);
        $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $resultado['cantidad'] = mysqli_stmt_num_rows($stmt);
            $datos = array();
            while (mysqli_stmt_fetch($stmt)) 
            {
                $row = array (
                        'idReferencia' => $idTituloEspecialista,
                        'especialidad' => $especialidad,
                        'idTipoEspecialista' => $idTipoEspecialista,
                        'idColegiado' => $idColegiado,
                        'matricula' => $matricula,
                        'sexo' => $sexo,
                        'apellido' => $apellido,
                        'nombres' => $nombre,
                        'mail' => $mail
                 );
                array_push($datos, $row);
            }
            $resultado['estado'] = true;
            $resultado['mensaje'] = "OK";
            $resultado['datos'] = $datos;
            $resultado['clase'] = 'alert alert-success'; 
            $resultado['icono'] = 'glyphicon glyphicon-ok'; 
        } else {
            $resultado['estado'] = false;
            $resultado['mensaje'] = "No hay Titulo a Enviar";
            $resultado['clase'] = 'alert alert-info'; 
            $resultado['icono'] = 'glyphicon glyphicon-exclamation-sign';
        }
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error buscando Titulos a Enviar";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}

function guardarTituloEnviadoColegiado($idEnvioMailTitulo, $idColegiado)
{
    $conect = conectar();
    mysqli_set_charset( $conect, 'utf8');
    $sql = "INSERT INTO enviomailtitulocolegiado 
        (IdEnvioMailTitulo, IdColegiado, FechaEnvio)
        VALUES (?, ?, NOW())";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('ii', $idEnvioMailTitulo, $idColegiado);
    $stmt->execute();
    $stmt->store_result();

    if(mysqli_stmt_errno($stmt)==0) {
        $resultado['estado'] = true;
        $resultado['mensaje'] = "OK";
        $resultado['clase'] = 'alert alert-success'; 
        $resultado['icono'] = 'glyphicon glyphicon-ok';
    } else {
        $resultado['estado'] = false;
        $resultado['mensaje'] = "Error agregando envio titulo colegiado";
        $resultado['clase'] = 'alert alert-error'; 
        $resultado['icono'] = 'glyphicon glyphicon-remove';
    }
    
    return $resultado;
}