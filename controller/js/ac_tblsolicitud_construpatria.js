$(document).ready(function() {

    $('#tblSolicitud_construpatria').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listsolicitud_construpatria.php"
    });

    $('#tblSolicitud_construpatria').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [21, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_solicitud_planilla_construpatria.php', '', '',21);
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
    if (xajax.call('validAction', {parameters: [21, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_solicitud_planilla_construpatria.php?solicitud=' + id + '&view=1', '', '',21);
    //alert("hola");
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [21, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_solicitud_planilla_construpatria.php?solicitud=' + id + '&view=0', '', '',21);
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [21, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}
