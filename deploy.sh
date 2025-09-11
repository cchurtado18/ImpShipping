#!/bin/bash
set -e
echo "🚀 Configurando servidor IMPEF..."

# 1. Actualizar sistema
echo "📦 Actualizando sistema..."
apt update && apt upgrade -y

# 2. Instalar dependencias básicas
echo "🔧 Instalando dependencias..."
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# 3. Agregar repositorio PHP 8.2
echo "�� Configurando PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt update

# 4. Instalar PHP 8.2 y extensiones
echo "�� Instalando PHP 8.2..."
apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl

# 5. Instalar Nginx
echo "🌐 Instalando Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx

# 6. Instalar MySQL
echo "🗄️ Instalando MySQL..."
apt install -y mysql-server
systemctl start mysql
systemctl enable mysql

# 7. Configurar MySQL de forma segura
echo "�� Configurando MySQL..."
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root123456';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mysql -e "FLUSH PRIVILEGES;"

# 8. Crear base de datos y usuario
echo "🗄️ Creando base de datos..."
DB_PASS=$(openssl rand -base64 32)
mysql -u root -proot123456 -e "CREATE DATABASE IF NOT EXISTS impef_db;"
mysql -u root -proot123456 -e "CREATE USER IF NOT EXISTS 'impef_user'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -proot123456 -e "GRANT ALL PRIVILEGES ON impef_db.* TO 'impef_user'@'localhost';"
mysql -u root -proot123456 -e "FLUSH PRIVILEGES;"

# 9. Instalar Composer
echo "🎼 Instalando Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 10. Instalar Node.js
echo "�� Instalando Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# 11. Crear directorio de la aplicación
echo "�� Creando directorio de la aplicación..."
mkdir -p /var/www/impef
chown -R www-data:www-data /var/www/impef
chmod -R 755 /var/www/impef

# 12. Configurar Nginx
echo "�� Configurando Nginx..."
cat > /etc/nginx/sites-available/impef << 'NGINX'
server {
    listen 80;
    server_name _;
    root /var/www/impef/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX

# Habilitar sitio
ln -sf /etc/nginx/sites-available/impef /etc/nginx/sites-enabled/
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
echo "�� Configurando backup automático..."
cat > /root/backup.sh << EOF
#!/bin/bash
DATE=\$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
DB_NAME="impef_db"
DB_USER="impef_user"
DB_PASS="$DB_PASS"
APP_DIR="/var/www/impef"

mkdir -p \$BACKUP_DIR

# Backup de la base de datos
mysqldump -u \$DB_USER -p\$DB_PASS \$DB_NAME > \$BACKUP_DIR/db_backup_\$DATE.sql

# Backup de archivos de la aplicación
tar -czf \$BACKUP_DIR/app_backup_\$DATE.tar.gz -C /var/www impef

# Mantener solo los últimos 7 backups
find \$BACKUP_DIR -name "*.sql" -mtime +7 -delete
find \$BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completado: \$DATE"
EOF

chmod +x /root/backup.sh

# 16. Configurar logrotate
echo "📝 Configurando logrotate..."
cat > /etc/logrotate.d/impef << EOF
/var/www/impef/storage/logs/*.log {
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
echo "✅ ¡Despliegue completado exitosamente!"
echo ""
echo "📋 Información del servidor:"
echo "   IP del servidor: $(hostname -I | awk '{print $1}')"
echo "   Directorio de la app: /var/www/impef"
echo "   Base de datos: impef_db"
echo "   Usuario DB: impef_user"
echo "   Contraseña DB: $DB_PASS"
echo "   MySQL Root Password: root123456"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Clonar tu repositorio: git clone https://github.com/cchurtado18/ImpShipping.git /var/www/impef"
echo "   2. Configurar .env con la contraseña de la base de datos"
echo "   3. Ejecutar: composer install --no-dev --optimize-autoloader"
echo "   4. Ejecutar: npm install && npm run build"
echo "   5. Ejecutar: php artisan key:generate"
echo "   6. Ejecutar: php artisan migrate --force"
echo "   7. Ejecutar: php artisan config:cache"
echo ""
echo "🔐 Guarda esta información de forma segura!"
echo ""
echo "�� URL: http://$(hostname -I | awk '{print $1}')"
