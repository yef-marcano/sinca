$(document).ready(function() {
    
        xajax_findValuesDropDownEstado(); 
    
    $("#txt_telefono").mask("0999-9999999", {placeholder: " "});
    
    
    
    $('#tblCementera_acopio').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listcementera_acopio.php"
    });

    $('#tblCementera_acopio').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [10, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormCementera_acopio').html("Agregar Cementera acopio");
        $('#sideBarFormCementera_acopio').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_descripcion').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormCementera_acopio').sidebar('hide');
    });

    $('#tabsCementera_acopio .item').tab({history: false});
    xajax_findValuesDropDownCementera();
    

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
                    prompt: 'Por favor ingrese la Descripcion'
                }]
        },
        txt_estado: {
            identifier: 'txt_estado',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el Estado'
                }]
        }
    };


    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsCementera_acopio .item').tab('change tab', 'first');
    //$("#hdn_id").val("");
    $("#txt_cementera").val("");
    //    $("#txt_descripcion").val("");
    //    $("#txt_estado").val("");
    //    $("#txt_municipio").val("");
    //    $("#txt_parroquia").val("");
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
    xajax_save(xajax.getFormValues('frmCementera_acopio'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [10, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormCementera_acopio').html("Ver Cementera acopio");
    $('#sideBarFormCementera_acopio').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [10, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormCementera_acopio').html("Editar Cementera acopio");
    $('#sideBarFormCementera_acopio').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_descripcion').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [10, 4], mode: 'synchronous'}) == 0) {
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