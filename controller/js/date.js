
$(function () {
$("#fecha").datepicker();
});

 jQuery.datepicker.regional['eu'] = {
 closeText: 'Egina',
 prevText: '<Aur',
 nextText: 'Hur>',
 currentText: 'Gaur',
 monthNames: ['Urtarrila','Otsaila','Martxoa','Apirila','Maiatza','Ekaina',
 'Uztaila','Abuztua','Iraila','Urria','Azaroa','Abendua'],
 monthNamesShort: ['Urt','Ots','Mar','Api','Mai','Eka',
 'Uzt','Abu','Ira','Urr','Aza','Abe'],
 dayNames: ['Igandea','Astelehena','Asteartea','Asteazkena','Osteguna','Ostirala','Larunbata'],
 dayNamesShort: ['Iga','Ast','Ast','Ast','Ost','Ost','Lar'],
 dayNamesMin: ['Ig','As','As','As','Os','Os','La'],
 dateFormat: 'yy/mm/dd', firstDay: 1,
 isRTL: false};
 $.datepicker.setDefaults($.datepicker.regional['eu']);
$(function () {
$("#fecha").datepicker();
});