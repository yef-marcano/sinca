$(document).ready(function() {

    $('#tblOrden_salida_cementera').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listorden_salida_cementera.php"
    });

    $('#tblOrden_salida_cementera').removeClass('display').addClass('table table-striped table-bordered');
    
    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [24, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_orden_salida_cementera_planilla.php', '', '');
    });

});

function cargar(frm, mod, icon) {
    $("#divloader").load(frm, function(response, status, xhr) {
        if (status == "error") {
            alert(xhr.status + " " + xhr.statusText);
        } else if (status == "success") {
            $('#divModuloLabel').html("<i class='" + icon + "'></i>" + mod);
        }
    });

}


function viewData(id) {
    if (xajax.call('validAction', {parameters: [24, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_orden_salida_cementera_planilla.php?orden=' + id + '&view=1', '', '');
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [24, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_orden_salida_cementera_planilla?orden.php=' + id + '&view=0', '', '');
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [24, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}