#!/bin/sh


ARCH_ORI=2
ARCH_NEW=$(ls -l /app/censo/backup/nuevos/ | wc -l)


echo " mostrando valor de la variable original"
echo $ARCH_ORI



echo " mostrando valor de la variable nuevo"
echo $ARCH_NEW


	if [  $ARCH_NEW -gt $ARCH_ORI ]; 
		then
		./resp_prfl.sh
		else 
		./NO.sh
	fi

