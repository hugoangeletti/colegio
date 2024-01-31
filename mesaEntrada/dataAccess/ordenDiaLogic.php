<?php

function obtenerOrdenes() {
    $sql = "SELECT *
            FROM ordendeldia
            WHERE Estado = 'A'
            ORDER BY Fecha DESC";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerOrdenPorId($idOrdenDia) {
    $sql = "SELECT *
            FROM ordendeldia
            WHERE Id = " . $idOrdenDia;
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function obtenerOrdenesPosteriores($idOrden) {
    $sql = "SELECT *
            FROM ordendeldia as odd1
            WHERE FechaCarga > (SELECT FechaCarga
                                FROM ordendeldia
                                WHERE Id = " . $idOrden . ")
            AND Estado = 'A' ";
    $res = conectar()->query($sql);

    return $res;
}

/*
 * ***************************************************************************
 */

function realizarAltaOrdenDiaDetalle($idOrdenDia, $idMesaEntrada, $tipoPlanilla) {
    $link = conectar();

    if (($tipoPlanilla != 3) && ($tipoPlanilla != 1)) {
        //No es Descartado

        //Dicho por Liliana: Lo que esté a partir del 01-05 arranca en 1.
        
        if ((date("Y") . "-05-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-12-31"))) {
            $anioProximo = date("Y") + 1;
            $query = " AND (odd.FechaCarga BETWEEN '" . date("Y") . "-05-01' AND '" . $anioProximo . "-04-31') ";
        } elseif ((date("Y") . "-01-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-04-31"))) {
            $anioAnterior = date("Y") - 1;
            $query = " AND (odd.FechaCarga BETWEEN '" . $anioAnterior . "-05-01' AND '" . date("Y") . "-04-31') ";
        }
        $sql1 = "SELECT MAX(oddd.Orden) as Orden
                FROM ordendeldiadetalle as oddd
                INNER JOIN ordendeldia as odd ON (odd.Id = oddd.IdOrdenDia)           
                WHERE odd.Estado = 'A'
                AND oddd.Estado = 'A'" . $query;
        
        $res1 = $link->query($sql1);

        $datoOrden = $res1->fetch_assoc();
        
        $consulta = ", Orden) ";
        $orden = $datoOrden['Orden'] + 1;
        
        $dato = ", '" . $orden . "')";
    } else {
        //ES DESCARTADO
        $consulta = ") ";
        $dato = ")";
    }



    $sql = "INSERT INTO ordendeldiadetalle (IdOrdenDia, TipoPlanilla, IdMesaEntrada, Estado" . $consulta . "
            VALUES('" . $idOrdenDia . "','" . $tipoPlanilla . "','" . $idMesaEntrada . "','A'" . $dato;

    $res = $link->query($sql);

    if (!$res) {
        return -1;
    } else {
        return 1;
    }
}

/*
 * ***************************************************************************
 */

function realizarAltaOrden($fecha, $periodo, $numeroOrden, $fechaDesde, $fechaHasta, $observaciones) {
    $sql = "INSERT INTO ordendeldia (Fecha,Periodo,Numero,FechaCarga,IdUsuario,Estado,FechaDesde,FechaHasta,Observaciones) 
            VALUES('" . $fecha . "','" . $periodo . "','" . $numeroOrden . "','" . date("Y-m-d") . "','" . $_SESSION['idUsuario'] . "','A','" . $fechaDesde . "','" . $fechaHasta . "','" . $observaciones . "')";
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

function realizarModificacionOrden($idOrdenDia, $fecha, $fechaDesde, $fechaHasta, $observaciones) {
    $sql = "UPDATE ordendeldia SET Fecha = '" . $fecha . "', FechaDesde = '" . $fechaDesde . "', FechaHasta = '" . $fechaHasta . "', Observaciones = '" . $observaciones . "' WHERE Id = " . $idOrdenDia;
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

function realizarBajaOrden($idOrdenDia) {
    $sql = "UPDATE ordendeldia SET Estado = 'B' WHERE Id = " . $idOrdenDia;
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

function realizarBajaOrdenDelDiaDetalle($idOrdenDia) {
    $sql = "UPDATE ordendeldiadetalle SET Estado = 'B' WHERE IdOrdenDia = " . $idOrdenDia;
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
?>
