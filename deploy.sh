#!/bin/bash

# Script de Despliegue Automático para IMPEF en DigitalOcean
# Ejecutar como root en el servidor

set -e  # Salir si hay algún error

echo "🚀 Iniciando despliegue de IMPEF en DigitalOcean..."

# Variables de configuración
APP_NAME="impef"
APP_DIR="/var/www/$APP_NAME"
DB_NAME="${APP_NAME}_db"
DB_USER="${APP_NAME}_user"
DB_PASS=$(openssl rand -base64 32)

echo "📋 Configurando variables..."
echo "App Directory: $APP_DIR"
echo "Database: $DB_NAME"
echo "Database User: $DB_USER"

# 1. Actualizar sistema
echo "🔄 Actualizando sistema..."
apt update && apt upgrade -y

# 2. Instalar dependencias básicas
echo "📦 Instalando dependencias básicas..."
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# 3. Instalar Nginx
echo "🌐 Instalando Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx

# 4. Instalar PHP 8.2
echo "🐘 Instalando PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-redis php8.2-sqlite3

# 5. Instalar Composer
echo "🎼 Instalando Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 6. Instalar MySQL
echo "🗄️ Instalando MySQL..."
apt install -y mysql-server

# Configurar MySQL de forma no interactiva
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PASS';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mysql -e "FLUSH PRIVILEGES;"

# 7. Instalar Node.js
echo "📦 Instalando Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# 8. Crear base de datos
echo "🗄️ Configurando base de datos..."
mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# 9. Crear directorio de la aplicación
echo "📁 Creando directorio de la aplicación..."
mkdir -p $APP_DIR
cd $APP_DIR

# 10. Configurar permisos
echo "🔐 Configurando permisos..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR

# 11. Crear archivo .env de ejemplo
echo "⚙️ Creando archivo .env..."
cat > $APP_DIR/.env << EOF
APP_NAME=IMPEF
APP_ENV=production
APP_DEBUG=false
APP_URL=http://$(hostname -I | awk '{print $1}')

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
SESSION_LIFETIME=120
EOF

# 12. Configurar Nginx
echo "🌐 Configurando Nginx..."
cat > /etc/nginx/sites-available/$APP_NAME << EOF
server {
    listen 80;
    server_name _;
    root $APP_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Habilitar sitio
ln -sf /etc/nginx/sites-available/$APP_NAME /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Verificar configuración de Nginx
nginx -t

# 13. Configurar PHP
echo "🐘 Optimizando PHP..."
sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/8.2/fpm/php.ini

# 14. Configurar firewall
echo "🔥 Configurando firewall..."
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# 15. Crear script de backup
echo "💾 Configurando backup automático..."
cat > /root/backup.sh << EOF
#!/bin/bash
DATE=\$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
DB_NAME="$DB_NAME"
DB_USER="$DB_USER"
DB_PASS="$DB_PASS"
APP_DIR="$APP_DIR"

mkdir -p \$BACKUP_DIR

# Backup de la base de datos
mysqldump -u \$DB_USER -p\$DB_PASS \$DB_NAME > \$BACKUP_DIR/db_backup_\$DATE.sql

# Backup de archivos de la aplicación
tar -czf \$BACKUP_DIR/app_backup_\$DATE.tar.gz -C /var/www $APP_NAME

# Mantener solo los últimos 7 backups
find \$BACKUP_DIR -name "*.sql" -mtime +7 -delete
find \$BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completado: \$DATE"
EOF

chmod +x /root/backup.sh

# 16. Configurar logrotate
echo "📝 Configurando logrotate..."
cat > /etc/logrotate.d/$APP_NAME << EOF
$APP_DIR/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
EOF

# 17. Reiniciar servicios
echo "🔄 Reiniciando servicios..."
systemctl restart php8.2-fpm
systemctl reload nginx

# 18. Mostrar información final
echo ""
echo "✅ Despliegue completado exitosamente!"
echo ""
echo "📋 Información del servidor:"
echo "   IP del servidor: $(hostname -I | awk '{print $1}')"
echo "   Directorio de la app: $APP_DIR"
echo "   Base de datos: $DB_NAME"
echo "   Usuario DB: $DB_USER"
echo "   Contraseña DB: $DB_PASS"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Subir tu código Laravel a: $APP_DIR"
echo "   2. Ejecutar: composer install --no-dev --optimize-autoloader"
echo "   3. Ejecutar: php artisan key:generate"
echo "   4. Ejecutar: php artisan migrate --force"
echo "   5. Ejecutar: npm install && npm run build"
echo "   6. Configurar dominio en Nginx"
echo "   7. Configurar SSL con Certbot"
echo ""
echo "🔐 Guarda esta información de forma segura!"
echo ""
echo "📚 Para más información, consulta: deployment-guide.md"
