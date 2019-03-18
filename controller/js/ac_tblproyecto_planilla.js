$(document).ready(function() {

    xajax_findValuesDropDownCementera();
    xajax_findValuesDropDownCementera_acopio();
    xajax_findValuesDropDownTipoproyecto();
    xajax_findValuesDropDownConstrupatria();
    xajax_findValuesDropDownSectoreconomico();
    xajax_findValuesDropDownEstatusproyecto();
    xajax_findValuesDropDownEstado(); 

    $('#divDropDownNacionalidad_tecnico').dropdown();
    $('#divDropDownNacionalidad_inspector').dropdown();
    $('#divDropDownNacionalidad_residente').dropdown();
    $('#divDropDownNacionalidad_contacto').dropdown();
    $("#txt_telefono_corptecnico").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_pertecnico").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_corpinspector").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_perinspector").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_corpresidente").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_perresidente").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_corpcontacto").mask("0999-9999999", {placeholder: " "});
    $("#txt_telefono_percontacto").mask("0999-9999999", {placeholder: " "});

    soloNumeros('txt_cedula_residente');
    soloNumeros('txt_cedula_contacto');
    soloNumeros('txt_cedula_inspecor');
    soloNumeros('txt_cedula_tecnico');
    soloLetras('txt_nombre_residente');
    soloLetras('txt_nombre_contacto');
    soloLetras('txt_nombre_inspector');
    soloLetras('txt_nombre_contacto');
    soloLetras('txt_apellido_residente');
    soloLetras('txt_apellido_contacto');
    soloLetras('txt_apellido_inspector');
    soloLetras('txt_apellido_contacto');
    soloNumeros('txt_codigo_cemento');
    soloNumeros('txt_codigo_construpatria');
    

    $('#btn_cancel').click(function () {
        $('#divloader').html("");
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormProyecto').sidebar('hide');
    });

    $('#tblProyecto').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [18, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#divDropDownEstatusProyecto').dropdown('set selected', '');
        $('#divDropDownTipoproyecto').dropdown('set selected', '');
        $('#divDropDownConstrupatria').dropdown('set selected', '');
        $('#divDropDownSectoreconomico').dropdown('set selected', '');
        $('#divDropDownNacionalidad_tecnico').dropdown('set selected', '');
        $('#divDropDownNacionalidad_inspector').dropdown('set selected', '');
        $('#divDropDownNacionalidad_residente').dropdown('set selected', '');
        $('#divDropDownNacionalidad_contacto').dropdown('set selected', '');
        $('#labelFormProyecto').html("Agregar Proyecto");
        $('#sideBarFormProyecto').sidebar('show');
        //$('#btn_save').removeClass("hidden");
        $('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_nombre_proyecto').focus();
    });


    $('#tabsProyecto .item').tab({history: false});

    $("#divDropDownEstado").change(function () {      
        xajax_findValuesDropDownMunicipio($("#txt_estado").val());
    });

    $("#divDropDownMunicipio").change(function () {
        xajax_findValuesDropDownParroquia($("#txt_municipio").val());
    });

    $("#txt_cedula_tecnico").blur(function () {
        if ($('#txt_nacionalidad_tecnico').val() != "" && $('#txt_cedula_tecnico').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_tecnico').val(), $('#txt_cedula_tecnico').val(), 'txt_nombre_tecnico', 'txt_apellido_tecnico'], mode: 'synchronous'});
       }
    });

    $("#divDropDownNacionalidad_tecnico").change(function () {
        if ($('#txt_nacionalidad_tecnico').val() != "" && $('#txt_cedula_tecnico').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_tecnico').val(), $('#txt_cedula_tecnico').val(), 'txt_nombre_tecnico', 'txt_apellido_tecnico'], mode: 'synchronous'});
        }
    });
        $("#txt_cedula_inspector").blur(function () {
        if ($('#txt_nacionalidad_inspector').val() != "" && $('#txt_cedula_inspector').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_inspector').val(), $('#txt_cedula_inspector').val(), 'txt_nombre_inspector', 'txt_apellido_inspector'], mode: 'synchronous'});
        }
    });

    $("#divDropDownNacionalidad_inspector").change(function () {
        if ($('#txt_nacionalidad_inspector').val() != "" && $('#txt_cedula_inspector').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_inspector').val(), $('#txt_cedula_inspector').val(), 'txt_nombre_inspector', 'txt_apellido_inspector'], mode: 'synchronous'});
        }
    });
            $("#txt_cedula_residente").blur(function () {
        if ($('#txt_nacionalidad_residente').val() != "" && $('#txt_cedula_residente').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_residente').val(), $('#txt_cedula_residente').val(), 'txt_nombre_residente', 'txt_apellido_residente'], mode: 'synchronous'});
        }
    });

    $("#divDropDownNacionalidad_residente").change(function () {
        if ($('#txt_nacionalidad_residente').val() != "" && $('#txt_cedula_residente').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_residente').val(), $('#txt_cedula_residente').val(), 'txt_nombre_residente', 'txt_apellido_residente'], mode: 'synchronous'});
        }
    });

     $("#txt_cedula_contacto").blur(function () {
        if ($('#txt_nacionalidad_contacto').val() != "" && $('#txt_cedula_contacto').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_contacto').val(), $('#txt_cedula_contacto').val(), 'txt_nombre_contacto', 'txt_apellido_contacto'], mode: 'synchronous'});
        }
    });

    $("#divDropDownNacionalidad_contacto").change(function () {
        if ($('#txt_nacionalidad_contacto').val() != "" && $('#txt_cedula_contacto').val() != "") {
            xajax.call('searchSaime', {parameters: [$('#txt_nacionalidad_contacto').val(), $('#txt_cedula_contacto').val(), 'txt_nombre_contacto', 'txt_apellido_contacto'], mode: 'synchronous'});
        }
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
        txt_nombre: {
            identifier: 'txt_nombre',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el nombre del Proyecto'
                }]
        },
        txt_descripcion: {
            identifier: 'txt_descripcion',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la descripci&oacute;n'
                }]
        },
        txt_proyecto: {
            identifier: 'txt_proyecto',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Seleccione un proyecto'
                }]
        },
        txt_tipo_proyecto: {
            identifier: 'txt_tipo_proyecto',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Seleccione Un tipo de proyecto'
                }]
        },
        txt_sector_economico: {
            identifier: 'txt_sector_economico',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Seleccione Un Sector Economico'
                }]
        },
        txt_estatus_proyecto: {
            identifier: 'txt_estatus_proyecto',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Seleccione Estatus de proyecto'
                }]
        },
        txt_estado: {
            identifier: 'txt_estado',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese Estado'
                }]
        },
        txt_municipio: {
            identifier: 'txt_municipio',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese Municipio'
                }]
        },
        txt_parroquia: {
            identifier: 'txt_parroquia',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese Parroquia'
                }]
        },
        txt_direccion: {
            identifier: 'txt_direccion',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese direccion'
                }]
        },
//        txt_nacionalidad_tecnico: {
//            identifier: 'txt_nacionalidad_tecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Nacionalidad'
//                }]
//        },
//        txt_cedula_tecnico: {
//            identifier: 'txt_cedula_tecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Cedula'
//                }]
//        },
//        txt_nombre_tecnico: {
//            identifier: 'txt_nombre_tecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Nombre'
//                }]
//        },
//        txt_apellido_tecnico: {
//            identifier: 'txt_apellido_tecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Apellido'
//                }]
//        },
//        txt_telefono_corptecnico: {
//            identifier: 'txt_telefono_corptecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono Corporativo'
//                }]
//        },
//        txt_telefono_pertecnico: {
//            identifier: 'txt_telefono_pertecnico',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_nacionalidad_inspector: {
//            identifier: 'txt_nacionalidad_inspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Nacionalidad'
//                }]
//        },
//        txt_cedula_inspector: {
//            identifier: 'txt_cedula_inspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Cedula'
//                }]
//        },
//        txt_nombre_inspector: {
//            identifier: 'txt_nombre_inspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Nombre'
//                }]
//        },
//        txt_apellido_inspector: {
//            identifier: 'txt_apellido_inspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Apellido'
//                }]
//        },
//        txt_telefono_corpinspector: {
//            identifier: 'txt_telefono_corpinspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_telefono_perinspector: {
//            identifier: 'txt_telefono_perinspector',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_nacionalidad_residente: {
//            identifier: 'txt_nacionalidad_residente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Nacionalidad'
//                }]
//        },
//        txt_cedula_residente: {
//            identifier: 'txt_cedula_residente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Cedula'
//                }]
//        },
//        txt_nombre_residente: {
//            identifier: 'txt_nombre_residente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Nombre'
//                }]
//        },
//        txt_apellido_residente: {
//            identifier: 'txt_apellido_residente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Apellido'
//                }]
//        },
//        txt_telefono_corpresidente: {
//            identifier: 'txt_telefono_corpresidente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_telefono_perresidente: {
//            identifier: 'txt_telefono_perresidente',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_nacionalidad_contacto: {
//            identifier: 'txt_nacionalidad_contacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Nacionalidad'
//                }]
//        },
//        txt_cedula_contacto: {
//            identifier: 'txt_cedula_contacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese una Nacionalidad'
//                }]
//        },
//        txt_nombre_contacto: {
//            identifier: 'txt_nombre_contacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Nombre'
//                }]
//        },
//        txt_apellido_contacto: {
//            identifier: 'txt_apellido_contacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Apellido'
//                }]
//        },
//        txt_telefono_corpcontacto: {
//            identifier: 'txt_telefono_corpcontacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
//        txt_telefono_percontacto: {
//            identifier: 'txt_telefono_percontacto',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese un Telefono'
//                }]
//        },
        txt_cementera: {
            identifier: 'txt_cementera',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese una Cementera'
                }]
        },
        txt_cementera_acopio: {
            identifier: 'txt_cementera_acopio',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese una Cementera Acopio'
                }]
        },
//        txt_codigo_cemento: {
//            identifier: 'txt_codigo_cemento',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese el codigo'
//                }]
//        },
        txt_construpatria: {
            identifier: 'txt_construpatria',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese una construpatria'
                }]
        }
//        txt_codigo_construpatria: {
//            identifier: 'txt_codigo_construpatria',
//            rules: [{
//                    type: 'empty',
//                    prompt: 'Por favor ingrese ingrese el codigo'
//                }]
//        }
    };

      $('#divForm').form(rules, settings);

    view($('#hdn_view').val());
});

function save() {
    xajax_save(xajax.getFormValues('frmProyecto_planilla'));
}

function cleanForm() {
    $('#tabsProyecto .item').tab('change tab', 'first');
//    $("#hdn_id").val("");
    $("#txt_nombre").val("");
    $("#txt_descripcion").val("");
    $("#txt_tipo_proyecto").val("");
    $("#txt_sector_economico").val("");
    $("#txt_direccion").val("");
    $("#txt_estatus_proyecto").val("");
    $("#txt_codigo_construpatria").val("");
    $("#txt_codigo_cemento").val("");
    $("#txt_construpatria").val("");
    $("#txt_cementera").val("");
    $("#txt_cementera_acopio").val("");
    $("#txt_nacionalidad_tenico").val("");
    $("#txt_cedula_tecnico").val("");
    $("#txt_nombre_tecnico").val("");
    $("#txt_apellido_tecnico").val("");
    $("#txt_telefono_corptecnico").val("");
    $("#txt_telefono_pertecnico").val("");
    $("#txt_nacionalidad_inspector").val("");
    $("#txt_cedula_inspector").val("");
    $("#txt_nombre_inspector").val("");
    $("#txt_apellido_inspector").val("");
    $("#txt_telefono_corpinspector").val("");
    $("#txt_telefono_perinspector").val("");
    $("#txt_nacionalidad_residente").val("");
    $("#txt_cedula_residente").val("");
    $("#txt_nombre_residente").val("");
    $("#txt_apellido_residente").val("");
    $("#txt_telefono_corpresidente").val("");
    $("#txt_telefono_perresidente").val("");
    $("#txt_nacionalidad_contacto").val("");
    $("#txt_cedula_contacto").val("");
    $("#txt_nombre_contacto").val("");
    $("#txt_apellido_contacto").val("");
    $("#txt_telefono_corpcontacto").val("");
    $("#txt_telefono_percontacto").val("");    
    $("#txt_estatus").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

//function view(view) {
//    if (view == 1) {
//        $('#btn_save').addClass("hidden");
//    }
//    $('#divLoader').removeClass("disabled").addClass("active");
//    xajax.call('view', {parameters: [$('#hdn_id').val()], mode: 'synchronous'});
//}

function view(view) {
    if (view == 1) {        
        $('#btn_save').css({visibility:"hidden"});
    }
    if ($('#hdn_id').val() != "") {
        xajax.call('view', {parameters: [$('#hdn_id').val()], mode: 'synchronous'});
       // xajax.call('view', {parameters: [view], mode: 'synchronous'});
        }
}

