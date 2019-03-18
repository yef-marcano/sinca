function showMessage(typeMessage) {
    if (typeMessage == 1) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon checkmark'></i>Guardado con exito</div>");
    } else if (typeMessage == 2) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon checkmark'></i>Actualizado con exito</div>");
    } else if (typeMessage == 3) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon checkmark'></i>Eliminado con exito</div>");
    } else if (typeMessage == 4) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon checkmark'></i>Publicado con exito</div>");
    } else if (typeMessage == 5) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon checkmark'></i>Despublicado con exito</div>");
    } else if (typeMessage == 6) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small Error floating hidden message'><i class='icon remove sign'></i>Error. Contacte con su administrador web</div>");
    } else if (typeMessage == 7) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small Error floating hidden message'><i class='icon remove sign'></i>No posee privilegios</div>");
    } else if (typeMessage == 8) {
        $("#divMessageColumn").html("<div id='divMessage' class='ui small success floating hidden message'><i class='icon remove sign'></i>Denegada con exito</div>");
    }

    $('#divMessage').transition({
        animation: 'horizontal flip'
    }).transition({
        animation: 'horizontal flip',
        duration: '4s'
    });
}

function showMessage2(div, typeMessage, msg) {
    if (typeMessage == 1) {
        $("#" + div).html("<div id='divMessage' class='ui small green floating hidden message'><i class='icon checkmark'></i>" + msg + "</div>");
    } else if (typeMessage == 2) {
        $("#" + div).html("<div id='divMessage' class='ui small red floating hidden message'><i class='icon remove sign'></i>" + msg + "</div>");
    }

    $('#divMessage').transition({
        animation: 'horizontal flip'
    });
}

function soloNumeros(idElement) {
    $('#' + idElement).keyup(function () {
        this.value = (this.value + '').replace(/[^0-9+\-Ee.]/g, '');
    });
}

function soloLetras(idElement) {
    $('#' + idElement).keyup(function () {
        this.value = (this.value + '').replace(/[^a-zA-z+\-Ee.]/g, '');
    });
}

function validarFecha(fecha) {
    var valida = true;
    var matches = /^(\d{2})[-](\d{2})[-](\d{4})$/.exec(fecha);

    if (matches == null) {
        valida = false;
    } else {
        var d = parseInt(matches[1]);
        var m = parseInt(matches[2] - 1);
        var a = parseInt(matches[3]);
        var f = new Date(a, m, d);

        console.log(f.getDate());
        console.log(d);
        console.log(f.getMonth());
        console.log(m);
        console.log(f.getFullYear());
        console.log(a);

        if (f.getDate() != d || f.getMonth() != m || f.getFullYear() != a) {
            valida = false;
        }
    }
    return valida;
}

function formatear(idElement, decimal) {
    element = $("#" + idElement);
    element.blur(function () {
        $("#" + idElement).val(Format(valorpase($("#" + idElement).val()), decimal));
    });
}

function Format(value, decimals, separators) {
    decimals = decimals >= 0 ? parseInt(decimals, 0) : 2;
    separators = separators || ['.', ".", ','];
    var number = (parseFloat(value) || 0).toFixed(decimals);
    if (number.length <= (4 + decimals))
        return number.replace('.', separators[separators.length - 1]);
    var parts = number.split(/[-.]/);
    value = parts[parts.length > 1 ? parts.length - 2 : 0];
    var result = value.substr(value.length - 3, 3) + (parts.length > 1 ?
            separators[separators.length - 1] + parts[parts.length - 1] : '');
    var start = value.length - 6;
    var idx = 0;
    while (start > -3) {
        result = (start > 0 ? value.substr(start, 3) : value.substr(0, 3 + start)) + separators[idx] + result;
        idx = (++idx) % 2;
        start -= 3;
    }
    return (parts.length == 3 ? '-' : '') + result;
}

function valorpase(a) {
    var valorpas = 0;
    valorpas = a;
    valorpas = valorpas.toString().replace(/\$|\,/g, '');
    valorpas = parseFloat(valorpas);
    return valorpas;
}