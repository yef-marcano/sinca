$(document).ready(function() {

    $('#tblToma_inventario').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listtoma_inventario.php"
    });

    $('#tblToma_inventario').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [26, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_toma_inventario_planilla.php', '', '');
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
    if (xajax.call('validAction', {parameters: [26, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_toma_inventario_planilla.php?inventario=' + id + '&view=1', '', '');
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [26, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#btn_save').css({visibility:"hidden"});
    disabledFields(true);
    cargar('frm_toma_inventario_planilla.php?inventario=' + id + '&view=0', '', '');
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [26, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}