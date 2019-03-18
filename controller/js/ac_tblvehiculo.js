$(document).ready(function() {
    
    
    $('#tblVehiculo').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listvehiculo.php"
    });

    $('#tblVehiculo').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [17, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormVehiculo').html("Agregar Vehiculo");
        $('#sideBarFormVehiculo').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_placa').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormVehiculo').sidebar('hide');
    });

    $('#tabsVehiculo .item').tab({history: false});

    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        xt_placa: {
            identifier: 'txt_placa',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la Placa'
                }]
        },
        xt_placa_batea: {
            identifier: 'txt_placa_batea',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el nombre del Placa Batea'
                }]
        },
        xt_marca: {
            identifier: 'txt_marca',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la Marca'
                }]
        },
        xt_color: {
            identifier: 'txt_color',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese El Color'
                }]
        }
    };

    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsVehiculo .item').tab('change tab', 'first');
    $("#hdn_id").val("");
    $("#txt_placa").val("");
    $("#txt_placa_batea").val("");
    $("#txt_marca").val("");
    $("#txt_color").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function save() {
    xajax_save(xajax.getFormValues('frmVehiculo'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [17, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormVehiculo').html("Ver Vehiculo");
    $('#sideBarFormVehiculo').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [17, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormVehiculo').html("Editar Vehiculo");
    $('#sideBarFormVehiculo').sidebar('show');
    //$('#btn_save').removeClass("hidden");
    $('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_codigo').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [17, 4], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    if (confirm("Seguro desea eliminar el registro con el id " + id + "?")) {
        xajax_delete(id);
    }
}

function disabledFields(bool) {
    if (bool) {
        $(".field input").attr({
            readonly: "readonly",
            disabled: "disabled"
        });
    } else {
        $(".field input").removeAttr('readonly');
        $(".field input").removeAttr('disabled');
    }
}