#!/bin/bash
set -e

# AxiaOrto Database Restore Script
# Usage: ./restore.sh <backup_file.sql.gz>
#
# Prerequisites:
# - .my.cnf file in project root with database credentials (permission 600)
# - mysql and gunzip installed

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

if [ -z "$1" ]; then
    echo -e "${RED}Error: File backup belum ditentukan.${NC}"
    echo "Penggunaan: ./restore.sh <file_backup.sql.gz>"
    echo "Contoh:    ./restore.sh /home/user/backups/2026/05/30/backup.sql.gz"
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}Error: File tidak ditemukan: $BACKUP_FILE${NC}"
    exit 1
fi

if [ ! -f ".my.cnf" ]; then
    echo -e "${RED}Error: File .my.cnf tidak ditemukan di direktori project.${NC}"
    echo "Buat .my.cnf dengan isi:"
    echo "[mysqldump]"
    echo "user=db_user"
    echo "password=db_password"
    exit 1
fi

# Get database name from .env
DB_NAME=$(grep DB_DATABASE .env 2>/dev/null | cut -d'=' -f2 | tr -d '"' | tr -d "'")
if [ -z "$DB_NAME" ]; then
    echo -e "${RED}Error: DB_DATABASE tidak ditemukan di .env${NC}"
    exit 1
fi

echo -e "${YELLOW}=== AxiaOrto Database Restore ===${NC}"
echo "File backup: $BACKUP_FILE"
echo "Database:    $DB_NAME"
echo ""
echo -e "${YELLOW}PERINGATAN: Semua data di database '$DB_NAME' akan ditimpa!${NC}"
read -p "Lanjutkan? (y/N): " CONFIRM

if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
    echo "Restore dibatalkan."
    exit 0
fi

echo ""
echo -e "${YELLOW}[1/4] Memproses backup...${NC}"
if [[ "$BACKUP_FILE" == *.gz ]]; then
    gunzip -c "$BACKUP_FILE" | mysql --defaults-extra-file=.my.cnf "$DB_NAME"
else
    mysql --defaults-extra-file=.my.cnf "$DB_NAME" < "$BACKUP_FILE"
fi
echo -e "${GREEN}Database berhasil direstore.${NC}"

echo -e "${YELLOW}[2/4] Menjalankan migrasi...${NC}"
php artisan migrate --force
echo -e "${GREEN}Migrasi selesai.${NC}"

echo -e "${YELLOW}[3/4] Membersihkan cache...${NC}"
php artisan config:cache
php artisan route:cache
echo -e "${GREEN}Cache diperbarui.${NC}"

echo -e "${YELLOW}[4/4] Restart queue worker...${NC}"
php artisan queue:restart 2>/dev/null || true
echo -e "${GREEN}Queue worker direstart.${NC}"

echo ""
echo -e "${GREEN}=== Restore selesai! ===${NC}"
echo "Verifikasi data di browser untuk memastikan restore berhasil."
