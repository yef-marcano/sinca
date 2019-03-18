$(document).ready(function() {

    $('#tblEntrada_almacen').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listentrada_almacen.php"
    });

    $('#tblEntrada_almacen').removeClass('display').addClass('table table-striped table-bordered');
    
    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [22, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cargar('frm_entrada_almacen_planilla.php', '', '');
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
    if (xajax.call('validAction', {parameters: [22, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_entrada_almacen_planilla.php?entrada=' + id + '&view=1', '', '');
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [22, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cargar('frm_entrada_almacen_planilla.php?entrada=' + id + '&view=0', '', '');
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [22, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}