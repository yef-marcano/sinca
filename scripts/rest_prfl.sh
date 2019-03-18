################################################
## Este script corre en el servidor principal ##
################################################

PATH_BCKP='/app/censo/backup'
FCH_ACT=`date +%Y-%m-%d' '%H:%M:%S`
NUEVOBCKP=`ls -1 $PATH_BCKP/nuevos/ | wc -l`
ARCHIVO='tbl_personas.csv'

if [ ! -d $PATH_BCKP/nuevos ]; then
    
    mkdir $PATH_BCKP/nuevos
    
fi

if [ ! -d $PATH_BCKP/procesados ]; then
    
    mkdir $PATH_BCKP/procesados
    
fi

if [ $NUEVOBCKP -gt  0 ]; then

    # Si se encuentran nuevos backups
    for n in $(ls -1 $PATH_BCKP/nuevos/ | grep tar.gz ); do
        
        ### Hacer para cada archivo de backup:
        
        # Descomprimir en carpeta temporal
        
        if [ ! -d $PATH_BCKP/nuevos/Desc ]; then
            
            mkdir $PATH_BCKP/nuevos/Desc
            
        fi
        
        tar xzf $PATH_BCKP/nuevos/$n -C $PATH_BCKP/nuevos/Desc/
        
        # Restaurar Personas
        
        NLN=`cat $PATH_BCKP/nuevos/Desc/tmp/$ARCHIVO | wc -l` # Se cuentan los registros en el archivo
        echo "el archivo de personas tiene $NLN registros"
        i=1
        
        while [ $i -le $NLN ]; do
        
            cedid=`head -$i $PATH_BCKP/nuevos/Desc/tmp/$ARCHIVO | tail -1 | cut -d, -f3` # se ubica la cedula
echo "cedula primer registro $cedid"
            enc=`mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -B --disable-column-names -e "SELECT id_persona FROM bd_saber_trabajo.tbl_personas WHERE cedula = '$cedid'"` # se busca esa ci en la bd principal
echo "encontrado es igual a "$enc            
            # si no se encuentra la cedula en el sistema, entonces se incluye a la persona con los 
            # datos de todas las tablas
            if [ "$enc" == "" ]; then
            
                # Como el campo id_persona es auto_increment no se incluye en el insert, y sus 
                # valores se agregan solos.
                #mysql -uroot -proot -e "INSERT INTO bd_saber_trabajo.tbl_personas (nacionalidad, cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, estado_civil, sexo, edad, numero_hijos, posee_discapacidad, id_discapacidad, posee_enfermedad, id_enfermedad, estatus, fecha_creacion, fecha_modificacion, fecha_eliminacion, id_usuario_creador, id_usuario_modificar, id_usuario_eliminar) VALUES (`head -$i $PATH_BCKP/nuevos/Desc/$ARCHIVO | tail -1 | cut -d, -f2-23 | sed -r 's/[^,]+/"&"/g'`)"
		#LAST=`mysql -uroot -proot -e "SELECT LAST_INSERT_ID()"`
		#mysql -uroot -proot -e "ALTER TABLE bd_saber_trabajo.tbl_personas AUTO_INCREMENT='$LAST'"
                
		
		
		mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;INSERT INTO bd_saber_trabajo.tbl_personas VALUES (NULL, `head -$i $PATH_BCKP/nuevos/Desc/tmp/$ARCHIVO | tail -1 | cut -d, -f2-23 `)"
		#head -$i $PATH_BCKP/nuevos/Desc/$ARCHIVO | tail -1 | cut -d, -f2-23 > $PATH_BCKP/datos.txt
		#cat $PATH_BCKP/datos.txt
                #mysql -uroot -proot -e "LOAD DATA INFILE '$PATH_BCKP/datos.txt' INTO TABLE bd_saber_trabajo.tbl_personas(nacionalidad, cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, fecha_nacimiento, estado_civil, sexo, edad, numero_hijos, posee_discapacidad, id_discapacidad, posee_enfermedad, id_enfermedad, estatus, fecha_creacion, fecha_modificacion, fecha_eliminacion, id_usuario_creador, id_usuario_modificar, id_usuario_eliminar)"
        
                echo $cedid >> ingresados.txt # se escriben las personas (cedula) ingresadas al sistema		
                
                echo "`date +%Y-%m-%d' '%H:%M:%S` $n El usuario con cedula $cedid se incorporo exitosamente al sistema"  >> $PATH_BCKP/restore_events.log
                
            else
            
                echo "`date +%Y-%m-%d' '%H:%M:%S` $n El usuario con cedula $cedid ya existe en la Base de Datos"  >> $PATH_BCKP/restore_events.log
                   
            fi
        
            i=`expr $i + 1`
        
        done 
        
        ##### Actualizar id_persona en las otras tablas
        
        
        NLN=`cat ingresados.txt | wc -l` # Se cuentan los registros en el archivo
        
        i=1
        
        ## Con cada persona ingresada en la BD Principal
        
        while [ $i -le $NLN ]; do
        
            cedid=`head -$i ingresados.txt | tail -1`
            v_idp=`grep $cedid $PATH_BCKP/nuevos/Desc/tmp/$ARCHIVO | cut -d, -f1` # se ubica el viejo ID de persona
	    echo $v_idp
            
            # Ubicar nuevo id de persona
            n_idp=`mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -B --disable-column-names -e "SELECT id_persona FROM bd_saber_trabajo.tbl_personas WHERE cedula = $cedid"`
            
            ### Creamos un archivo temporal para cada tabla con el nuevo id_persona en los registros
            
            # Cambiar id_persona para tabla tbl_personas_carreras_formarse
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_carreras_formarse.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-10`" >> /tmp/tbl_personas_carreras_formarse_tmp.csv
                fi
                j=`expr $j + 1`
            done
        
         # Cambiar id_persona para tabla tbl_personas_misiones
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_misiones.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-10`" >> /tmp/tbl_personas_misiones_tmp.csv
                fi
                j=`expr $j + 1`
            done

            # Cambiar id_persona para tabla tbl_personas_oficios
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_oficios.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-10`" >> /tmp/tbl_personas_oficios_tmp.csv
                fi
                j=`expr $j + 1`
            done
            
            # Cambiar id_persona para tabla tbl_personas_oficios_formarse
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_oficios_formarse.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-10`" >> /tmp/tbl_personas_oficios_formarse_tmp.csv
                fi
                j=`expr $j + 1`
            done
            
            # Cambiar id_persona para tabla tbl_personas_organizaciones_sociales
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_organizaciones_sociales.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-10`" >> /tmp/tbl_personas_organizaciones_sociales_tmp.csv
                fi
                j=`expr $j + 1`
            done
            
            # Cambiar id_persona para tabla tbl_personas_socioeconomicos
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_socioeconomicos.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-20`" >> /tmp/tbl_personas_socioeconomicos_tmp.csv
                fi
                j=`expr $j + 1`
            done
            
            # Cambiar id_persona para tabla tbl_personas_ubicaciones
            TBL="$PATH_BCKP/nuevos/Desc/tmp/tbl_personas_ubicaciones.csv"
            NLN_2=`cat $TBL | wc -l`
            j=1
            while [ $j -le $NLN_2 ]; do
                idp_2=`head -$j $TBL | tail -1 | cut -d, -f2`
                
                if [ "$idp_2" == "$v_idp" ]; then
                    echo "$n_idp,`head -$j $TBL | tail -1 | cut -d, -f3-23`" >> /tmp/tbl_personas_ubicaciones_tmp.csv
                fi
                j=`expr $j + 1`
            done
            
            
        
            i=`expr $i + 1`
        
        done
        
        
        ## Insertar las tablas actualizadas a la BD Principal 
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_carreras_formarse_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_carreras_formarse FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_carrera,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_misiones_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_misiones FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_mision,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_oficios_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_oficios FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_oficio,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_oficios_formarse_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_oficios_formarse FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_oficio,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_organizaciones_sociales_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_organizaciones_sociales FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_organizacion_social,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"        
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE '/tmp/tbl_personas_socioeconomicos_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_socioeconomicos FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_formacion_academica,posee_certificado,trabaja,estudia,id_carrera,id_profesion,sabe_leer,sabe_escribir,participar_mision,pertenece_organizacion,id_area_laboral,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        
        mysql -uroot -pH3r4cl1t0.. -h192.168.0.96 -P3307 -e "SET FOREIGN_KEY_CHECKS=0;LOAD DATA INFILE'/tmp/tbl_personas_ubicaciones_tmp.csv' INTO TABLE bd_saber_trabajo.tbl_personas_ubicaciones FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\r\n' (id_persona,id_region,id_estado,id_municipio,id_parroquia,id_centro_poblado,nombre_consejo_comunal,id_base_mision,nombre_comuna,direccion,telefono_movil,telefono_fijo,email,twitter,facebook,estatus,fecha_creacion,fecha_modificacion,fecha_eliminacion,id_usuario_creador,id_usuario_modificar,id_usuario_eliminar)"
        
        ## Eliminar archivos temporales
        
        rm /tmp/*_tmp.csv
        
        rm -r $PATH_BCKP/nuevos/Desc/*
        
        ## Mover backup a procesados
        
        mv $PATH_BCKP/nuevos/$n $PATH_BCKP/procesados
	
	cat /dev/null > /app/censo/scripts/ingresados.txt  
        # rm /app/censo/scripts/ingresados.txt
    	
        #touch /app/censo/scripts/ingresados.txt
        
        
    done # fin for

else

    # Si no se encuentran nuevos backups
    echo "No se encontraron backups para procesar"

fi
