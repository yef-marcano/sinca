
#!/bin/bash
HORA=$(date +%H:%M:%S)
DIA=$(date +%Y%m%d)
VIEJO=$(date --date '10 days ago' +%Y%m%d)
pg_dump -h localhost -U postgres -w bd_sicet_redi > /home/respaldosBD/sicet/bd_sicet_redi-$DIA-$HORA.backup


mysqldump --user=****** --password=******  db_1 db_2 db_n> /Ruta/Hacia/archivo_dump.SQL
