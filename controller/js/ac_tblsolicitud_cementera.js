$(document).ready(function() {

    $('#tblSolicitud_cementera').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listsolicitud_cementera.php"
    });

    $('#tblSolicitud_cementera').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [20, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_solicitud_planilla_cementera.php', '', '',20);
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
    if (xajax.call('validAction', {parameters: [20, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    //alert("hola");
    cargar('frm_solicitud_planilla_cementera.php?solicitud=' + id + '&view=1', '', '',20);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [20, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_solicitud_planilla_cementera.php?solicitud=' + id + '&view=0', '', '',20);
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [20, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}