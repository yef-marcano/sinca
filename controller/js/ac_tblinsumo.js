$(document).ready(function() {
    
        soloNumeros('txt_precio');
        formatear('txt_precio', 2);
        soloNumeros('txt_precioprivada');
        formatear('txt_precioprivada', 2);
        soloNumeros('txt_existencia_minima');
        soloNumeros('txt_existencia_maxima');
        
    $('#tblInsumo').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listinsumo.php"
    });

    $('#tblInsumo').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [13, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#divDropDownCategoria').dropdown('set selected', '');
        $('#divDropDownsubCategoria').dropdown('set selected', '');
        $('#divDropDownUnidadmedida').dropdown('set selected', '');
        $('#labelFormInsumo').html("Agregar Insumos");
        $('#sideBarFormInsumo').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_descripcion').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormInsumo').sidebar('hide');
    });

    $('#tabsInsumo .item').tab({history: false});
    
    xajax_findValuesDropDownCategoria();
    xajax_findValuesDropDownsubCategoria();
    xajax_findValuesDropDownUnidadmedida();

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
        }
    };

    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsInsumo .item').tab('change tab', 'first');
    $("#hdn_id").val("");
    $("#txt_categoria").val("");
    $("#txt_subcategoria").val("");
    $("#txt_descripcion").val("");
    $("#txt_unidadmedida").val("");
    $("#txt_precio").val("");
    $("#txt_precioprivada").val("");
    $("#txt_existencia_minima").val("");
    $("#txt_existencia_maxima").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function save() {
    xajax_save(xajax.getFormValues('frmInsumo'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [13, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormInsumo').html("Ver Insumo");
    $('#sideBarFormInsumo').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('#divDropDownCategoria').dropdown('destroy');
    $('#divDropDownsubCategoria').dropdown('destroy');
    $('#divDropDownnUnidadmedida').dropdown('destroy');
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [13, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormInsumo').html("Editar Insumo");    
    $('#sideBarFormInsumo').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('#divDropDownCategoria').dropdown();
    $('#divDropDownsubCategoria').dropdown();
    $('#divDropDownUnidadmedida').dropdown();
    disabledFields(false);
    $('#txt_descripcion').focus();
    $('#txt_precio').focus();
    $('#txt_precioprivada').focus();
    $('#txt_existencia_minima').focus();
    $('#txt_existencia_maxima').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [13, 4], mode: 'synchronous'}) == 0) {
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