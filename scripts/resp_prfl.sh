
###################################
## Este script corre en los PRFL ##
###################################


RUTA=/var/www/censo/backup

# Se debe crear un archivo en $RUTA llamado ultbckp con el contenido: 2014-12-01 00:00:00
# esto es, una fecha y hora previa al censo donde no se ha ingresado ningun registro todavia.
# Funciona como la fecha del ultimo backup realizado pero como no ha ocurrido, se coloca
# una fecha suficientemente anterior.

FCH_ACT=`date +%Y_%m_%d'_'%H_%M_%S`


# tbl_personas

#mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas" -B | sed 's/\t/,/g' > $RUTA/tbl_personas.csv  FIELDS TERMINATED \ 
#BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"


# tbl_personas

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas INTO OUTFILE '/tmp/tbl_personas.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_carreras_formarse

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_carreras_formarse INTO OUTFILE '/tmp/tbl_personas_carreras_formarse.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_misiones

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_misiones INTO OUTFILE '/tmp/tbl_personas_misiones.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'" 

# tbl_personas_oficios

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_oficios INTO OUTFILE '/tmp/tbl_personas_oficios.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_oficios_formarse

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_oficios_formarse INTO OUTFILE '/tmp/tbl_personas_oficios_formarse.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_organizaciones_sociales

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_organizaciones_sociales INTO OUTFILE '/tmp/tbl_personas_organizaciones_sociales.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_socioeconomicos

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_socioeconomicos INTO OUTFILE '/tmp/tbl_personas_socioeconomicos.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"

# tbl_personas_ubicaciones

mysql -u root -proot --disable-column-names -e "SELECT * FROM bd_saber_trabajo.tbl_personas_ubicaciones INTO OUTFILE '/tmp/tbl_personas_ubicaciones.csv' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n'"



tar -zcf $RUTA/bd_saber_trabajo_$FCH_ACT'_'$1_$2.tar.gz /tmp/tbl_personas*

#rm -r $RUTA/tbl_personas*
rm -r /tmp/tbl_personas*

