<?php

function obtenerEleccionVigente() {
    $conect = conectar();
    $sql = "SELECT *
            FROM elecciones
            WHERE Estado = 'A'";
    $stmt = $conect->query($sql);

    $result = $stmt->fetch_assoc();

    return $result;
}

function obtenerEleccionLocalidadPorIdEleccionPorLocalidad($IdElecciones, $localidad) {
    $conect = conectar();
    $sql = "SELECT IdEleccionesLocalidad, IdElecciones, Localidad, CantidadDelegados, CantidadElectores, CantidadValidos, CantidadAnulados, CantidadEnBlanco, CocienteElectoral, LocalidadDetalle
            FROM eleccioneslocalidad
            WHERE IdElecciones = ?
            AND Localidad = ?";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $IdElecciones, $localidad);
    $stmt->execute();
    $stmt->bind_result($IdEleccionesLocalidad, $IdElecciones, $Localidad, $CantidadDelegados, $CantidadElectores, $CantidadValidos, $CantidadAnulados, $CantidadEnBlanco, $CocienteElectoral, $LocalidadDetalle);

    $stmt->store_result();

    if ($stmt->num_rows() > 0) {
        $row = $stmt->fetch();
        $datos = array(
            "IdEleccionesLocalidad" => $IdEleccionesLocalidad,
            "IdElecciones" => $IdElecciones,
            "Localidad" => $Localidad,
            "CantidadDelegados" => $CantidadDelegados,
            "CantidadElectores" => $CantidadElectores,
            "CantidadValidos" => $CantidadValidos,
            "CantidadAnulados" => $CantidadAnulados,
            "CantidadEnBlanco" => $CantidadEnBlanco,
            "CocienteElectoral" => $CocienteElectoral,
            "LocalidadDetalle" => $LocalidadDetalle,
        );

        return $datos;
    }
    return false;
}

function obtenerEleccionesResultado($anio, $localidad) {
    $conect = conectar();
    $sql = "SELECT elr.IdELLista, SUM(elr.CantidadVotos) as Cantidad, ell.Nombre, ellr.CocienteObtenido, ellr.CantidadDelegados
            FROM eleccioneslocalidadresultado as elr
            INNER JOIN eleccioneslocalidadlista as ell on(ell.IdELLista = elr.IdELLista)
            INNER JOIN eleccioneslocalidad as el on(el.IdEleccionesLocalidad = ell.IdEleccionesLocalidad)
            INNER JOIN elecciones as e on(e.IdElecciones = el.IdElecciones)
            INNER JOIN eleccioneslocalidadlistaresultado as ellr on(ellr.IdELLista = ell.IdELLista)
            WHERE e.Anio = ?
            AND el.Localidad = ?
            GROUP BY elr.IdELLista
            ORDER BY ell.Nombre";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('is', $anio, $localidad);
    $stmt->execute();

    $stmt->bind_result($IdELLista, $Cantidad, $Nombre, $CocienteObtenido, $CantidadDelegados);

    $stmt->store_result();

    $result = array();

    if ($stmt->num_rows() > 0) {
        while ($stmt->fetch()) {
            $datos = array(
                "IdELLista" => $IdELLista,
                "Cantidad" => $Cantidad,
                "Nombre" => $Nombre,
                "CocienteObtenido" => $CocienteObtenido,
                "CantidadDelegados" => $CantidadDelegados,
            );
            array_push($result, $datos);
        }
    }
    return $result;
}

function obtenerVotosPorTipo($anio, $tipo, $localidad) {
    $conect = conectar();
    $sql = "SELECT SUM(elr.CantidadVotos) as Cantidad
            FROM eleccioneslocalidadresultado as elr
            INNER JOIN eleccioneslocalidadlista as ell on(ell.IdELLista = elr.IdELLista)
            INNER JOIN eleccioneslocalidad as el on(el.IdEleccionesLocalidad = ell.IdEleccionesLocalidad)
            INNER JOIN elecciones as e on(e.IdElecciones = el.IdElecciones)
            WHERE e.Anio = ?
            AND el.Localidad = ?
            AND ell.TipoLista = ?
            GROUP BY el.Localidad
            ORDER BY elr.IdELLista";
    $stmt = $conect->prepare($sql);
    $stmt->bind_param('iss', $anio, $localidad, $tipo);
    $stmt->execute();

    $stmt->bind_result($Cantidad);

    $stmt->store_result();

    if ($stmt->num_rows() > 0) {
        $row = $stmt->fetch();
        $datos = array(
            "Cantidad" => $Cantidad,
        );

        return $datos;
    }

    return false;
}
