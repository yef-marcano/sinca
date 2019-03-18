$(document).ready(function() {

    $('#tblUsuario').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listusuario.php"
    });

    $('#tblUsuario').removeClass('display').addClass('table table-striped table-bordered');
    
    $('#divDropDownCambiarClave').dropdown();

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [5, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#divDropDownPerfil').dropdown('set selected', '');
        $('#labelFormUsuario').html("Agregar Usuario");
        $('#sideBarFormUsuario').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_nombre').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormUsuario').sidebar('hide');
    });

    $('#tabsUsuario .item').tab({history: false});

    xajax_findValuesDropDownPerfil();
    xajax_findValuesDropDownEstado();
    //xajax_findValuesDropDownBaseMision();

    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        txt_nombre: {
            identifier: 'txt_nombre',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el nombre'
                }]
        },
        txt_apellido: {
            identifier: 'txt_apellido',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el apellido'
                }]
        },
        txt_usuario: {
            identifier: 'txt_usuario',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el usuario'
                }]
        },
        txt_password: {
            identifier: 'txt_password',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la contrase&ntilde;a'
                },
                {
                    type: 'length[6]',
                    prompt: 'La contrase&ntilde;a debe tener al menos 6 caracteres'
                }]
        },
        txt_password_repeat: {
            identifier: 'txt_password_repeat',
            rules: [{
                    type: 'match[txt_password]',
                    prompt: 'Las contrase&ntilde;a no coinciden'
                }]
        },
        txt_cambiar_clave: {
            identifier: 'txt_cambiar_clave',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor seleccione una opci&oacute;n'
                }
            ]
        },
        txt_perfil: {
            identifier: 'txt_perfil',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor seleccione un perfil'
                }
            ]
        }
    };

    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsUsuario .item').tab('change tab', 'first');
    $("#hdn_id").val("");
    $("#txt_nombre").val("");
    $("#txt_apellido").val("");
    $("#txt_usuario").val("");
    $("#txt_password").val("");
    $("#txt_password_repeat").val("");
    $("#txt_cambiar_clave").val("");
    $("#txt_perfil").val("");
    $("#txt_estado").val("");    
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function save() {
    //alert('Resgistro Exitoso');
    xajax_save(xajax.getFormValues('frmUsuario'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [5, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormUsuario').html("Ver Usuario");
    $('#sideBarFormUsuario').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('#divDropDownPerfil').dropdown('destroy');
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [5, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormUsuario').html("Editar Usuario");
    $('#sideBarFormUsuario').sidebar('show');
    //$('#btn_save').removeClass("hidden");
    $('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('#divDropDownPerfil').dropdown();
    disabledFields(false);
    $('#txt_nombre').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [5, 4], mode: 'synchronous'}) == 0) {
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