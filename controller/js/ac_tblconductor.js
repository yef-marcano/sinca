$(document).ready(function() {
    $('#divDropDownNacionalidad').dropdown();
    soloLetras('txt_nombre');  
    soloLetras('txt_apellido');   
    soloNumeros('txt_cedula');
    
    $("#txt_telefono").mask("0999-9999999", {placeholder: " "});
    
    $('#tblConductor').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listconductor.php"
    });

    $('#tblConductor').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [12, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormConductor').html("Agregar Conductor");
        $('#sideBarFormConductor').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_nacionalidad').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormConductor').sidebar('hide');
    });

    $('#tabsConductor .item').tab({history: false});
    
    //xajax_findValuesDropDownNacionalidad();
    
    $("#txt_cedula").blur(function () {
        if ($('#txt_nacionalidad').val() != "" && $('#txt_cedula').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad').val(), $('#txt_cedula').val(), 'txt_nombre', 'txt_apellido'], mode: 'synchronous'});
        }
    });

    $("#divDropDownNacionalidad").change(function () {
        if ($('#txt_nacionalidad').val() != "" && $('#txt_cedula').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad').val(), $('#txt_cedula').val(), 'txt_nombre', 'txt_apellido'], mode: 'synchronous'});
        }
    });
 
    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        xt_nacionalidad: {
            identifier: 'txt_nacionalidad',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la Nacionalidad'
                }]
        },
        xt_cedula: {
            identifier: 'txt_cedula',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese Cedula'
                }]
        },
        xt_nombre: {
            identifier: 'txt_nombre',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese El Nombre'
                }]
        },
        xt_apellido: {
            identifier: 'txt_apellido',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese El apellido'
                }]
        },
        xt_telefono: {
            identifier: 'txt_telefono',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese El telefono'
                }]
        }
    };

    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsConductor .item').tab('change tab', 'first');
    $("#hdn_id").val("");
    $('#divDropDownNacionalidad').dropdown('restore defaults');
    $("#txt_nacionalidad").val("");
    $("#txt_cedula").val("");
    $("#txt_nombre").val("");
    $("#txt_apellido").val("");
    $("#txt_telefono").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function save() {
    xajax_save(xajax.getFormValues('frmConductor'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [12, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormConductor').html("Ver Conductor");
    $('#sideBarFormConductor').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [12, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormConductor').html("Editar Conductor");
    $('#sideBarFormConductor').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_nacionalidad').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [12, 4], mode: 'synchronous'}) == 0) {
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