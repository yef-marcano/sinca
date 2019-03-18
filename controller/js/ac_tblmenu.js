$(document).ready(function() {

    $('#tblMenu').dataTable({
        "processing": true,
        "serverSide": true,
        "language": {
            "url": "../config/es_ES.txt"
        },
        "ajax": "../controller/php/ac_listmenu.php"
    });

    $('#tblMenu').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [1, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#divDropDownModulo').dropdown('set selected', '');
        $('#divDropDownAccion').dropdown('set selected', '');
        $('#labelFormMenu').html("Agregar Menu");
        $('#sideBarFormMenu').sidebar('show');
//        $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_descripcion').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm1").find(".field.error").find(".prompt").remove();
        $("#divForm1").find(".field.error").removeClass("error");
        $('#sideBarFormMenu').sidebar('hide');
    });

    $('#btn_accion').click(function() {
        if ($('#divDropDownAccion').dropdown('get value')) {
            insertActionTable($('#divDropDownAccion').dropdown('get text'), $('#divDropDownAccion').dropdown('get value'));
        }
    });

    $('#tabsMenu .item').tab({history: false});

    xajax_findValuesDropDownModulo();
    xajax_findValuesDropDownAccion();

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
        txt_archivo: {
            identifier: 'txt_archivo',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Por favor ingrese el archivo'
                }
            ]
        },
        txt_icono: {
            identifier: 'txt_icono',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Por favor ingrese el &iacute;cono'
                }
            ]
        },
        txt_orden: {
            identifier: 'txt_orden',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Por favor ingrese el orden'
                }
            ]
        }
    };

    $('#divForm1').form(rules, settings);
});

function cleanForm() {
    $('#tabsMenu .item').tab('change tab', 'first');
    $("#hdn_id").val("");
    $("#txt_modulo").val("");
    $("#txt_descripcion").val("");
    $("#txt_archivo").val("");
    $("#txt_orden").val("");
    $("#txt_icono").val("");
    $("#hdn_idaccion").val("");
    $("#hdn_textaccion").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
    $("#tblAction").html("&nbsp;");
    $("#divOptionDropDownAccion .item").removeClass("hidden");
}

function save() {
    xajax_save(xajax.getFormValues('frmMenu'));
}

function viewData(id) {
    if (xajax.call('validAction', {parameters: [1, 2], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormMenu').html("Ver Menu");
    $('#sideBarFormMenu').sidebar('show');
//    $('#btn_save').addClass("hidden");
$('#btn_save').css({visibility:"hidden"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('.delete.icon').addClass("hide");
    $('#divDropDownModulo').dropdown('destroy');
    $('#divDropDownAccion').dropdown('restore defaults');
    $('#divDropDownAccion').dropdown('destroy');
    disabledFields(true);
}

function editData(id) {
    if (xajax.call('validAction', {parameters: [1, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormMenu').html("Editar Menu");
    $('#sideBarFormMenu').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    $('#divDropDownModulo').dropdown();
    $('#divDropDownAccion').dropdown();
    disabledFields(false);
    $('#txt_descripcion').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [1, 4], mode: 'synchronous'}) == 0) {
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

function insertActionTable(text, value) {
    var html = "";

    var hdnIdAction = $("#hdn_idaccion").val();
    var hdnTextAction = $("#hdn_textaccion").val();

    (hdnIdAction == "") ? hdnIdAction = value : hdnIdAction += "," + value;
    (hdnTextAction == "") ? hdnTextAction = text : hdnTextAction += "," + text;

    $("#divOptionDropDownAccion .item[data-value=" + value + "]").addClass("hidden");
    $('#divDropDownAccion').dropdown('restore defaults');

    $("#hdn_idaccion").val(hdnIdAction);
    $("#hdn_textaccion").val(hdnTextAction);

    var arrIdAction = hdnIdAction.split(",");
    var arrTextAction = hdnTextAction.split(",");

    for (i = 0; i < arrIdAction.length; i++) {
        html += "<tr><td><div class='ui label'>" + arrTextAction[i] + "<i class='delete icon' onclick=\"deleteActionTable('" + arrTextAction[i] + "','" + arrIdAction[i] + "');\"></i></div></td>";
        i++;
        if (arrIdAction[i] != undefined) {
            html += "<td><div class='ui label'>" + arrTextAction[i] + "<i class='delete icon' onclick=\"deleteActionTable('" + arrTextAction[i] + "','" + arrIdAction[i] + "');\"></i></div></td></tr>";
        } else {
            html += "<td>&nbsp;</td></tr>";
        }
    }
    $("#tblAction").html(html);
}

function deleteActionTable(text, value) {
    var html = "";
    var arrIdAction = "";
    var arrTextAction = "";

    $("#divOptionDropDownAccion .item[data-value=" + value + "]").removeClass("hidden");
    $('#divDropDownAccion').dropdown('restore defaults');

    $("#hdn_idaccion").val($("#hdn_idaccion").val().replace(new RegExp("," + value + "|" + value + ",|" + value, 'g'), ''));
    $("#hdn_textaccion").val($("#hdn_textaccion").val().replace(new RegExp("," + text + "|" + text + ",|" + text, 'g'), ''));

    if ($("#hdn_idaccion").val() != "") {
        arrIdAction = $("#hdn_idaccion").val().split(",");
        arrTextAction = $("#hdn_textaccion").val().split(",");
    }

    for (i = 0; i < arrIdAction.length; i++) {
        html += "<tr><td><div class='ui label'>" + arrTextAction[i] + "<i class='delete icon' onclick=\"deleteActionTable('" + arrTextAction[i] + "','" + arrIdAction[i] + "');\"></i></div></td>";
        i++;
        if (arrIdAction[i] != undefined) {
            html += "<td><div class='ui label'>" + arrTextAction[i] + "<i class='delete icon' onclick=\"deleteActionTable('" + arrTextAction[i] + "','" + arrIdAction[i] + "');\"></i></div></td></tr>";
        } else {
            html += "<td>&nbsp;</td></tr>";
        }
    }
    $("#tblAction").html(html);
}

