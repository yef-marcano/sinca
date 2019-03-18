$(document).ready(function () {
    xajax.call('buildMenu', {parameters: [], mode: 'synchronous'});
    menu = {};
    menu.ready = function () {
        var $menuItem = $('.menu a.item, .menu .link.item'), handler = {
            activate: function () {
                $(this).addClass('active').closest('.ui.menu').find('.item').not($(this)).removeClass('active');
            }
        };

        $menuItem.on('click', handler.activate);
    };
    $(document).ready(menu.ready);

    $(document).ready(abrirModal());

    $('.ui.dropdown').dropdown();

    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function () {
            xajax.call('updatePassword', {parameters: [xajax.getFormValues('formHome')], mode: 'synchronous'});
        }
    };


    var rules = {
        txt_password_home: {
            identifier: 'txt_password_home',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la contrase&ntilde;a'
                },
                {
                    type: 'length[6]',
                    prompt: 'La contrase&ntilde;a debe tener al menos 6 caracteres'
                }]
        },
        txt_password_repeat_home: {
            identifier: 'txt_password_repeat_home',
            rules: [{
                    type: 'match[txt_password_home]',
                    prompt: 'Las contrase&ntilde;a no coinciden'
                }]
        }
    };

    $('#divFormHome').form(rules, settings);

    $('#btn_update').click(function () {
        $('#divFormHome').form('submit');
    });

    $('#btn_close').click(function () {
        $('#divModal').modal('hide');
    });
});

function cargar(frm, mod, icon) {
    $("#divloader").load(frm, function (response, status, xhr) {
        if (status == "error") {
            alert(xhr.status + " " + xhr.statusText);
        } else if (status == "success") {
            $('#divModuloLabel').html("<i class='" + icon + "'></i>" + mod);
        }
    });

}

function cerrarSession() {
    location.href = "frm_logout.php";
}

function abrirModal(opcion) {
    $('#txt_password_home').val();
    $('#txt_password_repeat_home').val();
    
    if ($('#hdn_cambiar_clave').val() == 1) {
        $('#divModal').modal('setting', 'transition', 'horizontal flip').modal('setting', 'closable', false).modal('show');
    } else {
        $('#btn_close').removeClass("disabled");
        if(opcion){
            $('#divModal').modal('setting', 'transition', 'horizontal flip').modal('setting', 'closable', true).modal('show');
        }
    }
}