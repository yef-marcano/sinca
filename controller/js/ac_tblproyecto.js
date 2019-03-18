$(document).ready(function() {

    $('#tblProyecto').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listproyecto.php"
    });

    $('#tblProyecto').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [18, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_proyecto_planilla.php', '', '');
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
    if (xajax.call('validAction', {parameters: [18, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_proyecto_planilla.php?proyecto=' + id + '&view=1', '', '');
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [18, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_proyecto_planilla_edit.php?proyecto=' + id + '&view=0', '', '');
}
function deleteData(id) {
    if (xajax.call('validAction', {parameters: [18, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}