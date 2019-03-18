$(document).ready(function() {
    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            valid();
        }
    };

    var rules = {
        txt_user: {
            identifier: 'txt_user',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el usuario'
                }]
        },
        txt_pass: {
            identifier: 'txt_pass',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Por favor ingrese la contrase&ntilde;a'
                }
            ]
        }
    };

    $('#divForm1').form(rules, settings);
});

function valid() {
    xajax_validUser(xajax.getFormValues('frmLogin'));
}