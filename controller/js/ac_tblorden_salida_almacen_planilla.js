$(document).ready(function() { 
    
    xajax.call('findValuesDropDownProyecto', {parameters: [], mode: 'synchronous'});    
    xajax.call('findValuesDropDownSolicitud', {parameters: [0], mode: 'synchronous'});    
    xajax.call('findValuesDropDownAlmacen', {parameters: [], mode: 'synchronous'});
    xajax.call('findValuesDropDownConductor', {parameters: [], mode: 'synchronous'});
    xajax.call('findValuesDropDownVehiculo', {parameters: [], mode: 'synchronous'});
    

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

            $('#tblorden_salida_almacen').dataTable({
                "processing": true,
                "serverSide": true,
                "language": {
                    "url": "../config/es_ES.txt"
                },
                "ajax": "../controller/php/ac_listorden_salida_almacen.php"
            });
            
            
            
    $("#txt_proyecto").change(function () {          
        xajax_findValuesDropDownSolicitud($("#txt_proyecto").val());
    });  
    
    $("#txt_solicitud").change(function () {          
        xajax_cargarSolicitud($("#txt_solicitud").val());
    });        
           
    $('#tblorden_salida_almacen').removeClass('display').addClass('table table-striped table-bordered');

    $('#btn_add').click(function() {
        if (xajax.call('validAction', {parameters: [23, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        cleanForm();
        $('#labelFormOrden_salida_almacen').html("Agregar Inventario");
        $('#sideBarFormOrden_salida_almacen').sidebar('show');
//        $('#btn_save').removeClass("hidden");
        $('#btn_save').css({visibility:"visible"});
        disabledFields(false);
    });

    $('#btn_cancel').click(function() {
        $("#divForm").find(".field.error").find(".prompt").remove();
        $("#divForm").find(".field.error").removeClass("error");
        $('#sideBarFormOrden_salida_almacen').sidebar('hide');
    });

    $('#tabsOrden_salida_almacen .item').tab({history: false});

    var settings = {
        on: 'submit',
        inline: 'true',
        onSuccess: function() {
            save();
        }
    };

    var rules = {
        txt_solicitud: {
            identifier: 'txt_solicitud',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor ingrese Una Solicitud'
                }]
        },
        txt_direccion: {
            identifier: 'txt_direccion',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese una Direccion'
                }]
        },
        txt_fecha: {
            identifier: 'txt_fecha',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese Una Fecha'
                }]
        },
        txt_Conductor: {
            identifier: 'txt_conductor',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese un Conductor'
                }]
        },
        txt_Vehiculo: {
            identifier: 'txt_vehiculo',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese un Vehiculo'
                }]
        },
        txt_Almacen: {
            identifier: 'txt_almacen',
            rules: [{
                    type: 'empty',
                    prompt: 'Por favor Ingrese un Almacen'
                }]
        }
    };

    $('#divForm').form(rules, settings);
    
    $('#btn_cancel').click(function () {
        $('#divloader').html("");
    });

    $('#btn_save').click(function () {
        guardarModal();
    });
    
    view($('#hdn_view').val());
});

function cleanForm() {
    $("#txt_almacen").val("");
    $("#txt_fecha").val("");
    $("#txt_observacion").val("");
    $("#txt_estatus_solicitud").val("");
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
    }
    if ($('#hdn_id').val() != "") {
        xajax.call('view', {parameters: [$('#hdn_id').val()], mode: 'synchronous'});
        $("input").attr('disabled','disabled');
        $("textarea").attr('disabled','disabled');
                document.getElementById("txt_almacen").disabled=true;
        document.getElementById("txt_solicitud").disabled=true;
        document.getElementById("txt_proyecto").disabled=true;
        document.getElementById("txt_vehiculo").disabled=true;
        document.getElementById("txt_conductor").disabled=true;
        }
}



function editData(id) {
    if (xajax.call('validAction', {parameters: [23, 3], mode: 'synchronous'}) == 0) {
        showMessage(7);
        return;
    }
    cleanForm();
    $('#divLoader').removeClass("disabled").addClass("active");
    $('#labelFormOrden_salida_almacen').html("Editar Solicitud");
    $('#sideBarFormOrden_salida_almacen').sidebar('show');
//    $('#btn_save').removeClass("hidden");
$('#btn_save').css({visibility:"visible"});
    xajax.call('view', {parameters: [id], mode: 'synchronous'});
    disabledFields(false);
    $('#txt_proyecto').focus();
}

function deleteData(id) {
    if (xajax.call('validAction', {parameters: [23, 4], mode: 'synchronous'}) == 0) {
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
    xajax_save(xajax.getFormValues('frmOrden_salida_almacen_planilla'));
}

function insertTr() {
    var num = $('#hdn_number').val();
    //var tdInsumo = "<td><select class='validationCustom' id='txt_insumo_" + num + "' name='txt_insumo[]'  onchange='cargarSolicitud(" + num + ")'></select></td>";
    //var tdInsumo = "<td><select class='validationCustom' id='txt_insumo_" + num + "' name='txt_insumo[]' onchange='cargarInsumo(" + num + ")'></select></td>";
    var tdInsumo = "<td><div class='ui mini input'><input class='validationCustom' id='txt_insumo_" + num + "' name='txt_insumo[]'  value='' readonly='readonly'></div></td>";
    var tdCantidadSolicitada = "<td><div class='ui mini input'><input class='validationCustom' type='text' id='txt_cantidad_solicitada_" + num + "' name='txt_cantidad_solicitada[]' readonly='readonly'></div></td>";
    var tdCantidadDespachar = "<td><div class='ui mini input'><input class='validationCustom' type='text' id='txt_cantidad_despachada_" + num + "' name='txt_cantidad_despachada[]'></div></td>";
    
    var tr = "<tr id='tr_" + num + "'>" + tdInsumo + tdCantidadSolicitada+tdCantidadDespachar+"</tr>";

    $('#tbodyProyectos').append(tr);
    
        soloNumeros("txt_cantidad_despachada_" + num);
        
    xajax.call('findValuesDropDownInsumo', {parameters: ["txt_insumo_" + num], mode: 'synchronous'});
   
    num++;
    
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