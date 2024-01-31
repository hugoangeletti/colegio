/* 
 *  Comprueba el campo del select del Tipo de Movimiento
 *  para saber si debe visualizar el Motivo de Cancelación o no 
 */
function motivo_cancelacion() {
    if ($("#tipo_movimiento").val() == 'idTipoMovimiento') {
        $("#mot-cancel").show();
    } else {
        $("#mot-cancel").hide();
    }
}



$(document).ready(function() {
    $(".tipoSolicitante").click(function() {
        $.ajax({
            url: $("#formSolicitante").attr("action"),
            type: "post",
            data: $("#formSolicitante").serialize(),
            success: function(data) {
                $("#consulta").html(data);
            }
        });
    });
    var hidden;
    $("#zone-bar li em").click(function() {
        var hidden = $(this).parents("li").children("ul").is(":hidden");

        $("#zone-bar>ul>li>ul").hide();
        $("#zone-bar>ul>li>a").removeClass();

        if (hidden) {
            $(this)
                    .parents("li").children("ul").toggle()
                    .parents("li").children("a").addClass("zoneCur");
        }
    });


});

/* Verificacion de campo fecha y Configuracion de la funcion "Calendario" de JQUERY */

function verif_fecha(id) {
    var regexDateValidator = function(fecha) {
        return (fecha).match(/^(0[1-9]|[12][0-9]|3[01])\-(0[1-9]|1[012])\-([1-2][0-9][0-9][0-9])/);
    };
    accept = regexDateValidator($('#' + id).val());
    if ((!accept) && ($('#' + id).val() != '')) {
        alert('¡Fecha inválida!');
        return false;
    }
    return true;
}

function verif_fecha_date(id) {
    var regexDateValidator = function(fecha) {
        return (fecha).match(/^([1-2][0-9][0-9][0-9])\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/);
    };
    accept = regexDateValidator($('#' + id).val());
    if ((!accept) && ($('#' + id).val() != '')) {
        alert('¡Fecha inválida!');
        return false;
    }
    return true;
}

function verif_desde_hasta(fD, fH)
{
    if (verif_fecha(fD))
    {
        if (verif_fecha(fH))
        {
            var fechaDesde = $("#" + fD).val().split(/-/);
            fechaDesde.reverse();

            var fechaHasta = $("#" + fH).val().split(/-/);
            fechaHasta.reverse();

            if (fechaDesde.join('-') <= fechaHasta.join('-'))
            {
                return true;
            }
            else
            {
                alert('¡Fecha Desde tiene que ser menor o igual que Fecha Hasta');
            }
        }
    }
    return false;
}

function verif_extravio_denuncia(fE, fD)
{
    if (verif_fecha_date(fE))
    {
        if (verif_fecha_date(fD))
        {
            var fechaDesde = $("#" + fE).val().split(/-/);
            fechaDesde.reverse();

            var fechaHasta = $("#" + fD).val().split(/-/);
            fechaHasta.reverse();

            if (fechaDesde.join('-') <= fechaHasta.join('-'))
            {
                return true;
            }
            /*
            else
            {
                alert('¡La Fecha de Extravío tiene que ser menor o igual que la Fecha de Denuncia');
            }
            */
        }
    }
    return false;
}

function verif_num(id)
{
    if (isNaN($('#' + id).val()))
    {
        alert("¡Matrícula Inválida!");
        $('#' + id).focus();
        return false;
    }
    return true;
}

$(function() {
    $("#tabs").tabs({
        beforeLoad: function(event, ui) {
            var tab_seleccionado = this;
            ui.jqXHR.error(function() {
                ui.panel.html(
                        "Hubo un error al cargar el contenido.");
            });
        }
    });
});
