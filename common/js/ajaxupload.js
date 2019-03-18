function $m(theVar) {
    return document.getElementById(theVar);
}
function remove(theVar) {
    var theParent = theVar.parentNode;
    theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn) {
    if (obj.addEventListener) {
        obj.addEventListener(evType, fn, true);
    }
    if (obj.attachEvent) {
        obj.attachEvent("on" + evType, fn);
    }
}
function removeEvent(obj, type, fn) {
    if (obj.detachEvent) {
        obj.detachEvent('on' + type, fn);
    } else {
        obj.removeEventListener(type, fn, false);
    }
}
function isWebKit() {
    return RegExp(" AppleWebKit/").test(navigator.userAgent);
}
function ajaxUpload(form, id_element, multiple) {
    var detectWebKit = isWebKit();
    form = typeof (form) == "string" ? $m(form) : form;
    var erro = "";
    if (form == null || typeof (form) == "undefined") {
        erro += "The form of 1st parameter does not exists.\n";
    } else if (form.nodeName.toLowerCase() != "form") {
        erro += "The form of 1st parameter its not a form.\n";
    }
    if ($m(id_element) == null) {
        erro += "The element of 3rd parameter does not exists.\n";
    }
    if (erro.length > 0) {
        alert("Error in call ajaxUpload:\n" + erro);
        return;
    }
    var iframe = document.createElement("iframe");
    iframe.setAttribute("id", "ajax-temp");
    iframe.setAttribute("name", "ajax-temp");
    iframe.setAttribute("width", "0");
    iframe.setAttribute("height", "0");
    iframe.setAttribute("border", "0");
    iframe.setAttribute("style", "width: 0; height: 0; border: none;");
    form.parentNode.appendChild(iframe);
    window.frames['ajax-temp'].name = "ajax-temp";
    var doUpload = function () {
        removeEvent($m('ajax-temp'), "load", doUpload);
        var cross = "javascript: ";
        cross += "window.parent.$m('" + id_element + "').innerHTML = document.body.innerHTML; void(0);";
        $m(id_element).innerHTML = "<div class='ui red message'>Error cargando la imagen</div>";
        $m('ajax-temp').src = cross;
        if (detectWebKit) {
            remove($m('ajax-temp'));
        } else {
            setTimeout(function () {
                remove($m('ajax-temp'))
            }, 250);
        }
    }
    addEvent($m('ajax-temp'), "load", doUpload);
    form.setAttribute("target", "ajax-temp");
    if(multiple == 1){
        form.setAttribute("action", "../common/php/ajaxupload.php");
    }else if(multiple == 2){
        form.setAttribute("action", "../common/php/ajaxuploadmultiple.php");
    }
    form.setAttribute("method", "post");
    form.setAttribute("enctype", "multipart/form-data");
    form.setAttribute("encoding", "multipart/form-data");
    $m(id_element).innerHTML = "<div id='divLoader2' class='ui active dimmer'><div class='ui large text loader'>Cargando...</div></div>";
    form.submit();
}