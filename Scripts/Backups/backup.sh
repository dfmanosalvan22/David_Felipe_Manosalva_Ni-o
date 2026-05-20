#!/bin/bash

if [[ $# -ne 1 ]]; then
    echo "Uso: $0 [volumen|sql]"
    exit 1
fi

TIPO=$1

CONTENEDOR="logitrans-bd"
BACKUP_DIR="/home/backup_servicio/backups_temp"

FECHA=$(date +%Y%m%d_%H%M%S)

mkdir -p "$BACKUP_DIR"

if [[ "$TIPO" = "sql" ]]; then

    ARCHIVO="$BACKUP_DIR/dump_$FECHA.sql"

    docker exec "$CONTENEDOR" \
    mariadb-dump -u root -p'roottoor*' logitrans_db > "$ARCHIVO"

    if [[ $? -eq 0 ]]; then
        echo "EXITO:$ARCHIVO"
    else
        echo "ERROR: Fallo backup SQL"
        exit 1
    fi

elif [[ "$TIPO" = "volumen" ]]; then

    ARCHIVO="$BACKUP_DIR/volumen_$FECHA.tar.gz"

    docker run --rm \
        --volumes-from "$CONTENEDOR" \
        -v "$BACKUP_DIR":/backup \
        ubuntu \
        tar czf "/backup/volumen_$FECHA.tar.gz" /var/lib/mysql

    if [[ $? -eq 0 ]]; then
        echo "EXITO:$ARCHIVO"
    else
        echo "ERROR: Fallo backup volumen"
        exit 1
    fi

else
    echo "ERROR: Tipo inválido"
    exit 1
fi
