<?php

//HUGO
function obtenerTipoMovimiento($idTipoMovimiento) {
    $sql = "SELECT * 
            FROM tipomovimiento 
            WHERE Id = " . $idTipoMovimiento;
    $res = conectar()->query($sql);

    return $res;
}

//TRAE TODOS LOS TIPOS DE MOVIMIENTOS DE OTROS DISTRITOS
function obtenerTiposMovimientosOtrosDistritos() {
    $sql = "SELECT *
            FROM tipomovimientootrodistrito
            ORDER BY DetalleCompleto";
    $res = conectar()->query($sql);

    return $res;
}

//TRAE TODOS LOS TIPOS DE MOVIMIENTOS
function obtenerTiposMovimientos() {
    $sql = "SELECT * 
            FROM tipomovimiento 
            ORDER BY DetalleCompleto";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTiposDenuncia() {
    $sql = "SELECT * 
            FROM tipodenuncia 
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTiposEntrega() {
    $sql = "SELECT * 
            FROM tipoentrega 
            ORDER BY Nombre";
    $res = conectar()->query($sql);

    return $res;
}

function obtenerTipoMovimientoMesaEntrada($idMovimientoActual) {
    $sql = "SELECT tm.Id, tm.DetalleCompleto
            FROM movimientomesaentradas mme
            INNER JOIN tipomovimiento tm ON(tm.Id = mme.IdTipoMovimiento)
            WHERE mme.IdMovimientoActual = " . $idMovimientoActual . " 
            ORDER BY tm.DetalleCompleto";
    $res = conectar()->query($sql);

    return $res;
}

function estadoColegiado($estado) {
    switch ($estado) {
        case 'A': return "ACTIVO";
            break;
        case 'C': return "BAJA";
            break;
        case 'F': return "FALLECIDO";
            break;
        case 'I': return "INSCRIPTO";
            break;
        case 'J': return "JUBILADO";
            break;
    }
}

?>
