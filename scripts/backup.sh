#!/bin/bash
FILENAME="backup_$(date +%Y%m%d_%H%M%S).tar.gz"
DEST="/home/backupuser/ftp/fitxers"
SRC="/home/app/ftp/www" # O /home/app/ftp si quieres guardar todo el proyecto

echo "Iniciando copia de seguridad..."

tar -czf $DEST/$FILENAME $SRC
# Opcional: Borrar copias más viejas de 7 días
find $DEST -type f -name "*.tar.gz" -mtime +7 -delete
echo "Copia guardada como $FILENAME"