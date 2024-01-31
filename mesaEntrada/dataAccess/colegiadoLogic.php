<?php

//HUGO

function obtenerColegiados($matricula) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE c.Matricula LIKE '" . $matricula . "%'";
//    $sql = "SELECT colegiado.*, persona.Apellido, persona.Nombre
//            FROM colegiado
//            INNER JOIN persona ON(persona.Id = colegiado.IdPersona)
//            WHERE Matricula LIKE '" . $matricula . "%'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoAutorizadosConsultorio($idMesaEntradaConsultorio) {
    $sql = "SELECT meca.IdColegiado, c.Matricula, p.Apellido, p.Nombres
            FROM mesaentradaconsultorioautorizado as meca
            INNER JOIN colegiado as c ON (c.Id = meca.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            WHERE meca.IdMesaEntradaConsultorio = " . $idMesaEntradaConsultorio;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoPorMatricula($matricula) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE c.Matricula = " . $matricula;
//    $sql = "SELECT * 
//            FROM colegiado 
//            WHERE Matricula = " . $matricula;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoPorId($idColegiado) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE c.Id = " . $idColegiado;
//    $sql = "SELECT * 
//            FROM colegiado 
//            WHERE Id = " . $idColegiado;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoPorApellidoNombre($apellido, $nombre) {
    if ($nombre != "") {
        $filtroNombre = " AND p.Nombres LIKE '" . $nombre . "%'";
    } else {
        $filtroNombre = "";
    }
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE p.Apellido LIKE '" . $apellido . "%'" . $filtroNombre;
//    $sql = "SELECT * 
//            FROM colegiado 
//            WHERE Apellido LIKE '" . $apellido . "%'" . $filtroNombre;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerDatosPersonalesColegiadoPorId($idColegiado) {
    $sql = "SELECT cc.TelefonoFijo, cc.TelefonoMovil, cc.CorreoElectronico, cdr.Calle, cdr.Lateral, 
            cdr.Numero, cdr.Piso, cdr.Departamento
            FROM colegiado as c
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id AND cc.IdEstado = 1)
            INNER JOIN colegiadodomicilioreal as cdr ON (cdr.idColegiado = c.Id AND cdr.idEstado = 1)
            WHERE c.Id = " . $idColegiado;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerRemitentes() {
    $sql = "SELECT *
            FROM remitente
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerUniversidades() {
    $sql = "SELECT *
            FROM universidad
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEnviosUniversidad($universidad, $offset = null, $porPagina = null) {

    $where = "";

    if ($universidad != "-") {
        $where = " AND eu.IdUniversidad = " . $universidad;
    }
    $limit = "";
    if (!is_null($offset)) {
        $limit = "LIMIT $offset, $porPagina";
    }

    $sql = "SELECT eu.Id, eu.FechaDesde, eu.FechaHasta, DATE(eu.FechaCarga) as FechaCarga, eu.Envio, eu.Pdf, u.Nombre as NombreUniversidad
            FROM enviouniversidad as eu
            INNER JOIN universidad as u ON (u.Id = eu.IdUniversidad)
            WHERE eu.Estado = 'A'" .
            $where . " 
            ORDER BY DATE(eu.FechaCarga) DESC, u.Nombre
            " . $limit;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEnvioUniversidadColegiadosPorIdEU($IdEnvioUniversidad) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, c.FechaMatriculacion
            FROM enviouniversidad as eu
            INNER JOIN enviouniversidadcolegiado as euc ON (euc.IdEnvioUniversidad = eu.Id)
            INNER JOIN colegiado as c ON (c.Id = euc.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE eu.Id = " . $IdEnvioUniversidad;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerUltimaFechaEnvioUniversidad() {
    $sql = "SELECT MAX(FechaHasta) as FechaHasta
            FROM enviouniversidad";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerRemitentePorId($idRemitente) {
    $sql = "SELECT * 
            FROM remitente 
            WHERE id = " . $idRemitente;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerRemitentesPorNombre($nombre) {
    $sql = "SELECT * 
            FROM remitente 
            WHERE Nombre LIKE '%" . $nombre . "%'
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerPartidos() {
    $sql = "SELECT * 
            FROM zonas";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerPartidoPorIdLocalidad($idLocalidad) {
    $sql = "SELECT zonas.Id as IdZona, zonas.Nombre as NombreZona
            FROM zonas
            INNER JOIN localidad ON (localidad.idZona = zonas.Id)
            WHERE localidad.Id = " . $idLocalidad;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerLocalidadesPorIdZona($idZona) {
    $sql = "SELECT *
            FROM localidad
            WHERE idZona = " . $idZona . "
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function realizarBajaRemitente($idRemitente) {
    $sql = "DELETE FROM remitente WHERE id = " . $idRemitente;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function tieneTituloEspecialistaParaRetirar($idColegiado){
    $sql = "select count(tituloespecialista.IdTituloEspecialista) as Cantidad
            from tituloespecialista 
            inner join resoluciondetalle on(resoluciondetalle.Id = tituloespecialista.IdResolucionDetalle)
            where resoluciondetalle.IdColegiado = ".$idColegiado."
            and tituloespecialista.FechaEmision >= '2016-01-01'
            and tituloespecialista.FechaEntrega is null";
    $res = conectar()->query($sql);
    if (!$res) {
        return -1;
    } else {
        return $res;
    }
}
