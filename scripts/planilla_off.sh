mysql -uadmin -pmysql -h192.168.0.96 -P3307  -e "DELETE from bd_saber_trabajo.tbl_perfil_menu_accion where id_perfil=2 and id_menu_accion in (25,26,27,65,66,67);"
