$(document).ready(function() {
    
    //xajax.call('findValuesDropDownProyecto', {parameters: [], mode: 'synchronous'});
    
    xajax_findValuesDropDownProyecto();
    
      $(function() {
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $('#txt_fecha').datepicker({
                                'setDate': '25/02/2013'
                                , altField: '#fecha_texto'
                                , altFormat: "dd/mm/yyyy"
                                , closeText: 'Cerrar'
                             , prevText: '<Ant'
                             , nextText: 'Sig>'
                             , currentText: 'Hoy'
                             , monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                             , monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic']
                             , dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                             , dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb']
                             , dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá']
                             , weekHeader: 'Sm'
                             , firstDay: 1
                             ,   isRTL: false
                             , showMonthAfterYear: false,
                              yearSuffix: ''
                            });
          });

            $('#tblSolicitud').dataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "url": "../config/es_ES.txt"
                },
                "ajax": "../controller/php/ac_listsolicitud.php"
            });
            
    $('#tblSolicitud').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [19, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormSolicitud').html("Agregar Solicitud");
        $('#sideBarFormSolicitud').sidebar('show');
//        $('#btn_save').removeClass("hidden");
        $('#btn_save').css({visibility:"visible"});
        disabledFields(false);
        $('#txt_proyecto').focus();
    });

    $('#btn_cancel').click(function() {
        $("#divForm").find(".field.error").find(".prompt").remove();
        $("#divForm").find(".field.error").removeClass("error");
        $('#sideBarFormSolicitud').sidebar('hide');
    });

    $('#tabsSolicitud .item').tab({history: false});

    var settings = {
        on: 'submit',
        inline: 'true',       
        onSuccess: function() {
            save();
        },    
        onFailure: function() {
            alert("Faltan campos por rellenar");
            $('html,body').animate({
                scrollTop: $("#frmSolicitud_Planilla").offset().top
            }, 1);
        }
       
    };

    var rules = {
        txt_proyecto: {
            identifier: 'txt_proyecto',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese el Proyecto'
                }]
        },
        txt_fecha: {
            identifier: 'txt_fecha',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese la Fecha'
                }]
        },
        hdn_number: {
            identifier: 'hdn_number',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor agregue Insumo'
                }]
        }
    };

    $('#divForm').form(rules, settings);

    $('#btn_addProyecto').click(function () {
        insertTr();
    });

   

    $('#btn_cancel').click(function () {
        $('#divloader').html("");
    });

    
    view($('#hdn_view').val());
   
});

function cleanForm() {
    $("#txt_proyecto").val("");
    $("#txt_fecha").val("");
    $("#txt_observacion").val("");
    $("#lbl_usuarioCreador").html("&nbsp;");
    $("#lbl_fechaCreador").html("&nbsp;");
    $("#lbl_usuarioModificacion").html("&nbsp;");
    $("#lbl_fechaModificacion").html("&nbsp;");
    $("#lbl_usuarioEliminacion").html("&nbsp;");
    $("#lbl_fechaEliminacion").html("&nbsp;");
}

function view(view) {
    if (view == 1) {        
        $('#btn_save').css({visibility:"hidden"});
        $('#btn_aprobar').css({visibility:"hidden"});
    }
    if ($('#hdn_id').val() != "") {
        xajax.call('view', {parameters: [$('#hdn_id').val()], mode: 'synchronous'});
       // xajax.call('view', {parameters: [view], mode: 'synchronous'});
        }
}
function editData(id) {
    if (xajax.call('validAction', {parameters: [19, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormSolicitud').html("Editar Solicitud");
    $('#sideBarFormSolicitud').sidebar('show');
    $('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_proyecto').focus();
}
//function aproveData(id) {
//    if (xajax.call('validAction', {parameters: [19, 5], mode: 'synchronous'}) == 0) {
//        showMessage(7);
//        return;
//    }
//    cleanForm();
//    $('#divLoader').removeClass("disabled").addClass("active");
//    $('#labelFormSolicitud').html("Aprovar Solicitud");
//    $('#sideBarFormSolicitud').sidebar('show');
////    $('#btn_save').removeClass("hidden");
//    $('#btn_save').css({visibility:"hidden"});
//    xajax.call('view', {parameters: [id], mode: 'synchronous'});
//    disabledFields(false);
//    $('#txt_proyecto').focus();
//}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [19, 4], mode: 'synchronous'}) == 0) {
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

function save() {
    xajax_save(xajax.getFormValues('frmSolicitud_planilla'));
}

//                                            AGREGARRRRR !!!!! 


function insertTr() {
    var num = $('#hdn_number').val();  
    num++;
    //var tdInsumo = "<td><select class='validationCustom' id='txt_insumo_" + num + "' name='txt_insumo[]' onchange='cargarInsumo(" + num + ")'></select></td>";
    var tdInsumo = "<td><select class='validationCustom' id='txt_insumo_" + num + "' name='txt_insumo[]'  onchange='cargarSolicitud(" + num + ")'></select></td>";
    var tdCantidad = "<td><div class='ui mini input'><input class='validationCustom' type='text' id='txt_cantidad_solicitada_" + num + "' name='txt_cantidad_solicitada[]'></div></td>";
    var tdButton = "<td><div id='btn_eliminar_" + num + "' class='mini ui red icon button' onclick=\"$('#tr_" + num + "').remove();\"><i class='trash icon'></i></div></td>";
    //var tdFiltro = "<td><select class='validationCustom' id='txt_unidad_despacho_" + num + "' name='txt_unidad_despacho[]'  onchange='cargarUnidad(" + num + ")'></select></td>";

    var tr = "<tr id='tr_" + num + "'>" + tdInsumo + tdCantidad +  tdButton +"</tr>";

    $('#tbodyProyectos').append(tr);
    
        soloNumeros("txt_cantidad_solicitada_" + num);
        
    xajax.call('findValuesDropDownInsumo', {parameters: ["txt_insumo_" + num], mode: 'synchronous'});
    xajax.call('findValuesDropDownUnidad_despacho', {parameters: ["txt_unidad_despacho_" + num], mode: 'synchronous'});


    $('#hdn_number').val(num);
}

function addSolicitud() {
    var dataNew = '';
    if (customValidationsModal()) {
        $('#tbodyRubros > tr').each(function () {
            var num = $(this).attr("id").split("_");

            if ($('#txt_rubro_' + num[2]).val() != "") {
                var rubro = $('#txt_rubro_' + num[2]).val();
                var cant = $('#txt_cantidad_' + num[2]).val().replace(",", "");
                var unidadCant = $('#txt_unidad_medida_cantidad_' + num[2]).val();
                var capacidad = $('#txt_capacidad_' + num[2]).val().replace(",", "");
                var unidadCap = $('#txt_unidad_medida_capacidad_' + num[2]).val();

                (dataNew == "") ? dataNew = rubro + "," + cant + "," + unidadCant + "," + capacidad + "," + unidadCap : dataNew = dataNew + "@" + rubro + "," + cant + "," + unidadCant + "," + capacidad + "," + unidadCap;
            }
        });
        $('#' + $('#hdn_element').val()).val(dataNew);
        $('#divModalProductos').modal('hide');
    }
}

//function cargarInsumo(num) {  
//    xajax.call('findValuesDropDownInsumo', {parameters: ["txt_insumo_" + num], mode: 'synchronous'});
//}