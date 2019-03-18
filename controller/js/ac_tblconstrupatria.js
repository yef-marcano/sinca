$(document).ready(function() {

    xajax_findValuesDropDownEstado(); 
    
    $("#txt_telefono").mask("0999-9999999", {placeholder: " "});

    $('#tblConstrupatria').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listconstrupatria.php"
    });

    $('#tblConstrupatria').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [11, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormConstrupatria').html("Agregar Centro de Acopio Construpatria");
        $('#sideBarFormConstrupatria').sidebar('show');
//        $('#btn_save').removeClass("hidden");
    $('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_descripcion').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormConstrupatria').sidebar('hide');
    });

    $('#tabsConstrupatria .item').tab({history: false});
    
    $("#divDropDownEstado").change(function () {      
        xajax_findValuesDropDownMunicipio($("#txt_estado").val());
    });

    $("#divDropDownMunicipio").change(function () {
        xajax_findValuesDropDownParroquia($("#txt_municipio").val());
    });
    
     cleanForm();
    
    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        txt_descripcion: {
            identifier: 'txt_descripcion',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la descripci&oacute;n'
                }]
        },
        txt_municipio: {
            identifier: 'txt_descripcion',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la descripci&oacute;n'
                }]
        }
    };

    $('#divForm1').form(rules, settings);
    

    view($('#hdn_view').val());
});

function cleanForm() {
    $("#txt_descripcion").val("");
    $("#txt_direccion").val("");
    $("#txt_telefono").val("");
    $("#txt_estatus").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function save() {
    xajax_save(xajax.getFormValues('frmConstrupatria'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [11, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormConstrupatria').html("Ver Centro de Acopio Construpatria");
    $('#sideBarFormConstrupatria').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [11, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormConstrupatria').html("Editar Centro de Acopio Construpatria");
    $('#sideBarFormConstrupatria').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_descripcion').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [11, 4], mode: 'synchronous'}) == 0) {
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