<?php
session_start();
if (is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblproyecto_edit.php');
require ('../model/md_tbltipo_proyecto.php');
require ('../model/md_utility.php');
require ('../model/md_tblcementera.php');
require ('../model/md_tblconstrupatria.php');
require ('../model/md_tblcementera_acopio.php');
require ('../model/md_saime.php');
require ('../model/md_tblestatus_proyecto.php');
require ('../model/md_tblsector_economico.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblproyecto.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Proyecto</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../common/css/select2.css">
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <form id="frmProyecto_planilla" name="frmProyecto_planilla" action="#" method="post">
            <input type="hidden" id="hdn_id" name="hdn_id" value="<?php echo $_REQUEST['proyecto'] ?>" />
            <input type="hidden" id="hdn_view" name="hdn_view" value="<?php echo $_REQUEST['view'] ?>" />
            <div class="ui segment">
                <div id="divForm" class="ui form">
                    <div class="ui teal raised secondary fluid segment">
                        <h2 class="ui header">Datos Básicos</h2>
                        <div class="two fields">
                            <div class="field">
                                <label>Nombre de Proyecto</label>
                                <div class="ui mini icon input">
                                    <textarea id="txt_nombre" name="txt_nombre" placeholder="Ingrese el nombre de proyecto" type="text"  style="height: 10px"></textarea>
                                <i class="icon asterisk"></i>   
                                </div>
                                
                            </div>
                            <div class="field">
                                <label>Descripcion</label>
                                <div class="ui mini icon input">
                                    <textarea id="txt_descripcion" name="txt_descripcion" placeholder="Ingrese la descripcion" style="height: 10px"></textarea>
                                 <i class="icon asterisk"></i>
                                </div>
                            </div>
                        </div>
                        <div class="three fields">
                            <div class="field">
                                <label>Tipo de Proyecto</label>
                                <div id="divDropDownTipoproyecto" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_tipo_proyecto" name="txt_tipo_proyecto"/>
                                    <div class="default text">Seleccione un Tipo de proyecto</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownTipoproyecto" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                                <label>Sector Economico</label>
                                <div id="divDropDownSectoreconomico" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_sector_economico" name="txt_sector_economico"/>
                                    <div class="default text">Seleccione un Sector Economico</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownSectoreconomico" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                                <label>Estatus de Proyecto</label>
                                <div id="divDropDownEstatusproyecto" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_estatus_proyecto" name="txt_estatus_proyecto"/>
                                    <div class="default text">Estatus de Proyecto</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownEstatusproyecto" class="menu"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="divFormDatosUbicacion" class="ui green raised secondary fluid segment">
                        <h2 class="ui header">Datos de Ubicaci&oacute;n</h2>
                        <div class="three fields">
                            <div class="field">
                                <label>Estado</label>
                                <div id="divDropDownEstado" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_estado" name="txt_estado"/>
                                    <div class="default text">Seleccione un estado</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownEstado" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                                <label>Municipio</label>
                                <div id="divDropDownMunicipio" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_municipio" name="txt_municipio"/>
                                    <div class="default text">Seleccione un municipio</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownMunicipio" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                                <label>Parroquia</label>
                                <div id="divDropDownParroquia" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_parroquia" name="txt_parroquia"/>
                                    <div class="default text">Seleccione una parroquia</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownParroquia" class="menu"></div>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Direcci&oacute;n</label>
                            <div class="ui mini icon input">
                                <textarea id="txt_direccion" name="txt_direccion" placeholder="Ingrese la direcci&oacute;n" style="height: 10px" ></textarea>
                            <i class="icon asterisk"></i>
                            </div>
                        </div>
                    </div>
                    
                   <!-------------------        Gerente Tecnico        ---------------------------------------------------------------->
                    
                    <div id="divFormDatostecnico" class="ui Blue raised secondary fluid segment">
                        <h2 class="ui header">Datos Gerente Técnico</h2>
                        <div class="three fields">
                            
                            <div class="field">
                                <label>Nacionalidad</label>
                                <div id="divDropDownNacionalidad_tecnico" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_nacionalidad_tecnico" name="txt_nacionalidad_tecnico"/>
                                    <div class="default text">Seleccione una nacionalidad</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownNacionalidad_tecnico" class="menu">
                                        <div class='item active' data-value=''>Seleccione una nacionalidad</div>
                                        <div class='item' data-value='V'>Venezolano(a)</div>
                                        <div class='item' data-value='E'>Extranjero(a)</div>
                                    </div>
                                </div>                           
                               
                            </div>
                            <div class="field">
                            <label>Cedula</label>
                            <div class="ui mini icon input">
                                <input id="txt_cedula_tecnico" name="txt_cedula_tecnico" placeholder="Ingrese la c&eacute;dula de identidad" type="text" maxlength="10">
                                
                            </div>
                        </div>
                            <div class="field">
                            <label>Nombre</label>
                            <div class="ui mini icon input">
                                <input id="txt_nombre_tecnico" name="txt_nombre_tecnico" placeholder="Ingrese el nombre" type="text" maxlength="10">
                          
                            </div>
                        </div>
                           </div>
                        
                        <div class="three fields">
                            <div class="field">
                            <label>Apellido</label>
                            <div class="ui mini icon input">
                                <input id="txt_apellido_tecnico" name="txt_apellido_tecnico" placeholder="Ingrese el apellido" type="text" maxlength="10">
                             
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Corporativo</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_corptecnico" name="txt_telefono_corptecnico" placeholder="Ingrese el Numero" type="text" maxlength="10">
                          
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Personal</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_pertecnico" name="txt_telefono_pertecnico" placeholder="Ingrese el Numero" type="text" maxlength="10">
                               
                            </div>
                        </div>
                           </div>
                         </div>
                   
                     <!-------------------        Gerente Tecnico  END      ---------------------------------------------------------------->
                     
                      <!-------------------        Inspector        ---------------------------------------------------------------->
                     
                    <div id="divFormDatosinspector" class="ui Green purple secondary fluid segment">
                        <h2 class="ui header">Datos Inspector</h2>
                        <div class="three fields">
                        <div class="field">
                                <label>Nacionalidad</label>
                                <div id="divDropDownNacionalidad_inspector" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_nacionalidad_inspector" name="txt_nacionalidad_inspector"/>
                                    <div class="default text">Seleccione una nacionalidad</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownNacionalidad_inspector" class="menu">
                                        <div class='item active' data-value=''>Seleccione una nacionalidad</div>
                                        <div class='item' data-value='V'>Venezolano(a)</div>
                                        <div class='item' data-value='E'>Extranjero(a)</div>
                                    </div>
                                </div>                           
                               
                            </div>
                            <div class="field">
                            <label>Cedula</label>
                            <div class="ui mini icon input">
                                <input id="txt_cedula_inspector" name="txt_cedula_inspector" placeholder="Ingrese la c&eacute;dula de identidad" type="text" maxlength="10">
                                
                            </div>
                        </div>
                            <div class="field">
                            <label>Nombre</label>
                            <div class="ui mini icon input">
                                <input id="txt_nombre_inspector" name="txt_nombre_inspector" placeholder="Ingrese el nombre" type="text" maxlength="10">
                                    
                            </div>
                        </div>
                           </div>
                        
                        <div class="three fields">
                            <div class="field">
                            <label>Apellido</label>
                            <div class="ui mini icon input">
                                <input id="txt_apellido_inspector" name="txt_apellido_inspector" placeholder="Ingrese el nombre" type="text" maxlength="10">
                             
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Corporativo</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_corpinspector" name="txt_telefono_corpinspector" placeholder="Ingrese el numero" type="text" maxlength="10">
                               
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Personal</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_perinspector" name="txt_telefono_perinspector" placeholder="Ingrese el numero" type="text" maxlength="10">
                            
                            </div>
                        </div>
                           </div>
                         </div>
                    <!--
                     -----------------        Inspector  END      --------------------------------------------------------------
                    
                     -----------------        ingeniero residente       --------------------------------------------------------------
                  
-->                      <div id="divFormDatosresidente" class="ui Green secondary fluid segment">
                        <h2 class="ui header">Datos Ingeniero residente</h2>
                        <div class="three fields">
                        <div class="field">
                                <label>Nacionalidad</label>
                                <div id="divDropDownNacionalidad_residente" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_nacionalidad_residente" name="txt_nacionalidad_residente"/>
                                    <div class="default text">Seleccione una nacionalidad</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownNacionalidad_residente" class="menu">
                                        <div class='item active' data-value=''>Seleccione una nacionalidad</div>
                                        <div class='item' data-value='V'>Venezolano(a)</div>
                                        <div class='item' data-value='E'>Extranjero(a)</div>
                                    </div>
                                </div>                           
                               
                            </div>
                            <div class="field">
                            <label>Cedula</label>
                            <div class="ui mini icon input">
                                <input id="txt_cedula_residente" name="txt_cedula_residente" placeholder="Ingrese la c&eacute;dula de identidad" type="text" maxlength="10">
                             
                            </div>
                        </div>
                            <div class="field">
                            <label>Nombre</label>
                            <div class="ui mini icon input">
                                <input id="txt_nombre_residente" name="txt_nombre_residente" placeholder="Ingrese el nombre" type="text" maxlength="10">
                              
                            </div>
                        </div>
                           </div>
                        
                        <div class="three fields">
                            <div class="field">
                            <label>Apellido</label>
                            <div class="ui mini icon input">
                                <input id="txt_apellido_residente" name="txt_apellido_residente" placeholder="Ingrese el nombre" type="text" maxlength="10">
                           
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Corporativo</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_corpresidente" name="txt_telefono_corpresidente" placeholder="Ingrese el numero" type="text" maxlength="10">
                               
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Personal</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_perresidente" name="txt_telefono_perresidente" placeholder="Ingrese el numero" type="text" maxlength="10">
                               
                            </div>
                        </div>
                           </div>
                         </div><!--
                    -----------------        ingeniero residente END      --------------------------------------------------------------
                    
                    -----------------        Otro Contacto      --------------------------------------------------------------
-->                    
                    <div id="divFormDatoscontacto" class="ui teal raised secondary fluid segment">
                        <h2 class="ui header">Otro Contacto</h2>
                        <div class="three fields">
                            <div class="field">
                                <label>Nacionalidad</label>
                                <div id="divDropDownNacionalidad_contacto" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_nacionalidad_contacto" name="txt_nacionalidad_contacto"/>
                                    <div class="default text">Seleccione una nacionalidad</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownNacionalidad_contacto" class="menu">
                                        <div class='item active' data-value=''>Seleccione una nacionalidad</div>
                                        <div class='item' data-value='V'>Venezolano(a)</div>
                                        <div class='item' data-value='E'>Extranjero(a)</div>
                                    </div>
                                </div>                           
                               
                            </div>
                            <div class="field">
                            <label>Cedula</label>
                            <div class="ui mini icon input">
                                <input id="txt_cedula_contacto" name="txt_cedula_contacto" placeholder="Ingrese la c&eacute;dula de identidad" type="text" maxlength="10">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                            <div class="field">
                            <label>Nombre</label>
                            <div class="ui mini icon input">
                                <input id="txt_nombre_contacto" name="txt_nombre_contacto" placeholder="Ingrese el nombre" type="text" maxlength="10">
                         
                            </div>
                        </div>
                           </div>
                        <div class="three fields">
                            <div class="field">
                            <label>Apellido</label>
                            <div class="ui mini icon input">
                                <input id="txt_apellido_contacto" name="txt_apellido_contacto" placeholder="Ingrese el nombre" type="text" maxlength="10">
                            
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Corporativo</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_corpcontacto" name="txt_telefono_corpcontacto" placeholder="Ingrese el numero" type="text" maxlength="10">
                     
                            </div>
                        </div>
                            <div class="field">
                            <label>Teléfono Personal</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono_percontacto" name="txt_telefono_percontacto" placeholder="Ingrese el numero" type="text" maxlength="10">
                               
                            </div>
                        </div>
                           </div>
                         </div><!--
                    
                     -----------------        Otro Contacto   END   --------------------------------------------------------------
                                                             
                               
                               
                               
                    -----------------        Proveedores      ---------------------------------------------------------------->        
                         <div id="divFormCementera" class="ui blue raised secondary fluid segment">
                        <h2 class="ui header">Cementera</h2>
                        <div class="three fields">
                            <div class="field">
                                <label>Cementera</label>
                                <div id="divDropDownCementera" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_cementera" name="txt_cementera"/>
                                    <div class="default text">Seleccione una Cementera</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownCementera" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                                <label>Cementera Acopio</label>
                                <div id="divDropDownCementera_acopio" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_cementera_acopio" name="txt_cementera_acopio"/>
                                    <div class="default text">Seleccione una Cementera</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownCementera_acopio" class="menu"></div>
                                </div>
                            </div>
                            <div class="field">
                            <label>Codigo Cementera</label>
                            <div class="ui mini icon input">
                                <input id="txt_codigo_cemento" name="txt_codigo_cemento" placeholder="Ingrese el Codigo" type="text" maxlength="10">
                        
                            </div>
                        </div>
                        </div>
                     </div>
                    <div id="divFormconstrupatria" class="ui red raised secondary fluid segment">
                        <h2 class="ui header">Construpatria</h2>
                        <div class="two fields">
                            
                             <div class="field">
                                <label>Construpatria</label>
                                <div id="divDropDownConstrupatria" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_construpatria" name="txt_construpatria"/>
                                    <div class="default text">Seleccione una Cementera</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownConstrupatria" class="menu"></div>
                                </div>
                            </div>
                            
                            <div class="field">
                            <label>Codigo Construpatria</label>
                            <div class="ui mini icon input">
                                <input id="txt_codigo_construpatria" name="txt_codigo_construpatria" placeholder="Ingrese el Codigo" type="text" maxlength="10">
             
                            </div>
                        </div>
                         </div>
                     </div>
                    
                    
                    
                    
                    <!-------------------        Proveedores End      ---------------------------------------------------------------->
                    
                    <br/>
                    <div style="float: right">
                        <div id="btn_cancel" class="mini ui red icon button">
                            <i class="delete icon"></i>
                            Cancelar
                        </div>
                        <div id="btn_save" class="mini ui green icon submit button">
                            <i class="save icon "></i>
                            Guardar
                        </div>
                    </div>
                </div>
      
    </div>
        <script type="text/javascript" language="javascript" src="../common/js/jquery.maskedinput.min.js"></script>
      <script type="text/javascript" language="javascript" src="../common/js/ajaxupload.js"></script>
    <script type="text/javascript" language="javascript" src="../controller/js/ac_tblproyecto_planilla.js"></script>
   </form>
</body>
</html>
