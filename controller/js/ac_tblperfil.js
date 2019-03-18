$(document).ready(function() {

    $('#tblPerfil').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listperfil.php"
    });

    $('#tblPerfil').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [4, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#tabsPerfil .item').tab('change tab', 'first');
        $('#labelFormPerfil').html("Agregar Perfil");
        $('#sideBarFormPerfil').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_descripcion').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormPerfil').sidebar('hide');
    });

    $('#tabsPerfil .item').tab({history: false});

    xajax.call('findMenuAction', {parameters: [1], mode: 'synchronous'});

    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        xt_descripcion: {
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
    $("#hdn_id").val("");
    $("#txt_descripcion").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
    $('.checkbox').checkbox('disable');
    $('.title').removeClass("active");
    $('.content').removeClass("active");
}

function save() {
    xajax_save(xajax.getFormValues('frmPerfil'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [4, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#tabsPerfil .item').tab('change tab', 'first');
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormPerfil').html("Ver Perfil");
    $('#sideBarFormPerfil').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [4, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#tabsPerfil .item').tab('change tab', 'first');
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormPerfil').html("Editar Perfil");
    $('#sideBarFormPerfil').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_descripcion').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [4, 4], mode: 'synchronous'}) == 0) {
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

function checkAll(button, idMenu) {
    if ($(button).attr('class') == "ui mini button green") {
        $('#content_' + idMenu + ' .checkbox').checkbox('enable');
        $(button).removeClass("green").addClass("red");
    } else {
        $('#content_' + idMenu + ' .checkbox').checkbox('disable');
        $(button).removeClass("red").addClass("green");
    }

}