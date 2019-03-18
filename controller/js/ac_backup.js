$(document).ready(function() {

    xajax_listFiles();

    $('#btn_backup').click(function() {
        if (xajax.call('validAction', {parameters: [15, 1], mode: 'synchronous'}) == 0) {
            showMessage(7);
            return;
        }
        if (confirm('Esta opcion genera un respaldo de la base de datos. Desea continuar?')) {
            xajax.call('backup', {parameters: [], mode: 'synchronous'});
        }
    });
});