$(document).ready(function () {

    $('#tblSolicitud').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listsolicitud.php"
    });

    $('#tblSolicitud').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function () {
        if (xajax.call('validAction', {parameters: [19, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_solicitud_planilla.php', '', '',19);
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
    if (xajax.call('validAction', {parameters: [19, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_solicitud_planilla.php?solicitud=' + id + '&view=1', '', '',19);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [19, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_solicitud_planilla.php?solicitud=' + id + '&view=0', '', '',19);
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [19, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}