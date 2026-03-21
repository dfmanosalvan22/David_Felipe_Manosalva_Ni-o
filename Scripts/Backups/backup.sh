#!/bin/bash

if [[ $# -ne 1 ]]; then
    echo "Uso: $0 [volumen|sql]"
    exit 1
fi

TIPO=$1
CONTENEDOR="logitrans-bd"
VOLUMEN="bd_datos"
BACKUP_DIR="/home/ubuntu_server_docker/backups"
FECHA=$(date +%Y%m%d_%H%M%S)

LOGS="$BACKUP_DIR/backup_logs.log"

log_backup(){
        echo "$FECHA - $1" >> "$LOGS"
}

mkdir -p $BACKUP_DIR

if [[ "$TIPO" = "volumen" ]]; then
    docker exec $CONTENEDOR tar czf /backups/backup_volumen_$FECHA.tar.gz -C /var/lib/mysql .

        if [ $? -eq 0 ]; then
                echo "EXITO:$BACKUP_DIR/backup_volumen_$FECHA.tar.gz"
                log_backup "EXITO: Backup de volumen realizado correctamente."
        else
                echo "ERROR: Consulta el log en $LOGS"
                log_backup "ERROR: El backup de volumen fallo."
        fi

elif [[ "$TIPO" = "sql" ]]; then
        docker exec $CONTENEDOR mariadb-dump -u root -p'roottoor*' logitrans_db > $BACKUP_DIR/dump_$FECHA.sql

        if [ $? -eq 0 ]; then
                echo "EXITO:$BACKUP_DIR/dump_$FECHA.sql"
                log_backup "EXITO: Backup SQL realizado correctamente."
        else
                echo "ERROR: Consulta el log en $LOGS"
                log_backup "ERROR: El backup SQL fallo."
        fi
else
        echo "ERROR: Tipo invalido, usa 'volumen' o 'sql'"
        log_backup "ERROR: Intento de ejecucion con parámetro invalido: $TIPO"
        exit 1
fi