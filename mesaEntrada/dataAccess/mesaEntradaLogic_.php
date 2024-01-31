<?php

/*
 * *********************   OBTENCION DE DATOS    ******************************
 */

function obtenerUltimaMesaEntradaConsultorio() {
    $sql = "SELECT MAX(IdMesaEntradaConsultorio) as IdMesaEntradaConsultorio
            FROM mesaentradaconsultorio";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerNumeroHojaRuta() {
    $sql = "SELECT MAX(IdMesaEntrada) as IdMesaEntrada FROM mesaentrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerUltimoRemitente() {
    $sql = "SELECT MAX(id) as idRemitente
            FROM remitente";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerNotasPorRemitente($idRemitente) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdRemitente = " . $idRemitente . " ORDER BY me.IdMesaEntrada desc";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerNotasPorRemitenteLimitado($remitente, $offset, $rowsPerPage) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            r.Nombre as NombreRemitente, men.Tema, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdRemitente = " . $remitente . " AND me.Estado = 'A' 
            ORDER BY me.IdMesaEntrada DESC LIMIT $offset, $rowsPerPage";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerFechaMesaEntrada($idMesaEntrada) {
    $sql = "SELECT FechaIngreso
            FROM mesaentrada
            WHERE IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorFecha($fecha) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            WHERE me.FechaIngreso = '" . $fecha . "' AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdColegiado($idColegiado) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
        c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdColegiado = " . $idColegiado . " AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerNotaPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, men.Tema, men.IncluyeMovimiento,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerDenunciaPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            tme.Nombre as NombreMovimiento, med.FechaDenuncia, med.FechaExtravio, med.IdTipoDenuncia, 
            td.Nombre as NombreTipoDenuncia, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradadenuncia as med ON (med.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN tipodenuncia as td ON (td.Id = med.IdTipoDenuncia)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEntregaPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            tme.Nombre as NombreMovimiento, mee.FechaEntrega, mee.IdTipoEntrega, te.Nombre as NombreTipoEntrega, 
            te.Leyenda, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaentrega as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN tipoentrega as te ON (te.Id = mee.IdTipoEntrega)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerNotasIncluyenMovimiento() {
    $sql = "SELECT r.id as IdRemitente, r.Nombre as NombreRemitente, men.Tema, me.FechaIngreso, 
            me.IdMesaEntrada 
            FROM mesaentradanota as men
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = men.IdMesaEntrada)
            INNER JOIN remitente as r ON (r.id = me.IdRemitente)
            WHERE men.IncluyeMovimiento = 'S'
            AND me.Estado = 'A'
            ORDER BY me.FechaIngreso DESC";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorNota($idMesaEntrada) {
    $sql = "SELECT cmd.Id, c.Matricula, p.Apellido, p.Nombres, tm.Detalle as NombreMovimiento, cmd.FechaDesde, 
            cmd.FechaHasta, cmd.DistritoCambio, me.IdUsuario, u.Usuario
            FROM colegiadomovimientodistritos as cmd
            INNER JOIN colegiado as c ON(c.Id = cmd.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN tipomovimiento as tm ON (tm.Id = cmd.IdMovimiento)
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = cmd.IdMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE cmd.IdMesaEntrada = " . $idMesaEntrada . "
            AND cmd.Estado = 'A'
            AND me.Estado = 'A'
            ORDER BY cmd.Id DESC";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoMovimientoBajaInscripcion($idMesaEntrada) {
    $sql = "SELECT cm.Id, c.Matricula, p.Apellido, p.Nombres, cm.FechaHasta as FechaDesde, me.IdUsuario,
            u.Usuario
            FROM colegiadomovimiento as cm
            INNER JOIN colegiado as c ON (c.Id = cm.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN colegiadomovimientomesaentrada as cmme ON (cmme.IdColegiadoMovimiento = cm.Id)
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = cmme.IdMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE cm.Estado = 'O'
            AND cm.FechaHasta IS NOT NULL
            AND cm.FechaHasta <> '0000-00-00'
            AND cm.IdMovimiento = 10
            AND cmme.IdMesaEntrada = " . $idMesaEntrada . "
            ORDER BY cm.FechaHasta DESC";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoMovimientoPorDistrito($idColegiado, $distritoOrigen) {

    $distritoRomano = pasarARomano($distritoOrigen);

    $sql = "SELECT Id
            FROM colegiadomovimiento
            WHERE IdColegiado = " . $idColegiado . "
            AND (DistritoCambio = " . $distritoOrigen . " OR DistritoCambio = '" . $distritoRomano . "')
            AND (FechaHasta IS NULL OR FechaHasta = '0000-00-00')
            AND Estado = 'O'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerHabilitacionPorConsultorioPorColegiado($idColegiado, $idConsultorio) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdColegiado = " . $idColegiado . "
            AND mec.IdConsultorio = " . $idConsultorio . "
            AND me.FechaIngreso = '" . date("Y-m-d") . "' 
            AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMovimientoPorIdPorIdColegiado($idColegiado, $idTipoMovimiento) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdColegiado = " . $idColegiado . "
            AND mem.IdTipoMovimiento = " . $idTipoMovimiento . "
            AND me.FechaIngreso = '" . date("Y-m-d") . "'
            AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMovimientoPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, mem.IdTipoMovimiento, 
            mem.IdMotivoCancelacion, mem.Fecha, mem.Distrito, mem.IdMesaEntradaMovimiento, tm.DetalleCompleto, 
            mc.Nombre as NombreMotivoCancelacion, mema.IdMesaEntradaMovimiento as IdMesaEntradaMovimientoAnulado,
            mem.IdPatologia, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            LEFT JOIN motivocancelacion as mc ON (mc.IdMotivoCancelacion = mem.IdMotivoCancelacion)
            LEFT JOIN mesaentradamovimientoanulacion as mema ON (mema.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMovimientoPorIdMovimiento($idMesaEntradaMovimiento) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, mem.IdTipoMovimiento, 
            mem.IdMotivoCancelacion, mem.Fecha, mem.Distrito, mem.IdMesaEntradaMovimiento, tm.DetalleCompleto, 
            mc.Nombre as NombreMotivoCancelacion, mema.IdMesaEntradaMovimiento as IdMesaEntradaMovimientoAnulado
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            LEFT JOIN motivocancelacion as mc ON (mc.IdMotivoCancelacion = mem.IdMotivoCancelacion)
            LEFT JOIN mesaentradamovimientoanulacion as mema ON (mema.IdMesaEntrada = me.IdMesaEntrada)
            WHERE mem.IdMesaEntradaMovimiento = " . $idMesaEntradaMovimiento;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerAutoprescripcionPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            r.Nombre as NombreRemitente, mea.Fecha, mea.Autorizado, mea.DocumentoAutorizado, mea.Parentezco, 
            mea.Autorizado2, mea.DocumentoAutorizado2, mea.Parentezco2,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN mesaentradaautoprescripcion as mea ON (mea.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidades() {
    $sql = "SELECT * 
            FROM especialidad
            ORDER BY Especialidad";

    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadMesaEntradaPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, me.EstadoMatricular, me.EstadoTesoreria, c.Matricula, p.Apellido, p.Nombres, 
            r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, mee.IdEspecialidad, 
            mee.TipoEspecialidad, mee.NumeroExpediente, mee.Distrito,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaespecialidad as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorFechaLimitado($fecha, $offset, $rowsPerPage) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, 
            tmov.DetalleCompleto, td.Nombre as NombreDenuncia, te.Nombre as NombreEntrega,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tmov ON (tmov.Id = mem.IdTipoMovimiento)
            LEFT JOIN mesaentradadenuncia as med ON (med.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipodenuncia as td ON (td.Id = med.IdTipoDenuncia)
            LEFT JOIN mesaentradaentrega as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipoentrega as te ON (te.Id = mee.IdTipoEntrega)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.FechaIngreso = '" . $fecha . "' AND me.Estado = 'A' 
            ORDER BY p.Apellido, p.Nombres DESC LIMIT $offset, $rowsPerPage";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdColegiadoLimitado($idColegiado, $offset, $rowsPerPage) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, 
            tmov.DetalleCompleto, td.Nombre as NombreDenuncia, te.Nombre as NombreEntrega,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tmov ON (tmov.Id = mem.IdTipoMovimiento)
            LEFT JOIN mesaentradadenuncia as med ON (med.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipodenuncia as td ON (td.Id = med.IdTipoDenuncia)
            LEFT JOIN mesaentradaentrega as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipoentrega as te ON (te.Id = mee.IdTipoEntrega)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdColegiado = " . $idColegiado . " AND me.Estado = 'A' 
            ORDER BY me.IdMesaEntrada DESC LIMIT $offset, $rowsPerPage";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorFechaTipoMesa($fecha, $tipoMesa) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.FechaIngreso = '" . $fecha . "' AND me.IdTipoMesaEntrada = " . $tipoMesa . " AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdColegiadoTipoMesa($idColegiado, $tipoMesa) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdColegiado = " . $idColegiado . " AND me.IdTipoMesaEntrada = " . $tipoMesa . " AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdOrdenDia($idOrden) {
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdTipoMesaEntrada, me.FechaIngreso, c.Matricula, p.Apellido, 
            p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, oddd.TipoPlanilla,
            me.IdUsuario, u.Usuario
            FROM ordendeldiadetalle as oddd
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = oddd.IdMesaEntrada)
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE oddd.Estado = 'A'
            AND oddd.TipoPlanilla IN (1,2)
            AND oddd.IdOrdenDia = " . $idOrden;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdOrdenDiaPorPlanilla($idOrden, $planilla) {
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdTipoMesaEntrada, me.FechaIngreso, c.Matricula, 
            p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tm.DetalleCompleto,
            tme.Nombre as NombreMovimiento, oddd.TipoPlanilla, me.Observaciones, men.Tema, tm.DetalleCompleto as DetalleCompleto, oddd.Orden
            FROM ordendeldiadetalle as oddd
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = oddd.IdMesaEntrada)
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            WHERE (oddd.Estado = 'A' OR oddd.Estado = 'P')
            AND oddd.TipoPlanilla = " . $planilla . "
            AND oddd.IdOrdenDia = " . $idOrden . "
            ORDER BY oddd.Orden, me.IdMesaEntrada ";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosParaOrdenDia($fechaDesde, $fechaHasta) {
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdTipoMesaEntrada, me.FechaIngreso, c.Matricula, p.Apellido, 
            p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, me.Observaciones, 
            men.Tema, tm.DetalleCompleto as DetalleCompleto
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "') AND (me.IdTipoMesaEntrada IN (1,3,4,7,8,9)) AND me.Estado = 'A'
            AND me.IdMesaEntrada NOT IN(SELECT oddd.IdMesaEntrada
                                        FROM ordendeldiadetalle as oddd
                                        INNER JOIN ordendeldia as odd ON (odd.Id = oddd.IdOrdenDia)
                                        WHERE oddd.Estado = 'A'
                                        AND odd.Estado = 'A')
            ORDER BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMotivosCancelacion() {
    $sql = "SELECT * 
            FROM motivocancelacion 
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerPatologias() {
    $sql = "SELECT * 
            FROM patologia 
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadesPorNombre($nombreEspecialidad, $matricula) {
    if (isset($matricula)) {
        $sql = "(SELECT e.Id as idEspecialidad, e.Especialidad as NombreEspecialidad,e.IdTipoEspecialidad, 
                ed.IdEspecialidadPrincipal, e1.Especialidad as NombreDependiente
                FROM especialidad as e
                LEFT JOIN especialidaddependiente as ed ON(e.Id = ed.IdEspecialidadDependiente)
                LEFT JOIN especialidad as e1 ON (e1.Id = ed.IdEspecialidadPrincipal AND e1.Estado = 'A')
                WHERE e.Especialidad LIKE '" . $nombreEspecialidad . "%'
                AND e.Estado = 'A')
                UNION
                (SELECT e.Id as idEspecialidad, e.Especialidad as NombreEspecialidad,e.IdTipoEspecialidad, 
                NULL, NULL
                FROM especialidad as e
                INNER JOIN colegiadoespecialista as ce ON (ce.Especialidad = e.Id)
                INNER JOIN colegiado as c ON(c.Matricula = ".$matricula." AND c.Id = ce.IdColegiado)
                WHERE e.Especialidad LIKE '" . $nombreEspecialidad . "%'
                AND e.Estado = 'B')";
    } else {
        $sql = "SELECT e.Id as idEspecialidad, e.Especialidad as NombreEspecialidad,e.IdTipoEspecialidad, 
                ed.IdEspecialidadPrincipal, e1.Especialidad as NombreDependiente
                FROM especialidad as e
                LEFT JOIN especialidaddependiente as ed ON(e.Id = ed.IdEspecialidadDependiente)
                LEFT JOIN especialidad as e1 ON (e1.Id = ed.IdEspecialidadPrincipal AND e1.Estado = 'A')
                WHERE e.Especialidad LIKE '" . $nombreEspecialidad . "%'
                AND e.Estado = 'A'
                ORDER BY e.Especialidad";
    }
    /*
      SELECT *
      FROM especialidad
      WHERE Especialidad LIKE '".$nombreEspecialidad."%'
      ORDER BY Especialidad";
     * 
     */
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadesPorNombrePorTipo($nombreEspecialidad, $tipoEspecialidad) {
    if ($tipoEspecialidad == "") {
        $consulta = "AND (idTipoEspecialidad = 1 OR idTipoEspecialidad = 2)";
    } else {
        $consulta = "AND idTipoEspecialidad = " . $tipoEspecialidad;
    }
    $sql = "SELECT * 
            FROM especialidad
            WHERE Especialidad LIKE '" . $nombreEspecialidad . "%' " . $consulta . "
            ORDER BY Especialidad";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultoriosPorCallePorTipo($nombreConsultorio, $tc) {
    $sql = "SELECT *
            FROM consultorio
            WHERE TipoConsultorio = '" . $tc . "' 
            AND Calle LIKE '%" . $nombreConsultorio . "%'
            AND Estado = 'A'
            ORDER BY Calle";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultorios($calle) {
    $sql = "SELECT c.IdConsultorio, c.TipoConsultorio, c.Nombre, c.Calle, c.Lateral, c.Numero, c.Telefono, 
            l.Nombre as NombreLocalidad, c.FechaCarga
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE c.TipoConsultorio IS NOT NULL
            AND c.Estado = 'A'
            AND c.Calle LIKE '" . $calle . "%'
            ORDER BY c.Calle";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultorioPorTipo($tipoConsultorio, $calle) {
    $sql = "SELECT c.IdConsultorio, c.TipoConsultorio, c.Nombre, c.Calle, c.Lateral, c.Numero, c.Telefono, 
            l.Nombre as NombreLocalidad, c.FechaCarga
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE c.TipoConsultorio = '" . $tipoConsultorio . "' 
            AND c.TipoConsultorio IS NOT NULL
            AND c.Estado = 'A'
            AND c.Calle LIKE '" . $calle . "%'
            ORDER BY c.Calle";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultoriosLimitado($offset, $porPagina, $calle) {
    $sql = "SELECT c.IdConsultorio, c.TipoConsultorio, c.Nombre, c.Calle, c.Lateral, c.Numero, c.Telefono, 
            l.Nombre as NombreLocalidad, c.FechaCarga
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE c.TipoConsultorio IS NOT NULL
            AND c.Estado = 'A'
            AND c.Calle LIKE '" . $calle . "%'
            ORDER BY c.Calle
            LIMIT $offset, $porPagina";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultorioPorTipoLimitado($tipoConsultorio, $offset, $porPagina, $calle) {
    $sql = "SELECT c.IdConsultorio, c.TipoConsultorio, c.Nombre, c.Calle, c.Lateral, c.Numero, c.Telefono, 
            l.Nombre as NombreLocalidad, c.FechaCarga
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE c.TipoConsultorio = '" . $tipoConsultorio . "' 
            AND c.TipoConsultorio IS NOT NULL
            AND c.Estado = 'A'
            AND c.Calle LIKE '" . $calle . "%'
            ORDER BY c.Calle
            LIMIT $offset, $porPagina";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerFechaTipoEspecialistaPorTipo($idColegiado, $tipoEspecialidad, $idEspecialidad) {
    $sql = "SELECT Fecha 
            FROM colegiadoespecialistatipo as cet
            INNER JOIN colegiadoespecialista as ce ON(ce.Id = cet.IdColegiadoEspecialista)
            WHERE ce.IdColegiado = " . $idColegiado . "
            AND cet.TipoEspecialista = '" . $tipoEspecialidad . "'
            AND ce.Especialidad = " . $idEspecialidad;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorFechaLimitadoTipoMesa($tipoMesaEntrada, $fecha, $offset, $rowsPerPage) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, tme.Nombre as NombreMovimiento, 
            tmov.DetalleCompleto, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            LEFT JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            LEFT JOIN tipomovimiento as tmov ON (tmov.Id = mem.IdTipoMovimiento)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.FechaIngreso = '" . $fecha . "' AND me.Estado = 'A' AND me.IdTipoMesaEntrada = " . $tipoMesaEntrada . "
            ORDER BY p.Apellido, p.Nombres DESC LIMIT $offset, $rowsPerPage";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMovimientosPorIdColegiadoLimitadoTipoMesa($tipoMesaEntrada, $idColegiado, $offset, $rowsPerPage) {
    switch ($tipoMesaEntrada) {
        case 1:
            $inner = "INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
                          INNER JOIN tipomovimiento as tm ON (tm.Id = mem.IdTipoMovimiento)
                          INNER JOIN motivocancelacion as mc ON (mc.IdMotivoCancelacion = mem.IdMotivoCancelacion)";
            $select = ", tm.DetalleCompleto, mc.Nombre as nombreCancelacion";
            break;
        case 2:
            $inner = "INNER JOIN mesaentradaespecialidad as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
                          INNER JOIN especialidad as e ON (e.Id = mee.IdEspecialidad)
                          INNER JOIN tipoespecialista as te ON (te.Codigo = mee.TipoEspecialidad)";
            $select = ", e.Especialidad as nombreEspecialidad, te.Nombre as TipoEspecialidad";
            break;
        case 3:
            $inner = "INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada";
            $select = ", men.Tema";
            break;
        case 4:
            $inner = "INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
                          INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
                          INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)";
            $select = ", con.Nombre as nombreConsultorio, con.TipoConsultorio, con.Calle, con.Lateral, con.Numero, e.Especialidad as nombreEspecialidad";
            break;
        case 9:
            $inner = "INNER JOIN mesaentradadenuncia as med ON (med.IdMesaEntrada = me.IdMesaEntrada)
                        INNER JOIN tipodenuncia as td ON (td.Id = med.IdTipoDenuncia)";
            $select = ",med.FechaDenuncia, med.FechaExtravio, med.IdTipoDenuncia, td.Nombre as NombreTipoDenuncia";
            break;
    }
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, 
                me.FechaIngreso, c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, 
                tme.Nombre as NombreMovimiento" . $select . ",
                me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            " . $inner . "
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON (p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdColegiado = " . $idColegiado . " AND me.Estado = 'A' AND me.IdTipoMesaEntrada = " . $tipoMesaEntrada . "
            ORDER BY me.IdMesaEntrada DESC LIMIT $offset, $rowsPerPage";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMovimientosPorIdColegiadoHoy($idColegiado) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            WHERE (me.FechaIngreso = '" . date("Y-m-d") . "')
            AND me.IdColegiado = " . $idColegiado . "
            AND Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, $tipoMesa) {
    $sql = "SELECT *
            FROM mesaentrada
            WHERE (FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND IdTipoMesaEntrada = " . $tipoMesa . "
            AND Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerNuevosMatriculadosPorFechas($fechaDesde, $fechaHasta) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE (c.FechaMatriculacion>='" . $fechaDesde . "' AND c.FechaMatriculacion<='" . $fechaHasta . "')
            AND ((c.Matricula >= 10000 and c.Matricula <=19999) 
            OR (c.Matricula >= 110000 and c.Matricula <=119999))";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadesPorColegiado($idColegiado) {
    $sql = "SELECT ce.FechaEspecialista as FechaEspecialista, ce.FechaVencimiento as FechaVencimiento, 
            ce.Especialidad as idEspecialidad, e.Especialidad as NombreEspecialidad, 
            cet.TipoEspecialista as TipoEspecialista
            FROM colegiadoespecialista as ce
            LEFT JOIN colegiadoespecialistatipo as cet ON (cet.IdColegiadoEspecialista = ce.Id)
            INNER JOIN especialidad as e ON (e.Id = ce.Especialidad)
            WHERE ce.IdColegiado = " . $idColegiado;
    
    //        AND e.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerNombreEspecialidadCabeceraPorIdEspecialidad($idEspecialidad) {
    $consultaEspecialidad = obtenerEspecialidadPorId($idEspecialidad);

    if ($consultaEspecialidad) {
        if ($consultaEspecialidad->num_rows != 0) {
            $especialidad = $consultaEspecialidad->fetch_assoc();


            if ($especialidad['IdTipoEspecialidad'] != 1) {
                $codigoEspecialidad = $especialidad['CodigoRes62707'];

                $dosDigitos = substr($codigoEspecialidad, 0, 2);
                $codigoBuscar = rellenarCerosAtras($dosDigitos, 6);

                $sql = "SELECT Especialidad as NombreEspecialidad
                        FROM especialidad
                        WHERE CodigoRes62707 = '" . $codigoBuscar . "' 
                        AND IdTipoEspecialidad = 1";

                $res = conectar()->query($sql);

                return $res;
            }
        }
    }

    return null;
}

/*
 * ***************************************************************************
 */

function obtenerCalificacionesAgregadasPorColegiado($idColegiado) {
    $sql = "SELECT e1.Id as idEspecialidad, e1.Especialidad as NombreEspecialidad, 
            colegiadoespecialista.FechaVencimiento as FechaVencimiento
            FROM especialidaddependiente as ed
            INNER JOIN especialidad as e ON(e.Id = ed.IdEspecialidadPrincipal AND e.Estado = 'A')
            INNER JOIN especialidad as e1 ON(e1.Id = ed.IdEspecialidadDependiente AND e1.Estado = 'A')
            INNER JOIN colegiadoespecialista ON(colegiadoespecialista.Especialidad = e.Id)
            WHERE colegiadoespecialista.IdColegiado = " . $idColegiado . "
            AND ed.IdEspecialidadDependiente NOT IN(SELECT colegiadoespecialista.Especialidad
                                                                        FROM colegiadoespecialista
                                                                        INNER JOIN especialidad ON (especialidad.Id = colegiadoespecialista.Especialidad)
                                                                        WHERE especialidad.IdTipoEspecialidad = 3
                                                                        AND colegiadoespecialista.IdColegiado = " . $idColegiado . ")
            GROUP BY e1.Id";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerDatosConsultorioPorId($idConsultorio) {
    $sql = "SELECT c.TipoConsultorio, c.Nombre as nombreConsultorio, c.Calle, c.Lateral, c.Numero, c.Piso, 
            c.Departamento, c.Telefono, c.IdLocalidad, c.Observaciones, l.Nombre as nombreLocalidad
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE IdConsultorio = " . $idConsultorio;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerAutorizadosPorIdMesaEntradaConsultorio($idMesaEntradaConsultorio) {
    $sql = "SELECT meca.*, c.Matricula, p.Apellido, p.Nombres
            FROM mesaentradaconsultorioautorizado as meca
            INNER JOIN colegiado as c ON(c.Id = meca.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE meca.IdMesaEntradaConsultorio = " . $idMesaEntradaConsultorio;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionConsultorioPorId($idMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            me.Observaciones, c.Matricula, p.Apellido, p.Nombres, r.Nombre as NombreRemitente, 
            mec.IdMesaEntradaConsultorio,con.IdConsultorio, con.Nombre, con.Calle, con.Lateral, con.Numero, 
            con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, con.IdLocalidad, 
            mec.IdEspecialidad, l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad,
            me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON(p.Id = c.IdPersona)
            LEFT JOIN remitente as r ON (r.id = me.IdRemitente)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada . "
            ORDER BY mec.IdMesaEntradaConsultorio DESC";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadesPorColegiadoPorTipo($idColegiado, $tipoEspecialidad) {
    $sql = "SELECT ce.FechaEspecialista as FechaEspecialista, ce.Especialidad as idEspecialidad, e.Especialidad as NombreEspecialidad, cet.TipoEspecialista as TipoEspecialista
            FROM colegiadoespecialista as ce
            LEFT JOIN colegiadoespecialistatipo as cet ON (cet.IdColegiadoEspecialista = ce.Id)
            INNER JOIN especialidad as e ON (e.Id = ce.Especialidad)
            WHERE ce.IdColegiado = " . $idColegiado . "
            AND cet.TipoEspecialista <> '" . $tipoEspecialidad . "'
            AND e.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerEspecialidadPorId($idEspecialidad) {
    $sql = "SELECT * 
            FROM especialidad
            WHERE Id = " . $idEspecialidad;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerFechaCalificacionAgregada($idColegiado, $califiacionAgregada) {
    $sql = "SELECT c.Matricula, ce.Especialidad as idEspecialidad, ce.FechaEspecialista, ce.FechaRecertificacion, ce.FechaVencimiento, ce.Estado, ce.IdColegiado, e.Especialidad as nombreEspecialidad
            FROM colegiadoespecialista as ce
            INNER JOIN colegiado as c on(c.Id = ce.IdColegiado)
            INNER JOIN especialidaddependiente as ed ON(ed.IdEspecialidadPrincipal = ce.Especialidad)
            INNER JOIN especialidad as e ON(e.Id = ed.IdEspecialidadPrincipal)
            WHERE ed.IdEspecialidadDependiente = " . $califiacionAgregada . "
            AND ce.IdColegiado = " . $idColegiado . "
            AND ce.Estado = 'A'
            ORDER BY ce.Especialidad";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerUltimoNumeroExpediente($anio) {
    $sql = "SELECT MAX(NumeroExpediente) as numero
            FROM mesaentradaespecialidad
            WHERE AnioExpediente = '" . $anio . "'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerInfoExpediente($idMesaEntrada) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradaespecialidad as mee ON(mee.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMatriculaJPorId($idMesaEntrada) {
    $sql = "SELECT *
            FROM mesaentrada
            WHERE IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMatriculasJPorFecha($fecha) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdRemitente, me.IdTipoMesaEntrada, me.FechaIngreso, 
            c.Matricula, p.Apellido, p.Nombres
            FROM mesaentrada as me
            LEFT JOIN colegiado as c ON (c.Id = me.IdColegiado)
            LEFT JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE me.FechaIngreso = '" . $fecha . "' AND me.IdTipoMesaEntrada = 5 AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTipoPagoPorTipoEspecialidad($idTipoEspecialidad) {
    $sql = "SELECT IdTipoPago
            FROM tipoespecialista
            WHERE Codigo = '" . $idTipoEspecialidad . "'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTipoPagoPorId($idTipoPago) {
    $sql = "SELECT *
            FROM tipopago
            WHERE Id = " . $idTipoPago;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTipoValorPorId($idValor) {
    $sql = "SELECT *
            FROM tablavalores
            WHERE Fecha <= DATE(NOW())
            AND IdValor = " . $idValor . "
            ORDER BY Fecha DESC
            LIMIT 1";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInformacionEspecialidadPorIdPorColegiado($idColegiado, $idEspecialidad) {
    $sql = "SELECT c.Id as IdColegiado, c.Matricula, ce.Especialidad as idEspecialidad, ce.FechaEspecialista, 
            ce.FechaRecertificacion, ce.FechaVencimiento, ce.IdTipoEspecialista, cet.TipoEspecialista, 
            cet.Fecha as FechaTipoEspecialista
            FROM colegiado as c
            LEFT JOIN colegiadoespecialista as ce ON (ce.IdColegiado = c.Id)
            LEFT JOIN colegiadoespecialistatipo as cet ON (cet.IdColegiadoEspecialista = ce.Id)
            WHERE c.Id = " . $idColegiado . "
            AND ce.Especialidad = " . $idEspecialidad . "
            AND ce.Estado = 'A'";
    /*
     * OBSOLETA
    $sql = "SELECT c.Id as IdColegiado, c.Matricula, ce.Especialidad as idEspecialidad, ce.FechaEspecialista, ce.FechaRecertificacion, ce.FechaVencimiento, ce.IdTipoEspecialista, cet.TipoEspecialista, cet.Fecha as FechaTipoEspecialista
            FROM colegiado as c
            LEFT JOIN colegiadoespecialista as ce ON (ce.IdColegiado = c.Id)
            LEFT JOIN colegiadoespecialistatipo as cet ON (cet.IdColegiado = c.Id AND cet.Especialidad = ce.Especialidad)
            WHERE c.Id = " . $idColegiado . "
            AND ce.Especialidad = " . $idEspecialidad . "
            AND ce.Estado = 'A'";
     * 
     */
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEspecialidadPorIdPorColegiado($idColegiado, $idEspecialidad, $tipoEspecialidad) {
    $sql = "SELECT *
            FROM mesaentradaespecialidad as mee
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = mee.IdMesaEntrada)
            WHERE me.IdColegiado = " . $idColegiado . "
            AND mee.IdEspecialidad = " . $idEspecialidad . "
            AND mee.TipoEspecialidad = '" . $tipoEspecialidad . "'
            AND me.Estado = 'A'
            AND YEAR(me.FechaIngreso) = YEAR(now())";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEstadoDeudaColegiadoPorIdMesaEntrada($idMesaEntrada) {
    $sql = "SELECT *
            FROM colegiadodeuda as cd
            WHERE cd.IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesSolicitadas() {
    $sql = "SELECT DISTINCT(me.IdMesaEntrada), me.IdColegiado, me.IdTipoMesaEntrada, me.FechaIngreso, 
        me.Observaciones, c.Matricula, p.Apellido, p.Nombres, mec.IdMesaEntradaConsultorio, con.Calle, 
        con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
        con.IdLocalidad, mec.IdConsultorio, mec.IdEspecialidad, l.Nombre as NombreLocalidad, 
        e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
        FROM mesaentrada as me
        INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
        INNER JOIN persona as p ON(p.Id = c.IdPersona)
        INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
        INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
        INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
        INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
        INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
        INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
        WHERE me.IdTipoMesaEntrada = 4
        AND con.Estado = 'A'
        AND me.Estado = 'A'
        AND me.IdMesaEntrada NOT IN(SELECT ih.IdMesaEntrada
                                    FROM inspectorhabilitacion as ih
                                    WHERE ih.Estado = 'A'
                                    )
        GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesAsignadas($idInspector) {
    $sql = "SELECT DISTINCT(ih.IdInspectorHabilitacion), me.IdMesaEntrada, me.IdColegiado, me.IdTipoMesaEntrada, 
            me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, mec.IdMesaEntradaConsultorio, 
            con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, 
            con.Observaciones as Horarios, con.IdLocalidad, mec.IdEspecialidad, l.Nombre as NombreLocalidad, 
            e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            WHERE ih.FechaInspeccion IS NULL
            AND ih.Estado = 'A'
            AND ih.IdInspector = " . $idInspector . " 
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesConfirmadas($idInspector) {
    $sql = "SELECT DISTINCT(ih.IdInspectorHabilitacion), ih.EstadoInspeccion, me.IdMesaEntrada, me.IdColegiado, 
            me.IdTipoMesaEntrada, me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, 
            mec.IdMesaEntradaConsultorio, con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, 
            con.Telefono, con.Observaciones as Horarios, con.IdLocalidad, mec.IdEspecialidad, 
            l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            WHERE ih.FechaInspeccion IS NOT NULL
            AND ih.Estado = 'A'
            AND ih.IdInspector = " . $idInspector . "
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesSolicitadasPorIdConsultorio($idConsultorio) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdTipoMesaEntrada, me.FechaIngreso, me.Observaciones, 
            c.Matricula, p.Apellido, p.Nombres, mec.IdConsultorio, MAX(mec.IdMesaEntradaConsultorio) as IdMesaEntradaConsultorio, 
            con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
            con.IdLocalidad, mec.IdEspecialidad, l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad, 
            cc.CorreoElectronico as Email, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.IdTipoMesaEntrada = 4
            AND mec.IdConsultorio = " . $idConsultorio . "
            AND me.IdMesaEntrada NOT IN(SELECT ih.IdMesaEntrada
                                        FROM inspectorhabilitacion as ih
                                        WHERE ih.Estado = 'A'
                                        )
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesAsignadasPorIdConsultorio($idConsultorio) {
    $sql = "SELECT ih.IdInspectorHabilitacion, me.IdMesaEntrada, me.IdColegiado, me.IdTipoMesaEntrada, 
            me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, mec.IdConsultorio, 
            MAX(mec.IdMesaEntradaConsultorio) as IdMesaEntradaConsultorio, con.Calle, con.Lateral, 
            con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
            con.IdLocalidad, mec.IdEspecialidad, l.Nombre as NombreLocalidad, 
            e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email,
            me.IdUsuario, u.Usuario
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE ih.FechaInspeccion IS NULL
            AND ih.Estado = 'A'
            AND mec.IdConsultorio = " . $idConsultorio . " 
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerHabilitacionesConfirmadasPorIdConsultorio($idConsultorio) {
    $sql = "SELECT ih.IdInspectorHabilitacion, ih.EstadoInspeccion, me.IdMesaEntrada, me.IdColegiado, 
            me.IdTipoMesaEntrada, me.FechaIngreso, me.Observaciones, c.Matricula, p.Apellido, p.Nombres, 
            mec.IdConsultorio, MAX(mec.IdMesaEntradaConsultorio) as IdMesaEntradaConsultorio, con.Calle, 
            con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
            con.IdLocalidad, mec.IdEspecialidad, l.Nombre as NombreLocalidad, 
            e.Especialidad as NombreEspecialidad, cc.CorreoElectronico as Email,
            me.IdUsuario, u.Usuario
            FROM inspectorhabilitacion as ih
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE ih.FechaInspeccion IS NOT NULL
            AND ih.Estado = 'A'
            AND mec.IdConsultorio = " . $idConsultorio . " 
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTodasHabilitacionesSolicitadas($idConsultorio) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.IdTipoMesaEntrada, me.FechaIngreso, me.Observaciones, 
            c.Matricula, p.Apellido, p.Nombres, mec.IdConsultorio, MAX(mec.IdMesaEntradaConsultorio) as IdMesaEntradaConsultorio, 
            con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, con.Telefono, con.Observaciones as Horarios, 
            con.IdLocalidad, mec.IdEspecialidad, l.Nombre as NombreLocalidad, e.Especialidad as NombreEspecialidad, 
            cc.CorreoElectronico as Email, me.IdUsuario, u.Usuario
            FROM mesaentrada as me
            INNER JOIN colegiado as c ON (c.Id = me.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN colegiadocontacto as cc ON (cc.IdColegiado = c.Id)
            INNER JOIN tipomesaentrada as tme ON (tme.IdTipoMesaEntrada = me.IdTipoMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN localidad as l ON (l.Id = con.IdLocalidad)
            INNER JOIN especialidad as e ON (e.Id = mec.IdEspecialidad)
            LEFT JOIN usuario as u ON (u.Id = me.IdUsuario)
            WHERE me.Estado = 'A'
            AND mec.IdConsultorio = " . $idConsultorio . "
            GROUP BY me.IdMesaEntrada";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTiposEspecialidades() {
    $sql = "SELECT *
            FROM tipoespecialista";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerEspecialidadesPorFechasPorTipo($fechaDesde, $fechaHasta, $codigo) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradaespecialidad as mee ON (mee.IdMesaEntrada = me.IdMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND mee.TipoEspecialidad = '" . $codigo . "' 
            AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultoriosPorFechasPorTipo($fechaDesde, $fechaHasta, $tipoConsultorio) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as c ON (c.IdConsultorio = mec.IdConsultorio)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND c.TipoConsultorio = '" . $tipoConsultorio . "'
            AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerNotasPorFechasColegiados($fechaDesde, $fechaHasta) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND me.IdColegiado IS NOT NULL
            AND me.Estado = 'A'
            AND men.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerNotasPorFechasRemitentes($fechaDesde, $fechaHasta) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradanota as men ON (men.IdMesaEntrada = me.IdMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND me.IdRemitente IS NOT NULL
            AND me.Estado = 'A'
            AND men.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMovimientosPorFechasPorTipo($fechaDesde, $fechaHasta, $tipoMovimiento) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            WHERE (me.FechaIngreso BETWEEN '" . $fechaDesde . "' AND '" . $fechaHasta . "')
            AND mem.IdTipoMovimiento = " . $tipoMovimiento . "
            AND me.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerSolicitudCertificadosPorFechas($fechaDesde, $fechaHasta) {
    $sql = "select solicitudcertificados.IdTipoCertificado, tipocertificado.Detalle, COUNT(*) as cantidad
            from solicitudcertificados
                 inner join tipocertificado on(tipocertificado.Id = solicitudcertificados.IdTipoCertificado)
            where solicitudcertificados.FechaSolicitud >= '" . $fechaDesde . "'
                  and solicitudcertificados.FechaSolicitud <= '" . $fechaHasta . "'
            group by solicitudcertificados.IdTipoCertificado";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectorPorIdColegiado($idColegiado) {
    $sql = "SELECT IdInspector
            FROM inspector
            WHERE IdColegiado = " . $idColegiado;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectoresParteMatricula($matricula) {
    $sql = "SELECT i.IdInspector, c.Id, p.Apellido, p.Nombres, c.Matricula
            FROM inspector as i
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE i.IdColegiado IN (SELECT Id
                                    FROM colegiado
                                    WHERE Matricula LIKE '" . $matricula . "%')
            AND i.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerColegiadoPorIdInspector($idInspector) {
    $sql = "SELECT c.Id, c.Matricula, p.Apellido, p.Nombres, p.Sexo, p.TipoDocumento, p.NumeroDocumento, 
            p.FechaNacimiento, p.IdPaises, c.Tomo, c.Folio,c.FechaMatriculacion, c.Estado, c.MatriculaNacional
            FROM colegiado as c
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            INNER JOIN inspector ON (inspector.IdColegiado = c.Id)
            WHERE IdInspector = " . $idInspector;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectores() {
    $sql = "SELECT i.*, c.Matricula, p.Apellido, p.Nombres
            FROM inspector as i
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE i.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectoresPorEstado($estado) {
    $sql = "SELECT i.*, c.Matricula, p.Apellido, p.Nombres
            FROM inspector as i
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON(p.Id = c.IdPersona)
            WHERE i.Estado = '" . $estado . "'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectorHabilitacionPorId($idInspectorHabilitacion) {
    $sql = "SELECT *
            FROM inspectorhabilitacion
            WHERE IdInspectorHabilitacion = " . $idInspectorHabilitacion;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectorHabilitacionPorIdMesaEntrada($idMesaEntrada) {
    $sql = "SELECT ih.IdInspectorHabilitacion, ih.IdMesaEntrada, ih.IdInspector, c.Matricula as MatriculaInspector, 
            p.Apellido as ApellidoInspector, p.Nombres as NombreInspector, con.IdConsultorio, 
            loc.Nombre as NombreLocalidad, con.Calle, con.Lateral, con.Numero, con.Piso, con.Departamento, 
            col.Matricula as MatriculaColegiadoConsultorio, per.Apellido as ApellidoColegiadoConsultorio, 
            per.Nombres as NombreColegiadoConsultorio, me.IdUsuario
            FROM inspectorhabilitacion as ih
            INNER JOIN inspector as i ON (i.IdInspector = ih.IdInspector)
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN colegiado as col ON (col.Id = me.IdColegiado)
            INNER JOIN persona as per ON (per.Id = col.IdPersona)
            INNER JOIN localidad as loc ON (loc.Id = con.IdLocalidad)
            WHERE ih.IdMesaEntrada = " . $idMesaEntrada . "
            AND ih.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerInspectorHabilitacionPorIdImprimir($idInspectorHabilitacion) {
    $sql = "SELECT ih.IdInspectorHabilitacion, ih.IdMesaEntrada, ih.IdInspector, 
            c.Matricula as MatriculaInspector, p.Apellido as ApellidoInspector, p.Nombres as NombreInspector, 
            con.IdConsultorio, loc.Nombre as NombreLocalidad, con.Calle, con.Lateral, con.Numero, con.Piso, 
            con.Departamento, col.Matricula as MatriculaColegiadoConsultorio, per.Apellido as ApellidoColegiadoConsultorio, 
            per.Nombres as NombreColegiadoConsultorio
            FROM inspectorhabilitacion as ih
            INNER JOIN inspector as i ON (i.IdInspector = ih.IdInspector)
            INNER JOIN colegiado as c ON (c.Id = i.IdColegiado)
            INNER JOIN persona as p ON (p.Id = c.IdPersona)
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = ih.IdMesaEntrada)
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN consultorio as con ON (con.IdConsultorio = mec.IdConsultorio)
            INNER JOIN colegiado as col ON (col.Id = me.IdColegiado)
            INNER JOIN persona as per ON (per.Id = col.IdPersona)
            INNER JOIN localidad as loc ON (loc.Id = con.IdLocalidad)
            WHERE ih.IdInspectorHabilitacion = " . $idInspectorHabilitacion . "
            AND ih.Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMesaEntradaConsultorioPorIdMesaEntrada($IdMesaEntrada) {
    $sql = "SELECT me.IdMesaEntrada, me.IdColegiado, me.FechaIngreso, mec.IdMesaEntradaConsultorio, 
            mec.IdConsultorio, mec.IdEspecialidad, me.IdUsuario
            FROM mesaentrada as me
            INNER JOIN mesaentradaconsultorio as mec ON (mec.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.IdMesaEntrada = " . $IdMesaEntrada;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerCantidadConsultoriosMesaEntrada($idConsultorio) {
    $sql = "SELECT COUNT(*) as cant
            FROM mesaentradaconsultorio as mec
            INNER JOIN mesaentrada as me ON (me.IdMesaEntrada = mec.IdMesaEntrada)
            LEFT JOIN consultoriocolegiado as cc ON (cc.IdConsultorio = mec.IdConsultorio
            AND cc.IdColegiado = me.IdColegiado)
            WHERE cc.IdConsultorioColegiado IS NULL
            AND mec.IdConsultorio = " . $idConsultorio;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerCantidadConsultoriosHabilitados($idConsultorio) {
    $sql = "SELECT COUNT(*) as cant
            FROM consultoriocolegiado
            WHERE IdConsultorio = " . $idConsultorio . "
            AND Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerConsultorioPorId($idConsultorio) {
    $sql = "SELECT c.IdConsultorio, c.TipoConsultorio, c.Nombre, c.Calle, c.Lateral, c.Numero, c.Piso, c.Departamento, c.Telefono, c.IdLocalidad, c.CodigoPostal, c.FechaCarga, c.Observaciones, c.CantidadConsultorios, l.CodigoPostal, l.idZona
            FROM consultorio as c
            INNER JOIN localidad as l ON (l.Id = c.IdLocalidad)
            WHERE IdConsultorio = " . $idConsultorio;
    $res = conectar()->query($sql);

    return $res;
}

function obtenerMatriculaJPorIdColegiado($idColegiado) {
    $sql = "SELECT *
            FROM mesaentrada
            WHERE IdColegiado = " . $idColegiado . "
            AND IdTipoMesaEntrada = '5' 
            AND FechaIngreso = '" . date("Y-m-d") . "' 
            AND Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerAutoprescripcionPorIdColegiado($idColegiado) {
    $sql = "SELECT *
            FROM mesaentrada
            WHERE IdColegiado = " . $idColegiado . "
            AND IdTipoMesaEntrada = '7' 
            AND FechaIngreso = '" . date("Y-m-d") . "' 
            AND Estado = 'A'";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************** REALIZACION DE ABM **************************************
 */

//Realiza el Alta de Movimiento con los parámetros correspondientes
//pasados en la función
function realizarAltaMesaEntrada($idColORem, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones) {

    if ($tipoRemitente == "C") {
        $consultaDatoColegiado = obtenerColegiadoPorId($idColORem);

        if (!$consultaDatoColegiado) {
            return -1;
        } else {
            if ($consultaDatoColegiado->num_rows == 0) {
                return -1;
            } else {
                $datoColegiado = $consultaDatoColegiado->fetch_assoc();
                $periodo = obtenerPeriodo();
                $estadoTesoreria = estadoTesoreriaPorColegiado($datoColegiado['Id'], $periodo);


                $sql = "INSERT INTO mesaentrada(TipoRemitente," . $ColORem . ",IdTipoMesaEntrada,FechaIngreso,Estado,IdUsuario,Observaciones,EstadoMatricular,EstadoTesoreria)
                        VALUES('" . $tipoRemitente . "','" . $idColORem . "','" . $idTipoMesaEntrada . "','" . date("Y-m-d") . "','A','" . $_SESSION['idUsuario'] . "','" . $observaciones . "', '" . $datoColegiado['Estado'] . "', '" . $estadoTesoreria . "')";
            }
        }
    } else {
        $sql = "INSERT INTO mesaentrada(TipoRemitente," . $ColORem . ",IdTipoMesaEntrada,FechaIngreso,Estado,IdUsuario,Observaciones)
            VALUES('" . $tipoRemitente . "','" . $idColORem . "','" . $idTipoMesaEntrada . "','" . date("Y-m-d") . "','A','" . $_SESSION['idUsuario'] . "','" . $observaciones . "')";
    }


    $link = conectar();
    $res = $link->query($sql);

    //$sql = "SELECT MAX(IdMesaEntrada) as IdMesaEntrada FROM mesaentrada";
    //$res=mysql_query($sql);

    if (!$res) {
        return -1;
    } else {
        return $link->insert_id;
    }
}

/*
 * ***************************************************************************
 */

function realizarImpactoMovimiento($idColegiado, $idTipoMovimiento, $fechaDesde, $distrito, $idMesaEntrada, $idPatologia) {
    $link = conectar();
    if ($distrito == 0) {
        $cierre = ",";
        $dato = ",";
    } else {
        $cierre = ",DistritoCambio,";
        $dato = ",'" . $distrito . "',";
    }

    $sql = "INSERT INTO colegiadomovimiento(IdColegiado, IdMovimiento, FechaDesde" . $cierre . "IdUsuarioCarga, FechaCarga, Estado) 
            VALUES('" . $idColegiado . "', '" . $idTipoMovimiento . "', '" . $fechaDesde . "'" . $dato . "'" . $_SESSION['idUsuario'] . "', '" . date("Y-m-d") . "', 'O')";

    $res = $link->query($sql);
    $idColegiadoMovimiento = $link->insert_id;
    if (!$res) {
        return -1;
    } else {
        $sql = "UPDATE colegiado SET Estado = '" . $idTipoMovimiento . "', FechaActualizacion = '" . date('Y-m-d') . "' WHERE Id = " . $idColegiado;

        $res = $link->query($sql);
        if (!$res) {
            return -1;
        } else {
            $sql = "INSERT INTO colegiadomovimientomesaentrada(IdColegiadoMovimiento, IdMesaEntrada)
                    VALUES('" . $idColegiadoMovimiento . "','" . $idMesaEntrada . "')";
            $res = $link->query($sql);

            if (($idTipoMovimiento == 5) || ($idTipoMovimiento == 10)) {
                $periodo = obtenerPeriodo();

                $sql = "SELECT *
                        FROM colegiadodeudaanual
                        WHERE IdColegiado = " . $idColegiado . "
                        AND Periodo = '" . $periodo . "'
                        AND Estado IN ('A', 'C')";
                $res = $link->query($sql);

                if ($res->num_rows == 0) {
                    $infoColegiado = obtenerColegiadoPorId($idColegiado);
                    $dataColegiado = $infoColegiado->fetch_assoc();

                    $antiguedad = calcularAntiguedad($dataColegiado['FechaMatriculacion']);

                    if ($antiguedad < 5) {
                        $sql = "SELECT *
                                FROM valoranualcolegiacion
                                WHERE periodo = '" . $periodo . "'
                                AND antiguedad = '1'";
                        $res = $link->query($sql);
                    } else {
                        $sql = "SELECT *
                                FROM valoranualcolegiacion
                                WHERE periodo = '" . $periodo . "'
                                AND antiguedad = '2'";
                        $res = $link->query($sql);
                    }

                    $dataValorAnualColegiacion = $res->fetch_assoc();

                    $sql = "INSERT INTO colegiadodeudaanual(Periodo, Importe, Cuotas, Antiguedad, EstadoMatricular, FechaCreacion, Estado, IdColegiado)
                            VALUES('" . $periodo . "', '" . $dataValorAnualColegiacion['Valor'] . "', '" . $dataValorAnualColegiacion['Cuotas'] . "',
                            '" . $dataValorAnualColegiacion['Antiguedad'] . "', '" . $idTipoMovimiento . "', '" . date("Y-m-d") . "', 'A', '" . $idColegiado . "')";
                    $res = $link->query($sql);

                    $idColegiadoDeudaAnual = $link->insert_id;

                    $sql = "SELECT *
                            FROM valorcuotacolegiacion
                            WHERE IdValorAnualColegiacion = " . $dataValorAnualColegiacion['Id'];
                    $cuotas = $link->query($sql);

                    while ($valorCuota = $cuotas->fetch_assoc()) {
                        $sql = "INSERT INTO colegiadodeudaanualcuotas
                                (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento,
                                Estado)
                                VALUES ('" . $idColegiadoDeudaAnual . "', '" . $valorCuota['Cuota'] . "', '" . $valorCuota['ValorColegiacion'] . "',
                                '" . $valorCuota['FechaVencimiento'] . "', '" . $valorCuota['ValorColegiacion'] . "', '" . $valorCuota['SegundoVencimiento'] . "',
                                '5')";
                        $res = $link->query($sql);
                    }
                    
                    $sql = "INSERT INTO colegiadodeudaanualtotal 
                    (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado, FechaActualizacion)
                    VALUES('" . $idColegiadoDeudaAnual . "', '" . $dataValorAnualColegiacion['PagoTotal'] . "', '" . $dataValorAnualColegiacion['VtoPagoTotal'] . "',
                    '1', '" . date("Y-m-d") . "')";
                $res = $link->query($sql);
                }
            }
            if (($idTipoMovimiento == 2) || ($idTipoMovimiento == 14) || ($idTipoMovimiento == 26)){
                //si es por cancelacion por enfermedad o jubilacion extraordinaria, se guarda la patologia 2016-11-9
                $sql = "INSERT INTO colegiadomovimientopatologia 
                    (IdColegiadoMovimiento, IdPatologia, IdUsuario, FechaCarga)
                    VALUES('" . $idColegiadoMovimiento . "', '" . $idPatologia . "', '" . $_SESSION['idUsuario'] . ",
                    now())";
                $res = $link->query($sql);
            }

            return 1;
        }
    }
}

function realizarImpactoDeuda($idColegiado) {
    $sql = "UPDATE colegiadodeudaanualcuotas as cdac, colegiadodeudaanual as cda
                SET cdac.Estado = 1, cdac.FechaActualizacion = date(now())
                WHERE cdac.IdColegiadoDeudaAnual = cda.Id
                AND cdac.SegundoVencimiento > '" . date("Y-m-d") . "'
                AND cdac.Estado = 5
                AND cda.IdColegiado = " . $idColegiado;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarImpactoCancelacion($idColegiado, $fechaDesde) {
    $sql = "UPDATE colegiadodeudaanualcuotas as cdac, colegiadodeudaanual as cda
                SET cdac.Estado = 5, cdac.FechaActualizacion = date(now())
                WHERE cdac.IdColegiadoDeudaAnual = cda.Id
                AND cdac.SegundoVencimiento > '" . $fechaDesde . "'
                AND cdac.Estado = 1
                AND cda.IdColegiado = " . $idColegiado;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarImpactoCancelacionFallecido($idColegiado) {
    $sql = "UPDATE colegiadodeudaanualcuotas as cdac, colegiadodeudaanual as cda
                SET cdac.Estado = 5, cdac.FechaActualizacion = date(now())
                WHERE cdac.IdColegiadoDeudaAnual = cda.Id
                AND cdac.Estado = 1
                AND cda.IdColegiado = " . $idColegiado;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * 5 -> Ingreso Definitivo
 * 10 -> Inscripto a otro distrito
 */

function realizarImpactoRehabilitacion($idColegiado, $fechaDesde, $idMesaEntrada) {
    $link = conectar();

    try {

        $link->query("START TRANSACTION");

        $sql = "SELECT cm.Id
                FROM colegiadomovimiento as cm
                INNER JOIN colegiado as c ON (c.Id = cm.IdColegiado)
                WHERE cm.FechaHasta IS NULL
                AND cm.IdMovimiento = c.Estado
                AND cm.IdColegiado = " . $idColegiado;
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            $idColegiadoMovimiento = -1;
        } else {
            $row = $res->fetch_assoc();
            $idColegiadoMovimiento = $row['Id'];
        }

        $sql = "UPDATE colegiadomovimiento as cm, colegiado as c
                SET cm.FechaHasta = '" . $fechaDesde . "',
                cm.FechaCargaRehabilitacion = '" . date("Y-m-d") . "',
                cm.IdUsuarioRehabilitador = '" . $_SESSION['idUsuario'] . "'
                WHERE cm.IdColegiado = c.Id
                AND cm.FechaHasta IS NULL
                AND cm.IdMovimiento = c.Estado
                AND cm.IdColegiado = " . $idColegiado;
        $res = $link->query($sql);

        //$idColegiadoMovimiento = $link->insert_id;

        $sql = "SELECT *
                FROM colegiadomovimiento
                WHERE Estado = 'O'
                AND IdColegiado = " . $idColegiado . "
                AND FechaHasta IS NULL
                AND IdMovimiento IN (SELECT Id
                                    FROM tipomovimiento
                                    WHERE Estado IN ('A', 'I'))
                ORDER BY FechaDesde DESC
                LIMIT 1";
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            //$idColegiadoMovimiento = -1;
            $estado = 1;
        } else {
            $row = $res->fetch_assoc();
            $estado = $row['IdMovimiento'];
            //$idColegiadoMovimiento = $row['Id'];
        }

        $sql = "UPDATE colegiado SET Estado = '" . $estado . "', FechaActualizacion = '" . date('Y-m-d') . "' WHERE Id = " . $idColegiado;
        $res = $link->query($sql);

        $sql = "INSERT INTO colegiadomovimientomesaentrada(IdColegiadoMovimiento, IdMesaEntrada)
                    VALUES('" . $idColegiadoMovimiento . "','" . $idMesaEntrada . "')";
        $res = $link->query($sql);

        $periodo = obtenerPeriodo();

        $sql = "SELECT *
                FROM colegiadodeudaanual
                WHERE IdColegiado = " . $idColegiado . "
                AND Periodo = '" . $periodo . "'
                AND Estado IN ('A', 'C')";
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            $infoColegiado = obtenerColegiadoPorId($idColegiado);
            $dataColegiado = $infoColegiado->fetch_assoc();

            $antiguedad = calcularAntiguedad($dataColegiado['FechaMatriculacion']);

            if ($antiguedad < 5) {
                $sql = "SELECT *
                        FROM valoranualcolegiacion
                        WHERE periodo = '" . $periodo . "'
                        AND antiguedad = '1'";
                $res = $link->query($sql);
            } else {
                $sql = "SELECT *
                        FROM valoranualcolegiacion
                        WHERE periodo = '" . $periodo . "'
                        AND antiguedad = '2'";
                $res = $link->query($sql);
            }

            if ($res->num_rows > 0) {

                $dataValorAnualColegiacion = $res->fetch_assoc();

                $sql = "INSERT INTO colegiadodeudaanual(Periodo, Importe, Cuotas, Antiguedad, EstadoMatricular, FechaCreacion, Estado, IdColegiado)
                    VALUES('" . $periodo . "', '" . $dataValorAnualColegiacion['Valor'] . "', '" . $dataValorAnualColegiacion['Cuotas'] . "',
                    '" . $dataValorAnualColegiacion['Antiguedad'] . "', '" . $estado . "', '" . date("Y-m-d") . "', 'A', '" . $idColegiado . "')";
                $res = $link->query($sql);

                $idColegiadoDeudaAnual = $link->insert_id;

                $sql = "SELECT *
                    FROM valorcuotacolegiacion
                    WHERE IdValorAnualColegiacion = " . $dataValorAnualColegiacion['Id'];
                $cuotas = $link->query($sql);

                while ($valorCuota = $cuotas->fetch_assoc()) {
                    $sql = "INSERT INTO colegiadodeudaanualcuotas
                        (IdColegiadoDeudaAnual, Cuota, Importe, FechaVencimiento, Recargo, SegundoVencimiento,
                        Estado)
                        VALUES ('" . $idColegiadoDeudaAnual . "', '" . $valorCuota['Cuota'] . "', '" . $valorCuota['ValorColegiacion'] . "',
                        '" . $valorCuota['FechaVencimiento'] . "', '" . $valorCuota['ValorColegiacion'] . "', '" . $valorCuota['SegundoVencimiento'] . "',
                        '5')";
                    $res = $link->query($sql);
                }
                
                $sql = "INSERT INTO colegiadodeudaanualtotal 
                    (IdColegiadoDeudaAnual, Importe, FechaVencimiento, IdEstado, FechaActualizacion)
                    VALUES('" . $idColegiadoDeudaAnual . "', '" . $dataValorAnualColegiacion['PagoTotal'] . "', '" . $dataValorAnualColegiacion['VtoPagoTotal'] . "',
                    '1', '" . date("Y-m-d") . "')";
                $res = $link->query($sql);
            }
        }


        $sql = "UPDATE colegiadodeudaanualcuotas as cdac, colegiadodeudaanual as cda
                SET cdac.Estado = 1
                WHERE cdac.IdColegiadoDeudaAnual = cda.Id
                AND cdac.SegundoVencimiento > '" . date("Y-m-d") . "'
                AND cdac.Estado = 5
                AND cda.IdColegiado = " . $idColegiado;
        $res = $link->query($sql);


        $link->commit();

        return 1;
    } catch (Exception $e) {

        print_r($e->getMessage());

        $link->rollback();
        return -1;
    }
}

function realizarBajaInscripcionOtroDistrito($idColegiadoMovimiento, $fechaHasta, $idColegiado, $idMesaEntrada) {
    $link = conectar();

    try {

        $link->query("START TRANSACTION");

        $sql = "UPDATE colegiadomovimiento
                SET FechaHasta = '" . $fechaHasta . "',
                IdUsuarioRehabilitador = '" . $_SESSION['idUsuario'] . "'
                WHERE Id = " . $idColegiadoMovimiento;
        $res = $link->query($sql);

        $sql = "INSERT INTO colegiadomovimientomesaentrada (IdColegiadoMovimiento, IdMesaEntrada)
                VALUES ('" . $idColegiadoMovimiento . "', '" . $idMesaEntrada . "')";
        $res = $link->query($sql);


        $sql = "SELECT *
                FROM colegiadomovimiento
                WHERE Estado = 'O'
                AND IdColegiado = " . $idColegiado . "
                AND (FechaHasta IS NULL OR FechaHasta = '0000-00-00')
                ORDER BY FechaDesde DESC
                LIMIT 1";
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            $idColegiadoMovimiento = -1;
            $estado = 1;
        } else {
            $row = $res->fetch_assoc();
            $estado = $row['IdMovimiento'];
            $idColegiadoMovimiento = $row['Id'];
        }

        $sql = "UPDATE colegiado SET Estado = '" . $estado . "', FechaActualizacion = '" . date('Y-m-d') . "' WHERE Id = " . $idColegiado;
        $res = $link->query($sql);

        $link->commit();

        return 1;
    } catch (Exception $e) {

        print_r($e->getMessage());

        $link->rollback();
        return -1;
    }
}

function rollBackBajaMovimiento($idMesaEntrada) {
    $link = conectar();

    try {

        $link->query("START TRANSACTION");

        $sql = "SELECT *
                FROM mesaentrada as me
                INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
                WHERE me.IdMesaEntrada = " . $idMesaEntrada;
        $res = $link->query($sql);

        if ($res->num_rows != 0) {
            $mesaEntrada = $res->fetch_assoc();


            if ($mesaEntrada['IdTipoMovimiento'] == 20) {
                $sql = "UPDATE colegiadomovimiento, colegiadomovimientomesaentrada
                            SET colegiadomovimiento.FechaHasta = NULL,
                            colegiadomovimiento.FechaCargaRehabilitacion = NULL,
                            colegiadomovimiento.IdUsuarioRehabilitador = NULL
                            WHERE colegiadomovimiento.Id = colegiadomovimientomesaentrada.IdColegiadoMovimiento
                            AND colegiadomovimientomesaentrada.IdMesaEntrada = " . $idMesaEntrada;
                $res = $link->query($sql);

                $sql = "SELECT *
                            FROM colegiadomovimiento as cm
                            INNER JOIN colegiadomovimientomesaentrada as cmme ON (cmme.IdColegiadoMovimiento = cm.Id)
                            WHERE cmme.IdMesaEntrada = " . $idMesaEntrada;
                $res = $link->query($sql);

                if ($res->num_rows == 0) {
                    $estado = 1;
                } else {
                    $row = $res->fetch_assoc();
                    $estado = $row['IdMovimiento'];
                }

                $setPosterior = 5;
                $setActual = 1;
            } else {
                $sql = "UPDATE colegiadomovimiento, colegiadomovimientomesaentrada
                            SET colegiadomovimiento.Estado = 'A'
                            WHERE colegiadomovimiento.Id = colegiadomovimientomesaentrada.IdColegiadoMovimiento
                            AND colegiadomovimientomesaentrada.IdMesaEntrada = " . $idMesaEntrada;
                $res = $link->query($sql);

                $sql = "SELECT *
                            FROM colegiadomovimiento
                            WHERE IdColegiado = " . $mesaEntrada['IdColegiado'] . "
                            AND FechaHasta IS NULL
                            AND Estado = 'O'
                            ORDER BY FechaDesde DESC
                            LIMIT 1";
                $res = $link->query($sql);

                if ($res->num_rows == 0) {
                    $estado = 1;
                } else {
                    $row = $res->fetch_assoc();
                    $estado = $row['IdMovimiento'];
                }

                $setPosterior = 1;
                $setActual = 5;
            }

            $sql = "UPDATE colegiado SET Estado = '" . $estado . "', FechaActualizacion = '" . date('Y-m-d') . "' WHERE Id = " . $mesaEntrada['IdColegiado'];
            $res = $link->query($sql);


            $sql = "UPDATE colegiadodeudaanualcuotas as cdac, colegiadodeudaanual as cda
                            SET cdac.Estado = " . $setPosterior . "
                            WHERE cdac.IdColegiadoDeudaAnual = cda.Id
                            AND cdac.SegundoVencimiento > '" . date("Y-m-d") . "'
                            AND cdac.Estado = " . $setActual . "
                            AND cda.IdColegiado = " . $mesaEntrada['IdColegiado'];
            $res = $link->query($sql);
        }


        $link->commit();

        return 1;
    } catch (Exception $e) {

        print_r($e->getMessage());

        $link->rollback();
        return -1;
    }
}

function realizarAltaAutoprescripcion($idMesaEntrada, $autorizado, $fecha, $documentoAutorizado, $parentezco, $autorizado2, $documentoAutorizado2, $parentezco2) {
    $sql = "INSERT INTO mesaentradaautoprescripcion
            (IdMesaEntrada,Fecha,Autorizado, DocumentoAutorizado,Parentezco,Autorizado2, DocumentoAutorizado2,Parentezco2) 
            VALUES('" . $idMesaEntrada . "','" . $fecha . "','" . $autorizado . "','" . $documentoAutorizado . "','" . $parentezco . "','" . $autorizado2 . "','" . $documentoAutorizado2 . "','" . $parentezco2 . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaDenunciaExtravio($idMesaEntrada, $fechaDenuncia, $fechaExtravio, $idTipoDenuncia) {
    $sql = "INSERT INTO mesaentradadenuncia
            (IdMesaEntrada,FechaDenuncia,FechaExtravio, IdTipoDenuncia)
            VALUES('" . $idMesaEntrada . "','" . $fechaDenuncia . "','" . $fechaExtravio . "','" . $idTipoDenuncia . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaEntrega($idMesaEntrada, $fechaEntrega, $idTipoEntrega, $idColegiado) {
    $sql = "INSERT INTO mesaentradaentrega
            (IdMesaEntrada,FechaEntrega, IdTipoEntrega)
            VALUES('" . $idMesaEntrada . "','" . $fechaEntrega . "','" . $idTipoEntrega . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        if ($idTipoEntrega == 3){
            //si es entrega de titulo especialista, marco la fecha de entrega en tituloespecialista
            $sql = "update tituloespecialista, resoluciondetalle
                    set tituloespecialista.FechaEntrega = '".$fechaEntrega."',
                        tituloespecialista.IdUsuarioEntrega = ".$_SESSION['idUsuario']."
                    where resoluciondetalle.IdColegiado = ".$idColegiado."
                    and tituloespecialista.IdResolucionDetalle = resoluciondetalle.Id";
            $res = conectar()->query($sql);
        }
        return 1;
    }
}

function realizarAltaAnulacionMovimiento($idMesaEntrada, $idMesaEntradaMovimiento) {
    $sql = "INSERT INTO mesaentradamovimientoanulacion
            (IdMesaEntrada,IdMesaEntradaMovimiento) 
            VALUES('" . $idMesaEntrada . "','" . $idMesaEntradaMovimiento . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaMovimiento($idMesaEntrada, $idTipoMovimiento, $fecha, $idMotivoCancelacion, $distrito, $obraSocial, $idPatologia) {
    if ($distrito == 0) {
        $cierre = "";
        $dato = "";
    } else {
        $cierre = ",Distrito";
        $dato = ",'" . $distrito . "'";
    }

    if (is_null($obraSocial)) {
        $cierreSocial = "";
        $datoSocial = "";
    } else {
        $cierreSocial = ",ObraSocialJubilado";
        $datoSocial = ",'" . $obraSocial . "'";
    }

    if (is_null($idPatologia)) {
        $cierrePatologia = "";
        $datoPatologia = "";
    } else {
        $cierrePatologia = ",IdPatologia";
        $datoPatologia = ",'" . $idPatologia . "'";
    }

    $sql = "INSERT INTO mesaentradamovimiento(IdMesaEntrada,IdTipoMovimiento,Fecha, IdMotivoCancelacion" . $cierre . $cierreSocial . $cierrePatologia . ") VALUES('" . $idMesaEntrada . "','" . $idTipoMovimiento . "','" . $fecha . "','" . $idMotivoCancelacion . "'" . $dato . $datoSocial . $datoPatologia . ")";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaMovimientoDistritos($idColegiado, $idTipoMovimiento, $fechaDesde, $fechaHasta, $distritoCambio, $distritoOrigen, $idMesaEntrada, $observaciones) {
    if ($fechaHasta == -1) {
        $consulta = "";
        $dato = "";
    } else {
        $consulta = " FechaHasta,";
        $dato = " '" . $fechaHasta . "',";
    }
    $sql = "INSERT INTO colegiadomovimientodistritos 
            (IdColegiado,IdMovimiento,FechaDesde," . $consulta . " DistritoCambio, DistritoOrigen, IdUsuarioCarga, 
            FechaCarga, Estado, ObservacionOtroDistrito, IdMesaEntrada) 
            VALUES('" . $idColegiado . "','" . $idTipoMovimiento . "','" . $fechaDesde . "', " . $dato . "'" . $distritoCambio . "', '" . $distritoOrigen . "', '" . $_SESSION['idUsuario'] . "', 
            '" . date("Y-m-d") . "', 'A', '" . $observaciones . "', '" . $idMesaEntrada . "') ";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarBajaMovimientoDistritos($idColegiadoMovimientoDistritos) {
    $sql = "UPDATE colegiadomovimientodistritos 
            SET Estado = 'B' 
            WHERE Id = " . $idColegiadoMovimientoDistritos;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarBajaMovimientoDistritosBajaInscripcion($idColegiadoMovimientoDistritos, $idMesaEntrada) {
    $link = conectar();

    try {

        $link->query("START TRANSACTION");

        $sql = "UPDATE colegiadomovimiento
                SET FechaHasta = NULL,
                IdUsuarioRehabilitador = NULL
                WHERE Id = " . $idColegiadoMovimientoDistritos;
        $res = $link->query($sql);

        $sql = "DELETE FROM colegiadomovimientomesaentrada
                WHERE IdColegiadoMovimiento = " . $idColegiadoMovimientoDistritos . "
                AND IdMesaEntrada = " . $idMesaEntrada;
        $res = $link->query($sql);


        $sql = "SELECT IdColegiado
                FROM colegiadomovimiento
                WHERE Id = " . $idColegiadoMovimientoDistritos;
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            $idColegiado = -1;
        } else {
            $row1 = $res->fetch_assoc();
            $idColegiado = $row1['IdColegiado'];
        }

        $sql = "SELECT *
                FROM colegiadomovimiento
                WHERE Estado = 'O'
                AND IdColegiado = " . $idColegiado . "
                AND (FechaHasta IS NULL OR FechaHasta = '0000-00-00')
                ORDER BY FechaDesde DESC
                LIMIT 1";
        $res = $link->query($sql);

        if ($res->num_rows == 0) {
            $idColegiadoMovimiento = -1;
            $estado = 1;
        } else {
            $row = $res->fetch_assoc();
            $estado = $row['IdMovimiento'];
        }

        $sql = "UPDATE colegiado SET Estado = '" . $estado . "', FechaActualizacion = '" . date('Y-m-d') . "' WHERE Id = " . $idColegiado;
        $res = $link->query($sql);

        $link->commit();

        return 1;
    } catch (Exception $e) {

        print_r($e->getMessage());

        $link->rollback();
        return -1;
    }
}

/*
 * ***************************************************************************
 */

function realizarAltaNota($idMesaEntrada, $tema, $incluye) {
    $sql = "INSERT INTO mesaentradanota(IdMesaEntrada,Tema,Estado,IncluyeMovimiento) VALUES('" . $idMesaEntrada . "','" . $tema . "','A', '" . $incluye . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaHabilitacionConsultorio($idMesaEntrada, $idConsultorio, $idEspecialidad) {
    $sql = "INSERT INTO mesaentradaconsultorio (IdMesaEntrada, IdConsultorio, IdEspecialidad)
            VALUES('" . $idMesaEntrada . "','" . $idConsultorio . "','" . $idEspecialidad . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaHabilitacionConsultorioAutorizado($idMesaEntradaConsultorio, $idColegiado) {
    $sql = "INSERT INTO mesaentradaconsultorioautorizado (IdMesaEntradaConsultorio, IdColegiado)
            VALUES('" . $idMesaEntradaConsultorio . "','" . $idColegiado . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarAltaEspecialidad($idMesaEntrada, $idEspecialidad, $tipoEspecialidad, $distrito) {
    if ($distrito == 0) {
        $cierre = ")";
        $dato = ")";
    } else {
        $cierre = ",Distrito)";
        $dato = ",'" . $distrito . "')";
    }
    $consultaNumeroExpediente = obtenerUltimoNumeroExpediente(date("Y"));

    if (!$consultaNumeroExpediente) {
        $res = false;
    } else {
        if ($consultaNumeroExpediente->num_rows == 0) {
            $sql = "INSERT INTO mesaentradaespecialidad(IdMesaEntrada,IdEspecialidad,TipoEspecialidad,NumeroExpediente,AnioExpediente" . $cierre . " VALUES('" . $idMesaEntrada . "','" . $idEspecialidad . "','" . $tipoEspecialidad . "','00001','" . date("Y") . "'" . $dato . "";
            $res = conectar()->query($sql);
        } else {
            $numeroExpediente = $consultaNumeroExpediente->fetch_assoc();
            $sql = "INSERT INTO mesaentradaespecialidad(IdMesaEntrada,IdEspecialidad,TipoEspecialidad,NumeroExpediente,AnioExpediente" . $cierre . " VALUES('" . $idMesaEntrada . "','" . $idEspecialidad . "','" . $tipoEspecialidad . "','" . ($numeroExpediente['numero'] + 1) . "','" . date("Y") . "'" . $dato . "";
            $res = conectar()->query($sql);
        }
    }

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaColegiadoDeuda($idColegiado, $idTipoPago, $importe, $idMesaEntrada) {
    $sql = "INSERT INTO colegiadodeuda(IdColegiado, FechaCreacion, IdTipoPago, Importe, IdMesaEntrada, IdTipoEstadoCuota)
            VALUES('" . $idColegiado . "','" . date("Y-m-d") . "','" . $idTipoPago . "','" . $importe . "','" . $idMesaEntrada . "','1')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaInspector($idColegiado) {
    $sql = "INSERT INTO inspector(IdColegiado, Estado, FechaCarga) VALUES('" . $idColegiado . "','A','" . date("Y-m-d") . "')";
    $link = conectar();
    $res = $link->query($sql);

    if (!$res) {
        return -1;
    } else {
        return $link->insert_id;
    }
}

function realizarAltaInspeccionSolicitada($idInspector, $idMesaEntrada) {
    $sql = "INSERT INTO inspectorhabilitacion(IdInspector, IdMesaEntrada, FechaAsignacion, Estado)
            VALUES('" . $idInspector . "','" . $idMesaEntrada . "','" . date("Y-m-d") . "','A')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaConsultorioColegiado($idConsultorio, $idColegiado, $idInspectorHabilitacion) {
    $sql = "INSERT INTO consultoriocolegiado(IdConsultorio,IdColegiado,TipoPersona,FechaCarga,Estado,IdInspectorHabilitacion)
            VALUES('" . $idConsultorio . "','" . $idColegiado . "','H','" . date("Y-m-d") . "','A','" . $idInspectorHabilitacion . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarModificacionMesaEntrada($idMesaEntrada, $observaciones) {
    $sql = "UPDATE mesaentrada SET Observaciones = '" . $observaciones . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarModificacionNota($idMesaEntrada, $tema, $observaciones, $incluye) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradanota SET Tema = '" . $tema . "', IncluyeMovimiento = '" . $incluye . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

/*
 * ***************************************************************************
 */

function realizarModificacionMovimiento($idMesaEntrada, $fechaDesde, $idTipoMovimiento, $idMotivo, $observaciones) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradamovimiento SET Fecha = '" . $fechaDesde . "', IdTipoMovimiento = '" . $idTipoMovimiento . "', IdMotivoCancelacion = '" . $idMotivo . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarModificacionAutoprescripcion($idMesaEntrada, $fecha, $autorizado, $documentoAutorizado, $parentezco, $observaciones, $autorizado2, $documentoAutorizado2, $parentezco2) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradaautoprescripcion SET Fecha = '" . $fecha . "', 
                Autorizado = '" . $autorizado . "', 
                DocumentoAutorizado = '" . $documentoAutorizado . "',
                Parentezco = '" . $parentezco . "', 
                Autorizado2 = '" . $autorizado2 . "', 
                DocumentoAutorizado2 = '" . $documentoAutorizado2 . "',
                Parentezco2 = '" . $parentezco2 . "' 
                WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarModificacionDenunciaExtravio($idMesaEntrada, $fechaDenuncia, $fechaExtravio, $idTipoDenuncia, $observaciones) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradadenuncia SET FechaDenuncia = '" . $fechaDenuncia . "', 
                FechaExtravio = '" . $fechaExtravio . "',
                IdTipoDenuncia = '" . $idTipoDenuncia . "'
                WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarModificacionEntrega($idMesaEntrada, $fechaEntrega, $idTipoEntrega, $observaciones) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradaentrega SET FechaEntrega = '" . $fechaEntrega . "', 
                IdTipoEntrega = '" . $idTipoEntrega . "'
                WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

/*
 * ***************************************************************************
 */

function realizarModificacionEspecialidad($idMesaEntrada, $idEspecialidad, $observaciones) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradaespecialidad SET IdEspecialidad = '" . $idEspecialidad . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarModificacionHabilitacionConsultorio($idMesaEntrada, $calle, $numero, $piso, $dpto, $horarios, $idEspecialidad, $idLocalidad, $observaciones) {
    $st = realizarModificacionMesaEntrada($idMesaEntrada, $observaciones);

    if ($st == 1) {
        $sql = "UPDATE mesaentradaconsultorio SET Calle = '" . $calle . "', Numero = '" . $numero . "', Piso = '" . $piso . "', Departamento = '" . $dpto . "', Horarios = '" . $horarios . "', IdEspecialidad = '" . $idEspecialidad . "', IdLocalidad = '" . $idLocalidad . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarModificacionInspectorHabilitacionPorSi($idInspectorHabilitacion, $fechaInspeccion, $fechaHabilitacion) {
    $sql = "UPDATE inspectorhabilitacion SET FechaInspeccion = '" . $fechaInspeccion . "', FechaHabilitacion = '" . $fechaHabilitacion . "', EstadoInspeccion = 'H' WHERE IdInspectorHabilitacion = " . $idInspectorHabilitacion;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarModificacionInspectorHabilitacionPorNo($idInspectorHabilitacion, $fechaInspeccion) {
    $sql = "UPDATE inspectorhabilitacion SET FechaInspeccion = '" . $fechaInspeccion . "', FechaHabilitacion = NULL, EstadoInspeccion = 'N' WHERE IdInspectorHabilitacion = " . $idInspectorHabilitacion;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarBajaMesaEntrada($idMesaEntrada) {
    $sql = "UPDATE mesaentrada SET Estado = 'B' WHERE IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAnulacionMovimiento($IdMesaEntradaMovimiento, $motivoAnulacion) {
    $sql = "INSERT mesaentradamovimientoanulacion (IdMesaEntradaMovimiento, FechaIngreso, Motivo, Estado, IdUsuario) VALUES('" . $IdMesaEntradaMovimiento . "', '" . date("Y-m-d") . "', '" . $motivoAnulacion . "', 'A', '" . $_SESSION['idUsuario'] . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarBajaNota($idMesaEntrada) {
    $st = realizarBajaMesaEntrada($idMesaEntrada);

    if ($st == 1) {
        $sql = "UPDATE mesaentradanota SET Estado = 'B' WHERE IdMesaEntrada = " . $idMesaEntrada;
        $res = conectar()->query($sql);

        if (!$res) {
            return -1;
        } else {
            return 1;
        }
    } else {
        return -1;
    }
}

function realizarBajaInspectorHabilitacion($idInspectorHabilitacion) {
    $sql = "UPDATE inspectorhabilitacion SET Estado = 'B' WHERE IdInspectorHabilitacion = " . $idInspectorHabilitacion;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaRemitente($nombreRemitente) {
    $sql = "INSERT INTO remitente(Nombre) VALUES('" . $nombreRemitente . "')";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarBajaInspector($idInspector) {
    $sql = "UPDATE inspector SET Estado = 'B', FechaBaja = '" . date("Y-m-d") . "' WHERE IdInspector = " . $idInspector;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarModificacionMatriculaJ($idMesaEntrada, $observaciones) {
    $sql = "UPDATE mesaentrada SET Observaciones = '" . $observaciones . "' WHERE IdMesaEntrada = " . $idMesaEntrada;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarAltaConsultorio($tipoConsultorio, $nombreConsultorio, $calle, $lateral, $numero, $piso, $dpto, $tel, $idLocalidad, $cp, $observaciones, $cant) {
    $sql = "INSERT INTO consultorio(TipoConsultorio, Nombre, Calle, 
            Lateral, Numero, Piso, Departamento, Telefono, IdLocalidad, 
            CodigoPostal, Estado, FechaCarga, IdUsuario, Observaciones, CantidadConsultorios) 
            VALUES('" . $tipoConsultorio . "','" . $nombreConsultorio . "','" . $calle . "',
            '" . $lateral . "','" . $numero . "','" . $piso . "','" . $dpto . "','" . $tel . "','" . $idLocalidad . "',
            '" . $cp . "','A','" . date("Y-m-d") . "','" . $_SESSION['idUsuario'] . "','" . $observaciones . "', '" . $cant . "')";
    $link = conectar();
    $res = $link->query($sql);
    if (!$res) {
        return -1;
    } else {
        return $link->insert_id;
    }
}

function realizarModificacionConsultorio($tipoConsultorio, $nombreConsultorio, $calle, $lateral, $numero, $piso, $dpto, $tel, $idLocalidad, $cp, $observaciones, $cant, $idConsultorio) {
    $sql = "UPDATE consultorio SET TipoConsultorio = '" . $tipoConsultorio . "', Nombre = '" . $nombreConsultorio . "', 
            Calle = '" . $calle . "', Lateral = '" . $lateral . "', Numero = '" . $numero . "', Piso = '" . $piso . "', 
            Departamento = '" . $dpto . "', Telefono = '" . $tel . "', IdLocalidad = '" . $idLocalidad . "', 
            CodigoPostal = '" . $cp . "', Observaciones = '" . $observaciones . "', CantidadConsultorios = '" . $cant . "'
            WHERE IdConsultorio = " . $idConsultorio;
    $link = conectar();
    $res = $link->query($sql);
    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

function realizarBajaConsultorio($idConsultorio) {
    $sql = "UPDATE consultorio SET Estado = 'B' WHERE IdConsultorio = " . $idConsultorio;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function verificarPermisoColegiado($estado, $formulario) {
    $sql = "SELECT *
            FROM tramiteestado
            WHERE Estado = '" . $estado . "'
            AND IdTipoTramite = " . $formulario;
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        if ($res->num_rows == 0) {
            return 0;
        } else {
            return 1;
        }
    }
}

/*
 * ***************************************************************************
 */

function verificarPermisoColegiadoHabilitacionConsultorio($estado, $formulario, $estadoTesoreria) {
    $sql = "SELECT *
            FROM tramiteestado
            WHERE EXISTS(SELECT *
                        FROM tramiteestado
                        WHERE Estado = '" . $estado . "'
                        AND IdTipoTramite = " . $formulario . ")";
    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        if ($res->num_rows == 0) {
            return 0;
        } else {
            if ($estadoTesoreria != 0) {
                return 0;
            } else {
                return 1;
            }
        }
    }
}

function obtenerEsUltimoMovimientoPorIdColegiado($idColegiado, $idMesaEntrada) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            WHERE me.Estado = 'A'
            AND me.IdColegiado = " . $idColegiado . "
            ORDER BY me.IdMesaEntrada DESC";
    $res = conectar()->query($sql);

    if (!$res) {
        return false;
    } else {
        $movimiento = $res->fetch_assoc();
        if ($movimiento['IdMesaEntrada'] == $idMesaEntrada) {
            return true;
        } else {
            return false;
        }
    }
}

function obtenerAnuladoPorIdMesaEntrada($idMesaEntrada) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            INNER JOIN mesaentradamovimientoanulacion as mema ON (mema.IdMesaEntradaMovimiento = mem.IdMesaEntradaMovimiento)
            WHERE me.IdMesaEntrada = " . $idMesaEntrada;

    $res = conectar()->query($sql);

    if (!$res) {
        return false;
    } else {
        if ($res->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }
}

function obtenerMesaEntradaPorIdMovimiento($idMesaEntradaMovimiento) {
    $sql = "SELECT *
            FROM mesaentrada as me
            INNER JOIN mesaentradamovimiento as mem ON (mem.IdMesaEntrada = me.IdMesaEntrada)
            WHERE mem.IdMesaEntradaMovimiento = " . $idMesaEntradaMovimiento;

    $res = conectar()->query($sql);

    if (!$res) {
        return -1;
    } else {
        $movimiento = $res->fetch_assoc();

        return $movimiento['IdMesaEntrada'];
    }
}
